<?php 
/*
 * Author : gelie
 * Date : 11/21/2015
 */

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
$params->put('Name_of_user', strtoupper($_SESSION['sess_user_name']));

global $db;

$date_from = date('Y-m-d',$_GET['from_date']);
$date_to = date('Y-m-d',$_GET['to_date']);

$user_id = $_SESSION['sess_temp_userid']; 

//Get patient type
$ptype_param = $_GET['param'];
$patient_type = substr($ptype_param, strrpos($ptype_param, "-") + 1);

if($patient_type == '1'){
	$encounter_type = "AND ce.`encounter_type` = 2";		//opd
	$patient_type = "OUTPATIENTS";
}
else if($patient_type == '2'){
	$encounter_type = "AND ce.`encounter_type` = 1";		//er
	$patient_type = "ER PATIENTS";
}
else if($patient_type == '3'){
	$encounter_type = "AND ce.`encounter_type` IN (3,4)";	//inpatient
	$patient_type = "INPATIENTS";
}
else{
	$encounter_type = "";
	$patient_type = "ALL PATIENTS";
}

$params->put('patient_type', $patient_type);

$sql = "SELECT 
		  fn_get_person_name (ce.`pid`) AS patient,
		  ce.`pid` AS hrn,
		  bt.`encounter_nr` AS case_no,
		  bt.`bill_nr`,
		  CASE
		  	WHEN bt.`action_taken` = 'payward_settle' THEN 'tentative'
		  	ELSE bt.`action_taken`
	  	  END AS action_done,
		  bt.`action_date` AS date_time_encoded 
		FROM
		  seg_billing_transactions bt 
		  INNER JOIN care_encounter ce 
		    ON bt.`encounter_nr` = ce.`encounter_nr` 
		WHERE DATE(bt.`action_date`) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
		  AND bt.`action_taken` IN ('tentative', 'rebilled', 'payward_settle') 
		  AND bt.`biller` = {$db->qstr($user_id)}
		  $encounter_type 
		ORDER BY date_time_encoded,
		  bill_nr,
		  patient ASC ";

$rs = $db->Execute($sql);
$data = array();
if (is_object($rs)) {
    if($rs->RecordCount()){
        while ($row = $rs->FetchRow()) {
            $data[] = array(
                'patient' => utf8_decode(trim($row['patient'])),
        		'hrn' => $row['hrn'],
        		'case_no' => $row['case_no'],
				'bill_nr' => $row['bill_nr'],
        		'action' => ucwords($row['action_done']),
        		'date_time_encoded' => date('m/d/Y h:i A', strtotime($row['date_time_encoded'])),
            );
        }
    }
    else {
    	$data[0] = array('patient' => 'No Data');
    }
} else {
    $data[0] = array('patient' => 'No Data');
}