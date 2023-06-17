<?php

/**
 * This is the model class for table "seg_credit_collection_ledger".
 *
 * The followings are the available columns in table 'seg_credit_collection_ledger':
 * @property integer $ref_no
 * @property string $encounter_nr
 * @property string $bill_nr
 * @property string $entry_type
 * @property double $amount
 * @property string $pay_type
 * @property string $control_nr
 * @property string $description
 * @property string $create_id
 * @property string $modify_id
 * @property string $create_date
 * @property string $modify_date
 * @property string $history
 * @property integer $is_deleted
 * @property integer $is_final
 *
 * The followings are the available model relations:
 * @property CareEncounter $encounterNr
 * @property SegBillingEncounter $billNr
 */
class CreditCollection extends CareActiveRecord
{
    const TYPE_FUND_CHECKS = 'fund_checks';
    const TYPE_PCSO = 'pcso';
    const TYPE_DSWD = 'dswd';
    const TYPE_SS = 'ss';
    const TYPE_COH = 'coh2';
    const TYPE_PAID = 'paid';
    const TYPE_PARTIAL = 'partial';
    const TYPE_PN = 'pn';
    const TYPE_MAP = 'map';
    const TYPE_LINGAP_EMERGENCY = 'lingap-emergency';
    const TYPE_LINGAP_RECOMMENDATION = 'lingap-recommendation';

    public $remarks;
    public $ref_no;
    public $total;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_credit_collection_ledger';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('is_deleted, is_final', 'numerical', 'integerOnly' => true),
            array('amount', 'numerical'),
            array('encounter_nr', 'length', 'max' => 12),
            array('bill_nr', 'length', 'max' => 25),
            array('entry_type', 'length', 'max' => 6),
            array('pay_type, control_nr', 'length', 'max' => 20),
            array('description', 'length', 'max' => 200),
            array('create_id, modify_id', 'length', 'max' => 35),
            array('create_time, modify_time, history', 'safe'),
            array('approved_date', 'date'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, account_nr, ref_no, encounter_nr, bill_nr, entry_type, amount, pay_type, control_nr, description, create_id, modify_id, create_time, modify_time, history, is_deleted, is_final', 'safe', 'on' => 'search'),
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
            'encounter' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
            'bill' => array(self::BELONGS_TO, 'SegBillingEncounter', 'bill_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'ref_no' => 'Ref No',
            'encounter_nr' => 'Encounter Nr',
            'bill_nr' => 'Bill Nr',
            'entry_type' => 'Entry Type',
            'amount' => 'Amount',
            'pay_type' => 'Pay Type',
            'control_nr' => 'Control Nr',
            'description' => 'Description',
            'create_id' => 'Create',
            'modify_id' => 'Modify',
            'create_time' => 'Create Date',
            'modify_time' => 'Modify Date',
            'history' => 'History',
            'is_deleted' => 'Is Deleted',
            'is_final' => 'Is Final',
            'account_nr' => 'Account Nr'
        );
    }

    /**
     * {@inheritDoc}
     * @return type
     */
    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    public function scopes()
    {
        return array(
            'unDeleted' => array(
                'condition' => "t.is_deleted = '0' OR t.is_deleted IS NULL"
            )
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

        $criteria->compare('ref_no', $this->ref_no);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('bill_nr', $this->bill_nr, true);
        $criteria->compare('entry_type', $this->entry_type, true);
        $criteria->compare('amount', $this->amount);
        $criteria->compare('pay_type', $this->pay_type, true);
        $criteria->compare('control_nr', $this->control_nr, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('modify_time', $this->modify_time, true);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('is_final', $this->is_final);
        $criteria->compare('account_nr', $this->account_nr);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CreditCollection the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * This will execute bulk insert.
     * @param $model
     * @param $data
     * @return bool
     * @author michelle 03-05-15
     */
    public static function saveMultipleRecord($model, $data)
    {
        $trans = Yii::app()->db->beginTransaction();
        try {
            $builder = Yii::app()->db->schema->commandBuilder;
            $command = $builder->createMultipleInsertCommand($model, $data);
            $exec = $command->execute();

            if (!$exec)
                return false;

            $trans->commit();
            return true;

        } catch (Exception $e) {
            $trans->rollback();
            throw new CHttpException(500, 'Unable to continue operation. Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * This will return remaining computed balance
     * @param $entryItems
     * @param $remainingBalance
     * @author michelle 03-09-15
     */
    public static function computeBalance($entryItems, $remainingBalance, $isRunningBalance = true)
    {
        $amount = 0;
        $total = 0.00;
        foreach ($entryItems as $item) {
            if($item->pay_type == 'ss'){
                if($item->entry_type == 'debit')
                    $amount += $item->total;
            }else
                $amount += $item->total;
        }
        if ($isRunningBalance)
            $total = $remainingBalance - $amount;
        else
            $total = $amount;

        return number_format($total, 2, '.', '');
        //return $total;
    }

    /**
     * This will check latest charity grant filter by encounter_nr
     * @param $nr
     * @return null
     */
    public function getCharityGrantsByEncounter($nr)
    {
        $sql = Yii::app()->db->createCommand("SELECT encounter_nr,
										SUBSTRING(MAX(CONCAT(grant_dte,grant_dte)),20) AS grant_dte,
										SUBSTRING(MAX(CONCAT(grant_dte,discount_amnt)),20) AS amount,
										SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20) AS sw_nr,
										SUBSTRING(MAX(CONCAT(grant_dte,discountid)),20) AS discountid,
										SUBSTRING(MAX(CONCAT(grant_dte,discount)),20) AS discount,
										SUBSTRING(MAX(CONCAT(grant_dte,notes)),20) AS notes,
										SUBSTRING(MAX(CONCAT(grant_dte,personal_circumstance)),20) AS personal_circumstance,
										SUBSTRING(MAX(CONCAT(grant_dte,community_situation)),20) AS community_situation,
										SUBSTRING(MAX(CONCAT(grant_dte,nature_of_disease)),20) AS nature_of_disease,
										SUBSTRING(MAX(CONCAT(grant_dte,reason)),20) AS reason,
										SUBSTRING(MAX(CONCAT(grant_dte,other_name)),20) AS other_name,
										SUBSTRING(MAX(CONCAT(grant_dte,id_number)),20) AS id_number,
                                        fn_get_personell_firstname_last(SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20)) AS encoder
									FROM seg_charity_grants
									WHERE encounter_nr='$nr' AND status NOT IN ('deleted','cancelled','expired')");

        $res = $sql->queryRow();

        if (!is_null($res['amount']))
            return $res;
        else
            return null;
    }

    public static function getItemIdsByEncrAndType($nr, $category, $pay_type = null, $controlNr = null, $approvedDate = null)
    {

        if (!is_null($pay_type))
            $condition = 'is_deleted = \'0\' AND encounter_nr = "' . $nr . '"' . " AND pay_type='$category' AND entry_type = '$pay_type'";
        else
            $condition = 'is_sdeleted = \'0\' AND encounter_nr = "' . $nr . '"' . " AND pay_type='$category'";

        $criteria = new CDbCriteria();
        $criteria->select = "id, ref_no";
        $criteria->condition = $condition;

        if(trim($controlNr) != ''){
            $criteria->addCondition('control_nr=:controlNr');
            $criteria->params = array_merge($criteria->params,array(':controlNr' => $controlNr));
        }

        if(trim($approvedDate) != ''){
            $criteria->addCondition('approved_date=:approvedDate');
            $criteria->params = array_merge($criteria->params,array(':approvedDate' => $approvedDate));
        }

        $model = self::model()->findAll($criteria);

        if (!$model)
            return array();

        $res = array();
        foreach ($model as $item) {
            $res[] = array('id' => $item->id, 'refno' => $item->ref_no);
        }
        return $res;
    }

    /**
     * This will return ledger details.
     * Search by `encounter_nr` and `bill_nr`
     * @param $params
     * @author michelle 03-05-15
     */
    public function findLedgerDetailsByParams($params)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.bill_nr = ' . $params['billNr']);
        $criteria->addCondition('t.encounter_nr = ' . $params['encounterNr']);

        $model = CreditCollection::model()->findAll($criteria);
        return $model;
    }

    /**
     * This will return undeleted items
     * @param $params
     * @return mixed
     */
    public function findPatientUndeletedItems($nr)
    {
        // set group_concat_max_len in session to 5MB
        $setSessionQuery = "SET SESSION group_concat_max_len = 5242880";
        $command = Yii::app()->db->createCommand($setSessionQuery);
        $command->execute();

        $query = '
            MAX(id) as id, is_deleted, entry_type, SUM(amount) AS amount, SUM(CASE WHEN entry_type = "debit" THEN amount ELSE 0 END) AS debit_amount, SUM(CASE WHEN entry_type = "credit" THEN amount ELSE 0 END) AS credit_amount,
            SUM(CASE WHEN entry_type = "debit" THEN amount ELSE (CASE WHEN entry_type ="credit" THEN -(amount) ELSE 0 END) END) AS total, pay_type,
            GROUP_CONCAT(ref_no SEPARATOR "-") AS ref_no,
            GROUP_CONCAT(
                history ORDER BY create_time DESC SEPARATOR "="
            ) AS remarks,
            control_nr,
            create_time,
            approved_date
        ';

        $criteria = new CDbCriteria();
        $criteria->select = $query;
        $criteria->condition = 'is_deleted = \'0\' AND encounter_nr = "'.$nr.'"';
        $criteria->group = 'pay_type,control_nr,approved_date';
        $criteria->order = 'id DESC';
        $model = self::model()->findAll($criteria);

        return $model;
    }

    /**
     * Return items by `encounter` and `type`
     * @param $nr
     * @param $payType
     * @return array|CActiveRecord|mixed|null
     */
    public function findPatientListItemsByType($nr, $payType)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'SUM(amount) AS amount, id, entry_type, pay_type, encounter_nr, bill_nr, GROUP_CONCAT(ref_no SEPARATOR "-") AS ref_no, GROUP_CONCAT(create_id, create_time SEPARATOR "\n") AS remarks';
        $criteria->condition = 'is_deleted = \'0\' AND encounter_nr = "'.$nr.'"' . ' AND pay_type = "'.$payType.'"';
        $model = self::model()->find($criteria);

        return $model;

    }

    public function generateRefNo($numDigits = 12)
    {
        try {
            $priKey = $this->getTableSchema()->primaryKey;
            // Select MAX(_id) FROM `table` FOR UPDATE
            $sql = $this->getDbConnection()->createCommand("SELECT MAX(CAST({$priKey} AS UNSIGNED)) FROM {$this->tableName()} FOR UPDATE");

            $lastId = 0;                                                // id is equ 0 by default.
            $rawSeriesNum = $sql->queryScalar();                        // execute query

            if ($rawSeriesNum) {                                           // if null lastid will be 0
                // converts the old id 0000000001 to a whole number 1
                $lastId = (strlen($rawSeriesNum) > 4) ? (int) substr($rawSeriesNum, 4) : (int) $rawSeriesNum;
            }

            $series = str_pad(++$lastId, $numDigits, '0', STR_PAD_LEFT);    // returns String in 0000000001 format

        } catch (Exception $e) {                                        // Failed to execute query or the value is null
            throw new Exception('Error in generating or no');
        }

        return $series;                                                 // Returns a String in 20130000000001 format
    }

    /**
     * This will return all `pay_type`
     * @return CActiveRecord[]
     */
    public function getAllPayTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'DISTINCT(pay_type)';

        $model = self::model()->findAll($criteria);
        return $model;
    }

    /**
     * Set different category alias. Use for displaying pay type values
     */
    public static function getCategoryAlias($category, $isOriginalName = false)
    {
        $name = array_search(
           $category,
           array(
             'funding checks' => self::TYPE_FUND_CHECKS,
             'pcso' => self::TYPE_PCSO,
             'dswd' => self::TYPE_DSWD,
             'ss' => self::TYPE_SS,
             'coh-dmh' => self::TYPE_COH,
             'paid' => self::TYPE_PAID,
             'partial' => self::TYPE_PARTIAL,
             'pn' => self::TYPE_PN,
             'map' => self::TYPE_MAP,
             'lingap-emergency' => self::TYPE_LINGAP_EMERGENCY,
             'lingap-recommendation' => self::TYPE_LINGAP_RECOMMENDATION
           )
        );

        if (!$name)
          return strtoupper($category);
        else
          return strtoupper($name);
    }

    /**
     * Get `pay_types` map values to db
     * @param $type
     * @return bool|mixed
     */
    public static function payTypeValues($type)
    {
        $types = array(
          self::TYPE_FUND_CHECKS => 'funding checks',
          self::TYPE_PCSO  => 'pcso',
          self::TYPE_DSWD => 'dswd',
          self::TYPE_SS => 'ss',
          self::TYPE_COH => 'coh-dmh',
          self::TYPE_PAID => 'paid',
          self::TYPE_PN => 'pn',
          self::TYPE_MAP => 'map',
          self::TYPE_LINGAP_EMERGENCY => 'lingap-emergency',
          self::TYPE_LINGAP_RECOMMENDATION => 'lingap-recommendation'
        );
        
        $res = array_search($type, $types);
        if (!$res)
          return $type;
        else
          return $res;
    }
}
