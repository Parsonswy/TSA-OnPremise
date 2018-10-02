<?php
//Todo, smart error display to user @ all "ERR"
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	
	if(CONFIG::SYS_LOCKOUT)//TODO:: inteligent redirects back (restore to exact point in transaction)
		header("Location:" . CONFIG::DOC_ROOT_WEB . "/Apps/Config/Lockout.php?prevURL=" . CONFIG::DOC_ROOT_WEB . "/TSA_AUCTION.php");
	
	if(!class_exists("Authentication"))
		require (CONFIG::DOC_ROOT . "/Apps/Authentication/Authentication.php");
	
	if(!class_exists("User"))
		require (CONFIG::DOC_ROOT . "/Apps/User/User.php");
//TransType
//1:Fee,2:Game/Custom,3:Basket
class Transaction{
	private $_transID;		//Transaction ID
	public function getTransID(){return $this->_transID;}
	private $_transOp;		//Transaction Operator
	private $_transUUID;	//Transaction User
	private $_transCharge = 0;	//$$$
	private $_transDesc = "";	//Description of what $$$ was for
	
	//Core Class Refences
	private $_logger;		//Logger Reference
	private $_mysql;		//Mysqli Reference
	private $_user;			//User Reference
	private $_auth;			//Authentication Reference

	public function __construct($transID = NULL){
		if(CONFIG::SYS_LOCKOUT)//Check for lockout
			exit("SIG_TERM_LOCKOUT [" + CONFIG::SYS_LOCKOUT);

		if(CONFIG::OPS_ENABLED){
			$this->_auth = new Authentication();
			if(!$this->_auth->validateOperatorSession()){//Verify operator session exists
				exit(json_encode(Authentication::getAuthPrompt()));
			}
		}

		$AppMysqli = new AppMysqli();
		if(!$this->_mysql = $AppMysqli->initMysql())
			exit("ERR - Database Init");
		
		if(!class_exists("Logger"))
			require(CONFIG::DOC_ROOT . "/Apps/Error/Logger.php");
		$this->_logger = new Logger();
			
		if(!class_exists("TableItems"))
			require(CONFIG::DOC_ROOT . "/Apps/Commerce/TableItems.php");
		
		if(!class_exists("TransactionException"))
			require(CONFIG::DOC_ROOT . "/Apps/Error/Exceptions/TransactionException.php");
		
		$this->_transID = $transID;
		$this->_transOp = $this->_mysql->real_escape_string($_COOKIE["ATHOPID"]);
		$this->_transUUID = $this->_mysql->real_escape_string(@$_COOKIE["CURRUD"]);
	}
	public function newTransaction($param){
		$this->totalTransaction($param);
		try{
			$this->_mysql->query("UPDATE `app_users` SET `balance` = (`balance` + '$this->_transCharge') WHERE `uuid`=$this->_transUUID");
			if(!$this->_mysql->affected_rows == 1){//Log error and note to op that user was not charged
				throw new TransactionException("Failed to charge User(ID)[" . $this->_transUUID . "] " . $this->_transCharge . " for \n" . var_dump($this->_transDesc),
						"",NULL,$this->_transOp,false);
			}
			$time = date("G:i:s");
				$this->_mysql->query("INSERT INTO `app_transTransactions` VALUES('',
						'$this->_transOp',
						'$this->_transUUID',
						'$this->_transDesc',
						'$this->_transCharge',
						'1',
						'$time')");
				if(!$this->_mysql->affected_rows == 1){//Log error and note to op that user was charged
					throw new TransactionException("Failed to log user transaction (ID)[" . $this->_transUUID . "] $" . $this->_transCharge . " \n Details: \n " . $this->_transDesc,
							"",NULL,$this->_transOp,true);
				}
				$this->_transID = $this->_mysql->insert_id;
		}catch(TransactionException $e){
			$this->logTransactionError($e);
			$wasCharged = ($e->wasCharged())? "User was charged correct ammount (" . $this->_transCharge . ")" : "User was not charged";
			return array(false, "An error occured while attempting to process this transaction. " . $wasCharged . ". REFID:" . time());
		}
		return array(true, $this->_transID);
	}
	
	//x,y,z,a,b,c,...,custom$,'customDesc' ($'s)
	private function totalTransaction($param){
		$loopI = count(TableItems::GAME_LIST);
		$param = explode(",",$param);
		for ($i = 0; $i < $loopI; $i++) {
			if(@$param[$i] > 0){
				$this->_transCharge += $param[$i];
		
				$gameParam = TableItems::GAME_LIST[$i];//Get game parameters
				$loopJ = count($gameParam["OPTS"]);
				for($j = 0;$j < $loopJ; $j++){							//Loop through all possible pricing options listed in sysConstants for specified game
					if($gameParam["OPTS"][$j]["VALUE"] == $param[$i]){	//Check if item charge matches value for $i PRICE[S]
						$this->_transDesc .= $gameParam["OPTS"][$j]["DESC"] . " | ";	//Select corresponding item description if match
						break;																//Break out of loop because correct value found
					}
				}
			}
		}
		$customCharge = (isset($param[count(TableItems::GAME_LIST)]))? $param[count(TableItems::GAME_LIST)] : 0;
		$this->_transCharge += $customCharge;
		$this->_transDesc .= (strlen($param[count(TableItems::GAME_LIST) +1]) >= 2)? "$" . $customCharge . " - " .  $this->_mysql->real_escape_string($param[count(TableItems::GAME_LIST) + 1]) : "";
	}
	
	//TODO: more preminent solution to the OPSESSION CURRUD cookie not getting updated to
	//properly charge user accounts entires
	//Exists because OP session not updated until after user created
	public function chargeEntryFee($uuid, $partyCount){
		$charge = $partyCount * (TableItems::ENTRY_FEE);
		$this->_transUUID = $uuid;
		try{
			$this->_mysql->query("UPDATE `app_users` SET `balance`=`balance` + $charge WHERE `uuid`=$this->_transUUID");
			if(!$this->_mysql->affected_rows == 1){//Log error and note to op that user was not charged
				throw new TransactionException("Failed to charge User(ID)[" . $this->_uuid . "] " . $charge . "for entry of " . $partyCount,
												"",NULL,$this->_transOp,false);
			}
			
			$time = date("G:i:s");
			
			$this->_mysql->query("INSERT INTO `app_transTransactions` VALUES('',
					'$this->_transOp',
					'$this->_transUUID',
					'Entrty Fee x $partyCount',
					'$charge',
					'1',
					'$time')");
			if(!$this->_mysql->affected_rows == 1){//Log error and note to op that user was charged
				throw new TransactionException("Failed log user transaction (ID)[" . $this->_uuid . "] " . $charge . " \n Details: \n Entry Fee x " . $partyCount,
						"",NULL,$this->_transOp,true);
			}
		}catch(Exception $e){
			$this->logTransactionError($e);
			$wasCharged = ($e->wasCharged())?"User was charged correct ammount (" . $charge . ")" : "User was not charged";
			exit(array(false, "An error occured while attempting to process this transaction. " . $wasCharged . ". REFID:" . time()));
		}
		return array(true);
	}
	
	public function chargeBasketItem($uuid,$basketDesc, $charge){
		try{
			$this->_mysql->query("UPDATE `app_users` SET `balance`=`balance` + $charge WHERE `uuid`=$uuid");
			if(!$this->_mysql->affected_rows == 1){//Log error and note to op that user was not charged
				throw new TransactionException("Failed to charge User(ID)[" . $uuid . "] " . $charge . " for " . $basketDesc,
						"",NULL,$this->_transOp,false);
			}
				
			$time = date("G:i:s");
				
			$this->_mysql->query("INSERT INTO `app_transTransactions` VALUES('',
					'$this->_transOp',
					'$uuid',
					'$basketDesc',
					'$charge',
					'2',
					'$time')");
			if(!$this->_mysql->affected_rows == 1){//Log error and note to op that user was charged
				throw new TransactionException("Failed log user transaction (ID)[" . $uuid . "] " . $charge . " \n Details: \n" . $basketDesc . 
						"",NULL,$this->_transOp,true);
			}
			$this->_transID = $this->_mysql->insert_id;
		}catch(Exception $e){
			$this->logTransactionError($e);
			$wasCharged = ($e->wasCharged())?"User was charged correct ammount (" . $charge . ")" : "User was not charged";
			exit(array(false, "An error occured while attempting to process this transaction. " . $wasCharged . ". REFID:" . time()));
		}
		return array(true);
	}
	
	//Returns parameters of transaction entry with transDesc broken up into array of items ("desc","price")("desc","price")...
	public function getTransaction(){
		if(!isset($this->_transID))
			return 0;
		
		$query = $this->_mysql->query("SELECT * FROM `app_transTransactions` WHERE `transID`=$this->_transID");
		$retArray =  $query->fetch_assoc();
		
		//Break into itemized descriptions
		$transItems = explode(" | ",$retArray["transDesc"]);
		$retArray["transDesc"] = array();//Clear transDesc to array for array_push in loop
		
		
		$loopI = count($transItems);//-1 removes the empty bit on the end that comes from appending the "|" after each item
		for($i=0;$i<$loopI;$i++){	
			$startPriceChars = strpos($transItems[$i],"$");					//Find '$' where price starts
			$endPriceChars = strpos($transItems[$i]," ",$startPriceChars);	//Look for first 'space' after '$' for price end
			
			$pushData = array("desc"=>$transItems[$i],"price"=>substr($transItems[$i],$startPriceChars,($endPriceChars - $startPriceChars)));//returns '$' to last digit
			array_push($retArray["transDesc"], $pushData);
		}
		return $retArray;
	}
	
	public function cashOut(){
		$query = $this->_mysql->query("SELECT `transDesc`,`transPrice`,`transType` FROM `app_transTransactions` WHERE `transUUID`=$this->_transUUID");
		$retBuffer = array("transID"=>"CashOut", "customer"=>$this->_transUUID,"transUUID"=>$this->_transUUID,"transDesc"=>array());
		$total = 0;
		while($rows = $query->fetch_assoc()){
			array_push($retBuffer["transDesc"],array("desc"=>$rows["transDesc"],"price"=>$rows["transPrice"]));
			$total += $rows["transPrice"];
		}
		$retBuffer["transOP"] = $this->_auth->getOPID();
		$retBuffer["transPrice"] = $total;
		$this->_mysql->query("UPDATE `app_users` SET `balance`=0,`active`=2 WHERE `uuid`=$this->_transUUID");
		
		$time = date("G:i:s");
		$retBuffer["time"] = $time;	
		
		$this->_mysql->query("INSERT INTO `app_transTransactions` VALUES('',
				'$this->_transOp',
				'$this->_transUUID',
				'Cashout - $this->_transUUID',
				'$total',
				'3',
				'$time')");
		return array(true,$retBuffer);
	}
	
	private function logTransactionError($e){
		$this->_logger->draftLog($e);
	}
}
?>
