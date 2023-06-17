<?php
/**
 * @author Matsuu - 02/05/2017
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',mb_strtoupper($hosp_name));

$param_get_encoder_id = end(explode(",",$_GET['param']));
$param_get_encoder_new_id = end(explode("--",$param_get_encoder_id));
$encoder_named = "SELECT 
  fn_get_pid_name (cp.pid) AS encoder 
FROM
  care_personell AS cp
  INNER JOIN care_users AS cu
  ON cu.`personell_nr` = cp.`nr`
WHERE cu.`login_id` =".$db->qstr($param_get_encoder_new_id);                  
            $phs_encoder_named = $db->GetOne($encoder_named);

$params->put('dateRange',"Period: " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));
$params->put('title',$phs_encoder=='all' || is_null($phs_encoder) ? "All" : $phs_encoder_named);
$params->put('user',$_SESSION['sess_user_name']);

$get_login = "SELECT cu.`login_id` FROM
  care_personell AS cp
  INNER JOIN care_users AS cu
  ON cu.`personell_nr` = cp.`nr`
WHERE cu.`login_id`  = " .$db->qstr($phs_encoder);
$get_login_id = $db->GetOne($get_login);
$cond1 = "DATE(sat.`date_changed`)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond = "DATE(sdm.`action_date`)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond2 = "DATE(snwat.`date_action`) 
                BETWEEN 
                 DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

if($phs_encoder != '' && $phs_encoder!='all'){
  $cond3 = " AND  sat.`login`= ".$db->qstr(utf8_decode($get_login_id));
  $cond5 = " AND sdm.action_id=".$db->qstr(utf8_decode($get_login_id));
  $cond4 = " AND snwat.action_personell=".$db->qstr(utf8_decode($get_login_id));
 

}else{
  $cond3 = " ";
  $cond5 = " ";
  $cond4 = " "; 
}
$employee = "SELECT 
  sat.`date_changed` AS action_date,
 fn_get_personell_firstname_last(sat.`pk_value`) AS employee,
  fn_get_person_name_first_mi_last (sat.`pk_value`) AS employees 
FROM
  seg_audit_trail AS sat 
WHERE " . $cond1 . $cond3."AND sat.`table_name` IN ('care_personell','care_users_trail','care_personell_assignment') UNION 
SELECT 
 snwat.`date_action` AS action_date,
 fn_get_personell_firstname_last(snwat.`personell_nr`) AS employee,
  fn_get_person_name_first_mi_last(snwat.`personell_nr`) AS employees
FROM
  `seg_inv_ward_accr_trail` AS snwat WHERE ".$cond2 . $cond4." GROUP BY snwat.`group_action`";
$dependent = " SELECT sdm.`action_date` AS action_date,
   fn_get_personell_firstname_last (sdm.`dependent_pid`) AS employee,
  fn_get_person_name_first_mi_last (sdm.`dependent_pid`) AS employees
FROM
  seg_dependents_monitoring AS sdm 
  LEFT JOIN care_personell AS cp 
    ON cp.`nr` = sdm.`dependent_pid`
WHERE " . $cond . $cond5." AND sdm.`action_taken` NOT IN ('deactivated') GROUP BY sdm.`dependent_pid`,
  sdm.`parent_pid`,
  sdm.`relationship`,
  sdm.`action_taken` ";

   $remarks = "SELECT 
  sat.`date_changed` AS action_date,
  fn_get_personell_firstname_last (sat.`pk_value`) AS employee,
  fn_get_person_name_first_mi_last (sat.`pk_value`) AS employees 
FROM
  seg_audit_trail AS sat 
WHERE " . $cond1 . $cond3."AND sat.`table_name` IN('seg_dependents_remarks','seg_dependents')";
if($phs_type=='employee'){
  $sql =$employee." ORDER BY action_date";
}
elseif($phs_type=='dependent'){
$sql = $dependent."UNION ALL ".$remarks." ORDER BY action_date";
}else{
   $sql = $employee. "UNION ALL ".$remarks."UNION ALL" . $dependent ." ORDER BY action_date";
}
// var_dump($employee);exit();
$res = $db->Execute($sql);
  $i = 1;
if($res){
  if($res->RecordCount() > 0){
      while($row = $res->FetchRow()){
         if(!empty($row['employee'])){
          $getName = $row['employee'];

        }
        else{
           $getName = $row['employees'];
        }
      
        $data[$i] = array(
                            'countno' => $i,
                            'modtime' => date("F j Y, h:i A",strtotime($row['action_date'])) ,
                            'name' => utf8_decode(trim(strtoupper($getName))),
                            'total' => $i,
                        );
                           $i++;
      }

  }
  else{
    $data[$i] = array(
                            'countno' => "No Record",
                            'modtime' => "No Record",
                            'name' => "No Record",
                            'total' => 0,
                        );
  }


  
}




