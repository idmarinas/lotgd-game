<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Combat\Battle;

use Lotgd\Core\Event\Fight;

trait Skill
{
    public function isBuffActive($name)
    {
        global $session;
        // If it's not already suspended.
        return ($session['bufflist'][$name] && ! $session['bufflist'][$name]['suspended']) ? 1 : 0;
    }

    public function applyBodyguard($level): void
    {
        global $session;

        if ( ! isset($session['bufflist']['bodyguard']))
        {
            return;
        }

        switch ($level)
        {
            default:
            case 1:
                $badguyatkmod = 1.05;
                $defmod       = 0.95;
                $rounds       = -1;

            break;

            case 2:
                $badguyatkmod = 1.1;
                $defmod       = 0.9;
                $rounds       = -1;

            break;

            case 3:
                $badguyatkmod = 1.2;
                $defmod       = 0.8;
                $rounds       = -1;

            break;

            case 4:
                $badguyatkmod = 1.3;
                $defmod       = 0.7;
                $rounds       = -1;

            break;

            case 5:
                $badguyatkmod = 1.4;
                $defmod       = 0.6;
                $rounds       = -1;

            break;
        }

        $this->applyBuff('bodyguard', [
            'startmsg'         => $this->translator->trans('skill.bodyguard.startmsg', [], 'page_battle'),
            'name'             => $this->translator->trans('skill.bodyguard.name', [], 'page_battle'),
            'wearoff'          => $this->translator->trans('skill.bodyguard.wearoff', [], 'page_battle'),
            'badguyatkmod'     => $badguyatkmod,
            'defmod'           => $defmod,
            'rounds'           => $rounds,
            'allowinpvp'       => 1,
            'expireafterfight' => 1,
            'schema'           => 'pvp',
        ]);
    }

    public function applySkill($skill)
    {
        if ('godmode' == $skill)
        {
            $this->applyBuff('godmode', [
                'name'         => $this->translator->trans('skill.godmode.name', [], 'page_battle'),
                'rounds'       => 1,
                'wearoff'      => $this->translator->trans('skill.godmode.wearoff', [], 'page_battle'),
                'atkmod'       => 25,
                'defmod'       => 25,
                'invulnerable' => 1,
                'startmsg'     => $this->translator->trans('skill.godmode.startmsg', [], 'page_battle'),
                'schema'       => 'skill',
            ]);
        }

        $this->dispatcher->dispatch(new Fight(), Fight::APPLY_SPECIALTY);
    }
}
