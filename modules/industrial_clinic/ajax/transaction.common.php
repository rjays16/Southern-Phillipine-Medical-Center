<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/industrial_clinic/ajax/transaction.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION,"saveTransaction");
$xajax->register(XAJAX_FUNCTION, "populateTransaction");
$xajax->configure('debug',false);
?>
