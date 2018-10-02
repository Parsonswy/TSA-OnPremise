<?php
	$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa");
	$query = $mysqli->query("SELECT * FROM `transactions` WHERE `completed`=1");
	echo "Found " . $query->num_rows . "completed transactions totaling to ";
	$total = 0;
	while($rows = $query->fetch_assoc()){
      $total += $rows["cost"];
    }
	echo $total . "</br>Discrepancy of "; 
    
	$total = 0;
	$query = $mysqli->query("SELECT `balance` FROM `tabcards` WHERE 1");
	while($rows = $query->fetch_assoc()){
      $total += $rows["balance"];
    }
	echo $total . ". <br/> In the bank: ";
	
	$total = 0;
	$query = $mysqli->query("SELECT `cost` FROM `transactions` WHERE `completed`=3");
	while($rows = $query->fetch_assoc()){
		$total += $rows["cost"];
    }
	echo $total . "<br/>Baskets:";
	
	$total = 0;
	$query = $mysqli->query("SELECT `price` FROM `baskets` WHERE 1");
	while($rows = $query->fetch_assoc()){
      $total += $rows["price"];
    }
	echo $total;

    echo "<hr/>";
	
	$breakDowns = array();

	while($rows = $query->fetch_assoc()){
      $breakup = explode($rows["log"], " | ");
    }

?>