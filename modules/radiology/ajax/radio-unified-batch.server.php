<?php

		function populateUnifiedBatchList($pid, $sElem,$searchkey,$page) {
				//global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'] + 10;//edited by art 01/29/2014

				global $root_path, $date_format, $HTTP_SESSION_VARS;

				$objResponse = new xajaxResponse();

				$radio_obj=new SegRadio();

				$offset = $page * $maxRows;
				//$searchkey = utf8_decode($searchkey);
				#edited by VAN 02-28-2013
                #optimize the code
                #$total_srv = $radio_obj->countUnifiedBatchList($pid, $maxRows,$offset);
                $ergebnis=$radio_obj->UnifiedBatchList($pid, $maxRows,$offset);
				#$objResponse->addAlert("sql = ".$radio_obj->sql);
				//$objResponse->addAlert($pid);
				#$total = $radio_obj->count;
                $total = $radio_obj->FoundRows();

				#$objResponse->addAlert('total = '.$total);
				//$objResponse->addScriptCall("msgPopUp","This request has been scheduled already.");
				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				#$ergebnis=$radio_obj->UnifiedBatchList($pid, $maxRows,$offset);
				#$objResponse->addAlert("sql = ".$radio_obj->sql);
				#$objResponse->addAlert("sql = ".$offset);
				$rows=0;
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","historyList");
				//$objResponse->addScriptCall("gawagawa","ok");
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								$batch_nr = $result["refno"];
								$date_request = date("m/d/Y h:i:A",strtotime($result["request_date"]." ".$result["request_time"]));
								$or_no = $result['or_no'];

								//$objResponse->addAlert("batch_nr = ".$batch_nr);
								$objResponse->addScriptCall("addtoList","historyList",$batch_nr,$date_request, mb_strtoupper($or_no));
								//$objResponse->addScriptCall("addtoList","historyList",$batch_nr);
						}#end of while
						if (!$rows) $objResponse->addScriptCall("addtoList","historyList",NULL);
				} #end of if
				else
						$objResponse->addScriptCall("addtoList","historyList",NULL);
				$objResponse->addScriptCall("endAJAXSearch",$sElem);
				return $objResponse;
		}

		function populateUnifiedBatchRequestList($pid, $sElem,$searchkey,$page) {
				//global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				global $root_path, $date_format, $HTTP_SESSION_VARS;

				$objResponse = new xajaxResponse();

				$radio_obj=new SegRadio();

				$offset = $page * $maxRows;
				//$searchkey = utf8_decode($searchkey);
                $ergebnis=$radio_obj->UnifiedBatchRequestList($pid, $maxRows,$offset);
				#$total_srv = $radio_obj->countUnifiedBatchRequestList($pid, $maxRows,$offset);
                $total = $radio_obj->FoundRows();
				#$objResponse->addAlert("sql = ".$radio_obj->sql);
				#$total = $radio_obj->count;

				#$objResponse->addAlert('total = '.$total);
				//$objResponse->addScriptCall("msgPopUp","This request has been scheduled already.");
				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				#$ergebnis=$radio_obj->UnifiedBatchRequestList($pid, $maxRows,$offset);
				#$objResponse->addAlert("sql = ".$radio_obj->sql);
				#$objResponse->addAlert("sql = ".$offset);
				$rows=0;
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","historyList");
				//$objResponse->addScriptCall("gawagawa","ok");
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						$borrow_details = '';
						$is_borrowed =0;
						while($result=$ergebnis->FetchRow()) {
                                $batch_nr = $result["batch_nr"];
								$service_code = $result["service_code"];
								//$date_request = date("m/d/Y",strtotime($result["request_date"]));
								$request = $result["name"];
								$status = $radio_obj->getDiagStatus($batch_nr);#added by art 07/04/2014
								#$objResponse->addAlert("batch_nr = ".$batch_nr);
								$rs = $radio_obj->getBorrowedInfo($batch_nr);
								if ($rs){
										$row2= $rs->FetchRow();
										if((string)$row2["remarks"]=="")
											$strRemarks="None";
										else
												$strRemarks=$row2["remarks"];

									if ($row2["is_borrowed"]==1) {

										$borrower_name=$row2['borrower'];
										if($borrower_name=="")
											$borrower_name=$row2['borrower_name'];
										$borrow_details = 'Borrower : '.mb_strtoupper($borrower_name).' <br>
																			 Date Borrowed : '.date('m/d/Y',strtotime($row2['date_borrowed'])).'<br>
																			 Time Borrowed : '.date('h:i A',strtotime($row2['time_borrowed'])).
																			 "</br>\nRemarks: ".$strRemarks;                           #added by angelo m. 08.11.2010;
										$is_borrowed = 1;
									}else{

										$borrow_details = "Still Available";
										$is_borrowed = 0;
									}
								}else{
										$borrow_details = "Still Available";
										$is_borrowed = 0;
								}
                                
                                 if (trim($result['name_short'])!=''){        
                                    $dept = trim($result['name_short']);
                                 }elseif (trim($result['id'])!=''){
                                    $dept = trim($result['id']);
                                 }else{
                                    $dept = trim($result['name_formal']);
                                 }

								#$objResponse->alert(print_r($borrow_details),true);
								#$objResponse->alert($borrow." - ".$row_rs["date_returned"]);
								$objResponse->addScriptCall("addtoList","historyList",$batch_nr,$service_code, $request, $is_borrowed, $borrow_details, $result["is_served"], $dept,$status);#edited by art 07/04/2014 added status

								//$objResponse->addScriptCall("addtoList","historyList",$batch_nr);
						}#end of while
						if (!$rows) $objResponse->addScriptCall("addtoList","historyList",NULL);
				} #end of if
				else
						$objResponse->addScriptCall("addtoList","historyList",NULL);
				$objResponse->addScriptCall("endAJAXSearch",$sElem);
				return $objResponse;
		}

	#added by VAN 03-23-09
	function getFindingNr($batch_nr){
		global $db;
		$objResponse = new xajaxResponse();

		#$findings_nr = 1;
		$sql = "SELECT findings FROM care_test_findings_radio WHERE batch_nr='".$batch_nr."'";
		$res = $db->Execute($sql);
		$row = $res->FetchRow();

		$findings_array = unserialize($row['findings']);
		$findings_nr = count($findings_array)-1;
		#$objResponse->alert('findings_nr ='.$findings_nr);

		$objResponse->addScriptCall("ViewResult_child",$batch_nr,$findings_nr);

		return $objResponse;
	}
	#-----------------

	#added by VAN 10-09-2014
	function parseHL7Result($batch_nr, $pid){
    	$objResponse = new xajaxResponse();
    	$radio_obj = new SegRadio;
    	$parseObj = new seg_parse_msg_HL7();
        $hl7fxnObj = new seg_HL7();
    	$details = (object) 'details';

    	$info = $radio_obj->getHL7Result($batch_nr, $pid);
    	#$objResponse->alert( $radio_obj->sql);
    	#$objResponse->alert(print_r($info,1));
		$details->hl7_msg = $info['hl7_msg'];
		$details->filename = $info['filename'];

		$segments = explode($parseObj->delimiter, trim($details->hl7_msg));
		$counter_obx = 1;
		#set all arrays to null
        unset($msh);
        unset($msa);
        unset($pid);
        unset($obr);
        unset($obx);
        unset($nte);

        foreach($segments as $segment) {
            $data = explode('|', trim($segment));
            #$objResponse->alert(print_r($data,1));
            if (in_array("MSH", $data)) {
                $msh = $parseObj->segment_msh($data);
            }

            if (in_array("MSA", $data)) {
                $msa = $parseObj->segment_msa($data);
            }

            if (in_array("PID", $data)) {
                $pidsegment = $parseObj->segment_pid($data);
            }

            if (in_array("OBR", $data)) {
                $obr = $parseObj->segment_obr($data);
            }

            if (in_array("OBX", $data)) {
            	$obx[$counter_obx] = $parseObj->segment_obx($data);
                $counter_obx++;
            }

            if (in_array("NTE", $data)) {
                $nte[$counter_nte] = $parseObj->segment_nte($data,$counter_obx);
                $counter_nte++;
            }    
        }
        
        $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr['test']));
        $details->testcode = $arr_test[0];
        $details->testname = $arr_test[1];
        
        for ($cnt=1; $cnt < $counter_obx; $cnt++){
        	$arr_testservice = explode($parseObj->COMPONENT_SEPARATOR, trim($obx[$cnt]['testservice']));
            $details->testservice = $arr_testservice[1];
            $details->testcode = $arr_testservice[0];
            $details->url = $obx[$cnt]['url'];

        }
        
        #$objResponse->alert(print_r($details,1));
        #$url = 'http://116.50.176.78/novaweb/launchviewer.aspx?UserName=admin&Password=novapacs&accession=OX77277';
        
    	if ($details->url){
    		if(strpos($details->url, "lite") !== false)
    			$objResponse->alert('Novapacs lite viewer is unavailable. Kindly view image using HTML5 Viewer.');
    		else  $objResponse->addScriptCall('loadPacsViewer', $details->url);
    		
    	}		
    	else
    		$objResponse->alert('No Pacs Image yet...');
        

    	return $objResponse;	
    }
    #=======================

		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require($root_path."modules/radiology/ajax/radio-unified-batch.common.php");

		require_once($root_path.'include/care_api_classes/class_globalconfig.php');

		require_once($root_path.'include/care_api_classes/class_encounter.php');
		require_once($root_path.'include/care_api_classes/class_radiology.php');
		include_once($root_path.'include/care_api_classes/class_paginator.php');

		#added by VAN 10-09-2014
		#PACS
		require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_parse_hl7_message.php');
		require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');

		$xajax->processRequests();
?>
