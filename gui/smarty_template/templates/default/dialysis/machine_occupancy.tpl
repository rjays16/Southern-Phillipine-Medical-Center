{{* machine_occupancy.tpl 2014-01-16 Jayson Garcia-OJT *}}
{{* main frame containing machine list *}}

{{$sWarningPrompt}}
{{$sFormStart}}
<table width="35%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td align="center" class="segPanelHeader" colspan="2"><strong>Search Option</strong></td>
			</tr>
			<tr>
				<td class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="2" style="font:12px Arial;color:#000000">
						<tr id="date" style="display:">
								<td align="right" width="20%">{{$search_select}}HRN/Patient:</td>
								<td align="left" width="80%">{{$search_by}}&nbsp;{{$search_box}}</td>
								<!-- <td align="left">{{$date_js}}</td> -->
						</tr>
						<tr id="date" style="display:">
								<td align="right" width="40%">Date:</td>
								<td align="left">{{$date}}{{$date_js}}</td>
						</tr>
						<tr>
							<td colspan="2" align="center">{{$view_btn}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	{{$sFormEnd}}
<BR>


<table cellspacing="0" cellpadding="0" width="100%">
  <tbody>{{if $sNodata}}<tr valign="top" ><td align="center" style="background-color:#93b6dc;color:red;"><b>NO RECORDS FOUND</b></td></tr>{{else}}
    <tr valign="top">
      <td>
		{{include file="dialysis/machine_occupancy_list.tpl"}}
	</td>
      <td align="right">{{$sSubMenuBlock}}</td>
    </tr>
    {{/if}}
  </tbody>
</table>

{{$sInputDate}}
{{$sInputHRN}}

<p>
{{$pbClose}}

<br>
{{* $sOpenWardMngmt *}}
</p>