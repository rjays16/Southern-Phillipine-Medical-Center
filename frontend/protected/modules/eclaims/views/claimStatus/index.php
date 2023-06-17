<?php

/**
 * Main view of Claim Status function where all the claims are listed
 * and that can also be filtered
 *
 * @author        Mary Joy L. Abuyo
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

$this->setpageTitle('Check Claim Status');

// var_dump($claim->search());die;

$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;


Yii::app()->clientScript->registerCss('_claimStatus-css', <<<CSS
    td > p.case-rate-p {
        margin-bottom: 0;
    }
    td > hr.case-rate-hr {
        margin: 3px 0;
        border-top-color: #BEBEBE;
    }
    .searchI{
            font-size: 25px;
            width: 60%;
          border: 1px solid #b7b7b7;

    }
    .searchStatus{
        width: 63%
    }
    .grid-view-loading{
    background-position: center bottom;
    background-color: #f9f9f9;
}

    /* Important part */
    .modal-dialog{
        overflow-y: initial !important
    }
    .modal-body{
        height: 500px;
        overflow-y: auto;
    }
CSS
);

Yii::app()->clientScript->registerScript('re-install-date-picker', "
    function reinstallDatePicker1(id, data) {
        reinstallDatePicker2();
        $('#discharge_date').datepicker({
            'format':'yyyy-mm-dd'
        });
    }

    function reinstallDatePicker2(id, data) {
        $('#admission_dt').datepicker({
            'format':'yyyy-mm-dd'
        });
    }
");

Yii::app()->clientScript->registerScript('search', "
    $('.search-form form').submit(function(){
        $('#claim').yiiGridView('select', {
            data: $(this).serialize()
        });
    return false;
    });
");
/*added by MARK April 21, 2017*/
$js
    = <<<JAVASCRIPT
jQuery(document).ready(function(){
   jQuery('#custom-search-data').click(function(e) {
     e.preventDefault();
      send();
    })
  
   function send(){
     jQuery('#myModal').modal('hide');
     var datas=jQuery("#seg-search-eclaims-form").serialize();
      console.log(datas);   
              $.ajax({
               type: 'GET',
                url: 'index.php?r=eclaims/claimStatus/index/SearchNew',
               data:datas,
               beforeSend: function() {
                    Alerts.loading({
                        'title': 'Please wait',
                        content: 'Searching Check Claim Status...'
                    });
                },
                success:function(data){
                            console.log(data);   
                            window.location.href ="index.php?r=eclaims/claimStatus/index&search="+"true"+"&"+datas;
                          },
               error: function(data){
                     alert("Error occured.please try again");
                },
                complete: function() {
                       Alerts.close();
                },
             
              dataType:'html'
              });


   }
   var GetData = jQuery('#ifSearch').val();
    if (GetData ==""){
        jQuery('#button-back').hide();
    }
});

 function getOtherDisable(id){
     var ids = jQuery(id).attr("id");
        jQuery('.searchI').each(function() {
            if (this.id == ids) {
                jQuery("#"+ids).prop('readonly', false);
                jQuery("#"+ids).attr("placeholder","Search "+ids);
            }
            else{
                jQuery("#"+this.id).prop('readonly', true);  
                jQuery("#"+this.id).val('');  
                jQuery("#"+this.id).attr("placeholder","Click me to search "+this.id);  
            }
        });
 }
 function OnBlurData(){
     jQuery('.searchI').each(function() {
        jQuery("#"+this.id).prop('readonly', false);
      });

 }
 /*END added by MARK April 21, 2017*/
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);


$this->beginWidget(
    'bootstrap.widgets.TbBox', array(
        'title'         => 'List of Claims',
        'headerIcon'    => 'icon-th-list',
        'headerButtons' => array(
            array(
                'class'       => 'bootstrap.widgets.TbButton',
                'label'       => 'Search',
                'type'        => 'primary',
                'icon'        => 'fa fa-search',
                'url'         => '',
                'htmlOptions' => array(
                    'data-toggle' => 'modal',
                    'data-target' => '#myModal',
                ),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButton',
                'label' => 'Back',
                'type'  => 'success',
                'icon'  => 'fa fa-arrow-left',
                'url'   => 'index.php?r=eclaims/claimStatus/index',
                'id'    => 'button-back',
            ),

        ),
    )
);

$this->widget(
    'bootstrap.widgets.TbExtendedGridView', array(
        'id'              => 'claim',
        'type'            => 'striped bordered condensed',
        'dataProvider'    => !empty($_GET['search']) ? $claim->searchNews()
            : $claim->search(),
        // 'filter'=>$claim,
        'fixedHeader'     => true,
        'afterAjaxUpdate' => 'reinstallDatePicker1',
        'columns'         => array(
            array(
                'name'        => 'transmit_no',
                'header'      => 'Transmittal No',
                'htmlOptions' => array('width' => '10%'),


            ),

            array(
                'name'   => 'transmit_dte',
                'type'   => 'datetime',
                'header' => 'Transmittal Date',


            ),
            array(
                'name'        => 'encounter_nr',
                'header'      => 'Encounter No',
                'htmlOptions' => array('width' => '10%'),

            ),
            array(
                'name'        => 'claim_series_lhio',
                'header'      => 'Claim Series Lhio',
                'htmlOptions' => array('width' => '10%'),

            ),
            array(
                'header' => 'Patient',
                'name'   => 'name_lasted',
                'value'  => function ($data, $row) {
                    $person = $data['sex'];
                    switch (strtolower($person)) {
                        case 'm':
                            $icon = '<i class="color-blue fa fa-male"></i>';
                            break;
                        case 'f':
                            $icon = '<i class="color-pink fa fa-female"></i>';
                            break;
                        default:
                            $icon = '';
                    }

                    return $data["name_lasted"].
                        " {$icon} <br/>".
                        $data["typ_enc"];
                },
                'type'   => 'raw',
                'filter' => CHtml::activeTextField(
                    $claim->getRelatedModel('person'),
                    'name_last',
                    array("placeholder" => "Enter Last Name")
                ),
            ),
            /* @todo: Fix Filter admission_dt to getAdmissionDt() */
            array(
                'name'        => 'admission_dt',
                'header'      => 'Admission Date',
                'type'        => 'date',
                'htmlOptions' => array('width' => '10%'),
            ),
            array(
                'name'        => 'bill_dte',
                'type'        => 'datetime',
                'header'      => 'Discharge Date',
                'htmlOptions' => array('width' => '10%'),
            ),
            array(
                'header'      => 'Package',
                'value'       => function ($data, $row) {
                    if (empty($data['package_data_new'])) {
                        return CHtml::tag('em', array('class' => 'muted'),
                            'NO PACKAGE IN BILL');
                    }


                    $_packages = array();
                    $description = explode("|", $data['package_data_new']);
                    // foreach($data->billing->getCaseRateInOrder() as $caseRate) {
                    $_helpIcon = CHtml::tag('i', array(
                        'class'       => 'fa fa-question-circle',
                        'data-title'  => $description[1],
                        'data-toggle' => 'tooltip',
                    ), ' ');

                    $_caseRateCode = CHtml::tag('small', array(),
                        end(explode("|", $data['package_data_new'])));

                    $_packages[] = CHtml::tag('p',
                        array('class' => 'case-rate-p text-right'),
                        $_caseRateCode.' '.$_helpIcon
                    );
                    // }
                    $_caseRatesAmout = CHtml::tag('div',
                        array('class' => 'text-right'),
                        Yii::app()->numberFormatter->formatCurrency(current(explode("|",
                            $data['package_data_new'])),
                            "PHP "));

                    $_formatted = implode("\n", $_packages)
                        ."<hr class='case-rate-hr'>"
                        .$_caseRatesAmout;

                    return $_formatted;
                },
                'type'        => 'raw',
                'htmlOptions' => array('width' => '10%'),
            ),
            array(
                'name'        => 'STATUS',
                'htmlOptions' => array('width' => '10%'),
                #'filter' => true,

            ),

            array(
                'header' => Yii::t('ses', 'Action'),
                'headerHtmlOptions' => array('style' => 'text-align:center; width: 50px;'),
                'htmlOptions' => array('style' => 'align:center; text-align: center;'),
                'value' => function ($data) {
                    Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
                        'size' => 'small',
                        'htmlOptions' => array(
                            'class' => 'col-md-12',
                            'style' => 'text-align:center;',
                        ),
                        'buttons' => array(
                            'detail' => array(
                                'icon' => 'icon-check',

                                'htmlOptions' => array(
                                    'class' => 'viewStatusBtn',
                                    'data-modal-url' => Yii::app()->createUrl('eclaims/claimStatus/ViewStatusModal'),
                                    "data-modal-claimId" => $data["id"],
                                    "data-modal-encounterNr" => $data["encounter_nr"],
                                    "data-searchin" => $_GET["search"],
                                    "data-update_status" => 1,
                                    "data-current_page" => $_GET["user_page"],
                                ),
                            ),
                        ),
                    ));
                },
            ),



        ),
    )
);
$this->endWidget();


?>
<!-- /*added by MARK April 21, 2017*/ -->
<input type="hidden" name="" id="ifSearch"
       value="<?php echo $_REQUEST['search']; ?>">
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'myModal')
); ?>


<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Search Claim Status by:</h4>
</div>

<div class="modal-body">

    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'                   => 'seg-search-eclaims-form',
        'enableAjaxValidation' => false,
        'htmlOptions'          => array(
            'onsubmit'   => "return send();",
            'onkeypress' => " if(event.keyCode == 13){ send(); } ",
        ),
    )); ?>

    <center>
        <small>(You can select only one field)</small>
        <br>
        <input class="searchI" type="text" id="encounter_nr"
               placeholder="Encounter No." onfocusout="OnBlurData();"
               onclick="getOtherDisable(this);" name="encounter_nr_new_data"
               value="">
        <br>
        <input class="searchI" type="text" id="transmit_no"
               placeholder="Transmittal No." onfocusout="OnBlurData();"
               onclick="getOtherDisable(this);" name="transmit_no_new_data"
               value="">
        <br>
        <input class="searchI" type="text" id="claim_series_lhio"
               placeholder="Claim Series Lhio"
               onfocusout="OnBlurData();" onclick="getOtherDisable(this);"
               name="claim_series_lhio" value="">
        <br>
        <input class="searchI" type="text" id="patient_lastname"
               placeholder="Patient Lastname"
               onfocusout="OnBlurData();" onclick="getOtherDisable(this);"
               name="patient_lastname" value="">
        <br>
        <?php
        $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'name'        => 'Transmittal_date',
                'htmlOptions' => array(
                    'class'       => 'searchI',
                    'placeholder' => 'Transmit Date',
                    'onfocusout'  => "OnBlurData()",
                    'onclick'     => "getOtherDisable(this)",
                ),
            )
        );
        ?>
        <br>
        <?php
        $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'name'        => 'admission_date',
                'htmlOptions' => array(
                    'class'       => 'searchI',
                    'placeholder' => 'Admission Date',
                    'onfocusout'  => "OnBlurData()",
                    'onclick'     => "getOtherDisable(this)",
                ),
            )
        );
        ?>
        <br>
        <?php
        $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'name'        => 'discharge_date',
                'htmlOptions' => array(
                    'class'       => 'searchI',
                    'placeholder' => 'Discharge Date',
                    'onfocusout'  => "OnBlurData()",
                    'onclick'     => "getOtherDisable(this)",
                ),
            )
        );
        $list = CHtml::listData($model, 'status_name', 'status_name');
        echo CHtml::dropdownlist('status', $selectedvalue,
            $list,
            array(
                'prompt' => '--- Select Status ----',
                'class'  => 'searchStatus',
            ));
        ?>


    </center>
</div>


<div class="modal-footer">
    <?php
    $this->widget('bootstrap.widgets.TbButton',
        array(
            'id'          => 'custom-search-data',
            'buttonType'  => 'submit',
            'type'        => 'primary',
            'icon'        => 'fa fa-search',
            'loadingText' => 'Saving ...',
            'label'       => 'Search',
            'htmlOptions' => array(
                'class' => 'getpinButton',
            ),
        )
    );

    ?>
    <?php $this->endWidget(); ?>
    <?php
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label'       => 'Close',
            'url'         => '#',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    );
    ?>

</div>
<?php $this->endWidget(); ?>
<!-- /*END added by MARK April 21, 2017*/ -->


<?php
$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id'          => 'claimStatus',
        'htmlOptions' => array(
            'style'         => "width:1250px;margin-left:-625px; overflow-y: auto; margin-top:-55px;",
            'data-backdrop' => "static",
        ),
    )
); ?>


<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><i class="color-blue fa fa-search"></i> View Claim Status:</h4>
</div>

<div class="modal-body" style="min-height:470px;">


</div>


<div class="modal-footer">

    <?php
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label'       => 'Close',
            'url'         => '#',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    );
    ?>

</div>
<?php $this->endWidget(); ?>
<!-- /*END added by MARK April 21, 2017*/ -->
<script>
    $(document).ready(function () {

        $(".viewStatusBtn").live("click", function (event) {
            var id = $(this).data('modal-claimid');
            // AJAX request
            $.ajax({
                url: $(this).data('modal-url'),
                type: 'post',
                data: {
                    "id": id
                },
                dataType: 'json',
                beforeSend: function () {
                    Alerts.loading({
                        'title': 'Please wait...',
                        content: 'Checking Claim Status'
                    });
                },
                success: function (response) {
                    // Add response in Modal body
                    $('#claimStatus .modal-body').html(response.form);

                    // Display Modal
                    $('#claimStatus').modal('show');

                    $('#claim').yiiGridView('update');

                },
                complete: function () {
                    Alerts.close();
                },
            });
        });


        $(document).on('hide.bs.modal', '#claimStatus', function (e) {
            $('#claim').yiiGridView('update');
        });
    });
</script>
