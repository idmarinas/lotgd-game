<?php
/**
	Modified by MarcTheSlayer

	28/06/2013 - v1.0.0
	+ Rewrote and added bitwise support. Code thanks to xChrisx
	+ Creatures can now be in more than one city.
*/

function citythemedcreatures_getmoduleinfo()
{
	$bitfield = '';
	if( httpget('module') != '' )
	{
		$sql = "SELECT cityid, cityname
				FROM " . db_prefix('cityprefs');
		// May as well add suport to my 'city_creator' module. :)
		if( is_module_active('city_creator') )
		{
			$sql = "SELECT cityid, cityname, cityactive
					FROM " . db_prefix('cities');
		}
		$result = db_query($sql);
		$value = 0;
		while( $row = db_fetch_assoc($result) )
		{
			$inactive = ( isset($row['cityactive']) && $row['cityactive'] != 1 ) ? translate_inline(' (inactive)') : '';
			$val = pow(2, $row['cityid']);
			$bitfield .= ','.$val.','.$row['cityname'] . $inactive;
			$value += (int)$val;
		}
		$bitfield .= "|$value";
	}

	$info = array(
		"name"=>"City Themed Creatures",
		"description"=>"Allows creatures to only appear in certain forests",
		"version"=>"1.0.0",
		"author"=>"<a href='http://www.joshuadhall.com'>Sixf00t4</a>`2, xChrisx, rewritten by `@MarcTheSlayer",
		"category"=>"Forest",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1456",
		"settings"=>array(
			"Settings,title",
				"usestats"=>"Use replacement creature's stats?,bool",
				"`^This over rides the replacement option for each creature which you can change in the creature editor.,note",
				"`i`b`@IMPORTANT:`b`i `2If you install more cities after installing this module then you'll need to go to the creature editor and add some creatures to it.,note",
		),
		"prefs-creatures"=>array(
			"creatureloc"=>"Forests where this creature is available`n(untick all to make all locations available):,bitfield,". 0xffffffff . $bitfield,
			"usestats"=>"Use replacement creature's stats?,bool",
			"`^If set to no then only the name&#44; weapon&#44; win and lose messages and aiscript will be replaced.,note",
		),
		"prefs"=>array(
			"lastloc"=>"Where was the player last (cityid bitwise):,viewonly",
		)
	);
	return $info;
}

function citythemedcreatures_install()
{
	module_addhook('village');
	module_addhook('header-runmodule');
	module_addhook('header-creatures');
	module_addhook('buffbadguy');
	return TRUE;
}

function citythemedcreatures_uninstall()
{
	return TRUE;
}

function citythemedcreatures_dohook($hookname,$args)
{
	global $session;

	switch( $hookname )
	{
		case 'village':
			require_once('modules/cityprefs/lib.php');
			$cityid = get_cityprefs_cityid('cityname',$session['user']['location']);
			set_module_pref('lastloc', pow(2, $cityid));
		break;

		case 'header-runmodule':
			// LotGD is written to *only* handle form checkboxes for superuser flags.
			// I need to grab my data from POST to turn it into something that wont break set_module_objpref().
			// This first part is for my 'alternative_creatures_editor' module. The hacks we have to do. :D
			if( httpget('module') != 'alternative_creatures_editor' ) break;
			if( httpget('op') != 'save' ) break;
			$name = 'citythemedcreatures-creatureloc';
			$cloc = httppost($name);
			// Fall through.
		case 'header-creatures':
			if( httpget('op') != 'save' ) break; // Need to do it again for creatures.php
			if( !isset($cloc) )
			{
				$name = 'creatureloc';
				$cloc = httppost($name);
			}
			if( $cloc != '' )
			{
				$value = 0;
				while( list($k,$v) = each($cloc) )
				{
					if( $v ) $value += (int)$k;
				}
				httppostset($name, $value);
			}
		break;

		case 'buffbadguy':
			if( !isset($args['creatureid']) ) break; // Badguys created by the AI script don't have an ID.
			$cloc = get_module_objpref('creatures',$args['creatureid'],'creatureloc');
			if( $cloc & get_module_pref('lastloc') || !$cloc ) break;

			$creatures = db_prefix('creatures');
			$objprefs = db_prefix('module_objprefs');
			$sql = "SELECT $creatures.creatureid as id
					FROM $creatures
					INNER JOIN $objprefs
						ON $creatures.creatureid = $objprefs.objid
					WHERE $objprefs.setting = 'creatureloc'
						AND $objprefs.value & '" . get_module_pref('lastloc') . "'
						AND $creatures.creaturelevel = " . $args['creaturelevel'] . "
						AND $creatures.forest = 1"; // ORDER BY RAND() is bad apparently so dump to array and shuffle. :)
			$result = db_query($sql);
			$id_array = array();
			while( $row = db_fetch_assoc($result) ) $id_array[] = $row['id'];
			shuffle($id_array); // Randomise the order.
			foreach( $id_array as $id )
			{
				$sql = "SELECT *
						FROM " . db_prefix('creatures') . "
						WHERE creatureid = " . $id;
				$result = db_query($sql);
				$badguy = db_fetch_assoc($result);
				if( get_module_setting('usestats') || get_module_objpref('creatures',$badguy['creatureid'],'usestats') )
				{
					debug("Replacing fully: {$args['creaturename']} with {$badguy['creaturename']}. Module: citythemedcreatures.php");
					$args = $badguy;
				}
				else
				{
					debug("Replacing partial: {$args['creaturename']} with {$badguy['creaturename']}. Module: citythemedcreatures.php");
					$args['creaturename'] = $badguy['creaturename'];
					$args['creatureweapon'] = $badguy['creatureweapon'];
					$args['creaturewin'] = $badguy['creaturewin'];
					$args['creaturelose'] = $badguy['creaturelose'];
					$args['creatureaiscript'] = $badguy['creatureaiscript']; // Do AI script code as well.
				}
				return $args;
			}
			debug('No level '.$args['creaturelevel'].' forest creatures have been assigned this location. Module: citythemedcreatures.php');
		break;
	}
	return $args;
}

function citythemedcreatures_run()
{
}
?>