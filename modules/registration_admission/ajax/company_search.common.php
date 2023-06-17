<?php
/* added by art 05/11/2014 */

require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/registration_admission/ajax/company_search.server.php");
$xajax->configure('characterEncoding', "iso-8859-1");
$xajax->register(XAJAX_FUNCTION, "populateCompany");
$xajax->configure('debug',false);