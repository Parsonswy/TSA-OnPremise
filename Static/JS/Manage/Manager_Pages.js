page_login = "<div id='login_wrapper'>\
<span id='login_message'></span> <br/>\
Operator:<br/>  <input type='text' id='login_username' value='' placeholder='Username' /><br/> \
Password:<br/>  <input type='password' id='login_password' value='' placeholder='Pin / Password'/><br/>\
<input type='button' id='login_submit' onclick='sendLogin();' value='login'/><br/>\
</div>";

page_permManagerSelect = "<form id='permCFGForm' method='POST' autocomplete='off' enctype='multipart/form-data'>\
<div class='manage_permissions'>\
<!--Heading-->\
<div style='text-align:center;'>\
<h2 style='display:inline;' id='manage_perm_group'>Select Permissions Group: </h2>\
<select class='manage_perm_groups' id='manage_perm_groups' name='perm_groups' onchange='loadClassPermissions();'>\
<option name='blank' value=''>-----</option>\
</select> &nbsp;&nbsp;\
<input class='manage_perm_groups_save' type='button' name='save' value='Save Permissions' onclick='sendInterceptedForm(this.form, 4102);'/>\
</div><!--End Heading-->\
<div id='manage_permissions' class='verticalFlexCol'></div></form>";

page_opManageHeader = "<form id='Operator_Configuration'>\
<div style='text-align:center;'>\
<h2>Operator Setup & Configuration</h2>\
<input type='button' value='Save Operator Settings' onclick='sendInterceptedForm(this.form,4111);'/>\
</div>\
<div class='verticalFlexCol'>\
<section class='fullblock' id='manage_operators'></section></div></form>";
