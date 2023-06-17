/**
 * created by Nick 07-15-2014
 */
var Procedures = new Object();

Procedures.getPatientSpecialProcedures = function(procedures){
    var encounter_nr = $j('#encounter_nr').val();
    $j.ajax({
        url : 'ajax/ajax-special-procedure-details.php',
        dataType : 'json',
        type : 'get',
        data : {
            'action' : 'get_special_procedures',
            'encounter_nr' : encounter_nr
        },
        success : function(data){
            Procedures.specialProcedures = data;
            Procedures.procedures = procedures;
            Procedures.showSpecialProcedures();
        },
        error : function(x,y){
            alert(y);
        }
    });
}

Procedures.setSpecialProcedureDetails = function(code,is_availed){
    var encounter_nr = $j('#encounter_nr').val();
    $j.ajax({
        url : 'ajax/ajax-special-procedure-details.php',
        dataType : 'json',
        type : 'get',
        data : {
            'action' : 'update_special_procedure',
            'encounter_nr' : encounter_nr,
            'code' : code,
            'is_availed' : (is_availed) ? is_availed : 0
        },
        success : function(data){
            if(data.result != true){
                alert("Something went wrong");
            }
        },
        error : function(x,y){
            alert(y);
        }
    });
}

Procedures.showSpecialProcedures = function(){
    var data = Procedures.specialProcedures;
    var list = '';
    var ul = $j('#special_procedure_details');
    ul.html('');
    for(var i=0;i<data.length;i++){
        var procedure = data[i];
        var value = (procedure.is_availed == true) ? "checked" : "";
        var is_final = (procedure.is_final == 1) ? "disabled" : "";
        list = '<li id="li'+procedure.package_id+'" class="li_procedure">'+
                    '<label>'+
                        '<input id="chk'+procedure.package_id+'" type="checkbox" '+value+' '+is_final+' onchange="checkChange(\''+i+'\')"/>'+
                        '<strong>'+procedure.description+'</strong>'+
                    '</label>'+
                '</li>';
        ul.append(list);
    }
    Procedures.init();
}

Procedures.init = function(){
    resetProcedureDetailList();
    $j('#first_rate').on('change', function() {
        changeCase('1');
        resetProcedureDetailList();
    });
    $j('#second_rate').on('change', function() {
        changeCase('2');
        resetProcedureDetailList();
    });
}

function resetProcedureDetailList(){
    hideAllProcedureDetails();
    showSelectedCaseRates();
}

function hideAllProcedureDetails(){
    $j(".li_procedure").hide();
}

function getProcedure(code){
    var data = Procedures.specialProcedures;
    for(var i=-0;i<data.length;i++){
        if(data[i].package_id == code){
            return data[i];
        }
    }
    return false;
}

function showProcedureDetails(code){
    var f = $j("#first_rate option:selected");
    var s = $j("#second_rate option:selected");
    var procedure = getProcedure(code);
    var prefix;

    if(procedure){
        var isAvailed = $('chk'+procedure.package_id).checked;
        if(isAvailed){
            if(procedure.is_for_availed){
                prefix = 'sp_';
            }else{
                prefix = 'orig_';
            }
        }else{
            if(procedure.is_for_availed){
                prefix = 'orig_';
            }else{
                prefix = 'sp_';
            }
        }

        var reference;
        if(f.attr('id') == code){
            reference = $j('#first_rate option:contains('+code+')');
            setAttributes(reference,prefix,procedure);
            changeCase('1');
        }else if(s.attr('id') == code){
            reference = $j('#second_rate option:contains('+code+')');
            setAttributes(reference,prefix,procedure);
            changeCase('2');
        }
    }
    $j("#li"+code).show();
}

function showSelectedCaseRates(){
    showProcedureDetails($j("#first_rate option:selected").attr("id"));
    showProcedureDetails($j("#second_rate option:selected").attr("id"));
}

function checkChange(index,is_availed){
    var procedure = Procedures.specialProcedures[index];
    var select = $('chk'+procedure.package_id);
    var code = procedure.package_id;
    var laterality = procedure.laterality;
    var f = $j("#first_rate option:selected");
    var s = $j("#second_rate option:selected");
    var prefix = '';
    var expression;

    if(is_availed){
        expression = is_availed;
    }else{
        expression = select.checked;
    }

    if(expression){
        if(procedure.is_for_availed){
            prefix = 'sp_';
        }else{
            prefix = 'orig_';
        }
    }else{
        if(procedure.is_for_availed){
            prefix = 'orig_';
        }else{
            prefix = 'sp_';
        }
    }

    var reference;
    var temp_lat = '';
    if(laterality){
        temp_lat = '_'+laterality;
    }
    if(f.attr('id') == code+temp_lat){
        reference = $j('#first_rate option:contains('+code+temp_lat+')');
        setAttributes(reference,prefix,procedure);
        changeCase('1');
    }else if(s.attr('id') == code+temp_lat){
        reference = $j('#second_rate option:contains('+code+temp_lat+')');
        setAttributes(reference,prefix,procedure);
        changeCase('2');
    }

    Procedures.setSpecialProcedureDetails(code,(select.checked) ? 1 : 0);
}

function setAttributes(reference,prefix,procedure){
    var pf = reference.attr(prefix+'pf');

    reference.attr('value',reference.attr(prefix+'amnt'));
    reference.attr('value_hf',reference.attr(prefix+'hf'));
    reference.attr('value_pf',pf);

    if(procedure.case_type=='p'){
        reference.attr('value_D3',pf * 0.6);
        reference.attr('value_D4',pf *0.4);
    } else {
        reference.attr('value_D1',pf);
    }
}