{{* machine_occupancy.tpl 2014-01-16 Jayson Garcia *}}
{{* One row for each machine *}}
{{* This template is used by /modules/dialysis/seg-dialysis-machine-list.php to populate the machine_occupancy_list.tpl template *}}

 {{if $bToggleRowClass}}
	<tr class="{{$class_label}}">
 {{else}}
	<tr class="{{$class_label}}">
 {{/if}}
		<td></td>
		<td >{{$sMachineNumber}}</td>
		<td align="center">{{$sGenderInfo}}</td>
	
		<td >{{$sTitle}} {{$sFamilyName}}{{$cComma}} {{$sName}}</td>
		<td style="font-size:x-small ">&nbsp;{{$sPatNr}}</td>
		<td style="font-size:x-small ">&nbsp;{{$sEnc}}</td>
		<td style="font-size:x-small" >
			<table cellspacing="0" width="100%" border="0">
				
				<td align="center">{{$sPrev}}</td>
				<td align="center">{{$sPres}}</td>
				<!-- <td align="center" width="30%">{{$sNew}}</td> -->
			</table>
		</td>
	
		<td align="center">&nbsp;{{$sAdmitDataIcon}} {{$sChartFolderIcon}} {{$sNotesIcon}} {{$sTransferIcon}} {{$sDischargeIcon}}{{$sRequestTray}}</td>
		</tr>
				 
				
		
		
		<tr>
		<td colspan="8" class="thinrow_vspacer">{{$sOnePixel}}</td>
		</tr>
