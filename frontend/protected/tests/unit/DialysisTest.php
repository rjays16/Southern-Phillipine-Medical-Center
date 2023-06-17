<?php

use SegHis\modules\dialysis\models\DialysisRequest;

class DialysisTest extends  CDbTestCase {

    public function testStr(){

        /* @var $test DialysisRequest */
        $test = DialysisRequest::model()->findByPk('2015700140');
        echo $test->encounter_nr . "\n";
        var_dump($test->getOldestPhilHealthTransaction()->transaction_date);
        var_dump($test->getLatestPhilHealthTransaction()->transaction_date);
        echo "\n\n";

        $test = DialysisRequest::model()->findByPk('2015700141');
        echo $test->encounter_nr . "\n";
        var_dump($test->getOldestPhilHealthTransaction()->transaction_date);
        var_dump($test->getLatestPhilHealthTransaction()->transaction_date);

    }

}