<?php
$action = $_GET["action"];
if(!ISSET($action))
  exit("NULL");

session_start();
require("./config.php");
require(CONFIG::DOC_ROOT . "/Apps/Operator/Operator.php");
require(CONFIG::DOC_ROOT . "/Apps/Accounts/Accounts.php");
require(CONFIG::DOC_ROOT . "/Apps/Accounts/User.php");
require(CONFIG::DOC_ROOT . "/Apps/Commerce/Transaction.php");
require(CONFIG::DOC_ROOT . "/Apps/Manager/Permissions.php");
require(CONFIG::DOC_ROOT . "/Mysqli.php");

//Authenticate - $Operator used by other objects as GLOBAL variable in some requests to get operator data
$Operator = new Operator();

//Check for management viewing permission
if(!$Operator->checkPermission("Manage_canView")){
  header("adminstatus: 500");
  exit(json_encode(array("type"=>1, "message"=>"Access Denied.", "time"=>8000)));
}
switch($action){
  case 2000:
    vis_loadNavigation();
  break; case 2001:
    vis_loadHome();
  break;case 4100:
    vis_loadPermissionClassList();
  break;case 4101:
    vis_loadPermissionClassEditor($mysqli->real_escape_string(@$_GET["perm_class"]));//id
  break; case 4102:
    act_updatePermissionsClass();
  break; case 4103:
    //create new permissions group
  break; case 4104:
    //delete permissions group
  break; case 4110:
    vis_loadOperatorEditor();
  break; case 4111:
    act_updateOperatorSettings();
  break; case 4112:
    //Create new operator
  break; case 4113:
    //delete operator
  break; default:
    header("adminstatus: 500");
    exit(json_encode(array("type"=>1, "message"=>"Requested not recognized", "time"=>8000)));
  break;
}


function vis_loadHome(){
  header("adminstatus:2001");
  exit();
}

//
//  Load array of navigation tile cfgs for pages that user can access
//
function vis_loadNavigation(){
  $Permissions = new Permissions();
  $navCFG = $Permissions->loadNavigation();
  header("adminstatus:2000");
  exit(json_encode($navCFG));
}

//
//  Load list of permissions classes for dropdown
//
function vis_loadPermissionClassList(){
  GLOBAL $Operator;
  if(!$Operator->checkPermission("Manage_canPermission")){//Check for permission management node
    header("adminstatus: 500");
    exit(json_encode(array("type"=>"1","message"=>"Access Denied.", "time"=>8000)));
  }

  $Permissions = new Permissions();
  $permClassList = $Permissions->loadPermissionsClassList();
  header("adminstatus: 4100");
  exit(json_encode($permClassList));
}

//
//  Permissions Editor
//
function vis_loadPermissionClassEditor($class){
  GLOBAL $Operator;
  if(!$Operator->checkPermission("Manage_canPermission")){//Check for permission management node
    header("adminstatus: 500");
    exit(json_encode(array("type"=>"1","message"=>"Access Denied.", "time"=>8000)));
  }

  $Permissions = new Permissions();
  $permEditorCFG = $Permissions->loadPermissionsClassDisplay($class);
  header("adminstatus:4101");
  exit(json_encode($permEditorCFG));
}

//
//  Update permission class (class_id) with JSON $cfg
//
function act_updatePermissionsClass(){
  if(intval($_POST["perm_groups"]) < 1){
    header("adminstatus: 500");
    exit(json_encode(array("type"=>"1", "message"=>"Invalid configuration received. Please try again.", "time"=>5000)));
  }

  $Permissions = new Permissions();
  $permUpdateStatus = $Permissions->modifyPermissionClass();//Works with $_POST directly
  header("adminstatus:4102");
  exit(json_encode($permUpdateStatus));
}

/*********************************************
  Operator Management Handling
*********************************************/
//get data to display editor
function vis_loadOperatorEditor(){
  $Permissions = new Permissions();
  $data = $Permissions->loadOperatorEditor();
  header("adminstatus: 4110");
  exit(json_encode($data));
}

//update database based on changes made
function act_updateOperatorSettings(){
  if($_POST["operators"] < 1 && $_POST["operators"] <= CONFIG::OPS_MAX_REGISTERED){
    header("adminstatus: 500");
    exit(json_encode(array("type"=>"1", "message"=>"Invalid configuration received. Please try again.", "time"=>5000)));
  }

  $Permissions = new Permissions();
  $opUpdateStatus = $Permissions->modyOperatorSettings();//works with $_POST directly
  header("adminstatus:4111");
  exit(json_encode($opUpdateStatus));
}

function act_registerNewOperator(){
  //CONFIG::OPS_MAX_REGISTERED impliment
}
?>
