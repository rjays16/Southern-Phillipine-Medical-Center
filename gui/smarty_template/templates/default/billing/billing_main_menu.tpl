
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
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDViewBill}}</`nobr></TD>
							<TD>Process billing of admitted patient or ER patient</TD>
						</tr> 
						end carriane --> 
						{{* Added by Francis *}}
						<!-- comment by: shandy <TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDViewBillnPHIC}}</`nobr></TD>
							<TD>Process billing of patients without PHIC</TD>
						</tr> -->
						<!-- added by poliam 01/05/2014 -->
						<!-- Commented by carriane 10/08/19; Refer BUG 2561 -->
						<!-- {{include file="common/submenu_row_spacer.tpl"}} -->
						<!-- ended by poliam 01/05/2014 -->
						<TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDViewBillPHIC}}</`nobr></TD>
							<!-- edited by:ian1-6-2014 -->
							<TD>Process billing of admitted patient or ER patient(New)</TD>
						</tr>
						{{* end - Francis *}}
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sLabServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDListOfBilling}}</nobr></TD>
							<TD>List of patients billed.</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
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
							<TD width="1%">{{$sManagePackageIcon}}</TD>                                            
							<TD class="submenu_item" width=35%><nobr>{{$LDManageClassification}}</nobr></TD>       
							<TD>Manage Packages </TD>                                                              
						</tr>   -->                                                                           
{{*						{{include file="common/submenu_row_spacer.tpl"}}				*}}	
						<TR>
							<TD width="1%">{{$sLDOtherServicesIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDOtherServices}}</nobr></TD>
							<TD>Manager for Miscellaneous Services</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sLDSocialReportsIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDBillReports}}</nobr></TD>
							<TD>Process transmittals to health insurances.</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
                        <TR>
                            <TD width="1%">{{$sLDTransmittalsHistIcon}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDTransmittalsHistory}}</nobr></TD>
                            <TD>History of Transmittals.</TD>
                        </tr>
                        {{include file="common/submenu_row_spacer.tpl"}}
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
			                            <td width="1%">{{$sLDAccountBudgetAllocIcon}}</td>
			                            <td class="submenu_item" width=35%><nobr>{{$LDAccountBudgetAlloc}}</nobr></td>
			                            <td>Manages Accounts and Budget Allotments</td>
			                        </tr>
			                        {{include file="common/submenu_row_spacer.tpl"}}
									<tr>
			                            <td width="1%">{{$sLDCashTransactionsIcon}}</td>
			                            <td class="submenu_item" width=35%><nobr>{{$LDCashTransactions}}</nobr></td>
			                            <td>Credit and Collection for Cash Transactions</td>
			                        </tr>
			                        {{include file="common/submenu_row_spacer.tpl"}}
			                        <TR>
			                            <TD width="1%">{{$sLDCreditCollectionIcon}}</TD>
			                            <TD class="submenu_item" width=35%><nobr>{{$LDCreditCollection}}</nobr></TD>
			                            <TD>Credit and Collection for Hospital Bills</TD>
			                        </tr>
			                        {{include file="common/submenu_row_spacer.tpl"}}
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
                            <TD width="1%">{{$sLDBillingReportsIcon}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDBillingReports}}</nobr></TD>
                            <TD>Reports of Billing</TD>
                        </tr>
                        {{include file="common/submenu_row_spacer.tpl"}}
                        <TR>
							<!-- <TD width="1%">{{$sLabSearchEmptIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDDocSearch}}</nobr></TD>
							<TD>Search Active and Inactive employee</TD>
						</tr>
                        {{include file="common/submenu_row_spacer.tpl"}}
                        <TR> -->
                            <TD width="1%">{{$sLDBillingReportsIcon_jasper}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDBillingReports_jasper}}</nobr></TD>

                            <TD>Reports of Billing</TD>

                        </tr>
                          {{include file="common/submenu_row_spacer.tpl"}}
                        <tr>
                        <TD width="1%">{{$sLDBillingIcon_Manual}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDBilling_PdfManual}}</nobr></TD>
                            <TD>PDF Copy of User's Manual</TD>
                            </tr>
                            {{include file="common/submenu_row_spacer.tpl"}}
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<p>
			<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<p>
			</blockquote>
