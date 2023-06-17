<?php
Yii::app()->clientScript->registerScript('main', <<<JAVASCRIPT

JAVASCRIPT
    , CClientScript::POS_READY);

/*Search Modal*/
echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'search-patient-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            'style' => 'height:80%;width:75%;margin-left:-38%'
        )
    )
); 
?>
	<div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5 id="allotment-header">Search Patient</h5>
    </div>
    <div class="modal-body" style="max-height:80%;">
        <div class="gg" style="margin-left: 10px;">
            <div class="row-fluid">
                <?php $this->renderPartial('modals/_searchPersonModal', array('model' => $model)); ?>

            </div>
        </div>
    </div>
<?php
$this->endWidget();
echo CHtml::closeTag('div');
/*End Search Modal*/

/*Process Referral Entry Modal*/
echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'referral-entry-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            'style' => 'height:80%;width:50%',
        )
    )
);
?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5 id="referral-entry-header"></h5>
    </div>

    <div class="modal-body" style="max-height:80%;">
        <div class="gg" style="margin-left: 50px;">
            <div class="row-fluid">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <?php 
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'type' => 'primary',
                'label' => 'Save',
                'url' => '',
                'htmlOptions' => array(
                    'id' => 'btnSaveReferral'
                ),
            )
        ); ?>
    </div>
<?php 
$this->endWidget();
echo CHtml::closeTag('div');
/*End Process Referral Entry Modal*/

/* View Requests Modal */
echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'add-request-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            'style' => 'height:90%;width:85%;margin-left:-40%'
        )
    )
); 
?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5 id="add-request-header"></h5>
    </div>

    <div class="modal-body" style="max-height:80%;">
        <div class="gg" style="margin-left: 10px;">
            <div class="row-fluid">
            </div>
        </div>
    </div>
<?php
$this->endWidget();
echo CHtml::closeTag('div');
/* End View Requests Modal */

/* Grant Request Modal */
echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'grant-request-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            'style' => 'height:75%;width:75%;margin-left:-40%'
        )
    )
); 
?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5 id="grant-request-header"></h5>
    </div>

    <div class="modal-body" style="max-height:80%;">
        <div class="gg" style="margin-left: 10px;">
            <div class="row-fluid">
            </div>
        </div>
    </div>
<?php
$this->endWidget();
echo CHtml::closeTag('div');
/* End Grant Request Modal */
 ?>
