<?php
	if (empty($root_path)) $root_path="../../";
	require_once($root_path.'classes/xajax/xajax.inc.php');
#	$xajax = new xajax("address.server.php");
	$xajax = new xajax($root_path.'modules/registration_admission/address.server.php');
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("setAll");
	$xajax->registerFunction("setBarangay");
	$xajax->registerFunction("setMuniCity");
	$xajax->registerFunction("setZipcode");
	$xajax->registerFunction("setProvince");	
	$xajax->registerFunction("setRegion");	
	
	#added by VAN 05-06-08
	$xajax->registerFunction("checkinDBperson");
	
	#added by VAN 04-29-09
	$xajax->registerFunction("validateDept");

	$xajax->registerFunction("addauditPHIC");

	#added by Matsuu 04232018
	$xajax->registerFunction("updateProfileEncounter");
        
        #added by Matsuu 01-01-2016
	$xajax->registerFunction("addDocuments");

	#added by Christian 06-08-2020
	$xajax->registerFunction("updatePhicInfo");
	#added by Christian 03-17-2020
    #commented by fritz 03/30/2021
//	$xajax->registerFunction("populateRegisteredFingerprint");
?>