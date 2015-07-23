<?php
if( is_numeric( $id ) )
{
	require_once( './modules/mysticalshop/lib.php' );
	$sql = 'SELECT gold,gems,name FROM '.db_prefix('magicitems').' WHERE id='.$id.' LIMIT 1';
	$result = db_query($sql);

	if( $row = db_fetch_assoc($result) )
	{
		$gold = $row['gold'];
		$gems = $row['gems'];


		$discount = mysticalshop_applydiscount( $gold, $gems, $disnum );
		$sellgold = round(($gold*.75),0);
		$sellgems = round(($gems*.25),0);
		$gem = translate_inline( 'gem' );
		$gem_pl = translate_inline( 'gems' );

		output("`2%s `2contemplates for a moment, then offers you a deal of `^%s gold `2and `%%s %s `2for your `3%s`2.`n`n", $shopkeep, $sellgold, $sellgems, abs( $sellgems ) != 1 ? $gem_pl : $gem, $row['name'] );
		if( $discount )
			output("`3Thinking that price is much too low, %s`3 reminds you the item is currently being sold at a discounted price, thus your refund is set to match.`n`n", $shopkeep);
		output_notl( '`0' );

		addnav("Yes",$from."op=shop&what=sellfinal&id=$id&cat=$cat");
		addnav("No",$from."op=shop&what=enter");
	}
	else
	{
		$item_cats = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak', 'helm', 'glove', 'boot', 'misc' );
		output( '`2%s`2 tries to understand what you are trying to sell, but fails to see it. You realize that you only imagined having "%s`2" and feel a little embarrassed.`0`n`n', $shopkeep, get_module_pref( $item_cats[$cat].'name' ) );
		mysticalshop_destroyitem( $item_cats[$cat] );
		addnav( 'Storefront', $from.'op=shop&what=enter' );
	}
}
else
	output( 'The item can\'t be sold.' );
?>