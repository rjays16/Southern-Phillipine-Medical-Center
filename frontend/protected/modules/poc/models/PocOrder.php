<?php
namespace SegHis\modules\poc\models;

/**
 * This is the model class for table "seg_poc_order".
 *
 * The followings are the available columns in table 'seg_poc_order':
 * @property string $refno
 * @property string $order_dt
 * @property string $encounter_nr
 * @property string $pid
 * @property integer $is_cash
 * @property string $settlement_type
 * @property string $order_type
 * @property string $ward_id
 * @property string $source_req
 * @property string $discountid
 * @property string $discount
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 *
 * The followings are the available model relations:
 * @property Discount $discount0
 * @property TypeCharge $settlementType
 * @property CareEncounter $encounterNr
 * @property CarePerson $p
 * @property TypeRequestSource $sourceReq
 * @property CareWard $ward
 * @property PocOrderDetail[] $pocOrderDetails
 */

class PocOrder extends \CareActiveRecord
{        
    const START = "START";
    const STOP = "STOP";
    const VOID = "VOID";
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_poc_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno, modify_dt', 'required'),
            array('is_cash', 'numerical', 'integerOnly'=>true),
            array('refno, encounter_nr, pid', 'length', 'max'=>12),
            array('settlement_type, discountid, discount', 'length', 'max'=>10),
            array('order_type, source_req', 'length', 'max'=>20),
            array('ward_id', 'length', 'max'=>35),
            array('modify_id, create_id', 'length', 'max'=>40),
            array('order_dt, create_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('refno, order_dt, encounter_nr, pid, is_cash, settlement_type, order_type, ward_id, source_req, discountid, discount, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
            'discount0' => array(self::BELONGS_TO, 'Discount', 'discountid'),
            'settlementType' => array(self::BELONGS_TO, 'TypeCharge', 'settlement_type'),
            'encounterNr' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'p' => array(self::BELONGS_TO, 'Person', 'pid'),
            'sourceReq' => array(self::BELONGS_TO, 'TypeRequestSource', 'source_req'),
            'ward' => array(self::BELONGS_TO, 'SegHis\modules\admission\models\assignment\Ward', 'ward_id'),
            'pocOrderDetails' => array(self::HAS_MANY, 'SegHis\modules\poc\models\PocOrderDetail', 'refno'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'refno' => 'Refno',
            'order_dt' => 'Order Dt',
            'encounter_nr' => 'Encounter Nr',
            'pid' => 'Pid',
            'is_cash' => 'Is Cash',
            'settlement_type' => 'Settlement Type',
            'order_type' => 'Order Type',
            'ward_id' => 'Ward',
            'source_req' => 'Source Req',
            'discountid' => 'Discountid',
            'discount' => 'Discount',
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
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new \CDbCriteria;

        $criteria->compare('refno',$this->refno,true);
        $criteria->compare('order_dt',$this->order_dt,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('pid',$this->pid,true);
        $criteria->compare('is_cash',$this->is_cash);
        $criteria->compare('settlement_type',$this->settlement_type,true);
        $criteria->compare('order_type',$this->order_type,true);
        $criteria->compare('ward_id',$this->ward_id,true);
        $criteria->compare('source_req',$this->source_req,true);
        $criteria->compare('discountid',$this->discountid,true);
        $criteria->compare('discount',$this->discount,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);

        return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PocOrder the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /***
     * 
     */
    public function getLastPocOrderNo($givenRefNo = "") 
    {
        $criteria = new \CDbCriteria();
        if (!empty($givenRefNo)) {
            $criteria->condition = "refno >= '{$givenRefNo}'";
        }
        $criteria->order = "refno DESC";            
        $criteria->limit = 1;

        $result = self::model()->findAll($criteria);
        return !empty($result) ? $result[0]->refno : null;
    }        

    /***
     * 
     */
    public function getNextPocOrderNo()
    {            
        $refno = $this->getLastPocOrderNo(date('Y')."000001");                                    
        if (!is_null($refno)) {
            $orderNo = (int)$refno + 1;
        }             
        else {
            $orderNo = date('Y')."000001";
        }
        return $orderNo;
    }

    /***
     * 
     */
    public function getLatestPocOrder($encounter_nr)
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = "encounter_nr = '{$encounter_nr}'";
        $criteria->order = "order_dt DESC";            
        $criteria->limit = 1;

        $result = self::model()->find($criteria);
        return $result;            
    }      

    /***
     * 
     * 
     */
    public function getLatestStartOrder($encounter_nr) {
        $criteria = new \CDbCriteria();
        $criteria->condition = "encounter_nr = '{$encounter_nr}'";
        $criteria->addCondition("order_type = '".self::START."'");
        $criteria->order = "order_dt DESC";            
        $criteria->limit = 1;

        $result = self::model()->find($criteria);
        return $result;                    
    }
    
    /***
     * 
     * 
     */
    public function getPocOrder($refno) {
        $pocOrder = self::model()->findByPk($refno);
        return $pocOrder;
    }
    
    /***
     * 
     * 
     */
    public function cancelCashStartOrder($refno) {        
        $transaction = null;
        if (!\Yii::app()->getDb()->getCurrentTransaction()->getActive()) {
            $transaction =  \Yii::app()->getDb()->beginTransaction();
        }                                    
        try {                
            self::model()->updateAll(array('order_type' => self::VOID), "refno = '".$refno."' AND is_cash = 1"); 
            if ($transaction != null) {
                $transaction->commit();
            }
            return true;

        } catch (\Exception $e) {
            if ( ($transaction != null) && ($transaction->active) ) {
                $transaction->rollback();                
            }
            return false;
        }                
    }    
}
