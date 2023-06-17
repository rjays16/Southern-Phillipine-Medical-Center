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
				$this->SetY(-8);

				 $this->AliasNbPages(); 
				//added by art 01/10/2014
				 //edited by KENTOOT 09-15-2014
				$this->SetFont('Arial','',8);
				$this->Cell(60,8,'Effectivity : March 1, 2017',0,0,'L');
				$this->Cell(80,8,'Revision : 2',0,0,'C');
				$this->Cell(50,8,'Page '.$this->PageNo().' of {nb}',0,0,'R');
				//end art
			#$this->Cell(0,10,$this->encoder,0,0,'C');
		}
	}
?>