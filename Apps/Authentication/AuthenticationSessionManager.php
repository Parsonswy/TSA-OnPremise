<?php 
//UserAccountController
//Alert system that operator now working new user
//Mostly just update SQL / Cookie
function opInitAccountSession(){
	global $mysqli, $Auth, $uuid;
	$User = new User($mysqli->escape_string($uuid), true);//Check that UUID is valid
	if(!$User){//Redirect to open account of user not found
		header("Location: " . CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountController.php?action=1&uuid=" . $_GET["uuid"]);
		exit(json_encode(array("action"=>"compiledDisplayError", "args"=>array("message"=>"Error. Account not found.","time"=>8000))));
	}

	if(!$Auth->updateOpSession("Curr_userID", $mysqli->real_escape_string($uuid)))//Update OP data in database
		exit(json_encode(array("action"=>"compiledDisplayError", "args"=>array("message"=>"Database communication error! Issue has been reported. Recomend restarting this transaction.","time"=>8000))));

		setcookie("CURRUD",$uuid,time()+86400,"/","172.16.0.254",false, false);//Update cookie
		return true;//Transaction handoff
}
?>
