<?php
include($root_path.'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_temp_userid']);

$allAccess = $objAcl->checkPermissionRaw(array('_a_0_all', 'System_Admin'));
$addTransmittal = $objAcl->checkPermissionRaw(array('_a_2_billtransmittal_add_claims'));
$viewTransmittal = $objAcl->checkPermissionRaw(array('_a_2_billtransmittal_view_claims'));
$updateTransmittal = $objAcl->checkPermissionRaw(array('_a_2_billtransmittal_udpate_claims'));
$deleteTransmittal = $objAcl->checkPermissionRaw(array('_a_2_billtransmittal_delete_claims'));
$billingTransmittalAccess = $objAcl->checkPermissionRaw(array('_a_1_billtransmittal'));

$cashcreditcollection = $objAcl->checkPermissionRaw(array('_a_1_creditCollectionCash'));
$accountbudgetallocation = $objAcl->checkPermissionRaw(array('_a_1_accountBudgetAllocation'));

$bilingTransmittalParentOnly = ($billingTransmittalAccess && !($addTransmittal || $viewTransmittal || $updateTransmittal || $deleteTransmittal));

$childOnly = (!$billingTransmittalAccess && ($addTransmittal && $viewTransmittal && $updateTransmittal && $deleteTransmittal));

$allTransmittal = ($billingTransmittalAccess && ($addTransmittal && $viewTransmittal && $updateTransmittal && $deleteTransmittal));

$canAddTransmittal = ($addTransmittal || $bilingTransmittalParentOnly || $allAccess);
$canUpdateTransmittal = ($updateTransmittal || $bilingTransmittalParentOnly || $allAccess);
$canDeleteTransmittal = ($deleteTransmittal || $bilingTransmittalParentOnly || $allAccess);
$canViewTransmittal = ($bilingTransmittalParentOnly || $viewTransmittal || $allAccess);

$canAccessCashCredit = ($cashcreditcollection || $allAccess);
$canAccessAccountBudget = ($accountbudgetallocation || $allAccess);

?>