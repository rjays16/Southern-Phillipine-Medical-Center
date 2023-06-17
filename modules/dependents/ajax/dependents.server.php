<?php
	
	function populateDependentsList($pid) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objHistory = new stdClass; // added rnel create new instance of stdClass
		
		$objResponse = new xajaxResponse();
		$dependent_Obj=new SegDependents(); 
		$offset = $page * $maxRows;
		
		$ergebnis=$dependent_Obj->getAllDependents($pid);
		#$objResponse->addAlert($dependent_Obj->sql);
		$rows=0;

		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			
			while($result=$ergebnis->FetchRow()) {

				$middleInitial = "";
				if (trim($result['name_middle'])!=""){
					$thisMI=split(" ",$result['name_middle']);	
					foreach($thisMI as $value){
						if (!trim($value)=="")
						$middleInitial .= $value[0];
					}
					if (trim($middleInitial)!="")
					$middleInitial .= ".";
				}

				/* added and modify by rnel */
				
				$objHistory->dependent_pid = $result['dependent_pid'];
				$objHistory->relationship = $result['relationship'];
				$objHistory->date_birth = $result['date_birth'];
				$objHistory->sex = $result['sex'];
				$objHistory->civil_status = $result['civil_status'];

				/* end rnel */
				
				
				/**
				 * added by rnel 08-16-2016
				 * get all the history of certain dependent
				 */
				
				$action_date = array();
				$objHistory->new_date = array();
				$objHistory->action_taken = array();
				$objHistory->action_personnel = array();
				$objHistory->oldV = array();

				$histories = $dependent_Obj->getDependent($result['dependent_pid']);
				$histories2 = $dependent_Obj->getDependent2($result['dependent_pid'], $pid);
				
				if($histories) {
					while ($history=$histories->FetchRow()) {

						$action_date[] = $history['action_date'];
						$objHistory->action_personnel[] = $history['action_personnel'];
						$objHistory->action_taken[] = $history['action_taken'];

					}

					//format the time to human readable
					foreach ($action_date as $value) {
						$format = 'Y-m-d H:i:s';
						$date = DateTime::createFromFormat($format, $value);
						$objHistory->new_date[] = $date->format('F j, Y h:i A');
					}
				} 

				if($histories2) {
					
					while ($history2 = $histories2->FetchRow()) {
						$old = explode("\n", $history2['history']);
						
					}

					foreach ($old as $oldValue) {
						$objHistory->oldV[] = "\n".$oldValue;
					}
				}
				
				/* end rnel */
				
				
				$dependent_name = $result['name_last'].", ".$result['name_first']." ".$middleInitial;
				$dependent_name = ucwords(strtolower($dependent_name));

				/*$objHistory->dependent_name = htmlspecialchars($dependent_name); *///modify by rnel
				$objHistory->dependent_name = htmlentities($dependent_name);
				
				if (is_numeric($result['person_age'])){	
					if ($result['person_age']==1)
						$age = $result['person_age']." year";
					elseif (!$result['person_age'])
						$age = "unknown";
					elseif($result['person_age']>1)
						$age = $result['person_age']." years";
				}elseif (!$result['person_age']){			
					$age = "unknown";
				}else
					$age = $result['person_age'];

				$objHistory->pid = $pid; // modify by rnel
				$objHistory->age = $age; // modify by rnel
				
				// $objResponse->addScriptCall("initialDependentList", json_encode($objHistory)); // modify by rnel //commeneted by kemps 9/13/2017
				$objResponse->addScriptCall("initialDependentList", html_entity_decode(json_encode($objHistory))); //modified by kemps 9/13/2017
			}#end of while
		} #end of if
		
		if (!$rows) $objResponse->addScriptCall("initialDependentList",NULL);
		
		return $objResponse;
	}

	//modify rnel
	function deleteDependent($pid, $id){
		$objResponse = new xajaxResponse();

		$dependent_Obj=new SegDependents();

		global $db;

		// $del = $dependent_Obj->deleteDependent($pid, $id);

		$dependent = $db->GetRow("SELECT * FROM seg_dependents WHERE parent_pid = '{$pid}' AND dependent_pid = '{$id}'");
		$data['parent_pid'] = $dependent['parent_pid'];
		$data['dependent_pid'] = $dependent['dependent_pid'];
		$data['relationship'] = $dependent['relationship'];

		$monitor = $dependent_Obj->dependentMonitoring($data, 'deleted');
		$del = $dependent_Obj->deleteDependent($pid, $id);
		// if($del && $monitor) {
		// 	$objResponse->alert("Dependent was deleted from this employee.");
		// 	$objResponse->addScriptCall('removeItem', $id);
		// }

		if($monitor) {
			$objResponse->alert("Dependent was deleted from this employee.");
			$objResponse->addScriptCall('removeItem', $id);
		}
		else{
			$objResponse->alert("Error.");
		}

		//$objResponse->alert($sql);

		return $objResponse;
	}
 
	# Added by: JEFF
	# Date: 08-18-17
	# Purpose: To update relationship of dependents using xajax.
	function changeRelation($rel,$id,$pid){

		$objResponse = new xajaxResponse();
		$dependent_Obj=new SegDependents();

		global $db;

		$new_rel = $dependent_Obj->changeRelation($rel,$id,$pid);

		if ($new_rel) {
			$objResponse->addScriptCall('window.location.reload');
		}

		return $objResponse;
	}
	#Ended by: JEFF 08-18-17

	function deleteAllDependents($pid){
		$objResponse = new xajaxResponse();
		$objDependent = new SegDependents();

		global $db;
		
		$sql = "SELECT * FROM seg_dependents WHERE parent_pid = '{$pid}' AND status = 'member'";
		$dependent = $db->Execute($sql);

		while($row = $dependent->FetchRow()) {
			$data['parent_pid'] = $row['parent_pid'];
			$data['dependent_pid'] = $row['dependent_pid'];
			$data['relationship'] = $row['relationship'];

			$objDependent->dependentMonitoring($data, 'deleted');
		}

		$del = $objDependent->deleteAllDependent($pid);

		if($del) {
			$objResponse->alert("All dependents of this employee were deleted.");
			$objResponse->addScriptCall("emptyIntialRequestList", NULL);
		}
		else
			$objResponse->alert("Error");

		return $objResponse;
	}

	//modify rnel
	function addDependent($data){
		$objResponse = new xajaxResponse();
		$objDependent = new SegDependents();

		global $db;

		$old = $db->GetOne("SELECT dependent_pid FROM seg_dependents WHERE parent_pid=".$db->qstr($data['parent_pid'])." AND dependent_pid=".$db->qstr($data['dependent_pid']));

        if($old){
            $update = $objDependent->updateExistingDependent($data);
            $monitor = $objDependent->dependentMonitoring($data, 'activated');

            if($monitor) {
                $objResponse->alert('Dependent was successfully added.');
                $objResponse->addScriptCall('window.location.reload');
            }
            else
                $objResponse->alert('Error');
        }
        else {
            $add = $objDependent->addDependentNew($data);
            $monitor = $objDependent->dependentMonitoring($data, 'activated');

            if ($monitor || $add) {
                $objResponse->alert("Dependent was successfully added.");
                $objResponse->addScriptCall('window.location.reload');
            }
            else
                $objResponse->alert("Error");
        }

		return $objResponse;
	}

	function dependentHistory($dependentId) {
		$objResponse = new xajaxResponse;
		$objDependent = new SegDependents;
		$objDependent->dependentHistory($dependentId);	
		return $objResponse;
	}
	
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path."modules/dependents/ajax/dependents.common.php");
	#added by VAN 04-17-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require_once($root_path.'include/care_api_classes/class_seg_dependents.php');
	include_once($root_path.'include/care_api_classes/class_paginator.php');
		 
	$xajax->processRequests();
?>