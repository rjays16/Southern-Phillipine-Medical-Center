<?php

/**
 * This is the model class for table "care_effective".
 *
 * The followings are the available columns in table 'care_effective':
 * @property integer $id
 * @property string $value
 * @property string $start_date
 * @property string $end_date
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property integer $is_deleted
 */

class CareEffective extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_effective';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('modify_dt', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('create_id, modify_id', 'length', 'max'=>50),
            array('value, start_date, end_date, create_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, value, start_date, end_date, create_id, create_dt, modify_id, modify_dt, is_deleted', 'safe', 'on'=>'search'),
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
            'value' => 'Value',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('start_date',$this->start_date,true);
        $criteria->compare('end_date',$this->end_date,true);
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
     * @return CareEffective the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getDetails($data)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.value = '{$data}'");

        $model = self::model()->findAll($criteria);

        return $model;
    }
}