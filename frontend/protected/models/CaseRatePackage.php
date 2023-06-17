<?php
namespace SegHis\models;
/**
 * This is the model class for table "seg_case_rate_packages".
 *
 * The followings are the available columns in table 'seg_case_rate_packages':
 * @property integer $package_id
 * @property string $code
 * @property string $description
 * @property string $group
 * @property double $package
 * @property double $hf
 * @property double $pf
 * @property double $shf
 * @property double $spf
 * @property string $case_type
 * @property integer $special_case
 * @property integer $for_infirmaries
 * @property integer $for_laterality
 * @property integer $is_allowed_second
 * @property integer $is_delivery
 * @property integer $is_prenatal
 * @property string $removed_from_phic
 * @property string $date_from
 * @property string $date_to
 * @property string $create_time
 * @property string $create_id
 * @property string $modify_time
 * @property string $modify_id
 * @property string $history
 */
class CaseRatePackage extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_case_rate_packages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, description, package, hf, pf, shf, spf, case_type', 'required'),
			array('special_case, for_infirmaries, for_laterality, is_allowed_second, is_delivery, is_prenatal', 'numerical', 'integerOnly'=>true),
			array('package, hf, pf, shf, spf', 'numerical'),
			array('code', 'length', 'max'=>15),
			array('case_type', 'length', 'max'=>1),
			array('create_id, modify_id', 'length', 'max'=>20),
			array('group, removed_from_phic, date_from, date_to, create_time, modify_time, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('package_id, code, description, group, package, hf, pf, shf, spf, case_type, special_case, for_infirmaries, for_laterality, is_allowed_second, is_delivery, is_prenatal, removed_from_phic, date_from, date_to, create_time, create_id, modify_time, modify_id, history', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'package_id' => 'Package',
			'code' => 'Code',
			'description' => 'Description',
			'group' => 'Group',
			'package' => 'Package',
			'hf' => 'Hf',
			'pf' => 'Pf',
			'shf' => 'Shf',
			'spf' => 'Spf',
			'case_type' => 'Case Type',
			'special_case' => 'Special Case',
			'for_infirmaries' => 'For Infirmaries',
			'for_laterality' => 'For Laterality',
			'is_allowed_second' => 'Is Allowed Second',
			'is_delivery' => 'Is Delivery',
			'is_prenatal' => 'Is Prenatal',
			'removed_from_phic' => 'Removed From Phic',
			'date_from' => 'Date From',
			'date_to' => 'Date To',
			'create_time' => 'Create Time',
			'create_id' => 'Create',
			'modify_time' => 'Modify Time',
			'modify_id' => 'Modify',
			'history' => 'History',
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

		$criteria->compare('package_id',$this->package_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('group',$this->group,true);
		$criteria->compare('package',$this->package);
		$criteria->compare('hf',$this->hf);
		$criteria->compare('pf',$this->pf);
		$criteria->compare('shf',$this->shf);
		$criteria->compare('spf',$this->spf);
		$criteria->compare('case_type',$this->case_type,true);
		$criteria->compare('special_case',$this->special_case);
		$criteria->compare('for_infirmaries',$this->for_infirmaries);
		$criteria->compare('for_laterality',$this->for_laterality);
		$criteria->compare('is_allowed_second',$this->is_allowed_second);
		$criteria->compare('is_delivery',$this->is_delivery);
		$criteria->compare('is_prenatal',$this->is_prenatal);
		$criteria->compare('removed_from_phic',$this->removed_from_phic,true);
		$criteria->compare('date_from',$this->date_from,true);
		$criteria->compare('date_to',$this->date_to,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('history',$this->history,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CaseRatePackage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
