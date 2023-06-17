<?php 
$baseUrl = Yii::app()->baseUrl;
$this->breadcrumbs = array(
	'Special Functions' => $baseUrl . '/main/spediens.php',
	'Credit Collection Guarantor Manager' => $baseUrl .'/index.php?r=grantAccount/grantAccounts/admin',
	'Create Collection Guarantor'
);

 ?>
<h1 align="center">Create Grant Accounts</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'option_grants'=> $option_grants, 'head_title' => 'New Records')); ?>