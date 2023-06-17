<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
include_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
require_once($root_path.'include/care_api_classes/class_walkin.php');

class SegOrder extends Core {

	var $target;
	var $order_tb 		= 'seg_pharma_orders';
	var $items_tb 		= 'seg_pharma_order_items';
	var $discounts_tb = 'seg_pharma_order_discounts';
	var $prod_tb 			= "care_pharma_products_main";
	var $seg_discounts_tb = "seg_discounts";
	var $person_tb 		= "care_person";
	var $walkin_tb 		= "seg_walkin";
	var $appCov_tb      = "seg_applied_coverage";
	var $dosage_tb = "seg_phil_medicine_strength";
	var $frequency_tb = "seg_phil_frequency";
	var $routes_tb = "seg_phil_routes";
	var $items_cf4_tb = "seg_pharma_items_cf4";
	var $array_inv_items      = array();
	var $serve_inv_items      = array();
	var $errorMsg = "";
    var $new_charge = "S";
    var $new_notcharge = "N";
    var $new_chargeType = 'charge';

	var $fld_pharma_order;

	function SegOrder() {
		global $db;

		$this->coretable = $this->order_tb;
		$this->setTable($this->coretable);
		$this->fld_pharma_order = $db->MetaColumnNames($this->order_tb);
		$this->setRefArray($this->fld_pharma_order);
	}

	function setTarget($target) {
	}

	function processPharmaTransaction($post){
		global $db;
		//First Step: Creating the Pharmacy Order Header
		$is_locked = $db->GetOne("SELECT IF(IS_USED_LOCK('saving_refno') IS NULL, FALSE , TRUE )");
		if (!$is_locked){
			$db->GetOne("SELECT GET_LOCK('saving_refno',5)");
			$db->StartTrans();
			$charge_serve = $post['iscash']?'N':'S';
			if($post["items"]){
				$bulk = array();
				$orig = $post['iscash'] ? $post['pcash'] :  $post['pcharge'];
				$total = 0;
				foreach ($post["items"] as $i=>$v) {
					if($post["flag"][$i]!=1){
						$consigned = in_array($v, $post['consigned']) ? '1' : '0';
						$bulk[] = array(
							$post["items"][$i],
							$post["qty"][$i],
							$post["qty"][$i],
							parseFloatEx($post["prc"][$i]),
							parseFloatEx($post["prc"][$i]),
							$consigned,
							$orig[$i], 
							$post['itemArea'][$i],
							$post['is_override'][$i],
			                $post['is_fs'][$i],
			                $charge_serve);
						$total += (parseFloatEx($post["prc"][$i]) * (float) $post["qty"][$i]);
					}
				}
				
				$data = array(
					'refno'=>$this->getLastNr(date("Y-m-d")),
					'encounter_nr'=>$post['encounter_nr'],
					'pharma_area'=>$post['area2'] ? strtoupper($post['area2']) : null,
					'request_source'=>$post['source_req'],
					'ordername'=>$post['ordername'],
					'orderaddress'=>$post['orderaddress'],
					'orderdate'=>$post['orderdate'],
					'charge_type'=>$post['charge_type'],
					'is_cash'=>$post['iscash'],
					'serve_status'=>$charge_serve,
					'amount_due'=>$total,
					'is_tpl'=>$post['is_tpl'],
					'discount'=>(($post['discount']) ? $post['discount'] : '0'),
					'discountid'=>$post['discountid'],
					'is_urgent'=>$post['priority'],
					'comments'=>$post['comments'],
					'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
					'create_id'=>$_SESSION['sess_temp_userid'],
					'modify_id'=>$_SESSION['sess_temp_userid'],
					'modify_time'=>date('YmdHis'),
					'create_time'=>date('YmdHis')
				);
				if ($post['issc']) $data["is_sc"] = 1;
				if ($post["pid"]){
					if (substr($post["pid"],0,1)=='W') {
						$data["walkin_pid"] = substr($post["pid"],1,strlen($post["pid"]));
						$data["pid"] = NULL;
					}
					else {
						$data["pid"] = $post["pid"];
						$data["walkin_pid"] = NULL;
					}
					$saveok = TRUE;
				} else {
					
					$wc = new SegWalkin();
					$walkin_data = array(
						'pid' => $wc->createPID(),
						'address' => $post['orderaddress'],
						'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
						'create_id'=>$_SESSION['sess_temp_userid'],
						'modify_id'=>$_SESSION['sess_temp_userid'],
						'modify_time'=>date('YmdHis'),
						'create_time'=>date('YmdHis')
					);
					$data['pid'] = NULL;
					$data['walkin_pid'] = $walkin_data['pid'];
					$name_arr = explode(',',$post['ordername']);
					if (trim($name_arr[0]))	$walkin_data['name_last'] = trim($name_arr[0]);
					if (trim($name_arr[1]))	$walkin_data['name_first'] = trim($name_arr[1]);
					$wc->setDataArray($walkin_data);
					$this->errorMsg = 'Unable to save walkin data...';
					$saveok = $wc->insertDataFromInternalArray();
				}
			}

			if ($_SESSION['sess_temp_userid'] == 'medocs')	$this->logger->debug('REFNO:'.$data['refno']);
			if ($saveok && !empty($bulk)) {
				$this->errorMsg = 'Unable to save request data...';
				$this->setDataArray($data);
				if($_SESSION['sess_temp_userid'] == 'medocs') $this->logger->info('Saving header...');
				$saveok = $this->insertDataFromInternalArray();
				if($_SESSION['sess_temp_userid'] == 'medocs') $this->logger->debug('Result:'.var_export($saveok, true));
			}
			if($saveok){
				$db->CompleteTrans();
			}else{
				$db->FailTrans();
				$db->CompleteTrans();
			}
			$db->GetOne("SELECT RELEASE_LOCK('saving_refno')");
		}
				$sectionDocPersonellNumber = $post['requestDoc'];
		        $sectionDoctors = $post['requestDocName'];
		        $sectionDept = $post['requestDept'];
		        $sectionClinicInfo = $post['clinicInfo'];
		        $ppid = $post['pid'];

		        $itemNames = $post['items'];
		        $itemPCharge = $post['pcharge'];
		        $itemPCash = $post['pcash'];
		        $itemQty = $post['qty'];
		        $itemDiscount1 = (($post['discount']) ? $post['discount'] : '0');
		        $itemDiscount2 = (($post['discount2']) ? $post['discount2'] : '0');
		        $sectionItems = array();
		        $itemCode = $post['items'];


			    $dr_nr = $db->GetOne("SELECT ce.`consulting_dr_nr` FROM `care_encounter` ce
								WHERE ce.`pid` =". $db->qstr($ppid));

		                for($i=0; $i<count($itemNames); $i++){
		                	$itemNames[$i] = $db->GetOne("SELECT artikelname FROM `care_pharma_products_main`
												WHERE `bestellnum`=$itemCode[$i]");
		                	$drugCode[$i] = $db->GetOne("SELECT drug_code FROM `care_pharma_products_main`
												WHERE `bestellnum`=$itemCode[$i]");
		                	$prodClass[$i] = $db->GetOne("SELECT prod_class FROM `care_pharma_products_main`
												WHERE `bestellnum`=$itemCode[$i]");
		                    	$sections = array(
			                    	"service_id"	=>	$service_id[$i],
			                    	"personnel_nr"	=>	$_SESSION["sess_login_personell_nr"],
			                    	"itemNames"	=>	$itemNames[$i],
			                    	"item_id"	=>	$itemCode[$i],
			                    	"itemPCharge"	=> $itemPCharge[$i],
			                    	"prodClass" => $prodClass[$i],
			                    	"itemDiscount1"	=> $itemDiscount1[$i],
			                    	"itemDiscount2"	=> $itemDiscount2[$i],
			                    	"itemQty"	=>	$itemQty[$i],
			                    	"drug_code"	=> 	$drugCode[$i]
		                    	);
		                    	array_push($sectionItems,$sections);
		                    }

		                    $pharmaReq = array(
								"pid"				=>	$post['pid'],
								"encounter_nr"		=>	$post['encounter_nr'],
								"transactionType"	=>	array(
										"type"		=> 	$post['iscash'],
										"grant_type"=>	$post['charge_type']
									),
								"priority"			=>	$post['priority'],
								"comments"			=>	$post['comments'],
								"charge_type"		=>	$post['charge_type'],
								"dstamp"			=>	$post['dstamp'],
								"hasPHIC"			=>	$post['hasPHIC'],
								'discountid'		=>	$post['discountid'],
								"order"				=>	array(
									array(
										"refno"		=>	$post['refno'],
										"encoder"	=>	$_SESSION['sess_temp_userid'],
										"sections"	=>	array(
											$sectionItems
										)
									)

								)
		                    );
		        
		        $ehr = Ehr::instance();
		        $patient = $ehr->postPharmaRequest($pharmaReq);
		        $asd = $ehr->getResponseData();
		        $EHRstatus = $patient->status;
		        if(!$EHRstatus){
		        // echo "<pre>";
		        //     var_dump($pharmaReq);
		        //     var_dump($asd);
		        //     var_dump($patient);
		            // die();
		        }
		//Second Step: Adding details to the Pharmacy Order Header
		if($saveok && !empty($bulk)){
			$db->StartTrans();
			$saving_to_order_items=true;
			try{

				$this->errorMsg = 'Unable to clear request details...';
				if ($_SESSION['sess_temp_userid'] == 'medocs') $this->logger->info('Clearing details...');
				$saveok = $this->clearOrderList($data['refno']);
				if ($_SESSION['sess_temp_userid'] == 'medocs') $this->logger->debug('Result:'.var_export($saveok, true));

				if($saveok && !empty($bulk)){
					$this->errorMsg = 'Unable to save request details...';
					if ($_SESSION['sess_temp_userid'] == 'medocs') $this->logger->info('Saving details...');
					$saveok = $this->addOrders($data['refno'],$bulk);
					
					$medicineItem = "M";
					

					foreach ($post['items'] as $key => $value) {
						if(($db->GetOne("SELECT prod_class FROM `care_pharma_products_main`
												WHERE `bestellnum`=".$db->qstr($value)))==$medicineItem){
							$sql = "INSERT INTO seg_pharma_items_cf4(refno,bestellnum,dosage,route,frequency,history,create_id,create_dt)VALUES('". $data['refno'] . "','" . $value . "','" . $post["dosage"][$key] . "','".$post["route"][$key]."','".$post["frequency"][$key]."','','". $_SESSION['sess_temp_userid'] ."','". date('YmdHis'). "')";
							$db->Execute($sql);
							
						}
					}

					if ($_SESSION['sess_temp_userid'] == 'medocs') $this->logger->debug('Result:'.var_export($saveok, true));
				}
			}catch(Exception $except){
				echo 'Caught exception: '.  $e->getMessage() . "<br/>";
				$saving_to_order_items=false;
			}
			if($saveok && $saving_to_order_items){
				$db->CompleteTrans();
				// if(!empty($this->array_inv_items)){
				// 	foreach($this->array_inv_items as $key_inv => $val_inv){
				// 		$this->AutoServer($val_inv['refno'],$val_inv['item'],$val_inv['pharma_area']);
				// 	}
				// }
			}else{
				$db->FailTrans();
				$db->CompleteTrans();
				$saveok=false;
				$this->deleteOrder($data['refno']);
			}
		}
		
		#Added by Jarel 04/22/2014 Do Saving Misc Request
		if($post["items"]) {
			$seg_ormisc = new SegOR_MiscCharges();
			$saveok_cnt = 0;
		 	$no_items = 0;
			//start saving miscellaneous
			foreach($post["items"] as $i=>$item)
			{
				if($post["flag"][$i]==1){
					if($post["misc_request_flag"][$i]){
						$flag = $post["misc_request_flag"][$i];
					}
					$miscItems[] = $post["items"][$i];
					$miscQty[] = $post["qty"][$i];
					$miscPrc[] = $post["pcharge"][$i];
		            $miscAdj[] = $post["pcharge"][$i];
					$miscType[] = $post["misc_account_type"][$i];
		            $miscClinic[] = $post["misc_clinicInfo"][$i];
		            $miscFlag[] = $flag;
				}
			}
			$refno = $seg_ormisc->getMiscRefno(date('Y-m-d H:i:s'));
		    $array = array(
				'refno' => $refno,
				'charge_date' => $post['orderdate'],
				'encounter_nr' => $post['encounter_nr'],
		    	'pid' => $post['pid'],
				'misc' => $miscItems,
		    	'discountid' => $post['discountid'],
		    	'discount' =>  (($post['discount']) ? $post['discount'] : '0'),
				'quantity' => $miscQty,
		    	'adj_amnt' => $miscAdj,
				'price' => $miscPrc,
				'request_flag' => NULL,//$miscFlag,
				'account_type' => $miscType,
				'is_cash' => $post['iscash'],
		    	'clinical_info' => $miscClinic,
				'area' => "opd"
			); //edit
		    if(!empty($miscItems)){
				$saveok = $seg_ormisc->saveMiscCharges($array);
		    }
		}
		if($saveok)	return $data['refno'];
		else return false;
	}

	function updatePharmaTransaction($post){
		
		global $db;
		$db->StartTrans();
		$bulk = array();
		$orig = $post['iscash'] ? $post['pcash'] :  $post['pcharge'];
		$total = 0;
        $charge_serve = $post['isNewCash'] == $this->new_chargeType ? $this->new_charge : $this->new_notcharge;

		foreach ($post["items"] as $i=>$v) {
			$consigned = in_array($v."_".$post['itemArea'][$i], $post['consigned']) ? '1' : '0';
			// var_dump($consigned);
			$bulk[] = array(
				$post["items"][$i],
				$post["qty"][$i],
				$post["qty"][$i],
				parseFloatEx($post["prc"][$i]),
				parseFloatEx($post["prc"][$i]),
				$consigned, 
				$orig[$i], 
				$post['itemArea'][$i],
				$post['is_override'][$i],
				$post['is_fs'][$i],
				$charge_serve
				);
			$total += (parseFloatEx($post["prc"][$i]) * (float) $post["qty"][$i]);
		}
		// die;
		$data = array(
			'amount_due'=>$total,
			'discount'=>$post['discount'],
			'discountid'=>$post['discountid'],
			'is_urgent'=>$post['priority'],
			'comments'=>$post['comments'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis')
		);
		$sectionItems = array();
	    $dr_nr = $db->GetOne("SELECT ce.`consulting_dr_nr` FROM `care_encounter` ce
						WHERE ce.`pid` =". $db->qstr($post['pid']));

                for($i=0; $i<count($post["items"]); $i++){

                	$itemCode = $post["items"][$i];
                	$ref_no = $db->qstr($post['refno']);
                	  		// var_dump($itemCode);
                	$itemNames[$i] = $db->GetOne("SELECT artikelname FROM `care_pharma_products_main`
										WHERE `bestellnum`=$itemCode");
                	$drugCode[$i] = $db->GetOne("SELECT drug_code FROM `care_pharma_products_main`
										WHERE `bestellnum`=$itemCode");
                	$prodClass[$i] = $db->GetOne("SELECT prod_class FROM `care_pharma_products_main`
										WHERE `bestellnum`=$itemCode");
                	$qty[$i] = $db->GetOne("SELECT quantity FROM `seg_pharma_order_items`
										WHERE `refno`=$ref_no AND `bestellnum`=$itemCode");
          //       	$priority = $db->GetOne("SELECT is_urgent FROM `seg_pharma_orders`
										// WHERE `refno`=$ref_no");
                    	$sections = array(
	                    	"service_id"	=>	$service_id[$i],
	                    	"personnel_nr"	=>	$dr_nr,
	                    	"itemNames"	=>	$itemNames[$i],
	                    	"item_id"	=>	$itemCode,
	                    	"itemPCharge"	=> $itemPCharge[$i],
	                    	"prodClass" => $prodClass[$i],
	                    	"itemDiscount1"	=> $post['discount'][$i],
	                    	"itemDiscount2"	=> $post['discount'][$i],
	                    	"itemQty"	=>	$qty[$i],
	                    	"drug_code"	=> 	$drugCode[$i]
                    	);
                    	array_push($sectionItems,$sections);
                    }

                    $pharmaReq = array(
						"pid"				=>	$post['pid'],
						"encounter_nr"		=>	$post['encounter_nr'],
						"transactionType"	=>	array(
								"type"		=> 	"1",
								"grant_type"=>	$post['charge_type']
							),
						// "priority"			=>	$priority,
						"priority"			=>	$post['priority'],
						"comments"			=>	$post['comments'],
						"charge_type"		=>	$post['charge_type'],
						"dstamp"			=>	$post['dstamp'],
						"hasPHIC"			=>	$post['hasPHIC'],
						'discountid'		=>	$post['discountid'],
						"order"				=>	array(
							array(
								"refno"		=>	$post['refno'],
								"encoder"	=>	$_SESSION['sess_temp_userid'],
								"sections"	=>	array(
									$sectionItems
								)
							)

						)
                    );
                // echo "<pre>";
        		// var_dump($priority);
        		// var_dump($dr_nr);
                // die();	

        
		
        $ehr = Ehr::instance();
        $patient = $ehr->postPharmaRemoveRequest($pharmaReq);
        $asd = $ehr->getResponseData();
        $EHRstatus = $patient->status;
        
        // echo "<pre>";
        // var_dump($asd); 
        // var_dump($patient);
        // die();	

        if(!$EHRstatus){
        // echo "<pre>";
        //     var_dump($pharmaReq);
        //     var_dump($asd);
        //     var_dump($patient);
            // die();
        }

		if ($post['issc'])
			$data["is_sc"] = 1;
		else
			$data['is_sc'] = 0;
		
		$this->setDataArray($data);
		$this->where = "refno=".$db->qstr($post['ref']);
		$this->errorMsg = 'Unable to save request data...';
		$saveok=$this->updateDataFromInternalArray($post["ref"],FALSE);

		if ($saveok) {
			$this->errorMsg = 'Unable to clear request details...';
			$saveok = $this->clearOrderList($post["ref"]);
		}

		if ($saveok) {
			$this->errorMsg = 'Unable to update request details...';
			$saving_to_order_items = $this->addOrders($post["ref"], $bulk);

            foreach ($_POST['items'] as $key => $value) {

                $sql = "SELECT c.refno, c.history FROM seg_pharma_items_cf4 c 
                            INNER JOIN care_pharma_products_main p 
                                ON c.bestellnum = p.bestellnum 
                                WHERE c.bestellnum = ".$db->qstr($value)." 
                                AND c.refno = " . $db->qstr($_POST['refno']);
                $result_cf4 = $db->GetRow($sql);


                if($result_cf4['refno']) {
                    $history_cf4 = "Update By: ".$_SESSION['sess_temp_userid']. ' '. date('m-d-Y H:i:s')."\n".$result_cf4['history'];

                    $sql_cf4 = "UPDATE seg_pharma_items_cf4 
                                    SET dosage =".$db->qstr($post['dosage'][$key]).",
                                    frequency = ".$db->qstr($post['frequency'][$key]).",
                                    route = ".$db->qstr($post['route'][$key]).",
                                    history = ".$db->qstr($history_cf4).",
                                    modify_id = ".$db->qstr($_SESSION['sess_temp_userid']).",
                                    modify_dt = ".$db->qstr(date('Y-m-d H:i:s'))."
                                    WHERE refno = ".$db->qstr($_POST['refno'])." 
                                    AND bestellnum = ".$db->qstr($value)."";
                    $db->Execute($sql_cf4);
                } else {
                    $check_prodClass = "SELECT prod_class
                                FROM care_pharma_products_main c
                                 WHERE c.bestellnum = ".$db->qstr($value)."
                                 AND prod_class = 'M'";

                    if ($prod_c = $db->GetOne($check_prodClass)) {
                        $history_cf4 = "Create By: ".$_SESSION['sess_temp_userid']. ' '. date('m-d-Y H:i:s');

                        $sql_cf4 = "INSERT INTO seg_pharma_items_cf4 (dosage,frequency,route,refno,bestellnum,create_id,create_dt,history)
                                    VALUES(
                                        ".$db->qstr($post['dosage'][$key]).",
                                        ".$db->qstr($post['frequency'][$key]).",
                                        ".$db->qstr($post['route'][$key]).",
                                        ".$db->qstr($_POST['refno']).",
                                        ".$db->qstr($value).",
                                        ".$db->qstr($_SESSION['sess_temp_userid']).",
                                        ".$db->qstr(date('Y-m-d H:i:s')).",
                                        ".$db->qstr($history_cf4)."
                                    )";

                        $db->Execute($sql_cf4);
                    }
                }
            }
			
		}

		if($saveok && $saving_to_order_items){
			$db->CompleteTrans();
			// if(!empty($this->array_inv_items)){
			// 	foreach($this->array_inv_items as $key_inv => $val_inv){
			// 		$this->AutoServer($val_inv['refno'],$val_inv['item'],$val_inv['pharma_area']);
			// 	}
			// }
		}else{
			$db->FailTrans();
			$db->CompleteTrans();
			$saveok=false;
			$this->deleteOrder($data['refno']);
		}
	
		if($saveok)	{
			return $data['refno'];
			$this->errorMsg=null;
		}
		else return false;

	}
	
	function getLastNr($today) {
		global $db;
		$today = $db->qstr($today);
		//$this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
		$year = date('Y');
		// $this->sql = "SELECT IFNULL(CAST(MAX(refno) AS UNSIGNED)+1,'".$year."000001')\n".
		// 	"FROM seg_pharma_orders\n".
		// 	"WHERE refno LIKE " . $db->qstr($year . '%');
		$this->sql = "SELECT CAST(IFNULL(MAX(refno),YEAR(NOW())*1000000) AS UNSIGNED) + 1
			FROM seg_pharma_orders";
		return $db->GetOne($this->sql);
	}

	function deleteOrder($refno) {
		global $db;
		$ref_no = $refno;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->coretable WHERE refno=$refno";
		$ok = $db->Execute($this->sql);

		if($ok) {
			$this->sendDeleteItemEhr($ref_no);
		}

		return $this->Transact();
	}

	function sendDeleteItemEhr($refno) {

		$data = array(
			'refno' => $refno
		);

        $ehr = Ehr::instance();
        $patient = $ehr->postRemovePharmaRequestBatch($data);
        $asd = $ehr->getResponseData();
        $EHRstatus = $patient->status;	

        // echo "<pre>";
        // var_dump($refno); 
        // var_dump($patient); 
        // var_dump($asd); 
        // die();

        if(!$EHRstatus){
            // var_dump($asd);
            // var_dump($patient->msg);
        }
	}

	function clearOrderList($refno) {
		global $db;
		$this->sql = "DELETE FROM $this->items_tb WHERE refno=".$db->qstr($refno);
		return $this->Transact();
	}
	/*Edited By add field is_override,remarks MARK 2016-04-10*/
	function addOrders($refno, $orderArray) {
		global $db;       
		$db->BeginTrans();
		define('__PHIC_ID__', 18);
		$refno = $db->qstr($refno);
		$ref = $db->GetRow("SELECT encounter_nr, is_cash,
								IF(is_cash,NULL,charge_type) AS charge_type
				FROM seg_pharma_orders
				WHERE refno = " . $refno);
		$IsCash = $ref['is_cash'];
		if(!isset($orderArray[0][10])) $orderArray[0][10]='N'; //By default, serve status is 'N' or Not Served
		if($orderArray[0][10]=='S'&&!$IsCash) $orderArray[0][1]=$orderArray[0][2]; // if serve status is Served and transaction is not Cash, then item requested quantity is equal to quantity
		if($IsCash){
			$this->sql = "INSERT INTO $this->items_tb
			(refno,bestellnum,requested_qty,quantity,pricecash,pricecharge,is_consigned,price_orig, pharma_area,is_down_inv,is_fs,serve_status)
	                         VALUES
			(".$refno.",?,?,?,?,?,?,?,?,?,?,?)";
		}else{
			$this->sql = "INSERT INTO $this->items_tb
			(refno,bestellnum,requested_qty,quantity,pricecash,pricecharge,is_consigned,price_orig, pharma_area,is_down_inv,is_fs,serve_status,serve_dt,serve_id)
	                         VALUES
			(".$refno.",?,?,?,?,?,?,?,?,?,?,?,NOW(),".$db->qstr($_SESSION['sess_user_name']).")";
		}

		// var_dump($this->sql);var_dump($orderArray);die();
		if($db->Execute($this->sql,$orderArray)){
			$ref = $db->GetRow("SELECT encounter_nr, is_cash,
								IF(is_cash,NULL,charge_type) AS charge_type
				FROM seg_pharma_orders
				WHERE refno = " . $refno);
			$IsCash = $ref['is_cash'];

			$this->sql2 = "SELECT spoi.bestellnum, spoi.pricecash, spoi.pharma_area, cppm.is_in_inventory
                                FROM $this->items_tb spoi 
							  LEFT JOIN care_pharma_products_main cppm 
							    ON spoi.bestellnum =cppm.bestellnum
                                WHERE spoi.refno = ".$refno;
           	$this->item = $db->Execute($this->sql2); 
			while ($row = $this->item->FetchRow()){
	            $ItemCode = $row["bestellnum"];	
				$pricecash = $row["pricecash"];	
				$pharma_area = $row["pharma_area"];	
				$is_in_inventory = $row["is_in_inventory"];	
				if($IsCash == '0'){
                    	if($ref['charge_type'] == 'PHIC'){
						$total = $db->GetRow("SELECT pricecash*quantity AS total, serve_status 
							FROM seg_pharma_order_items
							WHERE refno = " . $refno. 
							"AND bestellnum= " . $db->qstr($ItemCode));         				   

						$cov = $db->GetRow("SELECT coverage, item_code
							FROM seg_applied_coverage
							WHERE ref_no='T{$ref['encounter_nr']}'
							AND source='M'
							AND item_code = " . $db->qstr($ItemCode) .
									"AND hcare_id=".__PHIC_ID__);

                    		if ($cov['item_code']){
                    			$coverage = parseFloatEx($cov['coverage']) + parseFloatEx($total['total']);
                    			$this->sqlCovUpdate ="UPDATE seg_applied_coverage
							SET coverage = ".$coverage.
                    								" WHERE ref_no='T{$ref['encounter_nr']}'
							AND item_code = " . $db->qstr($ItemCode);
							$db->Execute($this->sqlCovUpdate);

							// $this->pushToInventoryArray($refno, $ItemCode, $pharma_area,$is_in_inventory);
                    		}else{
                    			$this->sqlAppCovUpdate = "INSERT INTO seg_applied_coverage
                    									(ref_no,source,item_code,hcare_id,coverage)
                    									VALUE
                    									('T{$ref['encounter_nr']}','M','".$ItemCode."','".__PHIC_ID__."','".$total['total']."')";
							$db->Execute($this->sqlAppCovUpdate);
							// $this->pushToInventoryArray($refno, $ItemCode, $pharma_area,$is_in_inventory);
						}
					}
                }else{//end if($IsCash == '0')
                	if($pricecash == 0){
	                	$this->sql = "UPDATE $this->items_tb SET request_flag = 
	                	'charity' WHERE refno = " . $refno . 
	                	" AND bestellnum = " . $db->qstr($ItemCode);
	                	$db->Execute($this->sql);
                    }
              	}
        	} //end while($row = $this->item->FetchRow())
        	return true;
        } else { return false; }
	}

    function pushToInventoryArray($refno, $item, $pharma_area,$is_in_inventory){
    	if($is_in_inventory){
    		$current_array=Array(
				"refno" => $refno,
	    		"item" =>  $item,
	    		"pharma_area" => $pharma_area
			);
	    	array_push($this->array_inv_items, $current_array);
    	}
    }

    function serveToInventoryArray($refno, $item, $pharma_area,$is_in_inventory){
    	if($is_in_inventory){
    		$serve_array=Array(
				"refno" => $refno,
	    		"item" =>  $item,
	    		"pharma_area" => $pharma_area
			);
	    	array_push($this->serve_inv_items, $serve_array);
    	}
    }

    function AutoServer($refno, $item, $pharma_area){
    	global $db, $root_path;
    	require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
    	$invServiceNew = new InventoryServiceNew();

    	$ref = $db->GetRow("SELECT IF(spo.`encounter_nr` = '' OR spo.`encounter_nr` IS NULL, 'WALKIN', spo.`encounter_nr`) AS encounter_nr,
    		spoi.`quantity`, IF(spo.`pid` IS NULL, sw.`pid`, spo.`pid`) AS pid, spo.`ordername`,
    		IF(spo.`is_cash`,NULL,spo.`charge_type`) AS charge_type,spo.`refno`,
    		spoi.`pricecash`, cppm.`item_code`, cppm.`barcode`, cppm.`is_in_inventory`,
    		IF(spo.`pid` IS NULL, sw.`name_last`, cp.`name_last`) AS name_last,
    		IF(spo.`pid` IS NULL, IF(sw.`name_first` = '', ' ', sw.`name_first`), cp.`name_first`) AS name_first
    		FROM seg_pharma_orders spo\n".
    		"LEFT JOIN seg_pharma_order_items spoi ON spoi.`refno` = spo.`refno`\n".
    		"LEFT JOIN care_pharma_products_main cppm ON cppm.`bestellnum` = spoi.`bestellnum`\n".
    		"LEFT JOIN care_person cp ON cp.`pid` = spo.`pid`\n".
    		"LEFT JOIN seg_walkin sw ON sw.`pid` = spo.`walkin_pid`\n".
    		"WHERE spo.`refno`=".$refno." AND spoi.`bestellnum` = ".$db->qstr($item).
    		" AND spoi.inv_uid IS NULL AND spoi.`pharma_area` = ".$db->qstr($pharma_area));
        //for inventory
    	try {
            // $sendArr = array(
            //     'item_code' => $ref['item_code'],
            //     'barcode' => $ref['barcode'],
            //     'quantity' => $ref['quantity'],
            //     'hnumber' => $ref['pid'],
            //     'cnumber' => $ref['encounter_nr'],
            //     'fname' => $ref['name_first'],
            //     'lname' => $ref['name_last']
            // );

    		$inv_uid = null;
    		$is_down_inv = null;
    		if($ref['is_in_inventory'] == 1){
    			$INV_UID ="";
    			$INV_DOWN ="";
    			$dataFromHIS = array( '&item_code'=>$ref['item_code'],
    				'&barcode'=>$ref['barcode'],
    				'&quantity'=>$ref['quantity'],
    				'&hnumber'=>$ref['pid'],
    				'&cnumber'=>$ref['encounter_nr'],
    				'&fname'=>$ref['name_first'],
    				'&lname'=>$ref['name_last']."-RefNo.(".$ref['refno'].")");
    			$trans_result= $invServiceNew->transactItemToDai($dataFromHIS,'item_transact',$pharma_area);
    			if ($trans_result != "Failed" || !empty($trans_result)) {
    				if ($trans_result =="AILED") {
    					$trans_result ="FAILED";
    				}
    				$inv_uid = $db->qstr($trans_result);
    				$is_down_inv = $db->qstr("0");
    				$INV_UID =$trans_result;
    				$INV_DOWN ="0";
                 } else {                         
    				if ($trans_result =="AILED") {
    					$trans_result ="FAILED";
                 }
    				$inv_uid  = $db->qstr($trans_result);
    				$is_down_inv = $db->qstr("1");
    				$INV_UID =$trans_result;
    				$INV_DOWN ="1";
        	}
    			unset($dataINV);
    			$dataINV = array("refno"=>$ref['refno'],
    				"bestellnum"=>$item,
    				"dispense_qty" =>$ref['quantity'],
    				"inv_uid" =>$INV_UID,
    				"is_down_inv" =>$INV_DOWN,
    				"ip_source" =>$invServiceNew->getIPsource(),
    				"uri_source" =>$invServiceNew->RequestURI(),
    				"pid"=>$ref['pid'],
    				"encounter_nr"=>$ref['encounter_nr'],
    				"fname"=>$ref['name_first'],
    				"lname"=>$ref['name_last'],
    				"serve_id" =>$_SESSION['sess_user_name'],
    				"url"=>'&item_code='.$dataFromHIS['&item_code'].
                            '&barcode='.$dataFromHIS['&barcode'].
                            '&quantity='.$dataFromHIS['&quantity'].
                            '&hnumber='.$dataFromHIS['&hnumber'].
                            '&cnumber='.$dataFromHIS['&cnumber'].
                            '&fname='.str_replace(' ', '-',$dataFromHIS['&fname']).
                            '&lname='.str_replace(' ', '-',$dataFromHIS['&lname'])
                            );
    			$this->insertInvUID(serialize($dataINV));
                unset($dataINV);


        	}
                // echo $inv_uid ;

            // $res = $invService->transactItem($pharma_area, $sendArr);
            // $inv_uid = null;
            // if($res){
            //     $start = stripos($res, "[") + 1;

            //     if($start !== false){
            //         $length = stripos($res, "]") - $start;
            //         $uid = substr($res, $start, $length);

            //         $inv_uid = $db->qstr($uid);
            //     }
            // }
    	} catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
		}

    	if(!empty($inv_uid)){
                    			$this->sqlupdate = "UPDATE $this->items_tb 
                                        SET serve_status = 'S',
    		serve_dt = NOW(),".
    		"is_down_inv = ".$is_down_inv.",".
    		"inv_uid = ".$inv_uid.
    		" WHERE refno = " . $db->qstr($refno) . " AND bestellnum = " . $db->qstr($item).
    		" AND pharma_area = ".$db->qstr($pharma_area);
    	}else{
    		$this->sqlupdate = "UPDATE $this->items_tb 
    		SET serve_status = 'S',
    		serve_dt = NOW()".
    		" WHERE refno = " . $db->qstr($refno) . " AND bestellnum = " . $db->qstr($item).
    		" AND pharma_area = ".$db->qstr($pharma_area);
		}
    	$db->Execute($this->sqlupdate);

                    			$this->sqlupdate2 = "UPDATE $this->order_tb
                                        SET serve_status = 'S'
    	WHERE refno = " . $db->qstr($refno);
    	$db->Execute($this->sqlupdate2);
	}



	 function ReferenceAutoServer($refno){
    	global $db, $root_path;
    	require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
    	$invServiceNew = new InventoryServiceNew();
    	$transacted_items = array();
    	$time_diff = 0;
    	
    	$ref = "SELECT IF(spo.`encounter_nr` = '' OR spo.`encounter_nr` IS NULL, 'WALKIN', spo.`encounter_nr`) AS encounter_nr,
    		spoi.`bestellnum`,spoi.`quantity`,spoi.`requested_qty`, IF(spo.`pid` IS NULL, sw.`pid`, spo.`pid`) AS pid, spo.`ordername`,
    		spoi.`pharma_area`, IF(spo.`is_cash`,NULL,spo.`charge_type`) AS charge_type,spo.`refno`,
    		spoi.`pricecash`, cppm.`item_code`, cppm.`barcode`, cppm.`is_in_inventory`,cppm.`artikelname`,
    		IF(spo.`pid` IS NULL, sw.`name_last`, cp.`name_last`) AS name_last,
    		IF(spo.`pid` IS NULL, IF(sw.`name_first` = '', ' ', sw.`name_first`), cp.`name_first`) AS name_first
    		FROM seg_pharma_orders spo\n".
    		"LEFT JOIN seg_pharma_order_items spoi ON spoi.`refno` = spo.`refno`\n".
    		"LEFT JOIN care_pharma_products_main cppm ON cppm.`bestellnum` = spoi.`bestellnum`\n".
    		"LEFT JOIN care_person cp ON cp.`pid` = spo.`pid`\n".
    		"LEFT JOIN seg_walkin sw ON sw.`pid` = spo.`walkin_pid`\n".
    		"LEFT JOIN seg_pay_request spr ON spr.`ref_no` = spo.`refno` AND spr.ref_source='PH' AND spoi.bestellnum=spr.service_code\n".
    		"LEFT JOIN seg_pay sp ON spr.`or_no` = sp.`or_no`\n".
    		"WHERE spo.`refno`=".$db->qstr($refno)." AND spoi.inv_uid IS NULL AND cppm.is_in_inventory='1' AND IF(spo.`is_cash`,sp.or_no IS NOT NULL AND request_flag='paid' AND spoi.serve_status='S',TRUE) LIMIT 1";
    		
    		// die($ref);
        //for inventory
    	try {
            // $sendArr = array(
            //     'item_code' => $ref['item_code'],
            //     'barcode' => $ref['barcode'],
            //     'quantity' => $ref['quantity'],
            //     'hnumber' => $ref['pid'],
            //     'cnumber' => $ref['encounter_nr'],
            //     'fname' => $ref['name_first'],
            //     'lname' => $ref['name_last']
            // );

    		if ($result = $db->Execute($ref)){
	            if ($result->RecordCount()) {
	                while ($ref = $result->FetchRow()) {
	                	
	     //            	$ref_item_is_locked = $db->GetOne("SELECT IF(IS_USED_LOCK('serving_item_from_refno') IS NULL, FALSE , TRUE )");
						// if (!$ref_item_is_locked){
						// 	$time_first = strtotime("now");
						// 	$db->GetOne("SELECT GET_LOCK('serving_item_from_refno',-1)");
		                    $inv_uid = null;
				    		$is_down_inv = null;
				    		if($ref['is_in_inventory'] == 1){
				    			$INV_UID ="";
				    			$INV_DOWN ="";
				    			$dataFromHIS = array( '&item_code'=>$ref['item_code'],
				    				'&barcode'=>$ref['barcode'],
				    				'&quantity'=>$ref['quantity'],
				    				'&hnumber'=>$ref['pid'],
				    				'&cnumber'=>$ref['encounter_nr'],
				    				'&fname'=>$ref['name_first'],
				    				'&lname'=>$ref['name_last']."-RefNo.(".$ref['refno'].")");
				    			
				    			$trans_result= $invServiceNew->transactItemToDai($dataFromHIS,'item_transact',$ref['pharma_area']);
				    			$orig_trans_result=$trans_result;
				    			
				    			if ($trans_result =="AILED" || $trans_result == "Failed" || empty($trans_result)) {
				    				if ($trans_result =="AILED" || $trans_result =="Failed") {
				    					$trans_result ="FAILED";
				                 	}
				    				$inv_uid  = $db->qstr($trans_result);
				    				$is_down_inv = $db->qstr("1");
				    				$INV_UID =$trans_result;
				    				$INV_DOWN ="1";
				                 } else {
				    				$inv_uid = $db->qstr($trans_result);
				    				$is_down_inv = $db->qstr("0");
				    				$INV_UID =$trans_result;
				    				$INV_DOWN ="0";                
				    				
                    			}
				    			unset($dataINV);
				    			$dataINV = array("refno"=>$ref['refno'],
				    				"bestellnum"=>$ref['bestellnum'],
				    				"dispense_qty" =>$ref['quantity'],
				    				"inv_uid" =>$INV_UID,
				    				"is_down_inv" =>$INV_DOWN,
				    				"ip_source" =>$invServiceNew->getIPsource(),
				    				"uri_source" =>$invServiceNew->RequestURI(),
				    				"pid"=>$ref['pid'],
				    				"encounter_nr"=>$ref['encounter_nr'],
				    				"fname"=>$ref['name_first'],
				    				"lname"=>$ref['name_last'],
				    				"serve_id" =>$_SESSION['sess_user_name'],
				    				"trans_result" =>$orig_trans_result,
				    				"url"=>'&item_code='.$dataFromHIS['&item_code'].
				                            '&barcode='.$dataFromHIS['&barcode'].
				                            '&quantity='.$dataFromHIS['&quantity'].
				                            '&hnumber='.$dataFromHIS['&hnumber'].
				                            '&cnumber='.$dataFromHIS['&cnumber'].
				                            '&fname='.str_replace(' ', '-',$dataFromHIS['&fname']).
				                            '&lname='.str_replace(' ', '-',$dataFromHIS['&lname'])
				                            );
				    			$this->insertInvUID(serialize($dataINV));
				                unset($dataINV);


                    		}

				        	if(!empty($inv_uid)){
				        		if($INV_UID=="FAILED" && !(isset($ref['charge_type']) && $orig_trans_result===0)){
				        			$this->sqlupdate = "UPDATE $this->items_tb 
						    		SET serve_status = 'N',
						    		quantity = ". $db->qstr($ref['requested_qty']).",
						    		serve_dt = NULL,".
						    		"is_down_inv = ".$is_down_inv.",".
						    		"inv_uid = ".$inv_uid.
						    		" WHERE refno = " . $db->qstr($refno) . " AND bestellnum = " . $db->qstr($ref['bestellnum']).
						    		" AND pharma_area = ".$db->qstr($ref['pharma_area']);
				        		}else{
				        			$this->sqlupdate = "UPDATE $this->items_tb 
						    		SET serve_status = 'S',
						    		serve_dt = NOW(),".
						    		"is_down_inv = ".$is_down_inv.",".
						    		"inv_uid = ".$inv_uid.
						    		" WHERE refno = " . $db->qstr($refno) . " AND bestellnum = " . $db->qstr($ref['bestellnum']).
						    		" AND pharma_area = ".$db->qstr($ref['pharma_area']);
                    	}
					    	}else{
                    		$this->sqlupdate = "UPDATE $this->items_tb 
                                        SET serve_status = 'S',
					    		serve_dt = NOW()".
					    		" WHERE refno = " . $db->qstr($refno) . " AND bestellnum = " . $db->qstr($ref['bestellnum']).
					    		" AND pharma_area = ".$db->qstr($ref['pharma_area']);
                    	}
							
					    	$db->Execute($this->sqlupdate);

                    		
							array_push($transacted_items,$ref['bestellnum'],$ref['artikelname'],$trans_result,$time_diff);
					    	// $db->GetOne("SELECT RELEASE_LOCK('serving_item_from_refno')");
         //            	}
					    // else {
					    // 	// $db->GetOne("SELECT RELEASE_LOCK('serving_item_from_refno')");
					    // 	return "LOCKED";
		       //          }
							    
                 }
        	}
        	}
    		
                // echo $inv_uid ;

            // $res = $invService->transactItem($pharma_area, $sendArr);
            // $inv_uid = null;
            // if($res){
            //     $start = stripos($res, "[") + 1;

            //     if($start !== false){
            //         $length = stripos($res, "]") - $start;
            //         $uid = substr($res, $start, $length);

            //         $inv_uid = $db->qstr($uid);
            //     }
            // }
    	} catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
	}
		return $transacted_items;
	
	}


	function grantPharmacyRequest($refno, $items) {
		global $db;
		if (!is_array($items)) return false;
		if (empty($arrayItems))
			return TRUE;
		$this->sql="INSERT INTO seg_granted_request (ref_no, ref_source, service_code) VALUES ($db->qstr($refno), 'PH', ?)";
		if ($db->Execute($this->sql,array($items))) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}

	function clearDiscounts($refno) {
		global $db;
		$this->sql = "DELETE FROM $this->discounts_tb WHERE refno=".$db->qstr($refno);
		return $this->Transact();
	}

	function getOrderInfo($refno) {
		global $db;
		$this->sql="SELECT o.*,\n".
//				"IFNULL(p.name_last,w.name_last) AS name_last,".
//				"IFNULL(p.name_first,w.name_first) AS name_first,".
//				"IFNULL(p.name_middle,'') AS name_middle,\n".
			"ce.encounter_type,ce.current_room_nr AS `current_room`,ce.current_ward_nr AS `current_ward`,cw.ward_id AS `wardname`,ce.er_location AS erloc, ce.er_location_lobby AS erloclob,ce.current_dept_nr AS curdept,\n".
			"a.area_name\n".
			"FROM $this->coretable AS o\n".
			"LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
			"LEFT JOIN $this->walkin_tb AS w ON w.pid=o.walkin_pid\n".
			"LEFT JOIN care_encounter AS ce  ON o.`encounter_nr` = ce.`encounter_nr`\n".
			"LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
			"  LEFT JOIN care_ward AS cw
         	ON ce.`current_ward_nr` = cw.`nr`\n".
         	"LEFT JOIN care_department AS cd ON cd.nr = ce.current_dept_nr\n".
			"WHERE o.refno=".$db->qstr($refno);
#echo $this->sql;
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getOrderDiscounts($refno) {
		global $db;
		$this->sql="SELECT discountid\n".
				"FROM $this->discounts_tb\n".
				"WHERE refno=".$db->qstr($refno);
		if($this->result=$db->Execute($this->sql)) {
			$ret = array();
			while ($row = $this->result->FetchRow())
				$ret[$row['discountid']] = $row['discountid'];
			return $ret;
		} else { return false; }
	}
	function hasPaidOrder($refno) {
		global $db;
		$this->sql="SELECT bestellnum\n".
				"FROM $this->items_tb\n".
				"WHERE refno=".$db->qstr($refno).
				"AND request_flag IN ('paid','credit') LIMIT 1";
		if($this->result=$db->Execute($this->sql)) {
			$ret = array();
			while ($row = $this->result->FetchRow())
				$ret[$row['bestellnum']] = $row['bestellnum'];
			return $ret;
		} else { return false; }
	}

	function getPersonInfoFromEncounter($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql= "
    	SELECT ps.nr AS personnelID, sri.rid, enc.encounter_nr, cp.senior_ID, cp.fromtemp, cp.pid,cp.name_last,cp.name_first,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.status,cp.street_name,
    	sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,(SELECT encounter_type FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY encounter_date DESC LIMIT 1) AS encounter_type,
    	enc.current_ward_nr, enc.current_room_nr, current_dept_nr, enc.is_medico,
    	SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20) AS discountid,
    	SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discount)),20) AS discount,
    	scgp.discountid AS discountid_pid, scgp.discount AS discount_pid, d.parentid
    	FROM care_encounter AS enc
    	INNER JOIN care_person AS cp ON cp.pid=enc.pid
    	LEFT JOIN seg_radio_id AS sri ON sri.pid=cp.pid
    	LEFT JOIN seg_charity_grants AS scg ON scg.encounter_nr=enc.encounter_nr
    	LEFT JOIN seg_charity_grants_pid AS scgp ON scgp.pid=cp.pid
    	LEFT JOIN seg_discount AS d ON (d.discountid=scg.discountid OR d.discountid=scgp.discountid)
    	LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
    	LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
    	LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
    	LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
    	LEFT JOIN care_personell AS ps ON cp.pid=ps.pid AND date_exit NOT IN ('0000-00-00', DATE(NOW())) AND contract_end NOT IN ('0000-00-00', DATE(NOW()))
    	WHERE enc.encounter_nr=$nr AND cp.status NOT IN ('deleted','hidden','inactive','void') AND (death_date in (null,'0000-00-00',''))
    	GROUP BY cp.pid,scg.encounter_nr
    	ORDER BY name_last ASC";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result->FetchRow();
		} else { return false; }
	}


		function getPersonMiniInfoFromEncounter($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$bed_nr_cond = "IF(

		    (SELECT 
		      cel.`location_nr` 
		    FROM
		      care_encounter_location AS cel 
		    WHERE cel.`type_nr` = ".$db->qstr(5)." 
		      AND cel.`location_nr` IS NOT NULL 
		      AND cel.`encounter_nr` = $db->qstr($nr)
		    ORDER BY cel.`create_time` ASC 
		    LIMIT 1),
		    (SELECT 
		      cel.`location_nr` 
		    FROM
		      care_encounter_location AS cel 
		    WHERE cel.`type_nr` = ".$db->qstr(5)." 
		      AND cel.`location_nr` IS NOT NULL 
		      AND cel.`encounter_nr` = $db->qstr($nr)
		    ORDER BY cel.`create_time` ASC 
		    LIMIT 1),
		    NULL
		  ) AS current_bed_nr";
		$this->sql= "
    	SELECT ps.nr AS personnelID, sri.rid, enc.encounter_nr, cp.senior_ID, cp.fromtemp, cp.pid,cp.name_last,cp.name_first,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.status,cp.street_name,
    	sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,(SELECT encounter_type FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') AND enc.`encounter_nr` = $nr ORDER BY encounter_date DESC LIMIT 1) AS encounter_type,
    	enc.current_ward_nr, enc.current_room_nr, current_dept_nr,enc.er_location,enc.er_location_lobby, enc.is_medico,

scgp.discountid AS discountid_pid, scgp.discount AS discount_pid, d.parentid, $bed_nr_cond
FROM care_encounter AS enc
INNER JOIN care_person AS cp ON cp.pid=enc.pid
LEFT JOIN seg_radio_id AS sri ON sri.pid=cp.pid
LEFT JOIN seg_charity_grants AS scg ON scg.encounter_nr=enc.encounter_nr
LEFT JOIN seg_charity_grants_pid AS scgp ON scgp.pid=cp.pid
LEFT JOIN seg_discount AS d ON (d.discountid=scg.discountid OR d.discountid=scgp.discountid)
LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
LEFT JOIN care_personell AS ps ON cp.pid=ps.pid 
LEFT JOIN care_encounter_location AS cel ON enc.encounter_nr=cel.encounter_nr
WHERE enc.encounter_nr=$nr AND cp.status NOT IN ('deleted','hidden','inactive','void') 
GROUP BY cp.pid,scg.encounter_nr
ORDER BY name_last ASC";
/*echo $this->sql; die();*/
		if($this->result=$db->Execute($this->sql)) {
			return $this->result->FetchRow();
		} else { return false; }
	}

	function getERRequest($encounter_nr) {
		global $db;
		if ($encounter_nr)
			$encounter_nr = $db->qstr($encounter_nr);
		$this->sql = "SELECT refno FROM seg_pharma_orders WHERE encounter_nr=$encounter_nr AND pharma_area='ER' ORDER BY orderdate DESC";
		return $this->result=$db->GetOne($this->sql);
	}

	function getRecentWardRefInDateRange($frm,$to,$encounter_nr='') {
		global $db;
		if ($encounter_nr)
			$encounter_nr = $db->qstr($encounter_nr);
		$frm = date("Y-m-d H:i:s",$frm);
		$to = date("Y-m-d H:i:s",$to);
		$this->sql = "SELECT refno FROM seg_pharma_orders WHERE orderdate>='$frm' AND orderdate<='$to' ".($encounter_nr ? "AND pharma_area='WD' AND encounter_nr=$encounter_nr " : '')."ORDER BY orderdate DESC,refno DESC";
		return $this->result=$db->GetOne($this->sql);
	}

	function getOrderItems($refno) {
		global $db;
		$this->sql="SELECT i.*,p.artikelname,p.description\n".
				"FROM $this->items_tb AS i\n".
				"LEFT JOIN $this->prod_tb AS p ON p.bestellnum=i.bestellnum\n".
				"WHERE i.refno=".$db->qstr($refno);
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getOrderItemsFullInfo($refno, $discountID) {
		global $db;
		$refno = $db->qstr($refno);
    	$this->sql = "SELECT IF(requested_qty + quantity > quantity, requested_qty, quantity) AS `quantity`,o.pricecash AS `force_price`,o.is_consigned,a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
				"IFNULL(a.price_charge,0) AS chrgrpriceppk,\n".
				"IF(a.is_socialized,\n".
					"IFNULL((SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),a.price_cash),\n".
					"a.price_cash) AS dprice,\n".
				"IFNULL(a.price_cash,0) AS cshrpriceppk,\n".
    	"o.serve_status,o.serve_status AS SERVE,o.serve_remarks,o.request_flag,\n".
    	"o.inv_refno,a.barcode,\n".
    	"o.quantity as dispensed_qty,\n".
    	"IF(o.`pharma_area` = '', oo.`pharma_area`, o.`pharma_area`) AS pharma_area,\n".
    	"IF(o.`pharma_area` = '', oopa.`area_name`, opa.`area_name`) AS area_name,\n".
    	"oo.serve_status AS hearder_status,\n".
    	"o.is_down_inv,\n". /*added MARK Nov 03, 2016 */
    	"o.inv_uid,\n". /*added MARK Nov 03, 2016 */
    	"a.is_in_inventory,\n". /*added MARK Nov 03, 2016 */
    	"opa.`inv_api_key`,\n". /*added MARK Nov 03, 2016 */
    	"oo.`pid`,\n". /*added MARK Nov 03, 2016 */
    	"o.`requested_qty`,\n". /*added MARK Feb 06, 2016 */

    	"(SELECT dosage FROM seg_pharma_items_cf4 WHERE refno = o.`refno` AND bestellnum = o.`bestellnum` ORDER BY create_dt DESC LIMIT 1) AS dosage,\n".
		"(SELECT frequency FROM seg_pharma_items_cf4 WHERE refno = o.`refno` AND bestellnum = o.`bestellnum` ORDER BY create_dt DESC LIMIT 1) AS frequency,\n".
		"(SELECT route FROM seg_pharma_items_cf4 WHERE refno = o.`refno` AND bestellnum = o.`bestellnum` ORDER BY create_dt DESC LIMIT 1) AS route\n".
				"FROM seg_pharma_order_items AS o\n".
    	"INNER JOIN seg_pharma_orders AS oo ON oo.refno=o.refno\n".
    	"LEFT JOIN seg_pharma_areas AS opa ON o.pharma_area=opa.area_code\n".
    	"LEFT JOIN seg_pharma_areas AS oopa ON oo.pharma_area=oopa.area_code\n".
				"LEFT JOIN care_pharma_products_main AS a ON o.bestellnum=a.bestellnum\n".
				"WHERE o.refno = $refno";
				#echo $this->sql;
				
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

    /*added by MARK 2016-11-29*/
    function getServeStatus($refno){
    	global $db;
    	$this->sql = "SELECT serve_status
    	FROM seg_pharma_order_items 
    	WHERE refno = ".$db->qstr($refno)."AND serve_status IN('N','P')";
    	$row = $db->GetRow($this->sql);

    	return $row;
    }
    /*End By MARK*/
	function getOrderItemsForServe($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "SELECT a.bestellnum,a.artikelname,\n".
			"o.request_flag,oo.charge_type,\n".
    		// "IF(o.requested_qty + o.quantity > o.quantity, o.requested_qty, o.quantity) as quantity,o.is_consigned,\n".
			"o.quantity,o.is_consigned,\n".
			"o.pricecash, o.pricecharge,\n".
			"o.serve_status,o.serve_remarks,\n".
    	"o.is_unused, oo.pharma_area,\n".
    	"o.quantity as dispensed_qty,\n".
    	"IF(o.`pharma_area` = '', oo.`pharma_area`, o.`pharma_area`) AS pharma_area,\n".
    	"IF(o.`pharma_area` = '', oopa.`area_name`, opa.`area_name`) AS area_name,\n".
    	" o.`is_down_inv`,\n".
    	" a.`is_in_inventory`,\n".
    	" oo.`pid`,\n".
    	" o.`inv_uid`,\n".
		" opa.`inv_api_key`,\n".
		" a.`prod_class`,\n".
		" (SELECT dosage FROM seg_pharma_items_cf4 WHERE refno=o.`refno` AND bestellnum=o.bestellnum) as dosage, \n".
		" (SELECT frequency FROM seg_pharma_items_cf4 WHERE refno=o.`refno` AND bestellnum=o.bestellnum) as frequency, \n".
		" (SELECT route FROM seg_pharma_items_cf4 WHERE refno=o.`refno` AND bestellnum=o.bestellnum) as route \n".
			"FROM seg_pharma_order_items AS o\n".
			"INNER JOIN seg_pharma_orders AS oo ON oo.refno=o.refno\n".
    	"LEFT JOIN seg_pharma_areas AS opa ON o.pharma_area=opa.area_code\n".
    	"LEFT JOIN seg_pharma_areas AS oopa ON oo.pharma_area=oopa.area_code\n".
			"LEFT JOIN care_pharma_products_main AS a ON o.bestellnum=a.bestellnum\n".
			"WHERE o.refno = $refno";
			
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function addDiscounts($refno, $discArray) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "INSERT INTO $this->discounts_tb(refno,discountid) VALUES($refno,?)";
		if($buf=$db->Execute($this->sql,$discArray)) {
				return true;
		} else { return false; }
	}

	function getPharAreaISO($area_code) {
		global $db;
		$area_code = $db->qstr($area_code);
		$patientEncounterNameSql = "SELECT spif.iso_footer AS 'iso_footer'
                							FROM seg_pharma_iso_footer spif
                							WHERE spif.area_code = ".$area_code;
        $pharma_iso = $db->GetRow($patientEncounterNameSql);
        return $pharma_iso['iso_footer'];
	}

//	function getActiveOrdersEx($filters, $offset=0, $rowcount=15) {
//		global $db;
//		#if (is_numeric($now)) $dDate = date("Ymd",$now);
//		#$where = array();
//		#if ($dDate) $where[] = "o.orderdate=$dDate";
//		#else $dDate = $db->qstr($dDate);
//		if (!$offset) $offset = 0;
//		if (!$rowcount) $rowcount = 15;
//
//		$phFilters = array();
//		if (is_array($filters)) {
//		foreach ($filters as $i=>$v) {
//			switch (strtolower($i)) {
//				case 'datetoday':
//					$phFilters[] = 'DATE(orderdate)=DATE(NOW())';
//				break;
//				case 'datethisweek':
//					$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND WEEK(orderdate)=WEEK(NOW())';
//				break;
//				break;
//				case 'datethismonth':
//					$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
//				break;
//				case 'date':
//					$phFilters[] = "DATE(orderdate)=".$db->qstr($v);
//				break;
//				case 'datebetween':
//					$phFilters[] = "orderdate BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
//				break;
//				case 'name':
//					if (strpos($v,',')!==false) {
//						$split_name = explode(',', $v);
//						$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0]).'%'). " OR w.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
//						$phFilters[] = "cp.name_first LIKE ".$db->qstr(trim($split_name[1]).'%'). " OR w.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');
//					}
//					else {
//						if ($v) {
//							$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($v).'%'). " OR w.name_last LIKE ".$db->qstr(trim($v).'%');
//						}
//					}
//				break;
//				case 'pid':
//					$phFilters[] = "o.pid=".$db->qstr($v);
//				break;
//				case 'patient':
//					$phFilters[] = "o.pid=".$db->qstr($v)." OR o.walkin_pid=".$db->qstr($v);
//				break;
//				case 'inpatient':
//					$phFilters[] = "o.encounter_nr=".$db->qstr($v);
//				break;
//				case 'walkin':
//					$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
//				break;
//				case 'area':
//					$phFilters[] = 'pharma_area='.$db->qstr($v);
//				break;
//			}
//		}}
//
//		if (!$phFilters) {
//			$phFilters[] = 'orderdate >= NOW()-INTERVAL 1 MONTH';
//		}
//		$phWhere=implode(") AND (",$phFilters);
//		if ($phWhere) $phWhere = "($phWhere)";
//		$this->sql="SELECT SQL_CALC_FOUND_ROWS\n".
//					"o.orderdate,o.refno,o.pid,fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) name,\n".
//					"o.is_cash,o.charge_type,a.area_name AS `area_full`,\n".
//					"(SELECT GROUP_CONCAT(CONCAT(IFNULL(oi.request_flag,''),'\\t',IFNULL(oi.serve_status,'N'),'\\t',prod.artikelname) SEPARATOR '\\n')\n".
//						"FROM seg_pharma_order_items AS oi\n".
//						"INNER JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
//						"WHERE o.refno = oi.refno) AS `items`\n".
//				"FROM $this->coretable AS o\n".
//				"LEFT JOIN care_person AS cp ON o.pid=cp.pid\n".
//				"LEFT JOIN seg_walkin AS w ON o.walkin_pid=w.pid\n".
//				"INNER JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
//				($phWhere ? "WHERE ($phWhere)\n" : "").
//				"ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n".
//				"LIMIT $offset, $rowcount";
//
//		//return mysql_query($this->sql,$db->_connectionID);
//		if($this->result=$db->Execute($this->sql)) {
//			return $this->result;
//		}
//		else {
//			return false;
//		}
//	}



	function getActiveOrders($filters, $offset=0, $rowcount=15) {
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$phFilters = array();
		$personFilters = array();
		$walkinFilters = array();
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters[] = 'DATE(orderdate)=DATE(NOW())';
					break;
					case 'datethisweek':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND WEEK(orderdate)=WEEK(NOW())';
					break;
					break;
					case 'datethismonth':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
					break;
					case 'date':
						$phFilters[] = "DATE(orderdate)=".$db->qstr($v);
					break;
					case 'datebetween':
						$phFilters[] = "orderdate BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
					break;
					case 'name':
						if (strpos($v,',')!==false) {
							$split_name = explode(',', $v);
							$personFilters[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
							$personFilters[] = "cp.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');

							$walkinFilters[] = "w.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
							$walkinFilters[] = "w.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');
						}
						else {
							if ($v) {
								$personFilters[] = "cp.name_last LIKE ".$db->qstr(trim($v).'%');
								$walkinFilters[] = "w.name_last LIKE ".$db->qstr(trim($v).'%');
							}
						}
					break;
					case 'pid':
					case 'patient':
						$phFilters[] = "o.pid=".$db->qstr($v);
					break;
					case 'inpatient':
						$phFilters[] = "o.encounter_nr=".$db->qstr($v);
					break;
					// arco
					case 'case_no':
						$phFilters[] = "o.encounter_nr=".$db->qstr($v);
					break; // arco
					case 'walkin':
						$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
					break;
					case 'area':
						$phFilters[] = 'pharma_area='.$db->qstr($v);
					break;
				}
			}
		}

		if (!$phFilters) {
			$phFilters[] = 'orderdate >= NOW()-INTERVAL 1 MONTH';
		}

		$query = "SELECT {calcRows}\n".
				"o.orderdate,o.refno,o.request_source,o.pid, {nameQuery},\n".
		"o.is_cash,o.charge_type,IFNULL(a.area_name,(SELECT aa.area_name FROM seg_pharma_areas AS aa WHERE aa.area_code = o.`pharma_area`)) AS `area_full`,o.is_urgent,ce.current_room_nr AS `current_room` ,ce.current_ward_nr AS `current_ward`,cw.ward_id AS `wardname`,\n".
				"ce.encounter_type AS enctype, ce.er_location AS erloc, ce.er_location_lobby AS erloclob,ce.current_dept_nr AS curdept, \n".
				"(SELECT GROUP_CONCAT(CONCAT(IFNULL(oi.request_flag,''),'\\t',IFNULL(oi.serve_status,'N'),'\\t',TRIM(prod.artikelname)) SEPARATOR '\\n')\n".
					"FROM seg_pharma_order_items AS oi\n".
					"INNER JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
					"WHERE o.refno = oi.refno) AS `items`\n".
			"FROM $this->coretable AS o\n".
		"LEFT JOIN `seg_pharma_order_items` AS oi\n".
		"ON o.`refno` = oi.`refno` \n".
			"  LEFT JOIN care_encounter AS ce
 ON ce.`encounter_nr` = o.`encounter_nr`\n".
 			"  LEFT JOIN care_ward AS cw
 ON ce.`current_ward_nr` = cw.`nr`\n".
			"{personJoin}\n".
		"LEFT JOIN seg_pharma_areas AS a ON a.area_code=oi.pharma_area\n".
			"{where}\n AND o.`is_deleted` = 0 ";

		// main query
		$queries = array();

		if ($personFilters) {
			$personWhere = array_merge($phFilters, $personFilters);
			$personWhere = implode(") AND (",$personFilters);
			if ($personWhere) $personWhere = "($personWhere)";
			$queries[] = strtr($query, array(
				'{calcRows}' => 'SQL_CALC_FOUND_ROWS',
				'{nameQuery}' => "fn_get_person_name(o.pid) `name`",
				'{personJoin}' => "LEFT JOIN care_person AS cp ON o.pid=cp.pid",
				'{where}' => ($personWhere ? "WHERE oi.refno IS NOT NULL AND ({$personWhere})" : "")
			));
		}

		if ($walkinFilters) {
			$walkinFilters = array_merge($phFilters, $walkinFilters);
			$walkinWhere = implode(") AND (",$walkinFilters);
			if ($walkinWhere) $walkinWhere = "($walkinWhere)";
			$queries[] = strtr($query, array(
				'{calcRows}' => empty($queries) ? 'SQL_CALC_FOUND_ROWS' : '',
				'{nameQuery}' => "fn_get_walkin_name(o.walkin_pid) `name`",
				'{personJoin}' => "LEFT JOIN seg_walkin AS w ON o.walkin_pid=w.pid",
				'{where}' => ($walkinWhere ? "WHERE oi.refno IS NOT NULL AND ({$walkinWhere})" : "")
			));
		}

		if (empty($queries)) {
			$phWhere = implode(") AND (",$phFilters);
			if ($phWhere) $phWhere = "($phWhere)";
			$queries[] = strtr($query, array(
				'{calcRows}' => 'SQL_CALC_FOUND_ROWS',
				'{nameQuery}' => "fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) `name`",
				'{personJoin}' => "",
				'{where}' => ($phWhere ? "WHERE oi.refno IS NOT NULL AND ({$phWhere})" : "")
			));
		}


		$this->sql = implode($queries, "UNION ALL\n") .
		" GROUP BY oi.refno ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n".
			"LIMIT $offset, $rowcount";


		// return mysql_query($this->sql,$db->_connectionID);
		if(($this->result=$db->Execute($this->sql)) !== false) {
			return $this->result;
		}
		else {
			return false;
		}
	}



	#edited by VAN 12-22-08
	#function getServeReadyOrders($filters, $offset=0, $rowcount=15) {
	function getServeReadyOrders($filters, $offset=0, $rowcount=15, $isreturned=0) {
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$phFilters = array("o.orderdate > NOW()-INTERVAL 2 MONTH");
		$phFields = array();
		//$phHaving = array("(is_cash AND paid) OR (NOT is_cash)");

		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'withtotals':
						$phFields[] = '(SELECT SUM(oi.pricecash*oi.quantity) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS amount_due';
					break;
					case 'withservecount':
						$phFields[] = "(SELECT COUNT(*) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS `count_total_items`";
						$phFields[] = "(SELECT COUNT(*) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno AND oi.serve_status='S') AS `count_served_items`";
						// added by Mark Feb 08,2017 for partial served
					$phFields[] = "(SELECT SUM(oi.requested_qty) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS `RtotalqtY`";
					$phFields[] = "(SELECT SUM(oi.quantity) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS `ALLtotalqtY`";

  						#edited by VAS 02-27-2017
					$phFields[] = "(SELECT DISTINCT GROUP_CONCAT(oi.serve_status) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno LIMIT 1) AS `server_status`";
					break;
					case 'area':
						if (strtoupper($v)!='ALL')
							$phFilters[] = 'pharma_area='.$db->qstr($v);
					break;
					case 'refno':
						$phFilters[] = "o.refno=".$db->qstr($v);
					break;
					case 'refno+name':
						//$phFilters[] = "ordername REGEXP '[[:<:]]".substr($db->qstr($v),1);
						if (strpos($v, ',') !== FALSE) {
							$split_name = explode(',', $v);
							$lname = trim($split_name[0]);
							$fname = trim($split_name[1]);
							$phFilters[] = "p.name_last LIKE ".$db->qstr($lname.'%');
							$phFilters[] = "p.name_first LIKE ".$db->qstr($fname.'%');
						}
						else {
							$phFilters[] = "p.name_last LIKE ".$db->qstr($v.'%');
						}
					break;
	//				case 'nopay':
	//					$phFilters[] = "pay.or_no IS NULL";
	//				break;
					case 'nopay':
						$phFilters[] = "is_cash=0";
						break;
					case 'daysago':
						$wFilters[] = "DATEDIFF(NOW(),orderdate)<=".$db->qstr($v);
						break;
					case 'datetoday':
						$phFilters[] = 'DATE(orderdate)=DATE(NOW())';
						break;
					case 'datethisweek':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND WEEK(orderdate)=WEEK(NOW())';
						break;
					case 'datethismonth':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
						break;
					case 'date':
						$phFilters[] = "DATE(orderdate)=".$db->qstr($v);
						break;
					case 'datebetween':
						$phFilters[] = "orderdate BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
						break;
					case 'name':
						if (strpos($v,',')!==false) {
							$split_name = explode(',', $v);
							$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0]).'%'). " OR w.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
							$phFilters[] = "cp.name_first LIKE ".$db->qstr(trim($split_name[1]).'%'). " OR w.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');
						}
						else {
							if ($v) {
								$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($v).'%'). " OR w.name_last LIKE ".$db->qstr(trim($v).'%');
							}
						}
						break;
					case 'pid':
						$phFilters[] = "o.pid = ".$db->qstr($v);
						break;
					case 'patient':
						$phFilters[] = "o.pid=".$db->qstr($v);
						break;
					case 'inpatient':
						$phFilters[] = "o.encounter_nr=".$db->qstr($v);
						break;
					// arco
					case 'case_no':
						$phFilters[] = "o.encounter_nr=".$db->qstr($v);
						break; // arco
					case 'walkin':
						$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
						break;
					case 'serve':
						switch (strtolower($v)) {
							case 's':
								$phHaving[] = "count_total_items=count_served_items";
								break;
							case 'p':
							$phHaving[] = "(RtotalqtY <> ALLtotalqtY)";
								break;
							case 'n':
								$phHaving[] = "count_served_items=0";
								break;
						}
						break;
				}
			}
		}

		$withDateFilters = strpos(strtoupper(implode("_",array_keys($filters))),"DATE") !== FALSE;
		if (!$withDateFilters) {
			// if no date is specified, fetch only requests that are at most 1 month old
			$phFilters[] = "DATE(orderdate)>(DATE(NOW())-INTERVAL 2 DAY)";
		}

		$phWhere=implode(") AND (",$phFilters);
		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";
		$fields=implode(",\n",$phFields);
		if ($fields) $fields .= ',';

		$havingClause = implode(") AND (",$filters);
		if ($havingClause) $havingClause = "HAVING ($havingClause)";

		#added by VAN 12-22-08 temporary .. pls change it :)
		if ($isreturned){
			$sql_pay = " LEFT JOIN seg_pay_request AS pay ON pay.ref_no=o.refno AND am.ref_source='PH'\n";
		}

		$this->sql="SELECT SQL_CALC_FOUND_ROWS\n".
			"o.orderdate,o.refno,o.pid,fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) name,\n".
			"o.is_cash,o.charge_type,ce.current_room_nr AS current_room ,ce.current_ward_nr AS current_ward, ce.encounter_type AS enctype,\n".
			"ce.er_location AS erloc, ce.er_location_lobby AS erloclob,ce.current_dept_nr AS curdept,\n".
			"a.area_name AS `area_full`,IFNULL(am.amount,-1) AS ss_amount,\n".
			$fields.
			"(SELECT GROUP_CONCAT(CONCAT(IFNULL(LCASE(oi.request_flag),''),'\\t',IFNULL(oi.serve_status,'N'),'\\t',prod.artikelname) SEPARATOR '\\n')\n".
				"FROM seg_pharma_order_items AS oi\n".
				"LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
				"WHERE o.refno = oi.refno) AS `items`,\n".
			"EXISTS(SELECT NULL FROM seg_pharma_order_items AS oi WHERE oi.refno=o.refno AND oi.request_flag IS NOT NULL) AS `paid`\n".
			#"EXISTS(SELECT NULL FROM seg_pharma_order_items AS i WHERE i.refno=o.refno AND i.request_flag='LINGAP') AS `lingap`,\n".
			#"EXISTS(SELECT NULL FROM seg_pharma_order_items AS i WHERE i.refno=o.refno AND i.request_flag='CMAP') AS `cmap`\n".
			"FROM $this->coretable o\n".
			"LEFT JOIN care_person cp ON cp.pid=o.pid\n".
			"LEFT JOIN care_encounter ce ON ce.encounter_nr = o.encounter_nr \n".
			"LEFT JOIN seg_walkin AS w ON o.walkin_pid=w.pid\n".
			"LEFT JOIN seg_pharma_areas a ON a.area_code=o.pharma_area\n".
			"LEFT JOIN seg_charity_amount am ON am.ref_no=o.refno AND am.ref_source='PH'\n".
#				$sql_pay. #added by VAN 12-22-08*/
#				"LEFT JOIN seg_pay_request AS pr ON pr.ref_no=o.refno AND pr.ref_source='PH'\n".
#				"LEFT JOIN seg_pay AS pay ON (pay.or_no=pr.or_no AND pay.cancel_date IS NULL)\n".
			"WHERE\n".
				"($phWhere)\n";
		if ($phHaving) $this->sql .= "HAVING (" . implode(") AND (",$phHaving) . ")\n";
		$this->sql .= "ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n" .
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function changeServeStatus($refno, $itemsArray, $remarksArray, $dispensedArray, $itemAreaArray) {
		global $db, $root_path;

		require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
		$invService = new InventoryServiceNew();

		if (!$itemsArray || !$dispensedArray) return FALSE;
		if (!is_array($itemsArray)) $itemsArray = array($itemsArray);
		if (!is_array($remarksArray)) $remarksArray = array($remarksArray);
		if (!is_array($dispensedArray)) $dispensedArray = array($dispensedArray);
		if (!is_array($itemAreaArray)) $itemAreaArray = array($itemAreaArray);

		foreach ($itemsArray as $i=>$item) {
			unset($data);
			$dbOk = TRUE;
			$invQtyOk =TRUE;
			$dispensed_qty = $dispensedArray[$i];
			$pharma_area = $itemAreaArray[$i];
			$data = array(
				"refno"=>$db->qstr($refno),
				"bestellnum"=>$db->qstr($item),
				"serve_remarks"=>$db->qstr($remarksArray[$i]),
				"pharma_area" => $db->qstr($pharma_area)
			);
			if ($dispensed_qty > 0) {
				$ref = $db->GetRow("SELECT IF(spo.`encounter_nr` = '' OR spo.`encounter_nr` IS NULL, 'WALKIN', spo.`encounter_nr`) AS encounter_nr,
					spoi.`quantity`, IF(spo.`pid` IS NULL, sw.`pid`, spo.`pid`) AS pid, spo.`ordername`,
					IF(spo.`is_cash`,NULL,spo.`charge_type`) AS charge_type, cppm.`is_in_inventory`,
					spoi.`pricecash`, cppm.`item_code`, cppm.`barcode`,spo.`refno`,spoi.`serve_status`,
					IF(spo.`pid` IS NULL, sw.`name_last`, cp.`name_last`) AS name_last,
					IF(spo.`pid` IS NULL, IF(sw.`name_first` = '', ' ', sw.`name_first`), cp.`name_first`) AS name_first,
					spa.`inv_api_key`,cppm.`artikelname` 
					FROM seg_pharma_orders spo\n".
					"LEFT JOIN seg_pharma_order_items spoi ON spoi.`refno` = spo.`refno`\n".
					"LEFT JOIN care_pharma_products_main cppm ON cppm.`bestellnum` = spoi.`bestellnum`\n".
					"LEFT JOIN care_person cp ON cp.`pid` = spo.`pid`\n".
					"LEFT JOIN seg_walkin sw ON sw.`pid` = spo.`walkin_pid`\n".
					"LEFT JOIN seg_pharma_areas spa ON spa.area_code = spoi.pharma_area\n".
					"WHERE spo.`refno`=".$db->qstr($refno)." AND spoi.`bestellnum` = ".$db->qstr($item).
					" AND spoi.`pharma_area` = ".$db->qstr($pharma_area));
				$newQTY_dispense = $dispensed_qty+$ref['quantity'];
				$data["quantity"] =$db->qstr(($ref['serve_status']!='S') ? $dispensed_qty : $newQTY_dispense);
				$data["serve_dt"]="NOW()";
				$data['serve_status'] = $db->qstr("S");

                //START for inventory UPDATE BY MARK 2016-10-30
				if($ref['is_in_inventory'] == 1){
					$INV_UID ="";
					$INV_DOWN ="";
					$dataItem = $invService->GetItemListFromDai($ref['inv_api_key'],'item_list');
					foreach ($dataItem['iteminfo'] as $key => $value) {
						if($value['item_code'] == $ref['item_code']){
							if($value['quantity']< $dispensed_qty){
								$this->error_qty_msg .= $ref['artikelname']."(".$value['quantity'].")<br>";
								$this->error_msg = "Quantity must not exceed with the actual stock in inventory. Please encode quantity within actual stock.<br>";	
								$invQtyOk =false;
							}
						}
					}
					if($invQtyOk){
						$dataFromHIS = array( '&item_code'=>$ref['item_code'],
							'&barcode'=>$ref['barcode'],
							'&quantity'=>$dispensed_qty,
							'&hnumber'=>$ref['pid'],
							'&cnumber'=>$ref['encounter_nr'],
							'&fname'=>$ref['name_first'],
							'&lname'=>$ref['name_last']."-RefNo.(".$ref['refno'].")");
						$trans_result= $invService->transactItemToDai($dataFromHIS,'item_transact',$pharma_area);
						if ($trans_result != "Failed" || !empty($trans_result)) {
							if ($trans_result =="AILED") {
								$trans_result ="FAILED";
							}
							$data["inv_uid"] = $db->qstr($trans_result);
							$data["is_down_inv"] = $db->qstr("0");
							$INV_UID =$trans_result;
							$INV_DOWN ="0";
			} else {
							if ($trans_result =="AILED") {
								$trans_result ="FAILED";
							}
							$data["inv_uid"] = $db->qstr($trans_result);
							$data["is_down_inv"] = $db->qstr("1");
							$INV_UID =$trans_result;
							$INV_DOWN ="1";
						}
						unset($dataINV);
						$dataINV = array(
							"refno"=>$refno,
							"bestellnum"=>$item,
							"dispense_qty" =>$dispensed_qty,
							"inv_uid" =>$INV_UID,
							"is_down_inv" =>$INV_DOWN,
							"ip_source" =>$invService->getIPsource(),
							"uri_source" =>$invService->RequestURI(),
							"pid"=>$ref['pid'],
							"encounter_nr"=>$ref['encounter_nr'],
							"fname"=>$ref['name_first'],
							"lname"=>$ref['name_last'],
							"serve_id" =>$_SESSION['sess_user_name']
						);
						$this->insertInvUID(serialize($dataINV));
                        unset($dataINV);
					}else{
						$this->error_msg .= $this->list_qty_item;
						$dbOk = false;
			}
			}
               	//END for inventory UPDATE BY MARK 2016-10-30
				if(!empty($data["inv_uid"]) || !($ref['is_in_inventory'] == 1)){
			if ($old_serve_status != $new_serve_status) {
				# Handle applied coverage for PHIC and other benefits
				if ($ref['charge_type'] == 'PHIC') {
						// Hardcode hcare ID (temporary workaround)
						define('__PHIC_ID__', 18);
						$this->sql = "SELECT coverage FROM seg_applied_coverage\n".
							"WHERE ref_no='T{$ref['encounter_nr']}'\n".
								"AND source='M'\n".
								"AND item_code=".$db->qstr($item)."\n".
								"AND hcare_id=".__PHIC_ID__;
							$newTotal = parseFloatEx($ref['pricecash']) * parseFloatEx($dispensedArray[$i]);
							$coverage = parseFloatEx($db->GetOne($this->sql)) + parseFloatEx($newTotal);
						$result = $db->Replace('seg_applied_coverage',
							array(
								'ref_no'=>"T{$ref['encounter_nr']}",
								'source'=>'M',
								'item_code'=>$item,
								'hcare_id'=>__PHIC_ID__,
								'coverage'=>$coverage
							),
							array('ref_no', 'source', 'item_code', 'hcare_id'),
							$autoquote=TRUE
						);
						if ($result) $dbOk = TRUE;
						else {
							$this->error_msg = "Unable to update applied coverage for item #{$item}...";
							$dbOk = FALSE;
						}
					}
			}
			// var_dump($new_serve_status);
			$getItems [] = array( 
				'items' => $item,
				'status' => $new_serve_status == "S" ? '1' : '0'
			);

			if ($dbOk) {
						$ok = $db->Replace(
							"seg_pharma_order_items",
					$data,
							array("refno","bestellnum","pharma_area"),
							$autoquote = FALSE
						);

				$dbOk = ($ok>0);
				if (!$dbOk) {
					$this->error_msg = "Unable to update serve status for item #{$item}...";
				}
			}
			if (!$dbOk) return FALSE;
				}else{
					$failedArr[] = $item;
				}
			}
		}
		if(!empty($failedArr)){
			$this->error_msg = 'Quantity must not exceed with the actual stock in inventory. Please encode quantity within actual stock.<br>';
		}
				if ($ok == 1) {

						$data = array(
							'encounter_nr' => $ref['encounter_nr'],
							'items' => $getItems,
						);
						
				        $ehr = Ehr::instance();
				        $patient = $ehr->postServePharma($data);
				        $asd = $ehr->getResponseData();
				        $EHRstatus = $patient->status;
				        // echo "<pre>";
				        // var_dump($asd); 
				        // var_dump($patient);	
				        // var_dump($asd); 
				        // die();	

				        if(!$EHRstatus){
				        // echo "<pre>";
				        //     var_dump($pharmaReq);
				        //     var_dump($asd);
				        //     var_dump($patient);
				            // die();
				        }
				}
		return TRUE;
	}

	function changeServeStatus2($refno, $itemsArray, $remarksArray, $dispensedArray, $itemAreaArray,$drf = array()) {
		global $db, $root_path;
		$has_inventory=FALSE;
		$medicineItem = "M";
		require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
		$invService = new InventoryServiceNew();
		$saving_to_order_items=true;
		if (!$itemsArray || !$dispensedArray) return FALSE;
		if (!is_array($itemsArray)) $itemsArray = array($itemsArray);
		if (!is_array($remarksArray)) $remarksArray = array($remarksArray);
		if (!is_array($dispensedArray)) $dispensedArray = array($dispensedArray);
		if (!is_array($itemAreaArray)) $itemAreaArray = array($itemAreaArray);
		// $is_locked = $db->GetOne("SELECT IF(IS_USED_LOCK('serving_refno') IS NULL, FALSE , TRUE )");
		// if (!$is_locked){
		// 	$db->GetOne("SELECT GET_LOCK('serving_refno',5)");
		foreach ($itemsArray as $i=>$item) {
			unset($data);
			$dbOk = TRUE;
			$invQtyOk =TRUE;
			$dispensed_qty = $dispensedArray[$i];
			$pharma_area = $itemAreaArray[$i];
			$data = array(
				"refno"=>$db->qstr($refno),
				"bestellnum"=>$db->qstr($item),
				"serve_remarks"=>$db->qstr($remarksArray[$i]),
				"pharma_area" => $db->qstr($pharma_area)
			);
			if(($db->GetOne("SELECT prod_class FROM `care_pharma_products_main`
							WHERE `bestellnum`=".$db->qstr($item)))==$medicineItem){
				$this->sql = "UPDATE $this->items_cf4_tb SET dosage=".$db->qstr($drf["dosage"][$i]).",frequency=".$db->qstr($drf["frequency"][$i]).",route=".$db->qstr($drf["route"][$i]).
							 " WHERE refno=".$db->qstr($refno)." AND bestellnum=". $db->qstr($item);
				$db->Execute($this->sql);
			}
			
			
			// if($item=='2460')
			// die("SELECT IF(spo.`encounter_nr` = '' OR spo.`encounter_nr` IS NULL, 'WALKIN', spo.`encounter_nr`) AS encounter_nr,
			// 		spoi.`quantity`, IF(spo.`pid` IS NULL, sw.`pid`, spo.`pid`) AS pid, spo.`ordername`,
			// 		IF(spo.`is_cash`,NULL,spo.`charge_type`) AS charge_type, cppm.`is_in_inventory`,
			// 		spoi.`pricecash`, cppm.`item_code`, cppm.`barcode`,spo.`refno`,spoi.`serve_status`,
			// 		IF(spo.`pid` IS NULL, sw.`name_last`, cp.`name_last`) AS name_last,
			// 		IF(spo.`pid` IS NULL, IF(sw.`name_first` = '', ' ', sw.`name_first`), cp.`name_first`) AS name_first,
			// 		spa.`inv_api_key`,cppm.`artikelname` 
			// 		FROM seg_pharma_orders spo\n".
			// 		"LEFT JOIN seg_pharma_order_items spoi ON spoi.`refno` = spo.`refno`\n".
			// 		"LEFT JOIN care_pharma_products_main cppm ON cppm.`bestellnum` = spoi.`bestellnum`\n".
			// 		"LEFT JOIN care_person cp ON cp.`pid` = spo.`pid`\n".
			// 		"LEFT JOIN seg_walkin sw ON sw.`pid` = spo.`walkin_pid`\n".
			// 		"LEFT JOIN seg_pharma_areas spa ON spa.area_code = spoi.pharma_area\n".
			// 		"WHERE spo.`refno`=".$db->qstr($refno)." AND spoi.`bestellnum` = ".$db->qstr($item).
			// 		" AND spoi.`pharma_area` = ".$db->qstr($pharma_area));
			if ($dispensed_qty > 0) {
				$ref = $db->GetRow("SELECT IF(spo.`encounter_nr` = '' OR spo.`encounter_nr` IS NULL, 'WALKIN', spo.`encounter_nr`) AS encounter_nr,
						spoi.`quantity`, IF(spo.`pid` IS NULL, sw.`pid`, spo.`pid`) AS pid, spo.`ordername`,
						IF(spo.`is_cash`,NULL,spo.`charge_type`) AS charge_type, cppm.`is_in_inventory`,
						spoi.`pricecash`, cppm.`item_code`, cppm.`barcode`,spo.`refno`,spoi.`serve_status`,
						IF(spo.`pid` IS NULL, sw.`name_last`, cp.`name_last`) AS name_last,
						IF(spo.`pid` IS NULL, IF(sw.`name_first` = '', ' ', sw.`name_first`), cp.`name_first`) AS name_first,
						spa.`inv_api_key`,cppm.`artikelname` 
						FROM seg_pharma_orders spo\n".
						"LEFT JOIN seg_pharma_order_items spoi ON spoi.`refno` = spo.`refno`\n".
						"LEFT JOIN care_pharma_products_main cppm ON cppm.`bestellnum` = spoi.`bestellnum`\n".
						"LEFT JOIN care_person cp ON cp.`pid` = spo.`pid`\n".
						"LEFT JOIN seg_walkin sw ON sw.`pid` = spo.`walkin_pid`\n".
						"LEFT JOIN seg_pharma_areas spa ON spa.area_code = spoi.pharma_area\n".
						"WHERE spo.`refno`=".$db->qstr($refno)." AND spoi.`bestellnum` = ".$db->qstr($item).
						" AND spoi.`pharma_area` = ".$db->qstr($pharma_area));
					
				$newQTY_dispense = $dispensed_qty+$ref['quantity'];
				$data["quantity"] =$db->qstr(($ref['serve_status']!='S') ? $dispensed_qty : $newQTY_dispense);
				$data["serve_dt"]="NOW()";
				$data['serve_status'] = $db->qstr("S");

	            //START for inventory UPDATE BY MARK 2016-10-30
				if($ref['is_in_inventory'] == 1){
					$has_inventory=TRUE;
					$INV_UID ="";
					$INV_DOWN ="";
					// $dataItem = $invService->GetItemListFromDai($ref['inv_api_key'],'item_list');
					// foreach ($dataItem['iteminfo'] as $key => $value) {
					// 	if($value['item_code'] == $ref['item_code']){
					// 		if($value['quantity']< $dispensed_qty){
					// 			$this->error_qty_msg .= $ref['artikelname']."(".$value['quantity'].")<br>";
					// 			$this->error_msg = "Quantity must not exceed with the actual stock in inventory. Please encode quantity within actual stock.<br>";	
					// 			$invQtyOk =false;
					// 		}
					// 	}
					// }
					// $this->serveToInventoryArray($data['refno'], $item, $pharma_area,$ref['is_in_inventory']);
				}
	            //END for inventory UPDATE BY MARK 2016-10-30
	            // var_dump($saving_to_order_items);die;
	            // var_dump($data);die();
				// if(!empty($data["inv_uid"]) || !($ref['is_in_inventory'] == 1)){
				if ($old_serve_status != $new_serve_status) {
					# Handle applied coverage for PHIC and other benefits
					if ($ref['charge_type'] == 'PHIC') {
						// Hardcode hcare ID (temporary workaround)
						define('__PHIC_ID__', 18);
						$this->sql = "SELECT coverage FROM seg_applied_coverage\n".
									"WHERE ref_no='T{$ref['encounter_nr']}'\n".
									"AND source='M'\n".
									"AND item_code=".$db->qstr($item)."\n".
									"AND hcare_id=".__PHIC_ID__;
						$newTotal = parseFloatEx($ref['pricecash']) * parseFloatEx($dispensedArray[$i]);
						$coverage = parseFloatEx($db->GetOne($this->sql)) + parseFloatEx($newTotal);
						$result = $db->Replace('seg_applied_coverage',
									array(
										'ref_no'=>"T{$ref['encounter_nr']}",
										'source'=>'M',
										'item_code'=>$item,
										'hcare_id'=>__PHIC_ID__,
										'coverage'=>$coverage
									),
									array('ref_no', 'source', 'item_code', 'hcare_id'),
									$autoquote=TRUE
						);
						if ($result) $dbOk = TRUE;
						else {
							$this->error_msg = "Unable to update applied coverage for item #{$item}...";
							$dbOk = FALSE;
						}
					}
				}
				if ($dbOk) {
					$saving_to_order_items = $db->Replace(
						"seg_pharma_order_items",
						$data,
						array("refno","bestellnum","pharma_area"),
						$autoquote = FALSE
					);
					if (!$saving_to_order_items) {
						$this->error_msg = "Unable to update serve status for item #{$item}...";
					}
				}
				if (!$saving_to_order_items) return FALSE;
			}
		}

		if($saving_to_order_items)	{
			return array($data['refno'],$has_inventory);
			$this->error_msg=null;
		}
		else return false;
	}

	function getPharmaArea($area, $fields='*') {
		global $db;
		$area = $db->qstr($area);
		$this->sql = "SELECT $fields FROM seg_pharma_areas WHERE area_code=$area";
		if($this->result=$db->GetRow($this->sql)) {
			return $this->result;
		} else { return false; }
	}
		function getPharmaAreaByuserDefault($personell_nr){
			global $db;
			$this->sql = "SELECT pda.area_code,spa.area_name 
			FROM pharma_default_areas AS pda
			INNER JOIN seg_pharma_areas AS spa
			ON spa.area_code = pda.area_code
			WHERE pda.default_area='1' AND pda.personell_nr = ".$db->qstr($personell_nr);
			$row = $db->GetRow($this->sql);
			return $row;
		}

	//added by CHA 09-23-09
	function checkOrderItemPaid($refno,$bestellnum)
	{
		global $db;
		$sql1="select r.or_no from seg_pay_request as r join seg_pay as p where r.ref_no=".$db->qstr($refno)." and r.service_code=".$db->qstr($bestellnum).
						" and r.or_no=p.or_no and p.cancelled_by='' and r.ref_source='PH'";
		$sql2="select d.entry_id from seg_lingap_entry_details as d join seg_lingap_entries as h ".
						"where d.entry_id=h.entry_id and d.ref_no=".$db->qstr($refno)." and d.service_code=".$db->qstr($bestellnum)." and h.is_deleted=0";
	 // echo "<br>sql1:".$sql1;
		//echo "<br>sql2:".$sql2;
		$result1 = $db->Execute($sql1);
		$row1 = $result1->FetchRow();
		$result2 = $db->Execute($sql2);
		$row2 = $result2->FetchRow();
	 // echo  "<br>or_no: ".$row1['or_no'];
		if($row1['or_no']!="" && $row2['entry_id']!="") return true;
		if($row1['or_no']!="" && $row2['entry_id']=="") return true;
		if($row1['or_no']=="" && $row2['entry_id']!="") return true;
		return false;
	}
	//end CHA


	#created by cha, may 31,2010
	function getWalkinIssuance($pid='', $area='', $product_code='', $from_dt='', $to_dt='')
	{
		global $db;
		$where = " WHERE ";

		if($product_code)
			$where.=" i.bestellnum=".$db->qstr($product_code)." AND\n";
		if($area)
			$where.=" p.pharma_area=".$db->qstr($area)."AND\n";
		if($from_dt || $to_dt)
			$where.=" (i.serve_dt BETWEEN ".$db->qstr($from_dt)." AND ".$db->qstr($to_dt).") AND\n";
		if($pid)
			 $where.=" w.pid=".$db->qstr($pid)." AND\n";

		$this->sql=
			"SELECT i.serve_dt as `date`, CONCAT(w.name_last, ', ', w.name_first) AS `name`,\n".
				"p.pharma_area AS`area`, i.quantity, m.artikelname\n".
			"FROM seg_pharma_order_items AS i\n".
				"INNER JOIN seg_pharma_orders AS p ON p.refno=i.refno\n".
				"INNER JOIN seg_walkin AS w ON p.walkin_pid=w.pid\n".
				"INNER JOIN care_pharma_products_main AS m ON i.bestellnum=m.bestellnum\n".
			$where." i.serve_status IN ('S','N')\n".
			"ORDER BY `name`, i.serve_dt ASC";
			//die("<pre>".$this->sql."</pre>");
		if( ($this->result=$db->Execute($this->sql)) !== false )
		{
			return $this->result;
		}
		else
		{
			return false;
		}
	}
	#end cha
	        function getWardInfo($ward_nr){
        global $db;


            $this->sql="SELECT w.*,d.name_formal AS dept_name,
                SUM(CASE WHEN r.status NOT IN ('closed',$this->dead_stat) then 1 else 0 end) AS nr_of_rooms
                FROM care_ward AS w
                INNER JOIN care_room AS r ON r.ward_nr=w.nr
                LEFT JOIN care_department AS d ON w.dept_nr=d.nr
                WHERE w.nr='$ward_nr' AND w.status NOT IN ('closed',$this->dead_stat)";  
			/* echo $this->sql; die();*/
                if($this->res['gwi']=$db->Execute($this->sql)) {
                        if($this->rec_count=$this->res['gwi']->RecordCount()) {
                 return $this->res['gwi']->FetchRow();
            } else { return false; }
        } else { return false; }
    }
            function getERLocation($loc_code, $lobby_code = '0') {
            global $db;

            $this->sql = "SELECT * FROM seg_er_location 
                            LEFT JOIN seg_er_lobby ON lobby_id = {$lobby_code} 
                          WHERE location_id = {$loc_code}";


            if ($this->result=$db->Execute($this->sql)) {
                if ($this->result->RecordCount()) {
                        return $this->result->FetchRow();
                } else {
                    return FALSE;
                }
            } else {
                    return FALSE;
            }
        }

        function getPharArea($phar_area)
        {
        		global $db;
		$area = $db->qstr($phar_area);
		$this->sql = "SELECT area_name FROM seg_pharma_areas WHERE area_code=$area";

                if ($this->result=$db->Execute($this->sql)){
                        if ($this->result->RecordCount())
                                return $this->result;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }


        }


			function getDeptAllInfo($nr){
			global $db;
		$this->sql="SELECT * FROM care_department WHERE nr='$nr'";
	#	echo $this-sql;
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

/*		function getWardInfo($ward_nr){
		global $db;*/
#		$this->sql="SELECT w.*,d.name_formal AS dept_name FROM $this->tb_ward AS w LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
#					WHERE w.nr=$ward_nr AND w.status NOT IN ('closed',$this->dead_stat)";   # burn commented: November 13, 2007
		/*$this->sql="SELECT w.*,d.name_formal AS dept_name,
						(SELECT COUNT(*) FROM care_room WHERE ward_nr=$ward_nr AND status NOT IN ('closed',$this->dead_stat)) AS nr_of_rooms
					FROM $this->tb_ward AS w LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
					WHERE w.nr=$ward_nr AND w.status NOT IN ('closed',$this->dead_stat)";   # burn added: November 13, 2007
			*/
/*
		    $this->sql="SELECT w.*,d.name_formal AS dept_name,
		        SUM(CASE WHEN r.status NOT IN ('closed',$this->dead_stat) then 1 else 0 end) AS nr_of_rooms
		        FROM care_ward AS w
		        INNER JOIN care_room AS r ON r.ward_nr=w.nr
		        LEFT JOIN care_department AS d ON w.dept_nr=d.nr
		        WHERE w.nr='$ward_nr' AND w.status NOT IN ('closed',$this->dead_stat)";   # burn added: November 13, 2007

#echo"class_ward.php : getWardInfo :: this->sql ='".$this->sql."' <br> \n";
				if($this->res['gwi']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['gwi']->RecordCount()) {
				 return $this->res['gwi']->FetchRow();
			} else { return false; }
		} else { return false; }
	}*/
	/*Added by MARK Feb 5, 2017*/
	function insertInvUID($dataINV){
        global $db;
		$this->sql ="INSERT INTO `seg_inventory_logs` SET
		`data_log`=".$db->qstr($dataINV)."";

		if($db->Execute($this->sql))
			return TRUE;
		else return FALSE;				
    }

	function getAllDosage(){
		global $db;
		$this->sql = "SELECT * FROM " . $this->dosage_tb;
		return $db->Execute($this->sql);
	}

	function getAllFrequency(){
		global $db;
		$this->sql = "SELECT * FROM " . $this->frequency_tb;
		return $db->Execute($this->sql);
	}

	function getAllRoutes(){
		global $db;
		$this->sql = "SELECT * FROM " . $this->routes_tb;
		return $db->Execute($this->sql);
	}

    function getRouteList()
    {
        global $db;

        $this->sql = "SELECT * FROM seg_phil_routes";

        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function getFrequencyList()
    {
        global $db;

        $this->sql = "SELECT * FROM seg_phil_frequency";
        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function getDosageList()
    {
        global $db;

        $this->sql = "SELECT * FROM seg_phil_medicine_strength";
        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function getPreviousDRF($encounter_nr,$bestellnum) {
        global $db;

        if(!$encounter_nr){
            return $this->result = false;
        }

        $this->sql = "SELECT 
						  spi.`dosage`,
						  spi.`frequency`,
						  spi.`route` 
						FROM
						  seg_pharma_orders spo 
						  LEFT JOIN seg_pharma_order_items spoi 
							ON spo.`refno` = spoi.`refno` 
						  LEFT JOIN seg_pharma_items_cf4 spi 
							ON spoi.`refno` = spi.`refno` 
							AND spoi.`bestellnum` = spi.`bestellnum` 
						WHERE spo.`encounter_nr` = ".$db->qstr($encounter_nr)."
						  AND spoi.`bestellnum` = ".$db->qstr($bestellnum)." 
						  AND spo.is_deleted = 0
						  AND spoi.is_deleted = 0
						  AND spoi.returns = 0
						ORDER BY spi.`create_dt` DESC 
						LIMIT 1 ";

        if(!$this->result=$db->GetRow($this->sql)) {
            $this->sql = "
				SELECT 
			      s.strength_disc AS dosage,'' AS frequency, '' AS route
			    FROM
			      care_pharma_products_main pm 
			      INNER JOIN seg_phil_medicine p 
			        ON pm.drug_code = p.drug_code 
			      INNER JOIN seg_phil_medicine_strength s 
			        ON p.strength_code = s.strength_code 
			    WHERE pm.bestellnum = ".$db->qstr($bestellnum)."
			    LIMIT 1";
            return $this->result = $db->GetRow($this->sql);
        } else { return $this->result; }
    }

}