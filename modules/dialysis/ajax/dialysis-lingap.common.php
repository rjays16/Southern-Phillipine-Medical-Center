<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/dialysis/ajax/dialysis-lingap.server.php");
//$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "addBills");
$xajax->register(XAJAX_FUNCTION, "populateBillsList");
$xajax->register(XAJAX_FUNCTION, "getBillNrDetails");


