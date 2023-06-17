<?php
#modified by KENTOOT 09/09/2014
#change new template ---------

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

require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
$GLOBAL_CONFIG = array();
$global_config = new GlobalConfig($GLOBAL_CONFIG);

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = $row['hosp_agency'];
		//$row['hosp_name']   = strtoupper($row['hosp_name']);
		$row['hosp_country'] = $row['hosp_country'];
	}
	else {
		 $row['hosp_country'] = "Republic of the Philippines";
		 $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		 $row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MANOLO FORTICH";
		 $row['hosp_addr1']   = "Malaybalay, Bukidnon";
		 $row['mun_name']     = "Malaybalay";
		 $row['prov_name']     = "Bukidnon";
		 $row['region_name']     = "Region X";
	}


//$_GET['encounter_nr'] = 2007500006;

if($_GET['encounter_nr']){
	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
	extract($encInfo);
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


//instantiate fpdf class
$pdf  = new FPDF("P","mm","Letter");
$pdf->AddPage("P");
$pdf->SetLeftMargin(25.5);
$pdf->SetRightMargin(25.5);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x,$y+10);

//Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "", $fontSizeInput-1);
$pdf->Cell(0,4,$row['hosp_country'], $border2,1,'C');
$pdf->SetFont($fontStyle, "B", $fontSizeInput-1);
$pdf->Cell(0,5,$row['hosp_agency'], $border2,1,'C');

#added by KENTOOT 09/09/2014
//City
$pdf->Ln(1);
$pdf->setFont($fontStyle,"", $fontSizeHeader-3);
$pdf->Cell(0,4, 'Center for Health Development Davao Region',$border2, 1, 'C');

//Hospital name- Davao Medical Center
$pdf->Ln(4);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,0, strtoupper($row['hosp_name']),$border2, 1, 'C');

// $pdf->Ln(2);
// $pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
// $pdf->Cell(0,4,'MEDICAL RECORDS SECTION',$border2, 1, 'C');

// //Hospital Address
// $pdf->Ln(2);
// $pdf->setFont($fontStyle,"", $fontSizeInput);
// $pdf->Cell(0,4,$row['hosp_addr1'],$border2, 1, 'C');

//City
$pdf->Ln(2);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,6,'Davao City',$border2, 1, 'C');

$pdf->SetFont('Arial','B',12);
$pdf->setY(50);
$pdf->Cell(0,4,"SPMC-F-MRI-12B", "",0, 'R');

$pdf->Ln(6);
$pdf->setFont($fontStyle,"B", $fontSizeHeader);
$pdf->Cell(0,10,'RECORD OF DEATH',$border2, 1, 'C');

#$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,25,30); commented by art 01/18/2014
//added by art 01/17/2014
$pdf->Image('../image/logo_doh.jpg',25,15,25,25);
$pdf->Image('../image/dmc_logo.jpg',170,15,25,25);
//end art

$pdf->Ln(6);

$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(20,15, 'Name : ', $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$name_patient = stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle)).' '.stripslashes(strtoupper($name_last));
$pdf->Cell(50,15, $name_patient, $border2,0,'L');

$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(20,15, 'Age    : ', $border2,0,'L');
//$pdf->SetFont($fontStyle,"B", $fontSizeText);

// if (!$age)
// 	$age = $age2." years";
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(50,15, $age.' old', $border2,0,'L');

$pdf->SetFont($fontStyle,"", $fontSizeText);
if ($sex=='f')
	$gender = "FEMALE";
elseif ($sex='M')
	$gender = "MALE";

$pdf->Cell(75,15, 'Sex : ', $border2,0,'C');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(-45,15, stripslashes(strtoupper($gender)), $border2,0,'C');



$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(35,15, 'Date of Death :', $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$date_death = date("F d, Y",strtotime($encounter_date));
$pdf->Cell(45,15, $date_death, $border2,0,'L');

$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
// $pdf->Cell(35,3, 'Immediate Cause of Death : Dead on Arrival', $border2,0,'L');
$pdf->Cell(35,15, 'Immediate Cause of Death : ', $border2,0,'L');
$pdf->Cell(70,15, 'Dead on Arrival', $border2,0,'C');
$pdf->Ln(6);
$pdf->Cell(30,15, "", $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText+5);
$pdf->MultiCell(150,15, stripslashes(strtoupper($is_DOA_reason)), 0,'L',0);

$pdf->Ln(30);
//Medical Staff Officer Name
$pdf->setFont($fontStyle,"B",$fontSizeText);
$pdf->Cell(90,6,'',"",0,"");

$newMedrecEffectivity = $global_config->getConfigValue('new_sig_medrec_effec'); // added by carriane 06/17/19
#added by VAN 06-14-08
$sig_info = $pers_obj->get_Signatory('doa', true);
while ($result = $sig_info->FetchRow()){
	$display = 0;

	if(date("Y-m-d H:i:s",strtotime($encounter_date)) < $newMedrecEffectivity){
		if(!$result['is_active'] && $result['is_default']) $display = 1;
	}else{
		if($result['is_active']) $display = 1;
	}

	if($display){
		$name_officer = mb_strtoupper($result['name']);
		$officer_position = $result['signatory_position'];
		$officer_title = $result['signatory_title'];
		$officer_title_af = mb_strtoupper($result['title']);
	}
}

$pdf->Cell(0,6,$name_officer.", ".$officer_title_af,"",0,"C");
$pdf->Ln(5);
$pdf->setFont($fontStyle,"",$fontSizeText);
$pdf->Cell(90,6,'',"",0,"");
$pdf->Cell(75,5,$officer_position,"",1,"C");
$pdf->Cell(90,6,'',"",0,"");
$pdf->Cell(75,5,$officer_title,"",1,"C");

// $pdf->Ln(20);
// $pdf->setFont($fontStyle,"",$fontSizeText);
// $pdf->Cell(20,3, 'Noted :', $border2,0,'L');

// $pdf->Ln(15);
// //Medical Staff Officer Name
// $pdf->setFont($fontStyle,"B",$fontSizeText);
// $sig_info = $pers_obj->get_Signatory('chiefadmin');
// $name_officer2 = mb_strtoupper($sig_info['name']);
// $officer_position2 = $sig_info['signatory_position'];
// $officer_title2 = $sig_info['signatory_title'];
// $officer_title2_af = $sig_info['title'];

// $pdf->Cell(75,6,$name_officer2.", ".$officer_title2_af,"",0,"L");
// $pdf->Ln(5);
// $pdf->setFont($fontStyle,"",$fontSizeText);
// $pdf->Cell(75,6,$officer_position2,"",1,"L");

$pdf->Ln(6);
$pdf->Ln(25);

#print_r($HTTP_SESSION_VARS);

$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(54,7, mb_strtoupper($encoder_name), "",0,'C');
$pdf->Cell(50,7, '', "",0,'L');
// $pdf->Cell(55,7, mb_strtoupper($informant_name), "",0,'C');
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(70,7, 'Name & Signature of Informant', "T",0,'C');
// $pdf->Cell(50,7, '', "",0,'L');
// $pdf->Cell(55,7, 'NAME & SIGNATURE OF INFORMANT', "T",0,'C');

$pdf->Ln(12);
$pdf->SetFont($fontStyle,"", $fontSizeText-2);
$pdf->Cell(65,35, date("m/d/Y")."    ".date("h:i A"), "",0,'C');
$pdf->Cell(50,7, '', "",0,'L');
// $pdf->Cell(55,7, mb_strtoupper($informant_relation), "",0,'C');
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(70,7, 'Relationship to Patient', "T",0,'C');
$pdf->Cell(70,7, '', "",0,'L');
$pdf->Ln(15);
$pdf->Cell(70,7, 'Date & Time', "T",0,'C');

$pdf->Ln(20);
$pdf->SetFont($fontStyle,"I", 8);
// $pdf->Cell(40,4, 'Encoded by : '.$HTTP_SESSION_VARS['sess_user_fullname'], "",0,'C');

//added by art 01/10/2014
$pdf->SetFont('Arial','B',12);
$pdf->setY(50);
// $pdf->Cell(0,4,"SPMC-F-MRI-12B", "",0, 'R');
$pdf->SetAutoPageBreak(true , 0);
$pdf->SetY(-8);
$pdf->AliasNbPages(); 
$pdf->Line($pdf->getX(), $pdf->getY(), 203, $pdf->getY());
$pdf->SetFont('Arial','',8);
$pdf->Cell(60,8,'Effectivity : September 1, 2014',0,0,'L');
$pdf->Cell(80,8,'Revision : 1',0,0,'C');
$pdf->Cell(43,8,'Page '.$pdf->PageNo().' of {nb}',0,0,'R');
//end art
//print pdf
$pdf->Output();

?>