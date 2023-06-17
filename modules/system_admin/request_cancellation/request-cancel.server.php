<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/system_admin/request_cancellation/request-cancel.common.php');
require_once($root_path.'include/care_api_classes/class_request_cancellation.php');

function cancelRequestFlag($area, $refno, $item_code, $reason)
{
	global $db;
	$objResponse = new xajaxResponse();
	$reqObj = new SegRequestCancel();

	switch(strtolower($area))
	{
		case 'ld':
			$cancelok = $reqObj->cancelLabRequestFlag($refno, $item_code, $reason);
			break;
		case 'rd':
			$cancelok = $reqObj->cancelRadioRequestFlag($refno, $item_code, $reason);
			break;
		case 'ph':
			$cancelok = $reqObj->cancelPharmaRequestFlag($refno, $item_code, $reason);
			break;
		case 'ot':
			$cancelok = $reqObj->cancelMiscRequestFlag($refno, $item_code, $reason);
			break;
	}

	if($cancelok!==FALSE) {
		$objResponse->alert("Request charge type cancelled.");
		$objResponse->call("listRequests");
	} else {
		$objResponse->alert("Error:".$reqObj->getErrorMsg()." Last Query:".$reqObj->getLastQuery());
	}

	return $objResponse;
}

function cancelStatus($area, $refno, $item_code, $reason)
{
	global $db;
	$objResponse = new xajaxResponse();
	$reqObj = new SegRequestCancel();

	switch(strtolower($area))
	{
		case 'ld':
			$cancelok = $reqObj->cancelLabRequestStatus($refno, $item_code, $reason);
			break;
		case 'rd':
			$cancelok = $reqObj->cancelRadioRequestStatus($refno, $item_code, $reason);
			break;
		case 'ph':
			$cancelok = $reqObj->cancelPharmaRequestStatus($refno, $item_code, $reason);
			break;
	}

	if($cancelok!==FALSE) {
		$objResponse->alert("Request status cancelled.");
		$objResponse->call("listRequests");
	} else {
		$objResponse->alert("Error:".$reqObj->getErrorMsg()." Last Query:".$reqObj->getLastQuery());
	}

	return $objResponse;
}

function deleteRequestItem($area, $refno, $item_code, $reason)
{
	global $db;
	$objResponse = new xajaxResponse();
	$reqObj = new SegRequestCancel();

	switch(strtolower($area))
	{
		case 'ld':
			$deleteok = $reqObj->deleteLabRequestItem($refno, $item_code, $reason);
			$selectok = $reqObj->check_BatchnumEmpty($refno); // checks the batch number if empty it will automaticaly disable the batch number
		break;
		case 'rd':
			$deleteok = $reqObj->deleteRadioRequestItem($refno, $item_code, $reason);
			$deleterad = $reqObj->deleteRadio($refno);
			break;
		case 'ph':
			$deleteok = $reqObj->deletePharmaRequestItem($refno, $item_code);
			$deletepharma = $reqObj->deletePharma($refno);
			break;
		case 'ot':
			$deleteok = $reqObj->deleteMiscRequestItem($refno, $item_code);
			break;
	}

	if($deleteok!==FALSE) {
		$objResponse->alert("Request item deleted.");
		$objResponse->call("listRequests");
	} else {
		$objResponse->alert("Error:".$reqObj->getErrorMsg()." Last Query:".$reqObj->getLastQuery());
	}

	return $objResponse;
}

$xajax->processRequest();
?>
