<?php
if( is_numeric( $id ) )
{
	//time to buy our goods. Sorry, no credit cards accepted
	require_once( './modules/mysticalshop/lib.php' );

	$nameprice = mysticalshop_additem( $id, $cat, false );
	$name = $nameprice['name'];
	if( $nameprice['name'] !== false )
	{
		$gold = $nameprice['gold'];
		$gems = $nameprice['gems'];
		output("`n`2%s `2compliments you on the purchase of %s`2.`n`n", $shopkeep,$name);
		output("\"You wear it well, friend,\" the shopkeep comments.");
		//subtract price
		mysticalshop_applydiscount( $gold, $gems, $disnum );
		$session['user']['gold'] -= $gold;
		$session['user']['gems'] -= $gems;
		debuglog( 'bought '.$name.' for '.$gold.' gold and '.$gems.' gems.' );
	}
	output_notl( '`0`n`n' );
}
else
	output( 'Item not available for purchase.' );
//
modulehook("mysticalshop-buy", array());
addnav( 'Merchandise' );
addnav( 'Overview of Goods', $from.'op=shop&what=enter' );
?>