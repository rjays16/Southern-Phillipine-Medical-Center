<?php


namespace SegHis\modules\medRec\services;


class EhrService
{
    public function __construct()
    {
        include getcwd().'/include/care_api_classes/ehrhisservice/Ehr.php';
        $ehr       = \Ehr::instance();
        $this->ehr = $ehr;
    }

    public function postWebex($params)
    {
        $data = $this->ehr->postWebEx($params);
        return $data;
    }
}
