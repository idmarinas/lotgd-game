<?php
$player	= httpget('player');
$sql		= "SELECT name,level,dragonkills FROM ".db_prefix("accounts")." WHERE acctid =".$player;
$result	= db_query($sql) or die(db_error(LINK));
$row		= db_fetch_assoc($result);
$playername	= $row['name'];
$baillvl	= get_module_setting('baillvl');
$baildk	= get_module_setting('baildk');
$cost		= ($row['level'] * $baillvl);

//RPGee.com - changed it from multiplying (dk*$baildk) to only adding it
//if ($row['dragonkills'] > 0) $cost = $cost * ($row['dragonkills']*$baildk); <-- ORIGINAL LINE

if ($row['dragonkills'] > 0) $cost = $cost + ($row['dragonkills']*$baildk);
//END RPGee.com

if (httpget('action') == "yes")
{
	if ($session['user']['gold'] >= $cost)
	{
		require_once("lib/systemmail.php");
		output
		(
			"`n`n`7You decide to help ".$row['name']."`7 get out of jail. So you hand over the `^%s gold`7 and %s yells to the 
			back for the guard to bring up ".$row['name']."", $cost, $sheriffname
		);
		addnav("Back to jail", "runmodule.php?module=jail");
		set_module_pref('injail', 0, 'jail',$player);

//RPGee.com - was the line below supposed to reduce the wanted level by 1?
//		set_module_pref('wantedlevel',-1,$player); <-- ORIGINAL LINE
		if (get_module_pref('wantedlevel') > 0) increment_module_pref('wantedlevel', -1, 'jail', $player);
//END RPGee.com

		$align	= get_module_pref('alignment', 'alignment', $player);
		$align	= ceil($align - $align * (get_module_setting('bailevil')) * .01);

//RPGEE.com - Was this supposed to update the alignment user pref?
//		set_module_pref('alignment', $align, $player); <-- ORIGINAL LINE
		set_module_pref('alignment', $align, 'alignment', $player);
		set_module_pref('daysin', 0, 'jail', $player); //RESET DAYS LEFT TO BE RELEASED
//END RPGee.com

		$session['user']['gold'] = $session['user']['gold'] - $cost;
		systemmail($player,"`^lucky you!`0","".$session['user']['name']." has bailed you out of jail!",$player);
		addnews("%s has bailed %s out of jail!", $session['user']['name'], $playername);
	}
	else
	{
		addnav("Back to jail", "runmodule.php?module=jail");
		output
		(
			"`n`n`7You decide to help %s, but %s tells you that you don't have enough money. %s will have to rot 
			in their cell until later.", $playername, $sheriffname, $playername
		);
	} 
}
elseif (httpget('action') == "no")
{
	output
	(
		"`7You decide that it's not worth it to get %s out of jail, so you thank %s and leave the office.", 
		 $playername, $sheriffname
	);
	addnav("Back to jail", "runmodule.php?module=jail");
}
else
{
	output
	(
		"%s`7 tells you that it will take about `^%s gold`7 to get %s out of jail.`n`n
		Would you like to bailout %s?`n`n", $sheriffname, $cost, $playername, $playername
	);
	addnav("Back to jail", "runmodule.php?module=jail");
	$yes	= translate_inline("Yes");
	$no	= translate_inline("No");
	output
	(
		"<a href=\"runmodule.php?module=jail&op=bailout&player=$player&action=yes\">`^$yes</a> `^/ 
		<a href=\"runmodule.php?module=jail&op=bailout&player=$player&action=no\">`^$no</a>", true
	);
	addnav("", "runmodule.php?module=jail&op=bailout&player=$player&action=yes");
	addnav("", "runmodule.php?module=jail&op=bailout&player=$player&action=no");
} 
?>