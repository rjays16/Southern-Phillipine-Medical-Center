<?php
# Author JEFF @ 11-29-17 for Generation of Crossmatching(XM) Request VIA HRN
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
include_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

global $db;

$glob_obj=new GlobalConfig;
$bb_age_limit = (int) $glob_obj->getConfigValue('adult_age');

# Getters
$enc_nr = $_GET['enc'];
$pid = $_GET['pid'];

# Objects
$pers_obj = new Personell;
$objInfo = new Hospital_Admin();
$seg_encounter = new Encounter();

# Get hospital info for header
if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hospital_name']   = strtoupper($row['hosp_name']);
}

# Get other details for header  
if ($xm = $objInfo->getXMhospitalInfo()) {
  $xm['dept_center'] = ($xm['dept_center']);
  $xm['dept_hosp']   = ($xm['dept_hosp']);
  $xm['rep_dept']   = $xm['rep_dept'];
  $xm['rep_title'] = strtoupper($xm['rep_title']);
  $xm['ped_pink']   = strtoupper($xm['ped_pink']);
  $xm['ped_green']   = strtoupper($xm['ped_green']);
  $xm['call_nr']   = strtoupper($xm['call_nr']);
  $xm['call_for']   = $xm['call_for'];
  $xm['local_address']   = $xm['local_address'];
}

 # Get data based on pid
 $sql = "SELECT 
            cp.`name_last` AS ln,
            cp.`name_first` AS fn,
            cp.`name_middle` AS mn,
            cp.`date_birth` AS bd,
           `fn_get_age`(NOW(),cp.`date_birth`) AS age,
            cp.`sex` AS sex,
            sbt.`name` AS blood_type
          FROM
            care_person AS cp
          LEFT JOIN seg_blood_type_patient AS sbtp
          ON sbtp.pid=cp.pid
          LEFT JOIN seg_blood_type AS sbt
          ON sbt.id=sbtp.blood_type
          WHERE cp.pid =". $db->qstr($pid);

    $res = $db->Execute($sql);
    if($res){
         while($pdata = $res->FetchRow()){
          $fn = $pdata['fn'];
          $mn = $pdata['mn'];
          $ln = $pdata['ln'];
          $bd = $pdata['bd'];
          $age = $pdata['age'];
          $sex = $pdata['sex'];
          $bloodType = $pdata['blood_type'];
         }
     }

$sql2 = "SELECT
          DATE_FORMAT(sbrs.issuance_date, '%M %d,%Y') AS issuance_date,
          sbrd.serial_no AS serial_nr,
          sbrd.component AS component,
          sbrd.blood_source AS source,
          (SELECT
              name
           FROM seg_blood_type AS sbt
           WHERE sbtp.blood_type = sbt.id) AS blood_type,
          (
            CASE
              (sbrd.result)
              WHEN 'noresult'
              THEN 'No Result'
              WHEN 'compat'
              THEN 'Compatible'
              WHEN 'incompat'
              THEN 'Incmpatible'
              WHEN 'retype'
              THEN 'Re-typing'
              WHEN NULL
              THEN ''
            END
          ) AS result,
          (
            IF(
              sbrs.date_return,
              DATE_FORMAT(sbrs.date_return, '%M %d,%Y'),
              DATE_FORMAT(sbrs.date_consumed, '%M %d,%Y')
            )
          ) AS con_ret_date
        FROM
          care_encounter AS ce
          INNER JOIN seg_lab_serv AS sls
            ON ce.encounter_nr = sls.encounter_nr
          INNER JOIN seg_blood_received_details AS sbrd
            ON sls.refno = sbrd.refno
            AND sbrd.status <> 'not yet'
          LEFT JOIN seg_blood_received_status AS sbrs
            ON sls.refno = sbrs.refno
            AND sbrd.ordering = sbrs.ordering
          LEFT JOIN seg_blood_type_patient AS sbtp
            ON sbtp.pid = ce.pid
        WHERE ce.pid = ".$db->qstr($pid);

$rs2 = $db->Execute($sql2);
if($rs2){
    if($rs2->RecordCount()){
        $data2 = $rs2->GetRows();
        $when = $data2[0]['con_ret_date'];
    }else{
        $when = "";
    }
}else{
    $when = "";
}

# fetching of date based on encounter, blank if null
$encounter_details = $seg_encounter->getEncounterInfo($_GET['enc']);
  if ($encounter_details) {
        $physician = $encounter_details['attending_physician_name'];
        $diagnosis = $encounter_details['admitting_diagnosis'];
        $room_nr = $encounter_details['current_room_nr'];
        $ward_nr = $encounter_details['ward_name'];
        $bloodGroup = $encounter_details['blood_group'];
  }

  # Format age
  $ageSuffix = $age;
  $age = explode(' ',$age);
  $age_suffix = $age[1];
  $age = $age[0];
  # Age bracket

  if ($age >= $bb_age_limit && $age_suffix == 'years') {
      $pediaSelect = $xm['ped_green'];
  }
  else{
      $pediaSelect = $xm['ped_pink'];
  }

  # Format date
  $bd = date("M d, Y",strtotime($bd));
  # Format middle name
  $mn = $mn[0];


# Data for JRXML array
$params = array(
  'img_doh' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."modules".DIRECTORY_SEPARATOR ."social_service".DIRECTORY_SEPARATOR."image".DIRECTORY_SEPARATOR ."Logo_DOH.jpg",
  'img_spmc' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."modules".DIRECTORY_SEPARATOR ."social_service".DIRECTORY_SEPARATOR."image".DIRECTORY_SEPARATOR ."dmc_logo.jpg",
  'hosp_country' =>  strtoupper($row['hosp_country']),
  'hosp_agency' =>  strtoupper($row['hosp_agency']),
  'hosp_name' =>  strtoupper($row['hosp_name']),
  'hosp_localadd' =>  $xm['local_address'],
  'CHD'=> $xm['dept_center'],
  'DPL'=> $xm['dept_hosp'],
  'BTS'=> $xm['rep_dept'],
  'call4505'=> $xm['call_nr'],
  'forPHLEBO'=>$xm['call_for'],
  'requestXM'=>$xm['rep_title'],
  'pediaSelect'=>$pediaSelect,
  'when'=>$when,
  'surname' => strtoupper($ln),
  'fn' => strtoupper($fn),
  'mn' => strtoupper($mn),
  'date_birth'=> $bd,
  'hrn'=>$pid,
  'physician'=> strtoupper($physician),
  # Leira 2/12/2018 
  // 'ward'=>$ward_nr,
  // 'room_nr'=>$room_nr,
  'age'=> $ageSuffix." / ".strtoupper($sex),
  // 'blood'=>$bloodType,
  # Leira end 2/12/2018
  'blood_group'=>$bloodGroup,
  'diagnosis'=>strtoupper($diagnosis)
  );

$data[0] = array();
showReport('BB_XM',$params,$data,'PDF');
# end of report line ----
?>
