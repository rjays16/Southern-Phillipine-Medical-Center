//added by angelo m.  august 26.2010

function showCompany(){
	if($('chkCompany').checked){
		$('frmCompany').style.display="block";
		$('employee-list').style.display="block";
		$('frmEmployee').style.display="none";
		$('txtsearchName').value="";
		$('txtPid').value="";



	}else{
		$('frmCompany').style.display="none";
		$('employee-list').style.display="none";
		$('frmEmployee').style.display="block";
		$('txtsearchName').value="";
		$('txtPid').value="";
	}



}


