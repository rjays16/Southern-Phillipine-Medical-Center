<?php
/* @var $this MedicalChartFollowUpController */
/* @var $model MedicalChartFollowUp */

$this->breadcrumbs=array(
	'Medical Chart Follow Ups'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List MedicalChartFollowUp', 'url'=>array('index')),
	array('label'=>'Create MedicalChartFollowUp', 'url'=>array('create')),
	array('label'=>'Update MedicalChartFollowUp', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete MedicalChartFollowUp', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage MedicalChartFollowUp', 'url'=>array('admin')),
);
?>

<h1>View MedicalChartFollowUp #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'pid',
		'encounter_nr',
		'refno',
		'date_request',
		'vshtwt',
		'hxpe',
		'remarks',
		'created_id',
		'created_dt',
		'modify_id',
		'modify_dt',
		'status',
	),
)); ?>
