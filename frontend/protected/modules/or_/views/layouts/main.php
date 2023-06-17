<head>
    <meta charset="utf-8" />
    <?php
        $baseUrl = Yii::app()->request->baseUrl;
        $cs = Yii::app()->clientScript;
        /* @var $cs CClientScript */
        $cs->registerCssFile($baseUrl . '/css/frontend/application.css')
        ->registerCss('or-added-css',<<<CSS
body div#padding{
    padding:15px;
}

table tbody tr td, table thead tr th{
    font-size: 12px;
}

#alert-success{
    background-color: rgba(51, 192, 222, 0.50);
    border-color: #1F92DB;
    color: #000;
    text-align: center;
}

#alert-failed{
    background-color: rgba(222, 51, 51, 0.77);
    border-color: #1F92DB;
    color: #000;
    text-align: center;
}

#packageTotalPrice{
    color: blue;
}

CSS
);
    ?>
</head>
<body>
    <div id="padding">
        <?php 
            echo $content; 
        ?>
    </div>
</body>