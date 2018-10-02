function renderTransactionTableItems(param){
  var dispHTML = "<h1 class='com_tableHeading'>" + username + "</h1>  <hr/>";
  for(i=0;i<param.length;i++){
    if(!param[i]["ACTIVE"])//Don't display if game is not set to active
      continue;
    dispHTML += "<div class='com_tableItem' id='com_tableItem" + i + "'>";
    dispHTML += "<h2 class='com_tableItemHeading'>";//Heading in game box
    dispHTML += param[i]["NAME"] + "</h2>";
    dispHTML += "<select class='com_tableItemSelecter' id='com_tI_" + i + "' name='com_tI_" + i + "' onclick='com_tIS_toggle(this);r_TTI_total();'>";
    dispHTML += "<option value='0'>------</option>"
    for(j=0;j<param[i]["OPTS"].length;j++){//Options in dropdown menu
      dispHTML+="<option value='" + param[i]["OPTS"][j]["VALUE"] + "'>" + param[i]["OPTS"][j]["DESC"] + "</option>"
    }
    dispHTML += "</select></div>";
  }
  dispHTML += "<div class='com_tableItem' id='com_tableItem_" + i + "'>";
  dispHTML += "<h2 class='com_tableItemHeading'>Custom Charge</h2>";//Heading in game box
  dispHTML += "<input type='number' class='com_tI_CSTM' id='com_tI_CSTM' value='' placeholder='0.00' onkeyup='com_tIS_toggleCSTM(this);r_TTI_total();'/>";
  dispHTML += "<input type='text' class='com_tI_CSTM' id='com_tI_CSTM_DESC' value='' placeholder='Description of Charge'/>";
  dispHTML += "</div><hr/>";
  dispHTML += "<div align='center'><span id='r_t_cost'>$0</span><br/><br/>";
  dispHTML += "<input type='button' name='r_t_charge' id='r_t_charge' value='Charge to Account' onclick='com_tTI_proc();'/></div>";
  r_TransactionTableItems = param.length;
  return dispHTML;
}

//render_TransactionTableItems_Total (Sums them for total to show on screen)
function r_TTI_total(){
	var total = 0;
	for(i=0;i<r_TransactionTableItems;i++){
		total += parseFloat(document.getElementById("com_tI_" + i).value);
	}
	total += (document.getElementById("com_tI_CSTM").value > 0)? parseFloat(document.getElementById("com_tI_CSTM").value) : "";
	document.getElementById("r_t_cost").innerHTML = "$" + total;
	return total;
}

function com_tIS_toggle(ref){
  if(ref.options.selectedIndex == 0){
    ref.style.backgroundColor = "#B9B9B9";
  }else{
    ref.style.backgroundColor = "White";
  }
}

function com_tIS_toggleCSTM(ref){
  if(ref.value == ""){
    ref.style.backgroundColor = "#B9B9B9";
    document.getElementById("com_tI_CSTM_DESC").style.backgroundColor = "#B9B9B9";
  }else{
    ref.style.backgroundColor = "White";
    document.getElementById("com_tI_CSTM_DESC").style.backgroundColor = "White";
  }
}

//Format data in fields for netsend
function com_tTI_proc(){
	var total = "";
	for(i=0; i<r_TransactionTableItems; i++){//x,y,z,a,b,c ($'s)
		total += document.getElementById("com_tI_" + i).value + ",";
	}
	total += document.getElementById("com_tI_CSTM").value;
	var totalDesc = document.getElementById("com_tI_CSTM_DESC").value;
	if(totalDesc.length < 1)
      totalDesc = "Custom Charge";
  
	if(!confirm("Charge " + getCookie("CURRUD") + " $" + r_TTI_total() + "?"))
		return;
	
	var postData = "param=" + total + "," + totalDesc;
	
	pageLoader(DOC_ROOT + "/Apps/Commerce/TransactionManager.php?action=2", postData);
}

function com_displayRecipt(param){
	var dispHTML = "<div id='com_reciptWrapper'><!-- JS Hidding / Showing -->" +
			"<div class='com_reciptMatt'></div>" +
			"<div class='com_recipt'><div class='com_rHeader'>" +
			"<table class='com_rH_data'><tr><td>Transaction ID:</td>" +
			"<td>" + param.transID + "</td>" + 
				"</tr><tr>" + 
					"<td>Customer:</td>" +
					"<td>" + param.customer + "</td>" +	
				"</tr><tr>" +
					"<td>UUID:</td>" +
					"<td>" + param.transUUID + "</td>" +
				"</tr></table>" +
			"<img style='display:inline;float:right;' width='110px' height='90px' src='http://172.16.0.254/Rebuild/Static/IMG/TSA_Logo.png'/>" +
		"</div><hr/><br/><div class='com_reciptBody'>" +
			"<table cellpadding='5px' class='com_rB_data'>";
	//Itemized List
	for(i=0;i<param.transDesc.length;i++){
		dispHTML += "<tr><td class='com_rB_dataTDesc'>" + param.transDesc[i].desc + "</td>";
		dispHTML += "<td class='com_rB_dataTPrice'>" + param.transDesc[i].price + "</td></tr>";
	}
					
		dispHTML += "<tr><td class='com_rB_dataTTotal'>Total:</td>" + 
			"<td class='com_rB_dataTPrice'><b>$" + param.transPrice + "</b></td></tr></table><hr/>" +
			"<i>Proccessed By:" + param.transOP + "</i> </br>" + param.time + "</div></div></div>";
	return dispHTML;
}
