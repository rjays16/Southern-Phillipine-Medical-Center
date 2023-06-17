<?php
namespace SegHis\models;
/**
 * This is the model class for table "seg_blood_waiver_details".
 *
 * The followings are the available columns in table 'seg_blood_waiver_details':
 * @property string $batch_nr
 * @property string $pid
 * @property string $details
 * @property string $create_time
 * @property string $create_id
 * @property string $modify_time
 * @property string $modify_id
 */

class BloodWaiverDetails extends \CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_blood_waiver_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('batch_nr, modify_time', 'required'),
			array('batch_nr, pid, create_id, modify_id', 'length', 'max'=>20),
			array('details, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('batch_nr, pid, details', 'safe', 'on'=>'search'),
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
			'person' => array(self::BELONGS_TO,'Person','pid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'batch_nr' => 'Batch Nr',
			'pid' => 'Pid',
			'details' => 'Details',
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

		$criteria->compare('batch_nr',$this->batch_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('details',$this->details,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BloodWaiverDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public function getWaiverInformation($batch_nr){
		$criteria = new CDbCriteria();
        $criteria->addCondition('batch_nr = ' . $batch_nr);
        
        $model = self::model()->findAll($criteria);
        return $model;
	}
}
