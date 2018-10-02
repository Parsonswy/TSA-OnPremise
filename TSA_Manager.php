<?php
  require("./config.php");
  require(CONFIG::DOC_ROOT . "/Apps/Operator/Operator.php");
  require(CONFIG::DOC_ROOT . "/Apps/Accounts/Accounts.php");
  require(CONFIG::DOC_ROOT . "/Apps/Accounts/User.php");
  require(CONFIG::DOC_ROOT . "/Apps/Commerce/Transaction.php");
  require(CONFIG::DOC_ROOT . "/Mysqli.php");
  //Authenticate
  session_start();
  $Operator = new Operator();
  if(!$Operator->checkLogin()){
    header("tsastatus: 400");
    //header("Location: " . CONFIG::DOC_ROOT_WEB . "/TSA_Auction.php");
    exit(json_encode(array("message"=>"Access Denied.")));
  }

  //Check for management viewing permission
  if(!$Operator->checkPermission("Manage_canView"))
    exit("Access Denied");
?>
<!DOCTYPE HTML>
<html>
  <head>
    <link rel="stylesheet" href="./Static/CSS/Main.css">
    <link rel="stylesheet" href="./Static/CSS/Manager.css">
    <link rel="stylesheet" href="./Static/CSS/Scrollbar.css">
  </head>
  <body>
    <navigation id="navigation"></navigation>
    <div id="content">
      <section class='fullblock'>
		tset
	  </section>
    </div>
    <br/>
    <a style="text-decoration:none; color:lightblue; font-size:18px; border:1px white solid; padding:2px; position:absolute; top:620px; left:50px" href="./TSA_Auction.php"> Back to Auction</a>
    <div id="errorDisplay">
			<p id="errorMSG" style="float:left;"></p>
			<img id="errorIMG" style='float:right;margin-top:1em;' height='42px' width='48px' src='./Static/IMG/warning.png'/>
		</div>
  </body>
  <script type="text/javascript" src="./Static/JS/config.js"></script>
  <script type="text/javascript" src="./Static/JS/Manage/Manager_Network.js"></script>
  <script type="text/javascript" src="./Static/JS/Manage/Manager_Visuals.js"></script>
  <script type="text/javascript" src="./Static/JS/Manage/Manager_Pages.js"></script>
  <script type="text/javascript" src="./Static/JS/Manage/Manager.js"></script>
  <script type="text/javascript" src="./Static/JS/Greensock_src/minified/TweenLite.min.js"></script>
  <script type="text/javascript" src="./Static/JS/Greensock_src/minified/plugins/CSSPlugin.min.js"></script>
</html>
