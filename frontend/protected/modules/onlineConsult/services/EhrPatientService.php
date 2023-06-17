<?php


namespace SegHis\modules\onlineConsult\services;


use Encounter;
use Person;

class EhrPatientService
{

    public $dbConnection;

    public $encounter;

    public $person;


    /**
     * EhrPatientService constructor.
     * @param Encounter $encounter
     */
    public function __construct(
        Encounter $encounter,
        Person $person
    )
    {
        $this->dbConnection = \Yii::app()->getComponent('ehrDb');
        $this->encounter    = $encounter;
        $this->person       = $person;
    }

    public function creatEhrPatient()
    {

        $this->createPerson();
        $this->createPatient();
        $this->createEncounter();
        $this->createDeptEncounter();
        $this->creatEncounterDoctor();
    }


    public function createPatient()
    {
        $command = $this->dbConnection->createCommand();
        $command->select('pid');
        $command->from('smed_patient_catalog');
        $command->where('spin=:spin');
        $command->params[':spin'] = $this->encounter->pid;

        $patient = $command->queryRow();

        if (empty($patient)) {
            $command = $this->dbConnection->createCommand(
                "INSERT INTO smed_patient_catalog
                            (`spin`,`pid`, `create_dt`, `modify_dt`, `date_registered`)
                        VALUES 
                            (:spin,:pid,:create_dt,:modify_dt,:date_registered)");
            $command->bindValue(':spin', $this->encounter->pid);
            $command->bindValue(':pid', $this->encounter->pid);
            $command->bindValue(':create_dt', date('Y-m-d H:i:s'));
            $command->bindValue(':modify_dt', date('Y-m-d H:i:s'));
            $command->bindValue(':date_registered', $this->person->date_reg);
            $execute = $command->execute();

            if (!$execute) {
                throw new \Exception('Unable to Save Patient');
            }
        }
    }

    public function createPerson()
    {
        $command = $this->dbConnection->createCommand();
        $command->select('pid');
        $command->from('smed_person_catalog');
        $command->where('pid=:pid');
        $command->params[':pid'] = $this->encounter->pid;

        $person = $command->queryRow();

        if (empty($person)) {
            $command = $this->dbConnection->createCommand(
                "INSERT INTO smed_person_catalog(`pid`,`name_last`,`name_first`,`name_middle`,`suffix`,`gender`,`birth_date`,`birth_place`,`soundex_name_first`,`soundex_name_last`,`create_id`) 
                    VALUES (:pid,:nameLast,:nameFirst,:nameMiddle,:suffix,:gender,:birth_date,:birth_place,:soundex_name_first,:soundex_name_last,:create_id)");

            $command->bindValue(':pid', $this->encounter->pid);
            $command->bindValue(':nameLast', $this->person->name_last);
            $command->bindValue(':nameFirst', $this->person->name_first);
            $command->bindValue(':nameMiddle', $this->person->name_middle);
            $command->bindValue(':suffix', $this->person->suffix);
            $command->bindValue(':gender', $this->person->sex);
            $command->bindValue(':create_id', $_SESSION['sess_user_name']);
            $command->bindValue(':birth_date', $this->person->date_birth);
            $command->bindValue(':birth_place', $this->person->place_birth);
            $command->bindValue(':soundex_name_first', $this->person->soundex_namefirst);
            $command->bindValue(':soundex_name_last', $this->person->soundex_namelast);
            $execute = $command->execute();

            if (!$execute) {
                throw new \Exception('Unable to Save Patient');
            }
        }
    }

    public function createEncounter()
    {
        $command = $this->dbConnection->createCommand();
        $command->select('encounter_no');
        $command->from('smed_encounter');
        $command->where('encounter_no=:encounter_no');
        $command->params[':encounter_no'] = $this->encounter->encounter_nr;

        $encounter = $command->queryRow();

        if (empty($encounter)) {
            $command = $this->dbConnection->createCommand(
                "INSERT INTO smed_encounter(`encounter_no`,`encounter_date`,`spin`,`is_discharged` ,`is_online`) VALUES (:encounterNo,:encounterDate,:spin,:isDischarged , :isOnline)");
            $command->bindValue(':encounterNo', $this->encounter->encounter_nr);
            $command->bindValue(':encounterDate', date('Y-m-d H:i:s'));
            $command->bindValue(':spin', $this->encounter->pid);
            $command->bindValue(':isDischarged', 0);
            $command->bindValue(':isOnline', 1);

            $execute = $command->execute();
            if (!$execute) {
                throw new \Exception('Unable to Save Patient');
            }
        }
    }

    public function createDeptEncounter()
    {
        $command = $this->dbConnection->createCommand();
        $command->select('encounter_no, er_areaid');
        $command->from('smed_dept_encounter');
        $command->where('deptenc_no=:encounter_no');
        $command->params[':encounter_no'] = $this->encounter->encounter_nr;

        $deptEncounter = $command->queryRow();

        if (empty($deptEncounter)) {

            $command = $this->dbConnection->createCommand(
                "INSERT INTO smed_dept_encounter(
                    `deptenc_no`,
                    `encounter_no`,
                    `deptenc_code`,
                    `er_areaid`,
                    `deptenc_date`
                    ) VALUES (:deptencNo,:encounterNo,:deptEncCode,:erAreaid,:deptEncDate)");

            $command->bindValue(':deptencNo', $this->encounter->encounter_nr);
            $command->bindValue(':encounterNo', $this->encounter->encounter_nr);
            $command->bindValue(':deptEncCode', 'opo');
            $command->bindValue(':erAreaid', $this->encounter->current_dept_nr);
            $command->bindValue(':deptEncDate', date('Y-m-d H:i:s'));
        } else {
            if ($deptEncounter['er_areaid'] != $this->encounter->current_dept_nr) {
                $command = $this->dbConnection->createCommand(
                "UPDATE smed_dept_encounter SET er_areaid=:erAreaid WHERE deptenc_no=:encounter_no");

                $command->bindValue(':erAreaid', $this->encounter->current_dept_nr);
                $command->bindValue(':encounter_no', $this->encounter->encounter_nr);
            }
        }

        $execute = $command->execute();
        if (!$execute) {
            throw new \Exception('Unable to Save Dept Encounter');
        }

    }

    public function creatEncounterDoctor()
    {

        $command = $this->dbConnection->createCommand();
        $command->select('encounter_no');
        $command->from('smed_encounter_doctor');
        $command->where('encounter_no=:encounter_no');
        $command->params[':encounter_no'] = $this->encounter->encounter_nr;

        $doctorEncounter = $command->queryRow();

        if (!$doctorEncounter) {
            $this->isDoctorSave();
        } else {
            $command = $this->dbConnection->createCommand(
                "UPDATE smed_encounter_doctor SET is_deleted=:is_deleted WHERE encounter_no=:encounter_no");

            $command->bindValue(':is_deleted', 1);
            $command->bindValue(':encounter_no', $this->encounter->encounter_nr);

            $execute = $command->execute();
            if (!$execute) {
                throw new \Exception('Unable to Save Dept Encounter');
            } else {
                $this->isDoctorSave();
            }
        }
    }

    public function isDoctorSave()
    {
        $command = $this->dbConnection->createCommand(
            "INSERT INTO smed_encounter_doctor(`id`,`encounter_no`,`doctor_id`,`is_primary`) VALUES (:id,:encounterNo,:doctorId,:isPrimary)"
        );
        $uuid = \Yii::app()->db->createCommand('Select UUID()')
            ->queryScalar();

        $command->bindValue(':id', $uuid);
        $command->bindValue(':encounterNo', $this->encounter->encounter_nr);
        $command->bindValue(
            ':doctorId', ($this->encounter->consulting_dr_nr? $this->encounter->consulting_dr_nr : NULL)
        );
        $command->bindValue(':isPrimary', 1);

        $execute = $command->execute();
        if (!$execute) {
            throw new \Exception('Unable to Save Dept Encounter');
        }
    }
}
