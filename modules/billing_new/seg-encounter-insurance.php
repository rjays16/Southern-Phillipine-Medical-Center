<?php
/**
 * Created by Nick 09-01-2014
 */
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');

define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
$userck="ck_pflege_user";

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_insurance.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
include_once($root_path . 'include/care_api_classes/class_department.php');

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new Smarty_Care('common');

$pid = $_GET['pid'];
$encounter_nr = $_GET['encounter_nr'];
$bill_type = $_GET['bill_type'];
$admission_date = $_GET['admission_date'];

#------------------------------------------------------------------------

$hidden_fields = array(
    "<input id='pid' type='hidden' value='$pid' />",
    "<input id='encounter_nr' type='hidden' value='$encounter_nr' />",
    "<input id='bill_type' type='hidden' value='$bill_type' />",
    "<input id='admission_date' type='hidden' value='$admission_date' />",
);

$tmpRoot = trim($root_path,"/");
$javascripts = array(
    "<script type='text/javascript' src='$tmpRoot/js/jsprototype/prototype.js'></script>",
    "<link rel='stylesheet' href='$tmpRoot/js/jquery/themes/seg-ui/jquery.ui.all.css' type='text/css' />",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/jquery-1.8.2.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/ui/jquery-ui-1.9.1.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/modules/billing_new/js/seg-encounter-insurance.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/listgen/listgen.js'></script>",
    "<link rel='stylesheet' href='$tmpRoot/js/listgen/css/default/default.css' type='text/css'/>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/iframecontentmws.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_draggable.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_filter.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_overtwo.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_scroll.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_shadow.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_modal.js'></script>"
);

$pinsure_obj = new PersonInsurance($pid);
$encounter_obj = new Encounter($encounter_nr);


$insurance_classes =& $pinsure_obj->getInsuranceClassInfoObject('class_nr, name, LD_var AS "LD_var"');
$classes = array();
foreach ($insurance_classes->GetRows() as $key => $row) {
    $classes[$row['class_nr']] =  $row['name'];
}

$person_insurance_rs =& $pinsure_obj->getPersonInsuranceObject($pid);
if($person_insurance_rs){
    if($person_insurance_rs->RecordCount())
        $person_insurance = $person_insurance_rs->FetchRow();
}

if ($p_insurance == false) {
    $insurance_show = true;
} else {
    if (!$p_insurance->RecordCount()) {
        $insurance_show = true;

    } elseif ($p_insurance->RecordCount() >= 1) {
        $buffer = $p_insurance->FetchRow();

        extract($buffer);

        if (!isset($insurance_class_nr)) $insurance_class_nr = $class_nr;
        $insurance_show = true;
        $insurance_firm_name = $pinsure_obj->getFirmName($insurance_firm_id);
    } else {
        $insurance_show = false;
    }
}
$options = $encounter_obj->getDeleteReasons();
foreach($options as $key => $option){
    $reasons .= "<option value='".$option['reason_id']."'>".$option['reason_description']."</option>";
}
#------------------------------------------------------------------------

$smarty->assign('javascripts',$javascripts);
$smarty->assign('insurance_classes',$classes);
$smarty->assign('person_insurance_class',($person_insurance['class_nr']) ? $person_insurance['class_nr'] : 3);
$smarty->assign('btnAddInsurance',$insurance_show);
$smarty->assign('hidden_fields',$hidden_fields);

#------------------------------------------------------------------------
$smarty->assign('delOptions', $reasons);
$smarty->assign('sMainBlockIncludeFile', 'billing_new/seg-encounter-insurance.tpl');
$smarty->display('common/mainframe2.tpl');


