<?php /* Smarty version 2.6.0, created on 2020-02-05 13:14:15
         compiled from common/layout.tpl */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hospital Information System">
    <meta name="author" content="Segworks">
    
    <?php if ($this->_tpl_vars['title']): ?><title><?php echo $this->_tpl_vars['title']; ?>
</title><?php endif; ?>

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

    <?php if ($this->_tpl_vars['jquery_enabled']): ?>
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
js/jquery/jquery-1.9.js"></script>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['jquery_ui_enabled']): ?>
        <link rel="stylesheet" href="<?php echo $this->_tpl_vars['baseUrl']; ?>
js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['bootstrap_enabled']): ?>
        <link href="<?php echo $this->_tpl_vars['baseUrl']; ?>
css/bootstrap/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
js/bootstrap/bootstrap.min.js"></script>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['mustache_enabled']): ?>
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
js/mustache.js"></script>
    <?php endif; ?>

        <?php if (count($_from = (array)$this->_tpl_vars['headTags'])):
    foreach ($_from as $this->_tpl_vars['headTag']):
?>
        <?php echo $this->_tpl_vars['headTag']; ?>

    <?php endforeach; unset($_from); endif; ?>

</head>
<body>

<div id="wrapper">
    <?php if ($this->_tpl_vars['contentFile'] != ""): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['contentFile'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
</div>

<?php if ($this->_tpl_vars['footer_enabled']): ?>
    <div class="footer">
        <?php echo $this->_tpl_vars['sCopyright']; ?>

        <span><?php echo $this->_tpl_vars['sPageTime']; ?>
</span>
    </div>
<?php endif; ?>
</body>
</html>