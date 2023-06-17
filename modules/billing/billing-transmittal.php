<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/billing-transmittal.common.php');
require_once($root_path.'include/care_api_classes/billing/class_transmittal.php');

require_once($root_path.'frontend/bootstrap.php');

$lang_tables[]='search.php';
define('LANG_FILE','finance.php');
define('CIRCULAR', 'new_circular');
$local_user='aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);

$from = $_GET["from"];
if (($from == "") || (!isset($from)))
		$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND."&userck=$userck";
else
		#Added Jayson-OJT 2/11/2014
		#To prevent page ERROR 404 when accessing from dialysis module. 
		if($_GET["userck"] == "ck_dialysis_user"){
			$breakfile=$root_path.'modules/dialysis/seg-dialysis-menu.php'.URL_APPEND;
		}else{
		#End Jayson-OJT
		$breakfile=$_GET["from"].".php".URL_APPEND;
		}
# Start Smarty templating here
/**
* LOAD Smarty
*/
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');

$smarty->assign('setCharSet', "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />");

# Title in toolbar
if ($from == 'dialysis_transmittal') {
	$smarty->assign('sToolbarTitle',"$LDDialysis :: $LDBillingTransmittal");
	$smarty->assign('QuickMenu', FALSE);
}else{
	$smarty->assign('sToolbarTitle',"$LDBillingMain :: $LDBillingTransmittal");
}
 

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('billing_main.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

if (($from != "") && (isset($from)))
		$_SESSION["breakfile"] = $breakfile;
else
		unset($_SESSION["breakfile"]);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBillingMain :: $LDListAll");

 if (isset($_POST["submitted"])) {
		$trnsmtl = new Transmittal();

		$trnsmtl->setTransmitNo($_POST["transmit_no"]);
		$trnsmtl->setTransmitDte($_POST["transmitdte"]);
		$trnsmtl->setInsuranceID($_POST["hcare_id"]);
		$trnsmtl->setRemarks($_POST["remarks"]);
		$trnsmtl->setUser($_SESSION['sess_user_name']);
		$trnsmtl->setOldTransmitNo($_POST["old_trnsmit_no"]);
		$trnsmtl->setEncountersWithClaim($_POST["cases"]);
		$trnsmtl->setPatientClaims($_POST["pclaims"]);

		$saveok = $trnsmtl->saveTransmittal();

		if ($saveok)
				$smarty->assign('sysInfoMessage','<strong>Successfully saved transmittal '.$_POST["transmit_no"].'!</strong>');
		else
				$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$trnsmtl->getErrorMsg());
}

 # Buffer page output
 ob_start();
?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>/js/shortcut.js"></script>

<!-- Calendar -->
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
 OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#ffffff;
	border:1px outset #3d3d3d;
}
.olcg {
	background-color:#ffffff;
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffff;
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px;
	font-weight:bold;
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>
<style type="text/css">
#icdAutoComplete {
		width:8em; /* set width here or else widget will expand to fit its container */
		padding-bottom:1.75em;
}

#icdDescAutoComplete {
		width:40em; /* set width here or else widget will expand to fit its container */
		padding-bottom:1.75em;
}

#barangay_autocomplete, #municipality_autocomplete {
	width:30.5em;
	font-weight:bold;
	padding-bottom:1.75em;
}
</style>

<style type="text/css">
		/* Clear calendar's float, using dialog inbuilt form element */
		#container .bd form {
				clear:left;
		}

		/* Have calendar squeeze upto bd bounding box */
		#container .bd {
				padding:0;
		}

		#container .hd {
				text-align:left;
		}

		/* Center buttons in the footer */
		#container .ft .button-group {
				text-align:center;
		}

		/* Prevent border-collapse:collapse from bleeding through in IE6, IE7 */
		#container_c.yui-overlay-hidden table {
				*display:none;
		}

		/* Remove calendar's border and set padding in ems instead of px, so we can specify an width in ems for the container */
		#cal {
				border:none;
				padding:1em;
		}

		/* Datefield look/feel */
		.datefield {
				position:relative;
				top:10px;
				left:10px;
				white-space:nowrap;
				border:1px solid black;
				background-color:#eee;
				width:25em;
				padding:5px;
		}

		.datefield input,
		.datefield button,
		.datefield label  {
				vertical-align:middle;
		}

		.datefield label  {
				font-weight:bold;
		}

		.datefield input  {
				width:15em;
		}

		.datefield button  {
				padding:0 5px 0 5px;
				margin-left:2px;
		}

		.datefield button img {
				padding:0;
				margin:0;
				vertical-align:middle;
		}

		/* Example box */
		.box {
				position:relative;
				height:30em;
		}
</style>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/yahoo/yahoo.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.8.1/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.8.1/autocomplete/assets/skins/sam/autocomplete.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.8.1/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.8.1/container/assets/skins/sam/container.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.8.1/calendar/assets/skins/sam/calendar.css" />

<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/connection/connection-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/animation/animation-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/autocomplete/autocomplete-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/element/element-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/button/button-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/container/container-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/calendar/calendar-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.8.1/selector/selector-min.js"></script>

<script type="text/javascript" src="<?=$root_path?>modules/medocs/js/ICDCodeParticulars.js"></script>

<!-- include billing.css -->
<link rel="stylesheet" type="text/css" href="css/billing.css" />
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<script type="text/javascript" src="js/billing-transmittal.js"></script>

<script>
		YAHOO.namespace("encounter.container");
		YAHOO.namespace("encounter.container"); // added by: syboy 06/24/2015
		YAHOO.namespace("categprompt.container");
		YAHOO.namespace("pphil.container");
		YAHOO.util.Event.onDOMReady(initFormsPrompt);
		YAHOO.util.Event.onDOMReady(initCataractFormsPrompt); // added by: syboy 06/24/2015
		YAHOO.util.Event.onDOMReady(initDataEditBox);
		YAHOO.util.Event.onDOMReady(initCategoryPrompt);
		YAHOO.util.Event.addListener(window, "load", init);
</script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<!-- Added by Nick, 3/28/2014 -->
<!-- <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script> -->
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/FileSaver.js"></script>
<!-- end nick -->

<script language="javascript" >
<!--

function auditTrail(){
    var new_value = jQuery('#transmit_no').val();
    var pageDiagnosis = "billing-transmittal-trail.php?transmit_no="+new_value;
    var dialogDiagnosis = jQuery('<div id="newIDialog"></div>')
        .html('<iframe id="myFRame" style="border: 0px; " src="' + pageDiagnosis + '" width="100%" height="345px"></iframe>')
        .dialog({
           autoOpen: true,
            modal: true,
            height: "auto",
            width: "80%",
            show: 'fade',
            hide: 'fade',
            resizable: false,
            draggable: false,
            title: "Transmittal Audit Trail",
            position: "top",
               buttons: {
              
                    Close: function () {
                        $j(this).dialog("close");
                    }
                }

        });


}
var $j = jQuery.noConflict();

jQuery(function($){
    $j('#memcat').on('change',function(){
        xajax_getBillsCount("<?=$_GET['tr_nr']?>",$j('#memcat').val());
    });
//    $j("#dischrgtme").mask("99:99:99");
});


function reportStat(){
	var curtransmit_no = $j('#curtransmit_no').val();
	var transmit_date = $j('#transmit_date').val();
	var root_path = $j('#root_path').val();

	var checkMedk = $j('input:radio[name=checkMedk]:checked').val();
		
	var urls = root_path+'/modules/billing/reports/Classification_Summary_Transmittal.php?trans_no='+curtransmit_no+'&trans_date='+transmit_date+'&status='+checkMedk+'';
	
    if (typeof checkMedk == "undefined") {
       	alert("Please select the Category.");
    }else{
    	window.open(urls);
    }
}



// -->
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
?>
<script type="text/javascript">
	YAHOO.namespace("encounter.container");
	YAHOO.util.Event.onDOMReady(function(){

				var Event = YAHOO.util.Event,
						Dom = YAHOO.util.Dom,
						dialog,
						calendar;

				var showBtn = Dom.get("show");

				Event.on(showBtn, "click", function() {

						// Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners, until the first time the button is clicked.
						if (!dialog) {

								// Hide Calendar if we click anywhere in the document other than the calendar
								Event.on(document, "click", function(e) {
										var el = Event.getTarget(e);
										var dialogEl = dialog.element;
										if (el != dialogEl && !Dom.isAncestor(dialogEl, el) && el != showBtn && !Dom.isAncestor(showBtn, el)) {
												dialog.hide();
										}
								});

								function resetHandler() {
										// Reset the current calendar page to the select date, or
										// to today if nothing is selected.
										var selDates = calendar.getSelectedDates();
										var resetDate;

										if (selDates.length > 0) {
												resetDate = selDates[0];
										} else {
												resetDate = calendar.today;
										}

										calendar.cfg.setProperty("pagedate", resetDate);
										calendar.render();
								}

								function closeHandler() {
										dialog.hide();
								}

								dialog = new YAHOO.widget.Dialog("container", {
										visible:false,
										context:["show", "tl", "bl"],
										buttons:[ {text:"Reset", handler: resetHandler, isDefault:true}, {text:"Close", handler: closeHandler}],
										draggable:false,
										modal:true,
										close:true
								});
								dialog.setHeader('Pick A Date');
								dialog.setBody('<div id="cal"></div>');
								dialog.render(document.body);

								dialog.showEvent.subscribe(function() {
										if (YAHOO.env.ua.ie) {
												// Since we're hiding the table using yui-overlay-hidden, we
												// want to let the dialog know that the content size has changed, when
												// shown
												dialog.fireEvent("changeContent");
										}
								});
						}

						// Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
						if (!calendar) {

								calendar = new YAHOO.widget.Calendar("cal", {
										iframe:false,          // Turn iframe off, since container has iframe support.
										hide_blank_weeks:true  // Enable, to demonstrate how we handle changing height, using changeContent
								});
								calendar.render();

								calendar.selectEvent.subscribe(function() {
										if (calendar.getSelectedDates().length > 0) {

												var selDate = calendar.getSelectedDates()[0];

												// Pretty Date Output, using Calendar's Locale values: Friday, 8 February 2008
												var wStr = calendar.cfg.getProperty("WEEKDAYS_LONG")[selDate.getDay()];
												var dStr = selDate.getDate();
												var mStr = calendar.cfg.getProperty("MONTHS_LONG")[selDate.getMonth()];
												var yStr = selDate.getFullYear();

												Dom.get("dischrgdate").value = wStr + ", " + dStr + " " + mStr + " " + yStr;
										} else {
												Dom.get("dischrgdate").value = "";
										}
										dialog.hide();
										Dom.get("dischrgdate").focus();
								});

								calendar.renderEvent.subscribe(function() {
										// Tell Dialog it's contents have changed, which allows
										// container to redraw the underlay (for IE6/Safari2)
										dialog.fireEvent("changeContent");
								});
						}

						var seldate = calendar.getSelectedDates();

						if (seldate.length > 0) {
								// Set the pagedate to show the selected date if it exists
								calendar.cfg.setProperty("pagedate", seldate[0]);
								calendar.render();
						}
						dialog.show();
				});
		});
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

//$smarty->assign('sOnUnloadJs',"onUnload=\"remLink()\"");

/* Effective Date for new circular*/
$DAYS_ALLOWED = Config::model()->get('days_allowed');
$NEW_DAYS_ALLOWED = Config::model()->get('new_days_allowed');

$circular = CareEffective::model()->getDetails(CIRCULAR);
$new_date_now = date('Y-m-d H:i:s');
$days_allowed = $DAYS_ALLOWED->value;

if ((($new_date_now >= $circular[0]->start_date) && ($new_date_now <= $circular[0]->end_date)) || !$circular[0]->end_date) {
    $days_allowed = $NEW_DAYS_ALLOWED->value;
}

if (isset($_SESSION['cases'])) unset($_SESSION['cases']);

//added by Jasper Ian Q. Matunog 11/25/2014 for eclaims
if ($_GET['eclaims'] == 1 && !isset($_POST["submitted"])) {
	require_once($root_path.'include/care_api_classes/class_insurance.php'); 
	$insurance = new Insurance;
	$temp = $insurance->getInsuranceInfo(18); //default to PHIC
	$stransmit_no = '';
	$dtransmit_dt = strftime("%Y-%m-%d %H:%M:%S");
	$nhcare_id    =  $temp['hcare_id'];
	$shcare_nm    = $temp['name'];
	$shcare_addr  = $temp['addr_mail'];
	$sremarks     = '';
} else {
	$stransmit_no = '';
	$dtransmit_dt = strftime("%Y-%m-%d %H:%M:%S");
	$nhcare_id    = 0;
	$shcare_nm    = '';
	$shcare_addr  = '';
	$sremarks     = '';
}

if (isset($_GET['tr_nr'])) {
		$smarty->assign('sTransmittalClaims',"
								<tr>
										<td colspan=\"8\">Transmittal list is currently empty ...</td>
								</tr>");

		// Get the transmittal header info and cases associated with it ...
		$trnsmtl = new Transmittal();
		if ($result = $trnsmtl->getTransmittalHeaderInfo($_GET['tr_nr'])) {
				$stransmit_no = $result["transmit_no"];
				$dtransmit_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime($result["transmit_dte"]));
				$nhcare_id    = $result["hcare_id"];
				$shcare_nm    = $result["name"];
				$shcare_addr  = $result["addr_mail"];
				$sremarks     = $result["remarks"];

				if ($result = $trnsmtl->getTransmittalDetailsInfo($_GET['tr_nr'])) {
						$cases = array();
						while ($row = $result->FetchRow()) {
								$cases[] = $row["encounter_nr"];
						}
						if (!empty($cases)) {
								$script = '<script type="text/javascript" language="javascript">';
								$script .= "\nvar encs =['".implode("','",$cases)."'];\n";
								$script .= "xajax_showTransmittalDetails(".$nhcare_id.", encs, '".$stransmit_no."');\n";
								$script .= "</script>";
								$src = $script;
						}
				}
				if ($src) $smarty->assign('sTransmittalClaims',$src);
		}
}
else {
	// Commented and added by Matsu 02042017
		$shcare_nm    = $_POST['hcname'];
		$shcare_addr  = $_POST['hcaddress'];
		$stransmit_no = $_POST["transmit_no"];
		$sremarks     = $_POST["remarks"];
		$nhcare_id    = $_POST["hcare_id"];
		$smarty->assign('sTransmittalClaims',"
								<tr>
										<td colspan=\"8\">Transmittal list is currently empty ...</td>
								</tr>");
// Ended by Matsu 02042017

}

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
		$smarty->assign('sTransmittalClaims',"
								<tr>
										<td colspan=\"9\">Transmittal list is currently empty ...</td>
								</tr>");

//    echo print_r($_POST["cases"]);

		if (is_array($_POST['cases'])) {
				$script = '<script type="text/javascript" language="javascript">';
				$cases = $_POST['cases'];
				$pclaims = $_POST['pclaims'] ;
				$pairing = array();
				$i = 0;
				foreach($cases as $v) {
						$pairing[$v] = $pclaims[$i++];
				}
				$script .= "\nvar encs =['".implode("','",$cases)."'];\n";
								$script .= "xajax_showTransmittalDetails(".$_POST["hcare_id"].", encs, '".$_POST['transmit_no']."');\n";
								$script .= "</script>";
				$src = $script;
		}
		if ($src) $smarty->assign('sTransmittalClaims',$src);
}
elseif (!isset($_GET['tr_nr'])) {
	// Commented and added by Matsu 02042017
		// $smarty->assign('sTransmittalClaims',"
		// 						<tr>
		// 								<td colspan=\"9\">Transmittal list is currently empty ...</td>
		// 						</tr>");

		if (is_array($_POST['cases'])) {
				$script = '<script type="text/javascript" language="javascript">';
				$cases = $_POST['cases'];
				$pclaims = $_POST['pclaims'] ;
				$pairing = array();
				$i = 0;
				foreach($cases as $v) {
						$pairing[$v] = $pclaims[$i++];
				}
				$script .= "\nvar encs =['".implode("','",$cases)."'];\n";
								$script .= "xajax_showTransmittalDetails(".$_POST["hcare_id"].", encs, '".$_POST['transmit_no']."');\n";
								$script .= "</script>";
				$src = $script;
		}
		if ($src) $smarty->assign('sTransmittalClaims',$src);
		// ended by Matsu 02042017
}

include($root_path.'modules/billing/billing-transmittal-access-permission.php');

if($canViewTransmittal && !$canDeleteTransmittal && !$canUpdateTransmittal && !$canAddTransmittal){
	$hideSave = ";display:none";
	$hideIcon = ";display:none";
	$hideAdd = "display:none";
	$disabled = "readOnly";
}
if(!$canAddTransmittal && !$canUpdateTransmittal && !$canDeleteTransmittal){
	$hideSave = ";display:none";
}

if(!$canUpdateTransmittal){
	$hideIcon = ";display:none";
	$disabled = "readOnly";
}

if(!$canAddTransmittal)
	$hideAdd = "display:none";


$submitted = isset($_POST["submitted"]);

$smarty->assign('sRootPath',$root_path);

// edited by carl

// updated by carriane 09/22/17
$smarty->assign('sTransmitNo', '<input class="segInput" id="transmit_no" maxlength="20" name="transmit_no" type="text" size="20" value="'.(($submitted && !$saveok) ? $_POST['transmit_no'] : $stransmit_no).'" style="font:bold 12px Arial; float;left; text-align:left" '.$disabled.'><span id="result">  </span>');
$smarty->assign('sHCareDesc', '<input class="segInput" id="hcname" name="hcname" type="text" size="60" value="'.(($submitted && !$saveok) ? $_POST['hcname'] : $shcare_nm).'" style="font:bold 12px Arial; float;left;" readOnly >');

//Select Health Insurance
$smarty->assign('sSelectHCare','<input class="segInput" id="select-hcare" type="image" src="../../images/FIND.gif" border="0" style=""
			 onclick="if (bClickedHCare) overlib(
				OLiframeContent(\'billing-select-hcare.php\', 700, 400, \'fSelHCare\', 0, \'auto\'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,
				CAPTION,\'Select Health Insurance\',
				MIDX,0, MIDY,0,
				STATUS,\'Select health insurance\'); return false;"
			 onmouseout="nd();" />');

$smarty->assign('sHCareAddress','<textarea class="segInput" id="hcaddress" name="hcaddress" cols="57" rows="2" style="font:bold 12px Arial" readOnly>'.(($submitted && !$saveok) ? $_POST['hcaddress'] : $shcare_addr).'</textarea>');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="57" rows="2" style="font:bold 12px Arial"'.$disabled.'>'.(($submitted && !$saveok) ? $_POST['remarks'] : $sremarks).'</textarea>');

$curTme  = strftime("%Y-%m-%d %H:%M:%S", strtotime($dtransmit_dt));
$curDate = strftime("%b %d, %Y %I:%M%p", strtotime($curTme));

// updated by carriane 09/22/17
$smarty->assign('sDate', '<span id="show_transmitdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? strftime("%b %d,%Y %I:%M%p", strtotime($_POST['transmitdte'])) : $curDate).'</span><input class="jedInput" name="transmitdte" id="transmitdte" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['transmitdte'])) : $curTme).'" style="font:bold 12px Arial" '.$disabled.'>');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="transmitdte_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer'.$hideIcon.'">');
$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_transmitdate\",
		inputField : \"transmitdte\",
		ifFormat : \"%Y-%m-%d %H:%M:%S\",
		daFormat : \"%b %d, %Y %I:%M%p\",
		showsTime : true,
		button : \"transmitdte_trigger\",
		singleClick : true,
		step : 1
	});
</script>";

$smarty->assign('jsCalendarSetup', $jsCalScript);

//added by Nick 06-03-2014
include($root_path.'include/care_api_classes/eTransmittal/class_eTransmittalXml.php');
// Editado por Matsu 04042017
$objETransmittal = new eTransmittalXml(!empty($_GET['tr_nr']) ? $_GET['tr_nr'] : $_POST['transmit_no'],0);
// Terminado por Matsu 04042017
$smarty->assign('billCount',count($objETransmittal->getTransmittals()));
$memcats = $objETransmittal->getMemberCategories();
$memcatOpts = "";
foreach ($memcats as $key => $memcat) {
    $memcatOpts .= "<option value='".$memcat['memcategory_id']."'>".htmlentities($memcat['memcategory_desc'])."</option>";
}
$smarty->assign('memcats',$memcatOpts);
//end nick


	if(!empty($_GET['tr_nr'])){$tr_no = $_GET['tr_nr'];}else{$tr_no = $_POST['transmit_no'];}
	// $linkdel ='&nbsp;<img id="btnDelete" style="cursor:pointer" src="'.$root_path.'/images/btn_delete.gif" border=0 onclick="if (confirm(\'Delete this transmittal?\')) xajax_delTransmittal('.$db->qstr($tr_no).')">';

	$linksave='<img id="btnSave" style="cursor:pointer'.$hideSave.'" src="'.$root_path.'/images/btn_save.gif" border=0 onclick="newValidateForm();" >';
// Initialize discharge date ....
//edited by borj 2-08-2014
//Add button Classification Summary Transmittal
$smarty->assign('sDischargeDate', '<input type="text" id="dischrgdate" name="dischrgdate" size="25" value="" disabled="disabled" readonly="readonly"/><img id="show" src="'.$root_path.'images/calbtn.gif" width="18" height="18" alt="Calendar" style="vertical-align:top">');

$smarty->assign('sBtnAddItem','<a style="'.$hideAdd.'" href="javascript:void(0);"
			 onclick="return overlib(
			OLiframeContent(\'billing-transmittal-list.php'.URL_APPEND.'&src=transmit&hid=\'+$(\'hcare_id\').value+\'\', 800, 400, \'fBillingTray\', 1, \'auto\'),
				WIDTH,800, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,
				CAPTION,\'Add Claims to Transmit\',
				MIDX,0, MIDY,0,
				STATUS,\'Add claim to transmit.\');"
			 onmouseout="nd();">
			 <img name="btnadd" id="btnadd" src="'.$root_path.'images/btn_add.gif" border="0"></a>');

$curtransmit_no = ($submitted && $saveok) ? $_POST['transmit_no'] : $_GET['tr_nr'];
$transmit_date = strtotime($dtransmit_dt);

if ($curtransmit_no != '') {
		$url = $root_path.'/modules/billing/reports/Classification_Summary_Transmittal.php?trans_no='.$curtransmit_no.'&trans_date='.$transmit_date.'&status='.$stat.'';
		$smarty->assign('sBtnDelete',$linkdel);
		#$smarty->assign('sAuditTrail','<input class="segButton" type="button"  style="cursor:pointer height:100px;width:100px;" border=0 value="Audit Trail" onclick="auditTrail()"/>');
		$smarty->assign('sAuditTrail', '&nbsp;<img id="sAuditTrail" style="cursor:pointer" src="'.$root_path.'/images/btn_audittrail.gif" border=0 onclick="auditTrail();";">');
        $smarty->assign('btnXml','<img id="btnSaveXml" onclick="xmlParamDialog(); return;" style="cursor:pointer" src="'.$root_path.'/images/XML.gif" border=0 >&nbsp;');
		$smarty->assign('sCheckboxMed', '<strong><input type="radio" name="checkMedk" value="Medical" id="checkMedk" />MEDICAL</strong>');
		$smarty->assign('sCheckboxSur', '<strong><input type="radio" name="checkMedk" value="Surgical" id="checkMedk" />SURGICAL</strong>');
		$smarty->assign('sCheckboxCas', '<strong><input type="radio" name="checkMedk" value="Case Rate" id="checkMedk" />CASE RATE</strong>');
		//$smarty->assign('sBtnSummary', '&nbsp;<img id="btnSummary" style="cursor:pointer" src="'.$root_path.'/images/btn_printsummary.gif" border=0 onclick="window.open(\''.$url.'\')">');
		$smarty->assign('sBtnSummary', '&nbsp;<img id="btnSummary" style="cursor:pointer" src="'.$root_path.'/images/btn_printsummary.gif" border=0 onclick="reportStat();">');
		$smarty->assign('sBtnPrintAll', '&nbsp;<img id="btnPrintALL" style="cursor:pointer" src="'.$root_path.'/images/btnprintall.png" border=0 onclick="printAllCF2();";">');
		$smarty->assign('sBtnPrint', '<img id="btnPrintTransmittal" style="cursor:pointer" src="'.$root_path.'/images/btn_printpdf.gif" border=0 >&nbsp;');
		$smarty->assign('sShowButtons', '');
		$smarty->assign('sNoShowButtons', 'style="display:none"');
}
else {
	$smarty->assign('sBtnPrintAll', '&nbsp;<img id="btnPrintALL" style="cursor:pointer" src="'.$root_path.'/images/btnprintall.png" border=0 onclick="printAllCF2();";">');
	$smarty->assign('sBtnDelete', '');
		$smarty->assign('sBtnPrint', '');
		//added by Jasper Ian Q. Matunog 11/25/2014
		if ($_GET['eclaims'] == 1) {
			$smarty->assign('sShowButtons', '');
		} else {
			$smarty->assign('sShowButtons', 'style="display:none"');
		}
		$smarty->assign('sNoShowButtons', '');
}

// ADDED by JEFF 06-06-17
//updated by carriane 09/22/17
#For hidden input of data...
$fieldsHidden ='<input type="hidden" id="reason_id" name="reason_id" value="" placeholder="reason id" />
<input type="hidden" id="has_exceeded" name="has_exceeded" value="0" />
<input type="hidden" id="days_allowed" name="days_allowed" value="'.$days_allowed.'" />
<input type="hidden" id="reason" name="reason" value="" placeholder="reason"/>
<input type="hidden" id="reason_others" name="reason_others" value="" placeholder="other reason"/>
<input type="hidden" id="del_enc_nr" name="del_enc_nr" value="" placeholder="encounter number"/> 
<input type="hidden" id="del_logid" name="del_logid" value="" placeholder="login id"/> 
<input type="hidden" id="del_patient" name="del_patient" value="" placeholder="patient name"/>
<input type="hidden" id="insurance_no" name="insurance_no" value="" placeholder="policy number"/> 
<input type="hidden" id="canDeleteTransmittal" name="canDeleteTransmittal" value="'.$canDeleteTransmittal.'" />
<input type="hidden" id="addpermission" name="addpermission" value="'.$canAddTransmittal.'" />';

$smarty->assign('hiddenFieldDelete',$fieldsHidden);
// END JEFF

$smarty->assign('sBtnSave',$linksave);

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'" method="POST" id="transmittal_form" name="transmittal_form" onSubmit="return validate();">');
$smarty->assign('sFormEnd','</form>');

ob_start();
?>
<input type="hidden" id="encounter_nr" name="encounter_nr" value="">
<input type="hidden" id="newform" name="newform" value="">
<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sFormsHiddenInputs', $stemp);

ob_start();
?>
<input type="hidden" name="submitted" value="1" />


<input type="hidden" id="curtransmit_no" name="curtransmit_no" value="<? echo $curtransmit_no ?>" />
<input type="hidden" id="transmit_date" name="transmit_date" value="<? echo $transmit_date ?>" />

<input type="hidden" id="hcare_id" name="hcare_id" value="<?=(($submitted && !$saveok) ? $_POST['hcare_id'] : $nhcare_id)?>" >
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<input type="hidden" id="old_trnsmit_no" name="old_trnsmit_no" value="<?=(($submitted && $saveok) ? $_POST['transmit_no'] : $_GET['tr_nr'])?>">
<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"  />
<input type="hidden" name="create_id" id="create_id" value="<?= ( ((!isset($_SESSION['sess_login_userid']) || ($_SESSION['sess_login_userid'] == '')) ) ? $_SESSION["sess_temp_userid"] : $_SESSION["sess_login_userid"] ) ?>"/>
<div style="display:none" id="cases"></div>
<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenItems', $stemp);
ob_start();
?>
<input type="hidden" id="memcateg_enc" name="memcateg_enc" value="" />
<input type="hidden" id="categ_id" name="categ_id" value="" />
<input type="hidden" id="categ_desc" name="categ_desc" value="" />
<input type="hidden" id="enc_type" name="enc_type" value="" />
<input type="hidden" id="dischrgdtetm" name="dischrgdtetm" value="" />
<input type="hidden" id="doc_nr" name="doc_nr" value="" />
<input type="hidden" id="gender" name="gender" value="" />
<input type="hidden" id="barangay_nr" name="barangay_nr" value="" />
<input type="hidden" id="municipality_nr" name="municipality_nr" value="" />
<input type="hidden" id="oldinsurance_nr" name="oldinsurance_nr" value="" />
<input type="hidden" id="meminfosrc" name="meminfosrc" value="" />
<?php
$xtemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sDataEditHiddenInputs', $xtemp);
# Assign page output to the mainframe template

ob_start();
?>
<script type="text/javascript">
YAHOO.example.BasicRemote = function() {
		// Use an XHRDataSource -- for barangay
		var brgyDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_brgy_query.php");
		// Set the responseType
		brgyDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		brgyDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		brgyDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var brgyAC = new YAHOO.widget.AutoComplete("barangay", "barangay_container", brgyDS);
		brgyAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
		};
		brgyAC.generateRequest = function(sQuery) {
				return "?query="+sQuery+"&mun_nr="+document.getElementById('municipality_nr').value;
		};

		var munName = YAHOO.util.Dom.get("municipality");
		var brgyName = YAHOO.util.Dom.get("barangay");

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var brgyNr = YAHOO.util.Dom.get("barangay_nr");
		var brgyHandler = function(sType, aArgs) {
				var bmyAC  = aArgs[0]; // reference back to the AC instance
				var belLI  = aArgs[1]; // reference to the selected LI element
				var boData = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				brgyNr.value = boData[0];
				brgyName.value = boData[1];

				xajax_getMuniCityandProv(brgyNr.value);
		};
		brgyAC.itemSelectEvent.subscribe(brgyHandler);

		// Use an XHRDataSource --- for municipality or city
		var munDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_municity_query.php");
		// Set the responseType
		munDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		munDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		munDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var munAC = new YAHOO.widget.AutoComplete("municipality", "municipality_container", munDS);
		munAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
		};

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var munNr = YAHOO.util.Dom.get("municipality_nr");
		var munHandler = function(sType, aArgs) {
				var mmyAC  = aArgs[0]; // reference back to the AC instance
				var melLI  = aArgs[1]; // reference to the selected LI element
				var moData = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				munNr.value = moData[0];
				munName.value = moData[1];
				//xajax_getProvince(munNr.value);
				brgyNr.value = '';
				brgyName.value = '';
		};
		munAC.itemSelectEvent.subscribe(munHandler);

		// Use an XHRDataSource
		var icdDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/icd-query.php");
		// Set the responseType
		icdDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		icdDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		icdDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var icdAC = new YAHOO.widget.AutoComplete("icdCode", "icdContainer", icdDS);
		icdAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"float:left;width:15%\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
		};

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var myICDDesc = YAHOO.util.Dom.get("icdDesc");
		var icdHandler = function(sType, aArgs) {
				var myAC1 = aArgs[0]; // reference back to the AC instance
				var elLI1 = aArgs[1]; // reference to the selected LI element
				var oData1 = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				myICDDesc.value = oData1[1];
		};
		icdAC.itemSelectEvent.subscribe(icdHandler);

		// Use an XHRDataSource
		var icdDescDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/icddesc-query.php");
		// Set the responseType
		icdDescDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		icdDescDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		icdDescDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var icdDescAC = new YAHOO.widget.AutoComplete("icdDesc", "icdDescContainer", icdDescDS);
		icdDescAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"float:left;width:85%\">"+oResultData[0]+"</span><span style\"float:left;width:15%\">"+oResultData[1]+"</span>";
		};

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var myICD = YAHOO.util.Dom.get("icdCode");
		var icdDescHandler = function(sType, aArgs) {
				var myAC2 = aArgs[0]; // reference back to the AC instance
				var elLI2 = aArgs[1]; // reference to the selected LI element
				var oData2 = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				myICD.value = oData2[1];
		};
		icdDescAC.itemSelectEvent.subscribe(icdDescHandler);

		return {
				brgyDS: brgyDS,
				munDS: munDS,
				brgyAC: brgyAC,
				munAC: munAC,
				icdDS: icdDS,
				icdDescDS: icdDescDS,
				icdAC: icdAC,
				icdDescAC: icdDescAC
		};
}();
</script>
<!-- added by carl -->
<script type="text/javascript">
(function($) {
	$(document).ready(function() {
	    var x_timer;    
	    $("#transmit_no").keyup(function (e){
	        clearTimeout(x_timer);
	        var transmit_no = $(this).val();
	        x_timer = setTimeout(function(){
	        	var str = (transmit_no != " " && transmit_no.length != 0) ? transmit_no.length : 0;
	        	check_username_ajax(transmit_no, str);
	        }, 1000);
	    }); 

		function check_username_ajax(transmit_no, lengths){
		    $("#result").html(' <img src="img/ajax-loader.gif" />');
		   	$.post('ajax/billing-transmittal.server.php', {'transmit_no':transmit_no}, function(data) {
		   		if(data == 0 && lengths != 0){
					$("#result").html(' <img src="img/available.png" />');
					document.getElementById("btnSave").style.visibility = 'visible'
		   		}
		   		else{
		   			$("#result").html(' <img src="img/not-available.png" />');
		   			document.getElementById("btnSave").style.visibility = 'hidden'
		   		}
		    });
		}
	});
})(jQuery);
</script>

<?php
$xtemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTailScripts', $xtemp);

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('class',"class=\"yui-skin-sam\"");
$smarty->assign('sMainBlockIncludeFile','billing/billing_transmittal_form.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>