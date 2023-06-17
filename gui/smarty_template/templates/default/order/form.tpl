{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<script type="text/javascript">
function openWindow(url) {
	window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}
</script>
<span id="ajax_display"></span>
{{$sFormStart}}
	<div style="width:1033px" align="center">
        <span>{{$sWarning}}</span>
		<!-- <table border="0" align="center" style="margin-bottom:2px" >
			<tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="*">{{$sSelectArea}}</td>
			</tr>
		</table> -->
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
			<tbody>
				<tr>
					<td class="segPanelHeader" width="*">
						Request Details
					</td>
					<td class="segPanelHeader" width="170">
						Reference No.
					</td>
					<td class="segPanelHeader" width="215">
						Request Date
					</td>
				</tr>
				<tr>
					<td rowspan="3" class="segPanel" align="left" valign="top">
						<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
							<tr height="22">
								<td align="right">Type:</td>
								<td valign="top" colspan="3">
									{{$sIsCash}}
									{{$sIsCharge}}
									{{$sChargeType}}
									<span style="display:none">{{$sIsTPL}}</span>
								</td>
							</tr>

							<tr>
								<td align="right" valign="top"><strong>Name:</strong></td>
								<td width="1" valign="middle">
									{{$sOrderEncNr}}
									{{$sOrderEncID}}
									{{$sOrderDiscountID}}
									{{$sOrderDiscount}}
									{{$sOrderName}}
								</td>
								<td width="1" valign="middle">
									{{$sSelectEnc}}
								</td>
								<td valign="middle">
									{{$sClearEnc}}
								</td>
							</tr>
							<tr>
								<td align="right" valign="top"><strong>Address:</strong></td>
								<td colspan="3">{{$sOrderAddress}}</td>
							</tr>
							<tr>
								<td></td>
								<td valign="top" colspan="3">
									<strong>Patient type:</strong>
									{{$sOrderEncType}}
									<span id="encounter_type_show" style="font-weight:bold;color:#000080">{{$sOrderEncTypeShow}}</span>
								</td>
							</tr>
							<tr>
								<td></td>
								<td valign="top" colspan="3">
									<strong>HRN:</strong>	<!-- edited by julus 01-06-2017 -->
									<span id="hrn_id" style="font-weight:bold;color:#000080">{{$sPID}}</span>
									
								</td>
							</tr>
							<tr>
								<td></td>
								<td valign="top" colspan="3">
									<strong>Location:</strong> <!-- edited by julus 01-06-2017 -->
									<span id="current_loc" style="font-weight:bold;color:#000080">{{$sLocation}}</span>
								</td>
							</tr>

							<tr>
								<td></td>
								<td valign="top" colspan="3">
									PHIC no:
									<span id="phic_nr" style="font-weight:bold;color:#000080">{{$sPhicNo}}</span>
								</td>
							</tr>
                            <tr>
                                <td></td>
                                <td valign="top" colspan="3">
                                    Category:
                                    <span id="mem_category" style="font-weight:bold;color:#000080">{{$sMemCategory}}</span>
                                </td>
                            </tr>
						</table>
					</td>
					<td class="segPanel" align="center" nowrap="nowrap">
						{{$sRefNo}}
						{{$sResetRefNo}}
					</td>
					<td class="segPanel" align="center" valign="middle" nowrap="nowrap">
						{{$sOrderDate}}{{$sCalendarIcon}}
					</td>
				</tr>
				<tr>
					<td class="segPanelHeader">Discounts</td>
					<td class="segPanelHeader">Request options</td>
				</tr>
				<tr>
					<td class="segPanel" align="center">
						<table style="font:bold 12px Arial">
							<tr>
{{if $ssView}}
{{else}}
								<td valign="middle">
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial;color:#006633">{{$sSWClass}}</span></div>
									<div style="margin-top:5px; vertical-align:middle; ">{{$sDiscountShow}}</div>
								</td>
{{/if}}
							</tr>
						</table>
						{{$sBtnDiscounts}}
					</td>
					<td class="segPanel" align="center" style="padding-bottom:5px;">
						<table border="0" cellpadding"0" cellspacing="0" style="font:normal 11.5px Arial;">
							<tr>
								<td align="right">
									<strong>Priority</strong>
								</td>
								<td>
									{{$sNormalPriority}}
									{{$sUrgentPriority}}
								</td>
							</tr>
							<tr>
								<td align="right" valign="top">
									<strong>Notes</strong>
								</td>
								<td>
									{{$sComments}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="width:1033px" align="center">
		<table width="100%">
			<tr>
				<td nowrap="nowrap" width="30%" align="left">
					{{$sBtnAddItem}}
					{{$sBtnEmptyList}}
					{{$sBtnCoverage}}
				</td>
				<td nowrap="nowrap" width="20%">
					<input id="coverage" type="hidden" value="-1" />
					<input id="phic_coverage" type="hidden" value="-1" />
					<span id="cov_type" style="font:bold 12px Tahoma"></span>
					<span id="cov_amount" style="font:bold 12px Tahoma;color:#000044"></span>

					<span style="font:bold 12px Tahoma; display:none">PHIC Coverage:</span>
					<span id="phic_cov" style="font:bold 12px Tahoma; color:#000044; display:none"></span>
					<img id="phic_ajax" src="images/ajax_spinner.gif" border="0" title="Loading..." style="display:none" />
				</td>
				<td align="right">
					{{$sContinueButton}}
					{{$sBreakButton}}
				</td>
			</tr>
		</table>
		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="1033px;">
			<thead>
				<tr id="order-list-header">
					<th width="8.3%%" nowrap="nowrap"></th>
					<th width="8.3%" nowrap="nowrap" class="centerAlign">Item No.</th>
					<th width="8.3%" nowrap="nowrap" class="leftAlign">Item Description</th>
					<th width="8.3%" nowrap="nowrap" class="leftAlign">Area</th>
					<th width="8.3%" nowrap="nowrap" class="centerAlign">Consigned</th>
					<th width="8.3%" class="centerAlign" nowrap="nowrap">Quantity</th>
					<th width="8.3%" nowrap="nowrap" class="centerAlign">Dosage</th>
					<th width="8.3%" nowrap="nowrap" class="centerAlign">Frequency</th>
					<th width="8.3%" nowrap="nowrap" class="centerAlign">Route</th>
					{{$addDispensedQtyColumn}}
					<th width="8.3%" class="rightAlign" nowrap="nowrap">Price(Orig)</th>
					<th width="8.3%" class="rightAlign" nowrap="nowrap">Price(Adj)</th>
					<th width="8.3%" class="rightAlign" nowrap="nowrap">Total</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			</tbody>
		</table>

		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong></th>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
			</tbody>
		</table>
	</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<div style="width:80%">
{{$sUpdateControlsHorizRule}}
{{$sUpdateOrder}}
{{$sCancelUpdate}}
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}

<div id="search-dialog" style="display: none;">
	<iframe id="search-dialog-frame" src="" style="height:100%;width:100%;border:none;">
	</iframe>
</div>

<div id="Loading-check-dialog" style="display: none;">
<span id="ajax_display2"></span><br>
<small id="tryAgain" onclick="tryAgain();" style="display: none; text-decoration: underline;cursor: pointer;">Try again?</small>
{{$DialogDAI}}
</div>

