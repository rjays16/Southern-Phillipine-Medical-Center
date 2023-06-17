<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_environment_global.php');
include 'parameters.php';
global $db;


if($dept == '182'){
	$enc_type = 14;
}else{
	$enc_type = 2;
}

 $tot_sql = "SELECT SUM(t.total) as total FROM (SELECT count(ed.code) AS total

                    FROM  care_encounter_diagnosis AS ed
                    INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
                    INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
                    INNER JOIN care_person AS p ON p.pid=e.pid
                    WHERE ed.encounter_type IN ($enc_type)
                    AND e.status NOT IN ('deleted','hidden','inactive','void')
                    AND ed.status NOT IN ('deleted','hidden','inactive','void')
                    AND ed.type_nr IN ($type_nr)
                    AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."    
                    GROUP BY ed.code
                    ORDER BY count(ed.code) DESC) as t";

 	if($tot_result=$db->Execute($tot_sql)){    
 		$over_alltotal  = $tot_result->FetchRow();         
	}
	
	$sql = "SELECT ed.code, c.description,

            SUM(CASE WHEN p.sex='m' AND (floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<1 OR floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age)) IS NULL) then 1 else 0 end) AS male_below1,

            SUM(CASE WHEN p.sex='f' AND (floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<1 OR floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age)) IS NULL) then 1 else 0 end) AS female_below1,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=1 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=4 then 1 else 0 end) AS male_1to4,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=1 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=4 then 1 else 0 end) AS female_1to4,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=5 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=9 then 1 else 0 end) AS male_5to9,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=5 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=9 then 1 else 0 end) AS female_5to9,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=10 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=14 then 1 else 0 end) AS male_10to14,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=10 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=14 then 1 else 0 end) AS female_10to14,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=15 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=19 then 1 else 0 end) AS male_15to19,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=15 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=19 then 1 else 0 end) AS female_15to19,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=20 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=44 then 1 else 0 end) AS male_20to44,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=20 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=44 then 1 else 0 end) AS female_20to44,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=45 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=59 then 1 else 0 end) AS male_45to59,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=45 
                AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))<=59 then 1 else 0 end) AS female_45to59,

            SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=60 then 1 else 0 end) AS male_60up,
            SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_ageyr(e.encounter_date,p.date_birth),age))>=60 then 1 else 0 end) AS female_60up,

            SUM(CASE WHEN p.sex='m' then 1 else 0 end) AS male_total,
            SUM(CASE WHEN p.sex='f' then 1 else 0 end) AS female_total,

            count(ed.code) AS total

            FROM  care_encounter_diagnosis AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
            INNER JOIN care_person AS p ON p.pid=e.pid
            WHERE e.encounter_type IN ($enc_type)
            AND e.status NOT IN ('deleted','hidden','inactive','void')
            AND ed.status NOT IN ('deleted','hidden','inactive','void')
            AND ed.type_nr IN ($type_nr)
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            GROUP BY ed.code
            ORDER BY count(ed.code) DESC";

			$result=$db->Execute($sql);
		    if ($result) {
			
		      $_count = $result->RecordCount();
			  $i=1;
              $percentage = 0; 
		      while ($row=$result->FetchRow()) {
        		
                  $t_male_below1 += $row['male_below1'];
                  $t_female_below1 += $row['female_below1']; 
                  
                  $t_male_1to4 += $row['male_1to4']; 
                  $t_female_1to4 += $row['female_1to4'];
                   
                  $t_male_5to9 += $row['male_5to9']; 
                  $t_female_5to9 += $row['female_5to9']; 
                  
                  $t_male_10to14 += $row['male_10to14']; 
                  $t_female_10to14 += $row['female_10to14']; 
                  
                  $t_male_15to19 += $row['male_15to19']; 
                  $t_female_15to19 += $row['female_15to19']; 
                  
                  $t_male_20to44 += $row['male_20to44']; 
                  $t_female_20to44 += $row['female_20to44']; 
                  
                  $t_male_45to59 += $row['male_45to59']; 
                  $t_female_45to59 += $row['female_45to59']; 
                  
                  $t_male_60up += $row['male_60up']; 
                  $t_female_60up += $row['female_60up']; 
                  
                  $t_male_total += $row['male_total']; 
                  $t_female_total += $row['female_total']; 
                  $t_total += $row['total'];
                  
                  $percentage = ($row['total'] / $over_alltotal['total']) * 100;
                  $percentage = round($percentage,2);

                  $t_percent += $percentage;
                  $t_percent = round($t_percent,2);

				$data[$i]=array(
                  'no' => $i,   
		          'diag' => $row['description'],
        		  'male_below1' => $row['male_below1'],
		          'female_below1' => $row['female_below1'],
        		  'male_1to4' => $row['male_1to4'],
		          'female_1to4' => $row['female_1to4'],
        		  'male_5to9' => $row['male_5to9'],
		          'female_5to9' => $row['female_5to9'],
        		  'male_10to14' => $row['male_10to14'],
		          'female_10to14' => $row['female_10to14'],
        		  'male_15to19' => $row['male_15to19'],
		          'female_15to19' => $row['female_15to19'],
        		  'male_20to44' => $row['male_20to44'],
        		  'female_20to44' => $row['female_20to44'],
		          'male_45to59' => $row['male_45to59'],
        		  'female_45to59' => $row['female_45to59'],
		          'male_60up' => $row['male_60up'],
				  'female_60up' => $row['female_60up'],
        		  'male_total' => $row['male_total'],
		          'female_total' => $row['female_total'],
				  'total' => $row['total'],
                  'percent' => $percentage."%",
                  'code' => $row['code'],
                  't_male_below1' => $t_male_below1,
                  't_female_below1' => $t_female_below1,
                  't_male_1to4' => $t_male_1to4,
                  't_female_1to4' => $t_female_1to4,
                  't_male_5to9' => $t_male_5to9,
                  't_female_5to9' => $t_female_5to9,
                  't_male_10to14' => $t_male_10to14,
                  't_female_10to14' => $t_female_10to14,
                  't_male_15to19' => $t_male_15to19,
                  't_female_15to19' => $t_female_15to19,
                  't_male_20to44' => $t_male_20to44,
                  't_female_20to44' => $t_female_20to44,
                  't_male_45to59' => $t_male_45to59,
                  't_female_45to59' => $t_female_45to59,
                  't_male_60up' => $t_male_60up,
                  't_female_60up' => $t_female_60up,
                  't_male_total' => $t_male_total,
                  't_female_total' => $t_female_total,
                  't_total' => $t_total,
                  't_percent' => $t_percent ."%",
                  'code1' => 'xxx'
				 );
				  $i++;
                  $percentage = 0;
      		}
          $baseurl = sprintf(
          "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
        );
      		$params = array(
			    'r_spmc' => $baseurl . "gui/img/logos/dmc_logo.jpg",
			    'l_spmc' => $baseurl . "img/ipbm.png",

          'hospital_country' => $hosp_country,
          'ipbm' => IPBM_HEADER,
          'hospital_name' => mb_strtoupper($hosp_name),
          'icd_class' => $icd_class,
          'date_span' => "FROM " . date('F d, Y',$from_date) . " T0 " . date('F d, Y',$to_date)
        );
        
    	}
    	else {
	      print_r($sql);
    	  print_r($db->ErrorMsg());
	      exit;
    	  # Error    
	    }

?>
