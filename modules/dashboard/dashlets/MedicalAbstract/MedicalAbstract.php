<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once $root_path . 'include/care_api_classes/class_acl.php';
require_once($root_path.'include/care_api_classes/class_encounter.php');

/**
 * Dashlet for Patient Radiology Findings
 */
class MedicalAbstract extends Dashlet
{

	protected static $name 	= 'Medical Abstract';
	protected static $icon 	= 'page_edit.png';
	protected static $group = '';

	public function init()
	{
		parent::init(Array(
			'contentHeight' => 'auto',
			'pageSize' => 5
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
			foreach ($data as $i=>$item) {
				if ($item['name'] == 'pageSize') {
					$pageSize = $item['value'];
				}
			}
			$this->preferences->set('pageSize', $pageSize);
			$this->setMode(DashletMode::getViewMode());
			$updateOk = $this->update();

			if (false !== $updateOk) {
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			} else {
				$response->alert('Error saving: '.$query);
			}
		} else {
			$response->extend( parent::processAction($action) );
		}

		return $response;
	}



	/**
	 * Processes a Render request and returns the output
	 *
	 */
	public function render($renderParams=null)
	{
		global $root_path;
		$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
		$file = $session->get('ActivePatientFile');

		if ( $renderParams['mode'] ) {
			$mode = $renderParams['mode'];
		} else {
			$mode = $this->getMode();
		}
		$acl = new Acl($_SESSION['sess_temp_userid']);
		$all = $acl->checkPermissionRaw('_a_0_all');
	
		$smarty = new smarty_care('common');
		
	
		if ($mode->is(DashletMode::VIEW_MODE)) {
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize')
			);
			$smarty->assign('settings', $preferencesSmarty);
			$smarty->assign('URL_APPEND', URL_APPEND);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/MedicalAbstract/templates/view.tpl');
		} elseif ($mode->is(DashletMode::EDIT_MODE)) {
			
			$dashletSmarty = array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize')
			);
			$smarty->assign('settings', $preferencesSmarty);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/MedicalAbstract/templates/config.tpl');
		} else {
			return parent::render($renderParams);
		}
	}

}
