<?php
$action = $_GET["action"];
if(!ISSET($action))
  exit("NULL");

session_start();
require("./config.php");
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
//
//  Load navigation and respond w/ JSON CFG. Used for initial login check aswell
//
switch($action){
  case 2000:
    vis_loadNavigation();
  break;case 2001:
    vis_loadHome();
  break;case 2002:
    //intercepted by Operator_Interface and handled.
  break;case 2003:
    vis_loadAccountCreate();
  break;case 2003.1:
    //intercepted by Operator_Interface and handled.
    vis_loadAccountSummary();
  break;case 2004:
    vis_loadAccountSummary();
  break;case 2004.1:
    act_getAccountStatus();
  break;case 2005:
    vis_loadStorefront();
  break;case 2101://Add/remove line item
    act_updateTransSession();
  break;case 2102:
    act_checkOutSTG1();
  break;case 2103:
    act_checkOutSTG2();
  break; case 2006:
    act_cashoutSTG1();
  break; case 2006.1:
    act_cashoutSTG2();
  default:
    header("tsastatus: 500");
    exit(json_encode(array("type"=>1, "message"=>"Requested not recognized", "time"=>8000)));
  break;
}

//
//  Get JSON of allowed tabs from Operator permissions to display to user
//
function vis_loadNavigation(){
  global $Operator;
  $navigationCFG = $Operator->loadNavigation();
  header("tsastatus: 2000");
  exit(json_encode($navigationCFG));
}

function vis_loadHome(){
  header("tsastatus:1004");
  exit("Page not found.");
}

function vis_loadAccountSelector(){
  //header("tsastatus: 2002");
  header("tsastatus: 500");
  exit(json_encode(array("type"=>0, "message"=>"Account Selector", "time"=>8000)));
}

//  $_p[data] is only a uuid
function act_qrGateway(){
  if(!isset($_POST["data"])){
    header("tsastatus: 500");
    exit(json_encode(array("type"=>1, "message"=>"Request Not Understood", "time"=>8000)));
  }
  GLOBAL $Accounts, $Operator;

  switch(Accounts::accountExists($_POST["data"])){
    case -1:
      //not found, searches directory and sends back full page load trigger w/ results
      vis_loadAccountCreate();//load account creator
    break; case 0:
      //account disabled, send popup w/ reason
    break; case 1:
      //account locked, send popup w/ reason
    break; case 2:
      //enabled, check for authentication methods, present
      $Operator->selectAccount($_POST["data"]);
      vis_loadAccountSummary($Accounts->getUser()->getUUID());
    break; case 3:
      //account cashed out
    break;default:
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Request Not Understood.", "time"=>8000)));
    break;
  }
}

//Send parameters to load account creation page
function vis_loadAccountCreate(){
  $cfg = array(
    "requirePin"=>CONFIG::ACT_REQUIRE_PIN,
    "requirePhotoID"=>CONFIG::ACT_PHOTO_ID,
    "uuid"=>@$_POST["data"]
  );
  header("tsastatus:2003");
  exit(json_encode($cfg));
}

//SESSION.uuid if account is being worked. $_POST if something else (qrGateway)
//Empty will load the account selector
function vis_loadAccountSummary(){
  GLOBAL $Accounts;

  $retData = $Accounts->loadAccountSummary();
  header("tsastatus:2004");
  exit(json_encode($retData));
}

function act_getAccountStatus(){
  header("tsastatus:2004.1");
  return Accounts::accountExists($_POST["uuid"]);
}

//form data POSTed
function act_createUserAccount(){
  $User = new User(
    array(
      "uuid"=>intval(htmlentities($_POST["uuid"])),
      "name"=>htmlentities($_POST["name"]),
      "notes"=>htmlentities($_POST["notes"])
    )
  );
  $Accounts = new Accounts($User);
  //createUserAccount will update Operator with new Account Interface / User
  $account_status = $Accounts->createUserAaccount($_POST["entrance_fee"], @$_POST["pin"], @$_POST["profileIDIMG"]);//exit()s on error
  vis_loadAccountSummary($User->getUUID());
}

function vis_loadStorefront(){
  $Store = new Store();
  $Store->loadStorefront();
}

//Add line item to current transaction session
function act_updateTransSession(){
  GLOBAL $Operator;
  $sku = $_POST["item"];//should be sku
  $value = $_POST["value"];//quanity, itemid, || price depnding on charge type
  $Transaction = $Operator->getTransactionSession();

  try{
    $LineItem = $Transaction->hasOfSKU($sku);//returns lineitem obj
  }catch(Error $e){
    header("tsastatus: 500");
    exit(json_encode(array("type"=>1, "message"=>"No User Account Selected to Charge.", "time"=>8000)));
  }

  $itemID = $Transaction->skuToItemID($sku, $value);
  if(!$itemID){
    header("tsastatus: 500");
    exit(json_encode(array("type"=>1, "message"=>"Unable to Process Item " . $sku . ".", "time"=>8000)));
  }

  //No line item on transaction, create with value
  if(!$LineItem){
    $Transaction->addLineItem($itemID, $value);
    $LineItem = $Transaction->hasOfSKU($sku);
    $costDelta = $LineItem->getPrice();
  }else{//exists as line item, update with
	if(intval($value) !== -1){
    $initCost = $LineItem->getPrice();
		$LineItem->update($itemID, $value);
    $costDelta = $LineItem->getPrice() - $initCost;
	}else{
    $costDelta = $LineItem->getPrice();
    $costDelta = -$costDelta;
		$Transaction->removeLineItem($LineItem->getLineItemId());
	}
  }
  header("tsastatus:2101.1");
  exit(json_encode(array("total"=>$costDelta)));
}

//sends back "recipt" (2103 STG2)
function act_checkOutSTG1(){
  Global $Operator;
  $Transaction = $Operator->getTransactionSession();
  $jString = $Transaction->generateReceipt();
  header("tsastatus:2103");
  exit($jString);
}

//confirm ('sign') recipt. Charge transaction
function act_checkOutSTG2(){
  Global $Operator;
  $Transaction = $Operator->getTransactionSession();
  $Transaction->chargeToAccount();
  vis_loadAccountSummary($Operator->getAccountsInterface()->getUser()->getUUID());
}

function act_cashoutSTG1(){
  GLOBAL $Accounts;
  $retData = $Accounts->loadAccountSummary();
  header("tsastatus: 2006");
  exit(json_encode($retData));
}

function act_cashOutSTG2(){
  GLOBAL $Operator;
  $Operator->getTransactionSession()->cashout();
  vis_loadAccountSummary($Operator->getAccountsInterface()->getUser()->getUUID());
}

function dieOfError($code, $message){
  header("tsastatus:500");
  exit(json_encode(array("type"=>1, "message"=>$message, "time"=>8000, "code"=>$code)));
}
?>
