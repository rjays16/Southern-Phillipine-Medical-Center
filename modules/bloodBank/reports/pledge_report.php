<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/bloodBank/ajax/blood-waiver.server.php');
require_once($root_path.'include/care_api_classes/class_blood_bank.php');


$objInfo = new Hospital_Admin();

if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hospital_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hospital_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}
  
$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$bloodObj = new SegBloodBank();

$pid = $_GET['pid'];
$refno = $_GET['ref_no'];
$encounter_nr = $_GET['enc'];

$pledge_details = $bloodObj->getPledgeDetails($refno, $encounter_nr, $pid);

switch ($pledge_details['donated_to']) {
  case 'spmc_program':
    $spmc_blood = '/';
    break;
  case 'dvo_blood_center':
    $dvo_blood_center = '/';
    break;
  case 'brgy_donation':
    $brgy_donation = '/';
    break;  
}

$blood_type = ($pledge_details['blood_type'] ? $pledge_details['blood_type'] : ($_GET['blood_type'] != '-Not Indicated-' ? $_GET['blood_type'] : "Not Indicated"));

$details = $db->GetAll("SELECT fn_get_pid_name(pid) as fullname, fn_get_complete_address(pid) as address, cellphone_1_nr FROM care_person WHERE pid = ".$db->qstr($pid));

if(strlen($details[0]['address']) > 33)
  $address_short = $details[0]['address'];
else $address = $details[0]['address'];  

if(strlen($pledge_details['watcher_name']) > 22) $watcher_name_short = $pledge_details['watcher_name'];
else $watcher_name = $pledge_details['watcher_name'];

if(strlen($details[0]['fullname']) > 22) $patient_name_short = $details[0]['fullname'];
else $patient_name = $details[0]['fullname'];

$params = array(
  'r_doh' => $baseurl . "img/doh.png",
  'r_spmc' => $baseurl . "gui/img/logos/dmc_logo.jpg",
  'hosp_name' => strtoupper($row['hosp_name']),
  'date'=> date("M d, Y"),
  'pid' => $pid,
  'patient_name' => $patient_name,
  'patient_name_short' => $patient_name_short,
  'blood_type' => $blood_type,
  'no_of_units' => $pledge_details['no_of_units'],
  'component' => $pledge_details['components'],
  'watcher_name' => $watcher_name,
  'watcher_name_short' => $watcher_name_short,
  'address' => $address,
  'address_short' => $address_short,
  'contact_no' => $details[0]['cellphone_1_nr'],
  'user_login'=> $_SESSION["sess_user_name"]
);


$output[0] = array(
        'spmc_blood' => $spmc_blood,
        'dvo_blood_center' => $dvo_blood_center,
        'brgy_donation'=> $brgy_donation
      );

showReport('BB_Pledge_of_Commitment',$params,$output,'PDF'); 
?>
