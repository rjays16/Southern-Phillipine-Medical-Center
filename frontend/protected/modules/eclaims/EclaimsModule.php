<?php

namespace SegHis\modules\eclaims;
/**
 * EclaimsModule
 *
 * @author Alvin Quinones <ajmquinones@gmail,com>
 * @copyright Copyright &copy; 2014. Segworks Technologies Corporation
 */

/**
 * @property EntityModel[] $entityModels
 */

class EclaimsModule extends \WebModule {
    /**
     *
     * @var type
     */
    public $layout = 'eclaims.views.layouts.ec-main';
    /**
     * @var string $title
     */
    public $title = 'Eclaims module';

    public $defaultController = 'main';

    /**
     *
     * @param Controller $controller
     * @param CAction $action
     */
    public function beforeControllerAction($controller, $action) {

        /**
        *  Added by JEFF 01-12-18 for access permission of modules for users at Main Menu/Navigation Bar.
        */
        require_once($root_path . 'include/care_api_classes/class_acl.php');
        $objAcl = new \Acl($_SESSION['sess_temp_userid']);

        $canManageAll = $objAcl->checkPermissionRaw('_a_0_all');
        $canManagemoduleAll = $objAcl->checkPermissionRaw('_a_1_eclaims_sudomanage');

        $canManagemodule1 = $objAcl->checkPermissionRaw('_a_2_eclaims_module1_sudomanage');
            $canPinVerify = $objAcl->checkPermissionRaw('_a_3_eclaims_module1_member_sudomanage');
            $canEligibility = $objAcl->checkPermissionRaw('_a_3_eclaims_module1_eligibility_sudomanage');
            $canDoctorAccreditation = $objAcl->checkPermissionRaw('_a_3_eclaims_module1_doctorAccreditation_sudomanage');

        $canManagemodule2 = $objAcl->checkPermissionRaw('_a_2_eclaims_module2_sudomanage');
            $canTransmittal = $objAcl->checkPermissionRaw('_a_3_eclaims_module2_transmittal_sudomanage');
            $canTransmitEclaims = $objAcl->checkPermissionRaw('_a_3_eclaims_module2_transmitEclaims_sudomanage');

        $canManagemodule3 = $objAcl->checkPermissionRaw('_a_2_eclaims_module3_sudomanage');
            $canCheckStatus = $objAcl->checkPermissionRaw('_a_3_eclaims_module3_claimStatus_sudomanage');
            $canGetVoucher = $objAcl->checkPermissionRaw('_a_3_eclaims_module3_voucherDetails_sudomanage');

        # Additional access permission for billing.
        $canBilling = $objAcl->checkPermissionRaw('_a_1_billmanage');
            $canTransmittalBilling = $objAcl->checkPermissionRaw('_a_1_billtransmittal');

        # -- Parent and Child Access Permission Algorithm -- #

        # Module 1 - check
        $m1typex = !$canPinVerify && !$canEligibility && !$canDoctorAccreditation && $canManagemodule1; // xxx
        $m1typey = $canPinVerify && $canEligibility && $canDoctorAccreditation && $canManagemodule1;    // 111

        $m1type1 = $canPinVerify && !$canEligibility && !$canDoctorAccreditation && $canManagemodule1;  // 1xx
        $m1type2 = !$canPinVerify && $canEligibility && !$canDoctorAccreditation && $canManagemodule1;  // x1x
        $m1type3 = !$canPinVerify && !$canEligibility && $canDoctorAccreditation && $canManagemodule1;  // xx1

        $m1type4 = $canPinVerify && $canEligibility && !$canDoctorAccreditation && $canManagemodule1;   // 11x
        $m1type5 = !$canPinVerify && $canEligibility && $canDoctorAccreditation && $canManagemodule1;   // x11
        $m1type6 = $canPinVerify && !$canEligibility && $canDoctorAccreditation && $canManagemodule1;   // 1x1

        $stype1 = $m1typey || $m1type1 || $m1type4 || $m1type6 && $canManagemodule1; // all c1
        $stype2 = $m1typey || $m1type2 || $m1type4 || $m1type5 && $canManagemodule1; // all c2
        $stype3 = $m1typey || $m1type3 || $m1type5 || $m1type6 && $canManagemodule1; // all c3

        # Module 1 - uncheck
        $Xm1typex = !$canPinVerify && !$canEligibility && !$canDoctorAccreditation && !$canManagemodule1; // xxx
        $Xm1typey = $canPinVerify && $canEligibility && $canDoctorAccreditation && !$canManagemodule1;    // 111

        $Xm1type1 = $canPinVerify && !$canEligibility && !$canDoctorAccreditation && !$canManagemodule1;  // 1xx
        $Xm1type2 = !$canPinVerify && $canEligibility && !$canDoctorAccreditation && !$canManagemodule1;  // x1x
        $Xm1type3 = !$canPinVerify && !$canEligibility && $canDoctorAccreditation && !$canManagemodule1;  // xx1

        $Xm1type4 = $canPinVerify && $canEligibility && !$canDoctorAccreditation && !$canManagemodule1;   // 11x
        $Xm1type5 = !$canPinVerify && $canEligibility && $canDoctorAccreditation && !$canManagemodule1;   // x11
        $Xm1type6 = $canPinVerify && !$canEligibility && $canDoctorAccreditation && !$canManagemodule1;   // 1x1

        $Xstype1 = $m1typey || $m1type1 || $m1type4 || $m1type6 && !$canManagemodule1; // all c1
        $Xstype2 = $m1typey || $m1type2 || $m1type4 || $m1type5 && !$canManagemodule1; // all c2
        $Xstype3 = $m1typey || $m1type3 || $m1type5 || $m1type6 && !$canManagemodule1; // all c3

        # Merge all qualified access permission for pin verification
        $APpinVerification  = $m1typex || $m1typey || $stype1 || $Xm1typey || $Xstype1 || $Xm1type1;
        # Merge all qualified access permission for check eligibility
        $APcheckEligibility  = $m1typex || $m1typey || $stype2 || $Xm1typey || $Xstype2 || $Xm1type2;
        # Merge all qualified access permission for doctor accreditation
        $APdoctorAccreditation  = $m1typex || $m1typey || $stype3 || $Xm1typey || $Xstype3 || $Xm1type3;
        

        # Module 2 - check
        $m2typex = !$canTransmittal && !$canTransmitEclaims && $canManagemodule2;  // xx
        $m2typey = $canTransmittal && $canTransmitEclaims && $canManagemodule2;    // 11

        $m2type1 = $canTransmittal && !$canTransmitEclaims && $canManagemodule2;  // 1x
        $m2type2 = !$canTransmittal && $canTransmitEclaims && $canManagemodule2;  // x1

        $stype1M2 = $m2typey || $m2type1 && $canManagemodule2; // all c1
        $stype2M2 = $m2typey || $m2type2 && $canManagemodule2; // all c2

        # Module 2 - uncheck
        $Xm2typex = !$canTransmittal && !$canTransmitEclaims && !$canManagemodule2;  // xx
        $Xm2typey = $canTransmittal && $canTransmitEclaims && !$canManagemodule2;    // 11

        $Xm2type1 = $canTransmittal && !$canTransmitEclaims && !$canManagemodule2;  // 1x
        $Xm2type2 = !$canTransmittal && $canTransmitEclaims && !$canManagemodule2;  // x1

        $Xstype1M2 = $m2typey || $m2type1 && !$canManagemodule2; // all c1
        $Xstype2M2 = $m2typey || $m2type2 && !$canManagemodule2; // all c2

        # Merge all qualified access permission for process transmittal
        $APprocessTransaction = $m2typex || $m2typey || $stype1M2 || $Xm2typey || $Xstype1M2 || $Xm2type1 || $canBilling || $canTransmittalBilling;
        # Merge all qualified access permission for transmit eclaims
        $APtransmitEclaims = $m2typex || $m2typey || $stype2M2 || $Xm2typey || $Xstype2M2 || $Xm2type2;
       

        # Module 3 - check
        $m3typex = !$canCheckStatus && !$canGetVoucher && $canManagemodule3;  // xx
        $m3typey = $canCheckStatus && $canGetVoucher && $canManagemodule3;    // 11

        $m3type1 = $canCheckStatus && !$canGetVoucher && $canManagemodule3;  // 1x
        $m3type2 = !$canCheckStatus && $canGetVoucher && $canManagemodule3;  // x1

        $stype1M3 = $m3typey || $m3type1 && $canManagemodule3; // all c1
        $stype2M3 = $m3typey || $m3type2 && $canManagemodule3; // all c2

        # Module 3 - uncheck
        $Xm3typex = !$canCheckStatus && !$canGetVoucher && !$canManagemodule3;  // xx
        $Xm3typey = $canCheckStatus && $canGetVoucher && !$canManagemodule3;    // 11

        $Xm3type1 = $canCheckStatus && !$canGetVoucher && !$canManagemodule3;  // 1x
        $Xm3type2 = !$canCheckStatus && $canGetVoucher && !$canManagemodule3;  // x1

        $Xstype1M3 = $m3typey || $m3type1 && !$canManagemodule3; // all c1
        $Xstype2M3 = $m3typey || $m3type2 && !$canManagemodule3; // all c2

        # Merge all qualified access permission for check status
        $APcheckStatus = $m3typex || $m3typey || $stype1M3 || $Xm3typey || $Xstype1M3 || $Xm3type1;
        # Merge all qualified access permission for get voucher
        $APgetVoucher = $m3typex || $m3typey || $stype2M3 || $Xm3typey || $Xstype2M3 || $Xm3type2;
    
        # -- End of P.C.A.P. Algorithm -- #

        $controller->breadcrumbs['eClaims'] = array('main/index');
        \Yii::app()->getClientScript()->registerScript('eclaims', <<<SCRIPT
SCRIPT
        , \CClientScript::POS_END);

        $controller->menu['eclaims-config'] = array(
            'class' => 'bootstrap.widgets.TbMenu',
            'items' => array(
                array(
                    'label' => 'Eligibility',
                    'icon' => 'fa fa-check-circle-o',
                    'items' => array(

                        ($canManageAll || $canManagemoduleAll || $APpinVerification ?
                        array('label' => 'PIN verification', 'icon' => 'fa fa-list-ol', 'url' => \Yii::app()->createUrl('eclaims/member/getPin')) : array('label' => 'PIN verification', 'icon' => 'fa fa-list-ol', 'url' => '', 'disabled' => true)
                        ),

                        ($canManageAll || $canManagemoduleAll || $APcheckEligibility ?
                        array('label' => 'Check eligibility', 'icon' => 'fa fa-check-square-o', 'url' => \Yii::app()->createUrl('eclaims/eligibility/index')) : array('label' => 'Check eligibility', 'icon' => 'fa fa-check-square-o', 'url' => '', 'disabled' => true)
                        ),

                        ($canManageAll || $canManagemoduleAll || $APdoctorAccreditation ?
                        array('label' => 'Doctor accreditation', 'icon' => 'fa fa-user-md', 'url' => \Yii::app()->createUrl('eclaims/doctorAccreditation/index')) : array('label' => 'Doctor accreditation', 'icon' => 'fa fa-user-md', 'url' => '', 'disabled' => true)
                        ),
                    )
                ),
                array(
                    'label' => 'Submit Claims',
                    'icon' => 'fa fa-envelope',
                    'items' => array(

                        ($canManageAll || $canManagemoduleAll ||  $APprocessTransaction?
                        array('label' => 'Process Transmittal', 'icon' => 'fa fa-inbox', 
                        'url' => \Yii::app()->request->baseUrl."/modules/billing/bill-pass.php?ntid=false&lang=en&userck=&target=seg_billing_transmittal_eclaims",
                        'linkOptions'=>array(
                            'id'=>'process_t'
                        )) : array('label' => 'Process Transmittal', 'icon' => 'fa fa-inbox', 'url' => '', 'disabled' => true,
                            'linkOptions'=>array(
                                'id'=>'process_t',
                            ))),

                        ($canManageAll || $canManagemoduleAll || $APtransmitEclaims ?
                        array('label' => 'Transmit e-Claim', 'icon' => 'fa fa-send-o', 'url' => \Yii::app()->createUrl('eclaims/transmittal/index')) : array('label' => 'Transmit e-Claim', 'icon' => 'fa fa-send-o', 'url' => '', 'disabled' => true)),
                    ),
                ),
                array(
                    'label' => 'Claims Status',
                    'icon' => 'fa fa-check-square',
                    'items' => array(

                        ($canManageAll || $canManagemoduleAll || $APcheckStatus ?
                        array('label' => 'Check status', 'icon' => 'fa fa-flag', 'url' => \Yii::app()->createUrl('eclaims/claimStatus/index')) : array('label' => 'Check status', 'icon' => 'fa fa-flag', 'url' => '', 'disabled' => true)
                        ),

                        ($canManageAll || $canManagemoduleAll || $APgetVoucher ?
                        array('label' => 'Get voucher', 'icon' => 'fa fa-envelope-o', 'url' => \Yii::app()->createUrl('eclaims/claimVoucher/index')) : array('label' => 'Get voucher', 'icon' => 'fa fa-envelope-o', 'url' => '', 'disabled' => true)
                        ),
                    ),
                ),
                '---',
                array(
                    'label' => false,
                    'url' => array('config/update'),
                    'icon' => 'fa fa-cog',
                    'linkOptions' => array(
                        'id' => 'eclaims-config-button',
                        'data-toggle' => 'modal',
                        'data-target' => '#eclaims-config-modal'
                    )
                ),
                '---',
            ),
            'htmlOptions' => array(
                'id' => 'eclaims-config',
                'class' => 'pull-right',
            ),
        );

        return parent::beforeControllerAction($controller, $action);
    }

}
