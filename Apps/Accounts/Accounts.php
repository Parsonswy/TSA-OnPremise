<?php
Class Accounts{
  private $_mysqli;

  private $_user;//instanceof User
  public function getUser(){return $this->_user;}

  public function __construct(User $user){
    GLOBAL $mysqli;
    $this->_mysqli = $mysqli;

    if(!($user instanceof User))
      return;

    $this->_user = $user;
  }

  //
  //  Check if account exists and returns active state
  //
  public static function accountExists($uuid){
    GLOBAL $mysqli;
    $query = $mysqli->prepare("SELECT `account_status` FROM `client_info` WHERE `uuid`=?");
    $query->bind_param("i", $uuid);
    $query->bind_result($account_status);
    $query->execute();
    $query->store_result();
    $query->fetch();
    if($query->num_rows != 1)
      return -1;//DNE
    return $account_status;
  }

  //
  //  Create user account/tab. data is  $_POSTed
  //
  public function createUserAaccount($entrance_fee, $pin, $profileIDIMG){
    $entrance_fee = htmlentities($_POST["entrance_fee"]);
    $pin = htmlentities(isset($_POST["pin"])? $_POST["pin"] : NULL);//silence if empty

    $output = array();
    if(!ISSET($pin))
      $output["setPin"] = 0;

    if(!ISSET($_POST["profileIDIMG"]))
      $output["setIDIMG"] = 0;

    if(!is_numeric($entrance_fee)){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Unexpected Data Validation Error.", "time"=>8000)));
    }

    if($this->_user->getAccountStatus() != -1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Account " . $this->_user->getUUID() . " already exists.", "time"=>8000)));
    }

    if(CONFIG::ACT_REQUIRE_PIN){
      if(strlen($pin) < 4){
        header("tsastatus: 500");
        exit(json_encode(array("type"=>1, "message"=>"Invalid Pin.", "time"=>8000)));
      }
    }

    $path = NULL;
    if(isset($_POST["profileIDIMG"]) || CONFIG::ACT_PHOTO_ID){
      //data:image/png;base64,iVBORw0KG...
      $imgData = explode(",", $_POST["profileIDIMG"]);//0:meta, 1:img
      unset($_POST["profileIDIMG"]);//RAM
      $img = imagecreatefromstring(base64_decode($imgData[1]));

      if(!$img){//check if valid / supported type
        header("tsastatus: 500");
        exit(json_encode(array("type"=>1, "message"=>"Photo ID invalid or of unsupported type!", "time"=>8000)));
      }
      $path = CONFIG::DOC_ROOT . "/Apps/Accounts/Accounts/" . $this->_user->getUUID() . ".png";
      imagepng($img, $path);
      imagedestroy($img);//RAM
    }

    $query = $this->_mysqli->prepare("INSERT INTO `client_info` VALUES(?,?,?,?,?,?,?,?,?,?)");
    $null = NULL; $balance = 0.00; $enabled = 2; $zero = 0;
    $uuid=$this->_user->getUUID(); $name=$this->_user->getName(); $notes=$this->_user->getNotes();
    $query->bind_param("isssdiiiis",$uuid,$name,$pin,$path,$balance,$zero,$enabled,$zero,$zero,$notes);
    $query->execute();
    $query->store_result();
    if($query->affected_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Database error.", "time"=>8000)));
    }

    $query->close();
    return true;
  }

  public function setAccountOpener($account_opener){
    $uuid = $this->_user->getUUID();
    $query = $this->_mysqli->prepare("UPDATE `client_info` SET `account_opened`=? WHERE `uuid`=?");
    $query->bind_param("ii",$account_opener,$uuid);
    $query->execute();
    $query->store_result();
    //minor if it doesn't go. Not worth stopping for. It can be found later with ease if needed
    $query->close();
    return;
  }

  //load selector / selector results
  public function loadAccountDirectory($qString){
    $results = "";
    if(strlen($qString) > 0)
      $results = $this->searchAccountDirectory($qString);
    return $results;
  }

  //Find users by search query
  public function searchAccountDirectory($qString){
    if(is_numeric($qString)){//if searching w/ uuid
      $queryKey = "uuid";
      $columnKey = "display_name";
      $query = $this->_mysqli->prepare("SELECT `display_name`,`account_status` FROM `client_info` WHERE `uuid` LIKE ? ORDER BY `uuid` ASC");
      $query->bind_param("i", $qString);
    }else{
      $queryKey = "display_name";//if searching w/ dispaly_name
      $columnKey = "uuid";
      $query = $this->_mysqli->prepare("SELECT `uuid`,`account_status` FROM `client_info` WHERE `display_name`=? ORDER BY `display_name` ASC");
      $query->bind_param("s", $qString);
    }
    $query->execute();
    $query->bind_result($columnValue, $account_status);
    $output = array();
    while($query->fetch()){
      $output[] = array("primary"=>$qString, "secondary"=>$columnValue, "acntStatus"=>$account_status);
    }
    echo var_dump($query);
    $query->close();
    return $output;
  }

  public function loadAccountSummary(){
    GLOBAL $Operator;
    $retData = array(
      "uuid"=>$this->_user->getUUID(),
      "display_name"=>$this->_user->getName()
    );
    $retData["Transactions"] = $this->getAllTransactions();
    $retData["Baskets"] = $this->getAllBaskets();
	  $retData["Balance"] = $this->_user->getBalance();
    $retData["Status"] = $this->_user->getAccountStatus();
    return $retData;
  }

  public function getAllTransactions(){
    $uuid = $this->_user->getUUID();
    $query = $this->_mysqli->prepare("SELECT `transaction_count` FROM `client_info` WHERE `uuid`=?");
    $query->bind_param("i", $uuid);
    $query->execute();
    $query->store_result();
    $query->bind_result($trans_count);
    $query->fetch();
    $query->close();

    $query = $this->_mysqli->prepare("SELECT `id`,`trans_total`,`line_items` FROM `trans_completed` WHERE `user_id`=?");
    $query->bind_param("i",$uuid);
    $query->execute();
    $results = $query->get_result();
    if($results->num_rows == 0)
      return "No Transactions"; //no transactions

    $transactions = array();
    while($row = $results->fetch_assoc()){//loop through transactions to get line items
      $lis = explode(",",$row["line_items"]);
      $lisn = array_slice($lis,0, (count($lis)-1));
      $ic = count($lisn);
      $lineItems = array();
      for($i=0;$i<$ic;$i++){//loop through line items to get data
        $li = new LineItem($lisn[$i], null);
        $lineItems[] = json_decode($li->genJString());
      }
      $transactions[] = array("id"=>$row["id"], "price"=>$row["trans_total"], "lineItems"=>$lineItems);
    }
    return $transactions;
  }

  public function getAllBaskets(){
    GLOBAL $Operator;
    return "No Baskets";
  }

  public function sendMultiFactor(){
    return true;
  }

  private function setupMultiFactor(){

  }

}
?>
