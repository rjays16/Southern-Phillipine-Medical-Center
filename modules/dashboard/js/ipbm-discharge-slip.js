/**
 * Created by: Jeff Ponteras
 * Date: May 17, 2018
 * Purpose: For IPBM Discharge Slip JavaScript Functions
 */

jQuery(function($) {
        $( "#date_follow_up_input" ).datepicker({
            dateFormat: "mm-dd-yy",
            changeMonth: true,
            changeYear: true
        });
        $( "#date_input" ).datepicker({
            dateFormat: "mm-dd-yy",
            changeMonth: true,
            changeYear: true
        });

        $( "#cu_day" ).datepicker({
            dateFormat: "mm-dd-yy",
            changeMonth: true,
            changeYear: true
        });

        if(document.getElementById('select_department').value != '' && document.getElementById("select_physician").value == ''){
            getDoctors();
        }
    });

    // jeff for generation of report
    function printDischargeSlip(encounter_nr){
        if (window.showModalDialog){  
            window.showModalDialog("ipbm-discharge-slip-pdf.php?encounter_nr="+encounter_nr+"");
        }else{
            window.open("ipbm-discharge-slip-pdf.php?encounter_nr="+encounter_nr,"modal, width=600,height=1000,menubar=no,resizable=yes,scrollbars=no");
        }
    }

    function printDischargeSlipIPD(encounter_nr){
        if (window.showModalDialog){  

            window.showModalDialog("ipbm-discharge-slip-ipd-pdf.php?encounter_nr="+encounter_nr+"");
        }else{
            window.open("ipbm-discharge-slip-ipd-pdf.php?encounter_nr="+encounter_nr,"modal, width=600,height=1000,menubar=no,resizable=yes,scrollbars=no");
        }
    }

    function getDoctors() {
        var dept_nr = document.getElementById('select_department').value;
        var dept_nr = dept_nr.split(",");

        var element = document.getElementById("select_physician");
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
        
        xajax_setDoctors(dept_nr[0]);
    }

    function getDepartments() {
        var doc_nr = document.getElementById('select_physician').value;

        xajax_setDepartments(doc_nr);
    }

    var js_time = "";
    function js_setTime(jstime){
        js_time = jstime;
    }

    function js_getTime(){
        return js_time;
    }

    function validateTime(S) {
        return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
    }

    function checkDate(input_date,element) {

        var date = new Date();
        var month = date.getMonth()+1;
        var day = date.getDate();
        var year = date.getFullYear();
        if(day<10) {
            day='0'+day;
        } 

        if(month<10) {
            month='0'+month
        }

        var date_now = month+"-"+day+"-"+year;
        var check_date = input_date.value;

        validateDate(check_date,date_now,element);
    }

    function validateDate(date,valid_date,element) {
       
        var date_format = /^(\d{1,2})(\/|-)(\d{1,2})\2(\d{4})$/;
        var msg = "Date is not in a valid format.";

        var matchArray = date.match(date_format); 

        if (matchArray == null) {
            if (date == '') {
                document.getElementById('date_follow_up_input').value = '';
            }
            else {
                alert(msg);
                document.getElementById(element).value = valid_date;
            }
        }

        month = matchArray[1]; 
        day = matchArray[3];
        year = matchArray[4];


        if (month < 1 || month > 12) { 
            alert(msg);
            document.getElementById(element).value = valid_date;
        }

        if (day < 1 || day > 31) {
            alert(msg);
            document.getElementById(element).value = valid_date;
        }

        if ((month==4 || month==6 || month==9 || month==11) && day==31) {
            alert(msg);
            document.getElementById(element).value = valid_date;
        }

        if (month == 2) {
            var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
            if (day>29 || (day==29 && !isleap)) {
                alert(msg);
                document.getElementById(element).value = valid_date;
            }
        }

        if (day.charAt(0) == '0') {
            day = day.charAt(1);
        }

    }

    function compareDate(date) {
        if (date) {
            var follow_up_date = date.value;
        }   
        else {
            var follow_up_date = document.getElementById('date_follow_up_input').value;
        }
       
        var discharge_date = document.getElementById('date_input').value;

        if (follow_up_date<discharge_date) {
            alert("Follow up date is later than Discharge date!");
            document.getElementById('date_follow_up_input').value = discharge_date; 
        };
    }

    function setFormatTime(thisTime,AMPM){
        var stime = thisTime.value;
        var hour, minute;
        var ftime ="";
        var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
        var f2 = /^[0-9]\:[0-5][0-9]$/;
        var jtime = "";

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
        }else   if((hour > 12)&&(hour < 24)){
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
    }

    function setDoctors(name, personell_nr) {
        var values = document.createElement("option");
        values.setAttribute("value", personell_nr);

        var option = document.createTextNode(name.toUpperCase());
        values.appendChild(option);

        document.getElementById("select_physician").appendChild(values);
    }

    function setDepartments(name, location_nr) {

        var values = document.createElement("option");
        values.setAttribute("value", location_nr);

        var option = document.createTextNode(name.toUpperCase());
        values.appendChild(option);

        document.getElementById("select_department").appendChild(values);
    }