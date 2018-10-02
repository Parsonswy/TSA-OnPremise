<?php
error_reporting (E_ALL ^ E_NOTICE);  //Discard errors about undefined variables

//error_reporting(E_ALL);
//ini_set('display_errors', 1); //Report all errors
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<title>Auction Program - Cashout complete</title>
	</head>
	
	<body>
	  <div class="menu">      
       <p class="links">
       <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
       </p> 
       </div>
		
		<?php
			
					$user = $_GET['user'];
					$com = $_POST['com'];
					require('./worker.php');
					mysql_query("UPDATE money SET Balance='0' WHERE User='$user'");
					$query = mysql_query("SELECT * FROM money WHERE User='$user'");
					$numrows = mysql_num_rows($query);
						if($numrows > 0){
							$info = "Account cash out";
							mysql_query("INSERT INTO transactions VALUES('', '', '$info', '$user', '', 'Cash Out')");
							mysql_query("INSERT INTO payments VALUES ('', '$user', '$com')");
							echo "<h4><center>Account of <b>$user</b> has been successfully 'Cashed Out.' Thank you for coming and have a save drive home!</center></h4>";
						}
						else
							echo "<center><h4>Warning account not found. Please verify the account balance has been reset manually</h4></center>";
				
		?>
	</body>
</html>