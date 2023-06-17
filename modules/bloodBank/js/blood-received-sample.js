var trayItems = 0;
var received_array = [];
function pSearchClose() {
    cClick();  //function in 'overlibmws.js'
}

/*
    This will trim the string i.e. no whitespaces in the
    beginning and end of a string AND only a single
    whitespace appears in between tokens/words
    input: object
    output: object (string) value is trimmed
*/
function trimString(objct){
    objct.value = objct.value.replace(/^\s+|\s+$/g,"");
    objct.value = objct.value.replace(/\s+/g," ");
}/* end of function trimString */

function preset(){
   //routine and stat
   // $J('#isStat_0').click(function(){
   //      $J('.checkdata_0').attr("checked",$('isStat_0').checked);
   //      if($('isStat_0').checked){
   //          $J('.checkdata_1').attr("checked","");
   //          $('isStat_1').checked = false;
   //      }
   // });

   //added by VAS 10/04/2019
   $J('input').live('keyup keydown', function(e) {
        var thisKey = e.keyCode ? e.keyCode : e.which;
        if (thisKey == 13) {
            e.preventDefault();
            e.stopPropagation();
            if (e.type == 'keyup') {
                checkBloodResult(this);
            }
        }
    });

   $J('#isStat_1').click(function(){
        $J('.checkdata_1').attr("checked",$J('#isStat_1').attr("checked"));
        
        if($('isStat_1').checked){
            $J('.checkdata_0').attr("checked","");
            $J('isStat_1').checked = true;
         
        }
   });

    var quantity = $('quantity').value;
    var service_code = $('service_code').value;
    
    for(i=1;i<=quantity;i++){
        id = service_code+i;
        // alert(id);
        setEnable(id);
        started_setEnable(id);
        done_setEnable(id);
        issuance_setEnable(id);
        release_setEnable(id);//added by Kenneth 10/06/2016
    //added by:borj 2013/25/11
        emptyOthers();
        returned_setEnable(id);
        reissue_setEnable(id);
        consumed_setEnable(id);
        


    //end borj
    }    

}


function meridianRecieve(id){
    $('meridian2'+id).value = $('meridian'+id).value
}


function started_setEnable(id){
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('date_started_trigger'+id).disabled = false;
        $('date_started_save'+id).disabled = false;
        $('date_started_cancel'+id).disabled = false;
        $('date_started'+id).readOnly = '';    
        $('time_started'+id).readOnly = '';
        $('started_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_started'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_started'+id).mask('99:99');
        });
        
        $J('#date_started'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_started'+id).bind('change', function() {
            setFormatTime(this,id,'started_');
        });
        
        $J('#date_started_save'+id).bind('click', function() {
            validateDate(id,'started');
        });
        
        $J('#date_started_cancel'+id).bind('click', function() {
            if ($J('#date_started'+id).val().length==0){
                alert('No Date Started to be cancelled...');
            }else{    
                if (confirm('Remove the Started Date entered?')){
                    $J('#date_started'+id).val('');
                    $J('#time_started'+id).val('');
                    $J('#started_meridian'+id).val('');
                    deleteDate(id,'started');
                }    
            }    
        });
        
        
        if ($J('#date_started'+id).val().length==0){
            $J('#date_started'+id).val('');
            $J('#time_started'+id).val('');
            $J('#started_meridian'+id).val('');
        }
        
        $J('#date_started_trigger'+id).css("cursor", "pointer");
        $J('#date_started_save'+id).css("cursor", "pointer");
        $J('#date_started_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('date_started_trigger'+id).disabled = true;
        $('date_started_save'+id).disabled = true;
        $('date_started_cancel'+id).disabled = true;
        $('date_started'+id).readOnly = 'readonly';
        $('date_started'+id).value='';
        
        $('time_started'+id).readOnly = 'readonly';
        $('time_started'+id).value = '';
        $('started_meridian'+id).disabled = true;
        $('started_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_started'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_started'+id).unmask('99:99');
        });
        
        $J('#date_started'+id).val('');
        $J('#time_started'+id).val('');
        $J('#started_meridian'+id).val('');
        
        $J('#date_started_trigger'+id).css("cursor", "default");
        $J('#date_started_save'+id).css("cursor", "default");
        $J('#date_started_cancel'+id).css("cursor", "default");
    }    
}

function done_setEnable(id){
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('date_done_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_done'+id).readOnly = '';    
        $('time_done'+id).readOnly = '';
        $('done_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_done'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_done'+id).mask('99:99');
        });
        
        $J('#date_done'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_done'+id).bind('change', function() {
            setFormatTime(this,id,'done_');
        });
        
        $J('#date_done_save'+id).bind('click', function() {
            validateDate(id,'done');
        });
        
        $J('#date_done_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Date Done to be cancelled...');
            }else{    
                if (confirm('Remove the Done Date entered?')){
                    $J('#date_done'+id).val('');
                    $J('#time_done'+id).val('');
                    $J('#done_meridian'+id).val('');
                    
                    deleteDate(id,'done');
                }    
            }    
        });
        
        
        if ($J('#date_done'+id).val().length==0){
            $J('#date_done'+id).val('');
            $J('#time_done'+id).val('');
            $J('#done_meridian'+id).val('');
        }
        
        $J('#date_done_trigger'+id).css("cursor", "pointer");
        $J('#date_done_save'+id).css("cursor", "pointer");
        $J('#date_done_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('date_done_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_done'+id).readOnly = 'readonly';
        $('date_done'+id).value='';
        
        $('time_done'+id).readOnly = 'readonly';
        $('time_done'+id).value = '';
        $('done_meridian'+id).disabled = true;
        $('done_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_done'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_done'+id).unmask('99:99');
        });
        
        $J('#date_done'+id).val('');
        $J('#time_done'+id).val('');
        $J('#done_meridian'+id).val('');
        
        $J('#date_done_trigger'+id).css("cursor", "default");
        $J('#date_done_save'+id).css("cursor", "default");
        $J('#date_done_cancel'+id).css("cursor", "default");
    }    
}

function issuance_setEnable(id){

    var caneditissuance = $J('#caneditdate1').val();
    //console.log(caneditissuance);

    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued' + id).disabled = false;
        $('date_issuance_trigger' + id).disabled = false;
        $('date_done_save' + id).disabled = false;
        $('date_done_cancel' + id).disabled = false;
        $('date_issuance' + id).readOnly = '';
        if(caneditissuance == 1){
            $('time_issuance' + id).readOnly = false;
            $('issuance_meridian' + id).disabled = false;
        }
        else {
            $('time_issuance' + id).readOnly = true;
            $('issuance_meridian' + id).disabled = false;
        }
        
        jQuery(function($){
            $J('#date_issuance'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_issuance'+id).mask('99:99');
        });
        
        $J('#date_issuance'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_issuance'+id).bind('change', function() {
            setFormatTime(this,id,'issuance_');
        });
        
        $J('#date_issuance_save'+id).bind('click', function() {
            validateDate(id,'issuance');
        });
        
        $J('#date_issuance_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Issuance Date to be cancelled...');
            }else{    
                if (confirm('Remove the Issuance Date entered?')){
                    $J('#date_issuance'+id).val('');
                    $J('#time_issuance'+id).val('');
                    $J('#issuance_meridian'+id).val('');
                    
                    deleteDate(id,'issuance');
                }    
            }    
        });
        
        if ($J('#date_issuance'+id).val().length==0){
            $J('#date_issuance'+id).val('');
            $J('#time_issuance'+id).val('');
            $J('#issuance_meridian'+id).val('');
        }
        
        $J('#date_issuance_trigger'+id).css("cursor", "pointer");
        $J('#date_issuance_save'+id).css("cursor", "pointer");
        $J('#date_issuance_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_issuance_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_issuance'+id).readOnly = 'readonly';
        $('date_issuance'+id).value='';
        
        $('time_issuance'+id).readOnly = 'readonly';
        $('time_issuance'+id).value = '';
        $('issuance_meridian'+id).disabled = true;
        $('issuance_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_issuance'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_issuance'+id).unmask('99:99');
        });
        
        $J('#date_issuance'+id).val('');
        $J('#time_issuance'+id).val('');
        $J('#issuance_meridian'+id).val('');
        
        $J('#date_issuance_trigger'+id).css("cursor", "default");
        $J('#date_issuance_save'+id).css("cursor", "default");
        $J('#date_issuance_cancel'+id).css("cursor", "default");
    }    
}

//added by Kenneth 10/06/2016
function release_setEnable(id){

    var caneditrelease = $J('#caneditrelease').val();
    //console.log(caneditrelease);

    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued' + id).disabled = false;
        $('date_release_trigger' + id).disabled = false;
        $('date_done_save' + id).disabled = false;
        $('date_done_cancel' + id).disabled = false;
        $('date_release' + id).readOnly = '';
        if(caneditrelease == 1){
            $('time_release' + id).readOnly = false;
            $('release_meridian' + id).disabled = false;
            $('date_release' + id).disabled = false;
        }
        else {
            $('date_release' + id).disabled = true;
            $('time_release' + id).readOnly = true;
            $('release_meridian' + id).disabled = false;
        }
        
        jQuery(function($){
            $J('#date_release'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_release'+id).mask('99:99');
        });
        
        $J('#date_release'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_release'+id).bind('change', function() {
            setFormatTime(this,id,'release_');
        });
        
        $J('#date_release_save'+id).bind('click', function() {
            validateDate(id,'release');
        });
        
        $J('#date_release_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No release Date to be cancelled...');
            }else{    
                if (confirm('Remove the release Date entered?')){
                    $J('#date_release'+id).val('');
                    $J('#time_release'+id).val('');
                    $J('#release_meridian'+id).val('');
                    
                    deleteDate(id,'release');
                }    
            }    
        });
        
        if ($J('#date_release'+id).val().length==0){
            $J('#date_release'+id).val('');
            $J('#time_release'+id).val('');
            $J('#release_meridian'+id).val('');
        }
        
        $J('#date_release_trigger'+id).css("cursor", "pointer");
        $J('#date_release_save'+id).css("cursor", "pointer");
        $J('#date_release_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_release_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_release'+id).readOnly = 'readonly';
        $('date_release'+id).value='';
        
        $('time_release'+id).readOnly = 'readonly';
        $('time_release'+id).value = '';
        $('release_meridian'+id).disabled = true;
        $('release_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_release'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_release'+id).unmask('99:99');
        });
        
        $J('#date_release'+id).val('');
        $J('#time_release'+id).val('');
        $J('#release_meridian'+id).val('');
        
        $J('#date_release_trigger'+id).css("cursor", "default");
        $J('#date_release_save'+id).css("cursor", "default");
        $J('#date_release_cancel'+id).css("cursor", "default");
    }    
}

function inheritData(id) {

    var BS_OTHERS = 4;
    var lastID = id.replace(/\D/g,'');

    var firstID =lastID;
        lastID = lastID - 1;

    if (lastID < 1) {
        lastID = 1; 
    }

    var countIf1 = $('service_code').value;
    var firstID = $('blood_dept'+countIf1+lastID).selectedIndex;
    var firstID_Component = $('component'+countIf1+lastID).selectedIndex;
    var firstID_BloodSource = $('blood_source'+countIf1+lastID).selectedIndex;
    var firstID_others = $('others'+countIf1+lastID).value;

    if(firstID_BloodSource == BS_OTHERS) {
        $('others'+id).style.display = "block";
    }else {
        $('others'+id).style.display = "none";
    }

    $('blood_dept'+id).selectedIndex = firstID;
    $('component'+id).selectedIndex = firstID_Component;
    $('blood_source'+id).selectedIndex = firstID_BloodSource;
    $('others'+id).value = firstID_others;
}

// Modified by: JEFF
// Date: 11-08-17
function changedept(id){
    var BS_OTHERS = 4;
    // var lastID = id.charAt(id.length - 1);
    var lastID = id.replace(/\D/g,'');

    var firstID =lastID;
        lastID = lastID - 1;

    if (lastID < 1) {
        lastID = 1; 
    }

    if($('is_received'+id).getAttribute("loadvalue") != "None_Existent"){
        $('blood_dept'+id).value = $('is_received'+id).getAttribute("loadvalue")
    }else{

        var countIf1 = $('service_code').value;
        var firstID = $('blood_dept'+countIf1+lastID).selectedIndex;
        var firstID_Component = $('component'+countIf1+lastID).selectedIndex;
        var firstID_BloodSource = $('blood_source'+countIf1+lastID).selectedIndex;
        // OJT - UIC
        var firstID_status = $('is_status_1'+countIf1+lastID).checked;
        var firstID_time_start =$('is_started'+countIf1+lastID).value;
        var firstID_date_started = $('date_started'+countIf1+lastID).value;
        var firstID_time_started = $('time_started'+countIf1+lastID).value;
        var firstID_started_meridian = $('started_meridian'+countIf1+lastID).selectedIndex;
        var firstID_is_done = $('is_done'+countIf1+lastID).value;
        var firstID_date_done = $('date_done'+countIf1+lastID).value;
        var firstID_time_done = $('time_done'+countIf1+lastID).value;
        var firstID_done_meridian = $('done_meridian'+countIf1+lastID).selectedIndex;
        var firstID_date_received = $('date_received'+countIf1+lastID).value;
        var firstID_time_received = $('time_received'+countIf1+lastID).value;
        var firstID_meridian = $('meridian'+countIf1+lastID).selectedIndex;
        var firstID_others = $('others'+countIf1+lastID).value;

        if(firstID_BloodSource == BS_OTHERS) {
            $('others'+id).style.display = "block";
        }else {
            $('others'+id).style.display = "none";
        }
                            

        $('blood_dept'+id).selectedIndex = firstID;
        $('component'+id).selectedIndex = firstID_Component;
        $('blood_source'+id).selectedIndex = firstID_BloodSource;
        $('is_status_1'+id).checked =  firstID_status; 
        $('date_received'+id).value = firstID_date_received;
        $('time_received'+id).value = firstID_time_received;
        $('meridian'+id).selectedIndex= firstID_meridian;
        $('others'+id).value = firstID_others;

        // if(firstID > 1){
        //     $('is_status_1'+id).checked =  firstID_status;  
        //     $('is_started'+id).checked = firstID_time_start;
        //     $('date_started'+id).value  = firstID_date_started;
        //     $('time_started'+id).value = firstID_time_started;
        //     $('started_meridian'+id).selectedIndex = firstID_started_meridian;
        //     $('is_done'+id).checked = firstID_is_done;
        //     $('date_done'+id).value= firstID_date_done;
        //     $('time_done'+id).value  = firstID_time_done;
        //     $('done_meridian'+id).selectedIndex = firstID_done_meridian;
        // }   
    }
}

function setEnable(id, rp, coverage, enc, code, type, sQty){

    var caneditdate = $J('#caneditdate').val();
    var chargeType = "phic";

    if ($J('#is_received'+id).is(":checked")){
        /*added By Mark 2016-31-08*/
        if (caneditdate == 1) {
            $('date_received_trigger'+id).disabled = false;
                $('time_received'+id).readOnly = false;
                $('meridian'+id).disabled = false;
            }else{
                $('date_received_trigger'+id).disabled = true;
                $('time_received'+id).readOnly = true;
                $('meridian'+id).readOnly = true;
            }
            /*$('date_received_trigger'+id).disabled = false;
            //$('date_received'+id).readOnly = '';
            $('time_received'+id).readOnly = false;
            $('meridian'+id).disabled = false;*/
            
            // $('serial'+id).readOnly = '';
            //Add Blood Ward/Dept in 2014-14-07 (Borj)
          
            $('is_status_1'+id).disabled = false;
            $('blood_dept'+id).disabled = false;
            $('component'+id).disabled = false;
            ////Add Blood Source in 2014-18-03
            $('blood_source'+id).disabled = false;
            
            jQuery(function($){
                $J('#date_received'+id).mask('99/99/9999');
            });
            
            jQuery(function($){
                $J('#time_received'+id).mask('99:99');
            });
            
            $J('#date_received'+id).bind('blur', function() {
                IsValidDate(this,'MM/dd/yyyy');
            });
            
            $J('#time_received'+id).bind('change', function() {
                setFormatTime(this,id,'');
            });
            
            
            if ($('date_received'+id).value==''){
                $('date_received'+id).value=$('current_date').value;
                $('time_received'+id).value = $('current_time').value;
                $('meridian'+id).value=$('current_meridian').value;
                $('meridian2'+id).value=$('meridian'+id).value;
            }
            
            $J('#date_received_trigger'+id).css("cursor", "pointer");
            
            // if(type == chargeType) {
            //    xajax_saveCoveragePHIC(enc,code,coverage,sQty); 
            // }

            if(type == chargeType) {
                received_array.push({
                    encounter: enc,
                    code: code,
                    coverage: coverage,
                    quantity: sQty,
                    typeBlood: 'saveCoveragePHIC'
                });
            }
            console.log(received_array);
    }else{
        var remove_index = -1;
        if(type == chargeType) {
            for(var i = 0; i < received_array.length; i++) {
               if(received_array[i].encounter === enc &&
                  received_array[i].code === code &&
                  received_array[i].coverage === coverage &&
                  received_array[i].quantity === sQty
                ) remove_index = i;
            }
            if(remove_index > -1) {
                received_array.splice(remove_index, 1);
            }
        }
        console.log(received_array);
        // Modified by: JEFF
        // Date: 11-08-17
        $('blood_dept'+id).selectedIndex = 0;
        $('component'+id).selectedIndex = 0;
        $('blood_source'+id).selectedIndex = 0; 
        $('is_status_1'+id).checked = false;
        // OJT - UIC
        $('is_started'+id).checked = false;
        $('date_started'+id).value  = '';
        $('time_started'+id).value = '';
        $('started_meridian'+id).selectedIndex = 0;
        $('is_done'+id).checked = false;
        $('date_done'+id).value= '';
        $('time_done'+id).value  = '';
        $('done_meridian'+id).selectedIndex = false;
        $('time_received'+id).value = '';
        $('meridian'+id).selectedIndex=0;
        $('others'+id).style.display = "none";
        $('date_received_trigger'+id).disabled = true;
        //$('date_received'+id).readOnly = 'readonly';
        $('date_received'+id).value='';
        // $('time_received'+id).value='';
        // $('meridian'+id).value='AM';
            
        // $('serial'+id).readOnly = 'readonly';
        $('serial'+id).value = '';
        ////Add Blood Ward/Dept in 2014-12-07
        $('is_status_0'+id).disabled = true;
        $('is_status_0'+id).value='';
        $('is_status_1'+id).disabled = true;
        $('is_status_1'+id).value='';
        $('blood_dept'+id).disabled = true;
        $('blood_dept'+id).value='';
        $('component'+id).disabled = true;
        $('component'+id).value='';
        ////Add Blood Source in 2014-18-03
        $('blood_source'+id).disabled = true;
        $('blood_source'+id).value='';
        $('others'+id).readOnly = 'readonly';
        
        $('result'+id).disabled = true;
        $('result'+id).value='noresult';
        
        $('time_received'+id).readOnly = 'readonly';
        $('time_received'+id).value = '';
        $('meridian'+id).disabled = true;
        $('meridian'+id).value='AM';
        $('meridian2'+id).value=$('meridian'+id).value;
        
        jQuery(function($){
            $J('#date_received'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_received'+id).unmask('99:99');
        });
        
        
        $J('#date_received_trigger'+id).css("cursor", "default");
            
    }
    
    /*$J('#date_done'+id).val('');
    $J('#time_done'+id).val('');
    $J('#done_meridian'+id).val('');
    
    $J('#date_issuance'+id).val('');
    $J('#time_issuance'+id).val('');
    $J('#issuance_meridian'+id).val('');*/
            
    
    $('counter').innerHTML = $J(".tdrec input:checked").length;//edited by nick, class tdrec, 2/5/14
    $('received_qty').value = $('counter').innerHTML;
}
//added by:borj 2013/23/11
function returned_setEnable(id){
    
    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_returned_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_returned'+id).readOnly = '';
        $('time_returned'+id).readOnly = true;
        $('returned_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_returned'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_returned'+id).mask('99:99');
        });
        
        $J('#date_returned'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_returned'+id).bind('change', function() {
            setFormatTime(this,id,'returned_');
        });
        
        $J('#date_returned_save'+id).bind('click', function() {
            validateDate(id,'returned');
        });
        
        $J('#date_returned_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Returned Date to be cancelled...');
            }else{    
                if (confirm('Remove the Returned Date entered?')){
                    $J('#date_returned'+id).val('');
                    $J('#time_returned'+id).val('');
                    $J('#returned_meridian'+id).val('');
                    
                    deleteDate(id,'returned');
                }    
            }    
        });
        
        if ($J('#date_returned'+id).val().length==0){
            $J('#date_returned'+id).val('');
            $J('#time_returned'+id).val('');
            $J('#returned_meridian'+id).val('');
        }
        
        $J('#date_returned_trigger'+id).css("cursor", "pointer");
        $J('#date_returned_save'+id).css("cursor", "pointer");
        $J('#date_returned_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_returned_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_returned'+id).readOnly = 'readonly';
        $('date_returned'+id).value='';
        
        $('time_returned'+id).readOnly = 'readonly';
        $('time_returned'+id).value = '';
        $('returned_meridian'+id).disabled = true;
        $('returned_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_returned'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_returned'+id).unmask('99:99');
        });
        
        $J('#date_returned'+id).val('');
        $J('#time_returned'+id).val('');
        $J('#returned_meridian'+id).val('');
        
        $J('#date_returned_trigger'+id).css("cursor", "default");
        $J('#date_returned_save'+id).css("cursor", "default");
        $J('#date_returned_cancel'+id).css("cursor", "default");
    }  
}
//Add Function Others in 2014-18-03
 function others_setEnable(id){
  
         if ($('blood_source'+id).value == 'OTHERS'){
                $('others'+id).style.display = "block";
                $('others'+id).readOnly = '';
         }else{($('blood_source'+id).value != 'OTHERS') 
                $('others'+id).style.display = "none";
                $J('#others'+id).val('');
                            
              }
}

function emptyOthers(){
    if ($('others'+id).value== ''){
         $('others'+id).style.display = "none";
     }else{
        $('others'+id).style.display = "block";
        $('others'+id).value =='';
     }
}
//End        
//edited by art 09/17/2014
function reissue_setEnable(id){
    var caneditdate = $J('#caneditdate').val();
    if(caneditdate == 1){
    //if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_reissue_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_reissue'+id).readOnly = '';
        //$('time_reissue'+id).readOnly = true;
        $('reissue_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_reissue'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_reissue'+id).mask('99:99');
        });
        
        $J('#date_reissue'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_reissue'+id).bind('change', function() {
            setFormatTime(this,id,'reissue_');
        });
        
        $J('#date_reissue_save'+id).bind('click', function() {
            validateDate(id,'reissue');
        });
        
        $J('#date_reissue_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Reissue Date to be cancelled...');
            }else{    
                if (confirm('Remove the Reissue Date entered?')){
                    $J('#date_reissue'+id).val('');
                    $J('#time_reissue'+id).val('');
                    $J('#reissue_meridian'+id).val('');
                    
                    deleteDate(id,'reissue');
                }    
            }    
        });
        
        if ($J('#date_reissue'+id).val().length==0){
            $J('#date_reissue'+id).val('');
            $J('#time_reissue'+id).val('');
            $J('#reissue_meridian'+id).val('');
        }
        
        $J('#date_reissue_trigger'+id).css("cursor", "pointer");
        //$J('#date_reissue_save'+id).css("cursor", "pointer");
        $J('#date_reissue_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_reissue_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_reissue'+id).readOnly = 'readonly';
        //$('date_reissue'+id).value='';
        
        $('time_reissue'+id).readOnly = 'readonly';
        //$('time_reissue'+id).value = '';
        $('reissue_meridian'+id).disabled = true;
        //$('reissue_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_reissue'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_reissue'+id).unmask('99:99');
        });

        $J('#date_reissue_save'+id).bind('click', function() {
            validateDate(id,'reissue');
        });

        //$J('#date_reissue'+id).val('');
        //$J('#time_reissue'+id).val('');
        //$J('#reissue_meridian'+id).val('');
        
        $J('#date_reissue_trigger'+id).css("cursor", "default");
        //$J('#date_reissue_save'+id).css("cursor", "default");
        $J('#date_reissue_cancel'+id).css("cursor", "default");
    }    
}
//end art

function consumed_setEnable(id){

    var caneditconsume = $J('#caneditconsume').val();

    if (($J('#is_received'+id).is(":checked"))&&($J('#date_received'+id).val())){
        $('is_issued'+id).disabled = false;
        $('date_consumed_trigger'+id).disabled = false;
        $('date_done_save'+id).disabled = false;
        $('date_done_cancel'+id).disabled = false;
        $('date_consumed'+id).readOnly = '';
        $('time_consumed'+id).readOnly = (caneditconsume == 1) ? false : true;
        $('consumed_meridian'+id).disabled = false;
        
        jQuery(function($){
            $J('#date_consumed'+id).mask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_consumed'+id).mask('99:99');
        });
        
        $J('#date_consumed'+id).bind('blur', function() {
            IsValidDate(this,'MM/dd/yyyy');
        });
        
        $J('#time_consumed'+id).bind('change', function() {
            setFormatTime(this,id,'consumed_');
        });
        
        $J('#date_consumed_save'+id).bind('click', function() {
            validateDate(id,'consumed');
        });
        
        $J('#date_consumed_cancel'+id).bind('click', function() {
            if ($J('#date_done'+id).val().length==0){
                alert('No Consumed Date to be cancelled...');
            }else{    
                if (confirm('Remove the Consumed Date entered?')){
                    $J('#date_consumed'+id).val('');
                    $J('#time_consumed'+id).val('');
                    $J('#consumed_meridian'+id).val('');
                    
                    deleteDate(id,'consumed');
                }    
            }    
        });
        
        if ($J('#date_consumed'+id).val().length==0){
            $J('#date_consumed'+id).val('');
            $J('#time_consumed'+id).val('');
            $J('#consumed_meridian'+id).val('');
        }
        
        $J('#date_consumed_trigger'+id).css("cursor", "pointer");
        $J('#date_consumed_save'+id).css("cursor", "pointer");
        $J('#date_consumed_cancel'+id).css("cursor", "pointer");
        
    }else{
        $('is_issued'+id).disabled = true;
        $('date_consumed_trigger'+id).disabled = true;
        $('date_done_save'+id).disabled = true;
        $('date_done_cancel'+id).disabled = true;
        $('date_consumed'+id).readOnly = 'readonly';
        $('date_consumed'+id).value='';

        $('time_consumed'+id).readOnly = 'readonly';
        $('time_consumed'+id).value = '';
        $('consumed_meridian'+id).disabled = true;
        $('consumed_meridian'+id).value='AM';
        
        jQuery(function($){
            $J('#date_consumed'+id).unmask('99/99/9999');
        });
        
        jQuery(function($){
            $J('#time_consumed'+id).unmask('99:99');
        });
        
        $J('#date_consumed'+id).val('');
        $J('#time_consumed'+id).val('');
        $J('#consumed_meridian'+id).val('');
        
        $J('#date_consumed_trigger'+id).css("cursor", "default");
        $J('#date_consumed_save'+id).css("cursor", "default");
        $J('#date_consumed_cancel'+id).css("cursor", "default");
    }    
}

//end borj

//  Modified by: JEFF
//  Date: 09-20-17 && 09-28-17
//  Purpose: Condition added for trapping on request dates upon submit
function submitRequest(refno,id,quantity){
    var details = new Object();
    var trap_counter = 0;
    var updateType = 'saveCoveragePHIC';

    //alert(id+quantity);
    var id = id.trim();
    var temp = $J('#serial').val();

    for(var i=1; i<=quantity; i++)
        { 
           if ($('trap_dateStarted'+id+i).value == 'trap' || $('trap_dateDone'+id+i).value == 'trap' || $('trap_result'+id+i).value == 'trap' || $('trap_issuanceDate'+id+i).value == 'trap' || $('trap_reIssue'+id+i).value == 'trap' || $('trap_consumed'+id+i).value == 'trap')
            {
                trap_counter = trap_counter + 1;
            }
            else
            {
                trap_counter = trap_counter;
            }

        }
        
        if (trap_counter >= 1) {
            alert("Date/s encoded is not yet saved!");
        }else{
            $('submitted').value = 0;
            $('submitted').value = 1;    
            
            //updated by VAS 11/29/2017
            //change to jquery and the permission
            /*if (window.parent.$J('#isserve').val() == 1) {
                window.parent.$J('#withsampleID' + id).attr("checked", true);
                //window.parent.document.getElementById('withsampleID' + id).checked = true; // add by carl Permision
            }*/

            if(received_array) {
                if(received_array.length > 0){
                    if(received_array[0]['typeBlood'] == updateType) {
                        xajax_saveCoveragePHIC(received_array);
                    }
                }
            }
            
            $('inputform').submit();  
        }

}

/*function repeatRequest(id, service_code){
     //alert('repeat = '+id);
     var refno = $('refno').innerHTML;

     if ($('date_received'+id).value==''){
            alert('Please indicate the date of repeat service.');
            $('date_received'+id).focus();
            //$('date_received_trigger'+id).focus();
            return false;
     }

     //alert(refno+", "+$('date_received'+id).value+", "+service_code);
}*/

function reset(id){
    
    //var n = $J("input:checked").length;
    var n = $J("input:checkbox").length;
    
    for (var i=1;i<=n;i++){
      $J('#date_received'+id+i).unmask('99/99/9999');
      $J('#time_received'+id+i).unmask('99:99');
      
      $J('#date_done'+id+i).unmask('99/99/9999');
      $J('#time_done'+id+i).unmask('99:99');   
      
      $J('#date_issuance'+id+i).unmask('99/99/9999');
      $J('#time_issuance'+id+i).unmask('99:99');      
      //added by:borj
      //2013/23/11
      $J('#date_returned'+id+i).unmask('99/99/9999');
      $J('#time_returned'+id+i).unmask('99:99');

      $J('#date_reissue'+id+i).unmask('99/99/9999');
      $J('#time_reissue'+id+i).unmask('99:99');

      $J('#date_consumed'+id+i).unmask('99/99/9999');
      $J('#time_consumed'+id+i).unmask('99:99');            
      //end borj

      //added by Kenneth
      $J('#date_release'+id+i).unmask('99/99/9999');
      $J('#time_release'+id+i).unmask('99:99');  
    }
    
    $J('input:text').val('');
    $J('input:text').attr('readOnly', 'readonly');
    $J('select').val('');
    $J('select').attr('disabled', true);
    $J('input:checkbox').attr('checked', false);
    $J('button').attr('disabled', true);
    $J('button[name=submit_btn]').attr('disabled', false);
    $J('button[name=close_btn]').attr('disabled', false);

}


/*function updateFields(cal) {
    var date = cal.selection.get();
    if (date) {
        date = Calendar.intToDate(date);
        document.getElementById("f_date").value = Calendar.printDate(date, "%Y-%m-%d");
    }
    document.getElementById("f_hour").value = cal.getHours();
    document.getElementById("f_minute").value = cal.getMinutes();
};*/

function set_calendar(id){
    var datenow = $('datenow').value;

    // disable from day after current day and onward
    Calendar.setup ({
        inputField: 'date_received'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_received_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });

        Calendar.setup ({
        inputField: 'date_started'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_started_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    
    
    Calendar.setup ({
        inputField: 'date_done'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_done_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    
    Calendar.setup ({
        inputField: 'date_issuance'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_issuance_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    //added by:borj
    //2013/23/11
    Calendar.setup ({
        inputField: 'date_returned'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_returned_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });

    Calendar.setup ({
        inputField: 'date_reissue'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_reissue_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });

    Calendar.setup ({
        inputField: 'date_consumed'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_consumed_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });
    //end borj

    //added by Kenneth 10/06/2016
     Calendar.setup ({
        inputField: 'date_release'+id,
        //dateFormat: '%B %e, %Y',
        dateFormat: '%m/%d/%Y %I:%M%P',
        trigger: 'date_release_trigger'+id,
        showTime: true,
        onSelect: function() { this.hide() },
        /*disabled: function(date) {
                if (date.getDay() == 5) {
                        return true;
                } else {
                        return false;
                }
        } */
        max: eval(datenow)
    });

    // onSelect     : updateFields,
    // onTimeChange : updateFields
}

var seg_validTime=false;

function setFormatTime(thisTime, id, name){
//    var time = $('time_text_d');
    var stime = thisTime.value;
    var hour, minute;
    var ftime ="";
    var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
    var f2 = /^[0-9]\:[0-5][0-9]$/;

    trimString(thisTime);

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

    if (hour==0){
         hour = 12;
         $(name+'meridian'+id).value = "AM";
    }else    if((hour > 12)&&(hour < 24)){
         hour -= 12;
         $(name+'meridian'+id).value = "PM";
    }
    
    if ((typeof hour)=='number'){
        if (hour < 10)
            hour = '0'.concat(hour);
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

function deleteDate(id, mode){
     var refno = $('refno').innerHTML;
     var service_code = $('test_code').innerHTML;
     var index = id.replace(service_code,'');
     
     xajax_save_dateinfo(refno, service_code, index, mode, '', ''); 

 }

 function save_ajax_dateinfo(mode, dateinfo, timeinfo, id, return_reason, release_result=''){
     var refno = $('refno').innerHTML;
     var service_code = $('test_code').innerHTML;
     var index = id.replace(service_code,'');
     // new
     var pid = $('hrn').innerHTML;
     var serv_result = $J('#result'+id+' option:selected').text();
     var blood_type = $('blood_type').innerHTML;
     var component = $J('#component'+id).val();
     var qty = $('qty').innerHTML;
     var serialno = $J('.getSerialNo');
     var status=0;

        serialno.each(function(x,y){
            ++x;

            if($J('#is_received'+$('test_code').innerHTML+x).is(":checked")) {
                status +=1;
            }else {
                status +=0;
            }

        });
      var serial_no = $J('#serial'+id).val();
     // end
     $('trap_dateStarted'+id).value = '';
     $('trap_dateDone'+id).value = '';
     $('trap_result'+id).value = '';
     $('trap_issuanceDate'+id).value = '';
     $('trap_reIssue'+id).value = '';
     $('trap_consumed'+id).value = '';

     //save_ajax_percheck(refno, id, mode, dateinfo, timeinfo);
     xajax_save_dateinfo(refno, service_code, index, mode, dateinfo, timeinfo, return_reason, release_result);

     xajax_save_percheck(refno, pid, serv_result, blood_type, component, qty, serial_no, status, mode, dateinfo, timeinfo);

 } 

//added by: borj 2013/2/12
function validateDate(id, str){
    var date = new Date();
    var received_date = Date.parse($J("#date_received"+id).val());
    var received_date2 = $J("#date_received"+id).val();
    var received_hour = $J('#time_received'+id).val().substring(0,2);
    var received_minute = $J('#time_received'+id).val().substring(3,5);

    var issuance_date = Date.parse($J("#date_issuance"+id).val());
    var issuance_date2 = $J("#date_issuance"+id).val();
    var issuance_hour = $J('#time_issuance'+id).val().substring(0,2);
    var issuance_minute = $J('#time_issuance'+id).val().substring(3,5);

    var returned_date = Date.parse($J("#date_returned"+id).val());
    var returned_date2 = $J("#date_returned"+id).val();
    var returned_hour = $J('#time_returned'+id).val().substring(0,2);
    var returned_minute = $J('#time_returned'+id).val().substring(3,5);

    var started_date = Date.parse($J("#date_started"+id).val());
    var started_date2 = $J("#date_started"+id).val();
    var started_hour = $J('#time_started'+id).val().substring(0,2);
    var started_minute = $J('#time_started'+id).val().substring(3,5);

    var done_date = Date.parse($J("#date_done"+id).val());
    var done_date2 = $J("#date_done"+id).val();
    var done_hour = $J('#time_done'+id).val().substring(0,2);
    var done_minute = $J('#time_done'+id).val().substring(3,5);

    var reissue_date = Date.parse($J("#date_reissue"+id).val());
    var reissue_date2 = $J("#date_reissue"+id).val();
    var reissue_hour = $J('#time_reissue'+id).val().substring(0,2);
    var reissue_minute = $J('#time_reissue'+id).val().substring(3,5);

    var release_date = Date.parse($J("#date_release"+id).val());
    var release_date2 = $J("#date_release"+id).val();
    var release_hour = $J('#time_release'+id).val().substring(0,2);
    var release_minute = $J('#time_release'+id).val().substring(3,5);

    var regExp = /(\d{1,2})\:(\d{1,2})\:(\d{1,2})/;
    
    if ($J('#meridian'+id).val()=='PM'){
       if (parseFloat(received_hour) < 12)            
            received_hour = parseFloat(received_hour) + 12;
       received_time = received_hour+":"+received_minute+":00";
    }else{
       if (parseFloat(received_hour) == 12)            
            received_hour = '00';
       received_time = received_hour+":"+received_minute+":00";
    }

    if ($J('#started_meridian'+id).val()=='PM'){
       if (parseFloat(started_hour) < 12)            
            started_hour = parseFloat(started_hour) + 12;
       started_time = started_hour+":"+started_minute+":00";
    }else{  
       if (parseFloat(started_hour) == 12)            
            started_hour = '00';
       started_time = started_hour+":"+started_minute+":00";
    }
//added by: borj 2013/1/12
    if ($J('#done_meridian'+id).val()=='PM'){
       if (parseFloat(done_hour) < 12)            
            done_hour = parseFloat(done_hour) + 12;
       done_time = done_hour+":"+done_minute+":00";
    }else{  
       if (parseFloat(done_hour) == 12)            
            done_hour = '00';
       done_time = done_hour+":"+done_minute+":00";
    }

    if ($J('#issuance_meridian'+id).val()=='PM'){
       if (parseFloat(issuance_hour) < 12)            
            issuance_hour = parseFloat(issuance_hour) + 12;
       issuance_time = issuance_hour+":"+issuance_minute+":00";
    }else{
       if (parseFloat(issuance_hour) == 12)            
            issuance_hour = '00';
        issuance_time = issuance_hour+":"+issuance_minute+":00";
    }
             
    if ($J('#returned_meridian'+id).val()=='PM'){
       if (parseFloat(returned_hour) < 12)            
            returned_hour = parseFloat(returned_hour) + 12;
       returned_time = returned_hour+":"+returned_minute+":00";
    }else{
       if (parseFloat(returned_hour) == 12)            
            returned_hour = '00';
       returned_time = returned_hour+":"+returned_minute+":00";
    }

     if ($J('#reissue_meridian'+id).val()=='PM'){
       if (parseFloat(reissue_hour) < 12)            
            reissue_hour = parseFloat(reissue_hour) + 12;
       reissue_time = reissue_hour+":"+reissue_minute+":00";
    }else{
       if (parseFloat(reissue_hour) == 12)            
            reissue_hour = '00';
       reissue_time = reissue_hour+":"+reissue_minute+":00";
    }
    

//end borj

    
    if (str=='done'){
        if ($J('#date_done'+id).val().length==0){
            alert('Please enter the date done.');   
            $J('#date_done'+id).focus();
            return false;
        }else if ($J('#time_done'+id).val().length==0){
            alert('Please enter the time done.');   
            $J('#time_done'+id).focus();
            return false
        }else{    
            var done_date = Date.parse($J("#date_done"+id).val());
            var done_date2 = $J("#date_done"+id).val();        
            var diff_rec_done = (done_date - started_date);
            
            var done_time;
                
            done_hour = $J('#time_done'+id).val().substring(0,2);
            done_minute = $J('#time_done'+id).val().substring(3,5); 
            
            if ($J('#done_meridian'+id).val()=='PM'){
               if (parseFloat(done_hour) < 12)
                    done_hour = parseFloat(done_hour) + 12;
                    
               done_time = done_hour+":"+done_minute+":00";
            }else{
                if (parseFloat(done_hour) == 12)            
                    done_hour = '00';
            
                done_time = done_hour+":"+done_minute+":00";
            }
                
            if (diff_rec_done < 0){
                alert('Date Done of the blood units must be later than the Started Date of the blood units.');
                $J('#date_done'+id).focus();
                return false;
            }else if (diff_rec_done == 0){
                if(parseFloat(done_time.replace(regExp, "$1$2$3")) < parseFloat(started_time.replace(regExp, "$1$2$3"))){
                    alert("Time Done must be later than the Time Started");
                    return false;
                }else{
                    save_ajax_dateinfo('done', done_date2, done_time, id);
                }
                
            }else{
                save_ajax_dateinfo('done', done_date2, done_time, id);
            }
        }    
    }else if (str=='started'){
        if ($J('#date_started'+id).val().length==0){
            alert('Please enter the date started.');   
            $J('#date_started'+id).focus();
            return false;
        }else if ($J('#time_started'+id).val().length==0){
            alert('Please enter the time started.');   
            $J('#time_started'+id).focus();
            return false
        }else{    
            var started_date = Date.parse($J("#date_started"+id).val());
            var started_date2 = $J("#date_started"+id).val();        
            var diff_rec_started = (started_date - received_date);

            // added by: syboy 07/08/2015
            var done_date = Date.parse($J("#date_done"+id).val());       
            var diff_rec_done = (done_date - received_date);
            // end 

            var started_time;
                
            started_hour = $J('#time_started'+id).val().substring(0,2);
            started_minute = $J('#time_started'+id).val().substring(3,5); 
            
            if ($J('#started_meridian'+id).val()=='PM'){
               if (parseFloat(started_hour) < 12)
                    started_hour = parseFloat(started_hour) + 12;
                    
               started_time = started_hour+":"+started_minute+":00";
            }else{
                if (parseFloat(started_hour) == 12)            
                    started_hour = '00';
            
                started_time = started_hour+":"+started_minute+":00";
            }

            // added by: syboy 07/08/2015
            var done_time;
                
            done_hour = $J('#time_done'+id).val().substring(0,2);
            done_minute = $J('#time_done'+id).val().substring(3,5); 
            
            if ($J('#done_meridian'+id).val()=='PM'){
               if (parseFloat(done_hour) < 12)
                    done_hour = parseFloat(done_hour) + 12;
                    
               done_time = done_hour+":"+done_minute+":00";
            }else{
                if (parseFloat(done_hour) == 12)            
                    done_hour = '00';
            
                done_time = done_hour+":"+done_minute+":00";
            }
            // end

            // alert(diff_rec_started);
            // if(parseFloat(started_time.replace(regExp, "$1$2$3")) > parseFloat(done_time.replace(regExp, "$1$2$3"))){
            //     alert("Time Done must be later than the Time Started");
            //     return false;
            // }

            // else if(diff_rec_started < done_date){ //added by: syboy 06/23/2015
            //      // alert("Time Started must not be later than Time Done");
            //      alert(diff_rec_started > done_date);
            //      return false; // end
            // }else if(diff_rec_started > 0){
            //     alert("Time Started must not be later than Time Done");
            //     return false; // end
            // }  > parseFloat(done_time.replace(regExp, "$1$2$3"))

            var sum_diff_rec_started = diff_rec_started + parseFloat(started_time.replace(regExp, "$1$2$3"));
            var sum_diff_rec_done = diff_rec_done + parseFloat(done_time.replace(regExp, "$1$2$3"));

            if (diff_rec_started < 0){
                alert('Date Started of the blood units must be later than the Received Date of the blood units.');
                $J('#date_started'+id).focus();
                return false;
            }else if (sum_diff_rec_started > sum_diff_rec_done) { // edited by : syboy 07/08/2015
                alert("Time Started must be later than the Time Done");
                // alert(sum_diff_rec_done);
                return false;
            }else if (diff_rec_started == 0){

                if(parseFloat(started_time.replace(regExp, "$1$2$3")) < parseFloat(received_time.replace(regExp, "$1$2$3"))){
                    alert("Time Started must be later than the Time Received");
                    return false;
                }else{
                    save_ajax_dateinfo('started', started_date2, started_time, id);
                }
                
            }else{
                save_ajax_dateinfo('started', started_date2, started_time, id);
            }
        }    
    }else if (str=='issuance'){
        if ($J('#date_issuance'+id).val().length==0){
            alert('Please enter the issuance date.');   
            $J('#date_issuance'+id).focus();
            return false;
        }else if ($J('#time_issuance'+id).val().length==0){
            alert('Please enter the issuance time.');   
            $J('#time_issuance'+id).focus();
            return false
        }else{    
            var issuance_date = Date.parse($J("#date_issuance"+id).val());   
            var issuance_date2 = $J("#date_issuance"+id).val();     
            var diff_rec_issuance = (issuance_date - done_date);
            
            var issuance_time;
                
            issuance_hour = $J('#time_issuance'+id).val().substring(0,2);
            issuance_minute = $J('#time_issuance'+id).val().substring(3,5); 
            
            if ($J('#issuance_meridian'+id).val()=='PM'){
               if (parseFloat(issuance_hour) < 12) 
                    issuance_hour = parseFloat(issuance_hour) + 12;
               
               issuance_time = issuance_hour+":"+issuance_minute+":00";
            }else{
               if (parseFloat(issuance_hour) == 12)            
                    issuance_hour = '00';
                     
               issuance_time = issuance_hour+":"+issuance_minute+":00";
            }
            
            if (diff_rec_issuance < 0){
                alert('Issuance Date of the blood units must be later than the Done Date of the blood units.');
                $J('#date_issuance'+id).focus();
                return false;
            }else if (diff_rec_issuance == 0){
                if(parseFloat(issuance_time.replace(regExp, "$1$2$3")) < parseFloat(done_time.replace(regExp, "$1$2$3"))){
                    alert("Issuance Time must be later than the Time Done");
                    return false;
                }else{
                    save_ajax_dateinfo('issuance', issuance_date2, issuance_time, id);
                }
                
            }else{
                save_ajax_dateinfo('issuance', issuance_date2, issuance_time, id);
            }
        }    
    }

    //added by Kenneth 10/06/2016
    else if (str=='release'){
        release_result = $J('#result'+id).val();

        if ($J('#date_release'+id).val().length==0){
            alert('Please enter the release date.');   
            $J('#date_release'+id).focus();
            return false;
        }else if ($J('#time_release'+id).val().length==0){
            alert('Please enter the release time.');   
            $J('#time_release'+id).focus();
            return false
        }else{    
            var release_date = Date.parse($J("#date_release"+id).val());   
            var release_date2 = $J("#date_release"+id).val();     
            var diff_rec_release = (release_date - done_date);
            
            var release_time;
                
            release_hour = $J('#time_release'+id).val().substring(0,2);
            release_minute = $J('#time_release'+id).val().substring(3,5); 

            if ($J('#release_meridian'+id).val()=='PM'){
               if (parseFloat(release_hour) < 12) 
                    release_hour = parseFloat(release_hour) + 12;
               
               release_time = release_hour+":"+release_minute+":00";
            }else{
               if (parseFloat(release_hour) == 12)            
                    release_hour = '00';
                     
               release_time = release_hour+":"+release_minute+":00";
            }
            
            if (diff_rec_release < 0){
                alert('Release Date of the blood units must be later than the Done Date of the blood units.');
                $J('#date_release'+id).focus();
                return false;
            }else if (diff_rec_release == 0){
                if(parseFloat(release_time.replace(regExp, "$1$2$3")) < parseFloat(done_time.replace(regExp, "$1$2$3"))){
                    alert("Release Time must be later than the Time Done");
                    return false;
                }else{
                    save_ajax_dateinfo('release', release_date2, release_time, id, '',release_result);
                }
                
            }else{
                save_ajax_dateinfo('release', release_date2, release_time, id, '',release_result);
            }
        }    
    }   


//added by:borj 2013/24/2013
    else if (str=='returned'){
        if ($J('#date_returned'+id).val().length==0){
            alert('Please enter the returned date.');   
            $J('#date_returned'+id).focus();
            return false;
        }else if ($J('#time_returned'+id).val().length==0){
            alert('Please enter the returned time.');   
            $J('#time_returned'+id).focus();
            return false
        }else{    
            var returned_date = Date.parse($J("#date_returned"+id).val());   
            var returned_date2 = $J("#date_returned"+id).val();     
            var diff_rec_returned = (returned_date - issuance_date);
            
            var returned_time;
                
            returned_hour = $J('#time_returned'+id).val().substring(0,2);
            returned_minute = $J('#time_returned'+id).val().substring(3,5); 
            
            if ($J('#returned_meridian'+id).val()=='PM'){
               if (parseFloat(returned_hour) < 12) 
                    returned_hour = parseFloat(returned_hour) + 12;
               
               returned_time = returned_hour+":"+returned_minute+":00";
            }else{
               if (parseFloat(returned_hour) == 12)            
                    returned_hour = '00';
                     
               returned_time = returned_hour+":"+returned_minute+":00";
}

            if (diff_rec_returned < 0){
                alert('Returned Date of the blood units must be later than the Issuance Date of the blood units.');
                $J('#date_returned'+id).focus();
                return false;
            }else if (diff_rec_returned == 0){
                if(parseFloat(returned_time.replace(regExp, "$1$2$3")) < parseFloat(issuance_time.replace(regExp, "$1$2$3"))){
                    alert("Returned Time must be later than the Time Issuance");
                    return false;
                }else{
                    save_ajax_dateinfo('returned', returned_date2, returned_time, id);
                }
                
            }else{
                save_ajax_dateinfo('returned', returned_date2, returned_time, id);
            }
        }
    }
    else if (str=='reissue'){
        if ($J('#date_reissue'+id).val().length==0){
            alert('Please enter the reissue date.');   
            $J('#date_reissue'+id).focus();
            return false;
        }else if ($J('#time_reissue'+id).val().length==0){
            alert('Please enter the reissue time.');   
            $J('#time_reissue'+id).focus();
            return false
        }else{    
            var reissue_date = Date.parse($J("#date_reissue"+id).val());   
            var reissue_date2 = $J("#date_reissue"+id).val();     
            var diff_rec_reissue = (reissue_date - returned_date);
            
            var reissue_time;
     
            reissue_hour = $J('#time_reissue'+id).val().substring(0,2);
            reissue_minute = $J('#time_reissue'+id).val().substring(3,5); 
     
            if ($J('#reissue_meridian'+id).val()=='PM'){
               if (parseFloat(reissue_hour) < 12) 
                    reissue_hour = parseFloat(reissue_hour) + 12;
               
               reissue_time = reissue_hour+":"+reissue_minute+":00";
            }else{
               if (parseFloat(reissue_hour) == 12)            
                    reissue_hour = '00';
                     
               reissue_time = reissue_hour+":"+reissue_minute+":00";
            }
            
            if (diff_rec_reissue < 0){
                alert('Reissue Date of the blood units must be later than the Returned Date of the blood units.');
                $J('#date_reissue'+id).focus();
                return false;
            }else if (diff_rec_reissue == 0){
                if(parseFloat(reissue_time.replace(regExp, "$1$2$3")) < parseFloat(returned_time.replace(regExp, "$1$2$3"))){
                    alert("Reissue Time must be later than the Time Returned");
                    return false;
                }else{
                    save_ajax_dateinfo('reissue', reissue_date2, reissue_time, id);
                }
                
            }else{
                save_ajax_dateinfo('reissue', reissue_date2, reissue_time, id);
            }
        }
 }
  else  if ((str=='consumed')&&($('date_issuance'+id).value!='')&&($('date_reissue'+id).value=='')){
        if ($J('#date_consumed'+id).val().length==0){
            alert('Please enter the consumed date.');   
            $J('#date_consumed'+id).focus();
            return false;
        }else if ($J('#time_consumed'+id).val().length==0){
            alert('Please enter the consumed time.');   
            $J('#time_consumed'+id).focus();
            return false
        }else{    
            var consumed_date = Date.parse($J("#date_consumed"+id).val());   
            var consumed_date2 = $J("#date_consumed"+id).val();     
            var diff_rec_consumed = (consumed_date - issuance_date);
           
            var consumed_time;
                
            consumed_hour = $J('#time_consumed'+id).val().substring(0,2);
            consumed_minute = $J('#time_consumed'+id).val().substring(3,5); 
            
            if ($J('#consumed_meridian'+id).val()=='PM'){
               if (parseFloat(consumed_hour) < 12) 
                    consumed_hour = parseFloat(consumed_hour) + 12;
               
                consumed_time = consumed_hour+":"+consumed_minute+":00";
            }else{
               if (parseFloat(consumed_hour) == 12)            
                    consumed_hour = '00';
                     
               consumed_time = consumed_hour+":"+consumed_minute+":00";
    }    
            
            if (diff_rec_consumed < 0){
                alert('Consumed Date of the blood units must be later than the Issuance Date of the blood units.');
                $J('#date_consumed'+id).focus();
                return false;
            }else if (diff_rec_consumed == 0){
                if(parseFloat(consumed_time.replace(regExp, "$1$2$3")) < parseFloat(issuance_time.replace(regExp, "$1$2$3"))){
                    alert("Consumed Time must be later than the Time Issuance");
                    return false;
                }else{
                    save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
                }
                
            }else{
                save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
            }
        } 
    //end borj            
}
   else if ((str=='consumed')&&($('date_issuance'+id).value!='')&&($('date_reissue'+id).value!='')){
        
        if ($J('#date_consumed'+id).val().length==0){
            alert('Please enter the consumed date.');   
            $J('#date_consumed'+id).focus();
            return false;
        }else if ($J('#time_consumed'+id).val().length==0){
            alert('Please enter the consumed time.');   
            $J('#time_consumed'+id).focus();
            return false
        }else{    
            var consumed_date = Date.parse($J("#date_consumed"+id).val());   
            var consumed_date2 = $J("#date_consumed"+id).val();     
            var diff_rec_consumed = (consumed_date - reissue_date);

            var consumed_time;

            consumed_hour = $J('#time_consumed'+id).val().substring(0,2);
            consumed_minute = $J('#time_consumed'+id).val().substring(3,5); 
            
            if ($J('#consumed_meridian'+id).val()=='PM'){
               if (parseFloat(consumed_hour) < 12) 
                    consumed_hour = parseFloat(consumed_hour) + 12;
               
                consumed_time = consumed_hour+":"+consumed_minute+":00";
            }else{
               if (parseFloat(consumed_hour) == 12)            
                    consumed_hour = '00';
                     
               consumed_time = consumed_hour+":"+consumed_minute+":00";
            }
     
            if (diff_rec_consumed < 0){
                alert('Consumed Date of the blood units must be later than the Reissue Date of the blood units.');
                $J('#date_consumed'+id).focus();
                return false;
            }else if (diff_rec_consumed == 0){
                if(parseFloat(consumed_time.replace(regExp, "$1$2$3")) < parseFloat(reissue_time.replace(regExp, "$1$2$3"))){
                    alert("Consumed Time must be later than the Time Reissue");
                    return false;
                }else{
                    save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
                }
     
            }else{
                save_ajax_dateinfo('consumed', consumed_date2, consumed_time, id);
            }
 }
    }    
}

 
function refreshFrame(outputResponse){
    alert(""+outputResponse);
    window.location.reload();
}


function getCurrentDate(id){
    if ($J('#is_issued'+id).is(":checked")){
                

        if ($('date_consumed'+id).value==''){
            $('date_consumed'+id).value=$('current_date').value;
            $('time_consumed'+id).value = $('current_time').value;
            $('consumed_meridian'+id).value=$('current_meridian').value;

        if ($('date_issuance'+id).value==''){
             $date_issuance = date("m/d/Y",strtotime($row_status['issuance_date']));
             $time_issuance = date("h:i",strtotime($row_status['issuance_date']));
             $issuance_meridian = date("A",strtotime($row_status['issuance_date']));
        }
    }else{
       $date_issuance = '';
       $time_issuance = '';
       $issuance_meridian = 'AM';
             
    }        
}
}

//Added by Nick, 11/23/2013
function openPrintStubDialog(){
    $J("#printClaimStubDialog").dialog("open");    
}

//for claim stub print with dialog
function printClaimStub(refno,cmCheck,coombsCheck,compCheck,duCheck,cmVal,coombsVal,compVal,duVal,others){
    var url = "seg-print-claim-stub.php?refno="+refno+"&cmCheck="+cmCheck+
              "&coombsCheck="+coombsCheck+"&compCheck="+compCheck+
              "&duCheck="+duCheck+"&cmVal="+cmVal+"&coombsVal="+coombsVal+
              "&compVal="+compVal+"&duVal="+duVal+"&others="+others;
    window.open(url,'Rep_Gen','menubar=no,directories=no');
}

function printClaimStub2(refno){
    var url = "seg-print-claim-stub2.php?refno="+refno;
    window.open(url,'Rep_Gen','menubar=no,directories=no');
}
//block letters in a textbox
function key_check(e, value){
    if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
        return true;
    }else 
        return false;
}
//  Added by Kenneth 10/06/2016
//  Modified by: JEFF
//  Date: 09-20-17
//  Purpose: JQuery functions are modified for trapping on request dates upon submit
function getReleaseCurrentDate(id){
    if ($J('#is_released'+id).is(":checked")){
        if ($('date_release'+id).value==''){
            $('date_release'+id).value=$('current_date').value;
            $('time_release'+id).value = $('current_time').value;
            $('release_meridian'+id).value=$('current_meridian').value;
        }
        if ($('trap_chk'+id).value == 'chk') {
                // alert("trap!");
                $('trap_result'+id).value = 'trap';
            }
    }else{
        $('date_release'+id).value= '';
        $('time_release'+id).value = '';
        $('release_meridian'+id).value= 'AM';
        $('trap_result'+id).value = '';
    }        
}

//added by: borj
//2013/23/11
function getReissueCurrentDate(id){
    if ($J('#is_reissue'+id).is(":checked")){
        if ($('date_reissue'+id).value==''){
            $('date_reissue'+id).value=$('current_date').value;
            $('time_reissue'+id).value = $('current_time').value;
            $('reissue_meridian'+id).value=$('current_meridian').value;
        }
        if ($('trap_chk'+id).value == 'chk') {
                // alert("trap!");
                $('trap_reIssue'+id).value = 'trap';
            }
    }else{
        $('date_reissue'+id).value= '';
        $('time_reissue'+id).value = '';
        $('reissue_meridian'+id).value= 'AM';
        $('trap_reIssue'+id).value = '';
    }        
}

function getConsumedCurrentDate(id){
    if ($J('#is_consumed'+id).is(":checked")){
        if ($('date_consumed'+id).value==''){
            $('date_consumed'+id).value=$('current_date').value;
            $('time_consumed'+id).value = $('current_time').value;
            $('consumed_meridian'+id).value=$('current_meridian').value;
        }
        if ($('trap_chk'+id).value == 'chk') {
                // alert("trap!");
                $('trap_consumed'+id).value = 'trap';
            }
    }else{
        $('date_consumed'+id).value= '';
        $('time_consumed'+id).value = '';
        $('consumed_meridian'+id).value= 'AM';
        $('trap_consumed'+id).value = '';
    }        
}

//Edited by Borj 2014-08-12
function getStatusRoutine(mode, dateinfo, timeinfo, id){
      var statusRoutine = "Routine";
       // save_ajax_datainfo(mode, dateinfo, timeinfo, id, statusRoutine);
       $('is_status_1'+id).checked=false;
       $('isStat_0').checked = false;
       $('isStat_1').checked = false;
}
function getStatusStat(mode, dateinfo, timeinfo, id){
      var statusStat = "Stat";
      //save_ajax_datainfo(mode, dateinfo, timeinfo, id, statusStat);
        if($('is_status_1'+id).checked){

        }else{
           $('isStat_1').checked = false;
        }
}

function getReturnReason(mode, dateinfo, timeinfo, id){
         var returnReason =prompt("Please put the reason.");
         var refno = $('refno').innerHTML;
         var service_code = $('test_code').innerHTML;
         var index = id.replace(service_code,'');

if (returnReason){
    
     $('date_returned_show'+id).style.display = "block";
     $('date_returned'+id).value=$('current_date').value;
     $('time_returned'+id).value=$('current_time').value;
     $('returned_meridian'+id).value=$('current_meridian').value;
      
      xajax_save_dateinfo(refno, service_code, index, mode, dateinfo, timeinfo, returnReason); 
     
     }else{
    
     $('is_returnedreason'+id).checked=false;

     }
        
}

function getStartedCurrentDate(id,chk){
    if ($J('#is_started'+id).is(":checked")){
            if ($('date_started'+id).value==''){
                $('date_started'+id).value=$('current_date').value;
                $('time_started'+id).value = $('current_time').value;
                $('started_meridian'+id).value=$('current_meridian').value;
            }
            if ($('trap_chk'+id).value == 'chk') {
                // alert("trap!");
                $('trap_dateStarted'+id).value = 'trap';
            }
        }
        else{
            $('date_started'+id).value= '';
            $('time_started'+id).value = '';
            $('started_meridian'+id).value= 'AM';
            $('trap_dateStarted'+id).value = '';

        }     
}

function getDoneCurrentDate(id){
    if ($J('#is_done'+id).is(":checked")){
        if ($('date_done'+id).value==''){
            $('date_done'+id).value=$('current_date').value;
            $('time_done'+id).value = $('current_time').value;
            $('done_meridian'+id).value=$('current_meridian').value;
        }
        if ($('trap_chk'+id).value == 'chk') {
                // alert("trap!");
                $('trap_dateDone'+id).value = 'trap';
            }
    }else{
        $('date_done'+id).value= '';
        $('time_done'+id).value = '';
        $('done_meridian'+id).value= 'AM';
        $('trap_dateDone'+id).value = '';
    }        
}

function getIssuanCurrentDate(id){
    if ($J('#is_issued'+id).is(":checked")){
        if ($('date_issuance'+id).value==''){
            $('date_issuance'+id).value=$('current_date').value;
            $('time_issuance'+id).value = $('current_time').value;
            $('issuance_meridian'+id).value=$('current_meridian').value;
        }
        if ($('trap_chk'+id).value == 'chk') {
                // alert("trap!");
                $('trap_issuanceDate'+id).value = 'trap';
            }
    }else{
        $('date_issuance'+id).value= '';
        $('time_issuance'+id).value = '';
        $('issuance_meridian'+id).value= 'AM';
        $('trap_issuanceDate'+id).value = '';
    }        
}


//added by VAS 06/19/2019
function checkBloodResult(obj){
    //check the LIS if there is result to be fetched
    if ($J('#in_lis').val()==1){
        var refno = $('refno').innerHTML;
        var service_code = $('test_code').innerHTML;
        var pid = $('hrn').innerHTML;
        var serialno = $J.trim($J(obj).val());

        if (serialno){
           if(confirm('Do you want to update the saved blood information result details?')){ 
                xajax_getLISResultInfo(pid, refno, service_code, serialno, obj.id);
           }     
        }else{
            alert('Please scan or input the serial number of the blood unit bag.');
        }
    }
    
}


function assignResultValue(details){

    if (details.error==1){
        alert('Saving blood Result information details FAILED.');
    }else{
        alert('SUCCESSFULLY save the blood Result information details.');
        window.location.reload();
    }
    
    /*var idname = details.service_code+details.index;
    
    $J('#blood_dept'+details.index).text(details.location);
    $J('#component'+idname).val(details.blood_component);
    $J('#blood_source'+details.index).text(details.blood_source);
    $J('#date_received'+idname).val(details.date_received);
    $J('#date_started'+idname).val(details.date_crossmatched);
    $J('#date_done'+idname).val(details.date_done);
    $J('#result'+idname).val(details.crossmatched_result);*/
        
}
//end //added by VAS 06/19/2019

//end borj