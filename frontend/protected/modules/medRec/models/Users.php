<?php

/**
 * This is the model class for table "care_users".
 *
 * The followings are the available columns in table 'care_users':
 * @property string $name
 * @property string $login_id
 * @property string $password
 * @property string $email_address
 * @property string $personell_nr
 * @property integer $lockflag
 * @property string $permission
 * @property integer $exc
 * @property string $s_date
 * @property string $s_time
 * @property string $expire_date
 * @property string $expire_time
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $lock_duration
 * @property string $old_name
 */
class Users extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('permission, history, modify_time', 'required'),
            array('lockflag, exc', 'numerical', 'integerOnly' => true),
            array('name, old_name', 'length', 'max' => 60),
            array('login_id, modify_id, create_id', 'length', 'max' => 35),
            array('password', 'length', 'max' => 255),
            array('email_address', 'length', 'max' => 200),
            array('personell_nr', 'length', 'max' => 10),
            array('status', 'length', 'max' => 15),
            array('lock_duration', 'length', 'max' => 30),
            array('s_date, s_time, expire_date, expire_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('name, login_id, password, email_address, personell_nr, lockflag, permission, exc, s_date, s_time, expire_date, expire_time, status, history, modify_id, modify_time, create_id, create_time, lock_duration, old_name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name'          => 'Name',
            'login_id'      => 'Login',
            'password'      => 'Password',
            'email_address' => 'Email Address',
            'personell_nr'  => 'Personell Nr',
            'lockflag'      => 'Lockflag',
            'permission'    => 'Permission',
            'exc'           => 'Exc',
            's_date'        => 'S Date',
            's_time'        => 'S Time',
            'expire_date'   => 'Expire Date',
            'expire_time'   => 'Expire Time',
            'status'        => 'Status',
            'history'       => 'History',
            'modify_id'     => 'Modify',
            'modify_time'   => 'Modify Time',
            'create_id'     => 'Create',
            'create_time'   => 'Create Time',
            'lock_duration' => 'Lock Duration',
            'old_name'      => 'Old Name',
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

        $criteria->compare('name', $this->name, true);
        $criteria->compare('login_id', $this->login_id, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('email_address', $this->email_address, true);
        $criteria->compare('personell_nr', $this->personell_nr, true);
        $criteria->compare('lockflag', $this->lockflag);
        $criteria->compare('permission', $this->permission, true);
        $criteria->compare('exc', $this->exc);
        $criteria->compare('s_date', $this->s_date, true);
        $criteria->compare('s_time', $this->s_time, true);
        $criteria->compare('expire_date', $this->expire_date, true);
        $criteria->compare('expire_time', $this->expire_time, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_time', $this->modify_time, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('lock_duration', $this->lock_duration, true);
        $criteria->compare('old_name', $this->old_name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Users the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
