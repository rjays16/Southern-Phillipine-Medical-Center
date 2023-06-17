<?php
	//Created by EJ 11/26/2014
	if (empty($root_path)) $root_path="../../";
	require_once($root_path.'classes/xajax/xajax.inc.php');

	$xajax = new xajax("dashboard.server.php");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("setDoctors");
	$xajax->registerFunction("setDepartments");
	$xajax->registerFunction("savedeathcause");
?>