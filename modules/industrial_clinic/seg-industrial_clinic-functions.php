<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'/include/inc_environment_global.php');
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
require_once($root_path.'global_conf/areas_allow.php');

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

$smarty->assign('sToolbarTitle',"Health Service and Specialty Clinic");

# Added for the common header top block
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDRadio')");

$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title',"Health Service and Specialty Clinic");

// --- Added by LST --- 4-18-2008 / Modified -- 6-26-2008 ----
unset($_SESSION["filteroption"]);
unset($_SESSION["filtertype"]);
unset($_SESSION["filter"]);
unset($_SESSION["current_page"]);
//------------------------------------

# Collect javascript
//ob_start();
//$sTemp = ob_get_contents();
//ob_end_clean();

# Append javascript to JavaScript block

$smarty->append('JavaScript',$sTemp);

#$url_search_emp = $root_path .'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_searchdoctor&from=ic"; # Added by: syboy 12/18/2015 : meow

$aMenu=array(
		//'href'=>$root_path.'modules/sponsor/seg_sponsor_cmap_patient.php'. URL_APPEND."&userck=$userck",
	"Patient Services"=>array(

		'sManageRegisterPatient' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_reg&from=ic",
				'label'=>"Register Patient",
				'description'=>"Register new patient data",
				'icon'=>createComIcon($root_path,'newpatient.gif','0') ),

		'sManageSearchPatient' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_searchpatient&from=ic",
				'label'=>"Search Patient",
				'description'=>"Search patient information",
				'icon'=>createComIcon($root_path,'search.gif','0') ),
	),

	"Health Service and Specialty Clinic Services"=>array(

		'sICTransactionsList' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_transactions_hist",
				'label'=>"Clinic Transactions History",
				'description'=>"Filter Health Service and Specialty Clinic Transactions History",
				'icon'=>createComIcon($root_path,'consultation.gif','0') ),


		'sICBilling' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_billing",
				'label'=>"Clinic Bills Generation",
				'description'=>"Manage or print clinic bills",
				'icon'=>createComIcon($root_path,'calculator_edit.png','0') ),

		'sBilling' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_consolidated_report_form",
				'label'=>"Company Bills",
				'description'=>"Print-out of all Requested Services (Company/Individual)",
				'icon'=>createComIcon($root_path,'wardlist.gif','0') ),
		/*commented by art 05/18/2014
		 'sDailyTransactionReport' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_transaction_daily_report",
				'label'=>"Reports",
				'description'=>"Generate ER reports",
				'icon'=>createComIcon($root_path,'report.png','0') ),
		*/
		/* added by art 04/26/2014 */
		 'sGenerateReport' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php?sid='.$sid.'&lang='.$lang.'&userck='.$userck.'&target=reportgen&from=ic',
				'label'=>"HSSC Report Launcher",
				'description'=>"Generate Hospital reports",
				'icon'=>createComIcon($root_path,'icon-reports.png','0') ),
		/* end art */

	),

	"Administration"=>array(

		'sManageAgency' => array(
				'href'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_manager",
				'label'=>"Agency Manager",
				'description'=>"Manage agency options",
				'icon'=>createComIcon($root_path,'timeplan.gif','0') ),
		# Added by: syboy 12/18/2015 : meow
        // 'sSearchEmp' => array(
        //     'href' => strtr($url_search_emp,array('{target}'=>'ic_searchdoctor')),
        //     'label' => "Search employee",
        //     'description' => 'Search Active and Inactive employee',
        //     'icon' => createComIcon($root_path,'lockfolder.gif','0'),
        // ),
        # Ended syboy
		'sPDFmanual' => array(
				'href'=>$root_path.'modules/industrial_clinic/pdf/INDUSTRIAL_CLINIC.pdf'. URL_APPEND."&userck=&target=",
				'label'=>"User Manual",
				'description'=>"PDF Copy of User's Manual",
				'icon'=>createComIcon($root_path,'pdf-icon.png','0') ),

	)

);

$smarty->assign('aMenu', $aMenu);

# Assign the submenu to the mainframe center block
 $smarty->assign('sMainBlockIncludeFile','common/basemenu.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
