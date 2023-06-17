<div class="data-form">
	<form id="form-{{$suffix}}" method="post" action="./">
		<div style="padding:4px">Select a Dashlet to add:</div>
		<div id="accordion-{{$suffix}}" style="width:100%">
{{foreach from=$categories item=category}}
			<h3><a href="#">{{$category.name}}</a></h3>
			<div style="padding:0; margin:0">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tbody>
{{foreach from=$category.dashlets item=dashlet}}
<!--Added by Jarel 10/02/2013 
	Show Radiology Findings Dashlet if user has permission
-->
						<tr height="24" {{if $dashlet.hide eq $dept}}
											style="display:none"
										{{elseif !$showradiofindingsdashlet and $dashlet.id eq 'PatientRadioFindingsDashlet'}}
											style="display:none"
										{{elseif $dept ne $IPBM_dept and $dashlet.id eq 'Referral_Forms'}}
											style="display:none"	
										{{elseif $dashlet.id eq 'MedicalAbstract' and $medabstract ne '1'}}
											style="display:none"
										{{else}}
											style=""	
										{{/if}}	>
							<td width="20%" align="center" style="border-bottom:1px solid #bebebe;">
								<img src="{{$sRootPath}}gui/img/common/default/{{$dashlet.icon}}" align="absmiddle" border="0"/>
							</td>
							<td align="left" style="border-bottom:1px solid #bebebe;">
								{{if $onlyPatientList and $is_doctor eq ""}}
									{{if $dashlet.id eq 'PatientList'}}
										<a id="add-{{$dashlet.id}}-{{$suffix}}" href="#" onclick="Dashboard.dialog.close(); Dashboard.dashlets.add({name:'{{$dashlet.id}}'}); return false;">
											<span style="font:bold 12px Arial">{{$dashlet.name}}</span>
										</a>
									{{else}}
										<a id="add-{{$dashlet.id}}-{{$suffix}}" href="#" disabled>
											<span style="font:12px Arial">{{$dashlet.name}}</span>
										</a>
									{{/if}}
								{{else}}
									<a id="add-{{$dashlet.id}}-{{$suffix}}" href="#" onclick="Dashboard.dialog.close(); Dashboard.dashlets.add({name:'{{$dashlet.id}}'}); return false;">
										<span style="font:bold 12px Arial">{{$dashlet.name}}</span>
									</a>
								{{/if}}
							</td>
						</tr>

{{/foreach}}
					</tbody>
				</table>
			</div>
{{/foreach}}
		</div>
	</form>
</div>

<script type="text/javascript">
(function($) {
	$("#accordion-{{$suffix}}").accordion({
		autoHeight: false,
		animated: "slide",
	});
})(jQuery);

</script>