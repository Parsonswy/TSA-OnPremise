<?php
session_start();
require("./../../config.php");
require(CONFIG::DOC_ROOT . "/Apps/Operator/Operator_Interface.php");
require(CONFIG::DOC_ROOT . "/Apps/Operator/Operator_Data.php");
require(CONFIG::DOC_ROOT . "/Apps/Accounts/Accounts.php");
require(CONFIG::DOC_ROOT . "/Apps/Accounts/User.php");
require(CONFIG::DOC_ROOT . "/Apps/Commerce/Transaction.php");
require(CONFIG::DOC_ROOT . "/Apps/Commerce/Store.php");
require(CONFIG::DOC_ROOT . "/Mysqli.php");
//
//  Check for client login before handling request
//
$Operator = new Operator_Interface();
$Accounts = $Operator->getAccountsInterface();

class Basket{
  private $mysqli;
  public function __construct(){
		$tsadb = new TSADB();
		$this->mysqli = $tsadb->gsql();
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
    $query = $this->mysqli->prepare("SELECT * FROM `baskets` WHERE `ID`=?");
    $query->bind_param('i', $i);
    $first = true;
    for($i=$min;$i <= $max; $i++){
   	  unset($name);unset($description);unset($user);unset($price);
      $query->execute();
      $query->bind_result($ID, $user, $name, $description, $null, $price, $null, $null);
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
      $query = $this->mysqli->query("SELECT `ID` FROM `baskets` WHERE `ID`=$id");//Check if basket already exists or is update to existing
      //exit(var_dump($query));
      if($query->num_rows == 1){
          $query = $this->mysqli->query("UPDATE `baskets` SET `name`='" . $content['NAME'] . "',
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

        $query = $this->mysqli->query("INSERT INTO `baskets` VALUES('','" . $content["USER"] . "',
        											'" . $content["NAME"] . "',
        											'" . $content["DESCR"] . "',
													'',
													'" . $content["PRICE"] . "',
        											'$claimed','')");//populate values*********************
      }

      if($content["PRICE"] > 0){
      	//Charge user account
				GLOBAL $Operator;
				$Operator->registerNewAccountsSesssion($content["USER"]);
				$Transaction = $Operator->getTransactionSession();
				$Transaction->addLineItem(11,$id);
				$transID = $Transaction->getTransSession();
				$Transaction->chargeToAccount();

				//Add transaction ID to basket database for later nullification if basket changes winners for some reason
			  $this->mysqli->query("UPDATE `baskets` SET `transID`=$transID WHERE `WINNER`=" . $content["USER"]);
		          return true;
	      }
	}

  //Check if basket is being reasigned to different user
  //Change balance / transactions if needed (Insert -cost charge to transactions)
  //
  //Return error if occured
  private function isReasign($b, $c){
  	$uuid = $this->mysqli->real_escape_string($c["USER"]);
		$query = $this->mysqli->query("SELECT `WINNER`,`price`,`transID`,`name` FROM `baskets` WHERE `price`>0 && `ID`=" . $b);
    if($query->num_rows){
    	//exit("reasign" . var_dump($query));
      $rows = $query->fetch_assoc();
 	  if($rows["WINNER"] != $c["USER"]){// || $rows["price"] != $c["PRICE"]
				$Transaction = $Operator->getTransactionSession();
				$status = $Transaction->revokeTransaction($rows["transID"]);//Works independently of rest of object. No Accoutn Daemon change

		      if($status){
		        return true;
		      }else{	return "Unable to reasign basket " . $b . " to user " . $rows["WINNER"] . ". Progress: " . $status;	}
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
    $query = $this->mysqli->query("DELETE FROM `baskets` WHERE `ID`=$id");
   	return true;
  }
}
?>
