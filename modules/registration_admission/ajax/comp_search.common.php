<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/registration_admission/ajax/comp_search.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateEncounterList");
	
	$xajax->registerFunction("populatePatientList");
    
    $xajax->registerFunction("populateAdmissionList");
	
	$xajax->registerFunction("changeStatus");
	#Vaccination Certificate if patient is new born
	#Medical Records ('Dialog box').
	#Comment by: borj 2014-05-06
    $xajax->registerFunction("saveVaccination");
    #End

    #added by fritz 03/30/2021
    $xajax->registerFunction("populateRegisteredFingerprint");

?>