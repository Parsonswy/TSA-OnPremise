<?php 

//Data retreived and mostly formatted by User->viewAccountPortal();
class ElemUserAccountPortal{
	private $_userTransActivity;
	
	private $_userBasketActivity;

	private $_userProfileInfo;
	private $_userName;
	
	private $_gendOutput;
	
	public function __construct($transAct, $basketAct, $profileInfo){
		$this->setTransActivity($transAct);
		$this->setBasketActivity($basketAct);
		$this->setProfileInfo($profileInfo);
	}
	
	public function setTransActivity($transAct){
		$this->_userTransActivity = $transAct;
	}
	
	public function setBasketActivity($basketAct){
		$this->_userBasketActivity = $basketAct;
//var_dump($basketAct);
	}
	
	public function setProfileInfo($profileInfo){
		$this->_userProfileInfo = $profileInfo;
		$this->_userName = @$this->_userProfileInfo["name"];//Supress undefined error if clearing array with null pass
//var_dump($profileInfo);
	}
	
	//Gen JSON output conf
	public function genOutput(){
		$this->_gendOutput = array();
		$this->_gendOutput["transes"] = $this->_userTransActivity;
		$this->_gendOutput["baskets"] = $this->_userBasketActivity;
		$this->_gendOutput["user"] =  $this->genUserInfo();
		
		return $this->_gendOutput;
	}
	
	//Genereate right side user panel with
	//balance, username, userparty, transaction total
	//Uses whatever is in _userProfileInfo array
	private function genUserInfo(){
		if($this->_userProfileInfo == null)
			return "Error";
		
		$pd = array("userBalance"=>$this->_userProfileInfo["userBalance"],
					"userName"=>$this->_userProfileInfo["userName"],
					//"userParty"=>$this->_userProfileInfo["userParty"],
					"transes"=>$this->_userProfileInfo["userTranses"]
		);
		
		return $pd;
	}
}

/* Structure for data arrays
 * array(){
 * 	array(){			Transaction Activity Board
 * 		"entryID"
 * 		"entryDesc"
 * 		"entryCost"
 * 	}
 * 	...
 * 	array(){			Basket Activity Panel
 *		"basketID"
 *		"basketDesc"
 *		"basketPrice"
 * 	}
 * ...
 * 	array(){			User account info panel
 * 		"userBalance"
 * 		"userName"
 * 		"userParty"=>
 * 			array(){
 * 				"names"
 * 				"james"
 * 			}
 * 		"userTranses"
 * 	}
 * }
 * 
 */
//TransID, TransDescription, TransCost
?>