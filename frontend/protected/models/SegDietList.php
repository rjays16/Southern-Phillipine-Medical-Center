<?php

/**
 * This is the model class for table "seg_diet_list".
 *
 * The followings are the available columns in table 'seg_diet_list':
 * @property integer $nr
 * @property string $encounter_nr
 * @property string $b
 * @property string $l
 * @property string $d
 * @property integer $is_deleted
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $history
 */
class SegDietList extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_diet_list';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('create_id, modify_id, modify_dt', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('encounter_nr', 'length', 'max'=>12),
            array('b, l, d', 'length', 'max'=>25),
            array('create_id, modify_id', 'length', 'max'=>35),
            array('create_dt, history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('nr, encounter_nr, b, l, d, is_deleted, create_id, create_dt, modify_id, modify_dt, history', 'safe', 'on'=>'search'),
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
            'nr' => 'Nr',
            'encounter_nr' => 'Encounter Nr',
            'b' => 'B',
            'l' => 'L',
            'd' => 'D',
            'is_deleted' => 'Is Deleted',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
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

        $criteria->compare('nr',$this->nr);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('b',$this->b,true);
        $criteria->compare('l',$this->l,true);
        $criteria->compare('d',$this->d,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('history',$this->history,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SegDietList the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 