<?php
function jail_getmoduleinfo()
{
	require("modules/jail/getmoduleinfo.php");
	return $info;
}
function jail_install()
{
	require_once("modules/jail/install.php");
	return true;
}
function jail_uninstall(){return true;}
function jail_dohook($hookname, $args)
{
	global $session;

//RPGee.com - Missing $evil variable in dohook
	$evil = get_module_pref('alignment', 'alignment');
//END RPGee.com

	require("modules/jail/dohook/$hookname.php");
	return $args;
}
function jail_runevent($type)
{
	global $session;
	$wanted = get_module_pref('wantedlevel');
	$evil = get_module_pref('alignment', 'alignment');
	if ($wanted > 0 and get_module_setting('usewanted') == 1)
	{
		output
		(
			"You encounter the sheriff who recognizes you from the wanted posters he was placing around the forest! 
			Looks like today is not you're lucky day. You're going to jail."
		);
		$user = $session['user']['name'];
		addnews("The sheriff arrested %s in the forest!", $user);
		set_module_pref('injail', 1);

//RPGee.com - set number of days if multiple days are enabled
		if (get_module_setting('moredays')) set_module_pref('daysin', get_module_setting('manydays'));
//END

		addnav("To jail", "runmodule.php?module=jail");
	}
	else if ($evil < get_module_setting("minevil") and get_module_setting('useevil') == 1)
	{
		output
		(
			"The sheriff can smell your evil from miles away, and has finally caught up to you.  
			Looks like today is not you're lucky day.  You're going to jail."
		);

//RPGee.com - set number of days if multiple days are enabled
		if (get_module_setting('moredays')) set_module_pref('daysin', get_module_setting('manydays'));
//END

		$user = $session['user']['name'];
		set_module_pref('injail', 1);
		addnews("The sheriff arrested %s in the forest!", $user);
		addnav("To jail", "runmodule.php?module=jail");
	}
	else output("You encounter the sheriff who is searching through the forest for evil-doers.  You smile as he passes you by.");
}
function injailnav()
{
	global $session;
	$barrister		= get_module_pref('barrister');
	$witness1		= get_module_pref('witness1');
	$witness2		= get_module_pref('witness2');
	$towncries		= get_module_pref('towncries');
	$injail		= get_module_pref('injail');
	$wenttocourt	= get_module_pref('wenttocourt');
	modulehook ('injail');
	if ($witness1 > 0 && $witness2 > 0 && $barrister > 0 && $wenttocourt == 0)
		addnav("Get a court Date", "runmodule.php?module=jail&op=courtdate"); 
	if ($towncries > 0) addnav("Use your town cry", "runmodule.php?module=jail&op=towncry"); 
	if ($injail == 0) addnav ("Take your things and go","runmodule.php?module=jail");
	addnav("Twiddle your thumbs", "runmodule.php?module=jail&op=twiddle"); 
	addnav("Go to Sleep", "runmodule.php?module=jail&op=sleep"); 
	addnav("Pay Bond", "runmodule.php?module=jail&op=paybond"); 
	addnav("Attempt suicide", "runmodule.php?module=jail&op=suicide"); 
	addnav("Ask for some soup - 1 gem", "runmodule.php?module=jail&op=soup"); 
	if ((get_module_setting('showforum')) && (is_module_active('forum')))
	{
		addnav("other");
		addnav("LotGD forum", "runmodule.php?module=forum");
	}
	if ($session['user']['superuser'] &~ SU_DOESNT_GIVE_GROTTO)
	{
		addnav("Superuser");
		addnav("Newday", "newday.php");
	}

//RPGee.com - added in note about how many more days until released
	if (get_module_setting('moredays')) output("`$`n`nYou have %s more days until you are released.`0`n`n", get_module_pref('daysin'));
//END RPGee.com

}
function jail_run()
{
	global $session;
	page_header("The Jail");
	$op	= httpget('op');

//RPGee.com - new multiple days wording and setting
	$daysin = get_module_pref('daysin');
//RPGee.com - added in check for more days setting to make sure characters get out of jail properly
	if ($daysin == 0 && get_module_setting('moredays')) set_module_pref('injail', 0);
//END RPGee.com

	$jaillocation	= translate_inline("`7The Jail");
	$sheriffname	= get_module_setting('sheriffname');
	$bardk		= get_module_setting('bardk');
	$turnwit		= get_module_setting('turnwit');
	$turnbar		= get_module_setting('turnbar');
	$minlvl		= get_module_setting('minlvl');
	$baillvl		= get_module_setting('baillvl');
	$baildk		= get_module_setting('baildk');
	if ($session['user']['location'] == $jaillocation) page_header(array("The Jail of %s", get_module_pref('playerloc')));
	else page_header(array("The Jail of %s", $session['user']['location']));
	if ($session['user']['location'] == $jaillocation)
	{
		$session['user']['location'] = get_module_pref('playerloc');
		set_module_pref('playerloc', "");
	}
	require_once("modules/jail/run/case_$op.php");
	page_footer();
}
?>