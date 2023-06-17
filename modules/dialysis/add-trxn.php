<?php 
/*created by art 09/10/2014*/
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . "include/care_api_classes/dialysis/class_dialysis.php");
require_once($root_path . "include/care_api_classes/dialysis/DialysisTransaction.php");
require_once($root_path . 'modules/dialysis/ajax/dialysis-transaction.common.php');
$obj_dialysis = new SegDialysis();
define('NO_2LEVEL_CHK', 1);
$local_user = 'ck_dialysis_user';

require_once($root_path . 'include/inc_front_chain_lang.php');
require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
require_once($root_path . 'include/inc_date_format_functions.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/inc_date_format_functions.php');
global $db;
#$db->debug=true;
# Collect javascript code
ob_start();
# Load the javascript code
?>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type='text/javascript' src="<?= $root_path ?>modules/dialysis/js/add-trxn.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
</script>
<script language="javascript">
	$j(document).ready(function(){
        preset();
    });
</script>

<?php
$smarty = new Smarty_Care('common');
$xajax->printJavascript($root_path . 'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript', $sTemp);


$enc_nr = $_GET['encounter_nr'];
#Title in the title bar -----------------------------------------------
$title = 'New transaction';
$smarty->assign('sToolbarTitle', $title);
$smarty->assign('bHideTitleBar', true);

#Window bar title -----------------------------------------------------
$smarty->assign('sWindowTitle', $title);
$bootstrap = '<link href="'.$root_path.'css/bootstrap/bootstrap.css" rel="stylesheet">';
$smarty->assign('bootstrap', $bootstrap);

#saving -------------------------------------------------------------
if (!empty($_POST)){
    #print_r($_POST);
    $count = count($_POST['ref_no']);
    $i=0;
    while ($i < $count) {
        $data = array('ref_no'      => $_POST['ref_no'][$i],
                      'bill_nr'     => $_POST['bill_nr'],
                      'amount'      => $_POST['amount'][$i],
                      'pay_type'    => $_POST['pay_type'][$i],
                      'control_nr'  => $_POST['control_nr'][$i],
                      'description' => $_POST['description'][$i],
                      'delete'      => $_POST['delete'][$i],
                    );
        $obj_dialysis->savePay($data);
        $i++;
    }
}
#-------------------------------------------------------------
$smarty->assign('enc_nr', '<input type="hidden" name="enc_nr" id="enc_nr" value="'.$enc_nr.'">');
$smarty->assign('curr_nr', '<input type="hidden" name="curr_nr" id="curr_nr" value="'.$_POST['bill_nr'].'">');
$smarty->assign('sMainBlockIncludeFile', 'dialysis/add-trxn.tpl');
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

 ?>