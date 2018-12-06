<?php
/**
 * Library (supporting) functions for page output
 *		addnews ready
 *		translator ready
 *		mail ready.
 *
 * @author core_module
 * @author rewritten + adapted by IDMarinas
 */
global $html;

$nopopups = [];
$runheaders = [];
$html = ['content' => ''];

/**
 * Starts page output.  Inits the template and translator modules.
 *
 * @param array|string $title
 *                            Hooks provided:
 *                            everyheader
 *                            header-{scriptname}
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
        if (! array_key_exists($script, $runheaders))
        {
            $runheaders[$script] = false;
        }

        if (isset($runheaders[$script]) && ! $runheaders[$script])
        {
            modulehook('everyheader', ['script' => $script]);

            if ($session['user']['loggedin'])
            {
                modulehook('everyheader-loggedin', ['script' => $script]);
            }

            $runheaders[$script] = true;
            modulehook("header-$script");
        }
    }

    $arguments = func_get_args();

    if (0 == count($arguments))
    {
        $arguments = ['Legend of the Green Dragon'];
    }

    $title = call_user_func_array('sprintf_translate', $arguments);
    $title = sanitize(holidayize($title, 'title'));
    calculate_buff_fields();

    //-- Add to html
    $html['title'] = $title;
    $html['content'] .= tlbutton_pop();

    $html['userPre'] = $session['user'] ?? [];
    unset($html['userPre']['password']);

    if (getsetting('debug', 0))
    {
        $session['debugstart'] = microtime();
    }
}

/**
 * Brings all the output elements together and terminates the rendering of the page.  Saves the current user info and updates the rendering statistics
 * Hooks provided:
 *	footer-{$script name}
 *	everyfooter.
 */
function page_footer($saveuser = true)
{
    global $output, $html, $nav, $session, $pagestarttime, $quickkeys, $y2, $z2, $logd_version, $copyright, $license, $SCRIPT_NAME, $nopopups, $lotgdJaxon;

    $z = $y2 ^ $z2;
    $html[$z] = $license.${$z};
    $request = \LotgdLocator::get(\Lotgd\Core\Http::class);

    //page footer module hooks
    $script = substr($SCRIPT_NAME, 0, strpos($SCRIPT_NAME, '.'));
    $replacementbits = modulehook("footer-$script", []);

    if ('runmodule' == $script && (($module = httpget('module'))) > '')
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

    if (isset($session['user']['loggedin']) && $session['user']['loggedin'])
    {
        $replacementbits = modulehook('everyfooter-loggedin', $replacementbits);
    }

    unset($replacementbits['__scriptfile__']);
    //output any template part replacements that above hooks need (eg, advertising)
    foreach ($replacementbits as $key => $val)
    {
        if (! isset($html[$key]))
        {
            $html[$key] = $val;

            continue;
        }

        $html[$key] .= $val;
    }

    $builtnavs = buildnavs();

    restore_buff_fields();
    calculate_buff_fields();

    tlschema('common');

    $charstats = charstats();
    restore_buff_fields();

    $sql = 'SELECT motddate FROM '.\DB::prefix('motd').' ORDER BY motditem DESC LIMIT 1';
    $result = \DB::query($sql);
    $row = \DB::fetch_assoc($result);
    \DB::free_result($result);
    $headscript = '';

    $session['needtoviewmotd'] = false;
    if (isset($session['user']['lastmotd'])
        && ($row['motddate'] > $session['user']['lastmotd'])
        && (! isset($nopopup[$SCRIPT_NAME]) || 1 != $nopopups[$SCRIPT_NAME])
        && $session['user']['loggedin']
    ) {
        $session['needtoviewmotd'] = true;
    }

    $html['scripthead'] = '';
    if ('' != $headscript)
    {
        $html['scripthead'] = "<script language='text/javascript'>".$headscript.'</script>';
    }

    $script = '';

    $session['user']['name'] = !isset($session['user']['name']) ? '' : $session['user']['name'];
    $session['user']['login'] = !isset($session['user']['login']) ? '' : $session['user']['login'];

    //output keypress script
    reset($quickkeys);
    $script .= \LotgdTheme::renderLotgdTemplate('key-press-script.twig', ['quickkeys' => $quickkeys]);

    //NOTICE |
    //NOTICE | Although under the license, you're not required to keep this
    //NOTICE | paypal link, I do request, as the author of this software
    //NOTICE | which I have made freely available to you, that you leave it in.
    //NOTICE |

    $paypalData = ['site' => ['currency' => getsetting('paypalcurrency', 'USD')]];

    $alreadyRegisteredLogdnet = true;
    if (! isset($request->getServer('logdnet')[''])
        || '' == $request->getServer('logdnet')['']
        || ! isset($session['user']['laston'])
        || date('Y-m-d H:i:s', strtotime('-1 hour')) > $session['user']['laston']
    ) {
        $alreadyRegisteredLogdnet = false;
    }

    $paypalData['author']['register_logdnet'] = false;
    $paypalData['author']['item_name'] = 'Legend of the Green Dragon Author Donation from '.full_sanitize($session['user']['name']);
    $paypalData['author']['item_number'] = htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.$request->getServer('HTTP_HOST').$request->getServer('REQUEST_URI');

    if (getsetting('logdnet', 0) && $session['user']['loggedin'] && ! $alreadyRegisteredLogdnet)
    {
        //account counting, just for my own records, I don't use this in the calculation for server order.
        $sql = 'SELECT count(acctid) AS c FROM '.\DB::prefix('accounts');
        $result = \DB::query($sql);
        $row = \DB::fetch_assoc($result);
        $c = $row['c'];
        $a = getsetting('serverurl', 'http://'.$request->getServer('SERVER_NAME').(80 == $request->getServer('SERVER_PORT') ? '' : ':'.$request->getServer('SERVER_PORT')).dirname($request->getServer('REQUEST_URI')));

        if (! preg_match("/\/$/", $a))
        {
            $a = $a.'/';
            savesetting('serverurl', $a);
        }

        $l = getsetting('defaultlanguage', 'en');
        $d = getsetting('serverdesc', 'Another LoGD Server');
        $e = getsetting('gameadminemail', 'postmaster@localhost.com');
        $u = getsetting('logdnetserver', 'http://logdnet.logd.com/');

        if (! preg_match("/\/$/", $u))
        {
            $u = $u.'/';
            savesetting('logdnetserver', $u);
        }

        $paypalData['author']['register_logdnet'] = true;
        $paypalData['author']['v'] = rawurlencode($logd_version);
        $paypalData['author']['c'] = rawurlencode($c);
        $paypalData['author']['a'] = rawurlencode($a);
        $paypalData['author']['l'] = rawurlencode($l);
        $paypalData['author']['d'] = rawurlencode($d);
        $paypalData['author']['e'] = rawurlencode($e);
        $paypalData['author']['u'] = rawurlencode($u);
    }

    $paysite = getsetting('paypalemail', '');

    if ('' != $paysite)
    {
        $paypalData['site']['paysite'] = $paysite;
        $paypalData['site']['item_name'] = getsetting('paypaltext', 'Legend of the Green Dragon Site Donation from').' '.full_sanitize($session['user']['name']);
        $paypalData['site']['item_number'] = htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.$request->getServer('HTTP_HOST').$request->getServer('REQUEST_URI');

        if (file_exists('payment.php'))
        {
            $paypalData['site']['notify_url'] = 'http://'.$request->getServer('HTTP_HOST').dirname($request->getServer('REQUEST_URI')).'/payment.php';
        }

        $paypalData['site']['paypalcountry_code'] = getsetting('paypalcountry-code', 'US');
    }

    //-- Dragon Prime
    $paypalData['dp']['item_name'] = getsetting('paypaltext', 'Legend of the Green Dragon DP Donation from ').' '.full_sanitize($session['user']['name']);
    $paypalData['dp']['item_number'] = htmlentities($session['user']['login'].':'.$request->getServer('HTTP_HOST').$request->getServer('REQUEST_URI'), ENT_COMPAT, getsetting('charset', 'UTF-8'));

    $html['paypal'] = $html['paypal'] ?? '';
    $html['paypal'] .= \LotgdTheme::renderLotgdTemplate('paypal.twig', $paypalData);
    unset($paypalData);

    //NOTICE |
    //NOTICE | Although I will not deny you the ability to remove the above
    //NOTICE | paypal link, I do request, as the author of this software
    //NOTICE | which I made available for free to you that you leave it in.
    //NOTICE |

    //output the nav
    $html['nav'] = \LotgdTheme::renderThemeTemplate('sidebar/navigation/menu.twig', ['menu' => $builtnavs]);

    //output the motd
    $html['motd'] = motdlink();

    //output the mail link
    $html['mail'] = translate_inline('Log in to see your Ye Olde Mail');
    if (isset($session['user']['acctid']) && $session['user']['acctid'] > 0 && $session['user']['loggedin'])
    {
        $html['mail'] = maillink();
        if (isset($session['user']['prefs']['ajax']) && $session['user']['prefs']['ajax'])
        {
            $script .= '<script>window.setInterval("JaxonLotgd.Ajax.Core.Mail.status()", 15000); window.setInterval("JaxonLotgd.Ajax.Core.Timeout.status()", 10000);</script>';
            $html['mail'] = '<span id="maillink">'.maillink().'</span>';
        }
    }

    //output petition count
    $html['petition'] = '<a href="petition.php" target="_blank" id="petition-embed" class="motd" data-force="true" onclick="Lotgd.embed(this)"><b>'.translate_inline('Petition for Help').'</b></a>';

    $html['petitiondisplay'] = '';
    if (isset($session['user']['superuser']) && $session['user']['superuser'] & SU_EDIT_PETITIONS)
    {
        $sql = 'SELECT count(1) AS c, status FROM '.\DB::prefix('petitions').' GROUP BY status';
        $result = \DB::query($sql);
        $petitions = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];

        while ($row = \DB::fetch_assoc($result))
        {
            $petitions[(int) $row['status']] = $row['c'];
        }

        \DB::free_result($result);

        $administrator = ($session['user']['superuser'] & SU_EDIT_USERS);

        $p = "`\${$petitions[5]}`0|`^{$petitions[4]}`0|`b{$petitions[0]}´b|{$petitions[1]}|`!{$petitions[3]}`0|`#{$petitions[7]}`0|`%{$petitions[6]}`0|{$petitions[2]}";

        $html['petitiondisplay'] = \LotgdTheme::renderThemeTemplate('parts/petition.twig', [
            'administrator' => $administrator,
            'petitioncount' => $p
        ]);
    }

    //output character stats
    $html['stats'] = $charstats;
    unset($charstats);
    //Add all script in page
    $html['script'] = $script;
    //output view PHP source link
    $sourcelink = 'source.php?url='.preg_replace('/[?].*/', '', ($request->getServer('REQUEST_URI')));
    $html['source'] = "<a href='$sourcelink' id='source' onclick='Lotgd.embed(this)' data-size='fullscreen' target='_blank'>".translate_inline('View PHP Source').'</a>';
    //output version
    $html['version'] = "Version: $logd_version";
    //output page generation time
    $gentime = microtime(true) - $pagestarttime;
    $session['user']['gentime'] += $gentime;
    $session['user']['gentimecount']++;

    $wrapper = \LotgdLocator::get(\Lotgd\Core\Lib\Dbwrapper::class);
    if (getsetting('debug', 0))
    {
        global $SCRIPT_NAME;
        $sql = 'INSERT INTO '.\DB::prefix('debug')." VALUES (0,'pagegentime','runtime','".$SCRIPT_NAME."','".($gentime)."');";
        $resultdebug = \DB::query($sql);
        $sql = 'INSERT INTO '.\DB::prefix('debug')." VALUES (0,'pagegentime','dbtime','".$SCRIPT_NAME."','".(round($wrapper->getQueryTime(), 3))."');";
        $resultdebug = \DB::query($sql);
    }

    //-- Add pagegen info
    $html['pagegen'] = sprintf('Page gen: %ss / %s queries (%ss Ave: %ss - %s/%s',
        round($gentime, 3),
        $wrapper->getQueriesThisHit(),
        round($wrapper->getQueryTime(), 3),
        round($session['user']['gentime'] / $session['user']['gentimecount'], 3),
        round($session['user']['gentime'], 3),
        round($session['user']['gentimecount'], 3)
    );

    tlschema();

    //-- Finalize output
    $lotgdJaxon->processRequest();

    $html['csshead'] = $lotgdJaxon->getCss();
    $html['scripthead'] .= $lotgdJaxon->getJs();
    $html['scripthead'] .= $lotgdJaxon->getScript();

    $html['userPost'] = $session['user'] ?? [];
    $html['session'] = $session ?? [];
    unset($html['session']['user'], $html['user']['password']);

    $html['content'] .= $output->get_output();
    $browserOutput = \LotgdTheme::renderTheme($html);
    $session['user']['gensize'] += strlen($browserOutput);
    $session['output'] = $browserOutput;

    if (true === $saveuser)
    {
        saveuser();
    }

    unset($session['output']);
    //this somehow allows some frames to load before the user's navs say it can
    session_write_close();
    echo $browserOutput;

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

    if (! $arguments || 0 == count($arguments))
    {
        $arguments = ['Legend of the Green Dragon'];
    }

    $title = call_user_func_array('sprintf_translate', $arguments);
    $title = sanitize(holidayize($title, 'title'));

    $html['userPre'] = $session['user'] ?? [];
    unset($html['userPre']['password']);

    //-- Add to html
    $html['title'] = $title;
    $html['content'] .= tlbutton_pop();
}

/**
 * Ends page generation for popup windows.  Saves the user account info - doesn't update page generation stats.
 */
function popup_footer()
{
    global $output, $html, $session, $y2, $z2, $copyright, $license, $lotgdJaxon;

    // Pass the script file down into the footer so we can do something if
    // we need to on certain pages (much like we do on the header.
    // Problem is 'script' is a valid replacement token, so.. use an
    // invalid one which we can then blow away.
    $replacementbits = modulehook('footer-popup', []);
    //output any template part replacements that above hooks need
    reset($replacementbits);

    foreach ($replacementbits as $key => $val)
    {
        if (! isset($html[$key]))
        {
            $html[$key] = $val;

            continue;
        }

        $html[$key] .= $val;
    }

    $z = $y2 ^ $z2;
    $html[$z] = $license.${$z};

    //-- Finalize output
    $lotgdJaxon->processRequest();

    $html['userPost'] = $session['user'] ?? [];
    $html['session'] = $session ?? [];
    unset($html['session']['user'], $html['user']['password']);

    $html['csshead'] = $lotgdJaxon->getCss();
    $html['scripthead'] = $lotgdJaxon->getJs();
    $html['scripthead'] .= $lotgdJaxon->getScript();

    $html['content'] .= $output->get_output();
    saveuser();

    session_write_close();
    echo \LotgdTheme::renderThemeTemplate('popup.twig', $html);

    exit();
}

/**
 * Resets the character stats array.
 */
function wipe_charstats()
{
    $stats = \LotgdLocator::get(Lotgd\Core\Character\Stats::class);

    return $stats->wipeStats();
}

/**
 * Add a attribute and/or value to the character stats display.
 *
 * @param string $label The label to use
 * @param string  $value (optional) value to display
 */
function addcharstat($label, $value = null)
{
    $stats = \LotgdLocator::get(Lotgd\Core\Character\Stats::class);

    return $stats->addcharstat($label, $value);
}

/**
 * Returns the character stat related to the category ($cat) and the label.
 *
 * @param string $cat   The relavent category for the stat
 * @param string $label The label of the character stat
 *
 * @return string The value associated with the stat
 */
function getcharstat($cat, $label)
{
    $stats = \LotgdLocator::get(Lotgd\Core\Character\Stats::class);

    return $stats->getcharstat($cat, $label);
}

/**
 * Sets a value to the passed category & label for character stats.
 *
 * @param string $cat   The category for the char stat
 * @param string $label The label associated with the value
 * @param mixed  $val   The value of the attribute
 */
function setcharstat($cat, $label, $val)
{
    $stats = \LotgdLocator::get(Lotgd\Core\Character\Stats::class);

    return $stats->setcharstat($cat, $label, $val);
}

/**
 * Is alias of getcharstat
 *
 * @param string $section The character stat section
 * @param string $title   The stat display label
 *
 * @return string The value associated with the stat
 */
function getcharstat_value($section, $title)
{
    $stats = \LotgdLocator::get(Lotgd\Core\Character\Stats::class);

    return $stats->getcharstat($cat, $label);
}

$statbuff = '';
/**
 * Returns output formatted character stats.
 *
 * @param array $buffs
 *
 * @return string
 */
function getcharstats($buffs)
{
    //returns output formatted character statistics.
    global $statbuff;

    $stats = \LotgdLocator::get(Lotgd\Core\Character\Stats::class);

    $charstatInfo = $stats->getStats();
    $charstattpl = [];

    foreach ($charstatInfo as $label => $section)
    {
        if (count($section))
        {
            $arr = translate_inline($label);
            $charstattpl[$arr] = [];
            reset($section);

            foreach ($section as $name => $val)
            {
                $a2 = translate_inline("`&$name`0");
                $charstattpl[$arr][$a2] = "`^$val`0";
            }
        }
    }

    $statbuff = \LotgdTheme::renderThemeTemplate('sidebar/character/statbuff.twig', [
        'title' => translate_inline('`0Buffs'),
        'value' => $buffs
    ]);

    return appoencode(\LotgdTheme::renderThemeTemplate('sidebar/character/stats.twig', [
        'charstat' => $charstattpl,
        'statbuff' => $statbuff
    ]), true);
}

/**
 * Returns the current character stats or (if the character isn't logged in) the currently online players
 * Hooks provided:
 *		charstats.
 *
 * @param bool $return
 *
 * @return array The current stats for this character or the list of online players
 */
function charstats($return = true)
{
    global $session, $playermount, $companions;

    wipe_charstats();

    $u = &$session['user'];

    if (isset($session['loggedin']) && $session['loggedin'])
    {
        $u['hitpoints'] = round($u['hitpoints'], 0);
        $u['experience'] = round($u['experience'], 0);
        $spirits = [-6 => 'Resurrected', -2 => 'Very Low', -1 => 'Low', '0' => 'Normal', 1 => 'High', 2 => 'Very High'];

        if (! $u['alive'])
        {
            $spirits[(int) $u['spirits']] = 'DEAD';
        }
        reset($session['bufflist']);

        require_once 'lib/playerfunctions.php';
        $oAtk = $atk = get_player_attack(); //Original Attack
        $oDef = $def = get_player_defense(); //Original Defense
        $spd = get_player_speed();
        $hitpoints = get_player_hitpoints(); //Health of character
        $u['maxhitpoints'] = $hitpoints;

        $buffs = [];

        foreach ($session['bufflist'] as $val)
        {
            if (($val['suspended'] ?? false))
            {
                continue;
            }
            $atk *= ($val['atkmod'] ?? 1);
            $def *= ($val['defmod'] ?? 1);

            // Short circuit if the name is blank
            if ($val['name'] > '' || $session['user']['superuser'] & SU_DEBUG_OUTPUT)
            {
                tlschema($val['schema']);
                //	removed due to performance reasons. foreach is better with only $val than to have $key ONLY for the short happiness of one debug. much greater performance gain here
                if (is_array($val['name']))
                {
                    $val['name'][0] = str_replace('`%', '`%%', $val['name'][0]);
                    $val['name'] = call_user_func_array('sprintf_translate', $val['name']);
                }
                //in case it's a string
                else
                {
                    $val['name'] = translate_inline($val['name']);
                }

                if ($val['rounds'] >= 0)
                {
                    // We're about to sprintf, so, let's makes sure that
                    // `% is handled.
                    $b = translate_inline('`#%s `7(%s rounds left)`0`n', 'buffs');
                    $b = sprintf($b, $val['name'], $val['rounds']);
                    $buffs[] = appoencode($b, true);
                }
                else
                {
                    $buffs[] = appoencode("`#{$val['name']}`0`n", true);
                }
                tlschema();
            }
        }

        if (! count($buffs))
        {
            $buffs[] = appoencode(translate_inline('`^None`0'), true);
        }

        $atk = round($atk, 2);
        if ($atk < $oAtk)
        {
            $atk = round($atk, 2).'(`$'.round($atk - $oAtk, 2).'`0)';
        }
        elseif ($atk > $oAtk)
        {
            $atk = round($atk, 2).'(`@+'.round($atk - $oAtk, 2).'`0)';
        }

        $def = round($def, 2);
        if ($def < $oDef)
        {
            $def = round($def, 2).'(`$'.round($def - $oDef, 2).'`0)';
        }
        elseif ($def > $oDef)
        {
            $def = round($def, 2).'(`@+'.round($def - $oDef, 2).'`0)';
        }

        addcharstat('Character Info');
        addcharstat('Name', $u['name']);
        addcharstat('Dragonkills', '`b'.$u['dragonkills'].'´b');
        addcharstat('Level', '`b'.$u['level'].check_temp_stat('level', 1).'´b');

        if ($u['alive'])
        {
            //-- HitPoints are calculated in base to attributes
            addcharstat('Hitpoints', sprintf('%s/%s `$<span title="%s">(?)</span>`0', $u['hitpoints'].check_temp_stat('hitpoints', 1), $u['maxhitpoints'].check_temp_stat('maxhitpoints', 1), explained_get_player_hitpoints()));

            if (is_module_active('staminasystem'))
            {
                addcharstat('Stamina', '');
            }
            else
            {
                addcharstat('Turns', $u['turns'].check_temp_stat('turns', 1));
            }

            if (is_module_active('displaycp'))
            {
                addcharstat('Drunkeness', '');
            }
            addcharstat('Experience', LotgdFormat::numeral($u['experience'].check_temp_stat('experience', 1)));
            addcharstat('Attack', sprintf("$atk `\$<span title='%s'>(?)</span>`0", explained_get_player_attack().check_temp_stat('attack', 1)));
            addcharstat('Defense', sprintf("$def `\$<span title='%s'>(?)</span>`0", explained_get_player_defense().check_temp_stat('defense', 1)));
            addcharstat('Speed', $spd.check_temp_stat('speed', 1));
            addcharstat('Strength', $u['strength'].check_temp_stat('strength', 1));
            addcharstat('Dexterity', $u['dexterity'].check_temp_stat('dexterity', 1));
            addcharstat('Intelligence', $u['intelligence'].check_temp_stat('intelligence', 1));
            addcharstat('Constitution', $u['constitution'].check_temp_stat('constitution', 1));
            addcharstat('Wisdom', $u['wisdom'].check_temp_stat('wisdom', 1));
        }
        else
        {
            $maxsoul = 50 + 10 * $u['level'] + $u['dragonkills'] * 2;
            addcharstat('Soulpoints', $u['soulpoints'].check_temp_stat('soulpoints', 1).'`0/'.$maxsoul);

            if (is_module_active('staminasystem'))
            {
                addcharstat('Stamina', '');
            }
            addcharstat('Torments', $u['gravefights'].check_temp_stat('gravefights', 1));
            addcharstat('Psyche', 10 + round(($u['level'] - 1) * 1.5));
            addcharstat('Spirit', 10 + round(($u['level'] - 1) * 1.5));
        }

        addcharstat('Race', translate_inline((RACE_UNKNOWN != $u['race']) ? $u['race'] : RACE_UNKNOWN, 'race'));

        if (count($companions) > 0)
        {
            addcharstat('Companions');

            foreach ($companions as $name => $companion)
            {
                if ($companion['hitpoints'] > 0 || (isset($companion['cannotdie']) && true == $companion['cannotdie']))
                {
                    $companion['hitpoints'] = max(0, $companion['hitpoints']);

                    $color = '`@';
                    if ($companion['hitpoints'] < $companion['maxhitpoints'])
                    {
                        $color = '`$';
                    }

                    $suspcode = '';
                    if (isset($companion['suspended']) && true == $companion['suspended'])
                    {
                        $suspcode = '`7 *';
                    }

                    addcharstat($companion['name'], $color.($companion['hitpoints']).'`7/`&'.($companion['maxhitpoints'])."$suspcode`0");
                }
            }
        }
        addcharstat('Personal Info');

        if ($u['alive'])
        {
            addcharstat('PvP', $u['playerfights']);
        }
        else
        {
            addcharstat('Favor', $u['deathpower'].check_temp_stat('deathpower', 1));
        }

        addcharstat('Spirits', translate_inline('`b'.$spirits[(int) $u['spirits']].'´b'));
        addcharstat('Gold', LotgdFormat::numeral($u['gold'].check_temp_stat('gold', 1)));
        addcharstat('Gems', LotgdFormat::numeral($u['gems'].check_temp_stat('gems', 1)));

        addcharstat('Equipment Info');

        if (is_module_active('inventorypopup'))
        {
            addcharstat('Inventory', '');
        }
        addcharstat('Weapon', $u['weapon']);
        addcharstat('Armor', $u['armor']);

        if ($u['hashorse'])
        {
            addcharstat('Creature', $playermount['mountname'].'`0');
        }

        modulehook('charstats');

        if ($return)
        {
            $charstat = getcharstats($buffs);

            if (! is_array($session['bufflist']))
            {
                $session['bufflist'] = [];
            }

            return $charstat;
        }

        return;
    }

    if (! $ret = datacache('charlisthomepage'))
    {
        $onlinecount = 0;
        // If a module wants to do it's own display of the online chars, let it.
        $list = modulehook('onlinecharlist', []);

        if (isset($list['handled']) && $list['handled'])
        {
            $onlinecount = $list['count'];
            $ret = $list['list'];
        }
        else
        {
            $sql = 'SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM '.\DB::prefix('accounts')." WHERE locked=0 AND loggedin=1 AND laston>'".date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds'))."' ORDER BY level DESC";
            $result = \DB::query($sql);
            $ret .= appoencode(sprintf(translate_inline('`bOnline Characters (%s players):´b`n'), \DB::num_rows($result)));

            while ($row = \DB::fetch_assoc($result))
            {
                $ret .= appoencode("`^{$row['name']}`0`n");
                $onlinecount++;
            }
            \DB::free_result($result);

            if (0 == $onlinecount)
            {
                $ret .= appoencode(translate_inline('`iNone´i'));
            }
        }
        savesetting('OnlineCount', $onlinecount);
        savesetting('OnlineCountLast', strtotime('now'));
        updatedatacache('charlisthomepage', $ret);
    }

    return $ret;
}

/**
 * Returns a display formatted (and popup enabled) mail link - determines if unread mail exists and highlights the link if needed.
 *
 * @return string The formatted mail link
 */
function maillink(): string
{
    global $session;

    $sql = 'SELECT sum(if(seen=1,1,0)) AS seencount, sum(if(seen=0,1,0)) AS notseen FROM '.\DB::prefix('mail')." WHERE msgto='{$session['user']['acctid']}'";
    $result = \DB::query($sql);
    $row = \DB::fetch_assoc($result);
    \DB::free_result($result);
    $row['seencount'] = (int) ($row['seencount'] ?? 0);
    $row['notseen'] = (int) ($row['notseen'] ?? 0);

    $session['mail']['seencount'] = $row['seencount'];
    $session['mail']['notseen'] = $row['notseen'];

    $text = sprintf(translate_inline('Ye Olde Mail: %s new, %s old', 'common'), $row['notseen'], $row['seencount']);
    if ($row['notseen'] > 0)
    {
        return '<a href="mail.php" target="_blank" id="mail-embed" class="hotmotd" data-force="true" onclick="Lotgd.embed(this)"><b>'.$text.'</b></a>';
    }

    return '<a href="mail.php" target="_blank" id="mail-embed" class="hotmotd" data-force="true" onclick="Lotgd.embed(this)">'.$text.'</a>';
}

/**
 * Returns a display formatted (and popup enabled) MOTD link - determines if unread MOTD items exist and highlights the link if needed.
 *
 * @return string The formatted MOTD link
 */
function motdlink()
{
    global $session;

    if ($session['needtoviewmotd'])
    {
        return '<a href="motd.php" target="_blank" id="motd-embed" class="hotmotd" data-force="true" onclick="Lotgd.embed(this)"><i class="certificate icon"></i> <b>'.translate_inline('MoTD').'</b></a>';
    }

    return '<a href="motd.php" target="_blank" id="motd-embed" class="motd" data-force="true" onclick="Lotgd.embed(this)">'.translate_inline('MoTD').'</a>';
}
