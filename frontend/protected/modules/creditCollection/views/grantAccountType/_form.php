<?php
/* @var $this GrantAccountTypeController */
/* @var $model GrantAccountType */
/* @var $form CActiveForm */
$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;

$css = <<<CSS
body { padding-top: 0;}
.box { margin: auto; width: 40%;}
.button-spacing {margin-left: 1%;}
CSS;

$cs->registerCss('css',$css);

?>
<?php echo CHtml::tag('div', array('class' => 'box')); 
	$this->beginWidget(
			'bootstrap.widgets.TbBox',
			array(
				'title' => 'New Records'
			)
		);
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'grant-account-type-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	
		<?php echo $form->labelEx($model,'type_name'); ?>
		<?php echo $form->textField($model,'type_name',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'type_name'); ?>
	

	
		<?php echo $form->labelEx($model,'alt_name'); ?>
		<?php echo $form->textField($model,'alt_name',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'alt_name'); ?>

		<br />

		<?php echo $form->checkBox($model,'with_budget',array('style'=>"margin-right:10px")); ?>
		<?php echo "Check if with Budget Allocation"; ?>
		<?php echo $form->error($model,'with_budget'); ?>
	
		<br />
		<br />
		
		<?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	
		<?php 
			$this->widget('bootstrap.widgets.TbButton',
			    array(
			        'label' => 'Cancel',
			        'type' => 'default',
			        'url' => 'index.php?r=creditCollection/grantAccountType/admin',
			        'icon' => 'fa fa-mail-reply-all',
				)
			);

			$this->widget('bootstrap.widgets.TbButton',
			    array(
			        'buttonType' => 'submit',
		            'type' => 'success',
		            'icon' => 'fa fa-send',
		            'label' => 'Submit',
		            'htmlOptions' => array(
		                'class' => 'button-spacing',
		            )
			    )
			);
		?>
<?php $this->endWidget(); ?>

</div><!-- form -->

<?php $this->endWidget();
 echo CHtml::closeTag('div'); ?>