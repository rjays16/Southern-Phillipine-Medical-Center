<?php
    # Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

    $lang_tables[] = 'departments.php';
    define('LANG_FILE','lab.php');
    define('LANG_FILE','konsil.php');
    define('NO_2LEVEL_CHK',1);

    $local_user='ck_lab_user';
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/inc_front_chain_lang.php');
    require($root_path.'modules/bloodBank/ajax/blood-received-sample.common.php');

    $dbtable='care_config_global'; // Taboile name for global configurations
    $GLOBAL_CONFIG=array();
    $new_date_ok=0;

    # Create global config object
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/inc_date_format_functions.php');

    $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
    $glob_obj->getConfig('refno_%');
    if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
    $date_format=$GLOBAL_CONFIG['date_format'];

    $phpfd=$date_format;
    $phpfd=str_replace("dd", "%d", strtolower($phpfd));
    $phpfd=str_replace("mm", "%m", strtolower($phpfd));
    $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
    
    $breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;
    $thisfile=basename(__FILE__);

    # Create laboratory object
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();

    #added by VAS 12/06/2017
    #for HL7 compliant
    # Create hl7 object
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_message.php');
    $HL7Obj = new seg_create_msg_HL7();
            
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
                
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
    
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();

    require_once($root_path.'include/care_api_classes/class_blood_bank.php');
    $bloodObj = new SegBloodBank();

    require_once($root_path.'include/care_api_classes/class_person.php');
    $person_obj = new Person;

    require_once($root_path.'include/care_api_classes/class_encounter.php');
    $enc_obj=new Encounter;

    require_once($root_path.'include/care_api_classes/class_department.php');
    $dept_obj=new Department;

    require_once($root_path.'include/care_api_classes/class_ward.php');
    $ward_obj = new Ward;

    define('BLOODBANK','B');
    define(IPBMIPD_enc, 13);
    define(IPBMOPD_enc, 14);

    $details = (object) 'details';
       
    $prefix = BLOODBANK."HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";            

    $row_hosp = $objInfo->getAllHospitalInfo();
    
    # Establish db connection
    $connection_type = $row_hosp['connection_type'];
    $HTTP_SESSION_VARS['connection_type'] = $connection_type;
    if ($connection_type=='odbc'){
        require_once($root_path.'include/inc_hclab_connection.php');
        
        require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
        $hclabObj = new HCLAB;
    }else{    
    
    
    $row_comp = $objInfo->getSystemCreatorInfo();
    
    $details->protocol_type = $row_hosp['LIS_protocol_type'];
    $details->protocol = $row_hosp['LIS_protocol'];
    $details->address_lis = $row_hosp['LIS_address'];
    $details->address_local = $row_hosp['LIS_address_local'];
    $details->port = $row_hosp['LIS_port'];
    $details->username = $row_hosp['LIS_username'];
    $details->password = $row_hosp['LIS_password'];
    
    $details->folder_LIS = $row_hosp['LIS_folder_path'];
    #LIS SERVER IP
    $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path'];
    #HIS SERVER IP
    $details->directory = "\\\\".$details->address_local.$row_hosp['LIS_folder_path'];
    #HIS SERVER IP
    $details->directory_local = "\\\\".$details->address_local.$row_hosp['LIS_folder_path_local'];
    $details->extension = $row_hosp['LIS_HL7_extension'];
    $details->service_timeout = $row_hosp['service_timeout'];    #timeout in seconds
    $details->directory_LIS = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path_inbox'];
    $details->hl7extension = ".".$row_hosp['LIS_HL7_extension'];
    
        
    $transfer_method = $details->protocol_type;    
    
    #msh
    $details->system_name = trim($row_comp['system_id']);
    $details->hosp_id = trim($row_hosp['hosp_id']);
    $details->lis_name = trim($row_comp['lis_name']);
    $details->currenttime = strftime("%Y%m%d%H%M%S");
    }    
    
    #----------------------

    global $db, $allow_updateBBDates;

    require_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common');

    require_once($root_path . 'include/care_api_classes/class_acl.php');
                $acl = new Acl($_SESSION['sess_temp_userid']);
                $caneditdate = $acl->checkPermissionRaw(array('_a_1_EditDate')); #edited by art 09/17/2014
                $caneditconsume = $acl->checkPermissionRaw(array('_a_1_editConsumedDate')); // added by Gervie 09/12/2015
               #$allpermission = $acl->checkPermissionRaw(array('_a_0_all'));
    $multiplePrintCompatibilityReport = $acl->checkPermissionRaw(array('_a_1_printMultipleCompatibility')); #edited by justin 03/23/2015
                $caneditissuance = $acl->checkPermissionRaw(array('_a_1_editIssuanceDate')); // add by gervie 07/26/2015
                $caneditrelease = $acl->checkPermissionRaw(array('_a_1_editReleaseDate')); // add by gervie 07/26/2015



    if ($_GET['refno'])
        $refno=$_GET['refno'];
    elseif ($_POST['refno'])
        $refno=$_POST['refno'];    

    if ($_GET['service_code'])
        $service_code = $_GET['service_code'];
    elseif ($_POST['service_code'])
        $service_code = $_POST['service_code'];    

    if ($_GET['price'])
        $price = $_GET['price'];
    elseif ($_POST['price'])
        $price = $_POST['price'];

    if ($_GET['encounter_nr'])
        $encBR = $_GET['encounter_nr'];
    elseif ($_POST['encounter_nr'])
        $encBR = $_POST['encounter_nr'];

    if ($_GET['type'])
        $type = $_GET['type'];
    elseif ($_POST['type'])
        $type = $_POST['type'];

    if ($_GET['qty'])
        $sQty = $_GET['qty'];
    elseif ($_POST['qty'])
        $sQty = $_POST['qty'];

    $with_lis = 0;
    $row_px = $srvObj->getPatientInfoRefno($refno, $service_code);
    $quantity = $row_px['quantity'];    
     # Assign Body Onload javascript code
     #$onLoadJS='';
     #$smarty->assign('sOnLoadJs',$onLoadJS);
     $smarty->assign('sOnLoadJs','onLoad="preset();"');
     if ($_POST["submitted"]){
        $arraySampleItems_h = array();
        $arraySampleItems_sh = array();
        $arraySampleItems_d = array();
        $hasrec = 0;
        $islack = 0;
        $with_sample_rec = 0;
        $received_qty = 0;
        $isstat = 0;
        $getSerialCon = '';
        $getResult = '';
         $getFinalRes = '';
         $countforRes = 1;
        for($i=1;$i<=$quantity;$i++){          

          #edited by VAS 12/05/2017  
          $id = trim($service_code.$i);

          if ($_POST['is_received'.$id]){
            $status_rec_d = "received";
          }else
            $status_rec_d = "not yet";  
          
          $ordering = $_POST['index'.$id];
          $serial = $_POST['serial'.$id];
          $is_status = $_POST['is_status'.$id];
          $getSerialCon .= $serial. " ";

          $is_status_1 = $_POST['is_status_1'.$id];
          
          //Add Blood Ward/Dept (Borj) in 2014-12-07
          $blood_dept = $_POST['blood_dept'.$id];
          $component = $_POST['component'.$id];
          //Add Blood Source and Others in 2014-18-03
          $blood_source = $_POST['blood_source'.$id];
          $others = $_POST['others'.$id];
          
          $result = $_POST['result'.$id];
          
          if (!$result)
            $result = 'noresult';
          
          #date received
          $date_received = $_POST['date_received'.$id];
          $time_received = $_POST['time_received'.$id];
          $meridian = $_POST['meridian2'.$id];
          #echo $meridian;
         
          $datetime =  $date_received." ".$time_received." ".$meridian;
          $date = trim($date_received." ".$time_received);

          if (empty($date))
            $received_date = "0000-00-00 00:00:00";
          else  {
            $received_date = date("Y-m-d H:i:s", strtotime($datetime));
            
            #added by VAS 12/06/2017
            #to be posted to LIS
            // $arrayItems[] = array($service_code, $i);
            // $with_lis =+ 1;
        }
          
          //Add Blood Source and Others in 2014-18-03 (Borj)
          //Add Blood Ward/Dept in 2014-12-07 (Borj)
          //routine and stat

            if($_POST['is_status_1'.$id]==1){
                $is_status_1 = '1';
                $isstat =+ 1;
            }else{
                $is_status_1 = '0';
            }

            if($_POST['hiddenRes'.$i] != $_POST['result'.$id]){
                $result = $_POST['result'.$id];
                $updateResultForSerial .= $_POST['serial'.$id].' ';
                if($result == "incompat"){
                    $getUpdatedResult .= "Incompatible, ";
                }else if($result == "retype") {
                    $getUpdatedResult .= "Re-Typing, ";
                }else if($result == "noresult"){
                    $getUpdatedResult .= "No Result Yet, ";
                }else if($result == "compat"){
                    $getUpdatedResult .= "Compatible, ";
                }

                $sqlforCheckRes = $db->GetAll("SELECT * FROM `seg_blood_monitoring` WHERE service_code = '" . rtrim($getUpdatedResult, ', ') . "' AND blood_type = '" . $row_px['blood_type'] . "' AND pid = '" . $row_px['pid'] . "' AND serial_no = '" . $_POST['serial' . $id] . "' ");
                if (count($sqlforCheckRes) == 1) {
                    $getUpdatedResult = '';
                    $updateResultForSerial = '';
                }
            }

            if($_POST['serialhidden'.$id] != $_POST['serial'.$id]) {
                $getUpdatedSerial .= $_POST['serial'.$id]." ";
                $result = $_POST['result'.$id];
                if($result == "incompat"){
                    $updateSerialForResult .= "Incompatible, ";
                }else if($result == "retype") {
                    $updateSerialForResult .= "Re-Typing, ";
                }else if($result == "noresult"){
                    $updateSerialForResult .= "No Result Yet, ";
                }else if($result == "compat"){
                    $updateSerialForResult .= "Compatible, ";
                }
                
            }

          if($result == "incompat"){
            $getResult .= "Incompatible, ";
           }else if($result == "retype") {
            $getResult .= "Re-Typing, ";
           }else if($result == "noresult"){
            $getResult .= "No Result Yet, ";
           }else if($result == "compat"){
              $getResult .= "Compatible, ";
           }



          $arraySampleItems_d[] = array($ordering, $received_date, $component, $serial, $status_rec_d, $result,$blood_source,$others,$blood_dept,$is_status_1);

        }
        #$status_sh = 'active';
        $received_qty = $_POST['received_qty'];
        $_POST['ordered_qty'] = $quantity;
        
        if ($received_qty == 0){
            $hasrec = 0;
            $islack =+ 1;
        }elseif ($received_qty < $quantity){
            $hasrec =+ 1;
            $islack =+ 1;
            $with_sample_rec =+ 1;
        }elseif ($received_qty == $quantity){
            $hasrec =+ 1;
            $iscomplete =+ 1;
            $with_sample_rec =+ 1;
        }
        
        if ($hasrec == 0)
            $status_rec = 'none';
         elseif ($islack)
            $status_rec = 'lack';
         elseif ($iscomplete)
            $status_rec = 'complete';
        
        $status_sh = $status_rec;    
        $_POST['status_sh'] = $status_sh;
        $_POST['status_rec'] = $status_rec;

        $_POST['arraySampleItems_d'] = $arraySampleItems_d;        
        
        $ok = $srvObj->updatebloodReceivedSample($refno, $service_code, $_POST);
        
        if ($ok){
            $smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");

            #update request to serve upon RECEIVE
            $status = 'done';
            $is_served = 1;
            $clerk = $HTTP_SESSION_VARS['sess_user_name'];
            $date_served = date("Y-m-d H:i:s");
            
            #$arrayItemsList = array();
            $arrayItemsList[] = array($status, $is_served, $date_served, $clerk, $date_served, $service_code);
            $srvObj->DoneRequest($refno,$arrayItemsList);
             

            //EHR is_served ==================================================>
            require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
            $itemLists = array();
            $requestinfo = $srvObj->getLabServiceReqInfo($refno);
            $itemRaw = array(
                "service_id"    => $service_code,
                "is_served"     => $is_served,
                "date_modified" => $_POST['current_date']
            );

            array_push($itemLists, $itemRaw);

            $data = array(
                "refno"         =>  $refno,
                "encounter_nr"  =>  $requestinfo['encounter_nr'],
                "items"         =>  $itemLists
            ); 

            $sql_check = $db->GetAll("SELECT * FROM seg_blood_monitoring WHERE date_received = '".$received_date."' AND pid = '".$requestinfo['pid']."'");

            $bbMon = array(
                            "pid"               =>  $requestinfo['pid'],
                            "refno"             =>  $refno, 
                            "service_code"      =>  rtrim($getResult, ', '),
                            "blood_type"        =>  $row_px['blood_type'],
                            "component"         =>  $component,
                            "ordered_qty"       =>  $_POST['ordered_qty'],
                            "serial_no"         =>  $getSerialCon, 
                            "status"            =>  $status_rec, 
                            "date_received"     =>  $received_date, 
                            "create_id"         =>  $_SESSION['sess_temp_userid'],
                            "create_dt"         =>  date("Y-m-d H:i:s")
                        ); 
            $bbMonUpdateSerial = array(
                            "pid"               =>  $requestinfo['pid'],
                            "refno"             =>  $refno, 
                            "service_code"      =>  rtrim($updateSerialForResult, ', '),
                            "blood_type"        =>  $row_px['blood_type'],
                            "component"         =>  $component,
                            "ordered_qty"       =>  $_POST['ordered_qty'],
                            "serial_no"         =>  $getUpdatedSerial,
                            "status"            =>  $status_rec,
                            "create_id"         =>  $_SESSION['sess_temp_userid'],
                            "create_dt"         =>  date("Y-m-d H:i:s")
                        );
            $bbMonUpdateResult = array(
                            "pid"               =>  $requestinfo['pid'],
                            "refno"             =>  $refno,
                            "service_code"      =>  rtrim($getUpdatedResult, ', '),
                            "blood_type"        =>  $row_px['blood_type'],
                            "component"         =>  $component,
                            "ordered_qty"       =>  $_POST['ordered_qty'],
                            "serial_no"         =>  $updateResultForSerial,
                            "status"            =>  $status_rec,
                            "create_id"         =>  $_SESSION['sess_temp_userid'],
                            "create_dt"         =>  date("Y-m-d H:i:s")
                        );
            if(count($sql_check) == 0) {
                $bloodObj->saveBloodMonitoringInfoFromArray($bbMon);
            }else if($getUpdatedSerial != NULL){
                $bloodObj->saveBloodMonitoringInfoFromArray($bbMonUpdateSerial);
            }else if($getUpdatedResult != NULL && count($sqlforCheckRes) == 0){
                $bloodObj->saveBloodMonitoringInfoFromArray($bbMonUpdateResult);
            }

            
            $ehr = Ehr::instance();
            $response = $ehr->postServeLabRequest($data);
            $asd = $ehr->getResponseData();
            $EHRstatus = $response->status;

            //EHR is_served ==================================================>


            #added by VAS 12/05/2017
            # for HL7 compliant
            #if successfully saved or updated
            #if (($connection_type=='hl7') && (count($arrayItems))){
            if ($connection_type=='hl7'){
              $fileObj = new seg_create_HL7_file($details);  
              
              $testinfo = $srvObj->getLabServiceInfo($service_code, BLOODBANK);
              $requestinfo = $srvObj->getLabServiceReqInfo($refno);
              $testinfo['name'] = trim($testinfo['name']);

              $pid = $requestinfo['pid'];
              $encounter_nr = $requestinfo['encounter_nr'];
              // $patient = $person_obj->getAllInfoArrayforBB($pid);
              $patient = $person_obj->getAllInfoArrayforBB($pid,$encounter_nr);
              
              $testreqinfo = $srvObj->getRequestTestInfo($refno, $service_code);
                  
              #check if the test item must be posted to LIS
              if ($testinfo['in_lis']==1){

                # Observation order - event O01
                $msg_type = "ORM";
                $event_id = "O01";
                $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
                $details->msg_type = $hl7_msg_type;
                                                
                #pid
                $details->POH_PAT_ID = trim($pid);
                $details->POH_PAT_ALTID = "";
                $details->patient_name = mb_strtoupper(trim($patient['name_first'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_last']));
                $details->POH_MIDDLENAME =mb_strtoupper(trim($patient['name_middle']));
                $details->POH_PAT_DOB = date("YmdHis",strtotime($patient['date_birth']));
                $details->POH_PAT_SEX = trim(strtoupper($patient['sex']));
                
                if ($patient['street_name']=='NOT PROVIDED')
                        $street_name = "";
                 else
                        $street_name = $patient['street_name'];
                 if ($patient['brgy_name']=='NOT PROVIDED')
                        $brgy_name = "";
                 else
                        $brgy_name = $patient['brgy_name'];

                 $mun_name = $patient['mun_name'];

                $addr = implode(", ",array_filter(array($street_name, $brgy_name, $mun_name)));
                 
                $details->address = trim($street_name).$COMPONENT_SEPARATOR.trim($brgy_name).$COMPONENT_SEPARATOR.trim($mun_name).$COMPONENT_SEPARATOR.trim($prov_name).$COMPONENT_SEPARATOR.trim($zipcode);
                $details->POH_CIVIL_STAT = trim(strtoupper($patient['civil_status']));

                if($patient){
                switch ($patient['encounter_type']){
                    case '1' :              
                                $enctype = "ER PATIENTs";
                                $patient_type = "ER";
                                $lis_test_code = trim($testinfo['erservice_code']);
                                $location1 = "ER".$COMPONENT_SEPARATOR."ER"; 
                                $loc_code = "ER";
                                $erLoc = $dept_obj->getERLocation($patient['er_location'], $patient['er_location_lobby']);
                                $lobby = ($erLoc['lobby_name'] != null) ? " (" . $erLoc['lobby_name'] . ")" : "";

                                $loc_name = "ER";
                                if ($erLoc['area_location'])
                                    $loc_name = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";

                                break;
                    case '2' :
                                $enctype = "OUTPATIENT";
                                $patient_type = "OP";
                                $lis_test_code = trim($testinfo['oservice_code']);
                                $location1 = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";
                                $loc_code = $patient['current_dept_nr'];
                                if ($loc_code)
                                    $dept = $dept_obj->getDeptAllInfo($loc_code);

                                $loc_name = stripslashes($dept['name_formal']);
                                
                                $rsdetails = $srvObj->getTestRequest($_POST['refno']);
                                    
                                if ($dept['parent_dept_nr']!=$rsdetails['request_dept']){
                                $loc_code2 = $rsdetails['request_dept'];
                                if ($loc_code2)
                                    $dept2 = $dept_obj->getDeptAllInfo($loc_code2);

                                $loc_name2 = stripslashes($dept2['name_formal']);
                                }    
                                break;
                    case '3' :  
                                $enctype = "INPATIENT (ER)";
                                $patient_type = "IN";
                                $lis_test_code = trim($testinfo['ipdservice_code']);
                                $location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
                                $loc_code = $patient['current_ward_nr'];
                                if ($loc_code)
                                    $ward = $ward_obj->getWardInfo($loc_code);

                                $room_nr = " Room #: " . $patient['current_room_nr'];
                                $bed_nr = $ward_obj->getCurrentBedNr($patient['encounter_nr']);
                                $bed = ($bed_nr) ? " Bed #: " . $bed_nr : '';

                                $loc_name = stripslashes($ward['name']) . $room_nr . $bed;
                                break;
                    case '4' :
                                $enctype = "INPATIENT (OPD)";
                                $patient_type = "IN";
                                $lis_test_code = trim($testinfo['ipdservice_code']);
                                $location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
                                $loc_code = $patient['current_ward_nr'];
                                if ($loc_code)
                                    $ward = $ward_obj->getWardInfo($loc_code);

                                $room_nr = " Room #: " . $patient['current_room_nr'];
                                $bed_nr = $ward_obj->getCurrentBedNr($patient['encounter_nr']);
                                $bed = ($bed_nr) ? " Bed #: " . $bed_nr : '';

                                $loc_name = stripslashes($ward['name']) . $room_nr . $bed;
                                break;
                    case '5' :
                                $enctype = "RDU";
                                $patient_type = "RDU";
                                $lis_test_code = trim($testinfo['ipdservice_code']);
                                $location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
                                $loc_code = "RDU";
                                $loc_name = "RDU";
                                break;
                    case '6' :
                                $enctype = "INDUSTRIAL CLINIC";
                                $patient_type = "IC";
                                $lis_test_code = trim($testinfo['oservice_code']);
                                $location1 = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";
                                $loc_code = "IC";
                                $loc_name = "INDUSTRIAL CLINIC";
                                break;

                    case IPBMIPD_enc:
                                $enctype = "INPATIENT (IPBM)";
                                $patient_type = "IN";
                                $lis_test_code = trim($testinfo['ipdservice_code']);
                                $location1 = "IPBM".$COMPONENT_SEPARATOR."IPD";
                                $loc_code = $patient['current_ward_nr'];
                                if ($loc_code)
                                    $ward = $ward_obj->getWardInfo($loc_code);

                                $room_nr = " Room #: " . $patient['current_room_nr'];
                                $bed_nr = $ward_obj->getCurrentBedNr($patient['encounter_nr']);
                                $bed = ($bed_nr) ? " Bed #: " . $bed_nr : '';

                                $loc_name = stripslashes($ward['name']) . $room_nr . $bed;
                                break;
                    case IPBMOPD_enc:
                                $enctype = "OUTPATIENT (IPBM)";
                                $patient_type = "OP";
                                $lis_test_code = trim($testinfo['oservice_code']);
                                $location1 = "IPBM".$COMPONENT_SEPARATOR."IPD";
                                $loc_code = $patient['current_dept_nr'];
                                if ($loc_code)
                                    $dept = $dept_obj->getDeptAllInfo($loc_code);

                                $loc_name = stripslashes($dept['name_formal']);
                                
                                $rsdetails = $srvObj->getTestRequest($_POST['refno']);
                                    
                                if ($dept['parent_dept_nr']!=$rsdetails['request_dept']){
                                $loc_code2 = $rsdetails['request_dept'];
                                if ($loc_code2)
                                    $dept2 = $dept_obj->getDeptAllInfo($loc_code2);

                                $loc_name2 = stripslashes($dept2['name_formal']);
                                }    
                                break;
                    default :
                                $enctype = "WALK-IN";
                                $patient_type = "WN";  #Walk-in
                                $lis_test_code = trim($testinfo['oservice_code']);
                                $location1 = "WN".$COMPONENT_SEPARATOR."WALKIN";
                                $loc_code = "WIN";
                                $loc_name = "WIN";
                                break;
                }
            }else{
                $enctype = "WALK-IN";
                $patient_type = "WN";  #Walk-in
                $lis_test_code = trim($testinfo['oservice_code']);
                $location1 = "WN".$COMPONENT_SEPARATOR."WALKIN";
                $loc_code = "WIN";
                $loc_name = "WIN";
            }

                #pv1
                $details->setID = "1";
                $details->POH_PAT_TYPE = mb_strtoupper($patient_type);
                
                $details->requesting_doc =  $testreqinfo['request_doctor'].$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($testreqinfo['doctor']));
                    
                $details->POH_PAT_CASENO = trim($requestinfo['encounter_nr']);
                
                $service_list = $lis_test_code.$COMPONENT_SEPARATOR.trim($testinfo['name']);

                $details->service_list = trim($service_list);

                if ($isstat){
                    $priority = "U";
                }else{
                    $priority = "R";
                }

                $details->POH_PRIORITY2 = trim($priority);
                $details->POH_TRX_DT =  date("YmdHis");
                $details->POH_CLI_INFO = addslashes(mb_strtoupper(trim($testreqinfo['clinical_info'])));
                $details->doctor = $details->requesting_doc;
                
                $details->location_dept = mb_strtoupper($loc_code).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name);
                $details->location = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
                
                if (!$loc_code2)
                    $details->location = $details->location_dept;

                $details->note =$details->POH_CLI_INFO;

                $with_item = 0;
                #for($cnt=0;$cnt<count($arrayItems);$cnt++){

                    #added by VAS 04/11/2018
                    $existhl7msg_row = $bloodObj->isExistHL7Msg($refno); 
                    
                    if ($existhl7msg_row['msg_control_id']){
                        $filecontent = $existhl7msg_row['hl7_msg'];
                        if (stristr($filecontent, 'ORC|NW|')){
                            $order_control = "RP";
                            
                        }elseif (stristr($filecontent, 'ORC|CA|')){
                            $order_control = "NW";
                            
                        }else
                            $order_control = "RP";    
                    
                    }else    
                        $order_control = "NW";


                    $hl7_row = $bloodObj->getInfo_HL7_request($refno, $lis_test_code, $testinfo['name'],$priority);

                    $create_new_order = 0;
                    $forreplace = 0;
                    if ($hl7_row['msg_control_id']){
                        #$forreplace = 1; 
                        $msg_control_id = $hl7_row['msg_control_id'];
                        $lis_order_no = $hl7_row['lis_order_no'];
                        $filecontent_current = $hl7_row['hl7_msg'];
                        

                        if ($hl7_row['msg_control_id']!=$existhl7msg_row['msg_control_id']){
                            $create_new_order = 1;
                        }

                    }else{
                        $create_new_order = 1;
                    }    
                    
                    $filecontent_parse = explode($COMPONENT_SEPARATOR, $filecontent);
                    $filecontent_parse = explode('|',$filecontent_parse[10]);
                    
                    $request = $filecontent_parse[0].'|'.$filecontent_parse[1];
                    $current_request = $testinfo['name'].'|'.$priority;
                    
                    if ($request==$current_request)
                        $forreplace = 1; 

                    

                    if (($create_new_order==1) && (!$forreplace)){
                        $msg_control_id = $bloodObj->getLastMsgControlID(BLOODBANK);
                        #obr
                        #$details->POH_ORDER_NO = $refno;
                        $lis_order_no = $bloodObj->getLastOrderNo();
                        $with_item = 1;
                    }

                    #orc
                    # NW = New Order
                    # RP = Order Replacement
                    # CA = Cancel Order
                    #$order_control = "NW";
                    $details->lis_order_no = $lis_order_no;
                    $details->POH_ORDER_NO = $lis_order_no;
                    $details->order_control = $order_control;

                    $details->msg_control_id_db = $msg_control_id;
                    $details->msg_control_id = $prefix.$msg_control_id;
                    
                    $msh_segment = $HL7Obj->createSegmentMSH($details);
                    $pid_segment = $HL7Obj->createSegmentPID($details);
                    $pv1_segment = $HL7Obj->createSegmentPV1($details);
                    $orc_segment = $HL7Obj->createSegmentORC($details);
                    $obr_segment = $HL7Obj->createSegmentOBR($details);
                    $nte_segment = $HL7Obj->createSegmentNTE($details);
                                                    
                    $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment."\n".$orc_segment."\n".$obr_segment."\n".$nte_segment;
                                                
                    $file = $details->msg_control_id;

                    #update msg control id
                    $details->msg_control_id = $details->msg_control_id_db;
                    
                    #if new message control id, update the tracker
                    #if (!$forreplace)
                    #    $hl7_ok = $bloodObj->updateHL7_msg_control_id($details->msg_control_id,BLOODBANK);
                        
                    // if ($with_item){                               
                        switch ($transfer_method){
                            #FTP (File Transf erProtocol) approach
                            case "ftp" :
                                        $transportObj = new seg_transport_HL7_file($details);
                                        $transportObj->ftp_transfer($file, $filecontent);
                                        break;
                                        
                            #window NFS approach or network file sharing
                            case "nfs" :
                                        #create a file
                                        $filename_local = $fileObj->create_file_to_local($file);
                                        #Thru file sharing
                                        #write a file to a local directory
                                        $fileObj->write_file($filename_local, $filecontent); 
                        
                                        $filename_hclab = $fileObj->create_file_to_hclab($file);
                                        #write a file to a hclab directory   
                                        $fileObj->write_file($filename_hclab, $filecontent); 
                                        unlink($filename_local);
                                        break;
                            #TCP/IP (communication approach)                    
                            case "tcp" :
                                        $transportObj = new seg_transport_HL7_file($details);
                                        
                                        #if ($transportObj->isConnected()){
                                             #send the message
                                             $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
                                             
                                             #return/print result
                                             $text = "LIS Server said:: ".$obj;
                                             #$text = "connected...";
                                        #}else{
                                        #     $text = "Unable to connect to LIS Server. Error: ".$transportObj->error."...";   
                                        #}
                                        
                                        echo $text;
                                        break;                    
                        } #end switch ($transfer_method)
                        #$details->lis_order_no = $refno;
                        $details->msg_type = $msg_type;
                        $details->event_id = $event_id;
                        $details->refno = $refno;
                        $details->pid = $pid;
                        $details->encounter_nr = $patient['encounter_nr'];
                        $details->hl7_msg =  $filecontent;
                        $details->service_code = $service_code;
                        #nth units
                        #$details->nth_units = $arrayItems[$cnt][1];
                        $details->nth_units = 1;
                        
                        $db->BeginTrans(); 

                        if(!$forreplace){
                            $hl7_ok = $bloodObj->addInfo_HL7_tracker($details);
                            // var_dump($bloodObj->sql);
                            $bSuccess = $bloodObj->updateHL7_msg_control_id($details->msg_control_id,BLOODBANK);
                            $bSuccess = $bloodObj->insert_Orderno_HCLAB($lis_order_no, $refno);
                            $bSuccess = $bloodObj->update_HCLabRefno_Tracker($lis_order_no);
                        }else{
                            $hl7_ok = $bloodObj->updateInfo_HL7_tracker($details);
                            // var_dump($bloodObj->sql);
                        }

                    // }                                   
                        
                        if ($bSuccess)
                            $db->CommitTrans();
                        else
                            $db->RollbackTrans();

                        #echo "<br>here ".$details->nth_units." == " . $details->msg_control_id . " == ".$lis_order_no;   
                     #end if there are new received unit    
                #} #end for loop $arrayItems
                
              } #end if ($testinfo['in_lis']==1)
            } #end if (($connection_type=='hl7') && (count($arrayItems)))
            #----------------------- end HL7 info
        }
        else
            $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);    
     }
     
     if ($popUp){
         $smarty->assign('bHideTitleBar',TRUE);
         $smarty->assign('bHideCopyright',TRUE);
     }
     
     # Collect javascript code
     ob_start();
     # Load the javascript code
     $xajax->printJavascript($root_path.'classes/xajax_0.5');
     
    $sql_mc = "SELECT return_reason
                           FROM seg_blood_received_status 
                           WHERE refno=".$db->qstr($refno);                             
    $show = $db->GetOne($sql_mc);

     
?>

<script language="javascript"> 
<?php
    require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();

function reportStat(id,in_lis){
    var types = $J('#is_status_1'+id).val();
    var serials = $J('#serial'+id).val();
    var depts = $J('#blood_dept'+id).val();
    var components = $J('#component'+id).val();
    var sources = $J('#blood_source'+id).val();
    var dreceived = $J('#date_received'+id).val();
    var treceived = $J('#time_received'+id).val();
    var mreceived = $J('#meridian'+id).val();
    var dstarted = $J('#date_started'+id).val();
    var tstarted = $J('#time_started'+id).val();
    var mstarted = $J('#started_meridian'+id).val();
    var ddone = $J('#date_done'+id).val();
    var tdone = $J('#time_done'+id).val();
    var mmeridian = $J('#done_meridian'+id).val();
    var result = $J('#result'+id).val();

    var pat_name = $J('#pat_name').text();
    var hrn = $J('#hrn').text();
    var age = $J('#age').text();
    var sex = $J('#sex').text();
    var blood_type = $J('#blood_type').text();
    var qty = $J('#qty').text();
    var refno = $J('#refno').text();
    var test_code = $J('#test_code').text();


    /*if (in_lis){
        var urls = '../../modules/reports/reports/BB_Compatibility_Report_lis.php?id='+id+'&serials='+serials+
            '&refno='+refno+
            '&test_code='+test_code+'';
    }else{*/
        var urls = '../../modules/reports/reports/BB_Compatibility_Report.php?id='+id+'&types='+types+'&serials='+serials+'&depts='+depts+
                '&components='+components+'&sources='+sources+'&dreceived='+dreceived+'&treceived='+treceived+'&mreceived='+mreceived+
                '&dstarted='+dstarted+'&tstarted='+tstarted+'&mstarted='+mstarted+'&ddone='+ddone+'&tdone='+tdone+'&mdone='+mmeridian+
                '&result='+result+
                '&pat_name='+pat_name+
                '&hrn='+hrn+
                '&sex='+sex+
                '&blood_type='+blood_type+
                '&qty='+qty+
                '&age='+age+
                '&refno='+refno+
                '&test_code='+test_code+'';
    //}        
    window.open(urls);
  
}

</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?= $root_path ?>modules/bloodBank/js/blood-received-sample.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/dateformat.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js" ></script> 

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<?php



    function level_label($index){
        switch($index){
            case '1' :  $label_index = 'First'; break;
            case '2' :  $label_index = 'Second'; break;
            case '3' :  $label_index = 'Third'; break;
            case '4' :  $label_index = 'Fourth'; break;
            case '5' :  $label_index = 'Fifth'; break;
            case '6' :  $label_index = 'Sixth'; break;
            case '7' :  $label_index = 'Seventh'; break;
            case '8' :  $label_index = 'Eighth'; break;
            case '9' :  $label_index = 'Ninth'; break;
            case '10' : $label_index = 'Tenth'; break;
            case '11' : $label_index = 'Eleventh'; break;
            case '12' : $label_index = 'Twelfth'; break;
            case '13' : $label_index = 'Thirteenth'; break;
            case '14' : $label_index = 'Fourteenth'; break;
            case '15' : $label_index = 'Fifteenth'; break;
            case '16' : $label_index = 'Sixteenth'; break;
            case '17' : $label_index = 'Seventeenth'; break;
            case '18' : $label_index = 'Eighteenth'; break;
            case '19' : $label_index = 'Nineteenth'; break;
            case '20' : $label_index = 'Twentieth'; break;
            case '21' : $label_index = 'Twenty First'; break;
            case '22' : $label_index = 'Twenty Second'; break;
            case '23' : $label_index = 'Twenty Third'; break;
            case '24' : $label_index = 'Twenty Fourth'; break;
            case '25' : $label_index = 'Twenty Fift'; break;
            case '26' : $label_index = 'Twenty Sixth'; break;
            case '27' : $label_index = 'Twenty Seventh'; break;
            case '28' : $label_index = 'Twenty Eighth'; break;
            case '29' : $label_index = 'Twenty Ninth'; break;
            case '30' : $label_index = 'Thirtieth'; break;
        }

        return $label_index;
    }
    
    

    $sTemp = ob_get_contents();
    ob_end_clean();
    $smarty->append('JavaScript',$sTemp);
    
    $smarty->assign('sPatientName',mb_strtoupper($row_px['patient_name']));
    $smarty->assign('sHRN',$row_px['pid']);
    
    $smarty->assign('sAge',$row_px['age']);
    $smarty->assign('sSex',$row_px['sex']);
    $smarty->assign('sBloodType',$row_px['blood_type']);
    
    $smarty->assign('sRefno',$refno);
    $smarty->assign('sTestName',$row_px['test_name']);
    $smarty->assign('sTestCode',$service_code);
    $smarty->assign('sQuantity',$row_px['quantity']);
    
    $date_encoded =date("m/d/Y h:iA", strtotime($row_px['serv_dt']." ".$row_px['serv_tm']));
    $smarty->assign('sDateEncoded',$date_encoded);

    $row_rs = $srvObj->getTestbyRefno($refno, $service_code);
    $norows = $srvObj->FoundRows();
    $date_format2 = '%m/%d/%Y';

    $no_quantity = $row_rs['quantity'];

    #check if in LIS
    $in_lis = $srvObj->isTestinLIS($service_code);

    if ($norows){
        if ($row_rs['quantity']){
            $i=1;
            $indexCheck = 1;
            $data = array();
            for($i=1; $i<=$row_rs['quantity']; $i++){

               // echo $is_status_1;
                
                
                $row_i = $srvObj->getBloodReceived($refno, $service_code, $i);
                $row_status = $srvObj->getBloodReceivedStatus($refno, $service_code, $i);
                
                $service_code = $row_rs['service_code'];             
                $no_repeat = 0;
                
                #edited by VAS 12/05/2017    
                $id = trim($service_code.$i);
                
                $chk = "";
                if ($row_i['status'] == 'received') {
                    $chk = "chk"; 
                }
                else{
                    $chk = "";
                }

                $trap_chk = '<div><input type="hidden" id="trap_chk'.$id.'" value="'.$chk.'"></div>';
                $trap_dateStarted = '<div><input type="hidden" id="trap_dateStarted'.$id.'" value=""></div>';
                $trap_dateDone = '<div><input type="hidden" id="trap_dateDone'.$id.'" value=""></div>';
                $trap_result = '<div><input type="hidden" id="trap_result'.$id.'" value=""></div>';
                $trap_issuanceDate = '<div><input type="hidden" id="trap_issuanceDate'.$id.'" value=""></div>';
                $trap_reIssue = '<div><input type="hidden" id="trap_reIssue'.$id.'" value=""></div>';
                $trap_consumed = '<div><input type="hidden" id="trap_consumed'.$id.'" value=""></div>';
                
                $checkox = '<input '.(($row_i['status']=='received')?'checked="checked" onclick="alert(\'This test is already recieved.\');return false;"':'').' type="checkbox" value="1" id="is_received'.$id.'" name="is_received'.$id.'" loadvalue="'. ($row_i['dept']?$row_i['dept']:"None_Existent") .'" onChange="setEnable(\''.$id.'\',\''.$no_repeat.'\',\''.$price.'\',\''.$encBR.'\',\''.$service_code.'\',\''.$type.'\',\''.$sQty.'\');changedept(\''.$id.'\',\''.$no_repeat.'\');">';
                $hiddenIdentifier='BAD';
                $index_label = level_label($i);
                $label =  $index_label." Test".'<input value="'.$i.'" type="hidden" id="index'.$id.'" name="index'.$id.'">';
                
                #$date_received = '<input type="text" readonly="readonly" maxlength="10" size="8" value="" class="segInput" id="date_received'.$id.'" name="date_received'.$id.'">
                #                  <button disabled class="segButton" id="date_received_trigger'.$id.'"><img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">Set</button>';
                
                if (($row_i['received_date']=='0000-00-00 00:00:00')||($row_i['received_date']=='')){
                    $date_received = '';
                    $time_received = '';
                    $meridian = 'AM';
                }else{
                    $date_received = date("m/d/Y",strtotime($row_i['received_date']));
                    $time_received = date("h:i",strtotime($row_i['received_date']));
                    $meridian = date("A",strtotime($row_i['received_date']));
                }

                 if (($row_status['started_date']=='0000-00-00 00:00:00')||($row_status['started_date']=='')){
                    $date_started = '';
                    $time_started = '';
                    $started_meridian = 'AM';
                }else{
                    $date_started = date("m/d/Y",strtotime($row_status['started_date']));
                    $time_started = date("h:i",strtotime($row_status['started_date']));
                    $started_meridian = date("A",strtotime($row_status['started_date']));
                }
                
                if (($row_status['done_date']=='0000-00-00 00:00:00')||($row_status['done_date']=='')){
                    $date_done = '';
                    $time_done = '';
                    $done_meridian = 'AM';
                }else{
                    $date_done = date("m/d/Y",strtotime($row_status['done_date']));
                    $time_done = date("h:i",strtotime($row_status['done_date']));
                    $done_meridian = date("A",strtotime($row_status['done_date']));
                }
                
                if (($row_status['issuance_date']=='0000-00-00 00:00:00')||($row_status['issuance_date']=='')){
                    $date_issuance = '';
                    $time_issuance = '';
                    $issuance_meridian = 'AM';
                }else{
                    $date_issuance = date("m/d/Y",strtotime($row_status['issuance_date']));
                    $time_issuance = date("h:i",strtotime($row_status['issuance_date']));
                    $issuance_meridian = date("A",strtotime($row_status['issuance_date']));
                }
                //added by:borj 2013/23/11
                if (($row_status['date_return']=='0000-00-00 00:00:00')||($row_status['date_return']=='')){
                    $date_returned = '';
                    $time_returned = '';
                    $returned_meridian = 'AM';
                }else{
                    $date_returned = date("m/d/Y",strtotime($row_status['date_return']));
                    $time_returned = date("h:i",strtotime($row_status['date_return']));
                    $returned_meridian = date("A",strtotime($row_status['date_return']));
                }

                 if (($row_status['date_reissue']=='0000-00-00 00:00:00')||($row_status['date_reissue']=='')){
                    $date_reissue = '';
                    $time_reissue = '';
                    $reissue_meridian = 'AM';
                }else{
                    $date_reissue = date("m/d/Y",strtotime($row_status['date_reissue']));
                    $time_reissue = date("h:i",strtotime($row_status['date_reissue']));
                    $reissue_meridian = date("A",strtotime($row_status['date_reissue']));
                }
                //added by Kenneth 10/06/2016
                 if (($row_status['date_released']=='0000-00-00 00:00:00')||($row_status['date_released']=='')){
                    $date_release = '';
                    $time_release = '';
                    $release_meridian = 'AM';
                }else{
                    $date_release = date("m/d/Y",strtotime($row_status['date_released']));
                    $time_release = date("h:i",strtotime($row_status['date_released']));
                    $release_meridian = date("A",strtotime($row_status['date_released']));
                }

                if (($row_status['date_consumed']=='0000-00-00 00:00:00')||($row_status['date_consumed']=='')){
                    $date_consumed = '';
                    $time_consumed = '';
                    $consumed_consumed = 'AM';
                }else{
                    $date_consumed = date("m/d/Y",strtotime($row_status['date_consumed']));
                    $time_consumed = date("h:i",strtotime($row_status['date_consumed']));
                    $consumed_meridian = date("A",strtotime($row_status['date_consumed']));
                }

                //end borj

                #added by KENTOOT 06/21/2014
                $date_received_row = '<div class="input text">
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" id="date_received'.$id.'" name="date_received'.$id.'" value="'.$date_received.'" class="segInput" readonly>
                                    </div>
                                    <button disabled id="date_received_trigger'.$id.'" name="date_received_trigger'.$id.'" style="cursor: pointer;  width: 25px" onclick="return false" title="Select Received Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    <br>
                                    
                                    <input class="segInput" maxlength="5" size="1" id="time_received'.$id.'" name="time_received'.$id.'" value="'.$time_received.'" type="text">';
                
                $date_received_row .= '<select disabled class="segInput" name="meridian'.$id.'" id="meridian'.$id.'" onchange ="meridianRecieve(\''.$id.'\')">
                                        <option '.(($meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;';
                
                   
                $date_received_row .= '<input type="hidden" maxlength="5" size="2" id="meridian2'.$id.'" name="meridian2'.$id.'" value="'.$meridian.'" class="segInput"></div>';
                
                #date started
                $checkox_started = '<input '.((($date_started!='')&&($date_started!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_started'.$id.'" name="is_started'.$id.'" onClick="getStartedCurrentDate(\''.$id.'\',\''.$chk.'\');">';
                $date_started_row = '<div class="input text">
                                '.$checkox_started.'
                                    <div style="display:inline-block">
                                        <input readonly="readonly" type="text" maxlength="10" size="7" id="date_started'.$id.'" name="date_started'.$id.'" value="'.$date_started.'" class="segInput">
                                    </div>
                                    <button id="date_started_trigger'.$id.'" name="date_started_trigger'.$id.'" style="cursor: pointer;  width: 25px" onclick="return false" title="Select Date Started">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_started_save'.$id.'" name="date_started_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Date Started">
                                    <br>
                                    <input readonly="readonly" class="segInput" maxlength="5" size="1" id="time_started'.$id.'" name="time_started'.$id.'" value="'.$time_started.'" type="text" value="">
                                    <select disabled class="segInput" name="started_meridian'.$id.'" id="started_meridian'.$id.'">
                                        <option '.(($started_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($started_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_started_cancel'.$id.'" name="date_started_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Date Started">
                                  </div>';
                 
                    $checkox_type = 'Stat<input '.(($row_i['is_urgents']=='1')?'checked="checked" ':'').' type="checkbox" value="1" class="checkdata_1" id="is_status_1'.$id.'" name="is_status_1'.$id.'" readonly>';               
                

                 //mother routine and stat     
                $CheckAll = 'Stat?<input type="checkbox" value="Stat" id="isStat_1" name="isStat_1"  checked=checked>';
                $smarty->assign('sCheckboxAll',$CheckAll);



                if($row_i['is_urgents']=='0' || trim($row_i['is_urgents'])==''){
                    $indexCheck = 0;
                }
                
                #date done
                #end KENTOOT
                $checkox_done = '<input '.((($date_done!='')&&($date_done!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_done'.$id.'" name="is_done'.$id.'" onClick="getDoneCurrentDate(\''.$id.'\',\''.$chk.'\')">';
                $date_done_row = '<div class="input text">
                                '.$checkox_done.'
                                    <div style="display:inline-block">
                                        <input readonly="readonly" type="text" maxlength="10" size="7" id="date_done'.$id.'" name="date_done'.$id.'" value="'.$date_done.'" class="segInput">
                                    </div>
                                    <button id="date_done_trigger'.$id.'" name="date_done_trigger'.$id.'" style="cursor: pointer;  width: 25px" onclick="return false" title="Select Date Done">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_done_save'.$id.'" name="date_done_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Date Done">
                                    <br>
                                    <input readonly="readonly" class="segInput" maxlength="5" size="1" id="time_done'.$id.'" name="time_done'.$id.'" value="'.$time_done.'" type="text" value="">
                                    <select disabled class="segInput" name="done_meridian'.$id.'" id="done_meridian'.$id.'">
                                        <option '.(($done_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($done_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_done_cancel'.$id.'" name="date_done_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Date Done">
                                  </div>';                  
                
                # issuance date
                $checkox_issued = '<input '.((($date_issuance!='')&&($date_issuance!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_issued'.$id.'" name="is_issued'.$id.'" onClick="getIssuanCurrentDate(\''.$id.'\',\''.$chk.'\')">';
                if($caneditissuance){
                    $meridianDisable = '';
                    $meridianOption = '<option ' . (($issuance_meridian == 'AM') ? 'selected="selected" ' : '') . ' value="AM">AM</option>
                                       <option ' . (($issuance_meridian == 'PM') ? 'selected="selected" ' : '') . ' value="PM">PM</option>';
                }
                else {
                    $meridianDisable = 'disable';
                    $meridianOption = '<option disabled ' . (($issuance_meridian == 'AM') ? 'selected="selected" ' : '') . ' value = "AM">AM</option>
                                    <option disabled ' . (($issuance_meridian == 'PM') ? 'selected="selected" ' : '') . ' value = "PM">PM</option>';
                }

                $date_issuance_row = '<div class="input text">
                                    '.$checkox_issued.'
                                    <div style="display:inline-block">
                                        <input readonly="readonly" type="text" maxlength="10" size="7" disabled id="date_issuance'.$id.'" name="date_issuance'.$id.'" value="'.$date_issuance.'" class="segInput">
                                    </div>
                                    <button id="date_issuance_trigger'.$id.'" name="date_issuance_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Issuance Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_issuance_save'.$id.'" name="date_issuance_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Issuance Date">
                                    <br>
                                    <input readonly="readonly" class="segInput" maxlength="5" size="1" id="time_issuance'.$id.'" name="time_issuance'.$id.'" value="'.$time_issuance.'" type="text" value="">
                                    <select disabled class="segInput" name="issuance_meridian'.$id.'" id="issuance_meridian'.$id.'">' .
                                        $meridianOption .
                                    '</select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_issuance_cancel'.$id.'" name="date_issuance_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Issuance Date">
                                  </div>';
                  

                //added by: borj 2013/21/11
                #returned date
                  if($date_returned==null){
                    $returnedDateShow = 'none';//hide
                    }
                     else{
                    $returnedDateShow = 'block';//show
                   }

                $checkox_returnedreason = '<input type="checkbox" id="is_returnedreason'.$id.'" title="'.$row_status['return_reason'].'" name="is_returnedreason'.$id.'" onClick="getReturnReason(\''.$mode.'\',\''.$dateinfo.'\',\''.$timeinfo.'\',\''.$id.'\')">';
                $date_returned_row =
                ''.$checkox_returnedreason.'
                <div class="input text" style="display:'.$returnedDateShow.'" id="date_returned_show'.$id.'" name="date_returned_show'.$id.'">
                                                                                                                                      
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" disabled id="date_returned'.$id.'" name="date_returned'.$id.'" value="'.$date_returned.'" class="segInput">
                                    </div>

                                    <button id="date_returned_trigger'.$id.'" name="date_returned_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Returned Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_returned_save'.$id.'" name="date_returned_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Returned Date">
                                    <br>
                                    <input class="segInput" maxlength="5" size="1" id="time_returned'.$id.'" name="time_returned'.$id.'" value="'.$time_returned.'" type="text" value="">
                                    <select class="segInput" name="returned_meridian'.$id.'" id="returned_meridian'.$id.'">
                                        <option disabled'.(($returned_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option disabled'.(($returned_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_returned_cancel'.$id.'" name="date_returned_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Returned Date">
                                  </div>';
                 #reissue date
                 if($date_returned==null){
                    $reissueDateShow = 'none';//hide
                    }
                     else{
                    $reissueDateShow = 'block';//show
                   }


                $checkox_reissue = '<input '.((($date_reissue!='')&&($date_reissue!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_reissue'.$id.'" name="is_reissue'.$id.'" onClick="getReissueCurrentDate(\''.$id.'\')">';
                $date_reissue_row =
                 '<div class="input text" style="display:'.$reissueDateShow.'" id="date_reissue_show'.$id.'"  name="date_reissue_show'.$id.'">
                                    '.$checkox_reissue.'
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" disabled id="date_reissue'.$id.'" name="date_reissue'.$id.'" value="'.$date_reissue.'" class="segInput">
                                    </div>
                                    <button id="date_reissue_trigger'.$id.'" name="date_reissue_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Reissue Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_reissue_save'.$id.'" name="date_reissue_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Reissue Date">
                                    <br>
                                    <input class="segInput" maxlength="5" size="1" id="time_reissue'.$id.'" name="time_reissue'.$id.'" value="'.$time_reissue.'" type="text" value="">
                                    <select class="segInput" name="reissue_meridian'.$id.'" id="reissue_meridian'.$id.'">
                                        <option '.(($reissue_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($reissue_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_reissue_cancel'.$id.'" name="date_reissue_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Reissue Date">
                                  </div>';

                #consumed date
                if($date_issuance==null){
                    $consumedDateShow = 'none';//hide
                    }
                     else{
                    $consumedDateShow = 'block';//show
                   }                 
                $checkox_consumed = '<input '.((($date_consumed!='')&&($date_consumed!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_consumed'.$id.'" name="is_consumed'.$id.'" onClick="getConsumedCurrentDate(\''.$id.'\')">';

                if($caneditconsume){
                    $date_disable = '';
                    $consume_disable = '<option '.(($consumed_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($consumed_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>';
                }
                else{
                    $date_disable = 'disabled';
                    $consume_disable = '<option disabled '.(($consumed_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option disabled '.(($consumed_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>';
                }

                $date_consumed_row =
                                    '<div class="input text" style="display:'.$consumedDateShow.'"  id="date_consumed_show'.$id.'"  name="date_consumed_show'.$id.'">
                                    '.$checkox_consumed.'
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7"' . $date_disable . ' id="date_consumed'.$id.'" name="date_consumed'.$id.'" value="'.$date_consumed.'" class="segInput">
                                    </div>
                                     <button id="date_consumed_trigger'.$id.'" name="date_consumed_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Reissue Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_consumed_save'.$id.'" name="date_consumed_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Consumed Date">
                                    <br>
                                    <input class="segInput" maxlength="5" size="1" id="time_consumed'.$id.'" name="time_consumed'.$id.'" value="'.$time_consumed.'" type="text" value=""/>
                                    <select class="segInput" name="consumed_meridian'.$id.'" id="consumed_meridian'.$id.'">'
                                    . $consume_disable .
                                    '</select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_consumed_cancel'.$id.'" name="date_consumed_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Consumed Date">
                                  </div>';
                //end borj
        
                #edited by VAS 06/16/2019
                #update the process flow here...  
                $serviceinfo = $srvObj->getLabServicesInfo("(s.service_code='".urlencode($service_code)."' OR s.service_code ='".$service_code."') AND s.group_code = sg.group_code");

                $serdata=$serviceinfo->FetchRow();

                $serdisabled = '';
                if($serdata['is_btreq'] && !$row_px['blood_type'])
                    $serdisabled = 'disabled';

                if ($in_lis){
                    /*$serial_col = '<input type="text" size="20" class="segInput getSerialNo" id="serial'.$id.'" name="serial'.$id.'" value="'.$row_i['serial_no'].'" onkeyup="if (event.keyCode==13) { event.preventDefault(); getLISResultInfo(\''.$pid.'\',\''.$refno.'\',\''.$service_code.'\',\''.$id.'\',\''.$i.'\');}">
                                   <input type="hidden" id="serialhidden'.$id.'" name="serialhidden'.$id.'" value="'.$row_i['serial_no'].'">';*/
                    $serial_col = '<input type="text" size="20" class="segInput" id="serial'.$id.'" name="serial'.$id.'" value="'.$row_i['serial_no'].'"'.$serdisabled.' >
                                   <input type="hidden" id="serialhidden'.$id.'" name="serialhidden'.$id.'" value="'.$row_i['serial_no'].'">';
                }else{
                    $serial_col = '<input type="text" size="20" onclick="inheritData(\''.$id.'\',\''.$no_repeat.'\');" class="segInput getSerialNo" id="serial'.$id.'" name="serial'.$id.'" value="'.$row_i['serial_no'].'"'.$serdisabled.'>
                                   <input type="hidden" id="serialhidden'.$id.'" name="serialhidden'.$id.'" value="'.$row_i['serial_no'].'">';
                }

                $component = $row_i['component'];
                
                $sql_components = 'SELECT * FROM seg_blood_component';
                $rs_components = $db->Execute($sql_components);
                $components_option="<option value=''>-Select a Component-</option>";
                if (is_object($rs_components)){
                    while ($row_components=$rs_components->FetchRow()) {
                        $selected='';
                        if ($component==$row_components['id']){
                            $selected='selected';
                        }

                        
                        $components_option.='<option '.$selected.' value="'.$row_components['id'].'">'.ucwords($row_components['long_name']).'</option>';
                    }
                }
                $components_col = '<select disabled id="component'.$id.'" name="component'.$id.'" class="segInput">
                                        '.$components_option.'
                                   </select>';
                

                 //Add Blood Ward/Dept in 2014-12-07 (Borj)
                $blood_dept = $row_i['dept'];
                
                $sql_blood_dept = 'SELECT * FROM seg_blood_dept';
                $rs_blood_dept = $db->Execute($sql_blood_dept);
                $blood_dept_option="<option value=''>-List of Wards-</option>";

                if (is_object($rs_blood_dept)){
                    while ($row_blood_dept=$rs_blood_dept->FetchRow()) {
                        $selected='';
                        if ($blood_dept==$row_blood_dept['id']){
                            $selected='selected';
                            $hiddenIdentifier='GOOD';
                        }
                        
                        $blood_dept_option.='<option '.$selected.' value="'.$row_blood_dept['id'].'">'.ucwords($row_blood_dept['long_name']).'</option>';
                    }

                }
                $blood_dept_col = '<select disabled id="blood_dept'.$id.'" name="blood_dept'.$id.'" class="segInput")">
                                        '.$blood_dept_option.' 
                                   </select>';

                 //Add Blood Source Query and Others in 2014-18-03
                $blood_source = $row_i['blood_source'];
                
                $sql_blood_source = 'SELECT * FROM seg_blood_source';
                $rs_blood_source = $db->Execute($sql_blood_source);
                $blood_source_option="<option value=''>-Blood Source-</option>";

                if (is_object($rs_blood_source)){
                    while ($row_blood_source=$rs_blood_source->FetchRow()) {
                        $selected='';
                        if ($blood_source==$row_blood_source['id'])
                            $selected='selected';
                        
                        $blood_source_option.='<option '.$selected.' value="'.$row_blood_source['id'].'">'.ucwords($row_blood_source['long_name']).'</option>';
                    }

                }
                $blood_source_col = '<select disabled id="blood_source'.$id.'" name="blood_source'.$id.'" class="segInput" onchange="others_setEnable(\''.$id.'\')">
                                        '.$blood_source_option.' 
                                   </select>';
                $hiddenperID = '<input type="hidden" name="refno" id="hidden'.$id.'" value="'.$hiddenIdentifier.'">';
                if($others==null){
                    $othersShow = 'none';//hide
                    }   
                     else{
                    $othersShow = 'block';//show
                   }    
                $others_col = '<input type="text" style="display:'.$othersShow.'" size="14" class="segInput" id="others'.$id.'" name="others'.$id.'" value="'.$row_i['others'].'">';

                $resulthidden_col = '<input type="hidden" id="hiddenRes'.$i.'" name="hiddenRes'.$i.'" value="'.$row_i['result'].'">';
                $result = $row_i['result'];


                if (!$result)
                    $result = 'noresult';
                
                $sql_result = 'SELECT * FROM seg_blood_result';
                $rs_result = $db->Execute($sql_result);
                $result_option="";
                if (is_object($rs_result)){
                    while ($row_result=$rs_result->FetchRow()) {
                        $selected='';
                        
                        if ($result==$row_result['id'])
                            $selected='selected';
                        
                        $result_option.='<option '.$selected.' value="'.$row_result['id'].'">'.ucwords($row_result['name']).'</option>';
                    }
                }                   
                $result_col = '<select id="result'.$id.'" name="result'.$id.'" class="segInput">
                                        '.$result_option.'
                                   </select>';

                $onClicker1 = 'onclick="reportStat(\''.$id.'\',\''.$in_lis.'\');" ';
                $onClicker2 = 'onclick="alert(\'To print result, save Date Started, Date Done and Result then Submit\');" ';
                # removed permission to print condition | ($multiplePrintCompatibilityReport)
                if ($in_lis){
                    $compatibilityReportBtn = '<img  id="btnSummary" '.$onClicker1.' src="'.$root_path.'/img/icons/pdf_icon.gif" height="15" width="15" style="cursor:pointer;" title="Print Compatibility Result">';
                }else{
                    if($srvObj->hasPrintedCompatibilityReport($refno, $id)){
                        $compatibilityReportBtn = '<button type="button" id="btnSummary" disabled>Print<img src="'.$root_path.'/img/icons/pdf_icon.gif" height="15" width="15"></button>';
                    }
                    else
                        $compatibilityReportBtn = '<button type="button" id="btnSummary" '.(($date_started&&$date_done&&$result!='noresult')? $onClicker1:$onClicker2).'>Print<img src="'.$root_path.'/img/icons/pdf_icon.gif" height="15" width="15"></button>';
                }    
                

                # release date
                $checkox_released = '<input '.((($date_release!='')&&($date_release!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_released'.$id.'" name="is_released'.$id.'" onClick="getReleaseCurrentDate(\''.$id.'\')">';
                # Commment by JEFF 11-17-17 as per request from QA.
                // if($caneditrelease){
                    $meridianDisable = '';
                    $meridianOption = '<option ' . (($release_meridian == 'AM') ? 'selected="selected" ' : '') . ' value="AM">AM</option>
                                       <option ' . (($release_meridian == 'PM') ? 'selected="selected" ' : '') . ' value="PM">PM</option>';
                
                
                $date_release_row = '<div class="input text">
                                    '.$checkox_released.'
                                    <div style="display:inline-block">
                                        <input readonly="readonly" type="text" maxlength="10" size="7" disabled id="date_release'.$id.'" name="date_release'.$id.'" value="'.$date_release.'" class="segInput">
                                    </div>
                                    <button id="date_release_trigger'.$id.'" name="date_release_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select release Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_release_save'.$id.'" name="date_release_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save release Date">
                                    <br>
                                    <input readonly="readonly" class="segInput" maxlength="5" size="1" id="time_release'.$id.'" name="time_release'.$id.'" value="'.$time_release.'" type="text" value="">
                                    <select disabled class="segInput" name="release_meridian'.$id.'" id="release_meridian'.$id.'">' .
                                        $meridianOption .
                                    '</select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_release_cancel'.$id.'" name="date_release_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel release Date">
                                  </div>';

                //date started
                $jsCalScript1  = '<script type="text/javascript">
                                        now = new Date();
                                        
                                        Calendar.setup ({
                                                inputField: "date_started'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_started_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });
                                        
                                    </script>
                                    ';                   
                                   
                $jsCalScript  = '<script type="text/javascript">
                                        now = new Date();
                                        
                                        Calendar.setup ({
                                                inputField: "date_done'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_done_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });
                                        
                                    </script>
                                    ';
                #added by KENTOOT 6/21/2014
                $jsCalScript2  = '<script type="text/javascript">
                                        now = new Date();
                                        
                                        Calendar.setup ({
                                                inputField: "date_received'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_received_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });
                                        
                                    </script>
                                    ';             
                                 

                #end KENTOOT
                #added by art 09/17/2014
                $jsCalScript3  = '<script type="text/javascript">
                                        now = new Date();
                
                                        Calendar.setup ({
                                                inputField: "date_reissue'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_reissue_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });
                                        
                                    </script>
                                    ';
                #end art
                if($caneditissuance) {
                    $jsCalScript4 = '<script type="text/javascript">
                                        now = new Date();

                                        Calendar.setup ({
                                                inputField: "date_issuance' . $id . '",
                                                dateFormat: "' . $date_format2 . '",
                                                trigger: "date_issuance_trigger' . $id . '",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });

                                    </script>
                                    ';
                }

                # added by Gervie 09/12/2015
                if($caneditconsume){
                    $jsCalScript5  = '<script type="text/javascript">
                                        now = new Date();

                                        Calendar.setup ({
                                                inputField: "date_consumed'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_consumed_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });

                                    </script>
                                    ';
                }
                # added by Kenneth 10/06/2015
                # comment by Jeff 11-17-17
                // if($caneditrelease){
                    $jsCalScript6  = '<script type="text/javascript">
                                        now = new Date();

                                        Calendar.setup ({
                                                inputField: "date_release'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_release_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });

                                    </script>
                                    ';
                // }
                //Add Blood Source and Others in 2014-18-03
                $class = (($i%2)==0)?"":"wardlistrow2";
                

                #added/updated by VAS 06/15/2019
                if ($in_lis){
                    $location = $row_i['dept'];
                    $blood_component = $row_i['component'];
                    $blood_source = $row_i['blood_source'];
                    $date_received = (($row_i['received_date'])&&($row_i['received_date']!='0000-00-00 00:00:00'))?date('m/d/Y h:i A',strtotime($row_i['received_date'])):'';
                    $date_crossmatched = (($row_status['started_date'])&&($row_status['started_date']!='0000-00-00 00:00:00'))?date('m/d/Y h:i A',strtotime($row_status['started_date'])):'';
                    $date_done = (($row_status['done_date'])&&($row_status['done_date']!='0000-00-00 00:00:00'))?date('m/d/Y h:i A',strtotime($row_status['done_date'])):'';
                    
                    $result_compatibility = $bloodObj->getBloodCrossmatchResultDescManual($row_i['result']);
                    
                    if(!$result_compatibility){
                        $result_compatibility = 'No Result Yet';
                        $row_i['result'] = '0';
                    }

                    
                    /*if ($row_i['result']=='compat'){
                        $color = 'green';
                    }elseif ($row_i['result']=='incompat'){
                        $color = 'red';
                    }elseif ($row_i['result']=='retype'){
                        $color = 'blue';
                    }else{
                        $color = 'brown';
                    }*/
                    $color = 'green';
                }    
                #----------ended (added by VAS 06/15/2019)


                #edited by nick, class="tdrec", 2/5/14
                #added by KENTOOT, $jsCalScript2, 6/21/2014 
                #added by art ,jsCalScript3 09/17/2014
                //date started, routine and stat
                $rows .= "<tr class=\"$class\" id=\"row$i\">
                            <td class=\"tdrec\" align=\"center\">$checkox$hiddenperID</td>
                            <td align=\"centerAlign\">$label</td>
                            <td align=\"centerAlign\">$checkox_type</td>
                            <td align=\"center\">$serial_col</td>
                            <td align=\"center\">$blood_dept_col</td>
                            <td align=\"center\">$components_col</td>
                            <td align=\"center\">$blood_source_col $others_col</td>
                            <td align=\"center\">$date_received_row</td>
                            <td align=\"center\">$date_started_row</td>
                            <td align=\"center\">$date_done_row</td>
                            <td align=\"center\">$result_col $resulthidden_col $compatibilityReportBtn $date_release_row</td>
                            <td align=\"center\">$date_issuance_row</td>
                            <td align=\"center\">$date_returned_row</td>
                            <td align=\"center\">$date_reissue_row</td>
                            <td align=\"center\">$date_consumed_row</td>
                            <td align=\"center\">$jsCalScript1</td>
                            <td align=\"center\">$jsCalScript2</td>
                            <td align=\"center\">$jsCalScript3</td>
                            <td align=\"center\">$jsCalScript4</td>
                            <td align=\"center\">$jsCalScript5</td>
                            <td align=\"center\">$jsCalScript6</td>
                            $trap_chk
                            $trap_dateStarted
                            $trap_dateDone
                            $trap_result
                            $trap_issuanceDate
                            $trap_reIssue
                            $trap_consumed
                            </tr>$jsCalScript\n";

            }

            if($indexCheck == '0'){
                $CheckAll = 'Stat?<input type="checkbox" value="Stat" id="isStat_1" name="isStat_1">';
                $smarty->assign('sCheckboxAll',$CheckAll);
            }
        }
    }else{
    
        $rows = "
                    <tr>
                        <td colspan=\"10\">Request list is currently empty...</td>
                    </tr>";
    }

    $smarty->assign('sOrderItems',$rows);
    
    $submit_btn = '<img border="0" src="../../images/btn_submit.gif" onClick="submitRequest(\''.$refno.'\',\''.$service_code.'\',\''.$no_quantity.'\');" style="margin-left: 4px; cursor: pointer;">';
    $close_btn = '<img border="0" src="../../images/reset.gif" onclick="reset(\''.$service_code.'\');" style="margin-left: 4px; cursor: pointer;">';
    $smarty->assign('sSubmitButton',$submit_btn);
    $smarty->assign('sCloseButton',$close_btn);
    
    //added by Nick, 11/23/2013 12:45 AM
    $print_btn = '<img border="0" src="../../images/btn_claim_stab.gif" onclick="printClaimStub2('.$refno.');" style="margin-left: 4px; cursor: pointer;">';
    $smarty->assign('sPrintButton',$print_btn);
                                    
             
    $smarty->assign('jsPrintDialog',$jsPrintDialog);
    $smarty->assign('printDialog',$print);

    
    //end Nick
             
    $sTemp='<input type="hidden" name="refno" id="refno" value="'.$refno.'">
            <input type="hidden" name="service_code" id="service_code" value="'.$service_code.'">
            <input type="hidden" name="quantity" id="quantity" value="'.$quantity.'">
            <input type="hidden" name="current_date" id="current_date" value="'.date("m/d/Y").'">
            <input type="hidden" name="current_time" id="current_time" value="'.date("h:i").'">
            <input type="hidden" name="current_meridian" id="current_meridian" value="'.date("A").'">
            <input type="hidden" name="submitted" id="submitted" value="0">
            <input type="hidden" name="received_qty" id="received_qty" value="0">
            <input type="hidden" name="caneditdate" id="caneditdate" value="'.$caneditdate.'">
            <input type="hidden" name="caneditdate1" id="caneditdate1" value="'.$caneditissuance.'">
            <input type="hidden" name="caneditrelease" id="caneditrelease" value="'.$caneditrelease.'">
            <input type="hidden" name="caneditconsume" id="caneditconsume" value="'.$caneditconsume.'">
            <input type="hidden" name="no_quantity" id="no_quantity" value="'.$no_quantity.'">
            <input type="hidden" name="in_lis" id="in_lis" value="'.$in_lis.'">
            <script>
            $J(function(){
                parent.getRequestInfo($(\'qty\').innerHTML,$(\'test_code\').innerHTML);
            });
            </script>
            ';
    $smarty->assign('sHiddenInputs',$sTemp);
    
    $smarty->assign('inLIS',$in_lis);

    $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform">');
    $smarty->assign('sFormEnd','</form>');
    
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','blood/blood-received-sample.tpl');
$smarty->display('common/mainframe.tpl');

?>