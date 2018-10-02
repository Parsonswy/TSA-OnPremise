<?php
/*
	Display transaction session with item list, prices, etc (Like @ a store)
*/
session_start();
$genSource = ($_SESSION["DISP_deviceID"] > -1)? $_SESSION["DISP_deviceID"] : -1; //0 - + are device ids, negative (-1) means generate device selection prompt
$genTitle = "Transaction Mirror - " . ($genSource != -1)? $genSource : "Setup"; //TODO: works?
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?php $genTitle ?></title>
		<meta charset="utf-8"/>
		<!--<meta name="viewport" content="width=, initial-scale=1.0"/>-->
		<style type="text/css">
			html{
				background-color: #000033;
			}
		</style>
	</head>
	<body>
		<style type="text/css">			
			div.wrapper_DeviceIdPrompt{
				margin:auto;
				text-align:center;
			}
			
			input.deviceId{
				width:200px;
				height:30px;
				
				border:none;
				border-radius:4px;
				
				background-color:#F8F8F8;
				
				font-size:25px;
				text-align:center;
				padding-top:2px;
			}
			
			input.submitDeviceId{
				width:200px;
				height:30px;
				margin-top:4px;
				margin-left:-4px;
				
				border:none;
				border-radius:4px;
				
				background-color:#00b300;
				color:white;
				font-size:20px;
				cursor:pointer;
			}
		</style>
		<!--Prompt for device id that transactions are to be monitored for-->
		<div id="wrapper_DeviceIdPrompt" style="display:none;">
			<input id="wrapper_DeviceIdPrompt" class="deviceId" type="number" name="deviceId" value="" placeholder="  Device ID" required="required"/>
			<br/>
			<input class="submitDeviceId" type="button" name="submitDeviceId" value="Start Mirror"/>
			<script type="text/javascript">
				document.getElementById("wrapper_DeviceIdPrompt").focus();
			</script>
		</div>
		
		<!--Current Transaction Display-->
		<div id="wrapper_DeviceTransactionDisplay" style="display:none;">
		
		</div>
		<script type="text/javascript">
			console.log("[remoteDisplay.php] Loading AJAX Client");
			
			var deviceIdPrompt = document.getElementById("wrapper_DeviceIdPrompt");
			var deviceTransactionDisplay = document.getElementById("wrapper_DeviceTransactionDisplay");
			
			//
			//Show prompt for device ID
			//
			function showDeviceIdPrompt(){
				deviceIdPrompt.style.display = "inline";
			}
			
			//
			//Hide prompt for device ID
			//
			function hideDeviceIdPrompt(){
				deviceIdPrompt.style.display = "none";
			}
			
			//
			//Show current device transaction info
			//
			function showDeviceTransactionDisplay(){
				deviceTransactionDisplay.style.display = "inline";
			}
			
			//
			//Hide current device transaction info
			//
			function hideDeviceTransactionDisplay(){
				deviceTransactionDisplay.style.display = "none";
			}
			
			//
			//Refresh current transaction information for device(n)
			//
			function getDeviceTransactionInfo(var n){
				if(httpCon typeof null){
					var httpCon = new XMLHttpRequest();
					httpCon.onreadystatechange = processDeviceTransactionRequest(httpCon);
				}
				httpCon.open("GET", "TODO:FILE", true);//TODO:FILE
				httpCon.send();
			}
			
			//
			//Handle XMLHttp response for device transaction info request
			//
			function processDeviceTransactionRequest(var httpCon){
				if(httpCon.readyState == 4 && httpCon.status == 200){
					var transInfoResponse = JSON.parse(httpCon.responseText);
					
					//TODO:Processing
				}
			}			
		</script>
	</body>
</html>