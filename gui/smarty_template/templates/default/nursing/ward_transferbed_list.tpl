{{* ward_occupancy_list.tpl  2004-05-15 Elpidio Latorilla *}}
{{* Table frame for the occupancy list *}}

<!-- {{if !$hidedatetime}} -->
<table>
		<tr>
			<td class="adm_item">
				<b style="color:red; font-size: 14px">Date and Time transferred:</b>
			</td>
			<td colspan=2 class="adm_input">
				{{$sLDDateFrom}}
				{{$sDateMiniCalendar}}
				{{$jsCalendarSetup}}
				{{$sLDTimeFrom}}
			</td>
		</tr>
</table>
<!-- {{/if}} -->
&nbsp;&nbsp;
<table cellspacing="0" width="100%">
<tbody>
	<tr>
		<td class="adm_item">{{$LDRoom}}</td>
		<!-- added by Mats 07262016 -->
		<td class="adm_item">{{$LDDescription}}</td>
		
		<td class="adm_item">{{$LDBed}}</td>
		<td class="adm_item">&nbsp;</td>
		<td class="adm_item">{{$LDFamilyName}}, {{$LDName}}</td>
		<td class="adm_item">{{$LDBirthDate}}</td>
		<td class="adm_item">{{$LDBillType}}</td>
		<td class="adm_item">&nbsp;</td>
	</tr>

	{{$sOccListRows}}

 </tbody>
</table>
