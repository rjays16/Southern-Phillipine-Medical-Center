<?php /* Smarty version 2.6.0, created on 2020-06-23 07:46:06
         compiled from clinics/request_tray.tpl */ ?>
<div>
<?php echo $this->_tpl_vars['form_start']; ?>


<div style="width:90%; margin-top:10px" align="left">
	<table border="0" cellspacing="2" cellpadding="3" align="center" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*" colspan="2">Patient Details</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table  width="100%" class="transaction_details_table" cellpadding="0" cellspacing="0" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td align="left" width="30%" nowrap="nowrap"><strong>PID : </strong><?php echo $this->_tpl_vars['sPatientID']; ?>
</td>
							<td nowrap="nowrap"><strong>Name : </strong><?php echo $this->_tpl_vars['patient_name']; ?>
</td>
							<td width="30%" nowrap="nowrap"><strong>Patient Type : </strong><?php echo $this->_tpl_vars['encounter_type']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
	<table width="100%" cellpadding="0" cellspacing="0" style="font:12px Tahoma bold;">
		<tr>
			<td align="left" style="font:12px Arial bold;">
					<strong>TOTAL Charge: </strong>
					<span id="overall-total-charge" style="font:14px Arial bold; color:#ff0000">0.00</span>
			</td>
			<td align="right">

				<!-- Added By Mary ~ June 09, 2016 -->
				<!-- For Deletion Request Audit Trail -->

				<button class="segButton" id="deletionRequestTrailBtn" name="deletionRequestTrailBtn" onclick="viewDeletionRequestTrail();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/open.gif" border="0"/>Audit Trail</button>

				<!-- End by Mary -->

				<button class="segButton" id="viewRequestPrintoutBtn" name="viewRequestPrintoutBtn" onclick="viewRequestPrintout();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/printer.png" border="0"/>Request printout</button>
				<button class="segButton" id="viewRequestPrintoutBtn" name="viewRequestPrintoutBtn" onclick="viewChargeRequestPrintout();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/printer_add.png" border="0"/>Charge Request printout</button>
			</td>

		</tr>
		<tr>
			<td align="left" style="font:12px Arial bold;">
					<strong>TOTAL Cash: </strong>
					<span id="overall-total-cash" style="font:14px Arial bold; color:#ff0000">0.00</span>
			</td>
			<td align="right">
				<span id="show_seldate" class="segInput" style="color: rgb(0, 0, 192); padding: 0px 2px; width: 200px; height: 24px;"><?php echo $this->_tpl_vars['dateToday']; ?>
</span>
				<input id="seldate" type="hidden" name="seldate" value="<?php echo $this->_tpl_vars['dateTodayValue']; ?>
"/>
				<button class="segButton" id="tg_seldate" name="tg_seldate" onclick="return false;" style="cursor:pointer"><img src="../../gui/img/common/default/calendar.png" border="0"/>Date of Request</button>
				<img src="../../images/cashier_refresh.gif" border="0" onclick="requestByDate();" align="absmiddle" class="segSimulatedLink" title="Refresh!"/>
				<script type="text/javascript">
						Calendar.setup ({
								displayArea: "show_seldate",
								inputField : "seldate",
								ifFormat : "%Y-%m-%d",
								daFormat : "	%B %e, %Y",
								showsTime : false,
								button : "tg_seldate",
								singleClick : true,
								step : 1
						});
				</script>
			</td>
		</tr>
	</table>
	</div>
</div>

<div id="tabs" style="width:90%;margin-top:5px">
	<ul id="rtabs">
		<li><a href="#tab-laboratory">Laboratory</a></li>
		<?php if ($this->_tpl_vars['isIC']): ?>
			<li><a href="#tab-iclab">IC Laboratory</a></li>
		<?php endif; ?>
		<li><a href="#tab-bloodbank">Blood Bank</a></li>
		<li><a href="#tab-splab">Special Lab</a></li>
		<li><a href="#tab-radiology">Radiology</a></li>
		<li><a href="#tab-ip">Pharmacy</a></li>
		<!-- <li><a href="#tab-mg">Murang Gamot</a></li> -->
		<li><a href="#tab-miscellaneous">Miscellaneous</a></li>
                <li><a href="#tab-poc">Point of Care</a></li>
		<li><a href="#tab-obgyne">OB-GYN USD</a></li>

</ul>

	<div id="tab-laboratory">
			<div class="dashlet" style="margin-top:5px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
							<td align="right">
								<button class="segButton"  id="openLabRequestBtn" name="openLabRequestBtn"  onclick="openLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/flask.png" border="0"/>New request</button>
								<button class="segButton" onclick="openLabResults();return false;" style="cursor:pointer" <?php echo $this->_tpl_vars['disableRes']; ?>
><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
							</td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
							<td><span id="lab-total-charge">0.00</span></td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
							<td><span id="lab-total-cash">0.00</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="lab_requests" align="center">
			</div>
	</div>

 <?php if ($this->_tpl_vars['isIC']): ?>
	<div id="tab-iclab">
			<div class="dashlet" style="margin-top:5px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
							<td align="right">
								<button class="segButton"  id="openICLabRequestBtn" name="openICLabRequestBtn"  onclick="openICLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/flag_yellow.png" border="0"/>New request</button>
								<button class="segButton" onclick="openLabResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
							</td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
							<td><span id="iclab-total-charge">0.00</span></td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
							<td><span id="iclab-total-cash">0.00</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="iclab_requests" align="center">
			</div>
	</div>
	<?php endif; ?>

	<div id="tab-bloodbank">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openBloodRequestBtn" name="openBloodRequestBtn" onclick="openBloodRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/heart_add.png" border="0"/>New request</button>
							<button class="segButton" onclick="openBloodResults();return false;" style="cursor:pointer" <?php echo $this->_tpl_vars['disableRes']; ?>
><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="blood-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="blood-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="blood_requests" align="center">
		</div>
	</div>

	<div id="tab-splab">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openSpLabRequestBtn" name="openSpLabRequestBtn" onclick="openSpLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/folder_heart.png" border="0"/>New request</button>
							<!--<button class="segButton" onclick="openLabResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>-->
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="splab-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="splab-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="splab_requests" align="center">
		</div>
	</div>

	<div id="tab-radiology">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openRadioRequestBtn" name="openRadioRequestBtn" onclick="openRadioRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/film.png" border="0"/>New request</button>
							<button class="segButton" onclick="openRadioResults();return false;" style="cursor:pointer" <?php echo $this->_tpl_vars['disableRes']; ?>
><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="radio-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="radio-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="radio_requests" align="center">
		</div>
	</div>
	<div id="tab-obgyne">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openOBGyneRequestBtn" name="openOBGyneRequestBtn" onclick="openOBGYNERequest();return false;" style="cursor:pointer" <?php echo $this->_tpl_vars['btnDisableOB']; ?>
><img src="../../gui/img/common/default/film.png" border="0"/>New request</button>
							<button class="segButton" onclick="openOBGYNEResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="ob-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="ob-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="obgyne_requests" align="center">
		</div>
	</div>

	<div id="tab-ip">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<!-- <button class="segButton" id="openOutsidePackageBtn" name="openOutsidePackageBtn" onclick="openOutdieMedsModal();return false;" style="cursor:pointer" <?php echo $this->_tpl_vars['newMedsDisable']; ?>
><img src="../../gui/img/common/default/add.png" border="0"/>Add Outside Medicine</button> -->
							<button class="segButton" id="openPackageBtn" name="openPackageBtn" onclick="openPackageModal();return false;" style="cursor:pointer" <?php echo $this->_tpl_vars['packageDisable']; ?>
><img src="../../gui/img/common/default/add.png" border="0"/>Add Package</button>
							<button class="segButton" id="openPharmaRequestBtnIP" name="openPharmaRequestBtnIP" onclick="openPharmaRequest('<?php echo $this->_tpl_vars['defaultArea']; ?>
');return false;" style="cursor:pointer"><img src="../../gui/img/common/default/pill.png" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="ip-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="ip-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ip_requests" align="center">
		</div>
	</div>

<!-- 	<div id="tab-mg">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openPharmaRequestBtnMG" name="openPharmaRequestBtnMG" onclick="openPharmaRequest('MG');return false;" style="cursor:pointer"><img src="../../gui/img/common/default/pill_add.png" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="mg-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="mg-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="mg_requests" align="center">
		</div>
	</div> -->

	<div id="tab-miscellaneous">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openMiscellaneousRequestBtn" name="openMiscellaneousRequestBtn" onclick="openMiscellaneousRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/order.gif" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="misc-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="misc-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="misc_requests" align="center">
		</div>
	</div>

	<div id="tab-poc">
			<div class="dashlet" style="margin-top:5px">
				<table id="poc-totals" width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="15%" valign="top"><h1 style="white-space:nowrap">List of Orders</h1></td>
							<td align="right">
								<button class="segButton"  id="openPocOrderBtn" name="openPocOrderBtn"  onclick="openPocOrder();" style="cursor:pointer"><img src="../../gui/img/common/default/flask.png" border="0"/>New Order</button>
								<button class="segButton" onclick="viewCbgResult();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
							</td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
							<td><span id="poc-total-charge">0.00</span></td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
							<td><span id="poc-total-cash">0.00</span></td>
						</tr>
					</tbody>
				</table>
                                <br>
                                <table id="poc_orders" class="display table table-bordered">
                                    <thead>
                                    <tr>
                                        <th data-field="refno" data-align="center">Ref. #</th>
                                        <th data-field="order_dt">Date/Time/Orderer</th>
                                        <th data-field="status" data-align="center">Status</th>
                                        <th data-field="service_name">POC Test/Service</th>
                                        <th data-field="quantity" data-align="center">Quantity</th>
                                        <th data-field="unit_price" data-align="right">Unit Price</th>
                                        <th data-field="total" data-align="right">Total</th>
                                    </tr>
                                    </thead>
                                </table>                           
			</div>

                        <!-- POC Modal -->
                        <div class="modal fade" id="divModalDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content modal-xlg" id="divModalContent">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                ...
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                              </div>
                            </div>
</div>
                        </div>                                                                                                                

			<div id="poc_requests" align="center">
			</div>
	</div>

</div>

<?php echo $this->_tpl_vars['form_end']; ?>

<?php echo $this->_tpl_vars['ptype']; ?>

<?php echo $this->_tpl_vars['user_from']; ?>
 <!-- added by Christian 12-03-2019 -->
<?php echo $this->_tpl_vars['request_source']; ?>

<?php echo $this->_tpl_vars['is_bill_final']; ?>

<?php echo $this->_tpl_vars['is_bill_deleted']; ?>

<?php echo $this->_tpl_vars['encounter_nr']; ?>

<?php echo $this->_tpl_vars['ipbmextend']; ?>

<?php echo $this->_tpl_vars['isIc_hidden']; ?>

</div>