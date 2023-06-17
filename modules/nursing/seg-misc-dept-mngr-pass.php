<?php
/**
 * Created by Nick 07-12-2014
 */
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');

define('LANG_FILE', 'stdpass.php');
define('NO_2LEVEL_CHK', 1);
$userck = "ck_pflege_user";

require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/areas_allow.php');

$allowedarea =& $allow_area['miscdeptmngr'];
$fileforward = $root_path . "modules/system_admin/seg-misc-dept-mngr.php?from=nursing";
$lognote = "$title ok";

if (isset($_GET['from']) && $_GET['from'] == 'nursing') {
    $title = "Nursing::Miscellaneous Department Manager";
    $breakfile = $root_path . "modules/nursing/nursing.php" . URL_APPEND;
} else {
    $title = "System Admin::Miscellaneous Department Manager";
    $breakfile = $root_path . "main/spediens.php" . URL_APPEND;
}

$userck = 'aufnahme_user';
setcookie($userck . $sid, '');
require($root_path . 'include/inc_2level_reset.php');
setcookie(ck_2level_sid . $sid, '');

require($root_path . 'include/inc_passcheck_internchk.php');

if ($pass == 'check')
    include($root_path . 'include/inc_passcheck.php');

require($root_path . 'include/inc_passcheck_head.php');

?>

<BODY
<?php
if (!$nofocus) echo 'onLoad="document.passwindow.userid.focus()"';
    echo ' bgcolor=' . $cfg['body_bgcolor'];
if (!$cfg['dhtml']) {
    echo ' link=' . $cfg['body_txtcolor'] . ' alink=' . $cfg['body_alink'] . ' vlink=' . $cfg['body_txtcolor'];
}
?>>

<p>

<P>
    <img src="../../gui/img/common/default/lampboard.gif" border=0 align="middle">
    <FONT COLOR="<?php echo $cfg[top_txtcolor] ?>" SIZE=5 FACE="verdana"> <b><?php echo "$title" ?></b></font>

<p>
<table width=100% border=0 cellpadding="0" cellspacing="0">

    <?php require($root_path . 'include/inc_passcheck_mask.php') ?>

    <p>
        <!-- img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDIntro2 $LDPharmacy $title " ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDWhat2Do $LDPharmacy $title " ?>?</a><br>
 -->

    <p>
        <?php
        require($root_path . 'include/inc_load_copyrite.php');
        ?>
</TABLE>