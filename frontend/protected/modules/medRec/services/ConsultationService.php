<?php


namespace SegHis\modules\medRec\services;

use CarePerson;
use ConsultMeeting;
use ConsultRequest;
use DoctorMeeting;
use Encounter;
use EncounterTracker;
use SegHis\modules\eclaims\models\EclaimsPharmaOrderItems;
use SegHis\components\notification\NotificationActiveResource;

class ConsultationService
{

    const TRIAGED = 'TRIAGED';

    public $ehrDb;

    public function __construct()
    {
        $this->ehrDb = \Yii::app()->getComponent('ehrDb');
    }

    public function saveConsultation($request)
    {
        $pending = $this->checkHasPendingConsultation($request['pid'], $request['consult_dept']);

        if ($pending) {
            throw new \Exception('You have an active consultation request!');
        }

        if (empty($request['consultationDateTime']) ||
            empty($request['official_receipt_nr']) ||
            empty($request['consult_dept']) 
        ) {
            throw new \Exception('Please fill in required fields');
        }

       

        $consultRequest = ConsultRequest::model()->findByPk($request['consultId']);

        $tracker = EncounterTracker::model()->findByAttributes(array(
            'triage' => 'opd'
        ));

        $tracker->last_encounter_nr = $tracker->last_encounter_nr + 1;

        $doctorMeeting = DoctorMeeting::model()->findByAttributes(array(
            'doctor_id' => $request['consult_dr_nr']
        ));

        $department = \Department::model()->findbyAttributes(
            array('nr' => $request['consult_dept'])
        );

        $meeting = array(
            'site_name' => $doctorMeeting->site_name,
            'webex_id'  => $doctorMeeting->webex_id,
            'password'  => $doctorMeeting->password
        );


        if (!$tracker->save()) {
            throw new \Exception('Unable to Save Encounter tracker');
        }

        $consult_req = ConsultRequest::model()->findByAttributes(array(
            'consult_id' => $request['consultId']
        ));

        $consult_req->is_allowed_cancel = 1;
      
        if (!$consult_req->save()) {
            throw new \Exception('Unable to Save Encounter Request');
        }

        $model                      = new Encounter();
        $model->encounter_nr        = $tracker->last_encounter_nr;
        $model->encounter_type      = 2;
        $model->encounter_class_nr  = 2;
        $model->pid                 = $request['pid'];
        $model->chief_complaint     = $request['chief_complaint'];
        $model->encounter_date      = date("Y-m-d H:i:s", strtotime($request['consultationDateTime']));
        $model->is_confidential     = $request['Encounter']['is_confidential'];
        $model->official_receipt_nr = 12;
        $model->smoker_history      = $request['Encounter']['smoker_history'];
        $model->drinker_history     = $request['Encounter']['drinker_history'];
        $model->modify_time         = date('Y-m-d H:i:s');
        $model->current_dept_nr     = $request['consult_dept'];
        $model->consulting_dr_nr    = $request['consult_dr_nr'];
        $model->consulting_dept_nr  = $request['consult_dept'];
        $model->history             = "Created " . date('Y-m-d H:i:s') . " " .
            $_SESSION['sess_user_name'] . " thru online consultation\n";

        if (!$model->save()) {
            throw new \Exception('Unable to Save Encounter');
        }

        $ehrApi = new EhrService();
        $url    = $ehrApi->postWebex($meeting);
     
        $meeting               = new ConsultMeeting();
        $meeting->id           = md5(date('Y-m-d H:i:s'));
        $meeting->encounter_nr = $model->encounter_nr;
        $meeting->consult_id   = $request['consultId'];
        $meeting->doctor_id    = $request['consult_dr_nr'];
        $meeting->is_valid     = 1;
        $meeting->create_dt    = date('Y-m-d H:i:s');
        $meeting->create_id    = $_SESSION['sess_user_name'];
        $meeting->modify_dt    = date('Y-m-d H:i');
        $meeting->modify_id    = $_SESSION['sess_user_name'];
        $meeting->status       = ConsultMeeting::STATUS_PENDING;
        $meeting->meeting_id   = $url->data->meeting_id;
        $meeting->meeting_url  = $url->data->meeting_url;
        $meeting->history      = "Created " . date('Y-m-d H:i:s') . " " .
            $_SESSION['sess_user_name']."\n";


        $ehrService = new EhrPatientService($model, $model->person);
        $ehrService->creatEhrPatient();
        if( $request['consult_dr_nr']){
        $user = \Users::model()->findByAttributes(
            array(
                'personell_nr' => $request['consult_dr_nr']
            )
        );
        }
        $param                  = array();
        $param["token"]         = $consultRequest->access_token;
        $param["doctor_name"]   = $user->name;
        $param["doctor_id"]     = $meeting->doctor_id;
        $param["user_username"] = $user->login_id;
        $param["encounter_no"]  = $model->encounter_nr;


        $notification = new NotificationService();

        $criteria         = new \CDbCriteria();
        $criteria->params = array(
            ':now'    => date("Y-m-d"),
            ':status' => ConsultMeeting::STATUS_CONFIRMED,
            ':doctor' => $request['consult_dr_nr']
        );

        $criteria->condition = 't.status = :status AND DATE(t.create_dt) = :now AND t.doctor_id = :doctor';
        $data                = ConsultMeeting::model()->findAll($criteria);
        $person = CarePerson::model()->findByPk($model->pid);

        $payload = array(
            'consult_id' => $request['consultId'],
            'sender'     => $consultRequest->onesignal_player_id,
            'title'      => 'SPMC Online Consultation',
            'message'    => ($person->name_first.' '.$person->name_last).", Appointment " .  ($user->name ? " with Dr. " .$user->name : " in department" ) . " of  " . $department->name_formal .
                " has been set. Please wait for your schedule. We will send you your room address later.",
            'users'      => \CJSON::encode(array($consultRequest->onesignal_player_id)),
            'param_data' => \CJSON::encode($param)
        );
        $notification->sendNotification('/notification/telemed/patient/assigned/doctor', $payload);

        if (count($data) < ConsultMeeting::CONSULTATION_LIMIT) {
            $json                  = array();
            $json['user_username'] = $user->login_id;
            $json['encounter_no']  = $meeting->encounter_nr;
            $json['doc_name']      = $user->name;
            $json['dept_name']     = $department->name_formal;
            $payload               = array(
                'consult_id' => $request['consultId'],
                'sender'     => $consultRequest->onesignal_player_id,
                'title'      => "SPMC Online Consultation",
                'message'    => ($person->name_first.' '.$person->name_last).", Do you want to proceed with your appointment " .($user->name ? " with Dr. " .$user->name : " in department" ). " of ". $department->name_formal,
                'users'      => \CJSON::encode(array($consultRequest->onesignal_player_id)),
                'param_data' => \CJSON::encode($json)
            );
            $meeting->conf_notif_sent = ConsultMeeting::CONSULTATION_SENT;
            $notification->sendNotification('/notification/telemed/confirm/patient', $payload);
        }
        
        if (!$meeting->save()) {
            throw new \Exception('Unable to Save Meeting');
        }

    }

    public function checkHasPendingConsultation($pid, $department)
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_consult_meeting t');
        $command->leftJoin('care_encounter enc', 't.encounter_nr=enc.encounter_nr');
        $command->where("enc.pid=:pid AND enc.current_dept_nr = :department AND DATE(t.create_dt) = :now AND t.status = :pending");
        $command->params = array(
            ':pid'        => $pid,
            ':now'        => date("Y-m-d"),
            ':department' => $department,
            ':pending'    => ConsultMeeting::STATUS_PENDING
        );
        return $command->queryRow($command);
    }

    public function checkConsultMeetingStatus($encounter_nr){
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_consult_meeting t');
        $command->where("t.encounter_nr=:encounter_nr AND (t.status = :done OR t.status = :confirmed)");

        $command->params = array(
            ':encounter_nr'     => $encounter_nr,
            ':done'             => ConsultMeeting::STATUS_DONE,
            ':confirmed'        => ConsultMeeting::STATUS_CONFIRMED
        );

        return $command->queryRow($command);
    }

    public function updateConsultation($encounter_nr,$request)
    {

        $drupdate = 0;

        if (empty($request['official_receipt_nr']) ||
            empty($request['consult_dept'])
            
        ) {
            throw new \Exception('Please fill in required fields');
        }

       
        $model                      = Encounter::model()->findByPk($encounter_nr);

        if($request['consult_dept'] != $model->consulting_dept_nr){
            $pending = $this->checkHasPendingConsultation($request['pid'], $request['consult_dept']);

            if ($pending) {
                throw new \Exception('You have an active consultation request!');
            }
        }

        if(($request['consult_dept'] != $model->consulting_dept_nr && $request['consult_dr_nr'] != $model->consulting_dr_nr) || ($request['consult_dr_nr'] != $model->consulting_dr_nr ) 
            || ($request['consult_dept'] != $model->consulting_dept_nr && $request['consult_dr_nr'] == $model->consulting_dr_nr)){

            $done = $this->checkConsultMeetingStatus($encounter_nr);

            if($done){
                throw new \Exception("Unable to update department and consulting physician. Consultation was either confirmed/done.");
            }else
                $drupdate = 1; 
        }

        $model->chief_complaint     = $request['chief_complaint'];
        $model->is_confidential     = $request['Encounter']['is_confidential'];
        $model->smoker_history      = $request['Encounter']['smoker_history'];
        $model->drinker_history     = $request['Encounter']['drinker_history'];
        $model->current_dept_nr     = $request['consult_dept'];
        $model->consulting_dr_nr    = $request['consult_dr_nr'];
        $model->consulting_dept_nr  = $request['consult_dept'];
        $model->modify_time         = date('Y-m-d H:i:s');
        $model->modify_id           = $_SESSION['sess_user_name'];
        $model->history             = $model->history."Updated " . date('Y-m-d H:i:s') . " " .
            $_SESSION['sess_user_name'] . " thru online consultation\n";

        if (!$model->save()) {
            throw new \Exception('Unable to update consultation details');
        }

        $ehrService = new EhrPatientService($model, $model->person);
        $ehrService->creatEhrPatient();

        if($drupdate) {
            $doctorMeeting = DoctorMeeting::model()->findByAttributes(array(
                'doctor_id' => $request['consult_dr_nr']
            ));

            $meeting = array(
                'site_name' => $doctorMeeting->site_name,
                'webex_id'  => $doctorMeeting->webex_id,
                'password'  => $doctorMeeting->password
            );

            $ehrApi = new EhrService();
            $url    = $ehrApi->postWebex($meeting);

            $meeting = ConsultMeeting::model()->findByAttributes(
                                        array('encounter_nr' => $encounter_nr)
                                    );

            $history = "Updated doctor_id from ". $meeting->doctor_id." to ".$request['consult_dr_nr']." ". date('Y-m-d H:i:s') . " " .
                $_SESSION['sess_user_name']."\n";

            $meeting->doctor_id    = $request['consult_dr_nr'];
            $meeting->modify_dt    = date('Y-m-d H:i');
            $meeting->modify_id    = $_SESSION['sess_user_name'];
            $meeting->status       = ConsultMeeting::STATUS_PENDING;
            $meeting->meeting_id   = $url->data->meeting_id;
            $meeting->meeting_url  = $url->data->meeting_url;
            $meeting->history      = $meeting->history.$history;

            $consultRequest = ConsultRequest::model()->findByPk($request['consultId']);
             if($request['consult_dr_nr']) {
                $user = \Users::model()->findByAttributes(
                    array(
                        'personell_nr' => $request['consult_dr_nr']
                    )
                );
            }

            $department = \Department::model()->findbyAttributes(
                array('nr' => $request['consult_dept'])
            );

            $param                  = array();
            $param["token"]         = $consultRequest->access_token;
            $param["doctor_name"]   = $user->name;
            $param["doctor_id"]     = $request['consult_dr_nr'];
            $param["user_username"] = $user->login_id;
            $param["encounter_no"]  = $encounter_nr;

            $person = CarePerson::model()->findByPk($model->pid);

            $notification = new NotificationService();
            
            $payload = array(
                'consult_id' => $request['consultId'],
                'sender'     => $consultRequest->onesignal_player_id,
                'title'      => 'SPMC Online Consultation',
                'message'    => ($person->name_first.' '.$person->name_last).", Your appointment has been re-assigned to ".($user->name ? " with Dr. " .$user->name : " in department" ) ." of ". $department->name_formal .
                    ". Please wait for your schedule. We will send you your room address later.",
                'users'      => \CJSON::encode(array($consultRequest->onesignal_player_id)),
                'param_data' => \CJSON::encode($param)
            );

            $notification->sendNotification('/notification/telemed/patient/assigned/doctor', $payload);

            $criteria         = new \CDbCriteria();
            $criteria->params = array(
                ':now'    => date("Y-m-d"),
                ':status' => ConsultMeeting::STATUS_CONFIRMED,
                ':doctor' => $request['consult_dr_nr']
            );
            $criteria->condition = 't.status = :status AND DATE(t.create_dt) = :now AND t.doctor_id = :doctor';
            
            
            $data                = ConsultMeeting::model()->findAll($criteria);
    
            if (count($data) < ConsultMeeting::CONSULTATION_LIMIT) {
                $json                  = array();
                $json['user_username'] = $user->login_id;
                $json['encounter_no']  = $encounter_nr;
                $json['doc_name']      = $user->name;
                $json['dept_name']     = $department->name_formal;
                $payload               = array(
                    'consult_id' => $request['consultId'],
                    'sender'     => $consultRequest->onesignal_player_id,
                    'title'      => "SPMC Online Consultation",
                    'message'    => ($person->name_first.' '.$person->name_last).", Do you want to proceed your appointment".(!empty($user->name) ? " with Dr. " .$user->name. " of " : " in the department " ) .$department->name_formal,
                    'users'      => \CJSON::encode(array($consultRequest->onesignal_player_id)),
                    'param_data' => \CJSON::encode($json)
                );

                $meeting->conf_notif_sent = ConsultMeeting::CONSULTATION_SENT;
                $notification->sendNotification('/notification/telemed/confirm/patient', $payload);
            }
            
            if (!$meeting->save()) {
                throw new \Exception("There's an error updating meeting");
            }
        }
    }

    /***
     * Returns count of online consultation requests for the day.
     */
    public function getConsultCountForMedRec() {
        $date_today = date('Y-m-d');        

        $command = \Yii::app()->db->createCommand()
            ->select('count(cr.consult_id) requests')
            ->from('seg_consult_request cr')
            ->leftJoin('seg_consult_meeting cm', 'cr.consult_id = cm.consult_id')
            ->where("(cr.request_status = :status) AND (cr.create_dt BETWEEN :startDt AND :endDt)",
                    array(
                        ':status'  => self::TRIAGED,
                        ':startDt' => $date_today.' 00:00:00',
                        ':endDt'   => $date_today.' 23:59:59'
                    ))
            ->andWhere('cm.consult_id IS NULL')
            ->order('cr.create_dt');
        $nRequests = $command->queryScalar();
        return $nRequests;
    }
    
    /***
     * 
     */
    public function isOnlineConsultMedRec() {
        require_once($root_path . 'include/care_api_classes/class_acl.php');
        $objAcl = new \Acl(\Yii::app()->SESSION['sess_temp_userid']);
   
        return $objAcl->checkPermissionRaw('_a_2_opdonlinecreateconsult') || $objAcl->checkPermissionRaw('_a_2_opdonlineregister');
    }

    /***
     * 
     */
    public function isBlockedConsultMedRec($consult_id)
    {
        $notify = NotificationActiveResource::instance();
        $status = $notify->isOngoingMedRec($consult_id);
		return $status['message'];
    }       
}
