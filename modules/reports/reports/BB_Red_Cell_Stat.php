<?php
/*
Created by Borj, 09/25/2014 09:00 AM
Red Cell Product
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

#_________________________________________________
$params->put('hosp_name',mb_strtoupper($hosp_name));
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
$params->put("transactionc", $transactionc);
#_________________________________________________
global $db;

// added by: syboy 06/17/2015
$paramsnotexploded = $_GET['param'];
$explodedparams = explode(',', $paramsnotexploded);
// end

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
// var_dump($bb_based_datecc); die();
$sql = "SELECT
                fn_get_person_name(sls.pid) AS patientname,
                sls.pid AS hrn,
                sbrd.serial_no AS unitnumber,
                sbc.long_name AS component,
                sbrd.is_urgents AS defining, -- added by: syboy 06/18/2015
                DATE_FORMAT(sbrd.received_date, '%M, %d,%Y \n %h:%i %p') AS receiveddate, -- edited by: syboy 06/02/2015
                DATE_FORMAT(sbrs.done_date,  '%M %d,%Y \n %h:%i %p') AS donedate, -- edited by: syboy 06/02/2015
                TIMEDIFF(sbrs.done_date, sbrd.received_date) AS turnaround                
            FROM seg_lab_serv sls
            LEFT JOIN seg_blood_received_details AS sbrd ON sls.refno = sbrd.refno
            LEFT JOIN seg_blood_received_status AS sbrs ON sbrd.`refno`=sbrs.`refno`
            AND sbrd.`ordering` = sbrs.`ordering`
            AND sbrd.`service_code` = sbrs.`service_code`
            AND sls.`refno`=sbrs.`refno` 
            LEFT JOIN seg_blood_component AS sbc ON sbc.id = sbrd.component
            WHERE
            sbrs.done_date <> ''
            AND 
            sbc.component_group='redcell'
            AND
            $bb_based_datecc
            AND
            sls.ref_source='BB' 
            AND 
            sls.status NOT IN ('deleted','hidden','inactive','void')
            AND
            DATE($bb_based_datec) BETWEEN (".$db->qstr($from_date_format).") AND (".$db->qstr($to_date_format).")";

    $i = 0;
    $data = array();

    // $db->debug = true;
    $rs = $db->Execute($sql);
    // die();

    if($rs){
        if($rs->RecordCount()){
            while($row = $rs->FetchRow()){

                // added by: syboy 06/17/2015
                $extraction = explode("--", $explodedparams[0]);
                if ($extraction[1] == "routine") {

                    if ($row['turnaround'] > "05:00:00") {
                      $result = "<span style='color:red;'>{$row['turnaround']}</sanp>";
                    } else {
                      $result = "<span style='color:black;'>{$row['turnaround']}</sanp>";
                    }

                } elseif ($extraction[1] == "stat") {

                    $result = "<span style='color:black;'>{$row['turnaround']}</sanp>";

                } else {

                    if ($row['defining'] == 0) {

                        if ($row['turnaround'] > "05:00:00") {
                          $result = "<span style='color:red;'>{$row['turnaround']}</sanp>";
                        } else {
                          $result = "<span style='color:black;'>{$row['turnaround']}</sanp>";
                        }

                    } else {
                        $result = "<span style='color:black;'>{$row['turnaround']}</sanp>";
                    }

                }
                // end

                $data[$i] =  array(
                                  'patientname'  => utf8_decode(trim(strtoupper($row['patientname']))),
                                  'hrn'          => $row['hrn'],
                                  'unitnumber'   => $row['unitnumber'],
                                  'component'    => $row['component'],
                                  'receiveddate' => $row['receiveddate'],
                                  'donedate'     => $row['donedate'],
                                  'turnaround' => $result // edited by: syboy 06/17/2015
                                  );
               
               $i++;
            }  
        }else{
            $data[0] = array('patientname'=>'No Data');
        }
    }else{
        $data[0] = array('patientname'=>'No Data');
    }
    // $wew = strtotime("25:00:00");
    // var_dump($wew); die();

?>