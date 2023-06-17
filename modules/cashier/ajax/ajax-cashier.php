<?php
include 'roots.php';
include $root_path.'include/inc_environment_global.php';
include $root_path.'include/care_api_classes/class_walkin.php';

AjaxCashier::call($_GET['a']);

/**
 * Class AjaxCashier
 * @author Nick 4-27-2015
 */
class AjaxCashier {
    public static function call($functionName){
        $me = new AjaxCashier;
        $functionName =  self::getFunctionName($functionName);
        if(method_exists($me,$functionName)){
            $me->$functionName();
        }else{
            die('method does not exist');
        }
    }

    private static function getFunctionName($functionName){
        return 'action'.strtoupper($functionName[0]).substr($functionName, 1);
    }

    public function actionSearchWalkIn(){
        $walkin = new SegWalkin;
        $term = $_GET['term'];
        if(is_numeric($term)){
            $filter = array('id' => $term);
        }else{
            $filter = array('name' => $term);
        }

        $result = $walkin->searchWalkin($filter);

        $rows = null;

        if($result){
            if($result->RecordCount() > 0){
                $rows = array();
                foreach ($result->GetRows() as $key => $row) {
                    $name = strtoupper($row['name_last'] . ',' . $row['name_first']);
                    $rows[] = array(
                        'id' => $row['pid'],
                        'description' => $name,
                        'label' => $name,
                        'value' => $name,
                        'address' => $row['address']
                    );
                }
            }
        }

        echo json_encode($rows);
    }
}