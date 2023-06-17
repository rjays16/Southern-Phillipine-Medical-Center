<?php
//created by janken 11/12/2014
// --additional UI for CF1

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing_new/ajax/reg-insurance.common.php');
require_once($root_path.'include/care_api_classes/class_person.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

// if($_GET['TranCode']){
// 	$transactionCode = strtolower($_GET['TranCode']);
// }

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='billing_insurance_addtnl_details.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$id = isset($_GET["hcare_id"]) ? $_GET["hcare_id"] : null;
$pid = isset($_GET["pid"]) ? $_GET["pid"] : null;

$person = new Person();

$info = $person->getMemberInsuranceInfo($pid, $id);

$email = @$info['email_address'];

$landline = @$info['landline_no'];
$mobile = @$info['mobile_no'];
$email = @$info['email_address'];
$specify = @$info['signatory_relation'];
$sName = @$info['signatory_name'];
$sDate = date('m-d-Y', strtotime(@$info['signatory_date']));
$relationType = @$info['relation_type'];
$sReason = @$info['reason'];

if(@$info['is_member']){
	$member = 'checked';
	$disabled = 'disabled';
}

if(@$info['is_incapacitated']){
	$incapacitated = 'checked';
	$disabled_reason = 'disabled';
}

if(@$info["sex"] == 'm')
	$selectedM = 'selected';
else if(@$info["sex"] == 'f')
	$selectedF = 'selected';


function getSignatoryRelation($selected, $disabled){
    global $db;

    $html = '<select onchange="checkType()" class="segInput" id="relation_type" '.$disabled.' name="relation_type" style="width:60%">';
    $strselected = ($selected === '') ? ' selected="selected"' : "";
    $html .= '<option value=""'.$strselected.'>--Select relation to member--</option>';

    $strSQL = "SELECT * FROM seg_relationtomember";
    $result = $db->Execute($strSQL);

    if ($result) {
        while ($row = $result->FetchRow()) {
            $strselected = ($selected === $row['relation_code']) ? ' selected="selected"' : "";
            $html .= '<option onkeydown="if(event.keyCode == 13)jumpNext(this,'.$select.')" value="'.$row['relation_code'].'"'.$strselected.'>'.$row['relation_desc'].'</option>';
        }
    }
    $html .= '</select><br/>';

    return $html;
}

ob_start();
?>

<!-- Core module and plugins:
-->

<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<link rel="stylesheet" href="<?=$root_path?>js/jquery/css/jquery-ui.css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery.simplemodal.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jsobj2phpobj.js"></script> 
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
//added by janken 11/10/2014
function disableSignatory(){
    if($('isMember').checked == '1'){
        $('relationName').disabled = true;
        $('relation_type').disabled = true;
        $('relation_type').value = '';
        $('relationSpecific').disabled = true;
        $('relationName').value = '';
        $('relationSpecific').value = '';
        $('isMember').value = '1';
    }
    else{
        $('relationName').disabled = false;
        $('relation_type').disabled = false;
        $('isIncapacitated').disabled = false; 
        $('isMember').value = '0'; 

        disableReason();
    }
    
}

function disableReason(){
    if($('isIncapacitated').checked == '1'){
        $('reason').disabled = true;
        $('reason').value = '';
        $('isIncapacitated').value = '1';
    }
    else{
        $('reason').disabled = false;
        $('isIncapacitated').value = '0'; 
    }
    
}

function checkType(){
    if($('relation_type').value == 'O')
        $('relationSpecific').disabled = false;
    else{
        $('relationSpecific').disabled = true;
        $('relationSpecific').value = '';
     }
}

function CheckFields(){
	var details = new Object();

	details.pid = $('pid').value;
	details.id = $('hcare_id').value;
	details.gender = $('gender').value;
	details.landline = $('landline').value;
	details.mobile = $('mobile').value;
	details.email = $('email').value;
	details.member = $('isMember').value;
	details.name = $('relationName').value;
	details.type = $('relation_type').value;
	details.relation = $('relationSpecific').value;
	details.date = $('sDate').value;
	details.incapacitated = $('isIncapacitated').value;
	details.reason = $('reason').value; 
	
	xajax_saveOtherMemberInfo(details);
}

jQuery(function($) {
    $( "#sDate" ).datepicker({
        dateFormat: "mm-dd-yy",
        changeMonth: true,
        changeYear: true
    });
});
</script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

// $sTemp = ob_get_contents();
//company discount insurance
$smarty->assign('sGender', "<select class='segInput' id='gender' name='gender' style='width:60%'> 
						<option value='0'>-Select Gender-</option>
	                    <option value='m' ".$selectedM.">Male</option>
	                    <option value='f' ".$selectedF.">Female</option>
					</select>");
$smarty->assign('sLandline', '<input type="text" style="width:60%" id="landline" class="segInput" value="'.$landline.'"/>');
$smarty->assign('sMobile', '<input type="text" style="width:60%" id="mobile" class="segInput" value="'.$mobile.'"/>');
$smarty->assign('sEmail', '<input type="text" style="width:60%" id="email" class="segInput" value="'.$email.'"/>');

$smarty->assign('sMember', '<input type="checkbox" id="isMember" name="isMember" class="segInput" onclick="disableSignatory();" value=""'.$member.'/>');
$smarty->assign('sName', '<input type="text" style="width:60%" id="relationName" name="relationName" '.$disabled.' class="segInput" value="'.$sName.'"/>');
$smarty->assign('sRelation', getSignatoryRelation($relationType, $disabled));
$smarty->assign('sSpecify', '<input type="text" style="width:60%" id="relationSpecific" name="relationSpecific" disabled="" class="segInput" value="'.$specify.'"/>');
$smarty->assign('sIncapacitated', '<input type="checkbox" id="isIncapacitated" name="isIncapacitated" '.$disabled.' class="segInput" onclick="disableReason();" value=""'.$incapacitated.'/>');
$smarty->assign('sReason', '<input type="text" style="width:60%" id="reason" name="reason" '.$disabled.''.$disabled_reason.' class="segInput" value="'.$sReason.'"/>');
$smarty->assign('sDate', '<input type="text" style="width:30%" id="sDate" class="segInput" value="'.$sDate.'"/>');

$smarty->assign('sbtnsave','<button class="segButton" onclick="CheckFields();">Save</button>');
$smarty->assign('sbtnCancel','<button class="segButton" onclick="window.parent.cClick();">Close</button>');



?>
<input type="hidden" name="hcare_id" id="hcare_id" value="<?echo $id?>">
<input type="hidden" name="pid" id="pid" value="<?echo $pid?>">


<?

//$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sMainBlockIncludeFile','billing_new/billing_insurance_addtnl_details.tpl');
$smarty->display('common/mainframe2.tpl');

?>