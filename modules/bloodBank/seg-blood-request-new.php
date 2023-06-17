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
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
	define('HSM', 'HOSPITAL SPONSORED MEMBER');
	define('NBB', 'SPONSORED MEMBER');
	define('BLOODBANK','B');
	define('INPATIENT','IN');
	define('ERPATIENT','ER');
	define('OPD','OP');

	$local_user='ck_lab_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	include_once $root_path . 'include/inc_ipbm_permissions.php'; // added by carriane 10/24/17

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	//$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;
	$breakfile=$root_path.'modules/bloodBank/bloodbank.php'.URL_APPEND;
	$thisfile=basename(__FILE__);

	if ($popUp!='1'){
			 # href for the close button
		 #$smarty->assign('breakfile',$breakfile);
	}else{
			# CLOSE button for pop-ups
			#$smarty->assign('breakfile','javascript:window.parent.close_overlib('.$_GET['from_or'].');');
			if (($_GET['view_from']=='ssview') || ($_GET['view_from']=='override'))
				$breakfile = "";
			else
				$breakfile  = "javascript:window.parent.cClick();";
	}

	$title="Blood Bank";

	# Create laboratory object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();

	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;

	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;

	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;

	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj = new Person;

	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

		#---added by CHA 08-28-09
		require_once($root_path.'include/care_api_classes/class_blood_bank.php');
		$bloodObj = new SegBloodBank();
		#---end CHA

	#added by VAN 06-03-2011
	require_once($root_path.'include/care_api_classes/class_workaround.php');
	$srvTempObj=new SegTempWorkaround();

	#added by VAS 06/23/2017
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

    require_once($root_path . 'include/care_api_classes/class_acl.php');
    $acl = new Acl($_SESSION['sess_temp_userid']);

    $canUncheckSample = $acl->checkPermissionRaw(array('_a_1_labwithsample')); #added by gervie 08//2015
    $canServeRequest = $acl->checkPermissionRaw(array('_a_1_blood_bank_served_request')); // add by carl Permision
    $details = (object) 'details';
       
    $prefix = BLOODBANK."HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";            

    $row_hosp = $objInfo->getAllHospitalInfo();
    
    # Establish db connection
    # Added by VAN 11-18-09
    $connection_type = $row_hosp['connection_type'];
    $HTTP_SESSION_VARS['connection_type'] = $connection_type;
    if ($connection_type=='odbc'){
        require_once($root_path.'include/inc_hclab_connection.php');
        #echo $dblink_hclab_ok;
        
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
    
    #if ($details->protocol_type=='tcp')
    #    $transfer_method = 'SOCKET';
    #else    
    #    $transfer_method = 'NFS';
        
    $transfer_method = $details->protocol_type;    
    
    #msh
    $details->system_name = trim($row_comp['system_id']);
    $details->hosp_id = trim($row_hosp['hosp_id']);
    $details->lis_name = trim($row_comp['lis_name']);
    $details->currenttime = strftime("%Y%m%d%H%M%S");
    }    
    
    #----------------------

	global $db, $allow_labrepeat, $allow_updateBloodData;

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
		if (!empty($_SESSION['sess_login_userid']))
		$seg_user_name = $_SESSION['sess_login_userid'];
	else
		$seg_user_name = $_SESSION['sess_temp_userid'];

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

	#if ($_GET['prevbatchnr'])
	#	$prevbatchnr = $_GET['prevbatchnr'];

	if ($_GET['prevrefno']){
		$prevrefno = $_GET['prevrefno'];
		$sql_prev = "SELECT encounter_nr FROM seg_lab_serv WHERE refno='".$prevrefno."'";
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

    //added by jasper 01/28/13
    if ($_GET['section'])
        $section = $_GET['section'];

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
		$patient = $person_obj->getAllInfoArrayforBB($pid,$encounter_nr);
		#echo "pid = ".$person_obj->sql;
	}

	$expiryInfo = $objSS->getExpiryInfo($pid);

	if ($patient['encounter_nr'])
		$encounter_nr = $patient['encounter_nr'];	

    #get encounter info
    $billinfo = $enc_obj->hasSavedBilling($encounter_nr);
    if ($billinfo){
        $bill_nr = $billinfo['bill_nr'];
        $hasfinal_bill = $billinfo['is_final'];
        $is_maygohome = $patient['is_maygohome'];
    }

    if ($refNoBasicInfo = $srvObj->getBasicLabServiceInfo($refno)){
		extract($refNoBasicInfo);

		$serv_dt = formatDate2Local($serv_dt,$date_format);

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
        }

    }

			if ($patient['name_middle']){
				$name_middle = mb_strtoupper(substr($patient['name_middle'],0,1));

				if ($name_middle)
					$name_middle = " ".$name_middle.".";
				else
					$name_middle = "";
			}
		$name_first_val = $patient['name_first'];
		$name_last_val = $patient['name_last'];

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

		if ($patient['date_birth']!='0000-00-00')
			$dob = date("Y-m-d",strtotime($patient['date_birth']));
		else
				$dob = "unknown";

		if ($patient['sex']=='f')
			$gender = "Female";
		elseif ($patient['sex']=='m')
			$gender = "Male";
		else
			$gender = "unknown";

		if ($patient['age'])
			$age = $patient['age'];
		else
			$age = "unknown";
	#}

	$current_att_dr_nr = $patient['current_att_dr_nr'];
	$current_dept_nr = $patient['current_dept_nr'];

	// updated by carriane 10/24/17; added IPBMOPD_enc and IPBMIPD_enc
	if (($patient['encounter_type']==2)||($patient['encounter_type']==IPBMOPD_enc)||($patient['encounter_type']==1))
		$impression = ($patient['er_opd_diagnosis'] != null) ? $patient['er_opd_diagnosis'] : $patient['chief_complaint'];
	elseif (($patient['encounter_type']==3)||($patient['encounter_type']==IPBMIPD_enc)||($patient['encounter_type']==4)|| $patient['encounter_type'] == 5)
		$impression = $patient['er_opd_diagnosis'];

	#added by VAN 03-09-2011
	if (!$impression) {
		$impression = '';

		$impression = $enc_obj->getLatestImpression($patient['pid'], $patient['encounter_nr']);

	}

	if ($repeaterror){
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}
	$location = $loc_name;
	$is_medico = $patient['is_medico'];
	$is_medico = $patient['is_medico'];

	$_POST['serv_tm'] = date('H:i:s',strtotime($_POST['orderdate']));

	$_POST['is_tpl'] = '0';

	if($_POST['grant_type'] == '' && !$_POST['iscash'])
		$_POST['is_tpl'] = '1';

	$_POST['fromBB'] = 1;

	#ref_source of blood bank
	#$_POST['grant_type'] = NULL;
	$_POST['ref_source'] = 'BB';

	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];

	$is_rdu = 0;

	require_once $root_path.'include/care_api_classes/class_request_source.php';

	switch ($ptype){
		case 'er' :
			$source_req = SegRequestSource::getSourceERClinics();
			break;
		case 'ipd' :
			// updated by carriane 10/24/17; added request resource if IPBM
			if($isIPBM)
				$source_req = SegRequestSource::getSourceIPBM();
			else
			$source_req = SegRequestSource::getSourceIPDClinics();
			break;
		case 'opd' :
			// updated by carriane 10/24/17; added request resource if IPBM
			if($isIPBM)
				$source_req = SegRequestSource::getSourceIPBM();
			else
			$source_req = SegRequestSource::getSourceOPDClinics();
			break;
		case 'phs' :
			$source_req = SegRequestSource::getSourcePHSClinics();
			break;
		case 'nursing' :
			$source_req = SegRequestSource::getSourceNursingWard();
			break;
		case 'lab' :
			$source_req = SegRequestSource::getSourceLaboratory();
			break;
		case 'spl' :
			$source_req = SegRequestSource::getSourceSpecialLab();
			break;
		case 'iclab' :
		case 'ic' :
			$source_req = SegRequestSource::getSourceIndustrialClinic();
			$sql_ic = "SELECT c.*, t.*
										FROM seg_industrial_transaction AS t
										LEFT JOIN seg_industrial_company AS c ON c.company_id=t.agency_id
										WHERE encounter_nr='".$encounter_nr."'";
			$rs_ic = $db->Execute($sql_ic);
			$row_ic = $rs_ic->FetchRow();
			$is_charge2comp = $row_ic['agency_charged'];
			$compID = $row_ic['agency_id'];
			$compName = $row_ic['name'];
			$discountid = "";
			$discount = 0;
			break;
		case 'or' :
			$source_req = SegRequestSource::getSourceOR();;
			break;
		case 'rdu' :
			$source_req = SegRequestSource::getSourceDialysis();;
			$is_rdu = 1;
			break;
		case 'doctor' :
			$source_req = SegRequestSource::getSourceDoctor();;
			break;
		default :
			$source_req = SegRequestSource::getSourceBloodBank();
			break;
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

		switch ($_POST['ptype']){
			case '1' :  $enctype = "ER PATIENT";
									$patient_type = "ER";
									$loc_code = "ER";
									$erLoc = $dept_obj->getERLocation($patient['er_location'], $patient['er_location_lobby']);
									$lobby = ($erLoc['lobby_name'] != null) ? " (" . $erLoc['lobby_name'] . ")" : "";
									if($erLoc['area_location'])
									$loc_name = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
									else
									$loc_name = "EMERGENCY ROOM";	
									break;
			case '2' :
									$enctype = "OUTPATIENT";
									$patient_type = "OP";
									$loc_code = $patient['current_dept_nr'];
									if ($loc_code)
										$dept = $dept_obj->getDeptAllInfo($loc_code);
									$loc_name = stripslashes($dept['name_formal']);
									break;
			case '3' :  $enctype = "INPATIENT (ER)";
									$patient_type = "IN";
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
									$loc_code = "RDU";
									$loc_name = "RDU";
									
									
									break;
			case '6' :
									$enctype = "INDUSTRIAL CLINIC";
									$patient_type = "IC";
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
			// end carriane 
			default :
									$enctype = "WALK-IN";
									$patient_type = "WN";  #Walk-in
									$loc_code = "WIN";
									$loc_name = "WIN";
									
									
									break;
		}

	$location = $loc_name;	

	#commented by VAS 06/23/2017
	#removed the auto serve function	
	#temporary, to be served the request
	/*if (!$_POST['is_cash']){
		$_POST['with_sample'] = 1;

		$status = 'done';
		$is_served = 1;
		$clerk = $_SESSION['sess_temp_userid'];
		$date_served = date("Y-m-d H:i:s");
	}else{
		$_POST['with_sample'] = 0;

		$status = 'pending';
		$is_served = 0;
		$clerk = "";
		$date_served = "0000-00-00 00:00:00";
	}*/

	//added by VAS 06/27/2017
	//fix the location of the patient
	if ($patient_type=='IN'){
		$room_loc = $patient['current_room_nr'];
		$bed = $enc_obj->getPatientLocation($encounter_nr, $loc_code);
		$bed_loc = $bed['location_nr'];
		$details->location = mb_strtoupper($loc_code).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name).$COMPONENT_SEPARATOR.mb_strtoupper($room_loc).$COMPONENT_SEPARATOR.mb_strtoupper($bed_loc);
		$details->location_dept = $details->location;
	}else{
		$details->location = mb_strtoupper($loc_code).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name).$COMPONENT_SEPARATOR.mb_strtoupper($room_loc).$COMPONENT_SEPARATOR.mb_strtoupper($bed_loc);
		$details->location_dept = $details->location;
	}

	if ($_POST["items"]!=NULL){
		 $arraySampleItems_h = array();
		 $arraySampleItems_d = array();
		 $hasrec = 0;
		 $islack = 0;
		 $iscomplete = 0;
		 $with_sample_rec = 0;

		 $arraySampleItems = array();
		 $arrayLISItems = array();
		 $with_sample = 0;
		 $with_LIS = 0;

		 foreach ($_POST["items"] as $i=>$v) {
				 #$arrayItemsList[] = array($status, $is_served, $date_served, $clerk, $date_served, $_POST["items"][$i]);

				 $qty_ordered = $_POST["qty"][$i];
				 $qty_received = 'rowSample'.$_POST["items"][$i];
				 #echo "<br><br><br> dd = ".$_POST[$qty_received]." == ".$_POST[$qty_ordered];
				 if ($_POST[$qty_received] == 0){
						$hasrec = 0;
						$islack =+ 1;
				 }elseif ($_POST[$qty_received] < $qty_ordered){
						$hasrec =+ 1;
						$islack =+ 1;
						$with_sample_rec =+ 1;
				 }elseif ($_POST[$qty_received] == $qty_ordered){
						$hasrec =+ 1;
						$iscomplete =+ 1;
						$with_sample_rec =+ 1;
	}

				$refno = $_POST['refno'];
				$item_code = $_POST["items"][$i];

				$sql = "SELECT `is_served` FROM `seg_lab_servdetails` WHERE refno =".$db->qstr($refno)." and service_code=".$db->qstr($item_code);
		    	$is_served_his = $db->GetOne($sql);
				$sql2 = "SELECT `date_served`  FROM `seg_lab_servdetails` WHERE refno =".$db->qstr($refno)." and service_code=".$db->qstr($item_code);
		    	$date_served_his = $db->GetOne($sql2);

				#added by VAS 06/23/2017 
				#with sample
				$sampleid = 'withsampleID'.$_POST["items"][$i];
				$sampleid = str_replace(".","_",$sampleid);
				// var_dump($_POST[$sampleid]);
				if ($_POST[$sampleid]){
					$arraySampleItems[] = $_POST["items"][$i];

					$_POST['with_sample'] = 1;

					$status = 'done';
					$is_served = 1;
					$is_forward = 1;
					$clerk = $_SESSION['sess_temp_userid'];
					$date_served = date("Y-m-d H:i:s");

					$with_sample =+ 1;

					$testinfo = $srvObj->getLabServiceInfo($_POST["items"][$i], BLOODBANK);
					$in_lis = $testinfo['in_lis'];
					
					if ($in_lis){
						#$arrayLISItems[] = array($service_code, $_POST[$nameitems]," ");
						if ($patient_type==INPATIENT)
							$service_code = $testinfo['ipdservice_code'];
						elseif ($patient_type==ERPATIENT)
							$service_code = $testinfo['erservice_code'];
						else
							$service_code = $testinfo['oservice_code'];
							
						$service_name = ($testinfo['alt_name'])?$testinfo['alt_name']:$testinfo['name'];
						$arrayLISItems[] = array($_POST["items"][$i],$service_code, $service_name);
						$with_lis =+ 1;
					}
				}else{
					$_POST['with_sample'] = 0;

					$status = 'pending';
					$is_served = 0;
					$is_forward = 0;
					$clerk = "";
					$date_served = "0000-00-00 00:00:00";
				}		


				 $arraySampleItems_d[] = array($_POST["items"][$i], $qty_ordered,$_POST[$qty_received]);
				$arrayItemsList[] = array($status, $is_served_his, $date_served_his, $clerk, $date_served, $_POST["items"][$i]);

		 }

		 if ($hasrec == 0)
				$status_rec = 'none';
		 elseif ($islack)
				$status_rec = 'lack';
		 elseif ($iscomplete)
				$status_rec = 'complete';

		 $receiver_id = $_SESSION['sess_temp_userid'];
		 $rec_date = date("Y-m-d H:i:s");

		 $arraySampleItems_h[] = array($receiver_id, $rec_date, $status_rec);
	}

	$_POST['arraySampleItems_h'] = $arraySampleItems_h;
	$_POST['arraySampleItems_d'] = $arraySampleItems_d;
	$_POST['with_sample_rec'] = $with_sample_rec;
	$_POST['arrayItemsList'] = $arrayItemsList;
	$_POST['arraySampleItems'] = $arraySampleItems;

	$_POST['arrayLISItems'] = $arrayLISItems;
	$_POST['with_lis'] = $with_lis;

	//EHR is_served ==================================================>
    require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
    
    $items = $_POST['arrayItemsList'];
    $itemRaw = array();
    $itemLists = array();
    for($i=0; $i<count($items); $i++){

    	$itemRaw = array(
    		"service_id"	=> $items[$i][5],
    		"is_served"		=> $items[$i][1],
    		"date_modified"	=> $items[$i][2]
    	);
    	array_push($itemLists, $itemRaw);
		
    }

    $data = array(
    	"refno"			=>  $_POST['refno'],
        "encounter_nr"  =>  $encounter_nr,
        "items"         =>  $itemLists
    ); 

    // $ehr = Ehr::instance();
    // $response = $ehr->postServeLabRequest($data);
    // $asd = $ehr->getResponseData();
    // $EHRstatus = $response->status;

    // echo "<pre>";
    // var_dump($asd); die();

	#added by VAN 06-02-2011
		if ($_POST['for_manual_payment']){
				$manual_data['service_area'] = 'LB';
				$manual_data['control_no'] = $_POST['manual_control_no'];
				$manual_data['approved_by'] = $_POST['manual_approved'];
				$manual_data['type'] = $_POST['for_manual_type'];
				$manual_data['reason'] = $_POST['manual_reason'];

				/*if ($mode=='save')
					$history_label = $srvObj->ConcatHistory("Create: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_temp_userid']."\n");
				elseif ($mode=='update')
					$history_label = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_temp_userid']."\n");

				$manual_data['history'] = $history_label;*/
			$manual_data['create_id'] = $_SESSION['sess_temp_userid'];
				$manual_data['create_date'] = date("Y-m-d H:i:s");
			$manual_data['modify_id'] = $_SESSION['sess_temp_userid'];
				$manual_data['modify_date'] = date("Y-m-d H:i:s");
				$_POST['request_flag'] = $_POST['for_manual_type'];
		}else{
				$manual_data['service_area'] = 'LB';
			$manual_data['history'] = $srvObj->ConcatHistory("Deleted: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_temp_userid']."\n");
			$manual_data['modify_id'] = $_SESSION['sess_temp_userid'];
				$manual_data['modify_date'] = date("Y-m-d H:i:s");
		}

 $success = 0; 
 $postToEmr = false;
 $updateClinicInfo = false;
 switch($mode){
		case 'save':
				if(trim($_POST['orderdate'])!=""){
					$_POST['serv_dt'] = date("Ymd",strtotime($_POST['orderdate']));
				}

				$_POST['loc_code'] = $loc_code;
				$_POST['clinical_info'] = $_POST['clinicInfo'];
				$_POST['request_doctor'] = $_POST['requestDoc'];
				$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
				$_POST['encoder'] = $_SESSION['sess_temp_userid'];
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$_SESSION['sess_temp_userid']." \n";

				$_POST['refno'] = $srvObj->getLastRefno();
                $_POST['request_doctor_out'] = $_POST['requestDocName'];

				#added by angelo m. 09.15.2010 for borrowed info
				#start
				$arr_data['refno']				= $_POST['refno'];
				$arr_data['is_borrowed'] 	= $_POST['chkIsBorrowed'];
				$arr_data['qty_borrowed'] = $_POST['qty_borrowed'];
				$arr_data['bb_remarks'] 	= $_POST['bb_remarks'];
				$arr_data['partner_type'] = $_POST['program_partner'];
				$arr_data['partner_name'] = $_POST['partnerName'];

				#echo print_r($arr_data,true);

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
                $service_id = $_POST['service_code'];
                $sectionItems = array();
                
                $encounter_nr = $enc_obj->getCurrentEncounter($ppid);

                for($i=0; $i<count($sectionDocPersonellNumber); $i++){
                    	$sections = array(
	                    	"personnel_nr"	=>	$sectionDocPersonellNumber[$i],
	                    	"service_id"	=>	$service_id[$i],
	                    	"doctorName"	=> ($sectionDoctors[$i]),
	                    	"sectionDept"	=> $sectionDept[$i],
	                    	"itemNames"		=>	$itemNames[$i],
	                    	"itemPCharge"	=> $itemPCharge[$i],
	                    	"itemDiscount1"	=> $itemDiscount1[$i],
	                    	"itemDiscount2"	=> $itemDiscount2[$i],
	                    	"itemQty"	=>	$itemQty[$i]
                    	);
                    	array_push($sectionItems,$sections);
                    }

                    $bbReq = array(
						"pid"				=>	$_POST['pid'],
						"refno"				=>	$_POST['refno'],
	                    "sectionClinicInfo"	=>  ($sectionClinicInfo),
						"encounter_nr"		=>	$_POST['encounter_nr'],
						"blood_type"		=>	$_POST['blood_type'],
						"is_hact"			=>	$_POST['is_hact'],
						"program_partner"	=>	$_POST['program_partner'],
						"partnerName"		=>	$_POST['partnerName'],
						"chkIsBorrowed"		=>	$_POST['chkIsBorrowed'],
						"qty_borrowed"		=>	$_POST['qty_borrowed'],
						"bb_remarks"		=>	($_POST['bb_remarks']),
						"transactionType"	=>	array(
								"type"		=> 	$is_cash,
								"grant_type"=>	$_POST['grant_type']
							),
						"priority"			=>	$_POST['priority'],
						"comments"			=>	($_POST['comments']),
						"dstamp"			=>	$_POST['dstamp'],
						"from"				=>	'BB',
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
                    // echo "<pre>";
                           
                    $date_c =  $_POST['serv_dt'] +" "+ $_POST['serv_tm'];
                    $bbMon = array(
                    	"pid"				=>	$_POST['pid'],
						"refno"				=>	$_POST['refno'],
						"blood_type"		=>	$_POST['blood_type'],
						"status"			=>	$status_rec,
						"ordered_qty"		=>	$qty_ordered,
						"date_created"		=>	date("Y-m-d H:i:s", strtotime($_POST['orderdate'])),
						"create_id"			=>	$_SESSION['sess_temp_userid'],
						"create_dt"			=>	date("Y-m-d H:i:s")
                    );

		require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
        $ehr = Ehr::instance();
        $patient = $ehr->postLaboratoryRequest($bbReq);
        $asd = $ehr->getResponseData();
        $EHRstatus = $patient->status;

				if ((isset($POST['repeat']))&&($_POST['repeat'])){
					#-------added by VAN 01-11-08-------------
					#$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					$_POST['is_cash'] = 1;

					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];

					#-----------------------------------------

					$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					$isCorrectInfo = $srvObj->count;

					if (($isCorrectInfo)||($allow_labrepeat)){
                        $srvObj->startTrans();
						if($refno = $srvObj->saveLabRefNoInfoFromArray($_POST)){
							$success = 1;
							$saveok=$srvObj->update_LabRefno_Tracker($_POST['refno']);
							$saveok=$srvObj->save_BorrowInfo($arr_data); #added by angelo m. 09.15.2010

							#$smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");
							/*
							if ($_POST['is_urgent']){
								$alert_obj->postAlert('LAB', 4, '', 'New Blood Bank Request', 'New urgent blood bank request posted...', 'h', '');
							}
							*/
						}else{
							#$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->getErrorMsg());
					}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=blood&user_origin=lab&popUp=1&repeat=1&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
                    
                    if (!$saveok){ 
                        $srvObj->FailTrans();
                        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
                    }else{    
                        $srvObj->CompleteTrans();
                        $postToEmr = true;
                        $updateClinicInfo = true;
                        $smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");
                    }
                    
				}else{

                    $srvObj->startTrans();
					if($refno = $srvObj->saveLabRefNoInfoFromArray($_POST)){
							$success = 1;
							$saveok=$srvObj->update_LabRefno_Tracker($_POST['refno']);
							$saveok=$srvObj->save_BorrowInfo($arr_data); #added by angelo m. 09.15.2010

							/*if ($refno && isset($_GET['from_or']) && isset($_GET['or_refno'])) {
									$request_data = array('lab_refno'=>$refno,
																				'or_refno'=>$_GET['or_refno'],
																				'encounter_nr'=>$encounter_nr,
																				'pid'=>$pid,
																				'location'=>2);

									$srvObj->insert_or_request($request_data);
							}*/

						#---added by CHA 08-28-09
						if($output= $bloodObj->saveDonorTransaction($_POST['refno'],$_POST['donor_id'],$_POST['donor_rel']))
						{
								#$smarty->assign('sysInfoMessage',"Blood Request Service successfully created.".$output);
								$smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");
						}
						#---end CHA

						#added by VAN 06-02-2011
						if ($_POST['for_manual_payment'])
							$srvTempObj->save_ManualPayment($_POST['refno'], $manual_data);
						else{
							$srvTempObj->ManualPayment($_POST['refno'], $manual_data);
							$srvTempObj->resetRequestFlag($_POST['refno'], $manual_data, 'seg_lab_servdetails');
						}

						#$smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");

						/*
						if ($_POST['is_urgent']){
								$alert_obj->postAlert('LAB', 4, '', 'New Blood Bank Request', 'New urgent blood bank request posted...', 'h', '');
							}
							*/
					}#else{
						#$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->getErrorMsg());
					#}
                    
                    if (!$saveok){ 
                        $srvObj->FailTrans();
                        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
					}else{
                        $srvObj->CompleteTrans();

                        $bloodObj->saveBloodMonitoringInfoFromArray($bbMon);
                        //var_dump($bloodObj->sql);die();
                        $postToEmr = true;
                        $updateClinicInfo = true;
                        $smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");
					}
				}
				if($postToEmr){
                    try {
                        require_once($root_path . 'include/care_api_classes/emr/services/LaboratoryEmrService.php');
                        $labService = new LaboratoryEmrService();
                        #add new argument to detect if to update patient demographic or not
                        $labService->saveLabRequest($refno);
                    } catch (Exception $exc) {
                        // echo $exc->getTraceAsString();die;
                    }
                }

                if($updateClinicInfo) {
                	$getImp = $enc_obj->getPatientEncInfo($_POST['encounter_nr']);

                	$data = array(
                		'encounter_nr' => $_POST['encounter_nr'],
                		'clinicalInfo' => $_POST['clinicInfo'][count($_POST['clinicInfo']) - 1],
                		'location' => 'BB'
                	);
if ($is_doctor || empty($getImp['er_opd_diagnosis'])  && !$_POST['area']) {
					$enc_obj->updateClinicalImpression($data);
                	$enc_obj->saveToClinicalImpressionTable($data);
}
                }
				break;
		case 'update':
				if(trim($_POST['orderdate'])!=""){
					$_POST['serv_dt'] = date("Ymd",strtotime($_POST['orderdate']));
				}

				$current_list = $srvObj->getListedRequestsByRefNo($refno);
				
				$_POST['clinical_info'] = $_POST['clinicInfo'];
				$_POST['request_doctor'] = $_POST['requestDoc'];
				$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
				$_POST['encoder'] = $_SESSION['sess_temp_userid'];
				$_POST['history'] = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_temp_userid']."\n");

				#$_POST['is_forward']='0';
				#$_POST['is_monitor']='0';

				#added by angelo m. 09.15.2010 for borrowed info
				#start
				$arr_data['refno']				= $_POST['refno'];
				$arr_data['is_borrowed'] 	= $_POST['chkIsBorrowed'];
				$arr_data['qty_borrowed'] = $_POST['qty_borrowed'];
				$arr_data['bb_remarks'] 	= $_POST['bb_remarks'];
				$arr_data['partner_type'] = $_POST['program_partner'];
				$arr_data['partner_name'] = $_POST['partnerName'];

                $itemNameDT = $_POST['nameitemsDT'];
                $ppid = $_POST['pid'];
 				$sectionItems = array();


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
                    }


					$encounter_nr = $enc_obj->getCurrentEncounter($ppid);
					
                    $labReq = array(
						"pid"				=>	$ppid,
						"encounter_nr"		=>	$_POST['encounter_nr'],
	                    "sectionClinicInfo"	=>  $_POST['clinical_info'],
						"transactionType"	=>	array(
								"type"		=> 	$_POST['is_cash'],
								"grant_type"=>	$_POST['grant_type']
							),
						"priority"			=>	$_POST['priority'],
						"comments"			=>	$_POST['comments'],
						"dstamp"			=>	$_POST['dstamp'],
						"from"				=>	'BB',
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

                $bbMon = array(
                    	"pid"				=>	$_POST['pid'],
						"refno"				=>	$_POST['refno'],
						"blood_type"		=>	$_POST['blood_type'],
						"modify_id"			=>	$manual_data['modify_id'],
						"modify_dt"			=>	$manual_data['modify_date']
                    );

		        $ehr = Ehr::instance();
		        $patient = $ehr->postRemoveLabRequestPerItem($labReq);
		        $asd = $ehr->getResponseData();
		        $EHRstatus = $patient->status;

				#echo print_r($arr_data,true);

				if ((isset($POST['repeat']))&&($_POST['repeat'])){
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					$_POST['is_cash'] = 1;
					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];

					$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					$isCorrectInfo = $srvObj->count;

					if (($isCorrectInfo)||($allow_labrepeat)){
						if($srvObj->updateLabRefNoInfoFromArray($_POST)){
							$success = 1;
							#edited by VAN 09-17-2010
							$br_info = $srvObj->get_BorrowedInfo($_POST['refno']);

							if ($br_info['refno'])
								$saveok=$srvObj->update_BorrowInfo($arr_data); #added by angelo m. 09.15.2010
							else
								$saveok=$srvObj->save_BorrowInfo($arr_data);

							//$reloadParentWindow='<script language="javascript">'.
//								'	window.parent.jsOnClick(); '.
//								'</script>';
                        	$postToEmr = true;
                        	$updateClinicInfo = true;
							$smarty->assign('sysInfoMessage',"Blood Request Service successfully updated.");
						}else{
							$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
						}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=blood&user_origin=lab&popUp=1&repeat=1&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
				}else{
					// added by: Arco - 05/30/2016
					// for updating blood borrowed
					$br_info = $srvObj->get_BorrowedInfo($_POST['refno']);
					if ($br_info['refno'])
						$saveok=$srvObj->update_BorrowInfo($arr_data);
					else
						$saveok=$srvObj->save_BorrowInfo($arr_data);
					$smarty->assign('sysInfoMessage',"Blood Request Service successfully updated.");
					// arco

					#echo "<br> parent refno = ".$_POST['parent_refno'];
					if($srvObj->updateLabRefNoInfoFromArray($_POST)){
							$success = 1;
							#edited by VAN 09-17-2010
							$br_info = $srvObj->get_BorrowedInfo($_POST['refno']);

							if ($br_info['refno'])
								$saveok=$srvObj->update_BorrowInfo($arr_data); #added by angelo m. 09.15.2010
							else
								$saveok=$srvObj->save_BorrowInfo($arr_data);

						//$reloadParentWindow='<script language="javascript">'.
//								'	window.parent.jsOnClick(); '.
//								'</script>';

							#added by VAN 06-02-2011
							if ($_POST['for_manual_payment']){
								// echo "<pre>".print_r($manual_data,true)."</pre>";
								$saveok=$srvTempObj->save_ManualPayment($_POST['refno'], $manual_data);

								
							}
							else{
								$saveok=$srvTempObj->ManualPayment($_POST['refno'], $manual_data);
								$saveok=$srvTempObj->resetRequestFlag($_POST['refno'], $manual_data, 'seg_lab_servdetails');
								
							}

                        $postToEmr = true;
                        $updateClinicInfo = true;
                        $bloodObj->updateBloodType($bbMon);
                        //var_dump($bloodObj->sql);die();
						$smarty->assign('sysInfoMessage',"Blood Request Service successfully updated.");
					}else{
						$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
					}
				}
				#echo "sql = ".$srvObj->sql;

				if($postToEmr){
                    try {
                        require_once($root_path . 'include/care_api_classes/emr/services/LaboratoryEmrService.php');
                        $labService = new LaboratoryEmrService();
                        #add new argument to detect if to update patient demographic or not
                        $labService->saveLabRequest($refno, 1);
                    } catch (Exception $exc) {
                        // echo $exc->getTraceAsString();die;
                    }
                }

                if($updateClinicInfo) {
                	$data = array(
                		'encounter_nr' => $_POST['encounter_nr'],
                		'clinicalInfo' => $impression,
                		'location' => 'BB'
                	);

                	if ($is_doctor) {
					$enc_obj->updateClinicalImpression($data);
                	$enc_obj->saveToClinicalImpressionTable($data);
}
                }
				break;
		case 'cancel':
				if($srvObj->deleteRefNo($_POST['refno'])){
					header('Location: '.$breakfile);
					exit;
				}else{
					$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
				}
				break;
	}# end of switch stmt

	#added by VAS 06/23/2017
    # for HL7 compliant
    #if successfully saved or updated
    if ($connection_type=='hl7'){
  	    #cancelled
        #validate if there a LIS posted request

        $delete_only_list = array_diff($current_list,$_POST['service_code']);
       
        if (is_array($delete_only_list) && !empty($delete_only_list)){
        	foreach ($delete_only_list as $key => $value){
	        	$scode .= "'".trim($value)."',";
			}
			$testcode = trim($scode);
            $testcode = substr($testcode,0,strlen($testcode)-1);
            #echo "<br>s =".$testcode;
            
		}	
		#echo "test =".$testcode;
		#there are items to be deleted
		if ($testcode){
	        #$hl7_row = $bloodObj->isExistHL7Msg($refno);
	        $rs = $bloodObj->getHL7Msg($refno, $testcode);
	        #echo "<br>".$bloodObj->sql;
	        #if ($hl7_row['msg_control_id']){
	        
	        if (is_object($rs)){
	            while($hl7_row=$rs->FetchRow()){    
	                $order_control = "CA";
	                
	                $hl7msg_row = $bloodObj->isforReplaceHL7Msg($refno,$order_control, $hl7_row['service_code']); 
	            	#echo "<br>".$bloodObj->sql;

	                if ($hl7msg_row['msg_control_id']){
	                    $msg_control_id = $hl7msg_row['msg_control_id'];
	                    $forreplace = 1;   
	                }else
	                    $msg_control_id = $bloodObj->getLastMsgControlID(BLOODBANK);
	                
	                $prefix = BLOODBANK."HIS";
	                #echo "<br>s=".$msg_control_id;
	                #replace NW or RP to CA
	                $filecontent = $hl7_row['hl7_msg'];
	                $filecontent = str_replace("ORC|NW|", "ORC|CA|", $filecontent);    
	                
	                $details->msg_control_id_db = $msg_control_id;
	                $details->msg_control_id = $prefix.$msg_control_id;
	                
	                $details->order_control = $order_control;
	                
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
	                                
	                                $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
	                                     
	                             	#return/print result
	                             	$text = "LIS Server said:: ".$obj;
	                                
	                                echo $text;
	                                break;                    
	                }
	                                                
	                #update msg control id
	                $details->msg_control_id = $details->msg_control_id_db;
	                
	                #if new message control id, update the tracker
	                #if (!$forreplace)
	                #    $hl7_ok = $bloodObj->updateHL7_msg_control_id($details->msg_control_id, BLOODBANK);
	                    
	                #HL7 tracker
	                $details->lis_order_no = $hl7_row['lis_order_no'];
	                $details->msg_type = $hl7_row['msg_type'];
	                $details->event_id = $hl7_row['event_id'];
	                $details->refno = $refno;
	                $details->pid = $hl7_row['pid'];
	                $details->encounter_nr = $hl7_row['encounter_nr'];
	                $details->hl7_msg =  $filecontent;
	                $details->service_code = $hl7_row['service_code'];
	                #nth units
	                $details->nth_units = $hl7_row['nth_units'];
	                                            
	                                    
	                if ($forreplace){
	                    #$bSuccess = $bloodObj->updateInfo_HL7_tracker($details);
	                }else{
	                	$db->BeginTrans();

	                    $bSuccess = $bloodObj->addInfo_HL7_tracker($details);
	                    $bSuccess = $bloodObj->updateHL7_msg_control_id($details->msg_control_id, BLOODBANK);
	                	#$bSuccess = $bloodObj->insert_Orderno_HCLAB($lis_order_no, $refno);
	                	#$bSuccess = $bloodObj->update_HCLabRefno_Tracker($lis_order_no);

	                	if ($bSuccess)
	                        $db->CommitTrans();
	                    else
	                        $db->RollbackTrans();
	                }
	                                        
	                
	            }
	        }    
        }    
    } #end if ($connection_type=='hl7')
    #----------------------- end HL7 info

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
	if ($prevrefno){
		$refInfo = $srvObj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);

		if ($refInfo['parent_refno'])
			//$refno = $refInfo['parent_refno'];
			$refno = $refInfo['refno'];
	}

	$mode='save';   # default mode
	if ($refNoBasicInfo = $srvObj->getBasicLabServiceInfo($refno)){
		$mode='update';
		extract($refNoBasicInfo);

		$serv_dt = formatDate2Local($serv_dt,$date_format);

	}

	 #$view_from = '';
	 if ($_GET['view_from'])
			$view_from = $_GET['view_from'];

	 if ($view_from=='ssview'){
		 if ($_GET['discountid']){
			 $discountid = $_GET['discountid'];
			 $infoSS = $objSS->getSSClassInfo($discountid);
//			 print_r($objSS->sql);
//
//			 if ($infoSS['parentid'])
//					$discountid = $infoSS['parentid'];
//			 else
					$discountid = $discountid;

			 $discount = $infoSS['discount'];
		 }
		 #$view_from = $HTTP_SESSION_VARS['view_from'];
	 }else
			$view_from = '';
 #echo $srvObj->sql;

 # Title in the title bar
 $LDBloodBank = "Blood Bank";

 $smarty->assign('sToolbarTitle',"$LDBloodBank :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBloodBank :: New Test Request");

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

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript">
var $J = jQuery.noConflict();
</script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/blood-request-new.js?t=<?=time()?>"></script>
 <script type="text/javascript">
		//added by angelo m. 09.15.2010
		//start
		function toggleBorrowed(){
			if($('chkIsBorrowed').checked){
				$('chkIsBorrowed').value=1;
				$('qty_borrowed').disabled=false;
				$('bb_remarks').disabled=false;


			}
			else{
				$('chkIsBorrowed').value=0;
				$('qty_borrowed').value="";
				$('bb_remarks').value="";
				$('qty_borrowed').disabled=true;
				$('bb_remarks').disabled=true;
			}
		}

		function isNumberKey(evt){
				 var charCode = (evt.which) ? evt.which : event.keyCode
				 if (charCode > 31 && (charCode < 48 || charCode > 57))
						return false;

				 return true;
		}

		//end
	</script>
<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	shortcut("F2",
		function(){
			var is_cash = $('is_cash').value;
			var pid = $('pid').value;
			var refno = $('refno').value;
			//alert(is_cash+", "+pid+", "+refno);
			viewPatientRequest(is_cash,pid,refno)
		}
	);

	function checkCash(){
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

	function NewRequest(){
		urlholder="seg-blood-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}

		//added by CHA 08-27-09
		function displayDonor(checkval)
		{
				if(checkval && document.inputform.with_donor.checked==true)
				{
						document.getElementById('add_donor_name').style.display='';
						document.getElementById('add_donor_rel').style.display='';
				}
				else
				{
						document.getElementById('add_donor_name').style.display='none';
						document.getElementById('add_donor_rel').style.display='none';
						document.getElementById('donor_name').value='';
						document.getElementById('donor_rel').value='';
						document.getElementById('donor_id').value='';
				}
		}
		//end CHA

-->

	function setReplaceBlood(enc, refno) 
	{
		if ($('chkIsReplaced').checked){
			$('chkIsBorrowed').checked=false;
			toggleBorrowed();
			xajax_replacedBlood(enc, refno);
		}
	}

//modified by raymond : add additional info before printing bloodbank waiver
function printWaiver() {
		var HRN = $J('#hrn').html();
		var enc_nr = $('encounter_nr').value;
		var age = $J('#age').html();
		var name = $('ordername').value;
		var refno = $('refno').value;
		if (HRN == "") {
			alert("Select a patient");
		}else{
			if(refno){
				overlib(
            		OLiframeContent('seg_blood_waiver.php<?php echo URL_REDIRECT_APPEND; ?>&ages='+age+'&refno='+refno+'&hrn='+HRN+'&name='+name+'&encounter_nr=' + enc_nr + "&userck=<?php echo $_GET['userck']; ?>",
                    1000, 450, 'fGroupTray', 0, 'auto'),
                	WIDTH, 1000, TEXTPADDING, 0, BORDER, 0,
	                STICKY, SCROLL, CLOSECLICK, MODAL,
	                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
	                CAPTIONPADDING, 2, CAPTION, 'Waiver Additional Information',
	                MIDX, 0, MIDY, 0,
	               	STATUS, 'Waiver Additional Information');
			}else {
				var url = "reports/waiver_report.php?pid=" + HRN + "&enc" + enc_nr + "&ages" + age;
				var win = window.open(url, '_blank');
				if (win) {
					win.focus();
				}
			}
    }
    return false;
}

function openTransfusionRecord() {
    var HRN = $J('#hrn').html();
    var enc_nr = $('encounter_nr').value;
    var age = $J('#age').html();
    var name = $('ordername').value;
    var refno = $('refno').value;

    if (HRN == "") {
        alert("Select a patient");
    }else{
        if(refno){
            overlib(
                OLiframeContent('seg_blood_transfusion_report.php<?php echo URL_REDIRECT_APPEND; ?>&ages='+age+'&refno='+refno+'&hrn='+HRN+'&name='+name+'&encounter_nr=' + enc_nr+"&userck=<?php echo $_GET['userck']; ?>",
                    1000, 600, 'fGroupTray', 0, 'auto'),
                WIDTH, 1000, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
                CAPTIONPADDING, 2, CAPTION, 'Blood Transfusion Record',
                MIDX, 0, MIDY, 0,
                STATUS, 'Blood Transfusion Record');
        }
    }
    return false;
}
	
function loadCoverage() {
	updateCoverage([$('encounter_nr').value]);
}
	
</script>

<?php

	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');

	$var_arr = array(
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_discountid"=>"discountid",
		"var_orig_discountid"=>"orig_discountid",
		"var_discount"=>"discount",
		"var_name"=>"ordername",
		"var_addr"=>"orderaddress",
		"var_clear"=>"clear-enc",
		"var_area"=>"area",
		"var_history"=>"btnHistory"
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
				OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&ref_source=BB&var_include_enc='+($('iscash1').checked?'0':'1'),".
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

	$loc = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name);
	$smarty->assign('sClassification',(($discountid2) ? $discountid2 : 'None'));
	$smarty->assign('sPatientType',(($enctype) ? mb_strtoupper($enctype): 'None'));
	$smarty->assign('sPatientLoc',(($location) ? mb_strtoupper($location) : 'None'));
	$smarty->assign('sPatientMedicoLegal',(($is_medico) ? "YES" : 'NO'));

	if ($_POST["ref"]!=NULL)
		$Ref = $_POST["ref"];
	elseif ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	else{
		if (!$repeat)
			$Ref = $refno;
	}


	if ($repeat){
		$Ref = "";
		#$Ref2 = "";
	}else{
		if ($is_cash==0){
			$Ref = $refno;
			#$Ref2 = $refno;
		}else{
			$sql_hasPaid = "SELECT SUM(CASE WHEN(request_flag IS NOT NULL ) THEN 1 ELSE 0 END) AS withpaid
										FROM seg_lab_servdetails WHERE refno='$refno'";
			$rspaid = $db->Execute($sql_hasPaid);
			$rowpaid = $rspaid->FetchRow();
			extract($rowpaid);

			if ($withpaid){
				#$hasPaid = $withpaid;
				$hasPaid = 1;
				#$Ref2 = $refno;
			}
		}
	}

	if (($is_cash==0) && ($hasPaid==1))
		$hasPaid = 0;

	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$Ref.'" readonly style="font:bold 12px Arial"/>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";

	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);

	if (($repeat)||(empty($serv_dt)))
		$curDate = date($dbtime_format);
	elseif (($serv_dt!='0000-00-00')||(!empty($serv_dt))) {
		$requestDate = $serv_dt." ".$serv_tm;
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

	#edited by VAN as DR. Vega's instruction
	#$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" onClick="checkPriority();" value="0"'.($is_urgent? "": " checked").'>Routine');
	#$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" onClick="checkPriority();" value="1"'.($is_urgent? " checked": "").'>STAT');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" onClick="" value="0"'.($is_urgent? "": " checked").'>Routine');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" onClick="" value="1"'.($is_urgent? " checked": "").'>STAT');

	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');

	// updated by carriane 10/24/17; added IPBMdisable
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');

	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" wrap="physical"  cols="30" rows="5" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"9\">Request list is currently empty...</td>
				</tr>");
	if (!$ischecklist){
		 $filename = 'special_lab/seg-splab-service-tray.php';
	}else{
		 $filename = 'laboratory/seg-request-tray-checklist.php';
	}
    #edited by jasper 01/29/13 appended &section=B
	/*$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\''.$root_path.'modules/'.$filename.'?&ref_source=BB&area='.$area.'&is_dr='.$is_dr.'&dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'&pid='.$pid.'&encounter_nr='.$encounter_nr.'&section=B\', 600, 435, \'fOrderTray\', 1, \'auto\'),
					WIDTH,435, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4,
					CAPTION,\'Add blood bank service item from request tray\',
					MIDX,0, MIDY,0,
					STATUS,\'Add blood bank service item from request tray\');"
			onmouseout="nd();">');*/
    
    // updated by carriane 10/24/17; added IPBMextend
    $smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
            onclick="if (validatePHIC()) { return overlib(
                OLiframeContent(\''.$root_path.'modules/'.$filename.'?&ref_source=BB&area='.$area.'&is_dr='.$is_dr.'&dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'&pid='.$pid.'&encounter_nr='.$encounter_nr.'&section=B'.$IPBMextend.'\', 600, 435, \'fOrderTray\', 1, \'auto\'),
                    WIDTH,435, TEXTPADDING,0, BORDER,0,
                    STICKY, SCROLL, CLOSECLICK, MODAL,
                    CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
                    CAPTIONPADDING,4,
                    CAPTION,\'Add blood bank service item from request tray\',
                    MIDX,0, MIDY,0,
                    STATUS,\'Add blood bank service item from request tray\'); } 
                    else alert(\'Cannot charge a blood request to PHIC because the PHIC number is not eligible. \n Either a temporary number or the number format is not correct. \n Please charge it but not to PHIC or pay the request instead.\');"
            onmouseout="nd();">');        

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
	$smarty->assign('sRDU','<input type="checkbox" '.(($is_rdu==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" onChange="definePriority(); enablePhic();" '.$IPBMdisable.'/>');
	$smarty->assign('sWalkin','<input type="checkbox" '.(($is_walkin==1)?'checked="checked" ':'').' name="is_walkin" id="is_walkin" onchange="checkIfWalkin()" value="1" '.$IPBMdisable.'/>');
	//end carriane
	$smarty->assign('sPE','<input type="checkbox" '.(($is_pe==1)?'checked="checked" ':'').' name="is_pe" id="is_pe" onchange="" '.(($is_personnel)?'':'disabled="disabled" ').' value="1" />');

    $request_time = $serv_dt." ".$serv_tm;
    $request_time = date("Y-m-d H:i:s", strtotime($request_time));
    $row_hact = $srvObj->checkHactInfo($pid, $request_time);
    #echo $srvObj->sql;
    if ($row_hact['status']=='hact')
        $is_hact = 1;
    else
        $is_hact = 0;    
    
    $smarty->assign('sHACT','<input type="checkbox" '.(($is_hact==1)?'checked="checked" ':'').' name="is_hact" id="is_hact" value="1" />');    

    #get patient Blood Type
    #$row_pbt = $srvObj->checkBloodTypeInfo($pid, $request_time);
    $row_pbt = $srvObj->getBloodTypeInfo($pid);
    $blood_type = $row_pbt['blood_type'];
    $sql_bt = 'SELECT * FROM seg_blood_type ORDER BY ordering';
    $rs_bt = $db->Execute($sql_bt);
    $bt_option = '';

    if(!$pid || ($pid && $allow_updateBloodData) || ($pid && !$allow_updateBloodData && !$blood_type)){
	    $bt_option="<option value=''>-Not Indicated-</option>";
    }

    if (is_object($rs_bt)){
        $disabled_bt = '';
        while ($row_bt=$rs_bt->FetchRow()) {
            $selected='';
            if ($blood_type==$row_bt['id']){
                $selected='selected';
                $disabled_bt = '';
            }else{
            	if(!$allow_updateBloodData && $blood_type)
            		$disabled_bt = 'disabled';	
            } 
            
            $bt_option.='<option '.$selected.' '.$disabled_bt.' value="'.$row_bt['id'].'">'.$row_bt['name'].'</option>';
            
        }
    }

    $bt_selection = '<select name="blood_type" id="blood_type" class="segInput" style="font-weight:bold;font-size:14px">
	                        '.$bt_option.'
	                    </select>';
    
    $smarty->assign('sBloodType',$bt_selection);
    
	$smarty->assign('sHistoryButton','<img type="image" name="btnHistory" id="btnHistory" src="'.$root_path.'images/btn_history.gif" border="0" style="cursor:pointer;" onclick="viewHistory($(\'pid\').value,$(\'encounter_nr\').value);">');
    $smarty->assign('sTransfusionHistoryButton','<img type="image" name="btnHistory" id="btnHistory" src="'.$root_path.'images/btn_transfusion_history.png" border="0" style="cursor:pointer;" onclick="printTransfusionHistory();">');
    #$smarty->assign('sOtherButton','<img type="image" name="btnOther" id="btnOther" src="'.$root_path.'images/btn_add_other.gif" border="0" style="cursor:pointer;" onclick="addOtherCharges($(\'pid\').value,$(\'encounter_nr\').value,$(\'ward_nr\').value);">');
    $charge_type = $glob_obj->getChargeType();
    /*if(isset($user_origin) && $user_origin !='lab'){
		$charge_type .= ",'sdnph'";
	}*/


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

		if(isset($user_origin) && $user_origin !='lab'){
			if(!in_array($row['id'], $labOnlyTypes))
				$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
		}

	}

	# Edited by James 2/24/2014

	/*
	** Added by James 2/24/2014
	** Purpose: To hide grant_type in Charge for IC Transactions
	*/
	#edited by art 07/10/2014
	#commented checkCharge
	if($encounter_type==6)
	{
		if(!$is_cash)
		{
			$smarty->assign('sChargeTyp',
	                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" hidden 
	                                     onchange=\" if (warnClear()) { emptyTray(); /*checkCharge(this.value); */changeChargeType(); return true;} else {return false;}\">
	                                         $options
									 </select>");
		}
		else
		{
		    $smarty->assign('sChargeTyp',
		                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled." 
		                                     onchange=\" if (warnClear()) { emptyTray(); /*checkCharge(this.value); */changeChargeType(); return true;} else {return false;}\">
		                                         $options
										 </select>");
		}
	}
	else
	{
	    $smarty->assign('sChargeTyp',
	                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled." 
	                                     onchange=\" if (warnClear()) { emptyTray(); /*checkCharge(this.value);*/ changeChargeType(); return true;} else {return false;}\">
	                                         $options
									 </select>");
	}
	
    //onchange=\" if (this.value=='phic'){if (warnClear()) { emptyTray(); changeChargeType(); return true;} else {return false;}}else {return false;}\">
	#------------end TEMPORARILY -------

	if ($parent_refno){
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

	#added by angelo m. 09.15.2010
	#start

	#global $db;

	$row=$srvObj->get_BorrowedInfo($refno);
	if($row){
		$_POST['is_borrowed'] = $row['is_borrowed'];
		$_POST['qty_borrowed'] = $row['qty_borrowed'];
		$_POST['bb_remarks'] = $row['bb_remarks'];
		$_POST['partner_type'] = $row['partner_type'];
		$_POST['partner_name'] = $row['partner_name'];
	}

	$query="SELECT
						sbp.code,
						sbp.description
					FROM seg_blood_program_partner AS sbp";

	$result=$db->Execute($query);
	$opt_prg_partners="<select name='program_partner' id='program_partner' class='segInput'>
											<option value=''>-- Select --</option>";
	if($result)
	while($row=$result->FetchRow()){
			if($_POST['partner_type']==$row['code'])
				$opt_prg_partners.="<option value='".$row['code']."' selected='selected'>".$row['description']."</option>";
			else
				$opt_prg_partners.="<option value='".$row['code']."'>".$row['description']."</option>";

	}
	$opt_prg_partners.="</select>";

	$smarty->assign('sProgramPartner',$opt_prg_partners);
	$smarty->assign('spartnerName',"<input type='text' id='partnerName' name='partnerName' size='40' class='segInput' value='".$_POST['partner_name']."' />");


	 if($_POST['is_borrowed'] == "1"){ #edited by: syboy, 07/22/2015
			$chkIsBorrowedChecked="checked=true";
			$chkIsBorrowedValue=1;
			$isDisableBorrow="";

		//Added by Jarel 12/12/13
		$smarty->assign('sReplaceRow','<tr>
											<td valign="left"><div style=""><strong>Replaced?</strong></div></td>
											<td valign="left"><input type="checkbox"  id="chkIsReplaced" name="chkIsReplaced"  onclick="setReplaceBlood('.$encounter_nr.', '.$refno.');" /></td>
										</tr>');
	 } else{
		$chkIsBorrowedChecked="";
		$chkIsBorrowedValue=0;
		$isDisableBorrow="disabled='true'";
	 }

	$smarty->assign('schkIsBorrowed',"<input type='checkbox'  id='chkIsBorrowed' name='chkIsBorrowed'  onclick='toggleBorrowed()'	$chkIsBorrowedChecked value='$chkIsBorrowedValue' />");
	$smarty->assign('sqty_borrowed',"<input type='text' id='qty_borrowed' size='5' name='qty_borrowed' value='".$_POST['qty_borrowed']."' class='segInput' onkeypress='return isNumberKey(event)' $isDisableBorrow />");
	$sbb_remarks='<textarea style="width: 100%; font: italic	 12px Arial; border:
						1px solid rgb(195, 195, 195); overflow-y: scroll; float: left;"
						 name="bb_remarks" rows="5" id="bb_remarks" class="segInput" '.$isDisableBorrow.'>'.$_POST['bb_remarks'].'</textarea>';
	$smarty->assign('sbb_remarks',$sbb_remarks);
	#end



	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sParentRefno','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/>');
	$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($remarks).'</textarea>');
	$smarty->assign('sHead','<input class="segInput" id="approved_by_head" name="approved_by_head" type="text" size="40" value="'.$approved_by_head.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');

	#added by VAN 08-23-2010
	#FOR Industrial Clinic Info
	$smarty->assign('sChargeToComp','<input type="checkbox" name="is_charge2comp" id="is_charge2comp" value="1" '.(($is_charge2comp=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sCompanyName',$compName);
	$smarty->assign('sCompanyID','<input class="segInput" id="compID" name="compID" type="hidden" size="10" value="'.$compID.'" style="font:bold 12px Arial;" readonly/>');

	$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
	$smarty->assign('sFormEnd','</form>');

	#added by VAN 06-02-2011
	$sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='LB' AND refno='".$refno."' AND is_deleted=0";
	$res_manual=$db->Execute($sql_manual);
	$row_manual_count=$res_manual->RecordCount();
	$row_manual = $res_manual->FetchRow();

	//Add Query for the DropdownList Borj 2014-20-03 PCSO

				$blood_payment = $row_manual['type'];

                $sql_blood_payment = 'SELECT * FROM seg_blood_payment';
                $rs_blood_payment = $db->Execute($sql_blood_payment);
                $blood_payment_option="<option value=''>-Manual Payment-</option>";

                if (is_object($rs_blood_payment)){
                    while ($row_blood_payment=$rs_blood_payment->FetchRow()) {
                        $selected='';
                        if ($blood_payment==mb_strtolower($row_blood_payment['id']))
                            $selected='selected';
                      
                        $blood_payment_option.='<option '.$selected.' id="'.$row_blood_payment['long_name'].'" value="'.$row_blood_payment['id'].'">'.ucwords($row_blood_payment['long_name'] == "CMAP" ? "MAP" : $row_blood_payment['long_name']).'</option>';
                    }

                }

                
    //END
    # added by: syboy 11/30/2015 : meow
    if ($allow_bloodManualPay) {
    	$is_allow = '';
    }else{
    	$is_allow = 'disabled';
    }
   
	$smarty->assign('sManualCheck','<input type="checkbox" '.$is_allow.' name="for_manual" id="for_manual" value="1" '.(($row_manual_count)?'checked="checked" ':'').' onClick="setManualPayment();" />
																	<input type="hidden" name="for_manual_payment" id="for_manual_payment" value="">');
	$smarty->assign('sManualNumber','<input class="segInput" '.$is_allow.' id="manual_control_no" name="manual_control_no" type="text" size="40" value="'.$row_manual['control_no'].'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sManualApprovedby','<input class="segInput" '.$is_allow.' id="manual_approved" name="manual_approved" type="text" size="40" value="'.$row_manual['approved_by'].'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sManualReason','<textarea class="segInput" '.$is_allow.' id="manual_reason" name="manual_reason" cols="37" rows="2" style="font:bold 12px Arial">'.$row_manual['reason'].'</textarea>');
	//Update Radio button to DropdownList Borj 2014-20-03 PCSO
	$smarty->assign('sManualTypeSelection',"<select  name=\"for_manual_type\" id=\"for_manual_type\" class=\"jedInput\"  onChange=\"setLabel();\" ".$is_allow.">$blood_payment_option  </select>");
	//END

	// $smarty->assign('sManualTypeSelection','<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type1" value="paid" '.(($row_manual['type']=='paid')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>Cash</strong>&nbsp;
	// 																				<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type2" value="lingap" '.(($row_manual['type']=='lingap')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>Lingap</strong>&nbsp;
	// 																				<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type3" value="cmap" '.(($row_manual['type']=='cmap')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>CMAP</strong>&nbsp;
	// 																				<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type4" value="phic" '.(($row_manual['type']=='phic')?'checked="checked" ':'').' disabled onClick="setLabel()"/>&nbsp;<strong>PHIC</strong>&nbsp;');

	#--------------

    #added by VAS 03/21/2012
    $phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$encounter_nr."') AS `phic_nr`");
    $smarty->assign('sPhicNo', $phic_nr);
    
    $smarty->assign('sBtnCoverage','<img type="image" name="btn-coverage" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" style="cursor:pointer;" onclick="return openCoverages();">');
//    $smarty->assign('sBtnReport','<img type="image" name="btn-coverage" id="print_report" onclick="printWaiver()" src="'.$root_path.'images/forms.jpg" border="0" style="cursor:pointer;" >');
//    $smarty->assign('sBtnGenXM','<img type="image" name="btn-XM" id="requst_XM" src="'.$root_path.'images/bloodtransfusionreport.jpg" border="0" style="cursor:pointer;" >');
//    $smarty->assign('sBtnPledgeCommit','<img type="image" name="btn-pledge" id="pledge_commit" src="'.$root_path.'images/btn_pledge.png" border="0" style="cursor:pointer;">');
    $formsIconBtn = '<img type="image" src="'.$root_path.'images/forms.jpg" border="0" style="cursor:pointer;width:63px;position:absolute;" >';
    $formsOptions = "<option>Forms</option>
                     <option onclick='printWaiver()'>Waiver of Deposit</option>
                     <option id='requst_XM'>Crossmatching Request</option>
                     <option id='pledge_commit'>Pledge of Commitment</option>
                    ";
    $smarty->assign('sBtnReport',$formsIconBtn.'<select id="forms" onchange="this.value=\'Forms\'" class="segInput" style="vertical-align: top;width:63px;margin-left:18px;">'.$formsOptions.'</select>');
    $smarty->assign('sBtnGenXM','<img type="image" onclick="openTransfusionRecord()" src="'.$root_path.'images/bloodtransfusionreport.jpg" border="0" style="cursor:pointer;" >');

    #$smarty->assign('sBtnCoverage','<button class="segButton" id="btn-coverage" onclick="return openCoverages(); return false;"><img src="'.$root_path.'gui/img/common/default/book_edit.png"/>PHIC Coverages</button>');
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
    if($CategoryUi == NBB || $CategoryUi == HSM)        
    	$smarty->assign('sMemCategoryColor', "red");  
    else
    	$smarty->assign('sMemCategoryColor', "#000080");  

    $smarty->assign('sMemCategory', $CategoryUi);
   #end pol
 
 ?>
<?php
ob_start();
$sTemp='';

if ($repeat){
	if ($refInfo['parent_refno'])
		$batchnr = $refInfo['refno'];
	else
		$batchnr = $prevbatchnr;
}else
	$batchnr = 0;

?>

	<script type="text/javascript" language="javascript">

		preset(<?= ($is_cash=='0')? "0":"1"?>);
		var refno = '<?=$refno?>';
		var view_from = '<?=$view_from?>';

		populaterequestitems(refno,view_from);

		// Transferred to a function by carriane 02-04-2020
		/*var user_origin = $('user_origin').value;
		var refno = '<?=$refno?>';
		var view_from = '<?=$view_from?>';
		var batchnr = '<?=$batchnr?>';
		var fromSS = 0;
		var discount = $('discount').value;
		var discountid = $('discountid').value;
		var ipbmenctype = $('ipbmenctype').value; // added by carriane 10/24/17

		if (view_from=='ssview')
			fromSS = 1;

		switch (user_origin){
			case 'blood' :  ref_source = 'BB'; break;
			case 'lab' 	 :	ref_source = 'LB'; break;
			case 'splab' :  ref_source = 'SPL'; break;
			case 'iclab' :  ref_source = 'IC'; break;
		}

		// added by carriane 10/24/17
		if(ipbmenctype != ''){
			$('is_rdu').disabled = true;
			$('is_walkin').disabled = true;
		}
		// end carriane

		if (refno){
			xajax_populateRequestListByRefNo(refno, ref_source, fromSS, discount, discountid);

            changeChargeType();
        } */   

	</script>


<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}

if (((($hasPaid)|| (!$is_cash))&&($mode=='update'))||($mode=='update')||($repeat)){
		$smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');
		$withclaimstub = 1;
}



ob_start();
$sTemp='';

?>	
	<input type="hidden" name="blood_manual" id="blood_manual" value="<?php echo $allow_bloodManualPay?>"> <!-- added by: syboy 12/13/2015 : meow -->
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
	<input type="hidden" id="name_first" name="name_first" value="<?=$name_first_val;?>">
	<input type="hidden" id="name_last" name="name_last" value="<?=$name_last_val;?>">
	<input type="hidden" id="name_middle" name="name_middle" value="<?=$name_middle;?>">
	<input type="hidden" id="total_quantity" name="total_quantity" value="">
	<input type="hidden" id="disablebtupdate" name="disablebtupdate" value="">

	<?php
		if (empty($Ref))
			$mode='save';
		else
			$mode='update';

		if ($_GET['view_from'])
			$view_from = $_GET['view_from'];
		elseif ($_POST['view_from'])
			$view_from = $_POST['view_from'];

		// updated by carriane 10/24/17; added IPBMIPD_enc
		if (($encounter_type==3)||($encounter_type==4)||($encounter_type==IPBMIPD_enc)){
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
        
		// added by carriane 10/24/17
		if($encounter_type == NULL){
			$enctypeIPBM = $enc_obj->EncounterType($encounter_nr);

			if($enctypeIPBM == IPBMOPD_enc || $enctypeIPBM == IPBMIPD_enc){
				$IPBMencounter_type = $enctypeIPBM;
			}
		}
		// end
        
	?>
	<input type="hidden" name="ipbmenctype" id="ipbmenctype" value="<?=$IPBMencounter_type?>"> <!-- added by carriane 10/24/17 -->
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
	<input type="hidden" name="isserve" id="isserve" value="<?=($canServeRequest)?1:0; ?>"> <!-- add by carl Permision -->
	<input type="hidden" name="createnew" id="createnew" value="<? ($_GET['pid'])? 1:0; ?>"> <!-- add by carl Create new form -->

	<input type="hidden" name="currenttime" id="currenttime" value="<?=date('H')?>">

	<input type="hidden" name="withclaimstub" id="withclaimstub" value="<?=$withclaimstub?>" />

	<input type="hidden" name="source_req" id="source_req" value="<?=(($repeat)||(empty($source_req)))?'BB':$source_req?>">
	<input type="hidden" name="login_user" id="login_user" value="<?=$personell_nr?>">
	<input type="hidden" name="is_dr" id="is_dr" value="<?=$is_dr?>">

    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
    <input type="hidden" name="accomodation" id="accomodation" value="">
    <input type="hidden" name="cov" id="cov" value="">
    <input type="hidden" name="admission_accomodation" id="admission_accomodation" value="<?= $_GET['enc_accomodation']?>">
    <input type="hidden" name="DiscountID" id="DiscountID" value="<?=$DiscountID?>">
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

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','blood/blood-request-new.tpl');
$smarty->display('common/mainframe.tpl');
