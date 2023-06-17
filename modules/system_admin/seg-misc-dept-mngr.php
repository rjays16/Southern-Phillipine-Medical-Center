<?php
/**
 * Created by Nick 07-01-2014
 */
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');

define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
$userck="ck_pflege_user";

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new Smarty_Care('common');

if(isset($_GET['from']) && $_GET['from'] == 'nursing'){
    $title = "Nursing::Miscellaneous Department Manager";
    $breakfile=$root_path."modules/nursing/nursing.php".URL_APPEND;
    $smarty->assign('sToolbarTitle',$title);
}else{
    $title = "System Admin::Miscellaneous Department Manager";
    $breakfile=$root_path."main/spediens.php".URL_APPEND;
    $smarty->assign('sToolbarTitle',$title);
}

/***********************************************
 * Data
 ***********************************************/

$javascripts = array(
    '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>',
    '<link rel="stylesheet" href="'.$root_path.'js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />',
    '<script type="text/javascript" src="'.$root_path.'js/jquery/jquery-1.8.2.js"></script>',
    '<script type="text/javascript" src="'.$root_path.'js/jquery/ui/jquery-ui-1.9.1.js"></script>',
    '<script type="text/javascript" src="'.$root_path.'js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>',
    '<script type="text/javascript" src="'.$root_path.'js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>',
    '<script type="text/javascript" src="'.$root_path.'modules/system_admin/js/seg-misc-dept-mngr.js"></script>',
    '<script type="text/javascript" src="'.$root_path.'js/listgen/listgen.js"></script>',
    '<link rel="stylesheet" href="'.$root_path.'js/listgen/css/default/default.css" type="text/css"/>'
);

/***********************************************
 * Assign
 ***********************************************/

$smarty->assign('javascripts',$javascripts);

/***********************************************
 * Finalize
 ***********************************************/
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('breakfile',$breakfile);

$smarty->assign('sMainBlockIncludeFile','system_admin/seg_misc_dept_mngr.tpl');

$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>