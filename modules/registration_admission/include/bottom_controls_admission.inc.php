<?php
	#echo "dept_belong, encounter_type, fromnurse = ".$dept_belong[id]." , ".$encounter_type." , ".$fromnurse;
	#echo "fromnurse = ".$fromnurse;
	/*
	if ($fromnurse)
		$bol = 1;
	else
		$bol = 0;
	*/
	global $allow_referral, $allow_only_clinic, $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;

	
	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];
	elseif ($HTTP_SESSION_VARS['ptype'])
		$ptype = $HTTP_SESSION_VARS['ptype'];
	
	include_once $root_path . 'include/inc_ipbm_permissions.php';

		#edited by VAN 01-25-08
	#if (($dept_belong[id]=="Admission")||($dept_belong[id]=="OPD-Triage")||($dept_belong[id]=="ER")){
	#if ((($dept_belong[id]=="Admission")||($dept_belong[id]=="OPD-Triage")||($dept_belong[id]=="ER"))&&!($fromnurse)){
	if ((($allow_ipd_user)||($allow_opd_user)||($allow_er_user))&&!($fromnurse)&&!$isIPBM){
	#echo "ipd = ".$allow_ipd_user;
	#echo "<br>ptype = ".$ptype;
	#echo "<br>etype = ".$encounter_type;
		#if (($dept_belong[id]=="Admission")&&(($encounter_type==1)||($encounter_type==2))){
		if ((($allow_ipd_user)&&($ptype=='ipd'))&&(($encounter_type==1)||($encounter_type==2))){
		#echo "<br>true";
?>
			<!--edited by VAN 01-25-08 -->
			<!--<a href="<?php echo $updatefile.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&update=1&ptype='.$ptype.'&target='.$target; ?>"><img <?php echo createLDImgSrc($root_path,'admit.gif','0','top') ?>></a>-->
			<a href="<?php echo $updatefile.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&update=1&ptype='.$ptype.'&fromnurse='.$bol.'&target='.$target; ?>"><img <?php echo createLDImgSrc($root_path,'admit.gif','0','top') ?>></a>
<?php }else{
						if ((!$allow_only_clinic)||($allow_updateData))    {
?>
			<!--edited by VAN 01-25-08 -->
			<!--<a href="<?php echo $updatefile.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&update=1&ptype='.$ptype.'&target='.$target; ?>"><img <?php echo createLDImgSrc($root_path,'update_data.gif','0','top') ?>></a>-->
			<a href="<?php echo $updatefile.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&update=1&ptype='.$ptype.'&fromnurse='.$bol.'&target='.$target; ?>"><img <?php echo createLDImgSrc($root_path,'update_data.gif','0','top') ?>></a>
<?php
						}
				}
		}

	if (($encounter_type!=2)&&(!$allow_only_clinic)||$allAccess){
		if($isIPBM && (($ipbmcanUpdateAdmit&&$encounter_type==IPBMIPD_enc)||($ipbmcanUpdateConsult&&$encounter_type==IPBMOPD_enc))){
?>
		<a href="<?php echo $updatefile.URL_APPEND.'&encounter_nr='.$_SESSION['sess_en'].'&update=1&ptype='.$ptype.'&fromnurse='.$bol.'&target='.$target.$IPBMextend.'&typeFrom='.$_GET['typeFrom'].''; ?>"><img <?php echo createLDImgSrc($root_path,'update_data.gif','0','top') ?>></a>
<?			
		}
?>
		<a href="javascript:makeBarcodeLabel('<?php echo $HTTP_SESSION_VARS['sess_en'];  ?>')"><img <?php echo createLDImgSrc($root_path,'barcode_label.gif','0','top') ?>></a>
		<a href="javascript:makeWristBands('<?php echo $HTTP_SESSION_VARS['sess_en']; ?>')"><img <?php echo createLDImgSrc($root_path,'barcode_wristband.gif','0','top') ?>></a>
        <a href="javascript:makeBarcodeSticker('<?php echo $HTTP_SESSION_VARS['sess_en']; ?>')"><img <?php echo createLDImgSrc($root_path, 'barcode_sticker.gif','0','top') ?>/></a>
<?php }
		#if((($encounter_type==1 && $allow_er_user) || ($encounter_type==2 && $allow_opd_user))&&(!$allow_only_clinic))
		if((($encounter_type==1 && $allow_er_user) || ($encounter_type==2 && $allow_opd_user) || ($isIPBM&&$encounter_type==IPBMOPD_enc&&$ipbmcanUpdateConsult))&&($allow_referral||$isIPBM))
		{
				?><a><img id="select-enc" onmouseout="nd();" onclick="return overlib( OLiframeContent('../../modules/registration_admission/seg-admission-history.php?encounter_nr=<?= $HTTP_SESSION_VARS['sess_en']?>', 800, 400, 'fSelEnc', 1, 'auto'), WIDTH,800, TEXTPADDING,0, BORDER,0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, '<img src=../..//images/close.gif border=0 >', CAPTIONPADDING,4, CAPTION,'Patient Admission History', MIDX,0, MIDY,0, STATUS,'Patient Admission History');" style="cursor: pointer;" title="Show details"  name="select-enc" type="image" <?php echo createLDImgSrc($root_path,'consultation_history.gif','0','top') ?> /></a><?php
		}
		else if($allow_ipd_user && ($encounter_type==3 || $encounter_type==4 || ($isIPBM&&$encounter_type == IPBMIPD_enc&&$ipbmcanUpdateAdmit)))
		{
				?><a><img id="select-enc" onmouseout="nd();" onclick="return overlib( OLiframeContent('../../modules/registration_admission/seg-admission-history.php?encounter_nr=<?= $HTTP_SESSION_VARS['sess_en']?>', 800, 400, 'fSelEnc', 1, 'auto'), WIDTH,800, TEXTPADDING,0, BORDER,0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, '<img src=../..//images/close.gif border=0 >', CAPTIONPADDING,4, CAPTION,'Patient Admission History', MIDX,0, MIDY,0, STATUS,'Patient Admission History');" style="cursor: pointer;" title="Show details"  name="select-enc" type="image" <?php echo createLDImgSrc($root_path,'admission_history.gif','0','top') ?> /></a><?php
		}
		?>