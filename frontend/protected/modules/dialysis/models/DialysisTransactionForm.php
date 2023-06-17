<?php
namespace SegHis\modules\dialysis\models;

use SegHis\models\BillingDoctor;
use SegHis\models\CaseRatePackage;
use SegHis\models\encounter\Encounter;
use SegHis\models\EncounterCaseType;
use SegHis\models\Hospital;

class DialysisTransactionForm extends \CFormModel
{

    public $transactionNr;
    public $transactionDateTime;
    public $machineNr;
    public $transactionType;
    public $dialyzerSerialNr;
    public $dialyzerName;
    public $dialyzerId;
    public $numberOfReuse;
    public $isPhilHealth;
    public $dateTimeOut;
    public $samedate;
    public $isNewRecord;

    /* @var $person \Person */
    public $person;

    /* @var $encounter Encounter */
    public $encounter;

    /* @var $transaction DialysisTransaction */
    public $transaction;

    /* @var $previousTransaction DialysisTransaction */
    public $previousTransaction;

    /* @var $lastTransaction DialysisTransaction */
    public $lastTransaction;

    /* @var $preBill DialysisPrebill */
    public $preBill;

    public function rules()
    {
        return array(

            array('transactionNr, transactionDateTime, machineNr, dialyzerName,
            dialyzerId, numberOfReuse, dateTimeOut, isPhilHealth', 'required'),

            array('transactionDateTime,dateTimeOut', 'date', 'format' => 'MM/dd/yyyy hh:mm a'),

            array('transactionType', 'safe')

        );
    }

    public function attributeLabels()
    {
        return array(
            'dateTimeOut' => 'Date/Time Out',
            'dialyzerId'  => 'Dialyzer Type',
        );
    }

    public static function findByTransactionNr($transactionNr)
    {
        /* @var $preBill DialysisPrebill */
        $preBill = DialysisPrebill::model()->findByPk($transactionNr);

        if (!$preBill)
            return false;

        /* @var $encounter Encounter */
        $encounter = Encounter::model()->findByPk($preBill->encounter_nr);
        $person = $encounter->person;

        $model = new DialysisTransactionForm();
        $model->transactionNr = $transactionNr;
        $model->encounter = $encounter;
        $model->person = $person;
        $model->preBill = $preBill;
        $model->isPhilHealth = $preBill->bill_type == DialysisPrebill::BILL_TYPE_PHILHEALTH ? 1 : 0;

        /* @var $transaction DialysisTransaction */
        if ($transaction = DialysisTransaction::model()->findByPk($transactionNr)) {
            $model->transaction = $transaction;
            $model->transactionDateTime = ($transaction->transaction_date == "0000-00-00 00:00:00" || $transaction->transaction_date==null) ? date('m/d/Y h:i A') : self::formatDate($transaction->transaction_date) /*ADDED MARK 15. 2016*/;
            $model->machineNr = $transaction->machine->machine_nr;
            $model->transactionType = false;
            $model->dialyzerSerialNr = $transaction->dialyzer->dialyzer_serial_nr;
            $model->dialyzerName = $transaction->dialyzer->dialyzerInfo->name;
            $model->dialyzerId = $transaction->dialyzer->dialyzer_id;
            $model->numberOfReuse = $transaction->dialyzer_reuse;
            $model->isNewRecord = false;
        } else {
            $model->isNewRecord = true;
        }
        $model->previousTransaction = $model->getPreviousTransaction();
        $model->lastTransaction = $model->getPatientLastTransaction();
         $model->transactionDateTime = ($transaction->transaction_date == "0000-00-00 00:00:00" || $transaction->transaction_date == null) ? date('m/d/Y h:i A') : self::formatDate($transaction->transaction_date) /*ADDED MARK 15. 2016*/;   
        $model->dateTimeOut = ($transaction->datetime_out == null) ? date('m/d/Y h:i A') : self::formatDate($transaction->datetime_out)/*ADDED MARK 15. 2016*/ ;

        return $model;
    }

    public function saveDialyzer()
    {
        $dialyzer = $this->transaction->dialyzer;
        if (!$this->transaction) {
            $dialyzer = new DialysisDialyzer();
            $dialyzer->dialyzer_serial_nr = uniqid();
        }

        $dialyzer->dialyzer_id = $this->dialyzerId;
        $dialyzer->dialyzer_type = DialysisDialyzer::getDialyzerType($this->dialyzerName);

        if (!$dialyzer->save()) {
            $this->addErrors($dialyzer->getErrors());
            return false;
        }

        $this->dialyzerSerialNr = $dialyzer->dialyzer_serial_nr;
        return true;
    }

    public function saveTransaction($dialyzerSerialNr)
    {
        global $db;
        if($this->isPhilHealth == 1){
            $this->preBill->bill_type = DialysisPrebill::BILL_TYPE_PHILHEALTH;
        } else {
            $this->preBill->bill_type = DialysisPrebill::BILL_TYPE_NONE_PHILHEALTH;
        }

        if(!$this->preBill->save()) {
            $this->addErrors($this->preBill->getErrors());
            return false;
        }

        $transaction = $this->transaction;
        if (!$this->transaction) {
            $transaction = new DialysisTransaction();
            $transaction->transaction_nr = $this->transactionNr;
            $transaction->pid = $this->encounter->pid;
            $transaction->dialyzer_serial_nr = $dialyzerSerialNr;
            $transaction->machine_nr = $this->machineNr;
            $transaction->history = 'Created by ' . \Yii::app()->user->getId() . ' at ' . date('m-d-Y h:i:s A') . "\n";
            $transaction->dialyzer_reuse = $this->numberOfReuse;
        } else {
            $transaction->history = 'Created by ' . \Yii::app()->user->getId() . ' at ' . date('m-d-Y h:i:s A') . "\n";
        }

        $transaction->transaction_date = self::formatDate($this->transactionDateTime, 'Y-m-d H:i:s');
        $transaction->datetime_out = self::formatDate($this->dateTimeOut, 'Y-m-d H:i:s');

       $cond=explode('-', $this->transactionNr);


        if (self::formatDate($this->transactionDateTime, 'Y-m-d') > self::formatDate($this->dateTimeOut, 'Y-m-d')) {
          return false;
        }

        $query = "SELECT * FROM seg_dialysis_transaction WHERE transaction_nr LIKE '%".$cond[0]."%'
                    AND (transaction_date LIKE '%".self::formatDate($this->transactionDateTime, 'Y-m-d')."%'       
                    OR datetime_out LIKE '%".self::formatDate($this->dateTimeOut, 'Y-m-d')."%' 
                    OR transaction_date > '".self::formatDate($this->transactionDateTime, 'Y-m-d')."')";       


        $query2=$db->Execute($query);
        // die($query);

                    
        // var_dump($query2->FetchRow());die;
        // if (count($query) >= 1) {
        //   return false;
        // }

        if ($query2->FetchRow()) {
            return false;
        }
        if ($this->preBill->bill_type == DialysisPrebill::BILL_TYPE_PHILHEALTH) {
            if (!$this->saveOperation($transaction))
                return false;
        }

        if (!$transaction->save()) {
            $this->addErrors($transaction->getErrors());
            return false;
        }

        return true;
    }

    public function saveOperation(DialysisTransaction &$transaction)
    {
        $operation = DialysisOperation::model()->findByAttributes(array(
            'encounter_nr' => $this->preBill->encounter_nr
        ));

        if (!$operation) {
            $operation = new DialysisOperation();
            $operation->refno = DialysisOperation::getNewReferenceNumber();
        }

        $operation->encounter_nr = $this->preBill->encounter_nr;
        $operation->chrge_dte = self::formatDate($transaction->transaction_date, 'Y-m-d H:i:s');

        if (!$operation->save()) {
            $this->addErrors($operation->getErrors());
            return false;
        }

        //separate kay naay trigger, mulahi ang refno -_-
        /* @var $operation DialysisOperation */
        $operation = DialysisOperation::model()->findByAttributes(array(
            'encounter_nr' => $this->preBill->encounter_nr
        ));

        /* @var $operationItem DialysisOperationItem */
        if ($transaction->isNewRecord) {
            $operationItem = new DialysisOperationItem();
            $operationItem->entry_no = 1;//temporary lang ni na entry number ma ilisdan japon ni sa trigger
        } else {
            $operationItem = DialysisOperationItem::model()->findByAttributes(array(
                'refno' => $operation->refno,
                'entry_no' => $transaction->op_entry_no
            ));
        }

//        $hemo = DialysisOperationItem::getHemodialysisInfo();
        $hemoOp = DialysisOperationItem::getHemodialysisOperationInfo();

        if (EncounterCaseType::isHouseCase($this->preBill->encounter_nr)) {
            $multiplier = EncounterCaseType::HOUSE_CASE_PCF;
        } else {
            $multiplier = Hospital::info()->pcf;
        }

        $operationItem->refno = $operation->refno;
        $operationItem->ops_code = DialysisOperationItem::CODE_HEMODIALYSIS;
        $operationItem->op_date = self::formatDate($transaction->transaction_date, 'Y-m-d');
        $operationItem->rvu = $hemoOp->rvu;
        $operationItem->multiplier = $multiplier;
        $operationItem->chrg_amnt = $hemoOp->rvu * $multiplier;
        $operationItem->laterality = '';

        if (!$operationItem->save()) {
            $this->addErrors($operationItem->getErrors());
            return false;
        }

        $transaction->op_entry_no = DialysisOperationItem::findLastEntryByReferenceNo($operation->refno)->entry_no;

        return true;
    }

    public function saveDoctor()
    {
        $doctor = BillingDoctor::model()->findByAttributes(array(
            'encounter_nr' => $this->preBill->encounter_nr
        ));
        if (!$doctor) {
            $doctor = new BillingDoctor();
            $doctor->encounter_nr = $this->preBill->encounter_nr;
            $doctor->dr_role_type_nr = BillingDoctor::DOCTOR_ROLE_SURGEON;
            $doctor->dr_level = BillingDoctor::DOCTOR_LEVEL_1;
            $doctor->dr_nr = $this->preBill->request->requesting_doctor;
            $doctor->entry_no = 1;
            $doctor->days_attended = 0;
        }

        $hemo = DialysisOperationItem::getHemodialysisInfo($this->encounter->encounter_date);

        /* @var $operations DialysisOperation */
        $operations = DialysisOperation::model()->findByAttributes(array(
            'encounter_nr' => $this->preBill->encounter_nr
        ));

        $charge = $hemo->pf * count($operations->items);
        $doctor->dr_charge = $charge;

        if (!$doctor->save()) {
            $this->addErrors($doctor->getErrors());
            return false;
        }

        return true;
    }

    public function save()
    {
        /* @var $dbTransaction \CDbTransaction */
        $dbTransaction = \Yii::app()->db->beginTransaction();

        $dialyzerOk = $this->saveDialyzer();
        if (!$dialyzerOk) {
            $dbTransaction->rollback();
            return false;
        }

        $transactionOk = $this->saveTransaction($this->dialyzerSerialNr);
        if (!$transactionOk) {
            $dbTransaction->rollback();
            return false;
        }

        if ($this->preBill->bill_type == DialysisPrebill::BILL_TYPE_PHILHEALTH) {
            $doctorOk = $this->saveDoctor();
            if (!$doctorOk) {
                $dbTransaction->rollback();
                return false;
            }
        }

        $dbTransaction->commit();
        return true;
    }

    public static function formatDate($date, $format = 'm/d/Y h:i A', $whenNull = null)
    {
        $timeStamp = strtotime($date);
        if (strtotime($date) === false || $timeStamp === -1)
            return $whenNull;
        else
            return date($format, $timeStamp);
    }

    /**
     * Gets all the previous transaction for this encounter.
     * @return \CActiveRecord
     * @throws \CDbException
     */
    public function getPreviousTransaction()
    {
        /* @var DialysisPrebill $preBill */
        $preBill = DialysisPrebill::model()->findByPk($this->transactionNr);
        if (!$preBill)
            throw new \CDbException('Pre-Bill not found for transaction #' . $this->transactionNr . '.');

        $criteria = new \CDbCriteria();
        $criteria->with = array('preBill');//join with prebill to consider carry overs

        $criteria->addColumnCondition(array(
            'preBill.encounter_nr' => $preBill->encounter_nr
        ));

        $criteria->order = 't.create_time DESC';
        return DialysisTransaction::model()->find($criteria);
    }

    /**
     * If patient has past dialysis encounters
     * @return bool
     */
    public function hasPastDialysisEncounters()
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'pid' => $this->person->pid
        ));
        $criteria->addCondition('encounter_nr <> :encounterNr');
        $criteria->params = array_merge($criteria->params, array(
            ':encounterNr' => $this->encounter->encounter_nr
        ));
        $transactions = DialysisRequest::model()->find($criteria);
        return !empty($transactions);
    }

    /**
     * Gets the patient's last dialysis transaction.
     * @return null|DialysisTransaction
     * @throws \CDbException If this transaction have no pre-bills
     */
    public function getPatientLastTransaction()
    {

        /* @var DialysisPrebill $preBill */
        $preBill = DialysisPrebill::model()->findByPk($this->transactionNr);
        if (!$preBill)
            throw new \CDbException('Pre-Bill not found for transaction #' . $this->transactionNr . '.');

        return DialysisTransaction::model()->find(array(
            'condition' => 'pid=:pid',
            'params' => array(':pid' => $this->person->pid),
            'order' => 'transaction_date DESC'
        ));
    }

}