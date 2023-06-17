<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/industrial_clinic/class_agency_mgr.php');

global $db;
$icObj = new SegAgencyManager();

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$search_key = $_REQUEST['search_key'];
$company_id = $_REQUEST['company_id'];
$mode = $_REQUEST['mode'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'package_name' => 'package_desc'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'package_name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

$result = $icObj->listCompanyPackages($company_id, $search_key, $sort_sql, $offset, $maxRows);

//echo "sql=".$icObj->sql;
/*echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $icObj->FoundRows();
	while ($row = $result->FetchRow()) {

		if($mode=='editpackage') {
			$buttons = '<button class="segButton" onclick="editCompanyPackage(\''.$row['package_id'].'\');return false;" style="cursor:pointer;" title="Edit package"><img src="../../gui/img/common/default/note_edit.png"/>Edit</button>&nbsp;'.
									 '<button class="segButton" onclick="deleteCompanyPackage(\''.$row['package_id'].'\');return false;" style="cursor:pointer;" title="Delete package"><img src="../../gui/img/common/default/delete.png"/>Delete</button>';
		} else if($mode=='otherpackage') {
			$buttons = '<button class="segButton" onclick="copyCompanyPackage(\''.$row['package_id'].'\');return false;" style="cursor:pointer;" title="Copy package"><img src="../../gui/img/common/default/note_edit.png"/>Copy</button>';
		}

		$data[] = array(
			'package_name' => ucfirst($row['package_desc']),
			'package_price' => number_format($row['price'],2),
			'options' => $buttons,
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