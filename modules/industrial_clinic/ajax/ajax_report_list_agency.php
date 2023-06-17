<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$search_key = $_REQUEST['search_key'];
$mode = $_REQUEST['mode'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'agency_name' => 'name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'agency_name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
//$phFilters = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}


$sql = "SELECT SQL_CALC_FOUND_ROWS ia.* FROM seg_industrial_company AS ia WHERE status <> 'deleted' ";
if($search_key) {
	$sql.=" AND name like '%$search_key%' ";
}
if($sort_sql) {
	$sql.=" ORDER BY {$sort_sql} ";
}
if($maxRows) {
	$sql.=" LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);


/*echo $sql;
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");

	while ($row = $result->FetchRow()) {

		if($mode=="cashier_search") {
			$select_btn = '<button class="segButton" style="cursor: pointer;" onclick="selectCompany(\''.$row['company_id'].'\',\''.$row['name'].'\',\''.$row['address'].'\');return false;">Select</button>';
			$options = $select_btn;
		} else {
			$details_btn = '<button class="segButton" style="cursor: pointer;" onclick="openDetailsTray(\''.$row['company_id'].'\');return false;" title="Print-out of all Requested Services"><img src="../../gui/img/common/default/page_go.png"/>View</button>';
			$options = $details_btn.'&nbsp;'.$delete_btn;
		}


		$data[] = array(
			'agency_name' => strtoupper($row['name']),
			'agency_address' => $row['address'],
			'agency_no' => $row['contact_no'],
			'options' => $options
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);