<?php

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person();


    require_once($root_path.'include/care_api_classes/class_personell.php');
    $pers_obj=new Personell;
		/* Create encounter object */
	$encounter_obj=new Encounter($encounter_nr);

#added by VAN 11/19/2013
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
$emr_obj = new EMR;

require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

$row_hosp = $objInfo->getAllHospitalInfo();
$EMR_address = $row_hosp['EMR_address'];
$EMR_directory = $row_hosp['EMR_directory'];
#======================

	/* Get the patient global configs */
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('patient_%');
	$glob_obj->getConfig('encounter_%'); 

	$newdata=1;
	
	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];
	elseif ($_POST['ptype'])
		$ptype = $_POST['ptype'];	
	#echo "ptype = ".$ptype;	
    
    if ($_GET['encounter_date'])
        $encounter_date = $_GET['encounter_date'];
    else
        $encounter_date = date('Y-m-d H:i:s');    
    

		/* Determine the format of the encounter number */
	if($GLOBAL_CONFIG['encounter_nr_fullyear_prepend'])
		$ref_nr=(int)date('Y').$GLOBAL_CONFIG['encounter_nr_init'];
	else 
		$ref_nr=$GLOBAL_CONFIG['encounter_nr_init'];
		//echo $ref_nr;
		# get an encounter number
		# NOTE: 2nd argument is ALWAYS 1 --- this file is ONLY being used by ER
	#$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_er_nr_adder'],1);
	  $triage = 'er';
		$last_enc_nr = $encounter_obj->getLastEncounterNr($triage);
							
		if ($last_enc_nr)
			$ref_nr = $last_enc_nr;
									
		$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,1);
	
	

  	if($encoder=='') 
		$encoder=$HTTP_SESSION_VARS['sess_login_username'];
		
	$HTTP_POST_VARS['pid']=$pid;
	$HTTP_POST_VARS['current_dept_nr']=$current_dept_nr;
	
	$HTTP_POST_VARS['category']=$category;
	
	#$HTTP_POST_VARS['encounter_date']=date('Y-m-d H:i:s');
    $HTTP_POST_VARS['encounter_date']=$encounter_date;
	$HTTP_POST_VARS['encounter_class_nr']=1;   # NOTE: 2nd argument is ALWAYS 1 --- this file is ONLY being used by ER
	$HTTP_POST_VARS['encounter_type']=1;
	$HTTP_POST_VARS['modify_id']=$encoder;
	$HTTP_POST_VARS['modify_time']=date('YmdHis');
	$HTTP_POST_VARS['create_id']=$encoder;
	$HTTP_POST_VARS['create_time']=date('YmdHis');
	$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' = '.$encoder;
	
	$encounter_obj->setDataArray($HTTP_POST_VARS);
	$db->StartTrans();								
	if($encounter_obj->insertDataFromInternalArray()){
		$bSuccess = $encounter_obj->update_Encounter_Tracker($HTTP_POST_VARS['encounter_nr'],$triage);

    # added by VAS 11/19/2013
    # integration to EMR starts here
    # Post corresponding Patient's Demographic demographic in EMR
    $encounter_nr = $HTTP_POST_VARS['encounter_nr'];

    /**
     * edited by marc lua 3/6/2014
     */
    require_once($root_path . 'include/care_api_classes/emr/services/EncounterEmrService.php');
    require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');


	$getPersonData = $pers_obj->getPersonData($pid);
	$date_birth = date('Y-m-d', strtotime( $getPersonData['date_birth'] ));

	$patient_type = 'ERE';
	include_once($root_path.'include/inc_date_format_functions.php');
	require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
	$patientData = array(
		'pid'					=>$pid,
		'encounter_nr'			=> $HTTP_POST_VARS['encounter_nr'],
		'er_areaid'				=> $HTTP_POST_VARS['current_dept_nr'],
		'date'		=> $HTTP_POST_VARS['encounter_date'].' '.$HTTP_POST_VARS['encounter_time'].":00 ".$HTTP_POST_VARS['selAMPM2'],
		'firstName'				=> $getPersonData['name_first'],
		'middleName'			=> $getPersonData['name_middle'],
		'lastName'				=> $getPersonData['name_last'],
		'gender'				=> $getPersonData['sex'],
		'dateOfBirth'			=> $date_birth,
		'street'				=> $getPersonData['street_name'],
		'barangay'				=> $getPersonData['brgy_nr'],
		'city'					=> $getPersonData['mun_nr'],
		'custom_middle_initial'	=> $getPersonData['custom_middle_initial'],
		'suffix'				=> $getPersonData['suffix'],
		'name_maiden'			=> $getPersonData['name_maiden'],
		'name_others'			=> $getPersonData['name_others'],
		'place_birth'			=> $getPersonData['place_birth'],
		'blood_group'			=> $getPersonData['blood_group'],
		'addr_str'				=> $getPersonData['addr_str'],
		'addr_str_nr'			=> $getPersonData['addr_str_nr'],
		'addr_zip'				=> $getPersonData['addr_zip'],
		'addr_citytown_nr'		=> $getPersonData['addr_citytown_nr'],
		'addr_is_valid'			=> $getPersonData['addr_is_valid'],
		'street_name'			=> $getPersonData['street_name'],
		'citizenship'			=> $getPersonData['citizenship'],
		'occupation'			=> $getPersonData['occupation'],
		'employer'				=> $getPersonData['employer'],
		'phone_1_code'			=> $getPersonData['phone_1_code'],
		'phone_2_code'			=> $getPersonData['phone_2_code'],
		'phone_2_nr'			=> $getPersonData['phone_2_nr'],
		'cellphone_1_nr'		=> $getPersonData['cellphone_1_nr'],
		'cellphone_2_nr'		=> $getPersonData['cellphone_2_nr'],
		'fax'					=> $getPersonData['fax'],
		'email'					=> $getPersonData['email'],
		'civil_status'			=> $getPersonData['civil_status'],
		'religion'				=> $getPersonData['religion'],
		'mother_fname'			=> $getPersonData['mother_fname'],
		'mother_maidenname'		=> $getPersonData['mother_maidenname'],
		'mother_mname'			=> $getPersonData['mother_mname'],
		'mother_lname'			=> $getPersonData['mother_lname'],
		'father_fname'			=> $getPersonData['father_fname'],
		'spouse_name'			=> $getPersonData['spouse_name'],
		'guardian_name'			=> $getPersonData['guardian_name'],
		'contact_person'		=> $getPersonData['contact_person'],
		'contact_relation'		=> $getPersonData['contact_relation'],
		'death_date'			=> $getPersonData['death_date'],
		'death_encounter_nr'	=> $getPersonData['death_encounter_nr'],
		'death_cause'			=> $getPersonData['death_cause'],
		'death_cause_code'		=> $getPersonData['death_cause_code'],
		'date_update'			=> $getPersonData['date_update'],
		'status'				=> $getPersonData['status'],
		'history'				=> $getPersonData['history'],
		'senior_ID'				=> $getPersonData['senior_ID'],
		'veteran_ID'			=> $getPersonData['veteran_ID'],
		'is_indigent'			=> $getPersonData['is_indigent'],
		'DOH_ID'				=> $getPersonData['DOH_ID'],
		'age'					=> $getPersonData['age'],
		'name_search'			=> $getPersonData['name_search'],
		'create_id'				=> $HTTP_POST_VARS['user_id'],
		'patient_type'			=> $patient_type
	);
	try {
		$ehr = Ehr::instance();
		$patient = $ehr->postAddEncounterPerson_er($patientData);
	} catch (Exception $e) {
		error_log("Unable to save ER to EHR. Error: ".$e, 0);
	}

    $encService = new EncounterEmrService();
    $patientService = new PatientEmrService();
    try {
        if ($patientService->savePatient($pid)) {
            $encService->saveEncounter($pid, $encounter_nr);
        }
    } catch (Exception $exc) {
        //echo $exc->getTraceAsString();
    }
    #===============================

		/* Get last insert id */
		if($dbtype=='mysql'){
			$encounter_nr=$db->Insert_ID();
		}else{
			$encounter_nr=$encounter_obj->postgre_Insert_ID($dbtable,'encounter_nr',$db->Insert_ID());
		}
	  	if(empty($encounter_nr)) 
			$encounter_nr=$HTTP_POST_VARS['encounter_nr'];

		header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&origin=admit&target=entry&newdata=1&ERSave=1&ptype=$ptype");
	}else{
		echo $LDDbNoSave.'<p>'.$encounter_obj->getLastQuery();
	}
if (!$bSuccess)
    $db->FailTrans();
	$db->CompleteTrans();
?>