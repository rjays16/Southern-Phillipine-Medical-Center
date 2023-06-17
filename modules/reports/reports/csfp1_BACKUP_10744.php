<?php
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');

	$enc_no = $param['enc_no'];
    $pid = $param['pid'];
    $bill_date = $param['billdte'];

    //patient info
	$strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
					p.name_3 AS ThirdName, p.name_middle AS MiddleName, p.suffix as Suffix, p.date_birth as Bday
					FROM care_person AS p
					WHERE p.pid = '$pid'";

	$result = $db->Execute($strSQL);
    $patient = $result->FetchRow();

    // $params->put("name_last", utf8_decode(strtoupper($patient['LastName'])));
    // $params->put("name_first", utf8_decode(strtoupper($patient['FirstName'])));
    // $params->put("name_middle", utf8_decode(strtoupper($patient['MiddleName'])));
    $params->put("name_last", mb_strtoupper($patient['LastName']));
    $params->put("name_first", mb_strtoupper($patient['FirstName']));
    $params->put("name_middle", mb_strtoupper($patient['MiddleName']));
    $params->put("name_suffix", strtoupper($patient['Suffix']));
    $params->put("birth_date", $patient['Bday']);
    
    //encounter info
    $strSQL = "SELECT 
                  ce.admission_dt AS DateAdmitted,
                  ce.discharge_date AS DateDischarged,
                  ce.encounter_date,
                  ce.encounter_type,
                  ce.is_discharged 
                FROM
                  care_encounter ce 
                WHERE ce.encounter_nr = '$enc_no'";
               
	$result = $db->Execute($strSQL);
    $encounter = $result->FetchRow();		  

    $params->put("date_admitted", is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted']);
    // $params->put("date_discharged", is_null($encounter['DateDischarged']) ? $bill_date : $encounter['DateDischarged']);
    // $params->put("date_discharged", $bill_date);
<<<<<<< HEAD
=======

    # Mod by jeff 01-06-18 for proper fetching of discharged date.
    $params->put("date_discharged", ($encounter['is_discharged'] == 1 || ($encounter['is_discharged'] == 0 && ($encounter['encounter_type'] != 3 || $encounter['encounter_type'] != 4 || $encounter['encounter_type'] != 13))) ? $bill_date : '');
>>>>>>> origin/SPMC-1260-CSF-Discharged-Date

    // var_dump($encounter);die();
    # Mod by jeff 01-06-18 for proper fetching of discharged date.
    if ($encounter['is_discharged'] == 1) {
        $params->put("date_discharged", $bill_date);
    }else{
        $params->put("date_discharged", ($encounter['encounter_type'] != 3 && $encounter['encounter_type'] != 4 && $encounter['encounter_type'] != 13) ? $bill_date : '');
    }
    
    //member info
    $strSQL = "SELECT seim.member_fname AS member_fname, 
				      seim.member_lname AS member_lname,
				      seim.member_mname AS member_mname,
				      seim.suffix AS member_suffix,
				      seim.birth_date AS member_bday,
				      seim.insurance_nr AS PIN,
				      seim.relation, 
				      seim.employer_no,
				      seim.employer_name
			   FROM seg_encounter_insurance_memberinfo seim
			   WHERE seim.encounter_nr = '$enc_no' AND seim.hcare_id = '18'";

	$result = $db->Execute($strSQL);
    $member = $result->FetchRow();

    $pattern = array('/[a-zA-Z]/', '/[ -]+/', '/^-|-$/');
    $pin = preg_replace($pattern, '', $member['PIN']);

    $params->put("member_pin", $pin);
    // $params->put("member_lname", utf8_decode(strtoupper($member['member_lname'])));
    // $params->put("member_fname", utf8_decode(strtoupper($member['member_fname'])));
    // $params->put("member_mname", utf8_decode(strtoupper($member['member_mname'])));
    $params->put("member_lname", mb_strtoupper($member['member_lname']));
    $params->put("member_fname", mb_strtoupper($member['member_fname']));
    $params->put("member_mname", mb_strtoupper($member['member_mname']));
    $params->put("member_suffix", strtoupper($member['member_suffix']));
    $params->put("member_bday", $member['member_bday']);
    
    //employer info
    $employer_no = preg_replace($pattern, '', $member['employer_no']);
    $employer_name = $member['employer_name'];
    if (strlen($employer_no) < 12) {
    	$employer_no = "";
    	$employer_name = "";
   	}

    $params->put("employer_no", $employer_no);
    // $params->put("employer_name", utf8_decode(strtoupper($employer_name)));
    $params->put("employer_name", mb_strtoupper($employer_name));
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
