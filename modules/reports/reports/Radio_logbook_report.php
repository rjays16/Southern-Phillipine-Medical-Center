<?php
#created by KENTOOT 08/09/2014
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require_once('./roots.php');
  require_once($root_path.'include/inc_environment_global.php');
  require_once($root_path.'include/care_api_classes/class_personell.php');
  $personell_obj=new Personell;

  include('parameters.php');

    $current_dt = date("Y-m-d");
    $current_time = date("h:i:s A");

    #TITLE of the report
    $params->put("hosp_country",$hosp_country);
    $params->put("hosp_agency", $hosp_agency);
    $params->put("hosp_name", mb_strtoupper($hosp_name));
    $params->put("hosp_addr1", $hosp_addr1);

    $params->put("datefrom", date("F j, Y", strtotime($from_date_format)));
    $params->put("dateto",  date("F j, Y", strtotime($to_date_format)));
    $params->put("user", strtoupper($_SESSION['sess_user_name']));


# added by: syboy 07/29/2015
        #_______ GET PARAMS _______#
        $paramsnotexploded = $_GET['param'];
        $explodedparams = explode(',', $paramsnotexploded);
        // var_dump($explodedparams); die();
        #_______ END GET PARAMS _______#

    #_______ EXPLODE PARAMS _______#
    if ($explodedparams[0] == "") {

        # Alphabetical
        $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        # Patient Type
        $enc_type_2 = "";

        # Radiological Requests
        $sql_type_2 = " AND f.batch_nr IS NULL ";

        # Radiology Section
        $group_cond_2 = " AND g.department_nr IN (167, 164, 208, 166, 165, 209)";

        # Status
        $sql_status_2 = " AND d.is_served IN (0, 1)";

        # Rad. Tech on Duty
        $radtech_cond_2 = "";

        # Resident Doctor
        $doctor_cond_2 = ""; # if paramsnotexploded is NULL

        # Index of Radiology Level 1
        $index_lvl_1 = "";

        # Index of Radiology Level 2
        $index_lvl_2 = "";

        # Index of Radiology Level 3
        $index_lvl_3 = "";

        # Index of Radiology Level 4
        $index_lvl_4 = "";

    } else if(count($explodedparams) == 1) {
        $exploded_count_1 = explode('--', $explodedparams[0]);

        if ($exploded_count_1[0] == 'param_radio_alphabetical'){

            if ($exploded_count_1[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_1[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_1[0] == 'param_radio_pattype') {
            
            if ($exploded_count_1[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_1[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_1[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_1[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_1[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_1[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_1[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_1[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_1[0] == 'param_radio_type') {
            
            if ($exploded_count_1[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_1[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_1[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_1[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_1[1]."'";

        } else if ($exploded_count_1[0] == 'param_radio_status') {
            
            if ($exploded_count_1[1] != 'all'){
                if ($exploded_count_1[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_1[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_1[0] == 'param_radio_radtech') {
            
            if ($exploded_count_1[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_1[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_1[0] == 'param_radio_doctor') {
            
            if ($exploded_count_1[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_1[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        } # if count of array is equal to 1

    } else if(count($explodedparams) == 2) {
        $exploded_count_2 = explode('--', $explodedparams[0]); 
        $exploded_count_2_1 = explode('--', $explodedparams[1]);

        if ($exploded_count_2[0] == 'param_radio_alphabetical'){

            if ($exploded_count_2[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_2[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_2[0] == 'param_radio_pattype') {
            
            if ($exploded_count_2[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_2[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_2[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_2[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_2[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_2[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_2[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_2[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_2[0] == 'param_radio_type') {
            
            if ($exploded_count_2[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_2[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_2[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_2[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_2[1]."'";

        } else if ($exploded_count_2[0] == 'param_radio_status') {
            
            if ($exploded_count_2[1] != 'all'){
                if ($exploded_count_2[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_2[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_2[0] == 'param_radio_radtech') {
            
            if ($exploded_count_2[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_2[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_2[0] == 'param_radio_doctor') {
            
            if ($exploded_count_2[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_2[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_2_1[0] == 'param_radio_alphabetical'){

            if ($exploded_count_2_1[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_2_1[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_2_1[0] == 'param_radio_pattype') {
            
            if ($exploded_count_2_1[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_2_1[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_2_1[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_2_1[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_2_1[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_2_1[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_2_1[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_2_1[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_2_1[0] == 'param_radio_type') {
            
            if ($exploded_count_2_1[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_2_1[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_2_1[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_2_1[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_2_1[1]."'";

        } else if ($exploded_count_2_1[0] == 'param_radio_status') {
            
            if ($exploded_count_2_1[1] != 'all'){
                if ($exploded_count_2_1[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_2_1[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_2_1[0] == 'param_radio_radtech') {
            
            if ($exploded_count_2_1[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_2_1[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_2_1[0] == 'param_radio_doctor') {
            
            if ($exploded_count_2_1[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_2_1[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }  # if count of array is equal to 2

    } else if(count($explodedparams) == 3) {
        $exploded_count_3 = explode('--', $explodedparams[0]);
        $exploded_count_3_1 = explode('--', $explodedparams[1]);
        $exploded_count_3_2 = explode('--', $explodedparams[2]);

        if ($exploded_count_3[0] == 'param_radio_alphabetical'){

            if ($exploded_count_3[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_3[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_3[0] == 'param_radio_pattype') {
            
            if ($exploded_count_3[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_3[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_3[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_3[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_3[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_3[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_3[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_3[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_3[0] == 'param_radio_type') {
            
            if ($exploded_count_3[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_3[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_3[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_3[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_3[1]."'";

        } else if ($exploded_count_3[0] == 'param_radio_status') {
            
            if ($exploded_count_3[1] != 'all'){
                if ($exploded_count_3[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_3[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_3[0] == 'param_radio_radtech') {
            
            if ($exploded_count_3[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_3[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_3[0] == 'param_radio_doctor') {
            
            if ($exploded_count_3[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_3[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_3_1[0] == 'param_radio_alphabetical'){

            if ($exploded_count_3_1[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_3_1[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_3_1[0] == 'param_radio_pattype') {
            
            if ($exploded_count_3_1[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_3_1[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_3_1[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_3_1[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_3_1[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_3_1[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_3_1[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_3_1[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_3_1[0] == 'param_radio_type') {
            
            if ($exploded_count_3_1[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_3_1[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_3_1[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_3_1[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_3_1[1]."'";

        } else if ($exploded_count_3_1[0] == 'param_radio_status') {
            
            if ($exploded_count_3_1[1] != 'all'){
                if ($exploded_count_3_1[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_3_1[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_3_1[0] == 'param_radio_radtech') {
            
            if ($exploded_count_3_1[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_3_1[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_3_1[0] == 'param_radio_doctor') {
            
            if ($exploded_count_3_1[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_3_1[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_3_2[0] == 'param_radio_alphabetical'){

            if ($exploded_count_3_2[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_3_2[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_3_2[0] == 'param_radio_pattype') {
            
            if ($exploded_count_3_2[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_3_2[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_3_2[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_3_2[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_3_2[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_3_2[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_3_2[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_3_2[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_3_2[0] == 'param_radio_type') {
            
            if ($exploded_count_3_2[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_3_2[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_3_2[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_3_2[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_3_2[1]."'";

        } else if ($exploded_count_3_2[0] == 'param_radio_status') {
            
            if ($exploded_count_3_2[1] != 'all'){
                if ($exploded_count_3_2[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_3_2[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_3_2[0] == 'param_radio_radtech') {
            
            if ($exploded_count_3_2[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_3_2[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_3_2[0] == 'param_radio_doctor') {
            
            if ($exploded_count_3_2[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_3_2[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        } # if count of array is equal to 3

    } else if(count($explodedparams) == 4) {
        $exploded_count_4 = explode('--', $explodedparams[0]);
        $exploded_count_4_1 = explode('--', $explodedparams[1]);
        $exploded_count_4_2 = explode('--', $explodedparams[2]);
        $exploded_count_4_3 = explode('--', $explodedparams[3]);

        if ($exploded_count_4[0] == 'param_radio_alphabetical'){

            if ($exploded_count_4[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_4[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_4[0] == 'param_radio_pattype') {
            
            if ($exploded_count_4[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_4[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_4[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_4[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_4[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_4[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_4[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_4[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_4[0] == 'param_radio_type') {
            
            if ($exploded_count_4[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_4[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_4[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_4[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_4[1]."'";

        } else if ($exploded_count_4[0] == 'param_radio_status') {
            
            if ($exploded_count_4[1] != 'all'){
                if ($exploded_count_4[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_4[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_4[0] == 'param_radio_radtech') {
            
            if ($exploded_count_4[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_4[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_4[0] == 'param_radio_doctor') {
            
            if ($exploded_count_4[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_4[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_4_1[0] == 'param_radio_alphabetical'){

            if ($exploded_count_4_1[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_4_1[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_4_1[0] == 'param_radio_pattype') {
            
            if ($exploded_count_4_1[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_4_1[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_4_1[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_4_1[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_4_1[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_4_1[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_4_1[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_4_1[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_4_1[0] == 'param_radio_type') {
            
            if ($exploded_count_4_1[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_4_1[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_4_1[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_4_1[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_4_1[1]."'";

        } else if ($exploded_count_4_1[0] == 'param_radio_status') {
            
            if ($exploded_count_4_1[1] != 'all'){
                if ($exploded_count_4_1[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_4_1[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_4_1[0] == 'param_radio_radtech') {
            
            if ($exploded_count_4_1[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_4_1[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_4_1[0] == 'param_radio_doctor') {
            
            if ($exploded_count_4_1[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_4_1[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_4_2[0] == 'param_radio_alphabetical'){

            if ($exploded_count_4_2[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_4_2[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_4_2[0] == 'param_radio_pattype') {
            
            if ($exploded_count_4_2[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_4_2[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_4_2[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_4_2[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_4_2[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_4_2[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_4_2[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_4_2[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_4_2[0] == 'param_radio_type') {
            
            if ($exploded_count_4_2[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_4_2[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_4_2[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_4_2[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_4_2[1]."'";

        } else if ($exploded_count_4_2[0] == 'param_radio_status') {
            
            if ($exploded_count_4_2[1] != 'all'){
                if ($exploded_count_4_2[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_4_2[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_4_2[0] == 'param_radio_radtech') {
            
            if ($exploded_count_4_2[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_4_2[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_4_2[0] == 'param_radio_doctor') {
            
            if ($exploded_count_4_2[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_4_2[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_4_3[0] == 'param_radio_alphabetical'){

            if ($exploded_count_4_3[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_4_3[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_4_3[0] == 'param_radio_pattype') {
            
            if ($exploded_count_4_3[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_4_3[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_4_3[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_4_3[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_4_3[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_4_3[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_4_3[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_4_3[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_4_3[0] == 'param_radio_type') {
            
            if ($exploded_count_4_3[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_4_3[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_4_3[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_4_3[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_4_3[1]."'";

        } else if ($exploded_count_4_3[0] == 'param_radio_status') {
            
            if ($exploded_count_4_3[1] != 'all'){
                if ($exploded_count_4_3[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_4_3[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_4_3[0] == 'param_radio_radtech') {
            
            if ($exploded_count_4_3[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_4_3[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_4_3[0] == 'param_radio_doctor') {
            
            if ($exploded_count_4_3[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_4_3[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        } # if count of array is equal to 4

    } else if(count($explodedparams) == 5) {
        $exploded_count_5 = explode('--', $explodedparams[0]);
        $exploded_count_5_1 = explode('--', $explodedparams[1]);
        $exploded_count_5_2 = explode('--', $explodedparams[2]);
        $exploded_count_5_3 = explode('--', $explodedparams[3]);
        $exploded_count_5_4 = explode('--', $explodedparams[4]);

        if ($exploded_count_5[0] == 'param_radio_alphabetical'){

            if ($exploded_count_5[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_5[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_5[0] == 'param_radio_pattype') {
            
            if ($exploded_count_5[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_5[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_5[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_5[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_5[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_5[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_5[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_5[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_5[0] == 'param_radio_type') {
            
            if ($exploded_count_5[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_5[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_5[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_5[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_5[1]."'";

        } else if ($exploded_count_5[0] == 'param_radio_status') {
            
            if ($exploded_count_5[1] != 'all'){
                if ($exploded_count_5[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_5[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_5[0] == 'param_radio_radtech') {
            
            if ($exploded_count_5[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_5[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_5[0] == 'param_radio_doctor') {
            
            if ($exploded_count_5[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_5[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_5_1[0] == 'param_radio_alphabetical'){

            if ($exploded_count_5_1[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_5_1[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_5_1[0] == 'param_radio_pattype') {
            
            if ($exploded_count_5_1[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_5_1[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_5_1[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_5_1[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_5_1[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_5_1[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_5_1[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_5_1[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_5_1[0] == 'param_radio_type') {
            
            if ($exploded_count_5_1[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_5_1[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_5_1[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_5_1[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_5_1[1]."'";

        } else if ($exploded_count_5_1[0] == 'param_radio_status') {
            
            if ($exploded_count_5_1[1] != 'all'){
                if ($exploded_count_5_1[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_5_1[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_5_1[0] == 'param_radio_radtech') {
            
            if ($exploded_count_5_1[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_5_1[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_5_1[0] == 'param_radio_doctor') {
            
            if ($exploded_count_5_1[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_5_1[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_5_2[0] == 'param_radio_alphabetical'){

            if ($exploded_count_5_2[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_5_2[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_5_2[0] == 'param_radio_pattype') {
            
            if ($exploded_count_5_2[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_5_2[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_5_2[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_5_2[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_5_2[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_5_2[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_5_2[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_5_2[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_5_2[0] == 'param_radio_type') {
            
            if ($exploded_count_5_2[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_5_2[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_5_2[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_5_2[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_5_2[1]."'";

        } else if ($exploded_count_5_2[0] == 'param_radio_status') {
            
            if ($exploded_count_5_2[1] != 'all'){
                if ($exploded_count_5_2[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_5_2[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_5_2[0] == 'param_radio_radtech') {
            
            if ($exploded_count_5_2[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_5_2[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_5_2[0] == 'param_radio_doctor') {
            
            if ($exploded_count_5_2[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_5_2[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_5_3[0] == 'param_radio_alphabetical'){

            if ($exploded_count_5_3[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_5_3[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_5_3[0] == 'param_radio_pattype') {
            
            if ($exploded_count_5_3[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_5_3[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_5_3[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_5_3[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_5_3[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_5_3[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_5_3[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_5_3[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_5_3[0] == 'param_radio_type') {
            
            if ($exploded_count_5_3[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_5_3[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_5_3[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_5_3[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_5_3[1]."'";

        } else if ($exploded_count_5_3[0] == 'param_radio_status') {
            
            if ($exploded_count_5_3[1] != 'all'){
                if ($exploded_count_5_3[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_5_3[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_5_3[0] == 'param_radio_radtech') {
            
            if ($exploded_count_5_3[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_5_3[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_5_3[0] == 'param_radio_doctor') {
            
            if ($exploded_count_5_3[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_5_3[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_5_4[0] == 'param_radio_alphabetical'){

            if ($exploded_count_5_4[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_5_4[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_5_4[0] == 'param_radio_pattype') {
            
            if ($exploded_count_5_4[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_5_4[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_5_4[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_5_4[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_5_4[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_5_4[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_5_4[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_5_4[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_5_4[0] == 'param_radio_type') {
            
            if ($exploded_count_5_4[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_5_4[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_5_4[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_5_4[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_5_4[1]."'";

        } else if ($exploded_count_5_4[0] == 'param_radio_status') {
            
            if ($exploded_count_5_4[1] != 'all'){
                if ($exploded_count_5_4[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_5_4[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_5_4[0] == 'param_radio_radtech') {
            
            if ($exploded_count_5_4[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_5_4[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_5_4[0] == 'param_radio_doctor') {
            
            if ($exploded_count_5_4[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_5_4[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        } # if count of array is equal to 5

    } else if(count($explodedparams) == 6) {
        $exploded_count_6 = explode('--', $explodedparams[0]);
        $exploded_count_6_1 = explode('--', $explodedparams[1]);
        $exploded_count_6_2 = explode('--', $explodedparams[2]);
        $exploded_count_6_3 = explode('--', $explodedparams[3]);
        $exploded_count_6_4 = explode('--', $explodedparams[4]);
        $exploded_count_6_5 = explode('--', $explodedparams[5]);

        if ($exploded_count_6[0] == 'param_radio_alphabetical'){

            if ($exploded_count_6[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_6[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_6[0] == 'param_radio_pattype') {
            
            if ($exploded_count_6[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_6[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_6[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_6[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_6[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_6[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_6[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_6[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_6[0] == 'param_radio_type') {
            
            if ($exploded_count_6[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_6[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_6[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_6[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_6[1]."'";

        } else if ($exploded_count_6[0] == 'param_radio_status') {
            
            if ($exploded_count_6[1] != 'all'){
                if ($exploded_count_6[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_6[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_6[0] == 'param_radio_radtech') {
            
            if ($exploded_count_6[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_6[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_6[0] == 'param_radio_doctor') {
            
            if ($exploded_count_6[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_6[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_6_1[0] == 'param_radio_alphabetical'){

            if ($exploded_count_6_1[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_6_1[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_6_1[0] == 'param_radio_pattype') {
            
            if ($exploded_count_6_1[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_6_1[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_6_1[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_6_1[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_6_1[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_6_1[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_6_1[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_6_1[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_6_1[0] == 'param_radio_type') {
            
            if ($exploded_count_6_1[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_6_1[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_6_1[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_6_1[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_6_1[1]."'";

        } else if ($exploded_count_6_1[0] == 'param_radio_status') {
            
            if ($exploded_count_6_1[1] != 'all'){
                if ($exploded_count_6_1[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_6_1[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_6_1[0] == 'param_radio_radtech') {
            
            if ($exploded_count_6_1[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_6_1[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_6_1[0] == 'param_radio_doctor') {
            
            if ($exploded_count_6_1[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_6_1[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_6_2[0] == 'param_radio_alphabetical'){

            if ($exploded_count_6_2[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_6_2[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_6_2[0] == 'param_radio_pattype') {
            
            if ($exploded_count_6_2[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_6_2[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_6_2[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_6_2[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_6_2[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_6_2[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_6_2[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_6_2[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_6_2[0] == 'param_radio_type') {
            
            if ($exploded_count_6_2[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_6_2[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_6_2[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_6_2[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_6_2[1]."'";

        } else if ($exploded_count_6_2[0] == 'param_radio_status') {
            
            if ($exploded_count_6_2[1] != 'all'){
                if ($exploded_count_6_2[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_6_2[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_6_2[0] == 'param_radio_radtech') {
            
            if ($exploded_count_6_2[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_6_2[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_6_2[0] == 'param_radio_doctor') {
            
            if ($exploded_count_6_2[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_6_2[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_6_3[0] == 'param_radio_alphabetical'){

            if ($exploded_count_6_3[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_6_3[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_6_3[0] == 'param_radio_pattype') {
            
            if ($exploded_count_6_3[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_6_3[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_6_3[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_6_3[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_6_3[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_6_3[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_6_3[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_6_3[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_6_3[0] == 'param_radio_type') {
            
            if ($exploded_count_6_3[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_6_3[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_6_3[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_6_3[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_6_3[1]."'";

        } else if ($exploded_count_6_3[0] == 'param_radio_status') {
            
            if ($exploded_count_6_3[1] != 'all'){
                if ($exploded_count_6_3[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_6_3[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_6_3[0] == 'param_radio_radtech') {
            
            if ($exploded_count_6_3[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_6_3[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_6_3[0] == 'param_radio_doctor') {
            
            if ($exploded_count_6_3[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_6_3[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_6_4[0] == 'param_radio_alphabetical'){

            if ($exploded_count_6_4[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_6_4[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_6_4[0] == 'param_radio_pattype') {
            
            if ($exploded_count_6_4[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_6_4[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_6_4[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_6_4[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_6_4[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_6_4[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_6_4[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_6_4[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_6_4[0] == 'param_radio_type') {
            
            if ($exploded_count_6_4[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_6_4[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_6_4[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_6_4[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_6_4[1]."'";

        } else if ($exploded_count_6_4[0] == 'param_radio_status') {
            
            if ($exploded_count_6_4[1] != 'all'){
                if ($exploded_count_6_4[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_6_4[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_6_4[0] == 'param_radio_radtech') {
            
            if ($exploded_count_6_4[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_6_4[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_6_4[0] == 'param_radio_doctor') {
            
            if ($exploded_count_6_4[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_6_4[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        }

        if ($exploded_count_6_5[0] == 'param_radio_alphabetical'){

            if ($exploded_count_6_5[1] == '1')
                $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
            elseif ($exploded_count_6_5[1] == '2')
                $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";

        } else if ($exploded_count_6_5[0] == 'param_radio_pattype') {
            
            if ($exploded_count_6_5[1] == '0')
                $enc_type_2 = "";
            elseif ($exploded_count_6_5[1] == '1')
                $enc_type_2 = "AND encounter_type IN (1)";
            elseif ($exploded_count_6_5[1] == '2')
                $enc_type_2 = "AND encounter_type IN (3,4)";
            elseif ($exploded_count_6_5[1] == '3')
                $enc_type_2 = "AND encounter_type IN (2)";
            elseif ($exploded_count_6_5[1] == '4')
                $enc_type_2 = "AND encounter_type IS NULL";
            elseif ($exploded_count_6_5[1] == '5')
                $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
            elseif ($exploded_count_6_5[1] == '6')
                $enc_type_2 = "AND encounter_type IN (1,3,4)";
            elseif ($exploded_count_6_5[1] == '7')
                $enc_type_2 = "AND encounter_type IN (6)";

        } else if ($exploded_count_6_5[0] == 'param_radio_type') {
            
            if ($exploded_count_6_5[1] == '1'){
                $sql_type_2 = " AND f.batch_nr IS NULL ";    
            }elseif ($exploded_count_6_5[1] == '2'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
            }elseif ($exploded_count_6_5[1] == '3'){
                $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
            }

        } else if ($exploded_count_6_5[0] == 'param_radio_section') {
            
            $group_cond_2 = " AND g.department_nr='".$exploded_count_6_5[1]."'";

        } else if ($exploded_count_6_5[0] == 'param_radio_status') {
            
            if ($exploded_count_6_5[1] != 'all'){
                if ($exploded_count_6_5[1] == '1')
                    $is_served = 1;
                else if ($exploded_count_6_5[1] == '2')
                    $is_served = 0;
                $sql_status_2 = " AND d.is_served= '".$is_served."'";
            } else {
                $sql_status_2 = " AND d.is_served IN (0, 1)";
            }

        } else if ($exploded_count_6_5[0] == 'param_radio_radtech') {
            
            if ($exploded_count_6_5[1] != 'all'){
                $radtech_cond_2 = " AND d.rad_tech= '".$exploded_count_6_5[1]."'";
            } else {
                $radtech_cond_2 = "";
            }

        } else if ($exploded_count_6_5[0] == 'param_radio_doctor') {
            
            if ($exploded_count_6_5[1] != 'all'){
                $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_count_6_5[1]."%' ";
            } else {
                $doctor_cond_2 = "";
            }

        } # if count of array is equal to 6

    } else if(count($explodedparams) == 7) {

            #--->> START radio_alphabetical params <<---#
            $exploded_radio_alphabetical = explode('--', $explodedparams[0]);
            if ($exploded_radio_alphabetical[0] == 'param_radio_alphabetical'){
                if ($exploded_radio_alphabetical[1] == '1')
                    $orderby_2 = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
                elseif ($exploded_radio_alphabetical[1] == '2')
                    $orderby_2 = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";
            }
            #--->> END radio_alphabetical params <<---#

            #--->> START radio_pattype params <<---#
            $exploded_radio_pattype = explode('--', $explodedparams[1]);
            if ($exploded_radio_pattype[1] == 'param_radio_pattype'){
                if ($exploded_radio_pattype[0] == '0')
                    $enc_type_2 = "";
                elseif ($exploded_radio_pattype[0] == '1')
                    $enc_type_2 = "AND encounter_type IN (1)";
                elseif ($exploded_radio_pattype[0] == '2')
                    $enc_type_2 = "AND encounter_type IN (3,4)";
                elseif ($exploded_radio_pattype[0] == '3')
                    $enc_type_2 = "AND encounter_type IN (2)";
                elseif ($exploded_radio_pattype[0] == '4')
                    $enc_type_2 = "AND encounter_type IS NULL";
                elseif ($exploded_radio_pattype[0] == '5')
                    $enc_type_2 = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
                elseif ($exploded_radio_pattype[0] == '6')
                    $enc_type_2 = "AND encounter_type IN (1,3,4)";
                elseif ($exploded_radio_pattype[0] == '7')
                    $enc_type_2 = "AND encounter_type IN (6)";
            }
            #--->> END radio_pattype params <<---#

            #--->> START radio_type params <<---#
            $exploded_radio_type = explode('--', $explodedparams[2]);
            if ($exploded_radio_type[0] == 'param_radio_type'){
                #'1-Without Results','2-Initial Results','3-Official Results'
                if ($exploded_radio_type[1] == '1'){
                    $sql_type_2 = " AND f.batch_nr IS NULL ";    
                }elseif ($exploded_radio_type[1] == '2'){
                    $sql_type_2 = " AND (f.batch_nr AND d.STATUS='pending') ";
                }elseif ($exploded_radio_type[1] == '3'){
                    $sql_type_2 = " AND (f.batch_nr AND d.STATUS='done') ";
                }
            }
            #--->> START radio_type params <<---#

            #--->> START radio_section params <<---#
            $exploded_radio_section = explode('--', $explodedparams[3]);
            if ($exploded_radio_section[0] == 'param_radio_section'){
                $group_cond_2 = " AND g.department_nr='".$exploded_radio_section[1]."'";
            }
            #--->> END radio_section params <<---#

            #--->> START radio_status params <<---#
            $exploded_radio_status = explode('--', $explodedparams[4]);
            if ($exploded_radio_status[0] == 'param_radio_status'){
                if ($exploded_radio_status[1] != 'all'){
                    if ($exploded_radio_status[1] == '1')
                        $is_served = 1;
                    else if ($exploded_radio_status[1] == '2')
                        $is_served = 0;
                    $sql_status_2 = " AND d.is_served= '".$is_served."'";
                } else {
                    $sql_status_2 = " AND d.is_served IN (0, 1)";
                }
            }
            #--->> START radio_status params <<---#

            #--->> START radio_radtech params <<---#
            $exploded_radio_radtech = explode('--', $explodedparams[5]);
            if ($exploded_radio_radtech[0] == 'param_radio_radtech'){
                if ($exploded_radio_radtech[1] != 'all'){
                    $radtech_cond_2 = " AND d.rad_tech= '".$exploded_radio_radtech[1]."'";
                } else {
                    $radtech_cond_2 = "";
                }
            }
            #--->> END radio_radtech params <<---#

            #--->> START radio_doctor params <<---#
            $exploded_radio_doctor = explode('--', $explodedparams[6]);
            if ($exploded_radio_doctor[0] == 'param_radio_doctor'){
                if ($exploded_radio_doctor[1] != 'all'){
                    $doctor_cond_2 = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$exploded_radio_doctor[1]."%' ";
                } else {
                    $doctor_cond_2 = "";
                }
            }
            #--->> END radio_doctor params <<---# # if count of array is equal to 7

    }
    #_______ END EXPLODE PARAMS _______#

#end

    #edited by VAN 03/25/2015
    $query = "SELECT DISTINCT SQL_CALC_FOUND_ROWS h.pid,
                        fn_get_person_name(h.pid) AS patient_name, 
                        IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
                        d.service_code,
                        g.name AS group_name,
                        s.NAME AS service_name,
                        dp.name_formal,
                        dp.name_short,
                        IF(d.is_served, 'Yes', 'Not Yet') AS is_served,
                        fn_get_personellname_lastfirstmi(d.rad_tech) AS radtech,
                        d.rad_tech,
                        CONCAT(h.request_date,' ', h.request_time) AS request_date,
                        rd.rid,
                        f.doctor_in_charge,
                        h.request_date,
                        d.service_date,
                        d.batch_nr,
                        d.save_and_done,
                        CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) AS doc_nr,
                        IF (d.served_date, d.served_date, IF(d.service_date,d.service_date,f.findings_date)) AS 
                        service_date,
                        f.create_dt,
                        e.encounter_type,
                        IF(h.is_cash,'Cash','Charge') AS iscash,
                        p.sex,
                        date_birth,
                        f.modify_dt,
                        IF ((f.batch_nr && d.STATUS='done'),'Official',IF(f.batch_nr && 
                        d.STATUS='pending','Initial','None')) AS result,
                        IF (f.batch_nr && d.STATUS='pending','',TIMEDIFF(IF(d.save_and_done = '0000-00-00 00:00:00' , f.create_dt , d.save_and_done),d.served_date)) AS turn_around_time,
                        h.refno, f.batch_nr, f.radio_impression, f.findings, d.clinical_info
                                

                FROM seg_radio_serv h
                INNER JOIN care_person p ON p.pid=h.pid
                INNER JOIN care_test_request_radio d ON d.refno=h.refno
                LEFT JOIN care_test_findings_radio f ON f.batch_nr=d.batch_nr
                LEFT JOIN care_test_findings_radio_doc_nr dr ON dr.batch_nr=d.batch_nr
                INNER JOIN seg_radio_services s ON s.service_code=d.service_code
                INNER JOIN seg_radio_service_groups g ON g.group_code=s.group_code
                INNER JOIN care_department dp ON dp.nr=g.department_nr
                INNER JOIN seg_radio_id rd ON rd.pid=h.pid
                LEFT JOIN care_encounter e ON e.encounter_nr=h.encounter_nr
                LEFT JOIN seg_radio_index_finding idx ON idx.Batch_nr = d.batch_nr
                WHERE DATE(h.request_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                AND h.STATUS NOT IN ('deleted','hidden','inactive','void')
                AND d.STATUS NOT IN ('deleted','hidden','inactive','void')
                AND g.fromdept='RD' 
                AND s.service_code NOT IN (SELECT service_code FROM seg_radio_services_excluded)

                       $enc_type_2
                       $sql_status_2
                       $radtech_cond_2
                       $group_cond_2
                       $doctor_cond_2
                       $index_lvl_1
                       $index_lvl_2
                       $index_lvl_3
                       $index_lvl_4
                       $sql_type_2
                       $orderby_2
                ";
  #die($query);         
  $rs=$db->Execute($query);

  #added by VAN 03/25/2015
  $num_rows = $db->GetOne("SELECT FOUND_ROWS()");

  $data = array();
  $i = 0;

      if(is_object($rs)){
          if($rs->RecordCount() > 0) {
         while ($row=$rs->FetchRow()) {
                if($row["date_birth"]!='0000-00-00')
                    $bdate = date("m/d/Y",strtotime($row["date_birth"]));
                else
                    $bdate = "unknown";
                    
                if (stristr($row['age'],'years')){
                    $age = substr($row['age'],0,-5);
                    $age = floor($age).' y';
                }elseif (stristr($row['age'],'year')){
                    $age = substr($row['age'],0,-4);
                    $age = floor($age).' y';
                }elseif (stristr($row['age'],'months')){
                    $age = substr($row['age'],0,-6);
                    $age = floor($age).' m';
                }elseif (stristr($row['age'],'month')){
                    $age = substr($row['age'],0,-5);
                    $age = floor($age).' m';
                }elseif (stristr($row['age'],'days')){
                    $age = substr($row['age'],0,-4);

                    if ($age>30){
                        $age = $age/30;
                        $label = 'm';
                    }else
                        $label = 'd';

                    $age = floor($age).' '.$label;
                }elseif (stristr($row['age'],'day')){
                    $age = substr($row['age'],0,-3);
                    $age = floor($age).' d';
                }else{
                    $age = floor($row['age']).' y';
                }

                 if ($row['encounter_type']==1){
                    $pat_type = "ERPx";
                }elseif ($row['encounter_type']==2){
                    $pat_type = 'OPDPx';
                }elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
                    $pat_type = 'InPx';
                }elseif ($row['encounter_type']==6){
                    $pat_type = 'ICPx';
                }else{
                    $pat_type = 'WPx';
                }    
                
                if (stristr($row["service_date"],'{')){
                    $date_array = unserialize($row["service_date"]);
                    $row["service_date"] = $date_array[count($date_array)-1];    
                }  

                if(($row["service_date"]!='0000-00-00 00:00:00')&&($row["service_date"]!='0000-00-00')&&($row["service_date"]!=''))
                    $service_date = date("m/d/Y \ h:i A",strtotime($row["service_date"]));
                else
                    $service_date = "";
                    
                    $docs =  $row['doc_nr'];
                    $doctor_final2 = '';
                    $nr = explode(',',$docs);
                    foreach($nr as $key => $value){
                        if($value!=''){
                            $row_pr=$personell_obj->get_Person_name($value);
                            $dr_name = mb_strtoupper($row_pr['dr_name']).", ".$row_pr['drtitle'];
                            $pos =  mb_strtoupper(trim($row_pr['job_position']));
                            $doctor_final2 .= $dr_name."\n".$pos."\n";
                        }
                    }    

                $doctors_array = unserialize($row['doctor_in_charge']);
                $doctor_final = $doctors_array[count($doctors_array)-1];
                if(!is_array($doctor_final) && $doctor_final != ''){
                   $doctor = mb_strtoupper(mb_convert_encoding($doctor_final, "ISO-8859-1", 'UTF-8')); 
                }else{
                    $doctor = mb_strtoupper(mb_convert_encoding($doctor_final2 , "ISO-8859-1", 'UTF-8')); 
                }

                 $create_dt = ($row["create_dt"] == NULL ? '' : date('m/d/Y \ h:i A',strtotime($row["create_dt"])));  
              
              if (!$row['radio_impression']){
                $impression = '';
                #$impression = "No Result Yet";
                #if ($row['clinical_info'])
                #  $impression = "Clinical Info : ".$row['clinical_info'];
              }else{
                $radio_impression_array = unserialize($row['radio_impression']);
                
                if ($radio_impression_array !== false)
                  $impression = $radio_impression_array[0];
                else
                  $impression = $row['radio_impression'];
              }  
              $created_dt2 ="";
              $Rad_Tech ="";

              if ($row['save_and_done'] == '0000-00-00 00:00:00') {
                  $created_dt2 =$create_dt;
              }elseif ($service_date ==''||  $row['result']=="None") {
                   $created_dt2 ="";
                   $Rad_Tech ="";


              }
              else{
                $Rad_Tech = mb_strtoupper($row['radtech']);
                $created_dt2 =date('m/d/Y \ h:i A',strtotime($row['save_and_done']));
              }

           $data[$i] = array(#'i'           => $i+1,
                              'pid'         => $row['pid'],
                              'rid'         => $row['rid'], 
                              'p_name'      => utf8_decode(trim( mb_strtoupper($row['patient_name']))),
                              'age'         => $age,
                              'sex'         => mb_strtoupper($row['sex']),
                              'type'        => $pat_type,
                              'section'     => $row['name_short'],
                              'group'       => $row['group_name'],
                              'service'     => $row['service_name'],
                              'date_serve'  => $service_date,
                              'served'      => $row['is_served'],
                              'date_official'    => $created_dt2,
                              'rad_tech'         => mb_strtoupper($row['radtech']),
                              'result'           => $row['result'],
                              'reader'           => $doctor,
                              'turn_around_time' => $row['turn_around_time'],
                              'impression'       => strip_tags(html_entity_decode($impression))
                            ); 
                  
       $i++;        
    }
          }
          else{
              $data[0]['p_name'] = "No Data Found.";
          }
  }else{
    $data[0]['patientID'] = NULL;
  } 
  
 #$params->put("no_of_records", strtoupper($i));  
  $params->put("no_of_records", $num_rows);  
?>