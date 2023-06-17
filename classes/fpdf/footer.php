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
			$this->AliasNbPages(); 
   		$this->SetY(-8);
	    $this->SetFont('Arial','',8);
		$this->Cell(60,8,'Effectivity : October 1, 2013',0,0,'L');
		$this->Cell(80,8,'Revision : 0',0,0,'C');
		$this->Cell(50,8,'Page '.$this->PageNo().' of {nb}',0,0,'R');
		}
		
		#added by VAN 02-12-08
		#http://www.fpdf.de/downloads/addons/49/
		function WordWrap(&$text, $maxwidth){
    		$text = trim($text);
		    if ($text==='')
      	  return 0;
		   $space = $this->GetStringWidth(' ');
		   $lines = explode("\n", $text);
		   $text = '';
		   $count = 0;

		   foreach ($lines as $line){
		     $words = preg_split('/ +/', $line);
      	  $width = 0;

	        foreach ($words as $word){
	           $wordwidth = $this->GetStringWidth($word);
   	         if ($width + $wordwidth <= $maxwidth){
	               $width += $wordwidth + $space;
   	            $text .= $word.' ';
      	      }
         	   else{
	               $width = $wordwidth + $space;
   	            $text = rtrim($text)."\n".$word.' ';
      	         $count++;
         	   }
        	}
 	       $text = rtrim($text)."\n";
   	     $count++;
	    }
   	 $text = rtrim($text);
	    return $count;
		}
		
	} #end of pdf class
	
?>