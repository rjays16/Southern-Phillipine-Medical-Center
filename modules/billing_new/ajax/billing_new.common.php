<?php
require('roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing_new/ajax/billing_new.server.php');
//$xajax->configure("debug",true);
$xajax->setCharEncoding("ISO-8859-1");

$xajax->register(XAJAX_FUNCTION, "populateBill");
$xajax->register(XAJAX_FUNCTION, "populateBillFirst");
$xajax->register(XAJAX_FUNCTION, "checkInsurance");
$xajax->register(XAJAX_FUNCTION, "isPayward");
$xajax->register(XAJAX_FUNCTION, "checkRemainingInsurance");
/*-------------Accommodation Dialog-------------*/
$xajax->register(XAJAX_FUNCTION, "setWardOptions");
$xajax->register(XAJAX_FUNCTION, "setWardRooms");
$xajax->register(XAJAX_FUNCTION, "getAccommodationRate");
$xajax->register(XAJAX_FUNCTION, "saveAccommodation");
$xajax->register(XAJAX_FUNCTION, "delAccommodation");
$xajax->register(XAJAX_FUNCTION, "populateAccommodation");
$xajax->register(XAJAX_FUNCTION, "showRemainingDays");//added by art 01/05/2014

/*------------Misc Services and Supplies--------*/
$xajax->register(XAJAX_FUNCTION, "chargeMiscService");
$xajax->register(XAJAX_FUNCTION, "populateMiscServices");
$xajax->register(XAJAX_FUNCTION, "delMiscService");
$xajax->register(XAJAX_FUNCTION, "delPharmaSupply");
$xajax->register(XAJAX_FUNCTION, "chargePharmaSupply");
$xajax->register(XAJAX_FUNCTION, "deleteUnpaidCps"); // added by gervie 07/25/2015
$xajax->register(XAJAX_FUNCTION, "convertUnpaidCps"); // added by gervie 07/30/2015
/*---------------Drugs and Medicines------------*/
$xajax->register(XAJAX_FUNCTION, "populateMeds");
/*---------------Misc Charges ------------------*/
$xajax->register(XAJAX_FUNCTION, "populateMiscCharges");
$xajax->register(XAJAX_FUNCTION, "delMiscChrg");
$xajax->register(XAJAX_FUNCTION, "chargeMiscChrg");


$xajax->register(XAJAX_FUNCTION, "setDeathDate"); 
$xajax->register(XAJAX_FUNCTION, "identifyProcedureConflict"); 

/*---------Operating Room Accomodation Charges---------*/ 
$xajax->register(XAJAX_FUNCTION, "setORWardOptions");
$xajax->register(XAJAX_FUNCTION, "setORWardRooms");
$xajax->register(XAJAX_FUNCTION, "populateAppliedOpsList");
$xajax->register(XAJAX_FUNCTION, "updateRVUTotal");
$xajax->register(XAJAX_FUNCTION, "saveORAccommodation");
$xajax->register(XAJAX_FUNCTION, "delOpAccommodation");

/*-------------------Add Doctors-----------------------*/
$xajax->register(XAJAX_FUNCTION, "setDoctors");
$xajax->register(XAJAX_FUNCTION, "setRoleArea");
$xajax->register(XAJAX_FUNCTION, "setOptionRoleLevel");
$xajax->register(XAJAX_FUNCTION, "processPrivateDrCharge");
$xajax->register(XAJAX_FUNCTION, "ProfFees");
$xajax->register(XAJAX_FUNCTION, "rmPrivateDr");
$xajax->register(XAJAX_FUNCTION, "rmDr");
$xajax->register(XAJAX_FUNCTION, "getDrRole");
$xajax->register(XAJAX_FUNCTION, "delDoctors");

/*--------------------Case Rates--------------------------*/
$xajax->register(XAJAX_FUNCTION, "populateCaseRate");
$xajax->register(XAJAX_FUNCTION, "caseRateDetails");
$xajax->register(XAJAX_FUNCTION, "updateHearingTest");//added by Nick, 4/22/2014
$xajax->register(XAJAX_FUNCTION, "caserateModificationHistory"); //Added by Rochie 7-12-17
$xajax->register(XAJAX_FUNCTION, "clearCaseRateMultiplier"); //added by carriane 09/07/17

/*--------------------Save bill--------------------------*/
$xajax->register(XAJAX_FUNCTION, "toggleMGH");
$xajax->register(XAJAX_FUNCTION, "saveThisBilling");

$xajax->register(XAJAX_FUNCTION, "setBillNr");//added by Nick, 12/27/2013
$xajax->register(XAJAX_FUNCTION, "classification");//added by poliam 01/04/2014
$xajax->register(XAJAX_FUNCTION, "confinment");//added by poliam 01/04/2013
$xajax->register(XAJAX_FUNCTION, "getConfineTypeOption");//added by poliam 01/04/2013
$xajax->register(XAJAX_FUNCTION, "setConfinementType");//added by poliam 01/04/2013
$xajax->register(XAJAX_FUNCTION, "setCaseType");
$xajax->register(XAJAX_FUNCTION, "setOpdArea"); // added by: syboy 08/19/2015

/*--------------------Delete bill--------------------------*/
$xajax->register(XAJAX_FUNCTION, "deleteBilling");//added by borj 2014-06-01
$xajax->register(XAJAX_FUNCTION, "clearBilling");//added by borj 2014-06-01

$xajax->register(XAJAX_FUNCTION, "saveOutMedsXLO");//added by jarel 01/12/2014
$xajax->register(XAJAX_FUNCTION, "getOutMedsXLO");//added by jarel 01/12/2014

$xajax->register(XAJAX_FUNCTION, "populateMeds");//added by jarel 04/28/2014
$xajax->register(XAJAX_FUNCTION, "populateXLO");//added by jarel 04/28/2014
$xajax->register(XAJAX_FUNCTION, "populateMisc");//added by jarel 04/28/2014
$xajax->register(XAJAX_FUNCTION, "getBilledOps");//added by jarel 04/28/2014

$xajax->register(XAJAX_FUNCTION, "saveDoctorCoverage");//added by jarel 01/12/2014
$xajax->register(XAJAX_FUNCTION, "updateOpDate");//added by Nick, 05/12/2014
$xajax->register(XAJAX_FUNCTION, "getMembershipTypes");//added by Nick, 05/12/2014
$xajax->register(XAJAX_FUNCTION, "updateMemCat");//added by Nick, 05/12/2014

$xajax->register(XAJAX_FUNCTION, "showSoa");//added by Nick, 08/13/2014
$xajax->register(XAJAX_FUNCTION, "checkDateForEclaims"); //added by janken 11/13/2014
$xajax->register(XAJAX_FUNCTION, "saveAdditionalLimit");//added by art, 11/22/2014
$xajax->register(XAJAX_FUNCTION, "getCurrentLimit");//added by art, 11/22/2014

$xajax->register(XAJAX_FUNCTION, "saveNote"); //Added by Gervie 01/26/2016
$xajax->register(XAJAX_FUNCTION, "setPatientNote"); //Added by Gervie 01/26/2016
$xajax->register(XAJAX_FUNCTION, "getAdmittingDiag");
$xajax->register(XAJAX_FUNCTION, "getCurrentDateTime");
?>