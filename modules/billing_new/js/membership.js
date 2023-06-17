$(function () {

    $('#btn-submit').on('click', function (e) {
        if (!validateDependents())
            return false;
        Alerts.loading();
        ajaxValidate();
    });

    $('#btn-toggle-cf1,#btn-toggle-pmrf').on('click', function () {
        var e = $('#' + $(this).data('content'));
        e.toggle();
        var flag = e.find('#' + $(this).data('flag'));
        if (flag.val() == 1)
            flag.val(0);
        else
            flag.val(1);

        signatoryFields();
        initAddPatientAsDependentButton();
    });

    $('#MembershipForm_isMember').on('change', function () {
        var isMemberCheckbox = $(this);
        if (isMemberCheckbox.is(':checked')) {
            confirmLoadPersonInfoToUi(isMemberCheckbox);
        }
        signatoryFields();
        memberFields();
        initAddPatientAsDependentButton();
    });

    $('#MembershipForm_cf1Signatory_is_representative').on('change', signatoryFields);

    $('#MembershipForm_cf1Signatory_is_representative2').on('change', signatoryFields2);

    $('#MembershipForm_sex,#MembershipForm_civilStatus').on('change', marriedFemale);

    $('#MembershipForm_pmrfMembershipCategory').on('change', membershipFields);

    $('#MembershipForm_pmrfMembershipIncome').on('change', formatMoneyFields);

    $('#btn-add-dependent').on('click', addDependent);

    $('#print-cf1').on('click', printCf1);

    $('#print-pmrf').on('click', printPmrf);

    $('#MembershipForm_cf1Signatory_relation').on('change', cf1RelationFields);

    $('#MembershipForm_cf1Signatory_relation2').on('change', cf1RelationFields2);

    $('#MembershipForm_cf1Is_incapacitated').on('change', isIncapacitated);
    
    $('#MembershipForm_cf1Is_incapacitated2').on('change', isIncapacitated2);

    $('#btn-add-patient-dependent').on('click',addPatientAsDependentButton);
    
    $('#MembershipForm_cf1Signatory_relation').on('change', cf1RelationFields);

    signatoryFields();

    signatoryFields2();

    initAddPatientAsDependentButton();

    marriedFemale();

    membershipFields();

    formatPin();

    formatCalendar();

    pmrfDependentRelation_Change();

    deleteButtonOld_Click();

    memberFields();

    cf1RelationFields();

    cf1RelationFields2();
});

function initAddPatientAsDependentButton() {
    var isMemberCheckBox = $('#MembershipForm_isMember');
    var button = $('#btn-add-patient-dependent');
    if(!isMemberCheckBox.is(':checked')) {
        button.show();
    } else {
        button.hide();
    }
}

function addPatientAsDependentButton() {
    Alerts.confirm({
        title: 'Confirm Action',
        content: 'Are you sure you want to add this patient to the list of dependents?',
        callback: function (result) {
            if(result) {
                var dependentsContainer = $('#dependents');
                var relation = $('#MembershipForm_relation option:selected').val();
                var patientPin = $('#MembershipForm_cf1Pin').val();
                var patientFirstName = personInfo.nameFirst || '';
                var patientMiddleName = personInfo.nameMiddle || '';
                var patientLastName = personInfo.nameLast || '';
                var patientExtensionName = personInfo.nameExtension || '';
                var patientBirthDate = personInfo.birthDate || '';
                var sex = personInfo.sex || '';
                var rows = dependentsContainer.find('tr');
                var theSame = false;
                rows.each(function(index, e) {
                    var $this = $(this);
                    var hits = 0;
                    if($this.find('.relation option:selected').val().toLowerCase() == relation.toLowerCase())
                        hits++;
                    if($this.find('.pin').val().toLowerCase() == patientPin.toLowerCase())
                        hits++;
                    if($this.find('.first_name').val().toLowerCase() == patientFirstName.toLowerCase())
                        hits++;
                    if($this.find('.middle_name').val().toLowerCase() == patientMiddleName.toLowerCase())
                        hits++;
                    if($this.find('.last_name').val().toLowerCase() == patientLastName.toLowerCase())
                        hits++;
                    if($this.find('.name_extension').val().toLowerCase() == patientExtensionName.toLowerCase())
                        hits++;
                    if($this.find('.birth_date').val().toLowerCase() == patientBirthDate.toLowerCase())
                        hits++;
                    if($this.find('.sex').val().toLowerCase() == sex.toLowerCase())
                        hits++;
                    theSame = hits == 8;
                });

                if(theSame) {
                    alert('The Person is already added.');
                    return false;
                }

                var template = $('#dependent-template').html();
                
                dependentsContainer.append(Mustache.render(template, {
                    dependent_pin : patientPin,
                    dependent_first_name : patientFirstName,
                    dependent_middle_name : patientMiddleName,
                    dependent_last_name : patientLastName,
                    dependent_name_extension : patientExtensionName,
                    dependent_birth_date : patientBirthDate
                }));

                var addedRow = dependentsContainer.find('tr:last-child');

                if(relation == 'P' && sex == 'm')
                    relation = 'f';
                else if(relation == 'P' && sex == 'f')
                    relation = 'm';
                else
                    relation = relation.toLowerCase();

                addedRow.find('.relation').val(relation);
                addedRow.find('.sex').val(sex);

                formatPin();
                formatCalendar();
                pmrfDependentRelation_Change();
                deleteButton_Click();
            }
        }
    });
}

function ajaxValidate() {
    var form = $('#membership-form');

    if ($('.alert').length > 0)
        $('[data-dismiss=alert]').trigger('click');
    $('.error').hide();

    $.ajax({
        url: baseUrl + '/index.php?r=phic/membership/validateForm',
        dataType: 'json',
        type: 'POST',
        data: form.serialize(),
        success: function (data) {
            var errorSummary = $('#membership-form_es_').find('ul');
            if (typeof(data) == 'object' && Object.keys(data).length > 0) {
                errorSummary.empty();
                var content = '';
                for (var property in data) {
                    if (data.hasOwnProperty(property)) {
                        content += '<li>' + data[property][0] + '</li>';
                        $('#' + property + '_em_').show().html(data[property][0]);
                    }
                }
                errorSummary.html(content);
                errorSummary.parent().show();

                scrollTo(0);

                Alerts.close();
            } else {
                form.submit();
            }
        }
    });
}

function isIncapacitated() {
    if($('#MembershipForm_cf1Is_incapacitated').is(':checked')) {
        $('#MembershipForm_cf1Reason,#MembershipForm_cf1Reason_em_,[for=MembershipForm_cf1Reason]').hide().val('');
        $('label[for=MembershipForm_cf1Reason]').html('Reason <span style="color:red">*');
        $('label[for=MembershipForm_cf1Signatory_name]').html('Signatory Name <span style="color:red">*');
    } else {
        
     
        $('#MembershipForm_cf1Reason,#MembershipForm_cf1Reason_em_,[for=MembershipForm_cf1Reason]').show();
        $('label[for=MembershipForm_cf1Reason]').html('Reason <span style="color:red">*');
        $('label[for=MembershipForm_cf1Signatory_name]').html('Signatory Name <span style="color:red">*');
    }
} 

//Added By Neil
function isIncapacitated2() {
    if($('#MembershipForm_cf1Is_incapacitated2').is(':checked')) {
        $('#MembershipForm_cf1Reason2,#MembershipForm_cf1Reason_em_2,[for=MembershipForm_cf1Reason2]').hide().val('');
        $('label[for=MembershipForm_cf1Reason2]').html('Reason <span style="color:red">*');
        $('label[for=MembershipForm_cf1Signatory_name2]').html('Signatory Name <span style="color:red">*');
        
    } else {
        $('#MembershipForm_cf1Reason2,#MembershipForm_cf1Reason_em_2,[for=MembershipForm_cf1Reason2]').show();
        $('label[for=MembershipForm_cf1Reason2]').html('Reason <span style="color:red">*');
        $('label[for=MembershipForm_cf1Signatory_name2]').html('Signatory Name <span style="color:red">*');
    }
}

function confirmLoadPersonInfoToUi(isMemberCheckbox) {
    Alerts.confirm({
        title: 'Confirm Action',
        content: 'Are you sure you want to make this patient as the Principal Holder?',
        callback: function (result) {
            if (result) {
                loadPersonInfoToUI();
                marriedFemale();
            } else {
                isMemberCheckbox.prop('checked', false);
            }
            signatoryFields();
            memberFields();
            initAddPatientAsDependentButton();
        }
    });
}

function loadPersonInfoToUI() {
    $('#MembershipForm_relation').find('option[value=""]').prop('selected', true);
    for (var property in personInfo) {
        if (personInfo.hasOwnProperty(property)) {
            var element = $('#MembershipForm_' + property);
            element.val(personInfo[property]);
        }
    }
}

function marriedFemale() {
    if ($('#MembershipForm_sex').val() == 'f' && $('#MembershipForm_civilStatus').val() == 'married') {
        $('#married-female-box').show();
    } else {
        $('#married-female-box').hide();
    }
}

function signByDiffPerson() {
    var fillCf1 = $('#MembershipForm_cf1Form').val();
    var isRepresentative = $('#MembershipForm_cf1Signatory_is_representative').val() == 1;

    return fillCf1 == 1 && isRepresentative;
}

//Added By Neil 2020
function signByDiffPerson2() {
    var fillCf1_2 = $('#MembershipForm_cf1Form').val();
    var isRepresentative_2 = $('#MembershipForm_cf1Signatory_is_representative2').val() == 1;

    return fillCf1_2 == 1 && isRepresentative_2;
}

function signatoryFields() {
    isIncapacitated();
    var fields = $('#MembershipForm_cf1Signatory_name,#MembershipForm_cf1Signatory_relation,#MembershipForm_cf1Other_relation,#MembershipForm_cf1Is_incapacitated');
    var reason_hide =   $('#MembershipForm_cf1Reason,#MembershipForm_cf1Reason_em_,[for=MembershipForm_cf1Reason]');
    var isSignedByDiffPerson = signByDiffPerson();

    if (isSignedByDiffPerson) {
        fields.show();
    } else {
        fields.hide();
        reason_hide.hide();
    }

    fields.each(function (index, element) {

        if ($(element).prop('type') == 'checkbox') {
            if (isSignedByDiffPerson)
                $(element).parent().show();
            else {
                $(element).parent().hide();
                $(element).prop('checked', false);
            }
        } else {
            var id = $(element).prop('id');
            if (isSignedByDiffPerson) {
                $('[for=' + id + ']').show();
            } else {
                $(element).val('');
                $('[for=' + id + ']').hide();
                $('#' + id + '_em_').hide();
            }
        }
    });

    cf1RelationFields();
}

function signatoryFields2() {
    isIncapacitated2();
    var fields2 = $('#MembershipForm_cf1Signatory_name2,#MembershipForm_cf1Signatory_relation2,#MembershipForm_cf1Other_relation2,#MembershipForm_cf1Is_incapacitated2');
    var reason_hide2 =   $('#MembershipForm_cf1Reason2,#MembershipForm_cf1Reason_em_2,[for=MembershipForm_cf1Reason2]');
    var isSignedByDiffPerson2 = signByDiffPerson2();

    if (isSignedByDiffPerson2) {
        fields2.show();
    } else {
        fields2.hide();
        reason_hide2.hide();
    }

    fields2.each(function (index, element) {

        if ($(element).prop('type') == 'checkbox') {
            if (isSignedByDiffPerson2)
                $(element).parent().show();
            else {
                $(element).parent().hide();
                $(element).prop('checked', false);
            }
        } else {
            var id = $(element).prop('id');
            if (isSignedByDiffPerson2) {
                $('[for=' + id + ']').show();
            } else {
                $(element).val('');
                $('[for=' + id + ']').hide();
                $('#' + id + '_em_').hide();
            }
        }
    });

    cf1RelationFields2();
}

function membershipFields() {
    var otherMembershipCategory = [12, 13, 17, 19, 20, 21],
        income = [12, 13],
        dateEffective = [23];

    var selectedCategory = $('#MembershipForm_pmrfMembershipCategory option:selected').val();
    var otherMemberCategoryInput = $('#MembershipForm_pmrfMembershipOther');
    var incomeInput = $('#MembershipForm_pmrfMembershipIncome');
    var dateEffectiveInput = $('#MembershipForm_pmrfMembershipEffectiveDate');

    if ($.inArray(parseInt(selectedCategory), otherMembershipCategory) >= 0)
        otherMemberCategoryInput.prop('readonly', false);
    else
        otherMemberCategoryInput.prop('readonly', true).val('');

    if ($.inArray(parseInt(selectedCategory), income) >= 0)
        incomeInput.prop('readonly', false);
    else
        incomeInput.prop('readonly', true).val('');

    if ($.inArray(parseInt(selectedCategory), dateEffective) >= 0) {
        dateEffectiveInput.prop('disabled', false);
    } else {
        dateEffectiveInput.prop('disabled', true).val('');
    }
}

function printCf1() {
    var nleft = 0;
    var ntop = 0;
    var url = baseUrl + '/index.php?r=phic/membership/printCf1/caseNr/' + $('#MembershipForm_encounterNr').val();
    window.open(url, "PMRF", "toolbar=no, status=no, menubar=no, width=" + screen.width + ", height=" + screen.height * 0.7 + ", location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
    
}

function printPmrf() {
    var nleft = 0;
    var ntop = 0;
    var url = baseUrl + '/index.php?r=phic/membership/printPmrf/caseNr/' + $('#MembershipForm_encounterNr').val();
    window.open(url, "PMRF", "toolbar=no, status=no, menubar=no, width=" + screen.width + ", height=" + screen.height * 0.7 + ", location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
}

function scrollTo(target) {
    $('html, body').animate({scrollTop: target}, {duration: 500});
}

function formatMoneyFields() {
    $('.money').format();
}

function formatPin() {
    $('.pin').mask("99-999999999-9");
}

function formatCalendar() {
    $('.calendar').datepicker({
        autoclose: true
    });
}

function cf1RelationFields() {
    if ($('#MembershipForm_cf1Signatory_relation option:selected').val() == 'O') {
        $('[for=MembershipForm_cf1Other_relation]').show();
        $('#MembershipForm_cf1Other_relation').show();
    } else {
        $('[for=MembershipForm_cf1Other_relation]').hide();
        $('#MembershipForm_cf1Other_relation').hide().val('');
    }
}

//Added By Neil 2020
function cf1RelationFields2() {
    if ($('#MembershipForm_cf1Signatory_relation2 option:selected').val() == 'O') {
        $('[for=MembershipForm_cf1Other_relation2]').show();
        $('#MembershipForm_cf1Other_relation2').show();
    } else {
        $('[for=MembershipForm_cf1Other_relation2]').hide();
        $('#MembershipForm_cf1Other_relation2').hide().val('');
    }
}

function memberFields() {

    var visibility = $('#MembershipForm_isMember').is(':checked') ? 'hide' : 'show';

    $('#MembershipForm_cf1Pin,#MembershipForm_relation,#MembershipForm_cf1Pin').each(function (index, element) {
        $(element)[visibility]();
        $('#' + element.id + '_em_')[visibility]();
        $('[for=' + element.id + ']')[visibility]();
    });
}

function addDependent() {
    var template = $('#dependent-template').html()
    var dependentsContainer = $('#dependents');

    dependentsContainer.append(Mustache.render(template, {}));

    formatPin();
    formatCalendar();
    pmrfDependentRelation_Change();
    deleteButton_Click();
}

function pmrfDependentRelation_Change() {
    $('.relation').on('change', function (e) {
        var item = $(this);
        if (item.val() == 'f')
            item.parent().parent().find('.sex').val('m');
        else if (item.val() == 'm')
            item.parent().parent().find('.sex').val('f');
    });
}

function deleteButton_Click() {
    $('.delete-button').on('click', function () {
        $(this).parent().parent().animate({opacity: 0.3}, {
            duration: 500, done: function () {
                $(this).remove();
            }
        });
    });
}

function deleteButtonOld_Click() {
    $('.delete-button-o').on('click', function () {
        var button = $(this);
        var row = button.parent().parent();

        Alerts.confirm({
            title: 'Confirm Action',
            content: 'Are you sure you want to delete this dependent?',
            callback: function (result) {
                if (result) {
                    $.ajax({
                        url: baseUrl + '/index.php?r=phic/membership/deleteDependent/id/' + row.find('.dependent-id').val(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.result) {
                                alert('Dependent deleted.');
                                row.animate({opacity: 0.3}, {
                                    duration: 500, done: function () {
                                        row.remove();
                                    }
                                });
                            } else {
                                alert('Unable to delete dependent.');
                            }
                        },
                        error: function () {
                            alert('Error deleting dependent.');
                        }
                    });
                }
            }
        });

    });
}

function validateDependents() {

    var hasErrors = false;

    var dependents = $('#dependents tr');
    var motherCount = 0,
        fatherCount = 0,
        spouseCount = 0,
        childrenCount = 0;

    var error = {
        children: '', spouse: '', father: '', mother: '',
        birthDate: '', firstName: '', lastName: '',
        fatherSex: '', motherSex: ''
    };

    dependents.each(function (index, tr) {
        var row = $(tr);
        var relation = row.find('.relation option:selected').val();
        var birthDate = row.find('.birth_date').val();
        var firstName = row.find('.first_name').val();
        var middleName = row.find('.middle_name').val();
        var lastName = row.find('.last_name').val();
        var sex = row.find('.sex').val();
        switch (relation) {
            case 'c' :
                childrenCount++;
                if (childrenCount > 3) {
                    error.children = 'Member can only have 3 Children as dependent.';
                }
                break;
            case 's' :
                spouseCount++;
                if (spouseCount > 1) {
                    error.spouse = 'Member can only have 1 Spouse as dependent.';
                }
                break;
            case 'f' :
                fatherCount++;
                if (fatherCount > 1) {
                    error.father = 'Member can only have 1 Father as dependent.';
                }
                break;
            case 'm' :
                motherCount++;
                if (motherCount > 1) {
                    error.mother = 'Member can only have 1 Mother as dependent.';
                }
                break;
        }

        if (birthDate.trim() == '') {
            error.birthDate = 'Birth Date is required.';
        }
        if (firstName.trim() == '') {
            error.firstName = 'First Name is required.';
        }

        if (lastName.trim() == '') {
            error.lastName = 'Last Name is required.';
        }

        if (relation == 'f' && sex == 'f') {
            error.fatherSex = 'Father cannot be a female.';
        }

        if (relation == 'm' && sex == 'm') {
            error.motherSex = 'Mother cannot be a male.';
        }

    });

    var errors = [];
    for (var property in error) {
        if (error.hasOwnProperty(property) && error[property].trim() != '') {
            errors.push(error[property]);
        }
    }

    if (errors.length > 0)
        hasErrors = true;

    if (hasErrors) {
        Alerts.error({
            title: 'Errors in PMRF Dependents.',
            content: errors
                .filter(function (data) {
                    return data.trim() != '';
                })
                .join('<br/>')
        });
    }

    return !hasErrors;
}