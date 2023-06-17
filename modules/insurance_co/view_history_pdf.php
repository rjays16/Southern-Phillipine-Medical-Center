<?php
	require('./roots.php');

	include_once($root_path."classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	require($root_path.'classes/adodb/adodb.inc.php');
	$patterns = array ('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/',
                   '/^\s*{(\w+)}\s*=/');
	$replace = array ('\3-\4-\1\2', '$\1 =');
	global $db;

	$pdf = new PDF("P",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");

	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;

	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);

	$pdf->SetFont("Times","B","10");
   	$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   	$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
   	$pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   	$pdf->Cell(0,4,'Room Type History',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);


	$sql = "SELECT * FROM care_type_room WHERE nr='$nr'";

	$result=$db->Execute($sql);
	$history=$result->FetchRow();

	$room_name = ucwords(strtoupper($history['name']));
	$room_desc = ucwords(strtoupper($history['description']));
	$room_rate = ucwords(strtoupper($history['room_rate']));

	$pdf->Ln($space*4);
	$pdf->SetFont("Times","","10");
	$pdf->Cell(270,4,'Room Type : '.$room_name,"",0,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(270,4,'Room Description : '.$room_desc,"",0,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(270,4,'Room Rate : '.number_format(trim($room_rate),2,".",""),"",0,'L');
	$pdf->Ln($space*4);
	$pdf->Cell(270,4,'Room Type History',"",0,'L');
	$pdf->Ln($space*4);


	if (!empty($history['history'])){
		$buffer=nl2br($history['history']);
		$buffer=str_replace('<br />','|',$buffer);
	  	$dbhistory = explode('|',$buffer);
		for($i=0; $i<sizeof($dbhistory); $i++){
			$pdf->Cell(0,4,preg_replace($patterns, $replace, $dbhistory[$i]),"",0,'L');
			$pdf->Ln($space*2);
		}
	}

	$pdf->Output();
?>