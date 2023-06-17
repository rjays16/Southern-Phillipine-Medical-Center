{{* reg_search_list_row.tpl  *}}
{{* This is the row for the resulting list of the person/patient search module *}}
{{* If you rearrange the row columns, be sure to synchronize it with the title row at reg_search_main.tpl *}}

<tr  {{if $toggle}} class="wardlistrow2" {{else}} class="wardlistrow1" {{/if}}>
	<td>&nbsp;{{$sCaseNr}} {{$sOutpatientIcon}} <font size=1 color="red">{{$LDAmbulant}}</font></td>
	<td>&nbsp;{{$sEncDate}}</td>
	<td>&nbsp;{{$sCurrentDept}}</td>
	<td>&nbsp;{{$sSex}}</td>
	<td>&nbsp;{{$sLastName}}</td>
	<td>&nbsp;{{$sFirstName}} {{$sCrossIcon}}</td>
	<td>&nbsp;{{$sMiddleName}}</td>
	<td>&nbsp;{{$sBday}}</td>
	<td>&nbsp;{{$sBrgy}}</td>
	<td>&nbsp;{{$sMuni}}</td>
<!--	
	<td>&nbsp;{{$sZipCode}}</td>
-->
	{{if $ptype eq 'ipd'}}
		<td align="center">{{$sCurrent_ward_name}}</td>
	{{/if}}	
	{{if $ptype eq 'ipd' || $ptype eq 'opd' || $ptype eq 'er'}}
		<td align="center">{{$sDischarge_date}}</td>
	{{/if}}	
	<td align="center">&nbsp;{{$sOptions}} {{$sHiddenBarcode}}</td>
	{{if $sServeOption}}
		<td align="center">{{$sServeOption}}</td>        
	{{/if}}	
</tr>
