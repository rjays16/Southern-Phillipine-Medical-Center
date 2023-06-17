<?php
/**
 * @author         : Syross P. Algabre
 * Date            : 12/07/2015 03:52 Pm
 * Description : Type of Service and Total Discharges According to Specialty
 */

#UPDATED BY VAS 08/25/2017

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

include('parameters.php');

define('INPATIENT','3,4');
define('WELLBABY','12');
define('NEWBORNDEPT','191,193'); #NICU and Pedia-Newborn Dept
$status = "'deleted','hidden','inactive'";

#TITLE of the report
$params->put("hospital_name", mb_strtoupper($hosp_name));
$params->put("header", $report_title);
$params->put("department", $area_type);

#=============REGULAR (NOT NEWBORN)
$sql_regular = "SELECT d.name_formal AS Type_Of_Service, d.nr,
        COUNT(e.encounter_nr) no_patient,
        SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay,

        ".$field_accommodation_type.",
        ".$field_discharge_disposition.",
        ".$field_discharge_result.",
        ".$field_death."
        
        ".$table_source_discharge_date."

        AND e.encounter_type IN (".INPATIENT.")
        AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) NOT IN (".NEWBORNDEPT.")
        GROUP BY d.name_formal
        ORDER BY d.name_formal";

$rs = $db->Execute($sql_regular);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){

            $discharged = (int) $row['discharged'] + (int) $row['no_disposition'];
            $total_discharges = $discharged + (int) $row['transferred'] + (int) $row['hama'] + (int) $row['absconded'];

            $total_deaths = (int) $row['deathbelow48'] + (int) $row['deathabove48'];

            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'type_service' => $row['Type_Of_Service'],
                              'no_patient' => (int) $row['no_patient'],
                              'total_length_day' => (int) $row['total_len_stay'],

                              'non_phic_pay' => (int) $row['non_phic_pay'],
                              'non_phic_service' => (int) $row['non_phic_service'],
                              'non_phic_total' => (int) $row['non_phic_total'],

                              'phic_pay' => (int) $row['phic_pay'],
                              'phic_service' => (int) $row['phic_service'],
                              'phic_total' => (int) $row['phic_total'],  
                              
                              'hmo' => (int) $row['hmo'],  
                              'owwa' => (int) $row['owwa'],

                              'discharged' => $discharged,
                              'transferred' => (int) $row['transferred'],
                              'hama' => (int) $row['hama'],
                              'absconded' => (int) $row['absconded'],
                              'total_discharges' => (int) $total_discharges,

                              'recovered_improved' => (int) $row['recovered_improved'],
                              'unimproved' => (int) $row['unimproved'],
                              
                              'deathbelow48' => (int) $row['deathbelow48'],
                              'deathabove48' => (int) $row['deathabove48'],
                              'total_deaths' => (int) $total_deaths,                                                             

                              );

            $rowindex++;

        }  
    }else{
        $data[0]['Type_Of_Service'] = NULL; 
    }  

#echo "<br>reg row = ".$rowindex;
#==========NEWBORN PATHOLOGIC (admitted)

$sql_patho = "SELECT d.name_formal AS Type_Of_Service, d.nr,
        COUNT(e.encounter_nr) no_patient,
        SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay,

        ".$field_accommodation_type.",
        ".$field_discharge_disposition.",
        ".$field_discharge_result.",
        ".$field_death."
        
        ".$table_source_discharge_date."

        #AND cp.fromtemp=1
        #AND cp.admitted_baby=1
        AND e.encounter_type IN (".INPATIENT.")
        AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (".NEWBORNDEPT.")
        ORDER BY d.name_formal";

$rs_patho = $db->Execute($sql_patho);    

if (is_object($rs_patho)){
        while($row_patho=$rs_patho->FetchRow()){

            $discharged_patho = (int) $row_patho['discharged'] + (int) $row_patho['no_disposition'];
            $total_discharges_patho = $discharged_patho + (int) $row_patho['transferred'] + (int) $row_patho['hama'] + (int) $row_patho['absconded'];

            $total_deaths_patho = (int) $row_patho['deathbelow48'] + (int) $row_patho['deathabove48'];

            $params->put("no_patient_patho", (int) $row_patho['no_patient']);
            $params->put("total_length_day_patho", (int) $row_patho['total_len_stay']);

            $params->put("non_phic_pay_patho", (int) $row_patho['non_phic_pay']);
            $params->put("non_phic_service_patho", (int) $row_patho['non_phic_service']);
            $params->put("non_phic_total_patho", (int) $row_patho['non_phic_total']);

            $params->put("phic_pay_patho", (int) $row_patho['phic_pay']);
            $params->put("phic_service_patho", (int) $row_patho['phic_service']);
            $params->put("phic_total_patho", (int) $row_patho['phic_total']); 
            
            $params->put("hmo_patho", (int) $row_patho['hmo']);  
            $params->put("owwa_patho", (int) $row_patho['owwa']);

            $params->put("discharged_patho", $discharged_patho);
            $params->put("transferred_patho", (int) $row_patho['transferred']);
            $params->put("hama_patho", (int) $row_patho['hama']);
            $params->put("absconded_patho", (int) $row_patho['absconded']);
            $params->put("total_discharges_patho", (int) $total_discharges_patho);

            $params->put("recovered_patho", (int) $row_patho['recovered_improved']);
            $params->put("unimproved_patho", (int) $row_patho['unimproved']);
            
            $params->put("deathbelow48_patho", (int) $row_patho['deathbelow48']);
            $params->put("deathabove48_patho", (int) $row_patho['deathabove48']);
            $params->put("total_deaths_patho", (int) $total_deaths_patho);                                                           
        }  
  }

  #=============== NEWBORN NON-PATHOLOGIC (wellbaby)

  $sql_nonpatho = "SELECT d.name_formal AS Type_Of_Service, d.nr,
        COUNT(e.encounter_nr) no_patient,
        SUM(DATEDIFF(e.discharge_date,DATE(e.encounter_date))+1) AS total_len_stay,

        ".$field_accommodation_type.",
        ".$field_discharge_disposition.",
        ".$field_discharge_result.",
        ".$field_death."
        
        ".$table_source_discharge_date."

        AND cp.fromtemp=1
        AND cp.admitted_baby=0
        AND e.encounter_type IN (".WELLBABY.")
        AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (".NEWBORNDEPT.")

        AND e.pid NOT IN (SELECT 
                            e.pid
                          FROM
                            care_encounter AS e 
                            WHERE e.STATUS NOT IN (".$status.") 
                            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                            AND e.discharge_date IS NOT NULL 
                            AND e.encounter_type IN (".INPATIENT.")
                            AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (".NEWBORNDEPT.")
                          )
        ORDER BY d.name_formal";

$rs_nonpatho = $db->Execute($sql_nonpatho);    

#echo "<br>nonpatho row = ".$rowindex;
if (is_object($rs_nonpatho)){
        while($row_nonpatho=$rs_nonpatho->FetchRow()){

            $discharged_nonpatho = (int) $row_nonpatho['discharged'] + (int) $row_nonpatho['no_disposition'];
            $total_discharges_nonpatho = $discharged_nonpatho + (int) $row_nonpatho['transferred'] + (int) $row_nonpatho['hama'] + (int) $row_nonpatho['absconded'];

            $total_deaths_nonpatho = (int) $row_nonpatho['deathbelow48'] + (int) $row_nonpatho['deathabove48'];
            
            $params->put("no_patient_nonpatho", (int) $row_nonpatho['no_patient']);
            $params->put("total_length_day_nonpatho", (int) $row_nonpatho['total_len_stay']);

            $params->put("non_phic_pay_nonpatho", (int) $row_nonpatho['non_phic_pay']);
            $params->put("non_phic_service_nonpatho", (int) $row_nonpatho['non_phic_service']);
            $params->put("non_phic_total_nonpatho", (int) $row_nonpatho['non_phic_total']);

            $params->put("phic_pay_nonpatho", (int) $row_nonpatho['phic_pay']);
            $params->put("phic_service_nonpatho", (int) $row_nonpatho['phic_service']);
            $params->put("phic_total_nonpatho", (int) $row_nonpatho['phic_total']); 
            
            $params->put("hmo_nonpatho", (int) $row_nonpatho['hmo']);  
            $params->put("owwa_nonpatho", (int) $row_nonpatho['owwa']);

            $params->put("discharged_nonpatho", $discharged_nonpatho);
            $params->put("transferred_nonpatho", (int) $row_nonpatho['transferred']);
            $params->put("hama_nonpatho", (int) $row_nonpatho['hama']);
            $params->put("absconded_nonpatho", (int) $row_nonpatho['absconded']);
            $params->put("total_discharges_nonpatho", (int) $total_discharges_nonpatho);

            $params->put("recovered_nonpatho", (int) $row_nonpatho['recovered_improved']);
            $params->put("unimproved_nonpatho", (int) $row_nonpatho['unimproved']);
            
            $params->put("deathbelow48_nonpatho", (int) $row_nonpatho['deathbelow48']);
            $params->put("deathabove48_nonpatho", (int) $row_nonpatho['deathabove48']);
            $params->put("total_deaths_nonpatho", (int) $total_deaths_pnonpatho);

        }  
  }

