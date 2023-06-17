 <?php

/**
 * This is the model class for table "seg_encounter_location_addtl".
 *
 * The followings are the available columns in table 'seg_encounter_location_addtl':
 * @property string $encounter_nr
 * @property integer $entry_no
 * @property integer $room_nr
 * @property integer $group_nr
 * @property integer $bed_nr
 * @property string $days_stay
 * @property string $hrs_stay
 * @property string $rate
 * @property string $occupy_date
 * @property string $modify_id
 * @property string $occupy_date_to
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $occupy_date_from
 *
 * The followings are the available model relations:
 * @property CareEncounter $encounterNr
 */
class EncounterLocationAddtl extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_location_addtl';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, entry_no, room_nr, group_nr, bed_nr, days_stay, hrs_stay, rate', 'required'),
            array('entry_no, room_nr, group_nr, bed_nr', 'numerical', 'integerOnly'=>true),
            array('encounter_nr', 'length', 'max'=>12),
            array('days_stay, hrs_stay, rate', 'length', 'max'=>10),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('occupy_date, occupy_date_to, modify_dt, create_dt, occupy_date_from', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('encounter_nr, entry_no, room_nr, group_nr, bed_nr, days_stay, hrs_stay, rate, occupy_date, modify_id, occupy_date_to, modify_dt, create_id, create_dt, occupy_date_from', 'safe', 'on'=>'search'),
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
            'encounter_nr' => 'Encounter Nr',
            'entry_no' => 'Entry No',
            'room_nr' => 'Room Nr',
            'group_nr' => 'Group Nr',
            'bed_nr' => 'Bed Nr',
            'days_stay' => 'Days Stay',
            'hrs_stay' => 'Hrs Stay',
            'rate' => 'Rate',
            'occupy_date' => 'Occupy Date',
            'modify_id' => 'Modify',
            'occupy_date_to' => 'Occupy Date To',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'occupy_date_from' => 'Occupy Date From',
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

        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('entry_no',$this->entry_no);
        $criteria->compare('room_nr',$this->room_nr);
        $criteria->compare('group_nr',$this->group_nr);
        $criteria->compare('bed_nr',$this->bed_nr);
        $criteria->compare('days_stay',$this->days_stay,true);
        $criteria->compare('hrs_stay',$this->hrs_stay,true);
        $criteria->compare('rate',$this->rate,true);
        $criteria->compare('occupy_date',$this->occupy_date,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('occupy_date_to',$this->occupy_date_to,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('occupy_date_from',$this->occupy_date_from,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EncounterLocationAddtl the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 