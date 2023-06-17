<?php /* Smarty version 2.6.0, created on 2020-02-06 09:36:45
         compiled from radiology/schedule_form_frame.tpl */ ?>

<form name="schedule-form" id="schedule-form" <?php echo $this->_tpl_vars['sFormAction']; ?>
 method="post">
<!--
<div style="width:90%; overflow:hidden; border:1px solid red">
-->
	<table id="schedule-table" border="0" cellspacing="2" cellpadding="2" width="100%" align="left">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> <?php echo $this->_tpl_vars['sPanelHeaderSchedule']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel" width="25%"> <strong>Ref. No.</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sBatchNr']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Service Code</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sServiceCode']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Name</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sPatientName']; ?>
 &nbsp; </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Date Scheduled</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sDateScheduled']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Time Scheduled</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sTimeScheduled']; ?>
 </td>
		</tr>
		<?php if (sServiceDate): ?>
		<tr>
			<td class="segPanel"> <strong>Date of Service</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sServiceDate']; ?>
 </td>
		</tr>
		<?php endif; ?>
		<tr>
			<td class="segPanel"> <strong>Please bring the following</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sInstructions']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Instructions</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sRemarks']; ?>
 </td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<?php echo $this->_tpl_vars['sScheduleButton']; ?>
 &nbsp;&nbsp; <?php echo $this->_tpl_vars['sPrintButton']; ?>
 
			</td>
		</tr>
	</table>
<!--
</div>
-->
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['sPresets']; ?>

</form>