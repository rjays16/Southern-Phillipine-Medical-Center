<?php

function getLatestORNumber() {
	global $db;
	$objResponse = new xajaxResponse();
	$cc = new SegCashier();
	$userid = $_SESSION['sess_temp_userid'];
	$orno = $cc->getNextORNum($userid);
	if ($orno) {
		$objResponse->assign('orno','value',$orno);
		$objResponse->call('warn','OR Number is valid!!',1);
	}
	else {

		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('cashier_%');

		$sql = "SELECT (SUBSTRING(MAX(CONCAT(or_date,or_no)),20)) latest_or,
						MAX(or_no) max_or FROM seg_pay\n";
		if ($_SESSION['sess_temp_userid'])
			$sql .= "WHERE or_no REGEXP '^[0-9]+$' AND YEAR(or_date) = YEAR(NOW()) 
		             AND create_id=".$db->qstr($_SESSION['sess_temp_userid'])."\n";
		
		$orseries = $db->GetRow($sql);
		
		$sql_used = "SELECT or_no
					 FROM seg_pay WHERE (or_no BETWEEN ".$db->qstr($orseries['latest_or'])." AND ".$db->qstr($orseries['max_or']).") LIMIT 100";
		$orused = $db->GetAll($sql_used);
		
		for($a = 0; $a < count($orused); $a++)
			$orarray[] = $orused[$a]['or_no'];

		$latest_or = $orseries['latest_or'];
		$max_or = $orseries['max_or'];
		
		$or = $latest_or;

		if ($latest_or == $max_or){
			$or = $latest_or;
		}elseif($latest_or < $max_or){
			$used=0;
			$range_len = $max_or - $latest_or;

			if(strlen((string)$range_len) < $GLOBAL_CONFIG['cashier_or_number_digits']){
				$getrange = range($latest_or, $max_or);
				$getDiff = array_diff($getrange, $orarray);
				
				if(count($getDiff) > 0)
					$or = reset($getDiff)-1;
				else
					$used = count($orarray);
			}else{
				do{
					$temp_or = $or;
					$get_or = $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($temp_or));

					if($get_or > 0){
						$is_exists = 1;
						$or = $temp_or;
						$temp_or++;
					}else $is_exists = 0;
				}while(!$is_exists);

			}
			
			// for($i=$latest_or; $i <= $max_or; $i++){
			// 	if (in_array($i, $orarray)){
			// 		//do nothing
			// 		$used++;
			// 	}else{
			// 		$or = $i-1;
			// 		break;
			// 	}
			// }
			
		}

		if ($used == sizeof($orarray))
			$or = $max_or;

		$dbOk = true;

		if ($or) {
			//$or = (int) preg_replace('\D','',$or);
			if (is_numeric($or)) {
				$len = strlen($or);
				if ($len < $GLOBAL_CONFIG['cashier_or_number_digits']) {
					$len = $GLOBAL_CONFIG['cashier_or_number_digits'];
				}
				$new_or = str_pad((int)$or+1,$len,'0', STR_PAD_LEFT);

				if (($result = $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($new_or)))!==FALSE) {
					if (((int)$result) > 0) {
						# $objResponse->alert("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($new_or));
						$objResponse->assign('orno','value','');
						$objResponse->call('warn','OR #'.$new_or.' already in use!',0);
					}
					else {
						if ((($new_or-1)%$GLOBAL_CONFIG['cashier_or_batch_size'])==0) {
							$objResponse->assign('orno','value','');
							# $ $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($new_or)
							$objResponse->call('warn','Please re-enter OR Number for the new feed...',0);
						}
						else {
							$objResponse->assign('orno','value',$new_or);
							$objResponse->call('warn','OR Number is valid!',1);
						}
					}
				}
				else {
					$dbOk = false;
				}
			}
			else {
				$objResponse->call('warn','Invalid (non-numeric) OR Number!',0);
			}
		}
		else {
			$dbOk = false;
		}

		if (!$dbOk) {
			$objResponse->assign('orno','value','');
			$objResponse->call('warn','Cannot retrieve latest OR Number from database...',0);
		}
	}
	return $objResponse;
}

function checkORNoExists($orno, $theORNo = FALSE) {
	global $db;
	$objResponse = new xajaxResponse();

	$len = strlen($orno);
	if ($len < 7) $len = 7;
	$orno = str_pad($orno,$len,'0', STR_PAD_LEFT);
	$objResponse->assign('orno','value',$orno);

	if ($orno && ($orno===$theORNo))
		$objResponse->call('warn','OR Number is valid!',1);
	else {
		if (($result = $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($orno)))!==FALSE) {
			if (((int)$result) > 0) {
				$objResponse->call('warn','OR Number already in use!',0);
			}
			else $objResponse->call('warn','OR Number is valid!',1);
		}
	}
	return $objResponse;
}

function refreshReference($src, $ref) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();
	$resultInfo = $cClass->GetRequestInfo($ref,$src);
	if ($resultInfo) {

	}
}

function populateBillsList($page_num = 0, $max_rows = 10, $sort_obj = NULL, $args = NULL) {
    global $db;
    $objResponse = new xajaxResponse();

    if (!$filters)
        $filters = array();

    $offset = $page_num * $max_rows;
    $sortColumns = array('date', 'source', 'refno', 'name', 'total', 'grant');
    $sort = array();
    if (is_array($sort_obj)) {
        foreach ($sort_obj as $i => $v) {
            if ($col = ($sortColumns[$i] ? $sortColumns[$i] : FALSE)) {
                if ((int) $v < 0)
                    $sort[] = "$col DESC";
                elseif ((int) $v > 0)
                    $sort[] = "$col ASC";
            }
        }
    }

    if ($sort)
        $sort_sql = implode(',', $sort);
    else
        $sort_sql = 'source ASC';

    list($filters['OFFSET'], $filters['ROWS'], $filters['SORT']) = array($offset, $max_rows, $sort_sql);
    $classDialysis = new SegDialysis();
    $billsList = $classDialysis->getBills($args['query'], $args['pid'], $offset, $max_rows);
    if ($billsList !== false) {
        $i = 0;
        $prebillPayments = 0;
        $data = array();
        $found_rows = $billsList->RecordCount();
        while ($row = $billsList->fetchRow()) {
        	$prebillPayments = $classDialysis->getTotalPrebillPayments($row['encounter_nr']);

            $data[$i]['amount'] = (($row['amount'] + $row['hdf_amount']) - $row['subsidy_amount']);
            $data[$i]['bill_nr'] = $row['encounter_nr'];
            $data[$i]['bill_type'] = $row['bill_type'];
            $data[$i]['encounter_nr'] = $row['encounter_nr'];
            $data[$i]['request_date'] = $row['request_date'];
            $data[$i]['status'] = $row['not_paid'] > 0 ? '' : 'paid';
            $data[$i]['fullname'] = utf8_encode($row['fullname']);
            $data[$i++]['pid'] = $row['pid'];
        }

        $last_page = ceil($found_rows / $max_rows) - 1;
        if ($page_num > $last_page)
            $page_num = $last_page;


        $objResponse->contextAssign('currentPage', $page_num);
        $objResponse->contextAssign('lastPage', $last_page);
        $objResponse->contextAssign('maxRows', $max_rows);
        $objResponse->contextAssign('listSize', $found_rows);

        if ($found_rows) {
            $objResponse->contextAssign('dataSize', $found_rows);
            $objResponse->contextAssign('listData', $data);
        } else {
            $objResponse->contextAssign('dataSize', 0);
            $objResponse->contextAssign('listData', NULL);
        }
    } else {
        // error
        $objResponse->alert("An error has occurred! Please contact your system administrator!");
        $objResponse->contextAssign('dataSize', -1);
        $objResponse->contextAssign('listData', NULL);
    }

    $objResponse->script('this.fetchDone()');
    return $objResponse;
}

function getBillNrDetails($billNr, $pid) {
    $xajax = new xajaxResponse();
    $classDialysis = new SegDialysis();
    $billsList = $classDialysis->getBillDetails($billNr, $pid);
    if ($billsList) {
        $rows = $billsList->getRows();
    }
    echo json_encode($row);
}

function populateDetails($sDept, $sRefNo, $hide_load=0, $checked_requests, $sOR = NULL) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();
	$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept,$sOR);
	if ($resultInfo) $rRow = $resultInfo->FetchRow();

	if (is_numeric($rRow['grant_amount']))
		$limit = $rRow['grant_amount'];
	else
		$limit = -1;

//	if ($_SESSION['sess_temp_userid'] == 'medocs')
//	{
//		$objResponse->alert($sOR);
//	}

	$rsDetails = $cClass->GetRequestDetails($sRefNo, $sDept, $sOR);
	if ($rsDetails) {
		while ($rowDetails=$rsDetails->FetchRow()) {

			$item->src = strtolower($sDept);
			$item->ref = $sRefNo;
			$item->id = $rowDetails["item_no"];
			$item->name = $rowDetails['item_name'];
			$item->desc = $rowDetails['item_group'];
			$item->grpId = $rowDetails['type_id'];// added by Nick, 3/31/2014
			$item->qty = $rowDetails['quantity'];
			$item->ispaid = $rowDetails['is_paid'];
			$item->price = $rowDetails["price_cash"];
			$item->origprice = $rowDetails["price_cash_orig"];
			$item->limit = $limit;
			$item->checked = (strpos($checked_requests, $sDept.$sRefNo.$rowDetails["item_no"])!==FALSE || !$sOR);
			$item->showdel= 0;
			$item->calculate= 0;
			$item->doreplace= 1;
			$objResponse->call("addServiceToList",$item);
		}
	}
	$objResponse->call("calcSubTotal",$sDept, $sRefNo);
	if ($hide_load==1) {
		$objResponse->call("refreshTotal");
		$objResponse->call("doneLoading");
	}
	return $objResponse;

}
#edited by art 05/25/2014 added ic
function addPFOItem($src, $ref, $code, $qty, $amount) {
	$price = (float)$amount / (float)$qty;

	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();
	// For now ignore items from LD, PH & RD

	if (in_array(strtolower($src),array('other','pp','fb','ic','db'))) {
		$info = $cClass->GetPFOItemInfo($src, $ref, $code);
#		$objResponse->addAlert(print_r($info,TRUE));
#		return $objResponse;
		if ($info) {

			$item->src = strtolower($src);
                        $item->ref =  $item->src == 'db' ? '0000000000' :  $ref;
			$item->id = $code;
			$item->name = $info['name'];
			$item->desc = $info['desc'];
			$item->qty = (float)$qty;
			$item->ispaid = 0;
			$item->price = $price;
			$item->origprice = ($src=='IC'?$info['total']:$price); 
			$item->limit = -1;
			$item->checked = 1;
			$item->showdel= 1;
			$item->calculate= 1;
			$item->doreplace= 1;

#		$objResponse->alert(print_r($item,TRUE));
#		return $objResponse;
			$objResponse->call("addServiceToList",$item);
		}
		else {
			$objResponse->alert($cClass->sql);
		}
	}
	return $objResponse;
}

function addReference($sDept, $sRefNo, $checked_requests=NULL, $hide_load=0, $sOR=NULL) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();

	$sDept = strtolower($sDept);
	$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept,$sOR);
    if ($resultInfo) {
        $rRow = $resultInfo->FetchRow();
    }

    if (is_numeric($rRow['grant_amount'])) {
		$limit = $rRow['grant_amount'];
    } else {
		$limit = -1;
    }
    if (in_array(strtolower($sDept), array('ph', 'rd', 'ld', 'poc', 'misc', 'db','ob'))) {
        $dept_names = array('ph' => 'Pharmacy request', 'rd' => 'Radiology request', 'ld' => 'Laboratory request', 'poc' => 'POC request', 'fb' => 'Final billing', 'pp' => 'Partial payment', 'or' => 'Operating room', 'other' => 'Misc. services', 'misc' => 'Miscellaneous request', 'db' => 'Dialysis Request');
		$name = $dept_names[strtolower($sDept)] . " no. $sRefNo";

		$details->name = $name;
		$details->limit = $limit;
		$details->populate = 0;

		$objResponse->call("refreshRequest",$sDept,$sRefNo,$details);
	}

	$rsDetails = $cClass->GetRequestDetails($sRefNo, $sDept, $sOR);
	
	if ($rsDetails) {
		while ($rowDetails=$rsDetails->FetchRow()) {
			$item->src = $sDept;
			$item->ref = $sRefNo;
			$item->id = $rowDetails["item_no"];
			$item->name = $rowDetails['item_name'];
			$item->desc = $rowDetails['item_group'];
			$item->grpId = $rowDetails['type_id'];// added by Nick, 3/31/2014
			$item->qty = $rowDetails['quantity'];
			$item->ispaid = $rowDetails['is_paid'];
			$item->price = $rowDetails["price_cash"];
			$item->origprice = $rowDetails["price_cash_orig"];

			$itemOr = $cClass->GetRequestOrNumber($item->ref, $item->src, $item->id);
//			if ($_SESSION['sess_temp_userid'] == 'medocs')
//			{
//				$objResponse->alert($cClass->sql);
//			}

            if ($itemOr !== $sOR) {
				$item->flag = strtolower($rowDetails["request_flag"]);
            } else {
				$item->flag = '';
            }

			$creditcollectiongrants = $cClass->getRequestCreditGrants($sRefNo, $sDept, $rowDetails["item_no"]);

			$item->limit = $limit;
			$item->checked = (strpos($checked_requests, $sDept.$sRefNo.$rowDetails["item_no"])!==FALSE || !$sOR);
			$item->showdel= 0;
			$item->calculate= 0;
			$item->doreplace= 1;
			$item->creditgrant = $creditcollectiongrants[0]['total_amount'];
			$objResponse->call("addServiceToList",$item);
		}
	}
	$objResponse->call("calcSubTotal",$sDept, $sRefNo);
	if ($hide_load==1) {
		$objResponse->call("refreshTotal");
		$objResponse->call("doneLoading");
	}
	return $objResponse;
}

function populateReferences($sDeptArray, $sRefNoArray , $checked_requests) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();

	foreach ($sRefNoArray as $index=>$sRefNo) {
		$sDept = strtolower($sDeptArray[$index]);
		$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept);
		if ($resultInfo) $rRow = $resultInfo->FetchRow();

		if (is_numeric($rRow['grant_amount']))
			$limit = $rRow['grant_amount'];
		else
			$limit = -1;

        $dept_names = array('ph' => 'Pharmacy request', 'rd' => 'Radiology request', 'ld' => 'Laboratory request', 'fb' => 'Final billing', 'pp' => 'Partial payment', 'or' => 'Operating room', 'other' => 'Misc. services', 'rdu' => 'Dialysis Requests');
		$name = $dept_names[strtolower($sDept)] . " no. $sRefNo";

		$details->name = $name;
		$details->limit = $limit;
		$details->populate = TRUE;

		$objResponse->call("refreshRequest",$sDept,$sRefNo,$details);
	}
	return $objResponse;
}

function populateORParticulars($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$cc = new SegCashier;
	$nr = $args[0];  // get Fetcher parameters
	$offset = $page_num * $max_rows;
	$sortColumns = array('r.service_code','CAST(r.ref_source AS CHAR)','service','r.qty','r.amount_due'); // the data column to be sorted
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "service";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'service ASC'; // Default Sort option

	$result = $cc->GetPayDetails($nr, $offset, $max_rows, $sort_sql);
	if($result) {
		$found_rows = $cc->FoundRows();
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

				$DATA[$i]['code'] = $row['service_code'];
				$DATA[$i]['source'] = $row['ref_source'];
				$DATA[$i]['service'] = $row["service"];
				$DATA[$i]['price'] = number_format($row['amount_due']/$row['qty'],2);
				$DATA[$i]['quantity'] = $row['qty'];
				$DATA[$i]['total'] = number_format($row['amount_due'],2);
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
		$objResponse->alert($cc->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}
	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

require('./roots.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_cashier.php');
require($root_path . 'include/care_api_classes/dialysis/class_dialysis.php');
require($root_path.'include/care_api_classes/class_cashier_service.php');
require($root_path."modules/cashier/ajax/cashier-main.common.php");
$xajax->processRequest();