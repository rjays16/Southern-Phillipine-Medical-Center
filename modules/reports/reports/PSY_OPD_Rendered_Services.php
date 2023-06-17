<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", IPBM_HEADER);
    
    $patient_type = IPBM_OPD;               
    $sql = "SELECT DATE(e.encounter_date) as dates,d.name_formal AS Type_Of_Service  FROM  care_encounter as e LEFT JOIN care_department AS d 
              ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY DATE(e.encounter_date)
            ORDER BY DATE(e.encounter_date)";

    
    $rs = $db->Execute($sql);
    $rowindex = 0;
    $data = array();
    $patients = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $sql_get_no = "SELECT ce.pid FROM care_encounter as ce  WHERE ce.STATUS NOT IN ('deleted','hidden','inactive','void')  AND ce.encounter_type IN ($patient_type) AND DATE(ce.encounter_date) = ".$db->qstr($row['dates'])." GROUP by ce.pid";
             $result = $db->Execute($sql_get_no);

             if(is_object($rs)){
                while ($row_pid = $result->FetchRow()){
                
                $new_patient = 0;
                $old_patient = 0;

                 $query_latest_enc = "SELECT MIN(DATE(e.encounter_date)) AS latest_enc, MIN(e.encounter_date) AS latest_enc_w_t, COUNT(DATE(e.`encounter_date`)) AS count_enc FROM care_encounter as e WHERE e.STATUS  NOT IN ('deleted','hidden','inactive','void') AND e.pid=".$db->qstr($row_pid['pid'])." GROUP BY DATE(e.`encounter_date`)";
                $latest_enc = $db->GetRow($query_latest_enc);
                         // echo "<pre>";
                         // var_dump($query_latest_enc);
                         // echo "</pre>";
                 if($row['dates']==$latest_enc['latest_enc'] && (int)$latest_enc['count_enc'] >= 2 ){
                  $new_patient++;
                 }
                       $get_visit = "SELECT COUNT(c.encounter_nr) as no_of_enc FROM care_encounter as c WHERE c.STATUS  NOT IN ('deleted','hidden','inactive','void') AND c.encounter_date > ".$db->qstr($latest_enc['latest_enc_w_t'])." AND DATE(c.encounter_date) = DATE(".$db->qstr($row['dates']).") AND c.pid=".$db->qstr($row_pid['pid']);
                         $count_visit = $db->GetOne($get_visit);
                         // echo "<pre>";
                         // var_dump($get_visit);
                         // echo "</pre>";
                    // if(!in_array($row_pid['pid'], $patients)){
                    //     array_push($patients,$row_pid['pid']);
                            if($count_visit > 0){
                               $old_patient+=$count_visit;
                            }
                            else{
                               $new_patient++;  
                            }
                            $total_new += $new_patient;
                            $total_old += $old_patient ;
                    // }
                }     
             } 
          $total = (int) $total_new + (int) $total_old;
          $type_of_serv = $row['Type_Of_Service'];
        }  
        // exit;

        
        $sql_no_patient = "SELECT COUNT(per.pid)as total_patient FROM (SELECT e.pid FROM care_encounter e
                           WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void') 
                           AND e.encounter_type IN ($patient_type) 
                           AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." GROUP BY e.pid) as per";
        $no_patients = $db->GetOne($sql_no_patient);
        $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Type_Of_Service' => $type_of_serv, 
                          'new' => (int) $total_new ,
                          'revisit' => (int) $total_old,
                          'total' => (int) $total,
                          );
        #default value;
        $grn_total = $total;
         if(empty($total)){
          $total = 0;
          $grn_total = 1;
          }
        $grand_total = $grn_total;
        $rowindex++;
        $datetime1 = date_create($from_date_format); 
        $datetime2 = date_create($to_date_format);
        $interval = date_diff($datetime1, $datetime2); 
        $compute = round($total/($interval->days + 1));
          $params->put("no_weekdays", (int) $compute);
          $params->put("grand_total", (int) $grand_total);
          $params->put("no_patients", (int) $no_patients);
          
          
          
          
      }else{
        $data[0]['id'] = NULL; 
    } 
    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );
$params->put("spmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put("ipbm_logo", $baseurl . "img/ipbm.png");   
