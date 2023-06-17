<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/industrial_clinic/ajax/agency_mgr.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "saveAgency");
$xajax->register(XAJAX_FUNCTION, "updateAgency");
$xajax->register(XAJAX_FUNCTION, "deleteAgency");
$xajax->register(XAJAX_FUNCTION, "deleteAgencyMember");
$xajax->register(XAJAX_FUNCTION, "assignAgencyMember");
$xajax->register(XAJAX_FUNCTION, "updateEmployeeData");
$xajax->register(XAJAX_FUNCTION, "saveServicePriceToCompany");
$xajax->register(XAJAX_FUNCTION, "deleteServicePriceToCompany");
$xajax->register(XAJAX_FUNCTION, "saveCompanyPackage");
$xajax->register(XAJAX_FUNCTION, "deleteCompanyPackage");
$xajax->register(XAJAX_FUNCTION, "showCompanyPackageDetails");
$xajax->register(XAJAX_FUNCTION, "editCompanyPackage");


#added code by angelo m. 08.24.2010
$xajax->register(XAJAX_FUNCTION, "populateNames");

?>
