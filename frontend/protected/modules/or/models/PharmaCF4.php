<?php

/**
 * This is the model class for table "seg_pharma_items_cf4".
 *
 * The followings are the available columns in table 'seg_pharma_items_cf4':
 * @property integer $id
 * @property string $refno
 * @property string $bestellnum
 * @property string $dosage
 * @property string $route
 * @property string $frequency
 * @property string $history
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 */
class PharmaCF4 extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_pharma_items_cf4';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bestellnum', 'required'),
            array('refno', 'length', 'max'=>10),
            array('bestellnum', 'length', 'max'=>25),
            array('frequency', 'length', 'max'=>50),
            array('create_id, modify_id', 'length', 'max'=>255),
            array('dosage, route, history, create_dt, modify_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, refno, bestellnum, dosage, route, frequency, history, create_id, create_dt, modify_id, modify_dt', 'safe', 'on'=>'search'),
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
            'id'         => 'ID',
            'refno'      => 'Refno',
            'bestellnum' => 'Bestellnum',
            'dosage'     => 'Dosage',
            'route'      => 'Route',
            'frequency'  => 'Frequency',
            'history'    => 'History',
            'create_id'  => 'Create',
            'create_dt'  => 'Create Dt',
            'modify_id'  => 'Modify',
            'modify_dt'  => 'Modify Dt',
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
        $criteria->compare('refno',$this->refno,true);
        $criteria->compare('bestellnum',$this->bestellnum,true);
        $criteria->compare('dosage',$this->dosage,true);
        $criteria->compare('route',$this->route,true);
        $criteria->compare('frequency',$this->frequency,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PharmaCF4 the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}