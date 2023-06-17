<?php
/* @var $this GrantAccountTypeController */
/* @var $model GrantAccountType */

$this->breadcrumbs=array(
	'Grant Account Types'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List GrantAccountType', 'url'=>array('index')),
	array('label'=>'Create GrantAccountType', 'url'=>array('create')),
	array('label'=>'Update GrantAccountType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete GrantAccountType', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage GrantAccountType', 'url'=>array('admin')),
);
?>

<h1>View GrantAccountType #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type_name',
		'alt_name',
		'discount',
		'deleted',
		'date_created',
		'date_modified',
	),
)); ?>
