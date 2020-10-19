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

        \trigger_error(\sprintf(
            'Usage of %s (message_of_the_day() Twig function) is obsolete since 4.5.0; and delete in version 5.0.0, use "{% block message_of_the_day parent() %}" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $newMotd = $session['needtoviewmotd'];

        return sprintf(
            '<a id="motd-button" class="ui tertiary basic button motd" onclick="JaxonLotgd.Ajax.Core.Motd.list(); $(this).addClass(\'loading disabled\');">%s %s</a>',
            $newMotd ? '<i aria-hidden="true" class="certificate icon"></i>' : '',
            $this->getTranslator()->trans('parts.motd.title', [], 'app-default')
        );
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

        if ($motd['motdtype'])
        {
            $params['motd'] = $this->getMotdRepository()->appendPollResults($motd, $session['user']['acctid'] ?? null);

            return \LotgdTheme::renderThemeTemplate('page/motd/parts/poll.twig', $params);
        }

        return \LotgdTheme::renderThemeTemplate('page/motd/parts/item.twig', $params);
    }
}
