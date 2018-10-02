<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); //Report all errors

error_reporting (E_ALL ^ E_NOTICE);  //Discard errors about undefined variables
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <link rel="stylesheet" href="styles.css" type="text/css" />
  <title>Check an Accounts Balance</title>
  
  
  
  </head>
  <body>
     <div class="menu">
       
       
       
       <p class="links">
       
       
         <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account</a> <a href='./games_index.php'>Games</a>
       </p> 
       
       </div>
     
        <?php
         $form = "<form action='./moneycheck.php' method='post'>
         <table>
         <tr>
          <td>Account ID</td>
          <td><input type='text' name='ID' /> </td>
         </tr>
         
         <tr>
          <td>Pin</td>
          <td><input type='password' name='pin' /></td>
         </tr>
         
         <tr>
         <td>
        <input type='submit' name='money' value='check'/></td>
         </tr>
         </table>
         </form>";
         
         if($_POST['money']){               //if submit butotn is pushed
           $ID =  $_POST['ID'];
           $pin = $_POST['pin'];
         
         if($ID){
           if($pin){                                 //Make sure fields have been filled out
           
           require("worker.php");                     //Get connect page
           
           $pin = md5($pin); 
                              //Encrypt pin
           
           
           $query = mysql_query("SELECT * FROM money WHERE User='$ID'");   //Look for user id in database  (select all from (db) where (x) = y)
           $numrows = mysql_num_rows($query);
           if($numrows == 1){
               $row = mysql_fetch_assoc($query);  //get info from the db query
               $dbpin = $row['Pin'];
               $dbID = $row['User'];
               $dbBal = $row['Balance'];
               if ($pin == $dbpin){
                if($dbBal == 0){
                  $pay = "All money has been paid";
				  
                }
                else
                   echo "<center><h2>" . "This account owes: ". "$" . $dbBal . "<h2></center></br>"; 
					$query = mysql_query("SELECT * FROM transactions WHERE buyer='$ID'");
					
				   
				   echo "<center><table border class='results'><tr><td>Transaction ID</td><td>Transaction Info</td><td>Buyer</td><td> Transaction AMT</td></tr>";
				   while($row = mysql_fetch_assoc($query)){
				   $trans_amt = $row['account_total'];
				   $trans_id = $row['trans_id'];
				   $trans_info = $row['transaction_info'];
				   $buyer = $row['buyer'];
				   $trans_time = $row['trans_time'];
				   $trans_charge = $row['trans_amt'];
				   echo "<tr><td>$trans_id</td><td>$trans_info</td><td>$buyer</td><td>$trans_charge</td></tr>";
				     
				   }
				   echo "</table></center>";
					
					
					
               }
               else
                  echo "Pin does not match ID";   
			
           }
          else
              echo "ID not found .";
          
           mysql_close();
           }
           else
                echo "Please enter a pin";
              
         }
         else
              echo "Please enter an ID";
         
         }
         else
		 ?>
         <div align="center">
			<div style="border: solid 2px black; background-color:#47A3FF; margin: 10px; width: 350px; height: auto;">
		 
		 <?php    
			 echo $form;
         ?> 
        </div>
         
	
		<div style="text-align:center; font-size:18px; margin:75;">
			<a href="moneycheck.php">Go Back</a>
         </div>
	
 
	
       
  </body>
</html>
