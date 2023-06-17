<?php
/**
 * @author : Syboy 03/30/2016 : meow
 * Description : LIST OF DEACTIVATED USER'S ACCOUNT
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'global_conf/areas.php');
include('parameters.php');
// var_dump($area_opt); die();
#_________________________________________________
$params->put('hosp_name',mb_strtoupper($hosp_name));
$params->put('ihomp',mb_strtoupper($ihomp));
$params->put('his_permission',mb_strtoupper($his_permission));
$from2 = date('F j, Y',$_GET['from_date']);
$to2 = date('F j, Y',$_GET['to_date']);
$params->put('date_span',$from2 . ' to ' . $to2);


$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
global $db;
$group_concat_limit = "SET SESSION group_concat_max_len = 1000000";
$db->Execute($group_concat_limit);
$sql = "SELECT 
  fn_get_person_name (sadt.pid) AS NAME,
  cu.login_id AS username,
  cpn.job_function_title AS jobtitle,
  cd.name_formal AS Department,
  sadt.`create_dt` AS DateTimeDeactivated,
  sadt.`create_dt` ModifiedDateTime,
  IF(
    sadt.`create_id` = 'Administrator',
    'SYSTEM',
    sadt.`create_id`
  ) AS ModifiedBy,
  IF(
    sadt.`create_id` = 'Administrator',
    'AUTO-LOCKED',
    'LOCKED'
  ) AS STATUS,
  cpr.remarks AS remarks,
  sadt.`duration` AS duration,
  sadt.reason AS reason
FROM
  seg_areas_duration_time sadt 
  LEFT JOIN care_personell cpn 
    ON sadt.`pid` = cpn.`pid` 
  LEFT JOIN care_users cu 
    ON cu.`personell_nr` = cpn.nr 
  LEFT JOIN care_personell_assignment cpa 
    ON cpa.`personell_nr` = cpn.`nr` 
  LEFT JOIN care_department cd 
    ON cd.`nr` = cpa.`location_nr` 
  LEFT JOIN care_personell_remarks cpr 
    ON cpn.nr = cpr.nr AND TIMESTAMPDIFF(
    SECOND,
    cpr.create_date,
    sadt.create_dt
  ) < 3 
  AND TIMESTAMPDIFF(
    SECOND,
    cpr.create_date,
    sadt.create_dt
  ) > - 3 
WHERE sadt.mode = 'LOCK'
  AND cu.login_id <> 'NULL' 
  AND DATE(sadt.`create_dt`) BETWEEN ('".$from."') 
		AND ('".$to."') ORDER BY sadt.`create_dt` DESC";

//var_dump($sql); die();

$i = 0;
$data = array();
$rs = $db->Execute($sql);

if ($rs) {
	if ($rs->RecordCount()) {
		while ($row = $rs->FetchRow()) {
                 
    if (trim($row['reason']) == ''){
      $reason = $row['remarks'];
    } 

    if (trim($row['remarks']) == '') {
      $reason = $row['reason'];
    }

			$data[$i] = array(
				'name' => utf8_decode(trim($row['NAME'])),
				'dept' => $row['Department'],
				'dateTimeCreated' => date('Y-m-d h:i A', strtotime($row['DateTimeDeactivated'])),
				'registerBy' => $row['ModifiedBy'],
				'module' => $row['username'],
				'givenPermission' => $reason,
				'status' => $row['jobtitle'],
				'dateTimeModi' => substr($row['duration'], 0,9),
				'actionTaken' => $row['STATUS']
				);
			$i++;
		}
	}else{
		$data[0] = array('name' => 'No Data');
	}
}else{
	$data[0]['name'] = 'No Data';
}
// die();