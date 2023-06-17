<?php
/*
Added by borj May 23, 2014
List of Unregistered death certificate for the month(jasper)
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/inc_environment_global.php');
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
include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

include('parameters.php');





#GET Info in Prepared Position
$person_position = $pers_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);

#GET Info title of the report


$params->put("hosp_country", $hosp_country);
$params->put("hosp_agency", $hosp_agency);
$params->put("hosp_name", $hosp_name);
$params->put("hosp_addr1", $hosp_addr1);

$img1 = 'gui/img/logos/dmc_logo.jpg';
$img2 = 'img/doh.png';

#GET Info in Prepared Name and Position
$psy_info = $pers_obj->get_Signatory('predeath');
$prep_name = mb_strtoupper($psy_info['name']);
$prep_pos = $psy_info['signatory_title'];



#GET Info in Noted Name and Position
$sig_info = $pers_obj->get_Signatory('medcert');
$noted_name = '<b>'. mb_strtoupper($sig_info['name']). ' ,' . $sig_info['title'] . '</b>';
$noted_pos = ''. $sig_info['signatory_title'];


$params->put("prepared_by", mb_strtoupper($sig_info['name']). ' ,' . $sig_info['title']);
$pType = IPD;
$deptCond = " AND ce.encounter_type NOT IN (" . IPBM_patient_type . ")";
if($_GET['dept_nr'] == IPBM_DEP){

   $psy_info = $pers_obj->get_Signatory('predeath-ipbm');
   $prep_name = mb_strtoupper($psy_info['name']);
   $prep_pos =  $psy_info['signatory_title'];

   $noted_info = $pers_obj->get_Signatory('deathcert-ipbm');
   $noted_name = '<b>'.mb_strtoupper($noted_info['name']). ', ' . $noted_info['title'] . '</b>';
   $noted_pos =  $noted_info['signatory_title'];

   $pType = IPBM_IPD;
   $ptypeIPBM = IPBM_patient_type;
   $deptCond = " AND ce.encounter_type IN (" . IPBM_patient_type . ")";

   $img1 = "img/ipbm_new.jpg";
   $img2 = "gui/img/logos/dmc_logo.jpg";
}

$params->put("prepared_by", $prep_name);
$params->put("prepared_pos", $prep_pos);
$params->put("noted_name", $noted_name);
$params->put("noted_pos", $noted_pos);

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$params->put("image_01", $baseurl.$img1);
$params->put("image_02", $baseurl.$img2);

global $db;


$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

$sql =  "SELECT
            fn_get_person_name (cp.pid) AS name_of_patient,
            IF (encounter_type IN ($pType), DATE_FORMAT(ce.admission_dt, '%m/%d/%Y'), DATE_FORMAT(ce.encounter_date, '%m/%d/%Y')) AS date_of_admission,
            DATE_FORMAT(cp.death_date, '%m/%d/%Y') AS date_of_death,
            IF (fn_get_ward_name (ce.current_ward_nr) IS NULL, 'ER', fn_get_ward_name (ce.current_ward_nr)) AS ward,
            'NO CLAIMANT' AS reason
         FROM
            care_person cp
            INNER JOIN care_encounter ce
            ON ce.pid=cp.pid
            AND ce.encounter_nr=cp.death_encounter_nr 
         WHERE (cp.death_date
            BETWEEN
            DATE(".$db->qstr($from).") 
            AND
            DATE(".$db->qstr($to)."))
            AND cp.pid NOT IN (SELECT pid FROM seg_cert_death)
            AND cp.status NOT IN ('deleted', 'void', 'cancelled', 'hidden')
            AND ce.status NOT IN ('deleted', 'void', 'cancelled', 'hidden')
            AND ce.is_DOA = 0
            $deptCond
            GROUP BY cp.pid";

$rs = $db->Execute($sql);

$rowindex = 0;
$data = array();



if($rs){
    if($rs->RecordCount()){
        while($row = $rs->FetchRow()){
            #$data[$patientname] = array('patient_name' => $row['patientname']);
            #$patientname++;
            $data[$rowindex] =  array('patient_name' => utf8_decode(trim(mb_strtoupper($row['name_of_patient']))),
                                      'date_admission' => $row['date_of_admission'],
                                      'date_death' => $row['date_of_death'],
                                      'ward' => mb_strtoupper($row['ward']),
                                      'reason' => $row['reason'],
                                    
                                    );
                                     
            $rowindex++;
        }
    }else{
        $data[0]['patient_name'] = '';
    }
}else{
    $data[0]['patient_name'] = '';
}




