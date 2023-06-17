<?php

/**
 * This is the model class for table "seg_soa_diagnosis".
 *
 * The followings are the available columns in table 'seg_soa_diagnosis':
 * @property integer $diag_id
 * @property string $encounter_nr
 * @property string $final_diagnosis
 * @property string $other_diagnosis
 * @property string $create_date
 * @property string $create_id
 * @property string $modify_date
 * @property string $modify_id
 * @property string $history
 */
class SoaDiagnosis extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_soa_diagnosis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr', 'required'),
            array('encounter_nr', 'length', 'max'=>45),
            array('create_id, modify_id', 'length', 'max'=>90),
            array('final_diagnosis, other_diagnosis, create_date, modify_date, history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('diag_id, encounter_nr, final_diagnosis, other_diagnosis, create_date, create_id, modify_date, modify_id, history', 'safe', 'on'=>'search'),
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
            'diag_id' => 'Diag',
            'encounter_nr' => 'Encounter Nr',
            'final_diagnosis' => 'Final Diagnosis',
            'other_diagnosis' => 'Other Diagnosis',
            'create_date' => 'Create Date',
            'create_id' => 'Create',
            'modify_date' => 'Modify Date',
            'modify_id' => 'Modify',
            'history' => 'History',
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

        $criteria->compare('diag_id',$this->diag_id);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('final_diagnosis',$this->final_diagnosis,true);
        $criteria->compare('other_diagnosis',$this->other_diagnosis,true);
        $criteria->compare('create_date',$this->create_date,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('modify_date',$this->modify_date,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('history',$this->history,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SoaDiagnosis the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 