<?php

/**
 * This is the model class for table "seg_encounter_vital_sign".
 *
 * The followings are the available columns in table 'seg_encounter_vital_sign':
 * @property string $uuid
 * @property string $encounter_nr
 * @property string $date_monitor
 * @property string $temperature
 * @property integer $pulse_rate
 * @property integer $systolic
 * @property integer $diastolic
 * @property integer $respiratory
 * @property integer $oxygen_saturation
 * @property double $weight
 * @property double $height
 * @property string $vision
 * @property string $length
 * @property string $headCirc
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property CareEncounter $encounterNr
 */
class EncounterVitalSign extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_vital_sign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('uuid, encounter_nr', 'required'),
            array('pulse_rate, systolic, diastolic, respiratory, oxygen_saturation, is_deleted', 'numerical', 'integerOnly'=>true),
            array('weight, height', 'numerical'),
            array('uuid', 'length', 'max'=>36),
            array('encounter_nr', 'length', 'max'=>12),
            array('date_monitor, create_id, modify_id', 'length', 'max'=>35),
            array('temperature', 'length', 'max'=>10),
            array('vision, length, headCirc, create_dt, modify_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('uuid, encounter_nr, date_monitor, temperature, pulse_rate, systolic, diastolic, respiratory, oxygen_saturation, weight, height, vision, length, headCirc, create_id, create_dt, modify_id, modify_dt, is_deleted', 'safe', 'on'=>'search'),
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
            'encounterNr' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'uuid' => 'Uuid',
            'encounter_nr' => 'Encounter Nr',
            'date_monitor' => 'Date Monitor',
            'temperature' => 'Temperature',
            'pulse_rate' => 'Pulse Rate',
            'systolic' => 'Systolic',
            'diastolic' => 'Diastolic',
            'respiratory' => 'Respiratory',
            'oxygen_saturation' => 'Oxygen Saturation',
            'weight' => 'Weight',
            'height' => 'Height',
            'vision' => 'Vision',
            'length' => 'Length',
            'headCirc' => 'Head Circ',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'is_deleted' => 'Is Deleted',
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

        $criteria=new CDbCriteria;

        $criteria->compare('uuid',$this->uuid,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('date_monitor',$this->date_monitor,true);
        $criteria->compare('temperature',$this->temperature,true);
        $criteria->compare('pulse_rate',$this->pulse_rate);
        $criteria->compare('systolic',$this->systolic);
        $criteria->compare('diastolic',$this->diastolic);
        $criteria->compare('respiratory',$this->respiratory);
        $criteria->compare('oxygen_saturation',$this->oxygen_saturation);
        $criteria->compare('weight',$this->weight);
        $criteria->compare('height',$this->height);
        $criteria->compare('vision',$this->vision,true);
        $criteria->compare('length',$this->length,true);
        $criteria->compare('headCirc',$this->headCirc,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EncounterVitalSign the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public  function getVitalSigns($data){

        $criteria = new CDbCriteria();
        $criteria->select = "t.*,fn_get_personell_lastname_first_by_loginid(t.create_id) AS create_id ";
        $criteria->addCondition('is_deleted=0');
        $criteria->addColumnCondition(array(
            'encounter_nr' => $data['encounter_nr']
        ));
        $criteria->order = 'date_monitor DESC';
        return $this->findAll($criteria);
    }

}