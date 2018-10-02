function pageLoader(url, data){//URLs defined in TSA_CORECONSTANTS, for POST requests: data to send to server
	cycleProcessing();
	
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function(){
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
//console.log(xmlHttp.responseText.length + " - " + xmlHttp.responseText);
			if(xmlHttp.responseText.length > 1){
				response = JSON.parse(xmlHttp.responseText);//"action"=function in render.js, "args"=function args, "append"=>append(true) or rewrite(false)
				console.log("[TSA]response:" + response.action + " (" + response.args + " )");
			}else{
				console.log("[TSA]Received Empty Response from " + url)
				return false;
			}
			
			try{
				renderResponse = window[response.action](response.args);//Consider changing to eval()?
			}catch(error){
				displayError("Sorry, I'm not sure how to display this response.", 8000);
				console.log(error);
				console.log(error.name + " " + error.message);
			}
			if(response.append == "none"){
				//Do nothing. Sent back non-display action
				console.log("[TSA]Page loader received non displayable action " + response.action + " (" + response.args + ");");
				window[response.action](response.args);//Consider changing to eval()?
			}else if(response.append){
				contWrap.innerHTML += renderResponse;
			}else{
				contWrap.innerHTML = renderResponse;
			}
			render_globalLeftNav();
			cycleProcessing();
		}
	}
	var reqType = "GET";
	var postData = "";
	if(data){
		postData = data;
		reqType = "POST";
	}
	
	xmlHttp.open(reqType, url, true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send(postData);
}

//Loading JS scripts for page actions
function loadPageResources(resources){
	for(i=0;i<resources.length;i++){
		//Prevent creation of duplicate elements
		if(!document.getElementById(resources.i.id) == "undefined")
			continue;
		
		if(resources.i.type == "text/css"){
			var link = document.createElement( "link" );
			link.href = resources.i.href
			link.type = "text/css";
			link.rel = "stylesheet";
			link.id  = resources.i.id;
			
			document.getElementsByTagName( "head" )[0].appendChild( link );
		}else if(resources.i.type == "text/javascript"){
			var script = document.createElement( "script" );
			script.src = resources.i.href
			script.type = "text/javascript";
			script.id  = resources.i.id;
			
			document.getElementsByTagName( "head" )[0].appendChild( script );
		}else
			continue;
	}
}

//Print directly to contWrap. Sets renderResponse.
function printRenderResponse(body){
	return body;
}

//Incoming request from QRgateway.php. Expects a uuid to work with
//Searchbox->uuid->autosearch
function qrGateway(uuid){
	contWrap.innerHTML = renderUserAccountSelector();
	var elem = document.getElementById("search");
	elem.value=uuid;//Write uuid to search bar
	var e = document.createEvent("HTMLEvents");
	e.initEvent("keyup", true, true);//"type", "bubbling?"(overflow to children), cancelable?
	elem.dispatchEvent(e);
}