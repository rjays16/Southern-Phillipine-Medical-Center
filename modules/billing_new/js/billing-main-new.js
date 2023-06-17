var dialogSelEnc, deathdate ,dialogInsurance, dialogInsurance, origcurdate,
    acc_computed = 0, //total computation of accommodation
    miscServices_computed = 0, //total computation of miscellaneous services
    med_computed = 0, //total computation of drugs and medicines
    miscCharges_computed= 0, //total computation of miscellaneous charges
    ops_computed= 0, totalPackage= 0, PercentPF, PercentHCI, HCIPackageAmount=0, PFPackageAmount = 0, totalHCIDiscount= 0,
    totalPFDiscount= 0, TotalDiscount= 0, HCIExcess= 0, PFExcess= 0, TotalExcess= 0, totalNet= 0, totalGross= 0, totalHCI= 0,
    totalPF= 0, totalHealthInsuranceHF = 0, totalHealthInsurancePF = 0, PFd1 = 0, PFd2 = 0, PFd3 = 0, PFd4 = 0, HIadm = 0,
    HIsurg = 0, HIanes = 0, tmpHIadm = 0, tmpHIsurg = 0, tmpHIanes = 0, deposit= 0, returnMeds= 0, firstcase=0, secondcase=0,
    firstratecodeHolder='', firstratecode='', secondratecodeHolder='', secondratecode='',
    servDisc=0, servHIC=0, bill_nr, accexcess=0, tmpRVU = 0, drCharge = 0, bill_nr,
    NBB = '5', HSM = '9', POS='13', PHS = false, process_type='' , D1_nr = '', D4_nr = '', D3_nr = '', D1_chrg = '', D4_chrg = '', D3_chrg = '',
    D2_nr = '', D2_chrg = '', TotalUnsed = 0, TotalAutoExcess =0, firstMultiplier='', secondMultiplier='';
    D1_discount1 = 0, D2_discount1 = 0, D3_discount1 = 0, D4_discount1 = 0, D1_discount2 = 0, D2_discount2 = 0, D3_discount2 = 0, D4_discount2 = 0,
    D1_coverage1 = 0, D2_coverage1= 0, D3_coverage1 = 0, D4_coverage1 = 0, D1_coverage2 = 0, D2_coverage2= 0, D3_coverage2 = 0, D4_coverage2 = 0, opsCode = '', temp_discount = 0;
    var HSM_desc = "HOSPITAL SPONSORED MEMBER";
    var NBB_desc = "SPONSORED MEMBER";
    isMedicolegal = false;
var encounter_date_start, encounter_date_end, procedure_date_start, procedure_date_end;
var NEWBORN_PKG = '99432';
var NEWBORN_PKG2 = '99460';
var TEMP_INSURANCE_NR = 11111;
var INSURANCE_TEMP = 1;
var IPD_ER = 3, IPD_OPD = 4, first_laterality='',second_laterality='';
var PAY = 2, SERVICE = 1;
//added by Nick 1/11/2014
var total_applied_discount = 0,
    total_serv_discount=0,
    total_pf_discount=0,
    total_msc_discount=0,
    final_phic=null,
    final_discount=null,
    final_net_amount=null;

var MDC = 144;
var OUT_PATIENT = 2;

var isFinalBill = false,
    isInfirmaryOrDependent = '',//added by Nick 4/8/2014
    isComputing = false,
    accommodations,//added by Nick 05-27-2014
    tempTotalSelectedOperation = 0;//added by Nick 02-06-2015, used at addSelectedOP(...) : void

var tmpOPDetails = new Array();
var cpsDetails = new Object();

// added by Robert, 04/21/2015
var KSMBHY = '11', LM = '6', SC = '10';
// end add by Robert
var currentRVU=0;//added by Kenneth 05-12-2016
var currentPrice=0;//added by Kenneth 05-12-2016
var disablePrompt = 0; 
function toDate(epoch, format, locale) {
    var date = new Date(epoch),
        format = format || 'dd/mm/YY',
        locale = locale || 'en'
        dow = {};

    dow.en = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday'
    ];

    var hour12format = (date.getHours() > 12 ? 
                                ((date.getHours()-12) < 10 ? ("0"+(date.getHours()-12)) : (date.getHours()-12)) : 
                                (date.getHours() == 0 ? 12 : (date.getHours() < 10 ? ("0"+date.getHours()) : date.getHours()))
                        );

    var formatted = format
        .replace('D', dow[locale][date.getDay()])
        .replace('dd', ("0" + date.getDate()).slice(-2))
        .replace('mm', ("0" + (date.getMonth() + 1)).slice(-2))
        .replace('M', date.toLocaleString('default', { month: 'short' }))
        .replace('yyyy', date.getFullYear())
        .replace('yy', (''+date.getFullYear()).slice(-2))
        .replace('hh', ("0" + date.getHours()).slice(-2))
        .replace('h', hour12format)
        .replace('mn', ("0" + date.getMinutes()).slice(-2))
        .replace('A', (date.getHours() > 11 ? "PM" : "AM"));

    return formatted;
}

function setIcdIcp(obj){
    _icdIcp = obj;
}

function getPreviousPayment(prevpayment){
    $('bdeposit').innerHTML = prevpayment;
    deposit = prevpayment.replace(/,/g,'');
}

function jQueryPrompt(title,message,onYes,onNo){
    $j('<div></div>')
        .html('<span><strong>'+message+'</strong><span>')
        .dialog({
            title: '<b style="color:#FF0000">'+title+'</b>',
            modal: true,
            position: 'top',
            buttons: {
                Yes: onYes,
                No: onNo
            }
        });
}

function unsetFinalBill(){
    var elem = $('bill_status');
    var check_elem = $('chkHearingTest');
        isFinalBill = true;
        if((check_elem!= null)){
            jQuery('#chkHearingTest').prop('disabled', false);
            jQuery('#lblHearingTest').prop('disabled', false);
        }

        $('btnEditMemCat').style.display = '';
        jQuery('#death_date').prop('disabled', false);

        // elem.style.visibility = "";
        // elem.innerHTML = "[FINAL BILLING]";
        jQuery('#isFinalBill').checked = false;

        jQuery('#btnPrevPack').prop('disabled', false);
        $('btnSave').style.display = "";
        jQuery("#chkboxrow").show();

        jQuery('#btnaccommodation').prop('Disabled', false);
        jQuery('#btnaddmisc_srvc').prop('Disabled', false);
        jQuery('#btnmedsandsupplies').prop('disabled', false);
        jQuery('#btnOPaccommodation').prop('disabled', false);
        jQuery('#btnaddmisc_chrg').prop('disabled', false);
        jQuery('#btnadddoctors').prop('disabled', false);
        jQuery('#btnadd_discount').prop('disabled', false);
        jQuery('#first_rate').prop('disabled', false);
        jQuery('#second_rate').prop('disabled', false);
        jQuery('#chkHearingTest').prop('disabled', false);
        jQuery('#first_multiplier').prop('disabled', false);
        jQuery('#second_multiplier').prop('disabled', false);

        //added by Nick, 2/24/2014
        jQuery('#isdied').prop('disabled', false);
        jQuery('#btnOutMedsXLO').prop('disabled', false);
        jQuery('#confineTypeOption').prop('disabled', false);
        jQuery('#caseTypeOption').prop('disabled', false);
        jQuery('#billdate_display').prop('disabled', false);
        //end Nick

        jQuery('#opd_area').prop('disabled', false); // added by: syboy 08/23/2015
        jQuery('#btnCF2Part3').hide();
        jQuery('#btnSave').show();
        jQuery('#btnDelete').hide();
        jQuery("#chkboxrow").show();
        // jQuery(".imgdelete").remove();
        jQuery("#btnAuditTrail").show();
        jQuery(".imgdelete").show();
        jQuery("#overwritelimitbtn").removeClass("ui-state-disabled");

        //added by art 02/21/2015
        var a = ["isdied", "death_date", "confineTypeOption", "caseTypeOption",
            "first_rate", "second_rate", "billdate_display", "overwritelimitbtn",
            "special_procedure_details :input", "lblHearingTest :input", "opd_area", "btnaccommodation",
            "btnaddmisc_srvc", "btnmedsandsupplies", "btnOPaccommodation", "btnaddmisc_chrg",
            "btnadddoctors", "btnadd_discount", "chkHearingTest"];

        a.forEach(function (entry){
            var elem = '#' + entry;
            if ($j(elem).length > 0) {
                $j(elem).prop('disabled', false);
            }
        });

        var bill_nr = $('bill_nr').value;

        $('isFinalBill').checked = false;

        if(bill_nr != ''){
            $('bill_status').innerHTML = "[NOT YET FINAL]";
        }else{
            $('bill_status').innerHTML = "";
        }
        
        return false;
}

function billWarningPrompt(title,message){
    var uncheckFinal = 0;
    var less_than_encdt = $j('#less_than_encdt').val();
    var newline = '';

    if(message != '')
        newline = '<br>';
    //added by Earl Galope 02/08/2018
    if(!$('isdied').checked){
        $j('#is24HrsPrompt').val(message)
    }
    if($('isdied').checked==true && $j('#isDeath24').val()=='1' && $('isFinalBill').checked==true && message.indexOf('24') <= 0){
        message+=newline+'Confinement is below 24 hours.';
    }
    //end

    if($j("#encounter_type").val() == 2 && $j("#consulting_dept_nr").val() == MDC)
        disablePrompt = 1;

    if($j("#is_exhausted").val() == 1 && disablePrompt == 0){
        message+=newline+'This patient\'s PHIC has been exhausted and cannot be billed.';
        uncheckFinal = 1;   
    }

    if(less_than_encdt == 1){
        if(message != '')
            newline = '<br>';
        
        message+=newline+'Bill Date is earlier than Case/Admission Date.';
        uncheckFinal = 1;
    }

    $j('#warning-dialog-message').html(message);
    $j('#warning-dialog-title').html(title);
    $j('#warning-dialog').dialog('open');

    var bill_nr = $j('#bill_nr').val();

    if(($('isFinalBill').checked && message.indexOf('diagnosis') >= 0) || (uncheckFinal == 1) || (message.indexOf('Accommodation') >= 0)){
        $('isFinalBill').checked = false;
         //added by Earl Galope 02/08/2018
        $('isFinalBillToggled').value='0';
        //end
        if(bill_nr != '') {
            $('bill_status').innerHTML = "[NOT YET FINAL]";
        }
        else {
            $('bill_status').style.display = "none";
        }
    }

}
function PromptDiagMsg(title,message){
    $j('#prompt-dialog-message').html(message);
    $j('#prompt-dialog-title').html(title);
    $j('#prompt-dialog').dialog('open');
}
function closeActiveDialog(){
    $j(this).closest('.ui-dialog').dialog('close'); 
}

function billValidationPrompt(title,message){
    prompt(title,message,function(){
        saveBill();
        $j(this).dialog("close");
    },function(){
        $j(this).dialog("close");
    });
}

function saveBill() {
    calculateDetails1();
    calculateDetails2();
    //added by Nick 05-27-2014
    var bill_nr = $j('#bill_nr').val();
    if (isComputing) {
        alert('Still computing please wait ...')
        return;
    }

    isphic = ($j('#phic').val().toUpperCase() == "NONE") ? false : true;
    phic_nr = $j('#phic').val();
    phic_nr = phic_nr.replace(/-/gi,"");
    isTemp = !$j.isNumeric(phic_nr);

    var check = '';
    var data = new Object();
    var details = new Array();
    var fields = $j("#body_hsListDetails :input, #body_mdListDetails :input").serializeArray();

    //added by Nick, 1/12/2014
    totalHCIDiscount = $('hiDiscount').innerHTML.replace(/,/g, '');
    totalPFDiscount = $('pfDiscount').innerHTML.replace(/,/g, '');
    totalHealthInsuranceHF = $('hiHIC').innerHTML.replace(/,/g, '');
    totalHealthInsurancePF = $('pfHC').innerHTML.replace(/,/g, '');
    totalNet = $('netamnt').innerHTML.replace(/,/g, '');
    //end nick

    data.encounter = $j('#encounter_nr').val();
    data.isFinal = $('isFinalBill').checked;
    data.billdate = $j('#billdate').val();
    data.save_total_acc_charge = $j('#save_total_acc_charge').val();
    data.save_total_med_charge = $j('#save_total_med_charge').val();
    data.save_total_srv_charge = $j('#save_total_srv_charge').val();
    data.save_total_ops_charge = $j('#save_total_ops_charge').val();
    data.save_total_msc_charge = $j('#save_total_msc_charge').val();
    data.save_total_doc_charge = $j('#save_total_doc_charge').val();
    data.save_total_prevpayment = $j('#save_total_prevpayment').val();
    data.isPhilHealth = isphic ? 1 : 0;
    data.isPaywardSettlement = ($('IsPaywardSettlement').checked) ? 1 : 0; // Added by Gervie 03/31/2016

    if (isphic) {
        data.first_rate = $j('#first_rate_amount').html().replace(/,/g, '');
        data.second_rate = $j('#second_rate_amount').html().replace(/,/g, '');
        data.first_rate_code = firstratecode;
        data.second_rate_code = (($j('#second_rate').val() != '0') ? secondratecode : '');
        if (data.first_rate <= 0 && $('isFinalBill').checked ) {
            alert("No case rate selected");
            $('isFinalBill').checked = false;
            if(bill_nr == '') {
                $('bill_status').innerHTML = "";
                // alert(bill_nr);
            }
            else {
                $('bill_status').innerHTML = "[NOT YET FINAL]";
            }
            return;
           
        }else if(data.first_rate != 0){
            //do nothing
        }else{
            data.first_rate = null;
            data.second_rate = null;
            data.first_rate_code = null;
            data.second_rate_code = null;
        }
    }
    else {
        data.first_rate = null;
        data.second_rate = null;
        data.first_rate_code = null;
        data.second_rate_code = null;
    }

    data.first_hci = $j("#first_rate option:selected").attr('value_hf');
    data.first_pf = $j("#first_rate option:selected").attr('value_pf');
    data.second_hci = $j("#second_rate option:selected").attr('value_hf');
    data.second_pf = $j("#second_rate option:selected").attr('value_pf');

    // added by: syboy 10/11/2015
    data.laterality_first = $j("#first_rate option:selected").attr('laterality');
    data.laterality_second = $j("#second_rate option:selected").attr('laterality');
    //  ended

    //Added by Gervie 06/02/2016
    data.first_multiplier = $j("#first_multiplier").val();
    data.second_multiplier = $j("#second_multiplier").val();

    data.d1coverage = D1_coverage1+D1_coverage2;
    data.d2coverage = D2_coverage1+D2_coverage2;
    data.d3coverage = D3_coverage1+D3_coverage2;
    data.d4coverage = D4_coverage1+D4_coverage2;
    // alert(data.d1coverage);
    // alert(data.d2coverage);
    // alert(data.d3coverage);
    // alert(data.d4coverage);
    // return 0;
    /*    data.D1_nr = D1_nr;
     data.D2_nr = D2_nr;
     data.D3_nr = D3_nr;
     data.D4_nr = D4_nr;
     data.D1_chrg = D1_chrg;
     data.D2_chrg = D2_chrg;
     data.D3_chrg = D3_chrg;
     data.D4_chrg = D4_chrg;*/
    var d1_disc=0,d2_disc=0,d3_disc=0,d4_disc=0;
    if(D1_discount1>D1_discount2) d1_disc=D1_discount1;
    else d1_disc=D1_discount2;
    if(D2_discount1>D2_discount2) d2_disc=D2_discount1;
    else d2_disc=D2_discount2;
    if(D3_discount1>D3_discount2) d3_disc=D3_discount1;
    else d3_disc=D3_discount2;
    if(D4_discount1>D4_discount2) d4_disc=D4_discount1;
    else d4_disc=D4_discount2;
    // alert(D1_discount1);
    // alert(D2_discount1);
    // alert(D3_discount1);
    // alert(D4_discount1);
    // alert(D1_discount2);
    // alert(D2_discount2);
    // alert(D3_discount2);
    // alert(D4_discount2);
    // return false;
    data.D1_discount = d1_disc;
    data.D2_discount = d2_disc;
    data.D3_discount = d3_disc;
    data.D4_discount = d4_disc;
    //data.msDiscount = $j('#msDiscount').html().replace(',','');
    data.hcidiscount = totalHCIDiscount;
    data.pfdiscount = totalPFDiscount;
    data.hcicoverage = totalHealthInsuranceHF;
    data.pfcoverage = totalHealthInsurancePF;
    data.billdatefrom = $j('#admission_date').val();
    data.pid = $j('#pid').val();
    data.disc_id = $j('#save_discountid').val();
    data.disc = $j('#save_discount').val();
    data.disc_amnt = $j('#save_discount_amnt').val();

    data.disc_amnt_credit_collection = $j('#save_discount_credit_collection').val();//added by Nick 8-8-2015
    data.disc_id_credit_collection = $j('#save_discountid_credit_collection').val();//added by Nick 8-8-2015

    data.excess = totalNet;
    data.ndays = $j('#savethis').html();//addedby art
    data.rem_days = $j('#remainingDays').val();//added by carriane 06/10/19
    data.actual_rem_days = $j('#actual_rem_days').val(); // Unknown
    data.accommodation_type = ((isPAYWARD()) ? '2' : '1');
    data.mgh_date = ($('isdied').checked) ? $('death_date').value : $('billdate').value;
    data.encounter_type = $j('#opd_area').val(); // added by janken 11/14/2014

    data.isInfirmaryOrDependent = isInfirmaryOrDependent;
    data.bill_time_started = $j('#bill_time_started').val(); //added by Gervie 11/24/2015

    $j.each(fields, function (i, field) {
        details[field.name] = field.value;
    });

    xajax_clearCaseRateMultiplier($j('#encounter_nr').val());

    if ($('isFinalBill').checked) {
        check = '1';
        xajax_toggleMGH(data, 1);

        if ((parseFloat(totalHealthInsurancePF) == 0) && (isphic)) {
            var answer = confirm("Are you sure to save this billing WITHOUT DOCTOR\'s COVERAGE?");
            if (answer){
                showLoading();
                xajax_saveThisBilling(data, check, details, process_type);
            }
            else {
                $('isFinalBill').checked = false;
                $j('#bill_status').html('');
                return false;
            }

        } else {
            showLoading();
            xajax_saveThisBilling(data, check, details, process_type);
        }
        disableUI();
    } else {
        showLoading();
        check = '0';
        xajax_toggleMGH(data, 0);
        xajax_saveThisBilling(data, check, details, process_type);
    }

}//end saveBill function

function preset() {

    var enc = $j('#encounter_nr').val();
    var pid = $j('#pid').val();
    var bill_nr = $j('#bill_nr').val();

    $j("tbody").find(".toggle").hide();
    $j('thead.togglehdr').each(function (idx, obj) {
        var obj = $j(obj);
        obj.find('th.toggleth').click(function () {
            obj.parent().children('tbody.toggle').toggle();
            obj.find(".arrow").toggleClass("up");
        });
    });

    //added by Nick 05-15-2014
    $j('#btnEditMemCat').click(function () {
        $j('#dlgMemCat')
            .dialog({
                title: "Select new Member Category",
                modal: true,
                position: "top",
                buttons: {
                    Save: function () {
                        enc = $j('#encounter_nr').val();
                        memcat = $j('#optMemCat').val();
                        xajax_updateMemCat(enc, memcat);
                    },
                    Cancel: function () {
                        $j(this).dialog("close");
                    }
                }
            });
    });

    /* added by Nick 6-9-2015 */
    $j('<div id="warning-dialog"></div>')
        .html('<table><tr><td style="color:#FFF;font-size:14.4em;background-color: red;text-align: center;"><b id="warning-dialog-title"></b></td></tr><tr><td><span style="text-align:center;"><strong style="font-size:4.27em;margin:auto;" id="warning-dialog-message"></strong></span></td></tr></table>')
        .dialog({
            autoOpen:false,
            title: '',
            modal: true,
            position: 'top',
            buttons: {
                Ok: function(){$j(this).dialog('close')}
            },
            width: 'auto',
            height: 'auto',
            open:function(event, ui){
                $j(this).parent().addClass('jquery-ui-red');
            },
        });

            $j('<div id="prompt-dialog"></div>')
        .html('<table><tr><td style="color:#FFF;font-size:5.4em;background-color: red;text-align: center;"><b id="prompt-dialog-title"></b></td></tr><tr><td><span style="text-align:center;"><strong style="font-size:4.27em;margin:auto;" id="prompt-dialog-message"></strong></span></td></tr></table>')
        .dialog({
            dialogClass: "no-close",
            autoOpen:false,
            title: '',
            modal: true,
            position: 'top',
            buttons: {
                Ok: function(){$j(this).dialog('close')
                 $('isFinalBill').checked = false;
                  $j('#bill_status').html('');
                    return false;
            }

            },
            width: 'auto',
            height: 'auto',
            open:function(event, ui){
                $j(this).parent().addClass('jquery-ui-red');
            },
        });

    $j("#IsPaywardSettlement").on("change",function(){
        $('isFinalBill').checked = false;
    });

    $j("#isFinalBill").on("change",function(){
        $('IsPaywardSettlement').checked = false;
    });


    $j('#categ_col').innerHTML = '<a title="Edit" href="#"></a>&nbsp;MEMBERSHIP CATEGORY:&nbsp;&nbsp;&nbsp;<span id="mcategdesc" name="mcategdesc"></span>';
    $j("#btnSave").button({text: true, icons: {primary: "ui-icon-disk"}});
    $j("#btnPrevPack").button({text: true, icons: {primary: "ui-icon-suitcase"}});
    $j("#btnPrint").button({text: true, icons: {primary: "ui-icon-print"}});
    $j("#btnPrint2").button({text: true, icons: {primary: "ui-icon-print"}}).css("background", "#F00");//new soa - for beta testing
    $j("#btnPrint2ForPatientCopy").button({text: true, icons: {primary: "ui-icon-print"}});//new soa - for beta testing
    $j("#PrintSelect").button({text: true, icons: {primary: "ui-icon-print"}});//new soa - for beta testing
    $j("#btnInsurance").button({text: true, icons: {primary: " ui-icon-plusthick"}});
    $j("#btnInsurance2").button({text: true, icons: {primary: " ui-icon-plusthick"}});
    $j("#btnAuditTrail").button({text: true, icons: {primary: " ui-icon-folder-open"}}); // added by Gervie 12/23/2015
    $j("#btnDiagnosis").button({text: true, icons: {primary: " ui-icon-lightbulb"}});
    $j("#btnDelete").button({text: true, icons: {primary: "ui-icon-trash"}});
    $j("#btnNew").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnaccommodation").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnOPaccommodation").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnmedsandsupplies").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnunpaid_cps").button({text: true, icons: {primary: " ui-icon-circle-plus"}}); // added by gervie 07/21/2015
    $j("#btnaddmisc_ops").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnaddmisc_srvc").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnaddmisc_chrg").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnadd_discount").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnCSFp2Blank").button({text: true, icons: {primary: " ui-icon-print"}}); //added by Jeff Ponteras 03-26-2018
    $j("#btnCSFp2").button({text: true, icons: {primary: " ui-icon-print"}}); //added by Jasper Ian Q. Matunog 11/21/2014
    $j("#btnadddoctors").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnCF2Part3").button({text: true, icons: {primary: " ui-icon-print"}});
    $j("#btnOutMedsXLO").button({text: true, icons: {primary: " ui-icon-circle-plus"}});
    $j("#btnRecalc").button({text: true, icons: {primary: " ui-icon-gear"}});
    $j("#overwritelimitbtn").button({text: true, icons: {primary: " ui-icon-gear"}});
    $j("#notesbtn").button({text: true, icons: {primary: " ui-icon-document"}});
    $j("#trailbtn").button({text: true, icons: {primary: " ui-icon-folder-open"}});

    var pageSelEnc = "../../modules/billing/billing-select-enc.php?bill_type=phic";
    dialogSelEnc = $j('<div></div>')
        .html('<iframe style="border: 0px; " src="' + pageSelEnc + '" width="100%" height=400px></iframe>')
        .dialog({
            autoOpen: false,
            modal: true,
            show: 'fade',
            hide: 'fade',
            height: 'auto',
            width: '800',
            title: 'Select Registered Person',
            position: 'top',
        });

    if (bill_nr) {
        populateBill();
    } else {
        $j('#select-enc').click(function () {
            dialogSelEnc.dialog('open');
            return false;
        });
    }


    $j('#btnSave').click(function () {

        /*if($j('#remarks').attr('remarks_id')==INSURANCE_TEMP && $('isFinalBill').checked){
            alert("Temporary insurance is not allowed for final billing");
            return false;
        }*/

        if($j('#phic').val().toUpperCase() == 'TEMP' && $('isFinalBill').checked) {
            alert("Temporary insurance is not allowed for final billing");
            return false;
        }
        
        isphic = ($j('#phic').val().toUpperCase() == "NONE") ? false : true;

        var phic_nr = $j('#phic').val();
        phic_nr = phic_nr.replace(/-/gi, "");
        // edited by: syboy 08/23/2015
        if ($j('#ptype').val() == 2) {
             if ($j('#opd_area').val() == 0) {
                alert('Please select opd area!');
            }else {
                if (isphic && !$j.isNumeric(phic_nr)) {
                    if(phic_nr.toUpperCase() == 'TEMP' && $('isFinalBill').checked)
                    {
                        alert("Temporary PHIC number is not allowed for final billing");
                    }
                    else 
                    {
                        jQueryPrompt('WARNING','PHIC No is temporary/non-numeric, do you want to proceed?',function () {
                            saveBill();
                            $j(this).dialog("close");
                        },function () {
                            $j(this).dialog("close");
                        });
                    }
                }else{
                    saveBill();
                }
            }

        }else{
            if (isphic && !$j.isNumeric(phic_nr)) {
                if(phic_nr.toUpperCase() == 'TEMP' && $('isFinalBill').checked)
                {
                    alert("Temporary PHIC number is not allowed for final billing");
                }
                else 
                {
                    jQueryPrompt('WARNING','PHIC No is temporary/non-numeric, do you want to proceed?',function () {
                        saveBill();
                        $j(this).dialog("close");
                    },function () {
                        $j(this).dialog("close");
                    });
                }
            }else{
                saveBill();
            }
        }

        // added by Christian 01-27-20
        var saveFrom = 'btnSave';
        saveDoctorCoverage(saveFrom);
        // end Christian 01-27-20
    
        // end

    });//end btnSave

    $j('#btnInsurance').click(function () {
        var enc = $j('#encounter_nr').val();
        var pid = $j('#pid').val();
        var seg_URL_APPEND = $j('#seg_URL_APPEND').val();
        var admission_date = $j('#admission_date').val();
        alert(admission_date);
        var pageInsurance = '../../modules/registration_admission/seg_insurance.php' + seg_URL_APPEND + '&encounter_nr=' + enc +
            '&update=1&target=search&popUp=1&frombilling=1&bill_type=phic&pid=' + pid;
        dialogInsurance = $j('<div></div>')
        .html('<iframe style="border: 0px; " src="' + pageInsurance + '" width="100%" height=450px></iframe>')
            .dialog({
                autoOpen: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 580,
                width: '65%',
                title: 'Insurance',
                position: 'top',
                close: function () {
                    populateBill();
                },
            });
        return false;
    });

    $j('#btnInsurance2').click(function () {
        var enc = $j('#encounter_nr').val();
        var pid = $j('#pid').val();
        var bill_nr = window.parent.$j("#bill_nr").val();
        var seg_URL_APPEND = $j('#seg_URL_APPEND').val();
        var admission_date = $j('#admission_date').val();
        var pageInsurance = '../../modules/billing_new/seg-encounter-insurance.php' + seg_URL_APPEND + '&encounter_nr=' + enc +
            '&update=1&target=search&popUp=1&frombilling=1&bill_type=phic&pid=' + pid +'&admission_date=' + admission_date;
        dialogInsurance = $j('<div></div>')
            .html('<iframe style="border: none;" src="' + pageInsurance + '" width="100%" height="100%"></iframe>')
            .dialog({
                autoOpen: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 580,
                width: '85%',
                title: 'Insurance',
                position: 'top',
                close: function () {
                    populateBill();
                    if(bill_nr != '') {
                        window.parent.$('bill_status').innerHTML = "[NOT YET FINAL]";
                    }
                    else {
                        window.parent.$('bill_status').style.display = "none";
                    }

                    window.parent.$('isFinalBill').checked = false;
                }
            });
        return false;
    });

    // added by Gervie 12/23/2015
    $j('#btnAuditTrail').click(function () {
        var enc = $j('#encounter_nr').val();
        var pid = $j('#pid').val();
        var seg_URL_APPEND = $j('#seg_URL_APPEND').val();
        var pageInsurance = '../../modules/billing_new/seg-soa-audit-trail.php' + seg_URL_APPEND + '&encounter_nr=' + enc +
            '&update=1&target=search&popUp=1&frombilling=1';
        dialogInsurance = $j('<div></div>')
            .html('<iframe style="border: none;" src="' + pageInsurance + '" width="100%" height="100%"></iframe>')
            .dialog({
                autoOpen: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 600,
                width: '85%',
                title: 'Audit Trail',
                position: 'top',
                close: function () {
                    populateBill();
                }
            });
        return false;
    });
//-Added by julz 01-09-2017
 $j('#trailbtn').click(function () {
      var enc = $j('#encounter_nr').val();
        var pid = $j('#pid').val();
        var seg_URL_APPEND = $j('#seg_URL_APPEND').val();
        var pageInsurance = '../../modules/billing_new/seg-notes-audit-trail.php' + seg_URL_APPEND + '&pid=' + pid +
            '&update=1&target=search&popUp=1&frombilling=1';
        dialogInsurance = $j('<div></div>')
            .html('<iframe style="border: none;" src="' + pageInsurance + '" width="100%" height="100%"></iframe>')
            .dialog({
                autoOpen: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 600,
                width: '85%',
                title: 'Note Trail',
                position: 'top',
                close: function () {
                    populateBill();
                }
            });
        return false;
    });


//----end

    var pageMiscSrvSupp = "billing-misc-services-tray-new.php";
    var htmlXLSO = '<iframe style="border: 0px; " src="' + pageMiscSrvSupp + '" width="100%" height=400px></iframe>';
    var dialogMiscSrvSupp = $j('<div></div>')
        .dialog({
            autoOpen: false,
            modal: true,
            height: "auto",
            width: "80%",
            show: 'fade',
            hide: 'fade',
            resizable: false,
            draggable: false,
            title: "Misc Services and Supplies",
            position: "top",
        });

    $j('#btnaddmisc_srvc').click(function () {
        dialogMiscSrvSupp.html(htmlXLSO);
        dialogMiscSrvSupp.dialog('open');
        return false;
    });

    $j('#dialogMiscServicesDelConfirm').dialog({
        autoOpen: false,
        modal: true,
        height: "auto",
        show: 'fade',
        hide: 'fade',
        width: "60%",
        title: "Delete miscellaneous service",
        position: "top",
        buttons: {
            "Yes": function () {
                jsDeleteMiscService();
                $j(this).dialog("close");
            },
            "No": function () {
                $j(this).dialog("close");
            }
        }
    });

    /*---------end Misc Services Dialog-------------*/

    /*---------Unpaid CPS Dialog--------------------*/
    $j('#btnunpaid_cps').click(function(){
        $j('#dialogUnpaidCps').dialog({
            autoOpen: true,
            modal: true,
            show: 'fade',
            fade: 'fade',
            height: 450,
            width: 900,
            resizable: false,
            draggable: false,
            title: "Convert Unpaid CPS Transaction",
            position: "top",
            buttons: {
                "Convert to Charge": function () {

                    var container = $j('#cpsInputs').serialize();
                    //console.log(container);

                    var ref = document.getElementsByName('del_refno[]');
                    var serv_code = document.getElementsByName('del_servcode[]');
                    var arr_serv = [], arr_refno = [];

                    for (i = 0; i < serv_code.length; i++) {
                        arr_serv.push(serv_code[i].value);
                        arr_refno.push(ref[i].value);
                    }

                    //xajax_deleteUnpaidCps(arr_refno, arr_serv);
                    //xajax_convertUnpaidCps(cpsDetails, container);
                    xajax.call('convertUnpaidCps',{
                        asynchronous:true,
                        parameters:[cpsDetails, container],
                        callback:setCallBack(function(){
                            xajax.call('deleteUnpaidCps',{
                                parameters:[arr_refno, arr_serv],
                                callback:setCallBack(jsRecomputeServices)
                            });
                        })
                    });
                    $j(this).dialog("close");
                    //jsRecomputeServices();
                    //alert('Unpaid CPS item/s are successfully converted to charge.');
                },
                "Cancel": function () {
                    $j(this).dialog("close");
                }
            }
        });
    });
    /*---------end Unpaid CPS Dialog----------------*/

    /*------------ Misc charges  Dialog-------------*/
    var pageMisc = "billing-misc-chrgs-tray-new.php";
    var htmlMisc = '<iframe style="border: 0px; " src="' + pageMisc + '" width="100%" height=400px></iframe>';
    var dialogMisc = $j('<div></div>')
        .dialog({
            autoOpen: false,
            modal: true,
            height: "auto",
            width: "60%",
            show: 'fade',
            hide: 'fade',
            resizable: false,
            draggable: false,
            title: "Add Miscellaneous Charge(s)",
            position: "top",
        });

    $j('#btnaddmisc_chrg').click(function () {
        dialogMisc.html(htmlMisc);
        dialogMisc.dialog('open');
        return false;
    });

    $j('#dialogMiscChargesDelConfirm').dialog({
        autoOpen: false,
        modal: true,
        height: "auto",
        show: 'fade',
        hide: 'fade',
        width: "60%",
        title: "Delete miscellaneous charge",
        position: "top",
        buttons: {
            "Yes": function () {
                jsDeleteMiscCharge();
                $j(this).dialog("close");
            },
            "No": function () {
                $j(this).dialog("close");
            }
        }
    });
    /*---------end Misc charges  Dialog-------------*/

    /*------------ Drugs and Medicnes --------------*/
    var pageMeds = "billing-more-pharmaorder-new.php";
    var htmlMeds = '<iframe style="border: 0px; " src="' + pageMeds + '" width="100%" height=400px></iframe>';
    var dialogMeds = $j('<div></div>')
        .dialog({
            autoOpen: false,
            modal: true,
            show: 'fade',
            hide: 'fade',
            height: "auto",
            width: "80%",
            resizable: false,
            draggable: false,
            title: "Drugs and Meds",
            position: "top",
        });

    $j('#btnmedsandsupplies').click(function () {
        dialogMeds.html(htmlMeds);
        dialogMeds.dialog('open');
        return false;
    });
    $j('#dialogMedicineDelConfirm').dialog({
        autoOpen: false,
        modal: true,
        height: "auto",
        show: 'fade',
        hide: 'fade',
        width: "60%",
        title: "Delete drugs and medicines",
        position: "top",
        buttons: {
            "Yes": function () {
                jsDeleteMed();
                $j(this).dialog("close");
            },
            "No": function () {
                $j(this).dialog("close");
            }
        }
    });
    /*---------end Drugs and Medicnes --------------*/


    var pageDc = "billing-discounts.php";
    var dialogDc = $j('<div></div>')
        .html('<iframe id="discount_frame" style="border: 0px; " src="" width="100%" height=400px></iframe>')
        .dialog({
            autoOpen: false,
            modal: true,
            height: "auto",
            show: 'fade',
            hide: 'fade',
            width: "60%",
            resizable: false,
            draggable: false,
            title: "Discounts",
            position: "top",
            open: function () {
                $j('#discount_frame').attr("src", pageDc);
            },
            close: function () {
                populateBill();
            }
        });

    $j('#btnadd_discount').click(function () {
        dialogDc.dialog('open');
        return false;
    });


    /*--------dialog box for diagnosis and procedures (ICD and ICP)----*/

    $j('#btnDiagnosis').click(function () {
        //Added by Christian 01-23-20
        if (isComputing) {
            alert('Still computing please wait ...')
            return;
        }
        var bill_nr = $j('#bill_nr').val();
        //end Christian 01-23-20
        var pid = $j('#pid').val();
        var encounter_nr = $j('#encounter_nr').val();
        var billDate = $j('#billdate').val();
        var frombilling = 1;
        // added by art 02/21/15
        var finalbill = $j('#bill_status').text() == '[FINAL BILLING]' ? 1 : 0;
        var caserate1 = $j('#first_rate').find('option:selected').attr('id');
        var caserate2 = $j('#second_rate').find('option:selected').attr('id');
        //end art
        encounter_date_start=$j('#admission_date').val();
        encounter_date_end=$j('#billdate_display').val();
        var pageDiagnosis = "billing-diagnosis-procedures.php?pid=" + pid + "&encounter_nr=" + encounter_nr + "&frombilling=" + frombilling + "&billDate=" + billDate + "&caserate1=" + caserate1 + "&caserate2=" + caserate2+ "&finalbill=" + finalbill + "&bill_nr="+ bill_nr;
        var dialogDiagnosis = $j('<div></div>')
            .html('<iframe style="border: 0px; " src="' + pageDiagnosis + '" width="100%" height=400px></iframe>')
            .dialog({
                autoOpen: true,
                modal: true,
                height: "auto",
                width: "90%",
                show: 'fade',
                hide: 'fade',
                resizable: false,
                draggable: false,
                title: "Diagnosis and Procedure",
                position: "top",
                close: function () {
                    $j("#icdCode").empty();
                    xajax_checkInsurance(encounter_nr);
                    populateBill();
                },
            });
    });

    /*-----end---dialog box for diagnosis and procedures (ICD and ICP)---end---*/

    //added by poliam 01/04/2014
    //previous package
    $j('#btnPrevPack').click(function () {
        var pid = $j('#pid').val();
        var encounter_nr = $j('#encounter_nr').val();
        var PagePreviousPackage = "billing-prev-package.php?pid=" + pid + "&enc_nr=" + encounter_nr;
        var dialogPrevPackage = $j('<div></div>').html('<iframe style="border: 0px;" src="' + PagePreviousPackage + '" width="100%" height=400px></iframe>').dialog({
            autoOpen: true,
            modal: true,
            height: "auto",
            width: "60%",
            resizable: false,
            draggable: false,
            title: "Previous Packages",
            position: "top",
        });


    });
    //ended by poliam 01/04/2014
    /*-------------Accommodation Dialog-------------*/
    resetAccommDialogForm(); //on page load reset accommodation dialog form
    $j('#btnaccommodation').click(function () {
        $j("#dialogAcc").dialog({
            autoOpen: true,
            modal: true,
            height: "auto",
            show: 'fade',
            hide: 'fade',
            width: "60%",
            resizable: false,
            draggable: false,
            // show: "blind",
            // hide: "explode",
            title: "More Accomodation Charges",
            position: "top",
            buttons: {
                "Save": function () {
                    if (isValidAccomForm()) {
                        jsSaveAccommodation();
                        $j(this).dialog("close");
                    }
                },
                "Cancel": function () {
                    $j(this).dialog("close");
                }
            },
            close: function () {
                resetAccommDialogForm();
            }
        });

        return false;
    });

    $j('#occupydateto').datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true
    });

    $j('#occupydatefrom').datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true
    });

    $j('#occupydatefrom').bind('keypress keydown', function () {
        return false;
    });

    $j('#occupydateto').bind('keypress keydown', function () {
        return false;
    });

    $j('#dialogAccDelConfirm').dialog({
        autoOpen: false,
        modal: true,
        height: "auto",
        width: "60%",
        show: 'fade',
        hide: 'fade',
        resizable: false,
        draggable: false,
        title: "Delete accommodation",
        position: "top",
        buttons: {
            "Yes": function () {
                jsDeleteAccommodation();
                $j(this).dialog("close");
            },
            "No": function () {
                $j(this).dialog("close");
            }
        }
    });
    /*-----------end Accommodation Dialog-----------*/

    /*----------Operating Room Accomodation Charges Dialog--------------*/
    $j('#btnOPaccommodation').click(function () {
        showOperatingRoomAcc();
        $j("#dialogOR").dialog({
            autoOpen: true,
            modal: true,
            height: "auto",
            width: "auto",
            resizable: false,
            draggable: false,
            show: 'fade',
            hide: "fade",
            title: "Operating Room Accomodation Charges",
            position: "top",
            buttons: {
                "Save": function () {
                    saveORAccommodation();
                    // $j( this ).dialog( "close" );
                },
                "Cancel": function () {
                    $j(this).dialog("close");
                }
            },
            close: function () {
                clearORACFields();
            }
        });
        return false;
    });
    /*----end----Operating Room Accomodation Charges Dialog------end-----*/

    /*---------------Procedure List Dialog Box--------------------------*/
    $j('#ops_selected').click(function () {
        clearORACFields();
        initProcedureList();
        $j("#dialogProcedureList").dialog({
            autoOpen: true,
            modal: true,
            height: "auto",
            width: "80%",
            resizable: false,
            draggable: false,
            show: 'fade',
            hide: "fade",
            title: "Procedures with Accomodation",
            position: "top",
            close: function (event, ui) {
                calcTotRVU();
            },
        });
        return false;
    });
    /*-----end-------Procedure List Dialog Box----------end-------------*/

    /*------------Add Doctors Dialog Box-------------------------------*/
    $j('#btnadddoctors').click(function () {
        var enc_number=$j('#encounter_nr').val(); //added by carriane 7/10/17
        var opd_area_type=$j('#opd_area option:selected').attr('data-atype'); //added by carriane 8/24/17; updated by carriane 7/8/19
        
        clearProfDialog();
        xajax_setDoctors(1, 0, 0, enc_number, opd_area_type); //updated by carriane 7/10/17, 8/24/17
        xajax_setRoleArea(1);
        xajax_setOptionRoleLevel();
        $j("#dialogAddDoc").dialog({
            autoOpen: true,
            modal: true,
            height: "auto",
            width: "520px",
            resizable: false,
            draggable: false,
            show: 'fade',
            hide: "fade",
            title: "Add Doctor",
            position: "top",
            close: function (event, ui) {
                $j("#hasAnes").hide();
                currentRVU=0;
                currentPrice=0;
            },
            buttons: {
                "Save": function () {
                    addDoctor();
                    $j("#hasAnes").hide();
                    currentRVU=0;
                    currentPrice=0;
                },
                "Cancel": function () {
                    $j("#hasAnes").hide();
                    $j(this).dialog("close");
                    currentRVU=0;
                    currentPrice=0;
                }
            }
        });
        return false;
    });

    $j('#ops4pf_selected').click(function () {
        currentRVU=0;
        currentPrice=0;
        initProcedureList();
        tmpRVU = 0;
        drCharge = 0;
        tempTotalSelectedOperation = 0;
        tmpOPDetails = [];
        $j("#dialogProcedureList").dialog({
            autoOpen: true,
            modal: true,
            height: "auto",
            width: "60%",
            resizable: false,
            draggable: false,
            show: 'fade',
            hide: "fade",
            title: "Select Procedures done by Doctor",
            position: "top",
            close: function (event, ui) {
                addDrCharge();
            }
        });
        return false;
    });
    //added by art 01/28/2014
    $j('#charge').keyup(function (e) {
        if (e.keyCode == 13) {
            addDoctor();
        }
        ;
    });
    //end art
    /*--------end--------Add Doctors Dialog Box------------end----------------*/

    $j('#imgBtnDelAcc').click(function () {
        $j('#btnaccommodation').dialog('open');
    });

    $j('#opwardlist').on('change', function () {
        var val = $j("#opwardlist").val();
        jsOpAccChrgOptionsChange('opwardlist', val);
    });

    $j('#orlist').on('change', function () {
        var val = $j("#orlist").val();
        jsOpAccChrgOptionsChange('orlist', val);
    });


    $j('#first_rate').on('click', function () {
        changeCase('1');
    });

    $j('#second_rate').on('click', function () {
        changeCase('2');
    });

    var onbilldate = false; // to double check if bildate datepicker was clicked
    var clickednow; // to identify if "now" button was clicked

    $j('#billdate_display').datetimepicker({
        dateFormat: 'M d, yy',
        timeFormat: 'hh:mm TT',
        onSelect: function (selectedDate) {
            /* Added this part to fetch server time upon clicking "now" button on datepickers */
            onbilldate = true;
            clickednow = false;
            $j(document).on('click',"button.ui-datepicker-current", function() {
                xajax_getCurrentDateTime();
                
                setTimeout(function(){
                    var current_dt = $j('#current_date_time').val();

                    $j.datepicker._curInst.input.datepicker('setDate', new Date(current_dt));

                    /* Currently, there are 2 datepickers (billdate & deathdate), and upon clicking "now", their hidden value needs to be updated also */

                    if(onbilldate == true){
                        console.log('onbilldate :'+onbilldate);
                        $j('#billdate').val(toDate(new Date(current_dt), "yyyy-mm-dd hh:mn") + ':00');
                        clickednow = true;
                    }else{
                        $j('#deathdate').val(toDate(new Date(selectedDate), "yyyy-mm-dd hh:mn") + ':00');
                    }
                }, 1000);
            });
            /* end here by carriane 05-20-2020 */

            if(clickednow == false){
                xajax_getCurrentDateTime();
                $j('#billdate').val(toDate(new Date(selectedDate), "yyyy-mm-dd hh:mn") + ':00');
            }
        },
        onClose: function () {
            onbilldate = false;

            var td = $j('#current_date_time').val();
            console.log(td);

            var today = new Date(td);
            console.log(today);

            var now_disp = toDate(new Date(today), "M dd, yyyy h:mn A");

            var billdate = $j('#billdate').val();
            var billdatetoTime = new Date(billdate);

            var admission_dt = $j('#admission_dte').val();
            var admdtTime = new Date(admission_dt);
            var adm_dte_disp = toDate(new Date(admission_dt), "M dd, yyyy h:mn A");

            console.log(billdate);

            if(billdatetoTime.getTime() > today.getTime() || billdatetoTime.getTime() < admdtTime.getTime()){
                if(billdatetoTime.getTime() > today.getTime()){
                    alert('Bill date and time should not be later than the current date and time ('+now_disp+')');
                }else{
                    alert('Bill date and time should not be earlier than the admission date and time ('+adm_dte_disp+')');
                }

                $j('#billdate').val(toDate(new Date(today), "yyyy-mm-dd hh:mn") + ':00');
                $j('#billdate_display').val(toDate(new Date(today), "M dd, yyyy h:mn A"));
            }else{
                Date.prototype.yyyymmdd = function() {
                   var yyyy = this.getFullYear().toString();
                   var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
                   var dd  = this.getDate().toString();
                   return yyyy +"-"+ (mm[1]?mm:"0"+mm[0]) +"-"+ (dd[1]?dd:"0"+dd[0]); // padding
                  };
                var farst = $j('#admission_date').val();
                var sekand = $j('#billdate_display').val();
                sekand = new Date([sekand.slice(0, 18), ' ', sekand.slice(18)].join(''));
                farst = new Date([farst.slice(0, 18), ' ', farst.slice(18)].join(''));
                // var enc_dt_start = new Date([parent.encounter_date_start.slice(0, 18), ' ', parent.encounter_date_start.slice(18)].join(''));
                // var enc_dt_end = new Date([parent.encounter_date_end.slice(0, 18), ' ', parent.encounter_date_end.slice(18)].join(''));
                // // var enc_dt_start2 = new Date(farst.slice(0, 18), ' ', farst.slice(18)].join(''));
                // // var enc_dt_end2 = new Date(sekand.slice(0, 18), ' ', sekand.slice(18)].join(''));
                var enc_number=$j('#encounter_nr').val();
                farst=farst.yyyymmdd();
                sekand=sekand.yyyymmdd();
                xajax_identifyProcedureConflict(enc_number,farst,sekand);
                populateBill();
            }
        },
    });

    $j('#death_date').datetimepicker({
        dateFormat: 'M d, yy',
        timeFormat: 'hh:mm tt',
        onSelect: function (selectedDate) {
            console.log('onbilldate :'+onbilldate);
            $j('#deathdate').val(toDate(new Date(selectedDate), "yyyy-mm-dd hh:mn") + ':00');
        },
        onClose: function (selectedDate) {
            Date.prototype.yyyymmdd = function() {
               var yyyy = this.getFullYear().toString();
               var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
               var dd  = this.getDate().toString();
               return yyyy +"-"+ (mm[1]?mm:"0"+mm[0]) +"-"+ (dd[1]?dd:"0"+dd[0]); // padding
              };
            $j('#deathdate').val(toDate(new Date(selectedDate), "yyyy-mm-dd hh:mn") + ':00');
            var enc = $j('#encounter_nr').val();
            var pid = $j('#pid').val();
            var deathdate = $j('#deathdate').val();
            var adm_dte = $j('#admission_date').val();
            // alert(selectedDate);
            adm_dte = new Date([adm_dte.slice(0, 18), ' ', adm_dte.slice(18)].join(''));
            adm_dte=adm_dte.yyyymmdd();

            if (deathdate < adm_dte) {
                alert('Invalid Date');
                populateBill();
            }else{
                xajax_setDeathDate(pid,1, enc, deathdate);
                populateBill(); 
            }
       
        }
    });

    if (enc != '') {
    } else {
        $j('#select-enc').click();
    }

    // updated by Gervie 08/31/2015
    $j('#btnDelete').click(function () {
        var old_billnr = $j('#bill_nr').val();
        var enc_nr = $j('#encounter_nr').val();
        var bill_started = $j('#bill_time_started').val();
        /*var message = "Do you really want to delete this billing?\nClick OK to delete, CANCEL otherwise!";
        var ret_val = false;
        if (old_billnr != "") {
            ret_val = confirm(message);
            if (ret_val == true) {

                xajax_deleteBilling(old_billnr, enc_nr);
                xajax_clearBilling();

            }
        }
        else {
            alert("No billing to delete!");
        }*/
        if(old_billnr != "") {
            $j('#reason-dialog').dialog({
                autoOpen: true,
                modal: true,
                height: 'auto',
                width: '500',
                resizable: false,
                draggable: false,
                show: 'fade',
                hide: 'fade',
                title: 'Delete bill',
                position: 'top',
                buttons: {
                    "Delete": function () {
                        var del_reason = $j('#delete_reason').val();
                        var del_other_reason = $j('#delete_other_reason').val();
                        if(del_reason != ""){
                            xajax_deleteBilling(old_billnr, enc_nr, del_reason, del_other_reason, bill_started);
                            xajax_clearBilling();
                            $j(this).dialog("close");
                        }
                        else{
                            alert("Please enter the reason of deleting this bill.");
                        }
                    },
                    "Cancel": function () {
                        $j("#form-reason")[0].reset();
                        $j(this).dialog("close");
                    }
                }
            });
        }
        else {
            alert("No billing to delete!");
        }
    });

    $j('#btnOutMedsXLO').click(function () {
        var enc = $j('#encounter_nr').val();
        xajax_getOutMedsXLO(enc);
        $j('#dialogOutMedsXLO').dialog({
            autoOpen: true,
            modal: true,
            height: 'auto',
            width: '300',
            resizable: false,
            draggable: false,
            show: 'fade',
            hide: 'fade',
            title: 'Enter Outside Medicnes And XLO Amount',
            position: "top",
            buttons: {
                "Save": function () {
                    xajax_saveOutMedsXLO(enc, $j('#meds_total').val().replace(/,/g, ''), $j('#xlo_total').val().replace(/,/g, ''));
                    $j(this).dialog("close");
                },
                "Cancel": function () {
                    $j(this).dialog("close");
                },
            }
        });
    });


    $j('#doccvrg').click(function () {
        if (!$('isFinalBill').checked) {
            $j('#coverage-dialog').dialog({
                autoOpen: true,
                modal: true,
                height: 'auto',
                width: '60%',
                resizable: false,
                draggable: false,
                show: 'fade',
                hide: 'fade',
                title: 'Coverage Distribution',
                position: "top",
                buttons: {
                    "Save": function () {
                        if (parseFloat($j('#phic-max-PF').html().replace(/,/g, '')) < 0 || parseFloat($j('#total-excess').html().replace(/,/g, '')) < 0) {
                            alert("Total PHIC PF Coverage or The total excess is negative. Please Distribute The PF Properly for Data Consistency. \n Thank You.");
                            return false;
                        } else {
                            // added by Christian 01-27-20
                            var saveFrom = 'doccvrg';
                            saveDoctorCoverage(saveFrom);
                            isSaved(1);
                            // end Christian 01-27-20
                        }
                    },
                    "Cancel": function () {
                        $j(this).dialog("close");
                    },
                },
                close: function (event, ui) {
                    isSaved(0); //Added by Christian 01-14-20
                    populateBill();
                },
                open: function () {
                    $j(".numeric").numeric();
                    calculateDetails1();
                    calculateDetails2();
                    assignDoctoTable()
                }
            });
        }
    });


    $j('#rate_type').on('change', function () {
        calcDrCharge();
        //alert('sa');
    });

    //added by Jasper Ian Q. Matunog 11/21/2014
    $j('#btnCSFp2').click(function() {

        var admissionDt = $j('#admission_dte').val();
        var billnr = $j('#bill_nr').val();
        var enc_no = $j('#enc').val();
        var pid =  $j('#pid').val();
        var billdate = $j('#billdate').val();
        var rawUrlData = {reportid:'csfp2',
                      repformat:'pdf',
                      admissionDt:admissionDt,
                      param:{enc_no:enc_no,billnr:billnr,pid:pid}};
        var urlParams = $j.param(rawUrlData);
        window.open('../reports/show_report.php?'+ urlParams, '_blank');
    });

    //added by Jeff Ponteras 03-26-18
    $j('#btnCSFp2Blank').click(function() {
        var billnr = $j('#bill_nr').val();
        var enc_no = $j('#enc').val();
        var pid =  $j('#pid').val();
        var billdate = $j('#billdate').val();
        var rawUrlData = {reportid:'csfp2Blank',
                      repformat:'pdf',
                      param:{enc_no:enc_no,billnr:billnr,pid:pid}};
        var urlParams = $j.param(rawUrlData);
        window.open('../reports/show_report.php?'+ urlParams, '_blank');
    });

    // -added by art 11/22/14
    //-for add additional meds and xlo limit dialog
    $j('#overwritelimitbtn').click(function () {
        var enc = $j('#encounter_nr').val();
        var pid = $j('#pid').val();
        dialogOverwrite= $j('#overwritelimit-div')
            .dialog({
                autoOpen: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 320,
                width: 420,
                title: 'Overwrite Limit',
                position: 'top',
                buttons: {
                    Save: function () {
                        var addamountxlo = $j('#addamountxlo').val();
                        var addamountmeds = $j('#addamountmeds').val();

                        var amountxlo = addamountxlo.replace(/[^\d\.\-\ ]/g, '');
                        var amountmeds = addamountmeds.replace(/[^\d\.\-\ ]/g, '');
                        if (addamountxlo == 0 && addamountmeds == 0) {
                            alert('AMOUNT NOT VALID. Please input amount and try again');
                            $j('#addamountxlo').focus();
                        } else {
                            var conf = confirm("Add amounts to the current limit?");
                            if(conf) {
                                xajax_saveAdditionalLimit(enc, amountxlo, amountmeds);
                            }
                        }
                    },
                    Cancel: function () {
                        $j(this).dialog("close");
                        $j("#overwritelimit-form")[0].reset();
                        populateBill();
                    }
                },
                open: function () {
                    //$j("#addamountxlo").numeric();
                    //$j('#addamountmeds').numeric();
                    $j("#addamountxlo").keyup(function(e){
                        var isFloatXlo = IsFloatOnly('input#addamountxlo');
                        if(!isFloatXlo){
                            e.preventDefault();
                            if ($j.inArray(e.keyCode, [46, 8, 9, 27, 13, 16, 17, 18, 20, 110, 116, 190]) !== -1 ||
                                    // Allow: Ctrl+A
                                (e.keyCode == 65 && e.ctrlKey === true) ||
                                    // Allow: Ctrl+C
                                (e.keyCode == 67 && e.ctrlKey === true) ||
                                    // Allow: Ctrl+X
                                (e.keyCode == 88 && e.ctrlKey === true) ||
                                    // Allow: Ctrl+R
                                (e.keyCode == 82 && e.ctrlKey === true) ||
                                    // Allow: home, end, left, right
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                                // let it happen, don't do anything
                                return;
                            }
                            alert('XLO amount is not valid. Please enter amount again.');
                            $j("#addamountxlo").val('');
                        }
                    });

                    $j("#addamountmeds").keyup(function(e){
                        var isFloatMeds = IsFloatOnly('input#addamountmeds');
                        if(!isFloatMeds){
                            e.preventDefault();
                            if ($j.inArray(e.keyCode, [46, 8, 9, 27, 13, 16, 17, 18, 20, 110, 116, 190]) !== -1 ||
                                    // Allow: Ctrl+A
                                (e.keyCode == 65 && e.ctrlKey === true) ||
                                    // Allow: Ctrl+C
                                (e.keyCode == 67 && e.ctrlKey === true) ||
                                    // Allow: Ctrl+X
                                (e.keyCode == 88 && e.ctrlKey === true) ||
                                    // Allow: Ctrl+R
                                (e.keyCode == 82 && e.ctrlKey === true) ||
                                    // Allow: home, end, left, right
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                                // let it happen, don't do anything
                                return;
                            }
                            alert('Meds amount is not valid. Please try again.');
                            $j("#addamountmeds").val('');
                        }
                    });
                },
                close: function () {
                    $j("#overwritelimit-form")[0].reset();
                    populateBill();
                }
            });
        return false;
    });
    //end art

    //Added by Gervie 01/26/2016
    $j('#notesbtn').click(function(){
        $j('#note-div').dialog({
            autoOpen: true,
            modal: true,
            show: 'fade',
            fade: 'fade',
            height: 220,
            width: 400,
            resizable: false,
            draggable: false,
            title: "Notes",
            position: "top",
            buttons: {
                "Save": function () {
                    var pid = $j('#pid').val();
                    var note = $j('#patient_note').val();
                    var encr = $j('#encounter_nr').val(); //added 01-10-2017

                    xajax_saveNote(pid, note, encr);
                    $j(this).dialog("close");
                },
                "Cancel": function () {
                    $j(this).dialog("close");
                }
            },
            open: function(){
                var pid = $j('#pid').val();
                xajax_setPatientNote(pid);
            }
        });
    });
}

function getPatientNote(){
    var pid = $j('#pid').val();
    xajax_setPatientNote(pid);
}

function setNoteLabel(bool){
    if(bool == 1)
        $j("#notesbtn").button({label: "View Note", icons: {primary: " ui-icon-document"}}).css("background", "#F00");
    else
        $j("#notesbtn").button({label: "Create Note", icons: {primary: " ui-icon-document"}}).css("background", "#12aef3 url(../../js/jquery/themes/seg-ui/images/ui-bg_glass_75_12aef3_1x400.png) 50% 50% repeat-x");
}

function hoverlimit(string, is_final){
    // alert(is_final);
    if(is_final == 1){ // edited by: syboy 08/09/2015
         // $j('#overwritelimitbtn').addClass("ui-state-disabled").addClass("ui-state-disabled"); commented out by: syboy 08/09/2015
         $j('#overwritelimitbtn').attr("disabled", "disabled").addClass("ui-state-disabled"); // added by: syboy 08/09/2015
         $j('#overwritelimitbtn').hover(function(){
            return overlib(string, AUTOSTATUS,WIDTH, 600);
        }); 
        $j('#overwritelimitbtn').mouseleave(function(){  
            return nd();
        });
    }else{
        $j('#overwritelimitbtn').hover(function(){
            return overlib(string, AUTOSTATUS,WIDTH, 600);
        }); 
        $j('#overwritelimitbtn').mouseleave(function(){
            return nd();
        });
    }
    
}
function alertlimit(success){
    if (success) {
        alert('Additional Limit Successfully Saved!');
        $j('#overwritelimit-div').dialog('close');
    }else{
        alert('Additional Limit is not Saved!');
    }
}

function format(input) {
    var nStr = input.value + '';
    nStr = nStr.replace( /\,/g, "");
    x = nStr.split( '.' );
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while ( rgx.test(x1) ) {
        x1 = x1.replace( rgx, '$1' + ',' + '$2' );
    }
    input.value = x1 + x2;
}

function IsFloatOnly(element) {
    var value = $j(element).val();
    var regExp = /^\d+(,\d+)*\d*\.?\d*$/;
    return value.match(regExp);
}

function isNBB(){

    var casetype = $j('#caseTypeOption option:selected').html();
    is_phic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true; // added by: syboy 08/06/2015

    if ((casetype == 'NBB') || (casetype == 'HSM') || (is_phic == true)) { // edited by: syboy 08/06/2015
        $j('#overwritelimitbtn').show();
    }else{
        $j('#overwritelimitbtn').hide();
    }

}

function assignCurrentLimit(xlo,meds,xloCov,medsCov){
    $j('#xlolimit').html(xlo);
    $j('#medslimit').html(meds);
    $j('#xloremain').html(xloCov);
    $j('#medsremain').html(medsCov);
}

function saveDoctorCoverage(saveFrom)
{
    var data = new Array();
    var fields = $j('#doc-coverage :input').serializeArray();
    data["refno"] = $j('#adj_refno').val();
    $j.each(fields, function(i, field){
        data[field.name] = field.value;
    });
    xajax_saveDoctorCoverage(data,saveFrom); // updated by Christian 01-27-20
}

function assignMemCategDesc(categ_desc, id, hist)
{
    if ($j('#phic').val().toUpperCase() == "NONE") {
         categ_desc = "NONE ASSIGNED";
     }else{
         categ_desc = (categ_desc == "" ? "NONE ASSIGNED" : categ_desc);
         $j('#memcategory_id').val(id);
     }

    $('billcol_01').colspan = "1";
    $('categ_col').style.display = "inline";
     categ_desc = (categ_desc == "" ? "NONE ASSIGNED" : categ_desc);
    $('mcategdesc').innerHTML = categ_desc;
    $('mcategdesc').style.background = "red";
}

function assignInsurance(nr)
{
    $j('#phic').val(nr);
    dialogInsurance.dialog('close');
}


function jsAddRefNo(tagId, ref){
    var refno = ref.refno;
    var source = ref.source;
    var hd = '<input type="hidden" id="'+source+'" name="'+source+'" value="'+refno+'"/>';
   $(tagId).innerHTML += hd;
}
//added by poliam 01/04/2014
function jsOnchangeConfineType() {
    var enc = $j('#encounter_nr').val();
    var type = $('confineTypeOption').options[$('confineTypeOption').selectedIndex].value;
    var bill_dte = $('billdate').value;
    var classify_id = $j('#classify_id').val();
    var create_id =  $j('#classify_id').val();

    if(enc != '') {
        //alert("selected options ="+ $('confineTypeOption').selectedIndex+ "\n selected value = "+ $('confineTypeOption').value );
        xajax_setConfinementType(enc, type, classify_id, create_id, bill_dte);
    } else {
        alert('Please select patient first');
    }
}

function js_setOption(tagId, value){
   var e1Targe = '#'+tagId;

   $j(e1Targe).val(value);
}// end of function js_setOption


//ended by poliam 01/04/2014
/*-------------Accommodation Dialog-------------*/
function updateValidationMessage(obj, msg){
    $(obj).style.display = '';
    $(obj).innerHTML += (msg +'<br>');
}//end updateValidationMessage

function jsClearList(tagId){
    $(tagId).innerHTML = '';
}

function disable(){
    var hide = '';

    if ($('isFinalBill').checked){
        hide = 'display:none';
    }else if (b_new){
        hide = 'display:none';
    }
}


/*-------------Accommodation Dialog-------------*/
var cur_accom_type = '';
function resetAccommDialogForm(){
    xajax_setWardOptions();
    assignRmRate('');
    jsClearList('validationAccomMsgBox');
    $('ward_nr').value = '';
    $('rm_nr').value = '';
    $('validationAccomMsgBox').style.display = 'none';
}//end resetAccommDialogForm

function assignRmRate(rmrate) {
    $('rate').value = rmrate;
}//end assignRmRate

function jsAccOptionsChange(obj, value){
    assignRmRate('');
    if(obj.id == 'wardlist') {
        $('ward_nr').value = value;
        if(Number(value)>0){
            xajax_setWardRooms(value);
        }
        else{
            js_ClearOptions('roomlist');
            js_AddOptions('roomlist','- Select Room -', 0);
        }
    }
    else {
        $('rm_nr').value = value;
        if(Number(value)>0){
            var room_info = new Object();
            room_info.ward_nr = $('ward_nr').value;
            room_info.room_nr = value;
            xajax_getAccommodationRate(room_info);
        }

    }
}//end jsAccOptionsChange

// function js_AddOptions(tagId, text, value){
//     var elTarget = $(tagId);
//     if(elTarget){
//         var opt = new Option(text, value);
//         opt.id = value;
//         elTarget.appendChild(opt);
//     }
//     var optionsList = elTarget.getElementsByTagName('OPTION');
// }//end of function js_AddOption

// function js_ClearOptions(tagId){
//     var optionsList, el=$(tagId);
//     if(el){
//         optionsList = el.getElementsByTagName('OPTION');
//         for(var i=optionsList.length-1; i >=0 ; i--){
//             optionsList[i].parentNode.removeChild(optionsList[i]);
//         }
//     }
// }//end of function js_ClearOptions

function isValidAccomForm(){
    var ward_nr = Number($('ward_nr').value.trim());
    var room_nr = Number($('rm_nr').value.trim());
    var encounter_nr = Number($('acc_enc_nr').value.trim());
    var room_rate = Number($('rate').value.trim());
    var objBox = 'validationAccomMsgBox';
    jsClearList(objBox);
    if(ward_nr<=0){
        updateValidationMessage(objBox, 'Please select a ward');
    }
    if(room_nr<=0){
        updateValidationMessage(objBox, 'Please select a room');
    }
    if(room_rate<=0){
        updateValidationMessage(objBox, 'Accommodation charge must be nonzero');
    }
    if($(objBox).style.display == ''){
      return false;
    }
    else {
      return true;
    }
}//end isValidAccomForm

function jsSetupAccommodationForm(start_date, end_date){
    var min_date = new Date(start_date);
    var max_date = new Date(end_date);
    if(min_date >= max_date ){
        $('btnaccommodation').disabled = 'disabled';
    }else{
        jQuery('#btnaccommodation').prop('disabled', false);
        jQuery('#occupydatefrom').datepicker('option', 'minDate', min_date);
        jQuery('#occupydatefrom').datepicker('option', 'maxDate', min_date);
        jQuery('#occupydatefrom').datepicker('setDate', min_date);
        jQuery('#occupydateto').datepicker('option', 'minDate', min_date);
        jQuery('#occupydateto').datepicker('option', 'maxDate', max_date);
        jQuery('#occupydateto').datepicker('setDate', min_date);
    }
}

function jsSaveAccommodation(){
    var data = new Object();

    data.ward_nr = Number($('ward_nr').value.trim());
    data.room_nr = Number($('rm_nr').value.trim());
    data.encounter_nr =  Number($('acc_enc_nr').value.trim());
    data.room_rate = Number($('rate').value.trim());
    data.datefrom = $('occupydatefrom').value;
    data.dateto = $('occupydateto').value;

    var bill_dt = $('billdate').value;
    var fields = jQuery("#faccbox :input").serializeArray();

    var accom_effec =  Date.parse($j('#accom_effectivity').val());
    var case_date_parse =  Date.parse($j('#admission_dte').val());

    if(case_date_parse < accom_effec){
        data.before_accom = 1;
    }

    xajax_saveAccommodation(data, bill_dt);
}//end jsSaveAccommodation

function jsRecomputeAccommodation(){
    var details = new Object();
    details.bill_dt = $('billdate').value;
    details.bill_frmdte = $('date_admitted').value;
    details.encounter_nr = Number($('encounter_nr').value.trim());
    details.death_date = '';
    if($('isdied').checked){
        details.death_date = $('deathdate').value;
    }
    xajax_populateAccommodation(details);
}//jsRecomputeAccommodation

function showAccommodationList(bshow) {
    $('accommodation_div').style.display = 'none';
    if (bshow){
        $('accommodation_div').style.display = '';
    }
}//showAccommodationList

function jsAccommodationList(data, total_charge, b_new){
    var type_nr = Number(data.type_nr);
    var room = data.room;
    var ward = data.ward;
    var rm_nr = data.location_nr;
    var type_desc = data.name;
    var days_count = data.days_stay;
    var adm_date = data.admission_date;
    var dis_date = data.discharge_date;
    var rm_rate = data.room_rate;
    var source = data.source;
    var srcRow, prefx = '';
    var hide = '';
    var acc_type = data.accommodation_type;

    var accom_effec =  Date.parse($j('#accom_effectivity').val());
    var case_date_parse =  Date.parse($j('#admission_dte').val());

    //added by gelie 10-29-2015
    var accom_date = '';
    if(adm_date != '01/01/1970' && dis_date != '01/01/1970'){   //check if admission and discharge date 
        accom_date = ' ('+adm_date+' to '+dis_date+') ';        //are not null
    }
    //end gelie

    if ($('isFinalBill').checked){
        hide = 'display:"none"';
    }else if (b_new){
        hide = 'display:"none"';
    }

    if(case_date_parse > accom_effec){
         hide = 'display:none';
         b_new = 'none';
    }

    $j('#accomodation_type').val(data.accommodation_type);

    if (!isNaN(type_nr)) {
        if (source=='BL'){
            prefx = '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer; '+hide+';" onclick="promptDelAccom('+ward+',\''+source+'\',\''+room+'\')"></td>'+
                    '<td style="border-left:hidden" width="52%">'+
                        'Room No. :'+rm_nr+'<br>'+
                        'Room Type:'+type_desc+
                    '</td>';
            }else{
             prefx = '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer; display:'+b_new+';" onclick="promptDelAccom('+ward+',\''+source+'\',\''+room+'\')"></td>'+
                    '<td style="border-left:hidden" width="52%">'+
                        'Room No. :'+rm_nr+'<br>'+
                        'Room Type:'+type_desc+
                    '</td>';
            }
        var days_label = 'Day';
        if(Number(days_count)>1)
            days_label += 's';

        hrs_label = '';
        if(data.is_per_hour == 1){
            var hrs_count = data.hours_stay;
            var hrs_label = ", "+hrs_count+' Hour';
            if(Number(hrs_count)>1)
                hrs_label += 's';
        }
        
        srcRow = '<tr id="type_'+type_nr+'_'+source+'">'+prefx+
                    '<td width="20%" align="center">'+
                        days_count+' '+days_label+hrs_label+accom_date+' <br>'+        //updated by gelie 10-21-2015
                    '</td >'+
                    '<td width="15%" align="right">'+rm_rate
                    +'</td>'+
                    '<td width="15%" align="right">'+total_charge
                    +'</td>'+
                '</tr>';
    }else{
        srcRow = '<tr>'+
                    '<td colspan="2" width="55%">No accommodation charged!</td>'+
                    '<td width="15%">&nbsp;</td >'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                '</tr>';
    }
    $('body_accListDetails').innerHTML += srcRow;

}

function setAccSubTotal(accAp,excess){
    $('accAP').innerHTML = accAp; // Display actual price charge


    if(excess)
        excess = excess;
    else
        excess = 0;

    accexcess = parseFloat(excess);
    acc_computed = parseFloat(accAp.replace(/,/g,""));
     $j('#save_total_acc_charge').val(acc_computed);
}//end setAccSubTotal

function promptDelAccom(id, type, rm_type){
    $('delAccomType').value = type;
    $('room').value = id;
    $('ward').value = rm_type;
    jQuery('#dialogAccDelConfirm').dialog('open');
}//end promptDelAccom

function jsDeleteAccommodation(){
    var room_info = new Object();
    room_info.encounter_nr = Number($('acc_enc_nr').value.trim());
    room_info.accom_type = $('delAccomType').value;
    room_info.room_type = $('room').value;
    room_info.ward_type = $('ward').value;
    xajax_delAccommodation(room_info);
}//end jsDeleteAccommodation
/*----------------end Accommodation Dialog------------------------------*/

/*----------------Miscellaneous Service(s)-------------*/
function addMiscService(info) {
    if(info.source=='Miscellaneous'){
        xajax_chargeMiscService(info,'xlo');
    }else{
        xajax_chargePharmaSupply(info,'xlo');
    }
}//end addMiscService

function jsHospitalServices(obj, servCharge, b_new){
    info = JSON.parse(obj);
    var prefx = '';
    var servCode = info.srv_code;
    var servDesc = info.srv_desc;
    var servQty = info.qty;
    var servPrice = info.srv_price;
    var servProvider = info.source_code;
    var grpDesc = info.grp_desc;
    var refno = info.ref_nr;
    var hidden = '';
    var hide = '';
    var encoder = info.encoder;

    if ($('isFinalBill').checked){
        hide = 'display:"none"';
    }else if (b_new){
        hide = 'display:"none"';
    }

    var srcRow, onClickAction='', source='', provider ='';
    if (isNaN(servProvider)) {
        switch (servProvider) {
            case 'LB':
                provider = 'LAB - '+ grpDesc;
                prefx = '<td align="center" width="3%"></td><td width="35%">'+servDesc+'</td>';
                break;
            case 'RD':
                provider = 'RAD - '+ grpDesc;
                prefx = '<td align="center" width="3%"></td><td width="35%">'+servDesc+'</td>';
                break;
            case 'SU':
                provider = grpDesc;
                prefx = '<td align="center" width="3%"></td><td width="35%">'+servDesc+'</td>';
                break;
            case 'MS':
                source = 'Pharmacy';
                provider = grpDesc;
                prefx = '<td align="center" width="3%">'
                            +'<img src="../../images/btn_delitem.gif" class="imgdelete" '
                            +'style="border-right:hidden; cursor:pointer; '+hide
                                +';" onclick="promptDelMiscService(\''
                                +servCode+'\', \''+servDesc.substring(0,70)+'\', \''+source+'\')"></td>'+
                        '<td style="border-left:hidden" width="35%">'+servDesc+'</td>';
                break;
            case 'MS_CON':
                source = 'Pharmacy';
                provider = grpDesc;
                prefx = '<td align="center" width="3%">'
                            +'<img src="../../images/btn_delitem.gif" class="imgdelete" '
                            +'style="border-right:hidden; cursor:pointer; '+hide
                                +';" onclick="promptDelMiscService(\''
                                +servCode+'\', \''+servDesc.substring(0,70)+'\', \''+source+'\')"></td>'+
                        '<td style="border-left:hidden" width="35%">'+servDesc+'</td>';
                break;
            case 'OA':
                source = 'Miscellaneous';
                provider = grpDesc;
                prefx = '<td align="center" width="3%">'
                            +'<img src="../../images/btn_delitem.gif"  class="imgdelete" '
                            +'style="border-right:hidden; cursor:pointer; '+hide
                                +';" onclick="promptDelMiscService(\''
                                +servCode+'\', \''+servDesc.substring(0,70)+'\', \''+source+'\')"></td>'+
                        '<td style="border-left:hidden" width="35%">'+servDesc+'</td>';
                break;
        }

        if(!info.same){
            hidden = '<div id="xlo_hidden_inputs" style="display:none">'+
                        '<input type="hidden" id="xlo_'+refno+'" name="xlo_'+refno+'" value="'+refno+'_'+servProvider+'"/>'+
                     '</div>';
        }
        //edited by art 07/28/2014 added hover
        srcRow = '<tr id="code_'+servCode+'" onmouseover="return overlib(\'' +encoder+ '\', AUTOSTATUS,WIDTH, 400);" onmouseout="nd();">'
                    +prefx+
                    '<td width="17%" align="left">'+provider+'</td>'+
                    '<td width="15%" align="center">'+servQty+'</td>'+
                    '<td width="15%" align="right">'+servPrice+'</td>'+
                    '<td width="15%" align="right">'+servCharge+'</td>'+
                    hidden+
                 '</tr>';
    } else {
        srcRow = '<tr>'+
                    '<td colspan="2" width="*">No hospital services charged!</td>'+
                    '<td width="17%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                 '</tr>';
    }

    $('body_hsListDetails').innerHTML += srcRow;
}//end of jsHospitalServices

// Unpaid CPS
function jsUnpaidCps(obj){
    info = JSON.parse(obj);

    var ref_no = info.ref_no;
    var serv_dt = info.serv_dt;
    var encounter_nr = info.enc_nr;
    var serv_name = info.serv_name;
    var serv_code = info.service_code;
    var price = info.price_cash;

    var price_cash = info.price_cash_orig;
    var price_charge = info.price_charge;
    var request_doctor = info.request_doctor;
    var request_dept = info.request_dept;
    var is_in_house = info.is_in_house;
    var clinical_info = info.clinical_info;
    var status = info._status;
    var quantity = info.quantity;

    var srcRow;
    var inputs;

    cpsDetails = JSON.parse(obj);
    //console.log(cpsDetails.length);

    if(ref_no != '0') {
        srcRow = '<tr>' +
        '<td style="padding: 5px;">' + serv_dt + '</td>' +
        '<td style="padding: 5px; text-align: center;">' + ref_no +
            '<input type="hidden" name="del_refno[]" value="' + ref_no + '">' +
        '</td>' +
        '<td style="padding: 5px">' + serv_name + '</td>' +
        '<td style="padding: 5px; text-align: center;">' + serv_code +
            '<input type="hidden" name="del_servcode[]" value="' + serv_code + '" />' +
        '</td>' +
        '<td style="padding: 5px; text-align: center;">' + price + '</td>' +
        '</tr>';
    }
    else{
        srcRow = '<tr>' +
            '<td style="padding-left: 5px; text-align: center; color: #FF0000;" colspan="5"><strong>No Unpaid Transactions.</strong></td>' +
            '</tr>';
    }

    //console.log(info.serv_name);

    $('body_cpsListDetails').innerHTML += srcRow;

    inputs = '<input id="ref_no" type="hidden" name="ref_no" value="' + ref_no + '">' +
             '<input type="hidden" name="serv_code" value="' + serv_code + '" />' +
             '<input type="hidden" name="price" value="' + price + '" />' +
             '<input type="hidden" name="price_cash" value="' + price_cash + '" />' +
             '<input type="hidden" name="price_charge" value="' + price_charge + '" />' +
             '<input type="hidden" name="request_doctor" value="' + request_doctor + '" />' +
             '<input type="hidden" name="request_dept" value="' + request_dept + '" />' +
             '<input type="hidden" name="is_in_house" value="' + is_in_house + '" />' +
             '<input type="hidden" name="clinical_info" value="' + clinical_info + '" />' +
             '<input type="hidden" name="status" value="' + status + '" />' +
             '<input type="hidden" name="quantity" value="' + quantity + '" />' +
             '<input id="enc_nr" type="hidden" name="encounter_nr" value="' + encounter_nr + '" />';

    $('cpsInputs').innerHTML += inputs;

}
// end unpaid cps

function jsCpsAdt(obj){
    //console.log(obj);
    adt = JSON.parse(obj);


    var refno = adt.ref_no;
    var name = adt.serv_name;
    var serv_code = adt.serv_code;
    var modify_id = adt.modify_id;
    var modify_dt = adt.modify_dt;

    var srcRow;

    if(refno != '0') {
        srcRow = '<tr>' +
        '<td style="padding: 5px; text-align: center;">' + refno + '</td>' +
        '<td style="padding: 5px;">' + name + '</td>' +
        '<td style="padding: 5px; text-align: center;">' + serv_code + '</td>' +
        '<td style="padding: 5px; text-align: center;">' + modify_id + '</td>' +
        '<td style="padding: 5px; text-align: center;">' + modify_dt + '</td>' +
        '</tr>';
    }
    else{
        srcRow = '<tr>' +
        '<td style="padding-left: 5px; text-align: center; color: #FF0000;" colspan="5"><strong>No Converted CPS Cash Transactions.</strong></td>' +
        '</tr>';
    }

    //console.log(info.serv_name);

    $('body_cpsAuditTrail').innerHTML += srcRow;
}

function cpsShowAuditTrail(){
    $j('#cpsAuditTrail').attr('hidden', false);
    $j('#cps_show_at').attr('hidden', true);
    $j('#cps_hide_at').attr('hidden', false);
}

function cpsHideAuditTrail(){
    $j('#cpsAuditTrail').attr('hidden', true);
    $j('#cps_show_at').attr('hidden', false);
    $j('#cps_hide_at').attr('hidden', true);
}

function promptDelMiscService(serv_code, serv_desc, source){
    $('delMiscServName').innerHTML = serv_desc;
    $('delMiscServCode').value = serv_code;
    $('delSource').value = source;
    jQuery('#dialogMiscServicesDelConfirm').dialog('open');

}//end promptDelMiscService

function jsDeleteMiscService(){
    var details = new Object();
    details.encounter_nr = Number($('encounter_nr').value.trim());
    details.bill_dt = $('billdate').value;
    details.bill_frmdte = $('admission_dte').value;
    details.serv_code = Number($('delMiscServCode').value);
    var source = $('delSource').value;
    details.tbl_loc = 'xlo';
    if(source=='Pharmacy'){
        xajax_delPharmaSupply(details,'xlo');
    }else{
        xajax_delMiscService(details);
    }
}//end jsDeleteMiscService

/**
 * @author Nick 5-31-2015
 * @description Reload Services accordingly
 * @see modules/billing_new/js/billing-items.js
 * @param area
 */
function jsRecomputeServices(area){
    switch(area){
        case 'acc': reloadAccommodation(); break;
        case 'xlo': reloadSupplyItems(ajaxPopulateOtherItems); break;//case 'xlo': reloadXlo(); break;
        case 'meds': reloadMedicines(); break;
        case 'misc': reloadMiscellaneous(); break;
        case 'op': reloadOperatingRooms(); break;
        default : populateBill();
    }
}

//populate all fields...
function jsRecomputeServices_old(area){
    var enc = $j('#encounter_nr').val();
    var bill_dte = $j('#billdate').val();
    var bill_frmdte = $j('#admission_dte').val();
    var bill_nr = $j('#bill_nr').val();
    if($('isdied').checked){
        deathdate = $j('#deathdate').val();
    }else{
        deathdate = '';
    }


    if(area=='xlo'){
        xajax_populateXLO(enc,bill_dte,bill_frmdte,deathdate);
    }else if (area=='meds') {
        xajax_populateMeds(enc,bill_dte,bill_frmdte,deathdate);
    }else if (area=='misc'){
        xajax_populateMisc(enc,bill_dte,bill_frmdte,deathdate);
    }else if(area=='op'){
        xajax_getBilledOps(enc,bill_dte,bill_frmdte,deathdate);
    }
    xajax_populateBill(enc,bill_dte,bill_frmdte,deathdate,firstratecode,secondratecode);

}//end jsRecomputeServices

function setMiscServices(hsAP){
    $('hsAP').innerHTML = hsAP;
    miscServices_computed = parseFloat(hsAP.replace(/,/g,""));
    $j('#save_total_srv_charge').val( miscServices_computed);
}//end setHospitalServices
/*----------------end Miscellaneous Service(s)-------------*/

/*------------------- Drugs & Medicines -------------------*/
function jsRecomputeMeds(){
    var details = new Object();
    details.encounter_nr = Number($('encounter_nr').value.trim());
    details.bill_dt = $('billdate').value;
    details.bill_frmdte = $('date_admitted').value;

    xajax_populateMeds(details);
}//end jsRecomputeMeds

function jsMedicineList(obj, b_new) {
    var info = JSON.parse(obj);
    var tagId = 'body_mdListDetails';
    var refno = info.ref_nr;
    var bestellnum = info.srv_code;
    var artikelname = info.srv_desc;
    var itemqty = info.qty;
    var itemprice = info.srv_price;
    var acPrice = info.itemcharge;
    var flag = info.flag;
    var unused = info.unused;
    var unused_amnt = parseFloat(info.unused_amnt);
    var unused_qty = info.unused_qty;
    var source = info.src;
    var srcRow, sMsg;
    var hidden ='';
    var servProvider;
    var hide, desc;
    var encoder = info.encoder; //added by art 07/30/2014

    if ($('isFinalBill').checked){
        hide = 'display:"none"';
    }else if (b_new){
        hide = 'display:"none"';
    }

    isphic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true;

    if(unused=='1' && isphic){
       desc = artikelname+' <span style="color:#ff002a;font-weight:bold">(Unused Blood ('+unused_qty+') )</span>';
    }else{
        desc = artikelname;
    }
    //edited by art 07/30/2014 added overlib
    if (bestellnum) {
        if (source=='Order'){
            srcRow = '<tr id="code_'+bestellnum+'" onmouseover="return overlib(\'' +encoder+ '\', AUTOSTATUS,WIDTH, 400);" onmouseout="nd();"><td align="center" width="3%"><img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer; '+hide+'" onclick="promptDelSupply('+bestellnum+', \''+artikelname+'\')"></td>'+
                        '<td style="border-left:hidden" width="52%">'+desc+'</td>';
            servProvider = 'OR';
        }else{
            srcRow = '<tr onmouseover="return overlib(\'' +encoder+ '\', AUTOSTATUS,WIDTH, 400);" onmouseout="nd();"><td width="3%"></td><td width="52%">'+desc+'</td>';
            servProvider = 'PH';
        }

        if(!info.same){
            hidden = '<td id="meds_hidden_inputs" style="display:none">'+
                        '<input type="hidden" id="md_'+refno+'" name="md_'+refno+'" value="'+refno+'_'+servProvider+'"/>'+
                     '</td>';
        }

        srcRow += '<td width="15%" align="center">'+itemqty+'</td>'+
                    '<td width="15%" align="right">'+itemprice+'</td>'+
                    '<td width="15%" align="right">'+acPrice+'</td>'+
                    hidden+
                    '</tr>';
    } else {
        sMsg = "No medicines charged!";
        srcRow = '<tr>'+
                    '<td colspan="2" width="55%">'+sMsg+'</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                '</tr>';
    }
    $(tagId).innerHTML += srcRow;
}//end jsMedicineList

function setMedicine(medAP){
    $('medAP').innerHTML = medAP;
    med_computed = parseFloat(medAP.replace(/,/g,""));
    $j('#save_total_med_charge').val(med_computed);
}// end of setMedicine

function addMoreMedicine(details){
    xajax_chargePharmaSupply(details,'meds');
}//end addMoreMedicine

function promptDelSupply(code, name){
    $('delMedName').innerHTML = name;
    $('delMedCode').value = code;
    jQuery('#dialogMedicineDelConfirm').dialog('open');
    }

function jsDeleteMed(){
    var details = new Object();
    details.encounter_nr = Number($('encounter_nr').value.trim());
    details.bill_dt = $('billdate').value;
    details.bill_frmdte = $('date_admitted').value;
    details.serv_code = $('delMedCode').value;
    details.tbl_loc = 'med';
    xajax_delPharmaSupply(details,'meds');
}
/*-------------------end Drugs & Medicines ----------------*/

/*------------------- Miscellaneous Charges ---------------*/
function jsRecomputeMiscCharges(){
    var enc = $j('#encounter_nr').val();
    var bill_dte = $j('#billdate').val();
    var bill_frmdte = $j('#admission_dte').val();
    var bill_nr = $j('#bill_nr').val();
    if($('isdied').checked){
        deathdate = $j('#deathdate').val();
    }else{
        deathdate = '';
    }

    xajax_populateBill(enc,bill_dte,bill_frmdte,deathdate,firstratecode,secondratecode);
}//end jsRecomputeMiscCharges

function jsMiscellaneousList(obj, total, b_new) {
    var details = JSON.parse(obj);
    var code = details.code;
    var refno = details.refno;
    var name = details.name;
    var description = ((details.desc!='null')? '' : details.desc);
    var qty = details.qty;
    var misc_chrg = details.chrg;
    var srcRow;
    var hide;

     if ($('isFinalBill').checked){
        hide = 'display:"none"';
    }else if (b_new){
        hide = 'display:"none"';
    }

    if (code) {

            srcRow = '<tr id="code_'+code+'">'+
                    '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer; '+hide+'" onclick="promptDelMiscChrg(\''+code+'\', \''+name+'\')"></td>'+
                    '<td style="border-left:hidden" width="52%"><span>'+name+'</span><br/><span class="description">'+description+'</span></td>';

        srcRow += '<td width="15%" align="center">'+qty+'</td>'+
                     '<input id="ref'+details.code+'_'+details.refno+'" type="hidden" value="'+details.refno+'">'+
                    '<td width="15%" align="right">'+misc_chrg+'</td>'+
                    '<td width="15%" align="right">'+total+'</td>'+
                 '</tr>';
    }else {
        srcRow = '<tr>'+
                    '<td colspan="2" width="*">No miscellaneous charges!</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                 '</tr>';
    }

    $('body_mscListDetails').innerHTML += srcRow;
}//end jsMiscellaneousList

function setMiscCharges(McharAp){
    $('mscAP').innerHTML = McharAp;

    miscCharges_computed = parseFloat(McharAp.replace(/,/g,""));
    $j('#save_total_msc_charge').val(miscCharges_computed);
}//end setMiscCharges

function promptDelMiscChrg(code, name){
    $('delMiscChargeName').innerHTML = name;
    $('delMiscChargeCode').value = code;

    jQuery('#dialogMiscChargesDelConfirm').dialog('open');
}//end promptDelMiscChrg

function jsDeleteMiscCharge(){
    var data = new Object();
    data.encounter_nr = Number($('encounter_nr').value.trim());
    data.bill_dt = $('billdate').value;
    data.bill_frmdte = $('admission_dte').value;
    data.code = $('delMiscChargeCode').value;
    xajax_delMiscChrg(data);
}//end jsDeleteMiscCharge

function addMiscChrg(details){
    xajax_chargeMiscChrg(details);
}//end addMiscChrg

/*---------------end  Miscellaneous Charges ---------------*/

/*--------------------Operating Room Accomodation Charges---------------------------*/
function clearORACFields() {
    $j("#total_rvu").val('');
    $j("#multiplier").val('');
    $j("#oprm_chrg").val('');
    tmpRVU = 0;
    drCharge = 0;
    tmpOPDetails = [];
}

function clearAppliedProcedureList(){
    $j('#procedure-list-body').empty();
}

function js_ClearOptions(tagId){
    var id = '#'+tagId;
    $j(id).empty();
}

function js_AddOptions(tagId, text, value){
    var elTarget = '#'+tagId;
    $j(elTarget).append($j("<option></option>").val(value).text(text));
}

function showOperatingRoomAcc() {
    clearORACFields();
    xajax_setORWardOptions();
}

//added by Nick 05-27-2014
function disableCaseTypeOptions(txt){
    if($('updateCaseTypePermission').value != 1){
        $j.each($j('#caseTypeOption option'),function(i, field){
            if(field.value > 2){
                $j('#caseTypeOption [value='+field.value+']').prop('disabled',true);
            }
        });
    }
}

function jsOpAccChrgOptionsChange(id, value){
    if(id == 'opwardlist') {
        $('opw_nr').value  = value;
        xajax_setORWardRooms(value);
    }
    else {
        $('opr_nr').value = value;
        //xajax_getRoomRate($('ward_nr').value, $('rm_nr').value);
    }
}

function initProcedureList() {
    var enc_nr = $j('#encounter_nr').val();
    xajax_populateAppliedOpsList(enc_nr);
}

//added by Nick 05-12-2014
function updateOpDate(entryno, code, refno){

    var editBox = 'opdateEditBox'+entryno+'_'+code;
    var editBoxVal = $(editBox).value;
    var origdate = $(editBox).value;

    if(confirm("Are you sure you want to edit the date?")){
        xajax_updateOpDate(editBoxVal, refno, code, entryno);
    }else{
        $(editBox).value = origdate;
    }
}

//added by Nick 05-12-2014
function showHideOpEditBox(entryno,code,mode,selectedDate){
    var editBox = 'opdateEditBox'+entryno+'_'+code;
    var editLnk = 'opdateEditLink'+entryno+'_'+code;
    if(mode){
        $(editBox).style.display = '';
        $(editLnk).style.display = 'none';
        $(editBox).focus();
    }else{
        $(editBox).style.display = 'none';
        $(editLnk).style.display = '';
        $(editLnk).innerHTML = $(editBox).value;
    }
}

function addAppliedOPtoList(details) {

    // var target = $('section').value;
    var rowSrc;
    var charge = details.rvu * details.multiplier;
    var fcharge = numFormat(charge);

    if (details.code) {
        rowSrc = '<tr>' +
            '<td>' +
            '<span id="description' + details.entry_no + '_' + details.code + '" style="font:bold 12px Arial">' + details.description + '</span><br />' +
            '<input id="descriptionFull' + details.entry_no + '_' + details.code + '" type="hidden" value="' + details.descriptionFull + '">' +
            '</td>' +
            '<td>' +
            '    <span style="font:bold 12px Arial;color:#660000">' + details.code + '</span>' +
            '    <input id="code' + details.entry_no + '_' + details.code + '" type="hidden" value="' + details.code + '">' +
            '</td>' +
                // '<td align="center">'+
                //     '<input id="groupcode'+details.entry_no+'_'+details.code+'" type="hidden" value="'+details.groupcode+'">'+details.groupcode+
                // '</td>'+
            '<td align="center">' +
            '<input id="opdate' + details.entry_no + '_' + details.code + '" type="hidden" value="' + details.opdate + '">' +
                //added by Nick 05-12-2014
            '<a id="opdateEditLink' + details.entry_no + '_' + details.code + '" type="text" class="opDateEditor" onclick="showHideOpEditBox(' + details.entry_no + ',' + details.code + ',true,\'\')">' + details.opdate + '</a>' +
            '<input id="opdateEditBox' + details.entry_no + '_' + details.code + '" type="text" value="' + details.opdate + '" class="opDateEditor" style="display:none;" />' +
                //end Nick
            '</td>' +
            '<td align="center">' +
            '<input id="rvu' + details.entry_no + '_' + details.code + '" type="hidden" value="' + details.rvu + '">' + details.rvu + '</span></td>' +
            '<td align="center">' +
            '<input id="multiplier' + details.entry_no + '_' + details.code + '" type="hidden" value="' + details.multiplier + '">' + details.multiplier +
            '</td>' +
            '<td align="right">' +
            '<span id="chrgrow_' + details.entry_no + '_' + details.code + '"><input id="chrg' + details.entry_no + '_' + details.code + '" type="hidden" value="' + charge + '">' + fcharge + '</span>' +
            '</td>' +
            '<td align="right">' +
            details.pf +
            '</td>' +
            '<td align="center">' +
            '<input onclick="addSelectedOP(' + details.code + ',\'' + details.entry_no + '\',\'' + details.refno + '\',\'' + charge + '\',\'' + details.multiplier + '\',' + details.rvu + ',' + details.pf + ');" type="checkbox" id="op_selected' + details.entry_no + '_' + details.code + '" name="op_selected' + details.entry_no + '_' + details.code + '" value="">' + //added by Kenneth 05-12-2016
            '<input id="entryno_' + details.code + '" type="hidden" value="' + details.entry_no + '">' +
            '<input id="refno_' + details.ntry_no + '_' + details.code + '" type="hidden" value="' + details.refno + '">' +
            '</td>' +
            '</tr>';
    }
    else {
        rowSrc = '<tr><td colspan="8" style="">No procedure...</td></tr>';
    }

    $j("#procedure-list-body").prepend(rowSrc);

    //added by Nick 05-12-2014
    $j('.opDateEditor').datepicker({
        onSelect: function (selectedDate) {
            updateOpDate(details.entry_no, details.code, details.refno);
        },
        onClose: function () {
            showHideOpEditBox(details.entry_no, details.code, false);
        },
        changeMonth: true,
        changeYear: true
    });

}//end of function addAppliedOPtoList

function calculateProfessionalChargeFromRvu(pf, role, rvu, hasAnestheologist) {
    var pf_limiter = $j('#pf_limiter').val();
    var pf_above_surg = $j('#pf_above_surg').val();
    var pf_above_anes = $j('#pf_above_anes').val();
    var pf_under_surg = $j('#pf_under_surg').val();
    var pf_under_anes = $j('#pf_under_anes').val();
    // alert(pf_under_anes);
    // surgeon with anestheologist
    if(role == 'D3' && hasAnestheologist) {
        if(rvu <= pf_limiter) {
            return pf * pf_under_surg;
        } else {
            return pf * pf_above_surg;
        }
    }

    // surgeon without anestheologist
    if(role == 'D3') {
        return pf;
    }

    // anestheologist
    if (role == 'D4') {
        if(rvu <= pf_limiter) {
            return pf * pf_under_anes;
        } else {
            return pf * pf_above_anes;
        }
    }
}

function addSelectedOP(code, entryno, refno, charge, multiplier, rvu, pf) {
    var tmpID = "#op_selected" + entryno + '_' + code;
    var role = $j('#role_nr').val();
    var charge = parseFloat(charge);
    var pfCharge = 0;

    /*if(role=='D3'){
     pfCharge = charge;
     }else if(role=='D4'){
     pfCharge = charge * (0.40);
     }else{
     pfCharge = 0;
     }*/

    pfCharge = calculateProfessionalChargeFromRvu(pf, role, rvu, $j('#Anes').is(':checked'));

    //if (role == 'D3') {
    //    if ($j('#Anes').is(':checked'))
    //        pfCharge = pf * 0.60;
    //    else
    //        pfCharge = pf;
    //} else if (role == 'D4') {
    //    pfCharge = pf * 0.40;
    //} else {
    //    pfCharge = 0;
    //}

    var n = 0, i = 0, tmp;
    for (i = 0; n < 1; i++) {
        if (!tmpOPDetails[i]) {
            tmpOPDetails[i] = refno + ';' + entryno + ';' + code + ';' + rvu + ';' + multiplier;
            n = 1;
        }
    }

    if ($j(tmpID).is(':checked')) {
        currentRVU=rvu;
        currentPrice=pf;
        tmpRVU += rvu;
        drCharge += pfCharge;
        tempTotalSelectedOperation += pf;
    } else {
        currentRVU=0;
        currentPrice=0;
        tmpRVU -= rvu;
        drCharge -= pfCharge;
        tempTotalSelectedOperation -= pf;
    }

    opsCode = code;
}

function addDrCharge(){
    if(isNaN(drCharge)) $j("#charge").val(0);
    else $j("#charge").val(drCharge);
}

function calcTotRVU(){
    var pDetails = new Object();
    pDetails.encNr = $j('#encounter_nr').val();
    pDetails.billdate = $j('#billdate').val();
    pDetails.nrvu = tmpRVU;
    pDetails.opsCode = opsCode;
    if(tmpRVU>0)
        xajax_updateRVUTotal(pDetails);
}

function applyRVUandMult(rvu,mul,chg){

    mul = parseFloat(mul).toFixed(2);
    chg = parseFloat(chg).toFixed(2);
    // fcharge = fcharge.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    $j("#total_rvu").val(rvu);
    $j("#multiplier").val(mul);
    $j("#oprm_chrg").val(chg);
}

function saveORAccommodation(){
    var accDetails = new Object();
    var billDate = $j('#billdate').val();

    accDetails.opacc_enc_nr = $j('#opacc_enc_nr').val();
    accDetails.opw_nr = $j('#opwardlist').val();
    accDetails.opr_nr = $j('#orlist').val();
    accDetails.total_rvu = $j('#total_rvu').val();
    accDetails.multiplier = $j('#multiplier').val();
    accDetails.oprm_chrg = $j('#oprm_chrg').val();
    accDetails.frm_dte = $j('#admission_dte').val();

    // alert(accDetails.opw_nr);
    if(accDetails.opr_nr)
        xajax_saveORAccommodation(accDetails,billDate,tmpOPDetails);
    else
        alert("Please fill the neccesary inputs!");
}

function addORAccCharge(opAccDetails){

    var ward = $j("#opwardlist option:selected").text();
    var rm = opAccDetails.rm_nr;
    var n_rvu = opAccDetails.nrvu;
    var n_multiplier = opAccDetails.nmultiplier;
    var n_total = opAccDetails.nchrg;
    var total = numFormat(n_total);

    if(opAccDetails.desc){
        var desc = opAccDetails.desc;
    }else{
        var desc = ward+' - Room '+rm;
    }

    var srcRow;
    var idROw = 'op_code'+rm+n_rvu;
    var sFunc = 'onclick="promptDelOpAccom(\''+rm+'\', \''+idROw+'\')">';

    if(rm){
        srcRow += '<tr id="'+idROw+'">'+
                  '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden; cursor:pointer;" '+sFunc+'</td>'+
                  '<td style="border-left:hidden" width="52%">'+desc+'</td>'+
                  '<td width="15%" align="center">'+n_rvu+'</td>'+
                  '<td width="15%" align="right">'+n_multiplier+'</td>'+
                      '<td width="15%" align="right">'+n_total+'</td>'+
                  '</tr>';
    }
    // else{
    //     sMsg = "No O.R. accommodation charged!";
    //     srcRow = '<tr>'+
    //                 '<td colspan="2">'+sMsg+'</td>'+
    //                 '<td width="15%">&nbsp;</td>'+
    //                 '<td width="15%">&nbsp;</td>'+
    //                 '<td width="15%">&nbsp;</td>'+
    //             '</tr>';
    // }


    $j( '#body_opsListDetails' ).prepend( srcRow );
    $j( "#dialogOR").dialog("close");
}

function promptDelOpAccom(rm,idRow){
    var bill_dte = $j('#billdate').val();
    var bill_frmdte = $j('#admission_dte').val();
    var enc = $j('#encounter_nr').val();

        xajax_delOpAccommodation(enc, bill_dte, bill_frmdte, rm, idRow);
    }

function delORAccCharge(idRow){
    var id = '#'+idRow;
    $j(id).remove();
    alert("Successfully Deleted!");
}

function showOpsTotals(opsAP) {
    $('opsAP').innerHTML = opsAP;
    ops_computed = parseFloat(opsAP.replace(/,/g,""));
    $j('#save_total_ops_charge').val(ops_computed);
    // $('opsDiscount').innerHTML = opsDiscount;
    // $('opsHC').innerHTML = opsHC;
    // $('opsEX').innerHTML = opsEX;
}

/*----------end--------Operating Room Accomodation Charges-----------end--------------*/

/*------------------------------------Add Doctors------------------------------------*/


function clearProfDialog() {
    $('doclist').value = "0";
    $('rolearea').value = "0";
    $('role_level').value = "0";
    $('ndays').value = "0"; // added by art 01/28/2014
    $('rate_type').value = "0"; // added by art 01/28/2014

    $('charge').value = '';
    $('dr_nr').value = '';
    $('role_nr').value = '';
    $('tier_nr').value = '';
    $('opstaken').innerHTML = '';

    $('doclist').focus();
}

function addDoctor(){
    var data = new Object();
    data.dr_nr = $j('#doclist').val();
    data.role_nr = $j('#rolearea').val();
    data.role_level = $j('#role_level').val();
    data.tier_nr = $j('#role_level').val();
    data.rate_type = $j('#rate_type').val();
    data.ndays = $j('#ndays').val();
    data.charge = $j('#charge').val();
    data.excluded = ($j('#is_excluded').checked) ? '1' : '0';
    data.hiAdm = HIadm;
    data.hiSurg = HIsurg;
    data.hiAnes = HIanes;
    data.enc = $j('#encounter_nr').val();
    data.bill_dte = $j('#billdate').val();
    data.accommodationType = $j('#doctorAccommodationType').val();
    //edited by art 01/28/2014
    //if(data.doclist == '0')
    if(data.dr_nr == '0')
        alert("Please select a doctor");
    //else if(data.rolearea == '0')
    else if(data.role_nr == '0')
        alert("Specify doctor's role");
   /* else if(data.role_level == '0')
        alert("Specify doctor's role level");*/
    //end art
    else if(data.charge=='')
        alert("Enter doctor's charge");
    else {
        xajax_processPrivateDrCharge(data, data.bill_dte, tmpOPDetails);
        clearProfDialog();
        $j( "#dialogAddDoc").dialog( "close" );
        return false;
    }
}

function jsDoctorsFees(tblId, roleId, roleDesc, totalCharge, Coverage){
    var srcRow;
    if(roleId){
        srcRow = '<tr>'+
                    '<td align="right"></td>'+
                    '<td colspan="5" style="font-weight:bold">'+roleDesc+
                        '<table id="'+roleId+'" width="95%" border="0" cellpadding="1" cellspacing="0" align="right">'+
                        '</table>'+
                    '</td>'+
                    // '<td align="right">'+totalCharge+'</td>'+
                    // '<td align="right" id="coverage_'+roleId+'">'+Coverage+'</td>'+
                 '</tr>';
    }else{
        srcRow = '<tr>'+
                    '<td colspan="5">No professional fees charged!</td>'+
                    '<td align="right">&nbsp;</td>'+
                    // '<td align="right">&nbsp;</td>'+
                 '</tr>';
    }
    $(tblId).innerHTML += srcRow;
}// end of jsDoctorsFee

function initMsgDialog(id,role_nr){
     var conf=confirm("Delete the selected doctor?");
    if (conf==true)
    {
        //clearDocVars();
        xajax_rmPrivateDr($('encounter_nr').value, id, role_nr, $('billdate').value);
    }

}

function initMsgDialog2(id,role_nr){
    var conf=confirm("Delete the selected doctor?");
    if (conf==true)
    {
        xajax_rmDr($('encounter_nr').value, id, role_nr, $('billdate').value);
    }
    }

function showPFTotals(detaislPF) {
   if (typeof(detaislPF)!=='undefined') {
    totalPF = parseFloat(detaislPF.pfCharge);
    totalPFDiscount = parseFloat(detaislPF.pfDiscount);
    PFd1 = parseFloat(detaislPF.d1);
    PFd2 = parseFloat(detaislPF.d2);
    PFd3 = parseFloat(detaislPF.d3);
    PFd4 = parseFloat(detaislPF.d4);
   } else {
        totalPF = 0;
        totalPFDiscount = 0;
        PFd1 = 0;
        PFd2 = 0;
        PFd3 = 0;
        PFd4 = 0;
   }


}

function jsOptionChange(obj, value){
    switch (obj.id) {
        // case 'doclist':
        //     $j('#dr_nr').val(value);
        //     getDrRole();
        //     break;

        case 'rolearea':
            xajax_getDrRole(value);
            break;

        // case 'role_level':
        //     $j('#tier_nr').val(value);
        //     getDrRole();
        //     break;
    }

}

function drRole(role) {
    $j('#role_nr').val(role);
    $j('#rate_type').val(0);

    if (role == 'D3') {
        $j("#Anes").attr("checked", false);
        $j("#hasAnes").show();
    }
    else{
        $j("#Anes").attr("checked", false);
        $j("#hasAnes").hide();
    }
    calcDrCharge();
}

function rollBackCasedate(stringed) {
    alert(stringed);
    $j('#billdate_display').val(origcurdate);
}
/**
 * @author Nick 2-6-2015
 * #Anes, #rolearea onChange event
 * @return undefined
 */
function newDoctorForm_Change(){
    var pf_limiter = $j('#pf_limiter').val();
    var pf_above_surg = $j('#pf_above_surg').val();
    var pf_above_anes = $j('#pf_above_anes').val();
    var pf_under_surg = $j('#pf_under_surg').val();
    var pf_under_anes = $j('#pf_under_anes').val();
    //added by Kenneth 05-12-2016
    if($j('#rate_type').val()==0){
        var role = $j('#role_nr').val();
        var multiplier = 0.0;
        if(role == "D3") {//surgeon
            if($j('#Anes').is(':checked')){
                if(currentRVU<=pf_limiter) multiplier = pf_under_surg; 
                else multiplier = pf_above_surg;
            }
            else
                multiplier = 1;
        }else if(role == "D4"){//anestheologist
            if(currentRVU<=pf_limiter) multiplier = pf_under_anes;
            else multiplier = pf_above_anes;
        }
        $j("#charge").val(currentPrice * multiplier);
    }
    else{
        currentRVU=0;
        currentPrice=0;
        calcDrCharge();
    }
    // var role = $j('#role_nr').val();
    // var multiplier = 0.0;
    // if(role == "D3") {//surgeon
    //     if($j('#Anes').is(':checked')){
    //         if(currentRVU<501) multiplier = 0.64; 
    //         else if(currentRVU>500) multiplier = 0.71;
    //     }
    //     else
    //         multiplier = 1;
    // }else if(role == "D4"){//anestheologist
    //     if(currentRVU<501) multiplier = 0.36;
    //     else if(currentRVU>500) multiplier = 0.29;
    // }
    // $j("#charge").val(currentPrice * multiplier);
    //end Kenneth
    // var isphic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true;
    // if(!isphic){
    //     var multiplier = 0;
    //     var role = $j('#role_nr').val();
    //     if(role == "D3") {//surgeon
    //         if($j('#Anes').is(':checked'))
    //             multiplier = 0.6;
    //         else
    //             multiplier = 1;
    //     }else if(role == "D4")//anestheologist
    //         multiplier = 0.4;

    //     $j("#charge").val(tempTotalSelectedOperation * multiplier);
    // }else{
    //     calcDrCharge();
    // }
}

function calcDrCharge(){
    var days = $j("#ndays").val();
    var role = $j('#role_nr').val();
    var chk = $j('#Anes').is(':checked');
    var rate_nr = $j('#rate_type').val();

    var charge_D1 = 0, charge_D3 = 0, charge_D4 = 0;

    var first_D1 = $j('#first_rate option:selected').attr('value_D1');
    var first_D3 = $j('#first_rate option:selected').attr('value_D3');
    var first_D4 = $j('#first_rate option:selected').attr('value_D4');

    //Professional Fee Distrbution based on Role for 2nd Case
    var second_D1 = $j('#second_rate option:selected').attr('value_D1');
    var second_D3 = $j('#second_rate option:selected').attr('value_D3');
    var second_D4 = $j('#second_rate option:selected').attr('value_D4');

    if(rate_nr==1){
        charge_D1 = ((first_D1) ? first_D1 : 0 );
        charge_D3 = ((first_D3) ? first_D3 : 0 );
        charge_D4 = ((first_D4) ? first_D4 : 0 );
    }else if(rate_nr==2){
        charge_D1 = ((second_D1) ? second_D1 : 0 );
        charge_D3 = ((second_D3) ? second_D3 : 0 );
        charge_D4 = ((second_D4) ? second_D4 : 0 );
    }else if(rate_nr==3){
        charge_D1 = parseFloat(((first_D1) ? first_D1 : 0 )) + parseFloat(((second_D1) ? second_D1 : 0 ));
        charge_D3 = parseFloat(((first_D3) ? first_D3 : 0 )) + parseFloat(((second_D3) ? second_D3 : 0 ));
        charge_D4 = parseFloat(((first_D4) ? first_D4 : 0 )) + parseFloat(((second_D4) ? second_D4 : 0 ));
    }


    if(days){
        days = parseFloat(days);
    }else{
        days = 0;
    }

    if((role=='D1')||(role=='D2')){
        if(HIadm){
            $j("#charge").val(charge_D1);
        }else if(role=='D1'){
            $j("#charge").val((days * 300));
        }else{
            $j("#charge").val(0.00);
        }
    }else{
        $j("#charge").val(0.00);
    }

    switch(role){

        case 'D1':
              if(HIadm){
                    $j("#charge").val(charge_D1);
                }else{
                    $j("#charge").val((days * 300));
                }
              break;

        case 'D2':
              if(HIadm){
                    $j("#charge").val(charge_D1);
                }else{
                    $j("#charge").val((days * 300));
                }
              break;

        case 'D3':
                if(HIsurg){
                    if(chk){
                        $j("#charge").val(charge_D3);
                    }else{
                        $j("#charge").val(parseFloat(charge_D3)+parseFloat(charge_D4));
                    }
                }else{
                    $j("#charge").val(0.00);
                }
          break;

        case 'D4':
              if(HIanes){
                    $j("#charge").val(charge_D4);
                }else{
                    $j("#charge").val(0.00);
                }
          break;

        default:
            $j("#charge").val(0.00);
        break;

    }

}

/*--------------end-------------------Add Doctors------------end---------------------*/


function genChkDecimal(obj, n){
    var objValue = obj.value;
    objValue = objValue.replace(/,/g, '');

    if (objValue=="")
        return false;

    if (isNaN(objValue)) {
        alert("Invalid amount!");
        obj.value="0.00";
        // obj.focus();
        return false;
    }

    // n = n || 2;

    // var nf = new NumberFormat();
    // nf.setPlaces(n);
    // nf.setNumber(objValue);

    // obj.value = nf.toFormatted();
    return true;
}// end of function genChkDecimal

function genChkInteger(obj){
    var objValue = obj.value;

    if (objValue=="")
        return false;

    if (isNaN(objValue)) {
        alert("Invalid whole number!");
        obj.value="0";
        // obj.focus();
        return false;
    }

    // var nf = new NumberFormat();
    // nf.setPlaces(0);
    // nf.setNumber(objValue);

    // obj.value = nf.toFormatted();
    return true;
}// end of function genChkInteger

function trimString(objct){
  objct.value.replace(/^\s+|\s+$/g,"");
  objct.value = objct.value.replace(/\s+/g,"");
}

function closeSelEncDiaglog()
{
    dialogSelEnc.dialog('close');
    firstpopulateBill();
}

//added by Nick, 2/25/2014
function disableImgDelete(){
    if(isFinalBill){
        $j('.imgdelete').hide();
    }
}

function disableUI(){
    //added by art 11/11/14
    var caneditiffinal = $j('#caneditiffinal').val();
    if (caneditiffinal != 1) {
        $j("#btnDiagnosis").attr("disabled", "disabled").addClass("ui-state-disabled");
    }
    var caneditInsuranceiffinal = $j('#caneditInsuranceiffinal').val(),
    caneditnewinsurancefinal = $j('#caneditnewinsurancefinal').val();
    //eded by poliam
    if(caneditInsuranceiffinal != 1){
        $j("#btnInsurance").attr("disabled", "disabled").addClass("ui-state-disabled");
    }
    if(caneditnewinsurancefinal != 1){
        $j("#btnInsurance2").attr("disabled", "disabled").addClass("ui-state-disabled");
    }


    $j('#overwritelimitbtn').attr("disabled", "disabled").addClass("ui-state-disabled"); // added by: syboy 08/10/2015
    
    //end art
    //enable diagnosis and insurance by borj 2014-11-03
    //$('btnInsurance').disabled = 'disabled';
    //$('btnDiagnosis').disabled = 'disabled';
    $('isdied').disabled = 'disabled';
    $('btnOutMedsXLO').disabled = 'disabled';
    $('confineTypeOption').disabled = 'disabled';
    $('caseTypeOption').disabled = 'disabled';
    $('billdate_display').disabled = 'disabled';
    
    isphic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true;
    
    if(isphic){
        $('first_multiplier').disabled = 'disabled';
        $('second_multiplier').disabled = 'disabled';
    }
 /*   $('first_rate').disabled = 'disabled';
    $('second_rate').disabled = 'disabled';*/

    disabled_button(1);//added by art 20/21/2015
}

function populateBill_old()
{

    showLoading();//added by nick 05-12-2014

    var enc = $j('#encounter_nr').val();
    var bill_dte = $j('#billdate').val();
    var bill_frmdte = $j('#admission_dte').val();
    var bill_nr = $j('#bill_nr').val();
    if($('isdied').checked){
        deathdate = $j('#deathdate').val();
    }else{
        deathdate = '';
    }

    //added by janken 11/19/2014 for getting the phic acct number
    var phic = $j('#phic').val();

    xajax_checkInsurance(enc);
    xajax_populateMeds(enc,bill_dte,bill_frmdte,deathdate);
    xajax_populateXLO(enc,bill_dte,bill_frmdte,deathdate);
    xajax_populateMisc(enc,bill_dte,bill_frmdte,deathdate);
    xajax_getBilledOps(enc,bill_dte,bill_frmdte,deathdate);
    xajax_populateBill(enc,bill_dte,bill_frmdte,deathdate,firstratecode,secondratecode);
    // xajax_populateCaseRate(enc,bill_dte,bill_frmdte);
     //added by poliam 01/04/2014
     xajax_classification(enc,bill_dte,bill_frmdte);
     xajax_confinment(enc);
     xajax_getConfineTypeOption(enc,bill_dte);
     //calculateTotals();
     //ended by poliam
     xajax_getCurrentLimit(enc,bill_dte,bill_frmdte,deathdate);
}

function assignValue(id,val){
    $j('#'+id).val(val);
}

//added by carriane 07/24/17
function assignHtml(id,val){
    $j('#'+id).html(numFormat(val));
}
//end carriane

//addded by Carriane 07/11/17
function overwriteLimitButton(accomodation, encounter_type){
    var button = $j('#overwritelimitbtn');
    var paypermission = $j('#paywardPermission').val();
    var servicepermission = $j('#servicewardPermission').val();
    var canoverwrite = $j('#canOverWriteLimit').val();
    // alert(encounter_type);
    if(((servicepermission == "") && (paypermission == "") && (canoverwrite == ""))){
        if(encounter_type == 3 || encounter_type == 4){
            button.attr("disabled", "disabled").addClass("ui-state-disabled");
        }
    }else{
        if((servicepermission) || (paypermission)){
            if((servicepermission == "") && (accomodation == 1)){
               button.attr("disabled", "disabled").addClass("ui-state-disabled");
            }

            if((paypermission == "") && (accomodation == 2)){
               button.attr("disabled", "disabled").addClass("ui-state-disabled");
            }
        }     
    }
}
//end Carriane

function toggleDeathDate(bool) {
    if($('isdied').checked){
        $('label_deathdate').style.display = "";
        $('input_deathdate').style.display = "";
    }else if(!$('isdied').checked){
        $('label_deathdate').style.display = "none";
        $('input_deathdate').style.display = "none";
        if(bool){
        xajax_setDeathDate($j('#pid').val(),0,$j('#encounter_nr').val());
        populateBill();
    }
    }
    //added by Earl Galope 02/20/2018
    if($('isFinalBill').checked && $('isdied').checked){
        $('isFinalBill').checked=false;
    }
    //end
}

//added by Art 01/05/2014
function showRemainingDays(show,ndayscover,ndaysremain,save, ptype, dept){
    var IPBM_opd = 14;

    isphicRem = ($j('#phic').val().toUpperCase()=="NONE") ? false : true;

    if(show && typeof(ndaysremain)!=='undefined'){
        if (ndaysremain != ''  && isphicRem) {
            $('remaindays').style.display = "";
            $('coverdays').style.display = "";
            $('daysCovered').value = ndayscover;
        }else if(ndaysremain != '' && ptype == OUT_PATIENT && dept == MDC) { // Unknown
            $('remaindays').style.display = "";
            $('coverdays').style.display = "";
        }else{
            $('remaindays').style.display = "none";
            $('coverdays').style.display = "none";
        }

        $('remaindays').innerHTML = "Remaining Days : "+ndaysremain;
        $('coverdays').innerHTML = "Number of Days Covered : "+ndayscover;
        //$('coverdays').style.display = "";
        $('remaindays').setStyle({
         backgroundColor: '#FF0000',
        });
        $('coverdays').setStyle({
         backgroundColor: '#FF0000',
        });

        $('savethis').innerHTML = +save;
    }else{
        $('remaindays').style.display = "none";
        $('coverdays').style.display = "none";
    }
}
//end art

/*----------------------------------------------MEDICO LEGAL-------------------------------------------------*/

function showMedicoLegal(show_medico, medicoDesc) {
    if (show_medico==1) {
        $('medicolegal').style.display = "";
        $('ShowMedicoCases').value = medicoDesc;
        isMedicolegal = true;
    } else {
        $('medicolegal').style.display = "none";
    }

}
/*----------------------------------------------IS ER PATIENT-------------------------------------------------*/
function hideAccomodation (code)
{
    if (code == 1)
        $('accommodation_div').style.display = "none";
}


/*----------------------------------------------Case Rates-------------------------------------------------*/

function setFields(){
    var sel = "<option id='0' value='0'>-Select Code-</option>";
    var multiplier = "0";
    var amnt = "0";
    var desc = "No case rate selected."
    $j('#first_rate').html(sel);
    $j('#second_rate').html(sel);
    $j('#first_case_desc').html(desc);
    $j('#second_case_desc').html(desc);
    $j('#first_rate_amount').html(numFormat(amnt));
    $j('#second_rate_amount').html(numFormat(amnt));
    $j('#first_multiplier').html(multiplier);
    $j('#second_multiplier').html(multiplier);
}

/*
*  Updated by Jarel 03/05/2014
*  Populate Case Rate Details
*  Add features for Single Period of Confinement
*
* Updated by Nick 4/22/2014
* Different case rate amount for new born with hearing
* and non-hearing test
*/
function populateCaseRates(details){
    // alert(details.laterality);
    var spc_label;
    var laterality_label; // added by: syboy 10/11/2015

    //default values
    amntFirst = details.amntFirst;
    hf1 = details.hf1;
    pf1 = details.pf1;
    amntSecond = details.amntSecond;
    hf2 = details.hf2;
    pf2 = details.pf2;

    var localFirstMultiplier = localStorage.getItem("currentFirstMultiplier"),
        localSecondMultiplier = localStorage.getItem("currentSecondMultiplier");

    // added by: syboy 10/11/2015
    if (details.laterality == "L") {
        laterality_label = "Left";
    }else if(details.laterality == "B") {
        laterality_label = "Both";
    }else if(details.laterality == "R") {
        laterality_label = "Right";
    }else{
        laterality_label = "";
    }
    // ended

    spc_label = ((details.spc == 1) ? ' (SPC)' : '');
    if(details.code){
        var temp_lat = '';

        if(details.laterality){
            temp_lat = "_"+details.laterality;
        }
        var var_firstOptions = $j('<option>', {
                onmouseover : "return overlib(\'Laterality : "+laterality_label+" \', AUTOSTATUS,WIDTH, 200);",
                onmouseout : 'nd();',   
                value: amntFirst,
                text : details.code +spc_label,
                id : details.code+temp_lat,
                value_hf : hf1,
                value_pf : pf1,
                value_D1 : details.D1,
                value_D3 : details.D3,
                value_D4 : details.D4,
                operation_date : details.operation_date,
                rvu : details.rvu,
                laterality : details.laterality,
                desc : details.desc,
                case_type : details.cType,
                orig_amnt: details.amntFirst,
                orig_hf: details.orig_hf1,
                orig_pf: details.orig_pf1,
                sp_amnt: details.sp_amnt,
                sp_hf: details.sp_hf,
                sp_pf: details.sp_pf,
                multiplier: details.multiplier,
                orig_multiplier: details.orig_multiplier,
                orig_amntFirst: details.orig_amntFirst
        });
        
        if(details.spc == 1 || details.spc == 3) {
            var_firstOptions.prop('disabled', true);
        }

        $j('#first_rate').append(var_firstOptions);

        if(details.isSecCase==1){
            var var_secondOptions = $j('<option>', {
                onmouseover : "return overlib(\'Laterality : "+laterality_label+" \', AUTOSTATUS,WIDTH, 200);",
                onmouseout : 'nd();',
                value: amntSecond,
                text : details.code +spc_label,
                id : details.code+temp_lat,
                value_hf : hf2,
                value_pf : pf2,
                value_D1 : details.D1_sec,
                value_D3 : details.D3_sec,
                value_D4 : details.D4_sec,
                operation_date : details.operation_date,
                rvu : details.rvu,
                laterality : details.laterality,
                desc : details.desc,
                case_type : details.cType,
                orig_amnt: details.amntSecond,
                orig_hf: details.hf2,
                orig_pf: details.pf2,
                sp_amnt: details.sp_amnt,
                sp_hf: details.sp_hf,
                sp_pf: details.sp_pf,
                multiplier: details.multiplier,
                orig_multiplier: details.orig_multiplier,
                orig_amntSecond: details.orig_amntSecond
            });
            
            if(details.spc == 1 || details.spc == 3) {
                var_secondOptions.prop('disabled', true);
        }

            $j('#second_rate').append(var_secondOptions);
        }
            // Commented by : syboy 11/24/2015 : meow
            /*if(details.spc == 1 || details.spc == 3) {
                $j('#first_rate option:contains('+details.code+')').attr('disabled','disabled');
                $j('#second_rate option:contains('+details.code+')').attr('disabled','disabled');
            }*/
    }
}//end function populateCaseRates

/*
* Added by Jarel 03/05/2014
* Set Case Rate detials
*
* Updated by Nick, 4/22/2014
* Change case rate amount for new born
* with hearing or without hearing test
*/
function setCaserate(first_code,second_code,first_case_multiplier,second_case_multiplier,firstLaterality='',secondLaterality='')
{
    var HF1, HF2, HF, PF1, PF2, PF;
    var first_D1, first_D3, first_D4,
        second_D1, second_D3, second_D4;

    var data = new Object();
    var enc = $j('#encounter_nr').val();

    firstratecodeHolder = firstratecodeHolder=='' ? first_code : firstratecodeHolder; //Added by Christian 01-14-19
    secondratecodeHolder = secondratecodeHolder=='' ? second_code : secondratecodeHolder; //Added by Christian 01-14-19

    firstratecode = first_code;
    secondratecode = second_code;
    var first_lat = '';
    var second_lat = '';
    if(firstLaterality=='' && first_laterality != ''){
        first_lat='_'+first_laterality;
    }else{
        if(firstLaterality != ''){
            first_lat='_'+firstLaterality;
        }
    }
    if(secondLaterality=='' && second_laterality != ''){
        second_lat='_'+second_laterality;
    }else {
        if(secondLaterality != ''){
            second_lat='_'+secondLaterality;
        }
    }
    console.log(first_lat);
    console.log(second_lat);
    $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('selected',true);
    $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('selected',true);
    var first_op_date_str = '';
    var second_op_date_str = '';

    // var first_op_date = new Date($j('#first_rate option[id="'+first_code+'_'+first_lat+'"]').attr('operation_date'));
    // first_op_date_str = first_op_date.toString();
    // first_op_date_str = first_op_date.substring(4, 15);
    // var second_op_date = new Date($j('#second_rate option[id="'+second_code+'_'+second_lat+'"]').attr('operation_date'));
    // second_op_date_str = second_op_date.toString();
    // second_op_date_str = second_op_date.substring(4, 15);


    //setSecondCaseAttributes(first_code,second_code,withHtest);
    // alert(first_code+'_'+first_laterality);
    // alert(second_code+'_'+second_laterality);
    // alert($j('#first_rate option[id="'+first_code+'_'+first_lat+'"]').attr('desc'));
    var first_concat = $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('operation_date');
    var second_concat = $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('operation_date');
    if((first_concat === undefined || first_concat=='') && !$('isFinalBill').checked){
        $j('#first_case_desc').html($j('#first_rate option[id="'+first_code+first_lat+'"]').attr('desc'));
    }else{
        if(!$('isFinalBill').checked){
            $j('#first_case_desc').html($j('#first_rate option[id="'+first_code+first_lat+'"]').attr('desc'));
        }else{
            $j('#first_case_desc').html($j('#first_rate option[id="'+first_code+first_lat+'"]').attr('desc')+'<br/>Site Already Claimed on '+first_concat);
        }
    }
    if((second_concat === undefined || second_concat=='') && !$('isFinalBill').checked){
        $j('#second_case_desc').html($j('#second_rate option[id="'+second_code+second_lat+'"]').attr('desc'));
    }else{
        if(!$('isFinalBill').checked){
            $j('#second_case_desc').html($j('#second_rate option[id="'+second_code+second_lat+'"]').attr('desc'));
        }else{
            $j('#second_case_desc').html($j('#second_rate option[id="'+second_code+second_lat+'"]').attr('desc')+'<br/>Site Already Claimed on '+second_concat);
        }
    }
    // $j('#first_case_desc').html($j('#first_rate option[id="'+first_code+'_'+first_lat+'"]').attr('desc'));
    // $j('#second_case_desc').html($j('#second_rate option[id="'+second_code+'_'+second_lat+'"]').attr('desc'));
    localStorage.setItem("currentFirstMultiplier", $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('multiplier'));
    localStorage.setItem("currentSecondMultiplier", $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('multiplier'));

    if(first_case_multiplier == '')
        $j('#first_multiplier').val($j('#first_rate option[id="'+first_code+first_lat+'"]').attr('multiplier'));
    else
        $j('#first_multiplier').val(first_case_multiplier);

    if(second_case_multiplier == '')
        $j('#second_multiplier').val($j('#second_rate option[id="'+second_code+second_lat+'"]').attr('multiplier'));
    else
        $j('#second_multiplier').val(second_case_multiplier);

    $j('#first_multiplier_2').val($j('#first_rate option[id="'+first_code+first_lat+'"]').attr('orig_multiplier'));
    $j('#second_multiplier_2').val($j('#second_rate option[id="'+second_code+second_lat+'"]').attr('orig_multiplier'));

    $j('#first_rate_amount').html(numFormat($j("#first_rate option:selected").val()));
    $j('#second_rate_amount').html(numFormat($j("#second_rate option:selected").val()));

    //Assign Hopital Fee
    HF1 = $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('value_hf');
    HF2 = $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('value_hf');
    HF = parseFloat(((HF1)? HF1 : 0 )) + parseFloat(((HF2)? HF2 : 0 ));

    //Assign Professional Fee
    PF1 = $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('value_pf');
    PF2 = $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('value_pf');
    PF = parseFloat(((PF1)? PF1 : 0 )) + parseFloat(((PF2)? PF2 : 0 ));

    //Professional Fee Distrbution based on Role for First Case
    first_D1 = $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('value_D1');
    first_D3 = $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('value_D3');
    first_D4 = $j('#first_rate option[id="'+first_code+first_lat+'"]').attr('value_D4');

    //Professional Fee Distrbution based on Role for 2nd Case
    second_D1 = $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('value_D1');
    second_D3 = $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('value_D3');
    second_D4 = $j('#second_rate option[id="'+second_code+second_lat+'"]').attr('value_D4');

    //Set HF and PF in multiplier
    $j('#first_multiplier_2').attr('value_hf', HF1);
    $j('#first_multiplier_2').attr('value_pf', PF1);
    $j('#second_multiplier_2').attr('value_hf', HF2);
    $j('#second_multiplier_2').attr('value_pf', PF2);

    //var HF = parseFloat(()?$j("#first_rate option:selected").val():0);
    totalHealthInsuranceHF = parseFloat(HF);
    totalHealthInsurancePF = parseFloat(PF);
    //totalPackage = parseFloat(details.Total);
    //firstcase = parseFloat(details.amntFirst);
    //secondcase = parseFloat(details.amntSec);
    HIadm = parseFloat((first_D1)? first_D1 : 0) + parseFloat((second_D1)? second_D1 : 0) ;
    HIsurg = parseFloat((first_D3)? first_D3 : 0) + parseFloat((second_D3)? second_D3 : 0) ;
    HIanes = parseFloat((first_D4)? first_D4 : 0) + parseFloat((second_D4)? second_D4 : 0) ;

    calculateTotals();
    calculateDetails1();
    calculateDetails2();

    try {
        if(parseFloat($j('#phic-max-PF').html().replace(/,/g,'')) < 0 || parseFloat($j('#total-excess').html().replace(/,/g,'')) < 0 ){
            alert("Total PHIC PF Coverage is negative. Please Distribute The PF Properly for Data Consistency. \n Thank You.");
            $j('#doccvrg').click();
            return false;
        }
    } catch(ex){
        //console.log(ex.message);
    }
}
//Added by Christian 01-14-19
function isSaved(status) {
    firstratecodeHolder = status==1 ? firstratecode : firstratecodeHolder;
    secondratecodeHolder = status==1 ? secondratecode : secondratecodeHolder;
}

function uncheckCheckBox() 
{
    if(firstratecodeHolder!=firstratecode) {
        $j(".uncheckFirstCase").each(function(){
            if($j(this).prop("checked") == true) {
                $j(this).prop('checked', false);
                $j('.clearInputFirstCase').val(numFormat(0));
            }
        });
    }
    if(secondratecodeHolder!=secondratecode) {
        $j(".uncheckSecondCase").each(function(){
            if($j(this).prop("checked") == true) {
                $j(this).prop('checked', false);
                $j('.clearInputSecondCase').val(numFormat(0));
            }
        });
    }
}
//end Christian 01-14-19

function changeCase(caseNum){
    var enc = $j('#encounter_nr').val();
    var laterality, firstratecode_temp, secondratecode_temp, code, first_case_multiplier, second_case_multiplier;
    var retVal = true;
    var case_type = $j("#first_rate option:selected").attr('case_type');

    if(caseNum == 1){
        localStorage.setItem("temp_first_code", 0);
    }else{
        localStorage.setItem("temp_second_code", 0);
    }

    firstratecode_temp = $j("#first_rate option:selected").text();
    secondratecode_temp = $j("#second_rate option:selected").text();

    var firstCaseMultiplier = localStorage.getItem("currentFirstMultiplier");
    var secondCaseMultiplier = localStorage.getItem("currentSecondMultiplier");

    localFirstMultiplier = localStorage.getItem("firstMultiplier");
    localSecondMultiplier = localStorage.getItem("secondMultiplier");

    if(firstratecode_temp == NEWBORN_PKG || firstratecode_temp == NEWBORN_PKG2){
        if(secondratecode_temp == NEWBORN_PKG || secondratecode_temp == NEWBORN_PKG2){
            alert("Unable to assign Newborn Package to both Case Rate.");
            if(caseNum == 1){
                $('first_rate').selectedIndex = 0;
                first_case_multiplier = firstCaseMultiplier;
                return 0;
            }else{
                $('second_rate').selectedIndex = 0;
                second_case_multiplier = secondCaseMultiplier;
                return 0;
            }
        }
    }

    if(firstMultiplier != '') {
        first_case_multiplier = firstMultiplier;
    }
    else if(localFirstMultiplier && firstMultiplier != '') {
        first_case_multiplier = localFirstMultiplier;
    }
    else {
        first_case_multiplier = $j("#first_rate option:selected").attr("multiplier");
    }

    if(secondMultiplier != '') {
        second_case_multiplier = secondMultiplier;
    }
    else if(localSecondMultiplier && secondMultiplier != '') {
        second_case_multiplier = localSecondMultiplier;
    }
    else {
        second_case_multiplier = $j("#second_rate option:selected").attr("multiplier");
    }
    
    laterality = $j("#second_rate option:selected").attr('laterality');
    if(caseNum == '1'){
        if (firstratecode_temp == secondratecode_temp && laterality != ''){
            firstratecode = firstratecode_temp;
            first_case_multiplier = $j("#first_rate option:selected").attr("multiplier");
            localStorage.setItem("currentFirstMultiplier", $j("#first_rate option:selected").attr("multiplier"));
        } else if(firstratecode_temp != secondratecode_temp){
            firstratecode = firstratecode_temp;
            first_case_multiplier = $j("#first_rate option:selected").attr("multiplier");
            second_case_multiplier = $j("#second_rate option:selected").attr("multiplier");
            localStorage.setItem("currentFirstMultiplier", $j("#first_rate option:selected").attr("multiplier"));
            localStorage.setItem("currentSecondMultiplier", $j("#second_rate option:selected").attr("multiplier"));
        } else if(firstratecode_temp == secondratecode_temp){
            alert('The Package is already Selected as Second Case Rate');
            $('first_rate').selectedIndex = 0;
            first_case_multiplier = firstCaseMultiplier;
        }
        first_laterality = $j("#first_rate option:selected").attr("laterality");

    }

    if (caseNum == '2') {
        if (firstratecode_temp == secondratecode_temp && laterality != ''){
            secondratecode = secondratecode_temp;
            second_case_multiplier = $j("#second_rate option:selected").attr("multiplier");
            localStorage.setItem("currentSecondMultiplier", $j("#second_rate option:selected").attr("multiplier"));
        }
        else if (firstratecode_temp != secondratecode_temp){
            secondratecode = secondratecode_temp;
            first_case_multiplier = $j("#first_rate option:selected").attr("multiplier");
            second_case_multiplier = $j("#second_rate option:selected").attr("multiplier");
            localStorage.setItem("currentFirstMultiplier", $j("#first_rate option:selected").attr("multiplier"));
            localStorage.setItem("currentSecondMultiplier", $j("#second_rate option:selected").attr("multiplier"));
        }
        else {
            alert('The Package is already Selected as First Case Rate');
            $j('#second_rate').val(0);
            secondratecode = secondratecode;
            second_case_multiplier = secondCaseMultiplier;
        }
        second_laterality = $j("#second_rate option:selected").attr("laterality");
    }

    if($('first_rate').selectedIndex == 0) {
        first_case_multiplier = '';
    }

    if($('second_rate').selectedIndex == 0) {
        second_case_multiplier = '';
    }

    uncheckCheckBox(); //Added by Christian 01-14-19
    setCaserate(firstratecode, secondratecode, first_case_multiplier, second_case_multiplier,first_laterality,second_laterality);
    return retVal;
}

//added by Gervie 05/18/2016
function changeMultiplier(caserate) {
    var first_multiplier = $j('#first_multiplier').val();
    var second_multiplier = $j('#second_multiplier').val();

    var orig_first_multi = $j('#first_multiplier_2').val();
    var orig_second_multi = $j('#second_multiplier_2').val();

    var HF, HF1, HF2, PF, PF1, PF2, first_HF, first_PF, second_HF, second_PF;
    var first_D3, first_D4, second_D3, second_D4;

    var regExp = /^\d+$/;

    var data = new Object();
    data.encounter = $j('#encounter_nr').val();
    data.caserate = caserate;

    if(caserate == '1') {
        if(parseInt(first_multiplier, 10) <= parseInt(orig_first_multi, 10)){
            if(first_multiplier.match(regExp) && first_multiplier != 0){
                data.multiplier = first_multiplier;
                data.package_id = $j('#first_rate option:selected').attr('id');

                // added by carriane 10/05/17
                if(first_multiplier == orig_first_multi){
                    localStorage.setItem("firstMultiplier", first_multiplier);
                    localStorage.setItem("secondMultiplier", second_multiplier);
                    localStorage.setItem("currentFirstMultiplier", first_multiplier);
                    localStorage.setItem("currentSecondMultiplier", second_multiplier);

                    var orig_amnt = $j('#first_rate option:selected').attr('orig_amntFirst');
                    var orig_hf = $j('#first_rate option:selected').attr('orig_hf');
                    var orig_pf = $j('#first_rate option:selected').attr('orig_pf');
                    var base_amnt = orig_amnt/orig_first_multi;
                    var base_hf = orig_hf/orig_first_multi;
                    var base_pf = orig_pf/orig_first_multi;

                    var new_amnt = base_amnt*first_multiplier;
                    HF1 = base_hf*first_multiplier;
                    PF1 = base_pf*first_multiplier;
                    
                    data.amount = new_amnt;

                    first_D3 = PF1 * (0.60);
                    first_D4 = PF1 * (0.40);

                    $j('#first_rate_amount').html(numFormat(new_amnt));
                    $j('#first_rate option:selected').attr('value_hf', HF1);
                    $j('#first_rate option:selected').attr('value_pf', PF1);
                    $j('#first_rate option:selected').attr('value_D3', first_D3);
                    $j('#first_rate option:selected').attr('value_D4', first_D4);
                    $j('#first_rate option:selected').attr('multiplier', first_multiplier);
                    $j('#first_rate option:selected').val(new_amnt);

                    firstMultiplier = first_multiplier;
                    
                    $j('#first_multiplier_2').attr('value_hf', HF1);
                    $j('#first_multiplier_2').attr('value_pf', PF1);

                    xajax_caserateModificationHistory(data);
                }

                //added by Carriane 07/19/17
                if(first_multiplier < orig_first_multi){
                    var src = "<div style='background:white;'><br><br><h1 style='color:FF0505;font-size:30px;'><center>Multiplier entered in first caserate is below maximum. Do you still want to proceed?</center</h1>"+ "</div>";
                    
                    $j(src).dialog({
                        dialogClass: 'no-close',
                        autoOpen: true,
                        modal:true,
                        height: "auto",
                        width: "30%",
                        resizable: false,
                        show: "fade",
                        hide: "explode",
                        title: "Warning!",
                        position: "top", 
                        buttons: {
                            "YES": function() 
                            {   
                                localStorage.setItem("firstMultiplier", first_multiplier);
                                localStorage.setItem("secondMultiplier", second_multiplier);
                                localStorage.setItem("currentFirstMultiplier", first_multiplier);
                                localStorage.setItem("currentSecondMultiplier", second_multiplier);
                                
                                var orig_amnt = $j('#first_rate option:selected').attr('orig_amntFirst');
                                var orig_hf = $j('#first_rate option:selected').attr('orig_hf');
                                var orig_pf = $j('#first_rate option:selected').attr('orig_pf');
                                var base_amnt = orig_amnt/orig_first_multi;
                                var base_hf = orig_hf/orig_first_multi;
                                var base_pf = orig_pf/orig_first_multi;

                                var new_amnt = base_amnt*first_multiplier;
                                HF1 = base_hf*first_multiplier;
                                PF1 = base_pf*first_multiplier;

                                data.amount = new_amnt;

                                first_D3 = PF1 * (0.60);
                                first_D4 = PF1 * (0.40);

                                $j('#first_rate_amount').html(numFormat(new_amnt));
                                $j('#first_rate option:selected').attr('value_hf', HF1);
                                $j('#first_rate option:selected').attr('value_pf', PF1);
                                $j('#first_rate option:selected').attr('value_D3', first_D3);
                                $j('#first_rate option:selected').attr('value_D4', first_D4);
                                $j('#first_rate option:selected').attr('multiplier', first_multiplier);
                                $j('#first_rate option:selected').val(new_amnt);

                                firstMultiplier = first_multiplier;
                                
                                $j('#first_multiplier_2').attr('value_hf', HF1);
                                $j('#first_multiplier_2').attr('value_pf', PF1);

                                xajax_caserateModificationHistory(data);

                                $j(this).dialog("close");
                            },
                            "NO": function(){
                                $j('#first_multiplier').val($j('#first_rate option:selected').attr('multiplier'));
                                $j('#first_rate_amount').html(numFormat($j('#first_rate option:selected').val()));

                                $j(this).dialog("close");
                            }
                        },      
                    });
                }
                //end Carriane

                if(first_multiplier <= 0) {
                    alert('Invalid multiplier.');
                    $j('#first_multiplier').val($j('#first_rate option:selected').attr('multiplier'));
                    $j('#first_rate_amount').html(numFormat($j('#first_rate option:selected').val()));
                    return;
                }
            }
            else {
                alert('Invalid multiplier.');
                $j('#first_rate_amount').html(numFormat($j('#first_rate option:selected').val()));
                $j('#first_multiplier').val($j('#first_rate option:selected').attr('multiplier'));
                return;
            }
        }
        else {
            alert('Invalid multiplier.');
            $j('#first_multiplier').val($j('#first_rate option:selected').attr('multiplier'));
            $j('#first_rate_amount').html(numFormat($j('#first_rate option:selected').val()));
            return;
        }
    }
    
    if(caserate == '2') {
        if(parseInt(second_multiplier, 10) <= parseInt(orig_second_multi, 10)){
            if(second_multiplier.match(regExp) && second_multiplier != 0) {
                data.multiplier = second_multiplier;
                data.package_id = $j('#second_rate option:selected').attr('id');

                if(second_multiplier == orig_second_multi){
                    var orig_amnt = $j('#second_rate option:selected').attr('orig_amntSecond');
                    var orig_hf = $j('#second_rate option:selected').attr('orig_hf');
                    var orig_pf = $j('#second_rate option:selected').attr('orig_pf');
                    
                    var base_amnt = orig_amnt/orig_second_multi;
                    var base_hf = orig_hf/orig_second_multi;
                    var base_pf = orig_pf/orig_second_multi;

                    var new_amnt = base_amnt*second_multiplier;
                    HF2 = base_hf*second_multiplier;
                    PF2 = base_pf*second_multiplier;

                    data.amount = new_amnt;

                    second_D3 = PF2 * (0.60);
                    second_D4 = PF2 * (0.40);

                    $j('#second_rate_amount').html(numFormat(new_amnt));
                    $j('#second_rate option:selected').attr('value_hf', HF2);
                    $j('#second_rate option:selected').attr('value_pf', PF2);
                    $j('#second_rate option:selected').attr('value_D3', second_D3);
                    $j('#second_rate option:selected').attr('value_D4', second_D4);
                    $j('#second_rate option:selected').attr('multiplier', second_multiplier);
                    $j('#second_rate option:selected').val(new_amnt);

                    secondMultiplier = second_multiplier;

                    $j('#second_multiplier_2').attr('value_hf', HF2);
                    $j('#second_multiplier_2').attr('value_pf', PF2);

                    xajax_caserateModificationHistory(data);
                }
        
                //added by Carriane 07/19/17
                if(second_multiplier < orig_second_multi){
                    var src = "<div style='background:white;'><br><br><h1 style='color:FF0505;font-size:30px;'><center>Multiplier entered in second caserate is below maximum. Do you still want to proceed?</center</h1>"+ "</div>";

                    $j(src).dialog({
                        dialogClass: 'no-close',
                        autoOpen: true,
                        modal:true,
                        height: "auto",
                        width: "30%",
                        resizable: false,
                        show: "fade",
                        hide: "explode",
                        title: "Warning!",
                        position: "top", 
                        buttons: {
                            "YES": function() 
                            {
                                var orig_amnt = $j('#second_rate option:selected').attr('orig_amntSecond');
                                var orig_hf = $j('#second_rate option:selected').attr('orig_hf');
                                var orig_pf = $j('#second_rate option:selected').attr('orig_pf');
                                
                                var base_amnt = orig_amnt/orig_second_multi;
                                var base_hf = orig_hf/orig_second_multi;
                                var base_pf = orig_pf/orig_second_multi;

                                var new_amnt = base_amnt*second_multiplier;
                                HF2 = base_hf*second_multiplier;
                                PF2 = base_pf*second_multiplier;

                                data.amount = new_amnt;

                                second_D3 = PF2 * (0.60);
                                second_D4 = PF2 * (0.40);

                                $j('#second_rate_amount').html(numFormat(new_amnt));
                                $j('#second_rate option:selected').attr('value_hf', HF2);
                                $j('#second_rate option:selected').attr('value_pf', PF2);
                                $j('#second_rate option:selected').attr('value_D3', second_D3);
                                $j('#second_rate option:selected').attr('value_D4', second_D4);
                                $j('#second_rate option:selected').attr('multiplier', second_multiplier);
                                $j('#second_rate option:selected').val(new_amnt);

                                secondMultiplier = second_multiplier;

                                $j('#second_multiplier_2').attr('value_hf', HF2);
                                $j('#second_multiplier_2').attr('value_pf', PF2);

                                xajax_caserateModificationHistory(data);
                                
                                $j(this).dialog("close");
                            },
                            "NO": function(){
                                $j('#second_multiplier').val($j('#second_rate option:selected').attr('multiplier'));
                                $j('#second_rate_amount').html(numFormat($j('#second_rate option:selected').val()));
                                
                                $j(this).dialog("close");
                            }
                        },
                    });
                }
                //end Carriane

                if(second_multiplier <= 0) {
                    alert('Invalid multiplier.');
                    $j('#second_multiplier').val($j('#second_rate option:selected').attr('multiplier'));
                    $j('#second_rate_amount').html(numFormat($j('#second_rate option:selected').val()));
                    return;
                }
            }
            else {
                alert('Invalid multiplier.');
                $j('#second_multiplier').val($j('#second_rate option:selected').attr('multiplier'));
                $j('#second_rate_amount').html(numFormat($j('#second_rate option:selected').val()));
                return;
            }
        }
        else {
            alert('Invalid multiplier.');
            $j('#second_multiplier').val($j('#second_rate option:selected').attr('multiplier'));
            $j('#second_rate_amount').html(numFormat($j('#second_rate option:selected').val()));
            return;
        }
    }

    first_HF = $j('#first_multiplier_2').attr('value_hf');
    first_PF = $j('#first_multiplier_2').attr('value_pf');
    second_HF = $j('#second_multiplier_2').attr('value_hf');
    second_PF = $j('#second_multiplier_2').attr('value_pf');

    HF = parseFloat(((first_HF)? first_HF : 0 )) + parseFloat(((second_HF)? second_HF : 0 ));
    PF = parseFloat(((first_PF)? first_PF : 0 )) + parseFloat(((second_PF)? second_PF : 0 ));

    totalHealthInsuranceHF = parseFloat(HF);
    totalHealthInsurancePF = parseFloat(PF);
 
    /*HIsurg = parseFloat((first_D3)? first_D3 : 0) + parseFloat((second_D3)? second_D3 : 0);
    HIanes = parseFloat((first_D4)? first_D4 : 0) + parseFloat((second_D4)? second_D4 : 0);*/

    calculateTotals();
    calculateDetails1();
    calculateDetails2();
}

/*-------------------end---------------------------Case Rates----------------------end---------------------------*/

function numFormat(num){
    var tmpNr = '';
    tmpNr = parseFloat(num).toFixed(2);
    tmpNr = tmpNr.replace(/\B(?=(\d{3})+(?!\d))/g,",");

    return tmpNr;
}

//added by borj 2014-06-01
//modified by Nick, 4/11/2014 - hide btnDelete, btnCF2Part3
function showBillingStatus(bool) {
    var elem = $('bill_status');
    var check_elem = $('chkHearingTest');

    if (bool==1) {
        isFinalBill = true;
        if((check_elem!= null)){
            $('chkHearingTest').disabled = 'disabled';
            $('lblHearingTest').disabled = 'disabled';
        }

        $('btnEditMemCat').style.display = 'none';
        $('death_date').disabled = 'disabled';

        elem.style.visibility = "";
        elem.innerHTML = "[FINAL BILLING]";
        $('isFinalBill').checked = true;

        $('btnPrevPack').disabled = "disabled";
        $('btnSave').style.display = "none";
        $j("#chkboxrow").hide();

        $('btnaccommodation').disabled = 'disabled';
        $('btnaddmisc_srvc').disabled = 'disabled';
        $('btnmedsandsupplies').disabled = 'disabled';
        $('btnOPaccommodation').disabled = 'disabled';
        $('btnaddmisc_chrg').disabled = 'disabled';
        $('btnadddoctors').disabled = 'disabled';
        $('btnadd_discount').disabled = 'disabled';
        $('first_rate').disabled = 'disabled';
        $('second_rate').disabled = 'disabled';
        $('chkHearingTest').disabled = 'disabled';
        $('first_multiplier').disabled = 'disabled';
        $('second_multiplier').disabled = 'disabled';

        //added by Nick, 2/24/2014
        $('isdied').disabled = 'disabled';
        $('btnOutMedsXLO').disabled = 'disabled';
        $('confineTypeOption').disabled = 'disabled';
        $('caseTypeOption').disabled = 'disabled';
        $('billdate_display').disabled = 'disabled';
        //end Nick

        $('opd_area').disabled = 'disabled'; // added by: syboy 08/23/2015

    }else {
        elem.innerHTML = "[NOT YET FINAL]";
        $('isFinalBill').checked = false;
    }

}
//added by borj 2014-06-01
function disabled_button(final) {
    var elem = $('bill_status');

    if (final == 1){
        $j('#btnCF2Part3').show();
        $j('#btnSave').hide();
        $j('#btnDelete').show();
        $j("#chkboxrow").hide();
        $j(".imgdelete").hide();
        $j("#btnAuditTrail").show();

        //added by art 02/21/2015
        var a = ["isdied", "death_date", "confineTypeOption", "caseTypeOption",
            "first_rate", "second_rate", "billdate_display", "overwritelimitbtn",
            "special_procedure_details :input", "lblHearingTest :input", "opd_area", "btnaccommodation",
            "btnaddmisc_srvc", "btnmedsandsupplies", "btnOPaccommodation", "btnaddmisc_chrg",
            "btnadddoctors", "btnadd_discount", "chkHearingTest"];

        a.forEach(function (entry){
            var elem = '#' + entry;
            if ($j(elem).length > 0) {
                $j(elem).prop("disabled", true);
            }
        });
        //end art

    }else{
        elem.innerHTML = "[NOT YET FINAL]";
    }
}


function js_NewBilling() {
    window.location.href="billing-main-new.php";
}

function toggleFinalBill() {

    var bill_nr = $j('#bill_nr').val();
     var pfAmount = parseFloat($j('#phic-max-PF').html().replace(/,/g,''))
    
    var src = "<div style='background:red;'><br><br><h1 style='color:#ffffff;font-size:50px;'><center>Understated amount in PF PHIC.</h1>"+                          
                                                "</div>";
    if(parseFloat(pfAmount)>1){
        $J(src).dialog({
                                        autoOpen: true,
                                        modal:true,
                                        height: "auto",
                                        width: "60%",
                                        resizable: false,
                                        show: "fade",
                                        hide: "explode",
                                        title: "Alert.",
                                        position: "top", 
                                        buttons: {
                                            "OK": function() 
                                            {
                                                 $J(this).dialog("close");
                                        }
                                    }
                                    });
         $('isFinalBill').checked = false;
         return false;
    }

    if ($('hasbloodborrowed').value=='1'){
        alert('This Patient has a pending transaction in Blood Bank. \n Please advice the patient to settle this transaction.');
        $('isFinalBill').checked = false;
        return;
    }
     
    if($('hasunpaidcps').value == '1'){
        alert('This patient has unpaid CPS transaction in Special Laboratory. \n Please advice the patient to settle this transaction.');
        $('isFinalBill').checked = false;
        return;
    }

    if($j('#phic').val().toUpperCase() != "NONE"){
        if(($('remainingDays').value <= 0) && ($('remainingDays').value) && ($('isFinalBill').checked)) {
            var finalBill = $j('#bill_status').text() == '[FINAL BILLING]' ? 1:0;

            if(bill_nr != '' && !finalBill) {
                $('bill_status').innerHTML = "[NOT YET FINAL]";
            }
            else {
                $('bill_status').style.display = "none";
            }
            
        }
    }

    if($('isFinalBill').checked){
        $('bill_status').style.display = "";
        $('bill_status').innerHTML = "[FINAL BILLING]";
        
        //added by Earl galope 02/15/2018
        disableSave();
        //end
        secondpopulateBill();
        
    }else{
        if(bill_nr != '') {
            $('bill_status').innerHTML = "[NOT YET FINAL]";
            //added by Earl Galope 02/15/2018
            disableSave();
            //end
        }
        else {
            $('bill_status').style.display = "none";
            //added by Earl Galope 02/20/2018
            disableSave();
            //end
        }
    }

    

    // checkAdmittingDiag();

    //commented by carriane 07/17/17
    // if ($j('#phic').val().toUpperCase() != "NONE") {
    //    checkAdmittingDiag();

    // }


}


//added by earl galope 02/14/2018
function disableSave() {

    var dateOfDeath, minutesTemp, getHour;
    dateOfDeath=new Date($j('#deathdate').val());
    var case_date = $j('#admission_dte').val();
    var covid_date =$j('#is_covid_valid').val().toString();
    var covid_validity = new Date(covid_date);
  
    
    var admission_date = new Date(case_date);

    var convertedDeathDate = (((dateOfDeath.getTime()/1000)/60)/60);
    var convertedAdmissionDate = (((admission_date.getTime()/1000)/60)/60);
    var substractedDates = convertedDeathDate - convertedAdmissionDate;
    // admission_date.setDate(admission_date.getDate() + 1);
    var is_Death24= (substractedDates <= 24) ? '1' : '0';
    var is_covid_valid= (covid_validity > admission_date)  ? '1' : '0';
    // var split = case_date.split(" ");
    // var getMonth = split[0];
    // var getDay = split[1];
    // var getYear = split[2];
    // var timeSplit=split[3].split(":");
    var case_type = $j("#first_rate option:selected").attr('case_type');

    var case_date_parse =  Date.parse($j('#admission_dte').val());
    var bill_date_parse = Date.parse($j('#billdate').val());
    var diff_case_bill = 0;
    var thirty_min_parse = $j('#thirty_min_parse').val()

    if(bill_date_parse > case_date_parse){
        diff_case_bill = parseInt(bill_date_parse) - parseInt(case_date_parse);
    }
    $j('#less_than_encdt').val(0);
    if(bill_date_parse < case_date_parse || (diff_case_bill < thirty_min_parse)){
        $j('#less_than_encdt').val(1);
    }
    // if(split[3].indexOf("P")){
    //     if(getHour=='12'){
    //         getHour='00';
    //     }else{
    //         getHour=parseInt(timeSplit[0],10)+12;
    //     }
    //     minutesTemp=timeSplit[1].split("P");
    // }else{
    //     getHour=timeSplit[0];
    //     minutesTemp=timeSplit[1].split("A");
    // }
    // var getMinutes = minutesTemp[0];

    // var temp = getMonth + " " + getDay + " " + getYear + " " + getHour + ":" +getMinutes;
    // var get_case_date = new Date(temp);
    // var is_Death24=(dateOfDeath.getTime()-get_case_date.getTime())/86400000;

    // alert(getHour+":"+getMinutes);
    // alert(case_date);
    // alert(dateOfDeath.getTime()-get_case_date.getTime());
    $j('#isDeath24').val(is_Death24);
    if($('isFinalBill').checked && $('isdied').checked && is_Death24 == '1' && is_covid_valid!='1' && $j('#daysCovered').val()>='1' && $j('#is24HrsPrompt').val()==""){
        // alert('disabled due to death');
        billWarningPrompt('Warnings', 'Confinement is below 24 hours.');
        if(case_type === 'm' && ($j('#encounter_type').val() == IPD_ER || $j('#encounter_type').val() == IPD_OPD)){
            
            $j('#btnSave').attr('disabled', true).addClass("ui-state-disabled");    
        }
        
        $j('#isDeathDateToggled').val('1');
    }else if($('isFinalBill').checked && $j('#is24HrsPrompt').val().indexOf('below 24 hours')>=0 && $j('#daysCovered').val()<='1'){
        // alert('disabled dut to 24 hours');
        if(case_type === 'm' && ($j('#encounter_type').val() == IPD_ER || $j('#encounter_type').val() == IPD_OPD)){
            $j('#btnSave').attr('disabled', true).addClass("ui-state-disabled");    
        }
        
        $j('#isFinalBillToggled').val('1');
    }else{
        // alert('enabled');
        $j('#btnSave').attr('disabled', false).removeClass("ui-state-disabled");
        $j('#isDeathDateToggled').val("0");
        $j('#isFinalBillToggled').val('0');
    }
            

}
//end
function statusDisplay(stat){
    if(stat == 'Other'){
            alert('Patients has no admitting diagnosis.');
                    $('isFinalBill').checked=false;
            if(bill_nr != '') {
                $('bill_status').innerHTML = "[NOT YET FINAL]";
            }
            else {
                $('bill_status').style.display = "none";
            }
            return;
    }
    else{

    }
}


function billingHeader(details)
{

    if (typeof(details)=='object') {
        var death_date = details.death_date;
        var is_final = details.is_final;
        if(death_date != '') {
            $j('#isdied').attr('checked',true);
            $j('#label_deathdate').show();
            $j('#input_deathdate').show();
            $j('#death_date').val(details.fdeath_date);
            $j('#deathdate').val(details.death_date);
        }else{
            $j('#isdied').attr('checked',false);
            $j('#label_deathdate').hide();
            $j('#input_deathdate').hide();
        }

        $j('#billdate').val(details.bill_dte);
        $j('#billdate_display').val(details.fbill_dte);
        $j('#admission_dte').val(details.bill_frmdte);
        $j('#admission_date').val(details.fbill_frmdte);
        $j('#bill_nr').val(details.bill_nr);
        $j('#opd_area').val(details.opd_type);

        if(details.hasTransmittal == 1)
            $('btnDelete').disabled = 'disabled';

        showBillingStatus(is_final);
    }
}

//added by poliam 01/04/2014
function ClassificationHeader(Class){
    if (Class){
        $('classification').innerHTML =Class;
    }else{
        $('classification').innerHTML= 'NO CLASSIFICATION ';
    }
}

function ConfinmentHeader(type){
    if (type){
         $('confine_label').style.display = "";
         $('confine_cbobox').style.display = "";
     }else{
         $('confine_label').style.display = "none";
         $('confine_cbobox').style.display = "none";
     }
}
//ended by poliam 01/04/2014

//added by ken 1/4/2013
function packageDisplay(insurance){
    if(insurance == 'PHIC'){
        
        var f_multi = "<span id=''>&nbsp;&nbsp;<input id='first_multiplier' type='text' onchange='changeMultiplier(1)' style='max-width: 30px; text-align: right'/></span>";
        var s_multi = "<span id=''>&nbsp;&nbsp;<input id='second_multiplier' type='text' onchange='changeMultiplier(2)' style='max-width: 30px; text-align: right'/></span>";

        if($j("#canUpdateCaseRateMultiplier").val() == ""){
            f_multi = "<span id=''>&nbsp;&nbsp;<input id='first_multiplier' type='text' onchange='changeMultiplier(1)' style='max-width: 30px; text-align: right' disabled/></span>";
            s_multi = "<span id=''>&nbsp;&nbsp;<input id='second_multiplier' type='text' onchange='changeMultiplier(2)' style='max-width: 30px; text-align: right' disabled/></span>";
        }

        $('td02').innerHTML = "<span id='' style='font-weight: bold;'>First Case Rate<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>: </span>"+
                                "<span id=''><select id='first_rate' name='first_rate'><option value='00.00'>-Select Code-</option></select></span>"+
                                f_multi +
                                "<span id=''>&nbsp;&nbsp;<input id='first_multiplier_2' type='hidden' style='max-width: 30px; text-align: right' value_hf='0' value_pf='0'/></span>"+
                                "<span>&nbsp;&nbsp;&nbsp;P&nbsp;</span>"+
                                "<span id='first_rate_amount'>00.00</span><br/>"+
                                "<span>&nbsp;&nbsp;</span>"+
                                "<span id='first_case_desc'>. . . .</span><br/>"+
                                "<span id='' style='font-weight: bold;'>Second Case Rate : </span>"+
                                "<span id=''><select id='second_rate' name='second_rate'><option value='00.00'>-Select Code-</option></select></span>"+
                                s_multi +
                                "<span id=''>&nbsp;&nbsp;<input id='second_multiplier_2' type='hidden' style='max-width: 30px; text-align: right' value_hf='0' value_pf='0'/></span>"+
                                "<span>&nbsp;&nbsp;&nbsp;P&nbsp;</span>"+
                                "<span id='second_rate_amount'>00.00</span><br/>"+
                                "<span>&nbsp;&nbsp;</span>"+
                                "<span id='second_case_desc'>. . . .</span><br/>"+
                                "<span>&nbsp;</span>"+
                                "<ul id='special_procedure_details' style='margin-top:0;margin-left:0;margin-bottom:10px;'></ul>"+
                                "<label id='lblHearingTest' style='display:none; cursor:pointer;'><strong>New Born with Hearing Test:</strong>&nbsp;<input id='chkHearingTest' type='checkbox'/><label>";//added by Nick, 4/21/2014
        Procedures.showSpecialProcedures();
        //jsRecomputeServices();
    }
    else{
        // $('td02').style.display = "none";
        $('td02').innerHTML = "<span id='' style='font-weight: bold;'></span>"+
                                "<span id=''></span>"+
                                "<span></span>"+
                                "<span id=''></span><br/>"+
                                "<span></span>"+
                                "<span id=''></span><br/>"+
                                "<span id='' style='font-weight: bold;'></span>"+
                                "<span id=''></span>"+
                                "<span></span>"+
                                "<span id=''></span><br/>"+
                                "<span></span>"+
                                "<span id=''></span><br/>"+
                                "<span></span>";
        //jsRecomputeServices();
    }
}

function setAccommodationObject(data){
    accommodations = data;
}

function getAccommodationTotals(){
    var acc = new Object()
    acc.charity = 0;
    acc.payward = 0;
    $j(accommodations).each(function(x,y){
        if(y.accommodation_type == 1){
            acc.charity += parseFloat(y.room_rate.replace(/,/g,'')) * y.day_stay;
        }else{
            acc.payward += parseFloat(y.room_rate.replace(/,/g,'')) * y.day_stay;
        }
    });
    return acc;
}

function hasPayWardAccommodation(){
    var hasPayWard = false;
    $j(accommodations).each(function(index,item){
        if(item.accommodation_type == 2) {
            hasPayWard = true;
            return true;
        }
    });
    return hasPayWard;
}

function isPrivateCase(){
    return $j('#confineTypeOption option:selected').val() == 1;
}

function calculateTotals()
{
    tmpHIadm = HIadm;
    tmpHIsurg = HIsurg;
    tmpHIanes = HIanes;

    if( HIadm > (PFd1+PFd2) ){
        tmpHIadm = PFd1+PFd2;
    }

    if( HIanes > PFd4 ){
        tmpHIanes = PFd4;
    }

    if( HIanes>PFd4 ){
        tmpHIsurg += (HIanes - PFd4);
    }

    if( tmpHIsurg > PFd3 ){
        tmpHIsurg = PFd3;
    }

    //totalHealthInsurancePF = tmpHIadm + tmpHIsurg + tmpHIanes;
    TotalAutoExcess = parseFloat(accexcess) + parseFloat(TotalUnsed)/* + parseFloat(TotalMiscAutoExcess)*/;
    totalHCI = acc_computed+miscServices_computed+med_computed+ops_computed+miscCharges_computed;

    total_pf_discount = parseFloat($j('#pfDiscount').html().replace(/,/g,''));
    HealthInsurancePF = parseFloat($j('#pfHC').html().replace(/,/g,''));
    total_pf_excess = totalPF - (total_pf_discount + HealthInsurancePF);

    $('hiTotal').innerHTML = numFormat(totalHCI);
    $('pfAP').innerHTML = numFormat(totalPF);
    $('pfEX').innerHTML = numFormat(total_pf_excess);

    isphic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true;
    acc_appDiscount = 0;
    xlo_appDiscount = 0;
    med_appDiscount = 0;
    ops_appDiscount = 0;
    //pfs_appDiscount = 0;
    msc_appDiscount = 0;

    acc_appDiscount = acc_computed * total_applied_discount;
    xlo_appDiscount = miscServices_computed * total_applied_discount;
    med_appDiscount = med_computed * total_applied_discount;
    ops_appDiscount = ops_computed * total_applied_discount;
    //pfs_appDiscount = totalPF * total_applied_discount;
    msc_appDiscount = miscCharges_computed * total_applied_discount;

    temp_serv_phic = 0;
    temp_pf_phic = 0;

    if(!isphic && isPAYWARD()){//none-phic, payward

        total_serv_discount = (acc_appDiscount + xlo_appDiscount + med_appDiscount + ops_appDiscount + msc_appDiscount);
        totalHealthInsuranceHF = 0;

        total_serv_excess = totalHCI - total_serv_discount;

        if(total_serv_discount < 0){
            total_serv_excess = (totalHCI - totalHealthInsuranceHF) + Math.abs(total_serv_discount);
            total_serv_discount = 0;
        }

        $('hiDiscount').innerHTML = numFormat(total_serv_discount);
        $('hiHIC').innerHTML = numFormat(totalHealthInsuranceHF);
        $('hiEX').innerHTML = numFormat(total_serv_excess);

    }else if(!isphic && !isPAYWARD()){//none-phic, charity
        total_serv_discount = ( ((isMedicolegal) ? acc_appDiscount : getAccommodationTotals().charity) + xlo_appDiscount + med_appDiscount + ops_appDiscount + msc_appDiscount) - TotalAutoExcess;
        totalHealthInsuranceHF = 0;

        total_serv_excess = totalHCI - total_serv_discount;

        if(total_serv_discount < 0){
            total_serv_excess = (totalHCI - totalHealthInsuranceHF) + Math.abs(total_serv_discount);
            total_serv_discount = 0;
        }

        $('hiDiscount').innerHTML = numFormat(total_serv_discount);
        $('hiHIC').innerHTML = numFormat(totalHealthInsuranceHF);
        $('hiEX').innerHTML = numFormat(total_serv_excess);


    }else if(isphic && isPAYWARD() && removeNBBCharity()){//with phic, payward

        total_serv_discount = (acc_appDiscount + xlo_appDiscount + med_appDiscount + ops_appDiscount + msc_appDiscount);
        total_serv_excess = totalHCI - (total_serv_discount + totalHealthInsuranceHF);

        if(total_serv_discount < 0){
            total_serv_excess = (totalHCI - totalHealthInsuranceHF) + Math.abs(total_serv_discount);
            total_serv_discount = 0;
        }

        serv_excess_negative = false;

        if(total_serv_excess < 0){
            serv_excess_negative = true;
            temp_serv_phic = totalHealthInsuranceHF + total_serv_excess;
            total_serv_excess = 0;
        }

        $('hiHIC').innerHTML = numFormat((serv_excess_negative) ? temp_serv_phic:(totalHealthInsuranceHF));
        $('hiDiscount').innerHTML = numFormat(total_serv_discount);
        $('hiEX').innerHTML = numFormat(total_serv_excess);


    }else if(isphic && isPAYWARD() && !removeNBBCharity()){//with phic, payward

        total_serv_discount = (acc_appDiscount + xlo_appDiscount + med_appDiscount + ops_appDiscount + msc_appDiscount);
        total_serv_excess = totalHCI - (total_serv_discount + totalHealthInsuranceHF);

        if(total_serv_discount < 0){
            total_serv_excess = (totalHCI - totalHealthInsuranceHF) + Math.abs(total_serv_discount);
            total_serv_discount = 0;
        }

        serv_excess_negative = false;

        if(total_serv_excess < 0){
            serv_excess_negative = true;
            temp_serv_phic = totalHealthInsuranceHF + total_serv_excess;
            total_serv_excess = 0;
        }

        $('hiHIC').innerHTML = numFormat((serv_excess_negative) ? temp_serv_phic:(totalHealthInsuranceHF));
        $('hiDiscount').innerHTML = numFormat(total_serv_discount);
        $('hiEX').innerHTML = numFormat(total_serv_excess);


    }else if(isphic && !isPAYWARD()){//with phic, charity

        total_serv_discount = (acc_appDiscount + xlo_appDiscount + med_appDiscount + ops_appDiscount + msc_appDiscount) /*- accexcess*/;
        total_serv_excess = totalHCI - (total_serv_discount + totalHealthInsuranceHF);

        if(total_serv_discount < 0){
            total_serv_excess = (totalHCI - totalHealthInsuranceHF) + Math.abs(total_serv_discount);
            total_serv_discount = 0;
        }


        serv_excess_negative = false;
        pf_excess_negative = false;

        if(total_serv_excess < 0){
            serv_excess_negative = true;
            temp_serv_phic = totalHealthInsuranceHF + total_serv_excess;
            total_serv_excess = 0;
        }

        $('hiHIC').innerHTML = numFormat(((serv_excess_negative) ? temp_serv_phic:totalHealthInsuranceHF));
        $('hiDiscount').innerHTML = numFormat(total_serv_discount);
        $('hiEX').innerHTML = numFormat(total_serv_excess);

    }

    final_discount = total_serv_discount + total_pf_discount;
    final_net_amount = total_serv_excess + total_pf_excess - deposit;
    final_phic = parseFloat($j('#hiHIC').html().replace(/,/g,'')) + parseFloat($j('#pfHC').html().replace(/,/g,''));
    totalGross =  totalHCI + totalPF;
    $j('#save_total_prevpayment').val(deposit);

    $('netbill').innerHTML = numFormat(totalGross);
    $('HealthInsuranceTotal').innerHTML = numFormat(final_phic);
    $('DiscountTotal').innerHTML = numFormat(final_discount);
    $('netamnt').innerHTML = numFormat(final_net_amount);

    $j('#amntlabel_discount').html("Total Discount : ");

    //if (isSponsoredMember() || isHSM() || isPHS() || isInfirmaryOrDependent.trim() != '') {
    //
    //    if (isSponsoredMember() && !isPAYWARD()) {
    //        $j('#amntlabel_discount').html("Total Discount : (NBB)");
    //        $j('#DiscountTotal').html(numFormat(final_net_amount + final_discount));
    //        $j('#netamnt').html('0.00');
    //        $j('#save_discountid').val('NBB');
    //        $j('#save_discount').val('0');
    //        $j('#save_discount_amnt').val(final_net_amount + final_discount);
    //    } else if(isHSM() && !isPAYWARD()) {
    //        $j('#amntlabel_discount').html("Total Discount : (HSM)");
    //        $j('#DiscountTotal').html(numFormat(final_net_amount + final_discount));
    //        $j('#netamnt').html('0.00');
    //        $j('#save_discountid').val('HSM');
    //        $j('#save_discount').val('0');
    //        $j('#save_discount_amnt').val(final_net_amount + final_discount);
    //    } else if(isPHS() || isInfirmaryOrDependent.trim() != ''){
    //
    //        //updated by Nick, 4/8/2014 - fix infirmary/infirmary-dependent patients - sei7
    //        if(isInfirmaryOrDependent.trim() != ''){
    //            $j('#amntlabel_discount').html("Total Discount : <strong>(" + isInfirmaryOrDependent.toUpperCase() + ')</strong>');
    //            if(isInfirmaryOrDependent.trim().toUpperCase() == 'INFIRMARY'){
    //                temp_discount = total_serv_excess + total_pf_excess + final_discount;
    //            }else if(isInfirmaryOrDependent.trim().toUpperCase() == 'DEPENDENT'){
    //                temp_discount = total_serv_excess + final_discount;
    //            }
    //        }
    //
    //        var net = parseFloat($j('#netbill').html().replace(',',''));
    //        var coverage = parseFloat($j('#HealthInsuranceTotal').html().replace(',',''));
    //        //alert(net +" = "+temp_discount+" = "+discount);
    //        $j('#DiscountTotal').html(numFormat(temp_discount));
    //        $j('#netamnt').html(numFormat(parseFloat(net) - (parseFloat(coverage) + parseFloat(temp_discount) + parseFloat(deposit)) ));
    //        $j('#save_discountid').val('Inf');
    //        $j('#save_discount').val('0');
    //        $j('#save_discount_amnt').val(temp_discount);
    //        //end Nick
    //    }
    //}

    var discountId, discountAmount;

    // updated by Nick 4-23-2015, updated by Gervie 03-19-2017
    if(isNbb() && hasHighFlux()){
        displayTotals();
    }else if(isPHS() || isInfirmaryOrDependent.trim() != ''){
        if(isInfirmaryOrDependent.trim().toUpperCase() == 'INFIRMARY'){
            $j('#amntlabel_discount').html("Total Discount : <strong>(" + isInfirmaryOrDependent.toUpperCase() + ')</strong>');
            temp_discount = total_serv_excess + total_pf_excess + final_discount;
            discountId = 'infirmary';
            discountAmount = total_serv_excess + total_pf_excess;
        }else if(isInfirmaryOrDependent.trim().toUpperCase() == 'DEPENDENT'){
            $j('#amntlabel_discount').html("Total Discount : <strong>(" + isInfirmaryOrDependent.toUpperCase() + ')</strong>');
            temp_discount = total_serv_excess + final_discount;
            discountId = 'dependent';
            discountAmount = total_serv_excess;
        }
        var net = parseFloat($j('#netbill').html().replace(/,/g,''));
        var coverage = parseFloat($j('#HealthInsuranceTotal').html().replace(/,/g,''));
        $j('#DiscountTotal').html(numFormat(temp_discount));
        $j('#netamnt').html(numFormat(parseFloat(net) - (parseFloat(coverage) + parseFloat(temp_discount) + parseFloat(deposit)) ));
        $j('#save_discountid').val('Inf');
        $j('#save_discount').val('0');
        $j('#save_discount_amnt').val(temp_discount);
        $j('#save_discount_credit_collection').val(discountAmount);
        $j('#save_discountid_credit_collection').val(discountId);
    } else {
        $j('#save_discountid').val('');
        $j('#save_discount').val('0');
        $j('#save_discount_amnt').val('');
        $j('#save_discount_credit_collection').val('');
        $j('#save_discountid_credit_collection').val('');
    }
    disableImgDelete();
}

function isPhilHealth() {
    return $j('#phic').val().toUpperCase() != "NONE";
}

/**
 * @author Nick 4-23-2015
 */
function memberInfo(){
    var memberId = $j('#memcategory_id').val();
    switch (memberId){
        case PHS    : return 'PHS';    break;
        case NBB    : return 'NBB';    break;
        case HSM    : return 'HSM';    break;
        case KSMBHY : return 'KSMBHY'; break;
        case LM     : return 'LM';     break;
        case SC     : return 'SC';     break;
        case POS    : return 'POS';    break;
    }
}

/**
 * @author Nick 4-23-2015
 */
function displayTotals(){

    var discount = 0;
    if(final_net_amount > 0){
        discount = final_net_amount;
    }else{
        discount = 0;
    }

    $j('#amntlabel_discount').html("Total Discount : (NBB)");
    $j('#DiscountTotal').html(numFormat(discount + final_discount));
    $j('#netamnt').html(numFormat(final_net_amount - discount));
    $j('#save_discountid').val(memberInfo());
    $j('#save_discount').val('0');
    $j('#save_discount_amnt').val(discount + final_discount);
    $j('#save_discount_credit_collection').val(discount);
    $j('#save_discountid_credit_collection').val('nbb');
}

//updated by Nick 2/8/2014
function isPAYWARD()
{

    // added by: syboy 08/23/2015
    if ($j('#ptype').val() == 2) {
        if ($j('#opd_area option:selected').attr('data-atype') == PAY) {
            return true;
        }else if ($j('#opd_area option:selected').attr('data-atype') == SERVICE) {
            return false;
        }else {
            return false;
        }
    }
    // end

    if($j('#accomodation_type').val() == 1 || $j('#accomodation_type').val() == 0)
        return false;
    else
        return true;
}

/*
 * added by: syboy 01/22/2016 : meow
 * Remove NBB if patient is select HI and ASU options in opd area
 */ 
function removeNBBCharity(){
    if ($j('#ptype').val() == 2) {
        if ($j('#opd_area option:selected').attr('data-atype') == PAY) {
            return true;
        }else if ($j('#opd_area option:selected').attr('data-atype') == SERVICE) {
            return false;
        }else {
            return false;
        }
    }
}   

function isPHS()
{
    return PHS;
}

function isSponsoredMember()
{
    return $j('#memcategory_id').val() == NBB;
}

function isHSM()
{
    return $j('#memcategory_id').val() == HSM;
}

// added by Robert, 04/21/2015
function isKasambahay()
{
    return $j('#memcategory_id').val() == KSMBHY;
}

function isLifetimeMember()
{
    return $j('#memcategory_id').val() == LM;
}

function isSeniorCitizen()
{
    return $j('#memcategory_id').val() == SC;
}

function isPointOfService()
{
    return $j('#memcategory_id').val() == POS;
}

function isNbb(){
    return /*!isPAYWARD()*/ isPhilHealth() && (!hasPayWardAccommodation() && !removeNBBCharity()) && !isPrivateCase() && (((isKasambahay() || isLifetimeMember() ||
        isSeniorCitizen() || isPointOfService()) && getCaseDate()) || isSponsoredMember() || isHSM());
}

function getCaseDate()
{
    var case_date = $j('#admission_date').val();
    var split = case_date.split(" ");
    var getMonth = split[0];
    var getDay = split[1];
    var getYear = split[2];
    var temp = getMonth + " " + getDay + " " + getYear;
    var get_case_date = new Date(temp),
        nbb_date = new Date('Apr 20, 2015');

    return get_case_date >=  nbb_date;
}
//end add by Robert
function checkAdmittingDiag(){
var enc = $j('#encounter_nr').val();
    xajax_getAdmittingDiag(enc);
}

function getBillNr(){
    var data = new Object();
    data.encounter = $j('#encounter_nr').val();
    xajax_setBillNr(data);
    //console.log(bill_nr);
}

function setBillNr(nr){
    bill_nr = nr;
    $j('#bill_nr').val(nr);
}

// Added by Gervie 03/31/2016
function setBillStarted(date_start){
    $j('#bill_time_started').val(date_start);
}

function js_btnHandler(){
    getBillNr();
}


function showCF2Part3()
{
    var rpath = $('rpath').value;
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_nr = $('bill_nr').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');

    urlholder = rpath+'modules/billing_new/billing-cf2-part3.php'+seg_URL_APPEND+
                '&pid='+pid+'&encounter_nr='+enc+'&bill_nr='+bill_nr;

    nleft = (screen.width - 680)/2;
    ntop = (screen.height - 520)/2;
     printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);


}

function showSoa(){
    if(!bill_nr || bill_nr==""){
        alert("This bill has not been saved yet!");
        return;
    }
    //process_type = 'print';
    //$j('#btnSave').click();

    var rpath = $('rpath').value;
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_dt = $('billdate').value;
    //var bill_nr = $('old_bill_nr').value;
    var frm_dte = $('admission_dte').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');

    if($('isdied').checked){
        var deathdate = $j('#deathdate').val();
    }else{
        var deathdate = '';
    }


        if (bill_nr == '') {
                var is_finalbill = ($('isFinalBill').checked) ? 1 : 0;
                if (is_finalbill) {
                    alert("This bill has not been saved yet! Please SAVE this final bill before printing!");
                    return;
                }
        }
        var detailed;
      
        if ($('IsDetailed').checked)
            detailed = 1;
           
        else
            detailed = 0;
        ///modules/billing_new/bill-pdf-summary.php
       urlholder = rpath+'modules/billing_new/SOA_versioning.php'+seg_URL_APPEND+
                          '&pid='+pid+'&encounter_nr='+enc+
                          '&from_dt='+(getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000)+
                          '&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+
                          '&nr='+bill_nr+'&IsDetailed='+detailed+'&deathdate='+deathdate;

        nleft = (screen.width - 680)/2;
        ntop = (screen.height - 520)/2;
        if(enc != "" && pid != "")
        {
            if (/*ld_computed &&*/ (acc_computed!=null) && (med_computed!=null) && /*(xlo_computed!=null) &&*/ (ops_computed!=null) /*&& (pfs_computed!=null) &&*/ /*(msc_computed!=null)*/)
                    printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
            else
                alert('Please wait for the billing calculation to complete!');
        }else{
            alert("Please specify patient!");
        }

        return true;
    }
//end nick

function assigPHS(bool)
{
    PHS = bool;
}

function setTotalDiscounts(data){
    total_applied_discount = data;
}

function jsOnchangeCaseType() {
    var enc = $j('#encounter_nr').val();
    var type = $('caseTypeOption').options[$('caseTypeOption').selectedIndex].value
    var bill_dte = $j('#billdate').val();
    var mem_category = $j.trim($j('#mcategdesc').html());
    var create_id =  $j('#classify_id').val();

    if(enc != '') {
        if (!isNbb() && type==7) {
            alert('NBB Policy is only applicable to the following Member Category : \n* Sponsored Member\n* Hospital Sponsored Mamber\n* Lifetime Member\n* Kasam-bahay\n* Senior Citizen');
            $('caseTypeOption').selectedIndex = 0;
        }else if(mem_category!=HSM_desc && type==8) {
            alert('Please change Member Category first to HOSPITAL SPONSORED MEMBER before selecting this case type');
             $('caseTypeOption').selectedIndex = 0;
        }else if(type == ""){
            $('caseTypeOption').selectedIndex = 0;
        }else{
            xajax_setCaseType(enc, type, create_id, bill_dte);
        }

    } else {
        alert('Please select patient first');
    }
}

$j("#caseTypeOption").on('load',function(){
    alert('load');
});

// added  by: syboy 08/19/2015
function jsonChangeOpdArea(){
    var enc = $j('#encounter_nr').val();
    var opdarea = $j('#opd_area').val();

    if (enc != '') {
        if (opdarea == 0) {
            alert('Please select OPD Area!');
        }else {
            populateBill();
        }
       
    } else {
        alert('Please select patient first');
    }
}

function setHasBloodTrans(bool)
{
    $j('#hasbloodborrowed').val(bool);
}

/* added by gervie 07/21/2015 */
function setHasUnpaidCps(bool)
{
    $j('#hasunpaidcps').val(bool);
}
/* end gervie */


/*function assignDrDetails(details)
{
    isphic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true;
    if (typeof(details)=='object') {
        if (details.area == 'D1' && D1_nr == '') {
            D1_nr = details.dr_nr;
            D1_chrg = details.charge;
        }else if (details.area == 'D2' && D2_nr == '') {
            D2_nr = details.dr_nr;
            D2_chrg = details.charge;
        }else if (details.area == 'D3' && D3_nr == '') {
            D3_nr = details.dr_nr;
            D3_chrg = details.charge;
        }
        else if (details.area == 'D4' && D4_nr == '') {
            D4_nr = details.dr_nr;
            D4_chrg = details.charge;
        }

        var total_doc_Charge = details.totalCharge.replace(',','');
        if(details.area=='D1'){
           if(!isphic && !isPAYWARD()){
                D1_discount = total_doc_Charge
           }else if(total_applied_discount != ''){
                D1_discount = total_doc_Charge * total_applied_discount;
           }else{
                D1_discount = 0;
           }
        }else if(details.area == 'D2'){
            if(!isphic && !isPAYWARD()){
                D2_discount = total_doc_Charge;
           }else if(total_applied_discount != ''){
                D2_discount = total_doc_Charge * total_applied_discount;
           }else{
                D2_discount = 0;
           }
        }else if(details.area == 'D3'){
            if(!isphic && !isPAYWARD()){
                D3_discount = total_doc_Charge;
           }else if(total_applied_discount != ''){
                D3_discount = total_doc_Charge * total_applied_discount;
           }else{
                D3_discount = 0;
           }
        }else if(details.area == 'D4'){
            if(!isphic && !isPAYWARD()){
                D4_discount = total_doc_Charge;
           }else if(total_applied_discount != ''){
                D4_discount = total_doc_Charge * total_applied_discount;
           }else{
                D4_discount = 0;
           }
        }
   }
}*/


/*function clearDocVars()
{
    D1_nr = '';
    D1_chrg = '';
    D2_nr = '';
    D2_chrg = '';
    D3_nr = '';
    D3_chrg = '';
    D4_nr = '';
    D4_chrg = '';
    D1_discount = 0;
    D2_discount = 0;
    D3_discount = 0;
    D4_discount = 0;
}
*/

function setUnusedAmount(amnt)
{
    isphic = ($j('#phic').val().toUpperCase()=="NONE") ? false:true;
    if(isphic){
        TotalUnsed = amnt;
   }else{
        TotalUnsed = 0;
   }

}

function jsDoctorsCoverage(tblId,data)
{
    $(tblId).innerHTML += data;
}


function calculateDetails1()
{
    var total_doc_Charge = 0,total_doc_discount = 0, area, hcare_id ='', temp_id = '',
        total_excess_x = 0, total_excess_y = 0, phic_coverage=0;
    var hcare_amount = new Array();
    var charge_field = $j('.calc-actual').serializeArray();
    var discount_field = $j('.calc-discount').serializeArray();
    var excess_field_x = $j('.calc-excess').serializeArray();
    var hcare_field = $j('.calc-hcare1').serializeArray();
        D1_discount1 = 0;
        D2_discount1 = 0;
        D3_discount1 = 0;
        D4_discount1 = 0;

        D1_coverage1 = 0;
        D2_coverage1 = 0;
        D3_coverage1 = 0;
        D4_coverage1 = 0;

    $j.each(charge_field, function(i, field){
        var str = field.name.split("_");
        var charge = field.value;
        var dr_nr = str[2];
        var role = str[3]
        var temp_excess = 0;
        total_doc_Charge += parseFloat(field.value);

        $j.each(excess_field_x, function(i, field_excess){
            var str2 =  field_excess.name.split("_");
            if(dr_nr == str2[2] && role == str2[3])
                temp_excess += parseFloat(field_excess.value);
        });

        total_excess_x = charge - temp_excess;
        total_excess_y += total_excess_x;
        $j('#total-excess-'+dr_nr+'-'+role).html(numFormat(total_excess_x));

    });

    $j.each(discount_field, function(i, field){
        total_doc_discount += parseFloat(field.value);
        area = $j('#'+field.name).attr('area');
        if(area=="D1"){
            D1_discount1 += parseFloat(field.value);
        }else if(area=="D2"){
            D2_discount1 += parseFloat(field.value);
        }else if(area=="D3"){
            D3_discount1 += parseFloat(field.value);
        }else if(area=="D4"){
            D4_discount1 += parseFloat(field.value);
        }
    });

    $j.each(hcare_field, function(i, field){
        hcare_id = $j('#'+field.name).attr('hcareid');
        $j('#total1_'+hcare_id).html(0);
    });

    $j.each(hcare_field, function(i, field){
        hcare_id = $j('#'+field.name).attr('hcareid');
        area = $j('#'+field.name).attr('itemcode');
        $j('#total1_'+hcare_id).html(numFormat(parseFloat($j('#total1_'+hcare_id).html().replace(/,/g,'')) + parseFloat(field.value)));
        if(hcare_id=='18'){
            phic_coverage +=  parseFloat(field.value);
            if(area=="D1"){
                D1_coverage1 += parseFloat(field.value);
            }else if(area=="D2"){
                D2_coverage1 += parseFloat(field.value);
            }else if(area=="D3"){
                D3_coverage1+= parseFloat(field.value);
            }else if(area=="D4"){
                D4_coverage1 += parseFloat(field.value);
            }
        }


    });

    setCoverageLimit1();
    // var ss =  $j('#phic-max-PF').html(numFormat(parseFloat($j('#phic-max-PF').html().replace(/,/g,''))- phic_coverage));
    // $j('#first_total').html(numFormat(parseFloat($j('#first_total').html().replace(/,/g,''))- phic_coverage));
    $j('#phic-max-PF').html(numFormat(parseFloat($j('#first_total').html().replace(/,/g,''))+parseFloat($j('#second_total').html().replace(/,/g,''))));
    // var getPFremaining = parseFloat($j('#phic-max-PF').html().replace(/,/g,''))
    $j('#pfamount1').val(parseFloat($j('#phic-max-PF').html().replace(/,/g,'')));
    $j('#total-excess').html(numFormat(total_excess_y));
    $j('#total-charges').html(numFormat(total_doc_Charge));
    $j('#total-discount').html(numFormat(total_doc_discount));

}

function calculateDetails2()
{
    var total_doc_Charge = 0,total_doc_discount = 0, area, hcare_id ='', temp_id = '',
        total_excess_x = 0, total_excess_y = 0, phic_coverage=0;
    var hcare_amount = new Array();
    var charge_field = $j('.calc-actual').serializeArray();
    var discount_field = $j('.calc-discount').serializeArray();
    var excess_field_x = $j('.calc-excess').serializeArray();
    var hcare_field = $j('.calc-hcare2').serializeArray();
        D1_discount2 = 0;
        D2_discount2 = 0;
        D3_discount2 = 0;
        D4_discount2 = 0;

        D1_coverage2 = 0;
        D2_coverage2 = 0;
        D3_coverage2 = 0;
        D4_coverage2 = 0;

    $j.each(charge_field, function(i, field){
        var str = field.name.split("_");
        var charge = field.value;
        var dr_nr = str[2];
        var role = str[3]
        var temp_excess = 0;
        total_doc_Charge += parseFloat(field.value);

        $j.each(excess_field_x, function(i, field_excess){
            var str2 =  field_excess.name.split("_");
            if(dr_nr == str2[2] && role == str2[3])
                temp_excess += parseFloat(field_excess.value);
        });

        total_excess_x = charge - temp_excess;
        total_excess_y += total_excess_x;
        $j('#total-excess-'+dr_nr+'-'+role).html(numFormat(total_excess_x));

    });

    $j.each(discount_field, function(i, field){
        total_doc_discount += parseFloat(field.value);
        area = $j('#'+field.name).attr('area');
        if(area=="D1"){
            D1_discount2 += parseFloat(field.value);
        }else if(area=="D2"){
            D2_discount2 += parseFloat(field.value);
        }else if(area=="D3"){
            D3_discount2 += parseFloat(field.value);
        }else if(area=="D4"){
            D4_discount2 += parseFloat(field.value);
        }
    });

    $j.each(hcare_field, function(i, field){
        hcare_id = $j('#'+field.name).attr('hcareid');
        $j('#total2_'+hcare_id).html(0);
    });

    $j.each(hcare_field, function(i, field){
        hcare_id = $j('#'+field.name).attr('hcareid');
        area = $j('#'+field.name).attr('itemcode');
        $j('#total2_'+hcare_id).html(numFormat(parseFloat($j('#total2_'+hcare_id).html().replace(/,/g,'')) + parseFloat(field.value)));
        if(hcare_id=='18'){
            phic_coverage +=  parseFloat(field.value);
            if(area=="D1"){
                D1_coverage2 += parseFloat(field.value);
            }else if(area=="D2"){
                D2_coverage2 += parseFloat(field.value);
            }else if(area=="D3"){
                D3_coverage2+= parseFloat(field.value);
            }else if(area=="D4"){
                D4_coverage2 += parseFloat(field.value);
            }
        }

    });

    setCoverageLimit2();
    // var ss =  $j('#phic-max-PF').html(numFormat(parseFloat($j('#phic-max-PF').html().replace(/,/g,''))- phic_coverage));
    // $j('#second_total').html(numFormat(parseFloat($j('#second_total').html().replace(/,/g,''))- phic_coverage));
    $j('#phic-max-PF').html(numFormat(parseFloat($j('#first_total').html().replace(/,/g,''))+parseFloat($j('#second_total').html().replace(/,/g,''))));
    // var getPFremaining = parseFloat($j('#phic-max-PF').html().replace(/,/g,''))
    $j('#pfamount2').val(parseFloat($j('#phic-max-PF').html().replace(/,/g,'')));
    $j('#total-excess').html(numFormat(total_excess_y));
    $j('#total-charges').html(numFormat(total_doc_Charge));
    $j('#total-discount').html(numFormat(total_doc_discount));
}

function applyAllCoverage_1(hcare_id,dr_nr,area)
{
    if(isNaN($j('#coverage1_18_'+dr_nr+'_'+area).val()) || $j('#coverage1_18_'+dr_nr+'_'+area).val()==""){
        $j('#coverage1_18_'+dr_nr+'_'+area).val(numFormat(0));
    }
    var is_apply = $('apply1_'+hcare_id+'_'+dr_nr+'_'+area).checked;
    var dr_charge = parseFloat($j('#doc_charge_'+dr_nr+'_'+area).val());
    var dr_discount = parseFloat($j('#doc_discount_'+dr_nr+'_'+area).val());
    var dr_firstcase = parseFloat($j('#coverage1_18_'+dr_nr+'_'+area).val());
    var dr_secondcase = parseFloat($j('#coverage2_18_'+dr_nr+'_'+area).val());
    var phic_max = parseFloat($j('#first_total').html().replace(/,/g,''));
    var temp_total = dr_charge - dr_discount - dr_secondcase;
    var total_surgeon = parseFloat($j('#D3_total').html().replace(/,/g,''));
    var total_anes = parseFloat($j('#D4_total').html().replace(/,/g,''));
    if(dr_charge==total_surgeon){
        phic_max=parseFloat($j('#D3_first').html().replace(/,/g,''));
    }else if(dr_charge==total_anes){
        phic_max=parseFloat($j('#D4_first').html().replace(/,/g,''));
    }
    if(is_apply && (temp_total > phic_max)){
        $j('#coverage1_'+hcare_id+'_'+dr_nr+'_'+area).val(phic_max);
    }else if(is_apply && (temp_total <= dr_charge)){
        $j('#coverage1_'+hcare_id+'_'+dr_nr+'_'+area).val(temp_total);
    }else{
        $j('#coverage1_'+hcare_id+'_'+dr_nr+'_'+area).val(numFormat(0));
    }
    calculateDetails1()

}

function applyAllCoverage_2(hcare_id,dr_nr,area)
{
    if(isNaN($j('#coverage2_18_'+dr_nr+'_'+area).val()) || $j('#coverage2_18_'+dr_nr+'_'+area).val()==""){
        $j('#coverage2_18_'+dr_nr+'_'+area).val(numFormat(0));
    }
    var is_apply = $('apply2_'+hcare_id+'_'+dr_nr+'_'+area).checked;
    var dr_charge = parseFloat($j('#doc_charge_'+dr_nr+'_'+area).val());
    var dr_discount = parseFloat($j('#doc_discount_'+dr_nr+'_'+area).val());
    var dr_firstcase = parseFloat($j('#coverage1_18_'+dr_nr+'_'+area).val());
    var dr_secondcase = parseFloat($j('#coverage2_18_'+dr_nr+'_'+area).val());
    var phic_max = parseFloat($j('#second_total').html().replace(/,/g,''));
    var temp_total = dr_charge - dr_discount - dr_firstcase;
    var total_surgeon = parseFloat($j('#D3_total').html().replace(/,/g,''));
    var total_anes = parseFloat($j('#D4_total').html().replace(/,/g,''));
    if(dr_charge==total_surgeon){
        phic_max=parseFloat($j('#D3_second').html().replace(/,/g,''));
    }else if(dr_charge==total_anes){
        phic_max=parseFloat($j('#D4_second').html().replace(/,/g,''));
    }
    if(is_apply && (temp_total > phic_max)){
        $j('#coverage2_'+hcare_id+'_'+dr_nr+'_'+area).val(phic_max);
    }else if(is_apply && (temp_total <= dr_charge)){
        $j('#coverage2_'+hcare_id+'_'+dr_nr+'_'+area).val(temp_total);
    }else{
        $j('#coverage2_'+hcare_id+'_'+dr_nr+'_'+area).val(numFormat(0));
    }
    calculateDetails2()

}


function setCoverageLimit1()
{
    PF1 = $j("#first_rate option:selected").attr('value_pf');
    PF2 = $j("#second_rate option:selected").attr('value_pf');
    totalHealthInsurancePF  =  parseFloat(((PF1)? PF1 : 0 )) + parseFloat(((PF2)? PF2 : 0 ));
    $j('#phic-max-PF').html(numFormat(totalHealthInsurancePF));
    if($j('#total1_18').length){
        $j('#first_total').html(numFormat(parseFloat(((PF1)? PF1 : 0 ))- parseFloat($j('#total1_18').html().replace(/,/g,''))));
    }
    
}

function setCoverageLimit2()
{
    PF1 = $j("#first_rate option:selected").attr('value_pf');
    PF2 = $j("#second_rate option:selected").attr('value_pf');
    totalHealthInsurancePF  =  parseFloat(((PF1)? PF1 : 0 )) + parseFloat(((PF2)? PF2 : 0 ));
    $j('#phic-max-PF').html(numFormat(totalHealthInsurancePF));
    if($j('#total2_18').length){
        $j('#second_total').html(numFormat(parseFloat(((PF2)? PF2 : 0 ))- parseFloat($j('#total2_18').html().replace(/,/g,''))));
    }
}


function checkNegation1(obj)
{
    if(isNaN($j('#coverage1_18_'+dr_nr+'_'+area).val()) || $j('#coverage1_18_'+dr_nr+'_'+area).val()==""){
        $j('#coverage1_18_'+dr_nr+'_'+area).val(numFormat(0));
    }
    var hcare_field = $j('.calc-hcare1').serializeArray();
    var phic_coverage = 0, val = 0;
    var str = obj.id.split("_");
    var dr_charge = parseFloat($j('#doc_charge_'+str[2]+'_'+str[3]).val());
    var dr_discount = parseFloat($j('#doc_discount_'+str[2]+'_'+str[3]).val());
    var temp_total = dr_charge - dr_discount;
    var PF1 = $j("#first_rate option:selected").attr('value_pf');

    $j.each(hcare_field, function(i, field){
        hcare_id = $j('#'+field.name).attr('hcareid');
        $j('#total1_'+hcare_id).html(numFormat(parseFloat($j('#total1_'+hcare_id).html().replace(/,/g,'')) + parseFloat(field.value)));
        if(hcare_id=='18')
            phic_coverage +=  parseFloat(field.value);

    });
    // alert(dr_charge);
    if((phic_coverage > PF1) || (temp_total < parseFloat($j('#'+obj.id).val()))) {
        alert('The amount you enter is greater than PHIC Maximum Coverage \n OR the greater than Actual Charge.');
        $j('#'+obj.id).val(0)
        $j('#total1_18').html(0);
        $j.each(hcare_field, function(i, field){
            if(obj.name == field.name){
                val = 0;
            }else{
                val = field.value;
            }
            hcare_id = $j('#'+field.name).attr('hcareid');
            $j('#total1_'+hcare_id).html(numFormat(parseFloat($j('#total1_'+hcare_id).html().replace(/,/g,'')) + parseFloat(val)))
        });
        calculateDetails1();
        return true;
    }else{
        return false;
    }
}

function checkNegation2(obj)
{
    if(isNaN($j('#coverage2_18_'+dr_nr+'_'+area).val()) || $j('#coverage2_18_'+dr_nr+'_'+area).val()==""){
        $j('#coverage2_18_'+dr_nr+'_'+area).val(numFormat(0));
    }
    var hcare_field = $j('.calc-hcare2').serializeArray();
    var phic_coverage = 0, val = 0;
    var str = obj.id.split("_");
    var dr_charge = parseFloat($j('#doc_charge_'+str[2]+'_'+str[3]).val());
    var dr_discount = parseFloat($j('#doc_discount_'+str[2]+'_'+str[3]).val());
    var temp_total = dr_charge - dr_discount;
    var PF2 = $j("#second_rate option:selected").attr('value_pf');

    $j.each(hcare_field, function(i, field){
        hcare_id = $j('#'+field.name).attr('hcareid');
        $j('#total2_'+hcare_id).html(numFormat(parseFloat($j('#total2_'+hcare_id).html().replace(/,/g,'')) + parseFloat(field.value)));
        if(hcare_id=='18')
            phic_coverage +=  parseFloat(field.value);

    });


    if((phic_coverage > PF2) || (temp_total < parseFloat($j('#'+obj.id).val()))) {
        alert('The amount you enter is greater than PHIC Maximum Coverage \n OR the greater than Actual Charge.');
        $j('#'+obj.id).val(0)
        $j('#total2_18').html(0);
        $j.each(hcare_field, function(i, field){
            if(obj.name == field.name){
                val = 0;
            }else{
                val = field.value;
            }
            hcare_id = $j('#'+field.name).attr('hcareid');
            $j('#total2_'+hcare_id).html(numFormat(parseFloat($j('#total2_'+hcare_id).html().replace(/,/g,'')) + parseFloat(val)))
        });
        calculateDetails2();
        return true;
    }else{
        return false;
    }
}


function assignDoctoTable()
{
    //First rate Info
    var PF1 = $j("#first_rate option:selected").attr('value_pf');
    var D1_first = $j("#first_rate option:selected").attr('value_D1');
    var D3_first = $j("#first_rate option:selected").attr('value_D3');
    var D4_first = $j("#first_rate option:selected").attr('value_D4');

    //Second rate info
    var PF2  = $j("#second_rate option:selected").attr('value_pf');
    var D1_second = $j("#second_rate option:selected").attr('value_D1');
    var D3_second = $j("#second_rate option:selected").attr('value_D3');
    var D4_second = $j("#second_rate option:selected").attr('value_D4');

    $j('#D1_first').html(((D1_first) ? numFormat(D1_first) : '0.00' ) );
    $j('#D1_second').html( ((D1_second) ? numFormat(D1_second) : '0.00' ) );
    $j('#D1_total').html( numFormat(parseFloat($j('#D1_first').html().replace(/,/g,'')) + parseFloat($j('#D1_second').html().replace(/,/g,''))));
    $j('#D3_first').html( ((D3_first) ? numFormat(D3_first) : '0.00' ) );
    $j('#D3_second').html( ((D3_second) ? numFormat(D3_second) : '0.00') );
    $j('#D3_total').html( numFormat(parseFloat($j('#D3_first').html().replace(/,/g,'')) + parseFloat($j('#D3_second').html().replace(/,/g,''))));
    $j('#D4_first').html( ((D4_first) ? numFormat(D4_first) : '0.00' )  );
    $j('#D4_second').html( ((D4_second) ? numFormat(D4_second) : '0.00') );
    $j('#D4_total').html( numFormat(parseFloat($j('#D4_first').html().replace(/,/g,'')) + parseFloat($j('#D4_second').html().replace(/,/g,''))));

    $j('#first_total').html(((PF1- parseFloat($j('#total1_18').html().replace(/,/g,''))) ? numFormat(PF1- parseFloat($j('#total1_18').html().replace(/,/g,''))) : '0.00' ));
    $j('#second_total').html(((PF2- parseFloat($j('#total2_18').html().replace(/,/g,''))) ? numFormat(PF2- parseFloat($j('#total2_18').html().replace(/,/g,''))) : '0.00' ));

}

//added by Nick, 05-12-2014
function addTooltip(elem,mod_id,mod_date){
    if(mod_id.trim() == "" || mod_date.trim() ==""){
        return;
    }
    var caption = "Modified by: " + mod_id + "<br>Modified date: " + mod_date;
    $j("#"+elem).hover(function(){
        return overlib(caption, CENTER);
    });

    $j("#"+elem).mouseleave(function(){
        return nd();
    });
}

//added by Nick, 05-12-2014
function showLoading(){
    isComputing = true;
    return overlib('Please Wait ...<br><img src="../../images/ajax_bar.gif">',
            WIDTH,300, TEXTPADDING,5, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            NOCLOSE, CAPTION,'',
            MIDX,0, MIDY,0,
            STATUS,'');

    // $j('#loadingBox')
    // .dialog({
    //     autoOpen:true,
    //     modal:true,
    //     title:"Loading",
    //     width:300,
    //     height:100,
    //     position:"top",
    //     closeOnEscape: false,
    //     open: function(event, ui){
    //         $j("#loadingBox .ui-dialog-titlebar-close ui-corner-all").hide();
    //     }
    // });
}

//added by Nick, 05-12-2014
function hideLoading(){
    isComputing = false;
    cClick();
    // $j('#loadingBox').dialog("close");
}

//added by Nick
function Dlg(elem,mode){
    $j('#'+elem).dialog(mode);
}

function hideDaysCovered(){
    $('coverdays').style.display = "none";
}
/*added By Mark 2016-08-30*/
var isPatientCopy ='0';
function SoaForPatientCOpy(ReturnFalse){
    isPatientCopy = ReturnFalse;
    xajax_showSoa($('encounter_nr').value);
 }

function printSoa(){
    var rpath = $('rpath').value;
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_dt = $('billdate').value;
    var billdate_display = $('billdate_display').value;
    var has_blood_borrowed = $('hasbloodborrowed').value;//added by arc 05/12/2016
    //var frm_dte = $('bill_frmdte').value;
    var frm_dte = $('admission_dte').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var isPaywardSettlement = $('IsPaywardSettlement').checked ? 1 : 0;

    if($('isdied').checked){
        var deathdate = $j('#deathdate').val();
    }else{
        var deathdate = '';

    }

  
    var detailed;

    if ($('IsDetailed').checked)
        detailed = 1;
    else
        detailed = 0;

    urlholder = rpath+'modules/billing_new/SOA_versioning.php'+seg_URL_APPEND+
                      '&pid='+pid+'&encounter_nr='+enc+
                      '&from_dt='+frm_dte+
                      '&billdate_display='+billdate_display+
                      '&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+
                      '&nr='+bill_nr+'&IsDetailed='+detailed+'&deathdate='+deathdate+
                      '&isPaywardSettlement='+isPaywardSettlement+
                      '&has_blood_borrowed='+has_blood_borrowed+
                      '&isPatientCopy='+0;

    urlholder2 = rpath+'modules/billing_new/SOA_versioning.php'+seg_URL_APPEND+
                      '&pid='+pid+'&encounter_nr='+enc+
                      '&from_dt='+frm_dte+
                      '&billdate_display='+billdate_display+
                      '&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+
                      '&nr='+bill_nr+'&IsDetailed='+detailed+'&deathdate='+deathdate+
                      '&isPaywardSettlement='+isPaywardSettlement+
                      '&has_blood_borrowed='+has_blood_borrowed+
                      '&isPatientCopy='+1;

    nleft = (screen.width - 680)/2;
    ntop = (screen.height - 520)/2;
        //modified by julius 01-12-16
        if(isPatientCopy == 0){   
            printwin = window.open(urlholder,"soa1","Print Billing", "toolbar=no, status=no, menubar=no, width=50, height=150,dependent=yes, resizable=yes, scrollbars=yes");
            printwin.moveTo(0,0);
            printwin2 = window.open(urlholder2,"soa2" ,"Print Billing", "toolbar=no, status=no, menubar=no, width=50, height=150,dependent=yes, resizable=yes, scrollbars=yes");
            printwin2.moveTo(400,0);
            isPatientCopy == 0;
        }else if(isPatientCopy == 2){
            printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
            isPatientCopy = 0;
        }else{
            printwin2 = window.open(urlholder2,"Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500,location=center,dependent=yes, resizable=yes, scrollbars=yes,top=" + ntop + ",left=" + nleft);
            isPatientCopy = 0;  
        }  
    // }, 3000); 
    

}

function set_isInfirmaryOrDependent(str){
    isInfirmaryOrDependent = str;
}

//added by janken 11/14/2014
function hideOpdArea(){
    $('showOpdType').style.display = 'none';
    $('opd_area').setStyle({
              display: 'none',
             });
}

//added by janken 11/11/2014 for eclaims checking
function checkEclaimsDate(cond){

    if(cond) {
        $('billdate_display').setStyle({
             backgroundColor: '#07d216',
             font: 'bold 12px Arial',
            });
        $('admission_date').setStyle({
             backgroundColor: '#07d216',
             font: 'bold 12px Arial',
            });
        $('eclaims_dte').innerHTML = 'ECLAIMS';
        $('eclaims_frmdte').innerHTML = 'ECLAIMS';
    }
    else{
         $('billdate_display').setStyle({
             backgroundColor: '',
             font: 'bold 12px Arial',
            });
        $('admission_date').setStyle({
             backgroundColor: '',
             font: 'bold 12px Arial',
            });
        $('eclaims_dte').innerHTML = '';
        $('eclaims_frmdte').innerHTML = '';
    }
}

// Added by Gervie 09/03/2015
function deleteReason(){
    var reason = $j('#select-reason').val();

    if(reason == '10'){
        $j('#delete_other_reason').show();
        $j('#delete_other_reason').val('');
        $j('#delete_reason').val(reason);
    }
    else{
        $j('#delete_other_reason').hide();
        $j('#delete_other_reason').val('');
        $j('#delete_reason').val(reason);
    }
}

/*added By Mark 08/31/2016*/
function SelectToprint(){
 $j('#selectPrint-dialog').dialog({
                autoOpen: true,
                modal: false,
                height: 'auto',
                width: 'auto',
                resizable: false,
                draggable: true,
                show: 'explode',
                hide: 'explode',
                title: 'Select to Print!',
                position: 'top',
                buttons: {
                 
                    "Cancel": function () {
                       
                        $j(this).dialog("close");
                    }
                }
            });
}

// Added by Gervie 03-19-2017
function hasHighFlux() {
    var hasHighFlux = $j('#hasHighFlux').val();

    if(hasHighFlux === "high") {
        return false;
    }

    return true;
}

// Added by Jeff 02-14-18 for prompt doctor is not accredited in PHIC.
function CheckDrPrompt(id){
    alert("DOCTOR IS NOT A PHIC ACCREDITED. Please check doctor's accreditation.");
    if ($(''+id).checked == true)
        {
            $(''+id).checked = false;
        }  
}
// created by JOY @ 02-21-2018
function checkDrAccreditation1(id,is_valid,prompt_msg){
    if (is_valid == '0') {
        alert(prompt_msg);
          $j("#apply1_"+id).attr("checked",false);
    }

  
} // ebd by JOY

// created by JOY @ 02-21-2018
function checkDrAccreditation2(id,is_valid,prompt_msg){
    if (is_valid == '0') {
        alert(prompt_msg);
          $j("#apply2_"+id).attr("checked",false);
    }

  
} // ebd by JOY
