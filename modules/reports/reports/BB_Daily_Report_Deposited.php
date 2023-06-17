<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    require_once($root_path.'include/care_api_classes/class_blood_bank.php');
    require_once($root_path.'modules/bloodBank/ajax/blood-waiver.server.php');
    require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');

    include('parameters.php');
    
    define(TIME_FROM,'12:00 AM');
    define(TIME_TO,'11:59 PM');

    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
    $params->put("transaction", $transaction);
    $params->put('generated_by', $_SESSION['sess_user_name']);
    

    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );

    $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
    $params->put("doh_logo", $baseurl . "img/doh.jpg");   
    
    $paramCondition = '';
    
    if(!empty($param_time_from) && !empty($param_time_to)){
     $param_time_froms= date("H:i", strtotime($param_time_from));
     $param_time_tos= date("H:i", strtotime($param_time_to));
    }else{
      $param_time_froms =date("H:i", strtotime(TIME_FROM));
      $param_time_tos = date("H:i", strtotime(TIME_TO));
      $param_time_to = TIME_TO;
      $param_time_from = TIME_FROM;
    }


    $params->put('time_to', $param_time_to);
    $params->put('time_from',$param_time_from);
  

    // if($from_date_format == $to_date_format){
      $paramCondition .= " sbwd.create_time BETWEEN ".$db->qstr($from_date_format." ".$param_time_froms)." AND ".$db->qstr($to_date_format." ".$param_time_tos);
    // }elseif($from_date_format < $to_date_format){
      // $from_date_format = $from_date_format.' '.TIME_RANGE;
      // $to_date_format = $to_date_format.' '.TIME_RANGE;
      // $paramCondition .= "sbwd.create_time BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format);
    // }

    if($blood_encoder){
      $paramCondition .= " AND (sbwd.`create_id` = fn_get_personell_firstname_last(".$db->qstr($blood_encoder).") OR sbwd.`create_id` = fn_get_personell_name(".$db->qstr($blood_encoder)."))";
    }

    $hasParameters = 0;
    if($bb_group || $bb_exp_date || $blood_component || $blood_source || $bb_donorunit)
      $hasParameters = 1;

    $sql = "SELECT 
              sbwd.`create_time`,
              fn_get_person_name (sbwd.pid) AS patient_name,
              sbwd.`pid`,
              sls.`encounter_nr`,
              sbwd.`batch_nr`,
              sbwd.`create_id`,  
              IF(sls.`discountid`='SC',1,0) as senior

            FROM
              seg_blood_waiver_details sbwd 
              LEFT JOIN care_person cp 
                ON sbwd.pid = cp.`pid`
              LEFT JOIN seg_lab_serv sls
                ON sbwd.`batch_nr` = sls.refno
            WHERE ".$paramCondition." ORDER BY create_time ASC";


// var_dump($sql);exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    $encounter_types = array("1"=>"ER PATIENT"/*ER*/,
                             "2"=>'OUTPATIENT'/*OPD*/, 
                             "3"=>'INPATIENT ER'/*Admitted through Emergency Room*/,
                             "4"=>'INPATIENT OPD',
                             "5"=>'DIALYSIS',
                             "6"=>'Health Service and Specialty Clinic');

    if (is_object($rs)){
        while($row=$rs->FetchRow()){
          $arr_component = array();
          $order=1;
          if($row['encounter_nr']){
            $seg_encounter = new Encounter();
            $encounter_details = $seg_encounter->getEncounterInfo($row['encounter_nr']);
            
            switch ($encounter_details['encounter_type']) {
              case 1:
                $key = $seg_encounter->getERlocationNew($encounter_details['er_location']);
                $key2 = $seg_encounter->getERlocationLobbyNew($encounter_details['er_location_lobby']);
                $ward_name = $key['area_location']."(".$key2['lobby_name'].")";
                break;
              case 2:
              case 14:
                $ward_name =$encounter_types[2];
                break;
              case 3:
              case 4:
              case 13:
                $ward_name = $encounter_details['ward_name'];
                break;
              case 6:
                $ward_name =$encounter_types[6];
                break;
            }

            $ward_name = $ward_name.' - '.$encounter_details['er_opd_admitting_dept_name'];
          }else{
            $ward_name = '';
          }



          $getWaiverDetails = fetchWaiverInformation($row['batch_nr'],$row['pid'],$row['encounter_nr']);
$serv_code='';
    
          foreach ($getWaiverDetails as $key) {
         $date_recieved='';
         $return_recieved='';
         $consumed_recieved='';
         $issuance_recieved='';
         $result_recieved='';
         $is_que = false; 
         $count ="" ;
         // $arr_services = array();
          array_push($arr_component,$key['component']);
          if(in_array($key['component'],$arr_component)){
            $count = array_count_values($arr_component);
          }
            $order = $count[$key['component']] - 1;

             // var_dump("<pre>");
             //  var_dump($order."+".$key['component']."+".$keys);
             //  var_dump("</pre>");
            // if($count[$key['component']]>1){
            //       $order = $count[$key['component']] - 1;
            // }else{
            //   $order = 0;
            // }
        
          // var_dump($order);exit();
                // var_dump("<pre>");
                // var_dump(print_r($arr_component)."+".$key['component']);
                // var_dump("</pre>");

            $senior_component = array('ALIQUOT','PRBC','WB','WB_PRBC');
            $retype_component = array('CRYO','FFP','PC');
             $seg_bloodbank = new SegBloodBank();
             // var_dump($key['component']);exit();
             if(in_array($key['component'],$senior_component)){
                  if($row['senior']){
                    $serv_code = 'XM_CON';
                  }else{
                     $serv_code = 'XM-BAG';
                  }
             }
            if(in_array($key['component'],$retype_component)){
                    $serv_code = 'RETPLS';
             }


            
             $crossmatch_data = $seg_bloodbank->fetchCrossMatched($row['batch_nr'],$key['component'],$serv_code);

              foreach ($crossmatch_data as $keys => $key_cross) {
                // var_dump("<pre>");
                // var_dump($key_cross);
                // var_dump("</pre>");
                // var_dump("<pre>");
                // var_dump($order."+".$key['component']."+".$keys);
                // var_dump("<pre>");
                // var_dump($key_cross);
                // array_push($arr_services,$key_cross['service_code']);
                // var_dump(count($key_cross));
              
                //   if($key_cross['is_senior']){

                //   }

                 if(!$key_cross['is_senior'] && $key_cross['service_code']=='XM-BAG'){
                          // $date_recieved  .= ."<br>";
                            $is_que = true;

                        } 
                        if($key_cross['is_senior'] && $key_cross['service_code']=='XM_CON'){
                            $is_que = true;
                        } 
                        if($key_cross['service_code']=='RETPLS'){
                            $is_que = true;
                        }

                //   var_dump("<pre>");
                // var_dump($keys."+".$order."+".$key['component']."+".$key_cross['service_code']);   
                if($keys == $order){
                    if($is_que){         
                             $result_recieved  = $key_cross['result'];
                            if(!empty($key_cross['started_date'])){
                               $date_recieved  =  date("m/d/Y h:i A", strtotime($key_cross['started_date']));
                            }
                            if(!empty($key_cross['issuance_date'])){
                               $issuance_recieved  =  date("m/d/Y h:i A", strtotime($key_cross['issuance_date']));
                            }
                            if(!empty($key_cross['date_return'])){
                                $return_recieved  =  date("m/d/Y h:i A", strtotime($key_cross['date_return']));
                            }
                            if(!empty($key_cross['date_consumed'])){
                               $consumed_recieved  =  date("m/d/Y h:i A", strtotime($key_cross['date_consumed']));
                            } 
                    }
                
                }
                $is_que = false;
               } 
               // die;

   
           
             
          
           
            $isIncluded = 0;
            if($hasParameters){
              $bb_group_exploded = explode(' ',trim($key['bloodgrp']));
              if($bb_group && (!$bb_exp_date && !$blood_component && !$blood_source && !$bb_donorunit)){
                // var_dump('has bb group');
                if($bb_group_exploded[0] == strtoupper($bb_group))
                    $isIncluded = 1;
              }elseif($bb_exp_date && (!$bb_group && !$blood_component && !$blood_source && !$bb_donorunit)){
                // var_dump('has expiry');
                if(date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date)
                  $isIncluded = 1;
              }elseif($blood_component && (!$bb_group && !$bb_exp_date && !$blood_source && !$bb_donorunit)){
                // var_dump('has component');
                if($key['component'] == strtoupper($blood_component))
                  $isIncluded = 1;
              }elseif($blood_source && (!$bb_group && !$bb_exp_date && !$blood_component && !$bb_donorunit)){
                // var_dump('has source');
                if($key['source'] == strtoupper($blood_source))
                  $isIncluded = 1;
              }elseif($bb_donorunit && (!$bb_group && !$bb_exp_date && !$blood_component && !$blood_source)){
                // var_dump('has donor unit');
                if($key['donorunit'] == $bb_donorunit)
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && (!$blood_component && !$blood_source && !$bb_donorunit)){
                // var_dump('has bb group and expiry');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date))
                  $isIncluded = 1;
              }elseif($bb_group && $blood_component && (!$bb_exp_date && !$blood_source && !$bb_donorunit)){
                // var_dump('has bb group and component');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['component'] == strtoupper($blood_component)))
                  $isIncluded = 1;
              }elseif($bb_group && $blood_source && (!$bb_exp_date && !$blood_component && !$bb_donorunit)){
                // var_dump('has bb group and source');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_donorunit && (!$bb_exp_date && !$blood_component && !$blood_source)){
                // var_dump('has bb group and donor unit');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_exp_date && $blood_component && (!$blood_source && !$bb_group && !$bb_donorunit)){
                // var_dump('has exp and component'); 
                if((date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)))
                  $isIncluded = 1;
              }elseif($bb_exp_date && $blood_source && (!$blood_component && !$bb_group && !$bb_donorunit)){
                // var_dump('has expiry and source');
                if((date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($bb_exp_date && $bb_donorunit && (!$blood_component && !$bb_group && !$blood_source)){
                // var_dump('has expiry and donor unit');
                if((date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($blood_component && $blood_source && (!$bb_exp_date && !$bb_group && !$bb_donorunit)){
                // var_dump('has component and source');
                if(($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($blood_component && $bb_donorunit && (!$bb_exp_date && !$bb_group && !$blood_source)){
                // var_dump('has component and donor unit');
                if(($key['component'] == strtoupper($blood_component)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($blood_source && $bb_donorunit && (!$bb_exp_date && !$bb_group && !$blood_component)){
                // var_dump('has source and donor unit');
                if(($key['source'] == strtoupper($blood_source)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && $blood_component && (!$blood_source && !$bb_donorunit)){
                // var_dump('has bb group, expiry and component');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)))
                  $isIncluded = 1;
              }elseif($bb_group && $blood_component && $blood_source && (!$bb_exp_date && !$bb_donorunit)){
                // var_dump('has bb group, component, and source');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && $blood_source && (!$blood_component && !$bb_donorunit)){
                // var_dump('has bb group, expiry, and source');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($bb_group && $blood_source && $bb_donorunit && (!$bb_exp_date && !$blood_component)){
                // var_dump('has bb group, source, and donor unit');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['source'] == strtoupper($blood_source)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && $bb_donorunit && (!$blood_source && !$blood_component)){
                // var_dump('has bb group, expiry, and donor unit');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_group && $blood_component && $bb_donorunit && (!$bb_exp_date && !$blood_component)){
                // var_dump('has bb group, component, and donor unit');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['component'] == strtoupper($blood_component)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_exp_date && $blood_component && $blood_source && (!$bb_group && !$bb_donorunit)){
                // var_dump('has expiry, component, and source');
                if((date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($bb_exp_date && $blood_component && $bb_donorunit && (!$bb_group && !$blood_source)){
                // var_dump('has expiry, component, and donor unit');
                if((date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($blood_component && $blood_source && $bb_donorunit && (!$bb_group && !$bb_exp_date)){
                // var_dump('has component, source, and donor unit');
                if(($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && $blood_component && $blood_source && (!$bb_donorunit)){
                // var_dump('has bb group, expiry, component and source');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && $blood_component && $bb_donorunit && (!$blood_source)){
                // var_dump('has bb group, expiry, component and donor unit');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_group && $blood_component && $blood_source && $bb_donorunit && (!$bb_exp_date)){
                // var_dump('has bb group, component, source and donor unit');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && ($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_exp_date && $blood_component && $blood_source && $bb_donorunit && (!$bb_group)){
                // var_dump('has expiry, component, source, and donor unit');
                if((date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }elseif($bb_group && $bb_exp_date && $blood_component && $blood_source && $bb_donorunit){
                // var_dump('has everything');
                if(($bb_group_exploded[0] == strtoupper($bb_group)) && (date("m/d/Y", strtotime($key['expiry'])) == $bb_exp_date) && ($key['component'] == strtoupper($blood_component)) && ($key['source'] == strtoupper($blood_source)) && ($key['donorunit'] == $bb_donorunit))
                  $isIncluded = 1;
              }
            }else{
              $isIncluded = 1;
            }
            
            if($isIncluded){
              $data[$rowindex]['count'] = $rowindex+1;

              $data[$rowindex]['date_time'] = date("m/d/Y h:i A", strtotime( $row['create_time']));
              $data[$rowindex]['patient_name'] = utf8_decode(trim($row['patient_name']));
              $data[$rowindex]['pid'] = $row['pid'];
              $data[$rowindex]['location'] = $ward_name;
              $data[$rowindex]['unitno'] = $key['unitno'];
              $data[$rowindex]['bloodgrp'] = $key['bloodgrp'];
              $data[$rowindex]['donorunit'] = $key['donorunit'];
              $data[$rowindex]['expiry'] = $key['expiry'];
              $data[$rowindex]['component'] = $key['component'];
              $data[$rowindex]['source'] = $key['source'];
              $data[$rowindex]['encoded_by'] = $row['create_id'];
              $data[$rowindex]['crossmatch'] = $date_recieved;
              $data[$rowindex]['compatibility_result'] = $result_recieved;
              $data[$rowindex]['tranfused'] = $issuance_recieved;
              $data[$rowindex]['return'] = $return_recieved;
              $data[$rowindex]['consume'] = $consumed_recieved;



              $rowindex++;
            }
          }
          // exit();
      
           
        }
            // exit();

        
    }else{
         $data[0]['date_time'] = NULL; 
    }  
