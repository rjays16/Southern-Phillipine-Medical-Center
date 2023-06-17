<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-schedule-common.php');

require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
include_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_department.php');
#$dept_obj=new Department;
include_once($root_path.'include/care_api_classes/class_personell.php');
#$pers_obj=new Personell;
require_once($root_path.'include/care_api_classes/class_radiology.php');
#$objService = new SegRadio;

require_once($root_path.'include/care_api_classes/class_tabview.php');
require($root_path.'include/care_api_classes/class_discount.php');

#added by VAN 06-03-2013
require_once($root_path.'include/care_api_classes/class_encounter.php');

#added by JESTHER
require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');

//for EMR 7/27/2015
require_once($root_path . 'include/care_api_classes/emr/services/RadiologyEmrService.php');

require_once($root_path.'include/care_api_classes/class_ward.php');

#added by VAN 06-16-2014
#for HL7 compliant
# Create hl7 object
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_create_hl7_message.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

// added by carriane 03/16/18
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);
// end carriane

define('Walkin_enc',5); #Added by Mats 04142020

#-------added by VAN 03-26-08
function populateScheduledList($sElem, $tbId, $searchkey, $page,$ob){
	global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv = new SegRadio();
		$enc_obj=new Encounter;
		$personell_obj = new Personell;
        
        $offset = $page * $maxRows;

		$searchkey = utf8_decode($searchkey);

		#if ($searchkey==NULL)
		#	$searchkey = 'now';

        #get dept
        $sub_dept_nr = substr($tbId,4);

        #$objResponse->alert('aj = '.$sub_dept_nr);
		#$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset);
		#$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,1);
        if ($searchkey){
            $ergebnis=$srv->SearchSelect($searchkey, $sub_dept_nr,$maxRows,$offset,$condition,$ob);
            // $objResponse->addAlert($srv->sql);
		    // $objResponse->addAlert($ob);
		    #$total = $srv->count;
            $total = $srv->FoundRows();
        }else{
            $ergebnis = false;
            $total = 0;
        }
		#$objResponse->addAlert('total = '.$total);
		$lastPage = floor($total/$maxRows);
		#$objResponse->addAlert('total = '.floor($total%10));
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		#$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,0);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		#$objResponse->addAlert("pageno, lastpage, pagen, total = ".$page.", ".$lastPage.", ".$maxRows.", ".$total);
		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList",$tbId);
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				if ($result["pid"]!=" ")
					$name = trim($result["name_first"])." ".trim($result["name_middle"])." ".trim($result["name_last"]);
				else
					$name = trim($result["ordername"]);

				if (!empty($result['modify_id'])){
					$scheduled_by = trim($result['modify_id']);
				}else{
					$scheduled_by = trim($result['create_id']);
				}

				#added by VAN 06-17-08
				#$sked_time = date("h:i A",strtotime(trim($result["scheduled_time"])));

				#added by VAN 07-08-08
				if (trim($result["scheduled_dt"]))
					$sked_date = date("m/d/Y",strtotime(trim($result["scheduled_dt"])));
				else
					#$sked_date = date("m/d/Y");
                    $sked_date = date("m/d/Y",strtotime(trim($result["request_date"])));

				if (trim($result["scheduled_time"]))
					$sked_time = date("h:i A",strtotime(trim($result["scheduled_time"])));
				else
					#$sked_time = date("h:i A");
                    $sked_time = date("h:i A",strtotime(trim($result["request_time"])));

				if (empty($scheduled_by)){
					if (!empty($result['encoder'])){
						$scheduled_by = trim($result['encoder']);
					}else{
						$scheduled_by = trim($result['encoder2']);
					}
				}

				if($ob){
					$result["dept_short_name"] = 'UCW';
				}

		$temp_encoder = $personell_obj->getUserFullName($scheduled_by);

		if($temp_encoder != false)
			$encoder = strtoupper($temp_encoder);
		else
			$encoder = strtoupper($scheduled_by);
				#-----------------

				// $objResponse->addAlert("type = ".$result['encounter_type']);

				if ($result['encounter_type']==1){
					$pat_type = "ERPx";
				}
				elseif ($result['encounter_type']==2 ||$result['encounter_type']==IPBMOPD_enc){
					if($result['encounter_type']==IPBMOPD_enc)
						$pat_type = "OPDPx (IPBM)";
					else
					$pat_type = "OPDPx";
				}
				elseif (($result['encounter_type']==3)||($result['encounter_type']==4)||($result['encounter_type']==IPBMIPD_enc)){
					if($result['encounter_type']==IPBMIPD_enc)
						$pat_type = "INPx (IPBM)";
					else	
					$pat_type = "INPx";
				}
				elseif ($result['encounter_type']==6){
					$pat_type = "Industrial Clinic";
				}
				elseif($result["encounter_type"]==Walkin_enc){
					$pat_type = "Walkin";
				}
				#--------------------
				#$objResponse->addAlert("type = ".$result["batchnum"]);
				#$objResponse->addAlert("refno, name, code, sked_date = ".trim($result["batch_nr"]).", ".$name.", ".trim($result["service_code"]).", ".trim($result["scheduled_dt"]).", ".trim($result["scheduled_time"]));
				#refnum
				#$objResponse->addScriptCall("addPerson","RequestList",trim($result["batch_nr"]),$name,trim($result["service_code"]),trim($result["serv_name"]),trim($result["scheduled_dt"]),$sked_time,trim($result["name_formal"]),trim($result["rid"]),$scheduled_by, trim($result["skstatus"]),trim($result["dept_short_name"]),$pat_type);
				$disabled_icon = 0;
				// if($result['fromdept']=='OBGUSD'){
				// 	if (($result["is_cash"]==1) && ($result["request_flag"]!='paid' && $result["request_flag"]!='cmap' && $result["request_flag"]!='lingap'))
    //                 $disabled_icon = 1;

				// }else{
					if (($result["is_cash"]==1) && ($result["hasPaid"]==0))
                    $disabled_icon = 1;


				// }
                
                #get encounter info
                $bill = (object) 'bill';
                $billinfo = $enc_obj->hasSavedBilling($result['encounter_nr']);
                if ($billinfo){
                    $bill->bill_nr = $billinfo['bill_nr'];
                    $bill->hasfinal_bill = $billinfo['is_final'];
                    $bill->is_maygohome = $result['is_maygohome'];
                    $bill->is_cash = $result['is_cash'];
                }    
                    
                $objResponse->addScriptCall("addPerson",$tbId, trim($result["refnum"]),$result["batchnum"],ucwords(strtolower($name)),trim($result["service_code"]),trim($result["serv_name"]),$sked_date,$sked_time,trim($result["name_formal"]),trim($result["rid"]),$encoder, trim($result["skstatus"]),trim($result["dept_short_name"]),$pat_type, $result["is_served"], $disabled_icon, $bill);
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson",$tbId,NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
}

function deleteScheduledRadioRequest($refno){
		global $db;
		$srv = new SegRadio;
		$objResponse = new xajaxResponse();

		if ($srv->deleteRadioSchedule($refno)) {
			$objResponse->addScriptCall("removeSkedRequest",$refno);
			$objResponse->addAlert("The scheduled request is successfully deleted.");
		}else{
			$objResponse->addAlert("The scheduled request is failed deleted.");
		}
		#$objResponse->addAlert("sql = ".$srv->sql);
		return $objResponse;
	}

#--------------------------------------

#added by VAN 10-07-2014
#PACS
#hl7 message
function saveHL7Info($objResponse, $refno, $batch_nr, $service_code, $is_served){
	global $db;

	$radio_obj = new SegRadio;
	$dept_obj=new Department;
	$ward_obj = new Ward;

	$row_test = $radio_obj->ProcedureInfo($service_code);
    $with_PACS = $row_test['in_pacs'];
    
    if ($with_PACS){
	
		$HL7Obj = new seg_create_msg_HL7();
	    
		$objInfo = new Hospital_Admin();
	    $row_hosp = $objInfo->getAllHospitalInfo();
	       	
	    if ($row_hosp['connection_type']=='hl7'){
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

	        #$details->location = mb_strtoupper($enctype);
	        $details->requesting_doc =  $request_doctor.$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($doctor_lastname)).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($doctor_firstname)).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($doctor_middlename));

	        $details->POH_PAT_CASENO = trim($encounter_nr);

	        #in UI
			#batch nr = refno
			#refno = batch nr	
			
            $existhl7msg_row = $radio_obj->isExistHL7Msg($refno, $batch_nr);

            # NW = New Request ; XO = Replace ; CA = Cancel
            if ($is_served){ 

	            if ($existhl7msg_row['msg_control_id']){
	                $filecontent = $existhl7msg_row['hl7_msg'];
	                if (stristr($filecontent, 'ORC|NW|')){
	                    $order_control = "XO";
	                }elseif (stristr($filecontent, 'ORC|CA|')){
	                    $order_control = "NW";
	                }else
	                    $order_control = "XO";    
	            
	            }else    
	                $order_control = "NW";
	        }else        
	        	$order_control = "CA";    

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
		    
		    if ($patient['encounter_type']==2 || $patient['encounter_type']==IPBMOPD_enc){

		    	if($patient['encounter_type']==IPBMOPD_enc)
		       		$location1 = "OPD (IPBM)".$COMPONENT_SEPARATOR."OUTPATIENT";
		       	else
		       		$location1 = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";

               	$loc_code = $patient['current_dept_nr'];

			   	if ($loc_code)
					$dept = $dept_obj->getDeptAllInfo($loc_code);

			   	$loc_name = stripslashes($dept['name_formal']);
		    }
		    elseif($patient['encounter_type']==1){
		       	$location1 = "ER".$COMPONENT_SEPARATOR."ER"; 

                   $loc_code = "ER";
			   	$erLoc = $dept_obj->getERLocation($patient['er_location'], $patient['er_location_lobby']);
			   	
			   	if ($erLoc['area_location'] && $erLoc['lobby_name']){
			   		$loc_name = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
			   	}else{
			   		$loc_name = "ER";
			   	}

		    }
		    elseif (($patient['encounter_type']==3) || ($patient['encounter_type']==4) || ($patient['encounter_type']==IPBMIPD_enc)){
		       	if($patient['encounter_type']==IPBMIPD_enc)
			       	$location1 = "IPD (IPBM)".$COMPONENT_SEPARATOR."INPATIENT";
		       	else
			       	$location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";

               	$loc_code = $patient['current_ward_nr'];
			   	if ($loc_code)
			   		$ward = $ward_obj->getWardInfo($loc_code);

			   	$loc_name = stripslashes($ward['name']); 
		    }
		    else{
	       		$location1 = "WN".$COMPONENT_SEPARATOR."WALKIN";
		       	$loc_code = "WN";
		       	$loc_name = "WN";
		    }
		    
		    $details->location_dept = mb_strtoupper($loc_code).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name);
		    $details->location = $details->location_dept; 
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
		}#-------------------------------PACS $row_hosp['connection_type']=='hl7'
	}	
	
}	

#added by VAN 08-14-2012
function savedServedPatient($refno, $batch_nr, $service_code, $is_served, $rad_tech=0, $served_date='', $served_time=''){
    global $db, $HTTP_SESSION_VARS;

    $objResponse = new xajaxResponse();
    $radio_obj = new SegRadio;

    if ($is_served){
        #$date_served = date("Y-m-d H:i:s");
        $date = $served_date.' '.$served_time;
        $date_served = date("Y-m-d H:i:s", strtotime($date));
        if($rad_tech==0){
        	$rad_tech = $HTTP_SESSION_VARS['sess_login_personell_nr'];
        }
        else{
        	$rad_tech  = $rad_tech;	
        }
    }else{
        $date_served = '0000-00-00 00:00:00';
        $rad_tech = 0;
    }



    #edit the $batch_nr, $refno to avoid confusion
    $save = $radio_obj->ServedRadioRequest($refno, $batch_nr, $service_code, $is_served, $date_served, $rad_tech);
    #$objResponse->alert("sql = ".$radio_obj->sql);

    if ($save){
        //for EMR 7/27/2015
        try {

        	//EHR is_served ==================================================>

        	$sql = "SELECT encounter_nr from seg_radio_serv WHERE refno=".$db->qstr($batch_nr);
        	$getEncounter = $db->GetOne($sql);
            $itemLists = array();
            // $requestinfo = $srvObj->getLabServiceReqInfo($refno);
            $itemRaw = array(
                "service_id"    => $service_code,
                "is_served"     => $is_served,
                "date_modified" => $date
            );

            array_push($itemLists, $itemRaw);
             
            $data = array(
            	"refno" => $batch_nr,
                "encounter_nr"  =>  $getEncounter,
                "items"         =>  $itemLists
            ); 
            
            $ehr = Ehr::instance();
            $response = $ehr->postServeRadRequest($data);
            $asd = $ehr->getResponseData();
            $EHRstatus = $response->status;
            // var_dump($$is_served); die();
            //EHR is_served ==================================================>

            $radService = new RadiologyEmrService();
            #add new argument to detect if to update patient demographic or not
            $radService->saveRadRequest($batch_nr, 1);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }
        //end EMR

        if ($is_served){
        	#in UI
			#batch nr = refno
			#refno = batch nr
			saveHL7Info(&$objResponse, $batch_nr, $refno, $service_code, $is_served);
			
            $objResponse->addScriptCall("closeWindow");
        }else{
        	
        	$existhl7msg_row = $radio_obj->isExistHL7Msg($batch_nr, $refno);
        	
        	if ($existhl7msg_row['msg_control_id']){
        		#in UI
				#batch nr = refno
				#refno = batch nr
				saveHL7Info(&$objResponse, $batch_nr, $refno, $service_code, $is_served);
	        }

            $objResponse->addScriptCall("ReloadWindow");
    }

    }

    return $objResponse;

}

$xajax->processRequests();
?>
