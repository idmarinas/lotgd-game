<?php
/*
Module Name:  Jail Tease
Category:  Forest Specials
Worktitle:  jailinator
Author:  DaveS  with the search engine adapted from Darkhorse.php by Eric Stevens

Description:
A forest special that threatens to have the player choose someone to jail.

A retired sheriff offers the player the opportunity to jail a player for a day.  However, after picking the
player to be jailed, the sheriff informs the player that the power to jail people is no longer
available.  Instead, the attempt to jail a player is announced in the news and the player loses some
alignment.

If the player walks away, they gain some alignment.

I wrote this module as a tip of my hat to NikSolo for all his help and threats to get me jailed.  It is
intentionally designed to cause a little trouble.

v3.0 Cleaned up code a little and added vertxtloc
*/

function jailinator_getmoduleinfo(){
	$info = array(
		"name"=>"Jail Tease",
		"version"=>"3.0",
		"author"=>"DaveS",
		"category"=>"Forest Specials",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=177",
		"vertxtloc"=>"",
		"settings"=>array(
			"staffname"=>"Name of Staff encountered,text|Sheriff Matlock",
			"alignloss"=>"Alignment points lost for attempting a jail, int|5",
			"aligngain"=>"Alignment points gained for not jailing, int|2",
		),
		"prefs"=>array(
		"Jail Tease - Preferences,title",
		"jailtried"=>"Had a chance to jail someone this newday?,bool|0",
		),
	);
	return $info;
}
function jailinator_chance() {
	global $session;
	if (get_module_pref('jailtried','jailinator',$session['user']['acctid'])==1) return 0;
	return 25;
}
function jailinator_install(){
	module_addeventhook("forest","require_once(\"modules/jailinator.php\"); 
	return jailinator_chance();");
	module_addhook("newday");
	return true;
}
function jailinator_uninstall(){
	return true;
}
function jailinator_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "newday":
			set_module_pref("jailtried",0);
		break;
	}
	return $args;
}
function jailinator_runevent($type,$link) {
	global $session;
	$from = $link;
	$session['user']['specialinc']="module:jailinator";
	$op = httpget('op');
	if ($op==""){
		set_module_pref("jailtried",1);
		$staffname= get_module_setting("staffname");
		output("`n`@As you search for more animals to slay, you find yourself suddenly face to face with
			`\$%s`@!!  You feel a huge amount of reverence and bend a knee before such a great warrior.`n`n",
			$staffname);
		output("`@Then the amazing `\$%s`@ speaks to you...`n`n",$staffname);
		output("`7'Although I retired from my work long ago, I still get a happy little twinge when I
			think of all the people I've jailed over the many years.  In fact, I sometimes have an urge
			to do some gratuitous jailing.  However, I don't know who to jail.  If you would like, just
			tell me the name of someone, and I will jail them for a day.'`n`n");
		output("`@Suddenly, you think of all the people that deserve a 'forced vacation'.  
			You have a choice to make, and `\$%s `@ awaits your answer.",$staffname);
		addnav("What do you do?");
		addnav("Leave","forest.php?op=leavestaff");
		addnav("Yes! I want to jail!","forest.php?op=enemies");
	}else if($op=="enemies"){
		$who = httpget('who');
		if ($who==""){
			output("`n`7'Who do you want to jail?'`n");
			$subop = httpget('subop');
			if ($subop!="search"){
				$search = translate_inline("Search");
				rawoutput("<form action='".$from."op=enemies&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
				addnav("",$from."op=enemies&subop=search");
				addnav("Leave","forest.php?op=leavestaff");
				rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
			}else{
				addnav("Search Again",$from."op=enemies");
				$search = "%";
				$name = httppost('name');
				for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			$sql = "SELECT name,alive,location,sex,level,laston,loggedin,login FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`n`7No.  That's too many names to pick from.  I'll let you choose from the first couple...`n");
				$max = 100;
			}
			$n = translate_inline("Name");
			$lev = translate_inline("Level");
			rawoutput("<table border=0 cellpadding=0><tr><td>$n</td><td>$lev</td></tr>");
			for ($i=0;$i<$max;$i++){
				$row = db_fetch_assoc($result);
				rawoutput("<tr><td><a href='".$from."op=enemies&who=".rawurlencode($row['login'])."'>");
				output_notl("%s", $row['name']);
				rawoutput("</a></td><td>{$row['level']}</td></tr>");
				addnav("",$from."op=enemies&who=".rawurlencode($row['login']));
			}
			rawoutput("</table>");
			}
		}else{
			$sql = "SELECT name,alive,location,maxhitpoints,gold,sex,level,weapon,armor,attack,defense FROM " . db_prefix("accounts") . " WHERE login='$who'";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				$row = db_fetch_assoc($result);
				$name = $row['name'];
				$staffname= get_module_setting("staffname");
				output("`n`n`7'So you have chosen to jail`7 %s`7.'`n`n", $name);
				output("'Well, I have some unfortunate news to give you.  I am retired, and I no longer have the ability
					to jail people.  However, I do have the ability to let everyone know that you tried to get
					`7%s `7jailed.'`n`n",$name);
				output("`@A chill runs through your body as you realize that `\$%s `@has reported your evil deed in the news.`n`n",$staffname);
				output("You hang your head in shame and skulk back to the forest.`n`n");
				$align = get_module_setting("alignloss");
				$session['user']['specialinc']="";
				set_module_pref("alignment",get_module_pref("alignment","alignment")-$align,"alignment");
				addnews("". ($session['user']['name']) . " `^tried to get `7%s `^jailed for a day.  Isn't that horrible??",$name);
			}else{
				output("'`7Heh...  I don't know anyone named that.'");
			}
		}
	}elseif ($op=="leavestaff") {
		global $session;
		$staffname= get_module_setting("staffname");
		output("`n`@As you leave, you contemplate whether you will ever retire like `\$%s`@ did.  But not today!  You've got a dragon
			to find.`n`n  You feel like a better person for resisting the temptation to jail someone.",$staffname);
		$session['user']['specialinc']="";
		$align = get_module_setting("aligngain");
		set_module_pref("alignment",get_module_pref("alignment","alignment")+$align,"alignment");
	}
}
?>