<?php
if( is_numeric( $id ) )
{
	$sql = 'SELECT * FROM '.db_prefix('magicitems').' WHERE id='.$id.' LIMIT 1';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$what = $row['name'];
	$cat = $row['category'];
	$verbose = $row['bigdesc'];
	$rare = $row['rare'];
	$rarenum = $row['rarenum'];
	$gold = $row['gold'];
	$gems = $row['gems'];

	output("`2You have chosen to view %s`2.`n`n", $what);
	//display that nice big description you typed out here
	if( $verbose != '' )
	{
		output("`3%s`n`n", $verbose);
	//otherwise, let's display a default one in it's place
	}else{
		output("`3No extended description for this item is available.`n`n");
	}
	if (get_module_setting("showstats")){
		$point = translate_inline( 'point' );
		$points = translate_inline( 'points' );
		$attack = $row['attack'];
		$defense = $row['defense'];
		$charm = $row['charm'];
		$turns = $row['turns'];
		$favor = $row['favor'];
		if ($attack<>0)
			output("`&This item's enchantments will alter your attack by `^%s `&%s.`n", $attack, abs( $attack ) != 1 ? $points : $point );
		if ($defense<>0)
			output("`&This item's enchantments will alter your defense by `^%s `&%s.`n", $defense, abs( $defense ) != 1 ? $points : $point );
		if ($charm<>0)
			output("`&This item's enchantments will alter your charm by `^%s `&%s.`n", $charm, abs( $charm ) != 1 ? $points : $point );
		if ($row['hitpoints']<>0)
			output("`&This item's enchantments will alter your maximum hit points by `^%s`&.`n",$row['hitpoints']);
		if ($turns<>0)
		{
			$trn = translate_inline( 'turn' );
			$trns = translate_inline( 'turns' );
			output("`&This item's enchantments will grant `^%s `&extra %s.`n", $turns, abs( $turns ) != 1 ? $trns : $trn );
		}
		if ($favor<>0)
			output( '`&This item\'s enchantments will alter your favor with %s`& by `^%s `&%s.`n', getsetting( 'deathoverlord', '`$Ramius' ), $favor, abs( $favor ) != 1 ? $points : $point );
	}
	//Now let's check if they're buying or selling an item.
	$gem = translate_inline( 'gem' );
	$gem_pl = translate_inline( 'gems' );
	output( '`n`@The cost of %s`@ is `^%s gold`@ and `%%s %s`@.', $what, $gold, $gems, abs( $gems ) != 1 ? $gem_pl : $gem );

	require_once( './modules/mysticalshop/lib.php' );
	if( mysticalshop_applydiscount( $gold, $gems, $disnum ) )
		output( '`&However, you manage to haggle the price down to `^%s gold`& and `%%s %s`&.', $gold, $gems, abs( $gems ) != 1 ? $gem_pl : $gem );

	//check to see if they can afford it first; saves from having to add extra checks. Bleh.
	$item_categories = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak', 'helm', 'glove', 'boots', 'misc' );
	if( get_module_pref( $item_categories[$cat] ) )
	{
		output("`@However, while possessing your %s`@, you're forced to merely look at %s`@ until you decide to sell your current item.", strtolower( $names[$cat] ), $what );
	}else if ($session['user']['gold']<$gold or $session['user']['gems']<$gems){
		output("`3However, checking your funds, you realize you can't afford to purchase this item at the moment.");		   
	//a quick check to make sure there are enough rare items instock for the player 
	}else if ($rare == 1 && $rarenum<1){
		output("`n`n`2%s `2suddenly realizes that the item you were about to purchase, `3%s`2, has been sold out.`n`n", $shopkeep,$what);
		output("`2\"Things go fast around here... much too fast sometimes,\" the shopkeeper notes.");
	//otherwise, display the purchase nav
	}else{
		$purchase = translate_inline( array( 'Purchase %s', $what ) );
		addnav("Sales");
		addnav( $purchase, $from."op=shop&what=purchase&id=$id&cat=$cat" );
	}
	if ($cat == 2 || $cat == 3){
		output("`n`n`6Be aware that magical blades and armor are adaptive.");
		output(" `6Precluding any extra magical properties, their attack and defensive properties are equal to your level.");
		output(" `6As you grow in strength (gain a level), they do, too.");
	}
	output_notl( '`0`n`n' );
}
else
	output( 'Nothing to preview.' );

modulehook("mysticalshop-preview", array());
addnav( 'Merchandise' );
addnav( array( 'Overview of %s', $names[$cat] ), $from."op=shop&what=viewgoods&cat=$cat" );
?>