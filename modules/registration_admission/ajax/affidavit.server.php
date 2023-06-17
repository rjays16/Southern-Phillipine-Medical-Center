<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path."modules/registration_admission/ajax/affidavit.common.php");

	include_once($root_path.'include/care_api_classes/class_person.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');


	function getChildFullname($pid) {
		$dataObj = new stdClass;
		$objPerson = new Person($pid);
		$objResponse = new xajaxResponse();

		$dataObj->fullname = $objPerson->concatname($objPerson->FirstName(), $objPerson->MiddleName(),$objPerson->LastName(),1);
		$dataObj->info = $objPerson->getPersonInfo($pid);
		if($dataObj) {
			$objResponse->addScriptCall("personInfo", json_encode($dataObj, true));
		}

		return $objResponse;
	}

	$xajax->processRequests();
?>