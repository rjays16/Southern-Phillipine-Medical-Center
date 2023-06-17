<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_Lab_Services extends DMCRepGen {

    var $from_date;
    var $to_date;    
    var $left;
    var $right;
    var $top;
    
    function RepGen_Lab_Services($from, $to) {
        global $db;
        
        $this->DMCRepGen("LABORATORY SERVICES", "L", "Letter", $db, TRUE);
        
        $this->Caption = "Laboratory Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(80,9,19,8,8,16,14,18,19,11,7,19,19);
        $this->Columns = 13;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('L','C','C','C','C','C','C','C','C','C','C','R','R');
        $this->PageOrientation = "L";
        //$this->from_date=date("Y-m-d",strtotime($from));
        //if ($to) $this->to_date=date("Y-m-d",strtotime($to));
        $this->from_date=$from;
        if ($to) $this->to_date=$to;

        $this->NoWrap = FALSE;
        
    }
    
    function Header() {
          
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
        $this->Ln(2);
        $this->SetFont("Arial","B","12");
        $this->Cell(0,4,'LABORATORY SERVICES',$border2,1,'C');
        //$this->Cell(0,4,date("m/d/Y",strtotime($this->from))." - ".date("m/d/Y",strtotime($this->to)),$border2,1,'C');
        $this->Cell(0,4,date("m/d/Y",strtotime($this->from_date))." - ".date("m/d/Y",strtotime($this->to_date)),$border2,1,'C');
        $from_dt=strtotime($this->from_date);
        $to_dt=strtotime($this->to_date);
        $this->Ln(1);
        
        //print labels
        $this->SetFont('Arial','B',9);
        $this->SetXY(47,16);
        $this->Cell(10,16,'Procedures',0,0,'C');
        $this->SetXY(130,18);
        $this->Cell(60,8,'Number of Patients',0,0,'C');
        $this->SetXY(116,23);
        $this->Cell(30,6,'In-Patient',0,0,'C');
        $this->SetXY(180,23);
        $this->Cell(30,6,'Out-Patient',0,0,'C');
        $this->SetXY(226,16);
        $this->Cell(11,16,'Total',0,0,'C');
        $this->SetXY(241,18);
        $this->Cell(20,7,'% of Grand',0,0,'C');
        $this->SetXY(241,22);
        $this->Cell(20,7,'Total',0,0,'C');
        
        //draw lines
        $this->left = 13;
        $this->right = 260;
        $this->SetDrawColor(0,0,0);
        $this->Line($this->left,19,$this->right,19);
        $this->Line($this->left,19,$this->left,28);
        $this->Line(92,19,92,28);
        $this->Line(92,24,223,24);
        $this->Line(168,24,168,28);
        $this->Line(223,19,223,28);
        $this->Line(241,19,241,28);
        $this->Line($this->right,19,$this->right,28);
        $this->Line($this->left,28,$this->right,28);
        $this->top = 29;
        $this->SetXY(13,30);
        
    }
    
    function BeforeData() {
        $this->FONTSIZE = 8.7;
        if ($this->colored) {
            $this->DrawColor = array(0xDD,0xDD,0xDD);
            #$this->DrawColor = array(255,255,255);
        }
    }

    function FetchData() {        
        
        if (empty($this->to)) $end_date="NOW()";
        else $end_date=$this->to;
        if (empty($this->from)) $start_date="NOW()";
        else $start_date=$this->from;

        //laboratory
        $sql = "SELECT * FROM seg_lab_services;";
        $result=$this->Conn->Execute($sql);
        $max = $result->RecordCount();
        $ctr = 0;
        $total = 0;
        $temp_array[$max][5];
        $sql = "SELECT * FROM seg_lab_service_groups;";
        $result=$this->Conn->Execute($sql);
        $this->_count = $result->RecordCount();
        $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($result) {
            $this->Data=array();
            $i=1;
            $this->Data[]=array("Laboratory Examination",'','','','','','','','','','','','');
            $this->_count = $this->_count + 1; 
            while ($row=$result->FetchRow()) {
                $service_group = $row["name"];
                $group_code = $row["group_code"];
                $temp_array[$ctr][0] = $i .". ".$service_group;
                $temp_array[$ctr][3] = "x";
                $ctr++;
                $j=0;
                $sql = "SELECT * FROM seg_lab_services WHERE group_code='".$group_code."';";
                $result2=$this->Conn->Execute($sql);
                $this->_count = $this->_count + $result2->RecordCount(); 
                if($result2)
                {
                    while ($row2=$result2->FetchRow()) {
                        $service = $row2["name"];
                        $service_code = $row2["service_code"];
                        //$sql = "SELECT COUNT(refno) as in_patient FROM seg_lab_servdetails WHERE service_code='".$service_code."' AND is_in_house=1;";
                        $sql = "SELECT COUNT(d.refno) as in_patient FROM seg_lab_servdetails as d INNER JOIN seg_lab_serv as s ON s.refno=d.refno WHERE d.service_code='".$service_code."' AND d.is_in_house=1 AND s.serv_dt <'".$this->to_date."' AND s.serv_dt>'".$this->from_date."';";
                        $result3=$this->Conn->Execute($sql);
                        if($result3)
                        {
                            $row3=$result3->FetchRow();
                            $inpatient = $row3["in_patient"];
                        }
                        else
                            $inpatient = 0;
                        //$sql = "SELECT COUNT(refno) as out_patient FROM seg_lab_servdetails WHERE service_code='".$service_code."' AND is_in_house=0;";
                        $sql = "SELECT COUNT(d.refno) as in_patient FROM seg_lab_servdetails as d INNER JOIN seg_lab_serv as s ON s.refno=d.refno WHERE d.service_code='".$service_code."' AND d.is_in_house=0 AND s.serv_dt <'".$this->to_date."' AND s.serv_dt>'".$this->from_date."';";
                        $result3=$this->Conn->Execute($sql);
                        if($result3)
                        {
                            $row3=$result3->FetchRow();
                            $outpatient = $row3["out_patient"];
                        }
                        else
                            $outpatient = 0;
                        $temp_array[$ctr][0] = "     ".$i.".".$j.". ".$service;
                        //$temp_array[$ctr][0] = $sql;
                        if($inpatient==0)
                            $temp_array[$ctr][1] = "z";
                        else
                            $temp_array[$ctr][1] = $inpatient;
                        if($outpatient==0)
                            $temp_array[$ctr][2] = "z";
                        else
                            $temp_array[$ctr][2] = $outpatient;
                        if($inpatient==0 && $outpatient==0)
                            $temp_array[$ctr][3] = "z";
                        else
                            $temp_array[$ctr][3] = $inpatient + $outpatient;
                        $in_total += $inpatient;
                        $out_total += $outpatient;
                        $ctr++;
                        $j++;
                    }
                }
                $i++;
            }
        }
        else
            echo $this->Conn->ErrorMsg();
        $total = $in_total + $out_total;
        $per_total = 0;
        for($z=0; $z<$ctr; $z++)
        {
            $prnt_total = $temp_array[$z][3];
            $prnt_inpatient = $temp_array[$z][1];
            $prnt_outpatient = $temp_array[$z][2];
            $temp_array[$z][4] = ($temp_array[$z][3] / $total)*100;
            $per_total += $temp_array[$z][4];
            $prnt_pertotal = number_format($per_total,2)."%";
            if($temp_array[$z][3]=="x")
            {
                $prnt_total='';
                $prnt_pertotal='';
            }
            if($temp_array[$z][2]=="z")    
            {
                $prnt_outpatient="0";
            }
            if($temp_array[$z][1]=="z")    
            {
                $prnt_inpatient="0";
            }
            if($temp_array[$z][3]=="z")    
            {
                $prnt_total="0";
                $prnt_pertotal="0.00%";
            }
            $this->Data[]=array($temp_array[$z][0],'',$prnt_inpatient,'','','','','',$prnt_outpatient,'','',$prnt_total,$prnt_pertotal);
        }
        $this->Data[]=array('Grand Total =>','',number_format($in_total),'','','','','',number_format($out_total),'','',number_format($total),$per_total."%");
    }
}

$rep = new RepGen_Lab_Services($_GET['from'],$_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>