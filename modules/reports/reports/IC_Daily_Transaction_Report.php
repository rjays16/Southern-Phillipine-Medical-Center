<?php
/**
 * added by art 06/27/2014
 * IC Daily Transaction Report
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
include('parameters.php');
$objIC = new SegICTransaction();

#hospital details -------------------------------------------------
$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
    $row['hosp_name']    = "DAVAO MEDICAL CENTER";
    $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}
#end hospital details --------------------------------------------

#title -----------------------------------------------------------
$title = strtoupper('Daily Transactions');
$title_department = strtoupper('HEALTH SERVICES AND SPECIALTY CLINIC (HSSC)');
$params->put("hosp_country", $row['hosp_country']);
$params->put("hosp_agency",  $row['hosp_agency']);
$params->put("hosp_name",    $row['hosp_name']);
$params->put("hosp_addr1",   $row['hosp_addr1']);
$params->put("title",        $title);
$params->put("title_department", $title_department);
#end title -------------------------------------------------------

$param = array($from_date_format,$to_date_format);

$sql = $db->Prepare("SELECT distinct cp.pid, cd.name_formal,
                            CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname,
                            CAST(encounter_date as DATE) as consult_date,
                            CAST(encounter_date AS TIME) AS consult_time,
                            fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age,
                            UPPER(sex) AS p_sex, addr_str, cd.id,
                            cp.street_name, sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name, ce.encounter_nr,
                            fn_get_icd_encounter(ce.encounter_nr) AS icd_code,
                            fn_get_personell_name(fn_get_icd_dr_encounter(ce.encounter_nr)) AS diagnosing_clinician,
                            IF(sip.name != 'Others', sip.name, (SELECT `name` FROM `seg_industrial_purpose_others` WHERE `refno` = sit.refno)) as purpose_exam,
                            sic.name as company_name,
                            sit.refno as refno,
                            -- fn_get_icd_diagnosis(ce.encounter_nr) AS diagnosis
                            diag.diagnosis as diagnosis
                             FROM care_encounter AS ce
                            INNER JOIN care_person AS cp ON ce.pid = cp.pid
                            INNER JOIN seg_industrial_transaction as sit on sit.encounter_nr=ce.encounter_nr
                            INNER JOIN seg_industrial_purpose as sip on sit.purpose_exam=sip.id
                            LEFT JOIN seg_industrial_company as sic on sic.company_id=sit.agency_id
                            LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
                            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
                            LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
                            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                            LEFT JOIN seg_industrial_med_chart AS diag on diag.encounter_nr=ce.encounter_nr
                          WHERE DATE(ce.encounter_date) BETWEEN ? AND ?
                            AND ce.encounter_type IN (6)
                            AND ce.status NOT IN ('deleted','hidden','inactive','void')
                            #GROUP BY ce.pid
                            ORDER BY encounter_date ASC");

$rs = $db->Execute($sql,$param);
#echo $sql;
#exit();

$rowindex = 0;
$grand_total = 0;
$data = array();
if (is_object($rs)){
    while($row=$rs->FetchRow()){

        #address --------------------------------------------------------------
        if (trim($row['street_name'])){
            if (trim($row["brgy_name"])!="NOT PROVIDED")
                $street_name = trim($row['street_name']).", ";
            else
                $street_name = trim($row['street_name']).", ";
        }else{
            $street_name = "";
        }

        if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED"))
            $brgy_name = "";
        else
            $brgy_name  = trim($row["brgy_name"]).", ";

        if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED"))
            $mun_name = "";
        else{
            if ($brgy_name)
                $mun_name = trim($row["mun_name"]);
            else
                $mun_name = trim($row["mun_name"]);
        }

        if ((!(trim($row["prov_name"]))) || (trim($row["prov_name"])=="NOT PROVIDED"))
            $prov_name = "";
        else
            $prov_name = trim($row["prov_name"]);

        if(stristr(trim($row["mun_name"]), 'city') === FALSE){
            if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
                if ($prov_name!="NOT PROVIDED")
                    $prov_name = ", ".trim($prov_name);
                else
                    $prov_name = trim($prov_name);
            }else{
                $prov_name = "";
            }
        }else
            $prov_name = "";

        $addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);
        #end address -------------------------------------------------------

        #age ---------------------------------------------------------------
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
        }
        #end age -------------------------------------------------------
        //$dr_name = $objIC->getPhysician($row['refno']);
        $data[$rowindex]=array(
            'hrn'         => $row['pid'],
            'fullname'    => utf8_decode(trim(ucwords(strtolower($row['fullname'])))),
            'age'         => $age,
            'sex'         => $row['p_sex'],
            'date'        => date("m/d/Y",strtotime($row['consult_date'])).' '.date("h:i A",strtotime($row['consult_time'])),
            'address'     => utf8_decode(trim($addr)),
            'purpose'     => strtoupper($row['purpose_exam']),
            'company'     => strtoupper($row['company_name']),
            'icd'         => $row['icd_code'],
            'diagnosis'  =>utf8_decode($row['diagnosis']),
        );

        $rowindex++;
    }

}else{
    $data[0][''] = NULL;
}
