<?php 
if(!@$q = $_GET["q"])
	exit("No Results");

require("/var/www/html/Rebuild/Apps/Config/Config.php");
require(CONFIG::DOC_ROOT . "/Apps/Mysqli/Mysqli.php");
	
$AppMysqli = new AppMysqli();
if(!$mysqli = $AppMysqli->initMysql()){
	exit("Database Communication Error!");
}

require(CONFIG::DOC_ROOT . "/Apps/User/User.php");
$User = new User();
$query = $User->autoFillIndex(true,"name",$q);

while($rows = $query->fetch_assoc()){
	echo "<div class='r_aO_qF_rEntry' onclick='r_aO_qF_rSelectEntry(this);'>" . 
			$rows["name"] . 
		"</div>";
}
?>