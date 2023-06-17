<?php

/** 
 * This is the model class for table "seg_pay". 
 * 
 * The followings are the available columns in table 'seg_pay': 
 * @property string $or_no
 * @property integer $account_type
 * @property string $cancel_date
 * @property string $cancelled_by
 * @property string $or_date
 * @property string $or_name
 * @property string $or_address
 * @property string $encounter_nr
 * @property string $pid
 * @property string $company_id
 * @property string $amount_tendered
 * @property string $amount_due
 * @property string $remarks
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $from_nbs
 * @property string $requesting_dr
 * 
 * The followings are the available model relations: 
 * @property SegCreditMemoDetails[] $segCreditMemoDetails
 * @property SegCashierAccountSubtypes $accountType
 * @property SegPayChecks[] $segPayChecks
 * @property SegPayCreditCards $segPayCreditCards
 * @property SegPayDeposit $segPayDeposit
 * @property SegDiscount[] $segDiscounts
 * @property SegPayRequest[] $segPayRequests
 * @property CarePerson[] $carePeople
 */ 
class Cashier extends CActiveRecord
{ 
    /** 
     * @return string the associated database table name 
     */ 
    public function tableName() 
    { 
        return 'seg_pay'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
            array('or_no, cancelled_by, history', 'required'),
            array('account_type, from_nbs', 'numerical', 'integerOnly'=>true),
            array('or_no, encounter_nr, pid, company_id', 'length', 'max'=>12),
            array('cancelled_by, modify_id, create_id', 'length', 'max'=>35),
            array('or_name, requesting_dr', 'length', 'max'=>200),
            array('or_address, remarks', 'length', 'max'=>300),
            array('amount_tendered, amount_due', 'length', 'max'=>10),
            array('cancel_date, or_date, modify_dt, create_dt', 'safe'),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
            array('or_no, account_type, cancel_date, cancelled_by, or_date, or_name, or_address, encounter_nr, pid, company_id, amount_tendered, amount_due, remarks, history, modify_id, modify_dt, create_id, create_dt, from_nbs, requesting_dr', 'safe', 'on'=>'search'),
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
            'segCreditMemoDetails' => array(self::HAS_MANY, 'SegCreditMemoDetails', 'or_no'),
            'accountType' => array(self::BELONGS_TO, 'SegCashierAccountSubtypes', 'account_type'),
            'segPayChecks' => array(self::HAS_MANY, 'SegPayChecks', 'or_no'),
            'segPayCreditCards' => array(self::HAS_ONE, 'SegPayCreditCards', 'or_no'),
            'segPayDeposit' => array(self::HAS_ONE, 'SegPayDeposit', 'or_no'),
            'segDiscounts' => array(self::MANY_MANY, 'SegDiscount', 'seg_pay_discount(or_no, discountid)'),
            'cashierReq' => array(self::HAS_MANY, 'SegPayRequest', 'or_no'),
            'carePeople' => array(self::MANY_MANY, 'CarePerson', 'seg_prepaid_consultation(or_no, pid)'),
        ); 
    } 

    /** 
     * @return array customized attribute labels (name=>label) 
     */ 
    public function attributeLabels() 
    { 
        return array( 
            'or_no' => 'Or No',
            'account_type' => 'Account Type',
            'cancel_date' => 'Cancel Date',
            'cancelled_by' => 'Cancelled By',
            'or_date' => 'Or Date',
            'or_name' => 'Or Name',
            'or_address' => 'Or Address',
            'encounter_nr' => 'Encounter Nr',
            'pid' => 'Pid',
            'company_id' => 'Company',
            'amount_tendered' => 'Amount Tendered',
            'amount_due' => 'Amount Due',
            'remarks' => 'Remarks',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'from_nbs' => 'From Nbs',
            'requesting_dr' => 'Requesting Dr',
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

        $criteria->compare('or_no',$this->or_no,true);
        $criteria->compare('account_type',$this->account_type);
        $criteria->compare('cancel_date',$this->cancel_date,true);
        $criteria->compare('cancelled_by',$this->cancelled_by,true);
        $criteria->compare('or_date',$this->or_date,true);
        $criteria->compare('or_name',$this->or_name,true);
        $criteria->compare('or_address',$this->or_address,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('pid',$this->pid,true);
        $criteria->compare('company_id',$this->company_id,true);
        $criteria->compare('amount_tendered',$this->amount_tendered,true);
        $criteria->compare('amount_due',$this->amount_due,true);
        $criteria->compare('remarks',$this->remarks,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('from_nbs',$this->from_nbs);
        $criteria->compare('requesting_dr',$this->requesting_dr,true);

        return new CActiveDataProvider($this, array( 
            'criteria'=>$criteria, 
        )); 
    } 

    /** 
     * Returns the static model of the specified AR class. 
     * Please note that you should have this exact method in all your CActiveRecord descendants! 
     * @param string $className active record class name. 
     * @return Cashier the static model class 
     */ 
    public static function model($className=__CLASS__) 
    { 
        return parent::model($className); 
    }

    /**
     * This will return `Cashier` filter by encounter
     * @param $nr
     * @return CActiveRecord[]
     * @author michelle 03-10-15
     */
    public function findByEncounter($nr)
    {
       $criteria = new CDbCriteria();
       $criteria->addCondition('t.encounter_nr = ' . $nr);

       $model = self::model()->find($criteria);
       return $model;
    }
} 