{{* ward_occupancy_list.tpl  2004-05-15 Elpidio Latorilla *}}
{{* Table frame for the occupancy list *}}

<table cellspacing="0" width="100%" border="0">
<tbody>
	<tr>
		<td class="wardlisttitlerow" width="9%">{{$LDRoom}}</td>
		<td class="wardlisttitlerow" width="15%">{{$LDDescription}}</td> <!-- added by Mats 06-24-2016 -->
		<td class="wardlisttitlerow" width="6%">{{$LDBed}}</td>
		<td class="wardlisttitlerow">&nbsp;</td>
		<td class="wardlisttitlerow" width="*">{{$LDFamilyName}}, {{$LDName}}</td>
		<td class="wardlisttitlerow" width="11%">{{$LDBirthDate}}</td>
		<td class="wardlisttitlerow" width="13%">{{$LDPatNr}}</td>
		<!--<td class="wardlisttitlerow" width="13%">{{$LDInsuranceType}}</td>-->
		<td class="wardlisttitlerow" width="13%">{{$LDCaseNo}}</td>
		<td colspan="9" class="wardlisttitlerow" width="15%" align="center">{{$LDOptions}}</td>
	</tr>

	{{$sOccListRows}}

 </tbody>
</table>
