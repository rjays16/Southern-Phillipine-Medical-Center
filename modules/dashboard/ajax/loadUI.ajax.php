<?php
define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once $root_path . 'include/care_api_classes/class_acl.php';
require_once($root_path . 'include/care_api_classes/class_personell.php'); //added by carriane 07/25/17

$smarty = new smarty_care('common');

$suffix = uniqid();
$smarty->assign('sRootPath', '../../');
$smarty->assign('suffix', $suffix);
define('IPBM_dept', '182');

require_once $root_path.'include/care_api_classes/dashboard/Dashboard.php';

// ui parameter determines what UI component/s to send back
switch( $_REQUEST['ui'] ) {


	// loads UI layout for the Create dashboard dialog
	case 'createDashboard':
		$smarty->assign('settings', Array(
			'title' => '',
		));
		$html = $smarty->fetch($root_path.'modules/dashboard/templates/ui/dashboard_create.tpl');
		break;


	// loads UI for the Dashboard settings dialog
	case 'settings':
		$id = $_REQUEST['dashboard'];

		// load the Dashboard, but do not load the Dashlets since we are only interested in retrieving the Dashboard info
		// this prevents unenecessary usage of computer resources as retrieving Dashlet information is rather expensive
		$dashboard = Dashboard::loadDashboard($id, $loadDashlets=false);

		if (false !== $dashboard)
		{
			$widths = $dashboard->getColumnWidths();
			foreach ($widths as $i=>$v)
			{
				if (!$widths[$i]) $widths[$i] = 0;
			}

			//$totalWidth = array_sum($widths);
			$smarty->assign('settings', Array(
				'title' => $dashboard->getTitle(),
				'columns' => $dashboard->getColumnCount(),
				'widths' => '['.implode(',', $widths).']',
				'totalWidth' => $totalWidth
			));

			// added by carriane 07/23/18
			$smarty->assign('position', $dashboard->getPosition());
			$smarty->assign('icon', $dashboard->getIcon());
			// end carriane

			$html = $smarty->fetch($root_path.'modules/dashboard/templates/ui/dashboard_settings.tpl');
		}
		else
		{
			// Show error message
			$html = 'Failed to load dashboard settings...';
		}

		break;


	// UI components for the Add dashlet dialog
	case 'addDashlet':

		# Added by Jarel 10/02/2013 For Radiology Findings Dashlet Permission
		$acl = new Acl($_SESSION['sess_temp_userid']);
		$showradiofindingsdashlet = $acl->checkPermissionRaw(array('_a_1_radiofindingsdashlet'));

		$query = "SELECT a.location_nr `dept`\n".
			"FROM care_users u\n".
				"INNER JOIN care_personell p ON p.nr=u.personell_nr\n".
				"INNER JOIN care_personell_assignment a ON a.personell_nr=p.nr\n".
				"LEFT JOIN care_department d ON d.nr=a.location_nr\n".
			"WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']);
		$info = $db->GetRow($query);
		$parentDept = $db->GetOne("SELECT parent_dept_nr FROM care_department WHERE nr=" . $db->qstr($info['dept']) );
		if(!$parentDept)
		{
			$dept = $info['dept'];
		}
		else
		{
			$dept = $parentDept;
		}
		# End Jarel

		#added by carriane 07/25/17
		$personnel = new Personell();
		$is_doctor = $personnel->isDoctor($_SESSION['sess_login_personell_nr']);

		$showpatientlist = $acl->checkPermissionRaw('_a_1_showalldept');
		$all = $acl->checkPermissionRaw('_a_0_all');
		$medicalAbstract = $acl->checkPermissionRaw('_a_1_medabstract');
		if($all){
			$medicalAbstract = 1;
		}

		if(($showpatientlist) && $all == ""){
			$smarty->assign('onlyPatientList', 1);
			$smarty->assign('is_doctor', $is_doctor);
		}
		$smarty->assign('IPBM_dept', IPBM_dept);
		$smarty->assign('medabstract', $medicalAbstract ? 1 : 0);
		#end carriane
		require_once $root_path.'include/care_api_classes/dashboard/DashletManager.php';
		$manager = DashletManager::getInstance();

		// get the list of Published Dashlets from the DashletManager
		$list = $manager->getDashlets();

		$categories = array();
		foreach ($list as $name=>$dashlet)
		{
			$category = $dashlet['category'];
			if (!$categories[$category])
			{
				$categories[$category] = array( 'name'=>$category, 'dashlets'=> array());
			}
			$categories[$category]['dashlets'][] = $dashlet;
		}

		$smarty->assign('categories', $categories);
		$smarty->assign('showradiofindingsdashlet', $showradiofindingsdashlet);
		$smarty->assign('dept', $dept);

	$html = $smarty->fetch($root_path.'modules/dashboard/templates/ui/dashlet_add.tpl');
		break;
}

echo $html;