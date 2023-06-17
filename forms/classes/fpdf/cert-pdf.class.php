<?php

	define('FPDF_FONTPATH','font/');
	require($root_path."/classes/fpdf/fpdf.php");


	class PDF extends FPDF{

		//Page header

		function Header(){

		}

		//Page footer
		function Footer(){
			 //Go to 0.5 cm from bottom
			 //$this->SetY(-20);
			 

				
			//Page number
				// $this->SetY(-40);
				// $this->setFont($fontStyle,"B",$fontSizeText);
				// $this->Cell(75,6,strtoupper($name_doctor).", MD","",0,"R");
				// $this->setFont($fontStyle,"",$fontSizeText);
				// $this->SetY(-36);
				// $this->Cell(155,6,"Attending Physician","",0,"R");
				// $this->SetY(-31);
				// $this->Cell(168,6,"Lic No. _______________","",0,"R");
			 	$this->SetY(-24);
			 	$this->setFont($fontStyle,"B",$fontSizeText);
			 	$this->Cell(2, 3 , '', "", 0,'');	
			 	$this->Cell(0,1,'NOT VALID ',0,1,'L');
				$this->SetY(-18);
				$this->Cell(2, 3 , '', "", 0,'');
				$this->Cell(0,1,'WITHOUT SPMC SEAL ',0,1,'L');

				$this->SetY(-15);
				$this->SetX(12);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,10,'Encoded by : '.$this->encoder,0,1,'L');
				$this->AliasNbPages(); 
				//added by art 01/10/2014
				$this->SetY(-10);
				$this->SetFont('Arial','B',12);
				$this->Cell(0,2,$this->code, "",1,'L');
				$this->SetFont('Arial','B',12);
				//$this->Cell(60,8,'SPMC-F-HIM-14',0,0,'L');//temp removed by Nick 3-13-2015
				$this->setFont($fontStyle,"",$fontSizeText);
				$this->SetFont('Arial','',8);
				$this->Cell(80,8,'Effectivity : October 1, 2013',0,0,'C');
				$this->Cell(50,8,'Revision : 0',0,0,'R');
				//end art
			#$this->Cell(0,10,$this->encoder,0,0,'C');
		}
	}
?>