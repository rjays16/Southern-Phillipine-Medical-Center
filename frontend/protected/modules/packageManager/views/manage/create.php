<?php

$form = $this->beginWidget('CActiveForm', array(
    'id' => 'packageForm',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'well'
    ),
));

echo $form->errorSummary($model);
?>

<table class="table table-condensed">
    <tr style="border-top: hidden">
        <td style="width: 150px;">
            <?php echo $form->labelEx($model, 'package_name', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
        </td>
        <td style="width: 10px;"></td>
        <td>
            <?php
            echo $form->textField($model, 'package_name', array('class' => 'span5'));
            echo $form->error($model, 'package_name');
            ?>
        </td>
    </tr>

    <tr style="border-top: hidden">
        <td>
            <?php echo $form->labelEx($model, 'is_er', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
        </td>
        <td style="width: 10px;"></td>
        <td>
            <?php
            echo $form->checkBox($model, 'is_er');
            echo $form->error($model, 'is_er');
            ?>
        </td>
    </tr>

    <tr style="border-top: hidden">
        <td>
            <?php echo $form->labelEx($model, 'is_ipd', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
        </td>
        <td style="width: 10px;"></td>
        <td>
            <?php
            echo $form->checkBox($model, 'is_ipd');
            echo $form->error($model, 'is_ipd');
            ?>
        </td>
    </tr>

    <tr style="border-top: hidden">
        <td>
            <?php echo $form->labelEx($model, 'is_opd', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
        </td>
        <td style="width: 10px;"></td>
        <td>
            <?php
            echo $form->checkBox($model, 'is_opd');
            echo $form->error($model, 'is_opd');
            ?>
        </td>
    </tr>

    <tr style="border-top: hidden">
        <td>
            <?php echo $form->labelEx($model, 'is_hssc', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
        </td>
        <td style="width: 10px;"></td>
        <td>
            <?php
            echo $form->checkBox($model, 'is_hssc');
            echo $form->error($model, 'is_hssc');
            ?>
        </td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td>
            <?php
            $this->widget('bootstrap.widgets.TbButton', array(
                'label' => 'Submit',
                'type' => 'success',
                'icon' => 'fa fa-send',
                'buttonType' => 'submit',
                'htmlOptions' => array(
                    'style' => 'margin-top: 10px;'
                ),
            ));
            ?>
        </td>
    </tr>
</table>

<?php

$this->endWidget();
unset($form);