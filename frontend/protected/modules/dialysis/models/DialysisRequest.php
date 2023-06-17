<?php
namespace SegHis\modules\dialysis\models;
/**
 * This is the model class for table "seg_dialysis_request".
 *
 * The followings are the available columns in table 'seg_dialysis_request':
 * @property string $encounter_nr
 * @property string $pid
 * @property string $request_date
 * @property integer $requesting_doctor
 * @property integer $attending_nurse
 * @property string $remarks
 * @property string $diagnosis
 * @property string $procedure
 * @property string $modify_id
 */
class DialysisRequest extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_dialysis_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, attending_nurse, diagnosis, procedure', 'required'),
            array('requesting_doctor, attending_nurse', 'numerical', 'integerOnly' => true),
            array('encounter_nr, pid', 'length', 'max' => 12),
            array('modify_id', 'length', 'max' => 50),
            array('request_date, remarks', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('encounter_nr, pid, request_date, requesting_doctor, attending_nurse, remarks, diagnosis, procedure, modify_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'person' => array(self::BELONGS_TO,'Person','pid'),
            'doctor' => array(self::HAS_ONE,'Personnel',array('nr' => 'requesting_doctor')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'encounter_nr' => 'Encounter Nr',
            'pid' => 'Pid',
            'request_date' => 'Request Date',
            'requesting_doctor' => 'Requesting Doctor',
            'attending_nurse' => 'Attending Nurse',
            'remarks' => 'Remarks',
            'diagnosis' => 'Diagnosis',
            'procedure' => 'Procedure',
            'modify_id' => 'Modify',
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
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new \CDbCriteria;

        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('request_date', $this->request_date, true);
        $criteria->compare('requesting_doctor', $this->requesting_doctor);
        $criteria->compare('attending_nurse', $this->attending_nurse);
        $criteria->compare('remarks', $this->remarks, true);
        $criteria->compare('diagnosis', $this->diagnosis, true);
        $criteria->compare('procedure', $this->procedure, true);
        $criteria->compare('modify_id', $this->modify_id, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DialysisRequest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return DialysisTransaction
     */
    public function getOldestPhilHealthTransaction()
    {
        $criteria = $this->_getPhicPreBillCriteria();
        $criteria->order = 'transaction_date ASC';
        return DialysisTransaction::model()->find($criteria);
    }

    /**
     * @return DialysisTransaction
     */
    public function getLatestPhilHealthTransaction()
    {
        $criteria = $this->_getPhicPreBillCriteria();
        $criteria->order = 'transaction_date DESC';
        return DialysisTransaction::model()->find($criteria);
    }

    private function _getPhicPreBillCriteria()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array('preBill');
        $criteria->addColumnCondition(array(
            'preBill.bill_type' => DialysisPrebill::BILL_TYPE_PHILHEALTH,
            'encounter_nr' => $this->encounter_nr
        ));
        return $criteria;
    }
}