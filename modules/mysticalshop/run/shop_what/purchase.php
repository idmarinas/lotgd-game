<?php
if( is_numeric( $id ) )
{
	//time to buy our goods. Sorry, no credit cards accepted
	require_once( './modules/mysticalshop/lib.php' );

	$nameprice = mysticalshop_additem( $id, $cat );
	$name = $nameprice['name'];
	$gold = $nameprice['gold'];
	$gems = $nameprice['gems'];
	output("`n`2%s `2compliments you on the purchase of %s`2.`n`n", $shopkeep,$name);
	output("\"You wear it well, friend,\" the shopkeep comments.`0`n`n");
	//subtract price
	mysticalshop_applydiscount( $gold, $gems, $disnum );
	$session['user']['gold'] -= $gold;
	$session['user']['gems'] -= $gems;
	debuglog( 'bought '.$name.' for '.$gold.' gold and '.$gems.' gems.' );
}
else
	output( 'Item not available for purchase.' );
//
modulehook("mysticalshop-buy", array());
addnav( 'Merchandise' );
addnav( 'Overview of Goods', $from.'op=shop&what=enter' );
?>