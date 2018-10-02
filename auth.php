<?php
	
?>
<!DOCTYPE html>
<html>
	<head>
		
	</head>
	<body>
		<div id="portal">
		
		</div>	
		<?php 
			echo "<script type='text/javascript'>
					xmlHttp = new XMLHttpRequest();
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.stats == 200)
							document.getElementById('portal').innerHTML = xmlHttp.responseText;
					}
					xmlHttp.open('GET', 'http://192.168.1.249/Rebuild/Apps/Authentication/OPLogin.php');
					xmlHttp.send();
				</script>";
		?>
		</body>
</html>