<?php
/**
 * Credit and Collection entry point
 * @author michelle 03-02-15
 */
define('LANG_FILE', 'lab.php');
define('NO_2LEVEL_CHK', 1);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_acl.php');

$breakfile = $root_path . 'main/startframe.php' . URL_APPEND;
$thisfile = basename(__FILE__);


require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Module title in the toolbar
$smarty->assign('sToolbarTitle', "Credit and Collection");
header('Content-type: text/html; charset=utf-8');

?>

    <!-- prototype -->
    <script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/fat/fat.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>/js/shortcut.js"></script>

    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>

    <!-- Core module and plugins: -->
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>

    <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
    <link rel="stylesheet" href="<?= $root_path ?>css/table.css" type="text/css"/>
    <!-- jquery -->
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery.validate.js"></script>
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
    <script type="text/javascript" src="<?= $root_path ?>modules/billing/js/billing-collection.js"></script>
    <style>
        .error {
            color: red;
        }
        #collectionGrid{
            width: 96%;
            box-shadow: none;
        }
        ul{
            padding-left:0;
            margin:0 !important;
            list-style:none;
        }
        ul li{
            padding: 5px;
            border: solid 1px #aaa;
        }
        td .remarks{
            padding:0;
        }
    </style>

<?php
//patient's basic info
$smarty->assign('sHRNInput', '<input id="hrnInput" type="text" value="" style="font:bold 12px Arial; float:left;" readonly />');
$smarty->assign('sSelectPatient', '<img id="selectPatient" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer" onclick="clearItems()"></img>');
$smarty->assign('sPatientInput', '<input id="pNameInput" type="text" value="" style="font:bold 12px Arial; float:left; width: 88%;" readonly />');
//$smarty->assign('sPatienAddress', '<input id="pAddressInput" type="text" value="" readonly />');
$smarty->assign('sPatienAddress', '<textarea class="segInput" id="pAddressInput" name="pAddressInput" cols="29" rows="2" style="font:bold 12px Arial" readOnly>'.'</textarea>');
$smarty->assign('sInsurance', '<input id="pInsuranceInput" type="text" style="color: #ff0000;font:bold 12px Arial; float:left;" value="" readonly />');
$smarty->assign('sEncounter', '<input id="pEncrNoInput" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right" readonly />');
$smarty->assign('sBillNo', '<input id="pBillNrInput" type="text" value="" style="font:bold 12px Arial; float:left; margin-left:10px; text-align:right;" readonly />');
$smarty->assign('sCaseDate', '<input id="pCaseDate" type="text" value="" style="font:bold 12px Arial; float:left;" readonly />');
$smarty->assign('sBillDate', '<input id="pBillDate" type="text" value="" style="font:bold 12px Arial; float:left;" readonly />');
$smarty->assign('sCaseDate', '<input id="pCaseDate" type="text" value="" style="font:bold 12px Arial; float:left;" readonly />');

//patient's bill info
$smarty->assign('sGrossAmount', '<input id="pGrossAmount" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sCoverage', '<input id="pCoverage" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sDiscount', '<input id="pDiscount" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sDeposit', '<input id="pDeposit" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sNetAmount', '<input id="pNetTotal" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sTotalGrants', '<input id="pTotalGrants" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');

//hidden
$smarty->assign('sBillDte', '<input id="pBillDateInput" type="hidden" value="" readonly />');
$smarty->assign('sBillFrmDte', '<input id="pBillFrmDateInput" type="hidden" value="" readonly />');
$smarty->assign('sIsFinal', '<input id="pIsfinal" type="hidden" value="" readonly />');

$smarty->assign('sMainBlockIncludeFile', 'billing/seg_credit_collection.tpl');
$smarty->display('common/mainframe.tpl');
?>