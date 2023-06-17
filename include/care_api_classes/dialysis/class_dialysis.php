<?php

require('./roots.php');
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . 'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path . 'include/care_api_classes/class_special_lab.php');
require_once($root_path . 'include/care_api_classes/class_social_service.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
require_once($root_path . 'include/care_api_classes/billing/class_ops_new.php');

/**
 * Created by -xXLXx- (Trainee)
 * Created on 04/03/2014
 */
define('MAX_ALLOWABLE_DIALYZER', 8);
define('HEMODIALYSIS', 90935);
/**
 * Doctor Fee per Hemodialysis session
 */
define('HEMODIALYSIS_CHARGE', 500);
/**
 * Doctor role for Doctor Fees
 */
define('DOCTOR_ROLE', 1);

class SegDialysis extends Core
{
    #Edited by Devon (Intern)
    #2/21/2014
    #Reason: Insert dialysis request data

    var $hemo_chrg = 0;
    var $tb_dialysis = "seg_dialysis_request";
    var $tb_prebill = "seg_dialysis_prebill";
    var $opsInfo;
    var $fld_dialysis =
        array(
            'encounter_nr',
            'pid',
            'request_date',
            'requesting_doctor',
            'attending_nurse',
            'remarks',
            'diagnosis',
            'procedure',
            'modify_id',
            'is_released', //added by Kenneth Kempis 04/13/2018
        );
    public $phQuantity;
    public $nphQuantity;
    public $phAmount;
    public $nphAmount;
    #added by raymond
    public $hdfQuantity;
    public $hdfAmmount;

    function SegDialysis()
    {
        $this->useDialysis();
    }

    function useDialysis()
    {
        $this->coretable = $this->tb_dialysis;
        $this->ref_array = $this->fld_dialysis;
    }

    function usePrebill()
    {
        $this->coretable = $this->tb_prebill;
    }

    function getNewRefno()
    {
        global $db;
        $ref_nr = date('Y') . '000001';
        $temp_ref_nr = date('Y') . "%";   # NOTE : XXXX?????? would be the format of Reference number
        $row = array();
        $this->sql = "SELECT refno FROM $this->tb_dialysis WHERE refno LIKE '$temp_ref_nr' ORDER BY refno DESC";
        if ($this->res['gnpn'] = $db->SelectLimit($this->sql, 1)) {
            if ($this->res['gnpn']->RecordCount()) {
                $row = $this->res['gnpn']->FetchRow();
                return $row['refno'] + 1;
            } else {/* echo $this->sql.'no count'; */
                return $ref_nr;
            }
        } else {/* echo $this->sql.'no sql'; */
            return $ref_nr;
        }
    }

    public function updateBillsDiscountId($billNrs, $id)
    {
        global $db;
        $db->startTrans();
        $this->usePrebill();
        try {
            foreach ($billNrs as $bill) {
                $this->sql = $db->Prepare('UPDATE ' . $this->coretable . ' SET discountid = ? WHERE bill_nr = ?');
                $db->Execute($this->sql, array($id, $bill));
            }
        } catch (Exception $e) {
            $db->FailTrans();
            return false;
        }
        $db->CompleteTrans();
        return true;
    }

    /**
     * Count bills. If not set defaults to 0.
     * @return type
     */
    function countBill()
    {

        if (empty($_POST['PHamount'])) {
            $this->phAmount = 0;
        } else {
            $this->phAmount = $_POST['PHamount'];
        }
        if (empty($_POST['PHquantity'])) {
            $this->phQuantity = 0;
        } else {
            $this->phQuantity = $_POST['PHquantity'];
        }

        if (empty($_POST['NPHamount'])) {
            $this->nphAmount = 0;
        } else {
            $this->nphAmount = $_POST['NPHamount'];
        }
        if (empty($_POST['NPHquantity'])) {
            $this->nphQuantity = 0;
        } else {
            $this->nphQuantity = $_POST['NPHquantity'];
        }
        #added by raymond
        if(empty($_POST['HDFAmount']))
        {
            $this->hdfQuantity = 0;
            $this->hdfAmmount = 0;
        }
        else
        {
            $this->hdfQuantity = $_POST['HDFquantity'];
            $this->hdfAmmount = $_POST['HDFAmount'];
        }

        return $this->phQuantity + $this->nphQuantity + $this->hdfQuantity;
    }

    /**
     * Count Philhealth Bills
     * @return type
     */
    function countBillPh()
    {
        return isset($this->phQuantity) ? $this->phQuantity : 0;
    }

    /**
     * Count Non-Philhealth Bills
     * @return type
     */
    function countBillNph()
    {
        return isset($this->nphQuantity) ? $this->nphQuantity : 0;
    }

    /**
     * Count HDF Bills
     * @return type
     */
    function countBillHdf()
    {
        return isset($this->hdfQuantity) ? $this->hdfQuantity : 0;
    }

    /**
     * Saves to seg_lab_serv and seg_lab_servdetails with data from dialysis.
     * @param type $serviceCode
     * @param type $postData
     */
    public function addLabRequest($serviceCode, $postData)
    {
        $spLab = new SegSpecialLab();
        $data = $this->prepareLabData($postData);
        $lab = new SegLab();
        if ($lab->isItemized($serviceCode)) {
            $query = $spLab->getAllServiceOfPackage($serviceCode, $data['is_cash']);
            while (true) {
                $row = $query->FetchRow();
                if (!$row)
                    break;
                $services[] = $row;
            }
        } else {
            $services[] = $lab->getTestCode($serviceCode);
        }

        try {
            $lab->saveLabRefNoInfoFromArray($this->formatLabData($data['is_cash'], $data, $services));
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
        }
    }

    /**
     * Fetch data for doctor info, refno and serv date for lab request.
     * @param type $postData
     * @return int
     */
    private function prepareLabData($postData)
    {
        $lab = new SegLab();
        $refNo = $lab->getLastRefno();
        $doctor = array();
        $personell = new Personell();
        $doctor = array_merge($doctor, $personell->get_Dept_name($postData['request_doctor']));
        $doctor = array_merge($doctor, $personell->get_Person_name($postData['request_doctor']));
        //update lab tracker

        $lab->update_LabRefno_Tracker($refNo);
        // $month = date('m') + 1;
        // $year = date('Y');
        //get the next month if december
        // if ($month > 12) {
        //     $month = $month % 12;
        //     $year++;
        // }
        //get first date of the next month
       
        //if transaction has philhealth amount/quantity then it is cash
        if ((intval($postData['PHamount']) + intval($postData['PHquantity'])) > 0)
        {
 $orderDate = strtotime("+1 Minute", strtotime($postData['requestdate']));
        $data = array();
        $data = array_merge($data, $postData);
        $data['ordername'] = $postData['name'];
        $data['orderaddress'] = $postData['address'];
        $data['orderdate'] = date('Y-m-d H:i:s');
        $data['refno'] = $refNo;
        $data['serv_dt'] = date('Y-m-d', $orderDate);
        $data['serv_tm'] = date('H:i:s', $orderDate);
        $data['is_cash'] = 0;
        $data['doctor_name'] = $doctor['dr_name'];
        $data['location_nr'] = $doctor['location_nr'];
        $data['source_req'] = 'LD';
        $data['ref_source'] = 'LB';
        $data['history'] = 'Create ' . date('Y-m-d H:i:s') . ' ' . $postData['request_encoder'];
        $data['encoder'] = $postData['request_encoder'];
        $data['is_rdu'] = 1;
        $data['grant_type'] = 'phic';
        $data['request_flag'] = 'phic';
        }
            
            elseif ((intval($postData['NPHamount']) + intval($postData['NPHquantity'])) > 0) 
            {
 $orderDate = strtotime("+1 Minute", strtotime($postData['requestdate']));
        $data = array();
        $data = array_merge($data, $postData);
        $data['ordername'] = $postData['name'];
        $data['orderaddress'] = $postData['address'];
        $data['orderdate'] = date('Y-m-d H:i:s');
        $data['refno'] = $refNo;
        $data['serv_dt'] = date('Y-m-d', $orderDate);
        $data['serv_tm'] = date('H:i:s', $orderDate);
        $data['is_cash'] = 1;
        $data['doctor_name'] = $doctor['dr_name'];
        $data['location_nr'] = $doctor['location_nr'];
        $data['source_req'] = 'LD';
        $data['ref_source'] = 'LB';
        $data['history'] = 'Create ' . date('Y-m-d H:i:s') . ' ' . $postData['request_encoder'];
        $data['encoder'] = $postData['request_encoder'];
        $data['is_rdu'] = 1;
            }

        return $data;
    }

    /**
     * Format data to be passed to class_lab_transaction saveLabRefNoInfoFromArray method
     * @param type $isCash
     * @param type $data
     * @param type $services
     * @return int
     */
    private function formatLabData($isCash, $data, $services)
    {
        $doc = $data['request_doctor'];
        unset($data['request_doctor']);
        foreach ($services as $serv) {
            $data['sservice'][] = 1;
            $data['pcash'][] = $serv['price_cash'];
            $data['pcharge'][] = $serv['price_charge'];
            $data['request_doctor'][] = $doc;
            $data['request_dept'][] = $data['location_nr'];
            $data['requestDocName'][] = $data['doctor_name'];
            $data['clinical_info'][] = $data['reqdiagnosis'];
            $data['is_in_house'][] = 1;
            $data['service_code'][] = $serv['service_code'];
            $data['pnet'][] = $isCash == true ? $serv['price_cash'] : $serv['price_charge'];
            $data['pnetbc'][] = $isCash == true ? $serv['price_cash'] : $serv['price_charge'];
            $data['quantity'][] = 1;
        }
        return $data;
    }

    /* Added by Devon (Intern)
     * 02/24/2014
     * Reason: To insert new dialysis pre=
     */

    function insertNewRequest($arr, $arr2)
    {
        global $db;
        $count_bill = $this->countBill();

        $count_billph = $this->countBillPh();
        $count_billnph = $this->countBillNph();
        // $count_billhdf = $this->countBillHdf();

        $data = array();
        $data2 = array();

        for ($i = 1; $i <= $count_billph; $i++) {

            $hasSubsidy = ($arr['amount'] + $arr['hdf_amount']) - $arr['subsidy_amount'];

            array_push($data, array($arr['encounter_nr'] . '-' . $i,
                    $arr['encounter_nr'],
                    $arr['bill_type'],
                    $arr['amount'],
                    ($hasSubsidy == 0) ? 'manual' : null, 
                    null,
                    $arr['hdf_amount'],
                   $arr['subsidy_amount'],$arr['subsidy_class'])
            );

            if($arr['subsidy_amount'] != '0.00') {
                $subsidy_data = array(
                    'encounter_nr' => $arr['encounter_nr'],
                    'bill_nr' => $arr['encounter_nr'].'-'.$i,
                    'amount' => $arr['subsidy_amount'],
                    'pay_type' => 'subsidized'
                );

                $this->savePay($subsidy_data);
            }
         }
    
        $last_digit = $count_billph + 1;

        #modified by raymond - add HDF prebills SPMC-861
        /*for ($k = 1; $k <= $count_billnph; $k++) {

            for ($last_digit; $last_digit <= $count_bill; $last_digit++) {

                array_push($data, array($arr2['encounter_nr'] . '-' . $last_digit,
                        $arr2['encounter_nr'],
                        $arr2['bill_type'],
                        $arr2['amount'],
                        null, null)
                );
            }
        }*/
        #for NPH
        for ($i = $last_digit; $i < ($last_digit+$count_billnph); $i++) {
            $hasSubsidy = ($arr2['amount'] + $arr2['hdf_amount']) - $arr2['subsidy_amount'];

            array_push($data, array($arr2['encounter_nr'] . '-' . $i,
                    $arr2['encounter_nr'],
                    $arr2['bill_type'],
                    $arr2['amount'],
                    ($hasSubsidy == 0) ? 'manual' : null, 
                    null,
                    $arr2['hdf_amount'],
                    $arr2['subsidy_amount'],$arr2['subsidy_class'])
            );

            if($arr2['subsidy_amount'] != '0.00') {
                $subsidy_data2 = array(
                    'encounter_nr' => $arr2['encounter_nr'],
                    'bill_nr' => $arr2['encounter_nr'].'-'.$i,
                    'amount' => $arr2['subsidy_amount'],
                    'pay_type' => 'subsidized'
                );

                $this->savePay($subsidy_data2);
            }
        }
        $last_digit = $last_digit+$count_billnph;

        #for HDF
        // for ($i = $last_digit; $i < ($last_digit+$count_billhdf); $i++) {
        //     array_push($data, array($arr['encounter_nr'] . '-' . $i,
        //             $arr3['encounter_nr'],
        //             $arr3['bill_type'],
        //             $arr3['hdf_amount'],
        //             null, null,$arr3['subsidy_amount'],$arr3['subsidy_class'])
        //     );
        // }

         $sql = $db->Prepare("INSERT INTO seg_dialysis_prebill 
                                        VALUES (?,?,?,?,?,?,?,?,?)");

        $db->bulkBind = TRUE;
        $rs1 = $db->Execute($sql, $data);


        if ($rs1) {

            return TRUE;
        } else {

            return FALSE;
        }
    }

    //added by Kenneth Kempis 04/13/2018
    function setIsReleased($enc_nr, $val)
    {
        global $db;
        $this->sql = $db->Prepare("UPDATE
                                    `seg_dialysis_request`
                                    SET
                                    `is_released` = ?
                                    WHERE
                                    `encounter_nr` = ?");
        if($result = $db->Execute($this->sql,array($val,$enc_nr)))
            return 1;
        else
            return 0;   
    }
    function getIsReleased($encounter_nr)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT
                                    `is_released`
                                    FROM
                                    `seg_dialysis_request` sdr
                                    WHERE sdr.`encounter_nr` = ?");
        if($result = $db->Execute($this->sql,$encounter_nr))
        {
            if($result->RecordCount())
                return $result;
            else
                return false;
        }
        else
            return false;
    }
    //end Kenneth Kempis 04/13/2018

    function getRequestDiagnosis($encNr)
    {
        global $db;
        $diagnosis = $db->GetOne('SELECT diagnosis FROM seg_dialysis_request WHERE encounter_nr = ' . $db->qstr($encNr));

        if ($diagnosis) {
            return $diagnosis;
        }
        return false;
    }

     function getPreviousDoctor($pid)
    {
        global $db;
           $this->sql = "SELECT requesting_doctor AS id,
  fn_get_personell_lastname_first2(requesting_doctor) AS doctor_name  FROM seg_dialysis_request WHERE pid =". $db->qstr($pid)." ORDER BY encounter_nr DESC LIMIT 1";

         // echo "this->sql = '".$this->sql."' <br> \n"; 
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount() > 0) {
                return $this->result;
            }
        } else {
            return FALSE;
        }
    }
    

    function saveTransaction($enc_data, $dialysis_data, $new_enc)
    {
        global $db;
        //$db->StartTrans();
        $enc_obj = new Encounter($new_enc);
        //save to encounter first
        $enc_obj->setDataArray($enc_data);
        if ($saveok = $enc_obj->insertDataFromInternalArray()) {
            if ($saveok = $enc_obj->update_Encounter_Tracker($new_enc, "dialysis")) {
                //save to dialysis
                $this->setDataArray($dialysis_data);
                if ($saveok = $this->insertDataFromInternalArray()) {
                    //$db->CompleteTrans();
                    return TRUE;
                }
            } else {
                //$db->FailTrans();
                $this->error_msg = $db->ErrorMsg();
                return FALSE;
            }
        } else {
            //$db->FailTrans();
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getDialysisPersonell($location_type_nr, $role)
    {
        global $db;

        if ($location_type_nr) {
            $cond_location = " pa.location_type_nr=" . $db->qstr($location_type_nr) . " AND \n";
        }
        if ($role) {
            $cond_role = " rp.role=" . $db->qstr($role) . " AND \n";
        }
        $this->sql = "SELECT pa.nr, pa.personell_nr, ps.job_function_title,rp.role,ps.job_position,ps.license_nr, \n" .
            "ps.tin, p.name_last, p.name_first, p.name_2, p.name_middle, p.date_birth, p.sex \n" .
            "FROM care_personell AS ps, \n" .
            "care_personell_assignment AS pa, \n" .
            "care_person AS p, \n" .
            "care_role_person AS rp, \n" .
            "care_department AS d \n" .
            "WHERE \n" .
            $cond_location .
            $cond_role .
             "(pa.date_end='0000-00-00' OR pa.date_end>='".date('Y-m-d')."') \n" .
            "AND pa.status NOT IN ('hidden','inactive','void') \n" .
            "AND  pa.personell_nr = ps.nr 
             AND ps.pid = p.pid 
             AND pa.location_nr = d.nr 
             AND (ps.short_id LIKE 'D%') 
             AND d.admit_inpatient = 1 \n" .
            "ORDER BY p.name_last";

         /*echo "this->sql = '".$this->sql."' <br> \n"; */
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount() > 0) {
                return $this->result;
            }
        } else {
            return FALSE;
        }
    }

    function updateTransactionStatus($refno, $status, $reason, $encounter_nr)
    {
        global $db;
        $db->StartTrans();
        $history = $db->GetOne("SELECT history FROM care_encounter WHERE encounter_nr=" . $db->qstr($encounter_nr));

        switch ($status) {
            case "0":
                $status_char = "UNDONE";
                break;
            case "1":
                $status_char = "DONE";
                break;
        }
        $this->sql = "UPDATE $this->tb_dialysis SET status=" . $db->qstr($status_char) . ", reason=" . $db->qstr($reason) .
            ", modify_id=" . $db->qstr($_SESSION["sess_temp_userid"]) . ", modify_date=NOW() " .
            "WHERE refno=" . $db->qstr($refno);
        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            if ($status == "0") {
                $new_history = $history . "\n" . "Cancelled " . date('Y-m-d H:i:s') . "=" . $_SESSION["sess_user_name"];
                $this->sql = "UPDATE care_encounter SET encounter_status='cancelled', is_discharged='0', \n" .
                    "discharge_date='', discharge_time='', history=" . $db->qstr($new_history) . ", \n" .
                    "modify_id=" . $db->qstr($_SESSION["sess_user_name"]) . ", modify_time=NOW() " .
                    "WHERE encounter_nr=" . $db->qstr($encounter_nr);
            } else {
                $new_history = $history . "\n" . "Updated " . date('Y-m-d H:i:s') . "=" . $_SESSION["sess_user_name"];
                $this->sql = "UPDATE care_encounter SET encounter_status='', is_discharged='1', discharge_date=" . $db->qstr(date('Y-m-d')) .
                    ", discharge_time=" . $db->qstr(date('H:i:s')) . " , history=" . $db->qstr($new_history) . ", \n" .
                    "modify_id=" . $db->qstr($_SESSION["sess_user_name"]) . ", modify_time=NOW() " .
                    "WHERE encounter_nr=" . $db->qstr($encounter_nr);
            }
            $this->result = $db->Execute($this->sql);
            if ($this->result !== FALSE) {
                $db->CompleteTrans();
                return TRUE;
            } else {
                $db->FailTrans();
                $this->error_msg = $db->ErrorMsg();
                return FALSE;
            }
        } else {
            $db->FailTrans();
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getTransactionDetails($pid, $enc_nr, $refno)
    {
        global $db;
        $this->sql = "SELECT dt.*, fn_get_person_name(dt.pid) AS `patient_name` \n" .
            "FROM seg_dialysis_transaction AS dt \n" .
            "INNER JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n" .
            "INNER JOIN care_person AS cp ON ce.pid=cp.pid \n" .
            "WHERE ce.is_discharged!='1' AND dt.transaction_nr=" . $db->qstr($transaction_nr) . " AND \n" .
            "dt.encounter_nr=" . $db->qstr($enc_nr) . " AND dt.pid=" . $db->qstr($pid);
        $details = $db->GetRow($this->sql);
        if ($details !== FALSE) {
            return $details;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function updateTransactionDetails($refno, $data)
    {
        global $db;
        $this->sql = "UPDATE $this->tb_dialysis SET encounter_nr=" . $db->qstr($data["encounter_nr"]) . ", " .
            "pid=" . $db->qstr($data["pid"]) . ", transaction_date=" . $db->qstr($data["transaction_date"]) . ", " .
            "status=" . $db->qstr($data["status"]) . ", " .
            "requesting_doctor=" . $db->qstr($data["requesting_doctor"]) . ", attending_nurse=" . $db->qstr($data["attending_nurse"]) . ", " .
            "dialysis_type=" . $db->qstr($data["dialysis_type"]) . ", remarks=" . $db->qstr($data["remarks"]) . ", " .
            "reason=" . $db->qstr($data["reason"]) . ", modify_id=" . $db->qstr($_SESSION["sess_temp_userid"]) . ", " .
            "modify_date=NOW() WHERE refno=" . $db->qstr($refno);
        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            return TRUE;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function requestChecker($encounter_nr)
    {
        global $db;
        $this->sql = "SELECT \n" .
            "EXISTS(SELECT refno FROM seg_lab_serv WHERE encounter_nr=" . $db->qstr($encounter_nr) . " AND status <> 'deleted') AS `lab`, \n" .
            "EXISTS(SELECT refno FROM seg_radio_serv WHERE encounter_nr=" . $db->qstr($encounter_nr) . "  AND status <> 'deleted') AS `radio`, \n" .
            "EXISTS(SELECT refno FROM seg_pharma_orders WHERE encounter_nr=" . $db->qstr($encounter_nr) . " ) AS `pharma`, \n" .
            "EXISTS(SELECT refno FROM seg_misc_service WHERE encounter_nr=" . $db->qstr($encounter_nr) . " ) AS `misc` ";
        $data = $db->GetRow($this->sql);
        $exists = FALSE;
        if ($data["lab"] == 1) {
            $exists = TRUE;
        } else if ($data["pharma"] == 1) {
            $exists = TRUE;
        } else if ($data["radio"] == 1) {
            $exists = TRUE;
        } else if ($data["misc"] == 1) {
            $exists = TRUE;
        }

        return $exists;
    }

    function deleteTransactionDetails($refno, $enc_nr)
    {
        global $db;
        $db->StartTrans();
        $this->sql = "UPDATE $this->tb_dialysis SET is_deleted='1', modify_id=" . $db->qstr($_SESSION["sess_temp_userid"]) .
            ", modify_date=NOW() WHERE refno=" . $db->qstr($refno);
        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            $history = $db->GetOne("SELECT history FROM care_encounter WHERE encounter_nr=" . $db->qstr($enc_nr));
            $new_history = $history . "\n" . "Cancelled " . date('Y-m-d H:i:s') . "=" . $_SESSION["sess_user_name"];
            $this->sql = "UPDATE care_encounter SET encounter_status='cancelled', is_discharged='0', \n" .
                "discharge_date='', discharge_time='', history=" . $db->qstr($new_history) . ", \n" .
                "modify_id=" . $db->qstr($_SESSION["sess_user_name"]) . ", modify_time=NOW() " .
                "WHERE encounter_nr=" . $db->qstr($enc_nr);
            $this->result = $db->Execute($this->sql);
            if ($this->result !== FALSE) {
                $saveok = true;
            } else {
                $saveok = false;
            }
        } else {
            $saveok = false;
        }

        if ($saveok) {
            $db->CompleteTrans();
            return TRUE;
        } else {
            $db->FailTrans();
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getTransactionByPid($pid)
    {
        global $db;
        $this->sql = "SELECT dt.*, fn_get_person_name(dt.pid) AS `patient_name` \n" .
            "FROM seg_dialysis_transaction AS dt \n" .
            "INNER JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n" .
            "INNER JOIN care_person AS cp ON ce.pid=cp.pid \n" .
            "WHERE ce.is_discharged!='1' AND dt.pid=" . $db->qstr($pid);
        $details = $db->GetRow($this->sql);
        if ($details !== FALSE) {
            return $details;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getTransactionByTnr($transactionNr)
    {
        global $db;
        //$db->debug = true;
        $sql = $db->Prepare('SELECT fn_get_person_name(cp.pid) AS name, cp.date_birth, cp.sex, dt.transaction_nr, dt.pid, dt.dialyzer_serial_nr, dt.machine_nr, dt.transaction_nr, dt.transaction_date
                                    FROM seg_dialysis_transaction dt
                                    INNER JOIN care_person cp ON cp.pid = dt.pid
                                    WHERE dt.transaction_nr = ?');
        $results = $db->Execute($sql, $transactionNr);

        if ($results) {
            return $results->FetchRow();
        }
        return false;
    }

    #Created: 2/10/2014  - Jayson OJT
    #New feature: list of patients and machine assignment per day.

    function getMachinePatientList($input_date, $input_data, $search_by, $transaction_nr)
    {
        global $db;
        //$db->debug = true;
        if (isset($input_date) && $input_date != "") {
            $input_month = substr($input_date, 0, 2);
            $input_day = substr($input_date, 3, 2);
            $input_year = substr($input_date, 6, 4);
            $complete_date = $input_year . "-" . $input_month . "-" . $input_day;
            $append_date = " AND a.transaction_date LIKE '$complete_date%'";
            $complete_date_time = $complete_date . " " . "23:59:59";
        }

        if (isset($input_data) && $input_data != "" && $search_by == "by_hrn") {
            $append_hrn = " AND a.pid =" . $db->qstr($input_data);
        } else if (isset($input_data) && $input_data != "" && $search_by == "by_name") {

            # for handling with comma with space input.
            $data = explode(",", trim($input_data));
            $last = trim($data[0]);
            $name = trim($data[1]);
            $append_hrn = " AND e.name_last LIKE '$last%' AND e.name_first LIKE '$name%'";
        }

        if (isset($transaction_nr) && $transaction_nr != "") {
            $append_tnr = " AND a.transaction_nr = '$transaction_nr' ";
        }

        $this->sql = "SELECT b.`machine_nr`,
                              a.`transaction_nr`,
                              a.`pid`,
                              a.`transaction_date`,
                              a.`dialyzer_serial_nr`,
                              a.`status`,
                              a.`request_flags`,
                              c.`dialyzer_type`,
                              c.`dialyzer_id`,
                              d.`encounter_nr`,
                              e.`name_first`,
                              e.`name_last`,
                              e.`name_middle`,
                              e.`date_birth`,
                              e.`sex`,
                              a.dialyzer_reuse AS dialyzer_count
                            FROM
                              seg_dialysis_transaction AS a
                              RIGHT JOIN seg_dialysis_machine AS b 
                                ON a.`machine_nr` = b.`machine_nr` 
                                " . $append_date . "
                              LEFT JOIN seg_dialysis_dialyzer AS c 
                                ON a.`dialyzer_serial_nr` = c.`dialyzer_serial_nr` 
                              LEFT JOIN seg_dialysis_prebill AS d 
                                ON d.`bill_nr` = a.`transaction_nr`
                              LEFT JOIN care_person AS e
                                ON e.`pid` = a.`pid` 
                            WHERE b.`machine_nr` = b.`machine_nr` " .
            $append_hrn . $append_tnr;

        $details = $db->Execute($this->sql);

        if ($details != FALSE) {
            $rows = $details->GetRows();
            return $rows;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    /**
     * @author Nick B. Alcala 06-16-2014
     * Get all machines and its users along with its
     * procedure reference data(if bill type is PHIC)
     * @param $date
     * @param $searchKey
     * @param $search_by
     * @param $transaction_nr
     * @return array/boolean
     */
    function getMachines($date, $searchKey, $search_by, $transaction_nr)
    {
        global $db;

        if ($date) {
            $cond[0] = $db->qstr(date('Y-m-d', strtotime($date)) . '%');
            $cond[3] = "LEFT";
        }

        if ($searchKey) {
            $cond[3] = "INNER";
            if (is_numeric($searchKey)) {
                $cond[1] = "AND sdt.pid = " . $db->qstr($searchKey);
            } else if (!is_numeric($searchKey) && strpos($searchKey, ",") > -1) {
                $name = explode(",", $searchKey);
                $name[0] = $db->qstr($name[0] . "%");
                $name[1] = $db->qstr($name[1] . "%");
                $cond[2] = "AND cp.name_last LIKE $name[0] AND cp.name_first LIKE $name[1]";
            }
        }

        if ($transaction_nr) {
            $cond[4] = "AND sdt.transaction_nr = " . $db->qstr($transaction_nr);
        }

        $this->sql = $db->Prepare("SELECT 
                                     sdm.machine_nr,
                                     sdp.encounter_nr,
                                     sdp.bill_nr,
                                     sdt.transaction_nr,
                                     sdt.pid,
                                     smop.entry_no,
                                     sdt.transaction_date,
                                     sdt.dialyzer_serial_nr,
                                     sdd.dialyzer_type,
                                     sdd.dialyzer_id,
                                     sdt.status,
                                     sdt.request_flags,
                                     cp.name_first,
                                     cp.name_last,
                                     cp.name_middle,
                                     cp.date_birth,
                                     cp.sex,
                                     ce.is_discharged,
                                     sdt.dialyzer_reuse AS dialyzer_count
                                   FROM
                                     seg_dialysis_machine AS sdm
                                     LEFT JOIN seg_dialysis_transaction AS sdt
                                       ON sdm.id = sdt.machine_nr
                                       AND sdt.transaction_date LIKE $cond[0] $cond[1]
                                     LEFT JOIN seg_dialysis_prebill AS sdp
                                       ON sdt.transaction_nr = sdp.bill_nr
                                     LEFT JOIN seg_dialysis_dialyzer AS sdd
                                       ON sdd.dialyzer_serial_nr = sdt.dialyzer_serial_nr
                                     $cond[3] JOIN care_person AS cp
                                       ON cp.pid = sdt.pid $cond[2]
                                     LEFT JOIN seg_misc_ops AS smo
                                       ON smo.encounter_nr = sdp.encounter_nr
                                     LEFT JOIN care_encounter as ce
                                       ON ce.encounter_nr = sdp.encounter_nr
                                     LEFT JOIN seg_misc_ops_details AS smop
                                       ON smo.refno = smop.refno
                                     AND (SUBSTR(
                                       sdt.transaction_nr,
                                       LENGTH(sdt.transaction_nr),
                                       1
                                     )) = smop.entry_no
                                     AND DATE_FORMAT(smop.op_date, '%Y-%m-%d') = DATE_FORMAT(
                                     sdt.transaction_date,
                                     '%Y-%m-%d'
                                   )");
        $rs = $db->Execute($this->sql);
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->GetRows();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function savePatientDetails($transaction_nr, $new_machine_nr, $dialyzer_nr, $tnDate)
    {
        global $db;
        //$db->debug = true;
        $history = $db->GetOne("SELECT 
                                        history 
                                    FROM 
                                        seg_dialysis_transaction 
                                    WHERE 
                                        transaction_nr=" . $db->qstr($transaction_nr)
        );

        $new_history = $history . "\n" . "Updated " . date('Y-m-d H:i:s') . "=" . $_SESSION["sess_user_name"] . " . ";

        $this->sql = "UPDATE seg_dialysis_transaction 
                                SET machine_nr=" . $db->qstr($new_machine_nr) . ", history=" . $db->qstr($new_history) .
            ", modify_id=" . $db->qstr($_SESSION["sess_user_name"]) .
            ", transaction_date=" . $db->qstr($tnDate) .
            ", modify_date=" . $db->qstr(date('Y-m-d H:i:s')) .
            ", dialyzer_serial_nr=" . $db->qstr($dialyzer_nr) .
            " WHERE transaction_nr= " . $db->qstr($transaction_nr);

        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            return TRUE;
        }
        $this->error_msg = $db->ErrorMsg();
        return FALSE;
    }

    function insertTransaction($tnr, $pid, $machineNr, $dialzyerSerialNr, $transDate)
    {
        global $db;
//        $db->debug = true;
        $history = "Created " . date('Y-m-d H:i:s') . "=" . $_SESSION["sess_temp_userid"] . " . ";
        $this->sql = $db->Prepare('INSERT INTO seg_dialysis_transaction
                                    (transaction_nr, pid, dialyzer_serial_nr, machine_nr, transaction_date, create_id,
                                    create_time, history, dialyzer_reuse)
                                    VALUES (?,?,?,?,?,?,?,?,?)');
        $result = $db->Execute($this->sql, array($tnr, $pid, $dialzyerSerialNr, $machineNr, $transDate, $_SESSION["sess_temp_userid"],
            date('Y-m-d H:i:s'), $history, 1));
        if ($result) {
            return true;
        }
        return false;
    }

    function getAllMachineNumbers($q)
    {
        global $db;

        $this->sql = "SELECT 
                            `machine_nr`
                        FROM
                            seg_dialysis_machine
                            WHERE machine_nr LIKE " . $db->qstr('%' . $q . '%') . "
                          LIMIT 10";

        $machines = $db->Execute($this->sql);

        if ($machines) {
            $list = array();
            $rows = $machines->getRows();
            foreach ($rows as $r) {
                $list[] = array(
                    'machine_nr' => $r['machine_nr']
                );
            }
            return $list;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function validateDialyzer($dialyzer_serial)
    {
        global $db;
        $this->sql = "SELECT 
                            dialyzer_serial_nr
                              FROM
                            seg_dialysis_dialyzer
                              WHERE
                            dialyzer_serial_nr = " . $db->qstr($dialyzer_serial);

        $validate = $db->GetRow($this->sql);

        if ($validate != NULL) {
            return $validate;
        }
    }

    /**
     * Edited by -xXLXx- (Trainee)
     * Edited on 04/03/2014
     */
    function getDialyzerList()
    {
        global $db;
        $this->sql = $db->Prepare("SELECT alt_service_code, name, name_short
                                    FROM seg_other_services
                                  WHERE name LIKE ?");

        $dialyzer = $db->Execute($this->sql, '%dialyzer%');
//        $db->debug = true;
        if ($dialyzer) {
            $list = $dialyzer->getRows();
            $dropdownList = array();
            foreach ($list as $l) {
                $dropdownList[$l['alt_service_code']] = $l['name'];
            }
            return $dropdownList;
        }
        return false;
    }

    function addNewDialyzer($serialNo, $serviceCode, $type)
    {
        global $db;

        $this->sql = $db->Prepare('INSERT INTO seg_dialysis_dialyzer (dialyzer_serial_nr, dialyzer_id, dialyzer_type)
                                    VALUES (?,?,?)');
        $result = $db->Execute($this->sql, array($serialNo, $serviceCode, $type));
        if ($result)
            return true;

        return false;
    }

    function addNewDialyzerOld($encounter, $service_code, $request_date, $pid, $dialyzer_serial, $transaction_nr)
    {
        global $db;
        $db->StartTrans();

        $input_month = substr($request_date, 0, 2);
        $input_day = substr($request_date, 3, 2);
        $input_year = substr($request_date, 6, 4);
        $input_time = substr($request_date, 10, 6);
        $complete_date = $input_year . "-" . $input_month . "-" . $input_day . " " . $input_time . ":00";

        $getNewMiscRefNo = "SELECT fn_get_new_refno_misc_srvc('" . $complete_date . "') AS newRefNo";
        $newMiscRefNo = $db->GetOne($getNewMiscRefNo);

        $getMiscDetails = "SELECT price,account_type FROM seg_other_services WHERE alt_service_code = " . $db->qstr($service_code);
        $miscDetails = $db->GetRow($getMiscDetails);

        $is_cash = '1';
        $request_source = 'RDU';
        $area = 'rdu';
        $quantity = '1';
        $entry_no = '1';


        $addDialyzerInMisc = "INSERT INTO seg_misc_service (refno, chrge_dte, encounter_nr, pid, is_cash, request_source, create_id, create_dt, area)
                                    VALUES (" . $db->qstr($newMiscRefNo) . ", " . $db->qstr($complete_date) . ", " . $db->qstr($encounter) . ", " . $db->qstr($pid)
            . ", " . $db->qstr($is_cash) . ", " . $db->qstr($request_source) . ", " . $db->qstr($_SESSION["sess_user_name"])
            . ", " . $db->qstr($complete_date) . ", " . $db->qstr($area) . ")";

        $addDialyzerInMiscDetails = "INSERT INTO seg_misc_service_details (refno, service_code, entry_no, account_type, adjusted_amnt, chrg_amnt, quantity)
                                        VALUES (" . $db->qstr($newMiscRefNo) . ", " . $db->qstr($service_code) . ", " . $db->qstr($entry_no) . ", " . $db->qstr($miscDetails['account_type'])
            . ", " . $db->qstr($miscDetails['price']) . ", " . $db->qstr($miscDetails['price']) . ", " . $db->qstr($quantity) . ")";

        $addDialyzerSerialNr = "INSERT INTO seg_dialysis_dialyzer (dialyzer_serial_nr, dialyzer_id, dialyzer_type)
                                        VALUES (" . $db->qstr($dialyzer_serial) . ", " . $db->qstr($service_code) . ", " . $db->qstr($dialyzer_type) . ")";

        $updatePatientDialyzer = "UPDATE seg_dialysis_transaction SET dialyzer_serial_nr = " . $db->qstr($dialyzer_serial) . " WHERE transaction_nr = " . $db->qstr($transaction_nr);

        $resultDialyzerInMisc = $db->Execute($addDialyzerInMisc);
        $resultDialyzerInMiscDetails = $db->Execute($addDialyzerInMiscDetails);
        $resultDialyzerSerialNr = $db->Execute($addDialyzerSerialNr);
        $resultPatientDialyzer = $db->Execute($updatePatientDialyzer);

        if ($resultDialyzerInMisc !== FALSE && $resultDialyzerInMiscDetails !== FALSE
            && $resultDialyzerSerialNr !== FALSE && $resultPatientDialyzer !== FALSE
        ) {
            $saveok = true;
        } else {
            $saveok = false;
        }


        if ($saveok) {
            $db->CompleteTrans();
            return TRUE;
        } else {
            $db->FailTrans();
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getLatestPatientTransaction($input_date, $input_data, $search_by, $transaction_nr)
    {
        global $db;

        if (isset($input_date) && $input_date != "") {
            $input_month = substr($input_date, 0, 2);
            $input_day = substr($input_date, 3, 2);
            $input_year = substr($input_date, 6, 4);

            $complete_date = $input_year . "-" . $input_month . "-" . $input_day;

            // $append_date = " AND a.transaction_date LIKE '$complete_date%'";

            $complete_date_time = $complete_date . " " . "23:59:59";
        }

        if (isset($input_data) && $input_data != "" && $search_by == "by_hrn") {
            $append_hrn = " AND a.pid =" . $db->qstr($input_data);
        } else if (isset($input_data) && $input_data != "" && $search_by == "by_name") {

            # for handling with comma with space input.
            $data = explode(",", trim($input_data));
            $last = trim($data[0]);
            $name = trim($data[1]);
            $append_hrn = " AND e.name_last LIKE '$last%' AND e.name_first LIKE '$name%'";
        }

        if (isset($transaction_nr) && $transaction_nr != "") {
            $append_tnr = " AND a.transaction_nr = '$transaction_nr' ";
        }

        $this->sql = "SELECT 
                              b.`machine_nr`,
                              a.`transaction_nr`,
                              a.`pid`,
                              a.`transaction_date`,
                              a.`dialyzer_serial_nr`,
                              a.`bill_nr`,
                              a.`status`,
                              a.`request_flags`,
                              c.`dialyzer_type`,
                              c.`dialyzer_id`,
                              d.`encounter_nr`,
                              e.`name_first`,
                              e.`name_last`,
                              e.`name_middle`,
                              e.`date_birth`,
                              e.`sex`,
                              (SELECT 
                                COUNT(n.`dialyzer_serial_nr`) 
                           FROM
                                seg_dialysis_transaction n 
                              WHERE n.`transaction_date` <= " . $db->qstr($complete_date_time) . "
                              AND n.`dialyzer_serial_nr` = a.`dialyzer_serial_nr` 
                              GROUP BY n.`dialyzer_serial_nr`) AS dialyzer_count 
                            FROM
                              seg_dialysis_transaction AS a 
                              RIGHT JOIN seg_dialysis_machine AS b 
                                ON a.`machine_nr` = b.`machine_nr` 
                            
                              LEFT JOIN seg_dialysis_dialyzer AS c 
                                ON a.`dialyzer_serial_nr` = c.`dialyzer_serial_nr` 
                              LEFT JOIN seg_dialysis_prebill AS d 
                                ON d.`bill_nr` = a.`bill_nr` 
                              LEFT JOIN care_person AS e 
                                ON e.`pid` = a.`pid` 
                            WHERE b.`machine_nr` = b.`machine_nr` " .
            $append_hrn . $append_tnr;

        $details = $db->Execute($this->sql);
        if ($details != FALSE) {
            return $details->FetchRow();
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    /**
     * Populate list for Dialysis billing list
     * Searches for cashier payment with OR #. Searches for cmap and lingap without OR.
     * @param $encounter_nr
     * @return bool
     */
    #updated by art 10/06/2014
    function getDialysisTransactionList($encounter_nr)
    {
        global $db;
        //$db->debug = true;

        $this->sql = $db->Prepare("SELECT
                                  pb.`bill_nr`,
                                      pb.`request_flag` AS STATUS,
                                  t.`transaction_date`,
                                  pb.`amount`,
                                  pb.`hdf_amount`,
                                  pb.`subsidy_amount`,
                                  pb.`bill_type`,
                                  ce.`is_discharged`,
                                      pr.`or_no`
                                FROM
                                  `seg_dialysis_prebill` pb
                                      LEFT JOIN seg_dialysis_transaction t ON t.`transaction_nr` = pb.`bill_nr`
                                      LEFT JOIN care_encounter ce ON ce.`encounter_nr` = pb.`encounter_nr`
                                      LEFT JOIN seg_pay_request pr ON pr.`service_code` = pb.`bill_nr` AND pr.`ref_source` = 'db'
                                      LEFT JOIN seg_pay sp ON sp.`or_no` = pr.`or_no`  
                                WHERE pb.`encounter_nr` = ?
                                      AND pb.`request_flag` IN ('cmap', 'lingap', 'paid','manual')
                                      AND sp.`cancel_date` IS NULL ");

        #echo $this->sql;
        $result = $db->Execute($this->sql, $encounter_nr);

        if ($result != FALSE) {
            return $result;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getPaidBils($encounter_nr)
    {
        global $db;

        if (isset($encounter_nr) && $encounter_nr != "") {
            $append_encounter = " AND `encounter_nr` =  '" . $encounter_nr . "'";
        }

        $this->sql = "SELECT 
            bill_nr 
            FROM `seg_dialysis_prebill` 
            WHERE request_flag IS NOT NULL " . $append_encounter;

        $result = $db->Execute($this->sql);

        if ($result != FALSE) {
            return $result;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getLatestTransaction($pid)
    {
        global $db;

        if (isset($pid) && $pid != "") {
            $append_pid = " WHERE pid='" . $pid . "' ORDER BY transaction_date DESC LIMIT 1;";
        }

        $this->sql = "SELECT `transaction_nr` , `dialyzer_serial_nr`
            FROM seg_dialysis_transaction " .
            $append_pid;

        $result = $db->Execute($this->sql);
        if ($result != FALSE) {
            return $result->FetchRow();
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    #End Aleeya
    #added by Keith March 6, 2014 10:00 AM

    function DisableDialysisEncounter($enc)
    {
        global $db;
        $encounter_nr = $db->qstr($enc);
        $discharge_date = date('Y-m-d');
        $discharge_time = date('H:i:s');

        $this->sql = "UPDATE care_encounter \n" .
            "SET is_discharged ='1', discharge_date=" . $db->qstr($discharge_date) . ", discharge_time=" . $db->qstr($discharge_time) . "\n" .
            "WHERE encounter_nr =$encounter_nr";

        // var_dump($this->sql);exit;
        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function EnableDialysisEncounter($enc)
    {
        global $db;
        $encounter_nr = $db->qstr($enc);
        $discharge_date = date('Y-m-d');
        $discharge_time = date('H:i:s');

        $this->sql = "UPDATE care_encounter \n" .
            "SET is_discharged ='0', discharge_date=NULL, discharge_time=NULL" . "\n" .
            "WHERE encounter_nr =$encounter_nr";

        // var_dump($this->sql);exit;
        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function GetEncounterDischargeFlag($encounter)
    {
        global $db;

        $this->sql = "SELECT is_discharged FROM care_encounter WHERE encounter_nr=" . $db->qstr($encounter);
        if ($details = $db->Execute($this->sql)) {
            return $details;
        } else {
            return false;
        }
    }

    public static function hasFinalBill($encounterNr)
    {
        global $db;
        return $db->GetOne("SELECT
                              is_final
                            FROM seg_billing_encounter
                            WHERE encounter_nr=? AND
                            is_deleted IS NULL AND
                            is_final=1", array(
            $encounterNr
        ));
    }

    function getBills($search = "", $pid = "", $offset = 0, $maxRows = 10)
    {
        global $db;
//        $db->debug = true;
        $pid = $db->qstr($pid);
        if ($orno) {
            $orno = $db->qstr($orno);
            if ($orno)
                $limit_or = "AND pr.or_no<>$orno";
        }

        $where = 'd.`pid`= ' . $pid . ' AND p.bill_nr LIKE "' . $search . '%" AND p.`encounter_nr`=d.`encounter_nr` AND c.`is_discharged`!= 1';

        $this->sql = "SELECT SUM(IF(p.request_flag is null, 1, 0)) as not_paid,
                            SUM(IF(p.request_flag is null, p.amount, 0)) as amount,
                            SUM(IF(p.request_flag is null, p.hdf_amount, 0)) as hdf_amount,
                            SUM(IF(p.request_flag is null, p.subsidy_amount, 0)) as subsidy_amount,
                            p.encounter_nr, substr(p.bill_nr,1,10) as bill_nr, d.`request_date`,
                            d.`pid`, fn_get_person_name(d.`pid`) AS fullname, p.`request_flag` \n" .
            "FROM seg_dialysis_prebill p, seg_dialysis_request d \n" .
            "INNER JOIN care_encounter c ON c.`encounter_nr`=d.`encounter_nr` \n" .
            "WHERE " . $where . "\n" .
            "GROUP BY p.encounter_nr \n" .
            "ORDER BY d.request_date DESC, p.bill_nr ASC \n";
        #echo $this->sql;
        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    /**
     * Added by Gervie 03-26-2017
     * @return total pre-bill payments of the encounter
     */
    function getTotalPrebillPayments($encounterNr) {
        global $db;

        $this->sql = "SELECT SUM(l.`amount`) FROM seg_dialysis_ledger l
                      INNER JOIN seg_dialysis_prebill p 
                        ON p.`encounter_nr` = l.`encounter_nr`
                        AND p.`bill_nr` = l.`bill_nr` 
                      WHERE p.`request_flag` IS NULL
                        AND l.`encounter_nr` = ".$db->qstr($encounterNr);

        return $db->GetOne($this->sql);
    }

    function getBillsByClassification($encounterNr, $classification)
    {
        global $db;

        $this->sql = $db->Prepare("SELECT p.encounter_nr, p.bill_nr, if(p.bill_type='PH','Dialysis Pre-Bill PHIC','Dialysis Pre-Bill NPHIC') as bill_type, d.`request_date`, d.`pid`, fn_get_person_name(d.`pid`) AS fullname, p.amount, p.`request_flag` " .
            "FROM  seg_dialysis_prebill p " .
            "INNER JOIN seg_dialysis_request d ON p.encounter_nr = d.encounter_nr " .
            "WHERE p.discountid = ? AND p.encounter_nr = ? " .
            "ORDER BY p.bill_type ASC, d.request_date DESC, p.bill_nr ASC ");

        $result = $db->Execute($this->sql, array($classification, $encounterNr));
        if ($result && $result->RecordCount() > 0) {
            return $result->getRows();
        }
        return false;
    }

    function getBillDetails($billNr, $pid = "")
    {
        global $db;
        #$db->debug = true;
        $pid = $db->qstr($pid);
        $where = 'd.`pid`= ' . $pid . ' AND p.bill_nr LIKE "' . $billNr . '%" AND p.`encounter_nr`=d.`encounter_nr` AND c.`is_discharged`!= 1 AND p.request_flag is null';
        $this->sql = "SELECT p.`encounter_nr`, p.`discountid`, p.`bill_nr`, p.`bill_type`, d.`request_date`, d.`pid`, fn_get_person_name(d.`pid`) AS fullname, \n" .
            "IF((p.`amount` + p.`hdf_amount`) - (SELECT IFNULL(SUM(a.`amount`),0) FROM seg_dialysis_ledger a WHERE a.`bill_nr` = p.`bill_nr` AND is_deleted <> 1) > 0,\n" .
            "(p.`amount` + p.`hdf_amount`) - (SELECT IFNULL(SUM(a.`amount`),0) FROM seg_dialysis_ledger a WHERE a.`bill_nr` = p.`bill_nr` AND is_deleted <> 1),0)AS amount, p.`request_flag` \n" .
            "FROM seg_dialysis_prebill p, seg_dialysis_request d \n" .
            "INNER JOIN care_encounter c ON c.`encounter_nr`=d.`encounter_nr` \n" .
            "WHERE " . $where . "\n" .
            "ORDER BY d.request_date DESC, p.bill_nr ASC \n";
        #die($this->sql);
        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function getDialyzerInfo($tnr)
    {
        global $db;
        $this->sql = $db->Prepare('SELECT dt.dialyzer_serial_nr,
                                  dt.machine_nr, dt.transaction_date, d.dialyzer_type,
                                  dt.dialyzer_reuse
                                   FROM seg_dialysis_dialyzer d
                                   INNER JOIN seg_dialysis_transaction dt ON d.dialyzer_serial_nr = dt.dialyzer_serial_nr
                                   WHERE dt.transaction_nr = ?');
        //$db->debug = true;
        $result = $db->Execute($this->sql, $tnr);
        if ($result) {
            $res = $result->FetchRow();
            return $res;
        }
        return false;

    }

    /**
     * Created by -xXLXx- (Trainee)
     * Created on 04/02/2014
     * This function is used to get Patient's last dialyzer_serial_nr, resusex
     * @param String pid
     * @return String Array arr_last_dialyzer
     */
    function getLastDialyzer($pid)
    {
        global $db;
        //$db->debug = true;
        $this->sql = $db->Prepare("SELECT a.`dialyzer_serial_nr`, MAX(a.dialyzer_reuse)+1 AS reusex, " .
            "dialyzer_type, request_flag " .
            "FROM `seg_dialysis_transaction` a " .
            "INNER JOIN `seg_dialysis_dialyzer` b " .
            "ON a.`dialyzer_serial_nr` = b.`dialyzer_serial_nr` " .
            "INNER JOIN `seg_dialysis_prebill` c " .
            "ON a.`transaction_nr` = c.`bill_nr` " .
            "WHERE `pid` = ? " .
            "GROUP BY a.`dialyzer_serial_nr` " .
            "ORDER BY a.transaction_date DESC " .
            "LIMIT 1");

        if ($this->result = $db->Execute($this->sql, $pid)) {
            return $this->result->FetchRow();
        } else {
            return false;
        }
    }

    function updateDialyzerReuse($tnr, $reuse)
    {
        global $db;
        //$db->debug = true;
        $this->sql = $db->Prepare("UPDATE seg_dialysis_transaction
                                    SET dialyzer_reuse = ?
                                    WHERE transaction_nr = ?");

        if ($this->result = $db->Execute($this->sql, array($reuse, $tnr))) {
            return true;
        }
        return false;
    }

    public function updateBillingDoctorFee($encounterNr, $doctorId)
    {
        global $db;
        $this->sql = $db->Prepare('SELECT count(*) AS doctor_exists
                                    FROM seg_encounter_privy_dr
                                    WHERE encounter_nr = ?
                                    AND dr_nr = ?');
        $result = $db->Execute($this->sql, array($encounterNr, $doctorId));
        if ($result) {
            $row = $result->FetchRow();
            if ($row['doctor_exists'] > 0) {
                $saveOk = $this->updateDoctorFee($encounterNr, $doctorId);
            } else {
                $rs = $this->insertDoctorFee($encounterNr, $doctorId);
                if ($rs) {
                    $saveOk = $this->insertSegOpsChargeDr($encounterNr, $doctorId);
                }

            }
        }

        if ($saveOk) {
            return true;
        } else {
            return false;
        }
    }

    #edited  by art 10/27/14 now() to strftime("%Y-%m-%d %H:%M:%S")
    private function insertDoctorFee($encounterNr, $doctorId)
    {
        global $db;
        $this->hemo_chrg = $this->getPFCharge($encounterNr);
        $this->getAddedOpsInfo($encounterNr);
        $this->sql = $db->Prepare('INSERT INTO seg_encounter_privy_dr
                                    (encounter_nr, dr_nr, entry_no, dr_role_type_nr, dr_level, days_attended,
                                    dr_charge, create_id, create_dt, modify_id) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $results = $db->Execute($this->sql, array($encounterNr, $doctorId, 1, 7, 1, 0, $this->hemo_chrg, $_SESSION['sess_user_name'], strftime("%Y-%m-%d %H:%M:%S"), $_SESSION['sess_user_name']));
        if ($results) {
            return true;
        }
        return false;
    }

    private function updateDoctorFee($encounterNr, $doctorId)
    {
        global $db;
        $this->hemo_chrg = $this->getPFCharge($encounterNr);
        $this->sql = $db->Prepare('UPDATE seg_encounter_privy_dr SET dr_charge = ?,
                                  modify_id = ?, modify_dt = NOW()
                                  WHERE encounter_nr = ? AND dr_nr = ?');
        $results = $db->Execute($this->sql, array($this->hemo_chrg, $_SESSION['sess_user_name'], $encounterNr, $doctorId));
        if ($results) {
            return true;
        }
        return false;
    }

    public function updateBilling($pid, $selectedEncounter, $tnDate, $mode)
    {
        global $db;
        $db->StartTrans();

        /** @var SegOps $segOps */
        $segOps = new SegOps();
        $hospObj = new Hospital_Admin();
        #$data = $this->getTransactionForBilling($pid); #commented by art 10/04/14
        $data = $this->getTransactionForBilling($selectedEncounter); #added by art 10/04/14

        if ($segOps->isHouseCase($data['encounter_nr']))
            $nPCF = HOUSE_CASE_PCF;
        else
            $nPCF = $hospObj->getDefinedPCF();

        $hemo = $this->getHemodialysis($nPCF);
        $data = array_merge($data, $hemo);
        $data['laterality'] = $data['for_laterality'];
        $data['charge'] = intval($data['rvu']) * intval($data['multiplier']);
        $data['user'] = $_SESSION['sess_user_name'];
        $data['opDate'] = $tnDate;
        $data['billDate'] = $tnDate;

        if ($mode == 'save') {
            $refNo = $segOps->getLastRefNo($data['admittingDate'], $data['encNr'], $data['code']);
            if ($refNo) {
                $data['refno'] = $refNo;
                $saveOk1 = $segOps->addProcedure2($data);
            } else {
                $saveOk1 = $segOps->addProcedure($data);
            }
            $saveOk2 = $this->updateBillingDoctorFee($selectedEncounter, $data['requesting_doctor']);

            if ($saveOk1 && $saveOk2) {
                $db->CompleteTrans();
                return true;
            } else {
                $db->FailTrans();
                return false;
            }
        } else {
            return true;
        }
    }

    //added by Nick 05-13-2014
    public function insertSegOpsChargeDr($encNr, $drNr)
    {
        global $db;
        $doc_info = $this->getAddedDoctorInfo($encNr, $drNr);
        $newEntryNo = $this->getNextDrChrgEntryNo($encNr);
        $ops = $this->opsInfo;
        $this->sql = $db->Prepare("INSERT INTO seg_ops_chrg_dr
                                                (encounter_nr,
                                                 dr_nr,
                                                 dr_role_type_nr,
                                                 entry_no,
                                                 role_type_level,
                                                 ops_refno,
                                                 ops_entryno,
                                                 ops_code,
                                                 rvu,
                                                 multiplier)
                                    VALUES (?,?,?,?,?,?,?,?,?,?)");

        $data = array(
            $encNr,
            $doc_info['dr_nr'],
            $doc_info['dr_role_type_nr'],
            $newEntryNo,
            1,
            $ops['refno'],
            $ops['entry_no'],
            $ops['ops_code'],
            $ops['rvu'],
            $ops['multiplier']
        );

        $rs = $db->Execute($this->sql, $data);
        if ($rs) {
            return true;
        } else {
            return false;
        }

    }

    //added By Nick 05-14-2014
    function getAddedOpsInfo($enc)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      b.* 
                                    FROM
                                      seg_misc_ops AS a 
                                      INNER JOIN seg_misc_ops_details AS b 
                                        ON a.`refno` = b.`refno` 
                                    WHERE encounter_nr = ? 
                                      AND b.`ops_code` = ?");
        $rs = $db->Execute($this->sql, array($enc, HEMODIALYSIS));
        if ($rs) {
            if ($rs->RecordCount()) {
                $this->opsInfo = $rs->FetchRow();
                return $this->opsInfo;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //added By Nick 05-13-2014
    function getAddedDoctorInfo($enc, $dr_nr)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT * FROM seg_encounter_privy_dr WHERE encounter_nr = ? AND dr_nr = ?");
        $rs = $db->Execute($this->sql, array($enc, $dr_nr));
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->FetchRow();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //added by Nick 05-13-2014
    function getNextDrChrgEntryNo($enc)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      entry_no 
                                    FROM
                                      seg_ops_chrg_dr 
                                    WHERE encounter_nr = ?
                                    ORDER BY entry_no DESC 
                                    LIMIT 1");
        $rs = $db->Execute($this->sql, $enc);
        if ($rs) {
            if ($rs->RecordCount()) {
                $row = $rs->FetchRow();
                return $row['entry_no'] + 1;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //added by Nick 05-16-2014
    function getBillType($bill_nr)
    {
        global $db;
        #$db->debug=true;
        $this->sql = $db->Prepare("SELECT bill_type FROM seg_dialysis_prebill WHERE bill_nr = ?");
        $rs = $db->Execute($this->sql, $bill_nr);
        if ($rs) {
            if ($rs->RecordCount()) {
                $row = $rs->FetchRow();
                return (mb_strtoupper($row['bill_type']) == 'PH');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
    ** edited by art 10/04/14
    ** change parameter from pid to enc
    */
    public function getTransactionForBilling($enc)
    {
        global $db;
//        $db->debug=  true;
        #$clsEncounter = new Encounter();
        //get encounter do not ignore dialysis. Function reuse and I don't know why it has a wrong spelling.
        #$dialysisEnc = $clsEncounter->getLastestEncounter($pid);
        //get encounter ignore dialysis
        #$latestEnc = $clsEncounter->getLatestEncounter($pid);

        $sql = "SELECT admission_dt FROM care_encounter WHERE encounter_nr =" . $enc;
        $admission_dt = $db->GetOne($sql);


        $this->sql = $db->Prepare('SELECT dr.requesting_doctor, 1 AS num_sessions,
                                  dt.transaction_date AS special_dates
                                FROM seg_dialysis_prebill pb
                                  INNER JOIN seg_dialysis_transaction dt
                                  ON pb.bill_nr = dt.transaction_nr
                                INNER JOIN seg_dialysis_request dr
                                  ON dr.encounter_nr = pb.encounter_nr
                                WHERE pb.encounter_nr = ?
                                AND dt.transaction_date IS NOT NULL
                                GROUP BY pb.encounter_nr');

        #$results = $db->Execute($this->sql, $dialysisEnc['encounter_nr']);
        $results = $db->Execute($this->sql, $enc);
        if ($results) {
            $row = $results->FetchRow();
            #$row['encNr'] = $dialysisEnc['encounter_nr'];
            #$row['admittingDate'] = $latestEnc['admission_dt'];
            $row['encNr'] = $enc;
            $row['admittingDate'] = $admission_dt;
            return $row;
        }
        return false;
    }

    public function getHemodialysis($nPCF)
    {
        global $db;
        //$db->debug = true;
        $this->sql = $db->Prepare(
            'SELECT cp.code, cp.description, op.rvu, ? AS multiplier, cp.for_laterality
            FROM seg_case_rate_packages AS cp
            INNER JOIN seg_ops_rvs AS op ON cp.code=op.code
            WHERE op.is_active<> 0 AND cp.case_type="p" AND cp.description LIKE ?');
        $result = $db->Execute($this->sql, array($nPCF, 'hemodialysis%'));
        if ($result) {
            return $result->FetchRow();
        }
        return false;
    }

    //added by Nick 05-16-2014
    function getPFCharge($enc)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      SUM(c.pf) AS charge
                                    FROM
                                      seg_misc_ops AS a 
                                      INNER JOIN seg_misc_ops_details AS b 
                                        ON a.refno = b.refno 
                                      INNER JOIN seg_case_rate_packages AS c 
                                        ON c.code = b.ops_code 
                                    WHERE a.encounter_nr = ?");
        $rs = $db->Execute($this->sql, $enc);
        if ($rs) {
            if ($rs->RecordCount()) {
                $row = $rs->FetchRow();
                return $row['charge'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //added by Nick 06-12-2014
    function setHemoOpDate($enc, $date, $entry_no)
    {
        global $db;

        $date = date('Y-m-d', strtotime($date));

        $this->sql = $db->Prepare("SELECT
                                      refno
                                    FROM
                                      seg_misc_ops
                                    WHERE encounter_nr = $enc
                                    ORDER BY refno DESC");
        $refno = $db->GetOne($this->sql);

        $this->sql = $db->Prepare("UPDATE
                                      seg_misc_ops_details AS smop
                                    SET
                                      smop.op_date = ?,
                                      smop.special_dates = ?
                                    WHERE smop.entry_no = ?
                                    AND smop.refno = ?");
        $rs = $db->Execute($this->sql, array(
            $date,
            $date,
            $entry_no,
            $refno
        ));

        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /*added by art 09/10/2014 -----------------------------------*/

    function getUnpaidBills($enc)
    {
        global $db;

        $this->sql = $db->Prepare("SELECT a.`bill_nr` FROM seg_dialysis_prebill a WHERE a.`encounter_nr` = ? AND ISNULL(a.`request_flag`)");

        if ($rs = $db->Execute($this->sql, $enc)) {
            if ($rs->RecordCount()) {
                return $rs;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getBillNrDetails($bill_nr)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT a.`amount` ,a.`request_flag`, a.`hdf_amount`, a.`subsidy_amount` FROM `seg_dialysis_prebill` a WHERE a.`bill_nr` = ?");
        
        $rs = $db->Execute($this->sql, $bill_nr);
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->FetchRow();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getPayType()
    {
        global $db;
        $this->sql = $db->Prepare("SELECT `pay_id`,`name` FROM seg_dialysis_pay_type");
        if ($rs = $db->Execute($this->sql)) {
            return $rs;
        } else {
            return FALSE;
        }
    }

    function savePay($data)
    {
        global $db;
        define(CASENUMBERLENGTH, 10);
        $encounter_nr = substr($data['bill_nr'], 0, CASENUMBERLENGTH);
        $sql = "SELECT history FROM seg_dialysis_ledger WHERE ref_no =" . $db->qstr($data['ref_no']);

        $isExists = $db->GetOne($sql);

        if ($isExists == NULL) {
            $create_date = date('Y-m-d H:i:s');
            $history = 'Create ' . $db->qstr($_SESSION["sess_temp_userid"]) . ' ' . date('Y-m-d H:i:s') . "\n";
            $fldarray = array(
                'encounter_nr' => $db->qstr($encounter_nr),
                'bill_nr' => $db->qstr($data['bill_nr']),
                'amount' => $db->qstr($data['amount']),
                'pay_type' => $db->qstr($data['pay_type']),
                'control_nr' => $db->qstr($data['control_nr']),
                'description' => $db->qstr($data['description']),
                'create_id' => $db->qstr($_SESSION["sess_temp_userid"]),
                'create_date' => $db->qstr($create_date),
                'history' => $db->qstr($history)
            );
        } else {
            if ($data['delete'] == 1) {
                $history = $isExists . ' Delete ' . $db->qstr($_SESSION["sess_temp_userid"]) . ' ' . date('Y-m-d H:i:s') . "\n";
            } else {
                $history = $isExists . ' Update ' . $db->qstr($_SESSION["sess_temp_userid"]) . ' ' . date('Y-m-d H:i:s') . "\n";
            }

            $modify_date = date('Y-m-d H:i:s');
            $fldarray = array(
                'ref_no' => $db->qstr($data['ref_no']),
                'control_nr' => $db->qstr($data['control_nr']),
                'amount' => $db->qstr($data['amount']),
                'pay_type' => $db->qstr($data['pay_type']),
                'description' => $db->qstr($data['description']),
                'modify_id' => $db->qstr($_SESSION["sess_temp_userid"]),
                'modify_date' => $db->qstr($modify_date),
                'history' => $db->qstr($history),
                'is_deleted' => $db->qstr($data['delete']),
            );
        }

        $bsuccess = $db->Replace('seg_dialysis_ledger', $fldarray, array('ref_no'));
        if ($bsuccess) {
            return true;
        } else {
            return FALSE;
        }
    }

    function getPrebillPayments($bill_nr)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      a.`ref_no`,
                                      a.`bill_nr`,
                                      a.`amount`,
                                      a.`pay_type`,
                                      a.`control_nr`,
                                      a.`description` 
                                    FROM
                                      `seg_dialysis_ledger` a 
                                    WHERE a.`bill_nr` = ? AND a.`is_deleted` <> 1");
        if ($rs = $db->Execute($this->sql, $bill_nr)) {
            if ($rs->RecordCount()) {
                return $rs;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getTotalAmountPayed($bill_nr)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      SUM(amount) AS total_pay 
                                    FROM
                                      `seg_dialysis_ledger` 
                                    WHERE `bill_nr` = ?
                                      AND `is_deleted` <> 1 ");
        $rs = $db->Execute($this->sql, $bill_nr);
        if ($rs) {
            if ($rs->RecordCount()) {
                $row = $rs->FetchRow();
                return $row['total_pay'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getPayDetails($ref_no)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      a.`ref_no`,
                                      a.`amount`,
                                      a.`control_nr`,
                                      a.`bill_nr`,
                                      a.`description`,
                                      a.`pay_type` 
                                    FROM
                                      `seg_dialysis_ledger` a 
                                    WHERE a.`ref_no` = ?");

        if ($rs = $db->Execute($this->sql, $ref_no)) {
            $row = $rs->FetchRow();
            return $row;
        } else {
            return false;
        }

    }

    function applyPay($bill_nr)
    {
        global $db;
        #$db->debug = true;
        define(CASENUMBERLENGTH, 10);
        $bill = substr($bill_nr, 0, CASENUMBERLENGTH) . '-' . substr($bill_nr, CASENUMBERLENGTH - strlen($bill_nr));
        $this->sql = $db->Prepare("UPDATE seg_dialysis_prebill SET request_flag = 'manual' WHERE bill_nr = ?");
        $rs = $db->Execute($this->sql, $bill);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }
    /*end art-----------------------------------*/

    #added by KENTOOT 09-15-2014
    function getUnusedDialysisPrebill($nr, $is_final, $excludeUnpaid = true)
    {
        global $db;
        if ($is_final)
            $cond = "AND EXISTS(SELECT * FROM seg_billing_encounter sbe WHERE sbe.encounter_nr=pb.encounter_nr)";
        else
            $cond = "AND NOT EXISTS(SELECT * FROM seg_billing_encounter sbe WHERE sbe.encounter_nr=pb.encounter_nr)";

        $excludeUnpaidCondition = '';
        if($excludeUnpaid)
            $excludeUnpaidCondition = "AND pb.`request_flag` IN ('cmap', 'lingap', 'paid','manual')";

        $this->sql = "SELECT
                          pb.bill_nr,
                          pb.request_flag AS STATUS,
                          t.transaction_date,
                          pb.amount,
                          pb.bill_type,
                          ce.is_discharged,
                          pr.or_no 
                        FROM
                          seg_dialysis_prebill pb
                          LEFT JOIN seg_dialysis_transaction t 
                            ON t.transaction_nr = pb.bill_nr
                          LEFT JOIN care_encounter ce 
                            ON ce.encounter_nr = pb.encounter_nr
                          LEFT JOIN seg_pay_request pr 
                            ON pr.service_code = pb.bill_nr
                          LEFT JOIN seg_billing_encounter sbe 
                            ON sbe.encounter_nr = pb.encounter_nr
                        WHERE pb.encounter_nr = ?
                        $excludeUnpaidCondition
                        AND t.transaction_date IS NULL
                        $cond";
        if ($this->result = $db->Execute($this->sql,$nr)) {
            return $this->result;
        } else {
            return false;
        }
    }

    #added by KENTOOT 09-15-2014
    function updateDialysisPrebill($new_nr, $old_nr, $is_final, $excludeUnpaid = true)
    {
        global $db;
        #$db->debug = true;

        if ($is_final)
            $cond = ' AND EXISTS(SELECT * FROM seg_billing_encounter sbe WHERE sbe.encounter_nr=pb.encounter_nr)';
        else
            $cond = 'AND NOT EXISTS(SELECT * FROM seg_billing_encounter sbe WHERE sbe.encounter_nr=pb.encounter_nr)';

        $excludeUnpaidCondition = '';
        if($excludeUnpaid)
            $excludeUnpaidCondition = "AND pb.request_flag IN ('paid','cmap','lingap','manual')";

        $this->sql = "UPDATE seg_dialysis_prebill pb 
                        LEFT JOIN seg_dialysis_transaction t ON t.transaction_nr = pb.bill_nr
                        LEFT JOIN seg_pay_request pr ON pr.service_code = pb.bill_nr
                        LEFT JOIN seg_billing_encounter sbe ON sbe.encounter_nr = pb.encounter_nr
                       SET pb.encounter_nr = ?
                       WHERE pb.encounter_nr = ?
                       $excludeUnpaidCondition
                       AND t.transaction_date IS NULL
                       $cond";

        if ($this->result = $db->Execute($this->sql,array($new_nr,$old_nr))) {
            return $this->result;
        } else {
            return false;
        }
    }

    function isPhicTrxn($transaction_nr)
    {
        global $db;
        $this->sql = "SELECT bill_type FROM seg_dialysis_prebill WHERE bill_nr = " . $db->qstr($transaction_nr);
        $rs = $db->GetOne($this->sql);
        if ($rs == 'PH') {
            return true;
        } else {
            return false;
        }
    }

    function updateBillType($transaction_nr, $is_phic)
    {
        if ($is_phic == 'true')
            $type = 'PH';
        else
            $type = 'NPH';

        global $db;
        $this->sql = $db->Prepare("UPDATE seg_dialysis_prebill
                                    SET bill_type = ?
                                    WHERE bill_nr = ?");

        if ($this->result = $db->Execute($this->sql, array($type, $transaction_nr))) {
            return true;
        }
        return false;

    }
// Added by Matsuu 01172017
    // edited by Matsu 02242017
    function getPHICTransaction($pid,$limit=1){
        
          global $db;

          $lastTransSql = "SELECT encounter_nr FROM seg_dialysis_request WHERE pid = ".$db->qstr($pid)." ORDER BY request_date DESC";
          $getLastTransaction = $db->GetOne($lastTransSql);

          $this->sql = "SELECT *, COUNT(*) AS 'bbtype' FROM seg_dialysis_prebill WHERE encounter_nr = ".$db->qstr($getLastTransaction)." GROUP BY bill_type";
          
          if($result = $db->Execute($this->sql)) {
                  $ret = array();

            while($row = $result->FetchRow()) {
                if($row['bill_type'] == 'PH') {
                            $ret['ph_type'] = $row['bbtype'];
                            $ret['ph_amount'] = $row['amount'];
                            $ret['ph_hdf'] = $row['hdf_amount'];
                            $ret['sub_class'] = $row['subsidy_class'];
                        }

                if($row['bill_type'] == 'NPH') {
                            $ret['nph_type'] = $row['bbtype'];
                            $ret['nph_amount'] =$row['amount'];
                            $ret['nph_hdf'] = $row['hdf_amount'];
                            $ret['n_sub_amount'] = $row['subsidy_amount'];
                            $ret['n_sub_class'] = $row['subsidy_class'];
                  }
            }

                  return $ret;
                }
                else {
            return false;
                }
    }
// Ended by Matsuu 01172017

    // added by Matsuu 01272017
function getPreviousDialysisEnc($pid,$encounter_nr){
    global $db;
    $this->sql = " SELECT 
                  ce.`encounter_nr`
                FROM
                 care_encounter AS ce 
                  LEFT JOIN   seg_billing_encounter AS sbe 
                    ON ce.`encounter_nr` = sbe.`encounter_nr`
                WHERE ce.`pid` =".$db->qstr($pid)."
                  AND sbe.`is_final` = 1 
                  AND ce.`encounter_type` = '5'
                  AND ce.`encounter_nr` < ".$db->qstr($encounter_nr)."
                  ORDER BY ce.`encounter_nr` DESC
                  LIMIT 1" ;
    if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return FALSE;
        }else{
            return FALSE;
        }
}
// Ended by Matsuu

    function getSessionDates($enc_nr = '')
    {
        global $db;

        if (empty($enc_nr))
            return false;

        $this->sql = "SELECT transaction_date
            FROM seg_dialysis_prebill pre
            INNER JOIN seg_dialysis_transaction trans ON pre.`bill_nr` = trans.`transaction_nr`
            WHERE pre.`bill_type` = 'PH' AND pre.`encounter_nr` = " . $db->qstr($enc_nr) . "
            ORDER BY transaction_date";

        if ($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }

    }

    public static function getPreviousDiagnosis($pid)
    {
        global $db;
        return $db->GetOne("SELECT
                        er_opd_diagnosis
                     FROM care_encounter
                     WHERE pid=? AND encounter_type=?
                     ORDER BY encounter_nr DESC", array(
            $pid,
            DIALYSIS_PATIENT
        ));
    }

    /**
     * @param $encounterNr
     * @return false|Array
     */
    public static function getLatestSessionByEncounter($encounterNr)
    {
        global $db;
        return $db->GetRow("SELECT
                              prebill.bill_type,
                              prebill.request_flag,
                              prebill.amount,
                              prebill.discountid,
                              transactions.dialyzer_serial_nr,
                              transactions.machine_nr,
                              transactions.transaction_date
                            FROM
                              seg_dialysis_prebill AS prebill
                              INNER JOIN seg_dialysis_transaction AS transactions
                                ON prebill.bill_nr = transactions.transaction_nr
                            WHERE prebill.encounter_nr = ?
                            ORDER BY transactions.transaction_date DESC", $encounterNr);
    }

    /**
     * @param $encounterNr
     * @return false|Array
     */
    public static function getOldestSessionByEncounter($encounterNr)
    {
        global $db;
        return $db->GetRow("SELECT
                              prebill.bill_type,
                              prebill.request_flag,
                              prebill.amount,
                              prebill.discountid,
                              transactions.dialyzer_serial_nr,
                              transactions.machine_nr,
                              transactions.transaction_date
                            FROM
                              seg_dialysis_prebill AS prebill
                              INNER JOIN seg_dialysis_transaction AS transactions
                                ON prebill.bill_nr = transactions.transaction_nr
                            WHERE prebill.encounter_nr = ?
                            ORDER BY transactions.transaction_date ASC", $encounterNr);
    }

    /**
      * Added by Gervie 04/06/2016
      */
    function getPaidInCashier($or_no, $bill_nr) {
        global $db;

        $this->sql = $db->Prepare("SELECT 
                                      * 
                                    FROM
                                      `seg_pay_request` pr 
                                    WHERE pr.`or_no` = ? AND pr.`service_code` = ?");
        if ($rs = $db->Execute($this->sql, array($or_no, $bill_nr))) {
            if ($rs->RecordCount()) {
                return $rs;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}//end class