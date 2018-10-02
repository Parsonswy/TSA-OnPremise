<?php
    $form = "<title>Transfer Account</title>
            <form action='./transferAccount.php' method='POST'>
            Account 1:<input type='text' name='accnt1' value='' placeholder='Account 1' required='required'> ---> Account 2:<input type='text' name='accnt2' value='' placeholder='Account 2' required='required'/>Transfer Balakce?<input type='checkbox' name='balance' value='1'/>
            </br><input type='submit' name='transfer' value='Transfer Account'/>
            </form>";
    if(ISSET($_POST['transfer'])){
        $set = 0;
        if($_POST['accnt1']){$set++; $accnt1 = $_POST['accnt1'];}else{} 
        if($_POST['accnt2']){$set++; $accnt2 = $_POST['accnt2'];}else{}
        if($set == 2){
            if($_POST['balance'] == 1){$transfer = $_POST['balance'];}else{}
            $mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa") or die($mysqli->error());
            $query = $mysqli->query("SELECT `balance`,`name`,`id`,`active` FROM `tabcards` WHERE uuid=$accnt1");
            if($query->num_rows == 1){
                $row = $query->fetch_assoc();
                $id_1 = $row['id'];
				$balance = $row['balance'];
				$name = $row['name'];
				$active = $row['active'];
                echo $active;
				if($active == 1){
                    $query = $mysqli->query("SELECT `active`,`id` FROM `tabcards` WHERE uuid=$accnt2");
                    if(!$query->num_rows == 1){
						$mysqli->query("INSERT INTO `tabcards` VALUES('',
																	  '$accnt2',
																	  '$balance',
																	  '$name',
																	  '1')");
						$query = $mysqli->query("UPDATE `tabcards` SET active=3 WHERE uuid=$accnt1");
						$query = $mysqli->query("UPDATE `tabcards` SET balance=-1 WHERE uuid=$accnt1");
						$query = $mysqli->query("SELECT `id` FROM `tabcards` WHERE uuid=$accnt2");
						if($query->num_rows){
							die($form . "</br>Transfer Successful!");			
						}else{die($form . "</br>Transfer failed!");}
                    }else{die($form . "</br> UUID 2 ($accnt2) already exists");}
                }else{die($form . "</br>UUID 1 ($accnt1) is not <a href='./tabOpen.php?UUID=$accnt1'>active</a>!");}
            }else{die($form . "</br>UUID 1 ($accnt1) not found.");}
        }else{die($form . "</br>Missing form data " . $set);}
    }else{echo $form;}//Do Nothing
?>