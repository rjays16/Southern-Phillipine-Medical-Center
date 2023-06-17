<?php
include($root_path.'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_temp_userid']);

$allAccess = $objAcl->checkPermissionRaw(array('_a_0_all', 'System_Admin'));
$noticeManager = $objAcl->checkPermissionRaw(array('_a_1_notice_manager'));
$createManager = $objAcl->checkPermissionRaw(array('_a_2_notice_manager_view_manager'));
$listManager = $objAcl->checkPermissionRaw(array('_a_2_notice_manager_view_manager'));
$editManager = $objAcl->checkPermissionRaw(array('_a_2_notice_manager_edit'));
$deleteManager = $objAcl->checkPermissionRaw(array('_a_2_notice_manager_delete'));
$viewManager = $objAcl->checkPermissionRaw(array('_a_2_notice_manager_view_manager'));
// $viewOrientation = $objAcl->checkPermissionRaw(array('_a_2_notice_manager_view_orientation'));

$noticeParentOnly = ($noticeManager && !($createManager || $listManager || $editManager || $deleteManager || $viewMeeting || $viewOrientation));

$childOnly = ($createManager || $listManager || $editManager || $deleteManager || $viewMeeting || $viewOrientation);

$ParentChildCreate = ($noticeManager && $createManager );
$ParentChildView = ($noticeManager && $viewManager );
$ParentChildEdit = ($noticeManager && $editManager );
$ParentChildDelete = ($noticeManager && $deleteManager );

$allTransmittal = ($noticeManager && ($createManager && $listManager && $editManager && $deleteManager && $viewMeeting ));

$cancreateManager = ($createManager || $noticeParentOnly || $allAccess || $allTransmittal || $ParentChildCreate);
// $canlistManager = ($listManager || $noticeParentOnly || $allAccess || $allTransmittal);
$caneditManager = ($editManager || $noticeParentOnly || $allAccess || $allTransmittal || $ParentChildEdit);
$candeleteManager = ($noticeParentOnly || $deleteManager || $allAccess || $allTransmittal || $ParentChildDelete);
$canview = ($viewManager || $noticeParentOnly || $allAccess || $allTransmittal || $ParentChildView);

?>