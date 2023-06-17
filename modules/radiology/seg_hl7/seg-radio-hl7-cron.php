<?php
    #for cron schedule
    #per minute
	# created by VAN 06-18-2014
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');

    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require_once($root_path.'include/care_api_classes/class_radiology.php');
    $radio_obj = new SegRadio;

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_parse_hl7_message.php');
    $parseObj = new seg_parse_msg_HL7();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
    $hl7fxnObj = new seg_HL7();
    
    $row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    $details->address = $row_hosp['PACS_address'];
    $details->username = $row_hosp['PACS_username'];
    $details->password = $row_hosp['PACS_password'];
    $details->protocol_type = $row_hosp['PACS_protocol_type'];
    $details->protocol_get_type = $row_hosp['PACS_protocol_get_type'];
    #print_r($details);    
    
    #$bsuccess = $radio_obj->getRadCronLock();
    #if (!$bsuccess) exit();  

    if ($details->protocol_get_type=='ftp'){

        if($ftp_connection=ftp_connect($details->address)){
            if(ftp_login($ftp_connection,$details->username,$details->password)){
                // Set to PASV mode
                // turn passive mode on (1)
                ftp_pasv( $ftp, 1);
            
                #$dir = $details->directory_inbox;
                $dir = 'ftp://'.$details->username.':'.$details->password.'@'.$details->address.'/'.$row_hosp['PACS_folder_path_inbox'].'/';
                echo "<br>Fetching HL7 messages...";
                
                // Open a known directory, and proceed to read its contents
                if (is_dir($dir)) {
                    if ($dh = opendir($dir)) {
                        #$cnt = 0;
                        #scan dir with limiting number
                        $limit = 100;
                        $files = array_slice(scandir($dir), -$limit);

                        #while (($file = readdir($dh)) !== false) {
                        foreach ($files as $key => $file) {
                            if ($file != "." && $file != ".."){
                                #get the file
                                $path_file =  $dir.$file;

                                #check if file exists
                                if (file_exists($path_file)) {
                                    $handle = fopen($path_file, "rb");
                                    if (!$handle) {
                                        echo "<p>Unable to open remote HL7 message file.\n";
                                    }else{
                                        #check only the HL7 file
                                        if (!stristr($file,'.HL7')===FALSE){
                                            $contents = '';
                                            while (!feof($handle)) {
                                              $contents .= fread($handle, 8192);
                                            }
                                            fclose($handle);
                                            
                                            #save to database
                                            #table : seg_hl7_file_received
                                            $details->filename = $file;
                                            $details->hl7_msg = addslashes(trim($contents));
                                            $details->parse_status = 'pending';
                                            $ok = $radio_obj->addInfo_HL7_file_received($details);
                                           
                                            if ($ok){

                                                #partly parse the HL7 message and store the msg to seg_hl7_radio_msg_receipt
                                                $segments = explode($parseObj->delimiter, trim($details->hl7_msg));

                                                $counter_obx = 1;

                                                #set all arrays to null
                                                unset($msh);
                                                unset($msa);
                                                unset($pid);
                                                unset($obr);
                                                unset($obx);
                                                unset($pv1);
                                                unset($orc);
                                                unset($nte);

                                                foreach($segments as $segment) {
                                                    $data = explode('|', trim($segment));

                                                    if (in_array("MSH", $data)) {
                                                        $msh = $parseObj->segment_msh($data);
                                                    }

                                                    if (in_array("MSA", $data)) {
                                                        $msa = $parseObj->segment_msa($data);
                                                    }

                                                    if (in_array("PID", $data)) {
                                                        $pid = $parseObj->segment_pid($data);
                                                    }

                                                    if (in_array("OBR", $data)) {
                                                        $obr = $parseObj->segment_obr($data);
                                                    }

                                                    if(in_array("OBX", $data)){
                                                        $obx[$counter_obx] = $parseObj->segment_obx($data);
                                                        $counter_obx++;
                                                    }

                                                    if(in_array("PV1", $data)){
                                                        $pv1 = $parseObj->segment_pv1($data);
                                                    }

                                                    if(in_array("ORC", $data)){
                                                        $orc = $parseObj->segment_orc($data);
                                                    }

                                                    if(in_array("NTE", $data)){
                                                        $nte = $parseObj->segment_nte($data);
                                                    }
                                                } 

                                                $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr['test']));
                                                $testcode = $arr_test[0];   

                                                $dataarr = array(
                                                    'msg_control_id'=>$msa['msg_control_id'],
                                                    'pacs_order_no'=>$obr['pacs_order_no'],  #as is variable name for $obr['lis_order_no']
                                                    'msg_type_id' =>$msh['msg_type_id'],
                                                    'event_id'=>$msh['event_id'],
                                                    'pid'=>$pid['pid'],
                                                    'test'=>$testcode,
                                                    'hl7_msg'=>$details->hl7_msg,
                                                    'filename'=>$details->filename,
                                                    'date_reported' => date("Y-m-d H:i:s", strtotime($msh['date_reported']))
                                                );
                                                $success1 = $hl7fxnObj->save_hl7_radio_received($dataarr);

                                                if($msh['msg_type_id'] == 'ORU'){
                                                    if($radio_obj->isServed($obr['pacs_order_no'])){
                                                        $findingData = array(
                                                            'batch_nr'=>$obr['pacs_order_no'],  #as is variable name for $obr['lis_order_no']
                                                            'result_findings' => $obx[1]['result_findings'],
                                                            'result_impression' => substr(strstr($obx[1]['result_impression'],"~"), 1),
                                                            // 'physician' => $obr['physician'],
                                                            // 'physician_transcribe' => $obr['physician_transcribe'],
                                                            'date_received' => $obr['date_received'],
                                                            //if preliminary result get doctor at obr-35 else if final result get doctor at obr-32
                                                            'encoder' => ($obr['result_status'] == "F")?$obr['physician']:$obr['physician_transcribe']
                                                        );
                                                
                                                        #save table seg_hl7_hclab_msg_receipt
                                                        $success &= $radio_obj->saveAFindingPacs($findingData);
                                                        
                                                        /*start commented by MARK March 24, 2017
                                                         condition if ($success) if data from PACS manually received */
                                                        #if ($success){
                                                            if($obr['result_status'] == "F")
                                                                $radio_obj->updateRadioRequestStatus($obr['pacs_order_no'], 'done', '0000-00-00', true);
                                                        #}
                                                         /*end commented by MARK March 24, 2017
                                                          condition if ($success) if data from PACS manually received */
                                                       

                                                    }else{
                                                        $success = false;
                                                    }
                                                }elseif($msh['msg_type_id'] == 'ORM'){
                                                    //check if patient has final bill
                                                    if(!$radio_obj->hasFinalBillingByRefno($obr['pacs_order_no'])){
                                                        //uncomment for serving from RIS
                                                        // if($orc["status"] == $parseObj->ORC_STATUS_CHANGE){
                                                        //     if($orc["status_info"] == $parseObj->ORC_SCAN_COMPLETED){
                                                        //         $rad_tech = explode('^', $orc["rad_tech"]);
                                                        //         $rad_tech_name = $rad_tech[2].' '.$rad_tech[1];

                                                        //         $success &= $radio_obj->serveRadioRequestByBatchNr($obr['pacs_order_no'], $rad_tech[0], $rad_tech_name);
                                                        //     }
                                                        // }else
                                                        if($orc["status"] == $parseObj->ORC_CANCEL){
                                                            $history = "Updated status to deleted ".date('Y-m-d H:i:s')." from PACS";
                                                            if(!empty($nte['comment']))
                                                                $history .= $nte['comment'];
                                                            $history .= "\n";

                                                            $success &= $radio_obj->deleteRequestByBatchNr($obr['pacs_order_no'], $history);
                                                        }
                                                    }
                                                }

                                                #flag parsing status to done
                                                if ($success){
                                                    $details->parse_status = 'done'; 
                                                    $hl7fxnObj->radio_update_parse_status($details); 
                                                }

                                                #added by VAS 02/02/2017
                                                if ($success1){
                                                    #delete the file
                                                    unlink($path_file);
                                                    $cnt++;
                                                }

                                            }else{
                                               echo '<br>Fetching of HL7 radiological messages FAILED..';  
                                            }
                                           #================= 
                                        }     
                                                
                                    }    
                                } else {
                                    echo '<br>The file $path_pdf does not exist..';
                                }
                            }    
                        }
                        closedir($dh);
                    }
                }else{
                    echo '<br>Is not a directory..'; 
                }
                
                if ($cnt){
                    echo '<br>HL7 radiological messages were successfully fetched..'; 
                    if ($cnt>1)
                        $label = "files";
                    else
                        $label = "file";        
                        
                    echo "<br>End of fetching HL7 radiological messages. There are $cnt $label fetched...";
                }else
                    echo '<br>No HL7 radiological messages were fetched..';     
               
                
            }#end of if(ftp_login($ftp_connection,$ftp_username,$ftp_password))     
        }#end of if($ftp=ftp_connect($ftp_ip))
       #end if ftp connection 
    }elseif ($details->protocol_get_type=='tcp'){
        echo '<br>No radiological results were fetched..';
    }#end if tcp connection               
    
    #$radio_obj->relRadCronLock();    
?>
