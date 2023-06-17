<?php 
/*
 * Author : gelie
 * Date : 11/22/2015
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

$user_id = $_SESSION['sess_user_name']; 

//Get patient type
$ptype_param = $_GET['param'];
$patient_type = substr($ptype_param, strrpos($ptype_param, "-") + 1);

if($patient_type == '1'){
	$encounter_type = "AND encounter_type = 2";			//opd
	$encounter_label = "Outpatients";
}
else if($patient_type == '2'){
	$encounter_type = "AND encounter_type = 1";			//er
	$encounter_label = "ER Patients";
}
else if($patient_type == '3'){
	$encounter_type = "AND encounter_type IN (3,4)";	//inpatient
	$encounter_label = "Inpatients";
}
else {
	$encounter_type = "";
	$encounter_label = "All Patients";
}

$params->put('enc_label', $encounter_label);

$sql = "SELECT 
			patient, hrn, case_no, refno, 
			med_id, CONCAT('(',SUM(qty), ')  ', TRIM(meds_xlo)) AS meds_xlo,
			DATE_FORMAT(date_time_encoded, '%m-%d-%Y %h:%i %p') AS date_time_encoded
		FROM(
			SELECT 
				UCASE(fn_get_person_name(ce.pid)) AS patient,
				ce.pid AS hrn,
				ce.encounter_nr AS case_no,
				smd.`refno` AS refno,
				ph.`bestellnum` AS med_id, 
				smd.quantity AS qty, 
				(CASE WHEN (ISNULL(generic) OR (generic = '')) 
					THEN artikelname ELSE generic END) AS meds_xlo,
				IF(smd.create_dt != '0000-00-00 00:00:00', smd.create_dt, sm.`chrge_dte`) AS date_time_encoded
			FROM care_pharma_products_main ph
			INNER JOIN seg_more_phorder_details smd ON smd.`bestellnum` = ph.`bestellnum`
			INNER JOIN seg_more_phorder sm ON sm.`refno` = smd.`refno`
			INNER JOIN care_encounter ce ON ce.encounter_nr = sm.encounter_nr 
			WHERE 
				DATE(smd.`create_dt`) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
				AND smd.`create_id` = ({$db->qstr($user_id)})
				$encounter_type
			UNION ALL
			SELECT 
			    UCASE(fn_get_person_name (ce.pid)) AS patient,
			    ce.pid AS hrn,
			    ce.encounter_nr AS case_no,
			    smd.`refno` AS refno,
			    smd.service_code AS med_id,
			    smd.quantity AS qty,
			    sos.name AS meds_xlo,
			    IF(smd.create_dt != '0000-00-00 00:00:00', smd.create_dt, sms.`chrge_dte`) AS date_time_encoded 
		  	FROM seg_misc_service sms 
		    INNER JOIN seg_misc_service_details smd ON sms.refno = smd.refno 
		    INNER JOIN care_encounter ce ON ce.encounter_nr = sms.encounter_nr 
		    INNER JOIN seg_other_services sos ON sos.alt_service_code = smd.service_code
		    WHERE  
				 DATE(smd.`create_dt`) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
				AND smd.`create_id` = ({$db->qstr($user_id)})
				$encounter_type) t
		GROUP BY t.med_id, t.case_no
		ORDER BY t.date_time_encoded, t.meds_xlo ASC";

$rs = $db->Execute($sql);
$data = array();
if (is_object($rs)) {
    if($rs->RecordCount() > 0){
        while ($row = $rs->FetchRow()) {
            $data[] = array(
                'patient' => $row['patient'],
        		'hrn' => $row['hrn'],
        		'case_no' => $row['case_no'],
        		'meds_xlo' => mb_strtoupper($row['meds_xlo']),
        		'date_time_encoded' => $row['date_time_encoded'],
            );
        }
    }
	else{
		$data[0] = array('patient' => 'No Data');
	}
} else {
    $data[0] = array('patient' => 'No Data');
}