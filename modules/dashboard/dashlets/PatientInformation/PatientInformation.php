<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
include_once($root_path.'include/inc_date_format_functions.php'); // added by carriane 01/29/18
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once $root_path . 'include/care_api_classes/class_acl.php';
include_once($root_path.'include/care_api_classes/class_person.php'); // added by carriane 01/29/18
require_once($root_path . 'include/care_api_classes/class_personell.php'); //added by carriane 07/25/17
require_once($root_path.'include/care_api_classes/class_cert_death.php'); //added rnel / rebranched carriane 01-19-18
require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');

/**
* Dashlet for Prescriptions
*/
class PatientInformation extends Dashlet {

	const EMeds_Easter = '267';	
	const EMeds = '243';
	const his_request = '1';	
	protected static $name 	= 'Patient Information';
	protected static $icon 	= 'info.png';
	protected static $group = 'PatientFile';
	private $IPBM_DEPT = '182';
	private $IPBM_IPD = '13';
	private $IPBM_OPD = '14';
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
			'contentHeight' => 'auto'
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
		if ($action->is('save'))
		{
			$data = (array) $action->getParameter('data');
			foreach ($data as $i=>$item)
			{
				if ($item['name'] == 'pageSize')
				{
					$pageSize = $item['value'];
				}
			}
			$this->preferences->set('pageSize', $pageSize);
			$this->setMode(DashletMode::getViewMode());
			$updateOk = $this->update();

			if (false !== $updateOk)
			{
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}
			else
			{
				$response->alert('Error saving: '.$query);
			}
		}
		elseif($action->is('setDoctors'))
		{
			global $db;
			$response = new DashletResponse;

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$userid = $_SESSION["sess_temp_userid"];
			$encounter_nr = $action->getParameter('data');
			$or = $action->getParameter('or');

			$index="encounter_nr, or_no, doctor_nr, create_id, create_time,history";
			$values ="'$encounter_nr','$or','$personell_nr','$userid',NOW(),CONCAT('Create: ',NOW(),' [$userid]\\n')";
 
			$sql2 = "INSERT INTO seg_doctors_co_manage ($index) VALUES ($values)";
			if($db->Execute($sql2)){
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}else{ 
				$response->alert('Error saving: '.$sql2);
			}
		}
		elseif($action->is('unsetDoctors'))
		{
			global $db;
			$response = new DashletResponse;

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$userid = $_SESSION["sess_temp_userid"];
			$encounter_nr = $action->getParameter('data');

			$sql2 = "UPDATE seg_doctors_co_manage SET
					is_deleted = 1 ,
					or_no = '',
					modify_id = '$userid',
					history = CONCAT(history,'UPDATE: ',NOW(),' [$userid]\\n')
					WHERE is_deleted=0 AND doctor_nr='$personell_nr' AND encounter_nr=".$db->qstr($encounter_nr);

			if($db->Execute($sql2)){
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}else{ 
				$response->alert('Error saving: '.$sql2);
			}
		}
		elseif($action->is('referPatient'))
		{
			global $db;
			$enc_obj=new Encounter();
			$response = new DashletResponse;

			$pid = $action->getParameter('pid');
			$enc =	$action->getParameter('enc');
			$sql = "SELECT referral_nr FROM seg_referral WHERE encounter_nr='$enc' ORDER BY create_time DESC";
			$res = $db->Execute($sql);
			if($res && $row = $res->FetchRow()){
				$referral_nr = $db->GetOne($sql)+1;
			}else{
				$referral_nr = $pid.'1';
			}	
			
			$ReferArray['referral_nr'] = $referral_nr;
			$ReferArray['encounter_nr'] = $enc;
			$ReferArray['referrer_dr'] = $action->getParameter('doc_nr');
			$ReferArray['referrer_dept'] = $action->getParameter('dept');
			$ReferArray['reason_referral_nr'] = $action->getParameter('reason');
			$ReferArray['userid'] = $_SESSION["sess_temp_userid"];
			$sql="Select reason from seg_referral_reason where id=".$ReferArray['reason_referral_nr'];
			$getReason = $db->GetOne($sql);
			$ReferArray['reason_referral'] = $getReason;
			
		
			$ok = $enc_obj->saveReferral($ReferArray);
			$ehr = Ehr::instance();
			$patient = $ehr->hci_postHciReferral($ReferArray);
			$asd = $ehr->getResponseData();
			$EHRstatus = $patient->status;
			// var_dump($asd);
				// $response->alert(print_r($asd, true));
				// return $response;
			
			
			if(!($EHRstatus==1)){
				$hs = $action->getParameter('hs');
				if (isset($hs) && $hs == self::his_request) {
					// no action
				}else{
					if(trim(print_r($patient, true)) != ''){
						$response->alert(print_r($patient, true));
						return $response;
					}
				}
				
			}

			if($ok){
				$response->alert('Successfully Saved The Referral');
				
				$response->call("Dashboard.dashlets.refresh", $this->getId());
				$response->call("refreshReferral");
			}else{ 
				$response->alert('Referral Not Saved');
			}
		}
		elseif($action->is('undoReferPatient'))
		{
			global $db;
			$response = new DashletResponse;
			$ref = $action->getParameter('ref');
			
			$sql = "DELETE FROM seg_referral WHERE referral_nr='$ref'";
			
			if($db->Execute($sql)){
				$response->call("Dashboard.dashlets.refresh", $this->getId());
				$response->call("refreshReferral");
			}else{ 
				$response->alert('Error saving: '.$sql);
			}
		}
		elseif($action->is('updateSmokerDrinkerData'))
		{
			global $db;
			$enc_obj=new Encounter();
			$response = new DashletResponse;

			$userid = $_SESSION["sess_temp_userid"];
			$encounter_nr = $action->getParameter('enc');
			$smoker = $action->getParameter('smoker');
			$drinker = $action->getParameter('drinker');

			$sql2 = "UPDATE care_encounter SET
					smoker_history = '$smoker',
					drinker_history = '$drinker',
					history = CONCAT('UPDATE: ',NOW(),' [$userid]\\n')
					WHERE encounter_nr=".$db->qstr($encounter_nr);

			if($db->Execute($sql2)){
				$response->alert('Successfully Saved');
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}else{ 
				$response->alert('Error saving: '.$sql2);
			}
		}
		else {
			$response->extend( parent::processAction($action) );
		}

		return $response;
	}



	/**
	* Processes a Render request and returns the output
	*
	*/
	public function render($renderParams=null) {
		global $root_path, $db;

		$mode = $this->getMode();
		$dept_obj=new Department;

		if ($mode->is(DashletMode::VIEW_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);


			$acl = new Acl($_SESSION['sess_temp_userid']);
			$radiopermission = $acl->checkPermissionRaw(array('_a_1_radiofindingsdashlet'));

			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$file = $session->get('ActivePatientFile');

			$query = "SELECT p.pid, p.date_birth, e.encounter_nr, fn_get_person_name(p.pid) `fullname`, p.sex, e.current_att_dr_nr,\n".
					"e.official_receipt_nr `or`, fn_get_complete_address(p.pid) `address`, fn_get_age(DATE(NOW()), date_birth) `age`, IF(death_date <> '0000-00-00', fn_get_age_days(death_date, date_birth), 0) `death_age`,\n".
					"e.chief_complaint, e.smoker_history, e.drinker_history, e.is_discharged, e.encounter_type, e.er_location, e.er_location_lobby,cw.accomodation_type\n".
				"FROM care_encounter e\n".
					"INNER JOIN care_person p ON p.pid=e.pid
					LEFT JOIN care_ward cw ON cw.nr=e.current_ward_nr\n".
				"WHERE e.encounter_nr=".$db->qstr($file);

			$row = $db->GetRow($query);

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);

			// added by: syboy 08/09/2015
			// get assign dept of personnel
			$rad_dept = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr =".$db->qstr($personell_nr);
			$location_nr = $db->GetOne($rad_dept);
			// end

			//Added by Gervie 03/12/2016
			//Get ER Location of the patient
			if($row['encounter_type'] == 1) {
				$sql_loc = "SELECT el.area_location FROM seg_er_location el WHERE el.location_id = {$row['er_location']}";
				$er_location = $db->GetOne($sql_loc);

				if($er_location != '') {
					$sql_lobby = "SELECT eb.lobby_name FROM seg_er_lobby eb WHERE eb.lobby_id = {$row['er_location_lobby']}";
					$er_lobby = $db->GetOne($sql_lobby);

					if($er_lobby != '') {
						$er_location_lobby = "ER - " . $er_location . " (" . $er_lobby . ")";
					}
					else {
						$er_location_lobby = "ER - " . $er_location;
					}
				}
				else{
					$er_location_lobby = 'EMERGENCY ROOM';
				}

			}

			

			$patientSmarty = Array(
				'pid' => $row['pid'],
				'encounter' => $row['encounter_nr'],
				'fullname' => $row['fullname'],
				'address' => $row['address'],
				'complaint' => $row['chief_complaint'],
				'date_birth' => $row['date_birth'],
				'age' => $row['age'],
				'death_age'=> $row['death_age'],
				'or' => $row['or'],
				'doc_nr' => $personell_nr,
				'is_discharged' => $row['is_discharged'], // edited by : syboy 06/13/2015
				'encounter_type' => $row['encounter_type'],
				'accomodation' => $row['accomodation_type']
			);

			if (strtoupper($row['sex']) == 'M')
				$patientSmarty['gender'] = 'Male';
			elseif (strtoupper($row['sex']) == 'F')
				$patientSmarty['gender'] = 'Female';

			if($row['smoker_history']=='yes'){
				$smokerYes = 'checked';
				$patientSmarty['smoker'] = 'Yes';
			}elseif($row['smoker_history']=='no'){
				$smokerNo = 'checked';
				$patientSmarty['smoker'] = 'No';
			}elseif($row['smoker_history']=='na'){
				$smokerNa = 'checked';
				$patientSmarty['smoker'] = 'N/A';
			}

			if($row['drinker_history']=='yes'){
				$drinkerYes = 'checked';
				$patientSmarty['drinker'] = 'Yes';
			}elseif($row['drinker_history']=='no'){
				$drinkerNo = 'checked';
				$patientSmarty['drinker'] = 'No';
			}elseif($row['drinker_history']=='na'){
				$drinkerNa = 'checked';
				$patientSmarty['drinker'] = 'N/A';
			}

			//Added by Gervie 03/12/2016
			if($row['encounter_type'] == 1) {
				$patientSmarty['er_location'] = $er_location_lobby;
			}

			//added by carriane 07/25/17
			$personnel = new Personell();
			$is_doctor = $personnel->isDoctor($_SESSION['sess_login_personell_nr']);
			$user_dept = $personnel->getDepartmentGroupId($_SESSION['sess_login_personell_nr']);
			$readWritePermission = $acl->checkPermissionRaw('_a_1_doctorsdutyplanwrite');

			//updated by Carriane 07/25/17
			if(!$is_doctor && $readWritePermission == ""){
				$smarty->assign('disable', 'disabled="disabled"');
				$disable = 'disabled="disabled"';
				$show = "display:none";
			}else{
				if(($file!==NULL AND $patientSmarty['is_discharged'] == 0)) { // edited by : syboy 06/13/2015
					$smarty->assign('disable', '');
					$disable = '';
					$show = "display:''";
				}else {
					$smarty->assign('disable', 'disabled="disabled"');
					$disable = 'disabled="disabled"';
					$show = "display:none";
				}
			}
			$all = $acl->checkPermissionRaw('_a_0_all');
			$medicalAbstract = $acl->checkPermissionRaw('_a_1_medabstract');
			if($all){
				$medicalAbstract = 1;
			}

			if ($medicalAbstract) {
				if(($file!==NULL AND $patientSmarty['is_discharged'] == 0)) {
					$smarty->assign('disableabst', '');
				}else {
					$smarty->assign('disableabst', 'disabled="disabled"');

				}
			
			}else {
				$smarty->assign('disableabst', 'disabled="disabled"');
				
			}

			$showIpbmMedcert = '';
			$is_ipbm = 0;
			if($location_nr == $this->IPBM_DEPT || $all){
				if($row['encounter_type'] != $this->IPBM_OPD){
					$showIpbmMedcert = "style='display:none;'";
				}else{
					$is_ipbm = 1;
				}
			}else{				
				$showIpbmMedcert = "style='display:none;'";
			}

			$smarty->assign('pat', $patientSmarty);

			// added by: syboy 08/09/2015
			if ($location_nr == 158) {
				$smarty->assign('disableRad', 'disabled="disabled"');
				$disableRad = 'disabled="disabled"';
			}else {
				$smarty->assign('disableRad', '');
				$disableRad = '';
			}
			// end

			//Added by EJ 12/04/2014
			if ($row['encounter_type'] != 1) {
				$smarty->assign('show_er', 'style="display:none"');
			}
			else {
				$smarty->assign('show_er', 'style="display:"');
			}

			if($row['encounter_type']==2){
				$showMed = "display:''";
			}else{
				$showMed = "display:none";
			}
		
			# Added by Jeff 05/16/2018 for IPBM discharge slip UI.
			if ($row['encounter_type'] == $this->IPBM_OPD || $row['encounter_type'] == $this->IPBM_IPD) {

				$smarty->assign('show_ipbm', 'style="display:"');
			}
			else {
				$smarty->assign('show_ipbm', 'style="display:none"');
			}
			# END Jeff ---
			
			if($user_dept == $this->IPBM_DEPT || $all){
				if ($row['encounter_type'] == $this->IPBM_OPD || $row['encounter_type'] == $this->IPBM_IPD) {
					$smarty->assign('hide_abstract', '');
				}else{
					$smarty->assign('hide_abstract', 'style="display:none !important;"');
				}
			}else{
				$smarty->assign('hide_abstract', 'style="display:none !important;"');
			}

			#commented by VAS 09-18-2014
			/*if($row['encounter_type']==2){
				$show1 = "display:''";
			}else{
				$show =	 "display:none";
				$show1 = "display:none";
			}*/
			#co-manage open for all patient type
			$show1 = "display:''";


			if($radiopermission && !$all)
			{
				$smarty->assign('show', 'style="display:none"');
				$show1 = "display:none";
				$show2 = "display:none";
			}

			$smarty->assign('encounterNr', '<input type="hidden" id="encounterNr" name="encounterNr" value="'.$file.'"/>');
			$smarty->assign('URL_APPEND', URL_APPEND);
			
			$sql1 = "SELECT * FROM seg_doctors_co_manage WHERE is_deleted=0 AND doctor_nr='$personell_nr' AND encounter_nr=".$db->qstr($file);
			$result = $db->Execute($sql1);	
			if ($result){
			    if($rows = $result->FetchRow()){
				    if(date("m/d/Y",strtotime($rows['create_time']))==date("m/d/Y")){
				    	$smarty->assign('btn_untagDoctor','<button class="button" '.$disable.' style="'.$show1.'" onclick="PatientHistory_unsetDoctors();return false;"><img src="../../gui/img/common/default/forums.gif"/>Undo Tag Patient</button>');
				    }
			    }else{
			    	$smarty->assign('btn_tagDoctor','<button class="button" '.$disable.' style="'.$show1.'" onclick="PatientHistory_setDoctors();return false;"><img src="../../gui/img/common/default/forums.gif"/>Tag My Patient</button>');
			    }
			}

			$sql2 = "SELECT fn_get_personell_name(doctor_nr) `doctor` FROM seg_doctors_co_manage WHERE is_deleted=0 AND encounter_nr=".$db->qstr($file);
			
			$result2 = $db->Execute($sql2);
			if($result2){
				$doctors ='';
				while($rows2 = $result2->FetchRow()){
					$doctors .="<li><span>Dr. ".$rows2['doctor']."</span></li>";
        		}
			}
			if($doctors!=''){
				$doctor_input = mb_strtoupper($doctors);
			}else{
				$doctor_input = "<li><span>Don\'t have Doctor yet.</span></li>";
			}
		
			$attributes .= 'onmouseover="return overlib(\''.$doctor_input.'\', CAPTION,\'Doctors\', BORDER,0,TEXTPADDING,5, TEXTFONTCLASS,\'oltxt\', CAPTIONFONTCLASS,\'olcap\',WIDTH,300, FGCLASS,\'olfgPopup\');"'; 
        	$attributes .= 'onMouseout ="return nd();"';
			
			$smarty->assign('doctors','<span style="font:bold 12px Verdana; '.$show.'" '.$attributes.'><img class="link" '.$attributes.' src="../../gui/img/common/default/forums.gif">Doctors</span>');
			$dept = array();
			$sqlReferral = "SELECT * FROM seg_referral WHERE encounter_nr='$file' AND referrer_dr='$personell_nr'";
			$res = $db->Execute($sqlReferral);
			while($res && $row = $res->FetchRow()){
				$dept[] = $row['referrer_dept'];
			}
			
			if($dept)
				$smarty->assign('refer','<button class="button" '.$disable.' '.$disableRad.' onclick="showHistoryReferral('.$personell_nr.');return false;"><img src="../../gui/img/common/default/patient.png"/>Show Referral History</button>');
			else
				$smarty->assign('refer','<button class="button" '.$disable.' '.$disableRad.'  onclick="PatientHistory_referPatient('.$personell_nr.');return false;"><img src="../../gui/img/common/default/patient.png"/>Refer Patient</button>');
			
		    /*
		     * $rCond = "(nr IN ('". self::EMeds_Easter . "','". self::EMeds . "')) OR";
		     * $result = $dept_obj->getAllOPDMedicalObject(0,'',$rCond);
		     * $result = $dept_obj->getAllOPDMedicalObject(0);
		    */

            /* 11/23/2019 Effectivity */
            $result_referral = $dept_obj->getReferralDepartment($file);

			$deptlist = "<option value='' selected>-Select a Department-</option>";
			while($result_referral && $row = $result_referral->FetchRow()){
				if (!in_array($row["nr"], $dept)) {
					$deptlist = $deptlist ."<option value='" .$row["nr"]. "' >" .$row["name_formal"]. "</option>";
				}
			}

			/* added rnel  if patient flag as dead on certain encounter.  add func. via death cert.*/
			/* rebranched carriane 01-19-18 */

			$encodeDeathDiagnose = $acl->checkPermissionRaw(array('_a_1_doctorsdeathdiagnoseencode'));
					
			$deathSql = "SELECT * FROM care_person WHERE pid = {$db->qstr($patientSmarty['pid'])} AND death_encounter_nr = {$db->qstr($patientSmarty['encounter'])} AND death_date <> '0000-00-00' AND death_time <> '00:00:00'";
			$resDeath = $db->Execute($deathSql);
			// var_dump($resDeath->RecordCount()); die;
			$disabled = 'disabled="disabled"';

			if($resDeath->RecordCount() > 0) {
				if(!$encodeDeathDiagnose) {
					$smarty->assign('sDeathButton', 
									'<button class="button" '.$disabled.' style="position:relative" onclick="PatientInformation_deathCertificate();return false;"><img src="../../gui/img/common/default/blackcross_sm.gif"/>Death Certificate</button>'
								);
				} else {
					$smarty->assign('sDeathButton', 
								'<button class="button" style="background:#ff0000;position:relative" onclick="PatientInformation_deathCertificate();return false;"><img src="../../gui/img/common/default/blackcross_sm.gif"/>Death Certificate</button>'
							);
				}		
				
			} else {
				$smarty->assign('sDeathButton', 
									'<button class="button" style="display:none;" onclick="PatientInformation_deathCertificate();return false;"><img src="../../gui/img/common/default/blackcross_sm.gif"/>Death Certificate</button>'
								);
			}

			/* end rnel */
	
	
			/*$sql = "SELECT * FROM seg_referral_from";
			$res = $db->Execute($sql);
			$hospital = "<option value=0>-Select a Hospital-</option>";
			while($res && $row = $res->FetchRow()){
				$hospital = $hospital ."<option value='" .$row["id"]. "' >" .$row["referral"]. "</option>";
			}*/

			$sqlReason = "SELECT * FROM seg_referral_reason ORDER BY reason";
			$res = $db->Execute($sqlReason);
			$reason = "<option value='' selected>-Select a Reason-</option>";
			while($res && $row = $res->FetchRow()){
				$reason = $reason ."<option value='" .$row["id"]. "' >" .$row["reason"]. "</option>";
			}

			/*$smarty->assign('sReferType', '<select class="segInput" name="ReferType">
											<option value="0">Department</option>
											<option value="1">Hospital-</option>
											</select>');*/
			
			$smarty->assign('sDept', '<select  name="department" id="department" class="segInput" onchange="assignValue(1,this.value);">'.$deptlist.'</select><input name="dept" id="dept" type="hidden">');
			//$smarty->assign("sHospital", '<select class="segInput" name="hospital" style="display:none">'.$hospital.'</select>');
			$smarty->assign("sReason", '<select class="segInput" name="referral_reason" id="referral_reason" onchange="assignValue(2,this.value);" >'.$reason.'</select><input name="reason" id="reason" type="hidden">');
			
			$smarty->assign('updateSmokerDrinkerData','<button class="button" '.$disable.' '.$disableRad.' onclick="PatientHistory_updateSmokerDrinkerData();return false;"><img src="../../gui/img/common/default/page_edit.png"/>Update Smoker/Drinker Data</button>');
			//Added by borj 2014-20-01
			$smarty->assign('medcert','<button class="button" '.$disable.' '.$disableRad.' onclick="viewCertMed('.$patientSmarty['pid'].'); return false;"><img src="../../gui/img/common/default/page_edit.png"/>Medical Certificate</button>');

			//Added by borj 2014-20-01
			//Update by borj 1/19/2015
			  $smarty->assign('medcert','<button class="button" '.$disable.' '.$disableRad.' style="'.$showMed.'" onclick="viewCertMed('.$patientSmarty['pid'].','.$patientSmarty['encounter'].' ); return false;"><img src="../../gui/img/common/default/page_edit.png"/>Medical Certificate</button>');

			$smarty->assign('psymedcert','<button class="button" '.$showIpbmMedcert.' '.$disable.' onclick="viewCertMed('.$patientSmarty['pid'].','.$patientSmarty['encounter'].','.$is_ipbm.' ); return false;"><img src="../../gui/img/common/default/page_edit.png"/>Medical Certificate</button>');
			  
			//end
			$smarty->assign('sSmoker','<td class="adm_input" colspan="2">
															<input id="smoker_yes" type="radio" '.$smokerYes.' value="yes" name="smoker">
															YES
															<input id="smoker_no" type="radio" '.$smokerNo.' value="no" name="smoker">
															NO
															<input id="smoker_na" type="radio" '.$smokerNa.' value="na" name="smoker">
															N/A
														</td>');
			$smarty->assign('sDrinker','<td class="adm_input" colspan="2">
											<input id="drinker_yes" type="radio" '.$drinkerYes.' value="yes" name="drinker">
											YES
											<input id="drinker_no" type="radio" '.$drinkerNo.' value="no" name="drinker">
											NO
											<input id="drinker_na" type="radio" '.$drinkerNa.' value="na" name="drinker">
											N/A
										</td>');

			// added rnel
			/* rebranched carriane 01-19-18 */

			$smarty->assign('isinfant', '<input name="isinfant" id="isinfant" value="0" type="hidden">');

			// end rnel

			if(substr($patientSmarty['death_age'], 0, 2) < 8)	
				$smarty->assign('isinfant', '<input name="isinfant" id="isinfant" value="1" type="hidden">');
			
			
			// return substr($patientSmarty['death_age'], 0, 2);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientInformation/templates/View.tpl');
		}
//		elseif ($mode->is(DashletMode::EDIT_MODE))
//		{
//			$smarty = new smarty_care('common');
//			$dashletSmarty = array(
//				'id' => $this->getId()
//			);
//			$smarty->assign('dashlet', $dashletSmarty);
//			$preferencesSmarty = Array(
//				'pageSize' => $this->preferences->get('pageSize')
//			);
//			$smarty->assign('settings', $preferencesSmarty);
//			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientList/templates/config.tpl');
//		}
//		else
//		{
//			return 'Mode not supported';
//		}
		else
		{
			return parent::render($renderParams);
		}
	}

	/**
	 *
	 * added by rnel / rebranched carriane 01-19-18
	   param pid 
	   return string
	 *
	 */
	

	private function getPatientType($pid, $date_birth) {
		global $db;
		$strAge = '';
		$adult = 'adult';
		$infant = 'infant';
		$date_format = "MM/dd/yyyy";

		$person_obj = new Person($pid);
		$sBdayBuffer = @formatDate2Local($date_birth,$date_format);
		$age = $person_obj->getAge($sBdayBuffer);
		
		if($age <= 7) {
			return $infant;
		} else {
			return $adult;
		}
	}

	#added rnel for clean slate.
	/* rebranched carriane 01-19-18 */
	private function cleanInput($input) {
		return utf8_decode(utf8_decode(utf8_encode($input)));
	}

	/* end rnel */

}


