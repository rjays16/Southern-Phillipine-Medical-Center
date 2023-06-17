<?php
    #for cron schedule
    #per minute
	# created by VAN 01-12-2012
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
    $parseObj = new seg_parse_msg_HL7();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
    $hl7fxnObj = new seg_HL7();

    # Create hl7 object
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_message.php');
    $HL7Obj = new seg_create_msg_HL7();

    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
                
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');

    require_once($root_path.'modules/laboratory/seg-lab-report-hl7.php');

    include_once($root_path.'include/care_api_classes/class_globalconfig.php');

    $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
    $glob_obj->getConfig('max_limit_fetch_labresult');
    $max_limit_fetch_labresult=$GLOBAL_CONFIG['max_limit_fetch_labresult'];
    
    #$row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    $details->address = $row_hosp['LIS_address'];
    $details->username = $row_hosp['LIS_username'];
    $details->password = $row_hosp['LIS_password'];
    $details->protocol_type = $row_hosp['LIS_protocol_type'];
    $details->protocol_get_type = $row_hosp['LIS_protocol_get_type'];

    #$bsuccess = $srvObj->getLabCronLock();
    #if (!$bsuccess) exit();

    if ($details->protocol_get_type=='ftp'){
        if($ftp_connection=ftp_connect($details->address)){
            if(ftp_login($ftp_connection,$details->username,$details->password)){
                // Set to PASV mode
                // turn passive mode on (1)
                ftp_pasv( $ftp, 1);
            
                #$dir = $details->directory_inbox;
                $ftp_dir = 'ftp://'.$details->username.':'.$details->password.'@'.$details->address;
                
                $dir = $ftp_dir.'/'.$row_hosp['LIS_folder_path_inbox'].'/';
                #$archives = $ftp_dir.'/'.$row_hosp['LIS_folder_path_archives'].'/';
                
                echo "<br>Fetching HL7 messages...";
                
                // Open a known directory, and proceed to read its contents
                if (is_dir($dir)) {
                        #$cnt = 0;
                        #scan dir with limiting number
                        #max_limit_fetch_labresult
                        $limit = $max_limit_fetch_labresult;
                        $files = array_slice(scandir($dir), -$limit);
                        
                        #while (($file = readdir($dh)) !== false) {
                        foreach ($files as $key => $file) {
                            
                            #if ($file != "." && $file != ".." && $cnt < $limit){
                            if ($file != "." && $file != ".."){
                                #get the file
                                $path_file =  $dir.$file;
                                #check if file exists
                                if (file_exists($path_file)) {

                                    $today = date('Y-m-d H:i:s');
                                    
                                    #$new_path_file = $archives.$file;
                                    $handle = fopen($path_file, "rb");

                                    #edited by VAN 09-01-2016
                                    #Open the file to get existing content
                                    $details->content_file = file_get_contents($file);

                                    #added by VAN 09-28-2016
                                    $contents = '';
                                    while (!feof($handle)) {
                                      $contents .= fread($handle, 8192);
                                    }
                                    fclose($handle);

                                    $details->hl7_msg = addslashes(trim($contents));
                                    
                                    $details->filename = $file;
                                    
                                    #added by VAN 08-15-2016
                                    #move the files to another location after getting the content
                                    #to overwrite, delete the old one
                                    $details->lis_order_no = '';
                                    $details->testcode = '';
                                    /*if(file_exists($new_path_file)) { 
                                        unlink($new_path_file);
                                        $ok = rename($path_file, $new_path_file);

                                    } else { 
                                        $ok = rename($path_file, $new_path_file); 
                                    }*/

                                    #---------------------

                                    if (!$handle) {
                                        /*echo "<p>Unable to open remote HL7 message file.\n";
                                        $details->message = 'Unable to open remote HL7 message file';
                                        $srvObj->addtoLogs($details);*/ 
                                    }else{
                                        #check only the HL7 file
                                        if (!stristr($file,'.HL7')===FALSE){
                                            
                                            #save to database
                                            #table : seg_hl7_file_received
                                            $details->filename = $file;
                                            $details->hl7_msg = addslashes(trim($contents));
                                            $details->parse_status = 'pending';
                                            $details->date_received = $today;
                                            #$ok = $srvObj->addInfo_HL7_file_received($details);
                                            #always set to TRUE, easier to do than remove the condition rather than looking for the end }
                                            $ok = 1;
                                           
                                            if ($ok){

                                                #partly parse the HL7 message and store the msg to seg_hl7_hclab_msg_receipt
                                                $segments = explode($parseObj->delimiter, trim($details->hl7_msg));

                                                #set all arrays to null
                                                unset($msh);
                                                unset($msa);
                                                unset($pid);
                                                unset($obr);

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
                                                } 

                                                $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr['test']));
                                                $testcode = $arr_test[0];   

                                                $details->filename_his = $details->filename."_".$pid['pid']."_".$obr['lis_order_no']."_".$testcode;

                                                $dataarr = array
                                                            (
                                                                'msg_control_id'=>$msa['msg_control_id'],
                                                                'lis_order_no'=>$obr['lis_order_no'],
                                                                'msg_type_id' =>$msh['msg_type_id'],
                                                                'event_id'=>$msh['event_id'],
                                                                'pid'=>$pid['pid'],
                                                                'test'=>$testcode,
                                                                'hl7_msg'=>$details->hl7_msg,
                                                                'filename'=>$details->filename,
                                                                'filename_his'=>$details->filename_his,
                                                                'date_created' => $details->date_received,
                                                            );

                                                #save table seg_hl7_hclab_msg_receipt
                                                $success = $hl7fxnObj->save_hl7_received($dataarr);
                                                #echo "<br><br> receipt = ".$hl7fxnObj->sql."<br><br>";
                                                
                                                #$success = 1;
                                                #flag parsing status to done
                                                if ($success){
                                                    
                                                    $details->parse_status = 'done'; 
                                                    #commented out by VAS 01/11/2017
                                                    #no more logs
                                                    #$hl7fxnObj->update_parse_status($details);

                                                    $refno = $srvObj->getLISOrderNo($obr['lis_order_no']);
                                                    #update the request if served and if there is a result
                                                    $date_served = date("Y-m-d h:i:s");
                                                    
                                                    $dirpath = $row_hosp['LIS_folder_path_pdf_dms'];
                                                    
                                                    $pid = $pid['pid'];
                                                    $lis = $obr['lis_order_no'];

                                                    #added by VAN 01-09-2015
                                                    #update the request if served and if there is a result
                                                    #commented by VAN 11/14/2016 ; redundant code $refno = $srvObj->getLISOrderNo($obr['lis_order_no']);
                                                    #$refno = $srvObj->getLISOrderNo($lis);
                                                    $date_served = date("Y-m-d h:i:s");

                                                    #get patient type
                                                    $patient_type = $srvObj->getPatientTypebyLoc($refno);

                                                    $service_code = $srvObj->getTestCodebyLoc($refno, $patient_type, $testcode);
                                                    $srvObj->DoneLabTestRequest($refno, $service_code, $date_served); 
                                                    
                                                    if ($lis){

                                                        $rs = $db->Execute("SELECT t.encounter_nr, refno, e.parent_encounter_nr
                                                                            FROM seg_hl7_lab_tracker t
                                                                            LEFT JOIN care_encounter e ON e.encounter_nr=t.encounter_nr
                                                                            WHERE e.encounter_type IN ('3','4') 
                                                                            AND lis_order_no = ? LIMIT 1", $lis);

                                                        if($rs){
                                                            $row = $rs->FetchRow();
                                                            $enc = $row['encounter_nr'];
                                                            $refno = $row['refno'];
                                                            $er_enc = $row['parent_encounter_nr'];
                                                        }else{
                                                            $enc = 0;
                                                            $refno = 0;
                                                        }

                                                        } 

                                                    #delete physical file located at the LIS server if successfully save in database seg_hl7_hclab_msg_receipt
                                                    unlink($path_file);      
                                                }else{
                                                    echo "<br><br>".$path_file." is still in the Inbox folder. It can't be deleted since it was not successfully save in HIS server.<br>";
                                                }

                                                #delete physical file located at the LIS server
                                                #unlink($path_file);
                                                $cnt++;
                                            }
                                        }//(!stristr($file,'.HL7')===FALSE)     
                                               
                                    }    
                                }
                            }    
                        }
                    #    closedir($dh);
                    #}
                }else{
                    echo '<br>Is not a directory..'; 
                    $details->message = 'Is not a directory..';
                    #$srvObj->addtoLogs($details);
                }
                
                if ($cnt){
                    echo '<br>HL7 Laboratory messages were successfully fetched..'; 
                    if ($cnt>1)
                        $label = "files";
                    else
                        $label = "file";        
                        
                    echo "<br>End of fetching HL7 lab messages. There are $cnt $label fetched...";
                }else{
                    echo '<br>No HL7 laboratory messages were fetched..';  
                    $details->message = 'No HL7 laboratory messages were fetched..'; 
                    #$srvObj->addtoLogs($details);  
                }
               

            }#end of if(ftp_login($ftp_connection,$ftp_username,$ftp_password))     
        }#end of if($ftp=ftp_connect($ftp_ip))
       #end if ftp connection 
    }elseif ($details->protocol_get_type=='tcp'){
        echo '<br>No laboratory results were fetched..';
    }#end if tcp connection               
    
    #$srvObj->relLabCronLock();
?>
