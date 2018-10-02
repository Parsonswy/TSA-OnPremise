<?php
	if(!$_GET["action"])
		exit("Null");
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	if(!class_exists("Transaction"))		
		require(CONFIG::DOC_ROOT . "/Apps/Commerce/Transaction.php");
	if($_GET["action"] == 1){//Show table items
		require(CONFIG::DOC_ROOT . "/Apps/Render/Render.php");
		$Render = new Render();
		exit(json_encode(array("action"=>"renderTransactionTableItems","args"=>$Render->renderTableItems(), "append"=>false)));
	}elseif($_GET["action"] == 2){//Process transaction
		if(!$_POST["param"])
			exit(json_encode(array("action"=>"displayError", "args"=>array("Error, empty transaction!", 8000), "append"=>"none")));

		$Transaction = new Transaction();
		$compltededTransaction = $Transaction->newTransaction($_POST["param"]);//USE 0's as place holders if game is not charging. Seperate with ','
		if($compltededTransaction[0]){
			$TransData = $Transaction->getTransaction();
			exit(json_encode(array("action"=>"com_displayRecipt","args"=>$TransData,"append"=>true)));
		}else{
			exit(json_encode(array("action"=>"displayError", "args"=>array($completedTransaction[1], 8000), "append"=>true)));
		}
	}elseif($_GET["action"] == 3){//Cashout
		$Transaction = new Transaction($_COOKIE["CURRUD"]);
		$CashOut = $Transaction->cashOut();
		if($CashOut[0])
			$CashOut = $CashOut[1];
		exit(json_encode(array("action"=>"com_displayRecipt","args"=>$CashOut,"append"=>true)));
	}else{
		echo "null";
	}
?>
