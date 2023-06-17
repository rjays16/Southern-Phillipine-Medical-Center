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
	//'date' => 'r.request_date, r.request_time',
	'date' => 'date_request',
);
//if (!$sortMap[$sortName]) $sort = 'request_date, request_time DESC';
if (!$sortMap[$sortName]) $sort = 'date_request DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

$pid = $db->GetOne($query);
$data = Array();
if($pid) {
	
	$query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT r_serv.refno, \n".
			 "CONCAT(r_serv.request_date,' ',r_serv.request_time) as date_request, \n".
             "IF((r_serv.is_cash=0 && request_flag IS NULL), 'Charge', r.request_flag) AS or_no \n".
             "FROM seg_radio_serv AS r_serv\n".
             "INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno\n".
             "WHERE r_serv.pid=".$db->qstr($pid)."\n".
             "AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0)\n".
             "AND r_serv.STATUS NOT IN ('deleted','hidden','inactive','void')\n".
             "AND r.STATUS NOT IN ('deleted','hidden','inactive','void')\n".
             "GROUP BY r_serv.refno \n".
             "ORDER BY $sort \n".
			 "LIMIT $offset, $maxRows";

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
				'refno' => $row['refno'],
				'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row['date_request']))),
				'pid' => $pid
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