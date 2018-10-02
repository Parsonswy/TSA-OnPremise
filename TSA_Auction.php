<!DOCTYPE HTML>
<html>
<?php

?>
<head>
  <link rel="stylesheet" type="text/css" href="./Static/CSS/Scrollbar.css"/>
  <link rel="stylesheet" type="text/css" href="./Static/CSS/Main.css"/>
  <link rel="stylesheet" type="text/css" href="./Static/CSS/Pages.css"/>
  <link rel="stylesheet" type="text/css" href="./Static/CSS/UserAccountPortal.css"/>
</head>
<body>
  <div id="client"><!--Client Wrapper-->
    <navigation id="navigation">
      <a><div class="link">
        Link
      </div></a>
    </navigation>
	  <div id='contentCover' style='display:none;background-color:#000;opacity:0.6;z-index:100;width:810px;height:600px;position:fixed;top:10px;left:210px;'></div><!--Emph popup shade-->
    <section class='popup' id='tsa_popup' style='display:none;'></section><!--Emph popup window-->
    <div id="content"></div><!--App Window-->
    <div id="errorDisplay">
			<p id="errorMSG" style="float:left;"></p>
			<img id="errorIMG" style='float:right;margin-top:1em;' height='42px' width='48px' src='./Static/IMG/warning.png'/>
		</div>
  </div>
</body>
<script type="text/javascript" src="./Static/JS/config.js"></script>
<script type="text/javascript" src="./Static/JS/Main/Client_Visuals.js"></script>
<script type="text/javascript" src="./Static/JS/Main/Client_Network.js"></script>
<script type="text/javascript" src="./Static/JS/Main/Client_Pages.js"></script>
<script type="text/javascript" src="./Static/JS/Main/Client.js"></script>
<script type="text/javascript" src="./Static/JS/Greensock_src/minified/TweenLite.min.js"></script>
<script type="text/javascript" src="./Static/JS/Greensock_src/minified/plugins/CSSPlugin.min.js"></script>
</html>
