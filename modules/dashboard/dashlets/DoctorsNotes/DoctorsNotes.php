<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once $root_path.'include/care_api_classes/class_encounter.php'; // added by: syboy 07/04/2015
//require_once $root_path.'include/care_api_classes/ehr/class_curl.php';
require_once($root_path . 'include/care_api_classes/class_acl.php');
require_once __DIR__ .'/../../../../include/care_api_classes/ehrhisservice/Ehr.php';

/**
 * Dashlet for Doctors Notes
 */
class DoctorsNotes extends Dashlet {

    protected static $name  = 'Notes';
    protected static $icon  = 'forums.gif';
    protected static $group = '';

    /**
     * Constructor
     *
     */
    public function __construct( $id=null )
    {
        parent::__construct( $id );
    }


    public function init()
    {
        parent::init(Array(
            'contentHeight' => 'auto',
            'pageSize'          => 10
        ));
    }


    /**
     * Processes an Action sent by the client
     *
     */
    public function processAction( DashletAction $action )
    {
        global $db;
        $response = new DashletResponse;

        $dataHistory = array(); //added rnel
        //$curl_ehr = new Rest_ehr();

        $sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
        $personell_nr = $db->GetOne($sql);

        if ($action->is("saveDrNote"))
        {
            $core = new Core();
            $db->BeginTrans();

            $core->setTable("seg_doctors_notes",TRUE);

            //prepare data array
            $data=(array)$action->getParameter("data");
            $saveData = array();
            foreach($data as $i=>$item)
            {
                $saveData[$item["name"]]=$item["value"];
            }

            // $sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
            // $personell_nr = $db->GetOne($sql);
            $saveData["personell_nr"] = $personell_nr;
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $saveData["encounter_nr"] = $encounter_nr;

            //if($printResponse = $curl_ehr->saveSOAP($saveData, $action->getParameter("data"))){
            // $response->alert(print_r($printResponse,true));
            //}else{
            //  $response->alert(print_r("Unable to save to ehr: ".$printResponse,true));
            //}

            //edited by: Kiefher Chavez 5/10/2021 para ma save sa his ang data without interfering ang saving sa ehr.
            

            $sql = "SELECT personell_nr FROM seg_doctors_notes WHERE personell_nr=".$db->qstr($personell_nr)." AND encounter_nr=".$db->qstr($encounter_nr);
            $check_sql = $db->GetOne($sql);

            if($check_sql==NULL){
                $saveok = $core->save($saveData);
                $saveData_his = $saveData;
                $saveData_his[] = 10;
                $saveData_holder = &$saveData;
                $saveData_holder[] = 10;
                $saveData_holder["personell_nr"] = NULL;
                $saveok = $core->save($saveData_holder);
                $sql_get_orig = "SELECT * FROM seg_doctors_notes WHERE encounter_nr=".$db->qstr($encounter_nr)." ORDER BY create_time ASC";
                $original_data = $db->GetRow($sql_get_orig);
                $saveData_holder["personell_nr"] = NULL;
                $saveData_holder["physical_examination"] = $original_data["physical_examination"];
                $saveData_holder["clinical_summary"] = $original_data["clinical_summary"];
                $saveData_holder["chief_complaint"] = $original_data["chief_complaint"];
                $saveok = $core->save($saveData_holder);
            }else{
                $saveData_his = $saveData;
                $saveData_his[] = 10;
                $saveData_holder = &$saveData;
                $saveData_holder[] = 10;
                $saveData_holder["personell_nr"] = NULL;
                $saveok = $core->save($saveData_holder);
            }
            //kiefher end

            if($saveok!==FALSE) {

                $ehr = Ehr::instance();
                $resp = '';
                foreach($data as $i=>$item)
                {
                    $saveData[$item["name"]]=$item["value"];
                    $resp->saved = false;
                    switch ($item["name"]){
                        case 'clinical_summary':
                            $resp = $ehr->patient_savesoapplan(array(
                                'soap_plan' => array(
                                    'plan' => utf8_encode($item["value"]),
                                    'encounter_nr' => $encounter_nr,
                                )
                            ));
                            break;

                        case 'physical_examination':
                            $resp = $ehr->patient_savesoapobjective(array(
                                'soap_objective' => array(
                                    'objective' => utf8_encode($item["value"]),
                                    'encounter_nr' => $encounter_nr,
                                )
                            ));
                            break;
                        default:
                            $resp->saved = true;
                            break;
                    }

                    $db->CommitTrans();
                    $success = true;
                    if($resp->saved){
                    }
                    else{
//                        $db->RollbackTrans();
//                        $response->alert("Error::".$ehr->getResponseData()."Query::".$core->getQuery());
                    }
                }
            }
            else
                $response->alert("Error::".$core->getErrorMsg()."Query::".$core->getQuery());

        }
        else if($action->is("saveDrDiagnosis")) {
            $core = new Core();
            $db->BeginTrans();
            $core->setTable("seg_doctors_diagnosis",TRUE);

            //prepare data array
            $saveData = array();
            $data = $action->getParameter("data");
            $saveData["icd_code"] = $data;

            $sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
            $personell_nr = $db->GetOne($sql);
            $saveData["personell_nr"] = $personell_nr;

            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $saveData["encounter_nr"] = $encounter_nr;

            // $response->alert(print_r($saveData,true));
            $saveok = $core->save($saveData);
            if($saveok===FALSE) {
                $response->alert("Error::".$core->getErrorMsg()."Query::".$core->getQuery());
            } else {
                $ehr = Ehr::instance();
                $resp = $ehr->patient_savesoapassesmenticd(array(
                    'soap_assessment_icd_add' => array(
                        'icd' => $data,
                        'desc' => '',
                        'encounter_nr' => $encounter_nr,
                    )
                ));
                $db->CommitTrans();
                $response->call("DoctorsNotes_refreshIcdList");
                if($resp->saved){
                }
                else {
//                    $db->RollbackTrans();
//                    $response->alert("Error:: on EHR: ".$resp->msg);
                }
            }
        }
        else if($action->is("deleteDrDiagnosis")) {
            $core = new Core();
            $db->BeginTrans();
            $core->setTable("seg_doctors_diagnosis",TRUE);

            //prepare data array
            $pkArray = array();
            $icd_code = $action->getParameter("data");
            $pkArray["icd_code"] = $icd_code;

            $sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
            $personell_nr = $db->GetOne($sql);
            $pkArray["personell_nr"] = $personell_nr;

            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $pkArray["encounter_nr"] = $encounter_nr;

            //$response->alert(print_r($saveData,true));
            $deleteok = $core->delete($pkArray);
            if($deleteok===FALSE) {
                $db->RollbackTrans();
                $response->alert("Error::".$core->getErrorMsg()."Query::".$core->getQuery());
            } else {
                $ehr = Ehr::instance();
                $resp = $ehr->patient_removesoapassesmenticd(array(
                    'soap_assessment_icd_remove' => array(
                        'icd' => $icd_code,
                        'encounter_nr' => $encounter_nr,
                    )
                ));
                $db->CommitTrans();
                $response->call("DoctorsNotes_refreshIcdList");
                if($resp->saved){
                }
                else {
//                    $db->RollbackTrans();
//                    $response->alert(json_encode($ehr->getResponseData(), true));
//                    $response->alert("Error:: on EHR: ".$resp->msg);
                }
            }
        }
        //added by Jasper Ian Q. Matunog 11/06/2014
        // else if ($action->is("saveClinicalImpression")) {

        //  $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
        //  $encounter_nr = $session->get('ActivePatientFile');
        //  $data = $action->getParameter("data");
        //  $encData['encounter_nr'] = $encounter_nr;
        //  $encData['er_opd_diagnosis'] = $data;

        //  //added rnel
        //  $dataHistory = array(
        //      'user' => $_SESSION["sess_temp_userid"],
        //      'diagnosis' => $data,
        //      'encounter_nr' => $encounter_nr
        //      );
        //  $this->createHistoryClinicalImpression($dataHistory);
        //  //end rnel

        //  $updateOk = $this->updateClinicalImpression($encData);

        //  if (!$updateOk) {
        //      $response->alert("Error on updating clinical impression!");
        //  }
        // }
        // //added by Jasper Ian Q. Matunog 11/06/2014

        // Added by Robert 04/28/2015
        else if ($action->is("saveClinicalImpressionOnButton")) {
            $db->BeginTrans();
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $data = $action->getParameter("data");
            $encData['encounter_nr'] = $encounter_nr;
            $encData['er_opd_diagnosis'] = stripslashes($data);
            $note_type = "Clinical Impression";
            // added rnel
            $dataHistory = array(
                'user' => $_SESSION["sess_temp_userid"],
                'personell_nr' => $personell_nr,
                'diagnosis' => $data,
                'encounter_nr' => $encounter_nr,
                'note_type' => $note_type
            );


            //if($printResponse = $curl_ehr->saveAssessmentDiagnosis($dataHistory)){
            // $response->alert(print_r($printResponse,true));
            //}else{
            //$response->alert(print_r("Unable to save to ehr: ".$printResponse,true));
            //}

            $this->createHistoryClinicalImpression($dataHistory);
            // end rnel

            $updateOk = $this->updateClinicalImpression($encData);
            if (!$updateOk) {
                $response->alert("Error on updating clinical impression!");
            }
            else{
                $ehr = Ehr::instance();
                $resp = $ehr->patient_savesoapclinicalimp(array(
                    'soap_assessment_clinical_imp' => array(
                        'impression' => utf8_encode($data),
                        'encounter_nr' => $encounter_nr,
                    )
                ));
                $db->CommitTrans();
                if($resp->saved){
                }
                else {
//                    $db->RollbackTrans();
////                    $response->alert(json_encode($ehr->getResponseData(), true));
//                    $response->alert("Error:: on EHR: ".$data);
                }
            }
        }
        // End add by Robert

        // Added by Kenneth 02/05/2018
        else if ($action->is("saveFinalDiagnosisOnButton")) {
            $db->BeginTrans();
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $data = $action->getParameter("data");
            $note_type = "Final Diagnosis";
            $dataHistory = array(
                'user' => $_SESSION["sess_temp_userid"],
                'personell_nr' => $personell_nr,
                'diagnosis' =>  $data,
                'encounter_nr' => $encounter_nr,
                'note_type' => $note_type
            );

            //if($printResponse = $curl_ehr->saveAssessmentDiagnosis($dataHistory)){
            // $response->alert(print_r($printResponse,true));
            //}else{
            //$response->alert(print_r("Unable to save to ehr: ".$printResponse,true));
            //}

            $updateOk = $this->saveFinalDiagnosis($dataHistory);
            // Added by MAtsuu 11022018
            $finaldiagOk = $this->saveEditedFinalDiagnosis($dataHistory);




            if (!$updateOk) {
                $db->RollbackTrans();
                $response->alert("Error on updating Final Diagnosis!");
            }
            else{
                $ehr = Ehr::instance();
                $resp = $ehr->patient_savesoapfinaldiag(array(
                    'soap_asses_final_diag' => array(
                        'final_diag' => utf8_encode($data),
                        'encounter_nr' => $encounter_nr,
                    )
                ));
                $db->CommitTrans();
                $response->call("DoctorsNotes_refreshAudit");
                if($resp->saved){
                }
                else {
//                    $db->RollbackTrans();
//                    $response->alert('Error: EHR '.$resp->msg);
                }



            }
            // ended by MAtsuu 11022018
        }

        else if ($action->is("saveOtherDiagnosisOnButton")) {
            $db->BeginTrans();
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $data = $action->getParameter("data");
            $note_type = "Other Diagnosis";

            $dataHistory = array(
                'user' => $_SESSION["sess_temp_userid"],
                'personell_nr' => $personell_nr,
                'diagnosis' =>  $data,
                'encounter_nr' => $encounter_nr,
                'note_type' => $note_type

            );


            //if($printResponse = $curl_ehr->saveAssessmentDiagnosis($dataHistory)){
            // $response->alert(print_r($printResponse,true));
            //}else{
            //$response->alert(print_r("Unable to save to ehr: ".$printResponse,true));
            //}

            $updateOk = $this->saveOtherDiagnosis($dataHistory);
            $finaldiagOk = $this->saveEditedOtherDiagnosis($dataHistory);
            if (!$updateOk) {
                $response->alert("Error on updating Other Diagnosis!");
            }
            else{
                $ehr = Ehr::instance();
                $resp = $ehr->patient_savesoapotherdiag(array(
                    'soap_asses_other_diag' => array(
                        'other_diag' => utf8_encode($data),
                        'encounter_nr' => $encounter_nr,
                    )
                ));
                $db->CommitTrans();
                $response->call("DoctorsNotes_refreshAudit");
                if($resp->saved){

                }
                else{
//                    $db->RollbackTrans();
//                    $response->alert("Error: EHR ".$resp->msg);
                }

            }
        }

        else if ($action->is("saveEditFinalDiagnosis")){
            $db->BeginTrans();
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $data = $action->getParameter("data");

            $dataFinalDiag = array(
                'user' => $_SESSION["sess_temp_userid"],
                'diagnosis' =>  $data,
                'encounter_nr' => $encounter_nr
            );

            $updateOk = $this->saveEditedFinalDiagnosis($dataFinalDiag);
            if (!$updateOk) {
                $response->alert("Error on updating Finals Diagnosis!");
            }
            else{
                $ehr = Ehr::instance();
                $resp = $ehr->patient_savesoapfinaldiag(array(
                    'soap_asses_final_diag' => array(
                        'final_diag' => utf8_encode($data),
                        'encounter_nr' => $encounter_nr,
                    )
                ));

                $db->CommitTrans();
                $response->call("DoctorsNotes_refreshAudit");
                if($resp->saved){
                }
                else {
//                    $db->RollbackTrans();
//                    $response->alert('Error: EHR '.$resp->msg);
                }
            }
        }

        else if ($action->is("saveEditOtherDiagnosis")){
            $db->BeginTrans();
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $data = $action->getParameter("data");

            $dataFinalDiag = array(
                'user' => $_SESSION["sess_temp_userid"],
                'diagnosis' =>  $data,
                'encounter_nr' => $encounter_nr
            );

            $updateOk = $this->saveEditedOtherDiagnosis($dataFinalDiag);
            if (!$updateOk) {
                $response->alert("Error on updating Other Diagnosis!");
            }
            else{
                $ehr = Ehr::instance();
                $resp = $ehr->patient_savesoapotherdiag(array(
                    'soap_asses_other_diag' => array(
                        'other_diag' => utf8_encode($data),
                        'encounter_nr' => $encounter_nr,
                    )
                ));
                $db->CommitTrans();
                $response->call("DoctorsNotes_refreshAudit");
                if($resp->saved){
                }
                else{
//                    $db->RollbackTrans();
//                    $response->alert("Error: EHR ".$resp->msg);
                }
            }
        }



        else {
            $response->extend( parent::processAction($action) );
        }

        return $response;
    }

    public function updateClinicalImpression($data){
        // return $data;
        global $db;
        $pk = array('encounter_nr');

        foreach ($data as $key => &$val) {
            $val = $db->qstr($val);
        }

        $rs = $db->Replace('care_encounter', $data, $pk);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    /**
     *
     * @author rnel
    create audit trail in clinical impression
     *
     */


    private function createHistoryClinicalImpression($data = array()) {

        global $db;
        $success = false;

        $date = date('M d, Y h:i A');

        $sql = "SELECT encounter_nr FROM seg_clinical_impression WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
        $result = $db->Execute($sql);

        $enc_obj = new Encounter();
        $latestClinicalImpression = $enc_obj->checkImpressionIfExists($data['encounter_nr']);

        if($result->RecordCount() > 0) {
            if(str_replace("'", "\'", $latestClinicalImpression['clinical_impression']) !== $data['diagnosis']) {
                $history = "Impression: " . addslashes($data['diagnosis']) .
                    "Updated from [Doctor's Dashboard] on ". date('Y-m-d H:i:s') . " by: " . $_SESSION['sess_login_username'] . "\n";

                $sqlUpdate = "UPDATE seg_clinical_impression
                                SET clinical_impression = ".$db->qstr($data['diagnosis']).",
                                    history = CONCAT(history, ".$db->qstr($history).")
                                WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
                $res = $db->Execute($sqlUpdate);
                if($res->Affected_Rows() > 0) {

                    $success = true;

                }
            }

        } else {
            $history = "Impression: " . addslashes($data['diagnosis']) .
                "Created from [Doctor's Dashboard] on ". date('Y-m-d H:i:s') . " by: " . $_SESSION['sess_login_username'] . "\n";

            $sqlInsert = "INSERT INTO seg_clinical_impression (encounter_nr, clinical_impression, history) 
                            VALUES('".$data['encounter_nr']."', '".$data['diagnosis']."', ".$db->qstr($history).") ";

            if($db->Execute($sqlInsert)) {

                $success = true;

            }
        }

        return $success;
    }

    // end rnel


    /**
     *
     * @author Kenneth 02/05/2018
    create audit trail in clinical impression
     *
     */


    private function saveFinalDiagnosis($data = array()) {

        global $db;

        $success = false;

        $date = date('M d, Y h:i A');

        $sql = "SELECT * FROM seg_soa_diagnosis WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
        $result = $db->Execute($sql);

        $enc_obj = new Encounter();
        $latestFinalDiagnosis = $enc_obj->getFinalDiagnosisIfExists($data['encounter_nr']);

        if($result->RecordCount() > 0) {
            $history = "Final Diagnosis: " . addslashes($data['diagnosis']) .
                " Updated from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($_SESSION['sess_login_username']) . "\n";

            $sqlUpdate = "UPDATE seg_soa_diagnosis
                                SET final_diagnosis = ".$db->qstr($data['diagnosis']).",modify_date=NOW(),modify_id=" . $db->qstr($_SESSION['sess_login_username']) . "
                                    , history = CONCAT(history, '".$history."')
                                WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";

            $res = $db->Execute($sqlUpdate);
            if($db->Execute($sqlUpdate)) {
                $success = true;
            }

        } else {
            $history = "Final Diagnosis: " . addslashes($data['diagnosis']) .
                " Created from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($_SESSION['sess_login_username']) . "\n";

            $sqlInsert = "INSERT INTO seg_soa_diagnosis (encounter_nr, final_diagnosis, history,create_id,create_date) 
                            VALUES('".$data['encounter_nr']."', ".$db->qstr($data['diagnosis']).",'".$history."'," . $db->qstr($_SESSION['sess_login_username']) . ", NOW()) ";
            if($db->Execute($sqlInsert)) {
                $success = true;
            }
        }
        return $success;
    }

    private function saveOtherDiagnosis($data = array()) {

        global $db;
        $success = false;

        $date = date('M d, Y h:i A');

        $sql = "SELECT * FROM seg_soa_diagnosis WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
        $result = $db->Execute($sql);

        $enc_obj = new Encounter();
        $latestOtherDiagnosis = $enc_obj->getOtherDiagnosisIfExists($data['encounter_nr']);

        if($result->RecordCount() > 0) {
            $history = "Other Diagnosis: " . addslashes($data['diagnosis']) .
                " Updated from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($_SESSION['sess_login_username']) . "\n";

            $sqlUpdate = "UPDATE seg_soa_diagnosis
                                SET other_diagnosis = ".$db->qstr($data['diagnosis']).",modify_date=NOW(),modify_id=" . $db->qstr($_SESSION['sess_login_username']) . "
                                    , history = CONCAT(history, '".$history."')
                                WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
            $res = $db->Execute($sqlUpdate);
            // var_dump($sqlUpdate);
            if($db->Execute($sqlUpdate)) {
                $success = true;
            }

        } else {
            $history = "Other Diagnosis: " . addslashes($data['diagnosis']) .
                " Created from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($_SESSION['sess_login_username']) . "\n";

            $sqlInsert = "INSERT INTO seg_soa_diagnosis (encounter_nr, other_diagnosis, history,create_id,create_date) 
                            VALUES('".$data['encounter_nr']."', ".$db->qstr($data['diagnosis']).",'".$history."'," . $db->qstr($_SESSION['sess_login_username']) . ", NOW()) ";
            // var_dump($sqlInsert);
            if($db->Execute($sqlInsert)) {
                $success = true;
            }
        }
        return $success;
    }

    // end Kenneth


#Added by Matsuu 01012018
    private function saveEditedFinalDiagnosis($data = array()) {

        global $db;
        $success = false;

        $date = date('M d, Y h:i A');
        $encounter_nr = $data['encounter_nr'];
        $encounter_nr = strval($encounter_nr);
        $diagnosis = $db->qstr($data['diagnosis']);
        $login_id = $_SESSION['sess_login_userid'];

        $table = 'seg_soa_diagnosis_new';
        $fields = array(
            'encounter_nr' => $encounter_nr,
            'final_diagnosis' => $data['diagnosis']
        );
        $pk = array(
            'encounter_nr'
        );
        $sql = "SELECT history FROM seg_soa_diagnosis_new WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
        $row = $db->GetRow($sql);
        if(isset($row) && !empty($row)){
            $fields['modify_date'] = $db->qstr(date('Y-m-d H:i:s'));
            $fields['modify_id'] = $login_id;
            $fields['history'] = $row['history'] . "Final Diagnosis: " . addslashes($data['diagnosis']) .
                " Updated from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($login_id) . "\n";
        }else{
            $fields['create_date'] = date('Y-m-d H:i:s');
            $fields['create_id'] =$login_id;
            $fields['history'] = $row['history'] ."Final Diagnosis: " . addslashes($data['diagnosis']) .
                " Created from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($login_id) . "\n";

        }
        $rs = $db->Replace($table,$fields,$pk,true);


        if($rs){
            return true;
        }else{
            return false;
        }
    }
    private function saveEditedOtherDiagnosis($data = array()) {

        global $db;
        $success = false;

        $date = date('M d, Y h:i A');
        $encounter_nr = $data['encounter_nr'];
        $diagnosis = $db->qstr($data['diagnosis']);
        $login_id = $_SESSION['sess_login_userid'];


        $table = 'seg_soa_diagnosis_new';
        $fields = array(
            'encounter_nr'=> $encounter_nr,
            'other_diagnosis' => $data['diagnosis']
        );
        $pk = array(
            'encounter_nr'
        );
        $sql = "SELECT history FROM seg_soa_diagnosis_new WHERE encounter_nr = {$db->qstr($data['encounter_nr'])}";
        $row = $db->GetRow($sql);
        if(isset($row) && !empty($row)){
            $fields['modify_date'] = $db->qstr(date('Y-m-d H:i:s'));
            $fields['modify_id'] = $login_id;
            $fields['history'] = $row['history'] . "Other Diagnosis: " . addslashes($data['diagnosis']) .
                " Updated from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($login_id) . "\n";


        }else{
            $fields['create_date'] = date('Y-m-d H:i:s');
            $fields['create_id'] =$login_id;
            $fields['history'] = $row['history'] ."Other Diagnosis: " . addslashes($data['diagnosis']) .
                " Created from [Doctor\'s Dashboard] on ". date('Y-m-d H:i:s') . " by: " . addslashes($login_id) . "\n";

        }
        $rs = $db->Replace($table,$fields,$pk,true);
        if($rs){
            return true;
        }else{
            return false;
        }

    }
    #Ended by Matsuu 01012018



    /**
     * Processes a Render request and returns the output
     *
     */
    public function render($renderParams=null) {
        global $root_path, $db;
        if ( $renderParams['mode'] )
        {
            $mode = $renderParams['mode'];
        }
        else
        {
            $mode = $this->getMode();
        }
        if ($mode->is(DashletMode::VIEW_MODE))
        {
            $core = new Core();
            $core->setTable("seg_doctors_notes",TRUE);

            $smarty = new smarty_care('common');
            $dashletSmarty = Array(
                'id' => $this->getId()
            );
            $smarty->assign('dashlet', $dashletSmarty);
            $preferencesSmarty = Array(
                'pageSize' => $this->preferences->get('pageSize')
            );
            $smarty->assign('settings', $preferencesSmarty);

            $sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
            $personell_nr = $db->GetOne($sql);
            $session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
            $encounter_nr = $session->get('ActivePatientFile');
            $enc_type = $db->GetOne("SELECT encounter_type FROM care_encounter WHERE encounter_nr = '$encounter_nr'");

            
            //added by: kiefher chavez 5/9/2021, para maka view ang mga doctor sa case sa patient bisan walay record sa table seg_doctors_notes
            $sql2 = "SELECT personell_nr FROM seg_doctors_notes WHERE personell_nr=".$db->qstr($personell_nr)." AND encounter_nr=".$db->qstr($encounter_nr);
            $check = $db->GetOne($sql2);

            if(($check != NULL) || ($check>=1)){
                $data = $core->fetch(array( 'personell_nr'=> $personell_nr, 'encounter_nr'=> $encounter_nr ));
                $smarty->assign('data',$data);
            }else{
                $sql3 = "SELECT personell_nr FROM seg_doctors_notes WHERE encounter_nr=".$db->qstr($encounter_nr)." ORDER BY create_time ASC";
                $original_personell_nr = $db->GetOne($sql3);
                $data = $core->fetch(array( 'personell_nr'=> $original_personell_nr, 'encounter_nr'=> $encounter_nr ));
                $smarty->assign('data',$data);
            }
            //kiefher END
            

            // added by: syboy 06/13/2015
            $is_dis = new Encounter();
            $is_discharged = $is_dis->is_discharged_patient($encounter_nr);
            $smarty->assign('is_discharged', $is_discharged);
            $objAcl = new Acl($_SESSION["sess_temp_userid"]);
            $edit_diagnosis = $objAcl->checkPermissionRaw('_a_1_doctorseditdiagnosis');


            if($edit_diagnosis){
                $smarty->assign('disabled_edit_btn','');
            }
            else{
                $smarty->assign('disabled_edit_btn','disabled');
            }

            if ($is_discharged == 1) {
                $smarty->assign('disable', 'disabled');
                $smarty->assign('disable_edit', '');
                $smarty->assign('hide_edit_button', '');
                $smarty->assign('hide_save_button', 'hidden');
            }else{
                $smarty->assign('disable', '');
                $smarty->assign('disable_edit', 'disabled');
                $smarty->assign('hide_edit_button', 'hidden');
                $smarty->assign('hide_save_button', '');
            }
            // end

            if($encounter_nr!==NULL) {
                define(IPBMIPD_enc, 13);
                define(IPBMOPD_enc, 14);
                //added by Jasper Ian Q. Matunog 11/10/2014
                $smarty->assign('sShowDiagnosisList', "");
                $smarty->assign('sHideClinicalImpression', "");
                $smarty->assign('disable_clinical', ($enc_type == 3 || $enc_type == 4) ? 'readonly' : '');
                $smarty->assign('disable_clinical_button', ($enc_type == 3 || $enc_type == 4) ? 'disabled' : '');
                // if (substr($encounter_nr,4,1) == 5) { //OPD only
                if ($enc_type == 2 || $enc_type == 1 || $enc_type == 3 || $enc_type == 4 || $enc_type == IPBMIPD_enc || $enc_type == IPBMOPD_enc) { //OPD only; edited by : syboy 01/28/2016 : meow; include encounter type 1 or ER patient
                    // modify by rnel include er admission and opd admission patient type
                    $sqlEncounter = "SELECT er_opd_diagnosis FROM care_encounter WHERE encounter_nr= '$encounter_nr'";
                    $clinicalImpression = $db->GetOne($sqlEncounter);

                    $sqlEncounter = "SELECT final_diagnosis FROM seg_soa_diagnosis WHERE encounter_nr= '$encounter_nr'";
                    $finalDiagnosis = $db->GetOne($sqlEncounter);

                    $sqlEncounter = "SELECT other_diagnosis FROM seg_soa_diagnosis WHERE encounter_nr= '$encounter_nr'";
                    $otherDiagnosis = $db->GetOne($sqlEncounter);

                    $sqlEditedFinalDiag = "SELECT IFNULL(ssdn.final_diagnosis,ssd.final_diagnosis) AS final_diagnosis FROM seg_soa_diagnosis AS ssd  LEFT JOIN seg_soa_diagnosis_new AS ssdn ON ssd.encounter_nr = ssdn.encounter_nr WHERE ssd.encounter_nr = '$encounter_nr'";
                    $editedFinalDiagnosis = $db->GetOne($sqlEditedFinalDiag);

                    $sqlEditedFinalDiagNew = "SELECT ssdn.final_diagnosis AS final_diagnosis FROM  seg_soa_diagnosis_new AS ssdn  WHERE ssdn.encounter_nr = '$encounter_nr'";
                    $editedFinalDiagnosisNew = $db->GetOne($sqlEditedFinalDiagNew);
                    if(!$editedFinalDiagnosis){
                        $editedFinalDiagnosis = $editedFinalDiagnosisNew;
                    }

                    $sqlEditedOtherDiag = "SELECT IFNULL(ssdn.other_diagnosis,ssd.other_diagnosis) AS other_diagnosis FROM seg_soa_diagnosis AS ssd  LEFT JOIN seg_soa_diagnosis_new AS ssdn ON ssd.encounter_nr = ssdn.encounter_nr WHERE ssd.encounter_nr = '$encounter_nr'";
                    $editedOtherDiagnosis = $db->GetOne($sqlEditedOtherDiag);

                    $sqlEditedOtherDiagNew = "SELECT ssdn.other_diagnosis AS other_diagnosis FROM  seg_soa_diagnosis_new AS ssdn  WHERE ssdn.encounter_nr = '$encounter_nr'";
                    $editedOtherDiagnosisNew = $db->GetOne($sqlEditedOtherDiagNew);

                    if(!$editedOtherDiagnosis){
                        $editedOtherDiagnosis = $editedOtherDiagnosisNew;
                    }

                    // Strip Slashes
                    $clinicalImpression = stripslashes($clinicalImpression);
                    $finalDiagnosis = stripslashes($finalDiagnosis);
                    $editedFinalDiagnosis = stripslashes($editedFinalDiagnosis);
                    $editedOtherDiagnosis = stripslashes($editedOtherDiagnosis);
                    $otherDiagnosis = stripslashes($otherDiagnosis);


               
                    $smarty->assign('sClinicalImpression', $clinicalImpression);
                    /* Modify by Matsuu 
                     $smarty->assign('sFinalDiagnosis', $finalDiagnosis); 
                     $smarty->assign('sOtherDiagnosis', $otherDiagnosis);
                    */
                    $smarty->assign('sFinalDiagnosis', $editedFinalDiagnosis);
                    $smarty->assign('sOtherDiagnosis', $editedOtherDiagnosis);
                    /* Ended here.. */
                    $smarty->assign('sEditFinalDiagnosis',$editedFinalDiagnosis);
                    $smarty->assign('sEncounterNumber',$encounter_nr);
                    $smarty->assign('sEditOtherDiagnosis',$editedOtherDiagnosis);
                    // $smarty->assign('sHideDiagnosisList', "display:none;");
                } else {
                    $smarty->assign('sHideClinicalImpression', "display:none;");
                }
                //added by Jasper Ian Q. Matunog 11/10/2014
                return $smarty->fetch($root_path.'modules/dashboard/dashlets/DoctorsNotes/templates/View.tpl');
            } else {
                return $smarty->fetch($root_path.'modules/dashboard/dashlets/DoctorsNotes/templates/NoView.tpl');
            }

        }
        elseif ($this->getMode()->is(DashletMode::EDIT_MODE))
        {
            $smarty = new smarty_care('common');
            $dashletSmarty = array(
                'id' => $this->getId()
            );
            $smarty->assign('dashlet', $dashletSmarty);
            return $smarty->fetch($root_path.'modules/dashboard/dashlets/JotPad/templates/noEdit.tpl');
        }
        else
        {
            return parent::render($renderParams);
        }
    }

}
