function init() {
    var encounter_nr = $('encounter_nr').value;
    var pid = $('pid').value;
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
        this.cancel();
    };
    var handleCancel = function() {
        this.cancel();
    };
}

function Load_Note(details){
    window.parent.$('datetime').value = details.dt;
    if(!details.ward == 0){
        window.parent.$('ward_nr').value = details.ward;
    }
    if(!details.diagnosis == ""){
        window.parent.$('diagnosis').value = details.diagnosis;
    }
    if(details.referral == 'internal'){
        window.parent.$('internal').checked = true;
    }else{
        window.parent.$('external').checked = true;
    }
    window.parent.$('informant').value = details.informant;
    window.parent.$('reltopatient').value = details.relationship;
    window.parent.$('purpose').value = details.purpose;
    window.parent.$('action_taken').value = details.action_taken;
    window.parent.$('recommendation').value = details.recommendation;
    window.parent.$('note_id').value = details.id;

    window.parent.$('pn_submit').style.display = 'none';
    window.parent.$('pn_update').style.display = '';

    //added by VAS 10/04/2018
    window.parent.$J('.ui-dialog-content:visible').dialog('close');
}    

function formatNumber(num,dec){
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}



function formatValue(num,dec){
    var nf = new NumberFormat(num.value);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    num.value = nf.toFormatted();
}

function computeMonthly(){  
    var income = $('m_income2').value.replace(',','');
    var otherIncome = $('other_income').value.replace(',','');
    var dep = document.getElementsByName('DepIncome[]');
    var totDepIncome = 0; 
    var total = 0;
    for (var i=0; i<dep.length; i++) {
        totDepIncome += parseFloat(dep[i].value);
    }
    
    if(income=='')
        income = 0;
    if(otherIncome=='')
        otherIncome = 0;
        
    total = totDepIncome + parseFloat(otherIncome) + parseFloat(income);                                     
    
    $('total_income').value = formatNumber(total,2);                             
    computeCapita();
    computeTotal();   
}

function computeCapita(){
    var percapita;
    var nodep = $('nr_dep').value;
    var mincome = $('total_income').value.replace(',','');
    if (((mincome)&&(mincome!=0)) && ((nodep)&&(nodep)!=0)){
        percapita = parseFloat(mincome) / parseFloat(nodep);
        $('capita_income').value = formatNumber(percapita,2);     
    }else{ 
        $('capita_income').value = formatNumber(mincome,2);     
    }
}

function computeTotal(){
    var total=0, hauz_lot=0, food=0, light = 0;
    var water=0, transport=0, other=0;
    var fuel=0, househelp=0, educ=0, medical=0 , clothing =0 , plan =0;
    if ($F('living_amount'))
        hauz_lot = parseFloat($('living_amount').value.replace(',',''));
    
    if (($F('food_amount')))
        food = parseFloat($('food_amount').value.replace(',',''));
    
    if (($F('light_amount')))
        light = parseFloat($('light_amount').value.replace(',',''));
    
    if (($F('water_amount')))
        water = parseFloat($('water_amount').value.replace(',',''));
        
    if ($F('trans_amount'))
        transport = parseFloat($('trans_amount').value.replace(',',''));
        
    if (($F('fuel_amount')))
        fuel = parseFloat($('fuel_amount').value.replace(',',''));
                                             
    if (($F('househelp_amount')))
        househelp = parseFloat($('househelp_amount').value.replace(',',''));
        
    if (($F('educ_amount')))
        educ = parseFloat($('educ_amount').value.replace(',',''));
        
    if (($F('medical_amount')))
        medical = parseFloat($('medical_amount').value.replace(',',''));        

    if (($F('clothing_amount')))
        clothing = parseFloat($('clothing_amount').value.replace(',',''));
        
    if (($F('plan_amount')))
        plan = parseFloat($('plan_amount').value.replace(',',''));
        
    if (($F('others_amount')))
        other = parseFloat($('others_amount').value.replace(',','')); 
    
    total = hauz_lot + food + light + water + transport + other + fuel + househelp + educ + medical + clothing + plan;
    
    $('total_expenses').value = formatNumber(total,2);
}

function saveDeMeData(){
    var data = new Array();
    var fields = jQuery("#mswd_part1 :input").serializeArray(); 
    var isPWD = /PWD/i; 
    jQuery.each(fields, function(i, field){
       data[field.name] = field.value;                    
    });
    xajax_checkPWDExist(data);  
   setTimeout(function(){  
   var checkPWDExist =  jQuery('input[name="checkifPWDExist"]').val();
    if(formValidation()){
        if(($('service_code').value== 'D' || $('service_code').value== 'Inf') && $('subservice_code').value==''){
            alert('Please select sub classification.');
            $('subservice_code').focus();    
        }else if($('sub_modifier_select').value=='' && $('modifier_select').value!=''){
            alert('Please select sub modifier.');
            $('sub_modifier_select').focus();     
        }
        else if((isPWD.test($('subservice_code').value)) && jQuery('input[name="pwd_id"]').val() == '') {
            alert('Please enter PWD ID Number.');
            jQuery('input[name="pwd_id"]').focus();
        }
        else if((isPWD.test($('subservice_code').value)) && jQuery('input[name="pwd_expiration"]').val() == '') {
            alert('Please enter PWD ID expiration date.');
            jQuery('input[name="pwd_expiration"]').focus();
        }
        else{
            if(checkPWDExist!='1'){
            alert("PWD ID is already used by another patient.");   
            }
            else{
            xajax_ProcessDeMeData(data);
            saveDependents();    
                
        } 
             
    }    
}
}, 1000);
   
}

function saveDependents(){
    var data_dep = new Array();
    var fields = jQuery("#dependents_form :input").serializeArray(); 
    jQuery.each(fields, function(i, field){ 
       data_dep[field.name]= field.value;          
    });
   xajax_addDependent(data_dep,$('pid').value,$('encounter_nr').value);    
}

function saveSocialFunctioning(){
    var data = new Array();
    var fields = jQuery("#social_form :input").serializeArray(); 
    jQuery.each(fields, function(i, field){ 
       data[field.name]= field.value; 
                   
    });
    
    if($('no_social_problem').checked){
        data['no_social_problem'] = 1;
    }else{
        data['no_social_problem'] = 0;
    }                        

    xajax_saveSocialFunctioning(data,$('pid').value,$('encounter_nr').value,J('#autosave').val());    
}

function saveSocialProblem(){
    var data = new Array();
    var fields = jQuery("#environment-form :input").serializeArray(); 
    jQuery.each(fields, function(i, field){ 
       data[field.name]= field.value; 
    });
    
    xajax_saveSocialProblem(data,$('pid').value,$('encounter_nr').value);    
}

function saveSocialFindings(){
    var data = new Array();
    var fields = jQuery("#social-findings :input").serializeArray();
    var problems = '';
    var topics = ''; 
    jQuery.each(fields, function(i, field){ 
       data[field.name]= field.value;
       var str = field.name; 
       var firstWord = '';                         
       firstWord = str.split("_");         
       
       if(firstWord[1]=='problems'){       
            problems += firstWord[0]+',';              
       }
       if(firstWord[1]=='topics'){       
            topics += firstWord[0]+',';              
       }
    });
    
    if($('counseling_done').checked){
        data['counseling_done'] = 1;
    }else{
        data['counseling_done'] = 0;
    } 
    data['problem'] = problems;
    data['topics'] = topics;
    data['pid'] = $('pid').value;
    data['encounter_nr'] = $('encounter_nr').value;           
    
    xajax_saveSocialFindings(data);    
}

function saveSocialCase(){
    var data = new Array();
    var fields = jQuery("#mswd_part3 :input").serializeArray();
    var planning = ''; 
    var provision = ''; 
    var outgoing = ''; 
    var incoming = ''; 
    var leading = '';
    var socialworkservices = ''; 
    var dischargeservices = ''; 
    var cases = ''; 
    var followup = '';
    var coordination = '';
    var documentation = '';
        
    jQuery.each(fields, function(i, field){ 
       data[field.name]= field.value;
       var str = field.name; 
       var firstWord = '';                         
       firstWord = str.split("_");         
       
       if(firstWord[1]=='planning'){       
            planning += firstWord[0]+',';              
       }else if(firstWord[1]=='provisionofdiscount'){       
            provision += firstWord[0]+',';              
       }else if(firstWord[1]=='outgoingreferral'){       
            outgoing += firstWord[0]+',';              
       }else if(firstWord[1]=='in-comingreferral'){       
            incoming += firstWord[0]+',';              
       }else if(firstWord[1]=='10leadingreasons'){       
            leading += firstWord[0]+',';              
       }else if(firstWord[1]=='socialworkservices'){       
            socialworkservices += firstWord[0]+',';                     
       }else if(firstWord[1]=='dischargeservices'){       
            dischargeservices += firstWord[0]+',';              
       }else if(firstWord[1]=='case'){       
            cases += firstWord[0]+',';                     
       }else if(firstWord[1]=='followup'){       
            followup += firstWord[0]+',';              
       }else if(firstWord[1]=='coordination' && firstWord[0] != 'other' ){       
            coordination += firstWord[0]+',';              
       }else if(firstWord[1]=='documentation' && firstWord[0] != 'other' ){       
            documentation += firstWord[0]+',';              
       }                                   
    });
    
    data['planning'] = planning;
    data['provision'] = provision;
    data['outgoing'] = outgoing;
    data['incoming'] = incoming;
    data['leading'] = leading;   
    data['cases'] = cases;
    data['dischargeservices'] = dischargeservices;
    data['socialworkservices'] = socialworkservices;
    data['followup'] = followup;
    data['documentation'] = documentation;
    data['coordination'] = coordination;
    data['pid'] = $('pid').value;           
    data['encounter_nr'] = $('encounter_nr').value;
    
    xajax_saveSocialCase(data);
}


function saveAssessment(auto)
{
    J('#autosave').val(auto);
    saveSocialFindings();
    saveSocialProblem();
    saveSocialFunctioning();

}


function formValidation(){
    var noError;
    var fields = jQuery("#mswd_part1 :input").serializeArray(); 
    jQuery.each(fields, function(i, field){
        var str = field.name;
        var id = str.slice(0,-1);
        var lastChar = str[str.length -1];
        if(lastChar=='*' && field.value == ''){
            $(id).focus();
            alert('All fields with * are required');
             noError = false;
             return false;    
        }else{
            noError = true;
        }
    });
    return noError;
}

function validateDepList(){
    if($('name_dep').value==''){
        alert("Please enter the name of dependent");
        $('name_dep').focus();
        return false;    
    }else if($('age_dep').value==''){
        alert("Please enter the age of dependent");
        $('age_dep').focus();
        return false;    
    }else if($('cstatus_dep').value==''){
        alert("Please select the civil status of dependent");
        $('cstatus_dep').focus();
        return false;
    }else if($('relation_dep').value==''){
        alert("Please select the relation of dependent");
        $('relation_dep').focus();
        return false;
    }else if($('educ_dep_select').value==''){
        alert("Please select the educational attainment of dependent");
        $('educ_dep_select').focus();
        return false;
    }else if($('mincome_dep').value==''){
        alert("Please enter the monthly income of dependent");
        $('mincome_dep').focus();
        return false;
    }else{
        return true;
    }
}

// commented out by Nick 1-29-2016
//function getDependent(){
//    xajax_populateDependent($('pid').value,$('encounter_nr').value,$('mode').value);
//}

function populateDependent(details){
    var list=$('dependents_form'), dRows, dBody, rowSrc;
    if (list) {    
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if(details){
                var id = details.id;
                var pid = $('pid').value; 
                var name = details.name.toUpperCase();
                var age = details.age;
                var status = details.status;
                var relation = details.relation;
                var educ = details.educ;
                var occu = details.occu;
                var income = details.income;    
                var totDepIncome = parseFloat(income.replace(',',''));
                $('dep_list_id').value = id;
                $('nr_dep').value = parseFloat(details.dep_nr) + 1;            
                rowSrc = '<tr id="id'+id+'">'+
                            '<input type="hidden"  name="DepIncome[]" id="DepIncome'+id+'" value="'+totDepIncome+'">'+
                            '<input type="hidden"  name="DepName_'+id+'" id="DepName_'+id+'" value="'+name+'">'+
                            '<input type="hidden"  name="DepAge_'+id+'" id="DepAge_'+id+'" value="'+age+'">'+
                            '<input type="hidden"  name="DepStatus_'+id+'" id="DepStatus_'+id+'" value="'+status+'">'+
                            '<input type="hidden"  name="DepRelation_'+id+'" id="DepRelation_'+id+'" value="'+relation+'">'+
                            '<input type="hidden"  name="DepEduc_'+id+'" id="DepEduc_'+id+'" value="'+educ+'">'+
                            '<input type="hidden"  name="DepOccu_'+id+'" id="DepOccu_'+id+'" value="'+occu+'">'+
                            '<input type="hidden"  name="IncomeDep_'+id+'" id="IncomeDep_'+id+'" value="'+income+'">'+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="name">'+name+'</div></td> '+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="age">'+age+'</div></td>'+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="status">'+status+'</div></td>'+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="relation">'+relation+'</div></td>'+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="educ">'+educ+'</div></td>'+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="occu">'+occu+'</div></td>'+
                            '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="income">'+income+'</div></td>'+
                            '<td><button id="editbtn" name="editbtn" class="add-dependent icon-only" type="submit" onclick="showEditDependentDialog('+id+');"><span class="ui-icon ui-icon-document" style="display:inline-block;"></span></button></td>'+
                            '<td><button id="minbtn" name="minbtn" class="add-dependent icon-only" type="submit" onclick="removeDependent('+pid+','+id+');"><span class="ui-icon ui-icon-circle-close" style="display:inline-block;"></span></button></td>'+                    
                         '</tr>';    
            }else{
                rowSrc = '';
            }
            dBody.innerHTML += rowSrc;
            computeMonthly();            
        }

         
}

function showDependentDialog(){
    jQuery( "#dependent" ).dialog({
        autoOpen: true,
        modal:true,
        height: 380,
        width: 350,
        show: "blind",
        hide: "explode",
        title: "ADD RELATIONS",
        position: "top",
        buttons: {
                ADD: function() {                         
                    if(validateDepList()){
                        addDependent();
                        $('name_dep').value = '';
                        $('age_dep').value= '';
                        $('mincome_dep').value = '';  
                        $('cstatus_dep').selectedIndex = 0;
                        $('relation_dep').selectedIndex = 0;
                        $('source_dep').selectedIndex = 0;
                        $('ot_source_dep').value = '';
                        $('educ_dep_select').selectedIndex = 0;
                        $('name_dep').focus();    
                    }
                },
                CANCEL: function(){
                    jQuery(this).dialog( "close" );
                }
        },
        open:function (){
            $('name_dep').value = '';
                        $('age_dep').value= '';
                        $('mincome_dep').value = '';  
                        $('cstatus_dep').selectedIndex = 0;
                        $('relation_dep').selectedIndex = 0;
                        $('source_dep').selectedIndex = 0;
                        $('ot_source_dep').value = '';
                        $('educ_dep_select').selectedIndex = 0;
                        $('name_dep').focus();
        },
        close: function(){
        jQuery(this).dialog( "close" );
        }
        
    });
}

//added by art 12/13/14
//open dialog for editing relation
function showEditDependentDialog(id){
    jQuery( "#dependent" ).dialog({
        autoOpen: true,
        modal:true,
        height: 380,
        width: 350,
        show: "blind",
        hide: "explode",
        title: "EDIT RELATIONS",
        position: "top",
        buttons: {
                SAVE: function() {                         
                    if(validateDepList()){
                        editDependent(id)
                        jQuery(this).dialog( "close" ); 
                    }
                },
                CANCEL: function(){  
                        jQuery(this).dialog( "close" );
                }
        },
        open: function(){
            jQuery('#name_dep').val(jQuery('#DepName_'+id).val());
            jQuery('#age_dep').val(jQuery('#DepAge_'+id).val());
            jQuery('#mincome_dep').val(jQuery('#IncomeDep_'+id).val()); 
            jQuery('#cstatus_dep').val(jQuery('#DepStatus_'+id).val());
            jQuery('#relation_dep').val(jQuery('#DepRelation_'+id).val());
            jQuery('#source_dep').val(jQuery('#DepOccu_'+id).val());
            //jQuery('#ot_source_dep').val(jQuery('#DepName_'+id).val());
            jQuery('#educ_dep_select').val(jQuery('#DepEduc_'+id).val());
        },
        close: function(){
                jQuery(this).dialog( "close" );
        }
        
    });
}

//added by art 12/13/14
//assign new values to relation
function editDependent(id){
    var name =      jQuery('#name_dep').val();
    var age =       jQuery('#age_dep').val();
    var status =    jQuery('#cstatus_dep').val();
    var relation =  jQuery('#relation_dep').val();
    var educ =      jQuery('#educ_dep_select').val();
    var occu =      jQuery('#source_dep').val();
    var mincome =   jQuery('#mincome_dep').val();

    jQuery('#DepName_'+id).val(name);
    jQuery('#DepAge_'+id).val(age);
    jQuery('#DepStatus_'+id).val(status);
    jQuery('#DepRelation_'+id).val(relation);
    jQuery('#DepEduc_'+id).val(educ);
    jQuery('#DepOccu_'+id).val(occu);
    jQuery('#IncomeDep_'+id).val(mincome);
    jQuery('tr#id'+id+' div#name').text(name);
    jQuery('tr#id'+id+' div#age').text(age);
    jQuery('tr#id'+id+' div#status').text(status);
    jQuery('tr#id'+id+' div#relation').text(relation);
    jQuery('tr#id'+id+' div#educ').text(educ);
    jQuery('tr#id'+id+' div#occu').text(occu);
    jQuery('tr#id'+id+' div#income').text(mincome);

}


function addDependent(){
    var list=$('dependents_form'), dRows, dBody, rowSrc;
    var i;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        var name = $('name_dep').value.toUpperCase();
        var age = $('age_dep').value;
        var status = $('cstatus_dep').value;
        var relation = $('relation_dep').value;
        var educ = $('educ_dep_select').value;
        var occu = ($('source_dep').value != 'Others') ? $('source_dep').value : $('ot_source_dep').value;
        var mincome = formatNumber($('mincome_dep').value,2); 
        var id = parseFloat($('dep_list_id').value) + 1;
        $('dep_list_id').value = id;
        $('nr_dep').value = parseFloat($('nr_dep').value) + 1;  
        rowSrc = '<tr id="id'+id+'">'+
                    '<input type="hidden"  name="DepIncome[]" id="DepIncome'+id+'" value="'+$('mincome_dep').value+'">'+
                    '<input type="hidden"  name="DepName_'+id+'" id="DepName_'+id+'" value="'+name+'">'+
                    '<input type="hidden"  name="DepAge_'+id+'" id="DepAge_'+id+'" value="'+age+'">'+
                    '<input type="hidden"  name="DepStatus_'+id+'" id="DepStatus_'+id+'" value="'+status+'">'+
                    '<input type="hidden"  name="DepRelation_'+id+'" id="DepRelation_'+id+'" value="'+relation+'">'+
                    '<input type="hidden"  name="DepEduc_'+id+'" id="DepEduc_'+id+'" value="'+educ+'">'+
                    '<input type="hidden"  name="DepOccu_'+id+'" id="DepOccu_'+id+'" value="'+occu+'">'+
                    '<input type="hidden"  name="IncomeDep_'+id+'" id="IncomeDep_'+id+'" value="'+mincome+'">'+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="name">'+name+'</div></td> '+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="age">'+age+'</div></td>'+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="status">'+status+'</div></td>'+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="relation">'+relation+'</div></td>'+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="educ">'+educ+'</div></td>'+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="occu">'+occu+'</div></td>'+
                    '<td><div class="input" style="font:bold 12px Arial; text-align:left" id="income">'+mincome+'</div></td>'+
                    '<td><button id="editbtn" name="editbtn" class="add-dependent icon-only" type="submit" onclick="showEditDependentDialog('+id+');"><span class="ui-icon ui-icon-document" style="display:inline-block;"></span></button></td>'+
                    '<td><button id="minbtn" name="minbtn" class="add-dependent icon-only" type="submit" onclick="removeDependent('+$('pid').value+','+id+');"><span class="ui-icon ui-icon-circle-close" style="display:inline-block;"></span></button></td>'+                    
                 '</tr>';
      
        dBody.innerHTML += rowSrc; 
        computeMonthly(); 
    }    
}

function removeDependent(pid,id){
    var table = $('dependents_form').getElementsByTagName('tbody').item(0);
    table.removeChild($('id'+id));     
    $('nr_dep').value = parseFloat($('nr_dep').value) - 1; 
    computeMonthly(); 
}

function ajxClearOptions(tagId) {
    var optionsList;
    var el;       
        el =$(tagId);
        if (el) {
            optionsList = el.getElementsByTagName('OPTION');
            for (var i=optionsList.length-1;i>=0;i--) {
                optionsList[i].parentNode.removeChild(optionsList[i]);
            }
        }
}

function ajxAddOption( tagId, text, value, desc) {
    var grpEl;
    grpEl=$(tagId);

    if (grpEl) {
        var opt = new Option( text, value );
        opt.id = value;
        if (desc!=0){
            opt.setAttribute("onMouseover", "return overlib($(\'submod"+value+"\').value, CAPTION,\'Details\', BORDER,0,TEXTPADDING,5, TEXTFONTCLASS,\'oltxt\', CAPTIONFONTCLASS,\'olcap\',WIDTH,400, FGCLASS,\'olfgPopup\', FIXX,10, FIXY,10);");
            opt.setAttribute("onMouseout", "mouseOut();");
        }
        grpEl.appendChild(opt);
    }
    var optionsList = grpEl.getElementsByTagName('OPTION');
    
    var el = document.createElement('input');
    el.type = 'hidden';
    el.name = 'submod'+value;
    el.id = 'submod'+value;
    el.setAttribute("value", desc);
    grpEl.appendChild(el);
}

function mouseOver(tagId, value){
    var modifier;
    var elTarget = $(tagId);
    if(elTarget){
        
        idname = $(tagId).id+value;
        
        if ($(tagId).id=='personal_circumstance')
            modifier = "Personal Circumstances";
        else if ($(tagId).id=='community_situation')
            modifier = "Community Situations";
        else if ($(tagId).id=='nature_of_disease')
            modifier = "Nature of Illness/Disease";    

        return overlib( $(idname).value, CAPTION,modifier, BORDER,0,
            TEXTPADDING,5, TEXTFONTCLASS,'oltxt', CAPTIONFONTCLASS,'olcap',
            WIDTH,400, FGCLASS,'olfgPopup', FIXX,10, FIXY,10
        );
    }
}

//edited by Daryl 10/22/2013
// remove  || id =='C3')
function getSubClass(id){
    // alert(id);
    if(id!=''){
        $('subservice_code').disabled = false;
    }else{                             
        $('subservice_code').disabled = true;
    }
    
    if(id != ''){
        $('additional_support').selectedIndex = 0;
    }
    showSCID(id);
    xajax_getSubClass(id);
}

function changeIndex(id){
    if($('additional_support').value != ''){
        $('service_code').selectedIndex = 0;
        $('subservice_code').selectedIndex = 0;    
    }

    showSCID(id);  
  
}



function getSubMod(id){
    xajax_getSubMod(id);
}                                                         

function assignValue(cond,details){         
    window.parent.$('address').innerHTML = details.address;
    if(cond==1){                 
         window.parent.$('senior_row').style.display   = '';   
         window.parent.$('tdsenior1').innerHTML = details.scLabel;
         window.parent.$('tdsenior2').innerHTML = details.scNumber;
    }else if(cond==2){             
         window.parent.$('senior_row').style.display   = ''; 
         window.parent.$('tdsenior1').innerHTML = details.scLabel;
         window.parent.$('tdsenior2').innerHTML = details.scNumber;
         window.parent.$('smss_no').innerHTML = details.mss;  
         window.parent.$('mssno').value = details.mss;
         window.parent.$('can_classify').value = 1;
    }else if(cond==3){
         window.parent.$('can_classify').value = 0;
         window.parent.$('intake').disabled = false;
    }else if(cond==4){
         window.parent.$('lingap_row').style.display = '';
         window.parent.$('discountId2').value = details.discountid;
    }else if(cond==5){
         window.parent.$('lingap_row').style.display = 'none';
         window.parent.$('discountId2').value = details.discountid;
    }else if(cond==6){
        window.parent.$('applybill_row').style.display = '';
    }else if (cond==7){
         window.parent.$('applybill_row').style.display = 'none';
    }else if (cond==8){
         window.parent.$('can_classify').value = 1;
         window.parent.$('intake').disabled = true;
    }
    
}

function mouseOut(){
    return nd();    
}

function showSCID(value){
     var temp = jQuery('input[name="pwd_temp"]').is(':checked');
    if(value=='SC'){
        $('id_no_tag').style.display='';
        $('id_no').focus();   
    }else
        $('id_no_tag').style.display='none';
        
    if(value=='OT'){
        $('other_tag').style.display='';
        $('other_row').focus();  
      
    }else
        $('other_tag').style.display='none';  

    var isPWD = /PWD/i;
     if(temp) {
        jQuery('#_pwd-expiry').attr('hidden', false);
    }

    if(isPWD.test(value)) {
        jQuery('#_pwd-id').attr('hidden', false);
    } 
    else {
        jQuery('#_pwd-id').attr('hidden', true);
        jQuery('#_pwd-expiry').attr('hidden', true);
      //  jQuery('input[name="pwd_temp"]').is(':checked',false);
    } 
        }

function hidetext(id){
    if($(id+'_problems').checked==true){        
        $('other_problem').style.display = '';
        $('other_problem').value = '';
    }else{                 
        $('other_problem').value = '';                 
        $('other_problem').style.display = 'none';
        
    }    
}

function hidetext1(id){
    var checkID = id.split("_");    
    if($(id).checked==true){        
        $('other_'+checkID[1]).style.display = '';
        $('other_'+checkID[1]).value = '';
    }else{
        $('other_'+checkID[1]).value = '';                 
        $('other_'+checkID[1]).style.display = 'none';
    }    
}

function changeVal(){
    if($('living').value == '1' || $('living').value == '2'){
        $('living_amount').value = "0.00";
        $('living_amount').readOnly = true;
    }else{
        $('living_amount').readOnly = false;
    }
}

function showOT(val){
    if(val==16){
        $('ot_occupation').style.display='';
        $('ot_occu').focus();
    }else{
        $('ot_occupation').style.display='none';
        $('ot_occu').value = '';
    }
}

function showOTDep(val){
    if(val=='Others'){
        $('ot_dep_occu').style.display='';
        $('ot_source_dep').focus();
    }else{
        $('ot_dep_occu').style.display='none';
        $('ot_source_dep').value = '';
    }
}

function savePdpu(){
    var pid = jQuery('#pid').val();
    var encounter_nr = jQuery('#encounter_nr').val();
    var mssno = jQuery('#mssno').val();
    var dx = jQuery('#dx').val();
    var ward = jQuery('#ward').val();
    var pdpuintervention = jQuery('#pdpuintervention').val();
    var pdpuremarks = jQuery('#pdpuremarks').val();                     
    var physician_nr = jQuery('#physician_nr').val();
    var created = jQuery('#encoder_name').val();
    var classification = jQuery('#pdpuclass').val();  
    var modify = jQuery('#encoder_name').val();
    var data  = {pid                : pid,
                 mssno              : mssno,
                 encounter_nr       : encounter_nr,
                 dx                 : dx,
                 classification     : classification,
                 ward               : ward,
                 pdpuintervention   : pdpuintervention,
                 pdpuremarks        : pdpuremarks,
                 physician_nr       : physician_nr,
                 created            : created,
                 modify             : modify};
                 
    xajax_savePdpu(data);
}

function printPdpu(){
    var mss = jQuery('#mssno').val();
    window.open("pdpu_printout.php?mss="+mss,"PDPU printout","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
}
// added by: syboy 11.06.2015 : meow
function reloadFrame(){
    window.location.reload();
}
// ended

/**
 * Fetches the current patient's dependents asynchronously then
 * calls setOriginalValue().
 * @author Nick 1-29-2016
 */
function getDependent() {
    xajax.call('populateDependent', {
        asynchronous: true,
        parameters: [
            $('pid').value,
            $('encounter_nr').value,
            $('mode').value
        ],
        callback: createXajaxCallback(setOriginalValues)
    });
}

/**
 * Sets the original values to be compared against the
 * changes made by the user when closing the profile
 * intake dialog.
 * @author Nick 1-29-2016
 */
function setOriginalValues() {
    window.parent.intakeFormData = jQuery('#intake_form').serialize();
}

/**
 * Returns a Xajax callback object.
 * @param callback
 * @author Nick 1-29-2016
 */
function createXajaxCallback(callback) {
    var cb = xajax.callback.create();
    cb.onComplete = callback;
    return cb;
}
/**

 * Added by Gervie 04-20-2017

 * Toggle PWD Temp

 */

function pwdTemp() {
    var temp = jQuery('input[name="pwd_temp"]').is(':checked');
    var pwd_id = jQuery('input[name="pwd_id"]').attr('t_val');
    if(temp) {
        jQuery('#_pwd-expiry').attr('hidden', false);

    }
    else {
        jQuery('#_pwd-expiry').attr('hidden', true);
        if(pwd_id) {
            jQuery('input[name="pwd_id"]').val(pwd_id);
        }
        else {
            jQuery('input[name="pwd_id"]').val('');

        }
    }

}

function clearPwdExpiryDate() {
    jQuery('input[name="pwd_expiration"]').val('');
}

// Added by Matsuu 08102017
function saveprogressnotes(){
    
    var pid = jQuery('#pid').val();
    var encounter = jQuery('#encounter_nr').val();
    var datetime = jQuery('#datetime').val()
    var ward_nr = jQuery('#ward_nr').val()
    var diagnosis =jQuery('#diagnosis').val();
    var referral = jQuery('input[name=referral]:checked').val();
    var informant= jQuery('#informant').val();
    var reltopatient= jQuery('#reltopatient').val();
    var purpose= jQuery('#purpose').val();
    var action_taken= jQuery('#action_taken').val();
    var recommendation= jQuery('#recommendation').val();
    var medsocwork = jQuery('#medsocwork').val();

    // Added By: Leira 02/24/2018
    var objct = [
            datetime,
            ward_nr,
            diagnosis,
            referral,
            informant,
            reltopatient,    
            purpose,
            action_taken,
            recommendation,
            medsocwork
        ];

    if(checkInput()){
        xajax_saveProgNotes(pid,encounter,objct);
    }  
}

function checkInput(){
    var datetime = jQuery('#datetime').val()
    var ward_nr = jQuery('#ward_nr').val()
    var diagnosis =jQuery('#diagnosis').val();
    var referral = jQuery('input[name=referral]:checked').val();
    var informant= jQuery('#informant').val();
    var reltopatient= jQuery('#reltopatient').val();
    var purpose= jQuery('#purpose').val();
    var action_taken= jQuery('#action_taken').val();
    var recommendation= jQuery('#recommendation').val();

    if(!(informant)){
    alert('All fields with * are required');
    jQuery('#informant').focus();
    return false; 
    }
    else if (!(referral)){
    alert('All fields with * are required');
      jQuery('#referral').focus();
      return false;
    }
    else if(!(reltopatient)){
    alert('All fields with * are required');
    jQuery('#reltopatient').focus();
       return false;
     }
    else if(!(purpose)){
    alert('All fields with * are required');
    jQuery('#purpose').focus();
        return false;
     }
    else if(!(action_taken)){
    alert('All fields with * are required');
    jQuery('#action_taken').focus();
        return false;
     }
    else if(!(recommendation)){
    alert('All fields with * are required');
    jQuery('#recommendation').focus();
    return false;
    }else{
        return true;
    }
}

function UpdateProgressNotes(){
    var datetime = jQuery('#datetime').val()
    var ward_nr = jQuery('#ward_nr').val()
    var diagnosis =jQuery('#diagnosis').val();
    var referral = jQuery('input[name=referral]:checked').val();
    var informant= jQuery('#informant').val();
    var reltopatient= jQuery('#reltopatient').val();
    var purpose= jQuery('#purpose').val();
    var action_taken= jQuery('#action_taken').val();
    var recommendation= jQuery('#recommendation').val();
    var note_id = jQuery('#note_id').val();

    var objct = [
            datetime,
            ward_nr,
            diagnosis,
            referral,
            informant,
            reltopatient,    
            purpose,
            action_taken,
            recommendation
        ];

    if(checkInput()){
        xajax_UpdateProgressNote(note_id, objct);
    }

}

function jsClearPrognotes(){
     location.reload();
}
function getDataPrognotes(data_encoded){
    alert("s");
            info = JSON.parse(data_encoded);
            var del_permission = jQuery('#del_prog_notes').val();
            var all_permission = jQuery('#all_prog_notes').val();
            var to_del_notes = '';
            var disabled =false;
            if(del_permission){
                to_del_notes = "removeProgNotes(info,pid,encounter_nr,'+count+');";
                              }
            else if(all_permission){
                to_del_notes = "removeProgNotes(info,pid,encounter_nr,'+count+');";
            }
            else{
                 disabled = 'disabled';

            }
            var count=0;
            jQuery.each(info, function(key) {
                count++;
                 var tr = '<tr  class="detailcontents" id='+count+'>'+
                '<td class="data">'+info[key].prog_date+'</td>'+
                '<td class="data">'+info[key].ward_name+'</td>'+
                '<td class="data">'+info[key].diagnosis+'</td>'+
                '<td class="data">'+info[key].informant+'</td>'+
                '<td class="data">'+info[key].relationship+'</td>'+
                '<td class="data">'+info[key].referral+'</td>'+
                '<td class="data">'+info[key].purpose+'</td>'+
                '<td class="data">'+info[key].action_taken+'</td>'+
                '<td class="data">'+info[key].recommendation+'</td>'+
                '<td class="data">'+info[key].medsocwork+'</td>'+
                '<td class="data"><button type="button"class="btn btn-small" style="color:white" Title="Delete Information" '+disabled+' onclick='+to_del_notes+'>x</td>'+'</tr>';
                jQuery('#social_form_data').append(tr);
            });

}

function viewpermssion(){
    var p_view = jQuery('#view_prog_notes').val();
    var p_del = jQuery('#del_prog_notes').val();
    var p_all = jQuery('#all_prog_notes').val();
    if(!p_view  && !p_all){
    alert("No Permssion to view notes.");
    }    

}

function printprogressnotes(frm, to){
    var hrn =jQuery('#pid').val();
    var url = "reports/progress_notes_reports.php?pid="+hrn+"&frm_date="+frm+"&to_date="+to;
    var win = window.open(url, '_blank');
    if (win)
        win.focus();
    else 
        alert('Please allow popups for this website');
}

// Created By: Leira Anonymous
function dialogDate(){
    jQuery('#date-dialog').dialog({
        autoOpen: true,
        modal: true,
        show: 'fade',
        hide: 'fade',
        height: 230,
        width: '30%',
        draggable:false,
        title: 'Progress Notes Report',
        position: 'center',
        buttons: {
            "Print": function() {
                var frm = jQuery('#datefrom').val();
                var to = jQuery('#dateto').val();

                if((frm=='') || (to=='')) {
                    alert('Please specify report period!');
                    return false;
                }else {
                    printprogressnotes(frm, to);
                }
            },
            "Cancel": function() {
                jQuery(this).dialog("close");
            },
        }
    });
}

var $J = jQuery.noConflict();

jQuery(function($){
     jQuery("#datefrom").mask("99/99/9999");
});

jQuery(function($){
     jQuery("#dateto").mask("99/99/9999");
});
// End


// Ended by Matsuu 08102017