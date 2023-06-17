			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
					<TBODY >
						<TR>
							<TD class="submenu_title" colspan=3>Social Service Classification</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDClassifyNewPatient}}</`nobr></TD>
							<TD>Classify admitted patient or ER patient</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sLabServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDListOfClassifiedPatient}}</nobr></TD>
							<TD>View and update Social Service Classification status</TD>
						</TR>
						{{include file="common/submenu_row_footer.tpl"}}
						<TR>
							<TD width="1%">{{$sProgressNotesIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDProgressNotesList}}</nobr></TD>
							<TD>Description Progress Notes</TD>
						</TR>
						{{include file="common/submenu_row_footer.tpl"}}
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<br/>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
					<TBODY>
						<TR>
							<TD class="submenu_title" colspan=3>Social Service Management</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sBloodRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDManageClassification}}</nobr></TD>
							<TD>Manage social service classifications and discounts</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						
						<!-- added by VAN 07-05-08 -->
						<TR>
							<TD width="1%">{{$sModifierIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDManageModifiers}}</nobr></TD>
							<TD>Manage social service modifiers</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sBloodTestReceptionIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDSocialReports}}</nobr></TD>
							<TD>View and print specific status reports</TD>
						</TR>
						<!-- added by gelie 10-30-2015 -->
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sReportLaunchIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDSSReportLaunch}}</nobr></TD>
							<TD>Generate reports</TD>
						</TR>
						<!-- end gelie -->
						{{include file="common/submenu_row_footer.tpl"}}
						<!-- <TR>
							<TD width="1%">{{$sLabSearchEmptIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDDocSearch}}</nobr></TD>
							<TD>Search Active and Inactive employee</TD>
						</TR> -->
						<!-- {{include file="common/submenu_row_footer.tpl"}} -->
						<TR>
							<TD width="1%">{{$sSocialServiceIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDUsersManual}}</nobr></TD>
							<TD>PDF Copy of User's Manual</TD>
						</TR>
						{{include file="common/submenu_row_footer.tpl"}}
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<br/>
			<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
