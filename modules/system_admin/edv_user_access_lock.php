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
define('LANG_FILE','edp.php');
$local_user='ck_edv_user';

require_once($root_path.'include/inc_front_chain_lang.php');

//$breakfile='edv_user_access_list.php'.URL_APPEND;
$breakfile='edv-system-admi-welcome.php'.URL_APPEND;
$updatereturn='edv_user_access_list.php';
//$updatereturn='edv_user_access_list.php';
$returnfile=$HTTP_SESSION_VARS['sess_file_return'].URL_APPEND;
//$db->debug=true;
//$HTTP_SESSION_VARS['sess_file_return']='edv.php';

require_once($root_path.'include/care_api_classes/class_access.php');
$user = & new Access($itemname);

if($user->UserExists()){
	if ($finalcommand=='changelock') {
		if($user->isLocked()){
			$result = $user->UnLock();
		}else{
			$result = $user->Lock($delete_reason, $delete_other_reason);
		}
		if($result){
			header("Location: ".$updatereturn.URL_REDIRECT_APPEND."&itemname=$itemname&remark=lockchanged");
			exit;
		}else {
			echo "$LDDbNoSave<p>".$user->getLastQuery();
		}
	}
}

# Prepare title
$sTitle = "$LDEDP::$LDAccessRight";
if($zeile['lockflag']) $sTitle = "$sTitle::$LDUnlock";
	else $sTitle = "$sTitle::$LDLock";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

 # hide return button
 $smarty->assign('pbBack',$returnfile);

# href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('edp.php','access','lock','".$zeile['lockflag']."')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

 # Buffer page output

 ob_start();
?>

<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="js/gui-access-rights.js"></script>

<body onload="checkLock(<?=$user->isLocked()?>)">
<p><br>
<center>

<table width=50% border=1 cellpadding="20">
<tr>
<td bgcolor="#ffffdd">
<p>
<?php if ($user->isLocked())
echo $LDSureUnlock; else echo $LDSureLock; ?>?<p>

<table border="0" cellpadding="5" cellspacing="1">
<tr>
<td align=right><font color=#000080><?php echo $LDName ?>:</td>
<td><font color=#800000>
<?php
echo $user->Name();
?>
</td>
</tr>
<tr>
<td align=right><font color=#000080><?php echo $LDUserId ?>:</td>
<td><font color=#800000>
<?php
echo $user->LoginName();
?>
</td>
</tr>

<!-- Added by Arvin 04/17/2018 -->
<?php
	$options = $user -> getLockReason();
	foreach ($options as $key => $option) {
		$reasons .= "<option value='".$option['id']."' id='".$option['id']."'>".$option['description']."</option>";
	}
?>	
<tr id="text_reason">
<td align=right><font color=#000080><?php echo $LDReason ?>:</td>
<td><font color=#800000>
	<select id="select-reason" onchange="deleteReason()">
    <?php echo $reasons; ?>
</select>
<br>
<textarea name="other_reason" id="other_reason" style="display: none" onkeypress="getOtherReason()"></textarea>
</td>
</tr>

<!-- <tr>
<td align=right><font color=#000080><?php echo $LDPassword ?>:</td>
<td><font color=#800000>
<?php
echo $zeile['password'];
?>
</td>
</tr> -->
</table>

<br>
<FORM action="edv_user_access_lock.php" onsubmit="return validateForm()" method="post" >
<INPUT type="hidden" name="itemname" value="<?php echo $itemname ?>">
<input type="hidden" name="finalcommand" value="changelock">
<input type="hidden" name="sid" value="<?php echo $sid;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<!-- Added by Arvin 04/17/2018 -->
<input type="hidden" name="delete_reason" id="delete_reason">
<input type="hidden" name="delete_other_reason" id="delete_other_reason">
<INPUT type="submit" name="versand"  value="  <?php echo $LDYesSure ?>  "></font></FORM>

<FORM  method=get action="edv_user_access_list.php" >
<input type="hidden" name="sid" value="<?php echo $sid;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<INPUT type="submit"  value="<?php echo $LDNoBack ?>"></font></FORM>

</center>

</td>
</tr>
</table>        

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
