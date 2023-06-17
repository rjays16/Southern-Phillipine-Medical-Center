<?php 
/*
 * @author : syboy 07/22/2015
 */ 
require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

$year = $_GET['year'];
$month = $_GET['month'];

$start = strtotime($year . '-' . $month . '-01');
$end = strtotime('+1 month', $start);
$month_year = date('M',$start) . " " . date('Y',$start);

$start = date('Y-m-d',$start);
$end = date('Y-m-d',$end);

// die('wew');
global $db;

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('hosp_addr1',$hosp_addr1);
$params->put('date_span',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));
$params->put('report_title',"Summary of TOP 15 ICD and ICP (ACR)");

$nbb = array("G","S","NO","NS");
$notnbb = array("K","SC","HSM","PS","I","POS"); 
$mem_category = strtr($mem_cats, array("'"=>""));
$catergory = explode(",", $mem_category);
$hasNBBparams = 0;
$hasNoNBBparams = 0;

if(count($catergory) > 1){
	foreach ($catergory as $value) {
		if(in_array($value,$nbb) && !in_array($value, $notnbb))
			$hasnbbparams++;
		elseif (!in_array($value,$nbb) && in_array($value, $notnbb))
			$hasNoNBBparams++;
	}

	if($hasnbbparams > 1 && !$hasNoNBBparams)
		$title = "Other Category";
	elseif($hasNoNBBparams > 1 && !$hasnbbparams)
		$title = "No Balance Billing";
}else{
	if($catergory[0] == 'G')
		$title = 'Government Employed Only';
	elseif($catergory[0] == 'S')
		$title = 'Private Employed Only';
	elseif($catergory[0] == 'NO')
		$title = 'Overseas Worker (OFW) Only';
	elseif($catergory[0] == 'NS')
		$title = 'Individual Paying-Self Employed Only';
	elseif($catergory[0] == 'K')
		$title = 'Kasambahay (Household-help) Only';
	elseif($catergory[0] == 'SC')
		$title = 'Senior Citizen Only';
	elseif($catergory[0] == 'HSM')
		$title = 'Hospital Sponsored Member Only';
	elseif($catergory[0] == 'PS')
		$title = 'Lifetime Member Only';
	elseif($catergory[0] == 'I')
		$title = 'Sponsored Member Only';
	elseif($catergory[0] == 'POS')
		$title = 'Point of Service Only';

}

if (!isset($mem_cats) || $mem_cats == 'all') {
	$where[] = " ";
	$title = "All";
}
 else {
    $where[] = "AND sm.memcategory_code in (" . $mem_cats .")";
}

$params->put("category",$title);
$condition = implode(') AND (', $where);

$sql_icd = "SELECT
			  package.`code`,
			  package.`description`,
			  COUNT(bill.bill_nr) AS cnt 
			FROM
			  seg_billing_encounter AS bill 
			   INNER JOIN care_encounter e 
			    ON e.encounter_nr = bill.encounter_nr 
			  INNER JOIN seg_billing_caserate AS caserate 
			    ON bill.bill_nr = caserate.`bill_nr`
				INNER JOIN `care_encounter` AS ce
   			 ON ce.`encounter_nr` = bill.`encounter_nr` 
			  INNER JOIN `seg_case_rate_packages` AS package
			    ON caserate.`package_id` = package.`code`
					AND (STR_TO_DATE(ce.encounter_date, '%Y-%m-%d') >= STR_TO_DATE(package.`date_from`, '%Y-%m-%d') 
									AND STR_TO_DATE(ce.encounter_date, '%Y-%m-%d') <= STR_TO_DATE(package.`date_to`, '%Y-%m-%d'))
			  LEFT JOIN seg_encounter_memcategory sem 
			    ON sem.encounter_nr = e.encounter_nr 
			  LEFT JOIN seg_memcategory sm 
			    ON sm.memcategory_id = sem.memcategory_id 
			WHERE STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') >= STR_TO_DATE(".$db->qstr(date('Y-m-d',$from_date)).", '%Y-%m-%d') 
			  AND STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') <= STR_TO_DATE(".$db->qstr(date('Y-m-d',$to_date)).", '%Y-%m-%d') 
			  AND bill.is_deleted IS NULL 
			  AND bill.is_final = 1
			  AND caserate.`rate_type` = 1 
			  AND package.`case_type` = 'm'
			  $condition
			GROUP BY package.code 
			ORDER BY cnt DESC 
			LIMIT 15";

$sql_icp = "SELECT 
			  package.`code`,
			  package.`description`,
			  COUNT(DISTINCT bill.bill_nr) AS cnt 
			FROM
			  seg_billing_encounter AS bill 
			   INNER JOIN care_encounter e 
			    ON e.encounter_nr = bill.encounter_nr 
			  INNER JOIN seg_billing_caserate AS caserate 
			    ON bill.bill_nr = caserate.`bill_nr`
				INNER JOIN `care_encounter` AS ce
   			 ON ce.`encounter_nr` = bill.`encounter_nr`  
			  INNER JOIN `seg_case_rate_packages` AS package 
			    ON caserate.`package_id` = package.`code`
					AND (STR_TO_DATE(ce.encounter_date, '%Y-%m-%d') >= STR_TO_DATE(package.`date_from`, '%Y-%m-%d') 
									AND STR_TO_DATE(ce.encounter_date, '%Y-%m-%d') <= STR_TO_DATE(package.`date_to`, '%Y-%m-%d'))
			   LEFT JOIN seg_encounter_memcategory sem 
			    ON sem.encounter_nr = e.encounter_nr 
			  LEFT JOIN seg_memcategory sm 
			    ON sm.memcategory_id = sem.memcategory_id 
			WHERE STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') >= STR_TO_DATE(".$db->qstr(date('Y-m-d',$from_date)).", '%Y-%m-%d') 
			  AND STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') <= STR_TO_DATE(".$db->qstr(date('Y-m-d',$to_date)).", '%Y-%m-%d') 
			  AND bill.is_deleted IS NULL 
			  AND bill.is_final = 1
			  AND caserate.`rate_type` = 1 
			  AND package.`case_type` = 'p'
			  $condition
			GROUP BY package.code 
			ORDER BY cnt DESC 
			LIMIT 15";
// echo $sql_icd; die();
$i = 0;
$data = array();

$rs_icd = $db->GetAll($sql_icd);
$rs_icp = $db->GetAll($sql_icp);

for ($i=0; $i < 15 ; $i++) { 
	$data[] = array(
		'code' => $rs_icd[$i]['code'],
		'description' => $rs_icd[$i]['description'],
		'count' => $rs_icd[$i]['cnt'],
		'code2' => $rs_icp[$i]['code'],
		'description2' => $rs_icp[$i]['description'],
		'count2' => $rs_icp[$i]['cnt']
		);
}
 ?>