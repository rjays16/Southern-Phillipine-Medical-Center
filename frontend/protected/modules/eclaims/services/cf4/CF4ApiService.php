<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/22/2019
 * Time: 4:43 AM
 */

namespace SegHis\modules\eclaims\services\cf4;

use EclaimsEncounter;

class CF4ApiService
{

    public $encounter;

    public $data;

    public $ehr;

    public function __construct(EclaimsEncounter $encounter)
    {
        include __DIR__.'/../../../../../..'.'/include/care_api_classes/ehrhisservice/Ehr.php';

        $ehr = \Ehr::instance();
        $this->encounter = $encounter;
//        $this->data = $this->getData();
        $this->ehr = $ehr;
    }

    public function getData()
    {
        $params = array('id' => $this->encounter->encounter_nr);

        return $this->ehr->patient_getpatientdatacf4($params);
    }

    public function getRepetitiveSession()
    {
        $params = array('encounter_nr' => $this->encounter->encounter_nr);

        return $this->ehr->billing_getRepetitivSession($params);
    }
}