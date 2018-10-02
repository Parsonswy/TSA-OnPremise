<!DOCTYPE html>
<html>
	<head>
		<title>[TSA]Transaction Control Panel - Moderator Control Panel</title>
		<style type="text/css">
			html{
				background-color: #A3A3C2;
			}
			
			div.wapper{
				width:700px;
				height:auto;
				border-left:0.5px solid #EEE;
				border-right:0.5px solid #EEE;
				box-shadow: 0 9px 0px 0px white, 0 -9px 0px 0px white, 12px 0 15px -4px rgba(31, 73, 125, 0.8), -12px 0 15px -4px rgba(31, 73, 125, 0.8);
				margin-top:-8px;
				margin-bottom: 2px;
			}
			
			div.footer{
				width:700px;
				height:70px;
				background-color:#EEE;
				text-align:left;
				padding-right:4px;
			}
			
			div.bueton{
				text-align:center;
				padding-top:35px;
				border:4px dashed green;
				font-size:18px;
				font-family:arial;
				height:60px;
				width:160px;
				background-color:#CCCCCC;
				margin-top:5px;
				margin-right:5px;
				margin-bottom: 1px;
			}
			
			#info{
				display:inline;
				width:100px;
				background-color:#EEE;
				font-size:11px;
				margin-left: 3px;
			}
			
			p.dev{
				display:inline;
				float:right;
				font-size:9px;
			}
		</style>
	</head>
	<body>
		<center>
			<div class="wapper">
				<table>
					<tr>
						<td>
							<div class="bueton" <?php echo "onclick=\"javascript:void window.open('http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/accountManager.php','1426192500093','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;\">";?>
								Charge to Card
							</div>
							</a>
						</td><td>
							<div class="bueton" <?php echo "onclick=\"javascript:void window.open('http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/qrGateWay.php','1426192500093','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;\">";?>
								Select Account
							</div>
							</a>
						</td>
					</tr><tr>
						<td>
							<div class="bueton" <?php echo "onclick=\"javascript:void window.open('http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/transferAccount.php','1426192500093','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;\">";?>
								Transfer Account
							</div>
							</a>
						</td><td>
							<div class="bueton" <?php echo "onclick=\"javascript:void window.open('http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/cashOut.php','1426192500093','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;\">";?>
								Cash Out
							</div>
							</a>
						</td>
					</tr><tr>
						<td>
							<div class="bueton" <?php echo "onclick=\"javascript:void window.open('http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/sysStats.php','1426192500093','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;\">";?>
								Statistics
							</div>
						</td>
						<td>
							<div class="bueton" <?php echo "onclick=\"javascript:void window.open('http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/Basket/BasketManager.php','1426192500093','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;\">";?>
								Silent Auction
							</div>
						</td>
					</tr>
					<tr></tr>
					<tr></tr>
					<tr></tr>
				</table>
			</div>
			<div class="footer">
				<p id="#info">
					Loading Client Information
				</p>
				<p class="dev">
					TS Auction Transaction Logger<br/>
					Developed by Wyatt Parsons
				</p>
			</div>
		</center>
		<script type="text/javascript">
			function tabBasicReq(){
				xmlhttp=new XMLHttpRequest();
				xmlhttp.open("GET","./AJAX/tcheckQuery.php", false);
				xmlhttp.send();
				document.getElementById("#info").innerHTML = xmlhttp.responseText;
			}
			window.setInterval(function(){tabBasicReq()},3000);	
			tabBasicReq();
		</script>
	</body>
</html>