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

include_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();
#	include_once($root_path.'include/care_api_classes/class_personell.php');
#	$personell_obj=new Personell;

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
$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
// echo "sql = ".$enc_obj->sql;
// var_dump($confCertInfo);die();

$wardName = $ward_obj->WardName($encInfo['current_ward_nr']);
$from = date('Y-m-d', strtotime($_GET['fromdte']));
$to = date('Y-m-d', strtotime($_GET['todte']));
// var_dump($or_no);die();
	// var_dump($row);die();
if($confCertInfo["modify_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($confCertInfo["modify_dt"]));
}elseif($confCertInfo["create_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($confCertInfo["create_dt"]));
}else
	$date_created = @formatDate2Local(date('Y-m-d'),$date_format);

$sig_info = $pers_obj->get_Signatory('confcert-ipbm');

if ($confCertInfo['dr_nr']){
	$officer_info = $pers_obj->getPersonellInfo($confCertInfo['dr_nr']);
	$officer_middleInitial = "";
	if (trim($officer_info['name_middle'])!=""){
		$thisMI=split(" ",$officer_info['name_middle']);
		foreach($thisMI as $value){
			if (!trim($value)=="")
				$officer_middleInitial .= $value[0];
		}
			if (trim($officer_middleInitial)!="")
			$officer_middleInitial = " ".$officer_middleInitial.".";
	}

	$name_officer = trim($officer_info['name_first'])." ".trim($officer_info['name_2'])." ".$officer_middleInitial." ".trim($officer_info['name_last']);

	while($result = $sig_info->FetchRow()){
		if($confCertInfo['dr_nr'] == $result['personell_nr'])
			$officer_position = $result['signatory_position'];
	}

}


	$medico_y=" ";
	$medico_n=" ";
	$medico_details="";
	if($confCertInfo['is_medico_legal']){
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
			if($DOI!='0000-00-00' && $DOI!='1970-01-01') $DOI_val = date('M d, Y', strtotime($DOI));
			else $DOI_val = "Not Indicated";
			$medico_details.="DOI: ".$DOI_val;
		}
	}
	else {
		$medico_n="X";
	}
	$sex = ($sex == "m")? "MALE":"FEMALE";
#$address = trim($street_name).", ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
if (trim($brgy_name)=='NOT PROVIDED')
	$brgy_name = "";
else
	$brgy_name = trim($brgy_name).", ";

if (trim($mun_name)=='NOT PROVIDED')
	$mun_name = "";

$address = trim($street_name).", ".$brgy_name.trim($mun_name)." ".trim($prov_name);


if (($encounter_type==1)||($encounter_type==2)){
	$fromDate= "".@formatDate2Local($er_opd_datetime,$date_format);
	$name_doctor = $er_opd_admitting_physician_name;
}else{
	$fromDate= "".@formatDate2Local($admission_dt,$date_format);
	$name_doctor = $attending_physician_name;
}
/*
if ((isset($_GET['doc_name'])) && (!empty($_GET['doc_name']))){
	$name_doctor = $_GET['doc_name'];
}
#$name_doctor = str_replace('dr.', "", $name_doctor);
#$name_doctor = ereg_replace("dr.", "", $name_doctor);
$name_doctor = preg_replace("/(dr.)|(Dr.)/", "", $name_doctor);
*/
$name_doctor = $confCertInfo['attending_doctor'];

$toDate= "".@formatDate2Local($discharge_dt,$date_format);

if (empty($name_doctor)) $name_doctor = "_____________________";

if (empty($wardName)){
	$wardName = " ";
}else{
	$wardName = ' at '.$wardName.' ward';
}

if($confCertInfo['modify_id'])
	$encoded_by = $confCertInfo['modify_id'];
else
	$encoded_by = $confCertInfo['create_id'];

// added by carriane 12/12/18;
// adjust font size if length exceeds to 59 characters
if(strlen($address) > 59){
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
	            "address"=> trim(stripslashes(strtoupper($address))),
	            'address_short' => trim(stripslashes(strtoupper($address_short))),
	            "admission_date"=> date('M d, Y', strtotime($fromDate))."",
	            "requestor"=> $confCertInfo['requested_by'],
	            "relation"=> $confCertInfo['relation_to_patient'],
	            "purpose"=> $confCertInfo['purpose'],
	            "civil_status" => strtoupper($civil_status),
	            "diagnosis"=>$er_opd_diagnosis,
	            "attending_physician"=> $name_officer,
	            "position"=> $officer_position."",
	            "medico_y"=> $medico_y,
	            "medico_n"=> $medico_n,
	            "medico_details"=> $medico_details,
	            "purpose_text" => '_________________________________________________________________________purpose(s) only.',
	            'encoded_by' => $encoded_by
	           );

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

showReport('confinement_certificate_ipbm',$params,array(0 => ""),'pdf');
?>