<?php
#------------------added 03-13-07-----------
/*
include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);
*/
#---------------------------------------------
#echo "dept_belong : ".$dept_belong['id'];
#echo "died = ".$is_died;

global $allow_updateData, $allow_add_charges, $allow_consult_admit, $allow_only_clinic, $allow_phs_user, $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update, $allow_ictransmanage;

include_once $root_path . 'include/inc_ipbm_permissions.php';

$allow_medocs_user = $acl->checkPermissionRaw(array('_a_1_medocsmedrecicd','_a_1_medocsmedrecmedical','_a_1_medocswrite','_a_0_all', 'System_Admin'));
#added by KENTOOT 05/23/2014
require_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj= new Personell;
$person_info = $personell_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);	
//Added by Marvin Cortes 05/19/2016
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
$tr_obj = new SegICTransaction();
$rs = $tr_obj->getHsscRecord($pid);
$counts = $rs['counts'];
//End MC
define("NURSE",2);
#end KENTOOT
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);
define('IPBMIPD_enc_STR', '13');
define('IPBMOPD_enc_STR', '14');
#echo "p = ".$ptype;
define('IcEncounterType', '6'); #added by art 02/22/2014
function Spacer()
{
/*?>
<TR bgColor=#dddddd height=1>
								<TD colSpan=3><IMG height=1
									src="../../gui/img/common/default/pixel.gif"
									width=5></TD></TR>
<?php
*/}
?>
<FONT face="Verdana,Helvetica,Arial" size=2 color="#cc0000">
<?php echo $LDOptsForPerson ?>  <a href="javascript:gethelp('preg_options.php')"><img <?php echo createComIcon($root_path,'frage.gif','0','absmiddle',TRUE) ?>></a>
</font>

	<TABLE border=0 cellPadding=0 cellSpacing=0 bgcolor="#F4F7FB">
<?php
	#if ($current_encounter)
	#if (($current_encounter)&&($allow_er_user))
	
	

	$isdischarged = 0;

	$row_ipd = $encounter_obj->getLatestEncounter($pid);
	$enctype = $row_ipd["encounter_type"];

	// $row_ipd = $encounter_obj->getLatestEncounter($pid);
	// $row = $encounter_obj->getPatientEncounter($current_encounter);
	// 	if (!$_GET['list']){
	// 	 if (($current_encounter)&&((($allow_er_user)&&($ptype=='er'))||(($allow_ipd_user)&&($ptype=='ipd'))||(($allow_ipbm_user)&&($ptype=='ipbm')))) {
	// 		$row = $encounter_obj->getPatientEncounter($current_encounter);
	// 		#echo "d = ".$current_encounter;
	// 					$enctype = $row["encounter_type"];
	// 					#echo "sql = ".$encounter_obj->sql;

	// 		#if ($row["is_discharged"])
	// 			if (($row["is_discharged"]) && (($row["encounter_type"]==1) || ($row["encounter_type"]==3)||($row["encounter_type"]==4)||($row["encounter_type"]==IPBMIPD_enc)))
	// 			$isdischarged = 1;
	// 			 }else{
	// 					 $enctype =  $row_ipd['encounter_type'];
	// 				if (($row_ipd["is_discharged"]) && (($row_ipd["encounter_type"]==1) || ($row_ipd["encounter_type"]==3)||($row_ipd["encounter_type"]==4)||($row_ipd["encounter_type"]==IPBMIPD_enc)))
	// 							$isdischarged = 1;
	// 			 }

	// }else{
	// 			$enctype =  $row_ipd['encounter_type'];
	// 	if (($row_ipd["is_discharged"]) && (($row_ipd["encounter_type"]==1)||($row_ipd["encounter_type"]==3)||($row_ipd["encounter_type"]==4)||($row_ipd["encounter_type"]==IPBMIPD_enc)))
	// 					$isdischarged = 1;

	// 	}
	// if($isIPBM){
	// 	$isdischarged = 0;
	// 	$row_ipd = $encounter_obj->getLatestEncounterIPBM($pid);
	// 	// var_dump($row_ipd);die();
	// 	if($row_ipd) $isdischarged = 0;
	// 	else $isdischarged = 1;
	// }
	if(((($allow_er_user)&&($ptype=='er'))||(($allow_ipd_user)&&($ptype=='ipd'))||(($allow_phs_user)&&($ptype=='phs'))||($isIPBM)||(($allow_opd_user)&&($ptype=='opd'))||($allow_ictransmanage)&&($ptype=='ic'))){
		$discharged_patient = ($encounter_obj->isPatientDischarged($pid));
		if($discharged_patient) $isdischarged = 1;
	}
	// var_dump($isdischarged);die();
	#echo "ds = ".$isdischarged;

	#echo "s = ".$encounter_obj->sql;
	#echo "ad = ".$enctype;
	#echo "allow clinic = ".$allow_only_clinic;
//if (!$allow_only_clinic){
	#if($current_encounter){
if ($allow_consult_admit||$isIPBM){
	#if (($current_encounter)&&($row["encounter_type"]!=2)){
	if (($current_encounter)&&(($row["encounter_type"]==3)||($row["encounter_type"]==4))||$isIPBM){
			#echo "here";
	#if(($current_encounter)||($dept_belong['id'] == "Medocs")){
	#if (($dept_belong['id'] == "ER")||($dept_belong['id'] == "OPD-Triage")){

?>
		<TR>
			<td width="32" align=center>&nbsp;</td>
			<td colspan="2" align=center background='<?php echo createComIcon($root_path,'opt_tl.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<TD vAlign=top >&nbsp;</TD>
		</TR>
<?php
		#if ($dept_belong['id'] == "OPD-Triage"){
		#if ((($allow_opd_user)&&($ptype=='opd'))||(($allow_ipd_user)&&($ptype=='ipd'))){
		#if (($allow_opd_user)&&($ptype=='opd')&&($isdischarged)){
		if (((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_opd_user)&&($ptype=='opd')&&(($enctype==2)||(empty($enctype)))))&&!$isIPBM){
?>
		<TR>
			<td rowspan="16" align=center bgcolor="#F4F7FB" background='<?php echo createComIcon($root_path,'opt_r.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<!-- commented 03-14-07 by vanessa ---->
					<!--<a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $current_encounter ?>&origin=patreg_reg"><?php echo $LDPatientData; ?></a>-->
					<!-- edit by vanessa--->
					<!--<a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $current_encounter ?>&origin=patreg_reg"><?php echo $LDStationary.' - '.$LDPatientData; ?>-->
					<!--<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><?php echo $LDAdmission.' - '.$LDAmbulant; ?></a>-->
					<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo $LDOPDConsultation; ?></a>
				</nobr>
				</FONT>
			</TD>
			<TD width="10" rowspan="16" vAlign=top background='<?php echo createComIcon($root_path,'opt_t.jpg','0','',FALSE) ?>'>&nbsp;</TD>
		</TR>

<?php
			$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&ptype='.$ptype;
		} #end of 'if ($dept_belong['id'] == "OPD-Triage")'
	elseif (((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_phs_user)&&($ptype=='phs')&&(($enctype==2)||(empty($enctype)))))&&!$isIPBM){
?>
		<TR>
			<td rowspan="16" align=center bgcolor="#F4F7FB" background='<?php echo createComIcon($root_path,'opt_r.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo "PHS Consultation"; ?></a>
				</nobr>
				</FONT>
			</TD>
			<TD width="10" rowspan="16" vAlign=top background='<?php echo createComIcon($root_path,'opt_t.jpg','0','',FALSE) ?>'>&nbsp;</TD>
		</TR>
<?php
			$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&ptype='.$ptype;
		} #end of 'if ($dept_belong['id'] == "OPD-Triage")'
	elseif(((($allow_ictransmanage)&&($ptype=='ic')&&($isdischarged))||(($allow_ictransmanage)&&($ptype=='ic')&&(($enctype==2)||(empty($enctype) || ($enctype == IcEncounterType)))))&&!$isIPBM){
		// echo "string";
# echo "e = >".$allow_ic_user;  echo "= b => ".$ptype."= ptype =>".$ptype."= isdischarged=>".$isdischarged." enctype=>".$enctype;
?>
		<TR>
			<td rowspan="16" align=center bgcolor="#F4F7FB" background='<?php echo createComIcon($root_path,'opt_r.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href=href="../../modules/industrial_clinic/seg-ic-transaction-form.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&userck=ck_ic_transaction_user&encounter_class_nr=6&ptype=<?=$ptype?>"><?php echo "Health Service and Specialty Clinic Transaction"; ?></a>
				</nobr>
				</FONT>
			</TD>
			<TD width="10" rowspan="16" vAlign=top background='<?php echo createComIcon($root_path,'opt_t.jpg','0','',FALSE) ?>'>&nbsp;</TD>
		</TR>
<?php
			$redirectAdConsult = '../../modules/industrial_clinic/seg-ic-transaction-form.php'.URL_APPEND.'&pid='.$pid.'&userck=ck_ic_transaction_user&encounter_class_nr=6&ptype='.$ptype;  //shortcut key PageUp

			} #end of 'Industrial Clinic'
	}elseif(!$death_date || ($death_date == DBF_NODATE)){

		#if (($dept_belong['id'] == "ER")||($dept_belong['id'] == "OPD-Triage")||($dept_belong['id'] == "Admission")){
		if (($allow_er_user)||($allow_opd_user)||($allow_ipd_user)||($allow_phs_user)){
?>
		<TR>
			<td width="32" align=center>&nbsp;</td>
			<td colspan="2" align=center background='<?php echo createComIcon($root_path,'opt_tl.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<TD vAlign=top >&nbsp;</TD>
		</TR>
		<TR>
			<?php

				if ((((($allow_ipd_user)&&($ptype=='ipd')&&(($row_ipd["encounter_type"]==2)||($row_ipd["encounter_type"]==IPBMOPD_enc)||(empty($row_ipd["encounter_type"]))|| ($row_ipd["is_discharged"]==1)))
						 ||(($allow_er_user)&&($ptype=='er')&&($isdischarged)) ||(($allow_opd_user)&&($ptype=='opd')&&((($enctype==2)||($enctype==5)||($enctype==6))||(empty($enctype))))
							|| (($allow_phs_user)&&($ptype=='phs')&&((($enctype==2)||($enctype==5)||($enctype==6))||(empty($enctype)))
							||(($allow_er_user)&&($ptype=='er')&&((($enctype==2)||($enctype==5)||($enctype==6))||(empty($enctype))))
					|| ((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))))
							|| ((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged)))
							
					))&&$isdischarged){                               
			?>
					<td align=center><img <?php echo createComIcon($root_path,'post_discussion.gif','0','',FALSE) ?>></td>
					
			<? } ?>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
				 <!--<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1"><?php echo $LDAdmission.' - '.$LDStationary; ?></a>-->
<?php
			// var_dump($isdischarged);die();
			#if ($dept_belong['id'] == "ER"){
			if (((($allow_er_user)&&($ptype=='er')&&($isdischarged))||(($allow_er_user)&&($ptype=='er')&&(($enctype==2)||(empty($enctype) || ($enctype == DIALYSIS_ENCOUNTER_TYPE) || ($enctype == IcEncounterType)))))&&$isdischarged){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>"><?php echo $LDERConsultation; ?></a>
<?php
				#$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&ptype='.$ptype;  //shortcut key PageUp
			#}elseif ($dept_belong['id'] == "OPD-Triage"){
#-------
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&ptype='.$ptype;  //shortcut key PageUp
			
#--------
			}elseif (((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_opd_user)&&($ptype=='opd')&&(($enctype==2)||(empty($enctype) || ($enctype == IcEncounterType)))))&&$isdischarged){
						 # echo "e = ".$enctype;

				#addded by KENTOOT 05/23/2014
				//if user is nurse (disable link)
				if ($person_info['job_type_nr']==NURSE){
					//$LDOPDConsultation;
					echo $LDOPDConsultation;
				}
					
				else {?>	
				  <a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>" ><?php echo $LDOPDConsultation; ?></a>
<?php
			}
			#end KENTOOT
?>

			<!--  uncomment temporary by KENTOOT May 22, 2014 -->
			<!--  <a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>" ><?php echo $LDOPDConsultation; ?></a> -->

<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&ptype='.$ptype;  //shortcut key PageUp
			#}elseif ($dept_belong['id'] == "Admission"){
			#}elseif (($allow_ipd_user)&&($ptype=='ipd')){
			}elseif (((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_phs_user)&&($ptype=='phs')&&(($enctype==2)||(empty($enctype) || ($enctype == IcEncounterType)))))&&$isdischarged){
						 # echo "e = ".$enctype;
						?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo "PHS Consultation"; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&ptype='.$ptype;  //shortcut key PageUp
		#	}elseif ((($allow_UpdatePatientD)&&($ptype=='ic')&&($isdischarged))||(($allow_UpdatePatientD)&&($ptype=='ic')&&(($enctype==2)||(empty($enctype) || ($enctype == IcEncounterType))))){
			#}elseif ((($allow_ic_user)&&($ptype=='ic')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_ic_user)&&($ptype=='ic')&&((($enctype==2)||($enctype==5)||($enctype==6))||(empty($enctype))))){
						#  echo "allow_ic_user = >".$allow_ic_user;  echo "=ptype =>".$ptype."= isdischarged=>".$isdischarged." enctype=>".$enctype;
						?>
				<!--<a href="../../modules/industrial_clinic/seg-ic-transaction-form.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&userck=ck_ic_transaction_user&encounter_class_nr=6&ptype=<?=$ptype?>"><?php echo "Industrial Clinic Transaction"; ?></a>-->
<?php
			#	$redirectAdConsult = '../../modules/industrial_clinic/seg-ic-transaction-form.php'.URL_APPEND.'&pid='.$pid.'&userck=ck_ic_transaction_user&encounter_class_nr=6&ptype='.$ptype;  //shortcut key PageUp
			#}elseif ($dept_belong['id'] == "Admission"){
			#}elseif (($allow_ipd_user)&&($ptype=='ipd')){
			}elseif ((($allow_ipd_user)&&($ptype=='ipd')&&(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"])) || ($row_ipd["is_discharged"]==1) || ($enctype == IcEncounterType) || ($enctype == IPBMOPD_enc)))&&$isdischarged){

?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1&ptype=<?=$ptype?>"><?php echo $LDDirectAdmission; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1&ptype='.$ptype;   //shortcut key shift+c 67
			
			}
			
?>

 				</nobr>
				</FONT>
			</TD>
		</TR>
<?php
	#Spacer();
?>
		<!-- uncomment temporary by mark on june 19, 2007  -->
		<!-- commented by VAN 02-23-08-->
		<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'discussions.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><?php echo $LDVisit.' - '.$LDAmbulant; ?></a>
				</FONT>
			</TD>
		</TR>
		-->
<?php
		#}# end of 'if (($dept_belong['id'] == "ER")||($dept_belong['id'] == "OPD-Triage")||($dept_belong['id'] == "Admission"))'
	}# end of 'elseif(!$death_date || ($death_date == DBF_NODATE))'



}
}
?>

<?php

if ((($allow_ictransmanage)&&($ptype=='ic')&&($isdischarged))||(($allow_ictransmanage)&&($ptype=='ic')&&(($enctype==2)||($enctype==IPBMOPD_enc)||(empty($enctype) || ($enctype == IcEncounterType))))){
			#}elseif ((($allow_ic_user)&&($ptype=='ic')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_ic_user)&&($ptype=='ic')&&((($enctype==2)||($enctype==5)||($enctype==6))||(empty($enctype))))){
						#  echo "allow_ic_user = >".$allow_ic_user;  echo "=ptype =>".$ptype."= isdischarged=>".$isdischarged." enctype=>".$enctype;
			if($counts < 1 ){	 //Edited By Marvin Cortes		
?>
			<TR>
				<td align=center><img <?php echo createComIcon($root_path,'post_discussion.gif','0','',FALSE) ?>></td>
					<TD vAlign=top >
						<FONT face="Verdana,Helvetica,Arial" size=2>

							<a href="../../modules/industrial_clinic/seg-ic-transaction-form.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&userck=ck_ic_transaction_user&encounter_class_nr=6&ptype=<?=$ptype?>"><?php echo "Health Service and Specialty Clinic Transaction"; ?></a>
						</FONT>
					</TD>
			</TR>
<?php
				$redirectAdConsult = '../../modules/industrial_clinic/seg-ic-transaction-form.php'.URL_APPEND.'&pid='.$pid.'&userck=ck_ic_transaction_user&encounter_class_nr=6&ptype='.$ptype;  //shortcut key PageUp
			} //End MC
}
if(($ipbmcanConsultOnly)&&($isIPBM)&&($isdischarged)){
?>

				<TR>
					<td rowspan="16" align=center bgcolor="#F4F7FB" background='<?php echo createComIcon($root_path,'opt_r.jpg','0','',FALSE) ?>'>&nbsp;</td>
					<td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
					<TD vAlign=top >
						<FONT face="Verdana,Helvetica,Arial" size=2>
						<nobr>
							<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&encounter_type=<?php echo IPBMOPD_enc; ?>&ptype=opd&from=ipbm"><?php echo "IPBM Consultation"; ?></a>
						</nobr>
						</FONT>
					</TD>
					<TD width="10" rowspan="16" vAlign=top background='<?php echo createComIcon($root_path,'opt_t.jpg','0','',FALSE) ?>'>&nbsp;</TD>
				</TR>
<?php
			
			$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&encounter_type='.IPBMIPD_enc.'&ptype=opd&from=ipbm';
		}
		if(($ipbmcanAdmitOnly)&&($isIPBM)&&($isdischarged)){
?> 
	<TR>
			<td align=center><img <?php echo createComIcon($root_path,'bnplus.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&encounter_type=<?php echo IPBMIPD_enc; ?>&ptype=ipd&from=ipbm"><?php echo "IPBM Admission" ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>

<?php
		$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&encounter_type='.IPBMOPD_enc.'&seg_direct_admission=1&ptype=ipd&from=ipbm';
		Spacer();
		#} #($admitted == 0)
	 }
?>
<!---->

<?php
	Spacer();
?>

		


<?php
	#if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){
	if (($allow_ipd_user)||($allow_medocs_user)){
?>
	<!-- commented by VAN 04-17-08-->
	 <!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'timeplan.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_appointment.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDAppointments ?></a>
				</FONT>
			</TD>
		</TR>
	-->
<?php
		#Spacer();
	} # end of 'if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs"))'
	

	 ?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'qkvw.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_encounter_list.php<?php echo URL_APPEND ?>&list=1&pid=<?php echo $pid ?>&target=<?php echo $target.$IPBMextend ?>&ptype=<?=$ptype?>"><?php echo $LDListEncounters ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>

<?php
		$redirectEncList = 'show_encounter_list.php'.URL_APPEND.'$pid='.$pid.'&target='.$target.'&ptype='.$ptype;    //shortcut key shift+l 76
	Spacer();
	 #echo "e = >".$allow_ic_user;  echo "= b => ".$ptype."= ptype =>".$ptype."= isdischarged=>".$isdischarged." enctype=>".$enctype;
?>


<?php
	#if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){
	if (($allow_ipd_user)||($allow_medocs_user)||($allow_UpdatePatientD)||$isIPBM){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'discussions.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_medocs.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>&ptype=<?=$ptype?>"><?php echo $LDMedocs ?></a>
				</FONT>
			</TD>
		</TR>
<?php
		$redirectHistory = 'show_medocs.php'.URL_APPEND.'&pid='.$pid.'&target='.$target.'&ptype='.$ptype;  //shortcut key shift+h 72
		Spacer();
		if((($allow_ipd_user)||($allow_medocs_user)||($allow_UpdatePatientD))&&!$isIPBM){
?>

		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'eye_s.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_drg.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>&ptype=<?=$ptype?>"><?php echo $LDDRG ?></a>
				</FONT>
			</TD>
		</TR>
		
<?php
		$redirectIcd10 = 'show_drg.php'.URL_APPEND.'&pid'.$pid.'&target='.$target.'&ptype='.$ptype;  //shortcut key shift+i 73
		Spacer();
		}
	}# end of 'if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs"))'

	#if ($dept_belong['id']=="Medocs"){
	if ((($allow_newborn_register)||($allow_medocs_user))&&!$isIPBM){
		#echo "is_died = ".$is_died;
		if ($fromtemp){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_birth_interface.php".URL_APPEND."&pid=$pid\" target=_blank>Birth Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>
		<!-- added by VAN 08-12-08-->
				<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_fetal_interface.php".URL_APPEND."&pid=$pid\" target=_blank>Fetal Death Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>

		<TR> <!-- START NEW FETAL -->
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_fetal_interface_new.php".URL_APPEND."&pid=$pid\" target=_blank>Fetal Death Certificate (Revised January 2007)</a>";
?>
				</FONT>
			</TD>
		</TR> <!-- END NEW FETAL-->

		<!-- -->
<?php Spacer(); }
		 if ($is_died){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface.php".URL_APPEND."&pid=$pid\" target=_blank>Death Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>
<?php
#		$redirectHistory = 'show_medocs.php'.URL_APPEND.'&pid='.$pid.'&target='.$target;  //shortcut key shift+h 72
		Spacer(); }
	}# end of 'if ($dept_belong['id']=="Medocs")'


	#if($dept_belong['id']=="Admission"){
	#if(($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){
	if(($allow_ipd_user)||($allow_medocs_user)){
?>
	<!--commented by VAN 04-17-08 -->
	<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'bubble.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_diagnostics_result.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDDiagXResults ?></a>
				</FONT>
			</TD>
		</TR>
	-->
		<!--
<?php
		#Spacer();
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'eye_s.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_diagnosis.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDDiagnoses ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
<?php
		Spacer();
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'discussions.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_procedure.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDProcedures ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
		-->
<?php
		Spacer();
?>
	<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'prescription.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_prescription.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDPrescriptions ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
		-->
		<!--
<?php
		#Spacer();
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_notes.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>&type_nr=21"><?php echo "$LDNotes - $LDPatientDev" ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
		-->
<?php
		Spacer();
?>
	<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_notes.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo "$LDNotes $LDAndSym $LDReports" ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
		-->
<?php
		#Spacer();
?>
	<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'people_search_online.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_immunization.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDImmunization ?></a>
				</FONT>
			</TD>
		</TR>
		-->
<?php
		#Spacer();
?>
	<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'people_search_online.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_weight_height.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDMeasurements ?></a>
				</FONT>
			</TD>
		</TR>
		-->
<?php
		/* If the sex is female, show the pregnancies option link */
		if($sex=='f' && !$isIPBM) {
?>
<?php
			Spacer();
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'man-whi.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_pregnancy.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDPregnancies ?></a>
				</FONT>
			</TD>
		</TR>
<?php
		} # end of 'if($sex=='f')'
?>
<?php
		Spacer();

		if ($fromtemp){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'new_address.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDBirthDetails ?></a>
				</FONT>
			</TD>
		</TR>
<?php

		Spacer();
		}
	}# end of 'if($dept_belong['id']=="Admission")'
?>
		<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'new_address.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="javascript:popRecordHistory('care_person',<?php echo $pid ?>)"><?php echo $LDRecordsHistory ?></a>
				</FONT>
			</TD>
		</TR>
		-->
<?php
	Spacer();
?>
		<!--
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<a href="<?php echo $root_path."modules/pdfmaker/registration/regdata.php".URL_APPEND."&pid=".$pid ?>" target=_blank><?php echo $LDPrintPDFDoc ?></a>
				</FONT>
			</TD>
		</TR>
		-->
		<!--
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
						<TD vAlign=top width=209>
								<FONT face="Verdana,Helvetica,Arial" size=2>
										<a href="javascript:void(0);" onclick="Dependents();" onmouseout="nd();">Dependents</a>
								</FONT>
						</TD>
				</TR>
		-->
		<?php if ($allow_newborn_register&&!$isIPBM){?>
		<TR>
						<td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
						<TD vAlign=top width=209>
								<FONT face="Verdana,Helvetica,Arial" size=2>
										<a href="javascript:void(0);" onclick="ChangeToBaby();">Change Status to Baby</a>
								</FONT>
						</TD>
				</TR>
		<?php } ?>
<?php
		Spacer();
?>
		<TR>
			<td>&nbsp;</td>
			<td colspan="2"background="<?php echo createComIcon($root_path,'opt_b.jpg','0','',FALSE) ?>">&nbsp;</td>
			<td >&nbsp;</td>
		</TR>
	</TABLE>

<?php include_once($root_path.'modules/registration_admission/include/yh_options.php') ?>
