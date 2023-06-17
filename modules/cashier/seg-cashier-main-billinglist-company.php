<?php
/*added by art 05/17/2017
** for ic company search in cashier
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
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

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";
$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Process billing");

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Process billing");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js" ></script>
<script type="text/javascript">
	function tabClick(el) {
		if (el.className!='segActiveTab') {
			$('mode').value = el.getAttribute('segSetMode');
			var dList = $(el.parentNode);
			if (dList) {
				var listItems = dList.getElementsByTagName("LI");
				for (var i=0;i<listItems.length;i++) {
					if (listItems[i] != el) {
						listItems[i].className = "";
						if ($(listItems[i].getAttribute('segTab'))) $(listItems[i].getAttribute('segTab')).style.display = "none";
					}
				}
				if ($(el.getAttribute('segTab'))) 
					$(el.getAttribute('segTab')).style.display = "block";
				el.className = "segActiveTab";
			}
		}
	}
	
	function prepareAdd(id) {
		var details = new Object();
		details.id = $('id_'+id).value;
		details.name = $('name_'+id).value;
		details.desc = "IC Hospital Bill";
		details.qty = 1;
		details.origprice = $('price_'+id).value;
		details.price = $('adjusted'+id).value;//price adjusted
		details.ispaid = $('paid_'+id).value;
		details.calculate= 1;
		details.checked= 1;
		details.showdel= 1;
		details.limit= -1;
		details.doreplace = 1;
		details.src = 'ic';
		details.ref = '0000000000';
		result = window.parent.addServiceToList(details);
		if (result) {
			alert("Bill entry added to payment list...");
		}
	}
	
	function disableNav() {
		with ($('pageFirst')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
		with ($('pagePrev')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
		with ($('pageNext')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
		with ($('pageLast')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
	}

	var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
	
	function jumpToPage(jumptype, page) {
		var form1 = document.forms[0];
	
		switch (jumptype) {
			case FIRST_PAGE:
				$('jump').value = 'first';
			break;
			case PREV_PAGE:
				$('jump').value = 'prev';
			break;
			case NEXT_PAGE:
				$('jump').value = 'next';
			break;
			case LAST_PAGE:
				$('jump').value = 'last';
			break;
			case SET_PAGE:
				$('jump').value = page;
			break;
		}
		form1.submit();
	}
	
	
	function pSearchClose() {
		$('search').disabled = true
		cClick()
		document.forms[0].submit()
	}
	
function myBlindUp(id) {
	var element = 'd'+id;
	element = $(element);
	element.makeClipping();
	element.style.overflowX = 'hidden';
	element.style.overflowY = 'scroll';
	return new Effect.Scale(element, 0,
		Object.extend({ scaleContent: false, 
			scaleX: false, 
			restoreAfterFinish: true,
			afterFinishInternal: function(effect) {
				effect.element.hide().undoClipping();
				$('details-'+id).style.display = 'none';
			} 
		}, arguments[1] || { })
	);
};

function myBlindDown (id) {
	var element = 'd'+id;
	element = $(element);
	$('details-'+id).style.display = '';
	var elementDimensions = element.getDimensions();
	elementDimensions.height = 180;
	return new Effect.Scale(element, 100, Object.extend({ 
		scaleContent: false, 
		scaleX: false,
		scaleFrom: 0,
		scaleMode: {originalHeight: elementDimensions.height, originalWidth: elementDimensions.width},
		restoreAfterFinish: false,
		afterSetup: function(effect) {
			effect.element.makeClipping().setStyle({height: '0px'}).show();
			element.style.overflowX = 'hidden';
			element.style.overflowY = 'scroll';
		},  
		afterFinishInternal: function(effect) {
			effect.element.undoClipping();
			element.style.overflowX = 'hidden';
			element.style.overflowY = 'scroll';
		}
	}, arguments[1] || { }));
};

function toggleDetails(id) {
	if ($('details-'+id)) {
		var show = ($('details-'+id).getAttribute('hideDetails') != 1);
		$('details-'+id).setAttribute('hideDetails',(show ? '1' : ''));
	}
	else { }
	
	var dt, nr;
	dt = $('d'+id);
	nr = $('id_'+id).value;
	if (dt) {
		if (!dt.src) setTimeout("$('d"+id+"').src='seg-cashier-main-billingdetails.php?nr="+nr+"'",300);
		if (show) {
			myBlindDown(id, { duration:0.2 });
		}
		else {
			myBlindUp(id, { duration:0.2 });
		}
		//if ($('expand-'+id)) $('expand-'+id).style.display = show ? "none" : "";
		//if ($('collapse-'+id)) $('collapse-'+id).style.display = show ? "" : "none";
	}
}
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
global $db;

require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
$cClass = new SegICTransaction();

# Resolve current page (pagination)
$current_page = $_REQUEST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 15;
switch (strtolower($_REQUEST['jump'])) {
	case 'last':
		$current_page = $_REQUEST['lastpage'];
	break;
	case 'prev':
		if ($current_page > 0) $current_page--;
	break;
	case 'next':
		if ($current_page < $_REQUEST['lastpage']) $current_page++;
	break;
	case 'first': default:
		$current_page=0;
	break;
}	

$count=0;
$rows = "";
$last_page = 0;
if ($_POST['submitted'] || $_REQUEST['patient']) {
	$patient = $_REQUEST['patient'];
	$result = $cClass->getBill(str_replace('IC', '', $patient));
	if ($result) {			
		$rows_found = $cClass->FoundRows();
		if ($rows_found) {
			$last_page = floor($rows_found / $list_rows);
			$first_item = $current_page * $list_rows + 1;
			$last_item = ($current_page+1) * $list_rows;
			if ($last_item > $rows_found) $last_item = $rows_found;
			$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
		}
		while ($row=$result->FetchRow()) {
			$class = (($count%2)==0)?"":"alt";
			$bill_date = date("Y-m-d h:ia",strtotime($row["bill_rundate"]));
			$cut_off = date("Y-m-d h:ia",strtotime($row["cutoff_date"]));
			$name = $row["name"];
			ob_start();
?>
			<tr class="<?= $class ?>">
				<td style="white-space:nowrap">
					<input type="hidden" id="id_<?= $count ?>" value="<?= $row['bill_nr'] ?>" />
					<span class="segSimulatedLink" onclick="toggleDetails('<?= $count ?>')" style="cursor:pointer;color:#600000">
					<?= $row["bill_nr"] ?>
					<img src="<?= $root_path ?>images/msgarrow.gif" align=\"absmiddle\"></span>
				</td>
				<td style="color:#000080" class="centerAlign"><?= $bill_date ?></td>
				<td style="color:#000080" class="centerAlign"><?= $cut_off ?></td>
				<td style="color:#008000" ><?= $row["encounter_nr"] ?></td>
				<td>
					<input type="hidden" id="name_<?= $count ?>" value="<?= "$name ($bill_date)" ?>" />
					<span><?= $name ?></span>
				</td>
				<td style="color:#008000" align="right">
					<input type="hidden" id="price_<?= $count ?>" value="<?= $row['total'] ?>"/>
					<?= number_format((float)$row['total'],2) ?>
					<input type="hidden" id="adjusted<?= $count ?>" value="<?= $val = $row['total'] - $row['discount_amount'] ?>"/>
				</td>
				<td align="center">
					<input type="hidden" id="paid_<?= $count ?>" value="<?= ($row['request_flag'] ? "1" : "") ?>"/>
					<?= ($row["request_flag"] ? ("<img src=\"".$root_path."images/flag_{$row['request_flag']}.gif\" align=\"absmiddle\" />") : "") ?>
				</td>
				<?php $prepare = $row['request_flag'] ? "alert('bill is already paid')" : "prepareAdd('$count')" ;?>
				<td class="centerAlign"><input class="segButton" type="button" style="color:#000060" value=">" onclick="<?= $prepare ?>" /></td>
			</tr>
			<tr id="details-<?= $count ?>" style="display:none;">
				<td id="detailstd-<?= $count ?>" style="padding:3px;background-color:#003366" class="plain" colspan="8" align="center">
					<div>
						<iframe id="d<?= $count ?>" frameborder="0" scrolling="yes" style="height:160px;overflow-x:hidden;overflow-y:scroll;width:100%;background-color:white;display:block"></iframe>
					</div>
				</td>
			</tr>
<?php
			$rows.=ob_get_contents();
			ob_end_clean();
			$count++;
		}
		if (!$rows)
			$rows = '		<tr><td colspan="10">No billing records found for this patient...</td></tr>';
	}
	else
		$rows = '		<tr><td colspan="10">'.$cClass->sql.'</td></tr>';
}

if (!$rows)
	$rows = '		<tr><td colspan="10">Select patient record and click Search to begin query...</td></tr>';


ob_start();
?>

<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">

<input type="hidden" name="submitted" value="1">
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">

<div style="width:98%; margin:0px; margin-top:10px" align="center">
	<div  style="width:100%; margin:0px" align="center">
		<table class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
			<thead>
				<tr class="nav">
					<th colspan="9">
						<div id="pageFirst" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(FIRST_PAGE)">
							<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
							<span title="First">First</span>
						</div>
						<div id="pagePrev" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(PREV_PAGE)">
							<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
							<span title="Previous">Previous</span>
						</div>
						<div id="pageShow" style="float:left; margin-left:10px">
							<span><?= $nav_caption ?></span>
						</div>
						<div id="pageLast" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(LAST_PAGE)">
							<span title="Last">Last</span>
							<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
						</div>
						<div id="pageNext" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(NEXT_PAGE)">
							<span title="Next">Next</span>
							<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
						</div>
					</th>
				</tr>
				<tr>
					<th width="10%" nowrap="nowrap">Bill Nr.</th>
					<th width="12%" nowrap="nowrap">Bill Date</th>
					<th width="12%" nowrap="nowrap">Cut off</th>
					<th width="1" nowrap="nowrap"></th>
					<th width="*%" nowrap="nowrap">Company Name</th>
					<th width="15%" nowrap="nowrap">Amount Due</th>
					<th width="10%" nowrap="nowrap">Status</th>
					<th width="1%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
<?= $rows ?>
			</tbody>
		</table>
	</div>
</div>
<br />

<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

</form>
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

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>