<?php /* Smarty version 2.6.0, created on 2020-02-07 10:49:41
         compiled from sponsor/lingap_billing.tpl */ ?>
<div style="width:660px; margin-top:10px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
		<tr>
			<td class="segPanelHeader">Patient information</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="98%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; padding:4px" >
					<tr>
						<td width="1" align="left" valign="middle">
							<label>HRN:</label>
						</td>
						<td width="1" valign="middle">
							<?php echo $this->_tpl_vars['sPatientID']; ?>

						</td>
						<td></td>
						<td></td>
						<td valign="middle">
							<label>Patient type:</label><br/>
							<?php echo $this->_tpl_vars['sPatientEncType']; ?>

							<span id="encounter_type_show" style="font-weight:bold;color:#000080"><?php echo $this->_tpl_vars['sOrderEncTypeShow']; ?>
</span>
						</td>
						<td valign="middle">
							<div style="">
								<strong>Classification:</strong><br/>
								<span id="sw-class" style="font:bold 12px Arial;color:#006633"><?php echo $this->_tpl_vars['sSWClass']; ?>
</span>
							</div>
						</td>
					</tr>
					<tr>
						<td width="1" align="left" valign="top" style="white-space:nowrap">
							<label>Patient name:</label>
						</td>
						<td width="1" valign="middle">
							<?php echo $this->_tpl_vars['sPatientEncNr']; ?>

							<?php echo $this->_tpl_vars['sPatientName']; ?>

						</td>
						<td width="1" valign="middle">
							<?php echo $this->_tpl_vars['sSelectEnc']; ?>

						</td>
						<td valign="middle" width="80">
							<?php echo $this->_tpl_vars['sClearEnc']; ?>

						</td>
						<td align="center" colspan="2" valign="middle" nowrap="nowrap" style="display:none">
							<strong>Current balance</strong>
							<?php echo $this->_tpl_vars['sRunningBalance']; ?>

						</td>
						<td width="1" valign="middle" style="display:none">
							<?php echo $this->_tpl_vars['sAdjustBalance']; ?>

						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div style="width:98%; text-align:right; padding:2px 4px; display:none">
		<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/magnifier.png"/>View ledger</button>
	</div>
</div>

<div id="rqsearch" style="width:660px" align="center">
	<div style="margin:1px;">
		<div class="dashlet" style="margin-top:20px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">Billing statements</h1></td>
					<td align="right" width="*"></td>
				</tr>
			</table>
		</div>
		<div>
<?php echo $this->_tpl_vars['lstRequest']; ?>

		</div>
	</div>
</div>

<br/>
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>