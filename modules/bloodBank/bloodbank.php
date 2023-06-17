<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
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
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

include_once($root_path.'include/care_api_classes/class_globalconfig.php');

$GLOBAL_CONFIG = array();
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Module title in the toolbar
 //$smarty->assign('sToolbarTitle',$LDLab);
 $smarty->assign('sToolbarTitle','Blood Bank');

//-----added 2007-10-03 FDP
 # Hide the return button
 $smarty->assign('pbBack',FALSE);
//-------------------------
#echo "root = ".$root_path;
?>
<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript">
	ShortcutKeys();
	function ShortcutKeys(){
		shortcut.add('Ctrl+Shift+N', NewRequest,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

		shortcut.add('Ctrl+Shift+L', RequestList,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		shortcut.add('Ctrl+Shift+D', TestRequestList,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		shortcut.add('Ctrl+Shift+B', BloodRequest,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );
				/*
		shortcut.add('Ctrl+Shift+P', PendingBloodRequest,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );
					*/
		shortcut.add('Ctrl+Shift+S', LabServicesAdmin,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		 //added by VAN 03-10-08
		 shortcut.add('Ctrl+Shift+G', LabServicesGroup,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		 shortcut.add('Ctrl+Shift+R', LabDeptReport,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		 //Added by Borj 2014-08-04 ISO
		 shortcut.add('Ctrl+Shift+R', LabotUserManual,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );
	}

	function NewRequest(){
		//labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabnew&user_origin=lab
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabnew&user_origin=lab";
		window.location.href=urlholder;
	}

	function RequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservrequest_new&user_origin=lab";
		window.location.href=urlholder;
	}

	function TestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabOrder&user_origin=lab&done="+done;
		window.location.href=urlholder;
	}
    
    function TestResultList(){
        urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=segbloodResult&user_origin=blood";
        window.location.href=urlholder;
    }

	function BloodRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood&user_origin=blood";
		window.location.href=urlholder;
	}

	//added by VAN 05-11-2010
	function OtherCharges(){
		 urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=charges&user_origin=lab";
		 window.location.href=urlholder;
	}

	function RequestSample(){
			urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=samples&user_origin=lab";
		 window.location.href=urlholder;
	}
	//------------
		/*
	function PendingBloodRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_list&subtarget=blood&user_origin=lab";
		window.location.href=urlholder;
	}
			*/

		function BloodPromissoryNote() {
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=promissory_note&user_origin=blood";
				window.location.href=urlholder;
		}

		//added by CHA 07-30-2009
		function BloodDonorRegistration()
		{
			 urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_donor&user_origin=blood";
			 window.location.href=urlholder;
		}
		//end cha

	function BloodRequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_list&user_origin=blood";
		window.location.href=urlholder;
	}

	function BloodTestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_result&user_origin=blood&done="+done;
		window.location.href=urlholder;
	}

	// function LabServicesAdmin(){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservadmin&user_origin=lab";
	// 	window.location.href=urlholder;
	// }

	// function LabServicesAdminOLD(){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservadminOLD&user_origin=lab";
	// 	window.location.href=urlholder;
	// }

	// //added by VAN 03-10-08
	// function LabServicesGroup(){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservgroup&user_origin=lab";
	// 	window.location.href=urlholder;
	// }

	// 	//added by Raissa 02-02-09
	// 	function LabTests(){
	// 			urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabtest&user_origin=lab";
	// 			//urlholder="labor_test.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabtests&user_origin=lab";
	// 			window.location.href=urlholder;
	// 	}

	// 	function LabReagents(){
	// 			urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabreagents&user_origin=lab";
	// 			window.location.href=urlholder;
	// 	}

	// function LabReagentsInventory(){
	// 			urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabinventory&user_origin=lab";
	// 			window.location.href=urlholder;
	// 	}

	// function LabDeptReport(){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabreports&user_origin=lab";
	// 	window.location.href=urlholder;
	// }

	// //added by VAN 07-23-2010
	// function SpecialLabRequest(){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=specialLab&user_origin=splab";
	// 	window.location.href=urlholder;
	// }

	// function SpecialLabRequestList(){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=specialLab_list&user_origin=splab";
	// 	window.location.href=urlholder;
	// }

	// function SpecialLabTestRequestList(done){
	// 	urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=specialLab_result&user_origin=splab&done="+done;
	// 	window.location.href=urlholder;
	// }

	//Added by Borj 2014-08-04 ISO
	//LDLabotUserManual
	function LabotUserManual(){
		//urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabreports&user_origin=lab";
		//window.location.href=urlholder;

		urlholder="<?=$root_path?>forms/BB Manual.pdf?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=ipd";
		window.location.href=urlholder;
	}
	//--------------------

	//added by VAN 08-31-2010
	function ICLabRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=ICLab&user_origin=iclab";
		window.location.href=urlholder;
	}

	function ICLabRequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=ICLab_list&user_origin=iclab";
		window.location.href=urlholder;
	}

	function ICLabTestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=ICLab_result&user_origin=iclab&done="+done;
		window.location.href=urlholder;
	}
	//--------------------
    
    //added by VAS 03-25-2013
    //report generator
    function ReportGenFxn(){
       urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reportgen&from=bloodbank";
       window.location.href=urlholder; 
    }

	//added by Gervie 10/05/2015
	function SplReportGenFxn(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=SplabRepGen&from=splab";
		window.location.href=urlholder;
	}

</script>

<?php
 # Help button href
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDLab);
	# Blood lab submenu block
	$smarty->assign('LDBloodBank','Blood Bank');
	$smarty->assign('sBloodRequestIcon','<img ' . createComIcon($root_path,'redlist.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDBloodRequest',"<a href=\"javascript:BloodRequest();\">$LDBloodRequest</a>");
	$smarty->assign('LDBloodRequestTxt',$LDBloodRequestTxt);

	#added by VAN
 $smarty->assign('sBloodServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest_new&user_origin=lab\">List of Laboratory Request</a>");
 $smarty->assign('LDBloodServicesRequest',"<a href=\"javascript:BloodRequestList();\">List of Service Requests</a>");

 #$smarty->assign('LDLabServicesRequestOld',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest&user_origin=lab\">Laboratory Services Request Old</a>");

#added by VAN 11-08-07
 $smarty->assign('sBloodServicesOrderIcon','<img ' . createComIcon($root_path,'hfolder.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
 $smarty->assign('LDBloodLabServicesOrder',"<a href=\"javascript:BloodTestRequestList(0);\">List of Undone Requests</a>");

 #added by VAN 07-02-08
 $smarty->assign('sBloodServicesDoneIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
 $smarty->assign('LDBloodServicesDone',"<a href=\"javascript:BloodTestRequestList(1);\">List of Done Requests</a>");

#added by VAS 07/16/2019
$smarty->assign('sBloodServicesResultIcon','<img ' . createComIcon($root_path,'yellowlist.gif','0') . ' align="absmiddle">');

$glob_obj->getConfig('bloodbank_result_effectivity%');
$bb_result_effect = explode(",",$GLOBAL_CONFIG['bloodbank_result_effectivity']);

if(strtotime(date("Y-m-d H:i:s")) < strtotime($bb_result_effect[0]) )
	$bloodServicesResult = "List of Blood Compatibility Results";
else $bloodServicesResult = "<a href=\"javascript:TestResultList();\">List of Blood Compatibility Results</a>";

$smarty->assign('LDBloodServicesResult',$bloodServicesResult);


 //Added by Omick 02-06-2009
$smarty->assign('blood_promissory_icon','<img src="'.$root_path.'images/laboratory/promissory_note.png" align="absmiddle">');
$smarty->assign('blood_promissory_title',"<a href=\"javascript:BloodPromissoryNote();\">Promissory Note</a>");

#Added by CHA 07-30-2009
$smarty->assign('blood_donor_icon','<img src="'.$root_path.'gui/img/common/default/group.png" align="absmiddle">');
$smarty->assign('blood_donor_title',"<a href=\"javascript:BloodDonorRegistration();\">Blood Donor Registration</a>");
#end CHA
	$smarty->assign('LDAdministration',"Laboratory Service Management");
	#Added by Borj 2014-08-04 ISO
	$smarty->assign('LDLabUserManual',"User Manual");

 #Added by Borj 2014-08-04 ISO
 $smarty->assign('sLaboUserManualIcon','<img ' . createComIcon($root_path,'pdf-icon.png','0') . ' align="absmiddle">');
 $smarty->assign('LDLaboUserManual',"<a href=\"javascript:LabotUserManual();\">User Manual</a>");


$smarty->assign('sBloodGenerateReportIcon','<img ' . createComIcon($root_path,'icon-reports.png','0') . ' align="absmiddle">');
$smarty->assign('LDBloodGenerateReport',"<a href=\"javascript:ReportGenFxn();\">Blood Bank Report Launcher</a>");


#added by mark 04-22-16 for Pharmacy

$smarty->assign('LDSegPharmaNewOrderIcon','<img ' . createComIcon($root_path,'order.gif','0') . ' align="absmiddle">');
$smarty->assign('LDSegPharmaNewOrder','<a href="'.$root_path.'modules/pharmacy/seg-pharma-order.php?sid=lk4h7ga7busdtreaa230de9lb6&lang=en&userck=ck_prod_order_user&target=new&area=bb&from=&checkintern=1&&bbDisabled=true&isBloodBB=1">Create new request</a>');

$smarty->assign('LDSegPharmaOrderManageIcon','<img ' . createComIcon($root_path,'manage_orders.gif','0') . ' align="absmiddle">');
$smarty->assign('LDSegPharmaOrderManage','<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=orderlist'.'&areaFROM=BB">Manage requests</a>');


$smarty->assign('LDSegPharmaOrderServeIcon','<img ' . createComIcon($root_path,'disc_unrd.gif','0') . ' align="absmiddle">');
$smarty->assign('LDSegPharmaOrderServe','<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=servelist'.'&areaFROM=BB">Serve request</a>');


$smarty->assign('LDSegPharmaSetAreaIcon','<img ' . createComIcon($root_path,'bul_arrowgrnsm.gif','0') . ' align="absmiddle">');
$smarty->assign('LDSegPharmaSetArea','<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&set=1&target=">Default area</a>');

#end add 04-22-16 for pharmacy
    
# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','laboratory/submenu_bloodbank.tpl');

 /**
 * show  Mainframe Template
 */

 $smarty->display('common/mainframe.tpl');

// require($root_path.'js/floatscroll.js');
?>