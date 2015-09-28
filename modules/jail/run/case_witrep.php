<?php
$player	= httpget('player');
$sql 		= "SELECT name,login,level FROM ".db_prefix("accounts")." WHERE acctid =".$player;
$result	= db_query($sql) or die(db_error(LINK));
$row 		= db_fetch_assoc($result);
$playername	= $row['name'];
$personid	= $session['user']['acctid'];
if(httpget('action') == "yes")
{
	if(get_module_pref('barrister', $player) == $personid || get_module_pref('witness1', $player) == $personid || 
		get_module_pref('witness2', $player) == $personid)
	{
		output
		(
			"You must really be good if you think you can run the whole show on your own! You're already 
			part of this case."
		);
		addnav("Back to jail", "runmodule.php?module=jail");
	} 
	elseif ($session['user']['level'] < $minlvl)
	{
		output("You will need some more experience in this village before your opinion matters in a court trial.");
		addnav("Back to jail", "runmodule.php?module=jail");
	}
	elseif (get_module_pref('witness2', $player) > 0)
	{
	output("This person already has enough witnesses.");
	addnav("Back to jail", "runmodule.php?module=jail");
	}
	elseif ($session['user']['turns'] < $turnwit)
	{
		output("You do not have enough turns to be a witness.");
		addnav("Back to jail", "runmodule.php?module=jail");
	}
	elseif (get_module_pref('witness1',$player) > 0)
	{
		require_once("lib/systemmail.php");
		set_module_pref('witness2', $personid, $player);
		$session['user']['turns'] -= $turnwit;
		systemmail($player,"`^Witness 2 spot filled`0","".$session['user']['name']." has agreed to be a witness in your court case.",$session['user']['acctid']);
		addnews("".$session['user']['name']." will be a witness for %s in court!", $playername);
		output("`n`n`7You decide to be the second witness for %s.", $playername);
		addnav("Back to jail", "runmodule.php?module=jail");
	}
	else
	{
		require_once("lib/systemmail.php");
		set_module_pref('witness1', $personid, $player);
		$session['user']['turns'] -= $turnwit;
		systemmail($player,"`^Witness 1 spot filled`0","".$session['user']['name']." has agreed to be a witness in your court case.",$session[user]['acctid']);
		addnews("".$session['user']['name']." will be a witness for %s in court!", $playername);
		output("`n`n`7You decide to be a witness for %s.", $playername);
		addnav("Back to jail", "runmodule.php?module=jail");
	} 
}
elseif (httpget('action') == "no")
{
	output
	(
		"`n`n`7 You decide that it's not worth it to be a witness for %s. So you thank %s `7and leave the office.",
		$playername, $sheriffname
	);
	addnav("Back to jail", "runmodule.php?module=jail");
}
else
{
	output
	(
		"`n`n%s`7 tells you that it will take %s turns to make the court date for %s.
		`n`nWould you like to be scheduled to appear as a witness?`n`n", $sheriffname, $turnwit, $playername
	);
	output
	(
		"<a href=\"runmodule.php?module=jail&op=witrep&player=$player&action=yes\">`^Yes
		</a> `^/ <a href=\"runmodule.php?module=jail&op=witrep&player=$player&action=no\">`^No</a>", true
	);
	addnav("Back to jail", "runmodule.php?module=jail");
	addnav("", "runmodule.php?module=jail&op=witrep&player=$player&action=yes");
	addnav("", "runmodule.php?module=jail&op=witrep&player=$player&action=no");
}
?>