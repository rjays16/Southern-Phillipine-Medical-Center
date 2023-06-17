{{* author Nick 7-9-2015 *}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hospital Information System">
    <meta name="author" content="Segworks">
    {{*<link rel="icon" href="../../favicon.ico">*}}

    {{if $title}}<title>{{$title}}</title>{{/if}}

    {{* TODO transfer in css file *}}
    <style>
        *{
            margin: 0;
            padding: 0;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        body{
            font-family: Arial, Arial, Helvetica, sans-serif;
            /*font-size: 62.5%;*/
        }
        .footer{
            font-size:1.2em;
            border-top: solid 1px #cfcfcf;
            border-bottom: solid 1px #cfcfcf;
            padding: 0.5em;
            margin-top: 1em;
            background-color: #e4e9f4;
        }
    </style>

    {{if $jquery_enabled}}
        <script type="text/javascript" src="{{$baseUrl}}js/jquery/jquery-1.9.js"></script>
    {{/if}}

    {{if $jquery_ui_enabled}}
        <link rel="stylesheet" href="{{$baseUrl}}js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
        <script type="text/javascript" src="{{$baseUrl}}js/jquery/ui/jquery-ui-1.9.1.js"></script>
    {{/if}}

    {{if $bootstrap_enabled}}
        <link href="{{$baseUrl}}css/bootstrap/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="{{$baseUrl}}js/bootstrap/bootstrap.min.js"></script>
    {{/if}}

    {{if $mustache_enabled}}
        <script type="text/javascript" src="{{$baseUrl}}js/mustache.js"></script>
    {{/if}}

    {{* an array of tags *}}
    {{foreach from=$headTags item=headTag}}
        {{$headTag}}
    {{/foreach}}

</head>
<body>
{{* TODO header/navbar *}}

<div id="wrapper">
    {{if $contentFile ne ""}}
        {{include file=$contentFile}}
    {{/if}}
</div>

{{if $footer_enabled}}
    <div class="footer">
        {{$sCopyright}}
        <span>{{$sPageTime}}</span>
    </div>
{{/if}}
</body>
</html>