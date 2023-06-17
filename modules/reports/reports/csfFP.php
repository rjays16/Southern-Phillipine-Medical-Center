<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    include_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/care_api_classes/ehrhisservice/Ehr.php');
    // Mod by Jeff 03-14-18 for enhancement of form.
    include('parameters.php');
    $ehr = Ehr::instance();
    $enc_no = $param['enc_no'];
    $pid = $param['pid'];
    define('OUTPATIENT', 2);
    define('NEW_HOUSE_DOCTOR', 'new_house_doctor');

    //patient info
    $strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
                    p.name_3 AS ThirdName, p.name_middle AS MiddleName, p.suffix as Suffix, p.date_birth as Bday
                    FROM care_person AS p
                    WHERE p.pid = '$pid'";

    $result = $db->Execute($strSQL);
    $patient = $result->FetchRow();

    if($patient['Suffix'])
      $patient['FirstName'] = str_replace(' '.$patient['Suffix'], '', $patient['FirstName']);
    
    // $pt_name = utf8_decode(strtoupper($patient['LastName'] . ", " . $patient['FirstName'] . " " .
    //            (is_null($patient['Suffix']) || $patient['Suffix'] == "" ? "" : $patient['Suffix']) .
    //            " " . $patient['MiddleName'])); 
    $pt_name = mb_strtoupper($patient['LastName'] . ", " . $patient['FirstName'] . " " .
               (is_null($patient['Suffix']) || $patient['Suffix'] == "" ? "" : $patient['Suffix']) .
               " " . $patient['MiddleName']);

    $params->put("patient_name", utf8_decode(trim($pt_name)));

    //signatory info
    $strSQL = "SELECT ss.personell_nr, ss.signatory_position, cpn.name_last, cpn.name_first, 
               cpn.name_middle, cpn.suffix, cpn.sex
               FROM seg_signatory ss INNER JOIN care_personell cp ON cp.nr = ss.personell_nr
               INNER JOIN care_person cpn ON cpn.pid = cp.pid WHERE ss.document_code = 'csf'";

    $result = $db->Execute($strSQL);
    $signatory = $result->FetchRow();

    $name_title = (strtoupper($signatory['sex']) == "M" ? "MR." : "MS.");

    // $signatory_name = $name_title . " " . utf8_decode(strtoupper($signatory['name_last'] . ", " . $signatory['name_first'] . " " .
    //            (is_null($signatory['suffix']) || $signatory['suffix'] == "" ? "" : $signatory['suffix']) .
    //            " " . $signatory['name_middle']));

    $signatory_name = $name_title . " " . mb_strtoupper($signatory['name_first'] . " " . $signatory['name_middle'] . " " . 
                      $signatory['name_last'] . " " . (is_null($signatory['suffix']) || $signatory['suffix'] == "" ? "" : $signatory['suffix']));

    $params->put("signatory_name", utf8_decode(trim($signatory_name)));
    $params->put("designation", strtoupper($signatory['signatory_position']));
               
    //bill info
    $strSQL = "SELECT sbe.accommodation_type, sbe.bill_nr, sbe.bill_dte FROM seg_billing_encounter sbe 
               WHERE sbe.encounter_nr = '$enc_no' AND sbe.is_deleted IS NULL AND sbe.is_final = 1";
    $result = $db->Execute($strSQL);

    $hasfinal = 0;
    if($result->RecordCount())
        $hasfinal = 1;

    if (!$result) {
        die("Error: No final bill for this encounter yet!");
    }

    // $hci_rep = getHCIRepresentative();
    // $params->put("hci_rep", $hci_rep[0].", ".$hci_rep[1]);
    // $params->put("hci_rep_position", $hci_rep[2]);

    $bill = $result->FetchRow();
    $isServ = $bill['accommodation_type'];
    $bill_nr = $bill['bill_nr'];
    $sign_date = getCalculateDate($bill['bill_dte']);
    $params->put("sign_date", $sign_date); 

    $strSQL = "SELECT sbp.dr_nr, cp.name_first, cp.name_last, cp.name_middle, cp.suffix, max_acc.accreditation_nr , sbp.role_area
               FROM seg_billing_pf sbp LEFT JOIN (SELECT sda.dr_nr, sda.accreditation_nr, MAX(sda.create_dt) AS create_dt 
               FROM seg_dr_accreditation sda GROUP BY sda.dr_nr) AS max_acc ON max_acc.dr_nr = sbp.dr_nr 
               INNER JOIN care_personell cpl ON cpl.nr = sbp.dr_nr INNER JOIN care_person cp ON cp.pid = cpl.pid
               WHERE sbp.bill_nr = '$bill_nr'";
    // var_dump($strSQL);die;
    $doctors = $db->Execute($strSQL);
    if (!$result) {
        die("Error: No professional fee coverage!");
    }
    
    // 1st case rate @ jeff 04-04-18
    $caseSQL = "SELECT 
                  sbc.`package_id`
                FROM
                  `seg_billing_caserate` AS sbc
                WHERE sbc.`bill_nr` =  ".$db->qstr($bill_nr)."
                  AND sbc.`rate_type` = '1' 
                  AND sbc.`is_deleted` <> '1' ";
    $cases = $db->Execute($caseSQL);
    $caseRate = $cases->FetchRow();
    $caseFirstRate = $caseRate['package_id'];
    $params->put("fcase", $caseRate['package_id']);
    
    // 2nd case rate @ jeff 04-04-18
    $caseSQL = "SELECT 
                  sbc.`package_id`
                FROM
                  `seg_billing_caserate` AS sbc
                WHERE sbc.`bill_nr` =  ".$db->qstr($bill_nr)."
                  AND sbc.`rate_type` = '2' 
                  AND sbc.`is_deleted` <> '1' ";
    $cases = $db->Execute($caseSQL);
    $caseRate = $cases->FetchRow();
    $caseSecondRate = $caseRate['package_id'];
    $params->put("scase", $caseRate['package_id']);

    $pattern = array('/[a-zA-Z]/', '/[ -]+/', '/^-|-$/');
    $rowindex = 0;
    $grpindex = 1;
    $data = array();
    $opdDept = getOpdAsuResult();
    $opdDept = explode(',', $opdDept);
    // var_dump($doctors->FetchRow());die;
    if($hasfinal){
        if ( (!isHouseCase($enc_no)) || (isHouseCase($enc_no) && $isServ == $opdDept[0]) || (!isHouseCase($enc_no) && $isServ == $opdDept[1])) {
            if (is_object($doctors)){
                while($row=$doctors->FetchRow()){
                    if($row['suffix'])
                        $row['name_first'] = str_replace(' '.$row['suffix'], '', $row['name_first']);

                    $accreditation_nr = preg_replace($pattern, '', $row['accreditation_nr']);
                    $data[$rowindex] = array('rowindex' => $rowindex+1,
                                             'groupidx' => $grpindex,
                                             'accreditation_nr' => $accreditation_nr,
                                             'name_last' => (strtoupper($row['name_last'])),
                                             'name_first' => (strtoupper($row['name_first'])),
                                             'name_middle' => (strtoupper($row['name_middle'])),
                                             'suffix' => strtoupper($row['suffix']),
                                             'date_signed' => (is_null($accreditation_nr) || $accreditation_nr == "" ? "" : $sign_date)
                                            );               
                   $rowindex++;
                   if ($rowindex % 3 == 0) {
                        $grpindex++;
                   }
                }  
                //add blank rows if necessary
                $rowspergroup = 3;
                $addrows = ($rowspergroup - $rowindex % 3);
                $totalrows = $addrows + $rowindex;
                $rowindex++;
                while ($rowindex <= $totalrows) {
                    $data[$rowindex] = array('rowindex' => $rowindex+1,
                                             'groupidx' => $grpindex,
                                             'accreditation_nr' => "",
                                             'name_last' => "",
                                             'name_first' => "",
                                             'name_middle' => "",
                                             'suffix' => "",
                                             'date_signed' => ""
                                            );               
                    $rowindex++;
                }  
            }else{
                $data[0]['code'] = NULL; 
            }
        } else { //housecase
            $pfroles = array();
            while($row=$doctors->FetchRow()){
                $pfroles[] = $row['role_area'];
            }
            $pfroles = array_unique($pfroles);
            $case = findCaseType($bill_nr);
            $result = getHouseCaseDoctor($case, $pfroles);
            /**
            * Added by jeff 04-04-18 for printing of csf2 before adding of doctors
            */
            if (!$result){
                $rowspergroup = 3;
                $addrows = ($rowspergroup - $rowindex % 3);
                $totalrows = $addrows + $rowindex;
                $rowindex++;
                while ($rowindex <= $totalrows) {
                    $data[$rowindex] = array('rowindex' => $rowindex+1,
                                             'groupidx' => $grpindex,
                                             'accreditation_nr' => "",
                                             'name_last' => "",
                                             'name_first' => "",
                                             'name_middle' => "",
                                             'suffix' => "",
                                             'date_signed' => ""
                                            );               
                    $rowindex++;
                }
            }else{
                while($row=$result->FetchRow()){
                    if($row['suffix'])
                        $row['name_first'] = str_replace(' '.$row['suffix'], '', $row['name_first']);

                    $accreditation_nr = preg_replace($pattern, '', $row['accreditation_nr']);
                    $data[$rowindex] = array('rowindex' => $rowindex+1,
                                            'groupidx' => $grpindex,
                                            'accreditation_nr' => $accreditation_nr,
                                            'name_last' => utf8_decode(strtoupper($row['name_last'])),
                                            'name_first' => utf8_decode(strtoupper($row['name_first'])),
                                            'name_middle' => utf8_decode(strtoupper($row['name_middle'])),
                                            'suffix' => strtoupper($row['suffix']),
                                            'date_signed' => (is_null($accreditation_nr) || $accreditation_nr == "" ? "" : $sign_date)
                                           );               
                    $rowindex++;
                    if ($rowindex % 3 == 0) {
                        $grpindex++;
                    }
                }
                //add blank rows if necessary
                $rowspergroup = 3;
                $addrows = ($rowspergroup - $rowindex % 3);
                $totalrows = $addrows + $rowindex;
                $rowindex++;
                while ($rowindex <= $totalrows) {
                    $data[$rowindex] = array('rowindex' => $rowindex+1,
                                             'groupidx' => $grpindex,
                                             'accreditation_nr' => "",
                                             'name_last' => "",
                                             'name_first' => "",
                                             'name_middle' => "",
                                             'suffix' => "",
                                             'date_signed' => ""
                                            );               
                    $rowindex++;
                }  
            }
        }    
    }else{
        $createDate = getCreateDate();
        $dateNow = date('Y-m-d H:i:s');

        $housedoctor = $dateNow >= $createDate ? getHCIRepresentative('new_house_doctor') : getHCIRepresentative('house_doctor');

        $sqlgetdefault = "SELECT sda.`accreditation_nr`, cp.`name_last`, cp.`name_first`, cp.`name_middle`, cp.`suffix` FROM seg_dr_accreditation sda INNER JOIN care_personell cpe ON sda.dr_nr = cpe.`nr` LEFT JOIN care_person cp ON cpe.pid = cp.pid WHERE sda.`dr_nr` = ".$db->qstr($housedoctor[0]);

        $defaultdoctor = $db->GetAll($sqlgetdefault);

        foreach($defaultdoctor as $default){
            if($default['suffix'])
                $default['name_first'] = str_replace(' '.$default['suffix'], '', $default['name_first']);
                    
            $accreditation_nr = preg_replace($pattern, '', $default['accreditation_nr']);
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                                     'groupidx' => $grpindex,
                                     'accreditation_nr' => $accreditation_nr,
                                     'name_last' => (strtoupper($default['name_last'])),
                                     'name_first' => (strtoupper($default['name_first'])),
                                     'name_middle' => (strtoupper($default['name_middle'])),
                                     'suffix' => strtoupper($default['suffix']),
                                     'date_signed' => ''
                                    );
            $rowindex++;
            if ($rowindex % 3 == 0) {
                $grpindex++;
           }
        }

        //add blank rows if necessary
        $rowspergroup = 3;
        $addrows = ($rowspergroup - $rowindex % 3);
        $totalrows = $addrows + $rowindex;
        $rowindex++;
        while ($rowindex <= $totalrows) {
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                                     'groupidx' => $grpindex,
                                     'accreditation_nr' => "",
                                     'name_last' => "",
                                     'name_first' => "",
                                     'name_middle' => "",
                                     'suffix' => "",
                                     'date_signed' => ""
                                    );               
            $rowindex++;
        }  

    }
    
    /**
    * Created By Jarel
    * Created On 03/07/2014
    * Edited by Jasper Ian Q. Matunog 11/24/2014
    * Get Calculate Date Excluding Weekends
    * @param string bill_dte
    * @return date
    **/
    function getCalculateDate($bill_dte) {
        
        if($bill_dte != NULL){
            $bill_dte = date('Y-m-d',strtotime($bill_dte));
            $numberofdays = 5;

            $date_orig = new DateTime($bill_dte);
            
            $t = $date_orig->format("U"); //get timestamp

            // loop for X days
            for($i=0; $i<$numberofdays ; $i++){

                // add 1 day to timestamp
                $addDay = 86400;

                // get what day it is next day
                $nextDay = date('w', ($t+$addDay));

                // if it's Saturday or Sunday get $i-1
                if($nextDay == 0 || $nextDay == 6) {
                    $i--;
                }

                // modify timestamp, add 1 day
                $t = $t+$addDay;
            }

            return date('mdY', ($t));
        }else{
            return '';
        }
    }    

    function isHouseCase($encno) {
        global $db;

        $housecase = true;
        $strSQL = "select fn_isHouseCase('" . $encno . "') as casetype";
        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                     $housecase = is_null($row["casetype"]) ? true : ($row["casetype"] == 1);
                }
            }
        }
        return $housecase;
    }

    function findCaseType($billno) {
        global $db;
        $first_type = '';
        $second_type = '';
        $strSQL = "SELECT p.case_type, sc.rate_type
                    FROM seg_billing_caserate sc 
                    INNER JOIN seg_case_rate_packages p 
                        ON p.`code` = sc.`package_id`
                    WHERE bill_nr = '$billno'"; 
        
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if($row['rate_type']==1)
                        $first_type = $row['case_type'];
                    else
                        $second_type = $row['case_type'];
                }
            }
        }

        //$case = 0;
        if ($first_type == 'm' && ($second_type == 'm' || is_null($second_type) || $second_type == '')) {
            $case = 1;
        } elseif($first_type == 'p' && ($second_type == 'p' || is_null($second_type) || $second_type == '')) {
            $case = 2;
        } elseif($first_type != $second_type && $second_type!='') {
            $case = 3;
        }

        return $case;
    }

    function getHouseCaseDoctor($case, $pfroles) {
        // var_dump($pfroles);die();
        global $db;
        $attnCond = "cpl.is_housecase_attdr = 1";
        $surgCond = "cpl.is_housecase_surgeon = 1";
        $anesCond = "cpl.is_housecase_anesth = 1";

        // Comment out by jeff as per CF2 inline consistency.
        // if ($case == 1) { //medical case - default Dr. Vega
        //     $strSQL .= $surgCond;
        //     if (in_array("D4",$pfroles) && in_array("D3",$pfroles) && in_array("D1",$pfroles)){
        //         $strSQL .= " OR " . $anesCond . " OR ".$attnCond;
        //     }
        //     if (in_array("D3",$pfroles) && in_array("D1",$pfroles)){
        //         $strSQL .= " OR ".$attnCond;
        //     }
        // }
        // elseif($case == 2) { //surgical case - default Dr. Vega and Dr. Audan
        //     $strSQL .= $surgCond;
        //     if (in_array("D4",$pfroles) && in_array("D3",$pfroles) && in_array("D1",$pfroles)){
        //         $strSQL .= " OR " . $anesCond . " OR ".$attnCond;
        //     }
        //     else {
        //         $strSQL .= " OR " . $anesCond;
        //     }
        // } 
        // else { //mixed case - default Dr. Vega, Dr. Audan and Dr. Concha(if with D1 or D2)

        // $strSQ;
        // }

        $filter = '';

        if (in_array("D4", $pfroles)) {
            $filter .= ' OR '.$anesCond;
        } 

        if (in_array("D1", $pfroles)) {
            $filter .= ' OR '.$attnCond;
        }

        if (in_array("D3", $pfroles)) {
            $filter .= ' OR '.$surgCond;
        }

        if (in_array("D2", $pfroles)) {
            $filter .= ' OR '.$attnCond;
        }

        $orCount = substr_count($filter,"OR");

        $filter = substr($filter, 3);
        



        $strSQL = "SELECT cpl.nr, cp.name_first, cp.name_last, cp.name_middle, cp.suffix, max_acc.accreditation_nr,
           cpl.is_housecase_surgeon, cpl.is_housecase_anesth, cpl.is_housecase_attdr  
           FROM care_personell cpl LEFT JOIN (SELECT sda.dr_nr, sda.accreditation_nr, MAX(sda.create_dt) AS create_dt 
           FROM seg_dr_accreditation sda GROUP BY sda.dr_nr) AS max_acc ON max_acc.dr_nr = cpl.nr 
           INNER JOIN care_person cp ON cp.pid = cpl.pid WHERE " . $filter;

        // $orderby = " ORDER BY cpl.is_housecase_anesthsurgeon DESC, cpl.is_housecase_anesth DESC, cpl.is_housecase_attdr DESC";

        $result = $db->Execute($strSQL);

        return $result;

    }

    /**
     * Select values from global config for dynamic values
     * @author Jeff Ponteras 03-1620-18
     * @return int OPD-dept values
     */
    function getHCIRepresentative($type='') {
        global $db;

        if($type)
            $filter = $type;
        else $filter = 'eclaims_inCharge';

        $strSQL = "SELECT ccg.type, ccg.value FROM care_config_global AS ccg
                         WHERE ccg.type = '$filter'";

        $result = $db->Execute($strSQL);
        $row_index = $result->FetchRow();
        $value = $row_index['value'];
        $value_new = explode(',', $value);

        return $value_new;

    }

    function getCreateDate() {
        global $db;

        $sql = "SELECT 
                    create_time
                FROM
                  `care_config_global` AS ccg
                WHERE ccg.`type` =  ".$db->qstr(NEW_HOUSE_DOCTOR);

        $rs = $db->Execute($sql);
        $result = $rs->FetchRow();
        return $result['create_time'];
    }

    function getOpdAsuResult(){
        $obj_global = new GlobalConfig();
        $opdValue = $obj_global->getOpdAsu();
        return $opdValue;
    }

     //encounter info
    $strSQL = "SELECT 
                  ce.admission_dt AS DateAdmitted,
                  bill.bill_dte AS DateDischarged,
                  ce.encounter_date,
                  ce.encounter_type,
                  ce.is_discharged,
                  p.`death_encounter_nr`,
                  p.`death_date`
                FROM
                  care_encounter ce 
                LEFT JOIN
                    seg_billing_encounter bill
                    ON bill.encounter_nr = ce.encounter_nr 
                    AND bill.is_final = 1
                    AND (bill.is_deleted IS NULL OR bill.is_deleted = 0)
                LEFT JOIN care_person p 
                    ON p.`pid` = ce.`pid` 
                WHERE ce.encounter_nr = '$enc_no'
                ORDER BY bill.bill_dte DESC
                ";

    $result = $db->Execute($strSQL);
    $encounter = $result->FetchRow();

    //member info
    $strSQL = "SELECT seim.member_fname AS member_fname, 
                      seim.member_lname AS member_lname,
                      seim.member_mname AS member_mname,
                      seim.suffix AS member_suffix,
                      seim.birth_date AS member_bday,
                      seim.insurance_nr AS PIN,
                      seim.relation, 
                      seim.employer_no,
                      seim.employer_name,
                      seim.patient_pin
               FROM seg_encounter_insurance_memberinfo seim
               WHERE seim.encounter_nr = '$enc_no' AND seim.hcare_id = '18'";

    $result = $db->Execute($strSQL);
    $member = $result->FetchRow();

    $pattern = array('/[a-zA-Z]/', '/[ -]+/', '/^-|-$/');
    $pin = preg_replace($pattern, '', $member['PIN']);
    $patient_pin = preg_replace($pattern, '', $member['patient_pin']);
    $params->put("member_pin", $pin);
    $params->put("patient_pin", $patient_pin);

    if($encType == OUTPATIENT) {
        $encounterEHR = array(
                "encounter_nr"  =>  $enc_no,
        );

        $dataEhr = $ehr->billing_getRepetitivSession($encounterEHR);
        $result = $dataEhr->status;
        $arr_start = array();
        $arr_end = array();
        $counterFirst = 0;
        $counterSecond = 0;

        foreach ($result as $key => $reps) {
            array_push(
                    $arr_start,
                    date('Y-m-d', strtotime($reps->session_start_date)).' '.date(
                            'h:i:s',
                            strtotime($reps->session_start_time)
                    )
            );

            array_push(
                    $arr_end,
                    date('Y-m-d', strtotime($reps->session_end_date)).' '.date(
                            'h:i:s',
                            strtotime($reps->session_end_time)
                    )
            );

            if($reps->rvs_code == $caseFirstRate) {
                $counterFirst++;
            }

            if($reps->rvs_code == $caseSecondRate) {
                $counterSecond++;
            }
        }

        $start = min($arr_start);
        $end = max($arr_start);
        $encType = $encounter['encounter_type'];
        $phic = $pin;
        
        if($counterFirst > 1) {
            if ($phic) {
                $dateStart = $start;
                $dateEnd = $end;
            } else {
                $dateStart = is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted'];
                $dateEnd = $encounter['DateDischarged'] ? $encounter['DateDischarged'] : "";
            }
        }else if($counterSecond > 1) {
            if ($phic) {
                $dateStart = $start;
                $dateEnd = $end;
            } else {
                $dateStart = is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted'];
                $dateEnd = $encounter['DateDischarged'] ? $encounter['DateDischarged'] : "";
            }
        }else {
            $dateStart = is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted'];
            $dateEnd = $encounter['DateDischarged'] ? $encounter['DateDischarged'] : "";
        }
    } else {
        $dateStart = is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted'];
        $dateEnd = $encounter['DateDischarged'] ? $encounter['DateDischarged'] : "";
    }

    $params->put("date_admitted", $dateStart);
    // $params->put("date_discharged", is_null($encounter['DateDischarged']) ? $bill_date : $encounter['DateDischarged']);
    // $params->put("date_discharged", $bill_date);

    # Mod by jeff 01-06-18 for proper fetching of discharged date.
    
    $bill_date = $dateEnd;
    $bill_date = ($encounter['death_encounter_nr'] == $enc_no) ? $encounter['death_date'] : $bill_date;
    $params->put("date_discharged", ($encounter['is_discharged'] == 1 || ($encounter['is_discharged'] == 0 && ($encounter['encounter_type'] != 3 || $encounter['encounter_type'] != 4 || $encounter['encounter_type'] != 13))) ? $bill_date : '');

    # Added BarCode by Encounter - jeff 04/11/18
    $params->put("enc_nr", $enc_no);


    // Added by Mugshot 02-06-2019
    $hci_rep = getHCIRepresentative();
    // var_dump($hci_rep);die;
    if($bill_date < $hci_rep[8] && $bill_date != NULL){
        $params->put("hci_rep", $hci_rep[3].", ".$hci_rep[4].", ".$hci_rep[5]);
        $params->put("hci_rep_position", $hci_rep[6]);
    }else{
        if ( $bill_date == "" && $admission_date >= $hci_rep[7]) // October 1, 2018 onwards Admission + Unbilled case
        {
          $params->put("hci_rep", $hci_rep[0].", ".$hci_rep[1]);
          $params->put("hci_rep_position", $hci_rep[2]);
        } elseif ( $bill_date == "" && $admission_date < $hci_rep[7] ) // Before October 1, 2018 Admission + Unbilled case
        {
          $params->put("hci_rep", $hci_rep[0].", ".$hci_rep[1]);
          $params->put("hci_rep_position", $hci_rep[2]);
        } elseif ( $bill_date >= $hci_rep[7] && $admission_date >= $hci_rep[7] ) // October 1, 2018 onwards Admission + Billed on or after the date of effectivity
        {
          $params->put("hci_rep", $hci_rep[0].", ".$hci_rep[1]);
          $params->put("hci_rep_position", $hci_rep[2]);
        } elseif ( $bill_date < $hci_rep[7] && $admission_date >= $hci_rep[7] ) // October 1, 2018 onwards Admission + Billed before the date of effectivity
        {
          $params->put("hci_rep", $hci_rep[3].", ".$hci_rep[4].", ".$hci_rep[5]);
          $params->put("hci_rep_position", $hci_rep[6]);
        } elseif ( $bill_date >= $hci_rep[7] && $admission_date < $hci_rep[7] ) // Before October 1, 2018 Admission + Billed on or after the date of effectivity
        {
          $params->put("hci_rep", $hci_rep[0].", ".$hci_rep[1]);
          $params->put("hci_rep_position", $hci_rep[2]);
        } elseif ( $bill_date < $hci_rep[7] && $admission_date < $hci_rep[7] ) // Before October 1, 2018 Admission + Billed before the date of effectivity
        {
          $params->put("hci_rep", $hci_rep[3].", ".$hci_rep[4].", ".$hci_rep[5]);
          $params->put("hci_rep_position", $hci_rep[6]);
        }
    }
    

    #condtion in JRXML (Dependent PIN). Removed due to Eclaims limitation- No Web-service of Dependent PIN 
    // $P{member_type}!='M'&& $P{member_pin}!=""? $P{member_pin}.charAt(0):'0

    // $params->put("member_lname", utf8_decode(strtoupper($member['member_lname'])));
    // $params->put("member_fname", utf8_decode(strtoupper($member['member_fname'])));
    // $params->put("member_mname", utf8_decode(strtoupper($member['member_mname'])));
    $params->put("member_lname", utf8_decode(trim(mb_strtoupper($member['member_lname']))));
    $params->put("member_fname", utf8_decode(trim(mb_strtoupper($member['member_fname']))));
    $params->put("member_mname", utf8_decode(trim(mb_strtoupper($member['member_mname'])) == '.'? '' : utf8_decode(trim($member['member_mname']))));
    $params->put("member_suffix", strtoupper($member['member_suffix']));
    $params->put("member_bday", $member['member_bday']);
    $params->put("member_type",$member['relation']);

    $m_initial = strtoupper(substr($member['member_mname'], 0, 1)).".";

    $member_cert_name = mb_strtoupper($member['member_lname'] . ", " . $member['member_fname'] . " " .
               (is_null($member['member_suffix']) || $member['member_suffix'] == "" ? "" : $member['member_suffix']) .
               " " . ( $m_initial == '.' ? '' : $m_initial) );

    $params->put("member_cert_name",utf8_decode(trim($member_cert_name)));

    if($member['relation']!='M')
    {
        $params->put("name_last", utf8_decode(trim(mb_strtoupper($patient['LastName']))));
        $params->put("name_first", utf8_decode(trim(mb_strtoupper($patient['FirstName']))));
        $params->put("name_middle", utf8_decode(trim(mb_strtoupper($patient['MiddleName']))));
        $params->put("name_suffix", utf8_decode(trim(strtoupper($patient['Suffix']))));
        $params->put("birth_date", $patient['Bday']);
    }
    elseif ($member['relation']='M') 
    {
        $params->put("name_last", utf8_decode(trim(mb_strtoupper($member['member_lname']))));
        $params->put("name_first", utf8_decode(trim(mb_strtoupper($member['member_fname']))));
        $params->put("name_middle", utf8_decode(trim(mb_strtoupper($member['member_mname'])) == '.'? '' : $member['member_mname']));
        $params->put("name_suffix", utf8_decode(trim(strtoupper($member['member_suffix']))));
        $params->put("birth_date", $member['member_bday']);
    }
    
    //employer info
    $employer_no = preg_replace($pattern, '', $member['employer_no']);
    $employer_name = $member['employer_name'];
    if (strlen($employer_no) < 12) {
        $employer_no = "";
        $employer_name = "";
    }

    $params->put("employer_no", $employer_no);
    // $params->put("employer_name", utf8_decode(strtoupper($employer_name)));
    $params->put("employer_name", utf8_decode(trim(mb_strtoupper($employer_name))));
    $params->put("relation", $member['relation']);

    //thanks Michelle
    $baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );
    $logo_path = $baseurl.'images/phic_logo.png';
    $params->put("logo_path", $logo_path);
    // var_dump($root_path); die;
    // $data[0]['test'] = 'asdf';

    