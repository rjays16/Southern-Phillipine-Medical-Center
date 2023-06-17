<!-- {{$sFormStart}} -->
{{$sFormNotes}}
<head>
    {{foreach from=$javascripts item=script}}
    {{$script}}
    {{/foreach}}
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
</head>

<div style="width:630px;">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" style="font:12px Arial;">
			<tbody valign="middle">



				<!-- <tr>
					<td class="segPanelHeader" colspan="2">Notes</td>
				</tr> -->
				<tr>
					<td class="segPanelHeader" colspan="2">Patient's Details</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  id="table_notes" style="font:14px Arial">
							<tr>
								<td colspan="2">
								<table  width="100%" class="transaction_details_table" cellpadding="0" cellspacing="0" style="font:normal 14px Arial; padding:4px" >
									<tr>
										<td nowrap="nowrap"><strong>Name : </strong></td><td nowrap="nowrap">{{$patient_name}}</td>
										<td><strong>Date : </strong></td><td>{{$pNotes_display}}</td>
										{{$NotesDate}}
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>HRN : </strong></td><td>{{$sPatientID}}</td>
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>Case No. : </strong></td><td>{{$case_number}}</td>
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>Bed No. : </strong></td><td>{{$bedNumDisplay}}
									</td>
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>Room No. : </strong></td><td>{{$roomNumDisplay}}</td>
									</tr>
								</table>
								</td>
							</tr>
					</td>		
				</tr>

				<tr>
					<td class="segPanelHeader" colspan="2">Patient's Note</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  id="table_notes" style="font:14px Arial">
							<tr>			
							
<!-- 								<td width="135px"><label  style="font:14px Arial;">Impression/Diagnosis:</label></td>
								<td>{{$impression}}</td> -->
								<td width="135px"><label  style="font:14px Arial;">Full Diagnosis:</label></td>
								<td>{{$impression}}</td> 
							</tr>
							<tr>
								<td><label style="font:14px Arial;">&nbsp</label></td>
							</tr>

							<tr>
								<td><label style="color: red; font-size: 16px">*</label><label style="font:14px Arial;">Diet:</label></td>
								<td><span style="{{$brw_diet}}">{{$diet}}</span><span style="{{$brw_remarks}}">&nbsp;&nbsp;&nbsp;{{$remarks}}</span></td>
								<!-- <td>{{$remarks}}</td> -->
							</tr>
							
							<tbody id="tb_notes">
								{{$diet_list}}
							</tbody>
							<tr>
								<td>{{$listBR}}</td>
								<td>{{$listBR}}</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">&nbsp</label></td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">IVF/Level/Due Time:</label></td>
								<td>{{$ivf}}</td>
							</tr>
			
							<tr>
								<td><label style="font:14px Arial;">Religion:</label></td>
								<td>{{$religion}}</td>
							</tr>
							<tr>
								<td><label style="color: red; font-size: 16px">*</label><label style="font:14px Arial;">Height:</label></td>
								<td>{{$height}}cm</td>
							</tr>
								<tr>
								<td><label style="color: red; font-size: 16px">*</label><label style="font:14px Arial;">Weight:</label></td>
								<td>{{$weight}}kg</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">BMI:</label></td>
								<td>{{$bmi_category}}</td>
							</tr>
								<tr>
								<td>&nbsp;</td>
							</tr>
								{{if $isICU neq 'ICU'}}
							<tr>
								<td><label style="font:14px Arial;">Available Meds:</label></td>
								<td>{{$avail_meds}}</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">Other Gadgets Incl. Blood (Bag#, S#, Type):</label></td>
								<td>{{$gadgets}}</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">Problems/Meds/Msg/Others:</label></td>
								<td>{{$problems}}</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">Actions:</label></td>
								<td>{{$actions}}</td>
							</tr>

							<tr>
							<!-- Start added  -->
							{{/if}}
							{{if $isICU eq 'ICU'}}
							<tr>
								<td width="135px"><label  style="font:14px Arial;">Service:</label></td>
								<td>{{$services}}</td><br>
							</tr>
						
							<tr>
								<td><label style="font:14px Arial;">&nbsp</label></td>
							</tr>
							<!-- <tr>
								<td><label style="font:14px Arial;">Attending Doctor:</label></td>
								<td><span style="{{$nr}}">{{$dept_nr}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="{{$doc_name}}">{{$dr_nr}}</span></td>
							</tr> -->

							<tr>
								<td width="135px"><label  style="font:14px Arial;">Other Gadgets Incl. Blood (Bag#,S#,Types):</label></td>
								<td>{{$other}}</td> 
							</tr>

							<tr>
								<td width="135px"><label  style="font:14px Arial;">Diagnostic Procedures:</label></td>
								<td>{{$diagnostic}}</td> 
							</tr>

							<tr>
								<td width="135px"><label  style="font:14px Arial;">Special Endorsement:</label></td>
								<td>{{$special}}</td> 
							</tr>
<!-- 
							<tr>
								<td width="135px"><label  style="font:14px Arial;">Additional Endorsement:</label></td>
								<td>{{$additional}}</td> 
							</tr> -->

							<tr>
								<td width="135px"><label  style="font:14px Arial;">VS/ I&O:</label></td>
								<td>{{$vs}}</td> 
							</tr>
							{{/if}}
							<!-- End added  -->
							{{if $lastmod}}
								<td><label style="font:14px Arial;">Last modified by:</label></td>
									<td><span>{{$lastmod}}</span></td>
									<tr>
									<td><label style="font:14px Arial;">Date/time:</label></td>
									<td><span>{{$datetime}}</span></td></tr>
							{{/if}}
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<!--<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center">{{$cancelBtn}} </td>
				</tr>   -->
			</tbody>
		</table>
</div>
{{$submitted}}
{{$encounter_nr}}
{{$pid}}
{{$ward}}
{{$ward_nr}}
{{$nBmi}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<!-- {{$sFormEnd}} -->
{{$sTailScripts}}