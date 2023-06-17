		<BLOCKQUOTE>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
<tr>
	<!-- <TD class="submenu_title" colspan=3>Pharmacy Orders{{if $sCurrentArea}} (Current Area: {{$sCurrentArea}}){{else}}&nbsp;<span style="font-size:11px; font-weight:normal">(click on {{$sSetAreaLink}} to select default Area)</span>{{/if}}</TD> -->
<TD class="submenu_title" colspan=3>Pharmacy Orders<span style="font-size:11px; font-weight:normal"> <a href="#" onclick="myArea();">(click to select or change default Area)</a> </span>{{$sCurrentAreaSelected}}</TD>
<div id="search-dialog" style="display: none;">
	<iframe id="search-dialog-frame" src="" style="height:100%;width:100%;border:none;">
	</iframe>
</div>
</tr>
{{$LDSegPharmaNewOrder}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaOrderManage}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaOrderServe}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaSetArea}}
{{include file="common/submenu_row_footer.tpl"}}
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
	<TD class="submenu_title" colspan=3>Ward Stocks</TD>
</tr>
{{$LDSegPharmaNewStock}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaRecentStocks}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaStocksManage}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaWardManage}}
{{include file="common/submenu_row_footer.tpl"}}
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
	<TD class="submenu_title" colspan=3>Pharmacy Returns</TD>
</tr>
{{$LDSegPharmaNewReturn}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaNewRefund}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaReturnList}}
{{include file="common/submenu_row_footer.tpl"}}
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
{{$LDPInviMngr}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDPharmaDb}}
{{include file="common/submenu_row_spacer.tpl"}}
<!-- {{$LDDocSearch}}
{{include file="common/submenu_row_footer.tpl"}} -->
{{$LDPharmaReports}}
{{include file="common/submenu_row_spacer.tpl"}}
<!--Added by Borj 2014-08-04 ISO-->
{{$LDPharmaUserManualReports}}
{{include file="common/submenu_row_footer.tpl"}}
<!--Added by Darryl-->
{{$LDBillingReports_jasper}}
{{include file="common/submenu_row_footer.tpl"}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<BR/>
			<A href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<BR/>
			</BLOCKQUOTE>
