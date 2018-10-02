<?php
$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa") or die($mysqli>connect_error);
session_start();
$uuid = $_GET['uuid'];
$query = $mysqli->query("SELECT * FROM `tabcards` WHERE uuid=$uuid");
if($query->num_rows){
	$row = $query->fetch_assoc();
	echo "User: " . $row['name'] ."|" . $row['uuid']. "</br>";
	echo "Balance:" .$row['balance'] . "</br>";
	var_dump($row);
	echo "</br>___________<a href='./sessionWipe.php'>Session Data</a>___________</br>";
	var_dump($_SESSION);
	$mysqli->close();
}else{$mysqli->close(); die("$uuid Not found");}
?>