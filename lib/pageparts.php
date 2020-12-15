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

$nopopups   = [];
$runheaders = [];
$html       = ['content' => ''];

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
    $charstattpl  = [];

    foreach ($charstatInfo as $label => $section)
    {
        if (\count($section))
        {
            $arr               = translate_inline($label);
            $charstattpl[$arr] = [];
            \reset($section);

            foreach ($section as $name => $val)
            {
                $a2                     = translate_inline("`&{$name}`0");
                $charstattpl[$arr][$a2] = "`^{$val}`0";
            }
        }
    }

    $template = \LotgdTheme::load('@theme'.\LotgdTheme::getThemeNamespace().'/_blocks/_character_stats.html.twig');

    $statbuff = $template->renderBlock('character_stat_buff', [
        'value' => $buffs,
    ]);

    return \LotgdFormat::colorize($template->renderBlock('character_stats', [
        'charstat' => $charstattpl,
        'statbuff' => $statbuff,
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

    if (isset($session['loggedin']) && $session['loggedin'])
    {
        $u = &$session['user'];

        $u['hitpoints']  = \round($u['hitpoints'], 0);
        $u['experience'] = \round($u['experience'], 0);
        $spirits         = [-6 => 'Resurrected', -2 => 'Very Low', -1 => 'Low', '0' => 'Normal', 1 => 'High', 2 => 'Very High'];

        if ( ! $u['alive'])
        {
            $spirits[(int) $u['spirits']] = 'DEAD';
        }
        \reset($session['bufflist']);

        require_once 'lib/playerfunctions.php';
        $oAtk              = $atk              = get_player_attack(); //Original Attack
        $oDef              = $def              = get_player_defense(); //Original Defense
        $spd               = get_player_speed();
        $hitpoints         = get_player_hitpoints(); //Health of character
        $u['maxhitpoints'] = $hitpoints;

        $buffs = [];

        $session['bufflist'] = \array_map('array_filter', $session['bufflist'] ?? []);

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
                if (\is_array($val['name']))
                {
                    $val['name'][0] = \str_replace('`%', '`%%', $val['name'][0]);
                    $val['name']    = \call_user_func_array('sprintf_translate', $val['name']);
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
                    $b       = translate_inline('`#%s `7(%s rounds left)`0`n', 'buffs');
                    $b       = \sprintf($b, $val['name'], $val['rounds']);
                    $buffs[] = \LotgdFormat::colorize($b, true);
                }
                elseif ($val['name'])
                {
                    $buffs[] = \LotgdFormat::colorize("`#{$val['name']}`0`n", true);
                }
                else
                {
                    $buffs[] = \LotgdFormat::colorize("<code>`7{$val['schema']}`0</code>`n", true);
                }
            }
        }

        if ( ! \count($buffs))
        {
            $buffs[] = \LotgdFormat::colorize(translate_inline('`^None`0'), true);
        }

        $atk = \round($atk, 2);

        if ($atk < $oAtk)
        {
            $atk = \round($atk, 2).'(`$'.\round($atk - $oAtk, 2).'`0)';
        }
        elseif ($atk > $oAtk)
        {
            $atk = \round($atk, 2).'(`@+'.\round($atk - $oAtk, 2).'`0)';
        }

        $def = \round($def, 2);

        if ($def < $oDef)
        {
            $def = \round($def, 2).'(`$'.\round($def - $oDef, 2).'`0)';
        }
        elseif ($def > $oDef)
        {
            $def = \round($def, 2).'(`@+'.\round($def - $oDef, 2).'`0)';
        }

        addcharstat(\LotgdTranslator::t('statistic.category.character.info', [], 'app-default'));
        addcharstat(\LotgdTranslator::t('statistic.stat.name', [], 'app-default'), $u['name']);
        addcharstat(\LotgdTranslator::t('statistic.stat.dragonkills', [], 'app-default'), '`b'.$u['dragonkills'].'´b');
        addcharstat(\LotgdTranslator::t('statistic.stat.level', [], 'app-default'), '`b'.$u['level'].check_temp_stat('level', 1).'´b');

        if ($u['alive'])
        {
            //-- HitPoints are calculated in base to attributes
            addcharstat(\LotgdTranslator::t('statistic.stat.hitpoints', [], 'app-default'), \sprintf('%s/%s `$<span title="%s">(?)</span>`0', $u['hitpoints'].check_temp_stat('hitpoints', 1), $u['maxhitpoints'].check_temp_stat('maxhitpoints', 1), explained_get_player_hitpoints()));

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
            addcharstat(\LotgdTranslator::t('statistic.stat.attack', [], 'app-default'), \sprintf("{$atk} `\$<span title='%s'>(?)</span>`0", explained_get_player_attack().check_temp_stat('attack', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.defense', [], 'app-default'), \sprintf("{$def} `\$<span title='%s'>(?)</span>`0", explained_get_player_defense().check_temp_stat('defense', 1)));
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
            addcharstat(\LotgdTranslator::t('statistic.stat.psyche', [], 'app-default'), 10 + \round(($u['level'] - 1) * 1.5));
            addcharstat(\LotgdTranslator::t('statistic.stat.spirit', [], 'app-default'), 10 + \round(($u['level'] - 1) * 1.5));
        }

        addcharstat(\LotgdTranslator::t('statistic.stat.race', [], 'app-default'), \LotgdTranslator::t('character.racename', [], (RACE_UNKNOWN != $u['race']) ? $u['race'] : RACE_UNKNOWN));

        if (\count($companions) > 0)
        {
            addcharstat(\LotgdTranslator::t('statistic.category.companions', [], 'app-default'));

            foreach ($companions as $name => $companion)
            {
                if ($companion['hitpoints'] > 0 || (isset($companion['cannotdie']) && true == $companion['cannotdie']))
                {
                    $companion['hitpoints'] = \max(0, $companion['hitpoints']);

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

                    addcharstat($companion['name'], $color.($companion['hitpoints']).'`7/`&'.($companion['maxhitpoints'])."{$suspcode}`0");
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

        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CHARACTER_STATS);
        modulehook('charstats');

        if ($return)
        {
            $charstat = getcharstats($buffs);

            if ( ! \is_array($session['bufflist']))
            {
                $session['bufflist'] = [];
            }

            return $charstat;
        }

        return;
    }

    if ( ! $ret = \LotgdCache::getItem('charlisthomepage'))
    {
        $onlinecount = 0;
        // If a module wants to do it's own display of the online chars, let it.
        $list = [];
        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CHARACTER_ONLINE_LIST, null, $list);
        $list = modulehook('onlinecharlist', $list);

        if (isset($list['handled']) && $list['handled'])
        {
            $onlinecount = $list['count'];
            $ret         = $list['list'];
        }
        else
        {
            $result      = [];
            $onlinecount = 0;

            if (\Doctrine::isConnected())
            {
                $repository  = \Doctrine::getRepository('LotgdCore:Accounts');
                $result      = $repository->getListAccountsOnline();
                $onlinecount = \count($result);
            }

            $tpl = \LotgdTheme::load('{theme}/_blocks/_partials.html.twig');

            $ret = $tpl->renderBlock('online_list', [
                'list'        => $result,
                'onlineCount' => $onlinecount,
                'textDomain'  => 'page-home',
            ]);
        }

        savesetting('OnlineCount', $onlinecount);
        savesetting('OnlineCountLast', \strtotime('now'));
        \LotgdCache::setItem('charlisthomepage', $ret);
    }

    return $ret;
}
