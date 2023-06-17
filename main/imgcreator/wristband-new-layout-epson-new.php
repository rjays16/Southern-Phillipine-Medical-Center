<?php
#created by Borj, 2/8/2014
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');

define(ER, 1);

global $db;

$encounter_nr = $en;

$sql =  "SELECT
          ce.encounter_nr,
          ce.pid AS pid,
          fn_get_gender (ce.pid) AS Gender,
          UPPER(fn_get_person_name (ce.pid)) AS NAME_PATIENT,
          fn_get_age_wrist (IF(ce.admission_dt,ce.admission_dt, ce.encounter_date), cp.date_birth) AS age,
          DATE_FORMAT(IF(ce.admission_dt,ce.admission_dt, ce.encounter_date),'%m/%e/%Y %h:%i %p') AS DateofAddmission,
          ce.encounter_type
        FROM
          care_encounter AS ce
        LEFT JOIN care_person AS cp
          ON cp.pid = ce.pid
        WHERE ce.encounter_nr= ".$db->qstr($encounter_nr);
// echo $sql;die;
      if($result=$db->Execute($sql)){
        while ($row = $result->FetchRow()){

          $pid = $row['pid']; 
          $sex = $row['Gender']; 
          $NAME_PATIENT = $row['NAME_PATIENT'];
          $age = $row['age'];
          $DateOfAdd = $row['DateofAddmission'];

          if($row['encounter_type'] == ER){
            $date_label = "Consultation Date: ";
          }else $adm_label = "Admission Date: ";

        }
      }else{
        return FALSE; 
      }

if ($sex=='f')
    $Gender = 'FEMALE';
elseif ($sex=='m')
    $Gender = 'MALE';

if(strlen($NAME_PATIENT) > 20){
  $NAME_PATIENT_short = $NAME_PATIENT;
  $NAME_PATIENT = '';
}
#$img =  'C:/xampp/tomcat/webapps/JavaBridge/resource/dmc_logo.jpg';
#$img =  '/usr/local/tomcat/webapps/JavaBridge/resource/dmc_logo.jpg';

$img = $location.'/reports/dmc_logo.jpg';
#die($img);
$params = array('NAME_PATIENT' => $NAME_PATIENT,
                'NAME_PATIENT_short' => $NAME_PATIENT_short,
                'pid' => $pid,
                'en' => $encounter_nr,
                'Gender' => $Gender,
                'age' => $age,
                'img_dmc' => $img,
                'DateOfAdd' => $DateOfAdd,
                'date_label' => $date_label,
                'adm_label' => $adm_label);


$data[0] = array();


// $baseurl = sprintf(
//     "%s://%s%s",
//     isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
//     $_SERVER['HTTP_HOST'],
//     substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
// );
#tomcat\webapps\JavaBridge\resource

$data[0]['img_dmc'] = $root_path . 'C:/xampp/tomcat/webapps/JavaBridge/resource/dmc_logo.jpg';
#die($data[0]['img_dmc']);
#$data[0]['image_02'] = $baseurl . "img/doh.png";

if(strpos($age, 'y') !== false)
  showReport('barcoding-new', $params, $data, 'PDF');
else showReport('barcoding-new-child', $params, $data, 'PDF');
?>
