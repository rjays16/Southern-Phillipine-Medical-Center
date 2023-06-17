<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path . '/include/care_api_classes/class_user_token.php');
session_start();

$user_token = new UserToken;
$auth = $user_token->repUserLogin();

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="../../js/messi/messi.js"></script>

<link rel="stylesheet" href="../../css/messi/messi.css" type="text/css" />

<script type="text/javascript">
    var $J = jQuery.noConflict();

    $J(document).ready(function(){
        new Messi('Please wait generating Report. This may take few seconds.', {title: 'Generating...', titleClass: 'anim warning'});
        window.location = "<?php echo $_SESSION['loading_report_link']; ?>";
        //alert("session: <?php echo $_SESSION['loading_report_link']; ?>");
        
        <?php if(trim($_GET['report_type']) == 'excel'){ ?>
        	setInterval(
	            function(){ 
	                /*$.post(
	                    '<?= $root_path ?>modules/reports/sessiondownloadreport.php',
	                    {},
	                    function(data){
	                    	//alert('data:'+data);
	                        if(data == 'done'){*/
	                        	//setting the time to 5 minutes
	                            new Messi('Please close this window after saving your file!', {title: 'File created...'});
	                            setTimeout(function() {
	                                 window.close();
	                               }, (5*60*1000));
	                    /*    }
	                    }
	                );*/
	            }, 
	            2000
	        );
	        
        <?php } ?>
        	
    });
    
</script>
</head>
<body></body>
</html>