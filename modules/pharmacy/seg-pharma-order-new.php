<?php

include_once $root_path . 'include/inc_ipbm_permissions.php'; // added by carriane 10/24/17

// updated by carriane 10/24/17; added IPBM encounter types
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)',IPBMOPD_enc=>"IPBM - OPD", IPBMIPD_enc => "IPBM - IPD");

	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_order.php");
include_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once $root_path.'frontend/bootstrap.php';
$dept_obj=new Department;
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;
$xajax->printJavascript($root_path.'classes/xajax_0.5');
// var_dump($_POST["submitted"]);die();
?>


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/autocomplete.js"></script>
<link rel="stylesheet" href="<?=$root_path?>modules/pharmacy/css/autocomplete.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/order-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.9.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>

<link rel="stylesheet" href="<?=$root_path?>css/frontend/application.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="<?=$root_path?>css/frontend/alerts.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="<?=$root_path?>css/frontend/animate.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="<?=$root_path?>js/mustache.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/frontend/alert.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();
</script>
<script type="text/javascript" language="javascript">


	var trayItems = 0;
	var has_inventory_items=0;
	function showLoadingGui(item='',name=''){
		console.log("showLoadingGui");
		if(item!='') item="Item "+name+"("+item+") is Done. ";
	    // return overlib(item+'Please wait...<br/>Transmitting Inventory Items to DAI...<br><img src="../../images/ajax_bar.gif">',
	    //     WIDTH,300, TEXTPADDING,5, BORDER,0,
	    //     STICKY, SCROLL, CLOSECLICK, MODAL,
	    //     NOCLOSE, CAPTION,'Fetching information',
	    //     MIDX,0, MIDY,0,
	    //     STATUS,'Fetching information');
	    Alerts.loading({
        	'title': item+'Please wait...',
			content: 'Transmitting Inventory Items to DAI...'
        });
	}

	function showLockedLoadingGui(){
		console.log("showLockedLoadingGui");
	    // return overlib('Another DAI transaction is processing... <br/>Please wait... <br><img src="../../images/ajax_bar.gif">',
	    //     WIDTH,300, TEXTPADDING,5, BORDER,0,
	    //     STICKY, SCROLL, CLOSECLICK, MODAL,
	    //     NOCLOSE, CAPTION,'Fetching information',
	    //     MIDX,0, MIDY,0,
	    //     STATUS,'Fetching information');
	    Alerts.loading({
        	'title': item+'Please wait...',
			content: 'Another DAI transaction is processing...'
        });
	}

	function lockedItemStatus(ref){
		console.log("lockedItemStatus");
	    showLockedLoadingGui();
	    setTimeout(function(){
	     	xajax_serveToInventory(ref); 
	 	}, 5000);
	}

	function hideLoadingGui(){
		console.log("hideLoadingGui");
	    // cClick();
	    Alerts.close();
	}

	function updateItemStatus(ref,item,name,message,time_diff){
		console.log("updateItemStatus");
		has_inventory_items++;
		var htmlString = jQuery("#serve_notificatioon").html();
		if(message=="FAILED" || message=="Failed") {
			message="FAILED";
			jQuery("#serve_notificatioon").html(htmlString + "<br/><b style='background-color:red;'>Item "+name+" - "+message+"("+time_diff+" sec)</b>");
			jQuery("#"+item+"_STATUS").html("<b style='background-color:red;'>"+message+"</b>");

		}
		else{
			message="TRANSMITTED";
			jQuery("#serve_notificatioon").html(htmlString + "<br/><b>Item "+name+" - "+message+"("+time_diff+" sec)</b>");
			jQuery("#"+item+"_STATUS").html(message);
		}
	    showLoadingGui(item,name);
	    xajax_serveToInventory(ref);
	}

	function endOfTrransmission(){
		console.log("endOfTrransmission");
		if(has_inventory_items>0){
			var htmlString = jQuery("#serve_notificatioon").html();
			htmlString = htmlString.replace('<h1>DO NOT CLOSE WINDOW!</h1>','<br/>');
			jQuery("#serve_notificatioon").html(htmlString + "<br/><b>END OF TRANSMISSION</b>");
		}else{
			jQuery("#serve_notificatioon").html("Successfully saved details...");
		}
	}

	function init() {
		console.log("init");
		$('phic_cov').update('None');
		//for IC
		// checkReqSource();
		changeTransactionType(0);
		
            //added by pol 10/12/2013
	loadDai();


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
		shortcut.add('F10', keyF10,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F9', keyF9,
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
		shortcut.add('F8', keyF8,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F3', keyOrderlist,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F4', keyServelist,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
<?php
	}
?>
	}

	

	function keyF8() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order-functions.php?userck=<?=$userck?>";
	}

	function keyOrderlist() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=list&area=<?=$area?>";
	}

	function keyServelist() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=servelist&area=<?=$area?>";
	}
	function keyF2() {
		openOrderTray();
	}
	function keyF10() {
		if (confirm('Clear the order list?'))	emptyTray();
	}

	function keyF9() {
		<?php

	$var_arr = array(

		"var_pid"=>"pid",

		"var_encounter_nr"=>"encounter_nr",

		"var_parent_discountid"=>"discountid",

		"var_discount"=>"discount",

		"var_name"=>"ordername",

		"var_addr"=>"orderaddress",

		"var_clear"=>"clear-enc",

		"var_enctype"=>"encounter_type",

		"var_enctype_show"=>"encounter_type_show",

		"var_include_walkin"=>"1",

		"var_reg_walkin"=>"1"

	);

	$vas = array();

	foreach($var_arr as $i=>$v) {

		$vars[] = "$i=$v";

	}

	$var_qry = implode("&",$vars);

?>

        //added by VAN 02-06-2012

        //for bloodbank only as per Mrs Angie Balayon's request 

        var ref_source1 = '<?=$ref_source?>';

        

        if (ref_source1=='BB')

            ref_source = ref_source1;

         // else if (ref_source2=='BB')  
         //    ref_source = ref_source2; 

        else

            ref_source = "";    

        //-----------------    

        

        $('warningcaption').innerHTML = '';  

        

		if (warnClear()) {

			emptyTray();

//			added by Nick 11-20-2015

			jQueryDialogSearch = jQuery('#search-dialog')

				.dialog({

					modal: true,

					title: 'Select a Person',

					width: '80%',

					height: 500,

					position: 'top',

					open: function(){

							jQuery('#search-dialog-frame').attr('src','<?= $root_path ?>/index.php?r=person/search');

						jQuery('.ui-dialog .ui-dialog-content').css({

							overflow : 'hidden'

						});

					}

				});

		}

		return false;
	}

	function keyF12() {
		if (validate()) document.inputform.submit()
	}


	function openCoverages() {
		console.log("openCoverages");
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
		console.log("openOrderTray");
		var discount = $('discountid').value;
		var mixed_misc = $('mixed_misc').value;
        var goadd = true;
        var encounter_nr = $('encounter_nr').value;
        var isBloodBB = $('isBloodBB').value;

        var url = 'seg-order-tray.php?d='+discount+'&mixed_misc='+mixed_misc+"&areas="+"<?=$_GET['area']?>&encounter_nr="+encounter_nr+"&isBloodBB="+isBloodBB;

		    overlib(
			    OLiframeContent(url, 1200, 450, 'fOrderTray', 0, 'auto'),
			    WIDTH,1200, TEXTPADDING,0, BORDER,0,MODALSCROLL,
			    STICKY, SCROLL, CLOSECLICK, MODAL,
			    CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 onclick="loadDai()" >',
			    CAPTIONPADDING,2,
			    CAPTION,'Add pharmacy item from Order tray',
			    MIDX,0, MIDY,0,
			    STATUS,'Add product from Order tray');
		return true;
	}

  var timer=0,tleft,timeoutId;
  var resetat;

  	function loadDai(){
  		 jQuery("#latestData").load("seg-dai-connection.php");
  	}
	function validate(message) {
		console.log("validate");
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
			if (!$("ordername").value) {
				alert("Please select a registered person using the person search function...");
				return false;
			}
			// if (!$("pid").value) {
			// 	alert("Please select a registered person using the person search function...");
			// 	return false;
			// }
		}
		if (document.getElementsByName('items[]').length==0 && document.getElementsByName('misc_item[]').length==0) {
			alert("Item list is empty...");
			return false;
		}

		return confirm(message);
	
	}


function validateDForm(){
	console.log("validateDForm");
		 dataNew();
}
/*added by Mark March 7, 2017
	check DAI connection status every 3 seconds 
*/
window.setInterval(function(){
  /// call your function here
  loadDai();
}, 3000);
 
function dataNew(){
	var hasEmptyField = 0;
    var areaCode = jQuery('#isBloodBB').val();
    var areaCodes = document.getElementsByName('areaCode[]');
    var dosage = document.getElementsByName('dosage[]');
    var frequency = document.getElementsByName('frequency[]');
    var route = document.getElementsByName('route[]');
    var isBloodBank = 'BB';
    var isNotBloodBank = 1;

    for (var i=0;i<areaCodes.length;i++) {
        if((areaCodes[i].value!=isBloodBank || areaCode != isNotBloodBank) && dosage[i].value.trim()=="") {
            alert("Dosage is Required!");
            hasEmptyField = 1;
        }

        if((areaCodes[i].value!=isBloodBank || areaCode != isNotBloodBank) && frequency[i].value.trim()=="") {
            alert("Frequency is Required!");
            hasEmptyField = 1;
        }

        if((areaCodes[i].value!=isBloodBank || areaCode != isNotBloodBank) && route[i].value.trim()=="") {
            alert("Route is Required!");
            hasEmptyField = 1;
        }
    }

	if(hasEmptyField)return;
	
	loadDai();
		var offline = jQuery("#DAIcon").val();
		var offlineLabel = jQuery("#INV_address").val();
	if (offline == 1) {
		jQuery('.is_overrideYES').each(function() {
		     jQuery(this).val("0");
		});
	   if (validate('Inventory is connected Process this pharmacy request?')) document.inputform.submit();

	}else if (offline == 0) {
			jQuery('.is_overrideYES').each(function() {
		     				jQuery(this).val("1");
						});
	   if (validate('Inventory system is down, do you want to process this pharmacy request?')) document.inputform.submit();
   	}
}
</script>
<?php
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

$enc_obj=new Encounter;

#added by art 06/26/2014
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
$objIC = new SegICTransaction();
#end art
$seg_ormisc = new SegOR_MiscCharges();

$order_obj = new SegOrder("pharma");
$order_obj->setupLogger();
global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy::New request");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Pharmacy::New request");

#added by VAN 02-06-2012
//for bloodbank only as per Mrs Angie Balayon's request 
 
if ($area=='bb'){
    $ref_source = "BB";
}

if (isset($_POST["submitted"])) {
	$has_inventory=0;
	$Ref = $order_obj->processPharmaTransaction($_POST);
	if ($Ref) {
		// If everything goes well
		$smarty->assign('sMsgTitle','Pharmacy request successfully saved!');
		if($_POST["DAIcon"]){
			$smarty->assign('sysInfoMessage','<span id="serve_notificatioon">Successfully saved details...<b><h1>DO NOT CLOSE WINDOW!</h1>TRANSMITTING INVENTORY ITEMS...</b></span>');
		}else{
			$smarty->assign('sysInfoMessage','<span id="serve_notificatioon">Successfully saved details...</span>');
		}
		
		$smarty->assign('sMsgBody','The request details have been saved into the database...');
		$sBreakImg ='close2.gif';
		$smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
		$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print&userck=$userck".'&cat=pharma&ref='.$Ref;
		$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
		
		$infoResult = $order_obj->getOrderInfo($Ref);

		if ($infoResult)  $info = $infoResult->FetchRow();

		# Assign submitted form values
//		$smarty->assign('sSelectArea', $_REQUEST['area']);
//		$smarty->assign('sRefNo', $data['refno']);
//		$smarty->assign('sCashCharge', ($_REQUEST['iscash']=="1") ? "Cash" : "Charge");
//		$smarty->assign('sOrderDate', $_REQUEST['orderdate']);
//		$smarty->assign('sOrderName', $_REQUEST['ordername']);
//		$smarty->assign('sOrderAddress', $_REQUEST['orderaddress']);
//		$smarty->assign('sPriority',($_REQUEST['priority']=="0") ? "Normal" : "Urgent");
//		$smarty->assign('sRemarks',$_REQUEST['comments']);

		$smarty->assign('sRefNo', $Ref);
		$smarty->assign('sSelectArea', $info['area_name']);
		$smarty->assign('sCashCharge',
			($info['is_cash']=="1" ?
				("Cash".($info['is_tpl']=="1" ? " (TPL)" : "")) :
				"Charge (".$info['charge_type'].")"));
		$smarty->assign('sOrderDate', date("F j, Y g:ia",strtotime($info['orderdate'])));
		$smarty->assign('sOrderName', $info['ordername']);
		$smarty->assign('sOrderAddress', $info['orderaddress']);
		$smarty->assign('sPriority',($info['priority']=="0") ? "Routine" : "Stat");
		$smarty->assign('sRemarks',$info['comments']);


		$itemsResult = $order_obj->getOrderItemsFullInfo($Ref);
		
		if ($itemsResult) {
			$oRows = "";
			 while ($oItem=$itemsResult->FetchRow()) {
			 		$uid_length = strlen($oItem['inv_uid']);
			 		if($oItem['is_in_inventory']) $has_inventory=1;
			 		if ($oItem['inv_uid']=="FAILED" || $oItem['inv_uid']=="AILED" && $oItem['inv_uid'] !=""){
			 			$message_uid = TRUE;
			 		}
				$oRows .= '<tr>
										<td id="'.$oItem['bestellnum'].'_STATUS">'.(($oItem['inv_uid']=="FAILED" || $oItem['inv_uid']=="AILED") ? "<small style='color:red'><b>FAILED</b></small>" :" ").'</td>
										<td align="center" style="font:bold 11px Tahoma;color:#000080">'.$oItem['bestellnum'].'</td>
										<td >'.$oItem['artikelname'].'</td>
										<td align="right">'.number_format((float)$oItem['force_price'],2).'</td>
										<td align="center">'.number_format((float)$oItem['quantity']).'</td>
										<td align="right">'.number_format((float)$oItem['quantity']*(float)$oItem['force_price'],2).'</td>
									</tr>
';
			}

			
				$smarty->assign('UIDexist',$message_uid);
				$smarty->assign('UIDmessage','There seems to have an error in Inventory transaction!');
			
			if (!$oRows) {
				$oRows = '<tr><td colspan="12" class="segPanel3">Order list is empty...</td><td></td><td></td><td></td></tr>';
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
            $pharmaService->savePharmaRequest($Ref);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();die;
        }
        if($_POST["DAIcon"]){
        	echo "<script type='text/javascript' language='javascript'> showLoadingGui(); xajax_serveToInventory('".$Ref."'); </script>";
        }

		exit;
	}
	else {
		// Some error occurred along the way
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
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

# Collect javascript code
ob_start();
	 # Load the javascript code
?>




<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

$lastnr = $order_obj->getLastNr(date("Y-m-d"));

if ($_REQUEST['encounterset']) {
	$person = $enc_obj->getEncounterInfo($_REQUEST['encounterset']);
	// $person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"12\">Order list is currently empty...</td><td></td><td></td><td></td>
				</tr>");

	if (is_array($_POST['items'])) {
		$script = '<script type="text/javascript" language="javascript">';
		$items = $_POST['items'];
		$prc = array();
		$qty = array();
		foreach ($items as $i=>$item) {
			$prc[$i] = $_POST['prc'][$i];
			$qty[$i] = $_POST['qty'][$i];
			$con[$i] = in_array($item, $_POST['consigned']) ? '1' : '0';
			if (!is_numeric($prc[$i])) $prc[$i] = 'null';
			if (!is_numeric($qty[$i])) $qty[$i] = '0';
		}

		$script .= "var item0=['" .implode("','",$items)."'];";
		$script .= "var prc0=[" .implode(",",$prc). "];";
		$script .= "var qty0=[" .implode(",",$qty). "];";
		$script .= "var con0=[" .implode(",",$con). "];";
		$script .= "xajax_add_item('" .$_POST['discountid']. "', item0, qty0, prc0, con0);";
		$script .= "</script>";
		$src = $script;
	}
	if ($src) $smarty->assign('sOrderItems',$src);
}
else {
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"9\">Order list is currently empty...</td><td></td><td></td><td></td>
				</tr>");
}


# Render form elements
$submitted = isset($_POST["submitted"]);
$readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";

if ($person) {
	$_POST['pid'] = $person['pid'];
	$_POST['encounter_nr'] = $person['encounter_nr'];
	$_POST['ordername'] = $db->GetOne("SELECT fn_get_person_name(".$db->qstr($person['pid']).")");
	//$person['name_first']." ".$person['name_last'];
    if($person['mun_name'] == "NOT PROVIDED" && $person['brgy_name'] == "NOT PROVIDED" || $person['brgy_name'] == null) {
        $person['mun_name'] = '';
        $person['brgy_name'] = '';
    }
	$addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
	if ($person["zipcode"])
		$addr.=" ".$person["zipcode"];

	if ($person["prov_name"] == 'NOT PROVIDED')
	    $addr.="";
	else
		$addr.=" ".$person["prov_name"];
    //$addr = $db->GetOne("SELECT fn_get_complete_address(".$db->qstr($person['pid']).")");
	$_POST['orderaddress'] = rtrim($addr, ', ');
	if(!empty($person['discountid']))
		$_POST['discountid'] = $person['discountid'];
	else
		$_POST['discountid'] = $person['discountid_pid'];
	$_POST['discount'] = $person['discount'];
}


require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();
//modified by cha, 11-24-2010
//$disabled = (strtolower($_GET['area']) != 'all') ? ' disabled="disabled"' : '';
$disabled = (strtolower($_GET['area']) != 'all') ? '' : '';
$index = 0;
$count = 0;
$select_area = '';
$selected_disabled ="";
while($row=$prod->FetchRow()){
	$checked=strtolower($row['area_code'])==strtolower($_GET['area']) ? 'selected="selected"' : "";
	$select_area .= "	<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
	if ($checked) $index = $count;
	$count++;
}
if (isset($_GET['bbDisabled'])) {
	$selected_disabled ="disabled";
}
/*$select_area = '<select class="segInput" name="area" id="area"'.$disabled.' onchange="checkExcludedArea(); if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'"'.$selected_disabled.'  >'."\n".$select_area."</select>\n".
	"<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['area']."\"/>";
$smarty->assign('sSelectArea',$select_area);*/

	if(isset($_POST['encounter_nr'])){
		$set_encounter = $_POST['encounter_nr'];
	}else{
		$set_encounter = $_GET['encounterset'];
	}
$getinfo = $order_obj->getPersonMiniInfoFromEncounter($set_encounter);
$is_inpatient=0;
$checked_cash = '';
$checked_charge = '';
$mixed_misc = false;




#added by VAN 01/23/2013
#requested by Ma'am Angie of Blood Bank and with approval of Sir Justol
#open to all pharmacy area
#$option_mission = '';
#if ($ref_source=='BB')
/*    $option_mission = '<option value="MISSION">MISSION</option>
                       <option value="PCSO">PCSO</option>
                      ';
*/
#added by EJ 12/13/2014
#The Newborn Screening Center - Mindanao requested to have a code "NSC-M" in all cost centers as Charge Type.
#open to all pharmacy area

# Modified by JEFF @ 11-23-17
$sql="SELECT id,charge_name FROM seg_type_charge_pharma WHERE in_pharmacy = 1 ORDER BY ordering ASC";

    	$option_all='<select id="charge_type" name="charge_type" class="segInput" style="'.$show.'" onchange="if (warnClear()) { emptyTray(); changeChargeType(); return true;} else {return false;}"> ';

	$result=$db->Execute($sql);
    	while ($charged_name=$result->FetchRow()){
    		
    		$option_all .= "<option value=\"".strtoupper($charged_name['id'])."\">".strtoupper($charged_name['charge_name'])."</option>\n";
    			
    	}

    	$option_all .= "	</select>";



$smarty->assign('sChargeType',$option_all);



if($getinfo){

			if ($getinfo["encounter_type"]==1){
				$is_inpatient=0;
				// var_dump($getinfo);die();
				$erLoc = $dept_obj->getERLocation($getinfo['er_location'], $getinfo['er_location_lobby']);
				// die($erLoc);
				$lobby = ($erLoc['lobby_name'] != null) ? " (" . $erLoc['lobby_name'] . ")" : "";
				if($erLoc['area_location'])
				$location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
				else
				$location = "EMERGENCY ROOM";	
			}elseif ($getinfo["encounter_type"]==2||$getinfo["encounter_type"]==IPBMOPD_enc){

				$dept = $order_obj->getDeptAllInfo($getinfo['current_dept_nr']);
				$location = stripslashes($dept['name_formal']);
			}/*elseif (($row['enctype']==3)||($row['enctype']==4)){					
				$ward = $oclass->getWardInfo($row['current_ward']);
				$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room'];
			}*/
			// updated by carriane 10/24/17; added IPBMIPD_enc
			elseif(($getinfo["encounter_type"]==4)|| ($getinfo["encounter_type"]==3)|| ($getinfo["encounter_type"]==IPBMIPD_enc)){
				$is_inpatient=1;
				$dward = $order_obj->getWardInfo($getinfo['current_ward_nr']);
				$room_nr = " Room #: " . $patient['current_room_nr'];
				$bed_nr = $ward_obj->getCurrentBedNr($patient['encounter_nr']);
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

$ipbmenctype = $enc_obj->EncounterType($_REQUEST['encounterset']); // added by carriane 10/24/17

// updated by carriane 10/24/17; added tracking if from IPBM module
if($isIPBM){
	if($ipbmenctype == IPBMIPD_enc || $ipbmenctype == IPBMOPD_enc){
		$checked_cash = 'checked="checked" ';
	    $show = 'display:none';
	}
}else{
	if(($_POST["iscash"]!="0" && $_GET['area']=='amb' && $person['encounter_type'] == '2') || $_POST["iscash"]=="0"){
	    $checked_cash = '';
	    $checked_charge = 'checked="checked" ';
	    $show = '';
	    if($_GET['area']=='amb' && $person['encounter_type'] == '2')
	        $mixed_misc = true;
	}
	#added by art 06/26/2014
	elseif ($_GET['request_source']=='IC') {
	    $ic = $objIC->isCharge($_GET['encounterset']);
	    if ($ic == 1) {
	        #charged
	        $checked_cash = '';
	        $checked_charge = 'checked="checked" ';
	    }else{
	        #cash
	        $checked_cash = 'checked="checked" ';
	        $checked_charge = '';
	    }
	}
	#end art
	else{
		// var_dump($is_inpatient);die;
		if($is_inpatient){
			$checked_cash = '';
	        $checked_charge = 'checked="checked" ';
		}else{
			$checked_cash = 'checked="checked" ';
		    $checked_charge = '';
		    $show = 'display:none';
		}
	}
}

if ($_GET['request_source']=='IC') {
	$source_req = SegRequestSource::getSourceIndustrialClinic();
	$encounter_nr = $_POST["encounter_nr"];
	if (empty($encounter_nr))
		$encounter_nr_cond = " encounter_nr IS NULL ";
	else
		$encounter_nr_cond = " encounter_nr = ".$db->qstr($encounter_nr);

	$sql_ic = "SELECT t.agency_charged
		FROM seg_industrial_transaction AS t
		WHERE ".$encounter_nr_cond;
	$rs_ic = $db->Execute($sql_ic);
	$row_ic = $rs_ic->FetchRow();
	$is_charge2comp = $row_ic['agency_charged'];
	if(!$is_charge2comp)
    	$show = 'display:none';
}

$smarty->assign('sIsCash','<input class="segInput" type="radio" name="iscash" id="iscash1" value="1" '.$checked_cash.'onclick="if (warnClear()) { emptyTray(); changeTransactionType(0); return true;} else {return false;}" /><label for="iscash1" class="segInput" style="font:bold 12px Arial; color:#3e7bc6">Cash</label>');
$smarty->assign('sIsCharge','<input class="segInput" type="radio" name="iscash" id="iscash0" value="0" '.$checked_charge.'onclick="if (warnClear()) { emptyTray(); changeTransactionType(0); return true;} else return false;" style="margin-left:10px" /><label class="segInput" for="iscash0" style="font:bold 12px Arial; color:#c64c3e">Charge</label>');
$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');

$smarty->assign('sLocation', (($location) ? mb_strtoupper($location) : 'None'));
$smarty->assign('sPID',$_POST['pid']);
$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sOrderEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
if ($_POST['encounter_type'])	$smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	// updated by carriane 10/24/17; added tracking if from IPBM module
	if($isIPBM){
		$smarty->assign('sOrderEncTypeShow',$enc[$ipbmenctype]);
	}else{
		if ($person['encounter_type'])
			$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
		else	$smarty->assign('sOrderEncTypeShow', 'WALK-IN');
	}
}



#added by pol

$phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$_POST["encounter_nr"]."') AS `phic_nr`");
$smarty->assign('sPhicNo', $phic_nr);

    if($_POST["encounter_nr"]){
        $sql_mc = "SELECT m.memcategory_desc
                        FROM seg_encounter_memcategory `e`
                        INNER JOIN seg_memcategory `m`
                        ON e.memcategory_id=m.memcategory_id
                        WHERE e.encounter_nr=".$db->qstr($_POST["encounter_nr"]);
        $category = $db->GetOne($sql_mc);
        if($category){                        
                        $CategoryUi = $category;
                    }else{
                        $CategoryUi = 'None';    
                    }
    } else {
                    
                }
     
$smarty->assign('sMemCategory', $CategoryUi);
#end pol



if($enc_obj->EncounterType($set_encounter) == '6'){
    $patient = $enc_obj->getEncounterInfo($set_encounter);
    $_POST["discountid"] = $patient['discountid'];
    if($_POST["discountid"] == 'SC'){
        $_POST["discount"] = ($_POST["discountid"]) ? 0.2 : "";
        $_POST["issc"] = 1;
    }
}

$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');


if ($person) {
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="" readonly="readonly" value="'.$_POST["ordername"].'"/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly" >'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="" value="Clear" disabled="disabled" />');
}
else {
	/* $smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["ordername"].'"/>'); */
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="font:" value="'.$_POST["ordername"].'" onfocus="autoSuggestWalkin(this)" autocomplete="off"/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly" onfocus="this.select()">'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" onclick="clearEncounter()" '.(($_POST['pid'])?'':' disabled="disabled"').' />');
}
$smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style=""/>');
$smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>');

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_REQUEST['dateset']) {
	$curDate = date($dbtime_format,$_REQUEST['dateset']);
	$curDate_show = date($fulltime_format, $_REQUEST['dateset']);
}
else {
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
}
$smarty->assign('sOrderDate','<span id="show_orderdate" class="segInput" style="color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="segInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="">');

if ($_REQUEST['billing']) {
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

# LINGAP/CMAP
if (true) {
	$sponsorHTML = '<select class="segInput" name="sponsor" id="sponsor">
<option value="" style="font-weight:bold">No coverage</option>
';
	include_once($root_path."include/care_api_classes/class_sponsor.php");
	$sc = new SegSponsor();
	$sponsors = $sc->get();
	while($row=$sponsors->FetchRow()){
		$sponsorHTML .= "									<option value=\"".$row['sp_id']."\">".$row['sp_name']."</option>\n";
	}
	$sponsorHTML .= "					</select>";
	$smarty->assign('sSponsor',$sponsorHTML);
}

$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="segInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/><label class="segInput" for="p0">Routine</label>');
$smarty->assign('sUrgentPriority','<input class="segInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/><label class="segInput" for="p1">Stat</label>');
$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="14" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.$_POST['comment'].'</textarea>');

if ($_REQUEST['billing'])
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="opacity: 0.2"/>');
else
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer" onclick="keyF9()" onmouseout="nd();" />');

$smarty->assign('sRootPath',$root_path);

$text="add";
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="openOrderTray()">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the order list?\')) emptyTray()"/>');
$smarty->assign('sBtnCoverage','<img class="segSimulatedLink" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" onclick="return openCoverages();"/>');
$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' '.(($_GET['area']=='amb' && $person['encounter_type'] == '2') ? 'disabled="disabled" ': ' ').' onclick="seniorCitizen()"><label class="segInput" for="issc" style="font:bold 12px Arial;">Senior citizen</label>');
$smarty->assign('DialogDAI','<img src="'.$root_path.'assets/13ebb492/img/loading.gif" id="loadingIMG"/> <label id="messageID-DAI"></label>
							<br><br>
							<center>
							<img id="dSubmit" src="../../images/btn_submitorder.gif" align="center"  style="cursor:pointer">
							<img id="dClose" src="../../gui/img/control/default/en/en_cancel.gif" border="0" align="center" width="73" height="23" alt="close" style="cursor:pointer"></center>');

/*
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
$smarty->assign('sBtnDiscounts','<input class="segInput" type="image" id="btndiscount" src="'.$root_path.'images/btn_discounts.gif"
			 onclick="overlib(
				OLiframeContent(\'seg-order-discounts.php\', 380, 125, \'if1\', 1, \'auto\'),
				WIDTH,380, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,
				CAPTION,\'Change discount options\',
				REF,\'btndiscount\', REFC,\'LL\', REFP,\'UL\', REFY,2,
				STATUS,\'Change discount options\'); return false;"
			 onmouseout="nd();">');
*/
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');
/*
	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			inputField : \"orderdate\", ifFormat : \"$phpfd\", showsTime : false, button : \"orderdate_trigger\", singleClick : true, step : 1
		});
	</script>
	";
$smarty->assign('jsCalendarSetup', $jsCalScript);*/

$exlude_if_phic_areas = PharmacyArea::model()->findAllByAttributes(array('exclude_if_phic'=> 1));
$areas = array();
foreach ($exlude_if_phic_areas as $key => $value) {
    $areas[] = $value['area_code'];
}
$exclude_area = implode(",", $areas);
$is_excluded = 0;
if ($_GET['area']) {
	if (in_array($_GET['area'], $areas)) {
		$is_excluded = 1;
	}
}

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$qs = "";
if ( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
if ( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
if ( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

$smarty->assign("sWarning","<em><font color='RED'><strong>&nbsp;<span id='warningcaption'>".$warningCaption."</span></strong></font></em>"); 

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=new&clear_ck_sid=".$clear_ck_sid.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="mixed_misc" id="mixed_misc" value="<?=$mixed_misc?>" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<input type="hidden" name="target" value="<?php echo $target ?>">
	<input type="hidden" name="source_req" id="source_req" value="<?php echo empty($_GET['request_source'])?'IP':$_GET['request_source']; ?>">
	<input type="hidden" name="is_charge2comp" id="is_charge2comp" value="<?php echo $is_charge2comp ?>">

	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
	<input type="hidden" name="billing" id="billing" value="<?= $_REQUEST['billing'] ?>">
	<input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>">
	<input type="hidden" name="encounterset" id="encounterset" value="<?= $_REQUEST['encounterset'] ?>">
    <input type="hidden" name="area" id="area" value="<?= $_REQUEST['area'] ?>">
    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
    <input type="hidden" name="hasPHIC" id="hasPHIC" value="0">
    <input type="hidden" name="uiDiscount" id="uiDiscount" value="<?=$uiDiscount?>">
    <input type="hidden" name="area2" id="area2" value="<?=$_GET['area']?>">
	<input type="hidden" name="exclude_area" id="exclude_area" value="<?=$is_excluded?>">
    <input type="hidden" name="accomodation" id="accomodation" value="<?=$accomodation ?>">
    <input type="hidden" name="cov" id="cov" value="">
    <input type="hidden" name="admission_accomodation" id="admission_accomodation" value="<?= $_GET['enc_accomodation']?>">
    <input type="hidden" name="isBloodBB" id="isBloodBB" value="<?= $_GET['isBloodBB'] ?>"/>
    <!-- added By Mark 2016-10-17 -->
	 <div id = "latestData"></div>
	
	<!-- end by Mark -->
<?php

$sTemp = ob_get_contents();
ob_end_clean();

/*
global $GPC;
echo $GPC;
echo "<hr>sid:$sid;clear:$clear_ck_sid";
*/

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="validateDForm();"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/form.tpl');
$smarty->display('common/mainframe.tpl');

?>