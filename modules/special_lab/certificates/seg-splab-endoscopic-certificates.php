<?php
#Added by Borj 2014-08-8
#Special Lab Certificate
#CHOLANGIOPANCREATOGRAPHY REPORT
#ENDOSCOPY REPORT
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');

include_once($root_path . 'include/inc_date_format_functions.php');
include_once($root_path . 'reports/reports/parameters.php');

require_once($root_path . '/include/care_api_classes/class_drg.php');
$objDRG = new DRG;
include_once($root_path . 'include/care_api_classes/class_encounter.php');
$enc_obj = new Encounter;
include_once($root_path . 'include/care_api_classes/class_cert_med.php');
include_once($root_path . 'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;
include_once($root_path . 'include/care_api_classes/class_personell.php');
$pers_obj = new Personell;
include_once($root_path . 'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

$pid = $_GET['pid'];


$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hospital_name'] = strtoupper($row['hosp_name']);

} else {
    $row['hosp_agency'] = "Department of Health";
    $row['hosp_addr1'] = "J.P. Laurel Bajada, Davao City";
}


if($_GET['select'] == 'report1'){
      $row['name_rep1'] = 'ENDOSCOPIC RETROGRADE';
      $row['name_rep2'] = 'CHOLANGIOPANCREATOGRAPHY REPORT';
    }else{
      $row['name_rep1'] = 'UPPER GASTROINTESTINAL';
      $row['name_rep2'] = 'ENDOSCOPY REPORT';
    }

$params = array(
                "hosp_agency" => $row['hosp_agency'],
                "hosp_addr1" => $row['hosp_addr1'],

                "name_report1" => $row['name_rep1'],
                "name_report2" => $row['name_rep2'],

                "date" => $_GET['datepicker'],
                "hrn" => $_GET['endo_hrn'],
                "name" => strtoupper($_GET['endo_name']),
                "agesex" => strtoupper($_GET['endo_age']),
                "address" => strtoupper($_GET['endo_address']),
                "indication" => strtoupper($_GET['endo_indication']),
                "ruv" => strtoupper($_GET['endo_ruv']),
                "phic" => $_GET['select_phic'],
                "anesthesiologist" => $_GET['blood_gastro'],
                "pre-endoscopyimpress" => strtoupper($_GET['endo_endoscopy']),
                "findings" => strtoupper($_GET['endo_findings']),
                "impression" => strtoupper($_GET['endo_impressions']),
                "biopsy" => strtoupper($_GET['endo_biopsy']),
                "suggestions" => strtoupper($_GET['endo_suggestions']),
                "endoscopist_name" => $_GET['blood_endoscopist'],
                
                );

$data[0] = array();


$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['image_02'] = $baseurl . "img/doh.png";

showReport('SPLAB_Endoscopi_Certificate', $params, $data, 'PDF');
?>
