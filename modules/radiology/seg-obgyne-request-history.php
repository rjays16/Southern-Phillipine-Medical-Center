<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once($root_path . 'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

global $db;

$srvObj=new SegRadio;
$person_obj=new Person;
$enc_obj=new Encounter;
$pers_obj=new Personell;


$pid = $_GET['pid'];
$encounter_nr = $_GET['encounter_nr'];

$person = $person_obj->BasicDataArray($pid);

$request_name = $person['name_first']." ".$person['name_middle']." ".$person['name_last'];
$request_name = ucwords(strtolower($request_name));
$request_name = htmlspecialchars($request_name);
$age = $person['age'];
$civil_status = $person['civil_status'];

if ($person['sex']=='m')
$sex = 'MALE';
elseif($person['sex']=='f')
$sex = 'FEMALE';
else
$sex = 'unspecified';

$street_name = $person['street_name'];
$brgy_name = $person['brgy_name'];
$mun_name = $person['mun_name'];
$prov_name = $person['prov_name'];

if ($street_name){
if ($brgy_name!="NOT PROVIDED")
	$street_name = $street_name.", ";
else
	$street_name = $street_name.", ";
}


if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
$brgy_name = "";
else
$brgy_name  = $brgy_name.", ";

if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
$mun_name = "";
else{
if ($brgy_name)
	$mun_name = $mun_name;

}

if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
$prov_name = "";


if(stristr(trim($mun_name), 'city') === FALSE){
if ((!empty($mun_name))&&(!empty($prov_name))){
	if ($prov_name!="NOT PROVIDED")
		$prov_name = ", ".trim($prov_name);
	else
		$prov_name = "";
}else{
	
	$prov_name = "";
}
}else
$prov_name = ", ".trim($prov_name);

$request_address = $street_name.$brgy_name.$mun_name.$prov_name;

$insurance = $person_obj->getInsurance_nr($pid);
if (!$insurance['insurance_nr'])
	$insurance_nr = 'Not a Member';
else
	$insurance_nr = $insurance['insurance_nr'];

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['HTTP_HOST'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

//---------------------------------------------------------------------------------------------//


$requestObj = $srvObj->getAllObRequestByPid($pid,$encounter_nr);

if ($requestObj) {
	$i=1;
	$data = array();
	while($result=$requestObj->FetchRow()) {

		$date_request = date("m/d/Y h:i A",strtotime($result['serv_dt']));

		if ($result['request_doc'])
			$doctor = 'DR. '.mb_strtoupper($result['request_doc']);
        else
            $doctor = 'DR. '.mb_strtoupper($result['manual_doctor']);

		if ($result['is_cash'])
			$payment_mode = 'Cash';
		else
			$payment_mode = 'Charge';

		$data[] = array(
			'id' => $i,
			'req_date' => $date_request,
			'refno' => $result['refno'],
			'request' => $result['request_item'],
			'req_doctor' => $doctor,
			'encoder' => $result['encoder'],
			'payment' => $payment_mode,
			'status' => $result['status']
		);
		
		$i++;	
	}
}

$params = array(
   	'name' => $request_name,
   	'age' => $age,
   	'address' => $request_address,
   	'pid' => $pid,
   	'encounter_nr' => $encounter_nr,
   	'civil_status' => strtoupper($civil_status),
   	'sex' => $sex,
   	'phic' => $insurance_nr
);


showReport('RADIO_request_hist', $params, $data, 'PDF');