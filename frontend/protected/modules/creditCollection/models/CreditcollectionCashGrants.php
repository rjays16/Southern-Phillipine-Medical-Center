<?php

Yii::import('or_.models.*');

/**
 * This is the model class for table "seg_creditcollection_cash_grants".
 *
 * The followings are the available columns in table 'seg_creditcollection_cash_grants':
 * @property string $id
 * @property string $refno
 * @property string $req_source
 * @property string $account
 * @property string $sub_account
 * @property string $amount
 * @property string $control_no
 * @property string $date
 * @property string $remarks
 * @property string $history
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property integer $is_full
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property SegGrantAccountType $account0
 * @property SegGrantAccounts $subAccount
 */
class CreditcollectionCashGrants extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_creditcollection_cash_grants';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno', 'required'),
            array('is_full, is_deleted', 'numerical', 'integerOnly'=>true),
            array('refno', 'length', 'max'=>12),
            array('req_source', 'length', 'max'=>20),
            array('account, sub_account', 'length', 'max'=>10),
            array('amount', 'length', 'max'=>18),
            array('control_no', 'length', 'max'=>100),
            array('create_id, modify_id', 'length', 'max'=>35),
            array('date, remarks, history, create_time, modify_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, refno, req_source, account, sub_account, amount, control_no, date, remarks, history, create_id, create_time, modify_id, modify_time, is_full, is_deleted', 'safe', 'on'=>'search'),
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
            'account0' => array(self::BELONGS_TO, 'SegGrantAccountType', 'account'),
            'subAccount' => array(self::BELONGS_TO, 'SegGrantAccounts', 'sub_account'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'refno' => 'Refno',
            'req_source' => 'Req Source',
            'account' => 'Account',
            'sub_account' => 'Sub Account',
            'amount' => 'Amount',
            'control_no' => 'Control No',
            'date' => 'Date',
            'remarks' => 'Remarks',
            'history' => 'History',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'is_full' => 'Is Full',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('refno',$this->refno,true);
        $criteria->compare('req_source',$this->req_source,true);
        $criteria->compare('account',$this->account,true);
        $criteria->compare('sub_account',$this->sub_account,true);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('control_no',$this->control_no,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('remarks',$this->remarks,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('is_full',$this->is_full);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CreditcollectionCashGrants the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getAllEncounterRequests(){
        $encounter_nr = $_GET['encounter_nr'];

        $labFields = " 'LAB' AS costcenter, sls.refno, sls.create_dt `date`, CONCAT(ls.`serv_dt`, ' ', ls.`serv_tm`) AS date_requested, s.`name` itemname, sls.`service_code`, sls.`quantity`, FORMAT(sls.`price_cash`,2) orig_price, FORMAT(sls.`price_cash` * sls.`quantity`, 2) adjstd_price, sls.`request_flag`, sls.`is_served`";

        $labFrom = ' FROM seg_lab_servdetails sls 
                INNER JOIN seg_lab_serv ls 
                    ON sls.refno = ls.refno 
                LEFT JOIN seg_lab_services s 
                    ON s.service_code = sls.service_code
                LEFT JOIN seg_lab_service_groups g
                    ON g.group_code = s.group_code';

        $labWhere = " WHERE ls.encounter_nr=:encounter_nr AND ls.is_cash = 1 AND sls.status NOT IN ('deleted','hidden','inactive','void')";

        $miscFields = " 'MISC' AS costcenter, md.`refno`, md.`create_dt` `date`, m.`chrge_dte` AS date_requested, s.`name` itemname, md.`service_code`, md.`quantity`, FORMAT(md.`chrg_amnt`,2) orig_price, FORMAT(md.`adjusted_amnt`, 2) adjstd_price, md.`request_flag`, '0'";

        $miscFrom = " FROM
                        seg_misc_service_details md 
                      INNER JOIN seg_misc_service m 
                        ON m.refno = md.refno 
                      INNER JOIN seg_other_services s 
                        ON s.alt_service_code = md.service_code";
        $miscWhere = " WHERE m.`encounter_nr` = :encounter_nr AND m.is_cash = 1";

        $pharmaFields = " 'PHARMA' AS costcenter, oi.refno, o.create_time `date`, o.orderdate date_requested, p.`artikelname` itemname, oi.`bestellnum` service_code, oi.`quantity`, FORMAT(oi.`pricecash`,2) `orig_price`, FORMAT(oi.`pricecash` * oi.`quantity`, 2) `adjstd_price`, oi.request_flag, oi.serve_status = 'S' `is_served`";

        $pharmaFrom = " FROM
                            seg_pharma_order_items oi 
                          INNER JOIN seg_pharma_orders o 
                            ON o.refno = oi.refno 
                          INNER JOIN care_pharma_products_main p 
                            ON p.bestellnum = oi.bestellnum";
        $pharmaWhere = " WHERE o.encounter_nr = :encounter_nr AND o.is_cash = '1'";

        $radFields = " 'RAD' AS costcenter, rd.refno,r.create_dt `date`, CONCAT(r.request_date, ' ', r.request_time) date_requested, s.`name` AS itemname, rd.`service_code`, 1 as quantity, FORMAT(rd.`price_cash`,2) `orig_price`, FORMAT(rd.`price_cash`, 2) `adjstd_price`, rd.request_flag, rd.`is_served`";

        $radFrom = " FROM
                        care_test_request_radio rd 
                  INNER JOIN seg_radio_serv r 
                    ON r.refno = rd.refno 
                  INNER JOIN seg_radio_services s 
                    ON s.service_code = rd.service_code";

        $radWhere = " WHERE r.encounter_nr = :encounter_nr AND r.is_cash = '1' AND rd.status NOT IN ('deleted','hidden','inactive','void') AND r.fromdept='RD'";

        $params = array(':encounter_nr'=> $encounter_nr);
        $orderBy = " ORDER BY DATE DESC";

        if($_GET['status'] == 'settled'){
            $miscWhere .= " AND md.request_flag IS NOT NULL";
            $pharmaWhere .= " AND (oi.request_flag IS NOT NULL OR oi.serve_status = 'S')";
            $radWhere .= " AND (rd.request_flag IS NOT NULL OR rd.is_served = 1)";
            $labWhere .= " AND (sls.request_flag IS NOT NULL OR sls.is_served = 1)";
        }
        elseif($_GET['status'] == 'pending'){
            $miscWhere .= " AND md.request_flag IS NULL";
            $pharmaWhere .= " AND oi.request_flag IS NULL AND oi.serve_status = 'N'";
            $radWhere .= " AND rd.request_flag IS NULL AND rd.is_served <> 1";
            $labWhere .= " AND sls.request_flag IS NULL AND sls.is_served <> 1";
        }

        if($_GET['date_from'] && $_GET['date_to']){
            $miscWhere .= " AND (DATE(m.chrge_dte) BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."')";
            $pharmaWhere .= " AND (DATE(o.orderdate) BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."')";
            $radWhere .= " AND (r.request_date BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."')";
            $labWhere .= " AND (ls.serv_dt BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."')";
        }

        if(!$_GET['request_source']){
            $labsql = "SELECT ".$labFields.$labFrom.$labWhere;
            $miscsql = "SELECT ".$miscFields.$miscFrom.$miscWhere;
            $pharmasql = "SELECT ".$pharmaFields.$pharmaFrom.$pharmaWhere;
            $radsql = "SELECT ".$radFields.$radFrom.$radWhere;

            $unionsql = $labsql." UNION ALL ".$miscsql." UNION ALL ".$pharmasql." UNION ALL ".$radsql.$orderBy;

            $countSQL = "SELECT COUNT(*) FROM (".$unionsql.") as temp";
            $selectSQL = $unionsql;
        }else{
            if($_GET['request_source'] == 'MISC'){
                $fields = $miscFields;
                $from = $miscFrom;
                $where = $miscWhere;
            }elseif($_GET['request_source'] == 'PHARMA'){
                $fields = $pharmaFields;
                $from = $pharmaFrom;
                $where = $pharmaWhere;
            }elseif($_GET['request_source'] == 'RD'){
                $fields = $radFields;
                $from = $radFrom;
                $where = $radWhere;
            }elseif($_GET['request_source'] == 'LD' || $_GET['request_source'] == 'SPL' || $_GET['request_source'] == 'BB'){
                if($_GET['request_source'] == 'LD')
                    $req_source = 'LB';
                else $req_source = $_GET['request_source'];

                $fields = $labFields;
                $from = $labFrom;
                $labWhere .= " AND ls.ref_source='".$req_source."'";
                $where = $labWhere;
            }

            $countSQL = 'SELECT COUNT(*)'.$from.$where;
            $selectSQL = 'SELECT SQL_CALC_FOUND_ROWS'.$fields.$from.$where." ORDER BY date DESC";
            
        }
        

        $command=Yii::app()->db->createCommand($countSQL);

        foreach ($params as $key => $val){
            $command->bindParam($key, $val, PDO::PARAM_STR);
        }

        $count = $command->queryScalar();
// var_dump($selectSQL);die;
        return new CSqlDataProvider($selectSQL, array(
            'keyField' => 'refno',
            'totalItemCount'=>$count,
            'params'=>$params,
            'sort'=>array(
                'attributes'=>array(
                    'date'
                ),
                'defaultOrder'=>array(
                    'date'=>CSort::SORT_ASC,
                ),
            ),
            'pagination'=>array(
                'pageSize'=>10,
            ),
        ));


        /*$criteria = new CDbCriteria();
        $criteria->select = array("t.*", "UPPER(alt_name) AS alt_name", "UPPER(name) AS sub_account", "FORMAT(amount, 2) AS amount");
        $criteria->order = 't.create_time DESC';
        
        $criteria->addColumnCondition(array(
                        't.encounter_nr' => $_GET['encounter_nr'],
                        't.is_deleted' => 0
                    ));

        $criteria->with = array('account0' => 
            array(
                'select' => 'alt_name',
                'joinType' => 'LEFT JOIN'
            ), 
            'subAccount' => array(
                'select' => 'name',
                'joinType' => 'LEFT JOIN'
                )
        );*/
        // var_dump($labrequests);die;
        /*$dataProvider = new \CSqlDataProvider($labrequests, array(
            'pagination'=>array(
                'pageSize'=>10,
            )
        ));

        return $dataProvider;*/
    }

    public function getRequestDetails($refno,$costcenter,$itemcode){
        $criteria = new CDbCriteria;

        if($costcenter == 'LAB'){
            $criteria->select = array("t.*", "t.price_cash as itemcharge", "serviceCode.name as item_name");
            $criteria->condition = "refno='".$refno."' AND t.service_code='".$itemcode."'";
            $criteria->with = array('serviceCode' => 
                                    array(
                                        'select' => 'name',
                                        'joinType' => 'LEFT JOIN'
                                    )
                                );
            $models = LabServdetails::model()->findAll($criteria);
        }elseif($costcenter == 'MISC'){
            $criteria->select = array("t.*", "adjusted_amnt as itemcharge", "serviceCode.name as item_name");
            $criteria->condition = "refno='".$refno."' AND t.service_code='".$itemcode."'";
            $criteria->with = array('serviceCode' => 
                                    array(
                                        'select' => 'name',
                                        'joinType' => 'LEFT JOIN'
                                    )
                                );
            $models = MiscServiceDetails::model()->findAll($criteria);
        }elseif($costcenter == 'RAD'){
            $criteria->select = array("t.*", "t.price_cash as itemcharge", "serviceCode.name as item_name");
            $criteria->condition = "refno='".$refno."' AND t.service_code='".$itemcode."'";
            $criteria->with = array('serviceCode' => 
                                    array(
                                        'select' => 'name',
                                        'joinType' => 'LEFT JOIN'
                                    )
                                );
            $models = CareTestRequestRadio::model()->findAll($criteria);
        }elseif($costcenter == 'PHARMA'){
            $criteria->select = array("t.*", "t.pricecash as itemcharge", "serviceCode.artikelname as item_name");
            $criteria->condition = "refno='".$refno."' AND t.bestellnum='".$itemcode."'";
            $criteria->with = array('serviceCode' => 
                                    array(
                                        'select' => 'artikelname',
                                        'joinType' => 'LEFT JOIN'
                                    )
                                );
            $models = PharmaOrderItems::model()->findAll($criteria);
        }

        return $models;
    }

    public function getAllEncounterGrants($encounter_nr){
        $criteria = new CDbCriteria;

        $criteria->condition = "encounter_nr = '".$encounter_nr."' AND is_deleted <> 1";

        return $this->findAll($criteria);
    }
} 