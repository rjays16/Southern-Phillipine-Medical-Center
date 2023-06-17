<?php
	#----------------------
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	
	#added by VAN 03-10-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require($root_path.'include/care_api_classes/class_ward.php');
	require($root_path.'include/care_api_classes/class_personell.php');
	// require($root_path.'include/care_api_classes/class_notes_nursing.php');#added by 
	require($root_path."modules/nursing/ajax/nursing-ward-common.php");
	#created by VAN 04-08-08
	require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
	
	// added by Francis 07-18-13
	function moveToWaitingList($enr){
		global $db, $HTTP_SESSION_VARS;
		
		$objResponse = new xajaxResponse();
		$enc_obj = new Encounter;
		$resultFailed = "Failed to transfer patient.";

		$move = $enc_obj->MoveToWaitingList($enr);
		if($move){
			$objResponse->addScriptCall("refreshWindow",NULL);
		}else{
			$objResponse->alert($resultFailed);
		}
		

		  return $objResponse;
	}

	# added by: syboy 02/22/2016 : meow
	# move to expired patient
	function moveToExpiredPatient($enc){
		global $db, $HTTP_SESSION_VARS;
		
		$objResponse = new xajaxResponse();
		$enc_obj = new Encounter;
		$resultFailed = "Failed to transfer patient.";

		$move = $enc_obj->MoveToExpiredPatient($enc);
		if($move){
			$objResponse->addScriptCall("refreshWindow",NULL);
		}else{
			$objResponse->alert($resultFailed);
		}
		
		return $objResponse;
	}

	function populateWardList($sElem,$searchkey,$page,$personell_nr) {
		global $db, $HTTP_SESSION_VARS;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$ward_obj = new Ward;
		$pers_obj=new Personell;
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		$searchkey = utf8_decode($searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $ward_obj->countSearchNursingWard($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$ward_obj->sql);
		$total = $ward_obj->count;
		#$objResponse->addAlert("total = ".$personell_nr);
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$ward_obj->SearchNursingWard($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$ward_obj->sql);
		$rows=0;
		$row_per = $pers_obj->get_Personell_info($personell_nr);
		$job = substr($row_per['short_id'],0,1);
		#$objResponse->alert(print_r($HTTP_SESSION_VARS,true));sess_permission
		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","labgrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			
			$admin_permission = array('System_Admin', '_a_0_all');
			
			for ($i=0; $i<sizeof($admin_permission);$i++){
					if (ereg($admin_permission[$i],$HTTP_SESSION_VARS['sess_permission'])){
							$allow_all = 1;
							break;
					}else
							$allow_all = 0; 		     
			}

			if (ereg('_a_1_nursingstationviewpatientward',$HTTP_SESSION_VARS['sess_permission'])){
				$allow_pharmacists = 1;
			}else
				$allow_pharmacists = 0;
			#$objResponse->alert($allow_all);
			#$assigned = in_array($HTTP_SESSION_VARS['sess_permission'], $admin_permission));
			while($result=$ergebnis->FetchRow()) {
				if ((!$row_per['is_reliever'])&&($job=='N')){
					$ward_row = $pers_obj->get_Nurse_Ward_Area_Assign($personell_nr, $result['nr']);
					$assigned_ward =$pers_obj->count;
				}elseif ((($row_per['is_reliever'])&&($job=='N'))||($allow_all)||($allow_pharmacists)){
					$assigned_ward =1;
				}else{
					$assigned_ward =0;
				}	
				#$objResponse->alert($pers_obj->sql);
				#$objResponse->addAlert("sql c2 = ".$result["group_code"]." , ".$result["name"]." , ".$result["other_name"]);
			   $rooms = $result["room_nr_start"]." - ".$result["room_nr_end"];
				#$objResponse->alert($result['nr']." - ".$with_ward);	
				$ward_id = strtr('nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.urlencode($result['ward_id']).'&location_id='.urlencode($result['ward_id']).'&ward_nr='.$result['nr'],' ',' ');
				#$objResponse->addScriptCall("addWardList","labgrouplistTable",stripslashes($result["ward_id"]),stripslashes($result["name"]),$rooms,number_format($result["ward_rate"],2));
				#$objResponse->addScriptCall("addWardList","labgrouplistTable",$ward_id,strtoupper($result['ward_id']),stripslashes($result["name"]),$rooms,number_format($result["ward_rate"],2));
				$objResponse->addScriptCall("addWardList","labgrouplistTable",$ward_id,strtoupper($result['ward_id']),stripslashes($result["name"]),$rooms,number_format($result["ward_rate"],2),$result["is_temp_closed"],$result['nr'],$assigned_ward);
			}
		}
		#commented by VAN 03-17-08
		if (!$rows) $objResponse->addScriptCall("addWardList","labgrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}

	function moveToBeDischarge($enc_nr) {
		$objResponse = new xajaxResponse();
		$encObj = new Encounter;
		$errorMsg = 'Unable to move the patient!';
		$successMsg = 'Succesfully Transferred!';

		$toBeDischarge = $encObj->MoveToBeDischarge($enc_nr);

		if($toBeDischarge) {
			$objResponse->alert($successMsg);	
			$objResponse->addScriptCall("refreshWindow",NULL);
			
		} else {
			$objResponse->alert($toBeDischarge);		
		}

		return $objResponse;
	}

	/* end */

	function moveBackToWaitingList($enc_nr) {

		$objResponse = new xajaxResponse();
		$encObj = new Encounter;
		$errorMsg = 'Unable to move the patient!';
		$successMsg = 'Succesfully Transferred!';

		$backToWaitingList =  $encObj->MoveBackToWaitaingList($enc_nr);

		if($backToWaitingList) {
			$objResponse->alert($successMsg);
			$objResponse->addScriptCall("refreshWindow",NULL);
		} else {
			$objResponse->alert($errorMsg);
		}

		return  $objResponse;
	}
	
	/*-------------Accommodation Dialog-------------*/

	function setWardRooms($ward_nr){
		$objResponse = new xajaxResponse();

		$objwrd = new Ward();
		$wards = $objwrd->getRoomsData($ward_nr);
		if ($wards) {
			if ($wards->RecordCount()) {
				$objResponse->addScriptCall("js_ClearOptions","roomlist");
				$objResponse->addScriptCall("js_AddOptions","roomlist","- Select Room -", 0);
				while ($row = $wards->FetchRow()) {
					$objResponse->addScriptCall("js_AddOptions", "roomlist", $row['room_nr'], $row['nr'], $row['info'], $row['is_per_hour']);
				}
			}
		}
		else
			$objResponse->alert("ERROR: Cannot retrieve available rooms!");

		return $objResponse;
	}	

	function saveAccommodation($aFormValues, $bill_dt) {
		global $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();
		$objBilling = new Billing();
		$objEncounter = new Encounter();

		$occ_date_from = $aFormValues['datefrom'];
		$occ_date_to = $aFormValues['dateto'];

		if ($aFormValues['encounter_nr'] != '') {
			$tmp_dte = getAdjustedDate($bill_dt);
	        $sesID = $HTTP_SESSION_VARS['sess_user_name'];
	        $sesUN = $HTTP_SESSION_VARS['sess_user_name'];
	        $date_from = date('Y-m-d', strtotime($occ_date_from));
	        $date_to = date('Y-m-d', strtotime($occ_date_to));

	        $ndays = ((abs(strtotime($occ_date_to)-strtotime($occ_date_from)))/(60*60*24));
	        if($date_from==$date_to){
	        	$ndays = 1;
	        }

	        $hours = 0;
	        if($aFormValues['is_per_hour']){
	        	$aFormValues['occupytimefrom'] = date('H:i:s', strtotime($occ_date_from));
	        	$aFormValues['occupytimeto'] = date('H:i:s', strtotime($occ_date_to));

	        	$df = strtotime($occ_date_from);
				$dt = strtotime($occ_date_to);
				$diff = $dt - $df;
				$hours = $diff / ( 60 * 60 );
	        }
	        $room_info['ward_nr'] = $aFormValues['ward_nr'];
	        $room_info['room_nr'] = $aFormValues['room_nr'];

	        $rm_rate = $objBilling->getRoomRate($room_info);
	        $rm_rate = number_format($rm_rate, 2, '.', '');

	        $aFormValues['room_rate'] = $rm_rate;
	        $aFormValues['occupydatefrom'] = $date_from;
	        $aFormValues['occupydateto'] = $date_to;
	        $aFormValues['days'] = $ndays;
	        $aFormValues['hrs'] = $hours;
	        $aFormValues['sessionID'] = $sesID;
	        $aFormValues['sessionUN'] = $sesUN;
	        $aFormValues['createdate'] = $tmp_dte;

	        $history = "Added ".$aFormValues['ward_name']." (".$aFormValues['occupydatefrom']." to ".$aFormValues['occupydateto'].") by ".$sesID." ".strftime("%Y-%m-%d %H:%M:%S")."\n";

	        $saveok = $objBilling->saveAdditionalAccommodation($aFormValues);

	        if($saveok){
	        	$saveok = $objEncounter->updateAccommodationTrail($aFormValues['encounter_nr'], $history);

	        	if($saveok){
		        	$objResponse->alert("Successfully Added Accommodation");
		        	$objResponse->addScriptCall("reloadWindow");
	        	}
	        }
		}
		return $objResponse;
	}

	function getAdjustedDate($date){
		if (strcmp($date, "0000-00-00 00:00:00") != 0)
			$tmp_dte = $date;
		else
			$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");
	
		return strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));
	}

	function checkifOverlaps($enc, $date_from, $date_to){
		$objResponse = new xajaxResponse();
		$encObj = new Encounter;

		$date_from_convert = date("Y-m-d", strtotime($date_from));
		$date_to_convert = date("Y-m-d", strtotime($date_to));

		$overlapped = $encObj->checkAccIfOverlaps($enc, $date_from_convert, $date_to_convert);

		if($overlapped)
			$objResponse->addScriptCall("validateAdd",1);
		else
			$objResponse->addScriptCall("validateAdd",0);

		return $objResponse;
	}

	function deleteAccommodation($id, $encounter_nr){
		global $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();
		$encObj = new Encounter;

		$data_id = explode('_', $id);
		
		$ward_id = $data_id[0];
		$room = $data_id[1];
		$date_from = date('Y-m-d', strtotime($data_id[2]));
		$date_to = date('Y-m-d', strtotime($data_id[3]));
		$ward_name = $encObj->getWardName($ward_id);

		foreach ($ward_name as $key => $value) {
			$ward_name = $value;
		}

		$deleted = $encObj->deleteNursingAccommodation($encounter_nr, $ward_id, $room, $date_from, $date_to);

		$history = "Deleted ".$ward_name['ward_name']." (".$date_from." to ".$date_to.") by ".$HTTP_SESSION_VARS['sess_user_name']." ".strftime("%Y-%m-%d %H:%M:%S")."\n";

		if($deleted){
			$saveok = $encObj->updateAccommodationTrail($encounter_nr, $history);

        	if($saveok){
				$objResponse->alert("Successfully Deleted Accommodation");
				$objResponse->addScriptCall("reloadWindow");
			}
		}
		else
			$objResponse->alert("Unable to delete accommodation");

		return $objResponse;
	}
	
	function getPatientClassInfo($encounter_nr){
		global $db;
		$objResponse = new xajaxResponse();
		$enc_obj = new Encounter;
		$ward_obj = new Ward;


		$patient = $enc_obj->getPatientNameEncInfo($encounter_nr);
		$pClass = $enc_obj->getPTypeClassification($encounter_nr);
		$custodial = $enc_obj->getPTypeClassification($ward_obj->mod_custodial);
	
		
		if ($patient) {
			$modList = $enc_obj->getPTypeClassification();
			$currentPClass = $enc_obj->getPatientTypeClassification($patient['admission_dt'],date("Y-m-d H:i:s"));
			$warnmessage = $ward_obj->pClassWarning . " " .strtoupper($currentPClass);

			if ($pClass) {
			
				if ($currentPClass && (strtoupper($pClass[0]['classification_name']) == strtoupper($currentPClass) || $pClass[0]['classification_type'] == $ward_obj->mod_custodial)) {
					$warnmessage = "";
				}
			}else{
				if ($currentPClass == $custodial[0]['classification_name']) {
					$warnmessage = "";
				}
			}
			
			
			$modListOptions = "";
			$disabled = "disabled='disabled' selected";
			foreach ($modList as $key => $value) {
				$currentValue = (isset($pClass[0]['classification_type']) && $pClass[0]['classification_type'] == ($key+1)) ? $disabled : '';
				$modListOptions .= '<option '.($currentValue).' value="'.$value['id'].'">'.$value['classification_name'].'</option>';
			}
			
			$patient['mod_options'] = $modListOptions;
			
			$date1=date_create($patient['admission_dt']);  
			$date2=date_create(date("Y-m-d H:i:s"));  
			$diff=date_diff($date1,$date2);  
			$patient['confinement_days'] = $diff->format("%a days");  
		
			$patient['admission_dt'] = date("F d, Y",strtotime($patient['admission_dt']));
			$objResponse->addScriptCall("showPclassWarning",$warnmessage);
			$objResponse->addScriptCall("loadPatientClassificationInfo",$patient);
		}
		return $objResponse;
	}

	function updateClassification($encounter_nr,$pClass_id){
		global $db;
		$objResponse = new xajaxResponse();
		$enc_obj = new Encounter;

		$res = $enc_obj->updatePatientClassification($encounter_nr,true,$pClass_id);
		
		if ($res) {
			$objResponse->alert('Successfully Updated !');
			$objResponse->addScriptCall("refreshWindow",NULL);
		}else{
			$objResponse->alert('Failed to Update.');
		}

		return $objResponse;
	}

	$xajax->processRequests();
?>