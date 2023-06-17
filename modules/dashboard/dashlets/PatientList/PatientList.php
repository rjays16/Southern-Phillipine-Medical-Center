<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* Dashlet for Prescriptions
*/
class PatientList extends Dashlet {

	protected static $name 	= 'Patient List';
	protected static $icon 	= 'patient.png';
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
			'pageSize'			=> 5,
			'viewType'			=> 'list',
			'filter'				=> 'department'
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
		if ($action->is('save')) {
			$data = (array) $action->getParameter('data');
			foreach ($data as $i=>$item)
			{
				switch ($item['name'])
				{
					case 'pageSize':
						$pageSize = $item['value'];
						break;
					case 'viewType':
						$viewType = $item['value'];
						break;
					case 'filter':
						$filter = $item['value'];
						break;
				}
			}

			$this->preferences->set('pageSize', $pageSize);
			$this->preferences->set('viewType', $viewType);
			$this->preferences->set('filter', $filter);

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
		} elseif ($action->is('openFile')) {
			$file = $action->getParameter('file');
			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$session->set('ActivePatientFile', $file);
			$response->classRefresh('Referral_Forms');
			$response->execute("$('PatientList-".$this->getId()."').list.reload()");
			$response->classRefresh('PatientInformation');
			$response->classRefresh('PatientHistory');
			$response->classRefresh('PatientLabResults');
			$response->classRefresh('PatientRadioResults');
			$response->classRefresh('PatientRadioFindings');
			$response->classRefresh('DoctorsNotes');
			$response->classRefresh('RxWriter');
			$response->classRefresh('PatientRadioFindingsDashlet');
			$response->classRefresh('PatientMedicalCert');
			$response->classRefresh('MedicalAbstract');
			$response->classRefresh('CounselingNotes');

			
		} else {
			$response->extend( parent::processAction($action) );
		}

		return $response;
	}



	/**
	* Processes a Render request and returns the output
	*
	*/
	public function render($renderParams=null) {
		global $root_path;

		# Added by Jarel 10/02/2013 For Radiology Findings Dashet Permission
		require_once $root_path . 'include/care_api_classes/class_acl.php';
		$acl = new Acl($_SESSION['sess_temp_userid']);
		#$showradiofindingsdashlet = $acl->checkPermissionRaw(array('_a_1_radiofindingsdashlet')); commented by art 07/28/2014
		$showAllDept = $acl->checkPermissionRaw(array('_a_1_showalldept')); #added by art 07/28/2014
		# End Jarel

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
			$smarty = new smarty_care('common');
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);

			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize'),
				'viewType' => $this->preferences->get('viewType'),
				'filter' => $this->preferences->get('filter')
			);
			$smarty->assign('settings', $preferencesSmarty);


			$viewType = $this->preferences->get('viewType');
			if ($viewType == 'item')
				return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientList/templates/ItemView.tpl');
			else
				return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientList/templates/ListView.tpl');
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize'),
				'viewType' => $this->preferences->get('viewType'),
				'filter' => $this->preferences->get('filter'),
				#'isShowAllDept'=> $showradiofindingsdashlet,#commented by art 07/28/2014
				'isShowAllDept'=> $showAllDept, #added by art 07/28/2014
			);
			$smarty->assign('settings', $preferencesSmarty);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientList/templates/Config.tpl');
		}
		else
		{
			return parent::render($rendeParams);
		}
	}

}
