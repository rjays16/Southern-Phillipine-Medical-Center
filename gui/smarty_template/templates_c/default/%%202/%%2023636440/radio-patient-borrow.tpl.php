<?php /* Smarty version 2.6.0, created on 2020-02-12 16:50:41
         compiled from radiology/radio-patient-borrow.tpl */ ?>
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
	<table id="borrow-table" border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> <?php echo $this->_tpl_vars['sPanelHeaderBorrow']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel" width="20%"> <strong>Batch No.</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sBatchNr']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Borrower</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sBorrower']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Date Borrowed</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sDateBorrowed']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Time Borrowed</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sTimeBorrowed']; ?>
 </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Film Releaser</strong> </td>
			<td class="segPanel"> 
				<?php echo $this->_tpl_vars['sFilmReleaser']; ?>
 
				<?php echo $this->_tpl_vars['sNewFilmReleaser']; ?>

			</td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Remarks</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sRemarks']; ?>
 </td>
		</tr>
		<tr>
			<td colspan="2">
				<?php echo $this->_tpl_vars['sBorrowButton']; ?>

				<?php echo $this->_tpl_vars['sUpdateBorrowButton']; ?>
						
			</td>
		</tr>
		<tr id="headerReturned">
			<td class="segPanelHeader" align="left" colspan="2"> <?php echo $this->_tpl_vars['sPanelHeaderReturn']; ?>
 </td>
		</tr>
		<tr id="penaltyRow">
			<td class="segPanel" width="20%"> <strong>Penalty</strong> </td>
			<td class="segPanel"> Php <?php echo $this->_tpl_vars['sPenalty']; ?>
 </td>
		</tr>
		<tr id="dateReturned">
			<td class="segPanel" width="20%"> <strong>Date Returned</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sDateReturned']; ?>
 </td>
		</tr>
		<tr id="timeReturned">
			<td class="segPanel"> <strong>Time Returned</strong> </td>
			<td class="segPanel"> <?php echo $this->_tpl_vars['sTimeReturned']; ?>
 </td>
		</tr>
		<tr id="filmReceiver">
			<td class="segPanel"> <strong>Film Receiver</strong> </td>
			<td class="segPanel"> 
				<?php echo $this->_tpl_vars['sFilmReceiver']; ?>
 
				<?php echo $this->_tpl_vars['sNewFilmReceiver']; ?>

			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php echo $this->_tpl_vars['sReturnButton']; ?>

				<?php echo $this->_tpl_vars['sUpdateReturnButton']; ?>

			</td>
		</tr>
	</table>
		
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

	<hr id="doneButtonHr">
<?php echo $this->_tpl_vars['sDoneButton']; ?>

<?php echo $this->_tpl_vars['sIntialRequestList']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>


<?php if ($this->_tpl_vars['sRecordHistory']): ?>
<div align="center" style="width:100%;">
	<hr>
	<table class="segList" border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="6"> <?php echo $this->_tpl_vars['sPanelHeaderRecordHistory']; ?>
</td>
			</tr>
			<tr class="segPanel">
				<td align="center">Borrower's Name</td>
				<td align="center">Date Borrowed</td>
				<td align="center">Releaser's Name</td>
				<td align="center">Date Returned</td>
				<td align="center">Receiver's Name</td>
				<td align="center">Remarks</td>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->_tpl_vars['sRecordHistory']; ?>

		</tbody>
	</table>
</div>
<?php endif; ?>
<br><br><br>