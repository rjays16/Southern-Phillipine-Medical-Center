<?php
/* @var $this ProgressController */
/* @var $model PdpuProgressNotes */
$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;
$cs->registerCss('progress-notes-css', <<<CSS
                body ul.breadcrumb{
                    margin-top: -48px;
                }
CSS
);

$js = <<<JAVASCRIPT
        
        function searchPerson(){
            var case_number = $('#ssEncounter_nr').val();
            var name = $('#ssName').val();
                jQueryDialogSearch = jQuery('#search-dialog')
                    .dialog({
                        modal: true,
                        title: 'Progress Note - ' + name + ' ' + '(Case #:'+case_number+')',
                        width: '80%',
                        height: '500',
                        open: function() {
                            jQuery('#search-dialog-frame').attr('src', 'index.php?r=socialService/progress/viewPatientEncounter&pdpup=1&encounter_nr='+case_number+'')
                        }
                    });
                    return false;
        }
        
        function loadPerson(response){
        
            $("#ssFinalDiagnosis").val(response.final_diagnosis);
            $("#ssNotesID").val(response.notes_id);
            $("#ssInformant").val(response.informant);
            $("#ssPurposeReasons").val(response.purpose_reasons);
            $("#ssActionTaken").val(response.action_taken);
            $("#ssProblemEnc").val(response.problem_encountered);
            $("#ssPlan").val(response.plan);
            $('#check_value').val('1');
            
            jQuery('#search-dialog').dialog('close');
            
        }
        
       $("document").ready(function(){
            $("#ssbtnPrint").click(function(e){
                var id = $("#ssNotesID").val();
                var enc = $("#ssEncounter_nr").val();
                var pid = $("#ssPid").val();
                var problem = $('#ssProblemEnc').val();


                e.preventDefault();
                window.open("modules/reports/reports/pdpu_progress_notes_form.php?id="+id+"&enc_nr="+enc+"&pid="+pid);
            });
            
            $("#ssbtnDelete").click(function(e){
                var id = $("#ssNotesID").val();
                if(window.confirm("Are you sure you want to delete this progress note?")){
                   window.location.href = "index.php?r=socialService/progress&deleted_id="+id;
                }
            });
            
            $("#ssbtnUpdate").click(function(e){
                var informant = $('#ssInformant').val();
                var venue = $('#ssVenue').val();
                var purpose = $('#ssPurposeReasons').val();
                var action = $('#ssActionTaken').val();
                var problem = $('#ssProblemEnc').val();
                var plan = $('#ssPlan').val();
               

          
                if(informant == '' || venue == '' || purpose == '' || action == '' || problem == '' || plan == '') {
                    alert('Please Fill-In all mandatory fields *');
                    return false;
                }
            });
        });
        
     
        
        
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);

$this->breadcrumbs = array(

    'Social Service' => $baseUrl . '/modules/social_service/social_service_main.php',
    'Progress Notes' => 'index.php?r=socialService/progress',
    'Update Progress Notes',
);

$this->pageTitle = '';
?>
    <!-- <h3 align="center">Progress Notes Form</h3>
    <hr> -->
    <button id="showProgressButton" class="btn btn-success" style="float: right;" onclick="searchPerson()">View Progress Notes</button>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'horizontalForm',
        'type' => 'horizontal'
    ));
$model2 = new FreeFormModel();
$form->errorSummary($model);
?>

    <fieldset>
        <legend>Update Progress Notes</legend>

        <?php echo CHtml::hiddenField('check[value]', '', array(
                'size' => 60,
                'maxlength' => 128
        )); ?>
        <?php echo $form->textFieldRow($model2, '',
            array(
                    'id' => 'date_time_ui',
                    'readonly' => 'readonly',
                    'value' => $patientdata['datetime']
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
        <?php echo $form->hiddenField($model, 'notes_id',
            array(
                'id' => 'ssNotesID',
                'readonly' => 'readonly',
                'type' => 'hidden'
            )); ?>
        <?php echo $form->textFieldRow($model, 'pid',
            array(
                'id' => 'ssPid',
                'readonly' => 'readonly'
            )); ?>
        <?php echo $form->hiddenField($model, 'encounter_nr',
            array(
                'id' => 'ssEncounter_nr',
                'readonly' => 'readonly',
                'type' => 'hidden'
            )); ?>
        <?php echo $form->textFieldRow($model2, '',
            array(
                'id' => 'ssName',
                'readonly' => 'readonly',
                'value' => $patientdata['patientname']
            ),
            array(
                'label' => 'Name', 
                'labelOptions' => array('style' => 'text-align:right')
            )
        ); ?>
        <?php echo $form->dropDownListRow($model2, '', CHtml::listData($ward::model()->findAll(), 'nr', 'name'),
                array('empty' => 'Select Ward Name',
                    'disabled' => 'disabled',
                    'id' => 'ssWard',
                    'options'=>array($patientdata['ward']=>array('selected'=>'selected'))
                ),
                array(
                    'label' => 'Ward <font color="#ff0000">*</font>', 
                    'labelOptions' => array('style' => 'text-align:right')
                )
            );
        ?>
        <?php echo $form->textFieldRow($model2, '',
            array(
                'id' => 'ssDateAdmission',
                'readonly' => 'readonly',
                'value' => $patientdata['date_admission']
            ),
            array(
                'label' => 'Date Admission <font color="#ff0000">*</font>', 
                'labelOptions' => array('style' => 'text-align:right')
            )
        ); ?>
        <?php echo $form->textAreaRow($model2, '', array(
                'id' => 'ssFinalDiagnosis',
                'class' => 'span4',
                'readonly' => 'true',
                'value' => $patientdata['final_diagnosis']
            ),
            array(
                'label' => 'Final Diagnosis <font color="#ff0000">*</font>', 
                'labelOptions' => array('style' => 'text-align:right')
            )
        ); ?>
        <?php echo $form->textFieldRow($model, 'informant', array(
                'id' => 'ssInformant',
        )); ?>
        <?php echo $form->textFieldRow($model, 'venue', array(
                'id' => 'ssVenue',
        )); ?>
        <?php echo $form->textAreaRow($model, 'purpose_reasons', array(
                'maxlength' => 573,
                'id' => 'ssPurposeReasons',
                'class' => 'span4'
        )); ?>
        <?php echo $form->textAreaRow($model, 'action_taken', array(
                'maxlength' => 573,
                'id' => 'ssActionTaken',
                'class' => 'span4'
        )); ?>
        <?php echo $form->textAreaRow($model, 'problem_encountered', array(
                'maxlength' => 573,
                'id' => 'ssProblemEnc',
                'class' => 'span4'
        )); ?>
        <?php echo $form->textAreaRow($model, 'plan', array(
                'maxlength' => 573,
                'id' => 'ssPlan',
                'class' => 'span4'
        )); ?>

    </fieldset>
    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton',
            array(
                'id' => 'ssbtnUpdate',
                'buttonType' => 'submit',
                'type' => 'primary',
                'label' => 'Update'
            ));
        ?>
        <?php   $this->widget('bootstrap.widgets.TbButton',
            array(
                'id' => 'ssbtnPrint',
                'label' => 'Print',
                'type' => 'primary'

            ));
        ?>
        <?php $this->widget('bootstrap.widgets.TbButton',
            array(
                'id' => 'ssbtnDelete',
                'label' => 'Delete',
                'type' => 'danger'
                //'url' => Yii::app()->createUrl('pdpu/progress')
            ));
        ?>

    </div>

    <div id="search-dialog" style="display: none;">
        <iframe id="search-dialog-frame" src="" style="height: 100%; width: 100%; border: none;">
        </iframe>
    </div>

<?php
$this->endWidget();
