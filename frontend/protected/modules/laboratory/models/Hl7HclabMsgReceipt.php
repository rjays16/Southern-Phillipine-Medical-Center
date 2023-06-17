<?php
namespace SegHis\modules\laboratory\models;

/**
 * This is the model class for table "seg_hl7_hclab_msg_receipt".
 *
 * The followings are the available columns in table 'seg_hl7_hclab_msg_receipt':
 * @property string $filename
 * @property string $msg_control_id
 * @property string $lis_order_no
 * @property string $msg_type_id
 * @property string $event_id
 * @property string $pid
 * @property string $test
 * @property string $hl7_msg
 * @property string $date_update
 */
class Hl7HclabMsgReceipt extends \CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_hl7_hclab_msg_receipt';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('filename, msg_type_id, event_id, hl7_msg', 'required'),
			array('filename', 'length', 'max'=>100),
			array('msg_control_id, lis_order_no', 'length', 'max'=>20),
			array('msg_type_id, event_id', 'length', 'max'=>5),
			array('pid', 'length', 'max'=>12),
			array('test', 'length', 'max'=>10),
			array('date_update', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('filename, msg_control_id, lis_order_no, msg_type_id, event_id, pid, test, hl7_msg, date_update', 'safe', 'on'=>'search'),
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
			'filename' => 'Filename',
			'msg_control_id' => 'Msg Control',
			'lis_order_no' => 'Lis Order No',
			'msg_type_id' => 'Msg Type',
			'event_id' => 'Event',
			'pid' => 'Pid',
			'test' => 'Test',
			'hl7_msg' => 'Hl7 Msg',
			'date_update' => 'Date Update',
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

		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('msg_control_id',$this->msg_control_id,true);
		$criteria->compare('lis_order_no',$this->lis_order_no,true);
		$criteria->compare('msg_type_id',$this->msg_type_id,true);
		$criteria->compare('event_id',$this->event_id,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('test',$this->test,true);
		$criteria->compare('hl7_msg',$this->hl7_msg,true);
		$criteria->compare('date_update',$this->date_update,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Hl7HclabMsgReceipt the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        /***
         * 
         */
        public static function getLabResults(\EclaimsEncounter $encounter) 
        {
            $criteria = new \CDbCriteria();
            $criteria->condition = "pid = '".$encounter->pid."'";
            
            if ($encounter->encounter_type == '1' || $encounter->encounter_type == '2'){
                $encounter_date = date("Y-m-d",strtotime($encounter->encounter_date));
                $discharged_date = date("Y-m-d H:i:s",strtotime($encounter->encounter_date." 23:23:59"));
            }else{
                $encounter_date = date("Y-m-d",strtotime($encounter->encounter_date));
                $discharged_date = (!$encounter->is_discharged) ? date('Y-m-d H:i:s') : date("Y-m-d",strtotime($encounter->discharge_date." ".$encounter->discharge_time));
            }            
            
            $criteria->addBetweenCondition("date_update", $encounter_date, $discharged_date, 'AND');
            $criteria->addCondition("msg_type_id = 'ORU'");
            $criteria->addCondition("event_id = 'R01'");                        
            $criteria->order = "date_update";            
            
            $result = self::model()->findAll($criteria);
            return $result;            
        }
}
