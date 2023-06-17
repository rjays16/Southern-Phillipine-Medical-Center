<?php
/* @var $this ManageController */

    $baseurl = Yii::app()->baseUrl;
    $cs = Yii::app()->clientScript;
    $cs->registerCss('packageManager-added-css',<<<CSS
                    body ul.breadcrumb{
                        margin-top: -48px;
                    }
                    body div#padding{
                        padding:10px;
                    }

                    table tbody tr td, table thead tr th{
                        font-size: 12px;
                    }
CSS
    );
?>

<center><h3>Manage Packages</h3></center>
<hr/>

<?php
    $this->pageTitle = "";
    $this->breadcrumbs = array(
        'Special Functions' => $baseurl . '/main/spediens.php',
        'Package Manager'
    );

    $this->widget('bootstrap.widgets.TbAlert', array(
        'block' => 'true',
        'fade' => 'true',
        'closeText' => '&times',
    ));

$this->widget('bootstrap.widgets.TbTabs', array(
    'type' => 'tabs',
    'tabs' => array(
        array(
            'label' => 'List of Packages',
            'content' => $this->renderPartial('lists',array(
                'dataProvider' => $data,
                'search' => $model,
            ), true),
            'active' => true,
        ),
        array(
            'label' => 'Add Package',
            'content' => $this->renderPartial('create', array(
                'model' => $model,
            ), true),
        ),
    ),
));
?>
