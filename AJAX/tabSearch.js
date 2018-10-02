function search(){
  var xHttpReq = new XMLHttpRequest();
  user = document.getElementById("#name").value;
  xHttpReq.onreadystatechange = function(){document.getElementById("#suggestions").innerHTML = xHttpReq.responseText;console.log("returned");}
  xHttpReq.open("POST","./tabOpen.php?req=pop", true);
  xHttpReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xHttpReq.send("text=" + user);
}

function pushToText(text){
  document.getElementById("#user").value = text;
}