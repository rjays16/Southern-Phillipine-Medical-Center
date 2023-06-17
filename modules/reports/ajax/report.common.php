<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/reports/ajax/report.server.php");
    
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->register(XAJAX_FUNCTION, "getMuniCityandProv");
    $xajax->register(XAJAX_FUNCTION, "getProvince");
    $xajax->register(XAJAX_FUNCTION, "getIndexLevel2");
    $xajax->register(XAJAX_FUNCTION, "getIndexLevel3");
    $xajax->register(XAJAX_FUNCTION, "getIndexLevel4");
    $xajax->register(XAJAX_FUNCTION, "getGuarantor"); # added by: sybpy 03/15/2016 : meow
    $xajax->register(XAJAX_FUNCTION, "getDeptWard");
    $xajax->register(XAJAX_FUNCTION, "chargeName");

    $xajax->register(XAJAX_FUNCTION,"getICDICP");#Added by Matsuu

?>