<?php
	function populateProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$mixed_misc=false,$mode='new', $encounter_nr = false,$barcode='') {
		global $db, $config, $root_path,$totalF;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$invService = new InventoryService();
		$objResponse = new xajaxResponse();
		$pc = new SegPharmaProduct();
		$pc2 = new SegPharmaProduct();
        $new_order = new SegOrder();
		$invServiceNew =new InventoryServiceNew();
		$maxRows = 10;
		$offset = $page * $maxRows;
		define('__MEDICINE_ITEM', 'M');
		$dosageOptions = "";
		$routeOptions = "";
		$frequencyOptions = "";
		
		//check DAI connection
		$hosp_obj = new Hospital_Admin();
		if ($row = $hosp_obj->getAllHospitalInfo()){
	    	$INV_address = $row['INV_address'];
	      	$INV_directory = $row['INV_directory'];
	    }

	    try{
			$offline = 0;
			$fp = @fsockopen($INV_address, 80, $errno, $errstr, 0.5);
			if (!$fp) {
			  	$offline = 0;
			}else{
			 	$offline = 1;
			 	$inv_url = "http://".$INV_address.'/'.$INV_directory;
			 	$invsite = @file_get_contents($inv_url,false, $context, 0, 1000);
			    if (empty($invsite)) $offline = 0;
			}
		}catch (Exception $e){
			$offline = 0;
		}

		$objResponse->call("updateDAIStatus",$offline);
		#----------------------------------------------------------
		$ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows, '', $barcode);
		// $objResponse->alert($pc->sql);
		$bestellnumCheck =array();
		$classification_C = array("C1","C2","C3","C4");/*temporary data for desired output,*/
		if ($ergebnis) {
			$total = $pc->FoundRows();
			$totalF =$total;
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
			$rows=$ergebnis->RecordCount();

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$pharma_areaAPI = $pc->getAPIfromPharmaArea($area);
			$pharma_areaName = $pc->getAPIfByName($area);
			$getAllow_socialized = $pc->getAllow_socialized($area);
			
			$objResponse->call("clearList","product-list");
			while($result=$ergebnis->FetchRow()) {
				$details->id = $result["bestellnum"];
				$bestellnumCheck[] = $result["bestellnum"];
				if ($result["remarks"]){
					$details->name = $result["artikelname"] ." (" . $result[ "remarks"] . ") " . $result["unit"];
				}else{
					$details->name = (trim($result["artikelname"])) . " " . $result["unit"];
				}
				$priceKEY = $pc->getDiscountID($discountID,$result["bestellnum"]);
				// $objResponse->alert($pc->sql);
				if ($priceKEY['price']!="") {
					$prices = $priceKEY['price'];
				}else{
					if (in_array($discountID, $classification_C)) {
							$prices = $result['cshrpriceppk'];
					}else{
						$prices = $result['cshrpriceppk'];
						// if ($result["Dprice"] =="") {
						// 	$prices = $result['cshrpriceppk'];
						// }else{
						// 	$prices = $result['Dprice'];
						// }
					}
				}

				$details->desc = (($result['source']=='M') ? $result["generic"].' (MISC)' : $result["generic"].' (PHARMA)');
				$details->prod_class = trim(strtoupper($result['prod_class']));
				$details->cash = $prices;
				$details->charge = $result["chrgrpriceppk"];
				$details->cashsc = $result["cashscprice"];
				$details->chargesc = $result["chargescprice"];
				$details->d = $result["dprice"];
				$details->soc =$result["is_socialized"];
				$details->isFs = $result["is_fs"];
				$details->noqty = ($disable_qty==1) ? TRUE : FALSE;
				$details->restricted = $result["is_restricted"]; //added by cha, august 17, 2010
				$details->source = $result['source'];
				$details->account_type = $result['account_type'];
				$details->mode = $mode;
				$details->isInventory = $result['is_in_inventory'];
				$details->barcode = $result['barcode'];
				$details->iTemCode = $result['item_code'];
				$details->areaName = $pharma_areaName;
				$details->area = $area;
				$details->NewCash = $result['cshrpriceppk'];
				$details->NewCharge = $result['chrgrpriceppk'];
				$details->price_cash = $result['price_cash'];
				$details->price_charge = $result['price_charge'];
				$details->areaISsoc = $getAllow_socialized;
				$previousDRF = $new_order->getPreviousDRF($encounter_nr,$result['bestellnum']);
				
				$details->dosagePrevious = ($previousDRF) ? $previousDRF['dosage'] : "";
				$details->routePrevious = ($previousDRF) ? $previousDRF['route'] : "";
				$details->frequencyPrevious = ($previousDRF) ? $previousDRF['frequency'] : "";


				if($result['is_in_inventory'] == 1){
					/*ADDED By MARK 2016-09-30*/
					$dataItem = $invServiceNew->GetItemListFromDai($pharma_areaAPI,'item_list');
					// var_dump(array($pharma_areaAPI,'item_list'));
					$dataItems = count($dataItem['iteminfo']['barcode']);
					if ($dataItem == 404) {
						$details->stock = "n/a";
					}else{
						if (is_array($dataItem) || !empty($dataItem)) {
						    if ($dataItems == 1) {
						     	if($dataItem['iteminfo']['barcode'] == $result['barcode']){
						     		$details->stock = (empty($dataItem['iteminfo']['quantity']) || $dataItem['iteminfo']['quantity']==0)? 0:$dataItem['iteminfo']['quantity'];
										
								}
						    }else{
							    foreach ($dataItem['iteminfo'] as $inventory => $items) {
							       	if ($result['barcode'] == $items['barcode']){
							       		$details->stock = (empty($items['quantity']) || $items['quantity']==0)? 0:$items['quantity'];
							       	}
								}
						    }
						}
					}		
				 	$objResponse->call("ErrorConnectionDAI2");
	      		    /*END added By MARK 2016-09-30*/
				}else{
	      			$details->stock = "n/a";				
				}

				$objResponse->call("addProductToList","product-list",$details);

				if ($details->prod_class == __MEDICINE_ITEM) {
					if ($dosageOptions == "") {

						$dosageList = $new_order->getDosageList();
						if($dosageList){
							while ($rs = $dosageList->FetchRow()) {
								$dosageOptions .= "<option data-value=\"".$rs['strength_disc']."\">".$rs['strength_disc']."</option>\n";
							}
						}

						$routeList = $new_order->getRouteList();
						if($routeList){
							while ($rs = $routeList->FetchRow()) {
								$routeOptions .= "<option value=\"".$rs['route_disc']."\">".$rs['route_disc']."</option>\n";
							}
						}

						$frequencyList = $new_order->getFrequencyList();
						if($frequencyList){
							while ($rs = $frequencyList->FetchRow()) {
								$frequencyOptions .= "<option value=\"".$rs['frequency_disc']."\">".$rs['frequency_disc']."</option>\n";
							}
						}

					}

					$objResponse->assign('dosage'.$details->id . "_" . $area, 'innerHTML', $dosageOptions);
					$objResponse->assign('route'.$details->id . "_" . $area, 'innerHTML', $routeOptions);
					$objResponse->assign('frequency'.$details->id . "_" . $area, 'innerHTML', $frequencyOptions);
				}else{
					$objResponse->call('disableDRF',$details->id . "_" . $area);
				}
			}
		
			//   /*START added By MARK 2016-09-30 for inventory*/
			$INV = $pc->data_is_INV($keyword,$barcode);
			// $objResponse->alert($pc->sql);
			if ($INV==1) {
				$ergebnis = $pc->search_products_for_tray_is_INV($keyword, $discountID, $area, $offset, $maxRows, '', $barcode);
				// $objResponse->alert($pc->sql);
				if ($ergebnis) {
					$total = $pc->FoundRows();
					$lastPage = floor($total/$maxRows);
					if ($page > $lastPage) $page=$lastPage;
					$rows=$ergebnis->RecordCount();
					$totalF +=$total;
					$objResponse->call("setPagination",$page,$lastPage,$maxRows,$totalF);
					$pharma_areaAPI = $pc->getAPIfromPharmaArea($area);
					$pharma_areaName = $pc->getAPIfByName($area);
					$getAllow_socialized2 = $pc->getAllow_socialized($area);
					while($result=$ergebnis->FetchRow()) {
						$details->id = $result["bestellnum"];
						if ($result["remarks"]) {
							$details->name = $result["artikelname"] ." (" . $result[ "remarks"] . ") " . $result["unit"];
						}else{
							$details->name = (trim($result["artikelname"])) . " " . $result["unit"];
						}
  						$priceKEY = $pc->getDiscountID($discountID,$result["bestellnum"]);
						if ($priceKEY['price']!="") {
							$prices = $priceKEY['price'];
						}else{
							if (in_array($discountID, $classification_C)) {
								$prices = $result['cshrpriceppk'];
							}else{
								$prices = $result['cshrpriceppk'];
								/*if ($result["Dprice"] =="") {
									$prices = $result['cshrpriceppk'];
								}else{
									$prices = $result['Dprice'];
								}*/
							}
						}
						
						$details->desc = (($result['source']=='M') ? $result["generic"].' (MISC)' : $result["generic"].' (PHARMA)');
						$details->prod_class = trim(strtoupper($result['prod_class']));
						$details->cash = $prices;
						$details->charge = $result["chrgrpriceppk"];
						$details->cashsc = $result["cashscprice"];
						$details->chargesc = $result["chargescprice"];
						$details->d = $result["dprice"];
						$details->soc = $result["is_socialized"];
		                $details->isFs = $result["is_fs"];
						$details->noqty = ($disable_qty==1) ? TRUE : FALSE;
						$details->restricted = $result["is_restricted"]; //added by cha, august 17, 2010
						$details->source = $result['source'];
						$details->account_type = $result['account_type'];
						$details->mode = $mode;
						$details->isInventory = $result['is_in_inventory'];
						$details->barcode = $result['barcode'];
						$details->iTemCode = $result['item_code'];
						$details->areaName = $pharma_areaName;
						$details->area = $area;
						$details->NewCash = $result['cshrpriceppk'];
						$details->NewCharge = $result['chrgrpriceppk'];
						$details->price_cash = $result['price_cash'];
						$details->price_charge = $result['price_charge'];
						$details->areaISsoc = $getAllow_socialized2;
						$dataItem = $invServiceNew->GetItemListFromDai($pharma_areaAPI,'item_list');
					    $dataItems = count($dataItem['iteminfo']['barcode']);
					    $previousDRF = $new_order->getPreviousDRF($encounter_nr,$result['bestellnum']);
					    $details->dosagePrevious = ($previousDRF) ? $previousDRF['dosage'] : "";
						$details->routePrevious = ($previousDRF) ? $previousDRF['route'] : "";
						$details->frequencyPrevious = ($previousDRF) ? $previousDRF['frequency'] : "";

					    // var_dump($dataItem);
					    $details->stock = "n/a";
						if ($dataItem == 404) {
							$details->stock = "n/a";
						}else{
						    if (is_array($dataItem) || !empty($dataItem)) {
						     	if ($dataItems == 1) {
						     		if($dataItem['iteminfo']['barcode'] == $result['barcode']){
						     			$details->stock = (empty($dataItem['iteminfo']['quantity']) || $dataItem['iteminfo']['quantity']==0)? 0:$dataItem['iteminfo']['quantity'];
									}
						     	}else{
							       foreach ($dataItem['iteminfo'] as $inventory => $items) {
							       		if ($result['barcode'] == $items['barcode']) {
			                 				$details->stock = (empty($items['quantity']) || $items['quantity']==0)? "n\a":$items['quantity'];
			                 				break;
							       		}
									}
						     	}
							 }
						}
						// if ($details->stock !=0) {
						// 	if (in_array($result['bestellnum'], $bestellnumCheck)) {

						// 	}else{
						$objResponse->call("addProductToList","product-list",$details);

						if ($details->prod_class == __MEDICINE_ITEM) {
							if ($dosageOptions == "") {

								$dosageList = $new_order->getDosageList();
								if($dosageList){
									while ($rs = $dosageList->FetchRow()) {
										$dosageOptions .= "<option data-value=\"".$rs['strength_disc']."\">".$rs['strength_disc']."</option>\n";
									}
								}

								$routeList = $new_order->getRouteList();
								if($routeList){
									while ($rs = $routeList->FetchRow()) {
										$routeOptions .= "<option value=\"".$rs['route_disc']."\">".$rs['route_disc']."</option>\n";
									}
								}

								$frequencyList = $new_order->getFrequencyList();
								if($frequencyList){
									while ($rs = $frequencyList->FetchRow()) {
										$frequencyOptions .= "<option value=\"".$rs['frequency_disc']."\">".$rs['frequency_disc']."</option>\n";
									}
								}
								
							}
								
								$objResponse->assign('dosage'.$details->id . "_" . $area, 'innerHTML', $dosageOptions);
								$objResponse->assign('route'.$details->id . "_" . $area, 'innerHTML', $routeOptions);
								$objResponse->assign('frequency'.$details->id . "_" . $area, 'innerHTML', $frequencyOptions);
							}else{
								$objResponse->call('disableDRF',$details->id . "_" . $area);
							}
						// 	}
						// }

					}
				}
			}
		}else {
			if ($config['debug'])
				$objResponse->addScriptCall("display",$sql);
			else
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) {
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			$objResponse->call("addProductToList","product-list",NULL);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	/*function Added BY MARK Lou gupnp_root_device_get_relative_location(root_device) 2016-10-05*/
	// 	function IventoryCheckCOnnection() {
	// 	global $db;
	// 	$invServiceNew = new InventoryService();
	// 	$objResponse = new xajaxResponse();
	// 	try {
	// 		$invServiceNew =new InventoryServiceNew();
		
	// 	$onTheline = $invServiceNew->PingConnectionToDAI(); 
	// 		if ($onTheline ==1) {
	// 	  		$objResponse->call("display","<em><font color='Green'><strong>&nbsp;<span id='warningcaption'>".
	// 		            		"INVENTORY SYSTEM(".$invServiceNew->baseAddress.")IS CONNECTED....</span></strong></font></em>");
	      		
	// 		}else{
	// 			$objResponse->call("ErrorConnection");
	// 		}
	// 	} catch (Exception $e) {
	// 			$objResponse->call("ErrorConnection");
	// 	}
	// 	return $objResponse;
	// }

	#--for requesting anesthetic medicines only from OR
	function populateORProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$targetItems='') {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();
		$pc = new SegPharmaProduct();

		$maxRows = 10;
		$offset = $page * $maxRows;

		#-------added by CHA 12-15-2009 --------------------
		/*if($area=='OR')
			$ergebnis = $pc->search_products_for_anesthesia_tray($keyword, $discountID, $area, $offset, $maxRows, $targetItems);
		else*/	#--removed by cha, jan 9,2010
			$ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows);

		#$objResponse->alert($pc->sql);
		#return $objResponse;
		if ($ergebnis) {
			$total = $pc->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;

			$rows=$ergebnis->RecordCount();

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			while($result=$ergebnis->FetchRow()) {

				//$objResponse->alert($result['expiration_dates']);
				$details->id = $result["bestellnum"];
				if ($result["remarks"])
				{
					$details->name = $result["artikelname"] ." (" . $result[ "remarks"] . ")";
				}else{
					$details->name = (trim($result["artikelname"]));
				}
				$details->desc = $result["generic"];
				$details->restricted = $result["is_restricted"];
				$details->cash = $result["cshrpriceppk"];
				$details->charge = $result["chrgrpriceppk"];
				$details->cashsc = $result["cashscprice"];
				$details->chargesc = $result["chargescprice"];
				$details->d = $result["dprice"];
				$details->expiry = $result["expiration_dates"];
				$details->soc = $result["is_socialized"];
				$details->stock = $result["qty_stock"];
				$details->noqty = ($disable_qty==1) ? TRUE : FALSE;
								 $details->classification = $result['class_name'];

				#--added by CHA, jan 9, 2010
					$objResponse->call("addProductToAnestheticList","product-list",$details);
			}
		}
		else {
			if ($config['debug'])
				$objResponse->call("display",$sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) {
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			$objResponse->call("addProductToList","product-list",NULL);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	#-----added by CHA, Feb 10, 2010------------
	#-----for or packages------------------------
	function populatePackageItemList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$mode) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();
		$pc = new SegPharmaProduct();

		$maxRows = 10;
		$offset = $page * $maxRows;

		if($area=='PH'){
                    $area='';
                    $ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows);
		}
		else
                    $ergebnis = $pc->search_products_for_package_itemsTray($keyword, $discountID, $area, $offset, $maxRows);
		#$objResponse->alert($pc->sql);
		#return $objResponse;
		if ($ergebnis) {
			$total = $pc->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;

			$rows=$ergebnis->RecordCount();

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");

			while($result=$ergebnis->FetchRow()) {

				//$objResponse->alert($result['expiration_dates']);
				$details->id = $result["bestellnum"];
				if ($result["remarks"])
				{
					$details->name = $result["artikelname"] ." (" . $result[ "remarks"] . ")";
				}else{
					$details->name = (trim($result["artikelname"]));
				}		
				$details->desc = $result["generic"];
				$details->restricted = $result["is_restricted"];
				$unit_options='';
				$res = $db->Execute("select * from seg_unit where is_deleted='0' order by unit_name");
				while($row=$res->FetchRow())
				{
					$unit_options.='<option value="'.$row['unit_name'].'">'.$row['unit_name'].'</option>';
				}
				$details->opt = $unit_options;
				$details->mode = $mode;
				$objResponse->call("addPackageItemsToList","product-list",$details);

			}
		}
		else {
			if (defined("__DEBUG_MODE"))
				$objResponse->call("display",$sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) {
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			$objResponse->call("addProductToList","product-list",NULL);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_pharma_product.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path."modules/pharmacy/ajax/order-tray.common.php");
	require_once($root_path . 'include/care_api_classes/inventory/InventoryService.php');
	require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
    require_once($root_path.'include/care_api_classes/class_order.php');
	require_once($root_path."include/care_api_classes/class_hospital_admin.php");
	
	$xajax->processRequest();
