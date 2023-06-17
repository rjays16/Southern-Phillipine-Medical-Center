<?php
#added by bryan on Sept 18,2008
#

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
		$objResponse->call("refreshDiscount()");
		return $objResponse;
	}

	function populate_order( $refno, $discountID, $disabled=NULL ) {
		global $db;
		$objResponse = new xajaxResponse();
	
		$order_obj = new SegOrder("pharma");		
		$result = $order_obj->getOrderItemsFullInfo($refno, $discountID);
		$objResponse->call("clearOrder",NULL);
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
				$obj->forcePrice = $row["force_price"];
				$obj->qty = $row["quantity"];
				$obj->isConsigned = $row['is_consigned'];
				#$objResponse->alert(print_r($obj,TRUE));
				$objResponse->call("appendOrder", NULL, $obj, $disabled);
			}
			if (!$rows) $objResponse->call("appendOrder",NULL,NULL);
			$objResponse->call("refreshDiscount");
		}
		else {			
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
		return $objResponse;
	}
	
	#added by bryan on Sept 18,2008
	function populateOrderList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();	
	$oclass = new SegOrder("");
	$selpayee = "";
	$seldate = "";
	$selarea = "";
	$selpayee = $args["selpayee"];
	$seldate = $args["seldate"];
	$selarea = $args["selarea"];
	//$area = $args[0];
	
	if($selpayee!="") {
	switch(strtolower($args["selpayee"])) {
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
	
//	$filters["ENCODER"] = $_SESSION['sess_temp_userid'];
//	$filters["THISSHIFT"] = "";

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
	
	$result=$oclass->getActiveOrders($filters, /*$list_rows * $current_page*/$offset, $list_rows, $sort_sql);
	
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
			if ($row["pid"]) 
				$name = $row["name_last"].", ".$row["name_first"]." ".$row["name_middle"];
			else
					$name = $row["ordername"];
			if (!$name) $name='<i style="font-weight:normal">No name</i>';
			$class = (($count%2)==0)?"":"wardlistrow2";
			$items = explode("\n",$row["items"]);
			$items = implode(", ",$items);
	    //'stock_date','stock_nr','ward_name','items','encoder','area_full',		
   
			$DATA[$i]['orderdate'] = $row['orderdate'];
			$DATA[$i]['refno'] = $row['refno'];
			$DATA[$i]['name'] = $name;
			$DATA[$i]['items'] = $items;
			$DATA[$i]['urgency'] = $urgency;
			$DATA[$i]['area_full'] = $row['area_full'];
			$DATA[$i]['FLAG'] = 1;
			$i++;
			} //end while
			if (!$_REQUEST['selpayee']) $_REQUEST['selpayee']='name';
			
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}
		
	} else {
		// error
		$objResponse->alert($objSS->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}	
	$objResponse->script('this.fetchDone()');
	return $objResponse;
}
	

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path.'include/care_api_classes/class_order.php');
	require_once($root_path.'modules/pharmacy/ajax/order.common.php');
	$xajax->processRequest();
?>