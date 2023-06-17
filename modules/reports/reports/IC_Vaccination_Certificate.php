<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

global $db;
$enc_obj =new Encounter;
$obj_medCert = new SegICCertMed;
$tr_obj = new SegICTransaction;
$prsn_obj = new Personell;

$encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']);
$result = $tr_obj->getPersonData($_GET['pid']);
$vacc = $obj_medCert->getAllinfoCertVaccine($_GET['refno']);

$name = stripslashes(strtoupper($encInfo['name_last'])) . ', ' . stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle']));
$sex = ($encInfo['sex'] == 'f' ? 'Female' : 'Male');
$age = floor((time() - strtotime($encInfo['date_birth']))/31556926) + "";
$status = $encInfo['civil_status'];


if ($encInfo['mun_name'] == "NOT PROVIDED" && $encInfo['prov_name'] == "NOT PROVIDED" && $encInfo['mun_name'] == "NOT PROVIDED") {
    $address = $encInfo['street_name'];
} else {
    $address = $result['address'];
}



// added by carriane 12/12/18;
// adjust font size if length exceeds to 59 characters
if(strlen($address) > 70){
    $address_short = $address;
    $address = '';
}
// end carriane
#echo var_dump($encInfo);
#exit();

if($date1 = strtotime($vacc['first_tetanus'])) {
    $datetime1 = date('F d, Y', $date1);
}
else{
    $datetime1 = "";
}

if($date2 = strtotime($vacc['second_tetanus'])) {
    $datetime2 = date('F d, Y', $date2);
}
else{
    $datetime2 = "";
}

if($date3 = strtotime($vacc['third_tetanus'])) {
    $datetime3 = date('F d, Y', $date3);
}
else{
    $datetime3 = "";
}

if($date4 = strtotime($vacc['first_hepatitis'])) {
    $datetime4 = date('F d, Y', $date4);
}
else{
    $datetime4 = "";
}

if($date5 = strtotime($vacc['second_hepatitis'])) {
    $datetime5 = date('F d, Y', $date5);
}
else{
    $datetime5 = "";
}

if($date6 = strtotime($vacc['third_hepatitis'])) {
    $datetime6 = date('F d, Y', $date6);
}
else{
    $datetime6 = "";
}

$sig_info = $prsn_obj->get_specific_signatory($vacc['create_id']);
$sig_info2 = $prsn_obj->get_specific_signatory($_GET['noted_by']);

$license1 = $prsn_obj->get_Person_name($sig_info['personell_nr']);
$license2 = $prsn_obj->get_Person_name($sig_info2['personell_nr']);

if ($sig_info['title'] == null){
    $title = '';
}else{
    $title = ", ".$sig_info['title'];
}

if ($sig_info2['title'] == null){
    $title2 = '';
}else{
    $title2 = ", ".$sig_info2['title'];
}

$params = array(
    'hrn' => $_GET['pid'],
    'date' => date("F d, Y"),
    'name' => utf8_decode(trim($name)),
    'age' => $age,
    'sex' => $sex,
    'status' => ucfirst($status),
    'address' => $address,
    'address_short' => $address_short,
    'tetanus1' => ($vacc['first_tetanus']) ? '/' : '',
    'tetanus2' => ($vacc['second_tetanus']) ? '/' : '',
    'tetanus3' => ($vacc['third_tetanus']) ? '/' : '',
    'deltoid1' => strtoupper($vacc['first_tetanus_deltoid']),
    'deltoid2' => strtoupper($vacc['second_tetanus_deltoid']),
    'deltoid3' => strtoupper($vacc['third_tetanus_deltoid']),
    'date1' => $datetime1,
    'date2' => $datetime2,
    'date3' => $datetime3,
    'hepatitis1' => ($vacc['first_hepatitis']) ? '/' : '',
    'hepatitis2' => ($vacc['second_hepatitis']) ? '/' : '',
    'hepatitis3' => ($vacc['third_hepatitis']) ? '/' : '',
    'deltoid4' => strtoupper($vacc['first_hepatitis_deltoid']),
    'deltoid5' => strtoupper($vacc['second_hepatitis_deltoid']),
    'deltoid6' => strtoupper($vacc['third_hepatitis_deltoid']),
    'date4' => $datetime4,
    'date5' => $datetime5,
    'date6' => $datetime6,
    'signatory1' => $sig_info['name'] . $title,
    'license1' => 'License #: ' . $license1['license_nr'] . '/Expiration: ' . strtoupper($license1['prescription_license_nr']),
    'jobtitle1' => $sig_info['signatory_position'],
    'signatory2' => "(SGD) " . $sig_info2['name'] . $title2,
    'license2' => 'License #: ' . $license2['license_nr'],
    'jobtitle2' => $sig_info2['signatory_position']
);
$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$data[0] = array();
$data[0]['dmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['doh'] = $baseurl . "img/doh.png";

showReport('IC_Vaccination_Certificate', $params, $data, 'PDF');
?>