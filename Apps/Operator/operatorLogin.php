<?php
/*
  Handles unauthenticated requests to login as operator
*/

require("./../../config.php");
require("./Operator_Interface.php");
require("./Operator_Data.php");
require(CONFIG::DOC_ROOT . "/Mysqli.php");

if(!strlen($_POST["username"]) > 0){
  header("tsastatus:400");
  exit(
    json_encode(array("message"=>"Please enter a username."))
  );
}
$username = $_POST["username"];

if(!ISSET($_POST["password"])){
  header("tsastatus:400");
  exit(
    json_encode(array(
      "username"=>$username,
      "message"=>"Please enter a password."))
  );
}
$password = $_POST["password"];

session_start();
Operator_Interface::doLogout();
$Operator_Interface = new Operator_Interface();
$Operator_Status = $Operator_Interface->getInitStatus();
if($Operator_Status === false){
  header("tsastatus:400");
  exit(
    json_encode(array(
      "username"=>$username,
      "message"=>"Invalid Credentials"))
  );
}

header("Location: " . $Operator_Status);
exit();
?>
