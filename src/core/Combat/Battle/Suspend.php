<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Suspend
{
    /**
     * Suspend buffs if condition is true.
     *
     * @param string $susp The type of suspension
     * @param string $msg  the message to be displayed upon suspending
     */
    public function suspendBuffs($susp = '', $msg = ''): void
    {
        $notify = false;

        foreach ($this->userBuffs as &$buff)
        {
            if (\array_key_exists('suspended', $buff) && $buff['suspended'])
            {
                continue;
            }

            // Suspend non pvp allowed buffs when in pvp
            if ($susp && ( ! isset($buff[$susp]) || ! $buff[$susp]))
            {
                $buff['suspended'] = 1;
                $notify            = true;
            }

            // reset the 'used this round state'
            $buff['used'] = 0;
        }

        if ($notify)
        {
            $msg = $msg ?: 'skill.buffs.gods.suspended';

            $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
        }
    }

    public function unsuspendBuffs($susp = false, $msg = null)
    {
        $unsuspendnotify = 0;

        foreach ($this->userBuffs as &$buff)
        {
            if (\array_key_exists('expireafterfight', $buff) && $buff['expireafterfight'])
            {
                unset($buff);
            }
            elseif (\array_key_exists('suspended', $buff) && $buff['suspended'] && $susp && ( ! \array_key_exists($susp, $buff) || ! $buff[$susp]))
            {
                $buff['suspended'] = 0;
                $unsuspendnotify   = 1;
            }
        }

        if ($unsuspendnotify)
        {
            $msg = $msg ?: 'skill.buffs.gods.restored';

            $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
        }
    }

    public function suspendBuffByName($name, $msg = '')
    {
        // If it's not already suspended.
        if ($this->userBuffs[$name] && ! $this->userBuffs[$name]['suspended'])
        {
            $this->userBuffs[$name]['suspended'] = 1;

            // And notify.
            $msg = $msg ?: 'skill.buffs.gods.suspended';

            $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
        }
    }

    public function unsuspendBuffByName($name, $msg = false)
    {
        // If it's not already suspended.
        if ($this->userBuffs[$name] && $this->userBuffs[$name]['suspended'])
        {
            $this->userBuffs[$name]['suspended'] = 0;

            // And notify.
            $msg = $msg ?: 'skill.buffs.gods.restored';

            $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
        }
    }

    /**
     * Suspends companions on a given parameter.
     *
     * @param string $susp The type of suspension
     * @param string $msg  the message to be displayed upon suspending
     */
    public function suspendCompanions($susp = '', $msg = ''): void
    {
        $suspended = false;

        if (\is_array($this->companions))
        {
            foreach ($this->companions as &$companion)
            {
                if (\array_key_exists('suspended', $companion) && $companion['suspended'])
                {
                    continue;
                }

                if ($susp && ( ! isset($companion[$susp]) || ! $companion[$susp]))
                {
                    $suspended              = true;
                    $companion['suspended'] = true;
                }
            }
        }

        if ($suspended)
        {
            $msg = $msg ?: 'skill.companion.suspended';
            $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
        }
    }

    /**
     * Enables suspended companions.
     *
     * @param string      $susp  The type of suspension
     * @param string|null $nomsg The message to be displayed upon unsuspending. If null, no message will be displayed.
     */
    public function unSuspendCompanions($susp, $nomsg = null)
    {
        $notify = false;

        foreach ($this->companions as &$companion)
        {
            if (isset($companion['suspended']) && $companion['suspended'])
            {
                $notify                 = true;
                $companion['suspended'] = false;
            }
        }

        if ($notify && false !== $nomsg)
        {
            $nomsg = $nomsg ?: 'skill.companion.restored';

            if ($nomsg !== '' && $nomsg !== '0')
            {
                $this->addContextToRoundAlly($nomsg);
            }
        }
    }
}
