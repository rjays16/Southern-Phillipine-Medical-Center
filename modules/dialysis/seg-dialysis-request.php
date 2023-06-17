<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once $root_path . 'modules/dialysis/ajax/dialysis-lingap.common.php';
require_once $root_path . 'include/care_api_classes/dialysis/class_dialysis.php';
$xajax->printJavascript($root_path . 'classes/xajax_0.5');

define('LANG_FILE', 'order.php');
define('NO_2LEVEL_CHK', 1);

if(isset($_POST['bnr'])) {
    $billNrs = $_POST['bnr'];
    $boxs = $_POST['box'];
    $dialysis = new SegDialysis();
    foreach($boxs as $k => $box) {
       if(!isChecked($box)) {
           unset($billNrs[$k]);
       }
    }
    $dialysis->updateBillsDiscountId($billNrs, 'lingap');
}

function isChecked($bill) {
    return $bill == 'on' ? true : false;
}

require_once($root_path . 'include/inc_front_chain_lang.php');
# Create products object

$GLOBAL_CONFIG = array();

# Create global config object
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/inc_date_format_functions.php');

$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
if ($glob_obj->getConfig('date_format'))
    $date_format = $GLOBAL_CONFIG['date_format'];
$date_format = $GLOBAL_CONFIG['date_format'];

$breakfile = $root_path . "modules/cashier/seg-cashier-functions.php" . URL_APPEND;

$thisfile = basename(__FILE__);

require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path . 'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS', '10');
$bills = $listgen->createList(
        array(
            'LIST_ID' => 'lingapBills',
            'COLUMN_HEADERS' => array('', 'Status', 'Code', 'Service Description', 'Original Price', 'Net Price'),
            'AJAX_FETCHER' => 'populateBillsList',
            'EMPTY_MESSAGE' => "No Dialysis Requests",
            'ADD_METHOD' => "addBills",
            'FETCHER_PARAMS' => array('patient' => $_GET['patient'], 'bill_nr' => $_GET['refno']),
            'RELOAD_ONLOAD' => FALSE,
            'COLUMN_WIDTHS' => array("7%", "14%", "12%", "*", "12%", "8%")
        )
);
$smarty->assign('sToolbarTitle', "Cashier::Process dialysis billing");
$smarty->assign('breakfile', $breakfile);
$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright', TRUE);
$smarty->assign('bHideTitleBar', TRUE);
$smarty->assign('formAction', $thisfile . URL_APPEND . '&discountid=' . $_GET['discountid'] . '&refno=' . $_GET['refno'] . '&patient=' . $_GET['patient']);
$smarty->assign('lstRequest', $bills->getHTML());
$smarty->assign('classification', $_GET['discountid']);
$smarty->assign('disableApply', $_GET['discountid'] == 'LINGAP' ? '' : 'disabled');
$smarty->assign('pid', $_GET['patient']);
$smarty->assign('sMainBlockIncludeFile', 'dialysis/dialysis_lingap.tpl');
?>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/dialysis/js/dialysis-lingap.js"></script>
<?php
# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if (!isset($smarty)) {
    /**
     * LOAD Smarty
     * param 2 = FALSE = dont initialize
     * param 3 = FALSE = show no copyright
     * param 4 = FALSE = load no javascript code
     */
    include_once($root_path . 'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common', FALSE, FALSE, FALSE);

    # Set a flag to display this page as standalone
    $bShowThisForm = TRUE;
}
$smarty->display('common/mainframe.tpl');
?>