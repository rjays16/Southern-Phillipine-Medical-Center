<?php 
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $sub_caption);
    global $db;

    $condition=" AND 
    DATE(IF(
      seim.create_id != seim.modify_id,
      seim.modify_dt,
      seim.create_dt))
               BETWEEN
                    DATE(".$db->qstr(date($from_date_format)).")
                    AND
                    DATE(".$db->qstr(date($to_date_format)).")
        ORDER BY sim.create_date ASC";
        // var_dump($HSM_biller);exit();
     if($HSM_biller!='all' && $HSM_biller!=''){
      $condition2 = " AND cu.personell_nr=".$db->qstr($HSM_biller);

    }
    else{
      $condition2="";
    }

    $sql = "SELECT DISTINCT
  ce.encounter_nr,
  fn_get_pid_lastfirstmi (cp.pid) AS name_of_patient,
  CONCAT(
    sim.member_lname,
    ',',
    sim.member_fname,
    ' ',
    sim.`member_mname`
  ) AS name_of_member,
  (SELECT
            DATE(rduTransaction.transaction_date)
          FROM seg_dialysis_prebill AS rduPreBill
            INNER JOIN seg_dialysis_transaction AS rduTransaction
            ON rduPreBill.bill_nr = rduTransaction.transaction_nr
          WHERE rduPreBill.encounter_nr = ce.encounter_nr
          ORDER BY rduTransaction.transaction_date
          LIMIT 1) AS ADMISSION_DATE2,
        (SELECT 
            DATE(rduTransaction.`datetime_out`)
          FROM
            seg_dialysis_prebill AS rduPreBill 
            INNER JOIN seg_dialysis_transaction AS rduTransaction 
              ON rduPreBill.bill_nr = rduTransaction.transaction_nr 
          WHERE rduPreBill.encounter_nr = ce.encounter_nr 
          AND rduPreBill.bill_type IN ('PH','NPH')  
          ORDER BY rduTransaction.datetime_out DESC 
          LIMIT 1 )AS DISCHARGE_DATE2,
  DATE(sim.birth_date) AS bdate,
  DATE(ce.encounter_date) AS admission_date,
  DATE(ce.discharge_date) AS discharged_date,
  cp.street_name,
  cu.name AS NAME 
FROM
  care_encounter AS ce 
  INNER JOIN seg_insurance_member_info AS sim 
    ON sim.pid = ce.pid 
  INNER JOIN care_person AS cp 
    ON cp.pid = ce.pid
  INNER JOIN seg_encounter_insurance_memberinfo AS seim 
  ON seim.encounter_nr = ce.encounter_nr
 INNER JOIN care_users AS cu 
    ON IF(
      seim.create_id != seim.modify_id,
      seim.modify_id,
      seim.create_id
    ) = cu.login_id 
  WHERE seim.member_type = 'HSM' AND sim.insurance_nr !='TEMP' 
  AND ce.encounter_status NOT IN('cancelled','deleted/cancelled')".$condition2.$condition;
     
    // var_dump($sql);exit();
         $rs = $db->Execute($sql);
         $rowindex = 0;
          if ($rs->RecordCount() > 0){ // edited by: syboy 07/11/2015

        while($row=$rs->FetchRow()){ 
             $data[$rowindex] =array('rowindex' => $rowindex+1,
                                   'case_no' => $row['encounter_nr'],
                                  'patient_name' => utf8_decode(trim(strtoupper($row['name_of_patient']))),
                                   'member_name' => utf8_decode(trim(strtoupper($row['name_of_member']))),
                                  'bdate'=>strtoupper(date("F d, Y",strtotime($row['bdate']))),
                                  'date_admitted'=> strtoupper(($row['ADMISSION_DATE2']?$row['ADMISSION_DATE2']:$row['admission_date'])== "" ? "" : date("F d, Y",strtotime(($row['ADMISSION_DATE2']?$row['ADMISSION_DATE2']:$row['admission_date'])))),
                                  'date_discharged' =>strtoupper(($row['DISCHARGE_DATE2']?$row['DISCHARGE_DATE2']:$row['discharged_date'])==0000-00-00?"":date("F d, Y",strtotime(($row['DISCHARGE_DATE2']?$row['DISCHARGE_DATE2']:$row['discharged_date'])))) ,
                                  'address'=> strtoupper($row['street_name'])== "" ? "" : strtoupper($row['street_name'])
                                  );
             $rowindex++;
             // var_dump($HSM_biller);exit();
              $params->put("biller",$HSM_biller == "all" || is_null($HSM_biller) ? "All" : $row['NAME']);
        }
      }
      // var_dump($sql);exit();

      $params->put("start_date",date('F d, Y', strtotime($from_date_format)));
    $params->put("end_date", date('F d, Y', strtotime($to_date_format)));

