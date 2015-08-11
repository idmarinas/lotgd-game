<?php
			$id=httpget("id");
			$yes = translate_inline("Yes");
			$no = translate_inline("No");
			if ($id != "") {
				$sql = "SELECT * FROM ".db_prefix("magicitembuffs")." WHERE buffid = $id";
				$result = db_query($sql);
				$buff2 = db_fetch_assoc($result);
				$buff = unserialize($buff2['itembuff']);
				$buff['buffname'] = $buff2['buffname'];
			} else {
				$buff = array();
			}
			
			rawoutput("<form action='runmodule.php?module=mysticalshop_buffs&op=editor&what=newbuff2&id=$id' method='post'>");
			addnav("", "runmodule.php?module=mysticalshop_buffs&op=editor&what=newbuff2&id=$id");
			$format = array(
				"General Settings,title",				
					'buffname'=>"Buff name (shown in editor),string,250",
					'name'=>"Buff name (shown in charstats),string,250",
					'rounds'=>"Rounds,string,250",
				"Combat Modifiers,title",				
					'dmgmod'=>"Damage Modifier (Goodguy),string,250",
					'atkmod'=>"Attack Modifier (Goodguy),string,250",
					'defmod'=>"Defense Modifier (Goodguy),string,250",
					'badguydmgmod'=>"Damage Modifier (Badguy),string,250",
					'badguyatkmod'=>"Attack Modifier (Badguy),string,250",
					'badguydefmod'=>"Defense Modifier (Badguy),string,250",
				"Misc Combat Modifiers,title",				
					'lifetap'=>"Lifetap,string,250",
					'damageshield'=>"Damage Shield,string,250",
					'regen'=>"Regeneration,string,250",
				"Minion Count Settings,title",
					'minioncount'=>"Minion count,string,250",
					'minbadguydamage'=>"Min Badguy Damage,string,250",
					'maxbadguydamage'=>"Max Badguy Damage,string,250",
					'mingoodguydamage'=>"Max Goodguy Damage,string,250",
					'maxgoodguydamage'=>"Max Goodguy Damage,string,250",
				"Message Settings,title",
					'startmsg'=>"Start Message,string,250",
					'roundmsg'=>"Round Message,string,250",
					'wearoff'=>"Wear Off Message,string,250",
					'effectmsg'=>"Effect Message,string,250",
					'effectfailmsg'=>"Effect Fail Message,string,250",
					'effectnodmgmsg'=>"Effect No Damage Message,string,250",
				"Misc Settings,title",						
					'allowinpvp'=>"Allow in PvP?,bool",
					'allowintrain'=>"Allow in Training?,bool",
					'survivenewday'=>"Survive New Day?,bool",
					'invulnerable'=>"Invulnerable?,bool",
					'expireafterfight'=>"Expires after fight?,bool",
			);
			showform($format, $buff);
			output("`n`n`bOn Dynamic Buffs`b`n");
			output("`@In the above, for most fields, you can choose to enter valid PHP code, substituting <fieldname> for fields in the user's account table.`n");
			output("Examples of code you might enter:`n");
			output("`^<charm>`n");
			output("round(<maxhitpoints>/10)`n");
			output("round(<level>/max(<gems>,1))`n");
			output("`@Fields you might be interested in for this: `n");
			output("`3name, sex `7(0=male 1=female)`3, specialty `7(DA=darkarts MP=mystical TS=thief)`3,`n");
			output("experience, gold, weapon `7(name)`3, armor `7(name)`3, level,`n");
			output("defense, attack, alive, goldinbank,`n");
			output("spirits `7(-2 to +2 or -6 for resurrection)`3, hitpoints, maxhitpoints, gems,`n");
			output("weaponvalue `7(gold value)`3, armorvalue `7(gold value)`3, turns, title, weapondmg, armordef,`n");
			output("age `7(days since last DK)`3, charm, playerfights, dragonkills, resurrections `7(times died since last DK)`3,`n");
			output("soulpoints, gravefights, deathpower `7(Ramius favor)`3,`n");
			output("race, dragonage, bestdragonage`n`n");
			output("You can also use module preferences by using <modulename|preference> (for instance '<specialtymystic|uses>' or '<drinks|drunkeness>'`n`n");
			output("`@Finally, starting a field with 'debug:' will enable debug output for that field to help you locate errors in your implementation.");
			output("While testing new buffs, you should be sure to debug fields before you release them on the world, as the PHP script will otherwise throw errors to the user if you have any, and this can break the site at various spots (as in places that redirects should happen).");
			rawoutput("</form>");
?>