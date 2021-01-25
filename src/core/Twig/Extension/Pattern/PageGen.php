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
use Twig\Environment;

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
    public function gamePageGen(Environment $env)
    {
        global $session;

        return $env->load('{theme}/_blocks/_partials.html.twig')->renderBlock('game_page_gen', [
            'genTime'          => Debugger::timer('page-generating'),
            'avg'              => ($session['user']['gentime'] / $session['user']['gentimecount']),
            'userGenTime'      => $session['user']['gentime'],
            'userGenTimeCount' => $session['user']['gentimecount'],
        ]);
    }
}
