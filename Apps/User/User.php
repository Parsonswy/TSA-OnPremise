<?php
//vap = viewAccountProfile
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	class User{
		private $_isAuthenticated; //Pased Auth. check
		private $_uuid;	//UUID of customer
		public function getUUID(){return "UUID:" . $this->_uuid;}
		public function setUUID($uuid){$this->_uuid = $uuid;}
		private $_partyID;
		
		private $_auth;
		private $_mysqli;
		private $_render;
		public function __construct($uuid = 0){
			if(CONFIG::SYS_LOCKOUT)//Check for lockout
				exit("SIG_TERM_LOCKOUT");//TODO:Standardize
			if(CONFIG::OPS_ENABLED){
				if(!class_exists("Authentication")){
					require(CONFIG::DOC_ROOT . "/Apps/Authentication/Authentication.php");
				}
				$this->_auth = new Authentication();
				/*if(!$this->_auth->validateOperatorSession()){//Verify operator session exists
					exit(json_encode(Authentication::getAuthPrompt()));//Prompt for login if not
				}*/
				$this->_isAuthenticated = true;
			}else{
				$this->_isAuthenticted = true;
			}
			if(!$this->initMysql())
				return false;

			$this->_uuid = $this->_mysqli->real_escape_string($uuid);
		}
		
		/////////////////////////////////////////
		//	Initiate MysqlApp for mysql connection
		//
		private function initMysql(){
			if(!$this->_isAuthenticated)
				return false;
			
			if($this->_mysqli instanceof Mysqli)
				return true;
			
			if(!class_exists("AppMysqli"))
				require(CONFIG::DOC_ROOT . "/Apps/Mysqli/Mysqli.php");
			$AppMysqli = new AppMysqli();
			if(!$this->_mysqli = $AppMysqli->initMysql())
				return false;	
			return true;
		}
		
		final private function initRender(){
			if(!class_exists("Render")){
				require(CONFIG::DOC_ROOT . "/Apps/Render/Render.php");
			}	
		}
		
		/////////////////////////////////////////
		//	Open account with given properties
		//	userAccountController verifies data exists before passing
		public function openAccount($uuid, $name, $partyCount){
			$this->_uuid = $this->_mysqli->real_escape_string($uuid);
			$name = $this->_mysqli->real_escape_string($name);
			$partyCount = $this->_mysqli->real_escape_string($partyCount);
			$this->_mysqli->query("UPDATE `app_users` SET `name`='$name',`active`='1' WHERE `uuid`='$this->_uuid'");
			if(!$this->_mysqli->affected_rows == 1)
				return array(false,"UUID not found");
			
			
			if(intval($partyCount) <= 0)
				return array(true);
			
			require(CONFIG::DOC_ROOT . "/Apps/Commerce/Transaction.php");
			$Transaction = new Transaction();
			$entryFee = $Transaction->chargeEntryFee($this->_uuid, $partyCount);
			
			//Pass Error Up
			if(!$entryFee[0])
				return array(false,$entryFee[1]);
			
			return  array(true);
		}
		
		//Convert uuid list to names
		//Comes back with <br/>'s b/c easier to remove than add in later
		public function UUIDstoNames(array $uuids){
			$res = array();
			foreach($uuids as $uuid){
				$data = $this->getAccountProperty($uuid, array("name"));
				array_push($res, $data["name"]);
			}
			return $res;
		}
		////////////////////////////////////////
		//	Modify account information. Balance can also be modified by Transaction app
		//	[0]=>
		//		[0]="property",[1]=>"value"
		//Probably obsolete at this point TODO: Recon removal
		public function appendAccountProperty(array $properties){
			
		}
		
		public function accountIsActive($uuid){
			$uuid = $this->_mysqli->real_escape_string($uuid);
			$query = $this->getAccountProperty($uuid, array("active"));
			if($query["active"] == 1)
				return true;
			else
				return false;
		}
		
		//////////////////////////////////////
		//	Get account property(ies)
		//	objuuid || supplied uuid [0]=>"property",[1]=>"property"
		private function getAccountProperty($uuid, array $properties){
			if(!strlen($uuid) == 6)
				$uuid = $this->_uuid;
			$numProp = count($properties);
			$sql= "";
			foreach($properties as $property){
				$sql .= "SELECT `$property` FROM `app_users` WHERE `uuid`=$uuid;";
			}
			if(!($query = $this->_mysqli->multi_query($sql))){
				return false;
			}
			
			$res = array();
			do{
				if($row = $this->_mysqli->store_result()){//Sketchy workaround for propry not yet accessible bug/error thing
					$loc = $row->fetch_field()->name;//Take data fields and send back associative array with column=>data
					$cell = $row->fetch_assoc();
					$res["$loc"] = $cell["$loc"];
					$row->free();
				}
			//While are more results and prepare next result
			}while($this->_mysqli->more_results() && $this->_mysqli->next_result());
			return $res;
		}
		
		/////////////////////////////////////
		//	Account Info Page
		//
		public function viewAccountPortal(){
			//Query User Database for name/balance
			//Query Transaction Database for trans
			//Query Transaction database for baskets
			if(!$this->initMysql())
				return array(false, "Internal Error");
			
			//Query for user data
			$userData = $this->vap_userProps();
			if(!$userData)return false;
	//var_dump($userData);
			//Query for transaction data
			$transData = $this->vap_transProps();
	//var_dump($transData);
			//Total transactions need to drop from this array and move to userdata
			
			//Query for basket data
			$basketData = $this->vap_basketProps();
	//var_dump($basketData);
			
			require(CONFIG::DOC_ROOT . "/Apps/Render/elements_UserAccountPortal.php");//Get display templates
			$portalManager = new ElemUserAccountPortal($transData,$basketData,$userData);
			return $portalManager->genOutput();
		}
		
		//seperate to keep viewAccountPortal clean and pretty
		private function vap_userProps(){
			$properties = array("balance","name","active");
			$userData = $this->getAccountProperty(null ,$properties);
			if(!$userData)
				return false;
			
			//Format for ElemUserAccountPortal
			return array("userBalance"=>$userData["balance"],"userName"=>$userData["name"],"userTranses"=>"soonTM");
		}
		
		//TODO:Make it so that transaction actually handles this bit
		private function vap_transProps(){
			$query = $this->_mysqli->query("SELECT `transID`,`transDesc`,`transPrice`,`transType` FROM `app_transTransactions` WHERE `transUUID`=$this->_uuid ORDER BY `transTime` ASC");
			if($query->num_rows < 1)
				return null;
			
			$retBuffer = array();
			while($rows = $query->fetch_assoc()){
				array_push($retBuffer, array("entryID"=>$rows["transID"], "entryDesc"=>$rows["transDesc"], "entryCost"=>$rows["transPrice"]));
			}
			return $retBuffer;
		}

		private function vap_basketProps(){
			$query = $this->_mysqli->query("SELECT `ID`,`name`,`price` FROM `app_comInventory` WHERE `WINNER`=$this->_uuid");
			if($query->num_rows < 1)
				return null;
			
			$retBuffer = array();
			while($rows = $query->fetch_assoc()){
				array_push($retBuffer, array("basketID"=>$rows["ID"], "basketDesc"=>$rows["name"], "basketPrice"=>$rows["price"]));
			}
			return $retBuffer;
		}
		////////////////////////////////////
		//	Close user account. (Called by transaction)
		//	Only sets active to 0. Transaction app must clear balance
		public function closeAccount($uuid){
			
		}
		
		//////////////////////////////////
		//	Typing suggestions
		//	userAccountSelector.php
		public function autoFillIndex($archive, $key_property, $key_phrase){//True/false archive database, propery to search, value to search for
			$database = ($archive)? "app_usersArchive" : "app_users";
			if(!strlen($key_phrase) > 0)
				return false;
			//Verify that a valid key_property for index was provided
			$query = $this->_mysqli->query("SHOW COLUMNS FROM `$database`");
			$validProperty = false;
			while($ret = $query->fetch_assoc()){
				if($key_property === $ret["Field"]){
					$validProperty = true;
					break;
				}
			}
			if(!$validProperty)
				return array(false, "Invalid IndexProperty");
			
			if(!$query = $this->_mysqli->query("SELECT * FROM `$database` WHERE `$key_property` LIKE '%$key_phrase%' ORDER BY `$key_property` ASC")){//Query based on q string, order w/inactive card results first
				$ComFailException = new UnexpectedValueException("[FATAL]Failed to initiate database connection! \n" . $this->mysqli->error(), $this->mysqli->errno());
				AppMysqli::reportConnectionFail($ComFailException);
				return array(false, "Database Communication Error.");
			}
			return $query;
		}
		
		/////////////////////////////////
		//	Account selection page (searchbox / autofill)
		//
		final public function viewAccountSelector(){
			self::initRender();
			Render::renderAccountSelector();
		}
		
		final public function viewAccountOpen(){
			self::initRender();
			
		}
	}
?>