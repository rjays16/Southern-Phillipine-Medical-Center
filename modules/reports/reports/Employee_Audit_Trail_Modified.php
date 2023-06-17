<?php
/**
 * @author : Syboy 03/29/2016 : meow
 * Description : LIST OF MODIFIED USER'S ACCOUNT
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'language/en/lang_en_access.php');
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

//start modified by Mark Guerra 3/22/2018
$sql = "SELECT 
		  fn_get_person_name (cp.pid) AS NAME,
		  cu.login_id AS username,
		  cpn.job_function_title as jobtitle,
		  sadt.duration AS timeDuration,
		  cd.name_formal AS Department,
		  sadt.`create_dt` AS DateTimeCreated,
		  IF(cpn.`modify_time` <= cu.`modify_time`, cu.`modify_time`, cpn.`modify_time`) AS ModifiedDateTime,
		  IF(cpn.`modify_id` = '', cu.`modify_id`, cpn.`modify_id`) AS ModifiedBy,
		  IF(cpn.`status` = 'deleted', 'Inactive: Locked', 'Active') AS STATUS,
		  sadt.areas,
		  sadt.old_areas,
		  sadt.create_id as ModiBy,
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
		    ON sadt.`pid` = cpn.`nr` or sadt.`pid` = cpn.`pid`
		WHERE sadt.`mode` NOT IN ('save') AND cu.personell_nr IS NOT NULL AND DATE(sadt.`create_dt`) BETWEEN ('".$from."') 
		AND ('".$to."')
		AND ((sadt.areas != sadt.old_areas 
		AND sadt.areas != '') OR sadt.`mode`='update pass' OR sadt.`mode` IN ('UNLOCK'))
		AND sadt.duration IS NOT NULL
		GROUP BY sadt.`id`,sadt.`create_dt`
		ORDER BY sadt.create_dt DESC";
//end modified by Mark Guerra 3/22/2018
// var_dump($sql); die();
 // var_dump($area_opt); die();
$permissions=$area_opt;
$grant_acc = array('title'.$titleCount++ => 'Credit and Collection Accounts');
$result = $db->GetAll("SELECT id, type_name, alt_name FROM seg_grant_account_type WHERE deleted = 0 ORDER BY id ASC");
foreach ($result as $key => $grn_acc) {
	$grant_acc['_a_2_grant_account_'.$grn_acc['id'].'_'.$grn_acc['type_name']] = ucwords($grn_acc['alt_name']);
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
			$permission2 = explode(' ', $row['old_areas']);
			foreach ($areas as $key => $value) {
				foreach ($permission as $value_permi) {
					
					if ($key == $value_permi) {
						if (!in_array($value_permi, $permission2)) {
						    $covered_area .= '>(Added) '.$value."<br />";
						}
					}
				}
				foreach ($permission2 as $value_permi2) {
					if ($key == $value_permi2) {
						if (!in_array($value_permi2, $permission)) {
						    $covered_area .= '>(Removed) '.$value."<br />";
						}
					}
				}
			}
			//start modified by Mark Guerra 3/22/2018
			if ($row['mode'] == "update") {
				if(strpos($covered_area, 'Added') && !strpos($covered_area, 'Removed')) $actionTaken = "Added Permission";
				else if(!strpos($covered_area, 'Added') && strpos($covered_area, 'Removed')) $actionTaken = "Removed Permission";
				else if(strpos($covered_area, 'Added') && strpos($covered_area, 'Removed')) $actionTaken = "Added and Removed Permissions";
				else $actionTaken = "Updated Permissions";
			}else if ($row['mode'] == "update pass") {
				$actionTaken = "Change user password";
			}elseif ($row['mode'] == "LOCK") {
				$actionTaken = "Locked User";
				$covered_area=""; //change lng
			}elseif ($row['mode'] == "UNLOCK") {
				$actionTaken = "Unlocked User";
				$covered_area="";
			}
			//end modified by Mark Guerra 3/22/2018
			
			$data[$i] = array(
				'name' => utf8_decode(trim($row['NAME'])),
				'dept' => $row['Department'],
				'dateTimeCreated' => date('Y-m-d h:i A', strtotime($row['DateTimeCreated'])),
				'registerBy' => $row['ModiBy'],
				'module' => $row['username'],
				'givenPermission' => $covered_area,
				'status' => $row['jobtitle'],
				'dateTimeModi' => substr($row['timeDuration'], 0,9),
				'actionTaken' => $actionTaken
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