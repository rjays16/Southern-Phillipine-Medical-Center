<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com> and Mary Joy L. Abuyo <marjylabuyo@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.ClaimPayee');
Yii::import('eclaims.models.ClaimStatus');

class ClaimStatusController extends Controller
{

    public function filters()
    {
        return array(
            'accessControl',
            array(
                'bootstrap.filters.BootstrapFilter',
            ),
        );
    }

    /**
     *
     * @return type
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
                'expression' => '!Yii::app()->user->checkPermission("eclaims")',
            ),
            array(
                'allow',
                'actions' => array('*'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * [beforeAction description]
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function beforeAction($action)
    {

//        if (Yii::app()->request->isAjaxRequest) {
//            Yii::app()->clientScript->scriptMap['jquery.js'] = false;
//            Yii::app()->clientScript->scriptMap['jquery.livequery.js'] = false;
//            Yii::app()->clientScript->scriptMap['utils.js'] = false;
//            Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
//            Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
//            Yii::app()->clientScript->scriptMap['notify.min.js'] = false;
//            Yii::app()->clientScript->scriptMap['pharmacy.js'] = false;
//            Yii::app()->clientScript->scriptMap['searchForm-submit'] = false;
//            Yii::app()->clientScript->scriptMap['create_request.js'] = false;
//            Yii::app()->clientScript->scriptMap['jquery.slimscroll.min.js'] = false;
//            Yii::app()->clientScript->scriptMap['modal.js'] = false;
//            Yii::app()->clientScript->scriptMap['clonable.js'] = false;
//        }

        $this->breadcrumbs['Claim Status'] = array(
            'claimStatus/index',
        );

        return parent::beforeAction($action);
    }

    /**
     * Renders Check Status main page
     * @var object claim
     *
     */
    public function actionIndex()
    {
        Yii::import('eclaims.models.Claim');
        Yii::import('eclaims.models.ClaimStatusCatalog');
        $claim = new Claim('search');
        $model = ClaimStatusCatalog::model()->getAll();
        $claim->unsetAttributes();
        /*if (isset($_GET['Claim'])) { #modified
            $claim->attributes = $_GET['Claim'];
        }*/
        $claim->setAttributes($_GET['Claim']); #added
        $this->render('index', array(
            'claim' => $claim,
            'model' => $model
        ));
    }

    #added for new search format
    public function actionSearchNew()
    {
        if ($_REQUEST) {
            Yii::import('eclaims.models.Claim');
            $claim = new Claim('searchNews');
            $claim->setAttributes($_GET['Claim']);
            $this->render('index', array(
                'searchData' => $claim,
            ));

        }
    }

    #end

    public function actionViewStatusModal()
    {
        Yii::import('eclaims.models.Claim');
        Yii::import('eclaims.services.ServiceExecutor');

        $claim = Claim::model()->findByPk($_POST['id']);

        $data = $claim->compactClaimStatus();

        $service = new ServiceExecutor(array(
            'endpoint' => 'hie/claim/status',
            'params' => $data,
        ));

        try {
            $result = $service->execute();
            ClaimStatus::extractResult($result['data']);
            $claim = Claim::model()->findByPk($_POST['id']);
            $message = "Claim Status Successfully Updated";
            $success = true;
        } catch (ServiceCallException $e) {
            $result = $e->getData();
            $success = false;
            $message = $e->getMessage();
        }

        $form = $this->renderPartial(
            'modal/view', array(
            'claim' => $claim,
            'success' => $success,
            'message' => $message,
        ), true);


        echo CJSON::encode(
            array(
                'form' => $form,
            )
        );

    }


    /**
     * Renders page displaying the claim status and its corresponding details
     *
     */
    public function actionViewStatus()
    {

        $claimId = $_GET['claim_id'];
        Yii::import('eclaims.models.Claim');
        Yii::import('eclaims.services.ServiceExecutor');

        $claim = Claim::model()->findByPk($claimId);

        // var_dump($claim->status);die($claimId);

        #updated by monmon : enable auto-update
        // if (empty($claim->status) || ($_GET['update_status'] == 1)) {

        if ($_GET['update_status'] == 1) {
            if (!trim($claim->claim_series_lhio)) {
                Yii::app()->user->setFlash('error',
                    '<b>Error!</b> The claim is still not mapped to a PHIC claims series number');

                #$this->redirect(array('index'));

                #addded for searching
                if (!empty($_REQUEST['searchin'])) {
                    $this->redirect(array('index&search=true&encounter_nr_new_data=' . $claim->encounter_nr), true);
                } else {

                    $this->redirect(array('index'));
                }
                #end
            }

            $data = $claim->compactClaimStatus();

            // CVarDumper::dump($data, 10, true); die;

            $service = new ServiceExecutor(array(
                'endpoint' => 'hie/claim/status',
                'params' => $data,
            ));
            try {
                $result = $service->execute();


                ClaimStatus::extractResult($result['data']);
                $claim = Claim::model()->findByPk($claimId);
                // $this->redirect(array('viewStatus', 'claim_id' => $claimId));
                Yii::app()->user->setFlash('info', '<b>Success!</b> Claim status successfully updated!');

            } catch (ServiceCallException $e) {
                $result = $e->getData();
                Yii::app()->user->setFlash('error',
                    '<strong>' . $e->getMessage() . ':</strong> ' . @$result['reason'] . ' <small> (Code:' . $e->statusCode . ')</small>');
            }
        }

        $this->render('viewStatus', array(
            'claim' => $claim,
        ));
    }

    /**
     *
     * @param int $id Claim's primary key (ID)
     */
    public function actionSelect($id)
    {
        $claim = $this->loadModel($id);
        if (isset($_POST['Claim'])) {
            $claim->attributes = $_POST['Claim'];
            if ($claim->save()) {
                $this->redirect(array(
                    'view',
                    'id' => $claim->id,
                ));
            }
        }
        $this->render('select', array(
            'claim' => $claim,
        ));
    }
}
