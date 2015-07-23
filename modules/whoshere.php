<?php
/*
	Modified by MarcTheSlayer

	Knowing someone is in the same location and having no chat is pointless so I've removed certain areas.
	This module also relies on the position of the modulehook(). In the lodge and clanhalls 'whoshere' appears below the chat area.

	== 07/10/08 - v2.0a ==
	+ Added shades as there was  commentary.
	- Removed forest, graveyard, gypsy, inn, and stable hooks.
	- Removed a bunch of code that I don't think was really necessary.
	+ Everyone will now show up in the gardens no matter what village they entered from.
	== 12/10/08 - v2.0b ==
	+ In clanhall, only people in your clan get listed.
	== 25/02/09 - v2.0c ==
	+ Added setting to display names, or a popup window with names of the players in the same location.
*/
function whoshere_getmoduleinfo()
{
	$info = array(
		"name"=>"Who's Here",
		"description"=>"Above each commentary area is a list of players that are also there.",
		"version"=>"2.0c",
		"author"=>"`#Lonny Luberts, updated by sixf00t4, modified by `@MarcTheSlayer.",
		"category"=>"General",
		"download"=>"http://dragonprime.net/index.php?topic=9883.0",
		"allowanonymous"=>TRUE,
		"override_forced_nav"=>TRUE,
		"settings"=>array(
			"Who's Here Module Settings,title",
			"shownames"=>"Show names instead of a popup link?,bool|1",
			"Active in the following locations,note",
			"clan"=>"Clans,bool|1",
			"gardens"=>"Gardens,bool|1",
			"lodge"=>"Lodge,bool|1",
			"rock"=>"Rock,bool|1",
			"shades"=>"Shades,bool|1",
			"superuser"=>"Superuser Grotto,bool|1",
			"village"=>"Village (all villages),bool|1"
		),
		"prefs"=>array(
			"Who's Here User Preferences,title",
			"playerloc"=>"Player Location,text|",
			"playerloc2"=>"Player Location (everyhit),text|"
		)
	);
	return $info;
}

function whoshere_install()
{
	output("`c`b`Q%s 'whoshere' Module.`b`n`c", translate_inline(is_module_active('whoshere')?'Updating':'Installing'));
	module_addhook('everyheader');
	module_addhook('newday');
	module_addhook('changesetting');
	module_addhook('village');
	module_addhook('clanhall');
	module_addhook('gardens');
	module_addhook('shades');
	module_addhook('lodge');
	module_addhook('rock');
	module_addhook('header-superuser');
	return TRUE;
}

function whoshere_uninstall()
{
	output("`n`c`b`Q'whoshere' Module Uninstalled`0`b`c");
	return TRUE;
}

function whoshere_dohook($hookname,$args)
{
	switch( $hookname )
	{
		case 'everyheader':
			global $SCRIPT_NAME;
			set_module_setting('playerloc2',$SCRIPT_NAME);
		break;

		case 'newday':
			$sql = "SELECT acctid FROM " . db_prefix('accounts') . " WHERE loggedin = 1 and laston < '" . date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds")) . "'";
			$result = db_query($sql);
			while( $row = db_fetch_assoc($result) )
			{
				db_query("UPDATE " . db_prefix('accounts') . " SET loggedin = 0 WHERE acctid = '" . $row['acctid'] . "'");
			}
		break;

		case 'changesetting':
			if( get_module_setting('shownames') )
			{
				module_drophook('everyheader');
			}
			else
			{
				module_addhook('everyheader');
			}
		break;

		default:
			global $SCRIPT_NAME;
			set_module_pref('playerloc',$SCRIPT_NAME);
			$yn = str_replace('.php','',$SCRIPT_NAME);
			if( get_module_setting($yn) )
			{
				if( get_module_setting('shownames') )
				{
						global $session, $_SERVER;
						include('modules/whoshere/whoshere_names.php');
				}
				else
				{
					output("`n`@Who Else is here:`n");
					$text_link = appoencode(translate_inline('`3Click For Names`0'));
					rawoutput("<a href=\"runmodule.php?module=whoshere\" onClick=\"window.open('runmodule.php?module=whoshere','whoshere','scrollbars=yes,resizable=yes,width=500,height=250').focus(); return false;\" target=\"_blank\">$text_link</a>");
					addnav('',"runmodule.php?module=whoshere");
					output("`n`2-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`n");
				}
			}
		break;
	}

	return $args;
}

function whoshere_run()
{
	global $session, $_SERVER;

	if( $session['user']['loggedin'] )
	{
		popup_header("Who's Here?");
		$SCRIPT_NAME = get_module_pref('playerloc');
		if( $SCRIPT_NAME == get_module_setting('playerloc2') )
		{
			$yn = str_replace('.php','',$SCRIPT_NAME);
			if( get_module_setting($yn) )
			{
				include('modules/whoshere/whoshere_names.php');
			}
		}
		else
		{
			output('`2This location does not have commentary.');
		}
		popup_footer();
	}
	else
	{
		header("Location: home.php");
		exit();
	}
}
?>