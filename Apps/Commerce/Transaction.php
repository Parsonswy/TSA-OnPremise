<?php
require(CONFIG::DOC_ROOT . "/Apps/Commerce/TransactionLineItem.php");
class Transaction{
  private $_session_id;
    public function getTransSession(){return $this->_session_id;}
  //Objects
  private $_operator;
  private $_account;
  private $_line_items;//array
  private $_mysqli;

  private $_line_item_id_string;
    public function getLineItemIdString(){return $this->_line_item_id_string;}

  public function __construct($id=-1, $operator){
    GLOBAL $mysqli;
    $this->_mysqli = $mysqli;
    $this->_operator = $operator;
    if($id == -1){
      $id = $this->createTransactionSession();
    }else{
      $this->_session_id = $id;
      $this->instantiate();
    }
  }

  public function setOperatorInterface($Operator){$this->_operator = $Operator;}

  private function createTransactionSession(){
    $query = $this->_mysqli->prepare("INSERT INTO `trans_sessions` VALUES('',?,?,'')");
    $uuid = $this->_operator->getAccountsInterface()->getUser()->getUUID();
    $opID = $this->_operator->getOperator()->getOpID();
    $query->bind_param("ii", $opID, $uuid);
    $query->execute();
    $query->store_result();
    if(!$query->affected_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Database Error!", "time"=>8000)));
    }
    $query->close();
    $this->_session_id = $this->_mysqli->insert_id;
  }

  //Get Transaction data
  private function instantiate(){
    $query = $this->_mysqli->prepare("SELECT `line_items` FROM `trans_sessions` WHERE `id`=?");
    $query->bind_param("i",$this->_session_id);
    $query->execute();
    $query->store_result();
    $query->bind_result($this->_line_item_id_string);
    $query->fetch();

    if(!$query->num_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Transaction Database Error!", "time"=>8000)));
    }

    $query->free_result();
    $query->close();

    $line_item_ids = explode(",", $this->_line_item_id_string);
    if($line_item_ids[0] == "")
      return true;

    $this->_line_items = array();
    foreach($line_item_ids as $item_id){
      if($item_id == "")
        continue;

      $lineItem = new LineItem($item_id,$this->_session_id);
      $this->_line_items[] = $lineItem;
    }
  }

  public function getLineItemString(){

  }

  public function addLineItem($item_id, $value){
    $lineItem = new LineItem(null, $this->_session_id, $item_id, $value);
    $lineItem->insert();
    $this->_line_items[] = $lineItem;
    $lineItemId = $lineItem->getLineItemId();
    $this->_line_item_id_string .= $lineItemId;
    $query = $this->_mysqli->prepare("UPDATE `trans_sessions` SET `line_items`=CONCAT(line_items,?,',') WHERE `id`=?");
    $query->bind_param("si",$lineItemId, $this->_session_id);
    $query->execute();
    $query->store_result();
    if(!$this->_mysqli->affected_rows === 1){
      $lineItem->drop();
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"[TALI]Database Error!", "time"=>8000)));
    }
    //header("tsastatus:2101");
    //exit(json_encode(array("price")));
  }

  public function getSKUofItem($itemID){
    $query = $this->_mysqli->prepare("SELECT `SKU` FROM `trans_purchasable` WHERE `id`=?");
    $query->bind_param("i", $itemID);
    $query->execute();
    $query->store_result();

    if(!$query->num_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Transaction Database Error!", "time"=>8000)));
    }

    $query->bind_result($SKU);
    $query->fetch();
    $query->close();

    return $SKU;
  }

  //check if transaction has some # of $SKU already in cart
  //Returns the LineItem (OBJ) under that SKU if yes
  public function hasOfSKU($SKU){
    $iCount = count($this->_line_items);
    for($i=0;$i<$iCount;$i++){
      if($this->_line_items[$i]->getSKU() == $SKU){
        return $this->_line_items[$i];
      }
    }
    return false;
  }

  public function skuToItemID($sku, $value){
    $query = $this->_mysqli->prepare("SELECT `id`,`chargeType` FROM `trans_purchasable` WHERE `SKU`=? LIMIT 1");
    $query->bind_param("i",$sku);
    $query->execute();
    $query->store_result();
    $query->bind_result($id, $chargeType);
    $query->fetch();
    if($query->num_rows != 1){
      return false;
    }

    switch($chargeType){
      case "custom":
        return $id;
      break;case "leveled":
        return $value;//client sends product id as value
      break;case "quantity":
        return $id;
      break;default:
        return false;
      break;
    }
  }

  //TODO:Super inefficent process
  public function removeLineItem($itemId){
    $this->_line_item_id_string = "";
    foreach($this->_line_items as $item){
      if($item->getLineItemId() == $itemId){
        $item->drop();
      }else{
        $this->_line_item_id_string .= $item->getLineItemId() . ",";
      }
    }

	$query = $this->_mysqli->prepare("UPDATE `trans_sessions` SET `line_items`=? WHERE `id`=?");
	$query->bind_param("si", $this->_line_item_id_string, $this->_session_id);
	$query->execute();
	$query->store_result();

	if($this->_mysqli->affected_rows !== 1){
		header("tsastatus: 500");
		exit(json_encode(array("type"=>1, "message"=>"[TRLI]Transaction corrupted. Please contact administrator!", "time"=>8000)));
	}
	$query->close();
  }

  //Get data for recipt to display to client for confirmation
  public function generateReceipt(){
    $ic = count($this->_line_items);
    $retString = "[";
    for($i=0;$i<$ic;$i++){
      $retString .= $this->_line_items[$i]->genJString() . ",";
    }
    $retString = substr($retString, 0, strlen($retString)-1) . "]";
    return $retString;
  }

  //Charges / closes out transaction
  public function chargeToAccount(){
    $uuid = $this->_operator->getAccountsInterface()->getUser()->getUUID();
    $ic = count($this->_line_items);
    $total = 0.00;
    for($i=0;$i<$ic;$i++){
      $total += $this->_line_items[$i]->getPrice();
    }

    $query = $this->_mysqli->prepare("UPDATE `client_info` SET `balance`=`balance`+?, `transaction_count`=`transaction_count`+1 WHERE `uuid`=?");
    $query->bind_param("di", $total, $uuid);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Unable to charge account! Transaction NOT completed. " . $this->_mysqli->affected_rows, "time"=>8000)));
    }
        $query->close();

    $op_id = $this->_operator->getOperator()->getopID();$zero = 0; $emptyString = "";
    $query = $this->_mysqli->prepare("INSERT INTO `trans_completed` VALUES(?,?,?,?,?,?,?)");
    $query->bind_param("iiisdis", $this->_session_id, $op_id, $uuid, $this->_line_item_id_string, $total, $zero, $emptyString);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Unable to close transaction, but Account WAS charged succesfully.", "time"=>8000)));
    }
    $query->close();
//TODO: report discrepency

    $query = $this->_mysqli->prepare("DELETE FROM `trans_sessions` WHERE `id`=? LIMIT 1");
    $query->bind_param("i", $this->_session_id);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Unable to close transaction, but Account WAS charged succesfully.", "time"=>8000)));
    }
    $query->close();
//TODO: report discrepency
    $this->_operator->registerNewTransactionSession();
  }

  public function isTransactionEmpty(){
    if(count($this->_line_items) == 0){
      return true;
    }else{
      return false;
    }
  }

  //Copy transaction to empty continuity table
  public function markTransactionEmpty(){
    $query = $this->_mysqli->prepare("INSERT INTO `trans_empty` SELECT * FROM `trans_sessions` WHERE `id`=?");
    $query->bind_param("i",$this->_session_id);
    $query->execute();
    $query->store_result();
    if(!$query->affected_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Database Error!", "time"=>8000)));
    }

    $query = $this->_mysqli->prepare("DELETE FROM `trans_sessions` WHERE `id`=?");
    $query->bind_param("i", $this->_session_id);
    $query->execute();
    $query->store_result();
    if(!$query->affected_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Database Error!", "time"=>8000)));
    }

    $query->close();
    return true;
  }

  public function cashout(){
    GLOBAL $Operator;
    $uuid = $Operator->getAccountsInterface()->getUser()->getUUID();
    $query = $this->_mysqli->prepare("SELECT `balance` FROM `client_info` WHERE `uuid`=?");
    $query->bind_param("i",$uuid);
    $query->execute();
    $query->store_result();
    $query->bind_result($balance);
    $query->fetch();

    if($query->num_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Error Compiling Account Information", "time"=>8000)));
    }
    $query->close();

    //What TODO: if there are other items on transaction. Can there be 2 transactions w/ same uuid?
    //Make new, clean transaction in TSA.php to handle cashout?
    $this->addLineItem(2, $balance*-1);//cashout
    $this->chargeToAccount();

    $three = 3;
    $query = $this->_mysqli->prepare("UPDATE `client_info` SET `account_status`=?, account_closed=? WHERE `uuid`=? LIMIT 1");
    $query->bind_param("iii", $three, $this->_session_id, $uuid);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Error Proccessing Account. Please confirm a " . $balance * -1 . " charge has been applied to the account.", "time"=>8000)));
    }
    $query->close();
  }

  //ONLY SUPPORTED FOR BASKETS RIGHT NOW
  //REVOKES TRANSACTIONS-NOT LINE ITEMS. BAKSET TRANSACTIONS ARE ONLY ONES GAURENTEEED TO HAVE 1 LINE ITEM
  public function revokeTransaction($id){
    $query = $this->_mysqli->prepare("SELECT `user_id`,`line_items`,`trans_total`,`revoked` FROM `trans_completed` WHERE `id`=? LIMIT 1");
    $query->bind_param("i",$id);
    $query->execute();
    $query->store_result();
    if($query->num_rows != 1)
      return 100;
    $query->bind_result($userid, $lineItem, $total, $revoked);
    $query->fetch();
    $query->close();

    //already revoked
    if($revoked == 1)
      return 200;

    $query = $this->_mysqli->prepare("UPDATE `trans_line_items` SET `price`=`price`*-1 WHERE `id`=?");
    $query->bind_param("i", $lineItem);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1)
      return 300;
    $query->close();

    $query = $this->_mysqli->prepare("UPDATE `client_info` SET `balance`=`balance`-? WHERE `uuid`=?");
    $query->bind_param("di", $total, $userid);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1){
      return 400;
    }
    $query->close();

    $one = 1;$reason = "Silent Auction Item Transfered.";
    $query = $this->_mysqli->prepare("UPDATE `trans_completed` SET `revoked`=?,`revoked_reason`=? WHERE `id`=?");
    $query->bind_param("isi",$one, $reason, $id);
    $query->execute();
    $query->store_result();
    if($this->_mysqli->affected_rows != 1)
      return 500;
    $query->close();

    return true;
  }
}
?>
