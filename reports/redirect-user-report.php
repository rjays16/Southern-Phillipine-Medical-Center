<?php

    include_once('./roots.php');
    include_once($root_path.'include/inc_environment_global.php');
    include_once($root_path . 'include/care_api_classes/class_user_token.php');
    $user_token = new UserToken;
    $auth = $user_token->repUserLogin();
?>