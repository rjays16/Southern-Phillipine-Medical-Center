<?php
/* @var $this Controller */
/* @var $model \SegHis\modules\article\models\Article */

$this->setPageTitle('Article');
$this->setPageSubTitle('Create');
$this->renderPartial('_form', array(
    'model' => $model
));