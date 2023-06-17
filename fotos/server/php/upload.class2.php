<?php
/*
 * jQuery File Upload Plugin PHP Class 5.15
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
 
#edited by VAN 11-03-2012
#require('./roots.php');
#require_once($root_path.'include/care_api_classes/class_core.php');
#require_once('class_upload.php');
class UploadHandler
{
    protected $options;
    
    #added by VAN 11-13-2012
    /**
     * @var WSPolicy $plicy
     */
    protected $policy;
    /**
     * @var WSSecurityToken $securityToken
     */
    protected $securityToken;
    
    protected $key_hie = '../../../protected/hie/certificates/segworks.key';
    protected $cert_hie = '../../../protected/hie/certificates/segworks.cert';
    protected $cert_hosp = '../../../protected/hie/certificates/alice_cert.cert';
    protected $key_hosp = '../../../protected/hie/certificates/alice_key.pem';
    protected $policy_url = '../../../protected/hie/config/policy.xml';
    protected $wsdl = 'http://192.168.1.185/segtdd/hie/ecs/wsdl';
    #-----------------
    
    // PHP File Upload error message codes:
    // http://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height'
    );
        
    function __construct($options = null, $initialize = true) {
        $this->options = array(
            'script_url' => $this->get_full_url().'/',
            'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/files/',
            'upload_url' => $this->get_full_url().'/files/',
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            'access_control_allow_origin' => '*',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            
            #edited by VAN 11-17-2012
            #'accept_file_types' => '/^application\/(pdf)$/',
            
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,
            'image_versions' => array(
                // Uncomment the following version to restrict the size of
                // uploaded images. You can also add additional versions with
                // their own upload directories:
                /*
                'large' => array(
                    'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/files/',
                    'upload_url' => $this->get_full_url().'/files/',
                    'max_width' => 1920,
                    'max_height' => 1200,
                    'jpeg_quality' => 95
                ),
                */
                'thumbnail' => array(
                    //'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/thumbnails/',
                    //'upload_url' => $this->get_full_url().'/thumbnails/',
                    'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/thumbnails/'.$_REQUEST['encounter_nr_dir'],
                    'upload_url' => $this->get_full_url().'/thumbnails/'.$_REQUEST['encounter_nr_dir'],
                    'max_width' => 80,
                    'max_height' => 80
                )
            )
        );
        
        if ($options) {
            # array_replace_recursive will work with older PHP versions before 5.3.0
            #$this->options = array_replace_recursive($this->options, $options);
            if (function_exists('array_replace_recursive')){
                $this->options = array_replace_recursive($this->options, $options);
            }else{
                $this->options = $this->array_replace_recursive($this->options, $options);
            }
        }
        if ($initialize) {
            $this->initialize();
        }
    }
    
    # to bypass the array_replace_recursive since it will work with older PHP versions before 5.3.0
    #override the function
    public function array_replace_recursive($array, $array1){
      foreach ($array1 as $key => $value){
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))){
                $array[$key] = array();
            }
  
            // overwrite the value in the base array
            if (is_array($value)){
                $value = recurse($array[$key], $value);
            }
            $array[$key] = $value;
      }
      return $array;
    }

    protected function initialize() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
            case 'HEAD':
                $this->head();
                break;
            case 'GET':
                $this->get();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $this->delete();
                } else {
                    $this->post();
                }
                break;
            case 'DELETE':
                $this->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
    }

    protected function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }

    protected function set_file_delete_url($file) {
        #edited by VAN 10-30-2012
        $file->delete_url = $this->options['script_url']
        #    .'?file='.rawurlencode($file->name);
             .'index.php?file='.rawurlencode($file->name).'&encounter_nr='.$_REQUEST['encounter_nr'].'&uploader='.$_REQUEST['uploader'];
              
        $file->delete_type = $this->options['delete_type'];
        
        if ($file->delete_type !== 'DELETE') {
            $file->delete_url .= '&_method=DELETE';
        }
    }

    // Fix for overflowing signed 32 bit integers,
    // works for sizes up to 2^32-1 bytes (4 GiB - 1):
    protected function fix_integer_overflow($size) {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }

    protected function get_file_size($file_path, $clear_stat_cache = false) {
        if ($clear_stat_cache) {
            clearstatcache();
        }
        return $this->fix_integer_overflow(filesize($file_path));

    }

    protected function get_file_object($file_name) {
        $file_path = $this->options['upload_dir'].$file_name;
        if (is_file($file_path) && $file_name[0] !== '.') {
            $file = new stdClass();
            $file->name = $file_name;
            $file->size = $this->get_file_size($file_path);
            $file->url = $this->options['upload_url'].rawurlencode($file->name);
            foreach($this->options['image_versions'] as $version => $options) {
                if (is_file($options['upload_dir'].$file_name)) {
                    $file->{$version.'_url'} = $options['upload_url']
                        .rawurlencode($file->name);
                }
            }
            $this->set_file_delete_url($file);
            return $file;
        }
        return null;
    }

    protected function get_file_objects() {
        return array_values(array_filter(array_map(
            array($this, 'get_file_object'),
            scandir($this->options['upload_dir'])
        )));
    }

    protected function create_scaled_image($file_name, $options) {
        $file_path = $this->options['upload_dir'].$file_name;
        $new_file_path = $options['upload_dir'].$file_name;
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                return copy($file_path, $new_file_path);
            }
            return true;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                    $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                    $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0, 0, 0, 0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path, $image_quality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }

    protected function get_error_message($error) {
        return array_key_exists($error, $this->error_messages) ?
            $this->error_messages[$error] : $error;
    }

    protected function validate($uploaded_file, $file, $error, $index) {
        if ($error) {
            $file->error = $this->get_error_message($error);
            return false;
        }
        if (!$file->name) {
            $file->error = $this->get_error_message('missingFileName');
            return false;
        }
        if (!preg_match($this->options['accept_file_types'], $file->name)) {
            $file->error = $this->get_error_message('accept_file_types');
            return false;
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = $this->get_file_size($uploaded_file);
        } else {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }
        if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
            ) {
            $file->error = $this->get_error_message('max_file_size');
            return false;
        }
        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size']) {
            $file->error = $this->get_error_message('min_file_size');
            return false;
        }
        if (is_int($this->options['max_number_of_files']) && (
                count($this->get_file_objects()) >= $this->options['max_number_of_files'])
            ) {
            $file->error = $this->get_error_message('max_number_of_files');
            return false;
        }
        list($img_width, $img_height) = @getimagesize($uploaded_file);
        if (is_int($img_width)) {
            if ($this->options['max_width'] && $img_width > $this->options['max_width']) {
                $file->error = $this->get_error_message('max_width');
                return false;
            }
            if ($this->options['max_height'] && $img_height > $this->options['max_height']) {
                $file->error = $this->get_error_message('max_height');
                return false;
            }
            if ($this->options['min_width'] && $img_width < $this->options['min_width']) {
                $file->error = $this->get_error_message('min_width');
                return false;
            }
            if ($this->options['min_height'] && $img_height < $this->options['min_height']) {
                $file->error = $this->get_error_message('min_height');
                return false;
            }
        }
        return true;
    }

    protected function upcount_name_callback($matches) {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return ' ('.$index.')'.$ext;
    }

    protected function upcount_name($name) {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            array($this, 'upcount_name_callback'),
            $name,
            1
        );
    }

    protected function trim_file_name($name, $type, $index, $content_range) {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
        // Add missing file extension for known image types:
        if (strpos($file_name, '.') === false &&
            preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $file_name .= '.'.$matches[1];
        }
        $uploaded_bytes = $this->fix_integer_overflow(intval($content_range[1]));
        while(is_file($this->options['upload_dir'].$file_name)) {
            if ($uploaded_bytes === $this->get_file_size(
                    $this->options['upload_dir'].$file_name)) {
                break;
            }
            $file_name = $this->upcount_name($file_name);
        }
        return $file_name;
    }

    protected function handle_form_data($file, $index) {
        // Handle form data, e.g. $_REQUEST['description'][$index]
        #try add here
    }

    protected function orient_image($file_path) {
          $exif = @exif_read_data($file_path);
        if ($exif === false) {
            return false;
        }
          $orientation = intval(@$exif['Orientation']);
          if (!in_array($orientation, array(3, 6, 8))) {
              return false;
          }
          $image = @imagecreatefromjpeg($file_path);
          switch ($orientation) {
              case 3:
                  $image = @imagerotate($image, 180, 0);
                  break;
              case 6:
                  $image = @imagerotate($image, 270, 0);
                  break;
              case 8:
                  $image = @imagerotate($image, 90, 0);
                  break;
              default:
                  return false;
          }
          $success = imagejpeg($image, $file_path);
          // Free up memory (imagedestroy does not delete files):
          @imagedestroy($image);
          return $success;
    }

    #edited by VAN 11-03-2012
    /*protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
            $index = null, $content_range = null) {*/
     protected function handle_file_upload($uploaded_file, $data, $error,
            $index = null, $content_range = null) {       
        extract($data);        
        $file = new stdClass();
        $file->name = $this->trim_file_name($name, $type, $index, $content_range);
        $file->size = $this->fix_integer_overflow(intval($size));
        $file->type = $type;
        
        if ($this->validate($uploaded_file, $file, $error, $index)) {
            $this->handle_form_data($file, $index);
            $file_path = $this->options['upload_dir'].$file->name; 
            $append_file = $content_range && is_file($file_path) &&
                $file->size > $this->get_file_size($file_path);
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                if ($append_file) {
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }
            $file_size = $this->get_file_size($file_path, $append_file);
            if ($file_size === $file->size) {
                if ($this->options['orient_image']) {
                    $this->orient_image($file_path);
                }
                $file->url = $this->options['upload_url'].rawurlencode($file->name);
                foreach($this->options['image_versions'] as $version => $options) {
                    if ($this->create_scaled_image($file->name, $options)) {
                        if ($this->options['upload_dir'] !== $options['upload_dir']) {
                            $file->{$version.'_url'} = $options['upload_url']
                                .rawurlencode($file->name);
                        } else {
                            $file_size = $this->get_file_size($file_path, true);
                        }
                    }
                }
                
                #added by VAN 11-06-2012
                $data['name'] = $file->name;
                $this->add_info_db($data);
                #get data from database
                #$infodb = $this->get_files_db($data['name']);
                #$infodb = $this->get_files_db($file->name);
                
            } else if (!$content_range && $this->options['discard_aborted_uploads']) {
                unlink($file_path);
                $file->error = 'abort';
            }
            $file->size = $file_size;
            $this->set_file_delete_url($file);
        }
        
        return $file;
    }

    protected function generate_response($content, $print_response = true) {
        if ($print_response) {
            $json = json_encode($content);
            $redirect = isset($_REQUEST['redirect']) ?
                stripslashes($_REQUEST['redirect']) : null;
            if ($redirect) {
                header('Location: '.sprintf($redirect, rawurlencode($json)));
                return;
            }
            $this->head();
            echo $json;
        }
        return $content;
    }

    public function head() {
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        if ($this->options['access_control_allow_origin']) {
            header('Access-Control-Allow-Origin: '.$this->options['access_control_allow_origin']);
            header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: X-File-Name, X-File-Type');            
        }
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
    }

    #edited by VAN 11-06-2012
    public function get($print_response = true) {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        /*if ($file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = $this->get_file_objects();
        }*/
        
        $info = $this->get_files_db($file_name);
        
        return $this->generate_response($info, $print_response);
    }
    
    #added by VAN 11-06-2012
    public function filesize_format($size, $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB')){
        if ($size == 0) return('n/a');
        return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
    }

    function get_files_db($file_name=''){
        global $dbcon;
        
        $encounter_nr= $_REQUEST['encounter_nr'];
        $uploads_array = array();
        $cond = '';
        
        if ($file_name)
            $cond = " AND filename = '$file_name' ";
        
        $this->sql = "SELECT * FROM seg_claim_attachments 
                      WHERE encounter_nr='$encounter_nr' AND is_deleted=0
                      $cond
                      ORDER BY create_date";
        
        $result = $dbcon->Execute($this->sql);
        while ($row=$result->FetchRow()) {
            $file = new stdClass();
            $file->id = $row['attachment_id'];
            $file->name = $row['filename'];
            $file->size = $this->filesize_format($row['filesize']);
            $file->type = $row['filetype'];
            $file->url = $row['attachment_path']."/".$row['filename'];
            $file->delete_url = $row['url']."/index.php?file=".rawurlencode($row['filename'])."&encounter_nr=".$row['encounter_nr']."&uploader=".$_REQUEST['uploader']."&id=".$row['attachment_id'];
            $file->delete_type = "DELETE";
            $file->description = $row['description'];
            $file->doc_type = $row['document_type'];
            
            $file->is_uploaded = $row['is_uploaded'];
            
            $url_thumb = $row['attachment_path_thumb']."/".$row['filename'];
            $file->thumbnail_url = '';
            
            $header_response = get_headers($url_thumb, 1);
            if ( strpos( $header_response[0], "404" ) !== false ){
                $file->thumbnail_url = $row['url']."/thumbnails/blank.jpg";
            }else{
                $file->thumbnail_url = $url_thumb;
            }
            array_push($uploads_array,$file);
        }
        return $uploads_array;
    }
    #----------------------
    
    #edited by VAN 11-03-2012
    public function post($print_response = true) {
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete();
        }
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = isset($_SERVER['HTTP_CONTENT_RANGE']) ?
            split('[^0-9]+', $_SERVER['HTTP_CONTENT_RANGE']) : null;
        $size =  $content_range ? $content_range[3] : null;
        $info = array();
        if ($upload && is_array($upload['tmp_name'])) {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            $objname = '';
            
            $data['bill_nr'] = $_REQUEST['bill_nr'];
            $data['name_last'] = $_REQUEST['name_last'];
            $data['name_first'] = $_REQUEST['name_first'];
            $data['name_middle'] = $_REQUEST['name_middle'];
            foreach ($upload['tmp_name'] as $index => $value) {
                $data['name']  =  isset($_SERVER['HTTP_X_FILE_NAME']) ?
                                 $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index];
                $data['size']  =  $size ? $size : $upload['size'][$index];
                $data['type']  =  isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                                 $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index];              
                
                $fname = str_replace('.','_',$data['name']);
                
                $data['description']  = $_REQUEST['desc'.$fname];
                $data['doc_type'] = $_REQUEST['dtype'.$fname];
                
                $data['uploader'] = $_REQUEST['uploader'];
                #$objname = 'desc'.$data['name'];
                #echo "here = ".$objname;
                #echo "<br>multiple = ".$index.">>".$_REQUEST[$objname];
                /*$info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    isset($_SERVER['HTTP_X_FILE_NAME']) ?
                        $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                    $size ? $size : $upload['size'][$index],
                    isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                        $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );*/                           
                $info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    $data,
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
            /*$info[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                isset($_SERVER['HTTP_X_FILE_NAME']) ?
                    $_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
                        $upload['name'] : null),
                $size ? $size : (isset($upload['size']) ?
                        $upload['size'] : $_SERVER['CONTENT_LENGTH']),
                isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                    $_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
                        $upload['type'] : null),
                isset($upload['error']) ? $upload['error'] : null,
                null,
                $content_range
            );*/
            
            $data['name']  =  isset($_SERVER['HTTP_X_FILE_NAME']) ?
                             $_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
                             $upload['name'] : null);
            $data['size']  =  $size ? $size : (isset($upload['size']) ?
                             $upload['size'] : $_SERVER['CONTENT_LENGTH']);
            $data['type']  =  isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                             $_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
                             $upload['type'] : null);
            
            $fname = str_replace('.','_',$data['name']);
            $data['description']  = $_REQUEST['desc'.$fname];
            $data['doc_type'] = $_REQUEST['dtype'.$fname];
                
            $data['uploader'] = $_REQUEST['uploader'];
            
            $info[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                $data,
                isset($upload['error']) ? $upload['error'] : null,
                null,
                $content_range
            );                                  
        }
        
        #added by VAN 11-06-2012
        #$ok = $this->add_info_db($data);
        #get data from database by attachment id
        #$infodb = $this->get_files_db($data['name']);
        $infodb = $this->get_files_db($info[0]->name);
        
        #return $this->generate_response($info, $print_response);
        return $this->generate_response($infodb, $print_response);
        
    }

    public function delete($print_response = true) {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        $file_path = $this->options['upload_dir'].$file_name;
        
        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
        if ($success) {
            foreach($this->options['image_versions'] as $version => $options) {
                $file = $options['upload_dir'].$file_name;
                $info = '';
                if (is_file($file)) {
                    unlink($file);
                    #added by VAN 10-30-2012
                    $this->delete_info_db($file_name);
                    #$info = $this->get_files_db($file_name);
                    $info = 'Successfully deleted...';
                }else
                    $info = 'Deleting file failed...';
            }
        }
        return $this->generate_response($info, $print_response);
    }
    
    #added by VAN 11-03-2012
    #save info to database
    function add_info_db($data){
        global $dbcon;
        
        extract($data);
        $encounter_nr= $_REQUEST['encounter_nr'];
        $url = $this->get_full_url();
        $path = $this->get_full_url().'/files/'.$_REQUEST['encounter_nr_dir'];
        $path_thumb = $this->get_full_url().'/thumbnails/'.$_REQUEST['encounter_nr_dir'];
        #$date_created = date("Y-m-d H:i:s");
        #$timezone = date_default_timezone_get();
        $timezone = 'Asia/Manila';
        date_default_timezone_set($timezone);
        $date_created = date('Y-m-d H:i:s', time());
        
        $history = 'CONCAT(IF(history IS NULL, "", history),"Create '.$date_created.' '.$uploader.'\n")';
        
        $index = 'encounter_nr, filename, filetype, filesize, description, url, attachment_path, attachment_path_thumb, document_type, create_id, create_date, history, claimNumber';
        $values = "'".$encounter_nr."','".$name."','".$type."','".$size."','".$description."','".$url."','".$path."','".$path_thumb."','".$doc_type."','".$uploader."','".$date_created."',".$history.",'".$bill_nr."'";
        #$values = "'".$encounter_nr."','".$name."','".$type."','".$size."','".$description."','".$url."','".$path."','".$path_thumb."','".$doc_type."','".$uploader."',$date_created,".$history."";
        
        $this->sql = "INSERT INTO seg_claim_attachments($index)
                       VALUES($values)";
        #echo $this->sql;
        
        if ($dbcon->Execute($this->sql)) {
            if ($dbcon->Affected_Rows()) {  
                
                #instantiate param
                $path = $this->get_full_url().'/files/'.$_REQUEST['encounter_nr_dir'].$name;
                $details = pathinfo($path);
                $extension =  $details['extension'];
                
                $params = array(
                        'claimNumber' => $bill_nr,
                        'memberLastName' => $name_last,
                        'memberFirstName' => $name_first,
                        'memberMiddleName' => $name_middle,
                        'documentType' => $doc_type,
                        'fileName' => $name,
                        'extension' => $extension,
                        'mimeType' => $type,
                        'remarks' => '',           
                        'contents' => base64_encode(file_get_contents($path))
                        ); 
                #print_r($params,1);
                #upload to HITP
                $rs = $this->uploadClaimDocument($params);
                #print_r($rs);
                
                #update is_upload field
                if ($rs){
                    $attachment_id = $dbcon->Insert_ID();
                    
                    $params2 = array(
                        'attachment_id' => $attachment_id,
                        'encounter_nr' => $encounter_nr,
                        'fileId' => $rs['fileId'],
                        'accessUrl' => $rs['accessUrl'],
                        'is_uploaded' => '1'
                        );
                        
                    $this->setFlagUpload($params2);
                }
                
                return TRUE;
            }else return FALSE;
        }else return FALSE;
    }
    
    function setFlagUpload($data){
        global $dbcon;
        
        extract($data);
        $uploader = $_REQUEST['uploader'];
        $timezone = 'Asia/Manila';
        date_default_timezone_set($timezone);
        $date_created = date('Y-m-d H:i:s', time());
        $history = 'CONCAT(IF(history IS NULL, "", history),"UPLOADED to HITP '.$date_created.' '.$uploader.'\n")';
        
        $this->sql = "UPDATE seg_claim_attachments SET
                             is_uploaded='$is_uploaded', 
                             fileId = '$fileId',
                             accessUrl = '$accessUrl',
                             modify_id = '$uploader',
                             modify_date = '$date_created',
                             history = $history
                             WHERE encounter_nr='$encounter_nr'
                             AND attachment_id = '$attachment_id'";
        #print_r($this->sql);
        if ($dbcon->Execute($this->sql)) {
            if ($dbcon->Affected_Rows()) {
                return TRUE;
            }else return FALSE;
        }else return FALSE;    
    }
    
    function get_info_db($attachment_id){
        global $dbcon;
        
        $encounter_nr = $_REQUEST['encounter_nr'];
        
        $this->sql = "SELECT * FROM seg_claim_attachments 
                      WHERE attachment_id='$attachment_id' AND encounter_nr='$encounter_nr'";
        
        if ($this->result = $dbcon->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
                return $this->result->FetchRow();
            }else{
                return FALSE;
            }
        }else return FALSE;
    }    
    
    function delete_info_db($objId){
        global $dbcon;
        
        #$this->sql = "DELETE FROM seg_claim_attachments where filename='".$objId."'";
        
        #$uploader = $_COOKIE['aufnahme_userrmmiknr6fp2hn6s0orlv9dmlm1'];
        $uploader = $_REQUEST['uploader'];
        $encounter_nr = $_REQUEST['encounter_nr'];
        $attachment_id = $_REQUEST['id'];
        #$date_created = date("Y-m-d H:i:s");
        $timezone = 'Asia/Manila';
        date_default_timezone_set($timezone);
        $date_created = date('Y-m-d H:i:s', time());
        $history = 'CONCAT(IF(history IS NULL, "", history),"DELETED '.$date_created.' '.$uploader.'\n")';
        
        #logically deleted
        $this->sql = "UPDATE seg_claim_attachments 
                             SET is_deleted=1, 
                             modify_id = '$uploader',
                             modify_date = '$date_created',
                             history = $history
                             WHERE filename='".$objId."'
                             AND encounter_nr='$encounter_nr'
                             AND attachment_id = '$attachment_id'";
        
        if ($dbcon->Execute($this->sql)) {
            if ($dbcon->Affected_Rows()) {
                
                $rs = $this->get_info_db($attachment_id);
                $params = array(
                            'claimNumber' => $rs['claimNumber'],
                            'fileId' => $rs['fileId']
                        ); 
                
                print_r($params);
                #delete file to HITP
                $rs = $this->deleteClaimDocument($params);
                
                return TRUE;
            }else return FALSE;
        }else return FALSE;
    }
    
    
    /**
     *
     * @return string
     */
    public function getWsdlPath()
    {
        #return '../../../protected/hie/config/ECSService.wsdl';
        #return 'http://192.168.1.185/segtdd/hie/ecs/wsdl';
        return $this->wsdl;
    }
    
    public function getPolicy()
    {
        if (empty($this->policy)) {
            #$policyXml = file_get_contents('../../../protected/hie/config/policy.xml');
            $policyXml = file_get_contents($this->policy_url);
            $this->policy = new WSPolicy($policyXml);
        }

        return $this->policy;
    }

    /**
     * Creates the WSSecurityToken instance
     * @return WSSecurityToken
     */
    public function getSecurityToken()
    {
        if (empty($this->securityToken)) {
            #$cert = ws_get_cert_from_file('../../../protected/hie/certificates/segworks.cert');
            #$key = ws_get_key_from_file('../../../protected/hie/certificates/segworks.key');
            $cert = ws_get_cert_from_file($this->cert_hie);
            $key = ws_get_key_from_file($this->key_hie);

            $this->securityToken = new WSSecurityToken(array(
                'certificate' => $cert,
                'privateKey' => $key,
            ));
        }

        return $this->securityToken;
    }
    
    public function uploadClaimDocument($params){
        #extract($testData);

        #$cert = ws_get_cert_from_file("../../../protected/hie/certificates/alice_cert.cert");
        #$key = ws_get_key_from_file("../../../protected/hie/certificates/alice_key.pem");
        #$rootCert = ws_get_cert_from_file("../../../protected/hie/certificates/segworks.cert");
        $cert = ws_get_cert_from_file($this->cert_hosp);
        $key = ws_get_key_from_file($this->key_hosp);
        $rootCert = ws_get_cert_from_file($this->cert_hie);
        
        $securityToken = new WSSecurityToken(array(
            "privateKey" => $key,
            "certificate" => $cert,
            "receiverCertificate" => $rootCert
        ));
        
        // segworkstechcert.pem
        // clientcert.pem
        $client = new WSClient(array(
            "useWSA" => true,
            'wsdl' =>  $this->getWsdlPath(),
            "policy" => $this->getPolicy(),
            "securityToken" => $securityToken,
        ));

        $proxy = $client->getProxy();
        #print_r($proxy,1);
        try {    
            $result = $proxy->uploadClaimDocument($params);
            #print_r($result,1);
            return $result;
        
        } catch (Exception $e) {
            if ($e instanceof WSFault) {
                return $e->Reason;
            } else {
                #printf("Message = %s\n",$e->getMessage());
                return $e->getMessage();
            }
        }    
            
    }
    
    public function deleteClaimDocument($params){
        
        $cert = ws_get_cert_from_file($this->cert_hosp);
        $key = ws_get_key_from_file($this->key_hosp);
        $rootCert = ws_get_cert_from_file($this->cert_hie);
        
        $securityToken = new WSSecurityToken(array(
            "privateKey" => $key,
            "certificate" => $cert,
            "receiverCertificate" => $rootCert
        ));
        
        // segworkstechcert.pem
        // clientcert.pem
        $client = new WSClient(array(
            "useWSA" => true,
            'wsdl' =>  $this->getWsdlPath(),
            "policy" => $this->getPolicy(),
            "securityToken" => $securityToken,
        ));

        $proxy = $client->getProxy();
        #print_r($proxy,1);
        try {    
            $result = $proxy->deleteClaimDocument($params);
            #print_r($result,1);
            return $result;
        
        } catch (Exception $e) {
            if ($e instanceof WSFault) {
                return $e->Reason;
            } else {
                return $e->getMessage();
            }
        }    
            
    }
    
    #-----------------------

}
