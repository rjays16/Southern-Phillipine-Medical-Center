<?php
/**
* ListGen.php
*
*
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."classes/json/json.php";
define('IPBM_OPD', '14');
define('IPBM_IPD', '13');
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
	$sortName = 'date';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(

	'date' => 'create_dt',
);

if (!$sortMap[$sortName]) $sort = 'IFNULL(modify_dt,create_dt) DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

$pid = $db->GetOne($query);
$data = Array();

if($pid) {

	$query = "SELECT 
			  sma.*,e.encounter_date
			FROM
			  seg_med_abstract AS sma
			  JOIN care_encounter AS e ON sma.encounter_nr = e.encounter_nr
			WHERE e.pid = ".$db->qstr($pid)."
			AND e.encounter_type IN(".IPBM_IPD.",".IPBM_OPD.")
		    AND e.STATUS NOT IN ('void', 'deleted', 'hidden')
		    ORDER BY $sort LIMIT $offset, $maxRows";

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
				'req_date' => nl2br(date("M-d-Y\nh:ia", strtotime(($row['modify_dt'] ? $row['modify_dt'] : $row['create_dt'])))),
				'encounter_nr' => $row['encounter_nr'],
				'encounter_date' => nl2br(date("M-d-Y\nh:ia", strtotime($row['encounter_date']))),
				'dept' => "IPBM",
				'create_id' => $row['modify_id'] ? $row['modify_id'] : $row['create_id'],
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

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($response);