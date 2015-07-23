<?php
$playerid = httpget('playerid');
$id = httpget('id');
if( is_numeric( $id ) && is_numeric( $playerid ) )
{
	$giftid = get_module_pref("giftid");
	$sql = 'SELECT name,gold,gems FROM '.db_prefix('magicitems').' WHERE id='.$id.' LIMIT 1';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);	
	$gifteditem = $row['name'];
	$giftgold = $row['gold'];
	$giftgems = $row['gems'];
	require_once( './modules/mysticalshop/lib.php' );
	mysticalshop_applydiscount( $giftgold, $giftgems );

	//We've bought the player the gift, let's subtract the cost now. 
	$session['user']['gold']-=$giftgold;
	$session['user']['gems']-=$giftgems;
	//now, let's set their item data up
	$sql = "SELECT acctid,name,sex FROM ".db_prefix("accounts")." WHERE acctid=$playerid LIMIT 1";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$pname = $row['name'];
	$gifteeid = $row['acctid'];
	$usersex = translate_inline($row['sex']?"She":"He");
	output("`2You have bought `^%s`2 as a gift for `6%s`2!", $gifteditem, $pname );
	output(" %s will receive a mail with details on how to pick it up.`0`n`n",$usersex);
	set_module_pref("gifted",1,"mysticalshop",$gifteeid);
	set_module_pref("giftid",$id,"mysticalshop",$gifteeid);
	set_module_pref("giftcat",$cat,"mysticalshop",$gifteeid);
	//send a mail with name of item, shop, and location (for the clueless)
	$subject = translate_inline("Someone bought you a magical item!");
	$shop = get_module_setting("shopname");
	$loc = get_module_setting("shoploc");


	if( $levellimit == 1 || !is_numeric( $levellimit ) )
		$levelmessage = '';
	else
		$levelmessage = sprintf_translate( ' as soon as you reach level %s', $levellimit );

	if( get_module_setting( 'shopappear' ) == 1 && get_module_pref( 'pass', $gifteeid ) == 0 )
		$levelmessage.= translate_inline( '. You will need a pass (available at the lodge) to be able to find and enter the shop' );

	$message = translate_mail( array( '%s `2has bought you `^%s`2!`n`nYou may pick up your gift at `^%s `2in `^%s`2%s.`0',
			$session['user']['name'], $gifteditem, $shop, $loc, $levelmessage ) );
	require_once("lib/systemmail.php");
	systemmail($gifteeid,$subject,$message);
	debuglog( 'purchased '.$gifteditem.' for '.$pname.' at '.$shop.' in '.$loc.' (gold: '.$giftgold.', gems: '.$giftgems.').', $gifteeid );
}
else
	output( 'Either the item has become unavailable or the person may not receive it.' );
?>