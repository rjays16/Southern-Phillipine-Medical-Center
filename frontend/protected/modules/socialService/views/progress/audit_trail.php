<?php
/* @var $this ProgressController */
/* @var $model PdpuProgressNotes */

$baseUrl = Yii::app()->request->baseUrl;
$this->pageTitle = false;
$this->widget('bootstrap.widgets.TbGridView', array(
    'dataProvider' => $model->search(),
    'type' => 'bordered',
    'columns' => array(
        array(
            'name' => 'date_changed',
            'header' => 'Date',
            'type' => 'datetime',
            'filter' => false,
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align:center;'
            )
        ),
        array(
            'name' => 'action_type',
            'header' => 'Action Taken',
            'filter' => false,
            'value' => function($data) {
                switch ($data->action_type) {
                    case 'M':
                        return 'Modified';
                        break;
                    case 'D':
                        return 'Deleted';
                        break;
                    case 'C':
                        return 'Created';
                        break;
                }
            },
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align:center;'
            )
        ),
        array(
            'name' => 'login',
            'header' => 'Encoder',
            'filter' => false,
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align:center;'
            )
        )
    )
));

?>