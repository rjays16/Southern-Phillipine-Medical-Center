$(document).ready(function () {

	$('.combo-box').select2({
		width : 300
	});

	// $('#height').keyup(function(){
	// 	var id1 = '#height';
	// 	var id2 = '#heightyn';
	// 	changevalue(id1,id2);
	// });

	// $('#weight').keyup(function(){
	// 	var id1 = '#weight';
	// 	var id2 = '#weightyn';
	// 	changevalue(id1,id2);
	// });

	// $('#bp').keyup(function(){
	// 	var id1 = '#bp';
	// 	var id2 = '#bpyn';
	// 	changevalue(id1,id2);
	// });

	// $('#pr').keyup(function(){
	// 	var id1 = '#pr';
	// 	var id2 = '#pryn';
	// 	changevalue(id1,id2);
	// });

	// $('#rr').keyup(function(){
	// 	var id1 = '#rr';
	// 	var id2 = '#rryn';
	// 	changevalue(id1,id2);;
	// });

	// $('#bodybuilt').keyup(function(){
	// 	var id1 = '#bodybuilt';
	// 	var id2 = '#bodybuiltyn';
	// 	changevalue(id1,id2);
	// });

	$('#others').keyup(function(){
		var id1 = '#others';
		var id2 = '#othersyn';
		changevalue(id1,id2);
	});


	// diag

	$('#diag_albumin').keyup(function(){
		var id1 = '#diag_albumin';
		var id2 = '#diag_albumin_yn';
		changevalue(id1,id2);
	});

	$('#diag_sugar').keyup(function(){
		var id1 = '#diag_sugar';
		var id2 = '#diag_sugar_yn';
		changevalue(id1,id2);
	});

	$('#diag_pus').keyup(function(){
		var id1 = '#diag_pus';
		var id2 = '#diag_pus_yn';
		changevalue(id1,id2);
	});

	$('#diag_rbc').keyup(function(){
		var id1 = '#diag_rbc';
		var id2 = '#diag_rbc_yn';
		changevalue(id1,id2);
	});

	$('#diag_fbs').keyup(function(){
		var id1 = '#diag_fbs';
		var id2 = '#diag_fbs_yn';
		changevalue(id1,id2);
	});

	$('#diag_lipid').keyup(function(){
		var id1 = '#diag_lipid';
		var id2 = '#diag_lipid_yn';
		changevalue(id1,id2);
	});

	$('#diag_trigly').keyup(function(){
		var id1 = '#diag_trigly';
		var id2 = '#diag_trigly_yn';
		changevalue(id1,id2);
	});

	$('#diag_hdl').keyup(function(){
		var id1 = '#diag_hdl';
		var id2 = '#diag_hdl_yn';
		changevalue(id1,id2);
	});

	$('#diag_ldl').keyup(function(){
		var id1 = '#diag_ldl';
		var id2 = '#diag_ldl_yn';
		changevalue(id1,id2);
	});

	$('#diag_creatinine').keyup(function(){
		var id1 = '#diag_creatinine';
		var id2 = '#diag_creatinine_yn';
		changevalue(id1,id2);
	});

	$('#diag_sua').keyup(function(){
		var id1 = '#diag_sua';
		var id2 = '#diag_sua_yn';
		changevalue(id1,id2);
	});

	$('#diag_sgpt').keyup(function(){
		var id1 = '#diag_sgpt';
		var id2 = '#diag_sgpt_yn';
		changevalue(id1,id2);
	});


	$("#submit").click(function(){
		
	});
});


function changevalue(id1,id2){
	var a = $(id1).val();
	if (a != '') {
		$(id2).attr('value',1);
	}else{
		$(id2).attr('value','');
	}
};

function printMedChrt(encounter_nr, refno) {
	window.open("reports/seg-ic-medchart-pdf.php?enc=" + encounter_nr + "&refno=" + refno, "Medical Chart",
		"modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
}