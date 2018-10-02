<?php
//client = computer/operator, customer = person with chargecard
//COOKIE - ATHOPID = Session of ID Operator. Corisponds to app_authSessions
//COOKIE - CURRUD = UUID of current customer

abstract class CONFIG{
//Config section
	//System lockdown -TODO:Standardize
	const SYS_LOCKOUT = false;

	//Activate secure mode. Requires operators to login before being able to access most pages
	const OPS_ENABLED = true;

	//Maximium number of locations an operator can be logged into at once (based off ips in database)
	const OPS_MAXLOGIN = 2;//TODO:impliment

	//Root directory of TSA
  	const SVR_IP = "192.168.1.249";
	const DOC_ROOT = "/var/www/html/Rebuild";
	const DOC_ROOT_WEB = "http://192.168.1.249/Rebuild";
}
?>
