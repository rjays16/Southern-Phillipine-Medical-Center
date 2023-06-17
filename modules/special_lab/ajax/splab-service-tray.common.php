<?php
	require('./roots.php');
#	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path.'modules/special_lab/ajax/splab-service-tray.server.php');
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populateSpecialLabServiceList");
	$xajax->registerFunction("setALLDepartment");
	$xajax->registerFunction("setDepartmentOfDoc");
	$xajax->registerFunction("setDoctors");

	$xajax->registerFunction("populate_lab_checklist");

	$xajax->registerFunction("getAllServiceOfPackage");

	$xajax->registerFunction("getDeptDocValues");

	$xajax->registerFunction("checkTestERLab");

?>