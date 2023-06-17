<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once $root_path . 'modules/cashier/ajax/cashier-main.common.php';
$xajax->printJavascript($root_path . 'classes/xajax_0.5');

/**
 * CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
 * GNU General Public License
 * Copyright 2002,2003,2004,2005 Elpidio Latorilla
 * elpidio@care2x.org
 *
 * See the file "copy_notice.txt" for the licence notice
 */

define('LANG_FILE', 'order.php');
define('NO_2LEVEL_CHK', 1);
$local_user = 'ck_prod_db_user';
require_once($root_path . 'include/inc_front_chain_lang.php');
# Create products object
$dbtable = 'care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG = array();
$new_date_ok = 0;
# Create global config object
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/inc_date_format_functions.php');

$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
if ($glob_obj->getConfig('date_format'))
    $date_format = $GLOBAL_CONFIG['date_format'];
$date_format = $GLOBAL_CONFIG['date_format'];
$phpfd = $date_format;
$phpfd = str_replace("dd", "%d", strtolower($phpfd));
$phpfd = str_replace("mm", "%m", strtolower($phpfd));
$phpfd = str_replace("yyyy", "%Y", strtolower($phpfd));
$phpfd = str_replace("yy", "%y", strtolower($phpfd));

$title = $LDPharmacy;
$breakfile = $root_path . "modules/cashier/seg-cashier-functions.php" . URL_APPEND;
$imgpath = $root_path . "pharma/img/";
$thisfile = basename(__FILE__);


# Start Smarty templating here
/**
 * LOAD Smarty
 */
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path . 'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS', '10');

$bills = $listgen->createList(
        array(
            'LIST_ID' => 'bills',
            'COLUMN_HEADERS' => array('Bill #', 'Date Request', 'Name', 'Amount', 'Status'),
            'AJAX_FETCHER' => 'populateBillsList',
            'EMPTY_MESSAGE' => "No Dialysis Requests",
            'ADD_METHOD' => "addBills",
            'FETCHER_PARAMS' => array('patient' => $_GET['patient']),
            'RELOAD_ONLOAD' => FALSE,
            'COLUMN_WIDTHS' => array("12%", "14%", "*", "12%", "12%", "8%")
        )
);
$smarty->assign('sToolbarTitle', "Cashier::Process dialysis billing");
$smarty->assign('pbHelp', "javascript:gethelp('products_db.php','search','$from','$cat')");
$smarty->assign('breakfile', $breakfile);
$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright', TRUE);
$smarty->assign('bHideTitleBar', TRUE);

$smarty->assign('lstRequest', $bills->getHTML());
$smarty->assign('sFormStart', '<form ENCTYPE="multipart/form-data" action="' . $thisfile . URL_APPEND . "&clear_ck_sid=" . $clear_ck_sid . '&target=edit&from=' . $_GET['from'] . '" method="POST" id="inputForm" name="inputForm" onSubmit="return false">');
$smarty->assign('sFormEnd', '</form>');
$smarty->assign('pid', $_GET['patient']);
$smarty->assign('sMainBlockIncludeFile', 'cashier/dialysis_bills.tpl');

?>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?= $root_path ?>modules/cashier/js/dialysis-bills.js"></script>
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