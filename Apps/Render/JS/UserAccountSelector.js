/////////////////////////////////////////////////////////////////////////////////////////
//userAccountSelector.php
//TODO: Make own style sheet and remove inline style attributes
//staticDisplay(); Display initial page searchbox
function renderUserAccountSelector(){
	console.log("[TSA](Render)userAccountSelector");
	var retString = "";
	var inputValue = urlVarParse("UUID");
	retString = "<div style='width:1000px;padding:10px;color:#FFF;'>\
					UUID: <input style='background-color:#EEE;width:947px;' type='text' id='search' value='" + inputValue + "' onkeyup=\"uAS_search();\"/>\
					<hr/>\
					<div id='results'></div>\
				</div>";
	setTimeout(uAS_search,100);//Sketchy way of searching when page loads (See bottom of Main.JS in future) TODO:
	return retString;
}

//Send query string for search
function uAS_search(){
	var q = document.getElementById('search').value;
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function(){
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			if(xmlHttp.responseText.length > 1){
				parsedResponse = JSON.parse(xmlHttp.responseText);
				document.getElementById('results').innerHTML = window[parsedResponse.action](parsedResponse.args);
			}else{
				document.getElementById('results').innerHTML = "";
			}
		}
	};
	xmlHttp.open('GET', DOC_ROOT + '/Apps/Render/userAccountSelector.php?q=' + q,true);
	xmlHttp.send();
}

//dynamicOutput(); Displays results from user lookup
function renderUserAccountSelectorResults(param){//{[primary:val,secondary:val,bal:val,btnText:val,btnColor:val]}
	console.log("[TSA](Render)userAccountSelectorResults");
	var retString = "";
	param.forEach(function(i){
		retString +='<div style="width:950px;height:50px;padding-top:5px;padding-left:5px;border:solid #777777 1px; font-size:24px;">\
						<table><tr>\
							<td style="font-size:30px;color:#ffb366;">\
								' + i.primary + '\
							</td>\
							<td style="font-size:14px;padding-left:5px;">\
								' + i.secondary + '<br />\
								' + i.bal + '\
							</td>\
						</tr>\
						</table>\
						<input type="button" style="background-color:#282828;text-align:center;padding-top:5px;float:right;border:groove 1px #FFF;width:100px;height:40px;position:relative;top:-41px;right:5px;text-decoration:none;font-size:17px;color:' + i.btnColor + ';"\
						   value="' + i.btnText + '" onclick="' + i.btnLink + '">\
						</input>\
					</div>';
	});
	return retString;
}

//render_accountOpen
//Probably the worst bit of HTML in the whole program. TODO: Make much better
function render_aO_QueryFields(uuid){
	//Recipt stuff stolen from Transaction recipt stuff
	var dispHTML = "<div id='com_openAccountWrapper'><!-- JS Hidding / Showing -->" +
	"<div class='com_reciptMatt'></div>" +
	"<div class='r_aO_queryFields'>" +
	"<div class='r_aO_QF_header'>Open New Account</div><div class='r_aO_QF_hClose' onclick='r_aO_qF_close(this)'>X</div><hr/>" +
	"<div class='r_aO_qF_left'>" +
	"<font size='4'>UUID:</font><input type='number' class='r_aO_QF_text' id='r_aO_QF_UUID' value='" + uuid + "' placeholder='UUID'/><br/><br/>" +
	"<font size='4'>Entry:</font><input type='number' style='width:40px;' class='r_aO_QF_text' id='r_aO_QF_groupSize' value='1' placeholder='Number of Party Members'/>Entry Fee?:<input type='checkbox' id='r_aO_QF_chargeEntry' checked/><br/><br/></div>" +
	"<div class='r_aO_qF_right'><font size='4'>Name:</font><input type='text' class='r_aO_QF_text' id='r_aO_qF_Name' value='' placeholder='Customer Name' onkeyup='r_aO_qF_rSearch(this)'/><br/><hr/>" +
	"<div id='r_aO_qF_results'></div></div>" +
	"<input type='button' id='r_aO_QF_submit' onclick='r_aO_proccess();' value='Create Account'/>" +
	"</div>";
	
	contWrap.innerHTML += dispHTML;
}

function r_aO_qF_close(){
	document.getElementById("com_openAccountWrapper").remove();
	console.log("Bye Bye");
}

function r_aO_qF_rSearch(elem){
	var xHttpReq = new XMLHttpRequest();
	var user = elem.value;
	xHttpReq.onreadystatechange = function(){
		document.getElementById("r_aO_qF_results").innerHTML = xHttpReq.responseText;
		console.log("returned");
	}
	xHttpReq.open("GET",DOC_ROOT + "/Apps/Render/userAccountCreatorAutofill.php?q=" + user, true);
	xHttpReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xHttpReq.send();
}

//render_accountOpen_queryField_resltEntry
function r_aO_qF_rSelectEntry(elem){
	document.getElementById("r_aO_qF_Name").value = elem.innerHTML;
}

function r_aO_proccess(){
	var uuid = document.getElementById("r_aO_QF_UUID").value;
	var groupSize = document.getElementById("r_aO_QF_groupSize").value;
	var chargeEntry = document.getElementById("r_aO_QF_chargeEntry").checked;
	var name = document.getElementById("r_aO_qF_Name").value;
	
	if(!confirm("Create user account " + uuid + " for " + name + " and " + chargeEntry + " fee for " + groupSize))
		return;

	var postData = "uuid=" + uuid + "&name=" + name + "&entryFee=" + chargeEntry + "&entryGroup=" + groupSize;
	pageLoader(DOC_ROOT + "/Apps/Render/userAccountController.php?action=1",postData);
}