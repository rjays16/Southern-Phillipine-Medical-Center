<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCashierService extends Core {
	/**#@+
	* @access private
	* @var string
	*/

	/**
	* Tables
	*/
	var $target;
	var $tb_main					= 'seg_other_services';
	var $tb_type 					= 'seg_cashier_account_subtypes';

	/**
	* Field names of care_pharma_products_main or care_med_products_main tables
	* @var array
	*/

//	'is_billing_related',	(after account_type field).

	var $fld_main=array('service_code',
						'alt_service_code',#added by art, to fix saving issue 08/19/2014
										'name',
										'name_short',
										'price',
										'description',
										'account_type',
										'lockflag',
                                                                                'dept_nr', //added by cha, used in cmap - 11.26.2010
										'history',
										'modify_id',
										'modify_time',
										'create_id',
										'create_time');

	/**
	* Constructor
	*/
	//function SegCashierService($target='databank') {
	//comment out by cha, 11-26-2010
	function SegCashierService($target=FALSE) {
		$this->target = $target;
		$this->coretable = $this->tb_main;
		$this->setRefArray($this->fld_main);
	}

	function getLastNr() {
		global $db;
		$this->sql="SELECT IFNULL(LPAD(MAX(CAST(service_code AS UNSIGNED)+1),8,'0'),'00000001') FROM $this->coretable";
		return $db->GetOne($this->sql);
	}

	function searchOLRServices($name, $olr, $offset, $rowcount) {
		global $db;

		$sql = array();
		$calc = "";
		if (strpos($olr,'o')!==FALSE) {
			$sql[] = "(SELECT ".(!$calc ? $calc="SQL_CALC_FOUND_ROWS" : "") ."\n".
				"'O' AS `source`,o.code AS `code`,o.description AS `name`,'Procedure' AS `group`\n".
				"FROM care_ops301_en AS o\n".
				"WHERE o.description REGEXP " . $db->qstr("[[:<:]]".$name).")";
		}
		if (strpos($olr,'l')!==FALSE) {
			$sql[] = "(SELECT ".(!$calc ? $calc="SQL_CALC_FOUND_ROWS" : "") ."\n".
				"'L' AS `source`,l.service_code AS `code`,l.name AS `name`,lg.name AS `group`\n".
				"FROM seg_lab_services AS l\n".
				"LEFT JOIN seg_lab_service_groups AS lg ON lg.group_code=l.group_code\n".
				"WHERE l.name REGEXP " . $db->qstr("[[:<:]]".$name).")";
		}
		if (strpos($olr,'r')!==FALSE) {
			$sql[] = "(SELECT ".(!$calc ? $calc="SQL_CALC_FOUND_ROWS" : "") ."\n".
				"'R' AS `source`,r.service_code AS `code`,r.name AS `name`,rg.name AS `group`\n".
				"FROM seg_radio_services AS r\n".
				"LEFT JOIN seg_radio_service_groups AS rg ON rg.group_code=r.group_code\n".
				"WHERE r.name REGEXP " . $db->qstr("[[:<:]]".$name).")";
		}
		$this->sql = implode(" UNION ", $sql);
		if ($this->sql) $this->sql .= "\nORDER BY `name`,`group`\n";
		$this->sql .=	"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function deleteService($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->coretable WHERE service_code=$nr";
		return $this->Transact();
	}

	function getPayWardSubTypes() {
		global $db;

		$this->sql = "select type_id ".
					 "   from seg_cashier_account_subtypes as scas ".
						 "   where scas.billing_related <> 0";
		if ($result = $db->Execute($this->sql)) {
			if ($result->RecordCount()) {
				return $result->GetArray();
			} else { return FALSE; }
		} else { return FALSE; }
	}

    //added by Nick 07-02-2014
    function searchServices2($name, $department, $include_locked=FALSE, $offset=0, $rowcount=15, $orderby='s.name') {
        global $db;
        $where = array();
        $params = array();
        $this->sql = "SELECT
                          SQL_CALC_FOUND_ROWS sos.name,
                          sos.name_short,
                          sos.price,
                          sos.service_code AS CODE,
                          sos.alt_service_code AS alt_code,
                          sos.description,
                          scas.name_long AS type_name,
                          scat.name_long AS ptype_name,
                          sos.account_type,
                          sos.is_not_socialized,
                          cd.name_formal AS dept_name
                        FROM
                          seg_other_services AS sos
                          LEFT JOIN seg_cashier_account_subtypes AS scas
                            ON sos.account_type = scas.type_id
                          LEFT JOIN seg_cashier_account_types AS scat
                            ON scas.parent_type = scat.type_id
                          LEFT JOIN seg_misc_depts AS smd
                            ON smd.service_code = sos.service_code
                          LEFT JOIN care_department AS cd
                            ON cd.nr = smd.dept_nr";

        //search key
        if($name){
            if(is_numeric($name)){
                $where[] = "sos.service_code=?";
                $params[] = (int)$name;
            }else{
                $where[] = "sos.name REGEXP ? OR sos.name_short REGEXP ?";
                $params[] = "[[:<:]]".$name;
                $params[] = "[[:<:]]".$name;
            }
        }

        //department
        if($department){
            $where[] = "cd.nr=?";
            $params[] = $department;
        }

        //socialized
        if(!$include_locked){
            $where[] = "NOT sos.lockflag";
        }

        //target
        if ($this->target) {
            if ($this->target == "databank"){
                $where[] = "scas.billing_related=0";
            }
            elseif ($this->target == "miscellaneous"){
                $where[] = "scas.billing_related=1";
            }
        }

        //where
        if(count($where) > 0){
            $this->sql.= " WHERE (" .  implode(") AND (", $where) . ") GROUP BY sos.service_code ";
        }

        //order
        $this->sql .= "ORDER BY $orderby ";

        //limit
        if ($offset >= 0 && $rowcount >= 0) {
            $this->sql.= "LIMIT $offset, $rowcount ";
        }

        //execute
        $rs = $db->Execute($this->sql,(count($params) > 0) ? $params : null);

        if($rs){
            return $rs;
        }else{
            return false;
        }
    }//searchServices2

	function searchServices($name, $type, $include_locked=FALSE, $offset=0, $rowcount=15, $orderby='s.name') {
		global $db;

		#modified by CHA, 06-06-2010
		#modified by cha, 11-26-2010 /*added care_department*/
		//edited by: ian villanueva 1-9-2013
		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name,s.name_short,s.price, s.lockflag AS lockflag, \n".
			"s.service_code AS code, s.alt_service_code AS alt_code, s.description,t.name_long AS type_name, \n".
			"p.name_long AS ptype_name,s.account_type,s.`is_not_socialized`, d.name_formal AS dept_name\n".
			"FROM seg_other_services AS s\n".
			"LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id\n".
			"LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id\n".
			"LEFT JOIN care_department AS d ON d.nr=s.dept_nr";
		$where = array();
		if ($name) {
			#$codename = $db->qstr($codename);
			if (is_numeric($name)) {
				$where[] = "s.service_code=".(int)$name;
			}
			else {
				$where[] = "s.name REGEXP ".$db->qstr('[[:<:]]'.$name)." OR s.name_short REGEXP ".$db->qstr('[[:<:]]'.$name);
			}
		}
		if ($type) {
			if (is_array($type)) {
				if (count($type) > 0)
					$where[] = "s.account_type IN "."(".implode(", ", $type).") OR s.account_type is null";
				else
					$where[] = "s.account_type is null";
			}
			else
				$where[] = "s.account_type=".$db->qstr($type);
		}

		if (!$include_locked) {
			$where[] = " NOT s.lockflag";
		}

		if ($this->target) {
			if ($this->target == "databank") $where[] = "t.billing_related=0";
			elseif ($this->target == "miscellaneous") $where[] = "t.billing_related=1";
		}

		if ($where)
			$this->sql.= " WHERE (" .  implode(") AND (", $where) . ")\n";

		$this->sql .= "ORDER BY $orderby\n";
		if ($offset >= 0 && $rowcount >= 0) {
			$this->sql.= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function searchServicesSansBillingCharges($name, $type, $include_locked=FALSE, $offset=0, $rowcount=15, $orderby='s.name') {
		global $db;

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name,s.name_short,s.price,s.service_code AS code,s.description,t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,s.lockflag\n".
			"FROM seg_other_services AS s\n".
			"LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id\n".
			"LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id\n";
		$where = array( 'is_billing_related=0' );
		if ($name) {
			#$codename = $db->qstr($codename);
			if (is_numeric($name)) {
				$where[] = "s.service_code=".(int)$name;
			}
			else {
				$where[] = "s.name REGEXP ".$db->qstr('[[:<:]]'.$name)." OR s.name_short REGEXP ".$db->qstr('[[:<:]]'.$name);
			}
		}
		if ($type) {
			if (is_array($type)) {
				if (count($type) > 0)
					$where[] = "s.account_type IN "."(".implode(", ", $type).") OR s.account_type is null";
				else
					$where[] = "s.account_type is null";
			}
			else
				$where[] = "s.account_type=".$db->qstr($type);
		}

		if (!$include_locked) {
			$where[] = " NOT s.lockflag";
		}

		if ($this->target) {
			if ($this->target == "databank") $where[] = "t.billing_related=0";
			elseif ($this->target == "miscellaneous") $where[] = "t.billing_related=1";
		}

		if ($where)
			$this->sql.= " WHERE (" .  implode(") AND (", $where) . ")\n";

		$this->sql .= "ORDER BY $orderby\n";
		if ($offset >= 0 && $rowcount >= 0) {
			$this->sql.= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getServiceInfo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "SELECT *\n".
			"FROM seg_other_services WHERE service_code=$nr";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				$row = $buf->FetchRow();
				return $row;
			} else { return false; }
		} else { return false; }
	}

	/**
	* Checks if the service exists based on its primary key number.
	* @access public
	* @param int Item number
	* @return boolean
	*/
	function ServiceExists($nr=0){
		global $db;
		if(empty($type)||!$nr) return false;
		$this->sql="SELECT service_code FROM $this->coretable WHERE servicecode='$nr'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function getAccountTypes($include_locked=FALSE,$exclude=NULL,$billing_related_only=FALSE) {
		global $db;
		if (!is_array($select) && $select) $select = array($select);
		$this->sql = "SELECT a.type_id,a.name_short,a.name_long FROM seg_cashier_account_types AS a\n";
		$where = array();
		if (!$include_locked)
			$where[] = "NOT a.lockflag";
		if ($exclude) {
			$where[] = "type_id NOT IN (".implode(",",$exclude).")";
		}
			if ($billing_related_only) {
			$where[] = "EXISTS (select * from seg_cashier_account_subtypes as scas\n".
						 "           where scas.parent_type = a.type_id and scas.billing_related)";
		}
		if ($where) $this->sql .= "WHERE (".implode(") AND (",$where).")\n";
		$this->sql .= "ORDER BY name_long\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else return false;
	}

	function getSubAccountTypes($parent=NULL, $include_locked=FALSE, $exclude=NULL,$billing_related_only=FALSE) {
		global $db;
		if (!is_array($parent) && $parent) $parent = array($parent);
		if (!is_array($select) && $select) $select = array($select);
		$this->sql = "SELECT a.type_id,a.name_short,a.name_long,a.parent_type,b.name_short AS parent_short,b.name_long AS parent_long FROM seg_cashier_account_subtypes AS a\n".
			"LEFT JOIN seg_cashier_account_subtypes AS b ON a.parent_type=b.type_id\n";
		$where = array();
		if ($parent) {
			$where[] = "a.parent_type IN ".implode(",",$parent).")";
		}
		if (!$include_locked)
			$where[] = "NOT a.lockflag AND NOT b.lockflag";
		if ($exclude) {
			$where[] = "a.type_id NOT IN (".implode(",",$exclude).")";
		}
		if ($billing_related_only) {
			$where[] = "a.billing_related";
		}
		if ($where) $this->sql .= "WHERE (".implode(") AND (",$where).")\n";
		$this->sql .= "ORDER BY a.name_long\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else return false;
	}
	function getServiceExisting($name='',$code,$acc_type,$dept,$price)
	{
		global $db;
		$this->sql ="SELECT sos.service_code FROM seg_other_services AS sos WHERE (SELECT REPLACE(sos.name,' ',''))= ".$db->qstr($name)." AND (SELECT REPLACE(sos.name_short,' ',''))=".$db->qstr($code)." AND sos.account_type=".$db->qstr($acc_type)." AND sos.dept_nr=".$db->qstr($dept)." AND sos.price=".$db->qstr($price); 
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
		
	}



	 function saveServices($data,$action)
    {
        global $db;

    $sql ="SELECT sos.service_code FROM seg_other_services AS sos WHERE (SELECT REPLACE(sos.name,' ',''))= ".$db->qstr($data['service_name'])." AND (SELECT REPLACE(sos.name_short,' ',''))=".$db->qstr($data['service_short_name'])." AND sos.account_type=".$db->qstr($data['account_type'])." AND sos.dept_nr=".$db->qstr($data['dept_nr'])." AND sos.price=".$db->qstr($data['price'])." AND sos.lockflag=".$db->qstr($data['lockflag']);  
        $isExists = $db->GetOne($sql);
        if ($isExists == NULL) {
	        	if($action=='create'){
	        		$fldarray = array(
	                'name' => $db->qstr($data['name']),
	                'name_short' => $db->qstr($data['name_short']),
	                'price' => $db->qstr($data['price']),
	                'description' => $db->qstr($data['description']),
	                'account_type' => $db->qstr($data['account_type']),
	                'lockflag' => $db->qstr($data['lockflag']),
	                'is_ER_default' => $db->qstr($data["is_ER_default"]),
	                'dept_nr' => $db->qstr($data["dept_nr"]),
	                'modify_id' => $db->qstr($_SESSION["sess_temp_userid"]),
	                'modify_time' => $db->qstr($data["modify_time"]),
	                'service_code'=>$db->qstr($data['service_code']),
	                'alt_service_code'=>$db->qstr($data['alt_service_code']),
	                'create_id'=>$db->qstr($data['create_id']),
	                'create_time'=>$db->qstr($data['create_time']),
	                'history' => $db->qstr($data['history'])
	          );     	
	        	}
	        	else{
				    $fldarray = array(
				            'name' => $db->qstr($data['name']),
				            'name_short' => $db->qstr($data['name_short']),
				            'price' => $db->qstr($data['price']),
				            'description' => $db->qstr($data['description']),
				            'account_type' => $db->qstr($data['account_type']),
				            'lockflag' => $db->qstr($data['lockflag']),
				            'is_ER_default' => $db->qstr($data["is_ER_default"]),
				            'service_code'=>$db->qstr($data['service_code']),
				            'dept_nr' => $db->qstr($data["dept_nr"]),
				            'modify_id' => $db->qstr($_SESSION["sess_temp_userid"]),
				            'modify_time' => $db->qstr($data["modify_time"]),
				            'history' => $data['history']
				      );
	        	}

	       $bsuccess = $db->Replace('seg_other_services', $fldarray, array('service_code'));
	        if ($bsuccess) {
	            return true;
	        } else {
	            return FALSE;
	        }



        } 

       
    }

}
?>
