<?php

/**
 * This is the model class for table "seg_phil_frequency".
 *
 * The followings are the available columns in table 'seg_phil_frequency':
 * @property string $frequency_code
 * @property string $frequency_disc

 */
class PharmacyFrequency extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_phil_frequency';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PharmacyFrequency the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getFrequencyList(){
        $criteria = new CDbCriteria();

        $model =  $this->findAll();
        return $model;
    }

}