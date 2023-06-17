			<blockquote>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<!-- <TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Laboratory Test Requests</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDRequestTest}}</`nobr></TD>
							<TD>Fill out request for laboratory service</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sLabServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesRequest}}</nobr></TD>
							<TD>View, edit and delete laboratory service requests</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD class="submenu_icon">{{$sLabServicesRequestSampleIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesRequestSample}}</nobr></TD>
							<TD class="submenu_text">View, edit and delete laboratory service requests with or without sample</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sLabServicesOrderIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesOrder}}</nobr></TD>
							<TD>List of requests to be done</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sLabServicesDoneIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesDone}}</nobr></TD>
							<TD>List of requests that have results</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
                        
                        <!--
						<TR>
							<TD width="1%">{{$sOtherClinicalIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDOtherClinical}}</nobr></TD>
							<TD class="submenu_text">Add other Clinical Charges to Laboratory services</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						--><!-- 
					</TBODY>
					</TABLE> -->
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
							<TD class="submenu_title" colspan=3>{{$LDBloodBank}}</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sBloodRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDBloodRequest}}</nobr></TD>
							<TD>Fill out request for blood service</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sBloodServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDBloodServicesRequest}}</nobr></TD>
							<TD>View, edit and delete blood service requests</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sBloodServicesOrderIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDBloodLabServicesOrder}}</nobr></TD>
							<TD>List of blood requests to be done</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sBloodServicesDoneIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDBloodServicesDone}}</nobr></TD>
							<TD>List of blood requests that have results</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD class="submenu_icon">{{$blood_promissory_icon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$blood_promissory_title}}</nobr></TD>
							<TD class="submenu_text">Fill out promissory note for blood replacement</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD class="submenu_icon">{{$blood_donor_icon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$blood_donor_title}}</nobr></TD>
							<TD class="submenu_text">Register blood donor</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
                            <TD width="1%">{{$sBloodServicesResultIcon}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDBloodServicesResult}}</nobr></TD>
                            <TD>List of crossmatch that have blood compatibility results (Manually Encoded requests through LIS are included)</TD>
                        </tr>
                        {{include file="common/submenu_row_spacer.tpl"}}

                        <TR>
                            <TD width="1%">{{$sBloodGenerateReportIcon}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDBloodGenerateReport}}</nobr></TD>
                            <TD>Blood Bank Reports</TD>
                        </tr>
                        {{include file="common/submenu_row_spacer.tpl"}}
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>

			<p>

			<!--Edited By Mark 04-22-16  -->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Pharmacy Orders{{if $sCurrentArea}} (Current Area: {{$sCurrentArea}}){{else}}&nbsp;<span style="font-size:11px; font-weight:normal"><!-- (click on {{$sSetAreaLink}} to select default Area) --></span>{{/if}}</TD>
						</tr>
						<TR>
							<TD width="1%">{{$LDSegPharmaNewOrderIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDSegPharmaNewOrder}}</nobr></TD>
							<TD>Create new pharmacy request</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$LDSegPharmaOrderManageIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDSegPharmaOrderManage}}</nobr></TD>
							<TD>List of active pharmacy request</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$LDSegPharmaOrderServeIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDSegPharmaOrderServe}}</nobr></TD>
							<TD>Record served pharmacy requests</TD>
						</tr>
					<!-- 	{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$LDSegPharmaSetAreaIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDSegPharmaSetArea}}</nobr></TD>
							<TD>Set/change default pharmacy area</TD>
						</tr> -->
						{{include file="common/submenu_row_spacer.tpl"}}
				
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<!-- -######################################################################## -->
			

			<p>
			<!-- <TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>{{$LDICLab}}</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sICLabRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDICLabRequest}}</nobr></TD>
							<TD>Fill out request for industrial clinic laboratory service</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sICLabServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDICLabServicesRequest}}</nobr></TD>
							<TD>View, edit and delete industrial clinic laboratory service requests</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sICLabServicesOrderIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDICLabLabServicesOrder}}</nobr></TD>
							<TD>List of industrial clinic laboratory requests to be done</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sICLabServicesDoneIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDICLabServicesDone}}</nobr></TD>
							<TD>List of industrial clinic laboratory requests that have results</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
 -->
			<p>
			<!-- <TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>{{$LDAdministration}}</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sLabServicesAdminIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesAdmin}}</nobr></TD>
							<TD>Manage laboratory services options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sLabServicesGroupsIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesGroups}}</nobr></TD>
							<TD>Manage laboratory sections options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
					 -->	<!-- -->
						<!--added by Raissa 02-02-09-->
						<!-- <TR>
								<TD class="submenu_icon">{{$sLabTestsIcon}}</TD>
								<TD class="submenu_item" width=35%><nobr>{{$LDLabTests}}</nobr></TD>
								<TD class="submenu_text">Manage laboratory tests</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
								<TD align="center" class="submenu_icon">{{$sLabReagentsIcon}}</TD>
								<TD class="submenu_item"><nobr>{{$LDLabReagents}}</nobr></TD>
								<TD class="submenu_text">Manage laboratory reagents options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
								<TD class="submenu_icon">{{$sLabReagentsInventoryIcon}}</TD>
								<TD class="submenu_item"><nobr>{{$LDLabReagentsInventory}}</nobr></TD>
								<TD class="submenu_text">Manage laboratory reagents inventory</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD class="submenu_icon">{{$sLabServicesReportIcon}}</TD>
							<TD class="submenu_item"><nobr>{{$LDLabServicesReport}}</nobr></TD>
							<TD class="submenu_text">View and print specific status reports</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}


					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE> -->
			<!--Added by Borj 2014-08-04 ISO-->
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>{{$LDLabUserManual}}</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sLaboUserManualIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLaboUserManual}}</nobr></TD>
							<TD>PDF Copy of User's Manual</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
					

						
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<p>
			<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<p>
			</blockquote>