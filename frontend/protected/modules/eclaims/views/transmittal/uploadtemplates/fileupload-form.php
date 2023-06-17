<?php
/**
 * The file upload form used as target for the file upload widget
 *
 * @var TbFileUpload $this
 * @var array $htmlOptions
 */

echo CHtml::beginForm($this->url, 'post', $this->htmlOptions);
// start
// var_dump($this->extra['pid']);die;
if (!empty($this->extra['encounter_nr'])) {
    $urlManager = Yii::app()->getUrlManager();

    $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
    $laboratory_results = array(
        'domain' => Yii::app()->getBaseUrl(),
        'route' => $urlManager->createPathInfo(array(
            'modules' => '',
            'repgen' => '',
            'laboratory_results.php' => '',
        ), '', '/'),
        'params' => $urlManager->createPathInfo(array(
            'ntid' => 'false',
            'lang' => 'en',
            'encounter_nr' => $this->extra['encounter_nr'],
            'id' => $phic->hcare_id,
        ), '=', '&'),
    );
    $radiology_results = array(
        'domain' => Yii::app()->getBaseUrl(),
        'route' => $urlManager->createPathInfo(array(
            'modules' => '',
            'repgen' => '',
            'radiology_results.php' => '',
        ), '', '/'),
        'params' => $urlManager->createPathInfo(array(
            'ntid' => 'false',
            'lang' => 'en',
            'encounter_nr' => $this->extra['encounter_nr'],
            'id' => $phic->hcare_id,
        ), '=', '&'),
    );
    $laboratory_results = "{$laboratory_results['domain']}/{$laboratory_results['route']}?{$laboratory_results['params']}";
    $radiology_results = "{$radiology_results['domain']}/{$radiology_results['route']}?{$radiology_results['params']}";
}
// end
// var_dump($radiology_results);die;

$htmlOptions['style'] = 'display: none;';
if ($this->hasModel()) :
    echo CHtml::activeFileField($this->model, $this->attribute, $htmlOptions)
        . "\n";
else :
    echo CHtml::fileField($name, $this->value, $htmlOptions) . "\n";
endif;

$this->beginWidget(
    'application.widgets.SegBox', array(
        'title' => '',
        'headerButtons' => array(
            array(
                'class' => 'bootstrap.widgets.TbButtonGroup',
                'buttons' => array(
                    array(
                        'label' => 'Add Files',
                        'buttonType' => 'button',
                        'htmlOptions' => array(
                            'class' => 'fileinput-button',
                            'onclick' => '$(".multi-upload").trigger("click");',
                        ),
                    ),
                    array(
                        'label' => 'Upload attachment/s',
                        'type' => TbButton::TYPE_PRIMARY,
                        'buttonType' => TbButton::BUTTON_SUBMIT,
                        'visible' => $this->extra['service']->checkReturn()
                            ? false
                            : true,
                        'htmlOptions' => array(
                            'id' => 'attachments-submit',
                            'class' => 'start',

                        ),
                    ),
                    array(
                        'label' => 'Generate CF4',
                        'buttonType' => 'button',
                        'htmlOptions' => array(
                            'class' => 'btn btn-success',
                            'id' => 'print-cf4',
                            'data-url' => Yii::app()->getController()->createUrl('RenderCF4Modal'),
                            'data-encounter' => $this->extra['details']['encounter_nr'],
                            'data-transmittal' => $this->extra['details']->transmit_no
                            // 'onclick' => '$(".multi-upload").trigger("click");',
                        ),
                    ),
                    array(
                        'label' => 'Re-Upload Attachment',
                        'type' => TbButton::TYPE_INVERSE,
                        'buttonType' => TbButton::BUTTON_SUBMIT,
                        'visible' => $this->extra['service']->checkReturn(),
                        'htmlOptions' => array(
                            'id' => 'attachments-submit',
                            'class' => 'start',
                        ),
                    ),
                    array(
                        'label' => 'Diagnostic Results',
                        'buttonType' => 'button',
                        'htmlOptions' => array(
                            'class' => 'btn btn-default',
                            'id' => 'diagnostic-results',
                            'data-encounter' => $this->extra['details']['encounter_nr'],
                            'data-pid' => $this->extra['pid'],
                        ),
                    ),
                    array(
                        'buttonType' => TbButton::BUTTON_BUTTON,
                        'items' => array(
                            array(
                                'label' => 'Laboratory',
                                'icon' => 'fa fa-print',
                                'url' => "#",
                                'linkOptions' => array(
                                    'class' => 'laboratory-results',
                                ),
                                'itemOptions' => array(
                                    'class' => "",
                                ),
                            ),
                            array(
                                'label' => 'Radiology',
                                'icon' => 'fa fa-print',
                                'url' => '#',
                                'linkOptions' => array('class' => 'radiology-results'),
                                'itemOptions' => array(
                                    'class' => '',
                                ),
                            ),
                        ),

                    ),
                ),
                'htmlOptions' => array(
                    'class' => 'fileupload-buttonbar',
                ),
            ),
        ),
        'footer' => CHtml::tag(
            'div', array('class' => 'form-actions', 'id' => 'footerdiv'),

            $this->widget(
                'bootstrap.widgets.TbButton', array(
                'id' => 'close-button',
                'label' => 'Close',
                'buttonType' => TbButton::BUTTON_LINK,
                'url' => $this->getController()->createUrl(
                    'attachments', array(
                        'id' => $this->extra['details']->transmit_no,
                    )
                ),
            ), true
            )
        ),
    )
);
?>

<div class="row-fluid">
    <div class="span12 alert alert-info">
        <i class="fa fa-question-circle"></i> Hover your mouse over the
        <strong>filenames</strong> to preview the selected files
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="grid-view">
            <table id="attachments-grid"
                   class="table table-striped table-condensed table-bordered">
                <thead>
                <tr>
                    <th>Document type</th>
                    <th>Attachment</th>
                    <th class="button-column"></th>
                </tr>
                </thead>
                <tbody class="files" data-toggle="modal-gallery"
                       data-target="#modal-gallery"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row-fluid">
    <?php
    echo $this->widget('bootstrap.widgets.TbButton', array(
        'encodeLabel' => false,
        'label' => 'Assign <i class="fa fa-question-circle"></i>',
        'buttonType' => TbButton::BUTTON_BUTTON,
        'type' => TbButton::TYPE_SUCCESS,
        'id' => 'btnAssign',
        'htmlOptions' => array(
            'title' => "Auto-assign document file type",
            'data-url' => $this->getController()->createUrl('GetDocumentTypes'),
        ),
    ), true);
    ?>
</div>

<style type="text/css">
    #print-cf4 {
        margin-left: 3px;
    }

    #diagnostic-results {
        margin-left: 3px;
    }

    #laboratory-results {
        margin-left: 3px;
    }
</style>

<?php $this->endWidget() /* Box */ ?>
<?php echo CHtml::endForm(); ?>

<script>
    //add class in every dropdown menu
    jQuery('.dropdown-menu').addClass('pull-right');
    $('#print-cf4').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            dataType: 'JSON',
            data: {
                'url': $(this).data('url'),
                'id': $(this).data('encounter'),
                'transmittalNo': $(this).data('transmittal')
            },
            beforeSend: function () {
                $('#cf4Modal').modal('show');
                Alerts.loading({
                    'title': 'Please wait...',
                    content: 'Generating Eclaims CF4'
                });
            },
        }).done(function (data) {

            $('#cf4Modal .modal-body').html(data.form).load("xml", function () {
                Alerts.close();
            });
        })
    });

    $(".laboratory-results").click(function (e) {
        var getUrl = window.location;
        var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
        const pid = $("#diagnostic-results").data('pid');
        const encounter_nr = $("#diagnostic-results").data('encounter');
        // const encounter_nr = '2019401003';
        // const pid = '3247777';
        window.open(baseUrl + '/modules/laboratory/seg-lab-report-hl7-per-encounter.php?pid=' +
            pid + '&encounter_nr=' + encounter_nr + '&showBrowser=1', 'popUpWindow',
            'resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes');
    });

    $(".radiology-results").click(function (e) {
        var getUrl = window.location;
        var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
        const pid = $("#diagnostic-results").data('pid');
        const encounter_nr = $("#diagnostic-results").data('encounter');
        // const pid = '3247777';
        // const encounter_nr = '2019401003';
        window.open(baseUrl + '/modules/radiology/certificates/seg-radio-unified-report-pdf-per-encounter.php?encounter_nr=' + encounter_nr + '&pid=' + pid, 'popUpWindow', 'resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes');
    });
</script>
