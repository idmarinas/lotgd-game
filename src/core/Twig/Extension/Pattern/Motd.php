<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

/**
 * Trait to message of the day.
 */
trait Motd
{
    /**
     * Get message of the day link.
     */
    public function messageOfTheDay(): string
    {
        return $this->getTemplate()->renderBlock('message_of_the_day', "@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_buttons.html.twig", []);
    }

    /**
     * Display MoTD item or poll.
     *
     * @param array $params Extra params
     */
    public function display(array $motd, array $params = []): string
    {
        global $session;

        //-- Merge data
        $sub = $motd[0];
        unset($motd[0]);
        $motd   = \array_merge($sub, $motd);
        $params = \array_merge(['motd' => $motd], $params);

        $blockName = 'motd_item_item';

        if ($motd['motdtype'])
        {
            $blockName      = 'motd_item_poll';
            $params['motd'] = $this->getMotdRepository()->appendPollResults($motd, $session['user']['acctid'] ?? null);
        }

        return $this->getTemplate()->renderBlock($blockName, "@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_motd.html.twig", $params);
    }
}
