{{$form_start}}
<div style="width:700px">
	<table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="">
		<tbody>
			<tr>
				<td colspan="4" class="segPanelHeader">Agency Details</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td nowrap="nowrap" align="right" style="width:90px"><b>Agency</b></td>
								<td nowrap="nowrap" align="left" style="width:400px">{{$agency_search}}&nbsp;{{$search_btn}}&nbsp;{{$add_btn}}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="agency-list" style="margin-top:10px"></div>
</div>
{{$form_end}}