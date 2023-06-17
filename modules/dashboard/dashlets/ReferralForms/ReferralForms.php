<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
include_once($root_path.'include/inc_date_format_functions.php'); 
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once $root_path . 'include/care_api_classes/class_acl.php';
include_once($root_path.'include/care_api_classes/class_person.php'); 
require_once($root_path . 'include/care_api_classes/class_personell.php'); 
require_once($root_path.'include/care_api_classes/class_cert_death.php');
define('IPBM_DEPT', 182);

class Referral_Forms extends Dashlet {

	protected static $name 	= 'Referral Forms';
	protected static $icon 	= 'page_edit.png';
	protected static $group = 'PatientFile';
	protected $view_rootpath = '../../';

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

			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$file = $session->get('ActivePatientFile');
			
			$query = "SELECT p.pid, p.date_birth, e.encounter_nr, e.current_dept_nr, fn_get_person_name(p.pid) `fullname`, p.sex, e.current_att_dr_nr,\n".
					"e.official_receipt_nr `or`, fn_get_complete_address(p.pid) `address`, fn_get_age(DATE(NOW()), date_birth) `age`, IF(death_date <> '0000-00-00', fn_get_age_days(death_date, date_birth), 0) `death_age`,\n".
					"e.chief_complaint, e.smoker_history, e.drinker_history, e.is_discharged, e.encounter_type, e.er_location, e.er_location_lobby\n".
				"FROM care_encounter e\n".
					"INNER JOIN care_person p ON p.pid=e.pid\n".
				"WHERE e.encounter_nr=".$db->qstr($file);

			$row = $db->GetRow($query);

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);

			$rad_dept = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr =".$db->qstr($personell_nr);
			$location_nr = $db->GetOne($rad_dept);
			
			$patientSmarty = $row ? Array(
				'pid' => $row['pid'],
				'encounter' => $row['encounter_nr'],
				'current_dept_nr' => $row['current_dept_nr'],
				'fullname' => $row['fullname'],
				'address' => $row['address'],
				'complaint' => $row['chief_complaint'],
				'date_birth' => $row['date_birth'],
				'age' => $row['age'],
				'death_age'=> $row['death_age'],
				'or' => $row['or'],
				'doc_nr' => $personell_nr,
				'is_discharged' => $row['is_discharged'] 
			) : array();


			if (strtoupper($row['sex']) == 'M')
				$patientSmarty['gender'] = 'Male';
			elseif (strtoupper($row['sex']) == 'F')
				$patientSmarty['gender'] = 'Female';
			
			$personnel = new Personell();
			$is_doctor = $personnel->isDoctor($_SESSION['sess_login_personell_nr']);
			$isactive = "";
			if($is_doctor){
				if(($file!==NULL && $patientSmarty['current_dept_nr'] == IPBM_DEPT)) { 
					$smarty->assign('disable', '');
					$disable = '';
					$show = "display:''";
					$isactive = "btn-active";
				}else {
					$smarty->assign('disable', 'disabled="disabled"');
					$disable = 'disabled="disabled"';
					$show = "display:none";
					$isactive = "btn-inactive";
				}
				
			}else{
				$smarty->assign('disable', 'disabled="disabled"');
				$disable = 'disabled="disabled"';
				$show = "display:none";
				$isactive = "btn-inactive";
			}
			$smarty->assign('pat', $patientSmarty);

			$show = "display:''";
			

			$smarty->assign('encounter_nr', '<input type="hidden" id="encounter_nr" name="encounter_nr" value="'.$file.'"/>');
			$smarty->assign('URL_APPEND', URL_APPEND);

			$smarty->assign('btnConsultationReferral', '<button class="btn-default btn-inverse '.$isactive.'" onclick="viewConsultationReferral();" '.$disable.'>Consultation And Referral Sheet</button>');
			$smarty->assign('btnPatientReferral', '<button class="btn-default btn-inverse '.$isactive.'" onclick="veiwPatientReferral();" '.$disable.'>Patient Referral Form</button>');
			$smarty->assign('btnOccupationalReferral', '<button class="btn-default btn-inverse '.$isactive.'" style="height: 35px;" onclick="veiwOccuTherapyReferral();" '.$disable.'>Occupational Therapy Referral<br>Form</button>');
			$smarty->assign('btnPsychologicalReferral', '<button class="btn-default btn-inverse '.$isactive.'" style="height: 35px;" onclick="veiwPsychologicalServReferral();" '.$disable.'>Psychological Service Referral<br>Form</button>');
			
			$smarty->assign('encoder', urlencode($_SESSION['sess_login_username']));
			$smarty->assign('view_rootpath', $this->view_rootpath);
			
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/ReferralForms/templates/View.tpl');
		}

		else
		{
			return parent::render($renderParams);
		}
	}

	
	private function cleanInput($input) {
		return utf8_decode(utf8_decode(utf8_encode($input)));
	}



}


