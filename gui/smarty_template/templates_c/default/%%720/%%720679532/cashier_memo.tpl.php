<?php /* Smarty version 2.6.0, created on 2020-02-05 14:12:03
         compiled from cashier/cashier_memo.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<style type="text/css">
.tabFrame {
	padding:5px;
}
</style>

<?php echo $this->_tpl_vars['sFormStart']; ?>

<div style="width:75%">
	<div class="segPanel" style="padding:1px; width:100%">
		<div id="tab0" class="tabFrame" style="display:block" >
			<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
				<tbody>
					<tr>
						<td width="35%" valign="top">
							<table border="0" cellspacing="1" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
								<tr valign="top">
									<td nowrap="nowrap" align="right"><strong>Memo Nr</strong></td>
									<td nowrap="nowrap">
										<?php echo $this->_tpl_vars['sMemoNr']; ?>

										<?php echo $this->_tpl_vars['sResetNr']; ?>

									</td>
								</tr>
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right"><strong>Name</strong></td>
									<td align="left" valign="middle" nowrap="nowrap">
										<?php echo $this->_tpl_vars['sMemoEncNr']; ?>

										<?php echo $this->_tpl_vars['sMemoEncID']; ?>

										<?php echo $this->_tpl_vars['sMemoDiscountID']; ?>

										<?php echo $this->_tpl_vars['sMemoDiscount']; ?>

										<?php echo $this->_tpl_vars['sMemoName']; ?>

									</td>
									<td><?php echo $this->_tpl_vars['sSelectEnc']; ?>
</td>
									<td><?php echo $this->_tpl_vars['sClearEnc']; ?>
</td>
								</tr>
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right" rowspan="2"><strong>Address</strong></td>
									<td align="left" valign="middle" colspan="3">
										<?php echo $this->_tpl_vars['sMemoAddress']; ?>

									</td>
								</tr>
								<tr valign="top">
									<td align="left" valign="middle" colspan="4">
										<?php echo $this->_tpl_vars['sSWClass']; ?>

									</td>
								</tr>
							</table>
						</td>
						<td width="*" valign="top">
							<table border="0" cellspacing="2" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right"><strong>Date</strong></td>
									<td width="*" align="left" valign="middle">
										<?php echo $this->_tpl_vars['sIssueDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>

									</td>
								</tr>
								<tr valign="top">
									<td nowrap="nowrap" align="right"><strong>Notes</strong></td>
									<td>
										<?php echo $this->_tpl_vars['sRemarks']; ?>

									</td>
								</tr>
								<tr valign="middle">
									<td align="right"><strong>Assign to</strong></td>
									<td colspan="4">
										<?php echo $this->_tpl_vars['sPersonnel']; ?>

									</td>
								</tr>
								<tr valign="middle">
									<td nowrap="nowrap" align="right"><strong>Total refund</strong></td>
									<td>
										<?php echo $this->_tpl_vars['sTotalRefund']; ?>

									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div style="width:90%;" align="center">
	<div id="" style="padding:2px;margin-top:3px;">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="50%" align="left"><?php echo $this->_tpl_vars['sMemoAdd'];  echo $this->_tpl_vars['sMemoClearAll']; ?>
</td>
				<td align="right"><?php echo $this->_tpl_vars['sContinueButton'];  echo $this->_tpl_vars['sBreakButton']; ?>
</td>
			</tr>
		</table>
		<table id="memo-list" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-top:5px">
			<thead>
				<tr id="">
					<th align="center" width="8%" nowrap="nowrap">OR No.</th>
					<th align="center" width="8%" nowrap="nowrap">Source</th>
					<th align="center" width="8%" nowrap="nowrap">Req No</th>
					<th align="center" width="8%" nowrap="nowrap">Code</th>
					<th align="center" width="*" nowrap="nowrap">Item description</th>
					<th align="center" width="9%" nowrap="nowrap">Quantity</th>
					<th align="center" width="9%" nowrap="nowrap">Previous</th>
					<th align="center" width="9%" nowrap="nowrap" style="font:bold 11px Tahoma">Price/item</th>
					<th align="center" width="9%" nowrap="nowrap">Refund</th>
					<th align="center" width="9%" nowrap="nowrap" >Total</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
<?php echo $this->_tpl_vars['sMemoList']; ?>

			</tbody>
		</table>
	</div>
</div>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	