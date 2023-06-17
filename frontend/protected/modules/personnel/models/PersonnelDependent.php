<?php
namespace SegHis\modules\personnel\models;
/**
 * This is the model class for table "seg_dependents".
 *
 * The followings are the available columns in table 'seg_dependents':
 * @property string $parent_pid
 * @property string $dependent_pid
 * @property string $relationship
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 *
 * @property Personnel $personnel
 */
class PersonnelDependent extends \CareActiveRecord
{

	const STATUS_MEMBER = 'member';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_dependents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_pid, dependent_pid', 'required'),
			array('parent_pid, dependent_pid', 'length', 'max'=>12),
			array('relationship', 'length', 'max'=>25),
			array('status', 'length', 'max'=>9),
			array('modify_id, create_id', 'length', 'max'=>50),
			array('history, modify_dt, create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('parent_pid, dependent_pid, relationship, status, history, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
			'personnel' => array(self::BELONGS_TO, 'SegHis\modules\personnel\models\Personnel', array('parent_pid' => 'pid'))
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'parent_pid' => 'Parent Pid',
			'dependent_pid' => 'Dependent Pid',
			'relationship' => 'Relationship',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
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

		$criteria->compare('parent_pid',$this->parent_pid,true);
		$criteria->compare('dependent_pid',$this->dependent_pid,true);
		$criteria->compare('relationship',$this->relationship,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PersonnelDependent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @param $pid
	 * @return null|PersonnelDependent
	 */
	public static function findActiveDependentByPid($pid)
	{
		$criteria = new \CDbCriteria();
		$criteria->addColumnCondition(array('dependent_pid' => $pid, 'status' => 'member'));

		/* @var $dependent PersonnelDependent */
		$dependent = PersonnelDependent::model()->find($criteria);

		if($dependent) {
			if(!$dependent->personnel->isActive())
				return null;
		}
		return $dependent;
	}

}
