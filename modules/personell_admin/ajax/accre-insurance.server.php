<?php
    #edited by VAN 04-17-08
    function populateInsurance($sElem,$keyword, $personell_nr, $page) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_insurance_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_insurance_search_max_block_rows'];

        $objResponse = new xajaxResponse();
        $insObj=new Insurance;
        $offset = $page * $maxRows;

        $searchkey = utf8_decode($searchkey);
        $total_srv = $insObj->countSearchSelect($keyword,$maxRows,$offset);
        #$objResponse->addAlert($insObj->sql);
        $total = $insObj->count;
        #$objResponse->addAlert('total = '.$total);

        $lastPage = floor($total/$maxRows);
        #$objResponse->addAlert('total = '.$lastPage);
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;

        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$insObj->SearchSelect($keyword,$maxRows,$offset);
        #$objResponse->addAlert("sql = ".$insObj->sql);
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","product-list");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {

                #added by VAN 08-14-08
                $personell_Insinfo = $insObj->getPersonnelAccreditationInfo($personell_nr, trim($result["hcare_id"]));
            
                $objResponse->addScriptCall("addProductToList","product-list",trim($result["hcare_id"]),trim($result["firm_id"]),trim($result["name"]), $personell_Insinfo['accreditation_nr'],$personell_Insinfo['expiration'], $cnt);
            }#end of while
        } #end of if

        if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }

    #-------added by VAN 11-04-09
    function setDeactivatePersonnel($personell_nr, $deactivate,$remarks,$remarks_txt, $durationTime){
            global $db, $HTTP_SESSION_VARS, $root_path;
            $objResponse = new xajaxResponse();
            $objDependent = new SegDependents();
            $objPersonell = new Personell(); // Added by: Arco - 06/03/2016

            $sql_u = "SELECT * FROM care_users WHERE personell_nr='".$personell_nr."'";
            $rs_u = $db->Execute($sql_u);
            $with_account = $rs_u->RecordCount();

            $sql_perinfo = "SELECT pid FROM care_personell WHERE nr='".$personell_nr."'";
            $rs_perinfo = $db->Execute($sql_perinfo);
            $row_perinfo = $rs_perinfo->FetchRow();
            $pid = $row_perinfo['pid'];
            $start_time = $_SESSION['DEACTIVATION_TIME_IN'];
            $interval = $start_time->diff(new DateTime());
            $duration = $interval->format('%H:%I:%S');

            $remarks_name = $db->GetOne("SELECT name FROM seg_deactivate_remarks WHERE code = ".$db->qstr($remarks));
            
            if ($deactivate){
                    $history = "CONCAT(history,'Deactivate Personnel: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
                    $datenow = date('Y-m-d');
                    //$date_exit = date( "Y-m-d", strtotime( "$datenow -1 day" ));

                    $sql_personell = "UPDATE care_personell SET
                                                                date_exit=".$db->qstr($datenow).",
                                                                contract_end=".$db->qstr($datenow).",
                                                                status='deleted',
                                                                history = $history,
                                                                remarks =".$db->qstr($remarks_name).",
                                                                modify_id = ".$db->qstr($_SESSION['sess_user_name']).",
                                                                modify_time = '".date('Y-m-d H:i:s')."',
                                                                duration = ".$db->qstr($duration)."
                                                                WHERE nr=".$db->qstr($personell_nr);

                    $sql_personell_assign = "UPDATE care_personell_assignment SET
                                                                date_end=".$db->qstr($datenow).",
                                                                status='deleted',
                                                                history = $history,
                                                                modify_id = ".$db->qstr($_SESSION['sess_user_name']).",
                                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                                WHERE personell_nr='".$personell_nr."'";

                    $sql_personell_dependent = "UPDATE seg_dependents SET
                                                                        status='expired',
                                                                        history = $history,
                                                                        modify_id = ".$db->qstr($_SESSION['sess_user_name']).",
                                                                        modify_dt = ".$db->qstr(date('Y-m-d H:i:s'))."
                                                                        WHERE parent_pid=".$db->qstr($pid)." AND status='member'";

                    // Added by Gervie 04/13/2016
                    // For Dependents Monitoring
                    $sql = "SELECT * FROM seg_dependents WHERE parent_pid = ".$db->qstr($pid)." AND status = 'member'";
                    $dependent = $db->Execute($sql);

                    while($row = $dependent->FetchRow()) {
                        $data['parent_pid'] = $row['parent_pid'];
                        $data['dependent_pid'] = $row['dependent_pid'];
                        $data['relationship'] = $row['relationship'];

                        $objDependent->dependentMonitoring($data, 'deactivated');
                    }

                    $activation_flag = 0; // Added by: Arco - 06/15/2016
                                          // Modified by: Jeff - 08-10-17
                    $remarks_txt = utf8_decode(utf8_decode(utf8_encode($remarks_txt)));
                    $sql_personell_remarks = "INSERT INTO care_personell_remarks SET
                                                                        nr=".$db->qstr($personell_nr).",
                                                                        pid =".$db->qstr($pid).",
                                                                        remarks =".$db->qstr($remarks_name.$remarks_txt).",
                                                                        create_date=".$db->qstr(date('Y-m-d H:i:s')).",
                                                                        create_id=".$db->qstr($_SESSION['sess_user_name']).",
                                                                        on_deac = 1";



                    if ($with_account){
                        $lock_audit="INSERT INTO seg_areas_duration_time (pid,duration,mode,create_id,create_dt) SELECT cp.pid,'00:00:00 00','LOCK',".$db->qstr("[System]").",".$db->qstr(date('Y-m-d H:i:s'))." FROM care_personell cp LEFT JOIN care_users cu on cp.nr=cu.personell_nr WHERE cu.personell_nr=".$db->qstr($personell_nr);
                        $db->Execute($lock_audit);
                        $sql_update_account = "UPDATE care_users SET lockflag=1,modify_time=NOW(),modify_id=".$db->qstr($_SESSION['sess_user_name'])." WHERE personell_nr=".$db->qstr($personell_nr);
                    }

            }else{
                    $history = "CONCAT(history,'Activate Personnel: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
                    $sql_personell = "UPDATE care_personell SET
                                                                date_exit='',
                                                                contract_end='',
                                                                status='',
                                                                remarks =".$db->qstr($remarks_name).",
                                                                history = $history,
                                                                modify_id = ".$db->qstr($_SESSION['sess_user_name']).",
                                                                modify_time = '".date('Y-m-d H:i:s')."',
                                                                duration = ".$db->qstr($duration)."
                                                                WHERE nr=".$db->qstr($personell_nr);

                    $sql_personell_assign = "UPDATE care_personell_assignment SET
                                                                date_end='',
                                                                status='',
                                                                history = $history,
                                                                modify_id = ".$db->qstr($_SESSION['sess_user_name']).",
                                                                modify_time = ".$db->qstr(date('Y-m-d H:i:s'))."
                                                                WHERE personell_nr=".$db->qstr($personell_nr);

                    $sql_personell_dependent = "UPDATE seg_dependents SET
                                                                                status='member',
                                                                                history = $history,
                                                                                modify_id = ".$db->qstr($_SESSION['sess_user_name']).",
                                                                                modify_dt = ".$db->qstr(date('Y-m-d H:i:s'))."
                                                                                WHERE parent_pid=".$db->qstr($pid)." AND status='expired'";

                    // Added by Gervie 04/13/2016
                    // For Dependents Monitoring
                    $sql = "SELECT * FROM seg_dependents WHERE parent_pid = ".$db->qstr($pid)." AND status = 'expired'";
                    $dependent = $db->Execute($sql);

                    while($row = $dependent->FetchRow()) {
                        $data['parent_pid'] = $row['parent_pid'];
                        $data['dependent_pid'] = $row['dependent_pid'];
                        $data['relationship'] = $row['relationship'];

                        $objDependent->dependentMonitoring($data, 'activated');
                    }

                    $activation_flag = 1; // Added by: Arco - 06/15/2016
                    $lock_audit="INSERT INTO seg_areas_duration_time (pid,duration,mode,create_id,create_dt) SELECT cp.nr,'00:00:00 00','UNLOCK',".$db->qstr("[System]").",".$db->qstr(date('Y-m-d H:i:s'))." FROM care_personell cp LEFT JOIN care_users cu on cp.nr=cu.personell_nr WHERE cu.personell_nr=".$db->qstr($personell_nr);
                    $db->Execute($lock_audit);
                    $remarks_txt = utf8_decode(utf8_decode(utf8_encode($remarks_txt)));
                    $sql_personell_remarks = "INSERT INTO care_personell_remarks SET
                                                                        nr=".$db->qstr($personell_nr).",
                                                                        pid =".$db->qstr($pid).",
                                                                        remarks =".$db->qstr($remarks_name.$remarks_txt).",
                                                                        create_id=".$db->qstr($_SESSION['sess_user_name']).",
                                                                        create_date=".$db->qstr(date('Y-m-d H:i:s'));

                    if ($with_account){
                        $sql_update_account = "UPDATE care_users SET lockflag=0,lock_duration=".$db->qstr($duration).",modify_time=NOW(),modify_id=".$db->qstr($_SESSION['sess_user_name'])." WHERE personell_nr=".$db->qstr($personell_nr)."";
                    }
            }

            $db->BeginTrans();

            #update care_personell
            $ok = $db->Execute($sql_personell);
            #update care_personell_assign
            if ($ok)
                $ok=$db->Execute($sql_personell_assign);

            if ($ok)
                $ok=$db->Execute($sql_personell_dependent);

            if ($ok) 
                # code...
                $ok=$db->Execute($sql_personell_remarks);
            if (($ok)&& ($with_account))
                $ok = $db->Execute($sql_update_account);



            //FOR EHR 9/2/2015
            try {
                require_once($root_path . 'include/care_api_classes/doctors_mobility/DoctorService.php');
                require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
                $ehr = Ehr::instance();
                $doctorService = new DoctorService();
                if ($deactivate){
                    $doctorService->deactivateDoctor($personell_nr);
                    $docdata = array(
                        'personnel_nr'  =>  $personell_nr,
                        'lockflag'      =>  0
                    );
                }
                else{
                    $doctorService->saveDoctor($personell_nr);
                    $docdata = array(
                        'personnel_nr'  =>  $personell_nr,
                        'lockflag'      =>  1
                    );
                }
                $patient = $ehr->postDeactivatePersonnel($docdata);
                $asd = $ehr->getResponseData();
                $EHRstatus = $patient->status;
                if(!$EHRstatus){
                    // $objResponse->alert($patient);
                    // $objResponse->alert($asd);
                }
                    
            } catch (Exception $e) {
                
            }
            //END FOR EHR

            if ($ok){
                    
                    // Added by: Arco - 06/15/2016
                    // For Employee Monitoring Activation/Deactivation
                    $data['employee_nr'] = $personell_nr;
                    $data['employee_pid'] = $pid;
                    $data['checker_for_new_employee'] = 0;
                    $data['remarks'] = $remarks_name;
                    if (isset($data['employee_nr'])&&isset($data['employee_pid'])) {
                        if ($activation_flag == 1) {
                            $objPersonell->employeeMonitoring($data, 'activated');
                        }
                        if ($activation_flag == 0) {
                            $objPersonell->employeeMonitoring($data, 'deactivated');
                        }
                    }
                    // end arcute

                    $db->CommitTrans();
                    $objResponse->addScriptCall("ReloadWindow");
            }else{
                    $db->RollbackTrans();
                    $objResponse->alert("Changing personnel's employment status is failed.");
            }


            return $objResponse;
    }

    function setChangePassword($personell_nr,$password,$durationTime){
            global $db, $HTTP_SESSION_VARS, $root_path;
            $objResponse = new xajaxResponse();
            $start_time = $_SESSION['DEACTIVATION_TIME_IN'];
            $interval = $start_time->diff(new DateTime());
            $duration = $interval->format('%H:%I:%S');

            $history = "CONCAT(history,'Change Password: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
            $sql = "UPDATE care_users SET
                                                                password=md5('".$password."'),
                                                                history = $history,modify_time=NOW(),modify_id=".$db->qstr($_SESSION['sess_user_name'])."
                                                                WHERE personell_nr='".$personell_nr."'";
            $getUsername = "SELECT login_id from care_users WHERE personell_nr='".$personell_nr."'";
            
            $username = ($db->getOne($getUsername)); 
             
            #$objResponse->alert($password);
            #$objResponse->alert($sql);
            # added by: syboy 03/30/2016 : meow
            $areas_sql = "INSERT INTO seg_areas_duration_time (" .
                            "pid,".
                            "duration,".
                            "mode,".
                            "create_id,".
                            "create_dt".
                            ") VALUES (".
                            $db->qstr($personell_nr) . "," .
                            $db->qstr($durationTime) . "," .
                            $db->qstr("update pass") . "," .
                            $db->qstr($_SESSION['sess_user_name']) . "," .
                            $db->qstr(date('YmdHis')) .
                        ")";
            $db->BeginTrans();
            $ok_areas = $db->Execute($areas_sql);

            # ended syboy
            #update care_personell
            $db->BeginTrans();
            $ok = $db->Execute($sql);
            //FOR EHR 9/2/2015
            try {
                require_once($root_path . 'include/care_api_classes/doctors_mobility/DoctorService.php');
                $doctorService = new DoctorService();
                $doctorService->saveDoctor($personell_nr);
            } catch (Exception $e) {
                
            }
            if ($ok && $ok_areas){
                    $db->CommitTrans();
                    //START EHR
                    $docdata = array(
                        'username'  =>  $username,
                        'password'  =>  md5($password),
                        'nr'        =>  $personell_nr
                    );
                    require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
                    require_once($root_path . 'include/care_api_classes/API/curl_api.php');
                    $ehr = Ehr::instance();
                    //END FOR EHR
                    $patient = $ehr->doctor_postChangePassword($docdata);
                    $objResponse->alert("The personnel's password status is successfully change.");
                    $objResponse->addScriptCall("ReloadWindow");

                    $curl_obj = new Rest_Curl;
                    $curl_obj->change_password($username, $password, $personell_nr);
            }else{
                    $db->RollbackTrans();
                    $objResponse->alert("Changing personnel's password status is failed.");
            }

            return $objResponse;
    }

     function setWebexAccount($personell_nr,$webexEmail,$webexPass,$iscreate){
        global $db, $HTTP_SESSION_VARS, $root_path;
        $objResponse = new xajaxResponse();

        $site_name = 'spmc';
        if($iscreate){
             $webex_sql = "INSERT INTO seg_doctor_meeting (" .
                        "doctor_id,".
                        "site_name,".
                        "webex_id,".
                        "password,".
                        "create_dt,".
                        "create_id".
                        ") VALUES (".
                        $db->qstr($personell_nr) . "," .
                        $db->qstr($site_name) . "," .
                        $db->qstr(trim($webexEmail)) . "," .
                        $db->qstr(trim($webexPass)) . "," .
                        $db->qstr(date('YmdHis'))."," .
                        $db->qstr($_SESSION['sess_user_name']) .
                    ")";
        }else{

            $webex_sql = "UPDATE seg_doctor_meeting SET
                                    webex_id=".$db->qstr($webexEmail).",password=".$db->qstr($webexPass).",modified_dt=NOW(),modify_id=".$db->qstr($_SESSION['sess_user_name'])."
                                                        WHERE doctor_id='".$personell_nr."'";

        }
      
        $db->BeginTrans();
        $ok = $db->Execute($webex_sql);
        if ($ok){
             $db->CommitTrans();
        }else{
             $db->RollbackTrans();
        }

        return $objResponse;
    }


    #-------------------------------

    require_once('./roots.php');

    require($root_path.'include/inc_environment_global.php');
    require($root_path."modules/personell_admin/ajax/accre-insurance.common.php");
    #added by VAN 04-17-08
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/care_api_classes/class_insurance.php');
    require_once($root_path.'include/care_api_classes/class_seg_dependents.php');
    require_once($root_path.'include/care_api_classes/class_personell.php'); // Added by: Arco - 06/03/2016
    $xajax->processRequests();
