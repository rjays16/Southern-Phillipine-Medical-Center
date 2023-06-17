<?php
#created by KENTOOT 08/02/2014
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require_once('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');

	include('parameters.php');

  $current_dt = date("Y-m-d");
  $current_time = date("h:i:s A");

  $date_based = 'sr.request_date';

  #TITLE of the report
  $params->put("hosp_country",$hosp_country);
  $params->put("hosp_agency", $hosp_agency);
  $params->put("hosp_name", mb_strtoupper($hosp_name));
  $params->put("hosp_addr1", $hosp_addr1);

  #_____________________________________________________
  $params->put("current_dt", date("F d, Y", strtotime($current_dt)));
  $params->put("current_time", $current_time);
  $params->put("datefrom", date("F d, Y", strtotime($from_date_format)));
  $params->put("dateto",  date("F d, Y", strtotime($to_date_format)));
  #_____________________________________________________

  $sql = "SELECT rd.service_code, ls.name, COUNT(rd.service_code) AS no_of_request 
          FROM seg_radio_serv AS sr 
            INNER JOIN care_test_request_radio AS rd  ON rd.refno = sr.refno 
            INNER JOIN seg_radio_services AS ls ON ls.service_code = rd.service_code 
            INNER JOIN seg_radio_service_groups as g ON g.group_code = ls.group_code
          WHERE sr.status NOT IN ( 'deleted', 'hidden', 'inactive', 'void' )
            AND rd.status NOT IN ( 'deleted', 'hidden', 'inactive', 'void' ) 
            AND g.fromdept ='RD'
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)."  AND ".$db->qstr($to_date_format)." 
          GROUP BY rd.service_code
          ORDER BY rd.service_code";

	$result=$db->Execute($sql);
  $data = array();
  $i = 0;

  if(is_object($result)){
    while ($row = $result->FetchRow()) {
  	$data[$i] = array('service_code' => $row['service_code'],
                        'name' => utf8_decode(trim(strtoupper($row['name']))), 
                        'no_of_request'  =>  $row['no_of_request']
                      );
         $i++;
     }
 }else{
 	$data[0]['service_code'] = NULL;
 }
?>