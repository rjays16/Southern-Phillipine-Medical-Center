<?php
/*Created by Mark 08-11-2016*/
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    include('parameters.php');

    global $db;
    
    $session = $_SESSION['sess_login_personell_nr'];
    $strSQL = "select permission,login_id from care_users WHERE personell_nr=".$db->qstr($session);

    $permission = array();
    $login_id = "";
    if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            while ($row = $result->FetchRow()){
              $permission[] = $row['permission'];
              $login_id = $row['login_id'];
            }
        }
    }


    
    require_once($root_path . 'include/care_api_classes/class_acl.php');
    require_once($root_path.'include/care_api_classes/class_department.php');
    $objAcl = new Acl($login_id);
    $dept_obj = new Department;
    $admin_access = $objAcl->checkPermissionRaw('_a_0_all');
    $assgin_dept_only = $objAcl->checkPermissionRaw('_a_3_sassgin_dept_only');

    $params->put("hospital_name",  mb_strtoupper($hosp_name));
  
    $dateFrom = explode("-", $from_date_format);
    $dateFromFormat = $dateFrom[1]."/".$dateFrom[2]."/".$dateFrom[0];

    $dateTo = explode("-", $to_date_format);
    $dateToFormat = $dateTo[1]."/".$dateTo[2]."/".$dateFrom[0];

    $params->put("date_span","From: ".date("M-d-Y", strtotime($dateFromFormat))." to ".date("M-d-Y", strtotime($dateToFormat)));
    $deptWhere ="";
    $effectivity = "";
    $counr_all = "";
    $with_department = false;
    $GET_DEPARTMENT = array();

    $cond1 = "DATE(ce.encounter_date)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";  

   if($_GET['param']!=""){
    $paramexplode = explode(",", $_GET['param']);
      if(count($paramexplode) == 1){
          foreach ($paramexplode as $key) {
            $explodedvalue = explode("--", $key);
            if($explodedvalue[0] == "param_alpha"){
              if($explodedvalue[1] == 'ascending'){
                $orderby = "ORDER BY cp.name_last ASC";
              }else{
                $orderby = "ORDER BY cp.name_last DESC";
              }
              
            }

           
              if($explodedvalue[0] == "param_OPDdeptt"){
                $with_department = true;
                $GET_DEPARTMENT[] = $explodedvalue[1];

                // if($dept_obj->searchParentDept($explodedvalue[1])){
                //     $deptWhere ="AND (ce.consulting_dept_nr IN ('";
                //     $deptList = $explodedvalue[1]."',";
                
                //       $deptChildList = $dept_obj->getChildDeptList($explodedvalue[1]);
                //       if($deptChildList){
                //         while ($cdeptRes = $deptChildList->FetchRow()) {
                //           $deptList .= $db->qstr($cdeptRes["id"]).",";
                
                //         }

                //       }
                        
                //     $deptWhere .= rtrim($deptList,"',") . "'))";
                // }else{
                  $deptWhere ="AND (ce.consulting_dept_nr IN ('".$explodedvalue[1]."'))";
                // }
                
              }

            #end
          }
      }else if (count($paramexplode) == 2) {
          foreach ($paramexplode as $key) {
            $explodedvalue = explode("--", $key);
            if($explodedvalue[0] == "param_alpha"){
              if($explodedvalue[1] == 'ascending'){
                $orderby = "ORDER BY cp.name_last ASC";
              }else{
                $orderby = "ORDER BY cp.name_last DESC";
              }
              
            }

            
              if($explodedvalue[0] == "param_OPDdeptt"){
                 $with_department = true;
                 $GET_DEPARTMENT[] = $explodedvalue[1];
                 // if($dept_obj->searchParentDept($explodedvalue[1])){
                 //    $deptWhere ="AND (ce.consulting_dept_nr IN ('";
                 //    $deptList = $explodedvalue[1]."',";
                
                 //      $deptChildList = $dept_obj->getChildDeptList($explodedvalue[1]);
                 //      if($deptChildList){
                 //        while ($cdeptRes = $deptChildList->FetchRow()) {
                 //          $deptList .= $db->qstr($cdeptRes["id"]).",";
                
                 //        }

                 //      }
                        
                 //    $deptWhere .= rtrim($deptList,"',") . "'))";
                 //  }else{
                    $deptWhere ="AND (ce.consulting_dept_nr IN ('".$explodedvalue[1]."'))";
                  // }            
              }
            

        
          } #end
      }else{
          $explodedvalue = explode("--", $paramexplode[0]);
          $explodedvalue1 = explode("--", $paramexplode[1]);
          $explodedvalue2 = explode("--", $paramexplode[2]);
          #end
          if($explodedvalue[0] == "param_alpha"){
                if($explodedvalue[1] == 'ascending'){
                  $orderby = "ORDER BY cp.name_last ASC";
                }else{
                  $orderby = "ORDER BY cp.name_last DESC";
                }
                
              }

          
            if($explodedvalue2[0] == "param_OPDdeptt"){
              $with_department = true;
               $GET_DEPARTMENT[] = $explodedvalue[1];
               // if($dept_obj->searchParentDept($explodedvalue[1])){
               //      $deptWhere ="AND (ce.consulting_dept_nr IN ('";
               //      $deptList = $explodedvalue[1]."',";
                
               //        $deptChildList = $dept_obj->getChildDeptList($explodedvalue[1]);
               //        if($deptChildList){
               //          while ($cdeptRes = $deptChildList->FetchRow()) {
               //            $deptList .= $db->qstr($cdeptRes["id"]).",";
                
               //          }

               //        }
                        
               //      $deptWhere .= rtrim($deptList,"',") . "'))";
               //  }else{
                  $deptWhere ="AND (ce.consulting_dept_nr IN ('".$explodedvalue[1]."'))";
                // }
                                          
            }
              
        }
    }

    if(!$with_department){
      if($assgin_dept_only && !$admin_access){

        $u_sql = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=" . $db->qstr($session);
     
        $res = $db->Execute($u_sql);
        $deptList = "";
        $deptWhere="";
        if($res->RecordCount() > 0){

          while ($user_nr = $res->FetchRow()) {

            if($dept_obj->searchParentDept($user_nr["location_nr"])){
                $deptList .= $user_nr['location_nr'] . "',";
                $deptChildList = $dept_obj->getChildDeptList($user_nr["location_nr"]);
                if($deptChildList){
                  while ($cdeptRes = $deptChildList->FetchRow()) {
                    $deptList .= $db->qstr($cdeptRes["id"]) . ",";
                    
                  }
                }
               
                
            }else{
              $deptList .= $user_nr['location_nr'] . ",";
            } 
                
          }
          $deptWhere ="AND (ce.consulting_dept_nr IN ('".rtrim($deptList,"',")."'))";
      }else{
          $deptWhere ="";
      }
        
    } 
      
  }
    
    $data = array();
    $from_date_format .= " 00:00:01";
    $to_date_format .= " 23:59:59";


    /*department*/
    $deptQUER = "SELECT nr AS id,
              name_formal AS namedesc 
            FROM care_department cd 
            WHERE cd.is_inactive = 0 AND TYPE = 1 
              HAVING  id =".$db->qstr($GET_DEPARTMENT[0]);
   $dept_qeury = $db->Execute($deptQUER);
      $department_row = $dept_qeury->FetchRow(); 
    if ($department_row['namedesc'] !="") {
        $GET_DEPARTMENT[] = $department_row['namedesc'];
    }
     /*END*/
    $sql = "SELECT 
          ce.pid AS pid,
            CONCAT(
              (SELECT 
                IF(COUNT(pid) = 1, 'NEW', 'OLD') 
              FROM
                care_encounter 
              WHERE pid = ce.`pid`)
            ) AS patient_status,
            `fn_get_person_lastname_first` (ce.pid) AS patient_name,
            DATE_FORMAT(
              ce.encounter_date,
              '%M %d, %Y %I:%i %p'
            ) AS Datetime_consultation,
            fn_get_age (
              ce.`encounter_date`,
              cp.date_birth
            ) AS age,
            (
              CASE
                WHEN (
                  ce.`official_receipt_nr` REGEXP '^-?[0-9]+$'
                ) 
                THEN
                  CASE
                    WHEN(
                      (SELECT sot.`or_desc`
                        FROM `seg_opd_or_temp` AS sot
                          WHERE sot.`or_id` = ce.`official_receipt_nr`) IS NOT NULL
                      )
                    THEN (
                      (SELECT sot.`or_desc`
                        FROM `seg_opd_or_temp` AS sot
                          WHERE sot.`or_id` = ce.`official_receipt_nr`
                      )
                    )
                    ELSE(
                      (SELECT SUM(payr.`amount_due`) AS new_amount
                          FROM `seg_pay_request` AS payr 
                        WHERE payr.`or_no` =or_pay.`or_no` AND (
                            payr.`service_code` IN 
                          (SELECT consoce.`consultation_code` FROM
                           `seg_services_consultion_code` AS consoce) 
                        ) GROUP BY payr.`or_no` LIMIT 1
                      ) 
                    )
                  END
                ELSE ce.`official_receipt_nr` 
              END
            ) AS AMOUNT_PAID_2,
            IF(cp.sex = 'f', 'F', 'M') AS Gender,
            ce.`official_receipt_nr` AS AMOUNT_PAID,
            `fn_get_department_name` (ce.`current_dept_nr`) AS dept,
            ce.`encounter_nr`,
            ce.`er_opd_diagnosis` AS Diagnoses,
           /* (SELECT GROUP_CONCAT(fn_get_personell_name (consulting_dr_nr), ' ') FROM `care_encounter` WHERE encounter_nr = ce.`encounter_nr`) AS Physician   */        
              (SELECT GROUP_CONCAT(fn_get_personell_name (consulting_dr_nr), ' ') FROM `care_encounter` WHERE encounter_nr = ce.`encounter_nr`) AS Physician              
          FROM
            care_encounter `ce` 
            INNER JOIN care_person `cp` 
              ON ce.pid = cp.pid
            LEFT JOIN `seg_pay` AS or_pay
            ON ce.`official_receipt_nr` = or_pay.`or_no`
        WHERE  
        ".$cond1."
          AND ce.`encounter_status` NOT IN ('deleted', 'void', 'cancelled', 'hidden') 
          AND ce.`encounter_type` IN ('2') ".$deptWhere." ".$orderby;

    $result = $db->GetAll($sql);
    $countRecords = 0;
    $index = 0;
    $count_male_female = array();
    $count_old_new = array();
    $total_income = array();
    $counts_gender_all = array_count_values($count_male_female);
    if($result){
      
      $countRecords = count($result);
      foreach ($result as $key) {
    
      //place data
        $count_male_female[] =$key['Gender'];
        $count_old_new[] =$key['patient_status'];
        $total_income[] = (is_numeric($key['AMOUNT_PAID_2'])) ? $key['AMOUNT_PAID_2']:0;
        $data[$index]['pid'] = $key['pid'];
        $data[$index]['fullname'] = utf8_decode(trim($key['patient_name']));
        $data[$index]['datetime'] = date('h:i A' ,strtotime($key['Datetime_consultation']));
        $data[$index]['age'] = $key['age'];
        $data[$index]['gender'] = $key['Gender'];
        $data[$index]['stats'] = $key['patient_status'];
        $data[$index]['amount_paid'] = (is_numeric($key['AMOUNT_PAID_2'])) ?"<font style='color:#FF0000;'>&#x20B1; ".number_format($key['AMOUNT_PAID_2'],2)."</font>":$key['AMOUNT_PAID_2'] ;
        $data[$index]['physician'] = utf8_decode(trim($key['Physician']))=="" ?" " : utf8_decode(trim($key['Physician']));
        $data[$index]['department'] = $key['dept'];
        $data[$index]['diagnosis'] = $key['Diagnoses']=="" ?" " : $key['Diagnoses'];

        $data[$index]['num_field'] = $index + 1;
        $index++;
        $counr_all = $index;

      }     
    }else{
      $data[0]['patient_id'] = "No Record";
      $data[0]['fullname'] = $cond2;
    }
    $params->put("all_Data_now",$counr_all == "" ? 0 :$counr_all);
    $params->put("effectivity", $effectivity);
    $params->put("revision", $revision);
    $params->put("total_income_day","<font style='color:#FF0000;'>&#x20B1; ".number_format(array_sum($total_income),2)."</font>");
    $params->put("total_male",count(array_keys($count_male_female, "M")));
    $params->put("total_female",count(array_keys($count_male_female, "F")));
    $params->put("total_new",count(array_keys($count_old_new, "NEW")));
    $params->put("total_old",count(array_keys($count_old_new, "OLD")));
    $params->put("recordsfound", "Number of Records : ".$countRecords);
    $params->put("department", (empty($GET_DEPARTMENT[1])) ? "ALL/Department" : $GET_DEPARTMENT[1]. "/Department");

?>