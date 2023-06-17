
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
	define("OBGUSD", 'OBGUSD');
	define("RAD",'RD');
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
	define('OB',$_GET['ob']);
	define('OBDept', 'OB');
	


	$local_user='ck_radio_user';   # burn added : September 24, 2007
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/radiology/ajax/radio-request-new.common.php');

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	//include Yii for notification
	require_once($root_path.'frontend/bootstrap.php');
	
        include_once $root_path . 'include/inc_ipbm_permissions.php'; // added by carriane 10/24/17
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	$fromdept = RD;



	if($_GET['ob']){
		$obgynedept = "&ob=OB";
		$fromdept = OBGUSD;
	}

	$breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND.$obgynedept;   # bun added: September 8, 2007
$thisfile=basename(__FILE__);


	#edited by CELSY 08/24/10
#transferred by VAN 05-24-2010
	#$ptype = $_GET['ptype'];
	if ($_GET['ptype'])     {
		$ptype = $_GET['ptype'];
		//echo "<br>ptype get: ".$ptype."<br>";
	}else  {
		$ptype = $_POST['ptype2'];
		//echo "<br>ptype post: ".$ptype."<br>";
	}

	if ($popUp!='1'){
			 # href for the close button
		 #$smarty->assign('breakfile',$breakfile);
	}else{
			# CLOSE button for pop-ups
			if (($_GET['view_from']=='ssview') || ($_GET['view_from']=='override'))
				$breakfile = "";
			else
				$breakfile  = "javascript:window.parent.cClick();";
	}

	$title=$LDRadiology;

	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$radio_obj = new SegRadio;

	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;

	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;

	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;

	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj = new Person;

	require_once($root_path.'include/care_api_classes/class_personell.php');
	$personell_obj = new Personell;

	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	require_once $root_path . 'include/care_api_classes/class_acl.php';
	$acl = new Acl($_SESSION['sess_temp_userid']);
	$allow_ob_manual_pay = $acl->checkPermissionRaw(array('_a_1_OBGynemanualpayment'));

	#commented by VAN 10-08-2014
	#PACS
	/*
	#added by VAN 06-16-2014
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
            
    $details = (object) 'details';
        
    $prefix = "HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";            

    $row_hosp = $objInfo->getAllHospitalInfo();
    
    $row_comp = $objInfo->getSystemCreatorInfo();
    
    $details->protocol_type = $row_hosp['PACS_protocol_type'];
    $details->protocol = $row_hosp['PACS_protocol'];
    #as is the name of the variable, dont change!!!
    #$details->address_lis
    #$details->folder_LIS
    #$details->directory_LIS

    $details->address_lis = $row_hosp['PACS_address'];
    $details->address_local = $row_hosp['PACS_address_local'];
    $details->port = $row_hosp['PACS_port'];
    $details->username = $row_hosp['PACS_username'];
    $details->password = $row_hosp['PACS_password'];
    
    $details->folder_LIS = $row_hosp['PACS_folder_path'];
    #PACS SERVER IP
    $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['PACS_folder_path'];
    #HIS SERVER IP
    $details->directory = "\\\\".$details->address_local.$row_hosp['PACS_folder_path'];
    #HIS SERVER IP
    $details->directory_local = "\\\\".$details->address_local.$row_hosp['PACS_folder_path_local'];
    #same with PACS extension
    $details->extension = $row_hosp['PACS_HL7_extension']; 
    $details->service_timeout = $row_hosp['PACS_service_timeout'];    #timeout in seconds
    $details->directory_LIS = "\\\\".$details->address_pacs.$row_hosp['PACS_folder_path_inbox'];
    $details->hl7extension = ".".$row_hosp['PACS_HL7_extension'];
    
    $transfer_method = $details->protocol_type;    
    
    #msh
    $details->system_name = trim($row_comp['system_id']);
    $details->hosp_id = trim($row_hosp['hosp_id']);
    #as is lis_name, don't change the variable, for third party software provider
    $details->lis_name = trim($row_comp['pacs_name']);
    $details->currenttime = strftime("%Y%m%d%H%M%S");    
    
    #----------------------
    */

	#added by VAN 06-03-2011
	require_once($root_path.'include/care_api_classes/class_workaround.php');
	$srvTempObj=new SegTempWorkaround();

	global $db, $allow_radiorepeat;

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}

	# added by VAN 01-11-08

	if ($_GET['repeat'])
		$repeat = $_GET['repeat'];
	else
		$repeat = $_POST['repeat'];

	#added by VAN 05-26-2011
	#to get the personnel nr of the doctor if the user is a doctor
		if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

	$personell_nr = $dept_belong['personell_nr'];

	if (stristr($dept_belong['job_function_title'],'doctor')===FALSE)
		$is_doctor = 0;
	else
		$is_doctor = 1;

	#==================


	if ($_GET['is_dr'])
	$is_dr = $_GET['is_dr'];
	else
		$is_dr = $is_doctor;
	#echo "<br>get repeat = ".$repeat."<br>";

	if ($_GET['prevbatchnr'])
		$prevbatchnr = $_GET['prevbatchnr'];

	if ($_GET['prevrefno']){
		$prevrefno = $_GET['prevrefno'];
		$sql_prev = "SELECT encounter_nr FROM seg_radio_serv WHERE refno='".$prevrefno."'";
		$rs_prev = $db->Execute($sql_prev);
		$row_prev = $rs_prev->FetchRow();
		$encounter_nr = $row_prev['encounter_nr'];

	}

	$dr_nr = $_GET['dr_nr'];
	$dept_nr = $_GET['dept_nr'];

	#added by VAN 03-19-08
	$repeaterror = $_GET['repeaterror'];

	#added by VAN 06-25-08
	$discountid_get = $_GET['discountid'];

	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];

	if ($_GET['area'])
		$area = $_GET['area'];

	if ($_GET['pid'])
		$pid = $_GET['pid'];

	if ($_GET['ref'])
		$refno=$_GET['ref'];

	if ($_GET['user_origin'])
		$user_origin = $_GET['user_origin'];

	if ($_GET['area_type'])
		$area_type = 	$_GET['area_type'];

	if ($_GET['is_rdu'])
		$is_rdu = 	$_GET['is_rdu'];

	if ($_GET['ischecklist'])
		$ischecklist = 	$_GET['ischecklist'];

	$smarty->assign('breakfile',$breakfile);
	$smarty->assign('pbBack','');

	if ($repeaterror){
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}

	if ($encounter_nr){
		$patient = $enc_obj->getEncounterInfo($encounter_nr);
		#echo "enc = ".$enc_obj->sql;
	}else if($pid){
		$patient = $person_obj->getAllInfoArray($pid);
		#echo "pid = ".$enc_obj->sql;
	}

	$expiryInfo = $objSS->getExpiryInfo($pid);
   	// echo "<pre>";
    // var_dump($patient);
    #get encounter info
    $billinfo = $enc_obj->hasSavedBilling($encounter_nr);
    if ($billinfo){
        $bill_nr = $billinfo['bill_nr'];
        $hasfinal_bill = $billinfo['is_final'];
        $is_maygohome = $patient['is_maygohome'];
    }
    $warningCaption = '';
    /*if (($bill_nr)||($is_maygohome)){
       if (($bill_nr)&&($is_maygohome)) 
            $warningCaption = "This patient has a saved billing and already advised to go home...";
       elseif (($bill_nr)&&!($is_maygohome)) 
            $warningCaption = "This patient has a saved billing...";     
       elseif (!($bill_nr)&&($is_maygohome)) 
            $warningCaption = "This patient is already advised to go home...";
    }*/
    if (($bill_nr)&&($is_maygohome)) 
        $warningCaption = "This patient has a saved billing and already advised to go home...";
    
    $smarty->assign("sWarning","<em><font color='RED'><strong>&nbsp;<span id='warningcaption'>".$warningCaption."</span></strong></font></em>"); 
    
	$rid = $radio_obj->RIDExists($pid);
	if ((($encounter_nr)||($pid))&&(!$refno)){
		$ExistNonSocial = array("B-PWD","A-PWD","C1-PWD","C2-PWD","C3-PWD");
        if(!empty($expiryInfo['pwd_expiry']) || in_array($expiryInfo['discountid'],$ExistNonSocial)){
            $pwd_expiry_dt =  strtotime($expiryInfo['pwd_expiry']);
            $now = strtotime(date("Y-m-d"));
           if(empty($expiryInfo['pwd_expiry'])){
            	$discountid = $expiryInfo['discountid'];
                $discount = $expiryInfo['discount'];
            }
            if ($pwd_expiry_dt >= $now) {
                $discountid = $expiryInfo['discountid'];
                $discount = $expiryInfo['discount'];
            }
        }else{
            $discountid = $patient['discountid'];
            $discount = $patient['discount'];
            $walkin_id_number = $patient['walkin_id_number'];
        }
	}

			if ($patient['name_middle']){
				$name_middle = mb_strtoupper(substr($patient['name_middle'],0,1));

				if ($name_middle)
					$name_middle = " ".$name_middle.".";
				else
					$name_middle = "";
			}

		 $person_name = mb_strtoupper($patient['name_last']).", ".mb_strtoupper($patient['name_first']).$name_middle;

		 if (trim($person_name)==',')
				$person_name = "";

		 if ($patient['street_name']=='NOT PROVIDED')
				$street_name = "";
		 else
				$street_name = $patient['street_name'];
		 if ($patient['brgy_name']=='NOT PROVIDED')
				$brgy_name = "";
		 else
				$brgy_name = $patient['brgy_name'];
         if ($patient['mun_name'] == 'NOT PROVIDED')
                $mun_name = "";
         else
                $mun_name = $patient['mun_name'];

		 $addr = implode(", ",array_filter(array($street_name, $brgy_name, $mun_name)));
		 if ($zipcode)
			$addr.=" ".$zipcode;
		 if ($prov_name)
			$addr.=" ".$prov_name;

		 $orderaddress = trim($addr);

		 if (($patient["admission_dt"])&&(($patient["admission_dt"]!='0000-00-00 00:00:00')||(empty($patient["admission_dt"]))))
				$admission_dt = date("m/d/Y h:i A",strtotime($patient['admission_dt']));
		 else
				$admission_dt = "";

		 if (($patient["discharge_date"])&&(($patient["discharge_date"]!='0000-00-00')||(empty($patient["discharge_date"]))))
				$discharge_date = date("m/d/Y h:i A",strtotime($patient['discharge_date']));
		 else
				$discharge_date = "";

		if ($patient['date_birth']!='0000-00-00'){
			$dob = date("Y-m-d",strtotime($patient['date_birth']));
			$time_bod = strtotime($patient["date_birth"]);
			$patient_bdate = date("n/j/Y",$time_bod);
		}else{
			$dob = "unknown";
			$patient_bdate = "00/00/0000";
		}

		if ($patient['sex']=='f'){
			$gender = "Female";
			$sex = 2;
		}elseif ($patient['sex']=='m'){
			$gender = "Male";
			$sex = 1;
		}else{
			$gender = "unknown";
			$sex = 0;
		}
		$_POST['sex'] = $sex;

		if ($patient['age'])
			$age = $patient['age'];
		else
			$age = "unknown";
	#}

	$current_att_dr_nr = $patient['current_att_dr_nr'];
	$current_dept_nr = $patient['current_dept_nr'];

	// updated by carriane 10/24/17; added IPBM encounter types
	if (($patient['encounter_type']==2)||($patient['encounter_type']==1)||($patient['encounter_type']==IPBMOPD_enc))
		$impression = ($patient['er_opd_diagnosis'] != null) ? $patient['er_opd_diagnosis'] : $patient['chief_complaint'];
	elseif (($patient['encounter_type']==3)||($patient['encounter_type']==4)||($patient['encounter_type']==IPBMIPD_enc) || $patient['encounter_type']==5)
		$impression = $patient['er_opd_diagnosis'];
	// end carriane's update

	#added by VAN 03-09-2011
	if (!$impression) {
		$impression = '';

		$impression = $enc_obj->getLatestImpression($patient['pid'], $patient['encounter_nr']);

	}

	$is_rdu = 0;

	require_once $root_path.'include/care_api_classes/class_request_source.php';
	switch ($ptype){
		case 'er' :
			$source_req = SegRequestSource::getSourceERClinics();
			break;
		case 'ipd' :
			// updated by carriane 10/24/17; added request resource if from IPBM module
			if($isIPBM)
				$source_req = SegRequestSource::getSourceIPBM();
			else
			$source_req = SegRequestSource::getSourceIPDClinics();
			break;
		case 'opd' :
			if($isIPBM)
				$source_req = SegRequestSource::getSourceIPBM();
			else
			$source_req = SegRequestSource::getSourceOPDClinics();
			break;
			// end carriane's update
		case 'phs' :
			$source_req = SegRequestSource::getSourcePHSClinics();
			break;
		case 'nursing' :
			$source_req = SegRequestSource::getSourceNursingWard();
			break;
		case 'bb' :
			$source_req = SegRequestSource::getSourceBloodBank();
			break;
		case 'spl' :
			$source_req = SegRequestSource::getSourceSpecialLab();
			break;
		case 'iclab' :
		case 'ic' :
			$source_req = SegRequestSource::getSourceIndustrialClinic();

			if (empty($encounter_nr))
				$encounter_nr_cond = " encounter_nr IS NULL ";
			else
				$encounter_nr_cond = " encounter_nr='".$encounter_nr."' ";

			$sql_ic = "SELECT c.*, t.*
										FROM seg_industrial_transaction AS t
										LEFT JOIN seg_industrial_company AS c ON c.company_id=t.agency_id
										WHERE ".$encounter_nr_cond;
			$rs_ic = $db->Execute($sql_ic);
			$row_ic = $rs_ic->FetchRow();
			$is_charge2comp = $row_ic['agency_charged'];
			$compID = $row_ic['agency_id'];
			$compName = $row_ic['name'];
			$discount = 0;
			break;
		case 'or' :
			$source_req = SegRequestSource::getSourceOR();
			break;
		case 'rdu' :
			$source_req = SegRequestSource::getSourceDialysis();
			$is_rdu = 1;
			break;
		case 'doctor' :
			$source_req = SegRequestSource::getSourceDoctor();
			break;
		default :
			if($_GET['ob']=='OB'){
					$source_req = SegRequestSource::getSourceOBGyne();
			break;
			}
			else{
			$source_req = SegRequestSource::getSourceRadiology();
			break;

			}
				}

	#$_POST["source_req"] = $source_req;

	if (empty($area_type))
		$_POST["area_type"] = NULL;

    #added by VAN 06-04-2012
    #if request is cash transaction, ignore the grant type
    if (!$_POST['iscash']){
 $_POST['request_flag'] = $_POST['grant_type'];
    }else{
        $_POST['grant_type'] = NULL;
    }

	if (empty($_POST['request_flag']))
			$_POST['request_flag'] = NULL;


		if ($patient['encounter_type']){
			$_POST['ptype'] = $patient['encounter_type'];
			$encounter_type = $patient['encounter_type'];
		}

		if($_POST['ptype']){
			$ptype = $_POST['ptype'];
		}else{
			if(!$ptype && $pid){
				$enc =$enc_obj->getLatestEncounter($pid);
				$ptype = $enc['encounter_type'];
			}
		}
if($ptype){
		switch ($ptype){
			case '1' :  $enctype = "ER PATIENT";
									$patient_type = "E";
									$loc_code = "ER";
									#$loc_name = "ER";
									$erLoc = $dept_obj->getERLocation($patient['er_location'], $patient['er_location_lobby']);
									
									if($erLoc['area_location'] != '')
									$loc_name = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
									else
									$loc_name = "EMERGENCY ROOM";
									break;
			case '2' :
									$enctype = "OUTPATIENT";
									$patient_type = "O";
									$loc_code = $patient['current_dept_nr'];
									if ($loc_code)
										$dept = $dept_obj->getDeptAllInfo($loc_code);

									$loc_name = stripslashes($dept['name_formal']);

									#if current dept is a sub department of another department
                                    #if (!($dept['parent_dept_nr'])){
                                    #added by VAN 12-29-2011
                                    /*$rsdetails = $radio_obj->getTestRequest($_POST['refno']);
                                        
                                    #check if the requesting dept is the parent dept of the current dept
                                    if ($dept['parent_dept_nr']!=$rsdetails['request_dept']){
	                                    $loc_code2 = $rsdetails['request_dept'];
	                                    if ($loc_code2)
	                                        $dept2 = $dept_obj->getDeptAllInfo($loc_code2);

	                                    $loc_name2 = stripslashes($dept2['name_formal']);
                                    }*/ 

									break;
			case '3' :  $enctype = "INPATIENT (ER)";
									$patient_type = "I";
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
									$patient_type = "I";
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
									$patient_type = "R";
									$loc_code = "RDU";
									$loc_name = "RDU";
									break;
			case '6' :
									$enctype = "INDUSTRIAL CLINIC";
									$patient_type = "C";
									$loc_code = "IC";
									$loc_name = "INDUSTRIAL CLINIC";
									break;
			// updated by carriane 10/24/17; added IPBM encounter types
			case IPBMOPD_enc:
									$enctype = "IPBM-OPD";
									$patient_type = "IPBM";
									$loc_code = $patient['current_dept_nr'];
									if ($loc_code)
										$dept = $dept_obj->getDeptAllInfo($loc_code);

									$loc_name = stripslashes($dept['name_formal']);
									break;
			case IPBMIPD_enc:
									$enctype = "IPBM-IPD";
									$patient_type = "IPBM";
									$loc_code = $patient['current_ward_nr'];
									if ($loc_code)
										$ward = $ward_obj->getWardInfo($loc_code);

									$loc_name = stripslashes($ward['name']);
									break;
			// end carriane's update
			default :
									$enctype = "WALK-IN";
									$patient_type = "W";
									$loc_code = "WIN";
									$loc_name = "WIN";
									break;
		}
	}else{
		$enctype = "WALK-IN";
		$patient_type = "W";
		$loc_code = "WIN";
		$loc_name = "WIN";
	}



		#added by VAN 06-16-2014
		if (!$loc_code2)
            $loc_code2 = $loc_code;
            
        if (!$loc_name2)
            $loc_name2 = $loc_name;    
        
        #if walkin is checked
        if ($is_walkin){
            $loc_code2 = 'WIN';
            $loc_name2 = 'WIN'; 
            $patient_type = "W";  
        }        
        
        #if from RDU
        $is_rdu = $HTTP_POST_VARS['is_rdu'];
        if (($is_rdu)||($loc_code2=='144')){
            $loc_code2 = 'DIAL';
            $loc_name2 = 'DIALYSIS';
            $patient_type = "I"; 
        }

		$location = $loc_name;
		$is_medico = $patient['is_medico'];

		$_POST['patient_type'] = $patient_type;
		$_POST['loc_code'] = $loc_code;
		$_POST['loc_name'] = $loc_name;
		$_POST['patient_bdate'] = date("n/j/Y",strtotime($_POST["date_birth"]));
		$_POST['patient_name'] = $_POST['ordername'];

		#added by VAN 06-02-2011
		if ($_POST['for_manual_payment']){
				$manual_data['service_area'] = 'RD';
				$manual_data['control_no'] = $_POST['manual_control_no'];
				$manual_data['approved_by'] = $_POST['manual_approved'];
				$manual_data['type'] = $_POST['for_manual_type'];
				$manual_data['reason'] = $_POST['manual_reason'];

				/*if ($mode=='save')
					$history_label = $radio_obj->ConcatHistory("Create: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				elseif ($mode=='update')
					$history_label = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

				$manual_data['history'] = $history_label;*/
				$manual_data['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$manual_data['create_date'] = date("Y-m-d H:i:s");
				$manual_data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$manual_data['modify_date'] = date("Y-m-d H:i:s");
				$_POST['request_flag'] = $_POST['for_manual_type'];
		}else{
				$manual_data['service_area'] = 'RD';
				$manual_data['history'] = $radio_obj->ConcatHistory("Deleted: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				$manual_data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$manual_data['modify_date'] = date("Y-m-d H:i:s");
		}

//added by Francis L.G. 03-20-13               
$cthistoryInfo = $radio_obj->getCTHistoryInfo($pid,$refno);
$mrihistoryInfo = $radio_obj->getMriHistoryInfo($pid,$refno);

if($cthistoryInfo){
    $uuidCt = $cthistoryInfo['uuid'];
}

if($mrihistoryInfo){
    $uuidMri = $mrihistoryInfo['uuid'];
}

$ctMri = $_POST['ctMri'];
$radSrv = $_POST['radId'];

//print_r($radSrv);

if($radSrv){
	$radGrpCT = array();
	$radGrpMRI = array();
    for($i=0;$i<count($radSrv);$i++){
        $tmp = $radSrv[$i];
        $radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($tmp);
        if(!in_array($radSrvGrpInfo['name'], $radGrpCT)){
            if($radSrvGrpInfo['department_nr']==167)
                $radGrpCT[] = $radSrvGrpInfo['name'];
        }

        if(!in_array($radSrvGrpInfo['name'], $radGrpMRI)){
            if($radSrvGrpInfo['department_nr']==208)
                $radGrpMRI[] = $radSrvGrpInfo['name'];
        }
    }
}


$radio_request = implode(",",$_POST['items']);

$_POST['is_tpl'] = '0';

if($_POST['grant_type'] == '' && !$_POST['iscash'])
	$_POST['is_tpl'] = '1';

if($_POST['is_cash']=='1'){
    $transType = "cash";
}
else{
    $transType = $_POST['grant_type'];
}

$reqDate = date('Y-m-d', strtotime($_POST['orderdate']));
 $success = 0;              
 $postToEmr = false;
 $updateClinicInfo = false;

 switch($mode){

		case 'save':
				if(trim($_POST['orderdate'])!=""){
					$_POST['request_date'] = date("Ymd",strtotime($_POST['orderdate']));
					$_POST['request_time'] = date('H:i:s',strtotime($_POST['orderdate']));
				}
				$_POST['loc_code'] = $loc_code;
				$_POST['clinical_info'] = $_POST['clinicInfo'];
				#$_POST['clinical_info'] = stripslashes($_POST['clinicInfo']);
				$_POST['request_doctor'] = $_POST['requestDoc'];
				#$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
#				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				#$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['encoder'] = $_SESSION['sess_temp_userid'];   
                #$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
                $_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";

                $_POST['request_doctor_out'] = $_POST['requestDocName'];
                $_POST['fromdept'] =$fromdept;
                if($fromdept!='RD'){
                	$net_pf_price = $_POST['pfnet']; 	
	              }
	              else{
	              	$net_pf_price='0.00'; 
	              }
                $_POST['pf'] = $net_pf_price;

				#$_POST['refno'] = $radio_obj->getLastRefno();

				#if ((isset($POST['repeat']))&&($_POST['repeat'])){
				if ($_POST['isrepeat']==1){
					#-------added by VAN 01-11-08-------------
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					$_POST['is_cash'] = 1;

					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];

					#-----------------------------------------

					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$radio_obj->sql;
					$isCorrectInfo = $radio_obj->count;
					if (($isCorrectInfo)||($allow_radiorepeat)){

						if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
							$rid = $radio_obj->createNewRID($_POST['pid']);
							$postToEmr = true;
	                                                $updateClinicInfo = true;
							if(OB){
								$smarty->assign('sysInfoMessage',"Ultrasound Request Service successfully created.");
							}
							else{
								$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
							}
							
						}else{
							$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
					}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=0&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
						#$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
					}
				}else{
					#$radio_obj->startTrans();

					if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
						
						$success = 1;
						$rid = $radio_obj->createNewRID($_POST['pid']);
#						$errorMsg='<font style="color:#FF0000">Successfully saved!</font>';

						/*//added by cha, 11-22-2010
						if($_POST['iscash']==1) {
							$saveok = $radio_obj->updateRequestFlagPerORNumber($_POST, $refno);
							if($saveok!==FALSE) {
						$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
							}
						} else {
							$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
						}*/

						#added by VAN 06-02-2011
						if ($_POST['for_manual_payment'])
							$saveok=$srvTempObj->save_ManualPayment($_POST['refno'], $manual_data);
						else{
							$saveok=$srvTempObj->ManualPayment($_POST['refno'], $manual_data);
							$saveok=$srvTempObj->resetRequestFlag($_POST['refno'], $manual_data, 'care_test_request_radio');
						}

						$postToEmr = true;
                                                $updateClinicInfo = true;
						if(OB){
						$smarty->assign('sysInfoMessage',"Ultrasound Request Service successfully created.");
						}
						else{
							$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
						}
						
					}else{
						$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
					}

					/*if (!$saveok){ 
                        $radio_obj->FailTrans();
						$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->sql);
                    }else{    
                        $radio_obj->CompleteTrans();
                        $smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
					}*/	
				}

				$data = array(
			        "request"=>$radio_request,
			        "priority"=>$_POST['priority'],
			        "totalAmount"=>$_POST['netTotal'],
			        "discount"=>$_POST['discountTotal'],
			        "transaction"=>$transType,
			        "reqDate"=>$reqDate,
			        "refno"=>$refno
		        );
                
				

                if(in_array("CT",$ctMri)){
                    if($refno)
                    	for($i=0;$i<count($radGrpCT);$i++){
                    		$CtCliHis = $radio_obj->updateCTClinicalHistory($pid,$uuidCt,&$data,$radGrpCT[$i]);
                    	}
                }
                
                if(in_array("MRI",$ctMri)){
                    if($refno)
                    	for($i=0;$i<count($radGrpMRI);$i++){                   
                    		$MriCliHis = $radio_obj->updateMRIClinicalHistory($pid,$uuidCt,&$data,$radGrpMRI[$i]);
                		}
                }

                if($postToEmr){
                    try {
                        require_once($root_path . 'include/care_api_classes/emr/services/RadiologyEmrService.php');
                        $radService = new RadiologyEmrService();
                        #add new argument to detect if to update patient demographic or not
                        $radService->saveRadRequest($_POST['refno']);
                    } catch (Exception $exc) {
                        // echo $exc->getTraceAsString();die;
                    }

                }
                	$is_cash=='0'? "0":"1";

                    $sectionDocPersonellNumber = $_POST['requestDoc'];
                    $sectionDoctors = $_POST['requestDocName'];
                    $sectionDept = $_POST['requestDept'];
                    $sectionClinicInfo = $_POST['clinicInfo'];
                    $ppid = $_POST['pid'];

                    $itemNames = $_POST['items'];
                    $itemPCharge = $_POST['pcharge'];
                    $itemPCash = $_POST['pcash'];
                    $itemQty = $_POST['qty'];
                    $itemDiscount1 = $_POST['discount'];
                    $itemDiscount2 = $_POST['discount2'];
                    $itemNameDT = $_POST['nameitemsDT'];
                    $service_id = $_POST['service_code'];
                    $sectionItems = array();

                for($i=0; $i<count($sectionDocPersonellNumber); $i++){
                    	$sections = array(
	                    	"personnel_nr"	=>	$sectionDocPersonellNumber[$i],
	                    	// "service_id"	=>	"AO",
	                    	"service_id"	=>	$service_id[$i],
	                    	"doctorName"	=> $sectionDoctors[$i],
	                    	"sectionDept"	=> $sectionDept[$i],
                    		"itemNameDT" => $itemNameDT,
	                    	"itemNames"	=>	$itemNames[$i],
	                    	"itemPCharge"	=> $itemPCharge[$i],
	                    	"itemDiscount1"	=> $itemDiscount1[$i],
	                    	"itemDiscount2"	=> $itemDiscount2[$i],
	                    	"itemQty"	=>	$itemQty[$i]
                    	);
                    	array_push($sectionItems,$sections);
					}
					
					$encounter_nr = $enc_obj->getCurrentEncounter($ppid);
					
                    $radReq = array(
						"pid"				=>	$_POST['pid'],
						"encounter_nr"		=>	$_POST['encounter_nr'],
	                    "sectionClinicInfo"	=> $sectionClinicInfo,
						"transactionType"	=>	array(
								"type"		=> 	$is_cash,
								"grant_type"=>	$_POST['grant_type']
							),
						"priority"			=>	$_POST['priority'],
						"comments"			=>	$_POST['comments'],
						"dstamp"			=>	$_POST['dstamp'],
						"order"				=>	array(
							array(
								"refno"		=>	$_POST['refno'],
								"encoder"	=>	$_POST['encoder'],
								"sections"	=>	array(
									$sectionItems
								)
							)

						)
                    );
                    
		require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
        $ehr = Ehr::instance();
        $patient = $ehr->postRadRequest($radReq);
        $asd = $ehr->getResponseData();
        $EHRstatus = $patient->status;

                if($updateClinicInfo) {
                	$getImp = $enc_obj->getPatientEncInfo($_POST['encounter_nr']);
                	
                	$data = array(
                		'encounter_nr' => $_POST['encounter_nr'],
                		'clinicalInfo' => $_POST['clinicInfo'][count($_POST['clinicInfo']) - 1],
                		'location' => 'RD'
                	);

if ($is_doctor || empty($getImp['er_opd_diagnosis'])  && !$_POST['area']) {
					$enc_obj->updateClinicalImpression($data);
                	$enc_obj->saveToClinicalImpressionTable($data);
}
                }
				break;
		case 'update':
				// echo "<pre>";
				// var_dump($_POST); die;
				if(trim($_POST['orderdate'])!=""){
					$_POST['request_date'] = date("Ymd",strtotime($_POST['orderdate']));
					$_POST['request_time'] = date('H:i:s',strtotime($_POST['orderdate']));
				}

				$iscash = $_POST['is_cash'];	//added by cha,, 11-23-2010

				$_POST['clinical_info'] = $_POST['clinicInfo'];
				$_POST['request_doctor'] = $_POST['requestDoc'];
				$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
                $itemNameDT = $_POST['nameitemsDT'];
                $ppid = $_POST['pid'];
				#$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['orderaddress'] = addslashes($_POST['orderaddress']);
				$_POST['encoder'] = $_SESSION['sess_temp_userid'];
                $_POST['modify_id'] = $_SESSION['sess_temp_userid'];
                #$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
                $_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");
$_POST['pf'] = $_POST['pfnet'];

	$sectionItems = array();
 				$itemLists = array();

                for($i=0; $i<count($_POST['request_doctor']); $i++){
                    	$sections = array(
	                    	"personnel_nr"	=>	$_POST['request_doctor'][$i],
	                    	// "service_id"	=>	"AO",
	                    	"service_id"	=>	$_POST['service_code'][$i],
	                    	"doctorName"	=> $_POST['requestDocName'][$i],
	                    	"sectionDept"	=> $_POST['request_dept'][$i],
                    		"itemNameDT" => $itemNameDT,
	                    	"itemNames"	=>	$_POST['service_code'][$i],
	                    	"itemPCharge"	=> $_POST['pcharge'][$i],
	                    	"itemDiscount1"	=> $_POST['discount'][$i],
	                    	"itemDiscount2"	=> $_POST['discount2'][$i],
	                    	"itemQty"	=>	$_POST['qty'][$i]
                    	);
                    	array_push($sectionItems,$sections);

						$refno = $_POST['refno'];
						$item_code = $_POST['service_code'][$i];

						$sql = "SELECT `is_served`  FROM `care_test_request_radio` WHERE refno =".$db->qstr($refno)." and service_code=".$db->qstr($item_code);
				    	$is_served_his = $db->GetOne($sql);
						$sql2 = "SELECT `served_date`  FROM `care_test_request_radio` WHERE refno =".$db->qstr($refno)." and service_code=".$db->qstr($item_code);
				    	$date_served = $db->GetOne($sql2);

				    	$data = array(
				    		"service_id"	=> $item_code,
				    		"is_served"		=> $is_served_his,
				    		"date_modified"	=> $date_served
				    	);

                    	array_push($itemLists,$data);
                    }


                    $encounter_nr = $enc_obj->getCurrentEncounter($ppid);
                   
                    $radReq = array(
						"pid"				=>	$ppid,
						"encounter_nr"		=>	$_POST['encounter_nr'],
	                    "sectionClinicInfo"	=> $_POST['clinical_info'],
						"transactionType"	=>	array(
								"type"		=> 	$iscash,
								"grant_type"=>	$_POST['grant_type']
							),
						"priority"			=>	$_POST['priority'],
						"comments"			=>	$_POST['comments'],
						"dstamp"			=>	$_POST['dstamp'],
						"order"				=>	array(
							array(
								"refno"		=>	$_POST['refno'],
								"encoder"	=>	$_POST['encoder'],
								"sections"	=>	array(
									$sectionItems
								),
								"serve_details" => $itemLists
							)

						)
                    );

				require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
		        $ehr = Ehr::instance();
		        $patient = $ehr->postRemoveRadRequestPerItem($radReq);
		        $asd = $ehr->getResponseData();
		        $EHRstatus = $patient->status;

				#if ((isset($POST['repeat']))&&($_POST['repeat'])){
				if ($_POST['isrepeat']==1){
					#-------added by VAN 01-11-08-------------
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					$_POST['is_cash'] = 1;
					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];

				#-----------------------------------------

					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$radio_obj->sql;
					$isCorrectInfo = $radio_obj->count;
					#echo "<br>count = ".$isCorrectInfo;
					if (($isCorrectInfo)||($allow_radiorepeat)){
						if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
							$rid = $radio_obj->createNewRID($_POST['pid']);
							$postToEmr = true;
	                                                $updateClinicInfo = true;
							if(OB){
							$smarty->assign('sysInfoMessage',"UltraSound Request Service successfully updated.");
							}
							else{
							$smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");
							}
							
						}else{
							$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
						}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=0&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
						#$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
					}
				}else{

					if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
						$success = 1;
						$rid = $radio_obj->createNewRID($_POST['pid']);
							
						if($iscash==1) {
							// $saveok = $radio_obj->updateRequestFlagPerORNumber($_POST, $refno);
							// if($saveok!==FALSE) {
							if(OB){
									$smarty->assign('sysInfoMessage',"Ultrasound Request Service successfully updated.");
								}
								else{
									$smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");
								}
			
							// }
						} else {
							
							if(OB){
								$smarty->assign('sysInfoMessage',"Ultrasound Request Service successfully updated.");
							}
							else{
								$smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");
							}
						}

						#added by VAN 06-02-2011
							if ($_POST['for_manual_payment'])
								$saveok=$srvTempObj->save_ManualPayment($_POST['refno'], $manual_data);
							else{
								$saveok=$srvTempObj->ManualPayment($_POST['refno'], $manual_data);
								$saveok=$srvTempObj->resetRequestFlag($_POST['refno'], $manual_data, 'care_test_request_radio');
							}

						$postToEmr = true;
						$updateClinicInfo = true;
						// $smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");
// }
					}else{
						$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
					}
				}

				$data = array(
			        "request"=>$radio_request,
			        "priority"=>$_POST['priority'],
			        "totalAmount"=>$_POST['netTotal'],
			        "discount"=>$_POST['discountTotal'],
			        "transaction"=>$transType,
			        "reqDate"=>$reqDate,
			        "refno"=>$refno
		        );

                if(in_array("CT",$ctMri)){
                    if($refno)
                    	for($i=0;$i<count($radGrpCT);$i++){
                    		$grp = $radGrpCT[$i];
                    		$CtCliHis = $radio_obj->updateCTClinicalHistory($pid,$uuidCt,&$data,$grp);
                    	}
                }
                
                if(in_array("MRI",$ctMri)){
                    if($refno)
                    	for($i=0;$i<count($radGrpMRI);$i++){
                    		$grp = $radGrpMRI[$i];
                    		$MriCliHis = $radio_obj->updateMRIClinicalHistory($pid,$uuidMri,&$data,$grp);
                    	}
                }
                
                if($postToEmr){
                	//for delete notification
                	$personnel = $personell_obj->get_Person_name2($_SESSION['sess_login_personell_nr']);
                	if(!empty($radio_obj->deleted_items)){

						#added rnel
						$notifData = array();
						
						$notifData = array(
							'pname' => $radio_obj->data_array["ordername"],
							'items' => $radio_obj->deleted_items,
							'personnel' => $personnel['name_first'] . ' ' . $personnel['name_last']
						);

						#publish data for rad single deletion
						$radio_obj->notifRadMessage($notifData);
						#end rnel

                	}
                    try {
                        require_once($root_path . 'include/care_api_classes/emr/services/RadiologyEmrService.php');
                        $radService = new RadiologyEmrService();
                        #add new argument to detect if to update patient demographic or not
                        $radService->saveRadRequest($_POST['refno'], 1);
                    } catch (Exception $exc) {
                        // echo $exc->getTraceAsString();die;
                    }

                }

                if($updateClinicInfo) {
                	$data = array(
                		'encounter_nr' => $_POST['encounter_nr'],
                		'clinicalInfo' => $_POST['clinicInfo'][count($_POST['clinicInfo']) - 1],
                		'location' => 'RD'
                	);

if ($is_doctor) {
                    $enc_obj->updateClinicalImpression($data);
                    $enc_obj->saveToClinicalImpressionTable($data);
}
                }
				break;
		case 'cancel':
				if($radio_obj->deleteRefNo($_POST['refno'])){
					header('Location: '.$breakfile);
					exit;
				}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
				break;
	}# end of switch stmt
	#echo "sql = ". $radio_obj->sql;

	if (!isset($refno) || !$refno){
		if (isset($_GET['ref']) && $_GET['ref']){
			$refno = $_GET['ref'];
		}
		if (isset($_POST['refno']) && $_POST['refno']){
			$refno = $_POST['refno'];
		}

		if (empty($refno)){
			$refno = $_GET['prevrefno'];
			$prevrefno = $refno;
		}
	}

	# added by VAN 01-15-08
    
	if (($prevrefno)&&($prevbatchnr)){
		$refInfo = $radio_obj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);

		if ($refInfo['parent_refno'])
			//$refno = $refInfo['parent_refno'];
			$refno = $refInfo['refno'];
	}

	$mode='save';   # default mode
	if ($refNoBasicInfo = $radio_obj->getBasicRadioServiceInfo($refno)){
		$mode='update';
		extract($refNoBasicInfo);
		#$rid = $radio_obj->RIDExists($pid);
		$request_date = formatDate2Local($request_date,$date_format);
	}
	#echo $radio_obj->sql;
	 #$view_from = '';
	 if ($_GET['view_from'])
			$view_from = $_GET['view_from'];

	 if ($view_from=='ssview'){
		 if ($_GET['discountid']){
			 $discountid = $_GET['discountid'];
			 $infoSS = $objSS->getSSClassInfo($discountid);

//			 if ($infoSS['parentid'])
//					$discountid = $infoSS['parentid'];
//			 else
					$discountid = $discountid;

			 $discount = $infoSS['discount'];
		 }

	 }else
			$view_from = '';
 #echo $radio_obj->sql;

 # Title in the title bar
 $title=($_GET['ob']=='OB'? "OB-GYN Ultrasound": $LDRadiology);

 $smarty->assign('sToolbarTitle',"$title :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");


 # Window bar title
 $smarty->assign('sWindowTitle',"$title :: New Test Request");

 # Assign Body Onload javascript code
 $onLoadJS='onLoad="preset();checkCash();loadCoverage();"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 if ($popUp){
	 $smarty->assign('bHideTitleBar',TRUE);
	 $smarty->assign('bHideCopyright',TRUE);
 }
 # Collect javascript code

 ob_start();
 # Load the javascript code
 #$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
 $xajax->printJavascript($root_path.'classes/xajax_0.5');
?>

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcuts.js"></script>

<!-- added by Macoy June 24, 2014  -->
<!-- /////////////////////////////////// -->
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript">
    var $J = jQuery.noConflict();
</script>
<!-- /////////////////////////////////// -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/radio-request-new.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	shortcut("F2",
		function(){
			var is_cash = $('is_cash').value;
			var pid = $('pid').value;
			var refno = $('refno').value;
			// alert(is_cash+", "+pid+", "+refno);
			viewPatientRequest(is_cash,pid,refno)
		}
	);

	function checkCash(){
		// console.log($('refno').value);
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
		}else{
			document.getElementById('is_cash').value = 0;
		}
	}

	function saveDiscounts2(){
		 var discountgiven = Math.round($('show-discount').value);

		 if ($F('view_from')=='override'){
			 if (parseFloat(discountgiven) <= 0){
				 alert('Please input a decent discount.');
			 }else{
				 if ($F('view_from')=='override'){
						if(confirm("Grant this request?")){
							usr=prompt("Please enter your username.","");
							if(usr&&usr!=""){
								pw=prompt("Please enter your password.","");
								if(pw&&pw!=""){
									xajax_updateRequest(usr, pw, $F('refno'), $F('show-discount'));
								}
							}
						}
				 }else{
						submitform();
				 }
			 }
		 }else if ($F('view_from')=='ssview'){
				submitform();
		 }
	}

	function submitform(){
		inputform.submit();
	}

	// updated by carriane 10/24/17; added IPBM encounter types
	function payTypeDisable(is_served) {
		var IPBMOPD_enc = "<?=IPBMOPD_enc?>";
		var IPBMIPD_enc = "<?=IPBMIPD_enc?>";
		
		if(is_served == 1) {
			$('iscash1').disabled=true;
			$('iscash0').disabled=true;
			$('grant_type').disabled=true;
		} else {
			$('iscash1').disabled=false;

			if($('ptype').value != IPBMOPD_enc && $('ptype').value != IPBMIPD_enc){
			$('iscash0').disabled=false;
			}

			$('grant_type').disabled=false;
		}

		if($('for_manual').checked) {
			$('iscash1').disabled=true;
			$('iscash0').disabled=true;
			$('grant_type').disabled=true;
		}
		
	}

	function NewRequest(){
		urlholder="seg-radio-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}

	function loadCoverage(){
		updateCoverage([$('encounter_nr').value]);
	}

-->
</script>

<?php
    
    
    
        
	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

	#$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	$smarty->assign('sRID','<input class="segInput" id="rid" name="rid" type="text" size="15" value="'.$rid.'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');

	$var_arr = array(
		"var_rid"=>"rid",
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_discountid"=>"discountid",
		"var_orig_discountid"=>"orig_discountid",
		"var_discount"=>"discount",
		"var_name"=>"ordername",
		"var_addr"=>"orderaddress",
		"var_clear"=>"clear-enc",
		"var_history"=>"btnHistory",
		"var_ctscan"=>"btnCTScan",
		"var_mri"=>"btnMRI",
		"var_area"=>"area",
		"var_ward_nr"=>"ward_nr",
		"var_include_walkin"=>"1",
		"var_reg_walkin"=>"1",
		"var_from"=>"radio"
	);

	$vas = array();
	foreach($var_arr as $i=>$v) {
		$vars[] = "$i=$v";
	}
	$var_qry = implode("&",$vars);

	 if ($area){
			$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
	 }else{
		 $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
			 onclick="if (warnClear()) {  clearEncounter(); emptyTray(); overlib(
				OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc='+($('iscash1').checked?'0':'1'),".
				'700, 400, \'fSelEnc\', 0, \'auto\'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
				CAPTIONPADDING,2,
				CAPTION,\'Select registered person\',
				MIDX,0, MIDY,0,
				STATUS,\'Select registered person\'); } return false;"
			 onmouseout="nd();" />');
	}

	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');

	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');

	$infoSS2 = $objSS->getSSClassInfo($discountid);

	if (($infoSS2['parentid'])&&($infoSS2['parentid']=='D'))
		$discountid2 = $infoSS2['parentid'];
	else
		$discountid2 = $discountid;

	$smarty->assign('sClassification',(($discountid2) ? $discountid2 : 'None'));
	$smarty->assign('sPatientType',(($enctype) ? mb_strtoupper($enctype): 'None'));

	$smarty->assign('sPatientLoc',(($location) ? mb_strtoupper($location) : 'None'));
	$smarty->assign('sPatientMedicoLegal',(($is_medico) ? "YES" : 'NO'));

	if ($_POST["ref"]!=NULL)
		$Ref = $_POST["ref"];
	elseif ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	elseif (!$repeat)
			$Ref = $refno;
	else
		$Ref = $refno;


	if (($repeat)&&(empty($refInfo['parent_refno']))){
		$Ref = "";
		$Ref2 = "";
	}else{
		if ($is_cash==0){
			$Ref = $refno;
			$Ref2 = $refno;
		}else{
			$sql_hasPaid = "SELECT SUM(CASE WHEN(request_flag IS NOT NULL ) THEN 1 ELSE 0 END) AS withpaid
										FROM care_test_request_radio WHERE refno='$refno'";
			$rspaid = $db->Execute($sql_hasPaid);
			$rowpaid = $rspaid->FetchRow();
			extract($rowpaid);

			if ($withpaid){
				#$hasPaid = $withpaid;
				$hasPaid = 1;
				$Ref2 = $refno;
			}
		}
	}

	#commented by VAN 10-08-2014
	#PACS
	#pacs generating the hl7
	/*if ($_POST["items"]!=NULL){
		$arraySampleItems = array();
		$arrayPACSItems = array();
		$arrayItemsList = array();
		$with_PACS = 0;

		foreach ($_POST["items"] as $i=>$v) {
			$PACSid = 'inPACS'.$_POST["items"][$i];
            $PACSid = str_replace(".","_",$PACSid);

            $pacs_code = 'pacs_code'.$_POST["items"][$i];
            $pacs_code = str_replace(".","_",$pacs_code);

            $nameitems = 'nameitems'.$_POST["items"][$i];
            $nameitems = str_replace(".","_",$nameitems);

            #if paid or charge
            if (($is_cash==0) || ($hasPaid==1)){
            	$arraySampleItems[] = $_POST["items"][$i];
            	#echo "if paid = ".$is_cash." == ".$hasPaid;
            	if($_POST[$PACSid]){
            		$service_code = $_POST[$pacs_code];

            		$arrayPACSItems[] = array($pacs_code, $_POST[$nameitems]," ");
					$with_PACS =+ 1;
            	}	

            	$_POST['arrayPACSItems'] = $arrayPACSItems;
		 		$_POST['with_PACS'] = $with_lis;

		 		$_POST['arrayItemsList'] = $arrayItemsList;

		 	}
		}	
	}

	#added by VAN 06-15-2014
    # for HL7 compliant
    #if successfully saved or updated
    
    if ($success){
		$fileObj = new seg_create_HL7_file($details);  

		if (count($arrayPACSItems)){
			#get the existing msg control id if there is any
	        $hl7msg_row = $radio_obj->isforReplaceHL7Msg($refno,'XO'); 
	        #echo $radio_obj."<br>";
	        if ($hl7msg_row['msg_control_id']){
	            $msg_control_id = $hl7msg_row['msg_control_id'];
	            $forreplace = 1;   
	        }else
	            $msg_control_id = $radio_obj->getLastMsgControlID();
	        
	        #echo "he = ".$msg_control_id." == ".$forreplace;    
	        $details->msg_control_id_db = $msg_control_id;
	        $details->msg_control_id = $prefix.$msg_control_id;
	        
	        # Observation order - event O01
	        $msg_type = "ORM";
	        $event_id = "O01";
	        $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
	        $details->msg_type = $hl7_msg_type;

	        #pid
        	$details->POH_PAT_ID = trim($pid);
        	$details->POH_PAT_ALTID = "";
            #$details->patient_name = mb_strtoupper(trim($patient['name_first'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_last']));
            #$details->POH_MIDDLENAME = mb_strtoupper(trim($patient['name_middle']));
            $details->patient_name = mb_strtoupper(trim($patient['name_last'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_first'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_middle']));
            $details->POH_MIDDLENAME = "";
        	$details->POH_PAT_DOB = date("YmdHis",strtotime($patient['date_birth']));
        	$details->POH_PAT_SEX = trim(strtoupper($patient['sex']));
            
	        $details->address = trim($street_name).$COMPONENT_SEPARATOR.trim($brgy_name).$COMPONENT_SEPARATOR.trim($mun_name).$COMPONENT_SEPARATOR.trim($prov_name).$COMPONENT_SEPARATOR.trim($zipcode);
	        #$details->POH_CIVIL_STAT = trim(strtoupper($patient['civil_status']));
	        $details->POH_CIVIL_STAT = substr(trim(strtoupper($patient['civil_status'])), 0, 1);
	        
	        #pv1
	        #$details->setID = "1";
	        $details->setID = "";
	        $details->POH_PAT_TYPE = mb_strtoupper($patient_type);
	        #ward, room and bed

	        $details->location = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
	        $details->requesting_doc =  $_POST['requestDoc'][0].$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($_POST['requestDocName'][0]));

	        $details->POH_PAT_CASENO = trim($encounter_nr);

	        if ($mode=='save'){
	            $order_control = "NW";
	        }elseif ($mode=='update'){
	            if ($with_PACS){
		            $existhl7msg_row = $radio_obj->isExistHL7Msg($refno); 
	                if ($existhl7msg_row['msg_control_id']){
	                    $filecontent = $existhl7msg_row['hl7_msg'];
	                    if (stristr($filecontent, 'ORC|NW|')){
	                        $order_control = "XO";
	                    }elseif (stristr($filecontent, 'ORC|CA|')){
	                        $order_control = "NW";
	                    }else
	                        $order_control = "XO";    
	                
	                }else    
		                $order_control = "NW";
              	}else       
	                $order_control = "CA";
	        }elseif ($mode=='cancel'){
	            $order_control = "CA";
	        }

	        $details->order_control = $order_control;

	        #obr
	        #retain the reference no.
	        $details->POH_ORDER_NO = $refno;
	        #order items
	        $result = $radio_obj->getRequestDetailsbyRefnoPACS($refno);
	        #echo $radio_obj->sql;
	        $count = $radio_obj->FoundRows();

	        while($row_test=$result->FetchRow()){
	        	$service_code = trim($row_test['pacs_code']);

	        	$service .= $service_code.$COMPONENT_SEPARATOR.trim($row_test['name']).$REPETITION_SEPARATOR;
	        }

	        $service = trim($service);
	        $service_list = substr($service,0,strlen($service)-1);
	        $details->service_list = trim($service_list);

	        #S:stat request ; R:routine request
	        $details->POH_PRIORITY2 = (trim($priority)?'S':'R');
	        $details->POH_TRX_DT =  date("YmdHis");
	        $details->POH_CLI_INFO = addslashes(mb_strtoupper(trim($_POST['clinicInfo'][0])));
	        $details->doctor = trim($_POST['requestDoc'][0]).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper(trim($_POST['requestDocName'][0])));
	        
	        if ($patient['encounter_type']==2)
	           $location1 = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";
	        elseif($patient['encounter_type']==1)
	           $location1 = "ER".$COMPONENT_SEPARATOR."ER"; 
	        elseif (($patient['encounter_type']==3) || ($patient['encounter_type']==4))
	           $location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
	        else
	           $location1 = "WN".$COMPONENT_SEPARATOR."WALKIN";
	        
	        $details->location_dept = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
	         
	        #remarks or comments 
	        $details->note = addslashes((trim($_POST['comments'])));

	        $msh_segment = $HL7Obj->createSegmentMSH($details);
	        $pid_segment = $HL7Obj->createSegmentPID($details);
	        $pv1_segment = $HL7Obj->createSegmentPV1($details);
	        $orc_segment = $HL7Obj->createSegmentORC($details);
	        $obr_segment = $HL7Obj->createSegmentOBR($details);
	        $nte_segment = $HL7Obj->createSegmentNTE($details);
	         
	        $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment."\n".$orc_segment."\n".$obr_segment;

	        if ($details->note)
				$filecontent = $filecontent."\n".$nte_segment;

	        $file = $details->msg_control_id;
	        
	        #create a file
	        #$filename_local = $fileObj->create_file_to_local($file);
	                                        
	        #Thru file sharing
	        #write a file to a local directory
	        #$fileObj->write_file($filename_local, $filecontent); 	

	        switch ($transfer_method){
	            #FTP (File Transfer Protocol) approach
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
	                             $obj = $transportObj->sendHL7MsgtoSocket($filecontent,'pacs');
	                             
	                             #return/print result
	                             $text = "RIS Server said:: ".$obj;
	                             #$text = "connected...";
	                        #}else{
	                        #     $text = "Unable to connect to PACS Server. Error: ".$transportObj->error."...";   
	                        #}
	                        
	                        echo $text;
	                        break;                    
	        }

	        #update msg control id
	        $details->msg_control_id = $details->msg_control_id_db;
	        
	        #if new message control id, update the tracker
	        if (!$forreplace)
	            $hl7_ok = $radio_obj->updateHL7_msg_control_id($details->msg_control_id);
	            
	        #HL7 tracker
	        $radio_obj->getInfo_HL7_tracker($details->msg_control_id);
	        $with_rec = $radio_obj->count;
	                                        
	        $details->pacs_order_no = $refno;
	        $details->msg_type = $msg_type;
	        $details->event_id = $event_id;
	        $details->refno = $refno;
	        $details->pid = $pid;
	        $details->encounter_nr = $encounter_nr;
	        $details->hl7_msg =  $filecontent;

	        if ($with_rec){
	            $hl7_ok = $radio_obj->updateInfo_HL7_tracker($details);
	        }else{
	            $hl7_ok = $radio_obj->addInfo_HL7_tracker($details);
	        }


		}	
		#echo $details->hl7_msg;
		#exit();
	}*/	
    #===============================
	
	if (($is_cash==0) && ($hasPaid==1))
		$hasPaid = 0;

	$smarty->assign('sRefNo','<input class="segInput" name="refno2" id="refno2" type="text" size="10" value="'.$Ref2.'" readonly style="font:bold 12px Arial"/><input name="refno" id="refno" type="hidden" size="10" value="'.$Ref.'"/>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";

	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);

	if (($repeat)||(empty($request_date)))
		$curDate = date($dbtime_format);
	elseif (($request_date!='0000-00-00')||(!empty($request_date))) {
		$requestDate = $request_date." ".$request_time;
		$submitted = 1;
		$_POST['orderdate'] = $requestDate;
	}

	$jsCalScript = "
			<script type=\"text/javascript\">
				Calendar.setup ({
					displayArea : \"show_orderdate\",
					inputField : \"orderdate\",
					ifFormat : \"%Y-%m-%d %H:%M\",
					daFormat : \"	%B %e, %Y %I:%M%P\",
					showsTime : true,
					button : \"orderdate_trigger\",
					singleClick : true,
					step : 1
				});
			</script>";

	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">'.$jsCalScript);

	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" onClick="checkPriority();" value="0"'.($is_urgent? "": " checked").'>Routine');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" onClick="checkPriority();" value="1"'.($is_urgent? " checked": "").'>STAT');

	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');

	// updated by carriane 10/24/17; added IPBMdisable
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	// end carriane's update
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" wrap="physical"  cols="30" rows="10" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"10\">Request list is currently empty...</td>
				</tr>");

	if (!$ischecklist){
		 $filename = 'radiology/seg-radio-service-tray.php';
	}else{
		 $filename = 'radiology/seg-radio-service-checklist.php';
		 
	}
	if(!OB){
		$rd_caption = "radiological";
	}

	// updated by carriane 10/24/17; added IPBMextend
	$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\''.$root_path.'modules/'.$filename.'?area='.$area.'&is_dr='.$is_dr.'&dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'&pid='.$pid.'&encounter_nr='.$encounter_nr.$IPBMextend.'&ob='.$_GET['ob'].'\', 600, 390, \'fOrderTray\', 1, \'auto\'),
					WIDTH,390, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4,
					CAPTION,\'Add '.$rd_caption.' service item from request tray\',
					MIDX,0, MIDY,0,
					STATUS,\'Add '.$rd_caption.' service item from request tray\');"
			onmouseout="nd();">');
	// end carriane's update

	$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');

	$smarty->assign('sFree','<input type="checkbox" name="is_free" id="is_free" value="1" onClick="setDiscount();" />');
	$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="text" onBlur="computeDiscount(this.value);formatDiscount(this.value);" onFocus="clearValue();" style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" onkeydown="return key_check(event, this.value)" value="'.number_format($adjusted_amount,2).'"/>');

	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" onclick="saveDiscounts2();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');

	$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');

	#added by VAN 05-11-2010
	if (($admission_dt)&&(($admission_dt!='0000-00-00 00:00:00')||(empty($admission_dt))))
		$admission_dt = date("m/d/Y h:i A",strtotime($admission_dt));
	else
		$admission_dt = "";

	if (($discharge_date)&&(($discharge_date!='0000-00-00')||(empty($discharge_date))))
		$discharge_date = date("m/d/Y h:i A",strtotime($discharge_date));
	else
		$discharge_date = "";

	#$smarty->assign('sRDU','<input type="checkbox" '.(($is_rdu==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" />');
	$smarty->assign('sAdmissionDate',$admission_dt);
	$smarty->assign('sDischargedDate',$discharge_date);
	$smarty->assign('sPatientHRN',$pid);
	$smarty->assign('sAdmDiagnosis',mb_strtoupper($impression));

	$smarty->assign('sPatientAge',$age);
	$smarty->assign('sPatientSex',$gender);
	$smarty->assign('sPatientBdate',$dob);

	// updated by carriane 10/24/17; added IPBMdisable
	$smarty->assign('sRDU','<input type="checkbox" '.(($is_rdu==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" onChange="enablePhic();" '.$IPBMdisable.'/>');
	$smarty->assign('sWalkin','<input type="checkbox" '.(($is_walkin==1)?'checked="checked" ':'').' name="is_walkin" id="is_walkin" onchange="checkIfWalkin()" value="1" '.$IPBMdisable.'/>');
	// end carriane's update

	$smarty->assign('sPE','<input type="checkbox" '.(($is_pe==1)?'checked="checked" ':'').' name="is_pe" id="is_pe" onchange="" '.(($is_personnel)?'':'disabled="disabled" ').' value="1" />');
	if($_GET['ob']=='OB'){
		$smarty->assign('sHistoryButton','<img type="image" name="btnHistory" id="btnHistory" src="'.$root_path.'images/btn_history.gif" border="0" style="cursor:pointer;" onclick="viewOBHistory($(\'pid\').value,$(\'encounter_nr\').value);">');
	}else{
		$smarty->assign('sHistoryButton','<img type="image" name="btnHistory" id="btnHistory" src="'.$root_path.'images/btn_history.gif" border="0" style="cursor:pointer;" onclick="viewHistory($(\'pid\').value,$(\'encounter_nr\').value);">');
	}

	#$smarty->assign('sOtherButton','<img type="image" name="btnOther" id="btnOther" src="'.$root_path.'images/btn_add_other.gif" border="0" style="cursor:pointer;" onclick="addOtherCharges($(\'pid\').value,$(\'encounter_nr\').value,$(\'ward_nr\').value);">');

	if ($is_pay_full)
		$onchecked = "checked";
	else
		$onchecked = "";

	$smarty->assign('sPayFull','<input type="checkbox" name="ispayfull" id="ispayfull" value="1" '.$onchecked.' onchange="checkIfFull()" /><b>Pay Full?</b>');




	$charge_type .= $glob_obj->getChargeType();
    
		// $charge_type .= ",'sdnph'";
	

	// if($_GET['ob']){
	// 		$charge_type .=",'phic'";
	// }



		#added by VAN 07-16-2010 TEMPORARILY
	$result = $enc_obj->getChargeType("WHERE id NOT IN (".$charge_type.") AND ordering IS NOT NULL","ordering");
	#$result = $enc_obj->getChargeType("WHERE id NOT IN ('paid','phs','charity')","ordering");
	$options="";
	$grant_type = $grant_type;
	#if (empty($type_charge) || ($type_charge==0))
	if (!($grant_type)){
		$grant_type = '';
		$disabled = "";
	}else{
		if ($is_cash==1)
		$disabled = "disabled";
		else
			$disabled = "";
	}

	$glob_obj->getConfig('mainlab_only_chargetypes');

	$labOnlyTypes = explode(",", $GLOBAL_CONFIG['mainlab_only_chargetypes']);

	$options = "<option value=''>PERSONAL</option>";
	while ($row=$result->FetchRow()) {
		if ($grant_type==$row['id']){
			$checked = "selected";
			$smarty->assign('hasSaveGrantType', '<input type="hidden" name="hasSaveGrantType" id="hasSaveGrantType" value="1" />');
		}else
			$checked = "";

		if(!in_array($row['id'], $labOnlyTypes))
			$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
	}

	# Edited by James 2/24/2014

	/*
	** Added by James 2/24/2014
	** Purpose: To hide grant_type in Charge for IC Transactions
	*/
	/*if($encounter_type==6)
	{
		if(!$is_cash)
		{
			$smarty->assign('sChargeTyp',
	                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" hidden 
	                                     onchange=\" if (warnClear()) { emptyTray(); checkCharge(this.value); changeChargeType(); return true;} else {return false;}\">
	                                         $options
									 </select>");
		}
		else
		{
		    $smarty->assign('sChargeTyp',
		                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled."
		                                     onchange=\" if (warnClear()) { emptyTray(); checkCharge(this.value); changeChargeType(); return true;} else {return false;}\">
		                                         $options
										 </select>");
		}
	}
	else
	{
	    $smarty->assign('sChargeTyp',
	                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled." 
	                                     onchange=\" if (warnClear()) { emptyTray(); checkCharge(this.value); changeChargeType(); return true;} else {return false;}\">
	                                         $options
									 </select>");
	}*/
	#commented by art 07/10/2014
    /*$smarty->assign('sChargeTyp', "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled."
                                         onchange=\" if (warnClear()) { emptyTray(); checkCharge(this.value); changeChargeType(); return true;} else {return false;}\">
                                             $options
                                     </select>");*/

	#added by art 07/10/2014
	$smarty->assign('sChargeTyp', "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled."
                                         onchange=\" if (warnClear()) { emptyTray();changeChargeType(); return true;} else {return false;}\">
                                             $options
                                     </select>");
	#end art

	#------------end TEMPORARILY -------

	if (($parent_refno)&&($parent_batch_nr)){
		$repeat=1;
	}

	#commented by VAN 05-26-2011
	/*if (empty($parent_refno))
		$parent_refno = $refno;
	else*/
	if ($prevrefno)
		$parent_refno = $prevrefno;

	#echo "batch = ".$prevbatchnr;

	if ((empty($parent_batch_nr))||($prevbatchnr))
		$parent_batch_nr = $prevbatchnr;

	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.((OB==OBDept)?'  style="display:none"':'') . (($repeat=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sParentBatchNr','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/><input id="parent_batch_nr" name="parent_batch_nr" type="hidden" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($remarks).'</textarea>');
	$smarty->assign('sHead','<input class="segInput" id="approved_by_head" name="approved_by_head" type="text" size="40" value="'.$approved_by_head.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');

	#added by VAN 08-23-2010
	#FOR Industrial Clinic Info
	$smarty->assign('sChargeToComp','<input type="checkbox" name="is_charge2comp" id="is_charge2comp" value="1" '.(($is_charge2comp=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sCompanyName',$compName);
	$smarty->assign('sCompanyID','<input class="segInput" id="compID" name="compID" type="hidden" size="10" value="'.$compID.'" style="font:bold 12px Arial;" readonly/>');


	$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'&ob='.($_GET['ob']=='OB'?"OB":"").'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
	$smarty->assign('sFormEnd','</form>');

	#added by VAN 06-02-2011

	$sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='RD' AND refno='".$refno."' AND is_deleted=0";
	$res_manual=$db->Execute($sql_manual);
	$row_manual_count=$res_manual->RecordCount();
	$row_manual = $res_manual->FetchRow();

	// added by: syboy 12/13/2015 : meow
	if (($allow_radioManualPay&&!($_GET['ob']=='OB'))||($allow_ob_manual_pay&&$_GET['ob']=='OB')) {
		$is_allow = '';
	}else{
		$is_allow = 'disabled';
	}
	// ended
	$smarty->assign('sManualCheck','<input type="checkbox" '.$is_allow.' name="for_manual" id="for_manual" value="1" '.(($row_manual_count)?'checked="checked" ':'').' onClick="setManualPayment();" />
																	<input type="hidden" name="for_manual_payment" id="for_manual_payment" value="">');
	$smarty->assign('sManualNumber','<input class="segInput" '.$is_allow.' id="manual_control_no" name="manual_control_no" type="text" size="40" value="'.$row_manual['control_no'].'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sManualApprovedby','<input class="segInput" '.$is_allow.' id="manual_approved" name="manual_approved" type="text" size="40" value="'.$row_manual['approved_by'].'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sManualReason','<textarea class="segInput" '.$is_allow.' id="manual_reason" name="manual_reason" cols="37" rows="2" style="font:bold 12px Arial">'.$row_manual['reason'].'</textarea>');
	$smarty->assign('sManualTypeSelection','<input type="radio" class="jedInput" name="for_manual_type" '.$is_allow.' id="for_manual_type1" value="paid" '.(($row_manual['type']=='paid')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>Cash</strong>&nbsp;
																					<input type="radio" '.$is_allow.' class="jedInput" name="for_manual_type" id="for_manual_type2" value="lingap" '.(($row_manual['type']=='lingap')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>Lingap</strong>&nbsp;
																					<input type="radio" '.$is_allow.' class="jedInput" name="for_manual_type" id="for_manual_type3" value="cmap" '.(($row_manual['type']=='cmap')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>MAP</strong>&nbsp;
																					<input type="radio" '.$is_allow.' class="jedInput" name="for_manual_type" id="for_manual_type4" value="phic" '.(($row_manual['type']=='phic')?'checked="checked" ':'').' disabled onClick="setLabel()"/>&nbsp;<strong>PHIC</strong>&nbsp;');

	#--------------

    #added by VAS 03/21/2012
    $phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$encounter_nr."') AS `phic_nr`");
    $smarty->assign('sPhicNo', $phic_nr);
    $smarty->assign('sBtnCoverage','<img type="image" name="btn-coverage" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" style="cursor:pointer;" onclick="return openCoverages();">');

     #added by pol
    if($encounter_nr){
        $sql_mc = "SELECT m.memcategory_desc
                        FROM seg_encounter_memcategory `e`
                        INNER JOIN seg_memcategory `m`
                        ON e.memcategory_id=m.memcategory_id
                        WHERE e.encounter_nr=".$db->qstr($encounter_nr);
        $category = $db->GetOne($sql_mc);
        
        if($category){                        
            $CategoryUi = $category;
        }else{
            $CategoryUi = 'None';    
            }
    }else{
            
            }     
    $smarty->assign('sMemCategory', $CategoryUi);
   #end pol
    
   //$smarty->assign('sMemCategory', 'try');
 ?>
<?php
ob_start();
$sTemp='';

if ($repeat){
	if ($refInfo['parent_batch_nr'])
		$batchnr = $refInfo['batch_nr'];
	else
		$batchnr = $prevbatchnr;
}else
	$batchnr = 0;

?>

	<script type="text/javascript" language="javascript">


		preset(<?= ($is_cash=='0')? "0":"1"?>);
		var user_origin = $('user_origin').value; // added by Christian 12-31-19
		var refno = '<?=$refno?>';
		var view_from = '<?=$view_from?>';
		var batchnr = '<?=$batchnr?>';
		var fromSS = 0;
		var discount = $('discount').value;
		var discountid = $('discountid').value;

		if (view_from=='ssview')
			fromSS = 1;

		// added by Christian 12-31-19
		if (user_origin == 'radio')
			var ref_source = 'RD';
		// end Christian 12-31-19

		if (refno){
			xajax_populateRequestListByRefNo(refno, batchnr, fromSS, discount, discountid, ref_source); //Updated by Christian 12-31-19

            changeChargeType();
        }    

	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}

// $source_req = $radio_obj->getSourceReq($refno);

if (((($hasPaid)|| (!$is_cash))&&($mode=='update'))||($mode=='update')||($repeat)){
#if (((($hasPaid)|| (!$is_cash))&&($mode=='update'))||($repeat)){
	if($_GET['ob']!='OB'){
		$smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\',\''.$source_req.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');
		$withclaimstub = 1;

	}
		
}

ob_start();
$sTemp='';

?>
	<input type="hidden" name="radio_manual" id="radio_manual" value="<?php echo $allow_radioManualPay?>"> <!-- added by: syboy 12/13/2015 : meow -->
	<input type="hidden" name="submitted" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">

	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" id="pid" name="pid" value="<?php if (trim($info["pid"])) echo $info["pid"]; else echo $pid;?>">
	<input type="hidden" name="discount2" id="discount2" value="<?=$discount2?>" >
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >

	<input type="hidden" id="gender" name="gender" value="<?=$sex;?>">
	<input type="hidden" id="date_birth" name="date_birth" value="<?=$date_birth;?>">

	<input type="hidden" id="orig_discountid" name="orig_discountid" value="<?=$orig_discountid?>">
	<input type="hidden" id="discountid" name="discountid" value="<?=$discountid;?>">
<!--added by celsy 08/24/10	-->
	 <input type="hidden" id="ptype2" name="ptype2" value="<?=$ptype;?>">

     <input type="hidden" id="netTotal" name="netTotal" value="">
     <input type="hidden" id="discountTotal" name="discountTotal" value="">
     
	<?php
		if (empty($Ref))
			$mode='save';
		else
			$mode='update';

		if ($_GET['view_from'])
			$view_from = $_GET['view_from'];
		elseif ($_POST['view_from'])
			$view_from = $_POST['view_from'];

		if (($encounter_type==3)||($encounter_type==4)){
			if ($loc_code){
				$ward_sql = "SELECT * FROM care_ward AS w WHERE w.nr='".$loc_code."'";
				$ward_info = $db->GetRow($ward_sql);
				if ($ward_info['accomodation_type']==1)
					#CHARITY
					$area_type = 'ch';
				elseif ($ward_info['accomodation_type']==2)
					#PAYWARD
					$area_type = 'pw';
			}
		}
	?>

	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">

	<input type="hidden" name="isrepeat" id="isrepeat" value="<?= $repeat?$repeat:'0'?>">

	<input type="hidden" name="area" id="area" value="<?=$area?>" />
	<input type="hidden" name="ptype" id="ptype" value="<?=$encounter_type?>" />

	<input type="hidden" id="ward_nr" name="ward_nr" value="" />
	<input type="hidden" name="area_type" id="area_type" value="<?=$area_type?>" />
	<input type="hidden" name="source" id="source" value="<?=$ptype?>">

	<input type="hidden" name="user_origin" id="user_origin" value="<?=$user_origin?>">

	<input type="hidden" name="current_att_dr_nr" id="current_att_dr_nr" value="<?=$current_att_dr_nr?>">
	<input type="hidden" name="current_dept_nr" id="current_dept_nr" value="<?=$current_dept_nr?>">

	<input type="hidden" name="impression" id="impression" value="<?=$impression?>">
	<input type="hidden" name="ischecklist" id="ischecklist" value="<?=$ischecklist?>">

	<input type="hidden" name="currenttime" id="currenttime" value="<?=date('H')?>">

	<input type="hidden" name="withclaimstub" id="withclaimstub" value="<?=$withclaimstub?>" />

	<input type="hidden" name="source_req" id="source_req" value="<?=(($repeat)||(empty($source_req)))?SegRequestSource::getSourceRadiology():$source_req?>">
	<input type="hidden" name="from_dept" id="from_dept" value="<?php echo $_GET['ob']?>">
	<input type="hidden" name="login_user" id="login_user" value="<?=$personell_nr?>">
	<input type="hidden" name="is_dr" id="is_dr" value="<?=$is_dr?>">

	<input type="hidden" name="num_ctscan" id="num_ctscan" value="<?=$num_ctscan?>">
	<input type="hidden" name="num_mri" id="num_mri" value="<?=$num_mri?>">

    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
    <input type="hidden" name="DiscountID" id="DiscountID" value="<?=$DiscountID?>">
    <input type="hidden" name="accomodation" id="accomodation" value="">
    <input type="hidden" name="cov" id="cov" value="">
    <input type="hidden" name="admission_accomodation" id="admission_accomodation" value="<?= $_GET['enc_accomodation']?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" name="btnCancel" id="btnCancel" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this request?\')) if (checkRequestForm()) document.inputform.submit()">');

//added by VAN 07-04-2011
$smarty->assign('sCTScanAddHistoryButton','<img type="image" name="btnCTScan" id="btnCTScan" src="'.$root_path.'images/btn-ctcanform.gif" border="0" style="cursor:pointer;" onclick="showCTScanForm($(\'pid\').value,$(\'encounter_nr\').value,$(\'refno\').value);"></a>');
$smarty->assign('sMRIAddHistoryButton','<img type="image" name="btnMRI" id="btnMRI" src="'.$root_path.'images/btn-mriform.gif" border="0" style="cursor:pointer;" onclick="showMRIForm($(\'pid\').value,$(\'encounter_nr\').value,$(\'refno\').value);"></a>');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','radiology/radio-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>