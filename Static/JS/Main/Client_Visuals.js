//
//  Create / Display error div of type(warn/crit) with message, for time(millis)
//
dispError = false;
errorBox = document.getElementById("errorDisplay");
errorMessage = document.getElementById("errorMSG");
errorImg = document.getElementById("errorIMG");
function displayError(type, message, time){
  img = "warning.png";
  if(type == 0)
    img = "info.png";

  errorImg.src="./Static/IMG/" + img;

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
  console.log("[TSA] " + message);
}

navigation = document.getElementById("navigation");
//
//  JSON Config to set <navigation> content with <div.link>
//
function setNavigation(navCFG){
  if(navCFG == "")//clear request
    navigation.innerHTML = "";

  navigationHTML = "";
  for(i=0; i<navCFG.length; i++){

    if(navCFG[i].length == 0)//No Access pages come through as empy JSON objects
      break;

    navigationHTML += "<div class='link' style='color:" + navCFG[i]["color"] + "' onclick='" + navCFG[i]["loader"] + "'>" + navCFG[i]["display"] + "</div>";
  }
  navigation.innerHTML = navigationHTML;
}

//
//  HTML to replace content innerHTML
//
function setContent(html){
  content.innerHTML = html;
}

//
//  HTML to append to content innerHTML
//
function appendContent(html){
  content.innerHTML += html;
}

//
//  Full Screen content cover
//
var cover = document.getElementById("contentCover");
function toggleCover(){
  if(cover.style.display == "none")
    cover.style.display = "inline";
  else
    cover.style.display = "none";
}

//
//  Emphasis popup
//
var tsa_popup = document.getElementById("tsa_popup");
function setPopupContent(html){
  tsa_popup.innerHTML = html;
}

function appendPopupContent(html){
  tsa_popup.innerHTML = html;
}

function togglePopup(){
  toggleCover();
  if(tsa_popup.style.display == "none")
    tsa_popup.style.display = "inline-block";
  else
    tsa_popup.style.display = "none";
}
/*************************************************
      App Functions
*************************************************/

function vis_loadAccountCreationPage(cfg){
  setContent(page_userAccountCreate);

  if(cfg["requirePhotoID"])
    document.getElementById("photoIDEnabled").style.display = "inline";

  if(cfg["requirePin"]){}
    ///adfasf

  document.getElementById("tsa_createAccount_uuid").value = cfg["uuid"];
}

//Account:photoID
function createCameraPopup(){
  toggleCover();
  document.getElementById("cameraPopup").style.display = "inline";
  init_cameraFunctions();
}

//Account:photoID
function destroycameraPopup(){
  try{cameraView.captureStream().getTracks()[0].stop();}//End camera stream
  catch(error){
    console.log("[TSA]Warning - Camera Stream Not CLosed. This is a known bug.");
  }
  document.getElementById("cameraPopup").style.display = "none";
  toggleCover();
}

function vis_displayUserAccountSelectorResults(results){

}

function vis_loadUserAccountSummary(cfg){
  setContent(page_userAccountSummery(cfg));
}

//switch between basket and transaction lists on UASP
function switchTransactionList(show){
  var transactions = document.getElementById("uas_transaction_list");
  var baskets = document.getElementById("uas_basket_list");
  if(show == "basket"){
    transactions.style.display = "none";
    baskets.style.display = "inline";
  }else{
    transactions.style.display = "inline";
    baskets.style.display = "none";
  }

}

//
//Store Page
//Load Storefront
function vis_loadStorefront(cfg){
  console.log(cfg);
  var setString = "\
    <div class='store'>\
    <h2 style='text-align:center;display:inline-block;'>" + cfg["client_name"] + " - " + cfg["client_uuid"] + "</h2> <input type='number' id='store_trans_total' value='0.00' style='float:right'/>\
    <hr/>\
    <div id='store_shelf'>\
    <button style='background-color:#ffb366;width:500px;height:30px;color:white;font-size:16px;' onclick='sendInterceptedForm(document.getElementById(\"tsa_store\"), 2102);'>Checkout</button>\
    <form id='tsa_store'>";
  for(var i=0;i<Object.keys(cfg).length;i++){
    try{
      if(cfg[i].constructor != Object){
        continue;
      }
    }catch(e){
      continue;
    }

    switch(cfg[i].payType){
      case "drop":
        setString += page_storeFront_item_selDrop(cfg[""+i]["display"], cfg[""+i]["item"], cfg[""+i]["img"], cfg[""+i]["payLevels"]);
      break;case "radio":
        setString += page_storeFront_item_selRadio(cfg[""+i]["display"], cfg[""+i]["item"], cfg[""+i]["img"], cfg[""+i]["payLevels"]);
      break;case "count":
        setString += page_storeFront_item_selCount(cfg[""+i]["display"], cfg[""+i]["item"], cfg[""+i]["img"], cfg[""+i]["price"]);
      break;case "custom":
        setString += page_storeFront_item_selCustom(cfg[""+i]["display"], cfg[""+i]["item"], cfg[""+i]["img"]);
      break;default:
        continue;
    }
    console.log(cfg[i]);
  }
  setString += "</form></div></div>";
  setContent(setString);
}

//Type:count -/+ item counter
function store_incrimentalSelector(targetID, num){
  var target = document.getElementById(targetID);
  target.value = parseInt(target.value) + num;
  if(target.value < 0 ){
    target.value = 0;
    return;
  }
  sendTransSessionUpdate(targetID);
}

//[obj.item, obj.item, obj.item]
function vis_loadRecipt(cfg){
  popup_html = page_storeFront_recipt(cfg);
  setPopupContent(popup_html);
  togglePopup();
}
