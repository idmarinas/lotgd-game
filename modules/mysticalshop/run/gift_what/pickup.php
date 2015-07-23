<?php
$giftid = get_module_pref("giftid");
$cat = get_module_pref("giftcat");
//First, let's determine just what the item is, and record it thusly
require_once( './modules/mysticalshop/lib.php' );

mysticalshop_additem( $giftid, $cat );
output("`n`2%s compliments you on your gift:`n`n", $shopkeep);
output( '`2"It suits you quite well, %s`2. Do enjoy it," %s `2says.`0`n`n', $session['user']['name'], $shopkeep );
debuglog( 'picked up the gift (ID is '.$giftid.').' );
//It's done!
modulehook("mysticalshop-gift", array());
set_module_pref("gifted",0);
?>