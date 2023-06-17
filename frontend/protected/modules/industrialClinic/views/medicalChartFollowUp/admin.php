<?php
/* @var $this MedicalChartFollowUpController */
/* @var $model MedicalChartFollowUp */

$this->breadcrumbs=array(
	'Medical Chart Follow Ups'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List MedicalChartFollowUp', 'url'=>array('index')),
	array('label'=>'Create MedicalChartFollowUp', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#medical-chart-follow-up-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Medical Chart Follow Ups</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'medical-chart-follow-up-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'pid',
		'encounter_nr',
		'refno',
		'date_request',
		'vshtwt',
		/*
		'hxpe',
		'remarks',
		'created_id',
		'created_dt',
		'modify_id',
		'modify_dt',
		'status',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
