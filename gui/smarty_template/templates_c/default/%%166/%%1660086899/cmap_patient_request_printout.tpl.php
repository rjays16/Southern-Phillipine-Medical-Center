<?php /* Smarty version 2.6.0, created on 2020-02-05 12:17:57
         compiled from sponsor/cmap_patient_request_printout.tpl */ ?>
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
div#mainContent div, div#mainContent table {
	-moz-box-sizing: border-box;
}
</style>

<?php echo $this->_tpl_vars['sFormStart']; ?>

<div id="mainContent" style="width:98%">
	<div style="padding:4px;">
		<div class="" style="width:60%">
			<div id="basic" class="segPanel">
				<table width="100%" border="0" cellpadding="1" cellspacing="2" style="font:normal 12px Arial; margin:2px">
					<tr align="left">
						<td width="1" valign="top" style="white-space:nowrap">
							<label>View requests from</label>
						</td>
						<td>
<?php echo $this->_tpl_vars['sSources']; ?>

						</td>
					</tr>
					<tr id="row_dept" style="display:none">
						 <td valign="top" align="right">
							<label>Department</label>
						</td>
						<td><?php echo $this->_tpl_vars['sDepartment']; ?>
</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label>Date from</label>
						</td>
						<td>
<?php echo $this->_tpl_vars['sRequestFilterDateFrom']; ?>

						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label>Date to</label>
						</td>
						<td>
<?php echo $this->_tpl_vars['sRequestFilterDateTo']; ?>

						</td>
					</tr>
					<tr>
						<td align="left" colspan="2">
								<button class="segButton" onclick="search(); return false;"><img src="../../gui/img/common/default/magnifier.png" />Search</button>
								<button class="segButton" onclick="print(); return false;" value="1" disabled="disabled" id="print_button"><img src="../../gui/img/common/default/printer.png" />Print MAP</button>
								<!-- added by: syboy 12/31/2015 : meow -->
								<button class="segButton" onclick="print2(); return false;" value="0" disabled="disabled" id="print_button2"><img src="../../gui/img/common/default/printer.png" />Print PSCO/DSWD</button>
								<!-- ended syboy -->
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div>
		<div id="rqsearch" style="margin-top:10px; overflow:hidden" align="center">
			<div class="dashlet">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
					<tr>
						<td width="30%" valign="top"><h1 style="white-space:nowrap">Request list</h1></td>
						<td width="*" align="right" valign="top" nowrap="nowrap"></td>
					</tr>
				</table>
			</div>
			<div>
<?php echo $this->_tpl_vars['lstRequest']; ?>

			</div>
		</div>
		<div id="hidden-inputs" style="display:none">
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

	</div>
<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

</div>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>