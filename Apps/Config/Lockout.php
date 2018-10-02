<?php
	$prevURL = @$_GET["prevURL"];
?>
<!DOCTYPE html>
<html>
	<head>
		<title>TSA Auction - System Lockout</title>
		<script type="text/javascript">

		</script>
		<style type="text/css">
			html{
				background-color:black;
			}
			div.wrapper{
				text-align:center;
				color:white;
				background-color:#282828;
				margin:auto;
				padding-auto;
				width:400px;
				height:600px;
			}
		</style>
	</head>
	<body>
		<div class="wrapper">
			<img src="./PCS.png"/>
			<p>System has been temporarly disabled.
			You will be redirected back to your previous page
			momentarly</p>
		</div>
		<input type="hidden" name="preURL" value="<?php echo $prevURL; ?>"/>
	</body>
</html>
