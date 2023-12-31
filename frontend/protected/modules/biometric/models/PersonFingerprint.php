<?php
namespace SegHis\modules\biometric\models;
/**
 * This is the model class for table "seg_person_fingerprint".
 *
 * The followings are the available columns in table 'seg_person_fingerprint':
 * @property string $pid
 * @property string $rightPinky
 * @property string $rightRing
 * @property string $rightMiddle
 * @property string $rightIndex
 * @property string $rightThumb
 * @property string $leftPinky
 * @property string $leftRing
 * @property string $leftMiddle
 * @property string $leftIndex
 * @property string $leftThumb
 * @property string $birthYear
 * @property string $lastName
 * @property string $gender
 * @property integer $birthMonth
 *
 * The followings are the available model relations:
 * @property CarePerson $p
 */
class PersonFingerprint extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_person_fingerprint';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('birthMonth', 'numerical', 'integerOnly'=>true),
			array('pid', 'length', 'max'=>12),
			array('birthYear', 'length', 'max'=>4),
			array('lastName', 'length', 'max'=>60),
			array('gender', 'length', 'max'=>1),
			array('rightPinky, rightRing, rightMiddle, rightIndex, rightThumb, leftPinky, leftRing, leftMiddle, leftIndex, leftThumb', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pid, rightPinky, rightRing, rightMiddle, rightIndex, rightThumb, leftPinky, leftRing, leftMiddle, leftIndex, leftThumb, birthYear, lastName, gender, birthMonth', 'safe', 'on'=>'search'),
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
			'p' => array(self::BELONGS_TO, 'Person', 'pid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
            return array(
                'pid' => 'Pid',
                'rightPinky' => 'Right Pinky',
                'rightRing' => 'Right Ring',
                'rightMiddle' => 'Right Middle',
                'rightIndex' => 'Right Index',
                'rightThumb' => 'Right Thumb',
                'leftPinky' => 'Left Pinky',
                'leftRing' => 'Left Ring',
                'leftMiddle' => 'Left Middle',
                'leftIndex' => 'Left Index',
                'leftThumb' => 'Left Thumb',
                'birthYear' => 'Birth Year',
                'lastName' => 'Last Name',
                'gender' => 'Gender',
                'birthMonth' => 'Birth Month',
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

		$criteria=new \CDbCriteria;

		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('rightPinky',$this->rightPinky,true);
		$criteria->compare('rightRing',$this->rightRing,true);
		$criteria->compare('rightMiddle',$this->rightMiddle,true);
		$criteria->compare('rightIndex',$this->rightIndex,true);
		$criteria->compare('rightThumb',$this->rightThumb,true);
		$criteria->compare('leftPinky',$this->leftPinky,true);
		$criteria->compare('leftRing',$this->leftRing,true);
		$criteria->compare('leftMiddle',$this->leftMiddle,true);
		$criteria->compare('leftIndex',$this->leftIndex,true);
		$criteria->compare('leftThumb',$this->leftThumb,true);
		$criteria->compare('birthYear',$this->birthYear,true);
		$criteria->compare('lastName',$this->lastName,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('birthMonth',$this->birthMonth);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PersonFingerprint the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
