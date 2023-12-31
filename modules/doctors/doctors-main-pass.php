<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
define('LANG_FILE', 'stdpass.php');
define('NO_2LEVEL_CHK', 1);
require_once($root_path . 'include/inc_front_chain_lang.php');

require_once($root_path . 'global_conf/areas_allow.php');

$allowedarea =& $allow_area['doctors'];
//$append="?sid=$sid&lang=$lang&from=pass"; 

#add rnel
$rep_luncher_area = 'doctor';
#end rnel

switch ($target) {
    case 'dutyplan':
        $fileforward = "doctors-dienstplan-planen.php" . URL_REDIRECT_APPEND . "&dept_nr=$dept_nr&retpath=$retpath&pmonth=$pmonth&pyear=$pyear";
        $title = $LDDOCScheduler;
        break;
    case 'setpersonal':
        $fileforward = "doctors-dienst-personalliste.php" . URL_REDIRECT_APPEND . "&ipath=$retpath&retpath=$retpath";
        $title = $LDDocsList;
        break;
    case 'report-launcher':
        require($root_path.'include/care_api_classes/class_personell.php');
        $personnel = new Personell;
        $personnelAssignment = $personnel->get_Dept_name($_SESSION['sess_login_personell_nr']);
        $fileforward = $root_path.'modules/reports/report_launcher.php'.URL_REDIRECT_APPEND.'&from='.$rep_luncher_area.'&dept_nr='.$personnelAssignment['location_nr'];
        break;
    # added by: syboy 01/12/2016 : meow
    // case 'setsearchdoctor':
    //     $title = "Search Active and Inactive employee";
    //     $allowedarea=array('_a_1_searchempdependent');
    //     $fileforward = $root_path.'modules/personell_admin/personell_search.php?from=medocs&department=Doctors';
    //     break;
    default: {
        header("Location:" . $root_path . "language/" . $lang . "/lang_" . $lang . "_invalid-access-warning.php");
        exit;
    }
}

$thisfile = basename(__FILE__);

switch ($retpath) {
    case 'op':
        $breakfile = $root_path . 'main/op-doku.php' . URL_APPEND;
    default:
        $breakfile = 'doctors.php' . URL_APPEND;
}

$lognote = "Doctors $title ok";

$userck = 'ck_doctors_dienstplan_user';

//reset cookie;
//reset all 2nd level lock cookies
setcookie($userck . $sid, '');
require($root_path . 'include/inc_2level_reset.php');
setcookie(ck_2level_sid . $sid, '');

require($root_path . 'include/inc_passcheck_internchk.php');
if ($pass == 'check')
    include($root_path . 'include/inc_passcheck.php');

$errbuf = "Doctors $title";

require($root_path . 'include/inc_passcheck_head.php');
?>

<BODY onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
    <?php if (!$cfg['dhtml']) {
        echo ' link=' . $cfg['idx_txtcolor'] . ' alink=' . $cfg['body_alink'] . ' vlink=' . $cfg['idx_txtcolor'];
    } ?>>
<FONT SIZE=-1 FACE="Arial">

    <!--replaced, 2007-10-05 FDP----------
<P>
<img <?php echo createComIcon($root_path, 'employee.gif', '0', 'top') ?>>
<FONT  COLOR="<?php echo $cfg['top_txtcolor'] ?>"  SIZE=6  FACE="verdana"> <b><?php echo $title ?></b></font>
-----with this--->
    <table cellspacing="0" class="titlebar" border=0>
        <tr valign=top class="titlebar">
            <td bgcolor="#e4e9f4" valign="bottom">
                &nbsp;&nbsp;
                <img <?php echo createComIcon($root_path, 'employee.gif', '0', 'absmiddle') ?>>
                <font color="<?php echo $cfg[top_txtcolor] ?>" size=6 face="verdana"> <b><?php echo $title ?></b></font>
            </td>
        </tr>
    </table>
    <!----until here only, 2007-10-05 FDP--->

    <table width=100% border=0 cellpadding="0" cellspacing="0">

        <?php require($root_path . 'include/inc_passcheck_mask.php') ?>

        <p>
            <!-- <img <?php echo createComIcon($root_path, 'varrow.gif', '0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDIntro2 $title" ?></a><br>
<img <?php echo createComIcon($root_path, 'varrow.gif', '0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDWhat2Do $title" ?></a><br>
 -->
            <?php
            require($root_path . 'include/inc_load_copyrite.php');
            ?>

</FONT>


</BODY>
</HTML>
