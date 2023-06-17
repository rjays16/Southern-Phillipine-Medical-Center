<?php
use SegHis\modules\dialysis\models\DialysisMachine;
use SegHis\modules\dialysis\models\DialysisDialyzerType;
use \SegHis\modules\dialysis\models\DialysisTransaction;
use \SegHis\modules\dialysis\models\DialysisTransactionForm;

class DialysisTransactionController extends Controller
{
    
    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    public function actionMakeTransaction($transactionNr)
    {
        $formModel = DialysisTransactionForm::findByTransactionNr($transactionNr);
        if ($_POST) {
            $formModel->setAttributes($_POST[CHtml::modelName($formModel)]);
            if ($formModel->validate()) {
                if ($formModel->save()) {
                    $message = $formModel->isNewRecord ? 'Saved successfully!' : 'Updated successfully!';
                    Yii::app()->user->setFlash('success', $message);
                    $this->redirect(array('dialysisTransaction/makeTransaction/transactionNr/' . $transactionNr));
                } else {
                    if(!$samedate){
                         Yii::app()->user->setFlash('error', 'Admitted/Discharge Date cannot be the same with other transaction.');
                    }else{
                         Yii::app()->user->setFlash('error', 'An Error occurred.');
                    }
                   
                }
            }
        }

        $this->render('makeTransaction', array(
            'model' => $formModel,
            'machines' => DialysisMachine::getAllMachine(),
            'dialyzers' => DialysisDialyzerType::getAllDialyzer(),
            'defaults' => self::getDefaults($formModel)
        ));
    }

    private static function getDefaults(DialysisTransactionForm $model)
    {

        $last = DialysisTransaction::getPatientLastTransaction($model->person->pid);

        if ($model->machineNr) {
            $machineNr = $model->machineNr;
        } else if ($model->previousTransaction->machine_nr) {
            $machineNr = $model->previousTransaction->machine_nr;
        } else if ($last) {
            $machineNr = $last->machine_nr;
        } else {
            $machineNr = '';
        }

        if ($model->dialyzerId) {
            $dialyzerId = $model->dialyzerId;
        } else if ($model->previousTransaction->dialyzer->dialyzer_id) {
            $dialyzerId = $model->previousTransaction->dialyzer->dialyzer_id;
        } else if ($last) {
            $dialyzerId = $last->dialyzer->dialyzer_id;
        } else {
            $dialyzerId = '';
        }

        return array(
            'machineNr' => $machineNr,
            'dialyzerId' => $dialyzerId
        );
    }

}