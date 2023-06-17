<?php
/**
 * Created by Nick 07-01-2014
 */
require('./roots.php');
require $root_path . "classes/json/json.php";
require($root_path . 'include/inc_environment_global.php');

$searchKey = $_REQUEST['searchKey'];
$page = (int)$_REQUEST['page'];
$maxRows = (int)$_REQUEST['mr'];
$offset = ($page - 1) * $maxRows;
$sortDir = $_REQUEST['dir'] == '1' ? 'ASC' : 'DESC';
$sortName = $_REQUEST['sort'];

$params = array(
    'searchKey' => $searchKey,
    'page' => $page,
    'maxRows' => $maxRows,
    'offset' => $offset,
    'sortDir' => $sortDir,
    'sortName' => $sortName
);

switch($_REQUEST['mode']){
    case 'misc':
        getMiscs($params);
    break;
    case 'dept':
        getDepts($params);
    break;
    case 'add':
        addToDept();
    break;
    case 'delete':
        deleteFromDept();
    break;
    case 'showHideDept':
        showHideDeptToClinic();
    break;
}

function getDepts($params)
{
    global $db;
    extract($params);
    $code = $_REQUEST['code'];
    $data = array();

    if($_REQUEST['added_depts'] == 1){
        $join_type = 'INNER';
    }else{
        $join_type = 'LEFT';
    }

    $sql = $db->Prepare("SELECT
                              SQL_CALC_FOUND_ROWS cd.nr,
                              smd.service_code AS code,
                              cd.nr AS dept_nr,
                              IF(COALESCE(smd.dept_nr, '') = '', 0, 1) AS is_marked,
                              cd.name_formal AS department,
                              cd.clinic_visibility
                            FROM
                              care_department AS cd
                              $join_type JOIN seg_misc_depts smd
                                ON cd.nr = smd.dept_nr
                                AND smd.service_code = ?
                              LEFT JOIN seg_other_services sos
                                ON smd.service_code = sos.service_code
                            WHERE cd.is_inactive = 0");

    if(trim($searchKey) != ""){
        $where = " AND cd.name_formal LIKE ?";
        $values = array(
            $code,
            "%$searchKey%"
        );
    }else{
        $values = array(
            $code
        );
    }

    $order = ($sortName == "department") ? " ORDER BY cd.name_formal " . $sortDir : "";
    $limit = " LIMIT $offset,$maxRows";
    $sql .= $where.$order.$limit;
    $rs = $db->Execute($sql,$values);

    if($rs){
        if($rs->RecordCount()){
            $total = $db->GetOne("SELECT FOUND_ROWS()");
            while($row = $rs->FetchRow()){
                if($row['is_marked'] == 0){
                    $chkButton = '<button title="Add item to this department" class="segButton" onclick="addToDept(\''.$row['code'].'\',\''.$row['dept_nr'].'\')" style="cursor: pointer">

                                    Add
                                  </button>';
                }else{
                    $chkButton = '<button title="Remove item to this department" class="segButton" onclick="deleteFromDept(\''.$row['code'].'\',\''.$row['dept_nr'].'\')" style="cursor: pointer">
                                    <img src="../../images/btn_delitem.gif"/>
                                    Remove
                                  </button>';
                }
                if($row['clinic_visibility'] == 0){
                    $btnClinicVisibility = '<button title="Show department to clinic" class="segButton" onclick="showHideDeptToClinic(\''.$row['clinic_visibility'].'\',\''.$row['dept_nr'].'\')" style="cursor: pointer">
                                                Show
                                            </button>';
                }else{
                    $btnClinicVisibility = '<button title="Hide department from clinic" class="segButton" onclick="showHideDeptToClinic(\''.$row['clinic_visibility'].'\',\''.$row['dept_nr'].'\')" style="cursor: pointer">
                                                <img src="../../images/btn_delitem.gif"/>
                                                Hide
                                            </button>';
                }

                $data[] = array(
                    'department' => $row['department'],
                    'action' => $chkButton.$btnClinicVisibility
                );
            }
        }else{
            $data = array();
        }
    }else{
        $data = array();
    }

    $response = array(
        'currentPage' => $page,
        'total' => $total,
        'data' => $data
    );

    $json = new Services_JSON;
    print $json->encode($response);
}

function getMiscs($params){
    global $db;
    extract($params);
    $where = "";
    $params = array();

    $sql = $db->Prepare("SELECT
                              SQL_CALC_FOUND_ROWS s.name,
                              s.name_short,
                              s.price,
                              s.service_code AS code,
                              s.alt_service_code AS alt_code,
                              s.description,
                              t.name_long AS type_name,
                              p.name_long AS ptype_name,
                              s.account_type,
                              s.is_not_socialized,
                              d.name_formal AS dept_name
                            FROM
                              seg_other_services AS s
                              LEFT JOIN seg_cashier_account_subtypes AS t
                                ON s.account_type = t.type_id
                              LEFT JOIN seg_cashier_account_types AS p
                                ON t.parent_type = p.type_id
                              LEFT JOIN care_department AS d
                                ON d.nr = s.dept_nr");

    if (trim($searchKey) != "") {
        if (is_numeric($searchKey)) {
            $where = " WHERE s.service_code=? AND s.lockflag = 0";
            $params = array(
                $searchKey
            );
        } else {
            $where = " WHERE s.name_short LIKE ? OR s.name LIKE ? AND s.lockflag = 0";
            $params = array(
                utf8_decode("%$searchKey%"),
                utf8_decode("%$searchKey%")
            );
        }
    }else{
        $where = " WHERE s.lockflag = 0";
    }

    $order = " ORDER BY $sortName $sortDir";
    $limit = " LIMIT $offset,$maxRows";
    $sql .= $where . $order . $limit;
    $rs = $db->Execute($sql, $params);

    if ($rs) {
        if ($rs->RecordCount()) {
            $total = $db->GetOne("SELECT FOUND_ROWS()");
            while ($row = $rs->FetchRow()) {
                $btnUpdate = '<img onclick="addToDeptDialog(\'' . $row['code'] . '\',\''. preg_replace('/[\r\n]+/', " ",$row['name']) .'\')" src="../../images/btn_edit.gif" style="cursor:pointer;" />';
                $data[] = array(
                    'code' => $row['code'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'action' => $btnUpdate
                );
            }
        } else {
            $data = array();
        }
    } else {
        $data = array();
    }

    $response = array(
        'currentPage' => $page,
        'total' => $total,
        'data' => $data
    );

    $json = new Services_JSON;
    print $json->encode($response);
}

function addToDept(){
    global $db;
    $fields = array(
        'dept_nr' => $_POST['dept_nr'],
        'service_code' => $db->qstr($_POST['code'])
    );
    $pk = array(
        'dept_nr',
        'service_code'
    );
    $rs = $db->Replace('seg_misc_depts',$fields,$pk);
    if($rs){
        $response = array('result'=>true);
    }else{
        $response = array('result'=>false);
    }
    echo  json_encode($response);
}

function deleteFromDept(){
    global $db;
    $sql = "DELETE FROM seg_misc_depts WHERE service_code = ? AND dept_nr = ?";
    $rs = $db->Execute($sql,array(
        $_POST['code'],
        $_POST['dept_nr']
    ));
    if($rs){
        $response = array('result'=>true);
    }else{
        $response = array('result'=>false);
    }
    echo json_encode($response);
}

function showHideDeptToClinic(){
    global $db;
    $sql = "UPDATE care_department SET clinic_visibility = ? WHERE nr = ?";
    $rs = $db->Execute($sql,array(
        ($_POST['isVisible']) ? '0' : '1',
        $_POST['dept_nr']
    ));
    if($rs){
        $response = array('result'=>true);
    }else{
        $response = array('result'=>false);
    }
    echo json_encode($response);
}