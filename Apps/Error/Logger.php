<?php 
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	class Logger{
		private $_logPath = CONFIG::DOC_ROOT . "/Apps/Error/Errors/";
		
		private $_fileName;
		private $_humanTime;
		private $_deviceSession = array();
		private $_logText;
		public function __construct(){
			if(CONFIG::SYS_LOCKOUT)//If system is in lockdown / failed state
				return false;
		}
		
		//////////////////////////////////
		//	Write log
		//	exception to log about
		public function draftLog(Exception $e){
			$this->fileName($e);
			$this->humanTime();
			$this->deviceSession();
			$this->logText($e);
			$this->writeLogToFile();
		}
		
		//File name for log file | Prefixed by exception type
		private function fileName(Exception $e){
			$this->_fileName = get_class($e) . "_" . time() . ".txt";
		}
		
		//Get human readable time
		private function humanTime(){
			$this->_humanTime = date("G:i:s");//20:54:32
		}
		//Gather information about device session
		private function deviceSession(){
			array_push($this->_deviceSession, @$_COOKIE["ATHOPID"]);
			array_push($this->_deviceSession, $_SERVER["REMOTE_ADDR"]);
		}
		
		//Generate text to include in log emssage:
		//Session - date
		//errorCode:[line]
		//message
		//stack trace
		private function logText(Exception $e){
			@$this->_logText = $this->_deviceSession[1] . " - " . $this->_humanTime. "\n" . 
			$e->getCode() . ":[" . $e->getLine() . "](line)\n" . $e->getMessage() . "\n Stack Trace: \n" .
			var_export($e->getTrace(), true);
		}
		
		//Write log file with the currently defined proprties
		private function writeLogToFile(){
			$file = fopen($this->_logPath . $this->_fileName, "w");
			fwrite($file, $this->_logText);
		}
		
		//TODO: Make function to view log
		public function viewLog(){
			
		}
	}
?>