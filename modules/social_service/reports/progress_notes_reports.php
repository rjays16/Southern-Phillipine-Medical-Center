
<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_personell.php');
global $db;
$baseDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';
// Created By: Anonymous
$cond1 = "DATE(sspn.progress_date)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', strtotime($_GET['frm_date']))) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', strtotime($_GET['to_date']))) . ")";  
// End
$sql = "SELECT sspn.`progress_date`,sspn.`ward`,cw.`name`,sspn.`referral`,sspn.`diagnosis`,sspn.`relationship`,sspn.`purpose`,sspn.`informant`,sspn.`action_taken`,sspn.`recommendation`,`fn_get_personell_firstname_last`(sspn.`create_id`) AS medsocwork,sspn.`create_id` AS medsocname FROM seg_social_progress_notes AS sspn LEFT JOIN care_ward AS cw ON cw.`nr` = sspn.`ward` WHERE ".$cond1." AND sspn.pid =".$db->qstr($pid)." AND sspn.is_deleted ='0' ORDER BY sspn.`create_time` DESC";
/*var_dump($sql);die;*/
  $rs = $db->Execute($sql);
 if ($rs->RecordCount() > 0){
    $i=0;
   while($row=$rs->FetchRow()){ 
         $data[$i] =array(
        'dtwd' =>  date("Y-m-d h:ia",strtotime($row['progress_date']))."\n\n".$row['name']."\n\n".$row['diagnosis'],
        'ext' =>$row['referral']=='external'?('/'):'',
        'int' =>$row['referral']=='internal'?('/'):'',
        'informant' =>$row['informant'],
        'relationship' =>$row['relationship'],
        'purpose' =>$row['purpose'],
        'action_taken' =>$row['action_taken'],
        'recommendation' =>$row['recommendation'],
        'encoder' =>$row['medsocname']
        );
     $i++;
   }
 }
 $get_patient_info ="SELECT fn_get_pid_lastfirstmi (sspn.pid) AS name_of_patient,fn_get_age (NOW(), cp.date_birth) AS age,fn_get_complete_address (cp.pid) AS address,cp.pid, cp.sex FROM seg_social_progress_notes AS sspn LEFT JOIN care_person AS  cp ON cp.pid = sspn.pid WHERE sspn.pid = ".$db->qstr($pid)." and sspn.is_deleted ='0' GROUP BY sspn.pid";
 // var_dump($get_patient_info);die;
        $result=$db->Execute($get_patient_info);
          while($row_data = $result->FetchRow()){
                $name = $row_data['name_of_patient'];
                $age = $row_data['age'];
                $address = $row_data['address'];
                $hrn = $row_data['pid'];
                $sex = $row_data['sex'];
            }
            
        if($sex == 'm') {
          $sex = "Male";
        }else{
          $sex = "Female";
        }


  $logo_path = $baseDir.'gui/img/logos/dmc_logo.jpg';
  $logo_path1 = $baseDir.'img/doh.png';

  $params = array('name' => $name,
                  'address' => $address,
                  'age' =>$age,
                  'hrn' => $hrn,
                  'sex' => $sex,
                  'logo_path' => $logo_path1,
                  'logo_path1' => $logo_path,
                  'date_span' => "Period: " . date('F d, Y', strtotime($_GET['frm_date'])) . " to " . date('F d, Y', strtotime($_GET['to_date'])));

showReport('progress_notes',$params,$data,'PDF');
?>
