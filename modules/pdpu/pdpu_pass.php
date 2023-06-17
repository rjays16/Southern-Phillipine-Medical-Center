<?php
/**
 * @author Gervie 03/23/2016
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables = array('departments.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/areas_allow.php');

$append=URL_REDIRECT_APPEND.'&from=pass';

if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');

switch($target) {
    // Added by Gervie 11/02/2015
    case 'referral':
        $title = 'PDPU - Assessment and Referral Form';
        $breakfile = 'pdpu_main.php'.URL_APPEND;
        $fileforward = $root_path.'index.php?r=pdpu/referral';
        $lognote = 'PDPU ok';
        $allowedarea = array('_a_1_pdpuview');
        break;
}

$thisfile=basename(__FILE__);
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

$local_user='aufnahme_user';
$userck = 'aufnahme_user';

setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php');
setcookie(ck_2level_sid.$sid,'');

require($root_path.'include/inc_passcheck_internchk.php');

if ($pass=='check')
    include($root_path.'include/inc_passcheck.php');

$errbuf = $swSocialService;
$minimal = 1;

require_once($root_path.'include/inc_config_color.php');
require($root_path.'include/inc_passcheck_head.php');
?>

<BODY  onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
    <?php if (!$cfg['dhtml']){ echo ' link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>

<table cellspacing=0 class="titlebar" border=0>
    <tr valign=top class="titlebar">
        <td bgcolor="#e4e9f4" valign="bottom">
            &nbsp; &nbsp;

            <?php
            if($cfg['dhtml'])
            {
                switch($target){
                    case 'referral': $buf = 'PDPU'; break;
                    case 'progress': $target="`progress`"; $buf = 'PDPU'; break;
                    default: $target="`referral`"; $buf= 'PDPU';
                }
                echo '
          <script language=javascript>
            <!--
             if (window.screen.width)
 			{ if((window.screen.width)>1000) document.write(\'<img '.createComIcon($root_path,'people.gif','0','absmiddle').'><FONT  COLOR="'.$cfg['top_txtcolor'].'"  SIZE=6  FACE="verdana"> <b>'.$buf.'</b></font>\');}
 		    //-->
 		  </script>';
            }
            ?>
        </td>
    </tr>
</table>

<?php require($root_path.'include/inc_passcheck_mask.php') ?>

<p>
<?php
require($root_path.'include/inc_load_copyrite.php');
?>
</BODY>
</HTML>