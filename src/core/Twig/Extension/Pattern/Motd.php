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
        global $session;

        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_buttons.html.twig");

        return $template->renderBlock('message_of_the_day', ['user' => ['needtoviewmotd' => $session['needtoviewmotd']]]);
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
        $motd   = array_merge($sub, $motd);
        $params = array_merge(['motd' => $motd], $params);

        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_motd.html.twig");

        if ($motd['motdtype'])
        {
            $params['motd'] = $this->getMotdRepository()->appendPollResults($motd, $session['user']['acctid'] ?? null);

            return $template->renderBlock('motd_item_poll', $params);
        }

        return $template->renderBlock('motd_item_item', $params);
    }
}
