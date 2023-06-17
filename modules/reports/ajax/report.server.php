<?php
    function chargeName(){
        global $db;
        $objResponse = new xajaxResponse();
        $option_all = "<option value=''>-Select Patient Charge Type-</option>\n<option value='all'>All</option>\n";

        $sql1="SELECT id,charge_name,description FROM seg_type_charge_pharma WHERE in_pharmacy = 1 ORDER BY ordering ASC";

        $result1=$db->Execute($sql1);
        while ($charged_name=$result1->FetchRow()){
            $option_all .= "<option value=\"".strtoupper($charged_name['id'])."\">".strtoupper($charged_name['charge_name'])."-".$charged_name['description']."</option>\n";     
        }
        
        $objResponse->assign('param_phar_charge_type', 'innerHTML', $option_all);
        return $objResponse;
    }
    
    function getMuniCityandProv($brgy_nr) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $sql = "SELECT p.prov_nr, m.mun_nr, p.prov_name, m.mun_name \n
                      FROM (seg_barangays b INNER JOIN seg_municity m \n
                         ON b.mun_nr = m.mun_nr) INNER JOIN seg_provinces p \n
                         ON m.prov_nr = p.prov_nr \n
                         WHERE b.brgy_nr = $brgy_nr";
        #$objResponse->alert($sql); 
        if ($result = $db->Execute($sql)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setMuniCity", (is_null($row['mun_nr']) ? 0 : $row['mun_nr']), (is_null($row['mun_name']) ? '' : trim($row['mun_name'])));
                $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : trim($row['prov_name'])));
            }
        }
        
        return $objResponse;
    }
    
    function getProvince($mun_nr) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $sql = "SELECT p.prov_nr, p.prov_name \n
                      FROM seg_municity m INNER JOIN seg_provinces p \n
                         ON m.prov_nr = p.prov_nr \n
                      WHERE m.mun_nr = $mun_nr";
        #$objResponse->alert($sql);
        if ($result = $db->Execute($sql)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : trim($row['prov_name'])));
            }
        }
        
        return $objResponse;    
    }

    function getIndexLevel2($lvl1) {
        global $db;

        $objResponse = new xajaxResponse();

        $sql = "SELECT id2, UPPER(index_name_2) AS namedesc FROM seg_radio_index_level_02 lvl2 WHERE id_level_01 = {$lvl1}";

        $data = array();

        if($result = $db->Execute($sql)) {
            while($row = $result->FetchRow()) {
                $data[] = array($row['id2'] => $row['namedesc']);
            }

            $details = array();

            foreach ($data as $value) {
                foreach ($value as $key => $value2) {
                    $details[$key] = $value2;
                }
            }

            $details = json_encode($details);
            $objResponse->call("index_lvl2", $details);
        } else {
            $details = array();
            $details = json_encode($details);
            $objResponse->call("index_lvl2", $details);
        }

        return $objResponse;
    }

    function getIndexLevel3($lvl2) {
        global $db;

        $objResponse = new xajaxResponse();

        $sql = "SELECT lvl3.`id3`, UPPER(lvl3.`index_name_3`) AS namedesc FROM seg_radio_index_level_03 lvl3
                INNER JOIN seg_radio_index_level_02 AS lvl2  ON lvl3.`fk_lvl_one` = lvl2.`id2`
                WHERE lvl2.`id2` = {$lvl2}";

        $data = array();

        if($result = $db->Execute($sql)) {
            while($row = $result->FetchRow()) {
                $data[] = array($row['id3'] => $row['namedesc']);
            }

            $details = array();

            foreach ($data as $value) {
                foreach ($value as $key => $value2) {
                    $details[$key] = $value2;
                }
            }

            $details = json_encode($details);
            $objResponse->call("index_lvl3", $details);
        } else {
            $details = array();
            $details = json_encode($details);
            $objResponse->call("index_lvl3", $details);
        }

        return $objResponse;
    }

    function getIndexLevel4($lvl3) {
        global $db;

        $objResponse = new xajaxResponse();

        $sql = "SELECT id4, UPPER(index_name_4) AS namedesc FROM seg_radio_index_level_04 lvl4
                INNER JOIN seg_radio_index_level_03 lvl3 ON lvl4.`index_id_3` = lvl3.`id3` 
                WHERE lvl3.`id3` = {$lvl3}";

        $data = array();

        if($result = $db->Execute($sql)) {
            while($row = $result->FetchRow()) {
                $data[] = array($row['id4'] => $row['namedesc']);
            }

            $details = array();

            foreach ($data as $value) {
                foreach ($value as $key => $value2) {
                    $details[$key] = $value2;
                }
            }

            $details = json_encode($details);
            $objResponse->call("index_lvl4", $details);
        } else {
            $details = array();
            $details = json_encode($details);
            $objResponse->call("index_lvl4", $details);
        }

        return $objResponse;
    }
    function getDeptWard($id){
        global $db;

         $objResponse = new xajaxResponse();

         if ($id == 'er' || $id =='opd') {
          /*  $sql = "SELECT area_code AS id,area_name AS namedesc FROM seg_pharma_areas WHERE NOT(is_deleted)";*/
            $sql = "SELECT nr AS id, name_formal AS namedesc FROM care_department cd WHERE cd.is_inactive = 0 AND TYPE = 1 ORDER BY name_formal";
        /*    $objResponse->alert($sql);*/
            $data = array();

        if($result = $db->Execute($sql)) {
            while($row = $result->FetchRow()) {
                $data[] = array($row['id'] => $row['namedesc']);
            }

            $details = array();

            foreach ($data as $value) {
                foreach ($value as $key => $value2) {
                    $details[$key] = $value2;
                }
            }

            $details = json_encode($details);
            $objResponse->call("dept_ward", $details);
        } else {
            $details = array();
            $details = json_encode($details);
            $objResponse->call("dept_ward", $details);
        }

        return $objResponse;
         }
         elseif($id == 'ipd'){
$sql = "SELECT nr AS id,name AS namedesc FROM care_ward ORDER BY name";
$data = array();
        if($result = $db->Execute($sql)) {
            while($row = $result->FetchRow()) {
                $data[] = array('ward'.'--'.$row['id'] => $row['namedesc']);
            }

            $details = array();

            foreach ($data as $value) {
                foreach ($value as $key => $value2) {
                    $details[$key] = $value2;
                }
            }

            $details = json_encode($details);
            $objResponse->call("dept_ward", $details);
        } else {
            $details = array();
            $details = json_encode($details);
            $objResponse->call("dept_ward", $details);
        }
        return $objResponse;
         }
         elseif($id == 'walkin' || $id == 'all'){
            $details = json_encode($details);
            $objResponse->call("dept_ward", $details);
         }

         return $objResponse;
    }

    // added by: syboy 03/15/2016 : meow
    function getGuarantor($id){
        global $db;

        $objResponse = new xajaxResponse();

        $sql = "SELECT id, UPPER(name) as name FROM seg_grant_accounts WHERE account_type_id = ?";
        $data = array();
        if ($result = $db->Execute($sql, $id)) {
            while ($row = $result->FetchRow()) {
                $data[] = array($row['id'] => $row['name']);
            }
            $details = array();
            foreach ($data as $value) {
                foreach ($value as $key => $value2) {
                    $details[$key] = $value2;
                }
            }
            $details = json_encode($details);
            $objResponse->call("guarantor", $details);
        }

        return $objResponse;   
    }
    // ended syboy

    function getICDICP($id,$datefrom,$dateto){
        global $db;
        $objResponse = new xajaxResponse();
       foreach ($id as $value) {
        $mem_cat .= "'".$value."',";
       }
        $category = rtrim($mem_cat,',');
       if(!strpos($mem_cat,'all') !== false){
            if(!empty($mem_cat)){
                $condition = "AND sm.memcategory_code IN ($category)";
            }
       }
        $from =date('Y-m-d',strtotime($datefrom));
        $to = date('Y-m-d',strtotime($dateto));
        $data_icp = array();
        $data_icd = array();
        $sql_icd = "SELECT package.`code` as id,package.`description` as namedesc,COUNT(bill.bill_nr) AS cnt 
            FROM seg_billing_encounter AS bill 
               INNER JOIN care_encounter e 
                ON e.encounter_nr = bill.encounter_nr 
               INNER JOIN seg_billing_caserate AS caserate 
                ON bill.bill_nr = caserate.`bill_nr` 
               INNER JOIN `seg_case_rate_packages` AS package 
                ON caserate.`package_id` = package.`code`
               LEFT JOIN seg_encounter_memcategory sem 
                ON sem.encounter_nr = e.encounter_nr 
               LEFT JOIN seg_memcategory sm 
                ON sm.memcategory_id = sem.memcategory_id 
            WHERE STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') >= STR_TO_DATE('$from', '%Y-%m-%d') 
                  AND STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') <= STR_TO_DATE('$to', '%Y-%m-%d') 
                  AND bill.is_deleted IS NULL 
                  AND bill.is_final = 1
                  AND caserate.`rate_type` = 1 
                  AND package.`case_type` = 'm'
                    $condition
            GROUP BY package.code 
            ORDER BY cnt DESC LIMIT 15 ";
            
            if($result = $db->Execute($sql_icd)) {
            while($row_icd = $result->FetchRow()) {
                $data_icd[] = array($row_icd['id'] => utf8_encode($row_icd['namedesc']));
            }

            $details_icd = array();

            foreach ($data_icd as $value_icd) {
                foreach ($value_icd as $key => $value_icd2) {
                    $details_icd[$key] = $value_icd2;
                }
            }

            $details_icd = json_encode($details_icd);
            $objResponse->call("get_icd", $details_icd);
        } 

        $sql_icp = "SELECT package.`code` as id,package.`description` as namedesc,COUNT(bill.bill_nr) AS cnt 
            FROM seg_billing_encounter AS bill 
               INNER JOIN care_encounter e 
                ON e.encounter_nr = bill.encounter_nr 
              INNER JOIN seg_billing_caserate AS caserate 
                ON bill.bill_nr = caserate.`bill_nr` 
              INNER JOIN `seg_case_rate_packages` AS package 
                ON caserate.`package_id` = package.`code`
              LEFT JOIN seg_encounter_memcategory sem 
                ON sem.encounter_nr = e.encounter_nr 
              LEFT JOIN seg_memcategory sm 
                ON sm.memcategory_id = sem.memcategory_id 
            WHERE STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') >= STR_TO_DATE('$from', '%Y-%m-%d') 
                  AND STR_TO_DATE(bill.bill_dte, '%Y-%m-%d') <= STR_TO_DATE('$to', '%Y-%m-%d') 
                  AND bill.is_deleted IS NULL 
                  AND bill.is_final = 1
                  AND caserate.`rate_type` = 1 
                  AND package.`case_type` = 'p'
                  $condition
            GROUP BY package.code 
            ORDER BY cnt DESC LIMIT 15";

            if($result = $db->Execute($sql_icp)) {
                while($row_icp = $result->FetchRow()) {
                    $data_icp[] = array($row_icp['id'] => $row_icp['namedesc']);
                }

            $details_icp = array();

            foreach ($data_icp as $value_icp) {
                foreach ($value_icp as $key => $value_icp2) {
                    $details_icp[$key] = $value_icp2;
                }
            }
            $details_icp = json_encode($details_icp);
            $objResponse->call("get_icp", $details_icp);
        } 
        
        return $objResponse;
    }

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'modules/reports/ajax/report.common.php');        
    $xajax->processRequest();
?>