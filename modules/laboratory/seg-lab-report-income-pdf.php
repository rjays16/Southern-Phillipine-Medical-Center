<?php
  require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_ResultOfTreatment_Discharge extends RepGen{
var $colored = TRUE;
var $from, $to;                        
                           
function RepGen_ResultOfTreatment_Discharge ($from, $to) {
    global $db;
    $this->RepGen("INCOME REPORT: IN-PATIENT");
    $this->ColumnWidth = array(20,20,20,20,20,20,20,20);
    $this->RowHeight = 5;
    $this->TextHeight = 5;
    $this->TextPadding = 0.2;
    $this->Alignment = array('C','C','C','C','C','C','C','C');
    $this->PageOrientation = "P";
    $this->NoWrap = FALSE;
    
    //if ($from) $this->from=$from;
    //if ($to) $this->to=$to;
    //if ($code) $this->code=$code;
        if ($from) $this->from=date("Y-m-d",strtotime($from));
    if ($to) $this->to=date("Y-m-d",strtotime($to));    
    
    $this->useMultiCell = TRUE;
    $this->SetFillColor(0xFF);
    if ($this->colored)  $this->SetDrawColor(0xDD);
  }
  

  function Header() {
    global $root_path, $db;
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
    $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
    $this->SetFont("Arial","I","9");
    $total_w = 165;
    $this->Cell(50,4);
      #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
    $this->Cell(50,4);
      #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
      $this->Ln(2);
    $this->SetFont("Arial","B","10");
    $this->Cell(50,4);
      #$this->Cell($total_w,4,'DAVAO MEDICAL CENTER',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    $this->Cell(50,4);
      #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
      $this->Ln(4);
    $this->SetFont('Arial','B',12);
    $this->Cell(50,5);
    
  $this->Cell($total_w,4,'INCOME REPORT: IN-PATIENT',$border2,1,'C');
   $this->SetFont('Arial','B',9);
    $this->Cell(50,5);
    if ($this->fromdate==$this->todate)
      $text = "For ".date("F j, Y",strtotime($this->fromdate));
    else
        #$text = "Full History";
      $text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
      
    $this->Cell($total_w,4,$text,$border2,1,'C');
    $this->Ln(5);

    # Print table header    
    $this->SetFont('Arial','B',8);
    if ($this->colored) $this->SetFillColor(0xED);
    $this->SetTextColor(0);
    $row=6;
    #$this->Cell(0,4,'',1,1,'C');
    $this->Cell($this->ColumnWidth[0],$row,'Date',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'Shift',1,0,'C',1);
    $this->Cell($this->ColumnWidth[2],$row,'Amount Billed',1,0,'C',1);
    $this->Cell($this->ColumnWidth[3],$row,'Amount Paid',1,0,'C',1);
    $this->Cell($this->ColumnWidth[4],$row,'Charity',1,0,'C',1);
    $this->Cell($this->ColumnWidth[5],$row,'LINGAP',1,0,'C',1);
    $this->Cell($this->ColumnWidth[6],$row,'SS Discounts',1,0,'C',1);
    $this->Cell($this->ColumnWidth[7],$row,'OTHER',1,0,'C',1);
    
    $this->Ln();
  }

function Footer()
  {
    $this->SetY(-23);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
  }
  
  function BeforeRow() {
    $this->FONTSIZE = 8;
    if ($this->colored) {
      if (($this->ROWNUM%2)>0) 
        $this->FILLCOLOR=array(0xee, 0xef, 0xf4);
      else
        $this->FILLCOLOR=array(255,255,255);
      $this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
    }
  }
  
  function BeforeData() {
    if ($this->colored) {
      $this->DrawColor = array(0xDD,0xDD,0xDD);
    }
  }
  
  function BeforeCellRender() {
    $this->FONTSIZE = 8;
    if ($this->colored) {
      if (($this->RENDERPAGEROWNUM%2)>0) 
        $this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
      else
        $this->RENDERCELL->FillColor=array(255,255,255);
    }
  }
  
  function AfterData() {
    global $db;
    
    if (!$this->_count) {
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(255);
      $this->SetTextColor(0);
      $this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
    }
    
    $cols = array();
  }    
function FetchData(){
  global $db;
  if ($this->from) {
      $where[]="DATE(e.discharge_date) BETWEEN '$this->from' AND '$this->to'";
    }

    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";
      
$sql = "";

$result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
      while ($row=$result->FetchRow()) {
        $this->Data[]=array(
          $row['Type_Of_Service'],
          $row['Discharge'],
          $row['Transfered'],
          $row['HAMA'],
          $row['Absconded'],
          $row['Total_disposition'],
          $row['Recovered'],
          $row['Unimproved'],
          $row['Improved'],
          $row['Total_results'],
          $row['<48'],
          $row['>=48'],
          $row['Total_Death']);
      }
    }
    else {
      print_r($sql);
      print_r($db->ErrorMsg());
      exit;
      # Error    
    }
  }
}

$rep = new RepGen_ResultOfTreatment_Discharge($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
