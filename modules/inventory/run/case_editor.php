<?php
	page_header("Item Editor");
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Options - Items");
	addnav("New Item", "runmodule.php?module=inventory&op=editor&op2=newitem");
	addnav("Show all items", "runmodule.php?module=inventory&op=editor&op2=showitems");
	addnav("Options - Buffs");
	addnav("New Buff", "runmodule.php?module=inventory&op=editor&op2=newbuff");
	addnav("Show all buffs", "runmodule.php?module=inventory&op=editor&op2=showbuffs");
	addnav("Other Options");
	require_once("lib/showform.php");
	switch(httpget('op2')) {
		case "newitem2":
			$item = httpallpost();
			$id = httpget('id');
			$value = 0;
			while (list($k,$v)=each($item['activationhook'])){
				if ($v) $value += (int)$k;
			}
			$item['activationhook'] = $value;
			unset($item['showFormTabIndex']);
			require_once("lib/itemhandler.php");
			if (isset($item['itemid']) && $item['itemid'] == 0) unset($item['itemid']);
			inject_item($item);
			invalidatedatacache("item-activation-fightnav-specialties");
			invalidatedatacache("item-activation-forest");
			invalidatedatacache("item-activation-train");
			invalidatedatacache("item-activation-shades");
			invalidatedatacache("item-activation-village");
		case "newitem":
			$id=httpget("id");
			$subop=httpget("subop");
			require_once("lib/itemhandler.php");
			if ($id != "") {
				$item = get_item((int)$id);
				if ($subop=="module") {
					// Save modules settings
					$module = httpget("submodule");
					$post = httpallpost();
					unset($post['showFormTabIndex']);
					reset($post);
					while(list($key, $val) = each($post)) {
						set_module_objpref("items", $id, $key, $val, $module);
						output("`^Saved module objpref %s!`0`n", $key);
					}

				}
				addnav("Item properties", "runmodule.php?module=inventory&op=editor&op2=newitem&id=$id");
				module_editor_navs("prefs-items", "runmodule.php?module=inventory&op=editor&op2=newitem&subop=module&id=$id&submodule=");
			}
			if (!is_array($item)) $item = array();
			if ($subop=="module") {
				$module = httpget("submodule");
				rawoutput("<form action='runmodule.php?module=inventory&op=editor&op2=newitem&subop=module&id=$id&submodule=$module' method='POST'>");
				module_objpref_edit("items", $module, $id);
				rawoutput("</form>");
				addnav("", "runmodule.php?module=inventory&op=editor&op2=newitem&subop=module&id=$id&submodule=$module");
			} else {
				$sql = "SELECT buffid, buffname, buffshortname FROM ".db_prefix("itembuffs");
				$result = db_query($sql);
				while ($row = db_fetch_assoc($result)){
				  $row['buffname'] = str_replace(",", " ", $row['buffname']);
				  $row['buffshortname'] = str_replace(",", " ", $row['buffshortname']);
				  $buffs[] = $row['buffid'];
				  $buffs[] = "{$row['buffname']} ({$row['buffshortname']})";
				}
				if (is_array($buffs) && count($buffs))
					$buffsjoin = "0,none," . join(",",$buffs);
				else
					$buffsjoin = "0,none,";
				$enum_equip=",No where,righthand,Right Hand,lefthand,Left Hand,head,On the Head,body,On Upper Body,arms,On the Arms,legs,On Lower Body,feet,As Shoes,ring,As Ring,neck,Around the Neck,belt,As Belt";
				rawoutput("<form action='runmodule.php?module=inventory&op=editor&op2=newitem2&id=$id' method='post'>");
				addnav("", "runmodule.php?module=inventory&op=editor&op2=newitem2&id=$id");
				$format = array(
					"Basic information,title",
						"itemid"=>"Item id,viewonly",
						"class"=>"Item category, string|Loot",
						"name"=>"Item name, string|",
						"description"=>"Description, textarea,60,5|Just a normal, useless item.",
				  	"Values,title",
						"gold"=>"Gold value,int|0",
						"gems"=>"Gem value,int|0",
						"weight"=>"Weight,int|1",
						"droppable"=>"Is this item droppable,bool",
						"level"=>"Minimum level needed,range,1,15,1|1",
						"dragonkills"=>"Dragonkills needed,int|0",
						"customvalue"=>"Custom value,textarea",
						"exectext"=>"Text to display upon activation of the item,string,70",
						"Use %s to insert the item's name!,note",
						"noeffecttext"=>"Text to display if item has no effect,string,70",
						"execvalue"=>"Exec value,textarea",
						"Please see the file 'lib/itemeffects.php' for possible values,note",
						"hide"=>"Hide item from inventory?,bool",
					"Buffs and activation,title",
						"buffid"=>"Activate this buff on useage,enum,$buffsjoin",
						"charges"=>"Amount of charges the item has,int|0",
						"link"=>"Link that's called upon activation,|",
						"activationhook"=>"Hooks which show the item,bitfield,127,"
							.HOOK_NEWDAY.		",Newday,"
							.HOOK_FOREST.		",Forest,"
							.HOOK_VILLAGE.		",Village,"
							.HOOK_SHADES.		",Shades,"
							.HOOK_FIGHTNAV.	",Fightnav,"
							.HOOK_TRAIN.		",Train,"
							.HOOK_INVENTORY.	",Inventory",
					"Chances,title",
						"findchance"=>"Chance to get this item though 'get_random_item()',range,0,100,1|100",
						"loosechance"=>"Chance that this item gets damaged when dying in battle,range,0,100,1|100",
						"dkloosechance"=>"Chance to loose this item after killing the dragon,range,0,100,1|100",
					"Shop Options,title",
						"sellable"=>"Is this item sellable?,bool",
						"buyable"=>"Is this item buyable?,bool",
					"Special Settings,title",
						"uniqueforserver"=>"Is this item unique (server)?,bool",
						"uniqueforplayer"=>"Is this item unique for the player?,bool",
						"equippable"=>"Is this item equippable?,bool",
						"equipwhere"=>"Where can this item be equipped?,enum,$enum_equip",
			  );
			  showform($format, $item);
			  rawoutput("</form>");
			}
			break;
		case "takeitem":
			$id = (int)httpget('id');
			add_item($id);
			output("`\$Item no. %s added once, you now have %s pieces.", $id, check_qty($id));
		default:
		case "showitems":
			$sql = "SELECT itemid, class, name, description, gold, gems FROM ".db_prefix("item")." ORDER BY class ASC";
			$result = db_query($sql);
			$edit = translate_inline("Edit");
			$del = translate_inline("Delete");
			$take = translate_inline("Take");
			$conf = translate_inline("Do you really want to delete this item?");
			$oldclass = "";
			for ($i=0;$i<db_num_rows($result);$i++) {
				$row=db_fetch_assoc($result);
				$class = $row['class'];
				if ($class <> $oldclass) output("`n`n`^`b%s`b`0`n", $row['class']);
				$oldclass = $class;
				rawoutput("[ <a href='runmodule.php?module=inventory&op=editor&op2=newitem&id=".$row['itemid']."'>$edit</a> - <a href='runmodule.php?module=inventory&op=editor&op2=delitem&id=".$row['itemid']."' onClick=\"return confirm('$conf');\">$del</a> - <a href='runmodule.php?module=inventory&op=editor&op2=takeitem&id=".$row['itemid']."'>$take</a> ] - ");
				output_notl("`^%s `7- `&`i%s`i `7`n", $row['name'], substr($row['description'],0,47)."...");
				addnav("", "runmodule.php?module=inventory&op=editor&op2=newitem&id=".$row['itemid']);
				addnav("", "runmodule.php?module=inventory&op=editor&op2=delitem&id=".$row['itemid']);
				addnav("", "runmodule.php?module=inventory&op=editor&op2=takeitem&id=".$row['itemid']);
			}
			break;
		case "delitem":
			$id = httpget('id');
			$sql = "DELETE FROM ".db_prefix("item")." WHERE itemid = $id LIMIT 1";
			$result = db_query($sql);
			if (db_affected_rows($result)) output("Item succesfully deleted.`n`n");
			else output("While deleting this item an error occurred. Probably someone has already deleted this item.`n`n");
			$sql = "DELETE FROM ".db_prefix("inventory")." WHERE itemid = $id";
			$result = db_query($sql);
			if (db_affected_rows($result)) output("This item has been removed %s times from players' inventories.`n`n", db_affected_rows($result));
			else output("No item has been deleted from players' inventories.`n`n");
			invalidatedatacache("item-activation-fightnav-specialties");
			invalidatedatacache("item-activation-forest");
			invalidatedatacache("item-activation-train");
			invalidatedatacache("item-activation-shades");
			invalidatedatacache("item-activation-village");
			break;
		case "newbuff":
			$id=httpget("id");
			$yes = translate_inline("Yes");
			$no = translate_inline("No");
			if ($id != "") {
				$sql = "SELECT * FROM ".db_prefix("itembuffs")." WHERE buffid = $id";
				$result = db_query($sql);
				$buff = db_fetch_assoc($result);
			}
			rawoutput("<form action='runmodule.php?module=inventory&op=editor&op2=newbuff2&id=$id' method='post'>");
			addnav("", "runmodule.php?module=inventory&op=editor&op2=newbuff2&id=$id");
			rawoutput("<table border=0 cellpadding=1 cellspacing=5 cols=2 width=100%>");
			rawoutput("<tr><td width=40%>");
				output("Buff name`n(shown in editor):");
				rawoutput("</td><td>");
				rawoutput("<input name='buffname' value='{$buff['buffname']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Buff name`n(shown in charstats):");
				rawoutput("</td><td>");
				rawoutput("<input name='buffshortname' value='{$buff['buffshortname']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Rounds:");
				rawoutput("</td><td>");
				rawoutput("<input name='rounds' value='{$buff['rounds']}'>");
			rawoutput("</td></tr><tr><td colspan=2><hr>");
			rawoutput("</td></tr><tr><td>");
				output("Damage modificator (Goodguy):");
				rawoutput("</td><td>");
				rawoutput("<input name='dmgmod' value='{$buff['dmgmod']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Attack modificator (Goodguy):");
				rawoutput("</td><td>");
				rawoutput("<input name='atkmod' value='{$buff['atkmod']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Defense modificator (Goodguy):");
				rawoutput("</td><td>");
				rawoutput("<input name='defmod' value='{$buff['defmod']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Damage modificator (Badguy):");
				rawoutput("</td><td>");
				rawoutput("<input name='badguydmgmod' value='{$buff['badguydmgmod']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Attack modificator (Badguy):");
				rawoutput("</td><td>");
				rawoutput("<input name='badguyatkmod' value='{$buff['badguyatkmod']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Defense modificator (Badguy):");
				rawoutput("</td><td>");
				rawoutput("<input name='badguydefmod' value='{$buff['badguydefmod']}'>");
			rawoutput("</td></tr><tr><td colspan=2><hr>");
			rawoutput("</td></tr><tr><td>");
				output("Damageshield:");
				rawoutput("</td><td>");
				rawoutput("<input name='damageshield' value='{$buff['damageshield']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Lifetap:");
				rawoutput("</td><td>");
				rawoutput("<input name='lifetap' value='{$buff['lifetap']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Regeneration:");
				rawoutput("</td><td>");
				rawoutput("<input name='regen' value='{$buff['regen']}'>");
			rawoutput("</td></tr><tr><td colspan=2><hr>");
			rawoutput("</td></tr><tr><td>");
			rawoutput("</td></tr><tr><td>");
				output("Minion count:");
				rawoutput("</td><td>");
				rawoutput("<input name='minioncount' value='{$buff['minioncount']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Max badguy damage:");
				rawoutput("</td><td>");
				rawoutput("<input name='maxbadguydamage' value='{$buff['maxbadguydamage']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Min badguy damage:");
				rawoutput("</td><td>");
				rawoutput("<input name='minbadguydamage' value='{$buff['minbadguydamage']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Max goodguy damage:");
				rawoutput("</td><td>");
				rawoutput("<input name='maxgoodguydamage' value='{$buff['maxgoodguydamage']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Min goodguy damage:");
				rawoutput("</td><td>");
				rawoutput("<input name='mingoodguydamage' value='{$buff['mingoodguydamage']}'>");
			rawoutput("</td></tr><tr><td colspan=2><hr>");
			rawoutput("</td></tr><tr><td>");
				output("Start message:");
				rawoutput("</td><td>");
				rawoutput("<input name='startmsg' value='{$buff['startmsg']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Round message:");
				rawoutput("</td><td>");
				rawoutput("<input name='roundmsg' value='{$buff['roundmsg']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Wearoff:");
				rawoutput("</td><td>");
				rawoutput("<input name='wearoff' value='{$buff['wearoff']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Effect fail message:");
				rawoutput("</td><td>");
				rawoutput("<input name='effectfailmsg' value='{$buff['effectfailmsg']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Effect no-effect message:");
				rawoutput("</td><td>");
				rawoutput("<input name='effectnodmgmsg' value='{$buff['effectnodmgmsg']}'>");
			rawoutput("</td></tr><tr><td>");
				output("Effect message:");
				rawoutput("</td><td>");
				rawoutput("<input name='effectmsg' value='{$buff['effectmsg']}'>");
			rawoutput("</td></tr><tr><td colspan=2><hr>");
			rawoutput("</td></tr><tr><td>");
				output("Makes invulnerable?:");
				rawoutput("</td><td>");
				rawoutput("<select name='invulnerable'>");
				rawoutput("<option value='1'");
				rawoutput($buff['invulnerable']?" selected":"");
				rawoutput(">$yes</option><option value='0'");
				rawoutput($buff['invulnerable']?"":" selected");
				rawoutput(">$no</option></select>");
			rawoutput("</td></tr><tr><td>");
				output("Allow in PvP (not the activation!)?:");
				rawoutput("</td><td>");
				rawoutput("<select name='allowinpvp'>");
				rawoutput("<option value='1'");
				rawoutput($buff['allowinpvp']?" selected":"");
				rawoutput(">$yes</option><option value='0'");
				rawoutput($buff['allowinpvp']?"":" selected");
				rawoutput(">$no</option></select>");
			rawoutput("</td></tr><tr><td>");
				output("Allow in train (not the activation!)?:");
				rawoutput("</td><td>");
				rawoutput("<select name='allowintrain'>");
				rawoutput("<option value='1'");
				rawoutput($buff['allowintrain']?" selected":"");
				rawoutput(">$yes</option><option value='0'");
				rawoutput($buff['allowintrain']?"":" selected");
				rawoutput(">$no</option></select>");
			rawoutput("</td></tr><tr><td>");
				output("Survives newday?:");
				rawoutput("</td><td>");
				rawoutput("<select name='survivenewday'>");
				rawoutput("<option value='1'");
				rawoutput($buff['survivenewday']?" selected":"");
				rawoutput(">$yes</option><option value='0'");
				rawoutput($buff['survivenewday']?"":" selected");
				rawoutput(">$no</option></select>");
			rawoutput("</td></tr><tr><td colspan=2><hr>");
			rawoutput("</td></tr><tr><td>");
				$create = translate_inline($id?"Update":"Create");
				rawoutput("<input type=submit value='$create'>");
				rawoutput("</td><td>");
				rawoutput("<input type=reset>");
			rawoutput("</td></tr></table>");
			rawoutput("</form>");
			break;
		case "newbuff2":
			$post = httpallpost();
			$id = httpget('id');
			if (!$id) {
				$sql = "INSERT INTO ".db_prefix("itembuffs")." (`lifetap`, `roundmsg`, `rounds`, `buffname`, `buffshortname`, `invulnerable`, `dmgmod`,	`badguydmgmod`,	`atkmod`, `badguyatkmod`, `defmod`,`badguydefmod`, `damageshield`, `regen`, `minioncount`, `maxbadguydamage`, `minbadguydamage`, `maxgoodguydamage`, `mingoodguydamage`, `startmsg`, `wearoff`, `effectfailmsg`, `effectnodmgmsg`, `effectmsg`, `allowinpvp`, `allowintrain`, `survivenewday`) VALUES ('{$post['lifetap']}','{$post['roundmsg']}', '{$post['rounds']}', '{$post['buffname']}', '{$post['buffshortname']}', '{$post['invulnerable']}', '{$post['dmgmod']}', '{$post['badguydmgmod']}', '{$post['atkmod']}', '{$post['badguyatkmod']}', '{$post['defmod']}', '{$post['badguydefmod']}', '{$post['damageshield']}', '{$post['regen']}', '{$post['minioncount']}', '{$post['maxbadguydamage']}', '{$post['minbadguydamage']}', '{$post['maxgoodguydamage']}', '{$post['mingoodguydamage']}','{$post['startmsg']}', '{$post['wearoff']}',  '{$post['effectfailmsg']}', '{$post['effectnodmgmsg']}', '{$post['effectmsg']}', '{$post['allowinpvp']}', '{$post['allowintrain']}', '{$post['survivenewday']}')";
				db_query($sql);
				output("'`^%s`0' inserted.", $post['buffname']);
			} else {
				$sql = "UPDATE ".db_prefix("itembuffs")." SET
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
						WHERE buffid = $id";
				db_query($sql);
				invalidatedatacache("inventory-buff-$id");
				output("'`^%s`0' updated.", $post['buffname']);
			}
			break;
		case "showbuffs":
			$sql = "SELECT buffid, buffname, buffshortname FROM ".db_prefix("itembuffs")." ORDER BY buffid ASC";
			$result = db_query($sql);
			$edit = translate_inline("Edit");
			$del = translate_inline("Delete");
			$conf = translate_inline("Do you really want to delete this buff?");
			for ($i=0;$i<db_num_rows($result);$i++) {
				$row=db_fetch_assoc($result);
				output_notl("`^%s `7- `&`i%s`i `7- [", $row['buffname'], $row['buffshortname']);
				rawoutput("<a href='runmodule.php?module=inventory&op=editor&op2=newbuff&id=".$row['buffid']."'>$edit</a> - <a href='runmodule.php?module=inventory&op=editor&op2=delbuff&id=".$row['buffid']."' onClick=\"return confirm('$conf');\">$del</a>");
				addnav("", "runmodule.php?module=inventory&op=editor&op2=newbuff&id=".$row['buffid']);
				addnav("", "runmodule.php?module=inventory&op=editor&op2=delbuff&id=".$row['buffid']);
				output_notl("]`0`n");
			}
			break;
		case "delbuff":
			$id = httpget('id');
			$sql = "DELETE FROM ".db_prefix("itembuffs")." WHERE buffid = $id LIMIT 1";
			$result = db_query($sql);
			if (db_affected_rows($result)) output("Buff succesfully deleted.`n`n");
			else output("While deleting this buffs an error occured. Probably someone else already deleted this buff.`n`n");
	}
	page_footer();
?>