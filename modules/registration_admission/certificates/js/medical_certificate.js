$j(document).ready(function(){
	var cert_type = $j("input[name='cert_type']:checked").val();
	if(cert_type == 0){
		$j('#civil_case_no_field').hide();
		$j('#court_field').hide();
		$j('#judge_field').hide();
		$j('#space5').hide();
	}

	$j("input[id='cert_type']").change(function(){
	    if($j(this).val() == 0){
	    	$j('#civil_case_no_field').hide();
			$j('#court_field').hide();
			$j('#judge_field').hide();
			$j('#space5').hide();
	    }else{
	    	$j('#civil_case_no_field').show();
			$j('#court_field').show();
			$j('#judge_field').show();
			$j('#space5').show();
	    }
  	});
});