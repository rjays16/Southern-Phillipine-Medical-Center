<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/agency_mgr.common.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_agency_mgr.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_dialysis_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));
$currDate=date('m/d/Y');

if (!$_GET['from'])
	$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
}

$thisfile='seg-ic-agency-report_form_details.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Industrial Clinic :: Agency Manager";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

#save data here
/*if(isset($_POST['submitted'])){
	$data = array(
		'name'=>$_POST['agency_name'],
		'address'=>$_POST['agency_address'],
		'contact_number'=>$_POST['agency_contact'],
		'short_name'=>$_POST['agency_sname'],
		'president'=>$_POST['agency_president'],
		'hr_manager'=>$_POST['agency_hr'],
		'account_no'=>$_POST['agency_account']
	);
	if()
}*/

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
# Collect javascript code
ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?= $root_path ?>modules/industrial_clinic/js/seg-ic-company-details.js" ></script>

<script type="text/javascript">
var $J = jQuery.noConflict();

function initialize()
{
	load_company_employees();  // #for billing added by angelo
}

function load_company_employees()
{

	ListGen.create($('employee-list'),{
		id: 'member',
		url: '<?=$root_path?>modules/industrial_clinic/seg-ic-billing-employee-list.php',
		params: {'agency_id':$('agency_id').value},
		width: 625,
		height: 200,
		columnModel: [
			{
				name: 'patient_id',
				label: 'Patient ID',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'patient_name',
				label: 'Employee Name',
				width: 225,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_bdate',
				label: 'Birthdate',
				width: 110,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_age',
				label: 'Age',
				width: 60,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'patient_sex',
				label: 'Sex',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'patient_status',
				label: 'Civil Status',
				width: 90,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			}
		]
	});
}



//search wizard
function SearchName(){
		return overlib(
			OLiframeContent('../../modules/industrial_clinic/seg-ic-billing-name-select.php?company_id='+$('company_id').value, 600, 350, 'fOrderTray', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../..//images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Person Name',
				MIDX,0, MIDY,0,
				STATUS,'Run Person Name');

}

//added code by angelo m. 08.24.2010
//start
function openReport(){

	var url="<?=$root_path?>"+"modules/industrial_clinic/";
	var params="";
	var validDate="<?= $currDate; ?>";

	url =url+ "seg-ic-consolidated-print-out.php";
	if($("searchDteStart").value=="" || $("searchDteEnd").value=="")
			alert("Please specify specific date...");
	else{
		if($("searchDteStart").value>validDate || $("searchDteEnd").value>validDate)
			alert("Please specify valid date for transaction.");
		else{
			params ="pid="+$('txtPid').value+"&company_id="+$('company_id').value+"&date_from="+$('searchDteStart').value+"&date_to="+$('searchDteEnd').value;
			window.open(url+"?"+params,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
		}
	}

}
//end


//load jquery dom
$J(function() {
		$J("#tabs").tabs({
			selected:0,
		});

});

document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$amgr_obj = new SegAgencyManager();
$id = $_POST['agency_id'] ? $_POST['agency_id'] : $_GET['agency_id'];
$data = $amgr_obj->getCompanyDetails($id);

$smarty->assign('companyId', '<input type="hidden" class="segInput" id="company_id" name="company_id" value="'.$id.'"/>');
//end cha


$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="agency_form" name="agency_form">');
$smarty->assign('form_end','</form>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('agency_id', '<input type="hidden" value="'.$_GET['agency_id'].'" name="agency_id" id="agency_id"/>');

#added by angelo m. 08.23.2010
#start here
#for billing form


$forCompany='<label>
							<input type="checkbox" id="chkCompany" name="chkCompany" onclick="showCompany();"/>
							All
						</label>';
$forCompanyDetails='
<div id="frmCompany" style="display:none">
</div>';
$forCompany=$forCompany.$forCompanyDetails;

$forEmployee='
	<div id="frmEmployee" style="display:block">
	Employee:&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="hidden" id="txtPid" name="txtPid" class="segInput" size="50"/>
	<input type="text" id="txtsearchName" name="txtsearchName" class="segInput" size="50"/>
		<img border="0" align="absmiddle" onclick="SearchName()" alt="Search" src="../../images/his_searchbtn.gif" class="segSimulatedLink"/>
	</div>
';
$searchDteStart='
<input type="text"
 id="searchDteStart"
 value=""
 maxlength="10"
 size="10"
 name="searchDteStart"/>
<img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;" id="searchDteStart_trigger"
src="'.$root_path.'gui/img/common/default/show-calendar.gif">'.
'<script type="text/javascript">
							Calendar.setup ({
								inputField : "searchDteStart", ifFormat : "%m/%d/%Y",
								 showsTime : false,
								 button : "searchDteStart_trigger",
								 singleClick : true,
								 step : 1
							});
</script>';
$searchDteEnd='
<input type="text"
 id="searchDteEnd"
 value=""
 maxlength="10"
 size="10"
 name="searchDteEnd"/>
<img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;" id="searchDteEnd_trigger"
src="'.$root_path.'gui/img/common/default/show-calendar.gif">'.
'<script type="text/javascript">
							Calendar.setup ({
								inputField : "searchDteEnd", ifFormat : "%m/%d/%Y",
								 showsTime : false,
								 button : "searchDteEnd_trigger",
								 singleClick : true,
								 step : 1
							});
</script>';

$viewReportBtn='<button class="segButton" onclick="openReport();return false;" style="cursor:pointer">
<img src="'.$root_path.'gui/img/common/default/page_white_acrobat.png"/>View Report</button>';

$smarty->assign('forCompany',$forCompany);
$smarty->assign('forEmployee',$forEmployee);
$smarty->assign('searchDteStart',$searchDteStart);
$smarty->assign('searchDteEnd',$searchDteEnd);
$smarty->assign('viewReportBtn',$viewReportBtn);
#end here




ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/agency_report_form_details.tpl');
$smarty->display('common/mainframe.tpl');

