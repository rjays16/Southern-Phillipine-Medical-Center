<?php
namespace SegHis\modules\poc\models;

/**
 * This is the model class for table "seg_poc_order_detail".
 *
 * The followings are the available columns in table 'seg_poc_order_detail':
 * @property string $id
 * @property string $refno
 * @property string $service_code
 * @property string $unit_price
 * @property string $quantity
 *
 * The followings are the available model relations:
 * @property LabServices $serviceCode
 * @property PocOrder $refno0
 */
class PocOrderDetail extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_poc_order_detail';
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
			array('id', 'length', 'max'=>36),
			array('refno', 'length', 'max'=>12),
			array('service_code, unit_price, quantity', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, refno, service_code, unit_price, quantity', 'safe', 'on'=>'search'),
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
			'serviceCode' => array(self::BELONGS_TO, 'LabServices', 'service_code'),
			'refno0' => array(self::BELONGS_TO, 'PocOrder', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'refno' => 'Refno',
			'service_code' => 'Service Code',
			'unit_price' => 'Unit Price',
			'quantity' => 'Quantity',
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
		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('unit_price',$this->unit_price,true);
		$criteria->compare('quantity',$this->quantity,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PocOrderDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        /***
         * 
         * 
         */
        public function getPocOrderDetail($refno) {
            $pocDetail = self::model()->findAllByAttributes(array('refno'=>$refno));
            return $pocDetail;
        }
}
