<?php
	function populateDiagnosisList($encounter_nr, $sElem,$page, $frombilling=0) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$enc_obj=new Encounter;
		$ward_obj=new Ward;
		$dept_obj=new Department;
		$pers_obj=new Personell;
		$icdIcp = new Icd_Icp();

        if ($frombilling==1) $maxRows = 20;
		$ergebnis = $icdIcp->searchIcd($encounter_nr);

		$objResponse->addScriptCall("clearList","DiagnosisList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$doctorinfo = $pers_obj->get_Person_name($result['dr']);
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

				$doctor_name = $pers_obj->concatname((is_null($doctorinfo["name_last"])) ? "" : $doctorinfo["name_last"],
													 (is_null($doctorinfo["name_first"])) ? "" : $doctorinfo["name_first"], $middleInitial);
				$doctor_name = ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);

				if ($result['conf']==1){
					$doctor_name = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
				}
				$altdesc = '';
				$objResponse->addScriptCall("addDiagnosisToList","DiagnosisList",trim($result["diagnosis_nr"]),$result["code"],$result["description"],$doctor_name, 1, $altdesc, $result["type_nr"],$result["code_alt"]);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addDiagnosisToList","DiagnosisList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
			
	function addCode($encounter,$encounter_type,$xdate,$code,$doc_nr,$create_id, $type, $mp,$description='') {

		$cdObj=new Medocs;
		$icdIcpObj=new Icd_Icp($code);
		$objResponse = new xajaxResponse();
		//$_SESSION['token'] = md5(session_id() . time());
		 #$objResponse->addAlert($type);

        // Added by Gervie 09/14/2015
        global $db;

        $is_locked = $db->GetOne("SELECT IF(IS_USED_LOCK('is_icd_added') IS NULL, FALSE, TRUE)");

        if($is_locked){
            $objResponse->alert("Adding ICD code is being processed. Please try again later.");
            return $objResponse;
        }
        else{
            $db->GetOne("SELECT GET_LOCK('is_icd_added',10)");
        }
        // end Gervie

		if (!checkICDExist($encounter, $code)) {
			if($mp=='icd'){
				$alert_fail = "Saving of the ICD failed!";
				$alert_invalid = "Invalid ICD Code!";
				$list = 'DiagnosisList';
			}else if($mp=='icp'){
				$alert_fail = "Saving of the ICP failed!";
				$alert_invalid = "Invalid ICP Code!";
				$list = 'ProcedureList';
			}else{
				return FALSE;
			}

		   if($rw=$icdIcpObj->getInfo($code)){
				$desc=$rw->FetchRow();
				
				if($mp=='icd'){
					$xcode = strtoupper($desc['code']);
				}else{
					$xcode = strtoupper($desc['code']);
				}

				$pers_obj = new Personell();

				if ($docinfo = $pers_obj->get_Person_name2($doc_nr)) {
					$doc_name = $pers_obj->concatname((is_null($docinfo["name_last"])) ? "" : $docinfo["name_last"],
													  (is_null($docinfo["name_first"])) ? "" : $docinfo["name_first"],
													  (is_null($docinfo["name_middle"])) ? "" : $docinfo["name_middle"]);
					$dept_nr = (is_null($docinfo["location_nr"])) ? 0 : $docinfo["location_nr"];
				}
				else {
					$doc_name = "";
					$dept_nr = 0;
				}

				$result=$cdObj->save_Seg_encounter_diagnoses($encounter,trim($xcode),$create_id,$desc['description'],$type);

				if($result){
					$icdInfo = $icdIcpObj->getSavedICDinfo($code,$encounter);

					addDiagProcAdt($encounter, 'Added', 'Diagnosis', trim($code), $desc['description'], $_SESSION['sess_user_name']); //Added by EJ 11/13/2014

					$objResponse->addScriptCall("addDiagnosisToList", 'DiagnosisList', $icdInfo['diagnosis_nr'], trim($code), $desc['description'], $doc_name, 1, $type);
					if($mp=='icd'){				
						$objResponse->addScriptCall("clearICDFields");
					}else{
						$objResponse->addScriptCall("clearICPFields");
					}
					
				}else{
					$objResponse->addAlert($alert_fail);
				}
			}else{
				$pers_obj = new Personell();

				if ($docinfo = $pers_obj->get_Person_name2($doc_nr)) {
					$doc_name = $pers_obj->concatname((is_null($docinfo["name_last"])) ? "" : $docinfo["name_last"],
													  (is_null($docinfo["name_first"])) ? "" : $docinfo["name_first"],
													  (is_null($docinfo["name_middle"])) ? "" : $docinfo["name_middle"]);
					$dept_nr = (is_null($docinfo["location_nr"])) ? 0 : $docinfo["location_nr"];
				}
				else {
					$doc_name = "";
					$dept_nr = 0;
				}

				$result=$cdObj->save_Seg_encounter_diagnoses($encounter,$code,$create_id,$description,0);
				if($result){
					$icdInfo = $icdIcpObj->getSavedICDinfo($code,$encounter);
					$objResponse->addScriptCall("addDiagnosisToList", 'DiagnosisList', $icdInfo['diagnosis_nr'], trim($code), $description, $doc_name, 1, $type);
					if($mp=='icd'){				
						$objResponse->addScriptCall("clearICDFields");
					}else{
						$objResponse->addScriptCall("clearICPFields");
					}
					
				}else{
					$objResponse->alert($alert_fail);
				}
			}
	}
    else{
        $objResponse->alert("Error: This code is already added!");
        $objResponse->addScriptCall("clearICDFields");
    }
        $db->GetOne("SELECT RELEASE_LOCK('is_icd_added')"); // Added by Gervie 09/14/2015
		return $objResponse;
	}// End addCode Function

	function rmvCode($enc, $code, $diagnosis_nr, $create_id){ //edited by kenneth 04-29-16

		$cdObj=new Medocs;
		$objResponse = new xajaxResponse();
		$enc_obj = new Encounter();

		$enc_obj->addDiagProcAdt($enc, 'Deleted', 'Diagnosis', $code, $desc, $create_id); //edited by kenneth 04-29-16

		if ($result=$cdObj->removeICDCode($diagnosis_nr, $create_id))
		//if ($result = $cdObj->delICD($diagnosis_nr, $create_id))
			$objResponse->addAlert("Data has been successfully deleted!");

		if($result){
			$objResponse->addScriptCall("removeAddedICD",$diagnosis_nr);
		}else{
			$objResponse->addAlert(print_r($cdObj->sql,TRUE));
		}
		return $objResponse;
	}
	//added by Christian 01-21-20
	function rmvDoctorClaim($enc,$bill_nr,$rate_type) {
		$objBilling = new Billing();
		$objResponse = new xajaxResponse();
		$refno = $bill_nr ? $bill_nr : 'T'.$enc;
		
		if(!empty($rate_type)) {
			$data = $objBilling->getDoctorCoverageBreakdown($refno);
			if($data) {
				foreach ($data as $row) {
					if($rate_type=='first_claim')
						$row['first_claim'] = 0;
					if($rate_type == 'second_claim')
						$row['second_claim'] = 0;
					$dr_claim = $row['first_claim'] + $row['second_claim'];
					$objBilling->updateDoctorCoverageBreakdown($refno,$row['hcare_id'],$row['dr_nr'],$row['role_area'],$dr_claim,$row['first_claim'],$row['second_claim']);
					$objBilling->updateclearDoctorCoverage($refno,$row['hcare_id'],$row['dr_nr'],$row['role_area'],$dr_claim);
				}
			}
		}
		return $objResponse;
	}
	//end Christian 01-21-20

	// Added by LST -- 08.18.2009
	function saveAltDesc($enc_nr, $code, $desc, $create_id) {
		$objResponse = new xajaxResponse();

		$objmdoc = new Medocs();
		if (!$objmdoc->saveAltDesc($enc_nr, $code, $desc, $create_id)) {
			$objResponse->addAlert("ERROR: ".$objmdoc->sql);
		}

		return $objResponse;
	}

    //added by jasper 06/30/2013
    function saveAltCode($enc_nr, $code, $altcode, $create_id) {
        $objResponse = new xajaxResponse();

        $objmdoc = new Medocs();
        if (!$objmdoc->saveAltCode($enc_nr, $code, $altcode, $create_id)) {
            $objResponse->addAlert("ERROR: ".$objmdoc->sql);
        }

        return $objResponse;
    }
    //added by jasper 06/30/2013

    //added by jasper 04/24/2013
    function updateAltICD($icd10values, $encnr=0) {
        $objResponse = new xajaxResponse();

        $objmdoc = new Medocs();
        $y=0;
        for($i=0;$i<count($icd10values);$i++) {

                if (checkICDExist($encnr, $icd10values[$i]['code'])) {
                    //update seg_encounter_diagnosis
                    if(updateICDEntNr($encnr, $icd10values[$i]['code'], $icd10values[$i]['entry_no'], $icd10values[$i]['alt_code'])){
                    	$y++;
                    }
                    else{
                    	$y--;
                    }

                } else {
                    //insert seg_encounter_diagnosis
                    if(addICD($encnr, $icd10values[$i]['code'], $icd10values[$i]['diag'], $icd10values[$i]['alt_code'])){
                    	$y++;
                    }
                    else{
                    	$y--;
                    }
                }
        }
        if( $y==$i )$objResponse->alert("Sequence Updated!");
        else $objResponse->alert("Failed to update sequence!");
        return $objResponse;
    }

    function checkICDExist($encnr, $code) {
        global $db;

        $strSQL = "SELECT * FROM seg_encounter_diagnosis WHERE encounter_nr = '" . $encnr . "' AND code = '" . $code . "' AND is_deleted = 0";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()>0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    //added by jasper 04/24/2013

    //added by Francis 04/26/2013
    function updateICDEntNr($encnr, $code, $entnr, $alt_code = "") {
        global $db;
        global $HTTP_SESSION_VARS;

        $modID = $HTTP_SESSION_VARS['sess_temp_userid'];


        $strSQL = "UPDATE seg_encounter_diagnosis SET entry_no ='" .$entnr. "', " .
                   "code_alt = '" .$alt_code. "', " .
                   "modify_id = '" .$modID. "', " .
                   "modify_time = NOW() " .
                   "WHERE encounter_nr = '" . $encnr . "' AND code = '" . $code . "'";

        if ($db->Execute($strSQL)) {
            if ($db->Affected_Rows()) {
                return true;
            }
        }else{
            	return false;
        }
    }

    function addICD($encnr, $code, $diag, $alt_code = "") {
        global $db;
        global $HTTP_SESSION_VARS;

        $createID = $HTTP_SESSION_VARS['sess_temp_userid'];
        $diagnosis = str_replace("'", "\'", $diag);


        $strSQL = "INSERT INTO seg_encounter_diagnosis(encounter_nr,code,description,code_alt,is_deleted,modify_id,modify_time,create_id,create_time) ".
        		  "VALUES ('".$encnr."','".$code."','".$diagnosis."', '".$alt_code."','0','$createID',NOW(),'$createID',NOW())";
        if ($db->Execute($strSQL)) {
            if ($db->Affected_Rows()) {
                return true;
            }
        }else{
            	return false;
        }
    }
    //added by Francis 04/26/2013


/*-------------------------IPD--------------------------------------*/

function populateProcedureList($encNr,$billFrmDate,$billDate) {
	global $db;
	$objResponse = new xajaxResponse();
	$enc_obj = new Encounter;
	$srv = new SegOps;

	$MyData = new stdClass;
	$MyData->encounter_nr = $encNr;
	$MyData->billFromDate = $billFrmDate;
	$MyData->BillToDate = $billDate;
	
	$opInfoVersion2 = $srv->SearchCurrentOPVersion2($MyData);
	 // $objResponse->addAlert($srv->sql);
	$objResponse->addScriptCall("clearProcList");
	if($opInfoVersion2){
		foreach ($opInfoVersion2 as $key) {
			$description_short = substr(trim($key["description"]),0,50)."...";

			if ($key['lmp_date'] == 'null' or $key['lmp_date'] == '0000-00-00') {
    			$isDelivery = false;
    		}else{
    			$isDelivery = true;
    		}


			$data->code = trim($key["code"]);
			$data->opDate = date("Y-m-d", strtotime($key["op_date"]));
			$data->opDesc = ($key["alt_desc"] != null) ? trim($key["alt_desc"]) : trim($key["description"]);
			$data->opSDesc = trim($description_short);
			$data->opRVU = number_format($key['rvu']);
			$data->opMultiplier = number_format($key['multiplier']);

			$data->opProvider = $key["provider"];
			$data->opRefno = $key["refno"];
			$data->opEntry = $key["entry_no"];
			$data->opCount = $key["op_count"];
			$data->target = "ProcedureList-body";
			$data->opEncNr = $encNr;
			$data->charge = $charge;
			$data->isDelivery = $isDelivery;
			$data->opSticker = $key["sticker_no"];
			$objResponse->addScriptCall("addProcedureToList",$data);
		}
	}

	// $opInfo = $srv->SearchCurrentOP($encNr, $billFrmDate, $billDate);
	// $objResponse->addScriptCall("clearProcList");

	// if ($opInfo) {
	// 	$rows=$opInfo->RecordCount();
	// 	while($result=$opInfo->FetchRow()) {
	// 			$description_short = $result["description"];

	// 	if (strlen($description_short)>50)
	// 		$description_short = substr(trim($result["description"]),0,50)."...";

	// 		$charge = $result["multiplier"] * $result["rvu"];

	// 		$data->code = trim($result["code"]);
	// 		$data->opDate = strftime("%m-%d-%Y", strtotime($result["op_date"]));
	// 		$data->opDesc = ($result["alt_desc"] != null) ? trim($result["alt_desc"]) : trim($result["description"]);
	// 		$data->opSDesc = trim($description_short);
	// 		$data->opRVU = $result["rvu"];
	// 		$data->opMultiplier = $result["multiplier"];
	// 		$data->opProvider = $result["provider"];
	// 		$data->opRefno = $result["refno"];
	// 		$data->opEntry = $result["entry_no"];
	// 		$data->opCount = $result["op_count"];
	// 		$data->target = "ProcedureList-body";
	// 		$data->opEncNr = $encNr;
	// 		$data->charge = $charge;

	// 	$objResponse->addScriptCall("addProcedureToList",$data);

	// 	}#end of while
	// }#end of if

	// $objResponse->addScriptCall("addProcedureToList",$data);

	return $objResponse;
}

function deleteProcedure($details){
	global $db;
	$objResponse = new xajaxResponse();
	$enc_obj = new Encounter;
	$srv = new SegOps;

	$enc_nr = $details['enc'];
	$bill_dt = $details['bdate'];
	$bill_frmdte = $details['fdate'];
	$op_code = $details['code'];

	$parent_encnr = getParentEncounterNr($enc_nr);

	if ($parent_encnr != '')
			$encounter = "('$parent_encnr','$enc_nr')";
	else
			$encounter = "('$enc_nr')";

	$details['encounter_nr'] = $encounter;

	//$delProc = $srv->delProcedure($encounter,$bill_dt, $bill_frmdte, $op_code);

	$delProc = $srv->delProcedureversion2($details);

	if($delProc){
		$objResponse->addScriptCall("rmvProcRow",$op_code, $details['opEntry'], $details['refno']);
	}else{
		$objResponse->alert("No procedure was deleted!");
	}

	return $objResponse;

}

function getParentEncounterNr($enc_nr) {
	global $db;

	$parent_encnr = '';
	$strSQL = "select parent_encounter_nr
							from care_encounter
							where encounter_nr = '$enc_nr'";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			$row = $result->FetchRow();
			$parent_encnr = $row['parent_encounter_nr'];
		}
	}
	return $parent_encnr;
}

function addProcedure($details){
	global $db;
	$objResponse = new xajaxResponse();
	$enc_obj = new Encounter;
	$srv = new SegOps;
	$objBill = new Billing();

	if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
		$tmp_dte = $details['bDate'];
	else
		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

	$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

	$opDate = strftime("%Y-%m-%d", strtotime($details['opDate']));

    if ($details['lmp_date'] == 'null') {
    	$lmp_date = '0000-00-00';
    	$isDelivery = false;
    }
    else {
    	$lmp_date = strftime("%Y-%m-%d", strtotime($details['lmp_date']));
    	$isDelivery = true;
    }
	
	$procData = array('encNr'=>$details['encNr'],
					  'billDate'=>$tmp_dte,
					  'code'=>$details['code'],
					  'opDate'=>$opDate,
					  'user'=>$details['user'],
					  'rvu'=>$details['rvu'],
					  'multiplier'=>$details['multiplier'],
					  'charge'=>$details['charge'],
					  'laterality'=>$details['laterality'],
					  'num_sessions' => $details['sess_num'],
					  'special_dates' => $details['special_dates'],
					  'lmp_date'=>$lmp_date, //Added by EJ 12/11/2014
					  'prenatal_dates' => $details['prenatal_dates'], //Added by EJ 12/11/2014
					  'desc' => $details['desc'],
					  'sticker_no' => $details['sticker_no'] //Added by JEFF 06/27/2018
				);
	$has_irregularity = $objBill->isClearedForRVS_Details($details['code'],$details['laterality'],$details['encNr']);
	// var_dump($has_irregularity);die();
	if($has_irregularity){
		$objResponse->addScriptCall("NotifyIrregularity",$has_irregularity[0],$has_irregularity[1],$has_irregularity[2],$has_irregularity[3]);
	}else{
		$addResult = $srv->addProcedure($procData);
		$refno = $srv->getMiscOpRefNo($procData['billDate'], $procData['encNr']);
		$entry_no = $srv->GetEntryNo($procData, $refno);
		
		if($addResult){
			$tmpOpDate = strftime("%m-%d-%Y", strtotime($opDate));

			// $entry_no = $srv->GetEntryNo();

			$data->target = "ProcedureList-body";
			$data->code = $details['code'];
			$data->opDesc = $details['desc'];
			$data->opDate = $tmpOpDate;
			$data->opRVU = $details['rvu'];
			$data->opMultiplier = $details['multiplier'];
			$data->charge = $details['charge'];
			$data->laterality = $details['laterality'];
			$data->opRefno = $refno; // added by Nick, 3/6/2014
			$data->opCount = 1;
			$data->opSticker = $details['sticker_no']; // added jeff, 6/27/18
			if($entry_no){
				$data->opEntry = $entry_no;
			}else{
				$data->opEntry = 1;
			}
			$data->isDelivery = $isDelivery;

	        $objResponse->addScriptCall('Procedures.setSpecialProcedureDetails',$data->code, 0);
			$objResponse->addScriptCall("addProcedureToList",$data,true);
		}else{
			$objResponse->alert("Failed to add procedure !");
		}
	}

	// $objResponse->alert($opDate);
	return $objResponse;

}

//added by Nick, 3/1/2014
function updateIcdCode($enc_nr, $code, $altcode, $user_id){
	$objMedocs = new Medocs;
	$objResponse = new xajaxResponse();

	$data = array($altcode,$user_id,$enc_nr,$code);

	$update_cnt = $objMedocs->updateIcdAltCode($data);

	if($update_cnt > 0 ){
		$objResponse->alert("Alt code updated!");
	}else if($update_cnt <= 0){
		$objResponse->alert("No Alt code updated!");
	}else if(!$update_cnt){
		$objResponse->alert("Failed to update Alt code!");
	}

	return $objResponse;
}

//added by Nick, 3/1/2014
function updateIcdDesc($enc_nr, $code, $desc, $user_id){
	$objMedocs = new Medocs;
	$objResponse = new xajaxResponse();

	$data = array($desc,$user_id,$enc_nr,$code);

	$update_cnt = $objMedocs->updateIcdAltDesc($data);

	if($update_cnt > 0 ){
		$objResponse->alert("Alt description updated!");
	}else if($update_cnt <= 0){
		$objResponse->alert("No Alt description updated!");
	}else if(!$update_cnt){
		$objResponse->alert("Failed to update Alt description!");
	}

	return $objResponse;
}

//added by Nick, 3/4/2014
function updateIcpDesc($refno, $code, $desc, $opEntry){
	$objMedocs = new Medocs;
	$objResponse = new xajaxResponse();

	$data = array($desc,$refno,$code,$opEntry);

	$rs = $objMedocs->updateIcpAltDesc($data);
	if($rs){
		if($rs > 0){
			$objResponse->alert('Description updated');
		}else{
			$objResponse->alert('No description updated');
		}
	}else{
		$objResponse->alert('Failed to update description');
	}

	return $objResponse;
}

//added by Nick, 4/15/2014
function updateIcdSequence($encounter_nr,$icd_list){
	$objMedocs = new Medocs;
	$objResponse = new xajaxResponse();
	$rs = $objMedocs->updateIcdSequence($encounter_nr,$icd_list);
	$objResponse->alert("Sequence Updated!");
	return $objResponse;
}

//added by Jasper Ian Q. Matunog 11/11/2014
function updateWellbabyDx($encno, $dx) {
	global $db;
	$objResponse = new xajaxResponse();
	$data['encounter_nr'] = $encno;
	$data['er_opd_diagnosis'] = $dx;

	$pk = array('encounter_nr');
	foreach ($data as $key => &$val) {
		$val = $db->qstr($val);
	}

	$rs = $db->Replace('care_encounter', $data, $pk);
	if($rs){
		if (substr($encno, 4, 1) == 9) { //wellbaby only
			$objResponse->alert('Wellbaby admitting diagnosis updated!');		
		}
	}else{
		$objResponse->alert('Error on update admitting diagnosis!');
		// return false;
	}
	return $objResponse;
}

//added by EJ 11/13/2014
function addDiagProcAdt($enc, $act, $type, $code, $desc, $encdr) {
	global $db;
	$objResponse = new xajaxResponse();
	$enc_obj = new Encounter();
    $objBill = new Billing();

	$enc_obj->addDiagProcAdt($enc, $act, $type, $code, $desc, $encdr);

	return $objResponse;
}

/*
*@author : art 02/03/15 for spmc145
*/
function getProcedureDetails($enc,$code,$refno){
	global $db;
	$objResponse = new xajaxResponse();
	$icdIcp = new Icd_Icp();
	$rowSrc = '';
	$rs = $icdIcp->getProcedureDetails($enc,$code,$refno);
	$count = 1;
		foreach ($rs as $row) {
			$rowSrc .= 	'<div id="date'.$count.'"><table>
							<tr id="opDateBox-date-'.$count.'">
    	                		<td width="*" align="left">                           
    	                		    <strong> Date of Operation '.$count.'</strong>
    	                		    <input type="hidden" name="refno_'.$count.'" id="refno_'.$count.'" value="'.$row['refno'].'">
    	                		    <input type="hidden" name="entry_no_'.$count.'" id="entry_no_'.$count.'" value="'.$row['entry_no'].'">
    	                		</td>
    	                		<td width="*" align="left">  
    	                		    <input type="text" id="op_date_'.$count.'" name="op_date_'.$count.'" value="'.$row['op_date'].'" class="picker" maxlength="10" size="10" />
    	                		</td>
    	            		</tr>
    	            		<tr id="lmp-date-'.$count.'">
    	                		<td width="*" align="left">                            
    	                		    <strong> LMP Date </strong>
    	                		</td>
    	                		<td width="*" align="left"> 
    	                		    <input type="text" id="lmp_date_'.$count.'" name="lmp_date_'.$count.'" value="'.$row['lmp_date'].'" class="picker" maxlength="10" size="10" />
    	                		</td>
    	            		</tr>';
    	    if ($row['is_prenatal']) {
    	    	$prenatal = explode(",",trim(preg_replace('/\s+/', '', $row['prenatal_dates']),","));
    	    	$cnt = 1;
    	    	foreach ($prenatal as $dates) {
    	    		$rowSrc .= 	'<tr id="prenatal-date-'.$cnt.'">
    	    	                	<td width="*" align="left">                           
    	    	                	    <strong> Pre-natal Date # '.$cnt.'</strong>
    	    	                	</td>
    	    	                	<td width="*" align="left">  
    	    	                	    <input type="text" id="prenatal_date_'.$count.'_'.$cnt.'" name="prenatal_date_'.$count.'" value="'.$dates.'" class="picker" maxlength="10" size="10" />
    	    	                	</td>
    	    	                </tr>';
    	    	    $cnt++;
    	    	}#end foreach
    		}#end if
    	    $rowSrc .= '<tr><td><hr></td><td><hr></td></tr></table></div>';
    	    $count++;
		}#end foreach
		$objResponse->addScriptCall('prependEditDateDialog',$rowSrc);
	return $objResponse;
}
/*
*@author : art 02/03/15 for spmc145
*/
function updateLmpDate($op_date,$lmp_date,$prenate_dates,$ops_code,$refno,$entry_no){
	global $db;
	$objResponse = new xajaxResponse();
	$icdIcp = new Icd_Icp();
	$update = $icdIcp->updateLmpDate($op_date,$lmp_date,$prenate_dates,$ops_code,$refno,$entry_no);
	return $objResponse;
}

 
/**
 * For saving/update of filter card number.
 * @author Jeff Ponteras - June 29, 2018
 * @param  int $encounter            - Encounter Number
 * @param  varchar $ops_code         - Operation code
 * @param  varchar $filtercardnumber - Filter Card Number
 * @return boolean / alert           - Alert message for return true or false
 */
function saveFilterCardNumber($enc,$code,$refno,$filtercardnumber){
	global $db;
	$objResponse = new xajaxResponse();

	$saveSQL = "UPDATE seg_misc_ops_details AS smod
					SET
					  smod.`sticker_no` =".$db->qstr($filtercardnumber)."
					WHERE smod.`refno` = ".$db->qstr($refno)."
					AND
					smod.`ops_code` = ".$db->qstr($code);

		$result = $db->Execute($saveSQL);

		if ($result) {
			// $objResponse->alert("Success: Filter Card Number successfully saved!");
		} else{
			// $objResponse->alert("Error: Filter Card Number failed to save.");
		}

	return $objResponse;
}
/* END Jeff */


/*-------------------------IPD end----------------------------------*/

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/billing_new/ajax/icd_icp.common.php");
#added by VAN 04-17-08
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

require_once($root_path.'include/care_api_classes/billing/class_ops_new.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require($root_path.'include/care_api_classes/class_medocs.php');
require($root_path.'include/care_api_classes/class_icd10.php');
require($root_path.'include/care_api_classes/class_caserate_icd_icp.php');
require($root_path.'include/care_api_classes/billing/class_billing_new.php');

$xajax->processRequests();
?>