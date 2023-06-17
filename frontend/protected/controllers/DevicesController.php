<?php

/**
 * DeviceController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 */

/**
 * Description of DeviceController
 */

class DevicesController extends Controller
{

    public $layout = false;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     *
     */
    public function actionCamera()
    {
        $this->render('camera');
    }

}