			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY >
									<tr>
										<TD class="submenu_title" colspan=3>Patient Services</TD>
									</tr>
									{{$LDRegNewBorn}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSearch}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDAdvSearch}}
									<!-- added by Macoy June 23, 2014 -->
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDOnlineConsultation}}
									{{include file="common/submenu_row_spacer.tpl"}}
									<!-- //////////////////////////// -->
									<!--{{include file="common/submenu_row_spacer.tpl"}}-->
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<BR/>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
									<tr>
										<TD class="submenu_title" colspan=3>Administration</TD>
									</tr>
									{{$LDGenerateOPDReport}}
                                    {{include file="common/submenu_row_spacer.tpl"}}
                                    {{$LDDocSearch}}
                                    {{include file="common/submenu_row_spacer.tpl"}}
                                    {{$LDGenerateReport}}
                                     {{include file="common/submenu_row_spacer.tpl"}}
                                    {{$LDManual}}
									{{include file="common/submenu_row_footer.tpl"}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<!--
			 <BR/>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
									<tr>
										<TD class="submenu_title" colspan=3>Supplies</TD>
									</tr>
									{{$LDSegSupplyRequest}}
									{{include file="common/submenu_row_footer.tpl"}}
									{{$LDRequestsHistory}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegSupplyAcknowledge}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			-->
			<BR/>
			<A href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<BR/>

