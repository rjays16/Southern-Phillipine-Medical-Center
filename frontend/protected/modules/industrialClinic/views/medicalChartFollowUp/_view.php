<?php
/* @var $this MedicalChartFollowUpController */
/* @var $data MedicalChartFollowUp */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pid')); ?>:</b>
	<?php echo CHtml::encode($data->pid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('encounter_nr')); ?>:</b>
	<?php echo CHtml::encode($data->encounter_nr); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('refno')); ?>:</b>
	<?php echo CHtml::encode($data->refno); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date_request')); ?>:</b>
	<?php echo CHtml::encode($data->date_request); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vshtwt')); ?>:</b>
	<?php echo CHtml::encode($data->vshtwt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('hxpe')); ?>:</b>
	<?php echo CHtml::encode($data->hxpe); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('remarks')); ?>:</b>
	<?php echo CHtml::encode($data->remarks); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_id')); ?>:</b>
	<?php echo CHtml::encode($data->created_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_dt')); ?>:</b>
	<?php echo CHtml::encode($data->created_dt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_id')); ?>:</b>
	<?php echo CHtml::encode($data->modify_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_dt')); ?>:</b>
	<?php echo CHtml::encode($data->modify_dt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	*/ ?>

</div>