<?php
function mysticalshop_addbuff(){
	if (get_module_pref("ring","mysticalshop")){
		$id = get_module_pref("ringid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$buff = unserialize($row['itembuff']);
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="ring";
			apply_buff($row['buffname'],$buff);
		}
	}
	if (get_module_pref("amulet","mysticalshop")){
		$id = get_module_pref("amuletid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$buff = unserialize($row['itembuff']);
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="amulet";
			apply_buff($row['buffname'],$buff);
		}
	}
	if (get_module_pref("weapon","mysticalshop")){
		$id = get_module_pref("weaponid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$buff = unserialize($row['itembuff']);
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="weapon";
			apply_buff($row['buffname'],$buff);
		}
	}	
	if (get_module_pref("armor","mysticalshop")){
		$id = get_module_pref("armorid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
			$result = db_query($sql);			
			$row = db_fetch_assoc($result);			
			$buff = unserialize($row['itembuff']);						
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="armor";		
			apply_buff($row['buffname'],$buff);			
		}
	}	
	if (get_module_pref("cloak","mysticalshop")){
		$id = get_module_pref("cloakid","mysticalshop");
			$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
			$result = db_query($sql);			
			$row = db_fetch_assoc($result);			
			$buff = unserialize($row['itembuff']);						
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="cloak";		
			apply_buff($row['buffname'],$buff);			
		}	
	}			
	if (get_module_pref("glove","mysticalshop")){
		$id = get_module_pref("gloveid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
			$result = db_query($sql);			
			$row = db_fetch_assoc($result);			
			$buff = unserialize($row['itembuff']);						
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="gloves";		
			apply_buff($row['buffname'],$buff);			
		}	
	}	
	if (get_module_pref("boots","mysticalshop")){
		$id = get_module_pref("bootid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
			$result = db_query($sql);			
			$row = db_fetch_assoc($result);			
			$buff = unserialize($row['itembuff']);						
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="boots";		
			apply_buff($row['buffname'],$buff);			
		}	
	}	
	if (get_module_pref("misc","mysticalshop")){
		$id = get_module_pref("miscid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
			$result = db_query($sql);			
			$row = db_fetch_assoc($result);			
			$buff = unserialize($row['itembuff']);						
			if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="misc";		
			apply_buff($row['buffname'],$buff);			
		}	
	}	
	if (get_module_pref("helm","mysticalshop")){
			$id = get_module_pref("helmid","mysticalshop");
			$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$buffid = $row['buffid'];
			if ($buffid>0){
				$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$buff = unserialize($row['itembuff']);
				if (!isset($buff['schema']) || $buff['schema'] == "")
				$buff['schema']="helmet";
				apply_buff($row['buffname'],$buff);
			}
		}	
		return true;
	}
function mysticalshop_stripbuff(){
global $session;
if (get_module_pref("ring","mysticalshop") == 0){
	$id = get_module_pref("ringid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);			
		strip_buff($row['buffname']);		
	}
}
if (get_module_pref("amulet","mysticalshop") == 0){
	$id = get_module_pref("amuletid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);			
		strip_buff($row['buffname']);		
	}
}
if (get_module_pref("weapon","mysticalshop") == 0){
	$id = get_module_pref("weaponid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);	
		strip_buff($row['buffname']);			
	}
}
if (get_module_pref("armor","mysticalshop")  == 0){
	$id = get_module_pref("armorid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);			
		strip_buff($row['buffname']);		
	}
}
if (get_module_pref("cloak","mysticalshop")  == 0){
	$id = get_module_pref("cloakid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);			
		strip_buff($row['buffname']);		
	}	
} 
if (get_module_pref("glove","mysticalshop") == 0){
	$id = get_module_pref("gloveid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);			
		strip_buff($row['buffname']);		
	}	
} 
if (get_module_pref("boots","mysticalshop") == 0){
	$id = get_module_pref("bootid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);											
		strip_buff($row['buffname']);		
	}	
}
if (get_module_pref("misc","mysticalshop")  == 0){
	$id = get_module_pref("miscid","mysticalshop");
	$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$buffid = $row['buffid'];
	if ($buffid>0){		
		$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";				
		$result = db_query($sql);			
		$row = db_fetch_assoc($result);
		strip_buff($row['buffname']);		
	}
}
if (get_module_pref("helm","mysticalshop")  == 0){
		$id = get_module_pref("helmid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id='$id'";				
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){			
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";
			$result = db_query($sql);			
			$row = db_fetch_assoc($result);
			strip_buff($row['buffname']);		
		}
	}
	return true;
}
function mysticalshop_buffs_form($item){
	// Let's sanitize the data
	if (!isset($item['buffname'])) $item['buffname'] = "";
	if (!isset($item['buffid'])) $item['buffid'] = "";
	if (!isset($item['itembuff'])) $item['itembuff'] = array();
	if (!isset($item['itembuff']['name']))
		$item['itembuff']['name'] = "";
	if (!isset($item['itembuff']['roundmsg']))
		$item['itembuff']['roundmsg'] = "";
	if (!isset($item['itembuff']['wearoff']))
		$item['itembuff']['wearoff'] = "";
	if (!isset($item['itembuff']['effectmsg']))
		$item['itembuff']['effectmsg'] = "";
	if (!isset($item['itembuff']['effectnodmgmsg']))
		$item['itembuff']['effectnodmgmsg'] = "";
	if (!isset($item['itembuff']['effectfailmsg']))
		$item['itembuff']['effectfailmsg'] = "";
	if (!isset($item['itembuff']['rounds']))
		$item['itembuff']['rounds'] = 0;
	if (!isset($item['itembuff']['atkmod']))
		$item['itembuff']['atkmod'] = "";
	if (!isset($item['itembuff']['defmod']))
		$item['itembuff']['defmod'] = "";
	if (!isset($item['itembuff']['invulnerable']))
		$item['itembuff']['invulnerable'] = "";
	if (!isset($item['itembuff']['regen']))
		$item['itembuff']['regen'] = "";
	if (!isset($item['itembuff']['minioncount']))
		$item['itembuff']['minioncount'] = "";
	if (!isset($item['itembuff']['minbadguydamage']))
		$item['itembuff']['minbadguydamage'] = "";
	if (!isset($item['itembuff']['maxbadguydamage']))
		$item['itembuff']['maxbadguydamage'] = "";
	if (!isset($item['itembuff']['mingoodguydamage']))
		$item['itembuff']['mingoodguydamage'] = "";
	if (!isset($item['itembuff']['maxgoodguydamage']))
		$item['itembuff']['maxgoodguydamage'] = "";
	if (!isset($item['itembuff']['lifetap']))
		$item['itembuff']['lifetap'] = "";
	if (!isset($item['itembuff']['damageshield']))
		$item['itembuff']['damageshield'] = "";
	if (!isset($item['itembuff']['badguydmgmod']))
		$item['itembuff']['badguydmgmod'] = "";
	if (!isset($item['itembuff']['badguyatkmod']))
		$item['itembuff']['badguyatkmod'] = "";
	if (!isset($item['itembuff']['badguydefmod']))
		$item['itembuff']['badguydefmod'] = "";
	//
	rawoutput("<form action='runmodule.php?module=mysticalshop_buffs&op=editor&what=save&id={$item['buffid']}' method='POST'>");
	addnav("","runmodule.php?module=mysticalshop_buffs&op=editor&what=save&id={$item['buffid']}");
	rawoutput("<table width=75%>");
	rawoutput("<tr><td>");
	output("Buff Display Name:`n");
	rawoutput("<input name='magicitembuffs[buffname]' value=\"".htmlentities($item['buffname'])."\" size='50'><br/>");	
	output("Buff name - Should be same as above:`n");
	rawoutput("<input name='magicitembuffs[itembuff][name]' value=\"".htmlentities($item['itembuff']['name'])."\" size='50'><br/><br/>");
	output("`bBuff Messages:`b`n");
	output("Each round:`n");
	rawoutput("<input name='magicitembuffs[itembuff][roundmsg]' value=\"".htmlentities($item['itembuff']['roundmsg'])."\" size='50'><br/>");
	output("Wear off:`n");
	rawoutput("<input name='magicitembuffs[itembuff][wearoff]' value=\"".htmlentities($item['itembuff']['wearoff'])."\" size='50'><br/>");
	output("Effect:`n");
	rawoutput("<input name='magicitembuffs[itembuff][effectmsg]' value=\"".htmlentities($item['itembuff']['effectmsg'])."\" size='50'><br/>");
	output("Effect No Damage:`n");
	rawoutput("<input name='magicitembuffs[itembuff][effectnodmgmsg]' value=\"".htmlentities($item['itembuff']['effectnodmgmsg'])."\" size='50'><br/>");
	output("Effect Fail:`n");
	rawoutput("<input name='magicitembuffs[itembuff][effectfailmsg]' value=\"".htmlentities($item['itembuff']['effectfailmsg'])."\" size='50'><br/>");
	output("(message replacements: {badguy}, {goodguy}, {weapon}, {armor}, {creatureweapon}, and where applicable {damage}.)`n");
	output("`n`bEffects:`b`n");
	output("Rounds to last - (-1 for Unlimited Turns):`n");
	rawoutput("<input name='magicitembuffs[itembuff][rounds]' value=\"".htmlentities((int)($item['itembuff']['rounds']))."\" size='50'><br/>");
	output("Player Atk mod:`n");
	rawoutput("<input name='magicitembuffs[itembuff][atkmod]' value=\"".htmlentities($item['itembuff']['atkmod'])."\" size='50'>");
	output("(multiplier)`n");
	output("Player Def mod:`n");
	rawoutput("<input name='magicitembuffs[itembuff][defmod]' value=\"".htmlentities($item['itembuff']['defmod'])."\" size='50'>");
	output("(multiplier)`n");
	output("Player is invulnerable (1 = yes, 0 = no):");
	rawoutput("<input name='magicitembuffs[itembuff][invulnerable]' value=\"".htmlentities($item['itembuff']['invulnerable'])."\" size=50><br/>");
	output("Regen:`n");
	rawoutput("<input name='magicitembuffs[itembuff][regen]' value=\"".htmlentities($item['itembuff']['regen'])."\" size='50'><br/>");
	output("Minion Count:`n");
	rawoutput("<input name='magicitembuffs[itembuff][minioncount]' value=\"".htmlentities($item['itembuff']['minioncount'])."\" size='50'><br/>");

	output("Min Badguy Damage:`n");
	rawoutput("<input name='magicitembuffs[itembuff][minbadguydamage]' value=\"".htmlentities($item['itembuff']['minbadguydamage'])."\" size='50'><br/>");
	output("Max Badguy Damage:`n");
	rawoutput("<input name='magicitembuffs[itembuff][maxbadguydamage]' value=\"".htmlentities($item['itembuff']['maxbadguydamage'])."\" size='50'><br/>");
	output("Min Goodguy Damage:`n");
	rawoutput("<input name='magicitembuffs[itembuff][mingoodguydamage]' value=\"".htmlentities($item['itembuff']['mingoodguydamage'])."\" size='50'><br/>");
	output("Max Goodguy Damage:`n");
	rawoutput("<input name='magicitembuffs[itembuff][maxgoodguydamage]' value=\"".htmlentities($item['itembuff']['maxgoodguydamage'])."\" size='50'><br/>");

	output("Lifetap:`n");
	rawoutput("<input name='magicitembuffs[itembuff][lifetap]' value=\"".htmlentities($item['itembuff']['lifetap'])."\" size='50'>");
	output("(multiplier)`n");
	output("Damage shield:`n");
	rawoutput("<input name='magicitembuffs[itembuff][damageshield]' value=\"".htmlentities($item['itembuff']['damageshield'])."\" size='50'>");
	output("(multiplier)`n");
	output("Badguy Damage mod:`n");
	rawoutput("<input name='magicitembuffs[itembuff][badguydmgmod]' value=\"".htmlentities($item['itembuff']['badguydmgmod'])."\" size='50'>");
	output("(multiplier)`n");
	output("Badguy Atk mod:`n");
	rawoutput("<input name='magicitembuffs[itembuff][badguyatkmod]' value=\"".htmlentities($item['itembuff']['badguyatkmod'])."\" size='50'>");
	output("(multiplier)`n");
	output("Badguy Def mod:`n");
	rawoutput("<input name='magicitembuffs[itembuff][badguydefmod]' value=\"".htmlentities($item['itembuff']['badguydefmod'])."\" size='50'>");
	output("(multiplier)`n`n");
	//
	output("`bOn Dynamic Buffs`b`n");
	output("`@In the above, for most fields, you can choose to enter valid PHP code, substituting <fieldname> for fields in the user's account table.`n");
	output("Examples of code you might enter:`n");
	output("`^<charm>`n");
	output("round(<maxhitpoints>/10)`n");
	output("round(<level>/max(<gems>,1))`n");
	output("`@Fields you might be interested in for this: `n");
	output_notl("`3name, sex `7(0=male 1=female)`3, specialty `7(DA=darkarts MP=mystical TS=thief)`3,`n");
	output_notl("experience, gold, weapon `7(name)`3, armor `7(name)`3, level,`n");
	output_notl("defense, attack, alive, goldinbank,`n");
	output_notl("spirits `7(-2 to +2 or -6 for resurrection)`3, hitpoints, maxhitpoints, gems,`n");
	output_notl("weaponvalue `7(gold value)`3, armorvalue `7(gold value)`3, turns, title, weapondmg, armordef,`n");
	output_notl("age `7(days since last DK)`3, charm, playerfights, dragonkills, resurrections `7(times died since last DK)`3,`n");
	output_notl("soulpoints, gravefights, deathpower `7(Ramius favor)`3,`n");
	output_notl("race, dragonage, bestdragonage`n`n");
	output("You can also use module preferences by using <modulename|preference> (for instance '<specialtymystic|uses>' or '<drinks|drunkeness>'`n`n");
	output("`@Finally, starting a field with 'debug:' will enable debug output for that field to help you locate errors in your implementation.");
	output("While testing new buffs, you should be sure to debug fields before you release them on the world, as the PHP script will otherwise throw errors to the user if you have any, and this can break the site at various spots (as in places that redirects should happen).");
	rawoutput("</td></tr></table>");
	$save = translate_inline("Save Buff");
	rawoutput("<input type='submit' class='button' value='$save'></form>");
}
?>