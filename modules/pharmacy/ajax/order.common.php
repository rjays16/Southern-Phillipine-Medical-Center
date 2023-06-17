<?php
	require('./roots.php');
	#commented out bry bryan on Sept 15, 2008
	/*require_once($root_path.'classes/xajax/xajax.inc.php');*/
	
	#added by bryan on Sept 15, 2008
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/pharmacy/ajax/order.server.php");
	
	$xajax->setCharEncoding("ISO-8859-1");
  # $xajax->configure('debug',true); 
	$xajax->register(XAJAX_FUNCTION, "reset_referenceno");
	$xajax->register(XAJAX_FUNCTION, "add_item");
	$xajax->register(XAJAX_FUNCTION, "serveToInventory");
	$xajax->register(XAJAX_FUNCTION, "populate_order");
	$xajax->register(XAJAX_FUNCTION, "get_charity_discounts");
	$xajax->register(XAJAX_FUNCTION, "populateOrderList");
  $xajax->register(XAJAX_FUNCTION, "deleteOrder"); 
  $xajax->register(XAJAX_FUNCTION, "updatePHICCoverage"); 

  
  $xajax->register(XAJAX_FUNCTION, "updateCoverage"); 
  $xajax->register(XAJAX_FUNCTION, "insertPharmaArea");#added by MARK 10-02-16
  $xajax->register(XAJAX_FUNCTION, "saveAreasbyUserDefault");#added by MARK April 19, 2017

	//added by julius search hrd id order 01-06-2017
  $xajax->register(XAJAX_FUNCTION, "getpharmalocation"); 
  $xajax->register(XAJAX_FUNCTION, "checkifhasphic");
  $xajax->register(XAJAX_FUNCTION, "newChargeType");
  $xajax->register(XAJAX_FUNCTION, "returnChargeType");
  
  $xajax->register(XAJAX_FUNCTION, "saveAreasbyUserDefault"); # Added by Matsuu 04102018
  $xajax->register(XAJAX_FUNCTION, "getExpiryDate"); #Added by Matsuu
  $xajax->register(XAJAX_FUNCTION, "getExcludedAreas");
  $xajax->register(XAJAX_FUNCTION, "updatePHIC");
$xajax->register(XAJAX_FUNCTION,"getDrugDescription");
$xajax->register(XAJAX_FUNCTION,"initDosageRouteFreq");




