<?php 
	define('IPBMIPD_enc', 13);
	define('IPBMOPD_enc', 14);
	define('IPBMIPD_enc_STR', '13');
	define('IPBMOPD_enc_STR', '14');
	define('IPBMdept_nr', 182);
	define('IPBMdept_nr_STR', '182');
	
	# updated by carriane 10/24/17
	$isIPBM = ($_GET['from']=='ipbm'||$_GET['ptype']=='ipbm'||$_GET['fromIPBM'])?1:0;

	$IPBMextend = $isIPBM?'&from=ipbm':'';

	#start IPBM UNIFIED ACCESS PERMISSION //Kemps 07/27/2017
	require_once $root_path . 'include/care_api_classes/class_acl.php';
	include_once($root_path.'include/inc_func_permission.php');
	$acl = new Acl($_SESSION['sess_temp_userid']);
	$allAccess = $acl->checkPermissionRaw(array('_a_0_all', 'System_Admin'));

	#IPBM permission : Registration
	$ipbmpatientmanage = $acl->checkPermissionRaw(array('_a_1_ipbmpatientmanage'));
	$ipbmpatientregister = $acl->checkPermissionRaw(array('_a_2_ipbmpatientregister'));
	$ipbmpatientupdate = $acl->checkPermissionRaw(array('_a_2_ipbmpatientupdate'));
	$ipbmpatientview = $acl->checkPermissionRaw(array('_a_2_ipbmpatientview'));
	#IPBM permission : Admission
	$ipbmadmission = $acl->checkPermissionRaw(array('_a_1_ipbmadmission'));
	$ipbmadmissiononly = $acl->checkPermissionRaw(array('_a_2_ipbmadmitonly'));
	$ipbmviewadmission = $acl->checkPermissionRaw(array('_a_2_ipbmviewadmission'));
	$ipbmupdateadmission = $acl->checkPermissionRaw(array('_a_2_ipbmupdateadmission'));
	$ipbmcanceladmission = $acl->checkPermissionRaw(array('_a_2_ipbmcanceladmission'));
	$ipbmviewipdcoversheet = $acl->checkPermissionRaw(array('_a_2_ipbmviewipdcoversheet'));
	$ipbmclinicalcharges = $acl->checkPermissionRaw(array('_a_1_ipbmclinicalcharges','_a_0_all', 'System_Admin'));
	$ipbmviewlabradresults = $acl->checkPermissionRaw(array('_a_1_ipbmviewlabradresults')); // added by carriane 04/17/19

	#IPBM permission : Consultation
	$ipbmconsultation = $acl->checkPermissionRaw(array('_a_1_ipbmconsultation'));
	$ipbmconsultationonly = $acl->checkPermissionRaw(array('_a_2_ipbmconsultonly'));
	$ipbmviewconsultation = $acl->checkPermissionRaw(array('_a_2_ipbmviewconsultation'));
	$ipbmupdateconsultation = $acl->checkPermissionRaw(array('_a_2_ipbmupdateconsultation'));
	$ipbmcancelconsultation = $acl->checkPermissionRaw(array('_a_2_ipbmcancelconsultation'));
	$ipbmviewopdcoversheet = $acl->checkPermissionRaw(array('_a_2_ipbmviewopdcoversheet'));

	#IPBM permission : Medical Records
	$ipbmmedicalrecords = $acl->checkPermissionRaw(array('_a_1_ipbmmedicalrecords'));
	$ipbmicdicpmaccess = $acl->checkPermissionRaw(array('_a_2_ipbmcanAccessICDICPM'));
	$ipbmcanceldeath = $acl->checkPermissionRaw(array('_a_2_ipbmcanceldeath'));
	$ipbmcanceldischarge = $acl->checkPermissionRaw(array('_a_2_ipbmcanceldischarge'));
	$ipbmviewdeathcert = $acl->checkPermissionRaw(array('_a_2_ipbmviewdeathcertificate'));
	$ipbmviewreceivedpatientschart = $acl->checkPermissionRaw(array('_a_2_ipbmviewreceivedpatientschart'));
	$ipbmmedicalcertificate = $acl->checkPermissionRaw(array('_a_2_ipbmmedcert'));
	$ipbmmedicalabstract = $acl->checkPermissionRaw(array('_a_2_ipbmmedicalabstract'));
	$ipbmconfinementcertificate = $acl->checkPermissionRaw(array('_a_2_ipbmconcert'));

	#start of USABLE ACCESS PERMISSIONS
	$ipbmcanAccessAdvanceSearch = $acl->checkPermissionRaw(array('_a_1_ipbmadvancesearch','_a_0_all', 'System_Admin'));


	$ipbmcanAccessReportLauncher = $acl->checkPermissionRaw(array('_a_1_ipbm_report_launcher','_a_0_all', 'System_Admin','_a_2_opd_daily_trans','_a_2_sAdmission_Logbook_For_Docs','_a_2_opd_summary','_a_2_report_discharges','_a_2_causes_confinement','_a_2_report_referral','_a_2_report_icd_encoded','_a_2_opd_icd10_statistics','_a_2_death','_a_2_ave_daily_census_admitted','_a_2_top_10','_a_2_psy_opd_rendered','_a_2_smoking','_a_2_leading_discharges','_a_2_Discharges_7days_Admission','_a_2_discharge_treatment','_a_2_leading_morbidity_oveall','_a_2_discharges_served','_a_2_PSY_Unregistered_Death_Certificate','_a_2_icd_encoded_stat','_a_2_ipd_demog','PSY_leading_mortality','_a_2_summary_patient','_a_2_causes_mortality','_a_2_notifiable','_a_2_PSY_bor'));

	
	$pIpbmTriage = $acl->checkPermissionRaw(getChildPermissions($ipbmPermissions,"_a_1_manageipbmpatientencounter"));

	$checkedParentOnlyRegistration = ($ipbmpatientmanage && !($ipbmpatientregister||$ipbmpatientupdate||$ipbmpatientview));
	$checkedParentOnlyIPD = ($ipbmadmission && !($ipbmviewadmission||$ipbmadmissiononly||$ipbmupdateadmission||$ipbmcanceladmission||$ipbmviewipdcoversheet));
	$checkedParentOnlyOPD = ($ipbmconsultation &&!($ipbmviewconsultation||$ipbmconsultationonly||$ipbmupdateconsultation||$ipbmcancelconsultation||$ipbmviewopdcoversheet));
	$checkedParentOnlyMedicalRecords = ($ipbmmedicalrecords && !($ipbmicdicpmaccess || $ipbmcanceldeath || $ipbmcanceldischarge || $ipbmviewdeathcert || $ipbmviewreceivedpatientschart || $ipbmmedicalcertificate || $ipbmmedicalabstract || $ipbmconfinementcertificate));

	$ipbmcanRegisterPatient = $checkedParentOnlyRegistration||$ipbmpatientregister||$allAccess;
	$ipbmcanUpdatePatient = $checkedParentOnlyRegistration||$ipbmpatientupdate||$allAccess;

	$ipbmcanAdmitOnly = $checkedParentOnlyIPD||$ipbmadmissiononly||$allAccess;
	$ipbmcanUpdateAdmit = $checkedParentOnlyIPD||$ipbmupdateadmission||$allAccess;
	$ipbmcanViewAdmit = $checkedParentOnlyIPD||$ipbmviewadmission||$allAccess;
	$ipbmcanCancelAdmit = $checkedParentOnlyIPD||$ipbmcanceladmission||$allAccess;
	$ipbmcanViewCoverSheet = $checkedParentOnlyIPD||$ipbmviewipdcoversheet||$allAccess;

	$ipbmcanConsultOnly = $checkedParentOnlyOPD||$allAccess||$ipbmconsultationonly;
	$ipbmcanUpdateConsult = $checkedParentOnlyOPD||$allAccess||$ipbmupdateconsultation;
	$ipbmcanViewConsult = $checkedParentOnlyOPD||$allAccess||$ipbmviewconsultation;
	$ipbmcanCancelConsult = $checkedParentOnlyOPD||$allAccess||$ipbmcancelconsultation;
	$ipbmcanViewCoverSheetOPD = $checkedParentOnlyOPD||$allAccess||$ipbmviewopdcoversheet;

	$ipbmcanAccessICDICPM = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmicdicpmaccess;
	$ipbmcanAccessCancelDeath = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmcanceldeath;
	$ipbmcanAccessCancelDischarge = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmcanceldischarge;
	$ipbmcanAccessDeathCertificate = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmviewdeathcert;
	$ipbmcanAccessReceivedPatientChart = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmviewreceivedpatientschart;
	$ipbmcanAccessMedicalCertificate = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmmedicalcertificate;
	$ipbmcanAccessMedicalAbstract = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmmedicalabstract;
	$ipbmcanAccessConfinementCertificate = $checkedParentOnlyMedicalRecords||$allAccess||$ipbmconfinementcertificate;
	$allow_ipbmMedocs_user=$ipbmcanAccessICDICPM;

	$ipbmcanViewPatient = $checkedParentOnlyRegistration||$ipbmpatientview||$allAccess||$ipbmcanUpdatePatient||$ipbmcanAdmitOnly||$ipbmcanUpdateAdmit||$ipbmcanViewAdmit||$ipbmcanCancelAdmit||$ipbmcanViewCoverSheet||$ipbmcanViewCharges||$ipbmcanConsultOnly||$ipbmcanUpdateConsult||$ipbmcanViewConsult||$ipbmcanCancelConsult||$ipbmcanCancelConsult||$ipbmcanViewCoverSheetOPD||$ipbmcanViewChargesOPD||$ipbmcanAccessMedicalCertificate||$ipbmcanAccessMedicalAbstract||$ipbmcanAccessConfinementCertificate||$ipbmcanAccessAdvanceSearch||$ipbmclinicalcharges;

	$ipbmcanAccessTriageConsultation = ($ipbmcanUpdateConsult||$ipbmcanViewConsult||$ipbmcanCancelConsult||$ipbmcanViewCoverSheetOPD||$ipbmclinicalcharges||$ipbmviewlabradresults || $pIpbmTriage);
	$ipbmcanAccessTriageAdmission = ($ipbmcanUpdateAdmit||$ipbmcanViewAdmit||$ipbmcanCancelAdmit||$ipbmcanViewCoverSheet||$ipbmclinicalcharges||$ipbmviewlabradresults || $pIpbmTriage);
	
	$medocs = $acl->checkPermissionRaw('_a_1_medocswrite');
	$allow_nurse_user = $acl->checkPermissionRaw('_a_2_labresultsnurse');
	$canViewClinicalCover = $acl->checkPermissionRaw(array('_a_1_nursingclinicalcoversheet','_a_0_all', 'System_Admin'));
	$allow_updateData = $acl->checkPermissionRaw(array('_a_1_updateData','_a_0_all', 'System_Admin'));
	$medocsCanViewIPBM = $acl->checkPermissionRaw('_a_1_canAccessIPBMinfo'); // added by carriane 09/04/18
	#end IPBM UNIFIED ACCESS PERMISSION //Kemps 07/27/2017
