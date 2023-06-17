<?php
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

<h2 align="center">Manage Guarantor Accounts</h2>

<?php 
echo CHtml::tag('div');
	$this->beginWidget(
			'bootstrap.widgets.TbBox',
			array(
				'title' => 'List of Records'
			)	
		);

	$this->widget(
	    'bootstrap.widgets.TbButton',
	    array(
	        'label' => 'Add new',
	        'type' => 'primary',
	        'url' => $baseUrl .'/index.php?r=grantAccount/grantAccounts/create',
	        'icon' => 'fa fa-plus-circle',
	    )
	);

	$this->widget('bootstrap.widgets.TbGridView', array(
		'id'=>'grant-accounts-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'name',
			'title',
			// 'address',
			'accountTypeName',
			/*
			array(
				'class' => 'bootstrap.widgets.TbEditableColumn',
				'name' => 'name',
				'editable' => array(
					'options' => array(
						'mode' => 'popup',
						'inputclass' => 'span3',
						'type' => 'textarea',
						'placement' => 'right',
						'url' => Yii::app()->createUrl('grantAccount/grantAccounts/UpdateName'),
						)
					),
				'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all')
			),
			array(
				'class' => 'bootstrap.widgets.TbEditableColumn',
				'name' => 'title',
				'editable' => array(
					'options' => array(
						'mode' => 'popup',
						'inputclass' => 'span3',
						'type' => 'textarea',
						'placement' => 'right',
						'url' => Yii::app()->createUrl('grantAccount/grantAccounts/UpdateTitle'),
						)
					),
				'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all')
			),
			array(
				'class' => 'bootstrap.widgets.TbEditableColumn',
				'name' => 'address',
				'editable' => array(
					'options' => array(
						'mode' => 'popup',
						'inputclass' => 'span3',
						'type' => 'textarea',
						'placement' => 'right',
						'url' => Yii::app()->createUrl('grantAccount/grantAccounts/UpdateAddress'),
						)
					),
				'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all')
			),
			array(
				'class' => 'bootstrap.widgets.TbEditableColumn',
				'name' => 'accountTypeName',
				'editable' => array(
					'options' => array(
						'mode' => 'popup',
						'inputclass' => 'span3',
						'type' => 'textarea',
						'placement' => 'right',
						'url' => Yii::app()->createUrl('grantAccount/grantAccounts/UpdateAddress'),
						)
					),
				'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all')
			),
			*/ 
			array(
				'class' => 'bootstrap.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
				'buttons' => array(
						'update' => array(
							'visible' => 'true',
							'type' => ''
					),
						'delete' => array(
							'visible' => 'true',
							'type' => ''
					)
				),
			),
		),
	)); 
	$this->endWidget();
echo CHtml::closeTag('div');
?>
