<?php
function mysticalshop_applyenh( $buffs, $args = false )
{
	global $session;
	$userid = (int)httpget( 'userid' );

	$favor = 0;
	$turns = 0;

	$sql = 'SELECT * FROM '.db_prefix( 'magicitems' );
	if( !$args )
		$sql.= ( $session['user']['specialty'] == 'MN' ? ' WHERE category<>2 AND category<>3' : '' );
 	else
		output( '`n`&Applying enhancements for all items named below (if any):`0`n' );
	$items = db_query( $sql );

	$itemcats = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak', 'helm', 'glove', 'boot', 'misc' );

	while( $values = db_fetch_assoc( $items ) )
	{
		$cat = $itemcats[ $values['category'] ];
		$plural = ($cat == 'boot') ? 's' : ''; // inconsistent naming... sigh

		$isOwned = $args ? $args[ $cat.$plural ] && ( $args[$cat.'id'] == $values['id'] )
				: get_module_pref( $cat.$plural )	&& ( get_module_pref( $cat.'id' ) == $values['id'] );
		if( $isOwned )
		{
			output( '`n`!The magic in `^%s`! begins to affect some of your abilities...`0`n', $values['name'] );

			if( !$args )
				set_module_pref( $cat.'name', $values['name'] );
			else
				$args[$cat.'name'] = $values['name'];

			foreach( $buffs as $buffInPrefs=>$buff )
			{
				if( $values[$buffInPrefs] != 0 )
				{
					if( !$args )
						$session['user'][$buff] += $values[$buffInPrefs];
					else
					{
						output( '...%s change: %s`n', $buffInPrefs, $values[$buffInPrefs] );
						$sql = 'UPDATE '.db_prefix( 'accounts' )." SET $buff=$buff+{$values[$buffInPrefs]} WHERE acctid=$userid";
						db_query($sql);
					}

					if( $buffInPrefs == 'favor' )
						$favor += $values[$buffInPrefs];
					else if( $buffInPrefs == 'turns' )
						$turns += $values[$buffInPrefs];
				}
			}

			if( !$args )
			{
				if( $favor != 0 )
				{
					set_module_pref( 'favor', true );
					set_module_pref( 'res', 0 );
				}
				else
					set_module_pref( 'favor', false );

				set_module_pref( 'turnadd', $turns );
				set_module_pref( 'favoradd', $favor );
			}
			else
			{
				if( $favor != 0 )
				{
					$args['favor'] = true;
					$args['res'] = 0;
				}
				else
					$args['favor'] = false;

				$args['turnadd'] = $turns;
				$args['favoradd'] = $favor;
			}
		}
	}
	return $args;
}

function mysticalshop_destroyitem($item_type,$rare_id = FALSE, $userid = FALSE){
	global $session;
	$id = "";
	if ($userid === FALSE){
		$userid = $session['user']['acctid'];
		$id = get_module_pref($item_type."id","mysticalshop",$userid);
	}
	modulehook("mysticalshop-destroyitem",array("itemid"=>$id));
	if ($rare_id){
		$sql = "UPDATE ".db_prefix("magicitems")." SET rarenum=rarenum+1 WHERE id='$rare_id'";
		db_query($sql);
		if( getsetting( 'usedatacache', false ) )
		{
			invalidatedatacache( 'modules-mysticalshop-enter' );
			require_once( './modules/mysticalshop/libcoredup.php' );
			mysticalshop_massinvalidate( 'modules-mysticalshop-viewgoods-' );
		}
	}

	$sing_item_type = $item_type;
	if( $item_type == 'boot' ) $item_type = 'boots';

	// $id = FALSE; <-- What was that here for? --WK
	set_module_pref($item_type,0,"mysticalshop",$userid);
	set_module_pref($sing_item_type."id",0,"mysticalshop",$userid);
	set_module_pref($sing_item_type."name","None","mysticalshop",$userid);
}

/* Buff reset function attempts to subtract the "magical" effects of an item.
 * An item id for the current user can be obtained via
 * 	$itemid = get_module_pref( $itemcat.'id' );
 * where $itemcat is one of the item categories. */
function mysticalshop_resetbuffs( $itemid )
{
	if( is_numeric( $itemid ) )
	{
		global $session;

		$sql = 'SELECT * FROM '.db_prefix( 'magicitems' ).' WHERE id='.$itemid.' LIMIT 1';
		$result = db_query( $sql );
		$row = db_fetch_assoc( $result );

		//Undo any altered stats
		if ($row['attack']<>0) {
			$session['user']['attack']-=$row['attack'];
		}
		if ($row['defense']<>0) {
			$session['user']['defense']-=$row['defense'];
		}
		if ($row['charm']<>0) {
			$session['user']['charm']-=$row['charm'];
		}
		if ($row['hitpoints']<>0) {
			//note, add a check in to prevent any possible permadead situation after dk
			$session['user']['maxhitpoints']-=$row['hitpoints'];
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		}
		if ($row['turns']<>0) {
			if( $session['user']['turns'] > $row['turns'] )
				$session['user']['turns']-=$row['turns'];
			else
				$session['user']['turns'] = 0;
		/*	if (get_module_pref("turnadd")<$diff){ // per-item stats shouldn't affect other items
				set_module_pref("turnadd",0); // assuming negatives are allowed (as turn penalty); $diff is undefined
			}else{
		*/
		set_module_pref("turnadd",get_module_pref("turnadd")-$row['turns']);
		//	} // end if
		}
		//Undo favor
		if ($row['favor']<>0) {
			if ($session['user']['deathpower']<=$row['favor']){
				$session['user']['deathpower']=0;
			}else{
				$session['user']['deathpower']-=$row['favor'];
			}
			set_module_pref("res",0);
			$favoradd = get_module_pref("favoradd")-$row['favor'];
			set_module_pref("favoradd", $favoradd);
			if( $favoradd == 0 ) // if it's not 0, assuming that other items are still affecting it
				set_module_pref("favor",0);
		}
	}
	else
		debug( 'mysticalshop_resetbuffs: The item id must be a number.' );
}

function mysticalshop_additem( $id, $cat, $seller_present=true )
{
	if( $seller_present )
		$shopkeep = get_module_setting( 'shopkeepname' );
	global $session;

	$sql = 'SELECT * FROM '.db_prefix('magicitems').' WHERE id='.$id.' LIMIT 1';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$attack = $row['attack'];
	$defense = $row['defense'];
	$charm = $row['charm'];
	$health = $row['hitpoints'];
	$turns = $row['turns'];
	$name = $row['name'];
	$rare = $row['rare'];
	$subtract = ($row['rarenum']-1);
	//if this is a limited item, let's subtract from the total available
	if ($rare == 1){
		$sql = "UPDATE ".db_prefix("magicitems")." SET rarenum=$subtract WHERE id=$id";
		db_query($sql);
		if( getsetting( 'usedatacache', false ) )
		{
			invalidatedatacache( 'modules-mysticalshop-enter' );
			require_once( './modules/mysticalshop/libcoredup.php' );
			mysticalshop_massinvalidate( 'modules-mysticalshop-viewgoods-'.$cat );
		}
	}

	if ($cat==0){
		set_module_pref("ringid",$id);
		set_module_pref("ring",1);
		set_module_pref("ringname",$name);
	}else if ($cat == 1){
		set_module_pref("amuletid",$id);
		set_module_pref("amulet",1);
		set_module_pref("amuletname",$name);
	}else if ($cat==2){
		$value = $row['gold'];
		//alright, let's first subtract the previous weapon damage
		//credit to seretogis for catching this bug
		if( $session['user']['weapondmg'] != 0 )
			output( '`2Your `^%s`2 disintegrates as soon as you take hold of `^%s`2.`n`n', $session['user']['weapon'], $name );
		$session['user']['attack']-=$session['user']['weapondmg'];
		//these are magical blades, they adjust as you level
		//i.e. each level, their attack goes up by one
		$session['user']['weapon'] = $name;
		$session['user']['weaponvalue'] = $value;
		$session['user']['weapondmg']=$session['user']['level'];
		$session['user']['attack']+=$session['user']['weapondmg'];
		//
		set_module_pref("weaponid",$id);
		set_module_pref("weapon",1);
		set_module_pref("weaponname",$name);
		if( $seller_present )
			output("`^\"I'm also sorry to say,\" %s`^ notes, \"that MightyE's Weapon Shop is closed to you until you sell your weapon back. Business rivarly, you know.\"`n`n", $shopkeep);
	}else if ($cat==3){
		$value = $row['gold'];
		set_module_pref("armorid",$id);
		set_module_pref("armor",1);
		set_module_pref("armorname",$name);
		//take away original armor value, the sister of the bug stated above
		if( $session['user']['armordef'] != 0 )
			output( "`2You touch `^%s`2 and watch your `^%s`2 fall apart.`n`n", $name, $session['user']['armor'] );
		$session['user']['defense']-=$session['user']['armordef'];
		//magical armor, adjusts as you level
		$session['user']['armor'] = $name;
		$session['user']['armorvalue'] = $value;
		$session['user']['armordef'] = $session['user']['level'];
		$session['user']['defense']+=$session['user']['level'];
		//To defeat the double armor bug once and for all, I've blocked the armor shop from showing up. Let's tell the players this.
		if( $seller_present )
			output("`^\"I'm afraid to say Pegasus doesn't care too much for the competition,\" %s`^ notes. \"Her doors are closed to you until you sell your armor back to the shop.\"`n`n", $shopkeep);
	}else if ($cat==4){
		set_module_pref("cloakid",$id);
		set_module_pref("cloak",1);
		set_module_pref("cloakname",$name);
	}else if ($cat==5){
		set_module_pref("helmid",$id);
		set_module_pref("helm",1);
		set_module_pref("helmname",$name);
	}else if ($cat==6){
		set_module_pref("gloveid",$id);
		set_module_pref("glove",1);
		set_module_pref("glovename",$name);
	}else if ($cat==7){
		set_module_pref("bootid",$id);
		set_module_pref("boots",1);
		set_module_pref("bootname",$name);
	}else if ($cat==8){
		set_module_pref("miscid",$id);
		set_module_pref("misc",1);
		set_module_pref("miscname",$name);
	}
	//end
	$point = translate_inline( 'point' );
	$points = translate_inline( 'points' );
	//alter stats if needed
	if ($attack<>0) {
		$session['user']['attack']+=$attack;
		output("`&This item's enchantments have altered your attack by `^%s `&%s.`n", $attack, abs( $attack ) != 1 ? $points : $point );
	}
	if ($defense<>0) {
		$session['user']['defense']+=$defense;
		output("`&This item's enchantments have altered your defense by `^%s `&%s.`n", $defense, abs( $defense ) != 1 ? $points : $point );
	}
	if ($charm<>0) {
		$session['user']['charm']=$session['user']['charm']+$charm;
		output("`&This item's enchantments have altered your charm by `^%s `&%s.`n", $charm, abs( $charm ) != 1 ? $points : $point );
	}
	if ($health<>0) {
		$session['user']['maxhitpoints']+=$health;
		//adjust to fit
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		output("`&This item's enchantments have altered your maximum hit points by `^%s`&.`n", $health );
	}
	//this needs to be adjusted at newday as well.
	if ($turns<>0) {
		$session['user']['turns']+=$turns;
		output("`&This item's enchantments have altered your turns by `^%s`&.`n", $turns );
		set_module_pref("turnadd",get_module_pref("turnadd")+$turns);
	}
	//items that grant favor are a little trickier, since they have to be restored upon each resurrection
	//So, extra additions are needed. See newday above to see how this is done.
	if ($row['favor']<>0) {
	  $favor = $row['favor'];
		//grant one-time automatic favor
		$session['user']['deathpower']+=$favor;
		//store favor granted to be restored upon resurrection
		set_module_pref("res",$session['user']['resurrections']);
		set_module_pref("favor",1);
		set_module_pref("favoradd",get_module_pref("favoradd")+$favor);
		output( '`&This item\'s enchantments have altered your favor with %s`& by `^%s `&%s.`n', getsetting( 'deathoverlord', '`$Ramius' ), $favor, abs( $favor ) != 1 ? $points : $point );
	}
	return array( 'name'=>$name, 'gold'=>$row['gold'], 'gems'=>$row['gems'] );
}

function mysticalshop_discount( &$gold, &$gems, $disnum, $charm )
{
	if( $charm > 1 )
	{
		$discperc = ( $charm - 1 ) / ( $charm + $disnum / 1.34 ) - .001;
		if( $discperc < 0 )
			$discperc = 0;
		$discmod = 1 - $discperc; // was 1 - ( $session['user']['charm'] / $disnum );

		if( $gold > 0 )
			$gold = ceil( $gold * $discmod );
		else
			$gold = 0;

		if( $gems > 0 )
			$gems = ceil( $gems * $discmod );
		else
			$gems = 0;
	}
	else
		$discperc = 0;

	return $discperc;
}

function mysticalshop_applydiscount( &$gold, &$gems, $disnum = NULL, $charm = NULL )
{
	$discount = (bool)get_module_setting( 'discount' );
	if ( $discount )
	{
		$discgold = $gold;
		$discgems = $gems;

		if( is_null( $disnum ) )
			$disnum = get_module_setting( 'discountnum' );
		if( $disnum < 0 )
			$disnum = 0;

		if( $charm == 'show_table' )
		{
			output( '`n`c`7Charm Discounts`0' );
			rawoutput( '<table style="text-align:right" cellpadding="2" cellspacing="3"><tr class="trhead"><td>'
					.translate_inline( 'Charm' ).'</td><td>'
					.translate_inline( 'Discount, %' ).'</td><td>'
					.translate_inline( 'Gold' ).'</td><td>'
					.translate_inline( 'Gems' ).'</td></tr>' );

			$step = (int)( $disnum * .1 );
			$end = $step << 12;

			$i = true;
			for( $charm = 0; $charm < $end; $charm < $step / 2 ? $charm += 5 : $charm <<= 1 )
			{
        $i = !$i;
				$discperc = mysticalshop_discount( $gold, $gems, $disnum, $charm ) * 100;
				$disc = round( $discperc, 3 );
				rawoutput( '<tr class="'.( $i ? 'trlight' : 'trdark' ).'"><td>'.$charm.'</td><td>'.$disc.'</td><td>'.$gold.'</td><td>'.$gems.'</td></tr>' );
				$gold = $discgold;
				$gems = $discgems;
			}
			rawoutput( '</table>' );
			output( '`7Note: The discount will never reach 100%. All percentages are rounded down.`0`c' );
		}
		else
		{
			if( is_null( $charm ) )
			{
				global $session;
				$charm = $session['user']['charm'];
			}
			mysticalshop_discount( $gold, $gems, $disnum, $charm );
			if( $discgold == $gold && $discgems == $gems )
				$discount = false;
		}
	}
	return $discount;
}
?>