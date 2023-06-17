<?php

$baseUrl = Yii::app()->request->baseUrl;

$cs = Yii::app()->clientScript;
$cs->RegisterCss(
    'antibiotic-css', <<<CSS
        body ul.breadcrumb {
            margin-top: -48px;
        }
CSS
);

$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/socket.io.js',
    CClientScript::POS_END
);
$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/laravel-echo.js',
    CClientScript::POS_END
);
$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/notification.js',
    CClientScript::POS_END
);

$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css',
    CClientScript::POS_END
);
$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js',
    CClientScript::POS_END
);

Yii::app()->getClientScript()->registerScript('indexonlineConsult', <<<JAVASCRIPT

    $("#online-grid .searchdetails").live('click', function() {
        let status = $(this).data('status');        

        if (status == 1) {
            Alerts.alert({
                icon: 'fa fa-times',
                title: "OOPS!",
                content: "Consultation request of "+$(this).data('name')+" is already being registered!",
                callback: function (result) {
                    Alerts.close();
                }
            });            
            return false;
        }

        consultid = $(this).attr('id');
        var loc = window.location;
        $.ajax({            
            url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/blockConsultRegister',
            type: 'POST',
            data: {consultId: consultid},
            dataType : 'json',
            async : false,
            success : function(response) {
                console.log('Registration of consultation blocked!');
            }
        });        
        
        pid = $(this).attr('pid');        
        if (pid) {
            var url = $(this).attr('url') + "&pid=" + pid + "&id=" + consultid;
            window.location.href = url;
        }
        else {
            $("#consult_id").val(consultid);

            $.fn.yiiGridView.update("person-list-grid", {
                data: {'consult_id': consultid },
            });
        }
    });

    $('#search-patient-modal').on('hidden.bs.modal', function () {
        $("#person_search").val('');

        $.fn.yiiGridView.update('person-list-grid', {
            data: {'search': 0}
        });

        return false;
    });

    $("#filter_request").on("change", function(){
        $.fn.yiiGridView.update('online-grid', {
            data: {'filter': $(this).val() }
        });
    });

	if(localStorage.getItem('notifToken')){
		let notification = new Notification(localStorage.getItem('notifSocketHost'), localStorage.getItem('notifToken'));
        notification.initTeleconsultMedRecAlert();
    }    

JAVASCRIPT
    , CClientScript::POS_READY);

$this->breadcrumbs = array(
    'Medical Records' => $baseUrl . '/modules/medocs/seg-medocs-functions.php?ntid=false&lang=en',
    'Triaged Online Consultation Requests',
);
$this->pageTitle   = '';

?>

<h3 align="left">Triaged Online Consultation Requests</h3>

<?php

echo CHtml::dropDownList('filter_request', '', 
    array(
        'witho_consult' => "Without Consultation",
        'with_consult' => "With Consultation"
    ), 
    array(
        'id' => 'filter_request'
    )
);

$this->widget('bootstrap.widgets.TbGridView', array(
    'id'           => 'online-grid',
    'dataProvider' => $dataProviderOnline,
    'type'         => 'bordered',
    'columns'      => array(
            array(
            'name'              => 'pid',
            'header'            => 'HRN',
            'headerHtmlOptions' => array(
                'style' => 'width: 110px; text-align: center; vertical-align: middle;'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;',
                'id'    => 'pid'
            )
        ),
        array(
            'name'              => 'name_last',
            'header'            => 'Family Name',
            'headerHtmlOptions' => array(
                'style' => 'width: 110px; text-align: center; vertical-align: middle;'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;',
                'id'    => 'pid'
            )
        ),
        array(
            'name'              => 'name_first',
            'header'            => 'Given Name',
            'headerHtmlOptions' => array(
                'style' => 'width: 110px; text-align: center; vertical-align: middle;'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
        ),
        array(
            'name'              => 'name_middle',
            'header'            => 'Middle Name',
            'headerHtmlOptions' => array(
                'style' => 'width: 110px; text-align:center; vertical-align: middle;'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
        ),
        array(
            'name'              => 'date_birth',
            'header'            => 'Date of Birth',
            'headerHtmlOptions' => array(
                'style' => 'width: 50px; text-align:center; vertical-align: middle;'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'

            )
        ),
        array(
            'name'              => 'contact_no',
            'header'            => 'Contact No.',
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align: center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'

            )
        ),
        array(
            'name'              => 'address',
            'header'            => 'Address',
            'headerHtmlOptions' => array(
                'style' => 'width: 170px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
        ),
        array(
            'name'              => 'chief_complaint',
            'header'            => 'Chief Complaint',
            'headerHtmlOptions' => array(
                'style' => 'width: 170px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
        ),
        array(
            'name'              => 'yellow_card',
            'header'            => 'Yellow Card Number',
            'headerHtmlOptions' => array(
                'style' => 'width: 50px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
        ),
        array(
            'header'            => 'Related in this institution?',
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            ),
            'value' => function ($data) {
                return ($data['areRelated'] == 1 ? 'Yes' : 'No');
            }
        ),
         array(  
            'name'              => 'fathers_name',
            'header'            => "Father's Name",
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
    
        ),
        array(  
            'name'              => 'mothers_name',
            'header'            => "Mother's Name",
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
    
        ),
          array(  
            'name'              => 'spouse_name',
            'header'            => "Spouse's Name",
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
    
        ),
            array(  
            'name'              => 'guardians_name',
            'header'            => "Guardian's Name",
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            )
    
        ),
          array(
            'header'            => 'Facebook Account',
            'headerHtmlOptions' => array(
                'style' => 'width: 60px; text-align:center; vertical-align: middle'
            ),
            'htmlOptions'       => array(
                'style' => 'text-align: center;'
            ),
         // 'value' => 'CHtml::link("cl", "https://www.messenger.com/t/alhambra.marc", array("target"=>"_blank"))',
            'value'=> function ($data) {
                return CHtml::link($data['fb_username'], 'https://www.messenger.com/t/'.$data['fb_username'], array("target"=>"_blank"));

            },
             'type'    => 'raw'
        ),
        array(
            'header'            => 'Actions',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; vertical-align: middle; width: 50px;'
            ),
            'htmlOptions'       => array(
                'style'       => 'text-align: center; width: 50px;',
                'data-toggle' => 'modal',
                'data-target' => '#search-patient-modal'
            ),
            'value'             => function ($data) {
                $service = new SegHis\modules\medRec\services\ConsultationService();
                $status = $service->isBlockedConsultMedRec($data['consult_id']);

                Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup',
                    array(
                        'buttons' => array(
                            'view' => array(
                                'icon'        => 'fa fa-search',
                                'label'       => '',
                                'htmlOptions' => array(
                                    'title' => 'Process Consultation Request',
                                    'class' => 'btn-small searchdetails',
                                    'id'    => $data['consult_id'],
                                    'pid'   => $data['pid'],                                    
                                    'url'   => Yii::app()->createUrl("medRec/online/view_history"),
                                    'data-name' => ucfirst($data['name_first']) . " " . ucfirst($data['name_middle']) . " " . ucfirst($data['name_last']),
                                    'data-status' => $status ? "1" : "0",
                                    'style' => 'margin-right: 5px;'
                                )
                            )
                        )
                    )
                );
            }
        )
    )
));

?>

<div class="row-fluid">
    <?php
    echo \CHtml::hiddenField('consult_id', '');

    $this->widget('medRec.widgets.SearchPersonList', array(
        'last_name'  => $last_name,
        'first_name' => $first_name
    ));
    ?>
</div>
