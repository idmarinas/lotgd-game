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

use Tracy\Debugger;

/**
 * Trait to generate page gen time.
 */
trait PageGen
{
    /**
     * Get page gen time.
     *
     * @return string
     */
    public function gamePageGen()
    {
        global $session;

        return $this->getTemplate()->renderBlock('game_page_gen', "@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_partials.html.twig", [
            'genTime'          => Debugger::timer('page-generating'),
            'avg'              => ($session['user']['gentime'] / $session['user']['gentimecount']),
            'userGenTime'      => $session['user']['gentime'],
            'userGenTimeCount' => $session['user']['gentimecount'],
        ]);
    }
}
