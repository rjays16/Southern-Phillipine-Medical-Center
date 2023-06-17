<?php
$this->setPageTitle('Inventory Service');
$this->setPageSubTitle('List');

$this->beginWidget('application.widgets.SegBox', array(
	'title' => 'Inventory Service',
	'headerIcon' => 'fa fa-files-o',
	'headerButtons' => array(
		array(
			'class' => 'bootstrap.widgets.TbButton',
			'label' => 'Refresh',
			'type' => 'success',
			'icon' => 'fa fa-refresh',
			'url' => 'index.php?r=InventoryAPIServices/service',
		),
	),
));
if (!is_null($data)) {
		CVarDumper::dump($data,10,true);
}

$this->endWidget();