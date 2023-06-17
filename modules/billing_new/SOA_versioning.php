<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require_once($root_path . "classes/fpdf/fpdf.php");
require_once($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/billing/class_billing_new.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/billing/class_accommodation.php');
require_once($root_path . 'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path . 'include/care_api_classes/class_insurance.php');
require_once($root_path . 'include/care_api_classes/class_credit_collection.php'); // added by michelle 06-25-2015
require_once($root_path . 'include/care_api_classes/billing/class_bill_info.php');
require_once $root_path . 'frontend/bootstrap.php';
require_once($root_path . 'include/care_api_classes/class_define_config.php');

$define_soa_version2 = new Define_Config('SOA_VERSION_2');
define('SOA_VERSION_2', $define_soa_version2->get_value());

if(date('Y-m-d', $_GET['bill_dt']) > Date(SOA_VERSION_2)){
	require_once('SOA2.php');
}else{
	require_once('SOA.php');
}