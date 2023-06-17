<?php
# updaed by VAS 06/15/2019
# using HL7 approach
# parse a HL7 message for bloodbank crossmatching result that fetch from LIS

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');

#added by VAS 06/15/2019
require_once($root_path.'include/care_api_classes/class_blood_bank.php');
require_once($root_path.'frontend/bootstrap.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
#---------------- ended here added by VAS 06/15/2019

global $db;

define(BLUE, 3);
define(revM, 2);
define(revN, 0);
define(rev, 1);

$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('adult_age');
$adult_age = $GLOBAL_CONFIG['adult_age'];

// added by carriane 02/20/18
$glob_obj->getConfig('ENovember');
$ENovember = $GLOBAL_CONFIG['ENovember'];
$glob_obj->getConfig('EMarch');
$EMarch = $GLOBAL_CONFIG['EMarch'];
// end carriane

$units = $_GET['id'];
$unitLast = substr($units, -1);
$unitLast = preg_replace("/[^0-9,.]/", "", $units);
$refno = $_GET['refno'];
$serials = str_replace(" ", "", $serials);

$caption = "COMPATIBILITY TEST RESULT";

// Added By Matsuu 0602017
// Change By Leira 02/12/2018
$sql_getcompatibility = $db->Prepare("SELECT sbc.adult,sbc.pedia,sbc.child, sbc.generic, sbc.generic_effectivity, sbc.generic_rev from seg_blood_compatibility as sbc");
$getcompatibility= $db->Execute($sql_getcompatibility);
$compatibility = $db->Execute($sql);
while ($rows = $getcompatibility->FetchRow()) {
    $pedia = $rows['pedia'];
    $adult = $rows['adult'];
    $child = $rows['child'];

    #added by VAS 07/16/2019
    $generic_footer = $rows['generic'];
    $generic_effectivity = $rows['generic_effectivity'];
    $generic_rev = $rows['generic_rev'];
}
// Change Ended By Leira
// Ended by Matsuu

#added by VAS 06/15/2019
$bloodObj = new SegBloodBank();
$parseObj = new seg_parse_msg_HL7();
$hl7fxnObj = new seg_HL7();

$testcode = $themes = Config::model()->findByPk('bloodbank_default_testcode');
$pxblood_res = $bloodObj->getPatientBloodResult($refno, $unitLast, $testcode);

if (is_object($pxblood_res)){
    $row = $pxblood_res->FetchRow();
    extract($row);      

    #parse result starts here for the ABORH ORU
    #PART 1 : PATIENT ABORH

    #PID INFO
    $order_message = $row['order_hl7_msg'];
    $segments_order = explode($parseObj->delimiter, trim($order_message));
    $details_part1 = $parseObj->bloodparseHL7($segments_order);

    $obr_p1 = $details_part1->obr;

    $arr_physician = explode($parseObj->COMPONENT_SEPARATOR, trim($obr_p1['physician']));
    $physician = $arr_physician[1];

    $arr_loc = explode($parseObj->COMPONENT_SEPARATOR, trim($obr_p1['location']));
    $location = $arr_loc[1];

    $clinical_info = $obr_p1['clinical_info'];
    #------------PID INFO

    #OBR SEGMENT
    #OBR for ABORH
    $message = $row['hl7_msg'];
    $segments = explode($parseObj->delimiter, trim($message));
    $details_part2 = $parseObj->bloodparseHL7($segments);
    $obr_p2 = $details_part2->obr;

    $lab_no = $obr_p2['lab_no'];
    $date_received = $obr_p2['date_received'];
    $date_crossmatched = $obr_p2['date_crossmatched'];
    #---OBR SEGMENT for ABORH
} #end if (is_object($pxblood_res))   


#PART 2 : PATIENT BLOOD PREPARED PRODUCTS
$bloodprod = $bloodObj->getPatientPreparedBloodProd($lis_order_no, $pid);

$nofound=0;
$printbloodlabel = 0;
if (is_object($bloodprod)){
    $bps_row = $bloodprod->FetchRow();

    $bps_message = $bps_row['hl7_msg'];
    $segments_bps = explode($parseObj->delimiter, trim($bps_message));
    $details_bps = $parseObj->bloodparseHL7($segments_bps);
    

    $bpo = $details_bps->bpo;
    $component = $bpo['blood_component'];
    $total_no_bags = $bpo['no_units'];


    $bpx = $details_bps->bpx;
    
    $date_done = $bpx['date_crossmatched'];
    $patient_blood_type = $bpx['patient_blood_type'];

    $patient_blood_type = explode(" ", $patient_blood_type);
    $patient_blood_type = $patient_blood_type[0];

    $expiry_date = $bpx['date_expiry'];
    $serial_no = $bpx['serial_no'];

    $result_compatibility = $bloodObj->getBloodCrossmatchResultDesc($bpx['crossmatching_result']);
    
    if(stristr(mb_strtolower($result_compatibility), 'incompatible') === FALSE){
        $printbloodlabel = 1;
    }else{
        $printbloodlabel = 0;
    }
    
    $arr_source = explode($parseObj->COMPONENT_SEPARATOR, trim($bpx['blood_source']));
    $sources = $arr_source[0];

    #DONOR  
    $donor = $bloodObj->getDonorInfo($serial_no, $testcode);

    if (is_object($donor)){
        $donor_row = $donor->FetchRow();

        $donor_message = $donor_row['hl7_msg'];
        $segments_donor = explode($parseObj->delimiter, trim($donor_message));
        $details_donor = $parseObj->bloodparseHL7($segments_donor);
        $blood_donor = $details_donor->obx[1];

        $donor_blood_type = $blood_donor['result'];

        $donor_blood_type = explode(" ", $donor_blood_type);
        $donor_blood_type = $donor_blood_type[0];

        $arr_medtech = explode($parseObj->COMPONENT_SEPARATOR, trim($blood_donor['medtech']));
        $medtech = str_replace('~', '', $arr_medtech[1]);
    }

}else{
    $result_compatibility = 'No matching of Serial Number found.';
    $nofound=1;
} #------ if (is_object($bloodprod))  
#die((stristr(mb_strtolower($result_compatibility), 'incompatible') === FALSE)).'=='.$printbloodlabel.'//';

if ((!trim($result_compatibility))&&(!$nofound)){
    $result_compatibility = 'No Result Yet';
} 
#-------------ended here, added by VAS 06/15/2019

$getAge = explode(" ", $age);

if((int)$getAge[0]>=$adult_age && $getAge[1]=='years') {
    $footer = $adult;
    $effectivity = $EMarch;
    $rev = rev;
}
elseif (((int)$getAge[0]>=5 && $getAge[1]=='months') || ((int)$getAge[0]<19 && ($getAge[1]=='year' || $getAge[1]=='years') ) ){
    $footer = $child;
    $effectivity = $EMarch;
    $rev = revM;
}
else {
    $footer = $pedia;
    $color = BLUE;
    $effectivity = $ENovember;
    $rev = revN;
}


/*$sql = $db->Prepare("SELECT value
    FROM seg_define_config
    WHERE param = 'BB_COMPATIBILITY_AGE'");

$ageBracket = $db->Execute($sql);
$row2 = $ageBracket->FetchRow();

if($getAge['1'] !== "years")
    $compa_header = $caption;
else{
    if($getAge['0'] < $row2['value']){
        $compa_header = $caption;
    }else{
        $compa_header = $caption;
    }
}*/


$sql = $db->Prepare("SELECT value FROM seg_define_config WHERE id =".$db->qstr($color));

$ageBracket = $db->Execute($sql);
$row3 = $ageBracket->FetchRow();

$color_code = $row3['value'];


$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hospital_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hosp_header']  = "Center for Health Development";
  $row['hospital_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}

#updated by VAS 06/04/2019
$params = array(
    "hosp_country" => $row['hosp_country'],
    "hosp_agency" => $row['hosp_agency'],
    "hosp_header" => $row['hosp_header'],
    "hosp_name" => $row['hosp_name'],
    "hosp_addr1" => $row['hosp_addr1'],

    "personnel" => $encoder,
    "compa_header" => $caption,
    // Added By: Leira
    "color_code" => $color_code,
    "effectivity" => $effectivity,
    "rev" => $rev,
    // Ended By: Leira
    // Added by Matsu
    "footer"=>$footer,
    // Ended by Matsu

    #added by VAS 06/04/2019
    "patient_blood_type" => $patient_blood_type,
    "donor_blood_type" => $donor_blood_type,
    "result_compatibility" => $result_compatibility,
    "date_encoded" => ($date_received)?date('m/d/Y h:i A',strtotime($date_received)):'',
    "date_crossmatched" =>($date_crossmatched)?date('m/d/Y h:i A',strtotime($date_crossmatched)):'',
    "date_done" =>($date_done)?date('m/d/Y h:i A',strtotime($date_done)):'',
    "source" => $sources,
    "component" => $component,
    "units" => $unitLast,
    "serial" => ($serial_no)?$serial_no:$serials, #from HL7
    "blood" => $blood_type,
    "ward" => $location,
    "patient" => utf8_decode(trim($pat_name)),
    "hrn" => $pid,
    "gender" => ($sex=='m')?'Male':'Female',
    "age" => $age,
    "physician" => utf8_decode(trim($physician)),
    "diagnosis" => $clinical_info,
    "date_birth" => (($birth!='')&&($birth!='0000-00-00'))?date('m/d/Y',strtotime($birth)):'UNKNOWN',
    "date_generated" => date('m/d/Y h:i A'),
    //"lab_no" => $lab_no,
    "lis_order_no" => 'LIS Order No.: '.$lis_order_no,
    "type_request" => ($urgents == 1) ? 'STAT' : 'ROUTINE',
    "expiry_date" => ($expiry_date)?date('m/d/Y h:i A',strtotime($expiry_date)):'',
    "qtys" => $total_no_bags,
    "medtech" => $medtech,
    "generic_footer" => $generic_footer,
    "generic_effectivity" => $generic_effectivity,
    "generic_rev" => $generic_rev,
    "printbloodlabel" => $printbloodlabel,
  );


$data[0] = array();


$baseDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';

$data[0]['image_01'] = $baseDir . "gui/img/logos/dmc_logo.jpg";
$data[0]['image_02'] = $baseDir . "img/doh.png";
$data[0]['image_03'] = $baseDir . "images/laboratory/blood_com_signature.jpg";
$data[0]['image_04'] = $baseDir . "gui/img/common/default/check2.gif";
$data[0]['image_05'] = $baseDir . "images/space.gif";

showReport('BB_Compatibility_Report_lis', $params, $data, 'PDF');
?>