<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Tool;

use Lotgd\Core\Event\Character;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Output\Format;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Staff
{
    private $dispatcher;
    private $response;
    private $navigation;
    private $format;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        Response $response,
        Navigation $navigation,
        Format $format
    ) {
        $this->dispatcher = $dispatcher;
        $this->response   = $response;
        $this->navigation = $navigation;
        $this->format     = $format;
    }

    public function killPlayer($explossproportion = 0.1, $goldlossproportion = 1)
    {
        global $session;

        $args = new Character([
            'explossproportion'  => $explossproportion,
            'goldlossproportion' => $goldlossproportion,
        ]);
        $this->dispatcher->dispatch($args, Character::KILLED_PLAYER);
        $args = modulehook('killedplayer', $args->getData());

        if (isset($args['donotkill']))
        {
            return;
        }

        $exp     = $session['user']['experience'];
        $exploss = round($exp * $args['explossproportion']);

        if ($exploss > $exp)
        {
            $exploss = $exp;
        }

        if ($exploss > 0)
        {
            $session['user']['experience'] -= $exploss;
            $this->response->pageAddContent($this->format->colorize(sprintf('`$You lose %s experience.`n', $exploss), true));
        }

        $gold     = $session['user']['gold'];
        $goldloss = round($gold * $args['goldlossproportion']);

        if ($goldloss > $gold)
        {
            $goldloss = $gold;
        }

        if ($goldloss > 0)
        {
            $session['user']['gold'] -= $goldloss;
            $this->response->pageAddContent($this->format->colorize(sprintf('`$You lose %s gold.`n', $goldloss), true));
        }

        $session['user']['hitpoints'] = 0;
        $session['user']['alive']     = false;

        $this->navigation->addNav('Daily news', 'news.php');
        $this->response->pageEnd();
    }
}
