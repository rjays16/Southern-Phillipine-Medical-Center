<?php
namespace SegHis\modules\dialysis\models;

/**
 * This is the model class for table "seg_dialysis_transaction".
 *
 * The followings are the available columns in table 'seg_dialysis_transaction':
 * @property string $transaction_nr
 * @property string $pid
 * @property string $dialyzer_serial_nr
 * @property string $machine_nr
 * @property string $transaction_date
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_date
 * @property string $history
 * @property string $status
 * @property string $request_flags
 * @property integer $dialyzer_reuse
 * @property integer $op_entry_no
 * @property integer $datetime_out
 *
 * The followings are the available model relations:
 * @property DialysisMachine $machine
 * @property DialysisDialyzer $dialyzer
 * @property DialysisPrebill $preBill
 */
class DialysisTransaction extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_dialysis_transaction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('transaction_nr', 'required'),
			array('dialyzer_reuse, op_entry_no', 'numerical', 'integerOnly'=>true),
			array('transaction_nr', 'length', 'max'=>14),
			array('pid, create_id, modify_id, status', 'length', 'max'=>12),
			array('machine_nr', 'length', 'max'=>10),
			array('datetime_out, transaction_date, dialyzer_serial_nr, create_time, modify_date, history, request_flags', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('transaction_nr, pid, dialyzer_serial_nr, machine_nr, transaction_date, create_id, create_time, modify_id, modify_date, history, status, request_flags, dialyzer_reuse', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'dialyzer' => array(self::HAS_ONE,'SegHis\modules\dialysis\models\DialysisDialyzer',array('dialyzer_serial_nr' => 'dialyzer_serial_nr')),
			'machine' => array(self::HAS_ONE,'SegHis\modules\dialysis\models\DialysisMachine',array('id' => 'machine_nr')),
			'preBill' => array(self::BELONGS_TO,'SegHis\modules\dialysis\models\DialysisPrebill',array('transaction_nr' => 'bill_nr')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'transaction_nr' => 'Transaction Nr',
			'pid' => 'Pid',
			'dialyzer_serial_nr' => 'Dialyzer Serial Nr',
			'machine_nr' => 'Machine Nr',
			'transaction_date' => 'Transaction Date',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_date' => 'Modify Date',
			'history' => 'History',
			'status' => 'Status',
			'request_flags' => 'Request Flags',
			'dialyzer_reuse' => 'Dialyzer Reuse',
			'op_entry_no' => 'Op Entry No',
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

		$criteria->compare('transaction_nr',$this->transaction_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('dialyzer_serial_nr',$this->dialyzer_serial_nr,true);
		$criteria->compare('machine_nr',$this->machine_nr,true);
		$criteria->compare('transaction_date',$this->transaction_date,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('request_flags',$this->request_flags,true);
		$criteria->compare('dialyzer_reuse',$this->dialyzer_reuse);
		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DialysisTransaction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Gets the patient last transaction.
	 * @param $pid
	 * @return DialysisTransaction|null Returns DialysisTransaction if any, otherwise null
	 */
	public static function getPatientLastTransaction($pid)
	{
		$criteria = new \CDbCriteria();
		$criteria->addColumnCondition(array(
			'pid' => $pid
		));
		$criteria->order = 'create_time DESC';
		return self::model()->find($criteria);
	}

	public static function getPatientLastTransactionss($pid)
	{
		$criteria = new \CDbCriteria();
		$criteria->addColumnCondition(array(
			'pid' => $pid

		));
		$criteria->order = 'create_time DESC';
		return self::model()->find($criteria);
	}


}
