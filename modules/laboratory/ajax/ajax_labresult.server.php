<?php
//added by Nick 1/28/2014
    $header = 0;
$footer_group = '';
$doSignatories = false;
$isWritingDetails = false;
$final_medtech='';
$final_pathologist='';
$current_group='';
$result_info='';

	function Results($hrn,$lis_order_no){
		$objResponse = new xajaxResponse();

		global $db;

        $objInfo = new Hospital_Admin();
        $srvObj=new SegLab();
        $parseObj = new seg_parse_msg_HL7();
        $hl7fxnObj = new seg_HL7();

        $t_group = array();
        $t_name = array();
        $t_details = array();
        $t_info = array();

        // $hrn = $_GET['pid'];
        // $lis_order_no = $_GET['lis_order_no'];

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

                $date_received = date("m-d-Y, h:i A", strtotime($obr[$i]['date_received']));
                $date_reported = date("m-d-Y, h:i A", strtotime($msh[$i]['date_reported']));

                $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['test']));

                $testcode = $arr_test[0];
                $testname = $arr_test[1];
                
                $rstest = $srvObj->getLabGroup($testcode);
                $testgroup = $rstest['name'];

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

            if(!in_array($testgroup, $t_group)){
                    array_push($t_group, $testgroup);
                }

                $signatories = array("signatory",mb_strtoupper($medtech),mb_strtoupper($pathologist),$testgroup);
                $t_name = array();
                array_push($t_name, $testname);
                array_push($t_name,$t_details);
                $t_details = array();
                array_push($t_group, $t_name);
                array_push($t_group, $signatories);
                $i++;
            }//end for
                PatientTests($t_group,$t_info,&$objResponse);
        }else{
            $objResponse->call('hideLoading');
            $text2 = "Error : ".$db->ErrorMsg();
            echo "<html><head></head><body>".$text2."</body></html>";
        }

		return $objResponse;
	}


#------------------------------------------------------------------------------------------------------

    function PatientTests($testgroup,$testinfo,&$objResponse){

        $signatories = Signatories($testgroup,&$objResponse);
    // $objResponse->call('debug',print_r($testgroup,true));
    // $objResponse->call('debug',print_r($signatories,true));

        $index = 0;
        foreach ($testgroup as $key1 => $t_group) {
            if(!is_array($t_group)){
            $cur_group = $t_group;
                createHeader(&$objResponse);
                DocumentDetails($testinfo[$index],($index!=0)?0:1,$objResponse);
                $objResponse->call('createGroup',$t_group);
                $index++;
            }else{
                if($t_group[0]=="signatory"){
                    $isFinalSignatory = isFinalSignatory($signatories,$key1,$objResponse);
                }else{
                    foreach ($t_group as $key2 => $t_name) {
                        $header = 1;
                        if(!is_array($t_name)){
                        $isRedundant = false;
                        foreach ($t_group[1] as $key => $value) {
                            if($value[1] == $t_name){
                                $isRedundant = true;
                            }
                        }
                        if(!$isRedundant){
                            $objResponse->call('createTestName',$t_name);
                        }
                        }else{
                            $objResponse->call('createDetails',$t_name);
                        }
                    }
                    $header = 0;
                }
            }
        }
    $objResponse->call('hideLoading');
    }

    function DocumentDetails($details,$ln,&$objResponse){
        if($ln==0)
            $objResponse->call('Ln');
        $objResponse->call('documentDetails',$details);
    }

    function ColumnHeader(&$objResponse){
        $this->Cell(0,4,str_repeat("-", 105), 0, 1,'C');
        $this->SetFont('Courier','B',9);
        $this->Cell(60,4,"TEST", 0, 0,'C');
        $this->Cell(60,4,"RESULT", 0, 0,'C');
        $this->Cell(60,4,"REFERENCE RANGE", 0, 1,'C');
        $this->SetFont('Courier','',9);
        $this->Cell(0,4,str_repeat("-", 105), 0, 1,'C');
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

    function isFinalSignatory($signatories,$key,&$objResponse){
        $output_signatories = array();
        $output = array();

        foreach ($signatories as $key1 => $signatory) {
        $end_value = $signatory[0];
        foreach ($signatory as $key2 => $details) {
            if($end_value[4] == $key){
                    array_push($output_signatories, array(
                        $details[1],
                        $details[2]
                    ));
            }else{
                }
            }
        }
    WriteSignatories($output_signatories,$cur_group,$objResponse);
    }

function WriteSignatories($isFinalSignatory,$cur_group,&$objResponse){
        $medtechs = "";
    $added = array();
        if(count($isFinalSignatory)>0){
            foreach ($isFinalSignatory as $key => $value) {
            if(!in_array($value, $added)){
                array_push($added, $value);
                $medtechs .= $value[0].' , ';
            }
        }
        $doSignatories = true;
        $final_medtech = trim($medtechs,' , ');
        $final_pathologist = $isFinalSignatory[0][1];
        $current_group = $cur_group;
        Signatory($final_medtech,$final_pathologist,$objResponse);
        }
    }

    function Signatory($medtech,$pathologist,&$objResponse){
        $objResponse->call('createSignatories',strtoupper($medtech),strtoupper($pathologist));
    }

    function createHeader(&$objResponse) {
        if($header == 0){
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
            $objResponse->call('header',$row_hosp);
        }
    }

    function Footer(&$objResponse) {
        $this->SetY(-15);
        $this->SetFont('Courier','',8);
        $this->Cell(0,10,'*** This is electronically generated report. No signature is required. ***',0,0,'C');
        $this->Ln(5);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
    }

#------------------------------------------------------------------------------------------------------

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
	require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');

	require($root_path."modules/laboratory/ajax/ajax_labresult.common.php");
	$xajax->processRequest();
?>
