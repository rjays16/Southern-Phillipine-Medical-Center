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

#$breakfile='apotheke.php'.URL_APPEND;
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 ?>
 <!--added by VAN 09-20-08 -->
 <!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
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
		//new born registration
		shortcut.add('Alt+N', NewbornFxn,
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

		//added by Macoy July 5,2014
		shortcut.add('Alt+D', SearchDoc,
								{
									'type':'keydown',
									'propagate':false,
								}
						);				
		//end

		//search
		shortcut.add('Alt+P', SearchPxFxn,
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
 	}
	
	//new born registration
	function NewbornFxn(){
		urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=medocs_newbornreg&from=medocs";
		window.location.href=urlholder;
	}
	
	//search
	function SearchFxn(){
		urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=medocs_searchpatientrec&from=medocs";
		window.location.href=urlholder;
	}

	//added by Macoy July 5,2014
	function SearchDoc(){
		// urlholder="<?=$root_path?>";
		urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=medocs_searchdoctor&from=medocs";
		window.location.href=urlholder;
	}
	//end
	

	//search
	function SearchPxFxn(){
		urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=medocs_searchpatient&from=medocs";
		window.location.href=urlholder;
	}
	
	//reports
	function ReportsFxn(){
		urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=medocs";
		window.location.href=urlholder;
	}
    
    //added by VAS 05-28-2012
    //report generator, use cakePHP framework
    function ReportGenFxn(){
       urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reportgen&from=medocs";
       window.location.href=urlholder; 
    }
    //added by shandy 08-04-2014

    function ReportManualFxn(){
       urlholder="<?=$root_path?>forms/HIMD User manual01-08-18.pdf";
       window.location.href=urlholder; 
    }

	//consultation
	function OnlineConsultationFxn(){
		urlholder="<?=$root_path?>modules/medocs/medocs_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=onlineconsultation&from=medocs";
		window.location.href=urlholder;
	}	

</script>
<?php
 
 # Create a helper smarty object without reinitializing the GUI
 $smarty2 = new smarty_care('common', FALSE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Medical Records Department");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPharmacy $LDPharmaDb')");
 
 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"Medical Records Department");

 # Prepare the submenu icons
#line 55 was added by VAN 09-20-08 , add a link for new request for all cost centers
 $aSubMenuIcon=array(
  createComIcon($root_path,'requests.gif','0'),
  createComIcon($root_path,'search.gif','0'),
  createComIcon($root_path,'search_plus.gif','0'),
  createComIcon($root_path,'lockfolder.gif','0'),//added by Macoy July 5,2014
  createComIcon($root_path,'chart.gif','0'),
  //added by VAS 05-28-2012
  createComIcon($root_path,'icon-reports.png','0'),
  createComIcon($root_path,'pdf-icon.png','0'),
  #added by bryan for camiguin
  createComIcon($root_path,'comments.gif','0'),
  createComIcon($root_path,'memo_archives.gif','0'),
  createComIcon($root_path,'thumbs_up.gif','0'),
  createComIcon($root_path,'manager.gif','0'),
);

# Prepare the submenu item descriptions
$aSubMenuText=array(
  "Register new born data",
  "Search patient information", 
  "Search patient information with records", 
  "Search Active and Inactive employee",//added by Macoy July 5,2014; Edited by: syboy 12/17/2015 : meow
  "Generate Medical Records reports",
  "Register Patient and Create Encounter for Teleconsultation Requests",
  //added by VAS 05-28-2012
  "Generate Hospital reports",
  "PDF Copy of User's Manual",
  #added by bryan for camiguin                                                   
  "Post a request.",
  "History of requests made by your department.",
  "Acknowledge or accept issuances to your department.",
  "Adjust supplies in the inventory.", 
);

# Prepare the submenu item links indexed by their template tags

$aSubMenuItem=array(
  'LDRegNewBorn' => '<a href="javascript:NewbornFxn();">Register new born</a>',
  'LDSearch' => '<a href="javascript:SearchPxFxn();">Search patients</a>',
  'LDAdvSearch' => '<a href="javascript:SearchFxn();">Search patients with records</a>',
  'LDDocSearch' => '<a href="javascript:SearchDoc();">Search employee</a>',//added by Macoy July 5,2014; Edited by: syboy 12/17/2015 : meow
  'LDGenerateOPDReport' => '<a href="javascript:ReportsFxn();">Reports</a>',
  'LDOnlineConsultation' => '<a href="javascript:OnlineConsultationFxn();">Teleconsultation</a>',
  // 'LDGenerateOPDReport' => 'Reports',
  //added by VAS 05-28-2012
  'LDGenerateReport' => '<a href="javascript:ReportGenFxn();">Medical Records Report Launcher</a>',
  // 'LDGenerateReport' => 'Medical Records Report Launcher',
   'LDManual' => '<a href="javascript:ReportManualFxn();">User Manual</a>',
  #added by bryan for camiguin 120509
  'LDSegSupplyRequest' => '<a href="'.$root_path.'modules/supply_office/seg-check-user.php'. URL_APPEND."&userck=$userck".'&target=requestnew">Requisition</a>',
  'LDRequestsHistory' => '<a href="'.$root_path.'modules/supply_office/seg-check-user.php'. URL_APPEND."&userck=$userck".'&target=managereq">Requisition History</a>',
  'LDSegSupplyAcknowledge' => '<a href="'.$root_path.'modules/supply_office/seg-check-user.php'. URL_APPEND."&userck=$userck".'&target=issuanceack&from=">Acknowledge Issuance</a>',
  'LDSegSupplyAdjustment' => '<a href="'.$root_path.'modules/supply_office/seg-check-user.php'. URL_APPEND."&userck=$userck".'&target=adjustment"">Adjustment</a>',
);


# Create the submenu rows
/*
print_r($aSubMenuIcon);
echo "<br>";
print_r($aSubMenuText);
echo "<br>";
print_r($aSubMenuItem);
*/
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
$smarty->assign('sMainBlockIncludeFile','medocs/submenu_medocs.tpl');

  /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
