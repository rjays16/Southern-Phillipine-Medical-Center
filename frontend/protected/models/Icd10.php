<?php

/**
 * Icd10.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */


/**
 * This is the model class for table "table_name".
 *
 * The followings are the available columns in table 'care_encounter':
 * @property type
 */

class Icd10 extends CareActiveRecord {
    /**
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'care_icd10_en';
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array();
    }

    /**
     *
     * @return array relational rules.
     */
    public function relations() {
        return array(
            //'relation' => array(self::BELONGS_TO, 'RelatedModel', 'primary_key'),
        );
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array();
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $class active record class name.
     * @return Claim the static model class
     */
    public static function model($class=__CLASS__) {
        return parent::model($class);
    }

}

