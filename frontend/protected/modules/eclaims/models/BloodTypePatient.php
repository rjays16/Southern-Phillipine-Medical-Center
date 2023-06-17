<?php

namespace SegHis\modules\eclaims\models;

\Yii::import('eclaims.models.BloodType');

/**
 * This is the model class for table "seg_blood_type_patient".
 *
 * The followings are the available columns in table 'seg_blood_type_patient':
 * @property string $pid
 * @property string $history
 * @property string $blood_type
 * @property string $create_id
 * @property string $create_tm
 * @property string $modify_id
 * @property string $modify_tm
 *
 * The followings are the available model relations:
 * @property BloodType $bloodType
 */
class BloodTypePatient extends \CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_blood_type_patient';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pid', 'required'),
            array('pid, blood_type', 'length', 'max' => 12),
            array('create_id, modify_id', 'length', 'max' => 60),
            array('history, create_tm, modify_tm', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('pid, history, blood_type, create_id, create_tm, modify_id, modify_tm', 'safe', 'on' => 'search'),
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
            'bloodType' => array(self::BELONGS_TO, 'BloodType', 'blood_type'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pid' => 'Pid',
            'history' => 'History',
            'blood_type' => 'Blood Type',
            'create_id' => 'Create',
            'create_tm' => 'Create Tm',
            'modify_id' => 'Modify',
            'modify_tm' => 'Modify Tm',
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

        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('blood_type', $this->blood_type, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_tm', $this->create_tm, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_tm', $this->modify_tm, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BloodTypePatient the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}