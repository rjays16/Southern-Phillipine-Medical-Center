<?php

/**
 * This is the model class for table "seg_pdpu_progress_n_audit_trail".
 *
 * The followings are the available columns in table 'seg_pdpu_progress_n_audit_trail':
 * @property integer $id
 * @property integer $notes_id
 * @property string $date_changed
 * @property string $action_type
 * @property string $login
 */
class PdpuProgressNAuditTrail extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_pdpu_progress_n_audit_trail';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('notes_id, date_changed', 'required'),
            array('notes_id', 'numerical', 'integerOnly'=>true),
            array('action_type', 'length', 'max'=>50),
            array('login', 'length', 'max'=>25),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, notes_id, date_changed, action_type, login', 'safe', 'on'=>'search'),
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
            'notes_id' => 'Notes',
            'date_changed' => 'Date Changed',
            'action_type' => 'Action Type',
            'login' => 'Login',
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
        $criteria->compare('notes_id',$this->notes_id);
        $criteria->compare('date_changed',$this->date_changed,true);
        $criteria->compare('action_type',$this->action_type,true);
        $criteria->compare('login',$this->login,true);
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PdpuProgressNAuditTrail the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 