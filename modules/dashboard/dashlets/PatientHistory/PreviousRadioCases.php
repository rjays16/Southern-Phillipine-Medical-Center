<?php
/**
 * Created by Macoy 07-24-2014
 * for previous radio result 
 */

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
	$sortName = 'date';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(

	'date' => 'request_date',
	
);
if (!$sortMap[$sortName]) $sort = 'request_date DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$objAcl = new Acl($_SESSION['sess_temp_userid']);
$permission_RadioResultsPDF = $objAcl->checkPermissionRaw('_a_2_unifiedResults');

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $_REQUEST['encounter'];

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

$pid = $db->GetOne($query);
$data = Array();
if($pid) {
	$query = "SELECT SQL_CALC_FOUND_ROWS r.refno, \n".
						"fn_get_radiotest_request_code_all(d.refno) AS services, \n".
						"CONCAT(SUBSTRING(r.request_date, 1, 10),' ', r.request_time) AS `request_date`, r.is_urgent, \n".
						"d.service_date, d.request_flag, r.encounter_nr, r.pid \n".
						"FROM seg_radio_serv AS r \n".
						"INNER JOIN care_test_request_radio AS d ON d.refno=r.refno \n".
						"WHERE r.status NOT IN ('deleted','hidden','inactive','void') \n".
						"AND d.status NOT IN ('deleted','hidden','inactive','void') \n".
						"AND r.encounter_nr=".$db->qstr($encounter_nr)."\n".
						"AND r.pid=".$db->qstr($pid)."\n".
						"GROUP BY r.refno \n".
						"ORDER BY  $sort \n".
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
				'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row["request_date"]." ".$row["request_time"]))),
				'service' => strtoupper($row['services']) ,
				'refno' => $row["refno"],
				'pid' => $row['pid'],
                'permission' => $permission_RadioResultsPDF
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