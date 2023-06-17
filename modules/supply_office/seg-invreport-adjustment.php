<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

    class RepGen_Inventory_adjustment extends RepGen {
    var $area;
    var $date;
    var $shift_start;
    var $shift_end;
    var $detailed;
    var $adjustr;

    function RepGen_Inventory_adjustment ($area="", $date=FALSE, $adjustreason="") {
        global $db;
        $this->RepGen("INVENTORY",'P','Letter');
        #$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
        $this->ColumnWidth = array(31,32,32,32,20,20,29);
        $this->Alignment = array('C','L','R','R','R','R','L');
        $this->PageOrientation = "P";
        $this->Headers = array(
            'DATE/TIME',
            'AREA',
            'ITEM CODE',
            'NAME',
            'QTY',
            'UNIT',
            'REASON',
        );
        if ($date) $this->date=date("Y-m-d",strtotime($date));
        #else $this->date=date("Y-m-d");
        $this->area=$area;
        $this->adjustr = $adjustreason;
        $this->RowHeight = 6;
        $this->colored=FALSE;
        if ($this->colored)    $this->SetDrawColor(0xDD);
    }
    
    function Header() {
        global $root_path, $db;
        
        if ($this->area) {
            $sql = "SELECT area_name FROM seg_areas WHERE area_code=".$db->qstr($this->area);
            $this->areaName = $db->GetOne($sql);
        }
    
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

        #$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,8,15);
    /*
        $this->SetFont("Arial","I","8");
        $total_w = 0;
        #$this->Cell(19,3);
      $this->Cell($total_w,3,'Republic of the Philippines',$border2,1,'C');
        #$this->Cell(19,3);
      $this->Cell($total_w,3,'DEPARTMENT OF HEALTH',$border2,1,'C');
      $this->Ln(1);
        $this->SetFont("Arial","B","9");
        #$this->Cell(19,4);
      $this->Cell($total_w,4,'DAVAO MEDICAL CENTER',$border2,1,'C');
        $this->SetFont("Arial","B","8");
        #$this->Cell(19,5);*/
    $total_w = 0;
    //this is responsible for the logo in the left side
    $this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);
    
    $this->SetFont("Arial","I","9");
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
    $this->Ln(2);
    $this->SetFont("Arial","B","10");
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_addr1'],$border2,1,'C');
    $this->Ln(4);
    $this->SetFont('Arial','B',12);
    #$this->Cell(17,5);
    
      $this->Cell($total_w,3,'SUPPLY OFFICE',$border2,1,'C');
        $this->Ln(2);
        
      $this->SetFont('Arial','B',10);
    #$this->Cell(17,5);
      $this->Cell($total_w,4,'ADJUSTMENT',$border2,1,'C');
        
        $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->area)
          $this->Cell($total_w,4,$this->areaName,$border2,1,'C');
        else
          $this->Cell($total_w,4,"All areas",$border2,1,'C');

      $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->date) {
          $this->Cell($total_w,4,"As of ".date("F j, Y",strtotime($this->date)),$border2,1,'C');
        }
        else
          $this->Cell($total_w,4,"All dates",$border2,1,'C');
        $this->Ln(4);

        if (!$this->NoHeader) {
            # Print table header
            
            $this->SetTextColor(0);
            $row=5;            
            
            $this->SetFont('Arial','B',11);
            $this->Cell(0,$row,'INVENTORY',1,0,'C',1);
            $this->Ln($row);
            $this->SetFont('Arial','B',9);

            $this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[1],$row,'AREA',1,0,'C',1);
            $this->Cell($this->ColumnWidth[2],$row,'ITEM CODE',1,0,'C',1);
            $this->Cell($this->ColumnWidth[3],$row,'NAME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[4],$row,'QTY',1,0,'C',1);
            $this->Cell($this->ColumnWidth[5],$row,'UNIT',1,0,'C',1);
            $this->Cell($this->ColumnWidth[6],$row,'REASON',1,0,'C',1); 
            
            $this->Ln();
        }
    }
    
    function BeforeCell() {
        $this->FONTSIZE=8;
    }
    
    function BeforeData() {
        if ($this->colored) {
            $this->DrawColor = array(0xDD,0xDD,0xDD);
        }
    }
    
    function BeforeCellRender() {
        $this->FONTSIZE = 10;
        if ($this->colored) {
            if (($this->RENDERPAGEROWNUM%2)>0) 
                $this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
            else
                $this->RENDERCELL->FillColor=array(255,255,255);
        }
    }
    
    function AfterData() {
        global $db;
        
        if (!$this->CM) {
            if (!$this->_count) {
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255);
                $this->SetTextColor(0);
                $this->Cell(0, $this->RowHeight, "No supplies found...", 1, 1, 'L', 1);
            }
            
            $this->SetFont('Arial','B',9);
            if ($this->colored)    $this->SetFillColor(0xEE);
            $this->SetTextColor(0);
            $this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
            $this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total No. of Adjustments : $this->_count"), 1, 0, 'C', 1);
            //$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
            $this->Cell($this->ColumnWidth[3], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);
            /*
            $this->Cell(0,5,'',0,1);
            
            $this->CM = TRUE;
            
            $this->Data = $this->DataCM;
            $this->_total = $this->_totalCM;
            $this->_count = $this->_countCM;

            $this->Report();
            */
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }
    
    function FetchData() {        
        global $db;

        $sql = "SELECT b.adjust_date,
                (select d.area_name from seg_areas as d where d.area_code = b.area_code) as area_name,c.bestellnum,c.artikelname,a.adj_qty,e.unit_name,f.adj_reason_name
                FROM (seg_inventory_adjustment_details as a 
                JOIN seg_inventory_adjustment as b ON a.refno=b.refno) 
                JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum 
                JOIN seg_unit as e ON a.unit_id=e.unit_id
                JOIN seg_inventory_adjustment_reason as f ON a.reason=f.adj_reason_id  ";
        
        $where = array();
                    
        if ($this->date) {
            #$where[]="str_to_date(adjust_date, '%Y-%m-%d') =<'$this->date' ";
            $where[]="str_to_date(b.adjust_date, '%Y-%m-%d') < '$this->date' OR str_to_date(b.adjust_date, '%Y-%m-%d') = '$this->date'";
        }
        
        if ($this->area) {
            $where[]="(b.area_code='$this->area')";
        }
        
        if($this->adjustr){
            $where[]="(a.reason='$this->adjustr')";
        }
        
         if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") ORDER BY b.adjust_date";
        else $sql .= " ORDER BY b.adjust_date"; 
        
        $result=$db->Execute($sql);
                    
                    $this->_total = 0;
                    
                    if ($result) {
                        $this->Data=array();

                        while ($row=$result->FetchRow()) {
                            
                            $this->Data[]=array(
                                date("m/d/y h:ia",strtotime($row['adjust_date'])),
                                $row['area_name'],
                                $row['bestellnum'],
                                $row['artikelname'],                    
                                $row['adj_qty'],
                                $row['unit_name'], 
                                $row['adj_reason_name'],
                            );
                            $this->_total+=$row['amount_due'];
                        }
                        $this->_count = count($this->Data);
                        
                    }
                    else {
                        print_r($sql);
                        print_r($db->ErrorMsg());
                        exit;
                    } 

    }
}
$rep =& new RepGen_Inventory_adjustment($_GET['area'],$_GET['date'],$_GET['adjustreason']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>

