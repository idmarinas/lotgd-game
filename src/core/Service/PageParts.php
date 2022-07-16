<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Service;

use Lotgd\Core\Doctrine\ORM\EntityManager;
use Lotgd\Core\Event\Character;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Character\Stats;
use Lotgd\Core\Combat\TempStat;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Output\Format;
use Lotgd\Core\Tool\PlayerFunction;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PageParts
{
    private $stats;
    private $twig;
    private $format;
    private $playerFunction;
    private $settings;
    private $dispatcher;
    private $translator;
    private $tempStat;
    private EntityManager $doctrine;
    private $cache;

    public function __construct(
        Stats $stats,
        Environment $twig,
        Format $format,
        PlayerFunction $playerFunction,
        Settings $settings,
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator
    ) {
        $this->stats          = $stats;
        $this->twig           = $twig;
        $this->format         = $format;
        $this->playerFunction = $playerFunction;
        $this->settings       = $settings;
        $this->dispatcher     = $dispatcher;
        $this->translator     = $translator;
    }

    /**
     * Returns output formatted character stats.
     */
    public function getCharStats(array $buffs): string
    {
        //returns output formatted character statistics.
        $charstatInfo = $this->stats->getStats();
        $charstattpl  = [];

        foreach ($charstatInfo as $label => $section)
        {
            if ( ! empty($section))
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

        $template = $this->twig->load('_blocks/_character_stats.html.twig');

        $statbuff = $template->renderBlock('character_stat_buff', [
            'value' => $buffs,
        ]);

        return $this->format->colorize($template->renderBlock('character_stats', [
            'charstat' => $charstattpl,
            'statbuff' => $statbuff,
        ]));
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
    public function charStats($return = true)
    {
        global $session, $playermount, $companions;

        $this->stats->wipeStats();

        if ($session['loggedin'] ?? false)
        {
            $u = &$session['user'];

            $u['hitpoints']  = round($u['hitpoints'], 0);
            $u['experience'] = round($u['experience'], 0);
            $spirits         = [-6 => 'Resurrected', -2 => 'Very Low', -1 => 'Low', '0' => 'Normal', 1 => 'High', 2 => 'Very High'];

            if ( ! $u['alive'])
            {
                $spirits[(int) $u['spirits']] = 'DEAD';
            }

            $oAtk      = $atk      = $this->playerFunction->getPlayerAttack(); //Original Attack
            $oDef      = $def      = $this->playerFunction->getPlayerDefense(); //Original Defense
            $spd       = $this->playerFunction->getPlayerSpeed();
            $hitpoints = $this->playerFunction->getPlayerHitpoints(); //Health of character

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

                $val['name'] ??= '';

                // Short circuit if the name is blank
                if ($val['name'] > '' || $session['user']['superuser'] & SU_DEBUG_OUTPUT)
                {
                    //removed due to performance reasons. foreach is better with only $val than to have $key ONLY for the short happiness of one debug. much greater performance gain here
                    if (\is_array($val['name']))
                    {
                        $val['name'][0] = str_replace('`%', '`%%', $val['name'][0]);
                        $val['name']    = \call_user_func_array('sprintf', $val['name']);
                    }

                    $val['rounds'] ??= 0;

                    if ($val['rounds'] >= 0)
                    {
                        // We're about to sprintf, so, let's makes sure that
                        // `% is handled.
                        $b       = '`#%s `7(%s rounds left)`0`n';
                        $b       = sprintf($b, $val['name'], $val['rounds']);
                        $buffs[] = $this->format->colorize($b);
                    }
                    elseif ($val['name'])
                    {
                        $buffs[] = $this->format->colorize("`#{$val['name']}`0`n");
                    }
                    else
                    {
                        $buffs[] = $this->format->colorize("<code>`7{$val['schema']}`0</code>`n");
                    }
                }
            }

            if (empty($buffs))
            {
                $buffs[] = $this->format->colorize('`^None`0');
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

            $this->stats->addcharstat($this->translator->trans('statistic.category.character.info', [], 'app_default'));
            $this->stats->addcharstat($this->translator->trans('statistic.stat.name', [], 'app_default'), $u['name']);
            $this->stats->addcharstat($this->translator->trans('statistic.stat.dragonkills', [], 'app_default'), '`b'.$u['dragonkills'].'´b');
            $this->stats->addcharstat($this->translator->trans('statistic.stat.level', [], 'app_default'), '`b'.$u['level'].$this->tempStat->checkTempStat('level', 1).'´b');

            if ($u['alive'])
            {
                //-- HitPoints are calculated in base to attributes
                $this->stats->addcharstat($this->translator->trans('statistic.stat.hitpoints', [], 'app_default'), sprintf('%s/%s `$<span title="%s">(?)</span>`0', $u['hitpoints'].$this->tempStat->checkTempStat('hitpoints', 1), $u['maxhitpoints'].$this->tempStat->checkTempStat('maxhitpoints', 1), $this->playerFunction->explainedGetPlayerHitpoints()));

                $this->stats->addcharstat($this->translator->trans('statistic.stat.turns', [], 'app_default'), $u['turns'].$this->tempStat->checkTempStat('turns', 1));

                $this->stats->addcharstat($this->translator->trans('statistic.stat.experience', [], 'app_default'), $this->format->numeral($u['experience'].$this->tempStat->checkTempStat('experience', 1)));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.attack', [], 'app_default'), sprintf("{$atk} `\$<span title='%s'>(?)</span>`0", $this->playerFunction->explainedGetPlayerAttack().$this->tempStat->checkTempStat('attack', 1)));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.defense', [], 'app_default'), sprintf("{$def} `\$<span title='%s'>(?)</span>`0", $this->playerFunction->explainedGetPlayerDefense().$this->tempStat->checkTempStat('defense', 1)));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.speed', [], 'app_default'), $spd.$this->tempStat->checkTempStat('speed', 1));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.strength', [], 'app_default'), $u['strength'].$this->tempStat->checkTempStat('strength', 1));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.dexterity', [], 'app_default'), $u['dexterity'].$this->tempStat->checkTempStat('dexterity', 1));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.intelligence', [], 'app_default'), $u['intelligence'].$this->tempStat->checkTempStat('intelligence', 1));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.constitution', [], 'app_default'), $u['constitution'].$this->tempStat->checkTempStat('constitution', 1));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.wisdom', [], 'app_default'), $u['wisdom'].$this->tempStat->checkTempStat('wisdom', 1));
            }
            else
            {
                $maxsoul = 50 + 10 * $u['level'] + $u['dragonkills'] * 2;
                $this->stats->addcharstat($this->translator->trans('statistic.stat.soulpoints', [], 'app_default'), $u['soulpoints'].$this->tempStat->checkTempStat('soulpoints', 1).'`0/'.$maxsoul);

                $this->stats->addcharstat($this->translator->trans('statistic.stat.torments', [], 'app_default'), $u['gravefights'].$this->tempStat->checkTempStat('gravefights', 1));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.psyche', [], 'app_default'), 10 + round(($u['level'] - 1) * 1.5));
                $this->stats->addcharstat($this->translator->trans('statistic.stat.spirit', [], 'app_default'), 10 + round(($u['level'] - 1) * 1.5));
            }

            $this->stats->addcharstat($this->translator->trans('statistic.stat.race', [], 'app_default'), $this->translator->trans('character.racename', [], (RACE_UNKNOWN != $u['race']) ? $u['race'] : RACE_UNKNOWN));

            if (\count($companions) > 0)
            {
                $this->stats->addcharstat($this->translator->trans('statistic.category.companions', [], 'app_default'));

                foreach ($companions as $companion)
                {
                    if ($companion['hitpoints'] > 0 || (isset($companion['cannotdie']) && $companion['cannotdie']))
                    {
                        $companion['hitpoints'] = max(0, $companion['hitpoints']);

                        $color = '`@';

                        if ($companion['hitpoints'] < $companion['maxhitpoints'])
                        {
                            $color = '`$';
                        }

                        $suspcode = '';

                        if (isset($companion['suspended']) && $companion['suspended'])
                        {
                            $suspcode = '`7 *';
                        }

                        $this->stats->addcharstat($companion['name'], $color.($companion['hitpoints']).'`7/`&'.($companion['maxhitpoints'])."{$suspcode}`0");
                    }
                }
            }
            $this->stats->addcharstat($this->translator->trans('statistic.category.character.personal', [], 'app_default'));

            if ($u['alive'])
            {
                $this->stats->addcharstat($this->translator->trans('statistic.stat.pvp', [], 'app_default'), $u['playerfights']);
            }
            else
            {
                $this->stats->addcharstat($this->translator->trans('statistic.stat.favor', [], 'app_default'), $u['deathpower'].$this->tempStat->checkTempStat('deathpower', 1));
            }

            $this->stats->addcharstat($this->translator->trans('statistic.stat.spirits', [], 'app_default'), '`b'.$spirits[(int) $u['spirits']].'´b');
            $this->stats->addcharstat($this->translator->trans('statistic.stat.gold', [], 'app_default'), $this->format->numeral($u['gold'].$this->tempStat->checkTempStat('gold', 1)));
            $this->stats->addcharstat($this->translator->trans('statistic.stat.gems', [], 'app_default'), $this->format->numeral($u['gems'].$this->tempStat->checkTempStat('gems', 1)));

            $this->stats->addcharstat($this->translator->trans('statistic.category.character.equip', [], 'app_default'));

            $this->stats->addcharstat($this->translator->trans('statistic.stat.weapon', [], 'app_default'), $u['weapon']);
            $this->stats->addcharstat($this->translator->trans('statistic.stat.armor', [], 'app_default'), $u['armor']);

            if ($u['hashorse'])
            {
                $this->stats->addcharstat($this->translator->trans('statistic.stat.creature', [], 'app_default'), $playermount['mountname'].'`0');
            }

            $this->dispatcher->dispatch(new Character(), Character::STATS);

            if ($return)
            {
                $charstat = $this->getCharStats($buffs);

                if ( ! \is_array($session['bufflist']))
                {
                    $session['bufflist'] = [];
                }

                return $charstat;
            }

            return;
        }

        return $this->cache->get('char-list-home-page', function ($item)
        {
            $item->expiresAfter(600);

            $onlinecount = 0;
            // If a module wants to do it's own display of the online chars, let it.
            $list = new Character();
            $this->dispatcher->dispatch($list, Character::ONLINE_LIST);
            $list = $list->getData();

            if (isset($list['handled']) && $list['handled'])
            {
                $onlinecount = $list['count'];
                $ret = $list['list'];
            }
            else
            {
                $result = [];
                $onlinecount = 0;

                if ($this->doctrine->isConnected())
                {
                    /** @var \Lotgd\Core\Repository\UserRepository $repository */
                    $repository = $this->doctrine->getRepository('LotgdCore:User');
                    $onlinecount = $repository->count(['loggedin' => '1', 'locked' => '0']);
                }

                $tpl = $this->twig->load('_blocks/_partials.html.twig');
                $ret = $tpl->renderBlock('online_list', [
                    'list'        => $result,
                    'onlineCount' => $onlinecount,
                    'textDomain'  => 'page_home',
                ]);
            }

            $this->settings->saveSetting('OnlineCount', $onlinecount);
            $this->settings->saveSetting('OnlineCountLast', strtotime('now'));

            return $ret;
        });
    }

    /**
     * @required
     */
    public function setTempStat(TempStat $tempStat): self
    {
        $this->tempStat = $tempStat;

        return $this;
    }

    /**
     * @required
     */
    public function setDoctrine(EntityManagerInterface $doctrine): self
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    /**
     * @required
     */
    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }
}
