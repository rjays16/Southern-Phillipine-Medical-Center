<blockquote>
<TABLE cellSpacing=0 cellPadding=0 border=0 class="submenu_frame">
	<TBODY>
	<TR>
		<TD>
			<TABLE cellSpacing=1 cellPadding=3>
				<TBODY class="submenu">
					{{$LDPlugins}}
					{{include file='common/submenu_row_spacer.tpl'}}
                    <!--
                    {{$LDMiscDeptMngr}}
                    {{include file='common/submenu_row_spacer.tpl'}}
                    -->
					{{$ServicePrice}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$ServicePriceList}}
					{{include file='common/submenu_row_spacer.tpl'}}
				<!---commented out, as conferred with MLHE, 11-09-2007, fdp---
					{{$LDBilling}}
					{{include file='common/submenu_row_spacer.tpl'}}
				---------until here only--------------------------------fdp--->
				<!-- {{*$LDBillingMain*}}
					{{include file='common/submenu_row_spacer.tpl'}}
					 -->
					{{$LDCostCenterGuiMgr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDPersonellMngmnt}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDInsuranceCoMngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDAddressMngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDOccupationMngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDReligionMngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDEthnicMngr}}
                    {{include file='common/submenu_row_spacer.tpl'}}
                    {{$LDicd10Mngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDicpmMngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDicpmMngr2}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDPackageMngr}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$credColl}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$credColl2}}
					{{include file='common/submenu_row_spacer.tpl'}}
				<!---commented out temporarily, 04-01-2008, fdp--------------
					{{$LDPhotoLab}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDWebCam}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDSocialService}}
					{{include file='common/submenu_row_spacer.tpl'}}
				---------until here only--------------------------------fdp--->
				<!---commented out, as conferred with MLHE, 11-09-2007, fdp---
					{{$LDStandbyDuty}}
					{{include file='common/submenu_row_spacer.tpl'}}
				-----------------------until here only------------------fdp--->
					{{$LDCalendar}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDNews}}
					{{include file='common/submenu_row_spacer.tpl'}}
					{{$LDCalc}}
					{{include file='common/submenu_row_spacer.tpl'}}
				<!---commented out temporarily, 04-01-2008, fdp--------------
					{{if $bShowClock}}
						{{$LDClock}}
						{{include file='common/submenu_row_spacer.tpl'}}
					{{/if}}

					{{$LDUserConfigOpt}}
					{{include file='common/submenu_row_spacer.tpl'}}
				---------until here only--------------------------------fdp--->
					{{$LDAccessPw}}
					{{include file='common/submenu_row_spacer.tpl'}}

				<!---commented out temporarily, 04-01-2008, fdp--------------
					{{$LDNewsgroup}}

				---------until here only--------------------------------fdp--->
				{{$LDGenerateReports}}
					{{include file='common/submenu_row_spacer.tpl'}}
				{{$LDUserManualGuide}}
					{{include file='common/submenu_row_spacer.tpl'}}
				{{$LDSpecialTools}}
					{{include file='common/submenu_row_spacer.tpl'}}
				{{$LDReportGuide}}
					{{include file='common/submenu_row_spacer.tpl'}}
				</TBODY>
			</TABLE>
		</TD>
	</TR>

</TABLE>
<p>
<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
</blockquote>
