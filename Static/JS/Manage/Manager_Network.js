//
//  Communiation to server. Dispatches respones acordingly
//See response codes for statuses / error or sucess instead of complex JSON bs
function netSend(loc, method, data, callback){
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function(){
    if(xmlHttp.readyState == 4 && xmlHttp.status == 200){//see response Codes

      adminstatus = xmlHttp.getResponseHeader("adminstatus");
      console.log({"Admin Response":adminstatus, "httptext":xmlHttp.responseText});
      if(adminstatus == 302){
        loadPage(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(adminstatus == 400){//No login
        loadLogin(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(adminstatus == 404){
        displayError(xmlHttp.responseText);
        return;
      }

      if(adminstatus == 500){//Error
        var response = JSON.parse(xmlHttp.responseText);
        displayError(response["type"], response["message"], response["time"]);
        return;
      }

      if(adminstatus == 1000){//Ok. Direct HTML response
        setContent(xmlHttp.responseText);
        return;
      }

      if(adminstatus == 1001){//OK. Direct html response
        appendContent(xmlHttp.responseText);
        return;
      }

      if(adminstatus == 1004){//OK. Direct html response
        setContent(xmlHttp.responseText);
        return;
      }

      if(adminstatus == 2000){//load navigation
        setNavigation(JSON.parse(xmlHttp.responseText));
        loadHome();
        return;
      }

      if(adminstatus == 2001){
        setContent("<div style='color:white;text-align:center;font-size:18px; margin-top:10px;'>System Management and Administration.</div>");
        return;
      }

      if(adminstatus == 4100){
        permManage_setClassList(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(adminstatus == 4101){
        permManage_displayEditor(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(adminstatus == 4102){
        displayError(0, "Permissions Updated and Reloaded.", 8000);
        loadClassPermissions();//Refresh Editor
        return;
      }

      if(adminstatus == 4110){
        opManage_displayEditor(JSON.parse(xmlHttp.responseText));
        return;
      }

      if(adminstatus == 4111){
        displayError(0, "Operator Settings Updated and Reloaded", 8000);
        loadOperatorCFG();//Refresh editor
        return;
      }
      displayError(1, "Received unexpected response from the server. Aborting.", 8000);
      console.log({"adminstatus":adminstatus, "httptext":xmlHttp.responseText});
    }
  }

  xmlHttp.open(method, loc, true);
  xmlHttp.send(data);
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

//Send login request to server as post
function sendLogin(){
  username = document.getElementById("login_username").value;
  password = document.getElementById("login_password").value;
  if(!username.length > 0){alert("Please enter a username."); return;}
  if(!password.length > 0){alert("Please enter a password."); return;}
  netSend("./Apps/Operator/operatorLogin.php", "POST", "username=" + username + "&password=" + password, "");
}

//
//  Translation functions (Navigation Buttons)
//    Turn readable onclicks into network calls
function loadHome(){
  netSend(management + "?action=2001", "GET", "", "");
}

//Operator configuration / setup page
function loadOperatorCFG(){
  netSend(management + "?action=4110", "GET", "", "");
}

///////////////////////////////////////////////////////////////////////////////////////////////

//Permission configuration / setup page
function loadPermissionsCFG(){
  netSend(management + "?action=4100", "GET", "", "");
  perms_needSaved = false;//Tracking for 'are you sure' exit message / save message
}

function loadClassPermissions(){
  if(perms_needSaved)
    if(!alert("Are you sure you want to discard changes to this permission group? Click OK to DISCARD changes. Click CANCEL to stay on this page."))
      return;

  //Blank value selected, terminate
  if(class_select.value.length == 0)
    return;

  netSend(management + "?action=4101&perm_class=" + class_select.value, "GET", "","");//Manager_Visuals->permManage_setClassList()
}

//'Intercept' form submission and send as POST to server
function sendInterceptedForm(form, dataCode){
  var formData = new FormData(form);
  netSend(management + "?action=" + dataCode, "POST", formData, "");
}

//Statistics / System status / Tracking page
function loadStatistics(){
  netSend(management + "?action=5000", "GET", "", "");
}
