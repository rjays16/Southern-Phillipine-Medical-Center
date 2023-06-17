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

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');


$query = "SELECT SQL_CALC_FOUND_ROWS sad.encounter_nr,sad.date_changed,
  IF(
    sad.old_final_diagnosis IS NULL 
    OR sad.old_final_diagnosis = '',
    sad.old_other_diagnosis,
    sad.old_final_diagnosis
  ) AS diagnosis,
  sad.tod,
 fn_get_personell_lastname_first_by_loginid(sad.encoder)  AS doc_name 
 FROM seg_audit_diagnosis AS sad  WHERE sad.encounter_nr=".$db->qstr($encounter_nr)." ORDER by date_changed DESC LIMIT $offset, $maxRows";


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
			'pid' => $row["pid"],
			'encounter_nr' => $row["encounter_nr"],
			'date_changed' => date("m-d-Y h:i A",strtotime($row["date_changed"])),
			'doctor_name' => $row["doc_name"],
			'diagnosis' => $row["diagnosis"],
			'tod' => $row["tod"]

		);
	}
}
else
{
	$total = 0;
	echo $query;
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