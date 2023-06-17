<?php
#created by KENTOOT 08/02/2014
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require_once('./roots.php');
  require_once($root_path.'include/inc_environment_global.php');
  require_once($root_path.'include/care_api_classes/class_department.php');
  $dept_obj=new Department;
  require_once($root_path.'include/care_api_classes/class_ward.php');
  $ward_obj=new Ward;

  include('parameters.php');

    $current_dt = date("Y-m-d");
    $current_time = date("h:i:s A");

    #TITLE of the report
    $params->put("hosp_country",$hosp_country);
    $params->put("hosp_agency", $hosp_agency);
    $params->put("hosp_name", mb_strtoupper($hosp_name));
    $params->put("hosp_addr1", $hosp_addr1);
    $params->put("header_type", $header_dtype);

    #______________________________________________________
    $params->put("current_dt", date("F j, Y", strtotime($current_dt)));
    $params->put("current_time", $current_time);
    $params->put("datefrom", date("F j, Y", strtotime($from_date_format)));
    $params->put("dateto",  date("F j, Y", strtotime($to_date_format)));
    $params->put("section", $r_section);
    $params->put("pattype", $pattype);
    $params->put("discountID", strtoupper($class));
    $params->put("user", strtoupper($_SESSION['sess_user_name']));
    #_____________________________________________________

    $query = "SELECT s.pid AS hrn,s.refno AS batchNo, s.ordername AS patientName,
                  CONCAT(dr.con_doctor_nr,',',dr.sen_doctor_nr,',',dr.jun_doctor_nr) AS doc_nr,
                  CONCAT(SUBSTRING(s.request_date, 1, 10),' ',s.`request_time`) AS order_date,
                  rs.create_dt,
                  s.discountid AS classify, 
                  ce.encounter_type, s.is_tpl, s.is_urgent, dept.id, cp.sex,
                  ce.current_dept_nr, ce.current_ward_nr,
                  ce.current_room_nr, g.department_nr
              FROM seg_radio_serv AS s 
                  LEFT JOIN care_test_request_radio AS d ON s.refno = d.refno 
                  LEFT JOIN seg_radio_services AS ss ON d.service_code = ss.service_code 
                  LEFT JOIN seg_radio_service_groups AS g ON g.group_code = ss.group_code 
                  INNER JOIN care_department AS dept ON dept.nr = g.department_nr 
                  INNER JOIN care_person AS cp ON cp.pid = s.pid 
                  LEFT JOIN care_encounter AS ce ON s.encounter_nr = ce.encounter_nr 
                  LEFT JOIN care_test_findings_radio AS rs ON rs.batch_nr = d.batch_nr 
                  LEFT JOIN care_test_findings_radio_doc_nr AS dr ON dr.batch_nr = d.batch_nr
              WHERE s.status NOT IN('deleted','hidden','inactive','void')
              AND d.status NOT IN('deleted','hidden','inactive','void')
              $flag
              $join_rep_cond
              $enc_type
              $group_cond
              $dep_cond
              $date_cond
              $doctor_cond
              $grp
              ORDER BY cp.name_last, cp.name_first, s.refno, g.name
              ";

  #echo $query; exit;            

  $rs=$db->Execute($query);
  $data = array();
  $i = 0;

      if(is_object($rs)){
         while ($row = $rs->FetchRow()) {

              if (!trim($row['classify']))
                $classify = "NONE";
              else
                $classify = $row['classify'];

              if ($row['encounter_type']==1){
              $patient_type = "ER Patient";
              $location = "ER";
              }elseif ($row['encounter_type']==2){
                $patient_type = "Outpatient";
                $dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
                $location = $dept['id'];
              }elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
                if ($row['encounter_type']==3)
                  $wer = "(ER)";
                elseif ($row['encounter_type']==4)
                  $wer = "(OPD)";

                $patient_type = "Inpatient ".$wer;
                
                $ward = $ward_obj->getWardInfo($row['current_ward_nr']);
                $location = $ward['ward_id']." : Rm.#".$row['current_room_nr'];
              }elseif($row['encounter_type']==6){
                $patient_type = "IC Patient";
                $location =  'Industrial Clinic';
              }else{
                $patient_type = "Walkin";
                $location =  '';
              }

              if ($row['is_tpl'])
                $paidstatus = 'TPL';
              elseif (!($row['is_cash']))
                $paidstatus = 'CHARGE';
              elseif (($row['is_cash']) && !($row['is_urgent']))
                $paidstatus = 'CASH';

              $refno = $row['batchNo'];
              $pid = $row['hrn'];
              
            $query2 = "SELECT 
                         SUM(price_cash) AS price_cash,
                         SUM(price_cash_orig) AS price_cash_orig,
                         SUM(price_charge) AS price_charge
                       FROM care_test_request_radio
                       WHERE refno = '".$refno."'
                       AND status NOT IN ('deleted','hidden','inactive','void')";

            
            $rs2 = $db->Execute($query2);
              if(is_object($rs2)){

              while ($price = $rs2->FetchRow()) {

                      if ($row['is_cash'])
                        $total_amount = $price['price_cash'];
                      else  
                        $total_amount = $price['price_charge'];

                      $all_total_amount += $total_amount;
             }

           $query3 = "SELECT r.ref_no,r.ref_source,
                        SUM(CASE WHEN r.amount_due then r.amount_due else 0.00 end) AS amount_paid, p.*
                      FROM seg_pay_request AS r
                        INNER JOIN seg_pay AS p ON r.or_no=p.or_no
                      WHERE ref_no='".$refno."' AND ref_source='RD' AND pid='".$pid."'
                      ORDER BY p.or_no";

            $rs3 = $db->Execute($query3);
              if(is_object($rs3)){
              while ($row3 = $rs3->FetchRow()) {

                $paid = $row3['amount_paid'];

                $total_paid = $total_paid + $row3['amount_paid'];
                     
                $amount_bal = $total_amount - $paid;
                
                $total_amount_bal = $all_total_amount - $total_paid;
               
             
             }         

            $data[$i] = array('i' => $i+1 . ".)",
                          'patientID'     => $row['hrn'], 
                          'refno'         =>  $row['batchNo'],
                          'ordername'     => utf8_decode(trim($row['patientName'])),
                          'request_date'  => $row['order_date'],
                          'classify'      => $classify,
                          'patient_type'  => $patient_type,
                          'paidstatus'    => $paidstatus,
                          'location'      => $location,
                          'gross_amount'  => number_format($total_amount,2),
                          'amount_paid'   => number_format($paid,2),
                          'amount_bal'    => number_format($amount_bal,2),
                          'all_total_amount' => number_format($all_total_amount, 2),
                          'total_paid'       => number_format($total_paid,2),
                          'total_amount_bal' => number_format($total_amount_bal,2)
                        );
         $i++;
        }
      }
    }
  }else{
    $data[0]['patientID'] = NULL;
  } 
 $params->put("totalcount", strtoupper($i));  
?>