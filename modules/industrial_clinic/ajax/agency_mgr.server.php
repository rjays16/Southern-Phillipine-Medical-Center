<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/agency_mgr.common.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_agency_mgr.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');


function saveAgency($data)
{
	global $db;
	$objResponse = new xajaxResponse();
	$amgr_obj = new SegAgencyManager();
	$data['status']='';
	$data['create_id'] = $_SESSION['sess_temp_userid'];
	$data['modify_id'] = $_SESSION['sess_temp_userid'];
	$data['create_dt'] = date('Y-m-d H:i:s');
	$data['modify_dt'] = date('Y-m-d H:i:s');
	$data['history'] = "Create ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]";
	$new_id = $amgr_obj->getNewId();
	$data['company_id'] = $new_id;
	$saveok = $amgr_obj->saveCompany($data);
	if($saveok!==FALSE) {
		$objResponse->call('outputResponse', 'New company successfully saved!');
	} else {
		$objResponse->call('outputResponse', 'ERROR(SAVE):'.$amgr_obj->getErrorMsg().'\nSQL:'.$amgr_obj->sql);
	}
	return $objResponse;
}

function updateAgency($data, $id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$amgr_obj = new SegAgencyManager();
	$saveok = $amgr_obj->updateCompany($data, $id);
	if($saveok!==FALSE) {
		$objResponse->call('outputResponse', 'Company details successfully updated!');
	} else {
		$objResponse->call('outputResponse', 'ERROR(UPDATE):'.$amgr_obj->getErrorMsg().'\nSQL:'.$amgr_obj->sql);
	}
	return $objResponse;
}

function deleteAgency($id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$amgr_obj = new SegAgencyManager();
	$saveok = $amgr_obj->deleteCompany($id);
	if($saveok!==FALSE) {
		$objResponse->call('outputResponse', 'Delete successful!');
	} else {
		$objResponse->call('outputResponse', 'ERROR(DELETE):'.$amgr_obj->getErrorMsg().'\nSQL:'.$amgr_obj->sql);
	}
	return $objResponse;
}

function deleteAgencyMember($pid, $agency_id)
{
	$objResponse = new xajaxResponse();
	$amgr_obj = new SegAgencyManager();
	$saveok = $amgr_obj->deleteEmployeeAssignment($pid, $agency_id);
	if($saveok!==FALSE) {
		$objResponse->call('refreshlist', 'Delete membership from agency successful!');
	} else {
		$objResponse->call('refreshlist', 'ERROR(DELETE):'.$amgr_obj->getErrorMsg().'\nSQL:'.$amgr_obj->sql);
	}
	return $objResponse;
}

function assignAgencyMember($data)
{
	global $db;
	$objResponse = new xajaxResponse();
	$amgr_obj = new SegAgencyManager();

	$amgr_obj->useCompEmployee();
	$is_deleted = $amgr_obj->isEmployeeDeleted($data['pid'],$data['company_id']);
	$is_existing = $amgr_obj->isEmployeeExisting($data['pid'],$data['company_id']);

	//$objResponse->alert("is_deleted?".$is_deleted." is_existing?".$is_existing);

	if($is_deleted==1 && $is_existing==1) {
		$updateok = $amgr_obj->updateEmployeeStatus($data['pid'],$data['company_id']);
		//$objResponse->alert("update?".$updateok);
		//$objResponse->alert("sqlupdate?".$amgr_obj->sql);
		if($updateok!==FALSE) {
			$objResponse->alert("Patient assignment to agency member successful!");
		}else {
			$objResponse->alert("Error. Please contact your administrator!");
		}
	}else if($is_deleted==0 && $is_existing==1) {
		$objResponse->alert("This patient is already a member of this agency.");
	}else if($is_deleted==0 && $is_existing==0) {
		$data['status'] = '';
		$data['modify_id'] = $_SESSION['sess_temp_userid'];
		$data['create_id'] = $_SESSION['sess_temp_userid'];
		$data['create_dt'] = date('Y-m-d H:i:s');
		$data['modify_dt'] = date('Y-m-d H:i:s');
		$saveok=$amgr_obj->assignCompanyEmployee($data);
		if($saveok!==FALSE) {
			$objResponse->alert("Patient assignment to agency member successful!");
		} else {
			$objResponse->alert("Error. Please contact your administrator!");
		}
	}
	return $objResponse;
}

function updateEmployeeData($data)
{
	global $db;
	$objResponse = new xajaxResponse();
	$amgr_obj = new SegAgencyManager();
	$saveok=$amgr_obj->updateEmployeeData($data);
	if($saveok!==FALSE) {
		$objResponse->call("outputResponse","Update employee data successful!");
	} else {
		$objResponse->call("outputResponse","This patient is already a member of this agency.");
	}
	return $objResponse;
}



#added code by angelo m.  08.24.2010
function populateNames($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$tr_obj=new SegICTransaction();




	$keyword = $args[0];
	$company_id=$args[1];
	if($page_num>0)
		$offset = ($page_num-1) * $max_rows;
	else
		$offset=0;



	$result=false;
	$result=$tr_obj->fetchPersonNames($company_id,$keyword,$offset,$max_rows);

	if($result) {
		$found_rows = $tr_obj->FoundRows();
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
				$DATA[$i]['pid'] = $row['pid'];
				$DATA[$i]['full_name'] = $row['full_name'];
				$DATA[$i]['sex'] = $row['sex'];
				$DATA[$i]['name_last'] = $row['name_last'];
				$DATA[$i]['name_first'] = $row['name_first'];
				$DATA[$i]['name_middle'] = $row['name_middle'];
				$DATA[$i]['date_birth'] = $row['date_birth'];

				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}


	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

function saveServicePriceToCompany($company_id, $item_code, $item_area, $new_price)
{
	global $db;
	$objResponse = new xajaxResponse();
	$mgrObj = new SegAgencyManager();

	$existing = $mgrObj->isExistsCompanyPrice($company_id, $item_code, $item_area);
	if($existing!==FALSE) {
		$saveok = $mgrObj->updateCompanyPrice($company_id, $item_code, $item_area, $new_price);
	} else {
		$saveok = $mgrObj->saveCompanyPrice($company_id, $item_code, $item_area, $new_price);
	}

	if($saveok!==FALSE) {
		$objResponse->alert("New item price saved successfully.");
		$objResponse->call("listCompanyServices");
	} else{
		$objResponse->alert("Error: ".$mgrObj->getErrorMsg()."\nLast Query: ".$mgrObj->getLastQuery());
	}

	return $objResponse;
}

function deleteServicePriceToCompany($company_id, $item_code, $item_area)
{
	global $db;
	$objResponse = new xajaxResponse();
	$mgrObj = new SegAgencyManager();

	$deleteok = $mgrObj->deleteCompanyPrice($company_id, $item_code, $item_area);
	if($deleteok!==FALSE) {
		$objResponse->alert("Item deleted successfully.");
		$objResponse->call("listCompanyServices");
	} else{
		$objResponse->alert("Error: ".$mgrObj->getErrorMsg()."\nLast Query: ".$mgrObj->getLastQuery());
	}

	return $objResponse;
}

function saveCompanyPackage($company_id, $package_name, $package_price, $items, $areas)
{
	global $db;
	$objResponse = new xajaxResponse();
	$mgrObj = new SegAgencyManager();

	$db->StartTrans();
	$package_id = $mgrObj->savePackage($package_name);
	if($package_id!==FALSE) {
		$bulk = array();
		foreach($items as $i=>$v)
		{
			$bulk[] = array(
				'service_code'=>$items[$i],
				'service_area'=>$areas[$i]
			);
		}
		//$objResponse->alert(print_r($bulk, true));
		$saveok = $mgrObj->savePackageItems($package_id, $bulk);
		if($saveok!==FALSE) {
			$saveok = $mgrObj->saveCompanyPackage($company_id, $package_id, $package_price);
			if($saveok!==FALSE) {
				$db->CompleteTrans();
				$objResponse->alert("Package successfully saved!");
				$objResponse->call("listCompanyPackages");
				$objResponse->assign("package_name", "value", "");
				$objResponse->assign("package_price", "value", "");
				$objResponse->call("clearList", "packagelist");
				$objResponse->call("append_empty_list");
			}
		}
	}

	if($saveok===FALSE || $package_id===FALSE) {
		$db->FailTrans();
		$objResponse->alert("Error: ".$mgrObj->getErrorMsg()."\n Last Query: ".$mgrObj->getLastQuery());
	}
	return $objResponse;
}

function deleteCompanyPackage($package_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$mgrObj = new SegAgencyManager();

	$deleteok = $mgrObj->deletePackage($package_id);
	if($deleteok!==FALSE) {
		 $objResponse->alert("Package successfully deleted!");
		 $objResponse->call("listCompanyPackages");
	} else {
		 $objResponse->alert("Error: ".$mgrObj->getErrorMsg()."\n Last Query: ".$mgrObj->getLastQuery());
	}

	return $objResponse;
}

function showCompanyPackageDetails($company_id, $package_id, $mode)
{
	global $db;
	$objResponse = new xajaxResponse();
	$mgrObj = new SegAgencyManager();

	$data = $mgrObj->getCompanyPackageDetails($package_id, $company_id);
	if($data!==FALSE) {
		$objResponse->assign("package_name", "value", $data["package_desc"]);
		$objResponse->assign("package_id", "value", $package_id);
		$objResponse->assign("package_price", "value", number_format($data["price"],2));

		$details = $mgrObj->listCompanyPackageItems($company_id, $package_id);
		//$objResponse->alert("Last Query: ".$mgrObj->getLastQuery());
		if($details===FALSE) {
			 $objResponse->alert("Error: ".$mgrObj->getErrorMsg()."\n Last Query: ".$mgrObj->getLastQuery());
			 return $objResponse;
		}

		$objResponse->call("clearList", "packagelist");
		if($mode=='edit') {
			$objResponse->assign("update_package", "style.display", "");
			$objResponse->assign("save_package", "style.display", "none");
		}
		while($row = $details->FetchRow())
		{
			$objResponse->call("addItemToList", $row["service_code"], $row["item_name"], $row["service_area"]);
		}
	}

	return $objResponse;
}

function editCompanyPackage($company_id, $package_id, $package_name, $package_price, $items, $areas)
{
	global $db;
	$objResponse = new xajaxResponse();
	$mgrObj = new SegAgencyManager();

	$db->StartTrans();
	$saveok = $mgrObj->updatePackageName($package_id, $package_name);
	if($saveok!==FALSE) {
		$bulk = array();
		foreach($items as $i=>$v)
		{
			$bulk[] = array(
				'service_code'=>$items[$i],
				'service_area'=>$areas[$i]
			);
		}
		$saveok = $mgrObj->clearPackageItems($package_id);
		if($saveok!==FALSE) {
			$saveok = $mgrObj->savePackageItems($package_id, $bulk);
			if($saveok!==FALSE) {
				$saveok = $mgrObj->updateCompanyPackageDetails($package_id, $company_id, $package_price);
				if($saveok!==FALSE) {
					$db->CompleteTrans();
					$objResponse->alert("Package successfully updated!");
					$objResponse->call("listCompanyPackages");
					$objResponse->assign("package_name", "value", "");
					$objResponse->assign("package_price", "value", "");
					$objResponse->call("clearList", "packagelist");
					$objResponse->call("append_empty_list");
					$objResponse->assign("update_package", "style.display", "none");
					$objResponse->assign("save_package", "style.display", "");
				}
			}
		}

	}

	if($saveok===FALSE) {
		$db->FailTrans();
		$objResponse->alert("Error: ".$mgrObj->getErrorMsg()."\n Last Query: ".$mgrObj->getLastQuery());
	}
	return $objResponse;
}

$xajax->processRequest();
?>
