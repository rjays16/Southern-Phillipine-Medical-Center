
 
 function preset(){
     listPersonInsurance();
     listEncounterInsurance();
     listBirthCert();
 }
 
 function listPersonInsurance(){
     ListGen.create($('person_insurance'), {
         id: 'person_insurance_list',
         url: 'ajax/ajax-encounter-insurance.php',
         params: {
             action : 'person-insurance-list',
             pid : $('pid').value,
             encounter_nr : $('encounter_nr').value
         },
         width: "100%",
         height: "100%",
         autoLoad: true,
         columnModel: [
             {
                 name: 'firm_id',
                 label: 'Name',
                 width: "40%",
                 sorting: ListGen.SORTING.asc,
                 sortable: true
             },
             {
                 name: 'insurance_nr',
                 label: 'Number',
                 width: "20%",
                 sorting: ListGen.SORTING.asc,
                 sortable: true
             },
             {
                 name: 'is_principal',
                 label: 'Principal Holder',
                 width: "20%",
                 sortable: false,
                 render: function(data,index){
                     var value = (parseInt(data[index]['is_principal']) == 1) ? "YES" : "NO";
                     return '<a href="#">'+value+'</a>'
                 }
             },
             {
                 name: 'options',
                 label: 'Action',
                 width: "20%",
                 sortable: false,
                 render: function(data,index){
                     var item = data[index]['is_principal'];
                     return '<button class="segButton" onclick="addInsurance()"><img src="../../images/edit.gif" style="height: 15px; width: 15px;" />Add/Edit</button>';
                 }
             }
         ]
     });
 }
 
 function listEncounterInsurance(){
     ListGen.create($('encounter_insurance'), {
         id: 'encounter_insurance_list',
         url: 'ajax/ajax-encounter-insurance.php',
         params: {
             action : 'encounter-insurance-list',
             encounter_nr : $('encounter_nr').value
         },
         width: "100%",
         height: "100%",
         autoLoad: true,
         columnModel: [
             {
                 name: 'firm_id',
                 label: 'Name',
                 width: "40%",
                 sorting: ListGen.SORTING.asc,
                 sortable: true
             },
             {
                 name: 'insurance_nr',
                 label: 'Number',
                 width: "20%",
                 sorting: ListGen.SORTING.asc,
                 sortable: true
             },
             {
                 name: 'is_principal',
                 label: 'Principal Holder',
                 width: "20%",
                 sortable: false,
                 render: function(data,index){
                     var value = (parseInt(data[index]['is_principal']) == 1) ? "YES" : "NO";
                     return '<a href="#">'+value+'</a>'
                 }
             },
             {
                 name: 'options',
                 label: 'Action',
                 width: "20%",
                 sortable: false,
                 render: function(data,index){
                     var item = data[index];
                     var html = '<button class="segButton" onclick="removeEncounterInsurance('+item['hcare_id']+');"><img src="../../images/btn_delitem.gif"/>Remove</button>';
                         html += '<button class="segButton" onclick="csfFullPage();"><img src="../../images/cashier_print.gif"/>CSF Fullpage</button>';
                     //html += '<button class="segButton" onclick="viewInsurance(\''+item['hcare_id']+'\',\''+item['insurance_nr']+'\',\''+item['is_principal']+'\')">View</button>';
                     return html;
                 }
             }
         ]
     });
 }
 
 function addInsurance(){
     var pid = $('pid').value;
     var encounter_nr = $('encounter_nr').value;
 
     if($('bill_type').value){
         var url = 'seg-reg-insurance-tray.php?';
     }else{
         var url = "../../modules/billing_new/seg-reg-insurance-tray.php?";
     }
 
     url += 'pid=' + pid + '&encounter_nr=' + encounter_nr + '&frombilling=1'
 
 
     $j('<div></div>')
         .html('<iframe style="width: 100%; height: 100%" src="'+url+'"></iframe>')
         .dialog({
             autoOpen:true,
             modal:true,
             width:"80%",
             height: 550
         }
     );
 }
 
 function auditTrail(){
     var pid = $('pid').value;
     var encounter_nr = $('encounter_nr').value;
 
     if($('bill_type').value){
         var url = 'seg-insurance-audit-trail.php?';
     }
     else{
         var url = '../../modules/billing_new/seg-reg-insurance-tray.php?';
     }
 
     return overlib(
         OLiframeContent(url + 'pid='+pid+'&encounter_nr='+encounter_nr+'&frombilling=1', 600, 350, 'fOrderTray', 1, 'auto'),
                         WIDTH,600, TEXTPADDING,0, BORDER,0,
                         STICKY, SCROLL, CLOSECLICK, MODAL,
                         CLOSETEXT, '<img src='+'../../images/close.gif border=0 >',
                         CAPTIONPADDING,4,
                         CAPTION,'Insurance Audit Trail',
                         MIDX,0, MIDY,0,
                         STATUS,'Insurance Audit Trail');
 }
 
 function addInsuranceRow(){
 
 }
 
 /*function removeEncounterInsurance(hcare_id){
     if(!confirm("Are you sure you want to delete this insurance")){
        return false;
     }
     $j.ajax({
         url : 'ajax/ajax-encounter-insurance.php',
         data : {
             action : 'delete-encounter-insurance',
             encounter_nr : $('encounter_nr').value,
             hcare_id : hcare_id,
             pid : $('pid').value
         },
         success : function(data,textStatus,jqXHR){
             $('encounter_insurance').list.refresh();
         },
         error : function(x,y){
             alert(x);
         }
     });
 }*/
 
    // Added by Johnmel 12292018
    function csfFullPage(){
         var admissionDt = $('admission_date').value;
         var enc_no = $('encounter_nr').value;
         var pid = $('pid').value;
         var rawUrlData = {reportid:'csfFP', 
                          repformat:'pdf',
                          admissionDt:admissionDt,
                          param:{enc_no:enc_no,pid:pid}};
        var urlParams = $j.param(rawUrlData);
        window.open('../../modules/reports/show_report.php?'+ urlParams, '_blank');

    }
 
 
     function removeEncounterInsurance(hcare_id){
         
             res = confirm('Are you sure you want to delete this insurance?'); 
 
 
                if (res){
 
            $j('#reason-dialog').dialog({
                 autoOpen: true,
                 modal: true,
                 height: 'auto',
                 width: '500',
                 resizable: false,
                 draggable: false,
                 show: 'fade',
                 hide: 'fade',
                 title: 'Delete Insurance',
                 position: 'top',
                 buttons: {
                     "Delete": function () {
                         var del_reason = $j('#delete_reason').val();
                         var del_other_reason = $j('#delete_other_reason').val();
                         if(del_reason != ""){
                            // xajax_deleteBilling(old_billnr, enc_nr, del_reason, del_other_reason, bill_started);
                             //xajax_clearBilling();
 
                            // $j(this).dialog("close");
                                if(del_reason==8){
                                if(del_other_reason==''){
                                    alert("Please provide reason for deleting Insurance");
                                }
                            }
                                $j.ajax({
         url : 'ajax/ajax-encounter-insurance.php',
         data : {
             action : 'delete-encounter-insurance',
             encounter_nr : $('encounter_nr').value,
             hcare_id : hcare_id,
             pid : $('pid').value,
             reason : del_reason,
             other_reason: del_other_reason
         },
         success : function(data,textStatus,jqXHR){
             $('encounter_insurance').list.refresh();
         },
         error : function(x,y){
             alert(x);
         }
     });
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
 
     }
 
 function showAddInsuranceButton(){
     if($j('#insurance_classes option:selected').val() == 3){
         $j('#btn_add_insurance').hide();
         $j('#btn_audit_trail').hide();
     }else{
         $j('#btn_add_insurance').show();
         $j('#btn_audit_trail').show();
     }
 }
 function deleteReason(){
     var reason = $j('#select-reason').val();
 
     if(reason == '8'){
         $j('#delete_other_reason').show();
       $j('#delete_other_reason').val();
         $j('#delete_reason').val(reason);
     }
     else{
         $j('#delete_other_reason').hide();
         $j('#delete_other_reason').val('');
         $j('#delete_reason').val(reason);
     }
 }
 
function listBirthCert(){
    ListGen.create($('birthCertData'), {
        id: 'mother_birth_cert_list',
        url: 'ajax/ajax-encounter-insurance.php',
        params: {
            action : 'mother-birth-cert-list',
            pid : $('pid').value
        },
        width: "100%",
        height: "100%",
        autoLoad: true,
        columnModel: [
            {
                name: 'NAME',
                label: 'Name',
                width: "60%",
                sorting: ListGen.SORTING.asc,
                sortable: true
            },
            {
                name: 'bday',
                label: 'Birth Day',
                width: "20%",
                sorting: ListGen.SORTING.asc,
                sortable: true
            },
            {
                name: 'options',
                label: 'Action',
                width: "20%",
                sortable: false,
                render: function(data,index){
                    var item = data[index];
                    var html = '<button class="segButton" onclick="viewBirthCert('+item['pid']+');"><img src="../../images/cashier_view.png"/>View Birth Cert</button>';
                      html += '<button class="segButton" onclick="muslimLink('+item['pid']+');"><img src="../../images/cashier_view.png"/>View Birth Cert(Muslim)</button>';
                    //html += '<button class="segButton" onclick="viewInsurance(\''+item['hcare_id']+'\',\''+item['insurance_nr']+'\',\''+item['is_principal']+'\')">View</button>';
                    return html;
                }
            }
        ]
    });
}

function viewBirthCert(pid){
    window.open("../../modules/registration_admission/certificates/cert_birth_pdf_jasper.php?pid="+pid+"&pidJS="+"layout"+"","windowName", "height=1000,width=800"); 
    // window.open('../../modules/registration_admission/certificates/cert_birth_interface_new.php?ntid=false&lang=en&pid='+pid+'&viewCert=1', '_blank');
}
function muslimLink(pid){

    window.open("../../modules/registration_admission/certificates/cert_birth_muslim_withimage_pdf.php?id="+pid+"&pid22="+"layout"+"","windowName","height=1000,width=800");
}