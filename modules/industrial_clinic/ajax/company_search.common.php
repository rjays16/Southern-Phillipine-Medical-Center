<?php

require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/industrial_clinic/ajax/company_search.server.php");
$xajax->configure('characterEncoding', "iso-8859-1");
$xajax->register(XAJAX_FUNCTION, "populateCompany");
$xajax->configure('debug',false);