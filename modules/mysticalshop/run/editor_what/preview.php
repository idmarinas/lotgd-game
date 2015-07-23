<?php
if( is_numeric( $id ) )
{
	$sql = 'SELECT * FROM '.db_prefix('magicitems').' WHERE id='.$id.' AND category='.$cat.' LIMIT 1';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	output("`c`2`b<u>Preview of %s</u>`b`0`c`n`n",$row['name'],true);
	$gold = $row['gold'];
	$gems = $row['gems'];
	output("`2Cost Gold: `^%s`n",$gold);
	output("`2Cost Gems: `%%s`n",$gems);
	output("`2Dragon Kill Requirement: `^%s`n`n",$row['dk']);
	output("`2Short Description:`0`n");
	if ($row['description']>""){
		output("`i`3%s`0`i`n`n",$row['description']);
	}else{
		output("`i`3This item has no short description.`0`i`n`n");
	}
	output("`2Verbose Description:`n");
	if ($row['bigdesc']>""){
		output("`3%s`n`n",$row['bigdesc']);
	}else{
		output("`2This item has no Verbose Description.`n`n");
	}
	output("`2Stats:`n");
	$point = translate_inline( 'point' );
	$points = translate_inline( 'points' );
	$attack = $row['attack'];
	$defense = $row['defense'];
	$charm = $row['charm'];
	$favor = $row['favor'];
	if ($row['rare']>0)
		output("`&This item is rare and only a limited amount exists.`n");
	if ($attack<>0)
		output("`&This item's enchantments will alter attack by `^%s `&%s.`n", $attack, abs( $attack ) != 1 ? $points : $point );
	if ($defense<>0)
		output("`&This item's enchantments will alter defense by `^%s `&%s.`n", $defense, abs( $defense ) != 1 ? $points : $point );
	if ($charm<>0)
		output("`&This item's enchantments will alter charm by `^%s `&%s.`n", $charm, abs( $charm ) != 1 ? $points : $point );
	if ($row['hitpoints']<>0)
		output("`&This item's enchantments will alter maximum hit points by `^%s`&.`n",$row['hitpoints']);
	if ($row['turns']<>0)
		output("`&This item's enchantments will alter turns by `^%s`&.`n",$row['turns']);
	if ($favor<>0)
		output( '`&This item\'s enchantments will alter favor with %s`& by `^%s `&%s.`n', getsetting( 'deathoverlord', '`$Ramius' ), $favor, abs( $favor ) != 1 ? $points : $point );
	output_notl( '`0' );
	require_once( './modules/mysticalshop/lib.php' );
	mysticalshop_applydiscount( $gold, $gems, NULL, 'show_table' );

	output( '`nWould you like to <a href="'.htmlentities( $fromeditor.'edit&id='.$id.'&cat=' ).$cat.'">[ Edit ]</a> this item?', true );
	addnav( '', $fromeditor.'edit&id='.$id.'&cat='.$cat );
}
else
	output( 'Nothing to preview.`n' );

output("`n`3Click `iRefresh List`i to return to the category you were just viewing.`0`n`n");
?>