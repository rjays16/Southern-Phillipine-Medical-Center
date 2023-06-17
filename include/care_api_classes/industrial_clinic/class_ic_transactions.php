<?php
	define(ICDeptNr, '138'); #dept_nr for IC
    define(FamedDeptNr, '133'); #dept_nr for IC
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');

	class SegICTransaction extends Core {

		var $tb_transaction = "seg_industrial_transaction";
		var $tb_encounter = "care_encounter";

		var $fld_transaction =
			array (
				'refno',
				'trxn_date',
				'encounter_nr',
				'pid',
				'purpose_exam',
				'remarks',
				'agency_charged',
				'agency_id',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt',
				'history',
				'status'
			);


		function SegICTransaction()
		{
			$this->useTransaction();
		}

		function useTransaction()
		{
			$this->coretable = $this->tb_transaction;
			$this->ref_array = $this->fld_transaction;
		}

		function getNewId(){
				global $db;
				$id = date('Y').'000001';
				$temp_id = date('Y')."%";
				$this->sql='SELECT max(refno) as refno FROM '.$this->tb_transaction;
				$this->result=$db->Execute($this->sql);
				$row=$this->result->FetchRow();
				if($row["refno"]>0){
					$nextID=$row["refno"] + 1;
					return $nextID;
				}else{ return $id;}
		}

		function saveTransaction($data){
			global $db;
			$enc_obj = new Encounter();
			$this->setDataArray($data);
			#$enc_obj->setCurrentDept($data['encounter_nr'],ICDeptNr);
            $enc_obj->setCurrentDept($data['encounter_nr'],FamedDeptNr);
			return $this->insertDataFromInternalArray();
		}

		function updateTransaction($data,$item){
				global $db;

				if(!empty($data['agency_id']))
					$strAgencyID="`agency_id`=".$data['agency_id'].",";

				$this->sql="UPDATE seg_industrial_transaction SET
											`trxn_date`='".$data['trxn_date']."',
											`encounter_nr`='".$data['encounter_nr']."',
											`pid`='".$data['pid']."',
											`purpose_exam`='".$data['purpose_exam']."',
											`remarks`='".$data['remarks']."',
											`agency_charged`='".$data['agency_charged']."',
											".$strAgencyID."
											`modify_id`='".$data['modify_id']."',
											`modify_dt`='".$data['modify_dt']."',
											`create_id`='".$data['create_id']."',
											`create_dt`='".$data['create_dt']."',
											`history`='".$data['history']."',
											`status`='".$data['status']."'
											WHERE refno='$item' ";
				return $db->Execute($this->sql);
		}



		# get fetchCompanies
		function fetchCompanyNames($keyword,$pid) {
			global $db;


			$this->keyword=trim($keyword);
			$calc_rows = "";
			$this->sql="select  sic.company_id as company_id,
													sic.`name` as company_name,
													sic.address,
													a.employee_id,
													a.position,
													a.job_status
										 from seg_industrial_company as sic
													inner join seg_industrial_comp_emp as a on a.company_id=sic.company_id
										where a.pid='$pid'
											AND sic.`name` like '$this->keyword%' order by sic.`name` asc";
			$this->result=$db->Execute($this->sql);
			if($this->result) {
				return $this->result;
			} else {
				return FALSE;
			}

		}

		function fetchAllCompanyNames ($keyword) {
			global $db;


			$this->keyword=trim($keyword);
			$calc_rows = "";
			$this->sql="select  sic.company_id as company_id,
													sic.`name` as company_name,
													sic.address
										 from seg_industrial_company as sic
										where
											 sic.`name` like '$this->keyword%'
											 AND sic.status <> 'deleted'
												 order by sic.`name` asc";
			$this->result=$db->Execute($this->sql);
			if($this->result) {
				return $this->result;
			} else {
				return FALSE;
			}

		}

		# end function fetchCompanies

		//Added by Marvin Cortes 05/16/2016 for SPMC-608
		function getHsscRecord($pid){
			global $db;
			$pid=$db->qstr($pid);
			$this->sql="SELECT 			COUNT(*) AS counts,
										ce.encounter_nr AS casenumber
						FROM 			care_encounter AS ce
						INNER JOIN 		seg_industrial_transaction AS sit ON ce.encounter_nr = sit.encounter_nr
						WHERE 			ce.encounter_type='6'
						AND 			ce.is_discharged = '0' 
						AND 			DATE(ce.encounter_date) = DATE(NOW())
						AND 			ce.pid = ".$pid;
	          if ($this->result=$db->GetRow($this->sql)){
	      			return $this->result;
	    		}
	    			else return false;    
			}
		#function to getnew Encounter nr
		function getNew_Encounter_nr(){
			global $db;

			$enc_obj = new Encounter($encounter_nr);
			$enc_type = $db->GetOne("SELECT type_nr FROM care_type_encounter WHERE name like 'Industrial_Clinic%' AND status <> 'deleted'");
			#$last_enc = $enc_obj->getLastEncounterNr("industrial_clinic");#commented by art 02/14/2014
			$last_enc = $enc_obj->getLastEncounterNr("ic");#added by art 02/14/2014
			$new_encounter = $enc_obj->getNewEncounterNr($last_enc,$enc_type);
			$dataEncounter['encounter_nr']=$new_encounter;
			$dataEncounter['encounter_type']=$enc_type;
			$dataEncounter['encounter_class_nr']=$enc_type;
			return $dataEncounter;
		}
		#end function get new Encounter nr

		function insertNew_Encounter_nr($data){
			global $db;

			$enc_obj = new Encounter($encounter_nr);
			$enc_obj->setDataArray($data);
			return $enc_obj->insertDataFromInternalArray();
		}
                
                # added by gervie 06/11/2015
                function update_encounter($data){
			global $db;

			//$enc_obj = new Encounter();
			//$enc_obj->setDataArray($data);
			//return $enc_obj->update_encounter_history($data['encounter_nr'], $data['smoker_history'], $data['drinker_history']);
                        //return $enc_obj->updateEncounterFromInternalArray($data['encounter_nr']);
                        $this->sql = "UPDATE care_encounter
                                      SET drinker_history='".$data['drinker_history']."', smoker_history='".$data['smoker_history']."' WHERE encounter_nr='".$data['encounter_nr']."' ";
                        //var_dump($this->sql);
                        //die;
                        return $db->Execute($this->sql);
		}
                # end gervie
                

		function update_Encounter_tracker($encounter_nr){
			global $db;
			$enc_obj = new Encounter();
			return $enc_obj->update_Encounter_Tracker($encounter_nr,'ic');
		}


		# get fetchTransaction
		function fetchTransaction($keyword) {
			global $db;

			$this->keyword=trim($keyword);
			$calc_rows = "";
			$this->sql="select sit.pid as patient_id,
										sit.refno,
										sit.encounter_nr as case_no,
										fn_get_pid_lastfirstmi(sit.pid) as full_name
									from seg_industrial_transaction as sit
									where sit.encounter_nr like '".$keyword."%'
									";
			$this->result=$db->Execute($this->sql);
			if($this->result) {
				return $this->result;
			} else {
				return FALSE;
			}

		}
		# end function fetchTransaction


		#function get transaction record
		function getTransactionData($refno){
			global $db;

			$this->sql="SELECT sic.`name`, sic.`company_id`, sice.`job_status`, sice.`position`,
							sice.`employee_id`, fn_get_pid_lastfirstmi(sit.`pid`) AS full_name,
							SUBSTRING(sit.`remarks`, 1, 2000) AS remark, sit.*
						FROM seg_industrial_transaction AS sit
						LEFT JOIN seg_industrial_company AS sic  ON sic.`company_id` = sit.`agency_id`
						LEFT JOIN seg_industrial_comp_emp AS sice ON sice.`pid` = sit.`pid` AND sice.`company_id` = sic.`company_id`
						WHERE sit.`refno` = " . $db->qstr($refno);

			$this->result=$db->Execute($this->sql);
			if($this->result) {
				return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		}
		#end function

	 /**
		* @internal     Return the recordset of industrial clinic transactions given the filter.
		* @access       public
		* @author       Bong S. Trazo
		* @package      modules
		* @subpackage   billing
		*
		* @param        filters, offset, rowcount
		* @return       recordset if successful, FALSE otherwise.
		*/
		function getTransactions($filters, $offset=0, $rowcount=15) {
				global $db;

				if (!$offset) $offset = 0;
				if (!$rowcount) $rowcount = 15;

				if (is_array($filters)) {
						foreach ($filters as $i=>$v) {
								switch (strtolower($i)) {
										case 'datetoday':
												$phFilters[] = 'DATE(trxn_date)=DATE(NOW())';
										break;
										case 'datethisweek':
												$phFilters[] = 'YEAR(trxn_date)=YEAR(NOW()) AND WEEK(trxn_date)=WEEK(NOW())';
										break;
										break;
										case 'datethismonth':
												$phFilters[] = 'YEAR(trxn_date)=YEAR(NOW()) AND MONTH(trxn_date)=MONTH(NOW())';
										break;
										case 'date':
												$phFilters[] = "DATE(trxn_date)='$v'";
										break;
										case 'datebetween':
												#$phFilters[] = "trxn_date>='".$v[0]."' AND trxn_date<='".$v[1]."'";
												$phFilters[] = "trxn_date BETWEEN '".$v[0]."' AND '".$v[1]."'";
										break;
										case 'patient':
											if (is_numeric($v))
												$phFilters[] = "sit.pid LIKE '".$v."%'";
												// $phFilters[] = "sit.pid = '".$v."%'";
											else
												$phFilters[] = "fn_get_pid_lastfirstmi(sit.pid) LIKE '".$v."%'";
										break;
										case 'case_no':
												$phFilters[] = "sit.encounter_nr LIKE '".$v."%'";
												// $phFilters[] = "sit.encounter_nr = '".$v."%'";
										break;
								}
						}
				}

				$phFilters[] = "(sit.status NOT IN ('".DELETED."')  OR sit.status IS NULL) ";
				$phWhere=implode(") AND (",$phFilters);
				if ($phWhere) $phWhere = "($phWhere)";
				else $phWhere = "1";
				#edited by art 06/29/2014 added SQL_CALC_FOUND_ROWS ,desc
				$this->sql="select SQL_CALC_FOUND_ROWS distinct sic.name, sic.company_id, sice.job_status, sice.position, sice.employee_id,
													fn_get_pid_lastfirstmi(sit.pid) as full_name, sit.*
											 from seg_industrial_transaction as sit left join
													(seg_industrial_company as sic inner join seg_industrial_comp_emp as sice on sic.company_id = sice.company_id)
													on sit.agency_id = sic.company_id AND sice.pid = sit.pid
													where ($phWhere)
													order by sit.trxn_date DESC
													limit $offset, $rowcount";
				if ($this->result = $db->Execute($this->sql))
						return $this->result;
				else
						return false;
		}

	 /**
		* @internal     Logically delete the industrial clinic transaction where refno = @strans_no.
		* @access       public
		* @author       Bong S. Trazo
		* @package      modules
		* @subpackage   industrial clinic
		*
		* @param        strans_no (reference no.), suser_id
		* @return       status of update.
		*/
		function deleteICTransaction($strans_no, $suser_id) {
			$this->startTrans();
			$this->sql = "UPDATE ".$this->tb_transaction." SET
											 status = '2',
											 history =".$this->ConcatHistory("DELETED ".date('Y-m-d H:i:s')." ".$suser_id."\n").",
											 modify_id = '".$suser_id."'
											 WHERE refno = '".$strans_no."'";
			if ($bSuccess = $this->Transact()) {
				$this->sql = "UPDATE ".$this->tb_encounter." SET
												 status = '".DELETED."',
												 history =".$this->ConcatHistory("DELETED ".date('Y-m-d H:i:s')." ".$suser_id."\n").",
												 modify_id = '".$suser_id."'
												 WHERE encounter_nr = (select encounter_nr from ".$this->tb_transaction." where refno = '".$strans_no."')";
				$bSuccess = $this->Transact();
			}
			if (!$bSuccess) $this->failTrans();
			$this->completeTrans();
			return $bSuccess;
		}

		#added by angelo m.
		#modified by EJ 12/17/2014
		function getPersonData($pid){
			global $db;

			$this->sql="SELECT 
						  pid,
						  fn_get_person_lastname_first (pid) AS full_name,
						  date_birth,
						  age,
						  sex,
						  photo_filename,
						  civil_status,
						  fn_get_complete_address2 (pid) AS address,
						  street_name,
						  citizenship
						FROM
						  care_person 
						WHERE pid = '$pid'";
			$this->result=$db->Execute($this->sql);
			if($this->result){return $this->result->FetchRow();}
			else
				return false;
		}

		function fetchPersonNames($company_id,$strName='',$offset=0, $rowcount=10){
			global $db;


			$strName=str_replace(".","",$strName);
			$name=split(",",$strName);
			$name_last=trim($name[0]);
			$name_firstMiddle=trim($name[1]);

			if(!empty($strName))
			$cond="where cp.name_last LIKE '".$name_last."%' and cp.name_first LIKE '".$name_firstMiddle."%' ";
			else
				$cond="";
			$this->sql="select  cp.pid,
													fn_get_person_lastname_first(cp.pid) as full_name,
													cp.sex,
													cp.name_last,
													cp.name_first,
													cp.name_middle,
													cp.date_birth
												from care_person as cp
													 inner join seg_industrial_comp_emp as sice
														 on sice.pid=cp.pid
																and sice.company_id='".$company_id."'
																and sice.status!='deleted'
												 ".$cond.
												" order by cp.name_last asc
													limit $offset, $rowcount ";
			$this->result=$db->Execute($this->sql);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

	 /**
		* @internal     Retrieve the outstanding industrial clinic accounts given the cut-off date or particular account id.
		* @access       public
		* @author       Bong S. Trazo
		* @package      modules
		* @subpackage   industrial clinic
		*
		* @param        filters, offset, rowcount
		* @return       recordset if successful, FALSE otherwise.
		*/
		function getOutstandingICAccounts($filters, $offset=0, $rowcount=15, $bbilled=false) {
			global $db;

			if (!$offset) $offset = 0;
			if (!$rowcount) $rowcount = 15;

			if (is_array($filters)) {
					foreach ($filters as $i=>$v) {
							switch (strtolower($i)) {
									case 'datetoday':
											$dtFilter = 'DATE(trxn_date) <= DATE(NOW())';
									break;
									case 'date':
											$dtFilter = "DATE(trxn_date) <= '$v'";
									break;
									case 'account_name':
											$accFilter = "AND NAME LIKE '".$v."%'";
									break;
									case 'account_no':
										if (is_numeric($v)) {
											$accFilter = "AND sic.company_id LIKE '".$v."%'";
										}
									break;
							}
					}
			}

			// $this->sql = "SELECT company_id, hosp_acct_no, short_id, NAME,
			// 									(SELECT COUNT(DISTINCT t.encounter_nr) AS n_trxns
			// 										 FROM seg_industrial_transaction t LEFT JOIN seg_industrial_bill_d b
			// 												ON t.encounter_nr = b.encounter_nr
			// 										 WHERE ".$dtFilter."
			// 												AND b.encounter_nr IS NULL
			// 												AND t.agency_id = a.company_id
			// 												AND (t.status NOT IN ('2') OR t.status = '0')
			// 												AND t.agency_charged = 1) AS count_unbilled,
			// 									(SELECT COUNT(DISTINCT t.encounter_nr) AS n_trxns
			// 										 FROM seg_industrial_transaction t INNER JOIN seg_industrial_bill_d b
			// 												ON t.encounter_nr = b.encounter_nr
			// 										 WHERE ".$dtFilter."
			// 												AND t.agency_id = a.company_id
			// 												AND (t.status NOT IN ('2') OR t.status = '0')
			// 												AND t.agency_charged = 1) AS count_billed, 1 AS is_company
			// 								FROM seg_industrial_company a INNER JOIN seg_industrial_transaction t
			// 									 ON a.company_id = t.agency_id
			// 								WHERE (SELECT COUNT(DISTINCT t.encounter_nr)
			// 										 FROM seg_industrial_transaction t LEFT JOIN seg_industrial_bill_d b
			// 												ON t.encounter_nr = b.encounter_nr
			// 										 WHERE ".$dtFilter."
			// 												AND b.encounter_nr IS ".($bbilled ? "NOT" : "")." NULL
			// 												AND t.agency_id = a.company_id
			// 												AND (t.status NOT IN ('2')  AND t.status = '0')
			// 												AND t.agency_charged = 1) > 0
			// 									 AND (t.status NOT IN ('2')  AND t.status = '0')
			// 									 AND t.agency_charged = 1 ".$accFilter[0]."
			// 								UNION
			// 								SELECT p.pid, '' AS hosp_acct_no, '' AS short_id, CONCAT(IFNULL(name_last, ''), ', ', IFNULL(name_first, ''), IFNULL(name_middle,'')) AS NAME,
			// 									(SELECT COUNT(DISTINCT t.encounter_nr) AS n_trxns
			// 										 FROM seg_industrial_transaction t LEFT JOIN seg_industrial_bill_d b
			// 												ON t.encounter_nr = b.encounter_nr
			// 										 WHERE ".$dtFilter."
			// 												AND b.encounter_nr IS NULL
			// 												AND t.pid = p.pid
			// 												AND (t.status NOT IN ('2')  AND t.status = '0')
			// 												AND t.agency_charged = 0) AS count_unbilled,
			// 									(SELECT COUNT(DISTINCT t.encounter_nr) AS n_trxns
			// 										 FROM seg_industrial_transaction t INNER JOIN seg_industrial_bill_d b
			// 												ON t.encounter_nr = b.encounter_nr
			// 										 WHERE ".$dtFilter."
			// 												AND t.pid = p.pid
			// 												AND (t.status NOT IN ('2')  AND t.status = '0')
			// 												AND t.agency_charged = 0) AS count_billed, 0 AS is_company
			// 								FROM care_person p INNER JOIN seg_industrial_transaction t
			// 									 ON p.pid = t.pid
			// 								WHERE (SELECT COUNT(DISTINCT t.encounter_nr)
			// 										 FROM seg_industrial_transaction t LEFT JOIN seg_industrial_bill_d b
			// 												ON t.encounter_nr = b.encounter_nr
			// 										 WHERE ".$dtFilter."
			// 												AND b.encounter_nr IS ".($bbilled ? "NOT" : "")." NULL
			// 												AND t.pid = p.pid
			// 												AND (t.status NOT IN ('2')  AND t.status = '0')
			// 												AND t.agency_charged = 0) > 0
			// 									 AND (t.status NOT IN ('2')  AND t.status = '0')
			// 									 AND t.agency_charged = 0 ".$accFilter[1]."
			// 								ORDER BY NAME
			// 								LIMIT $offset, $rowcount";

			$this->sql = 'SELECT DISTINCT 
							  sit.agency_id,
							  sic.short_id,
							  sic.name,
							  sic.company_id 
							FROM
							  seg_industrial_transaction AS sit 
							  INNER JOIN seg_industrial_company AS sic 
							    ON sic.company_id = sit.agency_id 
							WHERE sit.agency_charged = 1 '.$accFilter;
							
			$this->result=$db->Execute($this->sql);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		function getBilledCompany($filters, $company_id){
			global $db;

			if (is_array($filters)) {
					foreach ($filters as $i=>$v) {
							switch (strtolower($i)) {
									case 'datetoday':
											$dtFilter = 'DATE(trxn_date) <= DATE(NOW())';
									break;
									case 'date':
											$dtFilter = "DATE(trxn_date) <= '$v'";
									break;
							}
					}
			}

			if (empty($dtFilter)) $dtFilter = 'DATE(trxn_date) <= DATE(NOW())';

			$this->sql = "SELECT 
							    COUNT(DISTINCT sit.encounter_nr) AS billed 
							  FROM
							    seg_industrial_transaction AS sit 
							  WHERE ".$dtFilter."
							    AND sit.agency_charged = 1 
							    AND sit.status = 1
							    AND sit.agency_id = ".$db->qstr($company_id);

			$this->result=$db->Execute($this->sql);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		function getUnbilledCompany($filters, $company_id){
			global $db;

			if (is_array($filters)) {
					foreach ($filters as $i=>$v) {
							switch (strtolower($i)) {
									case 'datetoday':
											$dtFilter = 'DATE(trxn_date) <= DATE(NOW())';
									break;
									case 'date':
											$dtFilter = "DATE(trxn_date) <= '$v'";
									break;
							}
					}
			}

			if (empty($dtFilter)) $dtFilter = 'DATE(trxn_date) <= DATE(NOW())';

			$this->sql = "SELECT 
							    COUNT(DISTINCT sit.encounter_nr) AS unbilled
							  FROM
							    seg_industrial_transaction AS sit 
							  WHERE ".$dtFilter."
							    AND sit.agency_charged = 1 
							    AND sit.status = 0
							    AND sit.agency_id = ".$db->qstr($company_id);

			$this->result=$db->Execute($this->sql);
			if($this->result){
				return $this->result;
			}
			else return false;

		}

		# Added by James 3/8/2014
		# ============================================

		# Functions below are for Populate Bill functionalities
		# For getting unbilled employees
		function getUnbilledEmployees($agency_id, $date) {
			global $db;

			if($date == "Today")
			{
				$date_clause = "NOW()";
			}
			else
			{
				$date = DATE("Y-m-d H:i:s", strtotime($date));
				$date_clause = "'".$date."'";
			}

			$this->sql = $db->prepare("SELECT 
											  sit.refno,
											  sit.trxn_date,
											  cp.name_first,
											  cp.name_last,
											  cp.name_middle 
											FROM
											  seg_industrial_transaction AS sit 
											  INNER JOIN care_person AS cp 
											    ON cp.pid = sit.pid 
											WHERE sit.agency_charged = 1 
											  AND DATE(sit.trxn_date) <= DATE(".$date_clause.")
											  AND sit.agency_id = ? 
											  AND sit.status = 0");

			$this->result=$db->Execute($this->sql, $agency_id);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		# For counting unbilled employees
		function countUnbilledEmployees($agency_id) {
			global $db;

			$this->sql = $db->prepare("SELECT 
										  COUNT(sit.refno) AS count
										FROM
										  seg_industrial_transaction AS sit 
										  INNER JOIN care_person AS cp 
										    ON cp.pid = sit.pid 
										WHERE sit.agency_charged = 1 
										  AND sit.agency_id = ? 
										  AND sit.status = 0");

			$this->result=$db->Execute($this->sql, $agency_id);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		# For getting unbilled employees
		#modified by art 05/17/2014 added company
		function getBilledEmployees($agency_id) {
			global $db;
			$param = array($agency_id,$agency_id);
			$this->sql = $db->prepare("SELECT 
										  sibh.*,
										  c.name,
										  billdisc.discount_amount,
										  (
										    SUM(sibd.total_charges) - IFNULL(
										      (SELECT 
										        discount_amount 
										      FROM
										        seg_industrial_bill_discount 
										      WHERE bill_nr = sibh.bill_nr
										      AND company_id = ?),
										      0
										    )
										  ) AS total 
										FROM
										  seg_industrial_bill_h AS sibh 
										  LEFT JOIN seg_industrial_bill_d AS sibd 
										    ON sibh.bill_nr = sibd.bill_nr 
										  LEFT JOIN seg_industrial_company AS c 
    										ON sibh.company_id = c.company_id
    									  LEFT JOIN seg_industrial_bill_discount AS billdisc 
										    ON sibh.bill_nr = billdisc.bill_nr 
										    AND sibh.company_id = billdisc.company_id 
										WHERE sibh.company_id = ?
										GROUP BY sibh.bill_nr
										ORDER BY bill_nr DESC");
			$this->result=$db->Execute($this->sql, $param);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		# For getting unbilled employees filter by date
		function getBilledAgenciesByDate($agency_id, $date) {
			global $db;

			$fomatted_date = DATE("Y-m-d H:i:s", strtotime("+1 day", strtotime($date)));

			$params = array($agency_id, $fomatted_date);

			$this->sql = $db->prepare("SELECT 
										  sibh.bill_nr,
										  sibh.bill_rundate,
										  (
										    SUM(sibd.total_charges) - IFNULL(
										      (SELECT 
										        discount_amount 
										      FROM
										        seg_industrial_bill_discount 
										      WHERE bill_nr = sibh.bill_nr
										      AND company_id = ".$db->qstr($agency_id)."),
										      0
										    )
										  ) AS total
										FROM
										  seg_industrial_bill_h AS sibh 
										  LEFT JOIN seg_industrial_bill_d AS sibd 
										    ON sibh.bill_nr = sibd.bill_nr 
										WHERE sibh.company_id = ?
										AND sibh.bill_rundate <= ?
										GROUP BY sibh.bill_nr
										ORDER BY bill_nr DESC");

			$this->result=$db->Execute($this->sql, $params);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		# Functions below are for Generate Bill functionalities
		# For generating fina bill - bill h (Contains: bill_nr, bill_rundate, etc ...)
		function generateBillH($bill_nr, $bill_rundate, $bill_cutoff, $agency_id) {
			global $db;

			$this->sql = "INSERT INTO seg_industrial_bill_h (bill_nr, bill_rundate, cutoff_date, company_id)
								VALUES
								  (".$db->qstr($bill_nr).",
								  ".$db->qstr($bill_rundate).",
								  ".$db->qstr($bill_cutoff).",
								  ".$db->qstr($agency_id).")";

			$this->result=$db->Execute($this->sql);
			if($this->result) return true;
			else return false;
		}

		# For generating fina bill - bill d (Contains: bill_nr, encounter_nr, etc ...)
		function generateBillD($bill_nr, $encounter_nr, $total_med_charge, $total_sup_charge, $total_srv_charge, $total_msc_charge, $total_charges) {
			global $db;

			$this->sql = "INSERT INTO
							seg_industrial_bill_d
						  VALUES (
						  	".$db->qstr($bill_nr).",
						  	".$db->qstr($encounter_nr).",
						  	".$db->qstr($total_med_charge).",
						  	".$db->qstr($total_sup_charge).",
						  	".$db->qstr($total_srv_charge).",
						  	".$db->qstr($total_msc_charge).",
						  	".$db->qstr($total_charges).")";

			$this->result=$db->Execute($this->sql);
			if($this->result) return true;
			else return false;
		}

		# Function below are for Update/Delete Bill functionalities
		# For unpdating billed employees or deleted to 1 for billed and 2 for deleted
		function updateEmployee($refno, $type) {
			global $db;

			if($type == 0)
			{
				$filter = '1';
			}
			else
			{
				$filter = '0';
			}

			$this->sql = "UPDATE seg_industrial_transaction
							 SET status = ".$filter."
						   WHERE refno = ".$db->qstr($refno);

			$this->result=$db->Execute($this->sql);
			if($this->result) return true;
			else return false;
		}

		# For Deleting Bill
		function deleteBill($bill_nr, $type) {
			global $db;

			if($type == 0)
			{
				$this->sql = "SELECT 
								  encounter_nr 
								FROM
								  seg_industrial_bill_d 
								WHERE bill_nr = ".$db->qstr($bill_nr);

				$this->result=$db->Execute($this->sql);
				if($this->result) return $this->result;
				else return false;
			}
			else
			{	
				$this->sql = "DELETE FROM seg_industrial_bill_discount
							   WHERE bill_nr = ".$db->qstr($bill_nr);

				$this->result=$db->Execute($this->sql);
				if(!$this->result) return false;

				$this->sql = "DELETE FROM seg_industrial_bill_h
							   WHERE bill_nr = ".$db->qstr($bill_nr);

				$this->result=$db->Execute($this->sql);
				if(!$this->result) return false;
				else return true;
			}
		}

		# Functions below are for Discount functionality
		# For checking if discount exist with given bill_nr 
		function checkDiscountBillNr($bill_nr, $agency_id) {
			global $db;

			$params = array($bill_nr, $agency_id);

			$this->sql = $db->prepare("SELECT 
										  COUNT(discount_percentage) as count 
										FROM
										  seg_industrial_bill_discount 
										WHERE bill_nr = ?
										AND company_id = ?");

			$this->result=$db->Execute($this->sql, $params);
			if($this->result) return $this->result;
			else return false;
		}

		# For updating discount informations
		function updateDiscount($process_type, $bill_nr, $agency_id, $discount, $remarks ,$discount_amount) {
			global $db;
			//added by art 05/27/2014
			if($process_type == 0)
			{
				/*$this->sql = "UPDATE 
								  seg_industrial_bill_discount 
								SET
								  discount_amount = ".$db->qstr($discount).",
								  discount_remarks = ".$db->qstr($remarks)."
								WHERE bill_nr = ".$db->qstr($bill_nr)."
								AND company_id = ".$db->qstr($agency_id);*/

				$this->sql = $db->Prepare('UPDATE 
											  seg_industrial_bill_discount 
											SET
											  discount_percentage = ?,
											  discount_remarks = ?
											WHERE bill_nr = ?
											AND company_id = ?');
				$params = array($discount, $remarks, $bill_nr, $agency_id);
			}
			else
			{
				/*$this->sql = "UPDATE 
								  seg_industrial_bill_discount 
								SET
								  is_generated = 1
								WHERE bill_nr = ".$db->qstr($bill_nr)."
								AND company_id = ".$db->qstr($agency_id);*/

				$this->sql = $db->Prepare('UPDATE
											  seg_industrial_bill_discount 
											SET
											  is_generated = ?,
											  discount_amount = ?
											WHERE bill_nr = ?
											AND company_id = ?');
				$params = array(1,$discount_amount,$bill_nr,$agency_id);
			}
			//end art
			$this->result=$db->Execute($this->sql,$params);
			if($this->result) return true;
			else return false;
		}

		# For adding discount informations
		function createDiscount($bill_nr, $agency_id, $discount, $remarks, $user) {
			global $db;
			/*
			$this->sql = "INSERT INTO seg_industrial_bill_discount 
							VALUES
							  (
							    ".$db->qstr($bill_nr).",
							    ".$db->qstr($agency_id).",
							    ".$db->qstr($discount).",
							    ".$db->qstr($remarks).",
							    DATE(NOW()),
							    '".$user."',
							    '0')";
			*/
			//added by art 05/27/2014
			$date  = date('Y-m-d');
			$generated = 0;
			$params = array($bill_nr, $agency_id, $discount, $remarks, $date, $user, $generated);
			$this->sql = $db->Prepare('INSERT INTO seg_industrial_bill_discount (bill_nr, company_id, discount_percentage, discount_remarks, grant_dte, personell_nr, is_generated) VALUES (?,?,?,?,?,?,?)');
			//end art
			$this->result=$db->Execute($this->sql,$params);
			if($this->result) return true;
			else return false;
		}

		# For retreiving discount informations
		function getDiscount($bill_nr, $agency_id) {
			global $db;

			$params = array($bill_nr, $agency_id);

			$this->sql = $db->prepare("SELECT 
										  discount_percentage, discount_remarks
										FROM
										  seg_industrial_bill_discount 
										WHERE bill_nr = ?
										AND company_id = ?");

			$this->result=$db->Execute($this->sql, $params);
			if($this->result) return $this->result;
			else return false;
		}

		# End James
		# ============================================

		function getMaxTrxnDte($acct_id, $is_corpacct, $cutoff_dte) {
			global $db;

			$filter = ($is_corpacct) ? "t.agency_id = '$acct_id' AND t.agency_charged = 1" : "t.pid = '$acct_id' AND t.agency_charged = 0";
			$this->sql = "SELECT DATE(MAX(trxn_date)) AS refdte
											FROM seg_industrial_transaction t LEFT JOIN seg_industrial_bill_d d ON t.encounter_nr = d.encounter_nr
											WHERE $filter
												 #AND DATE(trxn_date) <= DATE('$cutoff_dte')
												 AND t.status = 0
												 AND d.encounter_nr IS NULL";
			$this->result=$db->Execute($this->sql);
			if($this->result){
				if ($row = $this->result->FetchRow())
					return $row["refdte"];
				else
					return false;
			}
			else return false;
		}

		function isBillNrExists($billnr) {
			global $db;

			$this->sql = "SELECT * FROM seg_industrial_bill_h WHERE bill_nr = '$billnr'";
			if ($this->result = $db->Execute($this->sql)) {
				return ($this->result->RecordCount()) ? true : false;
			}
			else return false;
		}

		function getICTransactionsToBill($acct_id, $is_corpacct, $cutoff_dte) {
			global $db;

			$filter = ($is_corpacct) ? "t.agency_id = '$acct_id' AND t.agency_charged = 1" : "t.pid = '$acct_id' AND t.agency_charged = 0";
			$this->sql = "SELECT t.*
											FROM seg_industrial_transaction t LEFT JOIN seg_industrial_bill_d d ON t.encounter_nr = d.encounter_nr
											WHERE $filter
												 AND DATE(trxn_date) <= DATE('$cutoff_dte')
												 AND t.status = 0
												 AND d.encounter_nr IS NULL
												 ORDER BY trxn_date";
			$this->result=$db->Execute($this->sql);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		function deleteLastBill($acct_id, $is_corpacct) {
			global $db;

			if ($is_corpacct)
				$this->sql = "DELETE
												FROM seg_industrial_bill_h
												WHERE company_id = '".$acct_id."'
														AND (request_flag IS NULL or request_flag = '')
												ORDER BY cutoff_date DESC
												LIMIT 1";
			else
				$this->sql = "DELETE
												FROM seg_industrial_bill_h
												WHERE pid = '".$acct_id."'
														AND (request_flag IS NULL or request_flag = '')
												ORDER BY cutoff_date DESC
												LIMIT 1";
			return $this->Transact();
		}

		/**
		* added by VAN 03-01-2011
		* Checks if the company id exists in the database.
		* @access public
		* @param varchar company_id
		* @return varchar
		*/
		function getCompanyInfo($hosp_id){
		global $db;

			$this->sql ="SELECT * FROM seg_industrial_company WHERE short_id='$hosp_id';";

			if ($this->result=$db->Execute($this->sql)) {
					 $this->count=$this->result->RecordCount();
					 return $this->result->FetchRow();
				} else{
					 return FALSE;
				}
		}
		#-------------

		/*added by art 04/15/2014
		**Get IC exam purpose
		*/
		function getExamPurpose($id){
			global $db;
			$this->sql = $db->Prepare('SELECT name FROM seg_industrial_purpose WHERE id =?');
			if ($this->result= $db->Execute($this->sql, $id)){
				$row = $this->result->FetchRow();
				return $row['name'];
			}else{
				return FALSE;
			}
		}
		
		function getExamPurposeOthers($refno){
			global $db;
			$this->sql = $db->Prepare('SELECT name FROM seg_industrial_purpose_others WHERE refno =?');
			if ($this->result= $db->Execute($this->sql, $refno)){
				$row = $this->result->FetchRow();
				return $row['name'];
			}else{
				return FALSE;
			}
		}
		

		function saveExamPurposeOthers($refno,$name,$mode){
			global $db;
			if ($mode == 'save') {
				$param = array($refno,$name);
				$this->sql = $db->Prepare('INSERT INTO seg_industrial_purpose_others (refno,name) VALUES (?,?)');
			}else{
				$check = $db->Prepare('SELECT * FROM seg_industrial_purpose_others WHERE refno = ?');
				$result = $db->Execute($check,$refno);
				if ($result->RecordCount()) {
					$param = array($name,$refno);
					$this->sql = $db->Prepare('UPDATE seg_industrial_purpose_others SET name = ? WHERE refno = ?');
					$db->Execute($this->sql,$param);
				}else{
					$param = array($refno,$name);
					$this->sql = $db->Prepare('INSERT INTO seg_industrial_purpose_others (refno,name) VALUES (?,?)');
					$db->Execute($this->sql,$param);
				}
			}
		}

		function isDischarged($refno){
			global $db;
			$this->sql = $db->Prepare('SELECT ce.is_discharged FROM seg_industrial_transaction AS sit LEFT JOIN care_encounter AS ce ON sit.encounter_nr = ce.encounter_nr WHERE sit.refno = ?');
			if ($this->result= $db->Execute($this->sql, $refno)){
				$row = $this->result->FetchRow();
				return $row['is_discharged'];
			}else{
				return FALSE;
			}
		}
		#end art
		#added by art 05/18/2014
		function isSeniorCitizen($pid){
			global $db;
			$date  = date('Y-m-d H:i:s');
			$param = array($pid,'SC','0.20000000','0',$date);
			$this->sql = $db->Prepare('INSERT INTO seg_charity_grants_pid (pid,discountid,discount,sw_nr,grant_dte) VALUES (?,?,?,?,?)');
			$db->Execute($this->sql,$param);
		}
		#end art

        //added by Nick 06-23-2014
        function setSeniorCitizenDiscount($pid)
        {
            global $db;
            $this->sql = "SELECT date_birth FROM care_person WHERE pid = " . $db->qstr($pid);
            $bday = $db->GetOne($this->sql);
            if ($bday) {
                $age = floor((time() - strtotime($bday))/31556926); #added by art 02/20/2014
                /*$d1 = date_create($bday);
                $d2 = date_create(date('Y-m-d'));
                $d3 = date_diff($d1, $d2);
                $age = $d3->y;*/
                if ($age >= 60) {
                    $fields = array(
                        'pid'        => $db->qstr($pid),
                        'discountid' => $db->qstr('SC'),
                        'discount'   => 0.2,
                        'sw_nr'      => 0,
                        'grant_dte'  => 'NOW()'
                    );

                    $pk = array(
                        'pid'
                    );

                    $db->Replace('seg_charity_grants_pid', $fields, $pk);
                }
            }
        }

		/**
		 * @author Nick B. Alcala 05-19-2014
		 * Query by James Malatabon
		 * Get patient's examinations totals
		 * @return resultset/boolean
		 */
		function getPatientExaminationsTotals($enc_nr){
			global $db;
			// $db->debug = true;
			$this->sql = "SELECT
			(SELECT SUM(ld.price_cash*ld.quantity)
				FROM seg_lab_servdetails AS ld
				INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='LB' AND ld.status<>'deleted') AS lab_total_charge,
			(SELECT SUM(ld.price_cash*ld.quantity)
				FROM seg_lab_servdetails AS ld
				INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='IC' AND ld.status<>'deleted') AS iclab_total_charge,
			(SELECT SUM(ld.price_cash*ld.quantity)
				FROM seg_lab_servdetails AS ld
				INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='BB' AND ld.status<>'deleted') AS bb_total_charge,
			-- (SELECT SUM(ld.price_cash*ld.quantity)
			-- 	FROM seg_lab_servdetails AS ld
			-- 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			-- 	WHERE l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='SPL' AND l.status<>'deleted') AS splab_total_charge,
			-- (SELECT SUM(ld.price_cash*ld.quantity)
			-- 	FROM seg_lab_servdetails AS ld
			-- 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
			-- 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='IC' AND l.status<>'deleted') AS splab_total_charge,
			(SELECT SUM(b.price_cash * b.quantity) AS total
				FROM seg_lab_serv AS a 
				  INNER JOIN seg_lab_servdetails AS b ON a.refno = b.refno 
				  INNER JOIN seg_lab_services AS c ON c.service_code = b.service_code 
				  INNER JOIN seg_lab_service_groups AS d ON c.group_code = d.group_code 
				WHERE a.encounter_nr = '$enc_nr' AND d.group_code IN ('SPL', 'SPC') AND b.status<>'deleted' AND a.is_cash = 0) AS splab_total_charge, /* added by Nick 05-19-2014 */
			(SELECT SUM(rd.price_cash)
				FROM care_test_request_radio AS rd
				INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
				WHERE rd.is_served = 1 AND r.encounter_nr = '$enc_nr' AND r.is_cash=0 AND rd.status<>'deleted') AS radio_total_charge,
			(SELECT SUM(ph.pricecash*ph.quantity)
				FROM seg_pharma_order_items AS ph
				INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
				WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area IN ('AMB', 'BB', 'ER', 'IP', 'MG', 'MHC', 'OR', 'WD') ) AS ip_total_charge,
			(SELECT SUM(ph.pricecash*ph.quantity)
				FROM seg_pharma_order_items AS ph
				INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
				WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='MG' ) AS mg_total_charge,
			(SELECT SUM(ph.pricecash*ph.quantity)
				FROM seg_pharma_order_items AS ph
				INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
				WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND ph.is_deleted <> 1 AND p.pharma_area IN ('AMB', 'BB', 'ER', 'IP', 'MG', 'MHC', 'OR', 'WD')) AS pharma_total_charge,
			(SELECT SUM(md.chrg_amnt*md.quantity)
				FROM seg_misc_service_details AS md
				INNER JOIN seg_misc_service AS m ON m.refno=md.refno
				WHERE m.encounter_nr='$enc_nr' AND m.is_cash=0 ) AS misc_total_charge";

			$rs = $db->Execute($this->sql);
			return $rs;
		}



		function getBill($agency_id) {
			global $db;
			$this->sql = $db->prepare("SELECT 
										  sibh.*,
										  c.name,
										  billdisc.discount_amount,
										  (
										    SUM(sibd.total_charges)
										  ) AS total 
										FROM
										  seg_industrial_bill_h AS sibh 
										  LEFT JOIN seg_industrial_bill_d AS sibd 
										    ON sibh.bill_nr = sibd.bill_nr 
										  LEFT JOIN seg_industrial_company AS c 
    										ON sibh.company_id = c.company_id
    									  LEFT JOIN seg_industrial_bill_discount AS billdisc 
										    ON sibh.bill_nr = billdisc.bill_nr 
										    AND sibh.company_id = billdisc.company_id 
										WHERE sibh.company_id = ?
										GROUP BY sibh.bill_nr
										ORDER BY bill_nr DESC");
			$this->result=$db->Execute($this->sql, $agency_id);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		/*added by art 06/06/2014*/
		#ref_source = ('LB', 'IC', 'BB', 'SPL')
	
		function getLabExams($enc_nr , $discount, $ref_source){
			global $db;
			$params = array($discount,$ref_source ,$enc_nr);
			$this->sql = $db->Prepare("SELECT 
										  a.`encounter_nr`,
										  a.`ref_source`,
										  a.`create_dt`,
										  b.`service_code`,
										  b.`quantity`,
										  c.`name`,
										  b.`price_cash`,   
										  c.`is_socialized`,
										  IF(
										    c.`is_socialized` = 1,
										    b.`price_cash` * (? / 100),
										    0
										  ) AS discount
										FROM
										  seg_lab_serv AS a 
										  LEFT JOIN seg_lab_servdetails AS b 
										    ON b.`refno` = a.`refno` 
										  LEFT JOIN seg_lab_services AS c 
										    ON c.`service_code` = b.`service_code` 
										  LEFT JOIN seg_lab_service_groups AS d 
										    ON d.`group_code` = c.`group_code` 
										WHERE a.`is_cash` = 0 
										  #AND a.`ref_source`  IN ('LB', 'IC', 'BB', 'SPL')
										  AND b.`is_served` = 1 
										  AND a.`ref_source` = ? 
										  AND b.`status` <> 'deleted' 
										  AND a.`encounter_nr` = ?");

			$this->result=$db->Execute($this->sql, $params);
			if($this->result){
				return $this->result;
			}
			else return false;
		}
		/*edited by art 06/20/2014*/
		function getOtherLabs($enc_nr , $discount){
			global $db;
			$params = array($discount,$enc_nr,$discount,$enc_nr);
			$this->sql = $db->Prepare("SELECT 
										  a.`encounter_nr`,
										  a.`ref_source`,
										  a.`create_dt`,
										  b.`service_code`,
										  b.`quantity`,
										  b.`price_cash`,
										  c.`is_socialized`,
										  IF(
										    c.`is_socialized` = 1,
										    b.`price_cash` * (? / 100),
										    0
										  ) AS discount 
										FROM
										  seg_lab_serv AS a 
										  INNER JOIN seg_lab_servdetails AS b 
										    ON a.refno = b.refno 
										  INNER JOIN seg_lab_services AS c 
										    ON c.service_code = b.service_code 
										  INNER JOIN seg_lab_service_groups AS d 
										    ON c.group_code = d.group_code 
										WHERE a.encounter_nr = ?
										  AND d.group_code IN ('SPL', 'SPC') 
										  AND b.`status` <> 'deleted'
										  AND a.`is_cash` = 0 
										  AND b.`is_served` = 1
										UNION
										SELECT 
										  a.`encounter_nr`,
										  a.`ref_source`,
										  a.`create_dt`,
										  b.`service_code`,
										  b.`quantity`,
										  b.`price_cash`,
										  c.`is_socialized`,
										  IF(
										    c.`is_socialized` = 1,
										    b.`price_cash` * (? / 100),
										    0
										  ) AS discount 
										FROM
										  seg_lab_serv AS a 
										  INNER JOIN seg_lab_servdetails AS b 
										    ON a.refno = b.refno 
										  INNER JOIN seg_lab_services AS c 
										    ON c.service_code = b.service_code 
										  INNER JOIN seg_lab_service_groups AS d 
										    ON c.group_code = d.group_code 
										WHERE a.encounter_nr = ? 
										  AND a.ref_source = 'BB' 
										  AND b.`is_served` = 1 
										  AND b.`status` <> 'deleted' 
										  AND a.`is_cash` = 0 ");
			$this->result=$db->Execute($this->sql, $params);
			if($this->result){
				return $this->result;
			}
			else return false;
		}


		function getPharmacy($enc_nr,$discount){
			global $db;
			$params = array($discount,$enc_nr);
			$this->sql = $db->Prepare("SELECT 
										  a.`encounter_nr`,
										  a.`pharma_area`,
										  a.`orderdate`,
										  b.`bestellnum`,
										  b.`quantity`,
										  c.`artikelname` AS NAME,
										  --c.`price_cash`,
										  b.`pricecash` as price_cash,
										  c.`is_socialized`,
										  IF(
										    c.`is_socialized` = 1,
										    b.`pricecash` * (?/ 100),
										   0
										  ) AS discount 
										FROM
										  seg_pharma_orders AS a 
										  LEFT JOIN seg_pharma_order_items AS b 
										    ON b.`refno` = a.`refno` 
										  LEFT JOIN care_pharma_products_main AS c 
										    ON c.`bestellnum` = b.`bestellnum` 
										WHERE b.`serve_status` = 'S' 
										  AND a.`is_cash` = 0
										  AND a.`is_deleted` <> 1 
										  AND a.`pharma_area` IN ('AMB', 'BB', 'ER', 'IP', 'MG', 'MHC', 'OR', 'WD') 
										  AND a.`encounter_nr` = ?");

			$this->result=$db->Execute($this->sql, $params);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		function getRadio($enc_nr,$discount){
			global $db;
			$params = array($discount,$enc_nr);
			$this->sql = $db->Prepare("SELECT 
										  a.`encounter_nr`,
										  a.`request_date`,
										  b.`service_code`,
										  b.`price_cash`,
										  c.`name`,
										  c.`is_socialized`,
										  IF(
										    c.`is_socialized` = 1,
										    b.`price_cash` * ( ? / 100),
										   0
										  ) AS discount 
										FROM
										  seg_radio_serv AS a 
										  LEFT JOIN care_test_request_radio AS b 
										    ON b.`refno` = a.`refno` 
										  LEFT JOIN seg_radio_services AS c 
										    ON c.`service_code` = b.`service_code` 
										WHERE b.`is_served` = 1 
										  AND a.`is_cash` = 0 
										  AND b.`status` <> 'deleted' 
										  AND a.`encounter_nr` = ?");
			$this->result=$db->Execute($this->sql, $params);
			if($this->result){
				return $this->result;
			}
			else return false;
		}


		function getMisc($enc_nr,$discount){
			global $db;
			$params = array($discount,$enc_nr);
			$this->sql = $db->Prepare("SELECT 
										  a.`encounter_nr`,
										  a.`create_dt`,
										  b.`service_code`,
										  b.`quantity`,
										  c.`name`,
										  b.`chrg_amnt`,
										  c.`is_not_socialized`,
										  c.`name_short`,
										  IF(
										    c.`is_not_socialized` = 0,
										    b.`chrg_amnt` * (? / 100),
										   0
										  ) AS discount 
										FROM
										  seg_misc_service AS a 
										  LEFT JOIN seg_misc_service_details AS b 
										    ON b.`refno` = a.`refno` 
										  LEFT JOIN seg_other_services AS c 
										    ON c.`alt_service_code` = b.`service_code` 
										WHERE a.`is_cash` = 0 
										  AND a.`encounter_nr` = ?");

			$this->result=$db->Execute($this->sql, $params);
			if($this->result){
				return $this->result;
			}
			else return false;
		}

		function getEnc($refno){
			global $db;

			$this->sql = $db->Prepare ("SELECT pid,encounter_nr,trxn_date FROM seg_industrial_transaction WHERE refno=?");

			if ($this->result= $db->Execute($this->sql, $refno)){
				#$row = $this->result->FetchRow();
				return $row = $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}

		function getName($id){
			global $db;
			$this->sql = $db->Prepare("SELECT fn_get_person_name(pid) AS fullname 
								FROM seg_industrial_transaction
							   WHERE refno=?");

			if ($this->result= $db->Execute($this->sql, $id)){
				$row = $this->result->FetchRow();
				return $row['fullname'];
			}else{
				return FALSE;
			}
		}

		function getBillnr($maxdate){
			global $db;
			$this->sql = $db->Prepare("SELECT fn_get_ic_billing_nr_new(DATE(?)) AS bill_nr");

			if ($this->result= $db->Execute($this->sql, $maxdate)){
				$row = $this->result->FetchRow();
				return $row['bill_nr'];
			}else{
				return FALSE;
			}

		}
		#end art


		/**
		 * Added by: Art 06/10/2014
		 * copied from cashier module by nick
		 * Reference : http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
		 * Convert number to words
		 * @param $number Integer
		 * @return String
		 */
		function convert_number_to_words($number) {
		    
		    $hyphen      = '-';
		    $conjunction = ' ';// ' and ';
		    $separator   = ', ';
		    $negative    = 'negative ';
		    $decimal     = ' point ';
		    $dictionary  = array(
		        0                   => 'zero',
		        1                   => 'one',
		        2                   => 'two',
		        3                   => 'three',
		        4                   => 'four',
		        5                   => 'five',
		        6                   => 'six',
		        7                   => 'seven',
		        8                   => 'eight',
		        9                   => 'nine',
		        10                  => 'ten',
		        11                  => 'eleven',
		        12                  => 'twelve',
		        13                  => 'thirteen',
		        14                  => 'fourteen',
		        15                  => 'fifteen',
		        16                  => 'sixteen',
		        17                  => 'seventeen',
		        18                  => 'eighteen',
		        19                  => 'nineteen',
		        20                  => 'twenty',
		        30                  => 'thirty',
		        40                  => 'forty',
		        50                  => 'fifty',
		        60                  => 'sixty',
		        70                  => 'seventy',
		        80                  => 'eighty',
		        90                  => 'ninety',
		        100                 => 'hundred',
		        1000                => 'thousand',
		        1000000             => 'million',
		        1000000000          => 'billion',
		        1000000000000       => 'trillion',
		        1000000000000000    => 'quadrillion',
		        1000000000000000000 => 'quintillion'
		    );
		    
		    if (!is_numeric($number)) {
		        return false;
		    }
		    
		    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		        // overflow
		        trigger_error(
		            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
		            E_USER_WARNING
		        );
		        return false;
		    }

		    if ($number < 0) {
		        return $negative . $this->convert_number_to_words(abs($number));
		    }
		    
		    $string = $fraction = null;
		    
		    if (strpos($number, '.') !== false) {
		        list($number, $fraction) = explode('.', $number);
		    }
		    
		    switch (true) {
		        case $number < 21:
		            $string = $dictionary[$number];
		            break;
		        case $number < 100:
		            $tens   = ((int) ($number / 10)) * 10;
		            $units  = $number % 10;
		            $string = $dictionary[$tens];
		            if ($units) {
		                $string .= $hyphen . $dictionary[$units];
		            }
		            break;
		        case $number < 1000:
		            $hundreds  = $number / 100;
		            $remainder = $number % 100;
		            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
		            if ($remainder) {
		                $string .= $conjunction . $this->convert_number_to_words($remainder);
		            }
		            break;
		        default:
		            $baseUnit = pow(1000, floor(log($number, 1000)));
		            $numBaseUnits = (int) ($number / $baseUnit);
		            $remainder = $number % $baseUnit;
		            $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
		            if ($remainder) {
		                $string .= $remainder < 100 ? $conjunction : $separator;
		                $string .= $this->convert_number_to_words($remainder);
		            }
		            break;
		    }
		    
		    if (null !== $fraction && is_numeric($fraction)) {
		        $string .= $decimal;
		        $words = array();
		        foreach (str_split((string) $fraction) as $number) {
		            $words[] = $dictionary[$number];
		        }
		        $string .= implode(' ', $words);
		    }
		    
		    return $string;
		}

		/**
		 * Added by: Art 06/10/2014
		 * copied from cashier module by nick
		 * Convert number to String
		 * @param  $money Int/Double
		 * @return String
		 */
		function getMoneyInWords($money){
			if(is_numeric($money) && floor($money) != $money){
				$decimal = floor($money);
				$fraction = round($money - $decimal,2) * 100;
				$money_str = $this->convert_number_to_words($decimal) . " Peso/s and ";
				$money_str .= $this->convert_number_to_words($fraction) . " Centavo/s Only";
			}else{
				$money_str = $this->convert_number_to_words($money) . " Peso/s Only";
			}
			return $money_str;
		}

		//added by art 06/10/2014
		function getEncBilled($bill_nr){
			global $db;

			$this->sql = $db->Prepare("SELECT 
							   sibd.encounter_nr,
							   sibh.cutoff_date 
							   FROM seg_industrial_bill_h AS sibh
							   INNER JOIN seg_industrial_bill_d AS sibd
								ON sibh.bill_nr=sibd.bill_nr
							   WHERE sibh.bill_nr=?");
			$this->result=$db->Execute($this->sql, $bill_nr);
			if($this->result){
				return $this->result;
			}
			else return false;

		}
		//end
		//added by art 06/24/2014
		function isCharge($encounter_nr){
			global $db;
			$this->sql = $db->Prepare("SELECT 
								  agency_charged 
								FROM
								  seg_industrial_transaction 
								WHERE encounter_nr = ?");
			$this->result=$db->Execute($this->sql, $encounter_nr);
			
			if ($row=$this->result->FetchRow()) {
				return $row['agency_charged'];
			}else{
				return false;
			}
		}
		//end

        //added by art 06/24/2014
        function getPhysician($dr_nr){
            global $db;

            $this->sql = $db->Prepare("SELECT
                                          cpl.nr AS Personnel_num,
                                          `fn_get_person_name` (cp.`pid`) AS dr_name,
                                          cpl.`job_function_title` AS JobPosition
                                        FROM
                                          care_person AS cp
                                          INNER JOIN `care_personell` AS cpl
                                            ON cpl.`pid` = cp.`pid`
                                          INNER JOIN `seg_industrial_cert_med` AS sic
                                            ON sic.`dr_nr_med` = cpl.`nr`
                                        WHERE cp.`status` NOT IN (
                                            'deleted',
                                            'hidden',
                                            'inactive',
                                            'void'
                                          )
                                          AND sic.refno = ?");
            $this->result=$db->Execute($this->sql, $dr_nr);

            if ($row=$this->result->FetchRow()) {
                return $row['dr_name'];
            }else{
                return '';
            }
        }
        //end

        //added by Macoy, July 11,2014
        function getEncoder($id){
			global $db;
			$this->sql = $db->Prepare("SELECT name FROM care_users WHERE login_id = ?");
			$this->result=$db->Execute($this->sql, $id);
			
			if ($row=$this->result->FetchRow()) {
				return $row['name'];
			}else{
				return false;
			}
		}
		//end

		function getICtransDetails($enc){
			global $db;
			$this->sql = $db->Prepare("SELECT *, b.`name` AS agency FROM seg_industrial_transaction a INNER JOIN seg_industrial_company b ON a.`agency_id` =b.`company_id` WHERE encounter_nr =?");
			$this->result= $db->Execute($this->sql,$enc);

			if ($row=$this->result->FetchRow()) {
				return $row;
			}else{
				return false;
			}
		}


#added by art 08/07/2014
		function getTrxns($frm,$to){
			global $db;
			$params = array($frm,$to);
			$this->sql = $db->Prepare(" SELECT 
											`fn_get_person_name`(sit.`pid`) AS person_name,
											sit.`trxn_date`,
											sit.`encounter_nr`,
											SUM((SELECT SUM(amount_due) FROM seg_pay_request WHERE or_no = sp.`or_no` AND ref_source = 'LD'))AS LD,
											SUM((SELECT SUM(amount_due) FROM seg_pay_request WHERE or_no = sp.`or_no` AND ref_source = 'RD'))AS RD,
											SUM((SELECT SUM(amount_due) FROM seg_pay_request WHERE or_no = sp.`or_no` AND ref_source = 'PH'))AS PH,
											SUM((SELECT SUM(amount_due) FROM seg_pay_request WHERE or_no = sp.`or_no` AND ref_source = 'MISC'))AS MISC,
											SUM((SELECT SUM(amount_due) FROM seg_pay_request WHERE or_no = sp.`or_no` AND ref_source = 'OTHER'))AS OTHER,
											(SELECT SUM(slsd.`price_cash_orig` * slsd.`quantity`)FROM seg_lab_serv AS sls INNER JOIN seg_lab_servdetails AS slsd  ON sls.`refno` = slsd.`refno` 
												WHERE sls.`encounter_nr` = sit.`encounter_nr` AND slsd.`is_served` = 1 AND sls.`is_cash` <> 1 AND sls.`status` <> 'deleted')AS CLD,
											(SELECT SUM(b.`price_cash`) FROM seg_radio_serv AS a INNER JOIN care_test_request_radio AS b  ON a.`refno` = b.`refno` WHERE a.`encounter_nr` = sit.`encounter_nr` AND b.`is_served` = 1 
												AND a.`is_cash` <> 1 AND a.`status` <> 'deleted') AS CRD,
											(SELECT SUM(spo.`amount_due`) FROM seg_pharma_orders AS spo WHERE spo.`encounter_nr`=  sit.`encounter_nr` AND spo.`is_deleted` <> 1 AND spo.`is_cash` <> 1 AND spo.`serve_status` = 'S') AS CPH,
											(SELECT SUM(smsd.`chrg_amnt`) AS misc FROM seg_misc_service AS sms  INNER JOIN seg_misc_service_details AS smsd ON sms.`refno` = smsd.`refno` 
												WHERE sms.`encounter_nr` = sit.`encounter_nr` AND sms.`is_cash` <> 1 ) AS CMISC
										FROM seg_industrial_transaction AS sit 
										LEFT JOIN seg_pay AS sp ON sit.`encounter_nr` = sp.`encounter_nr`
										WHERE DATE(sit.`trxn_date`) BETWEEN ? AND ? GROUP BY sit.`encounter_nr`");

			if ($this->result= $db->Execute($this->sql, $params)){
				return $this->result;
			}else{
				return FALSE;
			}
		}
		#end


		#added by art 02/03/15 for spmc 144
		function getICtrxns($frm,$to){
			global $db;
			$this->sql = "SELECT 
						  		`fn_get_person_name`(sit.`pid`) AS person_name,
						  		sit.`trxn_date`,
						  		sit.`encounter_nr`
						  FROM seg_industrial_transaction AS sit 
						  LEFT JOIN seg_pay AS sp ON sit.`encounter_nr` = sp.`encounter_nr`
						  WHERE DATE(sit.`trxn_date`) BETWEEN ".$db->qstr($frm)." AND ".$db->qstr($to)." GROUP BY sit.`encounter_nr`";
			$this->result = $db->GetAll($this->sql);
			if ($this->result) {
				return $this->result;
			}else{
				return FALSE;
			}
		}
		#added by art 02/03/15 for spmc 144
		function getICincomeCash($encounter_nr){
			global $db;
			$this->sql = "SELECT spr.`or_no`,spr.`ref_source`,spr.`service_code`,spr.`qty`,spr.`amount_due` FROM seg_pay sp LEFT JOIN seg_pay_request spr ON sp.`or_no` = spr.`or_no` WHERE sp.`cancel_date` IS NULL AND sp.`encounter_nr` = ".$db->qstr($encounter_nr);
			$this->result = $db->GetAll($this->sql);
			if ($this->result) {
				return $this->result;
			}else{
				return FALSE;
			}
		}
		#added by art 02/03/15 for spmc 144
		function getChargedLab($encounter_nr){
			global $db;
			$this->sql ="SELECT 
	                      slsd.`price_charge` AS price,
	                      slsd.`quantity` AS qty,
	                      (
	                        slsd.`quantity` * slsd.`price_charge`
	                      ) AS total,
	                      slsd.`service_code` AS service_code 
	                    FROM
	                      seg_lab_serv AS sls 
	                      INNER JOIN seg_lab_servdetails AS slsd 
	                        ON sls.`refno` = slsd.`refno`
	                        AND slsd.`status` <> 'deleted'
	                    WHERE sls.`encounter_nr` = ".$db->qstr($encounter_nr)."
	                      AND slsd.`is_served` = 1 
	                      AND sls.`is_cash` <> 1 
	                      AND sls.`status` <> 'deleted'";
	        $this->result = $db->GetAll($this->sql);
			if ($this->result) {
				return $this->result;
			}else{
				return FALSE;
			}
		}
		#added by art 02/03/15 for spmc 144
		function getChargedRadio($encounter_nr){
			global $db;
			$this->sql = "SELECT 
                           b.`price_cash` AS price,
                           '1' AS qty,
                           b.`price_cash` AS total,
                           b.`service_code` AS service_code 
                         FROM
                           seg_radio_serv AS a 
                           INNER JOIN care_test_request_radio AS b 
                             ON a.`refno` = b.`refno` 
                             AND b.`status` <> 'deleted'
                         WHERE a.`encounter_nr` = ".$db->qstr($encounter_nr)."
                           AND b.`is_served` = 1 
                           AND a.`is_cash` <> 1 
                           AND a.`status` <> 'deleted'";
            $this->result = $db->GetAll($this->sql);
			if ($this->result) {
				return $this->result;
			}else{
				return FALSE;
			}
		}
		#added by art 02/03/15 for spmc 144
		function getChargedPh($encounter_nr){
			global $db;
			$this->sql = "SELECT 
                           spoi.`price_orig` AS price,
                           spoi.`quantity` AS qty,
                           (
                             spoi.`quantity` * spoi.`price_orig`
                           ) AS total,
                           spoi.`bestellnum` AS service_code 
                         FROM
                           seg_pharma_orders AS spo 
                           INNER JOIN `seg_pharma_order_items` spoi 
                             ON spo.`refno` = spoi.`refno` 
                             AND spoi.`is_deleted` <> 1
                         WHERE spo.`encounter_nr` = ".$db->qstr($encounter_nr)."
                           AND spo.`is_deleted` <> 1 
                           AND spo.`is_cash` <> 1 
                           AND spo.`serve_status` = 'S'";
            $this->result = $db->GetAll($this->sql);
			if ($this->result) {
				return $this->result;
			}else{
				return FALSE;
			}
		}
		#added by art 02/03/15 for spmc 144
		function getChargedMisc($encounter_nr){
			global $db;
			$this->sql = "SELECT 
                           smsd.`chrg_amnt` AS price,
                           smsd.`quantity` AS qty,
                           (
                             smsd.`chrg_amnt` * smsd.`quantity`
                           ) AS total,
                           smsd.`service_code` AS service_code 
                         FROM
                           seg_misc_service AS sms 
                           INNER JOIN seg_misc_service_details AS smsd 
                             ON sms.`refno` = smsd.`refno` 
                         WHERE sms.`encounter_nr` = ".$db->qstr($encounter_nr)."
                           AND sms.`is_cash` <> 1 ";
            $this->result = $db->GetAll($this->sql);
			if ($this->result) {
				return $this->result;
			}else{
				return FALSE;
			}
		}

}#end class
?>