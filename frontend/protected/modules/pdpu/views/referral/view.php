<?php
/* @var $this ReferralController */
/* @var $model SocialReferrals */

$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;
$cs->registerCss('sservice-added-css',<<<CSS
				body ul.breadcrumb{
                    margin-top: -48px;
                }
                body div#padding{
                    padding:10px;
                }

                table tbody tr td, table thead tr th{
                    font-size: 12px;
                }
CSS
);

$js = <<<JAVASCRIPT

$(document).ready(function(){
    enc_nr = $('#SocialReferrals_encounter_nr').val();
    if(enc_cr == "")
        enc_nr = 0;

    if(enc_nr != '')
    {
        $.getJSON('{$baseUrl}/index.php?r=pdpu/referral/checkMss/enc/'+enc_nr, function(response){
            if(response.length > 0)
            {
                $('#showAssessmentBtn').attr('disabled', false);
            }
            else
            {
                $('#showAssessmentBtn').attr('disabled', true);
            }
        });
    }
    else
    {
       $('#showAssessmentBtn').attr('disabled', true); 
    }
});

function printReferral(id, enc=0){
	window.open("modules/reports/reports/Social_referral_form.php?id="+id+"&enc_nr="+enc);
}

function showAssessment(){
    enc_nr = $('#SocialReferrals_encounter_nr').val();
    window.open("modules/reports/reports/PDPU_Assessment_Tool.php?enc_nr="+enc_nr);
}

JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);


$this->breadcrumbs=array(
    'PDPU' => $baseUrl . '/modules/social_service/pdpu_main.php',
    'Assessment and Referral Form' => 'index.php?r=pdpu/referral',
    $model->refer_id,
);
$this->pageTitle = 'PDPU - Assessment and Referral Form';
?>

<h3 align="center">Assessment and Referral Form</h3>
<hr/>

<button id="showAssessmentBtn" class="btn btn-success" style="float: right;" onclick="showAssessment()">View Assessment Form</button>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'horizontalForm',
    'type' => 'horizontal'
));

$form->errorSummary($model);
?>

    <fieldset>

        <legend>Edit Referral</legend>

        <?php echo $form->datePickerRow($model, 'refer_dt',
            array(
                'options' => array(
                    'clearBtn' => true,
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                )
            ),
            array(
                'prepend' => '<i class="fa fa-calendar"></i>'
            )); ?>
        <?php echo $form->textFieldRow($model, 'pid',
            array(
                'readonly' => 'readonly'
            ));
        ?>
        <?php echo $form->hiddenField($model, 'encounter_nr',
            array(
                'readonly' => 'readonly',
                'type' => 'hidden'
            ));
        ?>
        <?php echo $form->textFieldRow($model, 'name',
            array(
                'readonly' => 'readonly'
            ));
        ?>
        <?php echo $form->textAreaRow($model, 'refer_to',
            array(
                'class' => 'span4'
            ));?>
        <?php echo $form->textAreaRow($model, 'refer_diagnosis',
            array(
                'class' => 'span4'
            ));?>
        <?php echo $form->textAreaRow($model, 'refer_reason',
            array(
                'class' => 'span4'
            ));?>
        <?php echo $form->textAreaRow($model, 'refer_assessment',
            array(
                'class' => 'span4'
            ));?>
        <?php echo $form->textAreaRow($model, 'refer_intervention',
            array(
                'class' => 'span4'
            ));?>
     <?php echo CHtml::hiddenField('before' , $model->refer_intervention, array('id' => 'hiddenInput')); ?>
    </fieldset>

    <div class="form-actions">
        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'label' => 'Update'
            )
        ); ?>

        <?php
            if($model->encounter_nr == "")
            {
                $model->encounter_nr = 0;
            }
        ?>

        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'type' => 'primary',
                'label' => 'Print',
                'htmlOptions' => array(
                    'onclick' => 'printReferral('.$model->refer_id.','.$model->encounter_nr.')',
                ),
            )
        ); ?>

        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Back',
                'url' => Yii::app()->createUrl('pdpu/referral'),
            )
        ); ?>
    </div>

<?php
$this->endWidget();