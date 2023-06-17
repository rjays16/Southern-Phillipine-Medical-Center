<?php
Yii::import('pharmacy.models.*');

class PackageController extends Controller
{
    public $layout = '/layouts/main';

    public function actionIndex()
    {
        $listOrderPrice = 0.00;
        $packageModel = CarePharmaProductsMain::model()->getMedsByName();
//        CVarDumper::dump($packageModel, 10,true);
        $packageList = CHtml::listData($packageModel, 'bestellnum', 'generic');

        $listOrder = CarePharmaOutsideOrder::model()->fetchOutsideOrder($_GET);

        $newMedsModel = PhilMedicine::model()->getMedsByName();
//
        $newMedsList = CHtml::listData($newMedsModel, 'drug_code', 'description');

        $dosageList = PharmacyDosages::model()->getDosageList();
        #$dosageDataList = CHtml::listData($dosageList,'strength_code','strength_disc');
        $frequencyList = PharmacyFrequency::model()->getFrequencyList();
        #$frequencyDataList = CHtml::listData($frequencyList,'frequency_code','frequency_disc');
        $routeList = PharmacyRoutes::model()->getRoutesList();
        #$routeDataList = CHtml::listData($routeList,'route_code','route_disc');

        $dosageDataList = PharmacyDosages::model()->getDosageList();
        $frequencyDataList = PharmacyFrequency::model()->getFrequencyList();
        $routeDataList = PharmacyRoutes::model()->getRoutesList();

        foreach ($listOrder as $key => $value) {
                $listOrderPrice += ($value->price);
        }

        $dataProviderOrder =  new CArrayDataProvider(
            $listOrder,
            array(
                'pagination' => false,
                'keyField' => false
            )
        );

            if(!empty($_GET['search'])){
            $packageModel = CarePharmaProductsMain::model()->searchPharmaItem($_GET);

            $dataProvider =  new CArrayDataProvider(
                $packageModel,
                array(
                    'pagination' => array(
                        'pageSize' => 5
                    ),
                    'keyField' => false
                )
            );
                $newMedsModel = PhilMedicine::model()->searchOutsideItem($_GET);
                $dataProviderNew =  new CArrayDataProvider(
                    $newMedsModel,
                    array(
                        'pagination' => array(
                            'pageSize' => 5
                        ),
                        'keyField' => false
                    )
                );
        }else{
            $dataProvider =  new CArrayDataProvider(
                array(), 
                array(
                    'pagination' => array(
                        'pageSize' => 5,
                    ),
                    'keyField' => false
                )
            );
            $dataProviderNew =  new CArrayDataProvider(
                    array(),
                    array(
                        'pagination' => array(
                            'pageSize' => 5,
                        ),
                        'keyField' => false
                    )
                );
        }

        $this->render(
            'index',
            array(
                'packageList'       => $packageList,
                'newMedsList'       => $newMedsList,
                'dataProvider'      => $dataProvider,
                'encounter_nr'      => $_GET['encounter_nr'],
                'listOrder'         => $dataProviderOrder,
                'listOrder_price'   => $listOrderPrice,
                'dataProviderNew'   => $dataProviderNew,
                'dosageDataList'    => $dosageDataList,
                'frequencyDataList' => $frequencyDataList,
                'routeDataList'     => $routeDataList,
                'pid'               => $_GET['pid'],
            )
        );
    }

    public function actionDone(){
        $this->render(
            'done',
            array(
                'save' => $_GET['save']
            )
        );
    }



    public function actionSave(){
            
            $model = new CarePharmaOutsideOrder();
            $data = array();

            foreach($model->attributes as $k=>$v){
                if($k == "drug_code")continue;
                if(array_key_exists($k,$_REQUEST)){
                    if($k == "gen_code"){
                        
                        if(!$_REQUEST['isPhiLib']){
                            $data[$k] = $_REQUEST[$k];
                            $data["drug_code"] = Yii::app()->db->createCommand()->select('drug_code')
                                        ->from('care_pharma_products_main')
                                        ->where('bestellnum=:bestellnum',array(':bestellnum' => $_REQUEST['generic_code']))
                                        ->queryScalar();
                            if(!$data["drug_code"]){
                                $data["drug_code"] = NULL;
                            }
                            
                        }else{
                            $data[$k] = $v;
                            $data["drug_code"] = $_REQUEST[$k];
                        }
                        
                    }else{
                        $data[$k] = $_REQUEST[$k];
                    }
                    
                }else
                    $data[$k] = $v;
            }
            $data["create_id"] = $_SESSION["sess_login_userid"];
            $data["create_dt"] = date("Y-m-d H:i:s");
            
            $listOrderPrice = 0.00;
            $command = Yii::app()->db->createCommand();
            $saveok = $command->insert($model->tableName(), $data);

            if($saveok){
                $listOrder = CarePharmaOutsideOrder::model()->fetchOutsideOrder($_POST);
                foreach ($listOrder as $key => $value) {
                    $listOrderPrice += ($value->price);
                }
                    $msg = "Successfully Saved";
            }
            else{
                $msg = "Failed to Saved,Please check Qty Or Price ";
            }
       echo CJSON::encode(array('saved' => $success, 'total'=>$listOrderPrice,'msg' => $msg));
    }

    public function actionDelete(){

        $success = Yii::app()->db->createCommand()
                            ->update('care_pharma_outside_order',
                                        array('is_deleted'=> 1,
                                        ),
                                'id=:id',
                                array(':id'=>$_REQUEST['id'])
                            )->execute();

        echo CJSON::encode(array("deleted" => $success,'msg'=>"Deleted Data"));
    } 

    public function actionSavelist(){
        
        if($_POST['data']){
            foreach($_POST['data'] as $key => $value){
                $order = CarePharmaOutsideOrder::model()->findByPk($value['id']);
                $order->quantity = $value['quantity'];
                $order->dosage = $value['dosage'];
                $order->frequency = $value['frequency'];
                $order->route = $value['route'];
                $order->update();
            }
            
        }

        echo CJSON::encode("Successfully Saved.");
    }

    public function actionPrevious()
    {
        if ($_POST['bestellnum']) {
            $sql = "SELECT cp.frequency, cp.route
                    FROM care_pharma_outside_order cp
                    WHERE cp.encounter_nr =" . $_POST['encounter_nr'] . "
                    AND IFNULL(cp.gen_code, drug_code) ='"
                . $_POST['bestellnum'] . "'
                    AND cp.is_deleted = 0 ORDER BY cp.order_dt, cp.id DESC LIMIT 1";

            $command = Yii::app()->db->createCommand($sql)->queryRow();

            $sqlDosage = "SELECT cp.dosage
                    FROM care_pharma_outside_order cp
                    WHERE cp.encounter_nr = " . $_POST['encounter_nr'] . "
                    AND IFNULL(cp.gen_code, drug_code) ='"
                . $_POST['bestellnum'] . "'
                    AND cp.is_deleted = 0  ORDER BY cp.order_dt, cp.id DESC LIMIT 1";

            $commandDosage = Yii::app()->db->createCommand($sqlDosage)
                ->queryScalar();

            $sql = "SELECT 
                  prod_class,
                  drug_code
                FROM
                  care_pharma_products_main cp
                  WHERE cp.`bestellnum` ='" . $_POST['bestellnum'] . "'";

            $prodClass = Yii::app()->db->createCommand($sql)->queryAll();

            $value = $prodClass[0]['drug_code'] ? $prodClass[0]['drug_code']
                : $_POST['bestellnum'];

            $sql = "SELECT 
                  spms.`strength_disc`
                FROM
                  seg_phil_medicine spm
                  LEFT JOIN seg_phil_medicine_strength spms
                    ON spm.strength_code = spms.strength_code
                WHERE spm.`drug_code` ='" . $value . "'";

            $defaultDosage = Yii::app()->db->createCommand($sql)->queryScalar();

            $dosage = $commandDosage ? $commandDosage : $defaultDosage;

            echo CJSON::encode(
                array(
                    "route"     => $command['route'],
                    "frequency" => $command['frequency'],
                    "dosage"    => $dosage ? $dosage : '',
                    "prodClass" => $prodClass['prod_class']
                )
            );
        }
    }

}