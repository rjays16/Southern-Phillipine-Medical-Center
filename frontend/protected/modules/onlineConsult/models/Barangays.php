<?php

/**
 * This is the model class for table "seg_barangays".
 *
 * The followings are the available columns in table 'seg_barangays':
 * @property string $brgy_nr
 * @property string $brgy_name
 * @property string $mun_nr
 * @property string $code
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $ordering
 *
 * The followings are the available model relations:
 * @property Municity $munNr
 * @property InsuranceMemberInfo[] $insuranceMemberInfos
 */
class Barangays extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_barangays';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('brgy_nr, brgy_name, mun_nr, modify_id, create_id', 'required'),
            array('ordering', 'numerical', 'integerOnly'=>true),
            array('brgy_nr, mun_nr', 'length', 'max'=>11),
            array('brgy_name', 'length', 'max'=>80),
            array('code', 'length', 'max'=>50),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('modify_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('brgy_nr, brgy_name, mun_nr, code, modify_id, modify_time, create_id, create_time, ordering', 'safe', 'on'=>'search'),
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
            'munNr' => array(self::BELONGS_TO, 'Municity', 'mun_nr'),
            'insuranceMemberInfos' => array(self::HAS_MANY, 'InsuranceMemberInfo', 'brgy_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'brgy_nr' => 'Brgy Nr',
            'brgy_name' => 'Brgy Name',
            'mun_nr' => 'Mun Nr',
            'code' => 'Code',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'ordering' => 'Ordering',
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

        $criteria->compare('brgy_nr',$this->brgy_nr,true);
        $criteria->compare('brgy_name',$this->brgy_name,true);
        $criteria->compare('mun_nr',$this->mun_nr,true);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('ordering',$this->ordering);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Barangays the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

      public function getAllBarangay(){
        $criteria=new CDbCriteria;
        $criteria->order = "brgy_name ASC";
 
        return $this->findAll($criteria);
    }
} 