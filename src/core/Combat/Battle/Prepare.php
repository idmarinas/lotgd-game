<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

use Lotgd\Core\Event\Fight;

trait Prepare
{
    /**
     * This function prepares the fight, sets up options and gives hook a hook to change options on a per-player basis.
     *
     * @param array $options the options given by a module or basics
     */
    protected function prepareFight(): void
    {
        $basicoptions = [
            'maxattacks' => $this->settings->getSetting('maxattacks', 4),
        ];

        $fightoptions = new Fight($this->options + $basicoptions);
        $this->dispatcher->dispatch($fightoptions, Fight::OPTIONS);
        $fightoptions = $fightoptions->getData();

        // We'll also reset the companions here...
        $this->prepareCompanions();

        $this->setOptions($fightoptions);
    }

    /**
     * This functions prepares companions to be able to take part in a fight. Uses global copies.
     */
    protected function prepareCompanions(): void
    {
        if (\is_array($this->companions) && ! empty($this->companions))
        {
            foreach ($this->companions as &$companion)
            {
                if ( ! isset($companion['suspended']) || ! $companion['suspended'])
                {
                    $companion['used'] = false;
                }
            }
        }
    }
}
