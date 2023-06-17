<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."classes/json/json.php";

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'patient';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'date',
	'admission' => 'admission',
	'department' => 'department'
);
if (!$sortMap[$sortName]) $sort = 'patient ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$file = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($file);

$pid = $db->GetOne($query);

$data = Array();
if ($pid)
{
	$query = "SELECT 
  t.orderby,
  t.id,
  t.pid,
  t.encounter_nr,
  t.in_charged,
  t.status 
FROM
  (SELECT 
    1 AS orderby,
    scd.`id`,
    scd.`pid`,
    scd.`encounter_nr`,
    scd.`in_charged`,
    scd.`status` 
  FROM
    `seg_counseled_discharged` AS scd 
  WHERE scd.`pid` = ".$db->qstr($pid)."
    AND scd.`isreferral` = '1' 
    AND scd.is_canceled <> '1' 
  UNION
  ALL 
  SELECT 
    2 AS orderby,
    scm.`id`,
    scm.`pid`,
    scm.`encounter_nr`,
    scm.`in_charged`,
    scm.`status` 
  FROM
    `seg_counseled_monitoring` AS scm 
  WHERE scm.`pid` = ".$db->qstr($pid)."
    AND scm.`isreferral` = '1' 
    AND scm.is_canceled <> '1' 
  UNION
  ALL 
  SELECT 
    3 AS orderby,
    sc.`id`,
    sc.`pid`,
    sc.`encounter_nr`,
    sc.`in_charged`,
    'seen' AS STATUS 
  FROM
    `seg_counseled` AS sc 
   WHERE sc.`pid` = ".$db->qstr($pid)."
    AND sc.is_canceled <> '1' 
  UNION
  ALL 
  SELECT 
    4 AS orderby,
    scd.`id`,
    scd.`pid`,
    scd.`encounter_nr`,
    scd.`in_charged`,
    scd.`status` 
  FROM
    `seg_counseled_discharged` AS scd 
  WHERE scd.`pid` = ".$db->qstr($pid)."
    AND scd.`isreferral` = '0' 
    AND scd.is_canceled <> '1' 
  UNION
  ALL 
  SELECT 
    5 AS orderby,
    scm.`id`,
    scm.`pid`,
    scm.`encounter_nr`,
    scm.`in_charged`,
    scm.`status` 
  FROM
    `seg_counseled_monitoring` AS scm 
  WHERE scm.`pid` = ".$db->qstr($pid)."
    AND scm.`isreferral` = '0' 
    AND scm.is_canceled <> '1') AS t 
GROUP BY t.pid,
  encounter_nr 
ORDER BY t.orderby "
.		"LIMIT $offset, $maxRows";


	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);
	if ($rs !== false)
	{
		$total = 0;
		$total = $db->GetOne("SELECT FOUND_ROWS()");
		$rows = $rs->GetRows();
		foreach ($rows as $row)
		{
			$data[] = Array(
				'caseno' => $row['encounter_nr'],
				'assessedby' => $row['in_charged'],
				'options' => $row['encounter_nr'],
				'status' => $row['status']
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