<?php
  class Operator_Interface{
    private $_operator;//OP_data_obj
      public function getOperator(){return $this->_operator;}

    private $_op_accounts_interface;//accounts
      public function getAccountsInterface(){return $this->_op_accounts_interface;}

    private $_op_transaction_interface;//transaction
      public function getTransactionSession(){return $this->_op_transaction_interface;}

    private $_mysqli;
    private $_status;
      public function getInitStatus(){return $this->_status;}
    public function __construct(){
      if(!function_exists("dieOfError")){
        function dieOfError($code, $message){
          header("tsastatus:500");
          exit(json_encode(array("type"=>1, "message"=>$message, "time"=>8000, "code"=>$code)));
        }
      }
      $this->_mysqli = new TSADB();
      $this->_mysqli = $this->_mysqli->gsql();

      $this->_status = $this->build();
    }

    private function build(){
      $op_session_status = $this->_sessionIDExists();
      if($op_session_status === false){
        if(@isset($_POST["username"]) && @isset($_POST["password"])){
          $login_status = $this->operatorLoginAuthenticate($_POST["username"], $_POST["password"]);
          if($login_status === false){
            return false;
          }else{//Redirect to home after good login
            $op_id = $login_status;
            $this->createOperatorLoginCreateSession($op_id);
            return CONFIG::DOC_ROOT_WEB . "/TSA_Auction.php";
          }
        }else{//No session or login request
          header("tsastatus:400");
          exit(json_encode(array("message"=>"Access Denied")));
        }
      }else{//Valid session, operator id returned
        $op_id = $op_session_status;
        if($this->_resumeSession($op_id) === false){
          dieOfError(104, "[OP_104]Error Resuming Operator Session!");
        }
      }
    }

    private function _resumeSession($op_id){
      $this->_operator = new Operator($op_id);

      if(!$this->checkPermission("Operator_canLogin")){
        Operator_Interface::doLogout();
        header("tsastatus:400");
        exit(json_encode(array("message"=>"Session Terminated")));
      }

      if(!$this->checkPermission("Operator_canOperate")){
        header("tsastatus:400");
        exit(json_encode(array("message"=>"Permission Denied")));
      }

      //op validated, just loading empty page w/ navigation
      //Needed so that next request can be 2002 or 2003.1
      if(@$_GET["action"] == "2000"){
        return true;
      }

      //qrGateway || createUserAccount
      if(@$_GET["action"] == 2002 || @$_GET["action"] == 2003.1){//false returns on daemon binds are for New daemon creation functions
        //Selecting new user - Both die on failure
        if($_GET["action"] == 2003.1){
          if(strlen(@$_POST["uuid"]) < 5){
            dieOfError(108, "[OP_108]Unable to create user account - bad data from client");
          }
          $uuid = $_POST["uuid"];
        }else{
          if(strlen(@$_POST["data"]) < 5){
            dieOfError(108, "[OP_108]Unable to create user account - bad data from client");
          }
          $uuid = $_POST["data"];

          //non-existent account being requested w/ no create request
          $account_status = Accounts::accountExists($uuid);
          if($account_status == -1){
              vis_loadAccountCreate();
          }else{//Sent create request for account that already exists, bind and move on (malicious request)
            $this->_bindNewUserAccountDaemon($uuid);
            return;
          }
        }

        $this->_bindNewUserAccountDaemon($uuid);

        if(@$_GET["action"] == 2003.1){//just created user, need to charge entrance fee
          $entrance_fee = htmlentities($_POST["entrance_fee"]);
          if($entrance_fee < 0)
            dieOfError(107, "[OP_107]Unable to Charge Entrance Fee - Bad Value From Client");

          $this->_op_transaction_interface->addLineItem(1, $entrance_fee);
          $account_opener = $this->_op_transaction_interface->getTransSession();
          $this->_op_transaction_interface->chargeToAccount();
          $this->_op_accounts_interface->setAccountOpener($account_opener);

        }else{//Not a create
          return true;
        }
      }else{
        //Not selecting new user, just resumes from data in op_session table
        if($this->_bindAccountDaemon() === false){//no account of work, no transactions to work
          return true;
        }

        if($this->_bindTransactionDaemon() === false){//no transaction to start daemon
          $this->_createNewTransactionDaemon();//Has a UUID bound, need to create transaction to allow charges
          return true;
        }
      }

      return true;
    }

    //Check if operator session id exists in database
    private function _sessionIDExists(){
      $session = session_id();
      $query = $this->_mysqli->prepare("SELECT `op_id` FROM `op_sessions` WHERE `session_id`=? LIMIT 1");
      $query->bind_param("s", $session);
      $query->execute();
      $query->store_result();
      if($query->num_rows !== 1)
        return false;

      $query->bind_result($op_id);
      $query->fetch();
      $query->close();

      return $op_id;
    }

    //
    //  V: Logout of account
    //
    public static function doLogout(){
      //Delete OP ID & Session ID Cookie. Destroy PHP Session
      setcookie("TSAOP", "", time()-3600);
      session_destroy();
    }

    private function operatorLoginAuthenticate($username, $password){
      $query = $this->_mysqli->prepare("SELECT `id`,`secret` FROM `op_credentials` WHERE `username`=?");
      $query->bind_param("s", $username);
      $query->execute();
      $query->store_result();
      if($query->num_rows !== 1)
        return false;

      $query->bind_result($op_id, $secret);
      $query->fetch();
      $query->close();
      if(!(md5($password) === $secret))
        return false;

      return $op_id;
    }

    private function createOperatorLoginCreateSession($op_id){
      session_start();
      $sid = session_id();
      $ip = $_SERVER["REMOTE_ADDR"];
      $hostname = gethostbyaddr($ip);
      $expire = time() + (3600 * 24);
      $oneRef = intval("-1");

      $query = $this->_mysqli->prepare("INSERT INTO `op_sessions` VALUES(?,?,?,?,?,?,?)");
      $query->bind_param("issiiss", $op_id, $sid, $expire, $oneRef, $oneRef, $ip, $hostname);
      $query->execute();
      $query->store_result();
      if($this->_mysqli->affected_rows !== 1)
        dieOfError(103, "[OP_103]Error Creating Operator Session in Database!");

      //Set cookies to expire in 24 hours
      setcookie("TSAOP", $op_id, $expire, "/", CONFIG::OPS_SESSION_RESTRICT, CONFIG::OPS_SSL_ENABLED);
    }

    //
    //  Check if Permission class has given permission
    //    $this->_permission_level must be set (done during authentication)
    public function checkPermission($permission){
      $permission_level = $this->_operator->getOpPermissionLevel();
      $query = $query = $this->_mysqli->query("SELECT `$permission` FROM `op_permissions` WHERE `id`='$permission_level' AND `$permission`='1' AND `isEnabled`=1");
      if($query->num_rows == 1)
        return true;
      return false;
    }

    public function registerNewAccountsSesssion($uuid){
      if(Accounts::accountExists($uuid) == -1){//No Automated switch to non-existent account
        dieOfError(105.1, "[OP_105.1] Error shifting operator session to new user via internal reload");
      }
      $this->_bindNewUserAccountDaemon($uuid);
    }

    public function registerNewTransactionSession(){
      $this->_createNewTransactionDaemon();
    }

    //Create User Data obj / Accounts interface
    //return true if valid uuid supplied
    //return false if invalid / non-existent
    private function _bindAccountDaemon(){
      $user_uuid = $this->_getCurrentUserUUID();
      $User = new User($user_uuid);
      $this->_op_accounts_interface = new Accounts($User);
      return true;
    }

    private function _getCurrentUserUUID(){
      $op_id = $this->_operator->getOpID();
      $query = $this->_mysqli->prepare("SELECT `current_client` FROM `op_sessions` WHERE `op_id`=?");
      $query->bind_param("i", $op_id);
      $query->execute();
      $query->store_result();
      if($query->num_rows !== 1)
        return false;

      $query->bind_result($user_uuid);
      $query->fetch();
      $query->close();

      if($user_uuid === -1){
        return false;
      }
      return $user_uuid;
    }

    //Create LineItems Data obj / Transaction interface
    //return true if valid transaction id supplied
    //return false if invalid / non-existent
    private function _bindTransactionDaemon(){
      $op_id = $this->_operator->getOpID();
      $query = $this->_mysqli->prepare("SELECT `current_sale` FROM `op_sessions` WHERE `op_id`=?");
      $query->bind_param("i", $op_id);
      $query->execute();
      $query->store_result();
      if($query->num_rows !== 1)
        return false;

      $query->bind_result($op_sale);
      $query->fetch();
      $query->close();

      if($op_sale === -1)
        return false;

      $this->_op_transaction_interface = new Transaction($op_sale, $this);
      return true;
    }

    private function _bindNewUserAccountDaemon($new_uuid){
      $curr_user_uuid = $this->_getCurrentUserUUID();
      if($curr_user_uuid == $new_uuid){//if trying to run new bind for current ID, block and just "resume"
        $this->_bindAccountDaemon();
        $this->_bindTransactionDaemon();
        return true;
      }

      if($curr_user_uuid === false){//Easy, no current user, just insert
        $this->_createUserAccountDaemon($new_uuid);
        $this->_createNewTransactionDaemon();
      }else{
        //difficult, active and might have transaction
        if($this->_bindTransactionDaemon() === false){//easy, no transaction
          $this->_createUserAccountDaemon($new_uuid);
          $this->_createNewTransactionDaemon();//kills on error
        }else{//their is a transaction
          if($this->_op_transaction_interface->isTransactionEmpty() === true){//Emtpy session
            $this->_op_transaction_interface->markTransactionEmpty();
            $this->_createUserAccountDaemon($new_uuid);
            $this->_createNewTransactionDaemon();//kills on error
          }else{//charge has items on it
            dieOfError(106.1, "[OP_106.1]Unable to Shift Operator Session With Line Items Selected. Please Clear or Checkout the Current Account Before Proceeding");
          }
        }
      }
    }

    private function _createUserAccountDaemon($new_uuid){
      if(Accounts::accountExists($new_uuid) === -1){
        $User = new User(
          array(
            "uuid"=>intval(htmlentities(@$_POST["uuid"])),
            "name"=>htmlentities(@$_POST["name"]),
            "notes"=>htmlentities(@$_POST["notes"])
          )
        );
        $Accounts = new Accounts($User);
        $Accounts->createUserAaccount(@$_POST["entrance_fee"], @$_POST["pin"], @$_POST["profileIDIMG"]);
        //entrance fee charged after transaction daemon connected
      }
      $op_id = $this->_operator->getOpID();
      $query = $this->_mysqli->prepare("UPDATE `op_sessions` SET `current_client`=? WHERE `op_id`=?");
      $query->bind_param("ii",$new_uuid,$op_id);
      $query->execute();
      $query->store_result();
      if($this->_mysqli->affected_rows != 1){
        dieOfError(105, "[OP_105]Error Shifting Operator Session to New User.");
      }
      $query->close();

      $User = new User($new_uuid);
      $this->_op_accounts_interface = new Accounts($User);
      return true;
    }

    //Only runs post User Change && Post transaction close
    private function _createNewTransactionDaemon(){
      $this->_op_transaction_interface = new Transaction(-1, $this);
      $trans_id = $this->_op_transaction_interface->getTransSession();
      $op_id = $this->_operator->getOpID();
      $query = $this->_mysqli->prepare("UPDATE `op_sessions` SET `current_sale`=? WHERE `op_id`=?");
      $query->bind_param("ii",$trans_id,$op_id);
      $query->execute();
      $query->store_result();
      if($this->_mysqli->affected_rows != 1){
        dieOfError(106, "[OP_106]Error Shifting Operator Session to New Transaction.");
      }
      $query->close();
      return true;
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
  }
?>
