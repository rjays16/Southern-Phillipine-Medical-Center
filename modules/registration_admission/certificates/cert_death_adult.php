<?php

require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

if (isset($_GET['isinfant']) && $_GET['isinfant']){
	$isinfant = $_GET['isinfant'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

if($pid){
	//if(!($basicInfo = $person_obj->BasicDataArray($pid))){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the page cannot be displayed!</em> ';
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry, the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}

$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));

include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

$data = array('pid' => $pid, 'encounter_nr' => $encounter_nr);

# code for retrieving death certificate information
$deathCertInfo = $obj_deathCert->getDeathCauseRecord($data);

if($deathCertInfo){
	extract($deathCertInfo);
	$death_cause = json_decode($deathCertInfo['death_cause'], true);
}

// var_dump($deathCertInfo);die();
// $border="1";
// $border2="0";
// $space=2;
// $fontSizeInput=10;
// $fontSizeHeading=14;

// $pdf = new FPDF("P","mm","Legal");
// $pdf->AddPage("P");
// $pdf->SetDisplayMode(real,'default');

// $x = $pdf->GetX();
// $y = $pdf->GetY();

// $y=($x*2.8)-9;

// $pdf->SetXY($x,$y);
// $pdf->SetFont("Arial","",$fontSizeInput);

// $pdf->SetY(-0.5);
// $z = $pdf->GetY();

// 	$pdf->SetFont("Arial","",$fontSizeInput-4);
	

// if($isinfant){
// 	// 19a. CAUSES OF DEATH (if the deceased is aged 0 - 7 days)
// 	$pdf->SetXY($x+54, $y+34);
// 	$pdf->MultiCell(133, 2, strtoupper(utf8_decode($death_cause['cause1'])), '', 'J','0');
// 	$pdf->SetXY($x+57, $y+39);
// 	$pdf->MultiCell(130, 2, strtoupper(utf8_decode($death_cause['cause2'])), '', 'J','0');
// 	$pdf->SetXY($x+75, $y+44);
// 	$pdf->MultiCell(112, 2, strtoupper(utf8_decode($death_cause['cause3'])), '', 'J','0');
// 	$pdf->SetXY($x+74, $y+49);
// 	$pdf->MultiCell(112, 2, strtoupper(utf8_decode($death_cause['cause4'])), '', 'J','0');
// 	$pdf->SetXY($x+50, $y+54);
// 	$pdf->MultiCell(135, 2, strtoupper(utf8_decode($death_cause['cause5'])), '', 'J','0');
	
// }else{

// 	// 19b. CAUSES OF DEATH (if the deceased is aged 8 days and over)

// 	// Immediate Cause
// 	$pdf->SetXY($x+55, $y+96);
// 	$pdf->MultiCell(75, 2, strtoupper(utf8_decode($death_cause['cause6'])), '', 'J','0');

// 	if($death_cause['cause6interval'] != ''){
// 		$pdf->SetXY($x+131, $y+95);
// 		$pdf->MultiCell(65, 3, strtoupper(utf8_decode($death_cause['cause6interval'])), '', 'J','0');
// 	}

// 	// Antecedent Cause
// 	$y = $pdf->GetY();
// 	$pdf->SetXY($x+55, $y);
// 	$pdf->MultiCell(75, 3, strtoupper(utf8_decode($death_cause['cause7'])), '', 'J','0');

// 	if($death_cause['cause7interval'] != ""){
// 		$pdf->SetXY($x+131, $y);
// 		$pdf->MultiCell(65, 3, strtoupper(utf8_decode($death_cause['cause7interval'])), '', 'J','0');
// 	}

// 	// Underlying Cause
// 	$y = $pdf->GetY();
// 	$pdf->SetXY($x+55, $y+1);
// 	$pdf->MultiCell(75, 2, strtoupper(utf8_decode($death_cause['cause8'])), '', 'J','0');

// 	if($death_cause['cause7interval'] != ""){
// 		$pdf->SetXY($x+131, $y+1);
// 		$pdf->MultiCell(65, 2, strtoupper(utf8_decode($death_cause['cause8interval'])), '', 'J','0');
// 	}

// 	// Other Significant Condition Contributing to Death
// 	$y = $pdf->GetY();
// 	$pdf->Text($x+81, $y+4, $death_cause['cause9']);
// 	$pdf->SetLineWidth(10);
// 	// $pdf->MultiCell(115, 2, strtoupper(utf8_decode($death_cause['cause9'])), '', 'J','0');
// }
// die(var_dump($death_cause));

// $params = array('name' => $name,
//                 'address' => $address,
//                 'age' =>$age ,
//                 'hrn' => $hrn);
// showReport('progress_notes',$params,$data,'PDF'); 


$params = array(
	'cause1' => utf8_decode($obj_deathCert->cleanInput($death_cause['mainDisease'])),
	'cause2' => utf8_decode($obj_deathCert->cleanInput($death_cause['otherDisease'])),
	'cause3' => utf8_decode($obj_deathCert->cleanInput($death_cause['mainMaternal'])),
	'cause4' => utf8_decode($obj_deathCert->cleanInput($death_cause['otherMaternal'])),
	'cause5' => utf8_decode($obj_deathCert->cleanInput($death_cause['otherRelevant'])),
	'cause6' => utf8_decode($obj_deathCert->cleanInput($death_cause['immediate'])),
	'cause6interval' => utf8_decode($obj_deathCert->cleanInput($death_cause['immediate_int'])),
	'cause7' => utf8_decode($obj_deathCert->cleanInput($death_cause['antecedent'])),
	'cause7interval' => utf8_decode($obj_deathCert->cleanInput($death_cause['antecedent_int'])),
	'cause8' => utf8_decode($obj_deathCert->cleanInput($death_cause['underlying'])),
	'cause8interval' => utf8_decode($obj_deathCert->cleanInput($death_cause['underlying_int'])),
	'cause9' => utf8_decode($obj_deathCert->cleanInput($death_cause['other']))
);

showReport('death_cert_cause',$params,array(0 => ''),'pdf');
// $pdf->Output();   # less than or equal 7 days old at the time of death

?>