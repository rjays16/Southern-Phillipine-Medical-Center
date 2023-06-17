<?php
//added by Nick 1/28/2014
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/laboratory/ajax/ajax_labresult.server.php");
$xajax->registerFunction("Results");
?>
