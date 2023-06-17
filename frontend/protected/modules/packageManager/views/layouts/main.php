<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8"/>
    <title>Package Manager</title>

    <?php
        $baseUrl = Yii::app()->request->baseUrl;
        $cs = Yii::app()->clientScript;
        $cs->registerCssFile($baseUrl . '/css/frontend/application.css')
            ->registerCssFile($baseUrl . '/css/frontend/alerts.css')
            ->registerCssFile($baseUrl . '/css/frontend/animate.css')
            ->registerScriptFile($baseUrl . '/js/mustache.js')
            ->registerScriptFile($baseUrl . '/js/jquery/jquery.blockUI.js')
            ->registerScriptFile($baseUrl . '/js/frontend/alert.js')
            ->registerCss('package-added-css',<<<CSS
                body ul.breadcrumb{
                    margin-top: -48px;
                }
                body div#padding{
                    padding:10px;
                }

                table tbody tr td, table thead tr th{
                    font-size: 12px;
                }
CSS
                );
    ?>

</head>

<body>

    <?php if(isset($this->breadcrumbs)): ?>
    <?php
        $this->widget('bootstrap.widgets.TbBreadCrumbs', array(
            'homeLink' => false,
            'links' => $this->breadcrumbs,
        ));
    ?>
    <?php endif ?>

    <div id="padding">
        <?php echo $content; ?>
    </div>

</body>
</html>