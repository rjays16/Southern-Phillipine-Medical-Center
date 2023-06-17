<?php

require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/company_search.common.php');
//require_once($root_path.'include/care_api_classes/class_address.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');


function populateCompany($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$company=new SegICTransaction();

	$keyword = $args[0];
	$pid = $args[1];
//	$objResponse->alert($keyword.$pid);
	$result=$company->fetchCompanyNames($keyword,$pid);
	if($result->RecordCount()==0)
		$result=$company->fetchAllCompanyNames($keyword);

	#$objResponse->alert($company->sql);

	if($result) {
		$found_rows = $company->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$result->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $result->FetchRow()) {

				$DATA[$i]['company_id'] = $row['company_id'];
				$DATA[$i]['company_name'] = $row['company_name'];
				$DATA[$i]['company_address'] = $row['address'];
				$DATA[$i]['employee_id'] = $row['employee_id'];
				$DATA[$i]['position'] = $row['position'];
				$DATA[$i]['job_status'] = $row['job_status'];

				//$DATA[$i]['full'] = $row["full"];

				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end while
//			$objResponse->alert(print_r($DATA,true));
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			//$objResponse->alert(print_r($DATA,true));
		}
		else {


			$objResponse->alert("The patient is not yet registered to agency..");
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		$objResponse->alert("The patient is not yet registered to agency..");
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}



	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

$xajax->processRequest();
