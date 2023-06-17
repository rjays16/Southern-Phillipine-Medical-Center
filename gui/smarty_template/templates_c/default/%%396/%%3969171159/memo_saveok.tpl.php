<?php /* Smarty version 2.6.0, created on 2020-02-06 10:02:37
         compiled from cashier/memo_saveok.tpl */ ?>
<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>

<br/>
<div align="center">
	<table width="70%" border="0" cellpadding="0" cellspacing="0" class="jedDialog">
		<thead>
			<tr>
				<th width="*"><?php echo $this->_tpl_vars['sMessageHeader']; ?>
</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="center">
					<div align="left" style="width:95%;padding:0;margin:0">
						<?php echo $this->_tpl_vars['sPrintButton']; ?>

						<?php echo $this->_tpl_vars['sBreakButton']; ?>

					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<style type="text/css" media="all">
						.detailstb tr td {
						}

						.detailstb tr td span {
							font:bold 11px Tahoma;
							color:#00006d;
						}
					</style>
					<table class="detailstb" align="center" width="95%" border="1" cellpadding="2" cellspacing="0" style="border:1px solid #cad3e8;border-collapse:collapse; font:bold 12px Arial">
						<tr>
							<td width="20%"><b>Memo Nr.</b></td>
							<td><span><?php echo $this->_tpl_vars['sMemoNr']; ?>
</span></td>
						</tr>
						<tr>
							<td><b>Issue date</b></td>
							<td><span><?php echo $this->_tpl_vars['sIssueDate']; ?>
</span></td>
						</tr>
						<tr>
							<td><b>Name</b></td>
							<td><span><?php echo $this->_tpl_vars['sMemoName']; ?>
</span></td>
						</tr>
						<tr>
							<td><b>Address</b></td>
							<td><span><?php echo $this->_tpl_vars['sMemoAddress']; ?>
</span></td>
						</tr>
						<tr>
							<td><b>Notes</b></td>
							<td><span><?php echo $this->_tpl_vars['sRemarks']; ?>
</span></td>
						</tr>
						<tr>
							<td><b>Items</b></td>
							<td align="left">
								<table border="0" width="100%" cellpadding="1" cellspacing="1" style="margin:4px;border:1px solid #006699">
									<tbody>
										<tr>
											<td width="15%" class="jedPanelHeader">OR No.</td>
											<td width="15%" class="jedPanelHeader">Source</td>
											<td width="15%" class="jedPanelHeader">Code</td>
											<td width="45%" class="jedPanelHeader">Particulars</td>
											<td width="12%" class="jedPanelHeader" align="center">Price</td>
											<td width="12%" class="jedPanelHeader" align="center">Qty</td>
											<td width="15%" class="jedPanelHeader" align="center">Total</td>
										</tr>
										<?php echo $this->_tpl_vars['sItems']; ?>

									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center"></td>
			</tr>
		</tbody>
	</table>
</div>
<br>
<br>