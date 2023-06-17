{{$form_start}}
<div style="width:98%;padding 5px 0px;margin-top:5px">
	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="font:12px Arial bold">
		<tbody>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Patient ID</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$pid}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Company ID</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$company_id}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Employee ID</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$employee_id}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Position</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$position}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="30%"><strong>Job Status</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">{{$job_status}}</td>
			</tr>
		</tbody>
	</table>
</div>
<div style="margin-left:30px;margin-top:5px">
	{{$save_btn}}<!--{{$close_btn}} -->
</div>
{{$submitted}}
{{$mode}}
{{$form_end}}