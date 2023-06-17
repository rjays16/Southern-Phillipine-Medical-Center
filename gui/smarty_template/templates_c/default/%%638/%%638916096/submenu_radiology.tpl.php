<?php /* Smarty version 2.6.0, created on 2020-06-23 07:51:07
         compiled from radiology/submenu_radiology.tpl */ ?>
		<blockquote>
<!--			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
						<TR>
 				<td>
					<table cellSpacing=1 cellPadding=3 width=600>
					<tbody class="submenu">
						<tr>
							<td class="submenu_title" colspan=2>Transactions</td>
						</tr>
						<tr>
							<td class="submenu_item" width=30%><nobr><?php echo $this->_tpl_vars['LDCreateTransaction']; ?>
</nobr></td>
							<td>Create Service Transaction</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<td class="submenu_item" witdth=30%><nobr><?php echo $this->_tpl_vars['LDManageTransactions']; ?>
</nobr></td>
							<td>View, edit and delete service transactions</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>			
							<td class="submenu_item" widht=30%><nobr><?php echo $this->_tpl_vars['LDServicePrices']; ?>
</nobr></td>
							<td>Set the price for Radiology prices</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>			
							<td class="submenu_item" widht=30%><nobr><?php echo $this->_tpl_vars['LDViewAssignRequest']; ?>
</nobr></td>
							<td><?php echo $this->_tpl_vars['LDViewAssignRequestTxt']; ?>
</td>
						</tr>
					</tbody>
					</table>
				</td>
					</TR>
				<TBODY>
			</TABLE>
			<p>
-->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>Test Request</TD>
									</tr>
									<?php echo $this->_tpl_vars['LDCreateNewRadioServiceRequest']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

									<?php echo $this->_tpl_vars['LDRadioServiceRequestList']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									
									<!--<tr>
										<TD align="center" width="6%"><?php echo $this->_tpl_vars['sRadioTechIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioTech']; ?>
</nobr></TD>
										<TD>Record served radiological (XRAY, CT-SCAN, MRI, ULTRASOUND and others) requests.</TD>
									</tr> -->
									<?php if ($this->_tpl_vars['getOB'] != 'OB'): ?>
									<?php echo $this->_tpl_vars['LDRadioScheduleRequestCalendar']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<?php endif; ?>
									
									<?php echo $this->_tpl_vars['LDRadioScheduleRequestList']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									
									<?php echo $this->_tpl_vars['LDUndoneRequest']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<?php if ($this->_tpl_vars['getOB'] != 'OB'): ?>
									<?php echo $this->_tpl_vars['LDDoneRequest']; ?>

                                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                                   
                                    <?php echo $this->_tpl_vars['LDUnifiedResults']; ?>


                                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                                     <?php endif; ?>
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<p></p>
			<!--
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>
										Radiology Borrowing System
										</TD>
									</tr>
									<?php echo $this->_tpl_vars['LDRadioPatientList']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

									<?php echo $this->_tpl_vars['LDRadioBorrowList']; ?>

								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			-->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
						
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
								
									<tr>
										<TD class="submenu_title" colspan=3>
										<?php if ($this->_tpl_vars['getOB'] != 'OB'): ?>  Radiology <?php endif; ?> Borrowing System
										</TD>
									</tr>
									<?php if ($this->_tpl_vars['getOB'] != 'OB'): ?>
									<tr>
										<TD align="center" width="6%"><?php echo $this->_tpl_vars['sRadioPatientListIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioPatientList']; ?>
</nobr></TD>
										<TD>List of all radiology patients</TD>
									</tr>
									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<tr>
										<TD align="center"><?php echo $this->_tpl_vars['sRadioBorrowListIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioBorrowList']; ?>
</nobr></TD>
										<TD>Encode readers fee for patients</TD>
									</tr>
										<?php endif; ?>
									<!--Added by: Borj 2014-09-16 Professional Fee-->
									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<tr>
										<TD align="center"><?php echo $this->_tpl_vars['sRadioReaderListIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioReadersList']; ?>
</nobr></TD>
										<TD>Encode readers fee for patients</TD>
									</tr>
									<!--end-->
								</TBODY>
							</TABLE>
						
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<p></p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<tr>
							<TD class="submenu_title" colspan=3>Administration</TD>
						</tr>
						<!--edited by VAN 03-15-08 -->
						<!--
						<tr>
							<TD width="6%" align="center"><?php echo $this->_tpl_vars['sRadioServicesIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioServicesOLD']; ?>
</nobr></TD>
							<TD>Manage radiology services options</TD>
						</tr>
						
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						-->
						<tr>
							<TD align="center" width="6%"><?php echo $this->_tpl_vars['sRadioServicesIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioServices']; ?>
</nobr></TD>

							<TD>Manage <?php if ($this->_tpl_vars['getOB'] != 'OB'): ?> radiology <?php endif; ?>services options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioServicesGroupIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioServicesGroups']; ?>
</nobr></TD>
							<TD>Manage <?php if ($this->_tpl_vars['getOB'] != 'OB'): ?> radiology <?php endif; ?>group options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<!-- added by VAN 07-07-08 -->
						<?php if ($this->_tpl_vars['getOB'] != 'OB'): ?>	
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioFindingCodeIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioFindingCode']; ?>
</nobr></TD>
							<TD>Manage <?php if ($this->_tpl_vars['getOB'] != 'OB'): ?> radiology <?php endif; ?> finding's code</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioImpressionCodeIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioImpressionCode']; ?>
</nobr></TD>
							<TD>Manage <?php if ($this->_tpl_vars['getOB'] != 'OB'): ?> radiology <?php endif; ?> impression's code</TD>
						</tr>
							
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioDoctorPartnerIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioDoctorPartner']; ?>
</nobr></TD>
							<TD>Manage <?php if ($this->_tpl_vars['getOB'] != 'OB'): ?> radiology <?php endif; ?>'s co-reader physicians for film reading</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioDOCSchedulerIcon']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioDOCScheduler']; ?>
</nobr></td>
							<td>Resident in-charge scheduler, plan, view, update, edit, etc.</td>
						</tr>
					
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioReportIcon']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioReport']; ?>
</nobr></td>
							<td>View and print specific status reports</td>
						</tr>
					
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<!-- added by: syboy 01/12/2016 : meow -->
						<!-- <tr>
							<TD align="center"><?php echo $this->_tpl_vars['LDDocSearch']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDDocSearchLink']; ?>
</nobr></td>
							<td>Search Active and Inactive employee</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
						<!--Added by Borj 2014-08-04 ISO-->
						<?php endif; ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioUserManualtIcon']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioUserManualReport']; ?>
</nobr></td>
							<td>PDF Copy of User's Manual</td>
						</tr>
						
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                                                <!-- added by KENTOOT 10-10-2014 -->
                                                
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioGenIcon']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDReportLauncher']; ?>
</nobr></td>
							<td>Generate <?php if ($this->_tpl_vars['getOB'] != 'OB'): ?> radiology <?php endif; ?> Reports</td>
						</tr>
					
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<p>
			<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
			<p>
			</blockquote>