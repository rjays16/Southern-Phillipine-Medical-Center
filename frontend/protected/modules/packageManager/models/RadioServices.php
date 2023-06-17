<?php

/**
 * This is the model class for table "seg_radio_services".
 *
 * The followings are the available columns in table 'seg_radio_services':
 * @property string $service_code
 * @property string $group_code
 * @property string $name
 * @property string $price_cash
 * @property string $price_charge
 * @property integer $is_socialized
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $is_ER
 * @property integer $only_in_clinic
 * @property string $remarks
 * @property integer $has_group_stat
 * @property integer $in_phs
 * @property integer $in_pacs
 * @property string $pacs_code
 * @property integer $for_reading
 * @property string $modality
 * @property integer $no_days_expiry
 *
 * The followings are the available model relations:
 * @property ItemExpiration[] $itemExpirations
 * @property Hl7RadioModality $modality0
 * @property RadioServiceGroups $groupCode
 * @property RadioServicesExcluded[] $radioServicesExcludeds
 */
class RadioServices extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_radio_services';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_code, group_code, name, modify_id, create_id', 'required'),
			array('is_socialized, is_ER, only_in_clinic, has_group_stat, in_phs, in_pacs, for_reading, no_days_expiry', 'numerical', 'integerOnly'=>true),
			array('service_code, group_code, price_cash, price_charge, pacs_code', 'length', 'max'=>10),
			array('name', 'length', 'max'=>150),
			array('status, modify_id, create_id', 'length', 'max'=>35),
			array('modality', 'length', 'max'=>5),
			array('history, modify_dt, create_dt, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('service_code, group_code, name, price_cash, price_charge, is_socialized, status, history, modify_id, modify_dt, create_id, create_dt, is_ER, only_in_clinic, remarks, has_group_stat, in_phs, in_pacs, pacs_code, for_reading, modality, no_days_expiry', 'safe', 'on'=>'search'),
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
			'itemExpirations' => array(self::HAS_MANY, 'ItemExpiration', 'service_code'),
			'modality0' => array(self::BELONGS_TO, 'Hl7RadioModality', 'modality'),
			'groupCode' => array(self::BELONGS_TO, 'RadioServiceGroups', 'group_code'),
			'radioServicesExcludeds' => array(self::HAS_MANY, 'RadioServicesExcluded', 'service_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'service_code' => 'Service Code',
			'group_code' => 'Group Code',
			'name' => 'Name',
			'price_cash' => 'Price Cash',
			'price_charge' => 'Price Charge',
			'is_socialized' => 'Is Socialized',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'is_ER' => 'Is Er',
			'only_in_clinic' => 'Only In Clinic',
			'remarks' => 'Remarks',
			'has_group_stat' => 'Has Group Stat',
			'in_phs' => 'In Phs',
			'in_pacs' => 'In Pacs',
			'pacs_code' => 'Pacs Code',
			'for_reading' => 'For Reading',
			'modality' => 'Modality',
			'no_days_expiry' => 'No Days Expiry',
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

		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('group_code',$this->group_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price_cash',$this->price_cash,true);
		$criteria->compare('price_charge',$this->price_charge,true);
		$criteria->compare('is_socialized',$this->is_socialized);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('is_ER',$this->is_ER);
		$criteria->compare('only_in_clinic',$this->only_in_clinic);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('has_group_stat',$this->has_group_stat);
		$criteria->compare('in_phs',$this->in_phs);
		$criteria->compare('in_pacs',$this->in_pacs);
		$criteria->compare('pacs_code',$this->pacs_code,true);
		$criteria->compare('for_reading',$this->for_reading);
		$criteria->compare('modality',$this->modality,true);
		$criteria->compare('no_days_expiry',$this->no_days_expiry);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RadioServices the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
