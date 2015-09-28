<?php
$player 	= httpget('player');
$sql 		= "SELECT acctid, name,login,level FROM ".db_prefix("accounts")." WHERE acctid =".$player;
$result 	= db_query($sql) or die(db_error(LINK));
$row 		= db_fetch_assoc($result);
$playername = $row['name'];
$personid 	= $session['user']['acctid'];
if(httpget('action') == "yes")
{
	if(get_module_pref('barrister', $player) == $personid || get_module_pref('witness1', $player) == $personid || 
	 get_module_pref('witness2', $player) == $personid)
	{
		output
		(
			"You must really be good if you think you can run the whole show on your own! 
			You're already part of this case."
		);
		addnav("Back to jail", "runmodule.php?module=jail");
	} 
	elseif (get_module_pref('barrister', $player) > 0)
	{
		output("This person already has a barrister.");
		addnav("Back to jail", "runmodule.php?module=jail");
	}
	elseif ($session['user']['turns'] < $turnbar)
	{
		output("You do not have enough turns to take this case.");
		addnav("Back to jail", "runmodule.php?module=jail");
	}
	else
	{
		set_module_pref('barrister', $personid, $player);
		$session['user']['turns'] -= $turnbar;
		$newtitle = "Barrister";
		require_once("lib/names.php");
		$newname = change_player_title($newtitle);
		$session['user']['title'] = $newtitle;
		$session['user']['name'] = $newname;
		require_once("lib/systemmail.php");
		$repre = translate_inline("Representation");
		systemmail($player,"`^$repre`0","".$session['user']['name']." has agreed to take your case.",$session[user]['acctid']);
		addnews("".$session['user']['name']." will represent %s in court!", $playername);
		output("`n`n`7You decide to represent %s`7.", $playername);
		addnav("Back to jail", "runmodule.php?module=jail");
	} 
}
elseif (httpget('action') == "no")
{
	output
	(
		"You decide that it's not worth it to represent %s, so you thank %s`7 and leave the office.", 
		$playername, $sheriffname
	);
	addnav("Back to jail", "runmodule.php?module=jail");
}
else
{
	output
	(
		"`n`n%s`7 tells you that it will take %s turns to make the court date for %s.`n`n Would you like to 
		take the case?`n`n", $sheriffname, $turnbar, $playername
	);
	addnav("Back to jail", "runmodule.php?module=jail");
	output
	(
		"<a href=\"runmodule.php?module=jail&op=barrep&player=$player&action=yes\">`^Yes</a> `^/ 
		<a href=\"runmodule.php?module=jail&op=barrep&player=$player&action=no\">`^No</a>",true
	);
	addnav("", "runmodule.php?module=jail&op=barrep&player=$player&action=yes");
	addnav("", "runmodule.php?module=jail&op=barrep&player=$player&action=no");
} 
?>