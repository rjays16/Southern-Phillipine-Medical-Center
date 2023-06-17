<?php

    function populateRequestsList($encounter_nr, $page){
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        $encObj = new Encounter();
        $offset = $page * $maxRows;
        
        $ergebnis=$encObj->countEncounterRequests($encounter_nr);
        if($ergebnis)
            $total = $ergebnis->RecordCount();
        else
            $total = 0;
        
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","RequestList");
        $ergebnis=$encObj->getEncounterRequests($encounter_nr, $offset,1);
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                $req_date= $result['req_date'];
                $details = '';
                $i = 0;
                $refno = $result["refno"];
                $type = $result["req_type"];
                $detObj = $encObj->getRequestDetails($refno, $type);
                while($detObj && $det_res = $detObj->FetchRow()){
                    if($i>0)
                        $details .= ", ";
                    $details .= $det_res['item'];
                    $i++;
                }

                $lab_source = $db->GetOne("SELECT CASE WHEN ref_source = 'BB' THEN '/Bloodbank' WHEN ref_source = 'LB' THEN ''
                                            ELSE CONCAT('/',ref_source) END AS src FROM seg_lab_serv WHERE refno = ".$db->qstr($result['refno']));

                $radio = $db->Execute("SELECT name AS item FROM seg_radio_services AS s
										LEFT JOIN care_test_request_radio AS sd ON sd.service_code = s.service_code
										WHERE sd.refno = ".$db->qstr($result['refno']));

                if($radio->RecordCount() > 1){
                	$radio_group = "";
            	}
            	else {	
	                $radio_group = $db->GetOne("SELECT CONCAT('/',srg.`name`) FROM care_test_request_radio ctr
	                                            INNER JOIN seg_radio_services srs ON ctr.`service_code` = srs.`service_code`
	                                            INNER JOIN seg_radio_service_groups srg ON srs.`group_code` = srg.`group_code`
	                                            WHERE refno = ".$db->qstr($result['refno']));
            	}
            	

                switch($type){
                    case 1: $req_type = "Laboratory".$lab_source;
                            $result["req_by"] = $result['req_by'];
                            break;
                    case 2: $req_type = "Radiology".$radio_group;
                            $result["req_by"] = $result['req_by'];
                            break;
                    case 3: $req_type = "Pharmacy";
                            break;
                    case 4: $req_type = "Pharmacy";
                            break;
                    case 5: $req_type = "MISC Services";
                            break;
                    case 7: $req_type = "MISC Services";
                            $result["req_by"] = $result['req_by'];
                            break;
                    case 8: $req_type = "Pharmacy";
                    		break;
                    case 9: $req_type = "Point of Care";
                            break;
                    case 10: $req_type = "OB-GYNE Ultrasound".$radio_group;
                            break;
                    default: $req_type = "Others";
                }
                $objResponse->addScriptCall("addPerson","RequestList",$refno,$req_date,$req_type,strtoupper($result["req_by"]),$details);
            }#end of while
        } #end of if

        if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
        $objResponse->addScriptCall("endAJAXSearch",'0');
        
        return $objResponse;
    }

	 require('./roots.php');
	 require($root_path.'include/inc_environment_global.php');    
	 require($root_path."modules/registration_admission/ajax/mode-history.common.php");
	 require_once($root_path.'include/care_api_classes/class_globalconfig.php');
     require_once($root_path.'include/care_api_classes/class_encounter.php');
     require_once($root_path.'include/care_api_classes/class_paginator.php');
    #-------------------------------------
	 $xajax->processRequests();
?>
