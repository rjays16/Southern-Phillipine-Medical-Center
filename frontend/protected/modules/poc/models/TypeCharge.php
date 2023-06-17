<?php
namespace SegHis\modules\poc\models;

/**
 * This is the model class for table "seg_type_charge".
 *
 * The followings are the available columns in table 'seg_type_charge':
 * @property string $id
 * @property string $charge_name
 * @property string $description
 * @property integer $ordering
 * @property integer $is_excludedfrombilling
 *
 * The followings are the available model relations:
 * @property PocOrder[] $pocOrders
 */
class TypeCharge extends \CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_type_charge';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('ordering, is_excludedfrombilling', 'numerical', 'integerOnly'=>true),
			array('id', 'length', 'max'=>10),
			array('charge_name', 'length', 'max'=>25),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, charge_name, description, ordering, is_excludedfrombilling', 'safe', 'on'=>'search'),
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
			'pocOrders' => array(self::HAS_MANY, 'PocOrder', 'settlement_type'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'charge_name' => 'Charge Name',
			'description' => 'Description',
			'ordering' => 'Ordering',
			'is_excludedfrombilling' => 'Is Excludedfrombilling',
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

		$criteria=new \CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('charge_name',$this->charge_name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('ordering',$this->ordering);
		$criteria->compare('is_excludedfrombilling',$this->is_excludedfrombilling);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TypeCharge the static model class
	 */
	public static function model($className=__CLASS__)
	{
            return parent::model($className);
	}
        
        /***
         * 
         */
        public static function getTypeCharges() 
        {
            $criteria = new \CDbCriteria();                                                           
//            $criteria->condition = "is_excludedfrombilling = 0";
//            $criteria->condition = "id NOT IN ('paid','phs','charity','cmap','lingap','dost')";
            $criteria->condition = "id IN ('phic')";
//            $criteria->order = "charge_name";
            $criteria->order = "ordering";
            
            $result = self::model()->findAll($criteria);
            return $result;
        }
}
