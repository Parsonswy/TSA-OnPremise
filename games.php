<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
	<head>
		<?php
			//Wheel of Joy Prices
			$joy_price = 3;
			$joy_special = 2;
			$joy_price_special =5; 
			//Apple Ipad Mini Prices
			$Apad_price = 5;
			$Apad_special = 3;
			$Apad_price_special = 10;
			//Mystery Mania Prices
			$Mania_price = 5;
			//Red, White & Blue Raffle Prices
			$color_price = 10;
			$color_special = 2;
			$color_price_special = 15;
		?>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<title>Auction Program 1.0 - MGM Charge</title>
	</head>
	
	<body>
		  <div class="menu">
       
       
       
       <p class="links">
       
       
        <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="moneycharge.php">Charge Money</a> <a href="New_Account.php">New Account</a> <a href='./games.php'>Games</a>
       </p> 
       
       </div>
		
		<?php
			$form = "<form action='./games.php' method='POST'>
				<table>
					<tr>
						<td>User:</td>
						<td><input type='text' name='user' value=''/></td>
					</tr>
					<tr>
						<td>Pin:</td>
						<td><input type='password' name='pin' value=''/></td>
					</tr>
					<tr>
						<td>Game:</td>
						<tr><td><input type='radio' name='game' value='joy'/>Wheel of Joy</td></tr>
						<tr><td><input type='radio' name='game' value='Apad'/>Apple Ipad Minni</td></tr>
						<tr><td><input type='radio' name='game' value='Mania'/>Mystery Mania</td></tr>
						<tr><td><input type='radio' name='game' value='color'/>Red, White & Blue Raffle</td></tr>
					</tr>
					<tr>
						<td># of tickets</td>
						<td><input type='text' name='tickets' value='' /></td>
						<td><input type='submit' name='charge' value='Charge Money'/></td>
					</tr>
				</table>
			</form>";
			
			
			if(ISSET($_POST['charge'])){
				$user = $_POST['user'];
				$pass = $_POST['pin'];
				$game = $_POST['game'];
				$ticket = $_POST['tickets'];
				if($user){
					if($pass){
						if($game){
							
							require('./worker.php');
							$query = mysql_query("SELECT * FROM money WHERE User='$user'");
							if(mysql_num_rows($query) == 1){
								$row = mysql_fetch_assoc($query);
								$db_id = $row['ID'];
								$db_pin = $row['Pin'];
								$db_bal = $row['Balance'];
								$pass_ecy = md5($pass);
								
								if($pass_ecy === $db_pin){
									function transaction($amt, $trans_infop){
										GLOBAL $db_bal;
										GLOBAL $user;
										GLOBAL $db_id;
										$trans_time = date('h:i:s');
										$db_return_amt = $db_bal + $amt;											
										mysql_query("UPDATE money SET BALANCE='$db_return_amt' WHERE ID='$db_id'");
										mysql_query("INSERT INTO transactions VALUES('$db_return_amt', '', '$trans_infop', '$user', '$trans_time', '$amt')");
										
										
									}
									if($ticket > 1){$special = 1;} else{$special = 0;}
										
										if($game == "joy"){
											if($special == 1){
												$times = $ticket / 2;
												$amount = $joy_price_special * $times;
												$trans_info = "Wheel of Joy || Special: $special ||  * $times";
												transaction($amount, $trans_info);
											}
											else{
												$amount = $joy_price;
												$trans_info = "Wheel of Joy || Special: $special ||  * 0";
												transaction($amount, $trans_info);
												}
										}
										elseif($game == "Apad"){
											if($special == 1){
												$times = $ticket / 3;
												$amount = $Apad_price_special * $times;
												$trans_info = "Apple Ipad|| Special: $special ||  * $times";
												transaction($amount, $trans_info);	
											}
											else{
												$amount = $Apad_price;
												$trans_info = "Wheel of Joy || Special: $special ||  * 0";
												transaction($amount, $trans_info);
												}
										}
										elseif($game == "Mania"){
												$amount = $Mania_price * $times;
												$trans_info = "Mystery Mania|| Special: $special ||  * 0";
												transaction($amount, $trans_info);
										}
										elseif($game == "color"){
											if($special == 1){
												$times = $ticket / 2;
												$amount = $color_price_special * $times;
												$trans_info = "Red, White & Blue|| Special: $special ||  * $times";
												transaction($amount, $trans_info);
											}
											else{
												$amount = $color_price;
												$trans_info = "Wheel of Joy || Special: $special ||  * 0";
												transaction($amount, $trans_info);
												}
											echo $special;	
										}							
										else
											echo "ERROR in game selection protocol";
								}	
								else
									echo "Incorrect Password.";
							}
							else
								echo "User not found.";
							mysql_close();
						}
						else
							echo "Please select a game";
					}
					else
						echo "Please enter a pin";
				}
				else
					echo "Please enter a user name";
			
			}
			
		?>
		
		 <div align="center">
			<div style="border: solid 2px black; background-color:#47A3FF; margin: 10px; width: 550px; height: auto;">
		 
		 <?php    
			 echo $form;
         ?> 
        </div>
         
	
		
	</body>
</html>