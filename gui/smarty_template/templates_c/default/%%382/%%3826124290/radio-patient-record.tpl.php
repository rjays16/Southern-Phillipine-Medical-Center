<?php /* Smarty version 2.6.0, created on 2020-02-12 16:48:36
         compiled from radiology/radio-patient-record.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>


	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" align="left" colspan="2"> <?php echo $this->_tpl_vars['sPanelHeader']; ?>
 </td>
			</tr>
			<tr>
				<td class="segPanel" width="10%"> <strong>HRN</strong> </td>
				<td class="segPanel"> <?php echo $this->_tpl_vars['sPID']; ?>
 </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>RID</strong> </td>
				<td class="segPanel"> <?php echo $this->_tpl_vars['sRID']; ?>
 </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Name</strong> </td>
				<td class="segPanel"> <?php echo $this->_tpl_vars['sName']; ?>
 </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Birthdate</strong> </td>
				<td class="segPanel"> <?php echo $this->_tpl_vars['sBirthdate']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sAge']; ?>
 </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Gender</strong> </td>
				<td class="segPanel"> <?php echo $this->_tpl_vars['sGender']; ?>
 </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Address</strong> </td>
				<td class="segPanel"> <?php echo $this->_tpl_vars['sAddress']; ?>
 </td>
			</tr>
		</tbody>
	</table>
<br>
	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tr>
			<td>
				<?php echo $this->_tpl_vars['sSearchInput']; ?>

			</td>
		</tr>
	</table>
<br>
	<?php echo $this->_tpl_vars['sTabRadiology']; ?>

<br>
<?php echo $this->_tpl_vars['sAvailabilityNotes']; ?>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sIntialRequestList']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br>
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	