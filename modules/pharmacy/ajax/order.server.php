<?php
#added by bryan on Sept 18,2008
#
function newChargeType(){
	global $db;
	$objResponse = new xajaxResponse();
	$option_all;

	$sql1="SELECT id,charge_name FROM seg_type_charge_pharma WHERE in_pharmacy = 1 AND for_walkin = 1 ORDER BY charge_name DESC";

	$result1=$db->Execute($sql1);
	while ($charged_name=$result1->FetchRow()){
		$option_all .= "<option value=\"".strtoupper($charged_name['id'])."\">".strtoupper($charged_name['charge_name'])."</option>\n";		
	}
	
    $objResponse->assign('charge_type', 'innerHTML', $option_all);
    return $objResponse;
}

function initDosageRouteFreq($id,$prod_class,$dosage,$route,$frequency){
	global $db;
	$objResponse = new xajaxResponse();
	$pc = new SegOrder();
	define('__MEDICINE_ITEM','M');

	$dosageOptions = "";	
	if($prod_class == __MEDICINE_ITEM){
		$dosageList = $pc->getDosageList();
	
		if($dosageList){
			while ($rs = $dosageList->FetchRow()) {

				$dosageOptions .= "<option value=\"".$rs['strength_disc']."\" " .($rs['strength_disc'] == $dosage ? "selected=\"selected\"" : "").">".$rs['strength_disc']."</option>\n";
			}
		}
	}
	
	if($dosageOptions != ""){
		$objResponse->assign('rowdosage'.$id, 'innerHTML', $dosageOptions);
	}
	if ($dosage) {
		$objResponse->assign('rowdosage'.$id, 'value', $dosage);
	}
	$routeOptions = "";
	if($prod_class == __MEDICINE_ITEM){
		$routeList = $pc->getRouteList();
	
		if($routeList){
			while ($rs = $routeList->FetchRow()) {
				$routeOptions .= "<option value=\"".$rs['route_disc']."\" " .($rs['route_disc'] == $route ? "selected=\"selected\"" : "").">".$rs['route_disc']."</option>\n";
			}
		}
	}
	if($routeOptions != ""){
		$objResponse->assign('rowroute'.$id, 'innerHTML', $routeOptions);
	}
	if ($route) {
		$objResponse->assign('rowroute'.$id, 'value', $route);
	}

	$frequencyOptions = "";	
	if($prod_class == __MEDICINE_ITEM){
		$frequencyList = $pc->getFrequencyList();
		
		if($frequencyList){
			while ($rs = $frequencyList->FetchRow()) {
				$frequencyOptions .= "<option value=\"".$rs['frequency_disc']."\" " .($rs['frequency_disc'] == $frequency ? "selected=\"selected\"" : "").">".$rs['frequency_disc']."</option>\n";
			}
		}
	}
	if($frequencyOptions != ""){
		$objResponse->assign('rowfrequency'.$id, 'innerHTML', $frequencyOptions);
	}

	if($prod_class != __MEDICINE_ITEM){
		$objResponse->call('disableDRF', $id);
	}
	

	if ($frequency) {
		$objResponse->assign('rowfrequency'.$id, 'value', $frequency);
	}
	
	return $objResponse;
}

function returnChargeType($is_phic = 0,$isfinal_bill = 0){
	global $db;
	$objResponse = new xajaxResponse();
	$option_all;

	$sql1="SELECT id,charge_name FROM seg_type_charge_pharma WHERE in_pharmacy = 1 ORDER BY ordering ASC";

	$result1=$db->Execute($sql1);
	while ($charged_name=$result1->FetchRow()){
		$option_all .= "<option value=\"".strtoupper($charged_name['id'])."\">".strtoupper($charged_name['charge_name'])."</option>\n";
	}

    $objResponse->assign('charge_type', 'innerHTML', $option_all);
    if($is_phic && !$isfinal_bill){
    	$objResponse->assign('charge_type', 'value', 'PHIC');
    }
    return $objResponse;
}

function reset_referenceno() {
	global $db;
	$objResponse = new xajaxResponse();

	$order_obj = new SegOrder("pharma");
	$lastnr = $order_obj->getLastNr(date("Y-m-d"));

	if ($lastnr)
		$objResponse->call("resetRefNo",$lastnr);
	else
		$objResponse->call("resetRefNo","Error!",1);
	return $objResponse;
}


//added by julius order 01-06-2017
function getpharmalocation($curlocation_order)
{
	// added by carriane 03/16/18
	define('IPBMIPD_enc', 13);
	define('IPBMOPD_enc', 14);
	// end carriane

	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder("pharma");
	$getinfo = $order_obj->getPersonMiniInfoFromEncounter($curlocation_order);
	if($getinfo){


			if ($getinfo["encounter_type"]==1){
			
				$erLoc = $order_obj->getERLocation($getinfo['er_location'], $getinfo['er_location_lobby']);
				if($erLoc['area_location'])
    				$location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
    			else
    				$location = "EMERGENCY ROOM";
			}elseif ($getinfo["encounter_type"]==2 || $getinfo["encounter_type"]==IPBMOPD_enc){
				$dept = $order_obj->getDeptAllInfo($getinfo['current_dept_nr']);
				$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}/*elseif (($row['enctype']==3)||($row['enctype']==4)){					
				$ward = $oclass->getWardInfo($row['current_ward']);
				$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room'];
			}*/


			elseif(($getinfo["encounter_type"]==4)|| ($getinfo["encounter_type"]==3) || ($getinfo["encounter_type"]==IPBMIPD_enc)){
				$bed = $getinfo['current_bed_nr'] ? " BED #: " . $getinfo['current_bed_nr'] : "";
				$dward = $order_obj->getWardInfo($getinfo['current_ward_nr']);
				$location = strtoupper(strtolower(stripslashes($dward['name'])))." Rm # :" .$getinfo['current_room_nr'] . $bed;
			}
			elseif ($getinfo["encounter_type"]==6){			
				$location = "INDUSTRIAL CLINIC";
			}else{
				#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				#$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				$location = 'WALK-IN';
			}
	}else{
		$location = 'WALK-IN';
	}		

			// var_dump($location);die;
	$objResponse->call('recieved_orderloc',$location);
	return $objResponse;
}
//end by julius order 01-06-2017

function get_charity_discounts( $nr ) {
	global $db;
	$objResponse = new xajaxResponse();
	$discount= new SegDiscount();
	$ergebnis=$discount->GetEncounterCharityGrants( $nr );
	$objResponse->call("clearCharityDiscounts");
	if ($ergebnis) {
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
			$objResponse->call("addCharityDiscount",$result["discountid"],$result["discount"]);
		}
	}
	$objResponse->call("cClick");
	$objResponse->call("refreshTotal()");
	return $objResponse;
}

function populate_order( $refno, $discountID, $disabled=NULL ) {
	global $db, $config, $root_path;
    require_once($root_path . 'include/care_api_classes/inventory/InventoryService.php');
	$objResponse = new xajaxResponse();

	$order_obj = new SegOrder("pharma");
	$result = $order_obj->getOrderItemsFullInfo($refno, $discountID);
	require_once($root_path . 'include/care_api_classes/class_pharma_product.php');
	$pc = new SegPharmaProduct();
    $new_order = new SegOrder();

	// $objResponse->alert($order_obj->sql);
	$rows = 0;
	if ($result) {
		$rows=$result->RecordCount();
		while ($row=$result->FetchRow()) {
			$obj->id = $row["bestellnum"];
			$obj->name = $row["artikelname"];
			$obj->desc= $row["description"];
			$obj->prcCash = $row["cshrpriceppk"];
			$obj->prcCharge = $row["chrgrpriceppk"];
			$obj->prcCashSC = $row["cashscprice"];
			$obj->prcChargeSC = $row["chargescprice"];
			$obj->prcDiscounted = $row["dprice"];
			$obj->isSocialized = $row["is_socialized"];
            $obj->is_fs = $row["is_fs"];
			$obj->forcePrice = $row["force_price"];
			$obj->qty = $row["quantity"];
			$obj->isConsigned = $row['is_consigned'];
			$obj->inv_refno = $row['inv_refno'];
			$obj->area = $row['pharma_area'];
			$obj->area_name = $row['area_name'];
			$obj->is_down_inv = $row['is_down_inv'];
			$obj->is_in_inventory = $row['is_in_inventory'];
			$obj->inv_api_key = $row['inv_api_key'];
			$obj->pid = $row['pid'];
			$obj->served_new = $row['SERVE'];
			$obj->inv_uid = $row['inv_uid'];
			$obj->prod_class = $row['prod_class'];
			$obj->dosage = $row['dosage'];
			$obj->frequency = $row['frequency'];
			$obj->route = $row['route'];
			$obj->NewCash = $row['cshrpriceppk'];
			$obj->NewCharge = $row['chrgrpriceppk'];
			$obj->price_cash_CASH = $row['price_cash'];
			$obj->price_charge_CHARGE = $row['price_charge'];
			$obj->request_flag = $row['request_flag'];

			if($row['serve_status'] == "S"){
				$obj->dispensed_qty = $row['dispensed_qty'];
				$disabled = TRUE;
			}else{
				$obj->dispensed_qty = 0;
			}


			
			define('__MEDICINE_ITEM','M');
			
			$dosageOptions = array();	
			if($obj->prod_class == __MEDICINE_ITEM){
				$dosageList = $new_order->getDosageList();
				$i = 0;
				if($dosageList){
					while ($rs = $dosageList->FetchRow()) {
						$dosageOptions[$i] = $rs['strength_disc'];
					
						$i++;
					}
				}
			}

			$obj->dosageOptions = $dosageOptions;

			$frequencyOptions = array();	
			if($obj->prod_class == __MEDICINE_ITEM){
				$frequencyList = $new_order->getFrequencyList();
				$i = 0;
				if($frequencyList){
					while ($rs = $frequencyList->FetchRow()) {
						$frequencyOptions[$i] = $rs['frequency_disc'];
					
						$i++;
					}
				}
			}
			$obj->frequencyOptions = $frequencyOptions;

			$obj->dosageOptions = $dosageOptions;

			$routeOptions = array();	
			if($obj->prod_class == __MEDICINE_ITEM){
				$routeList = $new_order->getRouteList();
				$i = 0;
				if($routeList){
					while ($rs = $routeList->FetchRow()) {
						$routeOptions[$i] = $rs['route_disc'];
					
						$i++;
					}
				}
			}
			$obj->routeOptions = $routeOptions;

			#commented by MARK 2016-10-30
			// $objResponse->alert($obj->area);
			// try {
	  //           $invService = new InventoryService();
	  //           $res = $invService->getItemInfo($obj->area, array("barcode" => $row['barcode'], 'reference_number' => $row['inv_refno']));

	  //           $obj->stock = empty($res['quantity'])?0:$res['quantity'];;
	  //       } catch (Exception $exc) {
	  //           // echo $exc->getTraceAsString();die;
	  //       }
                       $r = SegHis\modules\costCenter\models\PharmacyRequestItemSearch::search(array(
	                'referenceNo' => $refno,
	                'itemCode' => $row['bestellnum']
            	)
			);
                     
            $disabled = $r->isFinalBill ? true : false;
            
			#$objResponse->alert(print_r($obj,TRUE));
			$objResponse->call("appendOrder", NULL, $obj, $disabled,$mode="edit");
		}
		if (!$rows) $objResponse->call("appendOrder",NULL,NULL);
		$objResponse->call("refreshTotal");
	}
	else {
		if ($config['debug']) {
			$objResponse->alert("SQL error: ",$order_obj->sql);
			# $objResponse->alert($sql);
		}
		else {
			$objResponse->alert("A database error has occurred. Please contact your system administrator...");
		}
	}
	return $objResponse;
}


function add_item( $discountID, $items, $qty, $prc, $consigned ) {
	global $db;
	$dbtable='care_pharma_products_main';
	$prctable = 'seg_pharma_prices';
	$objResponse = new xajaxResponse();

	# Later: Put this in a Class
	if (!is_array($items)) $item = array($items);
	if (!is_array($qty)) $qty = array($qty);
	if (!is_array($prc)) $prc = array($prc);
	if (!is_array($consigned)) $prc = array($consigned);

	foreach ($items as $i=>$item) {

		$sql="SELECT a.*,\n".
			"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
			"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
			"IFNULL(b.ppriceppk,0) AS ppriceppk,\n".
			"IFNULL(b.chrgrpriceppk,0) AS chrgrpriceppk,\n".
			"IF(a.is_socialized,\n".
				"IFNULL((SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),b.cshrpriceppk),\n".
				"cshrpriceppk) AS dprice,\n".
			"IFNULL(b.cshrpriceppk,0) AS cshrpriceppk\n".
			"FROM care_pharma_products_main AS a\n".
			"LEFT JOIN seg_pharma_prices AS b ON a.bestellnum=b.bestellnum\n".
			"WHERE a.bestellnum = '$item'";
		$ergebnis=$db->Execute($sql);
		
#			$objResponse->alert(print_r($qty,true));
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			$objResponse->call("clearOrder",NULL);
			while($result=$ergebnis->FetchRow()) {
				$obj = (object) 'details';
				$obj->id = $result["bestellnum"];
				$obj->name = $result["artikelname"];
				$obj->desc= $result["description"];
				$obj->prcCash = $result["cshrpriceppk"];
				$obj->prcCharge = $result["chrgrpriceppk"];
				$obj->prcCashSC = $result["cashscprice"];
				$obj->prcChargeSC = $result["chargescprice"];
				$obj->prcDiscounted = $result["dprice"];
				$obj->isSocialized = $result["is_socialized"];
                $obj->isFs = $result["is_fs"];
				$obj->forcePrice = $prc[$i];
				$obj->qty = $qty[$i];
				$obj->isConsigned = $consigned[$i];
				$objResponse->call("appendOrder", NULL, $obj);
			}
		}
		else {
			if (defined('__DEBUG_MODE'))
				$objResponse->call("display",$sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");
		}
	}
	$objResponse->call("refreshTotal");
	return $objResponse;
}

function serveToInventory($ref) {
		global $db;
		$objResponse = new xajaxResponse();
		// $objResponse->alert($ref);
		$order_obj = new SegOrder("pharma");	
		$time_first = strtotime("now");
		$result = $order_obj->ReferenceAutoServer($ref);
		$time_second = strtotime("now");
		$time_diff = $time_second - $time_first;
		if($result=="LOCKED"){
			$objResponse->call("hideLoadingGui");
			$objResponse->call("lockedItemStatus",$ref);
		}
		else if (!empty($result)) {
			$objResponse->call("hideLoadingGui");
			$objResponse->call("updateItemStatus",$ref,$result[0],$result[1],$result[2],$time_diff);
			// foreach ($result as &$value) {
			//     $value = $value * 2;
			// }
		}
		else {		
			$sql_serve_pharma = "UPDATE seg_pharma_orders
                                        SET serve_status = 'S'
					    	WHERE refno = " . $db->qstr($ref);
			$db->Execute($sql_serve_pharma);
			$objResponse->call("hideLoadingGui");	
			$objResponse->call("endOfTrransmission");
			if (true) {
				$objResponse->call("display",$order_obj->sql);
				# $objResponse->alert($sql);
			}
			else {
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");
			}
		}
		return $objResponse;
	}

#added by bryan on Sept 18,2008
#updated by carriane 10/24/17; added IPBM encounter types
function populateOrderList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;
	global $db;

	define('IPBMIPD_enc', 13);
	define('IPBMOPD_enc', 14);

	$objResponse = new xajaxResponse();
	$oclass = new SegOrder();
	$selpayor = "";
	$seldate = "";
	$selarea = "";
	$selpayor = $args["selpayor"];
	$seldate = $args["seldate"];
	$selarea = $args["selarea"];

	$filters = array();
	if($selpayor!="") {
		switch(strtolower($args["selpayor"])) {
			case "name":
				$filters["NAME"] = $args["name"];
			break;
			case "pid":
				$filters["PID"] = $args["pid"];
			break;
			case "patient":
				$filters["PATIENT"] = $args["patientname"];
			break;
			case "inpatient":
				$filters["INPATIENT"] = $args["inpatientname"];
			break;
			case "case_no":
				$filters["CASE_NO"] = $args["case_no"]; // arco
		}
	}

	if($args["seldate"]!="") {
		switch(strtolower($args["seldate"])) {
			case "today":
				$search_title = "Today's Active Requests";
				$filters['DATETODAY'] = "";
			break;
			case "thisweek":
				$search_title = "This Week's Active Requests";
				$filters['DATETHISWEEK'] = "";
			break;
			case "thismonth":
				$search_title = "This Month's Active Requests";
				$filters['DATETHISMONTH'] = "";
			break;
			case "specificdate":
				$search_title = "Active Requests On " . date("F j, Y",strtotime($args["specificdate"]));
				$dDate = date("Y-m-d",strtotime($args["specificdate"]));
				$filters['DATE'] = $dDate;
			break;
			case "between":
				$search_title = "Active Requests From " . date("F j, Y",strtotime($args["between1"])) . " To " . date("F j, Y",strtotime($args["between2"]));
				$dDate1 = date("Y-m-d",strtotime($args["between1"]));
				$dDate2 = date("Y-m-d",strtotime($args["between2"]));
				$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
			break;
		}
	}

	if ($args["selarea"]!="") {
		$filters["AREA"] = $args["selarea"];
	}

	$offset = $page_num * $max_rows;
	$sortColumns = array('orderdate','refno','name_last','','is_urgent','area_full');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "orderdate";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'orderdate DESC';

	$result=$oclass->getActiveOrders($filters, $offset, $list_rows, $sort_sql);
//	if ($_SESSION['sess_temp_userid'] === 'admin') {
//		$objResponse->alert($oclass->sql);
//	}

	if($result) {
		$found_rows = $oclass->FoundRows();
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

				$urgency = $row["is_urgent"]?"Urgent":"Normal";
				$name = strtoupper($row["name"]);
				if (!$name) $name='<i styl	e="font-weight:normal">No name</i>';
				$class = (($count%2)==0)?"":"alt";

				//$items = explode("\n",$row["items"]);
				//$items = implode(", ",$items);
				//'stock_date','stock_nr','ward_name','items','encoder','area_full',

				$items_result = explode("\n",$row["items"]);
				$items = array();
				$served = 0;
				$is_paid = 0;
				$is_lingap = 0;
				$is_cmap = 0;
				$is_charity = 0;
				foreach ( $items_result as $j=>$v ) {
//          if (substr($v,0,1)=='S') $served=1;
//          $items[$j] = substr($v,2);
					$item_parse = explode("\t", $v);
					switch(strtolower($item_parse[0])) {
						case 'paid':
							$is_paid=1;
						break;
						case 'lingap':
							$is_lingap=1;
						break;
						case 'cmap':
							$is_cmap=1;
						break;
						case 'charity':
							$is_charity=1;
						break;
					}
					if (strtoupper($item_parse[1])=='S')
						$served=1;
					$items[$j] = $item_parse[2];
				}
				$items = implode(", ",$items);

				// determine FLAG
				$flag = '';
				if ($is_lingap)
					$flag = 'lingap';
				if ($is_cmap)
					$flag = 'cmap';
				if ($is_charity)
					$flag = 'charity';
				if ($is_paid)
					$flag = 'paid';


if ($row['enctype']==1){
			
				$erLoc = $oclass->getERLocation($row['erloc'], $row['erloclob']);
				if($erLoc['area_location'] != '')
    				$location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
    			else
    				$location = "EMERGENCY ROOM";
			}elseif ($row['enctype']==2||$row['enctype']==IPBMOPD_enc){
				$dept = $oclass->getDeptAllInfo($row['curdept']);
				$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}/*elseif (($row['enctype']==3)||($row['enctype']==4)){					
				$ward = $oclass->getWardInfo($row['current_ward']);
				$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room'];
			}*/
			elseif(($row['enctype']==4)|| ($row['enctype']==3)|| ($row['enctype']==IPBMIPD_enc)){

				$dward = $oclass->getWardInfo($row['current_ward']);
				$location = strtoupper(strtolower(stripslashes($dward['ward_id'])))." Rm # :" .$row['current_room'];
			}
			elseif ($row['enctype']==6){			
				$location = "Industrial clinic";
			}else{
				#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				#$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				$location = 'WALK-IN';
			}
		


				$DATA[$i]['orderdate'] = nl2br(date("Y-m-d\nh:ia",strtotime($row['orderdate'])));
				$DATA[$i]['refno'] = $row['refno'];
				$DATA[$i]['name'] = $name;
				$DATA[$i]['items'] = $items;
				$DATA[$i]['is_cash'] = $row['is_cash'];
				$DATA[$i]['urgency'] = $urgency;
				$DATA[$i]['area_full'] = $row['area_full'];
				$DATA[$i]['current_room'] = $row['current_room'];
				$DATA[$i]['current_ward'] = $location;
				$DATA[$i]['wardname'] = strtoupper(strtolower(stripcslashes($row['wardname'])));
				$DATA[$i]['paid'] = $is_paid;
				$DATA[$i]['lingap'] = $is_lingap;
				$DATA[$i]['cmap'] = $is_cmap;
				$DATA[$i]['charity'] = $is_charity;
				$DATA[$i]['flag'] = $flag;
				$DATA[$i]['served'] = $served;
				$DATA[$i]['FLAG'] = 1;
				$i++;

			} //end while
			if (!$_REQUEST['selpayor']) $_REQUEST['selpayor']='name';

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);

			if ($config['debug'])
				$objResponse->alert("No Records To Display");
		}

	} else {
		// error
			if ($config['debug'])
				$objResponse->alert('SQL error: '.$oclass->sql);
			else {
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");
			}

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

function deleteOrder($refno) {
	global $db, $root_path;
	$objResponse = new xajaxResponse();
	$oclass = new SegOrder();
	if ($oclass->deleteOrder($refno)) {
#    if (true) {
		$objResponse->call('prepareDelete',$refno);
		try {
            require_once($root_path . 'include/care_api_classes/emr/services/PharmacyEmrService.php');
            $pharmaService = new PharmacyEmrService();
            #add new argument to detect if to update patient demographic or not
            $pharmaService->deletePharmaRequest($refno);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }

	}
	else {
		$objResponse->call('lateAlert',$db->ErrorMsg(), 1000);
	}
	return $objResponse;
}

function updatePHICCoverage($enc_nr) {
	$objResponse = new xajaxResponse();
	if ($enc_nr) {
		$bill_date = strftime("%Y-%m-%d %H:%M:%S");
		$bc = new Billing($enc_nr, $bill_date);
		$bc->getConfinementType();
		#$bc->getMedicineBenefits();
		#$meds = $bc->getMedConfineBenefits();
		$bc->getConfineBenefits('MS','M');
		$confine = $bc->med_confine_benefits;

		$amount = 0;
		foreach ($confine as $v) {
			if ($v->hcare_id == 18) {
				$amount = $v->hcare_amountlimit;
			}
		}

		$objResponse->assign('phic_cov','innerHTML', number_format($amount,2));
		//$objResponse->script('alert($("phic_cov").innerHTML)');
	}
	else
		$objResponse->assign('phic_cov','innerHTML', 'None');
	return $objResponse;
}

function updateCoverage($enc_nr, $charge_type='PHIC',$search=0) {
	global $db;

	$objResponse = new xajaxResponse();
	$amount = 0;
	if($charge_type==''||$search==0) $charge_type='PHIC';

	// $objResponse->alert($enc_nr."---".$charge_type."---".$search);
	//$objResponse->alert($enc_nr);
	if ($enc_nr) {
		if ($charge_type=='PHIC') {
			$bill_date = strftime("%Y-%m-%d %H:%M:%S");
			$bc = new Billing($enc_nr, $bill_date);
			$bc->checkExistingInsuranceCreditCollectionNBB();
			$bc->getConfinementType();
			$amount = 0;

			define('__HCARE_ID__',18);

			$total_coverage = $bc->getActualMedCoverage(__HCARE_ID__);
			
			if ($bc->nbbInsurance && !$bc->isPayward($enc_nr)) {
				$bc->confinetype_id = $bc->_NBBconf;
			}
			
			$bc->getConfineBenefits('MS','M', 0, true);
            $confine = $bc->med_confine_benefits;
            $amount = 0;
            foreach ($confine as $v) {
                if ($v->hcare_id == __HCARE_ID__) {
                    $total_benefits = $v->hcare_amountlimit;
                }
            }
            $additional = $db->GetOne("SELECT SUM(amountmed) FROM seg_additional_limit WHERE is_deleted IS NULL AND encounter_nr=".$db->qstr($enc_nr)); #added by art 11/20/14
//            if (in_array($_SESSION['sess_temp_userid'], array('admin', 'medocs'))) {
//                $objResponse->alert( print_r($total_coverage, TRUE) );
//            }
            $phic_coverage = (float)$additional + (float)$total_benefits - (float)$total_coverage;
			// if($phic_coverage<0) $phic_coverage=0.00;
			$objResponse->assign('coverage','value', $phic_coverage);
	     	$objResponse->assign('cov','value', $phic_coverage);
	     	$objResponse->assign('phic_cov','value', $phic_coverage);
	     	$objResponse->assign('phic_coverage','value', $phic_coverage);
		}else {
			$objResponse->assign('cov_type','innerHTML', '');
			$objResponse->assign('cov_amount','innerHTML', '');
			$objResponse->assign('coverage','value', -1);
			$objResponse->assign('cov','value', -1);
			$objResponse->assign('phic_cov','value', -1);
		}
	}
	else
		$objResponse->assign('cov_amount','innerHTML', '');
	$objResponse->call('removeTplChargeType',$search);
	return $objResponse;
}


/*added BY MARK 10-2-16*/
function insertPharmaArea($sql_statement,$area_code,$area_name,$allow_socialized,$lockflag,$show_area,$inv_area_code,$inv_api_key){
	global $db;
	$objResponse = new xajaxResponse();
    $inv_obj=new Inventory;
   	$retail_ID = $inv_obj->getRcodefromArea($inv_area_code);
   	$APICODE = $inv_obj->getAPIfromArea($area_code,$inv_api_key);
   
   	if ($retail_ID == $inv_area_code &&  $retail_ID != "" &&  $sql_statement != "UPDATE" && $sql_statement !="UNDO" && $sql_statement !="DELETE") {
   		  $objResponse->call("afterSave","ERRORARCODE");
   	}elseif ($APICODE == $inv_api_key && ($sql_statement == "UPDATE" || $sql_statement =="INSERT INTO" )) {
   		 $objResponse->call("afterSave","ERRORAPICODE");
   	}
   	else{
   		 $savePharma = $inv_obj->savePharmaAreas($sql_statement,$area_code,$area_name,$allow_socialized,$lockflag,$show_area,$inv_area_code,$inv_api_key);
	   	if ($savePharma) {
	   		if ($sql_statement=="UPDATE") 
	   			$objResponse->call("afterSave","Inventory Area: ".$area_code." Sucessfully Updated");
	   		if ($sql_statement=="INSERT INTO") 
	   			$objResponse->call("afterSave","Inventory Area: ".$area_code." Sucessfully Save");
	   		if ($sql_statement=="DELETE") 
	   			$objResponse->call("afterSave","Deleted");
	   		if ($sql_statement=="UNDO") 
	   			$objResponse->call("afterSave","Area: ".$area_code." Successfully Retreived.");
	  
	   	}else{
	   			$objResponse->call("afterSave","ERROR");

	   	}
   }
   	return $objResponse;
}
/**/

function checkifhasphic($enc_nr){
	global $db;

	$objResponse = new xajaxResponse();
	$value = 0;

	$ifhasPHIC = $db->GetOne("SELECT `encounter_nr` FROM `seg_encounter_insurance` WHERE `encounter_nr`=".$db->qstr($enc_nr));

	if($ifhasPHIC)
		$value = 1;
	
	$objResponse->assign('hasPHIC','value', $value);

	return $objResponse;
}
// Added by Matsuu 04102018
function saveAreasbyUserDefault($area_code){
	global $db;
	$objResponse = new xajaxResponse();
		 $fldarray = array('personell_nr' => $db->qstr($_SESSION['sess_login_personell_nr']),
	                        'area_code' => $db->qstr($area_code),
	                        'default_area' => $db->qstr('1'));
		 $save_areas = $db->Replace('pharma_default_areas', $fldarray, array('personell_nr'));
		 if ($save_areas) {
		 	$db->CommitTrans();
		 	// $objResponse->alert("Default Area has been changed.");
		 	$objResponse->call('getlocation');
		 }
	return $objResponse;
}

function getDrugDescription($drug_code){
    global $db;
    $objResponse = new xajaxResponse();
    $drug_desc = $db->GetOne("SELECT spm.description FROM `seg_phil_medicine` AS spm WHERE spm.drug_code = ".$db->qstr($drug_code));

    $objResponse->assign('drug_description','value', $drug_desc);
    $objResponse->call('fetchDrugDesc');
    return $objResponse;
}
// Ended here...

function getExpiryDate($encounter_nr=''){
    $objResponse = new xajaxResponse();
    $SocialService =  New SocialService();
    $SocialInfo= $SocialService->getLatestClassification($encounter_nr);

    if(!empty($SocialInfo['pwd_expiry'])){
        $pwd_expiry_dt =  strtotime($SocialInfo['pwd_expiry']);
        $now = strtotime(date("Y-m-d"));
        if ($pwd_expiry_dt >= $now) {
           $discountid = $SocialInfo['discountid'];
            $discount = $SocialInfo['discount'];
        }
        $objResponse->assign('sw-class','innerHTML', $discountid);
        $objResponse->assign('discountid','value', $discountid);
    }

    return $objResponse;

}
// Ended here...

function getExcludedAreas($area=''){
	global $db;

	$objResponse = new xajaxResponse();
	$exlude_if_phic_areas = PharmacyArea::model()->findAllByAttributes(array('exclude_if_phic'=> 1));
    $areas = array();
	foreach ($exlude_if_phic_areas as $key => $value) {
	    $areas[] = $value['area_code'];
	}

	$exclude_area = 0;
	if (in_array($area, $areas)) {
		$exclude_area = 1;
	}
	$objResponse->assign('exclude_area','value', $exclude_area);
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

            $total_coverage = $bc->getActualMedCoverage(__HCARE_ID__);
            $bc->getConfineBenefits('MS','M', 0, true);
            $confine = $bc->med_confine_benefits;
            $amount = 0;
            foreach ($confine as $v) {
                if ($v->hcare_id == __HCARE_ID__) {
                    $total_benefits = $v->hcare_amountlimit;
                }
            }
            $additional = $db->GetOne("SELECT SUM(amountmed) FROM seg_additional_limit WHERE is_deleted IS NULL AND encounter_nr=".$db->qstr($enc_nr));
            $phic_coverage = (float)$additional + (float)$total_benefits - (float)$total_coverage;
            // if($phic_coverage<0) $phic_coverage=0.00;
            $objResponse->assign('cov','value', $phic_coverage);
            $objResponse->assign('phic_coverage','value', $phic_coverage);
            $objResponse->call('refreshTotal');
        }
    }
   return $objResponse;
}



require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_discount.php');
require($root_path.'include/care_api_classes/class_order.php');
require($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path."include/care_api_classes/billing/class_billing.php");
require_once($root_path."include/care_api_classes/sponsor/class_lingap_patient.php");
require_once($root_path."include/care_api_classes/sponsor/class_cmap_patient.php");
require_once($root_path."include/care_api_classes/class_inventory.php");
require_once($root_path.'modules/pharmacy/ajax/order.common.php');
require_once($root_path."frontend/bootstrap.php");
$xajax->processRequest();
