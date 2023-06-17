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
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

define(hosp_title,"Institute of Psychiatry and Behavioral Medicine");
define(DateValue, '01/01/1970');
define('IPBMIPD_enc', 13);

global $db;
$drUser = $HTTP_SESSION_VARS['sess_user_name'];

function time_name($time){
            $times = date('g:i a',strtotime($time));
            return $times;
        }

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
$getLatestImpression = $enc_obj->getPatientEncInfo($_GET['encounter_nr']);
    
    $address =  $getLatestImpression['street_name'] . '  ' . $getLatestImpression['brgy_name'] . ' , ' . $getLatestImpression['mun_name'] . '  , ' . $getLatestImpression['prov_name'];   
    $admi = explode(' ', $getLatestImpression['admission_date']);
    $admission1 = $admi[0].'  '. date('h:i a',strtotime($admi[1]));
    if($getLatestImpression['discharge_time'] !=''){
        $discharge = $getLatestImpression['discharge_date']. '  ' .date('h:i a',strtotime($getLatestImpression['discharge_time']));
    }else{
        $discharge = $getLatestImpression['discharge_date'].'';
    }
    $locations = $getLatestImpression['location'];
    $chkuptime2 = time_name($chkuptime);
    if(strlen($getLatestImpression['final_diagnosis']) > 91){
    $fdiagnosis2 =  $getLatestImpression['final_diagnosis'];
    $fdiagnosis = ' ';
    }else{
        $fdiagnosis =  $getLatestImpression['final_diagnosis'];
        $fdiagnosis2 =  ' ';
    }
    
    $meds2 = json_decode($medications);
    
    $dr_nr = $getLatestImpression['current_att_dr_nr'];

    if ($dr_nr){
    if ($doc_info = $pers_obj->getPersonellInfo($dr_nr)){

        $middleInitial = "";
        if (trim($doc_info['name_middle'])!=""){
            $thisMI=split(" ",$doc_info['name_middle']);
            foreach($thisMI as $value){
                if (!trim($value)=="")
                    $middleInitial .= $value[0];
            }
            if (trim($middleInitial)!="")
                $middleInitial = " ".$middleInitial.".";
        }
        $physician_name ="Dr. ".$doc_info['name_first']." ".$doc_info['name_2'].$middleInitial." ".$doc_info['name_last'];
    }
}

/**** Commented by @Ryan 08/01/18 ****/
/* Explosions */
    
    $effects = explode(':', $side_effects);
    list($se[0], $se[1], $se[2],$se[3], $se[4], $se[5], $se[6], $se[7], $se[8], $se[9], $se[10]) = $effects;

    $e1 = ($se[0] == 'on') ? '/' : '';
    $e2 = ($se[1] == 'on') ? '/' : '';
    $e3 = ($se[2] == 'on') ? '/' : '';
    $e4 = ($se[3] == 'on') ? '/' : '';
    $e5 = ($se[4] == 'on') ? '/' : '';
    $e6 = ($se[5] == 'on') ? '/' : '';
    $e7 = ($se[6] == 'on') ? '/' : '';
    $e8 = ($se[7] == 'on') ? '/' : '';
    $e9 = ($se[8] == 'on') ? '/' : '';
    $e10 = ($se[9] == 'on') ? '/' : '';
    $e11 = ($se[10] == 'on') ? '/' : '';

/** * * End Update by @Ryan * * *

/*--- added by @Ryan 07/05/18 ---*/

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
    'hospaddr' => $hosp_address,
    'hospTel' => $hosp_name,
    'imagepath' => $image_dmc,
    'imageipbmpath' =>  $image_doh,
    'patient' => $patient,
    // 'notes' => $notes,
    'location' => $locations,
    'sched' => $dateSched,
    'un_no' => $homis_id,
    'inject' => $injection,
    'check_day' => $dateConsultation,
    'hrn' => $hrn,
    'discharge_date' => $dateDischrge,
    'discharge' => $discharge,
    'attending_physician' => $physician_name,
    'discharge_time' => $discharge_time,
    'admission1'=> $admission1,
    'age' => $age,
    'sex' => $sex,
    'address'=>$address,
    'chkuptime'=>$chkuptime2,
    'medtime'=>$medtime,
    'fdiagnosis'=>$fdiagnosis,
    'fdiagnosis2'=>$fdiagnosis2,
       
    
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

$data = array();
$index = 0;

while ($value = $meds2->{$index}) {
    $data[$index]['meds'] = stripslashes(utf8_decode($value->{'0'}));
    $data[$index]['dos'] = stripslashes(utf8_decode($value->{'1'}));
    $data[$index]['time'] = $value->{'2'};
    $data[$index]['remarks'] = stripslashes(utf8_decode($value->{'3'}));
    if($index < 7){
        $data[$index]['group'] = 'Home Medications';
        $data[$index]['groups'] = '';
    }else{
        $data[$index]['group'] = 'Depot Injection';
        $data[$index]['sintruct'] = 'Special Instructions: ';
        $data[$index]['notes'] = stripslashes(trim($notes));
        }
    $index++;
}
        
showReport('ipbm_discharge_slip2',$params,$data,'PDF');//<-- for IPBM-IPD

?>