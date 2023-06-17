<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/order.common.php");

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
global $db;

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
$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-pharma-products-edit.php';


# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_pharma_product.php");
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');


$pclass = new SegPharmaProduct();

#added by VAN 04-07-2011
$_POST['price_cash'] =  str_replace(",","",stripslashes($_POST['price_cash']));
$_POST['price_charge'] =  str_replace(",","",stripslashes($_POST['price_charge']));
#------

# Saving
if (isset($_POST["submitted"])) {
//add remarks to array , Macoy, 2014-05-20
	$data = array(
		'bestellnum'=>$_POST['bestellnum'],
		'generic'=>$_POST['generic'],
		'artikelname'=>$_POST['artikelname'],
		'description'=>$_POST['description'],
        'is_fs'=> ($_POST['is_fs'] ? 1 : 0),
		'is_socialized'=>($_POST['is_socialized'] ? 1 : 0),
		'prod_class'=>$_POST['prod_class'],
		'price_cash'=>$_POST['price_cash'],
		'price_charge'=>$_POST['price_charge'],
		'create_id'=>$_SESSION['sess_temp_userid'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis'),
		'create_time'=>date('YmdHis'),
		'category_id'=>$_POST['category_id'],
		'remarks' =>$_POST['remarks'],
		'is_in_inventory' =>($_POST['is_in_inventory'] ? 1 : 0),
        'drug_code'=>$_POST['drug_code']

	);
	$response = false;
    $pclass->startTrans();
	if ($_GET['nr']) {
		$data["history"] = $pclass->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		$pclass->setDataArray($data);
		$pclass->where = "bestellnum=".$db->qstr($_GET['nr']);
		$saveok=$pclass->updateDataFromInternalArray($_GET["nr"],FALSE);
	}
	else {
		$data["bestellnum"] = $pclass->createNR();
		$data["history"] = $pclass->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		$pclass->setDataArray($data);
		$saveok=$pclass->insertDataFromInternalArray();
	}

	if ($saveok) {

	    include __DIR__. '/../../include/care_api_classes/ehrhisservice/Ehr.php';
        $ehr = Ehr::instance();
        if($_POST['prod_class']=='M'){
            $response = $ehr->pharma_savepharmaitem(array(
                'pharma_item' => array(
                    'item_code' => $_POST['bestellnum'],
                    'drug_code'=>$_POST['drug_code'],
                    'brand_name'=>$_POST['artikelname'],
                    'item_name'=> $_POST['generic'],// ok kani nalang
                    'item_desc'=>$_POST['description'],
                    'is_deleted' => '0',
                    'inventory_typeid' => 11,
                    'update_create_date'=>date('yyy-mm-dd hh:mm:ss'),
                )
            ));
        }
        if(!$response->status  || $response === false){// false ni by default once naay syntax error sa ehr or something
            // var_dump($ehr->getResponseData());
            if($_POST['prod_class']!='M'){
                $pclass->CompleteTrans();
            }else{
                $pclass->FailTrans();
            }

        }else{
            $pclass->CompleteTrans();
        }

		if (!$_GET['nr']) $_GET['nr'] = $_POST['bestellnum'];
		$pclass->clearProductAvailability($_GET['nr']);
		$pclass->clearProductClassification($_GET['nr']);
		$pclass->clearProductDiscounts($_GET['nr']);
		$pclass->FsProductPackage($_GET['nr'],($_POST['is_fs'] ? 1 : 0));
		if ($_POST['availability']) {
			$pclass->setProductAvailability($_GET['nr'], $_POST['availability']);
		}
		if ($_POST['classification']) {
			$classificationArr = explode(",",$_POST['classification']);
			$pclass->setProductClassification($_GET['nr'], $classificationArr);
		}
		if ($_POST['discounts']) {
			$pclass->setProductDiscounts($_GET['nr'], $_POST['discounts'], $_POST['price']);
			print_r($db->ErrorMsg());
		}

		$smarty->assign('sysInfoMessage',"Product successfully saved!");
	}
	else {
		# Payment not saved
		$smarty->assign('sysErrorMessage',"Error processing request...<br>Error:".$pclass->sql." ".$db->ErrorMsg());
	}
	$Row = $_POST;
	$Row['availability'] = implode(",",$_POST['availability']);
}
else {
	if ($_GET['nr']) {
		$NR = $_GET['nr'];
		$Row = $pclass->getProductInfo($NR);

		if (!$Row) {
			die("Invalid product code.");
			exit;
		}
	}
}

 $smarty->assign('sRootPath',$root_path);
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Product databank");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Product databank");

 # Assign Body Onload javascript code
 $onLoadJS='onload="optTransfer.init(document.forms[0])"';
 $onLoadJS='onload="$(\'generic\').focus()"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
?>
    <script type='text/javascript' src='<?=$root_path?>js/jquery/jquery-1.8.2.js'></script>
    <script type='text/javascript' src='<?=$root_path?>js/jquery/select2-3.5.3/select2.js'></script>
    <link rel='stylesheet' type="text/css" href='<?= $root_path ?>js/jquery/select2-3.5.3/select2.css'>
<script>
    var $j = jQuery.noConflict();
</script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/OptionTransfer.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/cashier-main.js?t=<?=time()?>"></script>
<script type="text/javascript">
var trayItems = 0;
var discountItems = 0;


$j(document).ready(function(){
    $j('#drug_code').select2({
        width : 230,
        placeholder: 'Select a Drug Description'
    });
});



var optTransfer = new OptionTransfer("srclist","destlist");
optTransfer.setAutoSort(true);
optTransfer.setDelimiter(",");
optTransfer.setStaticOptionRegex("");
optTransfer.saveNewRightOptions("classification");

function getDrugDescription() {
    document.getElementById("drug_desc").innerHTML =document.getElementById('drug_code').value;
}


function validate(f) {
	// !! - Do this
	if (!f.artikelname.value) {
		alert('Please enter the product name...');
		f.artikelname.focus();
		return false;
	}
    
    if(!f.generic.value && f.prod_class.value == 'M'){
        alert('Please enter the generic name...');
        f.generic.focus();
        return false; 
    }
    var cash = parseFloat(f.price_cash.value.replace (/,/g, ""));
    var charge = parseFloat(f.price_charge.value.replace (/,/g, ""));
    var cost = parseFloat(f.price_cost.value.replace (/,/g, ""));
    var computed_cash = parseFloat(cost * (1 + .10));
    var computed_charge = parseFloat(cost * (1 + .30));

	if (!cash || cash <= 0 || cash < computer_cash) {
		alert('Please enter a valid value for the retail (cash) price...');
		f.price_cash.focus();
		return false;
	}
	if (!charge || charge <= 0 || charge < computer_charge) {
		alert('Please enter a valid value for the retail (charge) price...');
		f.price_charge.focus();
		return false;
	}
	return true;
}

function onChangeProdClass() {
	$('generic').disabled = ($('prod_class').value == 'S')
}
function onChangeProdCategory(id){
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function tabClick(listID, index) {
	var dList = $(listID);
	if (dList) {
		var listItems = dList.getElementsByTagName("LI");
		if (listItems[index]) {
			for (var i=0;i<listItems.length;i++) {
				if (i!=index) {
					listItems[i].className = "";
					if ($("tab"+i)) $("tab"+i).style.display = "none";
				}
			}
			if ($("tab"+index)) $("tab"+index).style.display = "block";
			listItems[index].className = "segActiveTab";
		}
	}
}

function toggleTBody(list) {
	var dTable = $(list);
	if (dTable) {
		var dBody = dTable.getElementsByTagName("TBODY")[0];
		if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
	}
}

function toggleCheckboxesByName(name, val) {
	var chk = document.getElementsByName(name);
	if (chk) {
		for (var i=0; i<chk.length; i++) {
			chk[i].checked = val;
		}
		return false;
	}
	return false;
}

function enableInputChildren(id, enable) {
	var el=$(id);
	if (el) {
		var children = el.getElementsByTagName("INPUT");
		if (children) {
			for (i=0;i<children.length;i++) {
				children[i].disabled = !enable;
			}
			return true;
		}
	}
	return false;
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

function clearDiscount(list) {
	if (!list) list = $('discountprices');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			discountItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function removeDiscount(id) {
	var destTable, destRows;
	var table = $('discountprices');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if (!document.getElementsByName("discounts[]") || document.getElementsByName("discounts[]").length <= 0)
			addDiscount(false, null);
		reclassRows(table,rndx);
	}
}

function addDiscount(list,details) {
	if (!list) list = $('discountprices');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('discounts[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details) {
				var id = details.id;
				var showPrice = (isNaN(details.price) || details.price==0) ? 'Arbitrary' : formatNumber(details.price,2);
				if (items) {
					if ($('id'+id)) {
						//alert($('qty'+id).innerHTML);
						$('price'+id).value	= details.price;
						$('show-price'+id).innerHTML 	= showPrice;
						return true;
					}
					if (items.length == 0) clearDiscount(list);
				}

				alt = (dRows.length%2)+1;
				src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
					'<input type="hidden" name="discounts[]" id="id'+id+'" value="'+details.id+'" />'+
					'<input type="hidden" name="price[]" id="price'+id+'" value="'+details.price+'" />'+
					'<td><span style="color:#660000">'+details.name+'</span></td>'+
					'<td class="rightAlign">'+
						'<span id="show-price'+id+'" style="font:bold 11px Tahoma">'+showPrice+'</span>'+
					'</td>'+
					'<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeDiscount(\''+id+'\')"/></td>'+
				'</tr>';
				discountItems++;
			}
			else {
				src = "<tr><td colspan=\"3\">No discounts added...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function prepareAdd() {
	var details = new Object();
	var dsc;

	while (true) {
		dsc = prompt("Enter discounted price:");
		if (dsc==null) return false;
		if (!isNaN(dsc)) {
			dsc = parseFloatEx(dsc)
			break;
		}
	}

	details.id = $("sel-discount").options[$("sel-discount").selectedIndex].value;
	details.name = $("sel-discount").options[$("sel-discount").selectedIndex].text;
	details.price = dsc;
	addDiscount($('discountprices'),details);
}
</script>
<style>
	.decoratedErrorField{
	    border:2px solid rgba(255, 0, 0, 0.75) !important;
	}
</style>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

# Render form values
if (isset($_POST["submitted"])) {
}
else {
//	$smarty->assign('sBtnAddMiscFees','<input class="segInput" type="button" value="Other hospital services"
//			 onclick="return overlib(
//				OLiframeContent(\'seg-cashier-hospital-services.php\', 600, 240, \'fMiscFees\', 1, \'auto\'),
//				WIDTH,600, TEXTPADDING,0, BORDER,0,
//				STICKY, SCROLL, CLOSECLICK, MODAL,
//				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
//				CAPTIONPADDING,4,
//				CAPTION,\'Add Hospital Service\',
//				MIDX,0, MIDY,0,
//				STATUS,\'Other hospital services\');"
//			 onmouseout="nd();" />');

//	$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
//			 onclick="overlib(
//				OLiframeContent(\'seg-order-select-enc.php\', 700, 400, \'fSelEnc\', 0, \'auto\'),
//				WIDTH,700, TEXTPADDING,0, BORDER,0,
//				STICKY, SCROLL, CLOSECLICK, MODAL,
//				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
//				CAPTIONPADDING,4,
//				CAPTION,\'Select registered person\',
//				MIDX,0, MIDY,0,
//				STATUS,\'Select registered person\'); return false;"
//			 onmouseout="nd();" />');

//	$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
//			 onclick="return overlib(
//				OLiframeContent(\'seg-order-tray.php\', 600, 340, \'fOrderTray\', 1, \'auto\'),
//				WIDTH,600, TEXTPADDING,0, BORDER,0,
//				STICKY, SCROLL, CLOSECLICK, MODAL,
//				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
//				CAPTIONPADDING,4,
//				CAPTION,\'Add product from Order tray\',
//				MIDX,0, MIDY,0,
//				STATUS,\'Add product from Order tray\');"
//			 onmouseout="nd();">
//			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
}

//add remarks to smarty, Macoy, 2014-05-20
if (!$_GET['nr']) {
	$nr = $pclass->createNR();
	$smarty->assign('sProductCode','<input class="segInput" type="text" name="bestellnum" id="bestellnum" size="20" value="'.$nr.'" readonly="readonly"/>');
}
else {
	$smarty->assign('sProductCode','<input class="segInput" type="text" name="bestellnum" id="bestellnum" size="20" value="'.$_GET['nr'].'" readonly="readonly"/>');
}
$smarty->assign('sItemCode','<input readOnly class="segInput" type="text" name="item_code" id="item_code" size="30" value="'.$Row['item_code'].'" />');
$smarty->assign('sBarcode','<input readOnly class="segInput" type="text" name="barcode" id="barcode" size="30" value="'.$Row['barcode'].'" />');
$smarty->assign('sGenericName','<input class="segInput" type="text" name="generic" id="generic" size="30" value="'.$Row['generic'].'" '.($Row['prod_class']=='S' ? 'disabled="disabled"' : '').'/>');
//$smarty->assign('sDrugCode','<input class="segInput" type="text"  onkeyup="getDrugDescription();" onchange="getDrugDescription();"name="drug_code" id="drug_code" size="30" value="'.$Row['drug_code'].'"/>');

//$smarty->assign('sDrugCode','<select id="drug_code">
//<option>Option1</option>
//<option>Option2</option>
//<option>Option3</option>
//</select>');
$sqlDrugCode="SELECT spm.`drug_code` , spm.`description` FROM `seg_phil_medicine` AS spm ORDER BY spm.`description`";
$resultDrugCode= $db->Execute($sqlDrugCode);
if($resultDrugCode) {
    $drug_field = '<select class="segInput" id="drug_code" name="drug_code" >';
    $drug_field .= '<option value="NONE">SELECT DRUG DESCRIPTION</option>';
    while ($row = $resultDrugCode->FetchRow()) {
        $drug_field .= '<option value=' . $row["drug_code"] . '';
        if ($Row["drug_code"] == $row["drug_code"]){
            $drug_field .= " selected='selected'";
        }
        $drug_field .= ">" . $row["description"] . "</option>";
    }
    $drug_field .= '</select>';

$smarty->assign('sDrugCode', $drug_field);
}
//$smarty->assign('sDrugDesc',($Row['drug_code']? $Row['drug_code'] : "Drug Description not found.  "));

$smarty->assign('sProductName','<input class="segInput" type="text" name="artikelname" id="artikelname" size="30" value="'.$Row['artikelname'].'" />');
$smarty->assign('sDescription','<textarea class="segInput" cols="27" rows="2" name="description" id="description">'.$Row['description'].'</textarea>');
$smarty->assign('sIsFs', '<input class="segInput" type="checkbox" name="is_fs" id="is_fs"' . ($Row['is_fs'] ? 'checked="checked"' : '') . '/>');
$smarty->assign('sIsSocialized','<input class="segInput" type="checkbox" name="is_socialized" id="is_socialized" '.($Row['is_socialized'] ? 'checked="checked"' : '').'/>');
$smarty->assign('sCashPrice','<input class="segInput" type="text" name="price_cash" id="price_cash" style="text-align:right" value="'.number_format($Row['price_cash'],2).'"/>');
$smarty->assign('sChargePrice','<input class="segInput" type="text" name="price_charge" id="price_charge" style="text-align:right" value="'.number_format($Row['price_charge'],2).'"/>');
$smarty->assign('sRemarks',$Row['remarks']);

if (empty($Row['item_code'])) {
$smarty->assign('sIsInInventory','<input class="segInput" type="checkbox" disabled name="is_in_inventory" id="is_in_inventory" '.($Row['is_in_inventory'] ? 'checked="checked"' : '').' />');
}
elseif(!empty($Row['item_code'])){
$smarty->assign('sIsInInventory','<input class="segInput" type="checkbox"  name="is_in_inventory" id="is_in_inventory" '.($Row['is_in_inventory'] ? 'checked="checked"' : '').' />');
}
$smarty->assign('sProductType',
					'<select class="segInput" id="prod_class" name="prod_class" onchange="onChangeProdClass()">
						<option value="M" '.($Row['prod_class']=='M' ? 'selected="selected"' : '').'>Medicines</option>
						<option value="S" '.($Row['prod_class']=='S' ? 'selected="selected"' : '').'>Supplies</option>
					</select>');

//for inventory



require_once($root_path.'include/care_api_classes/class_inventory.php');
require_once($root_path . 'include/care_api_classes/inventory/InventoryService.php');
require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
$invService = new InventoryService();
$NewInvService = new InventoryServiceNew();#added by MARK 2016-10-28
$inv_obj = new Inventory;

$inv_area = $inv_obj->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);

$invArr = array();
// noArea
if ($inv_area) {
		while ($areaRow = $inv_area->FetchRow()){
			$invArr[] = $areaRow['area_code'];
		}
}else{
	exit("<h2>Please contact IHOMP for inventory area assignment.</h2>");
}
if ($Row['is_in_inventory']) {
	# code...

# START added by MARK 2016-10-28
$newAreaCode = implode("','", $invArr);
$API_key_INV= array();
$landed_cost = array();
$connectionLoss = "";
$API_KEY = $db->Execute("SELECT * FROM seg_pharma_areas WHERE area_code IN ('".$newAreaCode."')");
if ($API_KEY) {
	while ($rowAPI=$API_KEY->FetchRow()) {
	     $API_key_INV[] = $rowAPI['inv_api_key'];
	
	}
}

foreach ($API_key_INV as $newKey) {
		$dataItem = $NewInvService->GetItemListFromDai($newKey,'item_price');
		if ($dataItem == 0) {
			$connectionLoss = "<font style='color:red'>INVENTORY SYSTEM IS DOWN. Please contact administrator</font>";
		}else{
				if ($dataItem == 404) {
					$landed_cost[] = '0';
				}else{
							$dataItem = $NewInvService->GetItemListFromDai($newKey,'item_price');
							$dataItems = count($dataItem['iteminfo']['barcode']);
							if ($dataItems == 1) {
								if($dataItem['iteminfo']['barcode'] == $Row['barcode']){
									if ($dataItem['iteminfo']['price'] !=0 || $dataItem['iteminfo']['price'] >=1)
										$landed_cost[] = $dataItem['iteminfo']['price'];
								}
							}else{
								foreach ($dataItem['iteminfo'] as $key => $NeWvalue) {
					 					if ($NeWvalue['barcode'] ==$Row['barcode']) {
					                    		if ($NeWvalue['price'] !=0 || $NeWvalue['price'] >=1)
					                    			$landed_cost[] = $NeWvalue['price'];
					 								
					                    	}
					 			}
				 			}	
					}
		}

}
// var_dump($landed_cost); die();
$smarty->assign('sCostPrice','<label>'.(empty($connectionLoss) ? number_format(array_sum($landed_cost),2) : $connectionLoss).'</label>');
}else{
	$smarty->assign('sCostPrice','<label>n/a</label>');
}
#END added by MARK 2016-10-28
// try {
//     $sendArr = array(
//         'barcode' => $Row['barcode'],
//         'item_code' => $Row['item_code']
//     );

//     if(!empty($sendArr['barcode']) || !empty($sendArr['item_code'])){
//     	$res = $invService->getItemInfo($invArr[0], $sendArr);
// 	    if(!empty($res['price'])){
// 	    	$cash = $Row['price_cash'];
// 	    	$charge = $Row['price_charge'];
// 	    	$cost = $res['price'];
// 	    	$computer_cash = $cost * (1 + .10);
//     		$computer_charge = $cost * (1 + .30);

//     		$warning = "";
// 	    	if($cash < $computer_cash || $charge < $computer_charge)
// 	    		$warning = "decoratedErrorField";
// 			$smarty->assign('sCostPrice','<input type="hidden" name="barcode" value="'.$Row['barcode'].'"/><input type="hidden" name="item_code" value="'.$Row['item_code'].'"/><input class="segInput '.$warning.'" readOnly type="text" name="price_cost" id="price_cost" style="text-align:right" value="'.number_format($res['price'],2).'"/>');
// 	    }
//     }
// } catch (Exception $exc) {
//     // echo $exc->getTraceAsString();die;
// }

//added code by angelo m. 07.05.2010
//start
$strSQL="SELECT a.id,a.description FROM seg_type_product_category as a WHERE a.is_deleted<>1";
$result= $db->Execute($strSQL);
if($result){
	$control_field= '<select class="segInput" id="category_id" name="category_id" onchange="onChangeProdCategory(this.value)">'.
		'<option value="">Uncategorized</option>';
	while($row=$result->FetchRow()){
			 $control_field.='<option value='.$row["id"].'';
				if($Row["category_id"]==$row["id"])
					$control_field.=" selected='selected'";
				$control_field.=">".$row["description"]."</option>";
	}
	$control_field.='</select>';
	$smarty->assign('sProductCategory',$control_field);
}
//end


# Product classification
$classificationHTML = "<select id=\"srclist\" name=\"srclist\" class=\"segInput\" size=\"3\" multiple=\"multiple\" style=\"width:170px\">\n";
if ($Row["classification"])
	$result = $db->Execute("SELECT * FROM seg_product_classification WHERE lockflag=0 AND class_code NOT IN (".$Row["classification"].") ORDER BY class_name");
else
	$result = $db->Execute("SELECT * FROM seg_product_classification WHERE lockflag=0 ORDER BY class_name");
if ($result) {
	while ($row=$result->FetchRow()) {
		$classificationHTML.="						<option value=\"".$row["class_code"]."\">".$row['class_name']."</option>\n";
	}
}
$classificationHTML .= "					</select>";
$smarty->assign('sSelectClassification',$classificationHTML);

if ($Row["classification"]) {
	$result = $db->Execute("SELECT * FROM seg_product_classification WHERE lockflag=0 AND class_code IN (".$Row["classification"].") ORDER BY class_name");
	$destHTML = "<select id=\"destlist\" name=\"destlist\" class=\"segInput\" size=\"3\" multiple=\"multiple\" style=\"width:170px\">\n";
	while ($row=$result->FetchRow()) {
		$destHTML.="						<option value=\"".$row["class_code"]."\">".$row['class_name']."</option>\n";
	}
	$destHTML.="					</select>";
	$smarty->assign('sSelectClassification2',$destHTML);
}
else
	$smarty->assign('sSelectClassification2','<select id="destlist" name="destlist" class="segInput" size="3" multiple="multiple" style="width:170px"></select>');

# Availability
$availHTML = "";
$result = $db->Execute("SELECT * FROM seg_pharma_areas WHERE lockflag=0 ORDER BY area_name");

#added by VAN 12/20/2016
$availability = explode(",", $Row['availability']);

if ($result) {
	while ($row=$result->FetchRow()) {

		#edited by VAN 12/20/2016
		#$checked = (strpos($Row['availability'], $row['area_code']) !== FALSE) ? 'checked="checked"' : '';
		$checked = (in_array($row['area_code'], $availability, TRUE)) ? 'checked="checked"' : '';
		$availHTML.="
					<span style=\"white-space:nowrap\">
						<input class=\"segInput\" id=\"avail".$row['area_code']."\" name=\"availability[]\" type=\"checkbox\" value=\"".$row["area_code"]."\" $checked /><label class=\"segInput\" for=\"avail".$row['area_code']."\">".$row["area_name"]."</label>
					</span>\n";
	}
}
// if ($Row['is_in_inventory']) {
// 	$gone = "display:none;";
// 	$gone2 = "style='display:none;'";
// }
   $smarty->assign('sAvailability',"<div style='".$gone."'>".$availHTML."</div>");
   $smarty->assign('sAvailability_True',TRUE);
   $smarty->assign('styles',$gone2);
# Discounts
$discountHTML = "<select id=\"sel-discount\" class=\"segInput\" onchange=\"$('add-discount').disabled = !this.value \">\n".
			"<option value=\"\">--Select discount class--</option>\n";
$result = $db->Execute("SELECT * FROM seg_discount WHERE lockflag=0 ORDER BY discountdesc");
if ($result) {
	while ($row=$result->FetchRow()) {
		$discountHTML.="<option value=\"".$row["discountid"]."\">".$row['discountdesc']."</option>\n";
	}
}
$discountHTML .= "</select>";
$smarty->assign('sSelectDiscount',$discountHTML);

if ($_GET['nr'] && !$_POST['submitted']) {
	$result=$pclass->getProductDiscounts($nr);
	if ($result) {
		$count=0;
		$Row['discounts'] = array();
		$Row['price'] = array();
		while ($row=$result->FetchRow()) {
			$Row['discounts'][] = $row['discountid'];
			$Row['price'][] = $row['price'];
		}
	}
}

$count = 0;
if ($Row['discounts']) {
	foreach ($Row['discounts'] as $i=>$v) {
		$class = (($count%2)==0)?"":"wardlistrow2";
		if ($Row['price'][$i]==0)
			$showPrice = 'Arbitrary';
		else
			$showPrice = number_format($Row['price'][$i],2);
		$name = $db->GetOne("SELECT discountdesc FROM seg_discount WHERE discountid='$v'");
		$rows .= '<tr class="'.$class.'" id="row'.$v.'">
					<td>
						<input type="hidden" name="discounts[]" id="id'.$v.'" value="'.$v.'" />
						<input type="hidden" name="price[]" id="price'.$v.'" value="'.$Row['price'][$i].'" />
						<span style="color:#660000">'.$name.'</span>
					</td>
					<td class="rightAlign">
						<span id="show-price'.$v.'" style="font:bold 11px Tahoma">'.$showPrice.'</span>
					</td>
					<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeDiscount(\''.$Row['discounts'][$i].'\')"/></td>
				</tr>';
		$count++;
	}
}


if (!$rows)	$rows = '		<tr><td colspan="3">No discounts set...</td></tr>';
$smarty->assign('sDiscounts',$rows);

if ($_GET['nr'])
 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&nr='.$_GET['nr'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate(this)">');
else
 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate(this)">');
$smarty->assign('sFormEnd','</form>');


ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="refno" value="<?php echo $sRefNo?>">
	<input type="hidden" name="dept" value="<?php echo $sDept?>">
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
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');
$smarty->assign('sContinueButton','<input class="segInput" type="submit" src="'.$root_path.'images/btn_submitorder.gif" align="center" value="Process payment">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','pharmacy/databank-form.tpl');
$smarty->display('common/mainframe.tpl');

?>