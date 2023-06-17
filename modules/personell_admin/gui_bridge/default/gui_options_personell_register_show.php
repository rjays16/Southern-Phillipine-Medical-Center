<!---- add 02-22-07 --------->
<script type="text/javascript">
function blocking(objectName, flag){
	 if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
	 } else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
	 } else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
	 }
}/* end of function blocking*/

function hideThis(label_role){
 var allow_dependent_only = '<?=$allow_dependent_only?>';
 
   if (allow_dependent_only==0){   
	 switch(label_role){
			case "doctor": 
				 blocking("doctor_option",true); /* display */ 
				 blocking("nurse_option",false);  /* not display */
			blocking("others_option",false);  /* not display */
			break;
			
		case "nurse": 
				 blocking("doctor_option",false); /* display */ 
				 blocking("nurse_option",true);  /* not display */
			blocking("others_option",false);  /* not display */
		break;
		
		case "others": 
				 blocking("doctor_option",false); /* display */ 
				 blocking("nurse_option",false);  /* not display */
			blocking("others_option",true);  /* not display */
		 break;
	 }/* end of switch statement*/
   }  
}/*end of function hideThis*/

function preSet(){
	var label_role;

	if (document.forms["form_option"].short_id.value.match("D")!=null)
		label_role = "doctor";
	else if (document.forms["form_option"].short_id.value.match("N")!=null)
		label_role = "nurse";
	else
		label_role = "others";
		
	hideThis(label_role);
}
</script>

<!---- add 02-22-07 --------->

<body onLoad="preSet();">

<?php
#echo "van : ".$short_id;
$hideinfo = '';
if ($allow_dependent_only){
    $hideinfo = 'style="display:none"';   
}    

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
<img <?php echo createComIcon($root_path,'angle_left_s.gif',0); ?>>
<br>
<FONT color="#cc0000">
<?php echo $LDOptions4Employee; ?>
</font>
<form name="form_option" id="form_option">

<TABLE cellSpacing=0 cellPadding=0 bgColor=#999999 border=0>
				<TBODY>
				<TR>
					<TD>
						<TABLE cellSpacing=1 cellPadding=2 bgColor=#999999 
						border=0>
							<TBODY>
					
							 <TR <?php echo $hideinfo?> bgColor=#eeeeee id="doctor_option"> <td align=center><img <?php echo createComIcon($root_path,'man-whi.gif','0') ?>></td>
								<TD vAlign=top >

<!-- 				 <a href="javascript:alert('Function not  available yet');"><?php echo $LDAssignDoctorDept; ?></a>
 -->				 <!-- added by: syboy 02/25/2016 : meow -->
 					<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != '' || $row_per['status'] == 'expired'): ?>
 						<?php echo $LDAssignDoctorDept; ?>
					<?php else: ?>
						<a href="<?php echo $root_path; ?>modules/doctors/doctors-select-dept.php<?php echo URL_APPEND."&target=plist&nr=$personell_nr&user_origin=personell_admin"; ?>"><?php echo $LDAssignDoctorDept; ?></a></TD>
					<?php endif ?>
					<!-- Ended syboy -->
								</TR>
				 
					 <?php Spacer(); ?>
					
						 <TR <?php echo $hideinfo?> bgColor=#eeeeee id="nurse_option"><td align=center><img <?php echo createComIcon($root_path,'nurse.gif','0') ?>></td>
								<TD vAlign=top width=150> 
				<!-- added by: syboy 02/25/2016 : meow -->
 					<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != '' || $row_per['status'] == 'expired'): ?>
 						<?php echo $LDAssignNurseDept; ?>
					<?php else: ?>					 
				<a href="<?php echo $root_path; ?>modules/nursing_or/nursing-or-select-dept.php<?php echo URL_APPEND."&target=plist&nr=$personell_nr&user_origin=personell_admin"; ?>"><?php echo $LDAssignNurseDept; ?></a>
					 <?php endif ?>
					<!-- Ended syboy -->
					 </FONT></TD>
								</TR>
				 
					 <?php Spacer(); ?>
				
					 <TR <?php echo $hideinfo?> bgColor=#eeeeee id="others_option"><td align=center><img <?php echo createComIcon($root_path,'authors.gif','0') ?>></td>
								<TD vAlign=top width=150>
					<!-- added by: syboy 12/18/2015 : meow --> 
					<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != '' || $row_per['status'] == 'expired'): ?>
						<?php echo $LDAssignStaffDept; ?>
					<?php else: ?>
						<a href="<?php echo $root_path; ?>modules/staff/staff-select-dept.php<?php echo URL_APPEND."&target=plist&nr=$personell_nr&user_origin=personell_admin"; ?>"><?php echo $LDAssignStaffDept; ?></a>
					<?php endif ?>
					<!-- Ended syboy -->	

					 </FONT></TD>
								</TR>
				 
					 <?php Spacer(); ?>
				
							<TR <?php echo $hideinfo?> bgColor=#eeeeee>  <td align=center><img <?php echo createComIcon($root_path,'violet_phone.gif','0') ?>></td>
								<TD vAlign=top >
					<!-- added by: syboy 12/18/2015 : meow --> 
					<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''): ?>
						<?php echo $LDAddPhoneInfo; ?>
					<?php else: ?>
						<a href="<?php echo $root_path.'modules/phone_directory/phone_edit.php'.URL_APPEND.'&user_origin=pers&nr='.$personell_nr; ?>"><?php echo $LDAddPhoneInfo ?></a>
					<?php endif ?>
					<!-- Ended syboy -->				 
					 </FONT></TD>  
								</TR>				 
				 
<!--  			   
					 <?php Spacer(); ?>
					
							 <TR bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'disc_repl.gif','0') ?>></td>
								<TD vAlign=top > 
									 
					<a href="javascript:alert('Function not  available yet')"><?php echo $LDPayrollOptions ?></a>
					 </FONT></TD>
								</TR>
				 
					 <?php Spacer(); ?>
					
					<TR bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'document.gif','0') ?>></td>
								<TD vAlign=top > 
									 <nobr>
				 <a href="javascript:alert('Function not  available yet')"><?php echo $LDLegalDocuments ?></a>
					</nobr> </FONT></TD>
								</TR>
 -->			   
					 <?php Spacer(); ?>
					
					<TR bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'bn.gif','0') ?>></td>
								<TD vAlign=top > 
									 <nobr>
					<!-- added by: syboy 12/18/2015 : meow -->
					<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''): ?>
						<?php echo $LDShowPersonalData; ?>
					<?php else: ?>
						<a href="<?php echo "person_register_show.php".URL_REDIRECT_APPEND."&pid=$pid&from=$from"; ?>"><?php echo $LDShowPersonalData ?></a>
					<?php endif ?>
					<!-- Ended syboy -->
					</nobr> </FONT></TD>
								</TR>
				<?php Spacer(); ?>
				<TR bgColor=#eeeeee>
			<td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2> 
				
						<?php 
				// added By Mark Gocela 07/14/16

				if(isset($_GET['department']) && $_GET['department'] =="Cashier" || $row_per['status'] == 'expired'){ ?>
						<a href="/" onclick="return false;" style="cursor: default;color: gray;">Dependents</a>
						<?php
				}
				else{ ?>
						<a href="javascript:void(0);" onClick="Dependents();" onMouseOut="nd();">Dependents</a>
						<?php
				}

				?>


				</FONT>
			</TD>
		</TR>
		<!-- added by VAN 12-04-09 -->
					 <?php Spacer(); ?>
					
					<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'application_key.png','0') ?>></td>
								<TD vAlign=top > 
									 <nobr>
					<?php
							global $db;
							$sql = "SELECT * FROM care_users WHERE personell_nr='".$personell_nr."' LIMIT 1";
							$rs = $db->Execute($sql);
							$row = $rs->FetchRow();  
							
							if(!empty($row['login_id'])){
									$with_access = 1;
									$userid = $row['login_id'];
									$username = '';
							}else{
									$with_access = 0;
									$sql_p = "SELECT p.* 
													FROM care_person AS p
													INNER JOIN care_personell AS pr ON pr.pid=p.pid
													WHERE nr='".$personell_nr."'";
													
									$rs_p = $db->Execute($sql_p);
									$row_p = $rs_p->FetchRow();		
									$userid = strtr($row_p['name_last'],' ','_');
									$username = strtr(($row_p['name_first'].' '.$row_p['name_last']),' ','+');
							}		
						
					?>
					<!-- added by: syboy 12/18/2015 : meow -->
					<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != '' || $row_per['status'] == 'expired'): ?>
						<?php echo "Access Permission"; ?>
					<?php else: ?>
						<!-- start - modified by Mark Ryan Guerra 3/15/2018 -->
						<a href="javascript:void(0);" onClick="if(accessMe()){alert('Access Permission is locked')} showPermission('<?=$personell_nr?>','<?=$with_access?>','<?=$userid?>','<?=$username?>');" onMouseOut="nd();">Access Permission</a>
						<!-- end modified by Mark Ryan Guerra -->
					<?php endif ?>
					<!-- Ended syboy -->				 
					</nobr> </FONT></TD>
								</TR>			
								
					<!--added by VAN 11-04-09 -->			
					 <?php Spacer(); ?> 
				 	
				 	<!-- update by carriane 07/06/17 -->
					<?php if ($row_per['death_status'] === "0000-00-00" && empty($row_per['status'])){ ?> 
							<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'user_delete.png','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>
							<!-- added by: syboy 12/18/2015 : meow -->			
							<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''): ?>
								<?php echo "Deactivate the personnel employment"; ?>
							<?php else: ?>
								<!--start -  modified by Mark Ryan Guerra  3/15/2018-->
								<a  onClick="if(accessMe()){alert ('Access permision is locked');} javascript:deactivatePersonnel('<?=$personell_nr?>',1);" href="#">Deactivate the personnel employment</a>
								<!-- end - modified by Mark Ryan Guerra -->
							<?php endif ?>
							<!-- Ended syboy -->	
							</nobr> </FONT></TD>
										</TR>    
						<?php if ($with_access){?>					
							<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'lock_edit.png','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>
							<!-- added by: syboy 12/18/2015 : meow -->
							<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''): ?>
								<?php echo "Change user password"; ?>
							<?php else: ?>
								<!--start  modified by Mark Ryan Guerra 3/15/2018 -->
								<a onClick="if(accessMe()){alert ('Access permision is locked');}javascript:changePassword('<?=$personell_nr?>');" href=javascript:changePassword('<?=$personell_nr?>');">Change user password</a>
								<!--end  modified by Mark Ryan Guerra 3/15/2018 -->
							<?php endif ?>
							<!-- Ended syboy -->
							</nobr> </FONT></TD>
										</TR>			
					<?php 
								}
						}else{?>		
							<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'bn.gif','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>
							<!-- added by: syboy 12/18/2015 : meow -->				 	
							<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != '' || $row_per['status'] == 'expired'): ?>
								<?php echo "Activate the personnel employment"; ?>
							<?php else: ?>
								<!-- start - modified by Mark Ryan Guerra 3/15/2018-->
								<a onClick="if(accessMe()){alert ('Access permision is locked');} javascript:deactivatePersonnel('<?=$personell_nr?>',0);" href="#">Activate the personnel employment</a>
								<!-- end -  modified by Mark Ryan Guerra -->
							<?php endif ?>
							<!-- Ended syboy -->	
							</nobr> </FONT></TD>
										</TR>
				

									<!-- John --> 
					<?php Spacer(); ?>
					<?php } ?>
					
							<TR <?php echo $hideinfo?> bgColor=#eeeee><td align=center><img <?php echo createComIcon($root_path,'group_add.png','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>				 	

								<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''  || $row_per['status'] == 'expired'): ?>
								<?php echo "Orientation"; ?> 
							<?php else: ?>

							

									<?php 
									require_once($root_path . 'include/care_api_classes/class_acl.php');
									$objAcl = new Acl($_SESSION['sess_temp_userid']);
									$seePermission = $objAcl->checkPermissionRaw('_a_1_orientation');


									$sqlCheckRecord="SELECT status FROM care_personell
									 WHERE nr='".$personell_nr."'";
									
								
									$result=$db->Execute($sqlCheckRecord);
									if($result->RecordCount()) {
										while ($row=$result->FetchRow()){
											if ($row['status']==deleted){
												echo "Orientation";	
											}else{
												?>
												<a href="javascript:function(){return false};" onClick="showOrientation('<?=$personell_nr?>','<?=$with_access?>','<?=$userid?>','<?=$username?>','<?=$seePermission?>');" onMouseOut="nd();">Orientation</a>
											<?php
											}
										}
									}else {
										?><a href="javascript:function(){return false};" onClick="showOrientation('<?=$personell_nr?>','<?=$with_access?>','<?=$userid?>','<?=$username?>','<?=$seePermission?>');" onMouseOut="nd();">Orientation</a>
										<?php
									}
									
									?>
							<?php endif ?>

							
							<!-- Ended John -->	
							</nobr> </FONT></TD>
							<?php 
							if(substr($short_id, 0, 1) == 'D'){
								$checkWebAcct="SELECT * FROM seg_doctor_meeting WHERE doctor_id=".$db->qstr($personell_nr);
								$resultWebex=$db->Execute($checkWebAcct);
								$create = 0;
								if($resultWebex->RecordCount()<1) {
									$labelWebex = "Create Webex Account";
									$create = 1;
								}else{
									$labelWebex = "Update Webex Account";
								}
							?> 
								<tr bgColor=#eeeee>
									<td align=center>
										<img <?php echo createComIcon($root_path,'world_link.png','0') ?>>
									</td>
									<td>
										<a href="#" onClick="openwebexdialog('<?=$personell_nr?>','<?=$create?>')" onMouseOut="nd();">
											<?php echo $labelWebex; ?>
										</a>
									</td>
								</tr>
							<?php } // close if ($short_id)?>
							</TR>

							<?php 
							if ( strpos("ND", substr($short_id, 0, 1), 0) !== false ) {
								$create = 0;
								if (!$fb_userid) {
									$labelFbId = "Add FB Messenger ID";
									$create = 1;
								}else{
									$labelFbId = "Update FB Messenger ID";
								}
							?> 
								<tr bgColor=#eeeee>
									<td align=center>
										<img <?php echo createComIcon($root_path,'fbmsngr.png','0') ?>>
									</td>
									<td>
										<a href="#" onClick="openMessengerIdInputDialog('<?=$personell_nr?>','<?=$create?>')" onMouseOut="nd();">
											<?php echo $labelFbId; ?>
										</a>
									</td>
								</tr>
							<?php } // close if not doctor or nurse ?>
				</TBODY>
		</TABLE>
		</TD></TR>
		</TBODY>
		</TABLE>
		<input type="hidden" name="short_id" id="short_id" value="<?php echo $short_id; ?>">
</form>
</body>