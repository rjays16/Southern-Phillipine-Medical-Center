<?php
	#created by VAN 04-08-08
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/nursing/ajax/nursing-ward-server.php");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populateWardList");

	$xajax->registerFunction("moveToWaitingList"); //added by Francis 07-18-13
	$xajax->registerFunction("moveToBeDischarge"); //added rnel
	$xajax->registerFunction("moveBackToWaitingList"); //added rnel
	$xajax->registerFunction("moveToExpiredPatient"); # added by: syboy 02/22/2016 : meow
	$xajax->registerFunction("setWardRooms"); # added by: carriane 12/07/18
	$xajax->registerFunction("saveAccommodation"); # added by: carriane 12/21/18
	$xajax->registerFunction("getAdjustedDate"); # added by: carriane 12/21/18
	$xajax->registerFunction("checkifOverlaps"); # added by: carriane 12/21/18
	$xajax->registerFunction("deleteAccommodation"); # added by: carriane 12/21/18
	$xajax->registerFunction("updateClassification"); # added by: fritz 03/15/20
	$xajax->registerFunction("getPatientClassInfo"); # added by: fritz 03/15/20
	