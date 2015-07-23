<?php
/**
	Rewritten by MarcTheSlayer :)

	09/02/2009 - v2.0.0
	+ Number of player warnings listed on bio page along with reasons.
	+ Show previous warnings with form to give another. Can't warn player if they're currently banned.
	+ If 'jail' module is installed, you can jail player along with the warning.
	+ Player warnings are stored in allprefs.
	+ With allprefseditor, warnings can be edited or deleted.

	17/02/2009 - v2.0.1
	+ Corrected some spelling errors.
	+ Made reasons text in bio red in colour.
	+ If 'mutemod' is installed, you can mute player for 1 day along with the warning.
	+ Missed 2 translate_inline()s.
	+ Can now edit/change the main part of the YoM message that gets sent.
*/
function warnlvl_getmoduleinfo()
{
		$info = array(
		"name"=>"Warning Level and Bans",
		"author"=>"`&S`7ephiroth`0, rewritten by `@MarcTheSlayer",
		"category"=>"Administrative",
		"version"=>"2.0.1",
		"download"=>"http://dragonprime.net/index.php?topic=9881.0",
		"requires"=>array(
			"allprefseditor"=>"DaveS"
		),
		"settings"=>array(
			"Warning Level and Ban Settings,title",
			"warns"=>"How many warnings does player get before being banned?,range,1,25,1|7",
			"bans"=>"In real days how long is ban after being warned?,range,1,50,1|3",
			"reasons"=>"Behaviour reasons.,textarearesizeable,25|Rude\r\nRacist\r\nSexist\r\nSwearing\r\nDisruptive\r\nCheating\r\nCode Exploiter\r\nHarasser\r\nPervert\r\nAggressive\r\nOther",
			"days"=>"How many days before automatically removing a warning?,range,0,30,1|0",
			"NOTE: Set setting to 0 to never automatically remove a warning.,note",
			"show"=>"Show warning level in bio to everyone not just moderators?,bool|1"
		),
		"prefs"=>array(
		    "Warning Level and Ban User Preferences,title",
		    "NOTE: Contains how many warnings who warned and comments. Do not edit here.,note",
			"allprefs"=>"Allprefs data.,textarea,20|"
		)
	);
	return $info;
}

function warnlvl_install()
{
	output("`c`b`Q%s 'warnlvl' Module.`b`n`c", translate_inline(is_module_active('warnlvl')?'Updating':'Installing'));
	module_addhook('bioinfo');
	module_addhook('allprefs');
	module_addhook('allprefnavs');
	return TRUE;
}

function warnlvl_uninstall()
{
	output("`n`c`b`Q'warnlvl' Module Uninstalled`0`b`c");
	return TRUE;
}

function warnlvl_dohook($hookname, $args)
{
	global $session;

	$id = $args['acctid'];

	require("modules/warnlvl/dohook/$hookname.php");

	return $args;
}

function warnlvl_run()
{
	global $session;

	page_header('Warning Level and Ban');

	$op = httpget('op');

	require_once("modules/warnlvl/run/case_$op.php");

	page_footer();
}
?>