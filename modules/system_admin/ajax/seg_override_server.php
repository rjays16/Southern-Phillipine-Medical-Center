<?php
require('./roots.php');

#added by michelle 03-25-15
require_once($root_path . 'include/care_api_classes/class_credit_collection.php');

function populateRequests($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	$enc_obj=new Encounter;
	global $HTTP_SESSION_VARS, $db;

	$encounter_nr = $HTTP_SESSION_VARS['sess_en'];
	$pid = $HTTP_SESSION_VARS['sess_pid'];

	$offset = $page_num * $max_rows;
	$sortColumns = array('refno','date_request','dept','total_charge');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "date_request";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'date_request DESC';

	$sslist = $objSS->getLCRInforequest($pid, $encounter_nr, $offset, $max_rows, $sort_sql);
	//$objResponse->alert($objSS->sql);
	if($sslist) {
		$found_rows = $objSS->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$sslist->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $sslist->FetchRow()) {
				#$date_request = date("Y-m-d  h:ia",strtotime($row["time_request"]));
				$date_request = date("m-d-Y",strtotime($row["date_request"]))." ".date("h:ia",strtotime($row["time_request"]));
				$total_charge = number_format($row['total_charge'], 2);

				$DATA[$i]['ref_no'] = $row['refno'];
				$DATA[$i]['request_date'] = $date_request;
				$DATA[$i]['timestamp'] = $row["time_request"];
				$DATA[$i]['total_charge'] = $total_charge;
				$DATA[$i]['dept'] = $row['dept'];
				$DATA[$i]['modifier3_text'] = $mod3[1];
				$DATA[$i]['FLAG'] = 1;

				$i++;
			} //end while
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			//$objResponse->alert(print_r($DATA,true));
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		#$objResponse->alert($objSS->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}
#Added by borj System Admin (Overriding of Test Request) 2014-26-06 Delete Discount
function deleleteBillDiscount($bill_nr)
{
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;

    # added by michelle get `encounter_nr`
    $encObj = new Encounter();
    $encounter_nr = $encObj->getEnc($bill_nr);

    # added by michelle check items on credit collection ledger
    $creditCollObj = new CreditCollection();
    $grants = $creditCollObj->getTotalAmountByEncounter($encounter_nr);
    $isMSSExist = $creditCollObj->isAllowedToCreateMSS($encounter_nr, 'coh');

    # updated by michelle check if still allowed to perform delete action
    if ($isMSSExist) {
        $delRes= $objSS->deleteBillDiscount($bill_nr);
        if ($delRes)
            //$objResponse->alert($delRes);
            $objResponse->alert('Discount successfully deleted in the bill.');
        else
            $objResponse->alert('Discount is deleted in the bill');
    } else {
        $objResponse->alert('Already applied other financial assistance. Please see Show Billing Discount.');
    }

	return $objResponse;
}

function ajaxApplyBillDiscount($encounter_nr, $amount_to_paid, $frmdte, $bill_date, $bill_nr){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
    	if (isset($frmdte) && $frmdte)
		$frm_dte = strftime("%Y-%m-%d %H:%M:%S", $frmdte);
	else
		$frm_dte = "0000-00-00 00:00:00";

	if (isset($bill_date) && $bill_date)
			$bill_dte = strftime("%Y-%m-%d %H:%M:%S", $bill_date);
	else
			$bill_dte = "0000-00-00 00:00:00";

	//$objBilling = new Billing($encounter_nr, $bill_dte, $frm_dte);
    //added by jasper 05/10/2013
    // $objEnc = new Encounter;
    // $result = $objEnc->getSaveBilling($encounter_nr);
    // if ($result->RecordCount()) {
    //     $row = $result->FetchRow();
        $billnr = $bill_nr;
    // }
    $discount = $objSS->getDiscountByEncounter($encounter_nr);
    // $objResponse->alert($objSS->sql);
    $discountid = $discount['discountid'];
    $discountrate = $discount['discount'];
    //added by jasper 05/10/2013
	global $HTTP_SESSION_VARS, $db;

    #check items on credit collection ledger
    $creditCollObj = new CreditCollection();
    $totalCollections = $creditCollObj->getTotalAmountByEncounter($encounter_nr);
    $isMSSExist = $creditCollObj->isAllowedToCreateMSS($encounter_nr, 'coh');

    $totalGrantsAmnt = 0;
    foreach ($totalCollections as $col) {
        if ($col['pay_type'] != 'ss')
            $totalGrantsAmnt += $col['total'];
    }

	#get the total bill of the patient
    $sql = "SELECT fn_billing_compute_net_amount(($bill_nr)) AS bill_amount";
    $bill_amount = $db->GetOne($sql);
   
    $sql = "SELECT fn_billing_compute_gross_amount(($bill_nr)) AS bill_gross_amount";
    $bill_gross_amount = $db->GetOne($sql);

    $newNetAmount = $bill_amount - $totalGrantsAmnt; # added by michelle  03-26-15
    $discount_amount = ($newNetAmount - $amount_to_paid) + ($bill_gross_amount - $bill_amount);

    if (empty($totalCollections))
        $isMSSExist = true;

    #check if there is a saved bill
    #Added by borj System Admin (Overriding of Test Request) 2014-26-06 Apply Discount
    #Updated by michelle to support credit collection
    if ($isMSSExist) {
        if ($newNetAmount) {
            if ($amount_to_paid <= $newNetAmount) {
                //$rs = $objSS->addDocvegaDiscount($encounter_nr,$discount_amount); commented by michelle
                $rs = true;
                // $objResponse->alert($objSS->sql);
                //added by jasper 05/10/2013
                //$objResponse->alert($billnr ."-". $discountid."-".$discountrate."-".$discount_amount);
                //$bSuccess = $objSS->saveBillDiscountAmount($billnr, 'DMH', $discountrate, $discount_amount, $encounter_nr, $amount_to_paid);
                $bSuccess = $creditCollObj->addOverrideAmountToCollectionLedger($encounter_nr, $billnr, 'DMH', 0, $amount_to_paid);

                //$objResponse->alert($objSS->sql);
                if ($rs && $bSuccess)
                    $objResponse->alert('Discount is successfully applied in the bill.');
                else
                    $objResponse->alert('Discount is NOT applied in the bill.');

                $objResponse->assign("discardDiscount", "disabled", FALSE);

            } else{
                $msg = "Total Bill : ".number_format($newNetAmount,2,".",',')." . \n Discount amount must not be greater than the total billing amount.".
                    "\nOnly medicines, laboratory, radiology services can be discounted.".
                    "\nAccomodation and Professional Fees can't be discounted.";
                $objResponse->alert($msg);
            }
        }else{
            $msg = "There is no SAVED BILL for this encounter. \n Please call Billing Section to save the Billing first or confirmed if the bill is already saved. \n Thank you.";
            $objResponse->alert($msg);
        }
    } else {
        $objResponse->alert('Already applied other financial assistance. Please see Show Billing Discount.');
    }

	   // $objResponse->alert('ok na');
	return $objResponse;
}
#end


function addApplyBillingDiscountToCollection($encounter_nr, $amount_to_paid, $frmdte, $bill_date, $bill_nr)
{

}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_social_service.php');
require($root_path.'include/care_api_classes/class_encounter.php');
#Added by borj System Admin (Overriding of Test Request) 2014-26-06
require($root_path.'include/care_api_classes/billing/class_billing_new.php');
#end
require_once($root_path.'modules/system_admin/ajax/seg_override_common.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$xajax->processRequest();

?>