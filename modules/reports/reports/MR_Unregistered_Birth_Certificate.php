<?php
/*
Created by Borj, 12/06/2014 06:00 AM
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

include_once($root_path . 'include/care_api_classes/class_personell.php');
$pers_obj = new Personell;

     $sig_info = $pers_obj->get_Signatory('birthcert');
     $name_officer = mb_strtoupper($sig_info['name']);
     $officer_position = $sig_info['signatory_position'];
     $officer_title = $sig_info['signatory_title'];

     $sig_info_1 = $pers_obj->get_Signatory('medcert');
     $name_officer = mb_strtoupper($sig_info_1['name']);
     $officer_position = $sig_info_1['signatory_position'];
     $officer_title = $sig_info_1['signatory_title'];


#_________________________________________________
// $params->put('hosp_name',mb_strtoupper($hosp_name));
// $from = date("F j, Y", strtotime($from_date_format) );
// $to = date("F j, Y", strtotime($to_date_format) );
// $params->put('date_span',mb_strtoupper($from) . ' to ' . mb_strtoupper($to));
$params->put("hosp_country", $hosp_country);
$params->put("hosp_agency", $hosp_agency);
$params->put("hosp_name", $hosp_name);
$params->put("hosp_addr1", $hosp_addr1);

$params->put('prep_1',mb_strtoupper($sig_info['name']));
$params->put('prep_2',$sig_info['signatory_position']);

$params->put('not_1',mb_strtoupper($sig_info_1['name']).mb_strtoupper($sig_info_1['title']));
$params->put('not_2',$sig_info_1['signatory_title']);


  

#_________________________________________________
global $db;

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

                  $sql="SELECT
                          `fn_get_person_name` (ce.pid) AS name_of_baby,
                          DATE_FORMAT(cp.`date_birth`, '%m/%d/%Y') AS date_of_birth,
                          `fn_get_mother_name` (ce.`pid`) AS mothers_name,
                          `fn_get_father_name` (ce.`pid`) AS fathers_name 
                        FROM
                          care_encounter ce 
                          INNER JOIN care_person cp 
                            ON cp.`pid` = ce.`pid` 
                        WHERE ce.`encounter_type` = '12'
                          AND NOT ce.`pid` IN (SELECT pid FROM seg_cert_birth)
                          AND  DATE(cp.date_birth) BETWEEN (".$db->qstr($from).") AND (".$db->qstr($to).")
                          ORDER BY date_of_birth"; 
                          
                          #echo $sql;exit();

                  $i = 0;
                  $data = array();
                  $rs = $db->Execute($sql);

                                  if($rs){
                                        if($rs->RecordCount()){
                                              while($row=$rs->FetchRow()){
                                                    $data[$i] = array(
                                                          'nob' => strtoupper($row['name_of_baby']),
                                                          'dob' => $row['date_of_birth'],
                                                          'mn' => strtoupper($row['mothers_name']),
                                                          'fn' => strtoupper($row['fathers_name'])
                                                         
                                                         );
                                                    $i++;
                                              }
                                              
                                        }else{
                                              $data[0]= array('name_of_baby'=>'No Data');
                                        }
                                        }else{
                                        $data[0]['name_of_baby'] = 'No records';
                                  }

                                  $baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$data[0]['image_02'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['image_01'] = $baseurl . "img/doh.png";