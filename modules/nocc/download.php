<?php
/*
 * $Header: /cvsroot/care2002/Care2002/modules/nocc/download.php,v 1.2 2005/10/29 20:08:10 kaloyan_raev Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * File for downloading the attachments
 */

if (eregi('MSIE', $HTTP_USER_AGENT) || eregi('Internet Explorer', $HTTP_USER_AGENT))
	session_cache_limiter('public');
session_start();
require ('conf.php');

header('Content-Type: application/x-unknown-' . $mime);
// IE 5.5 is weird, the line is not correct but it works
if (eregi('MSIE', $HTTP_USER_AGENT) && eregi('5.5', $HTTP_USER_AGENT))
	header('Content-Disposition: filename=' . urldecode($filename));
else
	header('Content-Disposition: attachment; filename=' . urldecode($filename));

$pop = imap_open('{'.$servr.'}'.$folder, $user, stripslashes($passwd));
$file = imap_fetchbody($pop, $mail, $part);
imap_close($pop);
if ($transfer == 'BASE64')
	$file = imap_base64($file);
elseif($transfer == 'QUOTED-PRINTABLE')
	$file = imap_qprint($file);

header('Content-Length: ' . strlen($file));
echo ($file);
?>