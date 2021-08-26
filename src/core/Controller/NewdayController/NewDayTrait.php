<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Controller\NewdayController;

use Lotgd\Core\Combat\Battle;
use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Event\Core;
use Lotgd\Core\Event\Other;
use Lotgd\Core\Tool\Sanitize;
use Lotgd\Core\Tool\Tool;

trait NewDayTrait
{
    private $sanitize;
    private $tool;
    private $buffer;
    private $battle;

    /**
     * @required
     */
    public function setSanitize(Sanitize $sanitize): self
    {
        $this->sanitize = $sanitize;

        return $this;
    }

    /**
     * @required
     */
    public function setTool(Tool $tool): self
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * @required
     */
    public function setBuffer(Buffer $buffer): self
    {
        $this->buffer = $buffer;

        return $this;
    }

    /**
     * @required
     */
    public function setBattle(Battle $battle): self
    {
        $this->battle = $battle;

        return $this;
    }

    protected function newDay(array &$params, $resurrection)
    {
        global $session;

        $this->response->pageStart('title.default', [], $this->getTranslationDomain());

        $params['tpl']                 = 'default';
        $params['moduleStaminaSystem'] = is_module_active('staminasystem');
        $params['maxGoldForInterest']  = $this->settings->getSetting('maxgoldforinterest', 100000);

        $params['resurrected'] = false;

        if ( ! $session['user']['alive'])
        {
            $params['resurrected'] = true;
            ++$session['user']['resurrections'];
            $session['user']['alive'] = true;
        }

        ++$session['user']['age'];
        $session['user']['seenmaster'] = 0;

        $turnstoday = "Base: {$params['turns_per_day']}";
        $args       = new Core(['resurrection' => $resurrection, 'turnstoday' => $turnstoday]);
        $this->dispatcher->dispatch($args, Core::NEWDAY_PRE);
        $args       = modulehook('pre-newday', $args->getData());
        $turnstoday = $args['turnstoday'];

        $interestrate = e_rand($params['min_interest'] * 100, $params['max_interest'] * 100) / (float) 100;

        $canGetInterest = ($session['user']['turns'] > $this->settings->getSetting('fightsforinterest', 4) && $session['user']['goldinbank'] >= 0);
        if ($params['moduleStaminaSystem'])
        {
            require_once 'modules/staminasystem/lib/lib.php';
            $stamina        = get_stamina(3);
            $canGetInterest = ($stamina <= 40);
        }

        $params['canGetInterest'] = $canGetInterest;
        $params['maxInterest']    = false;

        if ($canGetInterest)
        {
            $interestrate = 1;
        }
        elseif ($params['maxGoldForInterest'] && $session['user']['goldinbank'] >= $params['maxGoldForInterest'])
        {
            $interestrate          = 1;
            $params['maxInterest'] = true;
        }

        $params['interestRate'] = $interestrate;

        //clear all standard buffs
        $tempbuf = $session['user']['bufflist'] ?? [];

        $session['user']['bufflist'] = [];
        $this->buffer->stripAllBuffs();

        $params['buffMessages'] = [];

        foreach ($tempbuf as $key => $val)
        {
            if (\array_key_exists('survivenewday', $val) && 1 == $val['survivenewday'])
            {
                $this->buffer->applyBuff($key, $val);

                if (\array_key_exists('newdaymessage', $val) && $val['newdaymessage'])
                {
                    $params['buffMessages'][] = $val['newdaymessage'];
                }
            }
        }

        $dkff = 0;

        $params['forestTurnDragonKill'] = $dkff;

        if ( ! $params['moduleStaminaSystem'])
        {
            foreach ($session['user']['dragonpoints'] as $key => $val)
            {
                if ('ff' == $val)
                {
                    ++$dkff;
                }
            }

            $params['forestTurnDragonKill'] = $dkff;
        }

        if ($session['user']['hashorse'])
        {
            $buff = $playermount['mountbuff'];

            if ( ! isset($buff['schema']) || '' == $buff['schema'])
            {
                $buff['schema'] = 'mounts';
            }
            $this->buffer->applyBuff('mount', $buff);
        }

        $r1                = mt_rand(-1, 1);
        $r2                = mt_rand(-1, 1);
        $spirits           = $r1 + $r2;
        $resurrectionturns = $spirits;

        if ('true' == $resurrection)
        {
            $this->tool->addNews('news.resurrected', [
                'playerName'    => $session['user']['name'],
                'deathOverlord' => $this->settings->getSetting('deathoverlord', '`$Ramius`0'),
            ], $this->getTranslationDomain());

            $spirits           = -6;
            $resurrectionturns = $this->settings->getSetting('resurrectionturns', -6);

            if (strstr($resurrectionturns, '%'))
            {
                $resurrectionturns = strtok($resurrectionturns, '%');
                $resurrectionturns = (int) $resurrectionturns;

                if ($resurrectionturns < -100)
                {
                    $resurrectionturns = -100;
                }
                $resurrectionturns = round(($params['turns_per_day'] + $dkff) * ($resurrectionturns / 100), 0);
            }
            else
            {
                $resurrectionturns = -($params['turns_per_day'] + $dkff);
            }

            $session['user']['deathpower'] -= $this->settings->getSetting('resurrectioncost', 100);
            $session['user']['restorepage'] = 'village.php?c=1';
        }

        $params['spirits'] = [
            (-6) => 'spirits.00',
            (-2) => 'spirits.01',
            (-1) => 'spirits.02',
            (0)  => 'spirits.03',
            1    => 'spirits.04',
            2    => 'spirits.05',
        ];

        $params['spirit']            = $spirits;
        $params['resurrectionTurns'] = $resurrectionturns;

        $rp = $session['user']['restorepage'];
        $x  = max(strrpos('&', $rp), strrpos('?', $rp));

        if ($x > 0)
        {
            $rp = substr($rp, 0, $x);
        }

        $rp = ('badnav.php' == substr($rp, 0, 10) || '' == $rp) ? 'news.php' : $rp;
        $this->navigation->addNav('nav.continue', $this->sanitize->cmdSanitize($rp));

        $session['user']['laston']     = new \DateTime('now');
        $bgold                         = $session['user']['goldinbank'];
        $session['user']['goldinbank'] = round($session['user']['goldinbank'] * $interestrate);
        $nbgold                        = $session['user']['goldinbank'] - $bgold;

        if (0 != $nbgold)
        {
            $this->log->debug(($nbgold >= 0 ? 'earned ' : 'paid ').abs($nbgold).' gold in interest');
        }
        $turnstoday .= ", Spirits: {$resurrectionturns}, DK: {$dkff}";
        $session['user']['turns']     = $params['turns_per_day'] + $resurrectionturns + $dkff;
        $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
        $session['user']['spirits']   = $spirits;

        if ('true' != $resurrection)
        {
            $session['user']['playerfights'] = $params['daily_pvp_fights'];
        }
        $session['user']['transferredtoday'] = 0;
        $session['user']['amountouttoday']   = 0;
        $session['user']['seendragon']       = 0;
        $session['user']['seenmaster']       = 0;
        $session['user']['fedmount']         = 0;

        if ('true' != $resurrection)
        {
            $session['user']['soulpoints']  = 50 + 10 * $session['user']['level'] + $session['user']['dragonkills'] * 2;
            $session['user']['gravefights'] = $this->settings->getSetting('gravefightsperday', 10);
        }
        $session['user']['boughtroomtoday'] = 0;
        $session['user']['recentcomments']  = $session['user']['lasthit'];
        $session['user']['lasthit']         = new \DateTime('now');

        if ($session['user']['hashorse'])
        {
            $params['mountName'] = $playermount['mountname'] ?? '';

            $params['mountMessage'] = $this->tool->substituteArray('`n`&'.$playermount['newday'].'`0`n');

            $mff = (int) $playermount['mountforestfights'];
            $session['user']['turns'] += $mff;
            $turnstoday .= ", Mount: {$mff}";

            $params['mountTurns'] = $mff;
        }

        $params['haunted'] = false;

        if ($session['user']['hauntedby'] > '')
        {
            $params['haunted'] = $session['user']['hauntedby'];

            if ($params['moduleStaminaSystem'])
            {
                require_once 'modules/staminasystem/lib/lib.php';

                removestamina(25000);
                $turnstoday .= ', Haunted: Stamina reduction';
            }
            else
            {
                --$session['user']['turns'];
                $turnstoday .= ', Haunted: -1';
            }

            $session['user']['hauntedby'] = '';
        }

        $this->battle->unSuspendCompanions('allowinshades');

        //-- Run new day if not run by cronjob
        if ( ! $this->settings->getSetting('newdaycron', 0))
        {
            //check last time we did this vs now to see if it was a different game day.
            $lastnewdaysemaphore = $this->dateTime->convertGameTime(strtotime($this->settings->getSetting('newdaySemaphore', '0000-00-00 00:00:00').' +0000'));
            $gametoday           = $this->dateTime->gameTime();

            if (gmdate('Ymd', $gametoday) != gmdate('Ymd', $lastnewdaysemaphore))
            {
                // it appears to be a different game day, acquire semaphore and
                // check again.
                $this->settings->clearSettings();
                $lastnewdaysemaphore = $this->dateTime->convertGameTime(strtotime($this->settings->getSetting('newdaySemaphore', '0000-00-00 00:00:00').' +0000'));
                $gametoday           = $this->dateTime->gameTime();

                if (gmdate('Ymd', $gametoday) != gmdate('Ymd', $lastnewdaysemaphore))
                {
                    //we need to run the hook, update the setting, and unlock.
                    $this->settings->saveSetting('newdaySemaphore', gmdate('Y-m-d H:i:s'));

                    require 'lib/newday/newday_runonce.php';
                }
            }
        }

        $args = new Core([
            'resurrection'         => $resurrection,
            'turnstoday'           => $turnstoday,
            'includeTemplatesPre'  => $params['includeTemplatesPre'],
            'includeTemplatesPost' => $params['includeTemplatesPost'],
        ]);
        $this->dispatcher->dispatch($args, Core::NEWDAY);
        $args = modulehook('newday', $args->getData());

        $turnstoday                     = $args['turnstoday'];
        $params['includeTemplatesPre']  = $args['includeTemplatesPre'];
        $params['includeTemplatesPost'] = $args['includeTemplatesPost'];

        //## Process stamina for spirit
        $args = new Other(['spirits' => $spirits]);
        $this->dispatcher->dispatch($args, Other::STAMINA_NEWDAY);
        modulehook('stamina-newday', $args->getData());

        $this->log->debug("New Day Turns: {$turnstoday}");

        $session['user']['sentnotice'] = 0;
    }
}
