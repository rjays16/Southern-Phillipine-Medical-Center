<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-schedule-common.php');



require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
include_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_department.php');
#$dept_obj=new Department;
include_once($root_path.'include/care_api_classes/class_personell.php');
#$pers_obj=new Personell;
require_once($root_path.'include/care_api_classes/class_radiology.php');
#$objService = new SegRadio;

require_once($root_path.'include/care_api_classes/class_tabview.php');
require($root_path.'include/care_api_classes/class_discount.php');

#added by VAN 06-03-2013
require_once($root_path.'include/care_api_classes/class_encounter.php');

// added by carriane 03/16/18
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);
// end carriane


define('Walkin_enc',5); #Added by Mats 04142020
#-------added by VAN 03-26-08
//added by: Borj Radiology Readers Fee 2014-10-17
function populateScheduledList($sElem, $tbId, $searchkey,$page,$dept){
	global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv = new SegRadio();
		$enc_obj=new Encounter;
        
        $offset = $page * $maxRows;

		$searchkey = utf8_decode($searchkey);

		// $objResponse->addAlert($dept);
		#if ($searchkey==NULL)
		#	$searchkey = 'now';

        #get dept
        $sub_dept_nr = substr($tbId,4);


        #$objResponse->alert('aj = '.$sub_dept_nr);
		#$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset);
		#$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,1);
        //added by: Borj Radiology Readers Fee 2014-10-17
        if($dept=='OB'){
			$cond = "AND rs.is_served=1";	
        }
        else{
        	$cond = "AND rs.is_served = 1 AND (g.other_name LIKE '%USD%' OR g.other_name LIKE '%ultrasound%') AND r.encounter_nr IS NOT NULL";
        }
        if ($searchkey){
            $ergebnis=$srv->SearchSelect($searchkey, $sub_dept_nr,$maxRows,$offset,
                $cond,$dept);

		    // $objResponse->addAlert($srv->sql);
		    #$total = $srv->count;
            $total = $srv->FoundRows();
        }else{
            $ergebnis = false;
            $total = 0;
        }
		#$objResponse->addAlert('total = '.$total);
		$lastPage = floor($total/$maxRows);
		#$objResponse->addAlert('total = '.floor($total%10));
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		#$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,0);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		#$objResponse->addAlert("pageno, lastpage, pagen, total = ".$page.", ".$lastPage.", ".$maxRows.", ".$total);
		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList",$tbId);
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				if ($result["pid"]!=" ")
					$name = trim($result["name_first"])." ".trim($result["name_middle"])." ".trim($result["name_last"]);
				else
					$name = trim($result["ordername"]);

				if (!empty($result['modify_id'])){
					$scheduled_by = trim($result['modify_id']);
				}else{
					$scheduled_by = trim($result['create_id']);
				}

				#added by VAN 06-17-08
				#$sked_time = date("h:i A",strtotime(trim($result["scheduled_time"])));

				#added by VAN 07-08-08
				if (trim($result["scheduled_dt"]))
					$sked_date = date("m/d/Y",strtotime(trim($result["scheduled_dt"])));
				else
					#$sked_date = date("m/d/Y");
                    $sked_date = date("m/d/Y",strtotime(trim($result["request_date"])));

				if (trim($result["scheduled_time"]))
					$sked_time = date("h:i A",strtotime(trim($result["scheduled_time"])));
				else
					#$sked_time = date("h:i A");
                    $sked_time = date("h:i A",strtotime(trim($result["request_time"])));

				if (empty($scheduled_by)){
					if (!empty($result['encoder'])){
						$scheduled_by = trim($result['encoder']);
					}else{
						$scheduled_by = trim($result['encoder2']);
					}
				}
				#-----------------

				#$objResponse->addAlert("type = ".$result['encounter_type']);

				if ($result['encounter_type']==1)
					$pat_type = "ERPx";
				elseif ($result['encounter_type']==2 || $result['encounter_type']==IPBMOPD_enc){
					$pat_type = "OPDPx";

					if($result['encounter_type']==IPBMOPD_enc)
						$pat_type = "OPDPx (IPBM)";
				}
				elseif (($result['encounter_type']==3)||($result['encounter_type']==4)||($result['encounter_type']==IPBMIPD_enc)){
					$pat_type = "INPx";

					if($result['encounter_type']==IPBMIPD_enc)
						$pat_type = "INPx (IPBM)";
				}
				elseif ($result['encounter_type']==6){
					$pat_type = "Industrial Clinic";
				}
				elseif($result['encounter_type']==Walkin_enc){
					$pat_type = "Walkin";
				}
				#--------------------
				#$objResponse->addAlert("type = ".$result["batchnum"]);
				#$objResponse->addAlert("refno, name, code, sked_date = ".trim($result["batch_nr"]).", ".$name.", ".trim($result["service_code"]).", ".trim($result["scheduled_dt"]).", ".trim($result["scheduled_time"]));
				#refnum
				#$objResponse->addScriptCall("addPerson","RequestList",trim($result["batch_nr"]),$name,trim($result["service_code"]),trim($result["serv_name"]),trim($result["scheduled_dt"]),$sked_time,trim($result["name_formal"]),trim($result["rid"]),$scheduled_by, trim($result["skstatus"]),trim($result["dept_short_name"]),$pat_type);
				$disabled_icon = 0;
                // if (($result["is_cash"]==1) && ($result["hasPaid"]==0))
                //     $disabled_icon = 1;
                if($result['fromdept']=='OBGUSD'){
					if (($result["is_cash"]==1) && ($result["request_flag"]!='paid' && $result["request_flag"]!='cmap' && $result["request_flag"]!='lingap') && ($result["request_flag"]=='charity' && ($result['r_discountid']!='PHS' && $result['r_discountid']!='PHSDep')))
						  $disabled_icon = 1;
				}else{
					if (($result["is_cash"]==1) && ($result["hasPaid"]==0))
                    $disabled_icon = 1;
				}
                // $objResponse->alert($result['request_flag']."+".$result['r_discountid']."+".$disabled_icon);
				$hasManualPayment = $enc_obj->hasManualPayment($result['batchnum']);
				// $objResponse->alert($enc_obj->sql);
				// $objResponse->addAlert($enc_obj->sql);
				if($hasManualPayment){
					 $disabled_icon = 0;
				}

                #get encounter info
                $bill = (object) 'bill';
                $billinfo = $enc_obj->hasSavedBilling($result['encounter_nr']);
                if ($billinfo){
                    $bill->bill_nr = $billinfo['bill_nr'];
                    $bill->hasfinal_bill = $billinfo['is_final'];
                    $bill->is_maygohome = $result['is_maygohome'];
                    $bill->is_cash = $result['is_cash'];
                }

                if($dept=='OB'){
                		$readersFee = $enc_obj->hasSavedAuditTrailPF($result['batchnum'], trim($result["service_code"]));
                		if($readersFee['is_cash']==0){
                			$trans_type = "Charge";
                		}
                		else{
                			$trans_type = "Cash";
                		}
	                if($readersFee){
	                    $done = "Encoder: ". $readersFee['create_id']." \nReaders Fee: ".number_format(floatval($readersFee['pf_amount']),2)." \nSonologist Name : ".$readersFee['dr_name']." \nType: ".$trans_type;
	                }else
	                    $done = FALSE;

	                    $result["dept_short_name"] = 'UCW';
                }
                else{
	                	$readersFee = $enc_obj->hasSavedReaders($result['encounter_nr'], trim($result["service_code"]));
	                if($readersFee){
	                    $done = "Encoder: ". $readersFee['create_id']." Readers Fee: ".number_format(floatval($readersFee['dr_charge']),2);
	                }else
	                    $done = FALSE;
	
                }
                
                //Added by: Borj 2014-09-16 Professional Fee
                $objResponse->addScriptCall("addPerson",$tbId, trim($result["refnum"]), $done,
                    $result["batchnum"],ucwords(strtolower($name)),trim($result["service_code"]),
                    trim($result["serv_name"]),$sked_date,$sked_time,trim($result["name_formal"]),
                    trim($result["rid"]),$scheduled_by, trim($result["skstatus"]),trim($result["dept_short_name"]),
                    $pat_type, $result["is_served"], $disabled_icon, $bill, trim($result["pid"]));
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson",$tbId,NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
}

function deleteScheduledRadioRequest($refno){
		global $db;
		$srv = new SegRadio;
		$objResponse = new xajaxResponse();

		if ($srv->deleteRadioSchedule($refno)) {
			$objResponse->addScriptCall("removeSkedRequest",$refno);
			$objResponse->addAlert("The scheduled request is successfully deleted.");
		}else{
			$objResponse->addAlert("The scheduled request is failed deleted.");
		}
		#$objResponse->addAlert("sql = ".$srv->sql);
		return $objResponse;
	}

#--------------------------------------

#added by VAN 08-14-2012
function savedServedPatient($batch_nr, $refno, $service_code, $is_served, $rad_tech=0, $served_date='', $served_time=''){
    global $db, $HTTP_SESSION_VARS;

    $objResponse = new xajaxResponse();
    $srv = new SegRadio;

    if ($is_served){
        #$date_served = date("Y-m-d H:i:s");
        $date = $served_date.' '.$served_time;
        $date_served = date("Y-m-d H:i:s", strtotime($date));
        $rad_tech  = $rad_tech;
    }else{
        $date_served = '0000-00-00 00:00:00';
        $rad_tech = 0;
    }

    $save = $srv->ServedRadioRequest($batch_nr, $refno, $service_code, $is_served, $date_served, $rad_tech);
    #$objResponse->alert("sql = ".$srv->sql);

    if ($save){
        if ($is_served)
            $objResponse->addScriptCall("closeWindow");
        else
            $objResponse->addScriptCall("ReloadWindow");
    }

    return $objResponse;

}

$xajax->processRequests();
?>