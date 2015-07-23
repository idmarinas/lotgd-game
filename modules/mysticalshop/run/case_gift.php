<?php
//Time to be nice and send the player a gift!
$shopkeep = get_module_setting("shopkeepname");
$levellimit = get_module_setting( 'giftholdlimit' );
if( $what != 'pickup' || $session['user']['level'] >= $levellimit )
	require_once("./modules/mysticalshop/run/gift_what/$what.php");
else
{
	output( '`2You are about to grab your gift, but %s`2 sizes you up and suddenly puts it away.', $shopkeep );
	output( '%s`2\'s explanation follows shortly: "You need to get stronger to be able to handle the magical properties of this gift.', $shopkeep );
	output( 'It will be safe here. Come back when you are at level `^%s`2 or so."`0', $levellimit );
}
addnav( 'Merchandise' );
addnav( 'Overview of Goods', $from.'op=shop&what=enter' );
?>