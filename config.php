<?php
/*
*       TSA Auction Manager Configuration
*         Program Release: Alpha
*         Developed by Parsons Consulting Services
*/
abstract class CONFIG{
  //System path to installation root folder
  const DOC_ROOT = "/var/www/html/Development";

  //URL path to doc root
  const DOC_ROOT_WEB = "https://172.16.8.242/Development";

//
//  Operator Configuration Parameters
//

  //Require sales operators to login
  const OPS_ENABLED = true;

  //Enables layered user/operator permissions
  const OPS_PERMISSIONS = true;

  //Only allow operators to login from specific subnet
  const OPS_NETWORK = "172.16.8.0";
  const OPS_NETWORK_SUBNET = "255.255.255.0";

  //cookie security fields
  const OPS_SESSION_RESTRICT = "172.16.8.242";//location
  const OPS_SSL_ENABLED = true;//Secure
  //Allowed # of login sessions (0: unlimited)
  const OPS_MAXLOGIN = 0;
  //Max number of operator accounts allowed in system
  const OPS_MAX_REGISTERED = 1024;

//
//  Account Configuration Parameters
//

	//Pin required to use charge card
	const ACT_REQUIRE_PIN = 0;

	//Allow submission of photo ID when card created
	const ACT_PHOTO_ID = 1;

//
//  Transaction Configuration Parameters
//
  const TRS_ENTRANCE_FEE = 30;
}
?>
