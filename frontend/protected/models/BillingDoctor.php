<?php
namespace SegHis\models;
/**
 * This is the model class for table "seg_encounter_privy_dr".
 *
 * The followings are the available columns in table 'seg_encounter_privy_dr':
 * @property string $encounter_nr
 * @property integer $dr_nr
 * @property integer $dr_role_type_nr
 * @property integer $entry_no
 * @property integer $dr_level
 * @property string $days_attended
 * @property string $dr_charge
 * @property integer $is_excluded
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $from_date
 * @property string $to_date
 * @property integer $is_served
 * @property string $service_code
 */
class BillingDoctor extends \CareActiveRecord
{

	const DOCTOR_ROLE_SURGEON = 7;
	const DOCTOR_LEVEL_1 = 1;


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_encounter_privy_dr';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, dr_nr, dr_role_type_nr, entry_no, days_attended, modify_id, create_id', 'required'),
			array('dr_nr, dr_role_type_nr, entry_no, dr_level, is_excluded, is_served', 'numerical', 'integerOnly'=>true),
			array('encounter_nr', 'length', 'max'=>12),
			array('days_attended, service_code', 'length', 'max'=>10),
			array('dr_charge', 'length', 'max'=>20),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_dt, create_dt, from_date, to_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('encounter_nr, dr_nr, dr_role_type_nr, entry_no, dr_level, days_attended, dr_charge, is_excluded, modify_id, modify_dt, create_id, create_dt, from_date, to_date, is_served, service_code', 'safe', 'on'=>'search'),
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
			'encounter_nr' => 'Encounter Nr',
			'dr_nr' => 'Dr Nr',
			'dr_role_type_nr' => 'Dr Role Type Nr',
			'entry_no' => 'Entry No',
			'dr_level' => 'Dr Level',
			'days_attended' => 'Days Attended',
			'dr_charge' => 'Dr Charge',
			'is_excluded' => 'Is Excluded',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'is_served' => 'Is Served',
			'service_code' => 'Service Code',
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
	 * @return \CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new \CDbCriteria;

		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('dr_nr',$this->dr_nr);
		$criteria->compare('dr_role_type_nr',$this->dr_role_type_nr);
		$criteria->compare('entry_no',$this->entry_no);
		$criteria->compare('dr_level',$this->dr_level);
		$criteria->compare('days_attended',$this->days_attended,true);
		$criteria->compare('dr_charge',$this->dr_charge,true);
		$criteria->compare('is_excluded',$this->is_excluded);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('is_served',$this->is_served);
		$criteria->compare('service_code',$this->service_code,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BillingDoctor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
