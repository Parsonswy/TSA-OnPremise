<?php
	if(@$_GET["dummy"] == 1){//Clearing HTTP_AUTH_* credidentials. Most browsers clear when they are rejected
		header('HTTP/1.0 401 Unauthorized');
		exit("Response ok");
	}
	if(!class_exists("CONFIG"))
		require("/var/www/html/Rebuild/Apps/Config/Config.php");
	if(!CONFIG::OPS_ENABLED)
		exit(CONFIG::OPS_ENABLED);
	
	$user = "Foobar";
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>TSA Operator Authentication</title>
	</head>
	<body>
		<script type="text/javascript">
			
		</script>
	</body>
</html>
<?php
/*
Send username password prompt to user
Check against list of $operators
if correct, register op session to database and exit clean
any failure, reprompt
*/
	if(!class_exists("Authentication"))
		require(CONFIG::DOC_ROOT . "/Apps/Authentication/Authentication.php");
	
	$Auth = new Authentication();//Check if user already logged in
	if($Auth->validateOperatorSession()){
		header('HTTP/1.0 200 Response Ok');
		//exit("Already Logged In!");
	}else{
		if($Auth->registerOPSession($user)){
		//echo "Logged in!";
		header('HTTP/1.0 200 Response Ok');
		//exit();
		}
	}
	
	/*header('WWW-Authenticate: Basic realm="TSA"');//Login prompt
	header('HTTP/1.0 401 Unauthorized');*/
	
	//Validate input
	/*if(!strlen($user = $_SERVER["PHP_AUTH_USER"]) >=3)
		$Auth->reportLoginFail(null);
	if(!strlen($pass = $_SERVER["PHP_AUTH_PW"]) >=8)
		$Auth->reportLoginFail($user);*/
	
	//Operator list
	$operators = array(array(
					"Wyatt",
					"Orange15"),
					array(
					"Fred",
					"0605Aeiou"),
					array(
					"Lesley",
					"0605Aeiou"),
					array(
					"Ryleigh",
					"Rybear2002")
					);
	//Check credidentials valid
	/*$valid = false;
	foreach($operators as $operator){
		if(array_search($user, $operator, true) !== false && array_search($pass, $operator, true) !== false){
			$valid = true;
			break;
		}
	}*/
	
	//Die and reprompt if not
	/*if(!$valid)
		$Auth->reportLoginFail($user);*/
	
	//exit("System Error");
?>