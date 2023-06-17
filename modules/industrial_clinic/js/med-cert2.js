
$(document).ready(function () {

		$('INPUT[name=with_dental]').click(function (){
			if($(this).is(':checked')){
				$('#dentaldiv').show();
			}else{
				$('#dentaldiv').hide();
			}
		});

		$('INPUT[name=with_optha]').click(function (){
			if($(this).is(':checked')){
				$('#opthadiv').show();
			}else{
				$('#opthadiv').hide();
			}
		});

		$('INPUT[name=with_ent]').click(function (){
			if($(this).is(':checked')){
				$('#entdiv').show();
			}else{
				$('#entdiv').hide();
			}
		});

		$('INPUT[name=with_medical]').click(function (){
			if($(this).is(':checked')){
				$('#medicaldiv').show();
			}else{
				$('#medicaldiv').hide();
			}
		});


		if($('#with_dental').is(':checked')){
			$('#dentaldiv').show();
		}else{
			$('#dentaldiv').hide();
		}

		if($('#with_optha').is(':checked')){
			$('#opthadiv').show();
		}else{
			$('#opthadiv').hide();
		}

		if($('#with_ent').is(':checked')){
			$('#entdiv').show();
		}else{
			$('#entdiv').hide();
		}

		if($('#with_medical').is(':checked')){
			$('#medicaldiv').show();
		}else{
			$('#medicaldiv').hide();
		}

		var mode = $('#mode').val();
		if (mode == 'save') {
			$('#print').hide();
			$('#update').hide();
		}else{
			$('#save').hide();

		}

		$('#save').click(function (){
			chkForm();
			chckdental();
			chckoptha();
			chckent();
			chckmedical();
			$('#update').parents('form').submit();
		});

		$('#update').click(function (){
			chkForm();
			chckdental();
			chckoptha();
			chckent();
			chckmedical();
			$('#update').parents('form').submit();
		});

		$('#print').click(function (){
			/*var encounter_nr = $('#encounter_nr').val(); 
			var font_sizerem = $('#font_sizerem option:selected').val();*/ 
			var data = {encounter_nr : $('#encounter_nr').val(),
						// font_sizedental : $('#font_sizedental option:selected').val(), 
						// font_sizeoptha : $('#font_sizeoptha option:selected').val(), 
						// font_sizedent : $('#font_sizedent option:selected').val(), 
						// font_sizemed : $('#font_sizemed option:selected').val(), 
						font_sizerem : $('#font_sizerem option:selected').val() 
						}
			printMedCert(data);
		});

});


function chkForm(){
	if ($("#cert_med input:checkbox:checked").length == 0){
	   alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
	   return event.preventDefault();
	}else{
		return true;
	}
}

function chckdental(){
	if ($('#with_dental').is(':checked')) {
		if ($('textarea#dental_findings').val() == '' || $('#dr_nr_dental').val() == 0) {
			alert('Please input dental findings in the provided area and select the physician in-charge of the examination');
			return event.preventDefault();
		}else{
			return true;
		}
	}
}
function chckoptha(){
	if ($('#with_optha').is(':checked')) {
		if ($('textarea#optha_findings').val() == '' || $('#dr_nr_optha').val() == 0) {
			alert('Please input Opthalmology findings in the provided area and select the physician in-charge of the examination');
			return event.preventDefault();
		}else{
			return true;
		}
	}
}
function chckent(){
	if ($('#with_ent').is(':checked')) {
		if ($('textarea#ent_findings').val() == '' || $('#dr_nr_ent').val() == 0) {
			alert('Please input ENT findings in the provided area and select the physician in-charge of the examination');
			return event.preventDefault();
		}else{
			return true;
		}
	}	
}
function chckmedical(){
	if ($('#with_medical').is(':checked')) {
		if ($('textarea#medical_findings').val() == '' || $('#dr_nr_med').val() == 0) {
			alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
			return event.preventDefault();
		}else{
			return true;
		}
	}
}

function printMedCert(data){
	window.open("seg-ic-cert-med-pdf.php?encounter_nr="+data['encounter_nr']+"&fsizerem="+data['font_sizerem'],"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
	
}