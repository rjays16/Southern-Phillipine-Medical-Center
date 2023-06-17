<?php /* Smarty version 2.6.0, created on 2020-02-05 12:38:58
         compiled from order/return.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<div id="order_details" style="">
	<div align="center" style="width:90%">
        <strong id="warningcaption" style="white-space:nowrap; color:#FF0000"></strong>
		<table border="0" align="center" style="margin-bottom:2px" >
            <tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="*"><?php echo $this->_tpl_vars['sSelectArea'];  echo $this->_tpl_vars['sHiddenArea']; ?>
</td>
			</tr>
		</table>
		<table border="0" cellspacing="2" cellpadding="1" align="center" width="70%">
			<tbody>
				<tr>
					<td class="segPanelHeader" width="60%">Return information</td>
					<td class="segPanelHeader">Return date</td>
				</tr>
				<tr>
					<td class="segPanel" nowrap="nowrap" rowspan="3" valign="top" style="padding:5px">
						<table border="0" cellpadding="1" cellspacing="0" style="font:bold 12px Arial">
							<tr>
								<td width="60" align="right"><strong>Control no.</strong></td>
								<td>
									<?php echo $this->_tpl_vars['sReturnNr']; ?>

									<?php echo $this->_tpl_vars['sReturnNrReset']; ?>

								</td>
							</tr>
							<tr>
								<td align="right" valign="top"><strong>Name</strong></td>
								<td width="1" valign="middle">
									<?php echo $this->_tpl_vars['sReturnEncNr']; ?>

									<?php echo $this->_tpl_vars['sReturnEncID']; ?>

									<?php echo $this->_tpl_vars['sReturnDiscountID']; ?>

									<?php echo $this->_tpl_vars['sReturnDiscount']; ?>

									<?php echo $this->_tpl_vars['sReturnName']; ?>

								</td>
								<td width="1" valign="middle">
									<?php echo $this->_tpl_vars['sSelectEnc']; ?>

								</td>
								<td valign="middle" style="display:none">
									<?php echo $this->_tpl_vars['sClearEnc']; ?>

								</td>
							</tr>
							<tr>
								<td valign="top" align="right"><strong>Address</strong></td>
								<td colspan="3"><?php echo $this->_tpl_vars['sReturnAddress']; ?>
</td>
							</tr>
							<!--<?php if ($this->_tpl_vars['is_refund']): ?>
							<tr>
								<td align="right"><strong>Refund<br />amount</strong></td>
								<td>
									<?php echo $this->_tpl_vars['sRefundAmount']; ?>

								</td>
							</tr>
							<tr>
								<td align="right"><strong>Adjusted <br />amount</strong></td>
								<td>
									<div style="margin-bottom:2px">
										<?php echo $this->_tpl_vars['sCheckAdjust']; ?>

									</div>
									<?php echo $this->_tpl_vars['sAdjustAmount']; ?>

								</td>
							</tr>
							<?php endif; ?>-->
						</table>
					</td>
					<td class="segPanel" nowrap="nowrap" align="center">
						<?php echo $this->_tpl_vars['sReturnDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>

					</td>
				</tr>
				<tr>
					<td class="segPanelHeader">Notes</td>
				</tr>
				<tr>
					<td class="segPanel" style="padding:5px" align="center">
						<?php echo $this->_tpl_vars['sComments']; ?>

					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<table width="100%" border="0" cellpadding="2">
			<tr>
				<td width="50%" align="left">
					<?php echo $this->_tpl_vars['sAddItem'];  echo $this->_tpl_vars['sEmptyList']; ?>

				</td>
				<td width="*" align="right">
					<?php echo $this->_tpl_vars['sContinueButton'];  echo $this->_tpl_vars['sBreakButton']; ?>

				</td>
			</tr>
		</table>
		<table id="return-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%;">
			<thead>
				<tr id="return-list-header">
					<th width="10%" nowrap="nowrap" align="center">Ref No.</th>
					<th width="10%" nowrap="nowrap" align="center">Item No.</th>
					<th width="*" nowrap="nowrap" align="center">Item Description</th>
					<th width="10%" align="center" nowrap="nowrap">Qty</th>
					<th width="10%" align="center" nowrap="nowrap">Prev returns</th>
					<th width="10%" align="center" nowrap="nowrap">Price</th>
					<th width="10%" align="center" nowrap="nowrap">Returned</th>
					<th width="10%" align="center" nowrap="nowrap">Refundables</th>
					<th width="4%" nowrap="nowrap"></th>
				</tr>
			</thead>
			<tbody>
<?php echo $this->_tpl_vars['sReturnItems']; ?>

			</tbody>
		</table>
	</div>
</div>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<br/>
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>