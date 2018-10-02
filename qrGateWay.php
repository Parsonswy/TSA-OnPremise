<?php
	session_start();
	session_destroy();
    $uuid = strval($_GET['UUID']);
    if($uuid){
        $mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa");
        $query = $mysqli->query("SELECT `name`,`balance`,`id`,`uuid`,`active` FROM `tabcards` WHERE `uuid`=$uuid");
		echo $mysqli->error;
		$numrows = $query->num_rows;
		echo $numrows;
        if($numrows == 1){
            $row = $query->fetch_assoc();
			$active = $row['active'];
			echo $active;
            if($active == 1){
                session_start();
                $_SESSION['name'] = $row['name']; //Tab Holder
                $_SESSION['bal'] = $row['balance']; //Balanc on tab
                $_SESSION['id'] = $row['id'];       //SQL Specific ID for verification 
                $_SESSION['uuid'] = $row['uuid'];   //UUID saved to card. X ref w/SQL id for verification
				$uuid = $row['uuid'];
                header("Location: ./gateway.php?uuid=$uuid");
                die();
            }else
                exit("Card has been de-actived.");
        }else
            exit("<a href='http://" . $_SERVER["SERVER_ADDR"] . "/html/TSA/tabOpen.php?uuid=" . $uuid . "'>Click here to active $uuid</a>");
    }else
        exit("NULL Request Detected<br/>
				<form action='./qrGateWay.php' method='GET'>
					<input type='text' name='UUID' value='' placeholder='Account UUID'/>
					<input type='submit' name='' value='Select'/>
				</form>");
$mysql->close();
?>