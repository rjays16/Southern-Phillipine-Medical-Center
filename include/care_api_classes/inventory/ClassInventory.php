<?php

require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');

/**
 * Class that handles DA inventory integration
 * @author Justin Tan
 */
class DaInventory extends Core
{
    var $sql;

    function getAPIKeyByArea($area)
    {
        global $db;

        $this->sql = "SELECT inv_api_key FROM seg_pharma_areas WHERE area_code = ".$db->qstr($area);
        $row = $db->GetRow($this->sql);

        return $row;
    }
}