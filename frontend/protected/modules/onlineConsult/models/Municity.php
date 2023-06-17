<?php

/**
 * This is the model class for table "seg_municity".
 *
 * The followings are the available columns in table 'seg_municity':
 * @property string $mun_nr
 * @property string $mun_name
 * @property string $zipcode
 * @property string $prov_nr
 * @property string $code
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $ordering
 *
 * The followings are the available model relations:
 * @property Barangays[] $barangays
 * @property InsuranceMemberInfo[] $insuranceMemberInfos
 * @property Provinces $provNr
 */
class Municity extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_municity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('mun_nr, mun_name, zipcode, prov_nr, modify_id, create_id', 'required'),
            array('ordering', 'numerical', 'integerOnly'=>true),
            array('mun_nr, prov_nr', 'length', 'max'=>11),
            array('mun_name', 'length', 'max'=>80),
            array('zipcode', 'length', 'max'=>6),
            array('code', 'length', 'max'=>50),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('modify_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('mun_nr, mun_name, zipcode, prov_nr, code, modify_id, modify_time, create_id, create_time, ordering', 'safe', 'on'=>'search'),
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
            'barangays' => array(self::HAS_MANY, 'Barangays', 'mun_nr'),
            'insuranceMemberInfos' => array(self::HAS_MANY, 'InsuranceMemberInfo', 'mun_nr'),
            'provNr' => array(self::BELONGS_TO, 'Provinces', 'prov_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'mun_nr' => 'Mun Nr',
            'mun_name' => 'Mun Name',
            'zipcode' => 'Zipcode',
            'prov_nr' => 'Prov Nr',
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

        $criteria->compare('mun_nr',$this->mun_nr,true);
        $criteria->compare('mun_name',$this->mun_name,true);
        $criteria->compare('zipcode',$this->zipcode,true);
        $criteria->compare('prov_nr',$this->prov_nr,true);
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
     * @return Municity the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

      public function getAllMunicity(){
        $criteria=new CDbCriteria;
        $criteria->order = "mun_name ASC";

        return $this->findAll($criteria);
    }
} 