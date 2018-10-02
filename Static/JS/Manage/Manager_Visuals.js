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

  errorIMG.src="./Static/IMG/" + img;

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
  console.log("[Admin] " + message);
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

navigation = document.getElementById("navigation");
//
//  JSON Config to set <navigation> content with <div.link>
//
function setNavigation(navCFG){
  if(navCFG == "")//clear request
    navigation.innerHTML = "";

  navigationHTML = "";
  for(i=0; i<navCFG.length; i++){

    if(navCFG[i].length == 0)//No Access pages come through as empty JSON objects
      continue;

    navigationHTML += "<div class='link' style='color:" + navCFG[i]["color"] + "' onclick='" + navCFG[i]["loader"] + "'>" + navCFG[i]["display"] + "</div>";
  }
  navigation.innerHTML = navigationHTML;
}

//
//  Permissions Mnaager Functions
//
function permManage_setClassList(class_list){
  setContent(page_permManagerSelect);
  class_select = document.getElementById("manage_perm_groups");
  permDisplay = document.getElementById("manage_permissions");

  class_list.forEach(function(class_param){
    var option = document.createElement("option");
    option.text = class_param["name"];
    option.value = class_param["id"];
    class_select.add(option)
  });
}

//
//  Permissions Configuration Display
//
function permManage_displayEditor(display){
  var keys = Object.keys(display);
  var permSections = [];
  for(i=0; i<keys.length; i++){//Loop through and seperate meta from permissions sectons
    if(typeof display[keys[i]] == "object"){
      permSections[keys[i]] = display[keys[i]];//Copy over JSON formatting for permissions section
    }
  }

  var output = "<section class='fullblock'>\
                  <h3 class='manage_perm_section'>Class Configuration<h3/>\
                  <div style='float:left; margin-left:5px;'>\
                    Class Name: <input type='text' name='name' value='" + display.name + "'/>\
                    <br/></br>\
                    Class Enabled: <input type='checkbox' name='isEnabled'" + (display.isEnabled ? "checked" : "") + "/>\
                  </div>\
                  <div style='float:right; margin-right:5px;'>\
                    Class Description <br/>\
                    <textarea name='description' rows='3' cols='50'>" + display.description + "</textarea>\
                  </div>\
                </section>";
  var sectionNames = Object.keys(permSections);
  for(i=0; i<sectionNames.length; i++){//Loop through sections
    output += "<section class='fullblock'><h3>" + sectionNames[i] + "</h3><table><tr>";
    var nodeNames = Object.keys(permSections[sectionNames[i]]);
    for(j=0; j<nodeNames.length; j++){//Loop through nodes in section
      output += "<td class='node'>" + nodeNames[j].substr(sectionNames[i].length+1) + "<br/> <input type='checkbox' name='" + nodeNames[j] + "'" + (permSections[sectionNames[i]][nodeNames[j]] == 1 ? "checked" : "") + "/></td>";
    }
    output += "</tr></table></section>";
  }
  //permManage_setClassList
  permDisplay.innerHTML = output;
}

//
//  Operator Configuration Display
//
function opManage_displayEditor(data){
  setContent(page_opManageHeader);
  var output = "";
  data["users"].forEach(function(i){
    output += "<h3 style='text-align:left;'>" + i["display_name"] + "</h3>";
    output += "<div class='operator'>";
    output += "<span>Username:&nbsp;<input type='text' name='" + i["id"] + "_username' value='" + i["username"] + "'/></span>";
    output += "<span>New Password:&nbsp;<input type='password' name='" + i["id"] + "_secret' value=''/></span>";
    output += "<span>Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='" + i["id"] + "_display_name' value='" + i["display_name"] + "'/></span>";
    output += "<span>Permissions Group:&nbsp";
    output += "<select name='" + i["id"] + "_permission_level'>";
      data["permClasses"].forEach(function(perm_class){
        output += "<option value='" + perm_class["id"] + "' " + ((i["permission_level"] == perm_class["id"])? "selected" : "") + ">" + perm_class["name"] + "</option>";
      });
    output += "</select></span></div><br/>";
  });
  output += "<input type='hidden' name='operators' value='" + data["operators"] + "'/>";
  document.getElementById("manage_operators").innerHTML = output;
}
