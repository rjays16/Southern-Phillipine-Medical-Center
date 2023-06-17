<?php /* Smarty version 2.6.0, created on 2020-03-13 13:43:38
         compiled from billing/billing_main_menu.tpl */ ?>

			<blockquote>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Billing Service</TD>
						</tr>
						<!-- Commented by carriane 10/08/19; Refer BUG 2561
						 <TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sRequestTestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDViewBill']; ?>
</`nobr></TD>
							<TD>Process billing of admitted patient or ER patient</TD>
						</tr> 
						end carriane --> 
												<!-- comment by: shandy <TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sRequestTestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDViewBillnPHIC']; ?>
</`nobr></TD>
							<TD>Process billing of patients without PHIC</TD>
						</tr> -->
						<!-- added by poliam 01/05/2014 -->
						<!-- Commented by carriane 10/08/19; Refer BUG 2561 -->
						<!-- <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
						<!-- ended by poliam 01/05/2014 -->
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sRequestTestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDViewBillPHIC']; ?>
</`nobr></TD>
							<!-- edited by:ian1-6-2014 -->
							<TD>Process billing of admitted patient or ER patient(New)</TD>
						</tr>
												<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDListOfBilling']; ?>
</nobr></TD>
							<TD>List of patients billed.</TD>
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
			</TABLE>
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Billing Management</TD>
						</tr>
<!--						<TR>                                                                                       
							<TD width="1%"><?php echo $this->_tpl_vars['sManagePackageIcon']; ?>
</TD>                                            
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDManageClassification']; ?>
</nobr></TD>       
							<TD>Manage Packages </TD>                                                              
						</tr>   -->                                                                           
	
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLDOtherServicesIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDOtherServices']; ?>
</nobr></TD>
							<TD>Manager for Miscellaneous Services</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLDSocialReportsIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBillReports']; ?>
</nobr></TD>
							<TD>Process transmittals to health insurances.</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <TR>
                            <TD width="1%"><?php echo $this->_tpl_vars['sLDTransmittalsHistIcon']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDTransmittalsHistory']; ?>
</nobr></TD>
                            <TD>History of Transmittals.</TD>
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
			</TABLE>
			<br/>
			<table cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<tbody>
					<tr>
						<td>
							<table cellSpacing=1 cellPadding=3 width=600>
								<tbody class="submenu">
									<tr>
										<td class="submenu_title" colspan=3>Credit and Collection</TD>
									</tr>
			                        <tr>
			                            <td width="1%"><?php echo $this->_tpl_vars['sLDAccountBudgetAllocIcon']; ?>
</td>
			                            <td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDAccountBudgetAlloc']; ?>
</nobr></td>
			                            <td>Manages Accounts and Budget Allotments</td>
			                        </tr>
			                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<tr>
			                            <td width="1%"><?php echo $this->_tpl_vars['sLDCashTransactionsIcon']; ?>
</td>
			                            <td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDCashTransactions']; ?>
</nobr></td>
			                            <td>Credit and Collection for Cash Transactions</td>
			                        </tr>
			                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                        <TR>
			                            <TD width="1%"><?php echo $this->_tpl_vars['sLDCreditCollectionIcon']; ?>
</TD>
			                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDCreditCollection']; ?>
</nobr></TD>
			                            <TD>Credit and Collection for Hospital Bills</TD>
			                        </tr>
			                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br/>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Administration</TD>
						</tr>
						<TR>
                            <TD width="1%"><?php echo $this->_tpl_vars['sLDBillingReportsIcon']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBillingReports']; ?>
</nobr></TD>
                            <TD>Reports of Billing</TD>
                        </tr>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <TR>
							<!-- <TD width="1%"><?php echo $this->_tpl_vars['sLabSearchEmptIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDDocSearch']; ?>
</nobr></TD>
							<TD>Search Active and Inactive employee</TD>
						</tr>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <TR> -->
                            <TD width="1%"><?php echo $this->_tpl_vars['sLDBillingReportsIcon_jasper']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBillingReports_jasper']; ?>
</nobr></TD>

                            <TD>Reports of Billing</TD>

                        </tr>
                          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <tr>
                        <TD width="1%"><?php echo $this->_tpl_vars['sLDBillingIcon_Manual']; ?>
</TD>
                            <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDBilling_PdfManual']; ?>
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
			</TABLE>
			<p>
			<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
			<p>
			</blockquote>