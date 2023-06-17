<?php
/* @var $this GrantAccountsController */
/* @var $model GrantAccounts */
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
				'title' => $head_title
			)
		);
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'grant-accounts-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php
		 echo $form->labelEx($model,'account_type_id');
		 echo $form->dropDownList($model, 'account_type_id', $option_grants, array('prompt'=>'-- Select Category --'));
		 echo $form->error($model,'account_type_id');
	 ?>

	<?php echo $form->labelEx($model,'name'); ?>
	<?php echo $form->textField($model,'name',array('size'=>30,'maxlength'=>30)); ?>
	<?php echo $form->error($model,'name'); ?>

	<?php echo $form->labelEx($model,'title'); ?>
	<?php echo $form->textField($model,'title',array('size'=>30,'maxlength'=>30)); ?>
	<?php echo $form->error($model,'title'); ?>
<!-- 
	<?php echo $form->labelEx($model,'address'); ?>
	<?php echo $form->textArea($model,'address',array('rows'=>6, 'cols'=>50)); ?>
	<?php echo $form->error($model,'address'); ?> -->

	<br />
	<?php 
			$this->widget('bootstrap.widgets.TbButton',
			    array(
			        'label' => 'Cancel',
			        'type' => 'default',
			        'url' => 'index.php?r=grantAccount/grantAccounts/admin',
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