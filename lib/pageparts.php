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
    $stats = \LotgdKernel::get(Lotgd\Core\Character\Stats::class);

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
    $stats = \LotgdKernel::get(Lotgd\Core\Character\Stats::class);

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
    $stats = \LotgdKernel::get(Lotgd\Core\Character\Stats::class);

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
    $stats = \LotgdKernel::get(Lotgd\Core\Character\Stats::class);

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

    $stats = \LotgdKernel::get(Lotgd\Core\Character\Stats::class);

    $charstatInfo = $stats->getStats();
    $charstattpl  = [];

    foreach ($charstatInfo as $label => $section)
    {
        if (\count($section))
        {
            $arr               = $label;
            $charstattpl[$arr] = [];
            reset($section);

            foreach ($section as $name => $val)
            {
                $a2                     = "`&{$name}`0";
                $charstattpl[$arr][$a2] = "`^{$val}`0";
            }
        }
    }

    $template = \LotgdTheme::load('_blocks/_character_stats.html.twig');

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
        /** @var \Lotgd\Core\Tool\PlayerFunction */
        $playerFunctions = LotgdKernel::get('lotgd_core.tool.player_functions');
        $u               = &$session['user'];

        $u['hitpoints']  = round($u['hitpoints'], 0);
        $u['experience'] = round($u['experience'], 0);
        $spirits         = [-6 => 'Resurrected', -2 => 'Very Low', -1 => 'Low', '0' => 'Normal', 1 => 'High', 2 => 'Very High'];

        if ( ! $u['alive'])
        {
            $spirits[(int) $u['spirits']] = 'DEAD';
        }
        reset($session['bufflist']);

        $oAtk              = $atk              = $playerFunctions->getPlayerAttack(); //Original Attack
        $oDef              = $def              = $playerFunctions->getPlayerDefense(); //Original Defense
        $spd               = $playerFunctions->getPlayerSpeed();
        $hitpoints         = $playerFunctions->getPlayerHitpoints(); //Health of character
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
                if (\is_array($val['name']))
                {
                    $val['name'][0] = str_replace('`%', '`%%', $val['name'][0]);
                    $val['name']    = \call_user_func_array('sprintf', $val['name']);
                }

                $val['rounds'] = $val['rounds'] ?? 0;

                if ($val['rounds'] >= 0)
                {
                    // We're about to sprintf, so, let's makes sure that
                    // `% is handled.
                    $b       = '`#%s `7(%s rounds left)`0`n';
                    $b       = sprintf($b, $val['name'], $val['rounds']);
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
            $buffs[] = \LotgdFormat::colorize('`^None`0', true);
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

        addcharstat(\LotgdTranslator::t('statistic.category.character.info', [], 'app_default'));
        addcharstat(\LotgdTranslator::t('statistic.stat.name', [], 'app_default'), $u['name']);
        addcharstat(\LotgdTranslator::t('statistic.stat.dragonkills', [], 'app_default'), '`b'.$u['dragonkills'].'´b');
        addcharstat(\LotgdTranslator::t('statistic.stat.level', [], 'app_default'), '`b'.$u['level'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('level', 1).'´b');

        if ($u['alive'])
        {
            //-- HitPoints are calculated in base to attributes
            addcharstat(\LotgdTranslator::t('statistic.stat.hitpoints', [], 'app_default'), sprintf('%s/%s `$<span title="%s">(?)</span>`0', $u['hitpoints'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('hitpoints', 1), $u['maxhitpoints'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('maxhitpoints', 1), $playerFunctions->explainedGetPlayerHitpoints()));

            if (is_module_active('staminasystem'))
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.stamina', [], 'app_default'), '');
            }
            else
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.turns', [], 'app_default'), $u['turns'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('turns', 1));
            }

            if (is_module_active('displaycp'))
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.drunkeness', [], 'app_default'), '');
            }
            addcharstat(\LotgdTranslator::t('statistic.stat.experience', [], 'app_default'), LotgdFormat::numeral($u['experience'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('experience', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.attack', [], 'app_default'), sprintf("{$atk} `\$<span title='%s'>(?)</span>`0", $playerFunctions->explainedGetPlayerAttack().\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('attack', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.defense', [], 'app_default'), sprintf("{$def} `\$<span title='%s'>(?)</span>`0", $playerFunctions->explainedGetPlayerDefense().\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('defense', 1)));
            addcharstat(\LotgdTranslator::t('statistic.stat.speed', [], 'app_default'), $spd.\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('speed', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.strength', [], 'app_default'), $u['strength'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('strength', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.dexterity', [], 'app_default'), $u['dexterity'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('dexterity', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.intelligence', [], 'app_default'), $u['intelligence'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('intelligence', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.constitution', [], 'app_default'), $u['constitution'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('constitution', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.wisdom', [], 'app_default'), $u['wisdom'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('wisdom', 1));
        }
        else
        {
            $maxsoul = 50 + 10 * $u['level'] + $u['dragonkills'] * 2;
            addcharstat(\LotgdTranslator::t('statistic.stat.soulpoints', [], 'app_default'), $u['soulpoints'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('soulpoints', 1).'`0/'.$maxsoul);

            if (is_module_active('staminasystem'))
            {
                addcharstat(\LotgdTranslator::t('statistic.stat.stamina', [], 'app_default'), '');
            }
            addcharstat(\LotgdTranslator::t('statistic.stat.torments', [], 'app_default'), $u['gravefights'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('gravefights', 1));
            addcharstat(\LotgdTranslator::t('statistic.stat.psyche', [], 'app_default'), 10 + round(($u['level'] - 1) * 1.5));
            addcharstat(\LotgdTranslator::t('statistic.stat.spirit', [], 'app_default'), 10 + round(($u['level'] - 1) * 1.5));
        }

        addcharstat(\LotgdTranslator::t('statistic.stat.race', [], 'app_default'), \LotgdTranslator::t('character.racename', [], (RACE_UNKNOWN != $u['race']) ? $u['race'] : RACE_UNKNOWN));

        if (\count($companions) > 0)
        {
            addcharstat(\LotgdTranslator::t('statistic.category.companions', [], 'app_default'));

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

                    addcharstat($companion['name'], $color.($companion['hitpoints']).'`7/`&'.($companion['maxhitpoints'])."{$suspcode}`0");
                }
            }
        }
        addcharstat(\LotgdTranslator::t('statistic.category.character.personal', [], 'app_default'));

        if ($u['alive'])
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.pvp', [], 'app_default'), $u['playerfights']);
        }
        else
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.favor', [], 'app_default'), $u['deathpower'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('deathpower', 1));
        }

        addcharstat(\LotgdTranslator::t('statistic.stat.spirits', [], 'app_default'), '`b'.$spirits[(int) $u['spirits']].'´b');
        addcharstat(\LotgdTranslator::t('statistic.stat.gold', [], 'app_default'), \LotgdFormat::numeral($u['gold'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('gold', 1)));
        addcharstat(\LotgdTranslator::t('statistic.stat.gems', [], 'app_default'), \LotgdFormat::numeral($u['gems'].\LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat('gems', 1)));

        addcharstat(\LotgdTranslator::t('statistic.category.character.equip', [], 'app_default'));

        if (is_module_active('inventorypopup'))
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.inventory', [], 'app_default'), '');
        }
        addcharstat(\LotgdTranslator::t('statistic.stat.weapon', [], 'app_default'), $u['weapon']);
        addcharstat(\LotgdTranslator::t('statistic.stat.armor', [], 'app_default'), $u['armor']);

        if ($u['hashorse'])
        {
            addcharstat(\LotgdTranslator::t('statistic.stat.creature', [], 'app_default'), $playermount['mountname'].'`0');
        }

        \LotgdEventDispatcher::dispatch(new \Lotgd\Core\Event\Character(), \Lotgd\Core\Event\Character::STATS);
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

    $cache = \LotgdKernel::get('cache.app');

    return $cache->get('char-list-home-page', function ($item)
    {
        $item->expiresAfter(600);

        $onlinecount = 0;
        // If a module wants to do it's own display of the online chars, let it.
        $list = new \Lotgd\Core\Event\Character();
        \LotgdEventDispatcher::dispatch($list, \Lotgd\Core\Event\Character::ONLINE_LIST);
        $list = modulehook('onlinecharlist', $list->getData());

        if (isset($list['handled']) && $list['handled'])
        {
            $onlinecount = $list['count'];
            $ret = $list['list'];
        }
        else
        {
            $result = [];
            $onlinecount = 0;

            if (\Doctrine::isConnected())
            {
                $repository = \Doctrine::getRepository('LotgdCore:User');
                $onlinecount = $repository->count(['loggedin' => '1', 'locked' => '0']);
            }

            $tpl = \LotgdTheme::load('_blocks/_partials.html.twig');
            $ret = $tpl->renderBlock('online_list', [
                'list'        => $result,
                'onlineCount' => $onlinecount,
                'textDomain'  => 'page_home',
            ]);
        }

        LotgdSetting::saveSetting('OnlineCount', $onlinecount);
        LotgdSetting::saveSetting('OnlineCountLast', strtotime('now'));

        return $ret;
    });
}
