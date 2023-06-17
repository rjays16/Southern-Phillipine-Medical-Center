<div>
{{$form_start}}
<div id="tabs" style="width:90%;margin-top:5px" class="ui-tabs">
	<ul>
		<li><a href="#tab-billing">Billing</a></li>
	</ul>

	<div id="tab-employees" class="ui-tabs-hide">
		<div style="width:100%;margin-top:5px">
			<table width="100%" style="font:12px Arial bold">
				<tbody>
					<tr>
						<td colspan="2" align="left"><b>Search Employee</b>&nbsp;{{$search_fld}}&nbsp;{{$search_btn}}&nbsp;{{$addperson_btn}}</td>
					</tr>
				</tbody>
			</table>
			<div class="dashlet">
				<div id="member-list" style="margin-top:10px"></div>
			</div>
		</div>
	</div>


	<div id="tab-billing" class="ui-tabs-hide">
		{{$companyName}}{{$companyId}}
		<div style="width:100%;margin-top:5px">
			<table width="100%" style="font:12px Arial bold" border="0">
				<tbody>
					<tr>
						<td colspan="2" align="left" class="segPanelHeader">Search Options
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" class="segPanel">
								<b>
									<table width="100%" style="font:12px Arial bold" border="0" class="">
										<tbody>
											<tr>
												<td colspan="2">{{$forCompany}}
												<div id="employee-list" style="margin-top:10px;display:none"></div>
												</td>
											</tr>
											<tr>
													<td colspan="2" align="left">{{$forEmployee}}</td>
											</tr>
											<tr>
												<td width="20%">Start:</td><td width="*">{{$searchDteStart}}</td>
											</tr>
											<tr>
												<td width="20%">End:</td><td>{{$searchDteEnd}}</td>
											</tr>
											<tr>
												<td colspan="2" align="center" class="">{{$viewReportBtn}}
												</td>
											</tr>
										</tbody>
									</table>
								</b>
							</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>
</div>
{{$agency_id}}
{{$form_end}}
</div>


