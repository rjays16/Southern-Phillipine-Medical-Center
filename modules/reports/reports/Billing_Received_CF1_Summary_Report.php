<?php
require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';
define(GYNE,'124');
define(OB,'139');
define(PEDIANEW, '193');
define(NICU, '191');
define(HOSPITAL_SPONSORED_MEMBER, 'HSM');
define(POINT_OF_SERVICE,'POS');
define(WELL_BABY,12);
//Created by Kenneth 09/28/2016

global $db;
            switch ($billing_categories) {
                   case 'OBMAIN':
                   $category = "OB MAIN";
                   $cond_categories= " AND ce.`consulting_dept_nr` IN('".GYNE."','".OB."','".PEDIANEW."','".NICU."') AND seim.`member_type` NOT IN ('".HOSPITAL_SPONSORED_MEMBER."','".POINT_OF_SERVICE."')";
                       break;
                    case 'OBPOC':
                    $category = "OB POC";
                    $cond_categories= " AND ce.`consulting_dept_nr` IN('".GYNE."','".OB."','".PEDIANEW."','".NICU."') AND seim.`member_type` IN ('".HOSPITAL_SPONSORED_MEMBER."')";
                        break;
                    case 'RM':
                    $category ="REGULAR MAIN";
                    $cond_categories= " AND ce.`consulting_dept_nr` NOT IN('".GYNE."','".OB."','".PEDIANEW."','".NICU."') AND seim.`member_type` NOT IN('".HOSPITAL_SPONSORED_MEMBER."','".POINT_OF_SERVICE."')";
                        break;
                   case 'RP':
                   $category ="REGULAR POC";
                   $cond_categories= " AND ce.`consulting_dept_nr` NOT IN('".GYNE."','".OB."','".PEDIANEW."','".NICU."') AND seim.`member_type` IN ('".HOSPITAL_SPONSORED_MEMBER."')";
                       break;
                    case 'POS':
                    $category ="POINT OF SERVICE";
                    $cond_categories= " AND seim.`member_type` IN ('".POINT_OF_SERVICE."')";
                       break;
                    case 'WB':
                    $category ="WELL BABY";
                    $cond_categories= " AND ce.`encounter_type` IN ('".WELL_BABY."') AND seim.`member_type` NOT IN ('".POINT_OF_SERVICE."')";
                      break;
               }

$sql2 = $db->Execute($sql);
$res = $sql2->FetchRow();

  if(!isset($billing_encoder)||$billing_encoder=='all')
      {
        $res=array('thebiller' => "ALL");
        $cond2="";
      }
      else
      {
        $sql = "SELECT fn_get_personell_name(".$db->qstr($billing_encoder).") AS thebiller";
        $sql2 = $db->Execute($sql);
        $res = $sql2->FetchRow();

        $cond2="AND IF(
                  (SELECT 
                    login_id 
                  FROM
                    care_users 
                  WHERE personell_nr = ".$db->qstr($billing_encoder).") = seimf.create_id,
                  TRUE,
                  fn_get_personell_name (".$db->qstr($billing_encoder).") = seimf.create_id) ";
      }
      if(empty($billing_categories)){
        $category="ALL";
      }

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('title', strtoupper($report_title));
$params->put('dateRange',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));
$params->put('biller', $res['thebiller']);
$params->put('category',$category);

# ADDED by: JEFF
# DATE: 08-02-17
# PURPOSE: for fetching the loged-in user
// $params->put('reciever', strtoupper($_SESSION['sess_user_name']));
$params->put('reciever', $res['thebiller']);

# for no selection trap query, will choose the all departments
// define('blnk', "");
// if ($dept_nr == '')
//     {
//       $department_catch = blnk;
//     }
//     else
//     {
//       $dept_nr_holder = $db->qstr($dept_nr);
//       $department_catch = "AND ce.`consulting_dept_nr` = ".$dept_nr_holder." ";
//     }

$cond1 = "  (    IFNULL(DATE(seim.`modify_dt`),DATE(seim.`create_dt`)) 
    		  	BETWEEN
      			DATE(".$db->qstr(date('Y-m-d',$from_date)).")
      			AND
      			DATE(".$db->qstr(date('Y-m-d',$to_date))."))";

$query = "SELECT 
            DATE(seimf.create_dt) AS create_id,
            fn_get_person_name (ce.pid) AS patient_name,
            seimf.encounter_nr AS caseno,
            IF(ce.encounter_type='5',
                  (SELECT
                    rduTransaction.transaction_date
                  FROM
                    seg_dialysis_request AS rduRequest
                    INNER JOIN seg_dialysis_prebill AS rduPreBill
                    ON rduRequest.encounter_nr = rduPreBill.encounter_nr
                    INNER JOIN seg_dialysis_transaction AS rduTransaction
                    ON rduPreBill.bill_nr = rduTransaction.transaction_nr
                    WHERE rduRequest.encounter_nr = ce.encounter_nr 
                    ORDER BY rduTransaction.transaction_date
                    LIMIT 1),IF(ce.admission_dt IS NOT NULL,ce.admission_dt,ce.encounter_date)) AS admission_date,
            seimf.`create_id` AS biller,
            seimf.`insurance_nr` as insurance
            FROM
            seg_encounter_insurance_memberinfo_first seimf 
            LEFT JOIN seg_encounter_insurance_memberinfo seim
              ON seimf.`encounter_nr`=seim.`encounter_nr`
              AND seimf.`pid`=seim.`pid`
            LEFT JOIN care_encounter ce 
              ON seimf.encounter_nr = ce.encounter_nr 
            WHERE ". $cond1 . $cond2."
              AND seimf.insurance_nr=seim.insurance_nr ".$cond_categories."
              ORDER BY fn_get_person_name (ce.pid)";
// var_dump($query);exit();
              // var_dump($query);die();
# Modified by: JEFF @ 08-02-17
# Purpose: Enhancement / Update
$rs = $db->Execute($query);
if($rs){
    if($rs->RecordCount() > 0){
        $counter = 0;
        $i = 0;
        while($row = $rs->FetchRow()){
          $counter = ++$counter;
                $data[$i] = array(
                    'count' => $counter,
                    'patient_name' => utf8_decode(trim($row['patient_name'])),
                    'caseno' => $row['caseno'],
                );
				$i++;
        }
    }
    else{
        $data = array(
            array(
                'count' => 'No Data',
            )
        );
    }
}
else{
    $data = array(
        array(
            'count' => 'No Data',
        )
    );
}