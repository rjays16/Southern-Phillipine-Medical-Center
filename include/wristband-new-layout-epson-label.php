<?php
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');

global $db;

$encounter_nr = $en;

$sql =  "SELECT ce.encounter_nr, ce.pid AS pid,
                fn_get_gender(ce.pid )AS Gender,
                fn_get_person_name (ce.pid) AS NAME_PATIENT
         FROM care_encounter AS ce
         WHERE ce.encounter_nr= '$encounter_nr'";

      if($result=$db->Execute($sql)){
             while ($row = $result->FetchRow()){

              $pid = $row['pid']; 
              $sex = $row['Gender']; 
              $NAME_PATIENT = $row['NAME_PATIENT'];
            }
      }else{
        return FALSE; 
      }

if ($sex=='f')
    $Gender = 'FEMALE';
elseif ($sex=='m')
    $Gender = 'MALE';

$params = array('Name' => $NAME_PATIENT,
                'hrn' => $pid,
                'en' => $encounter_nr,
                'gender' => $Gender
                );


$data[0] = array();
showReport('Barcode-Generator', $params, $data, 'PDF');
?>