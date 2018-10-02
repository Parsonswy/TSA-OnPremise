
/*
Get information for basket(s) from low-high (inclusive)

return as html for direct output from server
*/
function getBaskets(low, high){
  var xHttpReq = new XMLHttpRequest();
  xHttpReq.onreadystatechange = function(){
	    document.getElementById("#AJAX").innerHTML = xHttpReq.responseText;
	    console.log("FIRED");
  }
  xHttpReq.open("POST","./BasketManager.php", false);
  xHttpReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xHttpReq.send("AJX=GET&low=" + low + "&high=" + high);
}

/*
Get user information for basket listings by uuid
------
return current balance / name as json object as received from server
*/
function getUserData(uuid){
  var xHttpReq = new XMLHttpRequest();
  xHttpReq.open("POST","./BasketManager.php", true);
  xHttpReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xHttpReq.send("action=#&uuid=" + uuid);
  xHttpReq.onreadystatechange = function(){
    return xHttpReq.responseText;
  }
}

/*
Delete basket (id)

No return
*/
function deleteBasket(id){
  var xHttpReq = new XMLHttpRequest();
  xHttpReq = new XMLHttpRequest();
  xHttpReq.open("POST","./BasketManager.php", true);
  xHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xHttpReq.send("DEL=true");
}

function uuidIsValid(uuid){
  var xHttpReq = new XMLHttpRequest();
  xHttpReq.open("POST", endpoint, true);
  xHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xHttpReq.send("uuid=" + uuid);
  if(xHttpReq.readyState == 4 && xHttpReq.status == 200){
    if(xHttpReq.responseText == -1)
      return false;//Account DNE
    return true;//Account exists
  }
}
