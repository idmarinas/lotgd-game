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

use Laminas\Math\Rand;

trait Surprise
{
    private $surprised = false;

    /**
     * Check if player is surprised.
     */
    protected function isSurprised(): void
    {
        $op = (string) $this->request->query->get('op', '');

        if ('run' != $op && 'fight' != $op && 'newtarget' != $op)
        {
            if ($this->getEnemiesCount() > 1)
            {
                $this->surprised = true;
                $this->addContextToRoundEnemy(['combat.start.surprised.multiple', [], $this->getTranslationDomain()]);

                return;
            }

            // Let's try this instead.Biggest change is that it adds possibility of
            // being surprised to all fights.
            if ( ! \array_key_exists('surprised', $this->options) || ! $this->options['surprised'])
            {
                // By default, surprise is 50/50
                $this->surprised = Rand::getBoolean();

                $this->surprisedAdjust();
            }
        }
    }

    private function surprisedAdjust(): void
    {
        // Now, adjust for slum/thrill
        $type = $this->request->query->get('type');

        if ('slum' == $type || 'thrill' == $type)
        {
            $num             = mt_rand(0, 2);
            $this->surprised = true;

            if (
                ('slum' == $type && 2 != $num)
                || (('thrill' == $type || 'suicide' == $type) && 2 == $num)
            ) {
                $this->surprised = false;
            }
        }

        if ( ! $this->surprised)
        {
            $this->addContextToRoundAlly(['combat.start.surprised.no', [], $this->getTranslationDomain()]);
        }
        else
        {
            $pvPve = 'pvp' == $this->getOptionType();

            $this->addContextToRoundEnemy(['combat.start.surprised.'.($pvPve ? 'pvp' : 'pve'), [
                ($pvPve ? 'player' : 'creatureName') => $this->enemies[0]['creaturename'],
            ], $this->getTranslationDomain()]);
        }

        $this->setOption('surprised', true);
        $this->optionsBattleActive();
    }
}
