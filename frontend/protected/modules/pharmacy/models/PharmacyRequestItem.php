<?php
namespace SegHis\modules\pharmacy\models;

/**
 * This is the model class for table "seg_pharma_order_items".
 *
 * The followings are the available columns in table 'seg_pharma_order_items':
 * @property string $refno
 * @property string $bestellnum
 * @property integer $requested_qty
 * @property integer $quantity
 * @property string $request_flag
 * @property string $discount_class
 * @property string $pricecash
 * @property string $pricecharge
 * @property string $price_orig
 * @property integer $is_consigned
 * @property string $serve_status
 * @property string $serve_remarks
 * @property string $serve_id
 * @property string $serve_dt
 * @property string $cancel_reason
 * @property integer $is_deleted
 * @property integer $returns
 * @property integer $is_unused
 * @property integer $unused_qty
 * @property string $inv_refno
 * @property string $inv_uid
 *
 * The followings are the available model relations:
 * @property PharmacyRequest $request
 */
class PharmacyRequestItem extends \CareActiveRecord
{
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
            array('refno, bestellnum, serve_remarks', 'required'),
            array('requested_qty, quantity, is_consigned, is_deleted, returns, is_unused, unused_qty', 'numerical', 'integerOnly' => true),
            array('refno, request_flag, discount_class, pricecash, pricecharge, price_orig', 'length', 'max' => 10),
            array('bestellnum', 'length', 'max' => 25),
            array('serve_status', 'length', 'max' => 1),
            array('serve_id', 'length', 'max' => 35),
            array('inv_refno', 'length', 'max' => 12),
            array('inv_uid', 'length', 'max' => 32),
            array('serve_dt, cancel_reason', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('refno, bestellnum, requested_qty, quantity, request_flag, discount_class, pricecash, pricecharge, price_orig, is_consigned, serve_status, serve_remarks, serve_id, serve_dt, cancel_reason, is_deleted, returns, is_unused, unused_qty, inv_refno, inv_uid', 'safe', 'on' => 'search'),
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
            'request' => array(self::BELONGS_TO, 'SegHis\modules\pharmacy\models\PharmacyRequest', 'refno')
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
            'requested_qty' => 'Requested Qty',
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
            'inv_refno' => 'Inv Refno',
            'inv_uid' => 'Inv Uid',
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

        $criteria = new \CDbCriteria;

        $criteria->compare('refno', $this->refno, true);
        $criteria->compare('bestellnum', $this->bestellnum, true);
        $criteria->compare('requested_qty', $this->requested_qty);
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
        $criteria->compare('inv_refno', $this->inv_refno, true);
        $criteria->compare('inv_uid', $this->inv_uid, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PharmacyRequestItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
