<?php
#edited by KENTOOT 08/31/2014
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require_once($root_path."classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/class_define_config.php');

$RDU_CHARGE_XLO = new Define_Config('RDU_CHARGE_XLO');
$RDU_CHARGE_Meds = new Define_Config('RDU_CHARGE_Meds');
$RDU_CHARGE_OR = new Define_Config('RDU_CHARGE_OR');
$RDU_CHARGE_PF = new Define_Config('RDU_CHARGE_PF');
$RDU_PHIC = new Define_Config('RDU_PHIC');
define('RDU_CHARGE_XLO', $RDU_CHARGE_XLO->get_value());
define('RDU_CHARGE_Meds', $RDU_CHARGE_Meds->get_value());
define('RDU_CHARGE_OR', $RDU_CHARGE_OR->get_value());
define('RDU_CHARGE_PF', $RDU_CHARGE_PF->get_value());
define('RDU_PHIC', $RDU_PHIC->get_value());

define('PERCENTAGE_20', 0.2);
define('PERCENTAGE_80', 0.8);
$TOTAL_HCI_CHARGES=0;
$TOTAL_HCI_DISCOUNT=0;
$TOTAL_HCI_PHIC=0;
$TOTAL_HCI_EXCESS=0;
$TOTAL_PF_CHARGES=0;
$TOTAL_PF_DISCOUNT=0;
$TOTAL_PF_PHIC=0;
$TOTAL_PF_EXCESS=0;
$TOTAL_AMOUNT_DUE=0;
$encobj = new Encounter($_GET['enc']);
$isPHIC = $encobj->isPHIC($_GET['enc']);
$patient_data = $db->GetRow("SELECT ce.pid,cp.name_last,cp.name_first,cp.name_middle,`fn_get_complete_address`(cp.pid) as address FROM care_encounter ce LEFT JOIN care_person cp on ce.pid=cp.pid WHERE encounter_nr=".$db->qstr($_GET['enc']));
define('PATIENT_PID', $patient_data['pid']);
define('PATIENT_ADDRESS', $patient_data['address']);
define('PATIENT_NAME', strtoupper($patient_data['name_last'] . ', ' . $patient_data['name_first'] . ' ' . $patient_data['name_middle']));

class soa extends FPDF{

	function __construct(){
		// $pg_array = array('215.9','330.2');
		$pg_array = 'Letter';
		$this->FPDF('P', 'mm', $pg_array);
		$patient_type="";
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
		$this->SetFont("Arial", "", "10");
		$this->Cell(0, 6, $row['hosp_country'], 0, 1, "C");
		$this->SetFont("Arial", "B", "10");
		$this->Cell(0, 10, $row['hosp_agency'], 0, 1, "C");
		$this->SetFont("Arial", "B", "10");
		$this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 8, $row['hosp_addr1'], 0, 1, "C");
		$this->SetFont("Arial", "B", "9");
		$this->Cell(0, 8, "Hemodialysis Procedure OUT - PATIENT", 0, 1, "C");
	}

	function Footer(){

		$this->SetY(-35);
		$this->Cell(0,0, '___________________________________________________________________________________________________________________', 0, 1, "L");
		$this->SetFont("Arial", "", "7");
		$this->SetY(-30);
		$this->Cell(0,0, 'PATIENT CLEARANCE', 0, 1, "C");
		$this->Ln(6);
		$this->SetFont("Arial", "B", "8");
		$this->setX(40);
		$this->Cell(0, 0, 'HRN #:', 0, 0, "L");
		$this->setX(55);
		$this->Cell(0, 0, PATIENT_PID, 0, 0, "L");
		$this->setX(125);
		$this->Cell(0, 0, 'PATIENT:', 0, 0, "L");
		$this->setX(140);
		$this->Cell(0, 0, PATIENT_NAME, 0, 0, "L");
		$this->SetFont("Arial", "", "8");
		$this->Ln(6);
		$this->setX(15);
		$this->Cell(0, 0, 'NURSE ON DUTY:', 0, 0, "L");
		$this->setX(40);
		$this->Cell(0, 0, "_________________________________", 0, 0, "L");
		$this->setX(125);
		$this->Cell(0, 0, 'BILLING:', 0, 0, "L");
		$this->setX(140);
		$this->Cell(0, 0, "_________________________________", 0, 0, "L");
		$this->Ln(4);
		$this->SetFont("Arial", "", "8");
        $this->Cell(60, 8, 'SPMC-F-BIL-12', 0, 0, 'L');
        $this->Cell(20, 8, 'Effectivity : November 15, 2018', 0, 0, 'C');
        $this->Cell(90, 8, 'Rev.1', 0, 0, 'C');
		$this->Cell(20, 8, 'Page '.$this->PageNo().' of {nb}',0,0,'C');
	}

	function showInfo($data,$position){
		global $db;
		$class_desc_sql = "SELECT class_desc FROM seg_subsidy_classification WHERE class_code=".$db->qstr($data['subsidy_class']);
        $class_desc = $db->GetRow($class_desc_sql);

		$this->SetFont("Arial", "", "8");
		// $this->Cell(0, 10, "STATEMENT OF ACCOUNT", 0, 1, "C");

		#uncomment by KENTOOT 08/29/2014
		// $this->SetFont("Times", "B", "20");
		// $this->Ln();
		// $this->Cell(0, 0, 'Pre-Bill No: ' . $data['bill_nr'], 0, 0, "L");

		$particular = $data['bill_type'] == 'HD' ?  : "HD SESSION";
		$TOTAL_AMOUNT_DUE = $data['amount'] + ($data['hdf_amount']*PERCENTAGE_80);
		$total = $data['amount'] + $data['hdf_amount']; 
		if ($data['hdf_amount']>0) {
			$particular3 = $data['bill_type'] == 'HDF' ? : "HDF SESSION"; 
		}
		if($data['subsidy_amount']>0){
			$particular2 ='HOSPITAL SUBSIDY ('.strtoupper($class_desc['class_desc']).')';
		}
		if($data['bill_type'] == 'PH') $isPHIC=true;
		else $isPHIC=false;
		// echo "<pre>";
		// print_r($data);
		// exit();
		$this->SetFont("Arial", "", "8");
		$this->setX(15);
		$this->Cell(0, 8, 'HRN #                         :', 0, 0, "L");
		$this->SetFont("Arial", "B", "8");
		$this->setX(50);
		$this->Cell(0, 8, PATIENT_PID, 0, 0, "L");
		$this->SetFont("Arial", "", "8");
		$this->setX(160);
		$this->Cell(0, 8, 'DATE : ', 0, 0, "L");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0,8, '___________________', 0, 1, "R");
		$this->Cell(0, 8, 'Name                          :', 0, 0, "L");
		$this->SetFont("Arial", "B", "8");
		$this->setX(50);
		$this->Cell(0, 8, strtoupper($data['name_last'] . ', ' . $data['name_first'] . ' ' . $data['name_middle']), 0, 1, "L");
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 8, 'Address                       :', 0, 0, "L");
		$this->SetFont("Arial", "B", "8");
		$this->setX(50);
		$this->Cell(0, 8, strtoupper(PATIENT_ADDRESS), 0, 1, "L");
		$this->setX(15);
		$this->Cell(0,-5, '____________________________________________________________________   ______________   ___________________   ______________', 0, 1, "L");
		$this->SetFont("Arial", "", "8");
		$this->setX(30);
		$this->Cell(0, 15, 'Particulars', 0, 0, "L"); 
		$this->setX(100);
		$this->Cell(0, 15, 'Actual Charges', 0, 0, "L"); 
		$this->setX(130);
		$this->Cell(0, 15, 'Discount', 0, 0, "L"); 
		$this->setX(153);
		$this->Cell(0, 15, 'Insurance/PHIC', 0, 0, "L"); 
		$this->setX(187);
		$this->Cell(0, 15, 'Excess', 0, 1, "L"); 
		$this->Cell(0,-12, '____________________________________________________________________   ______________   ___________________   ______________', 0, 1, "L");
		$this->Ln(12);
		$this->setX(15);
		$this->Cell(0, 0, 'Accommodation 4 Hours', 0, 1, "L"); 
		$this->Ln(7);
		$this->setX(15);
		$this->Cell(0, 0, 'X-Ray, Lab, & Others', 0, 0, "L"); 
		$this->setX(100);
		$this->Cell(0, 0, number_format(RDU_CHARGE_XLO, 2), 0, 1, "L"); 
		$this->Ln(7);
		$this->setX(15);
		$this->Cell(0, 0, 'Drugs & Medicines', 0, 0, "L"); 
		$this->setX(100);
		$this->Cell(0, 0, number_format(RDU_CHARGE_Meds, 2), 0, 1, "L"); 
		$this->Ln(7);
		$this->setX(15);
		$this->Cell(0, 0, 'Operating Room', 0, 0, "L"); 
		$this->setX(100);
		$this->Cell(0, 0, number_format(RDU_CHARGE_OR, 2), 0, 1, "L"); 
		$this->Ln(7);
		$this->setX(15);
		$this->Cell(0, 0, 'Miscellaneous', 0, 0, "L"); 
		$this->setX(100);
		$this->Cell(0, 0, number_format($data['hdf_amount'], 2), 0, 1, "L"); 
		$this->Cell(0,3, '                                                                                                        ________________   ______________   ___________________   ______________', 0, 1, "L");
		$this->Ln(4);
		$this->setX(83);
		$this->Cell(0, 0, 'Sub-Total', 0, 0, "L"); 
		$this->setX(100);
		$this->Cell(0, 0, number_format($TOTAL_HCI_CHARGES=(RDU_CHARGE_XLO+RDU_CHARGE_Meds+RDU_CHARGE_OR+$data['hdf_amount']), 2), 0, 0, "L"); 
		$this->setX(130);
		$this->Cell(0, 0, number_format($TOTAL_HCI_DISCOUNT=((RDU_CHARGE_XLO+RDU_CHARGE_Meds+RDU_CHARGE_OR+$data['hdf_amount'])*PERCENTAGE_20), 2), 0, 0, "L"); 
		$this->setX(157);
		$this->Cell(0, 0, $isPHIC?number_format($TOTAL_HCI_PHIC=$isPHIC?RDU_PHIC:0, 2):"", 0, 0, "L"); 
		$this->setX(187);
		$this->Cell(0, 0, number_format($TOTAL_HCI_EXCESS=($TOTAL_HCI_CHARGES-$TOTAL_HCI_DISCOUNT-$TOTAL_HCI_PHIC), 2), 0, 0, "L"); 
		$this->Ln(7);
		$this->setX(15);
		$this->Cell(0, 0, 'ADD:', 0, 1, "L");
		$this->Ln(7);
		$this->setX(30);
		$this->Cell(0, 0, 'Professional Fees', 0, 0, "L");
		$this->setX(100);
		$this->Cell(0, 0, number_format($TOTAL_PF_CHARGES=RDU_CHARGE_PF, 2), 0, 0, "L"); 
		$this->setX(130);
		$this->Cell(0, 0, number_format($TOTAL_PF_DISCOUNT=$isPHIC?($TOTAL_PF_CHARGES*PERCENTAGE_20):$TOTAL_PF_CHARGES, 2), 0, 0, "L"); 
		$this->setX(157);
		$this->Cell(0, 0, $isPHIC?number_format($TOTAL_PF_PHIC=$isPHIC?($TOTAL_PF_CHARGES*PERCENTAGE_80):0, 2):"", 0, 0, "L"); 
		$this->Ln(7);
		$this->setX(83);
		$this->Cell(0, 0, '', 0, 1, "L"); 
		$this->Cell(0,3, '                                                                                                        ________________   ______________   ___________________   ______________', 0, 1, "L");
		$this->Ln(4);
		$this->setX(15);
		$this->Cell(0, 0, 'TOTAL:', 0, 1, "L");
		$this->setX(100);
		$this->Cell(0, 0, number_format($TOTAL_HCI_CHARGES+$TOTAL_PF_CHARGES, 2), 0, 0, "L"); 
		$this->setX(130);
		$this->Cell(0, 0, number_format($TOTAL_HCI_DISCOUNT+$TOTAL_PF_DISCOUNT, 2), 0, 0, "L"); 
		$this->setX(157);
		$this->Cell(0, 0, $isPHIC?number_format($TOTAL_HCI_PHIC+$TOTAL_PF_PHIC, 2):"", 0, 0, "L"); 
		$this->setX(187);
		$this->Cell(0, 0, number_format($TOTAL_HCI_EXCESS+$TOTAL_PF_EXCESS, 2), 0, 0, "L"); 
		$this->Ln(7);
		$this->setX(15);
		$this->Cell(0, 0, 'LESS:', 0, 1, "L");
		$this->Ln(7);
		$this->setX(30);
		$this->Cell(0, 0, 'HOSPITAL SUBSIDY', 0, 0, "L");
		$this->setX(187);
		$this->Cell(0, 0, number_format((($TOTAL_HCI_EXCESS+$TOTAL_PF_EXCESS)-$TOTAL_AMOUNT_DUE), 2), 0, 0, "L");
		$this->Ln(0);
		$this->setX(180);
		$this->Cell(0,3, '______________', 0, 1, "L");
		$this->Ln(4);
		$this->SetFont("Arial", "B", "8");
		$this->setX(30);
		$this->Cell(0, 0, 'AMOUNT DUE', 0, 0, "L");
		$this->setX(187);
		$this->Cell(0, 0, number_format($TOTAL_AMOUNT_DUE, 2), 0, 0, "L");


		$this->Ln(20);
		$this->setX(100);
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 0, 'Prepared by: ', 0, 1, "L");
		$this->Ln(10);
		$this->setX(100);
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 0, strtoupper($data['personnelName']), 0, 1, "C");
		$this->Ln(1);
		$this->setX(100);
		$this->Cell(0, 0, '_________________________________', 0, 1, "C");
		$this->Ln(6);
		$this->setX(100);
		$this->SetFont("Arial", "", "8");
		$this->Cell(0, 0, 'Billing Clerk', 0, 1, "C");

	}
	

}//end class


global $db;

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
					  en.pid,
					  fn_get_personell_name (cu.personell_nr) AS personnelName ,
					  `fn_get_complete_address`(pe.pid) as address
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