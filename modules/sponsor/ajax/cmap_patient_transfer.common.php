<?php
	require('./roots.php');
	#commented out bry bryan on Sept 15, 2008
	/*require_once($root_path.'classes/xajax/xajax.inc.php');*/

	#added by bryan on Sept 15, 2008
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/sponsor/ajax/cmap_patient_transfer.server.php");

	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->register(XAJAX_FUNCTION, "save");
	$xajax->register(XAJAX_FUNCTION, "getFund");
	$xajax->register(XAJAX_FUNCTION, "update");	//added by cha, june 7, 2010
	$xajax->register(XAJAX_FUNCTION, "checkExistingCmapNo");
	$xajax->register(XAJAX_FUNCTION, "checkExistingReferralNo");


