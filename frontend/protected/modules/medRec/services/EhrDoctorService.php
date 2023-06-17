<?php


namespace SegHis\modules\medRec\services;




class EhrDoctorService
{

    public $dbConnection;

    public $personnel_nr;

    public $email;

    public $password;



    /**
     * EhrPatientService constructor.
     * @param Encounter $encounter
     */
    public function __construct(
       
    )
    {
        $this->dbConnection = \Yii::app()->getComponent('ehrDb');
        $this->personnel_nr   = $personnel_nr;
    }

   


    public function createDoctorWebex($data)
    {


        $command = $this->dbConnection->createCommand();
        $command->select('id');
        $command->from('smed_webex');
        $command->where('personnel_id=:personnel_id');
        $command->params[':personnel_id'] = $data['personnel_id'];

        $doctor = $command->queryRow();

        if (empty($doctor)) {
            $command = $this->dbConnection->createCommand(
                "INSERT INTO smed_webex
                            (id,personnel_id,site_name,webex_id,password,create_dt)
                        VALUES 
                            (:id,:personnel_id,:site_name,:webex_id,:password,:create_dt)");
            $command->bindValue(':id',md5(date('Y-m-d H:i:s')));
            $command->bindValue(':personnel_id',$data['personnel_id']);
            $command->bindValue(':site_name', 'spmc');
            $command->bindValue(':webex_id', $data['webexUser']);
            $command->bindValue(':password', $data['webexpass']);
            $command->bindValue(':create_dt', date('Y-m-d H:i:s'));
            $execute = $command->execute();

            if (!$execute) {
                throw new \Exception('Unable to Save Patient');
            }
        }else{

             $command = $this->dbConnection->createCommand(
                "UPDATE smed_webex
                            SET webex_id=:webex_id,password=:password, modified_dt=:modified_dt WHERE personnel_id = :personnel_id");
            
            $command->bindValue(':personnel_id',$data['personnel_id']);
            $command->bindValue(':webex_id', $data['webexUser']);
            $command->bindValue(':password', $data['webexpass']);
            $command->bindValue(':modified_dt', date('Y-m-d H:i:s'));

            $execute = $command->execute();

            if (!$execute) {
                throw new \Exception('Unable to Save Patient');
            }

        }

    }







}
