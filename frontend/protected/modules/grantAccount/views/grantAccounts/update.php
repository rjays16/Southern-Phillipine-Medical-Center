<?php
/* @var $this GrantAccountsController */
/* @var $model GrantAccounts */
$baseUrl = Yii::app()->baseUrl;
$this->breadcrumbs = array(
	'Special Functions' => $baseUrl . '/main/spediens.php',
	'Credit Collection Guarantor Manager' => $baseUrl .'/index.php?r=grantAccount/grantAccounts/admin',
	'Update Collection Guarantor'
);

/*
$this->breadcrumbs=array(
	'Grant Accounts'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List GrantAccounts', 'url'=>array('index')),
	array('label'=>'Create GrantAccounts', 'url'=>array('create')),
	array('label'=>'View GrantAccounts', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage GrantAccounts', 'url'=>array('admin')),
);
*/ 
?>

<h1 align="center">Update Grant Accounts</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'option_grants'=> $option_grants, 'head_title' => 'Update Records')); ?>