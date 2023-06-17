<?php
namespace SegHis\modules\socialService\models;
/**
 * This is the model class for table "seg_charity_grants_pid".
 *
 * The followings are the available columns in table 'seg_charity_grants_pid':
 * @property string $pid
 * @property string $grant_dte
 * @property integer $sw_nr
 * @property string $discountid
 * @property string $discount
 * @property string $discount_amnt
 * @property string $notes
 * @property string $personal_circumstance
 * @property string $community_situation
 * @property string $nature_of_disease
 * @property string $reason
 * @property string $other_name
 * @property string $id_number
 * @property string $status
 */
class PersonCharityGrant extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_charity_grants_pid';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, grant_dte, sw_nr, discountid, discount', 'required'),
			array('sw_nr', 'numerical', 'integerOnly'=>true),
			array('pid', 'length', 'max'=>12),
			array('discountid, discount, discount_amnt, reason', 'length', 'max'=>10),
			array('other_name', 'length', 'max'=>50),
			array('id_number', 'length', 'max'=>20),
			array('status', 'length', 'max'=>9),
			array('notes, personal_circumstance, community_situation, nature_of_disease', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pid, grant_dte, sw_nr, discountid, discount, discount_amnt, notes, personal_circumstance, community_situation, nature_of_disease, reason, other_name, id_number, status', 'safe', 'on'=>'search'),
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
			'pid' => 'Pid',
			'grant_dte' => 'Grant Dte',
			'sw_nr' => 'Sw Nr',
			'discountid' => 'Discountid',
			'discount' => 'Discount',
			'discount_amnt' => 'Discount Amnt',
			'notes' => 'Notes',
			'personal_circumstance' => 'Personal Circumstance',
			'community_situation' => 'Community Situation',
			'nature_of_disease' => 'Nature Of Disease',
			'reason' => 'Reason',
			'other_name' => 'Other Name',
			'id_number' => 'Id Number',
			'status' => 'Status',
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

		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('grant_dte',$this->grant_dte,true);
		$criteria->compare('sw_nr',$this->sw_nr);
		$criteria->compare('discountid',$this->discountid,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('discount_amnt',$this->discount_amnt,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('personal_circumstance',$this->personal_circumstance,true);
		$criteria->compare('community_situation',$this->community_situation,true);
		$criteria->compare('nature_of_disease',$this->nature_of_disease,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('other_name',$this->other_name,true);
		$criteria->compare('id_number',$this->id_number,true);
		$criteria->compare('status',$this->status,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PersonCharityGrant the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
