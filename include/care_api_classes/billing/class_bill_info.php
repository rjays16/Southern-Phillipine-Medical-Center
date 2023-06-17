<?php
//---------------------------------------------------------------------
// Class for retrieving or updating the saved billings.
// Created: 4-17-2008 (Lemuel S. Trazo)
// Updated: 4-02-2009 (Lemuel S. Trazo)
//---------------------------------------------------------------------

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_credit_collection.php');

define('SPONSORED', 'SPONSORED'); //added by jasper 04/16/2013
define('WELLBABY', 12); //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY

class BillInfo extends Core {
    var $memcategory = ''; //added by jasper 04/16/2013
    var $errmsg; //added by jasper 04/23/2013

	function BillInfo() {
		$this->coretable = "seg_billing_encounter";
	}

	function concatname($slast, $sfirst, $smid) {
		$stmp = "";

		if (!empty($slast)) $stmp .= $slast;
		if (!empty($sfirst)) {
			if (!empty($stmp)) $stmp .= ", ";
			$stmp .= $sfirst;
		}
		if (!empty($smid)) {
			if (!empty($stmp)) $stmp .= " ";
			$stmp .= $smid;
		}
		return($stmp);
	}

	function getBillingHeaderInfo($sbill_nr) {
		global $db;

		$this->sql = "select bill_nr, bill_dte, bill_frmdte, sbe.encounter_nr, ce.pid, ifnull(ce.admission_dt, bill_frmdte) as admission_date, ".
                     "      name_last, name_first, name_middle, street_name, brgy_name, mun_name, prov_name, addr_zip as zipcode, CONCAT(cp.death_date,' ',cp.death_time) as deathdate, cp.death_encounter_nr ".
					 "   from ((seg_billing_encounter as sbe inner join care_encounter as ce on sbe.encounter_nr = ce.encounter_nr) ".
					 "      inner join care_person as cp on ce.pid = cp.pid) left join ((seg_barangays as sbr ".
					 "      inner join seg_municity as sm on sbr.mun_nr = sm.mun_nr) inner join seg_provinces as spr on sm.prov_nr = spr.prov_nr) ".
					 "      on cp.brgy_nr = sbr.brgy_nr ".
					 "   where bill_nr = '". $sbill_nr ."' and sbe.is_deleted IS NULL";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getSavedBillings($filters, $offset=0, $rowcount=15) {
		global $db;
		#if (is_numeric($now)) $dDate = date("Ymd",$now);
		#$where = array();
		#if ($dDate) $where[] = "o.orderdate=$dDate";
		#else $dDate = $db->qstr($dDate);
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters[] = 'DATE(bill_dte)=DATE(NOW())';
					break;
					case 'datethisweek':
						$phFilters[] = 'YEAR(bill_dte)=YEAR(NOW()) AND WEEK(bill_dte)=WEEK(NOW())';
					break;
					break;
					case 'datethismonth':
						$phFilters[] = 'YEAR(bill_dte)=YEAR(NOW()) AND MONTH(bill_dte)=MONTH(NOW())';
					break;
					case 'date':
						$phFilters[] = "DATE(bill_dte)='$v'";
					break;
					case 'datebetween':
						$phFilters[] = "bill_dte>='".$v[0]."' AND bill_dte<='".$v[1]."'";
					break;
					case 'name':
						if (strpos($v, ",") === false) {
//							$phFilters[] = "concat(name_last, (case when isnull(name_first) or name_first = '' then (case when isnull(name_middle) or name_middle = '' then '' else ', ' end) else ', ' + name_first end), (case when isnull(name_middle) or name_middle = '' then '' else ' ' + name_middle end)) REGEXP '[[:<:]]".substr($db->qstr($v),1);
							$phFilters[] = "name_last like '".trim($v)."%'";
							if ( (trim($v) == '') || (strlen(trim($v)) < 3) ) $filter_err = "Specify at least 3 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters[] = "name_last like '".trim($tmp[0])."%'";
							$phFilters[] = "name_first like '".trim($tmp[1])."%'";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 3) )
								$filter_err = "Specify at least 3 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}

//						$phFilters[] = "concat(cp.name_last, (case when isnull(cp.name_first) or cp.name_first = '' then (case when isnull(cp.name_middle) or cp.name_middle = '' then '' else ', ' end) else ', ' + cp.name_first end), (case when isnull(cp.name_middle) or cp.name_middle = '' then '' else ' ' + cp.name_middle end)) REGEXP '[[:<:]]".substr($db->qstr($v),1);
	//					$phFilters[] = "ordername REGEXP '[[:<:]]".substr($db->qstr($v),1);
					break;
					case 'hrn':
						$phFilters[] = "ce.pid = ".$db->qstr($v);
					break;
	//				case 'patient':
	//					$phFilters[] = "o.pid=".$db->qstr($v);
	//				break;
					case 'case_no':
						$phFilters[] = "ce.encounter_nr = ".$db->qstr($v);
					break;
	//				case 'walkin':
	//					$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
	//				break;
	//				case 'area':
	//					$phFilters[] = 'pharma_area='.$db->qstr($v);
	//				break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}
        $phFilters[] = " sbe.is_deleted IS NULL";
		$phWhere=implode(") AND (",$phFilters);

		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";

		/*
		Edited by Jarel Used SQL function to get the total net amount and Person Name
		Remove also the table for care_person to optimize the Query
		*/
#		$havingClause = implode(") AND (",$filters);
#		if ($havingClause) $havingClause = "HAVING ($havingClause)";
        //edited by jasper 04/17/2013 (SELECT SUM(discount_amnt) FROM seg_billing_discount AS sbd WHERE sbd.bill_nr = sbe.bill_nr) AS total_discount_amnt, " .
        //edited by jasper 04/23/2013 sbe.is_deleted IS NULL
        //edited by borj 11-12-2014 added function fn_isPHIC (pic or non-phic)
		$this->sql = "select SQL_CALC_FOUND_ROWS sbe.bill_nr, sbe.`is_final`, bill_dte, ".
					 "ce.pid,ce.encounter_nr,
					 `fn_get_person_name_mname`(ce.pid) AS name,
					 `fn_isPHIC`(sbe.`encounter_nr`) as info_phic,
					 `fn_billing_compute_gross_amount`(sbe.`bill_nr`) AS net, ".
					 "     (select count(*) as bill_count from seg_billing_encounter as sbe1 ".
					 "          where sbe1.encounter_nr = sbe.encounter_nr and sbe1.bill_dte > sbe.bill_dte and sbe1.is_deleted IS NULL) as later_billings ".
					 "   from ($this->coretable as sbe inner join care_encounter as ce on sbe.encounter_nr = ce.encounter_nr) ".
					 "      inner join care_person as cp on ce.pid = cp.pid ".
					 "   where ($phWhere) ".
					 "   order by sbe.bill_dte asc ".
					 "   limit $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }

	}

	//updated by michelle 06-26-2015 to support credit collection
	function deleteBillInfo($bill_nr, $enc_nr, $del_reason = '', $other_reason = '', $data = array()) {
		global $db;


		if($other_reason == '')
			$reason = $db->GetOne("SELECT reason_description FROM seg_billing_delete_reasons WHERE reason_id='$del_reason'");
		else
			$reason = $other_reason;

		$history = "Deleted by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n";
		$history .= "Reason: ".$reason."\n";

		//$bill_nr = $db->qstr($bill_nr);
		//$db->LogSQL();
		//$this->sql = "delete from $this->coretable where bill_nr = $bill_nr";
        //edited by jasper 04/23/2013
        //$_SESSION['sess_temp_userid']   NOW()
        // updated by gervie 08/31/2015
        $strSQL = "UPDATE seg_billing_encounter SET " .
                     " is_deleted =  1, " .
                     " delete_reason = '" . $del_reason . "',".
					 " delete_other_reason = '" . utf8_decode($other_reason) . "',".
					 " history = IF(history IS NULL,".$db->qstr(utf8_decode($history)).",CONCAT(history,". $db->qstr(utf8_decode($history)).")),".
                     " modify_id = '" . $_SESSION['sess_temp_userid'] . "', " .
                     " modify_dt = NOW() " .
                     " WHERE bill_nr = '". $bill_nr ."'";

        $bSuccess = $db->Execute($strSQL);

        if (!$bSuccess) {
           $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update billing for encounter ".$enc_nr."."."\n".$strSQL;
           $db->FailTrans();
           return $bSuccess;
        } else {

        	// added by rnel removing MSS applied in SOA upon deletion of billing 08-30-2016
			$this->setAsDeleted($enc_nr);

        	//added by art 01/08/2014

        	$strSQL = "delete from seg_confinement_tracker where bill_nr = '". $bill_nr ."'";
        	$delete = $db->Execute($strSQL);
        	
        	if (!$delete) {
        		$this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete from confinement tracker for encounter ".$enc_nr."."."\n".$strSQL;
        	}else{//end art
        		$db->CompleteTrans();
        	}
            
        }

        //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
        if ($bSuccess) {
           /* if ($this->isWellBaby($enc_nr)) {
                $strSQL = "UPDATE care_encounter SET is_discharged = 0, is_maygohome = 0, discharge_date = '0000-00-00', discharge_time = '00:00:00' " .
                          " WHERE encounter_nr = '" . $enc_nr ."'";
            } else {
        $strSQL = "update care_encounter set " .
                     " is_maygohome =  0," .
                     " mgh_setdte = (NULL)" .
                     " where encounter_nr = '". $enc_nr ."'";

            }*/#commented by art 10/13/2014
            $strSQL = "update care_encounter set " .
                     " is_maygohome =  0," .
                     " mgh_setdte = (NULL)" .
                     " where encounter_nr = '". $enc_nr ."'";
        }
        //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY


        //edited by jasper 04/23/2013
		//$bSuccess = $this->Transact();
		//$db->LogSQL(false);
        $bSuccess = $db->Execute($strSQL);

		// added by michelle 06-26-2015
		if ($bSuccess) {
			if (!empty($data)) {
				$creditCollObj = new CreditCollection();
				$res = $creditCollObj->getTotalGrantsByTypeAndNr($data['type'], $data['encounter']);
				if (in_array($data['type'], array('infirmary', 'nbb', 'dependent'))) {
					$item = array(
						'ref_no' => $res['id'],
						'encounter_nr' => $data['encounter'],
						'bill_nr' => $data['bill_nr'],
						'entry_type' => 'credit',
						'amount' => $res['total'],
						'pay_type' => $data['type'],
						'control_nr' => strtoupper($data['type']),
						'description' => strtoupper($data['type']) . ' Billing Discount',
						'create_id' => $_SESSION['sess_user_name'],
						'create_time' => date('YmdHis'),
						'history' => 'Revoked ' . strtoupper($data['type']) . ' Billing Discount Added by ' . $_SESSION['sess_user_name'] . ' on ' . date('Y-m-d H:i:s A') . ' amount PHP ' . number_format($res['total'],2)
					);
					CreditCollection::insert($item);
				}
			}
		}
		// end

        if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update care encounter ".$enc_nr."."."\n".$strSQL;
        if (!$bSuccess) $db->FailTrans();
            $db->CompleteTrans();

        if (!$bSuccess) $db->FailTrans();
            $db->CompleteTrans();

		return $bSuccess;
	}

    function IsDischarge($encounter_nr, $is_discharged){
       global $db;
        $strSQL = "SELECT is_discharged
                           FROM care_encounter
                           WHERE encounter_nr='".$encounter_nr."'";


                    $is_discharged = false;
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        $row = $result->FetchRow();
                        $is_discharged = ($row['is_discharged'] != 1);
                    }
                }

        return $is_discharged;


    }


	function getSavedBillingsForTransmittal($filters, $offset=0, $rowcount=15, $hcare_id = 0) {
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';

		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
//						$phFilters[] = "DATE((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end))=DATE(NOW())";
						$phFilters[] = "discharge_date = DATE(NOW())";
					break;
					case 'datethisweek':
//						$phFilters[] = "YEAR((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end))=YEAR(NOW()) AND WEEK((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end))=WEEK(NOW())";
						$phFilters[] = "discharge_date BETWEEN DATE_SUB(DATE(NOW()), INTERVAL DAYOFWEEK(NOW())-1 DAY) AND DATE_ADD(DATE(NOW()), INTERVAL 7-DAYOFWEEK(NOW()) DAY)";
					break;
					case 'datethismonth':
//						$phFilters[] = "YEAR((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end))=YEAR(NOW()) AND MONTH((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end))=MONTH(NOW())";
						$phFilters[] = "discharge_date BETWEEN
						                          DATE_SUB(DATE(NOW()), INTERVAL DAYOFMONTH(NOW())-1 DAY)
						                                 AND
						                          DATE_SUB(DATE_ADD(DATE_SUB(DATE(NOW()), INTERVAL DAYOFMONTH(NOW())-1 DAY), INTERVAL 1 MONTH), INTERVAL 1 DAY)";
					break;
					case 'date':
//						$phFilters[] = "DATE((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end))='$v'";
							$phFilters[] = "discharge_date = DATE('$v')";
					break;
					case 'datebetween':
//						$phFilters[] = "str_to_date(concat(date_format((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end), '%Y-%m-%d'), ' ', date_format((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_time end), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '".$v[0]."' ".
//													 "AND str_to_date(concat(date_format((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_date end), '%Y-%m-%d'), ' ', date_format((case when (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.") then NOW() else discharge_time end), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <='".$v[1]."'";
						$phFilters[] = "discharge_date BETWEEN DATE('".$v[0]."') AND DATE('".$v[1]."')";
					break;
					case 'name':
						if (strpos($v, ",") === false) {
//							$phFilters[] = "concat(name_last, (case when isnull(name_first) or name_first = '' then (case when isnull(name_middle) or name_middle = '' then '' else ', ' end) else ', ' + name_first end), (case when isnull(name_middle) or name_middle = '' then '' else ' ' + name_middle end)) REGEXP '[[:<:]]".substr($db->qstr($v),1);
							$phFilters[] = "name_last like '".trim($v)."%'";
							if ( (trim($v) == '') || (strlen(trim($v)) < 3) ) $filter_err = "Specify at least 3 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters[] = "name_last like '".trim($tmp[0])."%'";
							$phFilters[] = "name_first like '".trim($tmp[1])."%'";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 3) )
								$filter_err = "Specify at least 3 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}

					break;
					case 'case_no':
						$phFilters[] = "ce.encounter_nr = ".$db->qstr($v);
					break;
	//				case 'pid':
	//					$phFilters[] = "o.pid REGEXP ".$db->qstr($v);
	//				break;
	//				case 'patient':
	//					$phFilters[] = "o.pid=".$db->qstr($v);
	//				break;
	//				case 'walkin':
	//					$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
	//				break;
	//				case 'area':
	//					$phFilters[] = 'pharma_area='.$db->qstr($v);
	//				break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}

		if (empty($phFilters)) {
			$phFilters[] = "discharge_date = DATE(NOW())";
		}

		$phWhere=implode(") AND (",$phFilters);
		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";

		// Removed: a.) cpi.insurance_nr;
		//				  b.) where ((ce.is_discharged <> 0) OR (ce.encounter_type = ".DIALYSIS_ENCOUNTER_TYPE.")) and ($phWhere)

		$this->sql = "select SQL_CALC_FOUND_ROWS
		                 (SELECT insurance_nr FROM care_person_insurance cpi WHERE cpi.pid = ce.pid AND cpi.hcare_id = ".$hcare_id.") insurance_nr,
		                 (case when isnull(sem.memcategory_id) then '' else sem.memcategory_id end) as categ_id,
										 (case when isnull(sm.memcategory_desc) then 'NONE' else sm.memcategory_desc end) as categ_desc,
										 (select concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', date_format(concat((case when discharge_date is null or discharge_date = '' then '0000-00-00' else discharge_date end), ' ', (case when discharge_time is null or discharge_time = '' then '00:00:00' else discharge_time end)), '%b %e, %Y %l:%i%p')) as prd
											 from care_encounter as ce1 where ce1.encounter_nr = ce.encounter_nr) as confine_period,
										 ce.encounter_nr, name_last, name_first, name_middle,
										 (select sum(total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_d1_coverage + total_d2_coverage + total_d3_coverage + total_d4_coverage + total_msc_coverage) as tclaim
											from seg_billing_coverage as sbc3 inner join seg_billing_encounter as sbe3 on sbc3.bill_nr = sbe3.bill_nr
											where sbc3.hcare_id = ".$hcare_id." and sbe3.encounter_nr = ce.encounter_nr and sbe3.is_deleted IS NULL) as this_coverage,
											(SELECT sben.bill_dte FROM seg_billing_encounter sben WHERE sben.encounter_nr = ce.encounter_nr AND sben.is_final = '1' AND sben.is_deleted IS NULL) AS bill_dte
									from (care_encounter ce inner join care_person cp on ce.pid = cp.pid)
											 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id)
											 on ce.encounter_nr = sem.encounter_nr
									WHERE ce.is_discharged = 1 AND ($phWhere)
									   AND EXISTS (SELECT * FROM seg_encounter_insurance sei WHERE sei.encounter_nr = ce.encounter_nr AND sei.hcare_id = ".$hcare_id." ORDER BY create_dt DESC LIMIT 1)
										 AND EXISTS (select * from seg_billing_encounter as sbe where sbe.encounter_nr = ce.encounter_nr and sbe.is_deleted IS NULL and sbe.is_final = 1 ORDER BY bill_dte DESC LIMIT 1)
										 AND NOT EXISTS (select * from seg_transmittal_details as std where std.encounter_nr = ce.encounter_nr and std.is_rejected = 0)
									order by discharge_date asc
									limit $offset, $rowcount";
// var_dump($this->sql);exit();
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function addDiagnosis($enc_nr, $diagnosis, $entry_no, $user_id) {
		global $db;

		if ($entry_no == 0) {
			// Save final diagnosis as specified by billing clerk ...
//			$fldArray = array('encounter_nr'=>"'$enc_nr'", 'description'=>"'$diagnosis'", 'is_deleted'=>"0", 'modify_id'=>"'$user_id'");
//			return ($db->Replace('seg_encounter_diagnosis', $fldArray, array('encounter_nr', 'entry_no')));

			$this->sql = "insert into seg_encounter_diagnosis (encounter_nr, description, is_deleted, modify_id)  \n
							 values('$enc_nr', '$diagnosis', '0', '$user_id')";
			return($this->Transact($this->sql));
		}
		else {
			// Mark 'deleted' alternate description if a blank description is saved ...
			$this->sql = "update seg_encounter_diagnosis set
							 is_deleted = 1,
							 modify_id = '$user_id',
							 description = '',
							 modify_time = now()
							 where encounter_nr = '$enc_nr'
								and entry_no = '$entry_no'";
			return($this->Transact($this->sql));
		}
	}

	function delDiagnosis($enc_nr, $entry_no = 0, $user_id) {
		// Mark 'deleted'
//		$this->sql = "update seg_encounter_diagnosis set
//						 is_deleted = 1,
//						 modify_id = '$user_id',
//						 modify_time = now()
//						 where encounter_nr = '$enc_nr'
//							and entry_no = '$entry_no'";
		$this->sql = "delete from seg_encounter_diagnosis
						 where encounter_nr = '$enc_nr'
							and entry_no = '$entry_no'";
		return($this->Transact($this->sql));
	}

	function updateDiagnosis($enc_nr, $entry_no = 0, $diagnosis, $user_id) {
		// Update the diagnosis ...
		$this->sql = "update seg_encounter_diagnosis set
						 description = '$diagnosis',
						 modify_id = '$user_id',
						 modify_time = now()
						 where encounter_nr = '$enc_nr'
							and entry_no = '$entry_no'";
		return($this->Transact($this->sql));
	}

	function getDiagnosisList($encounter_nr='', $maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		$this->sql = "SELECT entry_no, description
						 FROM seg_encounter_diagnosis as sd
						 WHERE sd.encounter_nr = '$encounter_nr'
							AND sd.is_deleted = 0
						 ORDER BY entry_no";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

    //added by jasper 04/04/2013
    function getPreviousBillAmt($encnr, $billnr='') {
        global $db;
            $strSQL = "select SQL_CALC_FOUND_ROWS sbe.bill_nr, bill_dte, sbe.encounter_nr, cp.name_last, cp.name_first, cp.name_middle, ".
                     "      (total_acc_charge + total_med_charge + total_sup_charge + total_srv_charge + total_ops_charge + total_doc_charge + total_msc_charge) ".
                     "         as total_charge, (select sum(discount) as tdiscount from seg_billing_discount as sbd ".
                     "                                                 where sbd.bill_nr = sbe.bill_nr) as total_discount, ".
                     "      (select sum(total_acc_discount + total_med_discount + total_sup_discount + total_srv_discount + total_ops_discount + ".
                     "             total_d1_discount + total_d2_discount + total_d3_discount + total_d4_discount + total_msc_discount) ".
                     "             as tcomputed from seg_billingcomputed_discount as sbcd where sbcd.bill_nr = sbe.bill_nr) as total_computed_discount, ".
                     "      (select sum(total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + ".
                     "             total_d1_coverage + total_d2_coverage + total_d3_coverage + total_d4_coverage + total_msc_coverage) ".
                     "             as tcoverage from seg_billing_coverage as sbc ".
                     "          where sbc.bill_nr = sbe.bill_nr) as total_coverage, ".
                     "      (select count(*) as bill_count from seg_billing_encounter as sbe1 ".
                     "          where sbe1.encounter_nr = sbe.encounter_nr and sbe1.bill_dte > sbe.bill_dte) as later_billings ".
                     "   from ($this->coretable as sbe inner join care_encounter as ce on sbe.encounter_nr = ce.encounter_nr) ".
                     "      inner join care_person as cp on ce.pid = cp.pid ";
        if ($billnr != "") {
            $strSQL .= "where sbe.encounter_nr = '" . $encnr . "' and sbe.bill_nr < '" . $billnr . "'";
        } else {
            $strSQL .= "where sbe.encounter_nr = '" . $encnr . "'";
        }
        $strSQL .= " and sbe.is_deleted IS NULL";
        if($this->result=$db->Execute($strSQL)) {
            if ($this->result->RecordCount()){
                return $this->result;
            } else {
                return false;
            }
        }
    }
    //added by jasper 04/04/2013

    //added by jasper 04/16/2013
    function isSponsoredMember($enc_nr='') {
        if ($this->memcategory == '') {
            $this->memcategory = $this->getMemCategoryDesc($enc_nr);
        }
        return (!(strpos(strtoupper($this->memcategory), SPONSORED, 0) === false));
    }

    function getMemCategoryDesc($enc_nr='') {
        global $db;

        $s_desc= "";
        //$filter = '';

        //if ($this->prev_encounter_nr != '') $filter = " or sem.encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select memcategory_desc ".
                    "from seg_memcategory as sm inner join seg_encounter_memcategory as sem ".
                    "on sm.memcategory_id = sem.memcategory_id ".
                    "where sem.encounter_nr = '". $enc_nr . "'";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $s_desc = $row['memcategory_desc'];
                }
            }
        }

        return $s_desc;
    }
    //added by jasper 04/16/2013

    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
    function isWellBaby($enc_nr) {
        global $db;

        $enc_type = 0;
        $strSQL = "select encounter_type ".
                            "   from care_encounter ".
                            "   where encounter_nr = '".$enc_nr."'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == WELLBABY);
    }
    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY

    function GetTypeBilling($bill_nr){
    	global $db;
    	$rs="";
    	$strSQL = "SELECT bill_nr 
    				FROM seg_billing_encounter_details
    				WHERE bill_nr = '$bill_nr'";
    	if($result=$db->Execute($strSQL)){
    		if($result->RecordCount()){
    			if($row=$result->FetchRow()){
    				$rs=$row['bill_nr'];
    			}
    		}
    	}
    	return $rs;
    }

    /**
     * added by rnel 08-30-2016
     */
    public function setAsDeleted($enc_nr) {
    	global $db;

    	$sql = "UPDATE seg_credit_collection_ledger
                SET is_deleted = '1'
                WHERE encounter_nr = ".$db->qstr($enc_nr)."
                AND control_nr = 'MSS applied'";
        if($db->Execute($sql)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * end
     */

}
?>