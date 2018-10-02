<?php
Class Operator{
  private $_mysqli;
  private $_ops_enabled;
  private $_permissions_layerd;

  private $op_id;
    public function get_op_id(){return $this->op_id;}

  private $_permission_level;
    public function get_permission_level(){return $this->_permission_level;}

  private $_op_accounts_interface;
      public function getAccountsInterface(){return $this->_op_accounts_interface;}
  private $_op_curr_user;
    public function get_curr_user(){return $this->_op_curr_user;}

  private $_op_trans; //obj
    public function getTransactionInterface(){return $this->_op_trans;}
  private $_op_trans_session;//# in php session
    public function get_cuur_sales_session(){return $this->_op_trans_session;}

  public function __construct(){
    GLOBAL $mysqli;
    $this->_mysqli = $mysqli;
    $this->_ops_enabled = CONFIG::OPS_ENABLED;
    $this->_permissions_layerd = CONFIG::OPS_PERMISSIONS;
    if(!$this->checkLogin()){
      header("tsastatus: 400");
      header("adminstatus: 400");
      exit(json_encode(array("message"=>"Access Not Verified.")));
    }
  }

  //
  //  V: Login with given credentials
  //
  public static function doLogin($user, $password){
    $password = md5($password);

    $query = $mysqli->prepare("SELECT `id`,`secret`,`permission_level`,`display_name` FROM `op_credentials` WHERE `username`=?");
    $query->bind_param("s", $user);
    $query->execute();
    $query->bind_result($op_id, $secret, $permission_level, $display_name);
    $query->store_result();
    if($query->num_rows != 1)
      return array("message"=>"Invalid Credentials", "username"=>"$user");

    $query->fetch();
    $query->close();

    if(!($password === $secret))
      return array("message"=>"Invalid credentials", "username"=>"$user");

    session_start();
    $sid = session_id();
    $ip = $_SERVER["REMOTE_ADDR"];
    $hostname = gethostbyaddr($ip);
    $expire = time() + (3600 * 24);
    $oneRef = intval("-1");

    $query = $mysqli->prepare("INSERT INTO `op_sessions` VALUES(?,?,?,?,?,?,?)");
    $query->bind_param("issiiss", $op_id, $sid, $expire, $oneRef, $oneRef, $ip, $hostname);
    $query->execute();
    $query->store_result();
    if($query->affected_rows != 1)
      return array("message"=>"System Error. Unable to Complete Login.", "username"=>"$user");
    $query->close();

    //Set cookies to expire in 24 hours
    setcookie("TSAOP", $op_id, $expire, "/", CONFIG::OPS_SESSION_RESTRICT, CONFIG::OPS_SSL_ENABLED);

    return 200;
  }

  //
  //  V: Logout of account
  //
  public static function doLogout(){
    //Delete OP ID & Session ID Cookie. Destroy PHP Session
    setcookie("TSAOP", "", time()-3600);
    session_destroy();
  }

  //
  //  B: Check if Operator is logged in
  //
  public function checkLogin(){
    $query = $this->_mysqli->prepare("SELECT `op_id`,`ipaddr`,`current_client`,`current_sale` FROM `op_sessions` WHERE `session_id`=?");
    $sid = session_id();
    $query->bind_param("s",$sid);
    $query->bind_result($this->op_id, $ipAddr, $clientUUID, $transID);
    $query->execute();
    $query->store_result();
    $query->fetch();
    if($query->num_rows !== 1)
      return false;

    if(!($this->op_id == @$_COOKIE["TSAOP"])){
      return false;
    }
    if(!($ipAddr == $_SERVER["REMOTE_ADDR"]))
      return false;

    $query = $this->_mysqli->query("SELECT `permission_level` FROM `op_credentials` WHERE `id`='$this->op_id'");
    $rows = $query->fetch_assoc();
    $this->_permission_level = $rows["permission_level"];

    if(!$this->checkPermission("Operator_canLogin")){//TODO: move to login once static/nonstatic stuff figured out
      $this->doLogout();
      return array("message"=>"Access Denied");
    }

    if(!$this->checkPermission("Operator_canOperate")){
	    return false;
    }
    $query->close();

    $this->reloadSession($clientUUID, $transID);
    return true;
  }

  public function reloadSession($user_uuid, $curr_transid){
    $_SESSION["OP_CUR_UUID"] = $user_uuid;
    $this->_op_trans_session = $_SESSION["OP_CUR_SALE"] = $transID;

    if($this->_op_trans_session > 0){
      $this->_op_trans = new Transaction($this->_op_trans_session, $this);
    }

    if($user_uuid > 99999 && $user_uuid < 1000000){
      $this->_op_curr_user = new User($user_uuid);
      $this->_op_accounts_interface = new Accounts($this->_op_curr_user);
    }

    return true;
  }

  public function selectAccount($uuid_new){
    $User_new = new User(intval($uuid_new));
    $Accounts_new = new Accounts($User_new);

    //Authenticate Customer
    if(!ISSET($_POST["mfToken"])){//no MF challenge response
      //Enabled on accout
      if($User_new->isPinNeeded() || $User_new->isPhotoIDNeeded()){
        $Accounts_new->sendMultiFactor();//TODO:everything
      }

      //Globally enabled
      if(CONFIG::ACT_REQUIRE_PIN || CONFIG::ACT_PHOTO_ID){
        $Accounts_new->sendMultiFactor();
      }
    }else{//Responding to MF challenge
      $Accounts_new->checkMultiFactor($_POST["mfToken"]);//TODO:everything
    }

    //Operator Session BS
    if(!$this->opSetCurrentClient($User_new)){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Database error.", "time"=>8000)));
    }

    $this->_op_curr_user = $User_new;
    $this->_op_accounts_interface = $Accounts_new;
    return true;
  }

  public function opSetCurrentClient($newUser){
    //Check how to terminate current transaction session
    if($_SESSION["OP_CUR_UUID"] == $newUser->getUUID()){//don't run request if operator already working with this user / session
      return true;
    }

    if($this->_op_trans_session > 0){
      $this->_op_trans = new Transaction($this->_op_trans_session, $this);
      $this->_op_trans->closeTransaction();//Will die and prompt operator for action if not imediatly closable
    }

  //Switch current UUID over
    $query = $this->_mysqli->prepare("UPDATE `op_sessions` SET `current_client`=? WHERE `op_id`=?");
    $uuid = $newUser->getUUID();
    $query->bind_param("ii",$uuid,$this->op_id);
    $query->execute();
    $query->store_result();

    if($query->affected_rows != 1){
      $this->clearOpTransactionSession();
      //^kills request
    }

    $this->_op_curr_user = $newUser;
    $_SESSION["OP_CUR_UUID"] = $uuid;

    //Create new transaction session for new customer
    $this->_op_trans = new Transaction(-1, $this);
    if(!$this->setCurrentSession($this->_op_trans->getTransSession()))
      return false;

    return true;
  }

  //Transaction sessions
  public function setCurrentSession($transID){
    $query = $this->_mysqli->prepare("UPDATE `op_sessions` SET `current_sale`=? WHERE `op_id`=?");
    $query->bind_param("ii",$transID,$this->op_id);
    $query->execute();
    $query->store_result();
    if($query->affected_rows != 1){
      $this->clearOpTransactionSession();
      //^kills request
    }
    $query->close();

    $this->_op_trans_session = $transID;
    $_SESSION["OP_CUR_SALE"] = $transID;
    return true;
  }

  private function clearOpTransactionSession(){
    $this->_op_trans = null;
    $this->_op_trans_session = -1;
    $_SESSION["OP_CUR_SALE"] = -1;

    $query = $this->_mysqli->prepare("UPDATE `op_sessions` SET `current_sale`=? WHERE `op_id`=?");
    $query->bind_param("ii",$this->_op_trans_session,$this->op_id);
    $query->execute();
    $query->store_result();

    if($query->affected_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Database error. Request Terminated and databased rolled back.", "time"=>8000)));
    }

    header("tsastatus: 500");
    exit(json_encode(array("type"=>1, "message"=>"Database error. Request terminated, but session data may be out of sync.", "time"=>8000)));
  }

  public function opGetHeldSessionList($op){
    if(!isset($op))
      $op = $this->op_id;

    $query = $this->_mysqli->prepare("SELECT `held_sessions` FROM `op_held_sessions` WHERE `op_id`=?");
    $query->bind_param("i",$op);
    $query->bind_result($opSessions);
    $query->execute();
    $query->store_result();
    $query->fetch();
    return $opSessions;
  }

  public function opAddHeldSession($transID){
    $opSessions = $this->opGetHeldSessionList();
    $opSessionsArray = explode(",", $opSessions);
    $match = array_search($transID, $opSessionsArray);
    if($match !== false){//Already on list
      return;
    }

    $query = $this->_mysqli->prepare("UPDATE `op_held_sessions` SET `held_sessions`=CONCAT(`held_sessions`,',',?)");
    $query->bind_param("s",$transID);
    $query->execute();
    return $query->affected_rows;
  }

  public function removeHeldSession($transID){
    $opSessions = $this->opGetHeldSessionList();
    $opSessionsArray = explode(",", $opSessions);
    $match = array_search($transID, $opSessionsArray);
    if($match === false){//not on list
      return;
    }
    $opSessionsArray = array_splice($opSessionsArray, $match, 1);
    $opSessions = implode(",",$opSessionsArray);

    $query = $this->_mysqli->prepare("UPDATE `op_held_sessions` SET `held_sessions`=?");
    $query->bind_param("s",$opSessions);
    $query->execute();
    $query->store_result();
    return $query->affected_rows;
  }

  //
  //  Gets JSON CFG for nav bar to send to operator
  //
  public function loadNavigation(){
    require(CONFIG::DOC_ROOT . "/Apps/Operator/config_navigation.php");//Load navigation display config
    foreach ($navigation as $tile) {
      if($this->checkPermission($tile["node"])){
        $navigationCFG[] = $tile;
      }
    }
    return $navigationCFG;
  }

  //
  //  Check if Permission class has given permission
  //    $this->_permission_level must be set (done during authentication)
  public function checkPermission($permission){
    $query = $query = $this->_mysqli->query("SELECT `$permission` FROM `op_permissions` WHERE `id`='$this->_permission_level' AND `$permission`='1' AND `isEnabled`=1");
    if($query->num_rows == 1)
      return true;
    return false;
  }
}
?>
