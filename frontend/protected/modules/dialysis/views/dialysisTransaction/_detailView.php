<?php
use SegHis\modules\dialysis\models\DialysisPrebill;
use SegHis\modules\dialysis\models\DialysisTransactionForm;
/* @var $model \SegHis\modules\dialysis\models\DialysisTransactionForm */
/* @var $form DialysisActiveForm */
ob_start();
$this->widget('bootstrap.widgets.TbDetailView',array(
    'data' => $model,
    'attributes' => array(
        array(
            'label' => 'HRN',
            'value' => $model->person->pid,
        ),
        array(
            'label' => 'Name',
            'value' => $model->person->getFullName(),
        ),
        array(
            'label' => 'Birth Date',
            'value' => DialysisTransactionForm::formatDate($model->person->date_birth,'F j, Y'),
        )
    )
));
$detail1 = ob_get_clean();



ob_start();
$this->widget('bootstrap.widgets.TbDetailView',array(
    'data' => $model,
    'attributes' => array(
        array(
            'label' => 'Sex',
            'value' => $model->person->getSex(),
        ),
        array(
            'label' => 'Current Machine',
            'value' => $model->machineNr,
        ),
    )
));
$detail2 = ob_get_clean();

echo CHtml::tag('div',array('class' => 'clearfix'));
echo CHtml::tag('div',array('class' => 'span6'),$detail1,true);
echo CHtml::tag('div',array('class' => 'span6'),$detail2,true);
echo CHtml::closetag('div');//clearfix