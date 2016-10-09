<?php
/**
 * Library (supporting) functions for page output
 *		addnews ready
 *		translator ready
 *		mail ready
 *
 * @author core_module
 * @author rewritten + adapted by IDMarinas
 * @package defaultPackage
 */

$nopopups = [];
$runheaders = [];
$html = [];

/**
 * Starts page output.  Inits the template and translator modules.
 *
 * @param array|string $title
 * Hooks provided:
 *		everyheader
 *		header-{scriptname}
 */
function page_header()
{
	global $html, $SCRIPT_NAME, $session, $template, $runheaders, $nopopups;

	$nopopups['login.php'] = 1;
	$nopopups['motd.php'] = 1;
	$nopopups['index.php'] = 1;
	$nopopups['create.php'] = 1;
	$nopopups['about.php'] = 1;
	$nopopups['mail.php'] = 1;

	//in case this didn't already get called (such as on a database error)
	translator_setup();

	$script = substr($SCRIPT_NAME, 0, strrpos($SCRIPT_NAME, '.'));
	if ($script)
	{
		if (! array_key_exists($script, $runheaders)) $runheaders[$script] = false;

		if (! $runheaders[$script])
		{
			modulehook('everyheader', ['script' => $script] );
            if ($session['user']['loggedin'])
				modulehook('everyheader-loggedin', ['script' => $script] );

			$runheaders[$script] = true;
			modulehook("header-$script");
		}
	}

	$arguments = func_get_args();
	if (! $arguments || count($arguments) == 0) $arguments = ['Legend of the Green Dragon'];

	$title = call_user_func_array('sprintf_translate', $arguments);
	$title = sanitize(holidayize($title, 'title'));
	calculate_buff_fields();

	//-- Add to html
	$html['title'] = $title . tlbutton_pop();

	if (getsetting('debug', 0)) $session['debugstart'] = microtime();
}

/**
 * Returns an output formatted popup link based on JavaScript
 *
 * @param string $page The URL to open
 * @param string $size The size of the popup window (Default: 728x400)
 * @return string
 */
function popup($page, $size = '728x400')
{
	// user prefs
	global $session;

	if ($size === '728x400' && $session['loggedin'])
	{
		if (!isset($session['user']['prefs'])) $usersize = '728x400';
		else
		{
			$usersize = &$session['user']['prefs']['popupsize'];
			if (! $usersize) $usersize = '728x400';
		}
		$s=explode('x', $usersize);
		$s[0] = (int) max(50, $s[0]);
		$s[1] = (int) max(50, $s[1]);
	}
	else $s = explode("x",$size);

	//user prefs
	return "window.open('$page','".preg_replace("([^[:alnum:]])","",$page)."','scrollbars=yes,resizable=yes,width={$s[0]},height={$s[1]}').focus()";
}

/**
 * Brings all the output elements together and terminates the rendering of the page.  Saves the current user info and updates the rendering statistics
 * Hooks provided:
 *	footer-{$script name}
 *	everyfooter
 *
 */
function page_footer($saveuser = true)
{
	global $output, $lotgd_tpl, $html, $nav, $session, $REMOTE_ADDR, $REQUEST_URI, $pagestarttime, $quickkeys, $y2, $z2, $logd_version,$copyright, $SCRIPT_NAME, $nopopups, $dbinfo;

	$z = $y2^$z2;

	//add XAJAX mail stuff
	if ($session['user']['prefs']['ajax'])
	{
		require 'mailinfo_common.php';
		$xajax->printJavascript('lib/xajax');
		addnav('', 'mailinfo_server.php');
	}
	//END XAJAX

	//page footer module hooks
	$script = substr($SCRIPT_NAME, 0, strpos($SCRIPT_NAME, '.'));
	$replacementbits = [];
	$replacementbits = modulehook("footer-$script", $replacementbits);
	if ($script == 'runmodule' && (($module = httpget('module'))) > '')
	{
		// This modulehook allows you to hook directly into any module without
		// the need to hook into footer-runmodule and then checking for the
		// required module.
		modulehook("footer-$module", $replacementbits);
	}
	// Pass the script file down into the footer so we can do something if
	// we need to on certain pages (much like we do on the header.
	// Problem is 'script' is a valid replacement token, so.. use an
	// invalid one which we can then blow away.
	$replacementbits['__scriptfile__'] = $script;
	$replacementbits = modulehook('everyfooter', $replacementbits);
    if ($session['user']['loggedin'])  $replacementbits = modulehook('everyfooter-loggedin', $replacementbits);

	unset($replacementbits['__scriptfile__']);
	//output any template part replacements that above hooks need (eg,
	//advertising)
	foreach ($replacementbits as $key => $val) $html[$key] = $val;

	$builtnavs = buildnavs();

	restore_buff_fields();
	calculate_buff_fields();

	tlschema('common');

	$charstats = charstats();
	restore_buff_fields();

	$sql = "SELECT motddate FROM " . DB::prefix("motd") . " ORDER BY motditem DESC LIMIT 1";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	DB::free_result($result);
	$headscript = '';
	if (isset($session['user']['lastmotd'])
		&& ($row['motddate'] > $session['user']['lastmotd'])
		&& (! isset($nopopup[$SCRIPT_NAME]) || $nopopups[$SCRIPT_NAME] != 1)
		&& $session['user']['loggedin']
	)
	{
		if (getsetting('forcedmotdpopup', 0)) $headscript .= popup('motd.php');

		$session['needtoviewmotd'] = true;
	}
	else $session['needtoviewmotd'] = false;

	if ('' != $headscript) $html['headscript'] = "<script language='JavaScript'>".$headscript."</script>";


	$script = '';

	if (! isset($session['user']['name'])) $session['user']['name'] = '';
	if (! isset($session['user']['login'])) $session['user']['login'] = '';

	//output keypress script
	reset($quickkeys);
	$script .= $lotgd_tpl->renderLotgdTemplate('key-press-script.html', ['quickkeys' => $quickkeys]);

	//NOTICE |
	//NOTICE | Although under the license, you're not required to keep this
	//NOTICE | paypal link, I do request, as the author of this software
	//NOTICE | which I have made freely available to you, that you leave it in.
	//NOTICE |

	$paypalData['currency'] = getsetting('paypalcurrency', 'USD');
	if (! isset($_SESSION['logdnet'])
		|| ! isset($_SESSION['logdnet'][''])
		|| $_SESSION['logdnet']['']==''
		|| date('Y-m-d H:i:s', strtotime('-1 hour')) > $session['user']['laston']
	) $already_registered_logdnet = false;
	else $already_registered_logdnet = true;

	$paypalData['registered_logdnet'] = false;
	if (getsetting('logdnet', 0) && $session['user']['loggedin'] && ! $already_registered_logdnet)
	{
		$paypalData['registered_logdnet'] = true;

		//account counting, just for my own records, I don't use this in the calculation for server order.
		$sql = "SELECT count(acctid) AS c FROM " . DB::prefix("accounts");
		$result = DB::query_cached($sql,"acctcount",600);
		$row = DB::fetch_assoc($result);
		$c = $row['c'];
		$a = getsetting("serverurl","http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] == 80?"":":".$_SERVER['SERVER_PORT']).dirname($_SERVER['REQUEST_URI']));
		if (!preg_match("/\/$/", $a)) {
			$a = $a . "/";
			savesetting("serverurl", $a);
		}

		$l = getsetting('defaultlanguage', 'en');
		$d = getsetting('serverdesc', 'Another LoGD Server');
		$e = getsetting('gameadminemail', 'postmaster@localhost.com');
		$u = getsetting('logdnetserver', 'http://logdnet.logd.com/');

		if (! preg_match("/\/$/", $u))
		{
			$u = $u . "/";
			savesetting('logdnetserver', $u);
		}

		$paypalData['v'] = rawurlencode($logd_version);
		$paypalData['c'] = rawurlencode($c);
		$paypalData['a'] = rawurlencode($a);
		$paypalData['l'] = rawurlencode($l);
		$paypalData['d'] = rawurlencode($d);
		$paypalData['e'] = rawurlencode($e);
		$paypalData['u'] = rawurlencode($u);
	}
	else
	{
		$paypalData['item_name'] = 'Legend of the Green Dragon Author Donation from '.full_sanitize($session['user']['name']);
		$paypalData['item_number'] = htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$paysite = getsetting('paypalemail', '');
	if ($paysite != '')
	{
		$paypalData['paysite'] = $paysite;
		$paypalData['item_name'] = getsetting('paypaltext', 'Legend of the Green Dragon Site Donation from').' '.full_sanitize($session['user']['name']);
		$paypalData['item_number'] = htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		if (file_exists("payment.php"))
		{
			$paypalData['notify_url'] = 'http://'.$_SERVER["HTTP_HOST"].dirname($_SERVER['REQUEST_URI']).'/payment.php';
		}

		$paypalData['paypalcountry-code'] = getsetting('paypalcountry-code', 'US');
	}

	$html['paypal'] = $lotgd_tpl->renderLotgdTemplate('paypal.html', $paypalData);
	unset($paypalData);

	//NOTICE |
	//NOTICE | Although I will not deny you the ability to remove the above
	//NOTICE | paypal link, I do request, as the author of this software
	//NOTICE | which I made available for free to you that you leave it in.
	//NOTICE |

	//output the nav
	// $footer = str_replace("{".($z)."}",$$z,$footer);
	$html[$z] = $$z;
	$html['nav'] = $lotgd_tpl->renderThemeTemplate('navs/menu.html', ['menu' => $builtnavs]);

	//output the motd
	$html['motd'] = motdlink();

	//output the mail link
	if (isset($session['user']['acctid']) && $session['user']['acctid']>0 && $session['user']['loggedin']) {
		if ($session['user']['prefs']['ajax'])
		{
			$script .= $lotgd_tpl->renderLotgdTemplate('mail-ajax.html', [
				'set_timeout' => ((getsetting('LOGINTIMEOUT',900)-120)*1000),
				'clear_xajax' => ((getsetting('LOGINTIMEOUT',900)+5)*1000)
			]);

			$html['mail'] = '<span id="maillink">'.maillink().'</span>';
		}
		else $html['mail'] = maillink();
	}
	else $html['mail'] = translate_inline("Log in to see your Ye Olde Mail");

	//output petition count
	$html['petition'] = "<a href='petition.php' onClick=\"".popup('petition.php').";return false;\" target='_blank' align='right' class='motd'>".translate_inline('Petition for Help')."</a>";

	if ($session['user']['superuser'] & SU_EDIT_PETITIONS)
	{
		$sql = "SELECT count(1) AS c, status FROM " . DB::prefix('petitions') . " GROUP BY status";
		$result = DB::query_cached($sql, 'petition_counts');
		$petitions = [0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0];
		while ($row = DB::fetch_assoc($result))
		{
			$petitions[(int)$row['status']] = $row['c'];
		}
		$pet = translate_inline("`0`bPetitions:`b");
		$ued = translate_inline("`0`bUser Editor`b");
		DB::free_result($result);
		$administrator = false;
		if ($session['user']['superuser'] & SU_EDIT_USERS) $administrator = true;

        $p = "`\${$petitions[5]}`0|`^{$petitions[4]}`0|`b{$petitions[0]}`b|{$petitions[1]}|`!{$petitions[3]}`0|`#{$petitions[7]}`0|`%{$petitions[6]}`0|`i{$petitions[2]}`i";

		$html['petitiondisplay'] = $lotgd_tpl->renderLotgdTemplate('other/petition.html', [
			'administrator' => $administrator,
			'petitioncount' => $p,
			'petition' => $pet,
			'ueditor' => $ued
		]);
	}

	//output character stats
	$html['stats'] = $charstats;
	unset($charstats);
	//Add all script in page
	$html['script'] = $script;
	//output view PHP source link
	$sourcelink = 'source.php?url='.preg_replace("/[?].*/", '', ($_SERVER['REQUEST_URI']));
	$html['source'] = "<a href='$sourcelink' onclick=\"".popup($sourcelink).";return false;\" target='_blank'>".translate_inline("View PHP Source")."</a>";
	//output version
	$html['version'] = "Version: $logd_version";
	//output page generation time
	$gentime = getmicrotime()-$pagestarttime;
	$session['user']['gentime']+=$gentime;
	$session['user']['gentimecount']++;
	if (getsetting('debug',0))
	{
		global $SCRIPT_NAME;
		$sql="INSERT INTO ".DB::prefix('debug')." VALUES (0,'pagegentime','runtime','".$SCRIPT_NAME."','".($gentime)."');";
		$resultdebug=DB::query($sql);
		$sql="INSERT INTO ".DB::prefix('debug')." VALUES (0,'pagegentime','dbtime','".$SCRIPT_NAME."','".(round($dbinfo['querytime'],3))."');";
		$resultdebug=DB::query($sql);
	}

	//-- Add pagegen info
	$html['pagegen'] = sprintf('Page gen: %ss / %s queries (%ss Ave: %ss - %s/%s',
		round($gentime, 3),
		$dbinfo['queriesthishit'],
		round($dbinfo['querytime'], 3),
		round($session['user']['gentime'] / $session['user']['gentimecount'], 3),
		round($session['user']['gentime'], 3),
		round($session['user']['gentimecount'], 3)
	);

	tlschema();

	//finalize output
	$html['content'] = $output->get_output();
	$browser_output = $lotgd_tpl->renderTheme($html);
	$session['user']['gensize'] += strlen($browser_output);
	$session['output'] = $browser_output;
	if ($saveuser === true)  saveuser();

	unset($session['output']);
	//this somehow allows some frames to load before the user's navs say it can
	session_write_close();
	echo $browser_output;
	exit();
}

/**
 * Page header for popup windows.
 *
 * @param string $title The title of the popup window
 */
function popup_header($title = 'Legend of the Green Dragon')
{
	global $html;

	translator_setup();

	modulehook('header-popup');

	$arguments = func_get_args();
	if (!$arguments || count($arguments) == 0) $arguments = ['Legend of the Green Dragon'];

	$title = sanitize(holidayize($title, 'title'));

	//-- Add to html
	$html['title'] = $title . tlbutton_pop();
}

/**
 * Ends page generation for popup windows.  Saves the user account info - doesn't update page generation stats
 *
 */
function popup_footer()
{
	global $output, $html, $session, $y2, $z2, $copyright, $lotgd_tpl;


	$footer = $template['popupfoot'];
	//add XAJAX mail stuff
	if ($session['user']['prefs']['ajax'])
	{
		require 'mailinfo_common.php';

		$xajax->printJavascript("lib/xajax");
		addnav('', 'mailinfo_server.php');
	}
	//END XAJAX

	// Pass the script file down into the footer so we can do something if
	// we need to on certain pages (much like we do on the header.
	// Problem is 'script' is a valid replacement token, so.. use an
	// invalid one which we can then blow away.
	$replacementbits = modulehook('footer-popup', []);
	//output any template part replacements that above hooks need
	reset($replacementbits);
	foreach ($replacementbits as $key=>$val) $html[$key] = $val;

	$z = $y2^$z2;
	$html[$z] = $$z;
	if (isset($session['user']['acctid']) && $session['user']['acctid']>0 && $session['user']['loggedin'])
	{
		if ($session['user']['prefs']['ajax'])
		{
			$html['script'] = $lotgd_tpl->renderLotgdTemplate('mail-ajax.html', [
				'set_timeout' => ((getsetting('LOGINTIMEOUT',900)-120)*1000),
				'clear_xajax' => ((getsetting('LOGINTIMEOUT',900)+5)*1000)
			]);
		}
	}

	//finalize output
	$html['content'] = $output->get_output();
	saveuser();
	session_write_close();
	echo $lotgd_tpl->renderThemeTemplate('popup.html', $html);
	exit();
}

$charstat_info = [];
$last_charstat_label = '';

/**
 * Resets the character stats array
 *
 */
function wipe_charstats()
{
	global $charstat_info, $last_charstat_label;

	$charstat_info = [];
	$last_charstat_label = '';
}

/**
 * Add a attribute and/or value to the character stats display
 *
 * @param string $label The label to use
 * @param mixed $value (optional) value to display
 */
function addcharstat($label, $value = false)
{
	global $charstat_info, $last_charstat_label;

	if (false === $value)
	{
		if (! isset($charstat_info[$label])) $charstat_info[$label] = [];
		$last_charstat_label = $label;
	}
	else
	{
		if ('' == $last_charstat_label)
		{
			$last_charstat_label = 'Other Info';
			addcharstat($last_charstat_label);
		}
		$charstat_info[$last_charstat_label][$label] = $value;
	}
}

/**
 * Returns the character stat related to the category ($cat) and the label
 *
 * @param string $cat The relavent category for the stat
 * @param string $label The label of the character stat
 * @return mixed The value associated with the stat
 */
function getcharstat($cat, $label)
{
	global $charstat_info;

	return $charstat_info[$cat][$label];
}

/**
 * Sets a value to the passed category & label for character stats
 *
 * @param string $cat The category for the char stat
 * @param string $label The label associated with the value
 * @param mixed $val The value of the attribute
 */
function setcharstat($cat, $label, $val)
{
	global $charstat_info, $last_charstat_label;

	if (! isset($charstat_info[$cat][$label]))
	{
		$oldlabel = $last_charstat_label;
		addcharstat($cat);
		addcharstat($label, $val);
		$last_charstat_label = $oldlabel;
	}
	else $charstat_info[$cat][$label] = $val;
}

/**
 * Returns output formatted character stats
 *
 * @param array $buffs
 * @return string
 */
function getcharstats($buffs)
{
	//returns output formatted character statistics.
	global $charstat_info, $lotgd_tpl;

	reset($charstat_info);
	$charstattpl = [];
	foreach ($charstat_info as $label => $section)
	{
		if (count($section))
		{
			$arr = array("title"=>translate_inline($label));
			$charstattpl[$arr] = [];
			reset($section);
			foreach ($section as $name => $val)
			{
				$a2 = translate_inline("`&$name`0");
				$charstattpl[$arr][$a2] = "`^$val`0";
			}
		}
	}

	$statbuff = $lotgd_tpl->renderLotgdTemplate('character/statbuff.html', [
		'title' => translate_inline("`0Buffs"),
		'value' => $buffs
	]);

	return appoencode($lotgd_tpl->renderLotgdTemplate('character/stats.html', [
		'charstat' => $charstattpl,
		'statbuff' => $statbuff
	]), true);
}

/**
 * Returns the value associated with the section & label.  Returns an empty string if the stat isn't set
 *
 * @param string $section The character stat section
 * @param string $title The stat display label
 * @return mixed The value associated with the stat
 */
function getcharstat_value($section, $title)
{
	global $charstat_info;

	if (isset($charstat_info[$section][$title])) return $charstat_info[$section][$title];
	else return;
}

/**
 * Returns the current character stats or (if the character isn't logged in) the currently online players
 * Hooks provided:
 *		charstats
 *
 * @return array The current stats for this character or the list of online players
 */
function charstats()
{
	global $session, $playermount, $companions;

	wipe_charstats();

	$u =& $session['user'];

	if ($session['loggedin'])
	{
		$u['hitpoints']=round($u['hitpoints'],0);
		$u['experience']=round($u['experience'],0);
		$spirits = [-6=>"Resurrected", -2=>"Very Low", -1=>"Low", "0"=>"Normal", 1=>"High", 2=>"Very High"];
		if (! $u['alive']) $spirits[(int)$u['spirits']] = translate_inline('DEAD', 'stats');
		//calculate_buff_fields();
		reset($session['bufflist']);

		require_once 'lib/playerfunctions.php';
		$o_atk = $atk = get_player_attack();//Original Attack
		$o_def = $def = get_player_defense();//Original Defense
		$spd = get_player_speed();
        $hitpoints = get_player_hitpoints();//Health of character
		$u['maxhitpoints'] = $hitpoints;

		$buffs = [];
		foreach ($session['bufflist'] as $val)
		{
			if (isset($val['suspended']) && $val['suspended']) continue;
			if (isset($val['atkmod'])) {
				$atk *= $val['atkmod'];
			}
			if (isset($val['defmod'])) {
				$def *= $val['defmod'];
			}
			// Short circuit if the name is blank
			if ($val['name'] > "" || $session['user']['superuser'] & SU_DEBUG_OUTPUT)
			{
				tlschema($val['schema']);
			//	if ($val['name']=="")
			//		$val['name'] = "DEBUG: {$key}";
			//	removed due to performance reasons. foreach is better with only $val than to have $key ONLY for the short happiness of one debug. much greater performance gain here
				if (is_array($val['name']))
				{
					$val['name'][0] = str_replace("`%","`%%",$val['name'][0]);
					$val['name']=call_user_func_array("sprintf_translate", $val['name']);
				}
				//in case it's a string
				else $val['name']=translate_inline($val['name']);

				if ($val['rounds']>=0)
				{
					// We're about to sprintf, so, let's makes sure that
					// `% is handled.
					//$n = translate_inline(str_replace("`%","`%%",$val['name']));
					$b = translate_inline("`#%s `7(%s rounds left)`n","buffs");
					$b = sprintf($b, $val['name'], $val['rounds']);
					$buffs[] = appoencode($b, true);
				}
				else $buffs [] = appoencode("`#{$val['name']}`n",true);
				tlschema();
			}
		}
		if (count($buffs)) $buffs[] = appoencode(translate_inline("`^None`0"), true);

		// $atk = $atk;
		// $def = $def;
		if ($atk < $o_atk) $atk = round($atk,2)."(`\$".round($atk-$o_atk,2).'`0)';
		else if($atk > $o_atk) $atk = round($atk,2)."(`@+".round($atk-$o_atk,2).'`0)';
		// They are equal, display in the 2 signifigant digit format.
		else $atk = round($atk, 2);

		if ($def < $o_def) $def = round($def,2)."(`\$".round($def-$o_def,2).'`0)';
		else if($def > $o_def) $def = round($def,2)."(`@+".round($def-$o_def,2).'`0)';
		// They are equal, display in the 2 signifigant digit format.
		else $def = round($def, 2);

		$point = getsetting('moneydecimalpoint', '.');
		$sep = getsetting('moneythousandssep', ',');

		addcharstat('Character Info');
		addcharstat('Name', $u['name']);
		addcharstat('Dragonkills', '`b'.$u['dragonkills'].'`b');
		addcharstat('Level', "`b".$u['level'].check_temp_stat('level', 1)."`b");
        if ($u['alive'])
		{
            //-- HitPoints are calculated in base to attributes
			addcharstat("Hitpoints", $u['hitpoints'].check_temp_stat("hitpoints",1)."`0/".$u['maxhitpoints'].check_temp_stat("maxhitpoints",1)." `\$<span title='".explained_get_player_hitpoints()."'>(?)</span>`0");
			if (is_module_active('staminasystem')) addcharstat("Stamina", "");
			if (is_module_active('displaycp')) addcharstat("Drunkeness", "");
			addcharstat("Experience",  number_format($u['experience'].check_temp_stat("experience",1),0,$point,$sep));
			addcharstat("Attack", $atk." `\$<span title='".explained_get_player_attack()."'>(?)</span>`0".check_temp_stat("attack",1));
			addcharstat("Defense", $def." `\$<span title='".explained_get_player_defense()."'>(?)</span>`0".check_temp_stat("defense",1));
			addcharstat("Speed", $spd.check_temp_stat("speed",1));
			addcharstat("Strength", $u['strength'].check_temp_stat("strength",1));
			addcharstat("Dexterity", $u['dexterity'].check_temp_stat("dexterity",1));
			addcharstat("Intelligence", $u['intelligence'].check_temp_stat("intelligence",1));
			addcharstat("Constitution", $u['constitution'].check_temp_stat("constitution",1));
			addcharstat("Wisdom", $u['wisdom'].check_temp_stat("wisdom",1));
		}
		else
		{
			$maxsoul = 50 + 10 * $u['level']+$u['dragonkills']*2;
			addcharstat("Soulpoints", $u['soulpoints'].check_temp_stat("soulpoints",1)."`0/".$maxsoul);
			if (is_module_active('staminasystem')) addcharstat("Stamina", "");
			addcharstat("Torments", $u['gravefights'].check_temp_stat("gravefights",1));
			addcharstat("Psyche", 10+round(($u['level']-1)*1.5));
			addcharstat("Spirit", 10+round(($u['level']-1)*1.5));
		}

		if ($u['race'] != RACE_UNKNOWN) addcharstat("Race", translate_inline($u['race'],"race"));
		else addcharstat("Race", translate_inline(RACE_UNKNOWN,"race"));

		if (count($companions)>0)
		{
			addcharstat("Companions");
			foreach ($companions as $name=>$companion)
			{
				if ($companion['hitpoints'] > 0 ||(isset($companion['cannotdie']) && $companion['cannotdie'] == true)) {
					if ($companion['hitpoints']<0) $companion['hitpoints'] = 0;

					if($companion['hitpoints']<$companion['maxhitpoints']) $color = "`\$";
					else $color = "`@";

					if (isset($companion['suspended']) && $companion['suspended'] == true) $suspcode = "`7 *";
					else $suspcode = "";

					addcharstat($companion['name'], $color.($companion['hitpoints'])."`7/`&".($companion['maxhitpoints'])."$suspcode`0");
				}
			}
		}
		addcharstat('Personal Info');
		if ($u['alive'])
		{
			// In version IDMarinas not use turns based game
			// addcharstat("Turns", $u['turns'].check_temp_stat("turns",1));
			addcharstat("PvP", $u['playerfights']);
			addcharstat("Spirits", translate_inline("`b".$spirits[(int)$u['spirits']]."`b"));
			addcharstat("Gold", number_format($u['gold'].check_temp_stat("gold",1),0,$point,$sep));
		}
		else addcharstat("Favor", $u['deathpower'].check_temp_stat("deathpower",1));

		addcharstat("Gems", number_format($u['gems'].check_temp_stat("gems",1),0,$point,$sep));
		addcharstat("Equipment Info");
		if (is_module_active('inventorypopup')) addcharstat("Inventory", "");
		addcharstat("Weapon", $u['weapon']);
		addcharstat("Armor", $u['armor']);
		if ($u['hashorse'])
			addcharstat("Creature", $playermount['mountname'] . "`0");

		modulehook("charstats");

		$charstat = getcharstats($buffs);

		if (!is_array($session['bufflist'])) $session['bufflist']= [];

		return $charstat;
	}
	else
	{
		if (! $ret = datacache("charlisthomepage"))
		{
			$onlinecount = 0;
			// If a module wants to do it's own display of the online chars,
			// let it.
			$list = modulehook('onlinecharlist', []);
			if ($list['handled'])
			{
				$onlinecount = $list['count'];
				$ret = $list['list'];
			}
			else
			{
				$sql="SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM " . DB::prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
				$result = DB::query($sql);
				$ret.=appoencode(sprintf(translate_inline("`bOnline Characters (%s players):`b`n"),DB::num_rows($result)));
				while ($row = DB::fetch_assoc($result))
				{
					$ret.=appoencode("`^{$row['name']}`n");
					$onlinecount++;
				}
				DB::free_result($result);
				if ($onlinecount==0) $ret.=appoencode(translate_inline("`iNone`i"));
			}
			savesetting("OnlineCount",$onlinecount);
			savesetting("OnlineCountLast",strtotime("now"));
			updatedatacache("charlisthomepage",$ret);
		}

		return $ret;
	}
}

/**
 * Returns a display formatted (and popup enabled) mail link - determines if unread mail exists and highlights the link if needed
 *
 * @return string The formatted mail link
 */
function maillink()
{
	global $session;

	$sql = "SELECT sum(if(seen=1,1,0)) AS seencount, sum(if(seen=0,1,0)) AS notseen FROM " . DB::prefix("mail") . " WHERE msgto=\"".$session['user']['acctid']."\"";
	$result = DB::query_cached($sql,"mail-{$session['user']['acctid']}",86400);
	$row = DB::fetch_assoc($result);
	DB::free_result($result);
	$row['seencount'] = (int)$row['seencount'];
	$row['notseen'] = (int)$row['notseen'];
	if ($row['notseen']>0)
		return sprintf("<a href='mail.php' target='_blank' onClick=\"".popup("mail.php").";return false;\" class='hotmotd'>".translate_inline("Ye Olde Mail: %s new, %s old","common")."</a>",$row['notseen'],$row['seencount']);
	else
		return sprintf("<a href='mail.php' target='_blank' onClick=\"".popup("mail.php").";return false;\" class='motd'>".translate_inline("Ye Olde Mail: %s new, %s old","common")."</a>",$row['notseen'],$row['seencount']);
}

/**
 * Returns a display formatted (and popup enabled) MOTD link - determines if unread MOTD items exist and highlights the link if needed
 *
 * @return string The formatted MOTD link
 */
function motdlink()
{
	global $session;

	if ($session['needtoviewmotd'])
		return "<a href='motd.php' target='_blank' onClick=\"".popup("motd.php").";return false;\" class='hotmotd'><b>".translate_inline("MoTD")."</b></a>";
	else
		return "<a href='motd.php' target='_blank' onClick=\"".popup("motd.php").";return false;\" class='motd'><b>".translate_inline("MoTD")."</b></a>";
}