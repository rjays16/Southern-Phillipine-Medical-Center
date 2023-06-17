<?php

/* @var $form TbActiveForm */

$infoUrl = $this->createUrl('member/getPIN');

Yii::app()->getClientScript()->registerScript('member.pin.searchPatient', <<<JAVASCRIPT
$('#pid').on('change', function(e) {
    Alerts.loading({ content: 'Retrieving patient/member information' });
    window.location.href = '{$infoUrl}&pid=' + $(this).val();
});

$('#PhicMember_relation').on('change', function(e) {
    var that = this;
    $('#member-form .inherited').each(function() {
        if (this !== that) {
            $(this).prop('disabled', $(that).val() == 'M');
        }
    });
});

$('#emp').on('change', function(e) {
    var that = $(this);
    console.log(that.val());
    $('#employer_no').val(that.val());
});



// Mod by jeff 02-26-18 for using SearchEmployer API.
$("#btnSaveMemInfo").click(function(){

    form = $("#member-form");
    var elem = $(this);

    $.ajax({
      type: "POST",
      url: elem.data('url'),
      data: form.serialize(),
        dataType:'JSON',
        success: function (response) { 
                if (response.success === true) {                    
                form = $("#member-form");
                form.attr("action","{$infoUrl}&pid=" + $("#pid").val());
                form.submit();
                    // location.reload();
            }else{
                    $.notify(response.message);
            }        
            
        },
        beforeSave : function () {

             Alerts.loading({
                'title': 'Please wait...',
                content: 'Validating information.'
            });
        },


        error: function(jqXHR, textStatus, errorThrown) {
           // Some error messages.
        }
    });
});

JAVASCRIPT
    , CClientScript::POS_READY);
?>


<div class="form">

<?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'patient-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => false,
        'htmlOptions' => array(
            'class' => 'service-form'
        )
    ));



    /* @var $form TbActiveForm */
?>

    <?php 
        echo $form->errorSummary($model, '<p><strong>Too bad!</strong> The member information was not saved!</p>
            <p>Please fix the following input errors:</p>'); 
    ?>

    <input type="hidden" name="tab" value="patient" />
    <div class="control-group" style="margin-bottom: 30px">
        <label for="patient_search">
            Search for an existing patient record
        </label>
        <div class="row-fluid">
            <?php

                $this->widget('eclaims.widgets.PatientSearch',
                    array(
                        'name' => 'pid',
                        'value' => $person->pid,
                        'options' => array(
                            'allowClear' => true,
                        ),
                        'htmlOptions' => array(
                            'class' => 'input-xxlarge'
                        )
                    )
                );
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <legend>
                <h5>
                    Member information
                    <?php
                        Yii::import('bootstrap.widgets.TbButton');
                        $this->widget(
                            'bootstrap.widgets.TbButton',
                            array(
                                'buttonType' => TbButton::BUTTON_BUTTON,
                                'label' => '<i class="fa fa-pencil"></i> Edit',
                                'encodeLabel' => false,
                                'disabled' => !$person->pid || $hasFinalBill,
                                'url' => '#',
                                'size' => TbButton::SIZE_MINI,
                                'htmlOptions' => array(
                                    'class' => 'pull-right',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#member-form-modal',
                                    'data-tooltip' => 'tooltip',
                                    'title' => 'Edit member info',
                                ),
                            )
                        );
                    ?>
                </h5>
            </legend>
    <?php

    Yii::import('bootstrap.widgets.TbDetailView');
    $memCategpryHelpText = CHtml::tag('i', array(
        'class' => 'fa fa-question-circle', 
        'data-toggle' => 'tooltip', 
        'title' => '"Non-PHIC" Member Category is auto set to "Indigent"'
    ), '');
    $this->widget(
        'bootstrap.widgets.TbDetailView',
        array(
            'data' => $member,
            'type' => array(TbDetailView::TYPE_STRIPED, TbDetailView::TYPE_CONDENSED, TbDetailView::TYPE_BORDERED),
            'attributes' => array(
                array('name' => 'MemberRelation'),
                array('name' => 'insurance_nr'),
                array('name' => 'patient_pin'),
                array('name' => 'member_lname'),
                array('name' => 'member_fname'),
                array('name' => 'member_mname'),
                array('name' => 'suffix'),
                array('name' => 'birth_date', 'type' => 'date'),
                array('name' => 'MemberTypeDesc', 'label' => 'Member Category ' . $memCategpryHelpText),
            ),
        )
    );

    ?>
        </div>
    </div>
<?php $this->endWidget(); ?>
</div>

<?php

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'member-form-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            // 'data-dismiss' => false,
            'style' => 'width:650px;margin-left:-325px;'    
        )
    )
); 
?>

    <div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5>Member information</h5>
    </div>

<div class="modal-body" style="height:1000px;">
    <div class="gg" style="margin-left: 30px;">
        <?php
             $memberForm = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                'id' => 'member-form',
                'type' => 'horizontal',
                'enableAjaxValidation' => true,
                'clientOptions'=>array(
                    'validateOnSubmit'=>true,
                    'afterValidate' => "js:function(form, data, hasError) {
                        if(hasError == false) {
                            Alerts.loading({
                                content: 'Please wait. We are currently saving the member\'s record!'
                            });
                            return true;
                        }
                        return false;
                    }"
                ),
            ));

            echo CHtml::hiddenField('pid' , $person->pid);
            echo $memberForm->hiddenField($member, 'employer_no');
            echo $memberForm->hiddenField($member, 'employer_name');
            echo $memberForm->hiddenField($member, 'street_name', array(
                'value' => $person->street_name
            ));

            echo $memberForm->errorSummary($member, '<p><strong>Too bad!</strong> The member information was not saved!</p>
                    <p>Please fix the following input errors:</p>'); 
            ?>
            <div class="row-fluid">
                <?php
                echo $memberForm->select2Row($member, 'relation', array(
                    'data' => CMap::mergeArray(array(null => ''), EclaimsPhicMember::getRelationTypes()),
                    'options' => array(
                        'allowClear' => true
                    ),
                    'htmlOptions' => array(
                        'class' => 'input-large phicMemberRelation',
                        'data-orig-value' => $member->relation,
                        'class' => 'span9',
                    ),
                    'events' => array(
                        'change' => "js:function() {
                            var _this    = $(this),
                                relation = _this.val(),
                                _fields  = [
                                    'patient_pin','member_lname', 'member_fname', 'member_mname', 
                                    'suffix', 'sex', 'birth_date'
                                ];

                            /* Update Fields value */
                            var _updateFields = function(data) {
                                if(data || false) {
                                    $.each(data, function(key, value) {
                                        if (key === 'birth_date') {
                                            var date = new Date(value);
                                            value = date.toLocaleDateString();
                                        }
                                        $('#EclaimsPhicMember2_' + key).val(value);
                                    });
                                }
                            };

                            /* disble/enable fields if relation is 'M' */
                            var _disableFields = function(fields) {
                                $.each(fields, function(key, value) {
                                    var _inputField = $('#EclaimsPhicMember2_' + value);
                                    // if(relation == 'M') {
                                    //     _inputField.prop('readonly', true);
                                    //     if(_inputField.prop('type') == 'select-one') {
                                    //         _inputField.attr('readonly', 'readonly');
                                    //     }
                                    // }
                                    // else {
                                        _inputField.prop('readonly', false);
                                        if(_inputField.prop('type') == 'select-one') {
                                            _inputField.removeAttr('readonly');
                                        }
                                    // }
                                });
                            };

                            var _doAjaxCall = function() {
                                $.ajax({
                                    url: '{$this->createUrl('getPersonData')}',
                                    dataType: 'JSON',
                                    data: {pid: '{$person->getPID()}', relation: relation},
                                    success: function(data) {
                                        _updateFields(data);
                                    }
                                });
                            };

                            _this.data('original-data', relation);
                            if(relation == 'M') {
                                Alerts.confirm({
                                    title: 'Are you sure?',
                                    // content: 'Do you want to update the fields using the selected patient information?',
                                    content: 'This will update some fields using the data of the selected patient.',
                                    callback: function(result) {
                                        if(result) {
                                            _this.attr('data-orig-value', relation);
                                            _doAjaxCall();
                                        } else {
                                            _this.select2('val', _this.attr('data-orig-value'));
                                        }
                                    }
                                });
                            } else {
                                _this.attr('data-orig-value', relation);
                            }

                            _disableFields(_fields);
                        }"
                    )
                ));
                ?>
                </div>

                <div class="row-fluid">       
                    <?php
                        echo $memberForm->textFieldRow($member, 'insurance_nr', array(
                            'class' => 'input-medium span7',
                            'maxlength'=>12,
                            'placeholder'=>'maxlength 12 character',

                        ));
                    ?>
                </div>

                <?php 
                    echo $memberForm->textFieldRow($member, 'patient_pin', array(
                        // 'readonly' => ($member->relation == 'M'),
                        'class' => 'input-medium span7',
                        'maxlength'=>12,
                        'placeholder'=>'maxlength 12 character',
                    ));
                ?>
            <?php


            echo $memberForm->textFieldRow($member, 'member_lname', array(
                // 'readonly' => ($member->relation == 'M'),
                'class' => 'input-xlarge inherited span9'
            ));
            
            echo $memberForm->textFieldRow($member, 'member_fname', array(
                // 'readonly' => ($member->relation == 'M'),
                'class' => 'input-xlarge inherited span9'
            ));

            echo $memberForm->textFieldRow($member, 'member_mname', array(
                // 'readonly' => ($member->relation == 'M'),
                'class' => 'input-xlarge inherited span9'
            ));

            echo $memberForm->textFieldRow($member, 'suffix', array(
                // 'readonly' => ($member->relation == 'M'),
                'class' => 'input-small inherited span9'
            ));

            echo $memberForm->dropDownListRow($member, 'sex', EclaimsPhicMember::getGenderTypes(),
                array(
                // 'readonly' => ($member->relation == 'M'),
                'class' => 'input-small inherited',
                'empty' => '- SELECT -'
            ));


            Yii::import('eclaims.components.EclaimsFormatter');
            $formatter = Yii::createComponent(array(
                'class' => 'EclaimsFormatter',
                'dateFormat' => 'm/d/Y',
            ));

            if(!empty($member->birth_date)) {
                $member->birth_date = $formatter->formatDate($member->birth_date);
                 // $member->birth_date = date('m-d-Y',strtotime($member->birth_date));
            }

            echo $memberForm->datePickerRow($member, 'birth_date', array(
                'options' => array(
                    'language' => 'en',
                    'format' => 'mm/dd/yyyy',
                    'clearBtn' => true
                ),
                'htmlOptions' => array(
                'readonly' => ($member->relation == 'M'),
                'class' => 'input-medium inherited',
                'placeholder' => 'mm/dd/yyyy'
                )
            ), array(
                'hint' => 'e.g. month/day/year',
                'append' => '<i class="icon-calendar"></i>',
            ));

            ?>

           <!--workaround datetimepicker  <div class="controls"> 
            <?php 
                $this->widget('bootstrap.widgets.TbDatePicker',
                array(
                    'name' => 'EclaimsPhicMember[birth_date]',
                    'htmlOptions' => array('class'=>'searchI',
                        'placeholder'=>'Birth Date',
                        'onfocusout' =>"OnBlurData()",
                        'onclick'=>"getOtherDisable(this)",
                        'style' => 'display:none'
                        ),
                    ));
            ?>
            <div class="help-inline error" id="EclaimsPhicMember2_birth_date_em_" style="display:none"></div><p class="help-block">e.g. 01-01-2014</p></div> -->

            <?php

            echo $memberForm->select2Row($member, 'member_type', array(
                'data' => CMap::mergeArray(array(null => ''), EncounterMemcategory::getTypesToArray()),
                'options' => array(
                    'allowClear' => true
                ),
                'htmlOptions' => array(
                    'data-orig' => $member->member_type,
                    'class'     => 'input-large span9'
                ),
                'events' => array(
                    'change' => "js:function() {
                        var _this    = $(this),
                            type = _this.val(),
                            _fields  = [
                                'employer_no', 'employer_name','emp-wrapper' 
                            ];

                        /* disble fields if Member Type is neither 'S' or 'G' */
                        var _disableFields = function(fields) {
                            console.log(_this.data('orig'));

                            $.each(fields, function(key, value) {
                                var _inputField = $('#EclaimsPhicMember2_' + value);
                                /* 
                                    If 'S' or 'G', enable the fields and reset their values if
                                    the former member_type is not 'S' or 'G'.
                                */
                                $('#employer_no').prop('readonly' , true);

                                if($.inArray(type, ['S', 'G', 'K']) > -1) {
                                    _inputField.prop('readonly', false);


                                    $('.employerDetails').removeClass( 'hidden' );
                                    $('.employer-requirements').prop('readonly', false);    
                                    $('.required-for-employer').removeAttr(' hidden ');
                                    $('.required-for-employer').removeClass(' hidden ');
                                    $('.employer-requirements').val('');
                                    $('.employer-requirements').select2('data', null)

                                    if(_inputField.prop('type') == 'select-one') {
                                        _inputField.removeAttr('readonly');
                                    }
                                    /* 
                                        Reset the value, if the former member_type 
                                        is not 'S' or 'G'.
                                    */


                                    if($.inArray(_this.data('orig'), ['S', 'G', 'K']) < 0) {
                                        _inputField.val(_inputField.data('orig'));
                                    }
                                    $('#employer_no').prop('readOnly' , true);

                                }
                                else {
                                    _inputField.prop('readonly', true);

                                    $('.required-for-employer').removeAttr(' hidden ');
                                    $('.employer-requirements').val('');

                                    $('.required-for-employer').addClass(' hidden ');
                                    $('.employer-requirements').select2('data', null)

                                    if(_inputField.prop('type') == 'select-one') {
                                        // _inputField.attr('readonly', 'readonly');
                                    }
                                    _inputField.val('');

                                }
                                console.log(_inputField.data('orig'));
                            });

                            /* Caches the current member_type to orig/former value */
                            _this.data('orig', type);
                        };

                        _disableFields(_fields);
                        $('#employer_no').prop('readOnly' , true);

                    }"
                )
            ));

        ?>


        <?php

            $isShowEmployerSearch = in_array($member->member_type, EclaimsPhicMember::getRequiredMemberTypeByEmployer());

            echo \CHtml::tag('div', array(
                'class' => 'required-for-employer',
                'hidden' => !$isShowEmployerSearch,
            ));
            echo $memberForm->widgetRow('eclaims.widgets.EmployerSearch', array(
                'model' => $member,
                'attribute' => 'searchEmployer',
                'options' => array(
                    'allowClear' => true,
                ),
                'val' => CJSON::encode($member->employer_name),
                'htmlOptions' => array(
                    'id' => 'emp',
                    'class' => 'input-xxlarge span9 employer-requirements',
                    'data-orig' => $member->employer_name,
                    'value' => $person->pid
                ),
            ), array(
                'labelOptions' => array(
                    'label' => 'Search Employer'
                )
            ));

            
            echo $memberForm->textFieldRow(
                $member, 'employer_no', 
                array(
                    'class' => 'input-xxlarge span9 employer-requirements',
                    'id' => 'employer_no',
                    'readOnly' => true,
                    'maxlength' => '12'
                )
            );

            echo \CHtml::closeTag('div');


        ?>

        

        <?php $this->endWidget(); ?>
    </div>
</div>

 <div class="modal-footer">
        <?php 
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'type' => 'primary',
                'label' => 'Save changes',
                'url' => '',
                'htmlOptions' => array(
                    'id' => 'btnSaveMemInfo',
                    'data-url' => $this->createUrl('getEmployeeInfo')
                ),
            )
        ); ?>
        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Close',
                'url' => '#',
                'htmlOptions' => array('data-dismiss' => 'modal'),
            )
        ); ?>
    </div>

<?php $this->endWidget(); ?>