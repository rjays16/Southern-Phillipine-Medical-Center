{{* ward_occupancy_list_row.tpl 2004-06-15 Elpidio Latorilla *}}
{{* One row for each occupant or room/bed *}}
{{* This template is used by /modules/nursing/nursing_station.php to populate the ward_occupancy_list.tpl template *}}

 {{if $bHighlightRow}}
 	<tr class="hilite">
 {{elseif $bToggleRowClass}}
	<tr class="wardlistrow1">

 {{else}}
	<tr class="wardlistrow2">
 {{/if}}
		<td>&nbsp;{{$sRoom}}</td>
                <!-- added by Mats 07262016 -->
		<td>&nbsp;{{$sDescription}}</td>
		<td>&nbsp;{{$sBed}}{{$sBedPlusIcon}}</td> <!-- edited by: syboy 06/30/2015 -->
		<td>
			<table>
				{{$sBedIcon}}
			</table>
		</td>
		<td>
			<table>
				{{$sTitle}} {{$sFamilyName}}{{$cComma}} {{$sName}}
			</table>
		</td>
		<td>
			<table>
				{{$sBirthDate}}
			</table>
		</td>
		<td>
			<table>
				{{$sInsuranceType}}
			</table>
		</td>
		<td>
			<table>
				{{$sNotesIcon}}
			</table>
		</td>
		</tr>
		<tr>
		<td colspan="8" class="thinrow_vspacer">{{$sOnePixel}}</td>
	</tr>
