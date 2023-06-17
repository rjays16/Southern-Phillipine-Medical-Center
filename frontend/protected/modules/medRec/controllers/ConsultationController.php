<?php


use SegHis\modules\medRec\services\ConsultationService;
use SegHis\components\notification\NotificationActiveResource;

class ConsultationController extends Controller
{
    /**
     * @var \SegHis\modules\medRec\services\ConsultationService
     */
    public $service;

    /**
     * ConsultationController constructor.
     *
     * @param \SegHis\modules\medRec\services\ConsultationService $service
     */
    public function __construct()
    {
        $this->service = new ConsultationService();
    }


    public function actionSaveConsultation()
    {
        $request     = $_POST;
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $this->service->saveConsultation($request);
            $transaction->commit();
            echo \CJSON::encode(
                array('success' => true)
            );
        } catch (Exception $e) {
            $transaction->rollBack();
            echo \CJSON::encode(array('errors' => $e->getMessage()));
        }
    }

    public function actionUpdateConsultation($encounter_nr)
    {
        $request     = $_POST;
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $this->service->updateConsultation($encounter_nr,$request);
            $transaction->commit();
            echo \CJSON::encode(
                array('success' => true)
            );
        } catch (Exception $e) {
            $transaction->rollBack();
            echo \CJSON::encode(array('errors' => $e->getMessage()));
        }
    }

    /***
     * Returns count of online consultation requests for the day.
     */
    public function actionConsultMedRecCount()
    {
        $nRequests = $this->service->getConsultCountForMedRec();
        echo CJSON::encode(
            array('count' => $nRequests)
        );        
    }

    /***
     * 
     */
    public function actionIsOnlineConsultMedRec()
    {
        $isWithPermission = $this->service->isOnlineConsultMedRec();
        echo CJSON::encode(
            array('permitted' => $isWithPermission)
        );        
    }

    /***
     * 
     */
    public function actionBlockConsultRegister()
    {
        $consult_id = $_POST['consultId'];

        //
        // Send notification to other MedRec personnel that current consultation request is already being registered.
        //
        $notify = NotificationActiveResource::instance();
        $params = array(
            array(
                'event' => 'BpoTeleconsultRegisterEvent',
                'title' => 'Online Consultation Registration',
                'message' => 'Registration of consult request ongoing!',
                'sender_username' => \Yii::app()->user->getId(),
                'param_data' => array(
                    'consult_id' => $consult_id
                )
            )
        );
        $status = $notify->sendSpmcSocketEvent($params);
        
        echo \CJSON::encode(
            array('success' => $status)
        );        
    }

    /***
     * 
     */
    public function actionSignalDoneConsultRegister()
    {
        $consult_id = $_POST['consultId'];

        //
        // Send notification to other MedRec personnel that registering is done.
        //
        $notify = NotificationActiveResource::instance();
        $params = array(
            array(
                'event' => 'BpoTeleconsultDoneRegisterEvent',
                'title' => 'Done Registration of Consultation Request',
                'message' => 'Registration of consult request done!',
                'sender_username' => \Yii::app()->user->getId(),
                'param_data' => array(
                    'consult_id' => $consult_id
                )                
            )
        );
        $status = $notify->sendSpmcSocketEvent($params);
        
        echo \CJSON::encode(
            array('success' => $status)
        );        
    }    
}
