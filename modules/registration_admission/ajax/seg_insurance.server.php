<?php
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    require_once($root_path.'modules/registration_admission/ajax/seg_insurance.common.php');
    require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');

    function isPrincipal($pid, $hid) {
        global $db;

        $bIsPrincipal = false;

        $sql = "SELECT i.is_principal AS Member FROM care_person_insurance AS i
                   where i.pid = '{$pid}' and i.hcare_id = {$hid}";
        if ($result = $db->Execute($sql)) {
            if ($row = $result->FetchRow()) {
                $bIsPrincipal = (is_null($row['Member'])) ? false : ($row['Member'] != 0);
            }
        }

        return $bIsPrincipal;
    }

    function isDependent($pid) {
        global $db;

        $parent_pid = '';

        $sql = "SELECT d.parent_pid AS parent
                FROM seg_dependents AS d
                where d.dependent_pid = '{$pid}' and upper(d.status) = 'MEMBER'";
        if ($result = $db->Execute($sql)) {
            if ($row = $result->FetchRow()) {
                $parent_pid = (is_null($row['parent'])) ? '' : $row['parent'];
            }
        }

        return ($parent_pid != '');
    }

    function setFlagForPrincipalNmFromTmp($pid, $hid) {

        $objResponse = new xajaxResponse();

        $objResponse->call("setNoPrincipalFlag", "0");

        if (!isPrincipal($pid, $hid)) {
//  Commented out by LST ... 06.13.2012 --- seg_dependents table is used by SPMC personell only ...
//            if (!isDependent($pid)) {
                $objResponse->call("setNoPrincipalFlag", "1");
//            }
// ------------------------------------------------------------------------------------------------
        }

        return $objResponse;
    }

    
    function clearEncCategory($enc)
    {
        global $db;
        $objResponse = new xajaxResponse();

        $sql = "DELETE FROM seg_encounter_memcategory WHERE encounter_nr=".$db->qstr($enc);
        $ok = $db->Execute($sql);

        if(!$ok){
            $objResponse->alert($sql);
        }

        return $objResponse;
    }

    //Added by EJ 11/20/2014
    function checkMembershipTypeData($id, $enc)
    {
        global $db;
        $objResponse = new xajaxResponse();
        $objBilling = new Billing();

        $checkMembershipDetail = $objBilling->checkMembership($enc);  
        if (!$checkMembershipDetail) {
            $objResponse->alert("Check Membership Type");
        }
        else {
            $objResponse->call("prepareAdd", $id);
        }
        

        return $objResponse;
    }

    //Created by EJ 11/13/2014
    function addInsuranceAdt($enc, $act, $ins, $encdr) {
        global $db;
        $objResponse = new xajaxResponse();
        $enc_obj = new Encounter();

        $enc_obj->addInsuranceAdt($enc, $act, $ins, $encdr);

        return $objResponse;
    }


    $xajax->processRequest();
?>