<?php
/* @var $this GrantAccountTypeController */
/* @var $dataProvider CActiveDataProvider */
$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;

$css = <<<CSS
body { padding-top: 0;}
CSS;

$cs->registerCss('css',$css);

$this->breadcrumbs = array(
	'Special Functions' => $baseUrl . '/main/spediens.php',
	'Credit Collection Guarantor Manager'
);

?>
<center>
<?php 
	echo CHtml::tag('div', array('style' => 'width:50%;'));
		$this->beginWidget(
			'bootstrap.widgets.TbBox',
			array(
				'title' => 'Manage Grant Accounts'
			)	
		);
			echo CHtml::openTag('table', array('border' => '1', 'style' => 'width:100%;'));
				echo CHtml::openTag('tr');
					echo CHtml::tag('td', array('icon' => 'fa fa-plus-circle'), '');
					echo CHtml::tag('td', array(), 'Serial Number');
					echo CHtml::tag('td', array(), 'Serial Number2');
				echo CHtml::closeTag('tr');
					echo CHtml::tag('td', array(), 'sample');
					echo CHtml::tag('td', array(), 'sample');
					echo CHtml::tag('td', array(), 'sample');
			echo CHtml::closeTag('table');	
		$this->endWidget();
	echo CHtml::closeTag('div');
 ?>
</center>