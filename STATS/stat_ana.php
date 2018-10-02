<?php
$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa");
$query = $mysqli->query("SELECT * FROM `transactions` WHERE `completed`=1 && name !='' && `timestamp` BETWEEN 'Mar 19, 2016 18:00' AND 'Mar 19, 2016 21:30'");

//Total number of purchases for each item each @quantityDemand
$LuckyYou = array(0,0);
$BagofBeer = array(0,0,0);
$NameYourCard = array(0,0,0);
$WineBarrel = array(0,0,0);
$Mystery = array(0,0,0);
$error = array();

while($rows = $query->fetch_assoc()){
	$log = explode(" | ",$rows["log"]);
	foreach($log as $subCharge){
		if($subCharge == " ")
			continue;
		//Lucky You - 1 for 5 | 3 for 10
		if($subCharge == "Lucky You - 1 for $5"){
			$LuckyYou[0]++;
			continue;
		}elseif($subCharge == "Lucky You - 3 for $10"){
			$LuckyYou[1]++;
			continue;
		}
		
		//Name your card - 1 for $20 | 2 for $40 | 3 for $60
		if($subCharge == "Name Your Card 1 for $20"){
			$NameYourCard[0]++;
			continue;
		}elseif($subCharge == "Name Your Card 2 for $40"){
			$NameYourCard[1]++;
			continue;
		}elseif($subCharge == "Name Your Card 3 for $60"){
			$NameYourCard[2]++;
			continue;
		}
		
		//Bag of Beer - 1 for $10 + INC(1,10)
		if($subCharge == "Bag of Beer - 1 for $10"){
			$BagofBeer[0]++;
			continue;
		}elseif($subCharge == "Bag of Beer - 2 for $20"){
			$BagofBeer[1]++;
			continue;
		}elseif($subCharge == "Bag of Beer - 3 for $30"){
			$BagofBeer[2]++;
			continue;
		}
		
		//Wine Barrel - 1 for $15 + INC(1,15)
		if($subCharge == "Wine Barrel Card Raffle - 1 for $15"){
			$WineBarrel[0]++;
			continue;
		}elseif($subCharge == "Wine Barrel Card Raffle - 2 for $30"){
			$WineBarrel[1]++;
			continue;
		}elseif($subCharge == "Wine Barrel Card Raffle - 3 for $45"){
			$WineBarrel[2]++;
			continue;
		}
		
		//Mystery Mania - 1 for $5 + INC(1,5)
		if($subCharge == "Mystery Mania - 1 for $5"){
			$Mystery[0]++;
			continue;
		}elseif($subCharge == "Mystery Mania - 2 for $10"){
			$Mystery[1]++;
			continue;
		}elseif($subCharge == "Mystery Mania - 3 for $15"){
			$Mystery[2]++;
			continue;
		}
		
		array_push($error, $subCharge);
	}
}

echo "Lucky <br/>";
foreach($LuckyYou as $index){
	echo $index . "<br/>";
}

echo "Card <br/>";
foreach($NameYourCard as $index){
	echo $index . "<br/>";
}

echo "Beer <br/>";
foreach($BagofBeer as $index){
	echo $index . "<br/>";
}

echo "Wine <br/>";
foreach($WineBarrel as $index){
	echo $index . "<br/>";
}

echo "Mystery <br/>";
foreach($Mystery as $index){
	echo $index . "<br/>";
}

echo "Errors <br/>";
foreach ($error as $index){
	echo $index . "<br/>";
}

$query = $mysqli->query("SELECT `ID`,`name`,`price` FROM `baskets` WHERE 1");
while($rows = $query->fetch_assoc()){
	echo $rows["ID"] . "<br/>";
}
?>
