<?php

require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');

/**
 * Class that handles doctor's mobility integration
 * @author Justin Tan
 */
class DoctorsMobility extends Core
{

    const ENCOUNTER_TYPE_1 = 'ERE';
    const ENCOUNTER_TYPE_2 = 'OPE';
    const ENCOUNTER_TYPE_3 = 'IPE';
    const ENCOUNTER_TYPE_4 = 'IPE';

    var $sql;

    function getAddressInfo($brgy_nr, $mun_nr)
    {
        global $db;

        $this->sql = "SELECT 
            CASE WHEN $db->qstr($brgy_nr) = '0' THEN '0' ELSE sb.`code` END AS brgy_code,
              sm.`code` AS lgu_code,
              sr.`code` AS region_code,
              sp.`code` AS prov_code,
              sb.`brgy_name` AS brgy_name,
              sr.`region_name` AS region_name,
              sm.`mun_name` AS lgu_name,
              sp.`prov_name`,
              sm.`zipcode` 
            FROM
              seg_barangays AS sb 
              LEFT JOIN seg_municity AS sm 
                ON sm.`mun_nr` = sb.`mun_nr` 
              LEFT JOIN seg_provinces AS sp 
                ON sp.`prov_nr` = sm.`prov_nr` 
              LEFT JOIN seg_regions AS sr 
                ON sr.`region_nr` = sp.`region_nr` 
            WHERE CASE WHEN $db->qstr($brgy_nr) = '0' THEN sm.`mun_nr` = $db->qstr($mun_nr) 
                    ELSE (sb.`brgy_nr` = $db->qstr($brgy_nr) AND sm.`mun_nr` = $db->qstr($mun_nr)) END
            LIMIT 1";

        $row = $db->GetRow($this->sql);

        return $row;
    }

    function getDoctorInfo($personell_nr)
    {
        global $db;

        $this->sql = "SELECT cp.`nr` AS 'personell_id', cp.`pid`, cu.`login_id`, cu.`password`,
            cpa.`location_nr`, cd.`id`, cd.`name_formal`, cd.`parent_dept_nr`,
            icd.`id` AS 'parent__dept_id', icd.`name_formal` AS 'parent__dept_name'
            FROM care_personell cp
            LEFT JOIN care_personell_assignment cpa ON cp.`nr` = cpa.`personell_nr`
            LEFT JOIN care_users cu ON cu.`personell_nr` = cp.`nr`
            LEFT JOIN care_department cd ON cd.`nr` = cpa.`location_nr`
            LEFT JOIN care_department icd ON icd.`nr` = cd.`parent_dept_nr`
            WHERE cp.`nr` = " . $db->qstr($personell_nr);

        $row = $db->GetRow($this->sql);
        $dept = $this->getDeptInfo($personell_nr);
        $row['location_nr'] = $dept ? $dept['location_nr'] : '';
        $row['dept_name'] = $dept ? $dept['name_formal'] : '';
        return $row;
    }

    function getDeptInfo($personell_nr)
    {
        $personell = new Personell();
        return $personell->get_Dept_name($personell_nr);
    }

    function isDoctor($personell_nr)
    {
        global $db;

        $this->sql = "SELECT IF(SUBSTR(short_id,1,1)='D',1,0) AS isdoctor 
                FROM care_personell p 
                WHERE nr=" . $db->qstr($personell_nr);

        $row = $db->GetRow($this->sql);
        return $row['isdoctor'];
    }

    /**
     * simplified get encounter for emr
     * @global type $db
     * @param type $encNr
     * @return type
     */
    function getEncounterInfo($encNr)
    {
        global $db;
        $this->sql = "SELECT *, IF(encounter_type=1,'ER', IF(encounter_type=2,'OPD', 
            IF(encounter_type in (3,4),'IPD', ''))) as patient_type, er_opd_diagnosis, 
            current_dept_nr, consulting_dept_nr, pid, encounter_nr, chief_complaint
            FROM care_encounter WHERE encounter_nr = " . $db->qstr($encNr);
        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    /**
     * This will return department info find by departmentNr
     * @param $depNr
     * @return bool
     */
    function getDepartmentInfo($depNr)
    {
        global $db;
        $this->sql = "SELECT d.`nr`, d.`id`, d.`name_formal`, d.`parent_dept_nr`, 
            (SELECT dd.`name_formal` FROM care_department dd WHERE dd.`nr` = d.`parent_dept_nr`) AS parent_name
            FROM care_department d
            WHERE d.`nr` = " . $db->qstr($depNr);
        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    function getLaboratoryInfo($refno){
        global $db;
        $this->sql = "SELECT ls.`pid` AS 'pid', ls.`encounter_nr` AS 'enc_nr', ls.`refno` AS 'refno',
                        serv.`group_code` AS 'group_code', lg.`name` AS 'group_name',
                        lsd.`service_code` AS 'service_code', serv.`name` AS 'service_name',
                        lsd.`request_doctor` AS 'doctor', ls.`ref_source`, lsd.`quantity`
                        FROM seg_lab_serv AS ls
                        INNER JOIN care_encounter AS enc ON enc.`encounter_nr` = ls.`encounter_nr`
                        INNER JOIN seg_lab_servdetails AS lsd ON lsd.`refno` = ls.`refno`
                        INNER JOIN seg_lab_services AS serv ON serv.`service_code` = lsd.`service_code`
                        INNER JOIN seg_lab_service_groups AS lg ON lg.`group_code` = serv.`group_code`
                        WHERE lsd.`status` <> 'deleted' AND ls.`refno` = ". $db->qstr($refno);

        $row = $db->GetAll($this->sql);
        return empty($row) ? false : $row;
    }

    function getRadiologyInfo($refno){
        global $db;
        $this->sql = "SELECT rs.`pid` AS 'pid', rs.`encounter_nr` AS 'enc_nr', 
            rs.`is_cash` AS 'is_cash', rs.`refno` AS 'refno', 
            trr.`clinical_info` AS 'clinical_info', rs.`is_urgent` AS 'is_urgent', 
            serv.`group_code` AS 'group_code', srsg.`name` AS 'group_name',
            trr.`service_code` AS 'service_code', serv.`name` AS 'service_name',
            trr.`request_doctor` AS 'doctor', trr.`batch_nr` AS 'batchNo'
            FROM seg_radio_serv rs
            INNER JOIN care_test_request_radio trr ON trr.`refno` = rs.`refno`
            INNER JOIN seg_radio_services serv ON serv.`service_code` = trr.`service_code`
            INNER JOIN seg_radio_service_groups srsg ON serv.`group_code` = srsg.`group_code`
            WHERE trr.`status` <> 'deleted' AND rs.`refno` = ". $db->qstr($refno);
        
        $row = $db->GetAll($this->sql);
        return empty($row) ? false : $row;
    }

    function getPharmacyInfo($refno){
        global $db;
        $this->sql = "SELECT spo.`refno`, spo.`pid`, 
            spo.`encounter_nr`, spoi.`quantity`, 
            spoi.`bestellnum`, cppm.`artikelname`,
            cppm.`generic`, ce.`current_att_dr_nr` as 'doctor',
            cppm.`prod_class`
            FROM seg_pharma_orders spo
            INNER JOIN seg_pharma_order_items spoi ON spo.`refno` = spoi.`refno`
            INNER JOIN care_pharma_products_main cppm ON cppm.`bestellnum` = spoi.`bestellnum`
            LEFT JOIN care_encounter ce ON spo.`encounter_nr` = ce.`encounter_nr`
            WHERE spo.`refno` = ". $db->qstr($refno);

        $row = $db->GetAll($this->sql);
        return empty($row) ? false : $row;
    }

    function getMiscInfo($refno){
        global $db;
        $this->sql = "SELECT sms.`refno`, sms.`encounter_nr`, sms.`chrge_dte`,
            smsd.`service_code`, sos.`name`, smsd.`quantity`, sms.`is_cash`, 
            ce.`current_att_dr_nr` as 'doctor'
            FROM seg_misc_service sms
            INNER JOIN seg_misc_service_details smsd ON smsd.`refno` = sms.`refno`
            INNER JOIN seg_other_services sos ON sos.`alt_service_code` = smsd.`service_code`
            INNER JOIN care_encounter ce ON ce.`encounter_nr` = sms.`encounter_nr`
            WHERE sms.`refno` = ". $db->qstr($refno);

        $row = $db->GetAll($this->sql);
        return empty($row) ? false : $row;
    }

    function getRadResultInfo($batchNr, $manual){
        global $db;
        if($manual)
            $this->sql = "SELECT ctrr.`batch_nr`, ctrr.`refno`, ctrr.`service_code`
                FROM care_test_request_radio AS ctrr
                INNER JOIN care_test_findings_radio AS ctfr ON ctfr.`batch_nr` = ctrr.`batch_nr`
                WHERE ctrr.`batch_nr` = ".$db->qstr($batchNr)." LIMIT 1";
        else
            $this->sql = "SELECT t.`pacs_order_no` AS 'batch_nr', ctrr.`refno`, ctrr.`service_code`
                FROM seg_hl7_radio_msg_receipt AS t
                INNER JOIN care_test_findings_radio AS ctfr ON ctfr.`batch_nr` = t.`pacs_order_no`
                INNER JOIN care_test_request_radio AS ctrr ON ctrr.`batch_nr` = t.`pacs_order_no`
                WHERE t.`pacs_order_no` = ".$db->qstr($batchNr)." AND t.`msg_type_id` = 'ORU' LIMIT 1";

        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    function getRadResultDataArray($dataarr){
        $data = array(
            'batchNr' => $dataarr['batch_nr'],
            'refno' => $dataarr['refno'],
            'service_id' => $dataarr['service_code']
        );

        return $data;
    }

    function getLaboratoryInfoByLisno($lis_order_no){
        global $db;
        $this->sql = "SELECT msg.`lis_order_no`, map.`refno`, service.`service_code`
            FROM seg_hl7_hclab_msg_receipt AS msg
            LEFT JOIN seg_lab_hclab_orderno AS map ON map.`lis_order_no` = msg.`lis_order_no`
            LEFT JOIN seg_lab_services AS service 
                ON (service.`service_code` = msg.`test` OR service.`oservice_code` = msg.`test` 
                    OR service.`ipdservice_code` = msg.`test` OR service.`erservice_code` = msg.`test`)
            WHERE msg.`lis_order_no` = ".$db->qstr($lis_order_no)." AND msg.`msg_type_id` = 'ORU'";

        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    function getDischargeInfoByEncNo($encounter_nr){
        global $db;
        $this->sql = "SELECT encounter_nr, discharge_date, discharge_time
            FROM care_encounter
            WHERE encounter_nr = ".$db->qstr($encounter_nr)." AND is_discharged = '1'";

        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    function getReferralInfoByPk($referral_nr){
        global $db;
        $this->sql = "SELECT r.`referral_nr`, r.`encounter_nr`, r.`referrer_dept`,
            r.`reason_referral_nr`, r.`referral_date`, r.`referrer_dr`, rr.`reason`,
            d.`id` AS 'area_code', d.`name_formal`, d.`parent_dept_nr`,
            (SELECT cd.`name_formal` FROM care_department cd 
                WHERE cd.`nr` = d.`parent_dept_nr`) AS 'parent_dept_name'
            FROM seg_referral r
            INNER JOIN seg_referral_reason rr ON r.`reason_referral_nr` = rr.`id`
            INNER JOIN care_department d ON r.`referrer_dept` = d.`nr`
            WHERE referral_nr = ". $db->qstr($referral_nr);

        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    function getReferralDataArray($dataarr){
        $data = array(
            "referral_nr" => $dataarr['referral_nr'],
            "encounter_nr" => $dataarr['encounter_nr'],
            "referrer_dept" => $dataarr['referrer_dept'],
            "reason_referral_nr" => $dataarr['reason_referral_nr'],
            "referral_date" => $dataarr['referral_date'],
            "referrer_dr" => $dataarr['referrer_dr'],
            "reason" => $dataarr['reason'],
            "area_code" => $dataarr['area_code'],
            "name_formal" => $dataarr['name_formal'],
            "parent_dept_nr" => $dataarr['parent_dept_nr'],
            "parent_dept_name" => $dataarr['parent_dept_name'],
        );

        return $data;
    }

    function getDischargeDataArray($dataarr){
        $data = array(
            "encounter_nr" => $dataarr['encounter_nr'],
            "discharge_date" => $dataarr['discharge_date'],
            "discharge_time" => $dataarr['discharge_time'],
        );

        return $data;
    }

    function getLabResultDataArray($dataarr){
        $data = array(
            "lis_order_no" => $dataarr['lis_order_no'],
            "refno" => $dataarr['refno'],
            "service_code" => $dataarr['service_code'],
        );

        return $data;
    }

    function getMiscDataArray($dataarr, $refno){
        $items = array();
        foreach ($dataarr as $key => $value) {
            $items[] = array(
                "service_code" => $value['service_code'],
                "name" => $value['name'],
                "quantity" => $value['quantity']
            );
        }

        $data = array(
            "order" => array(
                "encounter_no" => $dataarr[0]['encounter_nr'],
                "refno" => $refno,
                "is_cash" => $dataarr[0]['is_cash'],
                "charge_dt" => $dataarr[0]['chrge_dte'],
                "doctor_id" => $dataarr[0]['doctor'],
                "login_username" => $_SESSION['sess_login_username'],
                "login_personell_nr" => $_SESSION['sess_login_personell_nr']
            ),
            "request" => array("data" => $items)
        );

        return $data;
    }

    function getPharmaDataArray($dataarr){
        $items = array();
        foreach ($dataarr as $key => $value) {
            $items[] = array(
                "bestellnum" => $value['bestellnum'],
                "artikelname" => $value['artikelname'],
                'prod_class' => $value['prod_class'],
                "generic" => $value['generic'],
                "quantity" => $value['quantity']
            );
        }

        $data = array(
            "order" => array(
                "encounter_no" => $dataarr[0]['encounter_nr'],
                "spin" => $dataarr[0]['pid'],
                "refno" => $dataarr[0]['refno'],
                "doctor_id" => $dataarr[0]['doctor'],
                "login_username" => $_SESSION['sess_login_username'],
                "login_personell_nr" => $_SESSION['sess_login_personell_nr']
            ),
            "request" => array("data" => $items)
        );

        return $data;
    }

    function getRadDataArray($dataarr){
        $items = array();
        foreach ($dataarr as $key => $value) {
            $items[] = array(
                "id" => $value['batchNo'],
                "service_id" => $value['service_code'],
                "service_name" => $value['service_name'],
                "group_id" => $value['group_code'],
                "group_name" => $value['group_name']
            );
        }

        $data = array(
            "order" => array(
                "encounter_no" => $dataarr[0]['enc_nr'],
                "spin" => $dataarr[0]['pid'],
                "refno" => $dataarr[0]['refno'],
                "doctor_id" => $dataarr[0]['doctor'],
                "login_username" => $_SESSION['sess_login_username'],
                "login_personell_nr" => $_SESSION['sess_login_personell_nr']
            ),
            "request" => array("data" => $items)
        );

        return $data;
    }

    function getLabdataArray($dataarr){
        $items = array();
        foreach ($dataarr as $key => $value) {
            $items[] = array(
                "service_id" => $value['service_code'],
                "service_name" => $value['service_name'],
                "group_id" => $value['group_code'],
                "group_name" => $value['group_name'],
                "quantity" => $value['quantity']
            );
        }

        $data = array(
            "order" => array(
                "encounter_no" => $dataarr[0]['enc_nr'],
                "spin" => $dataarr[0]['pid'],
                "refno" => $dataarr[0]['refno'],
                "doctor_id" => $dataarr[0]['doctor'],
                'ref_source' => $dataarr[0]['ref_source'],
                "login_username" => $_SESSION['sess_login_username'],
                "login_personell_nr" => $_SESSION['sess_login_personell_nr']
            ),
            "request" => array("data" => $items)
        );

        return $data;
    }

    function getPatientdataArray($dataarr)
    {

        extract($dataarr);

        $mother = $mother_fname . (isset($mother_maidenname) ? ' ' . $mother_maidenname : '') .
                (isset($mother_mname) ? ' ' . $mother_mname : '') .
                (isset($mother_lname) ? ' ' . $mother_lname : '');

        $father = $father_fname . (isset($father_mname) ? ' ' . $father_mname : '') .
                (isset($father_lname) ? ' ' . $father_lname : '');

        $name_last = $name_last . (isset($suffix) ? ', ' . $suffix : '');

        $row_addr = $this->getAddressInfo($brgy_nr, $mun_nr);

        $street_name = $street_name . (isset($row_addr['brgy_name']) ? ', ' . $row_addr['brgy_name'] : '');

        $data = array(
            "FirstName" => $name_first,
            "MiddleName" => $name_middle,
            "LastName" => $name_last,
            "Gender" => strtoupper($sex),
            "MaidenLastName" => ($name_maiden ? $name_maiden : ''),
            "Title" => ($title ? $title : ''),
            "DateOfBirth" => date('m/d/Y', strtotime($date_birth)),
            "SecurityPin" => "",
            "RegistrationNotes" => "",
            "ClinicalNotes" => "",
            "EmergencyContactPhone" => "",
            "EmergencyContactName" => "",
            "SocialSecurityNumber" => "",
            "HISRegistrationDate" => $date_reg,
            "HISId" => $pid,
            "Street1" => ((trim($street_name)) ? trim($street_name) : ''),
            "Street2" => "",
            "City" => trim($row_addr['mun_name']),
            "Province" => trim($row_addr['prov_name']),
            "Country" => trim($row_addr['country_name']),
            "ZipCode" => trim($row_addr['zipcode']),
            "Email" => ($email ? $email : ''),
            "MotherName" => ($mother ? $mother : ''),
            "FatherName" => ($father ? $father : ''),
            "SpouseName" => ($spouse_name ? $spouse_name : ''),
            "HomePhone" => ($phone_1_nr ? $phone_1_nr : ''),
            "CellPhone" => ($cellphone_1_nr ? $cellphone_1_nr : ''),
            "WorkPhone" => ($phone_2_nr ? $phone_2_nr : ''),
            "SerialNumber" => "",
            "CompanyName" => ($employer ? $employer : ''),
            "CompanyAddressLine1" => "",
            "CompanyAddressLine2" => "",
            "CompanyCity" => "",
            "CompanyProvince" => "",
            "CompanyCountry" => "",
            "CompanyPostalCode" => "",
            "CompanyPhoneNumber" => "",
            "CompanyFaxNumber" => "",
            "GroupName" => "",
            "CarrierName" => "",
            "PlanCode" => "",
            "Copay" => "",
            "Status" => "",
            "GuarantorName" => "",
            "GuarantorDateOfBirth" => "",
            "PayerNotes" => "",
        );

        return $data;
    }

    function getParentDepartment($parentDepNr)
    {
        global $db;
        $this->sql = "
            SELECT id FROM care_department WHERE nr = " . $db->qstr($parentDepNr);
        $row = $db->GetOne($this->sql);
        return empty($row) ? false : $row;
    }

    function getDoctordataArray($dataarr)
    {
        extract($dataarr);

        if(empty($parent_dept_nr)){
            $area = array(
                'area_id'   => $location_nr,
                'area_code' => $id,
                'area_desc' => $name_formal,
                'dept_id'   => $location_nr,
            );
            $dept = array(
                'dept_id'   => $location_nr,
                'dept_code' => $id,
                'dept_name' => $name_formal
            );
        }else{
            $area = array(
                'area_id'   => $location_nr,
                'area_code' => $id,
                'area_desc' => $name_formal,
                'dept_id'   => $parent_dept_nr,
            );
            $dept = array(
                'dept_id'   => $parent_dept_nr,
                'dept_code' => $parent_dept_id,
                'dept_name' => $parent_dept_name
            );
        }

        $data = array(
            "personnel" => array(
                'personnel_id'      => $personell_id,
                'pid'               => $pid,
                'area_id'           => $location_nr
            ),
            "area" => $area,
            "dept" => $dept,
            "user" => array(
                'username'          => $login_id,
                'password'          => $password,
                'personnel_id'      => $personell_id,
                'default_authitem'  => 'his_doctor'
            ),
        );

        return $data;
    }

    function getEncounterdataArray($dataarr)
    {

        extract($dataarr);

        $data = array(
            "CaseNumber" => $encounter_nr,
            "PatientId" => $pid,
            "DepartmentId" => $dept_nr,
            "PatientType" => $patient_type,
            "CaseDescription" => $er_opd_diagnosis,
        );

        return $data;
    }

    /**
     * Returns personel info
     * @param $pid
     */
    function getPersonnelInfoByPid($pid)
    {
        global $db;
        $this->sql = "
            SELECT * FROM care_personell WHERE pid = " . $db->qstr($pid);
        $row = $db->GetRow($this->sql);
        #return empty($row) ? false : $row;
        return $row;
    }

    /**
     * Get Personel Info By `nr`
     * @param $nr
     */
    function getPersonnelInfoByNr($nr){
        global $db;
        $this->sql="SELECT cpa.location_nr, dept.name_formal AS dept_name,
                                                        dept.nr AS dept_nr,
                                                        ps.*,p.*,
                                                        c.funk1,
                                                        c.funk2,
                                                        c.inphone1,
                                                        c.inphone2,
                                                        c.inphone3
                                FROM care_personell AS ps
                                                        LEFT JOIN care_personell_assignment AS cpa ON cpa.personell_nr=ps.nr
                                                                LEFT JOIN care_department AS dept ON dept.nr=cpa.location_nr,
                                                care_person AS p LEFT JOIN
                                                care_phone AS c ON c.personell_nr='$nr'
                                WHERE ps.nr='$nr'
                                 AND ps.pid=p.pid";


        if ($this->result=$db->Execute($this->sql)) {
            if ($this->record_count=$this->result->RecordCount()) {
                return $this->result->FetchRow();
            } else {
                return FALSE;
            }
        }else {
            return FALSE;
        }
    }

    /**
     * This will return `department` objects
     * @param $data
     * @return array
     */
    public function formatSpecialties($data)
    {
        $departmentData = $data;
        $parentDepartmentData = $this->getDepartmentInfo($departmentData['parent_dept_nr']);

        $departmentDataItems = array(
            'nr'          => 'dept_id',
            'id'          => 'dept_code',
            'name_formal' => 'dept_name'
        );

        $specialtyDataItems = array(
            'nr'          => 'specialty_code',
            'name_formal' => 'specialty_name',
            'description' => 'specialty_desc',
            //'id' => 'parent_code'
        );

        $departmentObject = array();
        $specialtyObject = array();
        foreach ($departmentData as $deptK => $dept) {
            if (!is_int($deptK)) {
                $department[$deptK] = $dept;
                if (array_key_exists($deptK, $departmentDataItems)) {
                    $depItems = $departmentDataItems[$deptK];
                    $departmentObject[$depItems] = $dept;
                }

                if (array_key_exists($deptK, $specialtyDataItems)) {
                    $specialtyItems = $specialtyDataItems[$deptK];
                    $specialtyObject[$specialtyItems] = $dept;
                }
            }
        }

        $parentCode = array('parent_code' => null);
        $specialty = array();
        if (!is_null($parentDepartmentData['id']))
          $parentCode = array('parent_code' => $parentDepartmentData['id']);

        $department = array();
        if (!empty($departmentData)) {
            $department = array('department' => $departmentObject);
            $specialty = array(
                'specialty' => array_merge($specialtyObject, $parentCode)
            );
        }

        return array_merge($department, $specialty);

    }

    /**
     * This will return personnel data
     * @param $data
     */
    public function formatPersonnelData($data)
    {
        $personnelResultArray = $data;
        $personnelItems = array(
            'nr' => 'personnel_id',
            'contract_start' => 'date_hired',
            'contract_end' => 'date_inactive',
            'tin' => 'tin',
            'pid' => 'pid',
        );

        $personnelObjects = array();
        foreach ($personnelResultArray as $key => $personnel) {
            if (array_key_exists($key, $personnelItems)) {
                $pItems = $personnelItems[$key];
                $personnelObjects[$pItems] = $personnelResultArray[$key];
            }
        }

        // Specialties
        $departmentData = $this->getDepartmentInfo($personnelResultArray['dept_nr']);
        if (!empty($departmentData))
          $departmentObjects = $this->formatSpecialties($departmentData);

        $parentDepartmentData = $this->getDepartmentInfo($departmentData['parent_dept_nr']);
        if (!empty($parentDepartmentData))
          $parentDepartmentObjects = $this->formatSpecialties($parentDepartmentData);

        $specialtiesObjects = array();
        if ($departmentObjects !== null)
            $specialtiesObjects = array($departmentObjects);

        if ($parentDepartmentObjects !== null)
            array_push($specialtiesObjects, $parentDepartmentObjects);


        $data = array(
          'personnel' => $personnelObjects
        );

        if (!empty($specialtiesObjects))
          $data['specialties'] = $specialtiesObjects;

        #print_r(json_encode($data)); die;
        return $data;
    }

    /**
     * This will format patient data used for api
     *
     * @param $data
     * @param $isPersonnel
     * @return array
     */
    function formatPatientData($data, $isPersonnel = false)
    {
        $personResultArray = $data;
        # address info
        $res = $this->getAddressInfo($personResultArray['brgy_nr'], $personResultArray['mun_nr']);
       
        $personItems = array(
            'pid' => 'pid',
            'name_last' => 'name_last',
            'name_first' => 'name_first',
            'name_middle' => 'name_middle',
            'street_name' => 'address_line1',
            #ADDRESS 2,
            'suffix' => 'suffix',
            'sex' => 'gender',
            'place_birth' => 'birth_place',
            'date_birth' => 'birth_date',
            'brgy_code' => 'brgy_code',
            'lgu_code' => 'lgu_code',
            'religion_id' => 'religion_id',
            #'classification_id' => '',
            'occupation_id' => 'occupation_id',
            #'ethnic_id'=> 'ethnic_id'
        );

        foreach ($personResultArray as $key => $person) {
            if (is_int($key)) {
                unset($personResultArray[$key]);
            } else {
                if (array_key_exists($key, $personItems)) {
                    $pItems = $personItems[$key];
                    $personObjects[$pItems] = $personResultArray[$key];
                } elseif (array_key_exists($key, array('brgy_nr' => 'brgy_code'))) {
                    $municipalityItems = array('lgu_code' => $res['lgu_code'], 'lgu_name' => $res['lgu_name'], 'zipcode' => $res['zipcode']);
                    $baranggayItems = array('brgy_code' => $res['brgy_code'], 'brgy_name' => $res['brgy_name']);
                } elseif (array_key_exists($key, $religionItems =array('religion' => 'religion_id', 'religion_name' => 'religion_name'))) {
                    $religionObjecsts[$religionItems[$key]] = $personResultArray[$key];
                } elseif (array_key_exists($key, $civilStatItems = array('civil_status' => 'civil_status_name'))) {
                    $civilStatusObject[$civilStatItems[$key]] = $personResultArray[$key];
                } elseif (array_key_exists($key, $citizenshipItem=array('citizenship' => 'nationality_name'))) {
                    $nationalityObject[$citizenshipItem[$key]] = $personResultArray[$key];
                } elseif (array_key_exists($key, $occupationItem = array('occupation' => 'occupation_id', 'occupation_name' => 'occupation_desc'))) {
                    $occupationObject[$occupationItem[$key]] = $personResultArray[$key];
                } elseif (array_key_exists($key, $regionItem = array('region_name' => 'region_name'))) {
                    $regionObject = array('region_code' => $res['region_code'], 'region_name' => $res['region_name']);
                } elseif (array_key_exists($key, $provincesItem = array('prov_name' => 'prov_name'))) {
                    $provinceObject = array('prov_code' => trim($res['prov_code']), 'prov_name' => trim($res['prov_name']));
                }
            }
        }

        // formatted data
        $personDataObjects = array(
            'person'      => $personObjects,
            'patient'     => array(
                'spin' => $personObjects['pid'],
                'is_doe' => null,
                'is_nbr' => null,
                'date_registered' => null,
                'doe_desc' => null
            ),
            'religion'     => $religionObjecsts,
            'civilstatus'  => $civilStatusObject,
            'nationality'  => $nationalityObject,
            'occupation'   => $occupationObject,
            'region'       => $regionObject,
            'province'     => $provinceObject,
            'municipality' => $municipalityItems,
            'barangay'     => $baranggayItems,
        );

        return $personDataObjects;

    }

    /**
     * This will return encounter info
     *
     * @param $data
     * @return array
     */
    function formatEncounterData($data)
    {
        $encounterInfo = $data;
        $currentDepNr = 0;
        foreach ($encounterInfo as $encounterK => $encounter) {
            $encounterItems = array(
                'encounter_nr'        => 'encounter_no', 'encounter_date' => 'encounter_date',
                'is_maygohome'        => 'is_mgh', 'is_discharged' => 'is_discharged',
                'discharge_date'      => 'discharge_dt', 'history' => 'history',
                'mgh_setdte'          => 'mgh_dt',
                'parent_encounter_nr' => 'parent_encounter_nr',
                'encounter_type'      => 'encounter_type',
                'chief_complaint'     => 'impression'
            );

            if ($encounterK == 'current_dept_nr')
                $currentDepNr = $encounter;

            if (array_key_exists($encounterK, $encounterItems)) {
                $eItems = $encounterItems[$encounterK];
                $encounterObject[$eItems] = $encounter;
            }
        }

        # department
        $docMobility = new DoctorsMobility();
        foreach ($docMobility->getDepartmentInfo($currentDepNr) as $keyMob => $mobility) {
           if (!is_int($keyMob))
             $departmentObject[$keyMob] = $mobility;
        }

        $encounter_types = array(
            1 => self::ENCOUNTER_TYPE_1,
            2 => self::ENCOUNTER_TYPE_2,
            3 => self::ENCOUNTER_TYPE_3,
            4 => self::ENCOUNTER_TYPE_4
        );
        return array(
            'encounter'           => array_merge($encounterObject, array('is_infectious' => null, 'is_pregnant' => null)),
            'departmentenc'       => array(
               // 'deptenc_no' => null,
                'deptenc_code'   =>  $encounter_types[$encounterObject['encounter_type']],
                'deptenc_date'    => date('Y-m-d h:i:s'),
                'specialty_code'  => null,
                'is_medicolegal'  => null,
                'is_medicolegal'  => null,
                'is_DOA'          => null,
                'admit_diagnosis' => $encounterObject['impression'],
                'impression'      => $encounterObject['impression'],
            ),
            'erdepartment'  => array(
                'dept_id'   => $departmentObject['parent_dept_nr'],
                'dept_code' => $departmentObject['parent_dept_nr'],
                'dept_name' => $departmentObject['parent_name'],
            ),
            'erarea'        => array(
               'area_id'    => $departmentObject['nr'],
               'area_code'  => $departmentObject['id'],
               'area_desc'  => $departmentObject['name_formal'],
            ),
        );

    }

}