<?php /* Smarty version 2.6.0, created on 2020-02-05 13:01:48
         compiled from radiology/form_report.tpl */ ?>

<?php echo $this->_tpl_vars['sFormStart']; ?>

	<div style="padding:10px;width:95%;border:0px solid black">
	
	<!-- <font class="prompt"><?php echo $this->_tpl_vars['sDeleteOK'];  echo $this->_tpl_vars['sSaveFeedBack']; ?>
</font> -->
	<font class="warnprompt"><?php echo $this->_tpl_vars['sMascotImg']; ?>
 <?php echo $this->_tpl_vars['sDeleteFailed']; ?>
 <?php echo $this->_tpl_vars['LDOrderNrExists']; ?>
 <br> <?php echo $this->_tpl_vars['sNoSave']; ?>
</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr>
				<td align="right" width="140"><b>Select Report Mode</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectType']; ?>
</td>
			</tr>
			<tr id="mode_status" style="display:none">
				<td colspan="2">
						<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
							<tbody class="submenu">
								<tr>
									<td align="right" width="140"><b>Per Transaction No.</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sViewGroup']; ?>
</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Select Report</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportSelect']; ?>
</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sPatientSelect']; ?>
</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Radiology Section</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup']; ?>
</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Rad. Resident Doctor</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportRadDoctor']; ?>
</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>From</b></td>
									<td  width="80%"><?php echo $this->_tpl_vars['sFromDateHidden'];  echo $this->_tpl_vars['sFromDateInput'];  echo $this->_tpl_vars['sFromDateIcon']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>To</b></td>
									<td  width="80%"><?php echo $this->_tpl_vars['sToDateHidden'];  echo $this->_tpl_vars['sToDateInput'];  echo $this->_tpl_vars['sToDateIcon']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
								</tr>
								<!--<tr id="shiftrow" style="display:none">-->
								<!--
								<tr id="shiftrow">
									<td align="right" width="140"><b>Shift Schedule</b></td>
									<td colspan="80%"><?php echo $this->_tpl_vars['sShift']; ?>
</td>
								</tr>
								-->
								<tr>
									<td align="right" width="140"><b>Classification</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportSelectClassification']; ?>
</td>
								</tr>
								<!--
								<tr>
									<td align="right" width="140"><b>Sorted By</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportOrder']; ?>
</td>
								</tr>
								<tr>
									<td align=right width=140><?php echo $this->_tpl_vars['LDReset']; ?>
</td>
									<td align=right><?php echo $this->_tpl_vars['sUpdateButton']; ?>
</td>
								</tr>
								-->
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sContinueButton']; ?>
&nbsp;&nbsp;<?php echo $this->_tpl_vars['sReportButton']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="mode_stat" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<!--
							<tr>
								<td align="right" width="140"><b>Select Report</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportSelect2']; ?>
</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Laboratory Section</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup2']; ?>
</td>
							</tr>
							-->
							<tr>
								<td align="right" width="140"><b>Select Report</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportSelectStat']; ?>
</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sFromDateHidden2'];  echo $this->_tpl_vars['sFromDateInput2'];  echo $this->_tpl_vars['sFromDateIcon2']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sToDateHidden2'];  echo $this->_tpl_vars['sToDateInput2'];  echo $this->_tpl_vars['sToDateIcon2']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
							<td align="right" width="140"><b>Export as</b></td>
								<!--<td width="80%"><input type="radio" name="exp_type" id="exp_pdf">PDF <input type="radio" name="exp_type" id="exp_excel">EXCEL</td>-->
								<td width="80%"><?php echo $this->_tpl_vars['sExportAsPdf']; ?>
PDF<?php echo $this->_tpl_vars['sExportAsExcel']; ?>
EXCEL</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sStatButton']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="mode_class" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr>
								<td align="right" width="140"><b>Select Classification</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sCases']; ?>
</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sFromDateHidden3'];  echo $this->_tpl_vars['sFromDateInput3'];  echo $this->_tpl_vars['sFromDateIcon3']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sToDateHidden3'];  echo $this->_tpl_vars['sToDateInput3'];  echo $this->_tpl_vars['sToDateIcon3']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sContinueButton2']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
            <tr id="mode_logbook" style="display:none">
                <td colspan="2">
                        <table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
                            <tbody class="submenu">
                                <tr>
                                    <td align="right" width="140"><b>Select Status</b></td>
                                    <td width="80%"><?php echo $this->_tpl_vars['sReportStatus']; ?>
</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Select Type</b></td>
                                    <td width="80%"><?php echo $this->_tpl_vars['sReportSelect3']; ?>
</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Select Patient Type</b></td>
                                    <td width="80%"><?php echo $this->_tpl_vars['sPatientSelect3']; ?>
</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Radiology Section</b></td>
                                    <td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup3']; ?>
</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Rad. Tech on Duty</b></td>
                                    <td width="80%"><?php echo $this->_tpl_vars['sReportRadTech2']; ?>
</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Rad. Resident Doctor</b></td>
                                    <td width="80%"><?php echo $this->_tpl_vars['sReportRadDoctor2']; ?>
</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>From</b></td>
                                    <td  width="80%"><?php echo $this->_tpl_vars['sFromDateHidden5'];  echo $this->_tpl_vars['sFromDateInput5'];  echo $this->_tpl_vars['sFromDateIcon5']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>To</b></td>
                                    <td  width="80%"><?php echo $this->_tpl_vars['sToDateHidden5'];  echo $this->_tpl_vars['sToDateInput5'];  echo $this->_tpl_vars['sToDateIcon5']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
                                </tr>
                                <tr id="orderby">
                                    <td align="right" width="140"><b>Alphabetical</b></td>
                                    <td colspan="80%"><?php echo $this->_tpl_vars['sOrderBy2']; ?>
</td>
                                </tr>
                        </tbody>
                    </table>
                    <br>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sLogbookButton']; ?>
</td>
                        </tr>
                    </table>
                </td>
            </tr>
			<tr id="mode_results" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<!--
							<tr>
								<td align="right" width="140"><b>Patient's Name</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sOrderEncID'];  echo $this->_tpl_vars['sOrderName']; ?>
&nbsp;<?php echo $this->_tpl_vars['sSelectPatient'];  echo $this->_tpl_vars['sClearEnc']; ?>
</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Address</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sOrderAddress']; ?>
</td>
							</tr>
							-->
							<tr>
								<td align="right" width="140"><b>Radiology Section</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup2']; ?>
</td>
							</tr>
							<tr>
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sPatientSelect2']; ?>
</td>
								</tr>
							<tr>
								<td align="right" width="140"><b>Rad. Tech on Duty</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportRadTech']; ?>
</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Date</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sFromDateInput4'];  echo $this->_tpl_vars['sFromDateIcon4']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr id="shiftrow">
								<td align="right" width="140"><b>Shift Schedule</b></td>
								<td colspan="80%"><?php echo $this->_tpl_vars['sShift']; ?>
</td>
							</tr>
							<tr>
							<!-- Added by Cherry 11-12-10 -->
							<tr id="filter_impression">
								<td align="right" width="140"><b>Filter by Impression</b></td>
								<td colspan="80%"><?php echo $this->_tpl_vars['sFilterImp']; ?>
</td>
							</tr>
							<tr id="show_impression">
								<td align="right" width="140"><b>Impression</b></td>
								<td colspan="80%"><?php echo $this->_tpl_vars['sImpression']; ?>
</td>
							</tr>
							<!-- End Cherry -->
							</tr>
							<tr id="orderby">
								<td align="right" width="140"><b>Alphabetical</b></td>
								<td colspan="80%"><?php echo $this->_tpl_vars['sOrderBy']; ?>
</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sResultsButton']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<?php echo $this->_tpl_vars['sHiddenInputs']; ?>


<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sTransactionDetailsControls']; ?>

<br/>
<!--
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sContinueButton']; ?>
</td>
		<td width="80%">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sStatButton']; ?>
</td>
	</tr>
</table>
</div>
-->

</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>