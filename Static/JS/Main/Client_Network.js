//
//  Communiation to server. Dispatches respones acordingly
//See response codes for statuses / error or sucess instead of complex JSON bs
function netSend(loc, method, data, callback, contentType){
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function(){
    if(xmlHttp.readyState == 4 && xmlHttp.status == 200){//see response Codes
      if(callback.length >= 1){//No currently implemented, consider removal
        window[callback](data);
        return;
      }

      tsastatus = xmlHttp.getResponseHeader("tsastatus");
      console.log({"tsaresponse":tsastatus, "httptext":xmlHttp.responseText});

      if(tsastatus == 302){
        loadPage(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 400){//No login
        loadLogin(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 404){
        displayError(xmlHttp.responseText);
        return;
      }

      if(tsastatus == 500){//Error
        var response = JSON.parse(xmlHttp.responseText);
        displayError(response["type"], response["message"], response["time"]);
        return;
      }

      if(tsastatus == 1000){//Ok. Direct HTML response
        setContent(xmlHttp.responseText);
        return;
      }

      if(tsastatus == 1001){//OK. Direct html response
        appendContent(xmlHttp.responseText);
        return;
      }

      if(tsastatus == 1002 || tsastatus == 1003){//OK. JSON to create HTML response
        processContent(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 1004){//OK. Direct html response
        setContent(xmlHttp.responseText);
        return;
      }

      if(tsastatus == 2000){//load navigation
        setNavigation(JSON.parse(xmlHttp.responseText));
        if(action > 0 && action < 4000)
          netSend(endpoint + "?action=" + action, "POST", "data=" + urlVarParse("data"), "", 1);
        return;
      }

      if(tsastatus == 2001){//loadhomepage
        return;
      }

      if(tsastatus == 2002){//load account directory
        setContent(page_userAccountSelector);
        if(xmlHttp.responseText.length > 4)//if there are search results
          vis_displayUserAccountSelectorResults(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 2002.1){//search (results) account directory
        vis_displayUserAccountSelectorResults(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 2003){//create account cfg response
        vis_loadAccountCreationPage(JSON.parse(xmlHttp.responseText));
        init_accountCreatePage();
        return;
      }

      if(tsastatus == 2004){//Load Account Summary
        vis_loadUserAccountSummary(JSON.parse(xmlHttp.responseText));
        tsa_popup.display = "none";
        return;
      }

      if(tsastatus == 2005){//Load Storefront
        vis_loadStorefront(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 2102){//Receive cart configuration from server if trans session has line items
        return;
      }

      if(tsastatus == 2103){//display receipt
        vis_loadRecipt(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(tsastatus == 2006){//cashout?
        vis_loadUserAccountSummary(JSON.parse(xmlHttp.responseText));
        var col = document.getElementById("uas_col2");
        var button = "<br/><div style='margin:auto;height:40px;' class='receipt_button' id='receipt_submit' onclick='netSend(endpoint + \"?action=2006.1\", \"GET\", \"\",\"\", 0);'>\
                        Cashout\
                      </div>\
                      <br/>\
                      <font>Please review all transactions and click 'Cashout' to close the account.</font>";
        col.innerHTML += button;
        return;
      }

      if(tsastatus == 2006.1){//cashout.

      }

      if(tsastatus == 2101){
        data = JSON.parse(xmlHttp.responseText);
        document.getElementById("store_trans_total").value= data.total;
        return;
      }

      if(tsastatus == 2101.1){
        data = JSON.parse(xmlHttp.responseText);
        elm = document.getElementById("store_trans_total");
        elm.value = parseInt(elm.value) + parseInt(data.total);
        return;
      }

      displayError(0, "Received unexpected response from the server. Aborting.", 8000);
      console.log({"tsaresponse":tsastatus, "httptext":xmlHttp.responseText});
    }
  }

  xmlHttp.open(method, loc, true);
  switch(contentType){
    case 1:
      xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    break; case 2:
      xmlHttp.setRequestHeader("Content-type", "multipart/form-data");
    break; default:
      //do nothing, implicit
    break;
  }
  xmlHttp.send(data);
}

//
//  Action for 302 redirect
//
function loadPage(param){
  window.location = param.url;
  return;
}

//
//  Action for 400 unauth response
//
function loadLogin(loginMessage){
  setNavigation("");
  setContent(page_login);
  //Fill in credentials if returning from incorrect login info & fill in server message
  username = typeof loginMessage["username"] == "undefined"? "" : loginMessage["username"];
  password = typeof loginMessage["password"] == "undefined" ? "" : loginMessage["password"];
  document.getElementById("login_username").value = username;
  document.getElementById("login_password").value = password;
  document.getElementById("login_message").innerHTML = loginMessage["message"];
}

//'Intercept' form submission and send as POST to server
function sendInterceptedForm(form, dataCode){
  var formData = new FormData(form);
  netSend(endpoint + "?action=" + dataCode, "POST", formData, "", 2);
  return false;
}

function sendAccountCreateForm(){
  var formData = new FormData(document.getElementById("createNewAccount"));
  if(document.getElementById("photoIDEnabled").style.display == "inline"){
    var profileIDIMG = document.getElementById("image").toDataURL();//base64
    formData.append("profileIDIMG", profileIDIMG);
  }
  netSend(endpoint + "?action=2003.1", "POST", formData, "", 0);
}

//Send login request to server as post
function sendLogin(){
  username = document.getElementById("login_username").value;
  password = document.getElementById("login_password").value;
  if(!username.length > 0){alert("Please enter a username."); return;}
  if(!password.length > 0){alert("Please enter a password."); return;}
  netSend("./Apps/Operator/operatorLogin.php", "POST", "username=" + username + "&password=" + password, "", 1);
}

function sendTransSessionUpdate(item){
  var itemRef = document.getElementById(item);
  console.log(item);
  console.log(itemRef);
  var value = itemRef.value;
  netSend(endpoint + "?action=2101", "POST", "item=" + item + "&value=" + value, "", 1);
}

//
//  Translation functions (Navigation Buttons)
//    Turn readable onclicks into network calls
function loadHome(){
  netSend(endpoint + "?action=2001", "GET", "", "", 0);
}

function loadAccountCreate(){
  netSend(endpoint + "?action=2103", "GET", "","", 0);
}

function loadAccountSummary(){
  setContent("Loading...");
  netSend(endpoint + "?action=2004", "GET", "","", 0);
}

function loadStorefront(){
  netSend(endpoint + "?action=2005", "GET", "","", 0);
}

function loadReceipt(){
  netSend(endpoint + "?action=2006", "GET", "", "", 0);
}
