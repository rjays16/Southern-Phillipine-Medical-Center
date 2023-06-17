<?php
#created by KENTOOT 08/02/2014
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require_once('./roots.php');
  require_once($root_path.'include/inc_environment_global.php');
  require_once($root_path.'include/care_api_classes/class_department.php');
  $dept_obj=new Department;
  require_once($root_path.'include/care_api_classes/class_ward.php');
  $ward_obj=new Ward;
  require_once($root_path.'include/care_api_classes/class_personell.php');
  $pers_obj=new Personell;

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
                  rs.create_dt, d.request_flag,
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


          $query3 = "SELECT 
                       SUM(price_cash) AS price_cash,
                       SUM(price_cash_orig) AS price_cash_orig,
                       SUM(price_charge) AS price_charge
                     FROM care_test_request_radio
                     WHERE refno = '".$refno."'
                     AND status NOT IN ('deleted','hidden','inactive','void')";

            $rs3 = $db->Execute($query3);
              if(is_object($rs3)){

              while ($price = $rs3->FetchRow()) {

                      if ($row['is_cash'])
                        $total_amount = $price['price_cash'];
                      else  
                        $total_amount = $price['price_charge'];

                      $all_total_amount += $total_amount;
             }

           $query4 = "SELECT r.ref_no,r.ref_source,
                        SUM(CASE WHEN r.amount_due then r.amount_due else 0.00 end) AS amount_paid, p.*
                      FROM seg_pay_request AS r
                        INNER JOIN seg_pay AS p ON r.or_no=p.or_no
                      WHERE ref_no='".$refno."' AND ref_source='RD' AND pid='".$pid."'
                      ORDER BY p.or_no";

            $rs4 = $db->Execute($query4);
              if(is_object($rs4)){
              while ($row4 = $rs4->FetchRow()) {

                $paid = $row3['amount_paid'];

                $total_paid = $total_paid + $row4['amount_paid'];
                     
                $amount_bal = $total_amount - $paid;
                
                $total_amount_bal = $all_total_amount - $total_paid;
            }
          $query2 = "SELECT sd.*,ss.name,sg.group_code,sg.name AS groupnm,ss.is_socialized,
                      dept.name_formal,dept.name_short AS dept_name,
                      rs.doctor_in_charge,rs.findings_date,
                      TRIM(SUBSTRING(rs.findings_date,LOCATE('', rs.findings_date) + 1,10)) AS findings_date 
                    FROM seg_radio_serv AS s 
                      INNER JOIN care_test_request_radio AS sd  ON s.refno = sd.refno 
                      INNER JOIN seg_radio_services AS ss  ON sd.service_code = ss.service_code 
                      INNER JOIN seg_radio_service_groups AS sg  ON ss.group_code = sg.group_code 
                      INNER JOIN care_department AS dept  ON dept.nr = sg.department_nr 
                      LEFT JOIN care_test_findings_radio AS rs  ON rs.batch_nr = sd.batch_nr 
                    WHERE s.refno = '".$refno."'
                      AND s.status NOT IN ('deleted','hidden','inactive','void')
                    GROUP BY s.refno  
                    ORDER BY ss.name,sg.name 
                    ";
            
            $rs2 = $db->Execute($query2);
            if(is_object($rs2)){
              while ($row2 = $rs2->FetchRow()) {
                      $service = $row2['name'];
                      $dept = $row2['dept_name'];

                    if ($row3['is_socialized']) 
                      $socialized = 'YES';
                    else 
                      $socialized = 'NO';

                    if ($row['is_cash']) 
                      $gross_amount = $row2['price_cash_orig'];
                    else 
                      $gross_amount = $row2['price_charge'];

                    $discounted_amount = $row2['price_cash'];
                    // $all_total_amount = $all_total_amount + $gross_amount; 

                    $doctors_array = unserialize($row['doctor_in_charge']);
                    $doctors_final = $doctors_array[count($doctors_array)-1];
                    if(!is_array($doctors_final) && $doctors_final != ''){
                      if (stristr($doctors_final," / ")){
                        $doctor_array = explode(" / ",$doctors_final);
                        $doctor = '';
                        for ($j=0;$j<sizeof($doctor_array);$j++){
                          if (stristr($doctor_array[$j],","))
                            $pos = stripos($doctor_array[$j],",");
                          else
                            $pos = stripos($doctor_array[$j],"MD");

                          $dr_list = substr($doctor_array[$j],0,$pos);
                          $doctor .= trim($dr_list).",";
                        }

                        $dr = trim($doctor);
                        $doctors = substr($dr,0,strlen($dr)-1);

                      }else{
                        $pos = stripos($doctors_final,",");
                        $doctors = substr($doctors_final,0,$pos);
                      }
                    }else{
                      $docs =  $row['doc_nr'];
                      $doctor_final2 = '';
                      $nr = explode(',',$docs);
                      foreach($nr as $key => $value){
                          if($value!=''){
                              $row_pr=$pers_obj->get_Person_name($value);
                              $dr_name = mb_strtoupper($row_pr['dr_name']);        
                              $doctor_final2 .= $dr_name."\n";
                          }
                      } 
                      $doctors = $doctor_final2;
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
                          'total_amount_bal' => number_format($total_amount_bal,2),
                          'service'       => $service,
                          'dept'          => $dept,
                          'is_social'     => $socialized,
                          'gross_price'   => $gross_amount,
                          'discount_price'=> $discounted_amount,
                          'mode' => strtoupper($row['request_flag']),
                          'reader' => $doctors);
            $i++;
            }
          }
        }
      }
    }
  }else{
    $data[0]['patientID'] = NULL;
  } 
 $params->put("totalcount", strtoupper($i));  
?>