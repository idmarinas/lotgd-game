<?php
$shopkeep = get_module_setting("shopkeepname");
$shopdesc = get_module_setting("shopdesc");
$hasgift = get_module_pref("gifted");
$disnum = get_module_setting("discountnum");
if ($disnum == 0) $disnum = 1;
$id = httpget('id');
require_once("modules/mysticalshop/run/shop_what/$what.php");
?>