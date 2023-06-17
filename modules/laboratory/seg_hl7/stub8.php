<?php
    ob_start();
    echo "seghis sample lis result!";
    $output_so_far = ob_get_contents();
    ob_clean();
    $fullpath = "/srv/www/lisresults/samplelis2.txt";
    file_put_contents($fullpath, $output_so_far);
    echo $output_so_far;

    if(file_exists(($fullpath))){
        echo "true exists";
    }else{
        echo "false exists";
    }
?>
