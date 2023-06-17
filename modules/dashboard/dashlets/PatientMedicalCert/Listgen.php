<?php

/** ListGen.php **/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."include/care_api_classes/class_acl.php";
require_once $root_path."classes/json/json.php";

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'create_dt';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'create_dt',
);
if (!$sortMap[$sortName]) $sort = 'create_dt ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

$pid = $db->GetOne($query);
$data = Array();
if($pid) {
	$query ="SELECT SQL_CALC_FOUND_ROWS
					m.create_dt as create_dt,
  					m.encounter_nr,
  					m.referral_nr,
	  				m.cert_nr,
  					c.encounter_date AS date_admit,
  					IF ( m.referral_nr,fn_get_department_name (r.referrer_dept),
    				fn_get_department_name (c.current_dept_nr)) AS dept,
  					m.create_id AS prepared_by, fn_get_personell_name (m.dr_nr) AS dr 
				FROM seg_cert_med AS m 
  				LEFT JOIN care_encounter AS c ON c.encounter_nr = m.encounter_nr 
  				LEFT JOIN seg_referral AS r ON r.encounter_nr = m.encounter_nr AND r.referral_nr = m.referral_nr 
				WHERE c.pid = ".$db->qstr($pid)." AND c.encounter_nr= ".$db->qstr($encounter_nr)." AND m.dr_nr = ".$db->qstr($_SESSION['sess_user_personell_nr'])."
				ORDER BY  $sort LIMIT $offset, $maxRows";

	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);

	
	$data = Array();
	if ($rs !== false)
	{
		$total = 0;
		$total = $db->GetOne("SELECT FOUND_ROWS()");
		$rows = $rs->GetRows();
		foreach ($rows as $row)
		{
			$data[] = Array(
				'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row["create_dt"]))),
				'case_no' => $row['encounter_nr'],				
				'DateAdmitted' =>nl2br(date("M-d-Y\nh:ia", strtotime($row['date_admit']))),
				'department' => $row['dept'],				
				'prepared' => $row['prepared_by'],
				'cert_nr' => $row['cert_nr']	
			);
		}
	}
}

if (!$data)
{
	$total = 0;
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
);

/** Convert data to JSON and print **/

$json = new Services_JSON;
print $json->encode($response);