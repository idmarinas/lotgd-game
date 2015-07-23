<?php
if( is_numeric( $id ) )
{
	$sql = 'SELECT attack,defense,hitpoints,name,gold,gems,rare FROM '.db_prefix('magicitems').' WHERE id='.$id.' LIMIT 1';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	if( $row['attack'] >= $session['user']['attack']
			|| $row['defense'] >= $session['user']['defense']
			|| $row['hitpoints'] >= $session['user']['maxhitpoints']
		) {
		output( '`2%s`2 sizes you up, then decides that separating you from your %s`2 right now would be too painful for you.', $shopkeep, $row['name'] );
		output( 'The sale is off!' );
	} else {
		$gold = $row['gold'];
		$gems = $row['gems'];
		$gem = translate_inline( 'gem' );
		$gem_pl = translate_inline( 'gems' );

		require_once( './modules/mysticalshop/lib.php' );
		mysticalshop_applydiscount( $gold, $gems, $disnum );
		$sellgold = round(($gold*.75),0);
		$sellgems = round(($gems*.25),0);
		$rare = $row['rare'];

		$itemname = $row['name'];
		output("`2You have chosen to sell your %s`2.", $itemname);
		output( ' `2%s `2hands you your `^%s gold `2and `%%s %s `2and bids you a good day.`n`n', $shopkeep, $sellgold, $sellgems, abs( $sellgems ) != 1 ? $gem_pl : $gem );
		output("`3Any magic enchantments %s`3 provided have now been removed.", $itemname);
		output_notl( '`0`n`n' );
		debuglog( 'sold '.$itemname.' for '.$sellgold.' gold and '.$sellgems.' gems.' );
		//The hook is up here to catch the ID of the item before it's reset
		modulehook("mysticalshop-sell", array("itemid"=>$id));
		$rare = FALSE;
		if ($row['rare']) $rare = $id;
		require_once("modules/mysticalshop/lib.php");
		if ($cat == 0){
			mysticalshop_destroyitem("ring",$rare);
		}else if ($cat == 1){
			mysticalshop_destroyitem("amulet",$rare);
		}else if ($cat == 2){
			mysticalshop_destroyitem("weapon",$rare);
			//unset weapon
			$default_weapon = trim( get_module_setting( 'def_weapon' ) );
			if( $default_weapon === '' )
			{
				$default_weapon = db_fetch_assoc( db_query( 'DESC '.db_prefix( 'accounts' ).' weapon' ) );
				$default_weapon = $default_weapon['Default'];
			}
			$session['user']['weapon']= $default_weapon;
			$session['user']['attack']-=$session['user']['weapondmg'];
			$session['user']['weapondmg']=0;
			$session['user']['weaponvalue']=0;
		}else if ($cat == 3){
			mysticalshop_destroyitem("armor",$rare);
			//unset armor
			$default_armor = trim( get_module_setting( 'def_armor' ) );
			if( $default_armor === '' )
			{
				$default_armor = db_fetch_assoc( db_query( 'DESC '.db_prefix( 'accounts' ).' armor' ) );
				$default_armor = $default_armor['Default'];
			}
			$session['user']['armor']= $default_armor;
			$session['user']['defense']-=$session['user']['armordef'];
			$session['user']['armordef']=0;
			$session['user']['armorvalue']=0;
		}else if ($cat == 4){
			mysticalshop_destroyitem("cloak",$rare);
		}else if ($cat == 5){
			mysticalshop_destroyitem("helm",$rare);
		}else if ($cat == 6){
			mysticalshop_destroyitem("glove",$rare);
		}else if ($cat == 7){
			mysticalshop_destroyitem("boot",$rare);
		}else if ($cat == 8){
			mysticalshop_destroyitem("misc",$rare);
		}
		mysticalshop_resetbuffs( $id );

		//now, refund gold and gems
		$session['user']['gold']+=$sellgold;
		$session['user']['gems']+=$sellgems;
		modulehook("mysticalshop-sell-after", array());
	}
}
else
	output( 'The item can\'t be sold.' );

addnav( 'Merchandise' );
addnav( 'Overview of Goods', $from.'op=shop&what=enter' );
?>