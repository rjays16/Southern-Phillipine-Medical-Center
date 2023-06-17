<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');

#added by VAN 06-18-2014
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');

#added rnel
require_once($root_path.'frontend/bootstrap.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
#end rnel


# Create person object
#include_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path.'modules/radiology/ajax/radio-request-list.common.php');

#define('MAX_BLOCK_ROWS',30);
define('MAX_BLOCK_ROWS',10);
define('IPBMOPD', 14);
define('IPBMIPD', 13);


	function deleteRadioServiceRequest($ref_nr){
		global $root_path; global $db;
		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;



		 require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
        $service_id = array();
        $sql = "SELECT service_code from care_test_request_radio WHERE refno='{$ref_nr}'";
        $encounter_nr = $db->GetOne("SELECT encounter_nr from seg_radio_serv WHERE refno='{$ref_nr}'");
        $result=$db->Execute($sql);

        while($service_code=$result->FetchRow()) {
            array_push($service_id, $service_code);
        } 

        $data = array(
            "encounter_nr"  =>  $encounter_nr,
            "items"         =>  $service_id
        ); 
        
        $ehr = Ehr::instance();
        $response = $ehr->postRemoveRadRequest($data);
        $asd = $ehr->getResponseData();
        $EHRstatus = $response->status;

		// $objResponse->addAlert((print_r($asd)));
  //       return $objResponse;

        if(!$EHRstatus){
            // var_dump($asd);
            // var_dump($patient->msg);
            // die();
        }
		#add rnel get the rad info before the bulk deletion use in notification
		$dataRadDelInfo = $radio_obj->getRadInfoForBatchDeleteNotification($ref_nr);

#$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : ref_nr='$ref_nr'");
		if ($radio_obj->deleteRefNo($ref_nr)){
#			$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : Successfully deleted! ");
                        try {
	            require_once($root_path . 'include/care_api_classes/emr/services/RadiologyEmrService.php');
	            $radService = new RadiologyEmrService();
	            $radService->deleteRadRequest($ref_nr);
	        } catch (Exception $exc) {
	            // echo $exc->getTraceAsString();die;
	        }
	       
			$objResponse->addScriptCall("jsOnClick");
			
			/*
			#hl7 message
			$objInfo = new Hospital_Admin();
            $row_hosp = $objInfo->getAllHospitalInfo();

			#added by VAN 06-18-2014
			if ($row_hosp['connection_type']=='hl7'){
                    #validate if there a PACS posted request
                    $hl7_row = $radio_obj->isExistHL7Msg($ref_nr);
                    if ($hl7_row['msg_control_id']){
                        #update HL7 message tracker
                        $row_comp = $objInfo->getSystemCreatorInfo();
        
                        $details->protocol_type = $row_hosp['PACS_protocol_type'];
					    $details->protocol = $row_hosp['PACS_protocol'];
					    #as is the name of the variable, dont change!!!
					    #$details->address_lis
					    #$details->folder_LIS
					    #$details->directory_LIS
					    
					    $details->address_lis = $row_hosp['PACS_address'];
					    $details->address_local = $row_hosp['PACS_address_local'];
					    $details->port = $row_hosp['PACS_port'];
					    $details->username = $row_hosp['PACS_username'];
					    $details->password = $row_hosp['PACS_password'];
					    
					    $details->folder_LIS = $row_hosp['PACS_folder_path'];
					    #PACS SERVER IP
					    $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['PACS_folder_path'];
					    #HIS SERVER IP
					    $details->directory = "\\\\".$details->address_local.$row_hosp['PACS_folder_path'];
					    #HIS SERVER IP
					    $details->directory_local = "\\\\".$details->address_local.$row_hosp['PACS_folder_path_local'];
					    #same with PACS extension
					    $details->extension = $row_hosp['PACS_HL7_extension']; 
					    $details->service_timeout = $row_hosp['PACS_service_timeout'];    #timeout in seconds
					    $details->directory_PACS = "\\\\".$details->address_pacs.$row_hosp['PACS_folder_path_inbox'];
					    $details->hl7extension = ".".$row_hosp['PACS_HL7_extension'];
                        
                        $transfer_method = $details->protocol_type;    
            
                        #msh
                        $details->system_name = trim($row_comp['system_id']);
                        $details->hosp_id = trim($row_hosp['hosp_id']);
                        #as is lis_name, don't change the variable, for third party software provider
    					$details->lis_name = trim($row_comp['pacs_name']);
                        $details->currenttime = strftime("%Y%m%d%H%M%S");
                        
                        $fileObj = new seg_create_HL7_file($details);
                            
                        $order_control = "CA";
                        $hl7msg_row = $radio_obj->isforReplaceHL7Msg($ref_nr,$order_control); 
                        
                        if ($hl7msg_row['msg_control_id']){
                            $msg_control_id = $hl7msg_row['msg_control_id'];
                            $forreplace = 1;   
                        }else
                            $msg_control_id = $radio_obj->getLastMsgControlID();
                        
                        $prefix = "HIS";
                        
                        #replace NW or RP to CA
                        $filecontent = $hl7_row['hl7_msg'];
                        #search for the string NW or RP in the message
                        if (!stristr($filecontent, 'ORC|NW|') === FALSE){
                            #replace NW to CA
                            $filecontent = str_replace("ORC|NW|", "ORC|CA|", $filecontent);
                        }elseif (!stristr($filecontent, 'ORC|XO|') === FALSE){
                            #replace RP to CA
                            $filecontent = str_replace("ORC|XO|", "ORC|CA|", $filecontent);
                        }    
                        
                        $details->msg_control_id_db = $msg_control_id;
                        $details->msg_control_id = $prefix.$msg_control_id;
                        
                        $details->order_control = $order_control;
                        
						$file = $details->msg_control_id;
                        
                        #create a file
                        $filename_local = $fileObj->create_file_to_local($file);
                                                        
                        #Thru file sharing
                        #write a file to a local directory
                        $fileObj->write_file($filename_local, $filecontent);
                        
                        switch ($transfer_method){
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
                                             $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
                                             
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
                        $details->pacs_order_no = $ref_nr;
                        $details->msg_type = $hl7_row['msg_type'];
                        $details->event_id = $hl7_row['event_id'];
                        $details->refno = $ref_nr;
                        $details->pid = $hl7_row['pid'];
                        $details->encounter_nr = $hl7_row['encounter_nr'];
                        $details->hl7_msg =  $filecontent;
                                                    
                        if ($forreplace){
                            $hl7_ok = $radio_obj->updateInfo_HL7_tracker($details);
                        }else{
                            $hl7_ok = $radio_obj->addInfo_HL7_tracker($details);
                        }
                    }    
                    #--------------------------        
                }
			#-------------------------------*/

			$objResponse->addScriptCall("msgPopUp","Successfully deleted!");

			#added rnel rad batch delete notification message
			$personell_obj = new Personell;
			$personnel = $personell_obj->get_Person_name2($_SESSION['sess_login_personell_nr']);

			$data = array();
			$radInfo = array();

			foreach ($dataRadDelInfo as $datum) {
				# code...
				$radInfo['ordername'] = $datum['ordername'];
				$radInfo['items'][] = $datum['service_code'];
			}
			$data = array(
				'pname' => $radInfo['ordername'],
				'items' => $radInfo['items'],
				'personnel' => $personnel['name_first'] . ' ' . $personnel['name_last']
			);

			#publish data
			$radio_obj->notifRadMessageBulkDeletion($data);
			#end rnel


		}else{
#			$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : Failed to deleted! ");
			$objResponse->addScriptCall("msgPopUp","Failed to delete!");
		}
#		$objResponse->addAlert("radio-finding.server.php : deleteRadioServiceRequest : radio_obj->sql = '".$radio_obj->sql."' ");
		return $objResponse;
	}#end of function deleteRadioServiceRequest


#function PopulateRadioRequest($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir ){
function PopulateRadioRequest($tbId, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir, $mod=0, $patient_type = 0,$ob){
	global $root_path;
	$objResponse = new xajaxResponse();	
	
	// $objResponse->addAlert("ajax : tbid =".$tbId. "\n tbody = ".$tbody."\n searchkey = ".$searchkey."\n sub_dept_nr=".$sub_dept_nr."\n pgx=".$pgx."\n thisfile=".$thisfile." \n rpath= ".$rpath."\n mode=".$mode."\n oitem=".$oitem."\n odir=". $odir);
	// $objResponse->addAlert($ob);
	//Display table header 
	RadioRequestHeader($objResponse,$tbId,$sub_dept_nr,$oitem, $odir);
		
	//Paginate & display list of radiology request
	#PaginateRadioRequestlist($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem);
	
	PaginateRadioRequestlist($objResponse, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem,$mod, $patient_type,$ob);
	
	return $objResponse;
}//end of PopulateRadioRequest


function RadioRequestHeader(&$objResponse,$tbId, $sub_dept_nr, $oitem, $odir){

	$tr  = "<thead>";
	$tr .= "<tr><th colspan=\"15\" id=\"mainHead".$sub_dept_nr."\"></th></tr>";
	$tr .= "<tr>";
	$tr .= "<th width=\"5%\"></th>";
	$tr .= makeSortLink('Batch No','refno', $oitem, $odir, $sub_dept_nr,'10%');
	$tr .= makeSortLink('RID','rid', $oitem, $odir, $sub_dept_nr,'7%', 'center');
	$tr .= makeSortLink('Name','ordername', $oitem, $odir, $sub_dept_nr,'45%', 'left');
	$tr .= "<th width=\"10%\">Hosp. No.</th>";
	$tr .= "<th width=\"5%\">Age</th>";
	$tr .= "<th width=\"7%\">Bdate</th>";
	$tr .= "<th width=\"5%\">Type</th>";
	$tr .= "<th width=\"10%\">Location</th>";
	$tr .= makeSortLink('Date Requested','request_date', $oitem, $odir, $sub_dept_nr,'10%');
	$tr .= "<th width=\"10%\">OR No.</th>";
	$tr .= "<th width=\"10%\">Priority</th>";  		
	#$tr .= "<th width=\"10%\">Status</th>";
	$tr .= "<th width=\"5%\">Details</th>";
	$tr .= "<th width=\"5%\">Delete</th>";	
	$tr .= "</tr>";
	$tr .= "</thead> \n";
		
	$tbody="<tbody id=\"TBodytab".$sub_dept_nr."\"></tbody>";
#	$prevNextTR = "<tr><td id=\"prevRow\" colspan=\"6\"></td>";
#	$prevNextTR .=    "<td id=\"nextRow\" align=right></td></tr>";
	
#	$HTML = $tr.$tbody.$prevNextTR;
	$HTML = $tr.$tbody;
    
	#$objResponse->addAlert("item=".$item."\n oitem=".$oitem."\n odir=".$odir."\n sub_dept_nr=".$sub_dept_nr);
	// $objResponse->addAlert("tbId=".$tbId);
	$objResponse->addAssign($tbId,"innerHTML",$HTML);				
	
} // end of RadioRequestHeader

function makeSortLink($txt='SORT',$item, $oitem,$odir='ASC', $subDeptNr='', $width='', $align='center'){
	if($item == $oitem){
		if($odir == 'ASC'){
			$img = "<img src=\"../../gui/img/common/default/arrow_red_up_sm.gif\">";
		}else{
			$img = "<img src=\"../../gui/img/common/default/arrow_red_dwn_sm.gif\">";
		}
	}else{
		$img='&nbsp;';
	}
	
	if($odir=='ASC') $dir ='DESC';
	else $dir = 'ASC';
											 #jsSortHandler(items, oitem, dir, sub_dept_nr)			
	$td = "<th width=\"".$width."\" align=\"".$align."\" onClick=\"jsSortHandler('$item', '$oitem','$dir', '$subDeptNr');\">".$img."<b>".$txt."</b></th> ";
	
	return $td;
} // end of function makeSortLink

function PaginateRadioRequestlist(&$objResponse, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $odir='ASC', $oitem='create_dt', $mod=0, $patient_type = 0,$ob){
	global $date_format;
	global $db;
	$objRadio = new SegRadio();
	$dept_obj=new Department;
	$ward_obj = new Ward;
	
	#Instantiate paginator 
	$pagen = new Paginator($pgx, $thisfile, $searchkey, $rpath, $oitem, $odir);
	// $objResponse->addAlert($ob);
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	# Last resort, use the default defined at the start of this page
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); 
    else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
		// $objResponse->addAlert($ob);
#$pagen->setMaxCount(MAX_BLOCK_ROWS); 
#$objResponse->addAlert('searchkey = '.$searchkey);
#	if(($mode == 'search' || $mode == 'paginate') && !empty($searchkey)){
		$searchkey = strtr($searchkey, '*?', '%_');
#	}

	$ergebnis = &$objRadio->searchLimitBasicInfoRadioRefNo($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir, $mod, $patient_type,$ob);
	#$objResponse->addAlert("PaginateRadioRequestlist:: SQL objRadio->sql = ".$objRadio->sql);
	$linecount = $objRadio->LastRecordCount();
	#$linecount = $total;
	$pagen->setTotalBlockCount($linecount);
	
	#$totalcount = $total;
	if(isset($totalcount)&& $totalcount){
		$pagen->setTotalDataCount($totalcount);	
	}else{
		@$objRadio->_searchBasicInfoRadioRefNo($searchkey, $sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir, $mod, $patient_type,$ob);	
		$totalcount = $objRadio->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);	
	}
	
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
	
	#$objResponse->addAlert("PaginateRadioRequestlist:: ergebnis = ".$ergebnis);
#$objResponse->addAlert(" 2 : linecount=".$linecount." \n totalcount=".$totalcount);
	
	$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
	if ($linecount) 
		$textResult = '<hr width="80%" align="center">'.str_replace("~nr~",$totalcount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
#		echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else 
		$textResult = '<hr width="80%" align="center">'.str_replace('~nr~','0',$LDSearchFound);
#		echo str_replace('~nr~','0',$LDSearchFound); 
	$objResponse->addAssign('textResult',"innerHTML", $textResult);
	
	$my_count=$pagen->BlockStartNr();
	if($ergebnis){
		$temp=0;
#$objResponse->addAlert("PaginateRadioRequestlist: ergebnis = ".$ergebnis);
		while($row = $ergebnis->FetchRow() ){
#			if ($temp==0){
#				$objResponse->addAlert("PaginateRadioRequestlist: row : \n".print_r($row,true));		   		
#				$temp++;
#			}
			$gender = $row['sex'];	
			$date_request = @formatDate2Local($row['request_date'], $date_format);
			
			#added by VAN 06-17-08
			$date_request = $date_request." ".date("h:i A",strtotime($row['request_time']));
			#------------------
			
			$lname = htmlentities($row['name_last']);
			$fname = htmlentities($row['name_first']);
			$mname = htmlentities($row['name_middle']);
			
			if ( (!empty($row['pid'])) || ($row['pid']!='')){
#				$comma = (!empty($lname))? $comma = ", ":$comma = ""; 
#				$name = ucwords($lname).$comma.ucwords($fname);
				$name = $lname.", ".$fname." ".$mname;
			}else{
				$name = $row['ordername'];
			}
			
			
			#$objResponse->addAlert("type =".$row['charge_name']);
			#$objResponse->addAlert("batch, refno =".$row['parent_batch_nr']." - ".$row['parent_refno']);
			#added by VAN 01-15-08
			if ((!empty($row['parent_batch_nr'])) && (!empty($row['parent_refno'])))
				$repeat = 1;
			else	
				$repeat = 0;
			
			if ($row['encounter_type']==1){
				$enctype = "ERPx";
				
				$erLoc = $dept_obj->getERLocation($row['er_location'], $row['er_location_lobby']);
				if($erLoc['area_location'] != '')
    				$location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
    			else
    				$location = "EMERGENCY ROOM";
			}elseif ($row['encounter_type']==2 || $row['encounter_type']==IPBMOPD){
				if($row['encounter_type']==IPBMOPD) $enctype = "OPDx (IPBM)";
				else $enctype = "OPDx";
				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}elseif (($row['encounter_type']==3)||($row['encounter_type']==4) ||($row['encounter_type']==IPBMIPD)){
				if ($row['encounter_type']==3)
					$enctype = "INPx (ER)";
				elseif ($row['encounter_type']==4)
					$enctype = "INPx (OPD)";
				elseif ($row['encounter_type']==IPBMIPD)
					$enctype = "INPx (IPBM)";
						
				$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
				$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room_nr'];
			# Added by James 2/27/2014
			}elseif ($row['encounter_type']==6){
				$enctype = "IC";
				$location = "Industrial clinic";
			}else{
				$enctype = "WPx";
				#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				#$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				$location = 'WALK-IN';
			}

					 if (($row['is_cash']==0)&&(!$row['charge_name'])){
							$or_no="Charge";
							$paid = 0;
							#$objResponse->alert($row["refno"]);
					 }else{
								$sql = "SELECT c.charge_name, d.*
													FROM care_test_request_radio AS d
													LEFT JOIN seg_type_charge AS c ON c.id=d.request_flag
													WHERE refno='".trim($row["refno"])."'
													AND status NOT IN ('deleted','hidden','inactive','void')
													AND request_flag IS NOT NULL ORDER BY ordering LIMIT 1";
													// $objResponse->alert($sql);
								 $res=$db->Execute($sql);
								 $rows=$res->RecordCount();
								 $result_paid = $res->FetchRow();
								 $or_no = '';

								 if ($rows==0){
										$paid = 0;
								 }else{
										

								if($ob=='OB'){
										#$objResponse->alert($result_paid['request_flag']);
									 if ($result_paid["request_flag"]=='paid' || $result_paid["request_flag"]=='cmap' || $result_paid["request_flag"]=='lingap' || ($result_paid["request_flag"]=='charity' && ($row['r_discountid']=='PHS' || $row['r_discountid']=='PHSDep' || $row['r_discountid']=='SC'))){
								 		 $paid = 1;
									 }else{
										 $paid = 0;
										 }
										 $sql_manualpay = "SELECT * FROM seg_payment_workaround WHERE service_area='RD' AND refno='".trim($row["refno"])."' AND is_deleted=0";
												 			$rs_man=$db->Execute($sql_manualpay);
												 			$row_man_count=$rs_man->RecordCount();
												 			$row_man = $rs_man->FetchRow();
												 			$orpay_no = $row_man['control_no'];
											if($orpay_no){
												$paid = 1;
											}	 			
									$rad_dept = "OB";
								}else{
									$rad_dept = "RD";
									 if ($row["is_cash"]==1)
										 $paid = 1;
										 else
											 $paid = 0;
								}

										 if ($result_paid["request_flag"]=='paid'){
												$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																					FROM seg_pay_request AS pr
																					INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$row['pid']."'
																					WHERE pr.ref_source = ".$db->qstr($rad_dept)."  AND pr.ref_no = '".trim($row["refno"])."'
																					AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
														$rs_paid = $db->Execute($sql_paid);
														if ($rs_paid){
																$result2 = $rs_paid->FetchRow();
																$or_no = $result2['or_no'];
														}
														#added by VAN 06-03-2011
											 			#for temp workaround
											 			if (!$or_no){
												 			$sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='RD' AND refno='".trim($row["refno"])."' AND is_deleted=0";
												 			$res_manual=$db->Execute($sql_manual);
												 			$row_manual_count=$res_manual->RecordCount();
												 			$row_manual = $res_manual->FetchRow();
			
												 			$or_no = $row_manual['control_no'];
			
											 			}
			
										 }elseif ($result_paid["request_flag"]=='charity'){
												$sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																					FROM seg_granted_request AS pr
																					WHERE pr.ref_source = 'RD' AND pr.ref_no = '".trim($row["refno"])."'
																					LIMIT 1";
												$rs_paid = $db->Execute($sql_paid);
												if ($rs_paid){
														$result2 = $rs_paid->FetchRow();
														$or_no = 'CLASS D';
												}
										 }elseif (($result_paid["request_flag"]!=NULL)||($result_paid["request_flag"]!="")){
											 if ($withOR)
													$or_no = $off_rec;
			else	
													$or_no = $result_paid["charge_name"]== "CMAP" ? "MAP" :$result_paid["charge_name"];
										 }
								}
					 }
					 // $objResponse->alert($paid);
			$bdate = date("m/d/Y",strtotime($row['date_birth']));

			#$objResponse->addAlert('onqueue = '.$row['charge_name']);
			#$objResponse->addScriptCall("jsListRows",$sub_dept_nr, $my_count,$row['refno'],$row['rid'],$name,$gender,$date_request,$row['is_urgent'],$repeat,$paid,$row['charge_name'], $enctype,$location,$row['pid'],$row['age'],$bdate);
			
			$source_req = $objRadio->getSourceReq($row['refno']);
			$is_printed = $db->GetOne("SELECT is_printed FROM seg_radio_serv WHERE refno = " . $db->qstr($row['refno']));																				#sub_dept_nr,No,refNo,rid,name,sex,dateRequest,priority, repeat, charge_type, enctype,location,pid,age,bdate

			$r = \SegHis\modules\costCenter\models\RadiologyRequestSearch::search(array('referenceNo' => $row['refno']));
			$request = array(
				'allowDelete' => $r->allowDelete ? 1 : 0,
				'message' => $r->getMessage(),
				'warning' => $r->getWarning(),
			);

			$objResponse->addScriptCall("jsListRows",
				$sub_dept_nr,
				$my_count,
				$row['refno'],
				$row['rid'],
				$name,
				$gender,
				$date_request,
				$row['is_urgent'],
				$repeat,
				$paid,
				$or_no,
				$enctype,
				$location,
				$row['pid'],
				$row['encounter_nr'],
				$row['age'],
				$bdate,
				$request,
                                $source_req,
                                $is_printed
			);
			$my_count++;
		}//end while loop
	//end if (ergebnis)
	}else{
		//$tr = "<tr><td colspan=\"8\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:\"Arial\",Courier, mono; font-style:Bold; font-weight:Bold; font-size:12px;\">NO MATCHING REQUEST FOUND</td></tr>";
		$tr = "<tr><td colspan=\"15\"  style=\"\">No requests available at this time...</td></tr>";
		$objResponse->addAssign("TBodytab".$sub_dept_nr, "innerHTML", $tr);
	}

		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("PaginateRadioRequestlist : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);	
	$pageFirstOffset = 0;
	$pagePrevOffset = $prevIndex;
	$pageNextOffset = $nextIndex;		
	$pageLastOffset = $totalcount-($totalcount%$pagen->MaxCount());
	if ($pagen->csx){
		$pageFirstClass = "segSimulatedLink";
		$pageFirstOnClick = " setPgx($pageFirstOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
		$pagePrevClass = "segSimulatedLink";
		$pagePrevOnClick = " setPgx($pagePrevOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
	}else{
		$pageFirstClass = "segDisabledLink";
		$pagePrevClass = "segDisabledLink";
	}
	if ($nextIndex){
		$pageNextClass = "segSimulatedLink";
		$pageNextOnClick = " setPgx($pageNextOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
		$pageLastClass = "segSimulatedLink";
		$pageLastOnClick = " setPgx($pageLastOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
	}else{
		$pageNextClass = "segDisabledLink";
		$pageNextOffset = $pageLastOffset;		
		$pageLastClass = "segDisabledLink";
	}

	$img ='										<div id="pageFirst" class="'.$pageFirstClass.'" style="float:left" onclick="'.$pageFirstOnClick.'"> '.
			'											<img title="First" src="../../images/start.gif" border="0" align="absmiddle"/> '.
			'											<span title="First">First</span> '.
			'										</div> '.
			'										<div id="pagePrev" class="'.$pagePrevClass.'" style="float:left" onclick="'.$pagePrevOnClick.'"> '.
			'											<img title="Previous" src="../../images/previous.gif" border="0" align="absmiddle"/> '.
			'											<span title="Previous">Previous</span> '.
			'										</div> '.
			'										<div id="pageShow" style="float:left;margin-left:10px;"> '.
			'											<span>List of Service Requests</span> '.
			'										</div> '.
			'										<div id="pageLast" class="'.$pageLastClass.'" style="float:right" onclick="'.$pageLastOnClick.'"> '.
			'											<span title="Last">Last</span> '.
			'											<img title="Last" src="../../images/end.gif" border="0" align="absmiddle"/> '.
			'										</div> '.
			'										<div id="pageNext" class="'.$pageNextClass.'" style="float:right" onclick="'.$pageNextOnClick.'"> '.
			'											<span title="Next">Next</span> '.
			'											<img title="Next" src="../../images/next.gif" border="0" align="absmiddle"/> '.
			'										</div> ';
	$objResponse->addAssign("mainHead".$sub_dept_nr,"innerHTML", $img);
}// end of function PaginateRadioRequestlist 


$xajax->processRequest();
?>