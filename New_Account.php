<?php
error_reporting (E_ALL ^ E_NOTICE);  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1250">
	<link rel="stylesheet" href="styles.css" type="text/css"/>
	<title> Account Creator </title>
  </head>
  <body>
	 <div class="menu">
       
       
       
       <p class="links">
			<a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
		</p>
	</div>
	<?php
		
		
		if($_POST['register_btn']){
			$get_user = $_POST['user'];
			$get_pass = $_POST['pin'];
			$get_retyped_pass = $_POST['retypepin'];
			
			
			if($get_user){
				if($get_pass){
					if($get_retyped_pass){
						if($get_retyped_pass === $get_pass){
							$pin_ecy = md5($get_pass);
							require("./worker.php");
							
							$query = mysql_query("SELECT * FROM money WHERE User='$get_user'");
							$numrows = mysql_num_rows($query);
							if($numrows == 0){
								mysql_query("INSERT INTO money VALUES ('$get_user', '$pin_ecy', '', '')");
								mysql_query("INSERT INTO transactions VALUES ('', '', 'Account created', '$get_user', '', '')");
								
								$query = mysql_query("SELECT * FROM money WHERE User='$get_user'");
								$numrows = mysql_num_rows($query);
								if($numrows == 1){
									$success_msg = "The account was successfully created.";
									$get_user = "";
								
								}
								else
									$error_msg = "An internal error has occurred. Your account was not created.";
							}
							else
								$error_msg = "The selected username is already in use please try again.";
						}
						else
							$error_msg = "The pins that you have entered do not match.";
						mysql_close();
					}
					else
						$error_msg = "Please retype your Pin.";
				}
				else
					$error_msg = "Please enter a pin.";
			}
			else
				$error_msg = "Please enter a username";
		}
		else
	
		
	
	echo $error_msg;
		
		
		$form = "<form action='./New_Account.php' method='POST'>  
		<table>
			<tr>
				<td></td>
				<td><font color='red'> $error_msg </font>$success_msg</td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input type='text' name='user' value='$get_user'/></td>
			</tr>
			<tr>
				<td>Pin</td>
				<td><input type='password' name='pin' value=''/></td>
			</tr>
			<tr> 
				<td>Retype Pin</td>
				<td><input type='password' name='retypepin' value=''/></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' name='register_btn' value='Register'/></td>
			</tr>
		</table>
		</form>";
	
	?>
	<center>
		</br></br></br>
			<div style="border: solid black 2px; background-color:#47A3FF; text-align:center; height: auto; width: 350px; padding: 20px;">
			
			<?php	
					echo $form;  
			?>
			</div>
	</center>
		
 
  
  </body>
  </html>
  