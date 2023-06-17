<?php

/**
 * This is the model class for table "care_pharma_outside_order".
 *
 * The followings are the available columns in table 'care_pharma_outside_order':
 * @property integer $id
 * @property string $order_dt
 * @property string $encounter_nr
 * @property string $gen_code
 * @property string $drug_code
 * @property string $brand_name
 * @property string $route
 * @property string $frequency
 * @property string $price
 * @property integer $quantity
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property integer $is_deleted
 */
class CarePharmaOutsideOrder extends CareActiveRecord
{
    public $description;
    public $drug_code;
    public $generic;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_pharma_outside_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr', 'required'),
            array('price', 'required'),
            array('frequency', 'required'),
            array('dosage', 'required'),
            array('price', 'required'),
            array('route', 'required'),
            array('quantity, is_deleted', 'numerical', 'integerOnly'=>true),
            array('order_dt, create_id, modify_id', 'length', 'max'=>35),
            array('encounter_nr', 'length', 'max'=>12),
            array('gen_code, price', 'length', 'max'=>10),
            array('drug_code, brand_name,create_dt, modify_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_dt, encounter_nr, gen_code, drug_code, brand_name, route, frequency, price, quantity, create_id, create_dt, modify_id, modify_dt, is_deleted', 'safe', 'on'=>'search'),
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
            'genmedicine' => array(self::BELONGS_TO, 'CarePharmaProductsMain', 'gen_code'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'order_dt' => 'Order Dt',
            'encounter_nr' => 'Encounter Nr',
            'gen_code' => 'Gen Code',
            'drug_code' => 'Drug Code',
            'brand_name' => 'Brand Name',
            'dosage' => 'Dosage',
            'route' => 'Route',
            'frequency' => 'Frequency',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'is_deleted' => 'Is Deleted',
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
        $criteria->compare('order_dt',$this->order_dt,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('gen_code',$this->gen_code,true);
        $criteria->compare('drug_code',$this->drug_code,true);
        $criteria->compare('brand_name',$this->brand_name,true);
        $criteria->compare('dosage',$this->dosage,true);
        $criteria->compare('route',$this->route,true);
        $criteria->compare('frequency',$this->frequency,true);
        $criteria->compare('price',$this->price,true);
        $criteria->compare('quantity',$this->quantity);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CarePharmaOutsideOrder the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function fetchOutsideOrder($data)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "t.`id` ,t.`gen_code`,t.`drug_code`,t.`price`,t.`quantity`,t.`create_dt`,t.order_dt ,IFNULL(spm.description,
    (SELECT s.`description` FROM seg_phil_medicine  AS s WHERE s.`drug_code` = t.`drug_code`)) AS description,brand_name, fn_get_personell_lastname_first_by_loginid(t.create_id) AS create_id,t.`dosage`,t.`frequency`,t.`route`,cppm.generic";
        $criteria->join = 'LEFT JOIN `care_pharma_products_main` AS cppm ON t.`gen_code` = cppm.`bestellnum`
         LEFT JOIN `seg_phil_medicine` AS spm ON spm.`drug_code` = cppm.`drug_code`';

        $criteria->addCondition('t.is_deleted=0');
        $criteria->addColumnCondition(array(
            'encounter_nr' => $data['encounter_nr']
        )); 

        $criteria->order = 'order_dt DESC';
//        CVarDumper::dump($this->findAll($criteria),10,true);die;
        return $this->findAll($criteria);

    }

}