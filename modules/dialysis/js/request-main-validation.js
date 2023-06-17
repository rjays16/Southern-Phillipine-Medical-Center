
function showSubsidize(data, amount){
    // alert(data);
      jQuery('#subsidizeModal').dialog({
            autoOpen: true,
            modal: true,
            show: 'fade',
            fade: 'fade',
            height: 220,
            width: 400,
            resizable: false,
            draggable: false,
            title: "Subsidize",
            position: "top",
            open: function(){
                if (data =='ph') { 
                    jQuery('#subsidizeText').html('Subsidize Philhealth');

                    jQuery('#subsidyValue').val(jQuery('#PH_subsidize_amount').val());
                    jQuery('#subsidyClass').val(jQuery('#PH_subsidize_class').val());
                }
                if (data =='nph') {
                    jQuery('#subsidizeText').html('Subsidize Non-Philhealth');

                    jQuery('#subsidyValue').val(jQuery('#NPH_subsidize_amount').val());
                    jQuery('#subsidyClass').val(jQuery('#NPH_subsidize_class').val());
                }
                if (data =='hdf') 
                    jQuery('#subsidizeText').html('Subsidize HDF');
            },
            buttons: {
                "Save": function () {
                        if (data =='ph') {
                            if(jQuery('#subsidyValue').val() != '')
                                jQuery('#PHamount').val(jQuery('#subsidyValue').val());
                            
                            jQuery('#PH_subsidize_amount').val(jQuery('#subsidyValue').val());
                            jQuery('#PH_subsidize_class').val(jQuery('#subsidyClass').val());
                        }
                        else if (data =='nph') {
                            if(jQuery('#subsidyValue').val() != '')
                                jQuery('#NPHamount').val(jQuery('#subsidyValue').val());
                             
                             jQuery('#NPH_subsidize_amount').val(jQuery('#subsidyValue').val());
                             jQuery('#NPH_subsidize_class').val(jQuery('#subsidyClass').val());
                        }
                        else if (data =='hdf') {
                            jQuery('#HDFAmount').val(jQuery('#subsidyValue').val());
                            jQuery('#HDF_subsidize_amount').val(jQuery('#subsidyValue').val());
                            jQuery('#HDF_subsidize_class').val(jQuery('#subsidyClass').val());
                        }
                    jQuery(this).dialog("close");
                },
                "Cancel": function () {
                    jQuery(this).dialog("close");
                }
            },
            close: function(){
                // if (data =='ph') {
                //             alert(jQuery('#PH_subsidize_amount').val());
                //             alert(jQuery('#PH_subsidize_class').val());
                //         }
                //         else if (data =='nph') {
                //              alert(jQuery('#NPH_subsidize_amount').val());
                //             alert(jQuery('#NPH_subsidize_class').val());
                //         }
                //         else if (data =='hdf') {
                //             alert(jQuery('#HDF_subsidize_amount').val());
                //             alert(jQuery('#HDF_subsidize_class').val());
                // }
                jQuery('#subsidyValue').val('');
                jQuery('#subsidyClass').val(1);
            }
        });
}
// added by Matsuu 02242017
function getPrevTrn() {
    var NPH  = jQuery('#NPHquantity').val();
    var PH  =jQuery('#PHquantity').val();
    var HDF  =jQuery('#HDFQty').val();
    var limit =0;
    if(NPH) limit++;
    if(PH) limit++;
    if(HDF) limit++;
   jQuery('#limitcopy').val(limit);

}
// ended by Matsuu 02242017

jQuery(document).ready(function($) {
    // $('#HDFsubsidize').click(function(){
        //remove unappropriate word
    // });



    $("#orderForm").validate({
        focusInvalid: true,
        onfocusout: false,
        onclick:false,
        onkeyup:false,
        //modified by raymond - all return statement changed
        rules:{
            name: 'required',
            PHquantity: {
                required: function(elem) {
                    return (($('#NPHquantity').val() == "" || $('#NPHamount').val() == "") &&
                       ($('#HDFquantity').val() == "" || $('#HDFAmount').val() == ""));
                },
                min: 1,
                number:true
            },
            PHamount: {
                required: function(elem) {
                    return (($('#NPHquantity').val() == "" || $('#NPHamount').val() == "") &&
                       ($('#HDFquantity').val() == "" || $('#HDFAmount').val() == ""));
                },
                min: 0,
                number:true
            },
            NPHquantity: {
                required: function(elem) {
                    return (($('#PHquantity').val() == "" || $('#PHamount').val() == "") &&
                       ($('#HDFquantity').val() == "" || $('#HDFAmount').val() == ""));
                },
                min: 1,
                number:true
            },
            NPHamount: {
                required: function(elem) {
                    return (($('#PHquantity').val() == "" || $('#PHamount').val() == "") &&
                       ($('#HDFquantity').val() == "" || $('#HDFAmount').val() == ""));
                },
                min: 0,
                number:true
            },
            HDFquantity : {
                required: function(elem) {
                    return (($('#NPHquantity').val() == "" || $('#NPHamount').val() == "") &&
                       ($('#PHquantity').val() == "" || $('#PHamount').val() == ""));
                },
                min: 1,
                number:true
            },
            HDFamount : {
                required: function(elem) {
                    return ( $('#NPHamount').val() == "") &&
                       ($('#PHamount').val() == "");
                },
                min: 1,
                number:true
            },
            request_doctor:{
                min:1
            },
            attending_nurse:{
                min:1
            },
            reqdiagnosis:'required',
            procedure:'required'
        },
        messages: {
            name: 'Please Select Patient',
            reqdiagnosis:'Diagnosis is Required',
            procedure:'Procedure is Required',
            request_doctor:'Please Select Doctor',
            attending_nurse:'Please Select Nurse',
            PHquantity:'Please Enter Valid Philhealth quantity.',
            PHamount:'Please Enter Valid Philhealth amount.',
            NPHquantity:'Non-Philhealth quantity should be a number.',
            NPHamount:'Non-Philhealth amount should be a number.',
            HDFamount: 'Please Enter Valid HDF amount',
            HDFquantity: 'Please Enter Valid HDF quantity',
        },
        errorPlacement: function (error, element) {
            
        },
        showErrors: function(errorMap, errorList) {
            if(errorList.length > 0){
                alert(errorList[0]['message']);

            }
        },
        submitHandler: function(form){

            var additionalValidation = true;

            // if($('#PH_subsidize_amount').val() && $('#PHamount').val() != '') {
            //     if($('#PHamount').val() !== $('#PH_subsidize_amount').val()) {
            //         alert('Philhealth amount should be the same with the subsidized amount.');
            //         $('#PHamount').focus();
            //         additionalValidation = false;
            //     }
            // }

            // if($('#NPH_subsidize_amount').val() && $('#NPHamount').val() != '') {
            //     if($('#NPHamount').val() !== $('#NPH_subsidize_amount').val()) {
            //         alert('Non-Philhealth amount should be the same with the subsidized amount.');
            //         $('#NPHamount').focus();
            //         additionalValidation = false;
            //     }
            // }

            if(additionalValidation) {
                if(confirm('Continue printing pre-bills?')) {
                    form.submit();
                }
            }
        }

    });
  });
