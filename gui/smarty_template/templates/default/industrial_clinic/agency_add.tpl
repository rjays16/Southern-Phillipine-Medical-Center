{{$form_start}}
<div style="width:98%;padding 5px 0px;margin-top:5px">
	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="font:12px Arial bold">
		<tbody>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Agency Name</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_name}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Address</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_address}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Contact No.</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_contactnum}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Short Name</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_sname}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>CEO/President</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_president}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>HR Manager</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_hr}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Hospital Account No.</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$agency_accountnum}}</td>
			</tr>
		</tbody>
	</table>
</div>
<div style="margin-left:30px;margin-top:5px">
	{{$save_btn}}{{$close_btn}}
</div>
{{$submitted}}
{{$form_end}}