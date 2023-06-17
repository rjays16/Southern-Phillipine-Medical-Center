<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='stdpass.php';
define('LANG_FILE','specials.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/spediens.php'.URL_APPEND;
$thisfile=basename(__FILE__);

if($n==$n2)
{
	$screenall=1;
	$fileforward="my-passw-change-update.php?sid=$sid&lang=$lang&userid=$userid&n=$n";
	if ($pass=='check') 	
		include($root_path.'include/inc_passcheck.php');
}
else $n_error=1;

if(!isset($userid)) $userid=$HTTP_SESSION_VARS['sess_user_name'];

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$LDPWChange);

# href for return button
 $smarty->assign('pbBack',$breakfile);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('pw_change.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$LDPWChange);

 # Body Onload js
 if($mode!="pwchg") $smarty->assign('sOnLoadJs','onLoad="document.pwchanger.userid.focus()"');

# Collect javascript code

ob_start();
?>

<script language=javascript>
function pruf(d)
{
	return true;
	if((d.userid.value=="")||(d.keyword.value=="")||(d.n.value=="")||(d.n2.value=="")) return false;
	if(d.n.value!=d.n2.value){ alert("<?php echo $LDAlertPwError ?>"); return false;}
	return true;
}

</script>
 
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

#added by VAN 06-18-09 
if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
    $seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
    $seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
#--------

ob_start();

?>

<P>

<?php if($n_error) : ?><font class="warnprompt">
<img <?php echo createMascot($root_path,'images\warn.jpg','0','bottom') ?> align="absmiddle"><?php echo $LDNewPwDiffer ?>
</font>
<?php endif ?>
<ul>

<?php if($mode=='pwchg') : ?>
<font face="verdana,arial" size=3 color="#009900">
	<img <?php echo createMascot($root_path,'images\success.png','0','bottom') ?> align="absmiddle"><b><?php echo $LDPWChanged ?></b></font>
<?php else : ?>

<?php if (($pass=='check')&&$passtag) 
{
echo '<FONT  class="warnprompt">';

$errbuf=$title;

switch($passtag)
{
	case 1:$errbuf="$errbuf $LDWrongEntry";
			/*replace mascot3_r.gif by another icons by Ryan 5-25-2018*/
				print '<img '.createMascot($root_path,'images\warn.jpg','0').'> '." $LDWrongEntry <font size=2 color=\"#000000\">$LDPlsTryAgain</font>";
				//echo '<img '.createLDImgSrc($root_path,'cat-fe.gif','0','left').'>';
				break;
	case 2:$errbuf="$errbuf $LDNoAuth";
				print '<img '.createMascot($root_path,'images\warn.jpg','0').'>'."$LDNoAuth  <font size=2 color=\"#000000\">$LDPlsContactEDP</font>";
				//echo '<img '.createLDImgSrc($root_path,'cat-noacc.gif','0','left').'>';
				break;
	default:$errbuf="$errbuf $LDAuthLocked";
				print '<img '.createMascot($root_path,'images\warn.jpg','0').'>'."$LDAuthLocked  <font size=2 color=\"#000000\">$LDPlsContactEDP</font>";
				//echo '<img '.createLDImgSrc($root_path,'cat-sperr.gif','0','left').'>';
}


logentry($userid,$keyword,$errbuf,$thisfile,$fileforward);

echo '</FONT><p>';

}
?>

<br>
<form method=post action=<?php echo $thisfile; ?> onSubmit="return pruf(this)" name="pwchanger">
<table  border=0 cellpadding="0" cellspacing=1 bgcolor=#666666>
<tr>
<td >

<table border="0" cellpadding="20" cellspacing="0" bgcolor=#ffffdd>
<tr>
<td><font color=#800000>
<p>
<b><?php echo $LDUserIdPWPrompt ?></b><p></font>
<font color=#000080><?php echo $LDUserId ?>:

<!-- <input type="text" style="background-color:rgba(0,0,0,0) !important; border:none !important; font-weight: bold;" name="userid" size=25 maxlength=40 value="<?php echo $seg_user_name ?>"> -->
<label style="color:black !important; font-weight: bold; font-size: 17px;"><?php echo $seg_user_name; ?></label>
<input type="hidden" name="userid" id="userid" value="<?php echo $seg_user_name ?>">
<br>
<br>
<?php echo $LDPassword ?>:<br>
<input required type="password" name="keyword"  size=25 maxlength=40>
<input style="display: none;" type="password" name="password" size=25 maxlength=40>
<font color=#800000>
<b></b></font>
<p><?php echo $LDNewPwPrompt ?><br>
<input required type="password" name="n" size=25 maxlength=40 value=""><br>
<?php echo $LDNewPw2 ?>:<br>
<input required type="password" name="n2" size=25 maxlength=40 value="">
                            </td>
</tr>

<tr><td>
		<input type="hidden" name="sid" value="<?php echo $sid; ?>">
		<input type="hidden" name="lang" value="<?php echo $lang; ?>">
		<input type="hidden" name="mode" value="change">
 		<input type="hidden" name="pass" value="check">
 		<input type="submit" onclick="return confirm('Are you sure you want to change the password ?');" value="<?php echo $LDChangePw ?>"><p>
 		<!-- commented by Ryan 5-23-2018 -->
		<!-- <input type="reset" value="<?php echo $LDOops ?>"></td> -->
</tr>
</table>
</td>
</tr>
</table>
</form>
<?php endif ?>   

<p>

<a href="<?php echo $breakfile; ?>"><img <?php if($mode=='pwchg') echo createLDImgSrc($root_path,'close2.gif','0'); else echo createLDImgSrc($root_path,'cancel.gif','0'); ?>>
</a>

</ul>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
