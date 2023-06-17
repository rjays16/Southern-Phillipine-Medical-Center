<?php
/**
 * View file for devices/camera route
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segrworks Technolfies Corporation
 */

/**
 * The following variables are expected by this view script:
 *
 * @var Controller $this
 */
	$jsPath = Yii::app()->request->baseUrl.'/frontend/protected/node_modules/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <style type="text/css" media="screen">
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        #camera {
            width: 100%;
            height: 100%;
        }
    </style>
    <script src="<?=$jsPath?>webcamjs/webcam.js"></script>
    <script type="text/javascript">
        // Put event listeners into place
        window.addEventListener("DOMContentLoaded", function() {
            Webcam.set({
                dest_width: 422,
                dest_height: 352,
                image_format: 'jpeg',
                jpeg_quality: 90,
                force_flash: false,
                flip_horiz: true,
                fps: 45
            });
            Webcam.set( 'constraints', {

                optional: [
                    { minWidth: 200 }
                ]
            });
            Webcam.attach( '#camera' );
        });

        window.addEventListener('message',function(event) {
            if (event.data === 'closeDevice') {
                Webcam.reset();
            }
        });

    </script>
</head>
<body>
    <!--
        Ideally these elements aren't created until it's confirmed that the
        client supports video/camera, but for the sake of illustrating the
        elements involved, they are created with markup (not JavaScript)
    -->
    <div id="camera"></div>
</body>
</html>