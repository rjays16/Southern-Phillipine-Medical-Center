<?php /* Smarty version 2.6.0, created on 2021-02-03 09:58:18
         compiled from laboratory/submenu_lab.tpl */ ?>
			<blockquote>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Laboratory Test Requests</TD>
						</tr>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sRequestTestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRequestTest']; ?>
</`nobr></TD>
							<TD>Fill out request for laboratory service</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesRequest']; ?>
</nobr></TD>
							<TD>View, edit and delete laboratory service requests</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD class="submenu_icon"><?php echo $this->_tpl_vars['sLabServicesRequestSampleIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesRequestSample']; ?>
</nobr></TD>
							<TD class="submenu_text">View, edit and delete laboratory service requests with or without sample</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesOrderIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesOrder']; ?>
</nobr></TD>
							<TD>List of requests to be done</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesDoneIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesDone']; ?>
</nobr></TD>
							<TD>List of requests that have results</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        
                        <TR>
                            <TD width="1%"><?php echo $this->_tpl_vars['sLabServicesResultIcon']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesResult']; ?>
</nobr></TD>
                            <TD>List of requests that have results (Manually Encoded requests through LIS are included)</TD>
                        </tr>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<!--
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sOtherClinicalIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDOtherClinical']; ?>
</nobr></TD>
							<TD class="submenu_text">Add other Clinical Charges to Laboratory services</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						-->
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<p>
		<!-- 	<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3><?php echo $this->_tpl_vars['LDBloodBank']; ?>
</TD>
						</tr>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sBloodRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBloodRequest']; ?>
</nobr></TD>
							<TD>Fill out request for blood service</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sBloodServicesRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBloodServicesRequest']; ?>
</nobr></TD>
							<TD>View, edit and delete blood service requests</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sBloodServicesOrderIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBloodLabServicesOrder']; ?>
</nobr></TD>
							<TD>List of blood requests to be done</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sBloodServicesDoneIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBloodServicesDone']; ?>
</nobr></TD>
							<TD>List of blood requests that have results</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD class="submenu_icon"><?php echo $this->_tpl_vars['blood_promissory_icon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['blood_promissory_title']; ?>
</nobr></TD>
							<TD class="submenu_text">Fill out promissory note for blood replacement</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD class="submenu_icon"><?php echo $this->_tpl_vars['blood_donor_icon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['blood_donor_title']; ?>
</nobr></TD>
							<TD class="submenu_text">Register blood donor</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <TR>
                            <TD width="1%"><?php echo $this->_tpl_vars['sBloodGenerateReportIcon']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBloodGenerateReport']; ?>
</nobr></TD>
                            <TD>Blood Bank Reports</TD>
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
 -->
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3><?php echo $this->_tpl_vars['LDSpecialLab']; ?>
</TD>
						</tr>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sSpecialLabRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDSpecialLabRequest']; ?>
</nobr></TD>
							<TD>Fill out request for special laboratory service</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sSpecialLabServicesRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDSpecialLabServicesRequest']; ?>
</nobr></TD>
							<TD>View, edit and delete special laboratory service requests</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sSpecialLabServicesOrderIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDSpecialLabLabServicesOrder']; ?>
</nobr></TD>
							<TD>List of special laboratory requests to be done</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sSpecialLabServicesDoneIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDSpecialLabServicesDone']; ?>
</nobr></TD>
							<TD>List of special laboratory requests that have results</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sSpecialLabGenerateReportIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDSpecialLabGenerateReport']; ?>
</nobr></TD>
							<TD>Special Laboratory Reports</TD>
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
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3><?php echo $this->_tpl_vars['LDICLab']; ?>
</TD>
						</tr>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sICLabRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDICLabRequest']; ?>
</nobr></TD>
							<TD>Fill out request for industrial clinic laboratory service</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sICLabServicesRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDICLabServicesRequest']; ?>
</nobr></TD>
							<TD>View, edit and delete industrial clinic laboratory service requests</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sICLabServicesOrderIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDICLabLabServicesOrder']; ?>
</nobr></TD>
							<TD>List of industrial clinic laboratory requests to be done</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sICLabServicesDoneIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDICLabServicesDone']; ?>
</nobr></TD>
							<TD>List of industrial clinic laboratory requests that have results</TD>
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
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3><?php echo $this->_tpl_vars['LDAdministration']; ?>
</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesAdminIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesAdmin']; ?>
</nobr></TD>
							<TD>Manage laboratory services options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesGroupsIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabServicesGroups']; ?>
</nobr></TD>
							<TD>Manage laboratory sections options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<!-- -->
						<!--added by Raissa 02-02-09-->
						<TR>
								<TD class="submenu_icon"><?php echo $this->_tpl_vars['sLabTestsIcon']; ?>
</TD>
								<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLabTests']; ?>
</nobr></TD>
								<TD class="submenu_text">Manage laboratory tests</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
								<TD align="center" class="submenu_icon"><?php echo $this->_tpl_vars['sLabReagentsIcon']; ?>
</TD>
								<TD class="submenu_item"><nobr><?php echo $this->_tpl_vars['LDLabReagents']; ?>
</nobr></TD>
								<TD class="submenu_text">Manage laboratory reagents options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
								<TD class="submenu_icon"><?php echo $this->_tpl_vars['sLabReagentsInventoryIcon']; ?>
</TD>
								<TD class="submenu_item"><nobr><?php echo $this->_tpl_vars['LDLabReagentsInventory']; ?>
</nobr></TD>
								<TD class="submenu_text">Manage laboratory reagents inventory</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD class="submenu_icon"><?php echo $this->_tpl_vars['sLabServicesReportIcon']; ?>
</TD>
							<TD class="submenu_item"><nobr><?php echo $this->_tpl_vars['LDLabServicesReport']; ?>
</nobr></TD>
							<TD class="submenu_text">View and print specific status reports</TD>
						</tr>
							<!-- Added by Matsuu 07152017 -->
						<TR>
							<TD class="submenu_icon"><?php echo $this->_tpl_vars['sSpecialLabGenerateReportIcon']; ?>
</TD>
							<TD class="submenu_item"><nobr><?php echo $this->_tpl_vars['LDLabGenerateReport']; ?>
</nobr></TD>
							<TD class="submenu_text">Generate Laboratory Report</TD>
						</tr>
						<!-- Ended by Matsuu 07152017 -->
						<!-- <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->

						<!-- <TR>
                            <TD width="1%"><?php echo $this->_tpl_vars['sLabDocSearch']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDDocSearch']; ?>
</nobr></TD>
                            <TD>Search Active and Inactive employee</TD>
                        </tr> -->
                    <!--     <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->


					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<!--Added by Borj 2014-08-04 ISO-->
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3><?php echo $this->_tpl_vars['LDLabUserManual']; ?>
</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLaboUserManualIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDLaboUserManual']; ?>
</nobr></TD>
							<TD>PDF Copy of User's Manual</TD>
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

				<script>
					var l = window.location,
							baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1] +'/';
					if(window.parent.location['href'] === baseUrl){
						// Do nothing if the active window location is the index..
					}else{
						localStorage.notifToken = "<?php echo $this->_tpl_vars['notification_token']; ?>
";
						localStorage.notifSocketHost = "<?php echo $this->_tpl_vars['notification_socket']; ?>
";
						localStorage.username = "<?php echo $this->_tpl_vars['username']; ?>
";
						$j('<iframe />');  // Create an iframe element
						$j('<iframe />', {
							id: 'notifcontIf',
							src: '../../socket.html'
						}).appendTo('body');
						$j("iframe#notifcontIf").css("border-style","none");
						$j("iframe#notifcontIf").css("height", "0px");
					}
				</script>
			<p>
			<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
			<p>
			</blockquote>