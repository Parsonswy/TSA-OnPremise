<?php
class Permissions{
  private $_mysqli;
  private $_client_class_id;//Permission class of manager making request (COOKIE)
  private $_client_operator_id;//Operator id of manager making reuqest (COOKIE)

  public function __construct(){
    GLOBAL $mysqli;
    $this->_mysqli = $mysqli;

    if(!CONFIG::OPS_ENABLED){
      header("adminstatus: 500");
      exit(json_encode(array("type"=>0, "message"=>"System is not configured to utalize access control.", "time"=>8000)));
    }
    GLOBAL $Operator;//Manage.php
    $this->_client_operator_id = $Operator->get_op_id();
    $this->_client_class_id = $Operator->get_permission_level();
  }

  //
  //  Load list of permissions classes (perm manager dropdown)
  //
  public function loadPermissionsClassList(){
    $query = $this->_mysqli->query("SELECT `id`,`name` FROM `op_permissions` WHERE 1=1 ORDER BY `name` ASC");
    if($query->num_rows < 1){
      header("adminstatus: 500");
      exit(json_encode(array("type"=>0, "message"=>"Database error. Unable to retreive permissions data.", "time"=>8000)));
    }

    $resp = array();
    while($rows = $query->fetch_assoc()){
      array_push($resp, array("id"=>$rows["id"], "name"=>$rows["name"]));
    }
    return $resp;
  }

  //
  //  Get JSON of permission $class(id) for display to client
  //  Breaks nodes into sub-arrays based on prefix for app
  public function loadPermissionsClassDisplay($class){
    $class = $this->_mysqli->real_escape_string($class);

    //validate class id
    if($class < 0){
      header("adminstatus: 500");
      exit(json_encode(array("type"=>0, "message"=>"Database error. Please reload the page and try again.", "time"=>8000)));
    }

    $query = $this->_mysqli->query("SELECT * FROM `op_permissions` WHERE `id`=$class");
    if($query->num_rows != 1){
      header("adminstatus: 500");
      exit(json_encode(array("type"=>0, "message"=>"Database error. Please reload the page and try again.", "time"=>8000)));
    }

    $rows = $query->fetch_assoc();

    //Extract all keys w/o '-' and get position of first one with underscore
    $classMetaKeys = preg_grep("@^.*(?=(_))@",array_keys($rows), PREG_GREP_INVERT);
    $firstNode = count($classMetaKeys);//pos

    $classMeta = array_slice($rows, 0, $firstNode);

    $lastPrefix = "";
    $permsFormatted = array();
    foreach(new LimitIterator(new ArrayIterator($rows), $firstNode) as $key => $value){
      $prefix = substr($key, 0, strpos($key, "_"));//begining to _, exclusive
      if($lastPrefix !== $prefix){
        $lastPrefix = $prefix;
        $permsFormatted[$prefix] = array();
      }
      $permsFormatted[$prefix][$key] = $value;
    }
    return array_merge($classMeta, $permsFormatted);
    /*
      class => "meta"
      data => "individual"
      "permnodes"=>array("catagorized"=>0/1)
      "permnodes"=>array("catagorized"=>0/1)
    */
  }

  //
  //  Create permisson class with no permissions under the name given in $class
  //  Returns int id of class
  public function createPermissionClass($class){

  }

  //
  //  Applies configuration in JSON $cfg to class $class_id
  //
  public function modifyPermissionClass(){
    $metaDataQuery = $this->_mysqli->prepare("UPDATE `op_permissions` SET `name`=?, `description`=? WHERE `id`=?");
    $metaDataQuery->bind_param('ssi', $_POST["name"], $_POST["description"], $_POST["perm_groups"]);
    $metaDataQuery->execute();

    $query = $this->_mysqli->query("SHOW COLUMNS FROM `op_permissions`");
    $cols = "";
    while($rows = $query->fetch_assoc()){
      $db_key = $rows["Field"];
      if(!strpos($db_key, "_"))
        continue;

      $form_val = @$this->_mysqli->real_escape_string($_POST[$db_key]);//Supress undefined errors for unchecked boxes

      $value = 0;
      if($form_val == "on")//box checkd?
        $value = 1;

      $cols.= "`" . $db_key . "`=" . $value . ",";
    }
    $cols.= "`isEnabled`=" . (($_POST['isEnabled'] == "on")? 1 : 0);

    $id = $this->_mysqli->real_escape_string($_POST["perm_groups"]);
    $this->_mysqli->query("UPDATE `op_permissions` SET " . $cols . " WHERE `id`='$id'");

    return;//Info message set in JS 4102
  }

  //
  //  Delete permission group
  //
  public function deletePermissionClass($class){

  }

  //
  //  Gets JSON CFG for nav bat to send to manager
  //
  public function loadNavigation(){
    require(CONFIG::DOC_ROOT . "/Apps/Manager/config_navigation.php");//Load navigation display config
    global $Operator;//Manage.php
    foreach ($navigation as $tile) {
      if($Operator->checkPermission($tile["node"])){
        $navigationCFG[] = $tile;
      }
    }
    return $navigationCFG;
  }

  /**********************************************
    Operator Management
  ************************************************/

  //
  //  Get data to load operator editor
  //
  public function loadOperatorEditor(){
    $query  = $this->_mysqli->query("SELECT * FROM `op_credentials` WHERE 1 ORDER BY `display_name` ASC");
    if(!$query->num_rows > 0){
      header("adminstatus: 500");
      exit(json_encode(array("type"=>0, "message"=>"Warning, found no configured operator accounts in database.", "time"=>8000)));
    }

    $data = array();
    while($rows = $query->fetch_assoc()){
      unset($rows["secret"]);
      $data["users"][] = $rows;
    }

    $data["permClasses"] = $this->loadPermissionsClassList();
    $data["operators"] = $query->num_rows;
    //oplist as $data.users. Permissions Gorup dropdown as data.permClasses
    return $data;
  }

  //
  //  Save Operator Settings from Management Portal
  //
  public function modyOperatorSettings(){
    $query = $this->_mysqli->prepare("UPDATE `op_credentials` SET `username`=?,`permission_level`=?, `display_name`=? WHERE `id`=?");
    for($i=1; $i<=$_POST["operators"]; $i++){
      $query->bind_param("sisi", $_POST[$i . "_username"], $_POST[$i . "_permission_level"], $_POST[$i . "_display_name"], $i);
      $query->execute();
    }

    $query = $this->_mysqli->prepare("UPDATE `op_credentials` SET `secret`=? WHERE `id`=?");
    for($i=1; $i<=$_POST["operators"]; $i++){
      if(strlen($_POST[$i . "_secret"]) < 4)
        continue;

      $query->bind_param("si", md5($_POST[$i . "_secret"]), $i);
      $query->execute();
    }

    return; //Info message set in JS 4111
  }
}
?>
