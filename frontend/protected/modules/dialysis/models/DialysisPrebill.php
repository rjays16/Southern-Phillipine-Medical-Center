<?php
namespace SegHis\modules\dialysis\models;
/**
 * This is the model class for table "seg_dialysis_prebill".
 *
 * The followings are the available columns in table 'seg_dialysis_prebill':
 * @property string $bill_nr
 * @property string $encounter_nr
 * @property string $bill_type
 * @property double $amount
 * @property string $request_flag
 * @property string $discountid
 *
 * @property DialysisRequest $request
 */
class DialysisPrebill extends \CareActiveRecord
{

	const BILL_TYPE_PHILHEALTH = 'PH';
	const BILL_TYPE_NONE_PHILHEALTH = 'NPH';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_dialysis_prebill';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bill_nr', 'required'),
			array('amount', 'numerical'),
			array('bill_nr', 'length', 'max'=>14),
			array('encounter_nr', 'length', 'max'=>12),
			array('bill_type', 'length', 'max'=>4),
			array('request_flag', 'length', 'max'=>10),
			array('discountid', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bill_nr, encounter_nr, bill_type, amount, request_flag, discountid', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'request' => array(self::BELONGS_TO,'SegHis\modules\dialysis\models\DialysisRequest', array('encounter_nr' => 'encounter_nr'))
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'bill_nr' => 'Bill Nr',
			'encounter_nr' => 'Encounter Nr',
			'bill_type' => 'Bill Type',
			'amount' => 'Amount',
			'request_flag' => 'Request Flag',
			'discountid' => 'Discountid',
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

		$criteria->compare('bill_nr',$this->bill_nr,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('bill_type',$this->bill_type,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('request_flag',$this->request_flag,true);
		$criteria->compare('discountid',$this->discountid,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DialysisPrebill the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
