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
global $html, $statbuff;

$nopopups = [];
$runheaders = [];
$html = ['content' => ''];

/**
 * Starts page output.  Inits the template and translator modules.
 *
 * @param string|null $title
 * @param array       $params
 * @param string|null $textDomain
 *
 * Hooks provided:
 *      everyheader
 *      header-{scriptname}
 */
function page_header(?string $title = null, array $params = [], ?string $textDomain = null)
{
    global $html, $session, $runheaders, $nopopups;

    $nopopups['login.php'] = 1;
    $nopopups['motd.php'] = 1;
    $nopopups['index.php'] = 1;
    $nopopups['create.php'] = 1;
    $nopopups['about.php'] = 1;
    $nopopups['mail.php'] = 1;

    $session['user']['chatloc'] = '';

    if (! $title)
    {
        $title = 'title';
        $textDomain = 'app-default';
    }

    $html['title'] = [
        'title' => $title,
        'params' => $params,
        'textDomain' => $textDomain
    ];

    $script = \LotgdHttp::getServer('SCRIPT_NAME');
    $script = substr($script, 0, strpos($script, '.'));

    if ($script)
    {
        if (! array_key_exists($script, $runheaders))
        {
            $runheaders[$script] = false;
        }

        if (isset($runheaders[$script]) && ! $runheaders[$script])
        {
            modulehook('everyheader', ['script' => $script]);

            if ($session['user']['loggedin'] ?? false)
            {
                modulehook('everyheader-loggedin', ['script' => $script]);
            }

            $runheaders[$script] = true;

            modulehook("header-{$script}");

            //-- If the script is runmodule use name of module to module hook
            if ('runmodule' == $script && ($module = (string) \LotgdHttp::getQuery('module')))
            {
                // This modulehook allows you to hook directly into any module without
                // the need to hook into header-runmodule and then checking for the required module.
                modulehook("header-{$module}");
            }
        }
    }

    calculate_buff_fields();

    //-- Add to html
    $html['content'] .= tlbutton_pop();

    $html['userPre'] = $session['user'] ?? [];
    $html['session'] = $session ?? [];
    unset($html['session']['user'], $html['userPre']['password']);
}

/**
 * Brings all the output elements together and terminates the rendering of the page.  Saves the current user info and updates the rendering statistics
 * Hooks provided:
 *	footer-{$script name}
 *	everyfooter.
 */
function page_footer($saveuser = true)
{
    global $output, $html, $session, $nopopups, $lotgdJaxon;

    //page footer module hooks
    $script = \LotgdHttp::getServer('SCRIPT_NAME');
    $script = substr($script, 0, strpos($script, '.'));
    $replacementbits = modulehook("footer-$script", []);

    if ('runmodule' == $script && ($module = (string) \LotgdHttp::getQuery('module')))
    {
        // This modulehook allows you to hook directly into any module without
        // the need to hook into footer-runmodule and then checking for the required module.
        $replacementbits = modulehook("footer-{$module}", $replacementbits);
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

    restore_buff_fields();
    calculate_buff_fields();

    $charstats = charstats();
    restore_buff_fields();

    $repository = \Doctrine::getRepository('LotgdCore:Motd');
    $lastMotd = $repository->getLastMotdDate();

    $headscript = '';

    $session['needtoviewmotd'] = $session['needtoviewmotd'] ?? false;

    if (isset($session['user']['lastmotd'])
        && ($lastMotd > $session['user']['lastmotd'])
        && (! isset($nopopups[LotgdHttp::getServer('SCRIPT_NAME')]) || 1 != $nopopups[LotgdHttp::getServer('SCRIPT_NAME')])
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

    $session['user']['name'] = $session['user']['name'] ?? '';
    $session['user']['login'] = $session['user']['login'] ?? '';

    //NOTICE |
    //NOTICE | Although under the license, you're not required to keep this
    //NOTICE | paypal link, I do request, as the author of this software
    //NOTICE | which I have made freely available to you, that you leave it in.
    //NOTICE |

    $paypalData = ['site' => ['currency' => getsetting('paypalcurrency', 'USD')]];

    $alreadyRegisteredLogdnet = true;

    if ('' == ($session['logdnet'][''] ?? '') || ! isset($session['user']['laston']) || strtotime('-1 hour') > $session['user']['laston']->getTimestamp())
    {
        $alreadyRegisteredLogdnet = false;
    }

    $paypalData['author']['register_logdnet'] = false;
    $paypalData['author']['item_name'] = 'Legend of the Green Dragon Author Donation from '.\LotgdSanitize::fullSanitize($session['user']['name']);
    $paypalData['author']['item_number'] = htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.LotgdHttp::getServer('HTTP_HOST').'/'.LotgdHttp::getServer('REQUEST_URI');

    if (getsetting('logdnet', 0) && $session['user']['loggedin'] && ! $alreadyRegisteredLogdnet)
    {
        //account counting, just for my own records, I don't use this in the calculation for server order.
        $repository = \Doctrine::getRepository('LotgdCore:Accounts');
        $c = $repository->count([]);
        $a = getsetting('serverurl', 'http://'.LotgdHttp::getServer('SERVER_NAME').(80 == LotgdHttp::getServer('SERVER_PORT') ? '' : ':'.LotgdHttp::getServer('SERVER_PORT')).dirname(LotgdHttp::getServer('REQUEST_URI')));

        if (! preg_match("/\/$/", $a))
        {
            $a = $a.'/';
            savesetting('serverurl', $a);
        }

        $l = getsetting('defaultlanguage', 'en');
        $d = getsetting('serverdesc', 'Another LoGD Server');
        $e = getsetting('gameadminemail', 'postmaster@localhost.com');
        $u = getsetting('logdnetserver', 'http://lotgd.net');

        if (! preg_match("/\/$/", $u))
        {
            $u = $u.'/';
            savesetting('logdnetserver', $u);
        }

        $paypalData['author']['register_logdnet'] = true;
        $paypalData['author']['v'] = rawurlencode(\Lotgd\Core\Application::VERSION);
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
        $paypalData['site']['item_name'] = getsetting('paypaltext', 'Legend of the Green Dragon Site Donation from').' '.\LotgdSanitize::fullSanitize($session['user']['name']);
        $paypalData['site']['item_number'] = htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.LotgdHttp::getServer('HTTP_HOST').LotgdHttp::getServer('REQUEST_URI');

        if (file_exists('public/payment.php'))
        {
            $paypalData['site']['notify_url'] = '//'.LotgdHttp::getServer('HTTP_HOST').dirname(LotgdHttp::getServer('REQUEST_URI')).'/payment.php';
        }

        $paypalData['site']['paypalcountry_code'] = getsetting('paypalcountry-code', 'US');
    }

    //-- Dragon Prime
    $paypalData['dp']['item_name'] = getsetting('paypaltext', 'Legend of the Green Dragon DP Donation from ').' '.\LotgdSanitize::fullSanitize($session['user']['name']);
    $paypalData['dp']['item_number'] = htmlentities($session['user']['login'].':'.LotgdHttp::getServer('HTTP_HOST').LotgdHttp::getServer('REQUEST_URI'), ENT_COMPAT, getsetting('charset', 'UTF-8'));

    $html['paypal'] = $html['paypal'] ?? '';
    $html['paypal'] .= \LotgdTheme::renderLotgdTemplate('core/paypal.twig', $paypalData);
    unset($paypalData);

    //NOTICE |
    //NOTICE | Although I will not deny you the ability to remove the above
    //NOTICE | paypal link, I do request, as the author of this software
    //NOTICE | which I made available for free to you that you leave it in.
    //NOTICE |

    //output character stats
    $html['stats'] = $charstats;
    unset($charstats);
    //Add all script in page
    $html['script'] = $script;
    //output page generation time
    $gentime = \Tracy\Debugger::timer('page-footer');
    $session['user']['gentime'] += $gentime;
    $session['user']['gentimecount']++;

    $wrapper = \LotgdLocator::get(\Lotgd\Core\Db\Dbwrapper::class);

    //-- Register data in debug server
    if (getsetting('debug', 0))
    {
        $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Debug::class);

        $runtimeEntity = $repository->hydrateEntity([
            'type' => 'pagegentime',
            'category' => 'runtime',
            'subcategory' => LotgdHttp::getServer('SCRIPT_NAME'),
            'value' => $gentime
        ]);
        $dbtimeEntity = $repository->hydrateEntity([
            'type' => 'pagegentime',
            'category' => 'dbtime',
            'subcategory' => LotgdHttp::getServer('SCRIPT_NAME'),
            'value' => round($wrapper->getQueryTime(), 5)
        ]);

        \Doctrine::persist($runtimeEntity);
        \Doctrine::persist($dbtimeEntity);
    }

    //-- Finalize output
    $lotgdJaxon->processRequest();

    $html['csshead'] = $html['csshead'] ?? '';
    $html['csshead'] .= $lotgdJaxon->getCss();
    $html['scripthead'] .= $lotgdJaxon->getJs();
    $html['scripthead'] .= $lotgdJaxon->getScript();

    $html['userPost'] = $session['user'] ?? [];
    unset($html['userPost']['password']);

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

    \Doctrine::flush();
    \Doctrine::clear();

    exit();
}

/**
 * Page header for popup windows.
 *
 * @param string|null $title
 * @param array       $params
 * @param string|null $textDomain
 */
function popup_header(?string $title = null, array $params = [], ?string $textDomain = null)
{
    global $html, $session;

    $session['user']['chatloc'] = '';

    modulehook('header-popup');

    if (! $title)
    {
        $title = 'title';
        $textDomain = 'app-default';
    }

    $html['title'] = [
        'title' => $title,
        'params' => $params,
        'textDomain' => $textDomain
    ];

    $html['userPre'] = $session['user'] ?? [];
    $html['session'] = $session ?? [];
    unset($html['session']['user'], $html['userPre']['password']);

    //-- Add to html
    $html['content'] .= tlbutton_pop();
}

/**
 * Ends page generation for popup windows.  Saves the user account info - doesn't update page generation stats.
 */
function popup_footer()
{
    global $output, $html, $session, $lotgdJaxon;

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

    //-- Finalize output
    $lotgdJaxon->processRequest();

    $html['userPost'] = $session['user'] ?? [];
    $html['session'] = $session ?? [];
    unset($html['session']['user'], $html['userPost']['password']);

    $html['csshead'] = $html['csshead'] ?? '';
    $html['csshead'] .= $lotgdJaxon->getCss();
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
 * @param string $value (optional) value to display
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
 * Is alias of getcharstat.
 *
 * @param string $section The character stat section
 * @param string $title   The stat display label
 *
 * @return string The value associated with the stat
 */
function getcharstat_value($section, $title)
{
    return getcharstat($section, $title);
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

        $session['bufflist'] = array_map('array_filter', $session['bufflist'] ?? []);
        foreach ($session['bufflist'] as $val)
        {
            if ($val['suspended'] ?? false)
            {
                continue;
            }
            $atk *= ($val['atkmod'] ?? 1);
            $def *= ($val['defmod'] ?? 1);

            $val['name'] = $val['name'] ?? '';

            // Short circuit if the name is blank
            if ($val['name'] > '' || $session['user']['superuser'] & SU_DEBUG_OUTPUT)
            {
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

                $val['rounds'] = $val['rounds'] ?? 0;
                if ($val['rounds'] >= 0)
                {
                    // We're about to sprintf, so, let's makes sure that
                    // `% is handled.
                    $b = translate_inline('`#%s `7(%s rounds left)`0`n', 'buffs');
                    $b = sprintf($b, $val['name'], $val['rounds']);
                    $buffs[] = appoencode($b, true);
                }
                elseif ($val['name'])
                {
                    $buffs[] = appoencode("`#{$val['name']}`0`n", true);
                }
                else
                {
                    $buffs[] = appoencode("<code>`7{$val['schema']}`0</code>`n", true);
                }
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

        addcharstat(\LotgdTranslator::t('statistic.category.character.info', [], 'app-default'));
        addcharstat(\LotgdTranslator::t('statistic.stat.name', [], 'app-default'), $u['name']);
        addcharstat(\LotgdTranslator::t('statistic.stat.dragonkills', [], 'app-default'), '`b'.$u['dragonkills'].'´b');
        addcharstat(\LotgdTranslator::t('statistic.stat.level', [], 'app-default'), '`b'.$u['level'].check_temp_stat('level', 1).'´b');

        if ($u['alive'])
        {
            //-- HitPoints are calculated in base to attributes
            addcharstat(\LotgdTranslator::t('statistic.stat.hitpoints', [], 'app-default'), sprintf('%s/%s `$<span title="%s">(?)</span>`0', $u['hitpoints'].check_temp_stat('hitpoints', 1), $u['maxhitpoints'].check_temp_stat('maxhitpoints', 1), explained_get_player_hitpoints()));

            if (is_module_active('staminasystem'))
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.stamina', [], 'app-default'), '');
            }
            else
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.turns', [], 'app-default'), $u['turns'].check_temp_stat('turns', 1));
            }

            if (is_module_active('displaycp'))
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.drunkeness', [], 'app-default'), '');
            }
            addcharstat(\LotgdTranslator::t('statistic.stat.experience', [], 'app-default'), LotgdFormat::numeral($u['experience'].check_temp_stat('experience', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.attack', [], 'app-default'), sprintf("$atk `\$<span title='%s'>(?)</span>`0", explained_get_player_attack().check_temp_stat('attack', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.defense', [], 'app-default'), sprintf("$def `\$<span title='%s'>(?)</span>`0", explained_get_player_defense().check_temp_stat('defense', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.speed', [], 'app-default'), $spd.check_temp_stat('speed', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.strength', [], 'app-default'), $u['strength'].check_temp_stat('strength', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.dexterity', [], 'app-default'), $u['dexterity'].check_temp_stat('dexterity', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.intelligence', [], 'app-default'), $u['intelligence'].check_temp_stat('intelligence', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.constitution', [], 'app-default'), $u['constitution'].check_temp_stat('constitution', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.wisdom', [], 'app-default'), $u['wisdom'].check_temp_stat('wisdom', 1));
        }
        else
        {
            $maxsoul = 50 + 10 * $u['level'] + $u['dragonkills'] * 2;
            addcharstat(\LotgdTranslator::t('statistic.stat.soulpoints', [], 'app-default'), $u['soulpoints'].check_temp_stat('soulpoints', 1).'`0/'.$maxsoul);

            if (is_module_active('staminasystem'))
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.stamina', [], 'app-default'), '');
            }
            addcharstat(\LotgdTranslator::t('statistic.stat.torments', [], 'app-default'), $u['gravefights'].check_temp_stat('gravefights', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.psyche', [], 'app-default'), 10 + round(($u['level'] - 1) * 1.5));
            addcharstat(\LotgdTranslator::t('statistic.stat.spirit', [], 'app-default'), 10 + round(($u['level'] - 1) * 1.5));
        }

        addcharstat(\LotgdTranslator::t('statistic.stat.race', [], 'app-default'), \LotgdTranslator::t('character.racename', [], (RACE_UNKNOWN != $u['race']) ? $u['race'] : RACE_UNKNOWN, 'race'));

        if (count($companions) > 0)
        {
            addcharstat(\LotgdTranslator::t('statistic.category.companions', [], 'app-default'));

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
        addcharstat(\LotgdTranslator::t('statistic.category.character.personal', [], 'app-default'));

        if ($u['alive'])
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.pvp', [], 'app-default'), $u['playerfights']);
        }
        else
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.favor', [], 'app-default'), $u['deathpower'].check_temp_stat('deathpower', 1));
        }

        addcharstat(\LotgdTranslator::t('statistic.stat.spirits', [], 'app-default'), translate_inline('`b'.$spirits[(int) $u['spirits']].'´b'));
        addcharstat(\LotgdTranslator::t('statistic.stat.gold', [], 'app-default'), LotgdFormat::numeral($u['gold'].check_temp_stat('gold', 1)));
        addcharstat(\LotgdTranslator::t('statistic.stat.gems', [], 'app-default'), LotgdFormat::numeral($u['gems'].check_temp_stat('gems', 1)));

        addcharstat(\LotgdTranslator::t('statistic.category.character.equip', [], 'app-default'));

        if (is_module_active('inventorypopup'))
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.inventory', [], 'app-default'), '');
        }
        addcharstat(\LotgdTranslator::t('statistic.stat.weapon', [], 'app-default'), $u['weapon']);
        addcharstat(\LotgdTranslator::t('statistic.stat.armor', [], 'app-default'), $u['armor']);

        if ($u['hashorse'])
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.creature', [], 'app-default'), $playermount['mountname'].'`0');
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
            $repository = \Doctrine::getRepository('LotgdCore:Accounts');
            $result = $repository->getListAccountsOnline();
            $onlinecount = count($result);

            $ret = \LotgdTheme::renderThemeTemplate('parts/online-list.twig', [
                'list' => $result,
                'onlineCount' => $onlinecount,
                'textDomain' => 'page-home'
            ]);
        }

        savesetting('OnlineCount', $onlinecount);
        savesetting('OnlineCountLast', strtotime('now'));
        updatedatacache('charlisthomepage', $ret);
    }

    return $ret;
}
