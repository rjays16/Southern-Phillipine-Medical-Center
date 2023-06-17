/**
 * @author Nick B. Alcala 5-31-2015
 */

var $J = jQuery.noConflict(),
    billArguments = {
        encounterNr : null,
        billDate : null,
        billFromDate : null,
        billNr : null,
        deathDate :null,
        firstCaseRate : null,
        secondCaseRate : null,
        accommodationType : null,
        opd_area : null,
        firstCaseMultiplier : null,
        secondCaseMultiplier : null,
    },
    ajax = {},
    xloTabs = null,

    laboratoryTotal = 0,
    radiologyTotal = 0,
    supplyTotal = 0,
    otherTotal = 0,
    obgyeTotal = 0 

    ;/*end variable declarations */

$J(function(){

    xloTabs = $J('#xlo-tabs').tabs({
        collapsible:true,
        active:false
    });

});

/**
 * start
 */
function populateBill(){
    $('isFinalBill').checked=false;
    setArguments();
    start();

}

function firstpopulateBill(){
    billArguments = {
        encounterNr : $J('#encounter_nr').val(),
        billDate : $J('#billdate').val(),
        billFromDate : $J('#admission_dte').val(),
        billNr : $J('#bill_nr').val(),
        deathDate : $('isdied').checked ? $J('#deathdate').val() : '',
        firstCaseRate : firstratecode,
        secondCaseRate : secondratecode,
        accommodationType : isPAYWARD() ? 2 : 1,
        opd_area : $J('#opd_area option:selected').val(),
    };
    xajax.call('populateBillFirst',{
        asynchronous:true,
        parameters:[
            billArguments.encounterNr,
            billArguments.billDate,
            {less_than_encdt: $J('#less_than_encdt').val(), 
            is_exhausted: $J("#is_exhausted").val()}
        ],
        callback:setCallBack(stop)
    });
    populateBill();
}

function secondpopulateBill(){
    billArguments = {
        encounterNr : $J('#encounter_nr').val(),
        billDate : $J('#billdate').val(),
        billFromDate : $J('#admission_dte').val(),
        billNr : $J('#bill_nr').val(),
        deathDate : $('isdied').checked ? $J('#deathdate').val() : '',
        firstCaseRate : firstratecode,
        secondCaseRate : secondratecode,
        accommodationType : isPAYWARD() ? 2 : 1,
        opd_area : $J('#opd_area option:selected').val(),
    };
    
    xajax.call('populateBillFirst',{
        asynchronous:true,
        parameters:[
            billArguments.encounterNr,
            billArguments.billDate,
            {less_than_encdt: $J('#less_than_encdt').val(), 
            is_exhausted: $J("#is_exhausted").val()}
        ],
        callback:setCallBack(stop)
    });
}

function getInsuranceRemains(member_id,isPrincipal){
    
    var admissiondate = $J('#admission_dte').val();
    var p_id = $J('#pid').val();
    var encounter_type = $J('#encounter_type').val(); // Unknown
    var dept_nr = $J('#consulting_dept_nr').val(); // Unknown
    // alert(member_id+"---"+p_id+"---"+isPrincipal+"---"+admissiondate.substring(0,4))
    xajax.call('checkRemainingInsurance',{
        asynchronous:true,
        parameters:[member_id,p_id,isPrincipal,admissiondate.substring(0,4),encounter_type,dept_nr],
        callback:setCallBack(stop)
    });
}

function setArguments(){
    var regExp = /^\d+$/;

    billArguments = {
        encounterNr : $J('#encounter_nr').val(),
        billDate : $J('#billdate').val(),
        billFromDate : $J('#admission_dte').val(),
        billNr : $J('#bill_nr').val(),
        deathDate : $('isdied').checked ? $J('#deathdate').val() : '',
        firstCaseRate : firstratecode,
        secondCaseRate : secondratecode,
        accommodationType : isPAYWARD() ? 2 : 1,
        opd_area : $J('#opd_area option:selected').val(),
        firstCaseMultiplier : (firstratecode.match(regExp)) ? firstMultiplier : '',
        secondCaseMultiplier : (secondratecode.match(regExp)) ? secondMultiplier : '',
    };
}

function start(){
    showLoadingGui();
    $J('#btnSave').prop('disabled', true);
    isComputing = true;
    checkInsurance();
}

/**
 * ajax-1
 */
function checkInsurance(){
    xajax.call('checkInsurance',{
        asynchronous:true,
        parameters:[billArguments.encounterNr],
        callback:setCallBack(ajaxPopulateAccommodation)
    });
}

/**
 * ajax-2
 */
function ajaxPopulateAccommodation(){
    showSectionLoadingAnimation('acc');
    //reloadAccommodation(ajaxPopulateXlo);
    reloadAccommodation(ajaxPopulateLaboratoryItems);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadAccommodation(){
    isComputing = true;
    setArguments();
    showSectionLoadingAnimation('acc');
    xajax.call('populateAccommodation',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(getCallBack(arguments))
    });
}

/**
 * ajax-3
 */
function ajaxPopulateLaboratoryItems(){
    if(xloTabs.tabs('option','selected') != 0)
        xloTabs.tabs('select',0);
    hideSectionLoadingAnimation('acc');
    showSectionLoadingAnimation('xlo');
    reloadLaboratoryItems(ajaxPopulateRadiologyItems);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadLaboratoryItems(){
    var args = arguments;
    $J.ajax({
        url : 'ajax/ajax-billing.php?request=getLaboratoryItems',
        data : billArguments,
        dataType : 'json',
        success : function(data){
            $J('#laboratory-items').html(Mustache.render($J('#lb-rd-su-template').html(),data));
            getCallBack(args)();
            laboratoryTotal = data.total;
            addXloTotal();
        },
        error : function(){ alert('Laboratory : an error occured') }
    });
}

/**
 * ajax-4
 */
function ajaxPopulateRadiologyItems(){
    if(xloTabs.tabs('option','selected') != 1)
        xloTabs.tabs('select',1);
    hideSectionLoadingAnimation('acc');
    showSectionLoadingAnimation('xlo');
    reloadRadiologyItems(ajaxPopulateOBGyneItems);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadRadiologyItems(){
    var args = arguments;
    $J.ajax({
        url : 'ajax/ajax-billing.php?request=getRadiologyItems',
        data : billArguments,
        dataType : 'json',
        success : function(data){
            $J('#radiology-items').html(Mustache.render($J('#lb-rd-su-template').html(),data));
            getCallBack(args)();
            radiologyTotal = data.total;
            addXloTotal();
        },
        error : function(){ alert('Radiology : an error occured') }
    });
}


function ajaxPopulateOBGyneItems(){
    if(xloTabs.tabs('option','selected') != 1)
        xloTabs.tabs('select',1);
    hideSectionLoadingAnimation('acc');
    showSectionLoadingAnimation('xlo');
    reloadOBGyneItems(ajaxPopulateSupplyItems);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadOBGyneItems(){
    var args = arguments;
    $J.ajax({
        url : 'ajax/ajax-billing.php?request=getOBGyneItems',
        data : billArguments,
        dataType : 'json',
        success : function(data){
            $J('#obgyne-items').html(Mustache.render($J('#lb-rd-su-template').html(),data));
            getCallBack(args)();
            obgyeTotal = data.total;
            addXloTotal();
        },
        error : function(){ alert('Radiology : an error occured') }
    });
}


/**
 * ajax-5
 */
function ajaxPopulateSupplyItems(){
    reloadSupplyItems(ajaxPopulateOtherItems);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadSupplyItems(){
    isComputing = true;
    setArguments();
    if(xloTabs.tabs('option','selected') != 2)
        xloTabs.tabs('select',2);
    hideSectionLoadingAnimation('acc');
    showSectionLoadingAnimation('xlo');
    var args = arguments;
    $J.ajax({
        url : 'ajax/ajax-billing.php?request=getSupplyItems',
        data : billArguments,
        dataType : 'json',
        success : function(data){
            if(data.items.length > 0){
                $J.each(data.items,function(index,item){
                    data.items[index] = Object.extend(item,{
                        deleteButton : function(){
                            if($J('#isFinalBill').is(':checked'))
                                return '';
                            var source = '';
                            if(item.source == 'MS')
                                source = 'Pharmacy';
                            else if(item.source == 'OA')
                                source = 'Miscellaneous';
                            else
                                return '';
                            return '<img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer;" onclick="promptDelMiscService(\''+item.service_code+'\',\''+item.description.substring(0,70)+'\',\''+source+'\')" />';
                        }
                    });
                });
            }
            $J('#supply-items').html(Mustache.render($J('#ms-oa-template').html(),data));
            getCallBack(args)();
            supplyTotal = data.total;
            addXloTotal();
        },
        error : function(){ alert('Radiology : an error occured') }
    });
}

/**
 * ajax-6
 */
function ajaxPopulateOtherItems(){
    reloadOtherItems(ajaxPopulateMedicine);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadOtherItems(){
    if(xloTabs.tabs('option','selected') != 3)
        xloTabs.tabs('select',3);
    hideSectionLoadingAnimation('acc');
    showSectionLoadingAnimation('xlo');
    var args = arguments;
    $J.ajax({
        url : 'ajax/ajax-billing.php?request=getOtherItems',
        data : billArguments,
        dataType : 'json',
        success : function(data){
            if(data.items.length > 0){
                $J.each(data.items,function(index,item){
                    data.items[index] = Object.extend(item,{
                        deleteButton : function(){
                            if($J('#isFinalBill').is(':checked'))
                                return '';
                            var source = '';
                            if(item.source == 'MS')
                                source = 'Pharmacy';
                            else if(item.source == 'OA')
                                source = 'Miscellaneous';
                            else
                                return '';
                            return '<img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer;" onclick="promptDelMiscService(\''+item.service_code+'\',\''+item.description.substring(0,70)+'\',\''+source+'\')" />';
                        }
                    });
                });
            }
            $J('#other-items').html(Mustache.render($J('#ms-oa-template').html(),data));
            getCallBack(args)();
            otherTotal = data.total;
            addXloTotal();
            xloTabs.tabs('option','active',false);
        },
        error : function(){ alert('Radiology : an error occured') }
    });
}

function ajaxPopulateXlo(){
    hideSectionLoadingAnimation('acc');
    showSectionLoadingAnimation('xlo');
    reloadXlo(ajaxPopulateMedicine);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadXlo(){
    showSectionLoadingAnimation('xlo');
    xajax.call('populateXLO',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(getCallBack(arguments))
    });
}

/**
 * ajax-7
 */
function ajaxPopulateMedicine(){
    hideSectionLoadingAnimation('xlo');
    showSectionLoadingAnimation('med');
    reloadMedicines(ajaxPopulateOperatingRooms);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadMedicines(){
    isComputing = true;
    setArguments();
    showSectionLoadingAnimation('med');
    xajax.call('populateMeds',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(getCallBack(arguments))
    });
}

/**
 * ajax-8
 */
function ajaxPopulateOperatingRooms(){
    hideSectionLoadingAnimation('med');
    showSectionLoadingAnimation('ops');
    reloadOperatingRooms(populateMiscellaneous);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadOperatingRooms(){
    isComputing = true;
    setArguments();
    showSectionLoadingAnimation('ops');
    xajax.call('getBilledOps',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(getCallBack(arguments))
    });
}

/**
 * ajax-9
 */
function populateMiscellaneous(){
    hideSectionLoadingAnimation('ops');
    showSectionLoadingAnimation('misc');
    reloadMiscellaneous(ajaxClassification);
}

/**
 * Optional parameter func:function - custom callback function for ajax onComplete event. Default ajaxPopulateBill:void
 */
function reloadMiscellaneous(){
    isComputing = true;
    setArguments();
    showSectionLoadingAnimation('misc');
    xajax.call('populateMisc',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(getCallBack(arguments))
    });
}

/**
 * ajax-10
 */
function ajaxClassification(){
    showSectionLoadingAnimation('total');
    showSectionLoadingAnimation('doc');
    hideSectionLoadingAnimation('misc');
    xajax.call('classification',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate],
        callback:setCallBack(getConfinement)
    });
}

/**
 * ajax-11
 */
function getConfinement(){
    hideSectionLoadingAnimation('total');
    xajax.call('confinment',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(ajaxGetConfineTypeOption)
    });
}

/**
 * ajax-12
 */
function ajaxGetConfineTypeOption(){
    xajax.call('getConfineTypeOption',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(ajaxGetCurrentLimit)
    });
}

/**
 * ajax-13
 */
function ajaxGetCurrentLimit(){
    xajax.call('getCurrentLimit',{
        asynchronous:true,
        parameters:[billArguments.encounterNr, billArguments.billDate, billArguments.billFromDate, billArguments.deathDate],
        callback:setCallBack(ajaxPopulateBill)
    });
}

/**
 * ajax-14 final
 */
function ajaxPopulateBill(){
    setArguments();
    showSectionLoadingAnimation('final');
    xajax.call('populateBill',{
        asynchronous:true,
        parameters:[
            billArguments.encounterNr,
            billArguments.billDate,
            billArguments.billFromDate,
            billArguments.deathDate,
            billArguments.firstCaseRate,
            billArguments.secondCaseRate,
            billArguments.accommodationType,
            billArguments.opd_area,
            billArguments.firstCaseMultiplier,
            billArguments.secondCaseMultiplier,
        ],
        callback:setCallBack(stop)
    });
}
/**
 * stop
 */
function stop(){
    hideSectionLoadingAnimation('all');
    hideLoadingGui();
    var case_type = $j("#first_rate option:selected").attr('case_type');
    var IPD_ER = 3, IPD_OPD = 4;
    var case_date_parse =  Date.parse($j('#admission_dte').val());
    var bill_date_parse = Date.parse($j('#billdate').val());
    var diff_case_bill = 0;
    var thirty_min_parse = $j('#thirty_min_parse').val();

    var accom_effec =  Date.parse($j('#accom_effectivity').val());

    if(bill_date_parse > case_date_parse){
        diff_case_bill = parseInt(bill_date_parse) - parseInt(case_date_parse);
    }
    // $j('#less_than_encdt').val(0);
    //ADDED BY EARL GALOPE 06/02/2018
    if($J('#daysCovered').val()<='1' && $('isFinalBill').checked && $J('#is24HrsPrompt').indexOf('below 24 hours')<=0){
        if(case_type === 'm' && ($j('#encounter_type').val() == IPD_ER || $j('#encounter_type').val() == IPD_OPD)){
             $J('#btnSave').attr('disabled', true).addClass("ui-state-disabled");
        }
    }else if($('isdied').checked && $('isFinalBill').checked && $J('#isDeathDateToggled').val()=='1'){
        if(case_type === 'm' && ($j('#encounter_type').val() == IPD_ER || $j('#encounter_type').val() == IPD_OPD)){
            $J('#btnSave').attr('disabled', true).addClass("ui-state-disabled");
        }
    }else if(bill_date_parse < case_date_parse || (diff_case_bill < thirty_min_parse)){
        $j('#btnSave').attr('disabled', true).addClass("ui-state-disabled");
    }
    else{
        $J('#btnSave').attr('disabled', false).removeClass("ui-state-disabled");
    }
    
    if(case_date_parse > accom_effec){
        $J('#btnaccommodation').attr('style', "display:none");
    }
     // $j('#btnSave').attr('disabled', false); //commented by earl Galope 06/02/2018
     //end
    isComputing = false;
}

function setCallBack(callback){
    var cb = xajax.callback.create();
    cb.onComplete = callback;
    return cb;
}

function getCallBack(args){
    if(args.length == 1){
        if(typeof(args[0]) == "function")
            return args[0];
    }
    return ajaxPopulateBill;
}

function showSectionLoadingAnimation(prefix){
    $J('.'+prefix+'-loading').show();
}

function hideSectionLoadingAnimation(prefix){
    if(prefix=='all')
        $J('[class*="-loading"]').hide();
    else
        $J('.'+prefix+'-loading').hide();
}

function showLoadingGui(){
    return overlib('Please wait ...<br><img src="../../images/ajax_bar.gif">',
        WIDTH,300, TEXTPADDING,5, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        NOCLOSE, CAPTION,'Fetching information',
        MIDX,0, MIDY,0,
        STATUS,'Fetching information');
}

function hideLoadingGui(){
    cClick();
}

function addXloTotal(){
    miscServices_computed = laboratoryTotal + radiologyTotal + supplyTotal + otherTotal + obgyeTotal;
    $J('#hsAP').html(numFormat(miscServices_computed));
    $j('#save_total_srv_charge').val(miscServices_computed);
}