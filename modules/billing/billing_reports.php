<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/billing/ajax/billing.common.php');//added by nick 2/1/14
require_once($root_path.'include/care_api_classes/class_acl.php');//added by Nick 2/4/14
//added by Nick 2/1/14

$objAcl = new Acl($_SESSION['sess_temp_userid']);
$TransmittalReportPermission = $objAcl->checkPermissionRaw('_a_1_transmittalHistoryReport');

if(!(isset($_GET['jasperPrint']) && $_GET['jasperPrint']==1)){
	$AcrStyle = "display:none";//added by Nick 2/22/2014
	$TransmittalHistory = "display:none";
	$btnJasperPrint = "display:none;";
}else{
	if($TransmittalReportPermission != 1)
		$TransmittalHistory = "display:none";
	$btnPrint = "display:none";
	$format = "display:none";
}
//end nick

$title='Billing::Reports';
if (!$_GET['from'])
	$breakfile=$root_path."modules/billing/bill-main-menu.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/billing/bill-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg_billing_reports.php';

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
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

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);
require_once($root_path . '/frontend/bootstrap.php');
include_once($root_path . '/modules/repgen/redirect-report.php');

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad=""');

# Collect javascript code
ob_start()

?>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<?php   $xajax->printJavascript($root_path.'classes/xajax_0.5');  ?>

<!-- added by nick 2/1/2014 -->
<div id="print_params" style="display:none;">
	<table width="100%">
		<tr>
			<td width="25%">Print Format:</td>
			<td width="75%">
				<select id="print_format">
					<option id="pdf">PDF</option>
					<option id="excel">Excel</option>
				</select>
			</td>
		</tr>
		<tr id = "personnel_tr">
			<td width="25%">Billing Personnel:</td>
			<td width="75%">
				<select id="personnel">
					<!-- <options id="" selected>- Select a personnel -</options> -->
				</select>
			</td>
		</tr>
	</table>
</div>
<!-- end nick -->

<script type="text/javascript">

let report_portal = "<?=$report_portal; ?>";
let connect_to_instance = "<?=$connect_to_instance; ?>";
let personnel_nr = "<?= $personnel_nr; ?>";
let _token = "<?= $_token; ?>";

var $j = jQuery.noConflict();

var clerks;
function setClerks(data){
	clerks = data;
	addClerks();
}

function addClerks(){
	for(i=0; i<=clerks.length; i++){
		$j('#personnel').append('<option value="'+clerks[i][0]+'">'+clerks[i][1]+'</option>');
	}
}

function debug(data){
	$j('<div style="width:100%;height:100%;""></div>')
	.html('<textarea style="width:100%;height:100%;">'+data+'</textarea>')
	.dialog();
}

function openJasperReport(){
    $j('#print_params').dialog('open');
}

//added by nick 2/1/2014
$j(function(){
	xajax_getClerks();
	$j('#print_params').dialog({
		autoOpen:false,
		modal:true,
		width:400,
		title:"Additional Parameters",
		open:function(x,y){
			if ($('selreport').value == 'transmittal_history'){
				$('personnel_tr').style.display = 'none';
			}else{
				$('personnel_tr').style.display = '';
			}
		},
		buttons:{
			Print:function(){

					var rep_script = '';
					var report_type = '';
					var nleft = (screen.width - 680)/2;
				    var ntop = (screen.height - 520)/2;
				    var cancel = false;

				    var selected_report = $('selreport').value;
				    var report_dtype = $('daily_bills_rendered_Dtype').value;
				    var clerk = $('personnel').value;

				    var date = "";
				    var month = "";
				    var year = "";

				    var params = "";

				    if (selected_report == 'daily_bills_rendered'){
				    	date = $('daily_bills_rendered_date').value
				    	params = "date=" + date;
				    	rep_script = "bills_jasper.php";
				    }else if (selected_report == 'monthly_bills_rendered'){
				    	month = $('monthly_bills_rendered_month').value;
				    	year = $('monthly_bills_rendered_year').value;
				    	params = "month=" + month + "&year=" + year;
				    	rep_script = "bills_jasper.php";
				    }else if(selected_report == 'acr_daily'){
				    	report_dtype = $('acr_daily_dtype').value;
				    	rep_script = 'acr_jasper.php';
				    	params = "date=" + $('acr_daily_date').value;
				    }else if(selected_report == 'acr_monthly'){
				    	report_dtype = $('acr_monthly_dtype').value;
				    	month = $('acr_month').value;
				    	year = $('acr_year').value;
				    	params = "month=" + month + "&year=" + year;
				    	rep_script = "acr_jasper.php";
                    }else if(selected_report == 'acr'){
                        var report_dtype = $('acr_status').value;
                        var from = $('acr_from').value;
                        var to = $('acr_to').value;
                        var phic = $('acr_insurance').value
                        params = "from=" + from + "&to=" + to;
                        params+= "&insurance=" + phic;
                        rep_script = "acr_jasper.php";
				    }else{
				    	month = $('acr_month').value;
				    	year = $('acr_year').value;
				    	params = "month=" + month + "&year=" + year;
				    	rep_script = "acr_jasper.php";
				    }

				    url = "reports/"+rep_script+"?report="+selected_report
				                                 +"&dtype="+report_dtype
				                                 +"&"+params
				                                 +"&personnel="+clerk
				                                 +"&reportFormat="+$('print_format').value;
					window.open(url, "Transmittal Report", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
			},
			Cancel:function(){
				$j(this).dialog('close');
			}
		}
	});//print params
});
//end nick

var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

function pSearchClose() {
	cClick();
}

function selOnChange() {
	var optSelected = $('selreport').options[$('selreport').selectedIndex];
	var spans = $$('[name=selOptions]');
	for (var i=0; i<spans.length; i++) {
		if (optSelected) {
			if (spans[i].getAttribute("segOption") == optSelected.value) {
				spans[i].style.display = "";
			}
			else
				spans[i].style.display = "none";
		}
	}
}

function openReport() {
	var rep = $('selreport').options[$('selreport').selectedIndex].value
	var url = 'reports/'+rep+'.php?'
	var query = new Array()
	var params = document.getElementsByName('param')
	var paramsD = $('daily_bills_rendered_Dtype').options[$('selreport').selectedIndex].value
	for (var i=0; i<params.length; i++) {
		if (params[i].getAttribute("segOption") == rep) {
			var mit;
			if (params[i].type=='checkbox') mit=params[i].checked;
			else if (params[i].type=='radio') mit=params[i].checked;
			else mit=params[i].value;
			if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
		}
	}
	// alert(url+query.join('&'))

    if (connect_to_instance == 1)
	    window.open(report_portal+"/modules/billing/"+url+query.join('&')+"&personnel_nr="+personnel_nr+"&ptoken="+_token,rep,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
    else{
        window.open(url+query.join('&'),rep,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
    }
	return false;
}

</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


$month_options = '';
for ($i=1;$i<=12;$i++)
	$month_options .= "									<option value=\"$i\">".date("F", strtotime("$i/1/2000"))."</option>\n";

$year_options = '';
for ($i=1980;$i<((int)date("Y")+50);$i++)
	$year_options .= "									<option value=\"$i\" ".($i==date("Y") ? 'selected="selected"' : "").">$i</option>";

$current_date = date($php_date_format);

ob_start();
?>



<br>


<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:500px">
	<table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td align="left" class="segPanelHeader" ><strong>Report options</strong></td>
			</tr>
			<tr>
				<td nowrap="nowrap" align="right" class="segPanel">
                    <table width="100%" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td width="20%" align="center" nowrap="nowrap"><strong>Select Report Type</strong>
                                <select class="segInput" name="selreport" id="selreport" onchange="selOnChange()"/>
                                <optgroup label="Daily reports">
                                    <option value="daily_bills_rendered">Daily Summary of Bills Rendered</option>
                                </optgroup>
                                <optgroup label="Monthly reports">
                                    <option value="monthly_bills_rendered">Monthly Summary of Bills Rendered</option>
                                </optgroup>
                                <optgroup label="Transmittal History" style="<?=$TransmittalHistory?>">
                                    <option value="transmittal_history" style="<?=$TransmittalHistory?>">Transmittal History</option>
                                </optgroup>
                                <optgroup label="ACR" style="<?=$AcrStyle?>">
                                    <option value="acr" style="<?=$AcrStyle?>">ACR Census</option>
                                    <!--
                                    <option value="acr_daily" style="<?/*=$AcrStyle*/?>">ACR Census Daily</option>
                                    <option value="acr_monthly" style="<?/*=$AcrStyle*/?>">ACR Census Monthly</option>
                                    -->
                                </optgroup>
                                </select>
                            </td>
                        </tr>
                    </table>


                    <hr width="90%" size="1" style="color:rgba(0,0,0,0.1)"/>

                    <!-- ACR - Added by Nick 7-14-2014 -->
                    <table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="acr" style="display:none">
                        <tr>
                            <td align="right" >Status</td>
                            <td nowrap="nowrap">
                                <select class="segInput" id="acr_status" name="param" paramName="formatD" segOption="acr">
                                    <option value="SA">Show All</option>
                                    <option value="DB">Deleted Bill</option>
                                    <option value="FB">Final Bill</option>
                                    <option value="NFB">Not Final Bill</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" >Insurance</td>
                            <td nowrap="nowrap">
                                <select class="segInput" id="acr_insurance" name="param" paramName="formatD" segOption="acr">
                                    <option value="SA">Show All</option>
                                    <option value="PH">PHIC</option>
                                    <option value="NPH">Non-PHIC</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">From</td>
                            <td nowrap="nowrap">
                                <input class="segInput" name="param" id="acr_from" type="text" size="12" value="" paramName="date" segOption="acr"/>
                                <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_acr_from" align="absmiddle" style="cursor:pointer;"  />
                                <script type="text/javascript">
                                    Calendar.setup ({
                                        inputField : "acr_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_acr_from", singleClick : true, step : 1
                                    });
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">To</td>
                            <td nowrap="nowrap">
                                <input class="segInput" name="param" id="acr_to" type="text" size="12" value="" paramName="date" segOption="acr"/>
                                <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_acr_to" align="absmiddle" style="cursor:pointer;"  />
                                <script type="text/javascript">
                                    Calendar.setup ({
                                        inputField : "acr_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_acr_to", singleClick : true, step : 1
                                    });
                                </script>
                            </td>
                        </tr>
                    </table>
                    <!-- end nick -->

					<!-- ACR - Added by Nick 2/22/2014 -->
					<!-- Daily -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="acr_daily" style="display:none">
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="acr_daily_dtype" name="param" paramName="formatD" segOption="acr_daily">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
								</select>
							</td>
						</tr>
							<td align="right" >Select date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="acr_daily_date" type="text" size="12" value="" paramName="date" segOption="acr_daily"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_acr_daily" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "acr_daily_date", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_acr_daily", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!-- Monthly -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="acr_monthly" style="display:none">
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="acr_monthly_dtype" name="param" paramName="formatD" segOption="acr_monthly">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="acr_month" name="param" paramName="month" segOption="acr_monthly">
									<?= $month_options ?>
								</select>
								<select class="segInput" id="acr_year" name="param" paramName="year" segOption="acr_monthly">
									<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>
					<!-- end ACR -->
					<!-- end nick -->

					<!-- DAILY BILLS RENDERED -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="daily_bills_rendered" style="">
						<tr style="<?=$format?>">
							<td align="right" >Select report format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="daily_bills_rendered_type" name="param" paramName="format" segOption="daily_bills_rendered">
									<option value="CSV">CSV</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="daily_bills_rendered_Dtype" name="param" paramName="formatD" segOption="daily_bills_rendered">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						</tr>
							<td align="right" >Select date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="daily_bills_rendered_date" type="text" size="12" value="" paramName="date" segOption="daily_bills_rendered"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_daily_bills_rendered_date" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "daily_bills_rendered_date", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_daily_bills_rendered_date", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>


					<!-- MONTHLY PER ACCOUNT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="monthly_bills_rendered" style="display:none">
						<tr>
							<td align="right" >Select report format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthly_bills_rendered_type" name="param" paramName="format" segOption="monthly_bills_rendered">
									<option value="CSV">CSV</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						<tr>
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthly_bills_rendered_Dtype" name="param" paramName="formatD" segOption="monthly_bills_rendered">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						</tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthly_bills_rendered_month" name="param" paramName="month" segOption="monthly_bills_rendered">
<?= $month_options ?>
								</select>
								<select class="segInput" id="monthly_bills_rendered_year" name="param" paramName="year" segOption="monthly_bills_rendered">
<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>


					<!-- added by nick 2/1/2014 -->
					<!-- TRANSMITTAL -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="transmittal_history" style="display:none">
						<tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="transmittal_history_month" name="param" paramName="month" segOption="transmittal_history">
									<?= $month_options ?>
								</select>
								<select class="segInput" id="transmittal_history_year" name="param" paramName="year" segOption="transmittal_history">
									<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>
					<!-- end nick -->

					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Tahoma;margin-top:5px">
						<tr>
							<td width="30%"></td>
							<td>
								<button class="segButton" onclick="openReport(); return false;" style="<?=$btnPrint?>"><img src="<?= $root_path ?>gui/img/common/default/report.png" /> View Report </button>
								<button class="segButton" onclick="openJasperReport(); return false;" style="<?=$btnJasperPrint?>"><img src="<?= $root_path ?>gui/img/common/default/report.png" /> View Report  </button>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
	 * LOAD Smarty
	 * param 2 = FALSE = dont initialize
	 * param 3 = FALSE = show no copyright
	 * param 4 = FALSE = load no javascript code
	 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<input type="hidden" name="lang" value="<?php echo $lang ?>" />
<input type="hidden" name="userck" value="<?php echo $userck ?>" />
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>" />
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>" />
<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump" />


</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
$smarty->assign('sMainFrameBlockData',$sTemp);

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>