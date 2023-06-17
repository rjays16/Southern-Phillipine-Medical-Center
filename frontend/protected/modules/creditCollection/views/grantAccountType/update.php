<?php
/* @var $this GrantAccountTypeController */
/* @var $model GrantAccountType */
$baseUrl = Yii::app()->baseUrl;
$this->breadcrumbs = array(
	'Special Functions' => $baseUrl . '/main/spediens.php',
	'Credit Collection Manager' => $baseUrl .'/index.php?r=creditCollection/grantAccountType/admin',
	'Update Grant Account'
);
/*$this->breadcrumbs=array(
	'Grant Account Types'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List GrantAccountType', 'url'=>array('index')),
	array('label'=>'Create GrantAccountType', 'url'=>array('create')),
	array('label'=>'View GrantAccountType', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage GrantAccountType', 'url'=>array('admin')),
);*/
?>

<h1 align="center">Update Grant Account Type </h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>