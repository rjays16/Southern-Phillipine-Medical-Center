<?php
/**
 * Created by PhpStorm.
 * User: Leira
 * Date: 7/19/2019
 * Time: 5:57 PM
 */

/**
 * This is the model class for table "seg_billing_caserate".
 *
 * The followings are the available columns in table 'seg_billing_caserate':
 * @property string $bill_nr
 * @property string $package_id
 * @property string $laterality
 * @property integer $rate_type
 * @property string $amount
 * @property string $hci_amount
 * @property string $pf_amount
 * @property integer $saved_multiplier
 * @property string $datetime
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property BillingEncounter $billNr
 * @property CaseRatePackages $package
 */

class BillingCaserate extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_billing_caserate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('bill_nr, package_id, laterality, rate_type', 'required'),
                array('rate_type, saved_multiplier, is_deleted', 'numerical', 'integerOnly'=>true),
                array('bill_nr, package_id', 'length', 'max'=>12),
                array('laterality', 'length', 'max'=>1),
                array('amount, hci_amount, pf_amount', 'length', 'max'=>20),
                array('datetime', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
                array('bill_nr, package_id, laterality, rate_type, amount, hci_amount, pf_amount, saved_multiplier, datetime, is_deleted', 'safe', 'on'=>'search'),
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
                'billNr' => array(self::BELONGS_TO, 'BillingEncounter', 'bill_nr'),
                'package' => array(self::BELONGS_TO, 'CaseRatePackages', 'package_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'bill_nr' => 'Bill Nr',
                'package_id' => 'Package',
                'laterality' => 'Laterality',
                'rate_type' => 'Rate Type',
                'amount' => 'Amount',
                'hci_amount' => 'Hci Amount',
                'pf_amount' => 'Pf Amount',
                'saved_multiplier' => 'Saved Multiplier',
                'datetime' => 'Datetime',
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

        $criteria->compare('bill_nr',$this->bill_nr,true);
        $criteria->compare('package_id',$this->package_id,true);
        $criteria->compare('laterality',$this->laterality,true);
        $criteria->compare('rate_type',$this->rate_type);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('hci_amount',$this->hci_amount,true);
        $criteria->compare('pf_amount',$this->pf_amount,true);
        $criteria->compare('saved_multiplier',$this->saved_multiplier);
        $criteria->compare('datetime',$this->datetime,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BillingCaserate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}