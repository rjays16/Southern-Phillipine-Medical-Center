<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/4/2019
 * Time: 3:44 AM
 */

require_once __DIR__ .'/../../inc_init_main.php';


class TelemedEventmanager
{

    // private static $ehr;


    // do not delete this function
    // this will be called every request instance to validate user requesting
    // public  function validateRequstUser($personelID)
    // {
    //     require_once __DIR__ . '/controller/User.php';
    //     $user = new User();
    //     $res = $user->getOne("
    //         select * from care_personell cp 
    //         where cp.nr = :nr 
    //         and cp.status not in ('expired','deleted')
    //         and job_function_title in ('Anesthesia nurse','Nurse', 'Admitting doctor','Attending doctor','Consulting doctor', 'Doctor', 'Doctor On Call')
    //     ", array( 'nr' => $personelID));
    //     return $res ? true : false;
    // }


    
    public function onSaveConsultationRequest($request)
    {
        /**
         * @var Route $request
         */
        require_once __DIR__ . '/controller/ConsultationController.php';
        
        try {
            $s1 = new ConsultationController();
            $s1->transaction();
            $result = $s1->saveConsultRequest(
                $request->postD('consult_id'), 
                $request->postD('fb_username'), 
                $request->postD('last_name'), 
                $request->postD('first_name'), 
                $request->postD('middle_name'), 
                $request->postD('birth_date'), 
                $request->postD('contact_number'), 
                $request->postD('sex'), 
                $request->postD('religion'), 
                $request->postD('onesignal_player_id'), 
                $request->postD('onesignal_push_token'), 
                $request->postD('device_model'), 
                $request->postD('device_uuid'),
                $request->postD('device_device_unique_id'), 
                $request->postD('device_platform'), 
                $request->postD('accessToken'),
                $request->postD('address'),
                $request->postD('chief_complaint'),
                $request->postD('yellow_card'),
                $request->postD('areRelated'),
                $request->postD('waiverAproved')
            );

            $s1->commit();
            return $request->response('Request saved.',  200,array(
                'status' => true,
                'result_data'=> $result,
            ));
        } catch (PDOException $e) {
            $s1->rollback();
            return $request->response($e->getMessage(),  $e->getCode(),array(
                'status' => false,
                'data_input' => $request->inputs(),
            ),$e);
        } catch (Exception $e) {
            $s1->rollback();
            return $request->response($e->getMessage(), $e->getCode(),array(
                'status' => false,
                'data_input' => $request->inputs(),
            ),$e);
        }
        
    }




    public function onPatientConfirm($request)
    {
        /**
         * @var Route $request
         */
        require_once __DIR__ . '/controller/ConsultationController.php';
        
        try {
            $s1 = new ConsultationController();
            $s1->transaction();
            $resp = $s1->onPatientConfirm(
                $request->postD('sender_player_id'), 
                $request->postD('is_acknw'), 
                $request->postD('encounter_no')
            );

            $s1->commit();
            return $request->response('Request updated.',  200,array(
                'status' => true,
                'resp_data' => $resp
            ));
        } catch (PDOException $e) {
            $s1->rollback();
            return $request->response($e->getMessage(),  $e->getCode(),array(
                'status' => false,
                'data_input' => $request->inputs(),
            ),$e);
        } catch (Exception $e) {
            $s1->rollback();
            return $request->response($e->getMessage(), $e->getCode(),array(
                'status' => false,
                'data_input' => $request->inputs(),
            ),$e);
        }
        
    }

}
