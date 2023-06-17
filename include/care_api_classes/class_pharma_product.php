<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SegPharmaProduct extends Core {
	/**#@+
	* @access private
	* @var string
	*/

	/**
	* Tables
	*/
	var $tb_pmain				= 'care_pharma_products_main';
	var $tb_class 			= 'seg_product_classification';
	var $tb_prod_class 	=	'seg_pharma_products_classification';
	var $tb_areas 			= 'seg_pharma_areas';
	var $tb_avail 			= 'seg_pharma_products_availability';
	var $max_digits			= 8;


	/**
	* Field names of care_pharma_products_main or care_med_products_main tables
	* @var array
	*/
	// add remarks to array , Macoy, 2014-05-20
	var $fld_prodmain=array('bestellnum',
										'artikelnum',
										'industrynum',
										'artikelname',
										'generic',
										'description',
										'packing',
										'prod_class',
										'price_cash',
										'price_charge',
                                        'is_fs',
										'is_socialized',
										'is_restricted',
										'minorder',
										'maxorder',
										'proorder',
										'picfile',
										'encoder',
										'enc_date',
										'enc_time',
										'lock_flag',
										'medgroup',
										'cave',
										'status',
										'history',
										'modify_id',
										'modify_time',
										'create_id',
										'create_time',
										'category_id',
										'remarks',
										'drug_code',
										'is_in_inventory');  

	/**
	* Constructor
	*/
	function SegPharmaProduct(){
		$this->coretable = $this->tb_pmain;
		$this->setRefArray($this->fld_prodmain);
	}

	function createNR() {
		global $db;
		$this->sql = "SELECT MAX(CONVERT(bestellnum,UNSIGNED))+1 FROM care_pharma_products_main";
		if($buf=$db->Execute($this->sql)) {
			if($buf) {
				$row = $buf->FetchRow();
				return $row[0];
			} else { return false; }
		} else { return false; }
	}

//	function deleteProduct($id) {
//		global $db;
//		$this->sql = "DELETE FROM care_pharma_products_main WHERE bestellnum=".$db->qstr($id);
//		return $this->Transact();
//
//		if($ok=$db->Execute($this->sql)) {
//			return true;
//		} else { return false; }
//	}

	function deleteProduct($id) {
		global $db;
		$this->sql = "UPDATE care_pharma_products_main SET is_deleted=1 WHERE bestellnum=".$db->qstr($id);
		return $this->Transact();

		if($ok=$db->Execute($this->sql)) {
			return true;
		} else { return false; }
	}


	function searchProducts($codename, $generic, $classification, $prodclass, $offset=0, $rowcount=10, $sort_order='artikelname',$barcode='') {
		global $db;
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 10;
		$this->sql =
"SELECT SQL_CALC_FOUND_ROWS p.*,
IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='SC' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS sc_price,
IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='C1' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS c1_price,
IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='C2' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS c2_price,
IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='C3' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS c3_price
".
/*
	(SELECT GROUP_CONCAT(DISTINCT aa.area_name SEPARATOR ', ')
		FROM seg_pharma_products_availability AS a
		LEFT JOIN seg_pharma_areas AS aa ON aa.area_code = a.area_code
		WHERE a.bestellnum=p.bestellnum) AS availability,
	(SELECT GROUP_CONCAT(DISTINCT cc.class_name SEPARATOR ', ')
		FROM seg_pharma_products_classification AS c
		LEFT JOIN seg_product_classification AS cc ON cc.class_code = c.class_code
		WHERE c.bestellnum=p.bestellnum) AS classification
*/
"FROM care_pharma_products_main AS p
";
		$where = array("p.is_deleted!=1");
		if ($codename) {
			#$codename = $db->qstr($codename);
			$where[] = "p.bestellnum='$codename' OR p.artikelname REGEXP '[[:<:]]$codename'";
		}
		if ($generic) {
			# $generic = $db->qstr($generic);
			$where[] = "p.generic REGEXP '[[:<:]]$generic'";
		}
		if ($barcode) {
			$where[] = "p.barcode='$barcode'"; //Added by Christian 02-10-20
		}
		if ($classification) {
			# $classification = $db->qstr($classification);
			$where[] = "EXISTS(SELECT * FROM seg_pharma_products_classification WHERE bestellnum=p.bestellnum AND class_code='$classification')";
		}
		if ($prodclass) {
			$prodclass = $db->qstr($prodclass);
			$where[] = "p.prod_class=$prodclass";
		}

		if ($where)
			$this->sql.= " WHERE (" .  implode(") AND (", $where) . ")\n";

		$this->sql .=	"ORDER BY $sort_order LIMIT $offset, $rowcount";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getProductInfo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "SELECT *,\n".
			"(SELECT GROUP_CONCAT(DISTINCT area_code SEPARATOR ',') FROM seg_pharma_products_availability AS a WHERE a.bestellnum=p.bestellnum) AS availability,\n".
			"(SELECT GROUP_CONCAT(DISTINCT class_code SEPARATOR ',') FROM seg_pharma_products_classification AS c WHERE c.bestellnum=p.bestellnum) AS classification,\n".
			"(SELECT spm.description FROM `seg_phil_medicine` AS spm WHERE spm.drug_code = p.`drug_code`) AS drug_description\n".
			"FROM care_pharma_products_main AS p WHERE bestellnum=$nr";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				$row = $buf->FetchRow();
				return $row;
			} else { return false; }
		} else { return false; }
	}

	function clearProductClassification($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->tb_prod_class WHERE bestellnum=$nr";
		return $this->Transact();
	}

	function clearProductAvailability($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->tb_avail WHERE bestellnum=$nr";
		return $this->Transact();
	}

	function clearProductDiscounts($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM seg_service_discounts WHERE service_code=$nr AND service_area='PH'";
		return $this->Transact();
	}

	function setProductAvailability($nr, $availArray) {
		global $db;
		$bulk = array();
		foreach ($availArray as $avail) {
			$bulk[] = array($avail);
		}
		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO $this->tb_avail(bestellnum,area_code) VALUES($nr,?)";
		if($buf=$db->Execute($this->sql,$bulk)) {
			if($buf->RecordCount()) {
				return true;
			} else {
				return false; }
		} else { return false; }
	}

	function setProductClassification($nr, $csfArray) {
		global $db;
		$bulk = array();
		foreach ($csfArray as $csf) {
			$bulk[] = array($csf);
		}
		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO $this->tb_prod_class(bestellnum,class_code) VALUES($nr,?)";
		if($buf=$db->Execute($this->sql,$bulk)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function setProductDiscounts($nr, $dscArray, $prcArray) {
		global $db;

		$bulk = array();
		foreach ($dscArray as $i=>$dsc) {
			$bulk[] = array($dsc, $prcArray[$i]);
		}

		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO seg_service_discounts(discountid,service_code,price,service_area) VALUES(?,$nr,?,'PH')";
		if($buf=$db->Execute($this->sql,$bulk)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function getProductDiscounts($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "SELECT discountid,price FROM seg_service_discounts WHERE service_code=$nr AND service_area='PH'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return $buf;
			} else { return false; }
		} else { return false; }
	}

	function search_products_for_tray_is_INV($keyword, $discountID, $area, $offset=0, $rowcount=10, $prod_class="",$barcode='') {
		global $db;
		$this->sql="SELECT SQL_CALC_FOUND_ROWS a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

		if ($discountID != 'null') {
			$this->sql .= "IF(a.is_socialized".
					($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_pharma_areas AS a1 WHERE a1.area_code='$area'),1))" : "").
					",\n".
					"IFNULL(\n".
						"(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
						"IFNULL((IFNULL(a.price_cash,-1) * (1-(SELECT discount FROM seg_discount d3 WHERE d3.discountid='$discountID'))),IFNULL(a.price_cash,-1))),\n".
					"IFNULL(a.price_cash,-1)) AS dprice,\n";
		}
		else
				$this->sql .= "a.price_cash AS dprice,\n";

		$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
				"IFNULL(a.price_charge,-1) AS chrgrpriceppk, a.`barcode`,\n".
				"(SELECT otprice.price FROM seg_service_discounts AS otprice WHERE otprice.service_code=a.bestellnum AND otprice.discountid='OT' ) AS otprice,\n".
				"(SELECT otprice.price FROM seg_service_discounts AS otprice WHERE otprice.service_code=a.bestellnum AND otprice.discountid='D' ) AS Dprice\n".
				
				"FROM care_pharma_products_main AS a\n";

		$where = array("a.is_deleted!=1");
		//for inventory: does not include items with no price available
		// $where = array("(a.price_cash IS NOT NULL AND a.price_cash <> 0) OR (a.price_charge IS NOT NULL AND a.price_charge <> 0)");
		if ($keyword && $keyword!='*') {
			$terms = preg_split("/[,|]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
		}
		$where[] = "a.`is_in_inventory`=1";
		if ($barcode) 
			$where[] = "a.barcode='$barcode'";  //Added by Christian 02-10-20
		
		//commented for inventory integration
		/*added By MARK for availability*/
		// if ($area){ 
		// 	$data_is_inventory = $this->data_is_INV($keyword);
		// 	if ($data_is_inventory) {
		// 	   $where[] = "a.bestellnum IN (SELECT bestellnum FROM seg_pharma_products_availability WHERE area_code='$area')";
		// 	}
		// }
		if ($where)
			$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

		//added by cha, july 25, 2010
		if($prod_class) {
			$this->sql.= " AND a.prod_class=".$db->qstr($prod_class)." \n";
		}
		//end cha

		$this->sql .= "ORDER BY artikelname\n";
		if ($rowcount) {
			$this->sql .= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function search_products_for_tray($keyword, $discountID, $area, $offset=0, $rowcount=10, $prod_class="",$barcode='') {
		global $db;
		$this->sql="SELECT SQL_CALC_FOUND_ROWS a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

		if ($discountID != 'null') {
			$this->sql .= "IF(a.is_socialized".
					($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_pharma_areas AS a1 WHERE a1.area_code='$area'),1))" : "").
					",\n".
					"IFNULL(\n".
						"(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
						//"IFNULL((IFNULL(a.price_cash,-1) * (1-(SELECT discount FROM seg_discount d3 WHERE d3.discountid='$discountID'))),IFNULL(a.price_cash,-1))),\n".
						"IFNULL(a.price_cash,-1)),\n".
					"IFNULL(a.price_cash,-1)) AS dprice,\n";
		}
		else
				$this->sql .= "a.price_cash AS dprice,\n";

		$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
				"IFNULL(a.price_charge,-1) AS chrgrpriceppk,\n".
				"(SELECT otprice.price FROM seg_service_discounts AS otprice WHERE otprice.service_code=a.bestellnum AND otprice.discountid='OT' ) AS otprice,\n".
				"(SELECT otprice.price FROM seg_service_discounts AS otprice WHERE otprice.service_code=a.bestellnum AND otprice.discountid='D' ) AS Dprice,\n".
                "a.is_fs AS fs\n".
				"FROM care_pharma_products_main AS a\n";

		$where = array("a.is_deleted!=1","a.`is_in_inventory`=0");
		//for inventory: does not include items with no price available
		// $where = array("(a.price_cash IS NOT NULL AND a.price_cash <> 0) OR (a.price_charge IS NOT NULL AND a.price_charge <> 0)");
		if ($keyword && $keyword!='*') {
			$terms = preg_split("/[,|]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
		}


		if ($barcode) {
			$where[] = "a.barcode='$barcode'"; //Added by Christian 02-10-20
		}
		if ($area){ 
			   $where[] = "a.bestellnum IN (SELECT bestellnum FROM seg_pharma_products_availability WHERE area_code='$area')";
		}
		if ($where)
			$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

		//added by cha, july 25, 2010
		if($prod_class) {
			$this->sql.= " AND a.prod_class=".$db->qstr($prod_class)." \n";
		}
		//end cha

		$this->sql .= "ORDER BY artikelname\n";
		if ($rowcount) {
			$this->sql .= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	/*added By Mark*/
	function getDiscountID($id,$code){
        global $db;
        $dID = $db->qstr($id);
        $bestellnum = $db->qstr($code);
        $this->sql = "SELECT price 
        			FROM seg_service_discounts 
        			WHERE service_area='PH' AND discountid =$dID AND service_code =$bestellnum";
        $row = $db->GetRow($this->sql);
        return $row;
    }

	/*check if item is inventory or not*/
	function data_is_INV($keyword,$barcode=''){
		$data_is_inventory = $this->isInventory($keyword,$barcode);
		if ($data_is_inventory) return 1;
		else return 0;
		
	}
	/*check if item is inventory or not*/
	function isInventory($keyword,$barcode=''){
		global $db;

		$where = array("a.is_deleted!=1");
		$where[] = "a.`is_in_inventory`=1";

		if ($keyword && $keyword!='*') {
			$terms = preg_split("/[,|]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
		}

		if($barcode) 
			$where[] = "a.`barcode`='$barcode'"; //Added by Christian 02-10-20


		$this->sql="SELECT 
					a.`artikelname`,
					a.`is_in_inventory`,
  					a.`barcode`
					FROM
					  `care_pharma_products_main`  AS a ";
		if ($where) {
		$this->sql.= "WHERE (".implode(") AND (",$where).")\n";
		}
	
				if($buf=$db->Execute($this->sql)) {
						if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }

	}

	/**/


	/**
	* Checks if the product exists based on its primary key number.
	* @access public
	* @param int Item number
	* @param string Determines the final table name
	* @return boolean
	*/
	function ProductExists($nr=0,$type=''){
		global $db;
		if(empty($type)||!$nr) return false;
		$this->useProduct($type);
		$this->sql="SELECT bestellnum FROM $this->coretable WHERE bestellnum='$nr'";

				if($buf=$db->Execute($this->sql)) {
						if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	#added by angelo m. 09.13.2010
	#start
	function search_products_for_package_itemsTray($keyword, $discountID, $area, $offset=0, $rowcount=10, $prod_class="") {
		global $db;

		if($area=='LB')
			$tbl="seg_lab_services";
		elseif($area=='RD')
			$tbl="seg_radio_services";
		elseif($area=='MISC')
			$tbl="seg_other_services";

		$this->sql="SELECT
                        sls.service_code AS bestellnum,
                        sls.name         AS artikelname,
                        sls.name         AS generic,
                        ''               AS is_restricted
                    FROM $tbl AS sls
                    WHERE sls.name REGEXP '([[:<:]]($keyword))' ";

		$this->sql .= "ORDER BY sls.name\n";
		if ($rowcount) {
			$this->sql .= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	#end
	function FsProductPackage($nr,$is_fs = 0) {
		global $db;
		$this->sql = "UPDATE seg_package_details SET is_fs = $is_fs WHERE item_code=".$db->qstr($nr);
		return $this->Transact();

		if($ok=$db->Execute($this->sql)) {
			return true;
		} else { return false; }
	}
	/**
	*
	*
	*/
	function search_products_for_tray2($keyword, $discountID, $area, $offset=0, $rowcount=10, $prod_class="") {
		global $db;
		$this->sql="SELECT SQL_CALC_FOUND_ROWS a.bestellnum, a.artikelname, a.generic, a.is_socialized, 'P' as source, '' as account_type, \n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

		$misc_sql = " SELECT s.alt_service_code AS bestellnum , s.name AS artikelname, s.description as generic, s.is_discountable AS is_socialized, 'M' as source, s.account_type,
					-1 AS cashscprice ,-1 as chargescprice,
					IFNULL(s.price,-1) as dprice, IFNULL(s.price,-1)  as cshrpriceppk, IFNULL(s.price,-1)  as chrgrpriceppk
					FROM seg_other_services AS s
					LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
					LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id "; 

		if ($discountID) {
			$this->sql .= "IF(a.is_socialized".
					($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_pharma_areas AS a1 WHERE a1.area_code='$area'),1))" : "").
					",\n".
					"IFNULL(\n".
						"(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
						"IFNULL(a.price_cash,-1)),\n".
					"IFNULL(a.price_cash,-1)) AS dprice,\n";
		}
		else{
			$this->sql .= "a.price_cash AS dprice,\n";
		}
				

		$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
				"IFNULL(a.price_charge,-1) AS chrgrpriceppk\n".
				"FROM care_pharma_products_main AS a\n";

		$where = array("a.is_deleted!=1");
		$where_misc = array(" NOT s.lockflag ");
		if ($keyword && $keyword!='*' && !is_numeric($keyword)) {
			$terms = preg_split("/[,|]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
			$where_misc[] = "(s.name REGEXP '[[:<:]]($regexp)')";
		}elseif (is_numeric($keyword)) {
			$where[] = "(bestellnum LIKE '%$keyword%')";
			$where_misc[] = "(s.alt_service_code LIKE '%$keyword%')";
		}
		//commented for inventory integration
		// if ($area) $where[] = "a.bestellnum IN (SELECT bestellnum FROM seg_pharma_products_availability WHERE area_code='$area')";
		if ($where){
			$this->sql.= "WHERE (".implode(") AND (",$where).")\n";
			$misc_sql .= "WHERE (".implode(") AND (",$where_misc).")\n";
		}
		
		//added by cha, july 25, 2010
		if($prod_class) {
			$this->sql.= " AND a.prod_class=".$db->qstr($prod_class)." \n";
		}
		//end cha

		$this->sql .= " UNION ". $misc_sql; 

		$this->sql .= "ORDER BY artikelname\n";
		if ($rowcount) {
			$this->sql .= "LIMIT $offset, $rowcount\n";
		}
		
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	// ADDED BY MARK 2016-10-06
	function getAPIfromPharmaArea($area_code){
		global $db;
		$this->sql="SELECT inv_api_key FROM seg_pharma_areas WHERE area_code=".$db->qstr($area_code);
		 if ($result=$db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    if (!is_null($row["inv_api_key"])) {
                        $APIKEY = $row["inv_api_key"];
                    }
                }
            }
        }
        return $APIKEY;
	}
	// ADDED BY MARK 2016-10-06
	function getAPIfByName($area_code){
		global $db;
		$this->sql="SELECT area_name FROM seg_pharma_areas WHERE area_code=".$db->qstr($area_code);
		 if ($result=$db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    if (!is_null($row["area_name"])) {
                        $APIKEY = $row["area_name"];
                    }
                }
            }
        }
        return $APIKEY;
	}
	// ADDED by MARK Feb 1, 2017
    function getAllow_socialized($area){
        global $db;

        $this->sql = "SELECT allow_socialized 
        			FROM seg_pharma_areas 
        			WHERE area_code = ".$db->qstr($area);
        $row = $db->GetRow($this->sql);

        return $row;
    }

    function getRouteList() {
		global $db;

		$this->sql="SELECT * FROM seg_phil_routes";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getFrequencyList(){
		global $db;

		$this->sql="SELECT * FROM seg_phil_frequency";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getDosageList(){
		global $db;

		$this->sql="SELECT * FROM seg_phil_medicine_strength";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getPreviousDRF($encounter_nr,$bestellnum) {
		global $db;
		
		if(!$encounter_nr){
			return $this->result = false;
		}
		/*$this->sql="SELECT
				  dosage,
				  frequency,
				  route 
				FROM
				  seg_pharma_items_cf4 
				WHERE bestellnum = ".$db->qstr($bestellnum)."
				  AND (
				  	dosage != '' 
				  	AND frequency != '' 
				    AND route != '') 
				  AND refno IN 
				  (SELECT 
				    refno 
				  FROM
				    seg_pharma_orders 
				  WHERE encounter_nr = ".$db->qstr($encounter_nr)."
				    AND is_deleted != '1' 
				  UNION
				  SELECT 
				    refno 
				  FROM
				    seg_more_phorder 
				  WHERE encounter_nr = ".$db->qstr($encounter_nr).") 
				ORDER BY IFNULL(modify_dt, create_dt) DESC 
				LIMIT 1 ";*/

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
?>
