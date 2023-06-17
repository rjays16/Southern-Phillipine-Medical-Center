<?php
	# created by Justin 11/24/2015
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
	require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
	require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_create_hl7_message.php');
	
	class seg_class_pacs_hl7{

		function saveHL7InfoByBatch($batch_nr){
			$radio_obj = new SegRadio;
			$request_list = $radio_obj->getRequestInfoToPassHl7($batch_nr);

			//sends only charge request but allow deleted even if cash
			foreach ($request_list as $value) {
				if($value['is_cash'] == 0){
					$this->saveHL7Info($value['refno'], $value['batch_nr'], $value['service_code'], $value['status']);
				}else{
					if($value['status'] == 'deleted'){
						$this->saveHL7Info($value['refno'], $value['batch_nr'], $value['service_code'], $value['status']);
					}
				}
			}
		}

		function saveCancelHL7Request($batch_nr, $service_code, $is_in_outbox = FALSE){
			$radio_obj = new SegRadio;
			$request_list = $radio_obj->getRequestInfoToPassHl7($batch_nr, $service_code);

			foreach ($request_list as $value) {
				$this->saveHL7Info($value['refno'], $value['batch_nr'], $value['service_code'], 'deleted', $is_in_outbox);
			}
		}

		function saveHL7Info($refno, $batch_nr, $service_code, $status, $is_in_outbox = TRUE){
			global $db;
			$radio_obj = new SegRadio;

			$row_test = $radio_obj->ProcedureInfo($service_code);
			$with_PACS = $row_test['in_pacs'];
    		
    		//check if service is in pacs
    		if($with_PACS){
    			$HL7Obj = new seg_create_msg_HL7();

    			$objInfo = new Hospital_Admin();
    			$row_hosp = $objInfo->getAllHospitalInfo();

    			if ($row_hosp['connection_type'] == 'hl7'){
    				#per procedure
    				$details = $radio_obj->getHL7Info();
    				$fileObj = new seg_create_HL7_file($details);

	    			$COMPONENT_SEPARATOR = $details->COMPONENT_SEPARATOR;
	    			$REPETITION_SEPARATOR = $details->REPETITION_SEPARATOR;

			    	#get the existing msg control id if there is any
			        $hl7msg_row = $radio_obj->isforReplaceHL7Msg($refno, $batch_nr,'XO'); 

			        if ($hl7msg_row['msg_control_id']){
			            $msg_control_id = $hl7msg_row['msg_control_id'];
			            $forreplace = 1;   
			        }else
			            $msg_control_id = $radio_obj->getLastMsgControlID();

			        $details->msg_control_id_db = $msg_control_id;
			        $details->msg_control_id = $details->prefix.$msg_control_id;
	        
			        # Observation order - event O01
			        $msg_type = "ORM";
			        $event_id = "O01";
			        $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
			        $details->msg_type = $hl7_msg_type;    

			        #in UI
					#batch nr = refno
					#refno = batch nr
			        $patient = $radio_obj->getPersonInfoHL7details($refno, $batch_nr);
			        extract($patient); 
	        
			        #pid
			        $details->POH_PAT_ID = trim($patient['pid']);
			    	$details->POH_PAT_ALTID = "";
			        $details->patient_name = mb_strtoupper(trim($patient['name_last'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_first'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_middle']));
			        $details->POH_MIDDLENAME = "";
			    	$details->POH_PAT_DOB = date("YmdHis",strtotime($patient['date_birth']));
			    	$details->POH_PAT_SEX = trim(strtoupper($patient['sex']));
	        
			        $details->address = trim($street_name).$COMPONENT_SEPARATOR.trim($brgy_name).$COMPONENT_SEPARATOR.trim($mun_name).$COMPONENT_SEPARATOR.trim($prov_name).$COMPONENT_SEPARATOR.trim($zipcode);
			        $details->POH_CIVIL_STAT = substr(trim(strtoupper($patient['civil_status'])), 0, 1);

			        #pv1
			        $details->setID = "";

			        $info_enc = $radio_obj->getPersonEncType($encounter_type);
			        extract($info_enc);

			        $details->POH_PAT_TYPE = mb_strtoupper($patient_type);
			        #ward, room and bed

			        $details->location = mb_strtoupper($enctype);
			        $details->requesting_doc =  $request_doctor.$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($doctor_lastname)).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($doctor_firstname)).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($doctor_middlename));

			        $details->POH_PAT_CASENO = trim($encounter_nr);

			        #in UI
					#batch nr = refno
					#refno = batch nr	
			
           			$existhl7msg_row = $radio_obj->isExistHL7Msg($refno, $batch_nr);

		            # NW = New Request ; XO = Replace ; CA = Cancel
		            if ($existhl7msg_row['msg_control_id']){
		            	$filecontent = $existhl7msg_row['hl7_msg'];

		            	if($status == 'deleted'){
		            		if(stristr($filecontent, 'ORC|CA|'))
		                		return false;
		                	else
		                		$order_control = "CA";
	            		}else{
	            			if (stristr($filecontent, 'ORC|NW|'))
	            				return false;
		                	else
		                		$order_control = "NW";
	            		}
		            }else{
	            		if($status == 'deleted'){
		                	$order_control = "CA";
	            		}else{
		                	$order_control = "NW";
	            		}
		            }

	        		$details->order_control = $order_control;

			        #obr
				    #retain the reference no.
				    #$details->POH_ORDER_NO = $refno;
				    $details->POH_ORDER_NO = $batch_nr;

				    #order items
				    #only one service code include so omit the get method and looping
				    /*$result = $radio_obj->getRequestDetailsbyRefnoPACS($refno, $batch_nr);
				    #echo $radio_obj->sql;
				    $count = $radio_obj->FoundRows();

				    while($row_test=$result->FetchRow()){
				    	$service_code = trim($row_test['pacs_code']);

				    	$service .= $service_code.$COMPONENT_SEPARATOR.trim($row_test['name']).$REPETITION_SEPARATOR;
				    }*/
		    
				    #modality OBR
				    $details->modality = $patient['modality'];

				    if (!$details->modality)
				    	$details->modality = 'CR';	
		    
				    #$service_code = trim($row_test['pacs_code']);
				    $service_code = trim($row_test['service_code']);
				    $service_name = preg_replace("/'/", "", $row_test['name']);
				    $service .= $service_code.$COMPONENT_SEPARATOR.trim($service_name).$REPETITION_SEPARATOR;

				    $service = trim($service);
				    $service_list = substr($service,0,strlen($service)-1);
				    $details->service_list = trim($service_list);
	
				    #S:stat request ; R:routine request
				    $details->POH_PRIORITY2 = (trim($priority)?'S':'R');
				    $details->POH_PRIORITY_COMMENT = $comments;
				    $details->POH_TRX_DT =  date("YmdHis");
				    $details->POH_CLI_INFO = addslashes(mb_strtoupper(trim($clinical_info)));
				    $details->doctor = trim($request_doctor).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper(trim($request_doctor_name)));
		    
				    if ($patient['encounter_type']==2)
				       $location1 = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";
				    elseif($patient['encounter_type']==1)
				       $location1 = "ER".$COMPONENT_SEPARATOR."ER"; 
				    elseif (($patient['encounter_type']==3) || ($patient['encounter_type']==4))
				       $location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
				    else
				       $location1 = "WN".$COMPONENT_SEPARATOR."WALKIN";
		    
		    		$details->location_dept = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
		     
				    #remarks or comments 
				    $details->note = addslashes((trim($_POST['comments'])));
	        
				    $msh_segment = $HL7Obj->createSegmentMSH($details);
				    $pid_segment = $HL7Obj->createSegmentPID($details);
				    $pv1_segment = $HL7Obj->createSegmentPV1($details);
				    $orc_segment = $HL7Obj->createSegmentORC($details);
				    $obr_segment = $HL7Obj->createSegmentOBR($details);
				    $nte_segment = $HL7Obj->createSegmentNTE($details);
		     
		    		$filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment."\n".$orc_segment."\n".$obr_segment;

				    if ($details->note)
						$filecontent = $filecontent."\n".$nte_segment;

		    		$file = $details->msg_control_id;
		    
		    		switch ($details->transfer_method){
		    			#FTP (File Transfer Protocol) approach
		        		case "ftp" :
		        			$transportObj = new seg_transport_HL7_file($details);
		                    $transportObj->ftp_transfer($file, $filecontent);
		                    break;
				        #window NFS approach or network file sharing
				        case "nfs" :
		                    #create a file
		                    $filename_local = $fileObj->create_file_to_local($file);
		                    #Thru file sharing
		                    #write a file to a local directory
		                    $fileObj->write_file($filename_local, $filecontent); 
		    
		                    $filename_hclab = $fileObj->create_file_to_hclab($file);
		                    #write a file to a hclab directory   
		                    $fileObj->write_file($filename_hclab, $filecontent); 
		                    unlink($filename_local);
		                    break;
				        #TCP/IP (communication approach)                    
				        case "tcp" :
		                    $transportObj = new seg_transport_HL7_file($details);
		                    
		                    #if ($transportObj->isConnected()){
							#send the message
							$obj = $transportObj->sendHL7MsgtoSocket($filecontent,'pacs');

							#return/print result
							$text = "RIS Server said:: ".$obj;
							#$text = "connected...";
		                    #}else{
		                    #     $text = "Unable to connect to PACS Server. Error: ".$transportObj->error."...";   
		                    #}
		                    echo $text;
		                    break;                    
		    		}

				    #update msg control id
				    $details->msg_control_id = $details->msg_control_id_db;
		    
				    #if new message control id, update the tracker
				    if (!$forreplace)
				        $hl7_ok = $radio_obj->updateHL7_msg_control_id($details->msg_control_id);
		        
				    #HL7 tracker
				    $radio_obj->getInfo_HL7_tracker($details->msg_control_id);
				    $with_rec = $radio_obj->count;
				                                    
				    #$details->pacs_order_no = $refno;
				    $details->pacs_order_no = $batch_nr;
				    $details->msg_type = $msg_type;
				    $details->event_id = $event_id;
				    $details->refno = $refno;
				    $details->batch_nr = $batch_nr;
				    $details->pid = $pid;
				    $details->encounter_nr = $encounter_nr;
				    $details->hl7_msg =  $filecontent;

				    if ($with_rec){
				        $hl7_ok = $radio_obj->updateInfo_HL7_tracker($details);
				    }else{
				        $hl7_ok = $radio_obj->addInfo_HL7_tracker($details);
				    }

				    $radio_obj->updateIsInOutbox($batch_nr, $is_in_outbox);
				}#-------------------------------PACS $row_hosp['connection_type']=='hl7'
			}//end of if($with_PACS)
		}//end of function saveHL7Info
    }
    #------- end of class--------
?>
