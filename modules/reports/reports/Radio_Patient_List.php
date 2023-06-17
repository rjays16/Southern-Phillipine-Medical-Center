<?php
#created by KENTOOT 07/25/2014
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');

    include('parameters.php');

    $datefrom = date("Y-m-d",$_GET['from_date']);
    $dateto = date("Y-m-d",$_GET['to_date']);
    
    $date = date("Y-m-d");
    $current_dt = mb_strtoupper(date("F d, Y",strtotime($date)));

    if($radio_time_from=='' && $radio_time_to==''){
      echo "Please specify time schedule."; exit;
    }elseif($radio_time_from > 13 || $radio_time_to > 13){
      echo "Time must be specified correctly."; exit;
    }


    #_____________________________________________________
    $params->put("date", strtoupper($datefrom));
    $params->put("user", strtoupper($_SESSION['sess_user_name']));
    $params->put("from_time", $radio_time_from);
    $params->put("to_time", $radio_time_to);
    #_____________________________________________________


    $query = "SELECT rd.clinical_info, r.rid, d.name_formal, rs.pid,rs.encounter_nr, rs.refno, rs.request_date, rs.request_time, rs.is_cash,
              rd.batch_nr AS film_no, rd.service_code,rd.price_cash, s.name, p.name_last, p.name_first,
              p.name_middle, p.date_birth, p.sex,
              IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
              p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, spr.prov_name,
              sr.region_name, e.encounter_type,e.current_att_dr_nr,e.consulting_dr_nr,
              e.current_ward_nr, e.current_room_nr, e.current_dept_nr, rs.create_id,
              rs.modify_id, pay.or_no, pay.amount_due, gd.grant_no, s.price_cash AS orig_cash_price,
              s.price_charge AS orig_charge_price, rs.discountid,rd.request_doctor, IF(rd.clinical_info!='',rd.clinical_info,e.er_opd_diagnosis) AS adm_diagnosis,
              CONCAT( 'Dr. ',CAST(SUBSTRING((SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid),1,1) AS BINARY),
                    IF(
                       (SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
                     ),
                  SUBSTRING((SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid), 1, 1),
                    IF(
                       (SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
                     ),
                  (SELECT name_last FROM care_person AS p WHERE p.pid=pr.pid)) AS dr_name
              FROM seg_radio_serv AS rs
              INNER JOIN care_test_request_radio AS rd ON rd.refno = rs.refno
              INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code
              INNER JOIN care_person AS p ON p.pid=rs.pid
              INNER JOIN seg_radio_id AS r ON r.pid=rs.pid
              INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
              INNER JOIN care_department AS d ON d.nr=g.department_nr
              LEFT JOIN care_personell AS pr ON pr.nr=rd.request_doctor
              LEFT JOIN seg_pay AS sp ON sp.pid=rs.pid AND sp.encounter_nr=rs.encounter_nr
                        AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
              LEFT JOIN seg_pay_request AS pay ON pay.or_no=sp.or_no AND pay.ref_no=rs.refno
                        AND ref_source='RD' AND pay.service_code=rd.service_code
              LEFT JOIN seg_granted_request AS gd ON gd.ref_no=rs.refno AND gd.ref_source='RD'
                        AND gd.service_code=rd.service_code
              LEFT JOIN care_encounter AS e ON e.encounter_nr=rs.encounter_nr
              LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
              LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
              LEFT JOIN seg_provinces AS spr ON spr.prov_nr=sm.prov_nr
              LEFT JOIN seg_regions AS sr ON sr.region_nr=spr.region_nr

              WHERE rs.request_date LIKE ".$db->qstr($datefrom)."
              AND rs.request_time >= ".$db->qstr($radio_time_from)."' AND rs.request_time <= ".$db->qstr($radio_time_to)."
              AND (pay.or_no!='' OR gd.grant_no!='' OR pay.or_no IS NOT NULL OR gd.grant_no IS NOT NULL OR is_cash=0)
              GROUP BY rs.pid,rd.service_code";
     
  
    $rs = $db->Execute($query);
    $data = array();
    $i = 0;
    $no_req = 0;
    $no_pat = 0;

    if($rs){
       while ($row = $rs->FetchRow()) {

          $patient = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
          
          $data[$i] = array('pid'       => $pid, 
                        'name_formal'  =>  $row['name_formal'],
                        'or_no'     => $or_no,
                        'amount'    => number_format($amount,2),
                        'rid'       => $classify,
                        'refno'     => $refno,
                        'time'      => $time,
                        'patient'   => $patient,
                        'address'   => utf8_decode(trim($address)),
                        'age'       => $age,
                        'sex'       => $sex,
                        'bdate'     => $bdate,
                        'diagnosis' => $diagnosis,
                        'dr_name'   => utf8_decode(trim(mb_strtoupper($row["dr_name"]))),
                        'loc_name'  => $loc_name,
                        'name'      => $row["name"]);
    
         $i++;
         $no_pat += $i;
         $no_req += $i;
        }
    }else{
      $data[0]['pid'] = "No results found for this report.";
      $no_pat = '0';
      $no_req = '0';
  }
  $params->put("no_pat", "0");
  $params->put("no_req", "0");
?>  
