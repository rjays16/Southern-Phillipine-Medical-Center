<?php
//added by Nick 1/24/2014
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'classes/fpdf/fpdf.php');

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');

define('FONT_INCREMENT', 1);

class lab_pdf extends FPDF {

var $header = 0;
var $footer_group = '';
var $doSignatories = false;
var $isWritingDetails = false;
var $final_medtech,$final_pathologist,$current_group,$result_info;

var $pid,$lis,$sql;

    function Results(){

        global $db;

        $objInfo = new Hospital_Admin();
        $srvObj=new SegLab();
        $parseObj = new seg_parse_msg_HL7();
        $hl7fxnObj = new seg_HL7();

        $t_group = array();
        $t_name = array();
        $t_details = array();
        $t_info = array();

        $hrn = $this->pid;
        $lis_order_no = $this->lis;

        $rs = $hl7fxnObj->getAllResultByOrder($hrn, $lis_order_no);
        if (is_object($rs)){    
            
            $numrows = $hl7fxnObj->count;

            $row_pathologist = $srvObj->getPathologist();

            if ($row_pathologist['other_title'])
                $title = ", ".$row_pathologist['other_title'];
            
            $prefix_pathologist = "SGD ";               
            $pathologist = $prefix_pathologist.$row_pathologist['fullname'].$title;
            
            $i=1;
            while($row=$rs->FetchRow()){
                $message = $row['hl7_msg'];
                $filename = $row['filename'];

                #parse result starts here
                $segments = explode($parseObj->delimiter, trim($message));
                $counter_obx = 1;
                $counter_nte = 1;
                $cnt=1;
                $cnt2=1;

                foreach($segments as $segment) {
                    $data = explode('|', trim($segment));
                    
                    if (in_array("MSH", $data)) {
                        $msh[$i] = $parseObj->segment_msh($data);
                    }

                    if (in_array("MSA", $data)) {
                        $msa[$i] = $parseObj->segment_msa($data);
                    }

                    if (in_array("PID", $data)) {
                        $pidsegment[$i] = $parseObj->segment_pid($data);

                        #added by VAS 02/22/2021
                        #if some of PV1 is NULL (request was made in LIS and not in SEGHIS)
                        /*
                            MSH|^~\\&|HCLAB|SPMC|SEGWORKS|SPMC|20210219130129||ORU^R01|HCL0019866795|P|2.3||||||8859
                            PID|1||744491||^RAVELO SHARMANE|||
                            PV1|||||||||||||||||||
                        */

                        if ( !($pidsegment[$i]['sex']) || !($pidsegment[$i]['bdate'])){
                            $pidsegment[$i]['sex'] = $row['sex'];
                            $pidsegment[$i]['bdate'] = $row['bdate'];
                        }   
                    }

                    if (in_array("OBR", $data)) {
                        $obr[$i] = $parseObj->segment_obr($data);
                    }

                    if (in_array("OBX", $data)) {
                        $obx[$i][$counter_obx] = $parseObj->segment_obx($data);
                        $counter_obx++;
                    }

                    if (in_array("NTE", $data)) {
                        $nte[$i][$counter_nte] = $parseObj->segment_nte($data,$counter_obx);
                        $counter_nte++;
                    }
                }
                #=========================

                $patient_name = $row['patient_name'];
                $date_update = $row['date_update'];
                // echo json_encode($row) . "<br><hr>";
                
                $date_received = date("m-d-Y, h:i A", strtotime($obr[$i]['date_received']));
                $date_reported = date("m-d-Y, h:i A", strtotime($msh[$i]['date_reported']));

                $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['test']));

                $testcode = $arr_test[0];
                $testname = $arr_test[1];
                
                $rstest = $srvObj->getLabGroup($testcode);
                #$testgroup = $rstest['name'];
                #$isofooter = $rstest['examtype'];
                #edited by VAS 04/12/2018
                $testgroup = $rstest['name'].';;'.$rstest['examtype'].';;'.$rstest['iso'];

                $sex =  (($pidsegment[$i]['sex']=='M')?'Male':
                        (($pidsegment[$i]['sex']=='F')?'Female':'Unspecified'));

                $sql_age = "SELECT fn_get_age(DATE(".$db->qstr($date_update)."),DATE(".$db->qstr($pidsegment[$i]['bdate']).")) AS age";
                $age = $db->GetOne($sql_age);
                
                $arr_physician = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['physician']));
                $physician = $arr_physician[1];
                
                $arr_loc = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['location']));
                $location = $arr_loc[1];
                
                if($date_update == null || $date_update == "")
                    $date_released = '';
                else    
                    $date_released = date("m-d-Y  h:i A", strtotime($date_update));

                $labtestdetails = '';
                
                for ($cnt2=1; $cnt2 < $counter_nte; $cnt2++){
                    $comments = str_ireplace('\\.br\\', '*', stripslashes($nte[$i][$cnt2]['comment']));
                    $comments = str_ireplace('\\', '', $comments);

                    $index = $nte[$i][$cnt2]['index'];           
                    $notes[$i][$index] = $comments; 
                    $notes[$i][$index2] = $index;
                }
                
                unset($parenttest);
                for ($cnt=1; $cnt < $counter_obx; $cnt++){

                    $arr_testservice = explode($parseObj->COMPONENT_SEPARATOR, trim($obx[$i][$cnt]['testservice']));
                    $testservice = $arr_testservice[1];
                    $testcode = $arr_testservice[0];

                    $sql_test = $srvObj->getTestCode($testcode);
                    $code = $sql_test['service_code'];   
                    
                    if (stripslashes($obx[$i][$cnt]['result'])!='\"\"'){
                        $result = stripslashes($obx[$i][$cnt]['result']);

                        if ($result=='""')
                            $result = str_ireplace('""', '', $result);
                    }else{
                        $result = '';
                    }

                    $sql_childtest = "SELECT fn_get_labtest_child_code_all(".$db->qstr(trim($testcode)).") AS childtest";
                    $childtest = $db->GetOne($sql_childtest);   
                    
                    $arr_testchild = explode(",",$childtest);
                    
                    if ($arr_testchild[0]=='')
                        unset($arr_testchild);
                    
                    if (in_array($code, $parenttest))
                        $space3 = 0;
                    else
                        $space3 = 1;
                    
                    
                    #\\S\\
                    $units = str_ireplace('\\S\\', $parseObj->COMPONENT_SEPARATOR, stripslashes($obx[$i][$cnt]['units']));
                    $units = str_ireplace('\\', '', $units);

                    if (substr($arr_testservice[1], 0, 1)==' ')
                        $space4 = 0;
                    else
                        $space4 = 1;    
                    
                    if ($obx[$i][$cnt]['result_flag']=='N')      
                        $flag = '';
                    else
                        $flag = $obx[$i][$cnt]['result_flag'];
                    
                    if ($obx[$i][$cnt]['result']!='!'){
                        array_push($t_details, array( (($space4!=1||$space3!=1)?0:1), trim($testservice),$flag,$result,$units,$obx[$i][$cnt]['reference_range'],$notes[$i][$cnt+1]));
                    }      

                    if (sizeof($arr_testchild)){
                        $parenttest = $arr_testchild;
                    }
                }

                $medtechobj = trim($obx[$i][$counter_obx-1]['medtech']);  
                $arr_medtech = explode($parseObj->COMPONENT_SEPARATOR, $medtechobj);
                $medtech = $arr_medtech[1]; 

                $test_info = array("name"=>$patient_name,
                                   "lab_no"=>$obr[$i]['lab_no'],
                                   "pid"=>$hrn,
                                   "location"=>$location,
                                   "age"=>$age.' old',
                                   "gender"=>$sex,
                                   "physician"=>$physician,
                                   "received_dt"=>$date_received,
                                   "reported_dt"=>$date_reported,
                                   "released_dt"=>$date_released);

                array_push($t_info, $test_info);


                $testgroup_key = array_search($testgroup, $t_group);

                if(!in_array($testgroup, $t_group)){
                    array_push($t_group, $testgroup);
                }

                // if(!$testgroup_key){
                //     array_push($t_group, $testgroup);
                // }else{

                // }

                $signatories = array("signatory",mb_strtoupper($medtech),mb_strtoupper($pathologist),$testgroup);
                $t_name = array();
                array_push($t_name, $testname);
                array_push($t_name,$t_details);
                $t_details = array();
                array_push($t_group, $t_name);
                array_push($t_group, $signatories);
                $i++;
            }//end for

            // echo "<pre>" . print_r($t_group,true) . "</pre>";exit();
            $this->PatientTests($t_group,$t_info);

        }else{
            $text2 = "Error : ".$db->ErrorMsg();
            echo "<html><head></head><body>".$text2."</body></html>";
        }

    }//end Results function

    function addCell($w,$h,$label,$text,$ln=0,$d1=0.3,$d2=0.7,$fsize=9){
        $this->SetFont('Courier','',$fsize+FONT_INCREMENT);
        $this->Cell($w * $d1,$h,$label.":", 0, 0,'L');
        $this->SetFont('Courier','B',$fsize+FONT_INCREMENT);
        $this->Cell($w * $d2,$h,$text, 0, $ln,'L');
        $this->SetFont('Courier','',$fsize+FONT_INCREMENT);
    }

    function DocumentDetails($details){

        $this->addCell(100,4,"Name",$details['name'],0,0.15,0.85);
        $this->addCell(100,4,"Lab no",$details['lab_no'],1);

        $this->addCell(100,4,"PID",$details['pid'],0,0.15,0.85);
        $this->addCell(100,4,"Location",$details['location'],1);

        $this->addCell(50,4,"Age",$details['age']);
        $this->addCell(50,4,"Sex",$details['gender']);
        $this->addCell(100,4,"Physician",$details['physician'],1);

        $this->Ln(2);
        $this->addCell(80,4,"Date Received",$details['received_dt'],0,0.3,0.5,7);
        $this->addCell(80,4,"Date Reported",$details['reported_dt'],0,0.3,0.5,7);
        $this->addCell(80,4,"Date Released",$details['released_dt'],1,0.3,0.5,7);
        $this->SetFont('Courier','',9+FONT_INCREMENT);
    }

    function ColumnHeader(){
        $this->Cell(0,4,str_repeat("-", 95), 0, 1,'C');
        $this->SetFont('Courier','B',9+FONT_INCREMENT);
        $this->Cell(60,4,"TEST", 0, 0,'C');
        $this->Cell(70,4,"RESULT", 0, 0,'C');
        $this->Cell(70,4,"REFERENCE RANGE", 0, 1,'C');
        $this->SetFont('Courier','',9+FONT_INCREMENT);
        $this->Cell(0,4,str_repeat("-", 95), 0, 1,'C');
    }

    function Signatories($testgroup){
        $element_cnt = count($testgroup)-1;
        $added_signatories = array();
        $signatories = array();

        $t_groups = array();
        foreach ($testgroup as $key => $value) {
            if(!is_array($value))
                array_push($t_groups, $value);
        }

        $t_groups = array_unique($t_groups);
        foreach ($t_groups as $key => $value) {
            $signatories[$value] = array();
        }

        foreach ($testgroup as $key => $value) {
            if(is_array($value) && $value[0] == 'signatory'){
                if(!array_search($value[3], $added_signatories)){
                    array_push($added_signatories, $value[3]);

                    array_push($value, $key);
                    array_push($signatories[$value[3]], $value);
                }else{
                    array_push($value, $key);
                    array_push($signatories[$value[3]], $value);
                }
            }
        }

        return $signatories;

    }

    function isFinalSignatory($signatories,$key){

        $output_signatories = array();
        $output = array();

        foreach ($signatories as $key1 => $signatory) {
            $end_value = end($signatory);
            if($end_value[4] == $key){
                foreach ($signatory as $key2 => $details) {
                    array_push($output_signatories, array(
                        $details[1],
                        $details[2]
                    ));
                }
            }else{
            }
        }
        return $output_signatories;
    }

    function WriteSignatories($isFinalSignatory,$cur_group){
        $medtechs = "";
        $added = array();
        if(count($isFinalSignatory)>0){
            foreach ($isFinalSignatory as $key => $value) {
                if(!in_array($value, $added)){
                    array_push($added, $value);
                    $medtechs .= $value[0].' , ';
                }
            }
            $this->doSignatories = true;
            $this->final_medtech = trim($medtechs,' , ');
            $this->final_pathologist = $isFinalSignatory[0][1];
            $this->current_group = $cur_group;
            // $this->Signatory(trim($medtechs,' , '),$isFinalSignatory[0][1],$cur_group);
        }
    }

    function PatientTests($testgroup,$testinfo){
        $this->AddPage();

        $signatories = $this->Signatories($testgroup);

        $cur_group = '';

        $index = 0;
        $t_group_cnt = 0;
        $t_name_cnt = 0;

        foreach ($testgroup as $key1 => $t_group) {
            if(!is_array($t_group)){
                if($index!=0){
                    $this->AddPage();
                }

                #added by VAS 04/12/2018
                $isoextract = explode(';;', $t_group);
                #iso footer group
                $this->footer_group = $isoextract[2];
                $this->t_group = $isoextract[0];

                $cur_group = $t_group;
                $this->result_info = $testinfo[$index];
                $this->DocumentDetails($testinfo[$index]);
                $this->ColumnHeader();
                $this->SetFont('Courier','B',9+FONT_INCREMENT);
                #$this->Cell(0,4,$t_group, 0, 1,'L');
                #edited by VAS 04/12/2018

                #section name
                $this->Cell(0,4,$isoextract[0], 0, 1,'L');
                $this->SetFont('Courier','',9+FONT_INCREMENT);
                $this->Ln(2);
                $index++;
            }else{
                $this->isWritingDetails = true;
                if($t_group[0]=="signatory"){
                    $isFinalSignatory = $this->isFinalSignatory($signatories,$key1);
                    $isFinalSignatory = array_unique($isFinalSignatory);
                    $this->WriteSignatories($isFinalSignatory,$cur_group);
                }else{
                    foreach ($t_group as $key2 => $t_name) {
                        $this->header = 1;
                        // if($this->GetY() >= 115.1)
                        //     $this->AddPage();
                        if(!is_array($t_name)){
                            $this->SetFont('Courier','B',9+FONT_INCREMENT);
                            //search header
                            $isRedundant = false;
                            foreach ($t_group[1] as $key => $value) {
                                if($value[1] == $t_name){
                                    $isRedundant = true;
                                }
                            }
                            if(!$isRedundant)
                                $this->Cell(0,4,$t_name, 0, 1,'L');
                            $this->SetFont('Courier','',9+FONT_INCREMENT);
                        }else{
                            
                            foreach ($t_name as $key3 => $t_details) {

                                if($t_details[0]==0){
                                    $this->Cell(5,4,"", 0, 0,'L');
                                    $this->Cell(65,4,$t_details[1], 0, 0,'L');
                                }else{
                                    $this->Cell(70,4,$t_details[1], 0, 0,'L');
                                }

                                $this->Cell(10,4,$t_details[2], 0, 0,'L');
                                $this->Cell(40,4,$t_details[3], 0, 0,'L');
                                $this->Cell(30,4,$t_details[4], 0, 0,'L');
                                $this->Cell(30,4,$t_details[5], 0, 1,'L');
                                if($t_details[6]!='' || $t_details[6]!=null){
                                    $this->Cell(5,4,"", 0, 0,'L');
                                    $this->Cell(30,4,$t_details[6], 0, 1,'L');
                                }
                            }
                        }
                        $t_name_cnt++;
                    }
                    $this->header = 0;
                }
                $this->isWritingDetails = false;
            }
            $t_group_cnt++;
        }
        $this->Ln();
    }

    function Signatory($medtech,$pathologist,$cur_group){
        $this->Ln();
        $this->Ln();
        $this->SetFont('Courier','B',9+FONT_INCREMENT);
        $this->Cell(10,4,'', 0, 0,'C');//indent

        $medtech = str_replace('   ', ' ', $medtech);
        $this->Cell(70,4,$medtech, 0, 0,'C');//name
        $this->Cell(35,4,"", 0, 0,'C');//separate
        $this->Cell(70,4,$pathologist, 0, 1,'C');//name
        $this->SetFont('Courier','',9+FONT_INCREMENT);
        $this->Cell(10,4,'', 0, 0,'C');//indent
        $this->Cell(70,4,"Medical Technologist", 'T', 0,'C');//border top
        $this->Cell(35,4,"", 0, 0,'C');//separate
        $this->Cell(70,4,"Pathologist", 'T', 1,'C');//border top
        // $this->Footer2($cur_group);
        // $this->footer_group = $cur_group;
    }

    function lab_pdf($pid,$lis){
        $this->FPDF('P','mm',array(215.9,165.1));
        $this->SetTitle("Results", true);
        $this->pid = $pid;
        $this->lis = $lis;
    }

    function Header() {
        // if($this->header == 0){
            $objInfo = new Hospital_Admin();
            if ($row_hosp = $objInfo->getAllHospitalInfo()) {
                $row_hosp['hosp_agency'] = strtoupper($row_hosp['hosp_agency']);
                $row_hosp['hosp_name']   = strtoupper($row_hosp['hosp_name']);
            }else {
                $row_hosp['hosp_country'] = "Republic of the Philippines";
                $row_hosp['hosp_agency']  = "DEPARTMENT OF HEALTH";
                $row_hosp['hosp_name']    = "DAVAO MEDICAL CENTER";
                $row_hosp['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
            }
            $this->SetFont("Courier",'B', 9+FONT_INCREMENT);
            $this->Cell(0,4,$row_hosp['hosp_country'], 0, 1,'C');
            $this->Cell(0,4,$row_hosp['hosp_agency'], 0, 1,'C');
            $this->Cell(0,4,$row_hosp['hosp_name'], 0, 1,'C');
            $this->Cell(0,4,$row_hosp['hosp_addr1'], 0, 1,'C');
            $this->SetFont("Courier",'', 9+FONT_INCREMENT);
            $this->Ln();
            // echo "<pre>".print_r($this->result_info,true)."</pre>";
            if($this->isWritingDetails){
                $this->DocumentDetails($this->result_info);
                $this->ColumnHeader();
            }
        // }
    }

    function Footer() {
        if($this->doSignatories){
            $y = -30;
        }else{
            $y = -15;
        }
        $this->SetY($y);
        if($this->doSignatories){
            $this->Signatory($this->final_medtech,$this->final_pathologist,$this->current_group);
        }
        $this->SetFont('Courier','',8+FONT_INCREMENT);
        $this->Cell(0,10,'*** This is electronically generated report. No signature is required. ***',0,0,'C');
        $this->Ln(5);
        $this->SetFont('Courier','B',9+FONT_INCREMENT);

        #edited by VAS 04/13/2018
        $footer = $this->footer_group; #with exam type from seg_lab_services
        if (empty($this->footer_group)){
            $footer = $this->getIso($this->t_group);
        }

        $width = ($this->w - $this->lMargin - $this->rMargin) / 4;
        $this->Cell($width, 10, $footer, 0, 0, 'L');
        $this->Cell($width, 10, "Effectivity: October 1, 2013", 0, 0, 'L');
        $this->Cell($width, 10, "  Rev. 0", 0, 0, 'C');
        $this->Cell($width, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 1, 'R');
    }

    // function Footer2($t_group){
    //     $this->SetFont('Courier','',8+FONT_INCREMENT);
    //     $this->Cell(0,10,'*** This is electronically generated report. No signature is required. ***',0,0,'C');
    //     $this->Ln(5);
    //     $this->SetFont('Courier','B',8+FONT_INCREMENT);
    //     $this->Cell(100,10,$this->getIso($t_group),0,0,'L');
    //     $this->Cell(100,10,'Page '.$this->PageNo().' of {nb}',0,1,'R');
    //     $this->Cell(0,4,'', 'T', 1,'L');
    //     $this->SetFont('Courier','',8+FONT_INCREMENT);
    // }

    function getIso($t_group){
        switch ($t_group) {
            case 'SEROLOGY AND IMMUNOLOGY':
                return 'SPMC-F-LAB-SERO-01';
                break;
            case 'CLINICAL MICROSCOPY':
                return 'SPMC-F-LAB-CM-01';
                break;
            case 'HEMATOLOGY':
                return 'SPMC-F-LAB-HEMA-01';
                break;
            case 'CLINICAL CHEMISTRY':
                return 'SPMC-F-LAB-CC-01';
                break;
            default:
                return $t_group;
                break;
        }
    }

    function outputFile($mode=false){
        $this->AliasNbPages();
        $this->Results();
        if($mode){
            return $this->Output('','s');
        }else{
            $this->Output();
        }
    }

    function setIsStored($filename,$value){
        global $db;
        $this->sql = $db->Prepare("UPDATE seg_hl7_hclab_msg_receipt SET is_stored = ? WHERE filename = ?");
        $rs = $db->Execute($this->sql,array($value,$filename));
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    function setIsStored2($value){
        global $db;
        $this->sql = $db->Prepare("UPDATE seg_hl7_hclab_msg_receipt SET is_stored = ? WHERE pid = ? AND lis_order_no = ?");
        $rs = $db->Execute($this->sql,array($value,$this->pid,$this->lis));
        $db->debug = false;
        if($rs){
            return true;
        }else{
            return false;
        }
    }

}//end class

if(isset($_GET['pid']) && isset($_GET['lis_order_no'])){
    $fpdf = new lab_pdf($_GET['pid'],$_GET['lis_order_no']);
    $fpdf->outputFile();
}