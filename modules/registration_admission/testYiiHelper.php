<?php

require('./roots.php');

include_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path."frontend/protected/components/YiiCallInit.php");
require_once($root_path."frontend/protected/components/Biometric.php");


$pid = '1157027';
$pname = Biometric::getFingerBiometric($pid, '');
echo "Person is ".$pname;
