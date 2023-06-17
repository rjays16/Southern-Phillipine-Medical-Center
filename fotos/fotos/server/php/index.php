<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
#added by VAN 11-03-2012
require('./roots.php');
require($root_path.'classes/adodb/adodb.inc.php');
include($root_path.'include/inc_init_main.php');

#edited by VAN 10-29-2012
#pass a parameter for encounter_nr
$option = null;
if (isset($_REQUEST['encounter_nr'])) {
   if (substr($_REQUEST['encounter_nr'],-1)!= '/') {
      $_REQUEST['encounter_nr_dir'] = $_REQUEST['encounter_nr'].'/';
   }
   $dir = dirname($_SERVER['SCRIPT_FILENAME']).'/files/'.$_REQUEST['encounter_nr_dir'];
   $dir_thumb = dirname($_SERVER['SCRIPT_FILENAME']).'/thumbnails/'.$_REQUEST['encounter_nr_dir'];
   if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
      mkdir($dir_thumb, 0777, true);
   }
   $option = array('upload_dir' => $dir);
}
#--------------------------

#added by VAN 11-03-2012
$bConnected = 0;
#mysql connection settings
$dbcon = &ADONewConnection("$dbtype");
$bConnected = $dbcon->PConnect($dbhost, $dbusername, $dbpassword, $dbname);

require('upload.class.php');

#$upload_handler = new UploadHandler();
$upload_handler = new UploadHandler($option);
