<?php
/* @var $this MedicalChartFollowUpController */
/* @var $model MedicalChartFollowUp */

$this->breadcrumbs=array(
	'Medical Chart Follow Ups'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List MedicalChartFollowUp', 'url'=>array('index')),
	array('label'=>'Create MedicalChartFollowUp', 'url'=>array('create')),
	array('label'=>'View MedicalChartFollowUp', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage MedicalChartFollowUp', 'url'=>array('admin')),
);
?>

<h1>Update MedicalChartFollowUp <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>