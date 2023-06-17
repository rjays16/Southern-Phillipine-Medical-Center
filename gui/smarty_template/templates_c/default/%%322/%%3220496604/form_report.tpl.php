<?php /* Smarty version 2.6.0, created on 2020-02-10 10:09:00
         compiled from laboratory/form_report.tpl */ ?>

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
									<td align="right" width="140"><b>Laboratory Section</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup']; ?>
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
								<tr id="shiftrow">
									<td align="right" width="140"><b>Shift Schedule</b></td>
									<td colspan="80%"><?php echo $this->_tpl_vars['sShift']; ?>
</td>
								</tr>
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
							<!--Added by Cherry 04-21-09-->
							<tr id ="serv_grp">
								<td align="right" width="140"><b>Laboratory Section</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup3']; ?>
</td>
							</tr>

							<tr id="pat_type">
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sPatientSelect3']; ?>
</td>
								</tr>

							<tr id="charge_type">
									<td align="right" width="140"><b>Charge Type</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sChargeTypeSelect']; ?>
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
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sNscmButton'];  echo $this->_tpl_vars['sStatButton'];  echo $this->_tpl_vars['sIncomeButton'];  echo $this->_tpl_vars['sBloodBankButton']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
				<tr id="mode_forwarding" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr id = "pat_type">
								<td align="right" width="140"><b>Patient Type</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sForwadingPType']; ?>
</td>
							</tr>
							<tr id = "ward_name" style="display:none">
								<td align="right" width="140"><b>Ward/Station</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sWard']; ?>
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
							<tr id="shiftrow">
								<td align="right" width="140"><b>Time</b></td>
								<td colspan="80%"><?php echo $this->_tpl_vars['sShift2']; ?>
</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sForwardButton']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
			<!--Added by Cherry 04-29-09
			<tr id="mode_crossmatching" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
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
							<tr id="shiftrow">
								<td align="right" width="140"><b>Time</b></td>
								<td colspan="80%"><?php echo $this->_tpl_vars['sShift2']; ?>
</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sForwardButton']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
			 -->
			<tr id="mode_results" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr>
								<td align="right" width="140"><b>Laboratory Section</b></td>
								<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup2']; ?>
</td>
							</tr>
							<tr id = "pat_type">
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sPatientSelect2']; ?>
</td>
								</tr>
							<tr>
								<td align="right" width="140"><b>Date</b></td>
								<td  width="80%"><?php echo $this->_tpl_vars['sFromDateInput4'];  echo $this->_tpl_vars['sFromDateIcon4']; ?>
&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr id="shiftrow">
									<td align="right" width="140"><b>Shift Schedule</b></td>
									<td colspan="80%"><?php echo $this->_tpl_vars['sShift3']; ?>
</td>
								</tr>
							<tr>
									<td align="right" width="140"><b>Classification</b></td>
									<td width="80%"><?php echo $this->_tpl_vars['sReportSelectClassification2']; ?>
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
						<tr id="rpt_charges" style="display:none">
								<td colspan="2">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
														<td width="100%" align="center"><?php echo $this->_tpl_vars['sGenerate3Button']; ?>
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