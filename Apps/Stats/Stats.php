<!DOCTYPE HTML>
<html>
  
  <head>
    <meta http-equiv="refresh" content="5">
    <style type="text/css">
    	html{
          background-color:black;
          color:green;
		  font-size:16px;
        }
      	div.numbers{
          position:fixed;
          top:650px;
        }
    </style>    
  </head>
  <body>
    
    <?php
    require("/var/www/html/Rebuild/Apps/Mysqli/Mysqli.php");
    $AppMysqli = new AppMysqli();
    $mysqli = $AppMysqli->initMysql();

	//Active Accounts
	$active = $mysqli->query("SELECT `uuid` FROM `app_users` WHERE `active`=1");
	$active = $active->num_rows;
    //Totals
    $totals = $mysqli->query("SELECT * FROM `app_statsTotal` WHERE 1");
    $stats = array();
	while($ttlRows = $totals->fetch_assoc()){
    	array_push($stats, array($ttlRows["STAT"], $ttlRows["VALUE"]));
    }
	
	$cashedOut = $mysqli->query("SELECT `transPrice` FROM `app_transTransactions` WHERE `transType`=3");
	$cashOut = 0;
	while($cRows = $cashedOut->fetch_assoc()){
      $cashOut += $cRows["transPrice"];
    }
	$closed = $cashedOut->num_rows;
	
	echo "Open Accounts: " . $active . "<br/>";
	echo "Closed Accounts: " . $closed . "<br/>";
	echo "Total Gross: $"	. ($stats[0][1] - $cashOut) . "<br/>";
	echo "Total Sales: " . $stats[1][1] . "<br/>";
	
	

	$avgSale = (intval($stats[0][1]) / intval($stats[1][1]));
	echo "Average Sale $" . $avgSale;
	
	echo "<hr/><table cellpadding='5
    '><tr><td>ID</td><td>OP</td><td>UUID</td><td>Price</td></tr>";
    //20 Most recent transactions
    $transactions = $mysqli->query("SELECT * FROM `app_transTransactions` ORDER BY `transID` DESC LIMIT 20");
	while($transRows = $transactions->fetch_assoc()){
      	$ID = $transRows["transID"];
      	$OP = $transRows["transOP"];
      	$uuid = $transRows["transUUID"];
      	$price = $transRows["transPrice"];
      	$desc = $transRows["transDesc"];
		echo "<tr><td>" . $ID . "</td><td>" . $OP . "</td><td>" . $uuid . "</td><td>" . $price . "</td><td>" . $desc . "</tr>";
    }
	
	?>
    <!--<div class='numbers' id='numbers'>
      
    </div>
    <script type="text/javascript">
    	function genNumbers(){
          var loopI = 160;
          var numbers ="";
          for(i=0;i<loopI;i++){
            if(Math.random() >= 0.5)
            	numbers += "1";
            else
              	numbers += "0";
          }
          document.getElementById("numbers").innerHTML = numbers;
        }
      	setTimeout(genNumbers,700);
    </script>-->
   </body>
</html>