<?php 
$baseUrl = Yii::app()->baseUrl;
$this->breadcrumbs = array(
	'Special Functions' => $baseUrl . '/main/spediens.php',
	'Credit Collection Manager' => $baseUrl .'/index.php?r=creditCollection/grantAccountType/admin',
	'Create Grant Account'
);
 ?>
<h1 align="center">Create Grant Account</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>