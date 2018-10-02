/*
  Main loader on TSA_AUCTION.php runs on first load to initialize "web app"
    Sales operator actions beyond that point are handled by _Networking and _Visuals
*/

//Load Navigation - Will also check for login
netSend(endpoint + "?action=2000", "GET", "","", 0);
var action = urlVarParse("for");


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

/***********************************
  Account Pages
**********************************/
//initialize components (moztly canvas/camera) things on account creation page
function init_accountCreatePage(){
	image = document.getElementById("image");
	imageCTX = image.getContext("2d");
	imageCTX.fillStyle = "#000";
	imageCTX.fillRect(0,0, 150, 150);

  imageCTX.fillStyle = "#FFF";
  imageCTX.font = "12px Arial";
  imageCTX.fillText("Click to", 53,45);
  imageCTX.fillText("Take Picture", 42,65);
}

//init camera
function init_cameraFunctions(){
  navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mzGetUserMedia || navigator.oGetUserMedia || navigator.msGetUserMedia;
	if(navigator.getUserMedia){
		navigator.getUserMedia({video:true}, init_cameraDisplayStream, cameraError);
	}
}

//init camera streamt to onscreen video
function init_cameraDisplayStream(stream){
  cameraView = document.getElementById("cameraView");
  cameraView.src = window.URL.createObjectURL(stream);
	cameraView.play();
}

//display error
function cameraError(error){
  console.log(error);
}

function cameraTakePhoto(){
  imageCTX.fillStyle = "#DDD";
	imageCTX.fillRect(0,0, 150, 150);
  imageCTX.drawImage(cameraView,0,0,150,150);
  destroycameraPopup();
}

function calc_entranceFee(elem){
	document.getElementById("fee").innerHTML = '$' + elem.value * 30;
}
