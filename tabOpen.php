<?php
echo "<title>Card Activation</title>";
if(strlen($_GET['uuid']) === 6){
    $uuid = $_GET['uuid'];
    echo "<title>Card Activation $uuid</title>";
	
	if(ISSET($_GET['activate'])){
		if($_GET['user']){
			$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa");
			$user = $_GET['user'];
			$uuid = $mysqli->real_escape_string($uuid);
			$query = $mysqli->query("SELECT `active` FROM tabcards WHERE uuid=$uuid");
			if(!$query->num_rows){
				$entry = intval($_GET['entry']) * 30;
				$mysqli->query("INSERT INTO `tabcards` VALUES('',
															  '$uuid',
															  '$entry',
															  '$user',
															  '1')");
				$query = $mysqli->query("SELECT `active` FROM tabcards WHERE uuid=$uuid AND active=1");
				if($query->num_rows == 1){
					$mysqli->close();
					echo "UUID $uuid succesfully assigned to $user and was charged $".$entry."for entry";
				}
			}else{$mysqli->close();
				   echo "UUID " . $uuid . " has already been activated";}
		}else{echo "No name specified";}
	}else{}
}else{echo "No UUID specified";}
?>
<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?php
		echo '<form action="./tabOpen.php" method="GET">
			<input type="hidden" name="uuid" value="' . $_GET["uuid"] . '"
			<table><tr>Name:<input type="text" name="user" value=""/></tr>
			<tr><input type="text" name="entry" value="" placeholder="# Going In"</tr>
			<input type="submit" name="activate" value="Activate"/></tr></table>
		</form>';
		?>
	</body>
<html>