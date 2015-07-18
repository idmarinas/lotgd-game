<?php
	page_header("Skills Editor");
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Options - Skills");
	addnav("New Skill", "runmodule.php?module=skills&op=editor&op2=newskill");
	addnav("Show all skills", "runmodule.php?module=skills&op=editor&op2=showskills");
	addnav("Options - Buffs");
	addnav("New Buff", "runmodule.php?module=skills&op=editor&op2=newbuff");
	addnav("Show all buffs", "runmodule.php?module=skills&op=editor&op2=showbuffs");
	addnav("Other Options");
	require_once("lib/showform.php");
	switch(httpget('op2')) {
		case "addbuff":
			$post = httpallpost();
			$id = httpget('id');
			if ($post['buffadd'] != 0) {
				$sql = "SELECT buffids FROM ".db_prefix("skills")." WHERE skillid = $id";
				$result = db_query($sql);
				$skill = db_fetch_assoc($result);
				$buffids = unserialize($skill['buffids']);
				if (!isset($buffids[$post['buffadd']])) {
					$buffids[$post['buffadd']] = 1;		
					$buffids = serialize($buffids);		
					$sql = "UPDATE ".db_prefix("skills")." SET
								buffids = '$buffids'
							WHERE skillid = $id";
					db_query($sql);
					invalidatedatacache("skills-skill-$id");
					output("Buff added to this skill.");
				} else {
					output("Buff is already attached to this skill.");
				}
			} else {
				output("No buff selected!");
			}
					
			break;
		case "removebuff":
			$id = httpget('id');
			$id2 = httpget('id2');
			if ($id2 != 0) {
				$sql = "SELECT buffids FROM ".db_prefix("skills")." WHERE skillid = $id";
				$result = db_query($sql);
				$skill = db_fetch_assoc($result);
				$buffids = unserialize($skill['buffids']);
				if (isset($buffids[$id2])) {
					unset($buffids[$id2]);
					$buffids = serialize($buffids);		
					$sql = "UPDATE ".db_prefix("skills")." SET
								buffids = '$buffids'
							WHERE skillid = $id";
					db_query($sql);
					invalidatedatacache("skills-skill-$id");
					output("Buff removed from this skill.");
				} else {
					output("Buff was not attached to this skill.");
				}
					
			}
			break;
		case "newskill2":
			$post = httpallpost();
			$id = httpget('id');
			if (!$id) {
				$sql = "INSERT INTO ".db_prefix("skills")." (`name`,`ccode`,`cooldown`,`globals`,`requirement`,`execvalue`,`buffids`) VALUES ('{$post['name']}', '{$post['ccode']}', '{$post['cooldown']}', '{$post['globals']}', '{$post['requirement']}', '{$post['execvalue']}', 'a:0:{}')";
				debug($sql);
				db_query($sql);
				output("'`^%s`0' inserted.", $post['name']);
			} else {
				$sql = "UPDATE ".db_prefix("skills")." SET
							name = '{$post['name']}',
							ccode = '{$post['ccode']}',
							cooldown = '{$post['cooldown']}',
							globals = '{$post['globals']}',
							requirement = '{$post['requirement']}',
							execvalue = '{$post['execvalue']}'
						WHERE skillid = $id";
				db_query($sql);
				invalidatedatacache("skills-skill-$id");
				output("'`^%s`0' updated.`n", $post['name']);

			}
			break;
		case "newskill":
			$id=httpget("id");
			$yes = translate_inline("Yes");
			$no = translate_inline("No");
			if ($id != "") {
				$sql = "SELECT * FROM ".db_prefix("skills")." WHERE skillid = $id";
				$result = db_query_cached($sql, "skills-skill-$id");
				$skill = db_fetch_assoc($result);
			}
			else {
				$skill = array();
			}
			rawoutput("<form action='runmodule.php?module=skills&op=editor&op2=newskill2&id=$id' method='post'>");
			addnav("", "runmodule.php?module=skills&op=editor&op2=newskill2&id=$id");
			$format = array(
				"Skill information,title",
					"skillid"=>"Skill id,viewonly",
					"name"=>"Skill name,string,50",
					"ccode"=>"Color Code,string,5",
					"cooldown"=>"Cooldown (in rounds),int",
					"globals"=>"Put global commands needed here,textarea",
					"requirement"=>"Requirement to use,string,250",
					"Whatever is set as requirement must evaluate to true for the skill to become available to use,note",
					"execvalue"=>"Exec value,textarearesizeable",
			);
			showform($format, $skill);
			rawoutput("</form>");
			if ($id != "") {
				$sql = "SELECT buffid, buffname, buffshortname FROM ".db_prefix("skillsbuffs");
				$result = db_query($sql);
				while ($row = db_fetch_assoc($result)){
				  $row['buffname'] = str_replace(",", " ", $row['buffname']);
				  $row['buffshortname'] = str_replace(",", " ", $row['buffshortname']);
				  $buffarray[$row['buffid']]=$row;
				  $buffs[] = $row['buffid'];
				  $buffs[] = "{$row['buffname']} ({$row['buffshortname']})";
				}
				if (is_array($buffs) && count($buffs))
					$buffsjoin = "0,none," . join(",",$buffs);
				else
					$buffsjoin = "0,none,";
				$levelreq = translate_inline("Level");
				$cost = translate_inline("Cost");
				$name = translate_inline("Name");
				$edit = translate_inline("Edit");
				$del = translate_inline("Remove");
				$conf = translate_inline("Do you really want to remove this buff from this skill?");
				$nobuffs = translate_inline("There are no buffs attached to this skill.");
				output("Buffs attached to this skill:`n");
				$buffids=unserialize($skill['buffids']);
				foreach ($buffids as $buffid => $active){
						output_notl("`^%s `7- `&`i%s`i `7- [", $buffarray[$buffid]['buffname'], $buffarray[$buffid]['buffshortname']);
						rawoutput("<a href='runmodule.php?module=skills&op=editor&op2=newbuff&id=".$buffid."'>$edit</a> - <a href='runmodule.php?module=skills&op=editor&op2=removebuff&id=".$id."&id2=".$buffid."' onClick=\"return confirm('$conf');\">$del</a>");
						addnav("", "runmodule.php?module=skills&op=editor&op2=newbuff&id=".$buffid);
						addnav("", "runmodule.php?module=skills&op=editor&op2=removebuff&id=".$id."&id2=".$buffid);
						output_notl("]`0`n");
				}
				if (count($buffids) == 0) 
					rawoutput($nobuffs);
				rawoutput("<form action='runmodule.php?module=skills&op=editor&op2=addbuff&id=$id' method='post'>");
				addnav("", "runmodule.php?module=skills&op=editor&op2=addbuff&id=$id");
				$format = array(
						"buffadd"=>"Add this buff to this skill,enum,$buffsjoin",
				);
				showform($format, $skill);
				rawoutput("</form>");
			}
			break;
		case "":
		case "showskills":
			$sql = "SELECT skillid, name, ccode FROM ".db_prefix("skills");
			$result = db_query($sql);
			$name = translate_inline("Name");
			$edit = translate_inline("Edit");
			$del = translate_inline("Delete");
			$conf = translate_inline("Do you really want to delete this skill?");
			$noskills = translate_inline("There are no skills defined.");
			output("`bSkills in the database:`b`n`n");
			rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
			rawoutput("<tr class='trhead'>");
			rawoutput("<td></td><td>$name</td></tr>");
			$number=db_num_rows($result);
			for ($i=0;$i<$number;$i++){
				$row = db_fetch_assoc($result);
					rawoutput("<tr class='".($i%2==0?"trdark":"trlight")."'>", true);
					rawoutput("<td>[ <a href='runmodule.php?module=skills&op=editor&op2=newskill&id=".$row['skillid']."'>$edit</a> - <a href='runmodule.php?module=skills&op=editor&op2=delskill&id=".$row['skillid']."' onClick=\"return confirm('$conf');\">$del</a> ]</td><td>");
					addnav("", "runmodule.php?module=skills&op=editor&op2=newskill&id=".$row['skillid']);
					addnav("", "runmodule.php?module=skills&op=editor&op2=delskill&id=".$row['skillid']);
					output_notl("%s%s", $row['ccode'],$row['name']);
					rawoutput("</td></tr>");
			}
			if ($number == 0) 
				rawoutput("<tr class='trlight'><td colspan=4>$noskills</td></tr>");
			rawoutput("</table>");
			break;
		case "delskill":
			$id = httpget('id');
			$sql = "DELETE FROM ".db_prefix("skills")." WHERE skillid = $id LIMIT 1";
			$result = db_query($sql);
			if (db_affected_rows($result)) output("Skill succesfully deleted.`n");			
			else output("While deleting this skill an error occurred. Probably someone has already deleted this skill.`n");
			break;
		case "newbuff":
			$id=httpget("id");
			$yes = translate_inline("Yes");
			$no = translate_inline("No");
			if ($id != "") {
				$sql = "SELECT * FROM ".db_prefix("skillsbuffs")." WHERE buffid = $id";
				$result = db_query($sql);
				$buff = db_fetch_assoc($result);
			} else {
				$buff = array();
			}
			
			rawoutput("<form action='runmodule.php?module=skills&op=editor&op2=newbuff2&id=$id' method='post'>");
			addnav("", "runmodule.php?module=skills&op=editor&op2=newbuff2&id=$id");
			$format = array(
				"General Settings,title",				
					'buffid'=>"Buff ID,viewonly",
					'buffname'=>"Buff name (shown in editor),string,250",
					'buffshortname'=>"Buff name (shown in charstats),string,250",
					"The charstats name will automatically use the color of the skill that uses it,note",
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
					"You can use %c in any message and it will be replaced with the color code of the skill that activates the buff,note",
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
			rawoutput("</form>");
			break;
		case "newbuff2":
			$post = httpallpost();
			$id = httpget('id');
			if (!$id) {
				$sql = "INSERT INTO ".db_prefix("skillsbuffs")." (`lifetap`, `roundmsg`, `rounds`, `buffname`, `buffshortname`, `invulnerable`, `dmgmod`,	`badguydmgmod`,	`atkmod`, `badguyatkmod`, `defmod`,`badguydefmod`, `damageshield`, `regen`, `minioncount`, `maxbadguydamage`, `minbadguydamage`, `maxgoodguydamage`, `mingoodguydamage`, `startmsg`, `wearoff`, `effectfailmsg`, `effectnodmgmsg`, `effectmsg`, `allowinpvp`, `allowintrain`, `survivenewday`,`expireafterfight`) VALUES ('{$post['lifetap']}','{$post['roundmsg']}', '{$post['rounds']}', '{$post['buffname']}', '{$post['buffshortname']}', '{$post['invulnerable']}', '{$post['dmgmod']}', '{$post['badguydmgmod']}', '{$post['atkmod']}', '{$post['badguyatkmod']}', '{$post['defmod']}', '{$post['badguydefmod']}', '{$post['damageshield']}', '{$post['regen']}', '{$post['minioncount']}', '{$post['maxbadguydamage']}', '{$post['minbadguydamage']}', '{$post['maxgoodguydamage']}', '{$post['mingoodguydamage']}','{$post['startmsg']}', '{$post['wearoff']}',  '{$post['effectfailmsg']}', '{$post['effectnodmgmsg']}', '{$post['effectmsg']}', '{$post['allowinpvp']}', '{$post['allowintrain']}', '{$post['survivenewday']}','{$post['expireafterfight']}')";
				db_query($sql);
				output("'`^%s`0' inserted.", $post['buffname']);
			} else {
				$sql = "UPDATE ".db_prefix("skillsbuffs")." SET
							buffname = '{$post['buffname']}',
							rounds = '{$post['rounds']}',
							roundmsg = '{$post['roundmsg']}',
							lifetap = '{$post['lifetap']}',
							buffshortname = '{$post['buffshortname']}',
							invulnerable = '{$post['invulnerable']}',
							dmgmod = '{$post['dmgmod']}',
							badguydmgmod = '{$post['badguydmgmod']}',
							atkmod = '{$post['atkmod']}',
							badguyatkmod = '{$post['badguyatkmod']}',
							defmod = '{$post['defmod']}',
							badguydefmod = '{$post['badguydefmod']}',
							damageshield = '{$post['damageshield']}',
							regen = '{$post['regen']}',
							minioncount = '{$post['minioncount']}',
							maxbadguydamage = '{$post['maxbadguydamage']}',
							minbadguydamage = '{$post['minbadguydamage']}',
							maxgoodguydamage = '{$post['maxgoodguydamage']}',
							mingoodguydamage = '{$post['mingoodguydamage']}',
							startmsg = '{$post['startmsg']}',
							roundmsg = '{$post['roundmsg']}',
							wearoff = '{$post['wearoff']}',
							effectfailmsg = '{$post['effectfailmsg']}',
							effectnodmgmsg = '{$post['effectnodmgmsg']}',
							effectmsg = '{$post['effectmsg']}',
							allowinpvp = '{$post['allowinpvp']}',
							allowintrain = '{$post['allowintrain']}',
							survivenewday = '{$post['survivenewday']}'
							expireafterfight = '{$post['expireafterfight']}'
						WHERE buffid = $id";
				db_query($sql);
				invalidatedatacache("skills-buff-$id");
				output("'`^%s`0' updated.", $post['buffname']);
			}
			break;
		case "showbuffs":
			$sql = "SELECT buffid, buffname, buffshortname FROM ".db_prefix("skillsbuffs")." ORDER BY buffid ASC";
			$result = db_query($sql);
			$edit = translate_inline("Edit");
			$del = translate_inline("Delete");
			$conf = translate_inline("Do you really want to delete this buff?");
			$nobuffs = translate_inline("There are no buffs defined.");
			$number = db_num_rows($result);
			output("`bBuffs in the database:`b`n`n");
			for ($i=0;$i<$number;$i++) {
				$row=db_fetch_assoc($result);
				rawoutput("[ <a href='runmodule.php?module=skills&op=editor&op2=newbuff&id=".$row['buffid']."'>$edit</a> - <a href='runmodule.php?module=skills&op=editor&op2=delbuff&id=".$row['buffid']."' onClick=\"return confirm('$conf');\">$del</a>");
				output_notl("] - `^%s `7- `&`i%s`i `0`n", $row['buffname'], $row['buffshortname']);
				addnav("", "runmodule.php?module=skills&op=editor&op2=newbuff&id=".$row['buffid']);
				addnav("", "runmodule.php?module=skills&op=editor&op2=delbuff&id=".$row['buffid']);
			}
			if ($number == 0)
				output_notl("%s",$nobuffs);
			break;
		case "delbuff":
			$id = httpget('id');
			$sql = "DELETE FROM ".db_prefix("skillsbuffs")." WHERE buffid = $id LIMIT 1";
			$result = db_query($sql);
			if (db_affected_rows($result)) output("Buff succesfully deleted.`n`n");
			else output("While deleting this buffs an error occured. Probably someone else already deleted this buff.`n`n");
			$sql = "SELECT skillid,name,buffids FROM ".db_prefix("skills");
			$result = db_query($sql);
			$number = db_num_rows($result);
			for ($i=0;$i<$number;$i++){
				$row = db_fetch_assoc($result);
				$buffids = unserialize($row['buffids']);
				if (isset($buffids[$id])) {
					unset($buffids[$id]);
					$buffids = serialize($buffids);		
					$sql = "UPDATE ".db_prefix("skills")." SET
								buffids = '$buffids'
							WHERE skillid = {$row['skillid']}";
					db_query($sql);
					invalidatedatacache("skills-skill-{$row['skillid']}");
					output("Buff removed from skill `^%s`0.`n",$row['name']);
				}
			}

	}
	page_footer();
?>