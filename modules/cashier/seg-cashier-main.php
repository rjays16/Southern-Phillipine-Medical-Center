<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'modules/cashier/ajax/cashier-main.common.php';

// added by carriane 10/24/17
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);    
// end carriane.

define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path."include/care_api_classes/class_cashier_service.php");
include_once($root_path."include/care_api_classes/class_cashier.php");
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/care_api_classes/class_credit_collection.php');
$cClass = new SegCashier();
$sClass = new SegCashierService();

require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

if (!$_GET['from'])
	$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-main.php';

$sRefNoArray = array();
$sDeptArray = array();

if ($_GET['or']) {
	$rs_pay_ref = $cClass->GetPayReferences($_GET['or']);
	$sRefNoArray = array();
	$sDeptArray = array();
	while ($row_pay_ref = $rs_pay_ref->FetchRow()) {
		$sRefNoArray[] = $row_pay_ref['ref_no'];
		$sDeptArray[] = $row_pay_ref['ref_source'];
	}

	$rs_pay_req = $cClass->GetPayRequests($_GET['or']);
	$checked_requests = array();
	$tab_enabled = array();
	while ($row_pay_req = $rs_pay_req->FetchRow()) {
		$pkey = strtolower($row_pay_req['ref_source']) . "" . $row_pay_req['ref_no'] . "" . $row_pay_req['service_code'];
		$tab_enabled[strtolower($row_pay_req['ref_source'])] = TRUE;
		$checked_requests[$pkey] = $row_pay_req;
	}
}
elseif ($_REQUEST['reference']) {
	foreach ($_REQUEST['reference'] as $i=>$v) {
		$values = explode("_",$v);
		$sRefNoArray[] = $values[1];
		$sDeptArray[] = strtolower($values[0]);
	}
}
elseif ($_GET['ref']) {
	$sRefNoArray[]=$_GET["ref"];
	$sDeptArray[]=strtolower($_GET["dept"]);
}

/*
if (!$submitted && (!$sRefNoArray || !$sDeptArray)) {
	die("Invalid reference code or source department...");
}
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Saving
$saveok = false;
if (isset($_POST["submitted"])) {

	$db->StartTrans();
	$ORNo = $_POST["orno"];
	$ORDate = $_POST["ordate"];
	$AmtTendered = $_POST["amount_tendered"];
	$Remarks = $_POST["remarks"];

	$requests = $_POST['requests'];
    require_once($root_path.'include/care_api_classes/class_ward.php');
    $wardObj = new Ward();
    if(isset($_POST['encounter_nr'])) {
        $ward = $wardObj->EncounterLocationsInfo($_POST['encounter_nr']);
        //if ward id starts with mhc which is Mindanao Heart Center
        if(strtoupper(substr($ward['ward_id'],0,3)) == 'MHC') {
            $wardLocation = 'MHC';
        }
    }
	$total_due = 0;
	if (is_array($requests)) {
		$ref_arr = array();
		$src_arr = array();
		$svc_arr = array();
		$qty_arr = array();
		$amt_arr = array();
		$lab_items = array();
		foreach ($requests as $req) {
			$srcDept = strtolower(substr($req, 0, 2));
			if ($srcDept == 'ot') {
				$srcDept = strtolower(substr($req, 0, 5));
				$refNo = substr($req, 5);
			}
			else if ($srcDept == 'mi') {
				$srcDept = strtolower(substr($req, 0, 4));
				$refNo = substr($req, 4);
			}
            else if ($srcDept == 'po') {
                $srcDept = strtolower(substr($req, 0, 3));
                $refNo = substr($req, 3);
            }            
            else {
				$srcDept = strtolower(substr($req, 0, 2));
				$refNo = substr($req, 2);
			}

			$items = $_POST[$req];
            /**
             * substitute encounter_nr as refno for dialysis payment.
             */
            if($req == 'db0000000000') {
                $refNo = $_POST['encounter_nr'];
            }

			if (is_array($items)) {
				foreach ($items as $i=>$item) {

                    //handle account code when patient is confined in MHC
                    if ($wardLocation == 'MHC' && ($item == 'DEPOSIT' || $item == 'PARTIAL'))
                        $srcDept = $wardLocation;

					$src_arr[] = $srcDept;
					$ref_arr[] = $refNo;
					$svc_arr[] = $item;
					$qty_arr[] = $_POST['qty_'.$req][$i];
					$amt_arr[] = $_POST['total_'.$req][$i];
					$total_due += $_POST['total_'.$req][$i];

					if (strtoupper($srcDept) == "LD") {
						if (!array_key_exists($refNo, $lab_items)) {
							$lab_items[$refNo] = array($item);
						}
						else {
							//print_r($lab_items);
							$lab_items[$refNo][] = $item;
						}
					}
				}
			}
			else {
				# Item list is empty
				# Some error handling, maybe
			}
		}
	}

	//Poriferanbob Quadrilateraltrousers

	#Added by Jarel 09/25/2013
	if($_POST['checkcompany']==''){
		$or_name = $_POST['orname']; 
	}else if ($_POST['orname']==''){
		$or_name = $_POST['checkcompany'];
	}else{
		$or_name = $_POST['orname'].'/'.$_POST['checkcompany']; 
	}
	//Added by Jane 10/18/2013
	if($_POST['cashcompany']<>''){
		$or_name = $_POST['orname'].'/'.$_POST['cashcompany'];
	}

	$data = array(
		'or_date' => $_POST['ordate'],
		'or_name' => $or_name,
		'or_address' => $_POST['oraddress'],
		'amount_tendered' => $_POST['amount_tendered'],
		'amount_due' => $total_due,
		'history' => "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n",
		'remarks' => $_POST['remarks'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_dt'=>date('YmdHis'),
	);
	$cClass->usePay();

	if ($_POST['encounter_nr'])
		$data['encounter_nr'] = $_POST['encounter_nr'];
	if ($_POST['pid'])
		$data['pid'] = $_POST['pid'];
	else{
		require_once "{$root_path}include/care_api_classes/class_walkin.php";
		$walkIn = new SegWalkin();

		$name_arr = explode(',',$or_name);
		$lastName = trim($name_arr[0]);
		$firstName = trim($name_arr[1]);

		$person = SegWalkin::findPersonByName($firstName,$lastName);
		if(is_array($person) && !empty($person)){
			$data['pid'] = $person['pid'];
			$data['or_address'] = $person['address'];
			$data['or_name'] = $person['name'];
		}else{
			$walkin_data = array(
				'pid' => $walkIn->createPID(),
				'address' => trim($_POST['orderaddress'])!="" ?trim($_POST['orderaddress']):"NOT PROVIDED",
				'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
				'create_id'=>$_SESSION['sess_temp_userid'],
				'modify_id'=>$_SESSION['sess_temp_userid'],
				'modify_time'=>date('YmdHis'),
				'create_time'=>date('YmdHis')
			);

			if ($lastName) {
				$walkin_data['name_last'] = $lastName;
			}

			if ($firstName) {
				$walkin_data['name_first'] = $firstName;
			}

			$data['pid'] = 'W'.$walkin_data['pid'];

			$walkIn->setDataArray($walkin_data);
			$errorMsg = 'Unable to save walkin data...';
			$saveok = $walkIn->insertDataFromInternalArray();
		}
	}

	if ($_GET['or']) {
		$ORNo = $_GET['or'];
		$data["history"]=$cClass->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		$cClass->setDataArray($data);
		$cClass->where = "or_no=".$db->qstr($ORNo);

		$errorMsg = "Unable to update payment information...";
		$saveok=$cClass->updateDataFromInternalArray($ORNo,FALSE);

	//added by gelie 10-14-2015
		// delete previous bill's partial if partial payment is edited
		$objCollection = new CreditCollection();
        $objCollection->deletePrevPartialInLedger($_POST['encounter_nr'], $ORNo);  
	//end gelie
	}
	else {
		$data['or_no']=$_POST['orno'];
		$data['create_id']=$_SESSION['sess_temp_userid'];
		$data['create_dt']=date('YmdHis');
		$cClass->setDataArray($data);

		$errorMsg = "Unable to update payment information...";
		$saveok=$cClass->insertDataFromInternalArray();
		$ORNo = $data['or_no'];
	}

	if ($saveok) {
		if ($_POST["chkcheck"]) {
			$checkno = $_POST["checkno"];
			$checkdate = $_POST["checkdate"];
			$checkbank = $_POST["checkbank"];
			$checkpayee = $_POST["checkpayee"];
			$checkamount = $_POST["checkamount"];
			$checkcompany = $_POST["checkcompany"];//Added by Jarel 09/25/2013
			$errorMsg = "Unable to update check details...";
			$saveok=$cClass->setCheque(
                $ORNo,
                $checkno,
                $checkdate,
                $checkbank,
                $checkpayee,
                $checkamount,
                $checkcompany
            );
		}
		else {
			// Errors should be ignored in this branch
			$cClass->unsetCheque($ORNo);
		}
	}

	if ($saveok) {
		$errorMsg = 'Unable to clear payment details for updating...';
		$saveok = $cClass->deletePaymentDetails($ORNo);
	}

	if ($saveok) {
		if (is_array($requests)) {
			if (count($ref_arr) > 0) {
				$errorMsg = 'Unable to update payment details...';
				$saveok = $cClass->processRequestArray(
                    $ORNo,
                    $ref_arr,
                    $src_arr,
                    $svc_arr,
                    $qty_arr,
                                                       $amt_arr);                
                $pocItems = $cClass->getPOCItems();
			} else {
                // No requests to process
				$errorMsg = 'No items found...';
				$saveok=FALSE;
			}

		} else {
			# Invalid request posted
			$saveok=FALSE;
		}
	}

    # added by michelle 03-17-15
    if ($saveok) {
        $objEnc = new Encounter();
        $objBillingRes = $objEnc->getSaveBilling($_POST['encounter_nr']);
        $res = $cClass->GetPayRequests($ORNo);
        if ($res !== false) {
          $rows = $res->GetRows();
          foreach ($rows as $row) {
            if ($row['ref_source'] == 'FB') {
              if ($objBillingRes) {
                $res = $objBillingRes->FetchRow();
                $billnr = $res['bill_nr'];
                $objCollection = new CreditCollection();

                # added by: syboy 09/13/2015
                # if patient does exist in seg_credit_collection_ledger
                $checkOR = $objCollection->checkORandEnc($billnr);
                if (!$checkOR && $row['service_code'] != 'PARTIAL') {
                	$objCollection->addPatientPaidAmount($_POST['encounter_nr'], $billnr, $row['amount_due'], 'paid');
                }
                //added by gelie 11-12-2015
                else if($row['service_code'] == 'PARTIAL'){
                	$objCollection->addPatientPaidAmount($_POST['encounter_nr'], $billnr, $row['amount_due'], 'partial');
                }
                //end gelie
                // if ($checkOR) {
                // 	$objCollection->updatePatientPaidAmount($_POST['encounter_nr'], $billnr, $row['amount_due'], 'paid');
                // }else{
                // 	$objCollection->addPatientPaidAmount($_POST['encounter_nr'], $billnr, $row['amount_due'], 'paid');
                // }
                # ended

              }
            }
          }
        }
    }
    # end

	if ($saveok && $db->HasFailedTrans()) {
		$errorMsg = 'Unexpected error occurred ->'. $billnr;
		$saveok = false;
	}

	if (!$saveok) {
		$db_error_msg = $db->ErrorMsg();
		$db->FailTrans();
	}

	$db->CompleteTrans();
}


$smarty->assign('sRootPath', $root_path);

# Title in the title bar
if ($_GET['or']) {
    $smarty->assign('sToolbarTitle', "Cashier::Edit payment entry");
} else {
    $smarty->assign('sToolbarTitle', "Cashier::Create payment entry");
}

# href for the help button
$smarty->assign('pbHelp', "javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
if ($_GET['or']) {
    $smarty->assign('sWindowTitle', "Cashier::Edit payment entry");
} else {
    $smarty->assign('sWindowTitle',"Cashier::Create payment entry");
}

# Assign Body Onload javascript code
if (!$submitted || !$saveok) {
	$onLoadJS="onload=\"\"";
	$smarty->assign('sOnLoadJs', $onLoadJS);
}

 # Collect javascript code
ob_start();
	 # Load the javascript code
?>

<style>
	#orname{
		padding-right: 15px;
		width: 215px;
	}
	.search-text-input{
		background: url('../../images/arrows.png') no-repeat right center;
		background-size: contain;
	}
	.loading-text-input{
		background: url('../../images/ajax-loader.gif') no-repeat right center;
		background-size: contain;
	}
</style>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript"> var $J =jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript"> var $J =jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="js/cashier-main.js"></script>
<script type="text/javascript">
/*added by art 05/17/2014 
** show/hide ic billing dashlet
*/
$J(function() {
    $J("#search-walkin").change(function(){
    	var a = $J('#search-walkin option:selected').val();

		if(a == 1){
			$J('#orname').prop('readonly',false);
		}else{
			$J('#orname').prop('readonly',true);
		}

	    if (a == 2) {
	    	$J("#fb_dashlet").hide();
	    	$J("#ic_dashlet").show();
	    }else{
	    	$J("#fb_dashlet").show();
	    	$J("#ic_dashlet").hide();
	    };
    });

});
/*end art*/
var trayItems = 0;
var theORNo = <?= $_GET['or'] ? ("'".$_GET['or']."'") : 'null'?>;
var checkedItems = '<?= implode(",",array_keys($checked_requests)) ?>';

// Preload images
var img1 = new Image();
img1.src = "<?= $root_path ?>images/ajax_bar.gif";

function init() {
	shortcut.add('F2', enterAmountTendered,
		{
			'type':'keydown',
			'propagate':false
		}
	);
	shortcut.add('ESC', cClick,
		{
			'type':'keydown',
			'propagate':false
		}
	);
	shortcut.add('F9', keyF9,
		{
			'type':'keydown',
			'propagate':false
		}
	);

<?php if (!$_GET['or']) { ?>
	xajax.call('getLatestORNumber', {});
<?php } else { ?>
	$('orno').readOnly = false;
<?php
}
if ($sRefNoArray && $sDeptArray) {
?>
	var src = ['<?= implode("','",$sDeptArray) ?>'];
	var ref = ['<?= implode("','",$sRefNoArray) ?>'];
	startLoading();
	hide_load = 0;
<?php
	# Add each FB, PP or OTHER items
	if ($checked_requests) {
		foreach ($checked_requests as $i=>$item) {
			if (in_array(strtolower($item['ref_source']),array('other','fb','pp','ic','db'))) { #edited by art 05/25/2014 added IC
?>
	xajax.call('addPFOItem', {
		mode: 'synchronous',
		parameters:['<?= $item['ref_source'] ?>','<?= $item['ref_no'] ?>','<?= $item['service_code'] ?>', '<?= $item['qty'] ?>', '<?= $item['amount_due'] ?>']
	});
<?php
			}
		}
	}
?>
	for (i=0;i<src.length;i++) {
		if (i==src.length-1) hide_load = 1;
		if (src[i] == 'fb') {
            // empty
		}
		if (src[i] == 'db') {
            // empty
		}
		else if (src[i] == 'deposit') {
            // empty
		}
		else if (src[i] == 'other') {
            // empty
		}
		else {
			xajax.call('addReference', {
				mode:'synchronous',
				parameters: [src[i], ref[i], checkedItems, hide_load, theORNo]
			});
		}
	}
<?php
}
if ( !$_GET['reference'] && !$_GET['ref'] && !$_GET['or'] ) {
?>
	personSelect();
<?php
}
$var_arr = array(
	"var_pid"=>"pid",
	"var_target"=>"casenumber",
	"var_discountid"=>"discountid",
	"var_discount"=>"discount",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"orname",
	"var_addr"=>"oraddress",
	"var_clear"=>"clear-enc",
	"var_enctype"=>"encounter_type",
	"var_enctype_show"=>"encounter_type_show",
	"var_include_walkin"=>"1"
	
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}

$var_qry = implode("&",$vars);

?>
}

function personSelect() {
	var searchScript;
	var caption;
	var a = $J('#search-walkin').val();

	/*if ($('search-walkin').checked) commented by art 05/11/2014*/
	/*edited by art 05/11/2014*/
	if (a == 1)
		searchScript = "seg-select-walkin.php";
	else if (a == 2)
		searchScript = "seg-select-company.php";
	else{
		if(typeof a !== 'undefined')
			searchScript = "seg-select-enc.php";
		else searchScript = '';
	}
	/*end art*/

	if(searchScript != ''){
		overlib(
			OLiframeContent('<?=$root_path?>modules/registration_admission/'+searchScript+'?<?=$var_qry?>&var_include_enc=0',
				700, 400, 'fSelEnc', 0, 'auto'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
				CAPTION,'Select registered person',
				MIDX,0, MIDY,0,
				STATUS,'Select registered person');
		
	}
	return false;
}

function pSearchClose() {
	$J('#select-enc').addClass('disabled');
	clearRequests();
	refreshTotal();
	cClick();
}

function enterAmountTendered() {
}

function validateOR(nr) {
	if (!nr) return false;
	return !nr.match(/\D/);
}



function validate() {

	if ($('orno').getAttribute('orOk')!=1) {
		alert('Invalid OR Number...')
		$('orno').focus()
		return false;
	}

	if (parseFloatEx($('show-change').getAttribute('value')) < 0) {
		alert('Amount tendered is less the total amount payable...');
		clickAmountTendered();
		return false;
	}

	if (!parseFloatEx($('show-net-total').getAttribute('value'))) {
		alert('Total amount payable is 0...');
		return false;
	}

	if (!$('pid').value && !$('orname').value && !$('checkcompany').value) {
		alert('Please select patient/customer...');
		return false;
	}

	if($('chkcheck').checked){
		if(!$('checkno').value){
			alert('Check Number is Required');
			$('checkno').focus();
			return false;
		}else if(!$('checkbank').value){
			alert('Bank Name is Required');
			$('checkbank').focus();
			return false;
		}else if(!$('checkpayee').value){
			alert('Payee Name is Required');
			$('checkpayee').focus();
			return false;
		}else if(!$('checkamount').value){
			alert('Check Amount is Required');
			$('checkamount').focus();
			return false;
		}

	}

	return true;
}

function disableChildrenInputs(node, disable) {
	if (node) {
		var children = node.getElementsByTagName('INPUT')
		for (var i=0;i<children.length;i++) {
			if (children[i].type != 'checkbox')	children[i].disabled = disable;
		}
	}
}

function okORNo(ok) {
	if (ok) {
		// Void function (for now)
	}
}


// Setup shortcut keys
var keyF9 = personSelect;

document.observe("dom:loaded", function() {
	// initially hide all containers for tab content
	init();
	$$('input[type=text]').each(function(o)  {
		o.observe('keypress', function(event){
			if (event.keyCode == Event.KEY_RETURN) {
				this.blur();
				Event.stop(event);
				return false;
			}
			return true;
		}.bindAsEventListener(o))
	});
	refreshTotal();
});

/*
	if (document.addEventListener) {
		document.addEventListener("DOMContentLoaded", init, false);
	}
*/
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

if (isset($pocItems) && !empty($pocItems)) {
    $smarty->append('JavaScript',"<script type=\"text/javascript\">sendPocHl7Msg('".json_encode($pocItems)."');</script>");
}

# Assign prompt messages
 $smarty->assign('bShowHospitalServices',FALSE);

if (isset($_POST["submitted"])) {
	if ($saveok) {
		# $smarty->assign('sWarning',"Pay request successfully processed...");
		$change = ($_POST['amount_tendered'] - $total_due);
		#if ($change < 0) $change = '<span style="color:green">Charity</span>';

		#Added Jarel 09/26/2013
		if($_POST['checkcompany']==''){
			$or_name = $_POST['orname'];
		}else if ($_POST['orname']==''){
			$or_name = $_POST['checkcompany'];
		}else{
			$or_name = $_POST['orname'].' / '.$_POST['checkcompany'];
		}
		//Added by Jane 10/18/2013
		if($_POST['cashcompany']<>''){
			$or_name = $_POST['orname'].'/'.$_POST['cashcompany'];
		}

		$assignArray = array(
			'sUseCheck' => $_POST['chkcheck'],
			'sUseCard' => $_POST['chkcard'],
			'sORNo' => $_POST['orno'],
			'sORName' => $or_name,
			'sORAddress' => $_POST['oraddress']?$_POST['oraddress']:$data['or_address'],
			'sORDate' => date("d M Y h:ia",strtotime($_POST['ordate'])),
			'sPID' => $_POST['pid']?$_POST['pid']:$data['pid'],
			'sEncounterNr' => $_POST['encounter_nr'],
			'sAmountDue' => number_format($total_due,2),
			'sAmountTendered' => number_format($_POST['amount_tendered'],2),
			'sAmountChange' => number_format($change,2),
			'sRemarks' => $_POST['remarks'],
			'sCheckNo' => $_POST['checkno'],
			'sCheckDate' => $_POST['checkdate'],
			'sCheckBank' => $_POST['checkbank'],
			'sCompanyName' => $_POST['checkcompany'], //Added by Jarel 09/25/2013
			'sCheckName' => $_POST['checkpayee'],
			'sCheckAmount' => $_POST['checkamount'],
			'sCardNo' => $_POST['cardno'],
			'sCardBank' => $_POST['cardbank'],
			'sCardBrand' => $_POST['cardbrand'],
			'sCardName' => $_POST['cardname'],
			'sCardExpiry' => $_POST['cardexpr'],
			'sCardAmount' => $_POST['cardamount'],
			'sMessageHeader' => "Request successfully processed..."
		);

		foreach ($assignArray as $i=>$v)
			$smarty->assign($i, $v);

		$listgen->setListSettings('MAX_ROWS','10');
		$listgen->setListSettings('RELOAD_ONLOAD',TRUE);
		$pay = &$listgen->createList('pay',
			array('Code', 'Source', 'Item Description', 'Price', 'Quantity', 'Total'),
			array(0,0,1,0,0,NULL),
			'populateORParticulars');
		$pay->fetcherParams = array($_POST['orno']);
		$pay->addMethod = 'addParticularRow';
		$pay->columnWidths = array("12%", "12%", "*", "10%", "7%", "12%");
		$smarty->assign('sItemList',$pay->getHTML());

		$smarty->assign("sysInfoMessage", "Request successfully processed...");

		# $sBreakImg ='close2.gif';
		# $smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

		$printurl = $root_path.'modules/cashier/seg-cashier-print.php'. URL_APPEND."&nr=".$ORNo;
		$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printsummary.gif" border="0" align="absmiddle" alt="Print Summary" onclick="openWindow(\''.$printurl.'\')" onsubmit="return false;" style="cursor:pointer">');

		$draftPrintUrl = $root_path.'modules/cashier/seg-cashier-draft-print.php?'.URL_APPEND."&clear_ck_sid=$clear_ck_sid&nr=$ORNo";
		$directorUrl = $root_path.'modules/cashier/launch_director.php?'.URL_APPEND."&clear_ck_sid=$clear_ck_sid&nr=$ORNo&userck=$userck";

		$smarty->assign('draftPrintURL', $draftPrintUrl);
		$smarty->assign('directorURL', $directorUrl);

		//$smarty->assign('sDraftButton','<img id="print-draft" class="segSimulatedLink"  src="'.$root_path.'images/btn_printor.gif" border="0" align="absmiddle" alt="Print OR" onclick="draftPrint(\''.$drafturl.'\')" onsubmit="return false;" style="cursor:pointer">');

        require_once $root_path.'include/care_api_classes/class_acl.php';
        $access = new Acl($_SESSION['sess_temp_userid']);
        if($access->checkPermissionRaw('_a_1_cashiernewor')) {
        	$smarty->assign('ORprint',  '<button id="btn-launch-director" style="cursor:pointer" class="segButton"><img src="../../gui/img/common/default/printer_add.png" /> Print OR (Beta)</button>');
			$smarty->assign('sOnLoadJs',"onload=\"draftPrint('$directorUrl');\"");
        } else {
            $smarty->assign('ORprint',  '<button id="btn-launch-director" disabled="disabled" class="segButton"><img src="../../gui/img/common/default/printer_add.png" /> Print OR (Beta)</button>');
    		$smarty->assign('sOnLoadJs',"onload=\"draftPrint('$draftPrintUrl');\"");
        }
		$smarty->assign('sMainBlockIncludeFile','cashier/cashier_message.tpl');

		$smarty->display('common/mainframe.tpl');

		exit();
	}
	else {
		$smarty->assign("sysErrorMessage",
            "Error processing request...<br/>".
            "Message:".$errorMsg."<br/>".
            "DB Error:".$db_error_msg."<br/>"
        );
	}
}


if ($_GET['or']) {
	$pay_info = $cClass->GetPayInfo($_GET['or'], true);
}
else {
	if ($req_details = $cClass->GetRequestInfo($sRefNoArray[0], $sDeptArray[0])) {
		$req_info = $req_details->FetchRow();
		$pay_info['or_name'] = $req_info['request_name'];
		$pay_info['or_address'] = $req_info['request_address'];
		$pay_info['pid'] = $req_info['request_pid'];
		$pay_info['encounter_nr'] = $req_info['request_encounter'];
	}
}


/* *************************************************************************************************
	 REQUESTS
 ************************************************************************************************* */
$smarty->assign('sRequestAdd','<button class="segButton" onclick="openRequest(); return false"><img src="'.$root_path.'gui/img/common/default/requests.gif">Add request</button>');
$smarty->assign('sRequestAddSocial','<button class="segButton" onclick="openRequestFromSocial(); return false"><img src="'.$root_path.'gui/img/common/default/requests.gif">Add Consultation From Social Service</button>');
/* ************************************************************************************************ */


/* *************************************************************************************************
	 BILLING
 ************************************************************************************************* */
$smarty->assign('sBillingAdd','<img class="segSimulatedLink" src="'.$root_path.'images/btn_add_billing.gif" align="absmiddle" '.
	'onclick="openBilling()">');
//added by gelie 10-02-2015
$smarty->assign('sAddPartialBill','<img class="segSimulatedLink" src="'.$root_path.'images/btn_add_partial_bill.gif" align="absmiddle" '.
	'onclick="addPartialBill()">');
//end gelie
$smarty->assign('sBillingClear','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) clearList(\'fb\',\'0000000000\')"/>');
$smarty->assign('sBillingList',"
<tr>
	<td colspan=\"10\">List is currently empty...</td>
</tr>");

$smarty->assign('sBillingDialysis','<button class="segButton" onclick="openDialysis(); return false"><img src="'.$root_path.'gui/img/common/default/requests.gif">Add Dialysis Billing</button>');

/* ************************************************************************************************ */



/* *************************************************************************************************
	 DEPOSIT
 ************************************************************************************************* */
$smarty->assign('sDepositAdd','<img class="segSimulatedLink" src="'.$root_path.'images/btn_add_deposit.gif" align="absmiddle" '.
	'onclick="openDeposit()">');
$smarty->assign('sHoiAdd','<img class="segSimulatedLink" src="'.$root_path.'images/btn_add_hoi.gif" align="absmiddle" '.
    'onclick="openHoi()">');
$smarty->assign('sPartialAdd','<img class="segSimulatedLink" src="'.$root_path.'images/btn_add_partial.gif" align="absmiddle" '.
	'onclick="addPartialPayment()">');
//added by jasper 05/29/2013
$smarty->assign('sHospitalServiceOB','<img class="segSimulatedLink" src="'.$root_path.'images/btn_add_depositOB.gif" align="absmiddle" '.
    'onclick="openOBAnnexCharge()" />');
//added by jasper 05/29/2013
$smarty->assign('sDepositClear','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) clearList(\'pp\',\'0000000000\')"/>');
$smarty->assign('sDepositList',"
<tr>
	<td colspan=\"10\">List is currently empty...</td>
</tr>");

/* ************************************************************************************************ */

/* *************************************************************************************************
	 DIALYSIS
 ************************************************************************************************* */

$smarty->assign('sDialysisList',"
<tr>
	<td colspan=\"10\">List is currently empty...</td>
</tr>");

$smarty->assign('sDialysis','<button class="segButton" onclick="openDialysis(); return false"><img src="'.$root_path.'gui/img/common/default/requests.gif">Add Dialysis Requests</button>');
$smarty->assign('sDialysisClear','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) clearList(\'db\',\'0000000000\')"/>');

/* ************************************************************************************************ */


/* *************************************************************************************************
	 OTHER HOSPITAL SERVICES
 ************************************************************************************************* */
 /*
	$smarty->assign('sHospitalServiceAdd','<img class="segSimulatedLink" src="'.$root_path.'images/his_additems_button.gif" align="absmiddle" '.
		'onclick="openServices()">');
	$smarty->assign('sHospitalServiceClear','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) clearList(\'other\',\'0000000000\')"/>');
*/

$smarty->assign('sHospitalServiceClear','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) clearList(\'other\',\'0000000000\')"/>');
$smarty->assign('sHospitalServiceConsultation','<input class="segButton" type="button" value="Consultation" onclick="openServices(33)" />');
$smarty->assign('sHospitalServiceOrtho','<input class="segButton" type="button" value="Orthopedics" onclick="openServices(51)" />');
$smarty->assign('sHospitalServiceENT','<input class="segButton" type="button" value="ENT-HNS" onclick="openServices(52)" />');
$smarty->assign('sHospitalServiceDental','<input class="segButton" type="button" value="Dental" onclick="openServices(50)" />');
$smarty->assign('sHospitalServicePTOT','<input class="segButton" type="button" value="PT/OT" onclick="openServices(49)" />');
$smarty->assign('sHospitalServicePedia','<input class="segButton" type="button" value="Pedia" onclick="openServices(53)" />');
$smarty->assign('sHospitalServiceSpecialLab','<input class="segButton" type="button" value="Special Lab" onclick="openServices(54)" style="display:none"/>');
$smarty->assign('sHospitalServiceAdd','<input class="segButton" type="button" value="Others" onclick="openServices()" />');
$smarty->assign('sHospitalServiceClear','<input class="segButton" type="button" value="Clear list" onclick="if (confirm(\'Clear the list?\')) clearList(\'other\',\'0000000000\')" />');
$smarty->assign('sOtherHospitalServices',"
<tr>
	<td colspan=\"10\">List is currently empty...</td>
</tr>");

/* ************************************************************************************************ */

/*
	$smarty->assign('sHospitalServiceDiscount','<span class="segLink" onclick="">view discount<img align="absmiddle" src="'.$root_path.'images/cashier_discount_small.gif"/></span>');
	$smarty->assign('sHospitalServiceRemoveAll','<span class="segLink" onclick="">remove all<img align="absmiddle" src="'.$root_path.'images/cashier_delete_small.gif"/></span>');
	$smarty->assign('sHospitalServiceRemove','<span class="segLink" onclick="">remove <img align="absmiddle" src="'.$root_path.'images/cashier_delete_small.gif"/></span>');
	$smarty->assign('sHospitalServiceToggle','<span class="segLink" onclick="">toggle<img align="absmiddle" src="'.$root_path.'images/cashier_checkbox.gif"/></span>');
	$smarty->assign('sOtherHospitalServices',"
				<tr>
					<td colspan=\"10\">Services list is currently empty...</td>
				</tr>");
*/

if ($_POST['submitted'] && !$saveok) {
}
else {
}

$smarty->assign('sORNo','<input maxlength="7" orOk="1" class="segInput" id="orno" name="orno" type="text" size="15" style="padding-left:4px;font:bold 14px Arial" value="'.$pay_info['or_no'].'" '.($_GET['or'] ? 'readonly="readonly"' : '').' onfocus="if (!this.readOnly) this.select()" onkeyup="if (event.keyCode==13 && !this.readOnly) this.blur();" onblur="if (!this.readOnly) { this.readOnly=true; if (!validateOR(this.value)) {warn(\'Invalid (non-numeric) OR Number!\',0)} else xajax_checkORNoExists(this.value, theORNo) }" readonly="readonly"/>');
$smarty->assign('sImgWarn','<img id="warnicon" src="'.$root_path.'images/cashier_warn.gif" border="0" align="absmiddle" onmouseover="return showWarning()" onmouseout="nd()" style="display:none"/>');
$smarty->assign('sImgOK','<img id="okicon" src="'.$root_path.'images/cashier_ok.gif" border="0" align="absmiddle" onmouseover="return showOk()" onmouseout="nd()" style="display:none"/>');
$smarty->assign('sResetOR','<input class="segButton" id="reset-orno" type="button" value="Get Latest" '.
	(!$_GET['or'] || TRUE ?
		'onclick="$(\'orno\').readOnly=true;xajax_getLatestORNumber()"' :
		'disabled="disabled"').
	'/>');
//added by jane 10/18/2013 
$smarty->assign('sPayorTabCompanyName','<input class="segInput" id="cashcompany" name="cashcompany" type="text" size="30" value="" />');

$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sOrderEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', "6"=>'INDUSTRIAL CLINIC (IC)', IPBMOPD_enc => 'IPBM - OPD', IPBMIPD_enc => 'IPBM - IPD'); //edited by Macoy, July 08, 2014

$seg_encounter = new Encounter();
$person = new Person();
if($_GET['pid']){
	$enc_nr = $person->CurrentEncounter($_GET['pid']);
	$encounter_details = $seg_encounter->getEncounterInfo($enc_nr);
	$encounter_type = $enc[$encounter_details['encounter_type']];
}else{
	$encounter_type = false;
}

if ($pay_info['encounter_type'])  $smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($encounter_type)
		$smarty->assign('sOrderEncTypeShow',$encounter_type);
	else  $smarty->assign('sOrderEncTypeShow', 'WALK-IN');
}

$smarty->assign('sDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$pay_info["discountid"].'"/>');
$smarty->assign('sDiscount','<input id="discount" name="discount" type="hidden" value="'.$pay_info["discount"].'"/>');

$social_service = new SocialService();
if($encounter_type == 'OUTPATIENT'){
    $social_service_details = $social_service->getLatestClassificationByPid($_GET['pid']);
}else{
    $social_service_details = $social_service->getLatestClassificationByPid($enc_nr,0);
}
if ($social_service_details['discountid']){
     $smarty->assign('sSWClass',$social_service_details['discountid']);
}else{
    $smarty->assign('sSWClass','None');
}

$dbtime_format = "Y-m-d H:i:s";
$fulltime_format = "F j, Y g:ia";

if ($pay_info['or_date']) {
	$curDate = date($dbtime_format, strtotime($pay_info['or_date']));
	$curDate_show = date($fulltime_format, strtotime($pay_info['or_date']));
}
else {
	$dbDate = $db->GetOne("SELECT NOW()");
	$curDate = date($dbtime_format, strtotime($dbDate));
	$curDate_show = date($fulltime_format, strtotime($dbDate));
#		$curDate = date($dbtime_format);
#		$curDate_show = date($fulltime_format);
}
$smarty->assign('sORDate','<span id="show_ordate" class="segInput" style="font-weight:bold; color:#000080; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['ordate'])) : $curDate_show).'</span><input class="segInput" name="ordate" id="ordate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['ordate'])) : $curDate).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="ordate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_ordate\",
		inputField : \"ordate\",
		ifFormat : \"%Y-%m-%d %H:%M:%S\",
		daFormat : \"	%B %e, %Y %I:%M%P\",
		showsTime : true,
		button : \"ordate_trigger\",
		singleClick : true,
		step : 1
	});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

#added by Jarel 09/25/2013
if(strpos($pay_info['or_name'],'/')!==FALSE){
	$or_names = explode('/',$pay_info['or_name']);
	$or_name = $or_names[0];
}else{
	$or_name = $pay_info['or_name'];
}

$smarty->assign('sORName','<input class="segInput search-text-input" id="orname" name="orname" type="text" value="'.$or_name.'"/>');
$smarty->assign('sORAddress','<textarea class="segInput" id="oraddress" name="oraddress" cols="27" rows="2" >'.$pay_info['or_address'].'</textarea>');
$smarty->assign('sOREncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$pay_info["encounter_nr"].'"/>');
$smarty->assign('sOREncID','<input id="pid" name="pid" type="hidden" value="'.$pay_info["pid"].'"/>');
if ($_GET['or'] && FALSE)
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" disabled="disabled"/>');
else
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" onclick="clearEncounter()" '.(($pay_info['pid'])?'':' disabled="disabled"').' />');
/*$smarty->assign('sORWalkin','<input class="segInput" id="search-walkin" type="checkbox" />'); commented by art 05/11/2014*/
/*added by art 05/11/2014*/
$smarty->assign('sORWalkin','
					<select name="search-walkin" id="search-walkin">
						<option value="0" default >- select -</option>
						<option value="1">Walk-in</option>
						<option value="2">Company</option>
					</select>');
/*end art*/
#$smarty->assign('sSWClass','<div style="margin-top:5px"><span style="font:bold 11px Tahoma">Classification: </span><span id="sw-class" style="font:bold 14px Arial;color:#006633">'.($pay_info['discountid'] ? $_POST['discountid'] : 'None').'</span></div>');

$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_discountid"=>"discountid",
	"var_discount"=>"discount",
	"var_name"=>"orname",
	"var_addr"=>"oraddress",
	"var_clear"=>"clear-enc"
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);

if ($_GET['or'] && FALSE) {
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" class="disabled">');
}
else {
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" class="link"
	'.($_GET['or'] ? 'disabled="disabled"' : '').'
	 onclick="personSelect()"/>');
}

# MODIFIED AMOUNT TENDERED
$smarty->assign('sAmtTendered',$pay_info['amount_tendered']/*number_format($pay_info['amount_tendered'],2)*/); //updated by Nick 05-28-2014 - SEI-119 : remove number_format for data consistency
$smarty->assign('sGUIvAmtTendered',number_format($pay_info['amount_tendered'],2));
$smarty->assign('sRemarks','<textarea class="segInput" name="remarks" cols="25" rows="2" style="float:left;">'.htmlentities($pay_info['remarks']).'</textarea>');

/* Check options */
$chk_disabled = $pay_info['check_or_no'] ? '' : 'disabled="disabled"';

#Added by Jarel 07/17/2013
$dbtime_format1 = "Y-m-d";
$fulltime_format1 = "F j, Y";

if ($pay_info['check_date']) {
	$curDate1 = date($dbtime_format1, strtotime($pay_info['check_date']));
	$curDate_show1 = date($fulltime_format1, strtotime($pay_info['check_date']));
}else {
	$dbDate1 = $db->GetOne("SELECT NOW()");
	$curDate1 = date($dbtime_format1, strtotime($dbDate1));
	$curDate_show1 = date($fulltime_format1, strtotime($dbDate1));
}
$smarty->assign('sCheckDate','<span id="show_checkdate" class="segInput" style="font-weight:bold; color:#000080; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format1,strtotime($_POST['check_date'])) : $curDate_show1).'</span><input class="segInput require" name="checkdate" id="checkdate" type="hidden" value="'.($submitted ? date($dbtime_format1,strtotime($_POST['check_date'])) : $curDate1).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon1','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="checkdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript1 = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_checkdate\",
		inputField : \"checkdate\",
		ifFormat : \"%Y-%m-%d\",
		daFormat : \"	%B %e, %Y\",
		showsTime : true,
		button : \"checkdate_trigger\",
		singleClick : true,
		step : 1
	});
</script>";

$smarty->assign('jsCalendarSetup1', $jsCalScript1);

$smarty->assign('sCheckOption','<input class="segInput" id="chkcheck" name="chkcheck" type="checkbox" onchange="enableInputChildren(\'check-details\',this.checked)" '.($pay_info['check_or_no'] ? 'checked="checked"' : '').'/><label class="segInput" for="chkcheck">Use check</label>');
$smarty->assign('sCheckNo','<input class="segInput require" id="checkno" name="checkno" type="text" size="15" value="'.$pay_info['check_no'].'" '.$chk_disabled.' />');
$smarty->assign('sCheckBankName','<input class="segInput require" id="checkbank" name="checkbank" type="text" size="30" value="'.$pay_info['check_bank_name'].'" '.$chk_disabled.' />');
//Added by Jarel 09/25/2013
$smarty->assign('sCompanyName','<input class="segInput require" id="checkcompany" name="checkcompany" type="text" size="30" value="'.$pay_info['company_name'].'" '.$chk_disabled.' />');
$smarty->assign('sCheckPayee','<input class="segInput require" id="checkpayee" name="checkpayee" type="text" size="30" value="'.$pay_info['check_name'].'" '.$chk_disabled.' />');
$smarty->assign('sCheckAmount','<input class="segInput require" id="checkamount" name="checkamount" type="text" size="15" value="'.$pay_info['check_amount'].'" '.$chk_disabled.' />');

/* Credit Card */
$crd_disabled = $pay_info['card_or_no'] ? '' : 'disabled="disabled"';
$smarty->assign('sCardOption','<input class="segInput" id="chkcard" name="chkcard" type="checkbox" onchange="enableInputChildren('."'card-details'".',this.checked)" '.($pay_info['check_or_no'] ? 'checked="checked"' : '').'/><label class="segInput" for="chkcard">Use Card</label>');
$smarty->assign('sCardNo','<input class="segInput" id="cardno" name="cardno" type="text" size="15" value="'.$pay_info['card_no'].'" style="" '.$crd_disabled.' />');
$smarty->assign('sCardIssuingBank','<input class="segInput" id="cardbank" name="cardbank" type="text" size="30" value="'.$pay_info['card_bank_name'].'" '.$crd_disabled.' />');
$smarty->assign('sCardBrand','<input class="segInput" id="cardbrand" name="cardbrand" type="text" size="30" value="'.$pay_info['card_brand'].'" '.$crd_disabled.' />');
$smarty->assign('sCardName','<input class="segInput" id="cardname" name="cardname" type="text" size="30" value="'.$pay_info['card_name'].'" '.$crd_disabled.' />');
$smarty->assign('sCardExpiryDate','<input class="segInput" id="cardexpr" name="cardexpr" type="text" size="10" value="'.$pay_info['card_expiry_date'].'" '.$crd_disabled.' />');
$smarty->assign('sCardSecurityCode','<input class="segInput" id="cardcode" name="cardcode" type="text" size="5" value="'.$pay_info['card_security_code'].'" '.$crd_disabled.' />');
$smarty->assign('sCardAmount','<input class="segInput" id="cardamount" name="cardamount" type="text" size="15" value="'.$pay_info['card_amount'].'" '.$crd_disabled.' />');

$active_tab = 'request';
if ($_GET['tab']) $active_tab = $_GET['tab'];
if ($tab_enabled) {
	if ($tab_enabled['other']) $active_tab = 'other';
	if ($tab_enabled['pp']) $active_tab = 'deposit';
	if ($tab_enabled['fb']) $active_tab = 'billing';
	#added by art 05/25/2014 for IC
	if ($tab_enabled['ic']){ 
		$active_tab = 'billing';
	} #end art
}
$smarty->assign('bTab'.ucfirst($active_tab),TRUE);

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&ref='.$sRefNo.'&dept='.$sDept.'&or='.$_GET['or'].'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
<?php
	foreach ($sRefNoArray as $i=>$ref) {
		$src = $sDeptArray[$i];
?>
	<input type="hidden" name="reference[]" value="<?= $src . "_" . $ref ?>">
<?php
	}


$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img id="cancel-btn" class="segSimulatedLink" src="'.$root_path.'images/his_cancel_button.gif" align="absmiddle" />');
$smarty->assign('sContinueButton','<img id="process-btn" class="segSimulatedLink" src="'.$root_path.'images/his_process_button.gif" align="absmiddle" onclick="if (confirm(\'Process this payment?\')) if (validate()) document.inputform.submit()" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_main.tpl');
$smarty->display('common/mainframe.tpl');

?>