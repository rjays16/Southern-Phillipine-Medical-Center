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

$agency_id = $_REQUEST['agency_id'];
$search = $_REQUEST['search_person'];
$optionList=$_REQUEST['optionList'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'patient_id' => 'pid',
	'patient_name' => 'name',
	'patient_sex' => 'sex'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'pid';

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


$strName=str_replace(".","",$search);
			$name=split(",",$strName);
			$name_last=trim($name[0]);
			$name_firstMiddle=trim($name[1]);
if(!empty($strName) and !empty($search))
			$cond="and cp.name_last LIKE '".$name_last."%' and cp.name_first LIKE '".$name_firstMiddle."%' ";


$sql = "SELECT SQL_CALC_FOUND_ROWS cp.pid, fn_get_person_name(cp.pid) as `name`, \n".
			"cp.date_birth, cp.age, IF(cp.sex='f','Female','Male') AS `sex`, cp.civil_status \n".
			"FROM seg_industrial_comp_emp AS ap \n".
			"INNER JOIN seg_industrial_company AS am ON ap.company_id=am.company_id \n".
			"INNER JOIN care_person AS cp ON cp.pid=ap.pid \n".
			"WHERE ap.status<>'deleted' AND am.company_id=".$db->qstr($agency_id);

$sql.=$cond;
//echo $sql;
//die($sql);
if($sort_sql) {
	$sql.=" ORDER BY {$sort_sql} ";
}
if($maxRows) {
	$sql.=" LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);


//echo $sql;
/*echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");

	while ($row = $result->FetchRow()) {
		if(!empty($optionList)){
					$data[] = array(
					'patient_id' => $row['pid'],
					'patient_name' => strtoupper($row['name']),
					'patient_bdate' => date('d-M-Y', strtotime($row['date_birth'])),
					'patient_sex' => $row['sex'],
					'option' =>
										'<input type="button" id="btn'.$row['pid'].'"
										onclick="this.disbled=true;prepareSelect('.
										'\''.$row['pid'].'\', '.
										'\''.strtoupper($row['name']).'\' '.
										')"
											 value=">" />'
					);


		}
		else{

					$data[] = array(
					'patient_id' => $row['pid'],
					'patient_name' => strtoupper($row['name']),
					'patient_bdate' => date('d-M-Y', strtotime($row['date_birth'])),
					'patient_age' => $row['age'],
					'patient_sex' => $row['sex'],
					'patient_status' => ucfirst($row['civil_status'])
				);
		}

	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);