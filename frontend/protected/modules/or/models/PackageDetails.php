<?php

/**
 * This is the model class for table "seg_package_details".
 *
 * The followings are the available columns in table 'seg_package_details':
 * @property integer $item_id
 * @property integer $package_id
 * @property string $item_code
 * @property string $item_name
 * @property string $item_purpose
 * @property double $quantity
 * @property string $price_cash
 * @property string $price_charge
 * @property string $remarks
 * @property string $area
 * @property string $item_type
 * @property string $unit
 *
 * The followings are the available model relations:
 * @property Packages $package
 */
class PackageDetails extends CActiveRecord
{
	public $price;
	public $is_cash;

	public $packageName;



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_package_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('package_id', 'required'),
			array('package_id', 'numerical', 'integerOnly'=>true),
			array('quantity', 'numerical'),
			array('item_code, unit', 'length', 'max'=>30),
			array('item_name', 'length', 'max'=>50),
			array('item_purpose', 'length', 'max'=>4),
			array('price_cash, price_charge, area, item_type', 'length', 'max'=>10),
			array('remarks', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('item_id, package_id, item_code, item_name, item_purpose, quantity, price_cash, price_charge, remarks, area, item_type, unit', 'safe', 'on'=>'search'),
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
			'package' => array(self::BELONGS_TO, 'Packages', 'package_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'item_id' => 'Item',
			'package_id' => 'Package',
			'item_code' => 'Item Code',
			'item_name' => 'Item Name',
			'item_purpose' => 'Item Purpose',
			'quantity' => 'Quantity',
			'price_cash' => 'Price Cash',
			'price_charge' => 'Price Charge',
			'remarks' => 'Remarks',
			'area' => 'Area',
			'item_type' => 'Item Type',
			'unit' => 'Unit',
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

		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('package_id',$this->package_id);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('item_name',$this->item_name,true);
		$criteria->compare('item_purpose',$this->item_purpose,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('price_cash',$this->price_cash,true);
		$criteria->compare('price_charge',$this->price_charge,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('item_type',$this->item_type,true);
		$criteria->compare('unit',$this->unit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PackageDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getPackageDetailsByName($query = array()) {
		$criteria = new CDbCriteria();

		$criteria->join = 'INNER JOIN seg_packages pack ON pack.package_id = t.package_id
		                    LEFT JOIN care_pharma_products_main cp ON t.item_code = cp.bestellnum';

		if ($query['is_cash']) {

			$criteria->select = 't.*,t.price_cash as price, pack.package_name as packageName';
		} else {
			$criteria->select = 't.*,t.price_charge as price, pack.package_name as packageName';
		}

        $criteria->addCondition('t.package_id = :id');
        $criteria->order = 'cp.prod_class = "M" DESC';

        $criteria->params = array('id' => $query['search']);

        $data = $this->findAll($criteria);

 		return $data;
	}

	public function getPrice(){
		if($this->is_cash)
			return $this->price_cash;
		else
			return $this->price_charge;
	}

    public function DoFreRoute($bestellnum, $encounter)
    {
        $sql = "SELECT 
                  spi.`dosage`,
                  spi.`frequency`,
                  spi.`route`
                FROM
                  seg_pharma_orders spo 
                  LEFT JOIN seg_pharma_order_items spoi 
                    ON spo.`refno` = spoi.`refno` 
                  LEFT JOIN seg_pharma_items_cf4 spi 
                    ON spoi.`refno` = spi.`refno` 
                    AND spoi.`bestellnum` = spi.`bestellnum` 
                WHERE spo.`encounter_nr` = '" . $encounter . "'
                  AND spoi.`bestellnum` = '" . $bestellnum . "'
                  AND spo.is_deleted = 0
                  AND spoi.is_deleted = 0
                  AND spoi.returns = 0
                  ORDER BY spi.`create_dt` DESC
                  LIMIT 1";

        $command = Yii::app()->db->createCommand($sql)->queryAll();

        return $command;
    }

    public function ProdClass($bestellnum)
    {
        $sql = "SELECT 
                  cp.`prod_class`
                FROM
                  care_pharma_products_main cp
                WHERE cp.`bestellnum` = '".$bestellnum."'";

        $command = Yii::app()->db->createCommand($sql)->queryScalar();

        return $command;
    }

    public function DefaultDosagePHIC($bestellnum)
    {
        $sql = "SELECT 
                  spms.`strength_disc`
                FROM
                  care_pharma_products_main cp
                  LEFT JOIN seg_phil_medicine spm
                    ON cp.drug_code = spm.drug_code
                  LEFT JOIN seg_phil_medicine_strength spms
                    ON spm.strength_code = spms.strength_code
                WHERE cp.`bestellnum` = '".$bestellnum."'";

        $command = Yii::app()->db->createCommand($sql)->queryScalar();

        return $command;
    }
}
