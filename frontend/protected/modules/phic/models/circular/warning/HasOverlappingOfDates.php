<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Encounter;

/**
 * Class HasOverlappingOfDates
 * @package SegHis\modules\phic\models\circular\warning
 * @author Carriane Lastimoso 3-04-2019
 */
class HasOverlappingOfDates extends BaseBillWarning
{

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */
    public function validate(Encounter $encounter, $encounterInsurance, array $accommodations, array $billInfo)
    {
        /* warn if patient has no admitting diagnosis */
        return !(static::hasOverlappingOfDates($encounter->admission_dt,$accommodations, $billInfo['billDate']));

    }

    public function getWarningMessage()
    {
        return 'Accommodation has an overlapping of dates.';
    }

    public static function hasOverlappingOfDates($admission_dt, array $details, $billdate)
    {
        $a = 0;
        
        $has_overlapping = false;
        
        if ($details['deathdate']!=''){
            $tempdte_to = $details['deathdate'];
        }elseif(strcmp($billdate, "0000-00-00 00:00:00") != 0){
            $tempdte_to = $billdate;
        }else{
            $tempdte_to = strftime("%Y-%m-%d %H:%M:%S");
        }

        foreach ($details as $key => $value) {
            $accom_dates = array();
            if(is_numeric($key)){
                
                $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($details[$key]['date_to']));

                if($details[$key]['source'] == 'AD'){
                    if($details[$key]['status'] !='discharged'){
                        $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
                    }
                }

                $details[$key]['date_from'] = strftime("%m/%d/%Y", strtotime($details[$key]['date_from']));

                // gather all dates with accommodation
                $start_from_date = new \DateTime(date("m/d/Y", strtotime($details[$key]['date_from'])));
                $interval_on_date = new \DateInterval('P1D');
                $end_to_date = new \DateTime(date("m/d/Y", strtotime($details[$key]['date_to'])));

                if($details[$key]['date_from'] == $details[$key-1]['date_to'] && ($details[$key]['date_from'] != $details[$key]['date_to'])){
                    $start_from_date = new \DateTime(date("m/d/Y", strtotime($details[$key]['date_from']."+1 day")));
                }

                $end_to_date->setTime(0,0,1);

                $get_range_dates = new \DatePeriod($start_from_date, $interval_on_date, $end_to_date);
                
                $date_from_arr[$key] = $details[$key]['date_from'];
                $date_to_arr[$key] = $details[$key]['date_to'];

                if(($details[$key]['date_from'] == $details[$key]['date_to'])){
                    foreach ($get_range_dates as $key1 => $value1) {
                        $isExistTwice = array_keys($accom_dates, $value1->format('m/d/Y'));
                        if(count($isExistTwice) > 1)
                            $has_overlapping = true;
                        else{
                            $accom_dates[$a] = $value1->format('m/d/Y');
                            $a++;
                        }
                    }
                }else{
                    foreach ($get_range_dates as $key1 => $value1) {
                        if(!in_array($value1->format('m/d/Y'), $accom_dates)){
                            $accom_dates[$a] = $value1->format('m/d/Y');
                            $a++;
                        }else $has_overlapping = true;
                        
                    }
                }
                // end of gathering
            }

        }

        return $has_overlapping;
    }

}