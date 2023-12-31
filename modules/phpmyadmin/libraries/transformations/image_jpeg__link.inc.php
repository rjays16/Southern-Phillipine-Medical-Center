<?php
/* $Id: image_jpeg__link.inc.php,v 1.2 2005/10/29 20:08:13 kaloyan_raev Exp $ */
// vim: expandtab sw=4 ts=4 sts=4:

if (!defined('PMA_TRANSFORMATION_IMAGE_JPEG__LINK')){
    define('PMA_TRANSFORMATION_IMAGE_JPEG__LINK', 1);
    
    function PMA_transformation_image_jpeg__link($buffer, $options = array()) {
        include('./libraries/transformations/global.inc.php');
        
        $transform_options = array ('string' => '<a href="transformation_wrapper.php' . $options['wrapper_link'] . '" alt="[__BUFFER__]">[BLOB]</a>');
        $buffer = PMA_transformation_global_html_replace($buffer, $transform_options);
        
        return $buffer;
    }
}
