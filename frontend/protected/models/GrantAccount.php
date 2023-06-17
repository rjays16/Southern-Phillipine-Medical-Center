<?php

/**
 * This is the model class for table "seg_grant_accounts"
 *
 * @author MM
 */

class GrantAccount extends CareActiveRecord
{
    public $id;
    public $name;
    public $title;
    public $address;
    public $locked;
    public $account_type_id;
    public $priority;
    public $deleted;

    /**
     * @param string $className active record class name.
     * @return SegGrantAccounts the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_grant_accounts';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, account_type_id', 'required','on' => 'update'),
            array('name, title', 'length', 'max'=>30),
            array('name, title', 'required'),
            array('address, priority, locked, deleted', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'accountTypes' => array(self::BELONGS_TO, 'GrantAccountType', 'account_type_id'),
            'MssEntryGrant' => array(self::HAS_MANY, 'MssEntryGrant', 'account_id'),
        );
    }

    /**
     * {@inheritDoc}
     * @return array
     */
    public function scopes()
    {
        return array(
            'isNonDeleted' => array(
                'condition' => 'deleted = 0'
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'account_type_id' => 'Account Type',
            'priority' => 'Priority',
            'name' => 'Name',
            'title' => 'Title',
            'address' => 'Address',
            'locked' => 'Locked',
            'deleted' => 'Deleted',
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
        $criteria=new CDbCriteria;
        $criteria->addColumnCondition(array('deleted' => 0));
        //$criteria->compare('t.name', $this->name, true);
        if (isset($this->name)) {
            $criteria->addSearchCondition('name', $this->name.'%', true);
        }
        if (isset($this->id)) {
            $criteria->addSearchCondition('id', $this->id.'%', true);
        }

        $dp = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));

        return $dp;
    }

    /**
     * Returns `GrantAccount` model object search by `id`
     *
     * @param GrantAccount $account
     * @return null
     */
    public function getGrantsById($account)
    {
        $model = self::model()->findAllByAttributes(array(
            'id' => $account->id
        ));

        if (!$model)
            return null;

        return $model;
    }

    /**
     * @param $type
     */
    public function findByPaytype($type)
    {
      $criteria = new CDbCriteria;
      $criteria->addCondition("t.account_type_id = '$type' AND t.deleted = 0");

      $model = self::model()->findAll($criteria);

      if (!$model)
          return false;
      return $model;
    }

    public function getAllGrantAccount(){
        $criteria = new CDbCriteria();

        $criteria->addCondition('deleted <> 1');
        $criteria->order = "name ASC";
        $criteria->params = array('name' => trim($name).'%');

        return $this->findAll($criteria);
    }
}