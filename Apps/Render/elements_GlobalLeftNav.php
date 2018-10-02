<?php
	//probably the best script in the whole program
	//TODO:Make Everything Better (@Wyatt 2k17/18)
	$elem = array();

	//0.5 View Account Portal
	$Portal = array("color"=>"white","pgLink"=>CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountController.php?action=2&uuid=" . @$_COOKIE["CURRUD"],"text"=>"Account Portal");
	array_push($elem,$Portal);
	//1. Select Account
	$AccountSelect = array("color"=>"white","pgLink"=>CONFIG::DOC_ROOT_WEB . "/Apps/Render/userAccountSelector.php","text"=>"Select Account");
	array_push($elem, $AccountSelect);
	
	//2. New transactions => transation.php
	$Charge = array("color"=>"green","pgLink"=>CONFIG::DOC_ROOT_WEB . "/Apps/Commerce/TransactionManager.php?action=1","text"=>"New Transaction");
	array_push($elem, $Charge);

	//3. Cash out => Cashout.php
	$cashOut = array("color"=>"green","pgLink"=>CONFIG::DOC_ROOT_WEB . "/Apps/Commerce/TransactionManager.php?action=3","text"=>"Cashout");//Right?
	array_push($elem, $cashOut);

	//4. Auction items => Basket/Basket.php
	$SysBasket = array("color"=>"maroon","pgLink"=>"","text"=>"Auction Items");
	array_push($elem, $SysBasket);

	//5. System Control / Reports => ????
	$SysCTL = array("color"=>"maroon","pgLink"=>"","text"=>"System Control");
	array_push($elem, $SysCTL);

	//6. System statistics reports / generator => ????
	$SysStats = array("color"=>"maroon","pgLink"=>"","text"=>"Statistics");
	array_push($elem, $SysStats);

	//7. Transfer account => transAccount.php
	$SysTrans = array("color"=>"maroon","pgLink"=>"","text"=>"Transfer Account");
	array_push($elem, $SysTrans);
?>
