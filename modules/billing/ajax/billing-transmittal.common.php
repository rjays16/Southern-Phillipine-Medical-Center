<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing/ajax/billing-transmittal.server.php');

$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "showTransmittalDetails");
$xajax->register(XAJAX_FUNCTION, "assignToSessionVar");
$xajax->register(XAJAX_FUNCTION, "setMemCategoryOptions");
$xajax->register(XAJAX_FUNCTION, "setMemCategoryOptionsForPrint");
$xajax->register(XAJAX_FUNCTION, "delTransmittal");
$xajax->register(XAJAX_FUNCTION, "setMemCategory");
$xajax->register(XAJAX_FUNCTION, "removeCaseInClaim");
$xajax->register(XAJAX_FUNCTION, "populateDiagnosisList");
$xajax->register(XAJAX_FUNCTION, "getCurrentOpsInEncounter");
$xajax->register(XAJAX_FUNCTION, "getDischrgDateTime");
$xajax->register(XAJAX_FUNCTION, "saveICDDescs");
$xajax->register(XAJAX_FUNCTION, "saveICPDetails");
$xajax->register(XAJAX_FUNCTION, "saveEncounterInfo");
$xajax->register(XAJAX_FUNCTION, "setFormsForSelection");
$xajax->register(XAJAX_FUNCTION, "getPolicyHolderInfo");
$xajax->register(XAJAX_FUNCTION, "getPatientEncounterInfo");
$xajax->register(XAJAX_FUNCTION, "addCode");
$xajax->register(XAJAX_FUNCTION, "rmvCode");
$xajax->register(XAJAX_FUNCTION, "getMuniCityandProv");
$xajax->register(XAJAX_FUNCTION, "downloadXmlFile");
$xajax->register(XAJAX_FUNCTION, "getBillsCount");
$xajax->register(XAJAX_FUNCTION, "getTransReasonDelete"); #ADDED by JEFF 06-06-17
$xajax->register(XAJAX_FUNCTION, "getDeleteReasonDesc"); #ADDED by JEFF 06-06-17
$xajax->register(XAJAX_FUNCTION, "saveDeleteReasonNew"); #ADDED by JEFF 06-08-17
$xajax->register(XAJAX_FUNCTION, "downloadClaimsXmlArchive");
$xajax->register(XAJAX_FUNCTION, "updateCataractCode"); #added by: syboy 06/27/2015
$xajax->register(XAJAX_FUNCTION, "loadDeffCode");#added by: syboy 09/06/2015
$xajax->register(XAJAX_FUNCTION, "loadInputsCatCode");#added by: syboy 09/22/2015
?>