
//call autosave
/*setInterval("autosave()",3000);

function autosave(){
    alert('autosave');
}*/

function preset(){
  
    $J("#parameter_list").find("div").each(function(){
        //alert(this.id);
        $J("#"+this.id).hide();
        $J("#T"+this.id).hide();
    });

    $J('#Search').bind('keyup', function() {
      //if ((event.keyCode == 13)&&(isValidSearch($J('#Search').val()))) getReports();
      getReports();
    });
    
    $J('#datefrom').bind('blur', function() {
        IsValidDate(this,$J('#date_format').val());
    });
    
    $J('#dateto').bind('blur', function() {
        IsValidDate(this,$J('#date_format').val());
    });

}

function billing_insurance_control() {
    var nph =$J('#param_billing_insurance').val();
    if(nph =='nph') $J('#membership_category').hide();
    else  $J('#membership_category').show();
  }   
    
    
function isValidSearch(key){
    if (key.length >= 2)
        return true;
    else
        return false;
}
//set other parameters, aside from date period
//edited by Jarel 05/03/2013
function setParameter(reportid){
    var list = $('parameter_list'+reportid);
    var dBody=list.getElementsByTagName("tbody")[0];
    var param = new Array();
    var paramlabel = 'param_';
    var paramvalue;
    var paramid;
    var params;
    var cont;
        
    if (dBody) { 
        var dParams = dBody.getElementsByTagName("span");
        if (dParams) {
            for (i=0;i<dParams.length;i++) {
                paramid = paramlabel+dParams[i].id;
                if (paramid.match('time')){
                    var start_time = $J('#'+paramid+'_from').val();
                    var end_time = $J('#'+paramid+'_to').val();
                    paramvalue = '';
                    // time period ==> from and to
                    if ((start_time == "") && (end_time == "")){
                        cont = true;
                    }else if ((start_time != "") || (end_time != "")){
                        if ((start_time != "") && (end_time != "")){
                             var start = start_time+" "+$J('#'+paramid+'_meridian_from').val();
                             var end = end_time+" "+$J('#'+paramid+'_meridian_to').val();
                             //set a temp date
                             var dtStart = new Date('1/1/2012 '+start);
                             var dtEnd = new Date('1/1/2012 '+end);
                             diff_in_ms = dtEnd - dtStart;
                            var fromDateSelect = $J('#datefrom').val();
                            var toDateSelect = $J('#dateto').val();
                            if (diff_in_ms < 0 && fromDateSelect == toDateSelect ){
                                 alert("Invalid Time Range!\n Start Time cannot be after End Time!")
                                 $(paramid+'_from').focus();
                                 cont = false;
                                 return false;
                                 break;
                            }else{
                                //from
                                paramvalue_from = start
                                paramid_from = paramid+'_from';
                                
                                params = paramid_from+'--'+paramvalue_from;
                                param.push(params);
                                //to
                                paramvalue_to = end
                                paramid_to = paramid+'_to';
                                
                                params = paramid_to+'--'+paramvalue_to;
                                param.push(params);
                                
                                cont = true;
                               
                            }   
                        }else{
                            alert("Invalid Time Range!\n Start Time or End Time cannot be blank!")
                            if ($J('#'+paramid+'_from').val() == "")
                                $(paramid+'_from').focus();
                            if ($J('#'+paramid+'_to').val() == "")
                                $(paramid+'_to').focus();
                            cont = false;
                            break;
                        }
                    }     
                }else if (paramid.match('membership_category_1')){
                    var checkedCbs = document.querySelectorAll('#membership_category input[type="checkbox"]:checked');
                    // var ids = [];
                    // alert(checkedCbs[0].id);
                    if(checkedCbs.length==1){
                        params = $J('#'+checkedCbs[0].id).val()
                    }
                    else if(checkedCbs.length>1){
                        var counter = 1;
                        var limiter = checkedCbs.length;
                        params = $J('#'+checkedCbs[0].id).val()
                        document.getElementById(checkedCbs[0].id).checked = false;
                        while(counter < limiter){
                            params += "__"+$J('#'+checkedCbs[counter].id).val();
                            document.getElementById(checkedCbs[counter].id).checked = false;
                            counter++;
                        }
                    }
                    else{
                        params="all";
                    }
                    param.push('param_membership_category--'+params);
                    cont = true; 
                }else if (paramid.match('mem_category_1')){
                    var checkedCbs = document.querySelectorAll('#mem_category input[type="checkbox"]:checked');
                    // var ids = [];
                    // alert(checkedCbs[0].id);
                    if(checkedCbs.length==1){
                        params = $J('#'+checkedCbs[0].id).val()
                    }
                    else if(checkedCbs.length>1){
                        var counter = 1;
                        var limiter = checkedCbs.length;
                        params = $J('#'+checkedCbs[0].id).val()
                        document.getElementById(checkedCbs[0].id).checked = false;
                        while(counter < limiter){
                            params += "__"+$J('#'+checkedCbs[counter].id).val();
                            document.getElementById(checkedCbs[counter].id).checked = false;
                            counter++;
                        }
                    }
                    else{
                        params="all";
                    }
                    param.push('param_mem_category--'+params);
                    cont = true; 
                }else{
                    paramvalue = $J('#'+paramid).val();
                    if (paramvalue){
                        params = paramid+'--'+paramvalue;
                        param.push(params);
                    }
                    cont = true;
                }
                /*if (paramvalue){
                    params = paramid+'--'+paramvalue;
                    param.push(params);
                }*/ 
            }
        }
    }
    
    if (cont)
        return param;
    else
        return param;    
    
}

function __showReport(reportid, with_template, query_in_jasper,repformat) {
    var param;
    
    /*console.log("reportID:"+reportid+" repformat:"+repformat+" param:"+param);
    alert("repformat:"+$J.trim(repformat));*/

    frmdte = $J('#datefrom').val();   // format is mm/dd/YYYY
    todte  = $J('#dateto').val();
    dept_nr = $J('#SourceDepartment').val();
    session_user = $J('#session_user').val();
    if ((frmdte=='') || (todte=='')){
       alert('Please specify report period!');
       return false;
        
    }else{
        nleft = (screen.width - 800)/2;
        ntop = (screen.height - 640)/2;

        //added by gelie 11-01-2015    
        startMonth = new Date(frmdte).getMonth();
        endMonth = new Date(todte).getMonth();
        startYear = new Date(frmdte).getYear();
        endYear = new Date(todte).getYear();
        //end gelie

        frmdte = dateFormat(frmdte,'isoDate');
        todte = dateFormat(todte,'isoDate');
        
        frmdte = frmdte + ' 00:00:00';
        todte  = todte + ' 00:00:00';

        fromdate = getDateFromFormat(frmdte, 'yyyy-MM-dd HH:mm:ss')/1000;
        todate =  getDateFromFormat(todte, 'yyyy-MM-dd HH:mm:ss')/1000;
        
        if (fromdate > todate) {
            alert("Invalid Date Range!\nStart Date cannot be after End Date!")
            return false;
        }
       
        //commented by Mark April 5, 2017
         //added by gelie 11-01-2015
        // else if((reportid.substr(0,8) == "ER_Daily") && (startMonth != endMonth)){
        //     alert("To view ER Daily Statistical Reports,\nStart Date and End Date must be on the same month!")
        //     return false;
        // }
        //end gelie
        //end commented by Mark April 5, 2017
        else if((reportid.substr(0,8) == "ER_Daily") && (startYear != endYear)){
            alert("To view ER Daily Statistical Reports,\nStart Date and End Date must be on the same year!")
            return false;
        }
        /** Static condition. */
        else if ((reportid == 'Billing_Transmittal_Based_On_PHIC_Category') && (startYear != endYear)) {
            alert("To view Total # of Transmittal (per PHIC Category),\nStart Date and End Date must be on the same year!");
            return false;
        }
        else{
            param = setParameter(reportid);//edited by Jarel 05/03/2013
         
            if (param){
                if (with_template==1){ 
                    var openWin;
                    //$J.blockUI({ message: "<h1>Generating the report is in progress...</h1>" })

                        if($J.trim(repformat)=='graph'){
                            if(connect_to_instance==1){
                                openWin = window.open(report_portal+'/modules/reports/loadingreport.php?repfile=show_report_graph&reportid='+reportid+'&repformat='+repformat+'&personnel_nr='+spersonnel_nr+'&ptoken='+pToken+'&from_date='+fromdate+'&to_date='+todate+'&param='+param+'&dept_nr='+dept_nr, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                            }else{
                                $J.post('../reports/sessionreport.php',{'link':'../reports/show_report_graph.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param+'&session_user='+session_user},function(data){
                                    openWin = window.open('../reports/show_report_graph.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                                });
                            }
                        }else{
                            if (query_in_jasper==1){
                                if(connect_to_instance==1){
                                    openWin = window.open(report_portal+'/modules/reports/loadingreport.php?repfile=show_report_jasper&reportid='+reportid+'&repformat='+repformat+'&personnel_nr='+spersonnel_nr+'&ptoken='+pToken+'&from_date='+fromdate+'&to_date='+todate+'&param='+param+'&dept_nr='+dept_nr, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                                }else{
                                    $J.post('../reports/sessionreport.php',{'link':'../reports/show_report_jasper.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param+'&dept_nr='+dept_nr+'&session_user='+session_user},function(data){
                                        openWin = window.open('../reports/show_report_jasper.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                                    });
                                }
                                //openWin = window.open('../reports/show_report_jasper.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                            }else{
                                if(connect_to_instance==1){
                                    openWin = window.open(report_portal+'/modules/reports/loadingreport.php?repfile=show_report&reportid='+reportid+'&repformat='+repformat+'&personnel_nr='+spersonnel_nr+'&ptoken='+pToken+'&from_date='+fromdate+'&to_date='+todate+'&param='+param+'&dept_nr='+dept_nr, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                                }else{
                                    $J.post('../reports/sessionreport.php',{'link':'../reports/show_report.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param+'&dept_nr='+dept_nr+'&session_user='+session_user},function(data){
                                        openWin = window.open('../reports/show_report.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                                    });
                                }
                                //openWin = window.open('../reports/show_report.php?reportid='+reportid+'&repformat='+repformat+'&from_date='+fromdate+'&to_date='+todate+'&param='+param, '_blank', "toolbar=no, status=no, menubar=no, width=800, height=640, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
                            }
                        }
                        
                    
                    //$J.unblockUI();
                    
                }else{
                    alert('The template of this report is not yet AVAILABLE! \n Please inform the system administrator...');
                } 
            }       
        }    
    }    
}

function getReports() {
    searchSource();
}

function HidePane(){
    
    if ($J('#parameter_list').is(":visible")){
        $J('#parameter_list').hide();
        //$J('#collapse_trigger').removeClass('icon minus');
        //$J('#collapse_trigger').addClass('icon plus');    
        $J('#col').attr('class','icon plus');
    }else{
        $J('#parameter_list').show();     
        //$J('#collapse_trigger').removeClass('icon plus');    
        //$J('#collapse_trigger').addClass('icon minus');
        $J('#col').attr('class','icon minus');
    }
    
}

function setMuniCity(mun_nr, mun_name) {
    $J("#param_munnr").val(mun_nr);
    $J("#param1_munnr").val(mun_name);
}

function setProvince(prov_nr, prov_name) {
    $J("#param_provnr").val(prov_nr);
    $J("#param1_provnr").val(prov_name);
}

function clearNr(id) {
    
    if ($J('#'+id).val() == '') {
        switch (id) {
            case "param1_brgynr":
                $J('#param_brgynr').val('');
                break;
                
            case "param1_munnr":
                $J('#param_munnr').val('');  
                
                if ($J('#param1_brgynr').val()!=''){
                    $J('#param_brgynr').val('');
                    $J('#param1_brgynr').val('');
                }    
                    
                break;     
                
            case "param1_provnr":
                $J('#param_provnr').val('');
                
                if ($J('#param1_brgynr').val()!=''){
                    $J('#param_brgynr').val('');
                    $J('#param1_brgynr').val('');
                }    
                    
                if ($J('#param1_munnr').val()!=''){
                    $J('#param_munnr').val('');    
                    $J('#param1_munnr').val('');    
                }    
                
                break;  
        }
    }
}

function clearNr2(id) {
    
    switch (id) {
        case "param1_brgynr":
            $J('#param_brgynr').val('');
            break;
            
        case "param1_munnr":
            $J('#param_munnr').val('');  
            
            if ($J('#param1_brgynr').val()!=''){
                $J('#param_brgynr').val('');
                $J('#param1_brgynr').val('');
            }    
                
            break;     
            
        case "param1_provnr":
            $J('#param_provnr').val('');
            
            if ($J('#param1_brgynr').val()!=''){
                $J('#param_brgynr').val('');
                $J('#param1_brgynr').val('');
            }    
                
            if ($J('#param1_munnr').val()!=''){
                $J('#param_munnr').val('');    
                $J('#param1_munnr').val('');    
            }    
            
            break;  
    }
}

function validate(id){
    switch (id) {
        case "param1_brgynr":
            
            if (($J('#param1_munnr').val()=='') && ($J('#param1_provnr').val() != '')){
                alert('Enter a Municipality or City first.');
                $J('#param1_munnr').focus();
                return false;
            }/*else if ($J('#param1_provnr').is(":empty")){
                alert('Enter a Province first.');
                $J('#param1_provnr').focus();
            }*/
            
            break;
            
        case "param1_munnr":
            if ($J('#param1_provnr').val()==''){
                alert('Enter a Province first.');
                $J('#param1_provnr').focus();
                return false;            
            }     
            
            break;     
    }
    return true;
}

//Added by Jarel 05/03/2013
function showAddParams(reportid, with_template, query_in_jasper,repformat){
    
    frmdte = $J('#datefrom').val();   // format is mm/dd/YYYY
    todte  = $J('#dateto').val();
    
    if ((frmdte=='') || (todte=='')){
       alert('Please specify report period!');
       return false;
        
    }else{
        frmdte = dateFormat(frmdte,'isoDate');
        todte = dateFormat(todte,'isoDate');
        
        frmdte = frmdte + ' 00:00:00';
        todte  = todte + ' 00:00:00';

        fromdate = getDateFromFormat(frmdte, 'yyyy-MM-dd HH:mm:ss')/1000;
        todate =  getDateFromFormat(todte, 'yyyy-MM-dd HH:mm:ss')/1000;
        
        if (fromdate > todate) {
            alert("Invalid Date Range!\nStart Date cannot be after End Date!")
            return false;
        }else{
              $J( "#addParameters"+reportid).dialog({
                autoOpen: true,
                modal:true,
                height: "auto",
                width: "auto",
                show: "blind",
                hide: "explode",
                title: "Additional Parameters",
                position: "top", 
                buttons: {
                        GENERATE: function() {
                            __showReport(reportid, with_template, query_in_jasper,repformat);
                            $J( this ).dialog( "close" );
                        },
                        CANCEL: function() {
                            $J( this ).dialog( "close" );
                        }
                },
                open: function(){
                    // for ICD
                    if ($J( "#param1_icd10" )){
                        $J( "#param1_icd10" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_ICD10.php?iscode="+$(reportid+'_paramCheck_icd10').checked+"", request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            //alert(ui.item.id);
                            $('param_icd10').value = ui.item.id;
                        }
                        });
                        
                        $J('#paramCheck_icd10').click(function(){
                            $('param1_icd10').value = '';
                            $('param1_icd10').focus();    
                        });
                    }   
                    //---------------
                    
                    
                    // for ICP
                    if ($J( "#param1_icpm" )){
                        $J( "#param1_icpm" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_ICPM.php?iscode="+$(reportid+'_paramCheck_icpm').checked+"", request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            //alert(ui.item.id);
                            $('param_icpm').value = ui.item.id;
                        }
                        });
                        
                        $J('#paramCheck_icpm').click(function(){
                            $('param1_icpm').value = '';
                            $('param1_icpm').focus();    
                        });    
                    }   
                    // ---------------
                    
                    //added by VAN 03-02-2013
                    //DEMOGRAPHICS
                    //for province
                    if ($J( "#param1_provnr" )){
                        $J( "#param1_provnr" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_Province.php", request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            $('param_provnr').value = ui.item.id;
                        }
                        });
                    }
                    
                    //for municipal and city
                    if ($J( "#param1_munnr" )){
                        $J( "#param1_munnr" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_Municipality.php?prov_nr="+$J('#param_provnr').val(), request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            $('param_munnr').value = ui.item.id;
                            xajax_getProvince(ui.item.id);
                        }
                        });
                    }
                    
                    //for Barangay
                    if ($J( "#param1_brgynr" )){
                        $J( "#param1_brgynr" ).autocomplete({
                        minLength: 2,
                        source: function( request, response ) {
                            $J.getJSON( "ajax/ajax_Barangay.php?prov_nr="+$J('#param_provnr').val()+"&mun_nr="+$J('#param_munnr').val(), request, function( data, status, xhr ) {
                                response( data );
                            });
                        },
                        select: function( event, ui ) {
                            $('param_brgynr').value = ui.item.id;
                            xajax_getMuniCityandProv(ui.item.id);
                        }
                        });
                    }

                    //for ICD10 and ICPM codes
                    if($J("param_codetype")){
                        $J("#icd10").attr('hidden', true);
                        $J("#icpm").attr('hidden', true);
                        $J("#icd10_1").attr('hidden', true);
                        $J("#icpm_1").attr('hidden', true);

                        if($J("#icd10").next().is('br')){
                            $J("#icd10").next().remove();
                        }

                        $J("#param_codetype").change(function(){
                            if(this.value == 'icd'){
                                $J("#icd10").attr('hidden', false);
                                $J("#icpm").attr('hidden', true);
                                $J("#icd10_1").attr('hidden', false);
                                $J("#icpm_1").attr('hidden', true);
                                $J("#param1_icd10").focus();
                            }
                            else if(this.value == 'icp'){
                                $J("#icd10").attr('hidden', true);
                                $J("#icpm").attr('hidden', false);
                                $J("#icd10_1").attr('hidden', true);
                                $J("#icpm_1").attr('hidden', false);
                                $J("#param1_icd10").focus();

                                if($J("#icd10").next().is('br')){
                                    $J("#icd10").next().remove();
                                }
                            }
                            else if(this.value == ''){
                                $J("#icd10").attr('hidden', true);
                                $J("#icpm").attr('hidden', true);
                                $J("#icd10_1").attr('hidden', true);
                                $J("#icpm_1").attr('hidden', true);
                            }
                        });
                    }

                    //added by Gervie 01/04/2016
                    //Index of Radiology

                    //For Level 02
                    if ($J( "#param_index_lvl1" )){
                        $J( "#param_index_lvl1" ).change(function(){
                            xajax_getIndexLevel2(this.value);
                        });
                    }

                    //For Level 03
                    if ($J( "#param_index_lvl2" )){
                        $J( "#param_index_lvl2" ).change(function(){
                            xajax_getIndexLevel3(this.value);
                        });
                    }

                    //For Level 04
                    if ($J( "#param_index_lvl3" )){
                        $J( "#param_index_lvl3" ).change(function(){
                            xajax_getIndexLevel4(this.value);
                        });
                    }

                    // added by: syboy 03/15/2016 : meow
                    if ($J('#param_type_grant')) {
                        $J('#param_type_grant').change(function(){
                            xajax_getGuarantor(this.value);
                        });
                    }
                    // ended syboy

                    if ($J('#param_patient_type')) {
                        $J('#param_patient_type').change(function(){
                            if(reportid == 'PH_list_meds'){
                            }else{
                                xajax_getDeptWard(this.value);
                            }
                        });
                    }

                    if($J('#param_phar_charge_type')){
                        xajax_chargeName();
                    }

                    // alert(reportid +"-----"+with_template+"-----"+ query_in_jasper+"-----"+ repformat);
                },
                close:function(){
                    renameID2(reportid);
                } 
            });   
        }
          
    }


}

function index_lvl2(details){
    details = JSON.parse(details);
    $J('#param_index_lvl2').empty();

    len = Object.keys(details).length;

    if(len > 0) {
        $J('#param_index_lvl2').append('<option value="">-Select a Index of Radiology Level 2-</option>');
        $J.each(details, function(index, obj){
            $J('#param_index_lvl2').append('<option value="'+index+'">'+obj+'</option>');
        });
    }

    $J('#param_index_lvl2').trigger('change');
}

function index_lvl3(details){
    details = JSON.parse(details);
    $J('#param_index_lvl3').empty();

    len = Object.keys(details).length;

    if(len > 0) {
        $J('#param_index_lvl3').append('<option value="">-Select a Index of Radiology Level 3-</option>');
        $J.each(details, function(index, obj){
            $J('#param_index_lvl3').append('<option value="'+index+'">'+obj+'</option>');
        });
    }

    $J('#param_index_lvl3').trigger('change');
}

function index_lvl4(details){
    details = JSON.parse(details);
    $J('#param_index_lvl4').empty();

    len = Object.keys(details).length;

    if(len > 0) {
        $J('#param_index_lvl4').append('<option value="">-Select a Index of Radiology Level 4-</option>');
        $J.each(details, function(index, obj){
            $J('#param_index_lvl4').append('<option value="'+index+'">'+obj+'</option>');
        });
    }

    $J('#param_index_lvl4').trigger('change');
}
function dept_ward(details){
    details = JSON.parse(details);
        $J('#param_dept_ward').empty();

    len = Object.keys(details).length;

    if(len > 0) {
        $J('#param_dept_ward').append('<option value="">-Select Department/Ward-</option>');
        $J.each(details, function(index, obj){
            $J('#param_dept_ward').append('<option value="'+index+'">'+obj+'</option>');
        });
    }

    $J('#param_dept_ward').trigger('change');
}

function renameID(reportid){
    var id;
    var elems = $J("#addParameters"+reportid+" :input").serializeArray(); 
    $J.each(elems, function(i, field){ 
       id = field.name.replace(reportid+"_","");
       $J("#"+field.name).attr('id',id);        
    });
}

function renameID2(reportid){
    var id;
    var elems = $J("#addParameters"+reportid+" :input").serializeArray(); 
    $J.each(elems, function(i, field){
     if(field.name.indexOf("Check") > 0){
        $J("#"+field.name).attr('checked','');
     }else{
        id = field.name.replace(reportid+"_","");
        $J("#"+id).val(''); 
        $J("#"+id).attr('id',field.name);  
     }
       
    });
}

function genReport(reportid, with_template, query_in_jasper,repformat,param){
    if(param=='0' || reportid == 'EHR_User_Log_Monitoring'){
        __showReport(reportid, with_template, query_in_jasper,repformat);    
    }else{
        renameID(reportid);
        showAddParams(reportid, with_template, query_in_jasper,repformat);
    }
}

// added by: syboy 03/15/2016 : meow
//Edit by Marvin Cortes 05/04/2016
function guarantor(details){
    
    details = JSON.parse(details);
    $J('#param_type_guarantor').empty();
    var counter = 0;
    if (details.length == 0) {
        $J('#param_type_guarantor').append('<option value="">-Select Guarantor Name-</option>');
    }else{
        $J('#param_type_guarantor').append('<option value="">-Select Guarantor Name-</option>');
            $J.each(details, function(index, obj){
            counter++;
            $J('#param_type_guarantor').append('<option value='+ index +'>'+ obj +'</option>');
            });
            
        if(counter > 1 )$J('#param_type_guarantor').append('<option value="all">All</option>');
    }

    $J('#param_type_guarantor').trigger("change");

}
// ended syboy

function getICPICDP(){
        var datefrom  = $J('#datefrom').val();
        var dateto = $J('#dateto').val();
        var mem_category = [];
        $J.each($J("input[name='TOP15_ICP_ICD_Detailed_param_mem_category']:checked"), function(){
            mem_category.push($J(this).val());
        });
        xajax_getICDICP(mem_category,datefrom,dateto);
}

function get_icp(details) {
     details = JSON.parse(details);
    $J('#param_billing_icp').empty();
    len = Object.keys(details).length;

    if(len > 0) {
        $J('#param_billing_icp').append('<option value="">-Select TOP 15 ICP-</option>');
        $J.each(details, function(index, obj){
            $J('#param_billing_icp').append('<option value="'+index+'">'+index+'-'+obj+'</option>');
        });
    }

    $J('#param_billing_icp').trigger('change');
}

function get_icd(details) {
     details = JSON.parse(details);
    $J('#param_billing_icd').empty();
    len = Object.keys(details).length;

    if(len > 0) {
        $J('#param_billing_icd').append('<option value="">-Select TOP 15 ICD-</option>');
        $J.each(details, function(index, obj){
            $J('#param_billing_icd').append('<option value="'+index+'">'+index+'-'+obj+'</option>');
        });
    }
    alert("Generated ICD and ICP");

    $J('#param_billing_icd').trigger('change');
}


