<?php


?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<title>2014 Auction Program - Order Confirm</title>
			<style type='text/css'>
				.header{
					background-color:#66A3FF;
					position: relative;
					top: -20px;
					height: 100px;
					width: 100%;
				}
				
				.infop{
					background-color:#E8E8E8;
					width: 60%;
					border-radius: 40px;
					height: 400px;
				}
			</style>
			<?php
			
			
			?>
	</head>
	
	<body>
		<body>
		  <div class="menu">
       
       
       
       <p class="links">
       
       
         <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="moneycharge.php">Charge Money</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
       </p> 
       
       </div>
		
		<?php
			$get_user = $_GET['user'];
			$get_ticket = $_GET['ticket'];
			$get_charge = $_GET['price'];
			$get_info = $_GET['info'];
		?>
		
			<?php
				if(ISSET($_POST['charge'])){
					if($get_user && $get_ticket && $get_charge && $get_info){
						require('./worker.php');
						$query = mysql_query("SELECT * FROM money WHERE User='$get_user'");
						if(mysql_num_rows($query) == 1){
							$row = mysql_fetch_assoc($query);
							$bal = $row['Balance'];
							$bal_return = $bal + $get_charge;
							mysql_query("UPDATE money SET Balance='$bal_return' WHERE User='$get_user'");
							mysql_query("INSERT INTO transactions VALUES('', '', '$get_info', '$get_user', 'null', '$get_charge')");
							echo "<center><h2>Transaction Successful. New account balance of $get_user is <b>$$bal_return</b></h2>
									<a href='./games_index.php'>Click here to return to the games page</a>";
							$form ='';
						}
						else
							echo "An error has occured. Either the user does not exist or there are multiple instances of $get_user in the database";
								
					}
					else
						echo "It appears that your request is missing some of the required data to log this transaction. Please try again.";
				}
				else
				echo "<div class='header'>
			<center>
				<h2>Transaction Total: $$get_charge.00 </h2>
					
						<h4>You are about to charge $$get_charge.00 to the account of <b>$get_user</b>
					
			</center>
		</div>
		<div class='trans_infop'>
		<hr></hr>
			<div align='center'>
				<div class='infop'>
					
						Transaction Info</br>
						_____________________________________________
						<h3>You are about to charge $$get_charge.00 worth of plays ($get_ticket) to the account of $get_user</h3><h5>Other Info</h5><p>$get_info</p>
						 <form method='post' action='./confirm.php?user=$get_user&ticket=$get_ticket&price=$get_charge&info=$get_info'>
						<input type='submit' name='charge' value='Charge to Account'/>
						</form>
					
				</div>
			</div>
		</div>";
			?>
			
	</body>
</html>