<?

$minevil = get_module_setting('minevil');
output("`2You walk over to %s's desk and try to start up a conversation.`n`n", $sheriffname);
if (get_module_pref('wantedlevel') > 0 and get_module_setting('usewanted') == 1)
{
	output
	(
		"`2After a few moments %s recognizes you from a wanted poster. He draws his club and starts to walk toward you.`n`n
		What do you do?", $sheriffname
	);
	addnav("Sheriff");
	addnav("Flee", "runmodule.php?module=jail&op=flee");
	addnav("Give up", "runmodule.php?module=jail&op=giveup");
} 
elseif (get_module_pref('alignment', 'alignment') < $minevil && get_module_setting('useevil') == 1)
{
	output
	(
		"`2After a few moments %s recognizes you from complaints in the village.`n
		He draws his sword and starts to walk toward you.`n`nWhat do you do?", $sheriffname
	);
	addnav("Sheriff");
	addnav("Flee", "runmodule.php?module=jail&op=flee");
	addnav("Give up", "runmodule.php?module=jail&op=giveup");
}

else
{
	modulehook ("sheriff-jail");
	output
	(
		"The sheriff looks busy studying some new wanted posters he just got in. He looks up for a moment, smiles, 
		and goes back to his work."
	);
	addnav("Bail out a friend", "runmodule.php?module=jail&op=bailafriend");
	
//RPGEE.com - added nav back to jail for convenience
	addnav("Back to jail", "runmodule.php?module=jail");
//END RPGEE.com

	addnav("Return to Village", "village.php");
}
?>