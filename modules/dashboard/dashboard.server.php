<?php
/**
* Created by EJ 11/26/2014
* AJAX for dashboard module
**/
if (empty($root_path)) $root_path="../../";
require($root_path.'include/inc_environment_global.php');
require("dashboard.common.php");

include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_cert_death.php'); //added rnel / rebranched carriane 01-19-18

function setDoctors($dept_nr) {
	$objResponse = new xajaxResponse();
	$dept_obj= new Department;

	$result = $dept_obj->getDoctorsByDepartment($dept_nr);

	if($result->RecordCount()){
		while($row = $result->FetchRow()) {
			$objResponse->addScriptCall("setDoctors",$row['name'],$row['personell_nr']);
		}
	}else
		$objResponse->addScriptCall("setDoctors",'No Doctor Available','');

	return $objResponse;
}

function setDepartments($doc_nr) {
	$objResponse = new xajaxResponse();
	$dept_obj= new Department;

	$result = $dept_obj->getDeptofDoctor($doc_nr);

	$objResponse->addScriptCall("setDepartments",$result['name_formal'],$result['nr']);
	

	return $objResponse;
}

function savedeathcause($cause,$pid,$encounter_nr,$isinfant){
	$objResponse = new xajaxResponse();
	$deathData = array();

	$death_cert_obj = new DeathCertificate($pid);
	
	if($isinfant){
		$cause = array(
			'mainDisease' => utf8_encode($cause['mainDisease']),
			'otherDisease' => utf8_encode($cause['otherDisease']),
			'mainMaternal' => utf8_encode($cause['mainMaternal']),
			'otherMaternal' => utf8_encode($cause['otherMaternal']),
			'otherRelevant' => utf8_encode($cause['otherRelevant'])
		);
			
	}else{
		$cause = array(
			'immediate' => utf8_encode($cause['immediate']),
			'immediate_int' => utf8_encode($cause['immediate_int']),
			'antecedent' => utf8_encode($cause['antecedent']),
			'antecedent_int' => utf8_encode($cause['antecedent_int']),
			'underlying' => utf8_encode($cause['underlying']),
			'underlying_int' => utf8_encode($cause['underlying_int']),
			'other' => utf8_encode($cause['other'])
		);
	}

	$deathData['death_cause'] = json_encode($cause);
	$deathData['pid'] = $pid;

	$userid = $_SESSION["sess_temp_userid"];

	$historyData = array(
		'pid' => $pid,
		'encounter' => $encounter_nr,
		'user' => $userid,
		'death_cause' => $deathData['death_cause']
	);

	$resHistoryData = $death_cert_obj->deathCauseHistory($historyData);

	if($resHistoryData) {
		$objResponse->alert('Cause of Death Successfully Saved');
		$objResponse->addScriptCall('refresh');
	} else {
		$objResponse->alert('Error On Processing Data');
		$objResponse->addScriptCall('refresh');
	}

	return $objResponse;
}
$xajax->processRequests(); 
?>