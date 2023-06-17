<?php
/* @var $this MedicalChartFollowUpController */
/* @var $model MedicalChartFollowUp */
/* @var $form CActiveForm */

$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl;

$cs = Yii::app()->clientScript;
$css = <<<CSS
    body { padding-top: 0; }
    .box { margin: auto; width: 40%;}
    .button-spacing {margin-left: 1%;}
CSS;
$cs->registerCss('css',$css);
?>

<div class="form">
<?php 
	echo CHtml::tag('div', array('class' => 'box'));
	    $this->beginWidget(
	        'bootstrap.widgets.TbBox',
	        array(
	            'title' => 'New Follow Up'
	        )
	    );
     ?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'medical-chart-follow-up-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->datePickerRow($model,'date_request', array('options' => array('autoclose' => true),'htmlOptions' => array('value' => date('m/d/Y')))); ?>
	<?php echo $form->error($model,'date_request'); ?>

	<?php echo $form->labelEx($model,'vshtwt'); ?>
	<?php echo $form->textField($model,'vshtwt',array('size'=>25,'maxlength'=>25)); ?>
	<?php echo $form->error($model,'vshtwt'); ?>
	
	<?php echo $form->labelEx($model,'hxpe'); ?>
	<?php echo $form->textField($model,'hxpe',array('size'=>25,'maxlength'=>25)); ?>
	<?php echo $form->error($model,'hxpe'); ?>
	
	<?php echo $form->labelEx($model,'remarks'); ?>
	<?php echo $form->textArea($model,'remarks',array('rows'=>6, 'cols'=>50)); ?>
	<?php echo $form->error($model,'remarks'); ?>
	<br />
	<?php 
		$this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Cancel',
            'type' => 'default',
            'url' => 'index.php?r=industrialClinic/medicalChartFollowUp/index/caseNr/',
            'icon' => 'fa fa-mail-reply-all',
        ));

		$this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'label' => 'Submit',
            'type' => 'success',
            'icon' => 'fa fa-send',
            'htmlOptions' => array(
            	'class' => 'button-spacing',
            )
        )); 
    ?>


<?php $this->endWidget(); $this->endWidget();//TbBox?>

</div><!-- form -->