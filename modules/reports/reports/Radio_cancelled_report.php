<?php
/**
 * @author : Aiza M. Romano 05/31/2016 08:00 Pm 
 * Cancelled Radiology Report
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
$params->put("hosp_name", "SOUTHERN PHILIPPINES MEDICAL CENTER");
$params->put("rccp","LIST OF CANCELLED REQUESTS");
//$params->put('img_spmc', $baseurl . "gui/img/logos/dmc_logo.jpg");
//$params->put('img_doh', $baseurl . "img/doh.png");
#_______Page Header_______#

#_______Page Footer_______#
$params->put("effective","Effectivity : September 1, 2014");
$params->put("revision","Revision : 1");
#_______Page Footer_______#

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
$params_arr = explode(",",$param);
//$val_arr = explode("param_radio_cancel--", $params_arr[0]);
$ptype = explode("param_radio_pattype--", $params_arr[0]);
$rsection = explode("param_radio_section--", $params_arr[1]);
$ptype = $ptype[1];
$rsection = $rsection[1];


global $db;
#if there's date
if (($from)&&($to))	{
	$date_cond = " AND  (s.request_date >= '".$from."' AND s.request_date <= '".$to."')";
}else
	$date_cond = "";

if ($ptype == '')
	$ptypecond = "";
if ($ptype == '0')
    $ptypecond = "";
elseif ($ptype == '1')
    $ptypecond = "AND encounter_type IN (1)";
elseif ($ptype == '2')
    $ptypecond = "AND encounter_type IN (3,4)";
elseif ($ptype == '3')
    $ptypecond = "AND encounter_type IN (2)";
elseif ($ptype == '4')
    $ptypecond = "AND encounter_type IS NULL";
elseif ($ptype == '5')
    $ptypecond = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
elseif ($ptype == '6')
    $ptypecond = "AND encounter_type IN (1,3,4)";
elseif ($ptype == '7')
    $ptypecond = "AND encounter_type IN (6)";

if ($rsection)
	$section_cond = " AND g.department_nr='".$rsection."'";
else
	$section_cond = "";


$items = "s.discountid AS classID, s.pid AS patientID, s.modify_dt AS dateModified,
			 g.name AS grp_name, g.other_name AS grp_name2,
			 ss.name AS service_name, s.*, d.*, ss.*,
			 p.senior_ID, p.sex, u.name as cancelby, ROUND(DATEDIFF(CURDATE(),p.date_birth) / 365,0) AS age, c.grant_dte, c.sw_nr, enc.*, dept.name_formal, dept.name_short AS dept_name ";


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
					INNER JOIN care_users AS u
						ON u.login_id=s.modify_id
					LEFT JOIN care_encounter AS enc
						ON s.encounter_nr = enc.encounter_nr
					LEFT JOIN seg_charity_grants AS c
						ON s.encounter_nr=c.encounter_nr
					WHERE s.status IN('deleted','hidden','inactive','void') AND g.fromdept='RD'
					$ptypecond
					$section_cond
					$date_cond
					$grp
					$order
					";

				

$params->put("currency","Currency 	      : Philippine Peso (Php)");

// var_dump($report_info); die();
$i = 0;
$data = array();

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
				$erLoc = $dept_obj->getERLocation($row['er_location'], $row['er_location_lobby']);
				if($row['er_location']){
						$location = "/".strtoupper("ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")");
					}
				else
					$location="";
				$patient_type = "ERPx";
				#$location = "Emergency Room";

				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = $dept['name_formal']."".$location;
			}elseif ($row['encounter_type']==2){
				$patient_type = "OPDPx";
				#$patient_type = "OPD";
				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = $dept['name_formal'];
			}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
				if ($row['encounter_type']==3)
					$wer = "(ER)";
				elseif ($row['encounter_type']==4)	
					$wer = "(OPD)";
					
				$patient_type = "IPDPx ".$wer;
				
				$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
				$location = "/".$ward['ward_id']." : Rm.#".$row['current_room_nr'];
				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = $dept['name_formal']."".$location;

			}else{
				$patient_type = "Walkin";
				$location = 'None';
			}

			$requestedby = str_replace(" .","",$row['manual_doctor']);
			
			$data[$i] = array(
				'hrn' => $row['patientID'],
				'batch_nr' => $row['refno'],
				'name' => utf8_decode(trim(mb_strtoupper($row['ordername']))),
				'patientage' => $row['age'].' yr',
				'psex' => mb_strtoupper($row['sex']),
				'order_dateTime' => date("m/d/Y",strtotime($row['request_date'])).' '.date("h:i A",strtotime($row['request_time'])),
				'ptype' => $patient_type,
				'deptLocation' => $location,
				'request' => mb_strtoupper($row['service_name']),
				'reqby' => mb_strtoupper($requestedby),
				'section' => mb_strtoupper($row['dept_name']),
				'cancelledby' => $row['cancelby'],
				'datetimecancelled' => date("m/d/Y h:i A",strtotime($row['dateModified']))
				);
			$i++;
		}

	}else{
		$data[0] = array('hrn' => 'No Data');
	}
}else{
	$data[0]['hrn'] = 'No Data';
}

// die();
$baseurl = sprintf("%s://%s%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_ADDR'],substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir)));
$data[0]['image_02'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['img_03'] = $baseurl . "img/doh.png";
