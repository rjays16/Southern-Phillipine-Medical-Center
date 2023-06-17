<script  language="javascript">
<!-- 
function makeBarcodeLabel(en) {

var num = prompt("No# of Barcode Copy:");
    if (num != null) {
        urlholder="<?php echo $root_path; ?>main/imgcreator/wristband-new-layout-epson-label.php<?php echo URL_REDIRECT_APPEND."&full_en=".$HTTP_SESSION_VARS['sess_full_en']; ?>&en="+en+"&num="+num;
		wclabel<?php echo $sid ?>=window.open(urlholder,"wblabel<?php echo $sid ?>","menubar=no,resizable=yes,scrollbars=yes");
    }
}

function makeWristBands(en)
{	
	urlholder="<?php echo $root_path; ?>main/imgcreator/wristband-new-layout-epson-new.php<?php echo URL_REDIRECT_APPEND."&full_en=".$HTTP_SESSION_VARS['sess_full_en']; ?>&en="+en;
	wclabel<?php echo $sid ?>=window.open(urlholder,"wblabel<?php echo $sid ?>","menubar=no,resizable=yes,scrollbars=yes");
}

function makeBarcodeSticker(en)
{
    urlholder="<?php echo $root_path; ?>main/imgcreator/wristband-barcode-new.php<?php echo URL_REDIRECT_APPEND."&full_en=".$HTTP_SESSION_VARS['sess_full_en']; ?>&en="+en;
    wclabel<?php echo $sid ?>=window.open(urlholder,"wblabel<?php echo $sid ?>","menubar=no,resizable=yes,scrollbars=yes");
}
-->
</script>
