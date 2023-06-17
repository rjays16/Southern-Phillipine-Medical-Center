jQuery(document).ready(function ($) {
    $('#saveDialyzer').click(function (e) {
        if($("#dialyzerForm").valid()) {
            saveMachine();
        }
    });
    $("#dialyzerForm").validate({
        focusInvalid: false,
        onfocusout: false,
        onclick: false,
        onkeyup: false,
        rules: {
            datefrom: {
                required: true,
                date: true
            },
            selAMPM: {
                required: true
            },
            machine_nr: {
                required: true
            }
        },

        messages: {
            datefrom: 'Input Valid Date',
            selAMPM: 'Select AM/PM',
            machine_nr: 'Input Valid Machine Number',
        },
        showErrors: function (errorMap, errorList) {
            if (errorList.length > 0)
                alert(errorList[0]['message']);
        }
    });

});