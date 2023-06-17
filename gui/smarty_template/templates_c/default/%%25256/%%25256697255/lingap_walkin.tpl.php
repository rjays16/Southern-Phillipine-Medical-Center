<?php /* Smarty version 2.6.0, created on 2020-02-07 15:19:32
         compiled from sponsor/lingap_walkin.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>


<div style="width:660px; margin-top:10px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;">
		<tr>
			<td class="segPanelHeader">Walk-in information</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; padding:4px" >
					<tr>
						<td width="1" valign="top">
							<table style="font:normal 12px Arial;" cellpadding="0" cellspacing="0">
								<tr>
									<td><label>PID</label></td>
									<td><?php echo $this->_tpl_vars['sPatientID']; ?>
</td>
								</tr>
								<tr>
									<td>
										<label style="white-space:nowrap">Fullname</label>
									</td>
									<td nowrap="nowrap">
										<?php echo $this->_tpl_vars['sPatientEncNr']; ?>

										<?php echo $this->_tpl_vars['sPatientName']; ?>

										<?php echo $this->_tpl_vars['sSelectEnc']; ?>

										<?php echo $this->_tpl_vars['sClearEnc']; ?>

									</td>
								</tr>
							</table>
						</td>
						<td width="10"></td>
						<td width="*">
							<div style="white-space: nowrap">
								<label>Address</label>
							</div>
							<div style="white-space: nowrap">
								<?php echo $this->_tpl_vars['sAddress']; ?>

							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<div id="rqsearch" style="width:750px" align="center">
	<div style="margin:1px;">
		<div class="dashlet" style="margin-top:20px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">Lingap referrals (Walk-in only)</h1></td>
					<td align="right" width="*">
						<?php echo $this->_tpl_vars['sSelectService']; ?>

						<button id="find" class="segButton" onclick="return false;" disabled="disabled"><img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
gui/img/common/default/magnifier.png"/>Find</button>
						<button id="add-request" class="segButton" onclick="editRequest(); return false;" disabled="disabled"><img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
gui/img/common/default/folder_user.png"/>Add request</button>
					</td>
				</tr>
			</table>
		</div>
		<div>
<?php echo $this->_tpl_vars['lstRequest']; ?>

		</div>
	</div>
</div>



<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>


<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>