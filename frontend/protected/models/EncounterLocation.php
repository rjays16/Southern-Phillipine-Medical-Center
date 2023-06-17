<?php

/**
 * This is the model class for table "care_encounter_location".
 *
 * The followings are the available columns in table 'care_encounter_location':
 * @property integer $nr
 * @property string $encounter_nr
 * @property integer $type_nr
 * @property integer $location_nr
 * @property integer $group_nr
 * @property string $date_from
 * @property string $date_to
 * @property string $time_from
 * @property string $time_to
 * @property integer $discharge_type_nr
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $source_assign
 *
 * The followings are the available model relations:
 * @property Encounter $encounterNr
 * @property TypeLocation $typeNr
 */
class EncounterLocation extends CActiveRecord
{
    
    const TYPE_WARD = 2;
    const TYPE_ROOM = 4;
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'care_encounter_location';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('status, history, modify_time', 'required'),
                    array('type_nr, location_nr, group_nr, discharge_type_nr', 'numerical', 'integerOnly'=>true),
                    array('encounter_nr', 'length', 'max'=>12),
                    array('status', 'length', 'max'=>25),
                    array('modify_id, create_id', 'length', 'max'=>35),
                    array('source_assign', 'length', 'max'=>9),
                    array('date_from, date_to, time_from, time_to, create_time', 'safe'),
                    // The following rule is used by search().
                    // @todo Please remove those attributes that should not be searched.
                    array('nr, encounter_nr, type_nr, location_nr, group_nr, date_from, date_to, time_from, time_to, discharge_type_nr, status, history, modify_id, modify_time, create_id, create_time, source_assign', 'safe', 'on'=>'search'),
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
                    'encounterNr' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
                    'typeNr' => array(self::BELONGS_TO, 'TypeLocation', 'type_nr'),
            );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
            return array(
                    'nr' => 'Nr',
                    'encounter_nr' => 'Encounter Nr',
                    'type_nr' => 'Type Nr',
                    'location_nr' => 'Location Nr',
                    'group_nr' => 'Group Nr',
                    'date_from' => 'Date From',
                    'date_to' => 'Date To',
                    'time_from' => 'Time From',
                    'time_to' => 'Time To',
                    'discharge_type_nr' => 'Discharge Type Nr',
                    'status' => 'Status',
                    'history' => 'History',
                    'modify_id' => 'Modify',
                    'modify_time' => 'Modify Time',
                    'create_id' => 'Create',
                    'create_time' => 'Create Time',
                    'source_assign' => 'Source Assign',
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

            $criteria->compare('nr',$this->nr);
            $criteria->compare('encounter_nr',$this->encounter_nr,true);
            $criteria->compare('type_nr',$this->type_nr);
            $criteria->compare('location_nr',$this->location_nr);
            $criteria->compare('group_nr',$this->group_nr);
            $criteria->compare('date_from',$this->date_from,true);
            $criteria->compare('date_to',$this->date_to,true);
            $criteria->compare('time_from',$this->time_from,true);
            $criteria->compare('time_to',$this->time_to,true);
            $criteria->compare('discharge_type_nr',$this->discharge_type_nr);
            $criteria->compare('status',$this->status,true);
            $criteria->compare('history',$this->history,true);
            $criteria->compare('modify_id',$this->modify_id,true);
            $criteria->compare('modify_time',$this->modify_time,true);
            $criteria->compare('create_id',$this->create_id,true);
            $criteria->compare('create_time',$this->create_time,true);
            $criteria->compare('source_assign',$this->source_assign,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EncounterLocation the static model class
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
	
    /***
     * 
     * @param type $encounter_nr
     * @return Room No.
     */
    public static function getRoomNo($encounter_nr) {
        
        $criteria = new CDbCriteria();
        $criteria->condition = "encounter_nr = '{$encounter_nr}'";
        $criteria->addCondition("type_nr = ".self::TYPE_ROOM);
        $criteria->order = "date_from DESC";            
        $criteria->limit = 1;

        $result = self::model()->findAll($criteria);
        return !empty($result) ? $result[0]->location_nr : null;        
        
    }
    
    /****
     * 
     */
    public static function getWardNr($encounter_nr, $accom_date, $accom_time) {
        $criteria = new CDbCriteria();
        $criteria->condition = "encounter_nr = '{$encounter_nr}'";
        $criteria->addCondition("type_nr = ".self::TYPE_WARD);
        $criteria->addCondition("date_from < '{$accom_date}' OR (date_from = '{$accom_date}' AND time_from <= '{$accom_time}')");
        $criteria->addCondition("CASE WHEN date_to <> '0000-00-00' THEN date_to > '{$accom_date}' OR (date_to = '{$accom_date}' AND time_to >= '{$accom_time}') ELSE 1 END");
        $criteria->limit = 1;

        $result = self::model()->findAll($criteria);
        return !empty($result) ? $result[0]->location_nr : null;
    }
}
