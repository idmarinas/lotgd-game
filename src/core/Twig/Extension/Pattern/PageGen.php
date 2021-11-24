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

        $avg = 0;

        if (($session['user']['gentimecount'] ?? 0) !== 0)
        {
        $avg = ($session['user']['gentime'] / $session['user']['gentimecount']);
        }

        return $env->load('_blocks/_partials.html.twig')->renderBlock('game_page_gen', [
            'genTime'          => Debugger::timer('page-generating'),
            'avg'              => $avg,
            'userGenTime'      => $session['user']['gentime'],
            'userGenTimeCount' => $session['user']['gentimecount'],
        ]);
    }
}
