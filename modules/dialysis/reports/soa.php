<?php

require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/class_define_config.php');
global $db;
$RDU_SOA_VERSION_2 = new Define_Config('RDU_SOA_VERSION_2');
define('RDU_SOA_VERSION_2', $RDU_SOA_VERSION_2->get_value());
$get_enc_date = $db->GetOne("SELECT DATE(encounter_date) FROM care_encounter WHERE encounter_nr = ".$db->qstr($_GET['enc']));
// die($get_enc_date);
if(Date($get_enc_date) > Date(RDU_SOA_VERSION_2)){
	require_once('soa2.php');
	die;
}
#edited by KENTOOT 08/31/2014
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);


require_once($root_path."classes/fpdf/fpdf.php");
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class soa extends FPDF{

	function __construct(){
		// $pg_array = array('215.9','330.2');
		$pg_array = 'Letter';
		$this->FPDF('P', 'mm', $pg_array);
	}

	function Header(){
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
		// $this->SetFont("Times", "B", "10");
		// $this->Cell(0, 4, $row['hosp_country'], 0, 1,"C");
		// $this->Cell(0, 4, $row['hosp_agency'], 0, 1 , "C");
		// $this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");
		

		#updated by KENTOOT 08/29/2014
		$this->SetFont("Arial", "B", "10");
		$this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 8, $row['hosp_addr1'], 0, 1, "C");

		$this->SetFont("Arial", "B", "9");
		$this->Cell(0, 10, "DIALYSIS UNIT", 0, 1, "C");
	}

	function Footer(){
		$this->SetY(-15);
		$this->SetFont("Arial", "", "8");
        $this->Cell(60, 8, 'SPMC-F-BIL-12', 0, 0, 'L');
        $this->Cell(20, 8, 'Effectivity : October 1, 2013', 0, 0, 'C');
        $this->Cell(90, 8, 'Rev.0', 0, 0, 'C');
		$this->Cell(20, 8, 'Page '.$this->PageNo().' of {nb}',0,0,'C');
	}

	function showInfo($data,$position){
		global $db;
		$class_desc_sql = "SELECT class_desc FROM seg_subsidy_classification WHERE class_code=".$db->qstr($data['subsidy_class']);
        $class_desc = $db->GetRow($class_desc_sql);

		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 10, "STATEMENT OF ACCOUNT", 0, 1, "C");

		#uncomment by KENTOOT 08/29/2014
		// $this->SetFont("Times", "B", "20");
		// $this->Ln();
		// $this->Cell(0, 0, 'Pre-Bill No: ' . $data['bill_nr'], 0, 0, "L");

		$particular = $data['bill_type'] == 'HD' ?  : "HD SESSION";
		$total = $data['amount'] + $data['hdf_amount']; 
		if ($data['hdf_amount']>0) {
			$particular3 = $data['bill_type'] == 'HDF' ? : "HDF SESSION"; 
		}
		if($data['subsidy_amount']>0){
			$particular2 ='HOSPITAL SUBSIDY ('.strtoupper($class_desc['class_desc']).')';
		}

		// echo "<pre>";
		// print_r($data);
		// exit();
		

		$this->SetFont("Arial", "B", "8");
		$this->Cell(155, 15, 'DATE : ', 0, 0, "R");
		$this->SetFont("Arial", "", "9");
		$this->Cell(0,15, '___________________', 0, 1, "R");

		$this->SetFont("Arial", "B", "8");
		$this->Cell(0, 10, 'AMOUNT', 0, 1, "R");	

		$this->SetFont("Arial", "B", "8");
		$this->Cell(0, 10, 'NAME OF PATIENT    :     '.strtoupper($data['name_last'] . ', ' . $data['name_first'] . ' ' . $data['name_middle']).'' , 0, 0, "L");
		$this->SetFont("Arial", "IB", "10");
		$this->Cell(0, 8, "", 0, 1, "L");

		$this->SetFont("Arial", "B", "8");
		$this->Cell(0, 10, 'PARTICULAR              :     '.$particular, 0, 1, "L");
		$this->Cell(0, -10, $data['amount'], 0, 1, "R");

		if($data['hdf_amount']>0){
		$this->SetFont("Arial", "B", "8");
		$this->Cell(0, 20, '                                     :     '.$particular3, 0, 1, "L");
		$this->Cell(0, -20, $data['hdf_amount'], 0, 1, "R");
		}

		$h = 30;
		if($data['hdf_amount'] == '0.00')
			$h = 20;

		if($data['subsidy_amount']>0){
			$this->SetFont("Arial", "B", "8");
			$this->Cell(0, $h, '                                     :     '.$particular2, 0, 1, "L");
			$this->Cell(0, -$h, "-".$data['subsidy_amount'], 0, 1, "R");
		}

		$this->SetFont("Arial", "", "8");
		$this->Cell(0, $h+3, '_______________', 0, 0, "R");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, $h+4, '_______________', 0, 1, "R");
		
		$this->SetFont("Arial", "B", "8");
		$this->Cell(0, 10, 'TOTAL CHARGES      :     >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>', 0, 1, "L");
		$this->Cell(0, -10, number_format(($total-$data['subsidy_amount']),2), 0, 1, "R");
		
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 15, '_______________', 0, 0, "R");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 16, '_______________', 0, 1, "R");

		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 30, '________________________________________________________________________________________________________________________', 0, 1, "L");	
		// // $this->Cell(100, 0, 'Previous' . '          ' .'Present', 0, 1, "R");
		// // $this->Cell(0, 20, 'Dialyzer Used:'. '                                  ' . '_______'. '      ' .'_______', 0, 1, "L");
		// // $this->Cell(0, 0, 'Machine Number:'. '                             ' . '_________________', 0, 1, "L");
		$this->SetFont("Arial", "B", "12");
		$this->Cell(0, -25, '________________________________________________________________________________', 0, 0, "L");
		// $this->Cell(0, 25, '________________________________________________________________________________', 0, 1, "R");
		// $this->SetFont("Arial", "B", "14");
		// $this->Cell(0, 0, 'SOUTHERN PHILIPPINES MEDICAL CENTER', 0, 1, "C");
		// $this->Cell(0, 20, 'Name of Patient: ' . $data['name_last'] . ', ' . $data['name_first'] . ' ' . $data['name_middle'] , 0, 1, "L");

		// $this->SetFont("Arial", "", "12");
		// $this->Cell(0, 10, 'PARTICULAR:', 0, 1, "L");	
		// $this->Cell(0, 10, 'Supplies:', 0, 1, "L");
		// $this->Cell(0, 10, 'Diasafe:', 0, 1, "L");
		// $this->Cell(0, 10, 'Dialyzer:', 0, 1, "L");
		// $this->Cell(30, 10, 'High Flux', 0, 1, "R");
		// $this->Cell(25, 10, 'Others:', 0, 1, "R");
		// $this->Cell(0, 10, 'Transducer:', 0, 1, "L");
		// $this->Cell(0, 10, 'AVF Needles:', 0, 1, "L");
		// $this->Cell(0, 10, 'Medicines:', 0, 1, "L");
		$this->SetFont("Arial", "B", "8");
		$this->Cell(-363, 10, 'Prepared by: ', 0, 1, "C");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 15, strtoupper($data['personnelName']), 0, 1, "L");
		$this->Cell(0, -14, '_________________________________', 0, 1, "L");
		$this->SetFont("Arial", "BI", "8");
		// $this->Cell(0, 21, $position, 0, 1, "L");
		$this->Cell(0, 21, 'Billing Clerk', 0, 1, "L");

	}
	

}//end class




#added by KENTOOT 09/06/2014
//get HRN
$get_pid = "SELECT 
		cp.pid 
		FROM
		care_personell cp 
		LEFT JOIN care_users AS cu 
		ON cp.nr = cu.personell_nr 
		WHERE cu.name = ".$db->qstr($_SESSION['sess_user_name'])."";

$result = $db->Execute($get_pid);
if(is_object($result)){
              while ($fetch = $result->FetchRow()) {
              	$pid = $fetch['pid'];
              }
}              
//get job position
$get_job_position ="SELECT cp.job_position FROM care_personell cp LEFT JOIN care_person AS c ON cp.pid=c.pid WHERE cp.pid=".$db->qstr($pid)."";

$result2 = $db->Execute($get_job_position);
if(is_object($result2)){
              while ($fetch2 = $result2->FetchRow()) {
              	$position = $fetch2['job_position'];
              }
}

#modified by KENTOOT 09/06/2014
/*$sql = $db->Prepare("SELECT 
					  pr.bill_nr,
					  pe.name_last,
					  pe.name_first,
					  pe.name_middle,
					  pr.amount,
					  re.modify_id,
					  re.request_date,
					  fn_get_personell_name (cu.personell_nr) AS personnelName 
					FROM
					  seg_dialysis_prebill pr 
					  LEFT JOIN care_encounter AS en 
					    ON en.encounter_nr = pr.encounter_nr 
					  LEFT JOIN care_person AS pe 
					    ON pe.pid = en.pid 
					  LEFT JOIN seg_dialysis_request AS re 
					    ON re.encounter_nr = pr.encounter_nr 
					  LEFT JOIN care_users AS cu 
					    ON re.modify_id = cu.login_id 
					WHERE pr.encounter_nr = ? 
					ORDER BY pr.bill_nr ");
*/
#modified by raymond 1/16/2017
$sql = $db->Prepare("SELECT 
					  pr.bill_nr,
					  pr.bill_type,
					  pe.name_last,
					  pe.name_first,
					  pe.name_middle,
					  pr.amount,
					  pr.subsidy_amount,
					  pr.subsidy_class,
					  pr.hdf_amount,
					  re.modify_id,
					  re.request_date,
					  fn_get_personell_name (cu.personell_nr) AS personnelName 
					FROM
					  seg_dialysis_prebill pr 
					  LEFT JOIN care_encounter AS en 
					    ON en.encounter_nr = pr.encounter_nr 
					  LEFT JOIN care_person AS pe 
					    ON pe.pid = en.pid 
					  LEFT JOIN seg_dialysis_request AS re 
					    ON re.encounter_nr = pr.encounter_nr 
					  LEFT JOIN care_users AS cu 
					    ON re.modify_id = cu.login_id 
					WHERE pr.encounter_nr = ? 
					ORDER BY pr.bill_nr ");
$rs = $db->Execute($sql,$_GET['enc']);


$pdf = new soa();
$pdf->AliasNbPages();
$pdf->SetLeftMargin(15);

while($row = $rs->FetchRow()){
	$pdf->AddPage();
	$pdf->showInfo($row,$position);	
	
	// Hospital Logo
	$pdf->Image($root_path.'img/doh.jpg',20,10,25,0);
	$pdf->Image($root_path.'modules/registration_admission/image/dmc_logo.jpg',170,5,25,30);
}
$pdf->output();

?>