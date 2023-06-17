<?php
namespace SegHis\models;
/**
 * This is the model class for table "seg_encounter_case".
 *
 * The followings are the available columns in table 'seg_encounter_case':
 * @property string $encounter_nr
 * @property integer $casetype_id
 * @property integer $is_deleted
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 */
class EncounterCaseType extends \CareActiveRecord
{

    const PRIVATE_CASE = 1;
    const HOUSE_CASE = 2;
    const HOUSE_CASE_PCF = 40;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_case';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, casetype_id, modify_dt', 'required'),
            array('casetype_id, is_deleted', 'numerical', 'integerOnly' => true),
            array('encounter_nr', 'length', 'max' => 12),
            array('modify_id, create_id', 'length', 'max' => 35),
            array('create_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('encounter_nr, casetype_id, is_deleted, modify_id, modify_dt, create_id, create_dt', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'encounter_nr' => 'Encounter Nr',
            'casetype_id' => 'Casetype',
            'is_deleted' => 'Is Deleted',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
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
        $criteria->compare('casetype_id', $this->casetype_id);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_dt', $this->modify_dt, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_dt', $this->create_dt, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EncounterCaseType the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function isHouseCase($encounterNr)
    {
        /* @var $type EncounterCaseType */
        $type = EncounterCaseType::model()->findByAttributes(array(
            'encounter_nr' => $encounterNr
        ));
        return !$type || $type->casetype_id == self::HOUSE_CASE;
    }

}
