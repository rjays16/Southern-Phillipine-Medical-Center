
$('#orientation_start_time').timepicki();
$('#orientation_end_time').timepicki();

function change(value){
	document.getElementById("txtBoxTitle").value ="";
}

function buttonsubmit(){
	
	var emp_id=document.getElementById('hidn').value;
	var dateor=document.getElementById("specificdate").value;
	var timeorstart=document.getElementById("orientation_start_time").value;
	var timeorend=document.getElementById("orientation_end_time").value;
	var moduleor=document.getElementById("module").value;
	var titlemod=document.getElementById("txtBoxTitle").value;
	var venue=document.getElementById("venSel").value;
	var checkEmpty=	document.getElementById("hidnID").value;

	if (dateor=="" || timeorstart=="" || moduleor=="" || titlemod=="" || venue=="" || timeorend=="" ){
		alert("Fill in missing fields");
	}
	else{

		var hours = Number(timeorstart.match(/^(\d+)/)[1]);
		var minutes = Number(timeorstart.match(/:(\d+)/)[1]);
		var AMPM = timeorstart.match(/\s(.*)$/)[1];
		if(AMPM == "PM" && hours<12) hours = hours+12;
		if(AMPM == "AM" && hours==12) hours = hours-12;
		var sHours = hours.toString();
		var sMinutes = minutes.toString();
		if(sHours<10) sHours = "0" + sHours;
		if(sMinutes<10) sMinutes = "0" + sMinutes;
		var time_strt = sHours+ ":" + sMinutes;

		var hours_end = Number(timeorend.match(/^(\d+)/)[1]);
		var minutes_end = Number(timeorend.match(/:(\d+)/)[1]);
		var AMPM_end = timeorend.match(/\s(.*)$/)[1];
		if(AMPM_end == "PM" && hours_end<12) hours_end = hours_end+12;
		if(AMPM_end == "AM" && hours_end==12) hours_end = hours_end-12;
		var Hours_end = hours_end.toString();
		var Minutes_end = minutes_end.toString();
		if(Hours_end<10) Hours_end = "0" + Hours_end;
		if(Minutes_end<10) Minutes_end = "0" + Minutes_end;
		var time_end = Hours_end+ ":" + Minutes_end;

			var a = time_strt.split(':');
			var b = time_end.split(':');
			var sec_a = (+a[0]) * 60 * 60 + (+a[1]) * 60;
			var sec_b = (+b[0]) * 60 * 60 + (+b[1]) * 60;
			var interval = (sec_a-sec_b)/60;
			var minss = -30;
		
		if (checkEmpty==""){
			if(time_strt != time_end){
				if(time_strt < time_end){
					if(confirm("Are you sure you want to add orientation?") == true){
						xajax_saveOrientation(emp_id, time_strt,time_end,dateor,moduleor,titlemod,venue);
						alert("Orientation Added");
						ReloadWindow();
					}
				}else{
					alert("Ending time must not be earlier than the Orientation Time.");
				}
			}else{
				alert("Orientation time must not be the same.");
			}
		}else{
			if (confirm("Are you sure you want to update changes?") == true){
				
				if(time_strt != time_end){
					if(time_strt < time_end){
						if(interval < minss){
							xajax_updateFromOrientation(checkEmpty,time_strt,time_end,dateor,moduleor,titlemod,venue);
					        alert("Orientation Updated");
					        ReloadWindow();
						}else{
							alert("Orientation time between start and end must not be less than 30 minutes.")
						}
					}else{
						alert("Ending time must not be earlier than the Orientation Time.");
					}
							
				}
				else{
					alert("Orientation time must not be the same.");
				}			
			}else{
				this.close();
			}	
		}
	}
}

function removeFromList($orientation_list_id){
	var orientation_id=$orientation_list_id;
		//alert(orientation_id);
	if (confirm("Do you really want to delete?") == true){
		xajax_removeFromList(orientation_id);
		alert("Orientation Deleted");
		ReloadWindow();
	}else {
		this.close();
	}

}	

function updateFromList(me,orientation_list_id, venue_id){

	var fields=me.id.split('^');
	var date=document.getElementById('specificdate').value=fields[0];
	var time=document.getElementById('orientation_start_time').value=fields[1];
	var time=document.getElementById('orientation_end_time').value=fields[2];
	var module=document.getElementById('module').value=fields[3];
	var title=document.getElementById('txtBoxTitle').value=fields[4];
	var venue=document.getElementById("venSel").value=venue_id;
	document.getElementById("hidnID").value=fields[6];
	var orientation_id=orientation_list_id;
		
}

function ReloadWindow(){
	window.location.href=window.location.href;
}

function trimString(objct,allow_in_between_spaces){
//	alert("inside frunction trimString: objct = '"+objct+"'");
	objct.value.replace(/^\s+|\s+$/g,"");   //Removes ONLY leading and trailing whitespaces
	if (allow_in_between_spaces)
		objct.value = objct.value.replace(/\s+/g," ");   //ONLY a single whitespace appears in between tokens/words 			
	else
		objct.value = objct.value.replace(/\s+/g,"");   //Removes ONLY in-between whitespaces
}

function setFormatTime(thisTime,AMPM){
	//	var time = $('time_text_d');
	var stime = thisTime.value;
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";
	
	trimString(thisTime);
	//alert(thisTime+"  "+AMPM)
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
	js_setTime(jtime);
	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

    if (ftime.length==4)
        ftime = '0'+ftime;
            
	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;	
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
}

function js_setTime(jstime){
	js_time = jstime;
}