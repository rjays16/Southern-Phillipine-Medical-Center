<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/inc_environment_global.php';
	include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

require_once($root_path.'include/care_api_classes/class_med_abstract.php');
$med_obj=new MedAbstract;

include_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();
#	include_once($root_path.'include/care_api_classes/class_personell.php');
#	$personell_obj=new Personell;


include($root_path.'modules/reports/reports/parameters.php');


	if ($_GET['id']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['id']))){	
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}	
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}


function checkNullDate($date){
	return $date == '0000-00-00' || $date === NULL || empty($date) || $date == '' ? true : false;
} 


$medAbstInfo = $med_obj->getMedAbsRecord($encounter_nr);
if($medAbstInfo){
	$civil_status = $medAbstInfo['civil_status'];
	$age = $medAbstInfo['age'];
}
$dateHeader = "";
if(checkNullDate($admission_dt)){
	$dateHeader = date('M d, Y', strtotime($encounter_date));
}else{
	$dateHeader = date('M d, Y', strtotime($admission_dt));
}


// $from = date('Y-m-d', strtotime($_GET['fromdte']));
// $to = date('Y-m-d', strtotime($_GET['todte']));
// $date2 = "";
// if($medAbstInfo["modify_dt"]!=NULL){
// 	$date_created = date("m/d/Y",strtotime($medAbstInfo["modify_dt"]));
// 	$date2 = date("F j, Y",strtotime($medAbstInfo["modify_dt"]));
// }elseif($medAbstInfo["create_dt"]!=NULL){
// 	$date_created = date("m/d/Y",strtotime($medAbstInfo["create_dt"]));
// 	$date2 = date("F j, Y",strtotime($medAbstInfo["create_dt"]));
// }else
// 	$date_created = @formatDate2Local(date('Y-m-d'),$date_format);
$date2 = isset($medAbstInfo['modify_dt']) && $medAbstInfo['modify_dt'] != '' ? $medAbstInfo['modify_dt'] : $medAbstInfo['create_dt'];
$day = "";
$month = "";
$year = "";
$date2 = date('jS F Y',strtotime($date2));
$date2 = explode(' ', $date2);

$day = $date2[0];
$month = $date2[1];
$year = $date2[2];

$sex = ($sex == "m")? "MALE":"FEMALE";
if (trim($brgy_name)=='NOT PROVIDED')
	$brgy_name = "";
else
	$brgy_name = trim($brgy_name).", ";

if (trim($mun_name)=='NOT PROVIDED')
	$mun_name = "";
if(trim($prov_name)=='NOT PROVIDED')
    $prov_name="";
$address = trim($street_name != '' ? $street_name . ', ' : '')." ".$brgy_name.trim($mun_name)." ".trim($prov_name);


$doctor_res = $pers_obj->getIPBMDoctorByNr($medAbstInfo['dr_nr']);

if($doctor_res){
	$name_doctor = $doctor_res['name'];
	$lic_nr = $doctor_res['license_nr'];
}else{
	$name_doctor = '';
	$lic_nr = '';
}




if($medAbstInfo['modify_id'])
	$encoded_by = $medAbstInfo['modify_id'];
else
	$encoded_by = $medAbstInfo['create_id'];

	$baseurl = sprintf(
	    "%s://%s%s",
	    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
	    $_SERVER['HTTP_HOST'],
	    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
	);
	$params = array(
				"ipbm" => IPBM_HEADER,
				"hospital_name" => mb_strtoupper($hosp_name),
				'r_ipbm' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR ."ipbm.png",
		    	'r_spmc' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR ."gui".DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR ."dmc_logo.jpg",
				"hrn"=> $pid."",
	            "homis_id"=> $homis_id."",
	            "encounter_nr"=> $encounter_nr."",
	            "date"=>$dateHeader,
	            "dateHeader"=>$dateHeader,
	            "patient_name"=> stripslashes(strtoupper($name_last)).", ".stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle)),
	            "age"=> $age."",
	            "gender"=> stripslashes(strtoupper($sex)),
	            "address"=> trim(stripslashes(strtoupper(rtrim($address, ', ')))),
	            "admission_date"=> date('M d, Y', strtotime($fromDate))."",
	            "purpose"=> $medAbstInfo['purpose'],
	            "civil_status" => strtoupper($civil_status),
	            "diagnosis"=>$medAbstInfo['diagnosis'],
	            "remarks"=>$medAbstInfo['remarks'],
	            "attending_physician"=> $name_doctor != '' ? strtoupper($name_doctor) . ", MD" : '______________________________________',
	            "license_nr" => $lic_nr,
	            "prov_name" => "Davao City",
	            "day" => $day,
	            "month" => $month,
	            "year" => $year,
	           );
	$data = array(
			0 => 
				array(

					"brief_hist" => $medAbstInfo['brief_hist'],
					"mental_status" => $medAbstInfo['mental_status'],
					"diagnosis" => $medAbstInfo['diagnosis'],
					"remarks" => $medAbstInfo['remarks']
				)
		
		);

#-------------------------------------------------------------------------------------

showReport('medical_abstract_certificate_ipbm',$params,$data,'pdf');
?>