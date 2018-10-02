<!DOCTYPE html>
<html>
	<head>
     <link rel="stylesheet" href="styles.css" type="text/css" />	 
	 <title>Auction Program Home</title>
          <style>
           html{
		  background-color:	#A3A3C2;
		  }
		  
		  div.containter{
		  position:relative;
		  width:100%;
		  margin-top: 8%;
		  }
		  
		  div.check{
		  position:absolute;
		  left: 200px;
		  top: 140px;
		  background-color:	#4D94FF;
		  height: 100px;
		  width:  175px;
		  text-align:center;
		  padding-top:10px;
		  border: dashed 2px green; 
		  font-size: 20px;
		  }
		  
		  div.charge{
		  position: absolute;
		  right: 200px;
		  top: 140px;
		  background-color:	#4D94FF;
		  height: 100px;
		  width:  175px;
		  text-align:center;
		  padding-top: 10px;
		  border: dashed 2px green;
		  font-size: 20px;
		  }
		 
		  div.Account_New{
		  position: absolute;
		  left: 200px;
		  top: 300px; 
		  background-color:	#4D94FF;
		  height: 100px;
		  width:  175px;
		  text-align:center;
		  padding-top: 10px;
		  border: dashed 2px green;
		  font-size: 20px;
		  }

		  div.chat{
		  position: absolute;
		  right: 200px;
		  top: 300px;
		  background-color:	#4D94FF;
		  height: 100px;
		  width:  175px;
		  text-align:center;
		  padding-top: 10px;
		  border: dashed 2px green;
		  font-size: 20px;
		  }
		  
	
          </style>
	</head>
	
	<body>
		 <div class="menu">
       <p class="links">
        <a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="cash_out.php">Cash Out</a> <a href="New_Account.php">New Account </a> <a href='./'>Games</a>
       </p>
  </div>
  
  <div class="Containter">
	  
	  <a href="./wheel.php">
	  <div class="check" size='5'>
		<font color="black">Wheel</br> of </br> Joy</font>
	  </div>
	  </a>
	  
	  <a href="./apple.php">
	  <div class="charge">
		<font color="black" size='5'> Apple iPad </br> Mini </br> Heads or Tails</font>
	  </div>
	  </a>
	  
	  <a href="./mania.php" size='5'>
	  <div class="Account_New">
		<font color="black">Mystery </br> Mania</font>
	  </div>
	  </a>
	  
	  <a href='./colors.php' size='5'>
	  <div class='chat'>
		<font color='black'>Red, White </br> & </br> Blue </br> Raffle</font>
	  </div
	  </a>
  </div>

	</body>
</html>