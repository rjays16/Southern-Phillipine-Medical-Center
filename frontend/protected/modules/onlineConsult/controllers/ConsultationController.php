<?php


use SegHis\modules\onlineConsult\services\ConsultationService;
use SegHis\components\notification\NotificationActiveResource;
use SegHis\modules\onlineConsult\services\NotificationService;
use ConsultRequest;
use User;

class ConsultationController extends Controller
{
    /**
     * @var \SegHis\modules\onlineConsult\services\ConsultationService
     */
    public $service;

    /**
     * ConsultationController constructor.
     *
     * @param \SegHis\modules\onlineConsult\services\ConsultationService $service
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
    public function actionConsultRequestCount()
    {
        $nRequests = $this->service->getConsultRequestCount();
        echo CJSON::encode(
            array('count' => $nRequests)
        );        
    }

    /***
     * 
     */
    public function actionIsOnlineConsultTriage()
    {
        $isWithPermission = $this->service->isOnlineConsultTriage();
        echo CJSON::encode(
            array('permitted' => $isWithPermission)
        );        
    }

    /***
     * 
     */
    public function actionBlockConsultRequest()
    {
        $consult_id = $_POST['consultId'];

        //
        // Send notification to other triage personnel that current consultation request is already being triaged.
        //
        $notify = NotificationActiveResource::instance();
        $params = array(
            array(
                'event' => 'BpoTriageConsultEvent',
                'title' => 'Online Consultation Triage',
                'message' => 'Triage of consult request ongoing!',
                'sender_username' => Yii::app()->user->getId(),
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
     *  
     */    
    public function actionNotifyTriageStarted()
    {
        $consult_id = $_POST['consultId'];
        $consultRequest = ConsultRequest::model()->findByPk($consult_id);
        $user = User::model()->findByPk(Yii::app()->user->getId());

        $notification = new NotificationService();
        $json                  = array();
        $json['user_username'] = Yii::app()->user->getId();
        $json['fb_userid']     = $user->personnel->fb_userid;
        $payload               = array(
            'consult_id' => $consult_id,
            'sender'     => $consultRequest->onesignal_player_id,
            'title'      => "Online Consultation Request",
            'message'    => ($consultRequest->name_first.' '.$consultRequest->name_last).", SPMC's triage personnel may need to chat with you.  Click on the button below to chat using FB Messenger.",
            'users'      => CJSON::encode(array($consultRequest->onesignal_player_id)),
            'fb_userid'  => $user->personnel->fb_userid,
            'param_data' => CJSON::encode($json)
        );

        $notification->sendNotification('/telemed/triage/chat', $payload);        
    }

    /***
     * 
     */
    public function actionSignalDoneConsultRequest()
    {
        $consult_id = $_POST['consultId'];

        //
        // Send notification to other triage personnel that triaging is done. (Either referred to MedRec or cancelled.)
        //
        $notify = NotificationActiveResource::instance();
        $params = array(
            array(
                'event' => 'BpoDoneTriageConsultEvent',
                'title' => 'Done Triaging of Consultation Request',
                'message' => 'Triage of consult request done!',
                'sender_username' => Yii::app()->user->getId(),
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
    public function actionNotifyConsultMedRec()
    {
        $consult_id = $_POST['consultId'];

        //
        // Send notification to medical records of new consultation request for processing.
        //
        $notify = NotificationActiveResource::instance();
        $params = array(
            array(
                'event' => 'BpoNewTeleconsultMedRecEvent',
                'title' => 'Registration of Consultation Request',
                'message' => 'New consultation request for processing at Medical Records',
                'sender_username' => Yii::app()->user->getId(),
                'param_data' => array(
                    'consult_id' => $consult_id
                ),
                'url' => Yii::app()->request->hostInfo . Yii::app()->request->baseUrl . "/index.php?r=medRec/online/"
            )
        );
        $status = $notify->sendSpmcSocketEvent($params);
        
        echo \CJSON::encode(
            array('success' => $status)
        );         
    }
}
