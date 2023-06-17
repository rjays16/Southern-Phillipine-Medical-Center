{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}
	<table>
		<tr>
			<td>{{$sAddNewRequest}}</td>
		</tr>
	</table>
	<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
				<td class="segPanelHeader" width="15%">
					<!--Reference No.-->
					Batch No.
				</td>
				<td class="segPanelHeader" width="20%">
					Request Date
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >

						<tr>
							<td><strong>Transaction type</strong>
							&nbsp;&nbsp;&nbsp;
								{{$sIsCash}}
								{{$sIsCharge}}<span id="type_charge" style="display:none">{{$sChargeTyp}}</span>
								<!--&nbsp;&nbsp;&nbsp;{{$sIsTPL}}-->
							</td>

						</tr>

					</table>
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td align="right" width="1"><strong>HRN</strong></td>
							<td colspan="3" valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;">{{$sPatientHRN}}</span></td>
						</tr>
						<tr>
							<td align="right" width="1" valign="top"><strong>Name</strong></td>
							<td width="1" valign="middle">
								{{$sOrderEncID}}
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
							<td valign="top"><strong>Address</strong></td>
							<td colspan="3">{{$sOrderAddress}}</td>
						</tr>
						<!--
						<tr>
							<td valign="top" colspan="4">
								<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial; color:#0000FF;">{{$sClassification}}</span></div>
							</td>
						</tr>
						-->
						<!-- added by VAN 06-02-08 -->
					<td colspan="4">
						 <table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td valign="top" width="20%">
								<strong>From RDU?</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="*">
								<span id="rdu" style="font:bold 10px Arial; color:#0000FF;">{{$sRDU}}</span>
							</td>
							<td colspan="3">
								<table border="0" width="100%">
									<tr>
										<td valign="top" width="20%">
											<strong>Walkin?</strong>
										</td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" width="30%">
											<span id="walkin" style="font:bold 10px Arial; color:#0000FF;">{{$sWalkin}}</span>
										</td>
										<td valign="top" width="10%">
											<strong>PE?</strong>
										</td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<!--<td valign="top" width="20%" onmouseover="alert('PE is for Personnel Only')"> -->
										<td valign="top" width="20%">
											<span id="pe" style="font:bold 10px Arial; color:#0000FF;">{{$sPE}}</span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td valign="top" width="20%">
								<strong>Patient Type</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="*">
								<span id="patient_enctype" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientType}}</span>
							</td>
							<td valign="top" width="10%">
								<strong>Sex</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="20%">
								<span id="sex" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientSex}}</span>
							</td>

						</tr>
						<tr>
							<td valign="top" width="20%">
								<strong>Birth Date</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="*">
								<span id="dob" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientBdate}}</span>
							</td>
							<td valign="top" width="5%">
								<strong>Age</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="20%">
								<span id="age" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientAge}}</span>
							</td>
						</tr>
						<tr>
							<td valign="top" width="20%">
								<strong>Location/Clinic</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" colspan="4">
								<span id="patient_location" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientLoc}}</span>
							</td>
						</tr>
						<tr>
							<td valign="top"  width="20%">
								<strong>Medico Legal</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" colspan="4">
								<span id="patient_medico_legal" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientMedicoLegal}}</span>
							</td>
						</tr>
						<tr>
							<td valign="top"  width="20%">
								<strong>Diagnosis</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" colspan="4">
								<span id="adm_diagnosis" style="font:bold 10px Arial; color:#0000FF;">{{$sAdmDiagnosis}}</span>
							</td>
						</tr>
						<tr>
							<td valign="top" width="20%">
								<strong>Adm. Date</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="30%">
								<span id="admission_date" style="font:bold 10px Arial; color:#0000FF;">{{$sAdmissionDate}}</span>
							</td>
							<td valign="top" width="5%">
								<strong>Disc. Date</strong>
							</td>
							<td valign="top" align="left" width="1%">
								<strong>:</strong>
							</td>
							<td valign="top" width="30%">
								<span id="discharged_date" style="font:bold 10px Arial; color:#0000FF;">{{$sDischargedDate}}</span>
							</td>
						</tr>
						</table>
					 </td>
						<!-- ---->

						<!-- added by VAN 01-11-08-->
						<tr>
							<td valign="top" colspan="4">
								<table>
									<tr>
										<td valign="top" align="left" width="20%"><strong>Repeat Request</strong></td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" colspan="2">{{$sRepeat}}</td>
									</tr>
									<tr id="repeatinfo01">
										<td valign="top" align="left" width="20%"><strong>Previous Refno</strong></td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" colspan="2">{{$sParentRefno}}</td>
									</tr>
									<tr id="repeatinfo02">
										<!--<td valign="top" align="right"><strong>Remarks</strong></td>
										<td valign="top" colspan="3">{{$sRemarks}}</td>-->
										<td valign="top" align="left" width="20%"><strong>Previous Refno</strong></td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" colspan="2">{{$sParentRefno}}</td>
									</tr>
									<tr id="repeatinfo03">
										<td valign="top" align="left" width="20%"><strong>Approved By</strong></td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" colspan="2">{{$sHead}}</td>
									</tr>
									<tr id="repeatinfo04">
										<td valign="top" align="left" width="20%"><strong>User ID</strong></td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" colspan="2">{{$sHeadID}}</td>
									</tr>
									<tr id="repeatinfo05">
										<td valign="top" align="left" width="20%"><strong>Password</strong></td>
										<td valign="top" align="left" width="1%">
											<strong>:</strong>
										</td>
										<td valign="top" colspan="2">{{$sHeadPassword}}</td>
									</tr>
								</table>
							</td>
						</tr>
						<!------------------------------->
					</table>
				</td>
				<td class="segPanel" align="center">
					{{$sRefNo}}
					{{$sResetRefNo}}
				</td>
				<td class="segPanel" align="center" valign="middle">
					{{$sOrderDate}}
					{{$sCalendarIcon}}
					<!--<strong style="font-size:10px">mm/dd/yyyy</strong>-->
				</td>
			</tr>
			<tr>
				<td class="segPanelHeader">Discounts</td>
				<td class="segPanelHeader">Request Options</td>
			</tr>
			<tr>
				<!--
				<td class="segPanel" align="center" valign="top">
					<table>
						<tr>
							<td>{{$sAdjustedAmount}}</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					{{$sBtnDiscounts}}
				</td>
				-->

				<td class="segPanel" align="center" valign="top">
						<table>
							<tr>
								{{if $ssView}}
								{{else}}
								<td valign="middle">
									{{$sAdjustedAmount}}
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial; color:#0000FF;">{{$sClassification}}</span></div>
									<div style="margin-top:5px; vertical-align:middle; ">{{$sDiscountShow}}</div>
								</td>
								<td>{{$sDiscountInfo}}</td>
								{{/if}}
							</tr>
						</table>
						{{$sBtnDiscounts}}
					</td>

				<td class="segPanel" align="center" valign="top">
					<!--<div style="padding-left:5px">
						<strong>Priority</strong>
						{{$sNormalPriority}}
						{{$sUrgentPriority}}
					</div>
					<div style="padding-left:5px; margin-bottom:4px; vertical-align:middle">
						<strong style="float:left; margin-top:10px">Comments </strong>
						{{$sComments}}
					</div>-->
					<table>
						 <tr>
							 <td valign="top" width="5%"><strong>Priority</strong></td>
							 <td valign="top" width="5%">{{$sNormalPriority}}</td>
							 <td valign="top" width="5%">{{$sUrgentPriority}}</td>
						 </tr>
						 <tr>
							 <td valign="top" width="5%" colspan="3"><strong style="float:left; margin-top:10px">Comments </strong></td>
						 </tr>
						 <tr>
							 <td align="center" valign="middle" width="5%" colspan="3">{{$sComments}}</td>
						 </tr>
					</table>
				</td>
			</tr>

		</tbody>
	</table>

<br>
	<div align="left" style="width:95%">
		<table width="100%">
			<tr>
				<td width="70%" align="left">
					{{$sBtnAddItem}}
					{{$sBtnEmptyList}}
					{{$sHistoryButton}}
					{{$sOtherButton}}
					{{$sBtnPDF}}
				</td>
				<td align="right">
					{{$sContinueButton}}
					{{$sBreakButton}}
				</td>
			</tr>
		</table>
		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="order-list-header">
					<th width="4%" nowrap align="left">Cnt : <span id="counter">0</span></th>
					<th width="0.5%"></th>
					<th width="15%" nowrap align="left">&nbsp;&nbsp;Code</th>
					<th width="*" nowrap align="left">&nbsp;&nbsp;Service Description</th>
					<th width="5%" nowrap align="left">for Monitor</th>
					<th width="5%" nowrap align="left">Every Hr</th>
					<!--<th width="5%" nowrap align="left">No of Takes</th>-->
					<th width="5%" nowrap align="left">W/ Sample</th>
					<th width="15%" align="center">Original Price</th>
					<!--<th width="13%">Discount Type</th> -->
					<th width="17%" align="center">Net Price</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}

			<tbody id="socialServiceNotes" style="display:none">
				<tr>
					<td colspan="9">{{$sSocialServiceNotes}}</td>
				</tr>
			</tbody>
		</table>

		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tr>
			<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold">
			</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>


		</table>
		<div align="center">
			{{$sViewPDF}} &nbsp; {{$sClaimStub}}
		</div>
	</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sIntialRequestList}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>



<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
<hr/>
<!--
<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount">
<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals">
-->
{{$sRefreshDiscountButton}}
{{$sRefreshTotalButton}}