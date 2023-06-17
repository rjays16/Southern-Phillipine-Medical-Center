<?php

	define("IPBMIPD","13");
	define("IPBMOPD","14");

	#Vaccination Certificate if patient is new born
	#Medical Records ('Dialog box').
	#Comment by: borj 2014-05-06
    function  saveVaccination($vPid,$vDetails,$vDate){
        $objResponse = new xajaxResponse();
        $objPerson = new Person;
        $data = array(
            'pid' => $vPid,
            'vac_details' => $vDetails,
            'vac_date' => date('Y-m-d',strtotime($vDate))
        );

        if($objPerson->insertVaccination($data)){
            $objResponse->alert("Saved");
            $objResponse->addScriptCall('printVaccination');
        }else{
            $objResponse->alert("Failed");
        }

        return $objResponse;
    }
    #End
    
    function populateAdmissionList($pid, $sElem,$searchkey,$page) {
    
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        $enc_obj=new Encounter;
        $ward_obj=new Ward;
        $dept_obj=new Department;
        $pers_obj=new Personell;
        $hosp_obj=new Hospital_Admin;
        
        $offset = $page * $maxRows;
        $searchkey = utf8_decode($searchkey);
        $total_srv = $enc_obj->countSearchAdmissionList($pid, $searchkey,$maxRows,$offset);
        #$objResponse->addAlert($enc_obj->sql);
        $total = $enc_obj->count;
        
        #$objResponse->addAlert('total = '.$total);
        
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$enc_obj->SearchAdmissionList($pid, $searchkey,$maxRows,$offset);
        #$objResponse->addAlert("sql = ".$enc_obj->sql);
        #$objResponse->addAlert("sql = ".$maxRows);
        #$objResponse->addAlert("sql = ".$offset);
        $rows=0;
        
        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","historyList");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                
                $encounter_nr = $result["encounter_nr"];
                $admitted_date = date("m/d/Y",strtotime($result["referral_date"]));
                //$admitted_time = date("h:i:s A",strtotime($result["create_time"]));
                $location = $result["is_dept"];
                #$objResponse->addAlert("is_dept = ".$location);
                $is_dept = $result["is_dept"];
                if($is_dept==1){
                    $tmp = $dept_obj->getDeptAllInfo($result['referrer_dept']);
                    $location = $tmp['name_formal'];
                }
                else{
                    $tmp = $hosp_obj->getOtherHospitalInfo($result['referrer_dept']);
                    $location = $tmp['hosp_name'];
                }
                $doctor = $result['referrer_dr'];
                $status = $result['is_referral'];
                
                $doctorinfo = $pers_obj->get_Person_name($doctor);
                #$objResponse->addAlert($pers_obj->sql);
                $middleInitial = "";
                if (trim($doctorinfo['name_middle'])!=""){
                    $thisMI=split(" ",$doctorinfo['name_middle']);    
                    foreach($thisMI as $value){
                        if (!trim($value)=="")
                        $middleInitial .= $value[0];
                    }
                    if (trim($middleInitial)!="")
                    $middleInitial .= ".";
                }
    
                $doctor_name = $doctorinfo['name_first']." ".$doctorinfo['name_2']." ".$middleInitial." ".$doctorinfo['name_last'];
                $doctor_name = ucwords(strtolower($doctor_name));
                $doctor_name = htmlspecialchars($doctor_name);
                $objResponse->addScriptCall("addAdmissionToList","historyList",trim($result["referral_nr"]),$admitted_date,$location, $doctor_name, $status, $encounter_nr);
            }#end of while
            if (!$rows) $objResponse->addScriptCall("addAdmissionToList","historyList",NULL);
        } #end of if
        else
            $objResponse->addScriptCall("addAdmissionToList","historyList",NULL); 
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    	
	function populateEncounterList($pid, $sElem,$searchkey,$page,$isIPBM=0) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$enc_obj=new Encounter;
		$ward_obj=new Ward;
		$dept_obj=new Department;
		$pers_obj=new Personell;
		
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		#$total_srv = $enc_obj->countSearchEncounterList($pid, $searchkey,$maxRows,$offset);
		$ergebnis=$enc_obj->SearchEncounterList($pid, $searchkey,$maxRows,$offset,$isIPBM);
		#$objResponse->addAlert($enc_obj->sql);
		$total = $enc_obj->FoundRows();
		
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		#$ergebnis=$enc_obj->SearchEncounterList($pid, $searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql = ".$enc_obj->sql);
		#$objResponse->addAlert("sql = ".$maxRows);
		#$objResponse->addAlert("sql = ".$offset);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","historyList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				if (($result["encounter_type"]==1)||($result["encounter_type"]==2)){
					$admitted_date = date("m/d/Y",strtotime($result["encounter_date"]));
					$admitted_time = date("h:i:s A",strtotime($result["encounter_date"]));
					
					if ($result["encounter_type"]==1){
						$sql_loc = "SELECT el.area_location FROM seg_er_location el WHERE el.location_id = ".$result['er_location'];
						$er_location = $db->GetOne($sql_loc);

						if($er_location != '') {
							$sql_lobby = "SELECT eb.lobby_name FROM seg_er_lobby eb WHERE eb.lobby_id = ".$result['er_location_lobby'];
							$er_lobby = $db->GetOne($sql_lobby);

							if($er_lobby != '') {
								$location = 'ER - ' . $er_location . " (" . $er_lobby . ")";
							}
							else {
								$location = 'ER - ' . $er_location;
							}
						}
						else{
							$location = 'ER';
						}
					}
					elseif ($result["encounter_type"]==2){
						#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
						#$location = $dept['id'];
						if ($result['dept_name2'])
							$location = $result['dept_name2'];
						else	
							$location = $result['dept_name'];
					}
						
				}elseif (($result["encounter_type"]==3)||($result["encounter_type"]==4)){
					$admitted_date = date("m/d/Y",strtotime($result["admission_dt"]));
					$admitted_time = date("h:i:s A",strtotime($result["admission_dt"]));
					
					$ward = $ward_obj->getWardInfo($result['current_ward_nr']);
					$location = $ward['ward_id']." : Rm.#".$result['current_room_nr'];
				}elseif ($result["encounter_type"]==6){ //edited by Macoy, July 08, 2014
					$location = "Industrial Clinic";
					$admitted_date = date("m/d/Y",strtotime($result["encounter_date"])); //added by Macoy, July 10, 2014
					$admitted_time = date("h:i:s A",strtotime($result["encounter_date"])); //added by Macoy, July 10, 2014
				}elseif ($result["encounter_type"]==IPBMIPD){ //added by raymond for IPBM 3/9/2017
					$location = "IPBM";
					$admitted_date = date("m/d/Y",strtotime($result["encounter_date"])); 
					$admitted_time = date("h:i:s A",strtotime($result["encounter_date"])); 
				}elseif ($result["encounter_type"]==IPBMOPD){ //added by raymond for IPBM 3/9/2017
					$location = "IPBM";
					$admitted_date = date("m/d/Y",strtotime($result["encounter_date"])); 
					$admitted_time = date("h:i:s A",strtotime($result["encounter_date"])); 
				}


				
				if ($result['consulting_dr_nr']){
					$doctor	= $result['consulting_dr_nr'];
				}elseif ($result['current_att_dr_nr']){
					$doctor	= $result['current_att_dr_nr'];
				}elseif ($result['consulting_dr']){
					$doctor	= $result['consulting_dr'];	
				}else{ //added by Macoy, July 10, 2014
					$doctor	= "";
				}
				
				//echo "<pre>".print_r($result,true)."</pre>";

				$doctorinfo = $pers_obj->get_Person_name($doctor);
				#$objResponse->addAlert($pers_obj->sql);
				$middleInitial = "";
				if (trim($doctorinfo['name_middle'])!=""){
					$thisMI=split(" ",$doctorinfo['name_middle']);	
					foreach($thisMI as $value){
						if (!trim($value)=="")
						$middleInitial .= $value[0];
					}
					if (trim($middleInitial)!="")
					$middleInitial .= ".";
				}
	
				$doctor_name = $doctorinfo['name_first']." ".$doctorinfo['name_2']." ".$middleInitial." ".$doctorinfo['name_last'];
				$doctor_name = ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
				
				if ($result['is_confidential']==1){					
					$location = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
					$doctor_name = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
				}
				
				$objResponse->addScriptCall("addEncounterToList","historyList",trim($result["encounter_nr"]),$admitted_date,$admitted_time,$location, $doctor_name, $result['dept_name'], $pid);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addEncounterToList","historyList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
	
	function populatePatientList($enctype){
		global $db, $HTTP_SESSION_VARS;
		$objResponse = new xajaxResponse();
		
		$objResponse->addScriptCall("onsubmitForm");
		return $objResponse;
	
	}
	
	function changeStatus($pid){
		global $db, $HTTP_SESSION_VARS;
		$objResponse = new xajaxResponse();
		
		$sql_update = "UPDATE care_person SET 
						fromtemp=1,
						history = CONCAT(history,'Update: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n'),
						modify_id = '".$_SESSION['sess_user_name']."',
						modify_time = '".date('Y-m-d H:i:s')."'
						WHERE pid='".$pid."'";
		
		#$objResponse->alert($sql_update);
		$res_update=$db->Execute($sql_update);	
		
		if ($res_update)
			$objResponse->alert("The patient status is successfully change.");	
		else	
			$objResponse->alert("Changing patient's status is failed.");	
		
		$objResponse->addScriptCall("ReloadWindow");
		return $objResponse;
	
	}

	function populateRegisteredFingerprint($pid) {
		global $db;
		$objResponse = new xajaxResponse();

		$data = PersonFingerprint::getPersonFingerprintOnly($pid);
		$finger = array();
		$isExist = array();
		if ($data) {
			foreach($data as $key=>$value) {
				$finger[] = $key;
				$isExist[] = !empty($value) ? 1 : 0;
			}
		}
		$objResponse->addScriptCall("setRegisteredFingerprint", $finger, $isExist);
		return $objResponse;
	}
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path."modules/registration_admission/ajax/comp_search.common.php");
	#added by VAN 04-17-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	include_once($root_path.'include/care_api_classes/class_paginator.php');
    include_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/biometric/class_person_fingerprint.php');

    #Vaccination Certificate if patient is new born
	#Medical Records ('Dialog box').
	#Comment by: borj 2014-05-06
    include_once($root_path.'include/care_api_classes/class_person.php');
	#End	 
	$xajax->processRequests();
?>
