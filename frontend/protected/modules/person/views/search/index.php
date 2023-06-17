<?php
/* @var $this SearchController */
/* @var $clientScript CClientScript */
/* @var $model \SegHis\modules\person\models\Person */
$baseUrl = Yii::app()->baseUrl;
$clientScript = Yii::app()->clientScript;
$clientScript->registerCss('headCss', <<<CSS
body{padding-top: 0;}
.form-inline .form-group{display: inline-block;}
.patient-encounter-list{float: right;}
.case-number-link{cursor: pointer;}
CSS
);

$clientScript->registerScript('headJs', <<<JS

function search() {
    $('#people-grid-view').yiiGridView('update', {
        data: $('#form-search').serialize()
    });
}

function initCaseNumberClick(){
    $('.case-number-link').on('click', function(e){
        e.preventDefault();
        var _this = $(this);
        $.getJSON('{$baseUrl}/index.php?r=person/search/caseInformation/hrn/'+_this.data('hrn')+'/caseNumber/'+_this.data('case_number'),
        {},
        function(response){
            window.parent.loadPerson(response);
            window.parent.checkExcludedArea();
        });
    });
}

function initCollapse() {
    var collapsibleElements = $(".collapse");
    collapsibleElements.on('shown.bs.collapse', function(){
        var _this = $(this);
        var hrn = _this.data('hrn');
        var list = $('.encounter_list_' + hrn);
        
        var get_URL = window.location.href;
        var url = new URL(get_URL);
        var isPdpu = url.searchParams.get("pdpup");
        
        var template = '<tr>' +
            '<td>{{{case_nr}}}</td>' +
            '<td>{{case_date}}</td>' +
            '<td style="width: 140px;">{{department}}</td>' +
            '<td>{{case_type}}</td>' +
        '</tr>';
        $.getJSON('{$baseUrl}/index.php?r=person/search/caseNumbers/pid/' + _this.data('hrn') +'/pdpup/'+isPdpu,{},function(response){
            $('#loading_'+hrn).hide();
            if(response.length > 0) {
                var table = list.find('#encounter_table_' + hrn);
                for(var i=0; i < response.length; i++) {
                            table.find('tbody').append(Mustache.render(template, {
                                case_nr: '<a class="case-number-link" data-hrn="'+response[i].pid+'" data-case_number="'+response[i].encounter_nr+'">'+response[i].encounter_nr+'</a>',
                                case_date: response[i].encounter_date,
                                case_type: response[i].encounter_type,
                                department: response[i].department
                            }));
                    
                }
                initCaseNumberClick();
                list.show();
                table.show();
                console.log(table);
            }
        });
    });
    collapsibleElements.on('show.bs.collapse', function(){
        var _this = $(this);
        var hrn = _this.data('hrn');
        var list = $('.encounter_list_' + hrn);
        var table = list.find('#encounter_table_' + hrn);
        table.find('tbody').empty();
        table.hide();
        $('#loading_'+hrn).show();
    });
}
JS
    , CClientScript::POS_HEAD);

$clientScript->registerScript('loadJs', <<<JS

$('#form-search').on('submit', function(e){
    
    e.preventDefault();
    var hrn = $('#search-hrn').val();
    var caseNum = $('#search-case-number').val();

    if(hrn.length >= 2 || caseNum.length >= 4){
        search();
    }
});
initCollapse();
initCaseNumberClick();
JS
    , CClientScript::POS_LOAD);

$this->setPageTitle('Search Person');

?>
    <form id="form-search" class="form-inline">
        <div class="form-group">
            <?php
                if($_GET['bloodbank'] == 1){
            ?>        
                    <label for="search-name-hrn">HRN</label>
                    <input type="text" name="search-name-hrn" class="form-control" id="search-hrn" placeholder="HRN">
            <?php
                }else if($_GET['pdpup'] == 1){
            ?>
                    <label for="search-name-hrn">HRN / Name</label>
                    <input type="text" name="search-name-hrn" class="form-control" id="search-hrn" placeholder="HRN">
            <?php
                }
                else{
            ?>
                    <label for="search-name-hrn">HRN / Name</label>
                    <input type="text" name="search-name-hrn" class="form-control" id="search-hrn" placeholder="HRN / Name">
            <?php
                }
            ?>
        </div>
        <div class="form-group">
            <label for="search-case-number">Case #</label>
            <input type="text" name="search-case-number" class="form-control" id="search-case-number"
                   placeholder="Case Number">
        </div>
        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Search</button>
    </form>
<?php

$this->widget('person.widgets.PersonCustomGridView', array(
    'afterAjaxUpdate' => 'function(id,data){
        initCollapse();
        initCaseNumberClick();
    }',
    'id' => 'people-grid-view',
    'enableSorting' => false,
    'dataProvider' => $model->search(),
    'columns' => array(
        'pid',
        'fullName',
        array(
            'header' => 'Case #',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) {
                /* @var $data \SegHis\modules\person\models\Person */
                if ($data->activeEncounter) {
                    return CHtml::link($data->activeEncounter->encounter_nr, '#', array(
                        'class' => 'case-number-link',
                        'data-case_number' => $data->activeEncounter->encounter_nr,
                        'data-hrn' => $data->pid
                    ));
                } else {
                    return '';
                }
            }
        ),
        array(
            'header' => 'Case Date',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) {
                /* @var $data \SegHis\modules\person\models\Person */
                if (!$data->activeEncounter) {
                    return 'Walk-in';
                } else {
                    return date('F j, Y', strtotime($data->activeEncounter->encounter_date));
                }
            }
        ),
        array(
            'header' => 'Department',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) {
                /* @var $data \SegHis\modules\person\models\Person */
                if (!$data->activeEncounter) {
                    return 'Walk-in';
                } else {
                    return $data->activeEncounter->dept->name_formal;
                }
            }
        ),
        array(
            'header' => 'Confinement Type',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) {
                /* @var $data \SegHis\modules\person\models\Person */
                if ($data->activeEncounter) {
                    return $data->activeEncounter->getEncounterTypeDescription();
                } else {
                    return 'Walk-in';
                }
            }
        ),
        array(
            'header' => 'Sex',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) {
                /* @var $data \SegHis\modules\person\models\Person */
                if ($data->sex == 'f')
                    return '<i class="fa fa-female fa-lg" style="color:#ff1493;"></i>';
                else
                    return '<i class="fa fa-male fa-lg" style="color:#0000cd;"></i>';
            }
        ),
        array(
            'header' => 'options',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) {
                if (!$data->activeEncounter) {
                    /* @var $data \SegHis\modules\person\models\Person */
                    return CHtml::link($data->pid, '#', array(
                        'class' => 'case-number-link',
                        'data-case_number' => $data->activeEncounter->encounter_nr,
                        'data-hrn' => $data->pid
                    ));
                } else {
                    /* @var $data \SegHis\modules\person\models\Person */
                    return CHtml::link('<i class="fa fa-bars fa-lg"></i>', '#', array(
                        'data-toggle' => 'collapse',
                        'data-target' => '.encounter_list_' . $data->pid
                    ));
                }
            }
        )
    )
));