{{* Used by \modules\dashboard\dashlets\ReferralForms\ReferralForms.php *}}
<style type="text/css">
	.btn-default{
		font-size: 10px;
	}

	.btn-inverse{
		background: #222 url(../img/button-overlay.png) repeat-x center center;	
		display: inline-block;
		vertical-align: top;

		margin: 1px;
		color: #fff;
		text-decoration: none;
		font-weight: bold;
	    font-family: inherit;
		line-height: 14px;
	    white-space: nowrap;
	    width: 100% !important;
		border: 0 none transparent !important;
		border-bottom: 1px solid rgba(0,0,0,0.25);
		position: relative;
		cursor: pointer;
		padding: 2px 5px;
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		border-radius: 5px;
		-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
		-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
		box-shadow: 0 1px 3px rgba(0,0,0,0.5);
		text-shadow: 0 -1px 1px rgba(0,0,0,0.5);
	}
</style>
<div id="px-info-{{$dashlet.id}}" style="width:0; padding:0; background-color: #a9a9a9">
	<table cellpadding="0" cellspacing="0" border="0" style="width: 390px;">
		<tr>
			<td class="segPanel">
				<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
					<tr>
						<td style="" valign="top">
							<table cellpadding="5" cellspacing="5" border="0" style="width: 100%;">
							<tr>
								
								<td align="center" bgcolor="#eaeaea" style="width: 50%;">
									{{$btnConsultationReferral}}
								</td> 
								<td align="center" bgcolor="#eaeaea" style="width: 50%;">
									{{$btnPatientReferral}}
								</td>

							</tr>
							<tr>
								<td align="center" bgcolor="#eaeaea">
									{{$btnOccupationalReferral}}
								</td>
								<td align="center" bgcolor="#eaeaea">
									{{$btnPsychologicalReferral}}
								</td>
							 	
							</tr>
							
							</table>
						</td>
						
					</tr>
					
				</table>
			</td>
		</tr>
	</table>
</div>




<script type="text/javascript">

	function viewConsultationReferral(){
		
		window.open("{{$view_rootpath}}modules/dashboard/consultationReferralFrm.php?pid="+"{{$pat.pid}}"+"&encounter_nr="+"{{$pat.encounter}}"+"&encoder="+"{{$encoder}}"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function veiwPsychologicalServReferral(){
		window.open("{{$view_rootpath}}modules/dashboard/psychologicalServReferralFrm.php?pid="+"{{$pat.pid}}"+"&encounter_nr="+"{{$pat.encounter}}"+"&encoder="+"{{$encoder}}"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function veiwOccuTherapyReferral(){
		window.open("{{$view_rootpath}}modules/dashboard/occuTherapyReferralFrm.php?pid="+"{{$pat.pid}}"+"&encounter_nr="+"{{$pat.encounter}}"+"&encoder="+"{{$encoder}}"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function veiwPatientReferral(){
		window.open("{{$view_rootpath}}modules/dashboard/patientReferralFrm.php?pid="+"{{$pat.pid}}"+"&encounter_nr="+"{{$pat.encounter}}"+"&encoder="+"{{$encoder}}"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

</script>