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
     *
     * @return string
     */
    public function messageOfTheDay(): string
    {
        global $session;

        return \LotgdTheme::renderThemeTemplate('parts/motd.twig', ['newMotd' => $session['needtoviewmotd']]);
    }

    /**
     * Display MoTD item or poll.
     *
     * @param array $motd
     * @param array $params Extra params
     *
     * @return string
     */
    public function display(array $motd, array $params = []): string
    {
        global $session;

        //-- Merge data
        $sub = $motd[0];
        unset($motd[0]);
        $motd = array_merge($sub, $motd);
        $params = array_merge(['motd' => $motd], $params);

        if ($motd['motdtype'])
        {
            $params['motd'] = $this->repository->appendPollResults($motd, $session['user']['acctid'] ?? null);

            return \LotgdTheme::renderThemeTemplate('page/motd/parts/poll.twig', $params);
        }

        return \LotgdTheme::renderThemeTemplate('page/motd/parts/item.twig', $params);
    }
}
