var seg_validTime=false;

//formatting time
function setFormatTime(thisTime,AMPM){

	var strTime = thisTime.value;
	var stime = strTime.substring(0,5);
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";

//		trimString(thisTime);

	if (thisTime.value==''){
		seg_validTime=false;
		return;
	}

	stime = stime.replace(':', '');

	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
	}else{
		alert("Invalid time format.");
		thisTime.value = "";
		seg_validTime=false;
		thisTime.focus();
		return;
	}

	jtime = hour + ":" + minute;
//		js_setTime(jtime);

	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
}// end of function setFormatTime


//search wizard
function CompanySearch(pid){
	if($('ischargeToAgency').checked==true){
		return overlib(
			OLiframeContent('../../modules/industrial_clinic/seg-ic-company-select.php?pid='+pid, 600, 410, 'fOrderTray', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../..//images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Company',
				MIDX,0, MIDY,0,
				STATUS,'Run Company search');
	}
	alert("You need to check the charge to agency..");
	return false;
}


function validate(){
	if($('transaction_date').value=="")
	{
		alert("Please provide the transaction date.");
		$('transaction_date').focus();
		return false;
	}
	if($('transaction_time').value=="")
	{
		alert("Please provide the transaction time.");
		$('transaction_time').focus();
		return false;
	}
	if($('ischargeToAgency').checked==true){
		if($('agency_organization').value=="")
		{
			alert("Please provide the Agency/Organization.");
			$('agency_organization').focus();
			return false;
		}
		/* commented by art 05/04/2014
		if($('position').value=="")
		{
			alert("Please provide the position.");
			$('position').focus();
			return false;
		}
		if($('id_no').value=="")
		{
			alert("Please provide the ID no.");
			$('id_no').focus();
			return false;
		}
		*/
	}
	if($('purpose_exam').value=="")
	{
		alert("Please provide the purpose exam.");
		$('purpose_exam').focus();
		return false;
	}
	return true;
}
function doSave(action){
	var data=[];
	var dataEmp=[];
	var dateEmpTypeNew=0;

	if(action==1)
		action="add";

	else{
		action="update";
	}


	if (confirm('Process this transaction?')){
			 if(validate()){
					data['trxn_date'] = $('transaction_date').value;
					var ampm='';
					data['trxn_date'] =data['trxn_date']  + ' ' + $('transaction_time').value + ' ' + $('selAMPM1').value;
					if(action!=1)
                    data['encounter_nr']= $('caseNo').value;
					data['pid'] = $('pid').value;
					data['age'] = $('age').value;//added by art 05/18/2014
					data['purpose_exam'] = $('purpose_exam').value;
					data['purpose_exam_other'] = $('purpose_exam_other').value;
					data['remarks'] = $('remarks').value;
                                        data['smoker_history'] = $J('input[name=smoker_history]:checked').val();
                                        data['drinker_history'] = $J('input[name=drinker_history]:checked').val();
                                        //data['smoker_history'] = $('smoker').value;
                                        //data['drinker_history'] = $('drinker').value;
                                        //data['smoker_history'] = "no";
                                        //data['drinker_history'] = "no";
					if($('refno').value!="")
						data['refno']=$('refno').value;
					if($('ischargeToAgency').checked)
						data['agency_charged'] = 1;
					else
						data['agency_charged'] = 0;
					if($('agency_organization_id').value!=""){
						data['agency_id'] = $('agency_organization_id').value;
					}
					else
						data['agency_id'] ='';
					if($('id_no_status').value=="new"){
						dataEmp['company_id']=$('agency_organization_id').value;
						dataEmp['pid']=  $('pid').value;
						dataEmp['employee_id']=  $('id_no').value;
						dataEmp['position']=  $('position').value;
						dataEmp['job_status']=  $('status').value;
						dateEmpTypeNew=1;
					}
					xajax_saveTransaction(data,action,dataEmp,dateEmpTypeNew);
					
			 }
			 else return false;
	}
	else{
		return false;
	}


}//end function for saving transaction

function setStatus(value,id){
	$('status').value=value;
}


function outputResponse(rep)
{
	alert(rep);
	window.refresh();
	window.cClick();
}

function doCancel(brkfile){
	urlholder=brkfile;
	window.location.href=urlholder;
}

function CheckIsAgency(){
	if($('ischargeToAgency').checked==false){
		$('com_search').disabled=true;
	}else $('com_search').disabled=false;
}

function CheckIsEmployeeNew(){
	if($('id_no_status').value=="new"){
		$('position').disabled=false;
		$('id_no').disabled=false;
		$('statusR1').disabled=false;
		$('statusR2').disabled=false;
		$('statusR3').disabled=false;
		$('statusR4').disabled=false;
		$('statusR5').disabled=false;
		$('statusR6').disabled=false;
	}
}

function CheckIsJobStatus(id){
	var isChecked=false;

	 if($(id).checked==true){
		isChecked=true;
	 }
	 else
		isChecked=false;

	for(var i=1;i<=6;i++){
		var v_tmp="statusR"+i;
		if(v_tmp==id){
				$(v_tmp).checked=isChecked;
				setStatus($(v_tmp).value,$(v_tmp));
		}
		else
			$(v_tmp).checked=!isChecked;
	}
}

function openMedExamChart(canViewMedExamChart, medocs){
	//var CanViewMedExamChart = '<?=$CanViewMedExamChart?>';
	//var medocs = $J("#isMedocs").val();


	var url = 'seg-ic-cert-med-exam-interface1.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value;
	if(medocs || canViewMedExamChart){
		var w = screen.width * 0.8, h = screen.height * 0.8;
		var x = (screen.width/2)-(w/2), y = 0;
		window.open(url,'Medical Exam Chart','width='+w+',height='+h+',menubar=no,resizable=yes,scrollbars=yes,left='+x+',top='+y);
	}else{
		alert('You have no permission to access this feature.');
	}
}

// added by: syboy 07/14/2015
function openMedExamChartFollowUpForm(){
	var CanViewMedExamChart = '<?=$CanViewMedExamChart?>';
    var medocs = '<?=$allpermission?>';

    var url = '../../index.php?r=industrialClinic/medicalChartFollowUp/index/caseNr/'+$('caseNo').value;
    // alert(CanViewMedExamChart);
    // var url = 'seg-ic-cert-med-exam-follow-up-form-interface.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value;
    if (medocs || CanViewMedExamChart == 1) {
    	var w = screen.width * 0.8, h = screen.height * 0.8;
		var x = (screen.width/2)-(w/2), y = 0;
		window.open(url, 'Medical Examination Chart(Follow-up Form)','width='+w+',height='+h+',menubar=no,resizable=yes,scrollbars=yes,left='+x+',top='+y);
    }else{
    	alert('You have no persmission to access this feature.');
    }
}

