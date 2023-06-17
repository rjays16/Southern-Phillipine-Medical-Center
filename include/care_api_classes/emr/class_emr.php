<?php

require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');

/**
 * Class that handles emr integration
 * @author Vanessa A. Saren
 */
class EMR extends Core {

    var $sql;

    #consume POST and PUT method

    function consumeWRITEmethod($data, $url, $method) {

        $data = array_map('utf8_encode', $data);
        $data_string = json_encode($data);

        $client = curl_init($url);
        curl_setopt($client, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($client, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($client, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($client);
        curl_close($client);

        return $result;
    }

    #consume GET method

    function consumeREADmethod($url) {
        $client = curl_init($url);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($client);
        #$info = curl_getinfo($client);
        curl_close($client);

        return $result;
    }

    function consumeWRITEmethodnoDATA($url, $method) {
        $client = curl_init($url);
        curl_setopt($client, CURLOPT_URL, $url);
        #curl_setopt($client, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($client, CURLOPT_PUT, true);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($client);
        curl_close($client);

        return $result;
    }

    function getAddressInfo($brgy_nr, $mun_nr) {
        global $db;

        $this->sql = "SELECT sb.brgy_name, sm.mun_name,
				sp.prov_name,
				sc.country_name,
				sm.zipcode
				FROM seg_barangays AS sb
				LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr 
				LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
				LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
				LEFT JOIN seg_country AS sc ON sc.country_code=" . $db->qstr($citizenship);

        if(empty($brgy_nr) || $brgy_nr == 0)
            $this->sql .= " WHERE sm.mun_nr=" . $db->qstr($mun_nr);
        else      
            $this->sql .= " WHERE sb.brgy_nr=" . $db->qstr($brgy_nr) . " 
                            AND sm.mun_nr=" . $db->qstr($mun_nr);

        $row = $db->GetRow($this->sql);

        return $row;
    }

    function getDoctorInfo($personell_nr) {
        global $db;

        $this->sql = "SELECT ps.pid, ps.nr, ps.job_function_title,
				ps.job_position,ps.license_nr,ps.tin, p.name_last, p.name_first, 
				p.name_middle, p.date_birth, p.sex, p.street_name, 
				sm.mun_name, sp.prov_name, sc.country_name, sm.zipcode,
				p.phone_1_nr,p.phone_2_nr,p.fax,p.cellphone_1_nr,p.email,
				p.brgy_nr, p.mun_nr
				FROM care_personell ps
				INNER JOIN care_person AS p ON p.pid=ps.pid
				LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
				LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
				LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
				LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
				LEFT JOIN seg_country AS sc ON sc.country_code=p.citizenship
				WHERE ps.nr=" . $db->qstr($personell_nr);

        $row = $db->GetRow($this->sql);
        $dept = $this->getDeptInfo($personell_nr);
        $row['location_nr'] = $dept ? $dept['location_nr'] : '';
        $row['dept_name'] = $dept ? $dept['name_formal'] : '';
        return $row;
    }

    function getDeptInfo($personell_nr) {
        $personell = new Personell();
        return $personell->get_Dept_name($personell_nr);
    }

    function isDoctor($personell_nr) {
        global $db;

        $this->sql = "SELECT IF(SUBSTR(short_id,1,1)='D',1,0) AS isdoctor 
				FROM care_personell p 
				WHERE nr=" . $db->qstr($personell_nr);

        $row = $db->GetRow($this->sql);

        return $row['isdoctor'];
    }

    function getPatientdataArray($dataarr) {

        extract($dataarr);

        $mother = $mother_fname . (isset($mother_maidenname) ? ' ' . $mother_maidenname : '') .
                (isset($mother_mname) ? ' ' . $mother_mname : '') .
                (isset($mother_lname) ? ' ' . $mother_lname : '');

        $father = $father_fname . (isset($father_mname) ? ' ' . $father_mname : '') .
                (isset($father_lname) ? ' ' . $father_lname : '');

        $name_last = $name_last . (isset($suffix) ? ', ' . $suffix : '');

        $row_addr = $this->getAddressInfo($brgy_nr, $mun_nr);

        if(empty($brgy_nr) || $brgy_nr == 0)
            $street_name = $street_name;
        else
            $street_name = $street_name . (isset($row_addr['brgy_name']) ? ', ' . $row_addr['brgy_name'] : '');

        $data = array(
            "FirstName" => $name_first,
            "MiddleName" => $name_middle,
            "LastName" => $name_last,
            "Gender" => strtoupper($sex),
            "MaidenLastName" => ($name_maiden ? $name_maiden : ''),
            "Title" => ($title ? $title : ''),
            "DateOfBirth" => date('m/d/Y H:i:s', strtotime($date_birth.' '.$birth_time)),
            "SecurityPin" => "",
            "RegistrationNotes" => "",
            "ClinicalNotes" => "",
            "EmergencyContactPhone" => "",
            "EmergencyContactName" => "",
            "SocialSecurityNumber" => "",
            "HISRegistrationDate" => $date_reg,
            "HISId" => $pid,
            "Street1" => ((trim($street_name)) ? trim($street_name) : 'n/a'),
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

    function getDoctordataArray($dataarr) {

        extract($dataarr);

        $mother = $mother_fname . (isset($mother_maidenname) ? ' ' . $mother_maidenname : '') .
                (isset($mother_mname) ? ' ' . $mother_mname : '') .
                (isset($mother_lname) ? ' ' . $mother_lname : '');

        $father = $father_fname . (isset($father_mname) ? ' ' . $father_mname : '') .
                (isset($father_lname) ? ' ' . $father_lname : '');

        $name_last = $name_last . (isset($suffix) ? ', ' . $suffix : '');

        $row_addr = $this->getAddressInfo($brgy_nr, $mun_nr);

        if(empty($brgy_nr) || $brgy_nr == 0)
            $street_name = $street_name;
        else
            $street_name = $street_name . (isset($row_addr['brgy_name']) ? ', ' . $row_addr['brgy_name'] : '');

        $data = array(
            "PhysicianNumber" => $nr,
            "FirstName" => $name_first,
            "LastName" => $name_last,
            "Address1" => ((trim($street_name)) ? trim($street_name) : ''),
            "Address2" => "",
            "City" => trim($row_addr['mun_name']),
            "Province" => trim($row_addr['prov_name']),
            "ZipCode" => trim($row_addr['zipcode']),
            "MainPhone" => ($phone_1_nr ? $phone_1_nr : ''),
            "PrivatePhone" => ($phone_2_nr ? $phone_2_nr : ''),
            "Fax" => ($fax ? $fax : ''),
            "MobilePhone" => ($cellphone_1_nr ? $cellphone_1_nr : ''),
            "EmailAddress" => ($email ? $email : ''),
            "DepartmentId" => $location_nr,
            "DepartmentName" => $dept_name,
        );

        return $data;
    }

    function getEncounterdataArray($dataarr) {

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
     * simplified get encounter for emr
     * @global type $db
     * @param type $encNr
     * @return type
     */
    function getEncounterInfo($encNr) {
        global $db;
        $this->sql = "SELECT IF(encounter_type=1,'ER', IF(encounter_type=2,'OPD', IF(encounter_type in (3,4),'IPD', ''))) as patient_type, er_opd_diagnosis, current_dept_nr, consulting_dept_nr, pid, encounter_nr
            FROM care_encounter WHERE encounter_nr = " . $db->qstr($encNr);
        $row = $db->GetRow($this->sql);
        return empty($row) ? false : $row;
    }

    /**
     * added by VAN 10/14/2014
     * store in the audit trail log, all the transaction in EMR
    **/
    function storeAuditLog($data){
        global $db;
        
        $date_created = date("Y-m-d H:i:s");

        extract($data);

        $sql = "SELECT UUID() AS id";
        $id = $db->GetOne($sql);

        $created_id = $_SESSION['sess_temp_userid'];

        $result = $db->Replace('seg_emr_logs',
                                            array(
                                                     'id'=>$db->qstr($id),
                                                     'api'=>$db->qstr($api),
                                                     'url'=>$db->qstr($url),
                                                     'method'=>$db->qstr($method),
                                                     'message' =>$db->qstr($message),
                                                     'pid'=>$db->qstr($pid),
                                                     'encounter_nr'=>$db->qstr($encounter_nr),
                                                     'personell_nr'=>$db->qstr($personell_nr),
                                                     'refno'=>$db->qstr($refno),
                                                     'http_code'=>$db->qstr($http_code),
                                                     'date_created'=>$db->qstr($date_created),
                                                     'created_id'=>$db->qstr($created_id)
                                                ),
                                                array('id'),
                                                $autoquote=FALSE
                                           );
         
         if ($result) 
            return TRUE;
         else{
            $this->errormsg = "error : ".$db->ErrorMsg();
            return FALSE;
         } 
    }

    function getLaboratoryInfo($refno){
        global $db;
        $this->sql = "SELECT ls.pid AS 'pid', ls.encounter_nr AS 'enc_nr', 
                        ls.is_cash AS 'is_cash', ls.refno AS 'refno', lsd.clinical_info AS 'clinical_info',
                        ls.is_urgent AS 'is_urgent', serv.group_code AS 'group_code',
                        lsd.service_code AS 'service_code', serv.name AS 'service_name', lsd.request_doctor AS 'doctor'
                        FROM seg_lab_serv AS ls
                        INNER JOIN seg_lab_servdetails AS lsd ON lsd.refno = ls.refno
                        INNER JOIN seg_lab_services AS serv ON serv.service_code = lsd.service_code
                        WHERE ls.refno = ". $db->qstr($refno);

        $row = $db->GetAll($this->sql);
        return empty($row) ? false : $row;
    }

    function getRadiologyInfo($refno){
        global $db;
        $this->sql = "SELECT rs.pid AS 'pid', rs.encounter_nr AS 'enc_nr', 
                        rs.is_cash AS 'is_cash', rs.refno AS 'refno', trr.clinical_info AS 'clinical_info',
                        rs.is_urgent AS 'is_urgent', serv.group_code AS 'group_code',
                        trr.service_code AS 'service_code', serv.name AS 'service_name',
                        trr.request_doctor AS 'doctor', trr.batch_nr AS 'batchNo'
                        FROM seg_radio_serv rs
                        INNER JOIN care_test_request_radio trr ON trr.refno = rs.refno
                        INNER JOIN seg_radio_services serv ON serv.service_code = trr.service_code
                        WHERE rs.refno = ". $db->qstr($refno);
        
        $row = $db->GetAll($this->sql);
        return empty($row) ? false : $row;
    }

    function getLabRaddataArray($dataarr, $isRad = FALSE){
        $items = array();
        if($isRad){
            foreach ($dataarr as $key => $value) {
                $items[] = array(
                    "ReferenceNumber" => $value['batchNo'],
                    "SectionCode" => $value['group_code'],
                    "TestCode" => $value['service_code'],
                );
            }
        }
        else{
            foreach ($dataarr as $key => $value) {
                $items[] = array(
                    "SectionCode" => $value['group_code'],
                    "TestCode" => $value['service_code'],
                    "TestName" => $value['service_name']
                );
            }
        }
        

        $data = array(
            "PatientId" => $dataarr[0]['pid'],
            "CaseNumber" => $dataarr[0]['enc_nr'],
            "IsCash" => $dataarr[0]['is_cash'] == 1?TRUE:FALSE,
            "ReferenceBatchNumber" => $dataarr[0]['refno'],
            "DoctorId" => $dataarr[0]['doctor'],
            "ClinicalImpression" => $dataarr[0]['clinical_info']?$dataarr[0]['clinical_info']:'n/a',
            "IsUrgent" => $dataarr[0]['is_urgent'] == 1?TRUE:FALSE,
            "RequisitionOrderList" => $items
        );

        return $data;
    }

    function changeLaboratoryFlag($refno){
        global $db;
        $this->sql = "UPDATE seg_lab_servdetails
                        SET is_posted_emr = 1
                        WHERE refno =". $db->qstr($refno);

        return $db->Execute($this->sql);
    }

    #added By Mark 04/29/16
    function UpdateSaveDone($batch_no){
        global $db;
        $this->sql = "UPDATE care_test_request_radio
                        SET save_and_done =NOW()
                        WHERE batch_nr =". $db->qstr($batch_no);

        return $db->Execute($this->sql);
    }
}