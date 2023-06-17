<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/dialysis/ajax/dialysis-transaction.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "getAllMachines");
$xajax->register(XAJAX_FUNCTION, "getDialyzer");
$xajax->register(XAJAX_FUNCTION, "addNewDialyzer");
$xajax->register(XAJAX_FUNCTION, "validateDialyzer");
$xajax->register(XAJAX_FUNCTION, "getDialyzerDetails");
$xajax->register(XAJAX_FUNCTION, "getDoctors");
$xajax->register(XAJAX_FUNCTION, "getNurses");
$xajax->register(XAJAX_FUNCTION, "setVisitNo");
$xajax->register(XAJAX_FUNCTION, "populatePersonList");
$xajax->register(XAJAX_FUNCTION, "populateMiscRequests");
$xajax->register(XAJAX_FUNCTION, "populateIpRequests");
$xajax->register(XAJAX_FUNCTION, "computeTotalPayment");
$xajax->register(XAJAX_FUNCTION, "populateMgRequests");
$xajax->register(XAJAX_FUNCTION, "populateLabRequests");
$xajax->register(XAJAX_FUNCTION, "populateBloodRequests");
$xajax->register(XAJAX_FUNCTION, "populateRadioRequests");
$xajax->register(XAJAX_FUNCTION, "deleteRequest");
$xajax->register(XAJAX_FUNCTION, "deleteRadioServiceRequest");
$xajax->register(XAJAX_FUNCTION, "deleteOrder");
$xajax->register(XAJAX_FUNCTION, "deleteMiscRequest");
$xajax->register(XAJAX_FUNCTION, "changeTransactionStatus");
$xajax->register(XAJAX_FUNCTION, "populateSpLabRequests");
$xajax->register(XAJAX_FUNCTION, "deleteDialysisRequest");
$xajax->register(XAJAX_FUNCTION, "deleteBill");
$xajax->register(XAJAX_FUNCTION, "showEncounterByPid");
$xajax->register(XAJAX_FUNCTION, "populateOtherRequests");
$xajax->register(XAJAX_FUNCTION, "updatePatientDetails");
$xajax->register(XAJAX_FUNCTION, "savePatientDetails");
$xajax->register(XAJAX_FUNCTION, "disableDialysisEncounter");
$xajax->register(XAJAX_FUNCTION, "enableDialysisEncounter");
$xajax->register(XAJAX_FUNCTION, "getDischargeFlag");
#added by art 09/10/2014
$xajax->register(XAJAX_FUNCTION, "getBillNrDetails_ajx");
$xajax->register(XAJAX_FUNCTION, "getPrebillPayments_ajx");
$xajax->register(XAJAX_FUNCTION, "savePay_ajx");
$xajax->register(XAJAX_FUNCTION, "appendTbl_ajx");
$xajax->register(XAJAX_FUNCTION, "applyPay_ajx");
$xajax->register(XAJAX_FUNCTION, "getUnpaidPrebill_ajx");
$xajax->register(XAJAX_FUNCTION, "appendTbl_add_new_trxn_ajx");
#end art
//added by Kenneth Kempis 04/13/2018
$xajax->register(XAJAX_FUNCTION, "ajxsetIsPrinted");
$xajax->register(XAJAX_FUNCTION, "ajxgetIsPrinted");
//end Kenneth Kempis 04/13/2018
// Added by Matsuu 01172017
$xajax->register(XAJAX_FUNCTION, "getPreviousRequest");
$xajax->register(XAJAX_FUNCTION, "getPreviousDiag");
// Ended by Matsuu 01172017