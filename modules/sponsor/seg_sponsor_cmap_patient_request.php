<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/sponsor/class_cmap_request.php';
require_once $root_path."include/care_api_classes/sponsor/class_cmap_patient.php";
require_once $root_path."modules/sponsor/ajax/cmap_patient_request.common.php";

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');

$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

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

# $phpfd = config date format in PHP date() specification
 #$title="Sponsor grants";

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-sponsor-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg_sponsor_cmap_patient_request.php';

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/sponsor/class_cmap.php");
$cc = new SegCMAP;

include_once($root_path."include/care_api_classes/sponsor/class_cmap_patient.php");
$pc = new SegCMAPPatient;

global $db;

$Nr = $_GET['nr'];

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('bHideTitleBar',TRUE);

# Assign Body Onload javascript code

# Collect javascript code
ob_start();
# Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<link href="<?=$root_path?>js/prototypeui/themes/window/window.css" rel="stylesheet" type="text/css">
<link href="<?=$root_path?>js/prototypeui/themes/window/alphacube.css" rel="stylesheet" type="text/css">
<link href="<?=$root_path?>js/prototypeui/themes/window/lighting.css" rel="stylesheet" type="text/css">
<link href="<?=$root_path?>js/prototypeui/themes/shadow/mac_shadow.css" rel="stylesheet" type="text/css">
<style type='text/css'>
.message {
	font-family: Georgia;
	text-align: center;
	margin-top: 20px;
}

.spinner {
	background: url(../../images/spinner.gif) no-repeat center center;
	height: 40px;
}

.container {
	font: normal 12px Tahoma;
	padding: 4px 5px;
	margin: 0;
	background-color: #E4E9F4;
	border: 5px solid #4E8CCF;
	border-width: 5px 0px;
	-moz-border-radius: 0px 0px 6px 6px;
}

.container h1 {
	display: none;
	background-repeat: no-repeat;
	font-family: Tahoma;
	font-size: 18px;
	font-weight: normal;
	color: #580408;
	vertical-align:middle;
	margin: 0;
	padding: 0;
	padding-top: 6px;
	padding-left: 36px;
	height: 30px;
}

.container p {
	font: normal 11px Tahoma;
	color: #2d2d2d;
	margin: 3px 0px;
}

.errorfg {
	background-color: #cccccc;
}

.clearbg {
	background-color: transparent;
	border: 0;
}

.clearcg {
	background-color: transparent;
	background-image: none;
	text-align:center;
	margin:0;
}

.clearcgif {
	background-color: transparent;
	text-align: center;
}

.clearfg {
	background-color: transparent;
	text-align: center;
}

.clearfgif {
	background-color: none;
	text-align: center;
}

.clearcap {
	display: none;
	font-family:Tahoma;
	font-size:11px;
	font-weight:bold;
	color:white;
	margin-top:0px;
	margin-bottom:1px;
}

a.clearclo {
	display: none;
	font-family:Verdana;
	font-size:11px;
	font-weight:bold;
	color:#ddddff;
}

.cleartext {
	font:bold 11px Tahoma;
	color:#2d2d2d;
}
</style>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript"> var $J =jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/prototypeui/window.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript">
eraseCookie('__cmap_ck');

var glst, grid, buffer,
	isLoading=false;

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function addPatientRequest(details) {
	list = $('rlst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (typeof(details)=='object') {
				var source=details.source,
					nr=details.refno,
					item=details.itemno,
					entry=details.entry,
					id=source+nr+item+entry,
					date=details.date,
					name=details.name,
					desc=details.desc,
					qty=details.qty,
					status=details.status,
					price=details.price,
					total=details.price*details.qty,
					grant=details.grant,
					due=(total-details.grant)-details.creditgrant,
					flag=details.flag,
					disabled=(details.disabled=='1'),
					served = details.served;

				// Fallback for bizarre cases where grant amount is greater than actual price
				if (due<0) due=0;

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				var options;

				if ((flag && due==0) || served=="1") {
					// alert("xx");
					if (served == "1")
					{
						options = new Element('img',
							{ src:'../../images/flag_served.gif', title: flag, align:'absmiddle' }
						);
					}
					else if (flag === 'cmap') {
						options = new Element('span').update(
							new Element('img',
								{ src:'../../images/flag_'+flag+'.gif', title: flag, align:'absmiddle' }
							).setStyle({ margin:'1px' })
						).insert(
							new Element('img',
								{ class:'link', src:'../../images/cashier_delete_small.gif', title: 'Remove grant', align:'absmiddle' }
							).setStyle({ margin:'1px', display:'none' })
						);
					}
					else {
						options = new Element('img',
							{ src:'../../images/flag_'+flag+'.gif', title: flag, align:'absmiddle' }
						);
					}
				}
				else {
					options = new Element('button',
						{ id:'ri_add_'+id, class:'segButton' }
					).observe( 'click',
						function(event) {
							openGrant( { src: source, nr: nr, code: item, entry: entry} )
						}
					).update(
						new Element('img', { src:'../../gui/img/common/default/add.png'})
					).insert('Grant');

				}

				var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_date_'+id}).update(date)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_nr_'+id}).update(nr)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'ri_name_'+id }).update(name)
					).insert(
						new Element('div', { id:'ri_desc_'+id } ).setStyle({
							color: '#000080',
							font: 'normal 10px Arial'
						}).update(desc)
					).insert(
						new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_qty_show_'+id }).update(qty)
					).insert(
						new Element('input', { id:'ri_qty_'+id, type:'hidden', value:qty } )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_total_'+id}).update( formatNumber(total,2)
						).setStyle({
							fontWeight: 'bold'
						})
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_due_'+id}).update( formatNumber(due,2)
						).setStyle({
							fontWeight: 'bold',
							color:(due<total ? '#008000' : '#000000')
						})
					)
				).insert(
					new Element('td', { class:'centerAlign' }).update( options )
				);
				dBody.insert(row);
			}
			else {
				dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
			}
			return true;
		}
	}
	return false;
}

function calculateTotals() {
	var totals=$$('[name="amount[]"]');
	var total=0;
	if (totals) {
		totals.each( function (x) { total+=parseFloatEx(x.value) } );
	}

	var bal = parseFloatEx($('show-account').getAttribute('value'));
	$('show-total').update(formatNumber(total,2) ).setAttribute('value', total);
	$('show-balance').update(formatNumber(bal-total,2) ).setAttribute('value', bal-total);
}

//function addCMAPItem(details) {
//	var container = $('hidden-inputs');
//	if (container && typeof(details)=='object') {
//		var source=details["source"],
//				nr=details["nr"],
//				item=details["item"],
//				name=details["name"],
//				qty=details["qty"],
//				amount=details["amount"],
//				id=source+nr+item,
//				disabled=(details["disabled"]=='1');

//		var row = new Element('fieldset', { id:'li_'+id , style:'display:none' } ).update(
//				new Element('input', { id:'li_itemno_'+id, name:'item[]', type:'hidden', value:item } )
//			).insert(
//				new Element('input', { id:'li_source_'+id, name:'src[]', type:'hidden', value:source } )
//			).insert(
//				new Element('input', { id:'li_nr_'+id, name:'ref[]', type:'hidden', value:nr } )
//			).insert(
//				new Element('input', { id:'li_qty_'+id, name:'qty[]', type:'hidden', value:qty } )
//			).insert(
//				new Element('input', { id:'li_amount_'+id, name:'amount[]', type:'hidden', value:amount } )
//			).insert(
//				new Element('input', { id:'li_name_'+id, name:'service[]', type:'hidden', value:name } )
//			);

//		container.insert(row);
//		return true;
//	}
//	else
//		return false;
//}

function removeItem(id) {
	var removeObj=$('li_'+id);
	if (removeObj) {
		removeObj.remove();
		return true;
	}
	else
		return false;
}

function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('<img src="../../images/loading6.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			SHADOW, 0,
			MODALCOLOR, '#ffffff',
			MODALOPACITY, 80,
			FGCLASS, 'clearfg',
			CGCLASS, 'clearcg',
			BGCLASS, 'clearbg',
			TEXTFONTCLASS, 'cleartext',
			CAPTIONFONTCLASS, 'clearcap',
			CLOSEFONTCLASS, 'clearclo',
			STICKY, MODAL,
			CLOSECLICK, TIMEOUT, 0, OFFDELAY, 0,
			CAPTION,'Loading',
			MIDX,0, MIDY,0,
			STATUS,'Loading');
	}
}

function doneLoading() {
	if (isLoading) {
		setTimeout('cClick()', 500);
		isLoading = 0;
	}
}

//function clearErroneousInputs( ) {
//	var clearErrors = $$('.errorInput');
//	if (clearErrors) clearErrors.each( function(x) { x.removeClassName('errorInput') } );
//}

function validate(wId) {
	if (isNaN($(wId+'_amount').value) || parseFloatEx($(wId+'_amount').value) <= 0) {
		alert("Invalid grant amount specified...");
		return false;
	}
	return true;
}

function openGrant(args) {
	var wm = UI.defaultWM;
	var windows = wm.windows();
	if (windows.length)
		return false;

	// Reminder: check for duplicate windows

	var w = new UI.Window({
		width: 360,
		height: 220,
		shadow: true,
		minimize: false,
		maximize: false,
		resizable: false,
		draggable: true,
		show: Element.appear,
		hide: Element.fade,
		method: "GET"
	});
	w.content.update('<div class="message">Please wait...</div><div class="spinner"></div>');
	w.show(true);
	params = Object.toQueryString(args);
	w.setAjaxContent('ajax/cmap_grant.ajax.php?wid='+w.id+'&'+params, { method: "GET" });
	w.center();
	w.observe('hidden', function() {
		rlst.reload();
	});
	w.activate();
}

function closeGrant(wId) {
	var wm = UI.defaultWM;
	var windows = wm.windows();
	windows.each(function(win) { if (win.id == wId) win.destroy(); });
}

function save(wId) {
	if (validate(wId)) {
		startLoading();
		xajax.call( 'save', { parameters:[wId, xajax.getFormValues(wId+'_transfer_data')] } );
	}
}

function tooltip (text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function partialAmount(wId) {
	var amt;
	var balance=$(wId+'_referral').value;
	var due=$(wId+'_due').value;
	while (isNaN(amt)) {
		amt = prompt('Enter amount to be transferred:');
		if (amt===null) return false;
		if (parseFloatEx(amt) > parseFloatEx(balance)) amt=balance;
		if (parseFloatEx(amt) > parseFloatEx(due)) amt=due;
	}

	$(wId+'_amount').value = amt;
	$(wId+'_show_amount').value = formatNumber(amt,2);
}

function fullAmount(wId) {
	var balance=parseFloatEx($(wId+'_referral').value);
	var due=parseFloatEx($(wId+'_due').value);
	var amt;

	amt = balance;
	if (amt>due) amt=due;

	$(wId+'_amount').value = amt;
	$(wId+'_show_amount').value = formatNumber(amt,2);
}

function search() {
	//if (/\d{4}-\d{2}-\d{2}/.match(this.value)) { $(this).setStyle({color:\'\'}); if(rlst.fetcherParams) rlst.fetcherParams[\'date\']=this.value;rlst.reload(); } else { $(this).setStyle({color:\'red\'}); }
	var o = new Object;
	o['PID'] = '<?= htmlentities($_GET['pid']) ?>';
	//o['ENCOUNTER'] = '<?= htmlentities($_GET['nr']) ?>';
	if ($('basic-source').value) {
		o['FILTER_SOURCE'] = $('basic-source').value;
	}
	if ($('basic-name').value) {
		o['FILTER_NAME'] = $('basic-name').value;
	}
	if ($('date_request').value) {
		o['FILTER_DATE'] = $('date_request').value;
	}
	if ($('basic-grant').value) {
		o['FILTER_FLAG'] = $('basic-grant').value;
	}
	rlst.fetcherParams = o;
	rlst.reload();
	return false;
}

function sendPocHl7Msg(pocitems) {    
    var oitems = JSON.parse(pocitems);
    $J.ajax({
        type: 'POST',
        url: '../../index.php?r=poc/order/triggerCbgOrder',
        data: { test: JSON.stringify(oitems[0]) },  
        success: function(data) {
                    swal.fire({
                      position: 'top-end',
                      type: 'success',
                      title: 'Order sent to device!',
                      showConfirmButton: false,
                      timer: 1500
                    })
                },
        error: function(jqXHR, exception) {
                    console.log(jqXHR.responseText)
                    swal.fire({
                      position: 'top-end',
                      type: 'error',
                      title: jqXHR.responseText,
                      showConfirmButton: false,
                      timer: 1500
                    })
                },
        dataType: 'json'                  
    });     
}

</script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

$rlst = $listgen->createList(
	array(
		'LIST_ID' => 'rlst',
		'COLUMN_HEADERS' => array('Date','Reference','Item name','Qty','Total','Due','Grant'),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populateRequestList',
		'INITIAL_MESSAGE' => "No cost center requests found for this patient...",
		'EMPTY_MESSAGE' => "No cost center requests found for this patient...",
		'ADD_METHOD' => 'addPatientRequest',
		'FETCHER_PARAMS' => array('PID'=>$_GET['pid'], 'SOURCE'=>SegCmapRequest::BILLING_REQUEST),
		'RELOAD_ONLOAD' => FALSE,
		'COLUMN_WIDTHS' => array("12%", "14%", "*", "8%", "12%", "12%", "12%")
	)
);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = 'MAP :: Process MAP request entry';

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Controls
$smarty->assign('sControlNo','<input id="control_nr" name="control_nr" class="segInput" type="text" value="'.$_POST["control_nr"].'" />');
//$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_GET["encounter_nr"].'"/>');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" value="'.$_GET['pid'].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="" readonly="readonly" value="'.$_GET['name'].'"/>');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="30" rows="2" style="">'.$_POST['remarks'].'</textarea>');

$time_format = "F j, Y";
$date_show = date($time_format,time());
@ob_start();
?>
<input type="text" name="date_request" id="date_request" class="segInput" value="" style="width:100px" readonly="readonly" />
<button id="date_request_trigger" class="segButton" onclick="return false;"><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
<button id="date_request_clear" class="segButton" onclick="$('date_request').value='';return false;"><img <?= createComIcon($root_path,'delete.png','0') ?>>Clear</button>
<script type="text/javascript">
	Calendar.setup ({
		inputField: "date_request",
		dateFormat: "%B %e, %Y",
		trigger: "date_request_trigger",
		showTime: false,
		onSelect: function() { this.hide() }
	});
</script>
<?php
$dateFilter = @ob_get_contents();
@ob_end_clean();
$smarty->assign('sRequestFilterDate', $dateFilter);

# Totals
$pc = new SegCMAPPatient;
$smarty->assign('sAccountBalance', $pc->getBalance($_GET['pid']));
$smarty->assign('sCoverageTotal',0);

$sources = SegCmapRequest::getRequestTypes();
$sourceOptions = "";
foreach ($sources as $i=>$source) {
	$sourceOptions .= "<option value=\"{$i}\">".htmlentities($source)."</option>";
}
$smarty->assign('sSources', "<select id=\"basic-source\" class=\"segInput\">\n".$sourceOptions."</select>");

# Save/Cancel buttons
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="inputForm" name="inputForm" onSubmit="return false">');
$smarty->assign('sFormEnd','</form>');

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
<input type="hidden" id="refno" name="refno" value="">
<input type="hidden" id="refsource" name="refsource" value="">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="link" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="link" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/cmap_patient_request.tpl');
$smarty->display('common/mainframe.tpl');

