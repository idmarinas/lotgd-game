<?php
$id = httpget('id');
if( is_numeric( $id ) )
{
	$sql = "SELECT dk,name,gold,gems,rare,rarenum FROM " . db_prefix("magicitems") . " WHERE id=$id";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$dk = $row['dk'];
	$name = $row['name'];
	$gold = $row['gold'];
	$gems = $row['gems'];
	$gem = translate_inline( 'gem' );
	$gem_pl = translate_inline( 'gems' );

	//run some checks first to see if the player can afford the item in question
	output("`2The cost of `3%s `2is `^%s gold `2and `%%s %s`2.`0", $name, $gold, $gems, abs( $gems ) != 1 ? $gem_pl : $gem );
	require_once( './modules/mysticalshop/lib.php' );
	if( mysticalshop_applydiscount( $gold, $gems ) )
		output( '`&However, after some haggling, you convince %s`& to lower the price to `^%s gold `&and `%%s %s`&!`0', $shopkeep, $gold, $gems, abs( $gems ) != 1 ? $gem_pl : $gem );

	if( $row['rare'] == 1 && $row['rarenum'] < 1 )
	  output( '`$Too bad you can\'t buy any because they are all sold out!`0' );
	elseif($session['user']['gold']<$gold && $session['user']['gems']<$gems){
		output("`n`n`2Sorry, but you have neither enough gold nor gems to purchase `6%s `2as a gift.`0", $name);
	}else if ($session['user']['gold']<$gold){
		output("`n`n`2Sorry, you don't have enough gold to purchase `^%s `2as a gift.`0", $name);
	}else if ($session['user']['gems']<$gems){
		output("`n`n`2Sorry, you don't have enough gems to purchase `^%s `2as a gift.`0", $name);
	}else{
		//if the player can afford to, let's allow them to search
		output("`n`n`i`3You have chosen to give `^%s `3as a gift to another player.`0`i",$name);
		output( '`n`n`2"Ah, how thoughtful of you, %s`2," %s `2says with a smile. "Now, who would you like to give this to?"`0`n`n', $session['user']['name'], $shopkeep );
		rawoutput( '<form action="'.htmlentities( 'runmodule.php?module=mysticalshop&op=gift&what=search-done&dk='.$dk.'&id='.$id.'&cat='.$cat ).'" method="POST">' );
		rawoutput( '<input type="text" name="whom" id="gift" size="25"><br>' );
		rawoutput( '<input type="submit" value="Search Players">' );
		rawoutput("</form>");
		addnav( '', 'runmodule.php?module=mysticalshop&op=gift&what=search-done&dk='.$dk.'&id='.$id.'&cat='.$cat );
	}
}
else
	output( 'Item does not exist.' );
?>