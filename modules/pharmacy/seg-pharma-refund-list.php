<?php
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Ordering::Create Refund");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Ordering::Create Refund");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
	var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

	function openPDF(ref) {
		window.open('seg-pharma-order.php'+URL_FORWARD+'&target=print&ref='+ref,'openPDF',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

	function pSearchClose() {
		cClick();
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

	var djConfig = { isDebug: true };
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
	
	function deleteItem(id) {
		var dform = document.forms[0]
		$('delete').value = id
		dform.submit()
	}
	
	function view(id) {
		overlib(
			OLiframeContent('apotheke-pass.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>&target=orderview&ref='+id+'&from=CLOSE_WINDOW', 800, 420, 'fSelEnc', 0, 'auto'),
			WIDTH,800, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
			CAPTION,'View order',
			MIDX,0, MIDY,0, 
			STATUS,'View order');
	}
	
	function serve(id) {
		overlib(
			OLiframeContent('apotheke-pass.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>&target=serveorder&ref='+id+'&from=CLOSE_WINDOW', 800, 420, 'fSelEnc', 0, 'auto'),
			WIDTH,800, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
			CAPTION,'Serve order',
			MIDX,0, MIDY,0, 
			STATUS,'Serve order');
	}
	
	function enableChildren(obj, enable) {
		var nodes=obj.childNodes;
		if (nodes) {
			for (var i=0;i<nodes.length;i++) {
				if (nodes[i].nodeName.toUpperCase() == 'INPUT' || nodes[i].nodeName.toUpperCase() == 'SELECT' ||
						nodes[i].nodeName.toUpperCase() == 'TEXTAREA') {
					nodes[i].disabled = !enable;
				}
				if (nodes[i].childNodes) {
					enableChildren(nodes[i], enable);
				}
			}
		}
	}
	
	function validate() {
	}
-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
include($root_path."include/care_api_classes/class_order.php");
$order = new SegOrder('pharma');


if ($_POST['delete']) {
	if ($order->deleteOrder($_POST['delete'])) {
		$sWarning = 'Item successfully deleted!';
	}
	else {
		$sWarning = 'Error deleting order: '.$db->ErrorMsg();
	}
}

if ($_REQUEST['area']) {
	$filters["AREA"] = $_REQUEST['area'];
}

if ($_REQUEST['chkrefno']) {
	$filters["REFNO"] = $_REQUEST['refno'];
}

if ($_REQUEST['chkdate']) {
	switch(strtolower($_REQUEST["seldate"])) {
		case "today":
			$search_title = "Today's Paid/Urgent Requests";
			$filters['DATETODAY'] = "";
		break;
		case "thisweek":
			$search_title = "This Week's Paid/Urgent Requests";
			$filters['DATETHISWEEK'] = "";
		break;
		case "thismonth":
			$search_title = "This Month's Paid/Urgent Requests";
			$filters['DATETHISMONTH'] = "";
		break;
		case "specificdate":
			$search_title = "Paid/Urgent Requests On " . date("F j, Y",strtotime($_REQUEST["specificdate"]));
			$dDate = date("Y-m-d",strtotime($_REQUEST["specificdate"]));				
			$filters['DATE'] = $dDate;
		break;
		case "between":
			$search_title = "Paid/Urgent Requests From " . date("F j, Y",strtotime($_REQUEST["between1"])) . " To " . date("F j, Y",strtotime($_REQUEST["between2"]));
			$dDate1 = date("Y-m-d",strtotime($_REQUEST["between1"]));
			$dDate2 = date("Y-m-d",strtotime($_REQUEST["between2"]));
			$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
		break;
	}
}

if ($_REQUEST['chkpayee']) {
	switch(strtolower($_REQUEST["selpayee"])) {
		case "name":
			$filters["NAME"] = $_REQUEST["name"];
		break;
		case "pid":
			$filters["PID"] = $_REQUEST["pid"];
		break;
		case "patient":
			$filters["PATIENT"] = $_REQUEST["patient"];
		break;
		case "inpatient":
			$filters["INPATIENT"] = $_REQUEST["inpatient"];
		break;
	}
}

if ($_REQUEST['chkserve']) {
	$filters["SERVE"] = $_REQUEST['selserve'];
}

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

$result = $order->getServeReadyOrders($filters, $list_rows * $current_page, $list_rows);
$rows = "";
$last_page = 0;
$count=0;	
if ($result) {
	$rows_found = $order->FoundRows();
	if ($rows_found) {
		$last_page = floor($rows_found / $list_rows);
		$first_item = $current_page * $list_rows + 1;
		$last_item = ($current_page+1) * $list_rows;
		if ($last_item > $rows_found) $last_item = $rows_found;
		$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
	}
	while ($row=$result->FetchRow()) {
		if ($row["is_cash"] == "0") {
			$urgency = "Charge";
		}
		else {
			#$urgency = ($row["is_tpl"]=="1")?"<span style=\"color:#e42400\">Cash/TPL</span>":"Cash/Normal";	
			if ($row['amount_due']==0) {
				$urgency = "<span style=\"color:#248000\">No charge</span>";
			}
			elseif ($row["amount"]==0) {
				$urgency = "<span style=\"color:#248000\">Social Worker</span>";
			}
			elseif ($row["is_paid"]=="1")
				$urgency = '<img src="'.$root_path.'images/paid_item.gif" align="absmiddle"/>';
		}
		if ($row["pid"]) 
			$name = $row["name_last"].", ".$row["name_first"]." ".$row["name_middle"];
		else
			$name = $row["ordername"];
		if (!$name) $name='<i style="font-weight:normal">No name</i>';
		$class = (($count%2)==0)?"":"wardlistrow2";
		$items = explode("\n",$row["items"]);
		$items = implode(", ",$items);
			
		$total_items = (int) $row['count_total_items'];
		$total_served = (int) $row['count_served_items'];

		if ($total_items == 0) $served = '-';
		else {
			if ($total_items == $total_served) $served = "<span style=\"color:#00c\">Served</span>";
			elseif ($total_served == 0) $served = "<span style=\"color:#c00\">Not served</span>";
			else $served = "<span style=\"color:#c00\">Not served</span>";
		}
		$records_found = TRUE;
		$rows .= "		<tr class=\"$class\">
				<td align=\"center\">
					".substr($row["orderdate"],0,10)."
					<!-- <a href=\"seg-pharma-order.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid&target=list&view=date&sdate=".urlencode(str_replace("-","",$row["orderdate"]))."\">
						".substr($row["orderdate"],0,10)."
					</a> -->
				</td>
				<td style=\"color:#000066\">".$row["refno"]."</td>
				<td>".$name."</td>
				<td style=\"color:#660000\">".$items."</td>
				<td align=\"center\" id=\"serve_".$row["refno"]."\">$served</td>
				<td style=\"color:#007\" align=\"center\">$urgency</td>
				<td style=\"color:#007\" align=\"center\">".$row["area_full"]."</td>
				<td align=\"right\" nowrap=\"nowrap\">
					<!-- <a title=\"Serve\" href=\"apotheke-pass.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid&target=serveorder&ref=".$row["refno"]."&from=servelist\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_check.png\" border=\"0\" align=\"absmiddle\" /></a> -->
					<a title=\"Serve\" href=\"javascript:serve('".$row["refno"]."')\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_check.png\" border=\"0\" align=\"absmiddle\"/></a>
					<a title=\"View\" href=\"javascript:view('".$row["refno"]."')\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_view.png\" border=\"0\" align=\"absmiddle\"/></a>
					<a title=\"Delete\" href=\"#\">
						<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this order?')) deleteItem('".$row["refno"]."')\"/>
					</a>
				</td>
			</tr>\n";
		$count++;
	}
}
else {
	print_r($result);
	$rows .= '		<tr><td colspan="10">'.$order->sql.'</td></tr>';
}

if (!$rows) {
	$records_found = FALSE;
	$rows .= '		<tr><td colspan="10">No orders/requests available at this time...</td></tr>';
}

# Default payee mode selection
if (!$_REQUEST["selpayee"]) $_REQUEST["selpayee"]="name";
ob_start();
?>

<br>

<form action="<?= $thisfile.URL_APPEND."&target=servelist&clear_ck_sid=".$clear_ck_sid ?>&area=<?= $_REQUEST['area'] ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:60%">
	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">	
		<tbody>
			<tr>
				<td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
			</tr>	
			<tr>
				<td nowrap="nowrap" align="right" class="jedPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="50" align="right"><input type="checkbox" id="chkrefno" name="chkrefno" <?= ($_REQUEST['chkrefno'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdRefNo'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkrefno" class="jedInput">Reference #</label></td>
							<td id="tdRefNo">
								<input class="jedInput" name="refno" id="refno" type="text" size="20" value="<?= $_REQUEST['refno'] ?>" <?= $_REQUEST['chkrefno'] ? '' : 'disabled="disabled"' ?>/>
							</td>							
						</tr>
						<tr>
							<td align="right">
								<input type="checkbox" id="chkpayee" name="chkpayee" <?= ($_REQUEST['chkpayee'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdPayee'),this.checked)"/>
							</td>
							<td width="5%" align="left" nowrap="nowrap"><label for="chkpayee" class="jedInput">Select payee</label></td>
							<td id="tdPayee">
<script language="javascript" type="text/javascript">
<!--
	function selpayeeOnChange() {
		var optSelected = $('selpayee').options[$('selpayee').selectedIndex];
		var spans = document.getElementsByName('selpayeeoptions');
		
		for (var i=0; i<spans.length; i++) {
			if (optSelected) {
				if (spans[i].getAttribute("segOption") == optSelected.value) {
					spans[i].style.display = "";
				}
				else
					spans[i].style.display = "none";
			}
		}
		
		disableNav()
	}
-->
</script>
								<select class="jedInput" name="selpayee" id="selpayee" onchange="selpayeeOnChange()"  <?= $_REQUEST['chkpayee'] ? '' : 'disabled="disabled"' ?>/>
									<option value="name" <?= $_REQUEST["selpayee"]=="name" ? 'selected="selected"' : '' ?>>Payee Name</option>
									<option value="pid" <?= $_REQUEST["selpayee"]=="pid" ? 'selected="selected"' : '' ?>>Patient ID</option>
									<option value="patient" <?= $_REQUEST["selpayee"]=="patient" ? 'selected="selected"' : '' ?>>Patient Records</option>
									<option value="inpatient" <?= $_REQUEST["selpayee"]=="inpatient" ? 'selected="selected"' : '' ?>>Inpatient</option>
								</select>
								<span name="selpayeeoptions" segOption="name" <?= ($_REQUEST["selpayee"]=="name") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="name" id="name" type="text" size="20" value="<?= $_REQUEST['name'] ?>" <?= $_REQUEST['chkpayee'] ? '' : 'disabled="disabled"' ?>/>
									<input type="hidden" name="name_old" value="<?= $_REQUEST['name'] ?>" />
								</span>
								<span name="selpayeeoptions" segOption="pid" <?= ($_REQUEST["selpayee"]=="pid") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="pid" id="pid" type="text" size="20" value="<?= $_REQUEST['pid'] ?>" <?= $_REQUEST['chkpayee'] ? '' : 'disabled="disabled"' ?>/>
								</span>
								<span name="selpayeeoptions" segOption="patient" <?= ($_REQUEST["selpayee"]=="patient") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="patientname" id="patientname" readonly="readonly" type="text" size="20" value="<?= $_REQUEST['patientname'] ?>" <?= $_REQUEST['chkpayee'] ? '' : 'disabled="disabled"' ?>/>
									<input name="patient" id="patient" type="hidden" value="<?= $_REQUEST['patient'] ?>"/>
									<input type="image" id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
						       onclick="overlib(
						      OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_pid=patient&var_name=patientname', 700, 400, 'fSelEnc', 0, 'auto'),
				    	    WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?= $root_path ?>images/close.gif border=0 >',
					        CAPTIONPADDING,4, 
									CAPTION,'Select registered person',
					        MIDX,0, MIDY,0, 
				        	STATUS,'Select registered person'); return false;"
  	 							onmouseout="nd();" />
								</span>
								<span name="selpayeeoptions" segOption="inpatient" <?= ($_REQUEST["selpayee"]=="inpatient") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="inpatientname" id="inpatientname" readonly="readonly" type="text" size="20" value="<?= $_REQUEST['inpatientname'] ?>"/>
									<input name="inpatient" id="inpatient" type="hidden" value="<?= $_REQUEST['inpatient'] ?>"/>
									<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
				    		  onclick="overlib(
					        OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_encounter_nr=inpatient&var_name=inpatientname&var_include_enc=1', 700, 400, 'fSelEnc', 0, 'auto'),
					        WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?= $root_path ?>images/close.gif border=0 >',
						      CAPTIONPADDING,4, 
									CAPTION,'Select registered person',
				      	  MIDX,0, MIDY,0, 
			        		STATUS,'Select registered person'); return false;"
	      				 onmouseout="nd();" />
								</span>
							</td>
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_REQUEST['chkdate'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdDate'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkdate" class="jedInput">Select date</label></td>
							<td id="tdDate">
<script language="javascript" type="text/javascript">
<!--
	function seldateOnChange() {
		var optSelected = $('seldate').options[$('seldate').selectedIndex]
		var spans = document.getElementsByName('seldateoptions')
		for (var i=0; i<spans.length; i++) {
			if (optSelected) {
				if (spans[i].getAttribute("segOption") == optSelected.value) {
					spans[i].style.display = ""
				}
				else
					spans[i].style.display = "none"
			}
		}
		
		disableNav()
	}
-->
</script>
								<select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange()" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>>
									<option value="today" <?= $_REQUEST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
									<option value="thisweek" <?= $_REQUEST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
									<option value="thismonth" <?= $_REQUEST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
									<option value="specificdate" <?= $_REQUEST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
									<option value="between" <?= $_REQUEST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
								</select>
								<span name="seldateoptions" segOption="specificdate" <?= ($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_REQUEST['specificdate'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
										});
									</script>
								</span>
								<span name="seldateoptions" segOption="between" <?= ($_REQUEST["seldate"]=="between") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="between1" id="between1" type="text" size="8" value="<?= $_REQUEST['between1'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
										});
									</script>
									to
									<input class="jedInput" name="between2" id="between2" type="text" size="8" value="<?= $_REQUEST['between2'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
										});
									</script>
								</span>
							</td>
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkserve" name="chkserve" <?= ($_REQUEST['chkserve'] ? 'checked="checked"' : '') ?>  onclick="enableChildren($('tdServe'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkserve" class="jedInput">Serve status</label></td>
							<td id="tdServe">
								<select class="jedInput" id="selserve" name="selserve" <?= $_REQUEST['chkserve'] ? '' : 'disabled="disabled"' ?>>
									<option value="">- Select one -</option>
									<option value="N" <?= $_REQUEST["selserve"]=="N" ? 'selected="selected"' : '' ?>>Not served</option>
									<option value="P" <?= $_REQUEST["selserve"]=="P" ? 'selected="selected"' : '' ?>>Partially served</option>
									<option value="S" <?= $_REQUEST["selserve"]=="S" ? 'selected="selected"' : '' ?>>Served</option>
								</select>
							</td>							
						</tr>
						<tr>
							<td></td>
							<td colspan="2">
								<input type="submit" value="Search"  class="jedButton"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:90%">
	<div class="segContentPaneHeader" style="margin-top:10px">
		<h1>
			Search result: 
<?php
	echo $search_title;
?>
		</h1>
	</div>


	<div class="segContentPane">
		<table id="" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
					<th width="10%"><a href="#">Date</a></th>
					<th width="10%">Ref No.</th>
					<th width="15%">Name</th>
					<th width="*">Items</th>
					<th width="4%">Status</th>
					<th width="4%">Priority</th>
					<th width="4%">Area</th>
					<th width="6%">Details</th>
				</tr>
			</thead>
			<tbody>
<?= $rows ?>
			</tbody>
		</table>
		<br />
	</div>
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

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

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