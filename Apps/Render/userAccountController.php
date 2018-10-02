<?php 

	if(!class_exists("CONFIG"))//Config
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	
	if(!class_exists("AppMysqli"))//Authenticates through mysqliApp
		require(CONFIG::DOC_ROOT . "/Apps/Mysqli/Mysqli.php");
	
	if(!class_exists("Render"))
		require(CONFIG::DOC_ROOT . "/Apps/Render/Render.php");

	$AppMysqli = new AppMysqli();
	$mysqli = $AppMysqli->initMysql();
	$Auth = $AppMysqli->getMysqlAuth();//Get Auth object used to authenticate
	
	if(!class_exists("User"))
		require(CONFIG::DOC_ROOT . "/Apps/User/User.php");
	
	//Op Session Handling
	require(CONFIG::DOC_ROOT . "/Apps/Authentication/AuthenticationSessionManager.php");
	
	
	//1:Open | 2:Info | 3:Close | 4:Start 'session' -- Defaults to View, Changes to Open if no UUID specified
	$action = (@$_GET["action"] > 0 && @$_GET["action"] < 5)? $_GET["action"] : 2;
	$uuid = $mysqli->real_escape_string(@$_GET["uuid"]);
	
	
	takeAction($action);
	
	//Exists souly for recusrion on $act == 4 && $act == 1
	function takeAction($act){
		global $uuid;
		if($act == 4){
			opInitAccountSession();
			takeAction(2);
			return true;
		}
		
		$User = new User($uuid);
		if($act == 1){
			if(!$uuid = $_POST["uuid"]){exit(json_encode(array("action"=>"displayError", "args"=>array("Missing UUID", 8000), "append"=>"none")));}
			if(!$name = $_POST["name"]){exit(json_encode(array("action"=>"displayError", "args"=>array("Missing Name", 8000), "append"=>"none")));}
			if(!$entry = $_POST["entryFee"]){exit(json_encode(array("action"=>"displayError", "args"=>array("Missing Entry Fee", 8000), "append"=>"none")));}
			if(!$size = $_POST["entryGroup"]){exit(json_encode(array("action"=>"displayError", "args"=>array("Missing Group Size", 8000), "append"=>"none")));}
			
			if($entry != "true")
				$size = 0;

			$OpenAccount = $User->openAccount($uuid,$name,$size);	
			if(!$OpenAccount[0])
				exit(json_encode(array("action"=>"displayError", "args"=>array($OpenAccount[1], 8000), "append"=>"none")));
			
			takeAction(2);//View Account Info
			
		}elseif($act == 2){//View account portal
			$ret = $User->viewAccountPortal();
//echo json_encode($ret);
			if(!$ret){
				echo json_encode(array("action"=>"displayError", "args"=>array("Error obtaining user data!", 8000), "append"=>"none"));
			}else{
				echo json_encode(array("action"=>"renderUserAccountPortal","args"=>$ret,"append"=>false));
			}	
		}elseif($act == 3){
			opInitAccountSession();
			$Render = new Render();
			exit(json_encode(array("action"=>"renderTransactionTableItems","args"=>$Render->renderTableItems(), "append"=>false)));
		}
	}
?>