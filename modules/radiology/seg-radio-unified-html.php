<?php
/**
 * Created by Nick 06-18-2014
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

include("roots.php");
define('NO_2LEVEL_CHK',1);
// added by carriane 03/16/18
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);
// end carriane
$local_user='ck_prod_db_user';
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path . 'include/inc_date_format_functions.php');
include_once($root_path . 'include/care_api_classes/class_encounter.php');
include_once($root_path . 'include/care_api_classes/class_cert_med.php');
include_once($root_path . 'include/care_api_classes/class_person.php');
include_once($root_path . 'include/care_api_classes/class_personell.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/care_api_classes/class_radiology.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');

$smarty = new smarty_care('common');
$enc_obj = new Encounter;
$person_obj = new Person;
$radio_obj = new SegRadio;
$personell_obj = new Personell;

if (isset($_GET['pid']) && $_GET['pid']) {
    $pid = $_GET['pid'];
}

if (isset($_GET['batch_nr_grp']) && $_GET['batch_nr_grp']) {
    $batch_nr = $_GET['batch_nr_grp'];
}

if ($pid) {
    if (!($basicInfo = $person_obj->getAllInfoArray($pid))) {
        echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
        exit();
    }
    extract($basicInfo);
} else {
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
    exit();
}

if ($date_birth) {
    $segBdate = @formatDate2Local($date_birth, $date_format);
    if (!($age = $person_obj->getAge($segBdate))) {
        $age = '';
        $segBdate = 'Not Available';
        $segBdateAge = $segBdate;
    } else {
        $age = $age . ' year(s) old';
        $segBdateAge = $segBdate . '   / ' . $age;
    }
}

if ($sex == 'f') {
    $gender = "female";
} else if ($sex == 'm') {
    $gender = "male";
}

if ($street_name)
    $street_name = "$street_name ";
else
    $street_name = "";

if ($brgy_name == 'NOT PROVIDED')
    $brgy_name = "";

if (!($brgy_name))
    $brgy_name = "";
else
    $brgy_name = ", " . $brgy_name . ", ";

if ($mun_name == 'NOT PROVIDED')
    $mun_name = "";

if ($prov_name != 'NOT PROVIDED') {
    if (stristr(trim($mun_name), 'city') === FALSE) {
        if (!empty($mun_name)) {
            $province = ", " . trim($prov_name);
        } else {
            $province = trim($prov_name);
        }
    }
} else {
    $province = "";
}

$sAddress = trim($street_name) . " " . trim($brgy_name) . trim($mun_name) . " " . $province;
$final_reader = '';

if ($batch_nr) {
    if (!($radioResultObj = $radio_obj->getAllInfoUnifiedBatchRequestList($batch_nr))) {
        echo "seg-radio-findings-select-batchNr.php : radio_obj->sql = '" . $radio_obj->sql . "' <br> \n";
        echo '<em class="warn"> Cannot continue to display the page! <br> \n NO Result(s) found.</em>';
        exit();
    }
} else {
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Batch Number!</em>';
    exit();
}

while ($radioResultInfo = $radioResultObj->FetchRow()) {
    extract($radioResultInfo);

    if ($encounter_type == 1) {
        $area = 'ER';
    } elseif ($encounter_type == 2) {
        $area = 'OPD';
    } elseif ($encounter_type == 3) {
        $area = 'ER - Inpatient ' . $ward_id . " [" . $ward_name . "]";
        $area = "Inpatient [" . $ward_name . "]";
        $area = $ward_name;
    } elseif ($encounter_type == 4) {
        $area = 'OPD - Inpatient ' . $ward_id . " [" . $ward_name . "]";
        $area = "Inpatient [" . $ward_name . "]";
        $area = $ward_name;
    } elseif($encounter_type == IPBMOPD_enc){
        $area = "IPBM - OPD";
    } elseif($encounter_type == IPBMIPD_enc){
        $area = $ward_name;
    } else {
        $area = "WALKIN";
    }
    $rs_doc = $request_doctor_name;

    $rs_dep = $request_dept_name;
    $seg_request_date = 'No Date Requested indicated';
    if ($served_date && ($served_date != '0000-00-00')) {
        $seg_request_date = @formatDate2Local($served_date, $date_format);
    }
    if ($served_date == '') {
        $seg_request_date = @formatDate2Local($create_dt, $date_format);
    }
    if ($create_dt == '') {
        $seg_request_date = 'No Date Requested indicated';
    }

    $findings_array = unserialize($findings);
    #$findings = $findings_array[$findings_nr];
    $findings = $findings_array[count($findings_array) - 1];
    $findings_final = utf8_decode($findings_array[count($findings_array) - 1]);
    $radio_impression_array = unserialize($radio_impression);
    $radio_impression_final = $radio_impression_array[count($radio_impression_array) - 1];
    $doctors_array = unserialize($doctor_in_charge);

    #$doctors_final = $doctors_array[count($doctor_in_charge)-1];
    $doctors_final = $doctors_array[count($doctors_array) - 1];

    #edited by VAN 04-28-2011
    $findings_date_array = unserialize($findings_date);
    if (!count($findings_date_array))
        $index = count($findings_date_array);
    else
        $index = count($findings_date_array) - 1;

    #$seg_service_date = 'No Date Service indicated';
    if ($service_date && ($service_date != '0000-00-00')) {
        $seg_service_date = @formatDate2Local($service_date, $date_format);
    } else {

        $findings_date_final = $findings_date_array[$index];
        #$findings_date_final = $findings_date_array[0];

        if ($findings_date_final)
            $findings_date_final = @formatDate2Local($findings_date_final, $date_format);
        else
            $findings_date_final = 'No Date Service indicated';

        #added by VAN 04-28-2011
        if ($findings_date_final && ($findings_date_final != '0000-00-00'))
            $seg_service_date = $findings_date_final;
    }

    $doctor_in_charge_array = unserialize($doctor_in_charge);
    $doctor_in_charge_final = $doctor_in_charge_array[count($doctor_in_charge_array) - 1];

    #$doctor_in_charge_final = $doctor_in_charge_array[$findings_nr];
    if ($doctor_in_charge_final) {

        if ($reportingDoctorInfo = $personell_obj->getPersonellInfo($doctor_in_charge_final)) {
            $doctor_in_charge_name = trim($reportingDoctorInfo['name_first']);
            if (!empty($reportingDoctorInfo['name_middle'])) {
                $doctor_in_charge_name .= ' ' . substr(trim($reportingDoctorInfo['name_middle']), 0, 1) . '.';
            }
            if (!empty($reportingDoctorInfo['name_last'])) {
                $doctor_in_charge_name .= ' ' . trim($reportingDoctorInfo['name_last']);
            }
            $doctor_in_charge_name = trim($doctor_in_charge_name . ', MD');
        }
    }

    if (!empty($grant_no)) {
        $or_no_final = "CHARITY";
        $amount_paid = "0.00";
    } elseif (!empty($or_no)) {
        if (floatval($amount_or) > floatval($price_net)) {
            $or_no_final = $or_no . ' (Subsidized)';
            $amount_paid = $price_net;
        } else {
            $or_no_final = $or_no;
            $amount_paid = $amount_or;
        }
    } else {
        $or_no_final = 'Subsidized';
        $amount_paid = $price_net;
    }

    if (($served_date) && ($served_date != '0000-00-00 00:00:00')) {
        $served_date = date("m/d/Y h:i A", strtotime($served_date));
    } else {
        $served_date = "";
    }

    $req_doc = $personell_obj->get_Person_name3($request_doctor);
    if ($req_doc) {
        while ($row_doc = $req_doc->Fetchrow()) {
            $request_doctor_name = mb_strtoupper($row_doc['dr_name']);
        }
    }

    $batchNrArrayInfo[$id]['service_code'] = $service_code;
    $batchNrArrayInfo[$id]['request_doctor_name'] = $request_doctor_name;
    $batchNrArrayInfo[$id]['request_dept_name'] = $request_dept_name;
    $batchNrArrayInfo[$id]['or_no_final'] = $or_no_final;
    $batchNrArrayInfo[$id]['amount_paid'] = $amount_paid;
    $batchNrArrayInfo[$id]['status'] = $status;

    $batchNrArrayInfo[$id]['seg_request_date'] = $request_date;
    $batchNrArrayInfo[$id]['seg_service_date'] = $seg_service_date;
    $batchNrArrayInfo[$id]['batch_nr'] = $batch_nr;
    $batchNrArrayInfo[$id]['refno'] = $refno;
    $batchNrArrayInfo[$id]['service_name'] = $service_name;

    $batchNrArrayInfo[$id]['findings'] = $findings;
    $batchNrArrayInfo[$id]['findings_final'] = $findings_final;
    $batchNrArrayInfo[$id]['radio_impression_final'] = $radio_impression_final;
    $batchNrArrayInfo[$id]['findings_date_final'] = $findings_date_final;
    $batchNrArrayInfo[$id]['doctor_in_charge_final'] = $doctor_in_charge_final;
    $batchNrArrayInfo[$id]['doctor_in_charge_name'] = $doctor_in_charge_final;
    
    $temp_findings_encoder = $personell_obj->getUserFullName($findings_encoder);
    if($temp_findings_encoder != false)
        $findings_encoder = $temp_findings_encoder;
            
    $batchNrArrayInfo[$id]['findings_encoder'] = $findings_encoder;
    $batchNrArrayInfo[$id]['final_reader'] = $final_reader;
    if ($final_reader == '')
        $final_reader = $doctor_in_charge_final;
    else if ($final_reader == $doctor_in_charge_final)
        $same = FALSE;
    else
        $same = FALSE;
}

if ($service_dept_nr=='165'){
    #Ultrasound
    $report="Ultrasound Report";
    $rValue= "SPMC-RAD-18 ";
    $note_msg="";

}else if ($service_dept_nr=='166'){
    #Special Procedures
    $report="Special Procedures Report";
    $rValue= "SPMC-RAD-17 ";
    $note_msg="";
}else if ($service_dept_nr=='167'){
    #Computed Tomography
    $report="CT Scan Report";
    $rValue= "SPMC-RAD-14 ";
    $note_msg="";
}
#added by gelie 09/11/2015
else if($service_dept_nr=='235'){
    #Mammography
    $report="Digital Mammography Report";
    $rValue="SPMC-RAD-17";
    $note_msg="";
}
#end gelie 
else{
    #General Radiography
    $report="Roentgenological Report";
    $rValue= "SPMC-RAD-17 ";
    $note_msg="NOTE: This result is based on radiographic findings & must be correlated clinically.";
}

if ($status=='pending'){
    $result = " INITIAL READING";
    $foot_result = " / Initially Read";
}elseif ($status=='done'){
    $result = " OFFICIAL READING";
    $foot_result = " / Officially Read";
}elseif ($status=='referral')
    $result = " FOR REFERRAL";

$service_name = strtoupper($service_name);

foreach($batchNrArrayInfo as $batchNrInfo){
    extract($batchNrInfo);

    if($radio_obj->hasBatchNR($batch_nr, count($findings_array))) {
        $c = 0;
        $Fdoc = '';
        $Fpos = '';
        $Sdoc = '';
        $Spos = '';
        $Tdoc = '';
        $Tpos = '';
        $doc4 = '';
        $pos4 = '';
        $doc5 = '';
        $pos5 = '';
        $docNR = & $radio_obj->getDoctorNR($batch_nr, count($findings_array));
        $doc_NR = $docNR->Fetchrow();
        $docs[0] = $doc_NR['con_doctor_nr'];
        $docs[1] = $doc_NR['sen_doctor_nr'];
        $docs[2] = $doc_NR['jun_doctor_nr'];
        for ($x = 0; $x <= 2; $x++) {
            if ($docs[$x] != '') {
                $rs_pr = $personell_obj->get_Person_name3($docs[$x]);
                while ($row_pr = $rs_pr->Fetchrow()) {
                    $dr_name = mb_strtoupper($row_pr['dr_name']) . ", " . $row_pr['drtitle'];
                    $pos = mb_strtoupper(trim($row_pr['job_position']));
                    $c += 1;

                    $encoding_type = mb_detect_encoding($dr_name);
                    if ($encoding_type != 'UTF-8')
                        $dr_name = mb_convert_encoding($dr_name, 'UTF-8', $encoding_type);

                    if ($c == 1) {
                        $Fdoc = $dr_name;
                        $Fpos = $pos;
                    } elseif ($c == 2) {
                        $Sdoc = $dr_name;
                        $Spos = $pos;
                    } elseif ($c == 3) {
                        $Tdoc = $dr_name;
                        $Tpos = $pos;
                    } elseif ($c == 4) {
                        $doc4 = $dr_name;
                        $pos4 = $pos;
                    } else {
                        $doc5 = $dr_name;
                        $pos5 = $pos;
                    }
                }
            }
        }
    } else {
        $Fdoc = mb_strtoupper(mb_convert_encoding($doctors_final, "ISO-8859-1", 'UTF-8'));
    }
    $space = " ";
    $Fcell = '';
    $Scell = '';
    $Tcell = '';
    $cellpos = '';
    $Scellpos = '';
    $Tcellpos = '';

    if ($c == 5) {
        $Fcell = $Fdoc . "<br>" . $Fpos;
        $Scell = $Sdoc . " / " . $Tdoc;
        $Tcell = $doc4 . " / " . $doc5;
        $pos = strpos($Scell, "/");
        for ($x = 0; $x <= $pos - strlen($Spos); $x++) {
            $space .= "  ";
        }
        $Scellpos = $Spos . $space . " " . $Tpos;
        $pos1 = strpos($Tcell, "/");
        for ($x = 0; $x <= $pos1 - strlen($pos4); $x++) {
            $space1 .= "  ";
        }
        $Tcellpos = $pos4 . $space1 . " " . $pos5;
    } elseif ($c == 4) {
        $Fcell = $Fdoc . " / " . $Sdoc;
        $Scell = $Tdoc . " / " . $doc4;
        $pos = strpos($Fcell, "/");
        for ($x = 0; $x <= $pos - strlen($Fpos); $x++) {
            $space .= "  ";
        }
        $cellpos = $Fpos . $space . " " . $Spos;
        $pos1 = strpos($Scell, "/");
        for ($x = 0; $x <= $pos1 - strlen($Tpos); $x++) {
            $space1 .= "  ";
        }
        $Scellpos = $Tpos . $space1 . " " . $pos4;
    } elseif ($c == 3) {
        $Fcell = $Fdoc . "<br>" . $Fpos;
        $Scell = $Sdoc . " / " . $Tdoc;
        $pos = strpos($Scell, "/");
        for ($x = 0; $x <= $pos - strlen($Spos); $x++) {
            $space .= "  ";
        }
        $Scellpos = $Spos . $space . " " . $Tpos;
    } elseif ($c == 2) {
        $Fcell = $Fdoc . " / " . $Sdoc;
        $pos = strpos($Fcell, "/");
        for ($x = 0; $x <= $pos - strlen($Fpos); $x++) {
            $space .= "  ";
        }
        $cellpos = $Fpos . $space . " " . $Spos;
    } else {
        $Fcell = $Fdoc . "<br>" . $Fpos;
    }

    $findings_info .= "<tr><td><strong>$service_name</strong></td></tr>
                       <tr><td>FINDINGS:</td></tr>
                       <tr><td>$findings_final</td></tr>
                       <tr><td>IMPRESSIONS:</td></tr>
                       <tr><td><strong>$radio_impression_final</strong></td></tr>";
    if(!$same){
        $findings_info .=    "<tr><td>$Fcell</td></tr>
                              <tr><td>$cellpos</td></tr>
                              <tr><td>$Scell</td></tr>
                              <tr><td>$Scellpos</td></tr>
                              <tr><td>$Tcell</td></tr>
                              <tr><td>$Tcellpos</td></tr>";
    }
}


$radtech = $personell_obj->get_Person_name3($rad_tech,1);
if($radtech){
    while ( $row_tech = $radtech->Fetchrow()) {
        $radtech_name = mb_strtoupper($row_tech['dr_name']).", RRT";
    }
}
$served_by = $radtech_name;
$date_encoded = $findings_date_final;

/******************************************
 * Assign Smarty Values
 ******************************************/
$hospital = new Hospital_Admin();
$hospitalInfo = $hospital->getAllHospitalInfo();
$smarty->assign('hosp_country',$hospitalInfo['hosp_country']);
$smarty->assign('hosp_agency',$hospitalInfo['hosp_agency']);
$smarty->assign('hosp_name',$hospitalInfo['hosp_name']);
$smarty->assign('hosp_addr1',$hospitalInfo['hosp_addr1']);

$smarty->assign('patient_name',strtoupper($name_last.', '.$name_first.' '.$name_middle));
$smarty->assign('address',ucwords($sAddress));
$smarty->assign('gender',strtoupper($gender));
$smarty->assign('birth_date',$segBdate);
$smarty->assign('age',$age);

$smarty->assign('hrn',$pid);
$smarty->assign('rid',$rid);
$smarty->assign('batch_no',$batch_nr);

$smarty->assign('requesting_doctor',$request_doctor_name);
$smarty->assign('clinical_impression',strtoupper($clinical_info));
$smarty->assign('date_examination',$served_date);

$smarty->assign('exam',strtoupper($service_code));
$smarty->assign('department',$request_dept_name);
$smarty->assign('area',strtoupper($area));

$smarty->assign('docu_title',strtoupper($report));
$smarty->assign('status',strtoupper($note_msg));
$smarty->assign('note',$result);
$smarty->assign('findings_info',$findings_info);
// var_dump($batchNrArrayInfo['2018254808']['findings_encoder']);die;
$smarty->assign('served_by',ucwords($served_by));
$smarty->assign('encoded_by',ucwords($findings_encoder));
$smarty->assign('foot_result',$foot_result);
$smarty->assign('date_encoded',$date_encoded);

$smarty->display('radiology/radio-html-unified.tpl');
?>