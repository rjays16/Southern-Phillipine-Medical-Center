<?php
/**
* Created Computerized Discharge Slip
* Jasper Report
* Date Created 05/21/2018
* @author Jeff Ponteras
   Updated by @Ryan Nolasco
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

define(hosp_title,"Institute of Psychiatry and Behavioral Medicine");
define(DateValue, '01/01/1970');
define('IPBMIPD_enc', 13);

global $db;
$drUser = $HTTP_SESSION_VARS['sess_user_name'];

$row = $hosp_obj->getAllHospitalInfo();
if($row) {
    $hosp_country = $row['hosp_country'];
    $hosp_agency = $row['hosp_agency'];
    $hosp_name = $row['hosp_name'];
    $hosp_address = $row['hosp_addr1'];
    $image_dmc = dirname(dirname(dirname(__FILE__))) . '/gui/img/logos/dmc_logo.jpg';
    $image_doh = dirname(dirname(dirname(__FILE__))) . '/img/ipbm.png';
}

$result = $enc_obj->getDischargeMainInfoIPBM($encounter_nr);
while($row = $result->FetchRow()) {
    
    $hrn = $row['hrn'];
    $patient = strtoupper($row['person_name']);
    $age = $row['age'];
    $sex = strtoupper($row['sex']);
    $encType = $row['enc_type'];
    $discharge_date = date('m/d/Y', strtotime($row['discharge_date']));
    $homis_id = $row['homis_id'];

}

/* Discharge Info */
$result = $enc_obj->getDischargeSlipInfoIpbm($encounter_nr);
while($row = $result->FetchRow()) {
        $medtime = $row['medtime'];
        $chkuptime = $row['chkuptime'];
        $notes = $row['notes'];
        $checkup_place = $row['checkup_place'];
        $injection = $row['injection'];
        $schedule = $row['schedule'];
        $unit_nr = $row['unit_nr'];
        $discharge_time = $row['discharge_time'];
        $medications = $row['medications'];
        $side_effects = $row['side_effects'];

        $checkup_day = date('m/d/Y', strtotime($row['checkup_date']));
        $schedule = date('m/d/Y', strtotime($row['schedule']));
}

$meds2 = json_decode($medications);
   
/**** Commented by @Ryan 08/01/18 ****/
/* Explosions */
/*
$med = explode(":", $medications);
$m11 = $med[0]; $m12 = $med[1]; $m13 = $med[2]; $m14 = $med[3];
$m21 = $med[4]; $m22 = $med[5]; $m23 = $med[6]; $m24 = $med[7];
$m31 = $med[8]; $m32 = $med[9]; $m33 = $med[10]; $m34 = $med[11];
$m41 = $med[12]; $m42 = $med[13]; $m43 = $med[14]; $m44 = $med[15];
$m51 = $med[16]; $m52 = $med[17]; $m53 = $med[18]; $m54 = $med[19];

$effects = explode(":", $side_effects);
if ($effects[0] == 'on') { $e1 = '/';}
if ($effects[1] == 'on') { $e2 = '/';}
if ($effects[2] == 'on') { $e3 = '/';}
if ($effects[3] == 'on') { $e4 = '/';}
if ($effects[4] == 'on') { $e5 = '/';}
if ($effects[5] == 'on') { $e6 = '/';}
if ($effects[6] == 'on') { $e7 = '/';}
*** End Comment here ***
*/

/** * * Updated by @Ryan 08/01/18 * * **/
    // $meds = explode(':', $medications);
    //    list( 
    //         $list[0],$list[1],$list[2],$list[3],$list[4],$list[5],
    //         $list[6],$list[7],$list[8],$list[9],$list[10],$list[11],
    //         $list[12],$list[13],$list[14],$list[15],$list[16],
    //         $list[17],$list[18],$list[19] 
    //         ) = $meds;


    $effects = explode(':', $side_effects);
    list($se[0], $se[1], $se[2],$se[3], $se[4], $se[5], $se[6]) = $effects;

    $e1 = ($se[0] == 'on') ? '/' : '';
    $e2 = ($se[1] == 'on') ? '/' : '';
    $e3 = ($se[2] == 'on') ? '/' : '';
    $e4 = ($se[3] == 'on') ? '/' : '';
    $e5 = ($se[4] == 'on') ? '/' : '';
    $e6 = ($se[5] == 'on') ? '/' : '';
    $e7 = ($se[6] == 'on') ? '/' : '';

/** * * End Update by @Ryan * * **/

/*--- added by @Ryan 07/05/18 ---*/

$query_doc = "SELECT name FROM care_users WHERE login_id = ".$db->qstr($_SESSION['sess_temp_userid'])." ";
$rs = $db->Execute($query_doc);
if($rs !== false) {
    while($row = $rs->FetchRow()) {
        $physician_name = $row['name'];
    }
}

$query_ds = "SELECT discharge_date, encounter_date FROM care_encounter WHERE encounter_nr = ".$db->qstr($encounter_nr)." ";
$rs1 = $db->Execute($query_ds);
if($rs1 !== false) {
    while($row = $rs1->FetchRow()) {
        $discharge_dt = date('m/d/Y', strtotime($row['discharge_date']));
        $consultation_dt = date('m/d/Y', strtotime($row['encounter_date']));
    }
}

$dateSched = ($schedule == DateValue) ? '' : $schedule;
$dateDischrge = ($discharge_dt == DateValue) ? '' : $discharge_dt;
$dateConsultation = ($consultation_dt == DateValue) ? '' : $consultation_dt;

/*--- end by @Ryan ---*/


/* Array of Parameters */
$params = array(
    'hospcountry' => $hosp_country,
    'hospagency' => $hosp_agency,
    'hospname' => $hosp_name,
    'hosptitle' => hosp_title,
    'imagepath' => $image_dmc,
    'imageipbmpath' =>  $image_doh,
    'patient' => $patient,
    'notes' => stripslashes(trim($notes)),
    'sched' => $dateSched,
    'un_no' => $homis_id,
    'inject' => $injection,
    'check_day' => $dateConsultation,
    'hrn' => $hrn,
    'discharge_date' => $dateDischrge,
    'attending_physician' => $physician_name,
    
    'e1' => $e1,
    'e2' => $e2,
    'e3' => $e3,
    'e4' => $e4,
    'e5' => $e5,
    'e6' => $e6,
    'e7' => $e7,
    'e8' => $e8,
    'e9' => $e9,
    'e10' => $e10,
    'e11' => $e11
);
$index=0;
$data = array();

while ($value = $meds2->{$index}) {

    $data[$index]['meds'] = stripslashes(utf8_decode($value->{'0'}));
    $data[$index]['morning'] = stripslashes(utf8_decode($value->{'1'}));
    $data[$index]['aftnoon'] = stripslashes(utf8_decode($value->{'2'}));
    $data[$index]['night'] = stripslashes(utf8_decode($value->{'3'}));
    
    $index++;
}
    
showReport('ipbm_discharge_slip',$params,$data,'PDF');//<-- for opd ipbm

?>