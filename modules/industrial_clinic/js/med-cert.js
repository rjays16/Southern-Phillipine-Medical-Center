$j(document).ready(function () {
	/*dental*/
		$j('INPUT[name=with_dental]').click(function (){
			if($j(this).is(':checked')){
				$j('#dental').show();
				$j('#dental_findings').show();
				$j('#dr_nr_dental').show();
				$j('#dentist').show();
				$j('#show_dental').show();
			}else{
				$j('#dental').hide();
				$j('#dental_findings').hide();
				$j('#dr_nr_dental').hide();
				$j('#dentist').hide();
				$j('#show_dental').hide();
			}
		});
	/*end dental*/
	/*Optha*/
	$j('INPUT[name=with_optha]').click(function (){
		if($j(this).is(':checked')){
			$j('#optha').show();
			$j('#optha_findings').show();
			$j('#dr_nr_optha').show();
			$j('#opthalmologist').show();
			$j('#show_optha').show();;
		}else{
			$j('#optha').hide();
			$j('#optha_findings').hide();
			$j('#dr_nr_optha').hide();
			$j('#opthalmologist').hide();
			$j('#show_optha').hide();
		}
	});
	// /*ent*/
	$j('INPUT[name=with_ent]').click(function (){
		if($j(this).is(':checked')){
			$j('#ent').show();
			$j('#ent_findings').show();
			$j('#dr_nr_ent').show();
			$j('#entologist').show();
			$j('#show_ent').show();;
		}else{
			$j('#ent').hide();
			$j('#ent_findings').hide();
			$j('#dr_nr_ent').hide();
			$j('#entologist').hide();
			$j('#show_ent').hide();
		}
	});
	/*end ent*/
	/*medical*/
	$j('INPUT[name=with_medical]').click(function (){
		if($j(this).is(':checked')){
			$j('#medical').show();
			$j('#medical_findings').show();
			$j('#dr_nr_med').show();
			$j('#physician').show();
			$j('#show_medical').show();;
		}else{
			$j('#medical').hide();
			$j('#medical_findings').hide();
			$j('#dr_nr_med').hide();
			$j('#physician').hide();
			$j('#show_medical').hide();
		}
	});
	/*end medical*/
});


function preset(){

	if($j('#with_dental').is(':checked')){
		$j('#dental').show();
		$j('#dental_findings').show();
		$j('#dr_nr_dental').show();
		$j('#dentist').show();
		$j('#show_dental').show();
	}else{
		$j('#dental').hide();
		$j('#dental_findings').hide();
		$j('#dr_nr_dental').hide();
		$j('#dentist').hide();
		$j('#show_dental').hide();
	}


	if($j('#with_optha').is(':checked')){
		$j('#optha').show();
		$j('#optha_findings').show();
		$j('#dr_nr_optha').show();
		$j('#opthalmologist').show();
		$j('#show_optha').show();;
	}else{
		$j('#optha').hide();
		$j('#optha_findings').hide();
		$j('#dr_nr_optha').hide();
		$j('#opthalmologist').hide();
		$j('#show_optha').hide();
	}

	if($j('#with_ent').is(':checked')){
		$j('#ent').show();
		$j('#ent_findings').show();
		$j('#dr_nr_ent').show();
		$j('#entologist').show();
		$j('#show_ent').show();;
	}else{
		$j('#ent').hide();
		$j('#ent_findings').hide();
		$j('#dr_nr_ent').hide();
		$j('#entologist').hide();
		$j('#show_ent').hide();
	}


	if($j('#with_medical').is(':checked')){
		$j('#medical').show();
		$j('#medical_findings').show();
		$j('#dr_nr_med').show();
		$j('#physician').show();
		$j('#show_medical').show();;
	}else{
		$j('#medical').hide();
		$j('#medical_findings').hide();
		$j('#dr_nr_med').hide();
		$j('#physician').hide();
		$j('#show_medical').hide();
	}
}



function chkForm(){

	if($j('#with_medical').is(':checked')){
		if($j('#medical_findings').val() == '' || $j('#dr_nr_med').val() == 0){
			alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
			$j('#with_medical').focus();
			return false;
		}
	}

	else if($j('#with_dental').is(':checked')){
		if($j('#dental_findings').val() == '' || $('dr_nr_dental').val() == 0){
			alert('Please input dental findings in the provided area and select the dentist in-charge of the examination');
			$('#with_dental').focus();
			return false;
		}
	}
	else if($j('#with_optha').is(':checked')){
		if($j('#optha_findings').value=='' || $j('#dr_nr_optha').val() == 0){
			alert('Please input Opthalmology findings in the provided area and select the doctor in-charge of the examination');
			$j('with_dental').focus();
			return false;
		}
	}

	else if($j('#with_ent').is(':checked')){
		if($j('#ent_findings').value=='' || $j('#dr_nr_ent').val() == 0){
			alert('Please input ENT findings in the provided area and select the doctor in-charge of the examination');
			$('with_dental').focus();
			return false;
		}
	}

	else if($j('#with_medical').not(':checked') && $j('#with_dental').not(':checked') && $j('#with_optha').not(':checked') && $j('#with_ent').not(':checked')){
		if($j('#medical_findings').val() == '' || $j('#dr_nr_med').val() ==0 || $j('#dental_findings').val() =='' || $j('#dr_nr_dental').val()==0|| $j('#dr_nr_optha').val()==0|| $j('#dr_nr_ent').val()==0){
			alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
			$j('#with_medical').focus();
			return false;
		}
	}

	return true;

}
