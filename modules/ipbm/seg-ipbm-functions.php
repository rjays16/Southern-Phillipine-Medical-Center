<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

#start IPBM UNIFIED ACCESS PERMISSION //Kemps 07/27/2017
require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);
$allAccess = $acl->checkPermissionRaw(array('_a_0_all', 'System_Admin'));

include_once $root_path . 'include/inc_ipbm_permissions.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 ?>

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

<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript">

	ShortcutKeys();
	function ShortcutKeys(){
		//new person
		shortcut.add('Alt+P', NewRegFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);
		
		//search
		shortcut.add('Alt+Z', SearchFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);		
		
		//advance search
		shortcut.add('Alt+X', AdvanceSearchFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);				
		
		//comprehensive search
		shortcut.add('Alt+C', CompreSearchFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

        shortcut.add('Alt+D', SearchDoc,
                                {
                                    'type':'keydown',
                                    'propagate':false,
                                }
                        );

		//consultation
		shortcut.add('Alt+A', ConsultationFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);
		
		//icd, icpm
		shortcut.add('Alt+M', MedocsFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);	
						
		//reports
		shortcut.add('Alt+R', ReportsFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

		shortcut.add('Alt+G', ReportGenFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

		//OpdUserManual
		shortcut.add('Alt+O', OpdUserManualFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);											
 	}
	
	//new person
	function NewRegFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmreg&from=ipbm";
		window.location.href=urlholder;
	}
	
	//search
	function SearchFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmsearchpatient&from=ipbm";
		window.location.href=urlholder;
	}
	
	//advance search
	function AdvanceSearchFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmsearchadv&from=ipbm";
		window.location.href=urlholder;
	}
	
	//comprehensive search
	function CompreSearchFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmsearchcompre&from=ipbm";
		window.location.href=urlholder;
	}
	
    function SearchDoc(){
        urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmsearchdoctor&from=ipbm";
        window.location.href=urlholder;
    }
	
	//consultation
	function ConsultationFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmconsultation&from=ipbm";
		window.location.href=urlholder;
	}

	//admission
	function AdmissionFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmadmission&from=ipbm";
		window.location.href=urlholder;
	}

	// //pharmacy
	// function PharmacyFxn(){
	// 	urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=pharmacy&from=ipbm";
	// 	window.location.href=urlholder;
	// }
	// //cashier
	// function CashierFxn(){
	// 	urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=cashier&from=ipbm";
	// 	window.location.href=urlholder;
	// }
	// //soc service
	// function SocialServiceFxn(){
	// urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=socialservice&from=ipbm";
	// 	window.location.href=urlholder;
	// }
	// //admission
	// function BillingFxn(){
	// 	urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=billing&from=ipbm";
	// 	window.location.href=urlholder;
	// }
	//icd, icpm
	function MedocsFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmicdicpm&from=ipbm";
		window.location.href=urlholder;
	}

	//reports
	function ReportsFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=ipbm";
		window.location.href=urlholder;
	}

	// gen report added by: syboy 05/29/2015
	function ReportGenFxn(){
		urlholder="<?=$root_path?>modules/ipbm/seg-ipbm-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reportgen&from=ipbm";
		window.location.href=urlholder;
	}

	//OpdUserManual
	function IPBMUserManualFxn(){
		urlholder="<?=$root_path?>forms/IPBM.pdf?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=ipbm";
		window.location.href=urlholder;
	}
</script>

<?php
 
 # Create a helper smarty object without reinitializing the GUI
 $smarty2 = new smarty_care('common', FALSE);

 $title = 'Institute of Psychiatry and Behavioral Medicine';

 # Title in the title bar
 $smarty->assign('sToolbarTitle', $title);

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPharmacy $LDPharmaDb')");
 
 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$title);

 # Prepare the submenu icons
#line 55 was added by VAN 09-20-08 , add a link for new request for all cost centers
 $aSubMenuIcon=array(
  createComIcon($root_path,'newpatient.gif','0'),
  createComIcon($root_path,'search.gif','0'),
  createComIcon($root_path,'search_plus.gif','0'),
  createComIcon($root_path,'patdata.gif','0'),
  createComIcon($root_path,'lockfolder.gif','0'),
  createComIcon($root_path,'consultation.gif','0'), #consultation
  createComIcon($root_path,'new_patient.gif','0'),  #admission

  createComIcon($root_path,'medicine.gif','0'), #pharmacy
  createComIcon($root_path,'money_add.png','0'), #cashier
  createComIcon($root_path,'task_tree.gif','0'), #social service
  createComIcon($root_path,'calculator_add.png','0'), #billing

  createComIcon($root_path,'icd10.gif','0'),
  #createComIcon($root_path,'chart.gif','0'),
  createComIcon($root_path,'icon-reports.png','0'),
  createComIcon($root_path,'pdf-icon.png','0')
);

# Prepare the submenu item descriptions
$aSubMenuText=array(
  "Register new patient data",
  "Search patient information", 
  "Full-featured patient searching", 
  "Comprehensive patient information", 

  "IPBM Consultation", 
  "IPBM Admission", 

  "Create new pharmacy request",
  "Process payments for hospital cost center requests",
  "Classify patient",
  "Process billing of admitted patient",

  "Patient ICD/ICPM encoding",
  "Generate IPBM reports",
  "Generate Hospital reports",
  "PDF Copy of User's Manual"
);

$aSubMenuItem=array(
  'LDRegPatient' => ($ipbmcanRegisterPatient) ? '<a href="javascript:NewRegFxn();">Register patient</a>' : 'Register Patient',
  'LDSearch' => ($ipbmcanUpdatePatient || $ipbmcanViewPatient) ? '<a href="javascript:SearchFxn();">Search patients</a>' : 'Search patients',
  'LDAdvSearch' => ($ipbmcanAccessAdvanceSearch) ? '<a href="javascript:AdvanceSearchFxn();">Advanced Search</a>' : 'Advanced Search',
  'LDComprehensive' => ($ipbmcanAccessAdvanceSearch) ? '<a href="javascript:CompreSearchFxn();">Comprehensive</a>' : 'Comprehensive',
  'LDConsultation' => ($ipbmcanAccessTriageConsultation) ? '<a href="javascript:ConsultationFxn();">Consultation</a>' : 'Consultation',
  'LDAdmission' => ($ipbmcanAccessTriageAdmission) ? '<a href="javascript:AdmissionFxn();">Admission</a>' : 'Admission',
  'LDPharmacy' => 'Pharmacy',
  'LDCashier' => 'Cashier',
  'LDSocialService' =>'Social Service',
  'LDBilling' => 'Billing',

  'LDIcdIcpm' => ($checkedParentOnlyMedicalRecords || $ipbmcanAccessICDICPM) ? '<a href="javascript:MedocsFxn();">ICD/ICPM</a>' : 'ICD/ICPM',
  'LDIcdMedCert' => 'Medical Certificates',
  // 'LDGenerateReport' => 'IPBM Report Launcher',
  'LDGenerateReport' => ($ipbmcanAccessReportLauncher)?'<a href="javascript:ReportGenFxn();">IPBM Report Launcher</a>':'IPBM Report Launcher',
'LDIPBMUserManual' =>'<a href="javascript:IPBMUserManualFxn();">User Manual</a>'
);

$iRunner = 0;

while(list($x,$v)=each($aSubMenuItem)){
	$sTemp='';
	ob_start();
	if($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg','<img '.$aSubMenuIcon[$iRunner].'>');
	$smarty2->assign('sSubMenuItem',$v);
	$smarty2->assign('sSubMenuText',$aSubMenuText[$iRunner]);
	$smarty2->display('common/seg_submenu_row.tpl');
	$sTemp = ob_get_contents();
 	ob_end_clean();
	$iRunner++;
	$smarty->assign($x,$sTemp);
}

# Assign the submenu items table to the subframe

# Assign the subframe to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','ipbm/submenu_ipbm.tpl');

  /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
