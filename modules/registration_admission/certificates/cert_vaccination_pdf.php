<?php
//Added by Borj 2014-17-01
//Vaccination Certificate 
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');

include_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

if($_GET['encounter_nr']){
	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
	echo 
	extract($encInfo);
	//echo json_encode($enc_obj->getEncounterInfo($_GET['encounter_nr']));
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}

//set border
$border="1";
$border2="0";
$spacing =2;
// font setup
$fontSizeLabel = 8;
$fontSizeInput = 11;
$fontSizeText = 12;
$fontSizeHeader = 14;
//fontstyle setup
$fontStyle = "Arial";
$fontStyle2 = "Times";

$deathCertInfo = $obj_deathCert->getDeathCertRecord($pid);
if($deathCertInfo){
	extract($deathCertInfo);
}

//instantiate fpdf class
$pdf  = new FPDF("P","mm","Letter");
$pdf->AddPage("P");
$pdf->SetLeftMargin(26);
$pdf->SetRightMargin(25.5);

$pdf->SetFont($fontStyle, "", $fonSizeInput);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x,$y+10);

		if ($row = $objInfo->getAllHospitalInfo()) {
				$row['hosp_agency'] = strtoupper($row['hosp_agency']);
				$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
				
		}

		$pdf->SetFont("Arial","","11");
		$pdf->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		$pdf->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
		$pdf->Cell(0,4,'Center for Health Development Davao Region',$border2,1,'C');
		$pdf->Ln(1);
		$pdf->SetFont("Arial","B","12");
		$pdf->Cell(0,4,$row['hosp_name'],$border2,1,'C');

		$pdf->SetFont("Arial","","11");
		$pdf->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');

$pdf->Ln(6);
$pdf->setFont($fontStyle,"B", $fontSizeHeader);

$pdf->Cell(10,4,'',$border2, 0, '');
$pdf->MultiCell(160, 6,'VACCINATION CERTIFICATE', '0', 'C','0');

$pdf->Image($root_path.'modules/registration_admission/image/Logo_DOH.jpg',30,15,26,27);

$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',160,15,25,30);

$pdf->setFont($fontStyle,"", $fontSizeHeader);

//name
$fnamex=$pdf->getx();
$fnamey=$pdf->gety();
$pdf->setx($fnamex);
$pdf->sety($fnamey);
$pdf->SetFont($fontStyle,"", 12);
$fname = strtoupper(($lnamefname));

$pdf->setFont($fontStyle,"", $fontSizeHeader);
if (strlen($fname)>30){
	$pdf->SetFont($fontStyle,"", 12);
	$pdf->Cell(136,66, $fname,"", 0, 0, '');
	$pdf->setFont($fontStyle,"", $fontSizeHeader);
}elseif(strlen($fname)< 30){
	$pdf->SetFont($fontStyle,"", 10);
	$pdf->Cell(125,66, $fname,"", 0, 0, '');
	$pdf->setFont($fontStyle,"", $fontSizeHeader);
}

//birthdate
$bdatex=$pdf->getx();
$bdatey=$pdf->gety();
$pdf->setx($bdatex);
$pdf->sety($bdatey);
$pdf->SetFont($fontStyle,"", 12);
$bdate1 = strtoupper(($date_birth));
$bdate = date("F d, Y", strtotime($bdate1));
$pdf->Cell(67,78, $bdate,"", 0, 2, 'L');
$pdf->setFont($fontStyle,"", $fontSizeHeader);

//details
$detailsx=$pdf->getx();
$detailsy=$pdf->gety();
$pdf->setx($detailsx);
$pdf->sety($detailsy);
$pdf->SetFont($fontStyle,"", 12);
$details = strtoupper(($vac_details));

$pdf->setFont($fontStyle,"", $fontSizeHeader);
if (strlen($details)< 24){
 	$pdf->SetFont($fontStyle,"", 12);
 	$pdf->Cell(159,78, $details,"", 0, 0, 'L');
 	$pdf->setFont($fontStyle,"", $fontSizeHeader);
}elseif(strlen($details)> 24){
	$pdf->SetFont($fontStyle,"", 10);
 	$pdf->Cell(159,78, $details,"", 0, 0, 'L');
 	$pdf->setFont($fontStyle,"", $fontSizeHeader);
}elseif(strlen($details)> 30){
	$pdf->SetFont($fontStyle,"", 8);
 	$pdf->Cell(159,78, $details,"", 0, 0, 'L');
 	$pdf->setFont($fontStyle,"", $fontSizeHeader);
}

//date
$datex=$pdf->getx();
$datey=$pdf->gety();
$pdf->setx($datex);
$pdf->sety($datey);
$pdf->SetFont($fontStyle,"", 12);
$date1 = strtoupper(($vac_date));
$date = date("F d, Y", strtotime($date1));
$pdf->Cell(44,90, $date,"", 0, 0, 'L');
$pdf->setFont($fontStyle,"", $fontSizeHeader);

//day
$dayx=$pdf->getx();
$dayy=$pdf->gety();
$pdf->setx($dayx);
$pdf->sety($dayy);
$pdf->SetFont($fontStyle,"", 12);
$today = date('jS');
$pdf->Cell(53,126, $today,"", 0, 0, 'L');
$pdf->setFont($fontStyle,"", $fontSizeHeader);

//month
$monthx=$pdf->getx();
$monthy=$pdf->gety();
$pdf->setx($monthx);
$pdf->sety($monthy);
$pdf->SetFont($fontStyle,"", 12);
$month = date("F, Y");
$pdf->Cell(89,126, $month,"", 0, 0, 'R');
$pdf->setFont($fontStyle,"", $fontSizeHeader);

$text = "TO WHOM IT MAY CONCERN                                                                                                         ";

$text3 = "         This is to certify that ____________________________________ was born on, _______________ .In this hospital and was given ___________________________. ".
		 "on _______________ .        

		                                                                                                                                 
		                    ".
		 " Date this ____ of _____________ in Davao City, Philippines ";


	$sig_info = $pers_obj->get_Signatory('medcert');
	$name_officer = mb_strtoupper($sig_info['name']);
	$officer_position = $sig_info['signatory_position'];
	$officer_title = $sig_info['signatory_title'];

$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Ln(15);
$pdf->MultiCell(0,6, $text, "",1,'J');
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Ln(3);
$pdf->MultiCell(0,6, $text3, "",1,'J');
$pdf->Ln(50);

$head_namex=$pdf->getx();
$head_namey=$pdf->gety();

$line_over=$pdf->getx();
$line_over=$pdf->gety();
$pdf->setx($line_overx);
$pdf->setx($line_overy);
$pdf->Cell(0,0, '____________________________',"", "B", 0, 0, 'R');



$pdf->Cell(0,10,$officer_title,"", 0, 0, 'R');

$pdf->Ln(30);
$pdf->Cell(0,10, 'NOT VALID', 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(0,10, 'W/O SPMC SEAL', 0, 0, 'L');

$pdf->Ln(15);
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(37,10, 'SPMC-F-HIMD-15', "", 0, 'L');
$pdf->SetFont($fontStyle,"", $fontSizeText);

$pdf->Ln(15);
$pdf->SetFont($fontStyle,"", 7);
$pdf->Cell(20,-10, 'Effective: October 1,2013', "", 0, 'L');

$pdf->setx($line_overx+105);
$pdf->SetFont($fontStyle,"", 7);
$pdf->Cell(50,-10, 'Revision: 0', "", 0, 'L');

$pdf->setx($line_overx-40);
$pdf->SetFont($fontStyle,"", 7);
$pdf->Cell(70,-10, 'Page 1 of 1', "", 0, 'L');


$pdf->setx($head_namex);
$pdf->sety($head_namey-7);
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(132,13,$name_officer,"", 0, 0, 'L');
$pdf->Cell(30,13, ', MPA. MBA-HA', "", 0, 'R');



$pdf->Output();

?>