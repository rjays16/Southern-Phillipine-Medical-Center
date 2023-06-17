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
  $params->put("hosp_country", $hosp_country);
  $params->put("hosp_agency", $hosp_agency);
  $params->put("hosp_name", mb_strtoupper($hosp_name));
  $params->put("hosp_addr1", $hosp_addr1);

  #_____________________________________________________
  $params->put("current_dt", date("F d, Y", strtotime($current_dt)));
  $params->put("current_time", $current_time);
  $params->put("datefrom", date("F d, Y", strtotime($from_date_format)));
  $params->put("dateto",  date("F d, Y", strtotime($to_date_format)));
  #_____________________________________________________

  $query = "SELECT ls.service_code,ls.name,
              s.size, rs.id_size, count(rs.id_size) AS no_of_film
            FROM seg_radio_service_sized AS rs
              INNER JOIN seg_radio_film_size AS s 
                ON s.id=rs.id_size
              INNER JOIN care_test_request_radio AS rd
                ON rd.batch_nr=rs.batch_nr
              INNER JOIN seg_radio_serv AS sr 
                ON sr.refno=rd.refno
              INNER JOIN seg_radio_services AS ls 
                ON ls.service_code=rd.service_code
            WHERE DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            AND sr.status NOT IN('deleted','hidden','inactive','void')
            AND rd.status NOT IN('deleted','hidden','inactive','void')
            GROUP BY rd.service_code, rs.id_size";

	$rs = $db->Execute($query);
  $data = array();
  $count = 0;
  
  if(is_object($rs)){
     while ($row = $rs->FetchRow()){
			$data[$count] = array('name'        => utf8_decode(trim($row['name'])),
                            'size'        => $row['size'], 
                            'no_of_film'  => $row['no_of_film']
                            );
           $count++;
       }
   }else{
   	$data[0]['name'] = NULL;
   }

	?>