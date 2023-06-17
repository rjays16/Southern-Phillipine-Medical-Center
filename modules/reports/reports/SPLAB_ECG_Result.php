<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_special_lab.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

global $db;
$enc_obj =new Encounter;
$person_obj = new Person;
$srv_obj = new SegLab;
$spl_obj = new SegSpecialLab;
$pers_obj = new Personell;
$refno = $_GET['refno'];

$ref_info = $srv_obj->getAllLabInfoByRefNo($refno, 'SPL')->FetchRow();
if($ref_info['encounter_nr']){
$encInfo = $enc_obj->getEncounterInfo($ref_info['encounter_nr']);
}
else{
	$encInfo = $person_obj->getAllInfoObject($ref_info['pid'])->FetchRow();
}
$result = $spl_obj->getAllInfoEcgResult($refno);

$middle_name = ($encInfo['name_middle']) ? stripslashes(strtoupper(substr($encInfo['name_middle'], 0, 1))).'.' : '';
$name = stripslashes(strtoupper($encInfo['name_last'])).', '.stripslashes(strtoupper($encInfo['name_first'])).' '.$middle_name;
$sex = ($encInfo['sex'] == 'f' ? 'F' : 'M');
//$age = floor((time() - strtotime($encInfo['date_birth']))/31556926).' yo';
$age = $encInfo['age'];
$clinic = $db->GetOne('SELECT name_formal FROM care_department WHERE nr="'. $encInfo['current_dept_nr'] .'"');

if (trim($encInfo['street_name'])){
    if (trim($encInfo["brgy_name"])!="NOT PROVIDED")
        $street_name = trim($encInfo['street_name']).", ";
    else
        $street_name = trim($encInfo['street_name']).", ";
}else{
    $street_name = " ";
}

if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
    $brgy_name = " ";
else
    $brgy_name  = trim($encInfo["brgy_name"]).", ";

if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
    $mun_name = " ";
else{
    if ($brgy_name)
        $mun_name = trim($encInfo["mun_name"]);
    else
        $mun_name = trim($encInfo["mun_name"]);
}

if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
    $prov_name = " ";
else
    $prov_name = trim($encInfo["prov_name"]);

if(stristr(trim($encInfo["mun_name"]), 'city') === FALSE){
    if ((!empty($encInfo["mun_name"]))&&(!empty($encInfo["prov_name"]))){
        if ($prov_name!="NOT PROVIDED")
            $prov_name = ", ".trim($prov_name);
        else
            $prov_name = trim($prov_name);
    }else{
        #$province = trim($prov_name);
        $prov_name = " ";
    }
}else
    $prov_name = " ";

$address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

$doctor = $pers_obj->get_Person_name3($result['prepared_by']);
# update by: syboy 03/30/2016 : meow
if ($doctor) {
    $dr = $doctor->FetchRow();
    $doctor_name = mb_strtoupper($dr['dr_name']) . ", MD";
    $doctor_lic = $dr['license_nr'];
}else{
    $doctor_name = "No Doctor Assigned";
    $doctor_lic = "";
}
# ended syboy

$cnt_impression = explode("\n", $result['impression']);

if(sizeof($cnt_impression) > 1) {
	$cnt = 1;
	$cnt_arry = count($cnt_impression)-1;
	foreach ($cnt_impression as $key => $value) {
		$trim_value = trim($value);
		$cnt_value = array();

		if(!empty($trim_value)) {
			$impressions .= '<span><b>' . $cnt++ . '.)</b> </span><span>' . $trim_value . '</span>';

			if ($key != $cnt_arry) {
				$impressions .= '<br/><br/>';
			}
		}
	}
}
else
	$impression = $result['impression'];

$params = array(
    "name" => utf8_decode(trim($name)),
    "hrn" => $encInfo['pid'],
    "age" => $age,
    "sex" => $sex,
    "date" => strtoupper(date("d-M-Y", strtotime($result['result_date']))),
    "address" => utf8_decode(trim($address)),
    "clinic" => ($clinic) ? $clinic : 'N/A',
    "rhythm" => $result['rhythm'],
    "axis" => $result['axis'],
    "atrial" => $result['atrial'],
    "ventricular" => $result['ventricular'],
    "interval" => $result['interval'],
    "qrs" => $result['qrs'],
    "qt" => $result['qt'],
    "position" => $result['position'],
    "input_1" => $result['input_1'],
    "input_2" => $result['input_2'],
    "input_3" => $result['input_3'],
    "impression" => strtoupper($impression),
    "impressions" => strtoupper($impressions),
    "prepared_by" => $doctor_name.' (sgd.)',
    "license" => "License #: " . $doctor_lic,
);


$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$data[0] = array();
$data[0]['spmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['doh'] = $baseurl . "img/doh.png";
showReport('SPLAB_ECG_Result', $params, $data, 'PDF');
?>