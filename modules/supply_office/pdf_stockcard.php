<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
include_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path.'include/care_api_classes/class_area.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_StockCard extends DMCRepGen {

    var $from_date;
    var $to_date;    
    var $left;
    var $right;
    var $top;
    var $item;
    var $area;
    
    function RepGen_StockCard($item=FALSE, $area=FALSE) {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "L", "Letter", $db, TRUE);
        
        //$this->Caption = "Radiology Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 50;
        $this->ColumnWidth = array(19,16,27,14,22,26,14,22,26,14,22,25);
        $this->Columns = 12;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('L','L','C','R','R','R','R','R','R','R','R','R');
        $this->PageOrientation = "L";
        
        $this->date = date("Y-m-d");
        
        $this->item = $item;
        $this->area = $area;

        $this->NoWrap = FALSE;
        $this->colored = TRUE;
        
    }
    
    function Header() {
        
        $area_obj = new SegArea();
        
        //print labels
        $this->SetFont('Arial','B',12);
        $this->SetXY(50,16);
        $this->Cell(120,16,'STOCK CARD',0,0,'C');
        $this->SetFont('Arial','',9);
        $this->SetXY(199,13);
        $this->Cell(120,16,'STOCK NO.',0,0,'L');
        $this->SetXY(13,22);
        $this->Cell(120,16,'AGENCY / OFFICE',0,0,'L');
        $this->SetXY(110,22);
        $this->Cell(120,16,'LOCATION',0,0,'L');
        $this->SetXY(180,22);
        $this->Cell(50,16,'STOCK MOVEMENT',0,0,'L');
        $this->SetXY(225,22);
        $this->Cell(50,16,'REORDER POINT',0,0,'L');
        $this->SetXY(13,31);
        $this->Cell(50,16,'ITEM',0,0,'L');
        $this->SetXY(75,31);
        $this->Cell(50,16,'DESCRIPTION',0,0,'L');
        $this->SetXY(199,31);
        $this->Cell(50,16,'UNIT',0,0,'L');
        $this->SetXY(13,40);
        $this->Cell(50,16,'DATE',0,0,'L');  
        $this->SetXY(34,40);
        $this->Cell(50,16,'REFNO',0,0,'L');
        $this->SetFont('Arial','',8.5);
        $this->SetXY(46.5,46.5);
        $this->MultiCell(30, 3,'FROM WHOM RECEIVED OR TO WHOM ISSUED', '', 'C','');
        $this->SetFont('Arial','',9);
        $this->SetXY(75,40);
        $this->Cell(60,16,'RECEIVED',0,0,'C');
        $this->SetXY(137,40);
        $this->Cell(60,16,'ISSUED',0,0,'C');
        $this->SetXY(199,40);
        $this->Cell(60,16,'BALANCE',0,0,'C');
        $this->SetXY(75,45);
        $this->Cell(15,16,'QTY',0,0,'C');
        $this->SetXY(90,45);
        $this->Cell(20,16,'UNIT COST',0,0,'C');
        $this->SetXY(110,45);
        $this->Cell(28,16,'TOTAL COST',0,0,'C');
        
        $this->SetXY(137,45);
        $this->Cell(15,16,'QTY',0,0,'C');
        $this->SetXY(152,45);
        $this->Cell(20,16,'UNIT COST',0,0,'C');
        $this->SetXY(172,45);
        $this->Cell(28,16,'TOTAL COST',0,0,'C');
        
        $this->SetXY(199,45);
        $this->Cell(15,16,'QTY',0,0,'C');
        $this->SetXY(214,45);
        $this->Cell(20,16,'UNIT COST',0,0,'C');
        $this->SetXY(234,45);
        $this->Cell(28,16,'TOTAL COST',0,0,'C');   
        
        //insert information
        $objInfo = new Hospital_Admin();
    
        if ($row = $objInfo->getAllHospitalInfo()) {      
          $row['hosp_agency'] = strtoupper($row['hosp_agency']);
          $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
        $this->SetFont('Arial','B',8);
        $this->SetXY(13,32);
        $this->MultiCell(124, 3,$row['hosp_name'], '', 'L','');
        
        $this->SetXY(110,32);
        $this->MultiCell(124, 3,$area_obj->getAreaName($this->area), '', 'L','');
        
        $prod_obj = new SegPharmaProduct();
        $prodinfo = $prod_obj->getProductInfo($this->item);
        $prodextend = $prod_obj->getExtendedProductInfo($this->item);
        
        $this->SetXY(199,17);
        $this->Cell(120,16,$this->item,0,0,'L');
        
        $this->SetXY(225,32);
        $this->Cell(120,4,$prodextend['min_qty'],0,0,'L');
        
        $this->SetXY(13,41);
        $this->Cell(120,4,$prodinfo['artikelname'],0,0,'L');
        
        $unit_obj = new Unit();
        $this->SetXY(199,41);
        $this->Cell(120,4,$unit_obj->getUnitName($prodextend['pc_unit_id']),0,0,'L');      
        
        $this->SetFont('Arial','',9);
        //draw lines
        $this->left = 13;
        $this->right = 260;
        $this->SetDrawColor(0,0,0);
        $this->Line($this->left,19,$this->right,19);
        $this->Line($this->left,19,$this->left,56);
        $this->Line($this->right,19,$this->right,56); 
        $this->Line($this->left,28,$this->right,28);
        $this->Line($this->left,37,$this->right,37);
        $this->Line($this->left,46,$this->right,46);
        $this->Line($this->left,56,$this->right,56);
        $this->Line(75,51,$this->right,51);
        
        $this->Line(199,19,199,28);
        $this->Line(225,28,225,37);
        $this->Line(180,28,180,37); 
        $this->Line(110,28,110,37);
        $this->Line(75,37,75,56);
        $this->Line(199,37,199,56);
        $this->Line(32,46,32,56);
        $this->Line(48,46,48,56);
        $this->Line(137,46,137,56);
        $this->Line(89,51,89,56);
        $this->Line(111,51,111,56);
        $this->Line(151,51,151,56);
        $this->Line(173,51,173,56);
        $this->Line(213,51,213,56);
        $this->Line(235,51,235,56);
        
        $this->top = 50;
        $this->SetXY(13,56);
        $this->SetFont('Arial','',9);
        
    }
    
    function BeforeData() {
        $this->FONTSIZE = 8.5;
        if ($this->colored) {
            #$this->DrawColor = array(0xDD,0xDD,0xDD);
            $this->DrawColor = array(0,0,0);
        }
    }                                                            

    function FetchData() {
        global $db;
        
        $unit_obj = new Unit();
        $prod_obj = new SegPharmaProduct();
        $inv_obj = new Inventory();
        
        $sql = "SELECT b.issue_date,b.refno,
                (select d.area_name from seg_areas as d where d.area_code = b.src_area_code) as src_area,a.item_qty as rec_qty,a.unit_id as rec_unit,'' as iss_qty, '' as iss_unit,
                (select f.area_name from seg_areas as f where f.area_code = b.area_code) as dest_area, a.avg_cost as unit_cost
                FROM (seg_issuance_details as a 
                JOIN seg_issuance as b ON a.refno=b.refno) 
                JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum ";
        
        $where = array();
                    
        if ($this->date) {
            $where[]="str_to_date(b.issue_date, '%Y-%m-%d') < '$this->date' OR str_to_date(b.issue_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where[]="(b.area_code='$this->area')";
        }
        
        if($this->item){
            $where[]="(a.item_code='$this->item')";
        }
        
         if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") ";
        else $sql .= ""; 
        
        $sql .= "union (SELECT g.receipt_date,g.refno,'Delivery',i.item_qty,i.unit_id,'','','',i.unit_price FROM seg_delivery as g 
                                        JOIN seg_areas as h ON g.area_code=h.area_code 
                                        JOIN seg_delivery_details as i ON g.refno=i.refno
                                        JOIN care_pharma_products_main as j ON i.item_code=j.bestellnum ";
                                        
        $where2 = array(); 

        if ($this->date) {
            $where2[]="str_to_date(g.receipt_date, '%Y-%m-%d') < '$this->date' OR str_to_date(g.receipt_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where2[]="(g.area_code='$this->area')";
        }
        
        if($this->item){
            $where2[]="(i.item_code='$this->item')";
        }
        
         if ($where2)
            $sql .= "WHERE (".implode(") AND (",$where2).") ";
        else $sql .= "";
        
        $sql .= ") ";
        
        $sql .= "union (SELECT l.issue_date,l.refno,
                (select f.area_name from seg_areas as f where f.area_code = l.area_code),'','',k.item_qty,k.unit_id,
                (select n.area_name from seg_areas as n where n.area_code = l.src_area_code),k.avg_cost
                FROM (seg_issuance_details as k 
                JOIN seg_issuance as l ON k.refno=l.refno) 
                JOIN care_pharma_products_main as m ON k.item_code=m.bestellnum ";
        
        $where3 = array();
                    
        if ($this->date) {
            $where3[]="str_to_date(l.issue_date, '%Y-%m-%d') < '$this->date' OR str_to_date(l.issue_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where3[]="(l.src_area_code='$this->area')";
        }
        
        if($this->item){
            $where3[]="(k.item_code='$this->item')";
        }
        
         if ($where3)
            $sql .= "WHERE (".implode(") AND (",$where3).") ";
        else $sql .= ""; 
        
        $sql .= ") ORDER BY issue_date ASC";
        
        $result=$db->Execute($sql);
                    
        $this->_total = 0;
        
        $counterhelix = 1;
        $balcounter;
        
        if ($result) {
            $this->Data=array();
            
            $prodextend = $prod_obj->getExtendedProductInfo($this->item);
            
            while ($row=$result->FetchRow()) {
                /*
                if($counterhelix == 1){
                    $balcounter = $inv_obj->getInventoryAtHandbyDate($this->item,$this->area,$row['issue_date']);
                }
                */
                $recqty = $row['rec_qty'];
                $issqty = $row['iss_qty'];
                
                if($unit_obj->isUnitIDBigUnit($row['rec_unit'])){
                    if($prodextend)
                        $recqty = $recqty * $prodextend['qty_per_pack'];
                }
                
                if($unit_obj->isUnitIDBigUnit($row['iss_unit'])){
                    if($prodextend)
                        $issqty = $issqty * $prodextend['qty_per_pack'];
                }
                
                if($recqty != '')
                    $balcounter = $balcounter + $recqty;
                if($issqty != '')
                    $balcounter = $balcounter - $issqty;
                
                
                $this->Data[]=array(
                    date("m/d/Y",strtotime($row['issue_date'])),
                    $row['refno'],
                    $row['src_area'],
                    $recqty,  
                    //($recqty != '' ? $prodextend['avg_cost'] : '' ), 
                    //($recqty != '' ? ($prodextend['avg_cost'] * $recqty) : '' ),
                    ($recqty != '' ? $row['unit_cost'] : '' ), 
                    ($recqty != '' ? ($row['unit_cost'] * $recqty) : '' ),
                    $issqty,  
                    ($issqty != '' ? $row['unit_cost'] : '' ), 
                    ($issqty != '' ? ($row['unit_cost'] * $issqty) : '' ),
                    $balcounter,
                    $prodextend['avg_cost'],
                    ($prodextend['avg_cost'] * $balcounter)
                );    
                
                $this->_total+=$row['amount_due'];
                $counterhelix++;
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

$rep = new RepGen_StockCard($_GET['item'],$_GET['area']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>