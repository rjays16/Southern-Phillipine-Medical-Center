<?php
		#echo "id = ".$current_encounter;
		#echo "type = ".$enc_type;
		#if ($dept_belong['id']=='Admission'){
		if (($enc_type==3)||($enc_type==4)||($enc_type==IPBMIPD_enc)){
?>
		<a href="<?php echo 'aufnahme_daten_zeigen.php'.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.$IPBMextend; ?>"><img <?php echo createLDImgSrc($root_path,'admission_data.gif','0','top') ?>></a>
<?php }else{?>
		<a href="<?php echo 'aufnahme_daten_zeigen.php'.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.$IPBMextend; ?>"><img <?php echo createLDImgSrc($root_path,'consultation_data.gif','0','top') ?>></a>
<?php } ?>
<?php
#if(!$is_discharged){
if((!$is_discharged)&&($enc_type!=2)&&($enc_type!=IPBMOPD_enc)){
?>
<a href="javascript:makeBarcodeLabel('<?php echo $HTTP_SESSION_VARS['sess_en'];  ?>')"><img <?php echo createLDImgSrc($root_path,'barcode_label.gif','0','top') ?>></a>
<a href="javascript:makeWristBands('<?php echo $HTTP_SESSION_VARS['sess_en']; ?>')"><img <?php echo createLDImgSrc($root_path,'barcode_wristband.gif','0','top') ?>></a>
<?php
}
?>
