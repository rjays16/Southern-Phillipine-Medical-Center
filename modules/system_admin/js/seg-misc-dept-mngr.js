/**
 * Created by Nick 07-01-2014
 */
var selected_code="";

function preset() {
//    $j('#deptContainer').hide();
    getMiscItems();
    getDepts();
}

function getMiscItems() {
    ListGen.create($('misc_container'), {
        id: 'misc_items',
        url: 'ajax/ajax-seg-misc-dept-mngr.php',
        params: {
            'searchKey': $('miscSearchKey').value,
            'mode':'misc'
        },
        width: "100%",
        height: "100%",
        autoLoad: true,
        columnModel: [
            {
                name: 'code',
                label: 'Code',
                width: "20%",
                sorting: ListGen.SORTING.asc,
                sortable: true
            },
            {
                name: 'name',
                label: 'Name/Description',
                width: "50%",
                sorting: ListGen.SORTING.asc,
                sortable: true
            },
            {
                name: 'price',
                label: 'Price',
                width: "10%",
                sorting: ListGen.SORTING.asc,
                sortable: true
            },
            {
                name: 'action',
                label: 'Action',
                width: "20%",
                sortable: false
            }
        ]
    });
}

function getDepts() {
    ListGen.create($('dept_container'), {
        id: 'dept_items',
        url: 'ajax/ajax-seg-misc-dept-mngr.php',
        params: {
            'mode': 'dept',
            'searchKey': $('deptSearchKey').value,
            'code':''
        },
        width: 500,
        height: "100%",
        autoLoad: true,
        columnModel: [
            {
                name: 'department',
                label: 'Department',
                width: 300,
                sorting: ListGen.SORTING.asc,
                sortable: true
            },
            {
                name: 'action',
                label: 'Actions',
                width: 200,
                sortable: false
            }
        ]
    });
}

function miscSearch() {
    $('misc_container').list.params = {
        'mode': 'misc',
        'searchKey': $('miscSearchKey').value
    };
    $('misc_container').list.refresh();
}

function deptSearch(){
    $('dept_container').list.params = {
        'mode': 'dept',
        'searchKey': $('deptSearchKey').value,
        'code':selected_code,
        'added_depts':0
    };
    $('dept_container').list.refresh();
}

function getAddedDepts(){
    $('dept_container').list.params = {
        'mode': 'dept',
        'searchKey': $('deptSearchKey').value,
        'code':selected_code,
        'added_depts':1
    };
    $('dept_container').list.refresh();
}

function addToDeptDialog(code,desc) {
    $j('#deptContainer').dialog({
        modal: true,
        title: "Add to department",
        width: "auto",
        height: "auto",
        open: function () {
            selected_code = code;
            $('selected_code').innerHTML = code;
            $('selected_description').innerHTML = desc;
            deptSearch();
        }
    });
}

function addToDept(code,dept_nr){
    $j.ajax({
        url:'ajax/ajax-seg-misc-dept-mngr.php',
        type:'post',
        dataType:'json',
        data:{
            'mode':'add',
            'code':selected_code,
            'dept_nr':dept_nr
        },
        success:function(data,textStatus,jqXHR ){
            if(data.result != true){
                alert("Failed to udpate data");
            }
        },
        error:function(jqXHR,textStatus,errorThrown){
            alert("ERROR: " + errorThrown);
        },
        complete:function(jqXHR,textStatus){
            deptSearch();
        }
    });
}

function deleteFromDept(code,dept_nr){
    $j.ajax({
        url:'ajax/ajax-seg-misc-dept-mngr.php',
        type:'post',
        dataType:'json',
        data:{
            'mode':'delete',
            'code':code,
            'dept_nr':dept_nr
        },
        success:function(data,textStatus,jqXHR){
            if(data.result != true){
                alert("Failed to udpate data");
            }
        },
        error:function(jqXHR,textStatus,errorThrown){
            alert("ERROR: " + errorThrown);
        },
        complete:function(jqXHR,textStatus){
            deptSearch();
        }
    });
}

function showHideDeptToClinic(isVisible,dept_nr){
    $j.ajax({
        url:'ajax/ajax-seg-misc-dept-mngr.php',
        type:'post',
        dataType:'json',
        data:{
            'mode':'showHideDept',
            'dept_nr':dept_nr,
            'isVisible':isVisible
        },
        success:function(data,textStatus,jqXHR){
            if(data.result != true){
                alert("Failed to udpate data ");
            }
        },
        error:function(jqXHR,textStatus,errorThrown){
            alert("ERROR: " + errorThrown);
        },
        complete:function(jqXHR,textStatus){
            deptSearch();
        }
    });
}