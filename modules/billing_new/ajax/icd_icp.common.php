<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/billing_new/ajax/icd_icp.server.php");
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->registerFunction("populateDiagnosisList");
    $xajax->registerFunction("addCode");  
    $xajax->registerFunction("rmvCode"); 
    $xajax->registerFunction("rmvDoctorClaim"); // Added by Christian 01-22-19
    $xajax->registerFunction("saveAltDesc");    
    $xajax->registerFunction("updateAltICD"); //added by jasper 04/24/2013
    $xajax->registerFunction("saveAltCode"); //added by jasper 06/30/2013

    $xajax->registerFunction("addProcedure");
    $xajax->registerFunction("populateProcedureList");
    $xajax->registerFunction("deleteProcedure");
    $xajax->registerFunction("updateIcdCode"); //added by Nick, 3/1/2014
    $xajax->registerFunction("updateIcdDesc"); //added by Nick, 3/1/2014
    $xajax->registerFunction("updateIcpDesc"); //added by Nick, 3/1/2014
    $xajax->registerFunction("updateIcdSequence"); //added by Nick, 4/15/2014
    $xajax->registerFunction("updateWellbabyDx");//added by Jasper Ian Q. Matunog 11/11/2014
    $xajax->registerFunction("addDiagProcAdt"); //added by EJ 11/13/2014
    $xajax->registerFunction("getProcedureDetails");
    $xajax->registerFunction("updateLmpDate"); 
    $xajax->registerFunction("saveFilterCardNumber"); #ADDED by JEFF 06-29-18
?>