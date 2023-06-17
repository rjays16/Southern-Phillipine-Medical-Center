<?php

/**
 * This is the model class for table "seg_consult_meeting".
 *
 * The followings are the available columns in table 'seg_consult_meeting':
 * @property string $id
 * @property string $consult_id
 * @property string $encounter_nr
 * @property integer $doctor_id
 * @property integer $is_valid
 * @property string $status
 * @property string $create_dt
 * @property string $create_id
 * @property string $modify_dt
 * @property string $modify_id
 * @property string $history
 *
 * The followings are the available model relations:
 * @property DoctorMeeting $doctor
 * @property int conf_notif_sent
 */
class ConsultMeeting extends CActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DONE = 'done';
    const STATUS_CONFIRMED = 'confirmed';

    const CONSULTATION_LIMIT = 5;

    const CONSULTATION_SENT = 1;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_consult_meeting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, consult_id, encounter_nr', 'required'),
            array('doctor_id, is_valid , conf_notif_sent', 'numerical', 'integerOnly' => true),
            array('id', 'default',
                'value'      => new CDbExpression('UUID()'),
                'setOnEmpty' => false, 'on' => 'insert'),
            array('id', 'length', 'max' => 36),
            array('consult_id', 'length', 'max' => 40),
            array('encounter_nr', 'length', 'max' => 12),
            array('status', 'length', 'max' => 9),
            array('create_id, modify_id', 'length', 'max' => 35),
            array('create_dt, modify_dt, history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, consult_id, encounter_nr, meeting_id ,meeting_url , doctor_id, is_valid, status, create_dt, create_id, modify_dt, modify_id, history', 'safe', 'on' => 'search'),
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
            'doctor'  => array(self::BELONGS_TO, 'DoctorMeeting', 'doctor_id'),
            'request' => array(self::HAS_ONE, 'ConsultRequest', 'consult_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'           => 'ID',
            'consult_id'   => 'Consult',
            'encounter_nr' => 'Encounter Nr',
            'doctor_id'    => 'Doctor',
            'is_valid'     => 'Is Valid',
            'status'       => 'Status',
            'create_dt'    => 'Create Dt',
            'create_id'    => 'Create',
            'modify_dt'    => 'Modify Dt',
            'modify_id'    => 'Modify',
            'history'      => 'History',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('consult_id', $this->consult_id, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('doctor_id', $this->doctor_id);
        $criteria->compare('is_valid', $this->is_valid);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('create_dt', $this->create_dt, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('modify_dt', $this->modify_dt, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('history', $this->history, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ConsultMeeting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
