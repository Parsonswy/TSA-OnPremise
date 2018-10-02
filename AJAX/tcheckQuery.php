<?php
session_start();
if(isset($_SESSION['uuid'])){$uuid = $_SESSION['uuid'];
	$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa") or die($mysqli->connect_error);
	$uuid = $mysqli->real_escape_string($uuid);
	$query = $mysqli->query("SELECT `name`,`balance` FROM `tabcards` WHERE uuid=$uuid");
	if($query->num_rows){
		$qturn = $query->fetch_assoc();
		$name = $qturn['name'];
		$bal = $qturn['balance'];
		echo "[$uuid]$name has a balance of $" .$bal;
		$mysqli->close();
	}else{echo "UUID $uuid was not found on record";}
}else{echo "No UUID to process";
	}
?>