<?php
/* @var $this Controller */
/* @var $model \SegHis\modules\article\models\Article */
/* @var $cs CClientScript */

$cs = Yii::app()->clientScript;
$cs->registerCss('headCss', "body{padding-top:0}");

$this->setPageTitle('Article');
$this->setPageSubTitle('List');

$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Create new article',
    'type' => 'primary',
    'icon' => 'fa fa-pencil',
    'size' => 'large',
    'buttonType' => 'link',
    'url' => $this->createUrl('admin/create')
));

$this->widget('bootstrap.widgets.TbGridView', array(

    'afterAjaxUpdate' => "js:function(){
        jQuery('#SegHis_modules_article_models_Article_publish_date').datepicker({'autoclose':true,'language':'en'});
    }",

    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            'name' => 'publish_date',
            'type' => 'date',
            'htmlOptions' => array(
                'width' => 120
            ),
            'filter' => $this->widget('bootstrap.widgets.TbDatePicker', array(
                'model' => $model,
                'attribute' => 'publish_date',
                'options' => array(
                    'autoclose' => true
                )
            ), true)
        ),
        array(
            'name' => 'title',
            'htmlOptions' => array(
                'width' => 300
            )
        ),
        'preface',
        array(
            'name' => 'author',
            'htmlOptions' => array(
                'width' => 120
            )
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            // 'template' => '{update} {delete}', removed delete button by Ken 06/27/2016
            'template' => '{update}',
        )
    ),

));