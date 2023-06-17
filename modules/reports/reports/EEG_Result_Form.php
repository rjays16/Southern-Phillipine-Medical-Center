<?php
/* author gelie 
 * 11/28/2015 
 */
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once($root_path . 'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_special_lab.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

global $db;
$srv_obj = new SegLab;
$spl_obj = new SegSpecialLab;
$pers_obj = new Personell;
$refno = $_GET['refno'];
$service_code = $_GET['service_code'];

$ref_info = $srv_obj->getLabItemInfo($refno, $service_code)->FetchRow();
$result = $spl_obj->getAllInfoEEGResult($refno, $service_code);

$pid = $ref_info['pid'];
$address = trim($db->GetOne("SELECT fn_get_complete_address('$pid') AS address"));
$name = stripslashes(strtoupper($ref_info['name_first'])).' '.stripslashes(strtoupper($ref_info['name_middle'])).' '.stripslashes(strtoupper($ref_info['name_last']));
$sex = ($ref_info['sex'] == 'f' ? 'F' : 'M');
$age = floor((time() - strtotime($ref_info['date_birth']))/31556926).' years old';
$service_name = $ref_info['service'];

if($ref_info['request_doctor_name']){
     $request_physician =  $ref_info['request_doctor_name']. ", MD.";
}
else {
    $request_physician =  $ref_info['manual_doctor'];
}
$clinical_data = $ref_info['clinical_info'];

if ($doctor = $pers_obj->get_Person_name3($result['consult_doctor'])) {
$dr = $doctor->FetchRow();
$doctor_name = mb_strtoupper($dr['dr_name']) . ", MD, " . $result['doctor_title'];

}
else {
  $doctor_name = mb_strtoupper($doctor['dr_name']);   

}

//$doctor = $pers_obj->get_Person_name3($result['consult_doctor']);
//$dr = $doctor->FetchRow();

/*$doctor_name = mb_strtoupper($dr['dr_name']) . ", MD, " . $result

['doctor_title'];*/

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$params = array(
    "HRN" => $pid,
    "name" => utf8_decode(trim($name)),
    "age" => $age,
    "address" => utf8_decode(trim($address)),
    "sex" => $sex,
    "physician" => utf8_decode(trim(mb_strtoupper($request_physician))),
    "clinical" => $clinical_data,
    "medication" => $result['medication'],
    "date" => strtoupper(date("m/d/Y", strtotime($result['perform_dt']))),
    "summary" => $result['summary'],
    "interpret" => $result['interpretation'],
    "eeg_in_charge" => strtoupper($result['create_id']),
    "consult_doc" => mb_strtoupper($doctor_name),
);

if(file_exists($root_path . 'fotos/signatures/' . $dr['personell_nr'] . '.jpg')) {
    $params['signatureUrl'] = $baseurl . 'fotos/signatures/' . $dr['personell_nr'] . '.jpg';
} else {
    $params['signatureUrl'] = null;
}

$data[0] = array();
$data[0]['spmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['doh'] = $baseurl . "img/doh.png";
showReport('SPLAB_EEG_Result', $params, $data, 'PDF');
?>