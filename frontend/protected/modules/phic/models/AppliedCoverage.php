<?php
namespace SegHis\modules\phic\models;

use SegHis\modules\poc\models\PocOrder;

/**
 * This is the model class for table "seg_applied_coverage".
 *
 * The followings are the available columns in table 'seg_applied_coverage':
 * @property string $ref_no
 * @property string $source
 * @property string $item_code
 * @property string $hcare_id
 * @property integer $priority
 * @property string $coverage
 * @property string $history
 *
 * The followings are the available model relations:
 * @property CareInsuranceFirm $hcare
 */
class AppliedCoverage extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_applied_coverage';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('ref_no, source, item_code, hcare_id', 'required'),
            array('priority', 'numerical', 'integerOnly'=>true),
            array('ref_no', 'length', 'max'=>15),
            array('source', 'length', 'max'=>1),
            array('item_code', 'length', 'max'=>25),
            array('hcare_id', 'length', 'max'=>8),
            array('coverage', 'length', 'max'=>10),
            array('history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('ref_no, source, item_code, hcare_id, priority, coverage, history', 'safe', 'on'=>'search'),
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
            'hcare' => array(self::BELONGS_TO, 'CareInsuranceFirm', 'hcare_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'ref_no' => 'Ref No',
            'source' => 'Source',
            'item_code' => 'Item Code',
            'hcare_id' => 'Hcare',
            'priority' => 'Priority',
            'coverage' => 'Coverage',
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
        $criteria=new \CDbCriteria;

        $criteria->compare('ref_no',$this->ref_no,true);
        $criteria->compare('source',$this->source,true);
        $criteria->compare('item_code',$this->item_code,true);
        $criteria->compare('hcare_id',$this->hcare_id,true);
        $criteria->compare('priority',$this->priority);
        $criteria->compare('coverage',$this->coverage,true);
        $criteria->compare('history',$this->history,true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AppliedCoverage the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }       
    
    /***
     * 
     */
    public function applyCoverage(PocOrder $order)
    {        
        $coverage = self::model()->findAllByAttributes(array('ref_no'    => 'T'.$order->encounter_nr, 
                                                             'source'    => 'L',
                                                             'item_code' => $order->pocOrderDetails[0]->service_code, 
                                                             'hcare_id'  => \Yii::app()->params['PHIC']));
//        $qty = $orderD->quantity;
        $qty = 1;  // Default
        $orderD = $order->pocOrderDetails[0];
        $newTotal = (($orderD->unit_price * $qty) - (is_null($order->discount) ? 0 : $order->discount))/$qty;

        $transaction = \Yii::app()->getDb()->getCurrentTransaction();
        if (is_null($transaction)) {
            $transaction =  \Yii::app()->getDb()->beginTransaction();
        }
        else {
            if (!$transaction->getActive()) {
                $transaction =  \Yii::app()->getDb()->beginTransaction();
            }
        }
        
        try {         
            if (empty($coverage)) {
                $coverAmnt = $newTotal;            
                $sql = "INSERT INTO ".$this->tableName()." (ref_no, source, item_code, hcare_id, coverage, history) \n".
                       "VALUES (:refNo, 'L', :itemCode, :hcareId, :coverAmount, :coverHistory)";
                $parameters = array(':refNo'        => 'T'.$order->encounter_nr, 
                                    ':itemCode'     => $orderD->service_code, 
                                    ':hcareId'      => \Yii::app()->params['PHIC'],
                                    ':coverAmount'  => $coverAmnt,
                                    ':coverHistory' => 'Applied coverage for POC service rendered'
                              );
                \Yii::app()->db->createCommand($sql)->execute($parameters);
            }
            else {
                $coverAmnt = $coverage[0]->coverage + $newTotal;
                self::model()->updateAll(array('coverage' => $coverAmnt), 
                                               "ref_no = '".'T'.$order->encounter_nr."' AND source = 'L' AND item_code = '".$orderD->service_code."' AND hcare_id = ".\Yii::app()->params['PHIC']);
            }
            
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
