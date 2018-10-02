<?php
error_reporting (E_ALL ^ E_NOTICE);  //Discard errors about undefined variables

//error_reporting(E_ALL);
//ini_set('display_errors', 1); //Report all errors
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<title>2014 Auction Program 1.0 - Cash Out</title>
		<style></style>
	</head>
			
	<body>	
		<div class="menu">      
		   <p class="links">
			 <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
		   </p> 
       </div>
		<?php
			$form = "	<center><div style='width:350px; height: auto; background-color: #47A3FF; border: solid 2px black; margin: 10px;'>
					<form action='./cash_out.php' method='post'>
							<table>
								<tr>
									<td>Username:</td>
									<td><input type='text' name='user' value='$user'/></td>
								</tr>
								<tr>
									<td>Password:</td>
									<td><input type='password' name='pin' value=''/></td>
								</tr>
								<tr>
									<td></td>
									<td><input type='submit' name='cash' value='Cash Out'</td>
								</tr>
							<table>
					</form>
					</div></center>";
				if(ISSET($_POST['cash'])){
					$user = $_POST['user'];
					$pass = $_POST['pin'];
					if($user){
						if($pass){
								$pin_ecy = md5($pass);
									require('./worker.php');
									$query = mysql_query("SELECT * FROM money WHERE User='$user'");
									if(mysql_num_rows($query) == 1){
										$row = mysql_fetch_assoc($query);
										$bal = $row['Balance'];
										$query = mysql_query("SELECT * FROM transactions WHERE buyer='$user'");
										$numrows = mysql_num_rows($query);
										if($numrows > 0){
												echo "<center><h2> This account owes $$bal.00</h2>";
												echo "<table border><tr><td>Transaction ID</td><td>Transaction Info</td><td>Buyer</td><td>Transaction Amt</td></tr>";
											 while($row = mysql_fetch_assoc($query)){
												$trans_id = $row['trans_id'];
												$trans_info = $row['transaction_info'];
												$buyer = $row['buyer'];
												$trans_amt = $row['trans_amt'];
												echo "
													<tr><td>$trans_id</td><td>$trans_info</td><td>$buyer</td><td>$trans_amt</td></tr>
												";
												}
												echo "</table></center>";
												echo "<center><form method='post' action='./goodnight.php?user=$user'>
														<div style='background-color:#FF6600'>
															<table>
																<tr>
																	<td>Comments:</td>
																	<td><input type='text' name='com' value=''/></td>
																</tr>
																<tr>
																	<td></td>
																	<td><input type='submit' name='gn' value='Reset Balance'/></td>
																</tr>
															</table>
														</div>
													</form></center>";
														
											
										}
										else
											echo "<h2>This account owes $bal</h2>
												</br>
												<h4>No transaction data was found. Please report this ASAP</h4>";
									}
									else
										echo "User not found</br>$form";
						
						}
						else
							echo "Please enter a password</br>$form";
					}
					else
						echo "Please enter a username </br>$form";
				}	
				else
					echo "$form";
				
				
		?>
	</body>
</html>