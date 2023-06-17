<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/pharmacy/ajax/order-tray.common.php");
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
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
//Added by Chritian 02-10-20
 require_once($root_path.'include/care_api_classes/class_globalconfig.php');
 $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
 if($barcodeLength=$glob_obj->getBarcodeLength())
 //end Chritian 02-10-20	
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
	$onLoadJS="onload=\"init();\"";
	$smarty->assign('sOnLoadJs',$onLoadJS);

$mixed_misc = (($_GET['mixed_misc']) ? '1' : '0');
//checks if request is new/edit
$request_mode = empty($_GET['mode'])?'new':$_GET['mode']; 

use \SegHis\models\Hospital;
require_once $root_path.'frontend/bootstrap.php';
$hospitalInfo = Hospital::info();

try{
	$offline = 0;
	$fp = @fsockopen($hospitalInfo->INV_address, 80, $errno, $errstr, 0.5);
	if (!$fp) {
	  	$offline = 0;
	}
	else{
	 	$offline = 1;
	 	$inv_url = "http://".$hospitalInfo->INV_address.'/'.$hospitalInfo->INV_directory;
	 	$invsite = @file_get_contents($inv_url);
	    
	    if (empty($invsite)) $offline = 0;
	}
}catch (Exception $e){
	$offline = 0;
}

#-----------------------------

require($root_path.'include/care_api_classes/class_pharma_product.php');
$pc = new SegPharmaProduct();
// var_dump($pc->search_products_for_tray()); die();
 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="", lastSearchPage=-1;
var disableInput = "<?= $_GET['noinput'] ? "1" : "0" ?>";
var barcodeLength = <?=$barcodeLength?>;

function init() {
	shortcut.add('ESC', closeMe,
		{
			'type':'keydown',
			'propagate':false,
		}
	);
	setTimeout("$('search').focus()",100);
	ErrorConnectionDAI2();
	

}
function closeMe() {
	window.parent.cClick();
}

function prepareAddEx() {
	var prod = document.getElementsByName('prod[]');
	var qty = document.getElementsByName('qty[]');
	var prcCash = document.getElementsByName('prcCash[]');
	var prcCharge = document.getElementsByName('prcCharge[]');
	var nm = document.getElementsByName('pname[]');
	
	var details = new Object();
	var list = window.opener.document.getElementById('order-list');
	var result=false;
	var msg = "";
	for (var i=0;i<prod.length;i++) {
		result = false;
		if (prod[i].checked) {
			details.id = prod[i].value;
			details.name = nm[i].value;
			details.qty = qty[i].value;
			details.prcCash = prcCash[i].value;
			details.prcCharge = prcCharge[i].value;
			result = window.opener.appendOrder(list,details);
			msg += "     x" + qty[i].value + " " + nm[i].value + "\n";
			qty[i].value = 0;
			prod[i].checked = false;
		}
	}
	window.opener.refreshTotal();
	if (msg)
		msg = "The following items were added to the order tray:\n" + msg;
	else
		msg = "An error has occurred! The selected items were not added...";	
	alert(msg);
}

function startAJAXSearch(searchID, page) {
	//empties the ajax_display span below;
	$('ajax_display').innerHTML = "";
	var searchEL = $(searchID);
	if (!page) page = 0;
	var last_page;
	/*
	if (window.parent.$('area')) {
		areaSelected = window.parent.$('area').options[window.parent.$('area').selectedIndex].value;
	}
	*/
	//Added by Chritian 02-10-20
	var isBarcode = '';
	var isName = searchEL.value;
	if(!isNaN(isName) && isName.length>=barcodeLength) {
		isBarcode = searchEL.value;
		isName = '';
	}
	//end by Chritian 02-10-20

	var request_mode = "<?= $request_mode ?>";
	var areaSelected = $('area').value;
	var mixed_misc = "<?= $mixed_misc ?>";
	var discountID = <?= $_GET['d'] ? ("'".$_GET['d']."'") : "null" ?>;
	var encounter_nr = "<?= $_GET['encounter_nr'] ?>";
	//if (window.parent.$('discountid')) discountID=window.parent.$('discountid').value;
	// if (searchEL && (lastSearch!=searchEL.value || lastSearchPage!=page)) {
	if (true) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		var script = "xajax_populateProductList('"+searchID+"',"+page+",'"+isName+"'" +
			",'"+discountID+"'" +
			",'"+areaSelected+"'"+
			",'"+disableInput+"'"+
			", "+mixed_misc+", '"+request_mode+"', '"+encounter_nr+"'"+
			", '"+isBarcode+"')";
		AJAXTimerID = setTimeout(script,200);
		lastSearch = searchEL.value;
		lastSearchPage = page;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

// -->
</script> 

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>

<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/order-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
// $j(document).ready(function(){
// 		$j(".checkbox").change(function() {
// 		    if(this.checked) {
// 		       startAJAXSearch('search','',1);return false;
// 		    }else{
// 		    	 startAJAXSearch('search');return false;
// 		    }
// 		});
// });

</script>


<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

//for inventory
//get all assigned inventory area
require_once($root_path.'include/care_api_classes/class_inventory.php');
$inv_obj = new Inventory;

$inv_area = $inv_obj->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);
$inv_area_default = $inv_obj->getPharmaAreaByuserDefault($_SESSION['sess_login_personell_nr']);
$mysq_area=$_GET['areas'];
if (!empty($inv_area_default)) {
	$mysq_area = $inv_area_default['area_code'];	
}
// var_dump($inv_obj->sql); die();
$select_area = '';
if($inv_area){
	while ($row = $inv_area->FetchRow()){
		$select_area .= "<option ".(($row['is_deleted'] == 1) ?"title='".$row['area_name']." area has deactivated.'" : "")." value=\"".$row['area_code']."\" ".(($row['is_deleted'] == 1) ?"disabled" : "")." ".($row['area_code']==$mysq_area ? "selected" : "").">".$row['area_name']."</option>\n";
	}
}
//end inventory

?>
	<!-- ajax_display displays message ex. error in inventory side -->
	<span id="ajax_display"></span>
	<table width="100%" cellspacing="2" cellpadding="2" style="" scroll="scroll">
		<tbody>
			<tr>
				<td>
					<div style="padding:4px 2px; padding-left:10px;background-color:#e5e5e5;">
						<table width="100%" border="0" cellpadding="0" cellspacing="2">
							<tr>
								<td width="12%" nowrap="nowrap"><span style="font:bold 12px Arial;color:#2d2d2d;">Search item/barcode</span></td>
								<td width="30%"><input id="search" class="jedInput" type="text" style="width:95%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (event.keyCode==13) startAJAXSearch(this.id)" /></td>
								<td width="30%">
									<button style="cursor: pointer;" class="segButton" onclick="startAJAXSearch('search');return false;"><img src="<?= $root_path ?>gui/img/common/default/magnifier.png"/>Search</button>
								</td>
							
								<td width="25%">
								<!-- <small>show item only in this area</small><input id="availability" class="checkbox" type="checkbox" name="">
								 -->
									<select class="segInput" name="area" id="area" onchange="startAJAXSearch('search');return false;"><?php echo $select_area; ?></select>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #0d4688; overflow-y:scroll; height:290px; width:100%; background-color:#e5e5e5">
						<table id="product-list" class="jedList" cellpadding="1" cellspacing="1" width="100%">
							<thead>
								<tr class="nav">
									<th colspan="12">
										<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
											<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
											<span title="First">First</span>
										</div>
										<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
											<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
											<span title="Previous">Previous</span>
										</div>
										<div id="pageShow" style="float:left; margin-left:10px">
											<span></span>
										</div>
										<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
											<span title="Last">Last</span>
											<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
										</div>
										<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
											<span title="Next">Next</span>
											<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
										</div>
									</th>
								</tr>
								<tr>
									<th width="1%">Name/Description</th>
									<th width="2%" align="center">Item/Barcode</th>
									<th width="4%" style="" colspan="2" nowrap="nowrap">Cash/Charge<?= $_GET['d'] ? " (".$_GET['d'].")" : "" ?></th>
									<th width="4%" style="font-size:10px" colspan="2" nowrap="nowrap">Cash/Charge<br />(Senior Citizen)</th>
									<th width="2%" align="center">InStock</th>
									<th width="2%" align="center">Quantity</th>
									<th width="2%" align="center">Dosage</th>
									<th width="1%" align="center">Frequency</th>
									<th width="1%" align="center">Route</th>
									<th width="1%">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="12">No such product exists...</td>
								</tr>
							</tbody>
						</table>
					</div>
					<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="OverrideItem" style="display: none;">
		<img style="position: absolute;margin-top: 17px;" src="<?php echo $root_path."img/information.png"; ?>">
		<P style="color: blue;font-size: 15px;margin-left: 45px;">
		<font>Lost connection in inventory server<br>
		  Do you want to proceed?</font>
		</P>
		
	</div>
	<!-- new prompt msg -->
	<dl id="error-message" style="display: none;">
			<dt>System connection error in inventory server</dt>
			     <dd>
		<label id="msgInfoError" style="color:#0055bb;font-size: 15px;">Please Contact Administrator.</label> &nbsp;&nbsp;<a id="closeDD" style="cursor: pointer;">Close</a>
		</dd>
	</dl>
					
	
	<input type='hidden' id='DAIcon' name='DAIcon' value='<?php echo $offline; ?>'>
	<input type='hidden' id='INV_address' name='INV_address' value='<?php echo $hospitalInfo->INV_address; ?>'>
	<input type='hidden' id='hasClicked' name='hasClicked' value="0">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="classificatIon" id="ifClassified" value="<?php echo $_GET['d']; ?>">
	<input type="hidden" name="isBloodBB" id="isBloodBB" value="<?= $_GET['isBloodBB'] ?>">

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

<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
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
