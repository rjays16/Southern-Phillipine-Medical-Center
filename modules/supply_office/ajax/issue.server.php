<?php
#added by bryan on Sept 18,2008
#  
    function add_item($items, $item_names, $desc, $pending, $unitid, $perpc, $unitdesc, $expdate=NULL, $serial=NULL) {
        global $db;
        $objResponse = new xajaxResponse();

        # Later: Put this in a Class
        if (!is_array($items)) $items = array($items);
        if (!is_array($item_names)) $item_names = array($item_names);
        if (!is_array($desc)) $desc = array($desc);
        if (!is_array($pending)) $pending = array($pending);
        if (!is_array($unitid)) $unitid = array($unitid);
        if (!is_array($unitdesc)) $unitdesc = array($unitdesc);
        if (!is_array($perpc)) $perpc = array($perpc);
        if (!is_array($expdate)) $expdate = array($expdate);
        if (!is_array($serial)) $serial = array($serial);
        
        foreach ($items as $i=>$item) {
        
            #$objResponse->call("clearOrder",NULL);
        
            $obj = (object) 'details';
            $obj->id = $items[$i];
            $obj->name = $item_names[$i];
            $obj->desc= $desc[$i];
            $obj->pending = $pending[$i];
            $obj->unitid = $unitid[$i];
            $obj->unitdesc = $unitdesc[$i];
            $obj->perpc = $perpc[$i];
            $obj->expdate = $expdate[$i];
            $obj->serial = $serial[$i];
            
            $objResponse->call("appendOrder", NULL, $obj);

        }
        return $objResponse;
    }

    

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require($root_path.'include/care_api_classes/class_discount.php');
    require($root_path.'include/care_api_classes/class_order.php');
    require_once($root_path.'modules/supply_office/ajax/issue.common.php');
    $xajax->processRequest();
?>
