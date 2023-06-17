<?php
/**
* GUI Options for patient with temporary pid
* burn added : July 25, 2007
*/
//echo "pid = ".$pid;
//echo "enc = ".$encounter_type;

global $allow_updateData, $allow_add_charges, $allow_consult_admit, $allow_only_clinic, $allow_phs_user, $allow_ipddiscancel, $allow_receive, $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;

	include_once $root_path . 'include/inc_ipbm_permissions.php';

	# Modified by JEFF @ 11-23-17
	$canAccessAttestation = $acl->checkPermissionRaw(array('_a_1_medocsmedrecattess'));
	$canAccessAffidavitOrAll = $canAccessAttestation || $allAccess;

?>
<FONT face="Verdana,Helvetica,Arial" size=2 color="#cc0000">
<?php
		#edited by VAN 03-26-08
		#if (($fromtemp) || (($fromtemp)&&($dept_belong['id']=="Medocs")) || (($isDied)&&($dept_belong['id']=="Medocs")) || (($discharged)&&($dept_belong['id']=="Medocs"))){
		#if (($fromtemp) || (($fromtemp)&&($dept_belong['id']=="Medocs")) || (($isDied)&&($dept_belong['id']=="Medocs")) || ($discharged) || !($discharged)){
#if (!$allow_only_clinic){
		if (($fromtemp) || (($fromtemp)&&($allow_medocs_user || $allow_ipbmMedocs_user)) || ((($patient_result['result_code']==4)||($patient_result['result_code']==8)||($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user || $allow_ipbmMedocs_user)) || (($discharged)&&($allow_medocs_user || $allow_ipbmMedocs_user))
				|| ((!($discharged) && (($encounter_type_a==3)||($encounter_type_a==4)||($encounter_type_a==IPBMIPD_enc))) && !($isDied) && ($allow_medocs_user || $allow_ipbmMedocs_user)) || $allow_ipbmMedocs_user){

?>
<?php echo $LDOptsForPerson ?>  <a href="javascript:gethelp('preg_options.php')"><img <?php echo createComIcon($root_path,'frage.gif','0','absmiddle',TRUE) ?>></a>
<?php } ?>
</font>

	<TABLE border=0 cellPadding=0 cellSpacing=0 bgcolor="#F4F7FB">
		<!--added by VAN 02-23-08 -->
		<?php


								$isdischarged = 0;
								// $row_ipd = $encounter_obj->getLatestEncounter($pid);
								// if (!$_GET['list']){
								// 		 if (($current_encounter)&&((($allow_er_user)&&($ptype=='er'))||(($allow_ipd_user)&&($ptype=='ipd')))) {
								// 				$row = $encounter_obj->getPatientEncounter($current_encounter);
								// 				#echo "d = ".$current_encounter;
								// 				$enctype = $row["encounter_type"];
								// 				#echo "sql = ".$encounter_obj->sql;

								// 				#if ($row["is_discharged"])
								// 				if (($row["is_discharged"]) && (($row["encounter_type"]==1)||($row["encounter_type"]==3)||($row["encounter_type"]==4)))
								// 						$isdischarged = 1;
								// 		 }else{
								// 				 $enctype =  $row_ipd['encounter_type'];
								// 				 if (($row_ipd["is_discharged"]) && (($row_ipd["encounter_type"]==1)||($row_ipd["encounter_type"]==3)||($row_ipd["encounter_type"]==4)))
								// 						$isdischarged = 1;
								// 		 }

								// }else{
								// 		$enctype =  $row_ipd['encounter_type'];
								// 		if (($row_ipd["is_discharged"]) && (($row_ipd["encounter_type"]==1)||($row_ipd["encounter_type"]==3)||($row_ipd["encounter_type"]==4)))
								// 				$isdischarged = 1;
								// }
								// if($isIPBM){
								// 	$isdischarged = 0;
								// 	$row_ipd = $encounter_obj->getLatestEncounterIPBM($pid);
								// 	// var_dump($row_ipd);die();
								// 	if($row_ipd) $isdischarged = 0;
								// 	else $isdischarged = 1;
								// }
								// $isIPBM=($encounter_type_a==IPBMOPD_enc)||($encounter_type_a==IPBMIPD_enc);
								if(((($allow_er_user)&&($ptype=='er'))||(($allow_ipd_user)&&($ptype=='ipd'))||(($allow_phs_user)&&($ptype=='phs'))||($isIPBM)||(($allow_opd_user)&&($ptype=='opd')))){
									$discharged_patient = ($encounter_obj->isPatientDischarged($pid));
									if($discharged_patient) $isdischarged = 1;
								}
								#$row_ipd = $encounter_obj->getLatestEncounter($pid);

								if (empty($source)){
										if (($allow_consult_admit)&&(($allow_er_user)||($allow_opd_user)||($allow_ipd_user)||($allow_phs_user))){
													#if ((($allow_ipd_user)&&($ptype=='ipd')&&(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"]))))
													if ((($allow_ipd_user)&&($ptype=='ipd'))
																||(($allow_er_user)&&($ptype=='er')) || (($allow_opd_user)&&($ptype=='opd')) || (($allow_phs_user)&&($ptype=='phs'))){
		?>
				<TR>
								<?php
								#((($allow_ipd_user)&&($ptype=='ipd')&&($isdischarged))||(($enctype==2)||(empty($enctype))))
								if ((((($allow_ipd_user)&&($ptype=='ipd')&&($isdischarged))||(($enctype==2)||(empty($enctype))|| ($enctype==12)))
										|| ((($allow_er_user)&&($ptype=='er')&&($isdischarged))||(($allow_er_user)&&($ptype=='er')&&(($enctype==2)||(empty($enctype)))))
										|| ((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_opd_user)&&($ptype=='opd')&&(($enctype==2)||(empty($enctype)))))
					|| ((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_phs_user)&&($ptype=='phs')&&(($enctype==2)||(empty($enctype))))))&&$isdischarged&&!$isIPBM) {
						?>
			<td align=center><img <?php echo createComIcon($root_path,'post_discussion.gif','0','',FALSE) ?>></td>
			<? } ?>
						<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
				 <!--<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>"><?php echo $LDAdmission.' - '.$LDStationary; ?></a>-->
<?php
														}
			#if (($allow_er_user)&&($ptype=='er')&&($isdischarged)){
						if (((($allow_er_user)&&($ptype=='er')&&($isdischarged))||(($allow_er_user)&&($ptype=='er')&&(($enctype==2)||(empty($enctype)) ||($enctype==12))))&&!$isIPBM&&$isdischarged&&!$isIPBM){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>"><?php echo $LDERConsultation; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>';  //shortcut key PageUp
			#}elseif (($allow_opd_user)&&($ptype=='opd')){
						#}elseif (($allow_opd_user)&&($ptype=='opd')&&($isdischarged)){
						}elseif (((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_opd_user)&&($ptype=='opd')&&(($enctype==2)||(empty($enctype))||($enctype==12))))&&!$isIPBM&&$isdischarged&&!$isIPBM){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo $LDOPDConsultation; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2';  //shortcut key PageUp
			#}elseif (($allow_ipd_user)&&(($ptype=='ipd')||($ptype=='newborn'))&&($isdischarged)){
						#||(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"])))
			}elseif (((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_phs_user)&&($ptype=='phs')&&(($enctype==2)||(empty($enctype))||($enctype==12))))&&!$isIPBM&&$isdischarged&&!$isIPBM){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo "PHS Consultation"; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2';  //shortcut key PageUp
			#}elseif (($allow_ipd_user)&&(($ptype=='ipd')||($ptype=='newborn'))&&($isdischarged)){
						#||(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"])))
						}elseif (((($allow_ipd_user)&&($ptype=='ipd')&&($isdischarged))||(($enctype==2)||(empty($enctype))||($enctype==12)))&&!$isIPBM&&$isdischarged&&!$isIPBM){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1&ptype=<?=$ptype?>"><?php echo $LDDirectAdmission; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1';   //shortcut key shift+c 67
			}
			if(($ipbmcanConsultOnly)&&($isIPBM)&&($isdischarged)){
?>

				<TR>
					<td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
					<TD vAlign=top >
						<FONT face="Verdana,Helvetica,Arial" size=2>
						<nobr>
							<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&encounter_type=<?php echo IPBMOPD_enc; ?>&ptype=opd&from=ipbm"><?php echo "IPBM Consultation"; ?></a>
						</nobr>
						</FONT>
					</TD>
				</TR>
<?php
			
			$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2&encounter_type='.IPBMIPD_enc.'&ptype=opd&from=ipbm';
		}
		if(($ipbmcanAdmitOnly)&&($isIPBM)&&($isdischarged)){
?> 
	<TR>
			<td align=center><img <?php echo createComIcon($root_path,'bnplus.gif','0','',FALSE) ?>>&nbsp;</td>
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
		#} #($admitted == 0)
	 }
?>
				</nobr>
				</FONT>
			</TD>
		</TR>
		<?php } ?>
				<TR>
			<td align=center><img <?php echo createComIcon($root_path,'qkvw.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_encounter_list.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>&ptype=<?=$ptype?>"><?php echo $LDListEncounters ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
		<?php } ?>
		<!----------------------------------->
		<!--added by VAN 02-15-08-->
		<?php if ($fromtemp){ ?>
		<TR>

			<td align=center><img <?php echo createComIcon($root_path,'new_address.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<!--<a href="show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDBirthDetails ?></a>-->
					<!--<a href="../../modules/registration_admission/show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo ($source=='medocs')?'search':$target ?>"><?php echo $LDBirthDetails ?></a>-->
					<a href="../../modules/registration_admission/show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&ptype=<?=$ptype?>&target=<?php echo ($allow_medocs_user || ($allow_ipbmMedocs_user && $isIPBM))?'new':'search' ?>"><?php echo $LDBirthDetails ?></a>
				</FONT>
			</TD>
		</TR>
		<?php if ($allow_medocs_user|| ($allow_ipbmMedocs_user && $isIPBM)){ ?>
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
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_birth_interface_new.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Birth Certificate(NEW)</a>";
?>
</FONT>
			</TD>
		</TR>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href='#' onclick='printVaccinationCert(".$pid.")'>Vaccination Certificate</a>";
?>

                </FONT>
            </TD>
        </TR>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
        
        echo "<a id=\"errBirth\" href=\"javascript:void(0);\" onclick=\"viewBirthError();\">Erroneous Entry on Birth Cert.</a>";
?>
				</FONT>
			</TD>
		</TR>
		<!-- added by rnel -->
		<tr>
			<td
				align="center"
			>
				<img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>>
			</td>
			<td
				vAlign="top"
			>
				<font 
					face="Verdana,Helvetica,Arial" 
					size=2
				>
				<?php
				if($canAccessAffidavitOrAll){
echo "<a href=\"".$root_path."modules/registration_admission/certificates/affidavit_use_father_surename.php".URL_APPEND."&pid=$pid\" target=_blank>Affidavit to use the Surname of the Father/Sworn Attestation</a>";
}else{
	echo "Affidavit to use the Surname of the Father/Sworn Attestation";
}
?>
				</font>
			</td>
		</tr>
		<!-- end rnel -->

<!--- added by pet --- trial --- for viewing only --->
<?php
	#added by VAN 07-02-08
	#echo "temp = ".$fromtemp;
	#edited by VAN 08-01-08
	#if ($isDied){
	#if ((($death_date!='0000-00-00')||($isDied))&&($dept_belong['id']=="Medocs")&&($fromtemp)) {
	#edited by VAN 08-01-08
	if (((($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user)&&($fromtemp))||(($allow_medocs_user)&&($fromtemp))) {
		if($isIPBM){
			if($ipbmcanAccessDeathCertificate){
				?>
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
				<?
			}
		 }else{
?>
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
<?php 
		} //end if else fetal death certificate
	}
?>
<!--- until here only --- pet --->


<?php
	#added by devon 05/07/19
	#fetal death cert new
	if (((($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user)&&($fromtemp))||(($allow_medocs_user)&&($fromtemp))) {
		if($isIPBM){
			if($ipbmcanAccessDeathCertificate){
				?>
					<TR>
						<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
						<TD vAlign=top >
							<FONT face="Verdana,Helvetica,Arial" size=2>
							<?php
								echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_fetal_interface_new.php".URL_APPEND."&pid=$pid\" target=_blank>Fetal Death Certificate (Revised January 2007)</a>";
							?>
							</FONT>
						</TD>
					</TR>
				<?
			}
		 }else{
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php

	echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_fetal_interface_new.php".URL_APPEND."&pid=$pid\" target=_blank>Fetal Death Certificate (Revised January 2007)</a>";

?>
				</FONT>
			</TD>
		</TR>
<?php 
		} //end if else fetal death certificate 
	} //END new
?> 


<?php }} ?>
<?php
	#if (($isDied)&&($dept_belong['id']=="Medocs")) {
	#if ((($patient_result['result_code']==4)||($patient_result['result_code']==8)||($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($dept_belong['id']=="Medocs")) {
	#if ((($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($dept_belong['id']=="Medocs")) {
	#if ($allow_medocs_user) {
		if ((($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user || ($allow_ipbmMedocs_user && $isIPBM))) {
			//updated by carriane 09/02/17
			if($isIPBM){
				if($ipbmcanAccessDeathCertificate){
				?>
					<TR>
						<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
						<TD vAlign=top >
							<FONT face="Verdana,Helvetica,Arial" size=2>
					<?php
							if ($enc_Info['is_DOA']==1){
								echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_DOA_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
							}else{
								echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
							}

					?>
							</FONT>
						</TD>
					</TR>
				<?
				}
			}else{
			?>
				<TR>
					<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
					<TD vAlign=top >
						<FONT face="Verdana,Helvetica,Arial" size=2>
					<?php
						if ($enc_Info['is_DOA']==1){
							echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_DOA_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
						}else{
							echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
						}
					?>
						</FONT>
					</TD>
				</TR>
        <!-- added by jasper 01/05/12 -->
        <!-- updated by carriane 09/02/17 -->
        <?php 
        	} //end if else death certificate

        	if($isIPBM){
        		if($ipbmcanAccessDeathCertificate){
	    	?>
	    			<TR>
			            <td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			            <TD vAlign=top >
	                		<FONT face="Verdana,Helvetica,Arial" size=2>
								<?php
								    echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface_new.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate(New)</a>";
								?>
	                		</FONT>
	            		</TD>
	       			</TR>
	    	<?
	    		}
        	}else{
		        ?>
		        <TR>
		            <td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
		            <TD vAlign=top >
		                <FONT face="Verdana,Helvetica,Arial" size=2>
			<?php
		        #echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Erroneous Entry on Death Certificate</a>";
		        echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface_new.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate(New)</a>";
			?>
		                </FONT>
		            </TD>
		        </TR>
        <!-- added by jasper 01/05/12 -->
		<!-- added by VAN -->
		<!-- updated by carriane 09/02/17 -->
		<?
			} //end if else death certificate (new)

		if($isIPBM){
			if($ipbmcanAccessDeathCertificate){
				?>
					<TR>
						<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
						<TD vAlign=top >
							<FONT face="Verdana,Helvetica,Arial" size=2>
								<?php
								echo "<a href=\"javascript:void(0);\" onclick=\"viewDeathError();\">Erroneous Entry on Death Certificate</a>";
								?>
							</FONT>
						</TD>
					</TR>
				<?
			}
		}else{
			?>
			<TR>
				<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
				<TD vAlign=top >
					<FONT face="Verdana,Helvetica,Arial" size=2>
	<?php
			#echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Erroneous Entry on Death Certificate</a>";
			echo "<a href=\"javascript:void(0);\" onclick=\"viewDeathError();\">Erroneous Entry on Death Certificate</a>";
	?>
					</FONT>
				</TD>
			</TR>
		<!-- -->

<?php 	}
	} 
 #Referral

 #edited by VAN 09-22-09
// updated by carriane 09/02/17 --restrict to IPBM
if (($allow_referral && !$isIPBM) && !($discharged) && ((($encounter_type_a==2) || ($encounter_type_a==IPBMOPD_enc)) || (($encounter_type_a==1) &&($enc_Info['is_DOA']!=1)) || ((($encounter_type_a==3)||($encounter_type_a==4)||($encounter_type_a==IPBMIPD_enc))&&($isDied==0))))
 {
		 ?>
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'hfolder.gif','0'); ?>></td>
						<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"ReferItem();\" onmouseout=\"nd();\">Refer/Transfer Department</a>";
?>
								</FONT>
						</TD>
				</TR>
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'hfolder.gif','0'); ?>></td>
						<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"ReferOtherItem();\" onmouseout=\"nd();\">Refer/Transfer to Other Hospitals</a>";
?>
								</FONT>
						</TD>
				</TR>
<?php
}

	#if (($discharged)&&($dept_belong['id']=="Medocs")){
	#if ($dept_belong['id']=="Medocs"){
	#modified by carriane 10.8.17
	$canViewMedicalCertificate = 0;

	if (($allow_medocs_user)&&(($encounter_type_a==2) || ($encounter_type_a==1) || ($encounter_type_a==IPBMOPD_enc) || ($discharged)) && ($encounter_status<>'cancelled')&&!$isIPBM){
			$canViewMedicalCertificate = 1;

	}elseif ($isIPBM && $ipbmcanAccessMedicalCertificate && (($discharged) || ($encounter_type_a==IPBMOPD_enc)) && ($encounter_status<>'cancelled')){
		$canViewMedicalCertificate = 1;
	}

if ($encounter_nr && $canViewMedicalCertificate){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2> 
<?php
// echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_med_interface.php?encounter_nr=$encounter_nr\" target=_blank>Medical Certificate</a>";
					if(!$isIPBM && ($encounter_type_a==IPBMIPD_enc || $encounter_type_a==IPBMOPD_enc) && !$medocsCanViewIPBM){
						echo "<a href=\"javascript:void(0);\" onclick=\"noIPBMAcessAlert($pid);\">Medical Certificate</a>";
						
					}
						
					else{
 						echo "<a href=\"javascript:void(0);\" onclick=\"viewCertMed($pid);\">Medical Certificate</a>";
 						
					}
?>
				</FONT>
			</TD>
		</TR> 
		 
<?php
}
?>
<!-- added by VAN 03-27-08-->
<?php
		//echo "enctype = ".$encounter_type_a;
		if ((!($discharged) && (($encounter_type_a==3)||($encounter_type_a==4)||($encounter_type_a==IPBMIPD_enc))) && !($isDied) && ($allow_medocs_user)&&!$isIPBM){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
#echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr=$encounter_nr\" target=_blank>Cert. of Confinement</a>";
					// updated by carriane 09/04/18; add restriction in main medical records upon accessing IPBM Patients' data
					if(!$isIPBM && $encounter_type_a==IPBMIPD_enc && !$medocsCanViewIPBM)
						echo "<a href=\"javascript:void(0);\" onclick=\"noIPBMAcessAlert();\">Cert. of Confinement</a>";
					else
						echo "<a href=\"javascript:void(0);\" onclick=\"viewCertConf();\">Cert. of Confinement</a>";
					// end carriane
?>
				</FONT>
			</TD>
		</TR>
<?php }

if($isIPBM&&($encounter_type_a==IPBMIPD_enc)){
	if(!$discharged&&($ipbmcanAccessConfinementCertificate)){ ?>
	<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<?php
				#echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr=$encounter_nr\" target=_blank>Cert. of Confinement</a>";
				echo "<a href=\"javascript:void(0);\" onclick=\"viewCertConf();\">Cert. of Confinement</a>";
				?>
				</FONT>
			</TD>
		</TR>
	<?php
	}
}
?>

<?php
	if($isIPBM && ($encounter_type_a==IPBMIPD_enc || $encounter_type_a==IPBMOPD_enc)){
		
		if($ipbmcanAccessMedicalAbstract){		
?>
<TR>
	<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
	<TD vAlign=top >
		<FONT face="Verdana,Helvetica,Arial" size=2> 

			<a href="javascript:void(0);" onclick="viewMedAbst(<?=$pid; ?>,<?= $ipbmcanAccessMedicalAbstract; ?>);">Medical Abstract</a>
		</FONT>
	</TD>
</TR>
<?php
		} // END IF ABSTRACT ACCESS
	}//END $isIPBM && ($encounter_type_a==IPBMIPD_enc || $encounter_type_a==IPBMOPD_enc)
?>

<?php              
                if (($allow_medocs_user || ($allow_ipbmMedocs_user && $isIPBM)) && ($encounter_nr)){
?>
                 <tr>
                        <td align=center><img <?php echo createComIcon($root_path,'folder_page.png','0'); ?>>&nbsp;</td>
                             <td vAlign=top >
                                <font face="Verdana,Helvetica,Arial" size=2>
<?php
									// updated by carriane 09/04/18; add restriction in main medical records upon accessing IPBM Patients' data
									if(!$isIPBM && ($encounter_type_a==IPBMIPD_enc || $encounter_type_a==IPBMOPD_enc) && !$medocsCanViewIPBM){
										echo "<a href=\"javascript:void(0);\" onclick=\"noIPBMAcessAlert();\">History of Confinement</a>";
									}
   									else
   										echo "<a href=\"javascript:void(0);\" onclick=\"ConfinementHistory($pid);\">History of Confinement</a>";
   									// end carriane
?>
                                 </font>
                             </td>
                </tr>

<?php }#}?>
<!-- Vaccination Certificate if patient is new born
	 Medical Records ('Dialog box').
	 Comment by: borj 2014-05-06
-->
<!--edited by Borj 2014-17-01-->
<!--  <?php
                if (($allow_medocs_user) && ($pid)){
?>		
                 <TR>
                        <TD align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>>&nbsp;</td>
                             <TD vAlign=top >
                                <FONT face="Verdana,Helvetica,Arial" size=2>
<?php
    echo "<a href='#' onclick='printVaccinationCert(".$pid.")'>Vaccination Certificate</a>";
?>
                                 </FONT>
                           </TD>
                      </TR>
<?php }#}?> -->

<!--END-->

<?php
		if($isIPBM){
			if(($ipbmcanAccessReceivedPatientChart)&&(($encounter_type_a==IPBMIPD_enc)) && ($allow_ipbmMedocs_user && $isIPBM)){
				?>
					<TR>
						<td align=center><img <?php echo createComIcon($root_path,'check.gif','0'); ?>></td>
						<TD vAlign=top >
							<FONT face="Verdana,Helvetica,Arial" size=2>
							<?php
								echo "<a href=\"javascript:void(0);\" onclick=\"updateReceivedDate(".$encounter_nr.");\">Received Patient's Chart</a>";
							?>
							</FONT>
						</TD>
					</TR>
<?php
			}
		}elseif (($allow_receive)&&((($encounter_type_a==3)||($encounter_type_a==4)||($encounter_type_a==IPBMIPD_enc)) && ($allow_medocs_user))){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'check.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"updateReceivedDate(".$encounter_nr.");\">Received Patient's Chart</a>";
?>
				</FONT>
			</TD>
		</TR>
		<?php if (($received) && ($received!='not yet')){?>
				<TR>
							<td align=center><img <?php echo createComIcon($root_path,'delete.gif','0'); ?>></td>
							<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
										<?php
										echo "<a href=\"javascript:void(0);\" onclick=\"cancelReceivedDate(".$encounter_nr.");\">Cancel Received Chart</a>";
										?>
								</FONT>
							</TD>
						</TR>
		<?php } ?>
<?php }?>

<?php
				if($isIPBM){
					if(($ipbmcanAccessCancelDischarge) && ($discharged) && ($allow_ipbmMedocs_user) && ($encounter_type_a) && ($encounter_status<>'cancelled')){
						?>
							<TR>
								<td align=center><img <?php echo createComIcon($root_path,'manager.gif','0'); ?>></td>
								<TD vAlign=top >
									<FONT face="Verdana,Helvetica,Arial" size=2>
									<?php
										echo "<a href=\"javascript:void(0);\" onclick=\"cancelDischarged(".$encounter_nr.");\">Cancel Discharge</a>";
									?>
									</FONT>
								</TD>
							</TR>
				<?
					}
				}elseif (($allow_ipddiscancel) && ($discharged) && ($allow_medocs_user) && ($encounter_type_a) && ($encounter_status<>'cancelled')){
?>
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'manager.gif','0'); ?>></td>
						<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
								if(!$isIPBM && ($encounter_type_a==IPBMIPD_enc || $encounter_type_a==IPBMOPD_enc) && !$medocsCanViewIPBM)
									echo "<a href=\"javascript:void(0);\" onclick=\"noIPBMAcessAlert(".$encounter_nr.");\">Cancel Discharge</a>";
								else
									echo "<a href=\"javascript:void(0);\" onclick=\"cancelDischarged(".$encounter_nr.");\">Cancel Discharge</a>";
?>
								</FONT>
						</TD>
				</TR>
<?php 			}
?>


 <?php
                //added by shand 05-21-2013
                #can undo MGH
                if (($allow_MGH) && ($encounter_nr) && ($discharged==0) && ($is_maygohome)){
?>
                 <tr>
                        <td align=center><img <?php echo createComIcon($root_path,'arrow_undo.png','0'); ?>></td>
                             <td vAlign=top >
                                <font face="Verdana,Helvetica,Arial" size=2>
<?php
   echo "<a href=\"javascript:void(0);\" onclick=\"undoMGH(".$encounter_nr.");\">Undo MGH</a>";

?>
                                 </font>
                             </td>
                </tr>
<?php }#}?>






<?php
                //added by jarel 03-04-2013
				if($isIPBM){
					if($ipbmcanAccessCancelDeath && $death_date!='0000-00-00' && ($encounter_type_a) && $allow_ipbmMedocs_user){
					?>
						<TR>
	                        <td align=center><img <?php echo createComIcon($root_path,'blackcross_sm.gif','0'); ?>></td>
	                        <TD vAlign=top >
                                <FONT face="Verdana,Helvetica,Arial" size=2>
								<?php
									echo "<a href=\"javascript:void(0);\" onclick=\"cancelDeath(".$encounter_nr.",".$pid.");\">Cancel Death</a>";
								?>
                                </FONT>
                        	</TD>
               			 </TR>
					<?
					}
				}else{
					if (($allow_CancelDeath) && $death_date!='0000-00-00' && ($encounter_type_a) && ($allow_ipddiscancel)){
	?>
	                	<TR>
	                        <td align=center><img <?php echo createComIcon($root_path,'blackcross_sm.gif','0'); ?>></td>
	                        <TD vAlign=top >
	                                <FONT face="Verdana,Helvetica,Arial" size=2>
	<?php
									echo "<a href=\"javascript:void(0);\" onclick=\"cancelDeath(".$encounter_nr.",".$pid.");\">Cancel Death</a>";
	?>
	                                </FONT>
	                        </TD>
	               		</TR>
	<?php 			}
				}
?>

<!-- added by VAN 12-20-2011 -->
<?php
                if (($allow_ipddiscancel) && ($allow_medocs_user || ($allow_ipbmMedocs_user && $isIPBM)) && ($encounter_status=='cancelled')){
?>
                <TR>
                        <td align=center><img <?php echo createComIcon($root_path,'manager.gif','0'); ?>></td>
                        <TD vAlign=top >
                                <FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"undoCancellation(".$encounter_nr.",".$pid.");\">Undo Case Cancellation</a>";
?>
                                </FONT>
                        </TD>
                </TR>
<?php }#}?>

<!-- -->

	</TABLE>

<?php include_once($root_path.'modules/registration_admission/include/yh_options.php') ?>