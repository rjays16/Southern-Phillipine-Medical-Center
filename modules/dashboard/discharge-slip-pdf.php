<?php
/**
* Created Computerized Discharge Slip
* Jasper Report
* Date Created 11/23/2014
* @author EJ Ramos
**/
require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path.'include/inc_jasperReporting.php');

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
$hosp_obj = new Hospital_Admin();

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj = new Encounter();

require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');
$pres_obj = new SegPrescription();

global $db;

$row = $hosp_obj->getAllHospitalInfo();
if($row) {
    $hosp_country = $row['hosp_country'];
    $hosp_agency = $row['hosp_agency'];
    $hosp_name = $row['hosp_name'];
    $hosp_address = $row['hosp_addr1'];
    $image_dmc = dirname(dirname(dirname(__FILE__))) . '/gui/img/logos/dmc_logo.jpg';
    $image_doh = dirname(dirname(dirname(__FILE__))) . '/img/doh.png';
}

$result = $enc_obj->getDischargeMainInfo($encounter_nr);
while($row = $result->FetchRow()) {
    
    $patient = strtoupper($row['person_name']);
    #html_entity_decode(htmlentities($patient));
    $age = $row['age'];
    $sex = strtoupper($row['sex']);
    $status = strtoupper($row['civil_status']);
}

$result = $enc_obj->getDischargeSlipInfo($encounter_nr);
while($row = $result->FetchRow()) {
    $hrn = $row['pid'];
    $diagnosis = strtoupper($row['diagnosis']);
    $medications = strtoupper($row['medications']);
    
    $opd_on = "";
    if (($row['follow_up_date'] == NULL) || ($row['follow_up_date'] == '1970-01-01')){
         $opd_on;  
    }else {
         $opd_on = date('F j, Y',strtotime($row['follow_up_date']))." (".date('l',strtotime($row['follow_up_date'])).")";
    }
       
    $er_nod = strtoupper($row['er_nod']);
    $date = date('F j, Y',strtotime($row['discharge_date']));
    $time = date("h:i A", strtotime($row['discharge_time']));

    $department = strtoupper($db->GetOne("SELECT name_formal FROM care_department WHERE nr = '".$row['dept_nr']."'"));
    $attending_physician = strtoupper($db->GetOne("SELECT fn_get_person_name (cp.pid) FROM care_personell AS cpl LEFT JOIN care_person AS cp ON cp.pid = cpl.pid WHERE cpl.nr = '".$row['personnel_nr']."'"));
}

$home_med = $pres_obj->getPrescriptionByEnc($encounter_nr);
$presc_arr = array();
while($row_med = $home_med->FetchRow()){
    if($row_med['frequency_time']) {
        switch ($row_med['frequency_time']) {
            case 'OD':
                $frequency = "OD - Once a Day (6am)";
                break;
            case 'HS':
                $frequency = "@HS - Hours of Sleep (9pm)";
                break;
            case 'TID':
                $frequency = "TID - 3x a Day (6am-1pm-6pm)";
                break;
            case 'BID':
                $frequency = "BID - 2x a Day (6am-6pm)";
                break;
        }
    }
    else{
        $frequency = "";
    }

    if($row_med['period_interval'] != '' && $row_med['period_count'] != 0) {
        $period = "For: " . $row_med['period_count'];

        switch ($row_med['period_interval']) {
            case 'D':
                $period .= " day(s)";
                break;
            case 'M':
                $period .= " month(s)";
                break;
            case 'W':
                $period .= " week(s)";
                break;
        }
    }
    else{
        $period = "";
    }

    $prescription = $row_med['generic'] . '; ' . $row_med['dosage'] . ' ' . $frequency . '; ' . $period;
    array_push($presc_arr, $prescription);
}

$hospitalInfo = $hosp_obj->getAllHospitalInfo();
$params = array(
    'hospcountry' => $hosp_country,
    'hospagency' => $hosp_agency,
    'hospname' => $hosp_name,
    'hospaddr' => $hosp_address,
    'imagepath' => $image_dmc,
    'imagedohpath' =>  $image_doh,
    'patient' => $patient,
    'age' => $age,
    'sex' => $sex,
    'status' => $status,
    'hrn' => $hrn,
    'diagnosis' => $diagnosis,
    'medications' => $medications . "\n\n" . implode("\n", $presc_arr),
    'opd_on' => $opd_on,
    'er_nod' => $er_nod,
    'department' => $department,
    'date' => $date,
    'time' => $time,
    'attending_physician' => $attending_physician,
    'case_no' => $encounter_nr
);

$data = array();

showReport('discharge_slip',$params,$data,'PDF');
?>