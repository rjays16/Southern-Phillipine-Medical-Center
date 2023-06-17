<?php
/**
 * @author : Syboy 11/19/2015 : meow
 * Description : Report of list of access permision
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'language/en/lang_en_access.php');
require_once($root_path.'global_conf/areas.php');
include('parameters.php');// var_dump($area_opt); die();
#_________________________________________________

$params->put('hosp_name',mb_strtoupper($hosp_name));
$params->put('ihomp',mb_strtoupper($ihomp));
$params->put('his_permission',mb_strtoupper($his_permission));
$from2 = date('F j, Y',$_GET['from_date']);
$to2 = date('F j, Y',$_GET['to_date']);
$params->put('date_span',$from2 . ' to ' . $to2);
$group_concat_limit = "SET SESSION group_concat_max_len = 1000000";
$db->Execute($group_concat_limit);
 // var_dump($areas); die();

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
global $db;
$sql = "SELECT 
		  fn_get_person_name (cp.pid) AS NAME,
		  cu.login_id AS username,
		  cpn.job_function_title as jobtitle,
		  sadt.duration AS timeDuration,
		  cd.name_formal AS Department,
		  sadt.`create_dt` AS DateTimeCreated,
		  cu.`create_id` AS RegisteredBy,
		  IF(cpn.`modify_time` <= cu.`modify_time`, cu.`modify_time`, cpn.`modify_time`) AS ModifiedDateTime,
		  cu.`create_id` AS RegisBy,
		  IF(cpn.`status` = 'deleted', 'Inactive: Locked', 'Active') AS STATUS,
		  cu.permission,
		  sadt.areas,
		  sadt.`mode`
		FROM
		  care_person cp 
		  LEFT JOIN care_personell cpn 
		    ON cpn.pid = cp.`pid` 
		  LEFT JOIN care_personell_assignment cpa 
		    ON cpa.`personell_nr` = cpn.`nr` 
		  LEFT JOIN care_department cd 
		    ON cd.`nr` = cpa.`location_nr`
		  LEFT JOIN care_users cu
    		ON cu.personell_nr = cpn.nr 
    	  LEFT JOIN seg_areas_duration_time sadt 
		    ON sadt.`pid` = cu.`personell_nr`
		WHERE sadt.`mode` IN ('save') AND DATE(sadt.`create_dt`) BETWEEN ('".$from."') 
		AND ('".$to."') GROUP BY sadt.`pid` ORDER BY sadt.`create_dt` DESC";

// var_dump($sql); die();
$permissions=$area_opt;
$grant_acc = array('title'.$titleCount++ => 'Credit and Collection Accounts');
$result = $db->GetAll("SELECT id, type_name, alt_name FROM seg_grant_account_type WHERE deleted = 0 ORDER BY id ASC");
foreach ($result as $key => $grn_acc) {
	$grant_acc['_a_1_grant_account_'.$grn_acc['id'].'_'.$grn_acc['type_name']] = ucwords($grn_acc['alt_name']);
}
$areas_final = array_merge($permissions, $grant_acc);
// var_dump($areas_final);die();
$areas=$areas_final;
$i = 0;
$data = array();
$rs = $db->Execute($sql);
if ($rs) {
	if ($rs->RecordCount()) {
		while ($row = $rs->FetchRow()) {
			$covered_area = '';
			$covered_module = '';
			$final_module = '';
			$permission = explode(' ', $row['areas']);
			$permission2 = "";
			foreach ($areas as $key => $value) {
				foreach ($permission as $value_permi) {
					
					if ($key == $value_permi) {
						if (!in_array($value_permi, $permission2)) {
						    $covered_area .= '>(Added) '.$value."<br />";
						}
					}
				}
			}
			$actionTaken = "Added Permission";
			$data[$i] = array(
				'name' => utf8_decode(trim($row['NAME'])),
				'dept' => $row['Department'],
				'dateTimeCreated' => date('Y-m-d h:i A', strtotime($row['DateTimeCreated'])),
				'registerBy' => $row['RegisteredBy'],
				'module' => $row['username'],
				'givenPermission' => $covered_area,
				'modiBy' => $row['RegisBy'],
				'status' => $row['jobtitle'],
				'dateTimeModi' => substr($row['timeDuration'], 0,9)
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