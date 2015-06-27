<?php
/**
	23/08/09 - v0.0.2
	+ Fixed a bug where if the setting didn't have a default value you couldn't save data to it. Thanks bodinxx.

	29/08/09 - v0.0.3
	+ Fixed a bug where addslashes messed up the creature's AI php code. Removed addslashes. Thanks bodinxx.

	30/06/2013 - v0.0.4
	+ Changed the level dropdown menu to check boxes on the "add creature" form.
	  This means you can add the same creature to different levels at the same time instead of just one.
	+ Added delete object prefs line so the prefs get deleted when the creature does. This is missing from creatures.php
	+ Minor tweaks.

	A lot of this code was taken from the files creatures.php and modules.php
	and then modified to suit.
*/
function alternative_creatures_editor_getmoduleinfo()
{
	$info = array(
		"name"=>"Alternative Creatures Editor",
		"description"=>"An alternative creatures editor.",
		"version"=>"0.0.4",
		"author"=>"`@MarcTheSlayer `2and Dragonprime Development Team",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1455",
		"settings"=>array(
			"Module Settings,title",
				"block"=>"Block creature editor grotto link?,bool"
		)
	);
	return $info;
}

function alternative_creatures_editor_install()
{
	output("`c`b`Q%s 'alternative_creatures_editor' Module.`b`n`c", translate_inline(is_module_active('alternative_creatures_editor')?'Updating':'Installing'));
	module_addhook('superuser');
	return TRUE;
}

function alternative_creatures_editor_uninstall()
{
	output("`n`c`b`Q'alternative_creatures_editor' Module Uninstalled`0`b`c");
	return TRUE;
}

function alternative_creatures_editor_dohook($hookname,$args)
{
	global $session;

	if( $session['user']['superuser'] & SU_EDIT_CREATURES )
	{
		addnav('Editors');
		addnav('Alt Creature Editor','runmodule.php?module=alternative_creatures_editor');
		if( get_module_setting('block') == 1 ) blocknav('creatures.php');
	}

	return $args;
}

function alternative_creatures_editor_run()
{
	global $session;

	page_header('Creature Editor');

	$op = httpget('op');
	$id = httpget('creatureid');
	$level = httpget('level');
	$level = ( !empty($level) ) ? $level : 1;
	$loc = httpget('loc');

	$from = 'runmodule.php?module=alternative_creatures_editor';

	if( $op == 'save' )
	{
		// We want to check for these table fields only. Anything else belongs to a module
		$fields_array = array('creaturename','creatureweapon','creaturewin','creaturelose','creaturelevel','forest','graveyard','creatureaiscript');
		$fields_array2 = array('creaturehealth','creatureattack','creaturedefense','creatureexp','creaturegold');

		if( $_POST['creaturename'] == '' ) $_POST['creaturename'] = '`%Your Mum';
		if( $_POST['creatureweapon'] == '' ) $_POST['creatureweapon'] = '`%House Chores';

		$post = httpallpost();
		$id = httppost('creatureid');

		// Grab list of modules to have 'creatures' object prefs.
		$sql = "SELECT modulename
				FROM " . db_prefix('modules') . "
				WHERE infokeys
				LIKE '%|prefs-creatures|%'
				ORDER BY formalname";
		$result = db_query($sql);
		$module_array = array();
		while( $row = db_fetch_assoc($result) )
		{
			$module_array[] = $row['modulename'];
		}

		// Fill out an array of creature stats.
		require_once('lib/creatures.php');
		$creaturestats = array();
		for( $i=1; $i<=18; $i++ )
		{
			$creaturestats[$i] = creature_stats($i);
		}

		// Edit creature.
		if( $id > 0 )
		{
			$lev = (int)httppost('creaturelevel');
			reset($creaturestats[$lev]);

			$oldvalues = stripslashes(httppost('oldvalues'));
			$oldvalues = unserialize($oldvalues);
			unset($post['oldvalues'], $post['creatureid']);
			$sql = '';
			reset($post);
			while( list($key,$val) = each($post) )
			{
				if( in_array($key, $fields_array) )
				{
					if( isset($oldvalues[$key]) && $oldvalues[$key] != $val )
					{
						//	$sql .= "$key = '".addslashes($val)."', "; // Screws up the AI code as stripslashes isn't used in the code file.
						if( $key == 'creatureaiscript' ) $sql .= "$key = '$val', ";
						else $sql .= "$key = '".mysql_real_escape_string($val)."', ";
						unset($post[$key], $oldvalues[$key]);
					}
				}
			}
			while( list($key,$val) = each($creaturestats[$lev]) )
			{
				if( in_array($key, $fields_array2) )
				{
					if( isset($oldvalues[$key]) && $oldvalues[$key] != $val )
					{
						$sql .= "$key = '$val', ";
					}
				}
			}
			$sql = rtrim($sql, ', ');
			$sql = "UPDATE " . db_prefix('creatures') . " SET " . $sql . " WHERE creatureid = '$id'";
			db_query($sql);
			if( db_affected_rows() > 0 )
			{
				output('`@Creature\'s main details have been successfully updated!`n');
			}
			else
			{
				output('`$Creature\'s main details have not changed!`n');
			}

			foreach( $module_array as $mkey => $modulename )
			{
				$len = strlen($modulename);
				foreach( $post as $key => $val )
				{
					if( substr($key,0,$len) == $modulename )
					{
						if( isset($oldvalues[$key]) && $oldvalues[$key] != $val )
						{
							$len2 = strlen($key);
							$keyname = substr($key,$len+1,$len2);
							set_module_objpref('creatures', $id, $keyname, $val, $modulename);
							output('`7Module: `&%s `7Setting: `&%s `7ObjectID: `&%s `7Value changed from `&%s `7to `&%s`7.`n', $modulename, $keyname, $id, $oldvalues[$key], $val);
							unset($post[$key], $oldvalues[$key]);
						}
					}
				}
			}
			addnav('Options');
			addnav('Re-Edit Creature',$from.'&op=edit&creatureid='.$id.'&level='.$level);
		}
		else
		{
			// New Creature.
			unset($post['oldvalues'], $post['creatureid'], $fields_array['creaturelevel']);

			// Sort out bitwise level data.
			$levels = 0;
			while( list($k,$v) = each($post['creaturelevel']) )
			{
				if( $v ) $levels += (int)$k;
			}
			if( $levels == 0 ) $levels = 1;
			unset($post['creaturelevel']);

			$cols = array();
			$vals = array();

			reset($post);
			while( list($key,$val) = each($post) )
			{
				if( in_array($key, $fields_array) )
				{
					array_push($cols,$key);
					if( $key == 'creatureaiscript' ) array_push($vals,$val);
					else array_push($vals,mysql_real_escape_string($val));
					unset($post[$key]);
				}
			}

			$id_array = array();
			for( $i=1; $i<=18; $i++ )
			{
				// Go through each of the levels selected, generate the stats for that level and then INSERT.
				if( pow(2, $i) &~ $levels ) continue;

				$cols2 = $cols;
				$vals2 = $vals;
				reset($creaturestats[$i]);
				while( list($key,$val) = each($creaturestats[$i]) )
				{
					if( in_array($key, $fields_array2) )
					{
						array_push($cols2,$key);
						array_push($vals2,$val);
					}
				}
			
				$sql = "INSERT INTO " . db_prefix('creatures') . " (" . join(",",$cols2) . ",creaturelevel,createdby) VALUES (\"".join("\",\"",$vals2)."\",".$i.",'" . mysql_real_escape_string($session['user']['login']) . "')";
				db_query($sql);
				$id = db_insert_id();
				$id_array[] = $id; // Put IDs in array for the object prefs.
				if( db_affected_rows() > 0 )
				{
					output('`@Creature `q%s `@was successfully saved!`n', $id);
				}
				else
				{
					output('`$Creature was NOT saved!`n');
				}
			}

			foreach( $module_array as $mkey => $modulename )
			{
				$len = strlen($modulename);
				foreach( $post as $key => $val )
				{
					if( $val != '' )
					{
						if( substr($key,0,$len) == $modulename )
						{
							$len2 = strlen($key);
							$keyname = substr($key,$len+1,$len2);
							foreach( $id_array as $id )
							{
								set_module_objpref('creatures', $id, $keyname, $val, $modulename);
								output('`7Module: `&%s `7Setting: `&%s `7ObjectID: `&%s `7Value: `&%s`7.`n', $modulename, $keyname, $id, $val);
							}
							unset($post[$key]);
						}
					}
				}
			}
			addnav('Options');
		}

		addnav('Previous Page',$from.'&level='.$level);
		addnav('Main Page',$from);
		addnav('Add a Creature',$from.'&op=add&level='.$lev);
	}
	elseif( $op == 'del' )
	{
		db_query("DELETE FROM " . db_prefix('creatures') . " WHERE creatureid = '$id'");
		if( db_affected_rows() > 0 )
		{
			output('`@Creature successfully deleted.`0`n`n');
			module_delete_objprefs('creatures',$id);
		}
		else
		{
			output('Creature not deleted: %s', db_error(LINK));
		}

		addnav('Options');
		addnav('Previous Page',$from.'&level='.$level);
		addnav('Main Page',$from);
		addnav('Add a Creature',$from.'&op=add&level='.$level);
	}
	elseif( $op == 'add' || $op == 'edit' )
	{
		$row = array('creatureid'=>0);
		if( $id > 0 )
		{
			$sql = "SELECT *
					FROM " . db_prefix('creatures') . "
					WHERE creatureid = '$id'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			if( db_num_rows($result) <> 1 )
			{
				output('`$Error: That creature was not found!');
				$id = 0;
			}
		}

		if( $op == 'add' )
		{
			$levels = 'bitfield,'. 0xffffffff;
			for( $i=1; $i<=18; $i++ ) $levels .= ','.pow(2, $i).','.$i;
		}
		else
		{
			$levels = 'range,1,18,1|'.$row['creaturelevel'];
		}

		$form = array(
			"Creature Properties,title",
			"creatureid"=>"Creature id,hidden",
			"createdby"=>"Author:,viewonly",
			"creaturename"=>"Creature Name:",
			"creatureweapon"=>"Weapon Name:",
			"creaturewin"=>"Win Message:",
			"`^Displayed when the creature kills the player.,note",
			"creaturelose"=>"Death Message:",
			"`^Displayed when the creature is killed by the player.,note",
			"creaturelevel"=>"Levels:,".$levels,
			"`^There is no reason to add forest creatures to level 17 and 18 as players aren't supposed to stay at level 15. However if they get killed by the Dragon then they'll need graveyard creatures at those levels to fight in the Shades.,note",
			"forest"=>"Creature is in forest?,bool",
			"graveyard"=>"Creature is in graveyard?,bool",
			"creatureaiscript"=>"Creature's A.I.,textarearesizeable,40",
		);

		$sql = "SELECT formalname, modulename
				FROM " . db_prefix('modules') . "
				WHERE infokeys
				LIKE '%|prefs-creatures|%'
				ORDER BY formalname";
		$result = db_query($sql);
		while( $row2 = db_fetch_assoc($result) )
		{
			$formalname = $row2['formalname'];
			$modulename = modulename_sanitize($row2['modulename']);
			$modulefilename = "modules/{$modulename}.php";
			if( file_exists($modulefilename) )
			{
				require_once($modulefilename);
				$fname = $modulename.'_getmoduleinfo';
				if( function_exists($fname) )
				{
					$info = $fname();
					if( count($info['prefs-creatures']) > 0 )
					{
						$form[] = $formalname.',title'; // Each module gets its own title.
						while( list($key, $val) = each($info['prefs-creatures']) )
						{
							if( ($pos = strpos($val, ',title')) !== FALSE )
							{	// Any titles get converted to notes.
								$val = '`^`i'.str_replace(',title', '`i,note', $val);
							}
							if( is_array($val) )
							{
								$v = $val[0];
								$x = explode("|", $v);
								$val[0] = $x[0];
								$x[0] = $val;
							}
							else
							{
								$x = explode("|", $val);
							}
							$form[$modulename.'-'.$key] = $x[0];
							// Set up default values.
							$row[$modulename.'-'.$key] = ( isset($x[1]) ) ? $x[1] : '';
						}

						$sql = "SELECT setting, value
								FROM " . db_prefix('module_objprefs') . "
								WHERE modulename = '$modulename'
									AND objtype = 'creatures'
									AND objid = '$id'";
						$result2 = db_query($sql);
						while( $row3 = db_fetch_assoc($result2) )
						{
							$row[$modulename.'-'.$row3['setting']] = $row3['value'];
						}
					}
				}
			}
		}

		require_once('lib/showform.php');
		rawoutput('<form action="'.$from.'&op=save&level='.$level.'" method="POST">');
		addnav('',$from.'&op=save&level='.$level);
		showform($form, $row);
		rawoutput('<input type="hidden" name="oldvalues" value="'.htmlentities(serialize($row), ENT_COMPAT, getsetting("charset", "ISO-8859-1")).'" /></form>');

		addnav('Options');
		addnav('Previous Page',$from.'&level='.$level);
		addnav('Main Page',$from);
		addnav('Add a Creature',$from.'&op=add&level='.$level);

	}
	else
	{
		$q = httppost('q');
		$search = translate_inline('Search');
		rawoutput('<center><form action="'.$from.'&subop=search" method="POST">');
		addnav('',$from.'&subop=search');
		output('`2Creature Search: ');
		rawoutput('<input type="text" name="q" value="'.$q.'" />');
		rawoutput('<input type="submit" class="button" value="'.$search.'"></form></center><br />');

		$subop = httpget('subop');
		if( $subop == 'search' )
		{
			addnav('Options');
			addnav('Previous Page',$from.'&level='.$level);
			addnav('Main Page',$from);
			addnav('Add a Creature',$from.'&op=add&level='.$level);
			$where = "creaturename LIKE '%$q%' OR creatureweapon LIKE '%$q%' OR creaturewin LIKE '%$q%' OR creaturelose LIKE '%$q%' OR createdby LIKE '%$q%'";
		}
		else
		{
			addnav('Options');
			addnav('Add a Creature',$from.'&op=add&level='.$level);

			$where = "creaturelevel = '$level'";

			addnav('Levels');
			$sql = "SELECT count(creatureid) AS n, creaturelevel
					FROM " . db_prefix('creatures') . "
					GROUP BY creaturelevel
					ORDER BY creaturelevel";
			$result = db_query($sql);
			while( $row = db_fetch_assoc($result) )
			{
				if( $level == $row['creaturelevel'] )
				{
					addnav(array('`QLevel %s: (%s creatures)`0', $row['creaturelevel'], $row['n']),$from.'&level='.$row['creaturelevel']);
				}
				else
				{
					addnav(array('Level %s: (%s creatures)', $row['creaturelevel'], $row['n']),$from.'&level='.$row['creaturelevel']);
				}
			}
		}

		$sql = "SELECT setting, objid, value
				FROM " . db_prefix('module_objprefs') . "
				WHERE modulename = 'citythemedcreatures'
					AND objtype = 'creatures'";
		$result = db_query($sql);
		$creature_array = array();
		while( $row = db_fetch_assoc($result) )
		{
			$creature_array[$row['objid']][$row['setting']] = $row['value'];
		}

		$opshead = translate_inline('Ops');
		$name = translate_inline('Name');
		$lev = translate_inline('Level');
		$forest = translate_inline('Forest');
		$grave = translate_inline('Graveyard');
		$author = translate_inline('Author');
		$edit = translate_inline('Edit');
		$confirm = translate_inline('Are you sure you wish to delete this creature?');
		$del = translate_inline('Del');
		$all = translate_inline('All');
		$notset = translate_inline('Not Set');
		$yesno = translate_inline(array('Yes','No'));

		rawoutput("<center><table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
		rawoutput("<tr class=\"trhead\"><td>$opshead</td><td>$name</td><td>$forest</td><td>$grave</td><td>$author</td></tr>");

		$sql = "SELECT *
				FROM " . db_prefix('creatures') . "
				WHERE $where
				ORDER BY creaturelevel, creaturename";
		$result = db_query($sql);

		$i = 0;
		while( $row = db_fetch_assoc($result) )
		{
			rawoutput('<tr class="'.($i%2==0?'trdark':'trlight').'"><td nowrap="nowrap">[ <a href="'.$from.'&op=edit&creatureid='.$row['creatureid'].'&level='.$row['creaturelevel'].'">'.$edit.'</a> | <a href="'.$from.'&op=del&creatureid='.$row['creatureid'].'&level='.$row['creaturelevel'].'" onClick="return confirm(\''.$confirm.'\');">'.$del.'</a> ]</td><td>');
			addnav('',$from.'&op=edit&creatureid='.$row['creatureid'].'&level='.$row['creaturelevel']);
			addnav('',$from.'&op=del&creatureid='.$row['creatureid'].'&level='.$row['creaturelevel']);
			output_notl('%s', $row['creaturename']);
			rawoutput('</td><td align="center">');
			output_notl('%s', ($row['forest']==1?$yesno[0]:$yesno[1]));
			rawoutput('</td><td align="center">');
			output_notl('%s', ($row['graveyard']==1?$yesno[0]:$yesno[1]));
			rawoutput('</td><td>');
			output_notl('%s', $row['createdby']);
			rawoutput('</td></tr>');
			$i++;
		}
		rawoutput('</table></center><br />');
		output("`2Please note that the creatures are listed in alphabetical order, unless you've added colour codes to their names in which case they may appear out of order.`n`n");
	}

	if( $session['user']['superuser'] & SU_DEVELOPER )
	{
		addnav('Developer');
		addnav('Refresh',$from.'&op='.$op.'&creatureid='.$id.'&level='.$level);
	}

	addnav('Navigation');
	require_once('lib/superusernav.php');
	superusernav();

	page_footer();
}
?>