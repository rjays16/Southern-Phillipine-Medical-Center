<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');

	class SegAgencyManager extends Core {

		var $tb_company = "seg_industrial_company";
		var $tb_comp_emp = "seg_industrial_comp_emp";
		var $tb_comp_price = "seg_industrial_comp_price";
		var $fld_company =
			array (
				'company_id',
				'name',
				'short_id',
				'address',
				'contact_no',
				'president',
				'hr_manager',
				'hosp_acct_no',
				'is_deleted',
				'status',
				'history',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt'
			);
		var $fld_comp_emp =
			array(
				'company_id',
				'pid',
				'employee_id',
				'position',
				'job_status',
				'status',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt'
			);

		function SegAgencyManager()
		{
			$this->useCompany();
		}

		function useCompany()
		{
			$this->coretable = $this->tb_company;
			$this->ref_array = $this->fld_company;
		}

		function useCompEmployee()
		{
			$this->coretable = $this->tb_comp_emp;
			$this->ref_array = $this->fld_comp_emp;
		}

		function getNewId()
		{
			global $db;
			$id = date('Y').'000001';
			$temp_id = date('Y')."%";
			$row=array();
			$this->sql="SELECT company_id FROM $this->tb_company WHERE company_id LIKE '$temp_id' ORDER BY company_id DESC";
			if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
					if($this->res['gnpn']->RecordCount()){
							$row=$this->res['gnpn']->FetchRow();
							return $row['company_id']+1;
					}else{ return $id;}
			}else{ return $id;}
		}

		function saveCompany($data)
		{
			global $db;
			$this->setDataArray($data);
			return $this->insertDataFromInternalArray();
		}

		function updateCompany($data,$nr)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_company SET name=".$db->qstr($data['name']).", \n".
						"address=".$db->qstr($data['address']).", contact_no=".$db->qstr($data['contact_no']).", \n".
						"short_id=".$db->qstr($data['short_id']).", president=".$db->qstr($data['president']).", \n".
						"hr_manager=".$db->qstr($data['hr_manager']).", hosp_acct_no=".$db->qstr($data['hosp_acct_no']).", \n".
						"history=CONCAT(history,'\nUpdated: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_temp_userid'])."]'),\n".
						"modify_id=".$db->qstr($_SESSION['sess_temp_userid']).", modify_dt=".$db->qstr(date('Y-m-d H:i:s'))." \n".
						"WHERE company_id=".$db->qstr($nr);
			$saveok = $db->Execute($this->sql);
			if($saveok!==FALSE) {
				return TRUE;
			}else {
				return FALSE;
			}
		}

		function deleteCompany($nr)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_company SET status='deleted' WHERE company_id=".$db->qstr($nr);
			$saveok=$db->Execute($this->sql);
			if($saveok!==FALSE) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		function getCompanyDetails($nr)
		{
			global $db;
			$this->sql = "SELECT SQL_CALC_FOUND_ROWS ia.* FROM seg_industrial_company AS ia WHERE status <> 'deleted' \n".
									" AND company_id=".$db->qstr($nr);
			$this->result=$db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		}

		function assignCompanyEmployee($data)
		{
			global $db;
			$this->setDataArray($data);
			return $this->insertDataFromInternalArray();
		}

		function deleteEmployeeAssignment($pid, $nr)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_comp_emp SET status='deleted', \n".
			" modify_id=".$db->qstr($_SESSION['sess_temp_userid']).", \n".
			" modify_dt=".$db->qstr(date('Y-m-d H:i:s'))." \n".
			" WHERE pid=".$db->qstr($pid)." AND company_id=".$db->qstr($nr);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getEmployeeDetails($pid, $nr)
		{
			global $db;
			$test =array($nr,$pid);
			/*$this->sql = "SELECT SQL_CALC_FOUND_ROWS ia.* FROM seg_industrial_comp_emp AS ia WHERE ia.status <> 'deleted' \n".
									" AND ia.company_id=".$db->qstr($nr)." AND ia.pid=".$db->qstr($pid);*/
			$this->sql = $db->Prepare("SELECT SQL_CALC_FOUND_ROWS ia.* FROM seg_industrial_comp_emp AS ia WHERE ia.company_id=? AND ia.pid=?");						
			$this->result=$db->Execute($this->sql,$test);
			if($this->result!==FALSE) {
				return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		}

		function updateEmployeeData($data)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_comp_emp SET employee_id=".$db->qstr($data['employee_id']).", \n".
						"position=".$db->qstr($data['position']).", job_status=".$db->qstr($data['job_status']).", \n".
						"modify_id=".$db->qstr($_SESSION['sess_temp_userid']).", modify_dt=".$db->qstr(date('Y-m-d H:i:s'))." \n".
						"WHERE company_id=".$db->qstr($data['company_id'])." AND pid=".$db->qstr($data['pid']);
			$saveok = $db->Execute($this->sql);
			if($saveok!==FALSE) {
				return TRUE;
			}else {
				return FALSE;
			}
		}

		function isEmployeeDeleted($pid, $agency_id)
		{
			global $db;
			$this->sql = "SELECT EXISTS(SELECT pid FROM seg_industrial_comp_emp WHERE pid=".$db->qstr($pid)."\n".
							" AND company_id=".$db->qstr($agency_id)." AND status IN ('deleted')) AS `is_deleted`";
			if($this->result = $db->GetOne($this->sql)) {
				return TRUE;
			}else {
				return FALSE;
			}
		}

		function isEmployeeExisting($pid, $agency_id)
		{
			global $db;
			$this->sql = "SELECT EXISTS(SELECT pid FROM seg_industrial_comp_emp WHERE pid=".$db->qstr($pid)."\n".
							" AND company_id=".$db->qstr($agency_id).") AS `is_deleted`";
			if($this->result = $db->GetOne($this->sql)) {
				return TRUE;
			}else {
				return FALSE;
			}
		}

		function updateEmployeeStatus($pid, $agency_id)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_comp_emp SET status='', \n".
						"modify_id=".$db->qstr($_SESSION['sess_temp_userid']).", modify_dt=".$db->qstr(date('Y-m-d H:i:s'))." \n".
						"WHERE company_id=".$db->qstr($agency_id)." AND pid=".$db->qstr($pid);
			$saveok = $db->Execute($this->sql);
			if($saveok!==FALSE) {
				return TRUE;
			}else {
				return FALSE;
			}
		}

		function searchServiceForCompany($cost_center, $keyword, $sort, $offset, $maxrows)
		{
			global $db;

			switch($cost_center)
			{
				case 'LD':
					$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name AS `item_name`, s.service_code AS `item_code`, \n".
											"s.price_cash AS `item_price`, s.area AS `item_area` FROM seg_lab_services AS s, seg_lab_service_groups AS g \n".
											"WHERE s.group_code=g.group_code AND (s.service_code LIKE '%".$keyword."%' \n".
											"OR s.name LIKE '%".$keyword."%' OR s.code_num LIKE '%".$keyword."%') \n".
											"AND s.status NOT IN (".$this->dead_stat.") ORDER BY {$sort} LIMIT $offset, $maxrows";
					break;

				case 'RD':
					$this->sql = "SELECT s.name AS `item_name`, s.service_code AS `item_code`, \n".
											"s.price_cash AS `item_price`,'RD' AS `item_area` FROM seg_radio_services AS s, seg_radio_service_groups AS g \n".
											"WHERE s.group_code=g.group_code AND (s.service_code LIKE '%".$keyword."%' \n".
											"OR s.name LIKE '%".$keyword."%') AND s.status NOT IN (".$this->dead_stat.") ORDER BY {$sort} LIMIT $offset, $maxrows";
					break;

				case 'PH':
					$this->sql="SELECT SQL_CALC_FOUND_ROWS a.*, 'PH' AS `item_area`, \n".
										"a.artikelname AS `item_name`, a.bestellnum AS `item_code`, a.price_cash AS `item_price`\n".
										"FROM care_pharma_products_main AS a\n";
					$where = array("a.is_deleted!=1");
					if ($keyword && $keyword!='*') {
						$terms = preg_split("/[,|]+/",$keyword);
						foreach ($terms as $i=>$v)
							$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
						$regexp = implode(")|(",$terms);
						$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
					}
					if ($where)
						$this->sql.= "WHERE (".implode(") AND (",$where).")\n";
					$this->sql .= "ORDER BY {$sort} LIMIT $offset, $maxrows\n";
					break;

				case 'MISC':
					$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name AS `item_name`,s.price AS `item_price`, \n".
											"s.alt_service_code AS `item_code`, 'OT' AS `item_area` \n".
											"FROM seg_other_services AS s\n".
											"LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id\n".
											"LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id\n";
					$where = array();
					$where[] = "s.name REGEXP ".$db->qstr('[[:<:]]'.$keyword)." OR s.name_short REGEXP ".$db->qstr('[[:<:]]'.$keyword);
					$where[] = " NOT s.lockflag";
					$where[] = "t.billing_related=0";
					$this->sql.= " WHERE (" .  implode(") AND (", $where) . ") ORDER BY  {$sort} LIMIT $offset, $maxrows\n";
					break;
			}

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE)
			{
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function saveCompanyPrice($company_id, $item_code, $item_area, $new_price)
		{
			global $db;
			$this->sql = "INSERT INTO $this->tb_comp_price (company_id, service_code, price, service_area) \n".
									" VALUES (".$db->qstr($company_id).", ".$db->qstr($item_code).", ".$db->qstr($new_price)." \n".
									" , ".$db->qstr($item_area).")";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updateCompanyPrice($company_id, $item_code, $item_area, $new_price)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_comp_price SET price=".$db->qstr($new_price)." \n".
									"WHERE company_id=".$db->qstr($company_id)." AND service_code=".$db->qstr($item_code)." \n".
									"AND service_area=".$db->qstr($item_area);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deleteCompanyPrice($company_id, $item_code, $item_area)
		{
			global $db;
			$this->sql = "DELETE FROM $this->tb_comp_price \n".
									"WHERE company_id=".$db->qstr($company_id)." AND service_code=".$db->qstr($item_code)." \n".
									"AND service_area=".$db->qstr($item_area);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function isExistsCompanyPrice($company_id, $item_code, $item_area)
		{
			global $db;
			$this->sql = "SELECT price FROM $this->tb_comp_price \n".
									"WHERE company_id=".$db->qstr($company_id)." AND service_code=".$db->qstr($item_code)." \n".
									"AND service_area=".$db->qstr($item_area);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				$data = $this->result->FetchRow();
				if($data["price"]!='') {
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getCompanyServices($company_id, $keyword, $sort, $offset, $maxrows)
		{
			global $db;
			$this->sql = "SELECT ic.service_code AS `item_code`, ic.price AS `item_price`, ic.service_area AS `item_area`,
					(
						IF((ic.service_area='BB' OR ic.service_area='LB' OR ic.service_area='SPL' OR ic.service_area='IC'),
							l.name,
							IF(ic.service_area='RD',
								r.name,
								IF(ic.service_area='PH',
									p.artikelname,
									IF(ic.service_area='OT',
										o.name, NULL
									)
								)
							)
						)
					) AS `item_name`
					FROM seg_industrial_comp_price AS ic
					LEFT JOIN seg_lab_services AS l ON l.service_code=ic.service_code
					LEFT JOIN seg_radio_services AS r ON r.service_code=ic.service_code
					LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=ic.service_code
					LEFT JOIN seg_other_services AS o ON o.alt_service_code=ic.service_code
					WHERE ic.company_id=".$db->qstr($company_id)." \n".
					"AND (
						(l.name REGEXP ".$db->qstr('[[:<:]]'.$keyword)." OR r.name REGEXP ".$db->qstr('[[:<:]]'.$keyword)."\n".
						"OR p.artikelname REGEXP ".$db->qstr('[[:<:]]'.$keyword)." OR o.name REGEXP ".$db->qstr('[[:<:]]'.$keyword).") \n".
					"OR \n".
						"(l.service_code REGEXP ".$db->qstr('[[:<:]]'.$keyword)." OR r.service_code REGEXP ".$db->qstr('[[:<:]]'.$keyword)."\n".
						"OR p.bestellnum REGEXP ".$db->qstr('[[:<:]]'.$keyword)." OR o.alt_service_code REGEXP ".$db->qstr('[[:<:]]'.$keyword).") \n".
					")\n".
					"ORDER BY {$sort} LIMIT $offset, $maxrows ";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else
			{
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function saveCompanyPackage($company_id, $package_id, $price)
		{
			global $db;
			$this->sql = "INSERT INTO seg_industrial_comp_package (company_id, package_id, price) \n".
									"VALUES (".$db->qstr($company_id).", ".$db->qstr($package_id).", ".$db->qstr($price).")";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function savePackage($package_name)
		{
			global $db;
			$id = create_guid();
			$this->sql = "INSERT INTO seg_industrial_package (package_id, package_desc) \n".
									"VALUES (".$db->qstr($id).", ".$db->qstr($package_name).")";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $id;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function savePackageItems($package_id, $bulk_items)
		{
			global $db;
			$package_id = $db->qstr($package_id);
			$this->sql = "INSERT INTO seg_industrial_package_details (package_id, service_code, service_area) \n".
									"VALUES ($package_id, ?, ?)";
			if($buf = $db->Execute($this->sql,$bulk_items)) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deletePackage($package_id)
		{
			global $db;
			$this->sql = "DELETE FROM seg_industrial_package WHERE package_id=".$db->qstr($package_id);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function clearPackageItems($package_id)
		{
			global $db;
			$this->sql = "DELETE FROM seg_industrial_package_details WHERE package_id=".$db->qstr($package_id);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updatePackageName($package_id, $package_name)
		{
			global $db;
			$this->sql= "UPDATE seg_industrial_package SET package_desc=".$db->qstr($package_name)." \n".
									"WHERE package_id=".$db->qstr($package_id);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updateCompanyPackageDetails($package_id, $company_id, $price)
		{
			global $db;
			$this->sql= "UPDATE seg_industrial_comp_package SET price=".$db->qstr($price)." \n".
									"WHERE package_id=".$db->qstr($package_id)." AND company_id=".$db->qstr($company_id);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function listCompanyPackages($company_id, $keyword, $sort, $offset, $maxrows)
		{
			global $db;
			$this->sql = "SELECT SQL_CALC_FOUND_ROWS p.package_id, p.package_desc, cp.price FROM seg_industrial_package AS p \n".
									"INNER JOIN seg_industrial_comp_package AS cp ON p.package_id=cp.package_id \n".
									"WHERE cp.company_id=".$db->qstr($company_id)." \n".
									"AND p.package_desc REGEXP ".$db->qstr('[[:<:]]'.$keyword)." \n".
									"ORDER BY {$sort} LIMIT $offset, $maxrows";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function listCompanyPackageItems($company_id, $package_id)
		{
			global $db;
			$this->sql = "SELECT pd.service_code , pd.service_area,
					(
						IF((pd.service_area='BB' OR pd.service_area='LB' OR pd.service_area='SPL' OR pd.service_area='IC'),
							l.name,
							IF(pd.service_area='RD',
								r.name,
								IF(pd.service_area='PH',
									ph.artikelname,
									IF(pd.service_area='OT',
										o.name, NULL
									)
								)
							)
						)
					) AS `item_name`
					FROM seg_industrial_package_details AS pd
					INNER JOIN seg_industrial_package AS p ON pd.package_id=p.package_id
					INNER JOIN seg_industrial_comp_package AS cp ON cp.package_id=p.package_id
					LEFT JOIN seg_lab_services AS l ON l.service_code=pd.service_code
					LEFT JOIN seg_radio_services AS r ON r.service_code=pd.service_code
					LEFT JOIN care_pharma_products_main AS ph ON ph.bestellnum=pd.service_code
					LEFT JOIN seg_other_services AS o ON o.alt_service_code=pd.service_code
					WHERE cp.company_id=".$db->qstr($company_id)." \n".
					"AND cp.package_id=".$db->qstr($package_id)."\n".
					"ORDER BY service_area, item_name ASC";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getCompanyPackageDetails($package_id, $company_id)
		{
			global $db;
			$this->sql = "SELECT p.package_desc, cp.price FROM seg_industrial_comp_package AS cp \n".
									"INNER JOIN seg_industrial_package AS p ON cp.package_id=p.package_id \n".
									"WHERE cp.package_id=".$db->qstr($package_id)." AND cp.company_id=".$db->qstr($company_id);
			$this->result=$db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getCompanyList($company_id)
		{
			global $db;
			$this->sql = "SELECT company_id, name FROM $this->tb_company \n".
									"WHERE company_id!=".$db->qstr($company_id)." AND (status <> 'deleted' OR ISNULL(status)) ORDER BY name ASC";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		#added by VAN 09-13-2010
		function getCompanyBillInfo($company_id){
			global $db;

			$this->sql = "SELECT * FROM seg_industrial_bill_h
											WHERE company_id='$company_id'
											AND request_flag IS NULL
											ORDER BY bill_rundate DESC LIMIT 1";

			if($this->result=$db->Execute($this->sql)) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}

		}

	function getCompanyBillDetailsInfo($company_id, $bill_nr){
			global $db;

			$this->sql = "SELECT SUM(d.total_med_charge) AS med_charge, SUM(d.total_sup_charge) AS sup_charge,
												 SUM(d.total_srv_charge) AS service_charge, SUM(d.total_msc_charge) AS misc_charge,
												 SUM(d.total_med_charge+d.total_sup_charge+d.total_srv_charge+d.total_msc_charge) AS total_amount
										FROM seg_industrial_bill_h AS h
										INNER JOIN seg_industrial_bill_d AS d
										WHERE h.company_id='$company_id' AND d.bill_nr='$bill_nr'
										GROUP BY h.company_id, h.bill_nr";

			if($this->result=$db->Execute($this->sql)) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
	}

	function existDiscountAmount($bill_nr){
		global $db;

		if (!$bill_nr)
			return FALSE;

		$sql="SELECT * FROM seg_industrial_bill_discount WHERE bill_nr='$bill_nr'";

		if ($buf=$db->Execute($sql)){
			if($buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}#end of function existSegCharityAmount

	function getCompanyBillDiscount($company_id, $bill_nr){
			global $db;

			$this->sql = "SELECT d.*
										 FROM seg_industrial_bill_discount AS d
										 INNER JOIN seg_industrial_bill_h AS h ON h.bill_nr=d.bill_nr
										 WHERE h.company_id='$company_id' AND h.bill_nr='$bill_nr'";

			if($this->result=$db->Execute($this->sql)) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
	}

	#----------- end here-----------------

}


?>
