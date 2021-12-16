<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

use Twig\Environment;

/**
 * Trait to message of the day.
 */
trait Motd
{
    /**
     * Get message of the day link.
     */
    public function messageOfTheDay(Environment $env): string
    {
        return $env->load('_blocks/_buttons.html.twig')->renderBlock('message_of_the_day', []);
    }

    /**
     * Display MoTD item or poll.
     *
     * @param array $params Extra params
     */
    public function display(Environment $env, array $motd, array $params = []): string
    {
        global $session;

        //-- Merge data
        $sub = $motd[0];
        unset($motd[0]);
        $motd   = \array_merge($sub, $motd);
        $params = \array_merge(['motd' => $motd], $params);

        $template = 'motd/item.html.twig';

        if ($motd['motdtype'])
        {
            $template      = 'motd/poll.html.twig';
            $params['motd'] = $this->getMotdRepository()->appendPollResults($motd, $session['user']['acctid'] ?? null);
        }

        return $env->render($template, $params);
    }
}
