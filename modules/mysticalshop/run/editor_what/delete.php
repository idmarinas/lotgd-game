<?php
function mysticalshop_delete_item( $id )
{
	$magic_table = db_prefix( 'magicitems' );

	$item_stats_sql =
		'SELECT name,category,gold,gems,attack,defense,charm,hitpoints,turns,favor
			FROM '.$magic_table
			.' WHERE id='.$id
			.' LIMIT 1';
	$del_sql = 'DELETE FROM '.$magic_table.' WHERE id='.$id;

	$item_categories = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak',
			'helm', 'glove', 'boot', 'misc' );

	$item_stats_array = db_fetch_assoc( db_query( $item_stats_sql ) );
	$item_name = $item_stats_array['name'];
	$item_catint = $item_stats_array['category'];
	$item_cat = strtolower( $item_categories[$item_catint] );

	db_query( $del_sql );
	output( '`^Item %s`^ deleted.`n', $item_name );

	$prefs_table = db_prefix( 'module_userprefs' );
	$accts_sql = 'SELECT userid FROM '.$prefs_table
		.' WHERE modulename=\'mysticalshop\' AND setting=\''.$item_cat
			.'id\' AND `value`=\''.$id
			.'\' AND userid IN(SELECT userid FROM '.$prefs_table
				.' WHERE modulename=\'mysticalshop\' AND setting=\''.$item_cat
				.( $item_cat == 'boot' ? 's' : '' )
				.'\' AND value=\'1\')';
	$accts_result = db_query( $accts_sql );
	$accts = '';
	while( $acct_array = db_fetch_assoc( $accts_result ) )
		$accts.= $acct_array['userid'].',';
	$accts = rtrim( $accts, ',' );
	if( $accts != '' )
	{
		if( $item_cat == 'weapon' )
		{
			$default_weapon = trim( get_module_setting( 'def_weapon' ) );
			if( $default_weapon === '' )
			{
				$default_weapon = db_fetch_assoc( db_query( 'DESC '.db_prefix( 'accounts' ).' weapon' ) );
				$default_weapon = $default_weapon['Default'];
			}
			$extra_sql = ',armorvalue=0,armordef=0,weapon=\''.$default_weapon.'\'';
		}
		elseif( $item_cat == 'armor' )
		{
			$default_armor = trim( get_module_setting( 'def_armor' ) );
			if( $default_armor === '' )
			{
				$default_armor = db_fetch_assoc( db_query( 'DESC '.db_prefix( 'accounts' ).' armor' ) );
				$default_armor = $default_armor['Default'];
			}
			$extra_sql = ',weaponvalue=0,weapondmg=0,armor=\''.$default_armor.'\'';
		}
		else
			$extra_sql = '';

		$cleanup_sql = 'UPDATE '.db_prefix( 'accounts' )
			.' SET gold=gold+('.(int)$item_stats_array['gold']
				.'),gems=gems+('.(int)$item_stats_array['gems']
				.'),attack=attack-('.(int)$item_stats_array['attack']
				.'),defense=defense-('.(int)$item_stats_array['defense']
				.'),charm=charm-('.(int)$item_stats_array['charm']
				.'),maxhitpoints=maxhitpoints-('.(int)$item_stats_array['hitpoints']
				.'),turns=turns-('.(int)$item_stats_array['turns']
				.'),deathpower=deathpower-('.(int)$item_stats_array['favor'].')'
				.$extra_sql
			.' WHERE acctid IN('.$accts.')';
		db_query( $cleanup_sql );
		$affected = db_affected_rows();
		if( $affected > 0 )
			output( 'Player accounts refunded: %s. ', $affected );

		if( $item_cat )
		{
			$cleanuser_sql = 'UPDATE '.$prefs_table
				.' SET `value`=\'0\''
				.' WHERE userid IN ('.$accts
				.') AND modulename=\'mysticalshop\'
					AND (setting=\''.$item_cat.( $item_cat == 'boot' ? 's' : '' ).'\'
						OR setting=\''.$item_cat.'id\')';
			$clearname_sql = 'UPDATE '.$prefs_table
				.' SET `value`=\'None\''
				.' WHERE userid IN ('.$accts
				.') AND modulename=\'mysticalshop\'
					AND setting=\''.$item_cat.'name\'';
			$clearturns_sql = 'UPDATE '.$prefs_table
				.' SET `value`=`value`-('.(int)$item_stats_array['turns']
				.') WHERE userid IN ('.$accts
				.') AND modulename=\'mysticalshop\'
					AND setting=\'turnadd\'';
			$clearfavor_sql = 'UPDATE '.$prefs_table
				.' SET `value`=`value`-('.(int)$item_stats_array['favor']
				.') WHERE userid IN ('.$accts
				.') AND modulename=\'mysticalshop\'
					AND setting=\'favoradd\'';
			db_query( $cleanuser_sql );
			$affected_prefs = db_affected_rows();
			db_query( $clearname_sql );
			$affected_prefs += db_affected_rows();
			db_query( $clearturns_sql );
			$affected_prefs += db_affected_rows();
			db_query( $clearfavor_sql );
			$affected_prefs += db_affected_rows();
			if( $affected_prefs > 0 )
				output( 'Player preferences cleared: %s.', $affected_prefs );
		}
	}
	output_notl( '`0`n`n' );

	if( getsetting( 'usedatacache', false ) )
	{
		invalidatedatacache( 'modules-mysticalshop-editorcats' );
		invalidatedatacache( 'modules-mysticalshop-view-'.$item_catint );
		invalidatedatacache( 'modules-mysticalshop-enter' );
		require_once( './modules/mysticalshop/libcoredup.php' );
		mysticalshop_massinvalidate( 'modules-mysticalshop-viewgoods-'.$item_catint );
	}
}
?>