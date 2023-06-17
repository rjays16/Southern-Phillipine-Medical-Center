<?php
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
//var_dump($_SESSION);
?>

<center><h3><?php echo $model->package_name ?></h3></center>
<hr/>

<?php
    $this->pageTitle = "";
    $this->breadcrumbs = array(
        'Special Functions' => $baseurl . '/main/spediens.php',
        'Package Manager' => $baseurl . '/index.php?r=packageManager/manage',
        $model->package_id
    );

    $this->widget('bootstrap.widgets.TbAlert', array(
        'block' => 'true',
        'fade' => 'true',
        'closeText' => '&times',
    ));
?>

<div>

    <?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'detailsForm',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'well'
    ),
));

$form->errorSummary($model);
?>

    <table class="table table-condensed">
        <tr style="border-top: hidden">
            <td style="width: 150px;">
                <?php echo $form->labelEx($model, 'package_name', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
            </td>
            <td style="width: 10px;"></td>
            <td colspan="5">
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
            <td style="width: 10px;">
                <?php
                echo $form->checkBox($model, 'is_er');
                echo $form->error($model, 'is_er');
                ?>
            </td>

            <td >
                <?php echo $form->labelEx($model, 'is_opd', array('style' => 'font-weight: bold; text-align: center; color: #000;')); ?>
            </td>

            <td>
                <?php
                echo $form->checkBox($model, 'is_opd');
                echo $form->error($model, 'is_opd');
                ?>
            </td>

            <td>
                <?php echo $form->labelEx($model, 'is_dialysis', array('style' => 'font-weight: bold; text-align: center; color: #000;')); ?>
            </td>
            <td style="width: 10px;">
                <?php
                echo $form->checkBox($model, 'is_dialysis');
                echo $form->error($model, 'is_dialysis');
                ?>
            </td>

        </tr>

        <tr style="border-top: hidden">
            <td>
                <?php echo $form->labelEx($model, 'is_ipd', array('style' => 'font-weight: bold; text-align: right; color: #000;')); ?>
            </td>
            <td style="width: 10px;"></td>
            <td style="width: 10px;">
                <?php
                echo $form->checkBox($model, 'is_ipd');
                echo $form->error($model, 'is_ipd');
                ?>
            </td>

            <td>
                <?php echo $form->labelEx($model, 'is_hssc', array('style' => 'font-weight: bold; text-align: center; color: #000;')); ?>
            </td>

            <td>
                <?php
                echo $form->checkBox($model, 'is_hssc');
                echo $form->error($model, 'is_hssc');
                ?>
            </td>

            <td>
                <?php echo $form->labelEx($model, 'is_deleted', array('style' => 'font-weight: bold; text-align: center; color: #000;')); ?>
            </td>
            <td>
                <?php
               echo $form->checkBox($model, 'is_deleted');
               echo $form->error($model, 'is_deleted');
                ?>
            </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td colspan="3">
                <?php
                $this->widget('bootstrap.widgets.TbButton', array(
                    'label' => 'Update',
                    'type' => 'success',
                    'icon' => 'fa fa-edit',
                    'buttonType' => 'submit',
                    'htmlOptions' => array(
                        'style' => 'margin-top: 10px; width: 150px;'
                    ),
                ));
                ?>
            </td>
            <td>
                <?php echo $form->labelEx($model, 'Created By:', array('style' => 'font-weight: bold; text-align: right; color: #000; margin-top: 15px;'));?>
            </td>
            <td>
                <?php
                echo $form->textField($model, 'create_id', array('class' => 'span3', 'readOnly' => 'readOnly', 'style' => 'margin-top: 10px;'));
                echo $form->error($model, 'create_id');
                ?>
            </td>
            <td>
                <?php echo $form->labelEx($model, 'Modified By:', array('style' => 'font-weight: bold; text-align: right; color: #000; margin-top: 15px;'));?>
            </td>
            <td>
                <?php
                echo $form->textField($model, 'modify_id', array('class' => 'span3', 'readOnly' => 'readOnly', 'style' => 'margin-top: 10px;'));
                echo $form->error($model, 'modify_id');
                ?>
            </td>
        </tr>
    </table>

<?php
$this->endWidget();

$this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'Package Details',
    'headerIcon' => 'fa fa-list'
));

$this->renderPartial('v_create', array(
    'model' => $dt_model,
    'd_model' => $details_model,
    'totalCash' => $totalCash,
    'totalCharge' => $totalCharge,
));

$this->endWidget();
?>

</div>