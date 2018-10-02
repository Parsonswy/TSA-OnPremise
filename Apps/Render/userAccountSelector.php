<?php
	/*
		Handles display/data proccessing of uuid/name searches for "Select Account" page
		Gets data from User::autoFillIndex
		OR
		Triggers static page loading if no query string supplied
	*/
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	
	function initMysql(){
		$AppMysqli = new AppMysqli();
		if(!$MysqliLink = $AppMysqli->initMysql()){
			echo "<script type='text/javascript'>
				displayError('Error Contacting Database!',8000);
			</script>";
			exit();
		}
		return $MysqliLink;
	}
	
	function dynamicOutput($query, $q){//Reference to query from User:autoFillIndex & needle
		$retString = array("action"=>"renderUserAccountSelectorResults","args"=>array(),"append"=>"true");
		while($rows = @$query->fetch_assoc()){
			$primary_search_ident = $rows["uuid"];
			$secondary_ident = $rows["name"];
			
			if(!is_numeric($q) && strlen($q) > 0){
				$primary_search_ident = $rows["name"];
				$secondary_ident = $rows["uuid"];
			}
			
			//Define certain parameters for display based on type of account dealing with.
			if($rows["active"] == 1){
				$btnText = "Select";
				$btnColor = "#47d147";
				$btnLink = "pageLoader('" . CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountController.php?action=4&uuid=" . $rows["uuid"] . "');";
			}elseif($rows["active"] == 2){
				$btnText = "Closed";
				$btnColor = "maroon";
				$btnLink = "";
			}else{
				$btnText = "Create";
				$btnColor = "#428CFF";
				$btnLink = "render_aO_QueryFields(" . $rows["uuid"] . ");";
			}
			
			$pushData = array(
					"primary"=>$primary_search_ident,
					"secondary"=>$secondary_ident,
					"bal"=>(isset($rows["balance"]))?$rows["balance"] : "-",
					"btnText"=>$btnText,
					"btnColor"=>$btnColor,
					"btnLink"=>$btnLink
			);
			array_push($retString["args"], $pushData);
		}
		return $retString;
	}
	
	function userAccountSelectorStaticDisplay(){
		//Static display for Render
		return array("action"=>"renderUserAccountSelector","args"=>"","append"=>false);
	}
	
	if(@isset($_GET["q"])){//Query String Present
		if(!class_exists("AppMysqli"))
			require(CONFIG::DOC_ROOT . "/Apps/Mysqli/Mysqli.php");
	
			$Mysqli = initMysql();
	
			$q = $Mysqli->real_escape_string($_GET["q"]);
	
			if(!class_exists("User"))
				require(CONFIG::DOC_ROOT . "/Apps/User/User.php");
	
				if(!$AppUser = new User()){
					echo "<script type='text/javascript'>
				displayError('Error Obtaining User Information Access!',8000);
				</script>";
					exit();
				}
	
				if(!is_numeric($q)){
					$ret = $AppUser->autoFillIndex(false, "name", $q);
				}else{
					$ret = $AppUser->autoFillIndex(false, "uuid", $q);
				}
				echo json_encode(dynamicOutput($ret, $q));
	}else{
		echo json_encode(userAccountSelectorStaticDisplay());
	}
?>