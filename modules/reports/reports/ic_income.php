<?php 

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
include('parameters.php');
$objIC = new SegICTransaction();
global $db;
#$db->debug=true;
#hospital details -------------------------------------------------
$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
    $row['hosp_name']    = "DAVAO MEDICAL CENTER";
    $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}
#end hospital details --------------------------------------------

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

#title -----------------------------------------------------------
$title = strtoupper('Income Report');
$title_department = strtoupper('HEALTH SERVICES AND SPECIALTY CLINIC (HSSC)');
$params->put("hosp_country", $row['hosp_country']);
$params->put("hosp_agency",  $row['hosp_agency']);
$params->put("hosp_name",    $row['hosp_name']);
$params->put("hosp_addr1",   $row['hosp_addr1']);
$params->put("title",        $title);
$params->put("frmdate",      $from);
$params->put("todate",       $to);
$params->put("title_department", $title_department);
#end title -------------------------------------------------------

$rs = $objIC->getTrxns($from,$to);

if(is_object($rs)){
	while($row=$rs->FetchRow()){
		$data[$rowIndex] = array(
			                'date'         	=> date('Y-m-d',strtotime($row['trxn_date'])),
				            'fullname'    	=> utf8_decode(trim(strtoupper($row['person_name']))),
				            'enc'    	=> strtoupper($row['encounter_nr']),
				            'lab'    		=> $row['LD'],
				            'lab_charge'    => $row['CLD'],
				            'rad'    		=> $row['RD'],
				            'rad_charge'    => $row['CRD'],
				            'ph'    		=> $row['PH'],
				            'ph_charge'  	=> (!empty($row['CPH']) ? number_format($row['CPH'],2) : ''),
				            'misc'    		=> $row['MISC'],
				            'misc_charge'   => (!empty($row['CMISC']) ? number_format($row['CMISC'],2) : ''),
				            'total'			=> (double)($row['LD']+$row['CLD']+$row['RD']+$row['CRD']+$row['PH']+$row['CPH']+$row['MISC']+$row['CMISC']),
			                 );
		$rowIndex++;
	}
}else{
	$data[0][''] = 'No records';
}
?>