<?php
Yii::import('or.models.*');

class PackageController extends Controller
{
    public $layout = '/layouts/main';

    public function actionIndex()
    {
        $package_price = 0;
        $package_name = 'wewe';
        $packageModel = Packages::model()->findAllByPatientType($_GET['encounter_nr']);
        $packageList = CHtml::listData($packageModel, 'package_id', 'package_name');
        $inventoryService = new InventoryServiceYii();

        $packageDetailsModel = Packages::model()->findAllByPatientType($_GET['encounter_nr']);
        $packageProvider =  new CArrayDataProvider(
            $packageDetailsModel,
            array(
                'pagination' => array(
                    'pageSize' => 1000
                ),
                'keyField' => false
            )
        );

        if(!empty($_GET['search'])){

            $packageDetails = new PackageDetails();

            $packageDetailsModel = $packageDetails->getPackageDetailsByName($_GET);
//             \CVarDumper::dump($_GET, 10, true);die;

            $dataProvider =  new CArrayDataProvider(
                $packageDetailsModel,
                array(
                    'pagination' => array(
                        'pageSize' => 1000
                    ),
                    'keyField' => false
                )
            );


            foreach ($packageDetailsModel as $key => $value) {
                $package_price += ($value->price * $value->quantity);
            }
            $package_name = $packageDetailsModel[0]->package->package_name;

            // CVarDumper::dump($package_name);


        }else{
            $dataProvider =  new CArrayDataProvider(
                array(),
                array(
                    'pagination' => array(
                        'pageSize' => 1000,
                    ),
                    'keyField' => false
                )
            );
        }

        if(!empty($_POST)){
            $get_lock = Yii::app()->db->createCommand("SELECT GET_LOCK('SAVE_PACKAGE_DETAILS',10) AS lock_state")->queryRow();
            if($get_lock['lock_state']){
                $save = $this->savePackageDetails($_POST);
                Yii::app()->db->createCommand("SELECT RELEASE_LOCK('SAVE_PACKAGE_DETAILS') AS lock_state")->queryRow();
            }else{
                $save = false;
            }
            $_SESSION['tempDetails'] = $save['details']['pharmaDetails'];

            $this->redirect(
                $this->createUrl(
                    'done',
                    array(
                        'save'          => $save['status'] ? 1 : 0,
                        'packageSelect' => $_POST['packageSelect'],
                        'trans_type'    => $_POST['trans_type'],
                        'pharma_refno'  => $save['details']['headerId'],
                        'details'       => $save['details']['pharmaDetails'],

                    )
                )
            );
        }

        $insurance = EncounterInsurance::model()->findByAttributes(array('encounter_nr' => $_GET['encounter_nr']));
        $pharmacyAreas = PersonellInvArea::model()->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);
        $dosageDataList = PharmacyDosages::model()->getDosageList();
        $frequencyDataList = PharmacyFrequency::model()->getFrequencyList();
        $routeDataList = PharmacyRoutes::model()->getRoutesList();

        $this->render(
            'index',
            array(
                'packageList'       => $packageList,
                'dataProvider'      => $dataProvider,
                'packageProvider'   => $packageProvider,
                'inventoryService'  => $inventoryService,
                'encounter_nr'      => $_GET['encounter_nr'],
                'package_price'     => $package_price,
                'package_name'      => $package_name,
                'insurance'         => $insurance,
                'pharmacyAreas'     => $pharmacyAreas,
                'dosageDataList'    => $dosageDataList,
                'frequencyDataList' => $frequencyDataList,
                'routeDataList'     => $routeDataList,
            )
        );
    }

    public function actionDone(){
        $pharmacyAreas = PersonellInvArea::model()->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);

        if(!empty($_POST)){
            $get_lock = Yii::app()->db->createCommand("SELECT GET_LOCK('SAVE_FAILED_PHARMA',-1) AS lock_state")->queryRow();
            if($get_lock['lock_state']){
                $save = $this->saveFailedInvPharma($_POST);
                Yii::app()->db->createCommand("SELECT RELEASE_LOCK('SAVE_FAILED_PHARMA') AS lock_state")->queryRow();
            }else{
                $save = false;
            }

            $_SESSION['tempDetails'] = $save['details']['pharmaDetails'];

            $this->redirect(
                $this->createUrl(
                    'done',
                    array(
                        'save'          => $save['status'] ? 1 : 0,
                        'packageSelect' => $_POST['packageSelect'],
                        'trans_type'    => $_POST['trans_type'],
                        'pharma_refno'  => $save['details']['headerId']
                    )
                )
            );
        }

        $this->render(
            'done',
            array(
                'save'          => $_GET['save'],
                'packageSelect' => $_GET['packageSelect'],
                'trans_type'    => $_GET['trans_type'],
                'pharma_refno'  => $_GET['pharma_refno'],
                'pharmacyAreas' => $pharmacyAreas
            )
        );
    }

    private function _generateRefno(){
        return time().rand(10,99);
    }

    public function savePackageDetails($_POST)
    {
        $params = $_POST;
        $packageModel = Packages::model()->findByPk($params['packageSelect']);
        $enc_nr = $params['encounter_nr'];
        $trans_type = $params['trans_type'];
        $charge_type = $params['charge_type'];
        $encounterModel = Encounter::model()->findByPk($enc_nr);
        $pharmacyArea = $params['pharmacy_area'];
        $dosage = $params['dosage'];
        $frequency = $params['frequency'];
        $route = $params['route'];
        $dfr = 0;

        $pharmaHB = false;
        $pharmaHId = '';
        $pharmaFailed = array();
        $labHB = false;
        $labHId = '';
        $radHB = false;
        $radHId = '';
        $miscHB = false;
        $miscHId = '';
        $saveOk = true;

        $transaction = Yii::app()->getDb()->beginTransaction();
        $entryNo = MiscService::model()->getEntry($enc_nr);

        $orRequest = new OrRequest();
        $or_refno = $this->_generateRefno();
        $orRequest->attributes = array(
            'or_refno' => $or_refno,
            'encounter_nr' => $enc_nr,
            'trans_type' => 0,
            'date_requested' => date("Y-m-d H:i:s"),
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
        );
        if(!$orRequest->save()){
            $transaction->rollBack();
            return false;
        }

        $orPackageUse = new OrPackageUse();
        $orPackageUse->or_refno = $or_refno;
        $orPackageUse->package_id = $packageModel->package_id;
        $orPackageUse->package_amount = 0;
        if($orPackageUse->save()){
            foreach ($packageModel->packageDetails as $key => $value) {
                $value->is_cash = $trans_type;
                switch ($value->item_purpose) {
                    case 'PH':
                        if(!$pharmaHB){
                            $pharmaHB = true;
                            $pharmaHId = $this->createPhHeader($encounterModel, $or_refno, $trans_type, $charge_type, $pharmacyArea);
                        }

                        if(empty($pharmaHId)){
                            $saveOk = false;
                            break;
                        }
                        $pharmaProductModel = PharmaProductsMain::model()->findByPk($value->item_code);

                        $tempSaveOk = $this->savePhDetails($pharmaHId, $trans_type, $pharmacyArea, $value, $pharmaProductModel);

                        $pClass = 'M'; /* M = Medicines */
                        if (strtoupper($pharmaProductModel->prod_class)
                            == $pClass
                        ) {
                            $this->savePharmaCf4(
                                $pharmaHId, $pharmaProductModel->bestellnum,
                                $dosage[$dfr], $frequency[$dfr], $route[$dfr]
                            );
                            $dfr++;
                        }

                        if($tempSaveOk === 2){
                            array_push($pharmaFailed, array('bestellnum' => $pharmaProductModel->bestellnum, 'name' => $pharmaProductModel->artikelname));
                            $saveOk = TRUE; //continue but saving failed
                        }else {
                            $saveOk = $tempSaveOk;
                        }
                        break;
                    case 'LB':
                        if(!$labHB){
                            $labHB = true;
                            $labHId = $this->createLbHeader($encounterModel, $or_refno, $trans_type, $charge_type);
                        }

                        if(empty($labHId)){
                            $saveOk = false;
                            break;
                        }
                        $saveOk = $this->saveLbDetails($labHId, $value);
                        break;
                    case 'RD':
                        if(!$radHB){
                            $radHB = true;

                            $rid = RadioId::model()->createNewRadioId($encounterModel->pid);
                            $radHId = $this->createRdHeader($encounterModel, $or_refno, $trans_type, $charge_type);
                        }

                        if(empty($radHId)){
                            $saveOk = false;
                            break;
                        }
                        $saveOk = $this->saveRdDetails($radHId, $value);
                        break;
                    case 'MISC':
                        if(!$miscHB){
                            $miscHId = $this->createMiscHeader($encounterModel, $trans_type);
                            $miscHB = true;
                        }

                        if(empty($miscHId)){
                            $saveOk = false;
                            break;
                        }
                        $saveOk = $this->saveMiscDetails($miscHId, $entryNo, $value);
                        break;
                    default:
                        continue;
                }


                $orPackagesItem = new OrPackagesItems();
                $orPackagesItem->seg_or_package_use_id = $orPackageUse->id;
                $orPackagesItem->or_refno = $or_refno;
                $orPackagesItem->package_id = $value->package_id;
                $orPackagesItem->item_code = $value->item_code;
                $orPackagesItem->qty = $value->quantity;
                $orPackagesItem->price = $value->getPrice();
                if(!$orPackagesItem->save()){
                    $saveOk = false;
                    break;
                }

                $orPackageUse->package_amount += ($value->price * $value->quantity);
                if(!$orPackageUse->save()){
                    $saveOk = false;
                    break;
                }

                if(!$saveOk) {
                    break;
                }

            }
        }else{
            $saveOk = false;
        }

        if($saveOk){
            $transaction->commit();
            return array('status' => $saveOk, 'details' => array('headerId' => $pharmaHId, 'pharmaDetails'=>$pharmaFailed));
        }
        else{
            $transaction->rollBack();
            return array('status' => false, 'details' => null);
        }
    }

    private function createPhHeader($encounterModel, $or_refno, $trans_type, $charge_type, $pharmacyArea){
        $pharmaOrdersRefno = PharmaOrders::model()->latest()->find()->refno + 1;
        $pharmaOrdersModel = new PharmaOrders();
        $pharmaOrdersModel->attributes = array(
            'refno' => $pharmaOrdersRefno,
            'orderdate' => date('Y-m-d H:i:s'),
            'pharma_area' => $pharmacyArea,
            'request_source' => $_GET['req_src'],
            'pid' => $encounterModel->pid,
            'encounter_nr' => $encounterModel->encounter_nr,
            'related_refno' => $or_refno,
            'ordername' => $encounterModel->person->fullname,
            'orderaddress' => $encounterModel->person->fullAddress,
            'charge_type' => $trans_type?null:$charge_type,
            'is_cash' => $trans_type,
            'serve_status' => 'S',
            'amount_due' => 0,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid']
        );

        if(!$pharmaOrdersModel->save())
            return null;

        return $pharmaOrdersRefno;
    }



    //returns true = success, false = failed, 2 = item not saved because of inventory
    private function savePhDetails($pharmaOrdersRefno, $is_cash, $pharmacyArea, $packageDetail, $pharmaProductModel){
        $saveOkP = true;
        $packageDetail->is_cash=$is_cash;
        //for inventory
        $inventoryService = new InventoryServiceYii();
        // if($inventoryService->hasErrorConnecting && $pharmaProductModel->is_in_inventory) return 2;
        $sendData = array(
            "barcode" => $pharmaProductModel->barcode,
        );

        $stockOnHand = 0;

        //end inv
        if(!$inventoryService->hasErrorConnecting){
            if($pharmaProductModel->is_in_inventory){
                $invInfo = $inventoryService->getItemInfo($pharmacyArea,$sendData);
                if($invInfo){
                    $stockOnHand = $invInfo['quantity'];
                    $itemBarcode = $invInfo['barcode'];
                }
            }
            if(($stockOnHand >= $packageDetail->quantity && $itemBarcode==$pharmaProductModel->barcode) || !$pharmaProductModel->is_in_inventory){
                $proceed  = TRUE;
                $pharmaOrderItemsModel = new PharmaOrderItems();
                $pharmaOrderItemsModel->attributes = array(
                    'refno' => $pharmaOrdersRefno,
                    'bestellnum' => $packageDetail->item_code,
                    'is_fs' => $packageDetail->is_fs,
                    'pharma_area' => $pharmacyArea,
                    'quantity' => $packageDetail->quantity,
                    'pricecash' => $packageDetail->getPrice(),
                    'pricecharge' => $packageDetail->getPrice(),
                    'price_orig' => $packageDetail->getPrice(),
                    'serve_remarks' => ' ',
                    'serve_status' => 'N',
                );

                if(!$is_cash){
                    $pharmaOrdersModel = PharmaOrders::model()->findByPk($pharmaOrdersRefno);
                    $inv_uid = null;
                    $sendData = array(
                        "item_code" => $pharmaProductModel->item_code, //different from $packageDetail->item_code
                        "barcode" => $pharmaProductModel->barcode,
                        "quantity" => $packageDetail->quantity,
                        "hnumber" => $pharmaOrdersModel->pid,
                        "cnumber" => $pharmaOrdersModel->encounter_nr,
                        "fname" => $pharmaOrdersModel->p->name_first,
                        "lname" => $pharmaOrdersModel->p->name_last.'-RefNo.('.$pharmaOrdersRefno.')',
                    );

                    if($pharmaProductModel->is_in_inventory){
                        /* kato ang padung sa api */
                        $res = $inventoryService->transactItem($pharmacyArea, $sendData);
                        if($res){
                            $start = stripos($res, "[") + 1;

                            if($start !== false){
                                $length = stripos($res, "]") - $start;
                                $inv_uid = substr($res, $start, $length);
                            }
                        }
                    }
                    if(!empty($inv_uid)  || !$pharmaProductModel->is_in_inventory){
                        $pharmaOrderItemsModel->attributes = array(
                            'serve_status' => 'S',
                            'serve_dt' => date('Y-m-d H:i:s'),
                            'inv_uid' => $inv_uid,
                        );
                    }else $proceed = FALSE;
                }

                if($proceed){
                    if(!$pharmaOrderItemsModel->save()) $saveOkP = false;
                    if($saveOkP){
                        $pharmaOrdersModel = PharmaOrders::model()->findByPk($pharmaOrdersRefno);
                        $pharmaOrdersModel->amount_due += ($packageDetail->quantity *  $packageDetail->getPrice());
                        return $pharmaOrdersModel->save();
                    }else return 0;
                }
                return 2;
            }
            return 2;
        }else{
            $proceed  = TRUE;
            $pharmaOrderItemsModel = new PharmaOrderItems();
            $pharmaOrderItemsModel->attributes = array(
                'refno' => $pharmaOrdersRefno,
                'bestellnum' => $packageDetail->item_code,
                'is_fs' => $packageDetail->is_fs,
                'pharma_area' => $pharmacyArea,
                'quantity' => $packageDetail->quantity,
                'pricecash' => $packageDetail->getPrice(),
                'pricecharge' => $packageDetail->getPrice(),
                'price_orig' => $packageDetail->getPrice(),
                'serve_remarks' => ' ',
                'serve_status' => 'N',
            );

            if(!$is_cash){
                $pharmaOrdersModel = PharmaOrders::model()->findByPk($pharmaOrdersRefno);
                $inv_uid = null;
                $sendData = array(
                    "item_code" => $pharmaProductModel->item_code, //different from $packageDetail->item_code
                    "barcode" => $pharmaProductModel->barcode,
                    "quantity" => $packageDetail->quantity,
                    "hnumber" => $pharmaOrdersModel->pid,
                    "cnumber" => $pharmaOrdersModel->encounter_nr,
                    "fname" => $pharmaOrdersModel->p->name_first,
                    "lname" => $pharmaOrdersModel->p->name_last.'-RefNo.('.$pharmaOrdersRefno.')',
                );
            }
            // CVarDumper::dump($proceed);die();
            if($proceed){
                if(!$pharmaOrderItemsModel->save()) $saveOkP = false;
                if($saveOkP){
                    $pharmaOrdersModel = PharmaOrders::model()->findByPk($pharmaOrdersRefno);
                    $pharmaOrdersModel->amount_due += ($packageDetail->quantity *  $packageDetail->getPrice());
                    return $pharmaOrdersModel->save();
                }else return 0;
            }
            return 2;
        }
        return 2;
    }

    private function createMiscHeader($encounterModel, $trans_type){
        $miscServiceModel = new MiscService;
        $refno = $miscServiceModel->getPk(date('Y-m-d H:i:s'));
        $miscServiceModel->attributes = array(
            'refno' => $refno,
            'chrge_dte' => date('Y-m-d H:i:s'),
            'encounter_nr' => $encounterModel->encounter_nr,
            'pid' => $encounterModel->pid,
            'is_cash' => $trans_type,
            'request_source' => $_GET['req_src'],
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid']
            // 'area' => 'Area',
        );
        if(!$miscServiceModel->save())
            return null;

        return  $refno;
    }

    private function saveMiscDetails($miscServRefno, $entryNo, $packageDetail){
        $tempModel = OtherServices::model()->findByPk($packageDetail->item_code);
        $miscServicedetailsModels = new MiscServiceDetails;
        $miscServicedetailsModels->attributes = array(
            'refno' => $miscServRefno,
            'service_code' => $tempModel->alt_service_code,
            'entry_no' => $entryNo,
            'account_type' => 0,
            'adjusted_amnt' => $packageDetail->getPrice(),
            'chrg_amnt' => $packageDetail->getPrice(),
            'quantity' => $packageDetail->quantity,
            'is_fs' => $packageDetail->is_fs,
        );

        if(!$miscServicedetailsModels->save())
            return false;
        else
            return true;
    }

    public function createLbHeader($encounterModel, $or_refno, $trans_type, $charge_type){
        $labTrackerModel = LabTracker::model()->find();
        $labServRefno = $labTrackerModel->last_refno + 1;

        $labServModel = new LabServ();
        $labServModel->attributes = array(
            'refno' => $labServRefno,
            'serv_dt' => date('Y-m-d'),
            'serv_tm' => date('H:i:s'),
            'encounter_nr' => $encounterModel->encounter_nr,
            'pid' => $encounterModel->pid,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
            'ordername' => $encounterModel->person->fullname,
            'orderaddress' => $encounterModel->person->fullAddress,
            'source_req' => $_GET['req_src'],
            'grant_type' => $trans_type?null:strtolower($charge_type),
            'is_cash' => $trans_type,
            'ref_source' => 'LB',
            'status' => ' '
        );
        if(!$labServModel->save())
            return null;

        $labTrackerModel->last_refno = $labServRefno;
        if(!$labTrackerModel->save())
            return null;

        return $labServRefno;
    }

    public function saveLbDetails($labHId, $packageDetail){
        $labServdetailsModel = new LabServdetails();
        $labServdetailsModel->attributes = array(
            'refno' => $labHId,
            'service_code' => $packageDetail->item_code,
            'price_cash' => $packageDetail->getPrice(),
            'price_cash_orig' => $packageDetail->getPrice(),
            'price_charge' => $packageDetail->getPrice(),
            'quantity' => $packageDetail->quantity,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
        );
        if(!$labServdetailsModel->save())
            return false;
        else
            return true;
    }

    public function createRdHeader($encounterModel, $or_refno, $trans_type, $charge_type){
        $radioServRefno = RadioServ::model()->latest()->find()->refno + 1;

        $radioServModel = new RadioServ();
        $radioServModel->attributes = array(
            'refno' => $radioServRefno,
            'request_date' => date('Y-m-d'),
            'request_time' => date('H:i:s'),
            'encounter_nr' => $encounterModel->encounter_nr,
            'pid' => $encounterModel->pid,
            'ordername' => $encounterModel->person->fullname,
            'orderaddress' => $encounterModel->person->fullAddress,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
            'source_req' => $_GET['req_src'],
            'type_charge' => $trans_type?null:$charge_type,
            'is_cash' => $trans_type,
            'status' => ' ',
        );
        if(!$radioServModel->save())
            return null;

        return $radioServRefno;
    }

    public function saveRdDetails($radHId, $packageDetail){
        $careTestRequestRadioModel = new CareTestRequestRadio();
        $careTestRequestRadioRefno = CareTestRequestRadio::model()->latest()->find()->refno + 1;
        $careTestRequestRadioModel->attributes = array(
            'batch_nr' => $careTestRequestRadioRefno,
            'refno' => $radHId,
            'clinical_info' => ' ',
            'service_code' => $packageDetail->item_code,
            'price_cash' => $packageDetail->getPrice(),
            'price_cash_orig' => $packageDetail->getPrice(),
            'price_charge' => $packageDetail->getPrice(),
            'service_date' => date("Y-m-d H:i:s", '0000-00-00'),
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
            'status' => 'pending',
            'is_in_house' => 0,
            'request_doctor' => ' ',
            'request_date' => date('Y-m-d'),
            'encoder' => $_SESSION['sess_temp_userid'],
        );
        if(!$careTestRequestRadioModel->save())
            return false;
        else
            return true;
    }

    //try to save failed pharmacy items into different pharma area
    private function saveFailedInvPharma($params){
        $transaction = Yii::app()->getDb()->beginTransaction();

        $packageDetailsModel = PackageDetails::model()->findAllByAttributes(
            array(
                'package_id' => $params['packageSelect'],
                'item_purpose' => 'PH',
                'item_code' => $params['item_code']
            )
        );
        $pharmaHId = $params['pharma_refno'];
        $trans_type = $params['trans_type'];
        $pharmaFailed = array();
        $saveOk = true;

        foreach ($packageDetailsModel as $key => $value) {
            if(!empty($pharmaHId)){
                $pharmaProductModel = PharmaProductsMain::model()->findByPk($value->item_code);
                $pharmacyArea = $params['pharmacy_area'][$value->item_code];

                $tempSaveOk = $this->savePhDetails($pharmaHId, $trans_type, $pharmacyArea, $value, $pharmaProductModel);

                if($tempSaveOk === 2){
                    array_push(
                        $pharmaFailed,
                        array(
                            'bestellnum' => $pharmaProductModel->bestellnum,
                            'name' => $pharmaProductModel->artikelname
                        )
                    );
                    $saveOk = TRUE; //continue but saving failed
                }else
                    $saveOk = $tempSaveOk;
            }else{
                $saveOk = false;
            }
        }

        if($saveOk){
            $pharmaOrdersModel = PharmaOrders::model()->findByPk($pharmaHId);
            if(empty($pharmaOrdersModel->PharmaProductsMains)){
                $pharmaOrdersModel->delete();
            }
        }

        if($saveOk){
            $transaction->commit();
            return array('status' => true, 'details' => array('headerId' => $pharmaHId, 'pharmaDetails'=>$pharmaFailed));
        }
        else{
            $transaction->rollBack();
            return array('status' => false, 'details' => null);
        }
    }

    public function savePharmaCf4($ref, $bestellnum, $dosage, $frequency,
        $route
    ) {
        $sql = "SELECT sp.refno
                    FROM seg_pharma_items_cf4 sp
                    WHERE sp.refno = " . $ref . "
                    AND sp.bestellnum =" . $bestellnum;

        $sql = Yii::app()->db->createCommand($sql)->queryScalar();

        if (empty($sql)) {
            $command = Yii::app()->db->createCommand()
                ->insert(
                    'seg_pharma_items_cf4',
                    array(
                        'refno'      => $ref,
                        'bestellnum' => $bestellnum,
                        'dosage'     => $dosage,
                        'frequency'  => $frequency,
                        'route'      => $route,
                        'history'    => 'Create By: '.$_SESSION['sess_temp_userid'].' FROM Packages '.date('Y-m-d h:i:s'),
                        'create_id'  => $_SESSION['sess_temp_userid'],
                        'create_dt'  => date('Y-m-d H:i:s'),
                    )
                );
        }

        if ($command) {
            return true;
        } else {
            return false;
        }
    }

}