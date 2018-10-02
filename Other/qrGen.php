<?php
$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa");
$generated = array();
for($i = 0; $i<=199; $i++){
	$num = rand(100000, 999999);
	if(!array_search($num, $generated)){
		array_push($generated, $num);
		echo "[". $i ."]" .$num . "</br>";
		
		$mysqli->query("INSERT INTO `qrCodes` VALUES('$num','0')");
	}else{$i = $i - 1;}
}

?>