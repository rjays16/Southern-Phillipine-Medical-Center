<?php
namespace SegHis\modules\poc\models;

use HL7\Message;
use EncounterLocation;
use SegHEIRS\modules\integrations\hl7\segments\PID;
use SegHEIRS\modules\integrations\hl7\segments\OBR;
use SegHEIRS\modules\integrations\hl7\segments\OBX;
use SegHis\modules\admission\models\assignment\Ward;

/**
 * This is the model class for table "seg_cbg_reading".
 *
 * The followings are the available columns in table 'seg_cbg_reading':
 * @property string $post_id
 * @property string $post_dt
 * @property string $encounter_nr
 * @property string $pid
 * @property string $ward_id
 * @property string $reading_dt
 * @property double $reading_level
 * @property string $readby_id
 * @property string $readby_name
 * @property string $log_id
 *
 * The followings are the available model relations:
 * @property CarePerson $p
 * @property CareEncounter $encounterNr
 * @property Hl7MessageLog $log
 */
class CbgReading extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'seg_cbg_reading';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('reading_level', 'numerical'),
                    array('post_id, log_id', 'length', 'max'=>36),
                    array('encounter_nr, pid', 'length', 'max'=>12),
                    array('ward_id', 'length', 'max'=>35),
                    array('readby_id', 'length', 'max'=>40),
                    array('readby_name', 'length', 'max'=>200),
                    array('post_dt, reading_dt', 'safe'),
                    // The following rule is used by search().
                    // @todo Please remove those attributes that should not be searched.
                    array('post_id, post_dt, encounter_nr, pid, ward_id, reading_dt, reading_level, readby_id, readby_name, log_id', 'safe', 'on'=>'search'),
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
                    'p' => array(self::BELONGS_TO, 'CarePerson', 'pid'),
                    'ward' => array(self::BELONGS_TO, 'SegHis\modules\admission\models\assignment\Ward', 'ward_id'),
                    'encounterNr' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
                    'log' => array(self::BELONGS_TO, 'Hl7MessageLog', 'log_id'),
            );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
            return array(
                    'post_id' => 'Post',
                    'post_dt' => 'Post Dt',
                    'encounter_nr' => 'Encounter Nr',
                    'pid' => 'Pid',
                    'ward_id' => 'Ward',
                    'reading_dt' => 'Reading Dt',
                    'reading_level' => 'Reading Level',
                    'readby_id' => 'Readby',
                    'readby_name' => 'Readby Name',
                    'log_id' => 'Log',
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

            $criteria->compare('post_id',$this->post_id,true);
            $criteria->compare('post_dt',$this->post_dt,true);
            $criteria->compare('encounter_nr',$this->encounter_nr,true);
            $criteria->compare('pid',$this->pid,true);
            $criteria->compare('ward_id',$this->ward_id,true);
            $criteria->compare('reading_dt',$this->reading_dt,true);
            $criteria->compare('reading_level',$this->reading_level);
            $criteria->compare('readby_id',$this->readby_id,true);
            $criteria->compare('readby_name',$this->readby_name,true);
            $criteria->compare('log_id',$this->log_id,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CbgReading the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
            
    private static function getEncounterNo(Message $message) {
        // Extract PID from message
        $segments = $message->getSegmentsByName('PID');
        $forPID = PID::createFromSegment($segments[0]);
        return $forPID->getPatientAccountNumber();
    }
    
    private static function getPid(Message $message) {
        // Extract PID from message
        $segments = $message->getSegmentsByName('PID');
        $forPID = PID::createFromSegment($segments[0]);
        return $forPID->getPatientIdentifierList();
    }       
    
    private static function getReadingDateTime(Message $message) {
        // Extract OBR from message
        $segments = $message->getSegmentsByName('OBR');
        $forOBR = OBR::createFromSegment($segments[0]);
        return $forOBR->getSpecimenReceivedDateTime();
    }

    private static function getOperator(Message $message) {
        // Extract OBX from message
        $segments = $message->getSegmentsByName('OBX');
        $forOBX = OBX::createFromSegment($segments[0]);
        return $forOBX->getResponsibleObserver();
    }    
    
    private static function getReading(Message $message) {
        // Extract OBX from message
        $segments = $message->getSegmentsByName('OBX');
        $forOBX = OBX::createFromSegment($segments[0]);
        return $forOBX->getObservationValue();
    }

    /***
     * 
     */
    public static function getCbgReadingsCount($refno) 
    {                                     
        $readings = \Yii::app()->db->createCommand()
            ->select('COUNT(*) tcount')
            ->from('seg_cbg_reading rd')
            ->join('seg_hl7_message_log hl7', 'rd.log_id = hl7.log_id')
            ->where('ref_no = :refNo', array(':refNo' => $refno))
            ->queryRow();
        return (!empty($readings) && isset($readings['tcount'])) ? $readings['tcount'] : 0;
    }
    
    private static function getCurrentWardId($encounter_nr, $read_dt, $read_tm) {                
        $ward_nr = EncounterLocation::getWardNr($encounter_nr, $read_dt, $read_tm);                            
        $ward = Ward::model()->findByPk($ward_nr);
        
        return !empty($ward) ? $ward->ward_id : null;
    }
    
    /***
     * 
     */
    public static function postCbgReading(Message $message, $logId = null) 
    {                                
        $transaction = \Yii::app()->getDb()->beginTransaction();           
        try {                                                            
            $cbgrdng = new CbgReading();
            $cbgrdng->post_id = new \CDbExpression('UUID()');
            $cbgrdng->post_dt = new \CDbExpression('NOW()');
            $cbgrdng->encounter_nr = CbgReading::getEncounterNo($message);
            $cbgrdng->pid = CbgReading::getPid($message);
            $cbgrdng->reading_level = CbgReading::getReading($message);
            
            $read_dt = CbgReading::getReadingDateTime($message);                                        
            $cbgrdng->reading_dt = $read_dt;
            
            // Get the current ward no. of patient ...
            $cbgrdng->ward_id = CbgReading::getCurrentWardId($cbgrdng->encounter_nr, date('Y-m-d',strtotime($read_dt)), date('H:i:s',strtotime($read_dt)));
            
            // Operator ...
            $operator = CbgReading::getOperator($message);
            if (is_array($operator)) {              
                $cbgrdng->readby_id = $operator[0];
                $operator = array_slice($operator, 1);
                
                if (count($operator) >= 2) {
                    $readby_name = $operator[0].", ".$operator[1];
                    $operator = array_slice($operator, 2);
                    $readby_name .= " ";
                    $readby_name .= implode(" ",$operator);
                }
                else {
                    $readby_name = $operator[0];
                }
                $cbgrdng->readby_name = $readby_name;
            }
            else {
                $cbgrdng->readby_id = $operator;
                $cbgrdng->readby_name = $operator;                
            }
            
            $cbgrdng->log_id = $logId;
            
            if ( $cbgrdng->save() ) {
                $transaction->commit();
                return true;
            }
            else {
                $transaction->rollback();
                throw new \Exception("ERROR: CBG reading cannot be saved!");
            }

        } catch (\Exception $e) {
            if ($transaction->active) {
                $transaction->rollback();
            }
            return false;
        }            
    }
}
