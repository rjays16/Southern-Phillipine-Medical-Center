{{* machine_occupancy.tpl 2014-01-16 Jayson Garcia *}}
{{* Table frame for the mahine occupancy list *}}

<table cellspacing="0" width="100%" border="0">
<tbody>
	<tr>
		<td class="wardlisttitlerow" width="1%">&nbsp;</td>
		<td class="wardlisttitlerow" width="9%">{{$LDMachineNo}}</td>
		<td class="wardlisttitlerow" width="6%" align="middle">{{$LDGenderInfo}}</td>
		<!-- <td class="wardlisttitlerow" width="6%">{{$LDBed}}</td> -->
		<td class="wardlisttitlerow" width="*">{{$LDFamilyName}}, {{$LDName}}</td>
	
		<td class="wardlisttitlerow" width="8%">{{$LDPatNr}}</td>
		<td class="wardlisttitlerow" width="13%">{{$BillNr}}</td>
		<!--<td class="wardlisttitlerow" width="13%">{{$LDInsuranceType}}</td>-->
		<td class="wardlisttitlerow" width="20%">
			<table cellspacing="0" width="100%" border="0">
				<tr>
					<center>{{$LDDialyserUsed}}</center>
				</tr>
				<td>
					<center>{{$LDPrev}}</center>
				</td>
				<td>
					<center>{{$LDPres}}</center>
				</td>
				<td>
					<center>{{$LDNew}}</center>
				</td>
			</table>

		</td>
		<td class="wardlisttitlerow" width="15%" align="center">{{$LDOptions}}</td>
	</tr>

	{{$sOccListRows}}

 </tbody>
</table>
