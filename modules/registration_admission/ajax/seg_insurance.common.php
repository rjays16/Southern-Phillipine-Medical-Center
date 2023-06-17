<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/registration_admission/ajax/seg_insurance.server.php");
    
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->register(XAJAX_FUNCTION, "setFlagForPrincipalNmFromTmp");
    $xajax->register(XAJAX_FUNCTION, "clearEncCategory");
    $xajax->register(XAJAX_FUNCTION, "checkMembershipTypeData"); //Added by EJ 11/21/2014
    $xajax->register(XAJAX_FUNCTION, "addInsuranceAdt"); //added by EJ 11/13/2014
?>