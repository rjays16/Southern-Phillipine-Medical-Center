<?php
/**
 * Created by Nick 06-28-2014
 * Download Claims Archive file from a temporary folder
 * then delete the temporary folders/archives generated
 * in billing-transmittal.php
 */
extract($_GET);
require('./roots.php');

$tmp = $root_path."include/care_api_classes/eTransmittal/tmp/";
$archive = $tmp.$filename;
$folder = substr($archive,0,strlen($archive)-4);

$file_name = basename($archive);
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=$file_name");
header("Content-Length: " . filesize($archive));
readfile($archive);



unlink($archive);
deleteDir($folder);

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}