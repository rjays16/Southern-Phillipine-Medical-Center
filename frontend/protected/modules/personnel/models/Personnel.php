<?php
namespace SegHis\modules\personnel\models;
/**
 * This is the model class for table "care_personell".
 *
 * The followings are the available columns in table 'care_personell':
 * @property integer $nr
 * @property string $short_id
 * @property string $pid
 * @property integer $ris_id
 * @property integer $job_type_nr
 * @property string $job_function_title
 * @property string $job_position
 * @property string $category
 * @property string $job_status
 * @property string $date_join
 * @property string $date_exit
 * @property string $contract_class
 * @property string $contract_start
 * @property string $contract_end
 * @property integer $is_discharged
 * @property string $pay_class
 * @property string $pay_class_sub
 * @property string $local_premium_id
 * @property string $tax_account_nr
 * @property string $ir_code
 * @property integer $nr_workday
 * @property double $nr_weekhour
 * @property integer $nr_vacation_day
 * @property integer $multiple_employer
 * @property integer $nr_dependent
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $license_nr
 * @property string $prescription_license_nr
 * @property string $tin
 * @property integer $is_resident_dr
 * @property string $tier_nr
 * @property string $id_nr
 * @property string $other_title
 * @property integer $ward_nr
 * @property integer $is_reliever
 * @property string $newpid
 * @property integer $is_housecase_attdr
 * @property integer $is_housecase_surgeon
 * @property integer $is_housecase_anesth
 * @property string $ptr_nr
 * @property string $s2_nr
 * @property string $doctor_role
 * @property string $doctor_level
 * @property string $signature_filename
 *
 * @property string $departmentName
 */
class Personnel extends \Personnel
{
    public static $inActiveStatusCodes = array(
        'deleted',
        'hidden',
        'inactive',
        'void'
    );

    public function relations()
    {
        return \CMap::mergeArray(parent::relations(), array(
            'dependents' => array(self::HAS_MANY, 'PersonnelDependent', array('parent_pid' => 'pid'))
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Personnel the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $pid
     * @return Personnel|null
     */
    public static function findActivePersonnelByPid($pid)
    {
        $personnel = self::findPersonnelByPid($pid);
        if($personnel){
            if(!$personnel->isActive())
                return null;
        }
        return $personnel;
    }

    /**
     * @param $pid
     * @return null|Personnel
     */
    public static function findPersonnelByPid($pid)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'pid' => $pid,
        ));
        return Personnel::model()->find($criteria);
    }

    public function isActive()
    {
        return !in_array($this->status,static::$inActiveStatusCodes);
    }

}