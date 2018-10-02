page_login = "<div id='login_wrapper'>\
<span id='login_message'></span> <br/>\
Operator:<br/>  <input type='text' id='login_username' value='' placeholder='Username' /><br/> \
Password:<br/>  <input type='password' id='login_password' value='' placeholder='Pin / Password'/><br/>\
<input type='button' id='login_submit' onclick='sendLogin();' value='login'/><br/>\
</div>";

page_userAccountSelector = "<div style='width:1000px;padding:10px;color:#FFF;'>\
UUID: <input style='background-color:#EEE;width:947px;' type='text' id='userAccountSelector' value='' onkeyup=''/>\
<hr/>\
<div id='results'></div>\
</div>";

page_userAccountCreate = "<section class='fullblock' style='text-align:center;'>\
<h2>Create New Account</h2>\
<hr/>\
<form id='createNewAccount'>\
<font>UUID:</font> <input type='text' name='uuid' value='' id='tsa_createAccount_uuid'/>\
<br/><br/>\
<font>Name:</font> <input type='text' name='name' value=''/>\
<br/><br/>\
<font>Pin: &nbsp; &nbsp;</font> <input type='password' name='pin' value=''/>\
<br/><br/>\
<font>Entrance Fee:</font> <input style='height:24px;width:40px;border:none;border-radius:2px;' type='number' name='entrance_fee' value='1' onclick='calc_entranceFee(this);'/>&nbsp;&nbsp;<span id='fee' style='color:#2BB05B;font-size:18px;'>$30</span></span>\
<br/><br/>\
<span id='photoIDEnabled' style='display:none'><font>Photo ID:</font><br/>\
<span onclick='createCameraPopup();'><canvas id='image' width='150px' height='150px'></canvas></span>\
<br/><br/>\
<font>Notes:</font><br/><textarea rows='4' cols='40' name='notes' style='resize:none;border-radius:2px;'></textarea>\
<br/><br/>\
<input type='button' name='submit' value='Create Account' onclick='sendAccountCreateForm();'/>\
</form>\
</section>\
<section id='cameraPopup' class='fullblock' style='display:none;z-index:200; width:300px; height:320px; position:fixed; top:10px; left:450px; overflow:hidden;'>\
<span style='float:right;color:white;' onclick='destroycameraPopup();'>X</span>\
<video width='300px' height='260px' id='cameraView' onclick='cameraTakePhoto();'></video>\
<span style='cursor:pointer; font-size:16px; color:white; font-weight:600;' onclick='cameraTakePhoto();'>Take Picture</span>\
</section>";

function page_userAccountSummery(cfg){
  function generateTransactionList(transactions){
    var retString = "";
    if(typeof transactions == "string")
      return "No Transactions";

    for(i=0;i<transactions.length;i++){
      retString += "<div class='uas_transaction'>";
      retString += "<span style='color:orange;'>[" + transactions[i]["id"] + "]</span>";
      retString += "<span style='color:white;'>" +
      genLI(transactions[i]["lineItems"]) + "</span>";
      retString += "<span style='float:right;color:green;'>$" + transactions[i]["price"] + "</span>";
      retString += "</div>";
    }
    return retString;
  }

  //TODO: WORKS?
  function genLI(li){
    retString = "";
    for(j=0;j<li.length;j++){
      retString += li[j]["desc"] + "</br>";
    }
    return retString;
  }

  function accountStatus(status){
    if(status == 1){
      retString = "<span style='color:red;margin:auto;width:65px;display:block;'>Locked</span>";
    }else if(status == 2){
      retString = "<span style='color:green;margin:auto;width:65px;display:block;'>Active</span>";
    }else if(status == 3){
      retString = "<span style='color:purple;margin:auto;width:65px;display:block;'>Closed</span>";
    }
    return retString;
  }

  function generateBasketList(){

  }

  var retString = "<div class='uas_col1'>\
  <div class='uas_col_header' style='float:left;background-color:#505050;' onclick='switchTransactionList(\"trans\");'>Products</div>\
  <div class='uas_col_header' style='float:right;' onclick='switchTransactionList(\"basket\");'>Baskets</div>\
  <hr/>\
  <span id='uas_transaction_list' style='display:inline;'> " + generateTransactionList(cfg["Transactions"]) + "</span>\
  <span id='uas_basket_list' style='display:none;'> " + generateBasketList(cfg["Baskets"]) + "</span>\
  </div>\
  <div class='uas_col2' id='uas_col2'>\
  <div id='uas_display_name' style='height:26px;color:white;font-size:20px;text-align:center;'>" + cfg["display_name"] + "</div>\
  <hr/>\
  <h3 style='text-align:center;'>" + cfg["uuid"] + "</h3>"
  + accountStatus(cfg["Status"]) +
  "<img src='" + address + "Apps/Accounts/Accounts/" + cfg["uuid"] + ".png' style='border:1px #DDD solid; margin-top: 5px; margin-bottom: 5px; margin:auto; display:block;'/>\
  <div class='uas_balance' id='uas_balance'>" + cfg["Balance"] + "</div>\
  <div class='uas_party'> </div>\
  </div>";

  return retString;
}
function page_storeFront_item_selCount(display, item, img, price){
  var retString =
    "<div class='store_item'><!--ItemStart-->\
    <div class='store_item_left'>\
    <img class='store_item_img' src='" + img + "' alt='item title'/>\
    <span style='font-size:14px;color:white;'>$" + price + " per item</span>\
    </div>\
    <div class='store_item_right'>\
    <h3>" + display + "</h3>\
    <div class='store_item_selCounter'>\
    <span style='color:red;' clicker='true' onclick='store_incrimentalSelector(" + item + ", -1)'>-</span>\
    <input type='number' name='"+ item + "_quantity' value='0' id='" + item + "'/>\
    <span style='color:green;' clicker='true' onclick='store_incrimentalSelector(" + item + ", +1)'>+</span>\
    </div>\
    </div>\
    </div><!---Item End-->";

  return retString;
}


function page_storeFront_item_selRadio(display, item, img, payLevels){
  function generatePayLevels(_name, cfg){
    var payLevelString = "<div><input type='radio' name='" + item + "_itemID' value='-1' id='item_" + _num + "_count' checked/><label for='item_" + _num + "_count'>None</label></div>";
    for(var i=0;i<cfg.length;i++){
      payLevelString += "<div><input type='radio' name='" + item + "_itemID' id='" + cfg[i]["id"] + "' value='" + cfg[i]["id"] + "'/><label for='" + cfg[i]["id"] + "'>" + cfg[i]["desc"] + "</label></div>";
    }
    return payLevelString;
  }
  var retString =
    "<div class='store_item'><!--ItemStart-->\
    <div class='store_item_left'>\
    <img class='store_item_img' src='" + img + "' alt='item title'/>\
    </div>\
    <div class='store_item_right'>\
    <h3>" + display + "</h3>\
    <div class='store_item_selRadio'>\
      " + generatePayLevels(item, payLevels) + "\
    </div>\
    </div>\
    </div><!---Item End-->";

  return retString;
}

function page_storeFront_item_selDrop(display, item, img, payLevels){
  function generatePayLevels(cfg){
    var payLevelString = "<option value='-1'>-----</option>";
    for(var i=0;i<cfg.length;i++){
      payLevelString += "<option value='" + cfg[i]["id"] + "'>" + cfg[i]["desc"] + "</option>";
    }
    return payLevelString;
  }
  var retString =
    "<div class='store_item'><!--ItemStart-->\
    <div class='store_item_left'>\
    <img class='store_item_img' src='" + img + "' alt='item title'/>\
    </div>\
    <div class='store_item_right'>\
    <h3>" + display + "</h3>\
    <div class='store_item_selDrop'>\
    <select name='" + item + "_itemID' id=" + item + " onchange='sendTransSessionUpdate(\"" + item + "\");'>\
      " + generatePayLevels(payLevels) + "\
    <select>\
    </div>\
    </div>\
    </div><!---Item End-->";

  return retString;
}

function page_storeFront_item_selCustom(display, item, img){
  var retString =
    "<div class='store_item'><!--ItemStart-->\
    <div class='store_item_left'>\
    <img class='store_item_img' src='" + img + "' alt='item title'/>\
    </div>\
    <div class='store_item_right'>\
    <h3>" + display + "</h3>\
    <div class='store_item_selCustom'>\
    <input type='text' name='" + item + "_price' id='" + item + "' placeholder='0.00' onblur='sendTransSessionUpdate(\"" + item + "\")'/>\
    </div>\
    </div>\
    </div><!---Item End-->";

  return retString;
}

function page_storeFront_recipt(cfg){
  function generateTableRow(desc, price){
    return "<tr>\
      <td>" + desc + "</td>\
      <td>$ " + price + " </td>\
    </tr>";
  }

  var retString =
  "<div class='receipt'>\
  <div class='recipt_header'>\
    <h3>Purchase Confirmation</h3>\
  </div><hr/>\
  <table><tr>\
    <th>Description</th>\
    <th>Price</th>\
  </tr>";

  var total = 0;
  for(i=0;i<cfg.length;i++){
    retString += generateTableRow(cfg[i]["desc"], cfg[i]["price"]);
    total += parseFloat(cfg[i]["price"]);
  }

  retString += generateTableRow("Total", total.toFixed(2));

  retString +=
  "</table>\
    <p>The account has not been changed. Please click the 'Charge Account' button once the customer approves the itemized list.</p>\
    <div style='float:left;' class='receipt_button' id='receipt_cancel' onclick='togglePopup();'>\
      Edit\
    </div>\
    <div style='float:right;' class='receipt_button' id='receipt_submit' onclick='netSend(endpoint + \"?action=2103\", \"GET\", \"\",\"\", 0);togglePopup();'>\
      Charge Account\
    </div>\
  </div>";

  return retString;
}
