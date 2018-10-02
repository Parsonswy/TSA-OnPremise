<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./CSS/Main.css"/>
		<link rel="stylesheet" type="text/css" href="./CSS/Scrollbar.css"/>
		<link rel="stylesheet" type="text/css" href="./Apps/Render/CSS/UserAccountPortal.css"/>
		<link rel="stylesheet" type="text/css" href="./Apps/Render/CSS/UserAccountSelector.css"/>
		<link rel="stylesheet" type="text/css" href="./Apps/Render/CSS/TransactionTableDisplay.css"/>
		<title>TSA Auction Services - Home</title>
		<style type="text/css"></style>
	</head>
	<body onload="initPage();">
<?php
	//error_reporting(0);
	session_start();
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");

	//TODO:lockout
	if(CONFIG::SYS_LOCKOUT)//TODO:: inteligent redirects back (restore to exact point in transaction)
		header("Location:" . CONFIG::DOC_ROOT_WEB . "/Apps/Config/Lockout.php?prevURL=" . CONFIG::DOC_ROOT_WEB . "/TSA_AUCTION.php");
	
	//Register BYPASSED auth session
	require(CONFIG::DOC_ROOT . "/Apps/Authentication/OPLogin.php");

	if(!class_exists("User"))
		require (CONFIG::DOC_ROOT . "/Apps/User/User.php");

	//Navigation display configuration
	$domElements = array(false, true, false, false, true, true, true, true);
	$User = new User();
	if(!$User)
		echo "<script type='text/javascript'>displayError('Unable to access user portal',8000)</script>";
	
	if($uuid = $_GET["UUID"]){
		if($User->accountISActive($uuid)){
			$userName = $User->UUIDstoNames(array($uuid));//Get username avalible to JS
			if(count($userName) == 1)
				$userName = $userName[0];
			$uuid = $_GET["UUID"];//UUID avalible to JS
			
			//New Transaction
			$firstURL = CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountController.php?action=3&uuid=" . $uuid;
			$domElements = array(true, true, true, true, true, true, true, true);
		}else{
			$firstURL = CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountSelector.php?UUID=" . $uuid;
		}
	}else{
		$firstURL = CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountSelector.php";
	}
	echo "<input type='hidden' id='firstURL' value='" . @$firstURL . "'/>
			<input type='hidden' id='userName' value='" . @$userName . "'/>
			<input type='hidden' id='uuid' value='" . @$uuid . "'/>";
?>
		<div id="navBar">
		</div>

		<div id="contentBox">

		</div>

		<div id="contentProc">
			<img id="loadImage" src="./Static/IMG/loading.gif"/>
			<font style="position:relative;left:120px;top:55px;">Processing Your Request....</font>
		</div>

		<div id="errorDisp">
			<p id="errorMSG" style="float:left;"></p>
			<img style='float:right;margin-top:1em;' height='42px' width='48px' src='./Static/IMG/warning.png'/>
		</div>
	<script type="text/javascript" src="./Static/Greensock_src/minified/TweenLite.min.js"></script>
	<script type="text/javascript" src="./Static/Greensock_src/minified/plugins/CSSPlugin.min.js"></script>
	<script type="text/javascript" src="./Static/JS/PageLoader.js"></script>
	<script type="text/javascript" src="./Apps/Render/Render.js"></script>
	<script type="text/javascript" src="./Apps/Render/JS/UserAccountPortal.js"></script>
	<script type="text/javascript" src="./Apps/Render/JS/TransactionTableDisplay.js"></script>
	<script type="text/javascript" src="./Apps/Render/JS/UserAccountSelector.js"></script>
	<script type="text/javascript" src="./Static/JS/Main.js"></script>
	</body>
</html>
