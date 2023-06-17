<?php /* Smarty version 2.6.0, created on 2020-02-05 12:20:30
         compiled from sponsor/lingap_patient_request.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'sponsor/lingap_patient_request.tpl', 125, false),)), $this); ?>

<style type="text/css">
.displayTotals {
	text-align:right;
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
}

.displayTotalsLink {
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
	cursor:pointer;
	color:#000066;
}

span.displayTotalsLink:hover {
	text-decoration:underline;
	color:#660000;
	background: #cccccc;
}
</style>

<?php echo $this->_tpl_vars['sFormStart']; ?>

<div style="width:700px; margin-top:5px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
		<tr>
			<td class="segPanelHeader">Lingap referral details</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial; margin:4px" >
					<tr>
						<td width="1" align="left" valign="middle" style="white-space:nowrap">
							<label>EP:</label>
						</td>
						<td width="1" valign="middle">
							<?php echo $this->_tpl_vars['sIsAdvance']; ?>

						</td>
						<td width="20"></td>
						<td style="white-space:nowrap">
							<label>Entry date:</label>
						</td>
						<td valign="top" nowrap="nowrap">
							<?php echo $this->_tpl_vars['sEntryDate']; ?>

						</td>


					</tr>
					<tr>
						<td width="1" align="left" valign="middle" style="white-space:nowrap">
							<label>Control no: <span class="required">*</span></label>
						</td>
						<td valign="middle">
							<?php echo $this->_tpl_vars['sControlNo']; ?>

						</td>
						<td></td>
						<td width="1" valign="top" style="white-space:nowrap" rowspan="3">
							<label>Notes:</label>
						</td>
						<td rowspan="3">
							<?php echo $this->_tpl_vars['sRemarks']; ?>

						</td>

					</tr>
					<tr>
						<td width="1" align="left" valign="middle" style="white-space:nowrap">
							<label>PID:</label>
						</td>
						<td width="1" valign="middle">
							<?php echo $this->_tpl_vars['sPatientID']; ?>

						</td>
						<td></td>


					</tr>
					<tr>
						<td width="1" align="left" valign="top" style="white-space:nowrap">
							<label>Name:</label>
						</td>
						<td width="1" valign="top">
							<?php echo $this->_tpl_vars['sPatientEncNr']; ?>

							<?php echo $this->_tpl_vars['sPatientName']; ?>

						</td>

					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div style="text-align:right; padding:2px">
		<?php echo $this->_tpl_vars['sContinueButton']; ?>

		<?php echo $this->_tpl_vars['sBreakButton']; ?>

	</div>
</div>
<div id="rqsearch" style="width:700px; margin-top:10px; overflow:hidden" align="center">
	<div style="width:80%; margin:1px; margin-right:5px; float:left">
		<div class="dashlet">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">Request list</h1></td>
					<td width="*" align="right" valign="top" nowrap="nowrap" style="display:none">Date<?php echo $this->_tpl_vars['sRequestFilterDate']; ?>
</td>
				</tr>
			</table>
		</div>
		<div id="requests"></div>
	</div>
	<div style="width:18%; margin:1px; float:left">
		<div class="dashlet">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma;">
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">Totals</h1></td>
				</tr>
			</table>
		</div>
		<div>
			<table width="100%" style="" border="0" cellspacing="2" cellpadding="1">
				<tbody>
					<tr>
						<td width="20%" align="left" class="segPanelHeader" ><strong>Total due</strong></td>
					</tr>
					<tr>
						<td style="background-color:#e0e0e0;margin:1px 10px;text-align:right"><span id="show-account"	class="displayTotals" style="color:#000000;" value="<?php echo $this->_tpl_vars['sAccountBalance']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['sAccountBalance'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
</span></td>
					</tr>
					<tr>
						<td width="20%" align="left" class="segPanelHeader" ><strong>Total covered</strong></td>
					</tr>
					<tr>
						<td style="background-color:#d0d0d0;margin:1px 10px;text-align:right"><span id="show-total"	class="displayTotals" style="color:#660000;" value="<?php echo $this->_tpl_vars['sCoverageTotal']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['sCoverageTotal'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
</span></td>
					</tr>
					<tr>
						<td width="20%" align="left" class="segPanelHeader"><strong>Remaining balance</strong></td>
					</tr>
					<tr>
						<td style="background-color:#c0c0c0;margin:1px 10px;text-align:right"><span id="show-balance" class="displayTotals" style="color:#000066" value="<?php echo $this->_tpl_vars['sAccountBalance']-$this->_tpl_vars['sCoverageTotal']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['sAccountBalance']-$this->_tpl_vars['sCoverageTotal'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : number_format($_tmp, 2)); ?>
</span></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div id="hidden-inputs" style="display:none">
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

</div>
<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>


<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>