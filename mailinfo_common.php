<?php

require("lib/xajax/xajax_core/xajax.inc.php");
$xajax = new xajax("mailinfo_server.php");
//$xajax->setFlag("debug",true);
$xajax->configure('javascript URI','/lib/xajax/');
$xajax->register(XAJAX_FUNCTION,"mail_status");
$xajax->register(XAJAX_FUNCTION,"timeout_status");

?>
