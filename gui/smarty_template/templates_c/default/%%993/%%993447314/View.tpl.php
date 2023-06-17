<?php /* Smarty version 2.6.0, created on 2020-02-05 13:42:35
         compiled from ../../../modules/dashboard/dashlets/ReferralForms/templates/View.tpl */ ?>
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
<div id="px-info-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:0; padding:0; background-color: #a9a9a9">
	<table cellpadding="0" cellspacing="0" border="0" style="width: 390px;">
		<tr>
			<td class="segPanel">
				<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
					<tr>
						<td style="" valign="top">
							<table cellpadding="5" cellspacing="5" border="0" style="width: 100%;">
							<tr>
								
								<td align="center" bgcolor="#eaeaea" style="width: 50%;">
									<?php echo $this->_tpl_vars['btnConsultationReferral']; ?>

								</td> 
								<td align="center" bgcolor="#eaeaea" style="width: 50%;">
									<?php echo $this->_tpl_vars['btnPatientReferral']; ?>

								</td>

							</tr>
							<tr>
								<td align="center" bgcolor="#eaeaea">
									<?php echo $this->_tpl_vars['btnOccupationalReferral']; ?>

								</td>
								<td align="center" bgcolor="#eaeaea">
									<?php echo $this->_tpl_vars['btnPsychologicalReferral']; ?>

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
		
		window.open("<?php echo $this->_tpl_vars['view_rootpath']; ?>
modules/dashboard/consultationReferralFrm.php?pid="+"<?php echo $this->_tpl_vars['pat']['pid']; ?>
"+"&encounter_nr="+"<?php echo $this->_tpl_vars['pat']['encounter']; ?>
"+"&encoder="+"<?php echo $this->_tpl_vars['encoder']; ?>
"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function veiwPsychologicalServReferral(){
		window.open("<?php echo $this->_tpl_vars['view_rootpath']; ?>
modules/dashboard/psychologicalServReferralFrm.php?pid="+"<?php echo $this->_tpl_vars['pat']['pid']; ?>
"+"&encounter_nr="+"<?php echo $this->_tpl_vars['pat']['encounter']; ?>
"+"&encoder="+"<?php echo $this->_tpl_vars['encoder']; ?>
"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function veiwOccuTherapyReferral(){
		window.open("<?php echo $this->_tpl_vars['view_rootpath']; ?>
modules/dashboard/occuTherapyReferralFrm.php?pid="+"<?php echo $this->_tpl_vars['pat']['pid']; ?>
"+"&encounter_nr="+"<?php echo $this->_tpl_vars['pat']['encounter']; ?>
"+"&encoder="+"<?php echo $this->_tpl_vars['encoder']; ?>
"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function veiwPatientReferral(){
		window.open("<?php echo $this->_tpl_vars['view_rootpath']; ?>
modules/dashboard/patientReferralFrm.php?pid="+"<?php echo $this->_tpl_vars['pat']['pid']; ?>
"+"&encounter_nr="+"<?php echo $this->_tpl_vars['pat']['encounter']; ?>
"+"&encoder="+"<?php echo $this->_tpl_vars['encoder']; ?>
"+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

</script>