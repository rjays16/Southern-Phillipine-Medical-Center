<?php


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