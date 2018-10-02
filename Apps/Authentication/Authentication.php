<?php
	if(!class_exists("CONFIG"))
		require("/var/www/html/2018/Apps/Config/Config.php");
	if(!class_exists("AppMysqli"))
		require(CONFIG::DOC_ROOT . "/Apps/Mysqli/Mysqli.php");

	class Authentication{
		private $_OP_ID; public function getOPID(){return $this->_OP_ID;}
		private $_DEVICE_IP;

		private $_mysqli;
		private $_logger; public function getLogger(){return $this->_logger;}
		public function __construct(){
			if(CONFIG::SYS_LOCKOUT)//If system is in lockdown / failed state
				return false;
			$this->initLogger();
		}

		private function initLogger(){
			if(!class_exists("Logger"))
				require(CONFIG::DOC_ROOT . "/Apps/Error/Logger.php");

			$this->_logger = new Logger();
		}

		//TODO: Consider the weird Auth->mysqli->auth->initBasicMysql thing going on
		private function initMysql(){
			$this->_mysqli = new AppMysqli();
			$this->_mysqli = $this->_mysqli->initMysql();
			return true;
		}

		////////////////////////////////////////
		//	Verify operator session exists
		//
		public function validateOperatorSession(){
			if(!$Mysqli = AppMysqli::initBasicMysql())
				return false;

			$this->_OP_ID = @$Mysqli->real_escape_string($_COOKIE["ATHOPID"]);//AuTHOPID
			if(!isset($this->_OP_ID))
				return false;

			$this->_DEVICE_IP = $_SERVER["REMOTE_ADDR"];//Client IP

			$query = $Mysqli->query("SELECT `ID` FROM `app_authSessions` WHERE `ID`='$this->_OP_ID' AND `Device`='$this->_DEVICE_IP'");
			if($query->num_rows == 1){//Page will redirect to login page if false returns (TSA_AUCTION)
				return true;
			}
			setcookie("ATHOPID","",(time()-1000),"/","",false,true);//Delete invalid cookie
			return false;

		}

		///////////////////////////////////////////////TODO: Operator login
		//Check for active sessions in database. Session counts as unique IP addr with activity in last 10 minutes on that operator (`TIE`)
		//If too many within 10 minutes, clear cookies, prompt login and explain too many logged in
		//If too many, but outside 10 minutes, replace one old with new
		//If not exist, prompt login.
		//Currently unimplimented. Need to modify registerOPSession() to utilize
		private function allowedInstances(){
			$this->_OP = $this->Mysqli->real_escape_string($this->_OP);
			$query = $this->Mysqli->query("SELECT `SESH_ID`,`ACTIVE` FROM `OPS_SESSIONS` WHERE `TIE`=$this->_OP AND `IP`=" . $_SERVER["REMOTE_ADDR"]);

			if($query->num_rows <= TSA_CONSTANTS::OPS_MAXLOGIN && $query->num_rows >0){
				$rows = $this->Mysqli->fetch_assoc();
				$SESH_ID = $rows["SESH_ID"];

				$this->Mysqli->query("UPDATE `OPS_SESSIONS` SET `ACTIVE`=" . time() ." WHERE `SESH_ID`=" . $SESH_ID);//Update last active
				return true;
			}else if($query->num_rows > TSA_CONSTANTS::OPS_MAXLOGIN){

			}else{

			}
		}

		///////////////////////////////////////////
		//	Return html code to prompt for login
		//
		final public function getAuthPrompt(){
			return 	array("action"=>"authLoader","args"=>"");
		}

		///////////////////////////////////////////
		//Register new op session with database. Called by op_driverlogin after sucesful validation of credidentials
		//True on success
		//False on error. Echo error to user if applicable
		public function registerOPSession($user,$secure){
			if(!$_COOKIE["ATHOPSID"] > 0)
          		$this->destroySession();
          	
          	try {
				$Mysqli = AppMysqli::initBasicMysql();
			} catch (Exception $e) {
				echo "<script type='text/javascript'>
					displayError('Unable to initiate a connection to the database!',8000);
					</script>";
				return false;
			}
          	
          	$authID = $Mysqli->real_escape_string($_COOKIE["ATHOPID"]);
          	$Mysqli->query("SELECT `authSecure` FROM `app_authSessions` WHERE `authID=$authID");
          	if($Mysqli->num_rows != 0)
				$this->destroySession();//Delete if multiple or already existing sessions
            
          	$hostname = gethostname();
          	$clientIp = $_SERVER["REMOTE_ADDR"];
          	$authHash = md5($hostname . $user . $clientIp);//authID
          	
          	$secure = $Mysqli->real_escape_string($secure);//(BOOL)Identity session || security authentication session	
          
          	$Mysqli->query("INSERT INTO `app_authSessions VALUES('','$authHash','','','$secure')");
          
			setcookie("ATHOPSID",$Mysqli->insert_id,time()+80400,"/","192.168.1.249",false, true);
          	setcookie("ATHOPID", $authHash, time()+80400, "/", CONFIG::SVR_IP, false, true);
			return true;
		}

		//Update transactionID or uuid in op sessions db
		public function updateOpSession($key, $value){
			//if($key != "Cuur_userID" && $key != "Curr_transactionID")//Needed?
				//return false;
			//if(!isset($value))
				//return false;
			if(!$this->initMysql())
				exit(json_encode(array("action"=>"compiledDisplayError", "args"=>array("message"=>"Database Communication Error! Issue has been reported.","time"=>8000))));

			$opSessionID = $this->_mysqli->real_escape_string($_COOKIE["ATHOPID"]);
			$query = $this->_mysqli->query("UPDATE `app_authSessions` SET `$key`='$value' WHERE `ID`='$opSessionID'");
			if($query != 1)
				exit(json_encode(array("action"=>"compiledDisplayError", "args"=>array("message"=>"Database Communication Error! Issue has been reported. Restarting this transaction is recomended.","time"=>8000))));
			return true;
		}

      	public function destroySession(){
          //Check that hash value corisponds with SID trying to delete
        }	
      
		public function reportLoginFail($user){
			$ip = $ip = $_SERVER["REMOTE_ADDR"];
			if(!class_exists("AuthenticationFailureException"))
				require(CONFIG::DOC_ROOT . "/Apps/Error/Exceptions/AuthenticationFailureException.php");

				$authFailException = new AuthenticationFailureException("User failed to authenticate while loggin in", 0, null, $user, $ip);

				$this->_logger->draftLog($authFailException);
				exit("Auth Fail");
		}
	}
?>
