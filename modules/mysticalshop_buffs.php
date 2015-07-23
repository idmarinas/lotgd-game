<?php
/**************
Name: Equipment Buffs, for the Equipment Shop
Author: Eth - ethstavern(at)gmail(dot)com 
Version: 1.3.1
Re-Release Date: 01-25-2006
About: An addon for the Equipment Shop that lets you
	   add buffs to existing items. Could be *very*
	   unbalancing. Use at your own risk.
Notes: Inspired by XChrisX's Inventory mod.
	   pieced together from mounts.php and a few snippets 
	   from XChrisX's Inventory System.    
Translation compatible. Mostly.
*****************/
require_once("common.php");
require_once("lib/http.php");
require_once("lib/showform.php");
require_once("lib/buffs.php");
require_once("modules/mysticalshop/mysticalshop_buffs_func.php");
function mysticalshop_buffs_getmoduleinfo(){
    $info = array(
        "name"=>"Equipment Buffs",
        "version"=>"1.3.1",
        "author"=>"Eth",
        "category"=>"Equipment Shop",
		"download"=>"http://dragonprime.net/users/Eth/mysticalshop_buffs.zip",
		"vertxtloc"=>"http://dragonprime.net/users/Eth/",
		"requires"=>array(
			"mysticalshop"=>"2.8|By Eth, available at Dragonprime.net",
		),
    );
    return $info;
}

function mysticalshop_buffs_install(){
	global $session;
	$equipmentbuffs = db_prefix("magicitembuffs");
	$equipment = db_prefix("magicitems");
	$new_items = array('buffid'=> array('name'=>'buffid', 'type'=>'tinyint unsigned','null'=>'0'));
	$buff_table = array(
		'buffid'=> array('name'=>'buffid', 'type'=>'tinyint unsigned','null'=>'0', 'extra'=>'auto_increment'),
		'buffname'=> array('name'=>'buffname', 'type'=>'varchar(255)','null'=>'0'),
		'itembuff'=> array('name'=>'itembuff', 'type'=>'text', 'null'=>'1'),
		'key-PRIMARY' => array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'buffid'));
	require_once("lib/tabledescriptor.php");
	synctable($equipment, $new_items, true);
	synctable($equipmentbuffs, $buff_table, true);
	module_addhook("mysticalshop-editor");
	module_addhook("mysticalshop-buy");
	module_addhook("mysticalshop-sell-after");
	module_addhook("newday");
    return true;
}
function mysticalshop_buffs_uninstall(){
	$sql = "DROP TABLE IF EXISTS " . db_prefix("magicitembuffs");
	db_query($sql);
    return true;
}
function mysticalshop_buffs_dohook($hookname,$args){
    global $session;
    $from = "runmodule.php?module=mysticalshop_buffs&";
	switch($hookname){
	case "newday":
	//first, lets' remove any old buffs that might be lingering around
	//might not be needed, really - but you can't be too careful.
	mysticalshop_stripbuff();
	//now, let's add/refresh the new ones
	mysticalshop_addbuff();
	break;
	case "mysticalshop-buy":
	mysticalshop_addbuff();
	break;
	case "mysticalshop-sell-after":
	mysticalshop_stripbuff();
	break;
    case "mysticalshop-editor":
    	addnav("Admin Tools");
		addnav("`^Go to Buff Manager",$from."op=editor&what=view");
		break;
    }    
	return $args;
}
function mysticalshop_buffs_runevent($type) {}

function mysticalshop_buffs_run(){
	global $session;
	$title = translate_inline("Equipment Buffs Manager");
	page_header($title);
	$op = httpget('op');
	$id=httpget("id");
	$from = "runmodule.php?module=mysticalshop_buffs&";
	if ($op == "editor"){
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
			mysticalshop_buffs_form(array());
			break;
			case "save":
			$buff = array();
			$item = httppost('magicitembuffs');
			if ($item) {
				reset($item['itembuff']);
				while (list($key,$val)=each($item['itembuff'])){
					if ($val>""){
						$buff[$key]=stripslashes($val);
					}
				}
				$buff['schema']="magicitembuffs";
				httppostset('magicitembuffs', $buff, 'itembuff');
				list($sql, $keys, $vals) = postparse(false, 'magicitembuffs');
				if ($id>""){
					$sql="UPDATE " . db_prefix("magicitembuffs") ." SET $sql WHERE buffid='$id'";
				}else{
					$sql="INSERT INTO " . db_prefix("magicitembuffs") ." ($keys) VALUES ($vals)";
				}
				db_query($sql);
				if (db_affected_rows()>0){
					output("`^Your buff has been saved!`0`n");
				}else{
					output("`^Your buff was `\$not`^ saved: `\$%s`0`n", $sql);
				}
			}
			break;
			case "edit":
			$id = httpget("id");
			$sql = "SELECT * FROM ".db_prefix("magicitembuffs")." WHERE buffid = $id";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$row['itembuff']=unserialize($row['itembuff']);
			mysticalshop_buffs_form($row);
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
			rawoutput("<table border=0 cellpadding=3 width=75%><tr><td><b>$ops</b></td><td><b>$name</b></td><td><b>$buffname</b></td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++) {
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
				$row=db_fetch_assoc($result);
				rawoutput("<td><a href='runmodule.php?module=mysticalshop_buffs&op=editor&what=edit&id=".$row['buffid']."'>[$edit]</a> <a href='runmodule.php?module=mysticalshop_buffs&op=editor&what=delbuff&id=".$row['buffid']."' onClick=\"return confirm('$conf');\">[$del]</a></td>");
				addnav("", "runmodule.php?module=mysticalshop_buffs&op=editor&what=edit&id=".$row['buffid']);
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
			rawoutput("<table border=0 cellpadding=3 width=75%><tr><td><b>$name</b></td><td><b>$buffname</b></td></tr>");
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
			if (db_affected_rows($result)) output("`^Buff has been succesfully deleted.`n`n");
			else output("`3While deleting this buff, an error occured. Probably someone else already deleted this buff.`n`n");		
			break;
			case "newitem":
			$id=httpget("id");
			$yes = translate_inline("Yes");
			$no = translate_inline("No");
			rawoutput("<form action='runmodule.php?module=mysticalshop_buffs&op=editor&what=newitem2' method='post'>");
			addnav("", "runmodule.php?module=mysticalshop_buffs&op=editor&what=newitem2");
			rawoutput("<table border=0 cellpadding=1 cellspacing=5 cols=2 width=100%>");
			rawoutput("<tr><td width=25%>");
			output("List of Equipment:");
			rawoutput("</td><td>");
			$sql = "SELECT id, name  FROM ".db_prefix("magicitems");
			$result = db_query($sql);
			rawoutput("<select name='id'>");
			rawoutput("<option value=0");
			//rawoutput($item['buff']==0?" selected":"");
			rawoutput(">- none -</option>");
			for($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				rawoutput("<option value='{$row['id']}'");
			//	rawoutput($item['id']==$row['id']?" selected":"");
				rawoutput(">{$row['name']}</option>");
			}
			rawoutput("</select>");
			rawoutput("</td></tr><tr><td>");
			output("List of Buffs:");
			rawoutput("</td><td>");
			$sql1 = "SELECT buffid, buffname FROM ".db_prefix("magicitembuffs");
			$result1 = db_query($sql1);
			rawoutput("<select name='buffid'>");
			rawoutput("<option value=0");
			//rawoutput($item['buff']==0?" selected":"");
			rawoutput(">- none -</option>");
			for($i=0;$i<db_num_rows($result1);$i++){
				$buff1 = db_fetch_assoc($result1);
				rawoutput("<option value='{$buff1['buffid']}'");
		//		rawoutput($item['buffid']==$buff1['buffid']?" selected":"");
				rawoutput(">{$buff1['buffname']}</option>");
			}
			rawoutput("</select>");
			rawoutput("</td></tr><tr><td>");
			$create = translate_inline("Attach Buff");
			rawoutput("<input type=submit value='$create'>");
			rawoutput("</td><td>");
			rawoutput("<input type=reset>");
			rawoutput("</td></tr><tr><td colspan=5>");
			output("`2To remove a buff from an item, choose `3-none- `2from the buff list.");
			rawoutput("</td></tr></table>");
			rawoutput("</form>");
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
	addnav("Admin Tools");
	addnav("Create New Buff",$from."op=editor&what=newbuff");
	addnav("Add Buff to Item",$from."op=editor&what=newitem");
	addnav("View List of Buffs",$from."op=editor&what=showbuffs");
	addnav("View List of Items with Buffs",$from."op=editor&what=showitems");
	addnav("Other");
	addnav("View Instructions", $from."op=editor&what=view");
	addnav("Back to Equipment Editor", "runmodule.php?module=mysticalshop&op=editor&what=view&cat=0");
	}
	page_footer();
}
?>