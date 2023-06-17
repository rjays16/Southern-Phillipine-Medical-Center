<?php

/**
 * This is the model class for table "seg_cmap_accounts".
 *
 * The followings are the available columns in table 'seg_cmap_accounts':
 * @property integer $account_nr
 * @property string $account_name
 * @property string $account_address
 * @property string $running_balance
 * @property integer $is_locked
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property SegCmapAllotments[] $segCmapAllotments
 * @property SegCmapReferrals[] $segCmapReferrals
 */
class CmapAccounts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_cmap_accounts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_locked, is_deleted', 'numerical', 'integerOnly'=>true),
			array('account_name', 'length', 'max'=>100),
			array('account_address', 'length', 'max'=>200),
			array('running_balance', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('account_nr, account_name, account_address, running_balance, is_locked, is_deleted', 'safe', 'on'=>'search'),
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
			'segCmapAllotments' => array(self::HAS_MANY, 'SegCmapAllotments', 'cmap_account'),
			'segCmapReferrals' => array(self::HAS_MANY, 'SegCmapReferrals', 'cmap_account'),
		);
	}

	/**
	 * @{inheritDoc}
	 */
	public function scopes()
	{
		return array(
			'asc' => array(
				'order' => 't.account_name ASC'
			),
			'isNonDeleted' => array(
				'condition' => "t.is_deleted = '0'"
			),
			'isNonLocked' => array(
				'condition' => "t.is_locked = '0'"
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'account_nr' => 'Account Nr',
			'account_name' => 'Account Name',
			'account_address' => 'Account Address',
			'running_balance' => 'Running Balance',
			'is_locked' => 'Is Locked',
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

		$criteria->compare('account_nr',$this->account_nr);
		$criteria->compare('account_name',$this->account_name,true);
		$criteria->compare('account_address',$this->account_address,true);
		$criteria->compare('running_balance',$this->running_balance,true);
		$criteria->compare('is_locked',$this->is_locked);
		$criteria->compare('is_deleted',$this->is_deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CmapAccounts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
