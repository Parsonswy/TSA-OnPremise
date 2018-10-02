<?php
error_reporting (E_ALL ^ E_NOTICE);  //Discard errors about undefined variables
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <link rel="stylesheet" href="styles.css" type="text/css" />
  <title></title>
  </head>
  <body>
     <div class="menu">
       <p class="links">
       <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
       </p>
  </div>
  <?php
  
  
	  $login_form = "<form action='./moneycharge.php' method='post'> 
				<table>
					<tr>
						<td>Account ID</td>
						<td><input type='text' name='id' value='$get_user'/></td>
					</tr>
					<tr>
						<td> Charge Amount </td>
					    <td> <input type='text' name='charge_amt' value=''/> </td>
					</tr>
					<tr>
						<td> <font color='red'> Charge info </font> </td>
						<td> <input type='text' name='trans_info' value='' placeholder='What is this transaction for?'/></td>
					<tr>	
						<td></td>
						<td> <input type='submit' name='action_btn' value='Login' </td>
					</tr>
					</table>
			  </form>";
			  
	   
	  
		if ($_POST['action_btn']){
			$get_user = $_POST['id'];
			$get_trans = $_POST['charge_amt'];
			$get_infop = $_POST['trans_info'];
				if($get_user){
						if($get_infop){
							require("./worker.php");
							
							$query = mysql_query("SELECT * FROM money WHERE User='$get_user'");   //Look for user id in database
							$numrows = mysql_num_rows($query);
							if($numrows == 1){
								$row = mysql_fetch_assoc($query);  //get info from the db query
								$db_user = $row['User'];
								$db_bal  = $row['Balance'];
								$trans_time = date('h:i:s');
								
										
											if($get_trans){   //Start transaction code
												if($get_infop){
												$query = mysql_query("SELECT * FROM money WHERE User='$db_user'");   //Look for user id in database
												$numrows = mysql_num_rows($query);
												if($numrows == 1){
												$db_pin_verif = $row['Pin'];
												$db_bal_verif = $row['Balance'];
												
														$trans_total = $db_bal_verif + $get_trans;
														mysql_query("UPDATE money SET Balance='$trans_total' WHERE User='$get_user'");
														mysql_query("INSERT INTO transactions VALUES('$db_bal', '', '$get_infop', '$db_user', '$trans_time', '$get_trans')");
														mysql_close();
														$error_msg = "Transaction complete";
													
												 }
												 else
													$error_msg = "A major may error has occurred, login server may be compromised. Try again and if this continues contact Wyatt ASAP.";
												}
												else
													$error_msg = "Please enter the reason for this transaction. EX. Raffle 2 tickets.";
											}
											else
												$error_msg = "Please enter a transaction amount.";
							}
							else
								$error_msg="The user name entered was not found in the data base.";
								//mysql_close(); //Close database connection
						}
						else
						$error_msg = "Please enter the transaction information";
				}
				else
					$error_msg="Please enter a user name.";
					echo $error_msg;
		}
		else
		?>
		
		<div align="center">
			<div style="width:350px; height: auto; background-color: #47A3FF; border: solid 2px black; margin: 10px;">
			<?php
				echo $login_form;
			?>
			</div>
		</div>
	
	
	
		
  </body>
</html>
