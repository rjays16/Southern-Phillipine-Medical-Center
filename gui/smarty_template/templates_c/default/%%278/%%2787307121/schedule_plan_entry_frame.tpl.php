<?php /* Smarty version 2.6.0, created on 2020-04-10 11:14:44
         compiled from radiology/schedule_plan_entry_frame.tpl */ ?>

<form name="schedule" id="schedule" <?php echo $this->_tpl_vars['sFormAction']; ?>
 method="post">
<!--
<div style="width:90%; overflow:hidden; border:1px solid red">
-->
	<table id="schedule-table" border="0" cellspacing="2" cellpadding="2" width="50%" align="left">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> <?php echo $this->_tpl_vars['sPanelHeaderSchedule']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel" width="40%"> <strong>Ref. No.</strong> </td>
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
 &nbsp; <?php echo $this->_tpl_vars['sSelectBatchNr']; ?>
 &nbsp; <?php echo $this->_tpl_vars['sClearBatchNr']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Patient Type</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sPatientType']; ?>
 </td>
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
 &nbsp;&nbsp; <?php echo $this->_tpl_vars['sResetSchedule']; ?>

			</td>
		</tr>
	</table>
<!--
</div>
-->
<br style="clear:left ">
<br style="clear:left ">
<br style="clear:left ">
<!--
<div align="center" style="width:90%; border:1px solid red">
-->
	<table border="0" id="scheduled-list" cellspacing="2" cellpadding="2" width="75%" align="left" class="segList">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="9"> <?php echo $this->_tpl_vars['sPanelHeaderScheduledForTheDay']; ?>
</td>
			</tr>
			<tr class="segPanel" style=" font-weight:bold;">
				<td align="center" width="2%">No.</td>
				<td align="center" width="8%">Ref. No.</td>
				<td align="center" width="12%">Time</td>
				<td align="center" width="15%">Service Code</td>
				<td align="center" width="8%">RID</td>
				<td align="center" width="*">Patient's Name</td>
				<td align="center" width="12%">Scheduled By</td>
				<td align="center" width="10%" colspan="2">Options</td>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->_tpl_vars['sScheduledForTheDay']; ?>

		
	</table>
<!--
</div>
-->


<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['sPresets']; ?>

</form>