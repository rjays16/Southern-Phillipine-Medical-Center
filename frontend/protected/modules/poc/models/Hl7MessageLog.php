<?php
namespace SegHis\modules\poc\models;

use HL7\Message;
use SegHEIRS\modules\integrations\hl7\segments\MSH;
use SegHEIRS\modules\integrations\hl7\segments\MSA;
use SegHEIRS\modules\integrations\hl7\segments\PID;
use SegHEIRS\modules\integrations\hl7\segments\PV1;

/**
 * This is the model class for table "seg_hl7_message_log".
 *
 * The followings are the available columns in table 'seg_hl7_message_log':
 * @property string $log_id
 * @property string $message_id
 * @property string $message_type
 * @property string $ref_no
 * @property string $hl7_message
 * @property string $log_dt
 * @property string $ack_response
 * @property string $ack_message
 * @property string $target
 *
 * The followings are the available model relations:
 * @property CbgReading[] $cbgReadings
 */
class Hl7MessageLog extends \CareActiveRecord
{    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'seg_hl7_message_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('log_id', 'required'),
                    array('log_id', 'length', 'max'=>36),
                    array('message_id', 'length', 'max'=>25),
                    array('message_type', 'length', 'max'=>10),
                    array('ref_no', 'length', 'max'=>12),
                    array('ack_response', 'length', 'max'=>2),
                    array('target', 'length', 'max'=>100),
                    array('hl7_message, log_dt, ack_message', 'safe'),
                    // The following rule is used by search().
                    // @todo Please remove those attributes that should not be searched.
                    array('log_id, message_id, message_type, ref_no, hl7_message, log_dt, ack_response, ack_message, target', 'safe', 'on'=>'search'),
            );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                    'cbgReadings' => array(self::HAS_MANY, 'CbgReading', 'log_id'),
            );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
            return array(
                    'log_id' => 'Log',
                    'message_id' => 'Message',
                    'message_type' => 'Message Type',
                    'ref_no' => 'Ref No',
                    'hl7_message' => 'Hl7 Message',
                    'log_dt' => 'Log Dt',
                    'ack_response' => 'Ack Response',
                    'ack_message' => 'Ack Message',
                    'target' => 'Target',
            );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
            // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria=new \CDbCriteria;

            $criteria->compare('log_id',$this->log_id,true);
            $criteria->compare('message_id',$this->message_id,true);
            $criteria->compare('message_type',$this->message_type,true);
            $criteria->compare('ref_no',$this->ref_no,true);
            $criteria->compare('hl7_message',$this->hl7_message,true);
            $criteria->compare('log_dt',$this->log_dt,true);
            $criteria->compare('ack_response',$this->ack_response,true);
            $criteria->compare('ack_message',$this->ack_message,true);
            $criteria->compare('target',$this->target,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Hl7MessageLog the static model class
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    private static function getMessageControlId(Message $message) {
        // Extract MSH from message
        $segments = $message->getSegmentsByName('MSH');
        $forMSH = MSH::createFromSegment($segments[0]);
        return $forMSH->getMessageControlId();
    }  

    private static function getMessageType(Message $message) {
        // Extract MSH from message
        $segments = $message->getSegmentsByName('MSH');
        $forMSH = MSH::createFromSegment($segments[0]);
        return $forMSH->getMessageType();
    }         

    private static function getAcknowledgmentCode(Message $message) {
        // Extract MSA from message
        $segments = $message->getSegmentsByName('MSA');
        $forMSA = MSA::createFromSegment($segments[0]);
        return $forMSA->getAcknowledgmentCode();
    }

    private static function getAcknowledgedMessageId(Message $message) {
        // Extract MSA from message
        $segments = $message->getSegmentsByName('MSA');
        $forMSA = MSA::createFromSegment($segments[0]);
        return $forMSA->getMessageControlId();
    }        

    private static function getTarget(Message $message) {
        // Extract MSH from message
        $segments = $message->getSegmentsByName('MSH');
        $forMSH = MSH::createFromSegment($segments[0]);
        return $forMSH->getReceivingApplication();
    }

    private static function getEncounterNo(Message $message) {
        // Extract PID from message
        $segments = $message->getSegmentsByName('PID');
        $forPID = PID::createFromSegment($segments[0]);
        return $forPID->getPatientAccountNumber();
    }        

    private static function getOrderRefno(Message $message) {
        // Extract PV1 from message
        $segments = $message->getSegmentsByName('PV1');
        $forPV1 = PV1::createFromSegment($segments[0]);
        return $forPV1->getPreadmitNumber();
    }        

    private static function getUUID()
    {
        $sql = \Yii::app()->db->createCommand("SELECT UUID() as uuid");
        $res = $sql->queryRow();
        return !is_null($res['uuid']) ? $res['uuid'] : null;
    }

    public static function isValidHl7Msg(Message $message) {
        $mshSegments = $message->getSegmentsByName('MSH');
        $pidSegments = $message->getSegmentsByName('PID');
        return !empty($mshSegments) && !empty($pidSegments);            
    }

    /***
     * 
     */
    private static function getMsgRefNo($message) {        
        $refno = self::getOrderRefno($message);
        if (empty($refno)) {
            $pocOrder = new PocOrder();
            $orderRef = $pocOrder->getLatestStartOrder(self::getEncounterNo($message));
            if (!empty($orderRef)) {
                $refno = $orderRef->refno;
            }
        }               
        return $refno;
    }  
    
    /***
     * 
     * 
     */
    public static function logHl7Message(Message $message) {
        $transaction = \Yii::app()->getDb()->beginTransaction();           
        try {            
            $hl7Log = new Hl7MessageLog();

            $logId = self::getUUID();
            $hl7Log->log_id = $logId;

            $hl7Log->message_id = self::getMessageControlId($message);

            $msgType = self::getMessageType($message);
            if (is_array($msgType)) {
                $msgType = implode("^",$msgType);
            }                                
            $hl7Log->message_type = $msgType;                
            $hl7Log->ref_no = self::getMsgRefNo($message);                
            $hl7Log->hl7_message = $message->toString();
            $hl7Log->log_dt = new \CDbExpression('NOW()');
            $hl7Log->target = self::getTarget($message);
            if ( $hl7Log->save() ) {
                $transaction->commit();
                return $hl7Log;
            }
            else {
                $transaction->rollback();
                throw new \Exception("ERROR: ". var_export($hl7Log->getErrors(), true));
            }                
        } catch (\Exception $e) {
            if ($transaction->active) {
                $transaction->rollback();
            }
            fwrite(STDERR, "ERROR " . $e->getMessage() ."\n");
            return null;
        }                
    }

    public static function logAcknowledgement(Message $message) {
        $transaction = \Yii::app()->getDb()->beginTransaction();           
        try {                
            self::model()->updateAll(array('ack_response' => self::getAcknowledgmentCode($message), 'ack_message' => $message->toString()), "message_id = '".self::getAcknowledgedMessageId($message)."'"); 
            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            if ($transaction->active) {
                $transaction->rollback();
            }
            return false;
        }             
    }
        
        /***
         * 
         */
//        public static function getAcknowledgedMessage($refno) {
//            $user = Yii::app()->db->createCommand()
//                ->select('id, username, profile')
//                ->from('tbl_user u')
//                ->join('tbl_profile p', 'u.id=p.user_id')
//                ->where('id=:id', array(':id'=>$id))
//                ->queryRow();            
//        }
}