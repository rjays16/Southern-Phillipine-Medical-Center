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

		// added by: syboy 12/18/2015 : meow; search employee
        shortcut.add('Alt+D', SearchDoc,
                                {
                                    'type':'keydown',
                                    'propagate':false,
                                }
                        );
        // ended

		//comprehensive search
		shortcut.add('Alt+C', CompreSearchFxn,
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

		//Added by Borj 2014-08-04 ISO
		//ErUserManual
		shortcut.add('Alt+E', ErUserManualFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);											
 	}
	
	//new person
	function NewRegFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_reg&from=er";
		window.location.href=urlholder;
	}
	
	//search
	function SearchFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_searchpatient&from=er";
		window.location.href=urlholder;
	}
	
	//advance search
	function AdvanceSearchFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_searchadv&from=er";
		window.location.href=urlholder;
	}

	// added by: syboy 12/18/2015 : meow; Search Employee
    function SearchDoc(){
        // urlholder="<?=$root_path?>";
        urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_searchdoctor&from=er";
        window.location.href=urlholder;
    }
	
	//comprehensive search
	function CompreSearchFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_searchcompre&from=er";
		window.location.href=urlholder;
	}
	
	//consultation
	function ConsultationFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_consultation&from=er";
		window.location.href=urlholder;
	}
	
	//icd, icpm
	function MedocsFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_icdicpm&from=er";
		window.location.href=urlholder;
	}
    
    //Medical Certificate
    function MedCertFxn(){
        //urlholder="<?=$root_path?>modules/registration_admission/cert_med_search.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_icdicpm&from=er";
        urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_medcert&from=er";
        window.location.href=urlholder;
    }
	
	//reports
	function ReportsFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=er";
		window.location.href=urlholder;
	}

	function ReportLauncherFxn(){
		urlholder="<?=$root_path?>modules/er/seg-er-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reportgen&from=er";
		window.location.href=urlholder;
	}

	//Added by Borj 2014-08-04 ISO
	//EruserManual
	function ErUserManualFxn(){
		urlholder="<?=$root_path?>forms/ER.pdf?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=er";
		window.location.href=urlholder;
	}
</script>

<?php
 
 # Create a helper smarty object without reinitializing the GUI
 $smarty2 = new smarty_care('common', FALSE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Emergency Room Department");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPharmacy $LDPharmaDb')");

 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');
  
 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"Emergency Room Department");

 # Prepare the submenu icons
#line 55 was added by VAN 09-20-08 , add a link for new request for all cost centers
 $aSubMenuIcon=array(
  createComIcon($root_path,'newpatient.gif','0'),
  createComIcon($root_path,'search.gif','0'),
  createComIcon($root_path,'search_plus.gif','0'),
  createComIcon($root_path,'patdata.gif','0'),
  createComIcon($root_path,'lockfolder.gif','0'), # added by: syboy 12/18/2015 : meow
  createComIcon($root_path,'consultation.gif','0'),
  createComIcon($root_path,'icd10.gif','0'),
  createComIcon($root_path,'disc_unrd.gif','0'), 
  createComIcon($root_path,'chart.gif','0'),
  createComIcon($root_path,'icon-reports.png','0'),
  //Added by Borj 2014-08-04 ISO
  createComIcon($root_path,'pdf-icon.png','0')
);

# Prepare the submenu item descriptions
$aSubMenuText=array(
  "Register new patient data",
  "Search patient information", 
  "Full-featured patient searching", 
  "Comprehensive patient information", 
  "Search Active and Inactive employee", # added by: syboy 12/18/2015 : meow
  "Register patient ER consultation", 
  "Patient ICD/ICPM encoding", 
  "View and Generate Medical Certificates",
	"Generate ER reports",
	"Generate Hospital reports",
	//Added by Borj 2014-08-04 ISO
	"PDF Copy of User's Manual"
);

# Prepare the submenu item links indexed by their template tags
/*
$aSubMenuItem=array(
  'LDRegPatient' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=er_reg&from=er">Register patient</a>',
  'LDSearch' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=er_searchpatient&from=er">Search patients</a>',
  'LDAdvSearch' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=er_searchadv&from=er">Advanced search</a>',
  'LDComprehensive' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=er_searchcompre&from=er">Comprehensive</a>',  
  'LDConsultation' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=er_consultation&from=er">Consultation</a>',
  'LDIcdIcpm' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=er_icdicpm&from=er">ICD/ICPM</a>',
	'LDGenerateOPDReport' => '<a href="'.$root_path.'modules/er/seg-er-pass.php'. URL_APPEND."&userck=$userck".'&target=reports&from=er">Reports</a>',
);
*/
$aSubMenuItem=array(
  'LDRegPatient' => '<a href="javascript:NewRegFxn();">Register patient</a>',
  'LDSearch' => '<a href="javascript:SearchFxn();">Search patients</a>',
  'LDAdvSearch' => '<a href="javascript:AdvanceSearchFxn();">Advanced search</a>',
  'LDComprehensive' => '<a href="javascript:CompreSearchFxn();">Comprehensive</a>', 
  'LDDocSearch' => '<a href="javascript:SearchDoc();">Search employee</a>', # added by: syboy 12/18/2015 : meow 
  'LDConsultation' => '<a href="javascript:ConsultationFxn();">Consultation</a>',
  'LDIcdIcpm' => '<a href="javascript:MedocsFxn();">ICD/ICPM</a>',
  'LDIcdMedCert' => '<a href="javascript:MedCertFxn();">Medical Certificates</a>',
	'LDGenerateOPDReport' => '<a href="javascript:ReportsFxn();">Reports</a>',
	'LDERReportLauncher' => '<a href="javascript:ReportLauncherFxn();">ER Report Launcher</a>',
	//Added by Borj 2014-08-04 ISO
	'LDErUserManual' => '<a href="javascript:ErUserManualFxn();">User Manual</a>',
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
$smarty->assign('sMainBlockIncludeFile','er/submenu_er.tpl');

  /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
