<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path."modules/industrial_clinic/ajax/seg-ic-transactions.common.php");
require_once($root_path."include/care_api_classes/billing/class_billing.php");
require_once($root_path."include/care_api_classes/industrial_clinic/class_ic_transactions.php");

function updateFilterOption($noption, $bchecked) {
	$objResponse = new xajaxResponse();

	$_SESSION["filteroption"][$noption] = $bchecked;

	return $objResponse;
}

function updateFilterTrackers($sfiltertype, $ofilter) {
	$objResponse = new xajaxResponse();

	$_SESSION["filtertype"] = $sfiltertype;
	$_SESSION["filter"] = $ofilter;

	return $objResponse;
}

function updatePageTracker($npage) {
	$objResponse = new xajaxResponse();
	$_SESSION["current_page"] = $npage;

	return $objResponse;
}

function clearFilterTrackers() {
	$objResponse = new xajaxResponse();

	unset($_SESSION["filtertype"]);
	unset($_SESSION["filter"]);

	return $objResponse;
}

function clearPageTracker() {
	$objResponse = new xajaxResponse();
	unset($_SESSION["current_page"]);
	return $objResponse;
}

# Added by James 3/24/2014
function generateBill($encounter_nr, $acct_id, $cutoff_date, $total,$discount_amount) {
	
	global $db;

	$objResponse = new xajaxResponse();

	$objIC = new SegICTransaction();
	$bSuccess = true;

	# Get the billing no. to use
	$maxdte = $objIC->getMaxTrxnDte($acct_id, 1, DATE("Y-m-d", strtotime($cutoff_date)));
	$strSQL = "SELECT fn_get_ic_billing_nr_new(DATE('$maxdte')) AS bill_nr";
	$bill_nr = '';
	if ($res = $db->Execute($strSQL)) {
		if ($row = $res->FetchRow()) {
			$bill_nr = $row["bill_nr"];
		}
	}

	if(!$bill_nr == '')
	{

		$db->BeginTrans();

		$result_billh = $objIC->generateBillH($bill_nr, (DATE("Y-m-d H:i:s")), DATE("Y-m-d", strtotime($cutoff_date)), $acct_id);

		foreach ($encounter_nr as $employee_id) {

			$sql = "SELECT encounter_nr FROM seg_industrial_transaction WHERE refno=".$db->qstr($employee_id);

			$result = $db->Execute($sql);
			$row = $result->FetchRow();
			$enc_nr = $row['encounter_nr'];

			/* Commendted by Nick - Query Transfered to class */
			// $sql = "SELECT
			// (SELECT SUM(ld.price_cash*ld.quantity)
			// 	FROM seg_lab_servdetails AS ld
			// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='LB' AND l.status<>'deleted') AS `lab_total_charge`,
			// (SELECT SUM(ld.price_cash*ld.quantity)
			// 	FROM seg_lab_servdetails AS ld
			// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='IC' AND l.status<>'deleted') AS `iclab_total_charge`,
			// (SELECT SUM(ld.price_cash*ld.quantity)
			// 	FROM seg_lab_servdetails AS ld
			// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='BB' AND l.status<>'deleted') AS `bb_total_charge`,
			// -- (SELECT SUM(ld.price_cash*ld.quantity)
			// -- 	FROM seg_lab_servdetails AS ld
			// -- 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			// -- 	WHERE l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='SPL' AND l.status<>'deleted') AS `splab_total_charge`,
			// (SELECT SUM(ld.price_cash*ld.quantity)
			// 	FROM seg_lab_servdetails AS ld
			// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='IC' AND l.status<>'deleted') AS `splab_total_charge`,
			// (SELECT SUM(rd.price_cash)
			// 	FROM care_test_request_radio AS rd
			// 	INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
			// 	WHERE rd.is_served = 1 AND r.encounter_nr = '$enc_nr' AND r.is_cash=0 AND r.status<>'deleted') AS `radio_total_charge`,
			// (SELECT SUM(ph.pricecash*ph.quantity)
			// 	FROM seg_pharma_order_items AS ph
			// 	INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
			// 	WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='IP') AS `ip_total_charge`,
			// (SELECT SUM(ph.pricecash*ph.quantity)
			// 	FROM seg_pharma_order_items AS ph
			// 	INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
			// 	WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='MG' ) AS `mg_total_charge`,
			// (SELECT SUM(md.chrg_amnt*md.quantity)
			// 	FROM seg_misc_service_details AS md
			// 	INNER JOIN seg_misc_service AS m ON m.refno=md.refno
			// 	WHERE m.encounter_nr='$enc_nr' AND m.is_cash=0 ) AS `misc_total_charge`";
			
			$result = $objIC->getPatientExaminationsTotals($enc_nr);//added by Nick 05-19-2014
			$data = $result->FetchRow();

			$nMedicines 	= parseFloatEx($data["ip_total_charge"]+$data["mg_total_charge"]);
			$nSupplies		= parseFloatEx($data["bb_total_charge"]+$data["splab_total_charge"]);
			$nServices 		= parseFloatEx($data["lab_total_charge"]+$data["iclab_total_charge"]+$data["radio_total_charge"]);
			$nMiscellaneous = parseFloatEx($data["misc_total_charge"]);
			$nTotal 		= parseFloatEx($nMedicines+$nSupplies+$nServices+$nMiscellaneous);
			$subtotal 	   += $nTotal;

			# Generate Bill with Bill No.
			$result_billd = $objIC->generateBillD($bill_nr, $employee_id, $nMedicines, $nSupplies, $nServices, $nMiscellaneous, $nTotal);

			# Update employee's status to 1 (Billed)
			$result_update = $objIC->updateEmployee($employee_id, 0);

			if(!$result_billd OR !$result_update OR !$result_billh)
			{
				$bSuccess = false;
				$err_no = $employee_id;
			}
		}

		/*$result_discount = $objIC->updateDiscount(1, $bill_nr, $acct_id, 0, 0);commented by art 06/08/2014*/
		$result_discount = $objIC->updateDiscount(1, $bill_nr, $acct_id, 0, 0,$discount_amount); //added by art 06/08/2014
		if(!$result_discount)
			$bSuccess = false;


		if($bSuccess)
		{
			$db->CommitTrans();

			$objResponse->alert("Bill No. ".$bill_nr." has been generated.");
			$objResponse->call("refreshFrame");
		}
		else
		{
			$db->RollbackTrans();

			$objResponse->alert("Error on generating ".$err_no."'s record.");
			// $objResponse->alert("Error on generating ".$err_no."'s record. Info: Result 1 = ".$result1.", Result 2 = ".$result2.", Result 3 = ".$result3);
		}
	}
	else
	{
		$objResponse->alert("Error ocurred while generating Bill Number.");
	}

	return $objResponse;

}

# Added by James 3/25/2014

# For calculating Total Charges
# Contains: Net Total calculations with/without discount
function getTotalCharges($encounter_nr, $discount, $type)
{
	global $db;
	$disc_amount = 0;
	$objResponse = new xajaxResponse();
	foreach ($encounter_nr as $employee_id) {
		$sql = "SELECT encounter_nr FROM seg_industrial_transaction WHERE refno=".$db->qstr($employee_id);
		$result = $db->Execute($sql);
		$row = $result->FetchRow();
		$enc_nr = $row['encounter_nr'];

		/* Commendted by Nick - Query Transfered to class */
		// $sql = "SELECT
		// (SELECT SUM(ld.price_cash*ld.quantity)
		// 	FROM seg_lab_servdetails AS ld
		// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='LB' AND l.status<>'deleted') AS `lab_total_charge`,
		// (SELECT SUM(ld.price_cash*ld.quantity)
		// 	FROM seg_lab_servdetails AS ld
		// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='IC' AND l.status<>'deleted') AS `iclab_total_charge`,
		// (SELECT SUM(ld.price_cash*ld.quantity)
		// 	FROM seg_lab_servdetails AS ld
		// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='BB' AND l.status<>'deleted') AS `bb_total_charge`,
		// -- (SELECT SUM(ld.price_cash*ld.quantity)
		// -- 	FROM seg_lab_servdetails AS ld
		// -- 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		// -- 	WHERE l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='SPL' AND l.status<>'deleted') AS `splab_total_charge`,
		// (SELECT SUM(ld.price_cash*ld.quantity)
		// 	FROM seg_lab_servdetails AS ld
		// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 /*AND l.ref_source='IC' commented by art 05/13/14*/ AND l.status<>'deleted') AS `splab_total_charge`,
		// (SELECT SUM(rd.price_cash)
		// 	FROM care_test_request_radio AS rd
		// 	INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
		// 	WHERE rd.is_served = 1 AND r.encounter_nr = '$enc_nr' AND r.is_cash=0 AND r.status<>'deleted') AS `radio_total_charge`,
		// (SELECT SUM(ph.pricecash*ph.quantity)
		// 	FROM seg_pharma_order_items AS ph
		// 	INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		// 	WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='IP') AS `ip_total_charge`,
		// (SELECT SUM(ph.pricecash*ph.quantity)
		// 	FROM seg_pharma_order_items AS ph
		// 	INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		// 	WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='MG' ) AS `mg_total_charge`,
		// (SELECT SUM(md.chrg_amnt*md.quantity)
		// 	FROM seg_misc_service_details AS md
		// 	INNER JOIN seg_misc_service AS m ON m.refno=md.refno
		// 	WHERE m.encounter_nr='$enc_nr' AND m.is_cash=0 ) AS `misc_total_charge`";
		
		$objIC = new SegICTransaction();
		$result = $objIC->getPatientExaminationsTotals($enc_nr);//added by Nick 05-19-2014
		$data = $result->FetchRow();

		$nMedicines 	= parseFloatEx($data["ip_total_charge"]+$data["mg_total_charge"]);
		$nSupplies		= parseFloatEx($data["bb_total_charge"]+$data["splab_total_charge"]);
		$nServices 		= parseFloatEx($data["lab_total_charge"]+$data["iclab_total_charge"]+$data["radio_total_charge"]);
		$nMiscellaneous = parseFloatEx($data["misc_total_charge"]);
		$nTotal 		= parseFloatEx($nMedicines+$nSupplies+$nServices+$nMiscellaneous);
		$subtotal 	   += $nTotal;
		#added by art 06/08/2014
		#get LB
		  $lab = $objIC->getLabExams($enc_nr,$discount,'LB');
		  if($lab){
		    if ($lab->RecordCount()>0) {
		      while($row = $lab->FetchRow()){
		        $qty = $row['quantity'];
		        $disc_amount += $qty*$row['discount'];
		      }
		    }
		  }

		  #get SPL
		  #$spl = $objIC->getLabExams($enc,$disc_percent,'SPL');
		  $spl = $objIC->getOtherLabs($enc_nr,$discount);
		  if($spl){
		    if ($spl->RecordCount()>0) {
		      while($row2 = $spl->FetchRow()){
		        $qty = $row2['quantity'];
		        $disc_amount += $qty*$row2['discount'];
		      }
		    }
		  }

		  #get radio
		  $radio = $objIC->getRadio($enc_nr,$discount);
		  if($radio){
		    if ($radio->RecordCount()>0) {
		      while($row3 = $radio->FetchRow()){
		        $disc_amount += $row3['discount'];
		      }
		    }
		  }

		  #get meds
		  $meds = $objIC->getPharmacy($enc_nr,$discount);
		  if($meds){
		    if ($meds->RecordCount()>0) {
		      while($row4 = $meds->FetchRow()){
		        $qty = $row4['quantity'];
		        $disc_amount += $qty*$row4['discount'];
		      }
		    }
		  }

		  #get misc
		  $misc = $objIC->getMisc($enc_nr,$discount);
		  if($misc){
		    if ($misc->RecordCount()>0) {
		      while($row5 = $misc->FetchRow()){
		        $qty = $row5['quantity'];
		        $disc_amount += $qty*$row5['discount'];
		      }
		    }
		  }
		  #end art
	} #end foreach

	if($type != 0)
	{
		if($discount != 0)
		{
				$discount_amount = $disc_amount;
		}
	}
	else
	{
		if($subtotal == 0)
			$discount_amount = 0;
		else
			$discount_amount = $disc_amount;
	}

	if($subtotal-$discount_amount < 0)
		$total = 0;
	else
		#$total = $subtotal-$discount_amount; #commented by art 06/08/2014
		$total = $subtotal-$disc_amount;#added by art 06/08/2014

	$stotal = number_format($subtotal, 2, ".", ",");
	#$sdiscount = number_format($discount_amount, 2, ".", ","); #commented by art 06/08/2014
	$sdiscount = number_format($disc_amount, 2, ".", ","); #added by art 06/08/2014
	$snetTotal = number_format(($total), 2, ".", ",");

	# Show subtotal, discount and net total (seg-ic-generate-bill.php)
	$objResponse->call("populateCalcutations", $stotal, $sdiscount, $snetTotal);

	return $objResponse;
}

# For deleting Billed Account (seg-ic-generate-bill-billed.php)
function deleteBill($bill_nr) {

	global $db;

	$objResponse = new xajaxResponse();

	$objIC = new SegICTransaction();
	$bSuccess = true;

	$db->BeginTrans();

	$result_delete = $objIC->deleteBill($bill_nr, 0);

	if($result_delete)
	{
		while($row = $result_delete->FetchRow())
		{
			$result_update = $objIC->updateEmployee($row['encounter_nr'], 1);

			if(!$result_update) $bSuccess = false;
		}

		$result_delete = $objIC->deleteBill($bill_nr, 1);
		if(!$result_delete) $bSuccess = false;
	}
	else $bSuccess = false;

	if($bSuccess)
	{
		$db->CommitTrans();
		$objResponse->alert("Bill has been deleted successfully.");
		$objResponse->call("reloadFrame");
	}
	else
	{
		$db->RollbackTrans();
		$objResponse->alert("Error ocurred while Deleting this Bill No. ".$bill_nr.".");	
	}

	return $objResponse;
}

# For populating List of Billed Accounts filter by date (seg-ic-generate-bill-billed.php)
function populateBilledList($agency_id, $date) {
	global $db;

	$objResponse = new xajaxResponse();

	$objIC = new SegICTransaction();
	
	$result = $objIC->getBilledAgenciesByDate($agency_id, $date);

	if($result)
	{
		if($result->RecordCount() == 0)
		{
			$data->type = 1;
			$objResponse->call("populateList", $data);
		}
		else
		{
			while ($row = $result->FetchRow()) {

				$billno = $row['bill_nr'];
				$billdate = $row['bill_rundate'];
				
				if($row['total'] < 0)
					$billedamount = number_format(0, 2, '.', ',');
				else
					$billedamount = number_format($row['total'], 2, '.', ',');

				$data->agency_id = $agency_id;
				$data->bill_nr = $billno;
				$data->bill_date = $billdate;
				$data->bill_amount = $billedamount;
				$data->type = 0;

				$objResponse->call("populateList", $data);
			}
		}
	}	
	else
	{
		$data->type = 1;
		$objResponse->call("populateList", $data);
	}

	return $objResponse;
}

# For saving Discount informations (seg-ic-discount.php)
# Contains: checking discount, updating discount and adding discount
function saveDiscount($agency_id, $cutoff, $discount, $remarks)
{
	global $db, $HTTP_SESSION_VARS;

	$objResponse = new xajaxResponse();

	$objIC = new SegICTransaction();
	
	# Get the billing no. to use
	$maxdte = $objIC->getMaxTrxnDte($agency_id, 1, DATE("Y-m-d", strtotime($cutoff)));
	$strSQL = "SELECT fn_get_ic_billing_nr_new(DATE('$maxdte')) AS bill_nr";
	$bill_nr = '';
	if ($res = $db->Execute($strSQL)) {
		if ($row = $res->FetchRow()) {
			$bill_nr = $row["bill_nr"];
		}
	}

	if(!$bill_nr == '')
	{
		$check = $objIC->checkDiscountBillNr($bill_nr, $agency_id);
		$res = $check->FetchRow();

		if($res)
			$count = $res['count'];
		else
			$count = 0;

		if($count != 0)
		{
			$result = $objIC->updateDiscount(0, $bill_nr, $agency_id, $discount, $remarks,0);
			if($result)
				$objResponse->alert("Discount with Bill No. ".$bill_nr." has been updated.");
			else
				$objResponse->alert("Failed to update discount with Bill No. ".$bill_nr.".");
		}
		else
		{
			if ($HTTP_SESSION_VARS['sess_user_personell_nr'])
				$user = $HTTP_SESSION_VARS['sess_user_personell_nr'];
			elseif ($HTTP_SESSION_VARS['sess_temp_personell_nr'])
				$user = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

			$result = $objIC->createDiscount($bill_nr, $agency_id, $discount, $remarks, $user);

			if($result)
				$objResponse->alert("Discount with Bill No. ".$bill_nr." has been added.");
			else
			{
				$objResponse->alert("Failed to add discount with Bill No. ".$bill_nr.". Please close the Generate Bill dialog and open it again to recalculate the Total Amount.");
				$objResponse->alert($objIC->sql);
			}
		}
	}
	else
		$objResponse->alert("Error on generating Bill Nunmber for Save Discount.");


	return $objResponse;
}

# For retreiving discount informations (seg-ic-discount.php)
# Contains: getting values for both discount(seg-ic-discount.php) and generate bill (seg-ic-generate-bill.php)
function getDiscount($count, $agency_id, $cutoff, $process_type)
{
	global $db;

	$objResponse = new xajaxResponse;

	$objIC = new SegICTransaction();

	# Get the billing no. to use
	if($cutoff == "Today")
		$maxdte = $objIC->getMaxTrxnDte($agency_id, 1, DATE("Y-m-d"));
	else
		$maxdte = $objIC->getMaxTrxnDte($agency_id, 1, DATE("Y-m-d", $cutoff));
	$strSQL = "SELECT fn_get_ic_billing_nr_new(DATE('$maxdte')) AS bill_nr";
	$bill_nr = '';
	if ($res = $db->Execute($strSQL)) {
		if ($row = $res->FetchRow()) {
			$bill_nr = $row["bill_nr"];
		}
	}

	if(!$bill_nr == '')
	{
		$result = $objIC->getDiscount($bill_nr, $agency_id);
		if($result)
		{
			$row = $result->FetchRow();
			#$discount = $row['discount_amount'];
			$discount = $row['discount_percentage']; #added by art 05/27/2014
			$remarks = $row['discount_remarks'];
		}
		else
		{
			$remarks = "";
			$discount = 0;
		}

		if($process_type == 0)
			$objResponse->call("calculateSubTotal", $count, $agency_id, $discount, 0);
		else
		{
			$data->remarks = $remarks;
			$data->discount = $discount;

			$objResponse->call("populateList", $data);
		}
	}
	else
		$objResponse->alert("Error on generating Bill Number for Getting Discount.");

	return $objResponse;
}

# End James

$xajax->processRequest();
?>