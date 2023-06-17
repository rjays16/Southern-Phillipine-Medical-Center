<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    require_once("ajax/ajax_labresult.common.php");

    define('NO_2LEVEL_CHK',1);
    $local_user='ck_lab_user';
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/inc_front_chain_lang.php');

    require_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common');
    $smarty->assign('imgLoading',"<img src=\"" . $root_path . "/images/ajax_bar2.gif\">");
?>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript">
    var $J = jQuery.noConflict();
</script>
<script type="text/javascript" src="js/lab-parsedresult-view.js?t=<?=time()?>"></script>
<?php $xajax->printJavascript($root_path.'classes/xajax_0.5'); ?>
<script>
    var pid = "<?=$_GET['pid']?>";
    var lis_order_no = "<?=$_GET['lis_order_no']?>";
    
    xajax_Results(pid,lis_order_no);
</script>
<?php
    $smarty->display('laboratory/lab-parsedresult-view.tpl');
?>
