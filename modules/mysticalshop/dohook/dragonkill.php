<?php
$chance = get_module_setting("losechance");
//say bye bye to all your junk. Gah, I really kicked myself in the ass with this one...

$item_cats = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak', 'helm', 'glove', 'boot', 'misc' );

//you automatically lose your weapon/armor after a dragonkill anyways.
//so unset any stat-altering modifications here. No, I won't change this

$always_lose = array( 'weapon', 'armor' );
$lose_message = translate_inline( array(
		'ring'=>"You don't seem to be wearing a ring anymore",
		'amulet'=>"You notice you've lost your amulet",
		'cloak'=>'Your cloak has been shredded beyond repair',
		'helm'=>'You seem to have misplaced your helmet',
		'glove'=>'Your gloves have gone missing',
		'boot'=>'You are sure you were wearing boots once',
		'misc'=>'You seem to have lost the extra item you were carrying'
	) );

foreach( $item_cats as $item ){
	$autolose = false;
	if( in_array( $item, $always_lose, true ) )
		$autolose = true;

	if( get_module_pref( $item.( $item == 'boot' ? 's' : '' ) )
			&& ( $autolose || get_module_setting( 'lose'.$item ) ) )
	{
		if( !$autolose )
			$roll = e_rand(1,100);
		if( $autolose || $roll<$chance ) {
			$id = (int)get_module_pref( $item.'id' );
			$sql = 'SELECT charm,hitpoints,turns,rare FROM '.db_prefix('magicitems').' WHERE id='.$id.' LIMIT 1';
			$result = db_query($sql);
			$row = db_fetch_assoc($result);

			if ($row['charm']<>0) $session['user']['charm']-=$row['charm'];
			if( is_module_active( 'globalhp' ) && get_module_setting( 'carrydk', 'globalhp' )
					&& $row['hitpoints'] < 0 )
				$session['user']['maxhitpoints']-=$row['hitpoints'];
			if ($row['turns']<>0) {
				$session['user']['turns']-=$row['turns'];
				if (get_module_pref("turnadd")>0) {
					set_module_pref("turnadd",get_module_pref("turnadd")-$row['turns']);
				}
			}
			if (get_module_pref("favor")<>0){
				set_module_pref("res",0);
				set_module_pref("favor",0);
				set_module_pref("favoradd",0);
			}
			$rare = FALSE;
			if( get_module_setting( 'dkreaddrare' ) && $row['rare'] )
				$rare = $id;
			require_once("modules/mysticalshop/lib.php");
			mysticalshop_destroyitem($item, $rare);
			if( !$autolose )
				output_notl( '`3%s`0`n', $lose_message[ $item ] );
		}
	}
}

// --------------------------------Restore Stats--------------------------------
if( ( get_module_setting( 'restoreAll' ) && get_module_pref( 'restoreIndiv' ) == 0 )
	|| get_module_pref( 'restoreIndiv' ) == 1 ) // User setting overrides global
{
	$buffs = array( 'attack'=>'attack', 'defense'=>'defense', 'turns'=>'turns', 'favor'=>'deathpower' );
	if( !is_module_active( 'globalhp' ) || !get_module_setting( 'carrydk', 'globalhp') )
		$buffs['hitpoints'] = 'maxhitpoints'; // don't re-add hitpoints if they are not reset at DK
	// Charm doesn't get reset at DK, no need to restore it.
	require_once("modules/mysticalshop/lib.php");
	mysticalshop_applyenh( $buffs, false );
}
//------------------------------------------------------------------------------
	
if (get_module_pref("turnadd")<0) set_module_pref("turnadd",0);
if ($session['user']['hitpoints'] < 1) $session['user']['hitpoints']=1;
?>