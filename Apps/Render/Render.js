///////////////////////////////////////////////////////////////////////////////////////////////////
//Authentication
function authLoader(){
//Clearing HTTP_AUTH_* credidentials. Should clear when request is denied
	displayError("Please login to access this page in the next 30 seconds.", 8000);
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {console.log("Authentication Complete");}
	}
	
	xmlhttp.open("GET", DOC_ROOT + '/Apps/Authentication/OPLogin.php?dummy=1', true);
	xmlhttp.send("");
//Login request
	var loggedIn = false;
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
			console.log("Logon Attempt Completed");
			loggedIn = true;
			uAS_search();
	}
	xmlhttp.open("GET", DOC_ROOT + "/Apps/Authentication/OPLogin.php",true);
	xmlhttp.send();
//Clearing
	setTimeout(function(){
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				if(!loggedIn)
					displayError("Authentication Session Timeout", 8000);				
			}
		}
		
		xmlhttp.open("GET", DOC_ROOT +  '/Apps/Authentication/OPLogin.php?dummy=1', true);
		xmlhttp.send("");
	},30000);
}

function render_globalLeftNav(){
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			
			param = JSON.parse(xmlhttp.responseText);
			
			var retString = "";
			for(i=0;i<param.length;i++){
				retString += "<div id='navLink' style='color:" + param[i].color + ";' " +
						"onclick=\"pageLoader(\'" + param[i].pgLink + "\');\">" +
						param[i].text + "</div>";
			}
			document.getElementById("navBar").innerHTML = retString;
		}
	}
	
	xmlhttp.open("GET", DOC_ROOT + '/Apps/Render/renderLeftNav.php', true);
	xmlhttp.send("");
}
