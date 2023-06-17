<?php

function populateBillsList($page_num = 0, $max_rows = 10, $sort_obj = NULL, $args = NULL) {
    
    global $db;
    $objResponse = new xajaxResponse();

    if (!$filters)
        $filters = array();

    $offset = $page_num * $max_rows;
    $sortColumns = array('date', 'source', 'refno', 'name', 'total', 'grant');
    $sort = array();
    if (is_array($sort_obj)) {
        foreach ($sort_obj as $i => $v) {
            if ($col = ($sortColumns[$i] ? $sortColumns[$i] : FALSE)) {
                if ((int) $v < 0)
                    $sort[] = "$col DESC";
                elseif ((int) $v > 0)
                    $sort[] = "$col ASC";
            }
        }
    }

    if ($sort)
        $sort_sql = implode(',', $sort);
    else
        $sort_sql = 'source ASC';

    list($filters['OFFSET'], $filters['ROWS'], $filters['SORT']) = array($offset, $max_rows, $sort_sql);
    $classDialysis = new SegDialysis();
    $billsList = $classDialysis->getBillDetails($args['bill_nr'], $args['patient'], $offset, $max_rows);
    if ($billsList !== false) {
        $i = 0;
        $data = array();
        $found_rows = $billsList->RecordCount();
        while ($row = $billsList->fetchRow()) {
            $data[$i]['amount'] = $row['amount'];
            $data[$i]['net_price'] = $row['amount'];
            $data[$i]['bill_nr'] = $row['bill_nr'];
            $data[$i]['bill_type'] = $row['bill_type'] == 'PH' ? 'Dialysis Pre-Bill PHIC' : 'Dialysis Pre-Bill NPHIC';
            $data[$i]['encounter_nr'] = $row['encounter_nr'];
            $data[$i]['request_date'] = $row['request_date'];
            $data[$i]['fullname'] = $row['fullname'];
            $data[$i]['status'] = $row['request_flag'];
            $data[$i]['discountid'] = $row['discountid'];
            $data[$i++]['pid'] = $row['pid'];
        }

        $last_page = ceil($found_rows / $max_rows) - 1;
        if ($page_num > $last_page)
            $page_num = $last_page;

        $objResponse->contextAssign('currentPage', $page_num);
        $objResponse->contextAssign('lastPage', $last_page);
        $objResponse->contextAssign('maxRows', $max_rows);
        $objResponse->contextAssign('listSize', $found_rows);

        if ($found_rows) {
            $objResponse->contextAssign('dataSize', $found_rows);
            $objResponse->contextAssign('listData', $data);
        } else {
            $objResponse->contextAssign('dataSize', 0);
            $objResponse->contextAssign('listData', NULL);
        }
    } else {
        // error
        $objResponse->alert("An error has occurred! Please contact your system administrator!");
        $objResponse->contextAssign('dataSize', -1);
        $objResponse->contextAssign('listData', NULL);
    }

    $objResponse->script('this.fetchDone()');
    return $objResponse;
}

function getBillNrDetails($billNr, $pid) {
    $xajax = new xajaxResponse();
    $classDialysis = new SegDialysis();
    $billsList = $classDialysis->getBillDetails($billNr, $pid);
    if ($billsList) {
        $rows = $billsList->getRows();
    }
    echo json_encode($row);
}

require('./roots.php');
include_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require($root_path . 'include/inc_environment_global.php');
require($root_path . 'include/care_api_classes/dialysis/class_dialysis.php');
require($root_path . "modules/dialysis/ajax/dialysis-lingap.common.php");
$xajax->processRequest();