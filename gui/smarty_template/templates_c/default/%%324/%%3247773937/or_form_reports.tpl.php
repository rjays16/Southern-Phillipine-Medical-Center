<?php /* Smarty version 2.6.0, created on 2020-02-11 11:28:06
         compiled from or/or_form_reports.tpl */ ?>

<?php echo $this->_tpl_vars['sFormStart']; ?>

	<div style="padding:10px;width:95%;border:0px solid black">
	
	<!-- <font class="prompt"><?php echo $this->_tpl_vars['sDeleteOK'];  echo $this->_tpl_vars['sSaveFeedBack']; ?>
</font> -->
	<font class="warnprompt"><?php echo $this->_tpl_vars['sMascotImg']; ?>
 <?php echo $this->_tpl_vars['sDeleteFailed']; ?>
 <?php echo $this->_tpl_vars['LDOrderNrExists']; ?>
 <br> <!--<?php echo $this->_tpl_vars['sNoSave']; ?>
--></font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr>
				<td align="right" width="140"><b>Select report</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelect']; ?>
</td>
			</tr>
			<!--<tr id="section" style="display:none">
				<td align="right" width="140"><b>Social Service Section</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectGroup']; ?>
</td>
			</tr>
			<tr id="social_worker" style="display:none">
				<td align="right" width="140"><b>Social Worker</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportEncoder']; ?>
</td>
			</tr>  -->
			<!-- Added by Cherry 08/03/10 -->
			<tr id="from2" style="display:none">
				<td align="right" width="140"><b>Date</b></td>
				<td><?php echo $this->_tpl_vars['sFromDateHidden2'];  echo $this->_tpl_vars['sFromDateInput2'];  echo $this->_tpl_vars['sFromDateIcon2']; ?>
</td>
			</tr>
			<tr id="prob_observation" style="display:none">
				<td align="right" width="140"><b>Human resource problems:</b></td>
				<td width="80%"><!--<?php echo $this->_tpl_vars['sObservation']; ?>
--><textarea cols="43" name="observe" id="observe"><?php echo $this->_tpl_vars['observe']; ?>
</textarea>  </td>
			</tr>
			<tr id="prob_material" style="display:none">
				<td align="right" width="140"><b>Materials/Equipment problems:</b></td>
				<!--<td width="80%"><?php echo $this->_tpl_vars['sMaterials']; ?>
</td>   -->
				<td width="80%"><textarea name="materials" cols="43" id="materials"><?php echo $this->_tpl_vars['materials']; ?>
</textarea></td>

			</tr>
			<tr id="prob_environment" style="display:none">
				<td align="right" width="140"><b>Physical Environment problems:</b></td>
				<!--<td width="80%"><?php echo $this->_tpl_vars['sEnvironment']; ?>
</td> -->
				<td width="80%"><textarea name="environment" cols="43" id="environment"><?php echo $this->_tpl_vars['environment']; ?>
</textarea></td>
			</tr>
			<tr id="prob_endorsement" style="display:none">
				<td align="right" width="140"><b>Special Endorsement:</b></td>
			<!--	<td width="80%"><?php echo $this->_tpl_vars['sEndorsement']; ?>
</td>  -->
			<td width="80%"><textarea name="endorsement" cols="43" id="endorsement"><?php echo $this->_tpl_vars['endorsement']; ?>
</textarea></td>
			</tr>
			<!-- End Cherry -->
			<tr id="from" style="display:none">
				<td align="right" width="140"><b>From</b></td>
				<td><?php echo $this->_tpl_vars['sFromDateHidden'];  echo $this->_tpl_vars['sFromDateInput'];  echo $this->_tpl_vars['sFromDateIcon']; ?>
</td>
			</tr>
			<tr id="to" style="display:none">
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
<table border="0" cellpadding="0" cellspacing="10">
	<tr>
		<td width="1%"><?php echo $this->_tpl_vars['sContinueButton']; ?>
</td>
		<td width="1%"><?php echo $this->_tpl_vars['sSaveButton']; ?>
</td>
	</tr>

</table>
</div>


</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['submitted']; ?>

<?php echo $this->_tpl_vars['show']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>