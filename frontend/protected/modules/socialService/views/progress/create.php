<?php
/* @var $this ProgressController */
/* @var $model PdpuProgressNotes */
$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;
$cs->RegisterCss('progress-notes-css', <<<CSS
                    body ul.breadcrumb {
                        margin-top: -48px;
                    }
                    #search-person {
                        position: absolute;
                        margin-left: 410px;
                        margin-top: -46px;
                    }

CSS
);

$js = <<<JAVASCRIPT

        function convertTimeFormat(datetime){
            var datetimeformat = [];

            var month = datetime.getMonth()+1;
            var day = datetime.getDate();

            var output = datetime.getFullYear() + "-" +
                (month<10 ? "0" : "") + month + "-" +
                (day<10 ? "0" : "") + day;
        
            var hours = datetime.getHours(),
                hoursui = datetime.getHours(),
                minutes = datetime.getMinutes(),
                seconds = datetime.getSeconds(),
                ampm = 'AM';

            if(hours < 10){
                hours = '0'+hours;
                hoursui = '0'+hoursui;
            }
            else{
                if(hoursui > 12)
                    hoursui = hoursui-12;
                ampm = 'PM';
            }

            if(minutes < 10) minutes = '0'+minutes;

            if(seconds < 10) seconds = '0'+seconds;

            var time_ui = hoursui+':'+minutes+' '+ampm;
            var time = hours+':'+minutes+':'+seconds;

            datetimeformat['ui'] = output + " " + time_ui;
            datetimeformat['hidden'] = output + " " + time;

            return datetimeformat;
        }

        function searchPerson() {
            jQueryDialogSearch = jQuery('#search-dialog')
                .dialog({
                    modal: true,
                    title: 'Select a Person',
                    width: '80%',
                    height: '500',
                    open: function(){
                        jQuery('#search-dialog-frame').attr('src', 'index.php?r=person/search&pdpup=1')
                    }
                });
                return false;
        }

        function loadPerson(response){
            var d = new Date();
            var curdatetime = convertTimeFormat(d);
            
            var enc_date = new Date(response.encounter_date);
            var convEncDate = convertTimeFormat(enc_date);
            $("#date_time_ui").val(curdatetime['ui']);
            $("#progress_date_time").val(curdatetime['hidden']);
            $("#pid").val(response.pid);
            $("#patient_name").val(response.ordername);
            $("#encounter_nr").val(response.encounter_nr);
            // $("#date_admission").val(convEncDate['hidden']);
            $("#date_admission_ui").val(convEncDate['ui']);
            $("#ward").val(response.ward);
            $("#final_diagnosis").val(response.diagnosis);
            $("#encounter_type").val(response.encounter_type);
            $("#ssClassification").val(response.classification);
            
            $("#hiddenwardNum").val(response.ward);
            
            jQuery('#search-dialog').dialog('close');
            
        }

               $("document").ready(function(){
            
            $("#ssbtnSave").click(function(e){
                var encounter_type = $('#encounter_type').val();
                var ward = $("#ward").val();
                var date_admission = $("#date_admission_ui").val();
                var final_diagnosis = $("#final_diagnosis").val();
                var informant = $("#informant").val();
                var venue = $("#venue").val();
                var por_ob = $("#purpose_reasons").val();
                var acttaken = $("#action_taken").val();
                var prob_enc = $("#problem_encountered").val();
                var plan = $("#plan").val();
                              
            switch(encounter_type){
                 case "4":
                 case "3":
                 if(((ward == '' || date_admission == '') || (final_diagnosis == '' || informant=="")) || ((venue=="" || por_ob=="") || (acttaken=="" || prob_enc=="")) || plan==""){
                        alert("Unable to submit progress notes.fill in the mandatory fields *");
                        return false;
                    }
                    break;
                        
                case "1":
                case "2":  
                    if((((date_admission == '' || informant=="") || (venue=="" || por_ob=="")) || (acttaken==""|| plan=="")) || prob_enc==""){
                        alert("Unable to submit progress notes.fill in the mandatory fields *");
                        return false;
                    }
                    break;
                }

                return true;
            });
        });

    
JAVASCRIPT;




$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);

$this->breadcrumbs = array(
    'Social Service' => $baseUrl . '/modules/social_service/social_service_main.php',
    'Progress Notes' => 'index.php?r=socialService/progress',
    'New Progress Form',
);

$this->pageTitle = '';

?>

    <!-- <h3 align="center">Progress Notes Form</h3> -->
    <!-- <hr> -->
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'createForm',
        'type' => 'horizontal',
    ));
$model2 = new FreeFormModel();
$form->errorSummary($model);
?>
    <fieldset>
        <legend>New Progress Notes</legend>

        <?php echo $form->textFieldRow($model2, '',
            array(
                    'id' => 'date_time_ui',
                    'readonly' => 'readonly'
            ),
            array(
                'prepend' => '<i class="fa fa-calendar"></i>',
                'label' => 'Date and Time <font color="#ff0000">*</font>', 
                'labelOptions' => array('style' => 'text-align:right') 
            )); 
            echo $form->hiddenField($model, 'progress_date_time',
                array(
                    'id' => 'progress_date_time',
                    'type' => 'hidden'
                )
            );
        ?>
        <?php echo $form->textFieldRow($model, 'pid',
            array(
                'id' => 'pid',
                'readonly' => 'readonly'
            ));
        ?>
        <div id="search-person"><img id="select-enc" src="<?php $baseUrl ?>images/btn_encounter_small.gif" border="0"
                                     style="cursor: pointer;" onclick="searchPerson()"></div>
        <?php echo $form->hiddenField($model, 'encounter_nr',
            array(
                'id' => 'encounter_nr',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));
        ?>
        <?php echo $form->textFieldRow($model2, '',
            array(
                'id' => 'patient_name',
                'readonly' => 'readonly'
            ),
            array(
                'label' => 'Name', 
                'labelOptions' => array('style' => 'text-align:right')
            )
        );
        ?>
        <?php echo $form->dropDownListRow($model2, '', CHtml::listData($ward::model()->findAll(), 'nr', 'name'),
            array(
                'empty' => 'Select Ward Name',
                'disabled' => 'disabled',
                'id' => 'ward'
            ),
            array(
                'label' => 'Ward <font color="#ff0000">*</font>', 
                'labelOptions' => array('style' => 'text-align:right')
            )
        ); ?>
        <input type="hidden" id="hiddenwardNum" name="hiddenwardNum" value="<?php if(isset($_POST['PdpuProgressNotes'])){ echo $_POST['hiddenwardNum'];}?>">
        <?php echo $form->textFieldRow($model2, '',
                array(
                    'id' => 'date_admission_ui',
                    'readonly' => 'readonly'
                ),
                array(
                    'label' => 'Date Admission <font color="#ff0000">*</font>', 
                    'labelOptions' => array('style' => 'text-align:right')
                )
            );
         ?>
        <?php echo $form->textAreaRow($model2, '', array(
                'id' => 'final_diagnosis',
                'class' => 'span4',
                'readonly' => 'true'
            ),
            array(
                'label' => 'Final Diagnosis <font color="#ff0000">*</font>', 
                'labelOptions' => array('style' => 'text-align:right')
            )
        ); ?>
        <?php echo $form->textFieldRow($model, 'informant', array(
                'id' => 'informant',
        )); ?>
        <?php echo $form->textFieldRow($model, 'venue', array(
                'id' => 'venue',
        )); ?>
        <?php echo $form->textAreaRow($model, 'purpose_reasons', array(
                'maxlength' => 573,
                'id' => 'purpose_reasons',
                'class' => 'span4'
        )); ?>
        <?php echo $form->textAreaRow($model, 'action_taken', array(
                'maxlength' => 573,
                'id' => 'action_taken',
                'class' => 'span4'
        )); ?>
        <?php echo $form->textAreaRow($model, 'problem_encountered', array(
                'maxlength' => 573,
                'id' => 'problem_encountered',
                'class' => 'span4'
        )); ?>
        <?php echo $form->textAreaRow($model, 'plan', array(
                'maxlength' => 573,
                'id' => 'plan',
                'class' => 'span4'
        )); ?>
        <?php echo $form->hiddenField($model2, '',
            array(
                'id' => 'encounter_type',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));
        ?>
        <?php /*echo $form->hiddenField($model2, '',
            array(
                'id' => 'date_birth',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));*/
        ?>
        <?php /*echo $form->hiddenField($model, 'sex',
            array(
                'id' => 'sex',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));*/
        ?>
        <?php /*echo $form->hiddenField($model, 'civil_status',
            array(
                'id' => 'civil_status',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));*/
        ?>
        <?php /*echo $form->hiddenField($model, 'attending_physician',
            array(
                'id' => 'attending_physician',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));*/
        ?>
        <?php /*echo $form->hiddenField($model, 'classification',
            array(
                'id' => 'classification',
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));*/
        ?>

    </fieldset>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton',
            array(
                'id' => 'ssbtnSave',
                'buttonType' => 'submit',
                'type' => 'primary',
                'label' => 'Create'
            )); ?>
    </div>

    <div id="search-dialog" style="display: none;">
        <iframe id="search-dialog-frame" src="" style="height: 100%; width: 100%; border: none;">
        </iframe>
    </div>
<?php
$this->endWidget();