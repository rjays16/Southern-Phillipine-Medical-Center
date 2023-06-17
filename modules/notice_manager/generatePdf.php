
<?php
/*added by @Ryan*/

require('fpdf.php');
/*include('db_connection.php');*/
require('functions.php');

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$noticeId = $_GET['id'];

$query1 = $db->Execute("SELECT * FROM seg_notice_tbl WHERE note_id = '$noticeId' ");
    while($row = $query1->FetchRow()){

        $dt_pub   = date('F d, Y',strtotime($row['date_published']));
        $category = strtoupper($row['category']);
        $subject  = $row['subject'];
        $date     = date('F d, Y',strtotime($row['note_date'])). ' ' .date('h:i a',strtotime($row['time_from'])).' - '.date('h:i a',strtotime($row['time_to']));
        $venue    = ucwords($row['venue']);

    }

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    // $this->Image('files/vPHGKiDt_400x400.png',20,8,30);
    // Times bold 15
    $this->SetFont('Times','',11);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->Cell(30,15,'Republic of the Philippines',0,'C');
    //break
    $this->Ln(5);

    $this->Cell(84);
    $this->Cell(20,15,'Department of Health',0,'C');
    $this->Ln(5);

    $this->SetFont('Times','B',11);
    $this->Cell(64);
    $this->Cell(20,15,'Center of Health Development of Davao Region',0,'C');
    $this->Ln(5);

    $this->Cell(62);
    $this->Cell(20,15,'SOUTHERN PHILIPPINES MEDICAL CENTER',0,'C');
    $this->Ln(5);

    $this->Cell(72);
    $this->Cell(20,15,'HOSPITAL INFORMATION SYSTEM',0,'C');
    $this->Ln(5);

    // Line break
    $this->Ln(15);
}


// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    $this->SetFont('Arial','',8);
    $this->Cell(0,10,'Date Generated : '.date('F d, Y h:i A'));
    $this->setXY(100, -18);
    $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
   
}
}

// Instanciation of inherited class
$pdf = new PDF('P','mm',array(215.9,330.2));
$pdf->AliasNbPages();
$pdf->AddPage('P');

$pdf->SetFont('Times','B',11);
$pdf->Cell(0,20,'Date Published : '.$dt_pub,0,1);

$pdf->SetFont('Times','B',12);
$pdf->Cell(0,10,'LIST OF ACKNOWLEDGED NOTICE OF '.$category,0,1,'C');

$pdf->SetFont('Times','B',11);
$pdf->Cell(0,10,'Subject   : '.utf8_decode($subject),0,1,'L');
$pdf->Cell(0,1,'Date & Time: '.$date,0,1,'L');
$pdf->Cell(0,10,'Venue     : '.utf8_decode($venue),0,1,'L');

// $pdf->Cell(0,20,'Acknowledged By :',0,1,'L');




$width_cell = array(60,80,40);
$pdf->SetFont('Times','B',12);

$pdf->SetFillColor(193,229,252); // Background color of header 
// Header starts /// 
$pdf->Cell($width_cell[0],7,'NAME',1,0,C,true); // First header column 
$pdf->Cell($width_cell[1],7,'DEPARTMENT',1,0,C,true); // Second header column
$pdf->Cell($width_cell[0],7,'DATE / TIME',1,1,C,true); // Third header column 

$pdf->SetFont('Times','',10);
$pdf->SetFillColor(235,236,236); // Background color of header 
$fill = false; // to give alternate background fill color to rows 


$query2 = $db->Execute("SELECT * FROM seg_notice_acknledgmnts WHERE notice_id = '$noticeId' ");
    while($rows = $query2->FetchRow()){

        $dt_pub   = date('F d, Y h:i A',strtotime($rows['date_ack']));
        $user     = ucwords(strtolower($rows['sess_user']));
        $dprtmnt  = ucwords($rows['departmnt']);

        $pdf->Cell($width_cell[0],7,$user,1,0,C);
        $pdf->Cell($width_cell[1],7,$dprtmnt,1,0,C);
        $pdf->Cell($width_cell[0],7,$dt_pub,1,1,C);

    }


$pdf->Output();
?>