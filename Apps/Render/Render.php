<?php
/*
 * Render / print pages
 * $elem is standard local variable for printing in each function
 */
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	if(!class_exists("Authentication"))
		require(CONFIG::DOC_ROOT . "/Apps/Authentication/Authentication.php");
	//////////////////////////////
	//	Render page views
	//
	class Render{

		public function __construct(){
			if(CONFIG::SYS_LOCKOUT)//Check for lockout
				exit("SIG_TERM_LOCKOUT [" + CONFIG::SYS_LOCKOUT);

			if(CONFIG::OPS_ENABLED){
				$this->_auth = new Authentication();
				//if(!$this->_auth->validateOperatorSession()){//Verify operator session exists
					//exit(json_encode(Authentication::getAuthPrompt()));
				//}
			}
		}

		//Compile config for TableItems tile display
		//Transaction.php
		public function renderTableItems(){
			require(CONFIG::DOC_ROOT . "/Apps/Commerce/TableItems.php");//Get game configs
			$cuemList = array();
			$length = count(TableItems::GAME_LIST);
			for($i=0; $i<$length;$i++){
				if(!TableItems::GAME_LIST[$i]["ACTIVE"])
					continue;
				array_push($cuemList, TableItems::GAME_LIST[$i]);
			}
			
			return $cuemList;
		}

		///////////////////////////////////
		//	Genereate list of navigation buttons based on true/false $elem[]
		//	TSA_Auction.php -> JS onload(body)
		final public function renderGlobalLeftNav(array $elemParam){
			//Load list of elements
			require(CONFIG::DOC_ROOT . "/Apps/Render/elements_GlobalLeftNav.php");

			//Make sure provided param array has enough entires
			while(count($elemParam) <= count($elem)){
				array_push($elemParam, false);
			}
			
			$loopI = count($elem);
			$elemBuffer = array();
			for($i = 0; $i < $loopI; $i++){
				if($elemParam[$i])
					array_push($elemBuffer, $elem[$i]);
			}
			//Print to screen 1 at a time
			echo json_encode($elemBuffer);
		}

		/////////////////////////////////
		//	Genereate account selection page
		//	User.php ->viewAccountSelector();
		final public function renderAccountSelector(){
			require(CONFIG::DOC_ROOT . "/Apps/Render/userAccountSelector.php");
			echo userAccountSelectorStaticDisplay();//$userStaticDisplay set in userAccountSelector
		}
	}
?>
