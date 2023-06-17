<?php

function populateRequestListByRefNo($refno = 0, $ref_source = 'LB', $fromSS = 0, $discount = 0, $discountid = '')
{
	global $db;
	$objResponse = new xajaxResponse();
	$srvObj = new SegLab();

	if (!$discount)
		$discount = 0;

	#$objResponse->alert('refno = '.$ref_source);
	$rs = $srvObj->getAllLabInfoByRefNo($refno, $ref_source, $fromSS, $discount, $discountid);

	#$objResponse->alert('sql = '.$srvObj->sql);

	if ($rs) {
		while ($result = $rs->FetchRow()) {
			$name = $result["name"];
			if (strlen($name) > 40)
				$name = substr($result["name"], 0, 40) . "...";

			if (empty($result['every_hour']))
				$result['every_hour'] = '';

			$is_serial = 0;
			$change_icon = 0;
			#check if there is serial in the request and already have a previous check-in
			$serial_row = $srvObj->getSerialCountInfo($refno, $result['service_code']);
			#$objResponse->alert($srvObj->sql);
			if ($rs) {
				$is_serial = 1;
				#$objResponse->alert('serv = '.$serial_row['nth_takes']);
				if ($serial_row['no_takes']) {
					$change_icon = 1;
				} else {
					$change_icon = 0;
				}
			}

			#get the list of child test
			if ($result['is_profile']) {
				$sql_child = "SELECT fn_get_labtest_child_code(" . $db->qstr($result["service_code"]) . ") AS childtest";
				$child_test = $db->GetOne($sql_child);
				#$objResponse->alert($child_test);
			}

			#check if request_doctor is not from spmc
			if ($result['request_doctor'])
				$doctor = $result['request_doctor_name'];
			else
				$doctor = $result['manual_doctor'];

			$r = \SegHis\modules\costCenter\models\LaboratoryRequestItemSearch::search(array(
				'referenceNo' => $result['refno'],
				'serviceCode' => $result['service_code'],
			));

			$request = array(
				'allowDelete' => $r->allowDelete ? 1 : 0, 
				#Updated by Christian 12-31-19
				'message' => ($result['request_flag'] == 'charity' && $result['is_forward'] == 1) ? $r->getItemStatus($ref_source)  : $r->getMessage()
				# end Christian 12-31-19
			);

			$cashier_c = new SegCashier;
			$creditgrant = $cashier_c->getRequestCreditGrants($refno,$ref_source,$result['service_code']);
			$result['discounted_price'] -= (float) $creditgrant[0]['total_amount'];

			#$objResponse->alert('dsprice = '.$result['is_forward']." - ".$result['is_monitor']." - ".$result['every_hour']);
			#$objResponse->alert('name = '.$result['every_hour']);
			#updated by Nick, 4/14/2014 - added erservice_code
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
				number_format($result['discounted_price'], 2, '.', ''),
				$result['request_dept'],
				$result['request_flag'],
				$result['is_forward'],
				$result['is_monitor'],
				$result['every_hour'],
				$result['in_lis'],
				$result['oservice_code'],
				$result['ipdservice_code'],
				$result['erservice_code'],
				$result['icservice_code'],
				$is_serial,
				$change_icon,
				$result['is_package'],
				$result['is_profile'],
				$child_test,
				$request
			);
		}
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

function checkAccess($usr, $pw){
	 global $db, $HTTP_SESSION_VARS;
	 $objResponse = new xajaxResponse();
	 $user= new Access($usr,$pw);

	 if($user->isKnown()&&$user->hasValidPassword()&&$user->isNotLocked()){
		 $objResponse->call("print_true");
	 }else{
		 $objResponse->call("print_false");
	 }

	 return $objResponse;
}

	function checkTestERLab($service_code){
		global $db;
		$objResponse = new xajaxResponse();
		$srv=new SegSpecialLab();

		$row_service_code = $srv->get_TestAllowedER($service_code);
		#$objResponse->alert($srv->sql);
		#$objResponse->alert($row_service_code['service_code']);

		#allowed in ER LAB
		$service_code = trim($row_service_code['service_code']);

		if (!empty($service_code)){
			$objResponse->call("enableButtonClear",0);
		}else{
			$objResponse->call("enableButtonClear",1);
		}

		return $objResponse;
	}
    
    #added by VAS 03-23-2012
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
                                WHERE h.ref_source='LB' AND refno=".$db->qstr($nr);*/
                    #$objResponse->alert($query);            
                    $covered = (float) $db->GetOne($query);
                }
                $additional = $db->GetOne("SELECT SUM(amountxlo) FROM seg_additional_limit WHERE is_deleted IS NULL AND encounter_nr=".$db->qstr($enc_nr)); #added by art 11/20/14
                $objResponse->assign('coverage','value', (float)$additional + (float)$total_benefits-(float)$total_coverage+$covered);
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

    /**
     * @author Gervie 04/22/2016
     * Update Print Status of EHR Laboratory Requests.
     */
    function updatePrintStatus($refno, $status){
        $objResponse = new xajaxResponse();
        $lab_obj = new SegLab();

        $ok = $lab_obj->updatePrintStatus($refno, $status);

        if(!$ok){
        	return false;
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


    
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/laboratory/ajax/lab-request-new.common.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_access.php');
require_once($root_path.'include/care_api_classes/class_special_lab.php');
require_once($root_path.'include/care_api_classes/class_cashier.php');

#added by VAS 03-23-2012
require_once($root_path."include/care_api_classes/billing/class_billing.php");
require_once($root_path."include/care_api_classes/sponsor/class_lingap_patient.php");
require_once($root_path."include/care_api_classes/sponsor/class_cmap_patient.php");

require_once($root_path."frontend/bootstrap.php");

$xajax->processRequest();
?>