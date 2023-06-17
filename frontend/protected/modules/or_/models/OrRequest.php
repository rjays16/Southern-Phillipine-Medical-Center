<?php

/**
 * This is the model class for table "seg_or_request".
 *
 * The followings are the available columns in table 'seg_or_request':
 * @property string $or_refno
 * @property string $encounter_nr
 * @property integer $trans_type
 * @property integer $is_urgent
 * @property integer $dept_nr
 * @property string $dr_nr
 * @property string $or_type
 * @property string $or_case
 * @property string $request_flag
 * @property string $date_requested
 * @property string $requirements
 * @property string $create_id
 * @property string $create_date
 * @property string $modify_date
 * @property string $modify_id
 * @property string $history
 *
 * The followings are the available model relations:
 * @property OrChecklist[] $segOrChecklists
 * @property OrPackageUse[] $orPackageUses
 * @property OrPostOpDetails $orPostOpDetails
 * @property OrPreOpDetails $orPreOpDetails
 * @property CareEncounter $encounterNr
 * @property CareDepartment $deptNr
 * @property OrRequestDetails $orRequestDetails
 */
class OrRequest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_refno', 'required'),
			array('trans_type, is_urgent, dept_nr', 'numerical', 'integerOnly'=>true),
			array('or_refno, encounter_nr', 'length', 'max'=>258),
			array('dr_nr', 'length', 'max'=>11),
			array('or_type', 'length', 'max'=>5),
			array('or_case', 'length', 'max'=>12),
			array('request_flag', 'length', 'max'=>9),
			array('create_id, modify_id', 'length', 'max'=>20),
			array('date_requested, requirements, create_date, history', 'safe'),
			array('modify_date, create_date','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_id, create_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_date','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('modify_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('or_refno, encounter_nr, trans_type, is_urgent, dept_nr, dr_nr, or_type, or_case, request_flag, date_requested, requirements, create_id, create_date, modify_date, modify_id, history', 'safe', 'on'=>'search'),
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
			'segOrChecklists' => array(self::MANY_MANY, 'OrChecklist', 'seg_or_checklist_request_data(refno, checklist_id)'),
			'orPackageUses' => array(self::HAS_MANY, 'OrPackageUse', 'or_refno'),
			'orPostOpDetails' => array(self::HAS_ONE, 'OrPostOpDetails', 'or_refno'),
			'orPreOpDetails' => array(self::HAS_ONE, 'OrPreOpDetails', 'or_refno'),
			'encounterNr' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
			'deptNr' => array(self::BELONGS_TO, 'CareDepartment', 'dept_nr'),
			'orRequestDetails' => array(self::HAS_ONE, 'OrRequestDetails', 'or_refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'or_refno' => 'Or Refno',
			'encounter_nr' => 'Encounter Nr',
			'trans_type' => 'Trans Type',
			'is_urgent' => 'Is Urgent',
			'dept_nr' => 'Dept Nr',
			'dr_nr' => 'Dr Nr',
			'or_type' => 'Or Type',
			'or_case' => 'Or Case',
			'request_flag' => 'Request Flag',
			'date_requested' => 'Date Requested',
			'requirements' => 'Requirements',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
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
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('or_refno',$this->or_refno,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('trans_type',$this->trans_type);
		$criteria->compare('is_urgent',$this->is_urgent);
		$criteria->compare('dept_nr',$this->dept_nr);
		$criteria->compare('dr_nr',$this->dr_nr,true);
		$criteria->compare('or_type',$this->or_type,true);
		$criteria->compare('or_case',$this->or_case,true);
		$criteria->compare('request_flag',$this->request_flag,true);
		$criteria->compare('date_requested',$this->date_requested,true);
		$criteria->compare('requirements',$this->requirements,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('history',$this->history,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrRequest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
