<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/dependents/ajax/dependents.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	
	$xajax->registerFunction("populateDependentsList");
	$xajax->registerFunction("deleteDependent"); // added by Gervie 11/19/2015
	$xajax->registerFunction("addDependent"); // added by Gervie 01/26/2016
	$xajax->registerFunction("deleteAllDependents"); // added by Gervie 01/26/2016
	$xajax->registerFunction("changeRelation"); // added by JEFF 08/17/2017
?>