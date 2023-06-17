<?php
# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

include_once($root_path."include/care_api_classes/class_order.php");

$order_obj = new SegOrder("pharma");

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

    
// added by carriane 10/24/17
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);    
// end carriane


require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;


global $db;

if (!isset($_GET["ref"])) {
	die("Invalid item reference.");
	exit;
}

$Ref = $_GET["ref"];
$_POST["ref"] = $_GET["ref"];

if ($_REQUEST["viewonly"]) $view_only = 1;
if ($_REQUEST["view_from"]=='ssview') {
	$ss_view = TRUE;
}

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy::Edit request");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Pharmacy::Edit request");

if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {
	
	$saveok = $order_obj->updatePharmaTransaction($_POST);
	$sBreakImg ='close2.gif';
	$smarty->assign('sMsgTitle','Pharmacy request successfully updated!');
	$smarty->assign('sMsgBody','The request details have been saved into the database...');
	$smarty->assign('sBreakButton','<img class="link" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print&userck=$userck".'&cat=pharma&ref='.$Ref;
	$smarty->assign('sPrintButton','<img class="link"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');


	if ($saveok) {
		$infoResult = $order_obj->getOrderInfo($Ref);
		if ($infoResult)	$info = $infoResult->FetchRow();
		# Assign submitted form values

		$smarty->assign('sRefNo', $Ref);
		$smarty->assign('sSelectArea', $info['area_name']);
		$smarty->assign('sCashCharge',
			($info['is_cash']=="1" ?
				("Cash".($info['is_tpl']=="1" ? " (TPL)" : "")) :
				"Charge"));
		$smarty->assign('sOrderDate', date("F j, Y g:ia",strtotime($info['orderdate'])));
		$smarty->assign('sOrderName', $info['ordername']);
		$smarty->assign('sOrderAddress', $info['orderaddress']);
		$smarty->assign('sPriority',($info['priority']=="0") ? "Routine" : "Stat");
		$smarty->assign('sRemarks',$info['comments']);

		$itemsResult = $order_obj->getOrderItemsFullInfo($Ref);
		if ($itemsResult) {
			$oRows = "";
			$counter = 1;
			while ($oItem=$itemsResult->FetchRow()) {
				$oRows .= '<tr>
										<td align="center" style="font:bold 11px Tahoma;color:#000080">'.$counter++.'.)</td>
										<td align="center" style="font:bold 11px Tahoma;color:#000080">'.$oItem['bestellnum'].'</td>
										<td>'.$oItem['artikelname'].'</td>
										<td align="right">'.number_format((float)$oItem['force_price'],2).'</td>
										<td align="center">'.number_format((float)$oItem['quantity']).'</td>
										<td align="right">'.number_format((float)$oItem['quantity']*(float)$oItem['force_price'],2).'</td>
									</tr>
';
			}
			if (!$oRows) {
				$oRows = '<tr><td colspan="10" class="segPanel3">Order list is empty...</td></tr>';
			}
		}
		if (!$oRows) {
			$oRows = '<tr><td colspan="10" class="segPanel3">Error reading order details from database...</td></tr>';
		}
		$smarty->assign('sItems',$oRows);
		$smarty->assign('sMainBlockIncludeFile','order/saveok.tpl');
		$smarty->display('common/mainframe.tpl');

		try {
            require_once($root_path . 'include/care_api_classes/emr/services/PharmacyEmrService.php');
            $pharmaService = new PharmacyEmrService();
            #add new argument to detect if to update patient demographic or not
            $pharmaService->savePharmaRequest($Ref, 1);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }
	}
	else {
		if (!$order_obj->errorMsg) {
			$errorMsg = $db->ErrorMsg();
		}
		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
			$smarty->assign('sysErrorMessage','An item with the same order number already exists in the database.');
		else
			$smarty->assign('sysErrorMessage',"$errorMsg");
	}
}

 # Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);

$lastnr = $order_obj->getLastNr(date("Y-m-d"));
if ($_REQUEST['encounterset']) {
	$person = $enc_obj->getEncounterInfo($_REQUEST['encounterset']);
	// $person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}
$infoResult = $order_obj->getOrderInfo($Ref);

//$saved_discounts = $order_obj->getOrderDiscounts($Ref);
if ($infoResult)  $info = $infoResult->FetchRow();
if ($info['encounter_nr'])
	$encType = $db->GetOne("SELECT encounter_type FROM care_encounter WHERE encounter_nr=".$db->qstr($info['encounter_nr']));

$_POST = $info;
$_POST['encounter_type'] = $encType;
$_POST["iscash"] = $info["is_cash"];
$issc = ($info['is_sc'] == '1');

#added by VAN 01-29-2013
#get encounter info
$billinfo = $enc_obj->hasSavedBilling($info['encounter_nr']);
if ($billinfo){
    $bill_nr = $billinfo['bill_nr'];
    $hasfinal_bill = $billinfo['is_final'];
    
    if ($info['encounter_nr'])
        $is_maygohome = $db->GetOne("SELECT is_maygohome FROM care_encounter WHERE encounter_nr=".$db->qstr($info['encounter_nr']));
    
    $is_maygohome = $is_maygohome;
}

$warningCaption = '';
/*if (($bill_nr)||($is_maygohome)){
   if (($bill_nr)&&($is_maygohome)) 
        $warningCaption = "This patient has a saved billing and already advised to go home...";
   elseif (($bill_nr)&&!($is_maygohome)) 
        $warningCaption = "This patient has a saved billing...";     
   elseif (!($bill_nr)&&($is_maygohome)) 
        $warningCaption = "This patient is already advised to go home...";
        
   $view_only = true;
   $viewonly = true;
   $_REQUEST['viewonly'] = true;          
}*/

if (($bill_nr)&&($is_maygohome)){
   $warningCaption = "This patient has a saved billing and already advised to go home...";
        
   $view_only = true;
   $viewonly = true;
   $_REQUEST['viewonly'] = true;          
}

$smarty->assign("sWarning","<em><font color='RED'><strong>&nbsp;<span id='warningcaption'>".$warningCaption."</span></strong></font></em>"); 


if ($person) {
	$_POST['pid'] = $person['pid'];
	$_POST['encounter_nr'] = $person['encounter_nr'];
	$_POST['ordername'] = $person['name_first']." ".$person['name_last'];

	$addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
	if ($person["zipcode"])
		$addr.=" ".$person["zipcode"];
	if ($person["prov_name"])
		$addr.=" ".$person["prov_name"];
	$_POST['orderaddress'] = $addr;
	$_POST['discount_id'] = $person['discount_id'];
	$_POST['discount'] = $person['discount'];
}

# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/order-gui.js?t=<?=time()?>"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>


<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function init() {
		changeTransactionType(0);
		//xajax.call('', )
        

<?php
	if (!$_REQUEST['viewonly']) {
?>
		// Edit/Submit shortcuts
		shortcut.add('F2', keyF2,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F3', keyF3,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F5', keyF5,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F12', keyF12,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
<?php
	}
?>
/*added By MARK 2016-20-16*/


<?php
	// $area = $_POST['pharma_area'];
	if ($view_only)
		echo 'xajax_populate_order(\''.$Ref.'\',$(\'discountid\').value,1);';
	else
		echo 'xajax_populate_order(\''.$Ref.'\',$(\'discountid\').value);';
?>
	}

	function keyF2() {
		openOrderTray();
	}

	function keyF3() {
		if (confirm('Clear the order list?'))	emptyTray();
	}

	function keyF5() {
	}

	function keyF12() {
		if (validate()) document.inputform.submit()
	}


	function openCoverages() {
		var enc_nr = $('encounter_nr').value;
		if (enc_nr) {
			var url = '../../modules/insurance_co/seg_coverage_editor.php?userck=<?= $_GET['userck'] ?>&encounter_nr='+enc_nr+'&from=CLOSE_WINDOW&force=1';
			overlib(
				OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Insurance coverages',
				MIDX,0, MIDY,0,
				STATUS,'Insurance coverages');
		}
		else {
			alert('No patient with confinement case selected...');
		}
		return false
	}

	function openOrderTray() {
		var discount = $('discountid').value;
		var enc_nr = $('encounter_nr').value;
		var url = 'seg-order-tray.php?d='+discount+'&mode=edit&encounter_nr='+enc_nr;
		overlib(
			OLiframeContent(url, 1000, 450, 'fOrderTray', 0, 'auto'),
			WIDTH,1000, TEXTPADDING,0, BORDER,0,MODALSCROLL,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Add pharmacy item from Order tray',
			MIDX,0, MIDY,0,
			STATUS,'Add product from Order tray');
		return false;
	}

	function validate() {

		var fchck = 0;
		var dosage = jQuery(".dosageInput");
		dosage.each(function(v,k){
			if(jQuery(k).val() == ""){
				alert("Dosage is Required.");
				fchck = 1;
				return false;
			}
			
		});
		
		var frequency = jQuery(".frequencyInput");
		frequency.each(function(v,k){
			if(jQuery(k).val() == ""){
				alert("Frequency is Required.");
				fchck = 1;
				return false;
			}
			
		});

		var route = jQuery(".routeInput");
		route.each(function(v,k){
			if(jQuery(k).val() == ""){
				alert("Route is Required.");
				fchck = 1;
				return false;
			}
			
		});

		if(fchck)
			return false;		


		var iscash = $("iscash1").checked;
		if (!$('refno').value) {
			alert("Please enter the reference no.");
			$('refno').focus();
			return false;
		}
		if (iscash) {
			if (!$("ordername").value && !$("pid").value) {
				alert("Please enter the payer's name or select a registered person using the person search function...");
				$('ordername').focus();
				return false
			}
		}
		else {
			if (!$("pid").value) {
				alert("Please select a registered person using the person search function...");
				return false;
			}
		}
		if (document.getElementsByName('items[]').length==0) {
			alert("Item list is empty...");
			return false;
		}
		return confirm('Process this pharmacy request?');
	}
-->
jQuery(document).ready(function(){
	 jQuery("#latestDataDAI").load("seg-dai-connection.php");/*added MARK 16-10-20*/
       /*added MARK 16-10-20*/
        setTimeout(function(){
  			 ErrorConnectionDAI2();
		}, 2000);

		// jQuery(".ViewDAItransact").click(function() {
		//   alert( "Handler for .click() called." );
		// });
});
	function ErrorConnectionDAI2(){
				var offline = jQuery("#DAIcon").val();
				var offlineLabel = jQuery("#INV_address").val();	
				if (offline == 0){
				
					jQuery('#ajax_display').html("<em><font color='red'><strong>&nbsp;<span id='warningcaption'>"
				            		+"INVENTORY SYSTEM("+offlineLabel+")IS DOWN. Please contact administrator.</span></strong></font></em>");
				}else if(offline==1){
				     jQuery('#ajax_display').html("<em><font color='Green'><strong>&nbsp;<span id='warningcaption'>"
				            		+"INVENTORY SYSTEM("+offlineLabel+")IS CONNECTED....</span></strong></font></em>");
				
				}

		}
  
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Render form values
$smarty->assign('ssView',$ss_view);

/* No submitted data */
$smarty->append('JavaScript',$sTemp);

# Fetch order data

$submitted = true;
$readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";
$readOnlyAll = "";
if ($view_only) {
	$readOnly = "";
	$readOnlyAll = 'readonly="readonly" disabled="disabled"';
}

require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();
$charge = $db->Execute("SELECT id,charge_name FROM seg_type_charge_pharma WHERE in_pharmacy = 1 ORDER BY charge_name");
$index = 0;
$count = 0;
$select_area = '';
$sql="SELECT id,charge_name FROM seg_type_charge_pharma WHERE in_pharmacy = 1 ORDER BY charge_name";
$prod=$db->Execute($sql);
while($row=$prod->FetchRow()){
	if ($row['charge_name']== 'CMAP'){
			$option_all .= "<option value=\"".$row['charge_name']."\">MAP</option>\n";
    
    }elseif ($row['charge_name']== 'PHIC'){
		$option_all .= "<option id=\"charge_PHIC\" value=\"".$row['charge_name']."\">".$row['charge_name']."</option>\n";
    
    }else{
    	$option_all .= "<option value=\"".$row['charge_name']."\">".$row['charge_name']."</option>\n";
    			
    }
}
$select_area = '<select class="segInput" name="area" id="area" disabled="disabled" onchange="if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'" '.$readOnlyAll.'>'."\n".$select_area."</select>\n";
$smarty->assign('sSelectArea',$select_area);

if ($_REQUEST['billing']) {
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" onclick="return false" disabled="disabled" /><label class="segInput" for="iscash1"  style="font:bold 12px Arial; color:#3e7bc6">Cash</label>');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" checked="checked" onclick="return false" /><label class="segInput" for="iscash0" style="font:bold 12px Arial; color:#c64c3e">Charge</label>');
	$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" disabled="disabled" /><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');
}
else {
	$smarty->assign('sIsCash','<input class="segInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" disabled="disabled" /><label class="segInput" for="iscash1" style="font:bold 12px Arial; color:#3e7bc6">Cash</label>');
	$smarty->assign('sIsCharge','<input class="segInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" disabled="disabled" /><label class="segInput" for="iscash0" style="font:bold 12px Arial; color:#c64c3e">Charge</label>');
	$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').' '.$readOnlyAll.'/><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');
}


$select_charge = '<select id="charge_type" name="charge_type" class="segInput" '.($_POST['iscash']==='0'?'':'style="display:none"').' disabled="disabled">';
while($row=$charge->FetchRow()){
	$checktype = strtoupper($row['id']) == $_POST['charge_type'] ? 'selected ="selected"': "";
	$select_charge .= "	<option value=\"".strtoupper($row['id'])."\" $checktype>".strtoupper($row['charge_name'])."</option>\n";
	
}
$smarty->assign('sChargeType',$select_charge);


if ($person) {
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="" readonly="readonly" value="'.$_POST["ordername"].'"/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly" >'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" disabled="disabled" />');
}
else {
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="" readonly="readonly" value="'.$_POST["ordername"].'" '.$readOnlyAll.'/>');
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" onclick="clearEncounter()" disabled="disabled" />');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly">'.$_POST["orderaddress"].'</textarea>');
}


$smarty->assign('sOrderEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');

// updated by carriane 10/24/17; added IPBM encounter types
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', IPBMOPD_enc => 'IPBM - OPD', IPBMIPD_enc => 'IPBM - IPD');

if ($_POST['encounter_type'])	$smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($person['encounter_type'])
		$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
	else	$smarty->assign('sOrderEncTypeShow', 'WALK-IN');
}


$getinfo = $order_obj->getPersonMiniInfoFromEncounter($_POST['encounter_nr']);
if($getinfo){
			if ($_POST["encounter_type"]==1){
			
				$erLoc = $order_obj->getERLocation($getinfo['er_location'], $getinfo['er_location_lobby']);
				#var_dump($row['erloc']);
				if($erLoc['area_location'] != '')
    				$location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
    			else
    				$location = "EMERGENCY ROOM";
			}elseif ($getinfo["encounter_type"]==2 || $getinfo["encounter_type"]==IPBMOPD_enc){
				$dept = $order_obj->getDeptAllInfo($getinfo['current_dept_nr']);
				$location = stripslashes($dept['name_formal']);;
			}/*elseif (($row['enctype']==3)||($row['enctype']==4)){					
				$ward = $oclass->getWardInfo($row['current_ward']);
				$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room'];
			}*/
			elseif(($getinfo["encounter_type"]==4)|| ($getinfo["encounter_type"]==3)|| ($getinfo["encounter_type"]==IPBMIPD_enc)){
				
				$dward = $order_obj->getWardInfo($getinfo['current_ward_nr']);
				$room_nr = " Room #: " . $getinfo['current_room_nr'];
				$bed_nr = $ward_obj->getCurrentBedNr($getinfo['encounter_nr']);
				$bed = ($bed_nr) ? " Bed #: " . $bed_nr : '';
				$location = stripslashes($dward['name']) . $room_nr . $bed;
			}
			elseif ($getinfo["encounter_type"]==6){			
				$location = "INDUSTRIAL CLINIC";
			}else{
				#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				#$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				$location = 'WALK-IN';
			}
}else{
	$location = 'WALK-IN';
}
$smarty->assign('sLocation', $location);
$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
$smarty->assign('sPID',$_POST['pid']);

$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');

$smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style="" readonly="readonly"/>');
$smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" disabled="disabled" onclick="xajax_reset_referenceno()" '.$readOnlyAll.'/>');

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_REQUEST['dateset']) {
	$curDate = date($dbtime_format,$_REQUEST['dateset']);
	$curDate_show = date($fulltime_format, $_REQUEST['dateset']);
}
else {
	#$curDate = date($dbtime_format);
	#$curDate_show = date($fulltime_format);
	$dOrderDate = strtotime($_POST['orderdate']);
	$curDate = date($dbtime_format,$dOrderDate);
	$curDate_show = date($fulltime_format,$dOrderDate);
}
$smarty->assign('sOrderDate','<span id="show_orderdate" class="segInput" style="color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="segInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="">');

if ($view_only || $_REQUEST['billing']) {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
}
else {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_orderdate\",
		inputField : \"orderdate\",
		ifFormat : \"%Y-%m-%d %H:%M\",
		daFormat : \"	%B %e, %Y %I:%M%P\",
		showsTime : true,
		button : \"orderdate_trigger\",
		singleClick : true,
		step : 1
	});
</script>";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
}

$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="segInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').$readOnlyAll.'/><label class="segInput" for="p0">Routine</label>');
$smarty->assign('sUrgentPriority','<input class="segInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').$readOnlyAll.'/><label class="segInput" for="p1">Stat</label>');
$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="16" rows="2" style="float:left; margin-top:3px;margin-left:5px;" '.$readOnlyAll.'>'.$_POST['comments'].'</textarea>');
//}

$smarty->assign('sRootPath',$root_path);
#if ($view_only || $_REQUEST['billing'])
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="opacity:0.2">');
/*
else
	$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
			 onclick="if (warnClear()) { emptyTray(); overlib(
				OLiframeContent(\'seg-order-select-enc.php\', 700, 400, \'fSelEnc\', 0, \'auto\'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
				CAPTIONPADDING,2,
				CAPTION,\'Select registered person\',
				MIDX,0, MIDY,0,
				STATUS,\'Select registered person\'); } return false;"
			 onmouseout="nd();" />');
*/

if ($view_only) {
	$smarty->assign('sBtnAddItem','<img id="select-enc" class="disabled" src="'.$root_path.'images/btn_additems.gif" border="0" align="absmiddle" />');
	$smarty->assign('sBtnEmptyList','<img id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" class="disabled" border="0" align="absmiddle" />');
	$smarty->assign('sBtnCoverage','<img id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" class="disabled" border="0" align="absmiddle" />');
}
else {
	$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" align="absmiddle" onclick="return openOrderTray();">');
	$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" align="absmiddle" onclick="if (confirm(\'Clear the order list?\')) emptyTray()"/>');
	$smarty->assign('sBtnCoverage','<img class="segSimulatedLink" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" align="absmiddle" onclick="return openCoverages();"/>');
}

$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.($issc?'checked="checked" ':'').' onclick="seniorCitizen()" '.$readOnlyAll.'><label for="issc" class="segInput">Senior citizen</label>');


$smarty->assign('addDispensedQtyColumn', '<th width="10%" class="centerAlign" nowrap="nowrap">Dispensed</th>');

$qs = "";
if( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
if( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
if( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&ref='.$Ref.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');
//$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="edit">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
    <input type="hidden" name="accomodation" id="accomodation" value="<?=$accomodation ?>">
    <input type="hidden" name="cov" id="cov" value="">
    <input type="hidden" name="admission_accomodation" id="admission_accomodation" value="<?= $_GET['enc_accomodation']?>">
    <!--//added by VAN 01-29-2013 -->
    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
     <div id = "latestDataDAI"></div>
    
	<input type="hidden" name="view_from" value="<?= $_REQUEST['view_from'] ?>" />
<?php if (isset($_REQUEST['viewonly'])) { ?>	<input type="hidden" name="viewonly" value="<?= $_REQUEST['viewonly'] ?>" /><?php } ?>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input class="link" type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
	$smarty->assign('sBreakButton','<img class="link" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/form.tpl');
$smarty->display('common/mainframe.tpl');

?>