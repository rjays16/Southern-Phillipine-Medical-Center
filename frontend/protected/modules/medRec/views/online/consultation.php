<?php

$baseUrl = Yii::app()->request->baseUrl;

$cs = Yii::app()->clientScript;
$cs->registerCss('headCss', <<<CSS
        body{
            padding-top: 0;
        }
        .button-submit-consult {
            background-color: #24A0ED;
            border: none;
            color: white;
            padding: 10px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
CSS
);

$js = <<<JAVASCRIPT

var bNotDone = false;

function disableDoneRegister() {
    bNotDone = true;
}

$(document).ready(function(){
 
    $("#consult_dept").change(function () {
        var dept_nr = $( "#consult_dept" ).val();
        var select = $("#consult_dr_nr");

        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';

        if(dept_nr != ''){
            $.ajax({
                    url:baseUrl+"/DoctorList",
                data:{
                    dept_nr : dept_nr    
                },
                success: function(data){
                    var obj = JSON.parse(data);
                    $("#consult_dr_nr option").remove();

                    select.append(
                        $('<option>', {
                            value: '',
                            text: '-Select a Doctor-'
                        })
                    ).append(
                        $.map(obj.results, function(value,key) {

                            var name = value.name_last+", "+value.name_first;

                            var convertedName = name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                    return letter.toUpperCase();
                                });

                            convertedName = convertedName + " " + value.mid_initial;

                            return $('<option>', {
                                value: value.personell_nr,
                                text: convertedName,
                                "data-haswebex" : value.haswebexid
                            });
                        })
                    );
					
					let selected_dr = '$selected_doctor';
					if (selected_dr) {
						$("#consult_dr_nr").val('$selected_doctor');
					}
                }
            });
        }else{
            $("#consult_dr_nr option").remove();

            select.append(
                $('<option>', {
                    value: '',
                    text: '-Select a Doctor-'
                })
            );
        }

    });

    $("#consult_dr_nr").change(function(){
        var consult_dr_nr = $("#consult_dr_nr").find("option:selected").data('haswebex');

        $("#docHaswebex").val(consult_dr_nr);
    })

    $("#consult_dr_nr").prop("disabled", false);  
});
   
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);

$this->pageTitle = '';

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'                   => 'create-consultation-form',
    'type'                 => 'inline',
    'enableAjaxValidation' => false,
    'htmlOptions'          => array(
        'data-url' => $this->createUrl('/medRec/consultation/saveConsultation')
    )
));

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id'          => 'create-consultation-modal',
        'htmlOptions' => array(
            'data-backdrop' => 'static',
        ),
    )
);

echo \CHtml::hiddenField('pid', $pid);
echo \CHtml::hiddenField('consultId', $consultId);
echo \CHtml::hiddenField('docHaswebex', 0);

?>


<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h5 id="consultation-modal-header">New Online Consultation</h5>
</div>
<div class="modal-body" style="max-height:80%;">
    <div class="gg" style="margin-left: 10px;">

        <div class="row-fluid">
            <div class="span4">
                <font color="#ff0000"><?php echo CHtml::label('Consultation Date / Time ', 'consultationDateTime'); ?></font>
            </div>
            <div class="span8">
                <div>
                    <?php
                    $this->widget(
                        'bootstrap.widgets.TbDateTimePicker',
                        array(
                            'id'          => 'consultationDateTime',
                            'name'        => 'consultationDateTime',
                            'htmlOptions' => array(
                                'placeholder' => 'Select Date and Time',
                                'class'       => 'dateTimeField'
                            ),
                            'options'     => array(
                                'format'       => 'yyyy-mm-dd HH:ii P',
                                'startDate'    => date('Y-m-d'),
                                'todayBtn'     => 'linked',
                                'autoclose'    => true,
                                'showMeridian' => true
                            ),
                            'value'       => date('Y-m-d h:i A')
                        )
                    );
                    echo CHtml::textField('enc_date','',array('disabled' => 'disabled', 'id' => 'enc_date', 'style' => 'display:none'));
                    ?>
                </div>
            </div>
        </div>
        <br>
        <div class="row-fluid">
            <div class="span4">
                <label><font color="#ff0000">OR Number</font></label>
            </div>
            <div class="span8">
                <?php
                echo $form->textField($model, 'official_receipt_nr',
                    array(
                        'id'    => "official_receipt_nr",
                        'name'  => 'official_receipt_nr',
                        'value' => "Free Consultation"
                    )
                );
                ?>

                <select id="sel1">
                    <option>Free Consultation</option>
                </select>
            </div>
        </div>
        <br>
        <div class="row-fluid">
            <div class="span4">
                <?php echo $form->label($model, 'chief_complaint'); ?>
            </div>
            <div class="span8">
                <?php
                echo $form->textArea($model, 'chief_complaint',
                    array(
                        'id'    => "chief_complaint",
                        'name'  => 'chief_complaint',
                        'class' => 'input-xlarge',
                    )
                );
                ?>
            </div>
        </div>
        <br>
        <div class="row-fluid">
            <div class="span4"><?php echo $form->label($model, 'is_confidential'); ?></div>
            <div class="span8"><?php echo $form->checkBox($model,'is_confidential', array('value'=>1, 'uncheckValue'=>0)); ?></div>
        </div>

        <div class="row-fluid">
            <div class="span4"><label>History of Smoking</label></div>
            <div class="span8">
                <div class="span1">
                    <?php
                    echo $form->radioButton($model,'smoker_history', array(
                        'value'=>'yes',
                        'uncheckValue'=>null,
                        'class'=>"radioField"
                    ));
                    ?>
                </div>
                <div class="span1" style="margin-top: 3px"><?php echo 'Yes'; ?></div>
                <div class="span1">
                    <?php
                    echo $form->radioButton($model,'smoker_history', array(
                        'value'=>'no',
                        'uncheckValue'=>null,
                        'class' => "radioField"
                    ));
                    ?>
                </div>
                <div class="span1" style="margin-top: 3px"><?php echo 'No'; ?></div>
            </div>

        </div>

        <div class="row-fluid">
            <div class="span4"><label>History of Drinking</label></div>
            <div class="span8">
                <div class="span1">
                    <?php
                    echo $form->radioButton($model,'drinker_history', array(
                        'value'=>'yes',
                        'uncheckValue'=>null,
                        'class' => "radioField"
                    ));
                    ?>
                </div>
                <div class="span1" style="margin-top: 3px"><?php echo 'Yes'; ?></div>
                <div class="span1">
                    <?php
                    echo $form->radioButton($model,'drinker_history', array(
                        'value'=>'no',
                        'uncheckValue'=>null,
                        'class' => "radioField"
                    ));
                    ?>
                </div>
                <div class="span1" style="margin-top: 3px"><?php echo 'No'; ?></div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span4"><label><font color="#ff0000">Department</label></font></div>
            <div class="span8">
                <?php
                echo CHtml::dropDownList('consult_dept', '',$departmentList, array(
                    'prompt' => '-Select a Department-',
                    'id'     => 'consult_dept'
                ))
                ?>
            </div>

        </div>
        <div class="row-fluid">
            <div class="span4"><label>Consulting Physician</label></div>
            <div class="span8">
                <select class="form-control" id="consult_dr_nr" class="consult_dr_nr" name="consult_dr_nr">
                    <option value=''>-Select a Doctor-</option>

                </select>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?php
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'buttonType'  => 'submit',
            'type'        => 'primary',
            'label'       => 'Save',
            'url'         => '',
            'htmlOptions' => array(
                'id' => 'btnSaveConsult'
            ),
        )
    ); ?>
</div>
<script type="text/javascript">
    var loc = window.location;

    $('#create-consultation-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);

        var url = $this.data('url');
        var enc_nr = $("#btnSaveConsult").attr('data-encounter_nr');
        var loadingcontent = 'Creating Consultation. Please wait...';
        var successmsg = "Consultation was successfully saved";

        if(typeof enc_nr !== typeof undefined && enc_nr != ''){
            url = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/updateConsultation/encounter_nr/'+ enc_nr;
            loadingcontent = 'Updating Consultation. Please wait...';
            successmsg = "Consultation has been updated successfully";
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                Alerts.loading({content: loadingcontent });
            },
            success: function (data) {
                if (data.success === true) {
                    Alerts.warn({
                        title: 'Success!',
                        content: successmsg,
                        icon: 'fa-check-circle-o',
                        iconColor: '#2DCC70', actions: ''
                    });            
                    location.reload()
                } else {
                    Alerts.warn({
                        title: 'Error!',
                        content: data.errors,
                        icon: 'fa fa-times-circle-o',
                        iconColor: '#EC1F13',
                        callback: function (result) {
                            Alerts.close();
                        }
                    });
                    return false;
                }
            }
        });

        return false;
    });

	$(window).unload(function(e) {
		let flag = localStorage.getItem('bNotDone');
		if (flag && (flag == 1)) {
			bNotDone = (flag == 1);
			localStorage.removeItem('bNotDone');
		}
        if (!bNotDone) {
            let consultid = $("#consultId").val();
            var loc = window.location;
            $.ajax({            
                url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/signalDoneConsultRegister',
                type: 'POST',
                data: {consultId: consultid},
                dataType : 'json',
                async : false,
                success : function(response) {
                    console.log('Registration of consult request done!');
                }
            });
        }
        else {
            bNotDone = false;
        }        
	});	
</script>
<?php
$this->endWidget();
$this->endWidget();

?>