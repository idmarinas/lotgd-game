<?php
$giftid = get_module_pref("giftid");
if( is_numeric( $giftid ) )
{
	$sql = 'SELECT * FROM '.db_prefix('magicitems').' WHERE id='.$giftid.' LIMIT 1';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$gift = $row['name'];
	$verbose = $row['bigdesc'];
	output("`2%s `2 sets out `^%s `2for you to see.`n`n", $shopkeep, $gift);
	output("`2Viewing %s.`n`n", $row['name']);
	//display that nice big description you typed out here
	if ($verbose>""){
		output("`3%s`n`n",$row['bigdesc']);
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
	output_notl( '`0' );
	addnav("Keep It", $from."op=gift&what=pickup");
	addnav("Decline", $from."op=gift&what=decline");
}
else
	output( 'Sorry, the gift is not available.' );
?>