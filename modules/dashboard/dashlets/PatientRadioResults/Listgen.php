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
require_once $root_path."include/care_api_classes/class_acl.php";
require_once $root_path."classes/json/json.php";

#added by VAN 10-09-2014
#PACS
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_parse_hl7_message.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

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
	//'date' => 'r.request_date, r.request_time',
	'date' => 'request_date',
);
//if (!$sortMap[$sortName]) $sort = 'request_date, request_time DESC';
if (!$sortMap[$sortName]) $sort = 'request_date DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;


#added by VAN 10-09-2014
function parseHL7Result($refno, $pid){
	$radio_obj = new SegRadio;
	$parseObj = new seg_parse_msg_HL7();
    $hl7fxnObj = new seg_HL7();
	
	$info = $radio_obj->getHL7Result($refno, $pid);
	
	if ($info['filename']){
    	$details['hl7_msg'] = $info['hl7_msg'];
		$details['filename'] = $info['filename'];
		#echo $parseObj->delimiter;
		$segments = explode($parseObj->delimiter, trim($details['hl7_msg']));
		$counter_obx = 1;
		#set all arrays to null
        unset($msh);
        unset($msa);
        unset($pid);
        unset($obr);
        unset($obx);
        unset($nte);
        
        foreach($segments as $segment) {
            $data = explode('|', trim($segment));
            
            if (in_array("MSH", $data)) {
                $msh = $parseObj->segment_msh($data);
            }

            if (in_array("MSA", $data)) {
                $msa = $parseObj->segment_msa($data);
            }

            if (in_array("PID", $data)) {
                $pidsegment = $parseObj->segment_pid($data);
            }

            if (in_array("OBR", $data)) {
                $obr = $parseObj->segment_obr($data);
            }

            if (in_array("OBX", $data)) {
            	$obx[$counter_obx] = $parseObj->segment_obx($data);
                $counter_obx++;
            }

            if (in_array("NTE", $data)) {
                $nte[$counter_nte] = $parseObj->segment_nte($data,$counter_obx);
                $counter_nte++;
            }    
        }
        
        $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr['test']));
        $details['testcode'] = $arr_test[0];
        $details['testname'] = $arr_test[1];
        
        for ($cnt=1; $cnt < $counter_obx; $cnt++){
        	$arr_testservice = explode($parseObj->COMPONENT_SEPARATOR, trim($obx[$cnt]['testservice']));
            $details['testservice'] = $arr_testservice[1];
            $details['testcode'] = $arr_testservice[0];
            $details['url'] = $obx[$cnt]['url'];

        }	

    }	
    
	return $details;	
}

$objAcl = new Acl($_SESSION['sess_temp_userid']);
$permission_RadioResultsPDF = $objAcl->checkPermissionRaw('_a_2_unifiedResults');

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

$pid = $db->GetOne($query);
$data = Array();
if($pid) {
	$query = "SELECT SQL_CALC_FOUND_ROWS r.refno, d.batch_nr, \n".
						"fn_get_radiotest_request_code_all(d.refno) AS services, \n".
						"CONCAT(r.request_date,' ', r.request_time) AS `request_date`, r.is_urgent, \n".
						"d.service_date, d.request_flag, r.encounter_nr, r.pid \n".
						"FROM seg_radio_serv AS r \n".
						"INNER JOIN care_test_request_radio AS d ON d.refno=r.refno \n".
						"WHERE r.status NOT IN ('deleted','hidden','inactive','void') \n".
						"AND d.status NOT IN ('deleted','hidden','inactive','void') \n".
					//	"AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) \n".
						"AND r.encounter_nr=".$db->qstr($encounter_nr)."\n".
						"AND r.pid=".$db->qstr($pid)."\n".
						"AND r.fromdept='RD'\n".
						"GROUP BY r.refno \n".
						"ORDER BY  request_date DESC, request_time DESC \n".
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

			#added by VAN 10-10-2014
			#PACS
			$info_pacs = parseHL7Result($row["batch_nr"], $row['pid']);

			$dicom_available = ($info_pacs['url'])?'Yes':'No';
			

			$data[] = Array(
				'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row["request_date"]." ".$row["request_time"]))),
				'service' => strtoupper($row['services']) ,
				'refno' => $row["refno"],
				'pid' => $row['pid'],
                'permission' => $permission_RadioResultsPDF,
                'url' => $info_pacs['url'],
                'dicom' => $dicom_available,
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