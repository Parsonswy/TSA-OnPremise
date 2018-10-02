<?php
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	if(!class_exists("Authentication"))
		require(CONFIG::DOC_ROOT . "/Apps/Authentication/Authentication.php");
	class AppMysqli{
		static private $_logger;
		private $_auth;
		public function getMysqlAuth(){if($this->_auth instanceof Authentication){return $this->_auth;} else{return false;}}
		public function __construct(){
			if(CONFIG::SYS_LOCKOUT)//Check for lockout
				exit("SIG_TERM_LOCKOUT [" + CONFIG::SYS_LOCKOUT);
			
			if(CONFIG::OPS_ENABLED){
				$this->_auth = new Authentication();
				/*if(!$this->_auth->validateOperatorSession()){//Verify operator session exists
					exit(json_encode(Authentication::getAuthPrompt()));
				}*/
			}
			self::initLogger();
		}
		final private function initLogger(){
			if(!class_exists("Logger"))
				require(CONFIG::DOC_ROOT . "/Apps/Error/Logger.php");
			
			self::$_logger = new Logger();
		}
		
		///////////////////////////////////////
		//	Create connection to mysql database
		//
		public function initMysql(){
			if(!($Mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa_2017"))){
				$ConnFailException = new UnexpectedValueException("[FATAL]Failed to initiate database connection!", $this->Mysqli->errno());
				self::reportConnectionFail($ConnFailException);
				return false;
			}
			return $Mysqli;
		}

		//////////////////////////////////
		//	Basic view access to tabcards database
		//	app_AuthSessions  Insert, Select, update, insert on authID, Insert, select on authSecure
      //
		final public function initBasicMysql(){
			if(!($Mysqli = new mysqli("localhost", "tsa_sec_query", "E4QhFbUcpq2o6Pr2", "tsa_2017"))){
				$ConnFailException = new UnexpectedValueException("[FATAL]Failed to initiate database connection! \n" . $mysqli->error(), $mysqli->errno());
				self::reportConnectionFail($ConnFailException);
				return false;
			}
			return $Mysqli;
		}
		
		final private function reportConnectionFail(UnexpectedValueException $e){
			self::$_logger->draftLog($e);
		}
		
		final public function reportCommunicationFail(UnexpectedValueException $e){
			self::$_logger->draftLog($e);
		}
	}
?>