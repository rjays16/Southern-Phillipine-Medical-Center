<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_acl.php');#added by art 03/16/2015
/*** CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','radio.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

$thisfile=basename(__FILE__);
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.$thisfile;

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Create a helper smarty object without reinitializing the GUI
$smarty2 = new smarty_care('common', FALSE);

# Added for the common header top block

$smarty->assign('sToolbarTitle', "Dialysis");

# Added for the common header top block
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDDialysis')");

$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title',"Dialysis");

# Append javascript to JavaScript block

$smarty->append('JavaScript',$sTemp);

#$url_search_emp = $root_path .'modules/personell_admin/personell_search.php?from=medocs&department=Dialysis'; # Added by: syboy 12/18/2015 : meow

#added by art 03/16/2015
$objAcl = new Acl($_SESSION['sess_temp_userid']);
$NewRequest_permission = $objAcl->checkPermissionRaw('_a_1_dialysiscreaterequest') ? 1 : 0;
$PatientList_permission = $objAcl->checkPermissionRaw('_a_1_dialysislistofpatients') ? 1 : 0;
$Transmittal_permission = $objAcl->checkPermissionRaw('_a_1_dialysistransmittal') ? 1 : 0;
$Reports_permission = $objAcl->checkPermissionRaw('_a_1_dialysisreports') ? 1 : 0;
//$_a_1_searchempdependent = $objAcl->checkPermissionRaw('_a_1_searchempdependent') ? 1 : 0; # added by: syboy 03/10/2016 : meow
#end art
#note pass '' if no permission
$aMenu=array(

	"Service Request"=>array(

		'sNewRequest' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-request-new.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=newrequest",
				'label'=>"New Request",
				'description'=>"Fill out request for dialysis service",
				'icon'=>createComIcon($root_path,'door_in.png','0'),
				'permission' => $NewRequest_permission ),

		'sListPatients' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-request-new.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=listpatients",
				'label'=>"List of Patients",
				'description'=>"List of current patients today",
				'icon'=>createComIcon($root_path,'statbel2.gif','0'),
				'permission' => $PatientList_permission ),

		'sTransmittal' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-request-new.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=dialysis_transmittal",
				'label'=>"Transmittal",
				'description'=>"Process transmittals to health insurances",
				'icon'=>createComIcon($root_path,'file_update.gif','0'),
				'permission' => $Transmittal_permission ),

		/*
		Created: Jayson - OJT, 2/10/2014
		Added ListofPatients and Transmittal menu.
		*/

		/*'sListRequest' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-request-list.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=listrequest",
				'label'=>"List of Requests",
				'description'=>"List of active dialysis requests",
				'icon'=>createComIcon($root_path,'group_edit.png','0') ),

		'sBilling' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-billing.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=billing",
				'label'=>"Billing",
				'description'=>"Manage billing for dialysis requests",
				'icon'=>createComIcon($root_path,'folder_user.png','0') ),*/
		/* Modified: Jayson Garcia
			Reason: Request from the Dialysis clients
			Jan 14,2014
			*/

		# Added by: syboy 12/18/2015 : meow
        // 'sSearchEmp' => array(
        //     'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=diasearchdoctor",
        //     'label' => "Search employee",
        //     'description' => 'Search Active and Inactive employee',
        //     'icon' => createComIcon($root_path,'lockfolder.gif','0'),
        //     //'permission' => $_a_1_searchempdependent,
        // ),
        # Ended syboy

		// 'sReports' => array(
		// 		//'href'=>$root_path.'modules/dialysis/seg-dialysis-reports.php'. URL_APPEND."&target=reports&userck=$userck",
		// 		'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=reports",
		// 		'label'=>"Reports",
		// 		'description'=>"Generate Dialysis related reports",
		// 		'icon'=>createComIcon($root_path,'chart_bar.png','0'),
		// 		'permission' => $Reports_permission ),
                
                /* Created By: Mary Mae - Trainee April 3, 2014*/
                
        'sSessionReports' => array(
			'href'=>$root_path.'modules/reports/report_launcher.php'. URL_APPEND."&dept_nr=144",
			'label'=>"Report Launcher",
			'description'=>"Generate Dialysis reports",
			'icon'=>createComIcon($root_path,'chart_bar.png','0'),
			'permission' => $Reports_permission
		),

		'sUserManual' => array(
			'href'=>$root_path.'forms/DIALYSIS_NEW Manual.pdf',
			'label'=>"User's Manual",
			'description'=>"PDF Copy of User's Manual",
			'icon'=>createComIcon($root_path,'pdf-icon.png','0'),
			'permission' => ''
		),
	),
	 /*
	"Administration" => array(

		'sServicesManager' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-service-manager.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=manageservice",
				'label'=>"Services Manager",
				'description'=>"Manage dialysis services options",
				'icon'=>createComIcon($root_path,'images.png','0') ),

		'sTestManager' => array(
				//'href'=>$root_path.'modules/dialysis/seg-dialysis-test-manager.php'. URL_APPEND."&userck=$userck",
				'href'=>$root_path.'modules/dialysis/seg-dialysis-pass.php'. URL_APPEND."&userck=$userck&target=managetest",
				'label'=>"Test Default Manager",
				'description'=>"Manage dialysis default tests",
				'icon'=>createComIcon($root_path,'monitor_go.png','0') )
	)*/
);

$smarty->assign('aMenu', $aMenu);

# Assign the submenu to the mainframe center block
 $smarty->assign('sMainBlockIncludeFile','dialysis/submenu.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
