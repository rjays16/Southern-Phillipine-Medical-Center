<?php

/**
 * This is the model class for table "seg_provinces".
 *
 * The followings are the available columns in table 'seg_provinces':
 * @property string $prov_nr
 * @property string $prov_name
 * @property string $region_nr
 * @property string $code
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $ordering
 *
 * The followings are the available model relations:
 * @property Municity[] $municities
 * @property Regions $regionNr
 */
class Provinces extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_provinces';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('prov_nr, prov_name, region_nr, modify_id, create_id', 'required'),
            array('ordering', 'numerical', 'integerOnly'=>true),
            array('prov_nr, region_nr', 'length', 'max'=>11),
            array('prov_name', 'length', 'max'=>80),
            array('code', 'length', 'max'=>50),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('modify_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('prov_nr, prov_name, region_nr, code, modify_id, modify_time, create_id, create_time, ordering', 'safe', 'on'=>'search'),
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
            'municities' => array(self::HAS_MANY, 'Municity', 'prov_nr'),
            'regionNr' => array(self::BELONGS_TO, 'Regions', 'region_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'prov_nr' => 'Prov Nr',
            'prov_name' => 'Prov Name',
            'region_nr' => 'Region Nr',
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

        $criteria->compare('prov_nr',$this->prov_nr,true);
        $criteria->compare('prov_name',$this->prov_name,true);
        $criteria->compare('region_nr',$this->region_nr,true);
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
     * @return Provinces the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getAllProvinces(){
        $criteria=new CDbCriteria;
        $criteria->order = "prov_name ASC";

        return $this->findAll($criteria);
    }
} 