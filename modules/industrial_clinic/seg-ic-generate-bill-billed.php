<?
# ============================================
#
# Added by James 3/4/2014
# For Generating Bill - Industrial Clinic
#
# ============================================

error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/industrial_clinic/ajax/seg-ic-transactions.common.php');
require_once($root_path.'include/inc_date_format_functions.php');

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

# Import Classes here ====================================================

$svrObj = new SegICTransaction();

# ========================================================================

global $db;

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"Industrial Clinic :: List of Billed Accounts");
$smarty->assign('sWindowTitle',"Industrial Clinic :: List of Billed Accounts");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);

ob_start();
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

<script type="text/javascript" src="js/seg-ic-billing.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();

// Put you scripts here ================================================

	// For deleting Bill
	function deleteBill(bill_nr) {

		proceed = confirm("Proceed to delete this Bill?");

		if(proceed) xajax_deleteBill(bill_nr);
	}

	// For printing billing report
	function printReport(comp_id, bill_nr){
		//var url="<?=$root_path?>"+"modules/industrial_clinic/seg-ic-billing-statement-report-billed.php"; //commented by art 06/10/2014
		var url="<?=$root_path?>"+"modules/industrial_clinic/reports/billstatement_billed.php";//added by art 06/10/2014
			var params = "";
			var report = "";
			params = "comp_id="+comp_id+"&bill_nr="+bill_nr;
			report = "seg-ic-billing-statement-report-billed.php";
			window.open(url+"?"+params,report,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

	// For reloading page
	function reloadFrame(){
		window.parent.location.href = "seg-ic-pass.php?ntid=false&lang=en&userck=&target=ic_billing";
	}

	function searchDate() {

		var agency_id = "<?= $_GET['comp_id']; ?>";
		var date = $J('#specificdate').val();

		$('icRows').innerHTML = "";
		xajax_populateBilledList(agency_id, date);
	}

	function populateList(data){

		var srcRows;
		var agency_id = data.agency_id;
		var billno = data.bill_nr;
		var billdate = data.bill_date;
		var billedamount = data.bill_amount;
		var type = data.type;

		if(type == 0)
		{
			srcRows = '<tr>'+
							'<td width="20%">'+billno+'</td>'+
							'<td width="40%">'+billdate+'</td>'+
							'<td width="25%" align="right">'+billedamount+'</td>'+
							'<td width="15%" align="center">'+
								'<a href="#" title="Print Bill!">'+
									'<img class="segSimulatedLink" border="0"'+ 
									'align="absmiddle"'+ 
									'onmouseout="nd()"'+ 
									'onclick="printReport('+agency_id+','+billno+'); return false;"'+
									'src="../../images/cashier_print.gif"/>'+
								'</a>'+

								'<a href="#" title="Delete bill!">'+
									'<img class="segSimulatedLink" border="0" align="absmiddle"'+
									'onclick="deleteBill('+billno+');"'+
									'src="../../images/cashier_delete.gif"/>'+
								'</a>'+
							'</td>'+
						'</tr>';
		}
		else
		{
			srcRows = '<tr><td colspan="4" align="center"> No billed accounts found on the specified date... </td></tr>';
		}

		$('icRows').innerHTML += srcRows;

	}

// =====================================================================

</script>
<?

# Put your conditions here =============================================

	# Assign values
	$agency_id = $_GET['comp_id'];
	$agency_name = $_GET['comp_name'];

	# Populate List of Employees
	$result = $svrObj->getBilledEmployees($agency_id);

	while ($row = $result->FetchRow()) {

		$billno = $row['bill_nr'];
		$billdate = $row['bill_rundate'];

		if($row['total'] < 0)
			$billedamount = number_format(0, 2, '.', ',');
		else
			$billedamount = number_format($row['total'], 2, '.', ',');

		$srcRows .= '<tr>
						<td width="20%">'.$billno.'</td>
						<td width="40%">'.$billdate.'</td>
						<td width="25%" align="right">'.$billedamount.'</td>
						<td width="15%" align="center">
							<a href="#" title="Print Bill!">
								<img class="segSimulatedLink" border="0" 
								align="absmiddle" 
								onmouseout="nd()" 
								onclick="printReport('.$agency_id.','.$billno.'); return false;"
								src="../../images/cashier_print.gif"/>
							</a>

							<a href="#" title="Delete bill!">
								<img class="segSimulatedLink" border="0" align="absmiddle"
								onclick="deleteBill('.$billno.');"
								src="'.$root_path.'images/cashier_delete.gif"/>
							</a>
						</td>
					</tr>';
	}

	$dateTime = '<span name="seldateoptions" segOption="specificdate">
						<input onchange="searchDate();" class="jedInput" name="specificdate" id="specificdate" type="text" size="13" value=""/>
						<img src="../../gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"/>
						<script type="text/javascript">
							Calendar.setup ({
								inputField : "specificdate",
								ifFormat : "'.$phpfd.'",
								showsTime : false,
								button : "tg_specificdate",
								singleClick : true,
								step : 1
							});
						</script>
					</span>';

# ======================================================================

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# assign your elements here ============================================

$smarty->assign('sAgency',$agency_name);
$smarty->assign('sListRows',$srcRows);
$smarty->assign('sDateTimePicker',$dateTime);

# ======================================================================

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/generate-bill-billed.tpl'); //Assign the industrial_clinic template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

?>