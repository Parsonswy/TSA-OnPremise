//render->uap.php structure reference
//Driver
function renderUserAccountPortal(data){
	r_uap_numTrans = 0;
	var userTranses = "";
	if(data.transes != "Error" && data.transes != null){
		r_uap_numTrans = data.transes.length;
		for(i=0;i<data.transes.length; i++){
			userTranses += r_uap_transActivity(data.transes[i]);
		}
	}
	
	var userData = r_uap_userData(data.user);
	
	var userBaskets = "";
	if(data.baskets != "Error" && data.baskets != null){
		for(i=0;i<data.baskets.length; i++){
			userBaskets += r_uap_basketActivity(data.baskets[i]);
		}
	}
	
	return r_uap_compile(userData, userTranses, userBaskets);
}
//Compile into display structure
function r_uap_compile(ud, ut, ub){
	var output = "<div style='width:1085px; height:620px;overflow-y:hidden;'> <!-- Wrapper -->\
		<div id='userActivity'>\
			<div id='dataNav-header'>\
				<div id='dataNav-UUID'>\
					<b>" + username + " : " + uuid + "</b>\
				</div>\
			</div>\
				" + ut+ "\
			</div>\
		<div id='userInfo'>\
			" + ud + "\
			<div id='baskets'>\
				<span class='label' style='font-size:20px;'>Baskets</span>\
				<hr style='margin:4px;'/>\
				" + ub + "\
			</div>\
		</div>\
	</div>";
	return output;
}

//Extract / compile userdata
function r_uap_userData(data){
	///var partyList = "";
	/*if(!data == "Error"){
		for(i=0; i<data.userParty.length; i++){
			partyList += data.userParty.i + "<br/>";
		}
	}*/
	return "<div class='center' id='balance'>\
			$" + data.userBalance + "\
			</div>\
			<div id='personal'>\
				<span class='label' id='p_name'>" + data.userName + "</span>\
				<hr style='margin:4px;'/><br/>\
				<br/> Transactions: " + r_uap_numTrans + "\
			</div>";
}
//Dead party stuff I can probably delete but don't want to just in case
/*<span class='label' id='p_members'>Party Members</span><br/>" +
					data.userParty +"\
*/
//Extract / compile a transaction entry
function r_uap_transActivity(data){
	return "<div class='u_activity_entry'>\
				<div class='uae_transID label'>" + data.entryID + "</div>\
				<div class='uae_transDesc'>" + data.entryDesc + "</div>\
				<div class='uae_transPrice'>" + data.entryCost + "</div>\
			</div>";
}

//Extract / compile a basket entry
function r_uap_basketActivity(data){
	return "<div class='p_basket' id='p_basket_" + data.basketID + "'>\
				<p class='p_basket_name'>#" + data.basketID + " - " + data.basketDesc + "</p>\
				<p class='p_basket_price'>" + data.basketPrice + "</p>\
			</div>";
}