<?php

# Added by JEFF
// require_once($root_path . 'include/care_api_classes/class_acl.php');

/**
 *
 * MainController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2014, Segworks Technologies Corporation
 */

/**
 * Description of MainController
 *
 * @package
 */

Yii::import('eclaims.models.*');

class MainController extends Controller
{

    /**
     * Added by JEFF 01-12-1
     * For access permission of modules for users passed to view (view/main/index.php) Quick Navigation.
     */
    public $moduleAll;
    public $moduleAllData;
    public $module1Data;
    public $module2Data;
    public $module3Data;

    public $APpv;
    public $APce;
    public $APda;

    public $APpt;
    public $APte;

    public $APcs;
    public $APgv;

    public function init()
    {

    }

    /**
     *
     * @return type
     */
    public function filters()
    {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter'),
        );
    }

    /**
     *
     */
    public function accessRules()
    {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users' => array('?'),
            ),
            array(
                'deny',
                'actions' => array('index'),
                'expression' => '!Yii::app()->user->checkPermission("eclaims")',
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@'),
            ),
        );
    }

    /**
     *
     * @param type $action
     */
    public function beforeAction($action)
    {
        $this->breadcrumbs[] = 'Home';
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionIndex()
    {
        Yii::import('eclaims.models.EclaimsTransmittal');
        Yii::import('eclaims.models.Claim');
        $status = EclaimsTransmittal::countTransmittalsByStatus();
        $this->render('index', array(
            'transmittalStatuses' => $status));
    }

    public function actionUsermanual()
    {
        $file = dirname(dirname(__FILE__));
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=filename.pdf");
        @readfile($file . '/documents/Eclaims-User-Manual.pdf');
    }

}
