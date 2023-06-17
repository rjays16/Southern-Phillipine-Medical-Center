<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/industrial_clinic/ajax/seg-ic-transactions.server.php');

$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "updateFilterOption");
$xajax->register(XAJAX_FUNCTION, "updateFilterTrackers");
$xajax->register(XAJAX_FUNCTION, "updatePageTracker");
$xajax->register(XAJAX_FUNCTION, "clearFilterTrackers");
$xajax->register(XAJAX_FUNCTION, "clearPageTracker");
$xajax->register(XAJAX_FUNCTION, "generateBill");
$xajax->register(XAJAX_FUNCTION, "getTotalCharges"); # Added by James 3/25/2014
$xajax->register(XAJAX_FUNCTION, "deleteBill"); # Added by James 3/29/2014
$xajax->register(XAJAX_FUNCTION, "populateBilledList"); # Added by James 4/5/2014
$xajax->register(XAJAX_FUNCTION, "saveDiscount"); # Added by James 4/12/2014
$xajax->register(XAJAX_FUNCTION, "getDiscount"); # Added by James 4/12/2014
?>