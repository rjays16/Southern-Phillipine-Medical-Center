<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class SegBloodBank extends Core {

	/**
	* Database table for the discount data
	* @var string
	*/
	var $tb_blood_request='seg_blood_request';

	var $tb_seg_blood_monitoring='seg_blood_monitoring';
	/**
	* Reference number
	* @var string
	*/
	var $refno;

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $result;

	/**
	* Resulting record count
	* @var int
	*/
	var $count;

	/**
	* Fieldnames of the care_appointment table.
	* @var array
	*/
	var $fld_blood_request=array(
		"refno",
		"request_date",
		"request_time",
		"encounter_nr",
		"pid",
		"is_cash",
		"type_charge",
		"is_urgent",
		"doctor_nr",
		"diagnosis",
		"blood_type",
		"blood_source",
		"blood_component",
		"date_needed",
		"time_needed",
		"no_of_units",
		"when_prev_transfusion",
		"where_prev_transfusion",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt",
		"history",
		"comments",
		"ordername",
		"orderaddress",
		"status",
		"discountid",
		"loc_code",
		"parent_refno",
		"approved_by_head",
		"remarks",
		"headID",
		"headpasswd"
		);

	var $fld_seg_blood_monitoring=array(
				'pid',
				'refno',
				'service_code',
				'blood_type',
				'component',
				'ordering',
				'ordered_qty',
				'serial_no',
				'status',
				'date_created',
				'date_released',
				'date_received',
				'date_started',
				'date_done',
				'date_issuance',
				'date_return',
				'date_reissue',
				'date_consumed',
				'create_id',
				'create_dt',
				'modify_id',
				'modify_dt'
				
			);	

	/**
	* Constructor
	* @param string refno
					*/
	function SegBloodBank(){
		$this->setTable($this->tb_blood_request);
		$this->setRefArray($this->fld_blood_request);
	}

	/**
	* Sets the core object to point to seg_discount and corresponding field names.
	*/
	function useBloodBank(){
		$this->coretable=$this->tb_blood_request;
		$this->ref_array=$this->fld_blood_request;
	}

	function _useBloodMonitoring(){
		$this->coretable= $this->tb_seg_blood_monitoring;
		$this->ref_array= $this->fld_seg_blood_monitoring;
	}

	function getAllBloodComponents($blood_component){
		global $db;

		if ($blood_component)
			$where = "WHERE lis_code = '".$blood_component."'";

		$this->sql="SELECT * FROM seg_blood_component ".$where." ORDER BY name";

		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}

	function getAllBloodType($blood_type){
		global $db;

		if ($blood_type)
			$where = "WHERE code = '".$blood_type."'";

		$this->sql="SELECT * FROM seg_blood_type ".$where." ORDER BY name";

		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	function countSearchService($group_code, $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area='') {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($group_code)
			$grp_cond = "AND s.group_code='".$group_code."' ";
		else
			$grp_cond = "";

		if ($area=='ER')
			$area_cond = " AND is_ER=1 ";
		else
			$area_cond = "";

		if ($multiple){
			$keyword = $searchkey;
			 /*AND s.group_code='".$group_code."'*/
			$this->sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
									WHERE s.group_code=g.group_code
							$grp_cond
							AND (s.service_code IN (".$keyword.")
							OR s.name IN (".$keyword.") /*OR s.code_num IN (".$keyword.")*/)
							AND s.status NOT IN (".$this->dead_stat.")
							$area_cond
							ORDER BY s.name";
		}else{
			# convert * and ? to % and &
			$searchkey=strtr($searchkey,'*?','%_');
			$searchkey=trim($searchkey);
			#$suchwort=$searchkey;
			$searchkey = str_replace("^","'",$searchkey);
			$keyword = addslashes($searchkey);

			$this->sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
									WHERE s.group_code=g.group_code
								$grp_cond
										AND (s.service_code LIKE '%".$keyword."%'
									OR s.name LIKE '%".$keyword."%' OR s.code_num LIKE '%".$keyword."%')
									AND s.status NOT IN (".$this->dead_stat.")
								$area_cond
									ORDER BY s.name";
		}
		#-----------------
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchService($group_code, $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area=''){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($group_code)
				$grp_cond = " AND s.group_code='".$group_code."' ";
			else
				$grp_cond = "";

		if ($area=='ER')
			$area_cond = " AND is_ER=1 ";
		else
			$area_cond = "";

		if ($multiple){
			$keyword = $searchkey;
			$this->sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
									WHERE s.group_code=g.group_code
							$grp_cond
							AND (s.service_code IN (".$keyword.")
							OR s.name IN (".$keyword.") /*OR s.code_num IN (".$keyword.")*/)
							AND s.status NOT IN (".$this->dead_stat.")
							$area_cond
							ORDER BY s.name";
		}else{
			# convert * and ? to % and &
			$searchkey=strtr($searchkey,'*?','%_');
			$searchkey=trim($searchkey);
			#$suchwort=$searchkey;
			$searchkey = str_replace("^","'",$searchkey);
			$keyword = addslashes($searchkey);

			$this->sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
									WHERE s.group_code=g.group_code
								$grp_cond
										AND (s.service_code LIKE '%".$keyword."%'
									OR s.name LIKE '%".$keyword."%' OR s.code_num LIKE '%".$keyword."%')
									AND s.status NOT IN (".$this->dead_stat.")
								$area_cond
									ORDER BY s.name";
		}


		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	#added by VAN 04-20-2010
	function isServiceAPackage($service_code){
				global $db;

				$this->sql="SELECT count(service_code_child) AS count_child
											FROM seg_lab_group AS lg
											WHERE service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$row=$this->result->FetchRow();
					$this->count=$row['count_child'];
					return $this->count;
				} else{
					 return FALSE;
				}
	}

	function getAllServiceOfPackage($service_code){
				global $db;

				$this->sql="SELECT lg.service_code_child AS service_code,
										(SELECT name FROM seg_lab_services WHERE service_code=lg.service_code_child) AS name,
										(SELECT price_cash FROM seg_lab_services WHERE service_code=lg.service_code_child) AS cash,
										(SELECT price_charge FROM seg_lab_services WHERE service_code=lg.service_code_child) AS charge,
										(SELECT is_socialized FROM seg_lab_services WHERE service_code=lg.service_code_child) AS sservice,
										(SELECT group_code FROM seg_lab_services WHERE service_code=lg.service_code_child) AS group_code,
										(SELECT price FROM seg_service_discounts WHERE service_code=lg.service_code_child AND discountid='C1' AND service_area='LB') AS priceC1,
										(SELECT price FROM seg_service_discounts WHERE service_code=lg.service_code_child AND discountid='C2' AND service_area='LB') AS priceC2,
										(SELECT price FROM seg_service_discounts WHERE service_code=lg.service_code_child AND discountid='C3' AND service_area='LB') AS priceC3
										FROM seg_lab_group AS lg
										WHERE lg.service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}
	#----------------

	function get_blood_bank_details_by_refno($refno) {
			global $db;
			$query = "SELECT sls.refno, sls.encounter_nr, cw.name, cp.name_first, cp.name_last, cp.name_middle, cp.blood_group
								FROM seg_lab_serv sls INNER JOIN care_person cp ON (sls.pid=cp.pid)
								LEFT JOIN care_encounter ce ON (sls.encounter_nr = ce.encounter_nr)
								LEFT JOIN care_ward cw ON (cw.nr = ce.current_ward_nr) WHERE sls.refno='$refno' AND sls.ref_source='BB'";

			$result = $db->Execute($query);
			if ($result) {
				$row = $result->FetchRow();
				$blood_type = trim($row['blood_group']);
				$ward = trim($row['name']);
				$encounter_nr = trim($encounter_nr);

				$array = array('refno' => $refno,
											 'patient_name' => ucwords(strtolower($row['name_first'] . ' ' . $row['name_middle'] . ' ' . $row['name_last'])),
											 'encounter_nr' => $encounter_nr != '' ?  $encounter_nr : false,
											 'blood_type' => $blood_type != '' ? $blood_type : '-Not specified-',
											 'ward' => isset($row['name']) ? $ward : '-Not specified-');
				return $array;
			}
			else {
				return false;
			}

		}

		function file_promissory_note($blood_data) {
			global $db;
			extract($blood_data);
			$query = "INSERT INTO seg_promissory_blood(lab_serv_refno, borrowers_name, date_filed) VALUES('$refno', '$borrowers_name', '$date_filed')";
			if ($db->Execute($query)) {
					return true;
			}
			else {
					return false;
			}
		}

		function update_file_promissory_note($blood_data) {
			global $db;
			extract($blood_data);
			$query = "UPDATE seg_promissory_blood SET borrowers_name='$borrowers_name', date_filed='$date_filed' WHERE lab_serv_refno='$refno'";
			if ($db->Execute($query)) {
					return true;
			}
			else {
					return false;
			}
		}

		function insert_blood_bank_items($blood_data) {
			global $db;
			extract($blood_data);
			$item_count = count($date_borrowed);
			$items = array();
			for ($i = 0; $i < $item_count; $i++) {
				$items[] = array($date_borrowed[$i], $no_of_units[$i], $serial_number[$i], $date_replaced[$i], $no_of_units_replaced[$i], $item_status[$i], $remarks[$i]);
			}

			$index = 'lab_serv_refno, date_borrowed, no_of_units, serial_number, date_replaced, no_of_units_replaced, item_status, remarks';
			$values = "'$refno', ?, ?, ?, ?, ?, ?, ?";
			$query = "INSERT INTO seg_promissory_blood_items ($index) VALUES ($values)";

			$result = $db->Execute($query, $items);
			if ($result) {
					return true;
			}
			else {
					return false;
			}
		}

		function delete_blood_bank_items($refno) {
				global $db;
				$query = "DELETE FROM seg_promissory_blood_items WHERE lab_serv_refno='$refno'";
				if ($db->Execute($query)) {
						return true;
				}
				else {
						return false;
				}
		}

		function save_promissory_note($blood_data) {
				global $db;

				$errors = array();
				//$db->StartTrans();
				$query = "START TRANSACTION";
				$db->Execute($query);
				if (!$this->file_promissory_note($blood_data)) {
						$errors[] = 'Stage 1 Error';
				}
				if (!$this->delete_blood_bank_items($blood_data['refno'])) {
						$errors[] = 'Stage 2 Error';
				}
				if (!$this->insert_blood_bank_items($blood_data)) {
						$errors[] = 'Stage 3 Error';
				}
				if (empty($errors)) {
						$query = "COMMIT";
						$db->Execute($query);
						//$db->CommitTrans();
						return true;
				}
				else {
						foreach ($errors as $value) {
								echo $value . '<br/>';
						}
						$query = "ROLLBACK";
						$db->Execute($query);
						//$db->RollbackTrans();
						return false;

			 }
		}

		function get_promissory_note($refno) {
			global $db;
			$query = "SELECT lab_serv_refno, borrowers_name, date_filed FROM seg_promissory_blood WHERE lab_serv_refno='$refno'";
			$result = $db->Execute($query);
			if ($result) {
				if ($result->RecordCount()) {
					return $result->FetchRow();
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}

		function update_promissory_note($blood_data) {
				 global $db;

				$errors = array();
				//$db->StartTrans();
				$query = "START TRANSACTION";
				$db->Execute($query);
				if (!$this->update_file_promissory_note($blood_data)) {
						$errors[] = 'Stage 1 Error';
				}
				if (!$this->delete_blood_bank_items($blood_data['refno'])) {
						$errors[] = 'Stage 2 Error';
				}
				if (!$this->insert_blood_bank_items($blood_data)) {
						$errors[] = 'Stage 3 Error';
				}
				if (empty($errors)) {
						$query = "COMMIT";
						$db->Execute($query);
						//$db->CommitTrans();
						return true;
				}
				else {
						foreach ($errors as $value) {
								echo $value . '<br/>';
						}
						$query = "ROLLBACK";
						$db->Execute($query);
						//$db->RollbackTrans();
						return false;

			 }
		}

		function get_blood_bank_items($refno) {
			global $db;
			$this->sql = "SELECT * FROM seg_promissory_blood_items WHERE lab_serv_refno='$refno'";
			$this->result = $db->Execute($this->sql);
			if ($this->result) {
			$data = array();
				while($row = $this->result->FetchRow()) {
					$data[] = array($row['date_borrowed'], $row['no_of_units'], $row['serial_number'], $row['date_replaced'],
													$row['no_of_units_replaced'], $row['item_status'], $row['remarks']);
				}
				return $data;
			}
			else {
				return false;
			}
		}

			#-----added by cha 01-12-2010
		function getNewDonorID($donor_id)
		{
			 global $db;

					$temp_cntrl_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Reference number
					$row=array();
					$this->sql="SELECT donor_id FROM seg_donor_info WHERE donor_id LIKE '$temp_cntrl_nr' ORDER BY donor_id DESC";
					#echo "this ".$this->sql;
					if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
							if($this->res['gnpn']->RecordCount()){
									$row=$this->res['gnpn']->FetchRow();
									return $row['donor_id']+1;
							}else{ return $contrlnr=date('Y')."00000001";}
					}else{ return $contrlnr=date('Y')."00000001";}
		}
		#-----end cha

		//added by CHA 08-03-09
		function saveBloodDonorDetails($donor_details)
		{
			global $db;
			$maxlen=12;
			$this->sql="select donor_id from seg_donor_info order by donor_id desc limit 1";
			if($this->result=$db->Execute($this->sql))
			{
					$this->row=$this->result->FetchRow();
					$num_val=(int)$this->row['donor_id'];
					$donor_id = $this->getNewDonorID($this->row['donor_id']);
			}

			//get corresponding brgy_nr and mun_nr
			$this->sql = "SELECT sb.brgy_nr, sm.mun_nr FROM seg_barangays AS sb LEFT JOIN seg_municity AS sm".
										" ON sb.mun_nr=sm.mun_nr WHERE sb.brgy_name=".$db->qstr($donor_details[6]).
										" AND sm.mun_name=".$db->qstr($donor_details[7]);
			if($this->result=$db->Execute($this->sql))
			{
				$row = $this->result->FetchRow();
				$donor_details[6] = $row['brgy_nr'];
				$donor_details[7] = $row['mun_nr'];
			}

			$index="donor_id,last_name, first_name, middle_name, birth_date, age, street_name, brgy_nr, mun_nr, sex, blood_type, civil_status, register_date";
			$values="'".$donor_id."','";
			for($i=0;$i<count($donor_details);$i++)
			{
					$values.="".$donor_details[$i]."','";
			}
			$values.="".date('Y-m-d H:i:s')."'";
			#$this->sql = "UPDATE seg_donor_info set last_name='".$donor_details[0]."', first_name='".$donor_details[1]."',middle_name='".$donor_details[2]."',birth_date='".$donor_details[3]."',age='".$donor_details[4]."',street_name='".$donor_details[5]."',brgy_nr='".$donor_details[6]."',mun_nr='".$donor_details[7]."',sex='".$donor_details[8]."',blood_type='".$donor_details[9]."',civil_status='".$donor_details[10]."',register_date='".date('Y-m-d H:i:s')."'";
			$this->sql = "INSERT into seg_donor_info ($index) VALUES ($values)";
			#echo $this->sql;
			if($this->result=$db->Execute($this->sql))
				{
					return true;
				}
				else{ return false;}
		}

		function countBloodDonor($searchID, $multiple=0, $maxcount=100, $offset=0)
		{
				global $db;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;
				if($searchID!='*' && $searchID!='')
				{
							$this->sql="select donor_id, CONCAT(IF (trim(first_name) IS NULL,'',trim(first_name)),' ',IF(trim(middle_name) IS NULL ,'',trim(middle_name)),' ',
							 IF(trim(last_name) IS NULL,'',trim(last_name))) as Name,
							 CONCAT(IF (trim(street_name) IS NULL,'',trim(street_name)),', ',IF(trim(brgy_nr) IS NULL ,'',trim(brgy_nr)),', ',
							 IF(trim(mun_nr) IS NULL,'',trim(mun_nr))) as Address,
							 age, register_date, blood_type from seg_donor_info where ((donor_id='".$searchID."' or last_name='".$searchID."') or(donor_id like '".$searchID."%' or last_name like'".$searchID."%')) AND status NOT IN ('deleted')";
				}
				else
				{
							$this->sql="select donor_id, CONCAT(IF (trim(first_name) IS NULL,'',trim(first_name)),' ',IF(trim(middle_name) IS NULL ,'',trim(middle_name)),' ',
							 IF(trim(last_name) IS NULL,'',trim(last_name))) as Name,
							 CONCAT(IF (trim(street_name) IS NULL,'',trim(street_name)),', ',IF(trim(brgy_nr) IS NULL ,'',trim(brgy_nr)),', ',
							 IF(trim(mun_nr) IS NULL,'',trim(mun_nr))) as Address,
							 age, register_date, blood_type from seg_donor_info WHERE status NOT IN ('deleted')";
				}
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getDonorData($searchID, $multiple=0, $maxcount=100, $offset=0)
		{
			 global $db;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;
				if($searchID!='*' && $searchID!='')
				{
							$this->sql="select d.donor_id, CONCAT(IF (trim(d.first_name) IS NULL,'',trim(d.first_name)),' ',IF(trim(d.middle_name) IS NULL ,'',trim(d.middle_name)),' ',
							 IF(trim(d.last_name) IS NULL,'',trim(d.last_name))) as Name,
							 CONCAT(IF (trim(d.street_name) IS NULL,'',trim(d.street_name)),' ',
										IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
										IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
										IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
										IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
										IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) as Address,
															 age, register_date, blood_type from seg_donor_info as d
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=d.brgy_nr
																	LEFT JOIN seg_municity AS sm ON sm.mun_nr=d.mun_nr
																	LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
																	LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
								where (d.donor_id like '".$searchID."%' or d.last_name like '".$searchID."%') and d.status IS NULL order by d.donor_id";
				}
				else
				{
							$this->sql="select d.donor_id, CONCAT(IF (trim(d.first_name) IS NULL,'',trim(d.first_name)),' ',IF(trim(d.middle_name) IS NULL ,'',trim(d.middle_name)),' ',
							 IF(trim(d.last_name) IS NULL,'',trim(d.last_name))) as Name,
							 CONCAT(IF (trim(d.street_name) IS NULL,'',trim(d.street_name)),' ',
										IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
										IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
										IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
										IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
										IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) as Address,
										age, register_date, blood_type from seg_donor_info as d
										LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=d.brgy_nr
																			LEFT JOIN seg_municity AS sm ON sm.mun_nr=d.mun_nr
																			LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
																			LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
										WHERE d.status IS NULL
										order by d.donor_id";
				}
				#echo $this->sql;
				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset))
						{
								if($this->rec_count=$this->res['ssl']->RecordCount())
								{
										return $this->res['ssl'];
								}
								else{ return false; }
						}
						else{ return false; }
		}

		function deleteBloodDonor($deleteID)
		{
				global $db;
				$this->sql="UPDATE seg_donor_info SET status='deleted' WHERE donor_id='".$deleteID."'";
				if($this->result=$db->Execute($this->sql))
				{
						return true;
				}
				else
				{
						return false;
				}
		}

		function getEditDonorDetails($donorID)
		{
				global $db;
				$this->sql = "select * from seg_donor_info where donor_id='".$donorID."' and status not in ('deleted')";
				if($this->result=$db->Execute($this->sql))
				{
								return $this->result;
				}
		}

		function updateBloodDonorDetails($donor_details)
		{
				global $db;
			//get corresponding brgy_nr and mun_nr
			$this->sql = "SELECT sb.brgy_nr, sm.mun_nr FROM seg_barangays AS sb LEFT JOIN seg_municity AS sm".
										" ON sb.mun_nr=sm.mun_nr WHERE sb.brgy_name=".$db->qstr($donor_details[6]).
										" AND sm.mun_name=".$db->qstr($donor_details[7]);
			if($this->result=$db->Execute($this->sql))
			{
				$row = $this->result->FetchRow();
				$donor_details[6] = $row['brgy_nr'];
				$donor_details[7] = $row['mun_nr'];
			}

			$this->sql = "UPDATE seg_donor_info set last_name='".$donor_details[0]."', first_name='".$donor_details[1].
			"',middle_name='".$donor_details[2]."',birth_date='".$donor_details[3]."',age='".$donor_details[4].
			"',street_name='".$donor_details[5]."',brgy_nr='".$donor_details[6]."',mun_nr='".$donor_details[7].
			"',sex='".$donor_details[8]."',blood_type='".$donor_details[9]."',civil_status='".$donor_details[10].
			"' where donor_id='".$donor_details[11]."'";
				if ($this->result=$db->Execute($this->sql))
				{
						if($db->Affected_Rows())
						{
								return TRUE;
						}else { return FALSE; }
				}else { return FALSE; }
		}

		function saveBloodDetails($donorID, $blood_qty, $blood_unit, $donate_date, $donate_time)
		{
				global $db,$HTTP_SESSION_VARS; ;
				$index="donor_id, donor_date, donor_time, qty, unit, create_id, create_dt, modify_id, modify_dt";
				$create_id = $HTTP_SESSION_VARS['sess_user_name'];
				$modify_id = $HTTP_SESSION_VARS['sess_user_name'];
				$create_dt = date("Y-m-d H:i:s");
				$modify_dt = date("Y-m-d H:i:s");
				$values = "".$db->qstr($donorID).",".$db->qstr(date("Y-m-d",strtotime($donate_date))).",".$db->qstr($donate_time).
								",".$db->qstr($blood_qty).",".$db->qstr($blood_unit).",".$db->qstr($create_id).
								",".$db->qstr($create_dt).",".$db->qstr($modify_id).",".$db->qstr($modify_dt);
				$this->sql = "INSERT into seg_donor_transaction ($index) VALUES ($values)";
				if ($this->result=$db->Execute($this->sql))
				{
						if($db->Affected_Rows())
						{
								return TRUE;
						}else { return FALSE; }
				}else { return FALSE; }
		}

		function countBloodDonation($donorID, $multiple=0, $maxcount=100, $offset=0)
		{
				global $db;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;
				$this->sql="select * from seg_donor_transaction where donor_id=".$db->qstr($donorID)." and status not in ('deleted')";
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getDonationData($donorID, $multiple=0, $maxcount=100, $offset=0)
		{
				global $db;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;
				$this->sql="select donor_id,item_id,donor_date,donor_time,qty,unit from seg_donor_transaction where donor_id=".$db->qstr($donorID)." and status not in ('deleted') order by donor_date,donor_time";
				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset))
						{
								if($this->rec_count=$this->res['ssl']->RecordCount())
								{
										return $this->res['ssl'];
								}
								else{ return false; }
						}
						else{ return false; }
		}

		function deleteBloodItem($donorID, $itemID)
		{
				global $db;
				$this->sql="update seg_donor_transaction set status='deleted' where donor_id='".$donorID."' and item_id='".$itemID."'";
				if($this->result=$db->Execute($this->sql))
				{
						return true;
				}
				else{ return false;}
		}

		function updateBloodItemDetails($donorID, $itemID, $new_qty, $new_unit)
		{
				global $db,$HTTP_SESSION_VARS;
				$modify_id = $HTTP_SESSION_VARS['sess_user_name'];
				$modify_dt = date("Y-m-d H:i:s");
				$this->sql = "UPDATE seg_donor_transaction SET qty='".$new_qty."', unit='".$new_unit."', modify_dt='".$modify_dt."', modify_id='".$modify_id."' where donor_id='".$donorID."' and item_id='".$itemID."' and status not in ('deleted')";
				if($this->result=$db->Execute($this->sql))
				{
						return true;
				}
				else{ return false;}
		}

		function saveDonorTransaction($refno, $donorID, $donorRel)
		{
			 global $db;
			 $this->sql="insert into seg_blood_donor_request (refno, donor_id, donor_relationship) values ('".$refno."','".$donorID."','".$donorRel."')";
			 if($this->result=$db->Execute($this->sql))
				{
						return true;
				}
				else{ return false;}
		}
		//end cha

		#added by VAN 05-14-2010 12:47AM... bday :(
		function getBloodRequestInfo($refno, $pid, $service_code){
			global $db;

			$this->sql = "SELECT l.refno,l.pid,ld.service_code,s.group_code,s.name AS service_name,
									p.date_birth AS 'Date Birth',
									CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ',IF(trim(p.name_first) IS NULL ,'',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,'',trim(p.name_middle))) AS patient_name,
									IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
									upper(p.sex) AS sex
									FROM seg_lab_serv AS l
									INNER JOIN seg_lab_servdetails AS ld ON ld.refno=l.refno
									INNER JOIN seg_lab_services AS s ON s.service_code=ld.service_code
									INNER JOIN care_person AS p ON p.pid=l.pid
									WHERE l.ref_source='BB'
									AND l.status NOT IN ('deleted','hidden','inactive','void')
									AND ld.status NOT IN ('deleted','hidden','inactive','void')
									AND ld.service_code='".$service_code."'
									AND l.pid='".$pid."'
									AND l.refno='".$refno."'";
			#echo "here".$this->sql;
			if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()){
						return $this->result->FetchRow();
					}else{
						return FALSE;
					}
			 }else{
					return FALSE;
			 }
		}

		function getBloodProductsStock($service_code){
		global $db;

		$this->sql="SELECT p.bestellnum, p.artikelname, f.service_code, f.item_code,
								SUBSTRING(MIN(CONCAT(i.expiry_date,' ',i.qty)),1,10) AS expiry_date,
								SUBSTRING(MIN(CONCAT(i.expiry_date,qty)),11) AS qty
								FROM care_pharma_products_main AS p
								INNER JOIN seg_blood_products_item AS f ON f.item_code=p.bestellnum
								LEFT JOIN seg_inventory AS i ON i.item_code=f.item_code  AND i.qty>0
								WHERE /*p.prod_class='RS'
								AND*/ f.service_code='".$service_code."'
								GROUP BY p.bestellnum";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
		}
	}

	#added by VAS 06/23/2017
	#HL7 updates, integration with LIS
	function isforReplaceHL7Msg($refno, $key, $testcode=''){
        global $db;

        if ($testcode){
        	$cond = " AND service_code IN (".$testcode.")";
        }

        $this->sql="SELECT * FROM seg_hl7_bloodbank_tracker WHERE refno=".$db->qstr($refno)." 
                    AND hl7_msg LIKE '%|".$key."|%'
                    ".$cond."
                    ORDER BY modify_date LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
            return $this->result->FetchRow();
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
            }
    }

    function getLastMsgControlID($dept){
        global $db;

        $this->sql ="SELECT msg_control_id FROM seg_hl7_msg_control_id WHERE dept=".$db->qstr($dept)." LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
                 $this->count=$this->result->RecordCount();
                 $row = $this->result->FetchRow();
                 return $row['msg_control_id']+1;
            } else{
                 return FALSE;
            }
    }

    function updateHL7_msg_control_id($new_msg_control_id, $dept){
        global $db;
        
        $this->sql = "UPDATE seg_hl7_msg_control_id SET msg_control_id=".$db->qstr($new_msg_control_id)." WHERE dept=".$db->qstr($dept);
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }

    function getInfo_HL7_tracker($msg_control_id){
        global $db;

        $this->sql ="SELECT * FROM seg_hl7_bloodbank_tracker WHERE msg_control_id=".$db->qstr($msg_control_id);

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }

    function getInfo_HL7_request($refno, $service_code, $service_name, $priority){
        global $db;

        $this->sql ="SELECT * FROM seg_hl7_bloodbank_tracker WHERE refno=".$db->qstr($refno)."
						AND hl7_msg LIKE '%|".$service_code."^".addslashes($service_name)."|".$priority."|%'";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }

    function addInfo_HL7_tracker($details){
        global $db;
        
        $index = " msg_control_id,lis_order_no,msg_type,event_id,refno,pid,encounter_nr,service_code,nth_units,hl7_msg,create_date,modify_date";

        $values = $db->qstr($details->msg_control_id).",".
                  $db->qstr($details->lis_order_no).",".
                  $db->qstr($details->msg_type).",".
                  $db->qstr($details->event_id).",".
                  $db->qstr($details->refno).",".
                  $db->qstr($details->pid).",".
                  $db->qstr($details->encounter_nr).",".
                  $db->qstr($details->service_code).",".
                  $db->qstr($details->nth_units).",".
                  $db->qstr($details->hl7_msg).",NOW(),NOW()";

        $this->sql = "INSERT INTO seg_hl7_bloodbank_tracker ($index)
                            VALUES ($values)";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function updateInfo_HL7_tracker($details){
        global $db;
        
        $this->sql = "UPDATE seg_hl7_bloodbank_tracker SET 
                            lis_order_no = ".$db->qstr($details->lis_order_no).",
                            msg_type = ".$db->qstr($details->msg_type).",
                            event_id = ".$db->qstr($details->event_id).",
                            refno = ".$db->qstr($details->refno).",
                            pid = ".$db->qstr($details->pid).",
                            encounter_nr = ".$db->qstr($details->encounter_nr).",
                            service_code = ".$db->qstr($details->service_code).",
                            nth_units = ".$db->qstr($details->nth_units).",
                            hl7_msg = ".$db->qstr($details->hl7_msg).",
                            modify_date = NOW() 
                      WHERE msg_control_id=".$db->qstr($details->msg_control_id);
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }

    function isExistHL7Msg($refno){
        global $db;

        $this->sql="SELECT * FROM seg_hl7_bloodbank_tracker WHERE refno=".$db->qstr($refno)." ORDER BY modify_date DESC LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
            return $this->result->FetchRow();
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
            }
    }

    function getHL7Msg($refno, $testcode=''){
        global $db;

        if ($testcode){
        	$cond = " AND service_code IN (".$testcode.")";
        }

        $this->sql="SELECT * FROM seg_hl7_bloodbank_tracker 
        			WHERE refno=".$db->qstr($refno).
        			" AND hl7_msg NOT LIKE '%|CA|%'". $cond;
        		
        if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
		}
    }

    #----------end here added by VAS 06/23/2017

    #added by VAS 12/06/2017
    function insert_Orderno_HCLAB($order_no, $refno){
		$this->sql = "INSERT INTO seg_blood_hclab_orderno(lis_order_no,refno) 
						VALUES('".$order_no."','".$refno."')";
		return $this->Transact();
	}

	function getLastOrderNo(){
		global $db;

		$this->sql ="SELECT last_orderno FROM seg_blood_hclab_tracker LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();
				return  $row['last_orderno']+1;
			}else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function getOrderLastNr($order_init) {
		global $db;
		$this->sql="SELECT IFNULL(MAX(CAST(lis_order_no AS UNSIGNED)+1),$order_init) 
					FROM seg_blood_hclab_orderno";
		return $db->GetOne($this->sql);
	}

	function isExistOrderNo($refno){
		global $db;

		$this->sql="SELECT * FROM seg_lab_hclab_orderno
						WHERE refno=".$db->qstr($refno);

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
		 	}
		}else{
			return FALSE;
		}
	}

	function getLabOrderNoLIMIT($refno){
        global $db;

        $this->sql="SELECT lis_order_no FROM seg_blood_hclab_orderno WHERE refno=".$db->qstr($refno)."
                    ORDER BY lis_order_no DESC LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }


    #added by VAS 12/06/2017
    #will just add here to avoid conflict
    function getRequestInfo($refno){
        global $db;

        $this->sql="SELECT 
					  s.*
					  FROM
					  seg_lab_serv s 
					  WHERE s.refno = ".$db->qstr($refno);

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }

    function update_HCLabRefno_Tracker($orderno){
		global $db;
        
		$this->sql = "UPDATE seg_blood_hclab_tracker SET last_orderno=".$db->qstr($orderno);
		#return $this->Transact();
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
	}
	#------------- end here 12/06/2017	

	function getPledgeDetails($refno, $enc, $pid){
		global $db;

		$enc_nr = ($enc == '' ? ' AND encounter_nr = ""' : " AND encounter_nr = ".$db->qstr($enc));

		$this->sql = "SELECT * FROM seg_pledge_commitment_details WHERE pid = ".$db->qstr($pid)." AND batch_nr=".$db->qstr($refno).$enc_nr;

		if($this->result=$db->Execute($this->sql)){
			if($this->count=$this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return false;
		}else
			return false;

	}


	function getPatientBloodResult($refno, $test='',$ordering=''){
		global $db;

		if ($test){
			$cond_test = " AND r.test=".$db->qstr($test)." LIMIT 1";
		}


		if ($ordering){
			$cond_ordering = " AND d.ordering = ".$db->qstr($ordering);
		}

		$this->sql="SELECT 
					  d.is_urgents urgents,
					  o.lis_order_no,
					  o.refno,
					  o.pid,
					  o.encounter_nr,
					  o.service_code,
					  r.filename,
					  r.msg_type_id,
					  r.event_id,
					  r.test,
					  o.hl7_msg order_hl7_msg,
					  r.hl7_msg,
					  IF(
					    fn_calculate_age (sls.`serv_dt`, p.date_birth),
					    fn_get_age (sls.`serv_dt`, p.date_birth),
					    age
					  ) AS age,
					  UPPER(
					    fn_get_person_name (IFNULL(p.pid,CONCAT('W',w.pid)))
					  ) AS pat_name,
					  p.`date_birth` AS birth,
					  p.sex 
					FROM
					  seg_hl7_bloodbank_tracker o 
					  LEFT JOIN seg_hl7_hclab_msg_receipt r 
					    ON r.lis_order_no = o.lis_order_no 
					    AND r.pid = o.pid 
					  LEFT JOIN seg_blood_received_details d 
					    ON d.refno = o.refno 
					    AND d.service_code = o.service_code 
					  LEFT JOIN seg_lab_serv sls 
    					ON o.`refno` = sls.`refno`
					  INNER JOIN care_encounter ce 
					  	ON ce.pid=o.pid 
					  	AND ce.encounter_nr=o.encounter_nr  
  					  LEFT JOIN care_person p 
  					  	ON p.pid=o.pid  
  					  LEFT JOIN seg_walkin w 
                                ON w.pid=o.pid	  

					WHERE o.refno = ".$db->qstr($refno)."
					  AND r.msg_type_id='ORU' AND r.event_id='R01' ".
					  $cond_ordering .
					  $cond_test;

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result;
			 }else{
					return FALSE;
			 }
		}else{
			return FALSE;
		}
	}


	function getPatientPreparedBloodProd($lis_order_no, $pid, $serial_nr='',$result=''){
		global $db;
		$resultCond = '';

		$serial_nr = str_replace(" ", "", $serial_nr);

		if ($serial_nr){
			$cond = " AND hl7_msg REGEXP '(^".$lis_order_no."|".$serial_nr.")' ";
		}else{
			$cond = " AND hl7_msg REGEXP ".$db->qstr($lis_order_no);
		}

		if($result)
			$resultCond = " AND hl7_msg REGEXP ".$db->qstr($result);

		$this->sql="SELECT 
						  * 
						FROM
						  seg_hl7_hclab_msg_receipt 
						WHERE pid = ".$db->qstr($pid)." 
                                                  ".$cond.$resultCond."
						  AND msg_type_id = 'BPS' AND event_id='O29' ORDER BY date_update DESC LIMIT 1";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result;
			 }else{
					return FALSE;
			 }
		}else{
			return FALSE;
		}
	}

	function getDonorInfo($serial_no, $test, $started, $ended){
		global $db;

		if($started && $ended){
			$started = date("Y-m-d h:i:s", strtotime($started));
			$ended = date("Y-m-d h:i:s", strtotime($ended));

			$cond = " AND (date_update BETWEEN ".$db->qstr($started)." AND ".$db->qstr($ended).")";
		}

		$this->sql = "SELECT 
						  * 
						FROM
						  seg_hl7_hclab_msg_receipt 
						WHERE pid = ".$db->qstr($serial_no)."
						  AND msg_type_id = 'ORU' 
						  AND event_id = 'R01' 
						  AND test = ".$db->qstr($test)."
						  AND hl7_msg REGEXP 'DONOR'".$cond."
						  LIMIT 1";
		
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result;
			 }else{
				return FALSE;
			 }
		}else{
			return FALSE;
		}
	}


	function getBloodCrossmatchResultDesc($code){
		global $db;

		$this->sql="SELECT NAME FROM seg_blood_result_lis WHERE id=".$db->qstr($code);

		return $db->GetOne($this->sql);
	}

	function getBloodCrossmatchResultDescManual($code){
		global $db;

		$this->sql="SELECT NAME FROM seg_blood_result WHERE id = ".$db->qstr($code);

		return $db->GetOne($this->sql);
	}

	function getBloodCrossmatchResultDescLIS($code){
		global $db;

		$this->sql="SELECT 
					  IFNULL(l.name, r.name) NAME
					FROM
					  seg_blood_result r
					LEFT JOIN seg_blood_result_lis l ON l.id=r.lis_code  
					WHERE r.id = ".$db->qstr($code);

		return $db->GetOne($this->sql);
	}


	function getBloodDept($location){
		global $db;

		$this->sql="SELECT * FROM seg_blood_dept WHERE INSTR(".$db->qstr($location).",NAME)<>0";

		return $db->GetRow($this->sql);
	}


	function updateBloodReceivedDetails($details){
		global $db;

		$date_received = ($details->date_received)?date('Y-m-d H:i:s',strtotime($details->date_received)):'0000-00-00 00:00:00';    

        $this->sql = "UPDATE seg_blood_received_details SET 
							 received_date = ".$db->qstr($date_received).",
	                         dept = ".$db->qstr($details->location).",
	                         component = ".$db->qstr($details->blood_component).",
	                         serial_no = ".$db->qstr($details->serial_no).",
	                         blood_source = ".$db->qstr($details->blood_source).",
	                         result = ".$db->qstr($details->result).",
	                         history = ".$this->ConcatHistory('Update [BB LIS integration] '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name'].'\n').",
	                         modify_id = ".$db->qstr($_SESSION['sess_login_userid']).",
	                         modify_dt = ".$db->qstr(date('Y-m-d H:i:s'))." 
                       WHERE refno = ".$db->qstr($details->refno)."
	                   AND service_code = ".$db->qstr($details->service_code)."
	                   AND ordering = ".$db->qstr($details->ordering);
		
		if ($db->Execute($this->sql)) {
			return true;
		}else {
			return false;
		}                       
    }


	/*function updateBloodReceivedStatus($details){
		global $db;
		
		$date_done = ($details->date_done)?date('Y-m-d H:i:s',strtotime($details->date_done)):'0000-00-00 00:00:00';    
        $date_crossmatched = ($details->date_crossmatched)?date('Y-m-d H:i:s',strtotime($details->date_crossmatched)):'0000-00-00 00:00:00';    

     	$this->sql = "UPDATE seg_blood_received_status SET 
							 done_date = ".$db->qstr($date_done).",
	                         started_date = ".$db->qstr($date_crossmatched).",
	                         history = ".$this->ConcatHistory('Update [BB LIS integration] '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name'].'\n').",
	                         modify_id = ".$db->qstr($_SESSION['sess_login_userid']).",
	                         modify_dt = ".$db->qstr(date('Y-m-d H:i:s'))." 
                       WHERE refno = ".$db->qstr($details->refno)."
	                   AND service_code = ".$db->qstr($details->service_code)."
	                   AND ordering = ".$db->qstr($details->ordering);
		
		if ($db->Execute($this->sql)) {
			return true;
		}else {
			return false;
		}  
	}*/
	

	#updated by VAS 10/14/2019
	function updateBloodReceivedStatus($details){
		global $db;
		
		$fldarray = array(
						'done_date' => $db->qstr($details->date_done),
	                    'started_date' => $db->qstr($details->date_crossmatched),
	                    'history' => $this->ConcatHistory('Update [BB LIS integration] '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name'].'\n'),
	                    'modify_id' => $db->qstr($_SESSION['sess_login_userid']),
	                    'modify_dt' => $db->qstr(date('Y-m-d H:i:s')),
	                    'refno' => $db->qstr($details->refno),
	                    'service_code' => $db->qstr($details->service_code),
	                    'ordering' => $db->qstr($details->ordering),
	               );

		$bsuccess = $db->Replace(
							'seg_blood_received_status', 
							$fldarray, 
							array('refno', 'service_code','ordering'));

		if($bsuccess)
       		return true;
   		else
   			return FALSE;
	}

	function getBloodResultCode($code){
		global $db;

		$this->sql="SELECT 
					  b.id
					FROM
					  seg_blood_result_lis l
					  INNER JOIN seg_blood_result b ON b.id=l.code_map
					WHERE l.id = ".$db->qstr($code);

		return $db->GetOne($this->sql);
	}

	function saveBloodMonitoringInfoFromArray(&$data){

		$this->_useBloodMonitoring();
		$this->data_array=$data;
		return $this->insertDataFromInternalArray();

	}# end function saveBloodMonitoringInfoFromArray

	function updateBloodMonitoringInfoFromArray(&$data){

		$this->_useBloodMonitoring();
		$this->data_array=$data;
		if(isset($this->data_array['pid'])) 
			unset($this->data_array['pid']);
	
		$this->where="pid='".$data['pid']."' AND date_released='".$data['date_released']."' AND create_id='".$data['create_id']."'";
		return $this->updateDataFromInternalArray($data['pid'],FALSE);
	
	
	}# end function updateBloodMonitoringInfoFromArray

	function updateBloodType(&$data){

		$this->_useBloodMonitoring();
		$this->data_array=$data;
		if(isset($this->data_array['pid'])) 
			unset($this->data_array['pid']);
	
		$this->where="pid='".$data['pid']."'";
		return $this->updateDataFromInternalArray($data['pid'],FALSE);
	
	
	}# end function updateBloodType

	function savePerDate($refno, $pid, $serv_result, $blood_type, $component, $qty, $serial_no, $status, $mode, $datetime){
		global $db,$HTTP_SESSION_VARS;
		$encoder = $_SESSION['sess_temp_userid'];
        $date_created = date("Y-m-d H:i:s");
        $date_encode = date("Y-m-d H:i:s", strtotime($date_encoded));
        //$date_receive = date("Y-m-d H:i:s", strtotime($date_received));
        $final_stat = '';

		if ($mode=='started')
        	$date_fld = "date_started";
        elseif ($mode=='done')
            $date_fld = "date_done";
        elseif ($mode=='issuance')
            $date_fld = "date_issuance";
        elseif ($mode=='returned')
            $date_fld = "date_return";
        elseif ($mode=='reissue')
            $date_fld = "date_reissue";
        elseif ($mode=='consumed')
            $date_fld = "date_consumed";
        elseif ($mode=='release')
            $date_fld = "date_released";

         if (trim($datetime) == ''){
            $date_value = 'NULL';
        }else{
            $date_value = date("Y-m-d H:i:s",strtotime($datetime));
        }

        if($status == $qty) {
        	$final_stat = 'complete';
        }else{
        	$final_stat = 'lack';
        }
		$this->sql = "INSERT INTO seg_blood_monitoring (pid, refno, service_code, blood_type, component, ordered_qty, serial_no, status, ".$date_fld.", create_id, create_dt) 
						VALUES (".$db->qstr($pid).",
								".$db->qstr($refno).", 
								".$db->qstr($serv_result).", 
								".$db->qstr($blood_type).", 
								".$db->qstr($component).", 
								".$db->qstr($qty).", 
								".$db->qstr($serial_no).",
								'".$final_stat."', 
								'".$date_value."', 
								'".$encoder."', 
								'".$date_created."')";

        $sql_check = mysql_query("SELECT 
                                          * 
                                        FROM
                                          seg_blood_monitoring 
                                        WHERE ".$date_fld." = '".$date_value."'
                                          AND pid = ".$db->qstr($pid)."
                                          AND serial_no = ".$db->qstr($serial_no)."
                                          AND service_code = ".$db->qstr($serv_result)."");
		if(mysql_num_rows($sql_check) == 0) {
            $db->Execute($this->sql);
		}

	}# end 
	function fetchCrossMatched($batch_nr,$component,$service_code){
		global $db;
	$data = array();
	// $sql ="SELECT 
	// 	  sdrd.`received_date`,
	// 	  sdrd.`component`,
	// 	  sbr.`name` as result,
	// 	  sdrd.`dept`,
	// 	  sdrd.`serial_no`,
	// 	  sdrd.`service_code`,
	// 	  IF(sls.`discountid` = 'SC', 1, 0) AS is_senior,
	// 	  sdrd.`ordering`
	// 	FROM
	// 	  `seg_blood_received_details` AS sdrd 
	// 	   INNER  JOIN `seg_blood_component` AS sbc
 // 			 ON sdrd.`component` = sbc.`id`
	// 		INNER JOIN seg_lab_serv as sls 
 //   			 ON sls.`refno` = sdrd.`refno`
 //   			LEFT JOIN seg_blood_result as sbr
 //   			ON sbr.`id`= sdrd.`result`
	// 	WHERE `component` = ".$db->qstr($component)." 
	// 	  AND sdrd.`refno` =  ".$db->qstr($batch_nr);

	$sql = "SELECT t.`refno`,t.`received_date`,
    t.`component`,
    t.`result`,
    t.`dept`,
    t.`serial_no`,
    t.`service_code`,
    IF(t.`discountid` = 'SC', 1, 0) AS is_senior,
    t.`ordering`,
  (SELECT 
    sbrs.`issuance_date`
  FROM
    `seg_blood_received_status` AS sbrs 
  WHERE sbrs.`refno` =  ".$db->qstr($batch_nr)." 
    AND sbrs.`service_code` = t.service_code 
    AND sbrs.`ordering` = t.ordering) AS issuance_date ,
  (SELECT 
    sbrs.`date_return`
  FROM
    `seg_blood_received_status` AS sbrs 
  WHERE sbrs.`refno` =  ".$db->qstr($batch_nr)." 
    AND sbrs.`service_code` = t.service_code 
    AND sbrs.`ordering` = t.ordering) AS date_return ,
  (SELECT 
    sbrs.`date_consumed`
  FROM
    `seg_blood_received_status` AS sbrs 
  WHERE sbrs.`refno` =  ".$db->qstr($batch_nr)." 
    AND sbrs.`service_code` = t.service_code 
    AND sbrs.`ordering` = t.ordering) AS date_consumed,
  (SELECT 
    sbrs.`started_date`
  FROM
    `seg_blood_received_status` AS sbrs 
  WHERE sbrs.`refno` =  ".$db->qstr($batch_nr)." 
    AND sbrs.`service_code` = t.service_code 
    AND sbrs.`ordering` = t.ordering) AS started_date
FROM
  (SELECT
  	sdrd.`refno`,
    sdrd.`received_date`,
    sdrd.`component`,
    sbr.`name` AS result,
    sdrd.`dept`,
    sdrd.`serial_no`,
    sdrd.`service_code`,
    sdrd.`ordering`,
    sls.`discountid`
  FROM
    `seg_blood_received_details` AS sdrd 
    INNER JOIN `seg_blood_component` AS sbc 
      ON sdrd.`component` = sbc.`id` 
    INNER JOIN seg_lab_serv AS sls 
      ON sls.`refno` = sdrd.`refno`
    LEFT JOIN seg_blood_result AS sbr 
      ON sbr.`id` = sdrd.`result` 
  WHERE `component` =  ".$db->qstr($component)." 
    AND sdrd.`refno` = ".$db->qstr($batch_nr)." AND sdrd.`service_code` = ".$db->qstr($service_code)." ORDER BY sdrd.ordering) AS t ";
//     print_r("<pre>");
// var_dump($sql);
	$result = $db->Execute($sql);
	if($result->RecordCount()){
		while($row = $result->FetchRow()){
			array_push($data,array(
				'refno'  => $row['refno'],
				'received_date' =>	$row['received_date'],
				'component' => $row['component'],
				'result' => $row['result'],
				'dept' => $row['dept'],
				'serial_no' => $row['serial_no'],
				'service_code' => $row['service_code'],
				'is_senior' => $row['is_senior'],
				'ordering' => $row['ordering'],
				'issuance_date' => $row['issuance_date'],
				'service_code' => $row['service_code'],
				'date_return' => $row['date_return'],
				'date_consumed' => $row['date_consumed'],
				'started_date' => $row['started_date'],

			));
		}
	}
	return $data;
	}

	function fetchReceivedStatus($refno,$ordering,$service_code){
		global $db;
		$data = array();
			$sql ="SELECT 
					sbrs.`refno`,
					sbrs.`issuance_date`,
					sbrs.`ordering`,
					sbrs.`service_code`,
					sbrs.`date_return`,
					sbrs.`date_consumed`,
					sbrs.`started_date`
				FROM
					`seg_blood_received_status` AS sbrs 
				WHERE sbrs.`refno` =".$db->qstr($refno)."
					AND sbrs.`service_code` = ".$db->qstr($service_code)."
					AND sbrs.`ordering` = ".$db->qstr($ordering);

			$result = $db->Execute($sql);
				if($result->RecordCount()){
					while($row = $result->FetchRow()){
						array_push($data,array(
							'refno' =>	$row['refno'],
							'issuance_date' => $row['issuance_date'],
							'ordering' => $row['ordering'],
							'service_code' => $row['service_code'],
							'date_return' => $row['date_return'],
							'date_consumed' => $row['date_consumed'],
							'started_date' => $row['started_date'],
								));
					}
				}
			return $data;
			}
}
?>