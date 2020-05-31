// JavaScript Document

function isThere(url) {
	var req= new_ajax(); // XMLHttpRequest object
	try {
		req.open("HEAD", url, false);
		req.send(null);
		return req.status== 200 ? true : false;
	}
	catch (er) {
		return false;
	}
}


function new_ajax() {
	var obj;
	if (window.XMLHttpRequest) obj= new XMLHttpRequest();
	else if (window.ActiveXObject){
		try{
			obj= new ActiveXObject('MSXML2.XMLHTTP.3.0');
		}
		catch(er){
			try{
				obj= new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(er){
				obj= false;
			}
		}
	}
	return obj;
}


function new_ajax_request(url,variablen,werte) {
	var req = null;
	var parameter;

	req=new_ajax();

	parameter='';
	for (var i=0;i<variablen.length;i++) {
		parameter+=variablen[i]+"="+werte[i];
		if (i<variablen.length-1) parameter+="&";
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send(parameter);

	return req;
}
function ajax_ready(req) {
	if (req.readyState==4 && req.status==200)
		return true;
	else
		return false;
}
function ajax_error(req) {
	if (req.readyState==4 && req.status!=200)
		return true;
	else
		return false;
}
function ajax_answer(req) {
	return req.responseText;
}
function ajax_debug(req) {
	alert("Status: "+req.status+", readyState: "+req.readyState+", Response: "+req.responseText);
}