<?php
	if($_GET["req"] == "pop"){
      $mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa");
      $name = $_POST["text"];
      $sql = "SELECT `name`,`type` FROM `names` WHERE name LIKE '%".$name."%'";
      $query = $mysqli->query($sql);
      $html = "<table><tr>";
      while($row = $query->fetch_assoc()){
        if($row["type"] == 15){//Past year
          $html .= "<p onclick='pushToText(" . $row['name'] .");'>" . $row["name"] . "</p>";
        }elseif($row["type"] == 30){//free entry
          $html .= "<font color='green' onclick='pushToText(\"" . $row['name'] ."\");'>" . $row["name"] . "</font>";
        }
      }
      exit($html . "</tr></table>");
    }
?>
<!DOCTYPE html>
<html>
	<head>
      <?php
        echo "<title>Card Activation</title>";
        if(strlen($_GET['uuid']) === 6){
          $uuid = $_GET['uuid'];
          echo "<title>Card Activation $uuid</title>";
          $form = '<form action="./tabOpen.php" method="GET">
			<input type="hidden" name="uuid" value="' . $_GET["uuid"] . '"
			<table><tr>Name:<input type="text" id="#name" name="user" value="" onkeypress="search()"/></tr>
			<tr><input type="text" name="entry" value="" placeholder="# Going In"</tr>
			<input type="submit" name="activate" value="Activate"/></tr></table>
		</form>';
          
          echo $form;
        }else{echo "No UUID specified";}
      ?>
  	</head>
	<body>
      <?php
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
              ?>
      <hr/>
      	<div id='#suggestions'>
          Suggestions
        </div>
  		<script type="text/javascript" src="./AJAX/tabSearch.js"></script>
	</body>
<html>