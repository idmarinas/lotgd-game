<?php
	require_once("lib/showform.php");
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Return to the Equipment Editor", "runmodule.php?module=mysticalshop&op=editor&what=view&cat=0");
	addnav("Admin Tools");
	addnav("Create New Buff",$from."op=editor&what=newbuff");
	addnav("Add Buff to Item",$from."op=editor&what=newitem");
	addnav("View List of Buffs",$from."op=editor&what=showbuffs");
	addnav("View List of Items with Buffs",$from."op=editor&what=showitems");
	addnav("Other");
	addnav("View Instructions", $from."op=editor&what=view");
		switch (httpget('what')){
			case "view":
				output("`@Instructions for use:`n`n");
				output("`21.) In order to give items buffs, you must first create one by clicking on `3\"Create New Buff\" `2in the menu.`n`n");
				output("`22.) After, click `3\"Add Buff to Item\" `2to view a drop-down list of items and buffs. Choose an item from the list, then a buff, and click `iAttach Buff`i. You can use this method to edit/remove a buff from an item as well.`n`n");
				output("`23.) To edit or delete a buff, click `3\"View List of Buffs\"`2.`n`n");
				output("`@Other Notes:`n`n");
				output("`2It is advised you give each item it's own unique buff, as they overlap each other. `n`nAlso, if you add a buff to an existing item a player owns, it will activate upon a newday.");
				output(" `2Alternatively, if you delete/change a buff from an existing item, the buff will be stripped come a newday.`n`n");
				break;
			case "newbuff":
				require_once("modules/mysticalshop_buffs/newbuff.php");
				break;
			case "newbuff2":
				$post = httpallpost();
				$id = httpget('id');
				$buffname = $post['buffname'];
				unset($post['buffname']);
				$post = serialize($post);
				
				if (!$id) {
					$sql = "INSERT INTO ".db_prefix("magicitembuffs")." (`buffid`,`buffname`,`itembuff`) VALUES ('$id', '$buffname', '$post')";
					debug($sql);
					db_query($sql);
					output("'`^%s`0' inserted.", $post['buffname']);
				} else {
					$sql = "UPDATE ".db_prefix("magicitembuffs")." SET
								buffname = '$buffname',
								itembuff = '$post'
							WHERE buffid = $id";
					db_query($sql);
					invalidatedatacache("magicitem-buff-$id");
					output("'`^%s`0' updated.", $post['buffname']);
				}
				break;
			case "showbuffs":
				$sql = "SELECT buffid, buffname FROM ".db_prefix("magicitembuffs")." ORDER BY buffid ASC";
				$result = db_query($sql);
				$edit = translate_inline("Edit");
				$del = translate_inline("Delete");
				$conf = translate_inline("Do you really want to delete this buff?");
				$name = translate_inline("Display Name");
				$buffname = translate_inline("Buff Name");
				$ops = translate_inline("Ops");
				rawoutput("<table border='0' cellpadding='3' width='75%'><tr><td><b>$ops</b></td><td><b>$name</b></td><td><b>$buffname</b></td></tr>");
				for ($i=0;$i<db_num_rows($result);$i++) {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
					$row=db_fetch_assoc($result);
					rawoutput("<td><a href='runmodule.php?module=mysticalshop_buffs&op=editor&what=newbuff&id=".$row['buffid']."'>[$edit]</a> <a href='runmodule.php?module=mysticalshop_buffs&op=editor&what=delbuff&id=".$row['buffid']."' onClick=\"return confirm('$conf');\">[$del]</a></td>");
					addnav("", "runmodule.php?module=mysticalshop_buffs&op=editor&what=newbuff&id=".$row['buffid']);
					addnav("", "runmodule.php?module=mysticalshop_buffs&op=editor&what=delbuff&id=".$row['buffid']);
					output("<td>`^%s</td><td>`3%s</td>", $row['buffname'],$row['buffname'],true);
					rawoutput("</tr>");
				}
				rawoutput("</table>");
				break;
			case "showitems":
				$sql = "SELECT name, buffid FROM ".db_prefix("magicitems")." WHERE buffid>0 ORDER BY buffid ASC";
				$result = db_query($sql);
				$name = translate_inline("Equipment Name");
				$buffname = translate_inline("Buff Name");
				rawoutput("<table border='0' cellpadding='3' width='75%'><tr><td><b>$name</b></td><td><b>$buffname</b></td></tr>");
				for ($i=0;$i<db_num_rows($result);$i++) {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
					$row=db_fetch_assoc($result);
					$buffid = $row['buffid'];
					$sqla = "SELECT buffid, buffname FROM ".db_prefix("magicitembuffs")."  WHERE buffid = $buffid ORDER BY buffid ASC";
					$resulta = db_query($sqla);
					$rowa = db_fetch_assoc($resulta);
					output("<td>%s.) `2%s</td><td>`3%s</td>",$i,$row['name'],$rowa['buffname'],true);
					rawoutput("</tr>");
				}
				rawoutput("</table>");
				break;
			case "delbuff":
				$id = httpget('id');
				$sql = "DELETE FROM ".db_prefix("magicitembuffs")." WHERE buffid = $id LIMIT 1";
				$result = db_query($sql);
				invalidatedatacache("magicitem-buff-$id");
				if (db_affected_rows($result)) output("`^Buff has been succesfully deleted.`n`n");
				else output("`3While deleting this buff, an error occured. Probably someone else already deleted this buff.`n`n");		  
				$sql = "UPDATE ".db_prefix("magicitems")." SET buffid = 0 WHERE buffid = '$id'";
				db_query($sql);
				break;
			case "newitem":
				require_once("modules/mysticalshop_buffs/newitem.php");
				break;
			case "newitem2":
				$id = httppost('id');
				$buffid = httppost('buffid');
				if ($buffid == 0 OR $buffid == "")$buffid = 0;
				else $buffid = httppost('buffid');
				$name = httppost('name');
				$sql = "UPDATE ".db_prefix("magicitems")." SET buffid = $buffid WHERE id = '$id'";
				db_query($sql);
				output("`^Done!");
				break;
		}
?>