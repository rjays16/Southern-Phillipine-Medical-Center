<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../Notification.php';
class ConsultationController extends Database
{

    public function saveConsultRequest($consult_id, $fb_username,$lName, $fName, $mName, $bDate, $contactNo, $sex, $religion, $oneSigPlayerId, $oneSigPushToken, $deviceModel, $devicUuid,$deviceUniqueId, $devicePlat, $accessToken, $address, $chief_complaint,$yellow_card, $areRelated, $waiverAproved ){

        $id = $consult_id;
        $resp = $this->insert("INSERT INTO seg_consult_request
            (consult_id, fb_username, name_last, name_first, name_middle, date_birth, contact_no, sex, religion, create_dt, onesignal_player_id, onesignal_push_token, device_model, device_uuid, device_unique_id, device_platform, access_token, address, chief_complaint, yellow_card, areRelated, waiverAproved) 
            VALUES(:consult_id,:fb_username, :name_last, :name_first, :name_middle, :date_birth, :contact_no, :sex, :religion, :create_dt, :onesignal_player_id, :onesignal_push_token, :device_model, :device_uuid, :device_unique_id, :device_platform, :access_token, :addressV, :chief_complaint, :yellow_card, :areRelated, :waiverAproved)",
            array(
                'fb_username' => $fb_username,
                'consult_id' => $id,
                'name_last' => $lName,
                'name_first' => $fName,
                'name_middle' => $mName,
                'date_birth' => $bDate,
                'contact_no' => $contactNo,
                'sex' => $sex,
                'religion' => $religion,
                'create_dt' => date('Y-m-d H:i:s'),
                'onesignal_player_id' => $oneSigPlayerId,
                'onesignal_push_token' => $oneSigPushToken,
                'device_model' => $deviceModel,
                'device_uuid' => $devicUuid,
                'device_unique_id' => $deviceUniqueId,
                'device_platform' => $devicePlat,
                'access_token' => $accessToken,
                'addressV' => $address,
                'chief_complaint' => $chief_complaint,
                'yellow_card' => $yellow_card,
                'areRelated' => $areRelated,
                'waiverAproved' => $waiverAproved,
        ));

        if(!$resp){
            throw new Exception($this->getError(), 500);
        }

        return array(
            'consult_id' => $id,
            'sender' => '',
            'bpousers' => $this->getAllBpoUsers(),
            'users' => array(
                $oneSigPlayerId
            ),
        );
    }

    public function getAllBpoUsers()
    {
        return $this->runSelect("SELECT 
                                    login_id
                                from care_users 
                                where (permission like '%_a_1_opdonlinerequest%'
                                OR permission LIKE '%_a_0_all%')
                                AND lockflag = 0");
    }


    public function onPatientConfirm($sender_player_id,$is_acknw,$encounter_no)
    {
        $statuss = $is_acknw ? 'confirmed':'cancelled';
        $datetime = date('Y-m-d H:i:s');
        $resp = $this->runQuery("UPDATE seg_consult_meeting set status=:statuss, history=concat(history, :hist) where encounter_nr=:encounter_no",
            array(
                'statuss' => $statuss,
                'encounter_no' => $encounter_no,
                'hist' => ucfirst($statuss)." ON {$datetime}\n"
            )
        );
        

        if(!$resp){
            throw new Exception($this->getError(), 500);
        }
        


        $resp = $this->getOne("SELECT 
                                    cm.meeting_url,
                                    cd.name_formal,
                                    CONCAT(cp.name_last,' ', cp.name_first) AS doc_name, 
                                    CONCAT(cr.name_last,' ', cr.name_first) AS patient_name, 
                                    cr.consult_id,
                                    cu.login_id AS username
                                FROM seg_consult_meeting cm
                                INNER JOIN care_encounter c ON  c.encounter_nr = cm.encounter_nr
                                INNER JOIN care_department cd ON  cd.nr = c.consulting_dept_nr
                                LEFT JOIN care_users cu ON  cu.personell_nr = cm.doctor_id
                                LEFT JOIN care_personell cpp ON  cpp.nr = cm.doctor_id
                                LEFT JOIN care_person cp ON  cp.pid = cpp.pid
                                LEFT JOIN seg_consult_request cr ON  cr.consult_id = cm.consult_id
                                WHERE cm.encounter_nr=:encounter_no",
            array(
                'encounter_no' => $encounter_no,
            )
        );


        return array(
            'status' => true,
            'meeting_room' =>  $is_acknw ? $resp['meeting_url'] : '',
            'patient_name' =>  $resp['patient_name'],
            'bpousers' => $this->getAllBpoUsers(),
            'doc_name' =>  $resp['doc_name'],
            'department_name' => $resp['name_formal'],
            'consult_id' =>  $resp['consult_id'],
            'username' =>  $resp['username'],
            'statuss' => $statuss
        );;
    }

}