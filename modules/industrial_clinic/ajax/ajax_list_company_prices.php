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

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'item_code' => 'item_code',
	'item_name' => 'item_name',
	'item_area' => 'item_area'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'item_name';

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

$result = $icObj->getCompanyServices($company_id, $search_key, $sort_sql, $offset, $maxRows);

//echo "sql=".$icObj->sql;
/*echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $icObj->FoundRows();
	while ($row = $result->FetchRow()) {

		$data[] = array(
			'item_code' => strtoupper($row['item_code']),
			'item_name' => ucfirst($row['item_name']),
			'item_area' => strtoupper($row['item_area']),
			'item_price' => '<input type="text" style="text-align:right" class="segInput" value="'.number_format($row['item_price'],2).'" id="comp_item_price'.$row['item_code'].'" name="comp_item_prices[]"/>',
			'options' => '<img src="../../images/cashier_edit.gif" onclick="editServicePriceToCompany(\''.$row['item_code'].'\',\''.$row['item_area'].'\');" style="cursor:pointer;" title="Edit price"/>&nbsp;'.
									 '<img src="../../images/cashier_delete.gif" onclick="deleteServicePriceToCompany(\''.$row['item_code'].'\',\''.$row['item_area'].'\');" style="cursor:pointer;" title="Delete item"/>',
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