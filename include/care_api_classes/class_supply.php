<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");    
require_once($root_path.'include/care_api_classes/class_core.php');

class SegOrder extends Core {
    
    var $target;
    var $items_tb;
    var $discounts_tb;
    var $prod_tb;
    var $seg_discounts_tb = "seg_discounts";
    var $person_tb = "care_person";
  var $walkin_tb = "seg_walkin";

    
    var $fld_pharma_order = array(
        "refno",
        "pharma_area",
        "orderdate",
        "pid",
    "walkin_pid",
        "encounter_nr",
        "ordername",
        "orderaddress",
        "amount_due",
        "is_cash",
        "is_tpl",
        "is_urgent",
        "discount",
        "discountid",
        "comments",
        "create_id",
        "create_time",
        "modify_id",
        "modify_time"
    );
    
    function SegOrder($target='pharma') {
        $this->setTarget($target);
    }
    
    function setTarget($target) {
        if ($target != "pharma" || $target != "med") $target = "pharma";
        $this->target = $target;
        $this->coretable = "seg_".$target."_orders";
        $this->setTable($this->coretable);
        //$this->items_tb = "seg_".$target."_order_items";
        $this->items_tb = "seg_internal_request_details";
        $this->prod_tb = "care_".$target."_products_main";
        $this->discounts_tb = "seg_".$target."_order_discounts";
        if ($target == "pharma")
            $this->setRefArray($this->fld_pharma_order);
        else
            $this->setRefArray($this->fld_med_order);
    }
    
    function getLastNr($today) {
        global $db;
        $today = $db->qstr($today);
        $this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
        return $db->GetOne($this->sql);
    }
    
    function deleteOrder($refno) {
        global $db;
        $refno = $db->qstr($refno);
        $this->sql = "DELETE FROM $this->coretable WHERE refno=$refno";
    return $this->Transact();
    }
    
    /*function clearOrderList($refno) {
        global $db;
        $refno = $db->qstr($refno);
        $this->sql = "DELETE FROM $this->items_tb WHERE refno=$refno";
    return $this->Transact();
    }*/
    function clearRequestList($refno) {
        global $db;
        $refno = $db->qstr($refno);
        $this->sql = "DELETE FROM seg_internal_request_details WHERE refno=$refno";
    return $this->Transact();
    }
    /*function addOrders($refno, $orderArray) {
        global $db;
        $refno = $db->qstr($refno);
        $this->sql = "INSERT INTO $this->items_tb(refno,bestellnum,quantity,pricecash,pricecharge,is_consigned,price_orig) VALUES($refno,?,?,?,?,?,?)";
        if($buf=$db->Execute($this->sql,$orderArray)) {
            if($buf->RecordCount()) {
                return true;
            } else { return false; }
        } else { return false; }
    } ORIGINAL*/
		
		function addRequest($refno, $bulk) {                                
				global $db;
				$this->sql = "INSERT INTO seg_internal_request_details (refno, item_code, item_qty, unit_id, is_unitperpc)	VALUES ($refno,?,?,?,?)";
				if($buf=$db->Execute($this->sql,$bulk)) {
            if($buf->RecordCount()) {
                return true;
            } else { return false; }
        } else { return false; }
		}
        
    function addReqServ($refno, $bserv) {                                
                global $db;
                $this->sql = "INSERT INTO seg_requests_served (request_refno, issue_refno, item_code, requested_qty, served_qty)    VALUES ($refno,?,?,?,?)";
                if($buf=$db->Execute($this->sql,$bserv)) {
            if($buf->RecordCount()) {
                return true;
            } else { return false; }
        } else { return false; }
        } 
		
        
        
        
    function addOrders($refno, $orderArray) {
        global $db;
        $refno = $db->qstr($refno);
        $this->sql = "INSERT INTO $this->items_tb(refno,item_code,item_qty,unit_id,is_unitperpc) VALUES ('$refno','?','?','?','?')";
        if($buf=$db->Execute($this->sql,$orderArray)) {
            if($buf->RecordCount()) {
                return true;
            } else { return false; }
        } else { return false; }
    }
    
    function grantPharmacyRequest($refno, $items) {
        global $db;
        if (!is_array($items)) return false;
        if (empty($arrayItems))
            return TRUE;
        $this->sql="INSERT INTO seg_granted_request (ref_no, ref_source, service_code) VALUES ($refno, 'PH', ?)";
        if ($db->Execute($this->sql,array($items))) {
            if ($db->Affected_Rows()) {
                return TRUE;
            }else{ return FALSE; }
        }else{ return FALSE; }
    }
    
    function clearDiscounts($refno) {
        global $db;
        $refno = $db->qstr($refno);
        $this->sql = "DELETE FROM $this->discounts_tb WHERE refno=$refno";
    return $this->Transact();
    }
    
    function getOrderInfo($refno) {
    global $db;
        $refno = $db->qstr($refno);
        $this->sql="SELECT o.*,\n".
                "IFNULL(p.name_last,w.name_last) AS name_last,".
        "IFNULL(p.name_first,w.name_first) AS name_first,".
        "IFNULL(p.name_middle,w.name_middle) AS name_middle,\n".
                "a.area_name\n".
                "FROM $this->coretable AS o\n".
                "LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
        "LEFT JOIN $this->walkin_tb AS w ON w.pid=o.walkin_pid\n".
                "LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
                "WHERE o.refno=$refno";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }
    }
    
    function getOrderDiscounts($refno) {
    global $db;
        $refno = $db->qstr($refno);
        $this->sql="SELECT discountid\n".
                "FROM $this->discounts_tb\n".
                "WHERE refno=$refno";
        if($this->result=$db->Execute($this->sql)) {
            $ret = array();
            while ($row = $this->result->FetchRow())
                $ret[$row['discountid']] = $row['discountid'];
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
        $refno = $db->qstr($refno);
        $this->sql="SELECT i.*,p.artikelname,p.description\n".
                "FROM $this->items_tb AS i\n".
                "LEFT JOIN $this->prod_tb AS p ON p.bestellnum=i.bestellnum\n".
                "WHERE i.refno=$refno";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }
    }
    
    function getOrderItemsFullInfo($refno, $discountID) {
    global $db;
        $refno = $db->qstr($refno);
        $this->sql = "SELECT o.quantity,o.pricecash AS `force_price`,o.is_consigned,a.*,\n".
                "IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
                "IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
                "IFNULL(a.price_charge,0) AS chrgrpriceppk,\n".
                "IF(a.is_socialized,\n".
                    "IFNULL((SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),a.price_cash),\n".
                    "a.price_cash) AS dprice,\n".
                "IFNULL(a.price_cash,0) AS cshrpriceppk,\n".
                "o.serve_status,o.serve_remarks\n".
                "FROM seg_pharma_order_items AS o\n".
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
            if($buf->RecordCount()) {
                return true;
            } else { return false; }
        } else { return false; }
    }
    
    function getActiveOrders($filters, $offset=0, $rowcount=15) {
    global $db;
        #if (is_numeric($now)) $dDate = date("Ymd",$now);
        #$where = array();
        #if ($dDate) $where[] = "o.orderdate=$dDate";
        #else $dDate = $db->qstr($dDate);
        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
    $phFilters = array();
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
                    $phFilters[] = "DATE(orderdate)='$v'";
                break;
                case 'datebetween':
                    $phFilters[] = "orderdate>='".$v[0]."' AND orderdate<='".$v[1]."'";
                break;
                case 'name':
                    $phFilters[] = "ordername REGEXP '[[:<:]]".substr($db->qstr($v),1);
                break;
                case 'pid':
                    $phFilters[] = "o.pid REGEXP ".$db->qstr($v);
                break;
                case 'patient':
                    $phFilters[] = "o.pid=".$db->qstr($v)." OR o.walkin_pid=".$db->qstr($v);
                break;
                case 'inpatient':
                    $phFilters[] = "o.encounter_nr=".$db->qstr($v);
                break;
                case 'walkin':
                    $phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
                break;
                case 'area':
                    $phFilters[] = 'pharma_area='.$db->qstr($v);
                break;
            }
        }}
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";
        
#        $havingClause = implode(") AND (",$filters);
#        if ($havingClause) $havingClause = "HAVING ($havingClause)";
        
        
        $this->sql="SELECT SQL_CALC_FOUND_ROWS o.*,a.area_name AS `area_full`,\n".
        "IFNULL(p.name_last,w.name_last)  AS name_last,IFNULL(p.name_first,w.name_first) AS name_first,IFNULL(p.name_middle,w.name_middle) AS name_middle,\n".
                "(SELECT GROUP_CONCAT(prod.artikelname SEPARATOR '\\n')\n".
                "FROM seg_pharma_order_items AS oi\n".
                "LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
                "WHERE o.refno = oi.refno) AS `items`\n".
                "FROM $this->coretable AS o\n".
                "LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
        "LEFT JOIN $this->walkin_tb AS w ON w.pid=o.walkin_pid\n".
                "LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
                "WHERE NOT EXISTS(SELECT * FROM seg_pay_request AS pr WHERE pr.ref_no=o.refno AND pr.ref_source='PH')\n".
                    "AND ($phWhere)\n".
                "ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n".
                "LIMIT $offset, $rowcount";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }
    }
    
    function getServeReadyOrders($filters, $offset=0, $rowcount=15) {
    global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        $phFilters = array();
        $phFields = array();
        if (is_array($filters)) {
        foreach ($filters as $i=>$v) {
            switch (strtolower($i)) {
                case 'withtotals':
                    $phFields[] = '(SELECT SUM(oi.pricecash*oi.quantity) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS amount_due';
                break;
                case 'withservecount':
                    $phFields[] = "(SELECT COUNT(*) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS `count_total_items`";
                    $phFields[] = "(SELECT COUNT(*) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno AND oi.serve_status='S') AS `count_served_items`";
                break;
                case 'area':
                    if (strtoupper($v)!='ALL')
                        $phFilters[] = 'pharma_area='.$db->qstr($v);
                break;
                case 'refno':
                    $phFilters[] = "o.refno=".$db->qstr($v);
                break;
                case 'refno+name':
                    $phFilters[] = "o.refno=".$db->qstr($v)." OR ordername REGEXP '[[:<:]]".substr($db->qstr($v),1);
                break;
                case 'nopay':
                    $phFilters[] = "pay.or_no IS NULL";
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
                break;
                case 'datethismonth':
                    $phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
                break;
                case 'date':
                    $phFilters[] = "DATE(orderdate)='$v'";
                break;
                case 'datebetween':
                    $phFilters[] = "orderdate>='".$v[0]."' AND orderdate<='".$v[1]."'";
                break;
                case 'name':
                    $phFilters[] = "ordername REGEXP '[[:<:]]".substr($db->qstr($v),1);
                break;
                case 'pid':
                    $phFilters[] = "o.pid REGEXP ".$db->qstr($v);
                break;
                case 'patient':
                    $phFilters[] = "o.pid=".$db->qstr($v);
                break;
                case 'inpatient':
                    $phFilters[] = "o.encounter_nr=".$db->qstr($v);
                break;
                case 'walkin':
                    $phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
                break;
                case 'serve':
                    switch (strtolower($v)) {
                        case 's':
                            $phHaving[] = "count_total_items=count_served_items";
                        break;
                        case 'p':
                            $phHaving[] = "(count_served_items<count_total_items) AND (count_served_items>0)";
                        break;
                        case 'n':
                            $phHaving[] = "count_served_items=0";
                        break;
                    }                    
                break;
            }
        }}
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";
        $fields=implode(",\n",$phFields);
        if ($fields) $fields .= ',';
        
#        $havingClause = implode(") AND (",$filters);
#        if ($havingClause) $havingClause = "HAVING ($havingClause)";
        
        $this->sql="SELECT SQL_CALC_FOUND_ROWS DISTINCT o.*,a.area_name AS `area_full`,p.name_last,p.name_first,p.name_middle,IFNULL(am.amount,-1) AS ss_amount,\n".
                "pay.or_no AS `or_no`,\n".
                $fields.
                "(SELECT GROUP_CONCAT(prod.artikelname SEPARATOR '\\n')\n".
                "FROM seg_pharma_order_items AS oi\n".
                "LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
                "WHERE o.refno = oi.refno) AS `items`\n".
                "FROM $this->coretable AS o\n".
                "LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
                "LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
                "LEFT JOIN seg_charity_amount AS am ON am.ref_no=o.refno AND am.ref_source='PH'\n".
                "LEFT JOIN seg_pay_request AS pr ON pr.ref_no=o.refno AND pr.ref_source='PH'\n".
                "LEFT JOIN seg_pay AS pay ON (pay.or_no=pr.or_no AND pay.cancel_date IS NULL)\n".
                "WHERE\n".
                    "IF (o.is_cash,\n".
                        "(pay.or_no IS NOT NULL\n".
                        "OR o.is_tpl=1\n".# To pay later
                        "OR (am.amount=0 AND am.amount IS NOT NULL)\n".
                        "OR o.amount_due=0)\n".
                    ",1)\n". # Charge
                    "AND ($phWhere)\n";
        if ($phHaving) $this->sql .= "HAVING (" . implode(") AND (",$phHaving) . ")\n";
        $this->sql .= "ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n" . 
            "LIMIT $offset, $rowcount";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }
    }
    
    function changeServeStatus($refno, $itemsArray, $statusArray, $remarksArray) {
        if (!$itemsArray || !$statusArray) return FALSE;
        if (!is_array($itemsArray)) $itemsArray = array($itemsArray);
        if (!is_array($statusArray)) $statusArray = array($statusArray);
        if (!is_array($remarksArray)) $remarksArray = array($remarksArray);
        global $db;
        foreach ($itemsArray as $i=>$item) {
            $db->Replace("seg_pharma_order_items", 
                array("refno"=>$db->qstr($refno), "bestellnum"=>$db->qstr($item), "serve_status"=>$db->qstr($statusArray[$i]), "serve_remarks"=>$db->qstr($remarksArray[$i])),
                array("refno","bestellnum"),
                $autoquote = FALSE);
        }
        return TRUE;
    }
  
  function getPharmaArea($area, $fields='*') {
    global $db;
    $area = $db->qstr($area);
    $this->sql = "SELECT $fields FROM seg_pharma_areas WHERE area_code=$area";
    if($this->result=$db->GetRow($this->sql)) {
      return $this->result;
    } else { return false; }
  }
  
}

?>