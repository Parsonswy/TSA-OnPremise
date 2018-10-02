<?php
error_reporting (E_ALL ^ E_NOTICE);  //Discard errors about undefined variables

//error_reporting(E_ALL);
//ini_set('display_errors', 1); //Report all errors
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<?php
			$price = '5';
			$speical = '3';
			$special_price = '10';
		?>
		<style type='text/css'>
			.blind{
			font-size: 16px;
			}
		</style>
	</head>
	<body>
	   <div class="menu">      
       <p class="links">
       <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
       </p> 
       </div>
		
		<?php	
			$form = "<center><div style='width:350px; height: auto; background-color: #47A3FF; border: solid 2px black; margin: 10px;'><form action='./apple.php' method='post'>
					<form action='./apple.php' method='post'>
						<table>
							<tr>
								<td><font class='blind'>Name:</font></td>
								<td><input type='text' name='user' value='$get_user' placeholder='Your Username'/></td>
							</tr>
							<tr>
								<td><font class='blind'>Pin:</font></td>
								<td><input type='password' name='pin' value=''/></td>
							</tr>
							<tr>
								<td><font class='blind'>Ticket Number</td>
								<td><select name='tickets'> 
								<option value='1'>1 Necklace  $$price</option> 
								<option value='3'>3 Necklaces  $$special_price</option>
								<option value='6'>6 Necklaces $20</option>
								</select></td>
							</tr>
							<tr>
								<td></td>
								<td><input type='submit' name='charge' value='Click to continue'/></td>
							</tr>
						</table>
			</form></center></div>";
		if(ISSET($_POST['charge'])){
			$get_user = $_POST['user'];
			$get_pin = $_POST['pin'];
			$get_ticket = $_POST['tickets'];
			$get_info = "Apple Ipad Mini || $get_ticket tickets || $get_user : Account";
				if($get_user){
					if($get_pin){
						if($get_ticket){
							if($get_ticket  == 1){}
							elseif($get_ticket == 3){$price = $special_price;}
							elseif($get_ticket == 6){$price = $special_price * 2;}
							else
								echo "<h3><center>Invalid ticket amount returned</br>$form</center></h3>";
							
							echo "<center>Order is being processed, Please click <a href='./confirm.php?user=$get_user&ticket=$get_ticket&price=$price&info=$get_info'>here</a> to continue.</center>";
						}
						else
							echo "<center><h3>Please select a spin amount</br>$form</center></h3>";
					}
					else
						echo "<center>Please enter a pin.</br>$form</center> ";
				}
				else
					echo "<center><h3>Please enter a user name</br>$form</center></h3> ";
		}
		else
			echo $form;
		?>
	</body>
</html>