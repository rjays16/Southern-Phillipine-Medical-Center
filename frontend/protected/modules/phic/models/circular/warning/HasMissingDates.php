<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Encounter;

/**
 * Class HasMissingDates
 * @package SegHis\modules\phic\models\circular\warning
 * @author Carriane Lastimoso 3-04-2019
 */

class HasMissingDates extends BaseBillWarning
{

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */

    public function validate(Encounter $encounter, $encounterInsurance, array $accommodations, array $billInfo)
    {
        /* warn if patient has missing dates in accommodation */
       return !(static::hasMissingDatesInAccomm($encounter->admission_dt,$accommodations,$billInfo['billDate']));

    }

    public function getWarningMessage()
    {
        return 'Accommodation has lacking of dates.';
    }

    public static function hasMissingDatesInAccomm($admission_dt, array $details, $billdate)
    {

        $has_lacking_dates = false;
        $missing_dates = 0;

        $str_date_from = strtotime($details[0]['date_from']);
        $str_date_to = strtotime($details[0]['date_to']);
        $a =0;
        $dates = array();

        if ($details['deathdate']!=''){
            $tempdte_to = $details['deathdate'];
        }elseif(strcmp($billdate, "0000-00-00 00:00:00") != 0){
            $tempdte_to = $billdate;
        }else{
            $tempdte_to = strftime("%Y-%m-%d %H:%M:%S");
        }
        
        if(count($details[0]) != 0 && $details[0] != NULL){
            foreach ($details as $key => $value) {
                if(is_numeric($key)){
                    if($details[$key]['date_to'] == '0000-00-00'){
                        $details[$key]['date_to'] = strftime("%m/%d/%Y");
                    }else
                        $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($details[$key]['date_to']));

                    if($details[$key]['source'] == 'AD'){
                        if($details[$key]['status'] !='discharged'){
                            $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
                        }else{
                            if(strtotime($details[$key]['date_to']) > strtotime($tempdte_to)){
                                $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
                            }
                        }
                    }else{
                        if(strtotime($details[$key]['date_to']) > strtotime($tempdte_to)){
                            $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
                        }
                    }

                    $details[$key]['date_from'] = strftime("%m/%d/%Y", strtotime($details[$key]['date_from']));

                    if(strtotime($details[$key]['date_from']) == strtotime($details[$key-1]['date_to']))
                        $str_date_from = strtotime($details[$key]['date_from']."+1 day");
                    else
                        $str_date_from = strtotime($details[$key]['date_from']);

                    $str_date_to = strtotime($details[$key]['date_to']);

                    for ($i=$str_date_from; $i<=$str_date_to; $i+=86400) {  
                        $dates[$a] = date("m/d/Y", $i);  
                        $a++;
                    }
                    if($details[$key]['date_from'] != $details[$key-1]['date_to'] && ($details[$key-1]['date_to'] != NULL))
                        $missing_dates = 1;
                }
            }
        }else{
            $missing_dates = 1;
        } 

        if(!$missing_dates){
            $start_date = new \DateTime(date("m/d/Y", strtotime($admission_dt)));
            $interval = new \DateInterval('P1D');
            $end_date = new \DateTime(date("m/d/Y", strtotime($tempdte_to)));

            $end_date->setTime(0,0,1);

            $period = new \DatePeriod($start_date, $interval, $end_date);

            $a = 0;
            foreach ($period as $key => $value) {
                $dates2[$a] = $value->format('m/d/Y');
                $a++;
            }

            $missing_dates = count(array_diff($dates2, $dates));
            
            
        }/*else{
            $lack_of_date = 1;
        }*/
        
        if($missing_dates) $has_lacking_dates = true;

        return $has_lacking_dates;
    }

}