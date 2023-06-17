<?php
/*created by art 04/09/2014*/
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
$obj_medCert = new SegICCertMed;
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj =new Personell;
global $db;
$enc = $_GET['encounter_nr'];
$refno = $_GET['refno'];
$encInfo=$enc_obj->getEncounterInfo($enc);
$medcertDriverInfo = $obj_medCert->getAllInfoCertMedDriver($refno);

if (trim($encInfo['street_name'])){
		if (trim($encInfo["brgy_name"])==NULL)
			$street_name = trim($encInfo['street_name'])." ";
		else
			$street_name = trim($encInfo['street_name']).", ";
}else{
		$street_name = "";
}

if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
	$brgy_name = "";
else
	$brgy_name  = trim($encInfo["brgy_name"]).", ";

if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
	$mun_name = "";
else{
	if ($brgy_name)
		$mun_name = trim($encInfo["mun_name"]);
	else
		$mun_name = trim($encInfo["mun_name"]);
}

if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
	$prov_name = "";
else
	$prov_name = trim($encInfo["prov_name"]);

if(stristr(trim($encInfo["mun_name"]), 'city') === FALSE){
	if ((!empty($encInfo["mun_name"]))&&(!empty($encInfo["prov_name"]))){
		if ($prov_name!="NOT PROVIDED")
			$prov_name = ", ".trim($prov_name);
		else
			$prov_name = trim($prov_name);
	}else{
		$prov_name = "";
	}
}else
	$prov_name = "";

$address 		= trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);
$gender 		= ($encInfo['sex'] == 'f' ? 'Female' : 'Male');

// added by carriane 12/12/18;
// adjust font size if length exceeds to 59 characters
if(strlen($address) > 80){
	$address_short = $address;
	$address = '';
}
// end carriane

#doctor
$physician_num 	= $medcertDriverInfo['exam_physician'];
$physician 		= $pers_obj->get_Person_name($physician_num);
$dr_name 		= $physician['dr_name'].', MD';
$dr_license 	= $physician['license_nr'];


$params = array("surname"					=>	$encInfo['name_last'],
	            "firstname"					=>	$encInfo['name_first'] .' '. $encInfo['name_2'] .' '.$encInfo['name_3'],
	            "middlename"				=>	$encInfo['name_middle'],
	            "address"					=>	rtrim($address, ','),
	            "address_short"				=> 	rtrim($address_short, ','),
	            "age"						=>	$encInfo['age'],
	            "gender"					=>	$gender,
	            "height"					=>	$medcertDriverInfo['height'].' CM',
	            "weight"					=>	$medcertDriverInfo['weight'].' KG',
	            "marital"					=>	ucfirst($encInfo['civil_status']),
	            "gp_normal"					=>	$gp_normal 					= ($medcertDriverInfo['general_physique'] == 'Normal' ? 'x' : ''),
	            "gp_disability"				=>	$gp_disability 				= ($medcertDriverInfo['general_physique'] == 'Abnormal' ? 'x' : ''),
	            "abnormality"				=>	$medcertDriverInfo['abnormality'],
	            "contagious_none"			=>	$contagious_none 			= ($medcertDriverInfo['with_disease'] == '0' ? 'x' : ''),
	            "contagious_disease"		=>	$contagious_disease 		= ($medcertDriverInfo['with_disease'] == '1' ? 'x' : ''),
	            "diseases"					=>	$medcertDriverInfo['contagious_diseases'],
	            "bp"						=>	$medcertDriverInfo['systole'] .' / '.$medcertDriverInfo['diastole'],
	            "left_eye_snellen"			=>	$medcertDriverInfo['left_eye_snellen'],
	            "visual_left_with"			=>	$visual_left_with 			= ($medcertDriverInfo['left_eye_other']=="1" ? 'x' : ''),
	            "visual_left_colorblind"	=>	$visual_left_colorblind 	= ($medcertDriverInfo['left_eye_other']=="2" ? 'x' : ''),
	            "right_eye_snellen"			=>	$medcertDriverInfo['right_eye_snellen'],
	            "visual_right_with"			=>	$visual_right_with 			= ($medcertDriverInfo['right_eye_other']=="1" ? 'x' : ''),
	            "visual_right_colorblind"	=>	$visual_right_colorblind 	= ($medcertDriverInfo['right_eye_other']=="2" ? 'x' : ''),
	            "hearing_left_normal"		=>	$hearing_left_normal 		= ($medcertDriverInfo['left_ear'] == "1" ? 'x' : ''),
	            "hearing_left_disability"	=>	$hearing_left_disability 	= ($medcertDriverInfo['left_ear'] == "2" ? 'x' : ''),
	            "hearing_left_special"		=>	$hearing_left_special 		= ($medcertDriverInfo['left_ear'] == "4" ? 'x' : ''),
	            "hearing_right_normal"		=>	$hearing_right_normal 		= ($medcertDriverInfo['right_ear'] == "1" ? 'x' : ''),
	            "hearing_right_disability"	=>	$hearing_right_disability 	= ($medcertDriverInfo['right_ear'] == "2" ? 'x' : ''),
	            "hearing_right_special"		=>	$hearing_right_special 		= ($medcertDriverInfo['right_ear'] == "4" ? 'x' : ''),
	            "upper_left_normal"			=>	$upper_left_normal 			= ($medcertDriverInfo['left_upper_extremities'] == "1" ? 'x' : ''),
	            "upper_left_disability"		=>	$upper_left_disability 		= ($medcertDriverInfo['left_upper_extremities'] == "2" ? 'x' : ''),
	            "upper_left_special"		=>	$upper_left_special 		= ($medcertDriverInfo['left_upper_extremities'] == "3" ? 'x' : ''),
	            "upper_right_normal"		=>	$upper_right_normal 		= ($medcertDriverInfo['right_upper_extremities'] == "1" ? 'x' : ''),
	            "upper_right_disability"	=>	$upper_right_normal 		= ($medcertDriverInfo['right_upper_extremities'] == "2" ? 'x' : ''),
	            "upper_right_special"		=>	$upper_right_normal 		= ($medcertDriverInfo['right_upper_extremities'] == "3" ? 'x' : ''),
	            "lower_left_normal"			=>	$lower_left_normal 			= ($medcertDriverInfo['left_lower_extremities'] == "1" ? 'x' : ''),
	            "lower_left_disability"		=>	$lower_left_disability 		= ($medcertDriverInfo['left_lower_extremities'] == "2" ? 'x' : ''),
	            "lower_left_special"		=>	$lower_left_special 		= ($medcertDriverInfo['left_lower_extremities'] == "3" ? 'x' : ''),
	            "lower_right_normal"		=>	$lower_right_normal 		= ($medcertDriverInfo['right_lower_extremities'] == "1" ? 'x' : ''),
	            "lower_right_disability"	=>	$lower_right_disability 	= ($medcertDriverInfo['right_lower_extremities'] == "2" ? 'x' : ''),
	            "lower_right_special"		=>	$lower_right_special 		= ($medcertDriverInfo['right_lower_extremities'] == "3" ? 'x' : ''),
	            "comments_fit"				=>	$comments_fit 				= ($medcertDriverInfo['comment_drive'] == "1" ? 'x' : ''),
	            "comments_conditions"		=>	$comments_conditions 		= ($medcertDriverInfo['comment_drive'] == "3" ? 'x' : ''),
	            "comments_conditions_a"		=>	$comments_conditions_a 		= ($medcertDriverInfo['conditions'] == "1" ? 'x' : ''),
	            "comments_conditions_b"		=>	$comments_conditions_b 		= ($medcertDriverInfo['conditions'] == "2" ? 'x' : ''),
	            "comments_conditions_c"		=>	$comments_conditions_c 		= ($medcertDriverInfo['conditions'] == "3" ? 'x' : ''),
	            "comments_conditions_d"		=>	$comments_conditions_d 		= ($medcertDriverInfo['conditions'] == "4" ? 'x' : ''),
	            "comments_conditions_e"		=>	$comments_conditions_e 		= ($medcertDriverInfo['conditions'] == "5" ? 'x' : ''),
	            "comments_remarks"			=>	$medcertDriverInfo['remarks'],
	            "datetime"					=>	date('F j, Y, g:i a', strtotime($medcertDriverInfo['create_dt'])),
	            "date"						=>	date( "F j, Y", strtotime($medcertDriverInfo['create_dt'])),
	            "doctor_name"				=>	$dr_name,
	            "license"					=>	$dr_license,
	            "control"					=>	$medcertDriverInfo['control_number'],
	           );
$data[0] =array();
$top_dir = 'modules';
$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

//$data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
//$data[0]['image_02'] = $baseurl . "img/doh.png";
#--------------------------------------------------------------------------------------

showReport('medcert_drivers',$params,$data,'pdf');
?>