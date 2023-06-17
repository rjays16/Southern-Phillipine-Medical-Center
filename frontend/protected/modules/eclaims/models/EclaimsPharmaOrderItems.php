<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/20/2019
 * Time: 8:47 AM
 */

namespace SegHis\modules\eclaims\models;

class EclaimsPharmaOrderItems extends \CActiveRecord
{

    const STATUS_SERVED = 'S';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_pharma_order_items';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno, bestellnum', 'required'),
            array('quantity, is_consigned, is_deleted, returns, is_unused,is_fs, unused_qty', 'numerical', 'integerOnly' => true),
            array('refno, request_flag, discount_class, pricecash, pricecharge, price_orig', 'length', 'max' => 10),
            array('bestellnum', 'length', 'max' => 25),
            array('serve_status', 'length', 'max' => 1),
            array('serve_id', 'length', 'max' => 35),
            array('serve_dt, cancel_reason', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('refno, bestellnum, quantity, request_flag, discount_class, pricecash, pricecharge, price_orig, is_consigned, serve_status, serve_remarks, serve_id, serve_dt, cancel_reason, is_deleted, returns, is_unused,is_fs, unused_qty', 'safe', 'on' => 'search'),
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
            'cmapEntriesPharmacies' => array(self::HAS_MANY, 'CmapEntriesPharmacy', 'ref_no'),
            'cmapEntriesPharmacies1' => array(self::HAS_MANY, 'CmapEntriesPharmacy', 'service_code'),
            'lingapEntriesPharmacies' => array(self::HAS_MANY, 'LingapEntriesPharmacy', 'ref_no'),
            'lingapEntriesPharmacies1' => array(self::HAS_MANY, 'LingapEntriesPharmacy', 'service_code'),
            'lingapEntriesPharmacyWalkins' => array(self::HAS_MANY, 'LingapEntriesPharmacyWalkin', 'ref_no'),
            'lingapEntriesPharmacyWalkins1' => array(self::HAS_MANY, 'LingapEntriesPharmacyWalkin', 'service_code'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'refno' => 'Refno',
            'bestellnum' => 'Bestellnum',
            'quantity' => 'Quantity',
            'request_flag' => 'Request Flag',
            'discount_class' => 'Discount Class',
            'pricecash' => 'Pricecash',
            'pricecharge' => 'Pricecharge',
            'price_orig' => 'Price Orig',
            'is_consigned' => 'Is Consigned',
            'serve_status' => 'Serve Status',
            'serve_remarks' => 'Serve Remarks',
            'serve_id' => 'Serve',
            'serve_dt' => 'Serve Dt',
            'cancel_reason' => 'Cancel Reason',
            'is_deleted' => 'Is Deleted',
            'returns' => 'Returns',
            'is_unused' => 'Is Unused',
            'unused_qty' => 'Unused Qty',
            'is_fs' => 'Is Fs',
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

        $criteria = new CDbCriteria;

        $criteria->compare('refno', $this->refno, true);
        $criteria->compare('bestellnum', $this->bestellnum, true);
        $criteria->compare('quantity', $this->quantity);
        $criteria->compare('request_flag', $this->request_flag, true);
        $criteria->compare('discount_class', $this->discount_class, true);
        $criteria->compare('pricecash', $this->pricecash, true);
        $criteria->compare('pricecharge', $this->pricecharge, true);
        $criteria->compare('price_orig', $this->price_orig, true);
        $criteria->compare('is_consigned', $this->is_consigned);
        $criteria->compare('serve_status', $this->serve_status, true);
        $criteria->compare('serve_remarks', $this->serve_remarks, true);
        $criteria->compare('serve_id', $this->serve_id, true);
        $criteria->compare('serve_dt', $this->serve_dt, true);
        $criteria->compare('cancel_reason', $this->cancel_reason, true);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('returns', $this->returns);
        $criteria->compare('is_unused', $this->is_unused);
        $criteria->compare('unused_qty', $this->unused_qty);
        $criteria->compare('is_fs', $this->is_fs);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PharmaOrderItems the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
