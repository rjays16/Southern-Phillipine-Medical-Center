<?php

/**
 * This is the model class for table "seg_encounter_vital_sign_bmi".
 *
 * The followings are the available columns in table 'seg_encounter_vital_sign_bmi':
 * @property string $id
 * @property string $pid
 * @property string $encounter_nr
 * @property string $bmi_date
 * @property integer $weight
 * @property integer $height
 * @property integer $hip_line
 * @property integer $waist_line
 * @property integer $abdominal_girth
 * @property string $history
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property integer $is_deleted
 */
class EncounterVitalSignBmi extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_vital_sign_bmi';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, pid, encounter_nr, bmi_date, create_dt, modify_dt', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('id', 'length', 'max'=>36),
            array('pid, encounter_nr', 'length', 'max'=>12),
            array('history, create_id, modify_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, pid, encounter_nr, bmi_date, weight, height, hip_line, waist_line, abdominal_girth, history, create_id, create_dt, modify_id, modify_dt, is_deleted', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pid' => 'Pid',
            'encounter_nr' => 'Encounter Nr',
            'bmi_date' => 'Bmi Date',
            'weight' => 'Weight',
            'height' => 'Height',
            'hip_line' => 'Hip Line',
            'waist_line' => 'Waist Line',
            'abdominal_girth' => 'Abdominal Girth',
            'history' => 'History',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('pid',$this->pid,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('bmi_date',$this->bmi_date,true);
        $criteria->compare('weight',$this->weight);
        $criteria->compare('height',$this->height);
        $criteria->compare('hip_line',$this->hip_line);
        $criteria->compare('waist_line',$this->waist_line);
        $criteria->compare('abdominal_girth',$this->abdominal_girth);
        $criteria->compare('history',$this->history,true);
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
     * @return EncounterVitalSignBmi the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getVitalSignsBMI($data)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.*,fn_get_personell_lastname_first_by_loginid(t.create_id) AS create_id ";
        $criteria->addCondition('is_deleted=0');

        $criteria->addColumnCondition(array(
            'encounter_nr' => $data['encounter_nr']
        ));

        $criteria->order = 'create_dt DESC';

        return $this->findAll($criteria);
    }

    public function getCategory($data)
    {
        $sql = "SELECT bmi.`bmi_category` AS cat FROM seg_bmi_category bmi 
                WHERE bmi.`bmi` >= ".($data)." LIMIT  1 ";

        $command = \Yii::app()->db->createCommand($sql);
        $result = $command->queryScalar();

        return $result;
    }
}