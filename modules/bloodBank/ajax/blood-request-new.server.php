<?php
function populateRequestListByRefNo($refno = 0, $ref_source = 'LB', $fromSS = 0, $discount = 0, $discountid = '')
{
	global $db;
	$objResponse = new xajaxResponse();
	$srvObj = new SegLab();
	$total_quantity = 0;
	$disablebtupdate = 0;

	if (!$discount)
		$discount = 0;

	#$objResponse->alert('refno = '.$ref_source);
	$rs = $srvObj->getAllLabInfoByRefNo($refno, $ref_source, $fromSS, $discount, $discountid);

	#$objResponse->alert('sql = '.$srvObj->sql);

	if ($rs) {
		while ($result = $rs->FetchRow()) {
			$hasserial = 0;
			#blood received
			$sql_rec = "SELECT * FROM seg_blood_received_sample WHERE refno='" . trim($refno) . "' AND service_code='" . $result['service_code'] . "'";

			$res_rec = $db->Execute($sql_rec);
			$row_rec_count = $res_rec->RecordCount();
			$row_rec = $res_rec->FetchRow();

			if ($row_rec_count) {
				if ($row_rec['status'] == 'none')
					$withsample = 'NO';
				else
					$withsample = mb_strtoupper($row_rec['status']);

				$sql_rec_d = "SELECT s.name, r.*
                              FROM seg_blood_received_details AS r
                              INNER JOIN seg_lab_services AS s ON s.service_code=r.service_code
                              WHERE r.refno = '" . trim($refno) . "' AND r.service_code='" . $result['service_code'] . "'";
				$res_rec_d = $db->Execute($sql_rec_d);
				$i = 1;

				$details_rec = '<br>STATUS : ' . $withsample . ' SAMPLE';
				while ($row_rec_d = $res_rec_d->FetchRow()) {
					if ($row_rec_d["status"] == 'received')
						$statusrec = 'Date Received ' . date("m/d/Y h:i A", strtotime($row_rec_d["received_date"]));
					else
						$statusrec = 'Not Yet Received ';

					$details_rec .= '<br>' . $i . '.) Sample #: &nbsp;&nbsp;<font color=\'#000066\'><b>' . $row_rec_d["ordering"] . ' </b></font><br>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=\'#000066\'><b> ' . $statusrec . ' </font></b>';

                    if($row_rec_d["serial_no"]) $hasserial = 1;

					$i++;
				}
			} else {
				$withsample = 'NO SAMPLE';
			}

			$name = $result["name"];
			#check if request_doctor is not from spmc
			if ($result['request_doctor'])
				$doctor = $result['request_doctor_name'];
			else
				$doctor = $result['manual_doctor'];

			if (strlen($name) > 40)
				$name = substr($result["name"], 0, 40) . "...";

			$r = \SegHis\modules\costCenter\models\BloodBankRequestSearch::search(array(
				'referenceNo' => $result['refno'],
				'serviceCode' => $result['service_code'],
			));

			$request = array(
				'allowDelete' => $r->allowDelete ? 1 : 0,
				'message' => ($result['request_flag'] == 'charity' && $result['is_served'] == 1) ? $r->getItemStatus($ref_source)  : $r->getMessage(),
				'warning' => $r->getWarning()
			);

			$total_quantity += $result['quantity'];

			$cashier_c = new SegCashier;
			$creditgrant = $cashier_c->getRequestCreditGrants($refno,$ref_source,$result['service_code']);

			$result['discounted_price'] -= ((float) $creditgrant[0]['total_amount']/$result['quantity']);
			$serviceinfo = $srvObj->getLabServicesInfo("(s.service_code='".urlencode($result['service_code'])."' OR s.service_code ='".$result['service_code']."') AND s.group_code = sg.group_code");

            $serdata=$serviceinfo->FetchRow();

            if($serdata['is_btreq'] && $hasserial) $disablebtupdate = 1;

			$objResponse->call("initialRequestList",
				$result['service_code'],
				$result['group_code'],
				$name,
				stripslashes($result['clinical_info']),
				$result['request_doctor'],
				$doctor,
				$result['is_in_house'],
				$result['price_cash_orig'],
				$result['price_charge'],
				$result['hasPaid'],
				$result['is_socialized'],
				$result['approved_by_head'],
				$result['remarks'],
				$result['quantity'],
				$result['qty_received'],
				number_format($result['discounted_price'], 2, '.', ''),
				$result['request_dept'],
				$result['request_flag'],
				$withsample,
				$details_rec,
				$request,
				$result['is_forward'],
				$result['is_served']
			);

		}

		$objResponse->assign("total_quantity", "value", $total_quantity);
		$objResponse->assign("disablebtupdate", "value", $disablebtupdate);
	} else {
		$objResponse->call("emptyIntialRequestList");
	}

	$objResponse->call("refreshDiscount");
	return $objResponse;
}# end of function populateRequestListByRefNo

function existSegOverrideAmount($ref_no){
		global $db;

		if (!$ref_no)
			return FALSE;

		$sql="SELECT *	FROM seg_override_amount
					WHERE ref_no='".$ref_no."' AND ref_source='LD'";

		if ($buf=$db->Execute($sql)){
			if($buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}#end of function existSegCharityAmount

#added by VAN 08-11-2010
function updateRequest($usr, $pw, $refno, $discount_given){
	 global $db, $HTTP_SESSION_VARS;
	 $objResponse = new xajaxResponse();
	 $user= new Access($usr,$pw);

	 if($user->isKnown()&&$user->hasValidPassword()&&$user->isNotLocked()){

			if ($HTTP_SESSION_VARS['sess_user_personell_nr'])
				$personnel_nr = $HTTP_SESSION_VARS['sess_user_personell_nr'];
			elseif ($HTTP_SESSION_VARS['sess_temp_personell_nr'])
				$personnel_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

			$grand_dte =  date('Y-m-d H:i:s');
			$ref_source = 'LD';

			/*if (existSegOverrideAmount($refno)){
				$sql="UPDATE seg_override_amount
						SET grant_dte=NOW(), personnel_nr=".$personnel_nr.", amount=".$discount_given."
						WHERE ref_no='".$refno."' AND ref_source='".$ref_source."'";
			}else{*/
				$sql = "INSERT INTO seg_override_amount (ref_no, ref_source, grant_dte, personnel_nr, amount) ".
					 "\n VALUES('".$refno."', '".$ref_source."', '".$grand_dte."', '".$personnel_nr."' , '".$discount_given."' )";
			#}

			#$db->StartTrans();
			$ok = $db->Execute($sql);
			#$objResponse->alert($sql);
			if ($ok){
				#$db->CommitTrans();
				$objResponse->alert('Request has been successfully granted');
				$objResponse->call('submitform');
			}else{
				#$db->RollbackTrans();
				$objResponse->alert('Saving Data failed');
			}

	 }else{
		 $objResponse->alert('Your login or password is wrong');
	 }

	 return $objResponse;
}

	#added by VAN 01-10-10
	function checkwithDonor($refno){
			global $db;
			$objResponse = new xajaxResponse();

			$sql = "SELECT d.*, i.*
							FROM seg_blood_donor_request AS d
							INNER JOIN seg_donor_info AS i ON i.donor_id=d.donor_id
							WHERE refno='".$refno."'";
			$rs = $db->Execute($sql);

			$with_donor = 0;
			if ($rs){
				$count = $rs->RecordCount();
				$row = $rs->FetchRow();

				if ($count){
						$with_donor = 1;
						$name = trim($row['first_name'])." ".trim($row['middle_name'])." ".trim($row['last_name']);
						$relationship = trim($row['relationship']);
				}
			}
			return $objResponse;
	}
    
#added by VAS 03-21-2012
function updateCoverage($enc_nr, $type, $nr=null) {
    global $db;

    $objResponse = new xajaxResponse();
    $amount = 0;
    
    if ($enc_nr) {
        if ($type=='phic') {
            $bill_date = strftime("%Y-%m-%d %H:%M:%S");
            
            $bc = new Billing($enc_nr, $bill_date);
            $bc->checkExistingInsuranceCreditCollectionNBB();
            $bc->getConfinementType();
            $amount = 0;

            define('__HCARE_ID__',18);

            $total_coverage = $bc->getActualSrvCoverage(__HCARE_ID__);
            
            if ($bc->nbbInsurance && !$bc->isPayward($enc_nr)) {
				$bc->confinetype_id = $bc->_NBBconf;
			}
            $total_benefits = $bc->getConfineBenefits('HS', NULL, 0, TRUE);
            #$total_benefits = 2240;
            $covered = 0;
            
            if ($nr)
            {
                $query = "SELECT SUM(quantity*price_charge) FROM seg_lab_servdetails WHERE refno=".$db->qstr($nr);
                /*$query = "SELECT SUM(d.quantity*d.price_cash) 
                            FROM seg_lab_servdetails d
                            INNER JOIN seg_lab_serv h ON h.refno=d.refno
                            WHERE h.ref_source='BB' AND refno=".$db->qstr($nr);*/
                #$objResponse->alert($query);            
                $covered = (float) $db->GetOne($query);
            }
            $additional = $db->GetOne("SELECT SUM(amountxlo) FROM seg_additional_limit WHERE is_deleted IS NULL AND encounter_nr=".$db->qstr($enc_nr)); #added by art 11/20/14

            $objResponse->assign('coverage','value', (float)$additional +(float)$total_benefits-(float)$total_coverage+$covered);
            $objResponse->call('refreshTotal');
        }elseif ($type=='LINGAP') {
            $lc = new SegLingapPatient();
            $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($enc_nr));
            $amount = $lc->getBalance($pid);
            $objResponse->assign('coverage','value', $amount);
            $objResponse->call('refreshTotal');
        }
        elseif ($type=='CMAP') {
            $amount = 0;
            $pc = new SegCMAPPatient();
            $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($enc_nr));
            $amount = $pc->getBalance($pid);

            $objResponse->assign('coverage','value', $amount);
            $objResponse->call('refreshTotal');
        }
        else {
            $objResponse->assign('cov_type','innerHTML', '');
            $objResponse->assign('cov_amount','innerHTML', '');
            $objResponse->assign('coverage','value', -1);
            $objResponse->call('refreshTotal');
        }

    }
    else
        $objResponse->assign('cov_amount','innerHTML', '');
    return $objResponse;
}

//Added by Jarel 12/12/13 use to update seg_blood_borrow_info if has borrowed blood
function replacedBlood($enc_nr, $refno)
{
 	$objResponse = new xajaxResponse();
	$labObj = new SegLab();
	$ok = $labObj->replacedBlood($enc_nr, $refno);
	
	if (!$ok) {
		$objResponse->alert('Failed');
	}
	
	return $objResponse;
}

#---------------------

//added by Nick, 1/14/2014
function setDbData($refno,$code,$isOld){

	global $db;
	$objResponse = new xajaxResponse();
	$srvObj=new SegLab();

	$data = $srvObj->getPending($refno,$code,"string");

	// $objResponse->alert($srvObj->temp_sql);

	if($isOld == 0)
		$objResponse->call("set_old_db_data",$data);
	else{
		$objResponse->call("set_new_db_data",$data);
		$objResponse->call("getPendings");
	}

	return $objResponse;

}
//end nick

function savePledgeCommitment($data){
	global $db;
	$objResponse = new xajaxResponse();

	$components = implode(",", $data['components']);

	$name_of_watcher = utf8_decode($data['name_of_watcher']);
	
	$sql = "INSERT INTO seg_pledge_commitment_details (`batch_nr`, `encounter_nr`, `pid`, `donated_to`, `blood_type`, `no_of_units`, `components`, `watcher_name`, `create_time`, `create_id`) 
		VALUES (".$db->qstr($data['refno']).",".$db->qstr($data['encounter_nr']).",".$db->qstr($data['pid']).",".$db->qstr($data['to_be_donated']).",".$db->qstr($data['blood_type']).",".$db->qstr($data['no_of_units']).",".$db->qstr($components).",".$db->qstr($name_of_watcher).",".$db->qstr(date("Y-m-d H:i:s")).",".$db->qstr($_SESSION['sess_user_name']).")";

	$db->BeginTrans();

	$success = $db->Execute($sql);

	if($success){
		$db->CommitTrans();
		$objResponse->alert("Successfully saved!");
		$objResponse->call("refreshWindow");
	}else{
		$db->RollbackTrans();
		$objResponse->alert("Error:" .$db->ErrorMsg());
	}

	return $objResponse;                  
}

function updatePHIC($enc_nr, $type='phic', $nr=null) {
	global $db;

    $objResponse = new xajaxResponse();
    $amount = 0;    
        
    if ($enc_nr) {
        if ($type=='phic') {
            $bill_date = strftime("%Y-%m-%d %H:%M:%S");
            
            $bc = new Billing($enc_nr, $bill_date);

            $bc->getConfinementType();
            $amount = 0;

            define('__HCARE_ID__',18);

            $total_coverage = $bc->getActualSrvCoverage(__HCARE_ID__);
            $total_benefits = $bc->getConfineBenefits('HS', NULL, 0, TRUE);
            $covered = 0;
            
            if ($nr)
            {
                $query = "SELECT SUM(quantity*price_charge) FROM seg_lab_servdetails WHERE refno=".$db->qstr($nr);
                $covered = (float) $db->GetOne($query);
            }
            $additional = $db->GetOne("SELECT SUM(amountxlo) FROM seg_additional_limit WHERE is_deleted IS NULL AND encounter_nr=".$db->qstr($enc_nr));
            $objResponse->assign('cov','value', (float)$additional + (float)$total_benefits-(float)$total_coverage+$covered);
            // $objResponse->call('refreshTotal');
        }
    }
   return $objResponse;
}

function getParticular($particular,$fieldDesc=false){

    switch ($particular){
        case 0: //Release of Result
            $particular_name = ($fieldDesc === true) ? "Result date" : "date_released";
            break;
        case 1: //Issuance of Blood
            $particular_name = ($fieldDesc === true) ? "Issuance date" : "issuance_date";
            break;
        case 2: //Returned Blood
            $particular_name = ($fieldDesc === true) ? "Returned date" : "date_return";
            break;
        case 3: //Re-Issue Blood
            $particular_name = ($fieldDesc === true) ? "Re-issued date" : "date_reissue";
            break;
        case 4: //Consumed Blood
            $particular_name = ($fieldDesc === true) ? "Consumed date" : "date_consumed";
            break;
        default:
            $particular_name = "";
            break;
    };
    return $particular_name;
}

function hasEmptyFields($fields,$result){
    $hasEmptyField = false;
    if ($fields)
        foreach ($fields as $fieldName) {
            if (!$result[$fieldName]){
                $hasEmptyField[] = getRequiredField($fieldName);
            }
        }
    if($hasEmptyField){
        $emptyList = "";
        foreach ($hasEmptyField as $value){
            $emptyList .= $value . ",";
        }
        $hasEmptyField = rtrim($emptyList,",");
    }
    return $hasEmptyField;
}

function getRequiredField($fieldName){
    $fields = array("dept" => "Department/Ward",
        "component"=>"Component",
        "blood_source"=>"Blood Source",
        "started_date"=>"Date Started",
        "done_date"=>"Date Done",
        "result"=>"Result Type",
        "date_released"=>"Result Date",
        "issuance_date"=>"Issuance Date",
        "date_return"=>"Return Date");
    return $fields[$fieldName];
}

function setParticularDate($particular,$refno,$serial_no){
    $srvObj = new SegLab();
    $objResponse = new xajaxResponse();
    $particular_name = getParticular($particular);
    $result = $srvObj->getBloodReceivedStatusDate($refno, $serial_no);
    $checkValid = true;
    if (!$result){
        $checkValid = false;
        $objResponse->alert("Serial Number does not match");
    }else if ($result[$particular_name]){
        $checkValid = false;
        $fieldDescription = true;
        $objResponse->alert(getParticular($particular,$fieldDescription)." and time already filled");

    }else {
        $emptyFieldErrMsg = " Please fillout all the fields";
        switch ($particular){
            case 0: //Release of Result
                $fields = array("dept","component","blood_source","started_date","done_date","result");
                if ($r = hasEmptyFields($fields,$result)){
                    $checkValid = false;

                    $objResponse->alert("Lacking of entries(".$r.")".$emptyFieldErrMsg);
                }
                break;
            case 1: //Issuance of Blood
                $fields = array("dept","component","blood_source","started_date","done_date","result");

                if ($r = hasEmptyFields($fields,$result)){
                    $checkValid = false;
                    $objResponse->alert("Lacking of entries(".$r.")".$emptyFieldErrMsg);
                }
                break;
            case 2: //Returned Blood
                $fields = array("dept","component","blood_source","started_date","done_date","result","issuance_date");
                if ($r = hasEmptyFields($fields,$result)){

                    $checkValid = false;
                    $objResponse->alert("Lacking of entries(".$r.")".$emptyFieldErrMsg);
                }else{
                    $time = new DateTime($result['issuance_date']);
                    $diff = $time->diff(new DateTime());
                    $returnMaxMinutes = 30;
                    $minutes = ($diff->days * 24 * 60) +
                        ($diff->h * 60) + $diff->i;
                    if ($minutes > $returnMaxMinutes){
                        $checkValid = false;
                        $objResponse->alert("30 minutes have passed since the time of issue, can no longer return");
                    }else{
                        $objResponse->call(addReturnReason);
                    }
                }
                break;
            case 3: //Re-Issue Blood
                $fields = array("dept","component","blood_source","started_date","done_date","result","issuance_date","date_return");
                if ($r = hasEmptyFields($fields,$result)){
                    $checkValid = false;
                    $objResponse->alert("Lacking of entries(".$r.")".$emptyFieldErrMsg);
                }

                break;
            case 4: //Consumed Blood
                $fields = array("dept","component","blood_source","started_date","done_date","result","issuance_date");
                if ($r = hasEmptyFields($fields,$result)){
                    $checkValid = false;
                    $objResponse->alert("Lacking of entries(".$r.")".$emptyFieldErrMsg);
                }
                break;
            default:
                break;
        }
    }

    if ($checkValid === false) {
        $objResponse->assign("particulars","value","");
    }

    $objResponse->assign("trans-form","style","pointer-events:all");
    return $objResponse;
}

function saveTransfusion($refno,$serial_no,$r_date,$particular,$return_reason=false){

    $srvObj = new SegLab();
    $objResponse = new xajaxResponse();
    $particular_name = getParticular($particular);

    $res = $srvObj->updateParticularDate($refno, $serial_no, $r_date,$particular_name,$return_reason);
    if ($res){
        $objResponse->alert("Successfully submitted the record");
        $objResponse->assign("particulars","value","");
    }
    return $objResponse;
}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_access.php');

require_once($root_path.'include/care_api_classes/class_cashier.php');

#added by VAS 03-21-2012
require_once($root_path."include/care_api_classes/billing/class_billing.php");
require_once($root_path."include/care_api_classes/sponsor/class_lingap_patient.php");
require_once($root_path."include/care_api_classes/sponsor/class_cmap_patient.php");

require_once($root_path."frontend/bootstrap.php");

$xajax->processRequest();