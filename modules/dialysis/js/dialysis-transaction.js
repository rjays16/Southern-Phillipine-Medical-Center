var transactionType = $('#' + idPrefix + '_transactionType');
var machineNrField = $('#' + idPrefix + '_machineNr');
var numberOfReuseField = $('#' + idPrefix + '_numberOfReuse');
var dialyzerIdField = $('#' + idPrefix + '_dialyzerId');
var dialyzerNameField = $('#' + idPrefix + '_dialyzerName');

var origReuseField = $('#orig-reuse');
var origMachineNrField = $('#orig-machine-nr');
var origDialyzerName = $('#orig-dialyzer-name');
var origDialyzerId = $('#orig-dialyzer-id');

var TRANSACTION_TYPE_OLD = 1;
var TRANSACTION_TYPE_NEW = 2;
var TRANSACTION_TYPE_REUSE = 3;

$(function () {

    if (origReuseField.val() >= 0) {
        transactionType.val(TRANSACTION_TYPE_REUSE);
    }

    transactionType.on('change', transactionTypeOption_onChanged);
    transactionTypeOption_onChanged();

    dialyzerIdField.on('change', dialyzerIdField_onChanged);

    dialyzerIdField.trigger('change');

});

function transactionTypeOption_onChanged() {

    switch (parseInt(transactionType.val())) {
        case TRANSACTION_TYPE_OLD:
            numberOfReuseField.val(0).prop('readonly', false);
            setSelectAsReadWrite(dialyzerIdField);
            setSelectAsReadWrite(machineNrField);
            break;
        case TRANSACTION_TYPE_NEW:
            numberOfReuseField.val(0).prop('readonly', true);
            setSelectAsReadWrite(dialyzerIdField);
            setSelectAsReadWrite(machineNrField);
            break;
        case TRANSACTION_TYPE_REUSE:
            numberOfReuseField.val(origReuseField.val()).prop('readonly', true);
            dialyzerNameField.val(origDialyzerName.val());

            dialyzerIdField.val(origDialyzerId.val());
            setSelectAsReadOnly(dialyzerIdField);

            machineNrField.val(origMachineNrField.val());
            setSelectAsReadWrite(machineNrField);
            break;
    }

}

function dialyzerIdField_onChanged() {
    if (dialyzerIdField.prop('type') != 'hidden')
        dialyzerNameField.val(dialyzerIdField.find('option:selected').html());
}

function setSelectAsReadOnly(select) {
    select.data('default', select.find('option:selected').val());
    select.addClass('readonly');
    select.on('change', function () {
        if(select.find('option:selected').val() != select.data('default')) {
            select.val(select.data('default'));
        }
    });
}

function setSelectAsReadWrite(select) {
    select.removeData('default');
    select.removeClass('readonly');
    select.off('change');
}