<?php
    	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');

	$enc_no = $param['enc_no'];
    $pid = $param['pid'];

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
  
    
    //encounter info
    $strSQL = "SELECT 
                    ce.admission_dt AS DateAdmitted,
                    bill.bill_dte AS BillDate,
                    ce.encounter_date,
                    ce.encounter_type,
                    ce.is_discharged,
                    p.`death_encounter_nr`,
                    CONCAT(p.`death_date`, ' ', p.`death_time`) AS DateDeath
                FROM
                    care_encounter ce 
                    LEFT JOIN seg_billing_encounter bill 
                    ON bill.encounter_nr = ce.encounter_nr 
                    AND bill.is_final = 1 
                    AND (
                        bill.is_deleted IS NULL 
                        OR bill.is_deleted = 0
                    ) 
                    LEFT JOIN care_person p 
                    ON p.`pid` = ce.`pid` 
                WHERE ce.encounter_nr = '$enc_no' 
                ORDER BY bill.bill_dte DESC
                ";

	$result = $db->Execute($strSQL);
    $encounter = $result->FetchRow();

    $params->put("date_admitted", is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted']);
    // $params->put("date_discharged", is_null($encounter['BillDate']) ? $bill_date : $encounter['BillDate']);
    // $params->put("date_discharged", $bill_date);

    // # Mod by jeff 01-06-18 for proper fetching of discharged date.
    
    // $bill_date = $encounter['BillDate'] ? $encounter['BillDate'] : "";
    // $bill_date = $encounter['death_encounter_nr'] ? $encounter['death_date'] : $bill_date;
    // $params->put("date_discharged", ($encounter['is_discharged'] == 1 || ($encounter['is_discharged'] == 0 && ($encounter['encounter_type'] != 3 || $encounter['encounter_type'] != 4 || $encounter['encounter_type'] != 13))) ? $bill_date : '');

    # Mod by JC 5-30-18 for proper fetching hierarchy of bill date, death date {

    $bill_date = $encounter['BillDate'] ? $encounter['BillDate'] : "";
    $bill_date = $encounter['death_encounter_nr'] === $enc_no ? $encounter['DateDeath'] : $bill_date;
    $params->put("date_discharged", ($encounter['encounter_type'] != 3 || $encounter['encounter_type'] != 4 || $encounter['encounter_type'] != 13) ? $bill_date : '');

    // var_dump($bill_date); die;
    # end JC }

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

    # Added BarCode by Encounter - jeff 04/11/18
    $params->put("enc_nr", $enc_no);

    #condtion in JRXML (Dependent PIN). Removed due to Eclaims limitation- No Web-service of Dependent PIN 
    // $P{member_type}!='M'&& $P{member_pin}!=""? $P{member_pin}.charAt(0):'0

    // $params->put("member_lname", utf8_decode(strtoupper($member['member_lname'])));
    // $params->put("member_fname", utf8_decode(strtoupper($member['member_fname'])));
    // $params->put("member_mname", utf8_decode(strtoupper($member['member_mname'])));
    $params->put("member_lname", mb_strtoupper($member['member_lname']));
    $params->put("member_fname", mb_strtoupper($member['member_fname']));
    $params->put("member_mname", mb_strtoupper($member['member_mname'] == '.'? '' : $member['member_mname']));
    $params->put("member_suffix", strtoupper($member['member_suffix']));
    $params->put("member_bday", $member['member_bday']);
    $params->put("member_type",$member['relation']);

    $member_cert_name = mb_strtoupper($member['member_lname'] . ", " . $member['member_fname'] . " " .
               (is_null($member['member_suffix']) || $member['member_suffix'] == "" ? "" : $member['member_suffix']) .
               " " . $member['member_mname']);

    $params->put("member_cert_name",$member_cert_name);

    if($member['relation']!='M')
    {
        $params->put("name_last", mb_strtoupper($patient['LastName']));
        $params->put("name_first", mb_strtoupper($patient['FirstName']));
        $params->put("name_middle", mb_strtoupper($patient['MiddleName']));
        $params->put("name_suffix", strtoupper($patient['Suffix']));
        $params->put("birth_date", $patient['Bday']);
    }
    elseif ($member['relation']='M') 
    {
        $params->put("name_last", mb_strtoupper($member['member_lname']));
        $params->put("name_first", mb_strtoupper($member['member_fname']));
        $params->put("name_middle", mb_strtoupper($member['member_mname'] == '.'? '' : $member['member_mname']));
        $params->put("name_suffix", strtoupper($member['member_suffix']));
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
