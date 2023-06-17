<?php
/* @var $this GrantAccountTypeController */
/* @var $model GrantAccountType */

// $this->breadcrumbs=array(
// 	'Grant Account Types'=>array('index'),
// 	'Manage',
// );

// $this->menu=array(
// 	array('label'=>'List GrantAccountType', 'url'=>array('index')),
	// array('label'=>'Create GrantAccountType', 'url'=>array('create')),
// );
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
	'Credit Collection Manager'
);

?>

<h2 align="center">Manage Grant Account Types</h2>
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
	        'url' => $baseUrl .'/index.php?r=creditCollection/grantAccountType/create',
	        'icon' => 'fa fa-plus-circle',
	    )
	);

	 $this->widget('bootstrap.widgets.TbGridView', array(
		'id'=>'grant-account-type-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'type_name',
			'alt_name',
			/*array(
				'class' => 'bootstrap.widgets.TbEditableColumn',
				'name' => 
				'editable' => array(
					'options' => array(
						'mode' => 'popup',
						'inputclass' => 'span3',
						'type' => 'textarea',
						'placement' => 'right',
						'url' => Yii::app()->createUrl('creditCollection/grantAccountType/UpdateTypeName'),
						)
					),
				'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all')
			),
			array(
				'class' => 'bootstrap.widgets.TbEditableColumn',
				'name' => 
				'editable' => array(
					'options' => array(
						'mode' => 'popup',
						'inputclass' => 'span3',
						'type' => 'textarea',	
						'placement' => 'right',
						'url' => Yii::app()->createUrl('creditCollection/grantAccountType/UpdateAltName'),
						)
					),
				'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all')
			),*/
			array(
				'class' => 'bootstrap.widgets.TbButtonColumn',
				/*'deleteConfirmation' => 'Do you want to delete this grant account?',*/
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
		)
	));
	
	$this->endWidget();
echo CHtml::closeTag('div');
?>
