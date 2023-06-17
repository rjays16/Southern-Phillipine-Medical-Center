<?php
/**
 * @author Gervie 03/26/2016
 *
 * PDPU Assessment Tool (HTML Format)
 */
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once($root_path . 'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

global $db;

$enc_nr = $_GET['enc_nr'];

$sql = "SELECT
          ssp.*,
          IFNULL(ce.`admission_dt`, ce.`encounter_date`) AS date_admission,
          ce.`encounter_type`,
          ce.`pid`,
          ce.`er_opd_diagnosis`,
          fn_get_ward_name(ce.`current_ward_nr`) AS ward,
          fn_get_department_name(ce.`current_dept_nr`) AS department,
          CONCAT(el.`area_location`, ' - ', ell.`lobby_name`) AS er_location,
          cp.`name_last`,
          cp.`name_middle`,
          cp.`name_first`,
          fn_get_complete_address2(ce.`pid`) AS address2,
          fn_get_age(ce.`encounter_date`, cp.`date_birth`) AS age,
          cp.`sex`,
          rl.`religion_name`,
          cp.`date_birth`,
          cp.`place_birth`,
          ed.`educ_attain_name` AS education,
          IF(oc.`source_income_desc` = 'others', ssp.`other_occupation`, oc.`source_income_desc`) AS occupation,
          ht.`house_description`,
          srf.`refer_assessment`,
          srf.`refer_intervention`
        FROM
          seg_socserv_patient ssp
          INNER JOIN care_encounter ce ON ce.`encounter_nr` = ssp.`encounter_nr`
          INNER JOIN care_person cp ON cp.`pid` = ce.`pid`
          LEFT JOIN seg_religion rl ON rl.`religion_nr` = ssp.`religion`
          LEFT JOIN seg_educational_attainment ed ON ed.`educ_attain_nr` = ssp.`educational_attain`
          LEFT JOIN seg_source_income oc ON oc.`source_income_id` = ssp.`occupation`
          LEFT JOIN seg_social_house_type ht ON ht.`house_type_nr` = ssp.`house_type`
          LEFT JOIN seg_social_referrals srf ON srf.`encounter_nr` = ssp.`encounter_nr`
          LEFT JOIN seg_er_location el ON el.`location_id` = ce.`er_location`
          LEFT JOIN seg_er_lobby ell ON ell.`lobby_id` = ce.`er_location_lobby`
        WHERE ssp.`encounter_nr` = '{$enc_nr}'";

$result = $db->GetRow($sql);

$sql2 = "SELECT
           cg.`discountid`,
           seim.`member_type`,
           seim.`insurance_nr`,
           sei.`remarks`,
           cpi.`is_principal`
         FROM
           seg_charity_grants cg
           LEFT JOIN seg_discount sd ON sd.`discountid` = cg.`discountid`
           LEFT JOIN seg_encounter_insurance sei ON sei.`encounter_nr` = cg.`encounter_nr`
           LEFT JOIN seg_encounter_insurance_memberinfo seim ON seim.`encounter_nr` = cg.`encounter_nr`
           LEFT JOIN care_person_insurance cpi ON cpi.`pid` = seim.`pid`
         WHERE cg.`encounter_nr` = '{$enc_nr}'
         ORDER BY cg.`grant_dte` DESC
         LIMIT 1";

$result2 = $db->GetRow($sql2);

$sql3 = "SELECT * FROM care_encounter WHERE pid = '{$result['pid']}'";
$result3 = $db->Execute($sql3)->RecordCount();

$sql4 = "SELECT * FROM seg_social_patient_family spf WHERE spf.`encounter_nr` = '{$enc_nr}'";
$result4 = $db->Execute($sql4);

//---------------------------------------------------------------------------------------------//

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['HTTP_HOST'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);


$params = array(
    'r_doh' => $baseurl . "img/doh.png",
    'r_spmc' => $baseurl . "gui/img/logos/dmc_logo.jpg",
    'date_interview' => date('F d, Y', strtotime($result['date_interview'])),
    'date_admission' => date('F d, Y', strtotime($result['date_admission'])),
    'is_ward' => ($result['encounter_type'] == '3' || $result['encounter_type'] == '4') ? $result['ward'] : '',
    'is_opd' => ($result['encounter_type'] == '2') ? $result['department'] : '',
    'is_er' => ($result['encounter_type'] == '1') ? $result['er_location'] : '',
    'hrn' => $result['pid'],
    'mswd' => $result['mss_no'],
    'source_referral' => ucfirst(utf8_decode($result['source_referral'])),
    'address' => ucfirst(utf8_decode($result['info_agency'])),
    'tel_no' => $result['info_contact_no'],
    'lastname' => utf8_decode(trim(ucfirst($result['name_last']))),
    'firstname' => utf8_decode(trim(ucfirst($result['name_first']))),
    'middlename' => utf8_decode(trim(ucfirst($result['name_middle']))),
    'religion' => ucfirst($result['religion_name']),
    'contact_person' => ucfirst(utf8_decode($result['companion'])),
    'age' => $result['age'],
    'male' => ($result['sex'] == 'm') ? '<span style="color: red;"><b>M</b></span>' : 'M',
    'female' => ($result['sex'] == 'f') ? '<span style="color: red;"><b>F</b></span>' : 'F',
    'single' => ($result['status'] == 'single') ? '<span style="color: red;"><b>S</b></span>' : 'S',
    'cl' => ($result['status'] == 'child') ? '<span style="color: red;"><b>CL</b></span>' : 'CL',
    'married' => ($result['status'] == 'married') ? '<span style="color: red;"><b>M</b></span>' : 'M',
    'separated' => ($result['status'] == 'legal_separated' || $result['status'] == 'infact_separated') ? '<span style="color: red;"><b>SEP</b></span>' : 'SEP',
    'infact' => ($result['status'] == 'infact_separated') ? '<span style="color: red;"><b>In-fact</b></span>' : 'In-fact',
    'legal' => ($result['status'] == 'legal_separated') ? '<span style="color: red;"><b>Legal</b></span>' : 'Legal',
    'widow' => ($result['status'] == 'widowed') ? '<span style="color: red;"><b>W</b></span>' : 'W',
    'divorced' => ($result['status'] == 'divorced') ? '<span style="color: red;"><b>D</b></span>' : 'D',
    'permanent_address' => $result['address2'],
    'temporary_address' => utf8_decode($result['address']),
    'dob' => date('F d, Y', strtotime($result['date_birth'])),
    'pob' => ucfirst($result['place_birth']),
    'education' => ucfirst($result['education']),
    'occupation' => ucfirst(utf8_decode($result['occupation'])),
    'employer' => ucfirst(utf8_decode($result['employer'])),
    'income' => $result['monthly_income'],
    'per_capita' => $result['per_capita_income'],
    'owned' => ($result['house_type'] == '2') ? 'X' : '',
    'rent' => ($result['house_type'] == '3') ? 'X' : '',
    'shared' => ($result['house_type'] == '4') ? 'X' : '',
    'institution' => ($result['house_type'] == '6') ? 'X' : '',
    'homeless' => ($result['house_type'] == '7') ? 'X' : '',
    'owwa' => ($result2['member_type'] == 'NO') ? '<span style="color: red;"><b>OWWA</b></span>' : 'OWWA',
    'mem_pay' => ($result2['member_type'] == 'G' || $result2['member_type'] == 'S' || $result2['member_type'] == 'NS') ? '<span style="color: red;"><b>Paying</b></span>' : 'Paying',
    'mem_non' => (($result2['member_type'] == 'I' || $result2['member_type'] == 'PS' || $result2['member_type'] == 'HSM'
                  || $result2['member_type'] == 'SC' || $result2['member_type'] == 'K')
                  && $result2['is_principal'] == '1') ? '<span style="color: red;"><b>Non-Paying</b></span>' : 'Non-Paying',
    'dependent' => ($result2['is_principal'] == '0') ? '<span style="color: red;"><b>DEPENDENT</b></span>' : 'DEPENDENT',
    'new' => ($result2['insurance_nr'] == 'TEMP') ? '<span style="color: red;"><b>NEW</b></span>' : 'NEW',
    'indigent' => ($result2['remarks'] == '2' || $result2['remarks'] == '3') ? '<span style="color: red;"><b>INDIGENT</b></span>' : 'INDIGENT',
    'ipd' => ($result['encounter_type'] == '3' || $result['encounter_type'] == '4') ? '<span style="color: red;"><b>IPD</b></span>' : 'IPD',
    'opd' => ($result['encounter_type'] == '2') ? '<span style="color: red;"><b>OPD</b></span>' : 'OPD',
    'er' => ($result['encounter_type'] == '1') ? '<span style="color: red;"><b>ER</b></span>' : 'ER',
    'old_patient' => ($result3 > 1) ? '<span style="color: red;"><b>OLD Patient</b></span>' : 'OLD Patient',
    'new_patient' => ($result3 == 1) ? '<span style="color: red;"><b>NEW Patient</b></span>' : 'NEW Patient',
    'cases_forward' => 'Cases Forward',
    'closed_case' => 'Closed Case',
    'mswd_c1' => ($result2['discountid'] == 'C1') ? '<span style="color: red;"><b>C1</b></span>' : 'C1',
    'mswd_c2' => ($result2['discountid'] == 'C2') ? '<span style="color: red;"><b>C2</b></span>' : 'C2',
    'mswd_c3' => ($result2['discountid'] == 'C3') ? '<span style="color: red;"><b>C3</b></span>' : 'C3',
    'mswd_d' => ($result2['discountid'] == 'D' || $result2['discountid'] == 'SC' || $result2['discountid'] == 'Brgy'
                    || $result2['discountid'] == 'BHW' || $result2['discountid'] == 'BHW' || $result2['discountid'] == 'PWD'
                    || $result2['discountid'] == 'Indi' || $result2['discountid'] == 'OT') ? '<span style="color: red;"><b>D</b></span>' : 'D',
    'ot_sc' => ($result2['discountid'] == 'SC') ? '<span style="color: red;"><b>SENIOR CITIZEN</b></span>' : 'SENIOR CITIZEN',
    'ot_bo' => ($result2['discountid'] == 'Brgy') ? '<span style="color: red;"><b>BARANGAY OFFICIAL</b></span>' : 'BARANGAY OFFICIAL',
    'ot_bhw' => ($result2['discountid'] == 'BHW') ? '<span style="color: red;"><b>BHW</b></span>' : 'BHW',
    'ot_pwd' => ($result2['discountid'] == 'PWD') ? '<span style="color: red;"><b>PWD</b></span>' : 'PWD',
    'ot_indigent' => ($result2['discountid'] == 'Indi') ? '<span style="color: red;"><b>INDIGENOUS</b></span>' : 'INDIGENOUS',
    'ot_others' => ($result2['discountid'] != 'C1' && $result2['discountid'] != 'C2' && $result2['discountid'] != 'C3'
                    && $result2['discountid'] != 'D' && $result2['discountid'] != 'SC' && $result2['discountid'] != 'Brgy'
                    && $result2['discountid'] != 'BHW' && $result2['discountid'] != 'BHW' && $result2['discountid'] != 'PWD'
                    && $result2['discountid'] != 'Indi') ? '<span style="color: red;"><b>OTHERS</b></span>' : 'OTHERS',
    'informant' => ucfirst(utf8_decode($result['informant_name'])),
    'info_relation' => ucfirst(utf8_decode($result['relation_informant'])),
    'info_address' => ucfirst(utf8_decode($result['info_address'])),
    'income_source' => ucfirst(utf8_decode($result['source_income'])),
    'household_size' => $result['nr_dependents'],
    'house_lot' => ucfirst($result['house_description']),
    'l_electric' => ($result['light_source'] == 'EC') ? 'X' : '',
    'l_candle' => ($result['light_source'] == 'CN') ? 'X' : '',
    'l_kerosene' => ($result['light_source'] == 'KR') ? 'X' : '',
    'w_well' => ($result['water_source'] == 'WL') ? 'X' : '',
    'w_public' => ($result['water_source'] == 'PB') ? 'X' : '',
    'w_owned' => ($result['water_source'] == 'OD') ? 'X' : '',
    'w_district' => ($result['water_source'] == 'WD') ? 'X' : '',
    'f_gas' => ($result['fuel_source'] == 'GS') ? 'X' : '',
    'f_charcoal' => ($result['fuel_source'] == 'CH') ? 'X' : '',
    'f_firewood' => ($result['fuel_source'] == 'FW') ? 'X' : '',
    'o_food' => 'Php ' . (($result['food_expense'] == '') ? '0.00' : $result['food_expense']),
    'o_education' => 'Php ' . (($result['education_expense'] == '') ? '0.00' : $result['education_expense']),
    'o_clothing' => 'Php ' . (($result['clothing_expense'] == '') ? '0.00' : $result['clothing_expense']),
    'o_transportation' => 'Php ' . (($result['transport_expense'] == '') ? '0.00' : $result['transport_expense']),
    'o_househelp' => 'Php ' . (($result['househelp_expense'] == '') ? '0.00' : $result['househelp_expense']),
    'o_insurance' => 'Php ' . (($result['insurance_mortgage'] == '') ? '0.00' : $result['insurance_mortgage']),
    'o_others' => 'Php ' . (($result['other_expense'] == '') ? '0.00' : $result['other_expense']),
    'total_expenditure' => 'Php ' . (($result['total_monthly_expense'] == '') ? '0.00' : $result['total_monthly_expense']),
    'admitting_diagnosis' => ucfirst($result['er_opd_diagnosis']),
    'final_diagnosis' => ucfirst(utf8_decode($result['final_diagnosis'])),
    'duration_problem' => ucfirst(utf8_decode($result['duration_problem'])),
    'previous_treatment' => ucfirst(utf8_decode($result['duration_treatment'])),
    'present_treatment' => ucfirst(utf8_decode($result['treatment_plan'])),
    'health_access' => ucfirst(utf8_decode($result['accessibility_problem'])),
    'pdpu_assessment' => '',
    'intervention_remarks' => '',
    'pdpu_assessment' => ucfirst($result['refer_assessment']),
    'intervention_remarks' => ucfirst($result['refer_intervention'])
);

$data[0] = array(
    'f_name' => ucfirst($result['name_first'] . ' ' . $result['name_middle'] . ' ' . $result['name_last']),
    'f_age' => $result['age'],
    'f_status' => ucfirst($result['status']),
    'f_relation' => '(PATIENT)',
    'f_education' => ucfirst($result['education']),
    'f_occupation' => ucfirst(utf8_decode($result['occupation'])),
    'f_income' => 'Php ' . $result['income']
);

$i = 1;
if($result4) {
  if($result4->RecordCount() > 0) {
    while($row = $result4->FetchRow()) {
      $data[$i] = array(
          'f_name' => ucfirst(utf8_decode($row['dependent_name'])),
          'f_age' => $row['dependent_age'] . ' years',
          'f_status' => ucfirst($row['dependent_status']),
          'f_relation' => ucfirst($row['relation_to_patient']),
          'f_education' => ucfirst($row['dep_educ_attainment']),
          'f_occupation' => ucfirst(utf8_decode($row['dependent_occupation'])),
          'f_income' => 'Php ' . number_format($row['dep_monthly_income'], 2, '.', ',')
      );

      $i++;
    }
  }
}


showReport('PDPU_Assessment_Tool', $params, $data, 'HTML');