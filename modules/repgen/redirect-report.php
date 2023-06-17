<?php
if (class_exists('Config')){
    $report_portal = "";
    $REP_PORTAL_TXT = "report_portal";
    $REP_REDIRECT_TXT = "redirect_report";
    $connect_to_instance = Config::get($REP_REDIRECT_TXT)->value;
    if ($connect_to_instance){
        $PUBLIC_IP_VAR = "spmc_public_ip";
        $PUBLIC_IP_FORWARD_PORT = 'rep_public_ip_port_forward';
        $server_host = $_SERVER['HTTP_HOST'];
        $public_ip = Config::get($PUBLIC_IP_VAR)->value;
        $public_forward_ip = Config::get($PUBLIC_IP_FORWARD_PORT)->value;
        $his_public_ip = explode(',',$public_ip);
        $report_portal = "";

        foreach ($his_public_ip as $key=>$value){
            if ($server_host == $value){
                $report_portal = $public_forward_ip;
                break;
            }
        }

        if (!$report_portal){
            $report_portal = Config::get('report_portal')->value;
        }

        $personnel_nr = $HTTP_SESSION_VARS['sess_user_personell_nr'];
        $pToken = $HTTP_SESSION_VARS['sess_access_ptoken'];
    }
}

