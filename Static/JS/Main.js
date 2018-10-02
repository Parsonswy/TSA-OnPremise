var contWrap = document.getElementById("contentBox");//Reference to surrounding div of content viewer
var proc = document.getElementById("contentProc");   //Reference to processing gif
var errorBox = document.getElementById("errorDisp");
var errorMessage = document.getElementById("errorMSG");
var processing = false;
var dispError = false;
var DOC_ROOT = "http://192.168.1.249/Rebuild";
var username = document.getElementById("userName").value;
var uuid = document.getElementById("uuid").value;
document.addEventListener("keydown", forceCycleProcessing);
function initPage(){
	contWrap.display="none";
	proc.style.display = "inline";
	render_globalLeftNav();//Render navBar
	var fa;
	if(fa = urlVarParse("fa")){//Check for "firstAction" component.
		console.log("[TSA]Directing to " + fa);
		initWithFirstAction(fa);
	}else{
		initWithFirstPage();
	}
}

function initWithFirstAction(fa){
	var param = urlVarParse("param");//Get function parameters from url
	window[fa](param);//Execute function with provided param
}

function initWithFirstPage(){
	var firstURL = (document.getElementById("firstURL").value != "")? document.getElementById("firstURL").value : DOC_ROOT + "/Apps/Render/userAccountSelector.php";
	pageLoader(firstURL);
}
//Controls show/hide of processing animation and content viewer
function cycleProcessing(){
	if(!processing){//Loading animation showing
		TweenLite.to(contWrap, 1,{opacity:0});
		TweenLite.to(proc, 1 ,{opacity:0.8});
		proc.style.zIndex = 100;
		processing = true;
	}else{//Loading animation not showing
		TweenLite.to(contWrap, 1,{opacity:1});
		TweenLite.to(proc, 0.5 ,{opacity:0});
		proc.style.zIndex = -100;
		processing = false;
	}
}

//Re-show page if transition errors out (CTRL+R)
function forceCycleProcessing(e){
	if(e.keyCode == 82 && e.altKey){
		cycleProcessing();
	}
}

//Handles display/hide of error message box
//If no error being displayed, display error
//else if error displayed, concat to current showing error
//Print to console for further analyze if needed
function compiledDisplayError(combined){
	displayError(combined.message, combined.time);
}
function displayError(message, time){
	console.log("[ERROR]" + message);
	if(dispError == false){//Show box with error if not already out showing error
		dispError = true;
		errorMessage.innerHTML = message;
		errorBox.style.display = "inline";
		TweenLite.to(errorBox,1.5,{right:-10})

		setTimeout(function(){
			TweenLite.to(errorBox,1.5,{right:-440});
			errorBox.style.display = "none";
			dispError = false;
		}, time);
	}else{//If already displaying error, add new error to display in text box.
		//TODO: increase time to dispaly error box if adding new error
		errorMessage.innerHTML += "<br/>" + message;
	}
}

//Retreive specified url parameters from url
function urlVarParse(val) {
    var result = "";
    var tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
    }
    return result;
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
