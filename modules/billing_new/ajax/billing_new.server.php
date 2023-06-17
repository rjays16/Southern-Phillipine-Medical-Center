<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
require_once($root_path.'include/care_api_classes/billing/class_billareas.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_info.php');
require_once($root_path.'include/care_api_classes/billing/class_ops_new.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path."include/care_api_classes/class_hospital_admin.php");
require_once($root_path."include/care_api_classes/class_caserate_icd_icp.php");
require_once($root_path.'include/care_api_classes/billing/class_transmittal.php');//added by Nick, 2/24/2014
require_once($root_path.'include/care_api_classes/class_credit_collection.php');//added by Nick, 2/24/2014

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/emr/services/LaboratoryEmrService.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

require_once($root_path.'modules/billing_new/ajax/billing_new.common.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');


require_once($root_path.'frontend/bootstrap.php');

use \SegHis\modules\phic\models\circular\warning\BillWarning;
use \SegHis\modules\phic\models\circular\warning\PhilHealthBelow24Hours;
use \SegHis\modules\phic\models\circular\warning\PneumoniaAdultAgeBelow4Days;
use \SegHis\modules\phic\models\circular\warning\PneumoniaMinorAgeBelow4Days;
use \SegHis\modules\phic\models\circular\warning\DiarrheaBelow3Days;
use \SegHis\modules\phic\models\circular\warning\UtiBelow4Days;
use \SegHis\modules\phic\models\circular\warning\NoAdmittingDiagnosis;
use \SegHis\modules\phic\models\circular\warning\NoFinalDiagnosis;
use \SegHis\modules\phic\models\circular\warning\HasMissingDates;
use \SegHis\modules\phic\models\circular\warning\HasOverlappingOfDates;
//define('IT_ER','00002408');
//define('IT_IP','00002407');
define(IPBMIPD_enc, 13);
define(IPBMOPD_enc, 14);
define(OUT_PATIENT, 2);
define(MINDANAO_DIALYSIS_CENTER, 144);

function getAdjustedDate($date){
	if (strcmp($date, "0000-00-00 00:00:00") != 0)
		$tmp_dte = $date;
	else
		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

	return strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));
}
/*-------------Accommodation Dialog-------------*/
function setWardOptions() {
	$objResponse = new xajaxResponse();

	$objwrd = new Ward();
	$wards = $objwrd->getAllActiveWards();
	if ($wards) {
		$objResponse->call("js_ClearOptions","wardlist");
		$objResponse->call("js_ClearOptions","roomlist");

		if ($wards->RecordCount()) {
			$objResponse->call("js_AddOptions","wardlist","- Select Ward -", 0);
			$objResponse->call("js_AddOptions","roomlist","- Select Room -", 0);

			while ($row = $wards->FetchRow()) {
				$objResponse->call("js_AddOptions", "wardlist", ucwords($row['name']), $row['nr']);
			}
		}
	}
	else
		$objResponse->alert("ERROR: Cannot retrieve available wards!");

	return $objResponse;
}//end setWardOptions

function setWardRooms($ward_nr) {
	$objResponse = new xajaxResponse();

	$objwrd = new Ward();
	$wards = $objwrd->getRoomsData($ward_nr);
	if ($wards) {
		if ($wards->RecordCount()) {
			$objResponse->call("js_ClearOptions","roomlist");
			$objResponse->call("js_AddOptions","roomlist","- Select Room -", 0);
			while ($row = $wards->FetchRow()) {
				$objResponse->call("js_AddOptions", "roomlist", $row['room_nr'], $row['nr']);
			}
		}
	}
	else
		$objResponse->alert("ERROR: Cannot retrieve available rooms!");

	return $objResponse;
}//end setWardRooms

function getAccommodationRate($room_info) {
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	$rm_rate = $objBilling->getRoomRate($room_info);
	$objResponse->call('assignRmRate', number_format($rm_rate, 2, '.', ''));
	return $objResponse;
}//end getRoomRate

//added by shandy
//updated by Nick 05-12-2014 - added tooltip
//updated by Nick 05-15-2014 - load all categories for selection
function getMemCategory(&$objResponse, &$objBilling) {
	$sDesc = $objBilling->getMemCategoryDesc();
	$mem_id = $objBilling->getMemCategoryID();
	$objResponse->call('jsClearList','optMemCat');	
	$hist = $objBilling->getMemCatHist();
	$memCats = $objBilling->getMembershipTypes();
	foreach ($memCats as $key => $memCat) {
		$desc = htmlentities($memCat['memcategory_desc']);
		$id = htmlentities($memCat['memcategory_id']);
		$objResponse->call('js_AddOptions','optMemCat',$desc,$id);
	}
	$objResponse->call('js_setOption','optMemCat',$mem_id);
	$objResponse->call('assignMemCategDesc', $sDesc,$mem_id);
	$objResponse->call('addTooltip','mcategdesc',$hist['modify_id'],date('Y-m-d h:i A',strtotime($hist['modify_dt'])));
		//$objResponse->alert('test billing server ='. $sDesc);
	return $objResponse;
}
// Modified by Joy 06-15-2016
function GetPhicNumber($enc_nr){
	global $db;
	$objBilling = new Billing();
	// Added by Joy 06-30-2016
	$enc_obj = new Encounter();
	$isPHIC = $enc_obj->isPHIC($enc_nr);
	if ($isPHIC=='' || is_null($isPHIC)){
		$phic_nr = 'None';
	}else{
		$phic_nr = $objBilling->getPHICNumber($enc_nr);
	}
	// End by Joy 
	//$phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$enc_nr."') AS `phic_nr`"); 
	return $phic_nr;
}
// end by shandy
function saveAccommodation($aFormValues, $bill_dt) {
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$occ_date_from = $aFormValues['datefrom'];
	$occ_date_to = $aFormValues['dateto'];

	if ($aFormValues['encounter_nr'] != '') {
		$tmp_dte = getAdjustedDate($bill_dt);
        $sesID = $_SESSION['sess_user_name'];
        $sesUN = $_SESSION['sess_user_name'];
        $date_from = date('Y-m-d', strtotime($occ_date_from));
        $date_to = date('Y-m-d', strtotime($occ_date_to));

        $ndays = ((abs(strtotime($occ_date_to)-strtotime($occ_date_from)))/(60*60*24));
        if($date_from==$date_to){
        	$ndays = 1;
        }
        $aFormValues['occupydatefrom'] = $date_from;
        $aFormValues['occupydateto'] = $date_to;
        $aFormValues['days'] = $ndays;
        $aFormValues['sessionID'] = $sesID;
        $aFormValues['sessionUN'] = $sesUN;
        $aFormValues['createdate'] = $tmp_dte;

        $saveok = $objBilling->saveAdditionalAccommodation($aFormValues);
        if($saveok){
        	$objResponse->call("jsRecomputeServices",'acc');
        }
	}
	return $objResponse;
}//end saveAccommodation

//function populateAccommodation($info){
//	$objResponse = new xajaxResponse();
//	$objBilling = new Billing();
//
//	if($objBilling->isInPatient($info['encounter_nr'])){
//
//		$objResponse->call("showAccommodationList", true);
//		$objResponse->call("resetAccommDialogForm");
//
//		$rooms = $objBilling->getAccomodationList($info);
//		$objResponse->call("showAccommodationList", true);
//		$objResponse->call("jsClearList", "body_accListDetails");
//		$total_accom_days = 0;
//		$sub_total = 0;
//		if(is_array($rooms) && (count($rooms) > 0)){
//			foreach ($rooms as $rm) {
//				$rm->setRoomRate(number_format($rm->getRoomRate(), 2, '.', ','));
//				$total_charge = ($rm->getRoomRate() * $rm->getActualDays());
//				$sub_total += $total_charge;
//				$total_accom_days += $rm->getActualDays();
//				$bnew = trim($objBilling->getPrevEncounterNr($details['encounter_nr']));
//				$objResponse->call("jsAccommodationList",
//								$rm, number_format($total_charge, 2, '.', ','), (strlen($bnew)==0 ? '': 'none'));
//			}
//			$lstRefNo = $objBilling->getAccommodationRefno($info);
//			if (is_array($lstRefNo) && (count($lstRefNo) > 0)) {
//				foreach ($lstRefNo as $lsRef) {
//					$objResponse->call("jsAddRefNo", "hdAccommodationRef",$lsRef);
//				}
//			}
//		}else{
//			$objResponse->call("jsAccommodationList", 0, 0, FALSE);
//		}
//		$objResponse->call("setAccSubTotal", number_format($sub_total, 2, '.', ','));
//	}else{
//		$objResponse->call("showAccommodationList", false);
//	}
//	$info['admit_dte'] = $objBilling->getActualAdmissionDte($info['encounter_nr']);
//	$info['t_days'] = $total_accom_days;
//	setupNewAccommodationForm($objResponse, $info);
//	return $objResponse;
//}//populateAccommodation

function setupNewAccommodationForm(&$objResponse, $info){

	$from_date = empty($info['admit_dte']) ? date('Y-m-d', strtotime($info['bill_frmdte'])) : date('Y-m-d', strtotime($info['admit_dte']));
    $to_date = empty($info['death_date']) ? date('Y-m-d', strtotime($info['bill_dt'])) : date('Y-m-d', strtotime($info['death_date']));

	$from_date = strftime('%B %d, %Y',strtotime($from_date.' +'.$info['t_days'].' days' ));
	$to_date = strftime('%B %d, %Y',strtotime($to_date));
	 
	$objResponse->call("jsSetupAccommodationForm", $from_date, $to_date);

}//setupNewAccommodationForm

function delAccommodation($room_info) {

	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$delOk = $objBilling->deleteAccommodation($room_info);
	
	if($delOk){
		$objResponse->call("jsRecomputeServices",'acc');
	}

	return $objResponse;
}//end delAccommodation

/*--------------end Accommodation Dialog--------*/

/*-------------Misc Services -------------------*/
function chargeMiscService($data,$area){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	if ($data['enc_nr'] != '') {
		//get Adjust charge date by 1 second earlier than billing date ...
		$data['bill_dt'] = getAdjustedDate($data['bill_dt']);
 		$data['sess_user_name'] = $_SESSION['sess_temp_userid'];
		$data['msc_charge'] = str_replace(",", "", $data['msc_charge']);
		$data['qty'] = number_format($data['qty'], 0, '', '');
		$saveOk = $objBilling->saveMiscServices($data);
		if($saveOk){
			$saveOk1 = $objBilling->saveMiscServicesDetails($data);
		}
		if($saveOk1){
			$objResponse->call('jsRecomputeServices',$area);
		}
	}
	return $objResponse;
}// end of function chargeMiscService

function chargePharmaSupply($data,$area) {

	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	 
	if ($data['enc_nr'] != '') {
		// Adjust charge date by 1 second earlier than billing date ...
		$data['bill_dt'] = getAdjustedDate($data['bill_dt']);
		$data['sess_user_name'] = $_SESSION['sess_user_name'];
		$data['msc_charge'] = str_replace(",", "", $data['msc_charge']);
		$data['qty'] = number_format($data['qty'], 0, '', '');
		$saveOk = $objBilling->savePharmaSupply($data);
		if($saveOk){
			$saveOk1 = $objBilling->savePharmaSupplyDetails($data);
		}
		if($saveOk1){
			$objResponse->call('jsRecomputeServices',$area);
		}
	}

	return $objResponse;
}// end of function chargeMedorSupply

//commented by kenneth 12/16/2013 
//function not use
// function populateMiscServices($details){
// 	$objResponse = new xajaxResponse();
// 	$objBilling = new Billing();
	
// 	$objResponse->call("jsClearList", "body_hsListDetails");
// 	$objResponse->call("jsClearList", "hdXLORef");

// 	$hspServicesList = $objBilling->getMiscSrvs();
// 	$sub_total = 0;

// 	if (is_array($hspServicesList) && (count($hspServicesList) > 0)) {
// 		foreach ($hspServicesList as $hsValue) {
// 			$servCharge = number_format(($hsValue->getServQty() * $hsValue->getServPrice()), 2, '.', ',');
// 			$totalCharge = ($hsValue->getServQty() * $hsValue->getServPrice());
// 			$sub_total += $totalCharge;
// 			$hsValue->setServPrice(number_format($hsValue->getServPrice(), 2, '.', ','));
// 			$bnew = trim($objBilling->getPrevEncounterNr($details['encounter_nr']));
// 			$objResponse->call("jsHospitalServices", $hsValue, $servCharge, (strlen($bnew)==0 ? '': 'none'));
// 		}
// 		$lstRefNo = $objBilling->getXLOrefno($details);

// 		if (is_array($lstRefNo) && (count($lstRefNo) > 0)) {
// 			foreach ($lstRefNo as $lsRef) {
// 				$objResponse->call("jsAddRefNo", "hdXLORef",$lsRef);
// 			}
// 		}
// 	}else {
// 		$objResponse->call("jsHospitalServices", 0, 0, FALSE);
// 	}

// 	$objResponse->call("setMiscServices", number_format($sub_total, 2, '.', ','));

// 	return $objResponse;
// }//end populateMiscServices

function delMiscService($details) {

	$bSuccess = false;
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	$bSuccess = $objBilling->deleteMiscServices($details);
	if ($bSuccess) {
		$objResponse->call("jsRecomputeServices",'xlo');
	}
	return $objResponse;
}//end delMiscService

function delPharmaSupply($details,$area) {

	$bSuccess = false;
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	
	$bSuccess = $objBilling->deletePharmaSupply($details);

	if ($bSuccess) {
		$objResponse->call("jsRecomputeServices",$area);
	}

	return $objResponse;
}//end delPharmaSupply

/*---------end Misc Services -------------------*/

/*-------------Drugs and Medicines--------------*/
function populateMeds($enc,$bill_dte,$bill_frmdte,$death_date){

	$bill_frmdte = date('Y-m-d H:i:s',strtotime($bill_frmdte));
	$objBill = setArguments($enc,$bill_dte,$bill_frmdte,$death_date);
	$objResponse = new xajaxResponse();
	$result = $objBill->getMedsList_v2();
	$result2 = $objBill->getMedsList_Consigned();
	// $result = array_push($result,$result2);
	// var_dump($result);die();
	$objResponse->call("jsClearList", 'body_mdListDetails');
	$objResponse->call("jsClearList", 'hdMedRef');
	if($result && $result2){
		while($row=$result->FetchRow())
		{
			$details->ref_nr = $row['refno'];
			$details->srv_code = $row['bestellnum'];
			$details->srv_desc = $row['artikelname'];
			$details->flag = $row['flag'];
			$details->qty = $row['qty'];
			$details->srv_price = number_format($row['price'], 2, '.', ',');
			$details->itemcharge = number_format($row['itemcharge'], 2, '.', ',');
			$details->src = $row['source'];
			$details->total = $row['qty']*$row['price'];
			$details->grand_total += $details->total;
			$details->unused = $row['unused_flag'];
			$details->unused_qty = $row['unused_qty'];
			$details->unused_amnt += $row['unused_qty']*$row['price'];
			$details->encoder = $row['encoder'] . ' - ' . $row['time_encoded'];#added by art 07/30/2014
			$details->encoder =  mb_convert_encoding($details->encoder , 'utf-8');#added by art 09/01/2014
			if ($refno!=$row['refno']) {
				$details->same=false;
			}else{
				$details->same=true;
			}
			$refno=$row['refno'];
			$objResponse->call("jsMedicineList", json_encode($details) ,FALSE);
		}
		while($row=$result2->FetchRow())
		{
			$details->ref_nr = $row['refno'];
			$details->srv_code = $row['bestellnum'];
			$details->srv_desc = $row['artikelname'];
			$details->flag = $row['flag'];
			$details->qty = $row['qty'];
			$details->srv_price = number_format($row['price'], 2, '.', ',');
			$details->itemcharge = number_format($row['itemcharge'], 2, '.', ',');
			$details->src = $row['source'];
			$details->total = $row['qty']*$row['price'];
			$details->grand_total += $details->total;
			$details->unused = $row['unused_flag'];
			$details->unused_qty = $row['unused_qty'];
			$details->unused_amnt += $row['unused_qty']*$row['price'];
			$details->encoder = $row['encoder'] . ' - ' . $row['time_encoded'];#added by art 07/30/2014
			$details->encoder =  mb_convert_encoding($details->encoder , 'utf-8');#added by art 09/01/2014
			if ($refno!=$row['refno']) {
				$details->same=false;
			}else{
				$details->same=true;
			}
			$refno=$row['refno'];
			$objResponse->call("jsMedicineList", json_encode($details) ,FALSE);
		}
	}

	$objResponse->call("setUnusedAmount", (($details->unused_amnt) ? $details->unused_amnt : '0' ));
	$objResponse->call("setMedicine", number_format($details->grand_total, 2, '.', ','));
	$objResponse->call("assignValue","save_total_med_charge",$details->grand_total);

	return $objResponse;
}//end populateMeds

/*---------end Drugs and Medicines--------------*/

/*------------ Miscellaneous Charges------------*/
function populateMiscCharges($details){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$lstMiscChrg = $objBilling->getMiscChargesList($details);
	$objResponse->call("jsClearList", "body_mscListDetails");
	$objResponse->call("jsClearList", "hdMiscChargesRef");
	$sub_total = 0;

	if (is_array($lstMiscChrg) && (count($lstMiscChrg) > 0)) {
		foreach ($lstMiscChrg as $mscValue) {
			$total_charge = $mscValue->getMiscChrg()*$mscValue->getMiscQty();
			$sub_total += $total_charge;
			$mscValue->setMiscChrg(number_format($mscValue->getMiscChrg(), 2, '.', ','));
			$bnew = trim($objBilling->getPrevEncounterNr($details['encounter_nr']));
			$objResponse->call("jsMiscellaneousList", $mscValue, number_format($total_charge, 2, '.', ','), (strlen($bnew)==0 ? '': 'none'));
		}
		$lstRefNo = $objBilling->getChargeRefno($details);

		if (is_array($lstRefNo) && (count($lstRefNo) > 0)) {
			foreach ($lstRefNo as $lsRef) {
				$objResponse->call("jsAddRefNo", "hdMiscChargesRef",$lsRef);
			}
		}
	}
	else {
		$objResponse->call("jsMiscellaneousList", NULL,0, false);
	}

	$objResponse->call("setMiscCharges",number_format($sub_total, 2, '.', ','));

	return $objResponse;
}//end populateMiscCharges

function delMiscChrg($data){
	global $db;

	$bSuccess = false;
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$delOk = $objBilling->deleteMiscCharge($data);
	
	if ($delOk) {
		$objResponse->call("jsRecomputeServices",'misc');
	}

	return $objResponse;
}//end delMiscChrg

function chargeMiscChrg($data_misc){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	
	if ($data_misc['enc_nr'] != '') {
		//get Adjust charge date by 1 second earlier than billing date ...
		$data_misc['bill_dt'] = getAdjustedDate($data_misc['bill_dt']);
 		$data_misc['sess_user_name'] = $_SESSION['sess_user_name'];
		$data_misc['msc_charge'] = str_replace(",", "", $data_misc['msc_charge']);
		$data_misc['qty'] = number_format($data_misc['qty'], 0, '', '');
		
		$saveOk = $objBilling->CreateMiscCharge($data_misc);

		if($saveOk){
			$saveOk1 = $objBilling->saveMiscCharge($data_misc);
			
		}
		if($saveOk1)
			$objResponse->call('jsRecomputeServices','misc');
	}

	return $objResponse;
}

function admissionChrg(&$objResponse, &$objBilling, $enc){
	$enc_obj = new Encounter;
	$enc_type = $enc_obj->getEncounterInfo($enc);

	global $db;
	$num = $db->qstr($enc);

	/*$sql = "SELECT * FROM seg_misc_chrg mc
  			INNER JOIN seg_misc_chrg_details mcd ON mcd.refno = mc.refno
			WHERE mc.encounter_nr = ". $num ." AND mcd.service_code IN ('00000853', '00002407', '00002408')";

	$result = $db->Execute($sql);
	$count = $result->RecordCount();*/

	$data = array();

	$data['bill_frmdte'] = $objBilling->bill_frmdte;
	$data['bill_dt'] = $objBilling->bill_dte;
	$data['sess_user_name'] = $_SESSION['sess_user_name'];
	$data['enc_nr'] = $enc;

	$hasBill = $db->GetOne("SELECT bill_nr FROM seg_billing_encounter WHERE encounter_nr = '{$enc}' AND is_deleted IS NULL");

	if(!$objBilling->isFinal() && !$hasBill) {
		if ($enc_type['encounter_type'] == 3 || $enc_type['encounter_type'] == 4 || $enc_type['encounter_type'] == IPBMIPD_enc) {

			$strSQL = "SELECT * FROM seg_other_services AS sos " .
				"   WHERE is_IP_default <> 0 AND " .
				"      NOT EXISTS (SELECT * " .
				"                     FROM seg_misc_chrg_details AS smcd " .
				"                        INNER JOIN seg_misc_chrg AS smc ON smcd.refno = smc.refno " .
				"                     WHERE smcd.service_code = sos.service_code " .
				"                        AND smc.encounter_nr = '" . $enc . "')";

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow()) {
						$data['code'] = $row['service_code'];
						$data['acct_type'] = $row['account_type'];
						$data['msc_charge'] = $row['price'];
						$data['qty'] = 1;

						$saveOk = $objBilling->CreateMiscCharge($data);

						if ($saveOk)
							$saveOk1 = $objBilling->saveMiscCharge($data);

						if ($saveOk1)
							$objResponse->call('jsRecomputeServices', 'misc');
					}
				}
			}
		} else if ($enc_type['encounter_type'] == 1) {
			$strSQL = "SELECT * FROM seg_other_services AS sos " .
				"   WHERE is_ER_default <> 0 AND " .
				"      NOT EXISTS (SELECT * " .
				"                     FROM seg_misc_chrg_details AS smcd " .
				"                        INNER JOIN seg_misc_chrg AS smc ON smcd.refno = smc.refno " .
				"                     WHERE smcd.service_code = sos.service_code " .
				"                        AND smc.encounter_nr = '" . $enc . "')";

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow()) {
						$data['code'] = $row['service_code'];
						$data['acct_type'] = $row['account_type'];
						$data['msc_charge'] = $row['price'];
						$data['qty'] = 1;

						$saveOk = $objBilling->CreateMiscCharge($data);

						if ($saveOk)
							$saveOk1 = $objBilling->saveMiscCharge($data);

						if ($saveOk1)
							$objResponse->call('jsRecomputeServices', 'misc');
					}
				}
			}
		}
	}

	return $objResponse;
}
/*---------end Miscellaneous Charges------------*/

function hasTransmittal($enc){
	$objTransmittal = new Transmittal;
	$transmit_info = $objTransmittal->getPatientTrasmittalInfo($enc);
	if(is_array($transmit_info) && count($transmit_info) > 0){
		return 1;
	}else{
		return 0;
	}
}

function populateBill($enc,$bill_dte,$bill_frmdte,$death_date,$firstCode='',$secondCode='',$accommodationType=false, $opd_area=false,$firstCaseMultiplier='',$secondCaseMultiplier='')
{
	$objResponse = new xajaxResponse();
	$objBill = new Billing();

	$objData = new stdClass();

    //added by Carriane 07/11/17
    //updated by Carriane 07/12/17
    $getAcc = getCurrentAccomodationType($enc);
    $getEncType = getCurrentEncounterDetails($enc);

    $objResponse->call('overwriteLimitButton', $getAcc, $getEncType['encounter_type']);
    //end Carriane
   	$time_thirty_min_parse = Config::model()->get('time_thirty_min_parse');
   
   	$objResponse->call('assignValue', 'encounter_type',$getEncType['encounter_type']);
   	$objResponse->call('assignValue', 'consulting_dept_nr',$getEncType['consulting_dept_nr']);
   	$objResponse->call('assignValue', 'thirty_min_parse',$time_thirty_min_parse->value);
   	
	$insuranceNumber = GetPhicNumber($enc);

	$bill_frmdte = date('Y-m-d H:i:s',strtotime($bill_frmdte));
	$objResponse->call("assignValue","save_bill_dte",$bill_dte);
	$objResponse->call("assignValue","save_frmdte",$bill_frmdte);
	$objResponse->call("assignValue","save_encounter_nr",$enc);
	$objResponse->call("assignValue","save_current_year",date("Y"));

    $insurance_remarks = $objBill->getEncounterInsuranceInfo($enc,18,'id,title,remarks,other_remarks');
    
    if($insurance_remarks['remarks'] == '1') {
		$insuranceNumber = "TEMP";
	}

	if($insurance_remarks['id'] == '6') {
		$objResponse->call("assignValue", "remarks", $insurance_remarks['other_remarks']);
	}
	else{
		$objResponse->call("assignValue", "remarks", $insurance_remarks['title']);
	}
	$objResponse->call("\$j('#remarks').attr","remarks_id",$insurance_remarks['id']);

	if($death_date ==''){
		$death_date = $objBill->getDeathDate($enc);
	}
	#added by art 07/07/2014 
    #for bug 443
	// if ($objBill->getHoursOfDeath($enc)) {
	// 	$objResponse->alert("Patient's Death is within 24hours of Confinement");
	// } commented by earl galope 02/20/2018
	#end art
	$objBill->cur_billdate = $bill_dte;
	
	if ($billinfo = $objBill->hasSavedBill($enc)) {
		$details = new StdClass;
		$details->death_date = $death_date;
		$details->bill_nr = $billinfo['bill_nr'];
        $details->bill_dte = $bill_dte;
		$details->bill_frmdte = $billinfo['bill_frmdte'];
		$details->is_final = $billinfo['is_final'];
        $details->hasTransmittal = hasTransmittal($enc);

        # edited by: syboy 08/23/2015
        # if final remain selected opd area in combobox
        if ($billinfo['is_final']) {
        	$details->opd_type = $billinfo['opd_type']; // added by janken 11/14/2014
        }else{
        	if($billinfo['opd_type'] && $opd_area==0){
        		$details->opd_type = $billinfo['opd_type'];
        	}else{
        		$details->opd_type = $opd_area;
        	}
        }
        # end
        $details->encounter_nr = $billinfo['encounter_nr'];
		$objData->bill_nr = $details->bill_nr;
        if($details->is_final){
            $details->bill_dte = $billinfo['bill_dte'];
			$objResponse->call('disableUI');
		}

		$objBill->setBillArgs($enc,$billinfo['bill_dte'],$billinfo['bill_frmdte'],$death_date,$billinfo['bill_nr']);
		populateBillHeader(&$objResponse,$details);
		# edited by: syboy 11/10/2015
		if ($billinfo['is_final']) {
			$objBill->setAccomodationType($billinfo['accommodation_type']);
		} else {
        	$objBill->setAccomodationType($accommodationType);
        }
		# end
	} else {
		$objBill->setBillArgs($enc,$bill_dte,$bill_frmdte,$death_date);
		$objBill->setAccomodationType($accommodationType);
	}

	// getWarnings($objResponse, array(
	// 	'encounter_nr' => $enc,
	// 	'billDate' => $bill_dte
	// ));

	$objData->encounter_nr = $enc;
	$objData->bill_curDate = $bill_dte;

	/**
	 * Added by Gervie 03-19-2017
	 * Check if encounter used high flux machine
	 */
	$hasHighFlux = $objBill->hasHighFlux($enc);

	if($hasHighFlux) {
		$objResponse->call("assignValue", "hasHighFlux", "high");
	}

	// added by carriane 09/07/17
	$getSavedMultiplier = hasSavedCaseRate($enc);

	if($getSavedMultiplier['package_id_f'] != ''){
		$firstCode = $getSavedMultiplier['package_id_f'];

		if($getSavedMultiplier['package_id_s'] ==  '')
		 	$secondCode = $getSavedMultiplier['package_id_s'];
	}
	
	if($getSavedMultiplier['package_id_s'] != ''){
		$secondCode = $getSavedMultiplier['package_id_s'];

		if($getSavedMultiplier['package_id_f'] == '')
			$firstCode = $getSavedMultiplier['package_id_f'];
	}

	if($getSavedMultiplier['multiplier_f'] != ''){
		$firstCaseMultiplier = $getSavedMultiplier['multiplier_f'];

		if($getSavedMultiplier['multiplier_s'] == '')
			$secondCaseMultiplier = $getSavedMultiplier['multiplier_s'];
	}

	if($getSavedMultiplier['multiplier_s'] != ''){
		$secondCaseMultiplier = $getSavedMultiplier['multiplier_s'];

		if($getSavedMultiplier['multiplier_f'] == '')
			$firstCaseMultiplier = $getSavedMultiplier['multiplier_f'];
	}

	//end carriane

	getTotalAppliedDiscounts(&$objResponse,$enc);//added by nick, 1/8/2014
	getMemCategory(&$objResponse, &$objBill);
	setHasBloodTrans(&$objResponse,$enc);
    setHasUnpaidCps(&$objResponse, $enc); // added by gervie 07/20/2015
    populateUnpaidCps(&$objResponse, $enc); // added by gervie 07/23/2015
	populateAuditTrailCps(&$objResponse, $enc); // added by gervie 11/16/2015
	ProfFees(&$objResponse, &$objBill, $enc);
	populateCaseRate(&$objResponse,&$objBill,$enc,$firstCode,$secondCode,$firstCaseMultiplier,$secondCaseMultiplier);
	ismedico(&$objResponse,$enc);
	$ptype = getPatientType(&$objResponse,&$objBill,$enc);
	isPHS(&$objResponse, &$objBill,$enc);
	isInfirmaryOrDependent(&$objResponse, &$objBill);//added by Nick, 4/8/2014
	getCaseTypeOption(&$objResponse,&$objBill,$enc);
	$objResponse->call("assignInsurance",$insuranceNumber);
	getMemCategory(&$objResponse, &$objBill);
    checkDateForEclaims(&$objResponse,&$objBill, $bill_frmdte, $bill_dte, $enc, GetPhicNumber($enc));//added by janken 11/11/2014 - for checking in eclaims duration
	
	//added by art dingal 01/07/2014
	$deposit = number_format($objBill->getPreviousPayments()/* + $objBill->getOBAnnexPayment()*/, 2, '.', ',');
	$objResponse->call("getPreviousPayment",  $deposit);
	//end art
    $dept = $getEncType['consulting_dept_nr'];
	getRemainingDays($objResponse,$objBill,$ptype,$dept);
	$objResponse->call("calculateTotals");
	$objResponse->call('isNBB');
	$objResponse->call('hideLoading');
	$objResponse->call('getPatientNote');
	return $objResponse;
}

function populateBillFirst($enc,$bill_dte, array $addtlparams)
{

	$objResponse = new xajaxResponse();
	getWarnings($objResponse, array(
		'encounter_nr' => $enc,
		'billDate' => $bill_dte,
		'lessthanadm' => $addtlparams['less_than_encdt'],
		'is_phicexhausted' => $addtlparams['is_exhausted']
	));
	
	return $objResponse;
}

function checkRemainingInsurance($insurance_no,$pid,$isPrincipal,$year,$type,$dept_nr)
{
	$objResponse = new xajaxResponse();
	$objBill = new Billing();
	$daysconsumed = $objBill->getPrevConfinementByInsurance($insurance_no,$pid,$isPrincipal,$year);
	$isCovidSeasons = $objBill->isCovidSeasons();
	$today = date("Y-m-d");
	$expiry_date  = strtotime($isCovidSeasons);
	$current_dt = strtotime($today);
			if($expiry_date >= $current_dt){
				$is_covid = 0;
			}else{
				$is_covid = 1;
			}
	
	// Unknown
	if($type == OUT_PATIENT && $dept_nr == MINDANAO_DIALYSIS_CENTER) {
        if(($daysconsumed >= 90) && ($daysconsumed)) {
            // $objResponse->call('noInsuranceRemains');
            $objResponse->assign('isOkForContinue', 'value', 'true');
        }
        else{
            // $objResponse->call('hasInsuranceRemains');
            $objResponse->assign('isOkForContinue', 'value', 'false');
        }
    } else {
        if(($daysconsumed >= 45) && ($daysconsumed) && ($is_covid)) {
            // $objResponse->call('noInsuranceRemains');
            $objResponse->assign('isOkForContinue', 'value', 'true');
        }
        else{
            // $objResponse->call('hasInsuranceRemains');
            $objResponse->assign('isOkForContinue', 'value', 'false');
        }
    }

	return $objResponse;
}

//added by Nick 12-11-2014
function populateAccommodation($enc,$bill_dte,$bill_frmdte,$death_date){

	$objResponse = new xajaxResponse();

	$objResponse->call("jsClearList", "body_accListDetails");
	$accommodations = getAccommodationList($enc,$bill_dte,$bill_frmdte,$death_date);
	$total = 0;

	$objBill = setArguments($enc,$bill_dte,$bill_frmdte,$death_date);
	admissionChrg(&$objResponse, &$objBill, $enc); //added by Gervie 10/29/2015
	$accom_effectivity = \Config::model()->findByPk('ACCOMMODATION_REVISION');

	$dateAdmission = $objBill->getAdmissionDate();

	if($dateAdmission > $accom_effectivity->value)
        $objBill->greater_accom_effec = 1;

	$details = new StdClass;
	$json_accommodations = array();

	foreach($accommodations as $accommodation){
		/* @var $accommodation Accommodation */
		$details->temp_excess = $accommodation->mandatory_excess * $accommodation->n_days;
		$details->excess += $details->temp_excess;

		$details->encounter = $enc;
		$details->location_nr = $accommodation->getRoomNr();
		$details->type_nr = $accommodation->getTypeNr();
		$details->room = $accommodation->getTypeDesc();
		$details->ward = $accommodation->getWard();
		$details->accommodation_type = $accommodation->getAccomodationType();
		$details->name = $accommodation->name;
		$details->room_rate = number_format($accommodation->getRoomRate(), 2, '.', ',');
		$details->days_stay = $accommodation->getActualDays();
		$details->hours_stay = $accommodation->getExcessHrs();
		$details->admission_date = strftime("%m/%d/%Y", strtotime($accommodation->getAdmissionDteTime())); 	//added by gelie
		$details->discharge_date = strftime("%m/%d/%Y", strtotime($accommodation->getDischargeDteTime()));	//10-21-2015
		$details->source = $accommodation->getSource();
		$details->mandatory_excess = $accommodation->getExcess();

		if($objBill->greater_accom_effec){
			$details->is_per_hour = $accommodation->isRoomRatePerHour();
			$days_total = 0;
			$rate_per_hour = 0;
			$hours_total = 0;
			if($details->hours_stay){
				if($details->days_stay)
					$days_total = $accommodation->getRoomRate() * $details->days_stay;

				$rate_per_hour = number_format(($accommodation->getRoomRate()/24), 2);
				$hours_total = $details->hours_stay * $rate_per_hour;
				$details->total = $days_total + $hours_total;
			}else{
				$details->total = $accommodation->getRoomRate() * $details->days_stay;
			}
		}else{
			$details->total = $accommodation->getRoomRate() * $details->days_stay;
		}

		$total += $details->total;

		$json_accommodations[] = array(
			'accommodation_type' => $details->accommodation_type,
			'room_rate' => $accommodation->room_rate,
			'day_stay' => $accommodation->n_days
		);

		$objResponse->call("jsAccommodationList", $details, number_format($details->total, 2, '.', ','), false);
	}

	$objResponse->call('setAccommodationObject',$json_accommodations);
	$objResponse->call("setAccSubTotal", number_format($total, 2, '.', ','), $details->excess, false);
	$objResponse->call("assignValue", "save_total_acc_charge", $total);
	return $objResponse;
}

//added by Nick 3-15-2015
function getAccommodationList($enc,$bill_dte,$bill_frmdte,$death_date){

	$objBill = setArguments($enc,$bill_dte,$bill_frmdte,$death_date);

	$hosp_obj = new Hospital_Admin();
	$cutoff_hrs = $hosp_obj->getCutoff_Hrs();
	$accom_effectivity = \Config::model()->findByPk('ACCOMMODATION_REVISION');

	if ($death_date!=''){
		$tempbill_dte = $death_date;
	}elseif(strcmp($bill_dte, "0000-00-00 00:00:00") != 0){
		$tempbill_dte = $objBill->bill_dte;
	}else{
		$tempbill_dte = strftime("%Y-%m-%d %H:%M:%S");
	}

	$dateAdmission = $objBill->getAdmissionDate();

	if(strtotime($dateAdmission) > strtotime($accom_effectivity->value))
        $objBill->greater_accom_effec = 1;

	$accommodationList = array();
	$accommodations = $objBill->getAccomodationList()->GetRows();

	if($objBill->greater_accom_effec){
// echo "<pre>" . print_r($accommodations,true) . "</pre>";exit();
		$details = $objBill->getExtractedAccommodationList($accommodations);

		$flag = 0;

		foreach ($details as $i => $accommodation) {
			define(ICU_TYPE, 8);
			
			$Acc = new Accommodation;

			if($accommodation['date_to'] == '0000-00-00')
				$accommodation['date_to'] = strftime("%m/%d/%Y");

			$date_from = date_create($accommodation['date_from']);
			$date_to = date_create($accommodation['date_to']);
			$diff = date_diff($date_from,$date_to);
			$days_stay = $diff->format("%a");

			$hrs_in_words = '';
			if($accommodation['is_per_hour']){
				$days_stay = floor($accommodation['hrs_stay']/24);
				$hrs_stay = $accommodation['hrs_stay'] - ($days_stay * 24);
			}else {
				if($days_stay <= 0) $days_stay = 1;

				$hrs_stay = 0;
			}

			if($accommodation['source'] == 'AD'){
				$tmpdate_from = $accommodation['date_from'];
				$tmptime_from = $accommodation['time_from'];
				
				$Acc->setAdmissionDteTime($tmpdate_from, $tmptime_from);
				$tmpdate_to = $accommodation['date_to'];
				$tmpstatus = $accommodation['status'];
				if ($tmpstatus != "discharged") {
					$tmpdate_to = strftime("%Y-%m-%d", strtotime($tempbill_dte));
					$tmptime_to = strftime("%H:%M:%S", strtotime($tempbill_dte));

					$hourdiff = floor((strtotime($tempbill_dte) - strtotime($accommodation['date_from']." ".$accommodation['time_from']))/3600);

					if($accommodation['is_per_hour']){
						$days_stay = floor($hourdiff/24);
						$hrs_stay = $hourdiff - ($days_stay * 24);
					}else{
						$date_from = date_create($accommodation['date_from']);
						$date_to = date_create($tempbill_dte);
						$diff = date_diff($date_from,$date_to);
						$days_stay = $diff->format("%a");
					}
				}
				else {
					$tmptime_to = $accommodation['time_to'];
					$tmpref_dte = strftime("%Y-%m-%d", strtotime($tmpdate_to)). ' '.strftime("%H:%M:%S",  strtotime($tmptime_to));

					if (strtotime($tmpref_dte) > strtotime($tempbill_dte)) {
						$tmpdate_to = strftime("%Y-%m-%d", strtotime($tempbill_dte));
						$tmptime_to = strftime("%H:%M:%S", strtotime($tempbill_dte));
					}
				}
				$Acc->setDischargeDteTime($tmpdate_to, $tmptime_to);
				$Acc->setActualDays($days_stay);

				if($Acc->n_days <= 0 && !$accommodation['is_per_hour']){
					$Acc->setActualDays(1);
				}
				$Acc->setExcessHrs($hrs_stay);
			}else{
				if(strtotime($accommodation['date_to']) > strtotime($tempbill_dte)){
					$accommodation['date_to'] = strftime("%Y-%m-%d", strtotime($tempbill_dte));
					$date_to = date_create($accommodation['date_to']);
					$diff = date_diff($date_from,$date_to);
					$days_stay = $diff->format("%a");

					$hrs_in_words = '';
					if($accommodation['is_per_hour']){
						$days_stay = floor($accommodation['hrs_stay']/24);
						$hrs_stay = $accommodation['hrs_stay'] - ($days_stay * 24);
					}else {
						if($days_stay <= 0) $days_stay = 1;

						$hrs_stay = 0;
					}
				}

				$Acc->setActualDays($days_stay);
				$Acc->setExcessHrs($hrs_stay);
				$Acc->setAdmissionDteTime($accommodation['date_from'], $accommodation['time_from']);
				$Acc->setDischargeDteTime($accommodation['date_to'], $accommodation['time_to']);
			}
			$Acc->setRoomNr($accommodation['location_nr']);
			$Acc->setTypeNr($accommodation['type_nr']);
			$Acc->setTypeDesc($accommodation['room']);
			$Acc->setWard($accommodation['ward']);
			$Acc->setName($accommodation['name']);

			$room_rate = $accommodation['rm_rate'];
			
			$Acc->setRoomRate($room_rate);
			$Acc->setSource($accommodation['source']);
			$Acc->setExcess($accommodation['mandatory_excess']);
			$Acc->setAccomodationType($accommodation['accomodation_type']);
			$accommodationList[] = $Acc;
		}//end for each

	}else{
		$flag = 0;
		foreach ($accommodations as $i => $accommodation) {
			$Acc = new Accommodation;
			if($accommodation['source'] == 'AD'){
				if ($flag == 0) {
					$tmpadmit_dte = $objBill->getActualAdmissionDate();
					$tmpref_dte = $bill_frmdte;
					if (strtotime($tmpadmit_dte) < strtotime($tmpref_dte))
						$tmpadmit_dte = $tmpref_dte;
					$tmpdate_from = strftime("%Y-%m-%d", strtotime($tmpadmit_dte));
					$tmptime_from = strftime("%H:%M:%S", strtotime($tmpadmit_dte));
					$flag++;
				}
				else {
					$tmpdate_from = $accommodation['date_from'];
					$tmptime_from = $accommodation['time_from'];
				}
				$Acc->setAdmissionDteTime($tmpdate_from, $tmptime_from);
				$tmpdate_to = $accommodation['date_to'];
				$tmpstatus = $accommodation['status'];
				if ($tmpstatus != "discharged") {
					$tmpdate_to = strftime("%Y-%m-%d", strtotime($tempbill_dte));
					$tmptime_to = strftime("%H:%M:%S", strtotime($tempbill_dte));
				}
				else {
					$tmptime_to = $accommodation['time_to'];
					$tmpref_dte = strftime("%Y-%m-%d", strtotime($tmpdate_to)). ' '.strftime("%H:%M:%S",  strtotime($tmptime_to));

					if (strtotime($tmpref_dte) > strtotime($tempbill_dte)) {
						$tmpdate_to = strftime("%Y-%m-%d", strtotime($tempbill_dte));
						$tmptime_to = strftime("%H:%M:%S", strtotime($tempbill_dte));
					}
				}
				$Acc->setDischargeDteTime($tmpdate_to, $tmptime_to);//2015-05-01 19:36:00 || 2015-05-18 02:18:42
				$dis = strftime("%Y-%m-%d", strtotime($Acc->discharge_dtetime)).'00:00:00';
				$adm = strftime("%Y-%m-%d", strtotime($Acc->admission_dtetime)).'00:00:00';
				$Acc->setActualDays(round((strtotime($dis) - strtotime($adm)) / 86400));

				if($Acc->n_days <= 0){
					$Acc->setActualDays(1);
				}
				$Acc->setExcessHrs(0);
			}else{
				if ($accommodation['hrs_stay'] > $cutoff_hrs)
					$Acc->setActualDays($accommodation['days_stay'] + 1);
				else
					$Acc->setActualDays($accommodation['days_stay']);
				$Acc->setExcessHrs(0);
				$Acc->setAdmissionDteTime($accommodation['date_from'], $accommodation['time_from']);	//added by gelie
				$Acc->setDischargeDteTime($accommodation['date_to'], $accommodation['time_to']);		//10-23-2015
			}

			$Acc->setRoomNr($accommodation['location_nr']);
			$Acc->setTypeNr($accommodation['type_nr']);
			$Acc->setTypeDesc($accommodation['room']);//$Acc->setTypeDesc($accommodation['name']);
			$Acc->setWard($accommodation['ward']);
			$Acc->setName($accommodation['name']);

	//commented out by Nick 7-24-2015, removed these considerations
	//		$confinement_type = $objBill->getConfinementType();
	//		if ($objBill->isPHIC()) {
	//			$room_rate = $accommodation['rm_rate'];
	//			$room_rate = $objBill->getRoomRateByCaseType($confinement_type, $accommodation['name']);
	//			if ($room_rate > 0) {
	//			} else {
	//				$room_rate = $accommodation['rm_rate'];
	//			}
	//		} else {
	//			if(trim($death_date) != ''){
	//				if($objBill->isCharity() && !$objBill->isMedicoLegal($enc)){
	//					$room_rate = $objBill->getdeathroomrate($accommodation['name']);
	//					if($room_rate==0){
	//						$room_rate = $accommodation['rm_rate'];
	//					}
	//				}else{
	//					$room_rate = $accommodation['rm_rate'];
	//				}
	//			}else{
	//				$room_rate = $accommodation['rm_rate'];
	//			}
	//		}

			$room_rate = $accommodation['rm_rate'];//added by Nick 7-24-2015

			$Acc->setRoomRate($room_rate);
			$Acc->setSource($accommodation['source']);
			$Acc->setExcess($accommodation['mandatory_excess']);
			$Acc->setAccomodationType($accommodation['accomodation_type']);
			$accommodationList[] = $Acc;
		}//end for each
	}


	return $accommodationList;
}

//updated by Nick 05-15-2014
function getRemainingDays(&$objResponse,&$objBill,$ptype,$dept){
	//if($ptype != 2){
	$rem = $objBill->fortyFiveDays();//added by art 01/06/2014
	$objResponse->assign('remainingDays', 'value', $rem['remaining']); //added by Gervie 04/03/2016
	$objResponse->assign('is_exhausted', 'value', $rem['is_exhausted']); //added by carriane 06/18/19
    $objResponse->assign('actual_rem_days', 'value', $rem['actual_rem_days']);
	$objResponse->call("showRemainingDays",1,$rem['covered'],$rem['remaining'], $rem['save'], $ptype, $dept);//added by art 01/06/2014
	//}
}

//updated by Nick 05-15-2014
//updated by janken 11/14/2014
function getPatientType(&$objResponse,&$objBill,$enc){
	$ptype = $objBill->getEncounterType($enc);
	$objResponse->assign('ptype', 'value', $ptype); # added by: syboy 08/23/2015
	if ($objBill->isERPatient($enc) || $ptype == 2 || $ptype == IPBMOPD_enc) {
		if($ptype != 2 && $ptype != IPBMOPD_enc){
			$objResponse->call("hideOpdArea");
		}
		$objResponse->call("hideAccomodation", 1);
	} else {
		$objResponse->call("hideAccomodation", 0);
		$objResponse->call("hideOpdArea");
	}
	return $ptype;
}

//added by nick, 1/4/2014
function setHasBloodTrans(&$objResponse,$enc){  	
	$labObj = new SegLab();
	$hasbloodborrowed = 0;
	$labObj->hasBloodRequest($enc);
	if ($labObj->count)
	    $hasbloodborrowed = 1;

	$objResponse->call('setHasBloodTrans',$hasbloodborrowed);
}
//end nick

// added by gervie 07/21/2015
function setHasUnpaidCps(&$objResponse, $enc){
    $labObj = new SegLab();
    $hasunpaincps = 0;
    $lab = $labObj->hasUnpaidCps($enc);
    if ($lab->RecordCount()) {
        if($lab->FetchRow()) {
            $hasunpaidcps = 1;
        }
    }
    $objResponse->call('setHasUnpaidCps',$hasunpaidcps);
}

function populateUnpaidCps(&$objResponse, $enc){
    $labObj = new SegLab();
    //$objResponse = new xajaxResponse();

    $result = $labObj->hasUnpaidCps($enc);
    $objResponse->call("jsClearList", "body_cpsListDetails");
    $objResponse->call("jsClearList", "cpsInputs");

    if($result->RecordCount()){
        while($row = $result->FetchRow()){

            // for lab_serv
            $details->ref_no = $row['refno'];
            $details->serv_dt = date('F d, Y', strtotime($row['serv_dt']));
            $details->serv_tm = $row['serv_tm'];
            $details->enc_nr = $row['encounter_nr'];
            $details->pid = $row['pid'];
            $details->is_urgent = $row['is_urgent'];
            $details->is_tpl = $row['is_tpl'];
            $details->is_approved = $row['is_approved'];
            $details->ordername = $row['ordername'];
            $details->discount_id = $row['discount_id'];
            $details->orderaddress = $row['orderaddress'];
            $details->loc_code = $row['loc_code'];
            $details->parent_refno = $row['parent_refno'];
            $details->approved_by_head = $row['approved_by_head'];
            $details->remarks = $row['remarks'];
            $details->headID = $row['headID'];
            $details->headpasswd = $row['headpasswd'];
            $details->discount = $row['discount'];
            $details->source_req = $row['source_req'];
            $details->is_repeat = $row['is_repeat'];
            $details->is_rdu = $row['is_rdu'];
            $details->is_walkin = $row['is_walkin'];
            $details->is_pe = $row['is_pe'];
            $details->area_type = $row['area_type'];
            $details->grant_type = $row['grant_type'];
            $details->ref_source = $row['ref_source'];

            //lab services
            $details->serv_name = $row['name'];
            $details->group_code = $row['group_code'];

            //lab serv details
            $details->service_code = $row['service_code'];
            $details->price_cash = $row['price_cash'];
            $details->price_cash_orig = $row['price_cash_orig'];
            $details->price_charge = $row['price_charge'];
            $details->request_doctor = $row['request_doctor'];
            $details->request_dept = $row['request_dept'];
            $details->is_in_house = $row['is_in_house'];
            $details->clinical_info = $row['clinical_info'];
            $details->_status = $row['status'];
            $details->quantity = $row['quantity'];
            $details->old_quantity_request = $row['old_quantity_request'];
            $details->is_monitor = $row['is_monitor'];

            $objResponse->call('jsUnpaidCps', json_encode($details));
        }
    }
	else{
		$details->ref_no = '0';

		$objResponse->call('jsUnpaidCps', json_encode($details));
	}
}

function populateAuditTrailCps(&$objResponse, $enc)
{
	$labObj = new SegLab();

	$audit_trail = $labObj->cpsAuditTrail($enc);
	$objResponse->call("jsClearList", "body_cpsAuditTrail");

	if ($audit_trail) {
		if($audit_trail->RecordCount()){
			while($adt = $audit_trail->FetchRow()){
				$cps_adt->ref_no = $adt['refno'];
				$cps_adt->serv_name = $adt['name'];
				$cps_adt->serv_code = $adt['service_code'];
				$cps_adt->modify_id = $adt['modify_id'];
				$cps_adt->modify_dt = date('F d, Y', strtotime($adt['modify_dt']));
				$objResponse->call('jsCpsAdt', json_encode($cps_adt));
			}
		}
	} else {
		$cps_adt->ref_no = '0';

		$objResponse->call('jsCpsAdt', json_encode($cps_adt));
	}
}

function deleteUnpaidCps($refno, $serv_code){
    $labObj = new SegLab();
    $objResponse = new xajaxResponse();

    //$delOk = $labObj->deleteLabServ_details($refno);
    $history = "Converted to charge " . date('Y-m-d H:i:s') . " " . $_SESSION['sess_user_name'] . "\n";
    foreach($serv_code as $key => $value){
        $delOk = $labObj->deleteItemsByServiceCode($refno[$key], $value);

        if($delOk){
            $checkParent = $labObj->checkEmptyTray($refno[$key]);
            if($checkParent){
                $labObj->deleteRequestor($refno[$key], $history);
            }
        }
    }

    return $objResponse;
}

function convertUnpaidCps($details, /*$serv_code, $price_cash, $price_orig, $price_charge, $req_doc, $req_dept,
                          $in_house, $clinical_info, $quantity, $ref_no*/ $container){
    $objResponse = new xajaxResponse();
    $labObj = new SegLab();

    $contArr = explode('&', $container);
    $var1 = array_chunk($contArr, 12);
	$success = true;

    $arr_val = array();

    foreach ($var1 as $values){

        $temp = array();

        foreach($values as $value){
            $pair = explode('=', $value);
            $temp[$pair[0]] = $pair[1];
        }

        $arr_val[] = $temp;
        
    }

    $new_arr = array();

    foreach ($arr_val as $obj){
        if(!isset($new_arr[$obj['ref_no']])){
            $new_arr[$obj['ref_no']] = array('id' => $obj['ref_no']);
        }

        $new_arr[$obj['ref_no']]['serv_code'][] = $obj['serv_code'];
        $new_arr[$obj['ref_no']]['price'][] = $obj['price'];
        $new_arr[$obj['ref_no']]['price_cash'][] = $obj['price_cash'];
        $new_arr[$obj['ref_no']]['price_charge'][] = $obj['price_charge'];
        $new_arr[$obj['ref_no']]['request_doctor'][] = $obj['request_doctor'];
        $new_arr[$obj['ref_no']]['request_dept'][] = $obj['request_dept'];
        $new_arr[$obj['ref_no']]['is_in_house'][] = $obj['is_in_house'];
        $new_arr[$obj['ref_no']]['clinical_info'][] = $obj['clinical_info'];
        $new_arr[$obj['ref_no']]['quantity'][] = $obj['quantity'];
        $new_arr[$obj['ref_no']]['item'][] = array('pending', 1, date('Y-m-d H:i:s'), $_SESSION['sess_user_name'], date('Y-m-d H:i:s'), $obj['serv_code']);
        $new_arr[$obj['ref_no']]['history'][] = "Converted to charge from [". $obj['ref_no'] . "] " . date('Y-m-d H:i:s') . " " . $_SESSION['sess_user_name'] . " \n";
    }

    $new_arr = array_values($new_arr);


    for($i = 0; $i < sizeof($new_arr); $i++) {

        $data = array(
            'refno' => $labObj->getLastRefno(), 'serv_dt' => date('Ymd'), 'serv_tm' => date('H:i'),
            'encounter_nr' => $details['enc_nr'], 'pid' => $details['pid'], 'is_cash' => '0', 'type_charge' => '',
            'is_urgent' => $details['is_urgent'], 'is_tpl' => $details['is_tpl'], 'custom_ptype' => '',
            'encoder' => $_SESSION['sess_user_name'], 'orderdate' => date('Y-m-d H:i:s'),
            'history' => "Create " . date('Y-m-d H:i:s') . " " . $_SESSION['sess_user_name'] . " \n", 'comments' => '',
            'ordername' => $details['ordername'], 'orderaddress' => $details['orderaddress'], 'status' => '',
            'discountid' => '', 'loc_code' => $details['loc_code'], 'parent_refno' => '',
            'approved_by_head' => $details['approved_by_head'], 'remarks' => $details['remarks'], 'headID' => $details['headID'],
            'headpasswd' => $details['headpasswd'], 'discount' => $details['discount'], 'fromBB' => 0, 'with_sample' => 1,
            'source_req' => $details['source_req'], 'is_repeat' => $details['is_repeat'], 'is_rdu' => $details['is_rdu'],
            'is_walkin' => $details['is_walkin'], 'is_pe' => $details['is_pe'], 'area_type' => $details['area_type'],
            'grant_type' => $details['grant_type'], 'ref_source' => $details['ref_source'], 'still_in_er' => '0',
            'service_code' => $new_arr[$i]['serv_code'], 'pnet' => $new_arr[$i]['price'], 'pcash' => $new_arr[$i]['price_cash'],
            'pcharge' => $new_arr[$i]['price_charge'], 'request_doctor' => $new_arr[$i]['request_doctor'],
            'request_dept' => $new_arr[$i]['request_dept'], 'is_in_house' => $new_arr[$i]['is_in_house'],
            'clinical_info' => $new_arr[$i]['clinical_info'], 'quantity' => $new_arr[$i]['quantity'], 'is_forward' => '0',
            'is_monitor' => $details['is_monitor'], 'arraySampleItems' => $new_arr[$i]['serv_code'],
            'items' => $new_arr[$i]['serv_code'], 'request_flag' => null, 'history_item' => $new_arr[$i]['history'],
            'arrayItemsList' => $new_arr[$i]['item']
        );

        $labObj->StartTrans();
        $convert = $labObj->saveLabRefNoInfoFromArray($data);
        //print_r($convert);
        if($convert){
            $saveOk = $labObj->update_LabRefno_Tracker($data['refno']);
        }

        if($saveOk) {
            $labObj->CompleteTrans();
            $postToEmr = true;
            $delete_cps = true;
        }
        else{
            $labObj->FailTrans();
            //print_r('Failed');
			$success = false;
            print_r($labObj->getErrorMsg());
			break;
        }

        if($postToEmr){
            try {
                $labService = new LaboratoryEmrService();
                #add new argument to detect if to update patient demographic or not
                $labService->saveLabRequest($data['refno']);
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();die;
            }
        }

        //var_dump($data);
    }
    //exit;

	if($success)
		$objResponse->alert('Unpaid CPS item/s are successfully converted to charge.');
	else
		$objResponse->alert('Error occured during converting unpaid CPS transactions.');

    return $objResponse;
}
// end gervie

//Added by Gervie 01/26/2016
function saveNote($pid, $note,$enc_nr){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	global $db;
	$ncharge= '';
	$patient = $db->GetOne("SELECT pid FROM seg_billing_patient_notes WHERE pid=".$db->qstr($pid));

	if($patient){
		$check = "SELECT note FROM seg_billing_patient_notes WHERE pid=".$db->qstr($pid);
		if ($result = $db->Execute($check)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$ncharge = $row['note'];
				}
			}
		}
		if(empty($ncharge))
		{
			//---added julz
			$notes = $objBilling->updatePatientNoteinempty($pid, trim($note));

			if($notes) {
				$objResponse->alert("Patient note was successfully Created.");
				$objResponse->call("populateBill");
				setPatientNote($pid);
			}
			else
				$objResponse->alert("Error");
			//----end
		}
		else
		{
			$notes = $objBilling->updatePatientNote($pid, trim($note));

			if($notes) {
				$objResponse->alert("Patient note was successfully updated.");
				$objResponse->call("populateBill");
				setPatientNote($pid);
			}
			else
				$objResponse->alert("Error");
		}
	}
	else {
		$notes = $objBilling->savePatientNote($pid, $note,$enc_nr);

		if ($notes) {
			$objResponse->alert('Patient note was successfully saved.');
			$objResponse->call("populateBill");
			setPatientNote($pid);
		}
		else
			$objResponse->alert('Error');
	}

	return $objResponse;
}

function setPatientNote($pid){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$note = $objBilling->getPatientNote($pid);
	$hasNote = ($note) ? 1 : 0;

	$objResponse->call('assignValue', 'patient_note', $note);
	$objResponse->call('assignValue', 'hasNote', $hasNote);
	$objResponse->call('setNoteLabel', $hasNote);

	return $objResponse;
}

function ismedico(&$objResponse,$enc)
{
	$objBill = new Billing();
	if ($objBill->isMedicoLegal($enc)) {
        $objResponse->call("showMedicoLegal",1, ShowMedicoCases($enc)); 
            // $objResponse->alert($enc);
  	} else {
        $objResponse->call("showMedicoLegal",0);
    }
}

function ShowMedicoCases($enc){
	global $db;
	$sql_mc = "SELECT fn_get_medico_legal(".$enc.") AS medico_cases";
	$medicoCase = $db->getOne($sql_mc);
	return $medicoCase;
}

function populateXLO($enc,$bill_dte,$bill_frmdte,$death_date)
{
	$bill_frmdte = date('Y-m-d H:i:s',strtotime($bill_frmdte));
	$objBill = setArguments($enc,$bill_dte,$bill_frmdte,$death_date);
	$objResponse = new xajaxResponse();

	$result = $objBill->getXLOList_v2();
	$objResponse->call("jsClearList", "body_hsListDetails");
	$objResponse->call("jsClearList", "hdXLORef");
	if($result->RecordCount())
	{
		while($row=$result->FetchRow())
		{	
			$details->ref_nr = $row['refno'];
			$details->datetime = $row['serv_dt']+", "+ $row['serv_tm'];
			$details->srv_code = $row['service_code'];
			$details->srv_desc = $row['service_desc'];
			$details->grp_code = $row['group_code'];
			$details->grp_desc = $row['group_desc'];
			$details->qty = $row['qty'];
			$details->srv_price = number_format($row['serv_charge'], 2, '.', ',');
			$details->source_code = $row['source'];
			$details->total = $row['qty'] * $row['serv_charge'];
			$details->grand_total += $details->total;
			$details->encoder = $row['encoder'] . ' - ' . $row['time_encoded'];#added by art 07/28/2014
			$details->encoder =  mb_convert_encoding($details->encoder , 'utf-8');#added by art 09/01/2014
			if ($refno!=$row['refno']) {
				$details->same=false;
			}else{
				$details->same=true;
			}
			$refno=$row['refno'];
			//$info = serialize($details);
			$objResponse->call("jsHospitalServices",json_encode($details),number_format($details->total, 2, '.', ','),FALSE);

		}
	}
	$objResponse->call("setMiscServices", number_format($details->grand_total, 2, '.', ','));
	$objResponse->call("assignValue","save_total_srv_charge",$details->grand_total);
	return $objResponse;
}


function populateMisc($enc,$bill_dte,$bill_frmdte,$death_date)
{
	$bill_frmdte = date('Y-m-d H:i:s',strtotime($bill_frmdte));
	$objBill = setArguments($enc,$bill_dte,$bill_frmdte,$death_date);
	$objResponse = new xajaxResponse();	
	
	$result = $objBill->getMiscList();
	$objResponse->call("jsClearList", "body_mscListDetails");
	if($result)
	{
		while($row=$result->FetchRow())
		{
			$details->refno = $row['refno'];
			$details->code = $row['service_code'];
			$details->name = $row['name'];
			$details->desc = $row['description'];
			$details->qty = $row['qty'];
			$details->chrg = number_format($row['avg_chrg'], 2, '.', ',');
			$details->grand_total += $row['total_chrg'];
			
			/*if($row['service_code']!= IT_IP && $row['service_code']!= IT_ER){
				$details->misc_auto_excess +=  $row['total_chrg'];
			}*/
				
			$objResponse->call("jsMiscellaneousList", json_encode($details), number_format($row['total_chrg'], 2, '.', ','), false);
		}
	}
	else {
		$objResponse->call("jsMiscellaneousList", null, 0, false);
	}
	#$objResponse->call("setMiscAutoExcess",(($details->misc_auto_excess) ? $details->misc_auto_excess : '0'));
	$objResponse->call("setMiscCharges",number_format($details->grand_total, 2, '.', ','));
	$objResponse->call("assignValue","save_total_msc_charge",$details->grand_total);
	return $objResponse;
}


function setDeathDate($pid,$type, $enc ='0', $deathdate='0000-00-00 00:00:00')
{
	global $db;
	$objResponse = new xajaxResponse();
	$objPerson = new Person;
	$ok = $objPerson->setDeathDate($pid,$type,$enc,$deathdate);
	#$ok = $objPerson->setDeathDate($pid,$enc,$deathdate);
	if(!$ok)
	{
		$objResponse->alert($objPerson->sql);
	}
	return $objResponse;
}

function identifyProcedureConflict($enc ='0', $inDate='0000-00-00', $outDate='0000-00-00')
{
	$objResponse = new xajaxResponse();
	global $db;
	$strSQL="SELECT smod.`refno`, smod.`entry_no`, smod.`ops_code` AS CODE,
							'1' AS op_count, sor.`description`, smod.`rvu`,
							smod.`multiplier`, (smod.rvu * smod.`multiplier`) AS op_charge, smod.`group_code`,
							'OA' AS provider, smod.`op_date`, smod.`description` AS alt_desc,
							  smod.`lmp_date`
						FROM seg_misc_ops `smo`
						INNER JOIN seg_misc_ops_details `smod`
						ON smod.`refno` = smo.`refno`
						INNER JOIN seg_ops_rvs `sor`
						ON sor.`code` = smod.`ops_code`
						WHERE smo.`encounter_nr` = '".$enc."'
						AND (smod.`op_date` < '".$inDate."'
						OR smod.`op_date` > '".$outDate."')
					UNION
						SELECT smod.`refno`, smod.`entry_no`, smod.`ops_code` AS CODE,
							'1' AS op_count, sor.`description`, smod.`rvu`,
							smod.`multiplier`, (smod.rvu * smod.`multiplier`) AS op_charge,
							smod.`group_code`, 'OR' AS provider, ceo.`op_date`,
							smod.`description` AS alt_desc,   smod.`lmp_date`
						FROM seg_misc_ops `smo`
						INNER JOIN seg_misc_ops_details `smod`
						ON smo.`refno` = smod.`refno`
						INNER JOIN care_encounter_op `ceo`
						ON ceo.`refno` = smo.`refno`
						INNER JOIN seg_ops_rvs `sor`
						ON smod.`ops_code` = sor.`code`
						WHERE smo.`encounter_nr` = '".$enc."'
						AND (smod.`op_date` < '".$inDate."'
						OR smod.`op_date` > '".$outDate."')";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			$objResponse->call("rollBackCasedate","WARNING! One or more procedure date(s) are out of the confinement period.");
		}
	}
	else{$objResponse->alert($strSQL);}
	return $objResponse;
}

/*------------------------------Populates the First and Second Case Rate Package-------------------------*/
/**
 * Updated by Nick 4/22/2014
 * Different case rate(case rates with special package) amount for
 * new born with hearing and non-hearing test
 */
function populateCaseRate(&$objResponse, &$objBill, $encNr,$firstCode,$secondCode,$firstCaseMultiplier,$secondCaseMultiplier){
	// $objResponse = new xajaxResponse();
	$ops = new SegOps();
	$encObj = new Encounter();
	$icdIcp = new Icd_Icp();
	$objResponse->call("js_ClearOptions","first_rate");
	$objResponse->call("js_ClearOptions","second_rate");
	$objResponse->call("js_ClearOptions","first_case_desc");
	$objResponse->call("js_ClearOptions","second_case_desc");
	$objResponse->call("js_ClearOptions","first_rate_amount");
	$objResponse->call("js_ClearOptions","second_rate_amount");
	$objResponse->call("js_ClearOptions","first_multiplier");
	$objResponse->call("js_ClearOptions","second_multiplier");
	$objResponse->call("setFields");
	
	$diagnosis = $icdIcp->searchIcdIcp($encNr);
	$data['encounter'] = $encNr;
	$hasbillnr = $objBill->getbillnr($data);
	if($hasbillnr){
		$fcase = $objBill->hasSavedPackage($hasbillnr,1);
		$scase = $objBill->hasSavedPackage($hasbillnr,2);
	}
	$hasSec = '';
	$firstCodeExists = '';
	$secondCodeExists = '';
	$selected_first = '';
	$selected_second = '';
	if ($diagnosis) {

		$rows=$diagnosis->RecordCount();
		while($row=$diagnosis->FetchRow()){

			$row['num_sessions'] = ($row['code'] == '96408') ? 1 : $row['num_sessions'];//added by Nick 05-08-2014

			if($row['code'] == '67010' || $row['code'] == '67005'){
				$addIcpCond = ($icdIcp->additionalCondition($encNr, $row['code'], $row['laterality'])) ? 1 : 0;
			}
			else{
				$addIcpCond = 0;
			}

			if($row['code']){
				$pkg->spc = 0;
				$result = $objBill->GetPreviousPackage($encNr);
			
				#modified by Matsss 10272020
				#if(!$objBill->isClearedForRVS($row['code'],$row['laterality'],$encNr)) $pkg->spc = 1;
				if(!$objBill->isClearedForRVS($row['code'],$row['laterality'],$encNr)) {
				$isCovidSeasons = $objBill->isCovidSeasons();
				$today = date("Y-m-d");
				$expiry_date  = strtotime($isCovidSeasons);
				$current_dt = strtotime($today);
						if($expiry_date >= $current_dt){
							$pkg->spc = 0;
						}else{
							$pkg->spc = 1;
						}
				}
				// if ($result) {
				// 	while ($row2 = $result->FetchRow()) {
				// 		/*if ($row2['acr_groupid'] == $row['acr_groupid']) {
				// 			$pkg->spc = 1;
				// 		}*/
				// 		if ($row2['code'] == $row['code']) {
				// 			if(!empty($row2['laterality'])){
				// 				if($row2['laterality'] == $row['laterality'] 
				// 					|| $row2['laterality'] == "B" 
				// 					|| $row['laterality'] == "B"){
				// 					$pkg->spc = 1;
				// 				}
				// 			}
				// 			else
				// 				$pkg->spc = 1;
				// 		}
				// 	}
				// }
				

				# added by: syboy 10/11/2015
				$result_caserate2 = $objBill->getCaseRateRVSCode($encNr);
				if ($result_caserate2) {
					while ($row4 = $result_caserate2->FetchRow()) {
						// var_dump($row4); 
						// var_dump($row['laterality']);
						$dateAdmission = $objBill->getAdmissionDate();
						if(!$objBill->isClearedForRVS($row4['code'],$row4['laterality'],$encNr)) $pkg->spc = 3;
						// if ($dateAdmission >= "2015-07-15 00:00:01") {
							
						// 	if ($row4['prevlaterality'] == $row['laterality'] || $row4['prevlaterality'] == "B") {
						// 		$pkg->spc = 3;
						// 	} else if ($row4['prevlaterality'] == "R" && $row['laterality'] == "B") {
						// 		$pkg->spc = 3;
						// 	} else if ($row4['prevlaterality'] == "L" && $row['laterality'] == "B") {
						// 		$pkg->spc = 3;
						// 	}
						// }
						// var_dump($row4['prevlaterality'] .' -- '. $row['laterality']. ' -- '. $pkg->spc);
						// var_dump($row4['code'] .' -- '. $row['code'] .' -- '. $row4['prevlaterality'] .' -- '. $row['laterality'] . ' -- '. $pkg->spc);
						/*
						if (in_array($row['code'], $RVS_codes) && $row4['prevlaterality'] == "B") {
							$pkg->spc = 3;
						}else if($row4['code'] == $row['code'] || in_array($row['code'], $RVS_codes) && $row4['prevlaterality'] == $row['laterality']){
							$pkg->spc = 3;
						}
						*/ 
					}
				}
				// die();
				# ended

				//Added by Gervie 06/02/2016
				$saved_multiplier = $icdIcp->getMultiplier($encNr, $row['code']);
				$temp_saved_multiplier = $icdIcp->getTempMultiplier($encNr, $row['code']);
				
				if($saved_multiplier != '' && $temp_saved_multiplier == '') {
					$pkg->multiplier = $saved_multiplier;
				}elseif($temp_saved_multiplier != ''){
					$pkg->multiplier = $temp_saved_multiplier;
				}
				else {
					$pkg->multiplier = $row['num_sessions'];
				}


				$pkg->amntFirst = '';
				$pkg->amntSecond = '';
				$pkg->hf1 = '';
				$pkg->pf1 = '';
				$pkg->hf2 = '';
				$pkg->pf2 = '';
				$pkg->operation_date = '';
		
                $pkg->addIcpCond = $addIcpCond;

                if($firstCaseMultiplier != '') {
                	$pkg->amntFirst = ($row['package'] * ($pkg->multiplier - ($pkg->multiplier - $firstCaseMultiplier)));
                	$pkg->hf1 = ($row['hf'] * ($pkg->multiplier - ($pkg->multiplier - $firstCaseMultiplier)));
					$pkg->pf1 = ($row['pf'] * ($pkg->multiplier - ($pkg->multiplier - $firstCaseMultiplier)));
                }
                else {
					$pkg->amntFirst = ($row['package'] * $pkg->multiplier);
					$pkg->hf1 = ($row['hf'] * $pkg->multiplier);
					$pkg->pf1 = ($row['pf'] * $pkg->multiplier);
				}

				if($secondCaseMultiplier != '') {
					$pkg->amntSecond = (($row['shf'] + $row['spf']) * ($pkg->multiplier - ($pkg->multiplier - $secondCaseMultiplier)));
					$pkg->hf2 = ($row['shf'] * ($pkg->multiplier - ($pkg->multiplier - $secondCaseMultiplier)));
					$pkg->pf2 = ($row['spf'] * ($pkg->multiplier - ($pkg->multiplier - $secondCaseMultiplier)));
				}
				else {
					$pkg->amntSecond = (($row['shf'] + $row['spf']) * $pkg->multiplier);
					$pkg->hf2 = ($row['shf'] * $pkg->multiplier);
					$pkg->pf2 = ($row['spf'] * $pkg->multiplier);
				}

				$pkg->orig_amntFirst = ($row['package'] * $row['num_sessions']);
				$pkg->orig_amntSecond = (($row['shf'] + $row['spf']) * $row['num_sessions']);
				$pkg->orig_hf1 = ($row['hf'] * $row['num_sessions']);
				$pkg->orig_pf1 = ($row['pf'] * $row['num_sessions']);
				$pkg->orig_hf2 = ($row['shf'] * $row['num_sessions']);
				$pkg->orig_pf2 = ($row['spf'] * $row['num_sessions']);
				$pkg->orig_multiplier = $row['num_sessions'];						

				$pkg->sp_amnt = ($row['sp_package'] * $row['num_sessions']);
				$pkg->sp_hf = ($row['sp_shf'] * $row['num_sessions']);
				$pkg->sp_pf = ($row['sp_spf'] * $row['num_sessions']);
				$pkg->operation_date = $row['operation_date'];
				$pkg->code = $row['code'];
				$pkg->desc = $row['description'];
				$pkg->cType = $row['case_type'];
				$pkg->isSecCase = $row['is_allowed_second'];
				$pkg->caserate = $fcall;
				$pkg->laterality = $row['laterality'];
				$pkg->rvu = $row['rvu'];
				$pkg->D1 = '';
				$pkg->D3 = '';
				$pkg->D4 = '';
				$pkg->D1_sec = '';
				$pkg->D4_sec = '';
				$pkg->D3_sec = '';
				
				$GLOBAL_CONFIG = array();
				$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pf_distribution%');
				$pf_limiter = $GLOBAL_CONFIG['pf_distribution_limit'];
				$pf_abovelimit = explode(",",$GLOBAL_CONFIG['pf_distribution_abovelimit']);
				$pf_belowlimit = explode(",",$GLOBAL_CONFIG['pf_distribution_belowlimit']);

				if($row['case_type']=='p'){
					if($row['rvu']<=$pf_limiter){
						$pkg->D3 = $pkg->pf1 * ($pf_belowlimit[0]);
						$pkg->D4 = $pkg->pf1 * ($pf_belowlimit[1]);
					}
					else if ($row['rvu']>$pf_limiter){
						$pkg->D3 = $pkg->pf1 * ($pf_abovelimit[0]);
						$pkg->D4 = $pkg->pf1 * ($pf_abovelimit[1]);
					}
				} else {
					$pkg->D1 = $pkg->pf1;
				}

				if ($row['case_type'] == 'p' && $row['is_allowed_second'] == '1') {
					if($row['rvu']<=$pf_limiter){
						$pkg->D3_sec = $pkg->pf2 * ($pf_belowlimit[0]);
						$pkg->D4_sec = $pkg->pf2 * ($pf_belowlimit[1]);
					}
					else if ($row['rvu']>$pf_limiter){
						$pkg->D3_sec = $pkg->pf2 * ($pf_abovelimit[0]);
						$pkg->D4_sec = $pkg->pf2 * ($pf_abovelimit[1]);
					}
				} else {
					$pkg->D1_sec = $pkg->pf2;
				}

				if ($selected_first=='' && $pkg->spc != 1)
					$selected_first  = $pkg->code;

				if($selected_second=='' && $pkg->spc != 1 && $pkg->isSecCase == 1) {
					if($selected_first == $pkg->code && $pkg->laterality != '')
						$selected_second = $pkg->code;
					elseif($selected_first != $pkg->code ){
						$selected_second = $pkg->code;
					}
				}
				// $pkg->amntFirst = ($row['package'] * $row['num_sessions']);
				$objResponse->call("populateCaseRates",$pkg);
			}
		}//end while
		
        if(trim($firstCode)==''){
            if(isset($fcase['package_id']) && ($fcase['package_id'] != $secondCode)){
                $firstCode = $fcase['package_id'] ? $fcase['package_id'] : "";
            }
        }

        if(trim($secondCode)==''){
            if(isset($scase['package_id']) && ($scase['package_id'] != $firstCode)){
                $secondCode = $scase['package_id'] ? $scase['package_id'] : "";
            }
        }

        // if($firstCode == $secondCode){
        //     $secondCode = "";
        // }
        
        $objResponse->call("setCaserate",$firstCode,$secondCode,$firstCaseMultiplier,$secondCaseMultiplier,$fcase['laterality'],$scase['laterality']);
        $objResponse->call('Procedures.getPatientSpecialProcedures');//added by Nick 07-15-2014

	}else{
		$objResponse->call("populateCaseRates",NULL);
	}

	return $objResponse;
}


/*----------------------Operating Room Accomodation Charges---------------------------------------*/

function getBilledOps($enc,$bill_dte,$bill_frmdte,$death_date) {
	$bill_frmdte = date('Y-m-d H:i:s',strtotime($bill_frmdte));
	$objBill = setArguments($enc,$bill_dte,$bill_frmdte,$death_date);
	$objResponse = new xajaxResponse();

	$objBill->getOpBenefits();    // Get summary of operations and corresponding insurance coverage.

	$opsBenefitsArray = $objBill->hsp_ops_benefits;
	$totalOpsCharge   = $objBill->getTotalOpCharge();
	$objBill->initOpsConfineCoverage();

	$objResponse->call("jsClearList", "body_opsListDetails");

	if (is_array($opsBenefitsArray) && (count($opsBenefitsArray) > 0)) {
        $i = 0;
		foreach ($opsBenefitsArray as $key=>$value) {
				$n_rvu   = number_format($value->op_rvu, 0, '', ',');
				$n_mult  = number_format($value->op_multiplier, 2, '.', ',');
				$n_total = number_format($value->getOpCharge(), 2, '.', ',');
				$op_code = $value->op_code;
                if ($objBill->is_coveredbypkg) {
                    if ($i==0) {
                        $i++;
                        // $objBilling->getConfineBenefits('OR');
                    }
                }
                // else {
                //     $objBilling->getConfineBenefits('OR', $value->getOpCodePerformed());
                // }

				$opAcc->rm_nr       = substr($op_code, 3); // Discard 'OR-'
				$opAcc->nrvu        = $n_rvu;
				$opAcc->nmultiplier = $n_mult;
				$opAcc->nchrg       = $n_total;
				$opAcc->desc 		= $value->op_desc;

				$objResponse->call("addORAccCharge", $opAcc);
		}
	}
	else
		$objResponse->call("addORAccCharge", NULL);

    // if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
    //     $ndiscount = $objBilling->getBillAreaDiscount('OR');
    // } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) { //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
    //     $ndiscount = $objBilling->getBillAreaDiscount('OR');
    // } else {
    //     $ndiscount = 0.00;
    // }
    // if ($objBilling->isPHIC()) {
    //     if ($totalOpsCharge - $ndiscount <= $totalOpsCoverage) {
    //         $totalOpsCoverage = $totalOpsCharge - $ndiscount;
    //     }
    // }

	$totalOpsCoverage = $objBill->ops_confine_coverage;
	$objResponse->call("showOpsTotals", number_format($totalOpsCharge, 2, '.', ','));
	$objResponse->call("assignValue","save_total_ops_charge",$totalOpsCharge);
	return $objResponse;
    // $excess = number_format(($totalOpsCharge - $ndiscount - $totalOpsCoverage), 2, '.', ',');
	// $objResponse->call("showOpsTotals", number_format($totalOpsCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','), number_format($totalOpsCoverage, 2, '.', ','), $excess);
}

function getOPCharge($enc_nr, $bill_dt, $nrvu, $casetyp) {
	global $db;

	$ncharge = 0;

	$strSQL = "select fn_getORCharge('{$enc_nr}', date('{$bill_dt}'), {$nrvu}, {$casetyp}) as opcharge";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$ncharge = $row["opcharge"];
			}
		}
	}

	return $ncharge;
}

function setORWardOptions() {
	$objResponse = new xajaxResponse();
	$objwrd = new Ward();
	$wards = $objwrd->getAllOpWards();
	if ($wards) {
		$objResponse->call("js_ClearOptions","opwardlist");
		$objResponse->call("js_ClearOptions","orlist");

		if ($wards->RecordCount()) {
			$objResponse->call("js_AddOptions","opwardlist","- Select O.R. Ward -", 0);
			$objResponse->call("js_AddOptions","orlist","- Select Operating Room -", 0);

			while ($row = $wards->FetchRow()) {
				$objResponse->call("js_AddOptions", "opwardlist", $row['name'], $row['nr']);
			}
		}
	}
	else
		$objResponse->alert("ERROR: Cannot retrieve available O.R. wards!");

	return $objResponse;
}

function setORWardRooms($ward_nr) {
	$objResponse = new xajaxResponse();

	$objwrd = new Ward();
	$wards = $objwrd->getRoomsData($ward_nr);
	if ($wards) {
		if ($wards->RecordCount()) {
			$objResponse->call("js_ClearOptions","orlist");
			$objResponse->call("js_AddOptions","orlist","- Select Operating Room -", 0);
			while ($row = $wards->FetchRow()) {
				$objResponse->call("js_AddOptions", "orlist", $row['room_nr'], $row['nr']);
			}
		}
	}
	else
		$objResponse->alert("ERROR: Cannot retrieve available rooms!");

	return $objResponse;
}

function populateAppliedOpsList($senc_nr)
{
    $objResponse = new xajaxResponse();
    $Ops = new SegOps;
    $procInfo = $Ops->SearchAppliedOP($senc_nr);
    $objResponse->call("clearAppliedProcedureList");
    if ($procInfo) {
		$rows=$procInfo->RecordCount();
		while($result=$procInfo->FetchRow()) {
			$description_short = $result["description"];
			if (strlen($description_short)>50)
				$description_short = substr(trim($result["description"]),0,50)."...";

			$details = new StdClass;
			$details->code = $result["ops_code"];
			$details->description = trim($description_short);
			$details->descriptionFull = trim($result["description"]);
			$details->opdate = (is_null($result["op_date"])) ? '00/00/0000' : strftime("%m/%d/%Y", strtotime($result["op_date"]));
			$details->rvu = $result["rvu"];
			$details->multiplier = $result["multiplier"];
			$details->entry_no = $result["entry_no"];
			$details->refno = $result["refno"];
			$details->groupcode = $result["group_code"];
			$details->pf = $result["pf"];

			$objResponse->call("addAppliedOPtoList", $details);
		}#end of while
	}#end of if

	if (!$rows) $objResponse->call("addAppliedOPtoList", NULL);
	return $objResponse;
}

function updateRVUTotal($pDetails) {
	$objResponse = new xajaxResponse();
	$ops = new SegOps();
	$hospObj = new Hospital_Admin();
	$icdIcp = new Icd_Icp();

	if ($ops->isHouseCase($pDetails['encNr'])){
		$nPCF = HOUSE_CASE_PCF;
        $cType = 2;
	}else{
		$nPCF = $hospObj->getDefinedPCF();
		$cType = 1;
	}

    $diagnosis = $icdIcp->searchIcdIcp($pDetails['encNr']);
    if ($diagnosis) {
    	while($result=$diagnosis->FetchRow()){
    		if($pDetails['opsCode']==$result['code']){
    			$num_sessions = ($result['special_case'] == 1) ? 1 : $result['num_sessions'];
    			$additional = $icdIcp->getOpsAdditional($pDetails['opsCode']);

    		}
    	}
    }      

	$ncharge = $ops->getOPCharge($pDetails['encNr'], $pDetails['billdate'], $pDetails['nrvu'],$cType);
	$objResponse->call("applyRVUandMult", $pDetails['nrvu'], $nPCF, (($ncharge + $additional) * $num_sessions));
	
	// $foo = $pDetails['$cType'];
	// $objResponse->alert($nPCF);
	return $objResponse;
}

function saveORAccommodation($aFormValues, $bill_dt, $opDetails='') {
	global $db;
	$ops = new SegOps();

	$err_msg = '';

	$objResponse = new xajaxResponse();

	$s_enc_nr = $aFormValues['opacc_enc_nr'];
	$bSuccess = true;

	if ($s_enc_nr != '') {
		if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
				$tmp_dte = $bill_dt;
		else
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

		$tmpbill_dte = $tmp_dte;
		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

		$opAcc = new StdClass;
		$opAcc->w_nr        = $aFormValues['opw_nr'];
		$opAcc->rm_nr       = $aFormValues['opr_nr'];
		$opAcc->nrvu        = $aFormValues['total_rvu'];
		$opAcc->nmultiplier = $aFormValues['multiplier'];
		$opAcc->nchrg       = str_replace(",", "", $aFormValues['oprm_chrg']);
		$opAcc->frm_dte 	= $aFormValues['frm_dte'];
		$opAcc->desc 		= '';

		$objBilling = unserialize($_SESSION['billobject']['main']);
		$db->StartTrans();

		$opAcc->refno = $ops->getOpAccommodationRefNo($opAcc->frm_dte, $s_enc_nr);
		if ($opAcc->refno == '') {
			$strSQL = "insert into seg_opaccommodation (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
									"   values ('".$tmp_dte."', '".$s_enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
									"          '".$tmp_dte."')";
			if ($db->Execute($strSQL))
				$opAcc->refno = $ops->getOpAccommodationRefNo($opAcc->frm_dte, $s_enc_nr);
			else {
				$bSuccess = false;
				$err_msg = $db->ErrorMsg();
			}
		}

		$n = 0;
		if ($bSuccess) {
			$strSQL = "insert into seg_opaccommodation_details (refno, room_nr, group_nr, charge, modify_id, create_id, create_dt) ".
								"   values ('".$opAcc->refno."', ".$opAcc->rm_nr.", ".$opAcc->w_nr.", ".$opAcc->nchrg.", '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
								"          '".$tmp_dte."')";
			if ($db->Execute($strSQL)) {
				$n = $ops->getMaxNoFromOPAccomDetails($opAcc->refno);
				$bSuccess = ($n > 0);
			} else {
				$bSuccess = false;
				$err_msg = $db->ErrorMsg();
			}
		}

		if ($bSuccess) {
			foreach ($opDetails as $k => $row) {
				$v = explode(";", $row);

				$strSQL = "INSERT INTO seg_ops_chrgd_accommodation (refno, entry_no, ops_refno, ops_entryno, ops_code, rvu, multiplier) " .
				"   VALUE ('" . $opAcc->refno . "', " . $n . ", '" . $v[0] . "', " . $v[1] . ", '" . $v[2] . "', " . $v[3] . ", " . $v[4] . ")";

				if (!$db->Execute($strSQL)) {
					$bSuccess = false;
					$err_msg = $db->ErrorMsg();
					break;
				}
			}
		}	

		if ($bSuccess) {
			$db->CompleteTrans();
			$objResponse->call("jsRecomputeServices","op");
		} else {
			$db->FailTrans();
			$objResponse->alert("ERROR: ".$err_msg);
		}
	}
	return $objResponse;
}


function delOpAccommodation($enc_nr, $bill_dt, $bill_frmdte, $rm_nr, $idRow) {
	global $db;

	$bSuccess = false;
	$objResponse = new xajaxResponse();

	$parent_encnr = getParentEncounterNr($enc_nr);
	if ($parent_encnr != '')
		$encounter = "('$parent_encnr','$enc_nr')";
	else
		$encounter = "('$enc_nr')";

		// $rm_nr = substr($op_code, 3); // Discard 'OR-'
  	$strSQL = "select * from seg_opaccommodation_details ".
						"   where room_nr = ".$rm_nr." and exists (select * from seg_opaccommodation as so where so.refno = seg_opaccommodation_details.refno ".
						"      and so.encounter_nr in $encounter and so.chrge_dte >= '".$bill_frmdte."') ".
						"      and get_lock('sopd_lock', 10) ".
						"   order by entry_no desc limit 1";
 	$rs = $db->Execute($strSQL);
 	if ($rs) {
		$db->StartTrans();
		$row = $rs->FetchRow();
		if ($row) {
			$refno = $row['refno'];
			$entryno = $row['entry_no'];

			$db->LogSQL();

			$strSQL = "DELETE FROM seg_opaccommodation_details WHERE room_nr = $rm_nr AND entry_no = $entryno AND refno = '$refno'";
			$bSuccess = $db->Execute($strSQL);
			 
			$strSQL = "SELECT RELEASE_LOCK('sopd_lock')";
			$db->Execute($strSQL);

			if ($bSuccess) {

				$dcount = 0;
				$strSQL = "select count(*) dcount from seg_opaccommodation_details where refno = '$refno'";
				$rs = $db->Execute($strSQL);
				if ($rs) {
					$row = $rs->FetchRow();
					$dcount = ($row) ? $row['dcount'] : 0;
					if ($dcount == 0) {
						$strSQL = "delete from seg_opaccommodation WHERE refno = '$refno'";
						$db->Execute($strSQL);
					}
				}
			}
			else
				$err_msg = $db->ErrorMsg();

			$db->LogSQL(false);

		}
	}
	else
		$err_msg = $db->ErrorMsg();

	if ($bSuccess) {
		$db->CompleteTrans();
		//$objResponse->call("delORAccCharge",$idRow);
		$objResponse->call("jsRecomputeServices",'op');
	}
	else{
		$db->FailTrans();
		$objResponse->alert("ERROR: ".$err_msg);
	}

	return $objResponse;
}

/*-------end-------------Operating Room Accomodation Charges----------------end---------------------*/

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

/*-------------------------For Add Doctors------------------------------*/

function ProfFees(&$objResponse, &$objBilling, $enc) {

    $objBilling->getProfFeesList();
    $objBilling->getProfFeesBenefits();

    $hsp_pfs_benefits = $objBilling->getPFBenefits(); //role area
    $ndiscount = 0;
    $proffees_list = $objBilling->proffees_list;
    $objResponse->call("jsClearList", "body_docRoleArea");
    $objResponse->call("jsClearList", "doc-coverage");

    $objBilling->setPFCoverage(0);
    $objBilling->setPFDiscount(0);
    $prevrole_area = '';
    $d1 = 0;
    $d2 = 0;
    $d3 = 0;
    $d4 = 0;

    if (is_array($hsp_pfs_benefits) && (count($hsp_pfs_benefits) > 0)) {
        foreach ($hsp_pfs_benefits as $key => $value) {
            if ($value->role_area == $prevrole_area) continue;
            $prevrole_area = $value->role_area;
            reset($proffees_list);
            $objBilling->initProfFeesCoverage($value->role_area);

            $totalCharge = number_format($objBilling->getTotalPFCharge($value->role_area), 2);
            $coverage = number_format($objBilling->pfs_confine_coverage[$value->role_area], 2, '.', ',');

            $objResponse->call("jsDoctorsFees", "body_docRoleArea", $value->role_area, $value->role_desc, $totalCharge, $coverage);
            $objResponse->call("assignValue", "save_dr_nr", $value->dr_nr);
            $objResponse->call("assignValue", "save_role_area", $value->role_area);
            $objResponse->call("assignValue", "save_dr_charge", $totalCharge);
            // $objResponse->call("assignValue", "save_dr_nr", $value->dr_nr);

            //Display list of doctors in every role area
            $tr = '';
            if (!empty($proffees_list) && is_array($proffees_list)) {
                foreach ($proffees_list as $key => $profValue) {
                    if ($value->role_area == $profValue->role_area) {
                        $opcodes = $profValue->getOpCodes();
                        if ($opcodes != '') {
                            $opcodes = explode(";", $opcodes);
                        }
                        if (is_array($opcodes)) {
                            foreach ($opcodes as $v) {
                                $i = strpos($v, '-');
                                if (!($i === false)) {
                                    $code = substr($v, 0, $i);
                                    if ($objBilling->getIsCoveredByPkg()) break;
                                }
                            }
                        }
                        $caserateWord='';
                        if($profValue->caserate==1){
                        	$caserateWord='1st Caserate';
                        }
                        elseif($profValue->caserate==2){
                        	$caserateWord='2nd Caserate';
                        }
                        elseif($profValue->caserate==3){
                        	$caserateWord='1st and 2nd Caserate';
                        }
                        $roleWord='';
                        if($profValue->role_area=='D3'){
                        	$roleWord='Surgeon';
                        }
                        elseif($profValue->role_area=='D4'){
                        	$roleWord='Anesthesiologist';
                        }
                        // $drName = $profValue->dr_first . " " . $profValue->dr_mid . (substr($profValue->dr_mid, strlen($profValue->dr_mid) - 1, 1) == '.' ? " " : ". ") . $profValue->dr_last." (".$roleWord." - ".$caserateWord.")";
                        $drName = $profValue->dr_first . " " . $profValue->dr_mid . (substr($profValue->dr_mid, strlen($profValue->dr_mid) - 1, 1) == '.' ? " " : ". ") . $profValue->dr_last;

                        $drCharge = number_format($profValue->dr_charge, 2, '.', ',');

                        $totalPF += $profValue->dr_charge;

                        //commented out by Nick 08-19-2014
                        /*if (isDoctorsExists($objBilling->getCurrentEncounterNr(), $profValue->dr_nr, $profValue->getRoleNo())) {

                            $xtd = "<td align=\"center\" style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"5%\"><img src=\"../../images/btn_delitem.gif\" class=\"imgdelete\" style=\"cursor:pointer;\" onclick=\"initMsgDialog(" . $profValue->dr_nr . "," . $profValue->getRoleNo() . ")\"></td><td style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"75%\">" . $drName . "</td>";
                        } else {
                            $xtd = "<td align=\"center\" style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"5%\"><img src=\"../../images/btn_delitem.gif\" class=\"imgdelete\" style=\"cursor:pointer;\" onclick=\"initMsgDialog2(" . $profValue->dr_nr . "," . $profValue->getRoleNo() . ")\"></td><td style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"75%\">" . $drName . "</td>";
                        }*/

                        //added by Nick 08-19-2014
                        switch($profValue->getAccommodationType()){
                            case 1: $accommodationType = 'Service'; break;
                            case 2: $accommodationType = 'Payward'; break;
                            default: $accommodationType = '';
                        }
                        $dr_nr = $profValue->dr_nr;
                        $role = $profValue->getRoleNo();
                        // $objResponse->alert($profValue->getFromOb());
                        if (isDoctorsExists($objBilling->getCurrentEncounterNr(), $profValue->dr_nr, $profValue->getRoleNo())) {
                        	if($profValue->getFromOb()){
                        		  $xtd = <<<html
                                        <td align="Center" width="5%" style="border-top:solid #999999 thin">
                                            <img src="../../images/lock.gif" disabled class="imgdelete" style="cursor:pointer;" onclick="">
                                        </td>
                                        <td width="30%" style="border-top:solid #999999 thin">
                                            $drName
                                        </td>
                                        <td width="45%" style="border-top:solid #999999 thin; border-left: solid #999999 thin;padding-left: 5px;">
                                            $accommodationType
                                        </td>
html;

                        	}else{
                        		  $xtd = <<<html
                                        <td align="Center" width="5%" style="border-top:solid #999999 thin">
                                         <img src="../../images/btn_delitem.gif" class="imgdelete" style="cursor:pointer;" onclick="initMsgDialog('$dr_nr','$role')">
                                        </td>
                                        <td width="30%" style="border-top:solid #999999 thin">
                                            $drName
                                        </td>
                                        <td width="45%" style="border-top:solid #999999 thin; border-left: solid #999999 thin;padding-left: 5px;">
                                            $accommodationType
                                        </td>
html;

                        	}
                          

                        } else {
                        	if($profValue->getFromOb()){
                        		 $xtd = <<<html
                                        <td align="Center" width="5%" style="border-top:solid #999999 thin">
                                            <img src="../../images/lock.gif" class="imgdelete" style="cursor:pointer;" onclick="">
                                        </td>
                                        <td width="30%" style="border-top:solid #999999 thin">
                                            $drName
                                        </td>
                                        <td width="45%" style="border-top:solid #999999 thin; border-left: solid #999999 thin;padding-left: 5px;">
                                            $accommodationType
                                        </td>
html;

                        	}else{
                        		 $xtd = <<<html
                                        <td align="Center" width="5%" style="border-top:solid #999999 thin">
                                            <img src="../../images/btn_delitem.gif" class="imgdelete" style="cursor:pointer;" onclick="initMsgDialog2('$dr_nr','$role')">
                                        </td>
                                        <td width="30%" style="border-top:solid #999999 thin">
                                            $drName
                                        </td>
                                        <td width="45%" style="border-top:solid #999999 thin; border-left: solid #999999 thin;padding-left: 5px;">
                                            $accommodationType
                                        </td>
html;
                        	}
                           
                        }

                        $xtd .= "<td style=\"border-top:solid #999999 thin\" width=\"20%\" align=\"right\">" . $drCharge . "</td>";

                        $tr .= "<tr id=\"dr" . $profValue->dr_nr . "-" . $profValue->getRoleNo() . "\">" . $xtd . "</tr>";
                        $objResponse->assign($value->role_area, "innerHTML", $tr);
                        $objResponse->assign("coverage_" . $value->role_area, "innerHTML", number_format($objBilling->pfs_confine_coverage[$value->role_area], 2, '.', ','));

                        $details->dr_nr = $profValue->dr_nr;
                        $details->area = $value->role_area;
                        $details->charge = $profValue->dr_charge;
                        $details->totalCharge = $totalCharge;
                        $details->accommodationType = $profValue->getAccommodationType();
                        //$objResponse->call("assignDrDetails",$details);
                        switch ($value->role_area) {
                            case 'D1':
                                $d1 += $profValue->dr_charge;
                                break;

                            case 'D2':
                                $d2 += $profValue->dr_charge;
                                break;

                            case 'D3':
                                $d3 += $profValue->dr_charge;
                                break;

                            case 'D4':
                                $d4 += $profValue->dr_charge;
                                break;

                            default:
                                # code...
                                break;
                        }

                        $details->enc = $enc;
                        $details->name = $drName;
                        populateAdjCoverageDoctor(&$objResponse, &$objBilling, $details);

                    }
                } // end foreach proffees_list
            }
        }
        //1st foreach
    } else
        $objResponse->call("jsDoctorsFees", "body_docRoleArea", NULL, '', 0, 0);

    $nPFCharge = round($totalPF, 4);
    $totalExcess = number_format($nPFCharge - round($ndiscount, 4) - $totalPFCoverage, 2, '.', ',');

    $PF->d1 = $d1;
    $PF->d2 = $d2;
    $PF->d3 = $d3;
    $PF->d4 = $d4;
    $PF->pfCharge = $nPFCharge;
    $PF->pfDiscount = $ndiscount;
    $objResponse->call("showPFTotals", $PF);
    $objResponse->call("assignValue", "save_total_doc_charge", $nPFCharge);
    $objResponse->call('calculateDetails');
    if ($nPFCharge == 0) {
        $objResponse->assign('pfDiscount', 'innerHTML', number_format($nPFCharge, 2, '.', ','));
        $objResponse->assign('pfHC', 'innerHTML', number_format($nPFCharge, 2, '.', ','));
    }

    return $objResponse;

}

// end of function ProfFees

function rmPrivateDr($encounter_nr, $dr_nr, $role_nr, $bill_dt){
		global $db;
		$objResponse = new xajaxResponse();

		$db->LogSQL();

		$history = "Deleted by " . $_SESSION['sess_user_name'] . " [" . date('Y-m-d H:i:s') . "] \n";

		$sql = "UPDATE seg_encounter_privy_dr SET is_deleted = 1,".
					 "history = CONCAT(history, '" . $history . "') " .
					 "WHERE dr_nr = ".$dr_nr." ".
					 "   and encounter_nr = '".$encounter_nr."' ".
					 "   and dr_role_type_nr = $role_nr and is_deleted=0 ".
					 " order by entry_no desc limit 1";

		if($db->Execute($sql)){
			$objResponse->call("populateBill");
		}else{
			$objResponse->alert("Deleting Doctor failed\nError:" . $db->ErrorMsg());
		}

		$db->LogSQL(false);

		return $objResponse;
}// end of function rmPrivateDr

function rmDr($encounter_nr, $dr_nr, $role_nr, $bill_dt){
		global $db;
		$objResponse = new xajaxResponse();

		$db->LogSQL();

		$strSQL = "DELETE FROM seg_encounter_dr_mgt ".
							"   WHERE attending_dr_nr = ".$dr_nr." ".
							"      AND encounter_nr = '".$encounter_nr."'";
		if($db->Execute($strSQL)){
			// $_SESSION['computeflag'] = true;
			// $_SESSION['pf_done'] = false;
			// $objResponse->call("toggleBillAreaStatus", 'PF', 1);
			$objResponse->call("populateBill");
		}else{
			$objResponse->alert("Deletion of attending doctor failed!\nSQL = ".$strSQL);
		}

		$db->LogSQL(false);

		return $objResponse;
}// end of function rmDr

function isDoctorsExists($enc_nr, $dr_nr='', $roletype_nr){
	global $db;

	$sql = "SELECT * FROM seg_encounter_privy_dr WHERE encounter_nr = ".$db->qstr($enc_nr)." and dr_nr = ".$db->qstr($dr_nr)." and dr_role_type_nr = ".$db->qstr($roletype_nr)." and is_deleted = '0'";
	
	if($result = $db->Execute($sql)){
				#$objResponse->alert("sqll - " .$sql);
		if($result->RecordCount() >= 1)
			return true;
	}else
		return false;
}// end of function isDoctorsExists()

//added by Carriane 07/12/17
function getCurrentEncounterDetails($encounter_nr=0){
	global $db;

	$sql = "SELECT `encounter_type`, consulting_dept_nr FROM care_encounter WHERE `encounter_nr` = ".$db->qstr($encounter_nr);

	if($result = $db->Execute($sql)){
		if($result->RecordCount() >= 1){

			$rs = $result->FetchRow();
			return $rs;
		}
	}else{
		return false;
	}

}
//end carriane

//added by carriane 7/10/17
function getCurrentAccomodationType($encounter_nr=0){
	global $db;

	$sql = "SELECT ce.current_ward_nr, cw.accomodation_type
			FROM care_encounter ce LEFT JOIN care_ward cw
			ON ce.`current_ward_nr` = cw.`nr`
			WHERE ce.`encounter_nr` = '$encounter_nr'";

	if($result = $db->Execute($sql)){
		if($result->RecordCount() >= 1){

			$rs = $result->FetchRow();
			return $rs['accomodation_type'];
		}else
			return false;
	}else{
		return false;
	}
}
//end of function getCurrentAccomodationType()

function getCurrentConfinementType($encounter_nr=0){
	global $db;

	$sql = "SELECT `casetype_id` FROM seg_encounter_case WHERE `encounter_nr` = ". $db->qstr($encounter_nr) ." AND `is_deleted` = 0";

	if($result = $db->Execute($sql)){
		if($result->RecordCount() >= 1){
			$rs = $result->FetchRow();
			return $rs['casetype_id'];
		}else
			return false;
	}else{
		return false;
	}
}
//end of function getCurrentConfinementType()

function getAccommodation($enc) {
	global $db;
	$Acc = 1;
	$sql = "SELECT cel.`group_nr`,
				   cw.`accomodation_type` 
			FROM care_encounter_location AS cel 
			LEFT JOIN care_ward AS cw 
			ON cel.`group_nr` = cw.`nr` 
			WHERE cel.`encounter_nr` =".$db->qstr($enc)." AND cw.`accomodation_type` = '2'
			AND is_deleted <> 1
			UNION 
			SELECT sela.`group_nr`,
  				   cw.`accomodation_type` 
			FROM seg_encounter_location_addtl AS sela 
  			LEFT JOIN care_ward as cw 
    		ON sela.`group_nr` = cw.`nr` 
			WHERE sela.`encounter_nr` =".$db->qstr($enc)." AND cw.`accomodation_type` = '2' AND is_deleted <> 1";

	if($result = $db->Execute($sql)){
		if($result->RecordCount() >= 1){
			$rs = $result->FetchRow();
			return $rs['accomodation_type'];
		}else
			return $Acc;
	}else{
		return $Acc;
	}
	
}

function setDoctors($admit_inpatient=0, $dept_nr=0, $personell_nr=0, $encounter_nr=0, $opd_area=0) {
	global $db;
	define(OPD_SERVICE, 1);
	define(OPD_PAY, 4);
	define(ASU_SERVICE, 5);
	define(ASU_PAY, 2);
	define(HI_SERVICE, 6);
	define(HI_PAY, 3);
	define(ONCO_SERVICE, 7);
	define(ONCO_PAY, 8);
	
	define(AccPrivate, 2);
	define(AccNonPrivate, 1);

	$pers_obj=new Personell;
	$objbillinfo = new BillInfo();
	$objResponse = new xajaxResponse();

	//added by carriane 082417
	$sql = "SELECT `encounter_type` FROM `care_encounter` WHERE `encounter_nr`= ". $db->qstr($encounter_nr);
	$row = $db->Execute($sql)->FetchRow();
	$encounter_type = $row['encounter_type'];
	$getServiceWard = 0;
	
	$confinement_type = getCurrentConfinementType($encounter_nr);

	if($opd_area != 0){
		if($confinement_type == AccNonPrivate) {
			if($opd_area) {
				$confinement_type == AccNonPrivate;
			}
		}else {
			if((int)$opd_area == AccNonPrivate){
				$confinement_type = AccPrivate;
			}else if((int)$opd_area == AccPrivate){
				$confinement_type = AccNonPrivate;
			}
		}
	}else{
		$confinement_type = getCurrentConfinementType($encounter_nr);
	}

	$accoType = getAccommodation($encounter_nr);

	if(($encounter_type && $accoType == AccNonPrivate && $confinement_type=="") || ($encounter_type && $accoType == AccNonPrivate && $confinement_type == AccPrivate)) {

		$rs=$pers_obj->getServiceWardDoctors();
		$getServiceWard = 1;
	}else{
		if ($dept_nr)
			$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
		else
			$rs=$pers_obj->getDoctorsBilling($admit_inpatient);
	}

	if ($rs) {
		$objResponse->call("js_ClearOptions","doclist");
		if ($pers_obj->count > 0){
			$objResponse->call("js_AddOptions","doclist","-Select a Doctor-",0);
		}else{
			if ($dept_nr)
				$objResponse->call("js_AddOptions","doclist","-No Doctor Available-",0);
			else
				$objResponse->call("js_AddOptions","doclist","-Select a Doctor-",0);
		}
		while ($result=$rs->FetchRow()) {
			$doctor_name = $objbillinfo->concatname($result["name_last"], $result["name_first"], $result["name_middle"]);
			$doctor_name = ucwords(strtolower($doctor_name));

			if($getServiceWard)
				$objResponse->call("js_AddOptions","doclist",$doctor_name . " [" . $result["nr"] . "]", $result["nr"]);
			else
            	$objResponse->call("js_AddOptions","doclist",$doctor_name . " [" . $result["personell_nr"] . "]", $result["personell_nr"]);
		}
	}
	else {
		$objResponse->alert("setDoctors : Error retrieving Doctors information...");
	}

	return $objResponse;
}// end of function setDoctors()

function setRoleArea($jobType){
	global $db;
	$objResponse = new xajaxResponse();

	$sql = "SELECT crp.nr, crp.job_type_nr, crp.role, crp.name, crp.role_area ".
					"\n  FROM care_role_person as crp WHERE job_type_nr = '".$jobType."'";

	if($result = $db->Execute($sql)){
		if($result->RecordCount()){
			$objResponse->call("js_ClearOptions","rolearea");
			$objResponse->call("js_AddOptions","rolearea","-Select a Role area-",0);
			while($row = $result->FetchRow()){
					$objResponse->call("js_AddOptions", "rolearea", $row['name'], $row['nr']);
			}
		}
	}else{
		$objResponse->alert("setRoleArea : Error retrieving role person information...");
	}

	return $objResponse;
}

function setOptionRoleLevel() {
	global $db;
	$objResponse = new xajaxResponse();

	$sql = "SELECT * ".
					"\n  FROM seg_role_tier";

	if($result = $db->Execute($sql)){
		if($result->RecordCount()){
			$objResponse->call("js_ClearOptions", "role_level");
			$objResponse->call("js_AddOptions", "role_level", "-Select Level-",0);
			while($row = $result->FetchRow()){
					$objResponse->call("js_AddOptions", "role_level", $row['tier_desc'], $row['tier_nr']);
			}
		}
	}else{
		$objResponse->alert("setOptionRoleLevel : Error retrieving levels of role ...");
	}
	return $objResponse;
}

function processPrivateDrCharge($aFormValues, $bill_dt = "0000-00-00 00:00:00", $ops='') {
		global $db;
		$objResponse = new xajaxResponse();
		$objBilling = new Billing();
		$bolError = false;
		$err_msg = '';
		$bSuccess = true;

		if ($aFormValues['enc'] != '') {
				if($aFormValues['enc'] == '' && $aFormValues['dr_nr'] == ''){
						$objResponse->alert("Please select a  doctor");
						$bolError = true;
				}else{
						// Adjust current time by 1 second earlier than cut-off date in billing ...
						if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
								$tmp_dte = $bill_dt;
						else
								$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

						$tmpbill_dte = $tmp_dte;
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));
						// $objBilling = unserialize($_SESSION['billobject']['main']);
				}
				if($aFormValues['role_nr'] == ''){
						$objResponse->alert("Please specify doctors role.");
						$bolError = true;
				}

				$n = 0;
				if(!$bolError){
						if(isDoctorsExists($aFormValues['enc'], $aFormValues['dr_nr'], $aFormValues['role_nr'])) {
							$objResponse->alert("Doctor with the same role is already in the list!");
						}
						else {
						$nCharge = str_replace(",", "", $aFormValues['charge']);

						$db->StartTrans();

						$aFormValues['ndays'] = (($aFormValues['ndays'] == "") ? 0 : $aFormValues['ndays']);
							$history = "Added by " . $_SESSION['sess_user_name'] . " [" . date("Y-m-d H:i:s") . "]\n";

							$sql = "insert into seg_encounter_privy_dr (encounter_nr, dr_nr, dr_role_type_nr, dr_level, days_attended, caserate, dr_charge, is_excluded, create_id, create_dt, history) " .
								 "   values ('".$aFormValues['enc']."', ".$aFormValues['dr_nr'].", ".$aFormValues['role_nr'].", 0, ".$aFormValues['ndays'].", ".$aFormValues['rate_type'].", ".$nCharge.", ".
													 "            ".$aFormValues['excluded'].", '".$_SESSION['sess_user_name']."', '". $tmp_dte ."', '". $history ."')";

						if ($db->Execute($sql)) {
								$n = getMaxNoFromPrivyDr($aFormValues['enc'], $aFormValues['dr_nr'], $aFormValues['role_nr']);
								$bSuccess = ($n > 0);
						}
						else {
								$bSuccess = false;
								$err_msg = $db->ErrorMsg().' '.$sql;
						}

						if($bSuccess) {
								if (!empty($ops)) {
										foreach ($ops as $k => $row) {
												$v = explode(";", $row);
												$sqltmp = array();
												$sqltmp[0] = ($aFormValues['tier_nr'] != 0) ? 'role_type_level, ' : '';
												$sqltmp[1] = ($aFormValues['tier_nr'] != 0) ? $aFormValues['tier_nr'].", " : '';

                        
                        if (is_object($objBilling)) {
                          $tmpmult = $objBilling->getHouseCasePCF($aFormValues['enc'],$bill_dt);
                          if ($tmpmult != 0) $v[4] = $tmpmult;
                        }

												$strSQL = "insert into seg_ops_chrg_dr (encounter_nr, dr_nr, dr_role_type_nr, entry_no, ".$sqltmp[0]."ops_refno, ops_entryno, ops_code, rvu, multiplier) ".
																	"   value ('".$aFormValues['enc']."', ".$aFormValues['dr_nr'].", ".$aFormValues['role_nr'].", ".$n.", ".$sqltmp[1]."'".$v[0]."', ".$v[1].", ".
																	"          '".$v[2]."', ".$v[3].", ".$v[4].")";

												if (!$db->Execute($strSQL)) {
														$bSuccess = false;
														$err_msg = $db->ErrorMsg().' '.$sql;
														break;
												}
										}
								}
						}
						else {
								$bSuccess = false;
								$err_msg = $db->ErrorMsg();
						}

                        if($objBilling->setDoctorAccommodationType($aFormValues['enc'], $aFormValues['dr_nr'],$n, $aFormValues['accommodationType'])){
                            $bSuccess = true;
                        }else{
                            $bSuccess = false;
                            $err_msg = $db->ErrorMsg();
                        }

						if ($bSuccess) {
							$db->CompleteTrans();
							$objResponse->alert("New doctor successfully added in the list!");
							$objResponse->call("populateBill");
						}
						else{
							$db->FailTrans();
							$objResponse->alert("ERROR: ".$err_msg);
						}
				}
		}
		}

		return $objResponse;
}// end of function ProcessPrivateDrCharge()

function getMaxNoFromPrivyDr($enc_nr, $drnr, $roletypenr) {
	global $db;

	$n = 0;
	$strSQL = "select ifnull(max(entry_no), 0) as latest_no ".
						"   from seg_encounter_privy_dr as sod ".
						"   where encounter_nr = '".$enc_nr."' ".
						"      and dr_nr = ".$drnr.
						"      and dr_role_type_nr = ".$roletypenr;

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow())
				$n = $row['latest_no'];
		}
	}

	return($n);
}

function getDrRole($role_nr){
	global $db;
	$objResponse = new xajaxResponse();

	$strSQL = "SELECT role_area
					FROM care_role_person
					WHERE nr = ".$db->qstr($role_nr);

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$objResponse->call("drRole",$row['role_area']);
			}
		}else{return false;}
	}else{return false;}

	return $objResponse;
}


function delDoctors($enc){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$delDoc = $objBilling->delEncDoctors($enc);

	//$objResponse->call("selectCaseRate",$caseNum);
	//$objResponse->call("populateBill");
	
	return $objResponse;
}

/*---------end-------------For Add Doctors--------------end-------------*/

function populateBillHeader(&$objResponse,$details)
{
	global $db;

	$encounter_date = $db->GetOne("SELECT encounter_date FROM care_encounter WHERE encounter_nr = '$details->encounter_nr'");

	$details->fbill_dte = strftime("%b %d, %Y %I:%M%p", strtotime($details->bill_dte));
	//$details->fbill_frmdte = strftime("%b %d, %Y %I:%M%p", strtotime($details->bill_frmdte));
	$details->fbill_frmdte = strftime("%b %d, %Y %I:%M%p", strtotime($encounter_date));
	$details->fdeath_date = (($details->death_date != '') ? strftime("%b %d, %Y %I:%M%p", strtotime($details->death_date)): strftime("%b %d, %Y %I:%M%p", strtotime(date('Y-m-d'))));
	$objResponse->call('billingHeader',$details);
	return $objResponse;
}

function toggleMGH($data, $bsetMGH) {
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$enc_nr = $data['encounter']; 


	if (strcmp($data['billdate'], "0000-00-00 00:00:00") != 0)
		$mgh_date = "'".strftime("%Y-%m-%d %H:%M:%S", strtotime($data['mgh_date']))."'";
	else
		$mgh_date = "NULL";


	$SaveOK=$objBilling->toggleMGH($enc_nr, $mgh_date, $bsetMGH);
	if (!$SaveOK) {
		$objResponse->alert($db->ErrorMsg());
	}
	return $objResponse;
}

	function saveThisBilling($data, $final, $details, $process_type) {
		$objResponse = new xajaxResponse();
		$objBilling = new Billing();
		$objDialysis = new SegDialysis();
		$radioObj = new SegRadio();

		if(!validateBill($data,$objResponse)){
			return $objResponse;
		}

		$user = $_SESSION['sess_user_name'];
	    $data['billdatefrom']=strftime("%Y-%m-%d %H:%M:%S", strtotime($data['billdatefrom']));
		$data['bill_time_started'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($data['bill_time_started']));
		$data['bill_time_ended'] = ($final == 1) ? strftime("%Y-%m-%d %H:%M:%S", time()) : '';

	    global $db;
	    $db->BeginTrans();
	    
	    if($final){
			$radioObj->deleteAllPendingRequestByEncounter($data['encounter'], 'billing');
	    }

	    //Added by EJ 12/12/2014
	    $is_locked = $db->GetOne("SELECT IF(IS_USED_LOCK('is_finall_bill') IS NULL, FALSE , TRUE )");

	    if ($is_locked) {
	    	$objResponse->call("hideLoading");
	    	$objResponse->call("unsetFinalBill");
	    	$objResponse->alert("Another transaction is being processed. Please try again later.");
	    	
	    	return $objResponse;
	    }
	    else {
	    	$db->GetOne("SELECT GET_LOCK('is_finall_bill',10)");
			$hasbillnr = $objBilling->getbillnr($data);
	    }
	    //Ended by EJ 12/12/2014

		$prevencounter = $objBilling->getPrevEncounter($data['encounter']);
		$confinment = $objBilling->getConfinementType();
		
		//$prevpayment = $objBilling->getPreviousPayments();
		$objData = new stdClass();
		$objData->encounter_nr = $data['encounter'];
		$objData->bill_curDate = $data['billdate'];
		$data['isInfirmaryOrDependent'] = $data['isInfirmaryOrDependent'];

		if ($hasbillnr){
			$bill_nr = $hasbillnr;
			$savebill = $objBilling->updatebill($data, $hasbillnr, $final);
		}else{
			$bill_nr = $objBilling->getNewBillingNr();
			$savebill = $objBilling->savebill($data, $bill_nr, $final);
		}

		$db->GetOne("SELECT RELEASE_LOCK('is_finall_bill')"); //Added by EJ 12/12/2014
		$db->CommitTrans();
		$db->BeginTrans();
		
		if(!empty($details)){
			foreach($details as $key => $value){
				$values = explode("_",$value);
		        $insert .= "('".$bill_nr."','".$values[0]."','".$values[1]."'),";
			}
			$insert = substr($insert, 0, -1);
		}else{
			$insert = "('".$bill_nr."','0','NONE')";
		}
		
		// Added by James 1/7/2014
		if($savebill){
			$saveDetails = $objBilling->saveRefNo(&$insert);
		}// End If

		$isDischarge = TRUE;
		if($final && $objBilling->isDialysisPatient($data['encounter'])){
			$isDischarge = $objDialysis->DisableDialysisEncounter($data['encounter']);
		}
		$proceededToCommit = FALSE;

		if($savebill && $saveDetails) $proceededToCommit=$db->CommitTrans();

		if($proceededToCommit){
			if($final) {
				$removed_credit_collection = $objBilling->checkCreditCollectionNBB($data['encounter'], $bill_nr);

				if($removed_credit_collection) {
					$insurance_exist_credit_collection = $objBilling->checkExistingInsuranceCreditCollectionNBB($data['encounter']);

					if(!$insurance_exist_credit_collection) {
						$objBilling->removeInsuranceCreditCollectionNBB($data['encounter'], $bill_nr);
					}
				}
			}
			$objResponse->call("hideLoading");
			$objResponse->alert("Successfully Saved!");
			//added by borj 2014-06-01
			$objResponse->call("disabled_button",$final);
			$objResponse->call("setBillNr",$bill_nr);
			$objResponse->call("setBillStarted", date('Y-m-d H:i:s'));
			//	$objResponse->call("showSoa");
	        showSoa($data['encounter'],$objResponse);
		}else{
			$objResponse->call("hideLoading");
			$objResponse->call("unsetFinalBill");
			$objResponse->alert("Failed to save Billing!");
			$objResponse->call('console.log',$objBilling->getError());//added by Nick, 4/23/2014
		}

		return $objResponse;
	}

	//added by Nick, 12/27/2013
	function setBillNr($data){
		$objResponse = new xajaxResponse();
		$objBilling = new Billing();

		$bill_nr = $objBilling->getbillnr($data);
		$objResponse->call("setBillNr",$bill_nr);
		$objResponse->call("showSoa");
		return $objResponse;
	}
	//end Nick

	//added by ken 1/4/2013
	function checkInsurance($enc){
		$objResponse = new xajaxResponse();
		$objcheck = new Billing();

		$result = $objcheck->checkInsuranceRequest($enc);
		
		if($result->RecordCount() != 0){
			$objResponse->call("packageDisplay",'PHIC');
		}
		else{
			$objResponse->call("packageDisplay",'Other');
		}
		return $objResponse;
	}
	function getAdmittingDiag($enc){
		$objResponse = new xajaxResponse();
		$objcheck = new Billing();
		$error_alert=array();

		$result = $objcheck->checkAdmittingDiag($enc);
		if($result->RecordCount() == 0){
			array_push($error_alert, "Patient has no admitting diagnosis.");
		}

		$result = $objcheck->checkFinalDiag($enc);
		if($result->RecordCount() == 0){
			$resultEdit = $objcheck->checkEditFinalDiag($enc);
			if($resultEdit->RecordCount() == 0){
				array_push($error_alert, "Patient has no final diagnosis.");
			}
			
		}
		
		if (count($error_alert) == 0) {
		}else{
			$objResponse->call('PromptDiagMsg', 'Warning', implode('<br/>', $error_alert));
		}

		return $objResponse;

	}

	//added by poliam 01/04/2014
	function classification($enc, $bill_dte, $bill_from){
		$objResponse = new xajaxResponse();
		$objBilling = new Billing();

		
		$prevenc = trim($objBilling->getPrevEncounter($enc));
		$IsCharity = $objBilling->AccommodationType($enc, $bill_dte, $bill_from, $prevenc);
		$IsEr = $objBilling->isERPatient($enc);
		$classification = $objBilling->Classification($enc, $bill_dte, $IsCharity, $IsEr, $prevenc);
		$objResponse->call("ClassificationHeader",$classification);

		return $objResponse;
	}

	function confinment($enc){
		$objResponse = new xajaxResponse();
		$objBilling = new billing();

		$confinment = $objBilling->isDialysisPatient($enc);
		$objResponse->call("ConfinmentHeader", 1);//edited by Kenneth 02/15/17

		return $objResponse;
	}	

	function getConfineTypeOption($enc,$bill_date){
		$objResponse = new xajaxResponse();
		$objBilling = new billing();
		global $db;

		$sql = "select casetype_id as id, casetype_desc as typedesc from seg_type_case";

		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$objResponse->call("js_ClearOptions", "confineTypeOption");
						$objResponse->call("js_AddOptions","confineTypeOption", "- Select Confinement Type -", 0);
						while($row = $result->FetchRow()){
								$objResponse->call("js_AddOptions","confineTypeOption", $row['typedesc'], $row['id']);
						}
						$prevenc = trim($objBilling->getPrevEncounter($enc));
						$nConfinementId = $objBilling->getCaseTypeID($enc, $bill_date, $prevenc);
						if($nConfinementId){
								$objResponse->call("js_setOption", "confineTypeOption", $nConfinementId);
						}else{
								//$objResponse->alert("No confinement type set as of indicated bill date!");
								$objResponse->call("js_setOption", "confineTypeOption", "- Select Confinement Type -");
						}
				}else{
						//$objResponse->alert("ERROR: No confinement types found");
				}
		}

		return $objResponse;
} 
		

function setConfinementType($enc, $type, $classify_id, $create_id, $bill_dte = "0000-00-00 00:00:00"){
        global $db;
        $objResponse = new xajaxResponse();

    //Insert new data to seg_encounter_confinement
        if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0) {
        $modify_dt = date('Y-m-d H:i:s');
        $create_dt = date('Y-m-d H:i:s');
        }
        else {
        $modify_dt = $bill_dte;
        $create_dt = date('Y-m-d H:i:s');
        }
    $modify_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($modify_dt)));
//						$create_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($create_dt)));
    $create_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime($create_dt));

    if ($type != '0') {
        $fldarray = array('encounter_nr' => $db->qstr($enc),
                        'casetype_id'  => $db->qstr($type),
                        'modify_id'    => $db->qstr($modify_id),
                        'modify_dt'    => $db->qstr($modify_dt),
                'create_id'    => $db->qstr($create_id),
                        'create_dt'    => $db->qstr($create_dt),
                        'is_deleted'    => $db->qstr(0) //added by jasper 05/10/2013
               );
      //edited by jasper 05/10/2013 - TO INCLUDE IS_DELETED FIELD. ONE CASE TYPE EVERY ENCOUNTER ONLY EXCEPT DELETED
      $bsuccess = $db->Replace('seg_encounter_case', $fldarray, array('encounter_nr', 'is_deleted'));
       
        if($bsuccess){
          $objResponse->alert("Confinement type is successfully saved!");
      }else
      	$objResponse->alert("Confinement type is NOT saved!");
    }
    else {
      // Logically delete the set encounter case types later than current bill date ...
      //edited by jasper 05/09/2013 - FROM >= CHANGED TO <=
      $strSQL = "UPDATE seg_encounter_case SET
                    is_deleted = 1,
                    modify_id = '".$modify_id."',
                    modify_dt = NOW()
                 	WHERE
                    str_to_date(modify_dt, '%Y-%m-%d %H:%i:%s') <= '" . $bill_dte ."'
					          AND encounter_nr = '".$enc."' and !is_deleted";
      //$objResponse->alert($strSQL);
      if ($db->Execute($strSQL)) {
          $strSQL = "SELECT ROW_COUNT() rcount";
          if ( $result = $db->Execute($strSQL) ) {
            if ( $row = $result->FetchRow() ) {
              if ($row['rcount']) $objResponse->alert($row['rcount']." case types set at a later date deleted!");
            }
          }
      }
    }

    $objResponse->call('calculateTotals');

		return $objResponse;
} 
//ended by poliam 01/05/2014

//Added by borj 2014-4-01
	function clearBilling() {
		$objResponse = new xajaxResponse();
		$formatted_bill_dte = strftime("%b %d, %Y %I:%M%p");
		$objResponse->call("clearBillingHeaderInfo", date("Y-m-d H:i:s"), $formatted_bill_dte);
		return $objResponse;
}

function showBilling($sbill_nr) {
		$objResponse = new xajaxResponse();

		$objbillinfo = new BillInfo();
		$result = $objbillinfo->getBillingHeaderInfo($sbill_nr);
		if ($result) {
				if ($row = $result->FetchRow()) {
						$spatient_name = $objbillinfo->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);

						$addr = $row["street_name"];
						if ($row["brgy_name"])
								$addr.=", ".$row["brgy_name"];
						if ($row["mun_name"])
								$addr.=", ".$row["mun_name"];
						if ($row["prov_name"])
								$addr.=", ".$row["prov_name"];
						if ($row["zipcode"])
								$addr.=" ".$row["zipcode"];
       
						$admission_dt       = strftime("%b %d, %Y %I:%M%p", strtotime($row["admission_date"]));
						$formatted_bill_dte = strftime("%b %d, %Y %I:%M%p", strtotime($row["bill_dte"]));

                        if(strcmp($row["deathdate"], "0000-00-00 00:00:00") != 0){
                            $formatted_ddte = strftime("%b %d, %Y %I:%M%p", strtotime($row["deathdate"]));
                            $ddate = $row["deathdate"];
        }else{
                            $formatted_ddte = '';
                            $ddate = '';
                        }
                        $objResponse->call("assignBillingHeaderInfo", $row["bill_nr"], $row["encounter_nr"], $row["pid"], $row["bill_dte"], $row["bill_frmdte"], $formatted_bill_dte, $admission_dt, $spatient_name, $addr, $formatted_ddte, $ddate);
                        //$objResponse->alert( $row["encounter_nr"] . " " . $row["bill_nr"]);
				}
        }

        return $objResponse;
}

function deleteBilling($sbill_nr, $enc_nr, $delete_reason, $delete_other_reason = '', $bill_time_started) {
		$objResponse = new xajaxResponse();
		$objbill = new BillInfo();
		$objBilling = new Billing(); // added by michelle 06-26-2015
		$creditColObj = new CreditCollection(); //added by Louie 7-30-2015
        //$objResponse->alert($objbill->deleteBillInfo($sbill_nr, $enc_nr));

        // added by michelle 06-26-2015
        $objBilling->encounter_nr = $enc_nr;
        $objBilling->old_bill_nr = $sbill_nr;
		$nbbTypes = $objBilling->checkMembership($enc_nr);
		$objBilling->memcategory_id = $nbbTypes;
		
		
		if ($objBilling->isNbb()) {
			$type = 'nbb';
 		} else {
			switch (mb_strtoupper($objBilling->isInfirmaryOrDependent($enc_nr))) {
			  case 'INFIRMARY':
				$type = 'infirmary';
				break;
			  case 'DEPENDENT':
				$type = 'dependent';
				break;
			}

			if(!isset($type) && $type == null){
				$rs = $creditColObj->getCollectionByType("dependent",$enc_nr);
				if($rs){
					$type = "dependent";
				}
			}
		}
		
		$data = array();
		if ($type !== NULL)
			$data = array('type' => $type, 'encounter' => $enc_nr, 'bill_nr' => $sbill_nr, 'delete_reason' => $delete_reason);
		//end
		$creditColObj->deleteMSSDiscount($sbill_nr,$enc_nr); // added by mary~06-29-2016
		$creditColObj->deleteCredCollection($enc_nr, $type); //added by Louie 7-30-2015
		$objBilling->saveBillingTransaction($sbill_nr, $enc_nr, $bill_time_started, 2);
		$objBilling->updateAccommodation($enc_nr, '0000-00-00', 1);
		if ($objbill->deleteBillInfo($sbill_nr, $enc_nr, $delete_reason, $delete_other_reason, $data)) {

			$sWarning = 'Billing successfully deleted!';
			clearBilling();
			//$objResponse->call("closeSaveIndicator"); //removed by jasper 04/04/2013
            //added by jasper 04/04/2013
            $objResponse->call("js_NewBilling");
        }
		else {
			$sWarning = 'ERROR: '.$objbill->getErrorMsg();
		}

		$objResponse->alert($sWarning);
        return $objResponse;
}

function isPHS(&$objResponse, &$objBill, $enc)
{
	$objResponse->call("assigPHS",$objBill->checkIfPHS($enc));
}

function getCaseTypeOption(&$objResponse, &$objBill,$enc){
	global $db;
	#query Edited By Mark 06-15-2016
    $cond="";
	if (!$billinfo = $objBill->hasSavedBill($enc)) {
		$cond = "AND confinetype_id NOT IN(1,3,4,5)";
	}

	$sql = "select confinetype_id as id , confinetypedesc as typedesc\n
				 from seg_type_confinement\n
 				 where is_deleted = 0 ".$cond;


		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$objResponse->call("js_ClearOptions", "caseTypeOption");
						$objResponse->call("js_AddOptions","caseTypeOption", "- Case Type None -", 0);

						while($row = $result->FetchRow()){
								$objResponse->call("js_AddOptions","caseTypeOption", $row['typedesc'], $row['id']);
						}
						
						$confinementId = $objBill->getConfinementType();
						$caseTypeHist = $objBill->getCaseTypeHist();
						$objResponse->call('addTooltip','caseTypeOption',$caseTypeHist[0],date('Y-m-d h:i A',strtotime($caseTypeHist[1])));

						if($confinementId){
								$objResponse->call("js_setOption", "caseTypeOption", $objBill->getConfinementType());
						}else{
								$objResponse->call("js_setOption", "caseTypeOption", "A");
						}

						$objResponse->call('disableCaseTypeOptions');
				}
		}
		return $objResponse;

} // end of function getCaseTypeOption


function setCaseType($enc, $type, $create_id, $bill_dte = "0000-00-00 00:00:00"){
        global $db;
        $objResponse = new xajaxResponse();

        $classify_id = $_SESSION['sess_user_name'];

        //Insert new data to seg_encounter_confinement
        if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0) {
                $classify_dte = date('Y-m-d H:i:s');
                $create_time = date('Y-m-d H:i:s');
        }
        else {
                $classify_dte = $bill_dte;
                $create_time  = date('Y-m-d H:i:s');
        }
        $classify_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($classify_dte)));
        $create_time = strftime("%Y-%m-%d %H:%M:%S", strtotime($create_time));

        #updated by Carriane 08/03/17
        #Added by Jarel 06/17/2013
        
        if(!empty($type)){
        	#Series of query for old data
        	$sql_check = $db->GetOne("SELECT confinetype_id FROM seg_encounter_confinement
        			WHERE `encounter_nr` = ".$db->qstr($enc)." AND `confinetype_id` = ".$db->qstr($type). " AND is_deleted = 0");
        	if($sql_check){
        		
        		$sql_count = "SELECT * FROM `seg_encounter_confinement` WHERE `encounter_nr` = ". $db->qstr($enc);

        		$count_ok = $db->Execute($sql_count);
        		if($count_ok->RecordCount() > 1){
        			$sql_update = "UPDATE `seg_encounter_confinement` SET `is_deleted` = 1, `classify_id` = ".$db->qstr($classify_id) .", `classify_dte` = ".$db->qstr($classify_dte)." WHERE `encounter_nr` = ".$db->qstr($enc)." AND `is_deleted` = 0 AND `confinetype_id` != ". $db->qstr($type);
        		}else{
        			$sql_update = "UPDATE `seg_encounter_confinement` SET `classify_id` = ".$db->qstr($classify_id) .", `classify_dte` = ".$db->qstr($classify_dte)." WHERE `encounter_nr` = ".$db->qstr($enc)." AND `confinetype_id` = ". $db->qstr($type) . " AND `is_deleted` = 0";
        		}

        		if($db->Execute($sql_update))
        			$bsuccess = 1;

        	}else{
		        $fldarray = array('encounter_nr' => $db->qstr($enc),
		                'confinetype_id'  => $db->qstr($type),
		                'classify_id'    => $db->qstr($classify_id),
		                'classify_dte'    => $db->qstr($classify_dte),
		                'create_id'    => $db->qstr($create_id),
		                'create_time'    => $db->qstr($create_time),
		                'is_deleted' => $db->qstr(0)
		               );

		        $bsuccess = $db->Replace('seg_encounter_confinement', $fldarray, array('encounter_nr', 'is_deleted'));
        	}


	        if($bsuccess){
	            $objResponse->alert("Case type is successfully saved!");
	            $objResponse->call("populateBill"); # added by: syboy 08/11/2015
	            $objResponse->call("isNBB");
	            $objResponse->call("js_Recalculate");
	        }else{
	            $objResponse->alert("Case type is NOT saved!" . $db->ErrorMsg());
	        }
        	
        }else{
        	$strSQL = "UPDATE seg_encounter_confinement SET
                    is_deleted = 1,
                    classify_id = ".$db->qstr($classify_id).",
                    classify_dte = NOW()
                 	WHERE
                    str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') <= " . $db->qstr($classify_dte) ."
					          AND encounter_nr = ".$db->qstr($enc)." and !is_deleted";

	      	if ($db->Execute($strSQL)) {
	          	$objResponse->alert("No Case Type Selected. Defaulted to B");
	            $objResponse->call("populateBill");
	            $objResponse->call("isNBB");
	            $objResponse->call("js_Recalculate");
	      	}else{
	            $objResponse->alert("Case type is NOT saved!" . $db->ErrorMsg());
	        }
        }

        return $objResponse;
}

//added by Nick, 1/9/2014
function getTotalAppliedDiscounts(&$objResponse,$enc){
	global $db;

	$sql = "SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount 
			WHERE encounter_nr = ".$db->qstr($enc);

	$rs = $db->Execute($sql);
	if($rs){
		if($rs->RecordCount()>0){
			$row = $rs->FetchRow();
			$objResponse->call("setTotalDiscounts",$row['total_discount']);
		}else{
			$objResponse->call("setTotalDiscounts",0);
		}
	}else{
		$objResponse->call("setTotalDiscounts",0);
	}
}


/**
* Created By Jarel
* Created On 02/12/2014
* Save Outside Medicine and XLO amount
* @param string meds
* @param string xlo
* @return mixed objResponse
*/
function saveOutMedsXLO($enc,$meds,$xlo){
	global $db;

    $objResponse = new xajaxResponse();

    $user = $_SESSION['sess_temp_userid'];
    $create_time = strftime("%Y-%m-%d %H:%M:%S");

    $fldarray = array('encounter_nr' => $db->qstr($enc),
            'total_xlo'  => $db->qstr($xlo),
            'total_meds'    => $db->qstr($meds),
            'create_id'    => $db->qstr($user),
            'create_dt'    => $db->qstr($create_time),
            'modify_id'		=> $db->qstr($user)
           );

    $bsuccess = $db->Replace('seg_encounter_reimbursed', $fldarray, array('encounter_nr'));
   
    if($bsuccess){
            $objResponse->alert("Successfully Saved!");
    }else{
            $objResponse->alert("Failed To Saved!");
    }

    return $objResponse;
}


/**
* Created By Jarel
* Created On 02/12/2014
* Fetch saved data in seg_encounter_reimbursed
* @param string meds
* @param string xlo
* @return mixed objResponse
*/
function getOutMedsXLO($enc){
	global $db;

    $objResponse = new xajaxResponse();

    $sql ="SELECT total_meds, total_xlo 
    	   FROM seg_encounter_reimbursed
    	   WHERE encounter_nr =  ".$db->qstr($enc);
   
	$rs = $db->Execute($sql);
	if($rs){
		if($rs->RecordCount()>0){
			$row = $rs->FetchRow();
			$objResponse->assign("meds_total",'value',number_format($row['total_meds'],2,'.',','));
			$objResponse->assign("xlo_total",'value',number_format($row['total_xlo'],2,'.',','));
		}else{
			$objResponse->assign("meds_total",'value',0);
			$objResponse->assign("xlo_total",'value',0);
		}
	}else{
		$objResponse->assign("meds_total",'value',0);
		$objResponse->assign("xlo_total",'value',0);
	}
	return $objResponse; 
}

//added by Nick, 4/8/2014
function isInfirmaryOrDependent(&$objResponse, &$objBill){
	$objResponse->call('set_isInfirmaryOrDependent',$objBill->isInfirmaryOrDependent($objBill->encounter_nr));
}

//added by Nick, 4/8/2014
// function setAdditionalInfos(&$objResponse, &$objBill, $objData){
// 	$objResponse->call('setIsInfirmaryOrDependent',$objBill->isInfirmaryOrDependent($objData));
// 	$objResponse->call('setIsNewBorn',$isNewBorn);//added by Nick, 4/21/2014
// }

//added by Nick, 4/22/2014
function updateHearingTest($enc,$value){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	$rs = $objBilling->updateHearingTest($enc,$value);
	if(!$rs){
		$objResponse->alert("Failed to update Hearing test!");
		$objResponse->call('console.log',$objBilling->error_msg);
	}
	return $objResponse;
}

function setArguments($enc,$bill_dte,$bill_frmdte,$death_date)
{
	$objBill = new Billing();

	if($death_date ==''){
		$death_date = $objBill->getDeathDate($enc);
	}

	
	if ($billinfo = $objBill->hasSavedBill($enc)) 
	{
		if ($billinfo['is_final'] == 1) {
		$objBill->setBillArgs($enc,$billinfo['bill_dte'],$billinfo['bill_frmdte'],$death_date,$billinfo['bill_nr']);
		}
		else{
$objBill->setBillArgs($enc,$bill_dte,$billinfo['bill_frmdte'],$death_date,$billinfo['bill_nr']);
		}
		
	} 
	else 
	{
		$objBill->setBillArgs($enc,$bill_dte,$bill_frmdte,$death_date);
	}

	return $objBill;
}



/**
* Created By Jarel
* Created On 04/12/2014
* Populate data in adjusted coverage (Doctor's Area)
* @param object objResponse
* @param object objBilling
* @param object details
* @return mixed objResponse
*/


function populateAdjCoverageDoctor(&$objResponse, &$objBilling, $details)
{
	$th = '';
	$td  = '';
	$footer = '';
	$global_DrCheck = 0;
	global $db;
	$billinfo = $objBilling->hasSavedBill($details->enc);
	$refno = (($billinfo['bill_nr']) ? $billinfo['bill_nr'] : 'T'.$details->enc);
	
	$discount = $objBilling->getTotalAppliedDiscounts($details->enc);
	
	$result = $objBilling->getPerHCareCoverage($details->enc);

	$pers_obj = new Personell; // added by JOY @ 02-21-2018
	$enc_obj = new Encounter(); // added by JOY @ 02-21-2018
	$eclaims_accreditation_enable = Config::model()->get('eclaims_accreditation_switch'); // added by JOY @ 02-21-2018

	if ($result->RecordCount()) {
		while ($row = $result->FetchRow()) {
			$th .= 	"<th width=\"1%\" nowrap=\"nowrap\" ></th>
					 <th width=\"5%\" nowrap=\"nowrap\">".ucwords($row['firm_id'])." 1st Case</th>".
					 "<th width=\"1%\" nowrap=\"nowrap\" ></th>
					 <th width=\"5%\" nowrap=\"nowrap\">".ucwords($row['firm_id'])." 2nd Case</th>";
			
			# Mod by jeff 02-14-18 for verifying accradited PHIC doctors upon adding in billing.	
		 	$glob_obj=new GlobalConfig;
			$global_DrCheck = $glob_obj->getDrSwitch();

			 // if ($global_DrCheck == 1) {
			// $AccreditedDrCheck =  $objBilling->getCheckDoctorAccreditation($details->dr_nr);
			// added by JOY @ 02-21-2018
			$isPhic = $enc_obj->isPHIC($details->enc);
			$checkDrAccre = $pers_obj->get_Doctor_Accreditation($details->dr_nr);
			$errorMsg = "";
			$valid = 1;
			$datenow = date("Y-m-d");
			if ($checkDrAccre) {
				if (is_object($checkDrAccre)) {
                    while ($drRow = $checkDrAccre->FetchRow()) {
                    	if ($drRow['accreditation_nr']=='') {
                    		$valid = 0;
                            $errorMsg = "The doctor is not accredited! Please provide the accreditation number.";
                        }elseif (($drRow['accreditation_start']) && ($drRow['accreditation_end'])){
                            $drAccreStart = date("Y-m-d",strtotime($drRow['accreditation_start']));
                            $drAccreEnd = date("Y-m-d",strtotime($drRow['accreditation_end']));
                            if ($datenow < $drAccreStart  || $datenow > $drAccreEnd) {
                                $valid = 0;
                                $errorMsg = "The doctor is not accredited! Validity of accreditation ".$drRow['seg_dr_accreditation']." has already expired! Validity Period : ".date("m/d/Y",strtotime($drAccreStart))." to ".date("m/d/Y",strtotime($drAccreEnd));
                            }
                        }elseif (($drRow['accreditation_start']==NULL) || ($drRow['accreditation_end']==NULL)) {
                            $valid = 0;
                            $errorMsg = "The doctor is not accredited! Please provide the validity of accreditation.";
                        }
                    }
                }
			}else{
				$valid = 0;
                $errorMsg = "The doctor is not accredited! Please provide the accreditation number.";
			} // end by JOY

			$result1 = $objBilling->getDoctorCoverageDetails($refno, $row['hcare_id'], $details->dr_nr, $details->area);
			$id = $row['hcare_id']."_".$details->dr_nr."_".$details->area; // added by JOY @ 02-21-2018
			if ($result1->RecordCount()) {
				while ($row1 = $result1->FetchRow()) {
					if($row1['dr_claim']!=0 &&($row1['first_claim']==null && $row1['second_claim']== null)){
						$row1['first_claim']=$row1['dr_claim'];
					}
					// modified by JOY @ 02-21-2018
					// if ($AccreditedDrCheck || $global_DrCheck == 0) {
					if ($isPhic) {
						if ($eclaims_accreditation_enable == '0') {
							$drAccre_click = "applyAllCoverage_1('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
						}else {
							if ($valid == '1') {
								$drAccre_click = "applyAllCoverage_1('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
							}else{
								$drAccre_click = "checkDrAccreditation1('".$id."',".$valid.",'".$errorMsg."');";
							}
						} 
					}else{
						$drAccre_click = "applyAllCoverage_1('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
					}
					
					$check1 = "<input id=\"apply1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
									class=\"segInput uncheckFirstCase\" type=\"checkbox\" ".(($row1['first_claim']!=0) ? 'checked="checked"' : '' )."
									onclick=\"$drAccre_click\">";
					// end by JOY

					if ($valid == '1') {
						$inputs1 = "<input id=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare1 clearInputFirstCase\"
								name=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation1(this)){calculateDetails1();}\" value=\"".number_format($row1['first_claim'],2,'.','')."\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
					}else{
						$inputs1 = "<input id=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare1 clearInputFirstCase\" disabled=\"disabled\"
								name=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation1(this)){calculateDetails1();}\" value=\"".number_format($row1['first_claim'],2,'.','')."\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
					}

					if ($isPhic) {
						if ($eclaims_accreditation_enable == '0') {
							$drAccre_click = "applyAllCoverage_2('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
						}else {
							if ($valid == '1') {
								$drAccre_click = "applyAllCoverage_2('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
							}else{
								$drAccre_click = "checkDrAccreditation2('".$id."',".$valid.",'".$errorMsg."');";
							}
						} 
					}else{
						$drAccre_click = "applyAllCoverage_2('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
					}
					
					$check2 = "<input id=\"apply2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
									class=\"segInput uncheckSecondCase\" type=\"checkbox\" ".(($row1['second_claim']!=0) ? 'checked="checked"' : '' )."
									onclick=\"$drAccre_click\">";
					// end by JOY

					if ($valid == '1') {
						$inputs2 = "<input id=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare2 clearInputSecondCase\"
								name=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation2(this)){calculateDetails2();}\" value=\"".number_format($row1['second_claim'],2,'.','')."\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
					}else{
						$inputs2 = "<input id=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare2 clearInputSecondCase\" disabled=\"disabled\"
								name=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation2(this)){calculateDetails2();}\" value=\"".number_format($row1['second_claim'],2,'.','')."\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
					}
					
					
					$dr_claim += $row1['dr_claim'];	
					
					$td .= "<td class=\"centerLeft\" width=\"1%\">$check1</td>
						    <td class=\"centerLeft\" width=\"5%\">$inputs1</td>".
						    "<td class=\"centerLeft\" width=\"1%\">$check2</td>
						    <td class=\"centerLeft\" width=\"5%\">$inputs2</td>";
					$footer .= "<th width=\"1%\" nowrap=\"nowrap\" ></th>
							<th class=\"rightAlign\" id=\"total1_".$row['hcare_id']."\">0.00</th>".
							"<th width=\"1%\" nowrap=\"nowrap\" ></th>
							<th class=\"rightAlign\" id=\"total2_".$row['hcare_id']."\">0.00</th>";

				}

			}else{
				// if ($AccreditedDrCheck || $global_DrCheck == 0) {
				// modified by JOY @ 02-21-2018
				if ($isPhic) {
					if ($eclaims_accreditation_enable=='0') {
						$drAccre_click = "applyAllCoverage_1('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
					}
					else{
						if ($valid == '1') {
							$drAccre_click = "applyAllCoverage_1('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
						}else{
							$drAccre_click = "checkDrAccreditation1('".$id."',".$valid.",'".$errorMsg."');";
						}
					}
				}else{
					$drAccre_click = "applyAllCoverage_1('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
				}
				
				$check1 = "<input id=\"apply1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
								class=\"segInput uncheckFirstCase\" type=\"checkbox\" onclick=\"$drAccre_click\">";
				// end by JOY
				
				if ($valid == '1') {
					$inputs1 = "<input id=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							name=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare1 clearInputFirstCase\" 
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation1(this)){calculateDetails1();}\" value=\"0.00\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
				}else{
					$inputs1 = "<input id=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							name=\"coverage1_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare1 clearInputFirstCase\" disabled=\"disabled\"
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation1(this)){calculateDetails1();}\" value=\"0.00\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
				}
				
				if ($isPhic) {
					if ($eclaims_accreditation_enable=='0') {
						$drAccre_click = "applyAllCoverage_2('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
					}
					else{
						if ($valid == '1') {
							$drAccre_click = "applyAllCoverage_2('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
						}else{
							$drAccre_click = "checkDrAccreditation2('".$id."',".$valid.",'".$errorMsg."');";
						}
					}
				}else{
					$drAccre_click = "applyAllCoverage_2('".$row['hcare_id']."','".$details->dr_nr."','".$details->area."');";
				}
				
				$check2 = "<input id=\"apply2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
								class=\"segInput uncheckSecondCase\" type=\"checkbox\" onclick=\"$drAccre_click\">";
				// end by JOY
				
				if ($valid == '1') {
					$inputs2 = "<input id=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							name=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare2 clearInputSecondCase\" 
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation2(this)){calculateDetails2();}\" value=\"0.00\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
				}else{
					$inputs2 = "<input id=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" 
							name=\"coverage2_".$row['hcare_id']."_".$details->dr_nr."_".$details->area."\" class=\"segInput numeric calc-excess calc-hcare2 clearInputSecondCase\" disabled=\"disabled\"
							   	type=\"text\" style=\"width:100%; text-align:right\" onfocus=\"this.select()\" 
							   	onchange=\"if(!checkNegation2(this)){calculateDetails2();}\" value=\"0.00\" 
							    itemcode=\"".$details->area."\" refsource=\"1\" hcareid=\"".$row['hcare_id']."\">" ;
				}

				$td .= "<td class=\"centerLeft\" width=\"1%\">$check1</td>
						<td class=\"centerLeft\" width=\"5%\">$inputs1</td>
						<td class=\"centerLeft\" width=\"1%\">$check2</td>
						<td class=\"centerLeft\" width=\"5%\">$inputs2</td>";

				$footer .= "<th width=\"1%\" nowrap=\"nowrap\" ></th>
							<th class=\"rightAlign\" id=\"total1_".$row['hcare_id']."\">0.00</th>".
							"<th width=\"1%\" nowrap=\"nowrap\" ></th>
							<th class=\"rightAlign\" id=\"total2_".$row['hcare_id']."\">0.00</th>";
			}
		}
	}

	$isphic = $objBilling->checkInsuranceRequest($details->enc);
	if( ($isphic->RecordCount() == 0) && ($objBilling->getAccomodationType() == '1') && (!$objBilling->isMedicoLegal($details->enc)) && ($details->accommodationType == 1)){
		$doc_discount = $details->charge;
	} elseif( ($isphic->RecordCount() == 0) && ($objBilling->getAccomodationType() == '1') && ($objBilling->isMedicoLegal($details->enc)) && ($discount) ){
		$doc_discount = $details->charge * $discount;
	}elseif ( ($isphic->RecordCount() == 0 ) && ($objBilling->getAccomodationType() == '2') && ($discount) ) {
	 	$doc_discount = $details->charge * $discount;
	} elseif ( ($isphic->RecordCount() != 0 ) && ($discount) ){
		$doc_discount = $details->charge * $discount;
	}else{
		$doc_discount = 0;
	}
	
	$objBilling->setPFCoverage($dr_claim);
	$objBilling->setPFDiscount($doc_discount);
	
	$tr_header = "<tr>
					<input type=\"hidden\"  id=\"adj_refno\" name=\"adj_refno\" value=\"".$refno."\"/>
					<th width=\"*\">Billable Areas</th>
	                <th width=\"12%\" nowrap=\"nowrap\">Total Charge</th>
	                <th width=\"12%\" nowrap=\"nowrap\">Discount</th>
	                $th
	                <th width=\"12%\" nowrap=\"nowrap\">Excess</th>
	              </tr>";

	$tr_details ="<tr>
					<input type=\"hidden\" class=\"calc-actual\" id=\"doc_charge_".$details->dr_nr."_".$details->area."\" name=\"doc_charge_".$details->dr_nr."_".$details->area."\" value=\"".$details->charge."\"/>
					<input type=\"hidden\" class=\"calc-discount calc-excess\" id=\"doc_discount_".$details->dr_nr."_".$details->area."\" name=\"doc_discount_".$details->dr_nr."_".$details->area."\" value=\"".$doc_discount."\" area=\"".$details->area."\"/>
                    <td style=\"font:bold 12px Arial;\">".strtoupper($details->name)."</td>
                    <td class=\"rightAlign\" style=\"font:bold 12px Arial; color:#008000\">".number_format($details->charge, 2, '.' ,',')."</td>
                    <td class=\"rightAlign\" style=\"font:bold 12px Arial; color:#008000\">".number_format($doc_discount, 2, '.' ,',')."</td>
                    $td
                    <td class=\"rightAlign calc-total-excess\" style=\"font:bold 12px Arial; \" id=\"total-excess-".$details->dr_nr."-".$details->area."\"></td>
	              </tr>";

	$tr_footer ="<tr>
                    <th style=\"font:bold 12px Arial;\">Totals</th>
                    <th class=\"rightAlign\" style=\"font:bold 12px Arial;\" id=\"total-charges\"></th>
                    <th class=\"rightAlign\" style=\"font:bold 12px Arial;\" id=\"total-discount\"></th>
                    $footer
                    <th class=\"rightAlign\" style=\"font:bold 12px Arial;\" id=\"total-excess\"></th>
	              </tr>";

	$objResponse->assign('coverage-header', "innerHTML", $tr_header);
	$objResponse->assign('coverage-footer', "innerHTML", $tr_footer);
	$objResponse->assign('pfDiscount','innerHTML',number_format($objBilling->getPFDiscount(),2,'.',','));
	$objResponse->assign('pfHC','innerHTML',number_format($objBilling->getPFCoverage(),2,'.',','));
	$objResponse->call('jsDoctorsCoverage','doc-coverage', $tr_details);
	$objResponse->call('uncheckCheckBox'); //Added by Christian 01-14-19
}


/**
* Created By Jarel
* Created On 04/12/2014
* Save Doctors Coverage
* @param array data
* @return mixed objResponse
*/
function saveDoctorCoverage($data,$saveFrom)
{
	global $db;
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	$unified_claim=0;
	$first_case=0;
	$second_case=0;
	foreach ($data as $key => $value) {
		$str = explode("_",$key);
		if($key == 'refno') $refno = $value;

		if(strtolower($str[1])=="charge"){
			$doc_nr =  $str[2];
			$charge = $value;
		}
		if(strtolower($str[0])=="coverage1"){
			$unified_claim=$value;
			$first_case=$value;
		}elseif(strtolower($str[0])=="coverage2"){
			$unified_claim+=$value;
			$second_case=$value;
			if($unified_claim!=0) {
				$insert .= "('".$refno."','".$str[1]."','".$doc_nr."','".$str[3]."','".$charge."','".$unified_claim."'),";
				$insert2 .= "('".$refno."','".$str[1]."','".$doc_nr."','".$str[3]."','".$unified_claim."','".$first_case."','".$second_case."'),";
			}
			// var_dump($insert);
		}
		// if(strtolower($str[0])=="coverage1"||strtolower($str[0])=="coverage2")var_dump($refno."-".$str[1]."-".$doc_nr."-".$str[3]."-".$charge."-".$value);
		
	}
	$insert = substr($insert, 0,-1);
	$insert2 = substr($insert2, 0,-1);
	// var_dump($insert);
	$objBilling->clearDoctorCoverage($refno);
	$objBilling->clearDoctorCoverageBreakdown($refno);
	// die();
	$ok = $objBilling->saveDoctorCoverage($insert);
	if($ok) $ok=$objBilling->saveDoctorCoverageBreakdown($insert2);

	if($saveFrom == 'doccvrg'){ #added by Christian 01-27-20
		if($ok) {
			$objResponse->alert("Successfully saved");
		}else{
			$objResponse->alert("Failed to saved!");
		}
	}

	return $objResponse;
}

//added by Nick 05-12-2014
function updateOpDate($op_date, $refno, $ops_code, $entry_no){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();

	$rs = $objBilling->updateOpDate(date('Y-m-d',strtotime($op_date)), $refno, $ops_code, $entry_no);
	if($rs){
		$objResponse->alert("Procedure date updated");
	}else{
		$objResponse->alert("Failed updating procedure date");
	}
	return $objResponse;
}

//added by Nick 05-15-2014
function updateMemCat($enc,$memCat){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	$rs = $objBilling->setMemberCategory($enc,$memCat);
	if($rs){
		$objResponse->alert('Member Category updated successfully');
		$objResponse->call('Dlg','dlgMemCat','close');
		$objResponse->call('populateBill');
		$objResponse->assign('optMemCat','innerHTML','');
	}else{
		$objResponse->alert('Failed updating Member Category');
		$objResponse->call('Dlg','dlgMemCat','close');
	}

	return $objResponse;
}

function showSoa($encounter,&$objResponse=false){
	$objResponse = (!$objResponse) ? new xajaxResponse() : $objResponse;
	$objBilling = new Billing();
	$data['encounter'] = $encounter;
	$bill_nr = $objBilling->getbillnr($data);
	if($bill_nr){
		$objResponse->call('populateBill');
		$objResponse->call('setBillNr',$bill_nr);
		$objResponse->call('printSoa');
	}else{
		$objResponse->alert('This bill has not been saved yet!');
	}
	return $objResponse;
}

/**
* @author By art
* Created On 11/22/14
* Save additional limit
* @param string encounter number , xlo,meds
* @return mixed objResponse
*/
function saveAdditionalLimit($enc,$xlo,$meds){
	$objResponse = new xajaxResponse();
	$objBilling = new Billing();
	$data = $arrayName = array('encounter_nr' => $enc, 
							   'amountmed' => $meds, 
							   'amountxlo' => $xlo, 
							   'create_id' => $_SESSION['sess_login_userid']);
	$save = $objBilling->saveAdditionalLimit($data);
	if ($save) {
		$objResponse->call('alertlimit',1);
	}else{
		$objResponse->call('alertlimit',0);
	}
	return $objResponse;
}
/**
* @author By art
* Created On 11/22/14
* Save additional limit
* @param string encounter number
* @return mixed objResponse
*/
function getCurrentLimit($enc, $bill_dte, $bill_frmdte, $deathdate){

	$objResponse = new xajaxResponse();
	//$objBilling = new Billing();

    $bill_frmdte = date('Y-m-d H:i:s',strtotime($bill_frmdte));
    $objBilling = setArguments($enc, $bill_dte, $bill_frmdte, $deathdate);

	$limit = $objBilling->getEncounterLimit($enc);
    $def_limit = $objBilling->getDefaultLimit();
	$additional = $objBilling->getTotalAdditionalLimit($enc);
	$billinfo = $objBilling->hasSavedBill($enc);

    define('__HCARE_ID__',18);
    $xlo_covered = $objBilling->getActualSrvCoverage(__HCARE_ID__);
    $meds_covered = $objBilling->getActualMedCoverage(__HCARE_ID__);

	if ($limit) {
        if($limit['xlo'] != false || $limit['meds'] != false) {
            $xlo = $limit['xlo'] + $additional['xlo'];
            $meds = $limit['meds'] + $additional['meds'];
        }
        else{
            $xlo = $def_limit['xlo'] + $additional['xlo'];
            $meds = $def_limit['meds'] + $additional['meds'];
        }
        $xloCov = $xlo - $xlo_covered;
        $medsCov = $meds - $meds_covered;
		$objResponse->call('assignCurrentLimit',number_format($xlo, 2, '.', ','),number_format($meds, 2, '.', ','), number_format($xloCov, 2, '.', ','), number_format($medsCov, 2, '.', ','));
	}
	$data = $objBilling->getAddedLimitDetails($enc);
	if ($data) {
		$string .='<table>
					<tr>
						<th width="200px" align="center">Date/Time</th>
						<th width="200px" align="center">Encoder</th>
						<th width="100px" align="center">Meds</th>
						<th width="100px" align="center">XLO</th>
					</tr>';
		foreach ($data as $row) {
			$string .='<tr><td align="center">'.$row['create_dt'].'</td><td align="center">'.$row['name'].'</td><td align="center">'.number_format($row['meds'], 2, '.', ',').'</td><td align="center">'.number_format($row['xlo'], 2, '.', ',').'</td></tr>';
		}
		$string .='</table>';
	}else{
		$string = 'No Added Amount';
	}

	$objResponse->call('hoverlimit',$string, $billinfo['is_final']);
	return $objResponse;
}
#end art

#added by janken 11/13/2014
function checkDateForEclaims(&$objResponse, &$objBilling, $bill_frmdte, $bill_dte, $enc, $phic_no){

	$phic = $objBilling->checkInsuranceRequest($enc);

	if($phic->RecordCount() != 0){

		if(((strtotime($bill_dte) >= strtotime("2014-11-21") && strtotime($bill_dte) <= strtotime("2014-12-05"))
			&& (strtotime($bill_frmdte) >= strtotime("2014-11-21") && strtotime($bill_frmdte) <= strtotime("2014-12-05")))
			&& is_numeric($phic_no)){
			$objResponse->call('checkEclaimsDate', true);
		}
		else
			$objResponse->call('checkEclaimsDate', false);
	}
	else{
		$objResponse->call('checkEclaimsDate', false);
	}

	return $objResponse;
}

//added by carriane 09/07/17
function clearCaseRateMultiplier($encounter_nr){
	global $db;

	$objResponse = new xajaxResponse();
	$sql = "UPDATE `seg_caserate_trail` SET `package_id` = NULL, `amount`=".$db->qstr(0).", `saved_multiplier`= NULL WHERE `encounter_nr` = ". $db->qstr($encounter_nr);

	$db->Execute($sql);
}

function hasSavedCaseRate($enc=''){
	global $db;
	$data = array();


	$sql = "SELECT * FROM `seg_caserate_trail` WHERE `encounter_nr` = ". $db->qstr($enc)." AND `package_id` IS NOT NULL AND `amount` != ".$db->qstr(0.0000)." AND `saved_multiplier` IS NOT NULL";

	$result = $db->Execute($sql);

	if($result->RecordCount()){
		while($row = $result->FetchRow()){
			if($row['rate_type'] == 1){
				$data['package_id_f'] = $row['package_id'];
				$data['multiplier_f'] = $row['saved_multiplier'];
			}elseif($row['rate_type'] == 2){
				$data['package_id_s'] = $row['package_id'];
				$data['multiplier_s'] = $row['saved_multiplier'];
			}
		}
	}
	return $data; 
}
//end carriane

function getCurrentDateTime(){
	$objResponse = new xajaxResponse();

	$cur = date('Y-m-d H:i:s');

	$objResponse->assign('current_date_time', 'value', $cur);

	return $objResponse;
}

//ADDED BY ROCHIE 7-12-17
function caserateModificationHistory($data)
{
	global $db;
	$objResponse = new xajaxResponse();

	$objBilling = new Billing();
	$hasbillnr = $objBilling->getbillnr($data);

    if($data['caserate']=='1')
    {
    	$history = "First Case rate multiplier updated by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n";
    }
    else{
    	$history = "Second Case rate multiplier updated by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n";
    }

    //updated by Carriane 07/25/17
   	$checkExist = "SELECT * FROM seg_caserate_trail WHERE `encounter_nr`=".$db->qstr($data['encounter'])." AND `rate_type`=".$db->qstr($data['caserate']). " AND `package_id`=". $data['package_id'];
    
    $checkifhasNull = "SELECT * FROM seg_caserate_trail WHERE `encounter_nr`=".$db->qstr($data['encounter'])." AND `rate_type`= ".$db->qstr($data['caserate'])." AND `package_id`= '' AND `saved_multiplier`= 0";

    $rateType = 0;

    if($data['caserate'] == 1){
    	$rateType = 2;
    	$alertCaserateError = "Second";
    }elseif($data['caserate'] == 2){
    	$rateType = 1;
    	$alertCaserateError = "First";
    }

    $checkSamePackage = "SELECT * FROM seg_caserate_trail sct WHERE sct.`encounter_nr`=".$db->qstr($data['encounter'])." AND sct.`rate_type`= ".$db->qstr($rateType)." AND sct.`package_id`=". $db->qstr($data['package_id']);

    $checkifhasSaved = "SELECT `package_id` FROM `seg_caserate_trail` WHERE `encounter_nr`=".$db->qstr($data['encounter'])." AND `rate_type`=".$data['caserate'];
    $db->BeginTrans();

    $hasSavedPackage =$db->GetOne($checkifhasSaved);
    $hasSamePackage = $db->GetOne($checkSamePackage);

    $result = $db->GetAll($checkExist);

    $resultNull = $db->GetAll($checkifhasNull);

    if($hasbillnr != ""){
	    $sql2 = "UPDATE seg_billing_encounter 
	                    SET history = CONCAT(history,". $db->qstr($history) .")
	                        WHERE bill_nr = ".$db->qstr($hasbillnr);
    }

    if($result || count($resultNull) != 0 || $hasSavedPackage != ''){
		if($hasbillnr != ""){
			$sql = "UPDATE seg_caserate_trail 
	                    SET modified_by = ".$db->qstr($_SESSION['sess_user_name']).",
	                    	date_modified = ".$db->qstr(date('Y-m-d H:i:s')).",
	                    	package_id = ".$db->qstr($data['package_id']).",
	                    	rate_type = ".$db->qstr($data['caserate']).",
	                    	amount = ".$db->qstr($data['amount']).",
	                    	saved_multiplier = ".$db->qstr($data['multiplier'])."
	                        WHERE encounter_nr = ".$db->qstr($data['encounter'])."
	                        AND rate_type = ".$db->qstr($data['caserate']);
		}else{
			$sql = "UPDATE seg_caserate_trail 
	                    SET modified_by = ".$db->qstr($_SESSION['sess_user_name']).",
	                    	date_modified = ".$db->qstr(date('Y-m-d H:i:s')).",
	                    	history = CONCAT(history, ". $db->qstr($history) ."),
	                    	package_id = ".$db->qstr($data['package_id']).",
	                    	rate_type = ".$db->qstr($data['caserate']).",
	                    	amount = ".$db->qstr($data['amount']).",
	                    	saved_multiplier = ".$db->qstr($data['multiplier'])."
	                        WHERE encounter_nr = ".$db->qstr($data['encounter'])."
	                        AND rate_type = ".$db->qstr($data['caserate']);
		}
	}else{
		if($hasbillnr != ''){
			$sql = "INSERT INTO seg_caserate_trail (`encounter_nr`, `modified_by`, `date_modified`, `package_id`, `rate_type`, `amount`, `saved_multiplier`)
    			VALUES (
    			".$db->qstr($data['encounter']).", 
    			".$db->qstr($_SESSION['sess_user_name']).", 
    			".$db->qstr(date('Y-m-d H:i:s')).",
    			".$db->qstr($data['package_id']).",
    			".$db->qstr($data['caserate']).",
    			".$db->qstr($data['amount']).",
    			".$db->qstr($data['multiplier']).")";
		}else{
	    	$sql = "INSERT INTO seg_caserate_trail (`encounter_nr`, `modified_by`, `date_modified`, `history`, `package_id`, `rate_type`, `amount`, `saved_multiplier`)
	    			VALUES (
	    			".$db->qstr($data['encounter']).", 
	    			".$db->qstr($_SESSION['sess_user_name']).", 
	    			".$db->qstr(date('Y-m-d H:i:s')).", 
	    			".$db->qstr($history).",
	    			".$db->qstr($data['package_id']).",
	    			".$db->qstr($data['caserate']).",
	    			".$db->qstr($data['amount']).",
	    			".$db->qstr($data['multiplier']).")";
		}
	}

	if($hasbillnr != ""){
		$success = $db->Execute($sql2);
	}

	if($hasSamePackage){
    	$success = $db->Execute("UPDATE `seg_caserate_trail` SET `package_id` = NULL, `amount`=".$db->qstr(0).", `saved_multiplier`= 0 WHERE `encounter_nr` = ".$db->qstr($data['encounter'])." AND `rate_type` = ".$rateType);
    }

    $success = $db->Execute($sql);

    if($success){
		$db->CommitTrans();
		
		$objResponse->alert('Successfully updated case rate multiplier.');
    }else{
    	$db->RollbackTrans();
    	$objResponse->alert('Updating case rate multiplier failed.');
    }

    return $objResponse;
}

//added by Nick 5-20-2015
function validateBill(Array $billData,xajaxResponse &$xajax){
	$validations = array(
		//'isBelow24Hours'
	);
	foreach($validations as $validation){
		$result = $validation($billData);
		if(is_array($result)){
			$xajax->call('billValidationPrompt',$result['title'],$result['message']);
			return false;
		}
	}
	return true;
}

function getWarnings(xajaxResponse &$xajax, array $billInfo)
{
	BillWarning::add(new NoAdmittingDiagnosis());
	BillWarning::add(new NoFinalDiagnosis());
	if(!$valid_covid){
		BillWarning::add(new PhilHealthBelow24Hours());
	}
	BillWarning::add(new PneumoniaAdultAgeBelow4Days());
	// BillWarning::add(new PneumoniaMinorAgeBelow4Days()); //removed by Kenneth 04-29-16
	BillWarning::add(new DiarrheaBelow3Days());
	BillWarning::add(new UtiBelow4Days());
	BillWarning::add(new HasOverlappingOfDates());
	BillWarning::add(new HasMissingDates());

	$warnings = BillWarning::getWarnings($billInfo['encounter_nr'], $billInfo);
	$valid_covid = BillWarning::getValidateCovid($billInfo['encounter_nr'], $billInfo);
	
	if($valid_covid){
		if (!empty($warnings)) {
			$xajax->call('billWarningPrompt', 'Warnings', implode('<br />', $warnings));
		}
	}else{
		if (!empty($warnings) || $billInfo['lessthanadm'] || $billInfo['is_phicexhausted']) {
			$xajax->call('billWarningPrompt', 'Warnings', implode('<br />', $warnings));
		}
	}
	
}

//function getWarnings(Billing $bill,xajaxResponse &$xajax, Array $info){
//	$validations = array(
//		'isBelow24Hours'
//	);
//	foreach($validations as $validation){
//		$result = $validation($bill,$info);
//		if(is_array($result)){
//			$xajax->call('billWarningPrompt',$result['title'],$result['message']);
//			return false;
//		}
//	}
//	return true;
//}
//
////added by Nick 5-20-2015
//function isBelow24Hours(Billing $bill, $info){
//	$hasInsurance = $bill->checkInsuranceRequest($bill->encounter_nr);
//	if($hasInsurance){
//		if($hasInsurance->RecordCount() > 0){
//			if(strtotime($info['billDate']) < strtotime('+1 day',strtotime($bill->getAdmissionDate()))){
//				return array('title'=>'WARNING','message'=>'Confinement is below 24 hours.<br>');
//			}
//		}
//	}
//
//	return true;
//}

$xajax->processRequest();