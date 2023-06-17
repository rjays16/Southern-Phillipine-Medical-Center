<?php


/**
 * This is the model class for table "care_config_global".
 *
 * The followings are the available columns in table 'care_config_global':
 * @property string $type
 * @property string $value
 * @property string $notes
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class CareConfigGlobal extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_config_global';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('history, modify_time', 'required'),
            array('type', 'length', 'max'=>60),
            array('value, notes', 'length', 'max'=>255),
            array('status', 'length', 'max'=>25),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('type, value, notes, status, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
            'type' => 'Type',
            'value' => 'Value',
            'notes' => 'Notes',
            'status' => 'Status',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
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

        $criteria->compare('type',$this->type,true);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('notes',$this->notes,true);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CareConfigGlobal the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

} 