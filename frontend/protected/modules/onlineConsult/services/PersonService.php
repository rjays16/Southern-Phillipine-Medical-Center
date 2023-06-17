<?php


namespace SegHis\modules\onlineConsult\services;

use CarePerson;
use Config;


class PersonService
{

    public $dbConnection;

    public $date_reg;

    public $name_last;

    public $name_first;

    public $name_middle;

    public $date_birth;

    public $place_birth;

    public $sex;


    /**
     * PersonService constructor.
     * @param Encounter $encounter
     */
    public function __construct()
    {
        $this->date_reg     = $date_reg;
        $this->name_last    = $name_last;
        $this->name_first   = $name_first;
        $this->name_middle  = $name_middle;
        $this->date_birth   = $date_birth;
        $this->place_birth  = $place_birth;
        $this->sex          = $sex;
        $this->dbConnection = \Yii::app()->getComponent('ehrDb');
    }


    public function createPerson($data)
    {
        if (empty($data['date_reg']) ||
            empty($data['name_first']) ||
            empty($data['name_last']) ||
            empty($data['date_birth']) ||
            empty($data['sex']) ||
            empty($data['religion'])
        ) {
            throw new \Exception('Please fill in required fields');
        }
        $type = "person_id_nr_init";
        $getConfigHRN = Config::model()->findByPk($type);
        
        $queryHRN  = "SELECT pid FROM care_person WHERE pid>='" . $getConfigHRN . "' ORDER BY CAST(pid AS UNSIGNED) DESC";
        $sqlHRN    = \Yii::app()->db->createCommand($queryHRN);
        $latestHRN = $sqlHRN->queryScalar();

        $latestHRN = $latestHRN + 1;

        $final_name_first = $data['name_first'];

        $person = new CarePerson();
        $person->pid = $latestHRN;
        $person->date_reg = date('Y-m-d H:i:s', strtotime($data['date_reg']));

        if(strpos(addslashes($final_name_first), ', ') !== false){
            $commaPos = strpos(addslashes($final_name_first), ', ');

            $suffixFromName = substr($final_name_first, $commaPos+2);

            $suffix = $suffixFromName;
            $final_name_first = str_replace(', ', ' ', $final_name_first);

            $person->suffix = $suffix;
        }

        $person->name_first = $final_name_first;
        $person->name_middle = $data['name_middle'];
        $person->name_last = $data['name_last'];
        $person->place_birth = $data['place_birth'];
        $person->date_birth = date('Y-m-d',strtotime($data['date_birth']));
        $person->civil_status = $data['civil_status'];
        $person->sex = $data['sex'];
        $person->create_time = date('Y-m-d H:i:s');
        $person->cellphone_1_nr = $data['contact_no'];
        $person->create_id = $_SESSION['sess_user_name'];
        $person->religion = $data['religion'];
        $person->mun_nr =$data['mun_nr'];
        $person->street_name =$data['street_name'];
        $person->brgy_nr = ($data['brgy_nr']==''?'0':$data['brgy_nr']);


        $person->father_fname =$data['fathers_fname'];
        $person->mother_fname =$data['mother_fname'];
        $person->spouse_name =$data['spouse_name'];
        $person->guardian_name =$data['guardian_name'];
        $person->occupation =$data['occupation'];
        $person->citizenship =$data['citizenship'];


        $person->history = "Created ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']." thru online consultation";

        if (!$person->save()) {
            throw new \Exception("There was an error saving patient's details");
        } else {
            return array(
                'success' => true,
                'code'   => 200,
                'pid'    => $person->pid,
                'msg'    => 'Successfully saved'
            );
        }

    }

    public function updatePerson($data){
        if (empty($data['name_first']) ||
            empty($data['name_last']) ||
            empty($data['date_birth']) ||
            empty($data['sex']) ||
            empty($data['religion'])
        ) {
            throw new \Exception('Please fill in required fields');
        }

        $command =\Yii::app()->db->createCommand(
                "UPDATE care_person
                        SET name_first=:name_first,name_middle=:name_middle, name_last=:name_last,suffix=:suffix,place_birth=:place_birth,date_birth=:date_birth,religion=:religion,civil_status=:civil_status,sex=:sex,mun_nr=:mun_nr,brgy_nr=:brgy_nr,street_name=:street_name,father_fname=:father_fname,mother_fname=:mother_fname,spouse_name=:spouse_name,guardian_name=:guardian_name,occupation=:occupation,citizenship=:citizenship,modify_id=:modify_id,history=:history WHERE pid = :pid");

        $final_name_first = $data['name_first'];
        $suffix = NULL;

        if(strpos(addslashes($final_name_first), ', ') !== false){
            $commaPos = strpos(addslashes($final_name_first), ', ');

            $suffixFromName = substr($final_name_first, $commaPos+2);

            $suffix = $suffixFromName;
            $final_name_first = str_replace(', ', ' ', $final_name_first);
        }

        $command->bindValue(':pid',$data['pid']);
        $command->bindValue(':name_first', $final_name_first);
        $command->bindValue(':name_middle', $data['name_middle']);
        $command->bindValue(':name_last', $data['name_last']);
        $command->bindValue(':suffix', $suffix);
        $command->bindValue(':place_birth',$data['place_birth']);
        $command->bindValue(':date_birth',date('Y-m-d',strtotime($data['date_birth'])));
        $command->bindValue(':sex',$data['sex']);
        $command->bindValue(':religion',$data['religion']);
        $command->bindValue(':modify_id',$_SESSION['sess_user_name']);
        $command->bindValue(':civil_status',$data['civil_status']);
        $command->bindValue(':mun_nr',$data['mun_nr']);
        $command->bindValue(':brgy_nr',($data['brgy_nr']==''?'0':$data['brgy_nr']));
        $command->bindValue(':street_name',$data['street_name']);


        $command->bindValue(':father_fname',$data['father_fname']);
        $command->bindValue(':mother_fname',$data['mother_fname']);
        $command->bindValue(':spouse_name',$data['spouse_name']);
        $command->bindValue(':guardian_name',$data['guardian_name']);
        $command->bindValue(':occupation',$data['occupation']);
        $command->bindValue(':citizenship',$data['citizenship']);


        $command->bindValue(':history',$data['history']."\n Updated ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']);
        $execute = $command->execute();

        if (!$execute) {
            throw new \Exception("There was an error updating patient's details");
        }else{
            return array(
                'success' => true,
                'code'   => 200,
                'pid'    => $data['pid'],
                'msg'    => "Successfully updated patient's details"
            );
        }        

    }

    public function updatePersonEHR($data){

        $command = $this->dbConnection->createCommand();
        $command->select('pid');
        $command->from('smed_person_catalog');
        $command->where('pid=:pid');
        $command->params[':pid'] = $data['pid'];

        $person = $command->queryRow();
  
        if (!empty($person)) {
            $command = $this->dbConnection->createCommand(
                 "UPDATE smed_person_catalog
                            SET name_last=:nameLast,name_first=:nameFirst,name_middle=:nameMiddle,birth_date=:birth_date,birth_place=:place_birth,gender=:gender,modify_id=:modify_id WHERE pid = :pid");
       
            $command->bindValue(':nameLast', $data['name_last']);
            $command->bindValue(':nameFirst', $data['name_first']);
            $command->bindValue(':nameMiddle', $data['name_middle']);
            $command->bindValue(':gender', $data['sex']);
            $command->bindValue(':birth_date', date('Y-m-d',strtotime($data['date_birth'])));
            $command->bindValue(':place_birth',$data['place_birth']);
            $command->bindValue(':modify_id',$_SESSION['sess_user_name']);
            $command->bindValue(':pid',$data['pid']);
            $execute = $command->execute();

            if (!$execute) {
                throw new \Exception("There was an error updating patient's details");
            }else{
               return array(
                'success' => true,
                'code'   => 200,
                'pid'    => $data['pid'],
                    'msg'    => "Successfully updated patient's details"
            );
            }        
        }else{
             return array(
                'success' => true,
                'code'   => 200,
                'pid'    => $data['pid'],
                'msg'    => "Successfully updated patient's details"
            );
        }
    }

    public function assignPersonHRN($data){

          $command =\Yii::app()->db->createCommand(
                "UPDATE seg_consult_request
                        SET pid=:pid WHERE consult_id = :consult_id");
           $command->bindValue(':consult_id',$data['consult_id']);
           $command->bindValue(':pid',$data['pid']);

           $execute = $command->execute();
        if (!$execute) {
            throw new \Exception("There was an error updating patient's details");
        }else{
            return array(
                'success' => true,
                'code'   => 200,
                'pid'    => $data['pid'],
                'msg'    => "Successfully Assign"
            );
        }       
    }

}
