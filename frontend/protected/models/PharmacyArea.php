<?php

/**
 * This is the model class for table "seg_pharma_areas".
 *
 * The followings are the available columns in table 'seg_pharma_areas':
 * @property string $area_code
 * @property string $area_name
 * @property integer $allow_socialized
 * @property integer $lockflag
 *
 * The followings are the available model relations:
 * @property PharmaOrders[] $pharmaOrders
 * @property PharmaWardStocks[] $pharmaWardStocks
 */
class PharmacyArea extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_pharma_areas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('area_code, area_name', 'required'),
			array('allow_socialized, lockflag, exclude_if_phic', 'numerical', 'integerOnly'=>true),
			array('area_code', 'length', 'max'=>10),
			array('area_name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('area_code, area_name, allow_socialized, lockflag, exclude_if_phic', 'safe', 'on'=>'search'),
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
			'pharmaOrders' => array(self::HAS_MANY, 'PharmaOrders', 'pharma_area'),
			'pharmaWardStocks' => array(self::HAS_MANY, 'PharmaWardStocks', 'pharma_area'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'area_code' => 'Area Code',
			'area_name' => 'Area Name',
			'allow_socialized' => 'Allow Socialized',
			'lockflag' => 'Lockflag',
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

		$criteria->compare('area_code',$this->area_code,true);
		$criteria->compare('area_name',$this->area_name,true);
		$criteria->compare('allow_socialized',$this->allow_socialized);
		$criteria->compare('lockflag',$this->lockflag);
		$criteria->compare('exclude_if_phic',$this->exclude_if_phic);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PharmacyAreas the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getAllPharmaAreaWithInventory(){
		$criteria = new CDbCriteria;
        $criteria->condition = "inv_api_key IS NOT NULL OR inv_api_key <> ''";

        return $this->findAll($criteria);
	}

	public function getAPIKeyByAreaCode($area_code){
		$model = $this->findByAttributes(array("area_code" =>$area_code));

		if($model)
			return $model->inv_api_key;

		return "";
	}
}
