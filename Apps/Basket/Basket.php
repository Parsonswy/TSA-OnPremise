<?php
if(!class_exists("CONFIG"))
	require("/var/www/html/Rebuild/Apps/Config/Config.php");
if(!class_exists("User"))
	require(CONFIG::DOC_ROOT . "/Apps/User/User.php");
if(!class_exists("Transaction"))
	require(CONFIG::DOC_ROOT . "/Apps/Commerce/Transaction.php");
class Basket{
  private $mysqli;
  public function __construct(){
  	if(CONFIG::SYS_LOCKOUT)//Check for lockout
  		exit("SIG_TERM_LOCKOUT");//TODO:Standardize
  		if(CONFIG::OPS_ENABLED){
  			if(!class_exists("Authentication")){
  				require(CONFIG::DOC_ROOT . "/Apps/Authentication/Authentication.php");
  			}
  			$this->_auth = new Authentication();
  			if(!$this->_auth->validateOperatorSession()){//Verify operator session exists
  				exit(json_encode(Authentication::getAuthPrompt()));//Prompt for login if not
  			}
  			$this->_isAuthenticated = true;
  		}else{
  			$this->_isAuthenticted = true;
  		}
  	
  	$AppMysqli = new AppMysqli();
  	if(!$this->mysqli = $AppMysqli->initMysql()){
      exit("[" . $this->mysqli->connect_errno . "]" . $this->mysqli->connect_error . 
           "<br /> Database error!");
  	}
  }
  
  public function __destroy(){
    $this->mysqli->close();
  }


////////Page Generation//////////////////////////////////////////
  //Populate / generate editor with $min through $max (Invlusive)
  //
  //Returns html source for direct output to screen
  private $_html = "<form action='./BasketManager.php?UP=true' method='POST'><table>
  		<tr>
  			<td>X</td><td></td><td>ID</td><td>Basket Name</td><td>Basket Description</td><td>User(UUID)</td><td>Price</td>
  		</tr>";
  public function populateEditor($min, $max){
    $img = "./path/to/x.imageFile";
    $query = $this->mysqli->prepare("SELECT * FROM `app_comInventory` WHERE `ID`=?");
    $query->bind_param('i', $i);
    $first = true;
    for($i=$min;$i <= $max; $i++){
   	  unset($name);unset($description);unset($user);unset($price);
      $query->execute();
      $query->bind_result($ID, $null, $user, $name, $description, $null, $price, $null, $null, $null);
      $query->fetch();
      if($ID == 1 && $first){$ID = 1;}else{$ID=$i;}
      $this->_html .= "<tr>
      						<td><img src='" . $img . "'/><td>
                            <td><input type='number' value='" . $ID . "' width='10' disabled='disabled'/></td>
                            <td><input type='text' name='name_" . $i . "' value='". htmlspecialchars($name, ENT_QUOTES) ."'/></td>
                            <td><input type='text' name='desc_" . $i . "' value='" . htmlspecialchars($description, ENT_QUOTES) . "'/></td>
                            <td><input type='number' name='user_" . $i . "' value='" . htmlspecialchars($user, ENT_QUOTES) . "' /></td>
                            <td><input type='text' name='price_" . $i . "' value='" . htmlspecialchars($price, ENT_QUOTES) . "' /></td>
                    	</tr>";
    	$first = false;
    }
    $this->_html .= "<tr><td></td><td></td><td><input type='submit' value='Update'/></td></tr>
    						</table><input type='hidden' name='data-total' value='" . $i . "'/>
    						<input type='hidden' name='data-min' value='" . $min . "'/></form>";
    $query->close();
    return $this->_html;
  }


///////////////////////Form handling//////////////////////////
  //Get data submited by user through BaskManager.php
  //Updates values passed in array($content) to update for $id
  //
  //Return error if occured
	public function updateData($id, $content){
      $id = $this->mysqli->real_escape_string($id);
      
	  foreach($content as $key => $value){
	  	$content[$key] = $this->mysqli->real_escape_string($value);
	  }
      
      if(!$ret = $this->isReasign($id, $content)){
        return $ret; //Return error (if any)
      }//Check if basket winner is changing and run according calculations
      $claimed = $content["PRICE"] > 0?true : false;
      $query = $this->mysqli->query("SELECT `ID` FROM `app_comInventory` WHERE `ID`=$id");//Check if basket already exists or is update to existing
      //exit(var_dump($query));
      if($query->num_rows == 1){
          $query = $this->mysqli->query("UPDATE `app_comInventory` SET `name`='" . $content['NAME'] . "', 
                                  `description`='" . $content['DESCR'] . "', 
                                  `WINNER`='" . $content['USER'] . "',
                                  `price`='" . $content['PRICE'] . "',
                                  `claimed`='" . $claimed. "' WHERE `ID`='" . $id . "'");
          //exit("other" . var_dump($query) . var_dump($this->mysqli));
          if(!$query)
          	return "Query Error -UPDATE BASKET VALUES";
          if(!$this->mysqli->affected_rows)
            return "Unable to update basket values basket " . $id;
      }else{//Create basket is doesn't exist

        $query = $this->mysqli->query("INSERT INTO `app_comInventory` VALUES('','1','" . $content["USER"] . "',
        											'" . $content["NAME"] . "',
        											'" . $content["DESCR"] . "',
													'',
													'" . $content["PRICE"] . "',
        											'$claimed','1','')");//populate values*********************
      }
      
      if($content["PRICE"] > 0){
      	//Charge user account
  		  $Transaction = new Transaction($content["USER"]);//Was using COOKIE uuid to charge users incorrectly. Why chargeBasketItem has uuid field
  		  $Transaction->chargeBasketItem($content["USER"],$content["NAME"], $content["PRICE"]);
  			
		//Add transaction ID to basket database for later nullification if basket changes winners for some reason
		  $transID = $Transaction->getTransID();
		  $this->mysqli->query("UPDATE `app_comInventory` SET `transID`=$transID WHERE `WINNER`=" . $content["USER"]);
	          return true;
	      }
	}
  
  //Check if basket is being reasigned to different user
  //Change balance / transactions if needed (Insert -cost charge to transactions)
  //
  //Return error if occured
  private function isReasign($b, $c){
  	$uuid = $this->mysqli->real_escape_string($c["USER"]);
	$query = $this->mysqli->query("SELECT `WINNER`,`price`,`transID`,`name` FROM `app_comInventory` WHERE `price`>0 && `ID`=" . $b);
    if($query->num_rows){
    	//exit("reasign" . var_dump($query));
      $rows = $query->fetch_assoc();
 	  if($rows["WINNER"] != $c["USER"]){// || $rows["price"] != $c["PRICE"]
 	  		$Transaction = new Transaction($rows["WINNER"]);//Was using COOKIE uuid to charge users incorrectly. Why chargeBasketItem has uuid field
 	  		$Transaction->chargeBasketItem($rows["WINNER"], $rows["name"] . " [BASKET]Transfered to different user(" . $c["USER"] . ")",($c["PRICE"] * -1));//Subtract charge from old winner
 	  		
		      if($Transaction->getTransID()){
		        return true;
		      }else{	return "Unable to reasign basket " . $b . " to user " . $rows["WINNER"];	}
	 	  }
	  return true;
    }//No applicable basket found. Either doesn't exist or no charge applied to any account
    return true;
  }

  
/////////////////////////////////////Delete Basket/////////////////////////////////////
  //Delete basket $id
  //
  //Return error if occurs
  public function deleteBasket($id){
    $id = $this->mysqli->real_escape_string($id);
    $query = $this->mysqli->query("DELETE FROM `app_comInventory` WHERE `ID`=$id");
   	return true;
  }
}
?>