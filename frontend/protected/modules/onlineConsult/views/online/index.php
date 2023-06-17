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
    
    $("#online-grid .viewdetails").live('click', function() {
        let status = $(this).data('status');        

        if (status == 1) {
            Alerts.alert({
                icon: 'fa fa-times',
                title: "OOPS!",
                content: "Consultation request of "+$(this).data('name')+" is already being triaged!",
                callback: function (result) {
                    Alerts.close();
                }
            });            
            return false;
        }

        let consultid = $(this).attr('id');
        let index = $(this).data('index');
        let name = $(this).data('name');
        let fb_id = $(this).data('fb_id');
        let address = $(this).data('address');
        let contact_no = $(this).data('contact');
        let date_birth = $(this).data('date_birth');
        let complaint = $(this).data('complaint');
        
        $("#consult_id").val(consultid);
        $("#patient-name").val(name);
        $("#address").val(address);
        $("#contact-no").val(contact_no);
        $("#birth-date").val(date_birth);

        $("#fb-id").attr("href", 'https://www.messenger.com/t/'+fb_id);
        $("#fb-id").text(fb_id);

        $("#complaint").val(complaint);
        
        var loc = window.location;
        $.ajax({            
            url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/blockConsultRequest',
            type: 'POST',
            data: {consultId: consultid},
            dataType : 'json',
            async : false,
            success : function(response) {
                console.log('Consult request blocked!');
                
                $.ajax({            
                    url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/notifyTriageStarted',
                    type: 'POST',
                    data: {consultId: consultid},
                    dataType : 'json',
                    async : false,
                    success : function(response) {
                        console.log('Triage id sent to patient!');                        
                    }
                });
            }
        });
    });
    
    $("#view-consultation-modal").on("hidden", function () {
        let consultid = $("#online-grid .viewdetails").attr('id');
        if (consultid) {
            consultid = $("#consult_id").val();
        }

        if (consultid) {
            var loc = window.location;
            $.ajax({            
                url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/signalDoneConsultRequest',
                type: 'POST',
                data: {consultId: consultid},
                dataType : 'json',
                async : false,
                success : function(response) {
                    console.log('Triage of consult request done!');
                }
            });
        }
    });    

	if(localStorage.getItem('notifToken')){
		let notification = new Notification(localStorage.getItem('notifSocketHost'), localStorage.getItem('notifToken'));
        notification.initTeleconsultTriageAlert();
    }
    
JAVASCRIPT
    , CClientScript::POS_READY);

$this->breadcrumbs = array(
    'OPD' => $baseUrl . '/modules/opd/seg-opd-functions.php?ntid=false&lang=en',
    'Online Consultation Requests',
);
$this->pageTitle   = '';

?>

<h3 align="left">Online Consultation Requests</h3>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id'           => 'online-grid',
    'dataProvider' => $dataProviderOnline,
    'type'         => 'bordered',
    'columns'      => array(
        //     array(
        //     'name'              => 'pid',
        //     'header'            => 'HRN',
        //     'headerHtmlOptions' => array(
        //         'style' => 'width: 110px; text-align: center; vertical-align: middle;'
        //     ),
        //     'htmlOptions'       => array(
        //         'style' => 'text-align: center;',
        //         'id'    => 'pid'
        //     )
        // ),
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
                'data-target' => '#view-consultation-modal'
            ),
            'value'             => function ($data) {
                $service = new SegHis\modules\onlineConsult\services\ConsultationService();
                $status = $service->isBlockedConsultRequest($data['consult_id']);
                
                Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup',
                    array(
                        'buttons' => array(
                            'view' => array(
                                'icon'        => 'fa fa-navicon',
                                'label'       => '',
                                'htmlOptions' => array(
                                    'title' => 'Serve Consultation Request',
                                    'class' => 'btn-small viewdetails',
                                    'id'    => $data['consult_id'],
                                    'data-name' => ucfirst($data['name_first']) . " " . ucfirst($data['name_middle']) . " " . ucfirst($data['name_last']),
                                    'data-address' => $data['address'],
                                    'data-contact' => $data['contact_no'],
                                    'data-date_birth' => $data['date_birth'],
                                    'data-fb_id' => $data['fb_username'],
                                    'data-complaint' => $data['chief_complaint'],
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
        $this->widget('onlineConsult.widgets.ViewConsultation', array());
    ?>
</div>
