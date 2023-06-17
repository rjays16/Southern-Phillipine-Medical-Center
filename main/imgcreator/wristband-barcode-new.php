<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'/classes/tcpdf/config/lang/eng.php');
require($root_path.'/classes/tcpdf/tcpdf.php');
require_once($root_path.'include/inc_environment_global.php');


 
// create new PDF document
#$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('L', 'mm', array('65','40') , false, 'UTF-8', true);

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);


// add a page
$pdf->AddPage();


$pdf->SetFont('tahoma', 'I', 6);


// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

if(isset($_GET['en'])){
  $encounter_nr = $_GET['en'];
}else{
  $encounter_nr = 0; // set this to your default value if no URL value is present
}

$sql =  "SELECT ce.encounter_nr, ce.pid AS pid,
                fn_get_gender(ce.pid )AS Gender,
                fn_get_person_name (ce.pid) AS NAME_PATIENT
         FROM care_encounter AS ce
         WHERE ce.encounter_nr= '$encounter_nr'";

      if($result=$db->Execute($sql)){
             while ($row = $result->FetchRow()){

              $pid = $row['pid']; 
              $sex = $row['Gender']; 
              $NAME_PATIENT = $row['NAME_PATIENT'];
            }
      }else{
        return FALSE; 
      }

  if ($sex=='f')
    $Gender = 'FEMALE';
  elseif ($sex=='m')
    $Gender = 'MALE';
          
$params = $en;
$style = array(
    'position' => 'center',
    'align' => 'L',
    'stretch' => false,
    'fitwidth' => true,
    'margin-top' => '10',
    'cellfitalign' => '',
    'border' => '0',
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    #'bgcolor' => array(255,255,128),
    'text' => true,
    'label'=> 'Case #: '.$en,
    #'label' => 'Name:'.$NAME_PATIENT .'| Case: '.$en .'| Gender: '.$Gender,
    'font' => 'arial',
    'fontsize' => 7,
    'stretchtext' => 4,
    
);

$style1 = array(
    'position' => 'center',
    'margin-top' => '10',
    'align' => 'L',
    'padding' => '0',
    'stretch' => true,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => '0',
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    #'bgcolor' => array(255,255,128),
    'text' => true,
    'label'=> 'HRN #: '.$pid,
    #'label' => 'Name:'.$NAME_PATIENT .'| Case: '.$en .'| Gender: '.$Gender,
    'font' => 'arial',
    'fontsize' => 7,
    'stretchtext' => 4,
    
);


#$pdf->Cell(0, 0, 'CODE 39 EXTENDED + CHECKSUM', 0, 1);
#$pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));

$pdf->write1DBarcode($params, 'C39', '10', '1', 54, 14, 1, $style);


#$txt = "Name: SAAVEDRA, VANESSA | Gender: F";
// $txt =$Gender;
// $pdf->MultiCell(50, 10, $txt, 0, 'l', false, 1, 20, 23, true, 0, false, true, 0, 'T', false);

// $txt = <<<EOD
//             Name: $NAME_PATIENT | Gender: $Gender
// EOD;


// output the HTML content 
// $pdf->writeHTML($htmlcontent, true, 0, true, 0); 
#$pdf->Write(2.5, $txt, '', 0, 'L', true, 10, false, false, 0);

// }
$html = <<<EOD
<p></p>
<table border="0"  width="90%" style="text-align:center;">
<td align="center" width="1%"></td>
<tr >
</table>
<table >
<td align="left" style="font-family:Arial, Helvetica, sans-serif"  width="100%" >
<br>
<p style="margin-top:30px;">Name: $NAME_PATIENT</p></td>
</tr>

EOD;
$html1 = <<<EOD
<p></p>
<table border="0"  width="90%" style="text-align:center;">
<td align="center" width="1%"></td>
<tr>
</table>
<table>
<td align="left" style="font-family:Arial, Helvetica, sans-serif"  width="100%">
<br>
<p style="margin-top:30px;">Name: $NAME_PATIENT</p></td>


EOD;

$pdf->writeHTMLCell(0, 0, '7', '1', $html, 0, 2, 0, false, 'C', false);

//Close and output PDF document

$pdf->AddPage();
$pdf->write1DBarcode($pid, 'C39', '10', '1', 54, 14, 1, $style1);
$pdf->writeHTMLCell(0, 0, '7', '1', $html1, 0, 2, 0, true, 'C', false);

$pdf->Output('wristbands.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+






