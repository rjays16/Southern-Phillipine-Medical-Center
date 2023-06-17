<?php

Yii::import('bootstrap.helpers.TbHtml');
Yii::import('bootstrap.widgets.TbActiveForm');
Yii::import('bootstrap.widgets.TbButton');

$this->breadcrumbs[$details->transmit_no] = array('attachments', 'id' => $details->transmit_no);
$this->breadcrumbs[] = 'Manage attachments';


$cs = Yii::app()->clientScript;
$baseUrl = $baseUrl = Yii::app()->request->baseUrl;

$cs
    ->registerCssFile($baseUrl . '/css/codemirror/lib/codemirror.css')
    ->registerCssFile($baseUrl . '/css/codemirror/theme/ambiance.css')
    ->registerCssFile($baseUrl . '/css/codemirror/addon/hint/show-hint.css')
    ->registerCss('transmittal.generate', <<<STYLE
.CodeMirror {
    border: 1px solid #ccc;
    font-family: Monaco, Menlo, Consolas, 'Courier New', monospace;
    font-size: 14px;
        height: auto;
}
STYLE
    )->registerScriptFile($baseUrl . '/js/codemirror/lib/codemirror.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/mode/xml/xml.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/display/placeholder.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/hint/show-hint.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/hint/xml-hint.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/selection/active-line.js')
    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/tags.js');
//    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/generate.js', CClientScript::POS_END);


/* @var $this Controller */
/* @var $details EclaimsTransmittalDetails */

$this->setPageTitle('Manage claim attachments');
$cs = Yii::app()->getClientScript();
$cs->registerCss('transmittal.manageAttachments.css', <<<CSS
    .filename {
        font-weight: bold;
        cursor: pointer;
    }
    .popover {
        max-width: none;
    }
    .popover-content {
        width: 65em;
        height: 20em;
    }
CSS
)
    ->registerScript('transmittal.manageAttachments.js', <<<JAVASCRIPT

/**
 * Not checked: NULL, w/o errors: false, w/ errors: true
 */
var uploadFormAttachment = {
    error: null,
    getError: function() { return this.error; },
    setError: function(val) { this.error = val; },
    refreshError: function() { this.error = null; }
};


$('.remove-attachment-btn').live('click', function(e) {
    e.preventDefault();
    var that = this;
    Alerts.confirm({
        title: 'Remove this attachment?',
        content: 'The attachment will be <b>permanently deleted</b>. Are you sure?',
        callback: function(result) {
            if (result) {
                Alerts.loading({
                    title: 'Please wait',
                    content: 'Attempting to delete the attachment from our remote server...'
                });
                $('#remove-attachment-id').val(that.value);
                $('#remove-attachment-form').submit();
            }
        }
    })
});

<!--$('.remove-attachment-btn').off('click').on('click', function(e) {-->
<!--    e.preventDefault();-->
<!--    var that = this;-->
<!--    Alerts.confirm({-->
<!--        title: 'Remove this attachment?',-->
<!--        content: 'The attachment will be <b>permanently deleted</b>. Are you sure?',-->
<!--        callback: function(result) {-->
<!--            if (result) {-->
<!--                Alerts.loading({-->
<!--                    title: 'Please wait',-->
<!--                    content: 'Attempting to delete the attachment from our remote server...'-->
<!--                });-->
<!--                $('#remove-attachment-id').val(that.value);-->
<!--                $('#remove-attachment-form').submit();-->
<!--            }-->
<!--        }-->
<!--    })-->
<!--});-->


$('#attachments-submit').on('click', function() {
    if($._isAttachmentListEmpty()) {
        Alerts.error({
            title: 'Attachment error',
            content: 'No attachments/files selected for uploading!'
        });
        return false;
    }
});

// Added by Johnmel --- Alert for button Re-upload 07-04-2018
$('#reUploadttachments-submit').on('click', function() {
    if($._isAttachmentListEmpty()) {
        Alerts.error({
            title: 'Attachment error',
            content: 'No attachments/files selected for Re-uploading!'
        });
        return false;
    }
});
// end Johnmel


// auto assign file type on attachment : monmon
$('#btnAssign').on('click', function(e){
    e.preventDefault();
    var filesAttached = [];
    var ftypes = [];
     $.ajax({
        type: "POST",
        url: $(this).data('url'),
        dataType:'JSON',
        data : [] ,
        success: function (response) {
            ftypes = response;
          // gets the attached file name
          var obj = document.querySelectorAll("[data-title]")
          for (var i in obj) if (obj.hasOwnProperty(i)) {
              var filename = obj[i].getAttribute('data-title');
              // separate filename and file extension
              filename = filename.split(".");
              var holder = filename[0].toUpperCase();
          
              if(ftypes.indexOf(holder) >= 0)
                  filesAttached.push(holder);
              else
                  filesAttached.push('');
          }
          // assign selected value from the array above
          var attTypes = document.getElementsByClassName("attachment-type");
          for(var i = 0; i < attTypes.length; i++){
              var select = attTypes.item(i);
              select.value = filesAttached[i];
          }
        },
      });
   return false;
});
function getFileTypes(){
    var ftypes = [
            'CF1',
            'CF2',
            'CF3',
            'CF4',
            'CSF',
            'COE',
            'SOA',
            'MDR',
            'ORS',
            'POR',
            'CAE',
            'PIC',
            'MBC',
            'MMC',
            'CAB',
            'CTR',
            'DTR',
            'MEF',
            'MSR',
            'MWV',
            'NTP',
            'OPR',
            'PAC',
            'PBC',
            'STR',
            'TCC',
            'TYP'
    ];
    return ftypes;
}
// end

$('#close-button').click(function(e) {
    e.preventDefault();
    var that=this;

    if ($._isAttachmentListEmpty()) {
         window.history.back();
    } else {
        Alerts.confirm({
            title: 'Hey! Wait a minute...',
            content: 'Some attachments have not been uploaded yet. Do you still wish to leave this page?',
            callback: function(result) {
                if (result) {
                    window.location.href = that.href;
                } else {
                    // Do nothing
                }
            }
        });
    }
});

// Get all <select> attachment types elements
$._getAllSelectAttachmentType = function() {
    var form = $('form.multi-upload');
    return form.find('select.attachment-type');
};
// Is attachment form empty?
$._isAttachmentListEmpty = function() {
    var attachmentTypes = $._getAllSelectAttachmentType();
    if(attachmentTypes.length > 0) {
            return false;
        }
    return true;
};
// Has an empty attachment type?
$._hasEmptyAttachmentType = function() {
    var attachmentTypes = $._getAllSelectAttachmentType(),
        error = false;

    attachmentTypes.each(function() {
        var item = $(this);
        if(item.val().length < 1) {
            return error = true;
        }
    });
    return error;
};
// Validate attachment form, e.g. if has empty attachment type.
$._validateForm = function() {
    /* If already vaidated, do not revalidate; unless new item added */
    var error = uploadFormAttachment.getError();
    console.log(error);
    if(error === null) {
        error = $._hasEmptyAttachmentType();
        uploadFormAttachment.setError(error);
    }

    if(error) {
        Alerts.error({
            title: 'Attachment error',
            content: 'One or more attachments do not have a selected <b>attachment type</b>.'
        });
        return false;
        }
    return true;
    }

JAVASCRIPT
        , CClientScript::POS_READY);
?>
<div id="myDiv">
    <?php if (Yii::app()->user->hasFlash('error')): ?>

		<div class="flash-error">
			<div class="alert alert-danger" role="alert">
                <?php echo Yii::app()->user->getFlash("error"); ?>
			</div>
		</div>
    <?php endif ?>
</div>
<div class="row-fluid">
    <?php
    $this->widget(
        'bootstrap.widgets.TbDetailView',
        array(
            'data' => $details->encounter,
            'attributes' => array(
                array('label' => "Patient Name", 'value' => $details->encounter->person->getFullName()),
                array('label' => "HRN", 'value' => $details->encounter->pid),
                array('label' => "Case No.", 'value' => $details->encounter->encounter_nr),
            ),
            'type' => 'striped condensed bordered',
        )
    );
    ?>
</div>


<div class="row-fluid">
	<div class="span5">

        <?php
        $url = $service->checkReturn() ? "reupload" : "UploadAttachment";
        $this->widget('eclaims.widgets.AttachmentsUpload', array(
            'url' => $this->createUrl($url, array(
                'transmit_no' => $details->transmit_no,
                'encounter_nr' => $details->encounter_nr,
            )),
            'extra' => array(
                'details' => $details,
                'service' => $service,
                'encounter_nr' => $details->encounter_nr,
                'pid' => $details->encounter->pid,
            ),
            'model' => $attachmentForm,
            'attribute' => 'attachment', // see the attribute?
            'multiple' => true,
            'options' => array(
                'maxFileSize' => 21000000,
                'acceptFileTypes' => 'js:/(\.|\/)(pdf|xml)$/i',
                'add' => 'js:function (e, data) {
                    var that = this;
                    console.log(that);
                    $.blueimp.fileupload.prototype
                        .options.add.call(that, e, data);

                    var id = new Date().getTime();
                    data.paramName = data.paramName + "["+ id +"]";

                    var attachmentInput = $("tbody.files tr:last .attachment-type");
                    var cancelButton = $("tbody.files tr:last .attachment-row-cancel");
                    var filename = $("tbody.files tr:last .filename");
                  
                    console.log(data.error)
                    /*
                        rename <select> attachment type original name + id(see above)
                    */
                    attachmentInput.attr("name", attachmentInput.attr("name") + "["+ id +"]");
                    /*
                        add popover on filename mouseenter and popover mouseleave
                    */
                    filename.popover();
                    filename.off("hover").on("hover", function() {
                        var that = $(this),
                            popover = that.next();

                        that.popover("show");

                        popover.off("mouseleave").on("mouseleave", function() {
                            that.popover("hide");
                        });
                    }).off("mouseleave").on("mouseleave", function() {
                        var that = $(this),
                            sibling = that.next();
                        if(!sibling.is(":hover") && sibling.hasClass("popover")) {
                            that.popover("hide");
                        }
                    });
                    /*
                        Refresh Form error, to enable revalidate behavior
                        Listen events on: add, change(attachment type), delete
                    */
                    attachmentInput.off("change").on("change", function() {
                        uploadFormAttachment.refreshError();
                    });
                    cancelButton.off("click").on("click", function() {
                        uploadFormAttachment.refreshError();
                    });
                    uploadFormAttachment.refreshError(null);

                }',
                'success' => 'js:function(file, status) {
                    $.fn.yiiGridView.update("uploaded-attachments");
                    $.fn.yiiGridView.update("returned-attachments");
                }',
                'stop' => 'js:function(e, data) {
                    location.reload();
                }',
                'submit' => 'js:function(e, data) {
                    return $._validateForm();
                }',
            ),
            'formView' => 'eclaims.views.transmittal.uploadtemplates.fileupload-form',
            'uploadView' => 'eclaims.views.transmittal.uploadtemplates.fileupload-item',
            'downloadView' => 'eclaims.views.transmittal.uploadtemplates.fileupload-download',
            'htmlOptions' => array(
                'class' => 'multi-upload',
            ),
        ));
        ?>

	</div>

	<div class="span7">
        <?php
        $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'List of uploaded attachments',
            'htmlOptions' => array(
                'class' => 'bootstrap-widget-table',
            ),
        ));

        $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'remove-attachment-form',
            'type' => TbActiveForm::TYPE_VERTICAL,
            'action' => $this->createUrl('removeAttachment', array(
                'transmit_no' => $details->transmit_no,
                'encounter_nr' => $details->encounter_nr,
            )),
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));

        echo CHtml::hiddenField('id', null, array(
            'id' => 'remove-attachment-id',
        ));

        ?>
		<!-- Mod jeff 06-07-18 -->
        <?php
        $this->widget('bootstrap.widgets.TbGridView', array(
            'id' => 'uploaded-attachments',
            'type' => 'striped condensed bordered hover',
            'dataProvider' => $details->searchAttachments(),
            'template' => "{items}</br>{pager}",
            'columns' => array(
                array(
                    'header' => 'Type',
                    'type' => 'raw',
                    'value' => '"<span class=\"label label-info\">{$data->attachment_type}</span>"',
                ),
                array(
                    'header' => 'File name',
                    'type' => 'raw',
                    'value' => function ($data) {
                        $fileUrl = $data->getUrl();
                        if ($fileUrl) {
                            $item = CHtml::link($data->filename, $fileUrl, array('target' => '_blank'));
                        } else {
                            $item = $data->filename;
                        }

                        return "<b>{$item}</b><br/><small>Size: {$data->FileSize}</small> <small style=\"color:#888\">(Hash: {$data->hash})</small>";
                    },
                ),
                array(
                    'header' => 'Actions',
                    'type' => 'raw',
                    'value' => <<<ACTION
Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'size'        => 'mini',
    'label'       => 'Remove',
    'icon'        => 'fa fa-ban',
    'htmlOptions' => array(
        'class'   => 'remove-attachment-btn',
        'value'   => \$data->id
    )
), true)
ACTION
                ,
                ),
            ),
        ));
        ?>

        <?php $this->endWidget(); // Form ?>
        <?php $this->endWidget(); // SegBox2 ?>

        <?php

        // Added by Johnmel --- Table for the list of return attachment/files 07-04-2018
        if ($service->hasReturnedAttachment()) {
            $this->beginWidget('application.widgets.SegBox', array(
                'title' => 'List of returned attachments',
                'headerButtons' => array(
                    array(
                        'class' => 'bootstrap.widgets.TbButtonGroup',
                        'buttons' => array(
                            array(
                                'label' => 'Add attached documents',
                                'buttonType' => 'button',
                                'htmlOptions' => array(
                                    'class' => 'return-attachment',
                                    'data-url' => $this->createUrl(
                                        'addDocument'
                                    ),
                                    'data-encounter' => $details->encounter_nr,
                                    'data-transmit' => $details->transmit_no,
                                ),
                                'visible' => $service->checkReturn(),

                            ),
                        ),
                        'htmlOptions' => array(
                            'class' => 'fileupload-buttonbar',
                        ),
                    ),

                ),
                'htmlOptions' => array(
                    'visible' => false,

                    'class' => 'bootstrap-widget-table',
                ),
            ));


            echo CHtml::hiddenField('id', null, array(
                'id' => 'remove-attachment-id',
            ));


            $this->widget('bootstrap.widgets.TbGridView', array(
                'id' => 'returned-attachments',
                'type' => 'striped condensed bordered hover',
                'dataProvider' => $details->searchReturned(),
                'template' => "{items}</br> <div class='pull-right'> {pager} </div>",
                'columns' => array(
                    array(
                        'header' => 'Type',
                        'type' => 'raw',
                        'value' => '"<span class=\"label label-info\">{$data->attachment_type}</span>"',
                    ),
                    array(
                        'header' => 'File name',
                        'type' => 'raw',
                        'value' => function ($data) {
                            $fileUrl = $data->getUrl();
                            if ($fileUrl) {
                                $item = CHtml::link($data->filename, $fileUrl, array('target' => '_blank'));
                            } else {
                                $item = $data->filename;
                            }

                            return "<b>{$item}</b><br/><small>Size: {$data->FileSize}</small> <small style=\"color:#888\">(Hash: {$data->hash})</small>";
                        },
                    ),
                    array(
                        'header' => 'Actions',
                        'type' => 'raw',
                        'value' => <<<ACTION
Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'size' => 'mini',
    'label' => 'Remove',
    'icon' => 'fa fa-ban',
    'htmlOptions' => array(
        'class' => 'remove-attachment-btn',
        'value' => \$data->id
    )
), true)
ACTION
                    ,
                    ),
                ),
            ));

            $this->endWidget();
        } // SegBox2 ?>
	</div>
</div>


<?php

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'cf4Modal',
        'htmlOptions' => array(
            'style' => "width:1250px;margin-left:-625px; overflow-y: auto; margin-top:-55px;",
            'data-backdrop' => "static",
        ),
    )
); ?>


<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4><i class="color-blue fa fa-search"></i> CF4 Xml Generator</h4>
</div>

<div class="modal-body" style="min-height:540px;">


</div>


<div class="modal-footer">

    <?php
    //    $this->widget(
    //        'bootstrap.widgets.TbButton',
    //        array(
    //            'buttonType' => 'submit',
    //            'type' => 'primary',
    //            'icon' => 'fa fa-upload',
    //            'label' => 'Upload CF4',
    //            'htmlOptions' => array(
    //                'data-encounter' => $details->encounter_nr,
    //                'data-transno' => '',
    //                'id' => 'uploadCF4'
    //            )
    //        )
    //    );

    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'type' => 'success',
            'icon' => 'fa fa-upload',
            'label' => 'Download CF4',
            'htmlOptions' => array(
                'data-encounter' => $details->encounter_nr,
                'data-transno' => '',
                'id' => 'downloadCF4',
            ),
        )
    );

    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label' => 'Close',
            'url' => '#',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    );
    ?>

</div>
<?php $this->endWidget(); ?>

<script>

    $(".return-attachment").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: $this.data('url'),
            dataType: 'json',
            type: 'post',
            data: {
                'encounter': $this.data('encounter'),
                'transmit': $this.data('transmit')
            },
            beforeSend: function () {
                Alerts.loading({content: 'Contacting Philhealth Web Service...'});
            },
            success: function (response) {
                if (response === true) {
                    location.reload();
                } else {
                    location.reload();
                }
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

</script>



