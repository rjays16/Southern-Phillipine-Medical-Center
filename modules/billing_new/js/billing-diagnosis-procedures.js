 var HEMO = '90935';
 var NEWBORNPACKAGE = '99432';
 var NEWBORNPACKAGE_2 = '99460';
 var CHEMO = 'CHEMOTHERAPY';
 var DEB = 'DEBRIDEMENT';
// added by art 02/21/15
function GetURLParameter(sParam){
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    } 
}


var caserate1 = GetURLParameter('caserate1');
var caserate2 = GetURLParameter('caserate2');
var finalbill = GetURLParameter('finalbill');
//end art

jQuery(function($) {
        //added by art 02/07/15
        $j().tooltip({
            open: function (event, ui){
                ui.tooltip.css('width','auto');
            }
        });
        //end art

        $j('#btnAddIcpCode').click(function() {

            var icpCode = $j('#icpCode').val();
            var icdDesc = $j('#icpDesc').val();

            var enc = $j('#encounter_nr').val();
            var encdr = $j('#create_id').val();
            // added by art 02/21/15
            if ((finalbill == 1) && (icpCode == caserate1 || icpCode == caserate2)) {
                msg = icpCode == caserate1 ? 'First caserate' : 'Second caserate';
                alert('Add Failed! Code is used in '+msg);
                return false;
            }
            //end art
            // if(icpCode== HEMO || icdDesc.toUpperCase().indexOf(CHEMO)>=0){
            //     while (cnt) {
            //     }
            //     while (isNaN(parseFloat(cnt)) || parseFloat(cnt)<=0) {
            //         var cnt = prompt('Enter number of Sessions:');
            //         if (cnt === null) return false;
            //     }
            //     //$j("#is_special").val(1);
            //     $j("#num_sess").val(((icpCode==HEMO)?cnt:1));
            //     generateOpsDate(cnt);
            // } else if(icdDesc.toUpperCase().indexOf(DEB) >=0 && $j("#is_special").val()==1) {
            //     while (cnt) {
            //     }
            //     while (isNaN(parseFloat(cnt)) || parseFloat(cnt)<=0) {
            //         var cnt = prompt('Enter number of Operation:');
            //         if (cnt === null) return false;
            //     }

            //     $j("#num_sess").val(cnt);
            //     generateOpsDate(cnt);
            // } else {
            //     $j("#num_sess").val(1);
            //     generateOpsDate(1);
            // }

            $j("#num_sess").val(1);
            var ops_flag = generateOpsDate(1);

            if (ops_flag == false) {
                return false;
            };
            
            if(!!icpCode || ops_flag != false){
                if(!!icdDesc){

                // Added by James 1/6/2014
                var code = $j('#icpCode').val();

                for (var key in globalcode) {

                    //added by Nick 05-07-2014
                    var hasDeb = (icdDesc.toUpperCase().indexOf(DEB) >= 0) ? true:false ;
                    var hasChemo = (icdDesc.toUpperCase().indexOf(CHEMO) >= 0) ? true:false ;
                    var hasHemo = (icpCode == HEMO) ? true:false;

                    /*commented by Nick 05-28-2014, allow multiple procedures
                    if (globalcode.hasOwnProperty(key)){
                        if (globalcode[key] == code){
                            if(hasHemo || hasDeb || hasChemo){//added by Nick 05-07-2014 - allow multiple special procedures
                                continue;
                            }else{
                                alert("Procedure already added.  " + hasDeb + "  " + hasChemo);
                                return;
                            }
                        }
                    }
                    */
                } 

                    $j( "#opDateBox").dialog({
                        autoOpen: true,
                        modal:true,
                        height: "auto",
                        width: "auto",
                        resizable: false,
                        show: "fade",
                        hide: "explode",
                        title: "Date Databox",
                        position: "top", 
                        buttons: {
                            "Save": function() 
                            {
                                //Added by EJ 11/13/2014
                                xajax_addDiagProcAdt(enc, 'Added', 'Procedure', icpCode, icdDesc, encdr); 

                                //Edited by James 1/6/2014
                                if($("#laterality_option").val() == 0 && $("#laterality").val() == 1){
                                    alert("Please select a laterality!");
                                    return;
                                }else if($("#laterality").val() == 0){
                                    chkDate();
                                    return;
                                }

                                chkDate();
                            },
                            "Cancel": function() 
                            {
                                $( this ).dialog( "close" );
                            }
                        },
                        open: function(){
                            $j('.ui-button').focus();   
                            $j.each($j('#opDate-body :input').serializeArray(), function(i, field){ 
                                $j( '#'+field.name ).datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    maxDate: 0
                                });
                            });
                        }
                    });
                    
                  
                }else{
                    alert("Please indicate procedure description.");
                }
            }else{
                alert("Please indicate procedure code.");
            }
            
            return false;
        });

        if ($j( "#icdCode" )){
            $j( "#icdCode" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICD10.php?iscode=true", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    // alert(ui.item.label);
                    $j("#icdCode").val(ui.item.id);
                    $j("#icdDesc").val(ui.item.description);              
                }
            });
        }

        if ($j( "#icdDesc" )){
            $j( "#icdDesc" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICD10.php?iscode=false", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    // alert(ui.item.label);
                    $j("#icdCode").val(ui.item.id);
                    $j("#icdDesc").val(ui.item.description);              
                }
            });            
        }

        if ($j( "#icpCode" )){
            var enc = $j('#encounter_nr').val();
            //var is_phic = $j('#is_phic').val();

            $j( "#icpCode" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICPM.php?iscode=true&enc_nr="+enc, request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    $j("#icpCode").val(ui.item.id);
                    $j("#icpDesc").val(ui.item.description);
                    $j("#rvu").val(ui.item.rvu);
                    $j("#multiplier").val(ui.item.multiplier); 
                    $j("#laterality").val(ui.item.laterality);
                    $j("#is_special").val(ui.item.special_case); 
                    $j("#is_delivery").val(ui.item.is_delivery); //Added by EJ 12/11/2014
                    $j("#is_prenatal").val(ui.item.is_prenatal); //Added by EJ 12/11/2014
                    $j("#for_infirmaries").val(ui.item.for_infirmaries); //Added by JEFF 06/27/2018
                    console.log(ui);

                    if(ui.item.removed_from_phic == "1"){
                        alert("This ICP code cannot be used as for PHIC circular no. 008-2015 that take effect last June 01, 2015.");
                        clearICPFields();
                    }
                }
            });
        }

        if ($j( "#icpDesc" )){
            var enc = $j('#encounter_nr').val();
            //var is_phic = $j('#is_phic').val();

            $j( "#icpDesc" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICPM.php?iscode=false&enc_nr="+enc, request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    $j("#icpCode").val(ui.item.id);
                    $j("#icpDesc").val(ui.item.description);
                    $j("#rvu").val(ui.item.rvu);
                    $j("#multiplier").val(ui.item.multiplier);
                    $j("#laterality").val(ui.item.laterality);
                    $j("#is_special").val(ui.item.special_case);
                    $j("#is_delivery").val(ui.item.is_delivery); //Added by EJ 12/11/2014
                    $j("#is_prenatal").val(ui.item.is_prenatal); //Added by EJ 12/11/2014
                    $j("#for_infirmaries").val(ui.item.for_infirmaries); //Added by JEFF 06/27/2018

                    if(ui.item.removed_from_phic == "1"){
                        alert("This ICP code cannot be used as for PHIC circular no. 008-2015 that take effect last June 01, 2015.");
                        clearICPFields();
                    }
                }
            });
        }

        $j('#icdCode').click(function(){
            $j("#icdDesc").val("");
            $j('#icdCode').focus();    
        });

        $j('#icdDesc').click(function(){
            $j("#icdCode").val("");
            $j('#icdDesc').focus();    
        });

        $j('#icpCode').click(function(){
            $j("#icpDesc").val("");
            $j('#icpCode').focus();    
        });

        $j('#icpDesc').click(function(){
            $j("#icpCode").val("");
            $j('#icpDesc').focus();    
        });
    });

var globalcode = {};

function addProcedure(opDate,special_dates,lmpDate,prenatal_dates,$sticker_no) {

    var details = new Object();
    var mul = $j('#multiplier').val();

    details.encNr = $j('#encounter_nr').val();
    details.bDate = $j('#billdate').val();
    details.code = $j('#icpCode').val();
    details.desc = $j('#icpDesc').val();
    details.sticker_no = $sticker_no;
    details.opDate = opDate;
    details.user = $j('#create_id').val();
    details.multiplier = parseInt(mul);
    details.rvu = $j('#rvu').val();
    details.charge = details.multiplier * details.rvu;
    details.laterality =  $j("#laterality_option").val();
    details.sess_num = $j("#num_sess").val();
    details.special_dates = special_dates;
    details.lmp_date = lmpDate;
    details.prenatal_dates = prenatal_dates;
    details.icpDesc = $j('#icdDesc').val();
    xajax_addProcedure(details);

}

//Modified by EJ 12/11/2014
function chkDate(){
    var checker = true;
    var special_dates = '';
    var prenatal_dates = '';
    var lmpDate = null;
    var filterCardNo = '';
    var code = $j('#icpCode').val(); // Added by Johnmel
    $j.each($j('#opDate-body :input').serializeArray(), function(i, field){ 
        if(field.value=='' || !(isValidDate(field.value))){
            checker = false;
        }
        else{
            if(i==0){
                opDate = field.value
            }
            else if(i==1){
                lmpDate = field.value
            }
            else if(i>=2){
                prenatal_dates += field.value+',';
            }
            else if($j("#is_special").val()==1){
                special_dates += field.value+',';
            }
        }
    });
    var operation_dt = new Date(opDate);
    var enc_dt_start = new Date([parent.encounter_date_start.slice(0, 18), ' ', parent.encounter_date_start.slice(18)].join(''));
    var enc_dt_end = new Date([parent.encounter_date_end.slice(0, 18), ' ', parent.encounter_date_end.slice(18)].join(''));
    var operation_dt1 = operation_dt.setHours(23,59,59,999);
    var operation_dt2 = operation_dt.setHours(00,00,00,000);
    if(checker){
        if(operation_dt1<=enc_dt_start||operation_dt2>=enc_dt_end){
            alert("Procedure date must be within the confinement date.");
        }
        else{
            if ($('for_infirmaries').value == '1')
            {
                if( code == NEWBORNPACKAGE || code == NEWBORNPACKAGE_2 )
                {
                    filterCardNo =  prompt("Filter Card Number:");
                }
            }
                if ( filterCardNo.length >= 1 && filterCardNo.length <= 20 )
                {
                    $j( "#opDateBox").dialog( "close" );
                    addProcedure(opDate,special_dates,lmpDate,prenatal_dates,filterCardNo);
                }
                else
                {
                    if( code == NEWBORNPACKAGE  || code == NEWBORNPACKAGE_2 )
                    {
                        alert('Maximum number of Filter Card number is 20 please try again.');
                    }
                    else
                    {
                        $j( "#opDateBox").dialog( "close" );
                        addProcedure(opDate,special_dates,lmpDate,prenatal_dates,filterCardNo);
                    }
                }
        }
    }else{
        alert("Please enter a valid date!");
    }    

}
//added  by kenneth 04/27/2016 for spmc613
function isValidDate(str){
    // STRING FORMAT yyyy-mm-dd
    if(str=="" || str==null){return false;}                             
    
    // m[1] is year 'YYYY' * m[2] is month 'MM' * m[3] is day 'DD'                  
    var m = str.match(/(\d{4})-(\d{2})-(\d{2})/);
    
    // STR IS NOT FIT m IS NOT OBJECT
    if( m === null || typeof m !== 'object'){return false;}             
    
    // CHECK m TYPE
    if (typeof m !== 'object' && m !== null && m.size!==3){return false;}
                
    var day = parseInt(m[3], 10);
    var month = parseInt(m[2], 10);
    var year = parseInt(m[1], 10);
    // YEAR CHECK
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
      
}
//end Kenneth
//added by Nick, 3/4/2014
function edit_icp(code, opEntry, refno){
    $('icp_desc_input'+code+''+opEntry+''+refno).style.display = '';
    $('description'+code+''+opEntry+''+refno).style.display = 'none';
    $('icp_desc_input'+code+''+opEntry+''+refno).focus();
}

function cancel_icp(code,opEntry, refno){
    $('description'+code+''+opEntry+''+refno).style.display = '';
    $('icp_desc_input'+code+''+opEntry+''+refno).style.display = 'none';
}

function updateIcpAltDesc(e, code, opEntry, refno){
    var characterCode;
    var enc_nr   = $('encounter_nr').value;
    var user_id  = $('create_id').value;

    if (e) {
        if(e && e.which) {
            characterCode = e.which;
        }
        else {
            characterCode = e.keyCode;
        }
    }
    else
        characterCode = 13;

    if ( (characterCode == 13) || (isESCPressed(e)) ) {
        var refno = $('icp_refno'+code+''+refno).value;
        var desc = $('icp_desc_input'+code+''+opEntry+''+refno).value;
        xajax_updateIcpDesc(refno,code,desc,opEntry);
        $('description'+code+''+opEntry+''+refno).innerHTML = '<a id="description'+code+''+opEntry+''+refno+'" style="font:bold 12px Arial" onclick="edit_icp('+code+', '+opEntry+', '+refno+')">'+desc+'</a>';
        $('description'+code+''+opEntry+''+refno).style.display = '';
        $('icp_desc_input'+code+''+opEntry+''+refno).style.display = 'none';
    }
}
//end nick

//added by Nick 05-07-2014
function incrementOpCount(code){
    elem = $j("#"+code);
    if(typeof elem.html() != 'undefined'){
        opCount = $j('#'+code+' td:nth-child(3)').html();
        opCount++;
        $j('#'+code+' td:nth-child(3)').html(opCount);
        return true;
    }else{
        return false;
    }
}
function NotifyIrregularity(code,lat,encounter_nr,date) {
    window.parent.billWarningPrompt('Warnings','<center>Site Already Claimed ('+code+')<br/>('+date+') '+encounter_nr+'</center>');
}
function addProcedureToList(data,isFromAdd) {
    var rowSrc;
    var isDelivery; //added  by art 02/03/15 for spmc145
    var elTarget = '#'+data.target;
    var code = data.code;
    var filterCardNo = '';
    var filterCardUi = '';
    var encounter_nr = $j('#encounter_nr').val();
    globalcode[code] = code; // Addded by James 1/6/2014

    //added by Nick 05-07-2014 - for multiple special procedures
    // if(isFromAdd){
    //     if(incrementOpCount(code)){
    //         return;
    //     }
    // }

    if (data) {
        new_code = "'"+data.code +"'";
                if( code == NEWBORNPACKAGE || code == NEWBORNPACKAGE_2 ) {
                    filterCardNo = ' | Filter Card Number: '+data.opSticker;
                    filterCardUi = '<img src="../../images/cashier_edit_3.gif" style="border-right:hidden; cursor:pointer; margin-right:5px;" onclick="updateFCN('+encounter_nr+','+data.code+','+data.opRefno+')" title="Update Filter Card Number">';
                }
        isDelivery = data.isDelivery == true ? '<img src="../../images/btn_edit_small.gif" style="border-right:hidden; cursor:pointer; margin-right:5px;" onclick="editLMP('+encounter_nr+','+new_code+','+data.opRefno+');" title="View/Edit">' : ''; //added by art 02/07/15
        opEntry = "'"+data.opEntry+"'";
        rowSrc = '<tr id='+data.code+''+data.opEntry+'>'+
                        '<td>'+
                            '<span style="font:bold 12px Arial;color:#660000">'+data.code+'</span>'+
                        '</td>'+
                        '<td onclick="edit_icp('+new_code+', '+data.opEntry+', '+data.opRefno+')">'+
                            '<input id="icp_refno'+data.code+''+data.opRefno+'" type="hidden" value="'+data.opRefno+'" />'+
                            '<input id="icp_desc_input'+data.code+''+data.opEntry+''+data.opRefno+'" style="font:bold 12px Arial; display:none; width:100%;" value="'+data.opDesc+'" onblur="cancel_icp('+new_code+','+opEntry+', '+data.opRefno+')" onFocus="this.select();" onkeyup="updateIcpAltDesc(event,'+new_code+', '+opEntry+', '+data.opRefno+')">'+ // //added by Nick, 3/4/2014
                            '<a id="description'+data.code+''+data.opEntry+''+data.opRefno+'" style="font:bold 12px Arial" >'+data.opDesc+filterCardNo+'</a><br/>'+
                            '<input id="description'+data.code+''+data.opEntry+''+data.opRefno+'" type="hidden" value="'+data.opDesc+'" />'+
                        '</td>'+
                        '<td align="center">'+data.opCount+'</td>'+
                        // '<td align="center">'+data.opSticker+'</td>'+
                        '<td align="center">'+data.opDate+'</td>'+
                        '<td align="center">'+'<input id="rvu'+data.code+'" type="hidden" value="'+data.opRVU+'">'+data.opRVU+'</td>'+
                        '<td align="center">'+'<input id="multiplier'+data.code+'" type="hidden" value="'+data.opMultiplier+'">'+data.opMultiplier+'</td>'+
                        '<td align="right">'+'<input id="charge'+data.charge+'" type="hidden" value="'+data.charge+'">'+data.charge+'</td>'+
                        // '<td align="center"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer" onclick="xajax_delICP(\''+id+'\')" ></td></tr>';
                        '<td align="center">'+filterCardUi+isDelivery+'<img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer" onclick="prepDelProc('+new_code+','+opEntry+', '+data.opRefno+'); resetDoctorClaim('+new_code+');"></td>'+
                 '</tr>';
    }
    else {
        rowSrc = '<tr><td colspan="9" style="">No procedure encoded yet ...</td></tr>';
    }

    $j( elTarget ).prepend( rowSrc );
    clearICPFields();

}
//added  by art 02/03/15 for spmc145
function prependEditDateDialog(rowSrc){
    $j('#opDate-body').empty();
    $j('#opDate-body').prepend(rowSrc); 
    $j('.picker').datepicker({dateFormat: 'yy-mm-dd'});
}
//edited  by kenneth 04/27/2016 for spmc613
var error="";
function chkDate2(code){
    counter=0;
    var valid=true;
    var something;
    var cnt = $j("#opDate-body > div").length;
    while(cnt != 0){
        var refno = $j("#refno_"+cnt).val();
        var entry_no = $j("#entry_no_"+cnt).val();
        var op_date = $j("#op_date_"+cnt).val();
        if(!isValidDate(op_date)){valid=false; error="Date of Operation is not valid";}
        var lmp_date = $j("#lmp_date_"+cnt).val();
        if(!isValidDate(lmp_date)){valid=false; error="LMP Date is not valid";}
        var pre = 'prenatal_date_'+cnt;
        var values = [];
        $j("input[name='"+pre+"']").each(function() {
            values.push($j(this).val());
            if(!isValidDate($j(this).val())){valid=false; error="Prenatal Date "+(cnt+1)+" is not valid";}
        });
        var prenate_dates = values.join(", ");
        var operation_dt = new Date(op_date);
        var enc_dt_start = new Date([parent.encounter_date_start.slice(0, 18), ' ', parent.encounter_date_start.slice(18)].join(''));
        var enc_dt_end = new Date([parent.encounter_date_end.slice(0, 18), ' ', parent.encounter_date_end.slice(18)].join(''));
        var operation_dt1 = operation_dt.setHours(23,59,59,999);
        var operation_dt2 = operation_dt.setHours(00,00,00,000);
        if(operation_dt1<=enc_dt_start||operation_dt2>=enc_dt_end){
            alert("Procedure date must be within the confinement date.");
            valid=false;
        }
        if(valid)xajax_updateLmpDate(op_date,lmp_date,prenate_dates,code,refno,entry_no);
        cnt--;
    }
    return valid;
}
//end kenneth
//added  by art 02/03/15 for spmc145
function editLMP(enc,code,refno){
    $j( "#opDateBox").dialog({
                        autoOpen: true,
                        modal:true,
                        height: "auto",
                        width: "auto",
                        resizable: false,
                        show: "fade",
                        hide: "explode",
                        title: "Edit Date",
                        position: "top", 
                        buttons: {
                            "Update": function() 
                            {
                               if (chkDate2(code)) {
                                $j( this ).dialog( "close" );
                                alert('Successfully Updated!');
                                window.location.reload(false); 
                               }
                               else alert('Please enter valid date. '+error);
                            },
                            "Cancel": function() 
                            {
                                $j( this ).dialog( "close" );
                            }
                        },
                        open: function(){
                            $j('.ui-button').focus();
                            xajax_getProcedureDetails(enc,code,refno);
                        },
                    });

}

function clearProcList(){
    $j('#ProcedureList-body').empty();
}

function rmvProcRow(id, opEntry, refno){
    // $j('#'+id).remove();
    //added by Nick 05-07-2014
    var enc_nr = $j('#encounter_nr').val();

    var description = $('description'+id+''+opEntry+''+refno).innerHTML;
    var encdr = $j('#create_id').val();
    var code = id;

    // opCount = $j('#'+id+''+opEntry+' td:nth-child(3)').html();
    // if(opCount>1){
    //     opCount--;
    //     $j('#'+id+''+opEntry+' td:nth-child(3)').html(opCount);
    // }else{
        $j('#'+code+''+opEntry).remove();
    // }

    xajax_addDiagProcAdt(enc_nr, 'Deleted', 'Procedure', code, description, encdr); //Added by EJ 11/13/2014
    //end nick
    alert("Procedure successfully deleted!");  
}

function prepDelProc(code,opEntry, refno){

    var details = new Object();
    var msg = '';
    details.enc = $j('#encounter_nr').val();
    details.bdate = $j('#billdate').val();
    details.fdate = $j('#admission_dt').val();
    details.code = code;
    details.opEntry = opEntry;
    details.refno = refno;

    var description = $('description'+code+''+opEntry+''+refno).innerHTML;
    var encdr = $j('#create_id').val();
    // added by art 02/21/15
    if ((finalbill == 1) && (code == caserate1 || code == caserate2)) {
        msg = code == caserate1 ? 'First caserate' : 'Second caserate';
        alert('Delete Failed! Code is used in '+msg);
    }else{
    for (var key in globalcode) {
        if (globalcode.hasOwnProperty(key))
            if (globalcode[key] == code){
                delete globalcode[key];
            return;
        }
    }

    xajax_deleteProcedure(details);
}

}

//Added by Christian 01-22-20
function resetDoctorClaim(code) {
    var bill_nr = GetURLParameter('bill_nr');
    var encounter_nr = $j('#encounter_nr').val();
    var rateType = '';
    var firstCode = caserate1;
    var secondCode = caserate2;

    var firstIndex = firstCode.indexOf("_");
    if(firstIndex!=-1) {
        var firstLength = firstCode.length;
        firstCode = firstCode.slice(0,firstIndex);
    }

    var secondIndex = secondCode.indexOf("_");
    if(secondIndex!=-1) {
        var secondLength = secondCode.length;
        secondCode = secondCode.slice(0,secondIndex);
    }
    
    rateType = firstCode==code ? 'first_claim' : '';
    if(!Boolean(rateType))
        rateType = secondCode==code ? 'second_claim' : '';

    xajax_rmvDoctorClaim(encounter_nr, bill_nr, rateType);
}
//end Christian 01-22-20

function clearICPFields() {
    $j('#icpCode').val("");
    $j('#icpDesc').val("");
}

//Modified by EJ 12/11/2014
function generateOpsDate(cnt)
{   
    var is_delivery = $j('#is_delivery').val();
    var is_prenatal = $j('#is_prenatal').val();
    var rowSrc = '';
    var count = 0;
    var elTarget = '#opDate-body';
    $j('#opDate-body').empty();
    for (var i=0;i<cnt;i++) {
        rowSrc +='<tr id="opDateBox-date-'+i+'">'+
                    '<td width="*" align="left">'+                            
                    '    <strong> Date of Operation'+((cnt>1)? parseInt(i+1) : '')+'</strong>'+
                    '</td>'+
                    '<td width="*" align="left">'+  
                    '    <input type="text" id="op_date_'+i+'" name="op_date_'+i+'" maxlength="10" size="10" />'+
                    '</td>'+
                '</tr>';

        if (is_delivery == true) {
            rowSrc +='<tr id="lmp-date-'+i+'">'+
                    '<td width="*" align="left">'+                            
                    '    <strong> LMP Date </strong>'+
                    '</td>'+
                    '<td width="*" align="left">'+  
                    '    <input type="text" id="lmp_date_'+i+'" name="lmp_date_'+i+'" maxlength="10" size="10" />'+
                    '</td>'+
                '</tr>';
        };

        if (is_prenatal == true) {

            do {
                count = prompt("Enter how many pre-natal dates");

                if (count == null) {
                    return false;
                }
                else if (!Number(count)) {
                    alert("Please enter valid data");
                }
                else if (count < 4) {
                    alert("There has to be atleast 4 pre-natal dates")
                }
            }

            while(count < 4 || !Number(count))

            for (var i = 0; i < count; i++) {
                rowSrc +='<tr id="prenatal-date-'+i+'">'+
                        '<td width="*" align="left">'+                            
                        '    <strong> Pre-natal Date #'+((count>1)? parseInt(i+1) : '')+'</strong>'+
                        '</td>'+
                        '<td width="*" align="left">'+  
                        '    <input type="text" id="prenatal_date_'+i+'" name="prenatal_date_'+i+'" maxlength="10" size="10" />'+
                        '</td>'+
                    '</tr>';
            };
        };

        if($j("#laterality").val() == 1){
            rowSrc +=   '<tr id="opDateBox-laterality">'+
                        '    <td width="*" align="left">'+                            
                        '        <strong> Laterality </strong>'+
                        '    </td>'+
                        '    <td width="*" align="left">'+  
                        '        <select id="laterality_option">'+
                        '            <option value="0">-Select-</option>'+
                        '            <option value="L">Left</option>'+
                        '            <option value="R">Right</option>'+
                        '            <option value="B">Both</option>'+
                        '        </select>'+
                        '    </td>'+
                        '</tr>'
        }
    }

    $j( elTarget ).prepend( rowSrc );
}

function updateFCN(enc,code,refno){

    var enc = enc;
    var code = code;
    var refno = refno;
    var ok;

        var fcn = prompt("New Filter Card Number:");

    if (fcn != "") {
        if (fcn == null) {
            // Do nothing lang swa!...
        }else{

            if ( fcn.length >= 1 && fcn.length <= 20 ) { // Added by Johnmel
                   ok =  xajax_saveFilterCardNumber(enc,code,refno,fcn);
                   if (ok) {
                        alert("Success: Filter Card Number successfully saved!");
                        location.reload();
                    }
                } else {
                   alert('Maximum number of Filter Card number is 20 please try again.');
                }
        }
    }
    else{
        alert("System: Filter Card Number cannot be empty!");
        this.updateFCN(enc,code,refno);
    }
}