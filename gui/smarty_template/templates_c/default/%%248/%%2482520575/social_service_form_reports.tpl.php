<?php /* Smarty version 2.6.0, created on 2020-02-14 14:11:08
         compiled from social_service/social_service_form_reports.tpl */ ?>

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
				<td align="right" width="140"><b>Select report</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelect']; ?>
</td>
			</tr>
			<tr id="section" style="display:none">
				<td align="right" width="140"><b>Social Service Section</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup']; ?>
</td>
			</tr>
			<tr id="social_worker" style="display:none">
				<td align="right" width="140"><b>Social Worker</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportEncoder']; ?>
</td>
			</tr>
			<tr>
				<td align="right" width="140"><b>From</b></td>
				<td><?php echo $this->_tpl_vars['sFromDateHidden'];  echo $this->_tpl_vars['sFromDateInput'];  echo $this->_tpl_vars['sFromDateIcon']; ?>
</td>
			</tr>
			<tr>
				<td align="right" width="140"><b>To</b></td>
				<td><?php echo $this->_tpl_vars['sToDateHidden'];  echo $this->_tpl_vars['sToDateInput'];  echo $this->_tpl_vars['sToDateIcon']; ?>
</td>
			</tr>
			<!--<tr>
				<td align="right" width="140"><b>Classification</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectClassification']; ?>
</td>
			</tr>
			<tr>
				<td align=right width=140><?php echo $this->_tpl_vars['LDReset']; ?>
</td>
				<td align=right><?php echo $this->_tpl_vars['sUpdateButton']; ?>
</td>
			</tr>-->
		</tbody>
	</table>

	<?php echo $this->_tpl_vars['sHiddenInputs']; ?>


<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sTransactionDetailsControls']; ?>

<br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1%"><?php echo $this->_tpl_vars['sContinueButton']; ?>
</td>
	</tr>
</table>
</div>


</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>