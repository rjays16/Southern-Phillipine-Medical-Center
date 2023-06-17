<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/inc_environment_global.php';
include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();
	
#	include_once($root_path.'include/care_api_classes/class_personell.php');
#	$personell_obj=new Personell;

define('IPBMOPD_enc', '14');

if ($_GET['id']) {
	if (!($enc_info = $enc_obj->getEncounterInfo($_GET['id']))){
		#echo "enc_obj->sql = '".$enc_obj->sql."' <br> \n";		
		echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
		exit();
	}
	#echo "enc_obj->sql = '".$enc_obj->sql."' <br> \n";		
	extract($enc_info);
	#$personell_obj->getPersonellInfo($referrer_dr);
}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
		exit();	
}

$obj_medCert = new MedCertificate($encounter_nr);
$medCertInfo = $obj_medCert->getMedCertRecord($encounter_nr,$referral_nr,$cert_nr);
// var_dump($obj_medCert->sql);exit();

	if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
		$row['hosp_addr1']   = "Malaybalay, Bukidnon";
	}

$date_created = date("m/d/Y",strtotime($medCertInfo["scheduled_date"]));

//Content text
$sex = ($sex == "m")? "MALE":"FEMALE";
#$address = trim($street_name).", ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
if (trim($brgy_name)=='NOT PROVIDED')
	$brgy_name = "";
else
	$brgy_name = trim($brgy_name).", ";

if (trim($mun_name)=='NOT PROVIDED')
	$mun_name = "";

if(trim($prov_name)=='NOT PROVIDED')
    $prov_name = '';

$address = trim($street_name).", ".$brgy_name.trim($mun_name)." ".trim($prov_name);

#doctor's name was commented by VAN 04-28-08
if (($medCertInfo['consultation_date']!='0000-00-00') && ($medCertInfo['consultation_date']!=""))
	$er_opd_datetime = $medCertInfo['consultation_date'];


if (($er_opd_datetime!='0000-00-00') && ($er_opd_datetime!=""))
    $consult_date =  $er_opd_datetime;
else    
    $consult_date =  $admission_dt;

$fromDate= "".@formatDate2Local($consult_date,$date_format);

if($encounter_type == IPBMOPD_enc)
	$toDate = "".@formatDate2Local($consult_date,$date_format);
else
	$toDate= "".@formatDate2Local($discharge_dt,$date_format);

if (empty($age)) $age = "___";

$dateconfine = $fromDate;

$from = date('Y-m-d', strtotime($_GET['fromdte']));
$to = date('Y-m-d', strtotime($_GET['todte']));
// var_dump($or_no);die();
	// var_dump($row);die();
	$sig_info = $pers_obj->get_Signatory('medcert-ipbm');
	$name_officer = mb_strtoupper($sig_info['name']);
	$officer_position = $sig_info['signatory_position'];
	$officer_title = $sig_info['signatory_title'];
	$medico_y=" ";
	$medico_n=" ";
	$medico_details="";
	if($medCertInfo['is_medico_legal']){
		$medico_y="X";
		$medico_cases = $enc_obj->getEncounterMedicoCases($encounter_nr,$pid);
		#echo "sql = ".$enc_obj->sql;
		$NOI = '';
		if ($medico_cases){
			while($result=$medico_cases->FetchRow()) {
							if ($result['medico_cases']=='Others')
									$result['medico_cases'] = $result['description'];
				$NOI .= $result['medico_cases'].", ";
			}
			$medico_details="NOI: ".$NOI."\n\n";
		}
		if ($NOI){
			if ($TOI!='00:00:00'){
				if (strcmp($encInfo['TOI'],'24:')==1) $TOI_val = "12:".substr($encInfo['TOI'],3,2)." AM";
				else $TOI_val = date("h:i A",strtotime($TOI));
			} 
			else $TOI_val = "Not Indicated";
			$medico_details.="POI: ".$POI."\n\n";
			$medico_details.="TOI: ".$TOI_val."\n\n";
			if($DOI!='0000-00-00' && $DOI!='1970-01-01') $DOI_val =  date('M d, Y', strtotime($DOI));
			else $DOI_val = "Not Indicated";
			$medico_details.="DOI: ".$DOI_val."\n\n";
		}

		$civil_case_no = $medCertInfo['civil_case_no'] ? $medCertInfo['civil_case_no'] : "";
		$court = $medCertInfo['court'] ? $medCertInfo['court'] : "";
		$judge = $medCertInfo['judge'] ? $medCertInfo['judge'] : "";
		$civil_case_no_label = 'Criminal/Civil Case No: ';
		$court_label = 'Court: ';
		$judge_label = 'Judge: ';
	}
	else {
		$medico_n="X";
	}

	if($medCertInfo['modify_id'])
		$encoded_by = $medCertInfo['modify_id'];
	else
		$encoded_by = $medCertInfo['create_id'];

	if($medCertInfo['is_doc_sig']){
		if($medCertInfo['dr_name'] != '')
			$doc_name = $medCertInfo['dr_name'];
		else
			$doc_name = $medCertInfo['dr_nr'];
		
		$doc_position = "Attending Physician";
	}

	if(strlen($doc_name) > 34){
		$doc_name2 = $doc_name;
		$doc_name = "";
	}

	// added by carriane 12/12/18;
	// adjust font size if length exceeds to 59 characters
	if(strlen($address) > 70){
		$address_short = $address;
		$address = '';
	}
	// end carriane

	$baseurl = sprintf(
	    "%s://%s%s",
	    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
	    $_SERVER['HTTP_HOST'],
	    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
	);

	$fromIPBM = $_GET['from'];
	if(strlen($civil_case_no) > 90){
		$civil_case_no2 = $civil_case_no;
		$civil_case_no = '';
	}else
		$civil_case_no2 = '';

	$params = array(
				'r_ipbm' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR ."ipbm.png",
    			'r_spmc' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."gui".DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR ."dmc_logo.jpg",
				"hrn"=> $pid."",
	            "homis_id"=> $homis_id."",
	            "encounter_nr"=> $encounter_nr."",
	            "date"=>date('M d, Y', strtotime($date_created))."",
	            "patient_name"=> stripslashes(strtoupper($name_last)).", ".stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle)),
	            "age"=> $age."",
	            "gender"=> stripslashes(strtoupper($sex)),
	            "address"=> trim(stripslashes(strtoupper(rtrim($address, ', ')))),
	            "address_short"=> trim(stripslashes(strtoupper(rtrim($address_short, ', ')))),
	            "admission_date"=> date('M d, Y', strtotime($dateconfine))."",
	            "discharge_date"=> date('M d, Y', strtotime($toDate))."",
	            "purpose"=> $medCertInfo['purpose']?stripslashes($medCertInfo['purpose']):"",
	            "attending_physician"=> ($doc_name?$doc_name.",MD":""),
	            "attending_physician2"=> ($doc_name2?$doc_name2.",MD":""),
	            "license_nr" => $medCertInfo['lic_nr'],
	            "position"=> ($doc_position?$doc_position:""),
	            "diagnosis"=>stripslashes($medCertInfo['diagnosis_verbatim']),
	            "remarks_recom" => stripslashes($medCertInfo['remarks_recom']),
	            "status" => strtoupper($civil_status),
	            "medico_y"=> $medico_y,
	            "medico_n"=> $medico_n,
	            "medico_details"=> $medico_details,
	            "civil_case_no" => $civil_case_no,
	            "civil_case_no2" => $civil_case_no2,
	            /*"civil_case_no3" => (strlen($civil_case_no) > 60 ? substr($civil_case_no,60) : " "),*/
	            "court" => $court,
	            "judge" => $judge,
	            'civil_case_no_label' => $civil_case_no_label,
	            'court_label' => $court_label,
	            'judge_label' => $judge_label,
	            'encoded_by' => $encoded_by
	           );
// var_dump($medCertInfo['create_id']);die();
$data=array();
if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
	$row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr']   = "Quezon Ave., Digos City, Davao del Sur";
}
#-------------------------------------------------------------------------------------

showReport('medical_certificate_ipbm',$params,array(0 => ""),'pdf');
?>