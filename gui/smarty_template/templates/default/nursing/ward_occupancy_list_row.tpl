{{* ward_occupancy_list_row.tpl 2004-06-15 Elpidio Latorilla *}}
{{* One row for each occupant or room/bed *}}
{{* This template is used by /modules/nursing/nursing_station.php to populate the ward_occupancy_list.tpl template *}}

 {{if $bToggleRowClass}}
	<tr class="{{$class_label}}">
 {{else}}
	<tr class="{{$class_label}}">
 {{/if}}
 		<!-- <td>{{$sMiniColorBars}}</td> commented by mats 06-24-2016-->
		<td>&nbsp;{{$sRoom}}</td>
		<td>{{$sDescription}}</td>
		<td style="font-size:x-small">
			<table class="{{$full_width}}">
				<tr>
					{{ if isset($sPClass) }}
					<td>
						<table>
						{{$sPClass}}
						</table>
					</td>
					
					{{ /if }}
					<td>
						<table>
							<tr>
								{{$sBed}}{{$sBedPlusIcon}}	
							</tr>
						</table>
					</td>	
				</tr>
			</table>

			
		</td> <!-- added by: syboy 06/30/2015 -->
		<td style="font-size:x-small">
			<table width="100%" cellspacing="0">
				{{$sBedIcon}}
			</table>
		</td> <!-- edited by: syboy; 05/20/2015 -->
		<td>
			<table width="100%" cellspacing="0">
			{{$sTitle}} {{$fullnames}}
			</table>
		</td> <!-- {{$cComma}} {{$sName}} -->
		<td style="font-size:x-small; padding: 0;">
			<table width="100%" cellspacing="0">
				{{$sBirthDate}}
			</table>
		</td>
		<td style="font-size:x-small ">
			<table width="100%" cellspacing="0">
				{{$sPatNr}}
			</table>
		</td>
		<td style="font-size:x-small ">
			<table width="100%" cellspacing="0">
				{{$sCaseNo}}
			</table>
		</td>
		<td>
			<table>
				<tr>
					<td>
						<table>
							{{$sAccommodationIcon}}
						</table>
					</td>
					<td>
						<table>
							{{$sAdmitDataIcon}}
						</table>
					</td>
					<td>
						<table>
						{{$sChartFolderIcon}}
						</table>
					</td>
					<td>
						<table>
							{{$sNotesIcon}}
						</table>
					</td>
					<td>
						<table>
							{{$sTransferIcon}}
						</table>
					</td>
					<td>
						<table>
							{{$sDischargeIcon}}
						</table>
					</td>
					<td>
						<table>
							{{$sTransXpiredIcon}}
						</table>
					</td>
                                        <td>
						<table>
							{{$patient_to_be_discharge}}
						</table>
					</td>
				</tr>
			</table>
		</td>
		</tr>
				 
				 {{if $isBaby}}
					{{$BabyRows}}
				 {{else}}
				 {{/if}}

		<!-- dati code, jan. 24, 2010
				{{if $isBaby}}
				{{if $bToggleRowClass}}
				<tr class="wardlistrow1">
			 {{else}}
				<tr class="wardlistrow2">
			 {{/if}}
					<td></td>
					<td style="font-size:x-small">{{$sRoom}}</td>
					<td style="font-size:x-small ">&nbsp;{{$sBed}} {{$sBabyBedIcon}}</td>
					<td>{{$sBabyIcon}} {{$sBabyFamilyName}}{{$cComma}} {{$sBabyName}}</td>
					<td style="font-size:x-small ">{{$sBabyBirthDate}}</td>
					<td style="font-size:x-small ">&nbsp;{{$sBabyPatNr}}</td>
					<td></td>
					<td>&nbsp;{{$sBabyNotesIcon}} {{$sBabyTransferIcon}} {{*$sBabyDischargeIcon*}}</td>
					</tr>
			 {{else}}
			 {{/if}}
		-->
		
		<tr>
		<td colspan="17" class="thinrow_vspacer">{{$sOnePixel}}</td>
		</tr>
