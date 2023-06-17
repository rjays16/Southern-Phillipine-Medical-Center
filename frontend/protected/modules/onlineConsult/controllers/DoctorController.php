<?php

use SegHis\modules\onlineConsult\services\EhrDoctorService;

class DoctorController extends Controller
{
    /**
     * @return array action filters
     */
    public $defaultController = 'online';

    public $service ;


    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            //'postOnly + delete', // we only allow deletion via POST request
            array('bootstrap.filters.BootstrapFilter'),
        );
    }
    public function __construct()
    { 

       $this->service = new EhrDoctorService();
        
    }

    public function accessRules()
    {
        return array();
    }

    public function actionCreateWebexDoctor()
    {


        $request = $_GET;

        try {
            $this->service->createDoctorWebex($request);
            echo \CJSON::encode(array('success' => true));
        } catch (Exception $e) {
            echo \CJSON::encode(array('errors' => $e->getMessage()));
        }
   }

}
