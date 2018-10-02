<?php
require("./Basket.php");
if(ISSET($_GET["UP"])){//If form requires updating
  	$Basket = new Basket();
	for($i=$_POST["data-min"]; $i<$_POST["data-total"]; $i++){//loop through all values submited in form
        $content = array("NAME"=>$_POST["name_$i"],
                         "DESCR"=>$_POST["desc_$i"],
                         "USER"=>$_POST["user_$i"],
                         "PRICE"=>$_POST["price_$i"],
        				 "ID"=>$i);
        $res = array();
        if($ret = $Basket->updateData($i, $content) != true){
          array_push($res, $ret);//If error occurs: add to array for later output
        }
        unset($content);

        if(count($res) > 0){
        array_unshift($res, "The Following Errors Have Occured...");
        $js = "<script type='text/javascript'>
                    document.getElementById('#AJAX').innerHTML += '";
          foreach($res as $err)
          	$js .= "<br/>" . $err . "';
                </script>";
      }
	}
}else if(ISSET($_POST["AJX"])){//Generate editor window of baskets (low) - (high) inclusive
  $Basket = new Basket();
  echo $Basket->populateEditor($_POST["low"], $_POST["high"]);//Print to js->httpResponseText. Direct out to user as formatted html
  exit();
}else if(ISSET($_POST["DEL"])){//Delete basket (id)
 $Basket = new Basket();
 $Basket->deleteBasket($_POST["id"]);
 echo "<br/> Basket " . $_POST["id"] . " deleted.";
  exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <style type="text/css"></style>
  </head>
  <body onload="getBaskets(document.getElementById('#min').value,document.getElementById('#max').value);">
    <input type="number" id="#min" value="1"/> - <input type="number" id= "#max" value="10"/>
    	<input type="button" value="Populate" onclick="getBaskets(document.getElementById('#min').value,document.getElementById('#max').value);"/>
    <hr />
    <div id="#AJAX">
		Populating form...
   	</div>
    <script type="text/javascript" src="./../../Static/JS/config.js"></script>
    <script type='text/javascript' src='./dynamicNetworking.js'></script>
  </body>
</html>
