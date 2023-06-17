<?php

/**
 * This is the model class for table "seg_doctor_meeting".
 *
 * The followings are the available columns in table 'seg_doctor_meeting':
 * @property integer $doctor_id
 * @property string $site_name
 * @property string $webex_id
 * @property string $password
 * @property string $create_dt
 * @property string $create_id
 * @property string $modified_dt
 * @property string $modify_id
 *
 * The followings are the available model relations:
 * @property ConsultMeeting[] $consultMeetings
 * @property CarePersonell $doctor
 */
class DoctorMeeting extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_doctor_meeting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('doctor_id', 'numerical', 'integerOnly' => true),
            array('password', 'length', 'max' => 150),
            array('create_id, modify_id', 'length', 'max' => 36),
            array('site_name, webex_id, create_dt, modified_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('doctor_id, site_name, webex_id, password, create_dt, create_id, modified_dt, modify_id', 'safe', 'on' => 'search'),
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
            'consultMeetings' => array(self::HAS_MANY, 'ConsultMeeting', 'doctor_id'),
            'doctor'          => array(self::BELONGS_TO, 'CarePersonell', 'doctor_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'doctor_id'   => 'Doctor',
            'site_name'   => 'Site Name',
            'webex_id'    => 'Webex',
            'password'    => 'Password',
            'create_dt'   => 'Create Dt',
            'create_id'   => 'Create',
            'modified_dt' => 'Modified Dt',
            'modify_id'   => 'Modify',
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

        $criteria->compare('doctor_id', $this->doctor_id);
        $criteria->compare('site_name', $this->site_name, true);
        $criteria->compare('webex_id', $this->webex_id, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('create_dt', $this->create_dt, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('modified_dt', $this->modified_dt, true);
        $criteria->compare('modify_id', $this->modify_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DoctorMeeting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
