<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/clinics/ajax/clinic-requests.common.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_order.php');
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path."include/care_api_classes/billing/class_bill_info.php");
require_once($root_path.'include/care_api_classes/class_encounter.php');#added by janken 11/13/2014
require_once($root_path.'include/care_api_classes/class_personell.php'); #added rnel
// require_once($root_path.'frontend/bootstrap.php'); #commented rnel

function populateMiscRequests($encounter_nr, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$misc_obj = new SegOR_MiscCharges();
	//$objResponse->alert(" final bill \n".$is_bill_final);

	$sql = "SELECT sms.refno, IF(sms.is_cash='0','Charge','Cash') AS `charge_type`, sms.request_source, IFNULL(cu.name, sms.create_id) AS create_user FROM seg_misc_service sms\n".
				" LEFT JOIN care_users cu ON cu.login_id = sms.create_id \n".
				" WHERE sms.encounter_nr=".$db->qstr($encounter_nr).
				"AND EXISTS (SELECT * FROM seg_misc_service_details WHERE refno = sms.`refno` AND is_deleted != 1)".
				" AND DATE(sms.chrge_dte)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY sms.chrge_dte, sms.refno DESC";
	$result = $db->Execute($sql);
	//$objResponse->alert("misc\n".$sql);
	$objResponse->assign("misc_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			$objResponse->call("createTableHeader", "misc_requests", "misc-list".$ref['refno'], $ref['refno'], $ref['charge_type'], $ref['create_user']);
			$res = $misc_obj->getMiscOrderItemsByRefno($ref['refno']);
			//$objResponse->alert("misc\n".$misc_obj->sql);
			if($res!==FALSE){
				$req_flag=false;
				$not_req_flag = 0;		//counter for null request_flag
			 	$total_amount = 0;
			 	while($row=$res->FetchRow())
			 	{
					 switch(strtolower($row["request_flag"]))
					 {
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=false; //Change by Christian 11-07-19 from "true" value to enable detele button
								$not_req_flag += 1; break;
                                                 case 'crcu':
								$request_flag = '<img src="../../images/flag_crcu.gif" title="Item paid through CREDIT and COLLECTION (Cash)"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; $not_req_flag += 1; break;
					 }
        //edited by jasper 04/10/2013
			 		$data = array(
						'refno'=>$ref['refno'],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["chrge_dte"])),
						'status'=>$request_flag,
						'item_name'=>$row["name"],
						'item_code'=>$row["code"],
						'item_qty'=>$row["quantity"],
						'total_prc'=>$row["net_price"],
						'item_prc'=>parseFloatEx($row["net_price"]/$row["quantity"])
					);
                    /*$data = array(
                        'refno'=>$ref['refno'],
                        'order_date'=>date('d-M-Y h:i: a',strtotime($row["chrge_dte"])),
                        'status'=>$request_flag,
                        'item_name'=>$row["name"],
                        'item_code'=>$row["code"],
                        'item_qty'=>$row["quantity"],
                        'total_prc'=>parseFloatEx($row["chrg_amnt"]*$row["quantity"]),
                        'item_prc'=>parseFloatEx($row["chrg_amnt"])
                    );*/
					$objResponse->call("printRequestlist", "misc_requests", "misc-list".$ref['refno'], $data);

                    if($row['area'] == 'ic'){
                        if($row['is_cash'] == '1'){
                            $total_amountCash += $row["net_price"];
                        }else{
                            $total_amount += $row["net_price"];
                        }
                    }else{
                    	#added by art 08/20/2014 to fix calcutaion issue on cash requests
                    	if($row['is_cash'] == '1'){
                    		$total_amountCash+=parseFloatEx($row["net_price"]);
                    	}else{
                    		$total_amount+=parseFloatEx($row["quantity"]*$row["chrg_amnt"]);
                    	}
                    	#end art
                        /*$total_amount+=parseFloatEx($row["quantity"]*$row["chrg_amnt"]);
                        $total_amountCash+=parseFloatEx($row["net_price"]);*/ #commented by art 08/20/2014
                    }
				}

				if(strtolower($ref["charge_type"])=="cash") {
					$total_cash+=parseFloatEx($total_amountCash);
				}
				else if(strtolower($ref["charge_type"])=="charge") {
					$total_charge+=parseFloatEx($total_amount);
				}

				 #editted by CELSY 08/25/10
				if($ref["request_source"]==$ptype)
					$notPtype = false;
				else
					$notPtype = true;

				 #$objResponse->alert($ref["request_source"]."  hi  ".$ptype."  notptype ".$notPtype,"\n final bill ".$is_bill_final);
				if($not_req_flag>=1 && $notPtype==false && $is_bill_final==0) {


					$buttons = '<button class="segButton" onclick="openEditRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" onclick="openDeleteRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
			 	}else {
					$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}

//			 if($req_flag==true)
//			 {
//				$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//									'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//			 }else {
//				$buttons = '<button class="segButton" onclick="openEditRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//									'<button class="segButton" onclick="openDeleteRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//			 }

				$objResponse->assign("btn-".$ref['refno'],"innerHTML", $buttons);
		 	}
		}
		$objResponse->assign("misc-total-cash", "innerHTML", number_format($total_cash, 2));
		$objResponse->assign("misc-total-charge", "innerHTML", number_format($total_charge, 2));
	}
	return $objResponse;
}

function populateIpRequests($encounter_nr,$ptype,$is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();

	//commented by justin
	// #added by janken for disabling the add packages button
	// $seg_encounter = new Encounter();
	// $encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
	// if($encounter_details['encounter_type'] != '2')
	// 	$objResponse->call('disableAddPackage');
	// #ended by janken
	
	$filters = array('inpatient'=>$encounter_nr, 'date'=>$date);
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("IP order\n".$order_obj->sql);
	$objResponse->assign("ip_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($res!==FALSE) {
		while($row=$res->FetchRow()){
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			
			if($result!==FALSE) {
				$charge_type = $row["is_cash"]==0?'Charge':'Cash';
				$objResponse->call("createTableHeader", "ip_requests", "ip-list".$row["refno"], $row["refno"], $charge_type);
				$req_flag=false;
				$total_amount = 0;
				$serve_status = FALSE;
				$charityStatus = FALSE;
				while($row2=$result->FetchRow()){
					switch(strtolower($row2["request_flag"])){
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						case 'crcu':
								$request_flag = '<img src="../../images/flag_crcu.gif" title="Item paid through CREDIT and COLLECTION (Cash)"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}
						if ($row2["is_down_inv"]== 1 && $row2["is_in_inventory"]== 1 && $row2['inv_uid'] !=""){
							$DAIstatus ='<img  title="Overriden data in Inventory" src="../../images/claim_notok.gif" align="absmiddle"/>';
						}
						else if ($row2["is_down_inv"]  == 0 &&  $row2["is_in_inventory"] == 1 && $row2['inv_uid'] !=""){
							$DAIstatus ='<img onclick="viewTransDai(\''.$row2["inv_api_key"].'\',\''.$row2["pid"].'\')" class="ViewDAItransact" style="cursor:pointer;" title="Transacted in DAI" src="../../images/claim_ok.gif" align="absmiddle"/>';
						}
					
					$served_array = array();
					$served_array[] = $row2['SERVE'];
					
					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'DAIstatus'=>($row2['SERVE']=='S') ? $DAIstatus : "",
						'is_in_inventory'=>$row2["is_in_inventory"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					if($row2['requested_qty'] != $row2['dispensed_qty'])
						$serve_status_NEW = TRUE;
	
					if($row2["serve_status"] == 'S')
						$serve_status = TRUE;
					
					if(strtolower($row2["request_flag"])=='charity' && $row2["serve_status"] == 'S'){
						$charityStatus = TRUE;
					}

					$objResponse->call("printRequestlist", "ip_requests", "ip-list".$row["refno"], $data);
					$total_amount+=parseFloatEx($row2["quantity"]*$row2["force_price"]);
				}
				// foreach ($served_array as $value) {
				// 	$objResponse->alert($value);
				// }

				if(strtolower($charge_type)=="cash") {
					$total_cash+=parseFloatEx($total_amount);
				}
				else if(strtolower($charge_type)=="charge") {
					$total_charge+=parseFloatEx($total_amount);
				}

				$hasPaid = $order_obj->hasPaidOrder($row["refno"]);

				$key = $order_obj->getServeStatus($row["refno"]);
				// $objResponse->alert($serve_status_NEW);
				//
				#editted by CELSY 08/25/10
				if($row["request_source"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					//$objResponse->alert("req-".$req_flag."notp-".$notPtype."bill-".$is_bill_final." ".$ptype.' = '.);
				// var_dump($req_flag==false . $notPtype == false . $is_bill_final==0 . !$hasPaid)
				if(($req_flag==false && $is_bill_final==0 && !$hasPaid) || ($is_bill_final==0 && !$hasPaid && !$charityStatus && $ptype=='DOCTOR')) { //Updated by Christian 12-05-19
			   
					$buttons = '<button class="segButton" onclick="openEditRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" onclick="openDeleteRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
			   		 
				// Commented by Matsuu 05102017
// 					$r = SegHis\modules\costCenter\models\PharmacyRequestSearch::search(
// 						array(
// 							'referenceNo' => $row["refno"],
// 						)
// 					);
			 
// 					if ($r->allowDelete) {
// 						$buttons = <<<HTML
// 							<button class="segButton" style="cursor: pointer;"
// 							onclick="openEditRequest('ip_requests','{$row["refno"]}');return false;">
// 								<img src="../../gui/img/common/default/page_edit.png"/>
// 								Edit
// 							</button>
// 							<button class="segButton" style="cursor: pointer;"
// 							onclick="openDeleteRequest('ip_requests','{$row["refno"]}','{$r->getWarning()}');return false;">
// 								<img src="../../gui/img/common/default/cancel.png"/>
// 								Delete
// 							</button>
// HTML;
// 						} else {
// 							$buttons = <<<HTML
// 							<button class="segButton" style="cursor: pointer;" disabled title="{$r->getMessage()}">
// 								<img src="../../gui/img/common/default/page_edit.png"/>
// 								Edit
// 							</button>
// 							<button class="segButton" style="cursor: pointer;" disabled title="{$r->getMessage()}">
// 								<img src="../../gui/img/common/default/cancel.png"/>
// 								Delete
// 							</button>
// HTML;
// 						}
										// ended comment by Matsuu 05102017
				 }else
				 {
					$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}
//				if($req_flag==true)
//				{
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				}else{
//					$buttons = '<button class="segButton" onclick="openEditRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" onclick="openDeleteRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
		$objResponse->assign("ip-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("ip-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateMgRequests($encounter_nr, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();
	$filters = array('inpatient'=>$encounter_nr,'area'=>'MG', 'date'=>$date);
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("Mg order\n".$order_obj->sql);
	$objResponse->assign("mg_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($res!==FALSE) {
		while($row=$res->FetchRow())
		{
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			$charge_type = $row["is_cash"]==0?'Charge':'Cash';
			if($result!==FALSE) {
				$objResponse->call("createTableHeader", "mg_requests", "mg-list".$row["refno"], $row["refno"], $charge_type);
				$req_flag=false;
				$total_amount = 0;
				while($row2=$result->FetchRow())
				{
					switch(strtolower($row2["request_flag"]))
					{
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}

					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					$objResponse->call("printRequestlist", "mg_requests", "mg-list".$row["refno"], $data);

					$total_amount+=parseFloatEx($row2["quantity"]*$row2["force_price"]);
				}

				if(strtolower($charge_type)=="cash") {
					$total_cash+=parseFloatEx($total_amount);
				}
				else if(strtolower($charge_type)=="charge") {
					$total_charge+=parseFloatEx($total_amount);
				}
				#editted by CELSY 08/25/10
					if($row ["request_source"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}else {

					$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}

//				if($req_flag==true)
//				{
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				}else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
		$objResponse->assign("mg-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("mg-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}


function populateOtherRequests($encounter_nr, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();
	$filters = array('inpatient'=>$encounter_nr,'area'=>'MG', 'date'=>$date);
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("Mg order\n".$order_obj->sql);
	$objResponse->assign("mg_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($res!==FALSE) {
		while($row=$res->FetchRow())
		{
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			$charge_type = $row["is_cash"]==0?'Charge':'Cash';
			if($result!==FALSE) {
				$objResponse->call("createTableHeader", "mg_requests", "mg-list".$row["refno"], $row["refno"], $charge_type);
				$req_flag=false;
				$total_amount = 0;
				while($row2=$result->FetchRow())
				{
					switch(strtolower($row2["request_flag"]))
					{
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}

					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					$objResponse->call("printRequestlist", "mg_requests", "mg-list".$row["refno"], $data);

					$total_amount+=parseFloatEx($row2["quantity"]*$row2["force_price"]);
				}

				if(strtolower($charge_type)=="cash") {
					$total_cash+=parseFloatEx($total_amount);
				}
				else if(strtolower($charge_type)=="charge") {
					$total_charge+=parseFloatEx($total_amount);
				}
				#editted by CELSY 08/25/10
					if($row ["request_source"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}else {

					$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}

//				if($req_flag==true)
//				{
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				}else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
		$objResponse->assign("mg-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("mg-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}


function populateSpLabRequests($encounter_nr, $pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='SPL') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("splab_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while ($ref = $result->FetchRow()) {
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
				" AND r.refno=" . $db->qstr($ref["refno"]) . " AND d.status <> 'deleted' ORDER BY d.request_flag ASC ";
			 $res = $db->Execute($sql2);
			 $hasServe = false;
			 $hasPaid = FALSE;
			 $charityStatus = FALSE;
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "splab_requests", "splab-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
					$total_amount = 0;
				while ($row = $res->FetchRow()) {
					switch (strtolower($row["request_flag"])) {
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							case 'crcu':
									$request_flag = '<img src="../../images/flag_crcu.gif" title="Item paid through CREDIT and COLLECTION (Cash)"/>';
									$req_flag=true;
									break;
						default:
							$request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>';
							$req_flag = false;
							break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "splab_requests", "splab-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);

						if($row["is_served"])
							$hasServe = true;

						#Added by Christian 12-27-19
			 			$charityStatus = FALSE;
						$flagStatus = strtolower($row["request_flag"]);

						if($flagStatus == 'paid')
							$hasPaid = TRUE;

						if($flagStatus == 'charity' && $hasServe)
							$charityStatus = TRUE;
						#end Christian 12-27-19

					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
				} else if (strtolower($ref["charge_type"]) == "charge") {
						$total_charge+=parseFloatEx($total_amount);
					}

					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;
					//$objResponse->alert($notPtype." weh\n".$ref["source_req"]."\nptype\n".$ptype);

					if(($req_flag==false && $notPtype==false && $is_bill_final==0) || (!$hasPaid && $notPtype==false && $is_bill_final==0 && !$charityStatus && $ptype=='DOCTOR')) { //Update by Christian 12-27-19

					$r = SegHis\modules\costCenter\models\SpecialLaboratoryRequestSearch::search(array(
						'referenceNo' => $ref["refno"]
					));

					if ($r->allowDelete) {
						$buttons = <<<HTML
<button class="segButton" style="cursor: pointer;"
onclick="openEditRequest('splab_requests','{$ref["refno"]}');return false;">
	<img src="../../gui/img/common/default/page_edit.png"/>
	Edit
</button>
<button class="segButton" style="cursor: pointer;"
onclick="openDeleteRequest('splab_requests','{$ref["refno"]}','{$r->getWarning()}');return false;">
	<img src="../../gui/img/common/default/cancel.png"/>
	Delete
</button>
HTML;
					} else {
						$buttons = <<<HTML
<button class="segButton" style="cursor: pointer;"
onclick="openEditRequest('splab_requests','{$ref["refno"]}');return false;">
	<img src="../../gui/img/common/default/page_edit.png"/>
	Edit
</button>
<button class="segButton" style="cursor: pointer;" disabled title="{$r->getMessage()}">
	<img src="../../gui/img/common/default/cancel.png"/>
	Delete
</button>
HTML;
					}

//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'splab_requests\',\'' . $ref["refno"] . '\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>' .
//						'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'splab_requests\',\'' . $ref["refno"] . '\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				} else {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }
//				 if($req_flag==true)
//				 {
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				 }else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("splab-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("splab-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}


function populateLabRequests($encounter_nr,$pid, $ptype, $is_bill_final, $date, $isIPBM=0)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();
	$ipbmsql = ')';

	// Added by carriane 03/16/18
	if($isIPBM)
		$ipbmsql = "OR ref_source='IPBM')";
	// end carriane

	// updated by carriane 03/16/18; added $ipbmsql
	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='LB' ".$ipbmsql." AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("lab_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY d.request_flag ASC ";
			 $res = $db->Execute($sql2);
			 $hasServe = false;
			 $hasPaid = FALSE;
			 $charityStatus = FALSE;
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "lab_requests", "lab-list".$ref["refno"], $ref["refno"], $ref['charge_type']);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 case 'crcu':
							 		$request_flag = '<img src="../../images/flag_crcu.gif" title="Item paid through CREDIT and COLLECTION (Cash)"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "lab_requests", "lab-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);

						if($row["is_served"])
							$hasServe = true;

						#Added by Christian 12-27-19
						$flagStatus = strtolower($row["request_flag"]);

						if($flagStatus == 'paid')
							$hasPaid = TRUE;

						if($flagStatus == 'charity' && $hasServe)
							$charityStatus = TRUE;
						#end Christian 12-27-19
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if(($req_flag==false && $notPtype==false && $is_bill_final==0) || (!$hasPaid && $notPtype==false && $is_bill_final==0 && !$charityStatus && $ptype=='DOCTOR')) { #Updated by Christian 12-27-19

						$r = SegHis\modules\costCenter\models\LaboratoryRequestSearch::search(array(
							'referenceNo' => $ref["refno"],
						));

						if ($r->allowDelete) {
							$buttons = <<<HTML
							<button class="segButton" style="cursor: pointer;"
							onclick="openEditRequest('lab_requests','{$ref["refno"]}');return false;">
								<img src="../../gui/img/common/default/page_edit.png"/>
								Edit
							</button>
							<button class="segButton" style="cursor: pointer;"
							onclick="openDeleteRequest('lab_requests','{$ref["refno"]}','{$r->getWarning()}');return false;">
								<img src="../../gui/img/common/default/cancel.png"/>
								Delete
							</button>
HTML;
						}else{
							$buttons = <<<HTML
							<button class="segButton" style="cursor: pointer;"
							onclick="openEditRequest('lab_requests','{$ref["refno"]}');return false;">
								<img src="../../gui/img/common/default/page_edit.png"/>
								Edit
							</button>
							<button class="segButton" style="cursor: pointer;" disabled title="{$r->getMessage()}">
								<img src="../../gui/img/common/default/cancel.png"/>
								Delete
							</button>
HTML;
						}

//						if(!$hasServe){
//							$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//							'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//						}else{
//							$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//							'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//						}
					}else{
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
							'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}

//				 if($req_flag==true)
//				 {
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				 }else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("lab-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("lab-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateICLabRequests($encounter_nr,$pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='IC') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("iclab_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "iclab_requests", "iclab-list".$ref["refno"], $ref["refno"], $ref['charge_type']);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "iclab_requests", "iclab-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'iclab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'iclab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }else
				 {   $buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }

//				 if($req_flag==true)
//				 {
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				 }else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("iclab-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("iclab-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateBloodRequests($encounter_nr,$pid,$ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='BB') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("blood_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while ($ref = $result->FetchRow()) {
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
				" AND r.refno=" . $db->qstr($ref["refno"]) . " AND d.status <> 'deleted' ORDER BY d.request_flag ASC ";
			 $res = $db->Execute($sql2);
			// $objResponse->alert("refno\n".$sql2);
			 $hasServe = false;
			 $hasPaid = FALSE;
			 $charityStatus = FALSE;
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "blood_requests", "blood-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
					$total_amount = 0;
				while ($row = $res->FetchRow()) {
					switch (strtolower($row["request_flag"])) {
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							case 'crcu':
									$request_flag = '<img src="../../images/flag_crcu.gif" title="Item paid through CREDIT and COLLECTION (Cash)"/>';
									$req_flag=true;
									break;
						default:
							$request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>';
							$req_flag = false;
							break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "blood_requests", "blood-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);

						if($row["is_served"])
							$hasServe = true;

						#Added by Christian 12-27-19
						$flagStatus = strtolower($row["request_flag"]);

						if($flagStatus == 'paid')
							$hasPaid = TRUE;

						if($flagStatus == 'charity' && $hasServe)
							$charityStatus = TRUE;
						#end Christian 12-27-19

					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
				} else if (strtolower($ref["charge_type"]) == "charge") {
						$total_charge+=parseFloatEx($total_amount);
					}

					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if(($req_flag==false && $notPtype==false && $is_bill_final==0) || (!$hasPaid && $notPtype==false && $is_bill_final==0 && !$charityStatus && $ptype=='DOCTOR')) {

					$r = SegHis\modules\costCenter\models\BloodBankRequestSearch::search(array(
						'referenceNo' => $ref["refno"]
					));

					if ($r->allowDelete) {
						$buttons = <<<HTML
<button class="segButton" style="cursor: pointer;"
onclick="openEditRequest('blood_requests','{$ref["refno"]}');return false;">
	<img src="../../gui/img/common/default/page_edit.png"/>
	Edit
</button>
<button class="segButton" style="cursor: pointer;"
onclick="openDeleteRequest('blood_requests','{$ref["refno"]}','{$r->getWarning()}');return false;">
	<img src="../../gui/img/common/default/cancel.png"/>
	Delete
</button>
HTML;
					} else {
						$buttons = <<<HTML
<button class="segButton" style="cursor: pointer;"
onclick="openEditRequest('blood_requests','{$ref["refno"]}');return false;">
	<img src="../../gui/img/common/default/page_edit.png"/>
	Edit
</button>
<button class="segButton" style="cursor: pointer;" disabled title="{$r->getMessage()}">
	<img src="../../gui/img/common/default/cancel.png"/>
	Delete
</button>
HTML;
					}

//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'blood_requests\',\'' . $ref["refno"] . '\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>' .
//						'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'blood_requests\',\'' . $ref["refno"] . '\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				} else {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }
//					if($req_flag==true)
//					{
//						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="return false;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//											'<button class="segButton" style="cursor: pointer;" onclick="return false;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//					}else {
//						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//											'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("blood-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("blood-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateRadioRequests($encounter_nr, $pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	#edited by CELSY 8/25/10
	//get radio refno
	/*$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type` FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND status <> 'deleted' ".
				" AND DATE(request_date)=DATE(NOW())".
				" ORDER BY request_date, request_time DESC ";*/
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND status <> 'deleted' AND fromdept = 'RD' ".
				" AND DATE(request_date)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY request_date, request_time, refno DESC ";
	$result = $db->Execute($sql);
	// $objResponse->alert("refno\n".$sql);
	$objResponse->assign("radio_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if ($result !== FALSE) {
		while ($ref = $result->FetchRow()) {
			 $sql2 = "SELECT CONCAT(r.request_date,' ',r.request_time) as `orderdate`, rd.service_code, s.name,\n".
						" rd.price_cash, rd.price_charge, 1 as `quantity`, rd.request_flag, rd.is_served, \n".
						" EXISTS(SELECT f.batch_nr FROM care_test_findings_radio AS f WHERE f.batch_nr=rd.batch_nr) as `has_result`\n".
						" FROM seg_radio_serv AS r \n".
						" INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno \n".
						" INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
				" AND r.refno=" . $db->qstr($ref["refno"]) . " AND rd.status <> 'deleted' ORDER BY rd.request_flag ASC ";
			$res = $db->Execute($sql2);
			 $hasResult = FALSE;
			 $hasServe = FALSE;
			 $hasPaid = FALSE;
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "radio_requests", "radio-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
				$total_amount = 0;
				while ($row = $res->FetchRow()) {
					switch (strtolower($row["request_flag"])) {
						case 'cmap':
							$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
							break;
						case 'lingap':
							$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
							break;
						case 'paid':
							$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
							break;
						case 'charity':
							$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
							break;
							case 'crcu':
									$request_flag = '<img src="../../images/flag_crcu.gif" title="Item paid through CREDIT and COLLECTION (Cash)"/>';
									$req_flag=true;
									break;
						default:
							$request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>';
							$req_flag = false;
							break;
					}

					$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["request_date"]." ".$row["request_time"])),
							'status'=>$request_flag,
							'is_served'=>$row["has_result"],
							'item_name'=>$row["name"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
					);
						$objResponse->call("printRequestlist", "radio_requests", "radio-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);

						#Added by Christian 12-27-19
						$flagStatus = strtolower($row["request_flag"]);
						$hasServe = FALSE;
						if($row["is_served"] == 1)
							$hasServe = TRUE;

						if($flagStatus == 'paid')
							$hasPaid = TRUE;
						$charityStatus=FALSE;
						if($flagStatus == 'charity' && $hasServe)
							$charityStatus = TRUE;
						#end Christian 12-27-19

				}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
				} else if (strtolower($ref["charge_type"]) == "charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					if($ref["source_req"]==$ptype)
					$notPtype = false;
				else
					$notPtype = true;
				#editted by CELSY 08/25/10
				//if($req_flag==true)
					if(($req_flag==false && $notPtype==false && $is_bill_final==0) || (!$hasPaid && $notPtype==false && $is_bill_final==0 && !$charityStatus && $ptype=='DOCTOR')) {

					$r = SegHis\modules\costCenter\models\RadiologyRequestSearch::search(array(
						'referenceNo' => $ref["refno"],
					));

					if ($r->allowDelete) {
						$buttons = <<<HTML
<button class="segButton" style="cursor: pointer;"
onclick="openEditRequest('radio_requests','{$ref["refno"]}');return false;">
	<img src="../../gui/img/common/default/page_edit.png"/>
	Edit
</button>
<button class="segButton" style="cursor: pointer;"
onclick="openDeleteRequest('radio_requests','{$ref["refno"]}','{$r->getWarning()}');return false;">
	<img src="../../gui/img/common/default/cancel.png"/>
	Delete
</button>
HTML;
				}else{
						$buttons = <<<HTML
<button class="segButton" style="cursor: pointer;"
onclick="openEditRequest('radio_requests','{$ref["refno"]}');return false;">
	<img src="../../gui/img/common/default/page_edit.png"/>
	Edit
</button>
<button class="segButton" style="cursor: pointer;" disabled title="{$r->getMessage()}">
	<img src="../../gui/img/common/default/cancel.png"/>
	Delete
</button>
HTML;
					}

//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'radio_requests\',\'' . $ref["refno"] . '\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>' .
//						'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'radio_requests\',\'' . $ref["refno"] . '\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				} else {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'radio_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			}

		}
		$objResponse->assign("radio-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("radio-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateOBGRequests($encounter_nr, $pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	#edited by CELSY 8/25/10
	//get radio refno
	/*$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type` FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND status <> 'deleted' ".
				" AND DATE(request_date)=DATE(NOW())".
				" ORDER BY request_date, request_time DESC ";*/
	$sql = "SELECT DISTINCT srs.refno,IF(srs.is_cash = '0', 'Charge', 'Cash') AS `charge_type`,srs.source_req FROM seg_radio_serv AS srs INNER JOIN care_test_request_radio AS ctrr ON ctrr.`refno` = srs.`refno` INNER JOIN seg_radio_services AS srss ON srss.`service_code` = ctrr.`service_code` INNER JOIN seg_radio_service_groups AS srsg ON srsg.`group_code` = srss.`group_code` WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND srs.status <> 'deleted' AND srsg.`department_nr` = 209 AND srs.fromdept = 'OBGUSD'".
				" AND DATE(srs.request_date)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY srs.request_date, srs.request_time,srs.refno DESC ";
	$result = $db->Execute($sql);
	// $objResponse->alert("refno\n".$sql);
	$objResponse->assign("obgyne_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while ($ref = $result->FetchRow()) {
			 $sql2 = "SELECT CONCAT(r.request_date,' ',r.request_time) as `orderdate`, rd.service_code, s.name,\n".
						" rd.price_cash, rd.price_charge, 1 as `quantity`, rd.request_flag,rd.pf ,\n".
						" EXISTS(SELECT f.batch_nr FROM care_test_findings_radio AS f WHERE f.batch_nr=rd.batch_nr) as `has_result`\n".
						" FROM seg_radio_serv AS r \n".
						" INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno \n".
						" INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
				" AND r.refno=" . $db->qstr($ref["refno"]) . " AND rd.status <> 'deleted' ORDER BY rd.request_flag ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "obgyne_requests", "obgyne-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
					$total_amount = 0;
				while ($row = $res->FetchRow()) {
					switch (strtolower($row["request_flag"])) {
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
						default:
							$request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>';
							$req_flag = false;
							break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["orderdate"])),
							'status'=>$request_flag,
							'is_served'=>$row["has_result"],
							'item_name'=>$row["name"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"] + $row['pf'],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"] + $row['pf'])
						);
						$objResponse->call("printRequestlist", "obgyne_requests", "obgyne-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
				} else if (strtolower($ref["charge_type"]) == "charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;
					#editted by CELSY 08/25/10
					//if($req_flag==true)
					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'obgyne_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'obgyne_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}else{
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'obgyne_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }

		}
		$objResponse->assign("ob-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("ob-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function deleteRequest($refno)
{
	global $db, $root_path;
	$srv=new SegLab;
	$objResponse = new xajaxResponse();

	$sql = "SELECT ref_no FROM seg_pay_request
				WHERE ref_source = 'LD' AND ref_no = '$refno'
				UNION
				SELECT refno FROM seg_lab_result
				WHERE refno = '$refno'";

	 $res=$db->Execute($sql);
	 $row=$res->RecordCount();

	if ($row==0){

		$status=$srv->deleteRequestor($refno);

		if ($status) {
			$srv->deleteLabServ_details($refno);

			try {
                require_once($root_path . 'include/care_api_classes/emr/services/LaboratoryEmrService.php');
                $labService = new LaboratoryEmrService();

                $labService->deleteLabRequest($refno);
            } catch (Exception $exc) {
                // echo $exc->getTraceAsString();die;
            }

			$objResponse->alert("The request is successfully deleted.");
		}else
			$objResponse->call("showme", $srv->sql);
	 }else{
			$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	 }
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteRadioServiceRequest($ref_nr)
{
	global $root_path;
	$objResponse = new xajaxResponse();
	$radio_obj = new SegRadio;

	#commented by rnel refactor notification rad via clinical area.
	//$notificationMsg = $radio_obj->getDeleteNotificationMessage($ref_nr);

	#added rnel for notification rad via clinical area.
	$dataRadDelInfo = $radio_obj->getRadInfoForBatchDeleteNotification($ref_nr);

	if ($radio_obj->deleteRefNo($ref_nr)){
		try {
            require_once($root_path . 'include/care_api_classes/emr/services/RadiologyEmrService.php');
            $radService = new RadiologyEmrService();

            $radService->deleteRadRequest($ref_nr);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }

		#added rnel rad batch deletion via clinical area notification message
		$personell_obj = new Personell;
		$personnel = $personell_obj->get_Person_name2($_SESSION['sess_login_personell_nr']);

		$data = array();
		$radInfo = array();

		foreach ($dataRadDelInfo as $datum) {
			# code...
			$radInfo['ordername'] = $datum['ordername'];
			$radInfo['items'][] = $datum['service_code'];
		}

		$data = array(
			'pname' => $radInfo['ordername'],
			'items' => $radInfo['items'],
			'personnel' => $personnel['name_first'] . ' ' . $personnel['name_last']
		);

		#publish data
		$radio_obj->notifRadMessageBulkDeletion($data);
		#end rnel

        //for delete notification
        // Yii::app()->messagequeu->publish(
        //     'rad_dept', 
        //     array(
        //         'event' => 'Delete Order', 
        //         'message' => $notificationMsg
        //     )
        // );

		$objResponse->alert("The request is successfully deleted.");
	}else{
		$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteOrder($refno)
{
	global $db, $root_path;
	$objResponse = new xajaxResponse();
	$oclass = new SegOrder();
	if ($oclass->deleteOrder($refno)) {
		try {
            require_once($root_path . 'include/care_api_classes/emr/services/PharmacyEmrService.php');
            $pharmaService = new PharmacyEmrService();

            $pharmaService->deletePharmaRequest($refno);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }

		$objResponse->alert("The request is successfully deleted.");
	}
	else {
		$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteMiscRequest($refno)
{
	global $db, $root_path;
	$objResponse = new xajaxResponse();
	$misc_obj = new SegOR_MiscCharges();
	if($saveok=$misc_obj->deleteMiscOrder($refno))
	{
		try {
            require_once($root_path . 'include/care_api_classes/emr/services/MiscellaneousEmrService.php');
            $miscService = new MiscellaneousEmrService();

            $miscService->deleteMiscRequest($refno);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }

		$objResponse->alert("Miscellenous order successfully deleted.");
	}else {
		$objResponse->alert("Miscellenous order not successfully deleted.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function computeTotalPayment($pid, $encounter_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='LB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `lab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='LB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `lab_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `iclab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `iclab_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='BB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `bb_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='BB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `bb_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='SPL' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='SPL' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_cash_ic`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_charge_ic`,
	(SELECT SUM( ((pocd.unit_price*pocd.quantity) - IFNULL(poch.discount, 0))/pocd.quantity )
		FROM seg_poc_order_detail AS pocd
		INNER JOIN seg_poc_order AS poch ON pocd.refno=poch.refno
		WHERE poch.pid='$pid' AND poch.is_cash=0 AND DATE(poch.order_dt)=DATE(NOW()) AND poch.order_type = 'START') AS `poc_total_charge`,
	(SELECT SUM( ((pocd.unit_price*pocd.quantity) - IFNULL(poch.discount, 0))/pocd.quantity )
		FROM seg_poc_order_detail AS pocd
		INNER JOIN seg_poc_order AS poch ON pocd.refno=poch.refno
		WHERE poch.pid='$pid' AND poch.is_cash=1 AND DATE(poch.order_dt)=DATE(NOW()) AND poch.order_type = 'START') AS `poc_total_cash`,		                    
	(SELECT SUM(rd.price_cash)
		FROM care_test_request_radio AS rd
		INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
		WHERE r.pid='$pid' AND r.is_cash=1 AND DATE(r.request_date)=DATE(NOW()) AND r.status<>'deleted') AS `radio_total_cash`,
	(SELECT SUM(rd.price_cash)
		FROM care_test_request_radio AS rd
		INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
		WHERE r.pid='$pid' AND r.is_cash=0 AND DATE(r.request_date)=DATE(NOW()) AND r.status<>'deleted') AS `radio_total_charge`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=1 AND p.pharma_area='IP' AND DATE(p.orderdate)=DATE(NOW()) ) AS `ip_total_cash`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=0 AND p.pharma_area='IP' AND DATE(p.orderdate)=DATE(NOW()) ) AS `ip_total_charge`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=1 AND p.pharma_area='MG' AND DATE(p.orderdate)=DATE(NOW()) ) AS `mg_total_cash`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=0 AND p.pharma_area='MG' AND DATE(p.orderdate)=DATE(NOW()) ) AS `mg_total_charge`,
	(SELECT SUM(md.adjusted_amnt)
		FROM seg_misc_service_details AS md
		INNER JOIN seg_misc_service AS m ON m.refno=md.refno
		WHERE m.encounter_nr='$encounter_nr' AND m.is_cash=1 AND DATE(m.chrge_dte)=DATE(NOW()) ) AS `misc_total_cash`,
	(SELECT IF((SELECT encounter_type FROM care_encounter WHERE encounter_nr = '$encounter_nr') = '6',
	            SUM(md.adjusted_amnt),
	            SUM(md.chrg_amnt*md.quantity)
	          )
		FROM seg_misc_service_details AS md
		INNER JOIN seg_misc_service AS m ON m.refno=md.refno
		WHERE m.encounter_nr='$encounter_nr' AND m.is_cash=0 AND DATE(m.chrge_dte)=DATE(NOW()) AND md.is_deleted != 1) AS `misc_total_charge` ";
	//$objResponse->alert($sql);
	$result = $db->Execute($sql);
	$data = $result->FetchRow();

	$total_cash = parseFloatEx($data["lab_total_cash"]+$data["iclab_total_cash"]+$data["bb_total_cash"]+$data["splab_total_cash"]+$data["splab_total_cash_ic"]+$data["poc_total_cash"]+$data["radio_total_cash"]+$data["ip_total_cash"]+$data["mg_total_cash"]+$data["misc_total_cash"]);
	$total_charge = parseFloatEx($data["lab_total_charge"]+$data["iclab_total_charge"]+$data["bb_total_charge"]+$data["splab_total_charge"]+$data["splab_total_charge_ic"]+$data["poc_total_charge"]+$data["radio_total_charge"]+$data["ip_total_charge"]+$data["mg_total_charge"]+$data["misc_total_charge"]);

	$objResponse->assign("overall-total-cash", "innerHTML", number_format($total_cash,2));
	$objResponse->assign("overall-total-charge", "innerHTML", number_format($total_charge,2));

	return $objResponse;
}

/*
* Creted by Jarel
* Created on 11/10/2013
* Ajax function for auto tagging of patient, 
*/
function autoTagging($enc,$doc_nr,$or_no)
{
	global $db,$HTTP_SESSION_VARS;
	$objResponse = new xajaxResponse();
	$userid=$HTTP_SESSION_VARS['sess_user_name'];

    $fldarray = array('encounter_nr' => $db->qstr($enc),
    		'doctor_nr'    => $db->qstr($doc_nr),
            'or_no'  => $db->qstr($or_no),
            'modify_id' => $db->qstr($doc_nr),
            'create_id'    => $db->qstr($doc_nr),
            'create_time'    => 'NOW()',
            'history'    => "CONCAT('Create: ',NOW(),' [$userid]\\n')",
            'is_deleted' => '0'
           );

    $bsuccess = $db->Replace('seg_doctors_co_manage', $fldarray, array('encounter_nr', 'doctor_nr'));

	return $objResponse;
}

$xajax->processRequest();
