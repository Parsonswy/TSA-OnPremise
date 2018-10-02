<?php 
require("./Render.php");

$domElements = array(false, true, false, false, true, true, true, true);
if(strlen(@$_COOKIE["CURRUD"]) == 6)
	$domElements = array(true, true, true, true, true, true, true, true);

Render::renderGlobalLeftNav($domElements);
?>