 <?php

/**
 * This is the model class for table "seg_grant_accounts_allotment".
 *
 * The followings are the available columns in table 'seg_grant_accounts_allotment':
 * @property integer $id
 * @property string $grant_account
 * @property string $grant_account_type
 * @property string $date
 * @property string $amount
 * @property string $remarks
 * @property string $history
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property SegGrantAccountType $grantAccountType
 * @property SegGrantAccounts $grantAccount
 */
class GrantAccountsAllotment extends CActiveRecord
{
    public $account_fund;
    public $amount;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_grant_accounts_allotment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('grant_account, grant_account_type', 'length', 'max'=>10),
            array('amount', 'length', 'max'=>18),
            array('create_id, modify_id', 'length', 'max'=>35),
            array('date, remarks, history, create_time, modify_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, grant_account, grant_account_type, date, amount, remarks, history, create_id, create_time, modify_id, modify_time, is_deleted', 'safe', 'on'=>'search'),
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
            'grantAccountType' => array(self::BELONGS_TO, 'SegGrantAccountType', 'grant_account_type'),
            'grantAccount' => array(self::BELONGS_TO, 'SegGrantAccounts', 'grant_account'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'grant_account' => 'Grant Account',
            'grant_account_type' => 'Grant Account Type',
            'date' => 'Date',
            'amount' => 'Amount',
            'remarks' => 'Remarks',
            'history' => 'History',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('grant_account',$this->grant_account,true);
        $criteria->compare('grant_account_type',$this->grant_account_type,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('remarks',$this->remarks,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GrantAccountsAllotment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function scopes() {
        return array(
            'bydate' => array('order' => 'date DESC'),
        );
    }


    public function getAllAllotment(){
        $criteria = new CDbCriteria();
        $criteria->select = array("t.*", "FORMAT(amount, 2) AS amount");
        $criteria->condition = "is_deleted <> 1 AND id IS NULL";
        $criteria->order = "date DESC";
        $model =  $this->findAll($criteria);
        return $model;
    }

    public function getAccountFunds($type_id, $id=''){
        $criteria = new CDbCriteria();

        if($id){
            $condition = "grant_account_type = ".$type_id." AND grant_account = ".$id." AND is_deleted <> 1";
        }else{
            if($type_id != ''){
                $condition = "grant_account_type = ".$type_id." AND (grant_account IS NULL OR grant_account = '') AND is_deleted <> 1";
            }else{
                $condition = "grant_account_type IS NULL AND (grant_account IS NULL OR grant_account = '') AND is_deleted <> 1";
            }
        }

        $criteria->select = "SUM(amount) as account_fund";
        $criteria->condition = $condition;
        $criteria->order = "date DESC";
        $model =  $this->findAll($criteria);
        return $model;
    }
} 