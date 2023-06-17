<?php

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require($root_path.'modules/bloodBank/ajax/blood-received-sample.common.php');
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    require_once($root_path.'include/care_api_classes/class_blood_bank.php');
    require_once($root_path.'frontend/bootstrap.php');
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');

    
    function save_dateinfo($refno, $service_code, $index, $mode, $date, $time, $return_reason, $release_result=''){
        global $db;
        $srvObj=new SegLab();
        $objResponse = new xajaxResponse();    
        
        $datetime = $date." ".$time;
        
        //added by: borj
        //2013/28/11
        $RetReason = $srvObj->checkRetReason($refno);

       
        if ($return_reason){
            if ($RetReason != $return_reason)
                $RetReason = $return_reason;
       }
       
       if($release_result=='') {$ok = $srvObj->UpdateBloodStatus($refno, $service_code, $index, $mode, $datetime, $RetReason);}
       else {$ok = $srvObj->UpdateBloodStatus($refno, $service_code, $index, $mode, $datetime, $RetReason, $release_result); }
       
        
        if ($ok)
            $msg = "The Data is successfully change.";
        else    
            $msg = "Updating Data is failed.";
        
        $objResponse->alert($msg);
        
                
        return $objResponse;
    }
    function save_percheck($refno, $pid='', $serv_result, $blood_type, $component, $qty, $serial_no, $status, $mode, $date, $time){

        global $db;

        $bloodObj=new SegBloodBank();

        $datetime = $date." ".$time;

        $bloodObj->savePerDate($refno, $pid, $serv_result, $blood_type, $component, $qty, $serial_no, $status, $mode, $datetime);

    }

    
    #added by VAS 10/04/2019
    function getLISResultInfo($pid, $refno, $service_code, $serial_no, $id){
        global $db;
        $objResponse = new xajaxResponse(); 

        $bloodObj = new SegBloodBank();
        $parseObj = new seg_parse_msg_HL7();
        $hl7fxnObj = new seg_HL7(); 

        $arr = explode($service_code,$id);
        $index = $arr[1];

        $details->refno = $refno;
        $details->service_code = $service_code;
        $details->ordering = $index;

        $testcode = $themes = Config::model()->findByPk('bloodbank_default_testcode');
        
        $pxblood_res = $bloodObj->getPatientBloodResult($refno, $testcode, $index);
        
        if (is_object($pxblood_res)){
            $row = $pxblood_res->FetchRow();
            extract($row);      

            #parse result starts here for the ABORH ORU
            #PART 1 : PATIENT ABORH

            #PID INFO
            $order_message = $row['order_hl7_msg'];
            $segments_order = explode($parseObj->delimiter, trim($order_message));
            $details_part1 = $parseObj->bloodparseHL7($segments_order);

            $obr_p1 = $details_part1->obr;

            $arr_loc = explode($parseObj->COMPONENT_SEPARATOR, trim($obr_p1['location']));
            $loc = $arr_loc[1];

            $dept = $bloodObj->getBloodDept($loc);
            $location = $dept['long_name'];

            #OBR SEGMENT
            #OBR for ABORH
            $message = $row['hl7_msg'];
            $segments = explode($parseObj->delimiter, trim($message));
            $details_part2 = $parseObj->bloodparseHL7($segments);
            $obr_p2 = $details_part2->obr;

            $date_received = $obr_p2['date_received'];
            $date_crossmatched = $obr_p2['date_crossmatched'];
            #---OBR SEGMENT for ABORH
        } #end if (is_object($pxblood_res))  
        #PART 2 : PATIENT BLOOD PREPARED PRODUCTS
        $order = $bloodObj->getLabOrderNoLIMIT($refno);
        $lis_order_no = $order['lis_order_no'];
        $details->serial_no = str_replace(" ", "", $serial_no);
        $blood_seq = $db->GetAll("SELECT result_code FROM seg_blood_result_seq WHERE is_included = 1 ORDER BY lis_ordering DESC");

        foreach($blood_seq as $seq){
            $bloodprod = $bloodObj->getPatientPreparedBloodProd($lis_order_no,$pid,$details->serial_no,$seq['result_code']);

            if($bloodprod)
                break;
        }

        $nofound=0;
        if (is_object($bloodprod)){
            $bps_row = $bloodprod->FetchRow();

            $bps_message = $bps_row['hl7_msg'];
            $segments_bps = explode($parseObj->delimiter, trim($bps_message));
            $res_bps = $parseObj->bloodparseHL7($segments_bps);
            

            $bpo = $res_bps->bpo;
            $component = $bloodObj->getAllBloodComponents($bpo['blood_component']);

            $bpx = $res_bps->bpx;
            
            $date_done = $bpx['date_crossmatched'];
            
            $result_compatibility = $bloodObj->getBloodCrossmatchResultDesc($bpx['crossmatching_result']);
            
            $arr_source = explode($parseObj->COMPONENT_SEPARATOR, trim($bpx['blood_source']));
            $sources = $arr_source[0];

        }else{
            $result_compatibility = 'No matching of Serial Number found.';
            $nofound=1;
        }

        $details->error = 0;
        $details->id = $id;
        $details->index = $index;
        if ($nofound!=1){
            $details->location = $location;
            $details->blood_component = $component['id'];
            $details->blood_source = $sources;
            $details->date_received = ($date_received)?date('Y-m-d H:i:s',strtotime($date_received)):'';
            $details->date_crossmatched = ($date_crossmatched)?date('Y-m-d H:i:s',strtotime($date_crossmatched)):'';
            $details->date_done = ($date_done)?date('Y-m-d H:i:s',strtotime($date_done)):'';
            $details->crossmatched_result = $result_compatibility;

            $details->result = $bloodObj->getBloodResultCode($bpx['crossmatching_result']);

            #update the table seg_blood_received_details 
            $ok = $bloodObj->updateBloodReceivedDetails($details);
            $ok1 = $bloodObj->updateBloodReceivedStatus($details);
        }   
        
        
        if (!$ok){
            
            $details->location = '';
            $details->blood_component = '';
            $details->blood_source = '';
            $details->date_received = '';
            $details->crossmatched_result = '';
            $details->error = 1;
        }

        if(!$ok1){
            $details->date_crossmatched = '';
            $details->date_done = '';
            $details->error = 1;
        }
        
        #$objResponse->alert(print_r($details));
        $objResponse->call("assignResultValue",$details);

        return $objResponse;

    }

    // function save_datainfo($refno, $service_code, $index, $mode, $date, $time, $is_urgents){
    //     global $db;
    //     $srvObj=new SegLab();
    //     $objResponse = new xajaxResponse();    
        
    //     $datetime = $date." ".$time;
        
    //     //added by: borj
    //     //2013/28/11
    //     $statusInfo = $srvObj->checkstatusInfo1($refno);
        
       
    //     if ($is_urgents){
    //         if ($statusInfo != $is_urgents)
    //             $statusInfo = $is_urgents;
    //    }
       
    //    $ok = $srvObj->UpdateBloodStatus1($refno, $service_code, $index, $mode, $datetime, $statusInfo); 
       
        
    //     if ($ok)
    //         $msg = "The Data is successfully change.";
    //     else    
    //         $msg = "Updating Data is failed.";
        
    //     $objResponse->alert($msg);
        
                
    //     return $objResponse;
    // }

    function saveCoveragePHIC($received_array) {
        global $db;
        $srvObj=new SegLab();
        $objResponse = new xajaxResponse();

        foreach ($received_array as $value) {
            $enc = $value['encounter'];
            define('__PHIC_ID__', 18);
            $encounter_nr = "T$enc";
            $coverage = $value['coverage'];

            $sqlCoverage = "SELECT coverage FROM seg_applied_coverage\n".
                                        "WHERE ref_no='{$encounter_nr}'\n".
                                            "AND source='L'\n".
                                            "AND item_code='".$value['code']."'\n".
                                            "AND hcare_id=".__PHIC_ID__;

            $cov = parseFloatEx($db->GetOne($sqlCoverage)) + parseFloatEx($coverage);

            $result = $db->Replace('seg_applied_coverage',
                                                array(
                                                     'ref_no'=> $db->qstr($encounter_nr),
                                                     'source'=> $db->qstr('L'),
                                                     'item_code'=> $db->qstr($value['code']),
                                                     'hcare_id'=> $db->qstr(__PHIC_ID__),
                                                     'coverage'=> $db->qstr($cov)
                                                ),
                                                array('ref_no', 'source', 'item_code', 'hcare_id')
                                   );
            if ($result) {
                $dbOk = TRUE;
            } else {
                $this->error_msg = "Unable to update applied coverage for item #{$value['code']}...";
                $dbOk = FALSE;
            }
        }
        return $objResponse;
    }



    $xajax->processRequest();
?>