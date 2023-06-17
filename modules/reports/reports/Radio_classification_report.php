<?php
/**
 * @author : Syross P. Algabre 11/23/2015 02:00 Pm : meow
 * Radiology Report From old report of Rad to Report Launcher
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

#_______Page Header_______#
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('spanDate',mb_strtoupper($from) . ' to ' . mb_strtoupper($to));
$params->put("hosp_country", $hosp_country);
$params->put("hosp_agency", $hosp_agency);
$params->put("hosp_name", "SOUTHERN PHILIPPINES MEDICAL CENTER PHILIPPINES");
$params->put("hosp_addr1", $hosp_addr1);
$params->put("dept_of_radio","DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES");
$params->put("rccp","ROENTGENOLOGICAL CLASSIFICATION CASES' REPORT");
#_______Page Header_______#

#_______Page Footer_______#
$params->put("effective","Effectivity : September 1, 2014");
$params->put("revision","Revision : 1");
#_______Page Footer_______#

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
$params_arr = explode(",",$param);
$val_arr = explode("param_radio_classifi--", $params_arr[0]);
$not = 3;
$sc = 2;
$tpl = 1;
if ($params_arr[0] == '') {
	$rpt_cases = 'all';
}else if ($val_arr[1] != '') {
	$rpt_cases = $val_arr[1];
}

if ($rpt_cases==$tpl)
	$cases = "TPL";	
elseif ($rpt_cases==$sc)	
	$cases = "SENIOR CITIZEN";	
elseif ($rpt_cases==$not)	
	$cases = "NOT CLASSIFIED";	
else{
	if ($params_arr[0] == '') {
		$cases = 'All';
	}else if ($val_arr[1] == 'all') {
		$cases = 'All';
	}else{
		$info = $srvObj->getChargeTypeInfo($rpt_cases);
		$cases = mb_strtoupper($info['charge_name']);	
	}	
}
#$report_info = $srvObj->getListRadioClassification($rpt_cases, $tpl, $sc, $from, $to, $not);

global $db;
#if there's date
if (($from)&&($to))	{
	$date_cond = " AND  (s.request_date >= '".$from."' AND s.request_date <= '".$to."')";
}else
	$date_cond = "";


if ($rpt_cases==$tpl){
	$cond = " AND  s.is_tpl = '1' ";
}elseif ($rpt_cases==$sc){
	$cond = " AND (p.senior_ID != '' OR p.senior_ID != NULL) ";
}elseif ($rpt_cases==$not){
	$cond = " AND  ((p.senior_ID = '' OR p.senior_ID = NULL) AND s.is_tpl = '0' AND s.discountID='') ";
}elseif($rpt_cases=='all'){
	$cond = "";
}else{
	$cond = " AND s.grant_type = '$rpt_cases' ";
}

$items = "s.discountid AS classID, s.pid AS patientID,
			 g.name AS grp_name, g.other_name AS grp_name2,
			 ss.name AS service_name, s.*, d.*, ss.*,
			 p.*, c.grant_dte, c.sw_nr, enc.*, dept.name_formal, dept.name_short AS dept_name ";

$grp = "GROUP BY s.refno";

$order = " ORDER BY p.name_last, p.name_first, s.refno, g.name";


$sql = "SELECT $items
					FROM seg_radio_serv AS s
					INNER JOIN care_test_request_radio AS d
						ON s.refno=d.refno
					INNER JOIN seg_radio_services AS ss
						ON d.service_code=ss.service_code
					INNER JOIN seg_radio_service_groups AS g
						ON g.group_code=ss.group_code
					INNER JOIN care_department AS dept
						ON dept.nr=g.department_nr
					INNER JOIN care_person AS p
						ON p.pid=s.pid
					LEFT JOIN care_encounter AS enc
						ON s.encounter_nr = enc.encounter_nr
					LEFT JOIN seg_charity_grants AS c
						ON s.encounter_nr=c.encounter_nr
					WHERE s.status NOT IN('deleted','hidden','inactive','void') AND g.fromdept='RD'
					AND d.status NOT IN('deleted','hidden','inactive','void')
					$date_cond
					$cond
					$grp
					$order 	
					
				 ";

$params->put("classification","Classification : ".$cases);
$params->put("currency","Currency 	      : Philippine Peso (Php)");

// var_dump($sql); die();
$i = 0;
$data = array();
$all_total_amount = 0;
$total_paid = 0;
$total_amount_bal = 0;
$rs = $db->Execute($sql);
if ($rs) {
	$params->put("num_records","Number of Records : ".$rs->RecordCount());
	if ($rs->RecordCount()) {
		while ($row = $rs->FetchRow()) {
			if ($row['senior_ID'])
				$cases2 = "Senior Citizen";
			elseif ($row['is_tpl'])	
				$cases2 = "TPL";
			else{
				$info2 = $srvObj->getChargeTypeInfo($row['type_charge']);
				$cases2 = mb_strtoupper($info2['charge_name']);
			}	
			
			if (empty($cases2))
				$cases2 = "None";


			if ($row['encounter_type']==1){
				$patient_type = "ERPx";
				#$patient_type = "ER";
				$location = "ER";
			}elseif ($row['encounter_type']==2){
				$patient_type = "OPDPx";
				#$patient_type = "OPD";
				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = $dept['id'];
			}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
				if ($row['encounter_type']==3)
					$wer = "(ER)";
				elseif ($row['encounter_type']==4)	
					$wer = "(OPD)";
					
				$patient_type = "IPDPx ".$wer;
				
				$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
				$location = $ward['ward_id']." : Rm.#".$row['current_room_nr'];
			}else{
				$patient_type = "Walkin";
				$location = '';
			}

			$price = $srvObj->getSumPerTransaction($row['refno']);
			if ($row['is_cash'])
				$total_amount = $price['price_cash'];
			else	
				$total_amount = $price['price_charge'];

			$all_total_amount = $all_total_amount + $total_amount;
			$paid = $srvObj->getSumPaidPerTransaction($row['refno'],$row['patientID']);
			$total_paid = $total_paid + $paid['amount_paid'];
			$amount_bal = $total_amount - $paid['amount_paid'];
			$total_amount_bal = $total_amount_bal + $amount_bal;

			$data[$i] = array(
				'hrn' => $row['patientID'],
				'batch_nr' => $row['refno'],
				'name' => utf8_decode(trim($row['ordername'])),
				'order_dateTime' => $row['request_date'].' '.date("h:i:s A",strtotime($row['request_time'])),
				'ptype' => $patient_type,
				'class' => $cases2,
				'deptLocation' => $location,
				'request' => mb_strtoupper($row['service_name']),
				'section' => mb_strtoupper($row['dept_name']),
				'g_amount' => number_format($total_amount,2),
				'amount_paid' => number_format($paid['amount_paid'],2),
				'amount_bal' => number_format($amount_bal,2),
				);
			$i++;
		}

	}else{
		$data[0] = array('hrn' => 'No Data');
	}
}else{
	$data[0]['hrn'] = 'No Data';
}


$params->put("total_g_amount","GROSS AMOUNT 	   : Php ".number_format($all_total_amount,2));
$params->put("total_amount_paid","AMOUNT PAID   	   : Php ".number_format($total_paid,2));
$params->put("total_amount_bal","AMOUNT BALANCE  : Php ".number_format($total_amount_bal,2));
// die();
/*$baseurl = sprintf("%s://%s%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_ADDR'],substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir)));
$data[0]['image_02'] = $baseurl . "gui/img/logos/dmc_logo.jpg";*/