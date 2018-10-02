<?php
error_reporting (E_ALL ^ E_NOTICE);  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1250">
	<link rel="stylesheet" href="styles.css" type="text/css"/>
	<title> Account Creator </title>
  </head>
  <body>
	 <div class="menu">
       
       
       
       <p class="links">
			<a href="home.php">Home</a>  <a href="moneycheck.php">Account Balance</a> <a href="moneycharge.php">Charge Money</a> <a href="New_Account.php">New Account</a>
		</p>
	</div>
	<?php
		
		
		if($_POST['register_btn']){
			$get_user = $_POST['user'];
			$get_pass = $_POST['pin'];
			$get_retyped_pass = $_POST['retypepin'];
			$get_start_bal = $_POST['start_bal'];
			
			if($get_user){
				if($get_pass){
					if($get_retyped_pass){
						if($get_retyped_pass === $get_pass){
							$pin_ecy = md5($get_pass);
							require("./worker.php");
							
							$query = mysql_query("SELECT * FROM money WHERE User='$get_user'");
							$numrows = mysql_num_rows($query);
							if($numrows == 0){
								mysql_query("INSERT INTO money VALUES ( '$get_user', '$pin_ecy', '$get_start_bal', '', '')");
								
								$query = mysql_query("SELECT * FROM money WHERE User='$get_user'");
								$numrows = mysql_num_rows($query);
								if($numrows == 1){
									$success_msg = "The account was successfully created.";
								
								}
								else
									$error_msg = "An internal error has occurred. Your account was not created.";
							}
							else
								$error_msg = "The selected username is already in use please try again.";
						}
						else
							$error_msg = "The pins that you have entered do not match.";
					}
					else
						$error_msg = "Please retype your Pin.";
				}
				else
					$error_msg = "Please enter a pin.";
			}
			else
				$error_msg = "Please enter a username";
		}
		else
	
		
	
	echo $error_msg;
		
		
		$form = "<form action='./New_Account.php' method='POST'>  
		<table>
			<tr>
				<td></td>
				<td><font color='red'> $error_msg </font>$success_msg</td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input type='text' name='user' value='$get_user'/></td>
			</tr>
			<tr>
				<td>Pin</td>
				<td><input type='password' name='pin' value=''/></td>
			</tr>
			<tr> 
				<td>Retype Pin</td>
				<td><input type='password' name='retypepin' value=''/></td>
			</tr>
			<tr>
				<td>Starting Balance (opt)</td>
				<td><input type='text' placeholder='Leave Blank for 0 'name='start_bal' value=''/></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' name='register_btn' value='Register'/></td>
			</tr>
		</table>
		</form>";
	
	?>
	<center>
		</br></br></br>
			<div style="border: solid black 2px; background-color:#47A3FF; text-align:center; height: auto; width: 350px; padding: 20px;">
			
			<?php	
					echo $form;  
			?>
			</div>
	</center>
		
    <SCRIPT type="text/javascript">
/*
Snow Fall 1 - no images - Java Script
Visit http://rainbow.arch.scriptmania.com/scripts/
  for this script and many more
*/

// Set the number of snowflakes (more than 30 - 40 not recommended)
var snowmax=36

// Set the colors for the snow. Add as many colors as you like
var snowcolor=new Array("#aaaacc","#ddddff","#ccccdd","#f3f3f3","#f0ffff","#888888 ")

// Set the fonts, that create the snowflakes. Add as many fonts as you like
var snowtype=new Array("Times","Arial","Times","Verdana")

// Set the letter that creates your snowflake (recommended: * )
var snowletter="*"

// Set the speed of sinking (recommended values range from 0.3 to 2)
var sinkspeed=1

// Set the maximum-size of your snowflakes
var snowmaxsize=30

// Set the minimal-size of your snowflakes
var snowminsize=8

// Set the snowing-zone
// Set 1 for all-over-snowing, set 2 for left-side-snowing
// Set 3 for center-snowing, set 4 for right-side-snowing
var snowingzone=1

///////////////////////////////////////////////////////////////////////////
// CONFIGURATION ENDS HERE
///////////////////////////////////////////////////////////////////////////

// Do not edit below this line
var snow=new Array()
var marginbottom
var marginright
var timer
var i_snow=0
var x_mv=new Array();
var crds=new Array();
var lftrght=new Array();
var browserinfos=navigator.userAgent
var ie5=document.all&&document.getElementById&&!browserinfos.match(/Opera/)
var ns6=document.getElementById&&!document.all
var opera=browserinfos.match(/Opera/)
var browserok=ie5||ns6||opera

function randommaker(range) {
        rand=Math.floor(range*Math.random())
    return rand
}

function initsnow() {
        if (ie5 || opera) {
                marginbottom = document.body.scrollHeight
                marginright = document.body.clientWidth-15
        }
        else if (ns6) {
                marginbottom = document.body.scrollHeight
                marginright = window.innerWidth-15
        }
        var snowsizerange=snowmaxsize-snowminsize
        for (i=0;i<=snowmax;i++) {
                crds[i] = 0;
            lftrght[i] = Math.random()*15;
            x_mv[i] = 0.03 + Math.random()/10;
                snow[i]=document.getElementById("s"+i)
                snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)]
                snow[i].size=randommaker(snowsizerange)+snowminsize
                snow[i].style.fontSize=snow[i].size+'px';
                snow[i].style.color=snowcolor[randommaker(snowcolor.length)]
                snow[i].style.zIndex=1000
                snow[i].sink=sinkspeed*snow[i].size/5
                if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
                if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
                if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
                if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
                snow[i].posy=randommaker(2*marginbottom-marginbottom-2*snow[i].size)
                snow[i].style.left=snow[i].posx+'px';
                snow[i].style.top=snow[i].posy+'px';
        }
        movesnow()
}

function movesnow() {
        for (i=0;i<=snowmax;i++) {
                crds[i] += x_mv[i];
                snow[i].posy+=snow[i].sink
                snow[i].style.left=snow[i].posx+lftrght[i]*Math.sin(crds[i])+'px';
                snow[i].style.top=snow[i].posy+'px';

                if (snow[i].posy>=marginbottom-2*snow[i].size || parseInt(snow[i].style.left)>(marginright-3*lftrght[i])){
                        if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
                        if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
                        if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
                        if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
                        snow[i].posy=0
                }
        }
        var timer=setTimeout("movesnow()",50)
}

for (i=0;i<=snowmax;i++) {
        document.write("<span id='s"+i+"' style='position:absolute;top:-"+snowmaxsize+"'>"+snowletter+"</span>")
}
if (browserok) {
        window.onload=initsnow
}

</SCRIPT>
	
  
  </body>
  </html>
  