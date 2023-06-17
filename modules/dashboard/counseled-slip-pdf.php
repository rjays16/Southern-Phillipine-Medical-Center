<?php

require_once('roots.php');
require_once $root_path . 'include/inc_environment_global.php';
require_once($root_path . 'include/inc_jasperReporting.php');

include_once($root_path . 'include/care_api_classes/class_counseled_slip.php');
$counseled_obj = new CounseledSlip($_GET['encounter_nr']);
global $db;

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);



$counseledDWR = $counseled_obj->getCouseledDischarged(1);
$counseledMWR = $counseled_obj->getCouseledMonitoring(1);
$counseledA = $counseled_obj->getCouseled();
$counseledDWOR = $counseled_obj->getCouseledDischarged(0);
$counseledDWOM = $counseled_obj->getCouseledMonitoring(0);
$selectedType = $counseled_obj->getOrderType();
$dietOrder = $counseled_obj->getDietOrder($selectedType['selected_type'], $selected_type['refno']);

if ($dietOrder) {
    $diet = $dietOrder;
} else {
    $diet = 'No Diet Order';
}
if ($counseledDWR) {
    $counseled = $counseledDWR;
    $counseledType = 'With Refferal for Discharge';
} else if ($counseledMWR) {
    $counseled = $counseledDWR;
    $counseledType = 'With Refferal for Monitoring';
} else if ($counseledA) {
    $counseled = $counseledA;
    $counseledType = 'Counseled';
} else if ($counseledDWOR) {
    $counseled = $counseledDWOR;
    $counseledType = 'Without Refferal for Discharge';
} else if ($counseledDWOM) {
    $counseled = $counseledDWOM;
    $counseledType = 'Without Refferal for Monitoring';
} else {
    $counseled = array();
    $counseledType = 'No Data Inputed';
}
$pid =  $counseled_obj->getPid();
$person =  $counseled_obj->getPersonDetail($pid['pid']);


// var_dump($counseled);
// die;
// $hospitalInfo = $hosp_obj->getAllHospitalInfo();
$params = array(
    'pid' => strtoupper($person['pid']),
    'name_last' => strtoupper($person['name_last']),
    'name_first' => strtoupper($person['name_first']),
    'name_middle' => strtoupper($person['name_middle']),
    'sex' => $person['sex'] == 'm' ? 'MALE' : 'FEMALE',
    'age' => strtoupper($person['age']),
    'counseledType' => $counseledType,
    'visited_dt' => date('M d, Y', strtotime($counseled['visited_dt'])),
    'assessment' => $counseled['assessment'],
    'plan' => $counseled['plan'],
    'in_charged' => $counseled['in_charged'],
    'diet_order' => $diet,
    'logo1' => $baseurl . './gui/img/logos/dmc_logo.jpg',
    'logo2' => $baseurl . './img/Logo_DOH.jpg',
    'logo3' => $baseurl . './img/default-user-image.png',
);
// var_dump($params);
// die;
$data = array();

showReport('counselingnotesReport', $params, $data, 'PDF');
