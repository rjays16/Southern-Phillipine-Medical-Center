<?php
/* @var $this GrantAccountsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Grant Accounts',
);

$this->menu=array(
	array('label'=>'Create GrantAccounts', 'url'=>array('create')),
	array('label'=>'Manage GrantAccounts', 'url'=>array('admin')),
);
?>

<h1>Grant Accounts</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
