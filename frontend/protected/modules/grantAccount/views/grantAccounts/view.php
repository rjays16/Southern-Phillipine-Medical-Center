<?php
/* @var $this GrantAccountsController */
/* @var $model GrantAccounts */

$this->breadcrumbs=array(
	'Grant Accounts'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List GrantAccounts', 'url'=>array('index')),
	array('label'=>'Create GrantAccounts', 'url'=>array('create')),
	array('label'=>'Update GrantAccounts', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete GrantAccounts', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage GrantAccounts', 'url'=>array('admin')),
);
?>

<h1>View GrantAccounts #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'title',
		'address',
		'locked',
		'deleted',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'account_type_id',
	),
)); ?>
