<?php
	require_once('./roots.php'); 
  	require_once($root_path.'classes/xajax/xajax.inc.php');
  	$xajax = new xajax($root_path."modules/personell_admin/ajax/ajax-personnel-orientation.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	
// Added By John
	$xajax->registerFunction("saveOrientation");
	$xajax->registerFunction("removeFromList");
	$xajax->registerFunction("updateFromOrientation");


?>